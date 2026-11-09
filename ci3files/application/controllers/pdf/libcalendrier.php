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
function HeaderBol()
{
  // En-tête
  $this->SetY(4);
  $this->SetX(1);
  $this->SetFont('','B','14');
	$this->Image('./assets/00_images/logo2.png',15,5,40,0);
  $this->Cell(300,25,'Calendrier des Activités',0,1, 'C');
  $this->SetFont('','','7');

  //$this->Ln();
  $this->SetY(10);
  //----------------------------------------
  $this->SetY(35);

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
function FancyTable($header, $data)
{
	// Couleurs, épaisseur du trait et police grasse
  $iter = 1;
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B','12');
	// En-tête
	$w = array(20, 70, 100);
  //for($i=0;$i<count($header);$i++)
  //  $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
  //$this->Ln();
	// Restauration des couleurs et de la police
	$this->SetFillColor(224,235,255);
	$this->SetFont('','','12');
	// Données
	$fill = false;
  $dateactive = '';
  $monthactif = '';
	foreach($data as $row)
	{
    $date = new DateTime($row[0]);
    $testmonth = $date->format('n');
    $testjour = $date->format('w');
    if ($monthactif != $this->Mois[$testmonth - 1])
    {
      if ($iter > 1)
      {
        $this->AddPage();
        $this->HeaderBol();
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B','12');
      }
      $this->SetFillColor(43,43,43);
      $this->SetTextColor(252);
      $this->Cell(190,7,$this->Mois[$testmonth - 1]. ' '.$date->format('Y'),1,0,'C',true);
      $this->Ln();
      $this->Cell(190,7,'',0,0,'C');
      $this->Ln();
      $this->SetFillColor(255,0,0);
      $this->SetTextColor(255);
      $this->SetDrawColor(128,0,0);
      for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
      $this->Ln();
      //-----------------------------------------------

      $monthactif = $this->Mois[$testmonth - 1] ;
      $iter++;
    }
    if ($dateactive != $row[0])
    {
      $this->SetFillColor(169,176,188);
      $this->SetTextColor(0);
      $this->Cell(190,7,$this->Jour[$testjour]. ' le '.$date->format('j').' '.$this->Mois[$testmonth - 1],1,0,'C',true);
      $this->Ln();
      $this->SetFillColor(255,0,0);
      $this->SetTextColor(255);
      $this->SetDrawColor(128,0,0);
      $dateactive = $row[0];
    }
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $descript = $this->WriteHTML($row[2]);
		$this->Cell($w[0],6,$row[3],0,0,'L');
		$this->Cell($w[1],6,$row[1],0,0,'L');
    //$this->Cell(160,6, utf8_decode($this->WriteHTML($row[2])),0,'J');
    $this->MultiCell($w[2],6,$descript,0,'J');
		//$this->Cell($w[2],6,$descript,'LR',0,'J',$fill);
		//$this->Ln();
		$fill = !$fill;
	}
	// Trait de terminaison
	$this->Cell(array_sum($w),0,'','T');
}
}

?>
