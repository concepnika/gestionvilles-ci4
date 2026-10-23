<?php

namespace App\Models;

/**
 * Port mecanique de ExcelVille_model (CI3) â€” requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */


require_once APPPATH . '/libraries/PHPExcel.php';
//---------------------------- //  REQUÉRANT	REQUÊTE	CÉDULÉE	TITRE	ADRESSE	DESCRIPTION	STATUT /MAT.	DATE
const REQUERANT         = 'A';
const REQUETE           = 'B';
const CEDULEE           = 'C';
const TITRE             = 'D';
const ADRESSE           = 'E';
const DESCRIPTION       = 'F';
const STATUT_MAT        = 'G';
const DATE_CREE          = 'H';
//---------------------------
const REQUERANT_REP          = 'B';
const CEDULEE_REP            = 'C';
const DESCRIPTION_REP        = 'D';
const ADRESSE_REP            = 'E';
const STATUT_MAT_REP         = 'F';
const DATE_CREE_REP          = 'G';
//---------------------------
/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class ExcelVilleModel extends Ci3Model
{
  /** @var array $data */
	protected $data = array();
  /** @var array $table */
	protected $table = array('typecamion', 'typerh', 'typeabsence', 'typededechet',
                          'statutcamion', 'pertetemps', 'depotoire', 'emplacements', 'dictionprix');
  protected $JourDeSemaine  = array
              ( 'DIM', 'LUN', 'MAR',
                          'MER', 'JEU', 'VEN', 'SAM' ) ;
  protected $Alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
                'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AZ', 'AS',
                'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
  //----------------------------
	function __construct() {
		parent::__construct();
		
		
		
		
	}
  private function getLogo($objPHPExcel,$row)
  {
    //-------------------------------------------------------------------- Logo
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('test_img');
    $objDrawing->setDescription('test_img');
    $objDrawing->setPath('./assets/img/logo.jpg');
    $objDrawing->setCoordinates('A'. $row);
    //setOffsetX works properly
    $objDrawing->setOffsetX(10);
    $objDrawing->setOffsetY(3);
    //set width, height
    //$objDrawing->setWidth(120);
    $objDrawing->setHeight(28);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
  }
  private function getLogoREP($objPHPExcel,$row)
  {
    //-------------------------------------------------------------------- Logo
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('test_img');
    $objDrawing->setDescription('test_img');
    $objDrawing->setPath('./assets/img/logo.jpg');
    $objDrawing->setCoordinates('B'. $row);
    //setOffsetX works properly
    $objDrawing->setOffsetX(10);
    $objDrawing->setOffsetY(3);
    //set width, height
    //$objDrawing->setWidth(120);
    $objDrawing->setHeight(28);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
  }
  public function getReponse($idrequete, $objPHPExcel, &$iterRow)
  {
    //--------------------------------------------------------------------
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
    $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(trim(dteCedule), 10) as dteCedule,
							  (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
                (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
                Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
										  createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue, statut,
                noSerie, couleur, grandeur, matiere
							  FROM reponses WHERE idrequete = $idrequete
											  AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
    /** @var object $reponse */
    $reponse = $this->db->query($strSQL);
    //--------------------------------------------------------------------------
    $objPHPExcel->getActiveSheet()->mergeCells(REQUERANT_REP. $iterRow . ":". DATE_CREE_REP . $iterRow);
    $objPHPExcel->getActiveSheet()->setCellValue(REQUERANT_REP. $iterRow, 'DISCUSTIONS');
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $iterRow++;
//    const REQUERANT_REP          = 'B';
//const DESCRIPTION_REP        = 'C';
//const ADRESSE_REP            = 'E';
//const STATUT_MAT_REP         = 'G';
//const DATE_CREE_REP          = 'H';
    $objPHPExcel->getActiveSheet()->setCellValue(REQUERANT_REP . $iterRow, '');
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getAlignment()->setHorizontal
      (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $objPHPExcel->getActiveSheet()->setCellValue(CEDULEE_REP . $iterRow, utf8_encode('CÉDULÉE LE'));
    $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getAlignment()->setHorizontal
    (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $objPHPExcel->getActiveSheet()->setCellValue(DESCRIPTION_REP . $iterRow, 'DESCRIPTION');
    $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getAlignment()->setHorizontal
    (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $objPHPExcel->getActiveSheet()->setCellValue(ADRESSE_REP . $iterRow, utf8_encode('ADRESSE / BAC / MATIÈRE'));
    $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getAlignment()->setHorizontal
    (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $objPHPExcel->getActiveSheet()->setCellValue(STATUT_MAT_REP . $iterRow, 'STATUT');
    $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getAlignment()->setHorizontal
    (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $objPHPExcel->getActiveSheet()->setCellValue(DATE_CREE_REP . $iterRow, 'DATE');
    $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getAlignment()->setHorizontal
    (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getFont()->getColor()->setRGB("ffffff");
    $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getFill()->setFillType(
    PHPExcel_Style_Fill::FILL_SOLID
    )->getStartColor()->setARGB('008000');
    $iterRow++;
    /** @var object $row */
    foreach ($reponse->getResult() as $row) {
      //-----------------------------------------------------

      //---------------------------------------------------------------------------------
      if ($row->IsFrom == 0) {
        $objPHPExcel->getActiveSheet()->setCellValue(REQUERANT_REP . $iterRow, $row->NomVille);
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->getColor()->setRGB("FF3C11");
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow)->getFont()->setName("Arial Black");

      } else {
        $this->getLogoREP($objPHPExcel, $iterRow);
      }
      //----------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(CEDULEE_REP . $iterRow, $row->dteCedule);
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getFont()->getColor()->setRGB("f1a905");
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getFont()->setSize(16);
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getFill()->setFillType(
      PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE_REP . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(DESCRIPTION_REP . $iterRow, utf8_encode($row->description));
      $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getFont()->setSize(16);
      $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getFill()->setFillType(
      PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION_REP . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
      //---------------------------------------------------------------------------------
      $infoStatAdr = '';
      if ($row->statut == 9) {
        $infoStatAdr = '# Série: ' . $row->noSerie . ', Grandeur: ' . $row->grandeur .
        ', Couleur: ' . $row->couleur;
      } else if ($row->statut == 8) {
        $infoStatAdr = $row->adresse;
      } else if ($row->statut == 10) {
        $infoStatAdr = $row->matiere;
      }
      $objPHPExcel->getActiveSheet()->setCellValue(ADRESSE_REP . $iterRow,utf8_encode($infoStatAdr) );
      $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getFill()->setFillType(
      PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(ADRESSE_REP . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(STATUT_MAT_REP . $iterRow, $row->NonStatut);
      $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getFill()->setFillType(
      PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT_REP . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(DATE_CREE_REP . $iterRow, $row->dtecreate);
      $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getFill()->setFillType(
      PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE_REP . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->getStyle(REQUERANT_REP . $iterRow.":".DATE_CREE_REP . $iterRow)->applyFromArray(
      array(
      'borders' => array(
      'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => 'DDDDDD')
      )
      )
      )
      );

      $iterRow++;

    }
    $iterRow++;
  }
  public function getListRequetes($filename)
  {
    $objPHPExcel = PHPExcel_IOFactory::load($filename);
    $objPHPExcel->setActiveSheetIndex(0);
    //-------------------------------------------------------------------------- Requetes
    $idville = trim($this->input->post_get('idville', TRUE));
    if ($idville > 0) {
      $idville = ' and idville = ' . $this->db->escape($idville);
    } else {
      $idville = '';
    }
    $istraitees = trim($this->input->post_get('istraitees', TRUE));
    if ($istraitees == 1) {
      $istraitees = ' and statut = 7 ';
    } else {
      $istraitees = ' and statut != 7 ';
    }
    $dtestart = trim($this->input->post_get('dtestart', TRUE));
    if ($dtestart != '') {
      $dtestart = ' and dtecreate >= ' . $this->db->escape($dtestart);
    }
    $dteend = trim($this->input->post_get('dteend', TRUE));
    if ($dteend != '') {
      $dteend = $dteend . ' 23:59:00';
      $dteend = ' and dtecreate <= ' . $this->db->escape($dteend);
    }
    $endate = 'en date du : '.date('Y-m-d');
    $returnval = '';
    /** @var string  $strDesc */
    $strDesc = ($this->sess('site_lang') == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
    $strSQL = "SELECT id,CONCAT('R-', id) AS noRequete, idville, nom, adresse, IsFrom, id,
										CONCAT('upload/', idville,'/',fichier) as url, TRIM(`description`) AS `description`,(SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS matiere,
                    (SELECT COUNT(id) FROM reponses WHERE IsVue = 0 and IsFrom = 0 AND idrequete = requetes.id) as RepVue,
                    (SELECT Count(reponses.id) FROM reponses WHERE reponses.idrequete = requetes.id) as NbRep,
										                    (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statut,
                    (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
                    Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = requetes.idville) as NomVille,
										createby, left(dtecreate, 10) as dtecreate, dteupdate, left(dteCedule, 10) as dteCedule
							FROM requetes where Iscomplete = 0 and isdeleted = 0 $idville $istraitees $dtestart $dteend ORDER BY id";
    /** @var object $cal */
    $requetes = $this->db->query($strSQL);
    $idrequete = 0;
    //-----------------------------------------------------------------------------------------

    //-----------------
    $objPHPExcel->getActiveSheet()->setCellValue('C1', utf8_encode('Liste des Requêtes / '.$endate));
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    /** @var int $iterRow */
    $iterRow = 4;
    /** @var object $row */
    foreach ($requetes->getResult() as $row)
    {
      $idrequete = $row->id;
      if ($row->IsFrom == 0)
      {
        $objPHPExcel->getActiveSheet()->setCellValue(REQUERANT . $iterRow, $row->NomVille);
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT.$iterRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT . $iterRow)->getFont()->getColor()->setRGB("FF3C11");
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT . $iterRow)->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle(REQUERANT . $iterRow)->getFont()->setName("Arial Black");
        $objPHPExcel->getActiveSheet()->getStyle(REQUETE . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
        )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      }
      else
      {
        $this->getLogo($objPHPExcel, $iterRow);
      }
      $objPHPExcel->getActiveSheet()->setCellValue(REQUETE . $iterRow, $row->noRequete);
      $objPHPExcel->getActiveSheet()->getStyle(REQUETE . $iterRow)->getFont()->getColor()->setRGB("f1a905");
      $objPHPExcel->getActiveSheet()->getStyle(REQUETE . $iterRow)->getFont()->setSize(16);
      $objPHPExcel->getActiveSheet()->getStyle(REQUETE . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(REQUETE . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(CEDULEE . $iterRow, $row->dteCedule);
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE . $iterRow)->getFont()->setSize(16);
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(CEDULEE . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(TITRE . $iterRow, utf8_encode($row->nom));
      $objPHPExcel->getActiveSheet()->getStyle(TITRE . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(TITRE . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(TITRE . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(ADRESSE . $iterRow, utf8_encode($row->adresse));
      $objPHPExcel->getActiveSheet()->getStyle(ADRESSE . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(ADRESSE . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(ADRESSE . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(DESCRIPTION . $iterRow, utf8_encode($row->description));
      $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(DESCRIPTION . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(STATUT_MAT . $iterRow, $row->statut);
      $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(STATUT_MAT . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //---------------------------------------------------------------------------------
      $objPHPExcel->getActiveSheet()->setCellValue(DATE_CREE . $iterRow, $row->dtecreate);
      $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE . $iterRow)->getFont()->setSize(12);
      $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'b9ffff' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle(DATE_CREE . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //---------------------------------------------------------------------------------
      $iterRow++;
      if($row->NbRep > 0)
      {
        $this->getReponse($idrequete, $objPHPExcel, $iterRow);
      }

    }
    foreach ($objPHPExcel->getActiveSheet()->getRowDimensions() as $rd) {
      $rd->setRowHeight(-1);
    }
    ///----------------------------------------------------------------------
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(71);
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('test_img');
    $objDrawing->setDescription('test_img');
    $objDrawing->setPath('./assets/img/logo.jpg');
    $objDrawing->setCoordinates('A1');
    //setOffsetX works properly
    $objDrawing->setOffsetX(15);
    $objDrawing->setOffsetY(15);
    //set width, height
    $objDrawing->setWidth(110);
    $objDrawing->setHeight(60);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    //-----------------------------------------------------------------------
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea('A1:I'. $iterRow);
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save('./upload/liste_requetes.xlsx');
  }
  public function getListTelephone($listtype, $membres, $titre, $filename)
  {
    $objPHPExcel = PHPExcel_IOFactory::load($filename);
    $objPHPExcel->setActiveSheetIndex(0);
    //--------------------------------------------------------------------

    //--------------------------------------------------------------------------
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[2] . '1', utf8_encode($titre));
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[2] . '1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[2] . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[5] . '1', 'Nbre membres: ' . $membres->getNumRows());
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[5] . '1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[5] . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    /** @var string $ActiveCol */
    $ActiveCol = array('A', 'C', 'E');
    /** @var int $Col */
    $Col1 = 'A';
    $Col2 = 'B';
    $Col3 = 'C';
    /** @var bool $IsShow */
    $IsShow = false;
    /** @var int $iterRow */
    $iterRow = 3;
    $iter = 0;
    /** @var object $row */
    foreach ($membres->getResult() as $row) {
      $objPHPExcel->getActiveSheet()->setCellValue($Col1 . $iterRow, $row->Nom);
      $objPHPExcel->getActiveSheet()->getStyle($Col1 . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle($Col1 . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->setCellValue($Col2 . $iterRow, $row->Prenom);
      $objPHPExcel->getActiveSheet()->getStyle($Col2 . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle($Col2 . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->setCellValue($Col3 . $iterRow, $row->cell);
      $objPHPExcel->getActiveSheet()->getStyle($Col3 . $iterRow)->getFill()->setFillType(
        PHPExcel_Style_Fill::FILL_SOLID
      )->getStartColor()->setARGB(((($iterRow % 2) == 0) ? 'D8D8D8' : 'FFFFFF'));
      $objPHPExcel->getActiveSheet()->getStyle($Col3 . $iterRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $iterRow++;
      if ($iterRow > 37) {
        $iter++;
        $Col1 = 'E';
        $Col2 = 'F';
        $Col3 = 'G';
        $iterRow = 3;
      } else if ($iterRow == 44) {
        $iterRow++;
      }
    }
    foreach ($objPHPExcel->getActiveSheet()->getRowDimensions() as $rd) {
      $rd->setRowHeight(-1);
    }

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save($filename);
    $filename = "./upload/requete'.xlsx";
    force_download($filename, NULL);
  }
  public function getListMembres($membres, $filename)
  {
    // compagnie, service, num_compte,
    //left( echeance, 10) as echeance, depenses, FORMAT(montant, 2) as montant, contact, courriel,
    //  coordonnees AS Telephone, site_web, utilisateur, passw, commentaire
    $objPHPExcel = PHPExcel_IOFactory::load($filename);
    $objPHPExcel->setActiveSheetIndex(0);
    //--------------------------------------------------------------------
    //--------------------------------------------------------------------------


    /** @var string $ActiveCol */
    $ActiveCol = array(
      'A',
      'B',
      'C',
      'D',
      'E',
      'F',
      'G',
      'H',
      'I',
      'J',
      'K',
      'L',
      'M',
      'N',
      'O',
      'P',
      'Q',
      'R',
      'S',
      'T');
    /** @var string $Name */
    $Name = array(
      'Prenom',
      'Nom',
      'DateNaissance',
      'Age',
      'datedeb',
      'Depuis',
      'tel',
      'cell',
      'telAutre',
      'telUrgence',
      'Personnerejoindre',
      'Lien',
      'courriel',
      'Adresse',
      'Ville',
      'codepostal',
      'Note',
      'Province',
      'CarteMembre'
    );
    /** @var int $iterRow */
    $iterRow = 3;
    //$date = 'Mise à jour: ' . date('Y-m-d');
    //$objPHPExcel->getActiveSheet()->setCellValue('E1', $date);
    /** @var object $row */
    foreach ($membres->getResultArray() as $row) {
      for ($col = 0; $col < 19; $col++) {
        if ($col == 16) {
          $richText = $row[$Name[$col]];
          $objPHPExcel->getActiveSheet()->setCellValue($ActiveCol[$col] . $iterRow, $richText);
          $objPHPExcel->getActiveSheet()->getColumnDimension($ActiveCol[$col])->setWidth(48);
          $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
          $objPHPExcel->getActiveSheet()->getStyle($ActiveCol[$col])
            ->getAlignment()
            ->setWrapText(true);
        }
        else {
          $objPHPExcel->getActiveSheet()->setCellValue($ActiveCol[$col] . $iterRow, $row[$Name[$col]]);
          $objPHPExcel->getActiveSheet()->getStyle($ActiveCol[$col])
            ->getAlignment()
            ->setWrapText(true);
        }

      }
      $iterRow++;
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save($filename);

  }
  public function getListTels($membres, $filename)
  {
    // compagnie, service, num_compte,
    //left( echeance, 10) as echeance, depenses, FORMAT(montant, 2) as montant, contact, courriel,
    //  coordonnees AS Telephone, site_web, utilisateur, passw, commentaire
    $objPHPExcel = PHPExcel_IOFactory::load($filename);
    $objPHPExcel->setActiveSheetIndex(0);
    //--------------------------------------------------------------------
    //--------------------------------------------------------------------------


    /** @var string $ActiveCol */
    $ActiveCol = array(
      'A',
      'B',
      'C',
      'D',
      'E',
      'F',
      'G',
      'H',
      'I',
      'J',
      'K',
      'L',
      'M',
      'N',
      'O',
      'P',
      'Q',
      'R',
      'S',
      'T');
    /** @var string $Name */
    $Name = array(
      'Prenom',
      'Nom',
      'tel',
      'cell',
      'CarteMembre'
    );
    /** @var int $iterRow */
    $iterRow = 3;
    //$date = 'Mise à jour: ' . date('Y-m-d');
    //$objPHPExcel->getActiveSheet()->setCellValue('E1', $date);
    /** @var object $row */
    foreach ($membres->getResultArray() as $row) {
      for ($col = 0; $col < 5; $col++)
      {
          $objPHPExcel->getActiveSheet()->setCellValue($ActiveCol[$col] . $iterRow, $row[$Name[$col]]);
          $objPHPExcel->getActiveSheet()->getStyle($ActiveCol[$col])
            ->getAlignment()
            ->setWrapText(true);
      }
      $iterRow++;
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save($filename);

  }
}

?>