<?php

namespace App\Models;

/**
 * Port mecanique de Excel_model (CI3) â€” requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */


require_once env('ci3.webroot') . '/application/' . '/libraries/PHPExcel.php';
/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class ExcelModel extends Ci3Model
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
                'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
                'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BZ', 'BS',
                'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
                'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CZ', 'CS',
                'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
                'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DZ', 'DS',
                'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ','EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'EZ', 'ES',
                'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ',
                'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FZ', 'FS',
                'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ');
  //----------------------------
	function __construct() {
		parent::__construct();
		
		
		
		
	}
  /**
   *
   * @param string $dateactive
   * @param object $objPHPExcel
   *
   *
   */
  public function LoadToday($dateactive)
  {
    return $this->LoadCedules($dateactive, 3);
  }
  /**
   *
   * @param string $dateactive
   * @param int $from
   *
   */
  public function LoadCedules ($dateactive, $from)
	{
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
		//----------------------------------------------------------------------
    $colTypeCedule = 0;
    $ActiveCol = 0;
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
    							<td class="cellule" align="center">&nbsp;</td>';
    /** @var string $returnval */
    $returnval = '<table  border="1" style="width: 100%;"
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
    /** @var object $typecedule */
    foreach($resultCedule->getResult() as $typecedule)
    {
      $strSQL = "SELECT DISTINCT ifnull(ACol, 1) as ACol FROM grp_cedules WHERE datejour = $dateactive and TypeCedule = ".$typecedule->TypeCedule."  ORDER BY ACol;" ;
      /** @var object $result */
      $result = $this->db->query($strSQL);
      $nbCol = $result->getNumRows() ;
      $NbColTotal += $nbCol;

      $nbcolcedule = (($typecedule->TypeCedule == 1)? 4: 3);
      $end = ($nbcolcedule * $nbCol) + $ActiveCol - 1;
      $objPHPExcel->getActiveSheet()->mergeCells($this->Alphabet[$colTypeCedule].'1'.':'.$this->Alphabet[$end].'1');
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$colTypeCedule].'1', (($typecedule->TypeCedule == 1)? 'REAR END' : 'ROBOT'));
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$colTypeCedule].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB((($typecedule->TypeCedule == 1)? 'FF00FFFF' : 'FFD2691E'));
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$colTypeCedule].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$colTypeCedule].'1')->getFont()->setBold(true);
      $colTypeCedule = ($nbcolcedule * $nbCol);
      /** @var object $row */
      foreach($result->getResult() as $row)
      {
        $activestartcol = $ActiveCol;
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'CAM');
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(9);
        $ActiveCol++;
        //-----------------------------------
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'CHAUFFEUR');
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
        $ActiveCol++;
        //-----------------------------------
        if ($typecedule->TypeCedule == 1)
        {
          $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'AIDE');
          $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
          $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
          $ActiveCol++;
        }
        //-----------------------------------
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'SECTEUR');
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
        $ActiveCol++;
        $strSQL ="SELECT c.NOCAMION, c.IsStart,c.ID,g.datejour,g.idvilles,ACol,ARow, c.HELPERS_1, c.idchauffeurs,g.nb_cam,
                      if((SELECT suiviscamion.id FROM suiviscamion WHERE suiviscamion.nocamion = c.NOCAMION AND suiviscamion.idstatut = 2 AND
                       suiviscamion.dateend is NULL and suiviscamion.datestart <= $dateactive ) > 0, 1, 0) AS HaveMat,
                      (SELECT MAX(ARow) as nb from grp_cedules where grp_cedules.datejour = $dateactive AND grp_cedules.ACol = ". $row->ACol."  and typecedule = ".$typecedule->TypeCedule.") AS nbRow,
                      CONCAT((SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ), '-',
                           g.nb_cam, '=',
                           FORMAT((SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ) - g.nb_cam, 1)) AS NbCam,
                            (SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ) as ActiveNbCam,
                            (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
                            (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
                              sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dateactive
                              and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
                              ( Select CONCAT (Nom, ', ', LEFT(Prenom, 1) ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
                              ( Select CONCAT (Nom, ', ', LEFT(Prenom, 1) ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
                              IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
                              IFNULL(( Select colorhex FROM ville where id = g.idvilles ), -1)  As couleurville,
                              g.id as idgrp, g.TypeCedule, c.ID, c.chaufconfirme, c.helperconfirme, c.IsLocked,
                              c.IsLockedChauffeur,Secteur,
                              c.TypeDeProduit, ifnull(c.idchauffeurs, 0) AS nochauffeur, ifnull(c.HELPERS_1, 0) AS noaide
                          from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
                          WHERE g.datejour = $dateactive and ( g.TypeCedule = ".$typecedule->TypeCedule." )
                          AND (IFNULL(IF(c.idchauffeurs = 0, NULL, c.idchauffeurs),
                          IFNULL(if(c.NOCAMION = '', NULL, c.NOCAMION), if(c.HELPERS_1 = 0, NULL, c.HELPERS_1)))) IS NOT NULL
                          and ACol = ".$row->ACol." Order by  g.ARow, g.id, c.ID";
        /** @var object $resultLine */
        $resultLine = $this->db->query($strSQL);
        $this->getLignesCedule($resultLine, $GrpID, $activestartcol, $objPHPExcel);
      }
    }
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $filename = "./upload/cedule_'.$activedate.xlsx";
    $objWriter->save($filename);
    force_download($filename, NULL);
    delete_files("./upload/cedule_'.$activedate.xlsx");
    //---------------------------------------------------------------------------------------------------------------------------------------
	}
  /**
   *
   * @param string $dateactive
   * @param int $from
   *
   */
  public function LoadRollOff ($dateactive, $from)
	{
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
		//----------------------------------------------------------------------
    $colTypeCedule = 0;
    $ActiveCol = 0;
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
    							<td class="cellule" align="center">&nbsp;</td>';
    /** @var string $returnval */
    $returnval = '<table  border="1" style="width: 100%;"
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
    /** @var object $typecedule */
    foreach($resultCedule->getResult() as $typecedule)
    {
      $strSQL = "SELECT DISTINCT ifnull(ACol, 1) as ACol FROM grp_cedules WHERE datejour = $dateactive and TypeCedule = ".$typecedule->TypeCedule."  ORDER BY ACol;" ;
      /** @var object $result */
      $result = $this->db->query($strSQL);
      $nbCol = $result->getNumRows() ;
      $NbColTotal += $nbCol;

      $nbcolcedule = (($typecedule->TypeCedule == 1)? 4: 3);
      $end = ($nbcolcedule * $nbCol) + $ActiveCol - 1;
      $objPHPExcel->getActiveSheet()->mergeCells($this->Alphabet[$colTypeCedule].'1'.':'.$this->Alphabet[$end].'1');
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$colTypeCedule].'1', (($typecedule->TypeCedule == 1)? 'REAR END' : 'ROBOT'));
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$colTypeCedule].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB((($typecedule->TypeCedule == 1)? 'FF00FFFF' : 'FFD2691E'));
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$colTypeCedule].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$colTypeCedule].'1')->getFont()->setBold(true);
      $colTypeCedule = ($nbcolcedule * $nbCol);
      /** @var object $row */
      foreach($result->getResult() as $row)
      {
        $activestartcol = $ActiveCol;
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'CAM');
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(9);
        $ActiveCol++;
        //-----------------------------------
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'CHAUFFEUR');
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
        $ActiveCol++;
        //-----------------------------------
        if ($typecedule->TypeCedule == 1)
        {
          $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'AIDE');
          $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
          $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
          $ActiveCol++;
        }
        //-----------------------------------
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'2', 'SECTEUR');
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
        $ActiveCol++;
        $strSQL ="SELECT c.NOCAMION, c.IsStart,c.ID,g.datejour,g.idvilles,ACol,ARow, c.HELPERS_1, c.idchauffeurs,g.nb_cam,
                      if((SELECT suiviscamion.id FROM suiviscamion WHERE suiviscamion.nocamion = c.NOCAMION AND suiviscamion.idstatut = 2 AND
                       suiviscamion.dateend is NULL and suiviscamion.datestart <= $dateactive ) > 0, 1, 0) AS HaveMat,
                      (SELECT MAX(ARow) as nb from grp_cedules where grp_cedules.datejour = $dateactive AND grp_cedules.ACol = ". $row->ACol."  and typecedule = ".$typecedule->TypeCedule.") AS nbRow,
                      CONCAT((SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ), '-',
                           g.nb_cam, '=',
                           FORMAT((SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ) - g.nb_cam, 1)) AS NbCam,
                            (SELECT COUNT(ID) FROM cedules WHERE idgrp = g.id AND NOCAMION <> '' AND NOCAMION IS NOT NULL ) as ActiveNbCam,
                            (SELECT camions.TypeCamion FROM camions WHERE camions.NoCamion = c.NOCAMION) AS typecamion,
                            (Select idstatut From suiviscamion sc where sc.nocamion = c.NOCAMION and
                              sc.idstatut = 2 and DATE_FORMAT(sc.datestart, '%Y-%m-%d') < $dateactive
                              and ( dateend is null  )  LIMIT 0, 1 ) as dechet,
                              ( Select CONCAT (Nom, ', ', LEFT(Prenom, 1) ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
                              ( Select CONCAT (Nom, ', ', LEFT(Prenom, 1) ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
                              IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,
                              IFNULL(( Select colorhex FROM ville where id = g.idvilles ), -1)  As couleurville,
                              g.id as idgrp, g.TypeCedule, c.ID, c.chaufconfirme, c.helperconfirme, c.IsLocked,
                              c.IsLockedChauffeur,Secteur,
                              c.TypeDeProduit, ifnull(c.idchauffeurs, 0) AS nochauffeur, ifnull(c.HELPERS_1, 0) AS noaide
                          from grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
                          WHERE g.datejour = $dateactive and ( g.TypeCedule = ".$typecedule->TypeCedule." )
                          AND (IFNULL(IF(c.idchauffeurs = 0, NULL, c.idchauffeurs),
                          IFNULL(if(c.NOCAMION = '', NULL, c.NOCAMION), if(c.HELPERS_1 = 0, NULL, c.HELPERS_1)))) IS NOT NULL
                          and ACol = ".$row->ACol." Order by  g.ARow, g.id, c.ID";
        /** @var object $resultLine */
        $resultLine = $this->db->query($strSQL);
        $this->getLignesCedule($resultLine, $GrpID, $activestartcol, $objPHPExcel);
      }
    }
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $filename = "./upload/cedule_'.$activedate.'xlsx";
    $objWriter->save($filename);
    force_download($filename, NULL);
    delete_files("./upload/cedule_'.$activedate.'xlsx");
    //---------------------------------------------------------------------------------------------------------------------------------------
	}
  /**
   *
   * @param object $resultLine
   * @param int $GrpID
   * @param int $ActiveCol
   * @param object $objPHPExcel
   *
   * @return void
   */
  private function getLignesCedule($resultLine, $GrpID, $ActiveCol, &$objPHPExcel)
  {
    /** @var string $ColOne */
    /** @var int $span */
    /** @var string $color */
    /** @var int $iterRow */
    $iterRow = 3;
    /** @var int $StartCol */
    $StartCol = $ActiveCol;
    /** @var object $row */
    foreach($resultLine->getResult() as $row)
    {
      switch ($row->TypeDeProduit)
      {
        case 'ORGANIQUE':
          $color = 'ffebccaa';
          break;
        case 'RECYCLAGE':
          $color = 'FF9FF3F9';
          break;
        case 'DECHETS':
          $color = 'FFE5E5E5';
          break;
        case 'RESIDUS_VERT':
          $color = 'FF2DBC96';
          break;
        case 'GROS_REBUTS':
          $color = 'FF2B3B7AF';
          break;
        case 'BRANCHES':
          $color = 'FFD1B546';
          break;
        case 'FEUILLES':
          $color = 'FF2DBC96';
          break;
        default:
          $color = 'FFF7F1C5';
      }
      $ColOne = '<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
    							<td  align="center">&nbsp;</td>
                  '.(($row->TypeCedule == 2)? '' : '<td  align="center">&nbsp;</td>').'
    							<td  align="center">&nbsp;</td>';
      if ($GrpID != $row->idgrp)
      {
        $span = $ActiveCol + (($row->TypeCedule == 2)? '3' : '4');
        $objPHPExcel->getActiveSheet()->mergeCells($this->Alphabet[$ActiveCol].$iterRow.':'.$this->Alphabet[$span - 1].$iterRow);
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->nomville);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.substr($row->couleurville, 1));
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
        $GrpID = $row->idgrp;
        $iterRow++;
      }

      //-------------------------------------

      $cam = substr($row->NOCAMION ,0, 3);
      $backColor = '';
      if ($row->IsStart == 1)
      {
        $backColor = 'FFFFFF00' ;
      }
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, ((($cam == 'Cam') or (trim($cam) == ''))? '?' : $row->NOCAMION ));
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(9);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB((($backColor != '')? $backColor : 'FFFFFFFF'));
      $ActiveCol++;
      if ($row->chaufconfirme == 1)
      {
        $backColor = 'FFFFFF00' ;
      }
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, substr( $row->chauffeur, 0,20));
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB((($backColor != '')? $backColor : 'FFFFFFFF'));
      $ActiveCol++;
      if ($row->helperconfirme == 1)
      {
        $backColor = 'FFFFFF00' ;
      }
      if ($row->TypeCedule == 1)
      {
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, substr( $row->aide1, 0,20));
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB((($backColor != '')? $backColor : 'FFFFFFFF'));
        $ActiveCol++;
        //-------------------------------
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, substr( $row->Secteur, 0,20));
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
      }
      else
      {
        $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, substr( $row->Secteur, 0,20));
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
      }
      $ActiveCol = $StartCol;
      $iterRow++;
    }
  }
  public function getHresAides($dateactive, $strWhere)
  {
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $arrHresPM = array('11:00','11:15','11:30',
                        '11:45','12:00','12:15','12:30','12:45','13:00','13:15','13:30','13:45','14:00',
                        '14:15','14:30','14:45','15:00','15:15','15:30','15:45','16:00','16:15','16:30',
                        '16:45','17:00','17:15','17:30','17:45','18:00','18:15','18:30','18:45','19:00',
                        '19:15','19:30','19:45','20:00','20:15','20:30','20:45','21:00','21:15','21:30','21:45');
    $ActiveCol = 0;
    $iterRow = 0;
    $dateactive = $this->db->escape($dateactive);
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'1', 'Aides');
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(20);
    $ActiveCol++;
    //-----------------------------------
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'1', 'Début');
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
    $ActiveCol++;
    //-----------------------------------
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'1', 'Fin');
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
    $ActiveCol++;
    //-----------------------------------
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'1', 'Pause');
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
    $ActiveCol++;
    //-----------------------------------
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'1', 'Note');
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(25);
    $ActiveCol++;
    //-----------------------------------
    $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].'1', 'Hres');
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].'1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(16);
    $ActiveCol++;
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
    $ActiveCol = 0;
    $iterRow = 3;
    foreach($ville->getResult() as $row)
    {
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->NomComplet);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(20);
      $ActiveCol++;
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->StartTime);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(10);
      $ActiveCol++;
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->EndTime);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(10);
      $ActiveCol++;
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->pause);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(10);
      $ActiveCol++;
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->note);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(30);
      $ActiveCol++;
      $objPHPExcel->getActiveSheet()->setCellValue($this->Alphabet[$ActiveCol].$iterRow, $row->HrsTrav);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle($this->Alphabet[$ActiveCol].$iterRow)->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension($this->Alphabet[$ActiveCol])->setWidth(30);
      $ActiveCol = 0;
      $iterRow++;
    }
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $filename = "./upload/heures_aides_'.$dateactive.xlsx";
    $objWriter->save($filename);
    force_download($filename, NULL);
    delete_files("./upload/heures_aides_'.$dateactive.xlsx");
    //return $returnval;
  }
  //------------------------------------------------------------------- Fonction pour Prévision
  //
  //----------------------------------------------------------------------
  private function InsertCSV ($arrCSV, $idsource, $filename, $select)
  {

      $Connect = new ClassConnect();
      $query = "UPDATE sources
	                        SET
	                            filename='$filename',
	                            selectchamp='$select'
	                        WHERE idsource = '$idsource'" ;
      $result = $Connect->dbprotect->query($query);
      $query = "DELETE FROM sourceformat WHERE idsource= '$idsource' " ;
      $result = $Connect->dbprotect->query($query);
      foreach ($arrCSV as $fieldname) {
        $query = "INSERT INTO sourceformat
	            			(idsource, champ, displayname)
	                                           VALUES ( '$idsource', '$fieldname',  '$fieldname' )" ;
        $result = $Connect->dbprotect->query($query);
      }
      $Connect->close();

  }
  public  function ImportSource($filename)
  {
    /** @var array $arMois */
    $arMois = array('6,24','42,60','78,96','114,132','150,168','186,204','223,242','260,278','296,315','333,351','369,387','405,423');
    if ( ( strpos(strtolower($filename),'.txt' ) == true ) ||
              ( strpos(strtolower($filename),'.csv' ) == true )   )
    {
      //$resultCSV = $this->parseCSVField($filename);
      //return $this->InsertCSV($resultCSV,$idsource, $filename, $select);
    }
    else if ( ( strpos(strtolower($filename),'.xls' ) == true ) ||
            ( strpos(strtolower($filename),'.xlsx' ) == true )   )
    {
      $objPHPExcel = \PHPExcel_IOFactory::load('./upload/'.$filename);
      $objPHPExcel->setActiveSheetIndexByName('2021');
      /** @var int $ACol */

      /** @var int $aRow */
      /** @var int $Mois */
      for ($Mois = 0; $Mois <= 11; $Mois++)
      {
        $ACol = 2;
        /** @var array $arrRow */
        $arrRow =explode(',', $arMois[$Mois]);
        while($ACol < 177)
        {
          for ($aRow = $arrRow[0]; $aRow <= ($arrRow[0] + 13); $aRow++)
          {
            $NoCamion = $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $aRow)->getValue());
            if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $aRow)->getValue() == '0')
            {
              break;
            }
            $KiloDeb =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol, $aRow)->getValue());
            $KiloFin =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 1, $aRow)->getValue());
            $RevenuBrut =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 3, $aRow)->getValue());
            $Fuel =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 4, $aRow)->getValue());
            $HresWork =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 6, $aRow)->getValue());
            $Enfouissement =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 8, $aRow)->getValue());
            $PrixFuel =  $this->db->escape(1.10);
            $month = (($Mois + 1 <10) ? '0'.($Mois + 1) : ($Mois + 1) );
            $jour = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol, $arrRow[0] - 2)->getValue();
            if ($jour == '')
            {
              break;
            }
            $jour = (($jour + 1 <10) ? '0'.$jour : $jour );
            $DateJour =  $this->db->escape('2021-' .$month. '-' . $jour);
            $PrixChauf =  $this->db->escape(32);
            $date = new \DateTime('2021-' .$month. '-' . $jour);
            $j = $date->format('N');
            if (($j != 7))
            {
              $strSQL = "INSERT INTO tmp_from_admin
	                        (KiloDeb, KiloFin, RevenuBrut, Fuel, PrixFuel, Enfouissement, DateJour, idrh, HresWork, NoCamion, aCol, aRow, PrixChauf)
	                        VALUES ($KiloDeb, $KiloFin, $RevenuBrut, $Fuel, $PrixFuel, $Enfouissement, $DateJour, 24, $HresWork, $NoCamion, $ACol, $aRow, $PrixChauf)";
              $this->db->query($strSQL);
            }
            //-----------------------------------------------------

            //-----------------------------------------------------
          }
          //----------------------------------------------
          for ($aRow = $arrRow[1]; $aRow <= ($arrRow[1] + 13); $aRow++)
          {

            $NoCamion = $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $aRow)->getValue());
            if (($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $aRow)->getValue() == '0'))
            {
              break;
            }
            $KiloDeb =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol, $aRow)->getValue());
            $KiloFin =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 1, $aRow)->getValue());
            $RevenuBrut =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 3, $aRow)->getValue());
            $Fuel =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 4, $aRow)->getValue());
            $HresWork =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 6, $aRow)->getValue());
            $Enfouissement =  $this->db->escape($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol + 8, $aRow)->getValue());
            $PrixFuel =  $this->db->escape(1.10);
            $month = (($Mois + 1 <10) ? '0'.($Mois + 1) : ($Mois + 1) );
            $jour = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ACol, $arrRow[1] - 2)->getValue();
            if ($jour == '')
            {
              break;
            }
            $jour = (($jour + 1 <10) ? '0'.$jour : $jour );
            $DateJour =  $this->db->escape('2021-' .$month. '-' . $jour);
            $PrixChauf =  $this->db->escape(32);
            //-----------------------------------------------------
            $date = new \DateTime('2021-' .$month. '-' . $jour);
            $j = $date->format('N');
            if (($j != 7))
            {
              $strSQL = "INSERT INTO tmp_from_admin
	                        (KiloDeb, KiloFin, RevenuBrut, Fuel, PrixFuel, Enfouissement, DateJour, idrh, HresWork, NoCamion, aCol, aRow, PrixChauf)
	                        VALUES ($KiloDeb, $KiloFin, $RevenuBrut, $Fuel, $PrixFuel, $Enfouissement, $DateJour, 24, $HresWork, $NoCamion, $ACol, $aRow, $PrixChauf)";
              $this->db->query($strSQL);
            }
            //-----------------------------------------------------
          }
          $ACol += 11;
        }
      }
      //-------------------------------------------------------------------------------
    }
  }
  private function ModifyTblLigneFC($result)
  {
    foreach($result->getResult() as $row)
    {
      $col = 'col_'.$row->colnum;
      if (!$this->db->fieldExists($col, 'ligne_forecast'))
      {
        $strSQL = "ALTER TABLE ligne_forecast
                    ADD $col float;";
        $this->db->query($strSQL);
      }
    }
  }
  //----------------------------------------------------------------------
  private  function SourceCSV ($resultCSV, $idsource, $filename)
  {

    $returnval = '';
    $query =  "SELECT champ, displayname
	                          FROM sourceformat where idsource = '$idsource'
	                          order by idsourceformat";
    $arr = array();
    $ligne1  = '';
    $Connect = new ClassConnect();
    $result = $Connect->dbprotect->query($query);
    if ( $result->num_rows > 0 )
    {
      while($data = $result->fetch_assoc() )
      {
        $ligne1 .= '<th style="font: bold 13px Arial, Helvetica, sans-serif; color: #0079c2;  text-align:center;">'.$data['displayname'] .' </th>'  ;
      }
    }
    $returnval .= '<table class="table table-bordered table-hover"   >
	                        	  <thead>  <tr>    '.((trim($ligne1) == '') ? '' : $ligne1) .' </tr>    </thead>
	                        <tbody>';
    $returnval .= '<tr >' ;
    foreach ($resultCSV[0]  as $value)
    {
      $returnval .= '<td width="150" >'.$value .' </td>'  ;
    }
    $returnval .=  '</tr >' ;
    $returnval .= '</tbody></table>';
    $Connect->close();
    return  $returnval ;

  }
  //----------------------------------------------------------------------
  public  function ShowSourceDonnees($idsource, &$nbrow)
  {
      $filename = '';
      $returnval = '';
      $row = '';
      $alpha = "A;B;C;D;E;F;G;H;I;J;K;L;M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z;" ;
      $alpha .= "AA;AB;AC;AD;AE;AF;AG;AH;AI;AJ;AK;AL;AM;AN;AO;AP;AQ;AR;AS;AT;AU;AV;AW;AX;AY;AZ;" ;
      $alpha .= "BA;BB;BC;BD;BE;BF;BG;BH;BI;BJ;BK;BL;BM;BN;BO;BP;BQ;BR;BS;BT;BU;BV;BW;BX;BY;BZ;" ;
      $alpha .= "CA;CB;CC;CD;CE;CF;CG;CH;CI;CJ;CK;CL;CM;CN;CO;CP;CQ;CR;CS;CT;CU;CV;CW;CX;CY;CZ" ;
      $ArrAlphabet = explode(';',$alpha ) ;
      //$arrtest = array();
      $query =  "SELECT champ, displayname ,
	                        (SELECT selectchamp FROM sources WHERE idsource = '$idsource' LIMIT 0, 1) as wherechamp,
	                        (SELECT filename FROM sources WHERE idsource = '$idsource'  LIMIT 0, 1) as filen
	                            FROM sourceformat where idsource = '$idsource'
	                           order by idsourceformat";
      $arr = array();
      $Connect = new ClassConnect();
      $result = $Connect->dbprotect->query($query);
      $colonneA = '';
      $ligne1  = '';
      if ( $result->num_rows > 0 )
      {
        while($data = $result->fetch_assoc() )
        {
          $filename = $data['filen'];
          if (trim($filename) == '' )
          {
            return '' ;
          }

          if ( $data['wherechamp']  == 'ligne' )
          {
            $ligne1 .= '<th style="font: bold 13px Arial, Helvetica, sans-serif; color: #0079c2;  text-align:center;">'.$data['displayname'] .' </th>'  ;
          }
          else  if ( $data['wherechamp']  == 'colonne' )
          {
            $colonneA .= '<td style="font: bold 13px Arial, Helvetica, sans-serif; color: #0079c2;  text-align:left;">'.$data['displayname'] .' </td>|'  ;
          }
          //$arrtest[0][] = $data['displayname'];
        }
      }
      if (trim($filename) == '' )
      {
        return '' ;
      }
      $arrCol = explode("|", substr($colonneA, 0, -1)) ;
      $returnval .= '<table class="table table-bordered table-hover"   >
	                        	    <thead>  <tr>    '.((trim($ligne1) == '') ? '' : $ligne1) .' </tr>    </thead>
	                        	<tbody>';
      $objPHPExcel = \PHPExcel_IOFactory::load($filename);
      $objPHPExcel->setActiveSheetIndex(0);
      $highestColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
      $highestRow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
      $nbrow = $highestRow  - 1;
      $info1 = ((trim($ligne1) != '') ? 2 : 1);

      for ($iter = $info1; $iter <5000; $iter++)
      {
        $j=0;
        if  ( $iter > ($highestRow ) ) {
          break ;
        }
        if  (trim($ligne1) != '')
        {
          $returnval .= '<tr >' ;
          for ($col = 0; $col < 250; $col++)
          {
            $returnval .= '<td width="150" >'. $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $iter)->getFormattedValue() .' </td>'  ;
            if ($ArrAlphabet[$j] == $highestColumm )
            {
              break;
            }
            else
            {
              $j++;
            }
          }
          $returnval .=  '</tr >' ;
        }
        else
        {
          $returnval .= '<tr >'. $arrCol[$iter-1] ;
          for ($col = 2; $col < 250; $col++)
          {
            $returnval .= '<td width="150" >'. $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col,$iter)->getFormattedValue() .' </td>' ;
            if ($ArrAlphabet[$j] == $highestColumm )
            {
              break;
            }
            else
            {
              $j++;
            }
          }
          $returnval .=  '</tr >' ;
        }
      }
      $returnval .= '</tbody></table>';
      $Connect->close();
      return  $returnval ;

  }
  //----------------------------------------------------------------------
  public function updateSourceFormat()
  {
      $Connect = new ClassConnect();
      $query =  "SELECT idsourceformat
							FROM sourceformat where idsource = '$this->_idsource'
	                           order by idsourceformat";
      $result = $Connect->dbprotect->query($query);
      if ( $result->num_rows > 0 )
      {
        while($data = $result->fetch_assoc() )
        {
          $sourceformat ='fieldformat_' . $data['idsourceformat'];
          $displayname ='displayname_' . $data['idsourceformat'];
          if(isset($_REQUEST[$sourceformat]))
          {
            $valueformat = $_REQUEST[$sourceformat] ;
            $valuedisplay = $_REQUEST[$displayname] ;
            $id = $data['idsourceformat'];
            $query = "UPDATE sourceformat
										SET
											fieldformat='$valueformat',
											displayname='$valuedisplay'
										WHERE idsourceformat ='$id'";
            $postresult = $Connect->dbprotect->query($query);
          }
        }
      }
      $Connect->close();

  }
  //----------------------------------------------------------------------
  public  function parseCSV($Filename, $Length = 1000000, $Delimiter = ';', $Enclosure = '"', $Escape = '\\', $Mode = 'r')
  {
    if (!empty($Filename))
    {
      $ArrayContentFile = array();
      $ArrayTitre = array();
      $fp = fopen($Filename, $Mode);
      $IsFirst = TRUE ;
      if ($fp !== FALSE)
      {
        while (($Data = fgetcsv($fp, $Length, $Delimiter, $Enclosure)) !== FALSE)
        {
          if ($IsFirst) {
            $IsFirst = FALSE;
          }
          else
          {
            //$iter = 0;
            //$ArrayLigne = array();
            $ArrayLigne = $Data;
            $ArrayContentFile[] = $ArrayLigne;
          }
        }
      }
      fclose($fp);
      return $ArrayContentFile;
    }
    return array();
  }
  //----------------------------------------------------------------------
  public  function parseCSVField($Filename, $Length = 1000000, $Delimiter = ';', $Enclosure = '"', $Escape = '\\', $Mode = 'r')
  {
    if (!empty($Filename))
    {
      $ArrayContentFile = array();
      $ArrayTitre = array();
      $fp = fopen($Filename, $Mode);
      $IsFirst = TRUE ;
      if ($fp !== FALSE)
      {
        $Data = fgetcsv($fp, $Length, $Delimiter, $Enclosure);
        $ArrayTitre = $Data;
      }
      fclose($fp);
      return $ArrayTitre ;
    }
    return array();
  }
  //----------------------------------------------------------------------
  //			FIN PROCEDURE GENERALE
  //----------------------------------------------------------------------
  public  function GetModeleID($idgabarit)
  {
      $returnval = '' ;
      $query = "SELECT idmodele
							FROM gabarits	where idgabarit = '$idgabarit' ";
      $Connect = new ClassConnect();
      $result = $Connect->dbprotect->query($query);
      if ($result != false) {
        if ( $result->num_rows > 0 ) {
          $data = $result->fetch_assoc() ;
          $returnval = $data['idmodele'];
        }
      }
      $Connect->close();
      return $returnval ;

  }

	//----------------------------------------------------------
}

?>