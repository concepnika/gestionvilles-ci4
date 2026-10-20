<?php

namespace App\Libraries;

/**
 * Port CI4 de la chaîne de snapping OSRM extraite de
 * application/controllers/Co_tablette_sani.php (CI3).
 *
 * snapSegments() == Co_tablette_sani::snapLocationsWithOsrmPrecise()
 * Format segment : "lat,lng, timespan: ..., chauffeur: ..., adresse: ..._"
 * séparés par '|'.
 */
class OsrmSnapper
{
    private string $osrmBase;
    private int $snapStepMeters = 25; // densification le long de la géométrie matchée
    private string $cacheDir;

    public function __construct()
    {
        $this->osrmBase = rtrim((string) (env('osrm.base') ?: 'http://127.0.0.1:5000'), '/');
        $this->cacheDir = WRITEPATH . 'cache/osrm';
    }

    /**
     * Route OSRM entre 2 points — wrapper public de osrmRouteBetween
     * (endpoint /sanivan/bridge_route).
     */
    public function routeBetween(float $lat1, float $lng1, float $lat2, float $lng2): array
    {
        return $this->osrmRouteBetween($lat1, $lng1, $lat2, $lng2);
    }

    /**
     * Parse un segment brut → [points, metas, timestamps, chauffeur].
     * Wrapper public de parseSegmentWithChauffeur (endpoints polylines).
     */
    public function parseSegmentChauffeur(string $segment): array
    {
        return $this->parseSegmentWithChauffeur($segment);
    }

    /**
     * Découpe un tracé aux gaps distance/temps/angle.
     * Wrapper public de splitByGaps (endpoints polylines).
     */
    public function splitByGapsPublic(array $pts, array $timestamps, int $maxMeters = 120, int $maxSeconds = 90, int $angleCutDeg = 120, int $armMinMeters = 40): array
    {
        return $this->splitByGaps($pts, $timestamps, $maxMeters, $maxSeconds, $angleCutDeg, $armMinMeters);
    }

    /**
     * Snap OSRM + "bridges" automatiques entre sous-segments.
     * Retourne les segments reconstruits séparés par '|'.
     */
    public function snapSegments(array $segments): string
    {
        $out = [];
        foreach ($segments as $segment) {
            [$pts, $metas, $timestamps, $chauffeur] = $this->parseSegmentWithChauffeur($segment);
            if (empty($pts)) {
                $out[] = $segment;
                continue;
            }

            // Seuils originaux: 600 m / 360 s / 170° / 80 m
            $chunks = $this->splitByGaps($pts, $timestamps, 600, 360, 170, 80);
            if (empty($chunks)) {
                $out[] = $segment;
                continue;
            }

            // Préparer tous les chunks (échantillonnage + bearings + radiuses)
            $prepared = [];
            foreach ($chunks as $chunk) {
                $cPts = $chunk[0];
                $cTs = $chunk[1];

                // Limite OSRM: max 1000 points par requête /match
                if (count($cPts) > 1000) {
                    $step = (int) ceil(count($cPts) / 1000);
                    $sampledPts = [];
                    $sampledTs = [];
                    for ($si = 0; $si < count($cPts); $si += $step) {
                        $sampledPts[] = $cPts[$si];
                        $sampledTs[] = $cTs[$si] ?? null;
                    }
                    if (end($sampledPts) !== $cPts[count($cPts) - 1]) {
                        $sampledPts[] = $cPts[count($cPts) - 1];
                        $sampledTs[] = $cTs[count($cTs) - 1] ?? null;
                    }
                    $cPts = $sampledPts;
                    $cTs = $sampledTs;
                }

                $bearings = $this->computeBearings($cPts, 60);
                $radiuses = array_fill(0, count($cPts), 100);
                $prepared[] = ['pts' => $cPts, 'ts' => $cTs, 'bearings' => $bearings, 'radiuses' => $radiuses];
            }

            // Snap OSRM en parallèle (curl_multi)
            $matches = $this->osrmMatchBatchParallel($prepared);

            // Traiter les résultats
            $snapped = [];
            foreach ($matches as $idx => $match) {
                $cPts = $prepared[$idx]['pts'];
                $cTs = $prepared[$idx]['ts'];
                $coords = $match['geometry'];

                if (empty($coords)) {
                    // Fallback: osrmRouteBetween entre paires de points (max 10 appels)
                    $coords = [];
                    $m = count($cPts);
                    $MAX_ROUTE_CALLS = 10;
                    $routeCalls = 0;
                    $step = ($m > $MAX_ROUTE_CALLS + 1) ? (int) ceil(($m - 1) / $MAX_ROUTE_CALLS) : 1;
                    for ($i = 0; $i < $m; $i += $step) {
                        if ($i === 0) {
                            $coords[] = [(float) $cPts[$i][0], (float) $cPts[$i][1]];
                            continue;
                        }
                        $a = $cPts[$i - $step];
                        $b = $cPts[$i];
                        $routeCalls++;
                        if ($routeCalls > $MAX_ROUTE_CALLS) {
                            $coords[] = [(float) $b[0], (float) $b[1]];
                            continue;
                        }
                        $segCoords = $this->osrmRouteBetween((float) $a[0], (float) $a[1], (float) $b[0], (float) $b[1]);
                        if (! empty($segCoords)) {
                            if (count($coords) > 0) {
                                $last = end($coords);
                                $first = $segCoords[0];
                                if (abs($first[0] - $last[0]) < 1e-7 && abs($first[1] - $last[1]) < 1e-7) {
                                    array_shift($segCoords);
                                }
                            }
                            foreach ($segCoords as $p) {
                                $coords[] = $p;
                            }
                        } else {
                            $coords[] = [(float) $b[0], (float) $b[1]];
                        }
                    }
                    $lastIdx = $m - 1;
                    $lastCoord = end($coords);
                    if ($lastCoord !== false && (abs($lastCoord[0] - $cPts[$lastIdx][0]) > 1e-7 || abs($lastCoord[1] - $cPts[$lastIdx][1]) > 1e-7)) {
                        $coords[] = [(float) $cPts[$lastIdx][0], (float) $cPts[$lastIdx][1]];
                    }
                }

                // Densifier (25 m)
                $dense = $this->densifyLineString($coords, $this->snapStepMeters);
                $snapped[] = [
                    'coords' => $dense,
                    't0' => $cTs[0] ?? null,
                    't1' => $cTs[count($cTs) - 1] ?? null
                ];
            }

            // "Bridging" entre sous-segments (OSRM Route) si petits trous
            $BRIDGE_MAX_M = 2000.0;
            $BRIDGE_MAX_SECS = 900;
            $pieces = [];
            for ($i = 0; $i < count($snapped); $i++) {
                $cur = $snapped[$i]['coords'];
                if (! empty($cur)) {
                    $pieces[] = $cur;
                }
                if ($i < count($snapped) - 1) {
                    $next = $snapped[$i + 1]['coords'];
                    if (! empty($cur) && ! empty($next)) {
                        $a = end($cur);
                        $b = $next[0];

                        $d = $this->haversine($a[0], $a[1], $b[0], $b[1]);
                        $okDist = ($d <= $BRIDGE_MAX_M);

                        $okTime = true;
                        $t1 = $snapped[$i]['t1'];
                        $t2 = $snapped[$i + 1]['t0'];
                        if ($t1 !== null && $t2 !== null && $t2 >= $t1) {
                            $okTime = (($t2 - $t1) <= $BRIDGE_MAX_SECS);
                        }

                        if ($okDist && $okTime) {
                            $bridge = $this->osrmRouteBetween((float) $a[0], (float) $a[1], (float) $b[0], (float) $b[1]);
                            if (! empty($bridge)) {
                                $bridge = $this->densifyLineString($bridge, $this->snapStepMeters);
                                if (! empty($bridge)) {
                                    $first = $bridge[0];
                                    if (abs($first[0] - $a[0]) < 1e-7 && abs($first[1] - $a[1]) < 1e-7) {
                                        array_shift($bridge);
                                    }
                                    $pieces[] = $bridge;
                                }
                            }
                        }
                    }
                }
            }

            // Aplatir tout en une seule ligne
            $line = [];
            foreach ($pieces as $seg) {
                foreach ($seg as $p) {
                    $line[] = [$p[0], $p[1]];
                }
            }
            if (count($line) < 2) {
                $out[] = $segment;
                continue;
            }

            // Interpolation de temps sur toute la ligne
            $T0 = null;
            $T1 = null;
            foreach ($snapped as $s) {
                if ($s['t0'] !== null) {
                    $T0 = $s['t0'];
                    break;
                }
            }
            for ($k = count($snapped) - 1; $k >= 0; $k--) {
                if ($snapped[$k]['t1'] !== null) {
                    $T1 = $snapped[$k]['t1'];
                    break;
                }
            }
            if ($T0 === null) {
                $T0 = time();
            }
            if ($T1 === null || $T1 <= $T0) {
                $T1 = $T0 + max(1, count($line) - 1);
            }

            $rebuilt = [];
            $n = count($line);
            for ($i = 0; $i < $n; $i++) {
                $t = $this->lerpTime($T0, $T1, ($n > 1) ? ($i / ($n - 1)) : 0.0);
                $tsStr = date('Y-m-d H:i:s', $t);
                $metaStr = ', timespan: ' . $tsStr . ', chauffeur: ' . $chauffeur . ', adresse: ';
                $rebuilt[] = number_format($line[$i][0], 6, '.', '') . ',' . number_format($line[$i][1], 6, '.', '') . $metaStr;
            }
            $out[] = implode('_', $rebuilt);
        }
        return implode('|', $out);
    }

    // Parse un segment "lat,lng, timespan:..., chauffeur:..., adresse:..."
    // → points, metas, timestamps, chauffeur (dernier non vide)
    private function parseSegmentWithChauffeur(string $segment): array
    {
        $items = array_filter(explode('_', $segment), 'strlen');
        $points = [];
        $metas = [];
        $timestamps = [];
        $lastChauffeur = '';

        // Limites géographiques raisonnables (Canada/Québec)
        $MIN_LAT = 40.0;
        $MAX_LAT = 65.0;
        $MIN_LNG = -85.0;
        $MAX_LNG = -50.0;

        foreach ($items as $raw) {
            $raw = trim($raw);
            $pos1 = strpos($raw, ',');
            if ($pos1 === false) {
                continue;
            }
            $pos2 = strpos($raw, ',', $pos1 + 1);
            if ($pos2 === false) {
                continue;
            }

            $lat = (float) trim(substr($raw, 0, $pos1));
            $lng = (float) trim(substr($raw, $pos1 + 1, $pos2 - $pos1 - 1));
            $meta = substr($raw, $pos2);

            // Filtrer les coordonnées aberrantes
            if ($lat === 0.0 || $lng === 0.0 ||
                $lat < $MIN_LAT || $lat > $MAX_LAT ||
                $lng < $MIN_LNG || $lng > $MAX_LNG) {
                continue;
            }

            $ts = null;
            $chauffeur = '';
            if (preg_match('/timespan\s*:\s*([^,]+)/i', $raw, $m)) {
                $ts = strtotime(trim($m[1]));
            }
            if (preg_match('/chauffeur\s*:\s*([^,]+)/i', $raw, $mc)) {
                $chauffeur = trim($mc[1]);
            }
            if ($chauffeur !== '') {
                $lastChauffeur = $chauffeur;
            }

            $points[] = [$lat, $lng];
            $metas[] = ($meta !== false) ? $meta : '';
            $timestamps[] = ($ts !== false && $ts !== null) ? $ts : null;
        }

        // timestamps croissants
        $base = time();
        for ($i = 0, $c = count($timestamps); $i < $c; $i++) {
            if ($timestamps[$i] === null) {
                $timestamps[$i] = $base + $i;
            }
        }
        return [$points, $metas, $timestamps, $lastChauffeur];
    }

    private function computeBearings(array $points, int $range = 60): array
    {
        $bearings = [];
        $n = count($points);
        for ($i = 0; $i < $n; $i++) {
            $bear = ($i < $n - 1)
                ? $this->bearingDeg($points[$i], $points[$i + 1])
                : $this->bearingDeg($points[$i - 1], $points[$i]);
            $bearings[] = $bear . ',' . $range;
        }
        return $bearings;
    }

    private function bearingDeg(array $a, array $b): int
    {
        $lat1 = deg2rad($a[0]);
        $lon1 = deg2rad($a[1]);
        $lat2 = deg2rad($b[0]);
        $lon2 = deg2rad($b[1]);
        $y = sin($lon2 - $lon1) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lon2 - $lon1);
        $brng = rad2deg(atan2($y, $x));
        $brng = fmod(($brng + 360.0), 360.0);
        return (int) round($brng);
    }

    // Appel OSRM /match en parallèle (curl_multi) pour tous les chunks d'un segment
    private function osrmMatchBatchParallel(array $prepared): array
    {
        $results = [];
        $n = count($prepared);
        if ($n === 0) {
            return [];
        }

        // Nettoyage automatique: supprimer les fichiers cache > 24h (1x sur 50 appels)
        if (rand(1, 50) === 1 && is_dir($this->cacheDir)) {
            foreach (glob($this->cacheDir . '/*.json') as $f) {
                if (is_file($f) && (time() - filemtime($f)) > 86400) {
                    @unlink($f);
                }
            }
        }

        $handles = [];
        $curlMap = [];
        $cachedResults = [];

        for ($i = 0; $i < $n; $i++) {
            $points = $prepared[$i]['pts'];
            $timestamps = $prepared[$i]['ts'];
            $bearings = $prepared[$i]['bearings'];
            $radiuses = $prepared[$i]['radiuses'];

            $cacheKey = md5(serialize([$points, $timestamps, $bearings, $radiuses]));
            $cacheFile = $this->cacheDir . '/match_' . $cacheKey . '.json';
            if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
                $cached = @file_get_contents($cacheFile);
                if ($cached !== false) {
                    $dec = json_decode($cached, true);
                    if (is_array($dec) && isset($dec['tracepoints'])) {
                        $cachedResults[$i] = $dec;
                        continue;
                    }
                }
            }

            $coord = [];
            foreach ($points as $p) {
                $coord[] = number_format($p[1], 6, '.', '') . ',' . number_format($p[0], 6, '.', '');
            }
            $coordStr = implode(';', $coord);
            $tsStr = implode(';', array_map('intval', $timestamps));
            $bearStr = implode(';', $bearings);
            $radStr = implode(';', array_map(static fn ($r) => (int) $r, $radiuses));

            $url = $this->osrmBase . '/match/v1/driving/' . $coordStr
                . '?geometries=geojson&overview=full&gaps=ignore&tidy=true'
                . '&timestamps=' . $tsStr
                . '&bearings=' . $bearStr
                . '&radiuses=' . $radStr;

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => 'RdfRoutes-Leaflet-MapMatch'
            ]);
            $handles[] = $ch;
            $curlMap[count($handles) - 1] = $i;
        }

        $responses = [];
        if (! empty($handles)) {
            $mh = curl_multi_init();
            foreach ($handles as $ch) {
                curl_multi_add_handle($mh, $ch);
            }
            $active = null;
            do {
                $status = curl_multi_exec($mh, $active);
                if ($status == CURLM_OK) {
                    if (curl_multi_select($mh) == -1) {
                        usleep(10000); // 10ms
                    }
                }
            } while ($active && $status == CURLM_OK);

            foreach ($handles as $idx => $ch) {
                $resp = curl_multi_getcontent($ch);
                $preparedIdx = $curlMap[$idx];
                $responses[$preparedIdx] = $resp;
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }
            curl_multi_close($mh);
        }

        for ($i = 0; $i < $n; $i++) {
            if (isset($cachedResults[$i])) {
                $results[$i] = $cachedResults[$i];
                continue;
            }
            if (! isset($responses[$i]) || $responses[$i] === false) {
                $results[$i] = ['tracepoints' => [], 'geometry' => []];
                continue;
            }
            $json = json_decode($responses[$i], true);
            $tracepoints = [];
            $geometry = [];
            $points = $prepared[$i]['pts'];
            if (isset($json['tracepoints']) && is_array($json['tracepoints'])) {
                $tp = $json['tracepoints'];
                for ($j = 0; $j < count($points); $j++) {
                    if (isset($tp[$j]['location']) && is_array($tp[$j]['location'])) {
                        $tracepoints[$j] = [(float) $tp[$j]['location'][1], (float) $tp[$j]['location'][0]];
                    } else {
                        $tracepoints[$j] = null;
                    }
                }
            }
            if (isset($json['matchings']) && is_array($json['matchings'])) {
                foreach ($json['matchings'] as $m) {
                    if (isset($m['geometry']['coordinates']) && is_array($m['geometry']['coordinates'])) {
                        foreach ($m['geometry']['coordinates'] as $ll) {
                            $geometry[] = [(float) $ll[1], (float) $ll[0]];
                        }
                    }
                }
            }
            $result = ['tracepoints' => $tracepoints, 'geometry' => $geometry];
            if (! empty($geometry)) {
                if (! is_dir($this->cacheDir)) {
                    @mkdir($this->cacheDir, 0775, true);
                }
                $cacheKey = md5(serialize([$points, $prepared[$i]['ts'], $prepared[$i]['bearings'], $prepared[$i]['radiuses']]));
                @file_put_contents($this->cacheDir . '/match_' . $cacheKey . '.json', json_encode($result));
            }
            $results[$i] = $result;
        }

        return $results;
    }

    // Route OSRM simple entre 2 points (pont entre sous-segments)
    private function osrmRouteBetween(float $lat1, float $lng1, float $lat2, float $lng2): array
    {
        $cacheKey = md5(sprintf('%.6f,%.6f,%.6f,%.6f', $lat1, $lng1, $lat2, $lng2));
        $cacheFile = $this->cacheDir . '/route_' . $cacheKey . '.json';
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
            $cached = @file_get_contents($cacheFile);
            if ($cached !== false) {
                $dec = json_decode($cached, true);
                if (is_array($dec)) {
                    return $dec;
                }
            }
        }

        $url = $this->osrmBase . '/route/v1/driving/' .
            number_format($lng1, 6, '.', '') . ',' . number_format($lat1, 6, '.', '') . ';' .
            number_format($lng2, 6, '.', '') . ',' . number_format($lat2, 6, '.', '') .
            '?geometries=geojson&overview=full&steps=false';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'RdfRoutes-BridgeRoute'
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        if ($resp === false) {
            return [];
        }

        $json = json_decode($resp, true);
        if (! isset($json['routes'][0]['geometry']['coordinates']) || ! is_array($json['routes'][0]['geometry']['coordinates'])) {
            return [];
        }
        $coords = [];
        foreach ($json['routes'][0]['geometry']['coordinates'] as $ll) {
            // OSRM renvoie [lng, lat] -> [lat, lng]
            $coords[] = [(float) $ll[1], (float) $ll[0]];
        }
        if (! is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0775, true);
        }
        @file_put_contents($cacheFile, json_encode($coords));
        return $coords;
    }

    // Coupe un tracé en sous-segments sur écart distance/temps/angle
    private function splitByGaps(array $pts, array $timestamps, int $maxMeters = 120, int $maxSeconds = 90, int $angleCutDeg = 120, int $armMinMeters = 40): array
    {
        $chunks = [];
        $curPts = [];
        $curTs = [];
        $curIdx = [];
        $n = count($pts);
        if ($n === 0) {
            return [];
        }

        $curPts[] = $pts[0];
        $curTs[] = $timestamps[0] ?? null;
        $curIdx[] = 0;

        for ($i = 1; $i < $n; $i++) {
            $a = $pts[$i - 1];
            $b = $pts[$i];
            $dt = 0;
            if (isset($timestamps[$i - 1], $timestamps[$i]) && $timestamps[$i - 1] !== null && $timestamps[$i] !== null) {
                $dt = abs((int) $timestamps[$i] - (int) $timestamps[$i - 1]);
            }
            $d = $this->haversine($a[0], $a[1], $b[0], $b[1]);

            $cut = ($d > $maxMeters) || ($dt > $maxSeconds);

            // angle gate (sur triplets)
            if (! $cut && $i < $n - 1) {
                $c = $pts[$i + 1];
                $ang = $this->turnAngleDeg($a, $b, $c);
                $ab = $this->haversine($a[0], $a[1], $b[0], $b[1]);
                $bc = $this->haversine($b[0], $b[1], $c[0], $c[1]);
                if ($ang >= $angleCutDeg && (max($ab, $bc) >= $armMinMeters)) {
                    $cut = true;
                }
            }

            if ($cut) {
                if (count($curPts) >= 2) {
                    $chunks[] = [$curPts, $curTs, $curIdx];
                }
                $curPts = [$b];
                $curTs = [$timestamps[$i] ?? null];
                $curIdx = [$i];
            } else {
                $curPts[] = $b;
                $curTs[] = $timestamps[$i] ?? null;
                $curIdx[] = $i;
            }
        }
        if (count($curPts) >= 2) {
            $chunks[] = [$curPts, $curTs, $curIdx];
        }
        return $chunks;
    }

    private function turnAngleDeg(array $a, array $b, array $c): float
    {
        $abx = $b[1] - $a[1];
        $aby = $b[0] - $a[0];
        $bcx = $c[1] - $b[1];
        $bcy = $c[0] - $b[0];
        $dab = sqrt($abx * $abx + $aby * $aby);
        if ($dab == 0) {
            return 0.0;
        }
        $dbc = sqrt($bcx * $bcx + $bcy * $bcy);
        if ($dbc == 0) {
            return 0.0;
        }
        $cos = ($abx * $bcx + $aby * $bcy) / ($dab * $dbc);
        $cos = max(-1.0, min(1.0, $cos));
        return acos($cos) * 180.0 / M_PI;
    }

    // Densifie une LineString en insérant des points tous les $step mètres
    private function densifyLineString(array $coords, float $stepMeters): array
    {
        if (count($coords) < 2) {
            return $coords;
        }
        $dense = [];
        for ($i = 0; $i < count($coords) - 1; $i++) {
            $a = $coords[$i];
            $b = $coords[$i + 1];
            $dense[] = $a;
            $d = $this->haversine($a[0], $a[1], $b[0], $b[1]);
            if ($d > $stepMeters) {
                $n = (int) floor($d / $stepMeters);
                for ($k = 1; $k <= $n; $k++) {
                    $t = ($k * $stepMeters) / $d;
                    $dense[] = $this->interpLatLng($a, $b, $t);
                }
            }
        }
        $dense[] = $coords[count($coords) - 1];
        return $dense;
    }

    private function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    private function interpLatLng(array $a, array $b, float $t): array
    {
        return [
            $a[0] + ($b[0] - $a[0]) * $t,
            $a[1] + ($b[1] - $a[1]) * $t
        ];
    }

    private function lerpTime(int $t0, int $t1, float $t): int
    {
        if ($t1 <= $t0) {
            return $t0;
        }
        return (int) round($t0 + ($t1 - $t0) * $t);
    }
}
