<?php

namespace App\Models;

/**
 * Port mecanique de Requetes_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class RequetesModel extends Ci3Model
{
	/** @var array $data */
	protected $data = array();
	public $LastRow = 1;
	public $reqVille = array();
	public $repVille = array();
  public $adrVille = array();
  public $dteVille = array();
  protected $keysha = 'niKa64982c4edeb0brdf23vf';
	function __construct() {
		parent::__construct();
		
		
	}
  /**
   *
   * @param int $idville
   * @param int $rang
   *
   * @return array
   *
   */
  public function insert_data($idville, $rang)
  {
    /** @var array $dataResult */
    $dataResult = array();
    $key = $this->db->escape($this->keysha);
    $email = $this->db->escape(urldecode($this->input->post_get('email')));
    $password = $this->db->escape(urldecode($this->input->post_get('pass')));
    $strSQL = "INSERT INTO villesrecipiendaire
	                  (idville, rang, email, `password`)
	                  VALUES ($idville, $rang,
                              AES_ENCRYPT($email, UNHEX(SHA2($key,512))),
                              AES_ENCRYPT($password, UNHEX(SHA2($key,512))))";
    $this->db->query($strSQL);
    $error = $this->db->error();
    if (!empty($error['code'])) {
      $dataResult['msgerr'] = "Une erreur s'est produite.";
      $dataResult['id'] = 0;
    } else {
      $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
    }
    $dataResult['id'] = $this->db->insertID();
    return $dataResult;
  }

  public function update_data($idville, $rang)
  {
    $key = $this->db->escape($this->keysha);
    $email = $this->db->escape(urldecode($this->input->post_get('email')));
    $password = $this->db->escape(urldecode($this->input->post_get('pass')));
    $strSQL = "UPDATE villesrecipiendaire
                  SET
                    email=AES_ENCRYPT($email, UNHEX(SHA2($key,512))),
                    `password`=AES_ENCRYPT($password, UNHEX(SHA2($key,512))))
                WHERE idville = $idville AND rang = $rang";
    $this->db->query($strSQL);
    /** @var array $dataResult */
    $dataResult = array();
    $error = $this->db->error();
    if (!empty($error['code'])) {
      $dataResult['msgerr'] = "Une erreur s'est produite.";
    } else {
      $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
    }
    return $dataResult;
  }
  public function getInfoConnect($idville)
  {
    $this->data = array();
    $idville = $this->db->escape($idville);
    $key = $this->db->escape($this->keysha);
    /** @var string  $returnval */
    $returnval = '';
    /** @var string  $strSQL */
    $strSQL = "SELECT id, idville, AES_DECRYPT(email, UNHEX(SHA2($key,512))) AS mail, AES_DECRYPT(password, UNHEX(SHA2($key,512))) AS pass, rang, nom,prenom,cell,phone FROM villesrecipiendaire WHERE idville = $idville";
    $recipient = $this->db->query($strSQL);
    foreach ($recipient->getResult() as $row) {
      $this->data['inputEmail' . $row->rang] = $row->mail;
      $this->data['inputPassword' . $row->rang] = $row->pass;
    }
    if (count($this->data) < 3) {
      for ($iter = 1; $iter < 4; $iter++) {
        if (!isset($this->data['inputEmail' . $iter])) {
          $this->data['inputEmail' . $iter] = '';
          $this->data['inputPassword' . $iter] = '';
        }
      }
    }
    return $this->data;
  }
  //------------------------------------------------------------
  //Requetes ...........
	private function chkNewReqRep()
  {
    $strSql = "SELECT requetes.*
									FROM requetes WHERE statut = 1 and IsFrom = 0 and IsVue = 0
							ORDER BY id";
    $query = $this->db->query($strSql);
    foreach ($query->getResult() as $row) {
      //------------------------
      $this->reqVille[] = $row->idville;
    }
		//-------------------------------------------------------------
    $strSql = "SELECT reponses.*
									FROM reponses WHERE statut in (4, 6) and IsFrom = 0 and IsVue = 0
							ORDER BY id limit 1";
    $query = $this->db->query($strSql);
    foreach ($query->getResult() as $row) {
      //------------------------
      $this->repVille[] = $row->idville;
    }
		//-------------------------------------------------------------
    $strSql = "SELECT idrequete, dteCedule,idville FROM daterequete WHERE IsVue = 0 ORDER BY dteCedule DESC LIMIT 1";
    $query = $this->db->query($strSql);
    foreach ($query->getResult() as $row) {
      //------------------------
      $this->dteVille[] = $row->idville;
    }
		//-------------------------------------------------------------
    $strSql = "SELECT idrequete, adresse,idville FROM adrrequete WHERE IsVue = 0 ORDER BY id DESC LIMIT 1";
    $query = $this->db->query($strSql);
    foreach ($query->getResult() as $row) {
      //------------------------
      $this->adrVille[] = $row->idville;
    }
  }
	public function GetAllClassStatut()
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT CONCAT('.statreq',id,' {
								background-color: ', couleur,
								';color: darkblue;
								font-weight:blod;
								padding: 10px 20px;
								margin: 8px 0;
								border: none;
								cursor: pointer;
								width: 250px;
							}') AS classstat FROM statutrequete ORDER BY id";
    $query = $this->db->query($strSQL);
		foreach ($query->getResult() as $row)
    {
			$returnval .= $row->classstat;
    }
		return $returnval;
  }
  /**
   *
   *
   *
   *
   * @return string
   */
  public function GetActivesRequetes()
  {
    /** @var string $table */
		$this->chkNewReqRep();
    /** @var string $returnval */
    $returnval = '';
    /** @var string $drobBtn */
    $drobBtn = '';
    /** @var int $option */
    $option = '';
    /** @var int $maxCol */
    $maxCol = 4;
    /** @var int $Col */
    $Col = 1;
    /** @var int $iter */
    $iter = 1;
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
    /** @var string $strSQL */
    $strSQL = "SELECT id, (SELECT ville.Nom FROM ville WHERE  ville.id = requetes.idville) AS NomVille, nom, adresse, fichier, `description`, statut,
          (SELECT id FROM reponses
											WHERE idrequete = requetes.id AND IsVue = 0 AND
													CASE
														WHEN statut IN (4, 6) THEN 1
														ELSE 0
													END = 1) AS idreponse,left(dteCedule, 10) as dteCedule,
          (SELECT CONCAT('<span style=\"color:red;font-size: 18px;font-weight:bold\">Cédulée le ', left(dteCedule, 10), '</span>') FROM daterequete
											WHERE idrequete = requetes.id AND IsVue = 0 order by id desc limit 1) AS daterequete,
          (SELECT CONCAT('<span style=\"color:red\">Nouvelle adresse ', adresse, '</span>') FROM adrrequete
											WHERE idrequete = requetes.id AND IsVue = 0 order by id desc limit 1) AS newAdresse,
          (SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS Dechet,
          (SELECT $strDesc FROM statutrequete WHERE statutrequete.id =   requetes.statut) AS TypeStatut,
          (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id =   requetes.statut) AS CouleurStatut, Iscomplete, isdeleted, createby, updateby, left(dtecreate, 10) as dtecreate, dteupdate, idville
	          FROM requetes WHERE Iscomplete = 0 AND isdeleted = 0 and statut != 7 ORDER BY id desc";
    $requetes = $this->db->query($strSQL);
    /** @var string $nbRows */
    $nbRows = $requetes->getNumRows();
    $stylehide = ' style="display:block"> ';
    $chevronLeft = '<button class="btn-link" onclick="showRowReq(\'-1\')">
							    <i class="fa fa-chevron-left" style="font-size:24px" ></button>';
    $chevronRight = '<button class="btn-link" onclick="showRowReq(\'1\')">
							    <i class="fa fa-chevron-right" style="font-size:24px"></button>';
    foreach ($requetes->getResult() as $row) {
      if ($Col == 1) {
        $returnval .= '<tr id="trreq_' . $iter . '" '.$stylehide.' ><td>'.$chevronLeft.'</td>';
      }
			else if ($Col >  $maxCol)
      {
        $Col = 1;
        $iter++;
        $stylehide = ' style="display:none"> ';
        $returnval .= '<td>' . $chevronRight . '</td></tr><tr id="trreq_' . $iter . '" ' . $stylehide . ' ><td>' . $chevronLeft . '</td>';
      }
      $spinner = '';
      $option = '<li><a class="dropdown-item" href="/villes/show/requete/' . $row->id . '?from=0" >
							    <i class="fa fa-file-text-o" style="font-size:16px;height:35px;"></i> Afficher la Requête </a></li>';
      if ((array_search($row->idville,$this->reqVille) !==false) and ($row->statut == 1))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
      }
      if ((array_search($row->idville,$this->repVille) !==false) and ($row->idreponse > 0))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="/villes/show/reponse/'. $row->id.'">
							    <i class="fa fa-files-o" style="font-size:16px;height:35px;"></i> Afficher les Réponses </a></li>';
      }
      if ((array_search($row->idville,$this->repVille) !==false) and ($row->idreponse > 0))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="/villes/show/reponse/'. $row->id.'">
							    <i class="fa fa-files-o" style="font-size:16px;height:35px;"></i> Afficher les Réponses </a></li>';
      }
      if ((array_search($row->idville,$this->dteVille) !==false) and ($row->daterequete != ''))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="javascript:confirmeDate('. $row->id.')" >
							     '.$row->daterequete.' </a></li>';
      }
      if ((array_search($row->idville,$this->adrVille) !==false) and ($row->newAdresse != ''))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="javascript:confirmeAdr(' . $row->id . ')">
							     '.$row->newAdresse.' </a></li>';
      }
			$dteCedule = (trim($row->dteCedule) == '')? '<span style="color:red"> Non Cédulée</span>' : $row->dteCedule;
      $message =" <b> $dteCedule </b><br/>
																	Statut:<b> $row->TypeStatut<br/> </b>
                                             <b> Requête: R-$row->id </b> <br/>
                                             <b> Adresse: </b> <br/>
                                              $row->adresse <br/>";
      $message = htmlspecialchars_decode($message, ENT_QUOTES);
      $classBtn = 'statreq'.$row->statut;
      $drobBtn = '  <div class="dropup">
    <button class="btn '. $classBtn.' dropdown-toggle" type="button" data-toggle="dropdown">' . $spinner . $row->NomVille . '<b> ' . $row->id . ' </b>
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      ' . $option . '
    </ul>
  </div>';
      $returnval .= '<td class="cellule" align="left" style="width: 250px;font-size: 12px;margin-left:10px;" ><div class="input-group;color:black;width:250px;">'.$drobBtn.'
																		<span style="margin-left:5px;color:black"><b>' . $dteCedule . ', Statut: ' . $row->TypeStatut . '</b></span> <br />
                                   <span style="margin-left:5px;color:darkred;cursor:pointer" onclick="ShowInGoogle(\'' . $this->Slug($row->adresse) . '\')" ><b> Adr.: ' . $row->adresse . '
                                   </b></span> <br /><span style="margin-left:5px;color:black"><b>' . $row->description . '</b></span></div>
</td>';

      $Col++;
    }
    $this->LastRow = $iter;
    $table = '<table  border="2" cellspacing="2" cellpadding="2"  style="margin: auto;"
               id="tab_logic">';
    return $table.$returnval.'<td>' . $chevronRight . '</td></tr></table>
																		<script>
																			new PNotify({
																				title: "Votre attention est requise",
																				text: Vous avez reçu une notification,
																				type: "warning",
																				hide: true,
																				buttons: {
																					sticker: false
																				}
																			});
																		</script>';
  }
  /**
   *
   *
   *
   *
   * @return string
   */
  public function GetDropActivesRequetes()
  {
    /** @var string $table */
		$this->chkNewReqRep();
    /** @var string $returnval */
    $returnval = '';
    /** @var string $drobBtn */
    $drobBtn = '';
    /** @var int $option */
    $option = '';
    /** @var int $maxCol */
    $maxCol = 4;
    /** @var int $Col */
    $Col = 1;
    /** @var int $iter */
    $iter = 1;
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
    /** @var string $strSQL */
    $strSQL = "SELECT id, (SELECT ville.Nom FROM ville WHERE  ville.id = requetes.idville) AS NomVille, nom, adresse, fichier, `description`, statut,
          (SELECT id FROM reponses
											WHERE idrequete = requetes.id AND IsVue = 0 AND
													CASE
														WHEN statut IN (4, 6) THEN 1
														ELSE 0
													END = 1) AS idreponse,left(dteCedule, 10) as dteCedule,
          (SELECT CONCAT('<span style=\"color:red;font-size: 18px;font-weight:bold\">Cédulée le ', left(dteCedule, 10), '</span>') FROM daterequete
											WHERE idrequete = requetes.id AND IsVue = 0 order by id desc limit 1) AS daterequete,
          (SELECT CONCAT('<span style=\"color:red\">Nouvelle adresse ', adresse, '</span>') FROM adrrequete
											WHERE idrequete = requetes.id AND IsVue = 0 order by id desc limit 1) AS newAdresse,
          (SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS Dechet,
          (SELECT $strDesc FROM statutrequete WHERE statutrequete.id =   requetes.statut) AS TypeStatut,
          (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id =   requetes.statut) AS CouleurStatut, Iscomplete, isdeleted, createby, updateby, left(dtecreate, 10) as dtecreate, dteupdate, idville
	          FROM requetes WHERE Iscomplete = 0 AND isdeleted = 0 and statut = 1 ORDER BY id desc";
    $requetes = $this->db->query($strSQL);
    /** @var string $nbRows */
    $nbRows = $requetes->getNumRows();
    $stylehide = ' style="display:block"> ';
    $chevronLeft = '<button class="btn-link" onclick="showRowReq(\'-1\')">
							    <i class="fa fa-chevron-left" style="font-size:24px" ></button>';
    $chevronRight = '<button class="btn-link" onclick="showRowReq(\'1\')">
							    <i class="fa fa-chevron-right" style="font-size:24px"></button>';
    foreach ($requetes->getResult() as $row) {

      $spinner = '';
      $option = '<li><a class="dropdown-item" href="/villes/show/requete/' . $row->id . '?from=0" >
							    <i class="fa fa-file-text-o" style="font-size:16px;height:35px;"></i> Afficher la Requête </a></li>';
      if ((array_search($row->idville,$this->reqVille) !==false) and ($row->statut == 1))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
      }
      if ((array_search($row->idville,$this->repVille) !==false) and ($row->idreponse > 0))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="/villes/show/reponse/'. $row->id.'">
							    <i class="fa fa-files-o" style="font-size:16px;height:35px;"></i> Afficher les Réponses </a></li>';
      }
      if ((array_search($row->idville,$this->repVille) !==false) and ($row->idreponse > 0))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="/villes/show/reponse/'. $row->id.'">
							    <i class="fa fa-files-o" style="font-size:16px;height:35px;"></i> Afficher les Réponses </a></li>';
      }
      if ((array_search($row->idville,$this->dteVille) !==false) and ($row->daterequete != ''))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="javascript:confirmeDate('. $row->id.')" >
							     '.$row->daterequete.' </a></li>';
      }
      if ((array_search($row->idville,$this->adrVille) !==false) and ($row->newAdresse != ''))
      {
        $spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:black"></i>';
				if (trim($option) != '')
        {
          $option .= '<li class="divider"></li>';
        }
        $option .= '<li><a class="dropdown-item" href="javascript:confirmeAdr(' . $row->id . ')">
							     '.$row->newAdresse.' </a></li>';
      }
			$dteCedule = (trim($row->dteCedule) == '')? '<span style="color:red"> Non Cédulée</span>' : $row->dteCedule;
      $message =" <b> $dteCedule </b><br/>
																	Statut:<b> $row->TypeStatut<br/> </b>
                                             <b> Requête: R-$row->id </b> <br/>
                                             <b> Adresse: </b> <br/>
                                              $row->adresse <br/>";
      $message = htmlspecialchars_decode($message, ENT_QUOTES);
      $classBtn = 'statreq'.$row->statut;
      $drobBtn = '
    <button class="btn '. $classBtn.' type="button" onclick="window.open(\'/villes/show/requete/' . $row->id . '?from=0\')">' . $spinner . $row->NomVille . '<b> ' . $row->id . ' </b>
    <span class="caret"></span></button>
';
      $returnval .= '<li >
											<div class="input-group;color:black;width:250px;">' . $drobBtn . '
													</div>
									</li>';
      $Col++;
    }
      $returnval .= ' <li class="divider" ></li><li>
          <a href="/villes/show/requete/0" target="_blank" style="margin:4px 5px 0 0;width:100%;font-size:24px;">Envoyer une Requ&ecirc;te</a>
        </li>
        <li>

          <a href="/villes/liste/requetes" target="_blank" style="margin:4px 5px 0 0;width:100%;font-size:24px;color:black">Liste des requ&ecirc;tes</a>

        </li>';
    $this->LastRow = $iter;
    return $returnval.'<script>
													new PNotify({
														title: "Votre attention est requise",
														text: Vous avez reçu une notification,
														type: "warning",
														hide: true,
														buttons: {
															sticker: false
														}
													});
												</script>';
  }
	/**
	 *
	 * @param int $id
	 *
	 * @return void
	 *
	 */

	public function getListRequetes()
	{
		if (!($this->sess('email') !== null)) {
			$this->index();
		} else {
			$data['ville'] = $this->sess('ville');
			$data['listrequestes'] = $this->getAllRequetes();
			$data['id'] = $this->input->post_get('id');
			$data['nomcomplet'] = $this->sess('nom');
			$data['menu'] = view('inc_menu_2', $data);
			view('listrequetes', $data);
		}
	}
	public function getAllRequetes()
	{
    $idville = trim($this->input->post_get('idville'));
		if($idville > 0)
    {
      $idville = ' and idville = ' . $this->db->escape($idville);
    }
		else{
      $idville = '';
    }
    $istraitees = trim($this->input->post_get('istraitees', TRUE));
		if($istraitees == 1)
    {
      $istraitees = ' and statut = 7 ';
    }
		else {
      $istraitees = ' and statut != 7 ';
    }
    $dtestart = trim($this->input->post_get('dtestart', TRUE));
		if($dtestart != '')
    {
      $dtestart = ' and dtecreate >= '. $this->db->escape($dtestart);
    }
    $dteend = trim($this->input->post_get('dteend', TRUE));
		if($dteend != '')
    {
      $dteend = $dteend . ' 23:59:00';
      $dteend = ' and dtecreate <= '. $this->db->escape($dteend);
    }
		/** @var string  $returnval */
		$returnval = '';
    /** @var int $trIter */
    $trIter = 1;
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
		/** @var string  $strSQL */
		$strSQL = "SELECT id,CONCAT('R-', id) AS noRequete, idville, nom, adresse, IsFrom,
										CONCAT('upload/', idville,'/',fichier) as url, TRIM(`description`) AS `description`,(SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS matiere,
(SELECT COUNT(id) FROM reponses WHERE IsVue = 0 and IsFrom = 0 AND idrequete = requetes.id) as RepVue,
(SELECT Count(reponses.id) FROM reponses WHERE reponses.idrequete = requetes.id) as NbRep,
										(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statut,
(SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = requetes.idville) as NomVille,
										createby, left(dtecreate, 10) as dtecreate, dteupdate, left(dteCedule, 10) as dteCedule
							FROM requetes where Iscomplete = 0 and isdeleted = 0 $idville $istraitees $dtestart $dteend ORDER BY id";
		$query = $this->db->query($strSQL);

		foreach ($query->getResult() as $row) {
			/** @var string $download */
			$download = ' <a href="' . base_url($row->url) . '" download>
							    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i></a>';
      $vue = ($row->RepVue > 0) ? '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:red"></i>' : '';
      $discussion = ($row->NbRep < 1) ? '' : '<button type="button"
		                      class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHide(\'tr_' . $trIter . '\')" style="outline:none;">
                                  ' . $vue . '<i class="fa fa-chevron-down" style="font-size:24px"></i></button>';
      //--------------------------------------------------------------------
      $description = '<label  style="width: 320px;height:60px">' . $row->description . '</label>';
      $nom = ($row->IsFrom == 0) ? '<span style="font-weight:bold;font-size:18px;color:red">'. $row->NomVille . '</span>': '<img src="/assets/img/logo.png" width="145" alt="Robert Daoust" />';
			$dteCedule = (trim($row->dteCedule) != '')? '<span style="color:red">Cédulée le ' . $row->dteCedule . '</span>  ' : '';
      //if (strpos($row->description, '<img')) {
      //  $description = ' <a href="#"
      //            onclick="showModalDescription(\'' . $row->id . '\')">Voir la description</a> ';
      //}
      //--------------------------------------------------------------------
			$returnval .= '<tr id="trreq_'.$row->id.'" style="line-height:15px;">
												<td style="width:40px;" >'. $discussion.'
</td>
												    <td style="width:150px;" ><label style="width: 210px;font-wight:bold;">' . $nom . '</label>
</td>
													    <td width="160"><a href="/villes/show/requete/' . $row->id . '?from=1"
                               style="width: 220px;">' . $row->noRequete . '</a><br />
</td>
		                        <td width="180"><label style="width: 180px;font-wight:bold;">'. $dteCedule . 'Créée le ' . $row->dtecreate . '</label>
</td>
                                <td><label style="width: 100px;">' . $row->nom . '</label>
</td>
											<td width="350"><label onclick="ShowInGoogle(\'' . $this->Slug($row->adresse) . '\')" style="width: 350px;color:red;cursor:pointer">' . $row->adresse . '</label>
											</td>

		                        <td width="200">
															<label class="form-control" style="width:200px">' .$row->matiere  . '
															</label>
</td>
                                <td width="300">'.$description.'
</td>
		                        <td width="200">
															<label class="form-control" style="width:200px;background-color:'.$row->statCouleur.'">' .$row->statut  . '
															</label>
</td>
											</tr>'. $this->getReponseInReq($row->id, $trIter);
      $returnval .= " <script> $('#tr_" . $trIter . "').hide();  </script>";
			$trIter++;
		}
		return $returnval;
	}
	private function getAllDate($idrequete,$idreponse)
  {
    $strSQL = "SELECT left(dteCedule, 10) as dteCedule, idrequete
									FROM daterequete WHERE idrequete =$idrequete and  idreponse = $idreponse order by dteCedule DESC";
    $result = $this->db->query($strSQL);
    $returnval = '';
		$iter = 1;
    if ($result->getNumRows() > 0) {
      foreach ($result->getResult() as $row) {
        $returnval .= ($iter == 1) ? '<span style="color:red">Cédulée le ' . $row->dteCedule . '</span>' : 'Anci. date '. $row->dteCedule;
        $returnval .= '   ';
        $iter++;
      }
    }
		return $returnval;
  }
  /**
   * @param int $idrequete
   *
   * @return string
   */
	public function getDescriptionRequete($idrequete)
  {
    /** @var string  $returnval */
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
		/** @var string  $strSQL */
		$strSQL = "SELECT id,CONCAT('R-', id) AS noRequete, idville, nom, adresse, IsFrom, `description`,(SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS matiere,
				(SELECT COUNT(id) FROM reponses WHERE IsVue = 0 and IsFrom = 0 AND idrequete = requetes.id) as RepVue,
				(SELECT Count(reponses.id) FROM reponses WHERE reponses.idrequete = requetes.id) as NbRep,
														(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statut,
				(SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
				Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = requetes.idville) as NomVille,
														createby, left(dtecreate, 10) as dtecreate, dteupdate, left(dteCedule, 10) as dteCedule
							FROM requetes where id = $idrequete";
    $row = $this->db->query($strSQL)->getRow(0);
    $returnval = '<div class="input-group" style="width:100%;font-size:36px;font-weight:bold;color:darkblue;">
										<span class="form-control" style="width:200px;font-size:28px;font-weight:bold;color:darkblue;">' . $row->NomVille . '</span>
										<span class="form-control" style="width:100px;font-weight:bold;">REQUÊTE</span>
										<span class="form-control" style="font-size:20px;font-weight:bold;color:darkblue;">R-' . $row->id . '</span>
										<span class="form-control" style="width:80px;font-weight:bold;">STATUT </span>
										<span class="form-control" style="width:130px;text-align:center;background-color:
										' . $row->statCouleur . ';font-size:20px;font-weight:bold;color:darkblue;">' . $row->statut . ' </span>
										<span class="form-control" style="width:300px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->description . '</span>
										<span class="form-control" style="width:100px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->matiere . '</span>
										<span class="form-control" style="width:300px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->dtecreate . '</span>
										<span class="form-control" style="width:100px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->dteCedule . '</span>
								</div>';
		return $returnval;
  }
  /**
   * @param int $noRequete
   *
   * @return string
   */
  private function getReponseInReq($noRequete, $tr_iter)
  {
    /** @var string  $returnval */
    $returnval = '';
    /** @var int $trIter */
    $trIter = 1;
    /** @var int $iter */
    $iter = 1;
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
    /** @var string  $strSQL */
    $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(dteCedule, 10) as dteCedule,
							(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
(SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
										createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue, statut,
noSerie, couleur, grandeur, matiere
							FROM reponses WHERE idrequete = $noRequete
											AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
    $reponse = $this->db->query($strSQL);
    //$titreNB = $reponse->getNumRows() . ' commandes pour la requête ' . $noRequete;
    $returnval = '<tr id="tr_' . $tr_iter . '"  style="display:none;padding:15px;" > <td colspan="9" >
										<table class="table table-bordered"  >
											<thead>';
    $infoStatAdr = '';
    $returnval .= '
                      <tr  style="text-align:right;color:darkblue;font-weight:bold;background-color:lightgray">
                      <th align="center"></th>
                      <th align="center">REQUÉRANT</th>
                      <th>DATE CÉDULÉE</th>
                      <th>FICHIER</th>
                      <th>DESCRIPTION</th>
                      <th>ADRESSE / BAC / MATIÈRE</th>
                      <th>STATUT</th>
                      <th>DATE</th>
		                  </tr></thead><tbody id="tb_' . $noRequete . '" >';
    foreach ($reponse->getResult() as $row) {
      /** @var string $btnDTE */
      $btnDTE = ' <a href="#" >
							    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i></a>';
      $nom = ($row->IsFrom == 0) ? $row->NomVille : '<span style="color:red;font-size:24px;width:100%;">RDF</span>';
      $background = (($row->IsVue == 0) and ($row->IsFrom == 0)) ? 'style="background-color: lightpink"' : '';

			if($row->statut == 9)
      {
        $infoStatAdr = '# Série: ' . $row->noSerie . '<br />Grandeur: ' . $row->grandeur .
          '<br />Couleur: ' . $row->couleur;
      }
			else if($row->statut == 8)
      {
        $infoStatAdr = $row->adresse;
      }
			else if($row->statut == 10)
      {
        $infoStatAdr = $row->matiere;
      }
      $returnval .= '<tr id="tr_' . $trIter . '" ' . $background . '>
											<td width="20" style="font-weight:bold;text-align:center" >
												' . $iter . '
											</td>
											<td align="center" style="width:150px;text-align:center;margin-left:20px;"><label style="width: 100%;font-wight:bold;text-align:center;">' . $nom . '</label>
											</td>
											<td style="width:185px">
												<label  style="width: 200px;">' . $this->getAllDate($noRequete, $row->id) . '</label>
											</td>
											<td width="120" >
												' . $this->getResponseFile($row->id) . '
											</td>
											<td width="350"><label  style="width: 350px;height:60px">' . $row->description . '</label>
											</td>
		                         <td width="200"><label  style="width: 220px;">' . $infoStatAdr . '</label>
											</td>

											<td width="160"><label  style="width: 160px;height:60px;background-color:' . $row->statCouleur . '">
												' . $row->NonStatut . '</label>
											</td>
											<td style="width:90px;margin-right:20px;">
												<label  style="width: 90px;">' . $row->dtecreate . '</label>
											</td>
										</tr>
											';
      $iter++;
    }
    return $returnval . ' </tbody></table> </td></tr>';
  }
	public function getReponseForReq($noRequete)
  {
		/** @var string  $returnval */
    $returnval = ''; //'<tr><td align="center" colspan="7" style="text-align:center">' . $this->getDescriptionRequete($noRequete).'</td></tr>';
    /** @var int $trIter */
    $trIter = 1;
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
		/** @var string  $strSQL */
    $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(dteCedule, 10) as dteCedule,
							(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
(SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
										createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue
							FROM reponses WHERE idrequete = $noRequete
											AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
    $reponse = $this->db->query($strSQL);
    $data['titreNB'] = $reponse->getNumRows() . ' Réponse(s) et Ajout(s) pour la requête ' . $noRequete;

    foreach ($reponse->getResult() as $row)
    {
      /** @var string $btnDTE */
      $btnDTE = ' <a href="#" >
							    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i></a>';
      $nom = ($row->IsFrom == 0) ? $row->NomVille : '<span style="color:red;font-size:24px;width:100%;">RDF</span>';
			$background = (($row->IsVue == 0) and ($row->IsFrom == 0) ) ? 'style="background-color: lightpink"' : '';
      $returnval .= '<tr id="tr_'.$trIter.'" '.$background.'>

											<td align="center" style="width:150px;text-align:center;"><label style="width: 100%;font-wight:bold;text-align:center;">' . $nom . '</label>
											</td>
											<td style="width:185px">
												<label  style="width: 180px;">'.$this->getAllDate($noRequete,$row->id) .'</label><br />
<input type="date" onchange="changeDate(this.value, '. $noRequete.','.$row->idville.','.$row->id.')" class="form-control" id="dteCedule_'.$noRequete.'" value="" />
											</td>
											<td width="120" >
												'.$this->getResponseFile($row->id).'
											</td>
											<td width="350"><label  style="width: 350px;height:60px">' . $row->description . '</label>
											</td>
		                         <td width="200"><label  style="width: 220px;">' . $row->adresse . '</label>
											</td>

											<td width="160"><label  style="width: 160px;height:60px;background-color:' . $row->statCouleur . '">
												' . $row->NonStatut . '</label>
											</td>
											<td style="width:90px">
												<label  style="width: 90px;">'.$row->dtecreate.'</label>
											</td>
										</tr>
											';
    }
		$data['listreceptions'] = $returnval;
    $data['retour'] = '<a  href="/villes/liste/requetes?vue='. $noRequete.'">Retour</a>';
    return $data;
  }
	private function getResponseFile($idreponse)
  {
		/** @var string  $returnval */
		$returnval = '';
    $btndrop = '<div class="input-group mb-3">
														<div class="btn-group">
															<button type="button" class="btn btn-primary">Fichier(s)</button>
															<div class="btn-group" role="group">
																<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	<span class="sr-only">Toggle Dropdown</span>
																</button>
																<div class="dropdown-menu">';
		/** @var string  $strSQL */
		$strSQL = "SELECT id, fichier FROM reponse_files WHERE idreponse = $idreponse";
		$query = $this->db->query($strSQL);
		$iter = 1;
		foreach ($query->getResult() as $row) {
      //$arrFile = explode("/", $row->fichier);
      //$place = count($arrFile) - 1;
      //$fichier = $arrFile($place);
			$returnval .= '<option value="' . $row->id . '"><a class="dropdown-item" href="'.base_url($row->fichier).'"  download >
							    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>fichier '.$iter.'</a></option>';
      $iter++;
		}
		if ($returnval != '')
    {
			return $btndrop.$returnval.'</div>
															</div>
														</div>
													</div>';
    }
		else
    {
      return '';
    }
  }
	public function getOptStatut($statut)
	{
		/** @var string  $returnval */
		$returnval = '';
    /** @var string  $strDesc */

		/** @var string  $strSQL */
		$strSQL = "SELECT id, description_fr, description_en  FROM statutrequete ORDER BY description_fr";
		$query = $this->db->query($strSQL);

		foreach ($query->getResult() as $row) {
      $selected = ($statut == $row->id) ? ' selected ' : '';
			$strDesc = ($this->sess('site_lang') == 'fr') ? $row->description_fr : $row->description_en;
      $returnval .= '<option '.$selected.' value="' . $row->id . '">' . $strDesc . '</option>';
		}
		return $returnval;
	}
	public function getOptDechets($id = 0)
	{
		/** @var string  $returnval */
		$returnval = '';
		/** @var string  $strSQL */
		$strSQL = "SELECT id, Nom  FROM typededechet ORDER BY Nom";
		$query = $this->db->query($strSQL);

		foreach ($query->getResult() as $row) {
			$selected = ($row->id == $id) ? "selected" : '';
			$returnval .= '<option '. $selected.'  value="' . $row->id . '">' . $row->Nom . '</option>';
		}
		return $returnval;
	}
	private function findVille($id)
  {
    $query = $this->db->query("SELECT Nom FROM ville WHERE id = $id")->getRow(0);
		return $query->Nom;
  }
	//----------------------------------------
	/**
	 *
	 *
	 *
	 * @return array
	 *
	 */
	public function postRequete()
	{
    /** @var string $filename1 */
    $filename1 = '';
    /** @var string $filename2 */
    $filename2 = '';
    /** @var string $filename3 */
    $filename3 = '';
		/** @var array $data */
		$data = array();
		/** @var string $returnval */
		$returnval = '';
		/** @var string $strSQL */
		/** @var array $dataResult */

		$data = $this->input->post(NULL, TRUE);
    $adr = $data['adresse'];
		$nomVille = $this->findVille($data['idville']);
		if (strpos(strtolower($adr),strtolower($nomVille)) == FALSE)
    {
			$data['adresse'] .= ', ' . $nomVille;
    }
		switch($data['id'])
		{
			case 0:
				//--------------------------------------------------------- Post New Requete
				unset($data['id']);
				$data['createby'] = $this->sess('user_id');
				//$data['idville'] = $this->sess('idville');

				$this->db->table('requetes')->insert($data);
				//----------------------------------------
				$error = $this->db->error();
				if (!empty($error['code'])) {
					$dataResult['msgerr'] = "Une erreur s'est produite.";
					$dataResult['id'] = 0;
				} else {
					$dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
				}
				$dataResult['id'] = $this->db->insertID();
				break;
				//---------------------------------------------------------
			default:
				$id = $data['id'];
				unset($data['id']);
				$this->db->table('requetes')->where('id', $id)->update($data);
				$error = $this->db->error();
				if (!empty($error['code'])) {
					$dataResult['msgerr'] = "Une erreur s'est produite.";
					$dataResult['id'] = 0;
				} else {
					$dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
				}
				$dataResult['id'] = $id;
				break;
		}

		$config = array();
		$config['upload_path'] = './upload/'. $data['idville'].'/'. $dataResult['id'];
		if (!file_exists($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, true);
		}
		$config['max_size'] = '0';
		$config['max_width'] = '0';
		$config['max_height'] = '0';
		$config['allowed_types'] = '*';
    $config['file_ext_tolower'] = true;
    $config['remove_spaces'] = true;
		//$config['encrypt_name'] = TRUE;
		$this->upload = new \App\Libraries\Ci3Upload($config);
		if (!$this->upload->do_upload('fileRequete_1')) {
			$error = array('error' => $this->upload->display_errors());
			//Action, in case file upload failed
		} else {
			//Action, after file successfully uploaded
			$filename1 = $this->db->escape('/upload/' . $data['idville'] . '/' . $dataResult['id'] . '/' .$this->upload->file_name);
		}

		if (trim($filename1) != '')
    {
			$idrequete = $dataResult['id'];
      $createby = $this->sess('user_id');
      $strSQL = "INSERT INTO requetes_file
											(idrequete, createby, fichier)
											VALUES ($idrequete, $createby, $filename1)";
      $this->db->query($strSQL);
    }
		if (!$this->upload->do_upload('fileRequete_2')) {
			$error = array('error' => $this->upload->display_errors());
			//Action, in case file upload failed
		} else {
			//Action, after file successfully uploaded
			$filename2 = $this->db->escape('/upload/' . $data['idville'] . '/' . $dataResult['id'] . '/' . $this->upload->file_name);
		}

    if (trim($filename2) != '') {
      $idrequete = $dataResult['id'];
      $createby = $this->sess('user_id');
      $strSQL = "INSERT INTO requetes_file
											(idrequete, createby, fichier)
											VALUES ($idrequete, $createby, $filename2)";
      $this->db->query($strSQL);
    }
		if (!$this->upload->do_upload('fileRequete_3')) {
			$error = array('error' => $this->upload->display_errors());
			//Action, in case file upload failed
		} else {
			//Action, after file successfully uploaded
			$filename3 = $this->db->escape('/upload/' . $data['idville'] . '/' . $dataResult['id'] . '/' . $this->upload->file_name);
		}

    if (trim($filename3) != '') {
      $idrequete = $dataResult['id'];
      $createby = $this->sess('user_id');
      $strSQL = "INSERT INTO requetes_file
											(idrequete, createby, fichier)
											VALUES ($idrequete, $createby, $filename3)";
      $this->db->query($strSQL);
    }
		//---------------------------------------------------------

    $this->message_error = $dataResult['msgerr'];
		return $dataResult;
	}
	/**
	 *
	 *
	 *
	 * @return array
	 *
	 */
	public function postReponse()
	{
    /** @var string $filename1 */
    $filename1 = '';
    /** @var string $filename2 */
    $filename2 = '';
    /** @var string $filename3 */
    $filename3 = '';
		/** @var array $data */
		$data = array();
		/** @var string $returnval */
		$returnval = '';
		/** @var string $strSQL */
		/** @var array $dataResult */
		if (!$this->sess('email')) {
			//$this->logout();
		} else {
			$data = $this->input->post(NULL, TRUE);
      $adr = $data['adresse'];
			if (trim($adr) != '')
      {
				$nomVille = $this->findVille($data['idville']);
				if (strpos(strtolower($adr), strtolower($nomVille)) == FALSE) {
					$data['adresse'] .= ', ' . $nomVille;
				}
      }
			$idville = $data['idville'];
			$idrequete = $data['idrequete'];
      $data['createby'] = $this->sess('user_id');
      $this->db->table('reponses')->insert($data);
      $dataResult['id'] = $this->db->insertID();
			$idreponse = $dataResult['id'];
			$error = $this->db->error();
			if (!empty($error['code'])) {
				$dataResult['msgerr'] = "Une erreur s'est produite.";

			} else {
				$dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        $statut = $data['statut'];
				$addr = '';
				if (trim($data['adresse']) != '')
        {
          $addr = ',adresse = ' . $this->db->escape($data['adresse']);
          $adresse = $this->db->escape($data['adresse']);
          $strSQL = "INSERT INTO adrrequete
	                (idrequete, idreponse, adresse, idville, old_adresse)
	                VALUES ($idrequete, $idreponse, $adresse, $idville,
                          (SELECT adresse FROM requetes WHERE id = $idrequete))";
          $this->db->query($strSQL);
        }
				$dte = '';
				if (trim($data['dteCedule']) != '')
        {
          $dte = ',dteCedule = ' . $this->db->escape($data['dteCedule']);
          $dteCedule = $this->db->escape($data['dteCedule']);
          $strSQL = "INSERT INTO daterequete
	                (dteCedule, idrequete,idreponse, idville)
	                VALUES ($dteCedule, $idrequete, $idreponse, $idville)";
          $this->db->query($strSQL);
        }
        $strSQL = "UPDATE requetes
										SET
											statut=$statut
											$addr
											$dte
										WHERE id = $idrequete";
        $this->db->query($strSQL);
				//---------------------------------

			}
			$dataResult['idrequete'] = $idrequete;
      $config = array();
      $config['upload_path'] = './upload/' . $data['idville'].'/'. $idrequete.'/reponse/'. $idreponse;
      if (!file_exists($config['upload_path'])) {
        mkdir($config['upload_path'], 0777, true);
      }
      $config['max_size'] = '0';
      $config['max_width'] = '0';
      $config['max_height'] = '0';
      $config['allowed_types'] = '*';
      $config['file_ext_tolower'] = true;
      $config['remove_spaces'] = true;
      //$config['encrypt_name'] = TRUE;
      $this->upload = new \App\Libraries\Ci3Upload($config);
      if (!$this->upload->do_upload('fileRequete_1')) {
        $error = array('error' => $this->upload->display_errors());
        //Action, in case file upload failed
      } else {
        //Action, after file successfully uploaded
				$filename1 = $this->db->escape('/upload/' . $data['idville'].'/'. $idrequete.'/reponse/'. $idreponse.'/'.$this->upload->file_name);
      }
      if (trim($filename1) != '') {
        $idrequete = $dataResult['id'];
        $createby = $this->sess('user_id');
        $strSQL = "INSERT INTO reponse_files
												(idreponse, createby, fichier)
												VALUES ($idreponse, $createby, $filename1)";
        $this->db->query($strSQL);
      }
      if (!$this->upload->do_upload('fileRequete_2')) {
        $error = array('error' => $this->upload->display_errors());
        //Action, in case file upload failed
      } else {
        //Action, after file successfully uploaded
				$filename2 = $this->db->escape('/upload/' . $data['idville'] . '/' . $idrequete . '/reponse/' . $idreponse . '/' .$this->upload->file_name);
      }

      if (trim($filename2) != '') {
        $idrequete = $dataResult['id'];
        $createby = $this->sess('user_id');
        $strSQL = "INSERT INTO reponse_files
												(idreponse, createby, fichier)
												VALUES ($idreponse, $createby, $filename2)";
        $this->db->query($strSQL);
      }

      if (!$this->upload->do_upload('fileRequete_3')) {
        $error = array('error' => $this->upload->display_errors());
        //Action, in case file upload failed
      } else {
        //Action, after file successfully uploaded
				$filename3 = $this->db->escape('/upload/' . $data['idville'] . '/' . $idrequete . '/reponse/' . $idreponse . '/' .$this->upload->file_name);
      }

      if (trim($filename3) != '') {
        $idrequete = $dataResult['id'];
        $createby = $this->sess('user_id');
        $strSQL = "INSERT INTO reponse_files
												(idreponse, createby, fichier)
												VALUES ($idreponse, $createby, $filename3)";
        $this->db->query($strSQL);
      }
		}
    $this->message_error = $dataResult['msgerr'];
    return $dataResult;
	}

	private function symlink($file)
  {
    $link = 'https://villes.robertdaoustetfils.com';
    symlink($file, $link);

    readlink($link);
  }
  /**
   *
   * @param int $id
   *
   * @return array
   *
   */
  public function getRequete($id)
  {
    $idville = '';
		$data = array();
    if (!($this->sess('email') !== null)) {
      //$this->index();
    } else {
      $this->cedules = new CedulesModel();
      /** @var string  $strDesc */
      $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
      if ($id != 0) {
        $id = $this->db->escape($id);
        /** @var string $strSQL */
        $strSQL = "SELECT id, idville, nom, adresse, description, matiere, fichier,
											(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statutnom,
											(SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
											(select ville.Nom from ville where ville.id = requetes.idville) as villenom,
											CONCAT('upload/', idville,'/',fichier) as url,
											statut, dtecreate, Iscomplete FROM requetes WHERE id = $id";
        $requete = $this->db->query($strSQL);
        foreach ($requete->getResultArray() as $row) {
          $data = $row;
          /** @var string $download */
          $download = ' <a href="' . base_url($row['url']) . '" download>
							    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;Cliquer sur ce lien pour télécharger le fichier</a>';
          $data['noreq'] = '<div class="input-group" style="width:550px;">
															<span class="form-control">REQUÊTE</span>
															<span class="form-control">R-' . $row['id'] . '</span>
															<span class="form-control" style="width:80px;">STATUT </span>
															<span class="form-control" style="width:130px;text-align:center;background-color:
															' . $row['statCouleur'] . '">' . $row['statutnom'] . ' </span>
</div>';
          $data['inforequete'] = "
                       <b> Créée le:&nbsp;&nbsp; <span style='color:red'>" . $row['dtecreate'] . " </span><br/><br/> </b>
																						<b> Description de la requête: </b> <br/>
                                              <div style='border:double;padding:10px;'>" . $row['description'] . "</div> <br/>
                                             <b> Adresse: </b> <br/>
											<label class=\"form-control\" style='cursor:pointer'
onclick=\"ShowInGoogle('" . $this->Slug($row['adresse']) . "')\" > ". $row['adresse'] . "</label> <br/>
																						 <b> Matière: </b> <br/>";
          $idville = $data['idville'];
          $data['optMatiere'] = $this->cedules->GetAllMatieres($row['matiere']);
          $data['optStatut'] = $this->getOptStatut(0);
          $data['optVilles'] = $this->cedules->GetOptionVilles($idville);
          $data['fichiers'] = $this->GetFichiers($row['id'],0);
          $data['fileinclude'] = ((trim($row['fichier']) == '')? 'none' : 'block');
          $data['download'] = $download;
          $data['nomcomplet'] = $this->sess('nom');
        }
      } else {
        $data['noreq'] = '';
        $data['id'] = 0;
        $data['msgerror'] = '';
        $data['idville'] = '';
        $data['nom'] = '';
        $data['adresse'] = '';
        $data['fichier'] = '';
        $data['description'] = '';
        $data['matiere'] = '';
        $data['statut'] = '';
        $data['optMatiere'] = $this->cedules->GetAllMatieres(0);
        $data['optVilles'] = $this->cedules->GetOptionVilles(0);
        $data['nomcomplet'] = $this->sess('nom');
      }
    }
    return $data;
  }
	private function GetFichiers($id, $from)
  {
    /** @var string $returnval */
    $returnval = '';
		$iter = 1;
    /** @var string $strSQL */
    $strSQL = "SELECT id, idrequete, fichier, CONCAT((SELECT concat('upload/', idville,'/') FROM requetes WHERE requetes.id = requetes_file.idrequete),fichier) as url,
											(SELECT idville FROM requetes WHERE requetes.id = requetes_file.idrequete) as idville
							FROM requetes_file WHERE idrequete = $id";
    $result = $this->db->query($strSQL);
		foreach($result->getResultArray() as $row)
    {
      /** @var string $download */
      $download = ' <a href="' . base_url($row['fichier']) . '" download>
							    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;Fichier '. $iter.'</a><br />';
			$returnval .= $download;
      $iter++;
    }
		if((trim($returnval) != '') and ($from != 1))
    {
      $returnval = '<div class="col-md-6">
        <section class="card card-featured card-featured-primary mb-4">
          <header class="card-header">
            <div class="card-actions">
              <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
              <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
            </div>

            <h2 class="card-title">
              Fichier(s) disponible(s) pour cette requête
            </h2>
          </header>
          <div class="card-body">
            '.$returnval.'
          </div>


        </section>
      </div>';
    }
		return $returnval;
  }
	private function Slug($str)
	{
		$rep =strtolower(
			preg_replace(
				array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'),
				array('', '-', ''),
				$this->remove_accent($str)
			)
		);
		$rep = str_replace("--", "-", $rep);
		$rep = str_replace("--", "-", $rep);
		$rep = str_replace("--", "-", $rep);
		return ($rep);

	}
	/**
	 *
	 *
	 *
	 *
	 * void
	 */
	public function SlugUrl()
	{
		$str = $this->input->get_post('filename');
		$str = urldecode($str);
		$rep =strtolower(
			preg_replace(
				array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'),
				array('', '-', ''),
				$this->remove_accent($str)
			)
		);
		$rep = str_replace("--", "-", $rep);
		$rep = str_replace("--", "-", $rep);
		$rep = str_replace("--", "-", $rep);
		echo $rep;

	}
	private function remove_accent($str)
	{
		$a = array(
			'À',
			'Á',
			'Â',
			'Ã',
			'Ä',
			'Å',
			'Æ',
			'Ç',
			'È',
			'É',
			'Ê',
			'Ë',
			'Ì',
			'Í',
			'Î',
			'Ï',
			'Ð',
			'Ñ',
			'Ò',
			'Ó',
			'Ô',
			'Õ',
			'Ö',
			'Ø',
			'Ù',
			'Ú',
			'Û',
			'Ü',
			'Ý',
			'ß',
			'à',
			'á',
			'â',
			'ã',
			'ä',
			'å',
			'æ',
			'ç',
			'è',
			'é',
			'ê',
			'ë',
			'ì',
			'í',
			'î',
			'ï',
			'ñ',
			'ò',
			'ó',
			'ô',
			'õ',
			'ö',
			'ø',
			'ù',
			'ú',
			'û',
			'ü',
			'ý',
			'ÿ',
			'Ā',
			'ā',
			'Ă',
			'ă',
			'Ą',
			'ą',
			'Ć',
			'ć',
			'Ĉ',
			'ĉ',
			'Ċ',
			'ċ',
			'Č',
			'č',
			'Ď',
			'ď',
			'Đ',
			'đ',
			'Ē',
			'ē',
			'Ĕ',
			'ĕ',
			'Ė',
			'ė',
			'Ę',
			'ę',
			'Ě',
			'ě',
			'Ĝ',
			'ĝ',
			'Ğ',
			'ğ',
			'Ġ',
			'ġ',
			'Ģ',
			'ģ',
			'Ĥ',
			'ĥ',
			'Ħ',
			'ħ',
			'Ĩ',
			'ĩ',
			'Ī',
			'ī',
			'Ĭ',
			'ĭ',
			'Į',
			'į',
			'İ',
			'ı',
			'Ĳ',
			'ĳ',
			'Ĵ',
			'ĵ',
			'Ķ',
			'ķ',
			'Ĺ',
			'ĺ',
			'Ļ',
			'ļ',
			'Ľ',
			'ľ',
			'Ŀ',
			'ŀ',
			'Ł',
			'ł',
			'Ń',
			'ń',
			'Ņ',
			'ņ',
			'Ň',
			'ň',
			'ŉ',
			'Ō',
			'ō',
			'Ŏ',
			'ŏ',
			'Ő',
			'ő',
			'Œ',
			'œ',
			'Ŕ',
			'ŕ',
			'Ŗ',
			'ŗ',
			'Ř',
			'ř',
			'Ś',
			'ś',
			'Ŝ',
			'ŝ',
			'Ş',
			'ş',
			'Š',
			'š',
			'Ţ',
			'ţ',
			'Ť',
			'ť',
			'Ŧ',
			'ŧ',
			'Ũ',
			'ũ',
			'Ū',
			'ū',
			'Ŭ',
			'ŭ',
			'Ů',
			'ů',
			'Ű',
			'ű',
			'Ų',
			'ų',
			'Ŵ',
			'ŵ',
			'Ŷ',
			'ŷ',
			'Ÿ',
			'Ź',
			'ź',
			'Ż',
			'ż',
			'Ž',
			'ž',
			'ſ',
			'ƒ',
			'Ơ',
			'ơ',
			'Ư',
			'ư',
			'Ǎ',
			'ǎ',
			'Ǐ',
			'ǐ',
			'Ǒ',
			'ǒ',
			'Ǔ',
			'ǔ',
			'Ǖ',
			'ǖ',
			'Ǘ',
			'ǘ',
			'Ǚ',
			'ǚ',
			'Ǜ',
			'ǜ',
			'Ǻ',
			'ǻ',
			'Ǽ',
			'ǽ',
			'Ǿ',
			'ǿ'
		);

		$b = array(
			'A',
			'A',
			'A',
			'A',
			'A',
			'A',
			'AE',
			'C',
			'E',
			'E',
			'E',
			'E',
			'I',
			'I',
			'I',
			'I',
			'D',
			'N',
			'O',
			'O',
			'O',
			'O',
			'O',
			'O',
			'U',
			'U',
			'U',
			'U',
			'Y',
			's',
			'a',
			'a',
			'a',
			'a',
			'a',
			'a',
			'ae',
			'c',
			'e',
			'e',
			'e',
			'e',
			'i',
			'i',
			'i',
			'i',
			'n',
			'o',
			'o',
			'o',
			'o',
			'o',
			'o',
			'u',
			'u',
			'u',
			'u',
			'y',
			'y',
			'A',
			'a',
			'A',
			'a',
			'A',
			'a',
			'C',
			'c',
			'C',
			'c',
			'C',
			'c',
			'C',
			'c',
			'D',
			'd',
			'D',
			'd',
			'E',
			'e',
			'E',
			'e',
			'E',
			'e',
			'E',
			'e',
			'E',
			'e',
			'G',
			'g',
			'G',
			'g',
			'G',
			'g',
			'G',
			'g',
			'H',
			'h',
			'H',
			'h',
			'I',
			'i',
			'I',
			'i',
			'I',
			'i',
			'I',
			'i',
			'I',
			'i',
			'IJ',
			'ij',
			'J',
			'j',
			'K',
			'k',
			'L',
			'l',
			'L',
			'l',
			'L',
			'l',
			'L',
			'l',
			'L',
			'l',
			'N',
			'n',
			'N',
			'n',
			'N',
			'n',
			'n',
			'O',
			'o',
			'O',
			'o',
			'O',
			'o',
			'OE',
			'oe',
			'R',
			'r',
			'R',
			'r',
			'R',
			'r',
			'S',
			's',
			'S',
			's',
			'S',
			's',
			'S',
			's',
			'T',
			't',
			'T',
			't',
			'T',
			't',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'W',
			'w',
			'Y',
			'y',
			'Y',
			'Z',
			'z',
			'Z',
			'z',
			'Z',
			'z',
			's',
			'f',
			'O',
			'o',
			'U',
			'u',
			'A',
			'a',
			'I',
			'i',
			'O',
			'o',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'A',
			'a',
			'AE',
			'ae',
			'O',
			'o'
		);
		return str_replace($a, $b, $str);
	}
	//-------------------------------------------------------------------------
	function __destruct() {
		$this->db->close();
	}
}
