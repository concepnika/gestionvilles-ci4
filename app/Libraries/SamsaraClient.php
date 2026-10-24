<?php

namespace App\Libraries;

/**
 * Port CI4 de application/libraries/SamsaraLib.php (CI3).
 *
 * Phase 1 (Co_Rest_Sani) : listVehicles + findVehicleIdByName.
 * Port web chauffeur : stats GPS (position, historique, batch multi
 * véhicules) — même logique de retry que SamsaraLib::getLatestLocation.
 *
 * Config : samsara.apiKey et samsara.baseUrl dans .env
 * (repris de application/config/samsara.php).
 */
class SamsaraClient
{
    private ?string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('samsara.apiKey') ?: null;
        $base = env('samsara.baseUrl') ?: 'https://api.samsara.com';
        $this->baseUrl = rtrim($base, '/');
    }

    public function listVehicles($limit = 512)
    {
        return $this->request("{$this->baseUrl}/fleet/vehicles?limit={$limit}");
    }

    public function getVehicleStatsById($vehicleId)
    {
        return $this->request("{$this->baseUrl}/fleet/vehicles/stats?vehicleIds={$vehicleId}&types=gps");
    }

    public function getVehicleStatsHistory($vehicleId, $startIso, $endIso)
    {
        $url = "{$this->baseUrl}/fleet/vehicles/stats/history"
             . "?vehicleIds={$vehicleId}"
             . "&types=gps"
             . "&startTime=" . urlencode($startIso)
             . "&endTime=" . urlencode($endIso);
        return $this->request($url);
    }

    public function getVehicleStatsFeed($vehicleId, $afterCursor = null)
    {
        $url = "{$this->baseUrl}/fleet/vehicles/stats/feed?vehicleIds={$vehicleId}&types=gps";
        if ($afterCursor) {
            $url .= "&after=" . urlencode($afterCursor);
        }
        return $this->request($url);
    }

    /**
     * Positions de PLUSIEURS véhicules en un seul appel (batch).
     * Retourne [vehicleId => {lat, lng, ts, speedMph, address, heading}].
     */
    public function getMultiVehicleLocations($vehicleIds)
    {
        if (empty($vehicleIds)) {
            return [];
        }
        $ids = implode(',', $vehicleIds);
        $data = $this->request("{$this->baseUrl}/fleet/vehicles/stats?vehicleIds={$ids}&types=gps");

        $results = [];
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $vehicle) {
                $vid = $vehicle['id'] ?? '';
                $loc = $this->extractGpsFromVehicle($vehicle);
                if ($vid && $loc) {
                    $results[$vid] = $loc;
                }
            }
        }
        return $results;
    }

    /**
     * Dernière position d'un véhicule — même séquence de retry
     * que SamsaraLib::getLatestLocation : stats → feed → history 10 min.
     */
    public function getLatestLocation($vehicleId)
    {
        $loc = $this->parseStatsGps($this->getVehicleStatsById($vehicleId));
        if ($loc) {
            return $loc;
        }

        $loc = $this->parseStatsGps($this->getVehicleStatsFeed($vehicleId));
        if ($loc) {
            return $loc;
        }

        $end   = gmdate('c');
        $start = gmdate('c', time() - 600);
        return $this->parseStatsGps($this->getVehicleStatsHistory($vehicleId, $start, $end));
    }

    /**
     * Historique de points GPS [{lat, lng, ts}, ...].
     */
    public function getLocationHistory($vehicleId, $startIso, $endIso)
    {
        $data   = $this->getVehicleStatsHistory($vehicleId, $startIso, $endIso);
        $points = [];

        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $vehicle) {
                $gpsList = $vehicle['gps'] ?? [];
                foreach ($gpsList as $gps) {
                    $lat = $gps['latitude']  ?? null;
                    $lng = $gps['longitude'] ?? null;
                    $ts  = $gps['time']      ?? null;
                    if ($lat !== null && $lng !== null) {
                        $points[] = [
                            'lat' => (float) $lat,
                            'lng' => (float) $lng,
                            'ts'  => $ts,
                        ];
                    }
                }
            }
        }

        return $points;
    }

    /**
     * Trouve l'ID Samsara d'un véhicule par son nom de camion.
     * Correspondance exacte puis partielle (insensible à la casse).
     */
    public function findVehicleIdByName($nocamion): ?string
    {
        if (empty($nocamion)) {
            return null;
        }

        $data = $this->listVehicles();
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return null;
        }

        $needle = strtolower(trim($nocamion));
        $partial = null;

        foreach ($data['data'] as $v) {
            $name = isset($v['name']) ? strtolower(trim($v['name'])) : '';
            if ($name === '') {
                continue;
            }
            if ($name === $needle) {
                return $v['id'] ?? null;
            }
            if ($partial === null) {
                if (strpos($name, $needle) !== false || strpos($needle, $name) !== false) {
                    $partial = $v;
                }
            }
        }

        return $partial['id'] ?? null;
    }

    // ─── Helpers GPS ──────────────────────────────────

    private function extractGpsFromVehicle($vehicle)
    {
        $gpsList = $vehicle['gps'] ?? [];

        if (is_array($gpsList) && isset($gpsList[0])) {
            foreach ($gpsList as $gps) {
                $lat = $gps['latitude']  ?? null;
                $lng = $gps['longitude'] ?? null;
                if ($lat !== null && $lng !== null) {
                    return [
                        'lat'      => (float) $lat,
                        'lng'      => (float) $lng,
                        'ts'       => $gps['time'] ?? null,
                        'speedMph' => isset($gps['speedMilesPerHour']) ? (float) $gps['speedMilesPerHour'] : null,
                        'address'  => (isset($gps['reverseGeo']) && isset($gps['reverseGeo']['formattedLocation']))
                                        ? (string) $gps['reverseGeo']['formattedLocation'] : '',
                        'heading'  => $this->extractHeading($gps),
                    ];
                }
            }
        } elseif (is_array($gpsList) && isset($gpsList['latitude'], $gpsList['longitude'])) {
            return [
                'lat'      => (float) $gpsList['latitude'],
                'lng'      => (float) $gpsList['longitude'],
                'ts'       => $gpsList['time'] ?? null,
                'speedMph' => isset($gpsList['speedMilesPerHour']) ? (float) $gpsList['speedMilesPerHour'] : null,
                'address'  => (isset($gpsList['reverseGeo']) && isset($gpsList['reverseGeo']['formattedLocation']))
                                ? (string) $gpsList['reverseGeo']['formattedLocation'] : '',
                'heading'  => $this->extractHeading($gpsList),
            ];
        }
        return null;
    }

    private function extractHeading($gps)
    {
        if (isset($gps['headingDeg'])) {
            return (float) $gps['headingDeg'];
        }
        if (isset($gps['heading'])) {
            return (float) $gps['heading'];
        }
        return null;
    }

    private function parseStatsGps($data)
    {
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return null;
        }

        foreach ($data['data'] as $vehicle) {
            $gpsList = $vehicle['gps'] ?? [];

            if (is_array($gpsList) && isset($gpsList[0])) {
                foreach ($gpsList as $gps) {
                    $lat = $gps['latitude']  ?? null;
                    $lng = $gps['longitude'] ?? null;
                    if ($lat !== null && $lng !== null) {
                        return [
                            'lat'      => (float) $lat,
                            'lng'      => (float) $lng,
                            'ts'       => $gps['time'] ?? null,
                            'speedMph' => isset($gps['speedMilesPerHour']) ? (float) $gps['speedMilesPerHour'] : null,
                            'address'  => (isset($gps['reverseGeo']) && isset($gps['reverseGeo']['formattedLocation']))
                                            ? (string) $gps['reverseGeo']['formattedLocation'] : '',
                        ];
                    }
                }
            } elseif (is_array($gpsList) && isset($gpsList['latitude'], $gpsList['longitude'])) {
                return [
                    'lat'      => (float) $gpsList['latitude'],
                    'lng'      => (float) $gpsList['longitude'],
                    'ts'       => $gpsList['time'] ?? null,
                    'speedMph' => isset($gpsList['speedMilesPerHour']) ? (float) $gpsList['speedMilesPerHour'] : null,
                    'address'  => (isset($gpsList['reverseGeo']) && isset($gpsList['reverseGeo']['formattedLocation']))
                                    ? (string) $gpsList['reverseGeo']['formattedLocation'] : '',
                ];
            }
        }

        return null;
    }

    /**
     * Port de SamsaraLib::getIftaVehicleReport (CI3) : rapport IFTA
     * par véhicule, paginé mois par mois (endpoint
     * /fleet/reports/ifta/vehicle).
     */
    public function getIftaVehicleReport($startDate, $endDate, $jurisdiction = 'ON')
    {
        $months = $this->monthsInRange($startDate, $endDate);
        $monthNames = array(
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        );

        $allReports = array();

        foreach ($months as $ym) {
            $year  = $ym['year'];
            $month = $ym['month'];

            $params = "year={$year}&month=" . urlencode($monthNames[$month]) . "&jurisdictions=" . urlencode($jurisdiction);
            $after = null;

            do {
                $url = "{$this->baseUrl}/fleet/reports/ifta/vehicle?{$params}";
                if ($after) {
                    $url .= "&after=" . urlencode($after);
                }
                $data = $this->request($url);
                if (isset($data['error'])) {
                    return $data;
                }

                if (isset($data['data']['vehicleReports']) && is_array($data['data']['vehicleReports'])) {
                    foreach ($data['data']['vehicleReports'] as $entry) {
                        $allReports[] = $entry;
                    }
                }
                $after = isset($data['pagination']['endCursor']) ? $data['pagination']['endCursor'] : null;
                $hasNext = isset($data['pagination']['hasNextPage']) ? $data['pagination']['hasNextPage'] : false;
            } while ($hasNext && $after);
        }

        return $allReports;
    }

    /**
     * Port de SamsaraLib::getKmByCamion (CI3) : agrège le rapport IFTA
     * et lie chaque véhicule Samsara à son NoCamion de la BD.
     */
    public function getKmByCamion($startDate, $endDate, $jurisdiction = 'ON')
    {
        $reports = $this->getIftaVehicleReport($startDate, $endDate, $jurisdiction);
        if (isset($reports['error'])) {
            return $reports;
        }

        // Récupérer les camions de la BD (CI3 : get_instance()->db)
        $camions = \Config\Database::connect()
            ->query("SELECT NoCamion FROM camions WHERE isdeleted = 0")->getResult();
        $camionNames = array();
        foreach ($camions as $c) {
            $camionNames[strtolower(trim($c->NoCamion))] = $c->NoCamion;
        }

        $aggregated = array();
        foreach ($reports as $report) {
            // L'API Samsara retourne vehicle (objet) et jurisdictions (tableau)
            $vehicle    = isset($report['vehicle']) ? $report['vehicle'] : array();
            $vehicleId   = isset($vehicle['id']) ? $vehicle['id'] : null;
            $vehicleName = isset($vehicle['name']) ? $vehicle['name'] : '';
            $vin         = isset($vehicle['vin']) ? $vehicle['vin'] : '';

            if ($vehicleId === null) {
                continue;
            }

            // Tenter de lier avec un camion de la BD par nom
            if (! isset($aggregated[$vehicleId])) {
                $noCamion = null;
                $needle = strtolower(trim($vehicleName));
                if (isset($camionNames[$needle])) {
                    $noCamion = $camionNames[$needle];
                } else {
                    foreach ($camionNames as $dbName => $dbNo) {
                        if (strpos($dbName, $needle) !== false || strpos($needle, $dbName) !== false) {
                            $noCamion = $dbNo;
                            break;
                        }
                    }
                }
                $aggregated[$vehicleId] = (object) array(
                    'vehicleId'   => $vehicleId,
                    'vehicleName' => $vehicleName,
                    'vin'         => $vin,
                    'noCamion'    => $noCamion,
                    'totalKm'     => 0,
                    'taxableKm'   => 0,
                    'jurisdiction'=> $jurisdiction,
                );
            }

            if (isset($report['jurisdictions']) && is_array($report['jurisdictions'])) {
                foreach ($report['jurisdictions'] as $juris) {
                    if (isset($juris['jurisdiction']) && $juris['jurisdiction'] === $jurisdiction) {
                        $aggregated[$vehicleId]->totalKm   += isset($juris['totalMeters']) ? (float) $juris['totalMeters'] / 1000 : 0;
                        $aggregated[$vehicleId]->taxableKm += isset($juris['taxableMeters']) ? (float) $juris['taxableMeters'] / 1000 : 0;
                    }
                }
            }
        }

        $results = array_values($aggregated);

        usort($results, function ($a, $b) {
            $aKey = $a->noCamion ?: $a->vehicleName;
            $bKey = $b->noCamion ?: $b->vehicleName;
            return strcmp($aKey, $bKey);
        });

        return $results;
    }

    /** Port de SamsaraLib::_monthsInRange : liste {year, month} couverte. */
    private function monthsInRange($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);
        $end->modify('first day of next month');

        $months = array();
        $current = clone $start;
        $current->modify('first day of this month');

        while ($current < $end) {
            $months[] = array(
                'year'  => (int) $current->format('Y'),
                'month' => (int) $current->format('n'),
            );
            $current->modify('+1 month');
        }

        return $months;
    }

    private function request($url, $method = 'GET', $body = null)
    {
        if ($this->apiKey === null) {
            return ['error' => 'samsara.apiKey non configuré'];
        }

        $ch = curl_init($url);
        $headers = [
            "Authorization: Bearer {$this->apiKey}",
            'Accept: application/json',
        ];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        if ($method === 'POST' && $body !== null) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => $headers,
            ]);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', "[Samsara] cURL error: {$error}");
            return ['error' => $error];
        }
        if ($httpCode >= 400) {
            log_message('error', "[Samsara] HTTP {$httpCode}: {$response}");
            return ['error' => "HTTP {$httpCode}", 'body' => $response];
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : ['raw' => $response];
    }
}
