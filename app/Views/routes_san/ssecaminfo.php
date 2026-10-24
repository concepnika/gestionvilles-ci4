<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
//---------------- Connexion à la base de données 
$hostname_dbprotect = "www.robert-daoust.ca";
$database_dbprotect = "rdfroutes_cn_dev";
$username_dbprotect = "rdfroutes_cduserdev";
$password_dbprotect = "!Exya@R62HR%1r!0";
$dbprotect = new mysqli(
  $hostname_dbprotect,
  $username_dbprotect,
  $password_dbprotect,
  $database_dbprotect
);
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
    $strSQL = "SELECT nocamion,  REPLACE(track_lng, ',', '.') as lat, 
                    REPLACE(track_lat, ',', '.')  as lng, track_time, adresse 
                 FROM gps_sani 
                 WHERE LEFT(track_time, 10) = $dteJour AND IsInZone = 1 order by nocamion, track_time";
    $result = $this->db->query($strSQL);
    $updates = $result->result_array();
    foreach (($updates ?? []) as $value) {
      if ($cam != $value['nocamion']) {
        $cam = $value['nocamion'];
        if ((array_key_exists($cam, $lastUpdates))) {
          $lastupdates[$cam] = $value['track_time'];
        }
      } else {
        $lastupdates[$cam] = $value['track_time'];
      }
    }
  } else {
    $updates = array();
    $updOK = array();
    $datamerge = array();
    foreach (($lastupdates ?? []) as $key => $value) {
      $dteJour = $this->db->escape($value);
      $cam = $this->db->escape($key);
      $strSQL = "SELECT nocamion, REPLACE(track_lng, ',', '.') as lat, 
                    REPLACE(track_lat, ',', '.')  as lng, track_time, adresse 
                   FROM gps_sani 
                   WHERE track_time > $dteJour AND IsInZone = 1 AND nocamion = $cam order by track_time";
      $result = $this->db->query($strSQL);
      $resultArray = $result->result_array();
      foreach (($resultArray ?? []) as $value) {
        $value['lng'] = str_replace(',', '.', $value['lng']);
        $value['lat'] = str_replace(',', '.', $value['lat']);
        if ($cam != $value['nocamion']) {
          $cam = $value['nocamion'];
          if ((array_key_exists($cam, $lastUpdates))) {
            $lastupdates[$cam] = $value['track_time'];
          }
        } else {
          $lastupdates[$cam] = $value['track_time'];
        }
      }
      $updates = array_merge($updates, $resultArray);
    }
  }

  $this->GetCamOnRoad($dteJour);
  // Préparez vos données à envoyer au client
  $data = [
    'camions' => $updates,
    'chauffeursRte' => $this->data['chauffeursRte'],
    // Vous pouvez ajouter d'autres données si nécessaire
  ];

  // Envoi des données au format JSON en utilisant le format SSE (voir le préfixe "data:")
  echo "data: " . json_encode($data) . "\n\n";

  @ob_flush();
  @flush();

  // Attente de 10 secondes avant une nouvelle vérification
  sleep(10);

  // Si le client a fermé la connexion
  if (connection_aborted()) {
    break;
  }
}

