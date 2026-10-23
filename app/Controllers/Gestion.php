<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use App\Models\CedulesModel;
use App\Models\CreationcdModel;
use App\Models\RequetesModel;

/**
 * Port CI3 -> CI4 de co_gestion.
 * Chaque methode non portee est relayee a gestionvilles.local (CI3)
 * via toCi3() — voir AGENTS.md / MIGRATION.md.
 */
class Gestion extends Login
{
    // index(), attempt() et logout() sont herites de Login (deja portes).

    /**
     * CI3 : co_gestion/GotoHome — tableau de bord d'accueil.
     * PORTE : rend partials/home nativement (session requise).
     */
    public function GotoHome(...$args)
    {
        $ci3 = new Ci3Session();
        if (! $ci3->has('email')) {
            return $this->index();
        }

        helper('ci3view');
        $cedules = new CedulesModel();
        $creationcd = new CreationcdModel();

        $data = [
            'startVerifReqDel' => 0,
            'havedelcom' => 0,
            'nom' => $ci3->get('nom'),
            'email' => $ci3->get('email'),
            'privilege' => $ci3->get('privilege'),
            'typeabsences' => $cedules->GetTypeAbsences(),
            'CamionDrop' => $creationcd->GetHomeCamions(),
            'ChauffeurDrop' => $creationcd->GetChauffeursToDropHome(date('Y-m-d'), '1'),
            'AideDrop' => $creationcd->GetAidesToDrop(date('Y-m-d')),
            'tdbhome' => $creationcd->getTDBHome(),
            'employes' => $cedules->GetAllEmployes('0', '0'),
            'camions' => $cedules->GetAllCamions(),
            'typededechet' => $cedules->GetAllTypeProduits(),
        ];
        $data['navtop'] = view('partials/navtop', $data);
        $data['navleft'] = view('partials/navleft', $data);
        $ci3->set('LAST_PAGE', '/admin');

        return $this->response->setBody(view('partials/home', $data));
    }

    // CI3 : co_gestion/login (cible POST du formulaire de connexion) —
    // relaye a CI3 : authentifie, cree la session partagee et rend le
    // vrai tableau de bord (partials/home), exactement comme en CI3.
    public function login(...$args)
    {
        return $this->toCi3();
    }

    public function logoutchauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function getOptionSys(...$args)
    {
        return $this->toCi3();
    }

    public function setOptionSys(...$args)
    {
        return $this->toCi3();
    }

    public function setLastView(...$args)
    {
        return $this->toCi3();
    }

    public function postNewUser(...$args)
    {
        return $this->toCi3();
    }

    public function getInscription(...$args)
    {
        return $this->toCi3();
    }

    public function getPage(...$args)
    {
        return $this->toCi3();
    }

    public function getUsagers(...$args)
    {
        return $this->toCi3();
    }

    public function removeUser(...$args)
    {
        return $this->toCi3();
    }

    public function deleteUser(...$args)
    {
        return $this->toCi3();
    }

    public function updateUser(...$args)
    {
        return $this->toCi3();
    }

    public function showUser(...$args)
    {
        return $this->toCi3();
    }

    /**
     * CI3 : co_gestion/UpdCedule — mise a jour d'une cedule
     * (drag&drop camion/chauffeur/aides/secteur). PORTE.
     */
    public function UpdCedule(...$args)
    {
        $db = \Config\Database::connect();
        $input = $this->request;
        $chaufconfirme = (($input->getPostGet('idchauffeur') == '0') ? ' chaufconfirme = 0,' : '');
        $helperconfirme = (($input->getPostGet('idaide') == '0') ? ' helperconfirme = 0,' : '');
        $IsStart = (($input->getPostGet('camion') == '') ? ' IsStart = 0,' : '');
        $camion = (($input->getPostGet('camion') == '') ? ' NULL ' : $db->escape($input->getPostGet('camion')));
        $secteur = (($input->getPostGet('secteur') == '') ? ' NULL ' : $db->escape($input->getPostGet('secteur')));
        $idchauffeur = (($input->getPostGet('idchauffeur') == '') ? ' NULL ' : $db->escape($input->getPostGet('idchauffeur')));
        $idaide = (($input->getPostGet('idaide') == '') ? ' NULL ' : $input->getPostGet('idaide'));

        $dateactive = $input->getPostGet('dateactive');
        $id = $db->escape($input->getPostGet('id'));
        $helpers = '';
        if (strlen((string) $idaide) > 2 && strpos($idaide, ',', 2)) {
            $arAide = explode(',', $idaide);
            for ($iter = 0; $iter < count($arAide); $iter++) {
                switch ($iter) {
                    case 0:
                        $helpers .= 'HELPERS_1=' . $arAide[$iter] . ',';
                        break;
                    case 1:
                        $helpers .= 'HELPERS_2=' . $arAide[$iter] . ',';
                        break;
                    case 2:
                        $helpers .= 'HELPERS_3=' . $arAide[$iter] . ',';
                        break;
                    case 3:
                        $helpers .= 'HELPERS_4=' . $arAide[$iter] . ',';
                        break;
                }
            }
        }
        $helpers = substr($helpers, 0, -1);
        $strSQL = "UPDATE cedules
                    SET
                        secteur=$secteur,
                        NOCAMION=$camion,
                        idchauffeurs=$idchauffeur,
                        $chaufconfirme $helperconfirme $IsStart
                        $helpers
                    WHERE id = $id";
        $db->query($strSQL);
        return $this->showToday(4, $dateactive);
    }

    /**
     * CI3 : co_gestion/getHresAides — heures des aides (HTML ou vue
     * sanivan/heuresaides quand from=1). PORTE.
     */
    public function getHresAides($dateactive = null)
    {
        helper('ci3view');
        $cedules = new CedulesModel();
        $strSQL = '';
        $from = $this->request->getPostGet('from');
        if ($from == 1) {
            $data['ActiveDate'] = $dateactive;
            $data['listhresaides'] = $cedules->getWinHresAides($dateactive, $strSQL);
            return $this->response->setBody(view('sanivan/heuresaides', $data));
        }
        return $this->response->setBody($cedules->getHresAides($dateactive, $strSQL));
    }

    /**
     * CI3 : co_gestion/PostCedule — aiguillage add/upd d'une cedule.
     * PORTE.
     */
    public function PostCedule($typeprod = null, $camion = null, $chauffeur = null, $aide = null, $id = null, $idville = null, $date = null, $ville = null, $action = null)
    {
        switch ($action) {
            case 'add':
                return $this->AddToCedule($typeprod, $camion, $chauffeur, $aide, $date, $idville);
            case 'createcedule':
            case 'upd':
                return $this->UpdCedule($typeprod, $camion, $chauffeur, $aide, $id, -1, $date, $action);
        }
        return $this->response->setBody('');
    }

    /**
     * CI3 : co_gestion/AddToCedule (interne) — ajoute puis renvoie le
     * calendrier des cedules.
     */
    private function AddToCedule($typeprod, $camion, $chauffeur, $aide, $date, $ville)
    {
        $db = \Config\Database::connect();
        $cedules = new CedulesModel();
        $cedules->AddToCedule(
            $db->escape($typeprod),
            $db->escape($camion),
            $db->escape($chauffeur),
            $db->escape($aide),
            $db->escape($date),
            $db->escape($ville)
        );
        return $this->response->setBody($cedules->getCalendar());
    }

    /**
     * CI3 : co_gestion/showToday($from, $dateactive) — cedule du jour.
     * PORTE : from=1 (vue sanivan/today), 8 (today_tv), 99
     * (cedules_sani), 3 (JSON), 4/5 (JSON), 6/7 (HTML partiel).
     */
    public function showToday($from = null, $dateactive = null)
    {
        $ci3 = new Ci3Session();
        if (! $ci3->has('email')) {
            return redirect()->to('/admin/connexion');
        }
        $from = (int) $from;
        $db = \Config\Database::connect();
        $input = $this->request;
        helper('ci3view');
        $cedules = new CedulesModel();
        $creationcd = new CreationcdModel();
        $requetes = new RequetesModel();

        $datacd = [];
        if ($from == 8) {
            // Page TV: toujours la journee courante, jamais de cache.
            $date = date_create(date('Y-m-d'));
            $dateactive = $date->format('Y-m-d');
            $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->response->setHeader('Pragma', 'no-cache');
            $this->response->setHeader('Expires', '0');
        }
        $ActiveDate = $dateactive;
        $dateactive = $db->escape($dateactive);
        //--------------------------------------------------------------
        $typerh = $input->getPostGet('typerh') ?? '1,3,4,7';
        $typerh = (($typerh == 'chauf') ? '1,3,7' : $typerh);
        $typecam = $input->getPostGet('typecam') ?? 1;
        $typecedule = $input->getPostGet('typecedule') ?? 1;
        $activedrop = $input->getPostGet('activedrop') ?? 1;
        $limit = $input->getPostGet('limit') ?? 0;
        $notab = $input->getPostGet('notab') ?? 1;
        $AideIsShow = $input->getPostGet('AideIsShow') ?? 0;
        //--------------------------------------------------------------
        if ($from != 5) {
            $ci3->set('LAST_PAGE', '/sanivan/aujourdhui/1/' . $ActiveDate);
        } else {
            $from = 4;
        }
        $ci3->set('CHAMP_1', $from);
        $ci3->set('CHAMP_2', $ActiveDate);

        if (in_array($from, [1, 3, 8, 10, 99], true)) {
            $data = $creationcd->LoadCreateToday($ActiveDate, $typecedule);
            $data['employes'] = $cedules->GetAllEmployes('0', '0');
            $datacd = $cedules->LoadToday($ActiveDate, $from, $activedrop);
            $data['listhresaides'] = $cedules->getHresAides($ActiveDate, '');
            $data['updcamion'] = $cedules->GetTypeCamion($typecam);
            $data['camions'] = $cedules->GetAllCamions($typecam);
            $data['classstat'] = $requetes->GetAllClassStatut();
            // $data['requetes'] = $requetes->GetActivesRequetes(); (commente en CI3)
            $data['requetes'] = '';
            $data['mnureq'] = $requetes->GetDropActivesRequetes();
            $data['LastRow'] = $requetes->LastRow;
            $data['notab'] = $notab;
            $data['ActiveDate'] = $ActiveDate;
            $data['typecedule'] = $typecedule;
            $data['ActiveDrop'] = $activedrop;
            $data['limitcedule'] = $limit;
            $data['AideIsShow'] = $AideIsShow;
            $data['from'] = $from;
            $data['nom'] = $ci3->get('nom');
            $data['email'] = $ci3->get('email');
            $data['typerh'] = $cedules->GetTypeRH($typerh);
            $dataall = array_merge($data, $datacd);
            if ($from == 1) {
                return $this->response->setBody(view('sanivan/today', $dataall));
            }
            if ($from == 8) {
                return $this->response->setBody(view('sanivan/today_tv', $dataall));
            }
            if ($from == 99) {
                return $this->response->setBody(view('sanivan/cedules_sani', $dataall));
            }
            if ($from == 3 || $from == 10) {
                foreach ($dataall as $key => $value) {
                    $dataall[$key] = mb_convert_encoding(mb_convert_encoding((string) $value, 'ISO-8859-1', 'UTF-8'), 'UTF-8', 'ISO-8859-1');
                }
                return $this->response->setBody(json_encode($dataall));
            }
        } elseif ($from == 4 || $from == 5) {
            $data['ShowTransBo'] = 0;
            $data = $creationcd->LoadCreateToday($ActiveDate, $typecedule);
            $datacd = $cedules->LoadToday($ActiveDate, $from, $activedrop);
            $valtofind = $input->getPostGet('valtofind');
            if ($valtofind == '' || $valtofind === null) {
                $valtofind = '';
            } else {
                $valtofind = $db->escape('%' . $valtofind . '%');
                $valtofind = " AND (( Select CONCAT (Nom, ', ', left(Prenom, 1) )
	                      FROM reshumaine where id = suivis.idrh ) LIKE $valtofind )";
            }
            $data['employes'] = $cedules->GetAllEmployes('0', '0');
            $data['listhresaides'] = $cedules->getHresAides(date('Y-m-d'), $valtofind);
            $data['notab'] = $notab;
            $data['updcamion'] = $cedules->GetTypeCamion($typecam);
            $data['ActiveDate'] = $ActiveDate;
            $data['dtetoday'] = date('Y-m-d');
            $data['typecedule'] = $typecedule;
            $data['ActiveDrop'] = $activedrop;
            $data['limitcedule'] = $limit;
            $data['AideIsShow'] = $AideIsShow;
            $data['nom'] = $ci3->get('nom');
            $data['email'] = $ci3->get('email');
            $data['typerh'] = $cedules->GetTypeRH($typerh);
            $dataall = array_merge($data, $datacd);
            $dataNewHS = $cedules->checkNewInHS();
            $datapush = array_merge($dataall, $dataNewHS);
            foreach ($datapush as $key => $value) {
                $datapush[$key] = mb_convert_encoding(mb_convert_encoding((string) $value, 'ISO-8859-1', 'UTF-8'), 'UTF-8', 'ISO-8859-1');
            }
            return $this->response->setBody(json_encode($datapush));
        } elseif ($from == 6) {
            return $this->response->setBody($creationcd->LoadCedules($ActiveDate, $typecedule));
        } elseif ($from == 7) {
            return $this->response->setBody($cedules->LoadCedules($ActiveDate, 1));
        }
        return $this->response->setBody('');
    }

    public function showCreateToday(...$args)
    {
        return $this->toCi3();
    }

    public function showLigneCedule(...$args)
    {
        return $this->toCi3();
    }

    public function showCedules(...$args)
    {
        return $this->toCi3();
    }

    public function CalendrierDesVilles(...$args)
    {
        return $this->toCi3();
    }

    public function infoVille(...$args)
    {
        return $this->toCi3();
    }

    public function postRevenu(...$args)
    {
        return $this->toCi3();
    }

    public function calculerRevenuVille(...$args)
    {
        return $this->toCi3();
    }

    public function getRevenuResultat(...$args)
    {
        return $this->toCi3();
    }

    public function postCalDeploie(...$args)
    {
        return $this->toCi3();
    }

    public function postDaySanivan(...$args)
    {
        return $this->toCi3();
    }

    public function removeCalDeploie(...$args)
    {
        return $this->toCi3();
    }

    public function postDayCamion(...$args)
    {
        return $this->toCi3();
    }

    public function showValidSanivan(...$args)
    {
        return $this->toCi3();
    }

    public function getValidation(...$args)
    {
        return $this->toCi3();
    }

    public function getValidationChauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function PostSuivis(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteSuivis(...$args)
    {
        return $this->toCi3();
    }

    public function PostCam(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteCamion(...$args)
    {
        return $this->toCi3();
    }

    public function UpdEvent(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteEvent(...$args)
    {
        return $this->toCi3();
    }

    public function getSuivisCamion(...$args)
    {
        return $this->toCi3();
    }

    public function updStart(...$args)
    {
        return $this->toCi3();
    }

    public function updEmloye(...$args)
    {
        return $this->toCi3();
    }

    public function updCamion(...$args)
    {
        return $this->toCi3();
    }

    public function updTypeProd(...$args)
    {
        return $this->toCi3();
    }

    public function AddCamionSuivis(...$args)
    {
        return $this->toCi3();
    }

    public function AddRhSuivis(...$args)
    {
        return $this->toCi3();
    }

    public function UpdViderMat(...$args)
    {
        return $this->toCi3();
    }

    public function UpdSuivisRHRetour(...$args)
    {
        return $this->toCi3();
    }

    public function setProduit(...$args)
    {
        return $this->toCi3();
    }

    public function setAide(...$args)
    {
        return $this->toCi3();
    }

    public function addLigneAide(...$args)
    {
        return $this->toCi3();
    }

    public function setCamion(...$args)
    {
        return $this->toCi3();
    }

    public function addLigneCamion(...$args)
    {
        return $this->toCi3();
    }

    public function setSecteur(...$args)
    {
        return $this->toCi3();
    }

    public function setChauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function delEmploye(...$args)
    {
        return $this->toCi3();
    }

    public function delCam(...$args)
    {
        return $this->toCi3();
    }

    public function addLigneChauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function AddRow(...$args)
    {
        return $this->toCi3();
    }

    public function AddFirstRow(...$args)
    {
        return $this->toCi3();
    }

    public function AddVille(...$args)
    {
        return $this->toCi3();
    }

    public function UpdVille(...$args)
    {
        return $this->toCi3();
    }

    public function updRow(...$args)
    {
        return $this->toCi3();
    }

    public function updRang(...$args)
    {
        return $this->toCi3();
    }

    public function setVilleSuperviseur(...$args)
    {
        return $this->toCi3();
    }

    public function moveVilleGroupe(...$args)
    {
        return $this->toCi3();
    }

    public function setSupVisible(...$args)
    {
        return $this->toCi3();
    }

    public function getnewDate(...$args)
    {
        return $this->toCi3();
    }

    public function chkLoad(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteRow(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteVille(...$args)
    {
        return $this->toCi3();
    }

    public function copieCedule(...$args)
    {
        return $this->toCi3();
    }

    public function excCedule(...$args)
    {
        return $this->toCi3();
    }

    public function getWeekCedule(...$args)
    {
        return $this->toCi3();
    }

    public function postCopyCedule(...$args)
    {
        return $this->toCi3();
    }

    public function removeCedule(...$args)
    {
        return $this->toCi3();
    }

    public function camionInfo(...$args)
    {
        return $this->toCi3();
    }

    public function setIsDeletedCamion(...$args)
    {
        return $this->toCi3();
    }

    public function sendCamionInfo(...$args)
    {
        return $this->toCi3();
    }

    public function getAllCamion(...$args)
    {
        return $this->toCi3();
    }

    public function showRH(...$args)
    {
        return $this->toCi3();
    }

    public function showSuivisRHList(...$args)
    {
        return $this->toCi3();
    }

    public function showSuivisCamList(...$args)
    {
        return $this->toCi3();
    }

    public function getSuivisRH(...$args)
    {
        return $this->toCi3();
    }

    public function edtCam(...$args)
    {
        return $this->toCi3();
    }

    public function rhInfo(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteRH(...$args)
    {
        return $this->toCi3();
    }

    public function DeleteSuivisRH(...$args)
    {
        return $this->toCi3();
    }

    public function setNbCam(...$args)
    {
        return $this->toCi3();
    }

    public function sendRHInfo(...$args)
    {
        return $this->toCi3();
    }

    public function postRH(...$args)
    {
        return $this->toCi3();
    }

    public function getReloadTV(...$args)
    {
        return $this->toCi3();
    }

    /**
     * CI3 : co_gestion/GetRHToDrop — RH glissables pour la cedule.
     * PORTE.
     */
    public function GetRHToDrop($idtype = null, $activedate = null)
    {
        $idtype = (($idtype == 'chauf') ? '1,3,7' : $idtype);
        $creationcd = new CreationcdModel();
        return $this->response->setBody($creationcd->GetChauffeursToDrop($activedate, $idtype));
    }

    /**
     * CI3 : co_gestion/showPrintHresAides — page d'impression des
     * heures d'aides. PORTE.
     */
    public function showPrintHresAides($activedate = null)
    {
        helper('ci3view');
        $cedules = new CedulesModel();
        $strSQL = '';
        $input = $this->request;
        $data['ActiveDate'] = $activedate;
        $data['typecedule'] = $input->getPostGet('typecedule') ?? 1;
        $data['ActiveDrop'] = $input->getPostGet('activedrop') ?? 1;
        $data['notab'] = $input->getPostGet('notab') ?? 1;
        $data['listhresaides'] = $cedules->getHresAides($activedate, $strSQL);
        return $this->response->setBody(view('sections/print_hres_aides', $data));
    }

    public function showVille(...$args)
    {
        return $this->toCi3();
    }

    public function postVille(...$args)
    {
        return $this->toCi3();
    }

    public function showDictionnaire(...$args)
    {
        return $this->toCi3();
    }

    public function postDiction(...$args)
    {
        return $this->toCi3();
    }

    public function postStatutRequete(...$args)
    {
        return $this->toCi3();
    }

    public function postContenant(...$args)
    {
        return $this->toCi3();
    }

    public function getCamFromType(...$args)
    {
        return $this->toCi3();
    }

    public function getCamFromTypeTDB(...$args)
    {
        return $this->toCi3();
    }

    public function getTypeCam(...$args)
    {
        return $this->toCi3();
    }

    public function getRHFromType(...$args)
    {
        return $this->toCi3();
    }

    public function getAvisVerif(...$args)
    {
        return $this->toCi3();
    }

    public function sendMailAvis(...$args)
    {
        return $this->toCi3();
    }

    /**
     * CI3 : co_gestion/postHHelpers — MAJ d'un champ de suivis puis
     * renvoie la liste des heures d'aides. PORTE.
     */
    public function postHHelpers(...$args)
    {
        $db = \Config\Database::connect();
        $input = $this->request;
        $field = '';
        switch ($input->getPostGet('field')) {
            case 1:
                $field = 'StartTime';
                break;
            case 2:
                $field = 'EndTime';
                break;
            case 3:
                $field = 'pause';
                break;
            case 4:
                $field = 'note';
                break;
        }
        $from = $input->getPostGet('from');
        $idsuivis = $db->escape($input->getPostGet('idsuivis'));
        $value = $db->escape($input->getPostGet('value'));
        $ActiveDate = $input->getPostGet('activedate');
        $strSQL = "UPDATE suivis
                    SET
                        $field = $value
                    WHERE id = $idsuivis";
        $db->query($strSQL);
        $cedules = new CedulesModel();
        if ($from == 1) {
            return $this->response->setBody($cedules->getWinHresAides($ActiveDate, ''));
        }
        return $this->response->setBody($cedules->getHresAides($ActiveDate, ''));
    }

    public function postAllHresAide(...$args)
    {
        return $this->toCi3();
    }

    public function postViewHS(...$args)
    {
        return $this->toCi3();
    }

    public function reLoadCedule(...$args)
    {
        return $this->toCi3();
    }

    public function loadCedule(...$args)
    {
        return $this->toCi3();
    }

    /**
     * CI3 : co_gestion/loadChauffeur — liste des chauffeurs d'une
     * cedule (fragment HTML). PORTE.
     */
    public function loadChauffeur($typerh = null, $dateactive = null)
    {
        $db = \Config\Database::connect();
        $id = $db->escape($this->request->getPostGet('id'));
        $cedules = new CedulesModel();
        $objCedule = json_decode($cedules->getCedule($id, $dateactive, $typerh));
        return $this->response->setBody($objCedule->lstchauffeur ?? '');
    }

    public function DeleteSecteur(...$args)
    {
        return $this->toCi3();
    }

    public function showSecteur(...$args)
    {
        return $this->toCi3();
    }

    public function postSecteur(...$args)
    {
        return $this->toCi3();
    }

    public function getHSExist(...$args)
    {
        return $this->toCi3();
    }

    public function getRHExist(...$args)
    {
        return $this->toCi3();
    }

    public function setTransfertVille(...$args)
    {
        return $this->toCi3();
    }

    public function showPolyline(...$args)
    {
        return $this->toCi3();
    }

    public function getReduitPoly(...$args)
    {
        return $this->toCi3();
    }

    public function getSecteursByVille(...$args)
    {
        return $this->toCi3();
    }

    public function getPolylinesBySecteurVille(...$args)
    {
        return $this->toCi3();
    }

    public function setPolylineCedule(...$args)
    {
        return $this->toCi3();
    }

    public function getPolylinesGeoJson(...$args)
    {
        return $this->toCi3();
    }

    public function getPolylinesBySecteur(...$args)
    {
        return $this->toCi3();
    }

    public function setSecteurCedule(...$args)
    {
        return $this->toCi3();
    }

    public function togglePolylineCedule(...$args)
    {
        return $this->toCi3();
    }

    public function getPolylineCheckboxes(...$args)
    {
        return $this->toCi3();
    }

    public function getSelectedPolylines(...$args)
    {
        return $this->toCi3();
    }

    public function clearAllPolylines(...$args)
    {
        return $this->toCi3();
    }

    public function removePolyline(...$args)
    {
        return $this->toCi3();
    }

    public function removeSctPolyline(...$args)
    {
        return $this->toCi3();
    }

    public function movePolylineToFirst(...$args)
    {
        return $this->toCi3();
    }

    public function getPolylineGeoJsonById(...$args)
    {
        return $this->toCi3();
    }

    public function Requete(...$args)
    {
        return $this->toCi3();
    }

    public function postRequete(...$args)
    {
        return $this->toCi3();
    }

    public function getNomSecteur(...$args)
    {
        return $this->toCi3();
    }

    public function setConfirmePresence(...$args)
    {
        return $this->toCi3();
    }

    public function sendConfirm(...$args)
    {
        return $this->toCi3();
    }

    public function expCeduleToExcel(...$args)
    {
        return $this->toCi3();
    }

    public function expHresAidesToExcel(...$args)
    {
        return $this->toCi3();
    }

    public function resetPassW(...$args)
    {
        return $this->toCi3();
    }

    public function ValidOldPassW(...$args)
    {
        return $this->toCi3();
    }

    public function PostresetPassW(...$args)
    {
        return $this->toCi3();
    }

    public function ValidStrongPassW(...$args)
    {
        return $this->toCi3();
    }

    public function sendPassword(...$args)
    {
        return $this->toCi3();
    }

    public function excRestPassw(...$args)
    {
        return $this->toCi3();
    }

    public function getSuivisCamExist(...$args)
    {
        return $this->toCi3();
    }

    public function getCalendrier(...$args)
    {
        return $this->toCi3();
    }

    public function getRHCal(...$args)
    {
        return $this->toCi3();
    }

    public function getCalendrierCam(...$args)
    {
        return $this->toCi3();
    }

    public function getCamCal(...$args)
    {
        return $this->toCi3();
    }

    public function makeCamPDF(...$args)
    {
        return $this->toCi3();
    }

    public function makeRhPDF(...$args)
    {
        return $this->toCi3();
    }

    public function getCamFromTypeCal(...$args)
    {
        return $this->toCi3();
    }

    public function getRHtoDropCal(...$args)
    {
        return $this->toCi3();
    }

    public function getTypeCamCal(...$args)
    {
        return $this->toCi3();
    }

    public function sendConfirmVille(...$args)
    {
        return $this->toCi3();
    }

    public function getListMailForVille(...$args)
    {
        return $this->toCi3();
    }

    public function postMail(...$args)
    {
        return $this->toCi3();
    }

    public function ConfirmeVilleMail(...$args)
    {
        return $this->toCi3();
    }

    public function postReponse(...$args)
    {
        return $this->toCi3();
    }

    public function Reponse(...$args)
    {
        return $this->toCi3();
    }

    public function insertDte(...$args)
    {
        return $this->toCi3();
    }

    public function postAdresseRequete(...$args)
    {
        return $this->toCi3();
    }

    public function setDateRequete(...$args)
    {
        return $this->toCi3();
    }

    public function setReponseVue(...$args)
    {
        return $this->toCi3();
    }

    public function setRequeteVue(...$args)
    {
        return $this->toCi3();
    }

    public function getListRequetes(...$args)
    {
        return $this->toCi3();
    }

    public function verifRequetes(...$args)
    {
        return $this->toCi3();
    }

    public function RequetesPDF(...$args)
    {
        return $this->toCi3();
    }

    public function ReponsesPDF(...$args)
    {
        return $this->toCi3();
    }

    public function getVilleNom(...$args)
    {
        return $this->toCi3();
    }

    public function RequetesEXCEL(...$args)
    {
        return $this->toCi3();
    }

    public function resetAllVille(...$args)
    {
        return $this->toCi3();
    }

    public function moveCollecte(...$args)
    {
        return $this->toCi3();
    }

    public function delCollecte(...$args)
    {
        return $this->toCi3();
    }

}
