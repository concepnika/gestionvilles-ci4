<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Port CI4 de application/libraries/LibSaniRest.php (CI3).
 *
 * Ne contient que les méthodes réellement appelées par le dispatcher
 * Co_Rest_Sani::index_get(). Toutes les méthodes échappent leurs
 * paramètres en interne — passer les valeurs brutes.
 */
class SaniRestService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Retourne la première ligne de la cédule (GET_CEDULES).
     * $idcedule : valeur brute (non échappée).
     */
    public function getInfoCedule($idcedule): array
    {
        $id = $this->db->escape($idcedule);
        $strSQL = "SELECT cedules.ID as Id, cedules.TypeDeProduit,cedules.NOCAMION as NoCamion, DateJour, idsecteur, idpolyline,
                  remplace_cam, idremplace_chauf,  HELPERS_1, HELPERS_2, HELPERS_3, HELPERS_4,
			            cedules.idchauffeurs as IdChauffeur, cedules.idvilles as IdVilles,
			            (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
			            (SELECT typededechet.couleur FROM typededechet WHERE typededechet.Nom = cedules.TypeDeProduit) AS BackCouleurDechet,
			            (SELECT typededechet.textcolor FROM typededechet WHERE typededechet.Nom = cedules.TypeDeProduit) AS TextColorDechet
	                  FROM cedules
			            WHERE cedules.ID = $id  and
                      (SELECT typecamion FROM camions WHERE NoCamion = cedules.NOCAMION) != 4
                     ORDER BY (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp) ";
        $result = $this->db->query($strSQL);
        foreach ($result->getResultArray() as $row) {
            return $row;
        }
        return [];
    }

    /**
     * Liste d'IDs de cédules du jour, séparés par '_' (GET_NB_CEDULES).
     * $date : date brute Y-m-d.
     */
    public function getNbCedule($date, $idchauffeur): string
    {
        $dte = $this->db->escape($date);
        $Chauffeur = ((int) $idchauffeur === 0)
            ? ''
            : ' AND cedules.idchauffeurs = ' . $this->db->escape($idchauffeur) . ' ';
        $strSQL = "SELECT cedules.ID
	                  FROM cedules
			            WHERE cedules.DateJour = $dte $Chauffeur  and
                      (SELECT typecamion FROM camions WHERE NoCamion = cedules.NOCAMION) != 4
                     ORDER BY (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp) ";
        $returnval = '';
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            $returnval .= $row->ID . '_';
        }
        return substr($returnval, 0, -1);
    }

    /**
     * HELPERS_1..4 d'une cédule (GET_NB_AIDES).
     */
    public function getNbAide($idcedule): array
    {
        $id = $this->db->escape($idcedule);
        $strSQL = "SELECT IFNULL(cedules.HELPERS_1, '') AS HELPERS_1,IFNULL(cedules.HELPERS_2, '') AS HELPERS_2,IFNULL(cedules.HELPERS_3, '') AS HELPERS_3,IFNULL(cedules.HELPERS_4, '') AS HELPERS_4
	                  FROM cedules
			            WHERE cedules.ID = $id
                     ORDER BY (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp) ";
        foreach ($this->db->query($strSQL)->getResultArray() as $row) {
            return $row;
        }
        return [];
    }

    /**
     * Ligne camionjour du camion/date — INSERT si absente (GET_CAMION).
     */
    public function getInfoCamion($nocamion, $idchauffeur, $id, $DateJour, $KiloDeb)
    {
        $camionesc = $this->db->escape($nocamion);
        $idchauffeurEsc = $this->db->escape($idchauffeur);
        $DateJourEsc = $this->db->escape($DateJour);
        $KiloDebEsc = $this->db->escape($KiloDeb);
        $strSQL = "Select id,camionjour.DateJour, camionjour.NOCAMION, camionjour.KiloDeb, camionjour.KiloFin, camionjour.Fuel,camionjour.HreDeb, camionjour.HreFin
                      from camionjour where camionjour.NOCAMION = $camionesc AND DateJour = $DateJourEsc";
        $rest = $this->db->query($strSQL);
        if ($rest->getNumRows() > 0) {
            return $rest->getRow(0);
        }

        $strSQL = "INSERT INTO camionjour
	                  (idchauffeur, NOCAMION,  KiloDeb, DateJour)
	                  VALUES ($idchauffeurEsc, $camionesc, $KiloDebEsc, $DateJourEsc)";
        $this->db->query($strSQL);
        $resId = $this->db->query('SELECT LAST_INSERT_ID() as id')->getRow(0);
        $rest = $this->db->query("Select id,camionjour.DateJour, camionjour.NOCAMION, camionjour.KiloDeb, camionjour.KiloFin, camionjour.Fuel,camionjour.HreDeb, camionjour.HreFin
                        from camionjour where id = " . $resId->id);
        foreach ($rest->getResultArray() as $row) {
            return $row;
        }
        return [];
    }

    /**
     * Ligne sanivan_rh du chauffeur (TypeRh=0) — INSERT si absente (GET_CHAUFFEUR).
     */
    public function getChauffeur($idchauffeur, $id, $DateJour, $idcedule)
    {
        $idchauffeurEsc = $this->db->escape($idchauffeur);
        $idceduleEsc = $this->db->escape($idcedule);
        $dateEsc = $this->db->escape($DateJour);
        $strSQL = "SELECT id, idrh as IdChauffeur, TypeExtra, datejour, HreDeb, HreFin, HreDeb_extra, HreFin_extra,
                (SELECT CONCAT( Prenom , ' ', Nom)  FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh)  AS Nom
                    FROM sanivan_rh WHERE  idrh = $idchauffeurEsc
                      AND datejour = $dateEsc AND TypeRh = 0";
        $result = $this->db->query($strSQL);
        if ($result->getNumRows() > 0) {
            return $result->getRow(0);
        }
        if ($idchauffeur > 0) {
            $strSQL = "INSERT INTO sanivan_rh
	            (idcedule, idrh, TypeRh, datejour)
	            VALUES ($idceduleEsc, $idchauffeurEsc, 0, $dateEsc)";
            $this->db->query($strSQL);
            $strSQL = "SELECT id, idrh as IdChauffeur, TypeExtra, datejour, HreDeb, HreFin, HreDeb_extra, HreFin_extra
                    FROM sanivan_rh WHERE idcedule = $idceduleEsc AND idrh = $idchauffeurEsc
                      AND datejour = $dateEsc AND TypeRh = 0";
            return $this->db->query($strSQL)->getRow(0);
        }
        return [
            "0" => [
                "idsanivanrh" => "0",
                "idcedule" => "0",
                "idrh" => "0",
                "TypeRh" => "0",
                "datejour" => "",
                "HreDeb" => "",
                "HreFin" => "",
                "HreDeb_extra" => "",
                "HreFin_extra" => "",
                "TypeExtra" => ""
            ]
        ];
    }

    /**
     * Ligne sanivan_rh de l'aide (TypeRh=1) — INSERT si absente (GET_AIDES).
     */
    public function getaide($idaide, $id, $dtejour, $idcedule): array
    {
        $idaideEsc = $this->db->escape($idaide);
        $idceduleEsc = $this->db->escape($idcedule);
        $strSQL = "SELECT  id, (select Concat(Nom,', ', LEFT(Prenom, 1)) from reshumaine where reshumaine.id = sanivan_rh.idrh) AS Nom, HreDeb, HreFin
                    FROM sanivan_rh WHERE  idrh = $idaideEsc
                      AND datejour = $dtejour AND TypeRh = 1";
        $result = $this->db->query($strSQL);
        if ($result->getNumRows() > 0) {
            foreach ($result->getResultArray() as $row) {
                return $row;
            }
        } elseif ($idaide > 0) {
            $strSQL = "INSERT INTO sanivan_rh
	            (idcedule, idrh, TypeRh, datejour)
	            VALUES ($idceduleEsc, $idaideEsc, 1, $dtejour)";
            $this->db->query($strSQL);
            $strSQL = "SELECT id, (select Concat(Nom,', ', LEFT(Prenom, 1)) from reshumaine where reshumaine.id = sanivan_rh.idrh) AS Nom, HreDeb, HreFin
                    FROM sanivan_rh WHERE idcedule = $idceduleEsc AND idrh = $idaideEsc
                      AND datejour = $dtejour AND TypeRh = 1";
            foreach ($this->db->query($strSQL)->getResultArray() as $row) {
                return $row;
            }
        }
        return [];
    }

    /**
     * IDs des info_villes de la cédule — crée info_villes + info_depotoire
     * depuis cedules.idvilles si absents (GET_NB_VILLES).
     */
    public function getNbVilles($idcedule): array
    {
        $id = $this->db->escape($idcedule);
        $returnval = [];
        $result = $this->db->query("SELECT id FROM info_villes WHERE idcedule = $id ORDER BY id");
        if ($result->getNumRows() > 0) {
            foreach ($result->getResultArray() as $row) {
                $returnval[] = $row['id'];
            }
            return $returnval;
        }

        $result = $this->db->query("SELECT idvilles FROM cedules WHERE ID = $id");
        foreach ($result->getResult() as $row) {
            $idville = $this->db->escape($row->idvilles);

            $this->db->query("INSERT INTO info_villes
	                (idville, idcedule)
	                VALUES ($idville, $id)");

            $sub = $this->db->query("SELECT id FROM info_villes WHERE idcedule = $id ORDER BY id");
            foreach ($sub->getResultArray() as $r) {
                $returnval[] = $r['id'];
            }

            $detail = $this->db->query("SELECT id as Id, idville as IdVille,
                    (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
                    idcedule as IdCedule, HreDeb, HreFin, nbBac
                 FROM info_villes WHERE idville = $idville AND idcedule = $id");
            foreach ($detail->getResultArray() as $r) {
                $idinfoville = $this->db->escape($r['Id']);
                $this->db->query("INSERT INTO info_depotoire
	                (idinfoville)
	                VALUES ($idinfoville)");
            }
        }
        return $returnval;
    }

    /**
     * Ligne info_villes — crée info_villes + info_depotoire si absente (GET_VILLE).
     */
    public function getVille($idville, $id, $date, $idcedule): array
    {
        $idvilleEsc = $this->db->escape($idville);
        $idceduleEsc = $this->db->escape($idcedule);
        $strSQL = "SELECT id as Id, idville as IdVille,
                  (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
                  idcedule as IdCedule, HreDeb, HreFin, nbBac
               FROM info_villes WHERE idville = $idvilleEsc AND idcedule = $idceduleEsc";
        $result = $this->db->query($strSQL);
        if ($result->getNumRows() > 0) {
            foreach ($result->getResultArray() as $row) {
                return $row;
            }
        }

        $this->db->query("INSERT INTO info_villes
	                (idville, idcedule)
	                VALUES ($idvilleEsc, $idceduleEsc)");
        $result = $this->db->query($strSQL);
        foreach ($result->getResultArray() as $row) {
            $idinfoville = $this->db->escape($row['Id']);
            $this->db->query("INSERT INTO info_depotoire
	                (idinfoville)
	                VALUES ($idinfoville)");
            return $row;
        }
        return [];
    }

    /**
     * Lignes info_depotoire d'une info_ville (GET_DEPOTOIRE).
     */
    public function getDepotoire($idinfoville): array
    {
        $id = $this->db->escape($idinfoville);
        $strSQL = "SELECT id as Id, Depotoire as NomDepotoire, debut as HreDeb, fin as HreFin, idinfoville as IdVille, Ticket, Tonnage, IsTransbo, IsAvecMat, boite as Boite
                FROM info_depotoire WHERE idinfoville = $id";
        return $this->db->query($strSQL)->getResultArray();
    }

    /**
     * Noms des dépotoires sanivan, séparés par '_' (LIST_DEPOTOIRES).
     */
    public function getListDepotoire(): string
    {
        $returnval = '';
        $result = $this->db->query("SELECT nom FROM depotoire WHERE issanivan = 1 ORDER BY nom");
        foreach ($result->getResult() as $row) {
            $returnval .= $row->nom . "_";
        }
        return substr($returnval, 0, -1);
    }

    /**
     * IDs des info_depotoire d'une info_ville (GET_NB_DEPOTS).
     */
    public function getNbDepot($idville): array
    {
        $id = $this->db->escape($idville);
        $returnval = [];
        $result = $this->db->query("SELECT id FROM info_depotoire WHERE idinfoville = $id ORDER BY id");
        foreach ($result->getResultArray() as $row) {
            $returnval[] = $row['id'];
        }
        return $returnval;
    }

    /**
     * Noms des types extra, séparés par '_' (GET_EXTRA).
     */
    public function getTypeExtra(): string
    {
        $returnval = '';
        $result = $this->db->query("Select Nom, id from typeextra order BY Nom");
        foreach ($result->getResult() as $row) {
            $returnval .= $row->Nom . "_";
        }
        return substr($returnval, 0, -1);
    }

    /**
     * Segments GPS du camion pour la journée (format legacy
     * "lat,lng, timespan: ..., chauffeur: ..., adresse: ..._").
     * Même requête que Co_tablette_sani::getPolylineDynamique.
     */
    public function getPolylineDynamique($idchauffeur, $dateactive, $nocamion): array
    {
        $dateStart = $this->db->escape($dateactive . ' 00:00:00');
        $dateEnd   = $this->db->escape($dateactive . ' 23:59:59');
        $camEsc = $this->db->escape($nocamion);

        $chFilter = ((int) $idchauffeur > 0)
            ? ' AND g.idchauffeur = ' . $this->db->escape($idchauffeur)
            : '';

        $selectGps = "SELECT g.id, g.idchauffeur, g.track_lng, g.track_lat, g.track_time, g.adresse,
                        CONCAT(UPPER(TRIM(r.NOM)), ' ', UPPER(TRIM(r.PRENOM))) AS chauffeur
                   FROM gps_sani g
                   LEFT JOIN reshumaine r ON r.id = g.idchauffeur
                  WHERE g.track_time BETWEEN $dateStart AND $dateEnd
                    AND g.nocamion = $camEsc
                    AND g.IsInZone = 1";

        $q = $this->db->query($selectGps . $chFilter . ' ORDER BY g.track_time');
        if ($q->getNumRows() === 0 && $chFilter !== '') {
            $q = $this->db->query($selectGps . ' ORDER BY g.track_time');
        }
        if ($q->getNumRows() < 2) {
            return [];
        }

        $buf = '';
        foreach ($q->getResult() as $row) {
            $buf .= str_replace(',', '.', $row->track_lat) . ',' . str_replace(',', '.', $row->track_lng)
                . ', timespan: ' . $row->track_time . ', chauffeur: ' . $row->chauffeur . ', adresse: ' . $row->adresse . '_';
        }

        return $buf !== '' ? [substr($buf, 0, -1)] : [];
    }
}
