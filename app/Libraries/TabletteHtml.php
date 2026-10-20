<?php

namespace App\Libraries;

use Config\Database;

/**
 * Port des générateurs de fragments HTML de Co_tablette_sani (CI3) :
 * getInfoVille, getInfoDepotoire, getinfoAides, getaide, getHeadrerRte,
 * getAvecPolyline, getSansPolyline, getScriptRte, GetMenu, GetMenuTop,
 * GetCamOnRoad, GetAllCamions, GetChauffeurs, GetAllEmployes, GetVilles,
 * GetTypeExtra, GetOptionChauffeur, getOptionSecteur, getPolylineBase.
 *
 * $data est passé par référence là où CI3 utilisait $this->data.
 */
class TabletteHtml
{
    private $db;
    private TabletteService $svc;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->svc = new TabletteService();
    }

    public function esc($v)
    {
        return $this->db->escape($v);
    }

    //---------------------------------------------------------- camions / chauffeurs
    public function getAllCamions($NoCamion, array &$data): string
    {
        $typeCamion = '';
        $returnval = '<option value="" selected>Choisir</option>';
        $strSQL = "SELECT NoCamion, TypeCamion FROM camions WHERE isdeleted = 0 AND TypeCamion IN (1,7) ORDER BY TypeCamion, NoCamion";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            if ($typeCamion != $row->TypeCamion) {
                $returnval .= '<option style="color:' . (($row->TypeCamion == 1) ? 'green' : 'red') . ';font-weight:bold;" value="0" disabled >' . (($row->TypeCamion == 1) ? 'CHARGEMENT ARRIERE' : 'LATERAL') . '</option>';
                $typeCamion = $row->TypeCamion;
            }
            $select = ($NoCamion == $row->NoCamion) ? ' selected ' : '';
            $returnval .= '<option value="' . $row->NoCamion . '" ' . $select . '>' . $row->NoCamion . '</option>';
        }
        $data['camions'] = $returnval;
        return $returnval;
    }

    public function getChauffeurs(array &$data): string
    {
        $arrNameRH = ['1' => 'CHARGEMENT ARRIERE', '2' => 'AIDE', '3' => 'LATERAL', '4' => 'ROLL OFF', '7' => 'CHARGEMENT AVANT'];
        $typeDeRH = '';
        $typeResH = '1,3,4,7';
        $returnval = '<option value="" selected>Choisir</option>';
        $strSQL = "SELECT login, id, idreshum FROM reshumaine WHERE login != '' AND isdeleted = 0 AND idreshum IN ($typeResH) ORDER BY idreshum, login";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            if ($typeDeRH != $row->idreshum) {
                $returnval .= '<option style="color:red;font-weight:bold;" value="0" disabled >' . $arrNameRH[$row->idreshum] . '</option>';
                $typeDeRH = $row->idreshum;
            }
            $returnval .= '<option  value="' . $row->login . '">' . $row->login . '</option>';
        }
        $data['chauffeurs'] = $returnval;
        return $returnval;
    }

    public function getAllEmployes($typerh, $idrh): string
    {
        $arrNameRH = ['1' => 'CHARGEMENT ARRIERE', '2' => 'AIDE', '3' => 'LATERAL', '4' => 'ROLL OFF', '7' => 'CHARGEMENT AVANT'];
        $typeDeRH = '';
        $typeResH = '';
        switch ($typerh) {
            case 1:
                $typeResH = '1,3,4,7';
                break;
            case 2:
                $typeResH = '2';
                break;
        }
        $returnval = '';
        $strSQL = "SELECT CONCAT(Nom, ', ', Prenom) AS NomComplet, id, idreshum
                   FROM reshumaine WHERE idreshum IN ($typeResH) AND isdeleted = 0 ORDER BY idreshum, Nom";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            if ($typeDeRH != $row->idreshum) {
                $returnval .= '<option style="color:red;font-weight:bold;" value="" disabled >' . $arrNameRH[$row->idreshum] . '</option>';
                $typeDeRH = $row->idreshum;
            }
            $select = ($idrh == $row->id) ? ' selected ' : '';
            $returnval .= '<option ' . $select . ' value="' . $row->id . '">' . $row->NomComplet . '</option>';
        }
        return $returnval;
    }

    public function getVilles($userId): string
    {
        $returnval = '';
        $strSQL = "SELECT DISTINCT (SELECT Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
                          idvilles, ID, TypeDeProduit
                   FROM cedules WHERE cedules.DateJour = CURDATE()
                     AND (cedules.idchauffeurs = $userId OR cedules.idremplace_chauf = $userId)
                     AND idvilles != 51 ORDER BY cedules.ID";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            $returnval .= '<option style="font-weight:bold;" value="' . $row->ID . '" >' . $row->NomVille . '</option>';
        }
        return $returnval;
    }

    public function getTypeExtra($typeext = ''): string
    {
        $returnval = '';
        foreach ($this->db->query('SELECT Nom, id FROM typeextra ORDER BY Nom')->getResult() as $row) {
            $selected = ($typeext == $row->id) ? ' selected ' : '';
            $returnval .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->Nom . '</option>';
        }
        return $returnval;
    }

    public function getOptionChauffeur($idchauffeur, $dtejour): string
    {
        $returnval = '';
        $strSQL = "SELECT id, CONCAT(Nom, ', ', Prenom) AS NomComplet FROM reshumaine WHERE id IN
                   (SELECT DISTINCT idchauffeurs FROM cedules WHERE DateJour = $dtejour) ORDER BY Nom";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            $selected = (trim($idchauffeur) == trim($row->id)) ? ' selected ' : '';
            $returnval .= '<option ' . $selected . ' value="' . $row->id . '" >' . $row->NomComplet . '</option>';
        }
        return $returnval;
    }

    public function getOptionVilleJson(): string
    {
        $returnval = '<option value="0">Choisir</option>';
        foreach ($this->db->query('SELECT Nom, id FROM ville WHERE isdeleted = 0 ORDER BY Nom')->getResult() as $row) {
            $returnval .= '<option  value="' . $row->id . '" >' . $row->Nom . '</option>';
        }
        $secteurs = '<option value="0">Choisir</option>';
        foreach ($this->db->query('SELECT Nom, id FROM secteurs ORDER BY Nom')->getResult() as $row) {
            $secteurs .= '<option  value="' . $row->id . '" >' . $row->Nom . '</option>';
        }
        return json_encode(['villes' => $returnval, 'secteurs' => $secteurs]);
    }

    public function getPolylineBase($idville, $idsecteur)
    {
        $idville = (int) $idville;
        $idsecteur = (int) $idsecteur;
        $result = $this->db->query(
            "SELECT id, idville, idsecteur, nom, parcours, parcours_reduit, urlfile
             FROM polilyne_ville WHERE idville = $idville AND idsecteur = $idsecteur"
        )->getRow();
        return $result->parcours ?? null;
    }

    //---------------------------------------------------------- aides / info_villes / dépotoires
    public function getaide($idcedule, $idaide, $tyrh, $dtejour): array
    {
        $strSQL = "SELECT id AS idsanivanrh, idcedule, idrh, TypeRh, datejour, HreDeb, HreFin, HreDeb_extra, HreFin_extra, TypeExtra
                   FROM sanivan_rh WHERE idrh = $idaide AND datejour = $dtejour AND TypeRh = $tyrh";
        $result = $this->db->query($strSQL);
        if ($result->getNumRows() > 0) {
            return $result->getResultArray();
        }
        if ($idaide > 0) {
            $this->db->query("INSERT INTO sanivan_rh (idcedule, idrh, TypeRh, datejour) VALUES ($idcedule, $idaide, $tyrh, $dtejour)");
            $strSQL = "SELECT id AS idsanivanrh, idcedule, idrh, TypeRh, datejour, HreDeb, HreFin, HreDeb_extra, HreFin_extra, TypeExtra
                       FROM sanivan_rh WHERE idcedule = $idcedule AND idrh = $idaide AND datejour = $dtejour AND TypeRh = $tyrh";
            return $this->db->query($strSQL)->getResultArray();
        }
        return [[
            'idsanivanrh' => '0', 'idcedule' => '0', 'idrh' => '0', 'TypeRh' => '0',
            'datejour' => '', 'HreDeb' => '', 'HreFin' => '', 'HreDeb_extra' => '',
            'HreFin_extra' => '', 'TypeExtra' => '',
        ]];
    }

    public function getinfoAides($cedule, array &$data): void
    {
        $returnval = '';
        $iter = 1;
        $aideBloc = function ($num, $idaide, $infoAide) {
            return '<div class="input-group" style="width:920px;margin-top:25px">
              <div class="input-group" style="width:800px;margin-top:20px">
                <label class="form-control" style="width:70px;font-weight:bold;">Aide</label>
                <span class="input-group-btn">
                  <select class="form-control form-control-modern" id="HELPERS_' . $num . '" style="width:200px"> <option value="0">Choisir</option>
                    ' . $this->getAllEmployes(2, $idaide) . '
                  </select>
                </span>
                <label class="form-control" style="width:80px;font-weight:bold;">Hre deb</label>
                <span class="input-group-btn">
                  <input type="time" class="form-control form-control-modern" id="HreDeb_' . $num . '" value="' . $infoAide[0]['HreDeb'] . '" style="background-color: yellow; font-weight: bold;width:100px"  onchange="postTextSani(this.value, this.id)" />
                </span>
                <label class="form-control" style="width:80px;font-weight:bold;">Hre fin</label>
                <span class="input-group-btn">
                  <input type="time" class="form-control form-control-modern" id="HreFin_' . $num . '" value="' . $infoAide[0]['HreFin'] . '" style="background-color: yellow; font-weight: bold;width:100px"  onchange="postTextSani(this.value, this.id)" />
                </span>
            <button type=\'button\'
            class=\'btn btn-default btn-sm\' style=\'font-size:18px;\' xclass=\'btn-primary btn\'
                onclick="PostAideSani(' . $idaide . ',' . $num . ',' . $infoAide[0]['idsanivanrh'] . ')" title=\'Enregistrer\'><i class=\'fa fa-check  btn btn-success\' aria-hidden=\'true\'></i></button>
              </div>';
        };
        foreach ($cedule->getResult() as $row) {
            if ($iter > 1) {
                break;
            }
            $iter++;
            if ($row->HELPERS_1 < 1) {
                $row->HELPERS_1 = 0;
            }
            if (isset($row->HELPERS_1)) {
                $infoAide = $this->getaide($row->ID, $row->HELPERS_1, 1, $this->db->escape($row->DateJour));
                $returnval .= $aideBloc(1, $row->HELPERS_1, $infoAide);
            }
            if (isset($row->HELPERS_2)) {
                $infoAide = $this->getaide($row->ID, $row->HELPERS_2, 2, $this->db->escape($row->DateJour));
                $returnval .= $aideBloc(2, $row->HELPERS_2, $infoAide);
            }
            if (isset($row->HELPERS_3)) {
                $infoAide = $this->getaide($row->ID, $row->HELPERS_3, 3, $this->db->escape($row->DateJour));
                $returnval .= $aideBloc(3, $row->HELPERS_3, $infoAide);
            }
            if (isset($row->HELPERS_4)) {
                $infoAide = $this->getaide($row->ID, $row->HELPERS_4, 4, $this->db->escape($row->DateJour));
                $returnval .= $aideBloc(4, $row->HELPERS_4, $infoAide);
            }
        }
        $data['aides'] = $returnval;
    }

    public function getInfoDepotoire(int $idinfoville, array $data): string
    {
        $returnval = '';
        $strSQL = "SELECT id, idinfoville, Ticket, Tonnage, Depotoire, IsTransbo, boite, IsAvecMat,
                      (SELECT idcedule FROM info_villes WHERE id = info_depotoire.idinfoville) AS idcedule
                   FROM info_depotoire WHERE idinfoville = $idinfoville";
        $result = $this->db->query($strSQL);
        if ($result->getNumRows() == 0) {
            $this->db->query("INSERT INTO info_depotoire (idinfoville) VALUES ($idinfoville)");
            $newId = $this->db->insertID();
            $strSQL = "SELECT id, idinfoville, Ticket, Tonnage, Depotoire, IsTransbo, boite, IsAvecMat,
                          (SELECT idcedule FROM info_villes WHERE id = info_depotoire.idinfoville) AS idcedule
                       FROM info_depotoire WHERE id = " . $newId;
            $result = $this->db->query($strSQL);
        }
        foreach ($result->getResult() as $row) {
            if (trim((string) $row->Ticket) == '') {
                $ticket = '<span class="input-group-btn">
                            <button id="btnScanner_' . $row->id . '"  class="btn btn-primary" onclick="showScanPage(' . $row->id . ',' . $idinfoville . ')"  style="width:95px;max-height:45px;height:45px"> SCANNER</button>
                          </span>';
            } else {
                $ticket = '<span class="input-group-btn">
                          <a href="#" target="_blank" class="form-control" style="font-size:16px;" onclick="DownloadFile(' . $row->Ticket . ')">
                                        <i class="fa fa-download btn btn-success" style="font-size:16px;height:30px;">
                                  </i>&nbsp;&nbsp;Voir le BILLET</a>
                          </span>';
            }
            $checked = ($row->IsTransbo == 1) ? ' checked ' : '';
            $checkedMat = ($row->IsAvecMat == 1) ? ' checked ' : '';
            $returnval .= '<div class="input-group" style="width:1150px;margin-top:10px">
                          <label class="form-control" style="max-width: 110px;width:110px;font-weight:bold;max-height:45px">D&eacute;potoire / Mat. <span style="background-color: ' . ($data['couleur'] ?? '') . '"' . ($data['TypeDeProduit'] ?? '') . '</span></label>
                          <span class="input-group-btn">
                            <select class="form-control form-control-modern" id="depotoire_' . $row->id . '" style="width:185px;color:red;font-weight:bold;height:47px;">
                              <option value="" >Choisir</option>
                              ' . $this->svc->getAllDepotoires($row->Depotoire) . '
                            </select>
                          </span>
                          <label class="form-control" style="max-width: 80px;width:80px;font-weight:bold;max-height:45px;height:44px;">Billet</label>
                          <span id="spTicket" class="input-group-btn">' . $ticket . '
                          </span>
                          <label class="form-control" style="max-width: 90px;width:90px;font-weight:bold;max-height:45px;">Tonnage</label>
                          <span class="input-group-btn">
                            <input type="text" class="form-control form-control-modern" id="Tonnage_' . $row->id . '" value="' . str_replace('.', ',', (string) $row->Tonnage) . '" style="width:100px; background-color: yellow; font-weight: bold;height:45px" />
                          </span>
                          <button type=\'button\'
                        class=\'btn btn-default btn-sm\' style=\'font-size:18px;max-height:44px;\' xclass=\'btn-primary btn\'
                            onclick="postDepotoire(' . $row->id . ')" title=\'Enregistrer\'><i class=\'fa fa-check  btn btn-success\' aria-hidden=\'true\'></i></button>
                    </div>
<div class="input-group" style="width:1150px;margin-top:10px">
                          <label class="form-control" style="max-width: 90px;width:90px;font-weight:bold;max-height:45px;">TransBO</label>
                          <span class="input-group-btn">
                            <input type="checkbox" class="checkbox-custom checkbox-primary" id="chkTransbo_' . $row->id . '" ' . $checked . ' style="width:44px;height:44px" onclick="showBoite(' . $row->id . ')" />
                          </span>
<span class="input-group-btn" id="spBoite_' . $row->id . '" style="display:' . ((trim((string) $row->boite) == '') ? 'none' : 'block') . ';width:160px">
                          <label class="form-control" style="max-width: 90px;width:90px;font-weight:bold;max-height:45px;">Bo&icirc;te</label>
                          <span class="input-group-btn">
                            <input type="text" class="form-control form-control-modern" id="Boite_' . $row->id . '" value="' . $row->boite . '" style="width:90px; background-color: yellow; font-weight: bold;height:45px" />
                          </span>
                          </span>
                          <label class="form-control" style="max-width: 130px;width:130px;font-weight:bold;max-height:45px;">A domper</label>
                          <span class="input-group-btn">
                            <input type="checkbox" class="checkbox-custom checkbox-primary" id="chkADomper_' . $row->id . '" ' . $checkedMat . ' style="width:44px;height:44px"  onclick="setAvecMat(' . $row->id . ',' . $row->idcedule . ')" />
                          </span>

                    </div>';
        }
        return $returnval;
    }

    public function getInfoVille($idville, $ID, array &$data): string
    {
        if ($idville == '' || $idville === null) {
            $result = $this->db->query("SELECT * FROM cedules WHERE ID = " . (int) $ID)->getRow();
            if ($result) {
                $idville = $result->idvilles;
                $data['idville'] = $idville;
                $data['typedechet'] = $result->TypeDeProduit;
            }
        }
        $returnval = '<div><button type="button" class="btn btn-info" style="width:295px;margin-top:10px"
                                onclick="addDepotoire(' . $idville . ',' . $ID . ')"> Ajouter un ramassage</button></div>';
        $strSQL = "SELECT id, idville, idcedule, HreDeb, HreFin, nbBac,
                      (SELECT Nom FROM ville WHERE ville.id = info_villes.idville) AS NomVille
                   FROM info_villes WHERE idville = " . (int) $idville . " AND idcedule = " . (int) $ID;
        $result = $this->db->query($strSQL);
        if ($result->getNumRows() > 0) {
            foreach ($result->getResult() as $rowville) {
                $hredeb = $rowville->HreDeb;
                $hrefin = $rowville->HreFin;
                $infoville = $rowville->id;
                $nbBac = $rowville->nbBac;
                $returnval .= '
                                      <div class="input-group" style="width:1000px;margin-top:20px;">

    <label class="form-control" style="max-width:150px;width:150px;font-weight:bold;">Ramassage ville: </label>
                                        <span class="input-group-btn">
<label class="form-control form-control-modern" id="idville_' . $infoville . '" style="color: red;font-weight:bold" >' . $rowville->NomVille . ' </label>

                                        </span>
                                        <label class="form-control" style="max-width:90px;width:90px;font-weight:bold;">Hre deb</label>
                                        <span class="input-group-btn">
                                          <input type="time" class="form-control form-control-modern" id="HreDeb_' . $infoville . '"
    value="' . $hredeb . '" style="width:100px; background-color: yellow; font-weight: bold;"  onchange="PostVilleSani(' . $infoville . ')" required />
                                        </span>
                                        <label class="form-control" style="max-width:90px;width:90px;font-weight:bold;">Hre fin</label>
                                        <span class="input-group-btn">
                                          <input type="time" class="form-control form-control-modern" id="HreFin_' . $infoville . '"
    value="' . $hrefin . '" style="width:100px; background-color: yellow; font-weight: bold;"  onchange="PostVilleSani(' . $infoville . ')" required />
                                        </span>
                                <label class="form-control" style="width:90px;font-weight:bold;">Nb Bacs</label>
                                <span class="input-group-btn">
                                  <input type="number" class="form-control form-control-modern" id="nbBac_' . $infoville . '" value="' . $nbBac . '" style="width:100px; background-color: yellow; font-weight: bold;"  onchange="PostVilleSani(' . $infoville . ')" />
                                </span>
                                        <button type="button"
                                          class="btn btn-default btn-sm" style="font-size:18px;" xclass="btn-primary btn"
                                          onclick="PostVilleSani(' . $infoville . ')" title="Enregistrer">
                                          <i class="fa fa-check  btn btn-success" aria-hidden="true"></i>
                                        </button>

                                      </div>
                                    <div class="card-body">
                                      ' . $this->getInfoDepotoire($infoville, $data) . '

                                    </div>
';
                $data['idinfoville'] = $infoville;
            }
        }
        return $returnval;
    }

    public function getNewDepotHtml(int $idville, int $idcedule, array $data): string
    {
        $row = $this->db->query(
            "SELECT cedules.*, (SELECT Nom FROM ville WHERE ville.id = " . (int) $idville . ") AS NomVille
             FROM cedules WHERE ID = " . (int) $idcedule
        )->getRowArray();
        $this->db->query("INSERT INTO info_villes (idville, idcedule) VALUES (" . $row['idvilles'] . ", " . $row['ID'] . ")");
        $infoville = $this->db->insertID();
        return '<div class="row" style="margin-top:10px">

                                      <div class="input-group" style="width:1000px;margin-top:20px;">
    <label class="form-control" style="max-width:150px;width:150px;font-weight:bold;">Ramassage ville: </label>
                                        <span class="input-group-btn">
                                          <label class="form-control form-control-modern" id="idville_' . $infoville . '" style="color: red;font-weight:bold" >' . $row['NomVille'] . '</label>
                                        </span>
                                        <label class="form-control" style="max-width:90px;width:90px;font-weight:bold;">Hre deb</label>
                                        <span class="input-group-btn">
                                          <input type="time" class="form-control form-control-modern" id="HreDeb_' . $infoville . '"
    value="" style="width:100px; background-color: yellow; font-weight: bold;" required />
                                        </span>
                                        <label class="form-control" style="max-width:90px;width:90px;font-weight:bold;">Hre fin</label>
                                        <span class="input-group-btn">
                                          <input type="time" class="form-control form-control-modern" id="HreFin_' . $infoville . '"
    value="" style="width:100px; background-color: yellow; font-weight: bold;" required />
                                        </span>
                                <label class="form-control" style="width:90px;font-weight:bold;">Nb Bacs</label>
                                <span class="input-group-btn">
                                  <input type="number" class="form-control form-control-modern" id="nbBac_' . $infoville . '" value="" style="width:100px; background-color: yellow; font-weight: bold;" />
                                </span>
                                        <button type="button"
                                          class="btn btn-default btn-sm" style="font-size:18px;" xclass="btn-primary btn"
                                          onclick="PostVilleSani(' . $infoville . ')" title="Enregistrer">
                                          <i class="fa fa-check  btn btn-success" aria-hidden="true"></i>
                                        </button>

                                      </div>
                                    <div class="card-body">
                                      ' . $this->getInfoDepotoire((int) $infoville, $data) . '
                                    </div>
                              </div>';
    }

    public function getHeadrerRte($date, $idchauffeur, array &$data): void
    {
        if ($idchauffeur == '') {
            $idchauffeur = '';
        } else {
            $idchauffeur = " AND (cedules.idchauffeurs = $idchauffeur OR cedules.idremplace_chauf = $idchauffeur ) ";
        }
        $idville = '';
        $iter = 0;
        $data['tabsville'] = '';
        $data['contentville'] = '';
        $strSQL = "SELECT DISTINCT (SELECT Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille,
                          idvilles, ID, TypeDeProduit,
         (SELECT id FROM typededechet WHERE Nom = cedules.TypeDeProduit) AS IdTypeProduit,
         (SELECT couleur FROM typededechet WHERE Nom = cedules.TypeDeProduit) AS CouleurProduit
                   FROM cedules WHERE cedules.DateJour = $date $idchauffeur
                     AND idvilles != 51 ORDER BY idvilles";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            if ($idville != $row->idvilles) {
                $findville = $this->db->query(
                    "SELECT id, idville, idcedule, HreDeb, HreFin, nbBac
                     FROM info_villes WHERE idville = " . $row->idvilles . " AND idcedule = " . $row->ID
                );
                if ($findville->getNumRows() == 0) {
                    $this->db->query("INSERT INTO info_villes (idville, idcedule) VALUES (" . $row->idvilles . ", " . $row->ID . ")");
                    $infoville = $this->db->insertID();
                } else {
                    $infoville = $findville->getRow()->id;
                }
                if ($iter == 0) {
                    $data['activeCedule'] = $row->ID;
                    $data['idville'] = $row->idvilles;
                }
                switch ($row->TypeDeProduit) {
                    case 'ORGANIQUE': $color = '#ebccaa'; break;
                    case 'RECYCLAGE': $color = '#9FF3F9'; break;
                    case 'DECHETS': $color = '#E5E5E5'; break;
                    case 'RESIDUS_VERT': $color = '#2DBC96'; break;
                    case 'GROS_REBUTS': $color = '#2B3B7AF'; break;
                    case 'BRANCHES': $color = '#D1B546'; break;
                    case 'FEUILLES': $color = '#2DBC96'; break;
                    default: $color = '#F7F1C5';
                }
                $textColor = ';color:black';
                if ($iter == 0) {
                    $textColor = ';color:red;font-weight:bold';
                    $data['nav_tabs'] = '<li><label id="btnTabVille" class="form-control" style="background-color:' . $color . $textColor . '"> ' . $row->NomVille . '</label> <input type="hidden" id="idvilleactive" value="' . $row->idvilles . '" /> </li><li><label id="lblHproduit" class="form-control" style="background-color:' . $color . ';color:black;font-weight:bold">' . $row->TypeDeProduit . '</label> </li>';
                }
                $idville = $row->idvilles;
                $stylestar = ($iter == 0) ? 'style="color:blue"' : '';
                $style = ($iter == 0) ? 'color: red;font-weight:bold' : 'color:black;font-weight:normal';
                $data['tabsville'] .= '<li id="liville_' . $row->idvilles . '" class="nav-item">
                                <a class="nav-link" href="#" onclick="setTabVille(' . $row->idvilles . ')" ><i id="star_' . $row->idvilles . '" class="fas fa-star" ' . $stylestar . ' ></i><input type="hidden" id="idProduit_' . $row->idvilles . '" value="' . $row->TypeDeProduit . '" /><input type="hidden" id="tpProduit_' . $row->idvilles . '" value="' . $row->CouleurProduit . '" /> <label  id="btnVille_' . $row->idvilles . '" style="' . $style . '" >' . $row->NomVille . '</label></a>
                              </li>';
                $visible = ($iter == 0) ? 'display:block;' : 'display:none;';
                $data['contentville'] .= '<div id="content_' . $row->idvilles . '" class="tab-pane" style="' . $visible . '">
                                ' . $this->getInfoVille($row->idvilles, $row->ID, $data) . '
                              </div>';
                $iter++;
            }
        }
    }

    //---------------------------------------------------------- scripts carte (Google Maps)
    public function getAvecPolyline($center, $parcours): string
    {
        return "<script> const options = {
                      timeout: 8000,
                      enableHighAccuracy: true,
                      maximumAge: 0
                  };

                  var map, directionsService, directionsRenderer;
                  var markers = [];
                  var haightAshbury = '';
                  var truckFlagImg = document.getElementById('ImgTruckSani').src;
                  function initMap() {
                      directionsService = new google.maps.DirectionsService();
                      directionsRenderer = new google.maps.DirectionsRenderer();
                      haightAshbury = " . $center . ";
                      map = new google.maps.Map(document.getElementById(\"map\"), {
                          zoom: 16,
                          center: " . $center . ",
                          heading: 320,
                          tilt: 47.5,
                      });
                      directionsRenderer.setMap(map);
                      var polyLinePath = new google.maps.Polyline({
                       path: " . $parcours . ",
                        strokeColor: '#0000FF',
                        strokeOpacity: 1.0,
                        strokeWeight: 2
                      });

                    polyLinePath.setMap(map);

                    function changePolylineColor() {
                       polyLinePath.setOptions({strokeColor:  '#FF0000'});
                    }

                    var vehiclePosition = new google.maps.Marker({
                      position: " . $center . ",
                      map: map,
                      icon: truckFlagImg
                    });
                    google.maps.event.addListener(vehiclePosition, 'position_changed', function() {
                      var pathBounds = new google.maps.LatLngBounds();
                      polyLinePath.getPath().forEach(function(latLng) {
                          pathBounds.extend(latLng);
                      });

                      if (pathBounds.contains(vehiclePosition.getPosition())) {
                        changePolylineColor();
                      }
                    });

                  }
                  function calculateAndDisplayRoute() {
                      const start = new google.maps.LatLng(45.5, -73.7);
                      const end = new google.maps.LatLng(polyLinePath[0].lat, polyLinePath[0].lat);

                      directionsService.route({
                          origin: start,
                          destination: end,
                          travelMode: google.maps.TravelMode.DRIVING
                      }, function(response, status) {
                          if (status === 'OK') {
                              directionsRenderer.setDirections(response);
                          } else {
                              window.alert('Directions request failed due to ' + status);
                          }
                      });
                  }
                  function addMarker(position) {
                      const marker = new google.maps.Marker({
                          position,
                        map,
                          icon: truckFlagImg
                      });

                      markers.push(marker);
                  }

                  function setMapOnAll(map) {
                      for (let i = 0; i < markers.length; i++) {
                          markers[i].setMap(map);
                      }
                  }

                  function hideMarkers() {
                      setMapOnAll(null);
                  }

                  function showMarkers() {
                      setMapOnAll(map);
                  }

                  function deleteMarkers() {
                      hideMarkers();
                      markers = [];
                  }
                  function success(pos) {
                      const crd = pos.coords;
                      haightAshbury = { lat: crd.latitude, lng: crd.longitude };
                      var urlName = \"/tablettes_san/track?lat=\" + haightAshbury.lat + \"&lng=\" + haightAshbury.lng;
                      $.ajax({
                          url: urlName,
                          context: document.body
                      }).done(function (data) {
                      });
                      map.setCenter(haightAshbury)
                      vehiclePosition.position = haightAshbury;
                  }
                  function error(err) {
                  }
                  function RefreshTrack() {
                      idZoom = navigator.geolocation.watchPosition(
                          success,
                          error,
                          options
                      );
                  }


                  function postPosition() {
                      navigator.geolocation.watchPosition(
                          success,
                          error,
                          options
                      );
                      map.setCenter(haightAshbury)
                  }
                  window.initMap = initMap;
                  navigator.geolocation.watchPosition(
                      success,
                      error,
                      options
                  );
                  initMap();
                  myInterval = setTimeout(postPosition, 12000);
                  calculateAndDisplayRoute();
          </script>";
    }

    public function getSansPolyline(): string
    {
        return "<script> const options = {
                      timeout: 8000,
                      enableHighAccuracy: true,
                      maximumAge: 0
                  };
                  var vehiclePosition;
                  var map;
                  var markers = [];
                  var haightAshbury = '';
                  var truckFlagImg = document.getElementById('ImgTruckSani').src;
                  function initMap() {
                      haightAshbury = { lat: 45.399139, lng: -74.0781014 };
                      map = new google.maps.Map(document.getElementById(\"map\"), {
                          zoom: 16,
                          center: { lat: 45.399139, lng: -74.0781014 },
                          heading: 320,
                          tilt: 47.5,
                      });
                    vehiclePosition = new google.maps.Marker({
                      position: { lat: 45.399139, lng: -74.0781014 },
                      map: map,
                    });

                  }

                  function addMarker(position) {
                      const marker = new google.maps.Marker({
                          position,
                        map,
                          icon: truckFlagImg
                      });

                      markers.push(marker);
                  }

                  function setMapOnAll(map) {
                      for (let i = 0; i < markers.length; i++) {
                          markers[i].setMap(map);
                      }
                  }

                  function hideMarkers() {
                      setMapOnAll(null);
                  }

                  function showMarkers() {
                      setMapOnAll(map);
                  }

                  function deleteMarkers() {
                      hideMarkers();
                      markers = [];
                  }
                  function success(pos) {
                      const crd = pos.coords;
                      haightAshbury = { lat: crd.latitude, lng: crd.longitude };
                      var urlName = \"/tablettes_san/track?lat=\" + haightAshbury.lat + \"&lng=\" + haightAshbury.lng;
                      $.ajax({
                          url: urlName,
                          context: document.body
                      }).done(function (data) {
                      });
                      map.setCenter(haightAshbury)
                      vehiclePosition.setPosition(haightAshbury);
                      map.panTo(newLatLng);
                  }
                  function error(err) {
                  }
                  function RefreshTrack() {
                      idZoom = navigator.geolocation.watchPosition(
                          success,
                          error,
                          options
                      );
                  }


                  function postPosition() {
                      navigator.geolocation.watchPosition(
                          success,
                          error,
                          options
                      );
                      map.setCenter(haightAshbury)
                  }
                  window.initMap = initMap;
                  navigator.geolocation.watchPosition(
                      success,
                      error,
                      options
                  );
window.initMap = initMap;
                  myInterval = setTimeout(postPosition, 12000);</script>";
    }

    public function getPolylineForVille($idville, $idsecteur): array
    {
        $idville = (int) $idville;
        $idsecteur = (int) $idsecteur;
        $polyline = $this->db->query(
            "SELECT id, idville, idsecteur, nom, parcours, parcours_reduit, urlfile
             FROM polilyne_ville WHERE idville = $idville AND idsecteur = $idsecteur"
        )->getRow();
        $data = [];
        if ($polyline) {
            $arrLocation = explode('},{', $polyline->parcours_reduit);
            $center = substr($arrLocation[0], 1, strlen($arrLocation[0])) . '}';
            $data['center'] = $center;
            $data['parcours'] = $polyline->parcours_reduit;
            $data['script'] = $this->getAvecPolyline($center, $polyline->parcours);
            $data['havePoly'] = '1';
        } else {
            $data['center'] = '{ lat: 45.447302, lng: -73.797050 }';
            $data['parcours'] = '';
            $data['havePoly'] = '0';
        }
        return $data;
    }

    public function getScriptRte($row): string
    {
        $latlng = $row->latlng;
        return "<script>
                        var gLat = 0;
                        var gLng = 0;
                        var track = {
                          rider : 999,
                          delay : 10000,
                          timer : null,
                          hDate : null,
                          hLat : null,
                          hLng : null,
                          init : () => {
                              track.hDate = new Date();
                            track.update();
                            track.timer = setInterval(track.update, track.delay);
                          },
                          update : () =>  {
                              var urlName = '/sanivan/coors_cam/' + $row->idchauffeur + '/' + $row->dateactive;
                              $.ajax({
                                  url: urlName,
                                  context: document.body
                              }).done(function (data) {
alert(data);
                                  poscam = data;
                                  map.setCenter(poscam)
                                  deleteMarkers();
                                  addMarkerCam(poscam);
                              });
                          },
                        };
                        let map;
                        let markers = [];
                        let poscam = '';

                        function initMap() {
	                        poscam = $latlng;
                            map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 15,
                                center: poscam,
                                mapTypeId: 'terrain',
                            });
                            map.addListener('click', (event) => {
                                addMarkerCam(event.latLng);
                            });
                            addMarkerCam(poscam);
                        }
                        function addMarkerCam(position) {
                            const truckFlagImg = document.getElementById('ImgTruckSani').src;
                            const marker = new google.maps.Marker({
                                position,
                                map,
                                icon: truckFlagImg,
                            });

                            markers.push(marker);
                        }
              </script>
  <script
        window.initMap = initMap;
        window.onload = track.init;
    src='https://maps.googleapis.com/maps/api/js?key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4&callback=initMap&v=weekly'
    defer></script>";
    }

    public function getScriptRteAllMarker(): string
    {
        return '';
    }

    //---------------------------------------------------------- menu / carte jour
    public function getCamOnRoad($dateactive, $idchauffeur, array &$data): array
    {
        $dateactiveesc = $this->db->escape($dateactive);
        $dateStart = $this->db->escape($dateactive . ' 00:00:00');
        $dateEnd = $this->db->escape($dateactive . ' 23:59:59');

        $returnval = '<button onclick="showAllTrucks()" class="btn btn-primary">Tous les camions</button><br /><br />';
        if ($idchauffeur > 0) {
            $chauffeur = " AND idchauffeurs = $idchauffeur ";
            $data['allchauffeur'] = 0;
        } else {
            $chauffeur = '';
            $data['allchauffeur'] = 1;
        }
        $data['idchauffeur'] = $idchauffeur;

        $strSQL = "SELECT
                        c.NOCAMION, idvilles,
                        c.idchauffeurs,
                        GROUP_CONCAT(v.id ORDER BY v.id SEPARATOR ', ') AS villes
                    FROM cedules c
                    JOIN ville v ON v.id = c.idvilles
                    WHERE c.DateJour = $dateactiveesc
                      AND EXISTS (
                            SELECT 1
                            FROM gps_sani g
                            WHERE g.nocamion = c.NOCAMION
                              AND g.track_time BETWEEN $dateStart AND $dateEnd
                      )
                    $chauffeur
                    GROUP BY c.NOCAMION, c.idchauffeurs
                    ORDER BY c.NOCAMION";
        $result = $this->db->query($strSQL);

        $camList = [];
        $camRows = [];
        foreach ($result->getResult() as $row) {
            $camList[] = $row->NOCAMION;
            $camRows[] = $row;
        }

        $lastPositions = [];
        if (! empty($camList)) {
            $camInList = implode(',', array_map([$this->db, 'escape'], $camList));
            $sqlLast = "SELECT g.idchauffeur, g.nocamion, g.track_time, g.track_lng, g.track_lat, g.adresse, g.IsInZone
                        FROM gps_sani g
                        INNER JOIN (
                            SELECT nocamion, MAX(track_time) AS max_time
                            FROM gps_sani
                            WHERE nocamion IN ($camInList)
                              AND track_time BETWEEN $dateStart AND $dateEnd
                            GROUP BY nocamion
                        ) latest ON g.nocamion = latest.nocamion AND g.track_time = latest.max_time
                        WHERE g.track_time BETWEEN $dateStart AND $dateEnd
                          AND g.nocamion IN ($camInList)";
            foreach ($this->db->query($sqlLast)->getResult() as $loc) {
                $lastPositions[$loc->nocamion] = $loc;
            }
        }

        $latlng = '';
        foreach ($camRows as $row) {
            $returnval .= '<label id="lbl_' . $row->NOCAMION . '" class="form-control" onclick="showOnlyThisTruck(\'' . $row->NOCAMION . '\',' . $row->idvilles . ')"
    style="font-size:medium;font-weight:bold;background-color: darkgrey;
    color: white;height:40px;width: 140px;">' .
                $row->NOCAMION . '&nbsp;&nbsp;&nbsp;
<input type="checkbox" style="width:20px;height:20px" />
</label>';

            if (isset($lastPositions[$row->NOCAMION])) {
                $Location = $lastPositions[$row->NOCAMION];
                $latlng .= $row->NOCAMION . '_'
                    . str_replace(',', '.', (string) $Location->track_lat) . ','
                    . str_replace(',', '.', (string) $Location->track_lng) . ','
                    . (int) $Location->IsInZone . ';';
            }
            if ($idchauffeur > 0 && $idchauffeur == $row->idchauffeurs) {
                $data['nocamion'] = $row->NOCAMION;
            }
        }
        $latlng = substr($latlng, 0, -1);
        $data['latlng'] = $latlng;
        $data['center'] = '';
        $data['chauffeursRte'] = $returnval;
        return $data;
    }

    public function getMenu($dateactive): string
    {
        return '
	          <li  id="liPageDay" style="cursor:pointer;list-style:none;margin-top:10px;">
	            <a  href="javascript:ShowDivDrop()"><i class="fa fa-bars" style="font-size:24px;"></i>&nbsp;&nbsp;Onglet création</a>
	          </li>
	          <li  id="liHresAides" style="margin-top:10px;cursor:pointer;list-style:none;margin-top:10px;">
	            <a  href="javascript:ShowHresAides()"><i class="fa fa-user-clock" style="font-size:24px;"></i>&nbsp;&nbsp;Onglet heures aides</a>

	          </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          <li  style="cursor:pointer;list-style:none;margin-top:10px;">
	            <a onclick="window.print()"
		          <i class="glyphicon glyphicon-print" style="font-size:24px;"></i>&nbsp;Imprimer
	            </a>
	          </li>
            <li>
              <a href="/excel/cedule/' . $dateactive . '" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
                <i class="fa fa-file-excel" style="font-size:24px;"></i>&nbsp;&nbsp;Cédule vers EXCEL
              </a>

            </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          <li style="cursor:pointer;list-style:none;margin-top:10px;">
	            <a href="javascript:ShowRemoveCedule()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Supprimer" data-content="Supprimer une ou des journ&eacute;es">
		          <i class="fa fa-trash" style="font-size:24px;color: red;"></i>&nbsp;&nbsp;Suppression de c&eacute;dule
	            </a>
	          </li>
            <li style="cursor:pointer;list-style:none;margin-top:10px;">
              <a href="/sanivan/copie" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Copier" data-content="Copier des journ&eacute;es ou des semaines">
                <i class="far fa-clone" style="font-size:24px;"></i>&nbsp;&nbsp;Cloner
              </a>
            </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
            <li style="cursor:pointer;list-style:none;margin-top:10px;">
              <a href="#" onclick="ShowVillesCachees()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Villes Cach&eacute;es" data-content="Afficher toutes les villes">
                <i class="far fa-clone" style="font-size:24px;"></i>&nbsp;&nbsp;Afficher Villes cachées
              </a>
            </li>
            <li style="list-style:none;margin-top:10px;" class="divider"></li>
	          </li>' . $this->getMenuTop($dateactive);
    }

    private function getMenuTop($dateactive): string
    {
        return '    <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/admin">
              <i class="fa fa-home" style="font-size:24px;"></i>&nbsp;&nbsp;Accueil
            </a>
          </li>
          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/sanivan/aujourdhui/8/' . $dateactive . '" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
              <i class="fa fa-tv" style="font-size:24px;"></i>&nbsp;&nbsp;TV
            </a>

          </li>

          <li style="cursor:pointer;list-style:none;margin-top:10px;">
            <a href="/sanivan/dictionnaires/1/1">
              <i class="fa fa-book" style="font-size:24px;"></i>&nbsp;Dictionnaire
            </a>

          </li>';
    }
}
