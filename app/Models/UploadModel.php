<?php

namespace App\Models;

/**
 * Port mecanique de Upload_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class UploadModel extends Ci3Model
{
  /** @var array $data */
	public $data = array();
    function __construct() {
        parent::__construct();
        
				
    }
      /**
   * @param string $table
   *
   * @return	array
   */
    public function Getupload($table)
    {
        $this->menu = new menuModel();
        
        $this->data = $this->menu->getMenu();
        $this->data['dtestart'] = date('Y-m-d');
        $this->data['dteend'] = date('Y-m-d');
        $this->data['navtop'] = view('partials/navtop', $this->data);
        $this->data['navleft'] = view('partials/navleft', $this->data);
        $this->data['error'] = ' ';
        $this->data['table'] = $table;
        $this->data['route'] = 'admin/charger/'.$table;
        $file = $this->upload->data('full_path');
        /** @var array  $arrCol */
        $arrCol = $this->parseCSVField($file);
        $arrCsv = $this->parseCSV($file);
        switch( $table)
        {
          case 'employes':
            $this->AddEmployes($arrCsv, $arrCol);
            break;
          case 'projets':
            $this->AddProjets($arrCsv, $arrCol);
            break;
          case 'transactions':
            $this->AddTransactions($arrCsv, $arrCol);
            break;
          case 'functions':
            $this->AddFunctions($arrCsv, $arrCol);
            break;
        }
        $this->data['HaveError'] = FALSE;
        $this->data['error'] = 'Chargement effectué avec succès';
        return $this->data;

    }
    public static function parseCSVField($Filename, $Length = 1000000, $Delimiter = ';', $Enclosure = '"', $Escape = '\\', $Mode = 'r')
		{
			if (!empty($Filename))
			{
				$ArrayTitre = array();
				$fp = fopen($Filename, $Mode);
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
    public static function parseCSV($Filename, $Length = 1000000, $Delimiter = ';', $Enclosure = '"', $Escape = '\\', $Mode = 'r')
    {
      if (!empty($Filename))
      {
        $ArrayContentFile = array();
        $fp = fopen($Filename, $Mode);
        $IsFirst = TRUE ;
        if ($fp !== FALSE)
        {
          while (($Data = fgetcsv($fp, $Length, $Delimiter, $Enclosure)) !== FALSE)
          {
            if ($IsFirst) {
              $ArrayLigne = $Data;
              $arrCol = $ArrayLigne;
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
  /**
   * @param array $arrValues
   * @param array $arrCol
   *
   * @return	void
   */
    function AddEmployes($arrValues, $arrCol)
    {
      /** @var array  $colTable */
      $colTable = array('NOUNIQUE' => array_search('NOUNIQUE', $arrCol),
                          'CCODE_U' => array_search('CCODE_U', $arrCol),
                          'CNOM' => array_search('CNOM', $arrCol),
                          'NTARIF' => array_search('NTARIF', $arrCol));
      foreach ($arrValues as $row)
      {
        $query = "INSERT INTO employes
	                  (NOUNIQUE, CCODE_U, CNOM, NTARIF)
	                  VALUES (".$this->db->escape( $row[$colTable['NOUNIQUE']]).", ".$this->db->escape($row[$colTable['CCODE_U']]).
                            ", ".$this->db->escape($row[$colTable['CNOM']]).", ".$this->db->escape(str_ireplace(',','.', $row[$colTable['NTARIF']])).")";
        $this->db->query($query);
      }
    }
  /**
   * @param array $arrValues
   * @param array $arrCol
   *
   * @return	void
   */
    function AddTransactions($arrValues, $arrCol)
    {
      /** @var array  $colTable */
      $colTable = array('CUNAME' => array_search('CUNAME', $arrCol),
                          'DDATE' => array_search('DDATE', $arrCol),
                          'CCODE_P' => array_search('CCODE_P', $arrCol),
                          'SCODE_P' => array_search('SCODE_P', $arrCol),
                          'NTEMPS' => array_search('NTEMPS', $arrCol),
                          'NMONTANT' => array_search('NMONTANT', $arrCol),
                          'CCODE_F' => array_search('CCODE_F', $arrCol));
      foreach ($arrValues as $row)
      {
        $query = "INSERT INTO transactions
	                  (CUNAME, DDATE, CCODE_P, SCODE_P, NTEMPS, NMONTANT, CCODE_F)
	                  VALUES (".$this->db->escape( $row[$colTable['CUNAME']]).", ".$this->db->escape($row[$colTable['DDATE']]).
                            ", ".$this->db->escape($row[$colTable['CCODE_P']]).", ".$this->db->escape($row[$colTable['SCODE_P']]).
                            ", ".$this->db->escape(str_ireplace(',','.', $row[$colTable['NTEMPS']])).
                            ", ".$this->db->escape(str_ireplace(',','.', $row[$colTable['NMONTANT']])).
                            ", ".$this->db->escape($row[$colTable['CCODE_F']]).")";
        $this->db->query($query);
      }
    }
    /**
     * @param array $arrValues
     * @param array $arrCol
     *
     * @return	void
     */
    function AddProjets($arrValues, $arrCol)
    {
      /** @var array  $colTable */
      $colTable = array('CCODE_P' => array_search('CCODE_P', $arrCol),
                          'SCODE_P' => array_search('SCODE_P', $arrCol),
                          'CDESCRIP1' => array_search('CDESCRIP1', $arrCol),
                          'CDESCRIP2' => array_search('CDESCRIP2', $arrCol),
                          'ADV_DOSS' => array_search('ADV_DOSS', $arrCol));
      foreach ($arrValues as $row)
      {
        $query = "INSERT INTO ftprojet
	                  (CCODE_P, SCODE_P, CDESCRIP1, CDESCRIP2, ADV_DOSS)
	                  VALUES (".$this->db->escape( $row[$colTable['CCODE_P']]).", ".$this->db->escape($row[$colTable['SCODE_P']]).
                            ", ".$this->db->escape($row[$colTable['CDESCRIP1']]).", ".$this->db->escape($row[$colTable['CDESCRIP2']]).
                            ", ".$this->db->escape($row[$colTable['ADV_DOSS']]).")";
        $this->db->query($query);
      }
    }
     /**
     * @param array $arrValues
     * @param array $arrCol
     *
     * @return	void
     */
    function AddFunctions($arrValues, $arrCol)
    {
      /** @var array  $colTable */
      $colTable = array('CCODE_U' => array_search('CCODE_U', $arrCol),
                          'FNCOST102' => array_search('FNCOST102', $arrCol));
      foreach ($arrValues as $row)
      {
        $query = "INSERT INTO ftfonct
	                  (CCODE_U, FNCOST102)
	                  VALUES (".$this->db->escape( $row[$colTable['CCODE_U']]).
                            ", ".$this->db->escape(str_ireplace(',','.', $row[$colTable['FNCOST102']])).")";
        $this->db->query($query);
      }
    }
    function __destruct() {
        $this->db->close();
    }
}
