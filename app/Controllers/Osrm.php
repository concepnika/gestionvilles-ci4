<?php

namespace App\Controllers;

// CI3 : use GuzzleHttp\Client (getMatchedRoute — code non routé ;
// Guzzle absent du vendor CI4 : ne s'exécute que si la methode
// morte GetCamionPolyline est appelee un jour).
use GuzzleHttp\Client;

use App\Libraries\KalmanFilter;

/**
 * Port CI3 -> CI4 de co_osrm : carte leaflet de debug OSRM
 * (sanivan/showleaflet). Port mecanique via port_controller.php.
 */
class Osrm extends BaseController
{
    /** Filtres de Kalman du __construct CI3 (instances independantes). */
    private $latFilter;
    private $lonFilter;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        date_default_timezone_set('America/Toronto');
        $processNoise = 0.1;
        $measurementNoise = 0.01;
        $this->latFilter = new KalmanFilter($processNoise, $measurementNoise);
        $this->lonFilter = new KalmanFilter($processNoise, $measurementNoise);
    }

    public function showLeaflet()
    {    
        $data = array();
        $data['msgerror'] = '';
        $data['msgreset'] = '';
        echo view('leaflet/leaflet', $data);
    }



    public function GetCamionPolyline($dateactive)
    {    
        $idchauffeur = $this->input->post_get('idchauffeur');
        $nocamion = $this->input->post_get('nocamion');
        $locations = $this->getPolylineDynamique($idchauffeur, $dateactive, $nocamion);
        $coord = "";
        $iter = 0;
        foreach ($locations as $location) {
          //if ($iter == 15) {
          //  break;
          //}
          $coord .= $this->getMatchedRoute($location);
          //$iter++;
        }
        echo $coord;
    }

    private function getMatchedRoute(string $coordsString)
    {    
        // OSRM attend les coordonnées sous le format "lon,lat", séparées par des points-virgules.
        // Construction de l'URL pour le service match d'OSRM
    
        $url = "https://router.project-osrm.org/match/v1/driving/{$coordsString}?overview=full&geometries=geojson&gaps=ignore";
        // Initialisation du client Guzzle
        $client = new Client();
        try {
          $response = $client->get($url, [
            // Pour le développement, vous pouvez désactiver la vérification SSL
            // Attention : ne pas désactiver en production !
            'verify' => false,
    
            // Vous pouvez également définir un timeout par exemple
            'timeout' => 30
          ]);
          $body = $response->getBody()->getContents();
          $data = json_decode($body, true);
          if (!isset($data['code']) || $data['code'] !== "Ok") {
            log_message('error', 'Erreur dans la réponse OSRM: ' . $body);
            $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode(['error' => "Réponse invalide de l'API OSRM."]));
            return;
          }
          $matchedRoute = isset($data['matchings'][0]['geometry']) ? $data['matchings'][0]['geometry'] : null;
          $returnval = "";
          foreach ($matchedRoute['coordinates'] as $lnglat) {
            $returnval .= $lnglat[1].','.$lnglat[0].'_';
          }
          return $matchedRoute;
          //return substr($returnval, 0, -1);
        } catch (\Exception $e) {
          return null;
        }
    }

    private function getPolylineDynamique($idchauffeur, $dateactive, $nocamion)
    {    
        /** @var string $returnval */
        $returnval = '';
        /** @var string $valSimply */
        $valSimply = '';
        /** @var string $lastPosition */
        $lastPosition = '';
        /** @var array $arrCam */
        $arrCam = array();
        /** @var array $arrSimply */
        $arrSimply = array();
        //----------------------------------------------
        $cam = $nocamion;
        $date = $dateactive;
        $nocamion = $this->db->escape($nocamion);
        $idchauffeur = $this->db->escape($idchauffeur);
        $dateactive = $this->db->escape($dateactive);
        //----------------------------------------------
        //if (!($this->session->userdata('lastPosition') !== null)) {
    
        //  $this->session->set_userdata('lastPosition', $arrCam);
        //}
        //$arrCam = $this->session->userdata('lastPosition');
    
        //if (array_key_exists($cam, $arrCam)) {
        //  $lastPosition = ($this->isValidDate($date)) ?
        //    " AND track_time > " .
        //    $this->db->escape($arrCam[$cam]['timepos']) : " ";
        //} else {
        //  $lastPosition = "";
        //}
        $strSQL = "SELECT id, idchauffeur, track_lng,track_lat, IsInZone, track_time 
                            FROM gps_sani WHERE LEFT(track_time, 10) = $dateactive AND 
                            idchauffeur = $idchauffeur AND nocamion = $nocamion AND  
                            IsInZone =1 $lastPosition order by track_time";
        //----------------------------------------------
    
        $query = $this->db->query($strSQL);
        if ($query->getNumRows() == 0) {
          return '';
        }
        $iter = 0;
        foreach ($query->getResult() as $row) {
          $arrSimply[] = array('x' => floatval(str_replace(',', '.', $row->track_lat)), 'y' => floatval(str_replace(',', '.', $row->track_lng)));
    
        }
        $tol = '0.0004';
        $flo = floatval($tol);
        $data = $this->douglasPeucker($arrSimply, $flo);
        $arrNew = "";
        foreach ($data as $key => $value) {
          $arrNew .= $value['x'] . "," . $value['y'] . "_";
        }
        $arrNew = substr($arrNew, 0, -1);
        $arr = array();
        $valSimply = '';
        $iter = 0;
        $lastPos = '';
        $arrPetit = array();
        foreach (explode("_", $arrNew) as $value) {
          $arrRed = explode(",", $value);
          $arr = $this->filterLocation(
            floatval(str_replace(',', '.', floatval($arrRed[0]))),
            floatval(str_replace(',', '.', floatval($arrRed[1])))
          );
          if ($iter == 99) {
            $arrPetit[] = substr($valSimply, 0, -1);
            $valSimply = $lastPos;
            $iter = 0;
          }
          $valSimply .= $arr['longitude'] . "," . $arr['latitude'] . ";";
          $lastPos .= $arr['longitude'] . "," . $arr['latitude'] . ";";
          $iter++;
        }
        //$returnval = substr($returnval, 0, -1);
        //$valSimply = substr($valSimply, 0, -1);
        //return $arrNew;
        return $arrPetit;
    }

    private function perpendicularDistance(array $point, array $lineStart, array $lineEnd): float
    {    
        $dx = $lineEnd['x'] - $lineStart['x'];
        $dy = $lineEnd['y'] - $lineStart['y'];
    
        // Si les deux points de la ligne sont identiques, retourner la distance euclidienne.
        if (abs($dx) < 1e-10 && abs($dy) < 1e-10) {
          return sqrt(pow($point['x'] - $lineStart['x'], 2) + pow($point['y'] - $lineStart['y'], 2));
        }
    
        // Calcul du numérateur et du dénominateur pour la distance perpendiculaire.
        $numerator = abs($dy * $point['x'] - $dx * $point['y'] + $lineEnd['x'] * $lineStart['y'] - $lineEnd['y'] * $lineStart['x']);
        $denom = sqrt($dx * $dx + $dy * $dy);
    
        return $numerator / $denom;
    }

    public function filterLocation($latitude, $longitude)
    {    
        $filteredLat = $this->latFilter->filter($latitude);
        $filteredLon = $this->lonFilter->filter($longitude);
        return array('latitude' => $filteredLat, 'longitude' => $filteredLon);
    }

    private function douglasPeucker(array $points, float $tolerance): array
    {    
        $n = count($points);
        if ($n < 3) {
          return $points;
        }
    
        $maxDistance = 0.0;
        $index = 1;
        $endIndex = $n - 1;
    
        // Boucle sur les points intermédiaires pour trouver celui qui est le plus éloigné de la ligne reliant le premier et le dernier point.
        for ($i = 1; $i < $endIndex; $i++) {
          $arrX = $points[$i];
          $arrY = $points[1];
          $arrZ = $points[$endIndex];
          $distance = $this->perpendicularDistance($arrX, $arrY, $arrZ);
          if ($distance > $maxDistance) {
            $maxDistance = $distance;
            $index = $i;
          }
        }
    
        // Si la distance maximale trouvée dépasse la tolérance, on déclenche la récursivité.
        if ($maxDistance > $tolerance) {
          // On simplifie la partie de la première extrémité au point critique.
          $firstHalf = $this->douglasPeucker(array_slice($points, 1, $index + 1), $tolerance);
          // On simplifie la partie du point critique jusqu'à la fin.
          $secondHalf = $this->douglasPeucker(array_slice($points, $index), $tolerance);
    
          // Fusion des résultats en évitant de dupliquer le point critique.
          return array_merge(
            array_slice($firstHalf, 1, count($firstHalf) - 1),
            $secondHalf
          );
        } else {
          // Si aucun point n'est suffisamment éloigné, on ne garde que le premier et le dernier.
          return [$points[1], $points[$endIndex]];
        }
    }
}
