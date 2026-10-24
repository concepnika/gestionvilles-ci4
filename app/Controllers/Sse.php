<?php

namespace App\Controllers;

/**
 * Port CI3 -> CI4 de Sse : flux Server-Sent Events des positions GPS
 * des camions (sanivan/star_sse). Port mecanique via
 * port_controller.php + divergences SSE documentees ci-dessous.
 */
class Sse extends BaseController
{
    /** Miroir de $this->data (CI3) — rempli par GetCamOnRoad. */
    private $data = array();

    public function index()
    {
        //----------------------------------------------- Envoyer les headers SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        //-----------------------------------------------
        // Divergences SSE vs CI3 (nécessaires en CI4) :
        //  - set_time_limit(0) : la boucle infinie serait tuée par
        //    max_execution_time (CI3 avait le même problème en prod) ;
        //  - vidange de TOUS les niveaux de buffer : CI4 bufferise la
        //    sortie, ob_flush() seul ne suffit pas pour streamer.
        @set_time_limit(0);
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        /** @var array $lastUpdates */
        $lastUpdates = array();
        /** @var array $updates */
        $updates = array();
        /** @var string $dteJour */
        $dteJour = $this->db->escape($this->input->get('date'));
        if (empty($dteJour)) {
          echo "data: " . json_encode(['error' => 'Date manquante']) . "\n\n";
          exit;
        }
        // Boucle continue qui envoie un flux SSE
        while (TRUE) {
          $cam = "";
          if (empty($lastupdates)) {
            $strSQL = "SELECT nocamion,  REPLACE(track_lng, ',', '.') as lng, 
                        REPLACE(track_lat, ',', '.')  as lat, track_time, adresse 
                     FROM gps_sani 
                     WHERE LEFT(track_time, 10) = $dteJour AND IsInZone = 1 order by nocamion, track_time";
            $result = $this->db->query($strSQL);
            $updates = $result->getResultArray();
            foreach ($updates as $value)
            {
            	if($cam != $value['nocamion'])
              {
                $cam = $value['nocamion'];
                if ((array_key_exists($cam, $lastUpdates)))
                {
                	$lastupdates[$cam] = $value['track_time'];
                }
              }
              else
              {
              	$lastupdates[$cam] = $value['track_time'];
              }
            }
          }
          else {
            $updates = array();
            $updOK = array();
            $datamerge = array();
            foreach ($lastupdates as $key => $value) {
              $dteJour = $this->db->escape($value);
              $cam = $this->db->escape($key);
              $strSQL = "SELECT nocamion, REPLACE(track_lng, ',', '.') as lat, 
                        REPLACE(track_lat, ',', '.')  as lng, track_time, adresse 
                       FROM gps_sani 
                       WHERE track_time > $dteJour AND IsInZone = 1 AND nocamion = $cam order by track_time";
              $result = $this->db->query($strSQL);
              $resultArray = $result->getResultArray();
              foreach ($resultArray as $value) {
                $lastupdates[$cam] = $value['track_time'];
              }
              $updates = array_merge($updates, $resultArray);
            }
          }
          
          $this->GetCamOnRoad($dteJour);
          // Pr�parez vos donn�es � envoyer au client
          $data = [
            'camions' => $updates,
            'chauffeursRte' => $this->data['chauffeursRte'],
            // Vous pouvez ajouter d'autres donn�es si n�cessaire
          ];
    
          // Envoi des donn�es au format JSON en utilisant le format SSE (voir le pr�fixe "data:")
          echo "data: " . json_encode($data) . "\n\n";
    
          @ob_flush();
          @flush();
    
          // Attente de 10 secondes avant une nouvelle v�rification
          sleep(10);
    
          // Si le client a ferm� la connexion
          if (connection_aborted()) {
            break;
          }
        }
    }



    private function getCamLocation($lastupdates)
    {    
        /** @var string $returnval */
        $returnval = '';
        /** @var array $arrCamLoc */
        $arrCamLoc = array();
        foreach ($lastupdates as $key => $value) {
          $dteJour = $this->db->escape($value);
          $cam = $this->db->escape($key);
          $strSQL = "SELECT REPLACE(track_lng, ',', '.'), 
                            REPLACE(track_lat, ',', '.'), adresse
    	                  FROM gps_sani WHERE LEFT(track_time, 10) = $dteJour AND 
                          nocamion = $cam ORDER BY track_time DESC LIMIT 0,1 ";
          $result = $this->db->query($strSQL);
          foreach ($result->getResult() as $row) {
            $arrCamLoc[$cam] = $cam;
          }
        }
    }

    public function GetCamOnRoad($dateactive, $idchauffeur = 0)
    {    
        
        /** @var string $returnval */
        $returnval = '<button id="ville_0" onclick="ShowCamionSan(0)" style="height:50px;width: 75px;"><img src="/assets/img/google/truck2.png" width="70" alt="" style="margin-left: -5px" /></button><br /><br />';
        /** @var string $chauffeur */
        $chauffeur = '';
        /** @var array $arCam */
        $arCam = array();
    
        if ($idchauffeur > 0) {
          $chauffeur = " AND idchauffeurs = $idchauffeur ";
          $this->data['allchauffeur'] = 0;
        }
        else {
          $chauffeur = " AND idchauffeurs  IN (SELECT distinct gps_sani.idchauffeur FROM gps_sani WHERE LEFT(track_time, 10) =
                                  cedules.DateJour  and gps_sani.idchauffeur = cedules.idchauffeurs)";
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
        $strSQL = "SELECT ID, CONCAT('div_', idchauffeurs, '_', NOCAMION) as CamChauf,
                     (SELECT g.track_lat FROM gps_sani_last g
    												WHERE g.nocamion = cedules.NOCAMION) AS track_lat,
                      (SELECT g.track_lng FROM gps_sani_last g
    												WHERE g.nocamion = cedules.NOCAMION) AS track_lng,
                      (SELECT g.track_time FROM gps_sani_last g
    												WHERE g.nocamion = cedules.NOCAMION) AS track_time,
                  (SELECT CONCAT( Nom, ', ', left(Prenom, 1)) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS NomPrenom,
                    NOCAMION, idchauffeurs FROM cedules WHERE
                  cedules.DateJour = $dateactive $chauffeur ORDER BY NomPrenom, track_time";
        $result = $this->db->query($strSQL);
        foreach ($result->getResult() as $row) {
          if ($this->data['idchauffeur'] == 0) {
            $this->data['idchauffeur'] = 0;
          }
          if ($chaufcam != $row->CamChauf) {
            $key = array_search($row->NOCAMION, $arCam);
            if (($key == 0) or ($key == FALSE)) {
              $arCam[$iter] = $row->NOCAMION;
              $iter++;
              $returnval .= '<button id="ville_' . $row->ID . '" onclick="ShowCamionSan(' . $row->idchauffeurs . ')" style="height:100px;width: 100px;">
      <img src="/assets/img/google/' . $row->NOCAMION . '.png" width="90" alt="" /></button>
      <br /><br />';
            }
            //$markers .= " addMarkerCam($row->latlng);";
            //$center = $row->latlng;
          }
    
        }
        $this->data['chauffeursRte'] = $returnval;
        //return $this->data;
    }

    public function __destruct()
    {    
        $this->db->close();
    }
}
