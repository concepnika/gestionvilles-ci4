<?php

namespace App\Models;

/**
 * Port mecanique de Cedules_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class CedulesModel extends Ci3Model
{
  /** @var array $data */
	protected $data = array();
  /** @var array $table */
	protected $table = array('typecamion', 'typerh', 'typeabsence', 'typededechet',
                          'statutcamion', 'pertetemps', 'depotoire', 'emplacements', 'dictionprix','', '','typeextra');
  protected $JourDeSemaine  = array
              ( 'DIM', 'LUN', 'MAR',
                          'MER', 'JEU', 'VEN', 'SAM' ) ;
  protected $activeVille = 0;
  //----------------------------
	function __construct() {
		parent::__construct();
		
        // $this->remote_db =  // Désactivé - connexion distante bloquée sur WHC
        
	}
  /**
   *
   * @param string $dateactive
   * @param int $from
   *
   * @return array
   */
  public function LoadToday($dateactive, $from, $activedrop)
  {
    $this->InsertHelperForToday();
    if ($from == 4)
    {
      $this->LoadCedules($dateactive, $from, $activedrop);

      return $this->data;
    }
    else
    {
      $this->LoadCedules($dateactive, $from, $activedrop);
      $this->GetOptVilles();
      return $this->data;
    }
    //
  }
  /**
   * @param string $typeprod
   * @param string $camion
   * @param int $chauffeur
   * @param int $aide
   * @param int $id
   * @param int $idgroupe
   * @param int $isstart
   * @param string $date
   *
   * @return void
   */
  public function UpdEvent($typeprod, $camion, $chauffeur, $aide, $id, $idgroupe, $isstart, $date)
  {
    /** @var string $strSQL */
    $strSQL = "UPDATE cedules
	                    SET
		                    TypeDeProduit=$typeprod,
		                    NOCAMION=$camion,
		                    idchauffeurs=$chauffeur,
		                    HELPERS_1=$aide,
                        DateJour = $date
	                    WHERE id = $id";
    $this->db->query($strSQL);
    $strSQL = "UPDATE grp_cedules SET datejour=$date
	                WHERE id =$idgroupe ";
    $this->db->query($strSQL);
    $chauf = str_replace("'", "",$chauffeur );
    if (($chauf != '24' or $chauf != '25' or $chauf != '0' ) and (substr(str_replace("'", "",$camion ) ,0, 3) !='Cam'))
    {
      $strSQL = "SELECT id, idchauffeur, NOCAMION, KiloDeb, KiloFin, Fuel, PrixFuel, DateJour
											FROM camionjour WHERE DateJour = $date AND NOCAMION = $camion  " ;
      if (!($objCam = $this->db->query($strSQL)->getRow(0)))
      {
        $strSQL = "INSERT INTO camionjour
										(NOCAMION, datejour)
													VALUES ($camion, $date) ";
        $this->db->query($strSQL);
      }
      $strSQL = "SELECT id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin   FROM suivis WHERE
																						datejour = $date  AND idrh = $chauffeur ";
      if (!($objCam = $this->db->query($strSQL)->getRow(0)))
      {
        $strSQL = "INSERT INTO suivis
										(idrh, datejour, NOCAMION)
													VALUES ($chauffeur, $date, $camion) ";
        $this->db->query($strSQL);
      }
      else
      {
        if ($objCam->NOCAMION != $camion)
        {
          $strSQL = "INSERT INTO suivis
										(idrh, datejour, NOCAMION)
													VALUES ($chauffeur, $date, $camion) ";
          $this->db->query($strSQL);
        }
      }
      $helper = str_replace("'", "",$aide );
      if ($helper != '24' or $helper != '25' or $helper != '0' )
      {
        $strSQL = "SELECT id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin  FROM suivis WHERE
																						  datejour = $date  AND idrh = $aide ";
        if (!($objCam = $this->db->query($strSQL)->getRow(0)))
        {
          $strSQL = "INSERT INTO suivis
										  (idrh, datejour, NOCAMION)
													  VALUES ($aide, $date, $camion) ";
          $this->db->query($strSQL);
        }
        else
        {
          if ($objCam->NOCAMION != $camion)
          {
            $strSQL = "INSERT INTO suivis
										  (idrh, datejour, NOCAMION)
													  VALUES ($aide, $date, $camion) ";
            $this->db->query($strSQL);
          }
        }
      }
    }
  }
  /**
   * @param string $typeprod
   * @param string $camion
   * @param int $chauffeur
   * @param int $aide
   * @param int $id
   * @param int $isstart
   * @param string $date
   *
   * @return void
   */
  public function UpdCedule($typeprod, $camion, $chauffeur, $aide, $id, $isstart, $date)
  {
    /** @var string $strSQL */
    $strSQL = "UPDATE cedules
	                    SET
		                    TypeDeProduit=$typeprod,
		                    NOCAMION=$camion,
		                    idchauffeurs=$chauffeur,
		                    HELPERS_1=$aide
	                    WHERE id = $id";
    $this->db->query($strSQL);

  }
    /**
     * Récupère les secteurs d'une ville pour un dropdown
     * @param int $idville
     * @param int $selectedId ID du secteur sélectionné
     * @return string HTML options
     */
    public function GetOptSecteursParVille($idville, $selectedId = 0)
    {
        $returnval = '';

        if ($idville <= 0) {
            return $returnval;
        }

        $idville = $this->db->escape($idville);

        $strSQL = "SELECT id, Nom 
               FROM secteurs 
               WHERE idville = $idville 
                 AND isdeleted = 0
               ORDER BY Nom";

        $query = $this->db->query($strSQL);

        foreach ($query->getResult() as $row) {
            $selected = ($row->id == $selectedId) ? ' selected' : '';
            $returnval .= '<option value="' . $row->id . '"' . $selected . '>'
                . htmlspecialchars($row->Nom)
                . '</option>';
        }

        return $returnval;
    }
  /**
   *
   * @param object $resultLine
   * @param string $dateactive
   * @param int $from
   * @param int $GrpID
   * @param array $ArrRow
   * @param int $iterRow
   *
   * @return array
   */
  private function getLignesCedule($resultLine, $dateactive, $from, $GrpID, $ArrRow, $activedrop, &$iterRow, $padRows = true)
  {
    //$activedrop = $this->input->post_get('activedrop');
    /** @var string $ColOne */

    /** @var string $color */

    /** @var object $row */
        /** @var int $idcedule */
        $idcedule = 0;
    /** @var array $idsConfirm */
    $idsConfirm = array();
    foreach(((is_object($resultLine)) ? $resultLine->getResult() : $resultLine) as $row)
    {
      switch ($row->TypeDeProduit)
      {
        case 'ORGANIQUE':
          $color = '#ebccaa';
          break;
        case 'RECYCLAGE':
          $color = '#9FF3F9';
          break;
        case 'DECHETS':
          $color = '#E5E5E5';
          break;
        case 'RESIDUS_VERT':
          $color = '#2DBC96';
          break;
        case 'GROS_REBUTS':
          $color = '#2B3B7AF';
          break;
        case 'BRANCHES':
          $color = '#D1B546';
          break;
        case 'FEUILLES':
          $color = '#2DBC96';
          break;
        default:
          $color = '#F7F1C5';
      }
      $ColOne = '<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
                  '.(($row->TypeCedule == 2)? '' : '<td  align="center">&nbsp;</td>').'
    							<td  align="center">&nbsp;</td>';
      if ($GrpID != $row->idgrp)
      {
        //$nbCamion =   '<input id="nbCamion_'.$row->ID.'"    style="width:40px;height:20px"
        //                             onblur="setNbCam(this.value,'.$row->idgrp.')"   type="text" value="'.$row->nb_cam.'"/>';
        $confirmeDelete = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;' id='btnsupville_".$row->idvilles."'
                                onclick='RemoveVilleCreate(".$row->idgrp.','.$row->ACol.")' title='Suppression de la ville'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></a>";
        $sendconfirme = "<a class='btn btn primary' style='font-size:16px;cursor:pointer;width: 50px;' id='btnsendville_".$row->idvilles."'
                                onclick='sendConfirmVille(".$row->idvilles.")' title='Envoi à la ville'><i class='glyphicon glyphicon-send' aria-hidden='true' ></i></a>";
        $typeced = (($row->TypeCedule == 2) ? '1' : '2');
        $trto = (($row->TypeCedule == 2) ? 'Transfert->REAREND' : 'Transfert->ROBOT');
        $transfertTo = "<a class='btn btn primary' style='font-size:16px;cursor:pointer;width: 50px;' id='btntransfert_".$row->idvilles."'
                                onclick='transfertToCedule(".$row->ID.','.$typeced.','.$row->idgrp.")'>
<i class='fa fa-arrow-circle-o-right' style='font-size:24px'></i></a>";
        $retireSup = "<a class='btn btn primary' style='font-size:16px;cursor:pointer;width: 50px;' id='btnretiresup_".$row->idvilles."'
                                onclick='clearSupVille(".$row->idgrp.")' title='Retirer du superviseur'><i class='fa fa-user' style='font-size:18px;color:#16417c'></i></a>";
        $selectActVille = '<select style="width:50px">
                            <option style="color:red">'. $transfertTo.'</option>
                            <option style="color:red">'.$sendconfirme.'</option>
                            <option style="color:white">'.$confirmeDelete.'</option>
                          </select>';
        $selectActVille = ' <ul class="nav navbar-nav">
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:50px">
        <span>
          <i class="glyphicon glyphicon-chevron-down" style="color:darkblue;margin-top:-5px;font-size:16px;"></i>
        </span>
      </a>

      <ul class="dropdown-menu">
        <li>
          '. $transfertTo.$trto.'
        </li><li class="divider"></li>
        <li>
          '.$sendconfirme.'&nbsp;Envoyer Maintenant
        </li><li class="divider"></li>
        <li>
          '.$confirmeDelete.'&nbsp;Supprimer
        </li><li class="divider"></li>
        <li>
          '.$retireSup.'&nbsp;Retirer du superviseur
        </li>

      </ul>
    </li>

  </ul>';
		$oprang = '<select id="oprang_'.$row->ID.'" onchange="updTableRang('.$row->idgrp.','.$row->ARow.', this.value, '.$dateactive.', '.$row->ACol.','.$row->TypeCedule.','.((isset($row->idsup))? $row->idsup : 0).')" >';
        for($iter = 1; $iter <= $row->nbRow; $iter++)
        {
          $selected = (($row->ARow == $iter)? ' selected ' : '');
          $oprang .= '<option value="'.$iter.'" '.$selected.' >'.$iter.'</option>';
        }
        $oprang .= '</select>';
        $span = '';
        if($from ==3 )
        {
          $span = (($row->TypeCedule == 2)? '6' : '6');
        }
        else
        {
          $span = (($row->TypeCedule == 2)? '6' : '6');
        }
        $width = (($row->TypeCedule == 2)? '85%' : '100%');
        $tableville = '<table  style="width: 100%;"><tr> <td class="align-middle" style="background-color:'.$row->couleurville.' ;text-align:center;color:black;width: '.$width.';text-wrap:none;" >
<label id="lblville_'.$row->idvilles.'_'.$row->ACol.'_'.$row->idgrp.'" ondrop="dropligne(event,'.$row->idgrp.','.$row->TypeCedule.')" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event)" style="width: 100%;margin-left:5px;font-size: 14px;margin-top:5px;text-wrap:none;">'.
                                                            $row->nomville.'</label></td>
                         <td >'.$selectActVille.'</td><td>&nbsp;&nbsp;</td> <td>'.$oprang.'</td><td></td></tr></table>';
        $ville ='<label id="lblville_'.$row->idvilles.'_'.$row->ACol.'_'.$row->idgrp.'" '.(($from == 8)? '' : 'ondrop="dropligne(event,'.$row->idgrp.','.$row->TypeCedule.')" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event)"').' style="width: 100%;margin-left:5px;font-size: 14px;margin-top:5px;">'.
                                                            $row->nomville.'</label>';
        if (isset($ArrRow[$iterRow])) {

          $ArrRow[$iterRow] .= '<td class="align-middle" colspan="'.$span.'"   style="line-height:15px;background-color:'.$row->couleurville.' ;text-align:center;color:black;" >'.
              (($activedrop == 1 and $from != 8)? $tableville : $ville).'</td>';
        }
        else
        {
          $ArrRow[$iterRow] = '<td class="align-middle" colspan="'.$span.'"   style="line-height:15px;background-color:'.$row->couleurville.' ;text-align:center;color:black;" >'.
            (($activedrop == 1 and $from != 8)? $tableville : $ville).'</td>';
          }
        $GrpID = $row->idgrp;
        $iterRow++;
      }
      $edit = "<a  style='font-size:16px;color: #03F;cursor:pointer;'
                              onclick=\"ShowCedule('tr_".$row->ID."')\" title='Modifier'><i class='fa fa-pencil-square-o fa-4' aria-hidden='true'></i></a>";
      $button = '<div class="input-group"><span class="input-group-btn">'.$edit.'</span>

              </div>';
      if (isset($ArrRow[$iterRow]))
      {
        //if ($from == 3)
        //{
        //  //$ArrRow[$iterRow] .= '<td align="center"  style="width: 10px;"></td>' ;
        //}
        //else
        //{
        $ArrRow[$iterRow] .= '<td align="center"  style="width: 30px;line-height:15px;"><label style="width: 40px;margin-left:5px;margin-top:5px;">'.(($from == 5 or $from == 8)? '' : $button ).'</label></td>' ;
        //}
      }
      else{
        //if ($from == 3)
        //{
        //  $ArrRow[$iterRow] = '' ;
        //}
        //else
        //{
        $ArrRow[$iterRow] = '<td align="center"  style="width: 30px;line-height:15px;"><label style="width: 40px;margin-top:5px;">'.(($from == 5 or $from == 8)? '' : $button ).'</label></td>' ;
        //}
      }
      $cam = substr($row->NOCAMION ,0, 3);
      $backColor = '';
      $camToShow = $row->NOCAMION;
      if ($row->IsStart == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      if($row->HaveMat == 1)
      {
        $backColor = 'color:#dc143c;font-size: large;' ;
      }

      if ((($cam == '') or ($cam == 'Cam')) and (($row->nochauffeur != 0) or  ($row->HELPERS_1 != 0)) )
      {
        $backColor = 'background-color: fuchsia;' ;
        $cam = 'Cam';
      }
      else if ((($cam == '') or ($cam == 'Cam')) and ($row->nochauffeur == 0) and ($row->HELPERS_1 == 0) and ($row->Secteur != ''))
      {
        $cam = '';
        $camToShow = '';
      }

      $ArrRow[$iterRow] .= '<td  id="tdcam_'.$row->NOCAMION.'_'.$row->ID.'"  align="center" '.(($from == 8)? '' : 'ondrop="dropcam(event,'.$row->ID.')" draggable="true" ondragstart="drag(event)"  ondragover="allowDrop(event)"').'  style="line-height:15px;width: 50px;font-weight:bolder;'.$backColor.';"><label  id="cam_'.$row->ID.'" '.(($from == 8)? '' : 'onclick="SetStartCam(this.id, '.$dateactive.','.$from.')"').' style="width: 50px;'.$backColor.';font-size: '.((($cam == 'Cam'))? 'larger' : 'large' ).';margin-top:5px;cursor:pointer;">'.((($cam == 'Cam'))? '?' : $camToShow ).'</label></td>' ;

      //-------------------------------------------------------------------
      $backColor = '';
      if ($from != 8 and ($row->nochauffeur == 24 or $row->nochauffeur != 25 and $row->nochauffeur != 0) and ($row->chaufconfirme == 0) and ($row->TypeDeProduit == 'COMMERCIAL') )
      {
        $idsConfirm[] = $row->ID;
      }
      if ($row->chaufconfirme == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      $checked = (($row->chaufconfirme == 1)? ' checked="checked" ' : '');
      if ($from == 5 or $from == 8)
      {
        $showchauf =  '<label style="width: 150px;margin-left:5px;margin-top:5px;">' . (($row->chauffeur == 'NON-ASSIGNÉ, Chauf.')? '' : $row->chauffeur ) . '</label';
      }
      else
      {
        $showchauf =  '<label   onclick="SetStartChauf(this.id, '.$dateactive.','.$from.')" id="chauffeur_'.$row->ID.'" style="font-size:14px;font-weight:bold;width:150px;margin-top:5px;cursor:pointer;'.$backColor.'">
                                      ' .$row->chauffeur  . '</label>';
      }
      $ArrRow[$iterRow] .= '<td  align="left" id="tdchauffeur_'.$row->idchauffeurs.'_'.$row->ID.'" '.(($from == 8)? '' : 'ondrop="dropchauffeur(event,'.$row->ID.')" draggable="true" ondragstart="drag(event)"  ondragover="allowDrop(event)"').'  style="line-height:15px;width: 150px;font-size: 14px;font-weight:bold;'.$backColor.'"> ' .
                                '<div class="btn-group" style="margin-left:2px;width: 150px;"> '.$showchauf.'</div></td>';
      $backColor = '';
      if ($row->helperconfirme == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      $checked = (($row->helperconfirme == 1)? ' checked="checked" ' : '');
      if ($row->TypeCedule == 1)
      {
        if ($from == 5 or $from == 8)
        {
          $showaide =  '<label style="width: 150px;margin-left:5px;margin-top:5px;">' . (($row->aide1 == 'NON-ASSIGNÉ, Aide')? '' : $row->aide1) . '</label';
        }
        else
        {
          $showaide =  '<label   onclick="SetStartAide(this.id, '.$dateactive.','.$from.')" id="aide1_'.$row->ID.'" style="font-size:14px;font-weight:bold;width:150px;margin-top:5px;cursor:pointer;'.$backColor.'">
                                      ' .$row->aide1  . '</label>';
        }
                $ArrRow[$iterRow] .= '<td  align="left" id="tdaide_' . $row->HELPERS_1 . '_' . $row->ID . '" '.(($from == 8)? '' : 'ondrop="dropaide(event,' . $row->ID . ')" draggable="true" ondragstart="drag(event)"  ondragover="allowDrop(event)"').'  style="line-height:15px;width: 150px;font-size: 14px;' . $backColor .
                    '"><div class="btn-group" style="margin-left:2px;width: 150px;"> ' . $showaide . '</div></td>';
                // Récupérer le nombre de polylines sélectionnées
                $strSQL = "SELECT COUNT(*) as nb FROM cedule_polylines WHERE idcedule = " . $row->ID;
                $nbPolylines = $this->db->query($strSQL)->getRow()->nb;

                // Récupérer les noms des polylines sélectionnées avec leur ID
                $strSQL = "SELECT pv.id, pv.nom, pv.idville, s.id as idsecteur, s.Nom as nomsecteur
               FROM cedule_polylines cp
               INNER JOIN polilyne_ville pv ON cp.idpolyline = pv.id
               LEFT JOIN secteurs s ON pv.idsecteur = s.id
               WHERE cp.idcedule = " . $row->ID . "
               ORDER BY cp.ordre
               LIMIT 3";
                $idcedule = $row->ID;
                $polylines = $this->db->query($strSQL);
                $listPolylines = '';
                $count = 0;
                $idsecteur = 0;
                $nomsecteur = '';

                foreach ($polylines->getResult() as $poly) {
                    // Prendre le secteur de la première polyline
                    if ($count == 0) {
                        $idsecteur = $poly->idsecteur;
                        $nomsecteur = $poly->nomsecteur;
                    }

                    // Si le parcours a un secteur, utiliser showPolylineMapFromCedule
                    // Sinon, utiliser showPolylineSingle (affiche le parcours directement par son ID)
                    $onclickCmd = ((int)$poly->idsecteur > 0)
                        ? 'showPolylineMapFromCedule(' . (int) $idcedule . ', ' . (int) $poly->idsecteur . ', \'' . htmlspecialchars($poly->nomsecteur, ENT_QUOTES) . '\')'
                        : 'showPolylineSingle(' . (int) $poly->id . ', \'' . htmlspecialchars($poly->nom, ENT_QUOTES) . '\', ' . (int) $poly->idville . ')';

                    $listPolylines .= '<div style="font-size:9px;color:#4a90e2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px;cursor:pointer;"
            ' . (($from == 8)? '' : 'onclick="' . $onclickCmd . '"') . '
            title="Cliquer pour voir sur la carte: ' . htmlspecialchars($poly->nom) . '">
            <i class="fa fa-map-marker" style="font-size:8px;"></i> ' . htmlspecialchars($poly->nom) . '
        </div>';
                    $count++;
                }

                // Si plus de 3, ajouter un indicateur
                if ($nbPolylines > 3) {
                    $listPolylines .= '<div style="font-size:9px;color:#999;">... +' . ($nbPolylines - 3) . ' autre(s)</div>';
                }

                $btnClass = ($nbPolylines > 0) ? 'btn-info' : 'btn-success';
                $btnIcon = ($nbPolylines > 0) ? 'fa-edit' : 'fa-plus';
                $btnText = ($nbPolylines > 0) ? $nbPolylines : 'Ajouter';

                $ArrRow[$iterRow] .= '<td style="line-height:15px;vertical-align:top;" align="center">
        ' . (($from == 8)? '' : '<button onclick="showPolylineModal(' . $row->ID . ', ' . $row->idsecteur . ', ' . $row->idvilles . ')"
                class="btn btn-xs ' . $btnClass . '"
                style="margin-bottom:3px;"
                title="Gérer les parcours">
            <i class="fa ' . $btnIcon . '"></i> ' . $btnText . '
        </button>') . '
        ' . $listPolylines . '
    </td>';
      }
      else
      {
                // Récupérer le nombre de polylines sélectionnées
                $strSQL = "SELECT COUNT(*) as nb FROM cedule_polylines WHERE idcedule = " . $row->ID;
                $nbPolylines = $this->db->query($strSQL)->getRow()->nb;

                // Récupérer les noms des polylines sélectionnées avec leur ID
                $strSQL = "SELECT pv.id, pv.nom, pv.idville, s.id as idsecteur, s.Nom as nomsecteur
               FROM cedule_polylines cp
               INNER JOIN polilyne_ville pv ON cp.idpolyline = pv.id
               LEFT JOIN secteurs s ON pv.idsecteur = s.id
               WHERE cp.idcedule = " . $row->ID . "
               ORDER BY cp.ordre
               LIMIT 3";
                $idcedule = $row->ID;
                $polylines = $this->db->query($strSQL);
                $listPolylines = '';
                $count = 0;
                $idsecteur = 0;
                $nomsecteur = '';

                foreach ($polylines->getResult() as $poly) {
                    // Prendre le secteur de la première polyline
                    if ($count == 0) {
                        $idsecteur = $poly->idsecteur;
                        $nomsecteur = $poly->nomsecteur;
                    }

                    $listPolylines .= '<div class="polyline-clickable"
                        ' . (($from == 8)? '' : 'onclick="showPolylineMapFromCedule(' . (int) $idcedule . ', ' . (int) $idsecteur . ', \'' . htmlspecialchars($nomsecteur, ENT_QUOTES) . '\')"') . '
                        title="Cliquer pour voir sur la carte: ' . htmlspecialchars($poly->nom) . '">
                        <i class="fa fa-map-marker" style="font-size:8px;"></i> ' . htmlspecialchars($poly->nom) . '
                    </div>';
                    $count++;
                }

                // Si plus de 3, ajouter un indicateur
                if ($nbPolylines > 3) {
                    $listPolylines .= '<div style="font-size:9px;color:#999;">... +' . ($nbPolylines - 3) . ' autre(s)</div>';
                }

                $btnClass = ($nbPolylines > 0) ? 'btn-info' : 'btn-success';
                $btnIcon = ($nbPolylines > 0) ? 'fa-edit' : 'fa-plus';
                $btnText = ($nbPolylines > 0) ? $nbPolylines : 'Ajouter';

                $ArrRow[$iterRow] .= '<td style="line-height:15px;vertical-align:top;" align="center">
        ' . (($from == 8)? '' : '<button onclick="showPolylineModal(' . $row->ID . ', ' . $row->idsecteur . ', ' . $row->idvilles . ')"
                class="btn btn-xs ' . $btnClass . '"
                style="margin-bottom:3px;"
                title="Gérer les parcours">
            <i class="fa ' . $btnIcon . '"></i> ' . $btnText . '
        </button>') . '
        ' . $listPolylines . '
    </td>';
      }
      if ($from == 1 or $from == 4)
      {
        $showproduit = '<select class="form-control" onchange="SetTypeProd(this.id,'.$dateactive.')" id="produit_'.$row->ID.'" style="font-size:medium;font-weight:bold;width:150px;background-color:'.$color.'">
                                  <option value="DECHETS">Choisir</option>' .$this->GetAllProduits($row->TypeDeProduit) . '</select>';
        $ArrRow[$iterRow] .= '<td id="trproduit_'.$row->ID.'" class="cellule" align="center" style="background-color:'.$color.';" >' .$showproduit.'</td>';
      }
      else
      {
        $ArrRow[$iterRow] .= '<td class="cellule" align="center" style="background-color:'.$color.';" >' .
                  '<label style="width: 150px;margin-left:5px;background-color:'.$color.';font-size: 14px;">' .$row->TypeDeProduit . '</label></td>';
      }
      $iterRow++;
    }
    if ($padRows and isset($ColOne))
    {
      if ($iterRow < 25)
      {
        for ($iter = $iterRow; $iter <= 25; $iter++)
        {
          if (isset($ArrRow[$iter]))
          {
            $ArrRow[$iter]  .= $ColOne;
          }
          else
          {
            $ArrRow[$iter] = $ColOne;
          }
        }
      }
    }
    // Confirmation des chauffeurs COMMERCIAL : un seul UPDATE en batch
    // (avant : 1 UPDATE par ligne dans la boucle de rendu).
    if (count($idsConfirm) > 0)
    {
      /** @var string $updSQL */
      $updSQL = "UPDATE cedules SET chaufconfirme = 1
	             WHERE ID IN (" . implode(',', array_map('intval', $idsConfirm)) . ")";
      $this->db->query($updSQL);
    }

    return $ArrRow;
  }
  /**
   * Ajoute la colonne idsuperviseur a grp_cedules si absente.
   * idsuperviseur = reshumaine.id du superviseur responsable du groupe
   * de ville pour cette journee (NULL = non assigne).
   *
   * @return void
   */
  private function EnsureSuperviseurColumn()
  {
    if ($this->db->tableExists('grp_cedules') and ! $this->db->fieldExists('idsuperviseur', 'grp_cedules'))
    {
      $this->db->query("ALTER TABLE grp_cedules ADD COLUMN idsuperviseur INT NULL DEFAULT NULL");
    }
  }
  /**
   * Retourne les deux superviseurs affiches en lignes separatrices dans la
   * cedule du jour. L'id vient de reshumaine quand la personne existe,
   * sinon un id negatif fixe (les assignations restent persistees).
   *
   * @return array
   */
  public function GetSuperviseursCedule()
  {
    /** @var array $sups */
    $sups = array(
      array('id' => -1, 'nom' => 'Charles Daoust',  'prenom' => 'Charles', 'famille' => 'Daoust'),
      array('id' => -2, 'nom' => 'Robert Duperron', 'prenom' => 'Robert',  'famille' => 'Dup%ron'),
    );
    foreach ($sups as $idx => $sup)
    {
      /** @var string $strSQL */
      $strSQL = "SELECT id, CONCAT(TRIM(Prenom), ' ', TRIM(Nom)) AS nomcomplet
                          FROM reshumaine
                          WHERE isdeleted = 0 AND Prenom LIKE '".$sup['prenom']."%'
                            AND Nom LIKE '".$sup['famille']."'
                          ORDER BY id LIMIT 1";
      $row = $this->db->query($strSQL)->getRow(0);
      if ($row)
      {
        // L'id vient de reshumaine; le nom affiche garde l'orthographe
        // demandee par le client (DUPERRON, sans accent).
        $sups[$idx]['id'] = $row->id;
      }
    }
    return $sups;
  }
  /**
   * Indice de bande superviseur: 0 = 1er superviseur, 1 = 2e, 2 = non assigne.
   *
   * @param int $idsup
   * @param array $sups
   *
   * @return int
   */
  private function SupBandIndex($idsup, $sups)
  {
    foreach ($sups as $idx => $sup)
    {
      if ($idsup == $sup['id']) return $idx;
    }
    return 2;
  }
  /**
   * Table de visibilite des lignes separatrices superviseurs, par
   * journee. Reglage partage par TOUS les usagers (contrairement a la
   * session) mais limite a la date cochee/decochee: les autres journees
   * gardent le defaut (affichees).
   *
   * @return void
   */
  private function EnsureSupVisTable()
  {
    if (! $this->db->tableExists('cedules_supvis'))
    {
      $this->db->query("CREATE TABLE cedules_supvis (
                          datejour DATE NOT NULL,
                          supidx INT NOT NULL,
                          visible INT NOT NULL DEFAULT 1,
                          PRIMARY KEY (datejour, supidx))");
    }
  }
  /**
   * Visibilite des lignes separatrices superviseurs pour une journee
   * (table cedules_supvis, partagee par tous les usagers).
   * Defaut: les deux affichees.
   *
   * @param string $datejour
   *
   * @return array
   */
  private function GetSupVisibilite($datejour)
  {
    /** @var array $vis */
    $vis = array(1, 1);
    $this->EnsureSupVisTable();
    /** @var string $dte */
    $dte = $this->db->escape($datejour);
    /** @var object $result */
    $result = $this->db->query("SELECT supidx, visible FROM cedules_supvis WHERE datejour = $dte");
    foreach ($result->getResult() as $row)
    {
      if (isset($vis[intval($row->supidx)]))
      {
        $vis[intval($row->supidx)] = intval($row->visible);
      }
    }
    return $vis;
  }
  /**
   * Persiste la visibilite d'une ligne separatrice superviseur pour une
   * journee donnee (reglage global, visible par tous les usagers).
   *
   * @param int $supidx
   * @param int $visible
   * @param string $datejour
   *
   * @return void
   */
  public function SetSupVisibilite($supidx, $visible, $datejour)
  {
    $this->EnsureSupVisTable();
    /** @var string $dte */
    $dte = $this->db->escape($datejour);
    /** @var int $vis */
    $vis = ((intval($visible) == 0) ? 0 : 1);
    $this->db->query("INSERT INTO cedules_supvis (datejour, supidx, visible)
                        VALUES ($dte, ".intval($supidx).", $vis)
                        ON DUPLICATE KEY UPDATE visible = $vis");
  }
  /**
   * Superviseurs dont la ligne separatrice est affichee (cases du menu
   * cochees) pour la journee. Les villes d'un superviseur cache sont
   * affichees dans la bande non assignee (l'assignation est conservee en BD).
   *
   * @param string $datejour
   *
   * @return array
   */
  public function GetSuperviseursVisibles($datejour)
  {
    /** @var array $vis */
    $vis = $this->GetSupVisibilite($datejour);
    /** @var array $out */
    $out = array();
    foreach ($this->GetSuperviseursCedule() as $idx => $sup)
    {
      if ($vis[$idx] == 1) $out[] = $sup;
    }
    return $out;
  }
  /**
   * Renumerote les ARow d'une colonne (datejour, ACol, TypeCedule) dans
   * l'ordre d'affichage: bande superviseur 1, bande superviseur 2,
   * non assignes; ARow conserve l'ordre a l'interieur de chaque bande.
   *
   * @param string $date
   * @param int $acol
   * @param int $typecedule
   *
   * @return void
   */
  public function RenumberColumnDisplay($date, $acol, $typecedule)
  {
    /** @var array $sups */
    $sups = $this->GetSuperviseursVisibles($date);
    /** @var string $dte */
    $dte = $this->db->escape($date);
    /** @var string $strSQL */
    $strSQL = "SELECT id, ARow, IFNULL(idsuperviseur, 0) AS idsup
                      FROM grp_cedules
                      WHERE datejour = $dte AND ACol = ".$this->db->escape($acol)."
                        AND TypeCedule = ".$this->db->escape($typecedule)."
                      ORDER BY ARow, id";
    $result = $this->db->query($strSQL);
    /** @var array $buckets */
    $buckets = array(0 => array(), 1 => array(), 2 => array());
    foreach ($result->getResult() as $row)
    {
      $buckets[$this->SupBandIndex($row->idsup, $sups)][] = $row->id;
    }
    /** @var int $iter */
    $iter = 1;
    foreach ($buckets as $bucket)
    {
      foreach ($bucket as $idgrp)
      {
        $this->db->query("UPDATE grp_cedules SET ARow = $iter WHERE id = $idgrp");
        $iter++;
      }
    }
  }
  /**
   * Assigne un groupe de ville a un superviseur (drop sur la ligne
   * separatrice). Le groupe est place a la fin de la bande du superviseur.
   *
   * @param int $idgrp
   * @param int $idsup
   * @param string $date
   *
   * @return void
   */
  public function SetVilleSuperviseur($idgrp, $idsup, $date)
  {
    $this->EnsureSuperviseurColumn();
    /** @var string $dte */
    $dte = $this->db->escape($date);
    /** @var string $strSQL */
    $strSQL = "SELECT id, ACol, TypeCedule FROM grp_cedules WHERE id = ".intval($idgrp)." AND datejour = $dte";
    $grp = $this->db->query($strSQL)->getRow(0);
    if (! $grp) return;
    /** @var int $maxRow */
    $maxRow = $this->db->query("SELECT IFNULL(MAX(ARow), 0) + 1 AS mx FROM grp_cedules
                  WHERE datejour = $dte AND ACol = ".$this->db->escape($grp->ACol)."
                    AND TypeCedule = ".$this->db->escape($grp->TypeCedule))->getRow(0)->mx;
    $this->db->query("UPDATE grp_cedules SET idsuperviseur = ".((intval($idsup) == 0)? 'NULL' : intval($idsup)).", ARow = $maxRow WHERE id = ".intval($idgrp));
    $this->RenumberColumnDisplay($date, $grp->ACol, $grp->TypeCedule);
  }
  /**
   * Deplace un groupe de ville a la position d'un autre groupe (drop d'une
   * ville sur une autre ville): adopte le superviseur de la cible et, si la
   * section est la meme, se place dans la colonne de la cible a sa position.
   *
   * @param int $idgrp
   * @param int $idgrpTarget
   * @param string $date
   *
   * @return void
   */
  public function MoveVilleToGroupPosition($idgrp, $idgrpTarget, $date)
  {
    $this->EnsureSuperviseurColumn();
    /** @var string $dte */
    $dte = $this->db->escape($date);
    /** @var object $drag */
    $drag = $this->db->query("SELECT id, ACol, ARow, TypeCedule, IFNULL(idsuperviseur,0) AS idsup
                              FROM grp_cedules WHERE id = ".intval($idgrp)." AND datejour = $dte")->getRow(0);
    /** @var object $tgt */
    $tgt = $this->db->query("SELECT id, ACol, ARow, TypeCedule, IFNULL(idsuperviseur,0) AS idsup
                              FROM grp_cedules WHERE id = ".intval($idgrpTarget)." AND datejour = $dte")->getRow(0);
    if ((! $drag) or (! $tgt) or ($drag->id == $tgt->id)) return;
    /** @var int $idsup */
    $idsup = intval($tgt->idsup);
    $this->db->query("UPDATE grp_cedules SET idsuperviseur = ".(($idsup == 0)? 'NULL' : $idsup)." WHERE id = ".intval($idgrp));
    if ($drag->TypeCedule != $tgt->TypeCedule)
    {
      // Section differente (REAR END vs ROBOT): assignation seulement.
      $this->RenumberColumnDisplay($date, $drag->ACol, $drag->TypeCedule);
      return;
    }
    /** @var int $oldCol */
    $oldCol = $drag->ACol;
    if ($drag->ACol != $tgt->ACol)
    {
      $this->db->query("UPDATE grp_cedules SET ACol = ".intval($tgt->ACol)." WHERE id = ".intval($idgrp));
    }
    /** @var array $sups */
    $sups = $this->GetSuperviseursVisibles($date);
    /** @var array $buckets */
    $buckets = array(0 => array(), 1 => array(), 2 => array());
    $result = $this->db->query("SELECT id, ARow, IFNULL(idsuperviseur,0) AS idsup FROM grp_cedules
                                  WHERE datejour = $dte AND ACol = ".intval($tgt->ACol)."
                                    AND TypeCedule = ".intval($tgt->TypeCedule)."
                                    AND id <> ".intval($idgrp)."
                                  ORDER BY ARow, id");
    foreach ($result->getResult() as $row)
    {
      $buckets[$this->SupBandIndex($row->idsup, $sups)][] = $row->id;
    }
    // Insere le groupe deplace juste avant la cible dans sa bande.
    /** @var array $newOrder */
    $newOrder = array();
    foreach ($buckets as $bucket)
    {
      foreach ($bucket as $gid)
      {
        if ($gid == $tgt->id) $newOrder[] = intval($idgrp);
        $newOrder[] = intval($gid);
      }
    }
    /** @var int $iter */
    $iter = 1;
    foreach ($newOrder as $gid)
    {
      $this->db->query("UPDATE grp_cedules SET ARow = $iter WHERE id = $gid");
      $iter++;
    }
    if ($oldCol != $tgt->ACol)
    {
      $this->RenumberColumnDisplay($date, $oldCol, $drag->TypeCedule);
    }
  }
  /**
   * Reordonne un groupe a l'interieur de sa bande superviseur
   * (rang local a la bande affiche par le select oprang).
   *
   * @param int $idgrp
   * @param int $oldrang
   * @param int $newrang
   * @param string $date
   *
   * @return void
   */
  public function ReorderVilleRangSup($idgrp, $oldrang, $newrang, $date)
  {
    $this->EnsureSuperviseurColumn();
    /** @var string $dte */
    $dte = $this->db->escape($date);
    /** @var object $grp */
    $grp = $this->db->query("SELECT id, ACol, TypeCedule, IFNULL(idsuperviseur,0) AS idsup
                              FROM grp_cedules WHERE id = ".intval($idgrp)." AND datejour = $dte")->getRow(0);
    if (! $grp) return;
    /** @var array $sups */
    $sups = $this->GetSuperviseursVisibles($date);
    /** @var int $band */
    $band = $this->SupBandIndex($grp->idsup, $sups);
    /** @var array $bandIds */
    $bandIds = array();
    $result = $this->db->query("SELECT id, IFNULL(idsuperviseur,0) AS idsup FROM grp_cedules
                                  WHERE datejour = $dte AND ACol = ".intval($grp->ACol)."
                                    AND TypeCedule = ".intval($grp->TypeCedule)."
                                  ORDER BY ARow, id");
    foreach ($result->getResult() as $row)
    {
      if ($this->SupBandIndex($row->idsup, $sups) == $band)
      {
        $bandIds[] = intval($row->id);
      }
    }
    /** @var int $pos */
    $pos = array_search(intval($idgrp), $bandIds);
    if ($pos === false) return;
    array_splice($bandIds, $pos, 1);
    array_splice($bandIds, max(0, intval($newrang) - 1), 0, array(intval($idgrp)));
    // Reecrit l'ordre complet de la colonne: la bande modifiee garde ses
    // positions relatives, puis renumero dans l'ordre d'affichage.
    /** @var array $buckets */
    $buckets = array(0 => array(), 1 => array(), 2 => array());
    $result = $this->db->query("SELECT id, IFNULL(idsuperviseur,0) AS idsup FROM grp_cedules
                                  WHERE datejour = $dte AND ACol = ".intval($grp->ACol)."
                                    AND TypeCedule = ".intval($grp->TypeCedule)."
                                  ORDER BY ARow, id");
    /** @var int $bandIter */
    $bandIter = 0;
    foreach ($result->getResult() as $row)
    {
      if ($this->SupBandIndex($row->idsup, $sups) == $band)
      {
        $buckets[$band][] = $bandIds[$bandIter];
        $bandIter++;
      }
      else
      {
        $buckets[$this->SupBandIndex($row->idsup, $sups)][] = intval($row->id);
      }
    }
    /** @var int $iter */
    $iter = 1;
    foreach ($buckets as $bucket)
    {
      foreach ($bucket as $gid)
      {
        $this->db->query("UPDATE grp_cedules SET ARow = $iter WHERE id = $gid");
        $iter++;
      }
    }
  }
    public function MovePolylineToFirstByAssocId($assocId)
    {
        $assocId = intval($assocId);
        if ($assocId <= 0)
            return false;

        // Démarrer transaction
        $this->db->transStart();

        // Récupérer l'association
        $row = $this->db->table('cedule_polylines')->getWhere(['id' => $assocId])->getRow();
        if (!$row) {
            $this->db->transComplete();
            return false;
        }

        $idcedule = intval($row->idcedule);

        // Décaler tous les ordres existants (+1) pour cette cédule
        // UTILISER une expression SQL non échappée pour incrémenter
        $this->db->table('cedule_polylines')->set('ordre', 'ordre + 1', false)->where('idcedule', $idcedule)->update();

        // Positionner l'association demandée à 1
        $this->db->table('cedule_polylines')->where('id', $assocId)->update(['ordre' => 1]);

        $this->db->transComplete();
        return $this->db->transStatus();
    }
  /**
   *
   * @param object $resultLine
   * @param string $dateactive
   * @param int $from
   * @param int $GrpID
   * @param array $ArrRow
   * @param int $iterRow
   *
   * @return array
   */
  private function getLignesCeduleTV($resultLine, $dateactive, $from, $GrpID, $ArrRow, &$iterRow, $padRows = true)
  {
    /** @var string $ColOne */

    /** @var string $color */

    /** @var object $row */
    foreach(((is_object($resultLine)) ? $resultLine->getResult() : $resultLine) as $row)
    {
      switch ($row->TypeDeProduit)
      {
        case 'ORGANIQUE':
          $color = '#ebccaa';
          break;
        case 'RECYCLAGE':
          $color = '#9FF3F9';
          break;
        case 'DECHETS':
          $color = '#E5E5E5';
          break;
        case 'RESIDUS_VERT':
          $color = '#2DBC96';
          break;
        case 'GROS_REBUTS':
          $color = '#2B3B7AF';
          break;
        case 'BRANCHES':
          $color = '#D1B546';
          break;
        case 'FEUILLES':
          $color = '#2DBC96';
          break;
        default:
          $color = '#F7F1C5';
      }
      $ColOne = '<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>';
      if ($GrpID != $row->idgrp)
      {
        $span = (($row->TypeCedule == 2)? '4' : '4');
        $ville ='<label id="lblville_'.$row->idvilles.'_'.$row->ACol.'_'.$row->idgrp.'" ondrop="dropligne(event,'.$row->idgrp.','.$row->TypeCedule.')" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event)" style="width: 100%;margin-left:5px;font-size: 14px;margin-top:5px;">'.
                                                            $row->nomville.'</label>';
        if (isset($ArrRow[$iterRow])) {

          $ArrRow[$iterRow] .= '<td class="align-middle" colspan="'.$span.'"   style="line-height:15px;background-color:'.$row->couleurville.' ;text-align:center;color:black;" >'.
              $ville.'</td>';
        }
        else
        {
          $ArrRow[$iterRow] = '<td class="align-middle" colspan="'.$span.'"   style="line-height:15px;background-color:'.$row->couleurville.' ;text-align:center;color:black;" >'.
            $ville.'</td>';
        }
        $GrpID = $row->idgrp;
        $iterRow++;
      }

      $cam = substr($row->NOCAMION ,0, 3);
      $backColor = '';
      $camToShow = $row->NOCAMION;
      if ($row->IsStart == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      if($row->HaveMat == 1)
      {
        $backColor = 'color:#dc143c;font-size: large;' ;
      }
      if ((($cam == '') or ($cam == 'Cam')) and (($row->nochauffeur != 0) or  ($row->HELPERS_1 != 0)) )
      {
        $backColor = 'background-color: fuchsia;' ;
        $cam = 'Cam';
      }
      else if ((($cam == '') or ($cam == 'Cam')) and ($row->nochauffeur == 0) and ($row->HELPERS_1 == 0) and ($row->Secteur != ''))
      {
        $cam = '';
        $camToShow = '';
      }
      if (isset($ArrRow[$iterRow]))
      {
        $ArrRow[$iterRow] .= '<td  width="50" id="tdcam_'.$row->NOCAMION.'_'.$row->ID.'"  align="center" style="border-width:thin;line-height:15px;width: 50px;font-weight:bolder;'.$backColor.';"><label  id="cam_'.$row->ID.'" style="width: 50px;'.$backColor.';font-size: '.((($cam == 'Cam'))? 'larger' : 'large' ).';margin-top:5px;cursor:pointer;">'.((($cam == 'Cam'))? '?' : $camToShow ).'</label></td>' ;

      }
      else{
        $ArrRow[$iterRow] = '<td width="50"  id="tdcam_'.$row->NOCAMION.'_'.$row->ID.'"  align="center" style="border-width:thin;line-height:15px;width: 50px;font-weight:bolder;'.$backColor.';"><label  id="cam_'.$row->ID.'" style="width: 50px;'.$backColor.';font-size: '.((($cam == 'Cam'))? 'larger' : 'large' ).';margin-top:5px;cursor:pointer;">'.((($cam == 'Cam'))? '?' : $camToShow ).'</label></td>' ;

      }
      //$ArrRow[$iterRow] .= '<td  id="tdcam_'.$row->NOCAMION.'_'.$row->ID.'"  align="center" style="line-height:15px;width: 50px;font-weight:bolder;'.$backColor.';"><label  id="cam_'.$row->ID.'" style="width: 50px;'.$backColor.';font-size: '.((($cam == 'Cam'))? 'larger' : 'large' ).';margin-top:5px;cursor:pointer;">'.((($cam == 'Cam'))? '?' : $camToShow ).'</label></td>' ;
      //------------------------ &nbsp;&nbsp;&nbsp;&nbsp;
      $backColor = '';
      if ($row->chaufconfirme == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      $showchauf =  '<label id="chauffeur_'.$row->ID.'" style="font-size:14px;font-weight:bold;width:120px;margin-top:5px;cursor:pointer;'.$backColor.'">
                                      ' .substr( $row->chauffeur, 0,20)  . '</label>';
      $ArrRow[$iterRow] .= '<td  align="left" id="tdchauffeur_'.$row->idchauffeurs.'_'.$row->ID.'" style="border-width:thin;line-height:15px;width: 90px;font-size: 14px;font-weight:bold;'.$backColor.'"> ' .
                                '<div class="btn-group" style="margin-left:2px;width: 120px;"> '.$showchauf.'</div></td>';
      $backColor = '';
      if ($row->helperconfirme == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      if ($row->TypeCedule == 1)
      {
        $showaide =  '<label id="aide1_'.$row->ID.'" style="font-size:14px;font-weight:bold;width:90px;margin-top:5px;cursor:pointer;'.$backColor.'">
                                      ' .substr( $row->aide1, 0,10)  . '</label>';
        $ArrRow[$iterRow] .= '<td  align="left" id="tdaide_'.$row->HELPERS_1.'_'.$row->ID.'" style="border-width:thin;line-height:15px;width: 90px;font-size: 14px;'.$backColor.
                                '"><div class="btn-group" style="margin-left:2px;width: 100px;"> ' . $showaide .
                                ((trim($row->Secteur) == '')? '' : '<br />Sect.: '.trim($row->Secteur)). '</div></td>';
        //$ArrRow[$iterRow] .= '<td style="line-height:15px;" align="center" id="tdsecteur_'.$row->Secteur.'_'.$row->ID.'" >' .
        //          '<label style="width: 120px;margin-left:5px;font-weight:bold;font-size: 14px;max-width: 120px;margin-top:5px;"  title="'.$row->Secteur.'">' .substr( $row->Secteur, 0,10) . '</label></td>';
      }
      else
      {
        $ArrRow[$iterRow] .= '<td style="border-width:thin;line-height:15px;"  align="center" >' .
                  '<label  id="aide1_'.$row->ID.'" style="width: 100px;font-weight:bold;margin-left:5px;font-size: 14px;max-width: 120px;margin-top:5px;" title="'.$row->Secteur.'" >' .substr( $row->Secteur, 0,15) . '</label></td>';
      }
      if ($from == 1 or $from == 4)
      {
        $showproduit = '<select class="form-control" onchange="SetTypeProd(this.id,'.$dateactive.')" id="produit_'.$row->ID.'" style="font-size:medium;font-weight:bold;width:90%;background-color:'.$color.'">
                                  <option value="DECHETS">Choisir</option>' .$this->GetAllProduits($row->TypeDeProduit) . '</select>';
        $ArrRow[$iterRow] .= '<td id="trproduit_'.$row->ID.'" class="cellule" align="center" style="background-color:'.$color.';" >' .$showproduit.'</td>';
      }
      else
      {
        $ArrRow[$iterRow] .= '<td class="cellule" align="center" style="background-color:'.$color.';" >' .
                  '<label style="width: 100px;margin-left:5px;background-color:'.$color.';font-size: 14px;">' .$row->TypeDeProduit . '</label></td>';
      }
      $iterRow++;
    }
    if ($padRows and isset($ColOne))
    {
      if ($iterRow < 60)
      {
        for ($iter = $iterRow; $iter <= 40; $iter++)
        {
          if (isset($ArrRow[$iter]))
          {
            $ArrRow[$iter]  .= $ColOne;
          }
          else
          {
            $ArrRow[$iter] = $ColOne;
          }
        }
      }
    }

    return $ArrRow;
  }
  /**
   *
   * @param string $produit
   *
   * @return string
   */
  public function GetAllProduits($produit)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom FROM typededechet order by Nom";
    $typededechet = $this->db->query($strSQL);
    foreach($typededechet->getResult() as $row)
    {
      $selected = (($produit == $row->Nom)? ' selected ' : '');
      $returnval .= '<option value="'.$row->Nom.'" '.$selected .'>'.$row->Nom.'</option>';
    }
    return $returnval;
  }
  /**
   *
   * @param int $idmat
   *
   * @return string
   */
  public function GetAllMatieres($idmat)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT id, Nom FROM typededechet order by Nom";
    $typededechet = $this->db->query($strSQL);
    foreach($typededechet->getResult() as $row)
    {
      $selected = (($idmat == $row->id)? ' selected ' : '');
      $returnval .= '<option value="'.$row->id.'" '.$selected .'>'.$row->Nom.'</option>';
    }
    return $returnval;
  }
  private function InsertHelperForToday()
  {
    // Un seul INSERT ... SELECT : ajoute les aides de la journee absents
    // de suivis (avant : 1 SELECT + jusqu'a N SELECT/INSERT par aide).
    $strSQL = "INSERT INTO suivis (datejour, idrh)
               SELECT DISTINCT CURDATE(), c.HELPERS_1
               FROM cedules c
               WHERE c.DateJour = CURDATE()
                 AND c.HELPERS_1 IS NOT NULL
                 AND c.HELPERS_1 > 0
                 AND NOT EXISTS (
                       SELECT 1 FROM suivis s
                       WHERE s.datejour = CURDATE()
                         AND s.idrh = c.HELPERS_1)";
    $this->db->query($strSQL);
  }
    /**
     * Récupère les polylines pour un secteur/ville donné sous forme d'options HTML
     * @param int $idville
     * @param int $idsecteur
     * @param int $selectedId ID du polyline actuellement sélectionné (optionnel)
     * @return string HTML options
     */
    public function GetOptPolylinesBySecteurVille($idville, $idsecteur = 0, $selectedId = 0)
    {
        $returnval = '<option value="0">-- Sélectionner un parcours --</option>';

        if ($idville <= 0) {
            return $returnval;
        }

        $idville = $this->db->escape($idville);
        $whereClause = "WHERE idville = $idville";

        // Si un secteur est spécifié, filtrer par secteur
        if ($idsecteur > 0) {
            $idsecteur = $this->db->escape($idsecteur);
            $whereClause .= " AND idsecteur = $idsecteur";
        }

        $strSQL = "SELECT id, nom, idsecteur
               FROM polilyne_ville 
               $whereClause
               ORDER BY nom";

        $query = $this->db->query($strSQL);

        foreach ($query->getResult() as $row) {
            $selected = ($row->id == $selectedId) ? ' selected' : '';
            $secteurInfo = ($row->idsecteur > 0) ? ' (Secteur: ' . $this->getNomSecteurById($row->idsecteur) . ')' : '';
            $returnval .= '<option value="' . $row->id . '"' . $selected . '>'
                . htmlspecialchars($row->nom) . $secteurInfo
                . '</option>';
        }

        return $returnval;
    }
    /**
     * Génère les checkboxes de polylines pour un secteur
     * @param int $idville
     * @param int $idsecteur
     * @param int $idcedule
     * @return string HTML
     */
    public function GetPolylineCheckboxes($idville, $idsecteur, $idcedule)
    {
        if (!$idsecteur || $idsecteur <= 0) {
            return '<p class="text-muted">Sélectionnez d\'abord un secteur</p>';
        }

        // NE PAS échapper $idcedule ici car il sera utilisé dans le HTML/JavaScript
        $idville_escaped = $this->db->escape($idville);
        $idsecteur_escaped = $this->db->escape($idsecteur);
        $idcedule_escaped = $this->db->escape($idcedule);

        // Récupérer les polylines du secteur
        $strSQL = "SELECT pv.id, pv.nom
               FROM polilyne_ville pv
               WHERE pv.idville = $idville_escaped
                 AND pv.idsecteur = $idsecteur_escaped
                 AND pv.isdeleted = 0
               ORDER BY pv.nom";

        $query = $this->db->query($strSQL);

        if ($query->getNumRows() == 0) {
            return '<p class="text-muted">Aucun parcours disponible pour ce secteur</p>';
        }

        // Récupérer les polylines déjà sélectionnées
        $strSQL2 = "SELECT idpolyline FROM cedule_polylines WHERE idcedule = $idcedule_escaped";
        $selected = $this->db->query($strSQL2);
        $selectedIds = [];

        if ($selected) {
            foreach ($selected->getResult() as $row) {
                $selectedIds[] = $row->idpolyline;
            }
        }

        $html = '';
        foreach ($query->getResult() as $row) {
            $checked = in_array($row->id, $selectedIds) ? ' checked' : '';

            // IMPORTANT : Utiliser $idcedule (non échappé) pour JavaScript
            $html .= '<div class="checkbox">
            <label>
                <input type="checkbox" 
                       class="polyline-checkbox"
                       value="' . $row->id . '" 
                       data-idcedule="' . (int) $idcedule . '"
                       data-idpolyline="' . $row->id . '"
                       ' . $checked . '
                       onchange="togglePolyline(' . (int) $idcedule . ', ' . $row->id . ', this.checked)">
                <i class="fa fa-map-marker" style="color:#4a90e2;"></i> ' . htmlspecialchars($row->nom) . '
            </label>
        </div>';
        }

        return $html;
    }
    /**
     * Récupère le nom d'un secteur par son ID
     * @param int $idsecteur
     * @return string
     */
    private function getNomSecteurById($idsecteur)
    {
        if ($idsecteur <= 0)
            return '';

        $idsecteur = $this->db->escape($idsecteur);
        $strSQL = "SELECT Nom FROM secteurs WHERE id = $idsecteur";
        $query = $this->db->query($strSQL);

        if ($query->getNumRows() > 0) {
            $row = $query->getRow();
            return $row->Nom;
        }

        return '';
    }
    /**
     * Récupère les polylines associées à une cédule
     * @param int $idcedule
     * @return array
     */
    public function GetAllPolylinesVille($idville)
    {
        $idville = $this->db->escape($idville);

        $strSQL = "SELECT cp.id, cp.idpolyline, cp.ordre, pv.nom, pv.parcours
               FROM cedule_polylines cp
               INNER JOIN polilyne_ville pv ON cp.idpolyline = pv.id
               WHERE cp.idcedule = $idville
               ORDER BY cp.ordre";

        $query = $this->db->query($strSQL);
        return $query->getResult();
    }
    /**
     * Récupère les polylines associées à une cédule
     * @param int $idcedule
     * @return array
     */
    public function GetPolylinesByCedule($idcedule)
    {
        $idcedule = $this->db->escape($idcedule);

        $strSQL = "SELECT cp.id, cp.idpolyline, cp.ordre, pv.nom, pv.parcours
               FROM cedule_polylines cp
               INNER JOIN polilyne_ville pv ON cp.idpolyline = pv.id
               WHERE cp.idcedule = $idcedule
               ORDER BY cp.ordre";

        $query = $this->db->query($strSQL);
        return $query->getResult();
    }

    /**
     * Vérifie si une polyline est sélectionnée pour une cédule
     * @param int $idcedule
     * @param int $idpolyline
     * @return bool
     */
    public function IsPolylineSelected($idcedule, $idpolyline)
    {
        $idcedule = $this->db->escape($idcedule);
        $idpolyline = $this->db->escape($idpolyline);

        $strSQL = "SELECT COUNT(*) as count 
               FROM cedule_polylines 
               WHERE idcedule = $idcedule 
                 AND idpolyline = $idpolyline";

        $result = $this->db->query($strSQL)->getRow();
        return ($result->count > 0);
    }
  /**
   *
   * @param string $dateactive
   * @param int $from
   *
   * @return string
   */
  public function LoadCedules ($dateactive, $from, $activedrop = 1)
	{
		//----------------------------------------------------------------------
    // Filtre original restauré tel quel.
    // Le problème de "ville qui disparaît" est géré au niveau de la vue (today.php)
    // qui vérifie les villes manquantes après le chargement des cédules.
    $showallvilles = $this->input->post_get('showallvilles');
    $allvilles = (($showallvilles == 1)? '' : "AND (IFNULL(IF(c.idchauffeurs = 0, NULL, c.idchauffeurs),
												  IFNULL(if(c.NOCAMION = '', NULL, c.NOCAMION), ifnull(if(c.HELPERS_1 = 0, NULL, c.HELPERS_1),
                                        if(c.Secteur = '', NULL, c.Secteur))))) IS NOT NULL" );
    $activedate = $dateactive;
    $dateactive = $this->db->escape($dateactive);
    $limit = (($this->input->post_get('limit') > 0)? ' and TypeCedule = '.$this->db->escape($this->input->post_get('limit')) : '' );
    /** @var int $GrpID */
    $GrpID = 0;
    /** @var array $ArrRow */
    $ArrRow = array();
    /** @var string $ColOne */
    $ColOne = '<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
                  <td class="cellule" align="center">&nbsp;</td>
                  <td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>';
    /** @var string $ColOneRobot */
    $ColOneRobot = '<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
                  <td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>';
    if ($from == 3)
    {
      $ColOne = '<td  width="50" align="center">&nbsp;</td>
                    <td  width="120" align="center">&nbsp;</td>
    							  <td  width="120" align="center">&nbsp;</td>';
      $ColOneRobot = $ColOne;
    }
    /** @var bool $IsSet */
    $IsSet = false;
    /** @var string $returnval */
    $returnval = '<table border="2" style="width: 100%;"
							 id="tab_logic"><tr  style="color:black;text-align:center;line-height:20px;">';
    /** @var string $thCedule1 */
    $thCedule1 = '';
    /** @var string $thCedule2 */
    $thCedule2 = '';
    /** @var string $NbRow */
    $NbRow = 0;
    /** @var string $nbCol */
    $nbCol = 0;
    /** @var string $NbColTotal */
    $NbColTotal = 0;
    /** @var string $strSQL */
    $strSQL = "SELECT DISTINCT TypeCedule FROM grp_cedules WHERE datejour = $dateactive  $limit  ORDER BY TypeCedule;" ;
    /** @var object $resultCedule */
    $resultCedule = $this->db->query($strSQL);
    //------------------------------------------------------------------
    // Bandes superviseurs (deux lignes separatrices couvrant REAR END et
    // ROBOT). Inactif pour l'affichage TV (from 3/8).
    /** @var bool $useBands */
    $useBands = ($from != 3);
    /** @var array $sups */
    $sups = array();
    /** @var array $colBands */
    $colBands = array();
    /** @var int $totalCells */
    $totalCells = 0;
    if ($useBands)
    {
      $this->EnsureSuperviseurColumn();
      $sups = $this->GetSuperviseursVisibles($activedate);
    }
    /** @var object $typecedule */
    foreach($resultCedule->getResult() as $typecedule)
    {
      $strSQL = "SELECT DISTINCT ifnull(ACol, 1) as ACol FROM grp_cedules WHERE datejour = $dateactive and TypeCedule = ".$typecedule->TypeCedule."  ORDER BY ACol;" ;
    /** @var object $result */
    $result = $this->db->query($strSQL);
    $nbCol = $result->getNumRows() ;
    $NbColTotal += $nbCol;
    if ($from == 3)
    {
      $nbcolcedule = (($typecedule->TypeCedule == 1)? 3: 3);
    }
    else
    {
      $nbcolcedule = (($typecedule->TypeCedule == 1)? 7: 6);
    }
    $totalCells += $nbCol * $nbcolcedule;
    $thCedule1 .= '<th colspan="'.(7 * $nbCol). '" style="background-color: '.(($typecedule->TypeCedule == 1)? '#093' : 'red').';line-height:25px;text-align:center;color: white;'.(($typecedule->TypeCedule == 2)? 'border-left-color:red;border-left-style:solid;border-left-width:5px;' : '').'" >'.(($typecedule->TypeCedule == 1)? 'REAR END' : 'ROBOT').'</th>';

    /** @var object $row */
    foreach($result->getResult() as $row)
      {
        //$thCedule2 .= (($from == 3 or $from == 5)? '<th width="60" style="color:white;text-align:center;width: 10px;'.(($typecedule->TypeCedule == 2)? 'border-left-color:red;border-left-style:solid;border-left-width:5px;' : '').'"></th>' :
        //      '<th width="60" style="color:white;text-align:center;width: 60px;'.(($typecedule->TypeCedule == 2)? 'border-left-color:red;border-left-style:solid;border-left-width:5px;' : '').'"></th>' ) .'
        //      <th width="60" style="color:white;text-align:center;"width: 60px;><label style="width: 60px;">CAM.</label></th>
        //      <th width="150" style="color:white;text-align:center;width: 350px;"><label style="width: 250px;">CHAUFFEUR</label></th>
        //      <th width="60" style="color:white;text-align:center;width: 350px;"><label style="width: 250px;">'.(($typecedule->TypeCedule == 1)? 'AIDE' : 'SECTEUR').'</label></th>
        //      <th width="50" style="color:white;text-align:center;width: 100px;"><label style="width: 100px;">Ramassage</label></th>';

        if ($from == 3)
        {
          $thCedule2 .=
              '<td width="50" class="align-middle"  style="text-align:center;"width: 50px;><label style="width: 50px;margin-top:5px;">CAM</label></td>
              <td width="120" class="align-middle" style="width: 120px;"><label style="width: 120px;font-size=:13px;margin-top:5px;">ROUTE</label></td>
              <td width="120" class="align-middle" style="width: 120px;"><label style="width: 120px;font-size=:13px;margin-top:5px;">CHAUFFEUR</label></td>
              '.(($typecedule->TypeCedule == 1)? '<th valign="middle" width="120" style="text-align:center;width: 120px;"><label style="width: 120px;font-size=:13px;margin-top:5px;">AIDE</label></td>' : '<th class="align-middle" width="120" style="text-align:center;width: 120px;">
<label style="width: 120px;font-size=:13px;margin-top:5px;">SECTEUR</label></th>')
              .'<th width="50" style="color:white;text-align:center;width: 100px;"><label style="width: 100px;">MATIÈRE</label></th>';

        }
        else
        {
          $thCedule2 .= (($from == 4 or $from == 6)? '<th width="60" style="color:white;text-align:center;width: 10px;'.(($typecedule->TypeCedule == 2)? 'border-left-color:red;border-left-style:solid;border-left-width:5px;' : '').'"></th>' :
                '<th width="60" style="color:white;text-align:center;width: 60px;'.(($typecedule->TypeCedule == 2)? 'border-left-color:red;border-left-style:solid;border-left-width:5px;' : '').'"></th>' ) .'
						  <th width="60" style="color:white;text-align:center;"width: 60px;><label style="width: 60px;">CAM.</label></th>
						  <th width="150" style="color:white;text-align:center;width: 350px;"><label style="width: 250px;">CHAUFFEUR</label></th>';
          if ($typecedule->TypeCedule == 1)
          {
            $thCedule2 .= '<th width="60" style="color:white;text-align:center;width: 350px;"><label style="width: 250px;">'.(($typecedule->TypeCedule == 1)? 'AIDE' : 'SECTEUR').'</label></th>';
          }
          $thCedule2 .= '<th width="220"style="color:white;text-align:center;width: 220px;"><label style="width: 250px;" >PARCOURS</th>       <!-- NOUVELLE COLONNE -->						  
						  <th width="50" style="color:white;text-align:center;width: 100px;"><label style="width: 100px;">MATIÈRE</label></th>';

        }

        $iterRow = 0;
        $strSQL = "SELECT c.NOCAMION, c.IsStart,c.ID,g.datejour,g.idvilles,ACol,ARow, c.HELPERS_1, c.idchauffeurs,
                  c.idpolyline,
                  c.idsecteur,
                  if((SELECT suiviscamion.id FROM suiviscamion WHERE suiviscamion.nocamion = c.NOCAMION AND suiviscamion.idstatut = 2 AND
                   suiviscamion.dateend is NULL and suiviscamion.datestart <= $dateactive LIMIT 0,1 ) > 0, 1, 0) AS HaveMat,
                  (SELECT MAX(ARow) as nb from grp_cedules where grp_cedules.datejour = $dateactive AND grp_cedules.ACol = " . $row->ACol . "  and typecedule = " . $typecedule->TypeCedule . ") AS nbRow,

                        (SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id  AND NOCAMION <> 'Cam' AND NOCAMION <> '' AND NOCAMION IS NOT NULL ) as ActiveNbCam,
                        (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
												  (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
													  sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dateactive
													  and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
													  ( Select CONCAT (TRIM(Nom), ',', TRIM(Prenom) ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
													  CONCAT(IFNULL(( Select CONCAT (Nom, ', ', left(Prenom, 1)) FROM reshumaine where id = c.HELPERS_1 ), ''),
													  IFNULL(( Select CONCAT (' / ',Nom, ', ', left(Prenom, 1)) FROM reshumaine where id = c.HELPERS_2 ), ''),
													  IFNULL(( Select CONCAT (' / ',Nom, ', ', left(Prenom, 1)) FROM reshumaine where id = c.HELPERS_3 ),''),
													  IFNULL(( Select CONCAT (' / ',Nom, ', ', left(Prenom, 1)) FROM reshumaine where id = c.HELPERS_4 ), '')) As aide1,
													  IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
													  IFNULL(( Select colorhex FROM ville where id = g.idvilles ), -1)  As couleurville,
													  g.id as idgrp, g.TypeCedule, c.ID, c.chaufconfirme, c.helperconfirme, c.IsLocked,
													  c.IsLockedChauffeur,Secteur, pv.nom AS nom_polyline,
													  c.TypeDeProduit, ifnull(c.idchauffeurs, 0) AS nochauffeur, ifnull(c.HELPERS_1, 0) AS noaide,
													  ".(($useBands)? 'ifnull(g.idsuperviseur, 0)' : '0')." AS idsup
											  from grp_cedules g  LEFT JOIN cedules c  ON  c.idgrp = g.id
                                                        LEFT JOIN polilyne_ville pv ON c.idpolyline = pv.id
											  WHERE g.datejour = $dateactive and ( g.TypeCedule = " . $typecedule->TypeCedule . " )
                      $allvilles
											  and ACol = " . $row->ACol . " Order by  g.ARow, g.id, c.ID";
        /** @var object $resultLine */
        $resultLine = $this->db->query($strSQL);
        if ($NbRow < ( $resultLine->getNumRows() + 40) )
          $NbRow = 40 ;
        if ($from == 3)
        {
          $ArrRow = $this->getLignesCeduleTV($resultLine, $dateactive, $from, $GrpID, $ArrRow, $iterRow);
        }
        else
        {
          // Repartit les lignes de la colonne par bande superviseur
          // (0 = superviseur 1, 1 = superviseur 2, 2 = non assigne).
          /** @var array $bands */
          $bands = array(0 => array(), 1 => array(), 2 => array());
          foreach ($resultLine->getResult() as $ligne)
          {
            $bands[$this->SupBandIndex($ligne->idsup, $sups)][] = $ligne;
          }
          // Le remplissage doit respecter le nombre de cellules de la
          // section: 7 pour REAR END, 6 pour ROBOT (sinon les lignes
          // remplies sont decalees d'une colonne).
          $colBands[] = array('bands' => $bands,
            'colone' => (($typecedule->TypeCedule == 2) ? $ColOneRobot : $ColOne));
        }



      }
    }
    //---------------------------------------------------------------------------------------------------------------------------------------
    if ($useBands)
    {
      // Emission en 2e passe: les bandes superviseurs sont alignees sur
      // toutes les colonnes (les lignes separatrices occupent toute la
      // largeur du tableau).
      /** @var array $bandMax */
      $bandMax = array(0, 0, 0);
      foreach ($colBands as $col)
      {
        foreach ($col['bands'] as $b => $rows)
        {
          // Chaque groupe de ville occupe 1 ligne d'entete + 1 ligne par
          // ligne cedule (camion/chauffeur): la bande doit compter les deux.
          /** @var int $emitted */
          $emitted = 0;
          /** @var int $prevGrp */
          $prevGrp = -1;
          foreach ($rows as $ligne)
          {
            $emitted++;
            if ($ligne->idgrp != $prevGrp) $emitted++;
            $prevGrp = $ligne->idgrp;
          }
          if ($bandMax[$b] < $emitted) $bandMax[$b] = $emitted;
        }
      }
      // Conserve une hauteur minimale comme l'ancien remplissage a 26 lignes.
      /** @var int $totalRows */
      $totalRows = $bandMax[0] + $bandMax[1] + $bandMax[2];
      if ($totalRows < 26) $bandMax[2] += 26 - $totalRows;
      /** @var array $bandStart */
      $bandStart = array(0, $bandMax[0], $bandMax[0] + $bandMax[1]);
      $ArrRow = array();
      for ($iter = 0; $iter < $bandMax[0] + $bandMax[1] + $bandMax[2]; $iter++)
      {
        $ArrRow[$iter] = '';
      }
      foreach ($colBands as $col)
      {
        /** @var string $colOneCol */
        $colOneCol = $col['colone'];
        foreach ($col['bands'] as $b => $rows)
        {
          /** @var int $iterRow */
          $iterRow = $bandStart[$b];
          if (count($rows) > 0)
          {
            // Rang local a la bande pour le select oprang (updTableRang).
            /** @var int $rank */
            $rank = 0;
            /** @var int $prevGrp */
            $prevGrp = -1;
            /** @var int $grpCount */
            $grpCount = 0;
            foreach ($rows as $ligne)
            {
              if ($ligne->idgrp != $prevGrp) $grpCount++;
              $prevGrp = $ligne->idgrp;
            }
            $prevGrp = -1;
            foreach ($rows as $ligne)
            {
              if ($ligne->idgrp != $prevGrp) $rank++;
              $prevGrp = $ligne->idgrp;
              $ligne->ARow = $rank;
              $ligne->nbRow = $grpCount;
            }
            $GrpID = 0;
            // from = 8 (page TV): meme rendu que la page de travail,
            // mais sans edition (voir getLignesCedule).
            $ArrRow = $this->getLignesCedule($rows, $dateactive, $from, $GrpID, $ArrRow, $activedrop, $iterRow, false);
          }
          // Remplit le reste de la bande pour cette colonne.
          for ($iter = $iterRow; $iter < $bandStart[$b] + $bandMax[$b]; $iter++)
          {
            $ArrRow[$iter] .= $colOneCol;
          }
        }
      }
      // Colonnes sans aucune ligne: remplissage complet.
      if (count($colBands) == 0)
      {
        for ($iter = 0; $iter < count($ArrRow); $iter++)
        {
          $ArrRow[$iter] = $ColOne;
        }
      }
    }
    else
    {
      for ($iter = 0; $iter < count($ArrRow); $iter++)
      {
        if (strlen( $ArrRow[$iter]) == strlen(  $ColOne ) )
        {
          for ($iterCol = 1; $iterCol < $nbCol; $iterCol++)
      			$ArrRow[$iter] .= $ColOne;
        }
      }
    }
    $returnval .= $thCedule1.'</tr>'.
            '<tr class="bg-primary" style="color:white;text-align:center;line-height:25px;font-size:medium;">'.$thCedule2.'</tr>';
    if ($useBands)
    {
      // Convention de largeur: 7 cellules/colonne par section
      // (meme convention que la ligne d'en-tete).
      /** @var int $sepSpan */
      $sepSpan = 7 * $NbColTotal;
      /** @var array $sepRows */
      $sepRows = array();
      foreach ($sups as $supIdx => $sup)
      {
        // Ligne separatrice: bleu plus pale que l'ancien #16417c, nom du
        // superviseur en majuscules, rouge, centre horizontalement.
        $sepRows[$supIdx] = '<tr class="sup-separateur"><td colspan="'.$sepSpan.'" '.
          (($from == 8) ? '' : 'ondrop="dropSupVille(event,'.$sup['id'].')" ondragover="allowDrop(event)" ').
          'style="background-color:#5b9bd5;color:#fff;font-weight:bold;font-size:15px;line-height:24px;'.
          'text-align:center;vertical-align:middle;border-top:3px solid #000;position:relative;">'.
          '<i class="fa fa-user" style="margin-right:8px;"></i>SUPERVISEUR&nbsp;:&nbsp;'.
          '<span style="color:red;text-transform:uppercase;">'.$sup['nom'].'</span>'.
          (($from == 8) ? '' : '<span style="position:absolute;right:15px;top:0;font-weight:normal;font-size:11px;">'.
          'glisser une ville ici pour l\'assigner</span>').'</td></tr>';
      }
      for ($iter = 0; $iter < count($ArrRow); $iter++)
      {
        foreach ($sepRows as $supIdx => $sepRow)
        {
          if ($iter == $bandStart[$supIdx]) $returnval .= $sepRow;
        }
        $returnval .= '<tr  class="rangee" style="border-width:thin;line-height:15px;" >'.$ArrRow[$iter].'</tr>';
      }
      if (count($ArrRow) == 0)
      {
        foreach ($sepRows as $sepRow) $returnval .= $sepRow;
      }
    }
    else
    {
      for ($iter = 0; $iter <count($ArrRow); $iter++)
      {
        $returnval .= '<tr  class="rangee" style="border-width:thin;line-height:15px;" >'.$ArrRow[$iter].'</tr>';
      }
    }
    $strSQL = "SELECT (SELECT COUNT(cedules.NOCAMION) FROM cedules
                   WHERE (cedules.DateJour = $dateactive)  and ( trim(NOCAMION) != '' OR trim(NOCAMION) != NULL ) AND
						        cedules.NOCAMION IN (SELECT camions.NoCamion FROM camions WHERE camions.TypeCamion = 1) ) AS nbRearEnd,
                (SELECT COUNT(cedules.NOCAMION) FROM cedules
                  WHERE (cedules.DateJour = $dateactive)  and ( trim(NOCAMION) != '' OR trim(NOCAMION) != NULL )  AND
						    cedules.NOCAMION IN (SELECT camions.NoCamion FROM camions WHERE camions.TypeCamion = 2) ) AS nbRobot,
                (SELECT COUNT(cedules.NOCAMION) FROM cedules
                WHERE (cedules.DateJour = $dateactive)  and ( trim(NOCAMION) != '' OR trim(NOCAMION) != NULL )  AND
					      cedules.NOCAMION IN (SELECT camions.NoCamion FROM camions WHERE camions.TypeCamion IN (1,2))    ) AS nbAllCam" ;
    $nbCam = $this->db->query($strSQL)->getRow(0);
    $this->data['spRearEnd'] = $nbCam->nbRearEnd;
    $this->data['nbRobot'] = $nbCam->nbRobot;
    $this->data['nbAllCam'] = $nbCam->nbAllCam;
    $calcul = '<td style="background-color:aqua;font-weight:bold;font-size:14px;color:black;width: 80px;"><span id="spRearEnd">
                  REAR END &nbsp;&nbsp;'.$nbCam->nbRearEnd.'</span></td>
                <td style="background-color:chocolate;font-weight:bold;font-size:14px;color:black;width: 80px;"><span id="spRobot">
                  ROBOT &nbsp;&nbsp;'.$nbCam->nbRobot.'</span></td>';
    //$menu = '	<div style="text-align:center; margin:auto; float:none;">
    //          <a class="form-control btn-primary" style="cursor:pointer;" id="showMenu" onclick="PopMenu()" data-trigger="click" data-placement="bottom" tabindex="0" title="Menu" data-html="true" data-content=\'
    //          <div>
    //          <ul  class="list-group">'.$this->GetMenu($activedate).'
    //          </ul></div>\'>
    //          <label  onclick="PopMenu()" style="color: white;cursor:pointer;" aria-hidden="true">Menu</label></a>
    //        </div>';
    $this->data['dropmenu'] = $this->GetMenu($activedate);
    $this->data['mnusups'] = $this->GetMenuSupCheckboxes(true, $activedate);
    $showdate = '<input type="date" style="height: 28px;font-weight:bold;
" onchange="loadToday()" value="'.$activedate.'" id="dteActivePage1" />';
    if (isset($NbColTotal))
    {
      //$tableCam = '<table  border="1" cellspacing="2" cellpadding="2"
      //           id="tab_logic"><tr  class="entete" style="background-color: #093;line-height:25px;text-align:center;">
      //           <th width="500px" style="color:white;text-align:center;"width: 500px;><label style="width: 750px;">CAMION HORS SERVICE</label></th>
      //          <th width="50%" style="color:white;text-align:center;width: 50%;"><label style="width: 750%;">CAMION AVEC MATÉRIEL</label></th></tr><tr><td>'
      //    .$this->LoadCamion(1).'</td><td>'.$this->LoadCamion(2).'</td>'.'</tr></table>';
      //$tableRollOff = '<table  border="2" cellspacing="2" cellpadding="2"  style="width: 100%;"
      //           id="tab_logic"><tr  class="entete" style="background-color: mediumaquamarine;line-height:25px;text-align:center;">
      //           <th colspan="12" style="color:white;text-align:center;"width: 900px;><label >ROLL-OFF</label></th></tr>'
      //          .$this->LoadRollOff($dateactive, $from, (5 * $NbColTotal)).'</table>';
      //-------------------------------------------------------------Requetes Ville
      /** @var string $requetes */

      //-------------------------------------------------------------
      $this->data['today'] = $returnval. '</table>';
      $nbCam->today = $returnval. '</table>';
    }
    else
    {
      $this->data['today'] = $returnval.'</table>';
    }
    $this->GetSecteursRobo();
    return  $nbCam;
}
    /**
     * Génère les options de secteurs pour le dropdown
     * @return void
     */
    private function GetSecteursRobo()
    {
        /** @var string $returnval */
        $returnval = '';
        /** @var string $strSQL */
        $strSQL = "SELECT id, Nom FROM secteurs WHERE isdeleted = 0 ORDER BY Nom";
        $secteurs = $this->db->query($strSQL);
        foreach ($secteurs->getResult() as $row) {
            $returnval .= '<option value="' . $row->Nom . '">' . $row->Nom . '</option>';
        }
        $this->data['SecteurRobo'] = $returnval;
    }
  //public function GetSecteursRobo($secteur = '')
  //{
  //  /** @var string $returnval */
  //  $returnval = '';
  //  /** @var string $strSQL */
  //  $strSQL = "SELECT id, name FROM villes_zones order BY name";
  //  $ville = $this->db->query($strSQL);
  //  foreach($ville->getResult() as $row)
  //  {
  //    $selected = (($secteur == $row->name)? ' selected ' : '');
  //    $returnval  .= '<option '.$selected.'  value="'.$row->name.'">'.$row->name.'</option>';
  //  }
  //  $this->data['SecteurRobo'] =  $returnval ;
  //}
  /**
   *
   * @param int $radvalue
   * @param string $dateactive
   *
   * @return string
   */
  public function LoadWeekCedules ($radvalue, $dateactive)
	{
		//----------------------------------------------------------------------
    $dateactive = $this->db->escape($dateactive);
    /** @var string $jour */
    $jour = '';
    /** @var string $returnval */
    $returnval = '';
    /** @var string $thCedule1 */
    $thCedule1 = '';
    /** @var string $strWhere */
    $strWhere = (($radvalue == 1)?  " = $dateactive " : " BETWEEN  DATE_ADD($dateactive,INTERVAL 1 DAY) AND DATE_ADD($dateactive,INTERVAL 6 DAY)" );
    /** @var string $thCedule2 */
    $thCedule2 = '';/** @var object $row */

    $strSQL ="SELECT  grp_cedules.datejour, if(grp_cedules.TypeCedule = 1, 'REAR END', 'ROBOT') AS TypeCed,
	                CASE DAYOFWEEK(grp_cedules.datejour)
                              WHEN 2 THEN
                              CONCAT('LUN ', DAYOFMONTH(grp_cedules.datejour))
                              WHEN 3 THEN
                              CONCAT('MAR ', DAYOFMONTH(grp_cedules.datejour))
                              WHEN 4 THEN
                              CONCAT('MER ', DAYOFMONTH(grp_cedules.datejour))
                            WHEN 5 THEN
                              CONCAT('JEU ', DAYOFMONTH(grp_cedules.datejour))
                              WHEN 6 THEN
                                CONCAT('VEN ', DAYOFMONTH(grp_cedules.datejour))
                              ELSE
                              ''
                            END AS Jour,
   	                ville.Nom AS NomVille
                    FROM grp_cedules INNER JOIN ville ON grp_cedules.idvilles = ville.id
	                WHERE grp_cedules.datejour $strWhere AND ville.Nom <> 'ROLLOFF'
	                ORDER BY DAYOFWEEK(grp_cedules.datejour) , grp_cedules.TypeCedule, grp_cedules.ARow";
    /** @var object $resultLine */
    $resultLine = $this->db->query($strSQL);
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      if ($jour != $row->Jour)
      {
        if ($returnval != '')
        {
          $thCedule2 = '</table></div></div>';
        }
        $returnval .= $thCedule2.'<div id="jour_'.$row->datejour.'" class="col-sm-2">
            <div  ondrop="dropcol()"  class="box-header with-border">
              <h3 class="box-title"><label style="font-size: 14px;">'.$row->Jour.'</label></h3>
            </div>
            <div  id="body_'.$row->Jour.'" class="box-body">
                  <table  border="1" cellspacing="2" cellpadding="2">';
        $jour = $row->Jour;
      }
      $returnval .= '<tr><td class="cellule"  style="background-color: darkblue;text-align:center;color: white;" ><label style="font-size: 14px;">'.
                              $row->NomVille. '</label></td></tr>';
    }
    return $returnval.'</table></div></div>';

	}
  /**
   *
   * @param string $dateactive
   * @param int $from
   * @param int $nbCol
   *
   * @return string
   */
  public function LoadRollOff ($dateactive, $from, $nbCol)
	{
    //----------------------------------------------------------------------
    $strSQL ="SELECT c.NOCAMION, c.IsStart,
                        (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
                        (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
                          sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dateactive
                          and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
                          ( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
                          ( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
                          IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
                          IFNULL(( Select Couleur FROM ville where id = g.idvilles ), -1)  As couleurville,
                          g.id as idgrp, c.TypeCedule, c.ID, c.chaufconfirme, c.helperconfirme, c.IsLocked,
                          c.IsLockedChauffeur,  c.STARTTIME, c.ENDTIME, c.STARTPERTE, c.ENDPERTE, c.PerteDescription,
                          c.Note, c.TypeDeProduit,c.Tonnage,  ifnull(c.idchauffeurs, 0) AS nochauffeur, ifnull(c.HELPERS_1, 0) AS noaide
                      from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
                      WHERE g.datejour = $dateactive and ( g.TypeCedule = 1 )
                      and c.TypeCedule = g.TypeCedule and  TypeDeProduit = 'COMMERCIAL'  Order by g.id";
    /** @var object $resultLine */
     $resultLine = $this->db->query($strSQL);
    if ($resultLine->getNumRows() == 0)
    {
      $SQL ="SELECT NoCamion,
                  IFNULL((SELECT suivis.idrh FROM suivis WHERE suivis.NOCAMION = camions.NoCamion AND suivis.datejour = $dateactive LIMIT 1,1), 25) AS idrh
                  FROM camions WHERE typecamion = 4 AND NoCamion <> 900";
      /** @var object $resultLine */
      $resultLine = $this->db->query($SQL);
      $idcedule = 0;
      /** @var object $row */
      foreach($resultLine->getResult() as $row)
      {
        $this->AddToCedule('\'COMMERCIAL\'',$row->NoCamion,$row->idrh,24,$dateactive, 51, $idcedule);

      }
      $resultLine = $this->db->query($strSQL);
    }
    $SQL ="SELECT * FROM suivis WHERE dtecreate > $dateactive  AND datejour = $dateactive AND idcedule = 0 and NOCAMION <> 900 ORDER BY NOCAMION";
    /** @var object $resultLine */
    $resultLine = $this->db->query($SQL);
    $idcedule = 0;
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      if ($row->idrh != 25)
      {
        /** @var string  $updSQL */
        $updSQL = "UPDATE cedules
                  SET
                    idchauffeurs= ".$row->idrh.",
                    chaufconfirme = 1
                  WHERE NOCAMION = " . $this->db->escape( $row->NOCAMION ) . " AND DateJour = $dateactive ";
        $this->db->query($updSQL);
      }
    }
		//----------------------------------------------------------------------
    /** @var string $thCedule1 */
    $thCedule1 = '<tr>';
    /** @var string $NbRow */
    /** @var string $strSQL */
    $strSQL ="SELECT c.NOCAMION, c.IsStart,c.ID,
                        (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
												(Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
													sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dateactive
													and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
													( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
													( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
													IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
													IFNULL(( Select Couleur FROM ville where id = g.idvilles ), -1)  As couleurville,
													g.id as idgrp, c.TypeCedule, c.ID, c.chaufconfirme, c.helperconfirme, c.IsLocked,
													c.IsLockedChauffeur,  c.STARTTIME, c.ENDTIME, c.STARTPERTE, c.ENDPERTE, c.PerteDescription,
													c.Note, c.TypeDeProduit,c.Tonnage,  ifnull(c.idchauffeurs, 0) AS nochauffeur, ifnull(c.HELPERS_1, 0) AS noaide
											from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
											WHERE g.datejour = $dateactive and ( g.TypeCedule = 1 )
											and c.TypeCedule = g.TypeCedule and  TypeDeProduit = 'COMMERCIAL'  Order by g.id";
    /** @var object $resultLine */
    $resultLine = $this->db->query($strSQL);
    //$thCedule1 = '<td colspan="5" class="cellule" style="background-color:mediumaquamarine;text-align:center;color:black;" ><label style="width: 100%;margin-left:5px;font-size: 16px;">ROLL-OFF</label></td>';
    /** @var string $color */
    $iter = 0;
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      if ($iter >= 6)
      {
        $thCedule1 .= '</tr><tr>';
        $iter = 0;
      }
      $thCedule1 .= '<td  class="cellule" align="center" style="width: 50px;"><label style="width: 50px;margin-left:5px;font-size: 14px;">'.$row->NOCAMION.'</label></td>' ;
      //------------------------ &nbsp;&nbsp;&nbsp;&nbsp;
      $backColor = (($row->nochauffeur == 24 or $row->nochauffeur == 25 or $row->nochauffeur == 0)? 'background-color: lightcoral;' : '');
      if (($row->nochauffeur == 24 or $row->nochauffeur != 25 and $row->nochauffeur != 0) and ($row->chaufconfirme == 0) and ($row->TypeDeProduit == 'COMMERCIAL') )
      {
        $idcedule = $row->ID;
        /** @var string  $updSQL */
        $updSQL = "UPDATE cedules
	            SET
		            chaufconfirme = 1
	            WHERE ID = " . $idcedule;
        $this->db->query($updSQL);
      }
      if ($row->chaufconfirme == 1)
      {
        $backColor = 'background-color: yellow;' ;
      }
      $checked = (($row->chaufconfirme == 1)? ' checked="checked" ' : '');
      if ($from == 3 or $from == 5)
      {
        $showchauf =  '<label style="width: 250px;margin-left:5px;">' . (($row->chauffeur == 'NON-ASSIGNÉ, Chauf.')? '' : $row->chauffeur ) . '</label';
      }
      else
      {
        $showchauf =  '<input class="btn"  style="width: 20px;height:20px;" id="chkchauf_'.$row->ID.'" '.$checked.'
                                  type="checkbox" onclick="SetStartChauf(this.id, '.$dateactive.')" />
                                  <select class="btn" onchange="SetChauffeur(this.id, '.$dateactive.')" id="chauffeur_'.$row->ID.'" style="font-size:medium;font-weight:bold;width:90%;'.$backColor.'">
                              <option value="25">Choisir</option>' .
                                    $this->GetChauffeurs( $row->nochauffeur ) . '</select>';
      }
      $thCedule1 .= '<td class="cellule" align="left" style="width: 250px;font-size: 12px;margin-left:10px;'.$backColor.'"> ' .
                                '<div class="btn-group" style="margin-left:5px;width: 250px;"> '.$showchauf.'</div></td>';
      $iter++;
    }
    return $thCedule1.'</tr>';
	}
  /**
   *
   * @param int $statut
   *
   * @return string
   */
  private function LoadCamion($statut)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT suiviscamion.id, statutcamion.Nom,
                           suiviscamion.nocamion,
                            suiviscamion.datestart,
                            suiviscamion.dateretourprevu,
                            suiviscamion.dateend,
                            suiviscamion.note,
                            suiviscamion.TypeDechet,
                            suiviscamion.QteDechet,
                            (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ) as TypeCam
                       FROM statutcamion statutcamion
                            INNER JOIN suiviscamion suiviscamion
                               ON (statutcamion.id = suiviscamion.idstatut)
                               WHERE  suiviscamion.dateend is null
                               and suiviscamion.datestart <= CURDATE()
                               AND statutcamion.id = $statut
                               order by  (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ),
                               suiviscamion.nocamion";
    $suiviscamion = $this->db->query($strSQL);
    foreach($suiviscamion->getResult() as $row) {
      if (($row->dateend == '') or (!isset($row->dateend)))
      {
        $note = str_replace('%20', ' ', $row->note);
        switch ($statut)
        {
          case 1:
            $confirmeRetour = "<a class='btn btn-danger btn-sm' style='font-size:14px;color: white;' id='btnroll_".$row->id."'
                                onclick='UpdRetour(this.id)' title='Modifier'><i class='fa fa-check-square-o fa-4' aria-hidden='true'></i>Confirmer Retour</a>";

            $message = "
                       <b> Depuis :$row->datestart <br/><br/></b>
                                             <b> Retour prévu:<br/></b>
                                              $row->dateretourprevu <br/>
                                              <b>Notes: <br/></b>
                                              $note <br/><br/><hr />
                                                $confirmeRetour";
            $returnval .= '&nbsp;&nbsp;<a data-toggle="popover" data-trigger="click" data-html="true" title="Camion: '.$row->nocamion.' Hors service"   data-placement="bottom" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;'.
                                          (($row->TypeCam == 1)? '#da1c23' : 'DarkBlue' ).'">'. $row->nocamion.'</a>
                                            <script>$(document).ready(function(){$(\'[data-toggle="tooltip_'.$row->nocamion.'"]\').tooltip();}); </script>, ' ;
            break ;
          case 2:
            $confirmeVider = "<a class='btn btn-danger btn-sm' style='font-size:14px;color: white;' id='btnroll_".$row->id."'
                                onclick='UpdViderMat(this.id)' title='Modifier'><i class='fa fa-check' aria-hidden='true'></i>&nbsp;&nbspConfirmer Vider</a>";

            $message = "
                       <b> Depuis : $row->datestart <br/><br/> </b>
                                             <b> Vidange prévu: </b> <br/>
                                              $row->dateretourprevu <br/>
                                             <b> Type de déchet: </b> <br/>
                                              $row->TypeDechet <br/>
                                              <b>Notes: <br/> </b>
                                              $note <br/><br/><hr />
                                                $confirmeVider ";
            $returnval .= '&nbsp;&nbsp;<a  id="showcammat_'.$row->nocamion.'" onclick="PopOverCamMat(\''.$row->nocamion.'\')" data-toggle="popover"  data-html="true" title="Camion: '.$row->nocamion.' Avec Matériel"   data-placement="bottom" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;'.
                                            ((strtolower( $row->TypeDechet ) == 'recyclage')? '#26F4B2' : '#254AE0' ).'">'. $row->nocamion.'</a>,' ;
            break ;
        }
      }

    }
    $returnval = substr($returnval,0,-1).'&nbsp;&nbsp;' ;
    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   *
   * @return string
   */
  private function GetMenu($dateactive)
  {

		//--------------------------------------------------------------
    $typecedule = $this->input->post_get('typecedule');
    $typecedule = ((isset($typecedule))? $this->input->post_get('typecedule') : 1);
    $activedrop = $this->input->post_get('activedrop');
    $activedrop = ((isset($activedrop))? $this->input->post_get('activedrop') : 0);
    $notab = $this->input->post_get('notab');
    $notab = ((isset($notab))? $this->input->post_get('notab') : 1);
		//--------------------------------------------------------------
    $message = '
	          <li  id="liPageDay" style="cursor:pointer;list-style:none;margin-top:10px;">
	            <a  href="javascript:ShowDivDrop()"><i class="fa fa-bars" style="font-size:24px;"></i>&nbsp;&nbsp;Onglet création</a>
	          </li>
	          <li  id="liHresAides" style="margin-top:10px;cursor:pointer;list-style:none;margin-top:10px;">
	            <a  href="javascript:ShowHresAides()"><i class="fa fa-user-clock" style="font-size:24px;"></i>&nbsp;&nbsp;Onglet heures aides</a>

	          </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          <li  style="cursor:pointer;list-style:none;margin-top:10px;">
	            <a onclick="window.print()"
		          <i class="glyphicon glyphicon-print" style="font-size:24px;"></i>&nbsp;Imprimer
	            </a>
	          </li>
            <li>
              <a href="/excel/cedule/'.$dateactive.'" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
                <i class="fa fa-file-excel" style="font-size:24px;"></i>&nbsp;&nbsp;Cédule vers EXCEL
              </a>

            </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          <li style="cursor:pointer;list-style:none;margin-top:10px;">
	            <a href="javascript:ShowRemoveCedule()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Supprimer" data-content="Supprimer une ou des journ&eacute;es">
		          <i class="fa fa-trash" style="font-size:24px;color: red;"></i>&nbsp;&nbsp;Suppression de c&eacute;dule
	            </a>
	          </li>
            <li style="cursor:pointer;list-style:none;margin-top:10px;">
              <a href="/sanivan/copie" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Copier" data-content="Copier des journ&eacute;es ou des semaines">
                <i class="far fa-clone" style="font-size:24px;"></i>&nbsp;&nbsp;Cloner
              </a>
            </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
            <li style="cursor:pointer;list-style:none;margin-top:10px;">
              <a href="#" onclick="ShowVillesCachees()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Villes Cach&eacute;es" data-content="Afficher toutes les villes">
                <i class="far fa-clone" style="font-size:24px;"></i>&nbsp;&nbsp;Afficher Villes cachées
              </a>
            </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          '.$this->GetMenuSupCheckboxes(false, $dateactive).'
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          </li>'. $this->GetMenuTop($dateactive);


    return $message;
  }
  /**
   * Cases a cocher pour afficher/cacher les lignes separatrices des
   * superviseurs dans la cedule (etat global par journee, table
   * cedules_supvis: partage par tous les usagers, limite a la date).
   * $navbar = true: items <li> inline pour la barre de menu
   * (meme style que les cases REAREND/ROBOT); false: items de
   * dropdown-menu.
   *
   * @param bool $navbar
   * @param string $datejour
   *
   * @return string
   */
  private function GetMenuSupCheckboxes($navbar = false, $datejour = '')
  {
    /** @var array $sups */
    $sups = $this->GetSuperviseursCedule();
    /** @var array $vis */
    $vis = $this->GetSupVisibilite($datejour);
    /** @var string $out */
    $out = '';
    if ($navbar)
    {
      foreach ($sups as $supIdx => $sup)
      {
        $out .= '<li><div style="font-weight:bold;font-size:14px;color:black;">'.
          '<label class="form-control" style="width:auto;white-space:nowrap;background-color:#16417c;color:#fff;border-color:#0d2a52;">'.
          '<input type="checkbox" id="chkSup_'.$supIdx.'" '.(($vis[$supIdx] == 1)? 'checked' : '').
          ' onclick="toggleSupVisible('.$supIdx.', this)" />&nbsp;<i class="fa fa-user"></i>&nbsp;'.$sup['nom'].'</label></div></li>';
      }
      return $out;
    }
    $out = '<li style="list-style:none;margin-top:8px;padding-left:10px;color:#777;font-size:11px;text-transform:uppercase;">Lignes superviseurs</li>';
    foreach ($sups as $supIdx => $sup)
    {
      $out .= '<li style="cursor:default;list-style:none;margin-top:6px;" onclick="event.stopPropagation()">'.
        '<label style="font-weight:normal;cursor:pointer;margin-left:10px;">'.
        '<input type="checkbox" id="chkSup_'.$supIdx.'" '.(($vis[$supIdx] == 1)? 'checked' : '').
        ' onclick="toggleSupVisible('.$supIdx.', this)" style="margin-right:6px;" />'.
        '<i class="fa fa-user" style="font-size:16px;"></i>&nbsp;'.$sup['nom'].'</label></li>';
    }
    return $out;
  }
  /**
   *
   * @param string $dateactive
   *
   * @return string
   */
  private function GetMenuTop($dateactive)
  {
    $message = '    <li style="cursor:pointer;list-style:none;margin-top:10px;">
          <a href="/rapport/sani/'.date('Y-m-d').'/0" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation Sanivan" data-content="Afficher la page de validation pour les employés et les camions.">
            <i class="fa fa-tasks" style="font-size:24px;"></i>&nbsp;&nbsp;Validation des routes
          </a>
          </li>
          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/sanivan/aujourdhui/8/'.$dateactive.'" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
              <i class="fa fa-tv" style="font-size:24px;"></i>&nbsp;&nbsp;TV
            </a>

          </li>

          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/sanivan/dictionnaires/1/1">
              <i class="fa fa-book" style="font-size:24px;"></i>&nbsp;Dictionnaire
            </a>

          </li>';


    return $message;
  }
  /**
   *
   *
   * @return string
   */
  public function GetAllEmployes($typerh, $idrh)
  {
    if ($typerh == 0)
    {
      $typerh = '1,2,3';
    }
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
        from reshumaine where idreshum in ($typerh) and isdeleted = 0 order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $select = '';
      if ($idrh == $row->id)
      {
        $select = (($idrh == $row->id)? ' selected ' : '');
      }
      $returnval  .= '<option '.$select.' value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    $this->data['employes'] = $returnval;
    return $returnval;
  }
  /**
   *
   * @return string
   */
  public function ShowAddRH()
  {
    /** @var string $returnval */
    $returnval = '
				<form action="/sanivan/addrh//0" method="post" enctype="multipart/form-data" id="form_0"  name="form_0" >
														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
                            														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
															 <tr>
                              <td colspan="2"><h4># Camion :</h4><br> <input class="form-control"  id="nocamiontoadd" type="text" value="" style="width:50%">
                              </td>

                              <td colspan="2"><h4>Type de camion :</h4><br> <select class="form-control" id="selTypeCamion"><option value="" selected >Choisir</option>'.$this->GetTypeCamion( '' ).'</td>

                              </tr>
															<tr>
																<td colspan="4">&nbsp;</td>
															</tr>
															<tr>
		                            <td> <label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkAutoAdd" />&nbsp; &nbsp; Automatique </label></td>
                                <td> <label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkManuelAdd" />&nbsp; &nbsp; Manuel &nbsp; &nbsp;</label></td>
		                             <td><label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkTreuilAdd" />&nbsp; &nbsp; Trueil&nbsp; &nbsp; </label></td>
                                 <td><label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkVerseurAdd" />&nbsp; &nbsp; Verseur </label></td>
															</tr>
                              <tr>
																<td colspan="4">&nbsp;</td>
															</tr>
                            <tr>
                              <td ><button type="button" onclick="sendCamionInfo(\'\')" class="btn-success btn" > Enregistrer </button>
																			<button type="button" onclick="ShowHide(\'divAddCam\')" class="btn-danger btn" > Annuler </button></td>
                            </tr>
														</table>
													</form>';
    return $returnval. " <script> $('#divAdd').hide(); </script>" ;
  }

  public function GetRHList($showdeleted = ' WHERE isdeleted = 0 ')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT id, Nom, Prenom, idreshum,email,tel1,havescan,tel2,
	                (SELECT typerh.Nom FROM typerh WHERE typerh.id = reshumaine.idreshum) AS rhtype,
                DIM, LUN, MAR, MER, JEU, VEN, SAM, isdeleted
	                FROM reshumaine $showdeleted ORDER BY Nom";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $Nomcomplet = $row->Nom.', '.$row->Prenom;
      /** @var string $edit */
      $edit = ' <a style="width:85px" aria-label="Edit" title="Cliquer pour éditer" onclick="ShowRHInfo('.$row->id.')">
										           <i class="fa fa-edit btn btn-default" style="font-size:16px;"></i></a>';
      $confirmeDelete = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;' id='btnsuprh_".$row->id."'
                                onclick='RemoveRH(".$row->id.")'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></a>";
      $DIM = (($row->DIM == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $LUN = (($row->LUN == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $MAR = (($row->MAR == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $MER = (($row->MER == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $JEU = (($row->JEU == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $VEN = (($row->VEN == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $SAM = (($row->SAM == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $havescan = (($row->havescan == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $returnval .= '<tr>
													<td align="center">'.$confirmeDelete.'</td>
                            <td><label id="nom_'.$row->id.'" style="width:250px;line-height:35px;">'.$row->Nom.', '.$row->Prenom.'</td>
                            <td><label id="selTypeRH_'.$row->id.'" style="width:180px;line-height:35px;">'.$row->rhtype.'</td>
                            <td><label id="email_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->email.'</td>
                            <td><label id="tel1_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->tel1.'</td>
                            <td><label id="tel2_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->tel2.'</td>
		                    <td><label style="width:55px;line-height:35px;" '.(($row->DIM == 1)? ' class="label label-success"' : '').' id="DIM_'.$row->id.'">'.$DIM.'</td>
                        <td><label style="width:55px;line-height:35px;" '.(($row->LUN == 1)? ' class="label label-success"' : '').' id="LUN_'.$row->id.'">'.$LUN.'</td>
		                     <td><label style="width:55px;line-height:35px;" '.(($row->MAR == 1)? ' class="label label-success"' : '').' id="MAR_'.$row->id.'">'.$MAR.'</td>
                         <td><label style="width:55px;line-height:35px;" '.(($row->MER == 1)? ' class="label label-success"' : '').' id="MER_'.$row->id.'">'.$MER.'</td>
                         <td><label style="width:55px;line-height:35px;" '.(($row->JEU == 1)? ' class="label label-success"' : '').' id="JEU_'.$row->id.'">'.$JEU.'</td>
		                     <td><label style="width:55px;line-height:35px;" '.(($row->VEN == 1)? ' class="label label-success"' : '').' id="VEN_'.$row->id.'">'.$VEN.'</td>
                         <td><label style="width:55px;line-height:35px;" '.(($row->SAM == 1)? ' class="label label-success"' : '').' id="SAM_'.$row->id.'">'.$SAM.'</td>
                         <td><label style="width:55px;line-height:35px;" '.(($row->havescan == 1)? ' class="label label-success"' : '').' id="SAM_'.$row->id.'">'.$havescan.'</td>
												<td>'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetSuivisCamList($valtofind, $orderby)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT
		            (SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = suiviscamion.TypeDechet) AS TypeDechet,
                CASE
                    WHEN idstatut = 1 THEN 'HORS SERVICE'
                    WHEN idstatut = 2 THEN 'AVEC MATÉRIEL'
                    ELSE ''
                END as Statut,
                        suiviscamion.* FROM suiviscamion WHERE $valtofind
				            AND (SELECT camions.NoCamion  FROM camions WHERE camions.NoCamion = suiviscamion.nocamion) IS NOT NULL   ORDER BY $orderby";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $post */
      $post = ' <a style="width:85px" aria-label="Edit" title="Cliquer pour éditer" onclick="ShowCamModal('.$row->id.', '.$row->idstatut.')">
										           <i class="fa fa-edit btn btn-default" style="font-size:16px;"></i></a>';
      $confirmeDelete = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;' id='btnsuprh_".$row->id."'
                                onclick='RemoveSuivisCam(".$row->id.")'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></a>";
      $returnval .= '<tr>
													<td align="center">'.$confirmeDelete.'</td>
                            <td><label id="nom_'.$row->id.'" style="width:250px;line-height:35px;">'.$row->nocamion.'</td>
                            <td><label id="datestart_'.$row->id.'" style="width:180px;line-height:35px;">'.$row->datestart.'</td>
                            <td><label id="dateend_'.$row->id.'" style="width:320px;line-height:35px;color: darkred;">'.$row->dateend.'</td>
                            <td><label id="tel1_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->Statut.' / '.$row->TypeDechet.'</td>
                            <td><label id="tel1_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->note.'</td>
												<td>'.$post.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetSuivisRHList($valtofind, $orderby)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT (SELECT CONCAT( reshumaine.Nom,', ', reshumaine.Prenom) FROM reshumaine WHERE reshumaine.id = suivisrh.idrh) AS NomComplet,
		            (SELECT typeabsence.Nom FROM typeabsence WHERE typeabsence.id = suivisrh.idabsence) AS absence,
                        suivisrh.* FROM suivisrh WHERE $valtofind
				            AND (SELECT reshumaine.id  FROM reshumaine WHERE reshumaine.id = suivisrh.idrh) IS NOT NULL   ORDER BY $orderby ";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $post */
      $post = ' <a style="width:85px" aria-label="Edit" title="Cliquer pour éditer" onclick="ShowRHModalFromList('.$row->id.')">
										           <i class="fa fa-edit btn btn-default" style="font-size:16px;"></i></a>';
      $confirmeDelete = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;' id='btnsuprh_".$row->id."'
                                onclick='RemoveSuivisRH(".$row->id.")'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></a>";
      $returnval .= '<tr>
													<td align="center">'.$confirmeDelete.'</td>
                            <td><label id="nom_'.$row->id.'" style="width:250px;line-height:35px;">'.$row->NomComplet.'</td>
                            <td><label id="datestart_'.$row->id.'" style="width:180px;line-height:35px;">'.$row->datestart.'</td>
                            <td><label id="dateend_'.$row->id.'" style="width:320px;line-height:35px;color: darkred;">'.$row->dateend.'</td>
                            <td><label id="tel1_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->absence.'</td>
                            <td><label id="tel1_'.$row->id.'" style="width:320px;line-height:35px;">'.$row->note.'</td>
												<td>'.$post.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function getHresAides($dateactive, $strWhere)
  {
    $arrHresPM = array('11:00','11:15','11:30',
                        '11:45','12:00','12:15','12:30','12:45','13:00','13:15','13:30','13:45','14:00',
                        '14:15','14:30','14:45','15:00','15:15','15:30','15:45','16:00','16:15','16:30',
                        '16:45','17:00','17:15','17:30','17:45','18:00','18:15','18:30','18:45','19:00',
                        '19:15','19:30','19:45','20:00','20:15','20:30','20:45','21:00','21:15','21:30','21:45');
    $hrespm = '';

    $dateactive = $this->db->escape($dateactive);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT  ( Select CONCAT (Nom, ', ', left(Prenom, 1) )
	                      FROM reshumaine where id = suivis.idrh ) As NomComplet,
	                     IFNULL(( Select CAST(idreshum as CHAR(10) ) FROM reshumaine where id = suivis.idrh ), '0' ) As typeres,
	                     id, idrh, datejour, StartTime, EndTime, pause, note, LEFT( TIMEDIFF(TIMEDIFF(EndTime, pause),StartTime), 5) as HrsTrav
	                     FROM suivis where datejour = $dateactive and ( Select CAST(idreshum as CHAR(10) )
		                  FROM reshumaine where id = suivis.idrh ) is not null $strWhere order by NomComplet";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $hrespm = '';
      foreach ($arrHresPM as $hres)
      {
        $hrespm .= '<a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 2,\''.$hres.'\', 0)">'.$hres.'</a> <br />';
      }
      $returnval .= '<tr>
													<td><label class="form-control"  style="font-size:12px;font-weight:300;width: 130px;height: 30px;margin-top: 5px;" id="idrh_'.$row->idrh.'">'. $row->NomComplet.'</td>
		                    <td>
    <div class="btn-group form-inline" style="width: 115px;">
      <div class="input-group input-group-sm pull-left">
<span class="input-group-btn">
        <button type="button" class="btn btn-info dropdown-toggle" style="width: 30px;" data-toggle="dropdown">
           <i class="fa fa-clock"></i>
        </button>
        <div class="dropdown-menu" style="width: 75px;">
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'06:00'.'\', 0)">06:00</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'06:30'.'\', 0)">06:30</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'07:00'.'\', 0)">07:00</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'07:30'.'\', 0)">07:30</a>
        </div>
</span>
<input type="time" style="width: 85px;height:30px;font=size: 12px;" onkeyup="EnterKeyHre('.$row->id.', 1,this.value, event)" ondblclick="ClickHresAides('.$row->id.', 1,0)" onblur="ChangeHresAides('.$row->id.', 1,this.value, 0)" onchange="ChangeHresAides('.$row->id.', 1,this.value, 0)" id="idstar_'.$row->id.'" data-mask="99:99" value="'.$row->StartTime.'" required />
      </div>

    </div>
</td>
                        <td>
    <div class="btn-group form-inline" style="width: 115px;">
      <div class="input-group input-group-sm pull-left">
<span class="input-group-btn">
        <button type="button" class="btn btn-info dropdown-toggle" style="width: 30px;" data-toggle="dropdown">
           <i class="fa fa-clock"></i>
        </button>
        <div class="dropdown-menu" style="width: 75px;">'.$hrespm.'
        </div>
</span>
<input type="time" style="width: 85px;height:30px;font=size: 12px;" onkeyup="EnterKeyHre('.$row->id.', 2,this.value, event) onblur="ChangeHresAides('.$row->id.', 2,this.value)" ondblclick="ClickHresAides('.$row->id.', 2,0)" onchange="ChangeHresAides('.$row->id.', 2,this.value, 0)" id="idend_'.$row->id.'" data-mask="99:99" value="'.$row->EndTime.'" required />
      </div>


</td>
		                     <td>
    <div class="btn-group form-inline" style="width: 115px;">
      <div class="input-group input-group-sm pull-left">
<span class="input-group-btn">
        <button type="button" class="btn btn-info dropdown-toggle" style="width: 30px;" data-toggle="dropdown">
           <i class="fa fa-clock"></i>
        </button>
        <div class="dropdown-menu" style="width: 75px;">
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 3,\''.'00:00'.'\', 0)">00:00</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 3,\''.'00:30'.'\', 0)">00:30</a>
        </div>
</span>
<input type="text" style="width: 85px;height:30px;font=size: 12px;" onkeyup="EnterKeyHre('.$row->id.', 3,this.value,event)      onblur="ChangeHresAides('.$row->id.', 3,this.value, 0)" ondblclick="ClickHresAides('.$row->id.', 3,0)" onchange="ChangeHresAides('.$row->id.', 3,this.value, 0)" id="idpause_'.$row->id.'" required data-mask="99:99" value="'.$row->pause.'">
</div></div>
</td>
<td>
      <div class="input-group">
        <input type="text" style="height:30px;width: 180px;font=size: 12px;" id="idnote_'.$row->id.'" onkeyup="EnterKeyHre('.$row->id.', 4,this.value,event) onblur="ChangeHresAides('.$row->id.', 4,this.value, 0)"  onchange="ChangeHresAides('.$row->id.', 4,this.value, 0)" value="'.$row->note.'">
      </div>
</td>
<td>&nbsp;&nbsp;</td>
<td><input type="time" style="width: 85px;height:30px;font=size: 12px;"  data-mask="99:99" value="'.$row->HrsTrav.'"></td>
</tr>';
    }
    return $returnval;
  }
  public function getWinHresAides($dateactive, $strWhere)
  {
    $arrHresPM = array('11:00','11:15','11:30',
                        '11:45','12:00','12:15','12:30','12:45','13:00','13:15','13:30','13:45','14:00',
                        '14:15','14:30','14:45','15:00','15:15','15:30','15:45','16:00','16:15','16:30',
                        '16:45','17:00','17:15','17:30','17:45','18:00','18:15','18:30','18:45','19:00',
                        '19:15','19:30','19:45','20:00','20:15','20:30','20:45','21:00','21:15','21:30','21:45');
    $hrespm = '';

    $dateactive = $this->db->escape($dateactive);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT  ( Select CONCAT (Nom, ', ', left(Prenom, 1) )
	                      FROM reshumaine where id = suivis.idrh ) As NomComplet,
	                     IFNULL(( Select CAST(idreshum as CHAR(10) ) FROM reshumaine where id = suivis.idrh ), '0' ) As typeres,
	                     id, idrh, datejour, StartTime, EndTime, pause, note, LEFT( TIMEDIFF(TIMEDIFF(EndTime, pause),StartTime), 5) as HrsTrav
	                     FROM suivis where datejour = $dateactive and ( Select CAST(idreshum as CHAR(10) )
		                  FROM reshumaine where id = suivis.idrh ) is not null $strWhere order by NomComplet";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $hrespm = '';
      foreach ($arrHresPM as $hres)
      {
        $hrespm .= '<a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 2,\''.$hres.'\', 1)">'.$hres.'</a> <br />';
      }
      $returnval .= '<tr>
													<td><label class="form-control"  style="font-size:12px;font-weight:300;width: 180px;height: 30px;margin-top: 5px;" id="idrh_'.$row->idrh.'">'. $row->NomComplet.'</td>
		                    <td>
    <div class="btn-group form-inline" style="width: 115px;">
      <div class="input-group input-group-sm pull-left">
<span class="input-group-btn">
        <button type="button" class="btn btn-info dropdown-toggle" style="width: 30px;" data-toggle="dropdown">
           <i class="fa fa-clock"></i>
        </button>
        <div class="dropdown-menu" style="width: 75px;">
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'06:00'.'\', 1)">06:00</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'06:30'.'\', 1)">06:30</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'07:00'.'\', 1)">07:00</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 1,\''.'07:30'.'\', 1)">07:30</a>
        </div>
</span>
<input type="time" style="width: 105px;height:30px;font=size: 12px;" onkeyup="EnterKeyHre('.$row->id.', 1,this.value, event)" onchange="ChangeHresAides('.$row->id.', 1,this.value, 1)" ondblclick="ClickHresAides('.$row->id.', 1,1)"  id="edtdeb_'.$row->id.'" data-mask="99:99" value="'.$row->StartTime.'" required />
      </div>

    </div>
</td>
                        <td>
    <div class="btn-group form-inline" style="width: 115px;">
      <div class="input-group input-group-sm pull-left">
<span class="input-group-btn">
        <button type="button" class="btn btn-info dropdown-toggle" style="width: 30px;" data-toggle="dropdown">
           <i class="fa fa-clock"></i>
        </button>
        <div class="dropdown-menu" style="width: 75px;">'.$hrespm.'
        </div>
</span>
<input type="time" style="width: 105px;height:30px;font=size: 12px;" onkeyup="EnterKeyHreWin('.$row->id.', 2,this.value, event) onblur="ChangeHresAides('.$row->id.', 2,this.value, 1)" ondblclick="ClickHresAides('.$row->id.', 2,1)" onchange="ChangeHresAides('.$row->id.', 2,this.value, 1)" id="edtfin_'.$row->id.'" data-mask="99:99" value="'.$row->EndTime.'"  />
      </div>


</td>
<td>
    <div class="btn-group form-inline" style="width: 115px;">
      <div class="input-group input-group-sm pull-left">
<span class="input-group-btn">
        <button type="button" class="btn btn-info dropdown-toggle" style="width: 30px;" data-toggle="dropdown">
           <i class="fa fa-clock"></i>
        </button>
        <div class="dropdown-menu" style="width: 75px;">
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 3,\''.'00:00'.'\', 1)">00:00</a> <br />
          <a class="dropdown-item" style="width: 75px;" href="javascript:setHresAides('.$row->id.', 3,\''.'00:30'.'\', 1)">00:30</a>
        </div>
</span>
<input type="text" style="width: 105px;height:30px;font=size: 12px;" onkeyup="EnterKeyHreWin('.$row->id.', 3,this.value,event) onblur="ChangeHresAides('.$row->id.', 3,this.value, 1)" ondblclick="ClickHresAides('.$row->id.', 3,1)" id="edtpause_'.$row->id.'"  value="'.$row->pause.'"></td>
      </div>      </div>
<td>
      <div class="input-group">
        <input type="text" style="height:30px;width: 250px;font=size: 12px;" id="edtnote_'.$row->id.'" onkeyup="EnterKeyHreWin('.$row->id.', 4,this.value,event) onblur="ChangeHresAides('.$row->id.', 4,this.value, 1)" onchange="ChangeHresAides('.$row->id.', 4,this.value, 1)" value="'.$row->note.'">
<span class="input-group-btn">
<a href="#" class="form-control btn-success" style="height:30px;" onclick="PostHresAides('.$row->id.')" title="Cliquer pour enregistrer cette ligne">
            <span class="glyphicon glyphicon-ok"></span>
        </a>
</span>
      </div>
</td>
<td>&nbsp;&nbsp;</td>
<td><input type="time" style="width: 105px;height:30px;font=size: 12px; font-weight: bold;"  data-mask="99:99" value="'.$row->HrsTrav.'"></td>
</tr>';
    }
    return $returnval;
  }
  /**
   *
   *
   * @return string
   */
  public function GetTypeAbsences($typeabsence=0)
  {
    /** @var string $returnval */
    $returnval = '';
    if ($typeabsence == '')
    {
      $returnval  .= '<option value="" selected >Tous les types</option>';
    }
    else
    {
      $returnval  .= '<option value="" >Tous les types</option>';
    }
    /** @var string $strSQL */
    $strSQL = "SELECT id, Nom FROM typeabsence order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $selected = (($typeabsence == $row->id)? ' selected ' : '');
      $returnval  .= '<option value="'.$row->id.'" '.$selected. '>'.$row->Nom.'</option>';
    }
    $this->data['typeabsences'] = $returnval;
    return $returnval;
  }
  public function GetTypeRH($idrh = '')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select Nom, id from typerh order BY Nom ";
    $camions = $this->db->query($strSQL);
    foreach($camions->getResult() as $row)
    {
      $selected = (($idrh == $row->id)? ' selected ' : '');
      $returnval .= '<option value="'.$row->id.'" '.$selected. ' >'.$row->Nom.'</option>';
    }
    return $returnval.'</select>';
  }
  /**
   *
   * @param int $idchauffeur
   *
   * @return string
   */
  public function GetChauffeurs($idchauffeur = 0)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', LEFT(Prenom, 1)) AS NomComplet, id
        from reshumaine where idreshum in (1,3,4,7) and isdeleted = 0 order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $selected = (($idchauffeur == $row->id)? ' selected ' : '');
      $returnval  .= '<option '.$selected.' value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    $this->data['chauffeurs'] = $returnval;
    return $returnval;
  }
  /**
   *
   * @param int $idaide
   *
   * @return void
   */
  public function GetAides($idaide = 24)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
        from reshumaine where idreshum = 2  and isdeleted = 0 order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $selected = (($idaide == $row->id)? ' selected ' : '');
      $returnval  .= '<option '.$selected.' value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    $this->data['aides'] = $returnval;
    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param string $idaide
   * @param string $optaide
   *
   * @return string
   */
  public function GetAidesCal($dateactive, $idaide, $optaide = '' )
  {

    $dateactive = $this->db->escape($dateactive);
    /** @var string $where */
    $where = '';
    /** @var string $returnval */
    $returnval = $optaide;
    $dteact = str_replace("'","", $dateactive);
    $date = date_create($dteact );
    $day_of_week = $date->format('w');
    $day = $this->JourDeSemaine[$day_of_week];
    if (($idaide > 0) and ($idaide != ''))
    {
      $idaide = $this->db->escape($idaide);
      $where = " AND cdl.HELPERS_1 != $idaide ";
    }
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', LEFT(Prenom, 1)) AS NomComplet, id,
                  if(rh.$day = 0, 'background-color:lightgoldenrodyellow','') AS background,
                   ( select cdl.HELPERS_1 from cedules cdl where cdl.DateJour = $dateactive
                       and cdl.HELPERS_1 = rh.id   AND rh.id NOT IN (24, 25)  LIMIT 0, 1 )  as ChaufIn
                    From reshumaine rh where
                  ( Select idrh From suivisrh srh
                       WHERE srh.idrh = rh.id AND ( dateend >= $dateactive  or dateend is null )  and
							  datestart <= $dateactive LIMIT 0, 1 ) is null AND idreshum = 2
                        and rh.isdeleted = 0   Order By Nom, Prenom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $selected = (($idaide == $row->id)? ' selected ' : '');
      $background = (($row->ChaufIn == '')? $row->background : $row->background.';color:red');
      $returnval  .= '<option '.$selected.'  style="'.$background.'"  value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    $this->data['aides'] = '<option value="0">Choisir</option>'.$returnval;
    return '<option value="0">Choisir</option>'.$returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param int $typerh
   * @param string $optchauffeur
   *
   * @return string
   */
  public function GetChauffeursCal($dateactive, $typerh, $optchauffeur = '' )
  {
    $where = '';
    $date = date_create($dateactive);
    $day_of_week = $date->format('w');
    $day = $this->JourDeSemaine[$day_of_week];
		if ( $this->input->post_get('action') != 2 )
    {
      $where = ' and rh.' . $this->JourDeSemaine[$day_of_week].' = 1 ';
    }
    $dateactive = $this->db->escape($dateactive);
    /** @var string $returnval */
    $returnval = '<option selected value="0">Choisir</option>'.$optchauffeur;

    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', LEFT(Prenom, 1)) AS NomComplet, id,if(rh.$day = 0, 'background-color:lightgoldenrodyellow','') AS background,
                   ( select cdl.idchauffeurs from cedules cdl where cdl.DateJour = $dateactive
                                     and cdl.idchauffeurs = rh.id   AND rh.id NOT IN (24, 25)  LIMIT 0, 1 )  as ChaufIn From reshumaine rh where
                  ( Select idrh From suivisrh srh
                       WHERE srh.idrh = rh.id AND ( dateend >= $dateactive  or dateend is null )  and
							  datestart <= $dateactive LIMIT 0, 1 ) is null
						            AND rh.idreshum IN ($typerh) AND rh.isdeleted = 0 $where   Order By Nom, Prenom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $background = (($row->ChaufIn == '')? $row->background : $row->background.';color:red');
      $returnval  .= '<option style="'.$background.'" value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    //$this->data['aides'] = $returnval;
    return $returnval;
  }

  /**
   *
   *
   *
   * @return string
   */
  public function GetSecteurs($idaide = 24)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
        from reshumaine where idreshum = 8  and isdeleted = 0 order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $selected = (($idaide == $row->id)? ' selected ' : '');
      $returnval  .= '<option '.$selected.'  value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    $this->data['aides'] = $returnval;
    return $returnval;
  }
  /**
   *
   * @param string $depotoir
   *
   * @return string
  */
  public function GetAllDepotoires($depotoir)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT nom FROM depotoire order by nom";
    $typededechet = $this->db->query($strSQL);
    foreach($typededechet->getResult() as $row)
    {
        $selected = (($depotoir == $row->nom)? ' selected ' : '');
        $returnval .= '<option value="'.$row->nom.'" '.$selected.' >'.$row->nom.'</option>';
    }
    return $returnval;
  }
  /**
   *
   *
   *
   * @return string
   */
  public function GetAllTypeProduits()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom FROM typededechet order by Nom";
    $typededechet = $this->db->query($strSQL);
    foreach($typededechet->getResult() as $row)
    {
        $returnval .= '<option value="'.$row->Nom.'">'.$row->Nom.'</option>';
    }
    $this->data['typededechet'] = $returnval;
    return $returnval;
  }
  /**
   *
   * @param object $row
   * @return string
   */
  private function ShowUpdCamions($row)
  {

    $checked = (($row->isdeleted == 1)? ' checked ' : '');
    $enligne = '<input class="btn"  style="width: 20px;height:20px;" id="chkdel_'.$row->NoCamion.'" '.$checked.'
                                  type="checkbox"  />';
    $checked = (($row->HaveWinch == 1)? ' checked ' : '');
    $treuil = '<input class="btn"  style="width: 20px;height:20px;" id="chktreuil_'.$row->NoCamion.'" '.$checked.'
                                  type="checkbox"  />';
    $checked = (($row->IsManuel == 1)? ' checked ' : '');
    $manuel = '<input class="btn"  style="width: 20px;height:20px;" id="chkmanuel_'.$row->NoCamion.'" '.$checked.'
                                  type="checkbox"  />';
    $checked = (($row->IsAutomatique == 1)? ' checked ' : '');
    $auto = '<input class="btn"  style="width: 20px;height:20px;" id="chkauto_'.$row->NoCamion.'" '.$checked.'
                                  type="checkbox"  />';
    $checked = (($row->IsVerseur == 1)? ' checked ' : '');
    $verseur = '<input class="btn"  style="width: 20px;height:20px;" id="chkverseur_'.$row->NoCamion.'" '.$checked.'
                                  type="checkbox"  />';
    /** @var string $returnval */
    $returnval = '<tr style="text-align:right;"  id="tr_'.$row->NoCamion.'"  style="display: none;" > <td colspan="8" >
      <section class="panel panel-featured-left">
        <div id="divExisteFrais"  class="panel-body">
          <section class="card">
				<form >
														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
															 <tr>
                              <td><h4># Camion :</h4><br> <input class="form-control"  name="titrefr" type="text" value="'.$row->NoCamion.'" style="width:100%">
                              </td>
                              <td> &nbsp;&nbsp;&nbsp;</td>
                              <td> &nbsp;&nbsp;&nbsp;</td>
                              <td> <h4>Supprimé :</h4><br>'.$enligne.'</td>
                              <td> &nbsp;&nbsp;&nbsp;</td>
                              <td> &nbsp;&nbsp;&nbsp;</td>
                              <td><h4>Type de Camion :</h4><br> <select class="form-control" id="selTypeCamion"><option value="" selected >Choisir</option>'.$this->GetTypeCamion( '' ).'
                              </td>
                            </tr>
															<tr>
                                <td>&nbsp;</td>
																<td>&nbsp;</td>
                                <td>&nbsp;</td>
																<td></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
																<td></td>
															</tr>
															<tr>

		                            <td> <h4>Automatique :</h4><br>'.$auto.'</td>
                                <td> &nbsp;&nbsp;&nbsp;</td>
                                <td> <h4>Manuel :</h4><br>'.$manuel.'</td>
                                <td> &nbsp;&nbsp;&nbsp;</td>
		                             <td> <h4>Treuil :</h4><br>'.$treuil.'</td>
                                 <td> &nbsp;&nbsp;&nbsp;</td>
                                 <td> <h4>Verseur :</h4><br>'.$verseur.'</td>
															</tr>
                              <tr>
                                <td>&nbsp;</td>
																<td>&nbsp;</td>
                                <td>&nbsp;</td>
																<td></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
																<td></td>
															</tr>
                            <tr>
                              <td ><button type="button" onclick="SubmitImg(\'form_'.$row->NoCamion.'\')" class="btn-primary btn" > Enregistrer </button>
																	 <button type="button" onclick="ShowHide(\'tr_'.$row->NoCamion.'\')" class="btn-danger btn" > Annuler </button></td>
                            </tr>
														</table>
													</form>
          </div>
          </section>
        </div>
      </section></td></tr>'. " <script> $('#tr_".$row->NoCamion."').hide(); </script>";
    return $returnval ;
  }
  /**
   *
   * @return string
   */
  public function ShowAddCamions()
  {
    /** @var string $returnval */
    $returnval = '
				<form action="'.base_url().'gestion/update_marque/1//0" method="post" enctype="multipart/form-data" id="form_0"  name="form_0" >
														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
                            														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
															 <tr>
                              <td colspan="2"><h4># Camion :</h4><br> <input class="form-control"  id="nocamiontoadd" type="text" value="" style="width:50%">
                              </td>

                              <td colspan="2"><h4>Type de camion :</h4><br> <select class="form-control" id="selTypeCamion"><option value="0" selected >Choisir</option>'.$this->GetAllTypeCamion( '' ).'</td>

                              </tr>
															<tr>
																<td colspan="4">&nbsp;</td>
															</tr>
															<tr>
                                <td colspan="4">
                                 <table cellspacing="0" cellpadding="10" class="txtnormal">
                                    <tr>
		                            <td> <label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" type="checkbox" id="chkAutoAdd" />&nbsp; Automatique &nbsp; &nbsp;</label></td>
                                <td> <label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" type="checkbox" id="chkManuelAdd" />&nbsp; Manuel &nbsp; &nbsp;</label></td>
		                             <td><label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" type="checkbox" id="chkTreuilAdd" />&nbsp; Trueil&nbsp; &nbsp; </label></td>
                                 <td><label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" type="checkbox" id="chkVerseurAdd" />&nbsp; Verseur &nbsp; &nbsp;</label></td>
															                                    </tr>
                                  </table>
                                 </tr>
                                 </td>
                              <tr>
																<td colspan="4">&nbsp;</td>
															</tr>
                            <tr>
                              <td ><button type="button" onclick="sendCamionInfo()" class="btn-success btn" > Enregistrer </button>
																			<button type="button" onclick="ShowHide(\'divAddCam\')" class="btn-danger btn" > Annuler </button></td>
                            </tr>
														</table>
													</form>';
    return $returnval. " <script> $('#divAdd').hide(); </script>" ;
  }

  public function GetCamionList($showdeleted = ' WHERE isdeleted = 0 ')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select NoCamion, TypeCamion,
      (SELECT typecamion.Nom FROM typecamion WHERE id = camions.TypeCamion) AS TypeCam,
      IsAutomatique, IsManuel, IsVerseur, HaveWinch, Description, isdeleted from camions $showdeleted Order By NoCamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour éditer" onclick="ShowCamionInfo(\'cam_'.$row->NoCamion.'\')">
										           <i class="fa fa-edit btn btn-default" style="font-size:16px;"></i></a>';
      /** @var string $delete */
      $delete = "<a style='font-size:18px;color: #F00;' xclass='btn btn-danger' href='javascript:SupprimerCam(\"".$row->NoCamion."\");' title='Supprimer'><i class='fa fa-times' aria-hidden='true'></i></a>";
      $enligne = (($row->isdeleted == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $treuil = (($row->HaveWinch == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $manuel = (($row->IsManuel == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $auto = (($row->IsAutomatique == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $verseur = (($row->IsVerseur == 1)? 'Oui&nbsp;' : 'Non&nbsp;');
      $returnval .= '<tr>
													<td><label '.(($row->isdeleted == 1)? ' class="label label-danger"' : ' class="label label-info"').' id="isdel_'.str_replace(' ', '_', $row->NoCamion).'">'.$enligne.'</td>
                            <td><label id="cam_'.str_replace(' ', '_', $row->NoCamion).'">'.$row->NoCamion.'</td>
                            <td><label id="selTypeCamion_'.str_replace(' ', '_', $row->NoCamion).'">'.$row->TypeCam.'</td>
		                    <td><label '.(($row->IsAutomatique == 1)? ' class="label label-success"' : '').' id="auto_'.str_replace(' ', '_', $row->NoCamion).'">'.$auto.'</td>
                        <td><label '.(($row->IsManuel == 1)? ' class="label label-success"' : '').' id="manuel_'.str_replace(' ', '_', $row->NoCamion).'">'.$manuel.'</td>
		                     <td><label '.(($row->HaveWinch == 1)? ' class="label label-success"' : '').' id="treuil_'.str_replace(' ', '_', $row->NoCamion).'">'.$treuil.'</td>
                         <td><label '.(($row->IsVerseur == 1)? ' class="label label-success"' : '').' id="verseur_'.str_replace(' ', '_', $row->NoCamion).'">'.$verseur.'</td>
												<td>'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetCamionInfo($nocamion)
  {
    /** @var object $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select * from camions WHERE NoCamion = $nocamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
	  $row->camion = str_replace(' ', '_', $row->NoCamion);
      $returnval  = json_encode( $row);
    }
    return $returnval;
  }
  public function GetTypeDechets($table,$notype)
  {
    $order = (($table == 'dictionprix')? 'typeitem' : 'Nom');
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT * FROM $table ORDER BY $order";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $couleur */
      $couleur = 
        '<td align="center"><input class="form-control"  type="color"
                    id="selColor_' . $row->id . '" value="' . $row->couleur . '" /></td>';
      /** @var string $textcolor */
      $textcolor = '<td align="center"><input class="form-control"  type="color"
                    id="selTextColor_' . $row->id . '" value="' . $row->textcolor . '" /></td>';
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction('.$row->id.', '.$notype.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
      $returnval .= '<tr>
													<td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                            <td align="center"><input class="form-control" style="width: 300px" id="edtNom_'.$row->id.'" type="text" value="'.$row->Nom.'" /></td>
                            ' . $couleur . $textcolor . '
												<td  align="center">'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetDictionEmplacement($table,$notype)
  {
    $order = (($table == 'dictionprix')? 'typeitem' : 'nom');
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT * FROM $table ORDER BY $order";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction('.$row->id.','.$notype.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
      $returnval .= '<tr>
													<td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                            <td align="center"><input class="form-control" style="width: 300px" id="edtNom_'.$row->id.'" type="text" value="'.$row->nom.'" /></td>
												<td  align="center">'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetDictionPrix($table,$notype)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT id, prix, datestart, dateend, typeitem, isdeleted
	FROM $table ORDER BY id desc";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction('.$row->id.','.$notype.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
            $typeprix = $this->GetListItem($row->typeitem);
      $returnval .= '<tr>
						<td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                            <td align="center"><select class="form-control"style="width: 250px" id="selTypeprix_'.$row->id.'"><option value="" >Choisir</option>' . $typeprix.'" </select></td>
                            <td align="center"><input class="form-control" style="width: 100px" id="edtPrix_'.$row->id.'" type="number" value="'.$row->prix.'" /></td>
                            <td align="center"><input class="form-control" style="width: 150px" id="dteStart_'.$row->id.'" type="date" value="'.$row->datestart.'" /></td>
                            <td align="center"><input class="form-control" style="width: 150px" id="dteFin_'.$row->id.'" type="date" value="'.$row->dateend.'" /></td>
												<td  align="center">'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetListItem($typeitem)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom, isdeleted FROM type_depense WHERE isdeleted = 0";
    $query = $this->db->query($strSQL);
    foreach ($query->getResultArray() as $row) {
            $selected = ($row['Nom'] == $typeitem) ? ' selected' : '';
            $returnval .= '<option value="' . $row['Nom'] . '" ' . $selected . ' >'
                . $row['Nom'] . '</option>';
    }
    return $returnval;
  }
  public function GetDictionDepotoire($table,$notype)
  {
    $order = (($table == 'dictionprix')? 'typeitem' : 'nom');
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT * FROM $table ORDER BY $order";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction('.$row->id.', '.$notype.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
      $checkedSan = (($row->issanivan == 1) ? ' checked="checked" ' : '');
      $issanivan = '<input class="btn"  style="width: 25px;height:25px;" id="chkSanivan_'.$row->id.'" '. $checkedSan.'
                                      type="checkbox"/>' ;
      $returnval .= '<tr>
													<td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
													<td align="center">'. $issanivan.'&nbsp;&nbsp;<label '.(($row->issanivan == 1)? ' class="label label-danger"' : '').' id="issanivan_'.$row->id.'">'.(($row->issanivan == 1)? 'Oui' : 'Non').'</td>

                            <td align="center"><input class="form-control" style="width: 300px" id="edtNom_'.$row->id.'" type="text" value="'.$row->nom.'" /></td>
                            <td align="center"><input class="form-control" style="width: 100px" id="edtTypeDepot_'.$row->id.'" type="number" value="'.$row->TypeDepot.'" /></td>
                            <td align="center"><input class="form-control" style="width: 100px" id="edtPrix_'.$row->id.'" type="number" value="'.$row->prix.'" /></td>
                            <td align="center"><input class="form-control" style="width: 100px" id="edtPrixSani_'.$row->id.'" type="number" value="'.$row->prix_sani.'" /></td>
                            <td align="center"><input class="form-control" style="width: 400px" id="edtAdresse_'.$row->id.'" type="text" value="'.$row->adresse.'" /></td>
												<td  align="center">'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetDictionContenant($table,$notype)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT id, nom, prix, redevance, carburant, carburant_site, demolition,
                        asphalte, beton, beton_arme, isdeleted,tonnage FROM $table ORDER BY nom";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction('.$row->id.', '.$notype.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
      $returnval .= '<tr>
													<td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                            <td align="center"><input class="form-control" style="width: 110px" id="edtNom_'.$row->id.'" type="text" value="'.$row->nom.'" /></td>
                            <td align="center"><input class="form-control" style="width: 110px;text-align:right;" id="edtTonnage_'.$row->id.'" type="text" value="'.$row->tonnage.'" /></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtPrix_'.$row->id.'" type="text" value="'.number_format($row->prix,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>

                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtRedevance_'.$row->id.'" type="text" value="'.number_format($row->redevance,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtCarburant_'.$row->id.'" type="text" value="'.number_format($row->carburant,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtCarburant_site_'.$row->id.'" type="text" value="'.number_format($row->carburant_site,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtDemolition_'.$row->id.'" type="text" value="'.number_format($row->demolition,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtAsphalte_'.$row->id.'" type="text" value="'.number_format($row->asphalte,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px" id="edtBeton_'.$row->id.'" type="text" value="'.number_format($row->beton,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
                            <td align="center"><div class="input-group"><input class="form-control" style="width: 100px;text-align:right;" id="edtBeton_arme_'.$row->id.'" type="text" value="'.number_format($row->beton_arme,2,'.',' ').'" /><span class="input-group-btn form-control" style="width: 30px;">
                                     <i class="fa fa-dollar" style="font-size:18px;color:darkblue;"></i>
                                   </span>
                                  </div></td>
												<td  align="center">'.$edit.'</td>
												</tr>';
    }
    return $returnval;
  }
  public function GetDictionnaire($table,$notype)
  {
    $order = (($table == 'dictionprix')? 'typeitem' : 'Nom');
    $order = (($table == ' typecontenant ')? 'nom' : $order);
    if ($table == ' statutrequete ')
    {
      $order = 'description_fr';
    }
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT * FROM $table ORDER BY $order";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      /** @var string $couleur */
      $couleur = ((($notype == 3) or ($notype == 11)) ? '<td align="center"><input class="form-control"  type="color"
                    id="selColor_'.$row->id.'" value="'.$row->couleur.'" /></td>' :
                                              '' );
      /** @var string $textcolor */
      $textcolor = (($notype == 3) ? '<td align="center"><input class="form-control"  type="color"
                    id="selTextColor_'.$row->id.'" value="'.$row->textcolor.'" /></td>' :
                                              '' );
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction('.$row->id.','.$notype.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
      if ($table == ' typecontenant ')
      {
        $returnval .= '<tr>
													  <td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                              <td align="center"><input class="form-control" style="width: 300px" id="edtNom_'.$row->id.'" type="text" value="'.$row->nom.'" /></td>
												  <td  align="center">'.$edit.'</td>
												  </tr>';
      }
      else if ($table == ' statutrequete ')
      {
        $returnval .= '<tr>
													  <td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                              <td align="center"><input class="form-control" style="width: 300px" id="edtNom_'.$row->id.'" type="text" value="'.$row->description_fr.'" /></td>'.$couleur.'
												  <td  align="center">'.$edit.'</td>
												  </tr>';
      }
      else
      {
        $returnval .= '<tr>
													  <td align="center">'.$enligne.'&nbsp;&nbsp;<label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.(($row->isdeleted == 1)? 'Oui' : 'Non').'</td>
                              <td align="center"><input class="form-control" style="width: 300px" id="edtNom_'.$row->id.'" type="text" value="'.$row->Nom.'" /></td>'.$couleur.$textcolor.'
												  <td  align="center">'.$edit.'</td>
												  </tr>';
      }

    }
    return $returnval;
  }
  public function getAddDiction($notype)
  {
    switch ($notype)
    {
      case 1:
      case 2:
      case 3:
      case 4:
      case 5:
      case 6:
      case 8:
      case 12:
        return '<table class="table">
										<tr>
                      <td colspan="2"><h4>Nom :</h4><br> <input class="form-control"  id="edtNomAdd" type="text" value="" style="width:100%"></td>
                      <td> &nbsp;&nbsp;&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="8">&nbsp;</td>
									</tr>
                  <tr>
                    <td ><button type="button" onclick="addToDiction('.$notype.')" class="btn-success btn" > Enregistrer </button></td>
												<td >		<button type="button" onclick="ShowHideNika(\'divAddDiction\')" class="btn-danger btn" > Annuler </button></td>
                  </tr>
								</table>';
      case 7:
        return '<table class="table">
										<tr>
                      <td colspan="2"><h4>Nom :</h4><br> <input class="form-control"  id="edtNomAdd" type="text" value="" style="width:100%"></td>
                      <td> &nbsp;&nbsp;&nbsp;</td>
                      <td colspan="2"><h4>Type dépôt :</h4><br> <input class="form-control"  id="edtTypeDepotAdd" type="text" value="" style="width:100%"></td>
                      <td> &nbsp;&nbsp;&nbsp;</td>
                      <td ><h4>Prix :</h4><br> <input class="form-control"  id="edtPrixAdd" type="text" value="" style="width:100%"></td>
                      <td> &nbsp;&nbsp;&nbsp;</td>
                      <td ><h4>Redevance :</h4><br> <input class="form-control"  id="edtRedevanceAdd" type="text" value="" style="width:100%"></td>
                      <td> &nbsp;&nbsp;&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="10"><h4>Adresse :</h4><br> <input class="form-control"  id="edtAdresseAdd" type="text" value="" style="width:100%"></td>
									</tr>
                  <tr>
                    <td ><button type="button" onclick="addToDiction('.$notype.')" class="btn-success btn" > Enregistrer </button></td>
												<td >		<button type="button" onclick="ShowHideNika(\'divAddDiction\')" class="btn-danger btn" > Annuler </button></td>
                  </tr>
								</table>';
      case 9:
        return '<table class="table">
										<tr>
                      <td colspan="2"><h4>Nom :</h4><br> <input class="form-control"  id="edtNomAdd" type="text" value="" style="width:100%"></td>
                      <td  colspan="2"><h4>Prix :</h4><br> <input class="form-control"  id="edtPrixAdd" type="text" value="" style="width:100%"></td>
                      <td colspan="2"><h4>Date début :</h4><br> <input class="form-control"  id="dteStartAdd" type="date" value="" style="width:100%"></td>
                      <td colspan="2"><h4>Date fin :</h4><br> <input class="form-control"  id="dteFinAdd" type="date" value="" style="width:100%"></td>
                      <td colspan="2"><h4>Code :</h4><br> <input class="form-control"  id="edtCodeAdd" type="text" value="" style="width:100%"></td>
									</tr>
                  <tr>
                    <td ><button type="button" onclick="addToDiction('.$notype.')" class="btn-success btn" > Enregistrer </button></td>
												<td >		<button type="button" onclick="ShowHideNika(\'divAddDiction\')" class="btn-danger btn" > Annuler </button></td>
                  </tr>
								</table>';
    }
    return '';
  }
  public function GetVilleList($showdeleted = ' WHERE isdeleted = 0 ')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom, id, colorhex, isdeleted, sendinfo,
                (SELECT count(villesrecipiendaire.email) FROM villesrecipiendaire
                  WHERE villesrecipiendaire.idville = ville.id) AS nbMail FROM ville $showdeleted ORDER BY Nom";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $checked = (($row->isdeleted == 1)? ' checked="checked" ' : '');
      /** @var string $edit */
      $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="sendVilleInfo('.$row->id.')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $addemail = ' <button class="form-control-lg'.(($row->nbMail > 0)? ' btn-info': '') .'" id="btnmail_'.$row->id.'" title="Cliquer pour gérer les Courriels" onclick="showVilleMail('.$row->id.')">
										           &nbsp;Courriels&nbsp;&nbsp;'.(($row->nbMail > 0)? '<span class="badge badge-primary">'.$row->nbMail .'</span>': '').'</button>';
      $enligne = '<input class="btn"  style="width: 25px;height:25px;" id="chkDelete_'.$row->id.'" '.$checked.'
                                      type="checkbox"/>' ;
      $returnval .= '<tr>
													<td align="center"><label '.(($row->isdeleted == 1)? ' class="label label-danger"' : '').' id="isdel_'.$row->id.'">'.$enligne.(($row->isdeleted == 1)? '&nbsp;Oui' : '&nbsp;Non').'</td>
                            <td align="center"><input class="form-control" style="width: 250px" id="edtNom_'.$row->id.'" type="text" value="'.html_entity_decode($row->Nom).'" /></td>
                            <td align="center"><input class="form-control"  type="color" id="selColor_'.$row->id.'" value="'.$row->colorhex.'" /></td>
                            <td align="center">
                                <select class="form-control" style="width: 120px;" id="selSend_'.$row->id.'">
                                   <option '.(($row->sendinfo == 'Non')? ' selected ' : '').' value="Non">Non</option>
                                   <option '.(($row->sendinfo == 'Oui')? ' selected ' : '').' value="Oui">Oui</option>
                                </select></td>
                            <td align="center"><span style="width: 200px;">'.$addemail.'</span></td>
												<td  align="center">'.$edit.'</td>
												</tr>';
    }
    return (($returnval == '')? ' Aucune donnée pour cette demande' : $returnval);
  }
    public function GetSecteursList($where = '')
    {
        $returnval = '';

        // ⬅️ CORRECTION : Spécifier secteurs.isdeleted au lieu de juste isdeleted
        $strSQL = "SELECT secteurs.id, secteurs.Nom, secteurs.idville, secteurs.isdeleted,
                      ville.Nom as NomVille
               FROM secteurs 
               LEFT JOIN ville ON secteurs.idville = ville.id
               WHERE secteurs.isdeleted = 0
               ORDER BY secteurs.Nom";

        $query = $this->db->query($strSQL);

        foreach ($query->getResult() as $row) {
            $checked = ($row->isdeleted == 1) ? 'checked' : '';

            // Récupérer les polylines liées à ce secteur
            $strPolySQL = "SELECT id, nom 
                   FROM polilyne_ville 
                   WHERE idville = " . $this->db->escape($row->idville) . " 
                   AND nom = " . $this->db->escape($row->id) . "
                   ORDER BY nom";
            $polyQuery = $this->db->query($strPolySQL);
            $selectedPolylines = [];
            foreach ($polyQuery->getResult() as $poly) {
                $selectedPolylines[] = $poly->id;
            }

            $returnval .= '<tr>
    <td><input type="checkbox" id="chk_' . $row->id . '" ' . $checked . ' /></td>
    <td><input type="text" id="edtNom_' . $row->id . '" value="' . htmlspecialchars($row->Nom) . '" style="width:200px;" /></td>
    <td>
        <select id="selVille_' . $row->id . '" class="select-ville-secteur form-control" data-secteur-id="' . $row->id . '" style="width:200px;">
            <option value="">Sélectionner</option>
            ' . $this->GetOptVilles($row->idville) . '
        </select>
    </td>
    <td>
        <div style="display:flex; gap:5px; align-items:center;">
            <select id="selPolylines_' . $row->id . '" class="multiselect-polylines" multiple="multiple" style="width:200px;">';

            // Charger les polylines de la ville avec idsecteur
            if ($row->idville > 0) {
                $returnval .= $this->GetOptPolylinesByVille($row->idville, $row->id);
            }

            $returnval .= '</select>
            <button type="button" 
                    class="btn btn-sm btn-info" 
                    onclick="showPolylineMap(' . $row->id . ', \'' . htmlspecialchars($row->Nom, ENT_QUOTES) . '\', ' . $row->idville . ')" 
                    title="Afficher sur la carte">
                <i class="fa fa-map-marker"></i> Carte
            </button>
        </div>
        <script>
            $(function() {
                $("#selPolylines_' . $row->id . '").multiselect({
                    includeSelectAllOption: true,
                    selectAllText: "Tout sélectionner",
                    nonSelectedText: "Aucune sélection",
                    nSelectedText: " sélectionnées",
                    enableFiltering: true,
                    filterPlaceholder: "Rechercher",
                    buttonWidth: "200px",
                    maxHeight: 300
                });
                $("#selPolylines_' . $row->id . '").multiselect("select", [' . implode(',', $selectedPolylines) . ']);
            });
        </script>
    </td>
    <td><button type="button" onclick="sendSecteurInfo(' . $row->id . ')" class="btn btn-success btn-sm">Enregistrer</button></td>
</tr>';
        }

        return $returnval;
    }
    public function GetOptVilles($idville = 0)
    {
        $returnval = '';
        $strSQL = "SELECT id, Nom FROM ville ORDER BY Nom";
        $villes = $this->db->query($strSQL);
        foreach ($villes->getResult() as $row) {
            $selected = (($idville == $row->id) ? ' selected ' : '');
            $returnval .= '<option ' . $selected . ' value="' . $row->id . '" >' . $row->Nom . '</option>';
        }
        $this->data['optville'] = $returnval;
        return $returnval;
    }
    public function GetOptPolylinesByVille($idville, $idsecteur = 0)
    {
        $returnval = '';
        $idville = $this->db->escape($idville);
        $idsecteur = (int) $idsecteur;

        // Si on édite un secteur existant (idsecteur > 0), on filtre par idsecteur
        // Sinon, on affiche tous les polylines de la ville
        if ($idsecteur > 0) {
            $whereSecteur = " AND idsecteur = " . $this->db->escape($idsecteur);
        } else {
            $whereSecteur = "";
        }

        $strSQL = "SELECT id, nom 
               FROM polilyne_ville 
               WHERE idville = $idville 
               $whereSecteur
               ORDER BY nom";

        $query = $this->db->query($strSQL);

        foreach ($query->getResult() as $row) {
            $returnval .= '<option value="' . $row->id . '">' . htmlspecialchars($row->nom) . '</option>';
        }

        return $returnval;
    }
  public function GetRHInfo($id)
  {
    /** @var object $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select * from reshumaine WHERE id = $id";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $returnval  = json_encode( $row);
    }
    return $returnval;
  }
  public function GetCamionsCal($dateactive, $nocamion)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select NoCamion from camions WHERE isdeleted = 0 order BY NoCamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $selected = (($nocamion == $row->NoCamion)? ' selected ' : '');
      $returnval  .= '<button type="button"  class="btn btn-info" id="camion_'.$row->NoCamion.'">'. $row->NoCamion .'</button><br />';
    }
    return $returnval;
  }
  public function GetCamions()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select NoCamion from camions WHERE isdeleted = 0 order BY NoCamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $returnval  .= '<button type="button"  class="btn btn-info" id="camion_'.$row->NoCamion.'">'. $row->NoCamion .'</button><br />';
    }
    return $returnval;
  }
  public function GetTypeCamion($typecam)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select Nom, id from typecamion where id <> 4 order BY Nom ";
    $camions = $this->db->query($strSQL);
    foreach($camions->getResult() as $row)
    {
      $selected = (($typecam == $row->id)? ' selected ' : '');
      $returnval .= '<option value="'.$row->id.'" '.$selected.'>'.$row->Nom.'</option>';
    }
    return $returnval.'</select>';
  }
  public function GetAllTypeCamion($typecam)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select Nom, id from typecamion order BY Nom ";
    $camions = $this->db->query($strSQL);
    foreach($camions->getResult() as $row)
    {
      $selected = (($typecam == $row->id)? ' selected ' : '');
      $returnval .= '<option value="'.$row->id.'" '.$selected.'>'.$row->Nom.'</option>';
    }
    return $returnval.'</select>';
  }
  public function GetAllCamions($typecam = '')
  {
    if (is_numeric($typecam))
    {
      $typecam = " AND TypeCamion = $typecam ";
    }
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select NoCamion from camions WHERE isdeleted = 0 $typecam order BY NoCamion ";
    $camions = $this->db->query($strSQL);
    foreach($camions->getResult() as $row)
    {
      $returnval .= '<option value="'.$row->NoCamion.'">'.$row->NoCamion.'</option>';
    }
    $this->data['camions'] = $returnval;
    return $returnval;
  }
  public function GetVilles()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var int $iter */
    $iter = 1;
    /** @var string $strSQL */
    $strSQL = "SELECT Nom, id FROM ville where isdeleted = 0  ORDER BY Nom";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      if ($iter == 1)
      {
        $this->activeVille = $row->id;
        $iter++;
      }
      $returnval  .= '<div id="'.$row->id.'"   ondragstart="dragStart(event)" onclick="getTypeRoute('.$row->id.')" class="external-event bg-light-blue">'. $row->Nom .'</div>';
    }
    $this->data['ville'] = $returnval;
  }
  //public function GetOptVilles()
  //{
  //  /** @var string $returnval */
  //  $returnval = '';
  //  /** @var int $iter */
  //  $iter = 1;
  //  /** @var string $strSQL */
  //  $strSQL = "SELECT Nom, id FROM ville where isdeleted = 0  ORDER BY Nom";
  //  $ville = $this->db->query($strSQL);
  //  foreach($ville->getResult() as $row)
  //  {
  //    if ($iter == 1)
  //    {
  //      $this->activeVille = $row->id;
  //      $iter++;
  //    }
  //    $returnval  .= '<option value="'.$row->id.'">'. $row->Nom .'</option>';
  //  }
  //  $this->data['optville'] = $returnval;
  //}
  public function getCedule($id,$dateactive = '', $typerh = '')
  {
    /** @var string $strSQL */
    $strSQL = "SELECT cedules.ID, TypeDeProduit, idvilles, IsStart, ville.Nom AS VilleNom, cedules.DateJour, NOCAMION,cedules.idchauffeurs,cedules.Secteur,
                    (SELECT CONCAT(left(Prenom,1), '. ', Nom) FROM reshumaine WHERE id = cedules.idchauffeurs) AS NomChauffeur,cedules.HELPERS_1,
                    (SELECT CONCAT(left(Prenom,1), '. ', Nom) FROM reshumaine WHERE id = cedules.HELPERS_1) AS Aide, TypeCedule
                       FROM cedules INNER JOIN ville ON cedules.idvilles = ville.id
                                        where cedules.ID = $id ";
    /** @var object $data */
    $data = $this->db->query($strSQL)->getRow(0);
    switch ($data->TypeDeProduit)
    {
      case 'ORGANIQUE':
        $color = '#ebccaa';
        break;
      case 'RECYCLAGE':
        $color = '#9FF3F9';
        break;
      case 'DECHETS':
        $color = '#E5E5E5';
        break;
      case 'RESIDUS_VERT':
        $color = '#2DBC96';
        break;
      case 'GROS_REBUTS':
        $color = '#2B3B7AF';
        break;
      case 'BRANCHES':
        $color = '#D1B546';
        break;
      case 'FEUILLES':
        $color = '#2DBC96';
        break;
      default:
        $color = '#F7F1C5';
    }
    $typerh = (($typerh != '')? $typerh : '1,3,7');
    $typerh = (($typerh == 'chauf')? '1,3,7' : $typerh);
    $activedate = (($dateactive == '')? $data->DateJour : $dateactive );
    $optChauffeur = (($data->idchauffeurs > 0)? '<option value="' . $data->idchauffeurs . '" selected >' . $data->NomChauffeur . '</option>' : '');
    $data->color = $color ;
    $optAide = (($data->HELPERS_1 > 0)? '<option value="'.$data->HELPERS_1.'" selected >'.$data->Aide.'</option>':'<option value="0">Choisir</option>' );
    $data->lstaide = $this->GetAidesCal($activedate, $data->HELPERS_1,$optAide);
    $data->lstchauffeur = $this->GetChauffeursCal($activedate, $typerh, $optChauffeur);
    //$data->lstaide = $this->GetAidesCal($this->db->escape($data->DateJour), $data->HELPERS_1);
    $data->lsttyperh = $this->GetTypeRH((($data->TypeCedule == 1)? 1 : 3));
    $data->lsttypecam = $this->GetTypeCamion($data->TypeCedule);
    $this->creationcd = new CreationcdModel();
    $data->lstcamion = $this->creationcd->GetAllCamions($data->DateJour,$data->NOCAMION , $data->TypeCedule);
    $dataarr =(array)$data;
    foreach($dataarr  as $key => $value)
    {
      $dataarr[$key] = utf8_encode( utf8_decode($value));
    }
    return json_encode($dataarr);
  }
  public function getCalendar()
  {
    $returnval = '<div class="box box-primary">
            <div class="box-body no-padding">
              <div id="calendar"></div>
            </div>
          </div>';
    //$this->GetAllVillesCal();
    $returnval .= '<script>
             var selectedDay = "<?php echo $ActiveDate ?>";
            $(function () {var date = new Date()
        var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()
    var myCalendar = $(\'#calendar\').fullCalendar({
      header    : {
        left  : \'prev,next today\',
        center: \'title\',
        right : \'month,basicWeek,agendaDay\'
      },
      buttonText: {
        today: "Aujourd\'hui",
        month: "Mois",
        week : "Semaine",
        day  : "Jour"
      },
		eventClick: function (event, jsEvent, view) {
			evenPost = event;
			var arrTitle = event.title.split(\', \');
			$("#plTitre").html(arrTitle[0]);
      var arrDescript = event.description.split(\', \');
      ActiveVille = 0;
			$("#typeprod").val(arrTitle[1]);
			$("#chauffeur").val(arrDescript[2]);
      $("#aide").val(arrDescript[4]);
      $("#camion").val(arrDescript[0]);
			$("#calVilles").modal("show");
      },
    dayClick: function(date, jsEvent, view) {

        selectedDay = date.format();
    },
    select: function(start, end, allDay) {

      var title = "serge"
      if (title) {
        calendar.fullCalendar(\'renderEvent\', {
            title: title,
            allDay: allDay
          },

          true
        );
      }
      calendar.fullCalendar(\'unselect\');
    },
    eventRender: function (event, element, view) {
            $(element).css("font-weight", "bold");
    },
      //Random default events
      events    : ['. $this->data['dataCD'] .' ],
      defaultView: \'basicWeek\',
      eventTextColor: \'black\',
      editable: true,
      eventDrop: function (event, date, view) {
        evenPost = event;
        $(\'#spville\').html(event.title);
        $("#transfertModal").modal("show");
      },
      droppable : true, // this allows things to be dropped onto the calendar !!!
      eventLimit: false, // allow "more" link when too many events
      selectable: true, //permite sa selectezi mai multe zile
      selectHelper: true, //coloreaza selctia ta
      drop      : function (date, allDay) { // this function is called when something is dropped
        var originalEventObject = $(this).data(\'eventObject\')
        evenPost = $.extend({}, originalEventObject)
        var dt = new Date(date).toLocaleString("en-US", {timeZone: "America/New_York"});
        // assign it the date that was reported
        evenPost.id = 0
        evenPost.start           = date
        evenPost.allDay          = allDay
        evenPost.backgroundColor = $(this).css(\'background-color\')
        evenPost.borderColor = $(this).css(\'border-color\')
        $("#plTitre").html(evenPost.title);
        $("#typeprod").val("");
			  $("#chauffeur").val(0);
        $("#aide").val(0);
        $("#camion").val("");
        $("#calVilles").modal("show");
      }
    })
    })</script>';
    return $returnval;
  }
  public function GetAllVillesCal()
  {
     $returnval = '';
     $arrData = array();
    $date = date('Y-m-d');
    $this->data['ActiveDate'] = $date;
    //$date = '2022-02-01';
    $strSQL = "SELECT cedules.ID, TypeDeProduit, idvilles, ville.Nom AS VilleNom, cedules.DateJour, NOCAMION,cedules.idchauffeurs,
                    (SELECT CONCAT(left(Prenom,1), '. ', Nom) FROM reshumaine WHERE id = cedules.idchauffeurs) AS NomChauffeur,cedules.HELPERS_1,
                    (SELECT CONCAT(left(Prenom,1), '. ', Nom) FROM reshumaine WHERE id = cedules.idchauffeurs) AS Aide
                       FROM cedules INNER JOIN ville ON cedules.idvilles = ville.id
                                        where DateJour >= '$date' order by DateJour, ville.Nom";
    /** @var object $cedules */
    $cedules = $this->db->query($strSQL);
        /** @var string $color */
        /** @var string $colorville */
        foreach($cedules->getResult() as $row)
        {
          switch ($row->TypeDeProduit)
          {
            case 'ORGANIQUE':
              $color = '#ebccaa';
              break;
            case 'RECYCLAGE':
              $color = '#9FF3F9';
              break;
            case 'DECHETS':
              $color = '#E5E5E5';
              break;
            case 'RESIDUS_VERT':
              $color = '#2DBC96';
              break;
            case 'GROS_REBUTS':
              $color = '#2B3B7AF';
              break;
            case 'BRANCHES':
              $color = '#D1B546';
              break;
            case 'FEUILLES':
              $color = '#2DBC96';
              break;
            default:
              $color = '#F7F1C5';
          }
          $arrData[] = array(
                              "title"             => "$row->VilleNom, $row->TypeDeProduit",
                              "description"             => "'".$row->NOCAMION.", ".$row->NomChauffeur.", ".
                                               $row->idchauffeurs.", ".$row->Aide.", ".$row->HELPERS_1."'",
                              "id"             => $row->ID,
                              "start"             => "'".$row->DateJour."'",
                              "alldate"             => 'alldate',
                              "backgroundColor"             => "'".$color."'"
                            );
        $returnval .= " {
                              title             : '".$row->VilleNom.', '.$row->TypeDeProduit."',
                              description       : '".$row->NOCAMION.', '.$row->NomChauffeur.', '.
                                                     $row->idchauffeurs.', '.$row->Aide.', '.$row->HELPERS_1."',
                              id                :	".$row->ID.",
                              start          : '".$row->DateJour."',
                              alldate          : 'alldate',
                              backgroundColor   : '$color'
                            },";
      }

    $returnval = substr($returnval,0,-1) ;
    $this->data['dataArray'] = json_encode( $arrData );
    $this->data['dataCD'] =  $returnval;
    $this->GetAides();
    $this->GetVilles();
    $this->GetAllCamions();
    $this->GetChauffeurs();
    $this->GetSecteursRobo();
    $this->GetAllTypeProduits();
    //$this->GetAllEmployes();
    //$this->GetTypeAbsences();
    return $this->data ;
  }
  public function AddToCedule($typeprod, $camion, $chauffeur, $aide, $date, $idville, &$idcedule = 0)
  {
    $camion = $this->db->escape($camion);
    $strSQL ="SELECT ville.id FROM ville WHERE ville.id = $idville";
    /** @var object $data */
    $data = $this->db->query($strSQL)->getRow(0);
    $idville = $data->id;
    $strSQL ="SELECT grp_cedules.id FROM grp_cedules WHERE datejour = $date
			                      AND idvilles = $idville";
    $data = $this->db->query($strSQL)->getRow(0);
    if (($data == '' ) or (!isset($data)))
    {
      $idgroupe = '';
    }
    else
    {
      $idgroupe = $data->id;
    }
    if (($idgroupe == '' ) or (!isset($idgroupe)))
    {
      $strSQL = "INSERT INTO grp_cedules
	                (datejour, idvilles, TypeCedule, Largeur, Hauteur)
	                VALUES ($date, $idville, 1, 450, 205)";
      $result = $this->db->query($strSQL);
      $strSQL = 'SELECT LAST_INSERT_ID() as id';
      if ($row = $this->db->query($strSQL)->getRow(0)) {
        $idgroupe = $row->id ;
      }
    }
    else
    {
      $strSQL = "UPDATE grp_cedules
	                        SET
		                        Hauteur = Hauteur + 205
	                        WHERE id = $idgroupe";
      $result = $this->db->query($strSQL);
    }
    $strSQL = "INSERT INTO cedules
	              (CodeUtil, DateJour, TypeDeProduit, NOCAMION, idchauffeurs, TypeCedule, HELPERS_1, idgrp,idvilles)
	              VALUES ('web', $date, $typeprod, $camion, $chauffeur, 1, $aide, $idgroupe, $idville)";
    $result = $this->db->query($strSQL);
    $strSQL = 'SELECT LAST_INSERT_ID() as id';
    if ($row = $this->db->query($strSQL)->getRow(0)) {
      $id = $row->id ;
      $chauf = str_replace("'", "",$chauffeur );
      if (($chauf != '24' or $chauf != '25' or $chauf != '0' ) and (substr(str_replace("'", "",$camion ) ,0, 3) !=''))
      {
        $strSQL = "SELECT id, idchauffeur, NOCAMION, KiloDeb, KiloFin, Fuel, PrixFuel, DateJour
											FROM camionjour WHERE DateJour = $date AND NOCAMION = $camion  " ;
        if (!($objCam = $this->db->query($strSQL)->getRow(0)))
        {
          $strSQL = "INSERT INTO camionjour
										(NOCAMION, datejour)
													VALUES ($camion, $date) ";
          $this->db->query($strSQL);
        }
        $strSQL = "SELECT id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin  FROM suivis WHERE
																						datejour = $date  AND idrh = $chauffeur and idcedule=  $id";
        if (!($objCam = $this->db->query($strSQL)->getRow(0)))
        {
          $strSQL = "INSERT INTO suivis
										(idrh, datejour, NOCAMION, idcedule)
													VALUES ($chauffeur, $date, $camion, $id) ";
          $this->db->query($strSQL);
        }
        $helper = str_replace("'", "",$aide );
        if ($helper != '24' or $helper != '25' or $helper != '0' )
        {
          $strSQL = "SELECT id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin  FROM suivis WHERE
																						  datejour = $date  AND idrh = $aide  and idcedule=  $id";
          if (!($objCam = $this->db->query($strSQL)->getRow(0)))
          {
            $strSQL = "INSERT INTO suivis
										  (idrh, datejour, NOCAMION, idcedule)
													  VALUES ($aide, $date, $camion, $id) ";
            $this->db->query($strSQL);
          }
        }

      }
      $strSQL = "SELECT cedules.ID, TypeDeProduit, idvilles, ville.Nom AS VilleNom, cedules.DateJour, NOCAMION,cedules.idchauffeurs,
                    (SELECT CONCAT(left(Prenom,1), '. ', Nom) FROM reshumaine WHERE id = cedules.idchauffeurs) AS NomChauffeur,cedules.HELPERS_1,
                    (SELECT CONCAT(left(Prenom,1), '. ', Nom) FROM reshumaine WHERE id = cedules.idchauffeurs) AS Aide
                       FROM cedules INNER JOIN ville ON cedules.idvilles = ville.id
                                        where cedules.ID = $id ";
      /** @var object $data */
      $data = $this->db->query($strSQL)->getRow(0);
      switch ($data->TypeDeProduit)
      {
        case 'ORGANIQUE':
          $color = '#ebccaa';
          break;
        case 'RECYCLAGE':
          $color = '#9FF3F9';
          break;
        case 'DECHETS':
          $color = '#E5E5E5';
          break;
        case 'RESIDUS VERT':
          $color = '#2DBC96';
          break;
        case 'GROS REBUTS':
          $color = '#2B3B7AF';
          break;
        case 'BRANCHES':
          $color = '#D1B546';
          break;
        case 'FEUILLES':
          $color = '#2DBC96';
          break;
        default:
          $color = '#F7F1C5';
      }
      $data->color = $color ;
      $idcedule = $id;
      return json_encode($data);
    }
    return 0;
  }
  private function getRapportJourRes($date, $camion,$idcedule, $from = 0)
  {

    /** @var string $returnval */
    $returnval = '';
    /** @var string $partie1 */
    $partie1 = '<tr style="text-align:right;"> <td></td><td colspan="13" >
									<table class="table">
		                <thead>
		                  <tr  style="text-align:right;color:darkturquoise;font-weight:bold;">
                      <th colspan="3">
                        <div align="left"></div>
                      </th>
                      <th>
                        <div align="left">Nom</div>
                      </th>
                      <th>
                        <div align="right">Début</div>
                      </th>
                      <th>
                        <div align="right">Fin </div>
                      </th>
                      <th>
                        <div align="right">Temps Pause (en 00:00)</div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>'.(($from == 0)? ' <th> <div align="right"></div> </th> ' : '').'

		                  </tr>
		                </thead>
		                <tbody';
	/** @var string $sql */
		$sql="SELECT (SELECT CONCAT(reshumaine.Nom, ' ', LEFT(reshumaine.Prenom, 1), '.')
							FROM reshumaine WHERE reshumaine.id = suivis.idrh) AS Nom,
					IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = suivis.idrh), 0) AS isbroker,
					CASE
						WHEN
							(SELECT idreshum
									FROM reshumaine WHERE reshumaine.id = suivis.idrh) IN (1, 3, 4, 7) THEN 1
						ELSE 0
					END AS TypeRes,
					(SELECT ville.Nom FROM ville WHERE ville.id = (SELECT cedules.idvilles FROM cedules WHERE cedules.id = suivis.idcedule )) AS NomVille,
					 id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin, pause
						FROM suivis WHERE datejour  = $date AND NOCAMION = '$camion' AND suivis.idcedule = $idcedule
                       order by
                       CASE
                        WHEN
	 	                    (SELECT idreshum
				                    FROM reshumaine WHERE reshumaine.id = suivis.idrh) IN (1, 3, 4, 7) THEN 1
                        ELSE 0
                      END DESC,
                      (SELECT cedules.id FROM cedules WHERE cedules.id = suivis.idcedule ) DESC";

		/** @var object $obTable */
    $obTable = new \stdClass();
    $obTable = $this->db->query($sql);
    /** @var object $row */
    foreach ($obTable->getResult() as $row)
		{
      $edit = "<button type='button'
		                      class='btn btn-default btn-sm'  style='font-size:18px;color: #03F;' xclass='btn-primary btn'
                              onclick=\"newcalcsuivis('".$row->id."')\" title='Enregistrer'><i class=\"fa fa-check btn btn-success\"></i></button>";
      $delete = "<button type=\"button\" class='btn btn-default btn-sm' style='font-size:18px;color: #03F;' xclass='btn-primary btn'
                          title='Supprimer cette Ligne'
                          onclick=\"DeleteRessValid('".$row->id."')\"  style=\"margin:4px 5px 0 0;\"><i class=\"fas fa-trash btn btn-danger\"></i></button>";
      $cancel = "<button type='button'
		                      class='btn btn-default btn-sm'  xclass='btn-primary btn'
                              onclick=\"".(($from == 0)? 'CalculJour()' : 'CalculJourChauffeur()')."\" title='Annuler les modifications'><i class='fas fa-remove  btn btn-warning' aria-hidden='true' ></i></button>";
	  if ($row->isbroker == 1)
	  {
		$returnval .= '<tr style="text-align:right;">
					  <td colspan="3">
						<div align="left"></div>
					  </td>
							<td><label class="form-control pull-left" style="background-color:#FFA500;font-weight:bold;">'.$row->Nom.'</label></td>
							<td colspan="3">&nbsp;</td>
							<td>'.$edit.'</td>
						<td>'.$cancel.'</td>'
							.(($from == 0)? ' <td>'.$delete.'</td> ' : '').'</tr>';
	  }
	  else
	  {
	  $backColor1 = (($row->StartTime == '00:00')?"background-color:red" : "background-color:lightgreen");
	  $backColor2 = (($row->EndTime == '00:00')?"background-color:red" : "background-color:lightgreen");
	  $returnval .= '<tr style="text-align:right;">
					  <td colspan="3">
						<div align="left"></div>
					  </td>
							<td><label class="form-control pull-left" >'.$row->Nom.'</label></td>
							<td><input type="time" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;" value="'.$row->StartTime.'" id="StartTime_'.$row->id.'" /></td>
							<td><input type="time" class="form-control pull-right" style="'.$backColor2.';font-weight:bold;" value="'.$row->EndTime.'" id="EndTime_'.$row->id.'" /></td>
							<td><input type="time" class="form-control pull-right" value="'.$row->dinerdeb.'"  style="font-weight:bold;" id="dinerdeb_'.$row->id.'" /></td>
							<td>'.$edit.'</td>
						<td>'.$cancel.'</td>'
							.(($from == 0)? ' <td>'.$delete.'</td> ' : '').'</tr>';
	  }
    }
    if ($returnval != '')
    {
      return $partie1. $returnval . '<tbody></table> </td></tr>';
    }
		else
    {
      return $returnval;
    }
  }
  public function AddSuivis($date)
  {
    $strSQL = "SELECT ID, NOCAMION, idchauffeurs,
                    IF(((SELECT reshumaine.idreshum FROM reshumaine WHERE id = cedules.HELPERS_1 ) = 8), 0, HELPERS_1) AS Aide
                          FROM cedules WHERE DateJour = $date  order by NOCAMION";
		/** @var object $obTable */
    $obTable = new \stdClass();
    $obTable = $this->db->query($strSQL);
    /** @var object $row */
    foreach ($obTable->getResult() as $row)
    {
      $camion = $row->NOCAMION;
      $chauffeur = $row->idchauffeurs;
      if ($chauffeur == '')
        $chauffeur = 25;
      $aide = $row->Aide;
      if ($aide == '')
        $aide = 24;
      $idcedule = $row->ID;
      if (substr($camion, 0, 4) != 'Cam_' )
      {
        $strSQL = "SELECT NOCAMION  FROM camionjour WHERE
																						    datejour = $date  AND NOCAMION = '$camion' AND idcedule = $idcedule";
        if (!($camionjour = $this->db->query($strSQL)->getRow(0)))
        {
          $strSQL = "INSERT INTO camionjour
	                (NOCAMION, DateJour, idcedule)
                value ('$camion', $date, $idcedule)";
          /** @var object $obTable */
          $obTable = new \stdClass();
          $this->db->query($strSQL);
        }
      }
      if ($chauffeur != '24' and $chauffeur != '25' and $chauffeur != '0' )
      {
        $strSQL = "SELECT id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin  FROM suivis WHERE
																						    datejour = $date  AND idrh = $chauffeur and NOCAMION = '$camion' and idcedule=  $idcedule";
        if (!($objsuivis = $this->db->query($strSQL)->getRow(0)))
        {
          $strSQL = "INSERT INTO suivis
										    (idrh, datejour, NOCAMION, idcedule)
													    VALUES ($chauffeur, $date, '$camion', $idcedule) ";
          $this->db->query($strSQL);
        }
      }

      $helper = str_replace("'", "",$aide );
      if ($helper != '24' and $helper != '25' and $helper != '0' )
      {
        $strSQL = "SELECT id, StartTime, EndTime, dinerdeb, dinerfin, souperdeb, souperfin  FROM suivis WHERE
																						    datejour = $date  AND idrh = $aide";
        if (!($objsuivis = $this->db->query($strSQL)->getRow(0)))
        {
          $strSQL = "INSERT INTO suivis
										    (idrh, datejour, NOCAMION, idcedule)
													    VALUES ($aide, $date, '$camion', $idcedule) ";
          $this->db->query($strSQL);
        }
      }
    }
  }
  public function getRapportJour($date)
  {
    $date = $this->db->escape($date);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $camion */
    $camion = '';
    /** @var string $infoCam */
    $infoCam = '';
    /** @var string $partie1 */
    $partie1 = '<table class="table">
		                <thead>
		                  <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                      <th>
                        <div align="center">Ville</div>
                      </th>
                      <th>
                        <div align="left">Camion</div>
                      </th>
                      <th>
                        <div align="right">Kilo début</div>
                      </th>
                      <th>
                        <div align="right">Kilo fin</div>
                      </th>
                      <th>
                        <div align="right">Fuel</div>
                      </th>
                      <th>
                        <div align="right">Hre deb.</div>
                      </th>
                      <th>
                        <div align="right">hre fin</div>
                      </th>
                      <th>
                        <div align="right">Ticket</div>
                      </th>
                      <th>
                        <div align="right">Tonnage</div>
                      </th>
                      <th>
                        <div align="right">Dépotoire</div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
		                  </tr>
		                </thead>
		                <tbody';
    /** @var string $partie2 */
    $partie2 = '<table class="table">
		                <thead>
		                  <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                      <th>
                        <div align="center">Ville</div>
                      </th>
                      <th>
                        <div align="left">Camion</div>
                      </th>
                      <th>
                        <div align="left">&nbsp;&nbsp&nbsp;&nbsp</div>
                      </th>
                      <th>
                        <div align="left">&nbsp;&nbsp&nbsp;&nbsp</div>
                      </th>
                      <th>
                        <div align="left">&nbsp;&nbsp&nbsp;&nbsp</div>
                      </th>
                      <th>
                        <div align="right">Hre deb.</div>
                      </th>
                      <th>
                        <div align="right">hre fin</div>
                      </th>
                      <th>
                        <div align="right">Ticket</div>
                      </th>
                      <th>
                        <div align="right">Tonnage</div>
                      </th>
                      <th>
                        <div align="right">Dépotoire</div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
		                  </tr>
		                </thead>
		                <tbody';
    /** @var string $sql */
    $this->addSuivis($date);
		$sql="SELECT cedules.ID, cedules.TypeDeProduit,ifnull(camionjour.debut1, '') as debut1, ifnull(camionjour.fin1, '') as fin1,
						camionjour.Depotoire1,cedules.idchauffeurs,
						(SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
						IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs), 0) AS isbroker,
						camionjour.id AS idcam, camionjour.NOCAMION, camionjour.Ticket1, camionjour.Tonnage1,
				  camionjour.Ticket2, camionjour.Tonnage2,ifnull(camionjour.debut2,'') as debut2, ifnull(camionjour.fin2, '') as fin2,
						camionjour.Depotoire2,
				  camionjour.Ticket3, camionjour.Tonnage3,ifnull(camionjour.debut3, '') as debut3, ifnull(camionjour.fin3, '') as fin3,
						camionjour.Depotoire3,
				  camionjour.Ticket4, camionjour.Tonnage4,ifnull(camionjour.debut4, '') as debut4, ifnull(camionjour.fin4, '') as fin4,
						camionjour.Depotoire4,
						camionjour.KiloDeb, camionjour.KiloFin, camionjour.Fuel
					  FROM cedules INNER JOIN  camionjour ON cedules.ID = camionjour.idcedule
						WHERE cedules.DateJour = $date AND camionjour.DateJour = $date
										and cedules.TypeDeProduit <> 'COMMERCIAL'
					 ORDER BY cedules.NOCAMION, cedules.ID";
		/** @var object $color */
		/** @var object $obTable */
    $obTable = new \stdClass();
    $obTable = $this->db->query($sql);
    $idchauf = 0;
    /** @var object $row */
    foreach ($obTable->getResult() as $row)
		{
      switch ($row->TypeDeProduit)
      {
        case 'ORGANIQUE':
          $color = '#ebccaa';
          break;
        case 'RECYCLAGE':
          $color = '#9FF3F9';
          break;
        case 'DECHETS':
          $color = '#E5E5E5';
          break;
        case 'RESIDUS_VERT':
          $color = '#2DBC96';
          break;
        case 'GROS_REBUTS':
          $color = '#2B3B7AF';
          break;
        case 'BRANCHES':
          $color = '#D1B546';
          break;
        case 'FEUILLES':
          $color = '#2DBC96';
          break;
        default:
          $color = '#F7F1C5';
      }
      if ($camion != $row->NOCAMION)
      {
        if($row->KiloDeb == 0 )
        {
          $backColor1 = "background-color:red;color: white";
        }
        else
        {
          $backColor1 = "background-color:lightgreen;color: black";
        }
        if($row->KiloFin == 0 )
        {
          $backColor2 = "background-color:red;color: white";
        }
        else
        {
          $backColor2 = "background-color:lightgreen;color: black";
        }
        if($row->Fuel == 0 )
        {
          $backColor3 = "background-color:red;color: white";
        }
        else
        {
          $backColor3 = "background-color:lightgreen;color: black";
        }
		$infoCam = '<td><input type="number" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;" value="'.$row->KiloDeb.'" id="kiloDeb_'.$row->idcam.'" /></td>
							<td><input type="number" style="'.$backColor2.';font-weight:bold;" class="form-control pull-right" value="'.$row->KiloFin.'" id="kiloFin_'.$row->idcam.'" /></td>
							 <td><input type="number" style="'.$backColor3.';font-weight:bold;" class="form-control pull-right" value="'.$row->Fuel.'" id="Fuel_'.$row->idcam.'" /></td>';
		$titretoshow = $partie1;
		$camion = $row->NOCAMION;
	  }
	  else
	  {
		$infoCam = '<td><input type="hidden" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;" value="'.$row->KiloDeb.'" id="kiloDeb_'.$row->idcam.'" /></td>
							<td><input type="hidden" style="'.$backColor2.';font-weight:bold;" class="form-control pull-right" value="'.$row->KiloFin.'" id="kiloFin_'.$row->idcam.'" /></td>
							 <td><input type="hidden" style="'.$backColor3.';font-weight:bold;" class="form-control pull-right" value="'.$row->Fuel.'" id="Fuel_'.$row->idcam.'" /></td>';
		$titretoshow = $partie2;
	  }
	  if ($row->isbroker == 1)
	  {
		$titretoshow = $partie2;
		$infoCam = '<td><input type="hidden" value="0" id="kiloDeb_'.$row->idcam.'" />&nbsp;</td>
					<td><input type="hidden" value="0" id="kiloFin_'.$row->idcam.'" />&nbsp;</td>
					<td><input type="hidden" value="0" id="Fuel_'.$row->idcam.'" />&nbsp;</td>';
	  }
	  $depotoire1 = '<select class="form-control" style="font-weight:bold;" id="depotoire1_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire1).'</select>';
	  $depotoire2 = '<select class="form-control" style="font-weight:bold;" id="depotoire2_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire2).'</select>';
	  $depotoire3 = '<select class="form-control" style="font-weight:bold;" id="depotoire3_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire3).'</select>';
	  $depotoire4 = '<select class="form-control" style="font-weight:bold;" id="depotoire4_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire4).'</select>';
	  $backColor1 = (($row->debut1 == '')?"background-color:red;color: white" : "background-color:lightgreen;color: black");
	  $backColor2 = (($row->fin1 == '')?"background-color:red;color: white" : "background-color:lightgreen;color: black");
	  $edit = "<button type='button'
							  class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
							  onclick=\"newcalc(".$row->ID.','.$row->idcam.", 1)\" title='Enregistrer'><i class='fa fa-check  btn btn-success' aria-hidden='true'></i></a>";
      $delete = "<button type=\"button\" class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
                          title='Supprimer cette Ligne'
                          onclick=\"DeleteCamionValid('".$row->idcam."')\"  style=\"margin:4px 5px 0 0;\"><i  class=\"fa fa-trash btn btn-danger\" aria-hidden='true' ></i></button>";
      $cancel = "<button type='button'
		                      class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
                              onclick=\"CalculJour()\" title='Annuler les modifications'><i class='fa fa-remove btn btn-warning' aria-hidden='true'></i></a>";
      $returnval .= $titretoshow.'<tr style="text-align:right;">
                          <td><label class="form-control pull-left" style="background-color:'.$color.';width: 200px;">'.$row->NomVille.'</label></td>
                            <td><label class="form-control pull-left">'.$row->NOCAMION.'</label></td>'.$infoCam.'
                            <td><input type="time" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;"  value="'.(($row->debut1 == null)? '' : $row->debut1).'" id="debut1_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="'.$backColor2.';font-weight:bold;"  value="'.(($row->fin1 == null)? '' : $row->fin1).'" id="fin1_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket1.'" id="Ticket1_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage1.'" id="Tonnage1_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire1.'</td>
                        <td>'.$edit.'</td>
                        <td>'.$cancel.'</td>
		                    <td>'.$delete.'</td></tr><tr><td colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan="8"><table class="table">';
      for ($iter = 2; $iter < 5; $iter++)
      {
        $returnval .= '<tr id="tr_'.$iter.'_'.$row->idcam.'" style="text-align:right;display:'.
                            ((($iter == 2) or (($row->fin2 != '') and ($iter == 3)) or (($row->debut3 != '') and ($iter == 4)) )? 'block' : 'none').'">';
        switch ($iter)
        {
          case 2:
            $returnval .= '<td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->debut2 == null)? '' : $row->debut2).'" id="debut'.$iter.'_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->fin2 == null)? '' : $row->fin2).'" id="fin'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket2.'" id="Ticket'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage2.'" id="Tonnage'.$iter.'_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire2.'</td>';
            break;
          case 3:
            $returnval .= '<td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->debut3 == null)? '' : $row->debut3).'" id="debut'.$iter.'_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->fin3 == null)? '' : $row->fin3).'" id="fin'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket3.'" id="Ticket'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage3.'" id="Tonnage'.$iter.'_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire3.'</td>';
            break;
          case 4:
            $returnval .= '<td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->debut4 == null)? '' : $row->debut4).'" id="debut'.$iter.'_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.$row->fin4.'" id="fin'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket4.'" id="Ticket'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage4.'" id="Tonnage'.$iter.'_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire4.'</td>';
            break;
        }
        $returnval .= '<td>'.$edit.'</td>
                        <td>'.$cancel.'</td>
		                    <td>&nbsp;&nbsp;</td></tr>';
      }
      $returnval .= '</table></td></tr>'.(($idchauf != $row->idchauffeurs)? $this->getRapportJourRes($date, $row->NOCAMION, $row->ID,1) : '' ). '<tbody></table>';
      if ($idchauf != $row->idchauffeurs)
      {
        $idchauf = $row->idchauffeurs;
      }
    }
    $this->GetAllCamions();
    $this->GetAllTypeProduits();
    $this->GetSecteursRobo();
    $this->GetAllEmployes(0,0);
    $this->GetTypeAbsences();
		return  $returnval;
  }
  public function getValidChauffeurJour($date, $idchauffeur)
  {
    $date = $this->db->escape($date);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $camion */
    $camion = '';
    /** @var string $infoCam */
    $infoCam = '';
    /** @var string $partie1 */
    $partie1 = '<table class="table">
		                <thead>
		                  <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                      <th>
                        <div align="center">Ville</div>
                      </th>
                      <th>
                        <div align="left">Camion</div>
                      </th>
                      <th>
                        <div align="right">Kilo début</div>
                      </th>
                      <th>
                        <div align="right">Kilo fin</div>
                      </th>
                      <th>
                        <div align="right">Fuel</div>
                      </th>
                      <th>
                        <div align="right">Hre deb.</div>
                      </th>
                      <th>
                        <div align="right">hre fin</div>
                      </th>
                      <th>
                        <div align="right">Ticket</div>
                      </th>
                      <th>
                        <div align="right">Tonnage</div>
                      </th>
                      <th>
                        <div align="right">Dépotoire</div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
		                  </tr>
		                </thead>
		                <tbody';
    /** @var string $partie2 */
    $partie2 = '<table class="table">
		                <thead>
		                  <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                      <th>
                        <div align="center">Ville</div>
                      </th>
                      <th>
                        <div align="left">Camion</div>
                      </th>
                      <th>
                        <div align="left">&nbsp;&nbsp&nbsp;&nbsp</div>
                      </th>
                      <th>
                        <div align="left">&nbsp;&nbsp&nbsp;&nbsp</div>
                      </th>
                      <th>
                        <div align="left">&nbsp;&nbsp&nbsp;&nbsp</div>
                      </th>
                      <th>
                        <div align="right">Hre deb.</div>
                      </th>
                      <th>
                        <div align="right">hre fin</div>
                      </th>
                      <th>
                        <div align="right">Ticket</div>
                      </th>
                      <th>
                        <div align="right">Tonnage</div>
                      </th>
                      <th>
                        <div align="right">Dépotoire</div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
                      <th>
                        <div align="right"></div>
                      </th>
		                  </tr>
		                </thead>
		                <tbody';
    /** @var string $sql */
    $this->addSuivis($date);
		$sql="SELECT cedules.ID, cedules.TypeDeProduit, camionjour.debut1, camionjour.fin1,
						camionjour.Depotoire1,cedules.idchauffeurs,
						(SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
						IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs), 0) AS isbroker,
						camionjour.id AS idcam, camionjour.NOCAMION, camionjour.Ticket1, camionjour.Tonnage1,
				  camionjour.Ticket2, camionjour.Tonnage2,camionjour.debut2, camionjour.fin2,
						camionjour.Depotoire2,
				  camionjour.Ticket3, camionjour.Tonnage3,camionjour.debut3, camionjour.fin3,
						camionjour.Depotoire3,
				  camionjour.Ticket4, camionjour.Tonnage4,camionjour.debut4, camionjour.fin4,
						camionjour.Depotoire4,
						camionjour.KiloDeb, camionjour.KiloFin, camionjour.Fuel
					  FROM cedules INNER JOIN  camionjour ON cedules.ID = camionjour.idcedule
						WHERE cedules.DateJour = $date AND camionjour.DateJour = $date and cedules.idchauffeurs = $idchauffeur
										and cedules.TypeDeProduit <> 'COMMERCIAL'
				  GROUP BY cedules.ID
					 ORDER BY cedules.NOCAMION, cedules.ID";
		/** @var object $color */
		/** @var object $obTable */
    $obTable = new \stdClass();
    $obTable = $this->db->query($sql);
    $idchauf = 0;
    /** @var object $row */
    foreach ($obTable->getResult() as $row)
		{
      switch ($row->TypeDeProduit)
      {
        case 'ORGANIQUE':
          $color = '#ebccaa';
          break;
        case 'RECYCLAGE':
          $color = '#9FF3F9';
          break;
        case 'DECHETS':
          $color = '#E5E5E5';
          break;
        case 'RESIDUS_VERT':
          $color = '#2DBC96';
          break;
        case 'GROS_REBUTS':
          $color = '#2B3B7AF';
          break;
        case 'BRANCHES':
          $color = '#D1B546';
          break;
        case 'FEUILLES':
          $color = '#2DBC96';
          break;
        default:
          $color = '#F7F1C5';
      }
      if ($camion != $row->NOCAMION)
      {
        if($row->KiloDeb == 0 )
        {
          $backColor1 = "background-color:red;color: white";
        }
        else
        {
          $backColor1 = "background-color:lightgreen;color: black";
        }
        if($row->KiloFin == 0 )
        {
          $backColor2 = "background-color:red;color: white";
        }
        else
        {
          $backColor2 = "background-color:lightgreen;color: black";
        }
        if($row->Fuel == 0 )
        {
          $backColor3 = "background-color:red;color: white";
        }
        else
        {
          $backColor3 = "background-color:lightgreen;color: black";
        }
        $infoCam = '<td><input type="number" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;" value="'.$row->KiloDeb.'" id="kiloDeb_'.$row->idcam.'" /></td>
		                    <td><input type="number" style="'.$backColor2.';font-weight:bold;" class="form-control pull-right" value="'.$row->KiloFin.'" id="kiloFin_'.$row->idcam.'" /></td>
		                     <td><input type="number" style="'.$backColor3.';font-weight:bold;" class="form-control pull-right" value="'.$row->Fuel.'" id="Fuel_'.$row->idcam.'" /></td>';
        $titretoshow = $partie1;
        $camion = $row->NOCAMION;
      }
      else
      {
		$infoCam = '<td><input type="hidden" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;" value="'.$row->KiloDeb.'" id="kiloDeb_'.$row->idcam.'" /></td>
							<td><input type="hidden" style="'.$backColor2.';font-weight:bold;" class="form-control pull-right" value="'.$row->KiloFin.'" id="kiloFin_'.$row->idcam.'" /></td>
							 <td><input type="hidden" style="'.$backColor3.';font-weight:bold;" class="form-control pull-right" value="'.$row->Fuel.'" id="Fuel_'.$row->idcam.'" /></td>';
		$titretoshow = $partie2;
	  }
	  if ($row->isbroker == 1)
	  {
		$titretoshow = $partie2;
		$infoCam = '<td><input type="hidden" value="0" id="kiloDeb_'.$row->idcam.'" />&nbsp;</td>
					<td><input type="hidden" value="0" id="kiloFin_'.$row->idcam.'" />&nbsp;</td>
					<td><input type="hidden" value="0" id="Fuel_'.$row->idcam.'" />&nbsp;</td>';
	  }
	  $depotoire1 = '<select class="form-control" style="font-weight:bold;" id="depotoire1_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire1).'</select>';
	  $depotoire2 = '<select class="form-control" style="font-weight:bold;" id="depotoire2_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire2).'</select>';
	  $depotoire3 = '<select class="form-control" style="font-weight:bold;" id="depotoire3_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire3).'</select>';
	  $depotoire4 = '<select class="form-control" style="font-weight:bold;" id="depotoire4_'.$row->idcam.'"><option value="">Choisir</option>'.$this->GetAllDepotoires($row->Depotoire4).'</select>';
	  $backColor1 = (($row->debut1 == '')?"background-color:red;color: white" : "background-color:lightgreen;color: black");
	  $backColor2 = (($row->fin1 == '')?"background-color:red;color: white" : "background-color:lightgreen;color: black");
	  $edit = "<button type='button'
							  class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
							  onclick=\"newcalc(".$row->ID.','.$row->idcam.", 2)\" title='Enregistrer'><i class='fa fa-check  btn btn-success' aria-hidden='true'></i></a>";
			//$delete = "<button type=\"button\" class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
			//                    title='Supprimer cette Ligne'
			//                    onclick=\"DeleteCamionValid('".$row->idcam."')\"  style=\"margin:4px 5px 0 0;\"><i  class=\"fa fa-trash btn btn-danger\" aria-hidden='true' ></i></button>";
      $cancel = "<button type='button'
		                      class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
                              onclick=\"CalculJourChauffeur()\" title='Annuler les modifications'><i class='fa fa-remove btn btn-warning' aria-hidden='true'></i></a>";
      $returnval .= $titretoshow.'<tr style="text-align:right;">
                          <td><label class="form-control pull-left" style="background-color:'.$color.';width: 200px;">'.$row->NomVille.'</label></td>
                            <td><label class="form-control pull-left">'.$row->NOCAMION.'</label></td>'.$infoCam.'
                            <td><input type="time" class="form-control pull-right" style="'.$backColor1.';font-weight:bold;"  value="'.(($row->debut1 == null)? '' : $row->debut1).'" id="debut1_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="'.$backColor2.';font-weight:bold;"  value="'.(($row->fin1 == null)? '' : $row->fin1).'" id="fin1_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket1.'" id="Ticket1_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage1.'" id="Tonnage1_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire1.'</td>
                        <td>'.$edit.'</td>
                        <td>'.$cancel.'</td></tr><tr><td colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan="8"><table class="table">';
      for ($iter = 2; $iter < 5; $iter++)
      {
        $returnval .= '<tr id="tr_'.$iter.'_'.$row->idcam.'" style="text-align:right;display:'.
                            ((($iter == 2) or (($row->fin2 != '') and ($iter == 3)) or (($row->debut3 != '') and ($iter == 4)) )? 'block' : 'none').'">';
        switch ($iter)
        {
          case 2:
            $returnval .= '<td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->debut2 == null)? '' : $row->debut2).'" id="debut'.$iter.'_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->fin2 == null)? '' : $row->fin2).'" id="fin'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket2.'" id="Ticket'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage2.'" id="Tonnage'.$iter.'_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire2.'</td>';
            break;
          case 3:
            $returnval .= '<td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->debut3 == null)? '' : $row->debut3).'" id="debut'.$iter.'_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->fin3 == null)? '' : $row->fin3).'" id="fin'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket3.'" id="Ticket'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage3.'" id="Tonnage'.$iter.'_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire3.'</td>';
            break;
          case 4:
            $returnval .= '<td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.(($row->debut4 == null)? '' : $row->debut4).'" id="debut'.$iter.'_'.$row->idcam.'" /></td>
                            <td><input type="time" class="form-control pull-right" style="font-weight:bold;" value="'.$row->fin4.'" id="fin'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="text" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Ticket4.'" id="Ticket'.$iter.'_'.$row->idcam.'" /></td>
		                    <td><input type="number" class="form-control pull-right" style="font-weight:bold;" value="'.$row->Tonnage4.'" id="Tonnage'.$iter.'_'.$row->idcam.'" /></td>
		                    <td>'.$depotoire4.'</td>';
            break;
        }
        $returnval .= '<td>'.$edit.'</td>
                        <td>'.$cancel.'</td>
		                    <td>&nbsp;&nbsp;</td></tr>';
      }
      $returnval .= '</table></td></tr>'.(($idchauf != $row->idchauffeurs)? $this->getRapportJourRes($date, $row->NOCAMION, $row->ID,1) : '' ). '<tbody></table>';
      if ($idchauf != $row->idchauffeurs)
      {
        $idchauf = $row->idchauffeurs;
      }
    }
    $this->GetAllCamions();
    $this->GetAllTypeProduits();
    $this->GetAllEmployes(0,0);
    $this->GetTypeAbsences();
		return  $returnval;
  }

  public function PostSuivis($StartTime, $EndTime, $dinerdeb, $id)
  {
	//---------------------------------------------
	$strSQL = "UPDATE suivis
					  SET
						  StartTime=$StartTime,
						  EndTime=$EndTime,
						  dinerdeb=$dinerdeb
					  WHERE id = $id";
	$this->db->query($strSQL);
  }

  public function PostCam($KiloDeb, $KiloFin, $Fuel,
                          $Ticket1, $Tonnage1, $depotoire1, $debut1, $fin1,
                          $Ticket2, $Tonnage2, $depotoire2, $debut2, $fin2,
                          $Ticket3, $Tonnage3, $depotoire3, $debut3, $fin3,
                          $Ticket4, $Tonnage4, $depotoire4, $debut4, $fin4, $idcam)
  {
    $strSQL = "UPDATE camionjour
	                    SET
		                    KiloDeb=$KiloDeb,
		                    KiloFin=$KiloFin,
		                    Fuel=$Fuel,
                        Ticket1=$Ticket1,
		                    Tonnage1=$Tonnage1,
                        Depotoire1=$depotoire1,
                        debut1=$debut1,
                        fin1=$fin1,
		                    Ticket2=$Ticket2,
		                    Tonnage2=$Tonnage2,
                        Depotoire2=$depotoire2,
                        debut2=$debut2,
                        fin2=$fin2,
                        Ticket3=$Ticket3,
                        Tonnage3=$Tonnage3,
                        Depotoire3=$depotoire3,
                        debut3=$debut3,
                        fin3=$fin3,
                        Ticket4=$Ticket4,
                        Tonnage4=$Tonnage4,
                        Depotoire4=$depotoire4,
                        debut4=$debut4,
                        fin4=$fin4
	                    WHERE id = $idcam ";
    $this->db->query($strSQL);

  }
  /**
  *
  * @param string $dteFrom
  * @param string $dteTo
  *
  * @return string
  */
  public function getAvisVerif($dteFrom, $dteTo, $typecopie)
  {
    /** @var string $nbday */
    $dteFrom = $this->db->escape($dteFrom);
    $dteTo = $this->db->escape($dteTo);
    /** @var string $returnval */
    $returnval = '';
    $strWhere = (($typecopie == 1)?  " dateend = $dteTo " : " dateend BETWEEN  DATE_SUB($dteTo,INTERVAL 2 DAY) AND DATE_ADD($dteTo,INTERVAL 6 DAY)" );
    /** @var string $strSQL */
    $strSQL = "SELECT suivisrh.*,
			                (SELECT CONCAT(reshumaine.Prenom, ' ', reshumaine.Nom ) FROM reshumaine WHERE reshumaine.id = suivisrh.idrh) AS Nom
			                FROM suivisrh WHERE $strWhere AND showavis = 1 order by
			                (SELECT reshumaine.Nom FROM reshumaine WHERE reshumaine.id = suivisrh.idrh)";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {

      $returnval .= '<tr >
                            <td ><input class="form-control" style="width: 320px; background-color:black;color:white;" type="text" value="'.$row->Nom.'" readonly /></td></tr><tr >
												<td ><textarea class="form-control"  style="width:320px;font-size:small;font-weight:bold;color:darkblue;" rows="2" readonly >'.$row->avistoshow.'</textarea></td>
										</tr>';
    }
    return $returnval;
  }
  /**
   *
   * @param int $chkRearEnd
   * @param int $chkRobot
   * @param int $TypeCopy
   * @param string $dteFrom
   * @param string $dteTo
   *
   * @return object
   */
  public function CopyCeduleTo($chkRearEnd, $chkRobot, $TypeCopy, $dteFrom, $dteTo)
  {
    $dteFronEsc = $this->db->escape($dteFrom);
    $dteToEsc = $this->db->escape($dteTo);
    $typecedule = '';
    $strWhereCD = '';
    /** @var string $strWhere */
    $strWhere = '';
    if ($chkRearEnd == 1)
    {


      $strWhere = " TypeCedule = 1 ";





      $typecedule =  " cedules.TypeCedule = 1 ";

    }
    else if($chkRobot == 1)
    {
      $strWhere = " TypeCedule = 2  ";
      $typecedule =  " cedules.TypeCedule = 2 ";
    }
    else
    {
      return FALSE;
    }
    $strWhere .= (($TypeCopy == 1)?  " AND datejour = $dteFronEsc " : " AND datejour BETWEEN  $dteFronEsc AND DATE_ADD($dteFronEsc,INTERVAL 7 DAY)" );
    $strWhereCD = $typecedule.(($TypeCopy == 1)?  " AND cedules.DateJour = $dteFronEsc " : " AND cedules.DateJour BETWEEN  $dteFronEsc AND DATE_ADD($dteFronEsc,INTERVAL 7 DAY)" );
    $strSQL = "INSERT INTO grp_cedules
	                  (datejour, idvilles, idsecteurs, oldid, PosX, PosY, Largeur, Hauteur, ACol, ARow, TypeCedule, nb_cam)
                  SELECT DATE_ADD(datejour, INTERVAL DATEDIFF($dteToEsc, $dteFronEsc) DAY),
		                  idvilles, idsecteurs, id, PosX, PosY, Largeur, Hauteur, ACol, ARow, grp_cedules.TypeCedule, nb_cam
			                  FROM grp_cedules WHERE $strWhere ORDER BY datejour";
    /** @var object $resultLine */
    $resultLine = $this->db->query($strSQL);
    $strSQL = " INSERT INTO cedules (CodeUtil,  DateJour,
		TypeDeProduit, idvilles, NOCAMION, Secteur, idchauffeurs, HELPERS_1, olddgrp, cedules.TypeCedule, idgrp)
              SELECT cedules.CodeUtil,  DATE_ADD(cedules.DateJour, INTERVAL DATEDIFF($dteToEsc, $dteFronEsc) DAY),
		              cedules.TypeDeProduit, cedules.idvilles, cedules.NOCAMION, cedules.Secteur,
						  cedules.idchauffeurs, cedules.HELPERS_1, idgrp, cedules.TypeCedule,
		              grp_cedules.id
	              FROM cedules INNER JOIN grp_cedules ON cedules.idgrp = grp_cedules.oldid
					  WHERE  $strWhereCD ORDER BY cedules.DateJour ";

    $resultLine = $this->db->query($strSQL);
    //-------------------------------------------------------------------------------
    $dteStartDel = $this->db->escape($dteTo);
    $dteEndDel = new \DateTime( $dteTo );
    date_add($dteEndDel,date_interval_create_from_date_string("6 days"));
    $dteEndDel = $this->db->escape(date_format($dteEndDel,"Y-m-d") );
    //-----------------------------------------------------------------
    $strSQL = "Update cedules set idchauffeurs = 0 where (DateJour BETWEEN $dteStartDel and $dteEndDel) and  idchauffeurs in
                      ( select idrh from suivisrh where
			                ( ( cedules.DateJour BETWEEN datestart AND date_sub(dateend, INTERVAL 1 DAY)  or dateend is null)   OR
	                    ( CASE DAYOFWEEK(DateJour)
                        WHEN 2 THEN
                          idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.LUN = 0)
                        WHEN 3 THEN
                        idchauffeurs in 	( Select reshumaine.id from reshumaine where reshumaine.MAR = 0)
                        WHEN 4 THEN
                        idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.MER = 0)
                        WHEN 5 THEN
                        idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.JEU = 0)
                        WHEN 6 THEN
                        idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.VEN = 0)
                        ELSE
                        idchauffeurs in (0)
                      END ) or ( idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.isdeleted = 1) )))";
    $this->db->query($strSQL);
    //-----------------------------------------------------------------------
    $strSQL = "Update cedules set HELPERS_1 = 0 where (DateJour BETWEEN $dteStartDel and $dteEndDel) and  HELPERS_1 in
                      ( select idrh from suivisrh where
			                ( ( cedules.DateJour BETWEEN datestart AND date_sub(dateend, INTERVAL 1 DAY)  or dateend is null)   OR
	                    ( CASE DAYOFWEEK(DateJour)
                        WHEN 2 THEN
                          HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.LUN = 0)
                        WHEN 3 THEN
                        HELPERS_1 in 	( Select reshumaine.id from reshumaine where reshumaine.MAR = 0)
                        WHEN 4 THEN
                        HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.MER = 0)
                        WHEN 5 THEN
                        HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.JEU = 0)
                        WHEN 6 THEN
                        HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.VEN = 0)
                        ELSE
                        HELPERS_1 in (0)
                      END ) or ( HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.isdeleted = 1) )))";
    $this->db->query($strSQL);
    //-----------------------------------------------------------------------
    $strSQL = "Update cedules set NOCAMION = '' where DateJour BETWEEN $dteStartDel and $dteEndDel  and ((NOCAMION IN
                        ( SELECT nocamion FROM suiviscamion where
                          (( cedules.DateJour BETWEEN datestart AND date_sub(dateend, INTERVAL 1 DAY) )   or dateend is null) and idstatut = 1))
                        or NOCAMION IN (SELECT NoCamion FROM camions WHERE isdeleted = 1))";
    $this->db->query($strSQL);
    //-----------------------------------------------------------------------
    if ($TypeCopy == 2)
    {
      /** @var string $newDate */
      $date=date_create($dteFrom);
      $dateto=date_create($dteTo);
      $diff=date_diff($date,$dateto);
      $nbDay = $diff->format('%a');
      date_add($date,date_interval_create_from_date_string($diff->format('%a')." days"));
      for ($iter = 1; $iter < 8;$iter++)
      {
        date_add($date,date_interval_create_from_date_string("1 days"));
        $dateesc= $this->db->escape($date->format('Y-m-d'));
        $strSQL = "UPDATE grp_cedules
	                  SET
		                  oldid=0
	                  WHERE datejour =  $dateesc";
        /** @var object $resultLine */
        $resultLine = $this->db->query($strSQL);
      }
    }
    else
    {
      $strSQL = "UPDATE grp_cedules
	                  SET
		                  oldid=0
	                  WHERE datejour = DATE_ADD($dteToEsc, INTERVAL DATEDIFF($dteToEsc, $dteFronEsc) DAY) ";
      /** @var object $resultLine */
      $resultLine = $this->db->query($strSQL);
    }
    //--------------------------------------------------------------------------
    return ($resultLine);
  }

  /**
   *
   * @param int $chkRearEnd
   * @param int $chkRobot
   * @param int $TypeCopy
   * @param string $dteFrom
   * @param string $dteTo
   *
   * @return bool
   */
  public function execCopyCedule($chkRearEnd, $chkRobot, $TypeCopy, $dteFrom, $dteTo)
  {
    $dteFronEsc = $this->db->escape($dteFrom);
    $typecedule = '';
    /** @var string $strWhere */
    $strWhere = '';
    if ($chkRearEnd == 1)
    {
      if($chkRobot == 1)
      {
        $strWhere = " TypeCedule IN ( 1, 2) ";
        $typecedule =  " TypeCedule IN ( 1, 2) ";
      }
      else
      {
        $strWhere = " TypeCedule IN ( 1 ) ";
        $typecedule =  " TypeCedule IN ( 1) ";
      }
    }
    else if($chkRobot == 1)
    {
      $strWhere = " TypeCedule IN ( 2 ) ";
      $typecedule =  " TypeCedule IN ( 2) ";
    }
    else
    {
      return FALSE;
    }
    $strWhere .= (($TypeCopy == 1)?  " AND datejour = $dteFronEsc " : " AND datejour BETWEEN  DATE_ADD($dteFronEsc,INTERVAL 1 DAY) AND DATE_ADD($dteFronEsc,INTERVAL 6 DAY)" );
    $strSQL ="SELECT  grp_cedules.datejour, idvilles, ACol, ARow, TypeCedule, grp_cedules.id
                    FROM grp_cedules
	                WHERE $strWhere
	                        ORDER BY grp_cedules.datejour , grp_cedules.TypeCedule,grp_cedules.ARow";
    /** @var object $resultLine */
    $resultLine = $this->db->query($strSQL);
    /** @var string $newDate */
    $date=date_create($dteTo);
    $dteStartDel = $this->db->escape($dteTo);
    $dteEndDel = new \DateTime( $dteTo );
    date_add($dteEndDel,date_interval_create_from_date_string("6 days"));
    $dteEndDel = $this->db->escape(date_format($dteEndDel,"Y-m-d") );
    if ($TypeCopy == 2)
    {
      date_add($date,date_interval_create_from_date_string("1 days"));
    }
    $newDate = date_format($date,"Y-m-d") ;
    /** @var string $copyDate */
    $copyDate = '';
    /** @var int $iter */
    $iter = 1;
    /** @var int $idville */
    $idville = 0;
    /** @var int $newidgrp */
    $newidgrp = 0;
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      $copyDate = (($copyDate == '')? substr( $row->datejour,0,10) : $copyDate );
      if ( ($TypeCopy == 2) )
      {
        $testDate = substr( $row->datejour,0,10);
        if ($testDate != $copyDate)
        {
          $copyDate = substr( $row->datejour,0,10);
          $date=date_create($newDate);
          date_add($date,date_interval_create_from_date_string("1 days"));
          $newDate = date_format($date,"Y-m-d") ;
          $iter++;
        }
      }
      else if ($TypeCopy == 1)
      {
        $newDate =$dteTo;
      }
      if($idville != $row->idvilles)
      {
        $strSQL = "INSERT INTO grp_cedules
	                (datejour, idvilles, ACol, ARow, TypeCedule, nb_cam)
                SELECT ".$this->db->escape($newDate).",idvilles, ACol, ARow, TypeCedule, nb_cam
	                FROM grp_cedules WHERE id = ".$this->db->escape($row->id);
        $this->db->query($strSQL);
        $strSQL = 'SELECT LAST_INSERT_ID() as id';

        $result = $this->db->query($strSQL);
        foreach($result->getResult() as $rowID)
        {
          $newidgrp = $this->db->escape($rowID->id);
          $idville = $row->idvilles;
          $strSQL ="SELECT ID FROM cedules WHERE DateJour = ".$this->db->escape($row->datejour)."  AND $typecedule  AND idvilles = $idville order by ID";
          /** @var object $resultCopy */
          $resultCopy = $this->db->query($strSQL);
          foreach($resultCopy->getResult() as $rowIDCedule)
          {

            $testCheck = $this->input->post_get('chkCam') ;
            $chkCam = ((isset($testCheck))? 'NOCAMION' : $this->db->escape('') );
            $testCheck = $this->input->post_get('chkChauffeur') ;
            $chkChauf = ((isset($testCheck))? 'idchauffeurs' : '0' );
            $testCheck = $this->input->post_get('chkAide') ;
            $chkAide = ((isset($testCheck))? 'HELPERS_1' : '0' );
            $testCheck = $this->input->post_get('chkRamassage') ;
            $chkRamassage = ((isset($testCheck))? 'Secteur' : 'NULL' );
            $strSQL = "INSERT INTO cedules
	                                (CodeUtil, DateJour, Secteur, NOCAMION, idchauffeurs, idvilles, idsecteur, TypeCedule, idgrp, HELPERS_1, HELPERS_2)
	                                SELECT CodeUtil, ".$this->db->escape($newDate).", $chkRamassage, $chkCam, $chkChauf,  idvilles,  idsecteur, typecedule,
                                  $newidgrp , $chkAide, HELPERS_2
	                                FROM cedules WHERE ID = ".$this->db->escape($rowIDCedule->ID);
            $this->db->query($strSQL);
          }
        }

      }
      else
      {
        $strSQL = "INSERT INTO cedules
	                              (CodeUtil, DateJour, TypeDeProduit, NOCAMION, idchauffeurs, idvilles, idsecteur, TypeCedule, idgrp, HELPERS_1, HELPERS_2)
	                              SELECT CodeUtil, ".$this->db->escape($newDate).", TypeDeProduit, NOCAMION, idchauffeurs, idvilles, idsecteur, typecedule,
                                $newidgrp , HELPERS_1, HELPERS_2
	                              FROM cedules WHERE id = ".$this->db->escape($row->id);
        $this->db->query($strSQL);
      }
      $activedate = $this->db->escape($newDate);
      $strSQL = "Update cedules set idchauffeurs = 0 where ( ( idchauffeurs in
                      ( select idrh from suivisrh where datestart <= $dteStartDel
			                and ( dateend >= $dteEndDel or dateend is null) ) ) OR
	                    ( CASE DAYOFWEEK(DateJour)
                        WHEN 2 THEN
                          idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.LUN = 0)
                        WHEN 3 THEN
                        idchauffeurs in 	( Select reshumaine.id from reshumaine where reshumaine.MAR = 0)
                        WHEN 4 THEN
                        idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.MER = 0)
                        WHEN 5 THEN
                        idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.JEU = 0)
                        WHEN 6 THEN
                        idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.VEN = 0)
                        ELSE
                        idchauffeurs in (0)
                      END ) )
                      and DateJour BETWEEN $dteStartDel and $dteEndDel";
      $this->db->query($strSQL);
      //-----------------------------------------------------------------------
      $strSQL = "Update cedules set HELPERS_1 = 0 where ( ( HELPERS_1 in
                      ( select idrh from suivisrh where datestart >= $dteStartDel
			                and ( dateend >= $dteEndDel or dateend is null) ) ) OR
	                    ( CASE DAYOFWEEK(DateJour)
                        WHEN 2 THEN
                          HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.LUN = 0)
                        WHEN 3 THEN
                        HELPERS_1 in 	( Select reshumaine.id from reshumaine where reshumaine.MAR = 0)
                        WHEN 4 THEN
                        HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.MER = 0)
                        WHEN 5 THEN
                        HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.JEU = 0)
                        WHEN 6 THEN
                        HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.VEN = 0)
                        ELSE
                        HELPERS_1 in (0)
                      END ) )
                      and DateJour BETWEEN $dteStartDel and $dteEndDel";
      $this->db->query($strSQL);
      //-----------------------------------------------------------------------
      $strSQL = "Update cedules set NOCAMION = '' where NOCAMION IN
                        ( SELECT nocamion FROM suiviscamion where datestart <= $dteStartDel
                          and ( dateend >= $dteEndDel or dateend is null)) and DateJour BETWEEN $dteStartDel and $dteEndDel";
      $this->db->query($strSQL);
      //-----------------------------------------------------------------------
    }
    if ($TypeCopy == 1)
    {
      $activedate = $this->db->escape($dteTo);
      $strSQL = "Update cedules set idchauffeurs = 0 where ( ( idchauffeurs in
                       ( select idrh from suivisrh where datestart <= $dteStartDel
			                  and ( dateend >= $dteEndDel or dateend is null) ) ) OR
	                     ( CASE DAYOFWEEK(DateJour)
                         WHEN 2 THEN
                           idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.LUN = 0)
                          WHEN 3 THEN
                          idchauffeurs in 	( Select reshumaine.id from reshumaine where reshumaine.MAR = 0)
                         WHEN 4 THEN
                          idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.MER = 0)
                         WHEN 5 THEN
                          idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.JEU = 0)
                         WHEN 6 THEN
                          idchauffeurs in ( Select reshumaine.id from reshumaine where reshumaine.VEN = 0)
                         ELSE
                          idchauffeurs in (0)
                        END ) )
                        and DateJour BETWEEN $dteStartDel and $dteEndDel";
      $this->db->query($strSQL);
      //-----------------------------------------------------------------------
      $strSQL = "Update cedules set HELPERS_1 = 0 where ( ( HELPERS_1 in
                       ( select idrh from suivisrh where datestart <= $dteStartDel
			                  and ( dateend >= $dteEndDel or dateend is null) ) ) OR
	                     ( CASE DAYOFWEEK(DateJour)
                         WHEN 2 THEN
                           HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.LUN = 0)
                          WHEN 3 THEN
                          HELPERS_1 in 	( Select reshumaine.id from reshumaine where reshumaine.MAR = 0)
                         WHEN 4 THEN
                          HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.MER = 0)
                         WHEN 5 THEN
                          HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.JEU = 0)
                         WHEN 6 THEN
                          HELPERS_1 in ( Select reshumaine.id from reshumaine where reshumaine.VEN = 0)
                         ELSE
                          HELPERS_1 in (0)
                        END ) )
                        and DateJour BETWEEN $dteStartDel and $dteEndDel";
      $this->db->query($strSQL);
      //-----------------------------------------------------------------------
      $strSQL = "Update cedules set NOCAMION = '' where NOCAMION IN
                          ( SELECT nocamion FROM suiviscamion where datestart <= $dteStartDel
                            and ( dateend >= $dteEndDel or dateend is null) ) and DateJour BETWEEN $dteStartDel and $dteEndDel";
      $this->db->query($strSQL);
    }
    return TRUE;
  }
  public function postDiction($id, $notype, $isdel, $nom, $champ1, $champ2, $champ3, $champ4,$champ5 = '', $issanivan= '')
  {
    $id= $this->db->escape($this->input->get('id'));
    switch($notype)
    {
		  case 1:
		  case 2:
		  case 5:
			  $nom = $this->db->escape($this->input->get('nom'));

        $strSQL = "SELECT * FROM ".$this->table[$notype - 1]. " WHERE  id = $id";
        $query = $this->db->query($strSQL);
        $row = $query->getRow();
        if (!isset($row))
        {

          /** @var string  $strSQL */
          $strSQL = "INSERT INTO ".$this->table[$notype - 1]. " (Nom) VALUES ($nom)";
          $this->db->query($strSQL);
        }
        else
        {
          /** @var string  $strSQL */
          $strSQL = "UPDATE  ".$this->table[$notype - 1]. " SET Nom=$nom, isdeleted=$isdel WHERE  id = $id";
          $this->db->query($strSQL);
        }
			  break;
      case 3:
      case 4:
			  $nom = $this->db->escape($this->input->get('nom'));
			  $couleur = $this->db->escape('#'.$this->input->get('couleur'));
			  $textcolor = $this->db->escape('#'.$this->input->get('textcolor'));
        $strSQL = "SELECT * FROM ".$this->table[$notype - 1]. " WHERE  id = $id";
        $query = $this->db->query($strSQL);
        $row = $query->getRow();
        if (!isset($row))
        {

          /** @var string  $strSQL */
          $strSQL = "INSERT INTO ".$this->table[$notype - 1].
                      " (Nom, couleur, textcolor) VALUES ($nom, $couleur, $textcolor)";
          $this->db->query($strSQL);
          // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
        }
        else
        {
          /** @var string  $strSQL */
          $strSQL = "UPDATE  ".$this->table[$notype - 1].
                              " SET Nom=$nom,
                                    isdeleted=$isdel,
                                    couleur = $couleur,
                                    textcolor = $textcolor
                                WHERE  id = $id";
          $this->db->query($strSQL);
          // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
        }
			  break;
		  
		  case 6:
			  $nom = $this->db->escape($this->input->get('nom'));

        $strSQL = "SELECT * FROM ".$this->table[$notype - 1]. " WHERE  id = $id";
        $query = $this->db->query($strSQL);
        $row = $query->getRow();
        if (!isset($row))
        {

          /** @var string  $strSQL */
          $strSQL = "INSERT INTO ".$this->table[$notype - 1]. " (Nom) VALUES ($nom)";
          $this->db->query($strSQL);
        }
        else
        {
          /** @var string  $strSQL */
          $strSQL = "UPDATE  ".$this->table[$notype - 1]. " SET Nom = $nom, isdeleted=$isdel WHERE  id = $id";
          $this->db->query($strSQL);
        }
			  break;
		  case 8:
      case 12:
			  $nom = $this->db->escape($this->input->get('nom'));

        $strSQL = "SELECT * FROM ".$this->table[$notype - 1]. " WHERE  id = $id";
        $query = $this->db->query($strSQL);
        $row = $query->getRow();
        if (!isset($row))
        {

          /** @var string  $strSQL */
          $strSQL = "INSERT INTO ".$this->table[$notype - 1]. " (nom) VALUES ($nom)";
          $this->db->query($strSQL);
        }
        else
        {
          /** @var string  $strSQL */
          $strSQL = "UPDATE  ".$this->table[$notype - 1]. " SET nom = $nom, isdeleted=$isdel WHERE  id = $id";
          $this->db->query($strSQL);
        }
			  break;
		  case 7:
			  $nom = $this->db->escape($nom);
        $typedepot = $this->db->escape($champ1);
        $prix = $this->db->escape($champ2);
        $prix_sani = $this->db->escape($champ5);
        $redevance = $this->db->escape($champ3);
        $adresse = $this->db->escape($champ4);
        $issanivan = $this->db->escape($issanivan);
        $strSQL = "SELECT * FROM ".$this->table[$notype - 1]. " WHERE  id = $id";
        $query = $this->db->query($strSQL);
        $row = $query->getRow();
        if (!isset($row))
        {

          /** @var string  $strSQL */
          $strSQL = "INSERT INTO  ".$this->table[$notype - 1]. "
	                          (nom, prix, redevance,adresse,TypeDepot, prix_sani, issanivan)
	                          VALUES ($nom, $prix, $redevance,$adresse,$typedepot, $prix_sani, $issanivan)";
          $this->db->query($strSQL);
        }
        else
        {
          /** @var string  $strSQL */
          $strSQL = "UPDATE  ".$this->table[$notype - 1]. "
	                          SET
		                          TypeDepot=$typedepot,
		                          prix=$prix,
		                          prix_sani=$prix_sani,
		                          redevance=$redevance,
                              adresse=$adresse,
                              nom=$nom,
                              issanivan=$issanivan,
		                          isdeleted=$isdel
	                          WHERE  id = $id";
          $this->db->query($strSQL);
        }
			  break;
		  case 9:
			  $typeitem = $this->db->escape($nom);
        $datestart = $this->db->escape($champ1);
        $prix = $this->db->escape($champ2);
        $dateend = $this->db->escape($champ3);
        $code = $this->db->escape($champ4);
        $strSQL = "SELECT * FROM ".$this->table[$notype - 1]. " WHERE id = $id";
        $query = $this->db->query($strSQL);
        $row = $query->getRow();
        if (!isset($row))
        {

          /** @var string  $strSQL */
          $strSQL = "INSERT INTO  ".$this->table[$notype - 1]. "
	                          (typeitem, prix, datestart,dateend)
	                          VALUES ($typeitem, $prix, $datestart,$dateend)";
          $this->db->query($strSQL);
        }
        else
        {
          /** @var string  $strSQL */
          $strSQL = "UPDATE  ".$this->table[$notype - 1]. "
	                          SET
		                          typeitem=$typeitem,
		                          prix=$prix,
		                          datestart=$datestart,
                              dateend=$dateend,
		                          isdeleted=$isdel
	                          WHERE  id = $id";
          $this->db->query($strSQL);
        }
			  break;
    }
  }
  public function checkNewInHS()
  {
    $isuser= $this->db->escape( $this->session->userdata('user_id') );
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "select *,
                        (Select typecamion.Nom From typecamion where typecamion.id =
					                        (Select camions.TypeCamion From camions where camions.NoCamion = suiviscamion.nocamion ) ) As TypeCam,
					              (Select COUNT(camions.TypeCamion) From camions WHERE camions.TypeCamion = 1 and camions.NoCamion in
					              (SELECT sc.nocamion  from suiviscamion sc WHERE sc.idstatut = 1 and
                                      sc.datecreate >
                                      ( SELECT datecreate FROM messages where iduser = 1 ) ) )  As nbRear,
					              (Select COUNT(camions.TypeCamion) From camions WHERE camions.TypeCamion = 2 and camions.NoCamion in
					              (SELECT sc.nocamion  from suiviscamion sc WHERE sc.idstatut = 1 and
                        sc.datecreate >
                        ( SELECT datecreate FROM messages where iduser = $isuser ) ) )  As nbRobot
                         from suiviscamion where idstatut = 1 and
                                                datecreate >
                        ( SELECT datecreate FROM messages where iduser = $isuser ) order by nocamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $this->data['nbRearHS'] = $row->nbRear;
      $this->data['nbRobotHS'] = $row->nbRobot;
      $returnval .= '<tr>
                <td align="center"><label style="width: 100px;color: darkred;font-weight:bold;font-size: medium;">'.$row->nocamion.'</label ></td>
                            <td align="center"><label style="width: 130px;">'.substr( $row->datestart,0,10).'</label ></td>
                            <td align="center"><label  style="width: 130px;color: darkblue;font-weight:bold;font-size: medium;">'.$row->TypeCam.'</label ></td>
                            <td align="center"><label  style="width: 130px;">'.substr( $row->dateretourprevu,0,10).'</label ></td>
                            <td><label  style="width: 300px;background-color:yellow;color: black; font-weight:bold;">'.((trim($row->dateend) != '')? 'Ce Camion est de retour en service.' : '').'</label ><textarea rows="1" style="width: 300px;">'.$row->note.'</textarea ></td>
												</tr>';
    }
    $this->data['listhsnew'] = $returnval;
    return $this->data ;
  }
  public function getCalendrierVille($idville, $activeDate = '')
	{
    /** @var string $returnval */
    $returnval = '';
		$intDayOfMonth = date('t');
		$today = new \DateTime('NOW');
		$DayOfWeek = date('Y').'-'.date('m').'-01';
		$SelectedDate = date('Y').'-'.date('m').'-01';
		//date_add($today,date_interval_create_from_date_string("1 month"));
		$dateToScroll = date_create($SelectedDate);
		$monthtoshow = date('m');
    $intMonth = date('n');
    $activeMonth = $intMonth;
		$intYear = date('Y');
		$activeYear = $intYear;
		if ( $activeDate != '' )
    {
			$arrdate = explode('-', $activeDate );
			$monthtoshow = $arrdate[1];
      $activeMont = intval($arrdate[1]);
      $activeYear = intval($arrdate[0]);
			$newdate = new \DateTime($activeDate.'-01');
			$intDayOfMonth = $newdate->format('t');
			$DayOfWeek =  $newdate->format('Y-m-d');
			$SelectedDate = $newdate->format('Y-m-d');
			$dateToScroll = date_create($SelectedDate);
		}
		$scriptQuery = '<script>
					$.fx.speeds._default = 100;

					$(function() {
						$( "#%s" ).dialog({
							autoOpen: false,
							show: "blind",
							width: 600,
							height:450,
							 modal: true
						});

						$( "#%s" ).click(function() {
							$( "#%s" ).dialog({
							autoOpen: false,
							show: "blind",
							width: 600,
							height:450,
							 modal: true
						});
							$( "#%s" ).dialog( "open" );
							return false;
						});
					});
					</script>
					<div id="%s" style="x-large" title="%s">

					</div>
					';
		//-----------------------------------------------------------------
		$array = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet",
															"Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
		$listMois = '';
		$listYear = '';

    for ($iter = 1; $iter< 13; $iter++)
    {
      $tmp = (($iter == intval($monthtoshow))? ' selected ' : '');
      $listMois .= '<option ' .$tmp .' value="' .(($iter < 10)? '0'.$iter : $iter) .'">' .$array[$iter - 1].'</option>';
    }
    for ($iter = $intYear; $iter < $intYear + 4; $iter++)
    {
      $tmp = (($iter == $activeYear)? ' selected' : '');
      $listYear .= '<option ' .$tmp .' value="' .$iter.'">' .$iter.'</option>';
    }
		$this->data['listmois'] = $listMois;
		$this->data['listyear'] = $listYear;
		$returnval = '<form name="frmCalendrier" id="frmCalendrier" method="post" action="">

			<table class="tableCalendrier" >

				<tr><td  class="tdcalendrierHeader">Dimanche</td>
				<td  class="tdcalendrierHeader">Lundi</td><td class="tdcalendrierHeader">Mardi</td>
					<td class="tdcalendrierHeader">Mercredi</td><td class="tdcalendrierHeader">Jeudi</td>
					<td class="tdcalendrierHeader">Vendredi</td><td class="tdcalendrierHeader">Samedi</td>
				</tr>';
		//$returnval = '' ;
		$DayStart = 1;
		$dateMonth = new \DateTime($DayOfWeek);
		$DayOfWeek = date_format($dateMonth, 'w');
		if (($DayOfWeek == 0) || ( $DayOfWeek == 6)) {
			//
			if ( $DayOfWeek == 6) {
				$DayStart = 2;
			}
			else
				$DayStart = 1;
			$DayOfWeek = 0;
		}
		for ($iterLigne = 1; $iterLigne < 7; $iterLigne++)
		{
			$returnval .= '<tr>' ;
			if ( $DayOfWeek > 0 ) {
				for ($iter = 0; $iter < $DayOfWeek; $iter++)
				{
					$returnval .= '<td valign="top" class="tdcalendrier"> <table> <tr>
														<td valign="top" align="right" >
														<input type="text" readonly="readonly" class="tdjour" value="" /> </td> </tr>';

          $returnval .= '<tr>
														<td class="tdinfo">&nbsp;&nbsp;&nbsp;&nbsp;</td>
													</tr></table> 	</td>' ;
				}
			}

			for ($iter = $DayOfWeek; $iter <= 6; $iter++)
			{
				$returnval .= '<td valign="top" class="tdcalendrier"> <table> <tr>
														<td valign="top" align="right" >
														<input type="text" readonly="readonly" class="tdjour" value="'.$DayStart.'" /> </td> </tr>';
				$strSQL = "SELECT tblvillesdateramassage.Id, idville, Dateramassage, TypeRamassage,
															ville.rbCamionrecyclageRE, ville.rbCamiondechetsRE,
															ville.rbCamionorganiqueRE, ville.rbCamiongrosrebutsRE,
															ville.rbCamionrecyclageROBOT, ville.rbCamiondechetsROBOT,
															ville.rbCamionorganiqueROBOT, ville.rbCamiongrosrebutsROBOT
													FROM tblvillesdateramassage INNER JOIN ville
															ON tblvillesdateramassage.idville = ville.id
															WHERE idville = $idville AND MONTH(Dateramassage) = MONTH('$SelectedDate')
																									AND YEAR(Dateramassage) = YEAR('$SelectedDate')
																									and DAY(Dateramassage) = $DayStart order by tblvillesdateramassage.Id";
				$calendrier = $this->db->query($strSQL);
				$num_row = $calendrier->getNumRows();
				$iterRow = 1;
				if ($num_row > 0)
        {
					foreach($calendrier->getResult() as $row)
					{
						$titre = '';
						$color = '';
						switch ($row->TypeRamassage)
            {
              case 1:
								$color = '#E5E5E5';
                $titre = 'Nb RE: '. $row->rbCamiondechetsRE.', Nb RO: ' .$row->rbCamiondechetsROBOT;
                break;
              case 2:
								$color = '#9FF3F9';
                $titre = 'Nb RE: '. $row->rbCamionrecyclageRE.', Nb RO: ' .$row->rbCamionrecyclageROBOT;
                break;
              case 3:
								$color = '#ebccaa';
                $titre = 'Nb RE: '. $row->rbCamionorganiqueRE.', Nb RO: ' .$row->rbCamionorganiqueRE;
                break;
              case 4:
								$color = '#2B3B7AF';
                $titre = 'Nb RE: '. $row->rbCamiongrosrebutsRE.', Nb RO: ' .$row->rbCamiongrosrebutsROBOT;
                break;
            }
						$returnval .= '<tr>
								<td class="tdinfo"><a  onclick="showInfoRanmassage('.$row->Id.')" style="font-size:medium;background-color:'.$color.'" id="opener_'.$row->Id.'">'.$titre.'</a></td>
							</tr>' ;
						if (($num_row < 4) and ($iterRow == $num_row))
						{
              $diff=date_diff($dateToScroll,$today);
              $test =$diff->days;
              if($test < 1)
              {
								$returnval .= '<tr>
										<td class="tdinfo">
													<button type="button" onclick="AddRamassage(\''.date_format($dateToScroll,"Y-m-d").'\')" id="btnAddCom" class="btn btn-success" style="height:35px;width:35px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er une nouvelle commande">
																<i class="glyphicon glyphicon-plus"></i>
													</button>
										</td>

									</tr>';
              }
						}
						$iterRow++;
					}
        }
				else
				{
          $diff=date_diff($dateToScroll,$today);
					$test =$diff->days;
          if($test < 1)
          {
						$returnval .= '<tr>
								<td align="center" class="tdinfo">
									<button type="button" '.(($idville == 0)? ' disabled ' : '').' onclick="AddRamassage(\''.date_format($dateToScroll,"Y-m-d").'\')" id="btnAddCom" class="btn btn-success" style="height:35px;width:35px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er une nouvelle commande">
												<i class="glyphicon glyphicon-plus"></i>
									</button>
								</td>
							</tr>';
          }
				}
				$returnval .= '</table></td>' ;
				$DayStart++;
				date_add($dateToScroll,date_interval_create_from_date_string("1 days"));
				if ($DayStart > $intDayOfMonth ) {
					break ;
				}
			}
			$returnval .= '</tr>' ;
			$DayOfWeek = 0;
			//$DayStart += 1;
			if ($DayStart > $intDayOfMonth ) {
				break ;
			}
		}
		$returnval .= '</table></form>';
		$this->data['calendrier'] = $returnval;
		$this->data['optvilles'] = $this->GetOptionVilles($idville);
		return $this->data;
	}
  public function GetOptionVilles($idville = 0)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom, id FROM ville where isdeleted = 0 ORDER BY Nom ";
    $villes = $this->db->query($strSQL);
    foreach($villes->getResult() as $row)
    {
      $selected = (($idville == $row->id)? ' selected ' : '');
      $returnval .= '<option '.$selected.' value="'.$row->id.'" >'.$row->Nom.'</option>';
    }
    return $returnval;
  }
  public function GetOptSecteursPoly($secteur = 0)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT id, name FROM villes_zones order BY name";
    $ville = $this->db->query($strSQL);
    foreach ($ville->getResult() as $row) {
      $selected = (($secteur == $row->id) ? ' selected ' : '');
      $returnval .= '<option ' . $selected . '  value="' . $row->id . '">' . $row->name . '</option>';
    }
    return $returnval;
  }
  public function getListPolyline()
  {
    /** @var string $strSQL */
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT polilyne_ville.id, idville, (SELECT ville.Nom FROM ville WHERE ville.id = polilyne_ville.idville) AS NomVille,
      IFNULL((SELECT secteurs.Nom FROM secteurs WHERE secteurs.id = polilyne_ville.idsecteur), '') AS Secteur,
		       idsecteur, nom, urlfile
	      FROM polilyne_ville ORDER BY (SELECT ville.Nom FROM ville WHERE ville.id = polilyne_ville.idville)";
    $polylines = $this->db->query($strSQL);
    foreach ($polylines->getResult() as $row) {
      $delete = "<a style='font-size:24px;color: #F00;' href='javascript:SupprimerPolyline(" . $row->id . ");' title='Supprimer'><i class='fa fa-times btn btn-danger' aria-hidden='true'></i></a>";
      
    /** @var string $edit */
    $edit = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDiction(' . $row->id . ')">
										    &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';


            $returnval .= '<tr>
                           
                            <td  style="width: 260px;"><label id="lblVille_' . $row->id . '" style="margin-left: 10px;">' . $row->NomVille . '</label></td>

                            <td>
                                <div class="btn-group form-inline" style="width: 210px;">
                                  <div class="input-group input-group-sm pull-left">  
	                            <span class="input-group-btn">
	                              <input type="text" class="form-control form-control-modern" id="Titre_' . $row->id . '"
	                            value="' . htmlspecialchars($row->nom, ENT_QUOTES) . '" style="width:200px; background-color: yellow; font-weight: bold;"/>
	                            ' . $edit . '
	                            </span>

                                  </div>

                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0)" style="margin-left:10px;display:inline-block;" id="lblNom_' . $row->id . '"
                                   onclick="showPolylineSingle(' . $row->id . ', \'' . htmlspecialchars($row->nom, ENT_QUOTES) . '\', ' . $row->idville . ')"
                                   title="Afficher le parcours sur la carte">'
                . htmlspecialchars($row->nom, ENT_QUOTES) .
                '</a>
                            </td>

  <td style="width:60px">&nbsp;&nbsp;' . $delete . '</td>
												</tr>
';
    }
    return $returnval;
  }
  function __destruct() {
		$this->db->close();
	}
}
