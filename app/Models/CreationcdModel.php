<?php

namespace App\Models;

/**
 * Port partiel de Creationcd_model (CI3) — seules les methodes
 * utilisees par les pages deja portees sont reproduites.
 * Les requetes SQL et le HTML genere sont copies a l'identique.
 */
class CreationcdModel extends Ci3Model
{
    /**
     * Camions glissables de l'accueil (avec statut en cours).
     * CI3 : Creationcd_model::GetHomeCamions
     */
    public function GetHomeCamions($intype = '1')
    {
        $returnval = '';
        $strSQL = "SELECT  IFNULL((select CAST(sc.idstatut as CHAR(10) )
			            from suiviscamion sc where sc.nocamion = c.NoCamion and dateend is null LIMIT 0, 1 ), '1')  as StatutCam ,
			            CONCAT( If(c.IsAutomatique = 1, 'A', ''), If(c.IsManuel = 1, 'M', ''), If(c.IsVerseur = 1, 'V', ''),
		               If(c.HaveWinch = 1, 'W', ''), If(c.HaveRollBar = 1, 'R', '')) as CamPropriete,
		              c.NoCamion FROM camions c where  c.isdeleted = 0 and c.nocamion <> ''
		              AND TypeCamion  in ($intype)
		              order by TypeCamion, NoCamion";
        $ville = $this->db->query($strSQL);
        foreach ($ville->getResult() as $row) {
            $backColor = '';
            if ($row->StatutCam == 2) {
                $backColor = 'background-color: chocolate;color:darkblue;';
            }
            $returnval .= '<div id="camion_' . $row->NoCamion . '_0" draggable="true" ondragstart="drag(event)" class="bg-light-green" style="padding-left: 25px;' . $backColor . '" >' . $row->NoCamion . ' | ' . $row->CamPropriete . '</div>';
        }
        $this->data['CamionDrop'] = $returnval;
        return $returnval;
    }

    /**
     * Chauffeurs glissables disponibles a la date (accueil).
     * CI3 : Creationcd_model::GetChauffeursToDropHome
     */
    public function GetChauffeursToDropHome($activedate, $intype = '1,3')
    {
        $where = '';
        if ($this->input->post_get('action') != 2) {
            $date = date_create($activedate);
            $day_of_week = intval($date->format('w'));
            $where = ' and reshumaine.' . $this->JourDeSemaine[$day_of_week] . ' = 1 ';
        }
        $activedate = $this->db->escape($activedate);
        $returnval = '';
        $strSQL = "Select reshumaine.id as idchauffeur,
                   CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine
			             WHERE  reshumaine.isdeleted = 0 and idreshum in ($intype) $where ORDER BY reshumaine.Nom, reshumaine.Prenom";
        $ville = $this->db->query($strSQL);
        foreach ($ville->getResult() as $row) {
            $returnval .= '<div id="chauffeur_' . $row->idchauffeur . '_2" draggable="true" ondragstart="drag(event)" class="bg-light-red" style="padding-left: 10px;" >' . $row->NomComplet . '</div>';
        }
        $this->data['ChauffeurDrop'] = $returnval;
        $this->objData->ChauffeurDrop = $returnval;
        return $returnval;
    }

    /**
     * Aides glissables disponibles a la date (accueil).
     * CI3 : Creationcd_model::GetAidesToDrop
     */
    public function GetAidesToDrop($activedate)
    {
        $where = '';
        if ($this->input->post_get('action') != 2) {
            $date = date_create($activedate);
            $day_of_week = intval($date->format('w'));
            $where = ' and reshumaine.' . $this->JourDeSemaine[$day_of_week] . ' = 1 ';
        }
        $activedate = $this->db->escape($activedate);
        $returnval = '';
        $strSQL = "Select reshumaine.id as idaide,
                  if(reshumaine.DIM = 0, 'background-color:lightgoldenrodyellow','') AS background,
                   CONCAT (reshumaine.Nom, ', ', SUBSTRING( reshumaine.Prenom, 1, 1) ) as NomComplet From reshumaine
			             WHERE
			             ( Select idrh From suivisrh srh
                       WHERE srh.idrh = reshumaine.id AND ( dateend > $activedate  or dateend is null )  and
							  datestart <= $activedate LIMIT 0, 1 ) is null
						            AND reshumaine.idreshum IN (2 ) AND reshumaine.isdeleted = 0 $where ORDER BY reshumaine.Nom, reshumaine.Prenom";
        $ville = $this->db->query($strSQL);
        foreach ($ville->getResult() as $row) {
            $titre = (($row->background == '') ? '' : 'En congé');
            $returnval .= '<div id="aide_' . $row->idaide . '_3" title="' . $titre . '" draggable="true" ondragstart="drag(event)" class="bg-light-red" style="padding-left: 10px;' . $row->background . '" >' . $row->NomComplet . '</div>';
        }
        $this->data['AideDrop'] = $returnval;
        $this->objData->AideDrop = $returnval;
        return $returnval;
    }

    /**
     * RH indisponibles du jour (popovers d'absence).
     * CI3 : Creationcd_model::LoadRHHome
     */
    protected function LoadRHHome($typerh)
    {
        $returnval = '';
        $strSQL = "Select reshumaine.*, suivisrh.* , typeabsence.Nom as nomAbsence,
                suivisrh.id as idsuivis From reshumaine INNER JOIN  suivisrh   ON
                reshumaine.id = suivisrh.idrh     INNER JOIN typeabsence
                ON suivisrh.idabsence = typeabsence.id
                WHERE ( suivisrh.dateend is null OR suivisrh.dateend > CURDATE() ) AND
                datestart <= CURDATE() AND reshumaine.idreshum IN ( $typerh) order by reshumaine.Nom";
        $suiviscamion = $this->db->query($strSQL);
        foreach ($suiviscamion->getResult() as $row) {
            $note = str_replace('%20', ' ', $row->note);
            $note = str_replace('"', "'", $row->note);

            $message = "
                  <b> Depuis :$row->datestart <br/><br/></b>
                                        <b> Retour prévu:<br/></b>
                                        $row->dateretourprevu <br/>
                                        <b> Absence pour:<br/></b>
                                        $row->nomAbsence <br/>
                                        <b>Notes: <br/></b>
                                        $note <br/><br/>";
            $returnval .= '<a href="#" onclick="ShowRHModal(' . $row->idsuivis . ')"  data-toggle="popover" data-trigger="hover" data-html="true" title="RH: ' . $row->Nom . ' ' . $row->Prenom . ' Non Disponible"   data-placement="bottom" data-content="' . $message . '"  style="overflow:auto;font-size:medium;font-weight:bold;">' . $row->Nom . ' ' . $row->Prenom .
                '&nbsp;&nbsp;<i class="fas fa-share" ></i></a><br /><span style="font-size:small; color:black">&nbsp;&nbsp;&nbsp;&nbsp;' . $row->nomAbsence . '</span> <br />  ';
        }
        $returnval = substr($returnval, 0, -2);
        return $returnval;
    }

    /**
     * Camions hors service / avec materiel du jour.
     * CI3 : Creationcd_model::LoadCamionHome
     */
    protected function LoadCamionHome($statut, $typecam)
    {
        $returnval = '';
        $strSQL = "SELECT suiviscamion.id, statutcamion.Nom,
                           suiviscamion.nocamion,
                            suiviscamion.datestart,
                            suiviscamion.dateretourprevu,
                            suiviscamion.dateend,
                            suiviscamion.note,
                            suiviscamion.TypeDechet,
                            suiviscamion.QteDechet,
                            (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ) as TypeCam
                       FROM statutcamion statutcamion
                            INNER JOIN suiviscamion suiviscamion
                               ON (statutcamion.id = suiviscamion.idstatut)
                               WHERE  suiviscamion.dateend is null
                               and suiviscamion.datestart <= CURDATE()
                               AND statutcamion.id = $statut AND
                               (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ) = $typecam
                               order by  (select TypeCamion from camions cam
                            where cam.nocamion = suiviscamion.nocamion ),
                               suiviscamion.nocamion";
        $suiviscamion = $this->db->query($strSQL);
        foreach ($suiviscamion->getResult() as $row) {
            if (($row->dateend == '') or (! isset($row->dateend))) {
                $note = str_replace('%20', ' ', $row->note);
                $note = str_replace('"', "'", $row->note);
                switch ($statut) {
                    case 1:
                        $message = "
                       <b> Depuis :$row->datestart <br/><br/></b>
                                             <b> Retour prévu:<br/></b>
                                              $row->dateretourprevu <br/>
                                              <b>Notes: <br/></b>
                                              $note <br/><br/>";
                        $returnval .= '&nbsp;&nbsp;<a  onclick="ShowHSModal(\'' . $row->nocamion . '\')" data-toggle="popover" data-trigger="hover" data-html="true" title="Camion: ' . $row->nocamion . ' Hors service"   data-placement="bottom" data-content="' . $message . '" style="overflow:auto;font-size:medium;font-weight:bold;' .
                            (($row->TypeCam == 1) ? '#da1c23' : 'DarkBlue') . '">' . $row->nocamion . '</a>, ';
                        break;
                    case 2:
                        $message = "
                       <b> Depuis : $row->datestart <br/><br/> </b>
                                             <b> Vidange prévu: </b> <br/>
                                              $row->dateretourprevu <br/>
                                             <b> Type de déchet: </b> <br/>
                                              $row->TypeDechet <br/>
                                              <b>Notes: <br/> </b>
                                              $note <br/><br/>";
                        $returnval .= '&nbsp;&nbsp;<a  id="showcammat_' . $row->nocamion . '" onclick="ShowAvecMatModal(\'' . $row->nocamion . '\')" data-toggle="popover" data-trigger="hover"  data-html="true" title="Camion: ' . $row->nocamion . ' Avec Matériel"   data-placement="bottom" data-content="' . $message . '" style="overflow:auto;font-size:medium;font-weight:bold;' .
                            ((strtolower($row->TypeDechet) == 'recyclage') ? '#26F4B2' : '#254AE0') . '">' . $row->nocamion . '</a>, ';
                        break;
                }
            }
        }
        $returnval = substr($returnval, 0, -2);
        return $returnval;
    }

    /**
     * Tableau de bord d'accueil complet (drops camions/RH + hors
     * services + indisponibles).
     * CI3 : Creationcd_model::getTDBHome
     */
    public function getTDBHome()
    {
        $cedules = new CedulesModel();
        $tableCam = '<table  class = "table" id="tab_logic">
            <tr  class="entete" style="background-color: #093;line-height:25px;text-align:center;width:1100px">

                <tr>
                <td>
                  <h3  ondrop="dropREHors(event, 1,1)" ondragover="allowDrop(event)" style="margin-top: 11px;height:40px;padding-top: 8px;width:100%;color:white;text-align:center;background-color:black;">Types Camion</h4>
                  <div >
                        <select class="form-control" id="typecam" onchange="getCamForTypeHome(this.value)">
                        <option value="">Choisir</option>
                          ' . $cedules->GetTypeCamion(1) . '
                        </select>

                  </div>
                  <div class="box box-solid" style="overflow: scroll; max-height: 250px; width: 200px">

                    <div id="divDropCam" class="box-body" style="padding-left:7px;">
                ' . $this->GetHomeCamions() . '

                    </div>
                  </div>
                </td>
                <td><h4  ondrop="dropREHorsTDB(event, 1,1)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">HORS SERVICES REAREND</h4><div class="hometdb" style="height:300px;width: 300px;" >' . $this->LoadCamionHome(1, 1) . '</div></td>
                <td><h4  ondrop="dropREHorsTDB(event, 1, 2)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:peru;">REAREND AVEC MATÉRIEL</h4><div class="hometdb" style="height:300px;width: 300px;" >' . $this->LoadCamionHome(2, 1) . '</div></td>
                <td><h4  ondrop="dropRHNonDispoTDB(event)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">AIDES INDISPONIBLE</h4><div class="hometdb" style="height:300px;width: 300px;overflow: scroll;" >' . $this->LoadRHHome('2') . '</div></td></tr>

                <tr>
                <td><h3  ondrop="dropREHors(event, 1,1)" ondragover="allowDrop(event)" style="margin-top: 11px;height:40px;padding-top: 8px;width:100%;color:white;text-align:center;background-color:black;">Types de RH</h3>
                  <div>

                <select class="form-control" id="typerh" onchange="getRHForType(this.value)">
                  <option value="">Choisir</option>
              ' . $cedules->GetTypeRH(1) . '

                </select>
              </div>
              <div class="box box-solid" id="divDropChauf" style="padding-left:7px;display:block;overflow: scroll; max-height: 250px; width: 200px">
              ' . $this->GetChauffeursToDropHome(date('Y-m-d'), 1) . '

              </div>
            </td>
            <td><h4  ondrop="dropREHorsTDB(event, 1, 1)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">HORS SERVICES ROBOT</h4><div class="hometdb" style="height:300px;width: 300px;" >' . $this->LoadCamionHome(1, 2) . '</div></td>
            <td><h4  ondrop="dropREHorsTDB(event, 2, 2)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:peru;">ROBOT AVEC MATÉRIEL</h4><div class="hometdb" style="height:300px;width: 300px;" >' . $this->LoadCamionHome(2, 2) . '</div></td>
            <td><h4  ondrop="dropRHNonDispoTDB(event)" ondragover="allowDrop(event)" style="width:100%;height:40px;padding-top: 10px;color:white;text-align:center;background-color:green;">CHAUFFEURS INDISPONIBLE</h4><div class="hometdb" style="height:300px;width: 300px;overflow: scroll;" >' . $this->LoadRHHome('1, 3') . '</div></td></tr>
                            </table> <script type="text/javascript">
                $(document).ready(function () {
                    $(\'[data-toggle="popover"]\').popover();
                });
              </script>';
        $this->data['tdbhome'] = $tableCam;
        return $tableCam;
    }
}
