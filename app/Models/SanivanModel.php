<?php

namespace App\Models;

/**
 * Port mecanique de Sanivan_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */


//--------------------------------------------------------------
class Collection
{
  protected $items = array();

  public function addItem($obj, $key = null)
  {
    if ($key === null) {
      $this->items[] = $obj;
    } else {
      if (isset($this->items[$key])) {
        //throw new KeyHasUseException("Key $key already in use.");
      } else {
        $this->items[$key] = $obj;
      }
    }
  }

  public function deleteItem($key)
  {
    if (isset($this->items[$key])) {
      unset($this->items[$key]);
    }
  }

  public function getItem($key)
  {
    if (isset($this->items[$key])) {
      return $this->items[$key];
    } else {
      return null;
    }
  }

  public function keys()
  {
    return array_keys($this->items);
  }

  public function length()
  {
    return count($this->items);
  }

  public function keyExists($key)
  {
    return isset($this->items[$key]);
  }
  public function postItem($key, $date)
  {
    $this->items[$key]->Post($key, $date);
  }
  public function postAllItem($date)
  {
    foreach ($this->items as $key => $value) {
      $this->items[$key]->Post($key, $date);
    }
  }
  public function getDayReport($date)
  {
    $returnval = '';
    //-----------------------
    $hresCam = 0;
    $carburant = 0;
    $coutCarburant = 0;
    $hresChauf = 0;
    $coutChauf = 0;
    $hresAide = 0;
    $coutAide = 0;
    $transport = 0;
    $Collecte = 0;
    $coutDispot = 0;
    $disposition = 0;
    $nbBac = 0;
    $coutBroker = 0;
    $revenu = 0;
    $net = 0;
    $netByHres = 0;
    //-----------------------
    foreach ($this->items as $key => $value) {
      $hresChauf += $this->items[$key]->getHresChauf();
      $coutChauf += $this->items[$key]->getCoutChauf();
      $hresAide += $this->items[$key]->getHresAide();
      $coutAide += $this->items[$key]->getCoutAide();
      $hresCam += $this->items[$key]->getHresCam();
      $carburant += $this->items[$key]->getCarburant();
      $coutCarburant += $this->items[$key]->getCoutCarburant();
      $transport += $this->items[$key]->getTransport();
      $Collecte += $this->items[$key]->getCollecte();  
      $disposition += $this->items[$key]->getDisposition();
      $coutDispot += $this->items[$key]->getCoutDispot();
      $nbBac += $this->items[$key]->getnbBac();
      $coutBroker += $this->items[$key]->getCoutBroker();
      $revenu += $this->items[$key]->getRevenu();
      $net += $this->items[$key]->getNet();
      $netByHres += $this->items[$key]->getNetByHres();
    }
    return '<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $hresChauf . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;color:red;">' . $coutChauf . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $hresAide . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;color:red;">' . $coutAide . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $hresCam . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $carburant . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;color:red;">' . $coutCarburant . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $transport . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $Collecte . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $disposition . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;color:red;">' . $coutDispot . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px">' . $nbBac . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;color:red;background-color:#FFF3E0;">' . number_format((float)$coutBroker, 2, '.', '') . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;background-color:yellow;">' . number_format((float)$revenu, 2, '.', '') . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;background-color:yellow;">' . number_format((float)$net, 2, '.', '') . '</label></td>
          <td align="center"><label class="form-control form-control-modern" style="width:100px;background-color:yellow;">' . number_format((float)$netByHres, 2, '.', '') . '</label></td> ';
  }
}
//----------------------------------------------------------
class CamDay
{
  //- properties
  protected $CI;
  /** @var float $hresCam */
  protected $hresCam = 0;
  /** @var string $carburant */
  protected $carburant = 0;
  /** @var string $coutCarburant */
  protected $coutCarburant = 0;
  /** @var string $hresChauf */
  protected $hresChauf = 0;
  /** @var float $coutChauf */
  protected $coutChauf = 0;
  /** @var string $hresAide */
  protected $hresAide = 0;
  /** @var float $coutAide */
  protected $coutAide = 0;
  /** @var float $transport */
  protected $transport = 0;
  /** @var float $Collecte */
  protected $Collecte = 0;
  /** @var float $disposition */
  protected $coutDispot = 0;
  /** @var float $coutDispot */
  protected $disposition = 0;
  /** @var int $nbBac */
  protected $nbBac = 0;
  /** @var int $revenu */
  protected $revenu = 0;
  /** @var int $net */
  protected $net = 0;
  /** @var int $netByHres */
  protected $netByHres = 0;
  /** @var float $coutBroker */
  protected $coutBroker = 0;
  protected $id = 0;
  //------------------------------------
  function __construct($transport, $Collecte, $hrescam, $camion, $date)
  {
    $this->CI = new \stdClass();
    $this->CI->db = new \App\Libraries\Ci3Db();
    $this->transport = $transport;
    $this->Collecte = $Collecte;
    $this->hresCam = $hrescam;
    $this->Post($camion, $date);
  }
  //--------------------------------------------------fonction Base de donnees
  public function Post($camion, $date)
  {
    $camion = $this->CI->db->escape($camion);
    $date = $this->CI->db->escape($date);
    /** @var string $strSQL */
    $strSQL = "SELECT id FROM sanivanday WHERE DateJour = $date AND NOCAMION = $camion";
    $req = $this->CI->db->query($strSQL);
    $rowexiste = $req->getNumRows();
    switch ($rowexiste) {
      case 0:
        $this->insert($camion, $date);
        break;
      default:
        $this->update($camion, $date);
    }
  }
  private function insert($camion, $date)
  {
    $hresCam = $this->CI->db->escape($this->hresCam);
    $coutCarburant = $this->CI->db->escape($this->coutCarburant);
    $carburant = $this->CI->db->escape($this->carburant);
    $hresChauf = $this->CI->db->escape($this->hresChauf);
    $coutChauf = $this->CI->db->escape($this->coutChauf);
    $hresAide = $this->CI->db->escape($this->hresAide);
    $coutAide = $this->CI->db->escape($this->coutAide);
    $disposition = $this->CI->db->escape($this->disposition);
    $coutDisposition = $this->CI->db->escape($this->coutDispot);
    $nbBac = $this->CI->db->escape($this->nbBac);
    $strSQL = "INSERT INTO sanivanday (DateJour, NOCAMION, hresCam, coutCarburant, carburant,
                hresChauf, coutChauf, hresAide, coutAide, disposition, coutDisposition, nbBac)
	            VALUES ($date, $camion, $hresCam, $coutCarburant, $carburant, $hresChauf,
                        $coutChauf, $hresAide, $coutAide, $disposition, $coutDisposition, $nbBac)";
    $this->CI->db->query($strSQL);
    $strSQL = 'SELECT LAST_INSERT_ID() as id';
    $resId = $this->CI->db->query($strSQL)->getRow(0);
    $this->id = $resId->id;
  }
  private function update($camion, $date)
  {
    $hresCam = $this->CI->db->escape($this->hresCam);
    $coutCarburant = $this->CI->db->escape($this->coutCarburant);
    $carburant = $this->CI->db->escape($this->carburant);
    $hresChauf = $this->CI->db->escape($this->hresChauf);
    $coutChauf = $this->CI->db->escape($this->coutChauf);
    $hresAide = $this->CI->db->escape($this->hresAide);
    $coutAide = $this->CI->db->escape($this->coutAide);
    $disposition = $this->CI->db->escape($this->disposition);
    $coutDisposition = $this->CI->db->escape($this->coutDispot);
    $nbBac = $this->CI->db->escape($this->nbBac);
    $strSQL = "UPDATE sanivanday
	                SET
		                hresCam=$hresCam,
		                coutCarburant=$coutCarburant,
		                carburant=$carburant,
		                hresChauf=$hresChauf,
		                coutChauf=$coutChauf,
		                hresAide=$hresAide,
		                coutAide=$coutAide,
		                disposition=$disposition,
		                coutDisposition=$coutDisposition,
                    nbBac=$nbBac
	                WHERE NOCAMION = $camion and DateJour = $date ";
    $this->CI->db->query($strSQL);
  }
  //-------------------------------------------------- Proprietes set / get
  public function setCarburant($carburant)
  {
    $this->carburant = $carburant;
  }

  public function getCarburant()
  {
    return $this->carburant;
  }

  public function setCoutCarburant($cout)
  {
    $this->coutCarburant = $cout;
  }

  public function getCoutCarburant()
  {
    return $this->coutCarburant;
  }
  public function setHreChauf($hresChauf)
  {
    $this->hresChauf = $hresChauf;
  }

  public function getHresChauf()
  {
    return $this->hresChauf;
  }

  public function setCoutChauf($coutChauf)
  {
    $this->coutChauf = $coutChauf;
  }

  public function getCoutChauf()
  {
    return $this->coutChauf;
  }

  public function setHreAide($hresAide)
  {
    $this->hresAide = $hresAide;
  }

  public function getHresAide()
  {
    return $this->hresAide;
  }

  public function setCoutAide($coutAide)
  {
    $this->coutAide = $coutAide;
  }

  public function getCoutAide()
  {
    return $this->coutAide;
  }

  public function setHreCam($hresCam)
  {
    $this->hresCam = $hresCam;
  }

  public function getHresCam()
  {
    return $this->hresCam;
  }

  public function setNetByHres($netByHres)
  {
    $this->netByHres = $netByHres;
  }

  public function getNetByHres()
  {
    return $this->netByHres;
  }

  public function setNet($net)
  {
    $this->net = $net;
  }

  public function getNet()
  {
    return $this->net;
  }

  public function setRevenu($revenu)
  {
    $this->revenu = $revenu;
  }

  public function getRevenu()
  {
    return $this->revenu;
  }

  public function setNbBac($nbBac)
  {
    $this->nbBac = $nbBac;
  }

  public function getNbBac()
  {
    return $this->nbBac;
  }

  public function setDisposition($disposition)
  {
    $this->disposition = $disposition;
  }

  public function getDisposition()
  {
    return $this->disposition;
  }

  public function setCollecte($Collecte)
  {
    $this->Collecte = $Collecte;
  }

  public function getCollecte()
  {
    return $this->Collecte;
  }

  public function setTransport($transport)
  {
    $this->transport = $transport;
  }

  public function getTransport()
  {
    return $this->transport;
  }
  public function setCoutDispot($coutDispot)
  {
    $this->coutDispot = $coutDispot;
  }

  public function getCoutDispot()
  {
    return $this->coutDispot;
  }

  public function setCoutBroker($coutBroker)
  {
    $this->coutBroker = $coutBroker;
  }

  public function getCoutBroker()
  {
    return $this->coutBroker;
  }
  // ---------------------------------------------
}
//-----------------------------------------------------------------------
class SanivanModel extends Ci3Model
{
  /** @var array $data */
  protected $data = array();
  protected $arrOfObject = array();
  protected $dataville = array();
  protected $HreDebCam = '';
  protected $HreFinCam = '';
  protected $Tonnage = 0;
  /** @var float $transportCamion */
  protected $transportCamion = 0;
  protected $collecte = 0;
  protected $Transport = 0;
  protected $transportVille = array();
  protected $AllDayCamion;
  protected $optSys;
  function __construct()
  {
    parent::__construct();
    $this->cedules = new CedulesModel();
    
    
    
    $this->AllDayCamion = new Collection();
    $this->optSys = $this->db->query("SELECT * FROM optionssys")->getRow(0);
  }
  //----------------------------------------------------- Calcul
  /**
   * @param object $result
   *
   * @return void
   */
  private function setTransportCam($result)
  {
    //Chauffeur, NOCAMION,  KiloDeb,  KiloFin, KM,
    //Fuel, HreDeb, HreFin, HrsTravCam, idchauffeurs, HELPERS_1, HELPERS_2, HELPERS_3, HELPERS_4,
    //HreTravRH, HreVilleDeb, HreVilleFin, HreTravVille, FinDepotoire, idcam, ID, TypeRh, idrh,idvilles
    //---------------------------------------------------------
    /** @var int $iter */
    $iter = 0;
    /** @var string $nocamion */
    $nocamion = '';
    /** @var int $idville */
    $idville = '';
    /** @var int $idcedule */
    $idcedule = '';
    /** @var string $HreVilleFin */
    $HreDepFin = '';
    $lastrow = $result->getNumRows();
    foreach ($result->getResult() as $row) {
      $nocamion = $row->NOCAMION;
      $this->HreDebCam = $row->HreDeb;
      $this->HreFinCam = $row->HreFin;
      $tonnage = floatval(str_replace(',', '.', $row->Tonnage));
      if ((is_float($tonnage)) and ($idcedule != $row->ID)) {
        $this->Tonnage += $tonnage;
      }
      $to_time = strtotime($row->HreVilleFin);
      $from_time = strtotime($row->HreVilleDeb);
      $minutes = round(abs($to_time - $from_time) / 60, 2);
      $this->collecte += $minutes;
      //if ((is_float($minutes)) and ($idcedule != $row->ID)) {
      //    $this->collecte += $minutes;
      //    $idcedule = $row->ID;
      //}
      //$HreVilleFin = $row->HreVilleFin;
      //if (trim($row->FinDepotoire) != '') {
      //    $HreVilleFin = $row->FinDepotoire;
      //}
      //------------------ Ville Transport
      $to_time = strtotime($row->HreDeb);
      $from_time = strtotime($row->HreVilleDeb);
      $this->transportVille[] = $row->Transport_RDF + $row->Transport_DEP;
      if ($row->Transport_RDF == 0) {
        $to_time = strtotime($row->HreVilleDeb);
        $from_time = strtotime($HreDepFin);
        $minutes = 0;
        if (($to_time) and ($from_time)) {
          $minutes = round(abs($to_time - $from_time) / 60, 2);
        }
        $this->transportVille[] = $minutes;
      }
      if ($row->numero_ligne == $lastrow) {
        $this->transportVille[] = (($row->Transport_TO_RDF == null) ? 0 : intval($row->Transport_TO_RDF));
      }
      $minutes = round(abs($to_time - $from_time) / 60, 2);
      //if ($iter == 0) {
      //    $this->transportVille[$iter] = round($minutes / 60, 2);
      //    $idville = $row->idvilles;
      //    $idcedule = $row->ID;
      //} else if (($idville == $row->idvilles) and ($idcedule != $row->ID)) {
      //    $to_time = strtotime($row->HreVilleFin);
      //    $from_time = strtotime($row->HreVilleDeb);
      //    $minutes = round(abs($to_time - $from_time) / 60, 2);
      //    $this->transportVille[$iter - 1] += round($minutes / 60, 2);
      //} else {
      //    $to_time = strtotime($row->HreVilleFin);
      //    $from_time = strtotime($row->HreVilleDeb);
      //    $minutes = round(abs($to_time - $from_time) / 60, 2);
      //    $this->transportVille[$iter] = round($minutes / 60, 2);
      //    $idville = $row->idvilles;
      //}
      //---------------------
      //$this->transportVille[] = $minutes;
      $HreDepFin = $row->FinDepotoire;
    }
    $transport = 0;
    foreach ($this->transportVille as $value) {
      $transport += intval($value);
    }
    $transport = round($transport / 60, 2);
    $this->Transport = $transport;
    $this->collecte = round($this->collecte / 60, 2);
    //----------------------------------------------------------
  }
  /**
   * @param string $cam
   * @param string $date
   *
   * @return void
   */
  private function CalculDayTransportCam($cam, $date)
  {
    $dteactive = $this->db->escape($date);
    $strSQL = "SELECT DISTINCT ROW_NUMBER() OVER (ORDER BY id) AS numero_ligne, 
                  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS Chauffeur,
                  camionjour.NOCAMION, cedules.remplace_cam, cedules.NOCAMION as camcedule,
LEFT((SELECT Nom FROM typecamion WHERE typecamion.id = 
				(SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = camionjour.NOCAMION)), 2) AS TypeCam,                  
						camionjour.KiloDeb, camionjour.KiloFin, (camionjour.KiloFin - camionjour.KiloDeb) AS KM,
                  camionjour.Fuel,camionjour.HreDeb, camionjour.HreFin, LEFT( TIMEDIFF(camionjour.HreFin,camionjour.HreDeb), 5) as HrsTravCam,cedules.idchauffeurs, cedules.HELPERS_1, cedules.HELPERS_2, cedules.HELPERS_3, cedules.HELPERS_4, 

                  LEFT( TIMEDIFF(sanivan_rh.HreFin,sanivan_rh.HreDeb), 5) AS HreTravRH,
                  info_villes.HreDeb AS HreVilleDeb,info_villes.HreFin AS HreVilleFin,
                  if( ROW_NUMBER() OVER (ORDER BY id) > 1, '0', 
                  ROUND( TIME_TO_SEC( TIMEDIFF(info_villes.HreDeb, camionjour.HreDeb)) / 60, 1)) AS Transport_RDF,
                                  
                  LEFT( TIMEDIFF(info_villes.HreFin,info_villes.HreDeb), 5) AS HreTravVille,
			              if(info_depotoire.fin IS NULL, '0', info_depotoire.fin) AS FinDepotoire, 
			              if(info_depotoire.fin IS NULL, '0',
ROUND( TIME_TO_SEC( TIMEDIFF(info_depotoire.fin, info_villes.HreFin)))) / 60 AS Transport_DEP,
if(info_depotoire.fin IS NULL, '0',
ROUND(TIME_TO_SEC(TIMEDIFF(camionjour.HreFin, info_depotoire.fin)))) / 60 AS Transport_TO_RDF,

							  camionjour.id AS idcam,info_depotoire.Tonnage,
			               cedules.ID, sanivan_rh.TypeRh,sanivan_rh.id AS idrh, cedules.idvilles
	                FROM cedules LEFT JOIN info_villes ON cedules.ID = info_villes.idcedule
                  LEFT  JOIN sanivan_rh ON  cedules.ID = sanivan_rh.idcedule
							    LEFT  JOIN camionjour ON cedules.NOCAMION = camionjour.NOCAMION
                  LEFT  JOIN info_depotoire ON info_villes.id =info_depotoire.idinfoville
			            WHERE cedules.DateJour = $dteactive AND camionjour.DateJour = $dteactive AND 
(cedules.NOCAMION = $cam or cedules.remplace_cam = $cam )       
                  ORDER BY info_villes.HreDeb";
    $result = $this->db->query($strSQL);
    $this->Tonnage = 0;
    $this->transportVille = array();
    $this->setTransportCam($result);
    // Recalculer le tonnage avec une requête propre (le JOIN sanivan_rh duplique les lignes)
    $tonRow = $this->db->query("SELECT IFNULL(SUM(REPLACE(info_depotoire.Tonnage, ',', '.')), 0) AS totalTonnage
        FROM info_depotoire
          INNER JOIN info_villes ON info_depotoire.idinfoville = info_villes.id
          INNER JOIN cedules ON info_villes.idcedule = cedules.ID
        WHERE cedules.DateJour = $dteactive AND cedules.NOCAMION = $cam")->getRow(0);
    $this->Tonnage = ($tonRow) ? floatval($tonRow->totalTonnage) : 0;
    //$this->setTransportVille($result);
  }
  //-----------------------------------------------------  Sanivan Rapport
  public function get_count()
  {
    return $this->db->count_all($this->table);
  }
  /**
   * @param string $dateactive
   * @param int $add
   *
   * @return array
   */
  //public function getDayRapport($dateactive)
  //{
  //  /** @var string $returnval */
  //  $returnval = '';
  //  $dteactive = $this->db->escape($dateactive);
  //  $strSQL = "SELECT rolloffday.* FROM rolloffday WHERE date_fact = $dteactive
  //                    order by NOCAMION, STARTTIME";
  //  $rolloffday = $this->db->query($strSQL);
  //  //$rolloffday = $this->transfertRollOffForRapport($dteactive);
  //  foreach ($rolloffday->getResult() as $row) {
  //    /** @var string $edit */
  //    $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDayRapRO(\'' . $row->NoDemande . '\')">
  //                                                 <i class="fa fa-edit btn btn-success" style="font-size:16px;"></i></a>';
  //    $returnval .= '<tr>
  //                          <td align="center"><button class="btn btn-primary" title="' . $row->notes . '" onclick="ShowCommandeRap(' . $row->NoDemande . ')">' . $row->NoDemande . '</button></td>
  //                          <td align="center"><label style="width:120px"><a class="btn btn-info" aria-label="Edit" title="' . $row->Client . ': &#013;' . $row->AdresseComplete . '">' . substr($row->Client, 0, 10) . '</label></a></td>
  //                          <td align="center"><label  class="form-control" style="width:140px;" >' . $row->TypeDem . '</label></td>
  //                          <td  align="center"><button class="btn btn-default"  title="Boite: ' . $row->BoiteEnPlace . ', Tonnage:' . $row->NbTonnes . ' &#013; Depotoire: ' . $row->Depotoire . ' ">' . $row->FormatCont . '</button></td>
  //                                <td  align="center"><label style="width:110px;">' . $row->TypeMat . '</label></td>
  //                          <td  align="center"><button class="btn btn-default"  title="Chauffeur: ' . $row->Chauffeur . '">' . $row->NOCAMION . '</button></td>

  //                          <td  align="center">
  //                          <div class="input-group input-group-sm" style="width:110px;">
  //                          <input class="form-control" style="width: 80px;text-align: right;font-size:14px;" type="text" value="' . number_format($row->PrixDay, 2) . '" />
  //                          <span class="input-group-btn">
  //                          <label class="form-control" style="height:30px;" >$</label>
  //                          </span></div>
  //                          </td>
  //                          <td  align="center">
  //                          <div class="input-group input-group-sm" style="width:110px;">
  //                          <label class="form-control btn-info" style="width: 80px;text-align: right;font-size:14px;" >' . number_format($row->PrixCommande, 2) . '</label>
  //                          <span class="input-group-btn">
  //                          <label class="form-control" style="height:30px;" >$</label>
  //                          </span></div>
  //                          </td>

  //                          <td  align="center">
  //                          <div class="input-group input-group-sm" style="width:110px;">
  //                          <input class="form-control" style="width: 80px;text-align: right;font-size:14px;" type="text" value="' . number_format($row->CoutDepotoire, 2) . '" />
  //                          <span class="input-group-btn">
  //                          <label class="form-control" style="height:30px;" >$</label>
  //                          </span></div>
  //                          </td>
  //                          <td  align="center">
  //                          <div class="input-group input-group-sm" style="width:70px;">
  //                          <input class="form-control" style="width: 60px;text-align: right;font-size:14px;" type="text" value="' . number_format($row->surpluspoids, 2) . '" />
  //                          <span class="input-group-btn">
  //                          <label class="form-control" style="height:30px;" >TN</label>
  //                          </span></div>
  //                          </td>
  //                          <td  align="center">
  //                          <div class="input-group input-group-sm" style="width:80px;">
  //                          <input class="form-control" style="width: 80px;text-align: right;font-size:14px" type="text" value="' . number_format($row->SurplusLocation, 2) . '" />
  //                          <span class="input-group-btn">
  //                          <label class="form-control" style="height:30px;" >$</label>
  //                          </span></div>
  //                          </td>
  //                          <td>' . $post . '</td>
  //                                              </tr>';
  //  }
  //  //$this->data['rapdayro'] = $returnval;
  //  $this->data['rapdaychauf_cam'] = $this->getCamChaufDay($dateactive);
  //  //$this->data['rapdaychauf'] = $this->getChauffeursDay($dateactive);
  //  return $this->data;
  //}
  /**
   * @param string $dateactive
   * @param string $tr_iter
   * @param string $NOCAMION
   *
   * @return string
   */
//  private function getVilleRapport($dateactive, $tr_iter, $NOCAMION)
//  {
//    $get = $this->input->post_get('titre', TRUE);
//    $print = 'none';
//    if (isset($get)) {
//      $print = 'block';
//    }
//    /** @var string $returnval */
//    $dteactive = $dateactive;
//    $dteactive = $this->db->escape($dateactive);
//    $NOCAM = $this->db->escape($NOCAMION);
//    $span = '<label style="color:black;font-size:large;">';
//    $strSQL = "SELECT rolloffday.*, (Deglacage + Lavage + AjustPrix) as Ajustement,
//                  if(`code` = 5, getRedevance(idclient, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), Poids, IsTRansCour), 0) as Redevance,
//                  if( If(Poids = 0, 0, ROUND( Poids - getTonnage(idclient, FormatContenant), 2)) <=0, 0,`getPrixPoids`(idclient, Poids, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), IsTransCour)) AS prixpoids, getTonnage(idclient, FormatContenant) as Tonnage_5, if( If(Poids = 0, 0, ROUND( Poids - getTonnage(idclient, FormatContenant), 2)) <=0, 0,If(Poids = 0, 0, ROUND( Poids - getTonnage(idclient, FormatContenant), 2))) AS surpluspoids_5,`getPrixDepotoire`(idclient, Poids, FormatContenant, if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0)) AS prixdepotoire,
//                  if(`code` = 5, (SELECT CONCAT('<b>Frais Dispo:</b> ', fraispoids, ' * ', IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',  round(IFNULL(fraispoids * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2),
//                      ' + <br /><b>Frais car site:</b> ', fraisCarburantSite, ' * ',
//                      IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',
//                      ROUND(IFNULL(fraisCarburantSite  * rolloffday.surpluspoids, 0), 2),
//                      ' + <br /><b>Frais car:</b> ', IFNULL(fraisCarburant, 0),
//                      ' + <br /><b>Frais red:</b> ',	IFNULL(FraisRedevance, 0),
//                      ' + <br /><b>Frais red / TM:</b> ', fraisRedevanceTM, ' * ',
//                      IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ' ,ROUND(
//                      IFNULL(fraisRedevanceTM * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2)) FROM clients where id = rolloffday.idclient ),
//                              0) As Parti_1,
//                  if(`code` = 5, (SELECT If(PourcentCarburant = 0, '', CONCAT('<b>Calcul %</b><br />
//                        (<b>Frais Poids:</b> ', fraispoids, ' * ', IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',  round(IFNULL(fraispoids * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2),
//                        ' + <br /><b>Frais car site:</b> ', fraisCarburantSite, ' * ',
//                        IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ',
//                        ROUND(IFNULL(fraisCarburantSite  * rolloffday.surpluspoids, 0), 2),
//                        ' + <br />Frais car: ', IFNULL(fraisCarburant, 0),
//                        ' + <br />Frais red: ',	IFNULL(FraisRedevance, 0),
//                        ' + <br /><b>Frais red / TM:</b> ', fraisRedevanceTM, ' * ',
//                        IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), ' = ' ,ROUND(
//                          IFNULL(fraisRedevanceTM * IF(rolloffday.surpluspoids < 2, 2, rolloffday.surpluspoids), 0),2),
//                                  ') * ',  IFNULL((PourcentCarburant / 100),0))) FROM clients where id = rolloffday.idclient ),0)
//                                    AS Parti_2,
//                      if(SurplusLocation = 0, 0, CONCAT('<b>Date Liv. Parent:</b> ', (SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ) ,
//                              '<br /><b>Nb Jour Surplus:</b> ', DATEDIFF(rolloffday.date_fact, DATE_ADD((SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ), INTERVAL 14 DAY)),
//                              '<br /><b>Frais Liv. Jour:</b> ',  ((SELECT FraisLivraison FROM optionssys) / 7),
//                            '<br /><br />Calcul: ',DATEDIFF(rolloffday.date_fact, DATE_ADD((SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ), INTERVAL 14 DAY)),
//                            ' * ', ((SELECT FraisLivraison FROM optionssys) / 7),
//                            ' = ', (DATEDIFF(rolloffday.date_fact, DATE_ADD((SELECT co.date_fact FROM commandes AS co WHERE id = rolloffday.idparent ), INTERVAL 14 DAY)) * ((SELECT FraisLivraison FROM optionssys) / 7) ))) AS InfoLocation,
//                      if(`code` = 5 and FormatContenant not in (10, 11),
//                      getInfoCommande(idclient, FormatContenant ),
//                      CONCAT('<b>PRIX:</b> ', IFNULL(round(PrixCommande, 2), 0),
//                                ' + <br /><b>RED.:</b> ', IFNULL(RedevanceCommande, 0),
//                              ' + <br /><b>CAR.:</b> ', IFNULL(gazeline, 0),
//                              ' + <br /><b>CAR. SITE:</b> ',	IFNULL(carburant_site, 0),
//                              ' + <br /><b>DISTANCE:</b> ',	IFNULL(fraisdistance, 0),
//                              ' + <br /><b>DEMOLITION:</b> ',	IFNULL(fraisdemolition, 0)
//                      )) as InfoCommande,
//                      CONCAT('<b>PRIX:</b> ', IFNULL(ROUND(PrixDay,2), 0),
//                          ' + <br /><b>AjustPrix:</b> ', IFNULL(ROUND( AjustPrix,2), 0),
//                              ' + <br /><b>DISPO.:</b> ', if(`code` = 5 AND FormatContenant in (10, 11), `getPrixPoids`(idclient, Poids, FormatContenant,
//                              if(IsCarton = 1 or IsVerre = 1 or IsBois, 1, 0), IsTransCour),
//if(`code` = 5 AND FormatContenant not in (10, 11), 0,prixsurpluspoids)),
//                              ' + <br /><b>SUP. LOC.:</b> ', IFNULL(SurplusLocation, 0),
//                              ' + <br /><b>DEGLACAGE:</b> ', IFNULL(ROUND( Deglacage,2), 0),
//                              ' + <br /><b>LAVAGE:</b> ', IFNULL(ROUND( Lavage,2), 0) ) AS InfoRevBrute
//                  FROM rolloffday WHERE date_fact = $dteactive and NOCAMION = $NOCAM
//                      order by STARTTIME";
//    $rolloffday = $this->db->query($strSQL);
//    $this->data['IsLoaded'] = ($rolloffday->getNumRows() > 0) ? 1 : 0;
//    $titreNB = $rolloffday->getNumRows() . ' commandes pour le camion ' . $NOCAMION;
//    $returnval = '<tr style="text-align:right;" class="warning"  id="tr_' . $tr_iter . '"  style="display:' . $print . ';" > <td></td><td colspan="9" ><table class="table table-bordered">
//                        <thead><tr><th colspan="11"><label class="titrenb">' . $titreNB . '</label></th> </tr>';
//    $returnval .= '

//                      <tr  style="text-align:right;color:darkblue;font-weight:bold;">
//                        <th>&nbsp;&nbsp;</th>
//                        <th># COMMANDE</th>
//                        <th>INFO CLIENT</th>
//                        <th>CODE</th>
//                        <th>ACTION</th>
//                        <th>CONTENANT</th>
//                        <th>T.M INC</th>
//                        <th>T.M</th>
//                        <th>MAT&Eacute;RIAUX</th>
//                        <th>PRIX</th>
//                        <th>AJUSTEMENT PRIX</th>
//                        <th>CAR / %</th>
//                        <th>+T.M</th>
//                        <th>PRIX +T.M / RED</th>
//                        <th>PRIX +LOCATION</th>
//                        <th>D&Eacute;GLA&Ccedil;AGE</th>
//                        <th>LAVAGE</th>

//                        <th>REV. BRUT</th>
//                        <th><span >DISPO TM / RED</span></th>
//                        <th>REV. NET</th>
//                        <th>
//                        </th><th>
//                        </th>
//                          </tr></thead><tbody id="tb_' . $NOCAMION . '" >';
//    //$rolloffday = $this->transfertRollOffForRapport($dteactive);
//    foreach ($rolloffday->getResult() as $row) {
//      /** @var string $fraissupplementaire */
//      $fraissupplementaire = (($row->code == 5) ? $row->prixpoids : $row->prixsurpluspoids);
//      if (($row->IsRecBois == 1) or ($row->IsCarton == 1) or ($row->IsVerre == 1)) {
//        $fraissupplementaire = $row->CarRed;
//      }
//      /** @var string $edit */
//      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDayRapRO(' . $row->NoDemande . ',\'' . $NOCAMION . '\')">
//                                                   <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
//      /** @var string $refresh */
//      $refresh = ' <a aria-label="Edit" title="Cliquer pour recalculer la ligne" onclick="refreshRapRO(' . $row->NoDemande . ')">
//                                                   <i class="fa fa-refresh btn btn-warning" style="font-size:16px;"></i></a>';
//      $showcom = "<a class='btn btn-info btn-sm' style='font-size:14px;margin-left:30px;' id='btnroll_" . $row->NoDemande . "'
//                                onclick='ShowCommandeRap(" . $row->NoDemande . ")' >Voir Commande</a>";
//      $showparent = ($row->idparent == 0) ? '' : "<a class='btn btn-default btn-sm' style='font-size:14px;margin-left:30px;' id='btnroll_" . $row->NoDemande . "'
//                                onclick='ShowCommandeRap(" . $row->idparent . ")' >Voir Parent</a>";
//      $returnval .= '<tr id="trligne_' . $row->NoDemande . '">
//<td class="supprime"><button type="button"
//                              class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHidePrix(\'trPrix_' . $row->NoDemande . '\',' . $row->NoDemande . ')" style="outline:none;">
//                                  <span id="spShowHide_' . $row->NoDemande . '" class="glyphicon glyphicon-chevron-down" style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
//                            <td align="center"><button class="btn btn-primary" title="' . $row->notes . '" onclick="ShowCommandeRap(' . $row->NoDemande . ')">' . $row->NoDemande . '</button>
//' . (($row->idparent > 0) ? ' <br /><button class="btn btn-default"  onclick="ShowCommandeRap(' . $row->idparent . ')">' . $row->idparent . '</button> ' : '') . '
//</td>
//                            <td align="center"><label style="width:120px"><a class="btn btn-info" aria-label="Edit" title="' . $row->Client . ': &#013;' . $row->AdresseComplete . '">' . substr($row->Client, 0, 10) . '</label></a></td>
//                            <td align="center"><label  class="form-control" onclick="showCodeToSelect()" style="width:140px;font-weight:bold;' . (($row->code == 5) ? 'background-color:lime' : '') . '" >' . $row->code . '</label></td>
//                            <td align="center"><label  class="form-control" style="font-weight:bold;' . (($row->IsTransCour) ? 'width:185px;' : 'width:140px;') . '" >' .
//        $row->TypeDem . (($row->IsTransCour) ? '<span style="color:red;font-weight:bold;">-TRANSCOUR</span>' : '') . '</label></td>
//                            <td  align="center"><button class="btn btn-default" style="font-weight:bold;" title="Boite: ' . $row->BoiteEnPlace . ', Tonnage:' . $row->NbTonnes . ' &#013; Depotoire: ' . $row->Depotoire . ' ">' . $row->FormatCont . '</button></td>
//                                  <td  align="center"><label style="width:110px;">' . (($row->code == 5) ? $row->Tonnage_5 : $row->NbTonnes) . '</label></td>
//                                  <td  align="center"><input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;"
//                                  onkeyup="EnterField(this.value, event,' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  onblur="postDayRapRO(' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  type="text" id="Poids_' . $row->NoDemande . '" name="Poids_' . $row->NoDemande . '"
//                                  value="' . number_format($row->Poids, 2) . '" /></td>
//                                  <td  align="center"><label style="width:110px;">' . $row->TypeMat . '</label></td>

//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:110px;font-weight:bold;">
//                            <input class="form-control" style="width: 110px;text-align: right;font-size:18px;background-color:yellow;color:darkblue;" type="text" id="PrixDay_' . $row->NoDemande . '" name="PrixDay_' . $row->NoDemande . '" value="' . number_format($row->PrixDay, 2) . '" redonly  data-html="true"';
//      $popover = '
// data-toggle="popover" data-trigger="focus" data-placement="left" title="Calcul de la commande #' . $row->NoDemande . '" data-content="' . $showcom . $showparent . '<br /><br />' . $row->InfoCommande . '"';
//      if ($row->code == 5 and ($row->FormatContenant == 10 or $row->FormatContenant == 11)) {
//        $popover = '';
//      }
//      $returnval .= $popover . '/>
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:110px;">
//                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;"
//                                  onkeyup="EnterField(this.value, event,' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  type="text" id="AjustPrix_' . $row->NoDemande . '" name="AjustPrix_' . $row->NoDemande . '"
//                                      value="' . number_format($row->AjustPrix, 2) . '" />
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:110px;font-weight:bold;">
//                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="PrixDay_' . $row->NoDemande . '" name="PrixDay_' . $row->NoDemande . '" value="' . number_format($row->CarRed, 2) . '" disabled />
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:70px;">
//                            <input class="form-control" id="surpluspoids_' . $row->NoDemande . '" name="surpluspoids_' . $row->NoDemande . '"
//                                  onkeyup="EnterField(this.value, event,' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  onblur="postDayRapRO(' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  style="width: 60px;text-align: right;font-size:14px;font-weight:bold;" type="text" value="' . number_format((($row->code == 5) ? $row->surpluspoids_5 : $row->surpluspoids), 2) . '" disabled />
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >TM</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:80px;">
//                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text" id="prixsurpluspoids_' . $row->NoDemande . '" name="prixsurpluspoids_' . $row->NoDemande . '" value="' . number_format((($row->code == 5) ? $row->prixpoids : $row->prixsurpluspoids), 2) . '" readonly data-html="true"';

//      $popover = '
// data-toggle="popover" data-trigger="focus" data-placement="left" title="Calcul Charge Disposition #' . $row->NoDemande . '" data-content="' . $showcom . $showparent . '<br /><br />' . $row->Parti_1 . '<br />' . $row->Parti_2 . '"';
//      $chksurplus = ((($row->code == 5) ? $row->prixpoids : $row->prixsurpluspoids) == 0);
//      if ($chksurplus) {
//        $popover = '';
//      }
//      $returnval .= $popover . '/>
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:130px;">
//                            <input class="form-control" style="width: 80px;text-align: right;font-size:14px;color:darkred" type="text"
//                                    onkeyup="EnterField(this.value, event,' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                    onblur="postDayRapRO(' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                            id="SurplusLocation_' . $row->NoDemande . '" name="SurplusLocation_' . $row->NoDemande . '" value="' . number_format($row->SurplusLocation, 2) . '"
//                              data-html="true"';
//      $surloc = '';
//      if ($row->SurplusLocation > 0) {
//        $surloc = 'data-toggle="popover" data-trigger="focus" data-placement="bottom" title="Calcul surplus Location #' . $row->NoDemande . '" data-content="' . $showcom . $showparent . '<br /><br />' . $row->InfoLocation . '"';
//      }
//      $returnval .= $surloc . (($row->HaveSurplusLocation != 1) ? ' disabled ' : '') . '/>
//                                <span class="input-group-btn">
//                                  <label class="form-control" style="height:30px;" >$</label>
//                                </span>
//                            <div class="input-group-btn">
//                            <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px" onclick="chkSurplusLoc(' . $row->NoDemande . ')"
//                          ' . (($row->HaveSurplusLocation == 1) ? ' checked ' : '') . ' id="chkSurplusLoc_' . $row->NoDemande . '"  >

//                          </div></div>

//                            </td>
//                            <td  align="center">
//                              <div class="input-group input-group-sm" style="width:140px;">
//                                <input class="form-control" style="width: 80px;text-align: right;font-size:14px;font-weight:bold;" type="text"
//                                    onkeyup="EnterField(this.value, event,' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                    onblur="postDayRapRO(' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                    id="Deglacage_' . $row->NoDemande . '" name="Deglacage_' . $row->NoDemande . '"
//                                    value="' . number_format($row->Deglacage, 2) . '"  ' . (($row->IsDeglacage == 0) ? ' disabled ' : '') . ' />
//                                    <span class="input-group-btn">
//                                      <label class="form-control" style="height:30px;" >$</label>
//                                    </span>
//                                    <div class="input-group-btn">
//                                      <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px"
//                                          onclick="chkDeglacage(' . $row->NoDemande . ')" id="chkDeglacage_' . $row->NoDemande . '"
//                                              ' . (($row->IsDeglacage == 1) ? ' checked ' : '') . '  >
//                                      </div>
//                              </div>
//                            </td>

//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:130px;">
//                            <input class="form-control" style="width: 80px;height:30;text-align: right;font-size:14px;font-weight:bold;"
//                                  onkeyup="EnterField(this.value, event,' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  onblur="postDayRapRO(' . $row->NoDemande . ',\'' . $NOCAMION . '\')"
//                                  type="text" id="Lavage_' . $row->NoDemande . '" name="Lavage_' . $row->NoDemande . '"
//                                  value="' . number_format($row->Lavage, 2) . '"  ' . (($row->IsLavage == 0) ? ' disabled ' : '') . ' />
//                                <span class="input-group-btn">
//                                  <label class="form-control" style="height:30px;" >$</label>
//                                </span>
//                            <div class="input-group-btn">
//                            <input type="checkbox" class="form-control" style="width: 30px;height:30;margin-top:0px" onclick="chkLavage(' . $row->NoDemande . ')"
//                          ' . (($row->IsLavage == 1) ? ' checked ' : '') . ' id="chkLavage_' . $row->NoDemande . '"  >

//                          </div></div>

//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:140px;">
//                            <input class="form-control" style="width: 110px;text-align: right;font-size:18px;background-color:yellow;color:darkblue;font-weight:bold;" type="text" id="RevBrut_' . $row->NoDemande . '" name="RevBrut_' . $row->NoDemande . '" value="' . number_format($row->PrixDay + $fraissupplementaire + $row->Ajustement + (($row->HaveSurplusLocation == 1) ? $row->SurplusLocation : 0), 2) . '" readonly  data-html="true" ';
//      $popover = '
//         data-toggle="popover" data-trigger="focus" data-placement="left" title="Calcul Charge Disposition #' . $row->NoDemande . '" data-content="' . $showcom . $showparent . '<br /><br />' . $row->InfoRevBrute . '"';
//      $returnval .= $popover . '/>
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm"  style="width:145px;">
//                            <input class="form-control" id="CoutDepotoire_' . $row->NoDemande . '" name="CoutDepotoire_' . $row->NoDemande . '" style="width: 110px;text-align: right;font-size:18px;font-weight:bold;color:darkred;" type="text" value="' . number_format($row->CoutDepotoire, 2) . '" />
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td  align="center">
//                            <div class="input-group input-group-sm" style="width:140px;">
//                            <label  id="revenunet_' . $row->NoDemande . '" name="revenunet_' . $row->NoDemande . '" style="width: 110px;text-align: right;font-size:18px;font-weight:bold;padding-top:4px;background-color:darkred;color:white;height:30px;" >' . number_format(($row->PrixDay + $fraissupplementaire + $row->Ajustement + (($row->HaveSurplusLocation == 1) ? $row->SurplusLocation : 0) - $row->CoutDepotoire), 2) . ' </label>
//                            <span class="input-group-btn">
//                            <label class="form-control" style="height:30px;margin-top:-5px;" >$</label>
//                            </span></div>
//                            </td>
//                            <td>' . $post . '</td><td>' . $refresh . '</td></tr>
//                            <tr id="trPrix_' . $row->NoDemande . '" class="separateur" style="display:none"><td  class="no-line">&nbsp;&nbsp;</td><td colspan="16">' . $this->getCalculPrix($row->NoDemande) . '</td></tr>
//                                                ';
//      $strSQL = "UPDATE rapprojour
//                  SET
//                      RevenuBrut=(SELECT SUM(IFNULL(PrixDay,0) + IFNULL(prixsurpluspoids,0))
//                                FROM rolloffday WHERE NOCAMION = $NOCAMION AND date_fact = $dteactive),
//                      Enfouissement=(SELECT SUM(IFNULL(CoutDepotoire,0))
//                                FROM rolloffday WHERE NOCAMION = $NOCAMION AND date_fact = $dteactive)
//                  WHERE  nocamion = $NOCAMION AND datejour = $dteactive";
//      $rap = $this->db->query($strSQL);
//    }
//    return $returnval . ' </tbody></table> </td></tr>';
//  }
  /**
   *
   * @param string $dateactive
   * @param int $trIter
   * @param object $rowCedule
   *
   * @return string
   */
  public function getRHDay($dateactive, $trIter, $rowCedule)
  {
    $get = $this->input->post_get('titre', TRUE);

    $print = 'none';
    if (isset($get)) {
      $print = 'block';
    }
    /** @var string $idcedule */
    $idcedule = $rowCedule->ID;
    /** @var string $chauffeur */
    $chauffeur = '';
    /** @var string $returnval */
    $dteactive = $this->db->escape($dateactive);
    $where = " ( sanivan_rh.idrh = " . $rowCedule->idchauffeurs;
    $where .= (trim($rowCedule->idremplace_chauf) == "0" or trim($rowCedule->idremplace_chauf) == "") ? "" : " or sanivan_rh.idrh = " . $rowCedule->idremplace_chauf;
    $where .= (trim($rowCedule->HELPERS_1) == "0" or trim($rowCedule->HELPERS_1) == "") ? "" : " or sanivan_rh.idrh = " . $rowCedule->HELPERS_1;
    $where .= (trim($rowCedule->HELPERS_2) == "0" or trim($rowCedule->HELPERS_2) == "") ? "" : " or sanivan_rh.idrh = " . $rowCedule->HELPERS_2;
    $where .= (trim($rowCedule->HELPERS_3) == "0" or trim($rowCedule->HELPERS_3) == "") ? "" : " or sanivan_rh.idrh = " . $rowCedule->HELPERS_3;
    $where .= (trim($rowCedule->HELPERS_4) == "0" or trim($rowCedule->HELPERS_4) == "") ? "" : " or sanivan_rh.idrh = " . $rowCedule->HELPERS_4;
    $where .= ')';
	$span = '<label style="color:black;font-size:large;">';
	$strSQL = "SELECT  sanivan_rh.id, (SELECT CONCAT(Prenom, ' ', Nom)
					FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh) AS Chauffeur,
		  IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = sanivan_rh.idrh), 0) AS isbroker,
		  IFNULL(sanivan_rh.montant_forfaitaire, 0) AS montant_forfaitaire,
				`TypeRh`, HreDeb, HreFin, HreDeb_extra, HreFin_extra, `TypeExtra`,
		  IFNULL(
		  left(ADDTIME(TIMEDIFF(HreFin,HreDeb), TIMEDIFF(HreFin_extra,HreDeb_extra)), 5),
				  LEFT( TIMEDIFF(HreFin,HreDeb), 5)) AS HrsTrav
				FROM sanivan_rh
				WHERE
				sanivan_rh.datejour = $dteactive and $where ORDER BY idcedule, TypeRh";
	$sanivan_rh = $this->db->query($strSQL);
    $returnval = '<tr  class="info"  style="text-align:right;"  ><td colspan="13" ><table class="table table-bordered">
		                <thead><tr><th colspan="9"><label class="titrenb">Chauffeur et Aide(s)</label></th> </tr>';
    $returnval .= '

                      <tr  style="text-align:right;color:darkblue;font-weight:bold;">
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
					  <tbody >';
    $idsanrh = '';
    if ($sanivan_rh->getNumRows() > 0) {

	  foreach ($sanivan_rh->getResult() as $row) {
		$idsanrh = $row->id;
		if ($row->isbroker == 1) {
		  /** @var string $postBroker */
		  $postBroker = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postRHDayBroker(' . $row->id . ')">
													 <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
		  $returnval .= '<tr id="trligne_' . $row->id . '" style="background-color:#FFF3E0;">
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
							  <td>' . $postBroker . '</td></tr>';
		} else {
		/** @var string $post */
		$post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postRHDay(' . $row->id . ')">
													 <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
		$returnval .= '<tr id="trligne_' . $row->id . '">

							  <td align="center"><label style="width:250px">' . $row->Chauffeur . '</label></td>
							  <td align="center">
								  <input class="form-control" style="width: 100px;font-size:14px;" type="time"
									  id="HreDeb_' . $row->id . '" name="HreDeb_' . $row->id . '"
									  value="' . $row->HreDeb . '" />
							  </td>
							  <td align="center">
								  <input class="form-control" style="width: 100px;font-size:14px;" type="time"
									  id="HreFin_' . $row->id . '" name="HreFin_' . $row->id . '"
									  value="' . $row->HreFin . '" />';
		if ($row->TypeRh == "0") {
          $returnval .= '</td>
                              <td align="center">
								    <select class="form-control form-control-modern" id="TypeExtra_' . $row->id . '" style="width:180px;color:red;font-weight:bold;">
									  <option value="">Choisir</option>
												  ' . $this->GetTypeExtra($row->TypeExtra) . '
								    </select>
							  </td><td align="center">
								  <input class="form-control" style="width: 100px;font-size:14px;font-weight:bold;" type="time"
                                      id="HreDeb_extra_' . $row->id . '" name="HreDeb_extra_' . $row->id . '"
                                      value="' . $row->HreDeb_extra . '" />
							  </td>
                              <td align="center">
								  <input class="form-control" style="width: 100px;font-size:14px;font-weight:bold;" type="time"
                                      id="HreFin_extra_' . $row->id . '" name="HreFin_extra_' . $row->id . '"
                                      value="' . $row->HreFin_extra . '" />
							  </td>	';
          //--------------------------------------------------------
          //  HreDeb, HreFin, HreDeb_extra, HreFin_extra
          $to_time = strtotime($row->HreFin);
          $from_time = strtotime($row->HreDeb);
          $minutes = round(abs($from_time - $to_time) / 60, 2);
          $to_time = strtotime($row->HreFin_extra);
          $from_time = strtotime($row->HreDeb_extra);
          $minutes += round(abs($from_time - $to_time) / 60, 2);
          $hrsChauf = round($minutes / 60, 2);
          $hrs = $this->roundHres($hrsChauf);
          $this->AllDayCamion->getItem($rowCedule->NOCAMION)->setHreChauf($hrs);
          // Récupérer le prix chauffeur depuis dictionprix
          $prixChauf = $this->getPrixFromDiction('Chauffeur Sani', $dateactive);
          if ($prixChauf == 0) {
            // Fallback sur l'ancien système si aucun prix dans dictionprix
            $prixChauf = floatval($this->optSys->salairechauffeur_san);
          }
          $cout = round($hrs * $prixChauf, 2);
          $cout = number_format((float) $cout, 2, '.', '');
          $this->AllDayCamion->getItem($rowCedule->NOCAMION)->setCoutChauf($cout);
          //--------------------------------------------------------
        } else {
          $returnval .= '<td></td><td></td><td></td>';
          //--------------------------------------------------------
          $to_time = strtotime($row->HreFin);
          $from_time = strtotime($row->HreDeb);
          $minutes = round(abs($from_time - $to_time) / 60, 2);
          $hrsAide = round($minutes / 60, 2);
          $hrs = $this->roundHres($hrsAide);
          $this->AllDayCamion->getItem($rowCedule->NOCAMION)->setHreAide($hrs);
          // Récupérer le prix aide depuis dictionprix
          $prixAide = $this->getPrixFromDiction('Aide', $dateactive);
          if ($prixAide == 0) {
            // Fallback sur l'ancien système si aucun prix dans dictionprix
            $prixAide = floatval($this->optSys->salalaireaide);
          }
          $cout = round($hrs * $prixAide, 2);
          $cout = number_format((float) $cout, 2, '.', '');
          $this->AllDayCamion->getItem($rowCedule->NOCAMION)->setCoutAide($cout);
          //--------------------------------------------------------
        }
        $returnval .= '<td  align="center">
                              <input class="form-control" type="time" id="HrsTrav_' . $row->id . '" style="width:100px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" />
                              </td>
                <td>' . $post . '</td></tr>';
        } // end else (non-broker)
      }
    } else {
      $returnval .= '<td colspan="7" align="center">Information des RH sur autre ligne.</td>';
    }
    $villes = $this->getVilleDay($dateactive, $this->db->escape($rowCedule->NOCAMION), $rowCedule->ID);
    $sommaire = '<tr style="text-align:right;" class="warning"  id="tr_' . $trIter . '"  style="display:' . $print . ';" > <td></td><td colspan="14" ><table class="table table-bordered">

		                <thead>
					<tr><th colspan="16"><label class="titrenb">SOMMAIRE</label></th> </tr>';
	$sommaire .= '
					  <tr  style="text-align:right;color:darkblue;font-weight:bold;">
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
						<th style="text-align:center;background-color:#FFF3E0;">$ BROKER</th>
						<th style="text-align:center;background-color:yellow;">REVENU</th>
						<th style="text-align:center;background-color:yellow;">REV. NET</th>
					  </tr>
					  </thead>
					  <tbody >';
    $sommaire .= '<tr >
<td align="center"><a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postSommaire(' . $idsanrh . ')">
										             <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getHresChauf() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCoutChauf() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getHresAide() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCoutAide() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getHresCam() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCarburant() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCoutCarburant() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getTransport() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCollecte() . '</label></td>

<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getDisposition() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCoutDispot() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px">' . $this->AllDayCamion->getItem($rowCedule->NOCAMION)->getNbBac() . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px;color:red;background-color:#FFF3E0;">' . number_format((float)$this->AllDayCamion->getItem($rowCedule->NOCAMION)->getCoutBroker(), 2, '.', '') . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px;background-color:yellow;">' . number_format((float)$this->AllDayCamion->getItem($rowCedule->NOCAMION)->getRevenu(), 2, '.', '') . '</label></td>
<td align="center"><label class="form-control form-control-modern" style="width:100px;background-color:yellow;">' . number_format((float)$this->AllDayCamion->getItem($rowCedule->NOCAMION)->getNet(), 2, '.', '') . '</label></td></tr>';

    $sommaire .= $returnval . ' </tbody></table></td></tr>';
    //---------------------------------------------------------------------------
    $sommaire .= $villes . '</td></tr></tbody></table></td></tr>';
    //---------------------------------------------------------------------------

    return $sommaire;
  }
  private function getSommaireCam()
  {

  }
  public function GetTypeExtra($typeext = '')
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "Select Nom, id from typeextra order BY Nom ";
    $camions = $this->db->query($strSQL);
    foreach ($camions->getResult() as $row) {
      $selected = (($typeext == $row->id) ? ' selected ' : '');
      $returnval .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->Nom . '</option>';
    }
    return $returnval;
  }
  private function roundHres($hres)
  {
    if ((intval($hres) == 0) or ($hres == '')) {
      return '0.00';
    }
    $len = strlen($hres);
    if ($len < 4) {
      $hres = $hres . '.00';
    }
    if (strpos($hres, '.') !== false) {
      $arrHres = explode('.', $hres);
    } else {
      return round($hres, 2);
    }
    if (intval($arrHres[1]) < 13) {
      return $arrHres[0] . '.00';
    } else if (intval($arrHres[1]) > 12 and intval($arrHres[1]) < 26) {
      return $arrHres[0] . '.25';
    } else if (intval($arrHres[1]) > 25 and intval($arrHres[1]) < 38) {
      return $arrHres[0] . '.25';
    } else if (intval($arrHres[1]) > 37 and intval($arrHres[1]) < 52) {
      return $arrHres[0] . '.50';
    } else if (intval($arrHres[1]) > 51 and intval($arrHres[1]) < 63) {
      return $arrHres[0] . '.50';
    } else if (intval($arrHres[1]) > 62 and intval($arrHres[1]) < 76) {
      return $arrHres[0] . '.75';
    } else if (intval($arrHres[1]) > 75 and intval($arrHres[1]) < 87) {
      return $arrHres[0] . '.75';
    } else if (intval($arrHres[1]) > 76) {
      $hre = $arrHres[0] + 1;
      return $hre . '.00';
    }
  }

  /**
   * Récupère le prix depuis dictionprix pour un typeitem donné à une date donnée
   * @param string $typeitem - Le type d'item ('Chauffeur Sani', 'Aide', 'Fuel')
   * @param string $dateactive - La date de validation (format 'Y-m-d')
   * @return float - Le prix trouvé ou 0 si aucun prix valide
   */
  private function getPrixFromDiction($typeitem, $dateactive)
  {
    $typeEsc = $this->db->escape($typeitem);
    $dateEsc = $this->db->escape($dateactive);

    $sql = "SELECT prix 
            FROM dictionprix 
            WHERE typeitem = $typeEsc 
              AND isdeleted = 0
              AND datestart <= $dateEsc 
              AND (dateend IS NULL OR dateend >= $dateEsc OR dateend = '0000-00-00')
            ORDER BY datestart DESC 
            LIMIT 1";

    $result = $this->db->query($sql);

    if ($result && $result->getNumRows() > 0) {
      $row = $result->getRow();
      return floatval($row->prix);
    }

    return 0.0;
  }

  /**
   * @param string $dateactive
   *
   * @return array
   */
  public function getCamDay($dateactive)
  {
    $get = $this->input->post_get('titre', TRUE);

    $nocamion = $this->input->post_get('nocamion', TRUE);
    $camion = '';
    if (isset($nocamion)) {

      $camion = ' AND NOCAMION = ' . $this->db->escape($nocamion) . ' ';
    }
    $print = (isset($get));
    $this->data['IsLoaded'] = '2';
    $this->data['rapdaychauf_cam'] = '';
    $this->data['raprojoursani'] = '';

    /** @var string $returnval */
    $returnval = '';
    /** @var int $trIter */
    $trIter = 1;
    $dteactive = $this->db->escape($dateactive);
    //----------------------------------------------------------

    $strSQL = "SELECT distinct NOCAMION, id FROM camionjour WHERE DateJour = $dteactive AND 
                    camionjour.NOCAMION IN (SELECT distinct cedules.NOCAMION FROM cedules WHERE cedules.DateJour = $dteactive)
                    AND (SELECT camions.`TypeCamion` FROM camions WHERE camions.NoCamion = camionjour.NOCAMION) IN (1,7)
                $camion
              ORDER BY NOCAMION";
    $camjour = $this->db->query($strSQL);
    //----------------------------------------------------------
    $nbrows = $camjour->getNumRows();
    if ($nbrows == 0) {
      return $this->data;
    }

    foreach ($camjour->getResult() as $rowcam) {
      $iter = 0;
      $collect = 0;
      $this->collecte = 0;
      $this->Transport = 0;
      $this->transportVille = array();
      $cam = $this->db->escape($rowcam->NOCAMION);
      // Vérifier si le chauffeur est un broker avant le calcul
      $brokerChk = $this->db->query("SELECT IFNULL((SELECT reshumaine.isbroker FROM reshumaine 
          WHERE reshumaine.id = (SELECT idchauffeurs FROM cedules WHERE NOCAMION = $cam AND DateJour = $dteactive LIMIT 1)), 0) AS isbroker")->getRow(0);
      $isBrokerCam = (isset($brokerChk) && $brokerChk->isbroker == 1);
      if (!$isBrokerCam) {
        $this->CalculDayTransportCam($cam, $dateactive);
        $to_time = strtotime($this->HreFinCam);
        $from_time = strtotime($this->HreDebCam);
        $minutes = round(abs($to_time - $from_time) / 60, 2);
        $transCam = round($minutes / 60, 2);
        $hrescam = $transCam;
        $transCam -= $this->collecte;
      } else {
        $this->collecte = 0;
        $this->Transport = 0;
        $this->Tonnage = 0;
        $transCam = 0;
        $hrescam = 0;
        // Calculer le tonnage broker depuis les dépotoires
        $brokerTonRow = $this->db->query("SELECT IFNULL(SUM(REPLACE(info_depotoire.Tonnage, ',', '.')), 0) AS totalTonnage
            FROM info_depotoire
              INNER JOIN info_villes ON info_depotoire.idinfoville = info_villes.id
              INNER JOIN cedules ON info_villes.idcedule = cedules.ID
            WHERE cedules.DateJour = $dteactive AND cedules.NOCAMION = $cam")->getRow(0);
        $this->Tonnage = ($brokerTonRow) ? floatval($brokerTonRow->totalTonnage) : 0;
      }
      $this->AllDayCamion->addItem(new CamDay(
        $transCam,
        $this->collecte,
        $hrescam,
        $rowcam->NOCAMION,
        $dateactive
      ), $rowcam->NOCAMION);

      //----------------------------------------------------------
      /** @var int $HaveInfo */
      $HaveInfo = 1;
	  $strSQL = "SELECT DISTINCT
				  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS Chauffeur,
				  IFNULL((SELECT reshumaine.isbroker FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs), 0) AS isbroker,
				  camionjour.NOCAMION, camionjour.KiloDeb, camionjour.KiloFin, (camionjour.KiloFin - camionjour.KiloDeb) AS KM,
				  camionjour.Fuel,camionjour.HreDeb, camionjour.HreFin, 
				  LEFT( TIMEDIFF(camionjour.HreFin,camionjour.HreDeb), 5) as HrsTrav,
				  cedules.idchauffeurs, cedules.HELPERS_1, cedules.HELPERS_2, cedules.HELPERS_3, 
				  cedules.HELPERS_4,camionjour.id AS idcam,cedules.ID, cedules.remplace_cam,cedules.idremplace_chauf,
					(SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) as NomVille,
					IFNULL((SELECT sanivan_rh.idcedule FROM sanivan_rh WHERE 
							  sanivan_rh.idcedule = cedules.ID limit 0,1), 0) as HaveRH,
					(SELECT LEFT( TIMEDIFF(info_villes.HreFin,info_villes.HreDeb), 5) FROM info_villes 
							  WHERE info_villes.idcedule = cedules.ID limit 0, 1) AS HresVille,
						IFNULL((SELECT sanivan_rh.montant_forfaitaire FROM sanivan_rh WHERE sanivan_rh.idcedule = cedules.ID AND sanivan_rh.TypeRh = 0 LIMIT 1), 0) AS montant_forfaitaire,
						IFNULL((SELECT sanivan_rh.id FROM sanivan_rh WHERE sanivan_rh.idcedule = cedules.ID AND sanivan_rh.TypeRh = 0 LIMIT 1), 0) AS idsanrh
						FROM cedules LEFT JOIN info_villes ON cedules.ID = info_villes.idcedule
							INNER JOIN camionjour ON cedules.NOCAMION = camionjour.NOCAMION
						  WHERE cedules.DateJour = $dteactive AND camionjour.DateJour = $dteactive AND cedules.NOCAMION = $cam
					   ORDER BY cedules.NOCAMION, cedules.ID LIMIT 0,1";
      $sanicam = $this->db->query($strSQL);

      foreach ($sanicam->getResult() as $row) {

        //---------------------------------------------
        $test = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;'
                                  onclick='DayCamChauf($rowcam->id)' title='Enregistrer'><i class='fa fa-check btn btn-success' style='font-size:16px;'></i></a>";
        $errclass = ' btn-danger ';
        $returnval .= '<tr id="trcam_' . $row->idcam . '">
                          <td class="supprime"><button type="button"
		                        class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHideRap(\'tr_' . $trIter . '\',' . $trIter . ')" style="outline:none;">
                                    <span id="spShowHide_' . $trIter . '" class="glyphicon glyphicon-chevron-down" style="color:darkblue;font-weight:bold;" aria-hidden="true"></span></button></td>
                              <td align="left"><label id="lb_chauf_' . $trIter . '" style="width:190px">' . $row->Chauffeur . '</label>
                              </td>
                              <td align="center"><label id="lb_cam_' . $trIter . '" style="width:70px;color:darkred;">' . $row->NOCAMION . '</label></td>';
        if ($HaveInfo == 1) {
          //$hrescam = $this->db->escape($row->HrsTrav);
          //$sql = "SELECT TIMEDIFF($hrescam, $transport ) AS hretrans";
          //$hrestransport = $this->db->query($sql)->getRow(0);
          if ($row->isbroker == 1) {
            // Broker : aucune dépense sauf le dépotoire – remettre à 0
            $this->AllDayCamion->getItem($row->NOCAMION)->setCollecte(0);
            $this->AllDayCamion->getItem($row->NOCAMION)->setTransport(0);
            $this->AllDayCamion->getItem($row->NOCAMION)->setHreCam(0);
            $this->AllDayCamion->getItem($row->NOCAMION)->setCarburant(0);
            $this->AllDayCamion->getItem($row->NOCAMION)->setCoutCarburant(0);
            $this->AllDayCamion->getItem($row->NOCAMION)->setCoutBroker(floatval($row->montant_forfaitaire));
            $postBrokerCam = ' <a aria-label="Edit" title="Cliquer pour enregistrer le montant" onclick="postRHDayBroker(' . $row->idsanrh . ')">
                                                 <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
            $returnval .= '<td colspan="4" align="center"><label style="background-color:#FFA500;color:white;font-weight:bold;padding:4px 12px;border-radius:4px;">BROKER</label></td>
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
                    style="width:120px;text-align:center;font-size:14px;background-color:#E5E5E5;" value="' . $this->Tonnage . '" /></td>
          <td align="center"><label id="Revenu_' . $rowcam->id . '" class="form-control" style="width:120px;text-align:right;font-size:14px;background-color:yellow;font-weight:bold;">0.00</label></td>
          <td align="center"><label id="RevNet_' . $rowcam->id . '" class="form-control" style="width:120px;text-align:right;font-size:14px;background-color:yellow;font-weight:bold;">0.00</label></td>
          <td align="center">' . $postBrokerCam . ' <a aria-label="Supprimer" title="Supprimer les donn&eacute;es de test" onclick="deleteSanivanDay(\'' . $rowcam->NOCAMION . '\')"><i class="fa fa-trash btn btn-danger" style="font-size:16px;"></i></a></td>';
          } else {
          $this->AllDayCamion->getItem($row->NOCAMION)->setCarburant($row->Fuel);
          // Récupérer le prix du carburant depuis dictionprix
          $prixFuel = $this->getPrixFromDiction('Fuel', $dateactive);
          if ($prixFuel == 0) {
            // Fallback sur l'ancien système si aucun prix dans dictionprix
            $prixFuel = floatval($this->optSys->montantfuel);
          }
          $fuelcout = floatval($row->Fuel) * $prixFuel;
          $fuelcout = number_format((float) $fuelcout, 2, '.', '');
          $this->AllDayCamion->getItem($row->NOCAMION)->setCoutCarburant($fuelcout);
          $returnval .= '<td  align="center">
                                <div class="input-group input-group-sm" style="width:130px;">

                                <input class="form-control btn-info" type="text" id="KiloDeb_' . $rowcam->id . '" style="width:120px;text-align: right;font-size:14px" value="' . number_format($row->KiloDeb, 2) . '" />
                                <span class="input-group-btn">
                                <label class="form-control" style="height:30px;" >KM</label>
                                </span></div>
                                </td>
                                <td  align="center">
                                <div class="input-group input-group-sm" style="width:130px;">

                                <input class="form-control ' . (($row->KiloFin == 0) ? $errclass : 'btn-info') . '" id="KiloFin_' . $rowcam->id . '" type="text" style="width:120px;text-align: right;font-size:14px" value="' . number_format($row->KiloFin, 2) . '" />
                                <span class="input-group-btn">
                                <label class="form-control" style="height:30px;" >KM</label>
                                </span></div>
                                </td>
                                <td  align="center">
                                <div class="input-group input-group-sm" style="width:125px;">
                                <label class="form-control ' . (($row->KiloFin == 0) ? $errclass : 'btn-primary') . '"  id="Km_' . $rowcam->id . '" style="width:80px;text-align: right;font-size:14px">' . number_format($row->KM, 2) . '</label>
                                <span class="input-group-btn">
                                <label class="form-control" style="height:30px;" >KM</label>
                                </span></div>
                                </td>
                                <td  align="center">
                                <div class="input-group input-group-sm" style="width:118px;">

                                <input class="form-control btn-info" type="text" id="Fuel_' . $rowcam->id . '" style="width:80px;text-align: right;font-size:14px" value="' . number_format($row->Fuel, 2) . '" />
                                <span class="input-group-btn">
                                <label class="form-control" style="height:30px;" >Li</label>
                                </span></div>
                                </td>
                                <td  align="center">
                                <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-info') . '" type="time" id="HreDeb_' . $rowcam->id . '" style="width:120px;text-align: center;font-size:14px" value="' . $row->HreDeb . '" />
                                </td>
                                <td  align="center">
                                <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-info') . '" type="time" id="HreFin_' . $rowcam->id . '" style="width:120px;text-align: center;font-size:14px" value="' . $row->HreFin . '" />
                                </td>
                                <td  align="center">
                                <input class="form-control ' . ((trim($row->HreFin) == '') ? $errclass : 'btn-primary') . '" type="time" id="HrsTrav_' . $rowcam->id . '" style="width:120px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" />
                                </td>
                                <td>
<input class="form-control btn-warning" type="text" id="Transp_' . $rowcam->id . '" style="width:120px;text-align: center;font-size:14px" value="' . $this->Transport . '"  /></td>
                                <td >
<input class="form-control btn-success" type="text" id="Collecte_' . $rowcam->id . '" style="width:120px;text-align: center;font-size:14px" value="' . $this->collecte . '"  /></td>
								<td>
<input class="form-control" type="text" id="Dispo_' . $rowcam->id . '" style="width:120px;text-align: center;font-size:14px;background-color: #E5E5E5;" value="' . $this->Tonnage . '"  /></td>
								<td align="center"><label id="Revenu_' . $rowcam->id . '" class="form-control" style="width:120px;text-align:right;font-size:14px;background-color:yellow;font-weight:bold;">0.00</label></td>
								<td align="center"><label id="RevNet_' . $rowcam->id . '" class="form-control" style="width:120px;text-align:right;font-size:14px;background-color:yellow;font-weight:bold;">0.00</label></td>
								<td>' . $test . ' <a aria-label="Supprimer" title="Supprimer les donn&eacute;es de test" onclick="deleteSanivanDay(\'' . $rowcam->NOCAMION . '\')"><i class="fa fa-trash btn btn-danger" style="font-size:16px;"></i></a></td>
							  </td>

													';
		  }
		  $replInfo = $this->getInfoReplace($cam, $dteactive);
          $returnval .= $replInfo;
          $HaveInfo++;
        } else {
          $returnval .= '<td colspan="13" align="center">Informations sur autres ligne du camion<label style="width:70px;color:darkred;">' . $row->NOCAMION . '</label> </td>';
        }
        $returnval .= '</tr>' . $this->getRHDay($dateactive, $trIter, $row);
        $revenuVal = number_format((float)$this->AllDayCamion->getItem($row->NOCAMION)->getRevenu(), 2, '.', '');
        $netVal = number_format((float)$this->AllDayCamion->getItem($row->NOCAMION)->getNet(), 2, '.', '');
        $returnval .= "<script>$('#Revenu_" . $rowcam->id . "').text('" . $revenuVal . "');$('#RevNet_" . $rowcam->id . "').text('" . $netVal . "');</script>";

        if (!$print) {
          $returnval .= " <script> $('#tr_" . $trIter . "').hide(); </script>";
        }
        $this->AllDayCamion->postItem($row->NOCAMION, $dateactive);
        $trIter++;
      }
      //-------------------------------------------------------
      
      //-------------------------------------------------------
    }
    //----------------------------------------------------------
    $this->data['rapdaychauf_cam'] = trim($returnval);
    $this->data['raprojoursani'] = $this->AllDayCamion->getDayReport($dateactive);
    return $this->data;
  }
  private function getInfoReplace($cam, $dteactive)
  {
    $returnval = '';
    $SQL = "SELECT DISTINCT
                  (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE reshumaine.id = cedules.idchauffeurs) AS Chauffeur,
                  camionjour.NOCAMION, camionjour.KiloDeb, camionjour.KiloFin, (camionjour.KiloFin - camionjour.KiloDeb) AS KM,
                  camionjour.Fuel,camionjour.HreDeb, camionjour.HreFin, 
                  LEFT( TIMEDIFF(camionjour.HreFin,camionjour.HreDeb), 5) as HrsTrav,
                  cedules.idchauffeurs, cedules.HELPERS_1, cedules.HELPERS_2, cedules.HELPERS_3, 
                  cedules.HELPERS_4,camionjour.id AS idcam,cedules.ID, cedules.remplace_cam,
                    (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) as NomVille,
                    IFNULL((SELECT sanivan_rh.idcedule FROM sanivan_rh WHERE 
                              sanivan_rh.idcedule = cedules.ID limit 0,1), 0) as HaveRH,
                    (SELECT LEFT( TIMEDIFF(info_villes.HreFin,info_villes.HreDeb), 5) FROM info_villes 
                              WHERE info_villes.idcedule = cedules.ID limit 0, 1) AS HresVille
	                    FROM cedules INNER JOIN info_villes ON cedules.ID = info_villes.idcedule
							    INNER JOIN camionjour ON cedules.remplace_cam = camionjour.NOCAMION
			              WHERE cedules.DateJour = $dteactive AND camionjour.DateJour = $dteactive AND cedules.NOCAMION = $cam
                       ORDER BY cedules.NOCAMION, cedules.ID LIMIT 0,1";
    $result = $this->db->query($SQL);

    //-------------------------------------------------------
    foreach ($result->getResult() as $rowremplaced) {

      $to_time = strtotime($rowremplaced->HreFin);
      $from_time = strtotime($rowremplaced->HreDeb);
      $minutes = round(abs($to_time - $from_time) / 60, 2);
      $transCam = round($minutes / 60, 2);
      $hrescam = $transCam;
      $this->AllDayCamion->addItem(new CamDay(
        $transCam,
        0,
        $hrescam,
        $rowremplaced->remplace_cam,
        str_replace("'", "",$dteactive )
      ), $rowremplaced->remplace_cam);
      //---------------------------------------------
      $test = "<a style='font-size:16px;color: #03F;cursor:pointer;width: 50px;'
                                  onclick='DayCamChauf(".$rowremplaced->idcam.")' title='Enregistrer'><i class='fa fa-check btn btn-success' style='font-size:16px;'></i></a>";
      $errclass = ' btn-danger ';
      $returnval .= '</tr><tr id="trcam_' . $rowremplaced->idcam . '" style="background-color:papayawhip">
                          <td colspan="2" style="background-color:white"></td>
                              <td align="center"><label  style="width:70px;color:darkred;">' . $rowremplaced->remplace_cam . '</label></td>';
      
      $this->AllDayCamion->getItem($rowremplaced->remplace_cam)->setCarburant($rowremplaced->Fuel);
      $fuelcout = floatval($rowremplaced->Fuel) * floatval($this->optSys->montantfuel);
      $fuelcout = number_format((float) $fuelcout, 2, '.', '');
      $this->AllDayCamion->getItem($rowremplaced->remplace_cam)->setCoutCarburant($fuelcout);
      $returnval .= '<td  align="center">
                              <div class="input-group input-group-sm" style="width:130px;">

                              <input class="form-control btn-info" type="text" id="KiloDeb_' . $rowremplaced->idcam . '" style="width:120px;text-align: right;font-size:14px" value="' . number_format($rowremplaced->KiloDeb, 2) . '" />
                              <span class="input-group-btn">
                              <label class="form-control" style="height:30px;" >KM</label>
                              </span></div>
                              </td>
                              <td  align="center">
                              <div class="input-group input-group-sm" style="width:130px;">

                              <input class="form-control ' . (($rowremplaced->KiloFin == 0) ? $errclass : 'btn-info') . '" id="KiloFin_' . $rowremplaced->idcam . '" type="text" style="width:120px;text-align: right;font-size:14px" value="' . number_format($rowremplaced->KiloFin, 2) . '" />
                              <span class="input-group-btn">
                              <label class="form-control" style="height:30px;" >KM</label>
                              </span></div>
                              </td>
                              <td  align="center">
                              <div class="input-group input-group-sm" style="width:125px;">
                              <label class="form-control ' . (($rowremplaced->KiloFin == 0) ? $errclass : 'btn-primary') . '"  id="Km_' . $rowremplaced->idcam . '" style="width:80px;text-align: right;font-size:14px">' . number_format($rowremplaced->KM, 2) . '</label>
                              <span class="input-group-btn">
                              <label class="form-control" style="height:30px;" >KM</label>
                              </span></div>
                              </td>
                              <td  align="center">
                              <div class="input-group input-group-sm" style="width:118px;">

                              <input class="form-control btn-info" type="text" id="Fuel_' . $rowremplaced->idcam . '" style="width:80px;text-align: right;font-size:14px" value="' . number_format($rowremplaced->Fuel, 2) . '" />
                              <span class="input-group-btn">
                              <label class="form-control" style="height:30px;" >Li</label>
                              </span></div>
                              </td>
                              <td  align="center">
                              <input class="form-control ' . ((trim($rowremplaced->HreFin) == '') ? $errclass : 'btn-info') . '" type="time" id="HreDeb_' . $rowremplaced->idcam . '" style="width:120px;text-align: center;font-size:14px" value="' . $rowremplaced->HreDeb . '" />
                              </td>
                              <td  align="center">
                              <input class="form-control ' . ((trim($rowremplaced->HreFin) == '') ? $errclass : 'btn-info') . '" type="time" id="HreFin_' . $rowremplaced->idcam . '" style="width:120px;text-align: center;font-size:14px" value="' . $rowremplaced->HreFin . '" />
                              </td>
                              <td  align="center">
                              <input class="form-control ' . ((trim($rowremplaced->HreFin) == '') ? $errclass : 'btn-primary') . '" type="time" id="HrsTrav_' . $rowremplaced->idcam . '" style="width:120px;text-align: center;font-size:14px" value="' . $rowremplaced->HrsTrav . '" />
                              </td> <td align="center">' . $test . '</td>
                              <td colspan="5" style="background-color:white"></td>
                              
                            

												  ';


    }
    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param string $camion
   * @param int $idcedule
   *
   * @return string
   */
  public function getVilleDay($dateactive, $camion, $idcedule)
  {
    $iter = 0;
    $idville = '';
    $tonnage = 0;
    $nbBac = 0;
    $depot = '';
    $nocamion = '';
    $returnval = '';
    $get = $this->input->post_get('titre', TRUE);
    $print = 'none';
    if (isset($get)) {
      $print = 'block';
    }
    /** @var string $returnval */
    $dteactive = $this->db->escape($dateactive);

    $span = '<label style="color:black;font-size:large;">';
    $strSQL = "SELECT (SELECT grp_cedules.ARow FROM grp_cedules WHERE grp_cedules.id = cedules.idgrp) AS aRow,
info_villes.id, idville, idcedule, HreDeb, HreFin, nbBac,cedules.TypeDeProduit,
(SELECT ville.Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille,
 LEFT( TIMEDIFF(info_villes.HreFin,info_villes.HreDeb), 5) as HrsTrav,cedules.NOCAMION,
 (SELECT Tonnage FROM info_depotoire WHERE info_depotoire.idinfoville = info_villes.id) AS Tonnage,
 (SELECT Depotoire FROM info_depotoire WHERE info_depotoire.idinfoville = info_villes.id) AS Depotoire
	FROM info_villes INNER JOIN cedules ON info_villes.idcedule = cedules.ID WHERE  cedules.DateJour = $dteactive
	AND cedules.NOCAMION = $camion  ORDER BY info_villes.id";
    $sanivan_rh = $this->db->query($strSQL);

    $revenuTotal = 0;
    $transport = "";
    foreach ($sanivan_rh->getResult() as $row) {
      $villeTonnage = 0;
      if (is_numeric(str_replace(',', '.', $row->Tonnage))) {
        $villeTonnage = floatval(str_replace(',', '.', $row->Tonnage));
        $tonnage += $villeTonnage;
      }
      $villeNbBac = 0;
      if (is_numeric($row->nbBac)) {
        $villeNbBac = intval($row->nbBac);
        $nbBac += $villeNbBac;
      }
      // Calcul du revenu par ville depuis revenuville
      $revVille = $this->db->query("SELECT * FROM revenuville WHERE idville = " . intval($row->idville))->getRow(0);
      if (isset($revVille) && ($revVille->MontFixe > 0 || $revVille->Disposition > 0 || $revVille->MontRedevance > 0 || $revVille->MontTonne > 0 || $revVille->inHour > 0 || $revVille->AuBac > 0)) {
        $camDayItem = $this->AllDayCamion->getItem($row->NOCAMION);
        $hresCamVal = ($camDayItem != null) ? floatval($camDayItem->getHresCam()) : 0;
        $revenuTotal += floatval($revVille->MontFixe)
            + (floatval($revVille->Disposition) * $villeTonnage)
            + (floatval($revVille->MontRedevance) * $villeTonnage)
            + (floatval($revVille->MontTonne) * $villeTonnage)
            + (floatval($revVille->inHour) * $hresCamVal)
            + (floatval($revVille->AuBac) * $villeNbBac);
      }
      if ($sanivan_rh->getNumRows() > 1) {

      }
      $dep = $this->db->escape($row->Depotoire);
      $nocamion = $row->NOCAMION;
      $depot = $this->db->query("SELECT prix_sani FROM depotoire WHERE nom = $dep")->getRow(0);
      $returnval .= '<tr  class="info"  style="text-align:right;"  ><td colspan="13" >
                      <table class="table table-bordered">
		                <thead>';

      $returnval .= '<tr><th colspan="6"><label class="titrenb">COLLECTE / ' . $row->TypeDeProduit . '</label></th> </tr>

                      <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                        <th style="text-align:center">VILLE</th>
                        <th style="text-align:center">HRE DEB</th>
                        <th style="text-align:center">HRE FIN</th>
                        <th style="text-align:center">NB BACS</th>
                        <th style="text-align:center">HRES TRAV.</th>
                        <th></th>
		              </tr>
					  </thead>
					  <tbody >';
      /** @var string $post */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postVilleDay(' . $row->id . ')">
										           <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
      $returnval .= '<tr id="trligne_' . $row->id . '">

                            <td align="center"><label  class="titreville">' . $row->NomVille . '</label></td>
                            <td align="center">
								<input class="form-control" style="width: 100px;font-size:14px;" type="time"
                                    id="HreDeb_' . $row->id . '" name="HreDeb_' . $row->id . '"
                                    value="' . $row->HreDeb . '" />
							</td>
                            <td align="center">
								<input class="form-control" style="width: 100px;font-size:14px;" type="time"
                                    id="HreFin_' . $row->id . '" name="HreFin_' . $row->id . '"
                                    value="' . $row->HreFin . '" /></td>
                            <td align="center">
								<input class="form-control" style="width: 100px;font-size:14px;" type="text"
                                    id="nbBac_' . $row->id . '" name="nbBac_' . $row->id . '"
                                    value="' . $row->nbBac . '" />
							</td>';
      $returnval .= '<td  align="center">
                            <input class="form-control" type="time" id="HrsTrav_' . $row->id . '" style="width:100px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" />
                            </td>
              <td>' . $post . '</td></tr><tr><td>' . $this->getDepotoireDay($dateactive, $row->id) . '</td></tr> </tbody></table></td></tr>';
      $iter++;
    }
    $coutDispot = 0;
    if (is_object($depot) && isset($depot->prix_sani)) {
      $coutDispot = round($tonnage * $depot->prix_sani, 2);
    }

    try {
        if ($this->AllDayCamion->getItem($nocamion) != null) {
            $this->AllDayCamion->getItem($nocamion)->setDisposition($tonnage);
            $this->AllDayCamion->getItem($nocamion)->setCoutDispot($coutDispot);
            $this->AllDayCamion->getItem($nocamion)->setNbBac($nbBac);
            // Revenu
            $revenuTotal = round($revenuTotal, 2);
            $this->AllDayCamion->getItem($nocamion)->setRevenu($revenuTotal);
            // Revenu net = Revenu - dépenses
            $depenses = floatval($this->AllDayCamion->getItem($nocamion)->getCoutChauf())
                + floatval($this->AllDayCamion->getItem($nocamion)->getCoutAide())
                + floatval($this->AllDayCamion->getItem($nocamion)->getCoutCarburant())
                + floatval($coutDispot)
                + floatval($this->AllDayCamion->getItem($nocamion)->getCoutBroker());
            $netRevenu = round($revenuTotal - $depenses, 2);
            $this->AllDayCamion->getItem($nocamion)->setNet($netRevenu);
            // Net par heure
            $hresCamTot = floatval($this->AllDayCamion->getItem($nocamion)->getHresCam());
            $netByHres = ($hresCamTot > 0) ? round($netRevenu / $hresCamTot, 2) : 0;
            $this->AllDayCamion->getItem($nocamion)->setNetByHres($netByHres);
        }
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage();
    } finally {
        //echo "This part is always executed.";
    }
    return $returnval;
  }
  /**
   *
   * @param string $dateactive
   * @param string $idinfoville
   *
   * @return string
   */
  public function getDepotoireDay($dateactive, $idinfoville)
  {
    $get = $this->input->post_get('titre', TRUE);
    $print = 'none';
    if (isset($get)) {
      $print = 'block';
    }
    /** @var string $returnval */
    $dteactive = $this->db->escape($dateactive);

    $span = '<label style="color:black;font-size:large;">';
	$strSQL = "SELECT info_depotoire.id, info_depotoire.idinfoville, info_depotoire.IsTransBo, info_depotoire.Ticket,
info_depotoire.Tonnage, info_depotoire.debut, info_depotoire.Depotoire, info_depotoire.fin, info_depotoire.boite,
LEFT( TIMEDIFF(info_depotoire.fin,info_depotoire.debut), 5) as HrsTrav,
(SELECT pt.pdf_path FROM pdf_tickets pt
  WHERE pt.nocamion = cedules.NOCAMION
	AND pt.id_ville = cedules.idvilles
	AND DATE(pt.created_at) = cedules.DateJour
	AND cedules.DateJour >= '2026-04-08'
  ORDER BY pt.created_at DESC LIMIT 1) AS pdf_ticket_path
	FROM info_depotoire INNER JOIN info_villes ON  info_depotoire.idinfoville = info_villes.id
	INNER JOIN cedules ON info_villes.idcedule = cedules.ID
	WHERE info_depotoire.idinfoville = $idinfoville";
    $sanivan_rh = $this->db->query($strSQL);
    $returnval = '<tr style="text-align:right;"  > <td colspan="9" ><table class="table table-bordered">
		                <thead><tr><th colspan="11"><label class="titrenb">DISPOSITION</label></th> </tr>';
    $returnval .= '

                      <tr  style="text-align:right;color:darkblue;font-weight:bold;">
                        <th style="text-align:center">D&Eacute;POTOIRE</th>
                        <th style="text-align:center">BILLET</th>
                        <th style="text-align:center">TONNAGE</th>
                        <th style="text-align:center">HRE FIN</th>
                        <th style="text-align:center">TRANSBO</th>
                        <th style="text-align:center">Bo&icirc;te</th>
                        <th></th>
		              </tr>
					  </thead>
					  <tbody >';

    foreach ($sanivan_rh->getResult() as $row) {
      /** @var string $post */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postDepDay(' . $row->id . ')">
										           <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
      $checked = ($row->IsTransBo == 1) ? ' checked ' : '';
      $boite = ($row->boite == 'none') ? '' : $row->boite;
	  $ticket = 'Aucun';
	  $pdfPath = isset($row->pdf_ticket_path) ? trim($row->pdf_ticket_path) : '';
	  if ($pdfPath != '') {
		$ticket = '<span class="input-group-btn">
						  <a href="' . 'https://robert-daoust.ca/'.$pdfPath . '" target="_blank" style="font-size:16px;">
										<i class="fa fa-file-pdf-o btn btn-danger" style="font-size:16px;height:30px;">
								  </i>&nbsp;&nbsp;Voir le BILLET (PDF)</a>
						  </span>';
	  } else if (trim($row->Ticket) != '') {
		$ticket = '<span class="input-group-btn">
						  <a href="' . 'https://robert-daoust.ca/'.$row->Ticket . '" target="_blank" style="font-size:16px;" download>
										<i class="fa fa-download btn btn-success" style="font-size:16px;height:30px;">
								  </i>&nbsp;&nbsp;Voir le BILLET</a>
						  </span>';
	  } else {
		$ticket = '<input class="form-control" style="width:450px;"
							  name="edtAddTikect" id="edtAddTikect" type="file" />';
	  }
      $returnval .= '<tr id="trligne_' . $row->id . '">

                            <td align="center"><select class="form-control form-control-modern" id="Depotoire_' . $row->id . '" style="width:305px;color:red;font-weight:bold;height:47px;">
                              <option value="" >Choisir</option>
                              ' . $this->cedules->GetAllDepotoires($row->Depotoire) . '
                            </select></td>
                            <td align="center">' . $ticket . '</td>
                            <td align="center">
								<input class="form-control" style="width: 90px;font-size:14px;" type="text"
                                    id="Tonnage_' . $row->id . '" name="Tonnage_' . $row->id . '"
                                    value="' . $row->Tonnage . '" /></td>

                            <td align="center">
								<input class="form-control" style="width: 100px;font-size:14px;" type="time"
                                    id="fin_' . $row->id . '" name="fin_' . $row->id . '"
                                    value="' . $row->fin . '" /></td>
                            <td align="center">
                            <input type="checkbox" class="checkbox-custom checkbox-primary" id="chkTransbo_' . $row->id . '" ' . $checked . ' style="width:30px;height:30px;margin-left:0px"/>							</td>
                        <td align="center">

                            <input type="text" class="form-control form-control-modern" id="Boite_' . $row->id . '" value="' . $boite . '" style="width:90px; background-color: yellow; font-weight: bold;" />
							</td>';
      $returnval .= '<td>' . $post . '</td></tr>';
    }
    return $returnval . ' </tbody></table> </td></tr>';
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
    foreach ($rolloffday->getResult() as $row) {
      /** @var string $edit */
      $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer onclick="postRHDay(\'' . $row->id . '\')">
										           <i class="fa fa-edit btn btn-success" style="font-size:16px;"></i></a>';
      $returnval .= '<tr>

                            <td align="left"><label style="width:190px">' . $row->Chauffeur . '</label></td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="' . $row->StartTime . '" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="' . $row->EndTime . '" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="' . $row->dinerdeb . '" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="' . $row->dinerfin . '" />
                            </td>
                            <td  align="center">
                            <input class="form-control" type="time" style="width:120px;text-align: center;" value="' . $row->hres . '" />
                            </td>
                            <td align="center"><label style="width:70px">' . $row->NOCAMION . '</label></td>
                            <td>&nbsp;&nbsp;' . $post . '&nbsp;&nbsp;</td>
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
  public function RapSaniDay($dateactive, $from)
  {
    /** @var string $returnval */
    $returnval = '';
    $dateactive = $this->db->escape($dateactive);
    $strWhere = ($from == 1) ? ' and  idrh > 0 ' : '';
    $strSQL = "SELECT datejour, nocamion, KiloDeb, KIloFin, Fuel, KM, StartTime, EndTime, HresTrav,
          PrixFuel, RevenuBrut, Enfouissement, PrixChauf,
        (LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf AS CoutCH,
        (Fuel * PrixFuel) AS CoutFuel,
        (RevenuBrut -(((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) +
	        (Fuel * PrixFuel) + Enfouissement)) AS RevenuNet,
        (RevenuBrut -(((LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) * PrixChauf) +
	        (Fuel * PrixFuel) + Enfouissement))/ (LEFT(HresTrav, 2) + (RIGHT(HresTrav, 2) / 60 )) AS MoyenneHR FROM rapprojour where datejour = $dateactive $strWhere";
    $rap = $this->db->query($strSQL);
    foreach ($rap->getResult() as $row) {
      $returnval .= '<tr>
                            <td></td>
                            <td align="center"><label style="width:80px;text-align:center;width:100%">' . $row->nocamion . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->KiloDeb, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->KIloFin, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->KM, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->RevenuBrut, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->Fuel, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->CoutFuel, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:center;width:100%">' . $row->HresTrav . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->CoutCH, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->Enfouissement, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->RevenuNet, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%">' . number_format($row->MoyenneHR, 2) . '</label></td> <td></td>
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
    if (isset($totalrap)) {
      $returnval .= '<tr>
                            <td colspan="4" align="center"><label style="width:80px;text-align:center;width:100%;font-size:large;font-weight: bold;color: darkred">TOTAL</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->KM, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->RevenuBrut, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->Fuel, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->CoutFuel, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:center;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->smHres, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->CoutCH, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->Enfouissement, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->RevenuNet, 2) . '</label></td>
                            <td align="center"><label style="width:80px;text-align:right;width:100%;font-size:large;font-weight: bold;color: darkred">' . number_format($totalrap->MoyenneHR, 2) . '</label></td>
                            <td></td>
											  </tr>';
    }
    return $returnval;
  }
  /**
   *
   * @param string $cam
   *
   * @return string
   */
  public function GetMaxKiloCamions($camion)
  {
    $returnval = "";
    $cam = $this->db->escape($camion);
    $query = "select IFNULL( IF(( MAX(KiloFin) is null ) OR (MAX( KiloFin) < MAX(KiloDeb) ),
															MAX( KiloDeb ), MAX( KiloFin ) ), 0) as KiloDeb, 0 AS KiloFin, 0 AS Fuel
													 from camionjour where NOCAMION = $cam ";

    $result = $this->db->query($query)->getResult();
    foreach ($result as $row) {
      $returnval = $row;
      $KiloDeb = $row->KiloDeb;
      $this->PostCamionJour($camion, 0, $KiloDeb, 0);
    }
    return $returnval;
  }
  public function GetSuivisChauffeur($idchauffeur, $camion)
  {
    $returnval = '';
    $idrh = $this->db->escape($idchauffeur);
    $query = "select StartTime, EndTime, NOCAMION from suivis where
                                DateJour = DATE_FORMAT( CURDATE(), '%Y-%m-%d' ) and idrh = $idrh ";
    $result = $this->db->query($query)->getResult();
    if (isset($result)) {
      foreach ($result as $row) {
        $returnval = json_encode($row);
      }
      if (empty($returnval)) {
        $query = "SELECT TIME_FORMAT(CURTIME(), '%H:%i') as StartTime, '00:00' as EndTime,'' as NOCAMION, 'OK' as status";
        $result = $this->db->query($query)->getResult();
        foreach ($result as $row) {
          $vide = '';
          $this->PostSuivisChauffeurs($camion, $idchauffeur, $vide, $vide);
          $returnval = json_encode($row);
        }
      }
    }
    return $returnval;
  }
  public function PostCamionJour()
  {
    $returnval = '';
    $idcam = $this->db->escape($this->input->post_get('idcam', TRUE));
    $KiloDeb = $this->db->escape(str_replace(',', '', $this->input->post_get('KiloDeb', TRUE)));
    $KiloFin = $this->db->escape(str_replace(',', '', $this->input->post_get('KiloFin', TRUE)));
    $fuel = $this->db->escape($this->input->post_get('Fuel', TRUE));
    $HreDeb = $this->db->escape($this->input->post_get('HreDeb', TRUE));
    $HreFin = $this->db->escape($this->input->post_get('HreFin', TRUE));

    $query = "UPDATE camionjour
									SET
                  KiloDeb=$KiloDeb,
									KiloFin=$KiloFin,
									Fuel= $fuel,
		              HreDeb=$HreDeb,
		              HreFin=$HreFin
									WHERE id = $idcam";
    $this->db->query($query);
    $returnval = "OK";
    return $returnval;
  }
  public function postRHDay()
  {
	$returnval = '';
	$idrh = $this->db->escape($this->input->post_get('idrh', TRUE));
	$TypeExtra = $this->db->escape($this->input->post_get('TypeExtra', TRUE));
	$HreDeb = $this->db->escape($this->input->post_get('HreDeb', TRUE));
	$HreFin = $this->db->escape($this->input->post_get('HreFin', TRUE));
	$HreDeb_extra = $this->db->escape($this->input->post_get('HreDeb_extra', TRUE));
	$HreFin_extra = $this->db->escape($this->input->post_get('HreFin_extra', TRUE));
	$query = "UPDATE sanivan_rh
									SET
					  HreDeb=$HreDeb,
					  HreFin=$HreFin,
					  HreDeb_extra=$HreDeb_extra,
					  HreFin_extra=$HreFin_extra,
					  TypeExtra=$TypeExtra
									WHERE id = $idrh";
	$this->db->query($query);
	$returnval = "OK";
	//------------------------------------------------------
	return $returnval;
  }
  public function postRHDayBroker()
  {
	$returnval = '';
	$idrh = $this->db->escape($this->input->post_get('idrh', TRUE));
	$montant = $this->db->escape($this->input->post_get('montant', TRUE));
	$query = "UPDATE sanivan_rh
									SET
					  montant_forfaitaire=$montant
									WHERE id = $idrh";
	$this->db->query($query);
	$returnval = "OK";
	return $returnval;
  }
  public function postDepDay()
  {
    $returnval = '';
    $iddeb = $this->db->escape($this->input->post_get('iddeb', TRUE));
    $isTransbo = $this->db->escape($this->input->post_get('istransbo', TRUE));
    $Depotoire = $this->db->escape($this->input->post_get('Depotoire', TRUE));
    $Tonnage = $this->db->escape(str_replace(',', '', $this->input->post_get('Tonnage', TRUE)));
    $boite = $this->db->escape($this->input->post_get('boite', TRUE));
    $debut = $this->db->escape($this->input->post_get('debut', TRUE));
    $fin = $this->db->escape($this->input->post_get('fin', TRUE));
    $query = "UPDATE info_depotoire
									SET
		     		        IsTransBo=$isTransbo,
		     		        Depotoire=$Depotoire,
		                Tonnage=$Tonnage,
		                debut=$debut,
		                fin=$fin,
		                boite=$boite
									WHERE id = $iddeb";
    $this->db->query($query);
    $returnval = "OK";
    //------------------------------------------------------
    return $returnval;
  }
  public function postVilleDay()
  {
    $returnval = '';
    $id = $this->db->escape($this->input->post_get('id', TRUE));
    $nbBac = $this->db->escape($this->input->post_get('nbBac', TRUE));
    $HreDeb = $this->db->escape($this->input->post_get('HreDeb', TRUE));
    $HreFin = $this->db->escape($this->input->post_get('HreFin', TRUE));
    $query = "UPDATE info_villes
									SET
		              HreDeb=$HreDeb,
		              HreFin=$HreFin,
		              nbBac=$nbBac
									WHERE id = $id";
    $this->db->query($query);
    $returnval = "OK";
    //------------------------------------------------------
    return $returnval;
  }
  public function deleteSanivanDay()
  {
    $nocamion = $this->db->escape($this->input->post_get('nocamion', TRUE));
    $date = $this->db->escape($this->input->post_get('date', TRUE));
    $query = "DELETE FROM camionjour WHERE NOCAMION = $nocamion AND DateJour = $date";
    $this->db->query($query);
    $query = "DELETE FROM sanivanday WHERE NOCAMION = $nocamion AND DateJour = $date";
    $this->db->query($query);
    $query = "DELETE FROM cedules WHERE NOCAMION = $nocamion AND DateJour = $date";
    $this->db->query($query);
    return "OK";
  }
  /**
   *
   * @param int $value
   * @param string $field
   *
   * @return void
   */
  public function postValCam($value, $field)
  {
    /** @var string $camion */
    $camion = $this->db->escape($this->sess('camion'));
    $value = str_replace(',', '.', $value);
    switch ($field) {
      case 'KiloDeb':
      case 'KiloFin':
      case 'Fuel':
      case 'HreDeb_cam':
      case 'HreFin_cam':
        if (($field == 'HreDeb_cam') or ($field == 'HreFin_cam')) {
          $field = substr($field, 0, 6);
        }
        $value = $this->db->escape($value);
        $strSQL = "UPDATE camionjour
	              SET
		              $field=$value
	              WHERE NOCAMION = $camion and DateJour=CURDATE()";
        $this->db->query($strSQL);
        break;
      case 'HreDeb': //HreDeb, HreFin, HreDeb_extra, HreFin_extra
      case 'HreFin':
      case 'HreDeb_extra':
      case 'HreFin_extra':
        $value = $this->db->escape($value);
        $idrh = $this->db->escape($this->sess('user_id'));
        $strSQL = "UPDATE sanivan_rh
	              SET
		              $field=$value
	              WHERE idrh = $idrh and DateJour=CURDATE() AND TypeRh = 0";
        $this->db->query($strSQL);
        break;
      case 'HreDeb_1':
      case 'HreDeb_2':
      case 'HreDeb_3':
      case 'HreDeb_4':
      case 'HreFin_1':
      case 'HreFin_2':
      case 'HreFin_3':
      case 'HreFin_4':
        $value = $this->db->escape($value);
        $idrh = $this->db->escape($this->input->post_get('idaide'));
        $field = substr($field, 0, 6);
        //-------------------------------------------
        $strSQL = "UPDATE sanivan_rh
	              SET
		              $field=$value
	              WHERE idrh = $idrh and DateJour=CURDATE() AND TypeRh = 1";
        $this->db->query($strSQL);
        break;
      case 'HreDeb_' . $this->input->post_get('idinfoville'):
      case 'HreFin_' . $this->input->post_get('idinfoville'):
      case 'nbBac_' . $this->input->post_get('idinfoville'):
        $value = $this->db->escape($value);
        $arrField = explode('_', $field);
        $field = $arrField[0];
        $strSQL = "UPDATE info_villes
	                  SET
		                  $field=$value
	                  WHERE id = " . $this->db->escape($this->input->post_get('idinfoville'));
        $this->db->query($strSQL);
        break;
    }
  }
  //-----------------------------------------------------  RAPPORT FIN
  function __destruct()
  {
    $this->db->close();
  }
}