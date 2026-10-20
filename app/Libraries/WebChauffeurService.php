<?php

namespace App\Libraries;

use Config\Database;

/**
 * Port de Web_chauffeur_model (CI3) — requêtes spécifiques à
 * l'interface web chauffeur. Réutilise les mêmes tables que
 * LibSaniRest / Co_Rest_Sani.
 *
 * Le SQL est repris tel quel de CI3 (échappement via $db->escape)
 * pour garantir des résultats identiques — voir MIGRATION.md
 * « SQL concaténé » pour la migration Query Builder éventuelle.
 */
class WebChauffeurService
{
    private const KEYSHA = 'nikajhdgsjgdsdssd'; // login_model::$keysha

    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ─────────────────────────────────────────────────── AUTH

    /**
     * Vérifie le login chauffeur (login + password).
     * Même logique que Login_model::validate_chauffeur().
     */
    public function verifyLogin($login, $password)
    {
        $pwdPeppered = hash_hmac('sha256', $password, self::KEYSHA);

        $result = $this->db->table('reshumaine')
            ->where('login', $login)
            ->where('isdeleted', 0)
            ->get()
            ->getRow();

        if ($result && password_verify($pwdPeppered, $result->pass)) {
            return [
                'id'         => $result->id,
                'NomComplet' => trim($result->Prenom . ' ' . $result->Nom),
                'isadmin'    => $result->isadmin,
            ];
        }
        return null;
    }

    /**
     * Login complet chauffeur – même logique que Co_Rest_Sani::loginChauffeur().
     * 1) Vérifie login/password via hash_hmac + password_verify
     * 2) Vérifie qu'une cédule existe aujourd'hui pour ce chauffeur
     * Retourne array(id, isadmin, Nom) ou null.
     */
    public function loginChauffeur($codeUser, $password, $dteactive = null)
    {
        $user = $this->verifyLogin($codeUser, $password);
        if ($user === null) {
            return null;
        }

        $date = $this->db->escape(! empty($dteactive) ? $dteactive : date('Y-m-d'));
        $idChauffeur = (int) $user['id'];

        $strSQL = "SELECT cedules.idchauffeurs as id, reshumaine.isadmin, reshumaine.isbroker,
                          CONCAT(reshumaine.Nom, ', ', LEFT(reshumaine.Prenom, 1)) as Nom
                   FROM cedules
                   INNER JOIN reshumaine ON reshumaine.id = cedules.idchauffeurs
                   WHERE cedules.DateJour = $date
                     AND cedules.idchauffeurs = {$idChauffeur}";

        $res = $this->db->query($strSQL);
        if ($res->getNumRows() == 0) {
            return null;
        }

        return $res->getRowArray();
    }

    // ─────────────────────────────────────────────── CÉDULES

    /**
     * Retourne le dernier KM de fin pour un camion (action GETMAXENDKILO).
     */
    public function getMaxEndKilo($nocamion)
    {
        $cam = $this->db->escape($nocamion);
        $row = $this->db->query(
            "SELECT KiloFin as KiloDeb FROM camionjour
             WHERE KiloFin > 0 AND NOCAMION = $cam
             ORDER BY id DESC LIMIT 0,1"
        )->getRow();
        return $row ? $row->KiloDeb : '';
    }

    /**
     * Enregistre le KM de départ (action POST_KM_DEPART).
     */
    public function postKmDepart($nocamion, $kmdepart)
    {
        $cam  = $this->db->escape($nocamion);
        $km   = $this->db->escape(str_replace(',', '.', $kmdepart));
        $date = $this->db->escape(date('Y-m-d'));

        $row = $this->db->query(
            "SELECT id FROM camionjour WHERE NOCAMION = $cam AND DateJour = $date LIMIT 1"
        )->getRow();

        if ($row) {
            $this->db->query("UPDATE camionjour SET KiloDeb = $km WHERE id = " . (int) $row->id);
        }
    }

    /**
     * Retourne les IDs de cédules pour un chauffeur à une date donnée.
     * Même logique que LibSaniRest::getNbCedule().
     */
    public function getCeduleIds($idchauffeur, $date)
    {
        $date = $this->db->escape($date);
        $chauffeurFilter = '';
        if ($idchauffeur > 0) {
            $chauffeurFilter = " AND cedules.idchauffeurs = " . (int) $idchauffeur;
        }

        $result = $this->db->query(
            "SELECT cedules.ID
             FROM cedules
             WHERE cedules.DateJour = $date $chauffeurFilter
               AND (SELECT typecamion FROM camions WHERE NoCamion = cedules.NOCAMION) != 4
             ORDER BY (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp)"
        );

        $ids = [];
        foreach ($result->getResult() as $row) {
            $ids[] = $row->ID;
        }
        return $ids;
    }

    /**
     * Retourne les infos d'une cédule.
     * Même logique que LibSaniRest::getInfoCedule().
     */
    public function getInfoCedule($idcedule)
    {
        $idcedule = (int) $idcedule;
        $rows = $this->db->query(
            "SELECT cedules.ID as Id, cedules.TypeDeProduit, cedules.NOCAMION as NoCamion,
                    DateJour, idsecteur, idpolyline,
                    remplace_cam, idremplace_chauf, HELPERS_1, HELPERS_2, HELPERS_3, HELPERS_4,
                    cedules.idchauffeurs as IdChauffeur, cedules.idvilles as IdVilles,
                    (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
                    (SELECT typededechet.couleur FROM typededechet WHERE typededechet.Nom = cedules.TypeDeProduit) AS BackCouleurDechet,
                    (SELECT typededechet.textcolor FROM typededechet WHERE typededechet.Nom = cedules.TypeDeProduit) AS TextColorDechet
             FROM cedules
             WHERE cedules.ID = {$idcedule}
               AND (SELECT typecamion FROM camions WHERE NoCamion = cedules.NOCAMION) != 4
             ORDER BY (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp)"
        )->getResultArray();
        return $rows[0] ?? [];
    }

    /**
     * Retourne les infos du camion du jour.
     */
    public function getCamionJour($nocamion, $date)
    {
        $cam  = $this->db->escape($nocamion);
        $date = $this->db->escape($date);
        return $this->db->query(
            "SELECT id, idchauffeur, NOCAMION, KiloDeb, KiloFin, Fuel, HreDeb, HreFin, DateJour
             FROM camionjour
             WHERE NOCAMION = $cam AND DateJour = $date
             LIMIT 1"
        )->getRowArray() ?: null;
    }

    /**
     * GET_CAMION – Même logique que LibSaniRest::getInfoCamion().
     * Retourne ou crée le camionjour.
     */
    public function getOrCreateCamionJour($nocamion, $idchauffeur, $dateJour, $kiloDeb)
    {
        $cam   = $this->db->escape($nocamion);
        $date  = $this->db->escape($dateJour);
        $chauf = $this->db->escape($idchauffeur);
        $km    = $this->db->escape($kiloDeb);

        $row = $this->db->query(
            "SELECT id, DateJour, NOCAMION, KiloDeb, KiloFin, Fuel, HreDeb, HreFin
             FROM camionjour WHERE NOCAMION = $cam AND DateJour = $date"
        )->getRowArray();
        if ($row) {
            return $row;
        }

        $this->db->query(
            "INSERT INTO camionjour (idchauffeur, NOCAMION, KiloDeb, DateJour) VALUES ($chauf, $cam, $km, $date)"
        );
        $insertId = $this->db->insertID();

        return $this->db->query(
            "SELECT id, DateJour, NOCAMION, KiloDeb, KiloFin, Fuel, HreDeb, HreFin FROM camionjour WHERE id = {$insertId}"
        )->getRowArray() ?: [];
    }

    /**
     * Retourne les infos chauffeur (nom).
     */
    public function getChauffeurNom($idchauffeur)
    {
        $idchauffeur = (int) $idchauffeur;
        return $this->db->query(
            "SELECT id, CONCAT(Prenom, ' ', Nom) AS NomComplet, isadmin
             FROM reshumaine
             WHERE id = {$idchauffeur} AND isdeleted = 0
             LIMIT 1"
        )->getRowArray() ?: null;
    }

    // ─────────────────────────────────────────── VILLES / DÉPÔTS

    /**
     * Retourne les villes (info_villes) pour une cédule.
     */
    public function getVillesForCedule($idcedule)
    {
        $idcedule = (int) $idcedule;
        return $this->db->query(
            "SELECT info_villes.id AS Id, info_villes.idville AS IdVille,
                    (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
                    info_villes.idcedule AS IdCedule, info_villes.HreDeb, info_villes.HreFin, info_villes.nbBac
             FROM info_villes
             WHERE info_villes.idcedule = {$idcedule}
             ORDER BY info_villes.id"
        )->getResultArray();
    }

    /**
     * Retourne les dépotoires pour une info_ville.
     */
    public function getDepotsForVille($idinfoville)
    {
        $idinfoville = (int) $idinfoville;
        return $this->db->query(
            "SELECT id AS Id, Depotoire AS NomDepotoire, debut AS HreDeb, fin AS HreFin,
                    idinfoville AS IdVille, Ticket, Tonnage, IsTransbo, IsAvecMat, boite AS Boite
             FROM info_depotoire
             WHERE idinfoville = {$idinfoville}
             ORDER BY id"
        )->getResultArray();
    }

    public function getListDepotoires()
    {
        return $this->db->query(
            "SELECT id, Nom FROM depotoire WHERE isdeleted = 0 ORDER BY Nom"
        )->getResultArray();
    }

    public function getListVilles()
    {
        return $this->db->query(
            "SELECT id, Nom FROM ville WHERE isdeleted = 0 ORDER BY Nom"
        )->getResultArray();
    }

    /**
     * POST_VILLE (action 19) – Met à jour un champ d'info_villes.
     */
    public function postVilleField($id, $champ, $value)
    {
        $id    = (int) $id;
        $value = $this->db->escape($value);
        // Sécurité : on n'autorise que les champs connus
        $allowed = ['HreDeb', 'HreFin', 'nbBac'];
        if (! in_array($champ, $allowed)) {
            return;
        }
        $this->db->query("UPDATE info_villes SET {$champ} = {$value} WHERE id = {$id}");
    }

    /**
     * POST_ALL_VILLEDEPOT_ENTRY (action 29) – Met à jour HreDeb, HreFin, nbBac d'une ville.
     */
    public function postAllVilleDepotEntry($id, $hreDeb, $hreFin, $nbBac)
    {
        $id     = (int) $id;
        $hreDeb = $this->db->escape($hreDeb);
        $hreFin = $this->db->escape($hreFin);
        $nbBac  = $this->db->escape($nbBac);
        $this->db->query(
            "UPDATE info_villes SET HreDeb = {$hreDeb}, HreFin = {$hreFin}, nbBac = {$nbBac} WHERE id = {$id}"
        );
    }

    /**
     * POST_DEPOTOIRE (action 20) – Met à jour le nom du dépotoire.
     */
    public function postDepotoire($id, $depotoire)
    {
        $id        = (int) $id;
        $depotoire = $this->db->escape($depotoire);
        $this->db->query("UPDATE info_depotoire SET Depotoire = {$depotoire} WHERE id = {$id}");
    }

    /**
     * POST_DEPOT_ENTRY (action 25) – Met à jour un champ d'info_depotoire.
     */
    public function postDepotField($id, $champ, $value)
    {
        $id = (int) $id;
        $allowed = ['debut', 'fin', 'Tonnage', 'boite', 'IsTransBo', 'IsAvecMat', 'Depotoire'];
        if (! in_array($champ, $allowed)) {
            return;
        }

        if ($champ == 'IsTransBo' || $champ == 'IsAvecMat') {
            $value = (int) $value;
            $this->db->query("UPDATE info_depotoire SET {$champ} = {$value} WHERE id = {$id}");
        } else {
            $value = $this->db->escape($value);
            $this->db->query("UPDATE info_depotoire SET {$champ} = {$value} WHERE id = {$id}");
        }
    }

    /**
     * POST_ALL_DEPOT_ENTRY – Met à jour tous les champs du dépotoire d'un coup.
     */
    public function postAllDepotEntry($id, $depotoire, $hreDeb, $hreFin, $tonnage, $boite, $isTransBo, $isAvecMat)
    {
        $id        = (int) $id;
        $depotoire = $this->db->escape($depotoire);
        $hreDeb    = $this->db->escape($hreDeb);
        $hreFin    = $this->db->escape($hreFin);
        $tonnage   = $this->db->escape($tonnage);
        $boite     = $this->db->escape($boite);
        $isTransBo = (int) $isTransBo;
        $isAvecMat = (int) $isAvecMat;

        $this->db->query(
            "UPDATE info_depotoire SET
                Depotoire = {$depotoire},
                debut     = {$hreDeb},
                fin       = {$hreFin},
                Tonnage   = {$tonnage},
                boite     = {$boite},
                IsTransbo = {$isTransBo},
                IsAvecMat = {$isAvecMat}
             WHERE id = {$id}"
        );
    }

    /**
     * ADD_INFO_VILLE (action 31) – Ajoute une nouvelle ville+dépotoire à la cédule.
     * Retourne la nouvelle ville (info_villes row) avec son dépotoire.
     */
    public function addInfoVille($idville, $idcedule)
    {
        $idville  = (int) $idville;
        $idcedule = (int) $idcedule;

        $this->db->query(
            "INSERT INTO info_villes (idville, idcedule) VALUES ({$idville}, {$idcedule})"
        );

        $row = $this->db->query(
            "SELECT id AS Id, idville AS IdVille,
                    (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
                    idcedule AS IdCedule, HreDeb, HreFin, nbBac
             FROM info_villes
             WHERE idville = {$idville} AND idcedule = {$idcedule}
             ORDER BY id DESC LIMIT 1"
        )->getRowArray();

        if ($row) {
            $idinfoville = (int) $row['Id'];
            $this->db->query("INSERT INTO info_depotoire (idinfoville) VALUES ({$idinfoville})");

            $row['depot'] = $this->db->query(
                "SELECT id AS Id, Depotoire AS NomDepotoire, debut AS HreDeb, fin AS HreFin,
                        idinfoville AS IdVille, Ticket, Tonnage, IsTransbo, IsAvecMat, boite AS Boite
                 FROM info_depotoire WHERE idinfoville = {$idinfoville} ORDER BY id DESC LIMIT 1"
            )->getRowArray();
        }

        return $row;
    }

    /**
     * Sauvegarde le chemin du ticket (photo) d'un dépotoire.
     */
    public function saveDepotTicket($idDepotoire, $ticketPath)
    {
        $id   = (int) $idDepotoire;
        $path = $this->db->escape($ticketPath);
        $this->db->query("UPDATE info_depotoire SET Ticket = {$path} WHERE id = {$id}");
    }

    /**
     * Retourne les infos complètes d'un dépotoire avec sa ville associée.
     * Utilisé pour la génération du PDF ticket.
     */
    public function getDepotFullInfo($idDepotoire)
    {
        $id = (int) $idDepotoire;
        return $this->db->query(
            "SELECT d.id, d.Depotoire AS NomDepotoire, d.debut AS HreDeb, d.fin AS HreFin,
                    d.Tonnage, d.boite AS Boite, d.Ticket,
                    d.idinfoville,
                    iv.idville, iv.idcedule, iv.HreDeb AS VilleHreDeb, iv.HreFin AS VilleHreFin,
                    (SELECT v.Nom FROM ville v WHERE v.id = iv.idville) AS NomVille,
                    (SELECT c.NOCAMION FROM cedules c WHERE c.ID = iv.idcedule) AS NoCamion,
                    (SELECT c.TypeDeProduit FROM cedules c WHERE c.ID = iv.idcedule) AS TypeDeProduit
             FROM info_depotoire d
             LEFT JOIN info_villes iv ON iv.id = d.idinfoville
             WHERE d.id = {$id}
             LIMIT 1"
        )->getRowArray();
    }

    // ───────────────────────────────────────── SANIVAN RH / EXTRAS

    /**
     * GET_CHAUFFEUR – Même logique que LibSaniRest::getChauffeur().
     * Retourne les infos sanivan_rh du chauffeur (ou INSERT si inexistant).
     */
    public function getSanivanChauffeur($idchauffeur, $dateJour, $idcedule)
    {
        $idchauf = $this->db->escape($idchauffeur);
        $date    = $this->db->escape($dateJour);
        $idced   = $this->db->escape($idcedule);

        $row = $this->db->query(
            "SELECT id, idrh as IdChauffeur, TypeExtra, datejour, HreDeb, HreFin, HreDeb_extra, HreFin_extra,
                    (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh) AS Nom
             FROM sanivan_rh
             WHERE idrh = $idchauf AND datejour = $date AND TypeRh = 0"
        )->getRowArray();
        if ($row) {
            return $row;
        }

        if ($idchauffeur > 0) {
            $this->db->query(
                "INSERT INTO sanivan_rh (idcedule, idrh, TypeRh, datejour) VALUES ($idced, $idchauf, 0, $date)"
            );
            return $this->db->query(
                "SELECT id, idrh as IdChauffeur, TypeExtra, datejour, HreDeb, HreFin, HreDeb_extra, HreFin_extra,
                        (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh) AS Nom
                 FROM sanivan_rh WHERE idcedule = $idced AND idrh = $idchauf AND datejour = $date AND TypeRh = 0"
            )->getRowArray() ?: null;
        }
        return null;
    }

    /**
     * GET_NB_AIDES – Même logique que LibSaniRest::getNbAide().
     * Retourne les HELPERS_1..4 d'une cédule.
     */
    public function getNbAides($idcedule)
    {
        $idcedule = (int) $idcedule;
        $row = $this->db->query(
            "SELECT IFNULL(cedules.HELPERS_1, '') AS HELPERS_1,
                    IFNULL(cedules.HELPERS_2, '') AS HELPERS_2,
                    IFNULL(cedules.HELPERS_3, '') AS HELPERS_3,
                    IFNULL(cedules.HELPERS_4, '') AS HELPERS_4
             FROM cedules WHERE cedules.ID = {$idcedule}"
        )->getRowArray();
        return $row ?: ['HELPERS_1' => '', 'HELPERS_2' => '', 'HELPERS_3' => '', 'HELPERS_4' => ''];
    }

    /**
     * GET_AIDES – Même logique que LibSaniRest::getaide().
     * Retourne les infos d'un aide (ou INSERT si inexistant).
     */
    public function getSanivanAide($idaide, $dateJour, $idcedule)
    {
        $idaide = (int) $idaide;
        $date   = $this->db->escape($dateJour);
        $idced  = $this->db->escape($idcedule);

        $row = $this->db->query(
            "SELECT id, (SELECT CONCAT(Nom, ', ', LEFT(Prenom, 1)) FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh) AS Nom,
                    HreDeb, HreFin
             FROM sanivan_rh WHERE idrh = {$idaide} AND datejour = $date AND TypeRh = 1"
        )->getRowArray();
        if ($row) {
            return $row;
        }

        if ($idaide > 0) {
            $this->db->query(
                "INSERT INTO sanivan_rh (idcedule, idrh, TypeRh, datejour) VALUES ($idced, {$idaide}, 1, $date)"
            );
            return $this->db->query(
                "SELECT id, (SELECT CONCAT(Nom, ', ', LEFT(Prenom, 1)) FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh) AS Nom,
                        HreDeb, HreFin
                 FROM sanivan_rh WHERE idcedule = $idced AND idrh = {$idaide} AND datejour = $date AND TypeRh = 1"
            )->getRowArray() ?: null;
        }
        return null;
    }

    /**
     * GET_EXTRA – Même logique que LibSaniRest::GetTypeExtra().
     */
    public function getTypeExtras()
    {
        $extras = [];
        foreach ($this->db->query("SELECT Nom, id FROM typeextra ORDER BY Nom")->getResult() as $row) {
            $extras[] = $row->Nom;
        }
        return $extras;
    }

    // ───────────────────────────────────────────── POST CAMION/RH

    /**
     * POST_CAMION – Même logique que Co_Rest_Sani action 7.
     */
    public function postCamionInfo($id, $kiloDeb, $kiloFin, $fuel, $hreDeb, $hreFin)
    {
        $id     = $this->db->escape($id);
        $kmDeb  = $this->db->escape($kiloDeb);
        $kmFin  = $this->db->escape($kiloFin);
        $fuel   = $this->db->escape($fuel);
        $hDeb   = $this->db->escape($hreDeb);
        $hFin   = $this->db->escape($hreFin);

        $this->db->query(
            "UPDATE camionjour SET KiloDeb=$kmDeb, KiloFin=$kmFin, Fuel=$fuel, HreDeb=$hDeb, HreFin=$hFin WHERE id=$id"
        );
        return $this->db->error();
    }

    /**
     * POST_CHAUFFEURS – Même logique que Co_Rest_Sani action 6.
     */
    public function postChauffeurInfo($id, $hreDeb, $hreFin, $hreDebExtra, $hreFinExtra, $typeExtra)
    {
        $id     = $this->db->escape($id);
        $hDeb   = $this->db->escape($hreDeb);
        $hFin   = $this->db->escape($hreFin);
        $hDebEx = $this->db->escape($hreDebExtra);
        $hFinEx = $this->db->escape($hreFinExtra);
        $tExtra = $this->db->escape($typeExtra);

        $this->db->query(
            "UPDATE sanivan_rh SET TypeExtra=$tExtra, HreDeb=$hDeb, HreFin=$hFin, HreDeb_extra=$hDebEx, HreFin_extra=$hFinEx WHERE id=$id"
        );
        return $this->db->error();
    }

    /**
     * POST_AIDES – Même logique que Co_Rest_Sani action 24.
     */
    public function postAideInfo($id, $hreDeb, $hreFin)
    {
        $id   = $this->db->escape($id);
        $hDeb = $this->db->escape($hreDeb);
        $hFin = $this->db->escape($hreFin);

        $this->db->query("UPDATE sanivan_rh SET HreDeb=$hDeb, HreFin=$hFin WHERE id=$id");
        return $this->db->error();
    }

    /**
     * LIST_CAMIONS – Même logique que Co_Rest_Sani action 1.
     */
    public function getListCamionsDisponibles()
    {
        $dteActive = $this->db->escape(date('Y-m-d'));
        $cams = [];
        foreach ($this->db->query(
            "SELECT camions.NoCamion FROM camions WHERE typecamion IN (1,7)
             AND camions.NoCamion NOT IN (
                 SELECT DISTINCT NOCAMION FROM cedules
                 WHERE cedules.DateJour = $dteActive
                   AND cedules.NOCAMION IN (SELECT camionjour.NOCAMION FROM camionjour WHERE camionjour.DateJour = $dteActive)
             ) ORDER BY NOCAMION"
        )->getResult() as $row) {
            $cams[] = $row->NoCamion;
        }
        return $cams;
    }

    /**
     * LIST_CHAUFFEUR – Même logique que Co_Rest_Sani action 2.
     */
    public function getListChauffeursDisponibles()
    {
        $chaufs = [];
        foreach ($this->db->query(
            "SELECT CONCAT(Prenom, ' ', Nom) AS NomComplet
             FROM reshumaine
             WHERE isdeleted = 0 AND idreshum IN (1,3,4,7)
               AND CONCAT(Prenom, ' ', Nom) IS NOT NULL
             ORDER BY Nom, Prenom"
        )->getResult() as $row) {
            $chaufs[] = $row->NomComplet;
        }
        return $chaufs;
    }

    /**
     * ADD_ITEM_CEDULE – Change le camion d'une cédule (action 38).
     */
    public function changeCamionCedule($idcedule, $camion, $oldcamion)
    {
        $camion    = $this->db->escape($camion);
        $oldcamion = $this->db->escape($oldcamion);
        $idcedule  = $this->db->escape($idcedule);
        $this->db->query("UPDATE cedules SET NOCAMION=$camion, remplace_cam=$oldcamion WHERE ID=$idcedule");
    }

    /**
     * CHG_CHAUFFEUR_CEDULES – Change le chauffeur d'une cédule (action 40).
     */
    public function changeChauffeurCedule($idcedule, $chauffeurNom, $oldchauffeur)
    {
        $chauffeurEsc = $this->db->escape($chauffeurNom);
        $oldchauffeur = $this->db->escape($oldchauffeur);
        $idcedule     = $this->db->escape($idcedule);

        $res = $this->db->query(
            "SELECT reshumaine.id FROM reshumaine WHERE UPPER(CONCAT(Prenom, ' ', Nom)) = $chauffeurEsc"
        )->getRow();
        if (! $res) {
            return null;
        }

        $idchauffeur = (int) $res->id;
        $this->db->query(
            "UPDATE cedules SET idchauffeurs={$idchauffeur}, idremplace_chauf=$oldchauffeur WHERE ID=$idcedule"
        );
        return $idchauffeur;
    }

    // ───────────────────────────────────────── POLYLINES / TAGS

    /**
     * Retourne les polylines liées à une cédule.
     * NB: cp.idpolyline est aliasé en `id` car le contrôleur lit
     * $poly['id'] pour les boutons de parcours (api_polyline?id=...).
     */
    public function getPolylinesForCedule($idcedule)
    {
        $idcedule = (int) $idcedule;
        return $this->db->query(
            "SELECT cp.idpolyline AS id, pv.parcours, cp.ordre, cp.idpolyline
             FROM cedule_polylines cp
             LEFT JOIN polilyne_ville pv ON pv.id = cp.idpolyline
             WHERE cp.idcedule = {$idcedule}
             ORDER BY cp.ordre"
        )->getResultArray();
    }

    public function getPolylineById($idpolyline)
    {
        $idpolyline = (int) $idpolyline;
        return $this->db->query(
            "SELECT id, parcours FROM polilyne_ville WHERE id = {$idpolyline} LIMIT 1"
        )->getRowArray() ?: null;
    }

    /**
     * Retourne tous les tags ouverts.
     */
    public function getOpenTags()
    {
        return $this->db->query(
            "SELECT id, id_ville, tag_code, address_text, latitude, longitude, reason,
                    description, image_path, status, response_text, responded_at, created_at, updated_at
             FROM map_tags
             WHERE status <> 'DONE'
             ORDER BY created_at DESC"
        )->getResultArray();
    }

    /**
     * Retourne les camions sur la route pour une date.
     */
    public function getCamionsOnRoad($dateactive)
    {
        $dateStart = $this->db->escape($dateactive . ' 00:00:00');
        $dateEnd   = $this->db->escape($dateactive . ' 23:59:59');
        return $this->db->query(
            "SELECT gps.nocamion, gps.track_lat, gps.track_lng, gps.track_time, gps.adresse, gps.IsInZone
             FROM gps_sani gps
             INNER JOIN (
                 SELECT nocamion, MAX(id) as maxid
                 FROM gps_sani
                 WHERE track_time BETWEEN $dateStart AND $dateEnd
                 GROUP BY nocamion
             ) latest ON gps.id = latest.maxid
             ORDER BY gps.nocamion"
        )->getResultArray();
    }

    // ─────────────────────────────────────────────── GPS SANI

    /**
     * Enregistre une position GPS dans gps_sani.
     * Reproduit la persistance de LocationSenderService.cs (PushLocationAsync).
     */
    public function saveGpsPosition($data)
    {
        $this->db->table('gps_sani')->insert([
            'nocamion'    => $data['nocamion']    ?? '',
            'idchauffeur' => $data['idchauffeur'] ?? '',
            'track_lat'   => $data['track_lat']  ?? '',
            'track_lng'   => $data['track_lng']   ?? '',
            'track_time'  => $data['track_time']  ?? date('Y-m-d H:i:s'),
            'adresse'     => $data['adresse']    ?? '',
            'IsInZone'    => $data['IsInZone']    ?? '0',
        ]);
        return $this->db->insertID();
    }

    /**
     * Dernière position GPS d'un camion dans gps_sani.
     */
    public function getLastGpsPosition($nocamion)
    {
        $cam        = $this->db->escape($nocamion);
        $todayStart = $this->db->escape(date('Y-m-d') . ' 00:00:00');
        $todayEnd   = $this->db->escape(date('Y-m-d') . ' 23:59:59');

        $row = $this->db->query(
            "SELECT id, track_lat, track_lng, track_time, adresse
             FROM gps_sani
             WHERE nocamion = $cam AND track_time BETWEEN $todayStart AND $todayEnd
             ORDER BY track_time DESC
             LIMIT 1"
        )->getRowArray();
        if ($row) {
            return $row;
        }
        return $this->db->query(
            "SELECT id, track_lat, track_lng, track_time, adresse
             FROM gps_sani
             WHERE nocamion = $cam
             ORDER BY track_time DESC
             LIMIT 1"
        )->getRowArray() ?: null;
    }

    /**
     * Retourne les timestamps (epoch) de tous les enregistrements GPS
     * d'aujourd'hui pour un camion donné (déduplication batch).
     */
    public function getGpsTimesToday($nocamion)
    {
        $todayStart = date('Y-m-d') . ' 00:00:00';
        $todayEnd   = date('Y-m-d') . ' 23:59:59';
        $result = $this->db->query(
            "SELECT track_time FROM gps_sani WHERE nocamion = ? AND track_time BETWEEN ? AND ? ORDER BY track_time",
            [$nocamion, $todayStart, $todayEnd]
        );
        $times = [];
        foreach ($result->getResultArray() as $row) {
            $times[] = strtotime($row['track_time']);
        }
        return $times;
    }

    /**
     * Retourne TOUS les points GPS d'aujourd'hui pour un camion.
     * Utilisé par api_inzone_history pour restaurer le parcours.
     */
    public function getAllGpsToday($nocamion)
    {
        $todayStart = date('Y-m-d') . ' 00:00:00';
        $todayEnd   = date('Y-m-d') . ' 23:59:59';
        return $this->db->query(
            "SELECT track_lat, track_lng, track_time, adresse, IsInZone
             FROM gps_sani
             WHERE nocamion = ? AND track_time BETWEEN ? AND ?
             ORDER BY track_time",
            [$nocamion, $todayStart, $todayEnd]
        )->getResultArray();
    }

    // ──────────────────────────────────────────── HEARTBEAT

    /**
     * Enregistre / met à jour le heartbeat d'un chauffeur web actif.
     * UPSERT : INSERT ... ON DUPLICATE KEY UPDATE.
     */
    public function registerWebHeartbeat($nocamion, $idchauffeur, $vehicleId)
    {
        $cam = $this->db->escape($nocamion);
        $chf = (int) $idchauffeur;
        $vid = $this->db->escape($vehicleId);
        $now = $this->db->escape(date('Y-m-d H:i:s'));

        $this->db->query(
            "INSERT INTO web_chauffeur_active (nocamion, idchauffeur, vehicle_id, last_seen)
             VALUES ($cam, $chf, $vid, $now)
             ON DUPLICATE KEY UPDATE idchauffeur = $chf, vehicle_id = $vid, last_seen = $now"
        );
    }

    /**
     * Supprime le heartbeat d'un camion (logout).
     */
    public function removeWebHeartbeat($nocamion)
    {
        $cam = $this->db->escape($nocamion);
        $this->db->query("DELETE FROM web_chauffeur_active WHERE nocamion = $cam");
    }

    /**
     * Retourne les camions web actifs (heartbeat récent).
     */
    public function getActiveWebTrucks($minutesAgo = 5)
    {
        $min = (int) $minutesAgo;
        return $this->db->query(
            "SELECT nocamion, idchauffeur, vehicle_id
             FROM web_chauffeur_active
             WHERE last_seen >= DATE_SUB(NOW(), INTERVAL $min MINUTE)
             ORDER BY nocamion"
        )->getResultArray();
    }

    /**
     * Nettoyage des heartbeats expirés (> 30 minutes).
     */
    public function cleanExpiredHeartbeats()
    {
        $this->db->query(
            "DELETE FROM web_chauffeur_active WHERE last_seen < DATE_SUB(NOW(), INTERVAL 30 MINUTE)"
        );
    }
}
