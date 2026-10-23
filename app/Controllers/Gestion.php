<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use App\Models\CedulesModel;
use App\Models\CreationcdModel;

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

    public function UpdCedule(...$args)
    {
        return $this->toCi3();
    }

    public function getHresAides(...$args)
    {
        return $this->toCi3();
    }

    public function PostCedule(...$args)
    {
        return $this->toCi3();
    }

    public function showToday(...$args)
    {
        return $this->toCi3();
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

    public function GetRHToDrop(...$args)
    {
        return $this->toCi3();
    }

    public function showPrintHresAides(...$args)
    {
        return $this->toCi3();
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

    public function postHHelpers(...$args)
    {
        return $this->toCi3();
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

    public function loadChauffeur(...$args)
    {
        return $this->toCi3();
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
