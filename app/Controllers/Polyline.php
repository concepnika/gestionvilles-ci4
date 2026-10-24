<?php

namespace App\Controllers;

use App\Libraries\KalmanFilter;

/**
 * Port CI3 -> CI4 de co_polyline + polyline + Polyline (CI3 — même
 * fichier sous Windows insensible a la casse) : routes dynamiques des
 * camions (polylines, points GPS, camion sur la route du jour).
 * Port mecanique via port_controller.php.
 */
class Polyline extends BaseController
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
        // Charger la bibliothèque pour le filtre de Kalman.
        // L'alias permet d'avoir deux instances indépendantes.
        $processNoise = 0.00001;
        $measurementNoise = 0.001;
        $this->latFilter = new KalmanFilter($processNoise, $measurementNoise);
        $this->lonFilter = new KalmanFilter($processNoise, $measurementNoise);
    }

    public function save()
    {    
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
    
            if (!$data || !isset($data['points']) || !is_array($data['points'])) {
                // CI3 : http_response_code(400) — la Response CI4
                // ecrase le statut brut (defaut 200) : passer par elle.
                $this->response->setStatusCode(400);
                echo json_encode(['success' => false, 'message' => 'JSON invalide ou points manquants']);
                return;
            }
    
            // Insertion de la polyline principale
            $polyline = [
                'name'        => $data['name'],
                'date_created'=> $data['dateCreated'],
                'id_ville'    => $data['idVille'],
                'id_secteur'  => $data['idSecteur'],
                'id_chauffeur'=> $data['idChauffeur'],
                'camion'      => $data['camion']
            ];
            $this->db->table('polylines')->insert($polyline);
            $polyline_id = $this->db->insertID();
    
            // Insertion des points
            foreach ($data['points'] as $pt) {
                $point = [
                    'polyline_id' => $polyline_id,
                    'latitude'    => $pt['latitude'],
                    'longitude'   => $pt['longitude'],
                    'date'        => isset($pt['date']) ? $pt['date'] : null
                ];
                $this->db->table('polyline_points')->insert($point);
            }
    
            echo json_encode(['success' => true, 'message' => 'Polyline enregistrée']);
    }


    public function posPolyDynamique($idville)
    {
            $this->load->database();
            $dynPolyline = (string) $this->input->post_get('poly', TRUE);
            $dynPolyline = urldecode($dynPolyline);
            // CI3 tolerait un 'poly' absent (PHP7 : warning non fatal,
            // aucune insertion, puis echo 'ok'). PHP 8/CI4 leve une
            // exception (Undefined array key) — on reproduit le
            // comportement observable : boucle sautee si vide.
            if ($dynPolyline !== '') {
            // Insertion des points
            $data = explode('_', $dynPolyline);
            for ($i = 0; $i < count($data); $i++)
            {
                $arrPoint = explode(',', $data[$i]);
                $lat = $this->db->escape($arrPoint[0]);
                $lng = $this->db->escape($arrPoint[1]);
                $strSQL = "INSERT INTO polyline_points (idville, latitude, longitude, datejour) 
                VALUES 
                ($idville, $lat, $lng, NOW())";
                $this->db->query($strSQL);
            }
            }
            echo 'ok';
    }


    public function postPolyline()
    {    
            // Récupère le JSON brut envoyé dans le corps de la requête
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
    
            if (!$data) {
                // Erreur de décodage JSON
                $this->output
                    ->set_status_header(400)
                    ->set_output(json_encode(['error' => 'JSON invalide']));
                return;
            }
    
            // Accès aux données
            $idVille = isset($data['idVille']) ? $data['idVille'] : null;
            $idSecteur = isset($data['idSecteur']) ? $data['idSecteur'] : null;
            $points = isset($data['points']) ? $data['points'] : [];
            $str = implode('_', $points);
            // Exemple de traitement : boucle sur les points
            foreach ($points as $pt) {
                $lat = $pt['latitude'];
                $lng = $pt['longitude'];
                // ... insérer en base, log, etc.
            }
    
            // Réponse OK
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'ok']));
    }


    // Methodes de co_polyline (CI3)
    public function GetDayRoad($dateactive)
    {    
            $idchauffeur = $this->input->post_get('idchauffeur');
            if ($this->input->post_get('allchauffeur') == 1) {
                $data = $this->GetCamOnRoad($dateactive, 0);
            } else {
                $data = $this->GetCamOnRoad($dateactive, $idchauffeur);
            }
    
            $data['ActiveDate'] = $dateactive;
            $data['allchauffeur'] = $this->input->post_get('allchauffeur');
            $data['typeextra'] = $this->GetTypeExtra('');
            $data['privilege'] = $this->session->userdata('privilege');
            $data['newzoom'] = $this->input->post_get('newzoom');
            //$this->getValidChauffeurJour($dateactive, $idchauffeur);
            //$dataall = array_merge($data, $this->data);
            $from = $this->input->post_get('from');
            if ($from == 1) {
                $data['locations'] = '';
                if (($data['allchauffeur'] == 0) and ($idchauffeur > 0)) {
                    $data['locations'] =
                        $this->getPolylineDynamique($idchauffeur, $dateactive, $data['nocamion']);
                }
                $json = json_encode($data);
                echo $json;
            } else {
                echo view('routes_san/cam_on_road', $data);
            }
    }


    public function GetCamionPolyline($dateactive)
    {    
            $idchauffeur = $this->input->post_get('idchauffeur');
            $nocamion = $this->input->post_get('nocamion');
            $snap = (int) $this->input->post_get('snap');
            //$libosrm = new libOSRM();
            $segments = $this->getPolylineDynamique($idchauffeur, $dateactive, $nocamion);
            if (! is_array($segments) || empty($segments)) {
                echo '';
                return;
            }
    
            //if ($snap === 1) {
            //    $test = $libosrm->snapLocationsWithOsrmPrecise($segments);
            //    echo $libosrm->snapLocationsWithOsrmPrecise($segments);
            //    echo $segments;
            //    return;
            //}
    
            // Retour brut (comportement initial)
            echo rtrim(implode('|', $segments), '|');
    }



    private function encodePolyline($points)
    {    
            $result = '';
            $prevLat = 0;
            $prevLng = 0;
    
            foreach ($points as $point) {
                // Convertir en entier en multipliant par 1e5
                $lat = round($point[0] * 1e5);
                $lng = round($point[1] * 1e5);
    
                // Diff�rences avec le point pr�c�dent
                $dLat = $lat - $prevLat;
                $dLng = $lng - $prevLng;
    
                // Sauvegarder le point actuel pour la prochaine it�ration
                $prevLat = $lat;
                $prevLng = $lng;
    
                // Encoder les diff�rences
                $result .= $this->encodeSignedNumber($dLat);
                $result .= $this->encodeSignedNumber($dLng);
            }
            return $result;
    }

    private function encodeSignedNumber($num)
    {    
            $snum = $num << 1;
            if ($num < 0) {
                $snum = ~$snum;
            }
            return $this->encodeNumber($snum);
    }

    private function encodeNumber($num)
    {    
            $encodeString = '';
            while ($num >= 0x20) {
                $encodeString .= chr((0x20 | ($num & 0x1f)) + 63);
                $num >>= 5;
            }
            $encodeString .= chr($num + 63);
            return $encodeString;
    }

    public function getSmoothedPolyline($points)
    {    
            // Exemple de donn�es de points (latitude, longitude)
            // Dans un cas r�el, ces donn�es proviennent d'une base ou d'une API.
            //$points = [
            //    [48.8566, 2.3522],
            //    [48.8570, 2.3530],
            //    [48.8575, 2.3535],
            //    [48.8580, 2.3540],
            //    // ... ajoute d'autres points si n�cessaire
            //];
    
            $smoothedPoints = [];
    
            // Initialiser les filtres sur la premi�re mesure de chaque coordonn�e
            $this->kalmanFilterLat = new KalmanFilter(0.00001, 0.001, 1, $points[0][0]);
            $this->kalmanFilterLng = new KalmanFilter(0.00001, 0.001, 1, $points[0][1]);
    
            // Appliquer le filtre de Kalman sur chaque point
            foreach ($points as $point) {
                $smoothLat = $this->kalmanFilterLat->filter($point[0]);
                $smoothLng = $this->kalmanFilterLng->filter($point[1]);
                $smoothedPoints[] = [$smoothLat, $smoothLng];
            }
    
            // Encoder la polyline � partir des points liss�s
            $polyline = $this->encodePolyline($smoothedPoints);
    
            // Pr�parer la sortie en JSON
            $output = [
                'smoothed_points' => $smoothedPoints,
                'polyline' => $polyline,
            ];
    
            header('Content-Type: application/json');
            echo json_encode($output);
    }

    private function getPolylineBase($idville, $idsecteur)
    {    
            $strSQL = "SELECT id, idville, idsecteur, nom, parcours, parcours_reduit, urlfile
    	                    FROM polilyne_ville where idville = $idville and idsecteur = $idsecteur ";
            $polyline = $this->db->query($strSQL)->getRow(0);
            $data = array();
            if (isset($polyline)) {
                $arrLocation = explode('},{', $polyline->parcours_reduit);
                $center = substr($arrLocation[0], 1, strlen($arrLocation[0])) . '}';
    
                $data['center'] = $center;
                $data['parcours'] = $polyline->parcours_reduit;
                $data['script'] = $this->getAvecPolyline($center, $polyline->parcours);
                $data['havePoly'] = "1";
            } else {
                $data['center'] = '{ lat: 45.447302, lng: -73.797050 }';
                $data['parcours'] = "";
                $data['havePoly'] = "0";
                //$data['script'] = $this->getSansPolyline();    
            }
            return $data;
    }

    private function isValidDate($date)
    {    
            $pattern = '/^\d{4}-\d{2}-\d{2}$/';
            if (preg_match($pattern, $date)) {
                $parts = explode('-', $date);
                return checkdate($parts[1], $parts[2], $parts[0]);
            }
            return false;
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
            $lastid = '';
            $idstart = 0;
            //----------------------------------------------
            $strSQLstop = "SELECT id FROM  gps_sani WHERE LEFT(track_time, 10) = $dateactive AND 
                            idchauffeur = $idchauffeur AND nocamion = $nocamion AND  
                            IsInZone =0 AND Statutpoly = 2  order by track_time";
            $resultstop = $this->db->query($strSQLstop);
            if ($resultstop->getNumRows() > 0) {
                foreach ($resultstop->getResult() as $rowsstop) {
    
                    $idstop = $rowsstop->id;
                    $strSQL = "SELECT id, idchauffeur, track_lng,track_lat, IsInZone, track_time, adresse,
                        (SELECT CONCAT(UPPER(TRIM(NOM)), ' ', UPPER(TRIM(PRENOM))) FROM reshumaine 
                        WHERE id = gps_sani.idchauffeur) as chauffeur
                                FROM gps_sani WHERE LEFT(track_time, 10) = $dateactive AND 
                                idchauffeur = $idchauffeur AND nocamion = $nocamion AND  
                                IsInZone =1 and id > $idstart AND id < $idstop   order by track_time";
                    $query = $this->db->query($strSQL);
                    foreach ($query->getResult() as $row) {
                        //$arrSimply[] = array('x' => floatval(str_replace(',', '.', $row->track_lat)), 'y' => floatval(str_replace(',', '.', $row->track_lng)));
                        $returnval .= str_replace(',', '.', $row->track_lat).",".str_replace(',', '.', $row->track_lng).", timespan: ".$row->track_time.", chauffeur: ".$row->chauffeur.", adresse: ".$row->adresse."_";
                    }
                    $arrSimply[] = substr($returnval, 0, -1);
                    $returnval = "";
                    $idstart = $rowsstop->id;
                    //------------------------------------------------------------------------------------------
                }
                $strSQL = "SELECT id, idchauffeur, track_lng,track_lat, IsInZone, track_time, adresse,
                      (SELECT CONCAT(UPPER(TRIM(NOM)), ' ', UPPER(TRIM(PRENOM))) FROM reshumaine 
                      WHERE id = gps_sani.idchauffeur) as chauffeur
                              FROM gps_sani WHERE LEFT(track_time, 10) = $dateactive AND 
                              idchauffeur = $idchauffeur AND nocamion = $nocamion AND  
                              IsInZone =1 and id > $idstop order by track_time";
                $query = $this->db->query($strSQL);
                if ($query->getNumRows() > 0) {
                    foreach ($query->getResult() as $row) {
                        //$arrSimply[] = array('x' => floatval(str_replace(',', '.', $row->track_lat)), 'y' => floatval(str_replace(',', '.', $row->track_lng)));
                        $returnval .= str_replace(',', '.', $row->track_lat).",".str_replace(',', '.', $row->track_lng).", timespan: ".$row->track_time.", chauffeur: ".$row->chauffeur.", adresse: ".$row->adresse."_";
                    }
                    $arrSimply[] = substr($returnval, 0, -1);
                }
            } else {
                $strSQL = "SELECT id, idchauffeur, track_lng,track_lat, IsInZone, track_time, adresse,
                      (SELECT CONCAT(UPPER(TRIM(NOM)), ' ', UPPER(TRIM(PRENOM))) FROM reshumaine 
                      WHERE id = gps_sani.idchauffeur) as chauffeur
                              FROM gps_sani WHERE LEFT(track_time, 10) = $dateactive AND 
                              idchauffeur = $idchauffeur AND nocamion = $nocamion AND  
                              IsInZone =1  order by track_time";
                $query = $this->db->query($strSQL);
                if ($query->getNumRows() == 0) {
                    return [];
                }
                foreach ($query->getResult() as $row) {
                    //$arrSimply[] = array('x' => floatval(str_replace(',', '.', $row->track_lat)), 'y' => floatval(str_replace(',', '.', $row->track_lng)));
                    $returnval .= str_replace(',', '.', $row->track_lat).",".str_replace(',', '.', $row->track_lng).", timespan: ".$row->track_time.", chauffeur: ".$row->chauffeur.", adresse: ".$row->adresse."_";
                }
                $arrSimply[] = substr($returnval, 0, -1);
            }
            return $arrSimply;
    }

    public function GetCamOnRoad($dateactive, $idchauffeur = 0)
    {    
            $dateactiveesc = $this->db->escape($dateactive);
            /** @var string $returnval */
            $returnval = '<button onclick="showCam(0)" style="height:50px;width: 75px;"><img src="/assets/img/google/truck2.png" width="70" alt="" style="margin-left: -5px" /></button><br /><br />';
            /** @var string $chauffeur */
            $chauffeur = '';
            /** @var array $arCam */
            $arCam = array();
    
            if ($idchauffeur > 0) {
                $chauffeur = " AND idchauffeurs = $idchauffeur ";
                $this->data['allchauffeur'] = 0;
            } else {
                $chauffeur = "";
                $this->data['allchauffeur'] = 1;
            }
            /** @var string $markers */
            $markers = '';
            /** @var string $center */
            $center = '';
            /** @var int $iter */
            $iter = 1;
            $chaufcam = '';
            $strTemp = '';
            $latlng = '';
            $class = ' class="form-control" ';
            $this->data['idchauffeur'] = $idchauffeur;
            /** @var string $strSQL */
            $strSQL = "SELECT distinct NOCAMION, idchauffeurs  FROM cedules WHERE
                  cedules.DateJour = $dateactiveesc $chauffeur  ORDER BY NOCAMION";
            $result = $this->db->query($strSQL);
            foreach ($result->getResult() as $row) {
    
    
                $key = array_search($row->NOCAMION, $arCam);
                if (($key == 0) or ($key == false)) {
                    $arCam[$iter] = $row->NOCAMION;
                    $iter++;
                    $returnval .= '<button  
                              onclick="showCam(' . $row->idchauffeurs . ')" style="height:100px;width: 100px;">
                              <img src="/assets/img/google/' . $row->NOCAMION . '.png" width="90" alt="" /></button> <br /><br />';
                }
                //$this->data['nocamion'] = $row->NOCAMION;
                $cam = $this->db->escape($row->NOCAMION);
                $chauf = ($idchauffeur == 0) ? '' : " AND idchauffeur=" . $this->db->escape($idchauffeur);
                $strSQL = "SELECT idchauffeur, nocamion, track_time, track_lng, track_lat, adresse
    	FROM gps_sani_last WHERE nocamion = $cam $chauf";
                $Location = $this->db->query($strSQL)->getRow(0);
                if (isset($Location)) {
                    $latlng .= $row->NOCAMION . '-' . $Location->idchauffeur . '_' . str_replace(',', '.', $Location->track_lat) . ',' . str_replace(',', '.', $Location->track_lng) . ';';
    
                }
                if ($chauf != '') {
                    $this->data['nocamion'] = $row->NOCAMION;
                }
                //$markers .= " addMarkerCam($row->latlng);";
                //$center = $row->latlng;
    
    
            }
            $latlng = substr($latlng, 0, -1);
            //$this->data['markers'] = $markers;
            $this->data['latlng'] = $latlng;
            $this->data['center'] = $center;
            $this->data['chauffeursRte'] = $returnval;
            return $this->data;
    }

    public function GetTypeExtra($typeext = '')
    {    
            /** @var string $returnval */
            $returnval = '';
            /** @var string $strSQL */
            $strSQL = "Select Nom, id from typeextra order BY Nom ";
            $camions = $this->db->query($strSQL);
            foreach ($camions->getResult() as $row) {
                $selected = (($typeext == $row->id) ? ' selected ' : '');
                $returnval .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->Nom . '</option>';
            }
            return $returnval;
    }

    public function filterLocation($latitude, $longitude)
    {    
            $filteredLat = $this->latFilter->filter($latitude);
            $filteredLon = $this->lonFilter->filter($longitude);
            return array('latitude' => $filteredLat, 'longitude' => $filteredLon);
    }

    public function postLocation()
    {    
            $this->load->database();
            $track_lat = $this->db->escape($this->input->post_get('track_lat', TRUE));
            $track_lng = $this->db->escape($this->input->post_get('track_lng', TRUE));
            $nocamion = $this->db->escape($this->input->post_get('nocamion', TRUE));
            $idchauffeur = $this->db->escape($this->input->post_get('idchauffeur', TRUE));
    
            $adresse = $this->db->escape($this->input->post_get('Adresse', TRUE));
            $Statutpoly = $this->db->escape($this->input->post_get('Statutpoly', TRUE));
            $IsInZone = $this->db->escape($this->input->post_get('IsInZone', TRUE));
    
            $query = "INSERT INTO gps_sani
    	                    (idchauffeur, nocamion, track_time, track_lng, track_lat,adresse, Statutpoly, IsInZone)
    	                    VALUES ($idchauffeur, $nocamion, NOW(), $track_lng, $track_lat,$adresse, $Statutpoly, $IsInZone)";
            $this->db->query($query);
    
            $queryLast = "INSERT INTO gps_sani_last
    	                    (nocamion, idchauffeur, track_time, track_lng, track_lat, adresse, Statutpoly, IsInZone)
    	                    VALUES ($nocamion, $idchauffeur, NOW(), $track_lng, $track_lat, $adresse, $Statutpoly, $IsInZone)
    	                    ON DUPLICATE KEY UPDATE
    	                      idchauffeur=VALUES(idchauffeur), track_time=VALUES(track_time),
    	                      track_lng=VALUES(track_lng), track_lat=VALUES(track_lat),
    	                      adresse=VALUES(adresse), Statutpoly=VALUES(Statutpoly), IsInZone=VALUES(IsInZone)";
            $this->db->query($queryLast);
    }
}
