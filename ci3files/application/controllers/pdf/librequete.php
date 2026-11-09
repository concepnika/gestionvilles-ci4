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
  public $textrep;
  public $footergrid;
  public $totalhres;
  public $headerCol;
  public $headerColRep;
  public $from;
  private $isFinished;
  public $infoRequete;
  public $dataREQ;
  protected $widths;
  protected $aligns;
  private $iterHD = 1;
  private $breakOK = false;
  //------------------------------
  function __construct($orientation='P', $unit='mm', $format='LEGAL')
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


  function SetWidths($w)
  {
    // Set the array of column widths
    $this->widths = $w;
  }

  function SetAligns($a)
  {
    // Set the array of column alignments
    $this->aligns = $a;
  }

  function Row($data, $fill)
  {
    $this->SetFillColor(224, 235, 255);
    // Calculate the height of the row
    $nb = 0;
    for ($i = 0; $i < count($data); $i++)
      $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    $h = 5 * $nb;
    // Issue a page break first if needed
    $this->CheckPageBreak($h);
    // Draw the cells of the row
    for ($i = 0; $i < count($data); $i++) {
      $w = $this->widths[$i];
      $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'J';
      // Save the current position
      $x = $this->GetX();
      $y = $this->GetY();
      // Draw the border
      $this->Rect($x, $y, $w, $h);
      // Print the text
      $this->MultiCell($w, 5, $data[$i], 0, $a);
      // Put the position to the right of the cell
      $this->SetXY($x + $w, $y);

    }

    // Go to the next line
    $this->Ln($h);
  }

  function CheckPageBreak($h)
  {
    // If the height h would cause an overflow, add a new page immediately
    if ($this->GetY() + $h > $this->PageBreakTrigger)
      $this->AddPage($this->CurOrientation);
  }
  function NbLines($w, $txt)
  {
    //Computes the number of lines a MultiCell of width w will take
    $cw =& $this->CurrentFont['cw'];
    if ($w == 0)
      $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 and $s[$nb - 1] == "\n")
      $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ')
        $sep = $i;
      $l += $cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j)
            $i++;
        } else
          $i = $sep + 1;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else
        $i++;
    }
    return $nl;
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
		$this->SetY(4);

		$this->SetFont('','B','18');
		$this->Image('./assets/img/logo.jpg',30,10,60,0);
    //" <b> " . $row['dteCedule']
    //                                Statut:<b>" . $row['statut'] . "  </b><br/>
    //                                           <b> Adresse: </b> <br/>
    //                                            " . $row['adresse'] . ' ' . $row['matiere'] . "<br/>
    //                                           <b> Description de la requête: </b> <br/>
    //                                            " . $row['description'] . " <br/>";

    $this->SetY(10);
    $this->SetX(150);
    //$this->SetTextColor(255, 22, 53);
    $this->SetTextColor(0, 64, 124);
    $this->SetFont('', 'B', '18');
    $this->Cell(10,6,utf8_decode('LISTE DES REQUÊTES '),0,0, 'L');
    $this->SetTextColor(0);
		$this->SetDrawColor(128,0,0);
		$w =  array(25, 50, 15, 15, 25, 120);
		$this->SetFont('','','7');
		//$this->Ln();
		$this->SetY(10);
		//----------------------------------------
		$this->SetY(25);
		$this->SetFillColor(0,64,124);
		$this->SetTextColor(255);
		$this->SetDrawColor(0, 64, 124);
		$this->SetLineWidth(.3);
		$this->SetFont('','B','12');
		// En-tête

		if($this->from == 'REQ')
		{//'REQUÉRANT'), utf8_decode('REQUÊTE'), utf8_decode('CÉDULÉE LE'), 'TITRE', 'ADRESSE', utf8_decode('MATÉRIEL'),
                          //'DESCRIPTION', 'STATUT', 'DATE'
			$w =  array(40, 25, 25, 50, 60, 60,  40, 35); // 40, 25, 25, 50, 60, 30, 60, 40, 35
		}
    //$sumw = array_sum($w);
    //$this->Cell(array_sum($w), 7, $this->Titre, 1, 0, 'C', true);

    $this->Ln();
		for($i=0;$i<count($this->headerCol);$i++)
			$this->Cell($w[$i],7,$this->headerCol[$i],1,0,'C',true);
		$this->Ln();
	}

  function HeaderREP()
  {
    // En-tête
    //$this->SetY(4);

    $this->SetFont('', 'B', '18');
    $this->SetDrawColor(128, 0, 0);
    $w = array(25, 50, 15, 15, 25, 120);
    $this->SetFont('', '', '7');
    //$this->Ln();
    //$this->SetY(20);
    //----------------------------------------
    //$this->SetY(55);
    $this->SetFillColor(237, 237, 237);
    $this->SetTextColor(0);
    $this->SetDrawColor(0, 64, 124);
    $this->SetLineWidth(.3);
    $this->SetFont('', 'B', '12');
    // En-tête

    if ($this->from == 'REQ') { //utf8_decode('REQUÉRANT'), utf8_decode('DATE CÉDULÉE'), 'DESCRIPTION', 'CHANGEMENT ADRESSE', 'STATUT', 'DATE'  190
      $w = array(26, 30, 92, 92, 50, 25); // 270
    }
    $this->SetX(30);
    $this->Cell(array_sum($w), 7, 'DISCUSSION', 1, 0, 'C', true);

    $this->Ln();
    $this->SetX(30);
    for ($i = 0; $i < count($this->headerColRep); $i++)
      $this->Cell($w[$i], 7, $this->headerColRep[$i], 1, 0, 'C', true);
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
    $this->iterHD = 1;
		// Couleurs, épaisseur du trait et police grasse
		$this->SetFillColor(255,0,0);
		$this->SetTextColor(255);
		$this->SetDrawColor(0, 64, 124);
		$this->SetLineWidth(.3);
		$this->SetFont('','B','12');
		// En-tête
		$w = array(40, 25, 25, 50, 60, 60,  40, 35);
		if($this->from == 'REQ')
		{
			$w = array(40, 25, 25, 50, 60, 60,  40, 35);

		}
		$this->SetFillColor(224,235,255);
		$this->SetFont('','','10');
		// Données
		$fill = false;
    $line = 0;
		foreach($data as $row)
		{
      $test = $this->GetY();
      if ($test > 185) {
        $this->AddPage();
      }
      if($this->iterHD > 1)
      {
        $this->SetFillColor(0, 64, 124);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 64, 124);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B', '12');
        for ($i = 0; $i < count($this->headerCol); $i++)
          $this->Cell($w[$i], 7, $this->headerCol[$i], 1, 0, 'C', true);
        $this->Ln();
      }
			$this->SetFillColor(224,235,255);
      $this->SetTextColor(255,22,53);
			//$this->Cell($w[0], $row[9],$row[0], 1, 0, 'C', $fill);
      if ($row[0] == 'RDF') {

        $line = $this->GetY();
        $col = $this->GetX();
        $this->Rect($col, $line, 40, $row[9]);
        $this->Image('./assets/img/logo.jpg', $col + 5, $line + 1, 20, 6);
        $this->SetX($col + 40);

      } else {
        $this->Cell($w[0], $row[9], $row[0], 1, 0, 'C', $fill);
      }
      $this->SetFont('', 'B', '10');
      $this->SetTextColor(261, 169, 5);
      $this->Cell($w[1], $row[9], $row[1], 1, 0, 'L', $fill);
      $this->SetTextColor(0);
			$this->Cell($w[2], $row[9], $row[2], 1, 0, 'C', $fill);

      $line = $this->GetY();
      $this->SetFont('', '', '10');
      $this->MultiCell($w[3],  6, $row[3], 1, 'J', $fill);
      $this->SetY($line);
      $this->SetX(150);
      $this->MultiCell($w[4],  6, $row[4], 1, 'J', $fill);
      $this->SetY($line);
      $this->SetFont('', '', '10');
      $this->SetX(210);
      //$this->Cell($w[5], $row[9],$row[5],1,0,'C',$fill);
      //$this->SetY($line);
      //$this->SetFont('', '', '12');
      //$this->SetX(215);
      $this->MultiCell($w[5], 6, $row[5], 1, 'J', $fill);
      $this->SetY($line);
      $this->SetFont('', '', '10');
      $this->SetX(270);
      $this->MultiCell($w[6], 6, $row[6], 1, 'J', $fill);
      $this->SetY($line);
      $this->SetFont('', '', '10');
      $this->SetX(310);
			//$this->Cell($w[6], $row[9], $row[6],1,0,'J',$fill);
			$this->Cell($w[7], $row[9], $row[7],1,0,'C',$fill);

      $this->Ln();
      if ($row[10] > 0)
      {
        $line = $this->GetY();
        $this->SetY($line + 1);
        $this->HeaderREP();
        $this->ReponseTable($row[8]);

      }
      $this->Ln();
      $this->iterHD++;
      //$fill = !$fill;
		}//
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
  function ReponseTable($data)
  {
    // Couleurs, épaisseur du trait et police grasse
    $this->SetFillColor(255, 0, 0);
    $this->SetTextColor(255);
    $this->SetDrawColor(0, 64, 124);
    $this->SetLineWidth(.3);
    $this->SetFont('', 'B', '12');
    // En-tête
    $w = array(26, 30, 92, 92, 50, 25);
    if ($this->from == 'REQ') {
      $w = array(26, 30, 92, 92, 50, 25);

    }
    $this->SetFillColor(224, 235, 255);
    $this->SetFont('', '', '12');
    // Données
    $fill = false;
    foreach ($data as $row) {
      $test = $this->GetY();
      if ($test > 185) {
        $this->AddPage();
      }
      $this->SetX(30);
      $this->SetFillColor(224, 235, 255);
      $this->SetTextColor(255, 22, 53);
      $this->breakOK = false;
      if ($row[0] == 'RDF')
      {
        $line = $this->GetY();
        $col = $this->GetX();
        $this->Rect($col,$line, 26, $row[6] );
        $this->Image('./assets/img/logo.jpg', $col + 1,  $line + 1, 20, 6);
        $this->SetX($col + 26);
      }
      else
      {
        $this->Cell($w[0], $row[6], $row[0], 1, 0, 'C', $fill);
      }

      $this->SetFont('', 'B', '12');
      $this->SetTextColor(237, 177, 0);
      $this->Cell($w[1], $row[6], $row[1], 1, 0, 'L', $fill);
      $this->SetTextColor(0);
      $line = $this->GetY();
      $this->SetFont('', '', '12');
      $this->MultiCell($w[2],7, $row[2], 1, 'J', $fill);
      $this->SetY($line);
      $this->SetX(178);
      $this->MultiCell($w[3], 7, $row[3], 1, 'J', $fill);
      $this->SetY($line);
      $this->SetFont('', '', '12');
      $this->SetX(270);
      $this->Cell($w[4], $row[6], $row[4], 1, 0, 'C', $fill);
      $this->Cell($w[5], $row[6], $row[5], 1, 0, 'C', $fill);
      $this->breakOK = true;
      $this->Ln();
      $fill = !$fill;
    }
    //$this->isFinished = true;
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

		  $this->SetCol(0);
		  $this->SetY(-20);
			$this->SetFillColor(261, 169, 5);
			$this->SetTextColor(255);
      $this->SetFont('','B','14');
      // Numéro de page
      $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C', true);
    $this->iterHD = 1;
			//$this->Cell(339,10,utf8_decode($this->footergrid),1,0,'C',true);
			//$this->Cell(25,10,$this->totalhres,1,0,'C',true);
			//$this->Cell(140,10,'',1,0,'L',true);


  }
}

?>
