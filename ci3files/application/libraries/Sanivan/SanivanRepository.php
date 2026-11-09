<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SanivanRepository — Couche d'accès aux données pour le rapport Sanivan.
 *
 * Toutes les requêtes SQL sont centralisées ici.
 * Aucune logique métier ni génération HTML.
 */
class SanivanRepository
{
    /** @var object CI instance */
    private $CI;
    /** @var object CI DB */
    private $db;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
    }

    // ===================================================================
    //  LECTURES (SELECT)
    // ===================================================================

    /**
     * Retourne la ligne optionssys (taux salariaux, prix fuel, etc.).
     *
     * @return object|null
     */
    public function getOptionsSys()
    {
        return $this->db->query("SELECT * FROM optionssys")->row(0);
    }

    /**
     * Liste des camions ayant un enregistrement camionjour pour la date donnée,
     * filtrés sur TypeCamion IN (1,7) et présents dans les cédules.
     *
     * @param string      $date         Date Y-m-d
     * @param string|null $camionFilter NOCAMION unique (ou null = tous)
     * @return object[] Lignes avec NOCAMION, id
     */
    public function getCamionsJour($date, $camionFilter = null)
    {
        $dteEsc = $this->db->escape($date);
        $extra  = '';
        if ($camionFilter !== null && trim($camionFilter) !== '') {
            $extra = ' AND NOCAMION = ' . $this->db->escape($camionFilter) . ' ';
        }
        $sql = "SELECT DISTINCT NOCAMION, id
                  FROM camionjour
                 WHERE DateJour = $dteEsc
                   AND camionjour.NOCAMION IN (SELECT DISTINCT cedules.NOCAMION FROM cedules WHERE cedules.DateJour = $dteEsc)
                   AND (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = camionjour.NOCAMION) IN (1,7)
                   $extra
              ORDER BY NOCAMION";
        return $this->db->query($sql)->result();
    }

    /**
     * Vérifie si le chauffeur principal d'un camion pour une date est un broker.
     *
     * @param string $cam  NOCAMION (déjà échappé)
     * @param string $date Date Y-m-d
     * @return bool
     */
    public function isBrokerForCam($cam, $date)
    {
        $camEsc = $this->db->escape($cam);
        $dteEsc = $this->db->escape($date);
        $row = $this->db->query(
            "SELECT IFNULL((SELECT reshumaine.isbroker FROM reshumaine
                WHERE reshumaine.id = (SELECT idchauffeurs FROM cedules
                    WHERE NOCAMION = $camEsc AND DateJour = $dteEsc LIMIT 1)), 0) AS isbroker"
        )->row(0);
        return (isset($row) && $row->isbroker == 1);
    }

    /**
     * Récupère les données de transport pour un camion/date.
     * Utilisé par SanivanCalculator::calculateTransportFromRows().
     *
     * @param string $cam  NOCAMION
     * @param string $date Date Y-m-d
     * @return object CI_DB_result
     */
    public function getTransportData($cam, $date)
    {
        $camEsc = $this->db->escape($cam);
        $dteEsc = $this->db->escape($date);
        $sql = "SELECT DISTINCT ROW_NUMBER() OVER (ORDER BY id) AS numero_ligne,
                  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS Chauffeur,
                  camionjour.NOCAMION, cedules.remplace_cam, cedules.NOCAMION as camcedule,
                  LEFT((SELECT Nom FROM typecamion WHERE typecamion.id =
                      (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = camionjour.NOCAMION)), 2) AS TypeCam,
                  camionjour.KiloDeb, camionjour.KiloFin, (camionjour.KiloFin - camionjour.KiloDeb) AS KM,
                  camionjour.Fuel, camionjour.HreDeb, camionjour.HreFin,
                  LEFT(TIMEDIFF(camionjour.HreFin, camionjour.HreDeb), 5) AS HrsTravCam,
                  cedules.idchauffeurs, cedules.HELPERS_1, cedules.HELPERS_2, cedules.HELPERS_3, cedules.HELPERS_4,
                  LEFT(TIMEDIFF(sanivan_rh.HreFin, sanivan_rh.HreDeb), 5) AS HreTravRH,
                  info_villes.HreDeb AS HreVilleDeb, info_villes.HreFin AS HreVilleFin,
                  IF(ROW_NUMBER() OVER (ORDER BY id) > 1, '0',
                      ROUND(TIME_TO_SEC(TIMEDIFF(info_villes.HreDeb, camionjour.HreDeb)) / 60, 1)) AS Transport_RDF,
                  LEFT(TIMEDIFF(info_villes.HreFin, info_villes.HreDeb), 5) AS HreTravVille,
                  IF(info_depotoire.fin IS NULL, '0', info_depotoire.fin) AS FinDepotoire,
                  IF(info_depotoire.fin IS NULL, '0',
                      ROUND(TIME_TO_SEC(TIMEDIFF(info_depotoire.fin, info_villes.HreFin)))) / 60 AS Transport_DEP,
                  IF(info_depotoire.fin IS NULL, '0',
                      ROUND(TIME_TO_SEC(TIMEDIFF(camionjour.HreFin, info_depotoire.fin)))) / 60 AS Transport_TO_RDF,
                  camionjour.id AS idcam, info_depotoire.Tonnage,
                  cedules.ID, sanivan_rh.TypeRh, sanivan_rh.id AS idrh, cedules.idvilles
                FROM cedules
                  LEFT JOIN info_villes    ON cedules.ID = info_villes.idcedule
                  LEFT JOIN sanivan_rh     ON cedules.ID = sanivan_rh.idcedule
                  LEFT JOIN camionjour     ON cedules.NOCAMION = camionjour.NOCAMION
                  LEFT JOIN info_depotoire ON info_villes.id = info_depotoire.idinfoville
                WHERE cedules.DateJour = $dteEsc
                  AND camionjour.DateJour = $dteEsc
                  AND (cedules.NOCAMION = $camEsc OR cedules.remplace_cam = $camEsc)
                ORDER BY info_villes.HreDeb";
        return $this->db->query($sql);
    }

    /**
     * Récupère la ligne cédule/camionjour principale pour un camion/date.
     *
     * @param string $cam  NOCAMION
     * @param string $date Date Y-m-d
     * @return object[] Résultat (généralement 1 ligne)
     */
    public function getCeduleForTruck($cam, $date)
    {
        $camEsc = $this->db->escape($cam);
        $dteEsc = $this->db->escape($date);
        $sql = "SELECT DISTINCT
                  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS Chauffeur,
                  IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs), 0) AS isbroker,
                  camionjour.NOCAMION, camionjour.KiloDeb, camionjour.KiloFin, (camionjour.KiloFin - camionjour.KiloDeb) AS KM,
                  camionjour.Fuel, camionjour.HreDeb, camionjour.HreFin,
                  LEFT(TIMEDIFF(camionjour.HreFin, camionjour.HreDeb), 5) AS HrsTrav,
                  cedules.idchauffeurs, cedules.HELPERS_1, cedules.HELPERS_2, cedules.HELPERS_3,
                  cedules.HELPERS_4, camionjour.id AS idcam, cedules.ID, cedules.remplace_cam, cedules.idremplace_chauf,
                  (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
                  IFNULL((SELECT sanivan_rh.idcedule FROM sanivan_rh WHERE sanivan_rh.idcedule = cedules.ID LIMIT 0,1), 0) AS HaveRH,
                  (SELECT LEFT(TIMEDIFF(info_villes.HreFin, info_villes.HreDeb), 5)
                     FROM info_villes WHERE info_villes.idcedule = cedules.ID LIMIT 0,1) AS HresVille,
                  IFNULL((SELECT sanivan_rh.montant_forfaitaire FROM sanivan_rh
                     WHERE sanivan_rh.idcedule = cedules.ID AND sanivan_rh.TypeRh = 0 LIMIT 1), 0) AS montant_forfaitaire,
                  IFNULL((SELECT sanivan_rh.id FROM sanivan_rh
                     WHERE sanivan_rh.idcedule = cedules.ID AND sanivan_rh.TypeRh = 0 LIMIT 1), 0) AS idsanrh
                FROM cedules
                  LEFT JOIN info_villes ON cedules.ID = info_villes.idcedule
                  INNER JOIN camionjour ON cedules.NOCAMION = camionjour.NOCAMION
                WHERE cedules.DateJour = $dteEsc
                  AND camionjour.DateJour = $dteEsc
                  AND cedules.NOCAMION = $camEsc
                ORDER BY cedules.NOCAMION, cedules.ID
                LIMIT 0,1";
        return $this->db->query($sql)->result();
    }

    /**
     * Récupère les camions de remplacement pour un camion/date.
     *
     * @param string $cam       NOCAMION (déjà échappé)
     * @param string $dteactive Date (déjà échappée)
     * @return object[] Résultat
     */
    public function getReplacementTruck($cam, $dteactive)
    {
        $sql = "SELECT DISTINCT
                  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS Chauffeur,
                  camionjour.NOCAMION, camionjour.KiloDeb, camionjour.KiloFin, (camionjour.KiloFin - camionjour.KiloDeb) AS KM,
                  camionjour.Fuel, camionjour.HreDeb, camionjour.HreFin,
                  LEFT(TIMEDIFF(camionjour.HreFin, camionjour.HreDeb), 5) AS HrsTrav,
                  cedules.idchauffeurs, cedules.HELPERS_1, cedules.HELPERS_2, cedules.HELPERS_3,
                  cedules.HELPERS_4, camionjour.id AS idcam, cedules.ID, cedules.remplace_cam,
                  (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
                  IFNULL((SELECT sanivan_rh.idcedule FROM sanivan_rh WHERE sanivan_rh.idcedule = cedules.ID LIMIT 0,1), 0) AS HaveRH,
                  (SELECT LEFT(TIMEDIFF(info_villes.HreFin, info_villes.HreDeb), 5)
                     FROM info_villes WHERE info_villes.idcedule = cedules.ID LIMIT 0,1) AS HresVille
                FROM cedules
                  INNER JOIN info_villes ON cedules.ID = info_villes.idcedule
                  INNER JOIN camionjour  ON cedules.remplace_cam = camionjour.NOCAMION
                WHERE cedules.DateJour = $dteactive
                  AND camionjour.DateJour = $dteactive
                  AND cedules.NOCAMION = $cam
                ORDER BY cedules.NOCAMION, cedules.ID
                LIMIT 0,1";
        return $this->db->query($sql)->result();
    }

    /**
     * Récupère les données RH (chauffeur + aides) pour une cédule/date.
     *
     * @param string $date       Date Y-m-d
     * @param object $rowCedule  Ligne cédule (avec idchauffeurs, HELPERS_1..4, idremplace_chauf)
     * @return object CI_DB_result
     */
    public function getRHData($date, $rowCedule)
    {
        $dteEsc = $this->db->escape($date);
        $where = " ( sanivan_rh.idrh = " . $rowCedule->idchauffeurs;
        $where .= (trim($rowCedule->idremplace_chauf) == "0" || trim($rowCedule->idremplace_chauf) == "") ? "" : " OR sanivan_rh.idrh = " . $rowCedule->idremplace_chauf;
        $where .= (trim($rowCedule->HELPERS_1) == "0" || trim($rowCedule->HELPERS_1) == "") ? "" : " OR sanivan_rh.idrh = " . $rowCedule->HELPERS_1;
        $where .= (trim($rowCedule->HELPERS_2) == "0" || trim($rowCedule->HELPERS_2) == "") ? "" : " OR sanivan_rh.idrh = " . $rowCedule->HELPERS_2;
        $where .= (trim($rowCedule->HELPERS_3) == "0" || trim($rowCedule->HELPERS_3) == "") ? "" : " OR sanivan_rh.idrh = " . $rowCedule->HELPERS_3;
        $where .= (trim($rowCedule->HELPERS_4) == "0" || trim($rowCedule->HELPERS_4) == "") ? "" : " OR sanivan_rh.idrh = " . $rowCedule->HELPERS_4;
        $where .= ')';

        $sql = "SELECT sanivan_rh.id,
                  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh) AS Chauffeur,
                  IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh), 0) AS isbroker,
                  IFNULL(sanivan_rh.montant_forfaitaire, 0) AS montant_forfaitaire,
                  TypeRh, HreDeb, HreFin, HreDeb_extra, HreFin_extra, TypeExtra,
                  IFNULL(
                      LEFT(ADDTIME(TIMEDIFF(HreFin, HreDeb), TIMEDIFF(HreFin_extra, HreDeb_extra)), 5),
                      LEFT(TIMEDIFF(HreFin, HreDeb), 5)
                  ) AS HrsTrav
                FROM sanivan_rh
                WHERE sanivan_rh.datejour = $dteEsc AND $where
                ORDER BY idcedule, TypeRh";
        return $this->db->query($sql);
    }

    /**
     * Récupère les villes (info_villes) pour un camion/date.
     *
     * @param string $date   Date Y-m-d
     * @param string $camion NOCAMION
     * @return object CI_DB_result
     */
    public function getVillesForTruck($date, $camion)
    {
        $dteEsc = $this->db->escape($date);
        $camEsc = $this->db->escape($camion);
        $sql = "SELECT (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp) AS aRow,
                  info_villes.id, idville, idcedule, HreDeb, HreFin, nbBac, cedules.TypeDeProduit,
                  (SELECT ville.Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
                  LEFT(TIMEDIFF(info_villes.HreFin, info_villes.HreDeb), 5) AS HrsTrav, cedules.NOCAMION,
                  (SELECT Tonnage FROM info_depotoire WHERE info_depotoire.idinfoville = info_villes.id) AS Tonnage,
                  (SELECT Depotoire FROM info_depotoire WHERE info_depotoire.idinfoville = info_villes.id) AS Depotoire
                FROM info_villes
                  INNER JOIN cedules ON info_villes.idcedule = cedules.ID
                WHERE cedules.DateJour = $dteEsc
                  AND cedules.NOCAMION = $camEsc
                ORDER BY info_villes.id";
        return $this->db->query($sql);
    }

    /**
     * Récupère les dépotoires pour une info_ville donnée.
     *
     * @param int $idinfoville
     * @return object CI_DB_result
     */
    public function getDepotoireForVille($idinfoville)
    {
        $sql = "SELECT info_depotoire.id, idinfoville, IsTransBo, Ticket, Tonnage, debut, Depotoire, fin, boite,
                  LEFT(TIMEDIFF(info_depotoire.fin, info_depotoire.debut), 5) AS HrsTrav
                FROM info_depotoire
                  INNER JOIN info_villes ON info_depotoire.idinfoville = info_villes.id
                WHERE info_depotoire.idinfoville = " . (int) $idinfoville;
        return $this->db->query($sql);
    }

    /**
     * Récupère le prix_sani d'un dépotoire par son nom.
     *
     * @param string $depotoireName
     * @return object|null  Objet avec ->prix_sani
     */
    public function getDepotoirePrix($depotoireName)
    {
        $dep = $this->db->escape($depotoireName);
        return $this->db->query("SELECT prix_sani FROM depotoire WHERE nom = $dep")->row(0);
    }

    /**
     * Retourne le tonnage total pour un camion/date à partir des dépotoires.
     *
     * @param string $cam  NOCAMION
     * @param string $date Date Y-m-d
     * @return float
     */
    public function getTonnageForTruck($cam, $date)
    {
        $camEsc = $this->db->escape($cam);
        $dteEsc = $this->db->escape($date);
        $sql = "SELECT IFNULL(SUM(REPLACE(info_depotoire.Tonnage, ',', '.')), 0) AS totalTonnage
                FROM info_depotoire
                  INNER JOIN info_villes ON info_depotoire.idinfoville = info_villes.id
                  INNER JOIN cedules ON info_villes.idcedule = cedules.ID
                WHERE cedules.DateJour = $dteEsc
                  AND cedules.NOCAMION = $camEsc";
        $row = $this->db->query($sql)->row(0);
        return ($row) ? floatval($row->totalTonnage) : 0;
    }

    /**
     * Retourne les options HTML du type extra.
     *
     * @param string $selectedId  ID actuellement sélectionné
     * @return string HTML <option>
     */
    public function getTypeExtraOptions($selectedId = '')
    {
        $returnval = '';
        $rows = $this->db->query("SELECT Nom, id FROM typeextra ORDER BY Nom")->result();
        foreach ($rows as $row) {
            $sel = ($selectedId == $row->id) ? ' selected ' : '';
            $returnval .= '<option value="' . $row->id . '" ' . $sel . '>' . $row->Nom . '</option>';
        }
        return $returnval;
    }

    // ===================================================================
    //  ÉCRITURES (INSERT / UPDATE)
    // ===================================================================

    /**
     * Persiste (INSERT ou UPDATE) une ligne sanivanday pour un camion/date.
     *
     * @param string $camion NOCAMION
     * @param string $date   Date Y-m-d
     * @param array  $data   Tableau associatif (clés = colonnes sanivanday)
     * @return int ID de la ligne
     */
    public function saveSanivanDay($camion, $date, array $data)
    {
        $camEsc = $this->db->escape($camion);
        $dteEsc = $this->db->escape($date);

        $exists = $this->db->query("SELECT id FROM sanivanday WHERE DateJour = $dteEsc AND NOCAMION = $camEsc")->row(0);

        if (!$exists) {
            $data['DateJour']  = $date;
            $data['NOCAMION']  = $camion;
            $this->db->insert('sanivanday', $data);
            return $this->db->insert_id();
        } else {
            $this->db->where('NOCAMION', $camion);
            $this->db->where('DateJour', $date);
            $this->db->update('sanivanday', $data);
            return $exists->id;
        }
    }

    // ===================================================================
    //  POST HANDLERS (mêmes signatures que Sanivan_model)
    // ===================================================================

    /**
     * Met à jour camionjour depuis les données POST.
     * @return string "OK"
     */
    public function postCamionJour()
    {
        $idcam   = $this->db->escape($this->CI->input->post_get('idcam', TRUE));
        $KiloDeb = $this->db->escape(str_replace(',', '', $this->CI->input->post_get('KiloDeb', TRUE)));
        $KiloFin = $this->db->escape(str_replace(',', '', $this->CI->input->post_get('KiloFin', TRUE)));
        $fuel    = $this->db->escape($this->CI->input->post_get('Fuel', TRUE));
        $HreDeb  = $this->db->escape($this->CI->input->post_get('HreDeb', TRUE));
        $HreFin  = $this->db->escape($this->CI->input->post_get('HreFin', TRUE));

        $this->db->query("UPDATE camionjour SET KiloDeb=$KiloDeb, KiloFin=$KiloFin,
            Fuel=$fuel, HreDeb=$HreDeb, HreFin=$HreFin WHERE id = $idcam");
        return "OK";
    }

    /**
     * Met à jour sanivan_rh (heures chauffeur/aide) depuis les données POST.
     * @return string "OK"
     */
    public function postRHDay()
    {
        $idrh        = $this->db->escape($this->CI->input->post_get('idrh', TRUE));
        $TypeExtra   = $this->db->escape($this->CI->input->post_get('TypeExtra', TRUE));
        $HreDeb      = $this->db->escape($this->CI->input->post_get('HreDeb', TRUE));
        $HreFin      = $this->db->escape($this->CI->input->post_get('HreFin', TRUE));
        $HreDebExtra = $this->db->escape($this->CI->input->post_get('HreDeb_extra', TRUE));
        $HreFinExtra = $this->db->escape($this->CI->input->post_get('HreFin_extra', TRUE));

        $this->db->query("UPDATE sanivan_rh SET HreDeb=$HreDeb, HreFin=$HreFin,
            HreDeb_extra=$HreDebExtra, HreFin_extra=$HreFinExtra, TypeExtra=$TypeExtra
            WHERE id = $idrh");
        return "OK";
    }

    /**
     * Met à jour le montant forfaitaire (broker) depuis les données POST.
     * @return string "OK"
     */
    public function postRHDayBroker()
    {
        $idrh    = $this->db->escape($this->CI->input->post_get('idrh', TRUE));
        $montant = $this->db->escape($this->CI->input->post_get('montant', TRUE));
        $this->db->query("UPDATE sanivan_rh SET montant_forfaitaire=$montant WHERE id = $idrh");
        return "OK";
    }

    /**
     * Met à jour info_depotoire depuis les données POST.
     * @return string "OK"
     */
    public function postDepDay()
    {
        $iddeb     = $this->db->escape($this->CI->input->post_get('iddeb', TRUE));
        $isTransbo = $this->db->escape($this->CI->input->post_get('istransbo', TRUE));
        $Depotoire = $this->db->escape($this->CI->input->post_get('Depotoire', TRUE));
        $Tonnage   = $this->db->escape(str_replace(',', '', $this->CI->input->post_get('Tonnage', TRUE)));
        $boite     = $this->db->escape($this->CI->input->post_get('boite', TRUE));
        $debut     = $this->db->escape($this->CI->input->post_get('debut', TRUE));
        $fin       = $this->db->escape($this->CI->input->post_get('fin', TRUE));

        $this->db->query("UPDATE info_depotoire SET IsTransBo=$isTransbo, Depotoire=$Depotoire,
            Tonnage=$Tonnage, debut=$debut, fin=$fin, boite=$boite WHERE id = $iddeb");
        return "OK";
    }

    /**
     * Met à jour info_villes depuis les données POST.
     * @return string "OK"
     */
    public function postVilleDay()
    {
        $id    = $this->db->escape($this->CI->input->post_get('id', TRUE));
        $nbBac = $this->db->escape($this->CI->input->post_get('nbBac', TRUE));
        $HreDeb = $this->db->escape($this->CI->input->post_get('HreDeb', TRUE));
        $HreFin = $this->db->escape($this->CI->input->post_get('HreFin', TRUE));

        $this->db->query("UPDATE info_villes SET HreDeb=$HreDeb, HreFin=$HreFin, nbBac=$nbBac WHERE id = $id");
        return "OK";
    }
}
