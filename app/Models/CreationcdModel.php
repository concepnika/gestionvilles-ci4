<?php

namespace App\Models;

/**
 * Port mecanique de Creationcd_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class CreationcdModel extends Ci3Model
{
  /** @var array $data */
	protected $data = array();
  /** @var object $objData */
  protected $objData;
  protected $JourDeSemaine  = array
                ( 'DIM', 'LUN', 'MAR',
                            'MER', 'JEU', 'VEN', 'SAM' ) ;
  function __construct() {
    parent::__construct();
    
    // $this->remote_db =  // Désactivé - connexion distante bloquée sur WHC
    
    
    $this->objData = new \stdClass();
  }
  /**
   *
   * @param string $dateactive
   * @param int $from
   * @param int $width
   *
   */
  public function LoadCreateToday($dateactive, $from)
  {
    if ($from == 4)
    {
      //$this->LoadCedules($dateactive, $from);
      $typerh = $this->input->post_get('typerh');
      $typerh = ((isset($typerh))? $this->input->post_get('typerh') : '1,3,7');
      $typerh = (($typerh == 'chauf')? '1,3,7' : $typerh);
      $this->GetChauffeursToDrop($dateactive,$typerh);
      $typecam = $this->input->post_get('typecam');
      $typecam = ((isset($typecam))? $this->input->post_get('typecam') : 1);
      $this->GetCamions($this->db->escape($dateactive),$typecam);
      $this->GetAidesToDrop($dateactive);
      $this->GetSecteursToDrop();
      $this->GetTypeProduitsToDrop();
      $this->GetTypeAbsences();
      $this->GridNonDispo();
      $this->GetAllCamions($dateactive, '', $from);
      return $this->data;
      //return  $this->data['createtoday'].'~'.$this->data['typecedule'].'~'.$this->data['menucreate'].'~'.$this->data['ChauffeurDrop'].'~'. $this->data['CamionDrop']
      //          .'~'.$this->data['AideDrop'].'~'.$this->data['SecteurDrop'].'~'.$this->data['non_dispo_cam'].'~'.$this->data['non_dispo_chauf'].'~'.$this->data['non_dispo_aide'];
    }
    else
    {
      //$this->LoadCedules($dateactive, $from);
      $this->GetVilles();
      $typecam = $this->input->post_get('typecam');
      $typecam = ((isset($typecam))? $this->input->post_get('typecam') : 1);
      $typerh = $this->input->post_get('typerh');
      $typerh = ((isset($typerh))? $this->input->post_get('typerh') : '1,3,7');
      $typerh = (($typerh == 'chauf')? '1,3,7' : $typerh);
      $this->GetChauffeursToDrop($dateactive,$typerh);
      $this->GetCamions($this->db->escape($dateactive),$typecam);
      $this->GetAidesToDrop($dateactive);
      $this->GetSecteursToDrop();
      $this->GetTypeProduitsToDrop();
      //$this->GetAidesCal();
      $this->GetAllCamions($dateactive, '', $from);
      //$this->GetChauffeurs();
      $this->GetAllTypeProduits();
      //$this->GetAllEmployes();
      $this->GetTypeAbsences();
      $this->GridNonDispo();
      return $this->data ;
    }

  }
  /**
   *
   * @param object $resultLine
   * @param string $dateactive
   * @param int $from
   *
   * @return string
   */
  private function getLignesCedule($resultLine, $dateactive, $from)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $GrpID */
    $GrpID = '';
    /** @var string $left */
    $left = '';
    /** @var string $top */
    $top = '';
    /** @var int $iterrow */
    $iterrow = 1;
    /** @var string $color */
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      if ($row->TypeDeProduit != 'COMMERCIAL')
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
        if ($GrpID != $row->idgrp)
        {

          switch($row->ARow)
          {
            case 1:
              $top = '2px';
              break ;
            case 2:
              $top = '180px';
              break ;
            case 3:
              $top = '320px';
              break ;
            case 4:
              $top = '500px';
              break ;
            case 5:
              $top = '540px';
              break ;
            case 6:
              $top = '660px';
              break ;
            case 7:
              $top = '770px';
              break ;
            case 8:
              $top = '880px';
              break ;
          }
          $oprang = '<select id="oprang_'.$row->ID.'" onchange="updTableRang('.$row->idgrp.','.$row->ARow.', this.value, \''.$dateactive.'\', '.$row->ACol.')" >';
          for($iter = 1; $iter <= $row->nbRow; $iter++)
          {
            $selected = (($row->ARow == $iter)? ' selected ' : '');
            $oprang .= '<option value="'.$iter.'" '.$selected.' >'.$iter.'</option>';
          }
          $oprang .= '</select>';
          $colorVille = $row->couleurville;
          $nbCamion =   '<input id="nbCamion_'.$row->ID.'"  class="form-control"  style="width:80px;"
                                     onblur="setNbCam(this.value,'.$row->idgrp.')"   type="number" value="'.$row->nb_cam.'"/>';
          $confirmeDelete = "<a class='btn btn-danger btn-sm' style='font-size:14px;color: white;width: 100%' id='btnsupville_".$row->idvilles."'
                                onclick='RemoveVilleCreate(".$row->idgrp.','.$row->ACol.")' title='Suppression de la ville'><i class='fa fa-trash' aria-hidden='true'></i></a>";

          $returnval .= ( (trim($returnval != ''))? '</tbody></table></div></div>' : '' ).'
          <div id="group_'.$row->idgrp.'_'.$row->TypeCedule.'_'.$row->ACol.'_'.$row->ARow.'"   style="width: 300px;margin-top:5px">
            <div class="box-body">
             <table  border="1" style="width: 300px;" cellspacing="2" cellpadding="2" id="tab_logic">
             <tr >
              <th width="60" style="color:black;text-align:center;width: 40px;">'.$oprang.'</i></th>
              <th width="60" style="color:black;text-align:center;width: 60px;">NbCAM: '.$row->NbCam.$nbCamion.'</i></th>
              <th colspan="'.(($row->TypeCedule == 1)? '3' : '2').'" style="color:black;text-align:center;width: 60px;background-color:'.$colorVille.';"><label id="ville_'.$row->idvilles.'_'.$row->ACol.'" class="titre" ondrop="dropville(event,'.$row->idgrp.','.$row->idvilles.','.$row->TypeCedule.')" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event)" style="color: white;background-color:'.$colorVille.'">'. $row->nomville. '</label></th>
              <th width="60" style="color:black;text-align:center;width: 60px;">'.$confirmeDelete.'</th>
              </tr>
             <tr  class="entete">
               <th width="60" style="color:black;text-align:center;width: 60px;"></th>
						  <th width="60" style="color:black;text-align:center;"width: 60px;><label style="width: 60px;">CAM.</label></th>
						  <th width="120" style="color:black;text-align:center;width: 120px;"><label style="width: 120px;">CHAUFFEUR</label></th>';
          if($row->TypeCedule == 1)
          {
            $returnval .=  '<th width="60" style="color:black;text-align:center;width: 120px;"><label style="width: 120px;">'.
                                              (($row->TypeCedule == 1)? 'AIDE' : 'SECTEUR').'</label></th>';
          }

					$returnval .=  '<th width="50" style="color:black;text-align:center;width: 100px;"><label style="width: 100px;">Secteur</label></th>
              <th width="60" style="color:black;text-align:center;width: 60px;"></th></tr>
              <tbody id="trgrpville_'.$row->idvilles.'">';
          $GrpID = $row->idgrp;
          $iterrow++;
        }
        $edit = "<button type='button'
                          class='btn btn-default btn-sm' style='font-size:14px;color: #03F;' xclass='btn-primary btn'
                              onclick=\"ShowLigneCedule('tr_".$row->ID."')\" title='Modifier'><i class='fa fa-pencil-square-o fa-4' aria-hidden='true'></i></button>";
        $returnval .= '<tr><td align="center" class="cellule" style="width: 60px;"><label style="width: 60px;margin-left:5px;">'.(($from == 3 or $from == 5)? '' : $edit ).'</label></td>' ;
        $delete = "<button type=\"button\" class='btn btn-default btn-sm' style='font-size:18px;color: #03F;' xclass='btn-primary btn'
                          title='Supprimer cette Ligne'
                          onclick=\"DeleteLigneFromCreateCedule(".$row->ID.",".$row->idvilles.", ".$row->TypeCedule.")\"  style=\"margin:4px 5px 0 0;\"><i  class=\"fa fa-trash\" aria-hidden='true' style=\"color: red;\"></i></button>";
        $cam = substr($row->NOCAMION ,0, 3);
        $backColor = (($cam == '')? 'background-color: lightcoral;' : '');
        $returnval .=  '<td  class="cellule" align="center" style="width: 60px;"><input type="text" readonly  id="camion_'.$row->NOCAMION.'_'.$row->ID.'" ondrop="dropcam(event,'.$row->ID.')" draggable="true" ondragstart="drag(event)"  ondragover="allowDrop(event)" style="width: 60px;margin-left:5px;font-size: 14px;" value="'.(($cam == '')? '' : $row->NOCAMION ).'"/></td>' ;

        //------------------------ &nbsp;&nbsp;&nbsp;&nbsp;
        $backColor = (($row->chauffeur == '')? 'background-color: lightcoral;' : '');
        $showchauf =  '<input id="chauffeur_'.$row->idchauffeurs.'_'.$row->ID.'" readonly style="width:120px;" draggable="true" ondragstart="drag(event)"  ondrop="dropchauffeur(event,'.$row->ID.')" ondragover="allowDrop(event)"
                                      type="text" value="'.$row->chauffeur.'"/>';
        $returnval .=  '<td id="trchauffeur_'.$row->ID.'" class="cellule" align="left" style="width: 120px;font-size: 14px;"> ' .$showchauf.'</td>';
        if($row->TypeCedule == 1)
        {
          $showaide =   '<input id="aide_'.$row->HELPERS_1.'_'.$row->ID.'" readonly style="width:120px;" draggable="true" ondragstart="drag(event)"  ondrop="dropaide(event,'.$row->ID.')" ondragover="allowDrop(event)"
                                        type="text" value="'.$row->aide1.'"/>';
          $returnval .= '<td  id="traide_'.$row->ID.'" class="cellule" align="left" style="width: 120px;font-size: 14px;">' . $showaide . '</td>';
        }
        $showproduit = '<select class="form-control" onchange="SetProduit(this.id)" id="produit_'.$row->ID.'" style="font-size:small;font-weight:bold;width:90%;background-color:'.$color.'">
                                  <option value="DECHETS">Choisir</option>' .$this-> GetAllProduits($row->TypeDeProduit) . '</select>';
        $showsecteur =   '<input id="secteur_cr_'.$row->ID.'" readonly style="width:120px;" draggable="true" ondragstart="drag(event)"  ondrop="dropaide(event,'.$row->ID.')" ondragover="allowDrop(event)"
                                        type="text" value="'.$row->Secteur.'"/>';
        $returnval .= '<td id="trsecteur_'.$row->ID.'" class="cellule" align="center" style="width: 120px;font-size: 14px;">' .$showsecteur.'</td>
                          <td>'.$delete.'</td></tr>';
      }
    }
    return $returnval . '</tbody></table></div></div>';
  }

  /**
   *
   * @param string $dateactive
   * @param int $from
   * @param int $width
   *
   * @return string
   */
  public function LoadCedules ($dateactive, $from)
  {
    //----------------------------------------------------------------------

    $nocol = 0;
    $menu = '';
    $dteactive = $this->db->escape($dateactive);
    $fromesc = $this->db->escape($from);
    /** @var string $returnval */
    $returnval = '';
    /** @var int $GrpID */
    $iterrow = 1;
    /** @var array $ArrRow */
    $ArrRow = array();
    /** @var string $ColOne */
    $ColOne = '<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>
                  <td class="cellule" align="center">&nbsp;</td>
    							<td class="cellule" align="center">&nbsp;</td>';
    /** @var bool $IsSet */
    $IsSet = false;
    /** @var string $thCedule1 */
    $thCedule1 = '';
    /** @var string $thCedule2 */
    $thCedule2 = '';
    /** @var string $NbRow */
    /** @var string $left */
    $left = '10px';
    /** @var string $NbRow */
    /** @var string $strSQL */
    $strSQL = "SELECT DISTINCT ifnull(ACol, 1) as ACol FROM grp_cedules WHERE datejour = $dteactive ".(($nocol == 0) ? '' : ' And ACol = ' . $nocol)." and TypeCedule = $fromesc ORDER BY ACol;" ;
    /** @var object $result */
    $result = $this->db->query($strSQL);
    $nbCol = $result->getNumRows() ;
    /** @var object $row */
    foreach($result->getResult() as $row)
    {
      if (isset($row->ACol))
      {
        switch($row->ACol)
        {
          case 1:
            $left = '0px';
            break ;
          case 2:
            $left = '550px';
            break ;
          case 3:
            $left = '1102px';
            break ;
          case 4:
            $left = '1765px';
            break ;
        }
        if ($nocol== 0)
        {

          $menu = '	<div style="text-align:center; margin:auto; float:none;">
		        <a class="form-control btn-primary" style="cursor:pointer;max-width:80px" id="showMenuCreate" onclick="PopMenuCreate()" data-trigger="click" data-placement="bottom" tabindex="0" title="Menu" data-html="true" data-content=\'
		        <div>
		        <ul  class="list-group">'.$this->GetMenu($dateactive).'
		        </ul></div>\'>
		        <label  onclick="PopMenuCreate()" style="color: white;cursor:pointer;" aria-hidden="true">Menu</label></a>
	        </div>';

          $thCedule1 = '<div id="col_'.$row->ACol.'"  ondrop="dropcol(event,'.$row->ACol.','.$from.')" ondragover="allowDrop(event)" class="col-md-4"  style="margin-right: '.(($from == 1)? '60px' : '-25px' ).';width: 500px;min-height:800px; padding-bottom:50px;padding-top:10px">
              <div  ondrop="dropcol(event,'.$row->ACol.','.$from.')" ondragover="allowDrop(event)" class="box-header with-border" style="margin-top:10px">
                  Colonne #'.$row->ACol.'
              </div>
              <div  id="body_'.$row->ACol.'" class="box-body">';
        }
        $thCedule2 .=
              '<th width="60" style="color:white;text-align:center;width: 60px;"></th>
						  <th width="60" style="color:white;text-align:center;"width: 60px;><label style="width: 60px;">CAM.</label></th>
						  <th width="120" style="color:white;text-align:center;width: 120px;"><label style="width: 120px;">CHAUFFEUR</label></th>
              <th width="60" style="color:white;text-align:center;width: 120px;"><label style="width: 120px;">AIDE</label></th>
						  <th width="50" style="color:white;text-align:center;width: 100px;"><label style="width: 100px;">Ramassage</label></th>';
        $strSQL ="SELECT c.NOCAMION, c.IsStart,c.ID, c.idchauffeurs, c.HELPERS_1,
                    (SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ) AS NbCam,
                    (SELECT MAX(ARow) as nb from grp_cedules where grp_cedules.datejour = $dteactive AND grp_cedules.ACol = ". $row->ACol."  and typecedule = $from) AS nbRow,
                            (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
													  (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
														  sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dteactive
														  and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
														  ( Select CONCAT (Nom, ', ', LEFT(Prenom, 1) ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
														  ( Select CONCAT (Nom, ', ', LEFT(Prenom, 1) ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
														  IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
														  IFNULL(( Select colorhex FROM ville where id = g.idvilles ), -1)  As couleurville,
														  g.id as idgrp, g.TypeCedule, g.idvilles, c.chaufconfirme, c.helperconfirme, c.IsLocked,
														  c.IsLockedChauffeur, Secteur,
														  c.TypeDeProduit,ifnull(c.idchauffeurs, 0) AS nochauffeur,
                              ifnull(c.HELPERS_1, 0) AS noaide, ifnull(ACol, 1) as ACol, g.ARow
												  from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
												  WHERE g.datejour = $dteactive and ( g.TypeCedule = $from ) AND IFNULL(g.ACol, 1) =". $row->ACol."
												  Order by  g.ARow, g.id";
        /** @var object $resultLine */
        $resultLine = $this->db->query($strSQL);

        $returnval .= $thCedule1.$this->getLignesCedule($resultLine, $dateactive, $from).(($nocol == 0) ? '</div></div>' : '');
      }
      else if (($nbCol == 1) and (!isset($row->ACol)))
      {
        for ($iter = 1; $iter < 4; $iter++)
        {
          switch($iter)
          {
            case 1:
              $left = '0px';
              break ;
            case 2:
              $left = '555px';
              break ;
            case 3:
              $left = '1110px';
              break ;
            case 4:
              $left = '1665px';
              break ;
          }
          $returnval .= '<div id="col_'.$iter.'" class="col-md-4" style="position:absolute;left:'.$left.';min-height:800px;width: 500px; padding-bottom:50px;padding-top:20px">
              <div ondrop="dropcol(event,'.$iter.','.$from.', 0)" ondragover="allowDrop(event)"  class="box-header with-border">
                <h3 class="box-title"><label style="width: 100%;margin-left:5px;font-size: 16px;">Colonne #'.$iter.'</label></h3>
              </div>
              <div class="box-body"></div></div>';
          $nbCol = $iter;
        }
        $nbCol++;
      }
    }
    if (trim($returnval == ''))
    {
      $nbCol = 1;
      for ($iter = 1; $iter < 4; $iter++)
      {
        switch($iter)
        {
          case 1:
            $left = '0px';
            break ;
          case 2:
            $left = '555px';
            break ;
          case 3:
            $left = '1110px';
            break ;
          case 4:
            $left = '1665px';
            break ;
        }
        $returnval .= '<div id="col_'.$iter.'" class="col-md-4" style="position:absolute;left:'.$left.';min-height:800px;width: 500px; padding-bottom:50px;padding-top:20px">
            <div ondrop="dropcol(event,'.$iter.','.$from.', 0)" ondragover="allowDrop(event)"  class="box-header with-border">
              <h3 class="box-title"><label style="width: 100%;margin-left:5px;font-size: 16px;">Colonne #'.$iter.'</label></h3>
            </div>
            <div class="box-body"></div></div>';
        $nbCol = $iter;
      }
      $nbCol++;
    }

    for ($iter = $nbCol + 1; $iter < 4; $iter++)
    {
      switch($iter)
      {
        case 1:
          $left = '0px';
          break ;
        case 2:
          $left = '555px';
          break ;
        case 3:
          $left = '1110px';
          break ;
        case 4:
          $left = '1665px';
          break ;
      }
      $returnval .= '<div id="col_'.$iter.'" class="col-md-4"   style="position:absolute;left:'.$left.';min-height:800px;width: 500px; padding-bottom:50px;padding-top:20px">
          <div ondrop="dropcol(event,'.$iter.','.$from.', 0)" ondragover="allowDrop(event)"  class="box-header with-border">
            <h3 class="box-title"><label style="width: 100%;margin-left:5px;font-size: 16px;">Colonne #'.$iter.'</label></h3>
          </div>
          <div class="box-body"></div></div>';
    }

    $this->data['createtoday'] = '<div class="row">'.$returnval.'</div>';
    $this->objData->createtoday =  '<div class="row">'.$returnval.'</div>';
    $this->objData->typecedule =  $from;
    $this->objData->menucreate =  $menu;
    $this->data['typecedule'] = $from;
    $this->data['menucreate'] = $menu;
    return $this->data['createtoday'];
  }
  /**
   *
   * @param string $dateactive
   * @param int $from
   * @param int $nocol
   *
   * @return string
   */
  public function LoadColCedules ($dateactive, $from, $nocol )
  {
    //----------------------------------------------------------------------
    /** @var string $returnval */
    $returnval = '';
    /** @var string $NbRow */
    /** @var string $left */
    $left = '10px';
    /** @var string $strSQL */
    $strSQL = "SELECT DISTINCT ifnull(ACol, 1) as ACol FROM grp_cedules WHERE datejour = $dateactive And ACol = $nocol ORDER BY ACol;" ;
    /** @var object $result */
    $result = $this->db->query($strSQL);
    $nbCol = $result->getNumRows() ;
    /** @var object $row */
    foreach($result->getResult() as $row)
    {
      if (isset($row->ACol))
      {
        switch($row->ACol)
        {
          case 1:
            $left = '0px';
            break ;
          case 2:
            $left = '555px';
            break ;
          case 3:
            $left = '1115px';
            break ;
          case 4:
            $left = '1665px';
            break ;
        }
        $strSQL ="SELECT c.NOCAMION, c.IsStart,c.ID,
                    (SELECT MAX(ARow) as nb from grp_cedules where grp_cedules.datejour = $dateactive AND grp_cedules.ACol = ". $row->ACol.") AS nbRow,
                            (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
													  (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
														  sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dateactive
														  and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
														  ( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
														  ( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
														  IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
														  IFNULL(( Select Couleur FROM ville where id = g.idvilles ), -1)  As couleurville,
														  g.id as idgrp, c.TypeCedule, g.idvilles, c.chaufconfirme, c.helperconfirme, c.IsLocked,
														  c.IsLockedChauffeur,  c.STARTTIME, c.ENDTIME, c.STARTPERTE, c.ENDPERTE, c.PerteDescription,
														  c.Note, c.TypeDeProduit,c.Tonnage,  ifnull(c.idchauffeurs, 0) AS nochauffeur,
                              ifnull(c.HELPERS_1, 0) AS noaide, ifnull(ACol, 1) as ACol, g.ARow
												  from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
												  WHERE g.datejour = $dateactive and ( g.TypeCedule = $from ) AND IFNULL(g.ACol, 1) =". $row->ACol."
												  and c.TypeCedule = g.TypeCedule   Order by  g.ARow, g.id";
        /** @var object $resultLine */
        $resultLine = $this->db->query($strSQL);

        $returnval .= $this->getLignesCedule($resultLine, $dateactive, $from);
      }
    }
    return $returnval;
  }
  public function GridNonDispo()
  {
    $divNondispoCam = '
                <div id="showCamMat"  class="input-group" style="margin-left:20px">
                      <div class="input-group-btn" style="margin-left:15px;">

                  <a href="/admin"  target="_blank" class="fa fa-truck"  style="font-size:24px;">&nbsp;Avec Mat&eacute;riel</a>&nbsp;</div><div   style="margin-left:10px;padding-left: 5px;">'
                      .$this->LoadCamion(2).'</div></div>';
    $divHSCam = '<div class="input-group" style="margin-left:20px;border: 1px;">
                      <div class="input-group-btn" style="margin-left:15px;">
                    <i class="fa fa-truck" style="font-size:24px;">&nbsp;Camion HS</i>&nbsp;</div><div style="margin-left:10px;padding-left: 5px;">'
                      .$this->LoadCamion(1).'</div></div>';
    $divNondispoChauf = '<div class="input-group" style="margin-left:20px">
                      <div class="input-group-btn" style="margin-left:15px;">
                        <i class="fa fa-user" style="font-size:24px;">&nbsp;Chauffeurs</i>&nbsp;</div><div style="background-color: lightsalmon;margin-left:10px;padding-left: 5px;">'
                      .$this->LoadChauffeurNonDispo('1,3').'</div></div>';
    $divNondispoAide = '<div class="input-group" style="margin-left:20px">
                      <div class="input-group-btn" style="margin-left:15px;">
                    <i class="fa fa-user" style="font-size:24px;">&nbsp;Aides</i>&nbsp;</div><div style="background-color: slateblue;margin-left:10px;padding-left: 5px;">'
                      .$this->LoadChauffeurNonDispo('2').'</div></div>';
    $this->data['non_dispo_cam'] =  $divNondispoCam ;
    $this->objData->non_dispo_cam = $divNondispoCam ;
    $this->data['hs_cam'] =  $divHSCam ;
    $this->data['non_dispo_chauf'] =  $divNondispoChauf ;
    $this->objData->non_dispo_chauf =  $divNondispoChauf ;
    $this->data['non_dispo_aide'] = $divNondispoAide . '<script> $(document).ready(function () {
                                                          $(\'[data-toggle="popover"]\').popover();
                                                            });
                                                            $(document).ready(function () {
                                                              $(\'[data-toggle="popover"]\').popover();
                                                          });
                                                        </script>' ;
    $this->objData->non_dispo_aide = $this->data['non_dispo_aide'];
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
                            (select cam.TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ) as TypeCam
                       FROM statutcamion statutcamion
                            INNER JOIN suiviscamion suiviscamion
                               ON (statutcamion.id = suiviscamion.idstatut)
                               WHERE  suiviscamion.dateend is null
                               and suiviscamion.datestart <= CURDATE()
                               AND statutcamion.id = $statut
                               order by  (select cam.TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ),
                               suiviscamion.nocamion";
    $suiviscamion = $this->db->query($strSQL);
    foreach($suiviscamion->getResult() as $row) {
      if (($row->dateend == '') or (!isset($row->dateend)))
      {
        $note = str_replace('%20', ' ', $row->note);
        $note = str_replace('"', '', $row->note);
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
                                              $note <br/><br/><hr />";
            $returnval .= '<a data-toggle="popover" data-trigger="hover" data-html="true" title="Camion: '.$row->nocamion.' Hors service"   data-placement="bottom" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;margin-right: 10px;'.
                              (($row->TypeCam == 1)? 'color:#da1c23' : 'color:DarkBlue' ).'">'. $row->nocamion.'</a>
                                            <script>$(document).ready(function(){$(\'[data-toggle="tooltip_'.$row->nocamion.'"]\').tooltip();}); </script>, ' ;
            break ;
          case 2:
            $confirmeVider = "<a class='btn btn-danger btn-sm' style='font-size:14px;color: white;' id='btnroll_".$row->id."'
                                onclick='UpdViderMat(this.id)' title='Modifier'><i class='fa fa-check' aria-hidden='true'></i>Confirmer Vider</a>";
            $message = "
                       <b> Depuis : ".substr($row->datestart, 0, 10)." <br/><br/> </b>
                                             <b> Vidange prévu: </b> <br/>
                                              $row->dateretourprevu <br/>
                                             <b> Type de déchet: </b> <br/>
                                              $row->TypeDechet <br/>
                                              <b>Notes: <br/> </b>
                                              $note <br/><br/>$confirmeVider";
            $returnval .= '<a  data-toggle="popover"  data-trigger="click" data-html="true" title="Camion: '.$row->nocamion.' Avec Matériel"   data-placement="top" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;color: white;margin-right: 10px;
'.(($row->TypeCam == 1)? 'background-color: aqua;color:#da1c23' : 'background-color: chocolate;color:white' ).'">'. $row->nocamion.'</a>
                                            <script>$(document).ready(function(){$(\'[data-toggle="tooltip_'.$row->nocamion.'"]\').tooltip();}); </script>, ' ;
            break ;
        }
      }

    }
    $returnval = substr($returnval,0,-2) ;
    return $returnval;
  }
  /**
   *
   * @param int $type
   *
   * @return string
   */
  private function LoadChauffeurNonDispo($type)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select reshumaine.*, suivisrh.* , typeabsence.Nom as nomAbsence, suivisrh.id as idsuivis,
                              CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet
                  From reshumaine INNER JOIN  suivisrh
                        ON reshumaine.id = suivisrh.idrh
                     INNER JOIN typeabsence
         	            ON suivisrh.idabsence = typeabsence.id
                  WHERE ( suivisrh.dateend is null OR suivisrh.dateend > CURDATE() ) and datestart <= CURDATE()
                   AND reshumaine.idreshum in ( $type )";
    $suiviscamion = $this->db->query($strSQL);
    foreach($suiviscamion->getResult() as $row)
    {
      $color = '';
      switch ($type)
      {
        case '2':
          $color = ' lightsalmon';
          break;
        default:
          $color = ' limegreen';
      }

      $note = str_replace('%20', ' ', $row->note);

      $confirmeRetour = "<a class='btn btn-danger btn-sm' style='font-size:14px;color: white;' id='btnroll_".$row->id."'
                          onclick='UpdRetour(this.id)' title='Modifier'><i class='fa fa-check-square-o fa-4' aria-hidden='true'></i>Confirmer Retour</a>";

      $message = "
                  <b> Depuis :$row->datestart <br/><br/></b>
                                        <b> Retour prévu:<br/></b>
                                        $row->dateretourprevu <br/>
                                        <b> Absence pour:<br/></b>
                                        $row->nomAbsence <br/>
                                        <b>Notes: <br/></b>
                                        $note <br/><br/>";
      $returnval .= '<a data-toggle="popover" data-trigger="hover" data-html="true" title="RH: '.$row->NomComplet.' Non Disponible"   data-placement="bottom" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;color: white;">'. $row->NomComplet.'</a>
                                          <script>$(document).ready(function(){$(\'[data-toggle="tooltip_'.$row->idsuivis.'"]\').tooltip();}); </script>, ' ;



    }
    $returnval = substr($returnval,0,-2) ;
    return $returnval;
  }
  public function GetCamions($activedate, $intype = '1')
  {

    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT  IFNULL((select CAST(sc.idstatut as CHAR(10) )
			            from suiviscamion sc where sc.nocamion = c.NoCamion and dateend is null LIMIT 0, 1 ), '1')  as StatutCam ,
( select cdl.NOCAMION from cedules cdl where cdl.DateJour = $activedate
              and cdl.NOCAMION = c.nocamion  and cdl.NOCAMION is not null  LIMIT 0, 1  ) as camionIN,
			            CONCAT( If(c.IsAutomatique = 1, 'A', ''), If(c.IsManuel = 1, 'M', ''), If(c.IsVerseur = 1, 'V', ''),
		               If(c.HaveWinch = 1, 'W', ''), If(c.HaveRollBar = 1, 'R', '')) as CamPropriete,
		              c.NoCamion,c.TypeCamion
FROM camions c
where  c.isdeleted = 0 and c.nocamion <> ''
		              and ( Select sc.nocamion from suiviscamion sc where
		              sc.idstatut = 1 and sc.nocamion = c.nocamion and ( dateend > $activedate or dateend is null )
                  and datestart <= $activedate LIMIT 0, 1 ) is null  AND typecamion <> 4
		              order by TypeCamion, NoCamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $backColor = '';
      if($row->StatutCam == 2)
      {
        if ($row->camionIN != '')
        {
          $backColor = 'background-image: linear-gradient(to right,chocolate 40%,red 40%);color:darkblue;' ;
        }
        else
        {
          $backColor = 'background-color: chocolate;' ;
        }
      }
      else if($row->camionIN != '')
      {
        $backColor = 'color:darkred;font-weight: bold;' ;
      }
      $infotype = (($row->TypeCamion == 1)? 'SANI | ' : 'ROBOT | ');
      $returnval  .= '<div id="camion_'.$row->NoCamion.'_0" draggable="true" ondragstart="drag(event)"  ondrop="dropreturcam(event)"ondragover="allowDrop(event)" class="bg-light-green" style="padding-left: 25px;'.$backColor.'" >'. $infotype. $row->NoCamion .' | '. $row->CamPropriete .'</div>';
    }
    $this->data['CamionDrop'] = $returnval;
    $this->objData->CamionDrop = $returnval;
    return $returnval;
  }

  public function GetHomeCamions($intype = '1')
  {

    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT  IFNULL((select CAST(sc.idstatut as CHAR(10) )
			            from suiviscamion sc where sc.nocamion = c.NoCamion and dateend is null LIMIT 0, 1 ), '1')  as StatutCam ,
			            CONCAT( If(c.IsAutomatique = 1, 'A', ''), If(c.IsManuel = 1, 'M', ''), If(c.IsVerseur = 1, 'V', ''),
		               If(c.HaveWinch = 1, 'W', ''), If(c.HaveRollBar = 1, 'R', '')) as CamPropriete,
		              c.NoCamion FROM camions c where  c.isdeleted = 0 and c.nocamion <> ''
		              AND TypeCamion  in ($intype)
		              order by TypeCamion, NoCamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $backColor = '';
      if($row->StatutCam == 2)
      {
        $backColor = 'background-color: chocolate;color:darkblue;' ;
      }
      $returnval  .= '<div id="camion_'.$row->NoCamion.'_0" draggable="true" ondragstart="drag(event)" class="bg-light-green" style="padding-left: 25px;'.$backColor.'" >'. $row->NoCamion .' | '. $row->CamPropriete .'</div>';
    }
    $this->data['CamionDrop'] = $returnval;
    return $returnval;
  }
  public function GetChauffeursToDropHome($activedate,$intype = '1,3')
  {
    $where = '';
		if ( $this->input->post_get('action') != 2 )
    {
      $date = date_create($activedate);
      $day_of_week = intval($date->format('w'));
      $where = ' and reshumaine.' . $this->JourDeSemaine[$day_of_week].' = 1 ';
    }
    $activedate = $this->db->escape($activedate);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select reshumaine.id as idchauffeur,
                   CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine
			             WHERE  reshumaine.isdeleted = 0 and idreshum in ($intype) $where ORDER BY reshumaine.Nom, reshumaine.Prenom";
    //$strSQL = "Select distinct reshumaine.id as idchauffeur,
    //               CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine INNER JOIN  suivisrh
    //                     ON reshumaine.id = suivisrh.idrh
    //                  INNER JOIN typeabsence
    //                     ON suivisrh.idabsence = typeabsence.id
    //               WHERE ( suivisrh.dateend is null OR suivisrh.dateend < $activedate ) and datestart != $activedate
    //                    AND ( ( select cdl.HELPERS_1 from cedules cdl where cdl.DateJour = $activedate
    //                                 and cdl.HELPERS_1 = reshumaine.id   LIMIT 0, 1 ) is null  )
    //                    AND reshumaine.idreshum IN($intype)";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $returnval  .= '<div id="chauffeur_'.$row->idchauffeur.'_2" draggable="true" ondragstart="drag(event)" class="bg-light-red" style="padding-left: 10px;" >'. $row->NomComplet .'</div>';
    }
    $this->data['ChauffeurDrop'] =  $returnval;
    $this->objData->ChauffeurDrop = $returnval;
    return $returnval;
  }
  public function GetChauffeursToDrop($activedate,$intype = '1')
  {
    $where = '';
    $action = $this->input->post_get('action');
    $action = ((!isset($action))? 1 : $this->input->post_get('action')) ;
      $date = date_create($activedate);
      $day_of_week = $date->format('w');
      $day = $this->JourDeSemaine[$day_of_week];
		if ( $action != 0 )
    {

      $where = ' and reshumaine.' . $this->JourDeSemaine[$day_of_week].' = 1 ';
    }
    $activedate = $this->db->escape($activedate);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $rh = (($intype == '2')? 'cdl.HELPERS_1' : 'cdl.idchauffeurs');
    $lblID = (($intype == '2')? 'aide_' : 'chauffeur_');
    $strSQL = "Select reshumaine.id as idchauffeur,
                  if(reshumaine.$day = 0, 'background-color:lightgoldenrodyellow','') AS background,
                   CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet,
                   ( select $rh from cedules cdl where cdl.DateJour = $activedate
                                     and $rh = reshumaine.id   AND reshumaine.id NOT IN (24, 25)  LIMIT 0, 1 )  as ChaufIn  From reshumaine
			             WHERE
			             ( Select idrh From suivisrh srh
                       WHERE srh.idrh = reshumaine.id AND ( dateend > $activedate  or dateend is null )  and
							  datestart <= $activedate LIMIT 0, 1 ) is null
						            AND reshumaine.idreshum IN ($intype) AND reshumaine.isdeleted = 0 $where ORDER BY reshumaine.Nom, reshumaine.Prenom";
    //$strSQL = "Select distinct reshumaine.id as idchauffeur,
    //               CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine INNER JOIN  suivisrh
    //                     ON reshumaine.id = suivisrh.idrh
    //                  INNER JOIN typeabsence
    //                     ON suivisrh.idabsence = typeabsence.id
    //               WHERE ( suivisrh.dateend is null OR suivisrh.dateend < $activedate ) and datestart != $activedate
    //                    AND ( ( select cdl.HELPERS_1 from cedules cdl where cdl.DateJour = $activedate
    //                                 and cdl.HELPERS_1 = reshumaine.id   LIMIT 0, 1 ) is null  )
    //                    AND reshumaine.idreshum IN($intype)";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $titre = (($row->background == '')? '' : 'En congé');
      $background = (($row->ChaufIn == '')? $row->background : 'color:red');

      $returnval  .= '<div id="'.$lblID.$row->idchauffeur.'_2" title="'.$titre.'" draggable="true" ondragstart="drag(event)"  ondrop="dropreturnemp(event)"ondragover="allowDrop(event)" class="bg-light-red" style="padding-left: 10px;'.$background.'" >'. $row->NomComplet .'</div>';
    }
    $this->data['ChauffeurDrop'] =  $returnval;
    $this->objData->ChauffeurDrop = $returnval;
    return $returnval;
  }
  public function GetAidesToDrop($activedate)
  {
    $where = '';
		if ( $this->input->post_get('action') != 2 )
    {
      $date = date_create($activedate);
      $day_of_week = intval($date->format('w'));
      $where = ' and reshumaine.' . $this->JourDeSemaine[$day_of_week].' = 1 ';
    }
    $activedate = $this->db->escape($activedate);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select reshumaine.id as idaide,
                  if(reshumaine.DIM = 0, 'background-color:lightgoldenrodyellow','') AS background,
                   CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine
			             WHERE
			             ( Select idrh From suivisrh srh
                       WHERE srh.idrh = reshumaine.id AND ( dateend > $activedate  or dateend is null )  and
							  datestart <= $activedate LIMIT 0, 1 ) is null
						            AND reshumaine.idreshum IN (2 ) AND reshumaine.isdeleted = 0 $where ORDER BY reshumaine.Nom, reshumaine.Prenom";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $titre = (($row->background == '')? '' : 'En congé');
      $returnval  .= '<div id="aide_'.$row->idaide.'_3" title="'.$titre.'" draggable="true" ondragstart="drag(event)" class="bg-light-red" style="padding-left: 10px;'.$row->background.'" >'. $row->NomComplet .'</div>';
    }
    $this->data['AideDrop'] =  $returnval;
    $this->objData->AideDrop = $returnval;
    return $returnval;
  }
    public function GetSecteursToDrop()
    {
        /** @var string $returnval */
        $returnval = '';
        /** @var string $strSQL */
        $strSQL = "SELECT Nom FROM secteurs WHERE isdeleted = 0 ORDER BY Nom ";
        $ville = $this->db->query($strSQL);
        foreach ($ville->getResult() as $row) {
            $returnval .= '<div id="secteur_' . $row->Nom . '_4" draggable="true" ondragstart="drag(event)" class="bg-light-red" style="font-size: medium;font-weight:bold;padding-left: 10px;width: 300px;">' . $row->Nom . '</div>';
        }
        $this->data['SecteurDrop'] = $returnval;
        $this->objData->SecteurDrop = $returnval;
    }
  //public function GetSecteursToDrop()
  //{
  //  /** @var string $returnval */
  //  $returnval = '';
  //  /** @var string $strSQL */
  //  $strSQL = "SELECT name FROM villes_zones WHERE isdeleted = 0 ORDER BY name ";
  //  $ville = $this->db->query($strSQL);
  //  foreach($ville->getResult() as $row)
  //  {
  //    $returnval  .= '<div id="secteur_'.$row->name.'_4" draggable="true" ondragstart="drag(event)" class="bg-light-red" style="font-size: medium;font-weight:bold;padding-left: 10px;width: 300px;">'. $row->name .'</div>';
  //  }
  //  $this->data['SecteurDrop'] = $returnval;
  //  $this->objData->SecteurDrop = $returnval;
  //}
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
   *
   *
   * @return void
   */
  public function GetTypeProduitsToDrop()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom FROM typededechet order by Nom";
    $typededechet = $this->db->query($strSQL);
    foreach($typededechet->getResult() as $row)
    {
      $returnval .= '<div id="produit_'.$row->Nom.'" draggable="true" ondragstart="drag(event)" class="bg-light-green">'. $row->Nom .'</div>';
    }
    $this->data['ProduitDrop'] = $returnval;
    $this->objData->ProduitDrop = $returnval;
  }
  public function GetVilles()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var int $iter */
    $iter = 1;
    /** @var string $strSQL */
    $strSQL = "SELECT Nom, id, Couleur FROM ville where isdeleted = 0  ORDER BY Nom";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      //if ($iter == 1) {
      //  $this->cedules = new CedulesModel();
      //  $this->cedules->GetRoutes($row->id);
      //  $iter++;
      //}
      $colorVille = '#'.dechex($row->Couleur);
      $returnval  .= '<div id="ville_'.$row->id.'_0" draggable="true" ondragstart="drag(event)"  onclick="getTypeRoute(' . $row->id . ')" style="font-size: medium;font-weight:bold;padding-left: 10px;width: 300px;">'. $row->Nom .'</div>';
    }
    $this->data['ville'] = $returnval;
  }
  /**
   *
   *
   * @return string
   */
  public function GetAllEmployes()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
        from reshumaine where idreshum in (1, 3, 4, 7) and isdeleted = 0 order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $returnval  .= '<option value="'.$row->id.'">'.$row->NomComplet.'</option>';
    }
    $this->data['employes'] = $returnval;
    return $returnval;
  }
  /**
   *
   *
   * @return string
   */
  public function GetTypeAbsences()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT id, Nom FROM typeabsence order BY Nom";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $returnval  .= '<option value="'.$row->id.'">'.$row->Nom.'</option>';
    }
    $this->data['typeabsences'] = $returnval;
    $this->objData->typeabsences = $returnval;
    return $returnval;
  }
  /**
   *
   * @param int $id
   *
   * @return string
   */
  public function GetEmployeNom($id)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
        from reshumaine where id = $id";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $returnval = $row->NomComplet;
    }
    return $returnval;
  }
  /**
   *
   * @param int $idchauffeur
   *
   * @return string
   */
  public function GetChauffeurs($idchauffeur = 25)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select CONCAT(Nom, ', ', Prenom) AS NomComplet, id
        from reshumaine where idreshum in (1, 3, 4, 7) and isdeleted = 0 order BY Nom";
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
  public function GetAidesCal($idaide = 24)
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
   * @param string $secteur
   *
   *
   * @return string
   */
  public function GetSecteurs($secteur = '')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT name FROM villes_zones WHERE isdeleted = 0 ORDER BY name ";
    $reshumaine = $this->db->query($strSQL);
    foreach($reshumaine->getResult() as $row) {
      $selected = (($secteur == $row->Nom)? ' selected ' : '');
      $returnval  .= '<option '.$selected.'  value="'.$row->Nom.'">'.$row->Nom.'</option>';
    }
    $this->data['secteurs'] = $returnval;
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
      $returnval .= '<option value="'.$row->nom.'" $selected >'.$row->nom.'</option>';
    }
    return $returnval;
  }
  /**
   *
   *
   *
   * @return void
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
  }

  public function GetAllCamions($dateactive, $nocamion, $typecam)
  {
    $dateactive = $this->db->escape($dateactive);
    $typecam = $this->db->escape($typecam);
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT   IFNULL((select CAST(sc.idstatut as CHAR(10) )
			            from suiviscamion sc where sc.nocamion = c.NoCamion and dateend is null LIMIT 0, 1 ), '1')  as StatutCam ,
              ( select cdl.NOCAMION from cedules cdl where cdl.DateJour = $dateactive
              and cdl.NOCAMION = c.nocamion  and cdl.NOCAMION is not null  LIMIT 0, 1  ) as camionIN,
               IF ( ( Select sc.nocamion from suiviscamion sc where
             sc.idstatut = 1 and sc.nocamion = c.nocamion and ( dateend is null )  and datestart <= $dateactive  LIMIT 0, 1 ) > 0, 1, 0 ) As HS,
             CONCAT( If(c.IsAutomatique = 1, 'A', ''), If(c.IsManuel = 1, 'M', ''), If(c.IsVerseur = 1, 'V', ''),
             If(c.HaveWinch = 1, 'W', ''), If(c.HaveRollBar = 1, 'R', '')) as CamPropriete,
             if( IFNULL(( Select sc.nocamion from suiviscamion sc where
              sc.idstatut = 2 and sc.nocamion = c.nocamion and ( dateend is null )  and datestart <= CURDATE() LIMIT 0, 1 ), 0) = 0, 0, 1) AS HaveMat,
             c.* FROM camions c where c.nocamion <> '' AND  typecamion in (1,7)  and ( Select sc.nocamion from suiviscamion sc where
              sc.idstatut = 1 and sc.nocamion = c.nocamion AND sc.nocamion != '' and ( dateend is null )  and datestart <= CURDATE() LIMIT 0, 1 ) is null
              and c.isdeleted = 0 order by TypeCamion, NoCamion";
    $camions = $this->db->query($strSQL);
    if (trim($nocamion) != '')
    {
      $returnval .= '<option selected value="'.$nocamion.'">'.$nocamion.'</option>';
    }
    else
    {
      $returnval .= '<option selected value="">Choisir</option>';
    }

    foreach($camions->getResult() as $row)
    {
      $backColor = '';
      if($row->StatutCam == 2)
      {
        if ($row->camionIN != '')
        {
          $backColor = 'background-color: darkred;color:white;' ;
          //$backColor = 'background-image: linear-gradient(to right,chocolate 40%,red 40%);color:darkblue;' ;
        }
        else
        {
          $backColor = 'background-color: chocolate;' ;
        }
      }
      else if($row->camionIN != '')
      {
        $backColor = 'color:darkred;font-weight: bold;' ;
      }
      $infotype = (($row->TypeCamion == 1)? 'SANI | ' : 'ROBOT | ');
      $returnval .= '<option style="'.$backColor.'" value="'.$row->NoCamion.'">'.$infotype.$row->NoCamion.'</option>';
    }
    $this->data['camions'] = $returnval;
    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param int $from
   *
   * @return array
   */
  public function GetCedule($dateactive, $from)
  {
    $this->LoadRowVilles($dateactive);
    $this->LoadCedules($dateactive, $from);
    return $this->data;
  }
  /**
   *
   * @param string $dateactive
   * @param int $from
   *
   * @return void
   */
  public function LoadRowVilles($dateActive)
  {
    /** @var int $nbActiveRow */
    $nbActiveRow = 0;
    /** @var int $nbTotalRow */
    $nbTotalRow = 150;

  }
  /**
   * @param int $id
   * @param string $typeprod
   * @param string $color
   *
   * @return void
   */
  public function getFiledProd($id, $typeprod, $color)
  {

  }
  /**
   *
   * @param int $idvilles
   * @param int $from
   * @param string $dateactive
   *
   * @return string
   */
  public function getNewLine($idvilles, $from, $dateactive)
  {
    /** @var string $returnval */
    $returnval = '';
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
														  g.id as idgrp, c.TypeCedule, g.idvilles, c.chaufconfirme, c.helperconfirme, c.IsLocked,
														  c.IsLockedChauffeur,  c.STARTTIME, c.ENDTIME, c.STARTPERTE, c.ENDPERTE, c.PerteDescription,
														  c.Note, c.TypeDeProduit,c.Tonnage,  ifnull(c.idchauffeurs, 0) AS nochauffeur,
                              ifnull(c.HELPERS_1, 0) AS noaide, ifnull(ACol, 1) as ACol, g.ARow
												  from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
												  WHERE g.datejour = $dateactive and ( g.TypeCedule = $from )
												  and c.TypeCedule = g.TypeCedule and c.idvilles = $idvilles  Order by  g.ARow, g.id ";
    /** @var object $resultLine */
    $resultLine = $this->db->query($strSQL);
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      if ($row->TypeDeProduit != 'COMMERCIAL')
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
        $edit = "<button type='button'
                          class='btn btn-default btn-sm' style='font-size:14px;color: #03F;' xclass='btn-primary btn'
                              onclick=\"ShowLigneCedule('tr_".$row->ID."')\" title='Modifier'><i class='fa fa-pencil-square-o fa-4' aria-hidden='true'></i></button>";
        $returnval .= '<tr><td align="center" class="cellule" style="width: 60px;"><label style="width: 60px;margin-left:5px;">'.(($from == 3 or $from == 5)? '' : $edit ).'</label></td>' ;
        $delete = "<button type=\"button\" class='btn btn-default btn-sm' style='font-size:18px;color: #03F;' xclass='btn-primary btn'
                          title='Supprimer cette Ligne'
                          onclick=\"DeleteLigneFromCreateCedule(".$row->ID.",".$row->idvilles.", ".$row->TypeCedule.")\"  style=\"margin:4px 5px 0 0;\"><i  class=\"fa fa-trash\" aria-hidden='true' style=\"color: red;\"></i></button>";
        $cam = substr($row->NOCAMION ,0, 3);
        $backColor = (($cam == 'Cam')? 'background-color: lightcoral;' : '');
        $returnval .=  '<td  class="cellule" align="center" style="width: 60px;"><input type="text" id="camion_'.$row->ID.'" class="form-control"  ondrop="dropcam(event,'.$row->ID.')" ondragover="allowDrop(event)" style="width: 60px;margin-left:5px;'.$backColor.';font-size: 14px;" value="'.(($cam == '')? '' : $row->NOCAMION ).'"/></td>' ;

        //------------------------ &nbsp;&nbsp;&nbsp;&nbsp;
        $showchauf =  '<input id="chauffeur_'.$row->ID.'"  class="form-control"  style="width:150px;" ondrop="dropchauffeur(event,'.$row->ID.')" ondragover="allowDrop(event)"
                                      type="text" value="'.$row->chauffeur.'"/>';
        $returnval .=  '<td id="trchauffeur_'.$row->ID.'" class="cellule" align="left" style="width: 150px;font-size: 14px;margin-left:10px;"> ' .$showchauf.'</td>';

        $showaide =   '<input id="aide_'.$row->ID.'"  class="form-control"  style="width:150px;" ondrop="dropaide(event,'.$row->ID.')" ondragover="allowDrop(event)"
                                      type="text" value="'.$row->aide1.'"/>';
        $returnval .= '<td  id="traide_'.$row->ID.'" class="cellule" align="left" style="width: 150px;font-size: 14px;margin-left:10px;">' . $showaide . '</td>';
        $showproduit = '<select class="form-control" onchange="SetProduit(this.id)" id="produit_'.$row->ID.'" style="font-size:medium;font-weight:bold;width:90%;background-color:'.$color.'">
                                  <option value="DECHETS">Choisir</option>' .$this-> GetAllProduits($row->TypeDeProduit) . '</select>';
        $returnval .= '<td id="trproduit_'.$row->ID.'" class="cellule" align="center" style="background-color:'.$color.';" >' .$showproduit.'</td>
                          <td>'.$delete.'</td></tr>';
      }
    }

    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param int $idville
   * @param int $idcedule
   * @param int $ACol
   *
   * @return void
   */
  public function AddVilleToCedule($dateactive, $idville, $idcedule, $ACol)
  {
    $date = $this->db->escape($dateactive);
    $idvilleesc = $this->db->escape($idville);
    $idceduleesc = $this->db->escape($idcedule);
    $AColesc = $this->db->escape($ACol);
    ///** @var string $returnval */
    //$returnval = '';
    ////--------------------------------------------------------------
    //$strSQL ="SELECT ville.id FROM ville WHERE ville.id = $idvilleesc";
    ///** @var object $data */
    //$data = $this->db->query($strSQL)->getRow(0);
    //$idville = $data->id;
    //$strSQL ="SELECT grp_cedules.id FROM grp_cedules WHERE datejour = $date
    //                        AND idvilles = $idvilleesc and TypeCedule = $idceduleesc";
    //$data = $this->db->query($strSQL)->getRow(0);
    //$idgroupe = '';
    //if (($data == '' ) or (!isset($data)))
    //{
    //  $idgroupe = '';
    //}
    //else
    //{

    //  $idgroupe = $data->id;
    //}
    //if (($idgroupe == '' ) or (!isset($idgroupe)))
    //{
      $strSQL = "INSERT INTO grp_cedules
	                  (datejour, idvilles, ACol, ARow, TypeCedule)
	                  (SELECT $date, $idvilleesc, $AColesc, IFNULL((MAX(ARow) + 1), 1), $idceduleesc FROM grp_cedules
								                  WHERE datejour =  $date AND typecedule = $idceduleesc AND ACol = $AColesc)";
      $this->db->query($strSQL);
      $strSQL = 'SELECT LAST_INSERT_ID() as id';
      if ($row = $this->db->query($strSQL)->getRow(0)) {
        $idgroupe = $row->id ;
      }
      $strSQL = "INSERT INTO cedules
	              (CodeUtil, DateJour, TypeCedule, idgrp, idvilles,TypeDeProduit, NOCAMION, idchauffeurs, HELPERS_1)
	              VALUES ('web', $date,  $idceduleesc, $idgroupe, $idvilleesc, 'DECHETS', 'Cam',0,0 )";
      $this->db->query($strSQL);
      //----------------------------------------------------------------------
    //}
    //else
    //{
    //  $strSQL = "UPDATE grp_cedules
    //                    SET
    //                      ACol=$AColesc
    //                    WHERE id = $idgroupe";
    //  $result = $this->db->query($strSQL);
    //}
  }
  /**
   *
   * @param string $dateactive
   * @param int $idcedule
   * @param int $ACol
   *
   * @return string
   */
  public function getColInfo($dateactive, $idcedule, $ACol)
  {
    $date = $this->db->escape($dateactive);
    $idceduleesc = $this->db->escape($idcedule);
    $AColesc = $this->db->escape($ACol);
      /** @var string $returnval */
      $returnval = '';
      /** @var string $strSQL */
      $strSQL ="SELECT c.NOCAMION, c.IsStart,c.ID,
                      (SELECT MAX(ARow) as nb from grp_cedules where grp_cedules.datejour = '2021-02-12' AND grp_cedules.ACol = 2) AS nbRow,
                            (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
                            (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
                              sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < '2021-02-12'
                              and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
                              ( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
                              ( Select CONCAT (Nom, ', ', Prenom ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
                              IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
                              IFNULL(( Select Couleur FROM ville where id = g.idvilles ), -1)  As couleurville,
                              g.id as idgrp, c.TypeCedule, g.idvilles, c.chaufconfirme, c.helperconfirme, c.IsLocked,
                              c.IsLockedChauffeur,  c.STARTTIME,c.Secteur,
                               c.TypeDeProduit,ifnull(c.idchauffeurs, 0) AS nochauffeur,
                              ifnull(c.HELPERS_1, 0) AS noaide, g.ACol, g.ARow
                          from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
                          WHERE g.datejour = $date and ( g.TypeCedule = $idceduleesc ) AND g.ACol = $AColesc
                          and c.TypeCedule = g.TypeCedule  Order by g.ARow, g.id";
      /** @var object $resultLine */
      $resultLine = $this->db->query($strSQL);
      $returnval = $this->getLignesCedule($resultLine, $dateactive, $idcedule);

    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param int $idville
   * @param int $idcedule
   * @param int $ACol
   * @param int $OldCol
   *
   * @return void
   */
  public function UpdVilleToCedule($dateactive, $idville, $idcedule, $ACol, $OldCol)
  {
    $date = $this->db->escape($dateactive);
    $idvilleesc = $this->db->escape($idville);
    $idceduleesc = $this->db->escape($idcedule);
    $AColesc = $this->db->escape($ACol);
    $OldCol = $this->db->escape($OldCol);
    $newRow = 0;
    $strSQL = "(SELECT IFNULL((MAX(ARow) + 1), 1 ) as newrow FROM  grp_cedules  WHERE  datejour = $date AND
                                typecedule = $idceduleesc and ACol = $AColesc )";
    $result = $this->db->query($strSQL);
    foreach( $result->getResult() as $row)
    {
      $newRow = $row->newrow;
    }
    //--------------------------------------------------------------
    $strSQL = "UPDATE grp_cedules
	                SET
		                ACol=$AColesc,
                    ARow = $newRow
	                WHERE  datejour = $date AND typecedule = $idceduleesc AND  idvilles=$idvilleesc";
    $result = $this->db->query($strSQL);
    //----------------------------------------------------------------------
    $iterrow = 1;
    $strSQL = "SELECT * FROM  grp_cedules  WHERE  datejour = $date AND
                                typecedule = $idceduleesc and ACol = $OldCol  order by ARow";
    $result = $this->db->query($strSQL);
    foreach( $result->getResult() as $row)
    {
      $strSQL = "UPDATE grp_cedules
	                SET
                    ARow = $iterrow
	                WHERE  id = ".$row->id;
      $this->db->query($strSQL);
      $iterrow++;
    }
    //----------------------------------------------------------------------
  }
  /**
   *
   * @param int $id
   * @param int $oldrow
   * @param int $newrow
   * @param int $oldcol
   * @param int $newcol
   *
   * @return void
   */
  public function updRow($id, $oldrow, $newrow, $oldcol, $newcol)
  {
    /** @var string $strSQL */
    if($oldcol <> $newcol)
    {
      $strSQL = "UPDATE grp_cedules
	                  SET
		                  ARow = ARow - 1
	                  WHERE ARow > $oldrow AND ACol = $oldcol";
      $this->db->query($strSQL);
    }
    if ($newrow < $oldrow)
    {
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = ARow + 1
									  WHERE ARow >= $newrow AND ARow <= $oldrow and ACol = $newcol";
      $this->db->query($strSQL);
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = $newrow
									  WHERE id =$id";
    }
    else
    {
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = ARow - 1
									  WHERE ARow >= $oldrow AND ARow <= $newrow and ACol = $newcol";
      $this->db->query($strSQL);
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = $newrow
									  WHERE id =$id";
    }
		$this->db->query($strSQL);
  }
  /**
   *
   * @param int $idgroup
   * @param int $oldrang
   * @param int $newrang
   *
   * @return void
   */
  public function updTableRang($idgroup, $oldrang, $newrang, $dtejour, $col,$typecedule)
  {
		/** @var string $strSQL */
    if ( $oldrang < $newrang )
    {
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = ARow - 1
									  WHERE ARow <= $newrang AND ARow > $oldrang and datejour = $dtejour AND ACol = $col AND typecedule = $typecedule" ;
      $this->db->query($strSQL);
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = $newrang
									  WHERE id =$idgroup";
    }
    else
    {
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = ARow + 1
									  WHERE ARow >= $newrang  AND ARow < $oldrang and datejour = $dtejour AND ACol = $col AND typecedule = $typecedule";
      $this->db->query($strSQL);
      $strSQL = "UPDATE grp_cedules
									  SET
										  ARow = $newrang
									  WHERE id =$idgroup";
    }
		$this->db->query($strSQL);

  }
  /**
   *
   * @param string $typerh
   *
   * @return string
   */
  private function LoadRHHome($typerh)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select reshumaine.*, suivisrh.* , typeabsence.Nom as nomAbsence,
                suivisrh.id as idsuivis From reshumaine INNER JOIN  suivisrh   ON
                reshumaine.id = suivisrh.idrh     INNER JOIN typeabsence
                ON suivisrh.idabsence = typeabsence.id
                WHERE ( suivisrh.dateend is null OR suivisrh.dateend > CURDATE() ) AND
                datestart <= CURDATE() AND reshumaine.idreshum IN ( $typerh) order by reshumaine.Nom";
    $suiviscamion = $this->db->query($strSQL);
    foreach($suiviscamion->getResult() as $row) {
            $note = str_replace('%20', ' ', $row->note);
            $note = str_replace('"', "'", $row->note);

      $message = "
                  <b> Depuis :$row->datestart <br/><br/></b>
                                        <b> Retour prévu:<br/></b>
                                        $row->dateretourprevu <br/>
                                        <b> Absence pour:<br/></b>
                                        $row->nomAbsence <br/>
                                        <b>Notes: <br/></b>
                                        $note <br/><br/>";
      $returnval .= '<a href="#" onclick="ShowRHModal('.$row->idsuivis.')"  data-toggle="popover" data-trigger="hover" data-html="true" title="RH: '.$row->Nom. ' '.$row->Prenom.' Non Disponible"   data-placement="bottom" data-content="'.$message.'"  style="overflow:auto;font-size:medium;font-weight:bold;">'. $row->Nom. ' '.$row->Prenom.
                    '&nbsp;&nbsp;<i class="fas fa-share" ></i></a><br /><span style="font-size:small; color:black">&nbsp;&nbsp;&nbsp;&nbsp;'. $row->nomAbsence. '</span> <br />  ' ;
    }
    $returnval = substr($returnval,0,-2) ;
    return $returnval;
  }
  /**
   *
   * @param int $statut
   * @param int $typecam
   *
   * @return string
   */
  private function LoadCamionHome($statut, $typecam)
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
                               AND statutcamion.id = $statut AND
                               (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ) = $typecam
                               order by  (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ),
                               suiviscamion.nocamion";
    $suiviscamion = $this->db->query($strSQL);
    foreach($suiviscamion->getResult() as $row) {
      if (($row->dateend == '') or (!isset($row->dateend)))
      {
        $note = str_replace('%20', ' ', $row->note);
        $note = str_replace('"', "'", $row->note);
        switch ($statut)
        {
          case 1:

            $message = "
                       <b> Depuis :$row->datestart <br/><br/></b>
                                             <b> Retour prévu:<br/></b>
                                              $row->dateretourprevu <br/>
                                              <b>Notes: <br/></b>
                                              $note <br/><br/>";
            $returnval .= '&nbsp;&nbsp;<a  onclick="ShowHSModal(\''.$row->nocamion.'\')" data-toggle="popover" data-trigger="hover" data-html="true" title="Camion: '.$row->nocamion.' Hors service"   data-placement="bottom" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;'.
                                          (($row->TypeCam == 1)? '#da1c23' : 'DarkBlue' ).'">'. $row->nocamion.'</a>, ' ;
            break ;
          case 2:

            $message = "
                       <b> Depuis : $row->datestart <br/><br/> </b>
                                             <b> Vidange prévu: </b> <br/>
                                              $row->dateretourprevu <br/>
                                             <b> Type de déchet: </b> <br/>
                                              $row->TypeDechet <br/>
                                              <b>Notes: <br/> </b>
                                              $note <br/><br/>";
            $returnval .= '&nbsp;&nbsp;<a  id="showcammat_'.$row->nocamion.'" onclick="ShowAvecMatModal(\''.$row->nocamion.'\')" data-toggle="popover" data-trigger="hover"  data-html="true" title="Camion: '.$row->nocamion.' Avec Matériel"   data-placement="bottom" data-content="'.$message.'" style="overflow:auto;font-size:medium;font-weight:bold;'.
                                            ((strtolower( $row->TypeDechet ) == 'recyclage')? '#26F4B2' : '#254AE0' ).'">'. $row->nocamion.'</a>, ' ;
            break ;
        }
        //switch ($statut)
        //{
        //  case 1:
        //    $returnval .= '<a href="#" onclick="ShowHSModal(\''.$row->nocamion.'\')"  style="overflow:auto;font-size:medium;font-weight:bold;'.
        //                                  (($row->TypeCam == 1)? '#da1c23' : 'DarkBlue' ).'">'. $row->nocamion.'</a>
        //                                    <script>$(document).ready(function(){$(\'[data-toggle="tooltip_'.$row->nocamion.'"]\').tooltip();}); </script>, ' ;
        //    break ;
        //  case 2:
        //    $returnval .= '<a href="#" onclick="ShowAvecMatModal(\''.$row->nocamion.'\')"  style="overflow:auto;font-size:medium;font-weight:bold;'.
        //                                  (($row->TypeCam == 1)? '#da1c23' : 'DarkBlue' ).'">'. $row->nocamion.'</a>
        //                                    <script>$(document).ready(function(){$(\'[data-toggle="tooltip_'.$row->nocamion.'"]\').tooltip();}); </script>, ' ;
        //    break ;
        //}
      }

    }
    $returnval = substr($returnval,0,-2) ;
    return $returnval;
  }
  function getTDBHome()
  {
    $this->cedules = new CedulesModel();
    //----------------------------------------------------------------------------------
    $tableCam = '<table  class = "table" id="tab_logic">
            <tr  class="entete" style="background-color: #093;line-height:25px;text-align:center;width:1100px">

                <tr>
                <td>
                  <h3  ondrop="dropREHors(event, 1,1)" ondragover="allowDrop(event)" style="margin-top: 11px;height:40px;padding-top: 8px;width:100%;color:white;text-align:center;background-color:black;">Types Camion</h4>
                  <div >
                        <select class="form-control" id="typecam" onchange="getCamForTypeHome(this.value)">
                        <option value="">Choisir</option>
                          ' .$this->cedules->GetTypeCamion(1).'
                        </select>

                  </div>
                  <div class="box box-solid" style="overflow: scroll; max-height: 250px; width: 200px">

                    <div id="divDropCam" class="box-body" style="padding-left:7px;">
                ' .$this->GetHomeCamions().'

                    </div>
                  </div>
                </td>
                <td><h4  ondrop="dropREHorsTDB(event, 1,1)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">HORS SERVICES REAREND</h4><div class="hometdb" style="height:300px;width: 300px;" >' .$this->LoadCamionHome(1,1).'</div></td>
                <td><h4  ondrop="dropREHorsTDB(event, 1, 2)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:peru;">REAREND AVEC MATÉRIEL</h4><div class="hometdb" style="height:300px;width: 300px;" >' .$this->LoadCamionHome(2,1).'</div></td>
                <td><h4  ondrop="dropRHNonDispoTDB(event)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">AIDES INDISPONIBLE</h4><div class="hometdb" style="height:300px;width: 300px;overflow: scroll;" >' .$this->LoadRHHome('2').'</div></td></tr>

                <tr>
                <td><h3  ondrop="dropREHors(event, 1,1)" ondragover="allowDrop(event)" style="margin-top: 11px;height:40px;padding-top: 8px;width:100%;color:white;text-align:center;background-color:black;">Types de RH</h3>
                  <div>

                <select class="form-control" id="typerh" onchange="getRHForType(this.value)">
                  <option value="">Choisir</option>
              ' .$this->cedules->GetTypeRH(1).'

                </select>
              </div>
              <div class="box box-solid" id="divDropChauf" style="padding-left:7px;display:block;overflow: scroll; max-height: 250px; width: 200px">
              ' .$this->GetChauffeursToDropHome(date('Y-m-d'), 1).'

              </div>
            </td>
            <td><h4  ondrop="dropREHorsTDB(event, 1, 1)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">HORS SERVICES ROBOT</h4><div class="hometdb" style="height:300px;width: 300px;" >' .$this->LoadCamionHome(1,2).'</div></td>
            <td><h4  ondrop="dropREHorsTDB(event, 2, 2)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:peru;">ROBOT AVEC MATÉRIEL</h4><div class="hometdb" style="height:300px;width: 300px;" >' .$this->LoadCamionHome(2,2).'</div></td>
            <td><h4  ondrop="dropRHNonDispoTDB(event)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">CHAUFFEURS INDISPONIBLE</h4><div class="hometdb" style="height:300px;width: 300px;overflow: scroll;" >' .$this->LoadRHHome('1, 3').'</div></td></tr>
                            </table> <script type="text/javascript">
                $(document).ready(function () {
                    $(\'[data-toggle="popover"]\').popover();
                });
              </script>';
    $this->data['tdbhome'] = $tableCam;
    return $tableCam;
    //----------------------------------------------------------------------------------
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
    $message = '	<li  style="list-style:none;">
		          <input type="date" style="height: 28px;margin-top: 8px;" onchange="LoadToday(0)" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="'.$dateactive.'" id="dteActive" />
	          </li>
	          <li  id="PageRearEnd" style="display:'. (($notab == 2)? 'block' : 'none').';list-style:none;">
	            <a id="liPageRearEnd" '.(($typecedule == 1)? 'class="btn-primary"' : '') .' href="javascript:ShowREAREND()">REAR END</a>
	          </li>
	          <li id="PageRobot" style="display:'. (($notab == 2)? 'block' : 'none').';list-style:none;">
	            <a id="liPageRobot"style="margin-top:5px;" '. (($typecedule == 2)? 'class="form-control btn-primary"' : 'class="form-control"') .' href="javascript:ShowROBOT()">ROBOT</a>
	          </li>
	          <li  id="liPageCreation" style="margin-top:10px;display: '. (($notab == 1)? 'block' : 'none').';cursor:pointer;list-style:none;">
	            <a  href="javascript:ShowTabCedule(2)"><i class="fa fa-edit" style="font-size:24px;"></i>&nbsp;&nbsp;Cr&eacute;ation</a>

	          </li>
	          <li  id="liPageDay" style="display: '. (($notab == 2)? 'block' : 'none').';cursor:pointer;list-style:none;">
	            <a href="javascript:ShowTabCedule(1)" style="font-size:24px;"><i class="fa fa-table"></i>&nbsp;&nbsp;Aujourd hui</a>
	          </li>
	          <li style="cursor:pointer;list-style:none;">
	            <a href="javascript:ShowRemoveCedule()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Supprimer" data-content="Supprimer une ou des journ&eacute;es">
		          <i class="fa fa-trash" style="font-size:24px;color: red;"></i>&nbsp;&nbsp;Suppression de c&eacute;dule
	            </a>
	          </li>
	          <li  style="margin-top:10px;cursor:pointer;list-style:none;">
	            <a onclick="window.print()"
		          <i class="fa fa-print" style="font-size:24px;"></i>&nbsp;&nbsp;Imprimer
	            </a>
	          </li>
            <li style="list-style:none;" class="divider"><li style="list-style:none;" class="divider">
	          </li>'. $this->GetMenuTop($dateactive);


    return $message;
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
            <a href="/admin">
              <i class="fa fa-home" style="font-size:24px;"></i>&nbsp;&nbsp;Accueil
            </a>
          </li>
          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/sanivan/aujourdhui/3/'.$dateactive.'" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
              <i class="fa fa-tv" style="font-size:24px;"></i>&nbsp;&nbsp;TV
            </a>

          </li>
          <li class="dropdown"  style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
              <i class="fa fa-eye-slash" style="font-size:24px;"></i>&nbsp;Hors-Service
            </a>

            <ul class="dropdown-menu">

              <li>
                <a onclick="ShowCamion(2)" href="#" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Avec Mat&eacute;riel" data-content="Afficher la page pour les camions avec mat&eacute;riel">
                  <i class="fa fa-truck" style="font-size:24px;"></i>&nbsp;&nbsp;Avec Mat&eacute;riel
                </a>

              </li>
              <li>
                <a onclick="ShowCamion(1)" href="#" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Hors-service" data-content="Afficher la page pour les camions Hors-service">
                  <i class="fa fa-truck" style="font-size:24px;"></i>&nbsp;&nbsp;Hors-service
                </a>
              </li>


              <li>
                <a onclick="ShowRH()" href="#" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Chauffeurs et Aides" data-content="Afficher la page pour la gestion des Chauffeurs et des Aides">
                  <i class="fa fa-user" style="font-size:24px;"></i>&nbsp;&nbsp;Employ&eacute;s
                </a>
              </li>
            </ul>
          </li>

          <li class="dropdown" style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-edit" style="font-size:24px;"></i>&nbsp;Gestion
            </a>

            <ul class="dropdown-menu">
              <li>

                <a href="/sanivan/editcam/0">
                  <i class="fa fa-plus" style="font-size:24px"></i>&nbsp;&nbsp;Ajouter/Editer des Camions
                </a>
              </li>
              <li class="divider"></li>
              <li>

                <a href="/sanivan/showrh/0">
                  <i class="fa fa-plus" style="font-size:24px"></i>&nbsp;&nbsp;Ajouter/&Eacute;diter des Ressources
                </a>
              </li>
              <li class="divider"></li>
              <li>

                <a href="/sanivan/showville/0">
                  <i class="fa fa-plus" style="font-size:24px"></i>&nbsp;&nbsp;Ajouter/&Eacute;diter des villes
                </a>
              </li>
            </ul>
          </li>

          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/sanivan/dictionnaires/1/1">
              <i class="fa fa-book" style="font-size:24px;"></i>&nbsp;Dictionnaire
            </a>

          </li>
          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a style="color:white;float:center" href="/admin/quitter">

              <i class="glyphicon glyphicon-user" style="font-size:24px; color:red">Quitter</i>
            </a>

          </li>';


    return $message;
  }
  function __destruct() {
    $this->db->close();
  }
}
