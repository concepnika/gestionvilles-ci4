<?php

namespace App\Models;

/**
 * Port mecanique de Polyline_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Google_model
 * @author: Serge Dallaire
 */
class PolylineModel extends Ci3Model
{

  function __construct() {
      parent::__construct();
      
			

  }
  /**
   *
   * @param int $idville
   *
   *
   * @return string
   */
  public function GetVilles($idville)
  {
    /** @var string $returnval */
    $returnval = '';
    /** @var string $strSQL */
    $strSQL = "SELECT Nom, id FROM ville where isdeleted = 0  ORDER BY Nom";
    $ville = $this->db->query($strSQL);
    foreach ($ville->getResult() as $row) {

      $selected = ($row->id == $idville) ? "selected" : '';

      $returnval .= '<option style="font-weight:bold;" ' . $selected . ' value="' . $row->id . '" >' . $row->Nom . '</option>';
    }
    return $returnval;
  }
  /**
   *
   * @param int $idville
   *
   *
   * @return string
   */
  public function GetAllRoute($idville,$from=0)
  {
    //----------------------------------------------------------------------
    /** @var string $returnval */
    $returnval = '';
    /** @var object $row */
    /** @var string $strSQL */
    $strSQL = "SELECT id, idville, Nom, `Description`, geoRoutes, isdeleted FROM routes
                     WHERE isdeleted = 0 and idville = $idville ORDER BY Nom";
    /** @var object $resultLine */
    $resultLine = $this->db->query($strSQL);
    /** @var object $row */
    foreach ($resultLine->getResult() as $row) {
      /** @var string $geoRoutes */
      $geoRoutes = ' <a title="Cliquer pour afficher la route"  target="_blank" href="/sanivan/showroute/' . $row->idville .'/'.$row->id. '">
										           &nbsp;<i class="fa fa-road" style="font-size:16px;"></i>&nbsp;Route</a>';
      /** @var string $edit */
      $edit = ' <a title="Cliquer pour enregistrer" onclick="postRoute(' . $row->id . ')">
										           &nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a>';
      $checked = (($row->isdeleted == 1) ? ' checked ' : '');
      $enligne = '<input class="btn"  style="width: 20px;height:20px;" onclick="setDeleted()" id="chkdel_' . $row->id . '" ' . $checked . '
                                  type="checkbox"  />';
      $returnval .= '<tr><td><label style="font-size: 14px;">' . $enligne . '</label></td>
                    <td align="center"><input class="form-control" style="width: 230px" id="edtNom_' . $row->id . '" type="text" value="' . $row->Nom . '" /></td>
                    <td align="center"><input class="form-control" style="width: 400px" id="edtDescription_' . $row->id . '" type="text" value="' . $row->Description . '" /></td>
                    <td  align="center">' . $geoRoutes . '</td>
                    <td  align="center">' . $edit . '</td>
</tr>';
    }
    if($from == 1)
    {
      return $returnval;
    }
    else{
      return $returnval;
    }

  }
  public function PostRoutes($id)
  {
    $Nom = $this->db->escape($this->input->get('Nom'));
    $Description = $this->db->escape($this->input->get('Description'));
    $idville = $this->db->escape($this->input->get('idville'));
    /** @var string  $strSQL */
    switch ($id)
    {
      case 0:
        $strSQL = "INSERT INTO routes (idville, Nom, Description)
	                      VALUES ($idville, $Nom, $Description)";
        $this->db->query($strSQL);
        //$dataResult['id'] = $this->db->insertID();
        break;
      default:
        $geoRoutes = $this->db->escape($this->input->get('geoRoutes'));
        $isdel = $this->input->get('isdel');
        $strSQL = "UPDATE routes
	                      SET
		                      idville=$idville,
		                      Nom=$Nom,
		                      Description=$Description,
		                      geoRoutes=$geoRoutes,
                          isdeleted = $isdel
	                      WHERE id =  $id";
        $this->db->query($strSQL);
        //$dataResult['id'] = $id;
    }
    //$error = $this->db->error();
    //if (!empty($error['code'])) {
    //  $dataResult['msgerr'] = "Une erreur s'est produite.";
    //  $dataResult['id'] = 0;
    //} else {
    //  $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
    //}
    echo $this->GetAllRoute($idville, 1);
  }
  public function PostCoordRoutes($id)
  {
    $data = $this->input->post(NULL, TRUE);
    /** @var string  $strSQL */
    $geoRoutes = $this->db->escape(urldecode($data['field1']));
    $placeId = $this->db->escape(urldecode($data['field2']));
    $strSQL = "UPDATE routes
	                  SET
		                  geoRoutes=$geoRoutes,
                      placeId=$placeId
	                  WHERE id =  $id";
    $this->db->query($strSQL);

    echo 'OK';
  }
  private function ToObject($Array) {

    // Create new \stdClass object
    $object = new \stdClass();

    // Use loop to convert array into object
    foreach ($Array as $key => $value) {
      if (is_array($value)) {
        $value = $this->ToObject($value);
      }
      $object->$key = $value;
    }
    return $object;
  }
  public function StructurePolyline($id)
  {
    $query = "SELECT parcours, urlfile
	                    FROM polilyne_ville where id = $id";
    $polyline = $this->db->query($query)->getRowArray();
    $arrNew = '';
    $polyline['parcours'] = str_replace(',]', '', $polyline['parcours']);
    $arrLocation = explode('},{', $polyline['parcours']);
    //$center = substr($arrLocation[0], 1, strlen($arrLocation[0])) . '}';
    for ($iter = 0; $iter < count($arrLocation); $iter++) {
      if (substr($arrLocation[$iter], 0, 2) != '[{') {
        $arrTem = explode(',', $arrLocation[$iter]);
        $lat = str_replace('lat: ', '', $arrTem[0]);
        $lng = str_replace('lng: ', '', $arrTem[1]);
        $arrNew .= $lat . "," . $lng . "_";
      }
    }
    unset($arrLocation[0]);
    // Remove the last row
    unset($arrLocation[count($arrLocation) - 1]);
    $arrNew = str_replace("}_", "", $arrNew);
    $arrNew = $this->db->escape($arrNew);
    $strSQL = "UPDATE polilyne_ville
                    SET
                      parcours=$arrNew
                    WHERE id =  $id";
    $this->db->query($strSQL);
  }
  public function ReduitPolyline($id)
  {
    $query = "SELECT parcours, urlfile
	                    FROM polilyne_ville where id = $id";
    $tmp = '';
    $polyline = $this->db->query($query)->getRowArray();

    //$polyline['parcours'] = str_replace(',]', '', $polyline['parcours']);
    $arrLocation = explode('_', $polyline['parcours']);
    //$center = substr($arrLocation[0], 1, strlen($arrLocation[0])) . '}';
    for ($iter = 0; $iter < count($arrLocation); $iter++) {
        $arrTem = explode(',', $arrLocation[$iter]);
        $arrLocation[$iter] = array( 'x' => $arrTem[0], 'y'=> $arrTem[1]);
    }
    $tol = '0.00001';
    $flo = floatval($tol);
    $data = $this->douglasPeucker($arrLocation, $flo);
    $arrNew = "";
    foreach ($data as $key => $value) {
      $arrNew .= $value['x'].",".$value['y']."_";
    }
    $arrNew = str_replace("}_", "", $arrNew);
    
    //$obj = $this->ToObject($data);
    //$json = json_encode($data);
    //return $arrNew;
    $arrNew = $this->db->escape($arrNew);
    $strSQL = "UPDATE polilyne_ville
                    SET
                      parcours_reduit=$arrNew
                    WHERE id =  $id";
    $this->db->query($strSQL);
  }
  /**
   * Calcule la distance perpendiculaire d'un point � la ligne d�finie par deux autres points.
   *
   * @param array $point     Le point dont on veut mesurer la distance : ['x' => ..., 'y' => ...]
   * @param array $lineStart Le point de d�part de la ligne.
   * @param array $lineEnd   Le point d'arriv�e de la ligne.
   * @return float           La distance perpendiculaire.
   */
  private function perpendicularDistance(array $point, array $lineStart, array $lineEnd): float
  {
    $dx = $lineEnd['x'] - $lineStart['x'];
    $dy = $lineEnd['y'] - $lineStart['y'];

    // Si les deux points de la ligne sont identiques, retourner la distance euclidienne.
    if (abs($dx) < 1e-10 && abs($dy) < 1e-10) {
      return sqrt(pow($point['x'] - $lineStart['x'], 2) + pow($point['y'] - $lineStart['y'], 2));
    }

    // Calcul du num�rateur et du d�nominateur pour la distance perpendiculaire.
    $numerator = abs($dy * $point['x'] - $dx * $point['y'] + $lineEnd['x'] * $lineStart['y'] - $lineEnd['y'] * $lineStart['x']);
    $denom = sqrt($dx * $dx + $dy * $dy);

    return $numerator / $denom;
  }

  /**
   * Simplifie une polyline � l'aide de l'algorithme de Douglas-Peucker.
   *
   * @param array $points     Liste des points de la polyline.
   * @param float $tolerance  La tol�rance (distance maximale) � laquelle un point peut �tre ignor�.
   * @return array            La polyline simplifi�e.
   */
  private function douglasPeucker(array $points, float $tolerance): array
  {
    $n = count($points);
    if ($n < 3) {
      return $points;
    }

    $maxDistance = 0.0;
    $index = 1;
    $endIndex = $n - 1;

    // Boucle sur les points interm�diaires pour trouver celui qui est le plus �loign� de la ligne reliant le premier et le dernier point.
    for ($i = 1; $i < $endIndex; $i++) {
      $arrX = $points[$i];
      $arrY = $points[1];
      $arrZ = $points[$endIndex];
      $distance = $this->perpendicularDistance($arrX, $arrY, $arrZ);
      if ($distance > $maxDistance) {
        $maxDistance = $distance;
        $index = $i;
      }
    }

    // Si la distance maximale trouv�e d�passe la tol�rance, on d�clenche la r�cursivit�.
    if ($maxDistance > $tolerance) {
      // On simplifie la partie de la premi�re extr�mit� au point critique.
      $firstHalf = $this->douglasPeucker(array_slice($points, 1, $index + 1), $tolerance);
      // On simplifie la partie du point critique jusqu'� la fin.
      $secondHalf = $this->douglasPeucker(array_slice($points, $index), $tolerance);

      // Fusion des r�sultats en �vitant de dupliquer le point critique.
      return array_merge(
        array_slice($firstHalf, 1, count($firstHalf) - 1),
        $secondHalf
      );
    } else {
      // Si aucun point n'est suffisamment �loign�, on ne garde que le premier et le dernier.
      return [$points[1], $points[$endIndex]];
    }
  }
  public function __destruct() {
      $this->db->close();
  }
}
