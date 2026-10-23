<?php

namespace App\Libraries;

use Config\Database;

/**
 * Port de application/libraries/LibCalendrier.php (CI3).
 * $this->CI->db -> $this->db ; $this->CI->input->post_get ->
 * service('request')->getPostGet(). HTML genere identique.
 */
class LibCalendrier
{
    /** @var \CodeIgniter\Database\BaseConnection */
    private $db;

    /** @var array $data */
    public $data;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getCalendrierVille($idville, $activeDate = '', $addtomois = '')
    {
        /** @var string $returnval */
        $returnval = '';
        $intDayOfMonth = date('t');
        $today = new \Datetime('NOW');

        $DayOfWeek = date('Y') . '-' . date('m') . '-01';
        $SelectedDate = date('Y') . '-' . date('m') . '-01';
        //date_add($today,date_interval_create_from_date_string("1 month"));
        $dateToScroll = date_create($activeDate);
        $monthtoshow = $dateToScroll->format('m');
        $mois = $monthtoshow;
        $intYear = $dateToScroll->format('Y');
        $activeYear = $intYear;
        if ($activeDate != '') {
            $newdate = new \DateTime($activeDate);
            $intDayOfMonth = $newdate->format('t');
            $DayOfWeek = $newdate->format('Y-m-01');
            $SelectedDate = $newdate->format('Y-m-d');
        }
        $scriptQuery = '<script>
					$.fx.speeds._default = 100;

					$(function() {
						$( "#%s" ).dialog({
							autoOpen: false,
							show: "blind",
							width: 600,
							height:450,
							 modal: true
						});

						$( "#%s" ).click(function() {
							$( "#%s" ).dialog({
							autoOpen: false,
							show: "blind",
							width: 600,
							height:450,
							 modal: true
						});
							$( "#%s" ).dialog( "open" );
							return false;
						});
					});
					</script>
					<div id="%s" style="x-large" title="%s">

					</div>
					';
        //-----------------------------------------------------------------
        $array = ["Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet",
            "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre"];
        $listMois = '';
        $listYear = '';

        for ($iter = 1; $iter < 13; $iter++) {
            $tmp = (($iter == intval($monthtoshow)) ? ' selected ' : '');
            $listMois .= '<option ' . $tmp . ' value="' . (($iter < 10) ? '0' . $iter : $iter) . '">' . $array[$iter - 1] . '</option>';
        }
        for ($iter = $intYear; $iter < $intYear + 4; $iter++) {
            $tmp = (($iter == $activeYear) ? ' selected' : '');
            $listYear .= '<option ' . $tmp . ' value="' . $iter . '">' . $iter . '</option>';
        }
        $this->data['listmois'] = $listMois;
        $this->data['listyear'] = $listYear;
        $returnval = '<form name="frmCalendrier" id="frmCalendrier" method="post" action="">

			<table class="tableCalendrier" width="2000" >
				<tr><td colspan="7" class="tdcalendrierHeader">' . $array[intval($monthtoshow) - 1] . ' ' . $activeYear . '</td>	</tr>
				<tr><td  class="tdcalendrierHeader">Dim</td>
				<td  class="tdcalendrierHeader">Lundi</td><td class="tdcalendrierHeader">Mar</td>
					<td class="tdcalendrierHeader">Mer</td><td class="tdcalendrierHeader">Jeu</td>
					<td class="tdcalendrierHeader">Ven</td><td class="tdcalendrierHeader">Sam</td>
				</tr>';
        //$returnval = '' ;
        $DayStart = 1;
        $dateMonth = new \DateTime($DayOfWeek);
        $DayOfWeek = date_format($dateMonth, 'w');
        $dateToScroll = date_create($dateMonth->format('Y-m-01'));
        for ($iterLigne = 1; $iterLigne < 7; $iterLigne++) {
            $returnval .= '<tr>';
            if ($DayOfWeek > 0) {
                for ($iter = 0; $iter < $DayOfWeek; $iter++) {
                    $returnval .= '<td valign="top" class="tdcalendrier"> <table> <tr>
														<td valign="top" align="right" >
														<input type="text" readonly="readonly" class="tdjour" value="" /> </td> </tr>';

                    $returnval .= '<tr>
														<td class="tdinfo">&nbsp;&nbsp;&nbsp;&nbsp;</td>
													</tr></table> 	</td>';
                }
            }

            for ($iter = $DayOfWeek; $iter <= 6; $iter++) {
                $manuel = service('request')->getPostGet('manuel');
                $this->data['manuel'] = $manuel;
                $onclick = '';
                if ($manuel == '1') {
                    $onclick = 'onclick="AddRamassage(' . date_format($dateToScroll, "Ymd") . ')"';
                }
                $returnval .= '<td id="td_' . date_format($dateToScroll, "Ymd") . '" valign="top" class="tdcalendrier"> <table> <tr>
														<td valign="top" align="right" >
														<label ' . $onclick . '  ondrop="dropCollecte(event, ' . $DayStart . ')" ondragover="allowDrop(event)" class="form-control tdjour" >' . $DayStart . '</label> </td> </tr>';
                $strSQL = "SELECT tblvillesdateramassage.Id, idville, Dateramassage, TypeRamassage,typecam,
(SELECT typededechet.couleur FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) AS couleur,
(SELECT typededechet.textcolor FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) AS textcolor
													FROM tblvillesdateramassage INNER JOIN ville
															ON tblvillesdateramassage.idville = ville.id
															WHERE idville = $idville AND MONTH(Dateramassage) = MONTH('$SelectedDate')
																									AND YEAR(Dateramassage) = YEAR('$SelectedDate')
																									and DAY(Dateramassage) = $DayStart order by tblvillesdateramassage.Id";
                $calendrier = $this->db->query($strSQL);
                $num_row = $calendrier->getNumRows();
                $iterRow = 1;
                if ($num_row > 0) {
                    foreach ($calendrier->getResult() as $row) {
                        $titre = '';

                        $titre = (($row->typecam == 1) ? 'RE' : 'RO');
                        $returnval .= '<tr>
								<td class="tdinfo"><div id="divCollecte_' . date_format($dateToScroll, "Ymd") . '_' . $row->Id . '" class="btn btn-default" draggable="true" ondragstart="drag(event)"
										style="width:40px;height:30px;font-size:medium;background-color:' . $row->couleur . ';
										color:' . $row->textcolor . ';font-weight: bold;text-align:center;">' . $titre . '</div></td>
								</tr>';
                        $iterRow++;
                    }
                }
                $returnval .= '</table></td>';
                $DayStart++;
                date_add($dateToScroll, date_interval_create_from_date_string("1 days"));
                if ($DayStart > $intDayOfMonth) {
                    break;
                }
            }
            $returnval .= '</tr>';
            $DayOfWeek = 0;
            //$DayStart += 1;
            if ($DayStart > $intDayOfMonth) {
                break;
            }
        }
        $returnval .= '</table></form>';
        $this->data['calendrier' . $addtomois] = $returnval;
        $this->GetRevenu($idville);
        $this->data['nomenclature'] = '';
        $this->GetSecteurs();
        $this->data['optvilles'] = $this->GetOptionVilles($idville);
        $this->data['optramassages'] = $this->GetOptionRamassage();
        return $this->data;
    }

    public function GetRevenu($idville)
    {
        /** @var string $revenu */
        $revenu = '<table class="table table-bordered table-striped mb-0"
                          id="datatable-tabletools">
                          <thead>
                            <tr style="color:white;background-color:darkblue;">
                              <th align="center" style="text-transform: uppercase;text-align:center;">Type de collecte</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Nb Collecte</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Collecte</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Redevance</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Disposition</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">A la tonne</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">A l\'heure</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Au Bac</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;"></th>
                            </tr>
                          </thead>
                          <tbody id="tboRevenu">';
        $strSQL = "SELECT DISTINCT tblvillesdateramassage.TypeRamassage,
CONCAT('<td><label style=\"max-width:110px;width:110px;height:30px;max-height:30px;margin-top:4px\"><b>',
(SELECT Nom FROM typededechet WHERE typededechet.id =
			tblvillesdateramassage.TypeRamassage)
,'</b><span class=\"form-control input-group-addon\" style=\"width:30px;height:30px;max-height:30px;margin-top:4px;background-color:',
(SELECT typededechet.couleur FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage)
,';color:',
(SELECT typededechet.textcolor FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage)
,';\"></span></label></td>') AS Nomenclature,
			(SELECT typededechet.couleur FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) AS couleur,
			(SELECT typededechet.textcolor FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) AS textcolor,
			(SELECT Nom FROM typededechet WHERE typededechet.id =
			tblvillesdateramassage.TypeRamassage) AS NomDechet,
			IFNULL(revenuville.MontFixe, 0) as MontFixe, IFNULL(revenuville.inHour, 0) as inHour, revenuville.nbCollecte, IFNULL(revenuville.MontRedevance, 0) as MontRedevance,  IFNULL(revenuville.MontTonne, 0) as MontTonne,  IFNULL(revenuville.Disposition, 0) as Disposition,
			IFNULL(revenuville.AuBac, 0) as AuBac
			FROM tblvillesdateramassage LEFT JOIN revenuville ON revenuville.idville = tblvillesdateramassage.idville AND revenuville.TypeRamassage = tblvillesdateramassage.TypeRamassage
			WHERE tblvillesdateramassage.idville = $idville
												ORDER BY (SELECT Nom FROM typededechet
											WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) ";
        $result = $this->db->query($strSQL);
        foreach ($result->getResult() as $row) {
            //$fixe = (($row->IsFixeMont == 1) ?" checked ":"");
            //$hred = (($row->HaveRedevance == 1) ?" checked ":"");
            //id, idville, inHour, Disposition, MontFixe, AuBac, MontRedevance, MontTonne, TypeRamassage, nbCollecte
            $revenu .= '<tr>
                    <td> <Label style="width:130px;height:30px;max-height:30px;margin-top:4px;background-color:' . $row->couleur . ';color:' . $row->textcolor . ';">
										<b>' . $row->NomDechet . '</b></label></td>
                    <td><input class="form-control"  style="width: 80px; height: 30px;"  type="text" id="nbCollecte_' . $row->TypeRamassage . '" value="' . $row->nbCollecte . '" /></td>
                    <td><input type="text" class="form-control" style="width: 80px; height: 30px;" id="MontFixe_' . $row->TypeRamassage . '" value="' . round($row->MontFixe, 2) . '" placeholder="montant" /></td>
                    <td><input type="text" class="form-control" style="width: 80px; height: 30px;" id="MontRedevance_' . $row->TypeRamassage . '" value="' . round($row->MontRedevance, 2) . '" placeholder="montant" /></td>
                    <td><input type="text" class="form-control" style="width: 80px; height: 30px;" id="Disposition_' . $row->TypeRamassage . '" value="' . round($row->Disposition, 2) . '" placeholder="montant" /></td>
                    <td><input type="text" class="form-control" style="width: 80px; height: 30px;" id="MontTonne_' . $row->TypeRamassage . '" value="' . round($row->MontTonne, 2) . '" placeholder="montant" /></td>
                    <td><input type="text" class="form-control" style="width: 80px; height: 30px;" id="inHour_' . $row->TypeRamassage . '" value="' . round($row->inHour, 2) . '" placeholder="montant" /></td>
                    <td><input type="text" class="form-control" style="width: 80px; height: 30px;" id="AuBac_' . $row->TypeRamassage . '" value="' . round($row->AuBac, 2) . '" placeholder="montant" /></td>
										<td><a style="font-size:12px;color: #03F;cursor:pointer;width: 30px;max-height:30px;"
                        onclick="sendRev(' . $row->TypeRamassage . ')" title="Enregistrer"><i class="fa fa-check btn btn-success" style="font-size:12px;"></i></a></td>
                  </tr>';
        }
        $revenu .= '</tbody></table>';
        $this->data['revenu'] = $revenu;
    }

    public function GetOptionVilles($idville = 0)
    {
        /** @var string $returnval */
        $returnval = '';
        /** @var string $strSQL */
        $strSQL = "SELECT Nom, id FROM ville where isdeleted = 0 ORDER BY Nom ";
        $villes = $this->db->query($strSQL);
        foreach ($villes->getResult() as $row) {
            $selected = '';
            if (trim($idville) == trim($row->id)) {
                $selected = ' selected ';
            }

            $returnval .= '<option ' . $selected . ' value="' . $row->id . '" >' . $row->Nom . '</option>';
        }
        return $returnval;
    }

    public function GetOptionRamassage($id = 2)
    {
        /** @var string $returnval */
        $returnval = '';

        /** @var string $strSQL */
        $strSQL = "SELECT Nom, id FROM typededechet WHERE isdeleted = 0 ORDER BY Nom";
        $villes = $this->db->query($strSQL);
        foreach ($villes->getResult() as $row) {
            $selected = '';
            if (trim($id) == trim($row->id)) {
                $selected = ' selected ';
            }

            $returnval .= '<option ' . $selected . ' value="' . $row->id . '" >' . $row->Nom . '</option>';
        }

        return $returnval;
    }

    public function GetSecteurs()
    {
        /** @var string $returnval */
        $returnval = '';
        /** @var string $strSQL */
        $strSQL = "SELECT id, name FROM villes_zones order BY name";
        $ville = $this->db->query($strSQL);
        foreach ($ville->getResult() as $row) {
            $returnval .= '<option  value="' . $row->id . '">' . $row->name . '</option>';
        }
        $this->data['secteurs'] = $returnval;
    }
}
