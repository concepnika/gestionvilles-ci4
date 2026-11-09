<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SanivanHtmlBuilder — Génération HTML pour le rapport Sanivan.
 *
 * Chaque méthode produit un fragment HTML identique à ce que produisait
 * l'ancien Sanivan_model, afin de garantir la compatibilité avec
 * le JavaScript de la vue (IDs d'éléments, etc.).
 *
 * Aucune logique métier, aucun accès BD.
 */
class SanivanHtmlBuilder
{
    // ===================================================================
    //  LIGNE CAMION (en-tête du rapport par camion)
    // ===================================================================

    /**
     * Construit la ligne en-tête d'un camion normal (non-broker).
     *
     * @param object $row       Ligne sanicam (Chauffeur, NOCAMION, KiloDeb, KiloFin, Fuel, etc.)
     * @param object $rowcam    Ligne camionjour (id)
     * @param int    $trIter    Compteur d'itération pour les IDs
     * @param float  $transport Heures de transport calculées
     * @param float  $collecte  Heures de collecte calculées
     * @param float  $tonnage   Tonnage total
     * @return string HTML
     */
    public function buildTruckRow($row, $rowcam, $trIter, $transport, $collecte, $tonnage)
    {
        $errclass = ' btn-danger ';
        $test = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;'
            onclick='DayCamChauf($rowcam->id)' title='Enregistrer'><i class='fa fa-check btn btn-success' style='font-size:16px;'></i></a>";

        $html = '<tr id="trcam_' . $row->idcam . '">
            <td class="supprime"><button type="button"
                class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es"
                onclick="ShowHideRap(\'tr_' . $trIter . '\',' . $trIter . ')" style="outline:none;">
                <span id="spShowHide_' . $trIter . '" class="glyphicon glyphicon-chevron-down"
                    style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
            <td align="left"><label id="lb_chauf_' . $trIter . '" style="width:190px">' . $row->Chauffeur . '</label></td>
            <td align="center"><label id="lb_cam_' . $trIter . '" style="width:70px;color:darkred;">' . $row->NOCAMION . '</label></td>';

        // Colonnes de données
        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:130px;">
            <input class="form-control btn-info" type="text" id="KiloDeb_' . $rowcam->id . '"
                style="width:120px;text-align: right;font-size:14px" value="' . number_format($row->KiloDeb, 2) . '" />
            <span class="input-group-btn"><label class="form-control" style="height:30px;">KM</label></span></div></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:130px;">
            <input class="form-control ' . (($row->KiloFin == 0) ? $errclass : 'btn-info') . '"
                id="KiloFin_' . $rowcam->id . '" type="text"
                style="width:120px;text-align: right;font-size:14px" value="' . number_format($row->KiloFin, 2) . '" />
            <span class="input-group-btn"><label class="form-control" style="height:30px;">KM</label></span></div></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:125px;">
            <label class="form-control ' . (($row->KiloFin == 0) ? $errclass : 'btn-primary') . '"
                id="Km_' . $rowcam->id . '" style="width:80px;text-align: right;font-size:14px">'
                . number_format($row->KM, 2) . '</label>
            <span class="input-group-btn"><label class="form-control" style="height:30px;">KM</label></span></div></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:118px;">
            <input class="form-control btn-info" type="text" id="Fuel_' . $rowcam->id . '"
                style="width:80px;text-align: right;font-size:14px" value="' . number_format($row->Fuel, 2) . '" />
            <span class="input-group-btn"><label class="form-control" style="height:30px;">Li</label></span></div></td>';

        $html .= '<td align="center">
            <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-info') . '"
                type="time" id="HreDeb_' . $rowcam->id . '"
                style="width:120px;text-align: center;font-size:14px" value="' . $row->HreDeb . '" /></td>';

        $html .= '<td align="center">
            <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-info') . '"
                type="time" id="HreFin_' . $rowcam->id . '"
                style="width:120px;text-align: center;font-size:14px" value="' . $row->HreFin . '" /></td>';

        $html .= '<td align="center">
            <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-primary') . '"
                type="time" id="HrsTrav_' . $rowcam->id . '"
                style="width:120px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" /></td>';

        $html .= '<td><input class="form-control btn-warning" type="text" id="Transp_' . $rowcam->id . '"
            style="width:120px;text-align: center;font-size:14px" value="' . $transport . '" /></td>';

        $html .= '<td><input class="form-control btn-success" type="text" id="Collecte_' . $rowcam->id . '"
            style="width:120px;text-align: center;font-size:14px" value="' . $collecte . '" /></td>';

        $html .= '<td><input class="form-control" type="text" id="Dispo_' . $rowcam->id . '"
            style="width:120px;text-align: center;font-size:14px;background-color: #E5E5E5;" value="' . $tonnage . '" /></td>';

        $html .= '<td>' . $test . '</td></td>';

        return $html;
    }

    /**
     * Construit la ligne en-tête d'un camion broker.
     *
     * @param object $row       Ligne sanicam
     * @param object $rowcam    Ligne camionjour
     * @param int    $trIter    Compteur
     * @param float  $tonnage   Tonnage (disposition)
     * @return string HTML
     */
    public function buildBrokerTruckRow($row, $rowcam, $trIter, $tonnage = 0)
    {
        $html = '<tr id="trcam_' . $row->idcam . '">
            <td class="supprime"><button type="button"
                class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es"
                onclick="ShowHideRap(\'tr_' . $trIter . '\',' . $trIter . ')" style="outline:none;">
                <span id="spShowHide_' . $trIter . '" class="glyphicon glyphicon-chevron-down"
                    style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
            <td align="left"><label id="lb_chauf_' . $trIter . '" style="width:190px">' . $row->Chauffeur . '</label></td>
            <td align="center"><label id="lb_cam_' . $trIter . '" style="width:70px;color:darkred;">' . $row->NOCAMION . '</label></td>';

        $postBtn = ' <a aria-label="Edit" title="Cliquer pour enregistrer le montant" onclick="postRHDayBroker(' . $row->idsanrh . ')">
            <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';

        $html .= '<td colspan="4" align="center"><label style="background-color:#FFA500;color:white;font-weight:bold;padding:4px 12px;border-radius:4px;">BROKER</label></td>
            <td colspan="5" align="center">
                <div class="input-group input-group-sm" style="width:300px;">
                    <span class="input-group-addon" style="font-weight:bold;background-color:#FFA500;color:white;">Montant $</span>
                    <input class="form-control" style="font-size:14px;font-weight:bold;text-align:right;" type="number" step="0.01"
                        id="montant_' . $row->idsanrh . '" name="montant_' . $row->idsanrh . '"
                        value="' . $row->montant_forfaitaire . '" placeholder="0.00" />
                </div>
            </td>
            <td align="center">
                <input class="form-control" type="text" id="Dispo_' . $rowcam->id . '"
                    style="width:120px;text-align:center;font-size:14px;background-color:#E5E5E5;" value="' . $tonnage . '" /></td>
            <td align="center">' . $postBtn . '</td>';

        return $html;
    }

    /**
     * Construit la ligne "Informations sur autre ligne du camion".
     *
     * @param object $row       Ligne sanicam
     * @param object $rowcam    Ligne camionjour
     * @param int    $trIter    Compteur
     * @return string HTML
     */
    public function buildDuplicateTruckRow($row, $rowcam, $trIter)
    {
        $html = '<tr id="trcam_' . $row->idcam . '">
            <td class="supprime"><button type="button"
                class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es"
                onclick="ShowHideRap(\'tr_' . $trIter . '\',' . $trIter . ')" style="outline:none;">
                <span id="spShowHide_' . $trIter . '" class="glyphicon glyphicon-chevron-down"
                    style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
            <td align="left"><label id="lb_chauf_' . $trIter . '" style="width:190px">' . $row->Chauffeur . '</label></td>
            <td align="center"><label id="lb_cam_' . $trIter . '" style="width:70px;color:darkred;">' . $row->NOCAMION . '</label></td>';
        $html .= '<td colspan="8" align="center">Informations sur autres ligne du camion<label style="width:70px;color:darkred;">' . $row->NOCAMION . '</label></td>';
        return $html;
    }

    /**
     * Construit la ligne d'un camion de remplacement (fond papayawhip).
     *
     * @param object $row Ligne remplacement
     * @return string HTML
     */
    public function buildReplacementRow($row)
    {
        $errclass = ' btn-danger ';
        $test = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;'
            onclick='DayCamChauf(" . $row->idcam . ")' title='Enregistrer'><i class='fa fa-check btn btn-success' style='font-size:16px;'></i></a>";

        $html = '</tr><tr id="trcam_' . $row->idcam . '" style="background-color:papayawhip">
            <td colspan="2" style="background-color:white"></td>
            <td align="center"><label style="width:70px;color:darkred;">' . $row->remplace_cam . '</label></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:130px;">
            <input class="form-control btn-info" type="text" id="KiloDeb_' . $row->idcam . '"
                style="width:120px;text-align: right;font-size:14px" value="' . number_format($row->KiloDeb, 2) . '" />
            <span class="input-group-btn"><label class="form-control" style="height:30px;">KM</label></span></div></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:130px;">
            <input class="form-control ' . (($row->KiloFin == 0) ? $errclass : 'btn-info') . '"
                id="KiloFin_' . $row->idcam . '" type="text"
                style="width:120px;text-align: right;font-size:14px" value="' . number_format($row->KiloFin, 2) . '" />
            <span class="input-group-btn"><label class="form-control" style="height:30px;">KM</label></span></div></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:125px;">
            <label class="form-control ' . (($row->KiloFin == 0) ? $errclass : 'btn-primary') . '"
                id="Km_' . $row->idcam . '" style="width:80px;text-align: right;font-size:14px">'
                . number_format($row->KM, 2) . '</label>
            <span class="input-group-btn"><label class="form-control" style="height:30px;">KM</label></span></div></td>';

        $html .= '<td align="center">
            <div class="input-group input-group-sm" style="width:118px;">
            <input class="form-control btn-info" type="text" id="Fuel_' . $row->idcam . '"
                style="width:80px;text-align: right;font-size:14px" value="' . number_format($row->Fuel, 2) . '" />
            <span class="input-group-btn"><label class="form-control" style="height:30px;">Li</label></span></div></td>';

        $html .= '<td align="center">
            <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-info') . '"
                type="time" id="HreDeb_' . $row->idcam . '"
                style="width:120px;text-align: center;font-size:14px" value="' . $row->HreDeb . '" /></td>';

        $html .= '<td align="center">
            <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-info') . '"
                type="time" id="HreFin_' . $row->idcam . '"
                style="width:120px;text-align: center;font-size:14px" value="' . $row->HreFin . '" /></td>';

        $html .= '<td align="center">
            <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-primary') . '"
                type="time" id="HrsTrav_' . $row->idcam . '"
                style="width:120px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" /></td>';

        $html .= '<td align="center">' . $test . '</td>
            <td colspan="3" style="background-color:white"></td>';

        return $html;
    }

    // ===================================================================
    //  SECTION RH (Chauffeurs / Aides)
    // ===================================================================

    /**
     * Construit la section RH (chauffeur + aides) pour un camion.
     *
     * @param object[]          $rhRows          Résultat de getRHData
     * @param int               $numRows         Nombre de lignes RH
     * @param string            $typeExtraOptions HTML options pour type extra
     * @return string HTML des lignes RH (sans l'enveloppe table)
     */
    public function buildRHRows($rhRows, $numRows, $typeExtraOptions)
    {
        $html = '';
        if ($numRows > 0) {
            foreach ($rhRows as $row) {
                if ($row->isbroker == 1) {
                    $html .= $this->buildRHBrokerRow($row);
                } else {
                    $html .= $this->buildRHNormalRow($row, $typeExtraOptions);
                }
            }
        } else {
            $html .= '<td colspan="7" align="center">Information des RH sur autre ligne.</td>';
        }
        return $html;
    }

    /**
     * Ligne RH pour un broker.
     */
    private function buildRHBrokerRow($row)
    {
        $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postRHDayBroker(' . $row->id . ')">
            <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
        return '<tr id="trligne_' . $row->id . '" style="background-color:#FFF3E0;">
            <td align="center"><label style="width:250px;background-color:#FFA500;color:white;font-weight:bold;padding:2px 8px;border-radius:4px;">' . $row->Chauffeur . '</label></td>
            <td align="center" colspan="5">
                <div class="input-group input-group-sm" style="width:250px;">
                    <span class="input-group-addon" style="font-weight:bold;">Montant $</span>
                    <input class="form-control" style="font-size:14px;font-weight:bold;text-align:right;" type="number" step="0.01"
                        id="montant_' . $row->id . '" name="montant_' . $row->id . '"
                        value="' . $row->montant_forfaitaire . '" placeholder="0.00" />
                </div>
            </td>
            <td>&nbsp;</td>
            <td>' . $post . '</td></tr>';
    }

    /**
     * Ligne RH pour un chauffeur ou aide normal.
     */
    private function buildRHNormalRow($row, $typeExtraOptions)
    {
        $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postRHDay(' . $row->id . ')">
            <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';

        $html = '<tr id="trligne_' . $row->id . '">
            <td align="center"><label style="width:250px">' . $row->Chauffeur . '</label></td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                    id="HreDeb_' . $row->id . '" name="HreDeb_' . $row->id . '" value="' . $row->HreDeb . '" />
            </td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                    id="HreFin_' . $row->id . '" name="HreFin_' . $row->id . '" value="' . $row->HreFin . '" />';

        if ($row->TypeRh == "0") {
            // Chauffeur → afficher colonnes extra
            $html .= '</td>
                <td align="center">
                    <select class="form-control form-control-modern" id="TypeExtra_' . $row->id . '"
                        style="width:180px;color:red;font-weight:bold;">
                        <option value="">Choisir</option>' . $typeExtraOptions . '
                    </select>
                </td>
                <td align="center">
                    <input class="form-control" style="width: 100px;font-size:14px;font-weight:bold;" type="time"
                        id="HreDeb_extra_' . $row->id . '" name="HreDeb_extra_' . $row->id . '"
                        value="' . $row->HreDeb_extra . '" />
                </td>
                <td align="center">
                    <input class="form-control" style="width: 100px;font-size:14px;font-weight:bold;" type="time"
                        id="HreFin_extra_' . $row->id . '" name="HreFin_extra_' . $row->id . '"
                        value="' . $row->HreFin_extra . '" />
                </td>';
        } else {
            // Aide → colonnes vides
            $html .= '<td></td><td></td><td></td>';
        }

        $html .= '<td align="center">
                <input class="form-control" type="time" id="HrsTrav_' . $row->id . '"
                    style="width:100px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" />
            </td>
            <td>' . $post . '</td></tr>';

        return $html;
    }

    /**
     * Construit l'enveloppe RH complète (table "Chauffeur et Aide(s)").
     *
     * @param string $rhRowsHtml  HTML des lignes RH
     * @return string HTML table complète
     */
    public function wrapRHSection($rhRowsHtml)
    {
        return '<tr class="info" style="text-align:right;"><td colspan="13"><table class="table table-bordered">
            <thead>
                <tr><th colspan="9"><label class="titrenb">Chauffeur et Aide(s)</label></th></tr>
                <tr style="text-align:right;color:darkblue;font-weight:bold;">
                    <th style="text-align:center">CHAUFFEUR</th>
                    <th style="text-align:center">HRE DEB</th>
                    <th style="text-align:center">HRE FIN</th>
                    <th style="text-align:center">TYPE EXTRA</th>
                    <th style="text-align:center">HRE DEB EXTRA</th>
                    <th style="text-align:center">HRE DEB EXTRA</th>
                    <th style="text-align:center">HRES TRAV.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>' . $rhRowsHtml . '</tbody></table>';
    }

    // ===================================================================
    //  SOMMAIRE
    // ===================================================================

    /**
     * Construit la section SOMMAIRE pour un camion.
     *
     * @param int    $trIter   Compteur
     * @param object $entity   CamDayEntity
     * @param int    $idsanrh  ID sanivan_rh pour le bouton enregistrer
     * @param bool   $print    Afficher/masquer
     * @return string HTML
     */
    public function buildSommaireSection($trIter, $entity, $idsanrh, $print)
    {
        $display = $print ? '' : " <script> \$('#tr_" . $trIter . "').hide(); </script>";

        $html = '<tr style="text-align:right;" class="warning" id="tr_' . $trIter . '"><td></td><td colspan="14">
            <table class="table table-bordered">
            <thead>
                <tr><th colspan="12"><label class="titrenb">SOMMAIRE</label></th></tr>
                <tr style="text-align:right;color:darkblue;font-weight:bold;">
                    <th></th>
                    <th style="text-align:center">HRES CHAUF</th>
                    <th style="text-align:center">$ CHAUF</th>
                    <th style="text-align:center">HRES AIDE</th>
                    <th style="text-align:center">$ AIDE</th>
                    <th style="text-align:center">HRES CAM</th>
                    <th style="text-align:center">FUEL</th>
                    <th style="text-align:center">$ FUEL</th>
                    <th style="text-align:center">TRANSPORT</th>
                    <th style="text-align:center">COLLECTE</th>
                    <th style="text-align:center">DISPOSITION</th>
                    <th style="text-align:center">$ DISPO</th>
                    <th style="text-align:center">NB BACS</th>
                </tr>
            </thead>
            <tbody>';

        $html .= '<tr>
            <td align="center"><a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postSommaire(' . $idsanrh . ')">
                <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a></td>';

        $fields = ['hresChauf', 'coutChauf', 'hresAide', 'coutAide', 'hresCam',
                    'carburant', 'coutCarburant', 'transport', 'collecte',
                    'disposition', 'coutDispot', 'nbBac'];
        foreach ($fields as $f) {
            $html .= '<td align="center"><label class="form-control form-control-modern" style="width:100px">'
                . $entity->$f . '</label></td>';
        }
        $html .= '</tr>';

        return $html;
    }

    // ===================================================================
    //  SECTION VILLES
    // ===================================================================

    /**
     * Construit une ligne ville (collecte).
     *
     * @param object $row          Ligne info_villes
     * @param string $depotoireHtml HTML du dépotoire associé
     * @return string HTML
     */
    public function buildVilleRow($row, $depotoireHtml)
    {
        $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postVilleDay(' . $row->id . ')">
            <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';

        $html = '<tr class="info" style="text-align:right;"><td colspan="13">
            <table class="table table-bordered">
            <thead>
                <tr><th colspan="6"><label class="titrenb">COLLECTE / ' . $row->TypeDeProduit . '</label></th></tr>
                <tr style="text-align:right;color:darkblue;font-weight:bold;">
                    <th style="text-align:center">VILLE</th>
                    <th style="text-align:center">HRE DEB</th>
                    <th style="text-align:center">HRE FIN</th>
                    <th style="text-align:center">NB BACS</th>
                    <th style="text-align:center">HRES TRAV.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

        $html .= '<tr id="trligne_' . $row->id . '">
            <td align="center"><label class="titreville">' . $row->NomVille . '</label></td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                    id="HreDeb_' . $row->id . '" name="HreDeb_' . $row->id . '" value="' . $row->HreDeb . '" /></td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                    id="HreFin_' . $row->id . '" name="HreFin_' . $row->id . '" value="' . $row->HreFin . '" /></td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="text"
                    id="nbBac_' . $row->id . '" name="nbBac_' . $row->id . '" value="' . $row->nbBac . '" /></td>
            <td align="center">
                <input class="form-control" type="time" id="HrsTrav_' . $row->id . '"
                    style="width:100px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" /></td>
            <td>' . $post . '</td></tr>
            <tr><td>' . $depotoireHtml . '</td></tr>
            </tbody></table></td></tr>';

        return $html;
    }

    // ===================================================================
    //  SECTION DÉPOTOIRE
    // ===================================================================

    /**
     * Construit la section dépotoire pour une ville.
     *
     * @param object[] $rows              Lignes info_depotoire
     * @param string   $depotoireOptions  HTML options (GetAllDepotoires)
     * @return string HTML
     */
    public function buildDepotoireSection($rows, $depotoireOptions)
    {
        $html = '<tr style="text-align:right;"><td colspan="9"><table class="table table-bordered">
            <thead>
                <tr><th colspan="11"><label class="titrenb">DISPOSITION</label></th></tr>
                <tr style="text-align:right;color:darkblue;font-weight:bold;">
                    <th style="text-align:center">D&Eacute;POTOIRE</th>
                    <th style="text-align:center">BILLET</th>
                    <th style="text-align:center">TONNAGE</th>
                    <th style="text-align:center">HRE FIN</th>
                    <th style="text-align:center">TRANSBO</th>
                    <th style="text-align:center">Bo&icirc;te</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

        foreach ($rows as $row) {
            $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDepDay(' . $row->id . ')">
                <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';

            $checked = ($row->IsTransBo == 1) ? ' checked ' : '';
            $boite   = ($row->boite == 'none') ? '' : $row->boite;

            if (trim($row->Ticket) != '') {
                $ticket = '<span class="input-group-btn">
                    <a href="https://robert-daoust.ca/' . $row->Ticket . '" target="_blank" style="font-size:16px;" download>
                        <i class="fa fa-download btn btn-success" style="font-size:16px;height:30px;"></i>&nbsp;&nbsp;Voir le BILLET</a>
                    </span>';
            } else {
                $ticket = '<input class="form-control" style="width:450px;" name="edtAddTikect" id="edtAddTikect" type="file" />';
            }

            $html .= '<tr id="trligne_' . $row->id . '">
                <td align="center">
                    <select class="form-control form-control-modern" id="Depotoire_' . $row->id . '"
                        style="width:305px;color:red;font-weight:bold;height:47px;">
                        <option value="">Choisir</option>' . $depotoireOptions . '
                    </select></td>
                <td align="center">' . $ticket . '</td>
                <td align="center">
                    <input class="form-control" style="width: 90px;font-size:14px;" type="text"
                        id="Tonnage_' . $row->id . '" name="Tonnage_' . $row->id . '" value="' . $row->Tonnage . '" /></td>
                <td align="center">
                    <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                        id="fin_' . $row->id . '" name="fin_' . $row->id . '" value="' . $row->fin . '" /></td>
                <td align="center">
                    <input type="checkbox" class="checkbox-custom checkbox-primary" id="chkTransbo_' . $row->id . '"
                        ' . $checked . ' style="width:30px;height:30px;margin-left:0px" /></td>
                <td align="center">
                    <input type="text" class="form-control form-control-modern" id="Boite_' . $row->id . '"
                        value="' . $boite . '" style="width:90px; background-color: yellow; font-weight: bold;" /></td>
                <td>' . $post . '</td></tr>';
        }

        $html .= '</tbody></table></td></tr>';
        return $html;
    }

    // ===================================================================
    //  TOTAUX DU JOUR (pied de rapport)
    // ===================================================================

    /**
     * Construit la ligne de totaux du jour (identique à Collection::getDayReport).
     *
     * @param array $totals  Tableau retourné par CamDayCollection::getTotals()
     * @return string HTML <td>...</td>
     */
    public function buildDayReportRow(array $totals)
    {
        $fields = ['hresChauf', 'coutChauf', 'hresAide', 'coutAide', 'hresCam',
                    'carburant', 'coutCarburant', 'transport', 'collecte',
                    'disposition', 'coutDispot', 'nbBac', 'revenu', 'net', 'netByHres'];
        $colors = [
            'coutChauf' => 'color:red;', 'coutAide' => 'color:red;', 'coutCarburant' => 'color:red;',
            'coutDispot' => 'color:red;',
            'revenu' => 'background-color:yellow;', 'net' => 'background-color:yellow;',
            'netByHres' => 'background-color:yellow;',
        ];

        $html = '';
        foreach ($fields as $f) {
            $style = isset($colors[$f]) ? $colors[$f] : '';
            $html .= '<td align="center"><label class="form-control form-control-modern" style="width:100px;' . $style . '">'
                . $totals[$f] . '</label></td>';
        }
        return $html;
    }
}
