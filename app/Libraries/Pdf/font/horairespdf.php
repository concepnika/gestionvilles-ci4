<?php
require('fpdf.php');
// fonction hex2dec
// retourne un tableau associatif (clés : R,V,B) à
// partir d'un code html de couleur hexa (ex : #3FE5AA)
function hex2dec($couleur = "#000000"){
  $R = substr($couleur, 1, 2);
  $rouge = hexdec($R);
  $V = substr($couleur, 3, 2);
  $vert = hexdec($V);
  $B = substr($couleur, 5, 2);
  $bleu = hexdec($B);
  $tbl_couleur = array();
  $tbl_couleur['R']=$rouge;
  $tbl_couleur['V']=$vert;
  $tbl_couleur['B']=$bleu;
  return $tbl_couleur;
}

//conversion pixel -> millimètre en 72 dpi
function px2mm($px){
  return $px*25.4/72;
}

function txtentities($html){
  $trans = get_html_translation_table(HTML_ENTITIES);
  $trans = array_flip($trans);
  return strtr($html, $trans);
}
//--------------------------------------------------------------
class PDF extends FPDF
{
	//variables du parseur html
  protected $B;
  protected $I;
  protected $U;
  protected $HREF;
  protected $fontList;
  protected $issetfont;
  protected $issetcolor;
  protected $Mois;
  protected $Jour;
  //------------------------------
  public $Titre;
  public $footergrid1;
  public $footergrid2;
  public $totalhres;
  public $headerCol;
  public $from;
  private $isFinished;
  //------------------------------
  function __construct($orientation='P', $unit='mm', $format='A4')
  {
    //Appel au constructeur parent
    parent::__construct($orientation,$unit,$format);
    //Initialisation
    $this->Mois = array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
    $this->Jour = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi");
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
    $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
    $this->issetfont=false;
    $this->issetcolor=false;
    $this->isFinished = false;
  }
	function WriteHTML($html)
  {
    //Parseur HTML
    $this->htmlText = '';
    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><h1><h2><h3><h4>"); //supprime tous les tags sauf ceux reconnus
    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
    foreach($a as $i=>$e)
    {
      if($i%2==0)
      {
        //Texte
        if($this->HREF)
          $this->PutLink($this->HREF,$e);
        else
          $this->Write(5,stripslashes(txtentities($e)));
      }
      else
      {
        //Balise
        if($e[0]=='/')
          $this->CloseTag(strtoupper(substr($e,1)));
        else
        {
          //Extraction des attributs
          $a2=explode(' ',$e);
          $tag=strtoupper(array_shift($a2));
          $attr=array();
          foreach($a2 as $v)
          {
            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
              $attr[strtoupper($a3[1])]=$a3[2];
          }
          $this->OpenTag($tag,$attr);
        }
      }
    }
    return $this->htmlText;
  }

  function OpenTag($tag, $attr)
  {
    //Balise ouvrante
    switch($tag){
      case 'STRONG':
      case 'H':
      case 'H1':
        $this->SetStyle('B',true);
        break;
      case 'EM':
        $this->SetStyle('I',true);
        break;
      case 'B':
      case 'I':
      case 'U':
        $this->SetStyle($tag,true);
        break;
      case 'A':
        $this->HREF=$attr['HREF'];
        break;
      case 'IMG':
        if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
          if(!isset($attr['WIDTH']))
            $attr['WIDTH'] = 0;
          if(!isset($attr['HEIGHT']))
            $attr['HEIGHT'] = 0;
          $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
        }
        break;
      case 'TR':
      case 'BLOCKQUOTE':
      case 'BR':
        //$this->Ln(5);
        break;
      case 'P':
        //$this->Ln(10);
        break;
      case 'FONT':
        if (isset($attr['COLOR']) && $attr['COLOR']!='') {
          $coul=hex2dec($attr['COLOR']);
          $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
          $this->issetcolor=true;
        }
        if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
          $this->SetFont(strtolower($attr['FACE']));
          $this->issetfont=true;
        }
        break;
    }
  }

  function CloseTag($tag)
  {
    //Balise fermante
    if($tag=='STRONG')
      $tag='B';
    if($tag=='H1')
      $tag='B';
    if($tag=='H')
      $tag='B';
    if($tag=='EM')
      $tag='I';
    if($tag=='B' || $tag=='I' || $tag=='U')
      $this->SetStyle($tag,false);
    if($tag=='A')
      $this->HREF='';
    if($tag=='FONT'){
      if ($this->issetcolor==true) {
        $this->SetTextColor(0);
      }
      if ($this->issetfont) {
        $this->SetFont('arial');
        $this->issetfont=false;
      }
    }
  }

  function SetStyle($tag, $enable)
  {
    //Modifie le style et sélectionne la police correspondante
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
    {
      if($this->$s>0)
        $style.=$s;
    }
    $this->SetFont('',$style);
  }

  function PutLink($URL, $txt)
  {
    //Place un hyperlien
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
  }
	// Chargement des données
	function LoadData($file)
	{
		// Lecture des lignes du fichier
		$lines = file($file);
		$data = array();
		foreach($lines as $line)
			$data[] = explode(';',trim($line));
		return $data;
	}
	function Header()
	{
		// En-tête
    $this->Rect(10, 4, 276, 22 );
		$this->SetY(4);
		$this->SetX(1);
		$this->SetFont('','B','14');
		$this->Image('./assets/00_images/logo.jpg',15,5,40,0);

		$this->Cell(275,25,$this->Titre,0,0, 'C');
		$this->SetDrawColor(128,0,0);
		$w =  array(25, 50, 15, 15, 25, 120);
		$this->SetFont('','','7');
		//$this->Ln();
		$this->SetY(10);
		//----------------------------------------
		$this->SetY(27);
		$this->SetFillColor(68,149,162);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B','12');
		// En-tête

		$w =  array(28, 14, 40, 40, 90, 64);

		for($i=0;$i<count($this->headerCol);$i++)
			$this->Cell($w[$i],10,$this->headerCol[$i],1,0,'C',true);
		$this->Ln();
	}
	// Tableau simple
	function BasicTable($header, $data)
	{
		// En-tête
		foreach($header as $col)
			$this->Cell(40,7,$col,1);
		$this->Ln();
		// Données
		foreach($data as $row)
		{
			foreach($row as $col)
				$this->Cell(40,6,$col,1);
			$this->Ln();
		}
	}

	// Tableau amélioré
	function ImprovedTable($header, $data)
	{
		// Largeurs des colonnes
		$w = array(40, 35, 45, 40);
		// En-tête
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C');
		$this->Ln();
		// Données
		foreach($data as $row)
		{
			$this->Cell($w[0],6,$row[0],'LR');
			$this->Cell($w[1],6,$row[1],'LR');
			$this->Cell($w[2],6,number_format($row[2],0,',',' '),'LR',0,'R');
			$this->Cell($w[3],6,number_format($row[3],0,',',' '),'LR',0,'R');
			$this->Ln();
		}
		// Trait de terminaison
		$this->Cell(array_sum($w),0,'','T');
	}

	// Tableau coloré
	function FancyTable($data)
	{
		// Couleurs, épaisseur du trait et police grasse
		$this->SetFillColor(255,0,0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B','14');
		// En-tête
		$w = array(28, 14, 40, 40, 90, 64); //array(28, 14, 46, 100, 88);
		$this->SetFillColor(224,235,255);
		$this->SetFont('','','14');
		// Données
		$fill = false;
    $iter = 1;
		foreach($data as $row)
		{
			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);
      if($iter == 1)
      {
 			   $this->Cell($w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5] ,1,'',0,0,'C',$fill);
          $iter++;
         $this->Ln();
      }
			$this->Cell($w[0],12,$row[0],1,0,'C',$fill);
			$this->Cell($w[1],12,$row[1],1,0,'C',$fill);
      $this->Cell($w[2],12,$row[2],1,0,'L',$fill);
      $this->Cell($w[3],12,$row[3],1,0,'L',$fill);
      //$len = strlen($row[4]);
      $arrCorrecteur = explode(',', substr($row[4],0, strlen($row[4])));
      $correcteur1 = '';
      $correcteur2 = '';
      if(count($arrCorrecteur) > 3)
      {
        $correcteur1 = $arrCorrecteur[0] .','. $arrCorrecteur[1] .','. $arrCorrecteur[2] ;
        for ($i = 3;$i < count($arrCorrecteur); $i++)
        {
          if (trim($arrCorrecteur[$i]) != '')
            $correcteur2 .= $arrCorrecteur[$i].',';
        }
        $y = $this->GetY();
        $x = $this->GetX();
        $this->Cell($w[4],6,$correcteur1,'LTR',1,'L',$fill);

        $this->SetXY(132,$y + 6);
        $this->Cell($w[4],6,substr($correcteur2,0,strlen($correcteur2)),'LBR',0,'L',$fill);
        $this->SetXY(222,$y);
      }
      else
      {
			  $this->Cell($w[4],12,$row[4],1,0,'L',$fill);
      }
      $len = strlen($row[5]);
      $arrRamasseur = explode(',', substr($row[5],0, strlen($row[5])));
      $Ramasseur1 = '';
      $Ramasseur2 = '';
      if(count($arrRamasseur) > 2)
      {
        $Ramasseur1 = $arrRamasseur[0] .','. $arrRamasseur[1] ;
        for ($i = 2;$i < count($arrRamasseur); $i++)
        {
          if (trim($arrRamasseur[$i]) != '')
            $Ramasseur2 .= $arrRamasseur[$i].',';
        }
        $y = $this->GetY();
        $x = $this->GetX();
        $this->Cell($w[5],6,$Ramasseur1,'LTR',1,'L',$fill);

        $this->SetXY(198,$y + 6);
        $this->Cell($w[5],6,substr($Ramasseur2,0,strlen($Ramasseur2)),'LBR',0,'L',$fill);
        //$this->SetXY(196,$y);
      }
      else
      {
			  $this->Cell($w[5],12,$row[5],1,0,'L',$fill);
      }
			$fill = !$fill;
      $this->Ln();
		}
    $this->isFinished = true;
		//$this->Ln();
		//$this->Ln();
		//$this->Ln();
		//$this->SetFillColor(68,149,162);
		//$this->SetTextColor(255);
		//$this->Cell(105,6,$this->footergrid,1,0,'C',true);
		//$this->Cell(25,6,$this->totalhres,1,0,'C',true);
		//$this->Cell(140,6,'',1,0,'L',true);
		//$this->Ln();
		//$this->Ln();
		//// Trait de terminaison
		//$this->Cell(array_sum($w),0,'','T');
	}
	function SetCol($col)
	{
		// Positionnement sur une colonne
		$this->col = $col;
		$x = 10+$col*30;
		$this->SetLeftMargin($x);
		$this->SetX($x);
	}
  function Footer()
  {

      //$this->SetCol(0);
      //$this->SetY(-10);
      //$this->SetFillColor(68,149,162);
      //$this->SetTextColor(255);
      //$this->SetFont('','B','14');
      //$this->Cell(96+180,6,$this->footergrid1,1,0,'L',true);
      //$this->SetCol(0);
      //$y = $this->GetY();
      //$this->SetY($y + 6);
      //$this->Cell(96+180,6,'','B',0,'C',true);



  }
}

?>
