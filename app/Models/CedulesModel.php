<?php

namespace App\Models;

/**
 * Port partiel de Cedules_model (CI3) — seules les methodes
 * utilisees par les pages deja portees sont reproduites.
 * Les requetes SQL sont copiees a l'identique.
 */
class CedulesModel extends Ci3Model
{
    /**
     * Options HTML des employes (chauffeurs/aides) pour un type RH.
     * CI3 : Cedules_model::GetAllEmployes
     */
    public function GetAllEmployes($typerh, $idrh)
    {
        if ($typerh == 0) {
            $typerh = '1,2,3';
        }
        $returnval = '';
        $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
            from reshumaine where idreshum in ($typerh) and isdeleted = 0 order BY Nom";
        $reshumaine = $this->db->query($strSQL);
        foreach ($reshumaine->getResult() as $row) {
            $select = '';
            if ($idrh == $row->id) {
                $select = (($idrh == $row->id) ? ' selected ' : '');
            }
            $returnval .= '<option ' . $select . ' value="' . $row->id . '">' . $row->NomComplet . '</option>';
        }
        $this->data['employes'] = $returnval;
        return $returnval;
    }

    /**
     * Options HTML des types d'absence.
     * CI3 : Cedules_model::GetTypeAbsences
     */
    public function GetTypeAbsences($typeabsence = 0)
    {
        $returnval = '';
        if ($typeabsence == '') {
            $returnval .= '<option value="" selected >Tous les types</option>';
        } else {
            $returnval .= '<option value="" >Tous les types</option>';
        }
        $strSQL = "SELECT id, Nom FROM typeabsence order BY Nom";
        $reshumaine = $this->db->query($strSQL);
        foreach ($reshumaine->getResult() as $row) {
            $selected = (($typeabsence == $row->id) ? ' selected ' : '');
            $returnval .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->Nom . '</option>';
        }
        $this->data['typeabsences'] = $returnval;
        return $returnval;
    }

    /**
     * Options HTML des types de RH (se termine par </select> en CI3).
     * CI3 : Cedules_model::GetTypeRH
     */
    public function GetTypeRH($idrh = '')
    {
        $returnval = '';
        $strSQL = "Select Nom, id from typerh order BY Nom ";
        $camions = $this->db->query($strSQL);
        foreach ($camions->getResult() as $row) {
            $selected = (($idrh == $row->id) ? ' selected ' : '');
            $returnval .= '<option value="' . $row->id . '" ' . $selected . ' >' . $row->Nom . '</option>';
        }
        return $returnval . '</select>';
    }

    /**
     * Options HTML des types de dechets.
     * CI3 : Cedules_model::GetAllTypeProduits
     */
    public function GetAllTypeProduits()
    {
        $returnval = '';
        $strSQL = "SELECT Nom FROM typededechet order by Nom";
        $typededechet = $this->db->query($strSQL);
        foreach ($typededechet->getResult() as $row) {
            $returnval .= '<option value="' . $row->Nom . '">' . $row->Nom . '</option>';
        }
        $this->data['typededechet'] = $returnval;
        return $returnval;
    }

    /**
     * Options HTML de tous les camions (optionnellement par type).
     * CI3 : Cedules_model::GetAllCamions
     */
    public function GetAllCamions($typecam = '')
    {
        if (is_numeric($typecam)) {
            $typecam = " AND TypeCamion = $typecam ";
        }
        $returnval = '';
        $strSQL = "Select NoCamion from camions WHERE isdeleted = 0 $typecam order BY NoCamion ";
        $camions = $this->db->query($strSQL);
        foreach ($camions->getResult() as $row) {
            $returnval .= '<option value="' . $row->NoCamion . '">' . $row->NoCamion . '</option>';
        }
        $this->data['camions'] = $returnval;
        return $returnval;
    }

    /**
     * Options HTML des types de camion (id <> 4), se termine par
     * </select> en CI3.
     * CI3 : Cedules_model::GetTypeCamion
     */
    public function GetTypeCamion($typecam)
    {
        $returnval = '';
        $strSQL = "Select Nom, id from typecamion where id <> 4 order BY Nom ";
        $camions = $this->db->query($strSQL);
        foreach ($camions->getResult() as $row) {
            $selected = (($typecam == $row->id) ? ' selected ' : '');
            $returnval .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->Nom . '</option>';
        }
        return $returnval . '</select>';
    }
}
