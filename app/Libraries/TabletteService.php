<?php

namespace App\Libraries;

use Config\Database;

/**
 * Port des méthodes de Sanivan_model / login_model / Tablet_tags_model /
 * Cedules_model utilisées par Co_tablette_sani (CI3).
 *
 * Les méthodes post*Day lisent les paramètres du POST comme en CI3.
 */
class TabletteService
{
    private const KEYSHA = 'nikajhdgsjgdsdssd'; // login_model::$keysha

    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function escape($val)
    {
        return $this->db->escape($val);
    }

    // ------------------------------------------------------ login_model

    public function validateUser(string $email, string $password)
    {
        $pwdPeppered = hash_hmac('sha256', $password, self::KEYSHA);
        $result = $this->db->query(
            'SELECT * FROM webuser WHERE email = ' . $this->db->escape($email)
        )->getRow();
        if ($result !== null && password_verify($pwdPeppered, $result->pass)) {
            return $result;
        }
        return false;
    }

    public function validateChauffeur(string $login, string $password)
    {
        $pwdPeppered = hash_hmac('sha256', $password, self::KEYSHA);
        $result = $this->db->query(
            'SELECT * FROM reshumaine WHERE login = ' . $this->db->escape($login)
        )->getRow();
        if ($result !== null && password_verify($pwdPeppered, $result->pass)) {
            return $result;
        }
        return false;
    }

    // ------------------------------------------------------ Cedules_model

    public function getAllDepotoires($depotoir): string
    {
        $returnval = '';
        $rows = $this->db->query('SELECT nom FROM depotoire ORDER BY nom')->getResult();
        foreach ($rows as $row) {
            $selected = ($depotoir == $row->nom) ? ' selected ' : '';
            $returnval .= '<option value="' . $row->nom . '" ' . $selected . ' >' . $row->nom . '</option>';
        }
        return $returnval;
    }

    // ------------------------------------------------------ Sanivan_model

    public function getMaxKiloCamions($camion, array $post)
    {
        $returnval = '';
        $cam = $this->db->escape($camion);
        $query = "SELECT IFNULL( IF(( MAX(KiloFin) is null ) OR (MAX(KiloFin) < MAX(KiloDeb)),
                    MAX(KiloDeb), MAX(KiloFin)), 0) AS KiloDeb, 0 AS KiloFin, 0 AS Fuel
                  FROM camionjour WHERE NOCAMION = $cam";
        foreach ($this->db->query($query)->getResult() as $row) {
            $returnval = $row;
            $KiloDeb = $row->KiloDeb;
            // Appel legacy (args ignorés en CI3, les valeurs viennent du POST)
            $this->postCamionJour($post);
        }
        return $returnval;
    }

    public function getSuivisChauffeur($idchauffeur, $camion)
    {
        $returnval = '';
        $idrh = $this->db->escape($idchauffeur);
        $query = "SELECT StartTime, EndTime, NOCAMION FROM suivis WHERE
                  DateJour = DATE_FORMAT(CURDATE(), '%Y-%m-%d') AND idrh = $idrh";
        $result = $this->db->query($query)->getResult();
        foreach ($result as $row) {
            $returnval = json_encode($row);
        }
        if ($returnval === '') {
            $query = "SELECT TIME_FORMAT(CURTIME(), '%H:%i') AS StartTime, '00:00' AS EndTime, '' AS NOCAMION, 'OK' AS status";
            foreach ($this->db->query($query)->getResult() as $row) {
                $returnval = json_encode($row);
            }
        }
        return $returnval;
    }

    public function postCamionJour(array $post): string
    {
        $idcam = $this->db->escape($post['idcam'] ?? '');
        $KiloDeb = $this->db->escape(str_replace(',', '', (string) ($post['KiloDeb'] ?? '')));
        $KiloFin = $this->db->escape(str_replace(',', '', (string) ($post['KiloFin'] ?? '')));
        $fuel = $this->db->escape($post['Fuel'] ?? '');
        $HreDeb = $this->db->escape($post['HreDeb'] ?? '');
        $HreFin = $this->db->escape($post['HreFin'] ?? '');
        $this->db->query("UPDATE camionjour
            SET KiloDeb=$KiloDeb, KiloFin=$KiloFin, Fuel=$fuel, HreDeb=$HreDeb, HreFin=$HreFin
            WHERE id = $idcam");
        return 'OK';
    }

    public function postRHDay(array $post): string
    {
        $idrh = $this->db->escape($post['idrh'] ?? '');
        $TypeExtra = $this->db->escape($post['TypeExtra'] ?? '');
        $HreDeb = $this->db->escape($post['HreDeb'] ?? '');
        $HreFin = $this->db->escape($post['HreFin'] ?? '');
        $HreDeb_extra = $this->db->escape($post['HreDeb_extra'] ?? '');
        $HreFin_extra = $this->db->escape($post['HreFin_extra'] ?? '');
        $this->db->query("UPDATE sanivan_rh
            SET HreDeb=$HreDeb, HreFin=$HreFin, HreDeb_extra=$HreDeb_extra,
                HreFin_extra=$HreFin_extra, TypeExtra=$TypeExtra
            WHERE id = $idrh");
        return 'OK';
    }

    public function postRHDayBroker(array $post): string
    {
        $idrh = $this->db->escape($post['idrh'] ?? '');
        $montant = $this->db->escape($post['montant'] ?? '');
        $this->db->query("UPDATE sanivan_rh SET montant_forfaitaire=$montant WHERE id = $idrh");
        return 'OK';
    }

    public function postDepDay(array $post): string
    {
        $iddeb = $this->db->escape($post['iddeb'] ?? '');
        $isTransbo = $this->db->escape($post['istransbo'] ?? '');
        $Depotoire = $this->db->escape($post['Depotoire'] ?? '');
        $Tonnage = $this->db->escape(str_replace(',', '', (string) ($post['Tonnage'] ?? '')));
        $boite = $this->db->escape($post['boite'] ?? '');
        $debut = $this->db->escape($post['debut'] ?? '');
        $fin = $this->db->escape($post['fin'] ?? '');
        $this->db->query("UPDATE info_depotoire
            SET IsTransBo=$isTransbo, Depotoire=$Depotoire, Tonnage=$Tonnage,
                debut=$debut, fin=$fin, boite=$boite
            WHERE id = $iddeb");
        return 'OK';
    }

    public function postVilleDay(array $post): string
    {
        $id = $this->db->escape($post['id'] ?? '');
        $nbBac = $this->db->escape($post['nbBac'] ?? '');
        $HreDeb = $this->db->escape($post['HreDeb'] ?? '');
        $HreFin = $this->db->escape($post['HreFin'] ?? '');
        $this->db->query("UPDATE info_villes
            SET HreDeb=$HreDeb, HreFin=$HreFin, nbBac=$nbBac
            WHERE id = $id");
        return 'OK';
    }

    public function deleteSanivanDay(array $post): string
    {
        $nocamion = $this->db->escape($post['nocamion'] ?? '');
        $date = $this->db->escape($post['date'] ?? '');
        $this->db->query("DELETE FROM camionjour WHERE NOCAMION = $nocamion AND DateJour = $date");
        $this->db->query("DELETE FROM sanivanday WHERE NOCAMION = $nocamion AND DateJour = $date");
        $this->db->query("DELETE FROM cedules WHERE NOCAMION = $nocamion AND DateJour = $date");
        return 'OK';
    }

    // ------------------------------------------------------ Tablet_tags_model

    public function listOpenTagsForCity(int $idVille): array
    {
        return $this->db->query(
            "SELECT id, id_ville, id_chauffeur, title, tag_code, address_text, latitude, longitude, reason,
                    description, status, image_path, created_at, updated_at
               FROM map_tags
              WHERE id_ville = ? AND status <> 'DONE'
           ORDER BY created_at DESC",
            [$idVille]
        )->getResultArray();
    }

    public function listOpenTagsForChauffeur(int $idChauffeur, int $idVille): array
    {
        return $this->listOpenTagsForCity($idVille);
    }
}
