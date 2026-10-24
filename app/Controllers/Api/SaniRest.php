<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\OsrmSnapper;
use App\Libraries\SamsaraClient;
use App\Libraries\SaniRestService;

/**
 * Port CI4 de application/controllers/Co_Rest_Sani.php (CI3).
 *
 * API REST des tablettes camions Sanivan. Les clients appellent :
 *   GET  /sanivan/get?action=N&param=...   (indexGet — dispatcher)
 *   GET  /sanivan/get/login/{user}/{pass}  (login)
 *   POST /sanivan/post/depot | /api/upload (indexPost — POST_POLYLINE)
 *   POST /sanivan/post/camions             (postCamion)
 *   POST /api/add_to_polyline | /sanivan/send_polyline (saveBatch)
 */
class SaniRest extends BaseController
{
    // Actions (constantes CI3 — mêmes valeurs numériques)
    private const LIST_CAMIONS = 1;
    private const LIST_CHAUFFEUR = 2;
    private const GETMAXENDKILO = 3;
    private const GET_CEDULES = 4;
    private const POST_LOCALISATION = 5;
    private const POST_CHAUFFEURS = 6;
    private const POST_CAMION = 7;
    private const LIST_DEPOTOIRES = 8;
    private const VERIF_LOGIN = 10;
    private const VERIF_ADMIN = 11;
    private const GET_CHAUFFEUR = 13;
    private const GET_AIDES = 14;
    private const GET_CAMION = 15;
    private const GET_VILLE = 16;
    private const GET_DEPOTOIRE = 17;
    private const POST_VILLE = 19;
    private const POST_DEPOTOIRE = 20;
    private const GET_NB_CEDULES = 21;
    private const GET_NB_AIDES = 22;
    private const POST_AIDES = 24;
    private const POST_DEPOT_ENTRY = 25;
    private const GET_NB_DEPOTS = 27;
    private const ADD_DEPOTOIRE = 28;
    private const POST_ALL_VILLEDEPOT_ENTRY = 29;
    private const RELOAD_VILLE = 30;
    private const ADD_INFO_VILLE = 31;
    private const GET_NB_VILLES = 32;
    private const GET_EXTRA = 33;
    private const GET_PDF_TICKET = 34;
    private const GET_POLYLINE = 35;
    private const POST_KM_DEPART = 36;
    private const GET_LOCALISATION = 37;
    private const ADD_ITEM_CEDULE = 38;
    private const POST_CAMJOUR = 39;
    private const CHG_CHAUFFEUR_CEDULES = 40;
    private const POST_CHAUFJOUR = 41;
    private const POST_POLYLINE = 42;
    private const GET_ALL_TAG = 43;
    private const GET_CEDULE_POLYLINES = 44;
    private const GET_POLYLINE_CAM_TODAY = 45;
    private const GET_AUTORISATION_SPECTATEUR = 46;
    private const GET_POLYLINE_DYNAMIQUE = 47;

    private SaniRestService $svc;
    private int $httpCode = 400;
    private string $keysha = 'nikajhdgsjgdsdssd';
    private array $arrLogin = ['id' => '', 'isadmin' => ''];

    public function __construct()
    {
        date_default_timezone_set('America/Toronto');
        $this->svc = new SaniRestService();
    }

    /**
     * Équivalent du $this->response($data, $code) du RestController CI3
     * (rest_default_format = 'json'). json_encode() appliqué à tout —
     * y compris les chaînes, que setJSON() enverrait sinon non-quotées.
     */
    private function respond($data, int $code = 200)
    {
        return $this->response
            ->setStatusCode($code)
            ->setContentType('application/json')
            ->setBody(json_encode($data));
    }

    /** post_get CI3 : POST d'abord, puis GET. */
    private function req(string $key)
    {
        return $this->request->getPostGet($key);
    }

    /**
     * GET /sanivan/get/login/{CodeUser}/{password}
     * LoginDriver() n'existe pas dans CI3 (route morte en prod) — implémenté
     * ici avec la même logique que loginChauffeur (reshumaine + pepper).
     */
    public function login(string $CodeUser, string $password)
    {
        $result = $this->loginDriver($CodeUser, $password);
        return $this->respond($result, 200);
    }

    private function loginDriver(string $CodeUser, string $password): array
    {
        $pwdPeppered = hash_hmac('sha256', $password, $this->keysha);
        $user = $this->db->table('reshumaine')->where('login', $CodeUser)->get()->getRow();
        if ($user === null || ! password_verify($pwdPeppered, $user->pass)) {
            return ['idchauffeur' => '', 'NomComplet' => ''];
        }
        return [
            'idchauffeur' => $user->id,
            'NomComplet' => trim(($user->Prenom ?? '') . ' ' . ($user->Nom ?? '')),
        ];
    }

    /**
     * GET /sanivan/get — dispatcher action=N
     */
    public function indexGet()
    {
        $action = $this->req('action');

        switch ((int) $action) {
            case self::LIST_CAMIONS:
                $dteActive = $this->db->escape(date('Y-m-d'));
                $strSQL = "SELECT camions.NoCamion FROM camions WHERE typecamion IN (1,7)
	                      AND camions.NoCamion NOT IN  (SELECT distinct NOCAMION   FROM cedules WHERE
	                            cedules.DateJour = '2025-06-10' AND
							    cedules.NOCAMION IN (SELECT camionjour.NOCAMION FROM camionjour
	                            WHERE camionjour.HreFin = $dteActive AND camionjour.DateJour = $dteActive))   ORDER BY NOCAMION";
                $returnval = '';
                foreach ($this->db->query($strSQL)->getResult() as $row) {
                    $returnval .= $row->NoCamion . '_';
                }
                return $this->respond(substr($returnval, 0, -1), 200);

            case self::GET_POLYLINE_CAM_TODAY:
                $nocamion = $this->req('camion');
                $idchauffeur = $this->req('idchauffeur');
                if (empty($nocamion)) {
                    return $this->respond(['ok' => false, 'error' => 'nocamion manquant'], 400);
                }

                $dateactive = date('Y-m-d');
                $segments = $this->getPolylineDynamiquePublic($idchauffeur, $dateactive, $nocamion, 1);

                $polylines = [];
                if (is_array($segments) && ! empty($segments)) {
                    foreach ($segments as $seg) {
                        $pts = array_filter(explode('_', $seg), 'strlen');
                        $coords = [];
                        foreach ($pts as $pt) {
                            $parts = explode(',', $pt);
                            if (count($parts) >= 2) {
                                $lat = (float) trim($parts[0]);
                                $lng = (float) trim($parts[1]);
                                $coords[] = [$lat, $lng];
                            }
                        }
                        if (count($coords) >= 2) {
                            $polylines[] = $coords;
                        }
                    }
                }
                return $this->respond(['ok' => true, 'polylines' => $polylines], 200);

            case self::GET_AUTORISATION_SPECTATEUR:
                $CodeUser = $this->req('CodeUser');
                $password = $this->req('password');
                $codechauffeur = $this->req('codechauffeur');
                $camionchauffeur = $this->req('camionchauffeur');
                $dteactive = $this->req('dteactive');

                if (empty($CodeUser) || empty($password)) {
                    return $this->respond(['ok' => false, 'error' => 'CodeUser/password manquants'], 400);
                }

                $codeEsc = $this->db->escape($codechauffeur);
                $strSQL = "SELECT reshumaine.id
                        FROM reshumaine
			            WHERE reshumaine.login = $codeEsc";
                $res = $this->db->query($strSQL)->getRowArray();
                $idchauffeur = (int) ($res['id'] ?? 0);

                // Résoudre l'id Samsara du camion fourni (si présent)
                $idsamsara = '';
                if (! empty($camionchauffeur)) {
                    $nocam = trim((string) $camionchauffeur);
                    if (strlen($nocam) < 3) {
                        $nocam = '0' . $nocam;
                    }
                    try {
                        $idsamsara = (string) (new SamsaraClient())->findVehicleIdByName($nocam);
                    } catch (\Throwable $e) {
                        $idsamsara = '';
                    }
                }
                return $this->respond(['ok' => true, 'idchauffeur' => $idchauffeur, 'idsamsara' => $idsamsara], 200);

            case self::GET_POLYLINE_DYNAMIQUE:
                $idchauffeur = $this->req('idchauffeur');
                $dateactive = $this->req('dateactive');
                $nocamion = $this->req('nocamion');
                $snap = (int) $this->req('snap');

                if (empty($dateactive) || empty($nocamion)) {
                    return $this->respond(['ok' => false, 'error' => 'dateactive et nocamion requis'], 400);
                }

                $segments = $this->getPolylineDynamiquePublic($idchauffeur, $dateactive, $nocamion, $snap);
                return $this->respond(['ok' => true, 'segments' => $segments], 200);

            case self::LIST_CHAUFFEUR:
                $strSQL = "SELECT CONCAT(Prenom, ' ', Nom) AS NomComplet
                           FROM reshumaine WHERE
                                isdeleted = 0 AND  idreshum IN (1,3,4,7) AND
                                CONCAT(Prenom, ' ', Nom) IS NOT NULL
                           ORDER BY  Nom, Prenom";
                $returnval = '';
                foreach ($this->db->query($strSQL)->getResult() as $row) {
                    $returnval .= $row->NomComplet . '_';
                }
                return $this->respond(substr($returnval, 0, -1), 200);

            case self::VERIF_LOGIN:
                $result = $this->loginChauffeur();
                return $this->respond($result, $this->httpCode);

            case self::GETMAXENDKILO:
                $camion = $this->req('camion');
                $cam = $this->db->escape($camion);
                $query = "SELECT KiloFin as KiloDeb FROM camionjour
                            WHERE  KiloFin > 0 AND NOCAMION = $cam
                                        ORDER BY id DESC LIMIT 0,1";
                $result = $this->db->query($query)->getRowArray();
                return $this->respond(intval($result['KiloDeb'] ?? 0), 200);

            case self::GET_POLYLINE:
                $id = $this->db->escape($this->req('idpolyline'));
                $query = "SELECT parcours, urlfile
		                    FROM polilyne_ville where id = $id";
                $polyline = $this->db->query($query)->getRowArray();
                if (empty($polyline)) {
                    return $this->respond('Aucune donnée trouvée', 400);
                }
                return $this->respond($polyline['parcours'], 200);

            case self::GET_CEDULE_POLYLINES:
                $idcedule = $this->db->escape($this->req('idCedule'));
                $data = $this->getPolylinesByCedule($idcedule);
                return $this->respond(['success' => true, 'data' => $data], 200);

            case self::POST_LOCALISATION:
                $track_lat = $this->db->escape($this->req('track_lat'));
                $track_lng = $this->db->escape($this->req('track_lng'));
                $heading = $this->req('heading');
                $heading = $this->db->escape($heading === null ? '0.00' : $heading);
                $nocamion = $this->db->escape($this->req('nocamion'));
                $idchauffeur = $this->db->escape($this->req('idchauffeur'));
                $adresse = $this->db->escape($this->req('Adresse'));
                $Statutpoly = $this->db->escape($this->req('Statutpoly'));
                $IsInZone = $this->db->escape($this->req('IsInZone'));

                $query = "INSERT INTO gps_sani
		                      (idchauffeur, nocamion, track_time, track_lng, track_lat, heading, adresse, Statutpoly, IsInZone)
		                      VALUES ($idchauffeur, $nocamion, NOW(), $track_lng, $track_lat, $heading, $adresse, $Statutpoly, $IsInZone)";
                $this->db->query($query);

                $queryLast = "INSERT INTO gps_sani_last
		                      (nocamion, idchauffeur, track_time, track_lng, track_lat, heading, adresse, Statutpoly, IsInZone)
		                      VALUES ($nocamion, $idchauffeur, NOW(), $track_lng, $track_lat, $heading, $adresse, $Statutpoly, $IsInZone)
		                      ON DUPLICATE KEY UPDATE
		                        idchauffeur=VALUES(idchauffeur), track_time=VALUES(track_time),
		                        track_lng=VALUES(track_lng), track_lat=VALUES(track_lat), heading=VALUES(heading),
		                        adresse=VALUES(adresse), Statutpoly=VALUES(Statutpoly), IsInZone=VALUES(IsInZone)";
                $this->db->query($queryLast);
                return $this->respond('OK', 200);

            case self::GET_NB_CEDULES:
                $date = date('Y-m-d');
                $dteActive = $this->req('dteactive');
                if ((isset($dteActive) and ($dteActive != ''))) {
                    $date = $dteActive;
                }
                $idchauffeur = $this->req('idchauffeur');
                $nbRows = $this->svc->getNbCedule($date, $idchauffeur);
                return $this->respond($nbRows, 200);

            case self::ADD_ITEM_CEDULE:
                $camion = $this->db->escape($this->req('camion'));
                $oldcamion = $this->db->escape($this->req('oldcamion'));
                $idcedule = $this->db->escape($this->req('idcedule'));
                $strSQL = "UPDATE cedules
		                      SET
			                      NOCAMION=$camion,
			                      remplace_cam=$oldcamion
		                      WHERE ID =  $idcedule";
                $this->db->query($strSQL);
                return $this->respond(null, 200);

            case self::CHG_CHAUFFEUR_CEDULES:
                $chauffeur = $this->db->escape($this->req('chauffeur'));
                $oldchauffeur = $this->db->escape($this->req('oldchauffeur'));
                $strSQL = "SELECT reshumaine.id FROM reshumaine WHERE
                          UPPER(CONCAT(Prenom, ' ', Nom)) = $chauffeur";
                $res = $this->db->query($strSQL)->getRow(0);
                $idchauffeur = $res->id;
                $idcedule = $this->db->escape($this->req('idcedule'));
                $strSQL = "UPDATE cedules
		                      SET
			                      idchauffeurs=$idchauffeur,
			                      idremplace_chauf=$oldchauffeur
		                      WHERE ID =  $idcedule";
                $this->db->query($strSQL);
                return $this->respond($idchauffeur, 200);

            case self::POST_CHAUFJOUR:
                $idcedule = $this->db->escape($this->req('idcedule'));
                $idchauffeur = $this->db->escape($this->req('idchauffeur'));
                $strSQL = "INSERT INTO sanivan_rh
		                    (idcedule, idrh, TypeRh, datejour)
		                    VALUES ($idcedule, $idchauffeur, 0, CURDATE())";
                $this->db->query($strSQL);
                $strSQL = "SELECT CONCAT(Prenom, ' ', Nom) AS NomComplet
	                              FROM reshumaine WHERE id = $idchauffeur";
                $chauf = $this->db->query($strSQL)->getRow(0);
                return $this->respond($chauf->NomComplet, 200);

            case self::POST_CAMJOUR:
                $date = $this->db->escape(date('Y-m-d'));
                $camion = $this->db->escape($this->req('camion'));
                $idchauffeur = $this->db->escape($this->req('idchauffeur'));
                $strSQL = "INSERT INTO camionjour
		                  (idchauffeur, NOCAMION, DateJour)
		                  VALUES ($idchauffeur, $camion, $date)";
                $this->db->query($strSQL);
                return $this->respond(null, 200);

            case self::GET_CEDULES:
                $idcedule = $this->req('idcedule');
                $idcedule = str_replace('"', '', (string) $idcedule);
                $result = $this->svc->getInfoCedule($idcedule);
                return $this->respond($result, 200);

            case self::GET_CAMION:
                $idchauffeur = $this->req('idchauffeur');
                $nocamion = $this->req('nocamion');
                $id = $this->req('id');
                $DateJour = $this->req('DateJour');
                $KiloDeb = $this->req('KiloDeb');
                $result = $this->svc->getInfoCamion($nocamion, $idchauffeur, $id, $DateJour, $KiloDeb);
                return $this->respond($result, 200);

            case self::POST_CAMION:
                $id = $this->db->escape($this->req('id'));
                $KmDeb = $this->db->escape($this->req('KiloDeb'));
                $KmFin = $this->db->escape($this->req('KiloFin'));
                $Fuel = $this->db->escape($this->req('Fuel'));
                $HerDeb = $this->db->escape($this->req('HreDeb'));
                $HreFin = $this->db->escape($this->req('HreFin'));
                $strSQL = "UPDATE camionjour
		                          SET
			                          KiloDeb=$KmDeb,
			                          KiloFin=$KmFin,
			                          Fuel=$Fuel,
			                          HreDeb=$HerDeb,
			                          HreFin=$HreFin
		                          WHERE id=$id";
                $this->db->query($strSQL);
                $error = $this->db->error();
                if (! empty($error['code'])) {
                    return $this->respond($error['message'], 400);
                }
                return $this->respond('OK', 200);

            case self::POST_KM_DEPART:
                $camion = $this->db->escape($this->req('nocamion'));
                $KmDeb = $this->db->escape($this->req('kmdepart'));
                $strSQL = "UPDATE camionjour
		                          SET
			                          KiloDeb=$KmDeb
		                          WHERE  NOCAMION = $camion AND DateJour =CURDATE() ";
                $this->db->query($strSQL);
                return $this->respond('OK', 200);

            case self::POST_CHAUFFEURS:
                $id = $this->db->escape($this->req('id'));
                $HreDeb_extra = $this->db->escape($this->req('HreDeb_extra'));
                $HreFin_extra = $this->db->escape($this->req('HreFin_extra'));
                $TypeExtra = $this->db->escape($this->req('TypeExtra'));
                $HerDeb = $this->db->escape($this->req('HreDeb'));
                $HreFin = $this->db->escape($this->req('HreFin'));
                $strSQL = "UPDATE sanivan_rh
		                      SET
			                      TypeExtra=$TypeExtra,
			                      HreDeb=$HerDeb,
			                      HreFin=$HreFin,
			                      HreDeb_extra=$HreDeb_extra,
			                      HreFin_extra=$HreFin_extra
		                      WHERE id=$id";
                $this->db->query($strSQL);
                $error = $this->db->error();
                if (! empty($error['code'])) {
                    return $this->respond($error['message'], 400);
                }
                return $this->respond('OK', 200);

            case self::POST_AIDES:
                $id = $this->db->escape($this->req('id'));
                $HerDeb = $this->db->escape($this->req('HreDeb'));
                $HreFin = $this->db->escape($this->req('HreFin'));
                $strSQL = "UPDATE sanivan_rh
		                      SET
			                      HreDeb=$HerDeb,
			                      HreFin=$HreFin
		                      WHERE id=$id";
                $this->db->query($strSQL);
                $error = $this->db->error();
                if (! empty($error['code'])) {
                    return $this->respond($error['message'], 400);
                }
                return $this->respond('OK', 200);

            case self::GET_CHAUFFEUR:
                $idchauffeur = $this->req('idchauffeur');
                $id = $this->req('id');
                $DateJour = $this->req('DateJour');
                $idcedule = $this->req('idcedule');
                $result = $this->svc->getChauffeur($idchauffeur, $id, $DateJour, $idcedule);
                return $this->respond($result, 200);

            case self::GET_AIDES:
                $idaide = $this->req('idaide');
                $id = $this->req('id');
                $date = $this->db->escape($this->req('DateJour'));
                $idcedule = $this->req('idcedule');
                $result = $this->svc->getaide($idaide, $id, $date, $idcedule);
                return $this->respond($result, 200);

            case self::GET_NB_AIDES:
                $idcedule = $this->req('idcedule');
                $result = $this->svc->getNbAide($idcedule);
                return $this->respond($result, 200);

            case self::GET_NB_VILLES:
                $idcedule = str_replace('"', '', (string) $this->req('idcedule'));
                $result = $this->svc->getNbVilles($idcedule);
                return $this->respond($result, 200);

            case self::GET_VILLE:
                $idville = $this->req('idville');
                $id = $this->req('id');
                $date = $this->req('DateJour');
                $idcedule = $this->req('idcedule');
                $result = $this->svc->getVille($idville, $id, $date, $idcedule);
                return $this->respond($result, 200);

            case self::RELOAD_VILLE:
                $id = (int) $this->req('idville');
                $idcedule = (int) $this->req('idcedule');
                $strSQL = "SELECT id as Id, idville as IdVille,
               (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
               idcedule as IdCedule, HreDeb, HreFin, nbBac
               FROM info_villes WHERE id = " . $this->db->escape($id) . " AND idcedule = " . $this->db->escape($idcedule);
                $row = $this->db->query($strSQL)->getRowArray();
                if ($row) {
                    return $this->respond($row, 200);
                }
                return $this->respond(['ok' => false, 'error' => 'Not found'], 404);

            case self::ADD_INFO_VILLE:
                $idville = $this->db->escape($this->req('idville'));
                $idcedule = $this->db->escape($this->req('idcedule'));
                $strSQL = "INSERT INTO info_villes
		                  (idville, idcedule)
		                  VALUES ($idville, $idcedule)";
                $this->db->query($strSQL);
                $strSQL = "SELECT id as Id, idville as IdVille,
		                      (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
		                      idcedule as IdCedule, HreDeb, HreFin, nbBac
		                    FROM info_villes WHERE idville = $idville AND idcedule = $idcedule
		                    ORDER BY id desc limit 0, 1";
                $req = $this->db->query($strSQL);
                $result = [];
                foreach ($req->getResultArray() as $row) {
                    $idinfoville = $this->db->escape($row['Id']);
                    $strSQL = "INSERT INTO info_depotoire
			                (idinfoville)
			                VALUES ($idinfoville)";
                    $this->db->query($strSQL);
                    $result = $row;
                }
                return $this->respond($result, 200);

            case self::POST_VILLE:
                $id = $this->db->escape($this->req('id'));
                $champ = $this->req('champ');
                $value = $this->db->escape($this->req('value'));
                $strSQL = "UPDATE info_villes
		                      SET
			                      $champ=$value
		                      WHERE id= $id";
                $this->db->query($strSQL);
                return $this->respond('OK', 200);

            case self::ADD_DEPOTOIRE:
                $idville = str_replace('"', '', (string) $this->req('idville'));
                $idvilleEsc = $this->db->escape($idville);
                $strSQL = "INSERT INTO info_depotoire
		                  (idinfoville)
		                  VALUES ($idvilleEsc)";
                $this->db->query($strSQL);
                $resId = $this->db->query('SELECT LAST_INSERT_ID() as id')->getRow(0);
                $strSQL = "SELECT id as Id, Depotoire as NomDepotoire, debut as HreDeb, fin as HreFin,
		                            idinfoville as IdVille, Ticket, Tonnage, IsTransbo, IsAvecMat, boite as Boite
		                          FROM info_depotoire WHERE id = " . $resId->id;
                $result = $this->db->query($strSQL);
                return $this->respond($result, 200);

            case self::GET_NB_DEPOTS:
                $idville = str_replace('"', '', (string) $this->req('idville'));
                $result = $this->svc->getNbDepot($idville);
                return $this->respond($result, 200);

            case self::GET_DEPOTOIRE:
                $idville = str_replace('"', '', (string) $this->req('idville'));
                $result = $this->svc->getDepotoire($idville);
                return $this->respond($result, 200);

            case self::LIST_DEPOTOIRES:
                $depots = $this->svc->getListDepotoire();
                return $this->respond($depots, 200);

            case self::GET_ALL_TAG:
                return $this->listAllOpenMapTags();

            case self::GET_EXTRA:
                $depots = $this->svc->getTypeExtra();
                return $this->respond($depots, 200);

            case self::VERIF_ADMIN:
                $id = $this->db->escape($this->req('id'));
                $query = "SELECT isadmin  FROM reshumaine WHERE id = $id";
                $result = $this->db->query($query)->getRowArray();
                return $this->respond(intval($result['isadmin'] ?? 0), 200);

            case self::POST_DEPOTOIRE:
                $id = $this->db->escape($this->req('id'));
                $depotoire = $this->db->escape($this->req('depotoire'));
                $strSQL = "UPDATE info_depotoire
		                      SET
			                      Depotoire=$depotoire
		                      WHERE id= $id";
                $this->db->query($strSQL);
                return $this->respond('OK', 200);

            case self::POST_ALL_VILLEDEPOT_ENTRY:
                $id = $this->db->escape($this->req('id'));
                $HerDeb = $this->db->escape($this->req('HreDeb'));
                $HreFin = $this->db->escape($this->req('HreFin'));
                $nbBac = $this->db->escape($this->req('nbBac'));
                $strSQL = "UPDATE info_villes
		                        SET
			                        HreDeb=$HerDeb,
			                        HreFin=$HreFin,
			                        nbBac=$nbBac
		                        WHERE id = $id";
                $this->db->query($strSQL);
                return $this->respond('OK', 200);

            case self::POST_DEPOT_ENTRY:
                $id = $this->db->escape($this->req('id'));
                $champ = $this->req('champ');
                if (($champ == 'IsTransBo') or ($champ == 'IsAvecMat')) {
                    $value = $this->req('value');
                } else {
                    $value = $this->db->escape($this->req('value'));
                }
                $strSQL = "UPDATE info_depotoire
		                      SET
			                      $champ=$value
		                      WHERE id= $id";
                $this->db->query($strSQL);
                return $this->respond('OK', 200);

            case self::GET_PDF_TICKET:
                $id = $this->db->escape($this->req('id'));
                $strSQL = "SELECT Ticket
	                  FROM info_depotoire WHERE id = $id";
                $result = $this->db->query($strSQL)->getRow(0);
                $filePath = $result->Ticket ?? '';
                if ($filePath !== '' && file_exists($filePath)) {
                    return $this->response->download($filePath, null)
                        ->setFileName(basename($filePath));
                }
                return $this->respond('Fichier non trouvé.', 404);

            case self::GET_LOCALISATION:
                $dateactive = $this->req('dteactive');
                $data = $this->getCamOnRoad((string) $dateactive);
                if (empty($data)) {
                    return $this->respond('Aucune donnée trouvée', 400);
                }
                return $this->respond($data, 200);
        }

        return $this->respond(['error' => 'Action inconnue'], 400);
    }

    /**
     * POST /sanivan/post/depot | /api/upload — POST_POLYLINE (action=42).
     * Accepte action en query string ou dans le corps JSON.
     */
    public function indexPost()
    {
        $rawBody = $this->request->getBody();
        $json = null;
        if ($rawBody !== '' && $rawBody !== false) {
            $tmp = json_decode($rawBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $json = $tmp;
            }
        }

        // Priorité: ?action=, sinon JSON['action']
        $action = $this->request->getGet('action');
        if (! is_numeric($action) && isset($json['action'])) {
            $action = $json['action'];
        }
        if (! is_numeric($action)) {
            return $this->respond(['error' => 'Action manquante ou invalide'], 400);
        }

        $file = $this->request->getFile('file');
        if ($file !== null && $file->getName() !== '') {
            return $this->respond('1', 200);
        }

        switch ((int) $action) {
            case self::POST_POLYLINE:
                $idville = is_array($json) && array_key_exists('idville', $json)
                    ? $json['idville'] : $this->request->getPost('idville');
                $idsecteur = is_array($json) && array_key_exists('idsecteur', $json)
                    ? $json['idsecteur'] : $this->request->getPost('idsecteur');
                $pointsIn = is_array($json) && array_key_exists('points', $json)
                    ? $json['points'] : $this->request->getPost('points');
                $alreadyEncoded = is_array($json) && array_key_exists('encoded', $json)
                    ? (bool) $json['encoded'] : false;

                if ($idville === null || $idsecteur === null || $pointsIn === null) {
                    return $this->respond(['error' => 'Paramètres requis: idville, idsecteur, points'], 400);
                }
                if (! is_numeric($idville) || ! is_numeric($idsecteur)) {
                    return $this->respond(['error' => 'idville/idsecteur doivent être numériques'], 400);
                }

                $rawCount = 0;
                if ($alreadyEncoded) {
                    $encoded = (string) $pointsIn;
                    $decodedTest = $this->decodePolyline($encoded);
                    if (empty($decodedTest)) {
                        return $this->respond(['error' => 'Chaîne encodée invalide ou vide'], 400);
                    }
                    $rawCount = count($decodedTest);
                } else {
                    if (! is_array($pointsIn) || empty($pointsIn)) {
                        return $this->respond(['error' => 'Format points non valide (tableau attendu)'], 400);
                    }
                    $cleanPts = [];
                    foreach ($pointsIn as $p) {
                        if (is_array($p)) {
                            if (isset($p['lat'], $p['lng']) && is_numeric($p['lat']) && is_numeric($p['lng'])) {
                                $cleanPts[] = [(float) $p['lat'], (float) $p['lng']];
                            } elseif (count($p) === 2 && is_numeric($p[0]) && is_numeric($p[1])) {
                                $cleanPts[] = [(float) $p[0], (float) $p[1]];
                            }
                        }
                    }
                    if (empty($cleanPts)) {
                        return $this->respond(['error' => 'Aucun point valide'], 400);
                    }
                    $rawCount = count($cleanPts);
                    $encoded = $this->encodePolyline($cleanPts);
                }

                $idvilleEsc = $this->db->escape($idville);
                $idsecteurEsc = $this->db->escape($idsecteur);
                $encodedEsc = $this->db->escape($encoded);

                $row = $this->db->query("SELECT id FROM polilyne_ville WHERE idville=$idvilleEsc AND idsecteur=$idsecteurEsc")->getRow();

                if ($row) {
                    $this->db->query("UPDATE polilyne_ville SET parcours=$encodedEsc WHERE id=" . (int) $row->id);
                    $err = $this->db->error();
                    if (! empty($err['code'])) {
                        return $this->respond(['error' => $err['message']], 400);
                    }
                    $mode = 'update';
                    $idRecord = (int) $row->id;
                } else {
                    $this->db->query("INSERT INTO polilyne_ville (idville, idsecteur, parcours, nom)
	                           VALUES ($idvilleEsc, $idsecteurEsc, $encodedEsc, 'Nouveau parcours')");
                    $err = $this->db->error();
                    if (! empty($err['code'])) {
                        return $this->respond(['error' => $err['message']], 400);
                    }
                    $mode = 'insert';
                    $idRecord = (int) $this->db->insertID();
                }

                return $this->respond([
                    'status' => 'OK',
                    'mode' => $mode,
                    'id' => $idRecord,
                    'encoded_length' => strlen($encoded),
                    'points_count' => $rawCount
                ], 200);
        }

        return $this->respond(['error' => 'Action inconnue'], 400);
    }

    /**
     * POST /sanivan/post/camions — mise à jour camionjour (payload JSON).
     */
    public function postCamion()
    {
        $data = $this->request->getJSON(true);
        if ($data === null) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Erreur de décodage des données JSON.']);
        }

        $id = $data['id'] ?? null;
        $this->db->table('camionjour')->where('id', $id)->update($data);
        $error = $this->db->error();
        if (! empty($error['code'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => $error['message']]);
        }
        return $this->response->setStatusCode(200)
            ->setJSON(['message' => 'Données reçues et traitées avec succès.']);
    }

    /**
     * POST /api/add_to_polyline | /sanivan/send_polyline — batch GPS → gps_polyline.
     */
    public function saveBatch()
    {
        $input = $this->request->getJSON(true);
        if (empty($input['driver_id']) ||
            empty($input['truck_id']) ||
            empty($input['positions']) ||
            ! is_array($input['positions'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Payload invalide']);
        }

        $driverId = (int) $input['driver_id'];
        $truckId = $input['truck_id'];
        $saved = 0;

        foreach ($input['positions'] as $p) {
            if (isset($p['lat'], $p['lng'], $p['timestamp'], $p['address'])
                && is_numeric($p['lat'])
                && is_numeric($p['lng'])
                && is_string($p['address'])) {
                $data = [
                    'driver_id' => $driverId,
                    'truck_id' => $truckId,
                    'latitude' => (float) $p['lat'],
                    'longitude' => (float) $p['lng'],
                    'address' => $p['address'],
                    'timestamp' => $p['timestamp'],
                ];
                if ($this->addToPoly($data)) {
                    $saved++;
                }
            }
        }

        return $this->response->setJSON([
            'received' => count($input['positions']),
            'inserted' => $saved
        ]);
    }

    private function addToPoly(array $data): bool
    {
        $driverId = $this->db->escape($data['driver_id']);
        $truckId = $this->db->escape($data['truck_id']);
        $latitude = $this->db->escape($data['latitude']);
        $longitude = $this->db->escape($data['longitude']);
        $address = $this->db->escape($data['address']);
        $date = date('Y-m-d H:i:s', strtotime($data['timestamp']));
        $timestamp = $this->db->escape($date);
        $strSQL = "INSERT INTO gps_polyline
	              (idchauffeur, nocamion, track_time,
	              track_lng, track_lat, adresse)
	              VALUES ($driverId, $truckId, $timestamp,
	                          $longitude, $latitude, $address)";
        return (bool) $this->db->query($strSQL);
    }

    /**
     * POST /co_rest_sani/saveToFile — upload photo ticket.
     * (pas de route dédiée dans CI3 — accès direct par nom de méthode)
     */
    public function saveToFile()
    {
        $post = $this->request->getPost();
        $filename = 'pic_' . date('YmdHis') . '.jpeg';
        // L'URL retournee (/upload/sanivan/...) est servie par le docroot CI3 :
        // on ecrit donc dans le vrai dossier upload de CI3, pas dans public/.
        $urlName = rtrim((string) env('sanivan.uploadDir', ''), '/') . '/';
        if ($urlName === '/') {
            $urlName = FCPATH . 'upload/sanivan/';
        }
        if (! file_exists($urlName)) {
            mkdir($urlName, 0777, true);
        }
        $file = $this->request->getFile('image');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $file->move($urlName, $filename);
        }
        return $this->respond('/upload/sanivan/' . $filename, 200);
    }

    // ------------------------------------------------------------------
    // Helpers privés
    // ------------------------------------------------------------------

    /**
     * Wrapper de getPolylineDynamique + snap OSRM
     * (équivalent Co_tablette_sani::GetPolylineDynamiquePublic).
     */
    private function getPolylineDynamiquePublic($idchauffeur, $dateactive, $nocamion, int $snap = 0): array
    {
        $segments = $this->svc->getPolylineDynamique($idchauffeur, $dateactive, $nocamion);
        if (! is_array($segments) || empty($segments)) {
            return [];
        }
        if ((int) $snap !== 1) {
            return $segments;
        }

        $snapped = (new OsrmSnapper())->snapSegments($segments);
        if (! is_string($snapped) || $snapped === '') {
            return $segments;
        }
        $out = explode('|', $snapped);
        return is_array($out) ? $out : $segments;
    }

    private function getPolylinesByCedule($idcedule): array
    {
        $sql = "
            SELECT
                (SELECT parcours
                 FROM polilyne_ville
                 WHERE polilyne_ville.id = cedule_polylines.idpolyline
                ) AS parcours,
                cedule_polylines.ordre,
                cedule_polylines.idpolyline
            FROM cedule_polylines
            WHERE idcedule = $idcedule
            ORDER BY cedule_polylines.ordre
        ";
        return $this->db->query($sql)->getResultArray();
    }

    private function listAllOpenMapTags()
    {
        $rows = $this->db->query(
            "SELECT id, id_ville, tag_code, address_text, latitude, longitude, reason,
                description, image_path, status, response_text, responded_at, created_at, updated_at
           FROM map_tags
          WHERE status <> 'DONE'
       ORDER BY created_at DESC"
        )->getResultArray();

        if (! empty($rows) && $this->db->tableExists('map_tag_responses')) {
            $tagIds = array_map(static fn ($r) => (int) $r['id'], $rows);
            $tagIds = array_values(array_unique(array_filter($tagIds)));
            if (! empty($tagIds)) {
                $in = implode(',', array_map('intval', $tagIds));
                $respRows = $this->db->query(
                    "SELECT r.id, r.id_map_tag, r.id_ville, r.status, r.response_text, r.created_at, r.created_by
                   FROM map_tag_responses r
                  WHERE r.id_map_tag IN ($in)
               ORDER BY r.created_at ASC, r.id ASC"
                )->getResultArray();

                $authors = [];
                $authorIds = array_values(array_unique(array_filter(array_map(static fn ($r) => (int) ($r['created_by'] ?? 0), $respRows))));
                if (! empty($authorIds)) {
                    $inA = implode(',', array_map('intval', $authorIds));
                    $aRows = $this->db->query(
                        "SELECT id, CONCAT(COALESCE(prenom,''),' ',COALESCE(nom,'')) AS NomComplet
                       FROM villesrecipiendaire
                      WHERE id IN ($inA)"
                    )->getResultArray();
                    foreach ($aRows as $a) {
                        $authors[(int) $a['id']] = trim($a['NomComplet']);
                    }
                }

                $byTag = [];
                foreach ($respRows as $rr) {
                    $byTag[(int) $rr['id_map_tag']][] = [
                        'text' => (string) ($rr['response_text'] ?? ''),
                        'author' => (string) ($authors[(int) ($rr['created_by'] ?? 0)] ?? ''),
                        'time' => (string) ($rr['created_at'] ?? ''),
                        'status' => (string) ($rr['status'] ?? '')
                    ];
                }
                foreach ($rows as &$row) {
                    $row['discussions'] = $byTag[(int) $row['id']] ?? [];
                }
                unset($row);
            }
        }

        return $this->response
            ->setContentType('application/json', 'utf-8')
            ->setBody(json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Camions en route pour la journée + dernière position connue.
     * (port de Co_Rest_Sani::GetCamOnRoad — GET_LOCALISATION)
     */
    private function getCamOnRoad(string $dateactive): array
    {
        $dateactiveesc = $this->db->escape($dateactive);
        $dateStart = $this->db->escape($dateactive . ' 00:00:00');
        $dateEnd   = $this->db->escape($dateactive . ' 23:59:59');
        $data = [];

        $returnval = '<button onclick="showCam(0)" style="height:50px;width: 75px;">TOUT</button><br /><br />';
        $latlng = '';
        $strSQL = "SELECT distinct NOCAMION, idchauffeurs FROM cedules WHERE
			  cedules.DateJour = $dateactiveesc AND
			  EXISTS (SELECT 1 FROM gps_sani g WHERE g.nocamion = cedules.NOCAMION
					  AND g.track_time BETWEEN $dateStart AND $dateEnd)
			  ORDER BY NOCAMION";
        $result = $this->db->query($strSQL);

        $camList = [];
        $camRows = [];
        foreach ($result->getResult() as $row) {
            $camList[] = $row->NOCAMION;
            $camRows[] = $row;
        }

        $lastPositions = [];
        if (! empty($camList)) {
            $camInList = implode(',', array_map([$this->db, 'escape'], $camList));
            $sqlLast = "SELECT g.idchauffeur, g.nocamion, g.track_time, g.track_lng, g.track_lat, g.adresse
						FROM gps_sani g
						INNER JOIN (
							SELECT nocamion, MAX(track_time) AS max_time
							FROM gps_sani
							WHERE nocamion IN ($camInList)
							  AND track_time BETWEEN $dateStart AND $dateEnd
							GROUP BY nocamion
						) latest ON g.nocamion = latest.nocamion AND g.track_time = latest.max_time
						WHERE g.track_time BETWEEN $dateStart AND $dateEnd
						  AND g.nocamion IN ($camInList)";
            foreach ($this->db->query($sqlLast)->getResult() as $loc) {
                $lastPositions[$loc->nocamion] = $loc;
            }
        }

        foreach ($camRows as $row) {
            $returnval .= '<label id="lbl_' . $row->NOCAMION . '" class="form-control"
						onclick="showCam(' . $row->idchauffeurs . ',\'' . $row->NOCAMION . '\')"
						style="font-size:medium;font-weight:bold;background-color: darkgrey;
						color: white;height:40px;width: 140px;">' .
                $row->NOCAMION . '&nbsp;&nbsp;&nbsp;
						<input type="checkbox" style="width:20px;height:20px" /></label>';
            if (isset($lastPositions[$row->NOCAMION])) {
                $Location = $lastPositions[$row->NOCAMION];
                $latlng .= $row->NOCAMION . '_' . str_replace(',', '.', $Location->track_lat) .
                    ',' . str_replace(',', '.', $Location->track_lng) . ';';
            }
        }
        $latlng = substr($latlng, 0, -1);
        $data['latlng'] = $latlng;
        $data['chauffeursRte'] = $returnval;
        return $data;
    }

    /**
     * Vérifie login tablette (VERIF_LOGIN) — même logique que
     * login_model::validate_chauffeur + vérif cédule du jour.
     */
    private function loginChauffeur(): array
    {
        $CodeUser = $this->req('CodeUser');
        if (! isset($CodeUser)) {
            $this->httpCode = 400;
            return $this->arrLogin;
        }

        $pwdPeppered = hash_hmac('sha256', (string) $this->req('password'), $this->keysha);
        $user = $this->db->table('reshumaine')->where('login', $CodeUser)->get()->getRow();
        if ($user === null || ! password_verify($pwdPeppered, $user->pass)) {
            $this->httpCode = 400;
            return $this->arrLogin;
        }

        $date = $this->db->escape(date('Y-m-d'));
        $dteActive = $this->req('dteactive');
        if (isset($dteActive) && $dteActive != '') {
            $date = $this->db->escape($dteActive);
        }
        $strSQL = "SELECT  cedules.idchauffeurs as id, reshumaine.isadmin,
                    Concat(reshumaine.Nom, ', ', LEFT(reshumaine.Prenom, 1)) as Nom FROM cedules
                        INNER JOIN reshumaine ON reshumaine.id = cedules.idchauffeurs
			            WHERE  cedules.DateJour = $date AND cedules.idchauffeurs = " . $user->id;
        $res = $this->db->query($strSQL);
        if ($res->getNumRows() == 0) {
            $this->httpCode = 400;
            return $this->arrLogin;
        }
        foreach ($res->getResultArray() as $row) {
            $this->httpCode = 200;
            return $row;
        }

        $this->httpCode = 400;
        return $this->arrLogin;
    }

    // ------------------------------------------------------------------
    // Encodage/décodage Google Encoded Polyline (POST_POLYLINE)
    // ------------------------------------------------------------------

    private function encodePolyline(array $points): string
    {
        $lastLat = 0;
        $lastLng = 0;
        $result = '';
        foreach ($points as $pt) {
            if (! is_array($pt) || count($pt) < 2) {
                continue;
            }
            $lat = (int) round($pt[0] * 1e5);
            $lng = (int) round($pt[1] * 1e5);
            $dLat = $lat - $lastLat;
            $dLng = $lng - $lastLng;
            $lastLat = $lat;
            $lastLng = $lng;
            $result .= $this->encodeSignedNumber($dLat) . $this->encodeSignedNumber($dLng);
        }
        return $result;
    }

    private function encodeSignedNumber($num): string
    {
        $snum = $num << 1;
        if ($num < 0) {
            $snum = ~$snum;
        }
        $output = '';
        while ($snum >= 0x20) {
            $output .= chr((0x20 | ($snum & 0x1f)) + 63);
            $snum >>= 5;
        }
        $output .= chr($snum + 63);
        return $output;
    }

    private function decodePolyline($encoded): array
    {
        $len = strlen($encoded);
        $index = 0;
        $lat = 0;
        $lng = 0;
        $points = [];

        while ($index < $len) {
            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $deltaLat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $deltaLat;

            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $deltaLng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $deltaLng;

            $points[] = [$lat / 1e5, $lng / 1e5];
        }
        return $points;
    }
}
