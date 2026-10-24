<?php

namespace App\Models;

/**
 * Port mecanique de Rapport_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class RapportModel extends Ci3Model
{
  /** @var array $data */
	protected $data = array();
  /** @var string $RollOffDay */
  protected $RollOffDay = 'rolloffday';
  /** @var string $table */
  protected $table = 'rolloffday';
  //-------------------------------------
  function __construct() {
    parent::__construct();
    
		
		
		//$this->rolloff = new RollOffModel();
  }
  public function get_count() {
    return $this->db->count_all($this->table);
  }
  /**
   * @param string $dateactive
   * @param int $add
   *
   * @return array
   */
  public function getRollOffDayReport($dateactive,$add) {
    /** @var string $returnval */
    $returnval = '';
    $dteactive = $this->db->escape($dateactive);
    if ($add == 1)
    {
      $strSQL = "CALL rpRoUpdIns($dteactive)";
      $this->db->query($strSQL);
    }


    $strSQL = "SELECT rolloffday.* FROM rolloffday WHERE date_fact = $dteactive
                      order by NOCAMION, STARTTIME";
    $rolloffday = $this->db->query($strSQL);
    //$rolloffday = $this->transfertRollOffForRapport($dteactive);
    foreach($rolloffday->getResult() as $row)
    {
      /** @var string $edit */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDayRapRO(\''.$row->NoDemande.'\')">
										           <i class="fa fa-edit btn btn-success" style="font-size:16px;"></i></a>';
      $returnval .= '<tr>
                            <td align="center"><button class="btn btn-primary" title="'.$row->notes.'" onclick="ShowCommandeRap('.$row->NoDemande.')">'.$row->NoDemande.'</button></td>
                            <td align="center"><label style="width:120px"><a class="btn btn-info" aria-label="Edit" title="'.$row->Client.': &#013;'.$row->AdresseComplete.'">'.substr($row->Client,0,10).'</label></a></td>
                            <td align="center"><label  class="form-control" style="width:140px;" >'.$row->TypeDem.'</label></td>
                            <td  align="center"><button class="btn btn-default"  title="Boite: '.$row->BoiteEnPlace.', Tonnage:'.$row->NbTonnes.' &#013; Depotoire: '.$row->Depotoire.' ">'.$row->FormatCont.'</button></td>
			                      <td  align="center"><label style="width:110px;">'.$row->TypeMat.'</label></td>
                            <td  align="center"><button class="btn btn-default"  title="Chauffeur: '.$row->Chauffeur.'">'.$row->NOCAMION.'</button></td>

                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;" type="text" value="'.number_format( $row->PrixDay,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;">
                            <label class="form-control btn-info" style="width: 80px;text-align: right;font-size:14px;" >'.number_format( $row->PrixCommande,2).'</label>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>

                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;" type="text" value="'.number_format( $row->CoutDepotoire,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:70px;">
                            <input class="form-control" style="width: 60px;text-align: right;font-size:14px;" type="text" value="'.number_format( $row->surpluspoids,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >TN</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:80px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px" type="text" value="'.number_format( $row->SurplusLocation,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td>'.$post.'</td>
												</tr>';
    }
    //$this->data['rapdayro'] = $returnval;
    $this->data['rapdaychauf_cam'] = $this->getCamChaufDay($dateactive);
    //$this->data['rapdaychauf'] = $this->getChauffeursDay($dateactive);
    return $this->data;
  }
  /**
   * @param string $dateactive
   * @param string $tr_iter
   * @param string $NOCAMION
   *
   * @return string
   */
  private function getRollOffCommandes($dateactive,$tr_iter, $NOCAMION) {
    $get = $this->input->post_get('titre',  TRUE);
    $print = 'none';
    if (isset($get))
    {
      $print = 'block';
    }
    /** @var string $returnval */
    $dteactive = $dateactive;
    $dteactive = $this->db->escape($dateactive);
    $NOCAM = $this->db->escape($NOCAMION);
    $span ='<label style="color:black;font-size:large;">';
    $strSQL = "SELECT rolloffday.*, (Deglacage + Lavage + AjustPrix) as Ajustement,
                  if(`code` = 5, getRedevance(idclient, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), Poids, IsTRansCour), 0) as Redevance,
                  if( If(Poids = 0, 0, ROUND( Poids - getTonnage(idclient, FormatContenant), 2)) <=0, 0,`getPrixPoids`(idclient, Poids, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), IsTransCour)) AS prixpoids, getTonnage(idclient, FormatContenant) as Tonnage_5, if( If(Poids = 0, 0, ROUND( Poids - getTonnage(idclient, FormatContenant), 2)) <=0, 0,If(Poids = 0, 0, ROUND( Poids - getTonnage(idclient, FormatContenant), 2))) AS surpluspoids_5,`getPrixDepotoire`(idclient, Poids, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0)) AS prixdepotoire,
                  if(`code` = 5, (SELECT CONCAT('<b>Frais Dispo:</b> ', fraispoids, ' * ', IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',  round(IFNULL(fraispoids * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2),
                      ' + <br /><b>Frais car site:</b> ', fraisCarburantSite, ' * ',
                      IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',
                      ROUND(IFNULL(fraisCarburantSite  * rolloffday.surpluspoids, 0), 2),
                      ' + <br /><b>Frais car:</b> ', IFNULL(fraisCarburant, 0),
                      ' + <br /><b>Frais red:</b> ',	IFNULL(FraisRedevance, 0),
                      ' + <br /><b>Frais red / TM:</b> ', fraisRedevanceTM, ' * ',
                      IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ' ,ROUND(
                      IFNULL(fraisRedevanceTM * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2)) FROM clients where id = rolloffday.idclient ),
				              0) As Parti_1,
                  if(`code` = 5, (SELECT If(PourcentCarburant = 0, '', CONCAT('<b>Calcul %</b><br />
                        (<b>Frais Poids:</b> ', fraispoids, ' * ', IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',  round(IFNULL(fraispoids * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2),
                        ' + <br /><b>Frais car site:</b> ', fraisCarburantSite, ' * ',
                        IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',
                        ROUND(IFNULL(fraisCarburantSite  * rolloffday.surpluspoids, 0), 2),
                        ' + <br />Frais car: ', IFNULL(fraisCarburant, 0),
                        ' + <br />Frais red: ',	IFNULL(FraisRedevance, 0),
                        ' + <br /><b>Frais red / TM:</b> ', fraisRedevanceTM, ' * ',
                        IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ' ,ROUND(
                          IFNULL(fraisRedevanceTM * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2),
				                  ') * ',  IFNULL((PourcentCarburant / 100),0))) FROM clients where id = rolloffday.idclient ),0)
				                    AS Parti_2,
                      if(SurplusLocation = 0, 0, CONCAT('<b>Date Liv. Parent:</b> ', (SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ) ,
		                      '<br /><b>Nb Jour Surplus:</b> ', DATEDIFF(rolloffday.date_fact, DATE_ADD((SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ), INTERVAL 14 DAY)),
		                      '<br /><b>Frais Liv. Jour:</b> ',  ((SELECT FraisLivraison FROM optionssys) / 7),
	                        '<br /><br />Calcul: ',DATEDIFF(rolloffday.date_fact, DATE_ADD((SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ), INTERVAL 14 DAY)),
	                        ' * ', ((SELECT FraisLivraison FROM optionssys) / 7),
	                        ' = ', (DATEDIFF(rolloffday.date_fact, DATE_ADD((SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ), INTERVAL 14 DAY)) * ((SELECT FraisLivraison FROM optionssys) / 7) ))) AS InfoLocation,
                      if(`code` = 5 and FormatContenant not in (10, 11),
                      getInfoCommande(idclient, FormatContenant ),
                      CONCAT('<b>PRIX:</b> ', IFNULL(round(PrixCommande, 2), 0),
	                            ' + <br /><b>RED.:</b> ', IFNULL(RedevanceCommande, 0),
                              ' + <br /><b>CAR.:</b> ', IFNULL(gazeline, 0),
                              ' + <br /><b>CAR. SITE:</b> ',	IFNULL(carburant_site, 0),
                              ' + <br /><b>DISTANCE:</b> ',	IFNULL(fraisdistance, 0),
                              ' + <br /><b>DEMOLITION:</b> ',	IFNULL(fraisdemolition, 0)
                      )) as InfoCommande,
                      CONCAT('<b>PRIX:</b> ', IFNULL(ROUND(PrixDay,2), 0),
                          ' + <br /><b>AjustPrix:</b> ', IFNULL(ROUND( AjustPrix,2), 0),
		                      ' + <br /><b>DISPO.:</b> ', if(`code` = 5 AND FormatContenant in (10, 11), `getPrixPoids`(idclient, Poids, FormatContenant,
		                      if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), IsTransCour),
if(`code` = 5 AND FormatContenant not in (10, 11), 0,prixsurpluspoids)),
		                      ' + <br /><b>SUP. LOC.:</b> ', IFNULL(SurplusLocation, 0),
		                      ' + <br /><b>DEGLACAGE:</b> ', IFNULL(ROUND( Deglacage,2), 0),
		                      ' + <br /><b>LAVAGE:</b> ', IFNULL(ROUND( Lavage,2), 0) ) AS InfoRevBrute
                  FROM rolloffday WHERE date_fact = $dteactive and NOCAMION = $NOCAM
                      order by STARTTIME";
    $rolloffday = $this->db->query($strSQL);
    $this->data['IsLoaded'] = ($rolloffday->getNumRows() > 0)? 1 : 0;
    $titreNB = $rolloffday->getNumRows().' commandes pour le camion '. $NOCAMION ;
    $returnval = '<tr style="text-align:right;" class="warning"  id="tr_'.$tr_iter.'"  style="display:'.$print.';" > <td></td><td colspan="9" ><table class="table table-bordered">
		                <thead><tr><th colspan="11"><label class="titrenb">'.$titreNB.'</label></th> </tr>';
    $returnval .= '

                      <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                        <th>&nbsp;&nbsp;</th>
                        <th># COMMANDE</th>
                        <th>INFO CLIENT</th>
                        <th>CODE</th>
                        <th>ACTION</th>
                        <th>CONTENANT</th>
                        <th>T.M INC</th>
                        <th>T.M</th>
                        <th>MAT&Eacute;RIAUX</th>
                        <th>PRIX</th>
                        <th>AJUSTEMENT PRIX</th>
                        <th>CAR / %</th>
                        <th>+T.M</th>
                        <th>PRIX +T.M / RED</th>
                        <th>PRIX +LOCATION</th>
                        <th>D&Eacute;GLA&Ccedil;AGE</th>
                        <th>LAVAGE</th>

                        <th>REV. BRUT</th>
                        <th><span >DISPO TM / RED</span></th>
                        <th>REV. NET</th>
                        <th>
                        </th><th>
                        </th>
		                  </tr></thead><tbody id="tb_'.$NOCAMION.'" >';
    //$rolloffday = $this->transfertRollOffForRapport($dteactive);
    foreach($rolloffday->getResult() as $row)
    {
      /** @var string $fraissupplementaire */
      $fraissupplementaire = (($row->code == 5)? $row->prixpoids : $row->prixsurpluspoids);
      if (($row->IsRecBois == 1) or ($row->IsCarton == 1) or ($row->IsVerre == 1 ))
      {
        $fraissupplementaire = $row->CarRed;
      }
      /** @var string $edit */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')">
										           <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
      /** @var string $refresh */
      $refresh = ' <a aria-label="Edit" title="Cliquer pour recalculer la ligne" onclick="refreshRapRO('.$row->NoDemande.')">
										           <i class="fa fa-refresh btn btn-warning" style="font-size:16px;"></i></a>';
      $showcom = "<a class='btn btn-info btn-sm' style='font-size:14px;margin-left:30px;' id='btnroll_".$row->NoDemande."'
                                onclick='ShowCommandeRap(".$row->NoDemande.")' >Voir Commande</a>";
      $showparent = ($row->idparent == 0)? '' : "<a class='btn btn-default btn-sm' style='font-size:14px;margin-left:30px;' id='btnroll_".$row->NoDemande."'
                                onclick='ShowCommandeRap(".$row->idparent.")' >Voir Parent</a>";
      $returnval .= '<tr id="trligne_'.$row->NoDemande.'">
<td class="supprime"><button type="button"
		                      class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHidePrix(\'trPrix_'.$row->NoDemande.'\','.$row->NoDemande.')" style="outline:none;">
                                  <span id="spShowHide_'.$row->NoDemande.'" class="glyphicon glyphicon-chevron-down" style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
                            <td align="center"><button class="btn btn-primary" title="'.$row->notes.'" onclick="ShowCommandeRap('.$row->NoDemande.')">'.$row->NoDemande.'</button>
'.(($row->idparent > 0)? ' <br /><button class="btn btn-default"  onclick="ShowCommandeRap('.$row->idparent.')">'.$row->idparent.'</button> ' : '').'
</td>
                            <td align="center"><label style="width:120px"><a class="btn btn-info" aria-label="Edit" title="'.$row->Client.': &#013;'.$row->AdresseComplete.'">'.substr($row->Client,0,10).'</label></a></td>
                            <td align="center"><label  class="form-control" onclick="showCodeToSelect()" style="width:140px;font-weight:bold;'.(($row->code == 5)? 'background-color:lime' : '').'" >'.$row->code.'</label></td>
                            <td align="center"><label  class="form-control" style="font-weight:bold;'.(($row->IsTransCour)? 'width:185px;': 'width:140px;').'" >'.
                            $row->TypeDem .(($row->IsTransCour)? '<span style="color:red;font-weight:bold;">-TRANSCOUR</span>': '').'</label></td>
                            <td  align="center"><button class="btn btn-default" style="font-weight:bold;" title="Boite: '.$row->BoiteEnPlace.', Tonnage:'.$row->NbTonnes.' &#013; Depotoire: '.$row->Depotoire.' ">'.$row->FormatCont.'</button></td>
			                      <td  align="center"><label style="width:110px;">'.(($row->code == 5)? $row->Tonnage_5 : $row->NbTonnes).'</label></td>
			                      <td  align="center"><input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  type="text" id="Poids_'.$row->NoDemande.'" name="Poids_'.$row->NoDemande.'"
                                  value="'.number_format( $row->Poids,2).'" /></td>
			                      <td  align="center"><label style="width:110px;">'.$row->TypeMat.'</label></td>

                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;font-weight:bold;">
                            <input class="form-control" style="width: 110px;text-align: right;font-size:18px;background-color:yellow;color:darkblue;" type="text" id="PrixDay_'.$row->NoDemande.'" name="PrixDay_'.$row->NoDemande.'" value="'.number_format( $row->PrixDay,2).'" redonly  data-html="true"';
      $popover = '
 data-toggle="popover" data-trigger="focus" data-placement="left" title="Calcul de la commande #'.$row->NoDemande.'" data-content="'.$showcom.$showparent.'<br /><br />'.$row->InfoCommande.'"';
      if ($row->code == 5 and ($row->FormatContenant == 10 or  $row->FormatContenant == 11))
      {
        $popover = '';
      }
      $returnval .= $popover.'/>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  type="text" id="AjustPrix_'.$row->NoDemande.'" name="AjustPrix_'.$row->NoDemande.'"
                                      value="'.number_format( $row->AjustPrix,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;font-weight:bold;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="PrixDay_'.$row->NoDemande.'" name="PrixDay_'.$row->NoDemande.'" value="'.number_format( $row->CarRed,2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:70px;">
                            <input class="form-control" id="surpluspoids_'.$row->NoDemande.'" name="surpluspoids_'.$row->NoDemande.'"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  style="width: 60px;text-align: right;font-size:14px;font-weight:bold;" type="text" value="'.number_format((($row->code == 5)? $row->surpluspoids_5 : $row->surpluspoids),2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >TM</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:80px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="prixsurpluspoids_'.$row->NoDemande.'" name="prixsurpluspoids_'.$row->NoDemande.'" value="'.number_format((($row->code == 5)? $row->prixpoids : $row->prixsurpluspoids)  ,2).'" readonly data-html="true"';

      $popover = '
 data-toggle="popover" data-trigger="focus" data-placement="left" title="Calcul Charge Disposition #'.$row->NoDemande.'" data-content="'.$showcom.$showparent.'<br /><br />'.$row->Parti_1.'<br />'.$row->Parti_2.'"';
      $chksurplus = ((($row->code == 5)? $row->prixpoids : $row->prixsurpluspoids) == 0);
      if ($chksurplus)
      {
        $popover = '';
      }
      $returnval .= $popover.'/>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:130px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text"
                                    onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                    onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                            id="SurplusLocation_'.$row->NoDemande.'" name="SurplusLocation_'.$row->NoDemande.'" value="'.number_format( $row->SurplusLocation,2).'"
                              data-html="true"';
      $surloc = '';
      if ($row->SurplusLocation > 0)
      {
        $surloc = 'data-toggle="popover" data-trigger="focus" data-placement="bottom" title="Calcul surplus Location #'.$row->NoDemande.'" data-content="'.$showcom.$showparent.'<br /><br />'.$row->InfoLocation.'"';
      }
      $returnval .= $surloc. (($row->HaveSurplusLocation != 1)? ' disabled ': ''). '/>
                                <span class="input-group-btn">
                                  <label class="form-control" style="height:30px;" >$</label>
                                </span>
                            <div class="input-group-btn">
                            <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px" onclick="chkSurplusLoc('.$row->NoDemande.')"
                          '.(($row->HaveSurplusLocation == 1)? ' checked ': '').' id="chkSurplusLoc_'.$row->NoDemande.'"  >

                          </div></div>

                            </td>
                            <td  align="center">
                              <div class="input-group input-group-sm" style="width:140px;">
                                <input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;" type="text"
                                    onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                    onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                    id="Deglacage_'.$row->NoDemande.'" name="Deglacage_'.$row->NoDemande.'"
                                    value="'.number_format( $row->Deglacage,2).'"  '.(($row->IsDeglacage == 0)? ' disabled ': '').' />
                                    <span class="input-group-btn">
                                      <label class="form-control" style="height:30px;" >$</label>
                                    </span>
                                    <div class="input-group-btn">
                                      <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px"
                                          onclick="chkDeglacage('.$row->NoDemande.')" id="chkDeglacage_'.$row->NoDemande.'"
                                              '.(($row->IsDeglacage == 1)? ' checked ': '').'  >
                                      </div>
                              </div>
                            </td>

                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:130px;">
                            <input class="form-control" style="width: 80px;height:30;text-align: right;font-size:14px;font-weight:bold;"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  type="text" id="Lavage_'.$row->NoDemande.'" name="Lavage_'.$row->NoDemande.'"
                                  value="'.number_format( $row->Lavage,2).'"  '.(($row->IsLavage == 0)? ' disabled ': '').' />
                                <span class="input-group-btn">
                                  <label class="form-control" style="height:30px;" >$</label>
                                </span>
                            <div class="input-group-btn">
                            <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px" onclick="chkLavage('.$row->NoDemande.')"
                          '.(($row->IsLavage == 1)? ' checked ': '').' id="chkLavage_'.$row->NoDemande.'"  >

                          </div></div>

                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:140px;">
                            <input class="form-control" style="width: 110px;text-align: right;font-size:18px;background-color:yellow;color:darkblue;font-weight:bold;" type="text" id="RevBrut_'.$row->NoDemande.'" name="RevBrut_'.$row->NoDemande.'" value="'.number_format( $row->PrixDay + $fraissupplementaire + $row->Ajustement + (($row->HaveSurplusLocation == 1)? $row->SurplusLocation : 0) ,2).'" readonly  data-html="true" ';
      $popover = '
         data-toggle="popover" data-trigger="focus" data-placement="left" title="Calcul Charge Disposition #'.$row->NoDemande.'" data-content="'.$showcom.$showparent.'<br /><br />'.$row->InfoRevBrute.'"';
      $returnval .= $popover.'/>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm"  style="width:145px;">
                            <input class="form-control" id="CoutDepotoire_'.$row->NoDemande.'" name="CoutDepotoire_'.$row->NoDemande.'" style="width: 110px;text-align: right;font-size:18px;font-weight:bold;color:darkred;" type="text" value="'.number_format($row->CoutDepotoire ,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:140px;">
                            <label  id="revenunet_'.$row->NoDemande.'" name="revenunet_'.$row->NoDemande.'" style="width: 110px;text-align: right;font-size:18px;font-weight:bold;padding-top:4px;background-color:darkred;color:white;height:30px;" >'.number_format( ($row->PrixDay + $fraissupplementaire + $row->Ajustement + (($row->HaveSurplusLocation == 1)? $row->SurplusLocation : 0) - $row->CoutDepotoire  ),2).' </label>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;margin-top:-5px;" >$</label>
                            </span></div>
                            </td>
                            <td>'.$post.'</td><td>'.$refresh.'</td></tr>
                            <tr id="trPrix_'.$row->NoDemande.'" class="separateur" style="display:none"><td  class="no-line">&nbsp;&nbsp;</td><td colspan="16">'.$this->getCalculPrix($row->NoDemande).'</td></tr>
												';
      $strSQL = "UPDATE rapprojour
	              SET
		              RevenuBrut=(SELECT SUM(IFNULL(PrixDay,0) + IFNULL(prixsurpluspoids,0))
	                            FROM rolloffday WHERE NOCAMION = $NOCAMION AND date_fact = $dteactive),
		              Enfouissement=(SELECT SUM(IFNULL(CoutDepotoire,0))
	                            FROM rolloffday WHERE NOCAMION = $NOCAMION AND date_fact = $dteactive)
	              WHERE  nocamion = $NOCAMION AND datejour = $dteactive";
      $rap = $this->db->query($strSQL);
    }
    return $returnval .' </tbody></table> </td></tr>';
  }
  /**
   *
   * @param int $idcommande
   *
   * @return string
   */
  public function getLigneCommandes($idcommande) {

    /** @var string $returnval */

    $strSQL = "SELECT rolloffday.*, (Deglacage + Lavage + AjustPrix) as Ajustement,
                  if(`code` = 5, getRedevance(idclient, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), Poids), 0) as Redevance,
                  `getPrixPoids`(idclient, Poids, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0)) AS prixpoids, getTonnage(idclient, FormatContenant) as Tonnage_5, ROUND( Poids - getTonnage(idclient, FormatContenant), 2) AS surpluspoids_5,`getPrixDepotoire`(idclient, Poids, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0)) AS prixdepotoire
                  FROM rolloffday WHERE Nodemande = $idcommande";
    $rolloffday = $this->db->query($strSQL);
    $returnval = '';

    foreach($rolloffday->getResult() as $row)
    {
      /** @var string $fraissupplementaire */
      $fraissupplementaire = (($row->code == 5)? $row->prixpoids : $row->prixsurpluspoids);
      $NOCAMION = $row->NOCAMION;
      if (($row->IsRecBois == 1) or ($row->IsCarton == 1) or ($row->IsVerre == 1 ))
      {
        $fraissupplementaire = $row->CarRed;
      }
      /** @var string $edit */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')">
										           <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
      /** @var string $refresh */
      $refresh = ' <a aria-label="Edit" title="Cliquer pour recalculer la ligne" onclick="refreshRapRO('.$row->NoDemande.')">
										           <i class="fa fa-refresh btn btn-warning" style="font-size:16px;"></i></a>';
      $returnval .= '
<td class="supprime"><button type="button"
		                      class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHidePrix(\'trPrix_'.$row->NoDemande.'\','.$row->NoDemande.')" style="outline:none;">
                                  <span id="spShowHide_'.$row->NoDemande.'" class="glyphicon glyphicon-chevron-down" style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
                            <td align="center"><button class="btn btn-primary" title="'.$row->notes.'" onclick="ShowCommandeRap('.$row->NoDemande.')">'.$row->NoDemande.'</button>
'.(($row->idparent > 0)? ' <br /><button class="btn btn-default"  onclick="ShowCommandeRap('.$row->idparent.')">'.$row->idparent.'</button> ' : '').'
</td>
                            <td align="center"><label style="width:120px"><a class="btn btn-info" aria-label="Edit" title="'.$row->Client.': &#013;'.$row->AdresseComplete.'">'.substr($row->Client,0,10).'</label></a></td>
                            <td align="center"><label  class="form-control" onclick="showCodeToSelect()" style="width:140px;font-weight:bold;'.(($row->code == 5)? 'background-color:lime' : '').'" >'.$row->code.'</label></td>
                            <td align="center"><label  class="form-control" style="font-weight:bold;'.(($row->IsTransCour)? 'width:185px;': 'width:140px;').'" >'.
                            $row->TypeDem .(($row->IsTransCour)? '<span style="color:red;font-weight:bold;">-TRANSCOUR</span>': '').'</label></td>
                            <td  align="center"><button class="btn btn-default" style="font-weight:bold;" title="Boite: '.$row->BoiteEnPlace.', Tonnage:'.$row->NbTonnes.' &#013; Depotoire: '.$row->Depotoire.' ">'.$row->FormatCont.'</button></td>
			                      <td  align="center"><label style="width:110px;">'.(($row->code == 5)? $row->Tonnage_5 : $row->NbTonnes).'</label></td>
			                      <td  align="center"><input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  type="text" id="Poids_'.$row->NoDemande.'" name="Poids_'.$row->NoDemande.'"
                                  value="'.number_format( $row->Poids,2).'" /></td>
			                      <td  align="center"><label style="width:110px;">'.$row->TypeMat.'</label></td>

                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;font-weight:bold;">
                            <input class="form-control" style="width: 110px;text-align: right;font-size:18px;background-color:yellow;color:darkblue;" type="text" id="PrixDay_'.$row->NoDemande.'" name="PrixDay_'.$row->NoDemande.'" value="'.number_format( $row->PrixDay,2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  type="text" id="AjustPrix_'.$row->NoDemande.'" name="AjustPrix_'.$row->NoDemande.'"
                                      value="'.number_format( $row->AjustPrix,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:110px;font-weight:bold;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="CarRed_'.$row->NoDemande.'" name="CarRed_'.$row->NoDemande.'" value="'.number_format( $row->CarRed,2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:70px;">
                            <input class="form-control" id="surpluspoids_'.$row->NoDemande.'" name="surpluspoids_'.$row->NoDemande.'"
                                  style="width: 60px;text-align: right;font-size:14px;font-weight:bold;" type="text" value="'.number_format((($row->code == 5)? $row->surpluspoids_5 : $row->surpluspoids),2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >TM</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:80px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="prixsurpluspoids_'.$row->NoDemande.'" name="prixsurpluspoids_'.$row->NoDemande.'" value="'.number_format((($row->code == 5)? $row->prixpoids : $row->prixsurpluspoids)  ,2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:130px;">
                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="SurplusLocation_'.$row->NoDemande.'" name="SurplusLocation_'.$row->NoDemande.'" value="'.number_format( $row->SurplusLocation,2).'" disabled />
                                <span class="input-group-btn">
                                  <label class="form-control" style="height:30px;" >$</label>
                                </span>
                            <div class="input-group-btn">
                            <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px" onclick="chkSurplusLoc('.$row->NoDemande.')"
                          '.(($row->HaveSurplusLocation == 1)? ' checked ': '').' id="chkSurplusLoc_'.$row->NoDemande.'"  >

                          </div></div>

                            </td>
                            <td  align="center">
                              <div class="input-group input-group-sm" style="width:140px;">
                                <input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;" type="text"
                                    onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                    onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                    id="Deglacage_'.$row->NoDemande.'" name="Deglacage_'.$row->NoDemande.'"
                                    value="'.number_format( $row->Deglacage,2).'"  '.(($row->IsDeglacage == 0)? ' disabled ': '').' />
                                    <span class="input-group-btn">
                                      <label class="form-control" style="height:30px;" >$</label>
                                    </span>
                                    <div class="input-group-btn">
                                      <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px"
                                          onclick="chkDeglacage('.$row->NoDemande.')" id="chkDeglacage_'.$row->NoDemande.'"
                                              '.(($row->IsDeglacage == 1)? ' checked ': '').'  >
                                      </div>
                              </div>
                            </td>

                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:130px;">
                            <input class="form-control" style="width: 80px;height:30;text-align: right;font-size:14px;font-weight:bold;"
                                  onkeyup="EnterField(this.value, event,'.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  onblur="postDayRapRO('.$row->NoDemande.',\''.$NOCAMION.'\')"
                                  type="text" id="Lavage_'.$row->NoDemande.'" name="Lavage_'.$row->NoDemande.'"
                                  value="'.number_format( $row->Lavage,2).'"  '.(($row->IsLavage == 0)? ' disabled ': '').' />
                                <span class="input-group-btn">
                                  <label class="form-control" style="height:30px;" >$</label>
                                </span>
                            <div class="input-group-btn">
                            <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px" onclick="chkLavage('.$row->NoDemande.')"
                          '.(($row->IsLavage == 1)? ' checked ': '').' id="chkLavage_'.$row->NoDemande.'"  >

                          </div></div>

                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:140px;">
                            <input class="form-control" style="width: 110px;text-align: right;font-size:18px;background-color:yellow;color:darkblue;font-weight:bold;" type="text" id="RevBrut_'.$row->NoDemande.'" name="RevBrut_'.$row->NoDemande.'" value="'.number_format( $row->PrixDay + $fraissupplementaire + $row->Ajustement + $row->SurplusLocation ,2).'" disabled />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm"  style="width:145px;">
                            <input class="form-control" id="CoutDepotoire_'.$row->NoDemande.'" name="CoutDepotoire_'.$row->NoDemande.'" style="width: 110px;text-align: right;font-size:18px;font-weight:bold;color:darkred;" type="text" value="'.number_format($row->CoutDepotoire ,2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >$</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:140px;">
                            <label  id="revenunet_'.$row->NoDemande.'" name="revenunet_'.$row->NoDemande.'" style="width: 110px;text-align: right;font-size:18px;font-weight:bold;padding-top:4px;background-color:darkred;color:white;height:30px;" >'.number_format( ($row->PrixDay + $fraissupplementaire + $row->Ajustement + $row->SurplusLocation - $row->CoutDepotoire  ),2).' </label>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;margin-top:-5px;" >$</label>
                            </span></div>
                            </td>
                            <td>'.$post.'</td><td>'.$refresh.'</td>';
    }
    return $returnval ;
  }
  private function infoPrix5Compacteur($commande)
  {

    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 110px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Transport :</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->fraisfixe.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 80px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Dispo</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->fraispoids.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 110px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Rec. bois</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->fraisRecBois.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Rec. carton</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" type="text"
value="'.$commande->fraisRecCarton.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Rec. verre</label></span>
                                  <input class="form-control" id="Prix" name="Prix" type="text"
value="'.$commande->fraisRecVerre.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 130px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Car. site / TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->fraisCarburantSite.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 130px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Car. fixe</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->fraisCarburant.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 170px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Car. %</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->PourcentCarburant.'%" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
<span class="input-group-btn form-control" style="width: 130px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Red. / TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->fraisRedevanceTM.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Redevance REC.</label></span>
                                  <input class="form-control" id="FraisRedevanceRec" name="FraisRedevanceRec" type="text" value="'.$commande->FraisRedevanceRec.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 130px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Red. fixe</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->FraisRedevance.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
';
    return $returnval;
  }
  private function infoPrix5_12v($commande)
  {
    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Prix</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->prix_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais poids</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->fraispoids_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->carburant_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >% Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->PourcentCarburant_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" type="text"
value="'.$commande->carburant_site_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Tonnage</label></span>
                                  <input class="form-control" id="Prix" name="Prix" type="text"
value="'.$commande->tonnage_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->carburant_site_12v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>

                                  </div>
';
    return $returnval;
  }
  private function infoPrix5_15v($commande)
  {
    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Prix</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->prix_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais poids</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->fraispoids_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->carburant_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >% Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->PourcentCarburant_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" type="text"
value="'.$commande->carburant_site_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Tonnage</label></span>
                                  <input class="form-control" id="Prix" name="Prix" type="text"
value="'.$commande->tonnage_15v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->carburant_site_15v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>

                                  </div>
';
    return $returnval;
  }
  private function infoPrix5_20v($commande)
  {
    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Prix</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->prix_20v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais poids</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->fraispoids_20v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_20v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->carburant_20v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >% Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->PourcentCarburant_12v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" type="text"
value="'.$commande->carburant_site_20v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Tonnage</label></span>
                                  <input class="form-control" id="Prix" name="Prix" type="text"
value="'.$commande->tonnage_20v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->carburant_site_20v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>

                                  </div>
';
    return $returnval;
  }
  private function infoPrix5_30v($commande)
  {
    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Prix</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->prix_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais poids</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->fraispoids_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance TM</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_30v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->carburant_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >% Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->PourcentCarburant_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" type="text"
value="'.$commande->carburant_site_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Tonnage</label></span>
                                  <input class="form-control" id="Prix" name="Prix" type="text"
value="'.$commande->tonnage_30v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->carburant_site_30v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>

                                  </div>
';
    return $returnval;
  }
  private function infoPrix5_40v($commande)
  {
    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Prix</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->prix_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais poids</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->fraispoids_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 170px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais redevance TM</label></span>
<input class="form-control" id="gazeline" name="gazeline" type="text" value="'.$commande->redevance_40v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->carburant_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >% Carburant</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" type="text"
value="'.$commande->PourcentCarburant_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" type="text"
value="'.$commande->carburant_site_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Tonnage</label></span>
                                  <input class="form-control" id="Prix" name="Prix" type="text"
value="'.$commande->tonnage_40v.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site TM</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" type="text" value="'.$commande->carburant_site_40v_tm.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>

                                  </div>
';
    return $returnval;
  }
  private function infoPrix6($commande)
  {
    $returnval ='
                                <div class="input-group"><span class="input-group-btn form-control" style="width: 100px;background-color:lightcoral">
                                     <label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Redevance :</label>
                                   </span>
								<input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->Redevance.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 110px;background-color:lightcoral"><label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant</label></span>
<input class="form-control" id="gazeline" name="gazeline" onblur="CalculPrix()" type="text" value="'.$commande->gazeline.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 150px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais de distance</label></span>
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" onblur="CalculPrix()" type="text"
value="'.$commande->fraisdistance.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 180px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Frais de d&eacute;molition</label></span>
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" onblur="CalculPrix()" type="text"
value="'.$commande->fraisdemolition.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 70px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Prix</label></span>
                                  <input class="form-control" id="Prix" name="Prix" onblur="CalculPrix()" type="text"
value="'.$commande->Prix.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
<span class="input-group-btn form-control" style="width: 130px;background-color:lightcoral">
<label  style="text-align:left;font-size:medium;font-weight:bold;background-color:lightcoral;color:white;" >Carburant site</label></span>
                                  <input class="form-control" id="carburant_site" name="carburant_site" onblur="CalculPrix()" type="text" value="'.$commande->carburant_site.'" style="width:90px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;"  readonly />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
';
    return $returnval;
  }
  private function getCalculPrix($idcommande)
  {
    //----------------------------------------------
    $strSQL = "Select commandes.Code, commandes.Prix, commandes.Redevance, commandes.gazeline, commandes.carburant_site, commandes.AutreFrais, commandes.fraisdistance, fraisfixe, fraispoids, fraisRecBois, fraisRecCarton, fraisRecVerre, fraisCarburant, fraisCarburantSite, PourcentCarburant, fraisRedevanceTM,FraisRedevanceRec, FraisRedevance, prix_12v, fraispoids_12v, redevance_12v, redevance_12v_tm, carburant_12v, carburant_site_12v, tonnage_12v, prix_15v, fraispoids_15v, redevance_15v, redevance_15v_tm, carburant_15v, carburant_site_15v, tonnage_15v, prix_20v, fraispoids_20v, redevance_20v, redevance_20v_tm, carburant_20v, carburant_site_20v, tonnage_20v, prix_30v, fraispoids_30v, redevance_30v, redevance_30v_tm, carburant_30v, carburant_site_30v, tonnage_30v, prix_40v, fraispoids_40v, redevance_40v, redevance_40v_tm, carburant_40v, carburant_site_40v, tonnage_40v, carburant_site_12v_tm, carburant_site_15v_tm, carburant_site_20v_tm, carburant_site_30v_tm, carburant_site_40v_tm
      ,commandes.fraisdemolition, commandes.PrixVariation, commandes.FormatContenant, PourcentCarburant_12v, PourcentCarburant_15v, PourcentCarburant_20v, PourcentCarburant_30v, PourcentCarburant_40v,
      clients.fraisfixe from commandes INNER JOIN clients ON commandes.idclient = clients.id where commandes.id = $idcommande";
    $commande = $this->db->query($strSQL)->getRow(0);
    //----------------------------------------------
    switch ($commande->Code)
    {
      case 5:
        if (($commande->FormatContenant == 10) or ($commande->FormatContenant == 11))
        {
          return $this->infoPrix5Compacteur($commande);
        }
        else if ($commande->FormatContenant == 2)
        {
          return $this->infoPrix5_20v($commande);
        }
        else if ($commande->FormatContenant == 6)
        {
          return $this->infoPrix5_40v($commande);
        }
      case 65:
      case 6:
        return $this->infoPrix6($commande);
    }
    $returnval ='<td  class="no-line"><input class="form-control" id="Redevance" name="Redevance" type="text"
        value="'.$commande->Redevance.'" style="width:110px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >Carburant :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="gazeline" name="gazeline" onblur="CalculPrix()" type="text" value="'.$commande->gazeline.'" style="width:110px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >Carburant site :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="carburant_site" name="carburant_site" onblur="CalculPrix()" type="text" value="'.$commande->carburant_site.'" style="width:110px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >Frais de distance :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="fraisdistance" name="fraisdistance" onblur="CalculPrix()" type="text"
value="'.$commande->fraisdistance.'" style="width:110px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >Frais de d&eacute;molition :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="fraisdemolition" name="fraisdemolition" onblur="CalculPrix()" type="text"
value="'.$commande->fraisdemolition.'" style="width:110px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >Prix :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="Prix" name="Prix" onblur="CalculPrix()" type="text"
value="'.$commande->Prix.'" style="width:110px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class=\'fa fa-dollar\' style=\'font-size:18px;color:darkblue;\'></i>
                                   </span>
                                  </div>
                              </td>
                            </tr>';
  }
  public function transfertRollOffForRapport($dteActive)
  {
    $strSQL = "CALL rpRoUpdIns($dteActive)";
    $this->db->query($strSQL);
  }
  /**
  * @param string $dateactive
  *
  * @return array
  */
  public function getCamChaufDay($dateactive)
  {
    $get = $this->input->post_get('titre',  TRUE);
    $print = (isset($get));
    $this->data['IsLoaded'] = '2';
    /** @var string $returnval */
    $returnval = '';
    /** @var int $trIter */
    $trIter = 1;
    $dteactive = $this->db->escape($dateactive);
    $strSQL = "SELECT  (SELECT  CONCAT(Prenom, ' ', Nom ) AS NomComplet FROM reshumaine WHERE id = suivis.idrh ) as Chauffeur,
		              camionjour.id, camionjour.NOCAMION, KiloDeb, KiloFin, Fuel, (KiloFin - KiloDeb) AS Km,
		              suivis.EndTime, suivis.StartTime, idrh, LEFT( TIMEDIFF(suivis.EndTime,suivis.StartTime), 5) as HrsTrav,
                  camionjour.id AS idcam, suivis.id AS idsuivis, suivis.idrh
              FROM camionjour INNER JOIN suivis ON camionjour.NOCAMION = suivis.NOCAMION WHERE camionjour.NOCAMION IN
                  ( SELECT NoCamion FROM camions WHERE TypeCamion = 4 ) AND camionjour.DateJour = $dteactive
	               AND  suivis.datejour = $dteactive
	               AND idrh NOT IN (24,25) AND camionjour.NOCAMION IN
                   (SELECT commandes.NOCAMION FROM commandes WHERE date_fact = $dteactive )
              ORDER BY camionjour.NOCAMION";
    $rolloffday = $this->db->query($strSQL);
    foreach($rolloffday->getResult() as $row)
    {
      ///** @var string $edit */
      //$post = ' <a aria-label="Edit" title="Cliquer pour enregistrer onclick="DayCamChauf('.$trIter.')">
      //                         <i class="fa fa-edit btn btn-success" style="font-size:16px;"></i></a>';
      $idrh = $row->idrh;
      $nocamion = $this->db->escape($row->NOCAMION);
      $KiloDeb = $this->db->escape($row->KiloDeb);
      $KiloFin =$this->db->escape($row->KiloFin);
      $Fuel = $this->db->escape($row->Fuel);
      $KM = $this->db->escape($row->Km);
      $StartTime = $this->db->escape($row->StartTime);
      $EndTime = $this->db->escape($row->EndTime);
      $HresTrav = $this->db->escape($row->HrsTrav);
      //---------------------------------------------
      $strSQL = "UPDATE rapprojour
	                SET
		                idrh=$idrh,
		                KiloDeb=$KiloDeb,
		                KIloFin=$KiloFin,
		                Fuel=$Fuel,
		                KM=$KM,
		                StartTime=$StartTime,
		                EndTime=$EndTime,
		                HresTrav=$HresTrav,
PrixChauf = (select salairechauffeur from optionssys ),
PrixFuel = (select montantfuel from optionssys )
                WHERE nocamion = $nocamion AND datejour = $dteactive";
      $this->db->query($strSQL);
      $test = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;'
                                onclick='DayCamChauf($trIter)' title='Enregistrer'><i class='fa fa-check btn btn-success' style='font-size:16px;'></i></a>";
      $errclass = ' btn-danger ';
      $returnval .= '<tr>
<td class="supprime"><button type="button"
		                      class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHideRap(\'tr_'.$trIter.'\','.$trIter.')" style="outline:none;">
                                  <span id="spShowHide_'.$trIter.'" class="glyphicon glyphicon-chevron-down" style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
                            <td align="left"><label id="lb_chauf_'.$trIter.'" style="width:190px">'.$row->Chauffeur.'</label>
                            <input type="hidden" id="idsuivis_'.$trIter.'" value="'.$row->idsuivis.'" />
                            <input type="hidden" id="idcam_'.$trIter.'" value="'.$row->idcam.'" />
                            <input type="hidden" id="idrh_'.$trIter.'" value="'.$row->idrh.'" />
                            </td>
                            <td align="center"><label id="lb_cam_'.$trIter.'" style="width:70px;color:darkred;">'.$row->NOCAMION.'</label></td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:130px;">

                            <input class="form-control" type="text" id="KiloDeb_'.$trIter.'" style="width:100px;text-align: right;font-size:14px" value="'.number_format( $row->KiloDeb, 2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >KM</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:130px;">

                            <input class="form-control '.(($row->KiloFin == 0)? $errclass : '').'" id="KiloFin_'.$trIter.'" type="text" style="width:100px;text-align: right;font-size:14px" value="'.number_format( $row->KiloFin, 2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >KM</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:125px;">
                            <label class="form-control '.(($row->KiloFin == 0)? $errclass : 'btn-info').'"  id="Km_'.$trIter.'" style="width:80px;text-align: right;font-size:14px">'.number_format( $row->Km, 2).'</label>
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >KM</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <div class="input-group input-group-sm" style="width:118px;">

                            <input class="form-control" type="text" id="Fuel_'.$trIter.'" style="width:80px;text-align: right;font-size:14px" value="'.number_format( $row->Fuel, 2).'" />
                            <span class="input-group-btn">
                            <label class="form-control" style="height:30px;" >Li</label>
                            </span></div>
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" id="StartTime_'.$trIter.'" style="width:120px;text-align: center;font-size:14px" value="'.$row->StartTime.'" />
                            </td>
                            <td  align="center">
                            <input class="form-control'.(($row->EndTime == '00:00')? $errclass : '').'" type="time" id="EndTime_'.$trIter.'" style="width:120px;text-align: center;font-size:14px" value="'.$row->EndTime.'" />
                            </td>
                            <td  align="center">
                            <input class="form-control '.(($row->EndTime == '00:00')? $errclass : 'btn-info').'" type="time" id="HrsTrav_'.$trIter.'" style="width:100px;text-align: center;font-size:14px" value="'.$row->HrsTrav.'" />
                            </td>

                          </td>

                            <td>'.$test.'</td>
												</tr>'.$this->getRollOffCommandes($dateactive,  $trIter, $row->NOCAMION);
      if (!$print)
      {
        $returnval .= " <script> $('#tr_".$trIter."').hide(); </script>";
      }

      $trIter++;
    }
    $this->data['rapdaychauf_cam'] = $returnval;
    $this->data['raprojour'] = $this->RapRODay($dateactive, 1);
    return $this->data;
  }
  /**
  * @param string $dateactive
  *
  * @return string
  */
  private function getChauffeursDay($dateactive)
  {
    /** @var string $returnval */
    $returnval = '';
    $dateactive = $this->db->escape($dateactive);
    $strSQL = "SELECT id,idrh, StartTime,EndTime,dinerdeb,dinerfin,NOCAMION,
                  (SELECT  CONCAT(Prenom, ' ', Nom ) AS NomComplet FROM reshumaine WHERE id = suivis.idrh ) as Chauffeur,
                  left(GetHresWork(CONCAT(datejour, ' ', FormatDateEnd(StartTime)), CONCAT(datejour, ' ', FormatDateEnd(EndTime) ),
								                  CONCAT(datejour, ' ', dinerdeb), CONCAT(datejour, ' ', dinerfin),
								                  CONCAT(datejour, ' ', souperdeb), CONCAT(datejour, ' ', souperfin)), 5) as hres
                   FROM suivis WHERE datejour = $dateactive AND idrh NOT IN (24,25) AND
                   idrh IN (SELECT commandes.idchauffeurs FROM commandes WHERE date_fact = $dateactive AND commandes.IsComplete = 1) AND
                  NOCAMION IN (SELECT NoCamion FROM camions WHERE camions.TypeCamion = 4)
                  ORDER BY
                  (SELECT  CONCAT(Prenom, ' ', Nom ) AS NomComplet FROM reshumaine WHERE id = suivis.idrh )";
    $rolloffday = $this->db->query($strSQL);
    foreach($rolloffday->getResult() as $row)
    {
      /** @var string $edit */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer onclick="postDayRapRO(\''.$row->id.'\')">
										           <i class="fa fa-edit btn btn-success" style="font-size:16px;"></i></a>';
      $returnval .= '<tr>

                            <td align="left"><label style="width:190px">'.$row->Chauffeur.'</label></td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="'.$row->StartTime.'" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="'.$row->EndTime.'" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="'.$row->dinerdeb.'" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="'.$row->dinerfin.'" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="'.$row->hres.'" />
                            </td>
                            <td align="center"><label style="width:70px">'.$row->NOCAMION.'</label></td>
                            <td>&nbsp;&nbsp;'.$post.'&nbsp;&nbsp;</td>
												</tr>';
    }
    return $returnval;
  }
  /**
   * @param string $dateactive
   * @param int $from
   *
   * @return string
   */
  public function RapRODay($dateactive, $from)
  {
    /** @var string $returnval */
    $returnval = '';
    $dateactive = $this->db->escape($dateactive);
    $strWhere = ($from == 1)? ' and  idrh > 0 ' : '';
    $strSQL = "SELECT datejour, nocamion, KiloDeb, KIloFin, Fuel, KM, StartTime, EndTime, HresTrav,
          PrixFuel, RevenuBrut, Enfouissement, PrixChauf,
        (LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf AS CoutCH,
        (Fuel * PrixFuel) AS CoutFuel,
        (RevenuBrut -(((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) +
	        (Fuel * PrixFuel) + Enfouissement)) AS RevenuNet,
        (RevenuBrut -(((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) +
	        (Fuel * PrixFuel) + Enfouissement))/ (LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) AS MoyenneHR FROM rapprojour where datejour = $dateactive $strWhere";
    $rap = $this->db->query($strSQL);
    foreach($rap->getResult() as $row)
    {
      $returnval .= '<tr>
                            <td></td>
                            <td align="center"><label style="width:80px;text-align:center;width:100%">'.$row->nocamion.'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->KiloDeb, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->KIloFin, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->KM, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->RevenuBrut, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->Fuel, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->CoutFuel, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:center;width:100%">'.$row->HresTrav.'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->CoutCH, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->Enfouissement, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->RevenuNet, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">'.number_format( $row->MoyenneHR, 2).'</label></td> <td></td>
												</tr>';
    }
    $strSQL = "SELECT SUM(Fuel) as Fuel, SUM(KM) as KM, SUM((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 ))) AS smHres,
                  sum(RevenuBrut) as RevenuBrut, sum(Enfouissement) as Enfouissement,
                SUM((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) AS CoutCH,
                sum(Fuel * PrixFuel) AS CoutFuel,
                sum(RevenuBrut -(((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) +
	                (Fuel * PrixFuel) + Enfouissement)) AS RevenuNet,
                SUM((RevenuBrut -(((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) +
	                (Fuel * PrixFuel) + Enfouissement))/ (LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 ))) AS MoyenneHR
                FROM rapprojour where datejour = $dateactive GROUP BY datejour";
    $totalrap = $this->db->query($strSQL)->getRow(0);
    if(isset($totalrap))
    {
      $returnval .= '<tr>
                            <td colspan="4" align="center"><label style="width:80px;text-align:center;width:100%;font-size:large;font-weight: bold;color: darkred">TOTAL</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->KM, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->RevenuBrut, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->Fuel, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->CoutFuel, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:center;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->smHres, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->CoutCH, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->Enfouissement, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->RevenuNet, 2).'</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">'.number_format( $totalrap->MoyenneHR, 2).'</label></td>
                            <td></td>
											  </tr>';
    }
    return $returnval;
  }
  function __destruct() {
      $this->db->close();
  }
}
