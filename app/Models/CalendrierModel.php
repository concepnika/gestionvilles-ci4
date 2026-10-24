<?php

namespace App\Models;

/**
 * Port mecanique de Calendrier_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class CalendrierModel extends Ci3Model
{
   /** @var array $data */
  public $data;
  protected $mois;
	protected $trIter = 1;
  function __construct() {
    parent::__construct();
    
    
		
		
    $this->mois = array('Jan','F&eacute;v','Mar','Avr','Mai','Jun','Jul','Ao&ucirc;','Sep','Oct','Nov','D&eacute;c');
  }
  public function validate_user($data) {
          /** @var string $password */
          $password = $data['password'];
          $sql = "Select MD5( '$password') AS password";
          $query = $this->db->query($sql,FALSE);
          /** @var string $encrypt_pass */
          $encrypt_pass = '';
          /** @var object $encrypt_pass */
          $data2 = new \stdClass();
          if($data2 = $query->getRow(0))
          {
              $encrypt_pass =  ((isset($data2->password)) ?$data2->password : '');
          }
      $this->db->where('email', $data['email']);
      $this->db->where('pass', $encrypt_pass);
      return $this->db->get('utilisateurs')->getRow();
  }
  public function getSuivis($valtofind)
  {
    $dateEnd = '';
    $dteprev = '';
    $date = $this->db->escape( date('Y-m-d'));
    $returnval = '';
    $activedate = $this->input->get_post('dtedeb');
    $dateend = $this->input->get_post('dtefin');
    $this->data['ActiveDate'] = $this->input->get_post('dtedeb');
    $this->data['dateend'] = $this->input->get_post('dtefin');
    if (!isset($activedate))
    {
      $date = new \DateTime(date('Y-m-d'));
      date_sub($date,date_interval_create_from_date_string("1 year"));
      $this->data['ActiveDate'] = date_format($date,"Y-m-d");
    }
    if (!isset($dateend))
    {
      $date = new \DateTime(date('Y-m-d'));
      date_add($date,date_interval_create_from_date_string("1 year"));
      $this->data['dateend'] = date_format($date,"Y-m-d");
    }
    $arrDateFields = explode('-', $this->data['ActiveDate']);
    $this->data['ActiveDate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
    $arrDateFields = explode('-', date('Y-m-d'));
    $this->data['caldate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
    $date = ' >= '.$this->db->escape( $this->data['ActiveDate']).' and ( dateend > CURDATE() or dateend is null) ';
    $strSQL = "SELECT suivisrh.id, CONCAT( reshumaine.Prenom, ' ', reshumaine.Nom ) AS  NomComplet,
                        typeabsence.Nom  AS absence, left(datestart, 10) as datestart,
                     ifnull(avistoshow, '') AS Description, if(dateretourprevu is null, '',left(dateretourprevu, 10)) as endStr,typeabsence.couleur  AS backcolor,typeabsence.textcolor  AS textcolor,
                     suivisrh.dateend, suivisrh.note, suivisrh.dateretourprevu, suivisrh.showavis, suivisrh.avistoshow, suivisrh.idabsence, suivisrh.idrh
	                    FROM suivisrh INNER JOIN  reshumaine ON suivisrh.idrh = reshumaine.id
							  INNER JOIN typeabsence ON suivisrh.idabsence = typeabsence.id  WHERE suivisrh.datestart $date $valtofind and reshumaine.isdeleted = 0 order by suivisrh.datestart";
    /** @var object $cedules */
    $cedules = $this->db->query($strSQL);
    /** @var string $color */
    /** @var string $colorville */
    foreach($cedules->getResult() as $row)
    {
      $arrDate = explode('-', $row->datestart);
      $dateStart = $arrDate[2].' '.$this->mois[strval($arrDate[1]) - 1];
      if (trim($row->dateretourprevu) != '')
      {
        $arrDateEnd = explode('-', $row->endStr);
        $dateEnd = ' de retour le '.$arrDateEnd[2].' '. $this->mois[strval($arrDateEnd[1]) - 1 ];
        $dteprev = $row->dateretourprevu;
      }
      else{
        $date = new \DateTime(date('Y-m-d'));
        date_add($date,date_interval_create_from_date_string("1 year"));
        $dteprev = date_format($date,"Y-m-d");
        $dateEnd = ' retour ind&eacute;termin&eacute;';
      }
      $note = str_replace("'", "\'", $row->note) ;
      $returnval .= " {
														title: '". $row->NomComplet.' -- '.$row->absence.' --- '.$note."',
														id: '".$row->id."',
														start: '".$row->datestart."',
														end: '".$dteprev."',
														startStr: '".$dateStart."',
														endStr: '".$dateEnd."',
                            color:'".$row->backcolor."',
                            textColor:'".$row->textcolor."',
														description: '".$note."',
														idsuivis: '".$row->id."',
														allDay: true
											},";
    }

    $returnval = substr($returnval,0,-1) ;
    $this->data['dataCD'] =  $returnval;
    return $this->data ;
  }
  public function getSuivisCam($valtofind)
  {
    $dateEnd = '';
    $date = $this->db->escape( date('Y-m-d'));
    $returnval = '';
    $activedate = $this->input->get_post('dtedeb');
    $dateend = $this->input->get_post('dtefin');
    $this->data['ActiveDate'] = $this->input->get_post('dtedeb');
    $this->data['dateend'] = $this->input->get_post('dtefin');
    if (!isset($activedate))
    {
      $date = new \DateTime(date('Y-m-d'));
      date_sub($date,date_interval_create_from_date_string("1 year"));
      $this->data['ActiveDate'] = date_format($date,"Y-m-d");
    }
    if (!isset($dateend))
    {
      $date = new \DateTime(date('Y-m-d'));
      date_add($date,date_interval_create_from_date_string("1 year"));
      $this->data['dateend'] = date_format($date,"Y-m-d");
    }
    $arrDateFields = explode('-', $this->data['ActiveDate']);
    $this->data['ActiveDate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
    $arrDateFields = explode('-', date('Y-m-d'));
    $this->data['caldate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
    $date = ' >= '.$this->db->escape( $this->data['ActiveDate']).' and ( dateend > CURDATE() or dateend is null) ';
    $strSQL = "SELECT suiviscamion.*,
	              CASE
                  WHEN idstatut = 1 THEN 'Hors service'
                  WHEN idstatut = 2 THEN 'Avec Mat�riel'
                  WHEN idstatut = 3 THEN 'EN bris de service'
                  ELSE ''
	              END AS statut, left(datestart, 10) as startStr,
	              if(dateretourprevu is null, '',left(dateretourprevu, 10)) as endStr,
	              (SELECT typededechet.Nom FROM typededechet WHERE typededechet.id =  suiviscamion.TypeDechet) AS dechet,
	              CASE
                  WHEN idstatut = 1 THEN 'darkcyan'
                  WHEN idstatut = 2 THEN 'chocolate'
                  ELSE ''
	              END AS backcolor
              FROM suiviscamion  INNER JOIN camions ON suiviscamion.nocamion = camions.NoCamion
              WHERE datestart  $date $valtofind and camions.isdeleted = 0  order by datestart";
    /** @var object $cedules */
    $cedules = $this->db->query($strSQL);
    /** @var string $color */
    /** @var string $colorville */
    foreach($cedules->getResult() as $row)
    {
      $arrDate = explode('-', $row->datestart);
      $dateStart = $arrDate[2].' '.$this->mois[strval($arrDate[1]) - 1];
      if (trim($row->dateretourprevu) != '')
      {
        $arrDateEnd = explode('-', $row->endStr);
        $dateEnd = ' de retour le '.$arrDateEnd[2].' '. $this->mois[strval($arrDateEnd[1]) - 1 ];
        $dteprev = $row->dateretourprevu;
      }
      else{
        $date = new \DateTime(date('Y-m-d'));
        date_add($date,date_interval_create_from_date_string("2 days"));
        $dteprev = date_format($date,"Y-m-d");
        $dateEnd = ' retour ind&eacute;termin&eacute;';
      }
      if (trim($row->dateretourprevu) != '')
      {
        $arrDateEnd = explode('-', $row->endStr);
        $dateEnd = ' de retour le '.$arrDateEnd[2].' '. $this->mois[strval($arrDateEnd[1]) - 1 ] . ' ' . $row->hreend;
      }
      else{
        $dateEnd = ' retour ind&eacute;termin&eacute;';
      }
      $note = str_replace("'", "\'", $row->note) ;
      $returnval .= " {
														title: '". $row->nocamion.' -- '.utf8_encode($row->statut).' -- '.$note. "',
														id: '".$row->id."',
														start: '".$row->datestart."',
														end: '".$dteprev."',
														startStr: '".$dateStart."',
														endStr: '".$dateEnd."',
                            hrestart: '".$row->hrestart."',
                            hreend: '".$row->hreend."',
                            idsuivis: '".$row->id."',
                            color:'".$row->backcolor."',
														allDay: true
											},";
    }

    $returnval = substr($returnval,0,-1) ;
    $this->data['dataCD'] =  $returnval;
    return $this->data ;
  }
  public function GetDropCamions($intype = '1')
  {

    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT  c.NoCamion FROM camions c where  c.isdeleted = 0 and c.NoCamion <> ''
		              AND TypeCamion  in ($intype)
		              order by TypeCamion, NoCamion";
    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $returnval  .= '<div id="external-events" class="input-group"><span class="input-group-btn"><label id="camion_'.$row->NoCamion.'_HS_'.$intype.'" draggable="true" ondragstart="drag(event)" class="fc-event form-control" style="padding-left: 5px;width:80px;background-color:darkcyan;color:white;" >'. $row->NoCamion .'HS</label><label id="camion_'.$row->NoCamion.'_MAT_'.$intype.'" draggable="true" ondragstart="drag(event)" class="fc-event form-control" style="padding-left: 15px;width:80px;background-color:chocolate;color:white;" >'. $row->NoCamion .'MAT</label></span></div>';
    }
    $this->data['CamionDrop'] = $returnval;
    return $returnval;
  }
  public function GetChauffeursToDrop($intype = '1,3', $valtofind = '')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select reshumaine.id as idchauffeur,
                   CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine
			             WHERE  reshumaine.isdeleted = 0 and idreshum in ($intype) $valtofind  ORDER BY reshumaine.Nom, reshumaine.Prenom";

    $ville = $this->db->query($strSQL);
    foreach($ville->getResult() as $row)
    {
      $returnval  .= '<div id="external-events" data-event=\'{ "title": "'. $row->NomComplet .'", "duration": "$row->idchauffeur" }\' class="input-group"><label id="chauffeur_'.$row->idchauffeur.'_2" draggable="true" ondragstart="drag(event)" class="fc-event form-control" style="margin-left: 5px;width:227px" >'. $row->NomComplet .'</label></div>';
    }
    return $returnval;
  }
  /**
   * @param int $idsuivis
   *
   * @return void
   */
  public function getSuivisRH($idsuivis)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var array $data */
    $data = '';
    $idsuivis = $this->db->escape($idsuivis);
    /** @var string $strSQL */
    $strSQL = "SELECT id, idrh, idabsence,  dateend, note, left(datestart, 10) AS datestart,
                      (SELECT reshumaine.idreshum FROM reshumaine WHERE reshumaine.id = suivisrh.idrh) AS idtyperh,
		                          left(dateretourprevu, 10) AS dateretourprevu, showavis, avistoshow
	                          FROM suivisrh WHERE id =  $idsuivis ";
    $suivis = $this->db->query($strSQL);
    foreach($suivis->getResultArray() as $row)
    {
      $typerh =$row['idtyperh'];
      $idrh = $row['idrh'];
      $this->cedules = new CedulesModel();
      /** @var array $data */
      $data = array('listtype' =>  $this->cedules->GetAllEmployes($typerh,$idrh ));
      $data['lstabsence'] =   $this->cedules->GetTypeAbsences($row['idabsence']);
      $returnval = json_encode(array_merge( $row, $data));
    }
    echo $returnval;
  }
  public function GetAllCamions($nocamion, $typecam)
  {
    $typecam = $this->db->escape($typecam);
    /** @var string $returnval */
    $returnval = '<option selected value="Cam">Choisir</option>';
    /** @var string $strSQL */
    $strSQL = "SELECT
             c.* FROM camions c where c.nocamion <> '' AND  typecamion = $typecam
              and c.isdeleted = 0 order by NoCamion";
    $camions = $this->db->query($strSQL);
    if ((trim($nocamion) != '') and (trim($nocamion) != 'Cam'))
    {
      $returnval .= '<option selected value="'.$nocamion.'">'.$nocamion.'</option>';
    }
    else
    {
      $returnval .= '<option selected value="Cam">Choisir</option>';
    }
    foreach($camions->getResult() as $row)
    {
      $returnval .= '<option value="'.$row->NoCamion.'">'.$row->NoCamion.'</option>';
    }
    $this->data['camions'] = $returnval;
    return $returnval;
  }
  function __destruct() {
      $this->db->close();
  }
}
