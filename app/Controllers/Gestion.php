<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use App\Libraries\LibCalendrier;
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

    // Proprietes de classe du controleur CI3 (accumulateurs entre
    // methodes, equivalents aux private/public de co_gestion).
    private $idiom = 'fr';
    private $history;
    private $message_error = '';
    private $valtofinddrop = '';
    public $MenuNonActive = '';
    public $MenuActive = '';
    private $data;
    private $datanew;

    /**
     * Equiv. du __construct de co_gestion : les deux modeles
     * pre-charges etaient disponibles dans toutes les methodes.
     */
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->creationcd = new \App\Models\CreationcdModel();
        $this->cedules    = new \App\Models\CedulesModel();
    }

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
    public function login()
    {    
            $this->load->model("login_model", "login");
            /** @var string $email */
            $email = '';
            $email = $this->input->post_get('email', TRUE);
            
            if (isset($email)) {
                //$name=$this->input->post('name');
                //$email = $this->input->post('email');
                /** @var array $data */
    
                $data = [
                    'email' => $this->input->post_get('email', TRUE),
                    'password' => $this->input->post_get('password', TRUE)
                ];
                
                /** @var object $result */
                $result = new \stdClass();
                $result = $this->login->validate_user($data);
                //$result = $this->login->updateUser();
                if (isset($result)) {
                    $this->session->set_userdata('nom', $result->prenom.' '.$result->nom);
                    $this->session->set_userdata('email', $result->email);
                    $this->session->set_userdata('login_in', TRUE);
                    $this->session->set_userdata('user_id', $result->id_user);
                    $this->session->set_userdata('code', $this->input->post_get('email', TRUE));
                    $this->session->set_userdata('session_id', $this->session->session_id);
                    $this->session->set_userdata('privilege', $result->privilege);
                    //$session_id = $this->db->escape($this->session->session_id);
                    //$user_id = $result->id_user;
                    if ($result->forcechange == 1) {
                        return redirect()->to(ci3_url().'login/change?email='.$this->input->post_get('email', TRUE));
                        return;
                    }
                    //        $strSQL = "UPDATE webuser
                    //                SET
                    //                  SessionLastUpd=NOW(),
                    //                  SessionStart=NOW()
                    //                WHERE id_user = ".$result->id_user;
                    //$query = $this->db->query($strSQL);
                    /** @var array  $data */
                    $this->load->model("Cedules_model", "cedules");
                    //$data = $this->cedules->GetAllVillesCal();
                    $date = date_create(date('Y-m-d'));
                    $ActiveDate = $date->format('Y-m-d');
                    $dateend = date_create(date('Y-m-d'));
                    date_sub($date, date_interval_create_from_date_string("1 year"));
                    $this->objDate = (object) date_parse($date->format('Y-m-d'));
                    $data['dtestart'] = (($this->objDate->month < 10) ? '0'.$this->objDate->month : $this->objDate->month).'/01/'.$this->objDate->year;
                    $dateend->modify('last day of this month');
                    $data['dteend'] = $dateend->format('m/d/Y');
                    $data['alldate'] = $this->objDate->year.'-'.(($this->objDate->month < 10) ? '0'.$this->objDate->month : $this->objDate->month).'-01'.'_'.$dateend->format('Y-m-d');
                    $data['CamionList'] = $this->cedules->GetCamions();
                    $data['listcamions'] = $this->cedules->GetCamionList();
                    $data['addcamion'] = $this->cedules->ShowAddCamions();
                    $data['typecamion'] = $this->cedules->GetTypeCamion(1);
                    $data['typerh'] = $this->cedules->GetTypeRH(1);
                    $this->load->model("Creationcd_model", "creationcd");
                    $data['typeabsences'] = $this->cedules->GetTypeAbsences();
                    $data['CamionDrop'] = $this->creationcd->GetHomeCamions();
                    $data['ChauffeurDrop'] = $this->creationcd->GetChauffeursToDropHome(date('Y-m-d'), '1');
                    $data['AideDrop'] = $this->creationcd->GetAidesToDrop(date('Y-m-d'), '2');
                    $data['tdbhome'] = $this->creationcd->getTDBHome();
                    $data['employes'] = $this->cedules->GetAllEmployes('0', '0');
                    $data['camions'] = $this->cedules->GetAllCamions();
                    $arrDate = explode('_', $data['alldate']);
                    $data['tdbhome'] = $this->creationcd->getTDBHome();
                    ;
                    $data['nom'] = $this->session->userdata('nom');
                    $data['email'] = $this->session->userdata('email');
                    $data['privilege'] = $this->session->userdata('privilege');
                    $data['navtop'] = view('partials/navtop', $data);
                    $data['navleft'] = view('partials/navleft', $data);
                    $data['havedelcom'] = 0;
    
                    $HavePrivilege = true;
    
                    $data['startVerifReqDel'] = 1;
                    $from = $this->input->post_get('from', TRUE);
                    if ($from == 1) {
                        $date = date("Y-m-d");
                        $url = ci3_url("sanivan/routecam/$date?idchauffeur=0");
                        return redirect()->to($url);
                    } else {
                        echo view('partials/home', $data);
                    }
    
                } else {
                    $this->message_error = 'Courriel ou mot de passe invalide.';
                    $this->index();
                }
    
            } else {
                $this->index();
            }
    }


    public function logoutchauffeur()
    {    
            /** @var array  $sessionvar */
            $sessionvar = $this->session->all_userdata();
            $this->session->unset_userdata($sessionvar);
            $this->session->unset_userdata(['email', 'nom']);
            $this->session->set_userdata('login_in', FALSE);
            $this->session->sess_destroy();
            $this->output->set_header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_header("Expires: ".gmdate("D, d M Y H:i:s").' GMT');
            $this->pageLogin();
    }


    public function getOptionSys()
    {    
            $data = array();
            $strSQL = "SELECT * FROM optionssys";
            $optsys = $this->db->query($strSQL);
            foreach ($optsys->getResultArray() as $row) {
                $data = $row;
            }
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            $this->load->model("Cedules_model", "cedules");
            $data['chauffeurs'] = $this->cedules->GetChauffeurs($data['scantest']);
            $this->session->set_userdata('LAST_PAGE', 8);
            echo view('bureau/option', $data);
            //--------------------------------------------------------------
    }


    public function setOptionSys()
    {    
            /** @var string  $strSQL */
            $cie = $this->db->escape($this->input->post_get('cie'));
            $email = $this->db->escape($this->input->post_get('email'));
            $logo = trim($this->input->post_get('logo'));
            $Adresse = $this->db->escape($this->input->post_get('Adresse'));
            $ville = $this->db->escape($this->input->post_get('ville'));
            $province = $this->db->escape($this->input->post_get('province'));
            $autosend = $this->db->escape($this->input->post_get('autosend'));
            $timetosend = $this->db->escape($this->input->post_get('timetosend'));
            $NeedConfirm = $this->db->escape($this->input->post_get('NeedConfirm'));
            $TPS = $this->db->escape($this->input->post_get('TPS'));
            $TVQ = $this->db->escape($this->input->post_get('TVQ'));
            $NoTPS = $this->db->escape($this->input->post_get('NoTPS'));
            $NoTVQ = $this->db->escape($this->input->post_get('NoTVQ'));
            //----------------------------------------------------------
            $FraisLivraison = $this->db->escape(str_replace(',', '.', $this->input->post_get('FraisLivraison')));
            $FraisRedevanceTM = $this->db->escape(str_replace(',', '.', $this->input->post_get('FraisRedevanceTM')));
            $FraisPoids = $this->db->escape(str_replace(',', '.', $this->input->post_get('FraisPoids')));
            $SurchargeSite = $this->db->escape(str_replace(',', '.', $this->input->post_get('SurchargeSite')));
            $DureeLocation = $this->db->escape($this->input->post_get('DureeLocation'));
            $montantLivraison = $this->db->escape(str_replace(',', '.', $this->input->post_get('montantLivraison')));
            $montantTransBo = $this->db->escape(str_replace(',', '.', $this->input->post_get('montantTransBo')));
            $salairechauffeur = $this->db->escape(str_replace(',', '.', $this->input->post_get('salairechauffeur')));
            $salalaireaide = $this->db->escape(str_replace(',', '.', $this->input->post_get('salalaireaide')));
            $montantfuel = $this->db->escape(str_replace(',', '.', $this->input->post_get('montantfuel')));
            $FraisDeglacage = $this->db->escape(str_replace(',', '.', $this->input->post_get('FraisDeglacage')));
            $FraisLavage = $this->db->escape(str_replace(',', '.', $this->input->post_get('FraisLavage')));
            $MilageOntario = $this->db->escape(str_replace(',', '.', $this->input->post_get('MilageOntario')));
            //-----------------------------------------------------------
            $IsBeton = ((trim($this->input->post_get('IsBeton')) == '') ? 0 : $this->input->post_get('IsBeton'));
            ;
            $IsAsphalte = ((trim($this->input->post_get('IsAsphalte')) == '') ? 0 : $this->input->post_get('IsAsphalte'));
            $IsBranches = ((trim($this->input->post_get('IsBranches')) == '') ? 0 : $this->input->post_get('IsBranches'));
            $IsFumier = ((trim($this->input->post_get('IsFumier')) == '') ? 0 : $this->input->post_get('IsFumier'));
            $IsTerre = ((trim($this->input->post_get('IsTerre')) == '') ? 0 : $this->input->post_get('IsTerre'));
            $IsBois = ((trim($this->input->post_get('IsBois')) == '') ? 0 : $this->input->post_get('IsBois'));
            $IsAmiante = ((trim($this->input->post_get('IsAmiante')) == '') ? 0 : $this->input->post_get('IsAmiante'));
            $IsMateriaux = ((trim($this->input->post_get('IsMateriaux')) == '') ? 0 : $this->input->post_get('IsMateriaux'));
            //-----------------------------------------------------------
            $color_beton = $this->db->escape($this->input->post_get('color_beton'));
            $color_asphalte = $this->db->escape($this->input->post_get('color_asphalte'));
            $color_branches = $this->db->escape($this->input->post_get('color_branches'));
            $color_fumier = $this->db->escape($this->input->post_get('color_fumier'));
            $color_terre = $this->db->escape($this->input->post_get('color_terre'));
            $color_bois = $this->db->escape($this->input->post_get('color_bois'));
            $color_amiante = $this->db->escape($this->input->post_get('color_amiante'));
            $color_materiaux = $this->db->escape($this->input->post_get('color_materiaux'));
            //-----------------------------------------------------------
            if ($logo != '') {
                $logo = "Logo=".$this->db->escape($logo).',';
            }
            $strSQL = "UPDATE optionssys
    	                      SET
    		                      Cie=$cie,
    		                      Courriel=$email,
    		                      $logo
    		                      Adresse=$Adresse,
    		                      Ville=$ville,
    		                      Province=$province,
    		                      autosend=$autosend,
    		                      timetosend=$timetosend,
    		                      NeedConfirm=$NeedConfirm,
    		                      TPS=$TVQ,
    		                      TVQ=$TPS,
    		                      NoTPS=$NoTPS,
    		                      NoTVQ=$NoTVQ,
    		                      FraisLivraison=$FraisLivraison,
    		                      FraisRedevanceTM=$FraisRedevanceTM,
    		                      FraisPoids=$FraisPoids,
    		                      SurchargeSite=$SurchargeSite,
    		                      DureeLocation=$DureeLocation,
                              montantLivraison = $montantLivraison,
                              montantTransBo=$montantTransBo,
                              salairechauffeur=$salairechauffeur,
                              salalaireaide=$salalaireaide,
                              montantfuel=$montantfuel,
                              FraisDeglacage=$FraisDeglacage,
                              FraisLavage=$FraisLavage,
                              MilageOntario=$MilageOntario,
                              IsBeton = $IsBeton,
                              IsAsphalte = $IsAsphalte,
                              IsBranches = $IsBranches,
                              IsFumier = $IsFumier,
                              IsTerre = $IsTerre,
                              IsAmiante = $IsAmiante,
                              IsBois = $IsBois,
                              IsMateriaux = $IsMateriaux,
                              color_beton = $color_beton,
                              color_asphalte = $color_asphalte,
                              color_branches = $color_branches,
                              color_fumier = $color_fumier,
                              color_terre = $color_terre,
                              color_bois = $color_bois,
                              color_materiaux = $color_materiaux,
                              color_amiante = $color_amiante
    	                      WHERE Id = 2";
            $this->db->query($strSQL);
            // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
    }


    public function setLastView()
    {    
            $this->load->model("login_model", "login");
            /** @var string $email */
            $pass = $this->input->post_get('pass');
            $email = '';
            $email = $this->session->userdata('email');
            if (isset($email)) {
    
                $data = [
                    'email' => $this->session->userdata('email'),
                    'password' => $this->input->post_get('pass', TRUE)
                ];
                /** @var object $result */
                $result = new \stdClass();
                $result = $this->login->validate_user($data);
                if (! empty($result)) {
                    $this->session->set_userdata('nom', $result->prenom.' '.$result->nom);
                    $this->session->set_userdata('email', $result->email);
                    $this->session->set_userdata('login_in', TRUE);
                    $this->session->set_userdata('user_id', $result->id_user);
                    $session_id = $this->db->escape($this->session->session_id);
                    $user_id = $result->id_user;
                    $strSQL = "SELECT * FROM session_active WHERE idsession = $session_id";
                    $query = $this->db->query($strSQL);
                    $row = $query->getRow();
                    if (! isset($row)) {
                        $strSQL = "INSERT INTO session_active
                                (idsession, user_id, session_start, session_last_upd)
                                VALUES ($session_id, $user_id, NOW(), NOW())";
                        $this->db->query($strSQL);
                    }
                    echo $this->session->userdata('LAST_PAGE');
                } else {
                    echo '/session/bloque';
                }
            }
    }


    public function postNewUser()
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                $this->load->model("Menu_model", "menu");
                $this->load->library('LibInscription');
                $this->libinscription->postNewUser();
                //--------------------------------
                $this->getUsagers();
            }
    }


    public function getInscription()
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                $this->load->model("menu_model", "menu");
                $this->load->model("Usagers_model", "usagers");
                //$this->usagers->postNewUser();
                /** @var array  $data */
                $data['nom'] = $this->session->userdata('nom');
                $data['email'] = $this->session->userdata('email');
                $data['privileges'] = $this->usagers->getPrivilege();
                $this->session->set_userdata('LAST_PAGE', 11);
                echo view('partials/add_usager', $data);
            }
    }


    public function getPage(...$args)
    {
        return $this->toCi3();
    }

    public function getUsagers()
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                $this->load->model("Usagers_model", "usagers");
                /** @var array  $data */
                $this->load->model("Cedules_model", "cedules");
                //$data = $this->cedules->GetAllVillesCal();
                $data['nom'] = $this->session->userdata('nom');
                $data['email'] = $this->session->userdata('email');
                /** @var array  $datausager */
                $datausager = $this->usagers->getAllUsagers();
                /** @var array  $dataall */
                $dataall = array_merge($data, $datausager);
                $this->session->set_userdata('LAST_PAGE', 12);
                echo view('partials/get_usagers', $dataall);
            }
    }


    public function removeUser($iduser)
    {    
            if (! $this->session->has_userdata('email')) {
                return 'NON';
            } else {
                $this->load->library('LibInscription');
                return $this->libinscription->removeUser($iduser);
                //--------------------------------
            }
    }


    public function deleteUser()
    {    
            $strSQL = 'DELETE From webuser where id_user='.$this->db->escape($this->input->get('iduser'));
            $this->db->query($strSQL);
    }


    public function updateUser()
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                $this->load->model("Menu_model", "menu");
                $this->load->model("Login_model", "login");
                echo $this->login->updateUser();
                //--------------------------------
            }
    }


    public function showUser()
    {    
            $id = $this->input->get('iduser');
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                $this->load->model("menu_model", "menu");
                $this->load->model("Usagers_model", "usagers");
                /** @var array  $datausager */
                $datausager = $this->usagers->getUsager($id);
                $datausager['nom'] = $this->session->userdata('nom');
                $datausager['email'] = $this->session->userdata('email');
                $this->session->set_userdata('LAST_PAGE', 12);
                echo view('partials/usager_modif', $datausager);
            }
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

    public function showCreateToday($from, $ActiveDrop, $dateactive)
    {    
            /** @var string $ActiveDate */
            /** @var DateTime $date */
            //$date = date_create($dateactive);
    
            //switch($nbjour)
            //{
            //  case -1:
    
            //    date_sub($date,date_interval_create_from_date_string("1 day"));
            //    $ActiveDate = $date->format('Y-m-d');
            //    break;
            //  case 1:
            //    date_add($date,date_interval_create_from_date_string("1 day"));
            //    $ActiveDate = $date->format('Y-m-d');
            //    break;
            //  default:
            //$ActiveDate = $dateactive;
            //}
            $ActiveDate = $dateactive;
            //$dateactive = $this->db->escape($ActiveDate);
            //--------------------------------------------------------------
            $this->load->model("Creationcd_model", "creationcd");
            $data = $this->creationcd->LoadCreateToday($dateactive, $from);
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['ActiveDate'] = $ActiveDate;
            $data['ActiveDrop'] = $ActiveDrop;
            $this->session->set_userdata('LAST_PAGE', 2);
            $this->session->set_userdata('CHAMP_1', $from);
            $this->session->set_userdata('CHAMP_2', $ActiveDrop);
            $this->session->set_userdata('CHAMP_3', $ActiveDate);
            echo view('sanivan/createcedule', $data);
    }


    public function showLigneCedule($id)
    {    
            $id = $this->db->escape($id);
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->getCedule($id);
    }


    /**
     * CI3 : co_gestion/showCedules — calendrier des villes.
     * PORTE : vue sanivan/villescalendar.
     */
    public function showCedules(...$args)
    {
        $ci3 = new Ci3Session();
        helper('ci3view');
        $cedules = new CedulesModel();
        $data = $cedules->GetAllVillesCal();
        $datacal = $cedules->getCalendrierVille(42);
        $data['nom'] = $ci3->get('nom');
        $data['email'] = $ci3->get('email');
        $dataall = array_merge($data, $datacal);
        // Variables lues par villescalendar que CI3 tolerait en
        // undefined : on les initialise vides.
        $dataall += [
            'moisseul' => '', 'typederamassage' => '', 'redep' => '',
            'manuel' => '', 'revenu' => '', 'nomenclature' => '',
            'optramassages' => '', 'secteurs' => '', 'ActiveDate' => date('Y-m-d'),
            'listmois' => '', 'listyear' => '', 'optvilles' => '',
        ];
        for ($i = 1; $i <= 12; $i++) {
            $dataall += ['calendrier' . $i => ''];
        }
        $ci3->set('LAST_PAGE', 5);
        return $this->response->setBody(view('sanivan/villescalendar', $dataall));
    }

    public function CalendrierDesVilles($dateactive)
    {    
            /** @var string $ActiveDate */
            $ActiveDate = $dateactive;
            //--------------------------------------------------------------
            //$this->load->library('LibCalendrier');
            $this->load->model("Cedules_model", "sanivan");
            $data = $this->sanivan->GetAllVillesCal();
            //$data = $this->libcalendrier->getCalendrierVille(0);
            $this->data['dataCD'] = '';
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['ActiveDate'] = $ActiveDate;
            $data['recyclage'] = '';
            $data['dechets'] = '';
            $data['organique'] = '';
            $data['grosrebuts'] = '';
            $data['rbCamionrecyclageRE'] = '';
            $data['rbCamiondechetsRE'] = '';
            $data['rbCamionorganiqueRE'] = '';
            $data['rbCamiongrosrebutsRE'] = '';
            $data['rbCamionrecyclageROBOT'] = '';
            $data['rbCamiondechetsROBOT'] = '';
            $data['rbCamionorganiqueROBOT'] = '';
            $data['rbCamiongrosrebutsROBOT'] = '';
            $this->data['field_RE'] = '';
            $this->data['field_ROBOT'] = '';
            $this->data['DateRamasage'] = '';
            $data['typederamassage'] = '1';
            $this->session->set_userdata('LAST_PAGE', 2);
            //echo view('sanivan/villescalendrier', $data);
            echo view('sanivan/villescalendar', $data);
    }


    /**
     * CI3 : co_gestion/infoVille — infos + calendriers d'une ville
     * (12 mois glissants ou mois seul). PORTE.
     */
    public function infoVille(...$args)
    {
        $ci3 = new Ci3Session();
        helper('ci3view');
        $input = $this->request;
        $idville = $input->getPostGet('idville');
        $activeDate = $input->getPostGet('selectedDay');
        $redep = $input->getPostGet('redep');
        if ($input->getPostGet('from') == '2') {
            $activeDate = date('Y-m-d');
        }
        $arrMois = explode('-', $activeDate) + [1 => null];
        $typederamassage = $input->getPostGet('typederamassage');
        $cal = new LibCalendrier();

        $data = [];
        if ($input->getPostGet('moisseul') == '1') {
            $data = $cal->getCalendrierVille($idville, $activeDate, '');
        } else {
            $idmois = 1;
            for ($iter = (int) $arrMois[1]; $iter <= 13; $iter++) {
                if ($iter == $arrMois[1]) {
                    $data = $cal->getCalendrierVille($idville, $activeDate, $idmois);
                } else {
                    if ($iter == 13) {
                        $arrMois[0]++;
                        $arrMois[1] = '01';
                        $iter = 1;
                    } else {
                        $arrMois[1] = $iter;
                    }
                    $dataall = $cal->getCalendrierVille($idville, $arrMois[0] . '-' . $arrMois[1] . '-01', $idmois);
                    $data['calendrier' . $idmois] = $dataall['calendrier' . $idmois];
                }
                $idmois++;
                if ($idmois > 12) {
                    break;
                }
            }
        }
        $data['redep'] = ($redep == 1) ? $redep : '';
        $data['chauffeurs'] = '';
        $data['aides'] = '';
        $data['camions'] = '';
        $data['nom'] = $ci3->get('nom');
        $data['email'] = $ci3->get('email');
        $data['field_RE'] = '';
        $data['moisseul'] = $input->getPostGet('moisseul');
        $data['field_ROBOT'] = '';
        $data['ActiveDate'] = $activeDate;
        $data['typederamassage'] = $typederamassage;
        $ci3->set('LAST_PAGE', 2);
        if ($input->getPostGet('from') == '1') {
            return $this->response->setBody(json_encode($data));
        }
        if ($input->getPostGet('moisseul') == '1') {
            return $this->response->setBody(view('sanivan/villescalendarMois', $data));
        }
        return $this->response->setBody(view('sanivan/villescalendar', $data));
    }

    public function postRevenu()
    {    
            //--------------------------------------------------------------
            // IsFixeMont, IsTonnage, MontFixe, HaveRedevance, MontRedevance, MontTonne, TypeRamassage, nbCollecte
            $this->load->library('LibCalendrier');
            $idville = $this->db->escape($this->input->post_get('idville'));
            $MontFixe = $this->db->escape(str_replace(',', '.', $this->input->post_get('MontFixe')));
            $MontRedevance = $this->db->escape(str_replace(',', '.', $this->input->post_get('MontRedevance')));
            $MontTonne = $this->db->escape(str_replace(',', '.', $this->input->post_get('MontTonne')));
            $nbCollecte = $this->db->escape($this->input->post_get('nbCollecte'));
            $inHour = $this->db->escape(str_replace(',', '.', $this->input->post_get('inHour')));
            $Disposition = $this->db->escape(str_replace(',', '.', $this->input->post_get('Disposition')));
            $AuBac = $this->db->escape(str_replace(',', '.', $this->input->post_get('AuBac')));
            $typederamassage = $this->db->escape($this->input->post_get('typederamassage'));
            /** @var string $strSQL */
            $strSQL = "SELECT id FROM revenuville WHERE idville = $idville
    	                      AND TypeRamassage = $typederamassage";
            $calendrier = $this->db->query($strSQL);
            $num_row = $calendrier->getNumRows();
            if ($num_row == 0) {
                /** @var string  $strSQL */
                $strSQL = "INSERT INTO revenuville
    	                (idville, inHour, MontFixe, Disposition, MontRedevance, TypeRamassage, AuBac, MontTonne, nbCollecte)
    	                  VALUES ($idville, $inHour, $MontFixe, $Disposition, $MontRedevance, $typederamassage, $AuBac, $MontTonne, $nbCollecte)";
                $this->db->query($strSQL);
            } else {
                $rev = $calendrier->getRow(0);
                $idrev = $rev->id;
                /** @var string  $strSQL */
                $strSQL = "UPDATE revenuville
    	                      SET
    		                      inHour=$inHour,
    		                      MontFixe=$MontFixe,
    		                      Disposition=$Disposition,
    		                      MontRedevance=$MontRedevance,
    		                      AuBac=$AuBac,
    		                      MontTonne=$MontTonne,
    		                      nbCollecte=$nbCollecte
    	                      WHERE id= $idrev";
                $this->db->query($strSQL);
            }
            $error = $this->db->error();
            if (! empty($error['code'])) {
                $msgerr = "Une erreur s'est produite.";
            } else {
                $msgerr = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
            }
            echo $msgerr;
    }


    public function calculerRevenuVille()
    {    
            $idville    = intval($this->input->post_get('idville', TRUE));
            $date_debut = $this->input->post_get('date_debut', TRUE);
            $date_fin   = $this->input->post_get('date_fin', TRUE);
    
            if ($idville <= 0 || empty($date_debut) || empty($date_fin)) {
                echo json_encode(array('status' => 'error', 'message' => 'Paramètres manquants.'));
                return;
            }
    
            $this->load->model('Revenu_model', 'revenu_model');
    
            // Calculer pour chaque jour de la période
            $debut = new \DateTime($date_debut);
            $fin   = new \DateTime($date_fin);
            $fin->modify('+1 day');
            $interval = new \DateInterval('P1D');
            $periode  = new \DatePeriod($debut, $interval, $fin);
            $totalLignes = 0;
    
            foreach ($periode as $jour) {
                $dateStr = $jour->format('Y-m-d');
                $lignes = $this->revenu_model->calculerRevenus($dateStr, $idville);
                $totalLignes += count($lignes);
            }
    
            // Retourner le tableau HTML des résultats
            $html = $this->revenu_model->getRevenuTableHTML($idville, $date_debut, $date_fin);
    
            echo json_encode(array(
                'status'  => 'ok',
                'message' => $totalLignes . ' lignes calculées.',
                'html'    => $html
            ));
    }


    public function getRevenuResultat()
    {    
            $idville    = intval($this->input->post_get('idville', TRUE));
            $date_debut = $this->input->post_get('date_debut', TRUE);
            $date_fin   = $this->input->post_get('date_fin', TRUE);
    
            if ($idville <= 0 || empty($date_debut) || empty($date_fin)) {
                echo json_encode(array('status' => 'error', 'message' => 'Paramètres manquants.'));
                return;
            }
    
            $this->load->model('Revenu_model', 'revenu_model');
            $html = $this->revenu_model->getRevenuTableHTML($idville, $date_debut, $date_fin);
    
            echo json_encode(array(
                'status' => 'ok',
                'html'   => $html
            ));
    }


    /**
     * CI3 : co_gestion/postCalDeploie — deploiement de ramassages en
     * frequence (hebdo, 2 sem., 1er/2e/3e jour du mois). PORTE.
     */
    public function postCalDeploie(...$args)
    {
        $db = \Config\Database::connect();
        $input = $this->request;
        $arrday = [];
        $idville = $input->getPostGet('idville');
        $typecam = $db->escape($input->getPostGet('typecam'));
        $activeDate = $input->getPostGet('selectedDay');
        $startDate = new \DateTime($input->getPostGet('dtestartdeploie'));
        $endDate = new \DateTime($input->getPostGet('dteenddeploie'));
        $frequence = $input->getPostGet('frequence');
        $typederamassage = $db->escape($input->getPostGet('typederamassage'));
        if ($input->getPostGet('lun') == 1) {
            $arrday['lun'] = 'Mon';
        }
        if ($input->getPostGet('mar') == 1) {
            $arrday['mar'] = 'Tue';
        }
        if ($input->getPostGet('mer') == 1) {
            $arrday['mer'] = 'Wed';
        }
        if ($input->getPostGet('jeu') == 1) {
            $arrday['jeu'] = 'Thu';
        }
        if ($input->getPostGet('ven') == 1) {
            $arrday['ven'] = 'Fri';
        }
        $this->FrequenceDeploie($idville, $arrday, $startDate, $endDate, $typecam, $typederamassage, $frequence);
        return $this->infoVille();
    }

    /**
     * CI3 : co_gestion/postDaySanivan — ajout d'un ramassage au
     * calendrier d'une ville (cree grp_cedules + cedule). PORTE.
     */
    public function postDaySanivan($idville = null)
    {
        $db = \Config\Database::connect();
        $input = $this->request;
        $returnval = '';
        $typecam = $db->escape($input->getPostGet('typecam'));

        $activeDate = $input->getPostGet('selectedDay');
        $annee = substr($activeDate, 0, 4);
        $mois = substr($activeDate, 4, 2);
        $jour = substr($activeDate, -2);
        $dateToScroll = $db->escape($annee . '-' . $mois . '-' . $jour);
        $typederamassage = $db->escape($input->getPostGet('typederamassage'));
        $strSQL = "SELECT Id, idville, Dateramassage, TypeRamassage
	                    FROM tblvillesdateramassage
	                     WHERE Dateramassage = $dateToScroll AND idville = $idville
	                      AND TypeRamassage = $typederamassage AND typecam = $typecam";
        $calendrier = $db->query($strSQL);
        $num_row = $calendrier->getNumRows();
        if ($num_row == 0) {
            $strSQL = "INSERT INTO tblvillesdateramassage
	                  (idville, Dateramassage, TypeRamassage, typecam)
	                  VALUES ($idville, $dateToScroll, $typederamassage, $typecam)";
            $db->query($strSQL);
            $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules
                              WHERE datejour = $dateToScroll AND `TypeCedule` = $typecam";
            $grp = $db->query($strSQL)->getRow(0);
            $arow = (! isset($grp->MaxRow)) ? 1 : $grp->MaxRow;

            $strSQL = "INSERT INTO grp_cedules
	              (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet)
	              VALUES ($dateToScroll, $idville, 1, $arow, $typecam, $typederamassage)";
            $db->query($strSQL);
            $strSQL = 'SELECT LAST_INSERT_ID() as id';
            $result = $db->query($strSQL)->getRow(0);
            $idgrp = $result->id;
            $strSQL = "INSERT INTO cedules
	                      (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
	                    VALUES ($dateToScroll, $idville, $typecam, $idgrp,
                            (SELECT Nom FROM typededechet WHERE id = $typederamassage), 27)";
            $db->query($strSQL);
        }
        $returnval .= '<table> <tr>
														<td valign="top" align="right" >
														<label onclick="AddRamassage(' . $activeDate . ')" ondrop="dropCollecte(event, ' . intval($jour) . ')" ondragover="allowDrop(event)" class="form-control tdjour">' . $jour . '</label> </td> </tr>';
        $strSQL = "SELECT tblvillesdateramassage.Id, idville, Dateramassage, TypeRamassage,typecam,
(SELECT typededechet.couleur FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) AS couleur,
(SELECT typededechet.textcolor FROM typededechet WHERE typededechet.id = tblvillesdateramassage.TypeRamassage) AS textcolor
													FROM tblvillesdateramassage INNER JOIN ville
															ON tblvillesdateramassage.idville = ville.id
															WHERE idville = $idville AND MONTH(Dateramassage) = MONTH($dateToScroll)
																									AND YEAR(Dateramassage) = YEAR($dateToScroll)
																									and DAY(Dateramassage) = " . intval($jour) . " order by tblvillesdateramassage.Id";
        $calendrier = $db->query($strSQL);
        foreach ($calendrier->getResult() as $row) {
            $titre = '';
            $titre = (($row->typecam == 1) ? 'RE' : 'RO');
            $returnval .= '<tr id="tr_' . $dateToScroll . '_' . $row->Id . '">
								<td class="tdinfo"><div id="divCollecte_' . $annee . $mois . $jour . '_' . $row->Id . '" class="btn btn-default" draggable="true" ondragstart="drag(event)" onclick="showInfoRanmassage(' . $row->Id . ')"
										style="min-width:60px;width:40px;height:30px;font-size:medium;background-color:' . $row->couleur . ';color:' . $row->textcolor . ';font-weight:bold;text-align:center;">' . $titre . '</div></td>
							</tr>';
        }
        $returnval .= '</table>';
        return $this->response->setBody($returnval);
    }

    /**
     * CI3 : co_gestion/removeCalDeploie — suppression des ramassages
     * deployes (tblvillesdateramassage + cedules + grp_cedules). PORTE.
     */
    public function removeCalDeploie(...$args)
    {
        $db = \Config\Database::connect();
        $input = $this->request;
        $idville = $input->getPostGet('idville');
        $typecam = $db->escape($input->getPostGet('typecam'));
        $activeDate = $input->getPostGet('selectedDay');
        $startDate = $db->escape($input->getPostGet('dtestartdeploie'));
        $endDate = $db->escape($input->getPostGet('dteenddeploie'));
        $frequence = $input->getPostGet('frequence');
        $typederamassage = $db->escape($input->getPostGet('typederamassage'));
        $strSQL = "DELETE FROM tblvillesdateramassage WHERE idville = $idville AND typecam = $typecam
                    AND TypeRamassage = $typederamassage AND
                    Dateramassage BETWEEN $startDate AND $endDate";
        $db->query($strSQL);
        $strSQL = "DELETE FROM cedules WHERE idvilles = $idville AND `TypeCedule` = $typecam
                    AND TypeDeProduit =
						  (SELECT Nom FROM typededechet WHERE id = $typederamassage) AND
                    DateJour BETWEEN $startDate AND $endDate";
        $db->query($strSQL);
        $strSQL = "DELETE FROM grp_cedules WHERE idvilles = $idville AND `TypeCedule` = $typecam
                    AND TypeDechet = $typederamassage AND
                    datejour BETWEEN $startDate AND $endDate";
        $db->query($strSQL);
        return $this->infoVille();
    }

    /**
     * CI3 : co_gestion/FrequenceDeploie (interne) — genere les
     * ramassages selon la frequence choisie.
     */
    private function FrequenceDeploie($idville, $arrday, $startDate, $endDate, $typecam, $typederamassage, $frequence)
    {
        $db = \Config\Database::connect();
        switch ($frequence) {
            case '1':
            case '2':
                $freq = (($frequence == 1) ? "$frequence week" : "$frequence weeks");
                $interval = \DateInterval::createFromDateString($freq);
                foreach ($arrday as $jour) {
                    $datejour = clone $startDate;
                    $datejour->modify("first $jour of this month");
                    $daterange = new \DatePeriod($datejour, $interval, $endDate);
                    foreach ($daterange as $date1) {
                        $date = $db->escape($date1->format('Y-m-d'));
                        $strSQL = "INSERT INTO tblvillesdateramassage
	                    (idville, Dateramassage, TypeRamassage, typecam)
	                    VALUES ($idville, $date, $typederamassage, $typecam)";
                        $db->query($strSQL);
                        $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules
                              WHERE datejour = $date AND `TypeCedule` = $typecam";
                        $grp = $db->query($strSQL)->getRow(0);
                        $arow = (! isset($grp->MaxRow)) ? 1 : $grp->MaxRow;
                        $strSQL = "INSERT INTO grp_cedules
	              (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet)
	              VALUES ($date, $idville, 1, $arow, $typecam, $typederamassage)";
                        $db->query($strSQL);
                        $strSQL = 'SELECT LAST_INSERT_ID() as id';
                        $result = $db->query($strSQL)->getRow(0);
                        $idgrp = $result->id;
                        $strSQL = "INSERT INTO cedules
	                      (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
	                    VALUES ($date, $idville, $typecam, $idgrp,
                            (SELECT Nom FROM typededechet WHERE id = $typederamassage), 27)";
                        $db->query($strSQL);
                    }
                }
                break;
            case 3:
                $freq = "1 month";
                $interval = \DateInterval::createFromDateString($freq);
                $daterange = new \DatePeriod($startDate, $interval, $endDate);
                foreach ($daterange as $date1) {
                    foreach ($arrday as $jour) {
                        $date = clone $date1;
                        $date->modify("first $jour of this month");
                        $datejour = $db->escape($date->format('Y-m-d'));
                        $strSQL = "INSERT INTO tblvillesdateramassage
	                    (idville, Dateramassage, TypeRamassage, typecam)
	                    VALUES ($idville, $datejour, $typederamassage, $typecam)";
                        $db->query($strSQL);
                        $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules
                              WHERE datejour = $datejour AND `TypeCedule` = $typecam";
                        $grp = $db->query($strSQL)->getRow(0);
                        $arow = (! isset($grp->MaxRow)) ? 1 : $grp->MaxRow;
                        $strSQL = "INSERT INTO grp_cedules
	              (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet)
	              VALUES ($datejour, $idville, 1, $arow, $typecam, $typederamassage)";
                        $db->query($strSQL);
                        $strSQL = 'SELECT LAST_INSERT_ID() as id';
                        $result = $db->query($strSQL)->getRow(0);
                        $idgrp = $result->id;
                        $strSQL = "INSERT INTO cedules
	                      (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
	                    VALUES ($datejour, $idville, $typecam, $idgrp,
                            (SELECT Nom FROM typededechet WHERE id = $typederamassage), 27)";
                        $db->query($strSQL);
                    }
                }
                break;
            case 4:
                $freq = "1 month";
                $interval = \DateInterval::createFromDateString($freq);
                $daterange = new \DatePeriod($startDate, $interval, $endDate);
                foreach ($daterange as $date1) {
                    foreach ($arrday as $jour) {
                        $date = clone $date1;
                        $date->modify("second $jour of this month");
                        $datejour = $db->escape($date->format('Y-m-d'));
                        $strSQL = "INSERT INTO tblvillesdateramassage
	                    (idville, Dateramassage, TypeRamassage, typecam)
	                    VALUES ($idville, $datejour, $typederamassage, $typecam)";
                        $db->query($strSQL);
                        $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules
                              WHERE datejour = $datejour AND `TypeCedule` = $typecam";
                        $grp = $db->query($strSQL)->getRow(0);
                        $arow = (! isset($grp->MaxRow)) ? 1 : $grp->MaxRow;
                        $strSQL = "INSERT INTO grp_cedules
	              (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet)
	              VALUES ($datejour, $idville, 1, $arow, $typecam, $typederamassage)";
                        $db->query($strSQL);
                        $strSQL = 'SELECT LAST_INSERT_ID() as id';
                        $result = $db->query($strSQL)->getRow(0);
                        $idgrp = $result->id;
                        $strSQL = "INSERT INTO cedules
	                      (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
	                    VALUES ($datejour, $idville, $typecam, $idgrp,
                            (SELECT Nom FROM typededechet WHERE id = $typederamassage), 27)";
                        $db->query($strSQL);
                    }
                }
                break;
            case 5:
                $freq = "1 month";
                $interval = \DateInterval::createFromDateString($freq);
                $daterange = new \DatePeriod($startDate, $interval, $endDate);
                foreach ($daterange as $date1) {
                    foreach ($arrday as $jour) {
                        $date = clone $date1;
                        $date->modify("third $jour of this month");
                        $datejour = $db->escape($date->format('Y-m-d'));
                        $strSQL = "INSERT INTO tblvillesdateramassage
	                    (idville, Dateramassage, TypeRamassage, typecam)
	                    VALUES ($idville, $datejour, $typederamassage, $typecam)";
                        $db->query($strSQL);
                        $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules
                              WHERE datejour = $datejour AND `TypeCedule` = $typecam";
                        $grp = $db->query($strSQL)->getRow(0);
                        $arow = (! isset($grp->MaxRow)) ? 1 : $grp->MaxRow;
                        $strSQL = "INSERT INTO grp_cedules
	              (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet)
	              VALUES ($datejour, $idville, 1, $arow, $typecam, $typederamassage)";
                        $db->query($strSQL);
                        $strSQL = 'SELECT LAST_INSERT_ID() as id';
                        $result = $db->query($strSQL)->getRow(0);
                        $idgrp = $result->id;
                        $strSQL = "INSERT INTO cedules
	                      (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
	                    VALUES ($datejour, $idville, $typecam, $idgrp,
                            (SELECT Nom FROM typededechet WHERE id = $typederamassage), 27)";
                        $db->query($strSQL);
                    }
                }
                break;
        }
    }

    public function postDayCamion($idville)
    {    
            //--------------------------------------------------------------
            //$this->load->model("Cedules_model", "sanivan");
            //"/sanivan/postCalendrier/" + $("#optVilles").val() + "?nbRearEnd=" + $("#nbRearEnd").val() +
            //"&nbRobot=" + $("#nbRobot").val() + "&calVille=" + $('#calVille').multiDatesPicker('value') +
            //"&typederamassage=" + typederamassage + "&test=" + $("#altField").val();
            $nbRearEnd = $this->input->post_get('nbRearEnd');
            $nbRobot = $this->input->post_get('nbRobot');
            $calVille = $this->input->post_get('calVille');
            $typederamassage = $this->input->post_get('typederamassage');
            switch ($typederamassage) {
                case 1:
                    $calVille = 'dechets = '.$this->db->escape($calVille);
                    $nbRearEnd = 'rbCamiondechetsRE = '.$this->db->escape($nbRearEnd);
                    $nbRobot = 'rbCamiondechetsROBOT = '.$this->db->escape($nbRobot);
                    break;
                case 2:
                    $calVille = 'recyclage = '.$this->db->escape($calVille);
                    $nbRearEnd = 'rbCamionrecyclageRE = '.$this->db->escape($nbRearEnd);
                    $nbRobot = 'rbCamionrecyclageROBOT = '.$this->db->escape($nbRobot);
                    break;
                case 3:
                    $calVille = 'organique = '.$this->db->escape($calVille);
                    $nbRearEnd = 'rbCamionorganiqueRE = '.$this->db->escape($nbRearEnd);
                    $nbRobot = 'rbCamionorganiqueROBOT = '.$this->db->escape($nbRobot);
                    break;
                case 4:
                    $calVille = 'grosrebuts = '.$this->db->escape($calVille);
                    $nbRearEnd = 'rbCamiongrosrebutsRE = '.$this->db->escape($nbRearEnd);
                    $nbRobot = 'rbCamiongrosrebutsROBOT = '.$this->db->escape($nbRobot);
                    break;
            }
            /** @var string  $strSQL */
            $strSQL = "UPDATE ville
    	                    SET
    		                    $calVille,
    		                    $nbRearEnd,
    		                    $nbRobot
    	                    WHERE id = $idville";
            $this->db->query($strSQL);
            echo 'OK';
    }


    /**
     * CI3 : co_gestion/showValidSanivan — page de validation sanivan
     * (rapport de la veille). PORTE.
     */
    public function showValidSanivan(...$args)
    {
        $ci3 = new Ci3Session();
        helper('ci3view');
        $cedules = new CedulesModel();

        $date = date_create(date('Y-m-d'));
        $date = date_modify($date, "-1 days");
        $data['ActiveDate'] = $date->format('Y-m-d');
        $data['validsanivan'] = $cedules->getRapportJour($data['ActiveDate']);
        $data['html'] = view('sanivan/inc_rapport', $data);
        $data['nom'] = $ci3->get('nom');
        $data['email'] = $ci3->get('email');
        $data['employes'] = $cedules->GetAllEmployes('0', '0');
        $data['camions'] = $cedules->GetAllCamions();
        $data['typededechet'] = $cedules->GetAllTypeProduits();
        $data['typeabsences'] = $cedules->GetTypeAbsences();

        $data['navtop'] = view('partials/navtop', $data);
        $data['navleft'] = view('partials/navleft', $data);
        $ci3->set('LAST_PAGE', 6);
        return $this->response->setBody(view('sanivan/validation', $data));
    }

    /**
     * CI3 : co_gestion/getValidation — rapport du jour (HTML). PORTE.
     */
    public function getValidation($date = null)
    {
        $cedules = new CedulesModel();
        return $this->response->setBody($cedules->getRapportJour($date));
    }

    /**
     * CI3 : co_gestion/getValidationChauffeur — validation chauffeur
     * du jour. PORTE.
     */
    public function getValidationChauffeur($date = null)
    {
        $ci3 = new Ci3Session();
        $db = \Config\Database::connect();
        $cedules = new CedulesModel();
        return $this->response->setBody(
            $cedules->getValidChauffeurJour($date, $db->escape($ci3->get('user_id')))
        );
    }

    /**
     * CI3 : co_gestion/PostSuivis — MAJ heures d'un suivis puis
     * rapport du jour. PORTE.
     */
    public function PostSuivis($StartTime = null, $EndTime = null, $dinerdeb = null, $id = null, $dateactive = null)
    {
        $db = \Config\Database::connect();
        $StartTime = $db->escape($StartTime);
        $EndTime = $db->escape($EndTime);
        $dinerdeb = $db->escape($dinerdeb);
        $id = $db->escape($id);
        $cedules = new CedulesModel();
        $cedules->PostSuivis($StartTime, $EndTime, $dinerdeb, $id);
        return $this->response->setBody($cedules->getRapportJour($dateactive));
    }

    /**
     * CI3 : co_gestion/DeleteSuivis — suppression d'un suivis. PORTE.
     */
    public function DeleteSuivis($id = null)
    {
        $db = \Config\Database::connect();
        $id = $db->escape($id);
        $db->query("DELETE FROM suivis WHERE id = $id");
        return $this->response->setBody('ok');
    }

    public function PostCam($KiloDeb, $KiloFin, $Fuel,
        $Ticket1, $Tonnage1, $debut1, $fin1,
        $Ticket2, $Tonnage2, $debut2, $fin2,
        $Ticket3, $Tonnage3, $debut3, $fin3,
        $Ticket4, $Tonnage4, $debut4, $fin4, $id, $idcam, $dateactive, $from)
    {    
            //--------------------------------------------------------------
            $Tonnage1 = str_replace('_', '.', $Tonnage1);
            $Tonnage2 = str_replace('_', '.', $Tonnage2);
            $Tonnage3 = str_replace('_', '.', $Tonnage3);
            $Tonnage4 = str_replace('_', '.', $Tonnage4);
            $KiloDeb = str_replace('_', '.', $KiloDeb);
            $KiloFin = str_replace('_', '.', $KiloFin);
            $Fuel = str_replace('_', '.', $Fuel);
            $KiloDeb = (($KiloDeb == '') ? '0' : $this->db->escape($KiloDeb));
            $KiloFin = (($KiloFin == '') ? '0' : $this->db->escape($KiloFin));
            $Fuel = (($Fuel == '') ? '0' : $this->db->escape($Fuel));
            $Ticket1 = $this->db->escape((($Ticket1 == '%20') ? '' : $Ticket1));
            $Ticket2 = $this->db->escape((($Ticket2 == '%20') ? '' : $Ticket2));
            $Ticket3 = $this->db->escape((($Ticket3 == '%20') ? '' : $Ticket3));
            $Ticket4 = $this->db->escape((($Ticket4 == '%20') ? '' : $Ticket4));
            $Tonnage1 = (($Tonnage1 == '') ? '0' : $this->db->escape($Tonnage1));
            $Tonnage2 = (($Tonnage2 == '') ? '0' : $this->db->escape($Tonnage2));
            $Tonnage3 = (($Tonnage3 == '') ? '0' : $this->db->escape($Tonnage3));
            $Tonnage4 = (($Tonnage4 == '') ? '0' : $this->db->escape($Tonnage4));
            $depotoire1 = $this->db->escape($this->input->get('depotoire1'));
            $depotoire2 = $this->db->escape($this->input->get('depotoire2'));
            $depotoire3 = $this->db->escape($this->input->get('depotoire3'));
            $depotoire4 = $this->db->escape($this->input->get('depotoire4'));
            $debut1 = $this->db->escape((($debut1 == '%20') ? '' : $debut1));
            $debut2 = $this->db->escape((($debut2 == '%20') ? '' : $debut2));
            $debut3 = $this->db->escape((($debut3 == '%20') ? '' : $debut3));
            $debut4 = $this->db->escape((($debut4 == '%20') ? '' : $debut4));
            $fin1 = $this->db->escape((($fin1 == '%20') ? '' : $fin1));
            $fin2 = $this->db->escape((($fin2 == '%20') ? '' : $fin2));
            $fin3 = $this->db->escape((($fin3 == '%20') ? '' : $fin3));
            $fin4 = $this->db->escape((($fin4 == '%20') ? '' : $fin4));
            $id = $this->db->escape($id);
            $idcam = $this->db->escape($idcam);
            $this->load->model("Cedules_model", "cedules");
    
            $this->cedules->PostCam($KiloDeb, $KiloFin, $Fuel,
                $Ticket1, $Tonnage1, $depotoire1, $debut1, $fin1,
                $Ticket2, $Tonnage2, $depotoire2, $debut2, $fin2,
                $Ticket3, $Tonnage3, $depotoire3, $debut3, $fin3,
                $Ticket4, $Tonnage4, $depotoire4, $debut4, $fin4, $idcam);
            if ($from == 1) {
    
                echo $this->cedules->getRapportJour($dateactive);
            } else {
                echo $this->cedules->getValidChauffeurJour($dateactive, $this->db->escape($this->session->userdata('user_id')));
            }
            //$data['showdepot'] = 1;
            //if (count($arrTest) == 2)
            //{
            //  if(strlen($arrTest[0])== 2 and strlen($arrTest[1])== 2)
            //  {
            //    $arrTest = explode(':', fin3 );
            //    if(strlen($arrTest[0])== 2 and strlen($arrTest[1])== 2)
            //    {
            //      $data['showdepot'] = 4;
            //    }
            //    else
            //    {
            //      $data['showdepot'] = 3;
            //    }
            //  }
            //}
            //echo json_encode($data);
    }


    public function DeleteCamion($id)
    {    
            //--------------------------------------------------------------
            $id = $this->db->escape($id);
            $this->db->query("DELETE FROM camionjour WHERE id=$id");
            // $this->remote_db->query("DELETE FROM camionjour WHERE id=$id"); // Désactivé - connexion distante bloquée sur WHC
            echo 'ok';
    }


    public function UpdEvent($id, $dateactive)
    {    
            //--------------------------------------------------------------
            $id = $this->db->escape($id);
            //$ActiveDate = $dateactive;
            $strSQL = "select * from cedules where ID = $id";
            $cedule = $this->db->query($strSQL)->getRow(0);
            $typeprod = $this->db->escape($cedule->TypeDeProduit);
            $camion = $this->db->escape($cedule->NOCAMION);
            $chauffeur = $this->db->escape($cedule->idchauffeurs);
            $aide = $this->db->escape($cedule->HELPERS_1);
            $isstart = $this->db->escape($cedule->isstart);
            $idgroupe = $this->db->escape($cedule->idgrp);
            $dateactive = $this->db->escape($dateactive);
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            $this->cedules->UpdEvent($typeprod, $camion, $chauffeur, $aide, $id, $idgroupe, $isstart, $dateactive);
    }


    public function DeleteEvent($id)
    {    
            //--------------------------------------------------------------
            $id = $this->db->escape($id);
            $this->db->query("DELETE FROM cedules WHERE ID = $id");
            echo 'ok';
    }


    public function getSuivisCamion($camion)
    {    
            /** @var string $returnval */
            $returnval = '';
            $camion = $this->db->escape($camion);
            $typehs = $this->input->get('typehs');
            /** @var string $strSQL */
            switch ($typehs) {
                case 1:
                case 3:
                    $strSQL = "Select id, nocamion, left(datestart, 10) AS datestart, note, left(dateretourprevu, 10) AS dateretourprevu
                                            from suiviscamion where nocamion = $camion and idstatut in (1, 3) AND dateend IS null  ";
                    break;
                case 2:
                    $strSQL = "Select id, nocamion, left(datestart, 10) AS datestart, note, left(dateretourprevu, 10) AS dateretourprevu, TypeDechet, QteDechet
                                            from suiviscamion where nocamion = $camion and idstatut = 2 AND dateend IS null  ";
                    break;
            }
            $suivis = $this->db->query($strSQL);
            foreach ($suivis->getResultArray() as $row) {
                $returnval = json_encode($row);
            }
            echo $returnval;
    }


    public function updStart($id, $field, $dateactive)
    {    
            $from = $this->input->get('from');
            $fieldName = (($field == 1) ? 'chaufconfirme' : 'helperconfirme');
            switch ($field) {
                case 1:
                    $fieldName = 'chaufconfirme';
                    break;
                case 2:
                    $fieldName = 'helperconfirme';
                    break;
                case 3:
                    $fieldName = 'IsStart';
                    break;
            }
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET
    		                  $fieldName = if($fieldName = 1, 0, 1)
    	                  WHERE ID = ".$id;
            $this->db->query($strSQL);
            if ($field == 3) {
                $strSQL = "SELECT suiviscamion.id
                           FROM statutcamion statutcamion
                                INNER JOIN suiviscamion suiviscamion
                                   ON (statutcamion.id = suiviscamion.idstatut)
                                   WHERE  suiviscamion.dateend is null
                                   and suiviscamion.datestart <= CURDATE()
                                   AND statutcamion.id = 2 and
                                (select cedules.NOCAMION from cedules where cedules.ID = $id) = suiviscamion.nocamion
                                   order by  (select TypeCamion from camions cam
                                where cam.nocamion = suiviscamion.nocamion ),
                                   suiviscamion.nocamion";
                $suiviscamion = $this->db->query($strSQL);
                foreach ($suiviscamion->getResult() as $row) {
                    $this->UpdViderMat($row->id, $dateactive, 1);
                }
    
            }
            $this->showToday(4, $dateactive);
    }


    public function updEmloye($id, $field, $value, $dateactive)
    {    
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET
    		                  ".(($field == 1) ? 'idchauffeurs' : 'HELPERS_1')."= $value
    	                  WHERE ID = ".$id;
            $this->db->query($strSQL);
            echo 'OK'; //$this->showToday(4, $dateactive);
    }


    public function updCamion(...$args)
    {
        return $this->toCi3();
    }

    public function updTypeProd($id, $value, $dateactive)
    {    
            $id = $this->db->escape($id);
            $value = $this->db->escape($value);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET TypeDeProduit = $value
    	                  WHERE ID = ".$id;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function AddCamionSuivis($id)
    {    
            $nocamion = $this->db->escape($this->input->get('nocamion'));
            //
            $datestart = $this->db->escape($this->input->get('datestart'));
            $datestart = (($this->input->get('datestart') == '') ? $this->db->escape($this->input->get('dtestarths')) : $datestart);
            $TypeDechet = $this->db->escape($this->input->get('typemat'));
            $notehs = $this->input->get('notehs');
            $note = $this->db->escape(str_replace('%20', ' ', $notehs));
            $idstatut = $this->db->escape($this->input->get('idstatut'));
            /** @var string  $strSQL */
            switch ($id) {
                case 0:
                    $strSQL = "INSERT INTO suiviscamion
    	                      (nocamion, datestart, TypeDechet, note, idstatut)
    	                      VALUES ($nocamion, $datestart, $TypeDechet, $note,$idstatut )";
                    $this->db->query($strSQL);
                    if (($this->input->get('idstatut') == 1) or ($this->input->get('idstatut') == 3)) {
                        $strSQL = "UPDATE cedules
    	                  SET
    		                  NOCAMION=''
    	                  WHERE DateJour >= $datestart AND NOCAMION = $nocamion";
                        $this->db->query($strSQL);
                    }
                    break;
                default:
                    $strSQL = "UPDATE suiviscamion
    	                    SET
    		                    datestart=$datestart,
    		                    note=$note,
    		                    TypeDechet=$TypeDechet
    	                    WHERE id = $id";
                    $this->db->query($strSQL);
                    break;
            }
            $activedrop = $this->input->post_get('activedrop');
            $activedrop = ((isset($activedrop)) ? $this->input->post_get('activedrop') : 0);
            $limit = $this->input->post_get('limit');
            $limit = ((isset($activedrop)) ? $this->input->post_get('limit') : 0);
            $msg = $this->session->userdata('LAST_PAGE').'?limit='.$limit.'&activedrop='.$activedrop;
            echo $msg;
    }


    public function AddRhSuivis($idrh, $idabsence, $datestart)
    {    
            $note = $this->input->get_post('noterh');
            $note = $this->db->escape(str_replace('%20', ' ', $note));
            $avistoshow = $this->input->get_post('avistoshow');
            $avistoshow = $this->db->escape(str_replace('%20', ' ', $avistoshow));
            $idrh = $this->db->escape($idrh);
            $datestart = $this->db->escape($datestart);
            $dateend = $this->db->escape($datestart);
            $dateretourprevu = (($this->input->get('dteretour') == '') ? 'NULL' : $this->db->escape($this->input->get('dteretour')));
            $idabsence = $this->db->escape($idabsence);
            $idsuivis = $this->db->escape($this->input->get_post('idsuivis'));
            /** @var string  $showavis */
            if (is_numeric($this->input->get_post('showavis'))) {
                $showavis = $this->input->get_post('showavis');
            } else {
                $showavis = '0';
            }
            /** @var string  $strSQL */
            switch ($this->input->get('idsuivis')) {
                case 0:
                    $strSQL = "INSERT INTO suivisrh
    	                      (idrh, idabsence, datestart, note, dateretourprevu, dateend, showavis)
    	                      VALUES ($idrh, $idabsence, $datestart, $note, $dateretourprevu, $dateretourprevu, $showavis)";
                    break;
                default:
                    $strSQL = "UPDATE suivisrh
    	                  SET
    		                  idrh=$idrh,
    		                  idabsence=$idabsence,
    		                  datestart=$datestart,
    		                  note= $note,
    		                  dateretourprevu=$dateretourprevu,
                          dateend=$dateretourprevu,
    		                  showavis=$showavis,
    		                  avistoshow=$avistoshow
    	                  WHERE id = $idsuivis";
                    break;
            }
    
            $this->db->query($strSQL);
            $dateretourprevu = (($this->input->get('dteretour') == '') ? '' : 'and DateJour <= '.$this->db->escape($this->input->get('dteretour')));
            $strSQL = "Update cedules set idchauffeurs = 0 where  idchauffeurs = $idrh AND DateJour >= $datestart $dateretourprevu ";
            $this->db->query($strSQL);
            //-----------------------------------------------------------------------
            $strSQL = "Update cedules set HELPERS_1 = 0 where  HELPERS_1 = $idrh AND DateJour >= $datestart $dateretourprevu ";
            $this->db->query($strSQL);
            //-----------------------------------------------------------------------
    
            if ($this->input->post_get('from') == 1) {
                $this->getCalendrier();
            } else {
                $activedrop = $this->input->post_get('activedrop');
                $activedrop = ((isset($activedrop)) ? $this->input->post_get('activedrop') : 0);
                $limit = $this->input->post_get('limit');
                $limit = ((isset($activedrop)) ? $this->input->post_get('limit') : 0);
                echo $this->session->userdata('LAST_PAGE').'?limit='.$limit.'&activedrop='.$activedrop;
            }
    }


    public function UpdViderMat($id, $dateretour, $fromstart = 0)
    {    
            $dateretouresc = $this->db->escape($dateretour);
            /** @var string  $strSQL */
            $strSQL = "UPDATE suiviscamion SET dateend = $dateretouresc
    	                            WHERE id = ".$id;
            $this->db->query($strSQL);
            $activedrop = $this->input->post_get('activedrop');
            $activedrop = ((isset($activedrop)) ? $this->input->post_get('activedrop') : 0);
            $limit = $this->input->post_get('limit');
            $limit = ((isset($activedrop)) ? $this->input->post_get('limit') : 0);
            if ($fromstart == 0)
                $this->session->userdata('LAST_PAGE').'?limit='.$limit.'&activedrop='.$activedrop;
    }


    public function UpdSuivisRHRetour($id, $dateretour)
    {    
            $dateretour = $this->db->escape($dateretour);
            /** @var string  $strSQL */
            $strSQL = "UPDATE suivisrh SET dateend = $dateretour
    	                            WHERE id = ".$id;
            $this->db->query($strSQL);
    }


    public function setProduit($id, $typeprod)
    {    
            /** @var string $color */
            switch ($typeprod) {
                case 'ORGANIQUE':
                    $color = '#ebccaa';
                    break;
                case 'RECYCLAGE':
                    $color = '#9FF3F9';
                    break;
                case 'DECHETS':
                    $color = '#E5E5E5';
                    break;
                case 'RESIDUS_VERT':
                    $color = '#2DBC96';
                    break;
                case 'GROS_REBUTS':
                    $color = 'white';
                    break;
                case 'BRANCHES':
                    $color = '#D1B546';
                    break;
                case 'FEUILLES':
                    $color = '#2DBC96';
                    break;
                default:
                    $color = '#F7F1C5';
            }
            $typeprodesc = $this->db->escape($typeprod);
            $id = $this->db->escape($id);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET TypeDeProduit = $typeprodesc
    	                  WHERE ID = ".$id;
            $this->db->query($strSQL);
            $arrReturn = array('color' => $color, 'produit' => $typeprod);
            echo json_encode($arrReturn);
    }


    public function setAide($id, $idaide, $dateactive, $oldid)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $idaideesc = $this->db->escape($idaide);
            $idesc = $this->db->escape($id);
            $oldid = $this->db->escape($oldid);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET HELPERS_1 = 0,
                        helperconfirme = 0
    	                  WHERE ID = $oldid";
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET HELPERS_1 = $idaideesc,
                            helperconfirme =0
    	                  WHERE ID = ".$idesc;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function addLigneAide($id, $idaide, $dateactive, $oldid)
    {    
            $typecedule = $this->input->post_get('typecedule');
            $typecedule = ((isset($typecedule)) ? $this->db->escape($this->input->post_get('typecedule')) : 1);
            $idaide = $this->db->escape($idaide);
            $dteactive = $this->db->escape($dateactive);
            $idesc = $this->db->escape($id);
            $strSQL = "SELECT idvilles FROM grp_cedules WHERE id = $idesc";
            $result = $this->db->query($strSQL)->getRow(0);
            $idville = $this->db->escape($result->idvilles);
            $oldid = $this->db->escape($oldid);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO cedules
    	              (CodeUtil, DateJour, TypeCedule, idgrp, idvilles,TypeDeProduit, NOCAMION, idchauffeurs, HELPERS_1)
    	              VALUES ('web', $dteactive,  $typecedule, $idesc, $idville, 'DECHETS', 'Cam',0,$idaide )";
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET HELPERS_1 = 0
    	                  WHERE ID = ".$oldid;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function setCamion($id, $idfrom, $cam, $dateactive)
    {    
            $camesc = $this->db->escape($cam);
            $idesc = $this->db->escape($id);
            $idfrom = $this->db->escape($idfrom);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET NOCAMION = $camesc,
                            chaufconfirme = 0,
                            IsStart = 0
    	                  WHERE ID = ".$idesc;
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET NOCAMION = 'Cam',
                            IsStart = 0,
                            chaufconfirme = 0
    	                  WHERE ID = ".$idfrom;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function addLigneCamion($id, $idfrom, $cam, $dateactive)
    {    
            $typecedule = $this->input->post_get('typecedule');
            $typecedule = ((isset($typecedule)) ? $this->db->escape($this->input->post_get('typecedule')) : 1);
    
            $camesc = $this->db->escape($cam);
            $dteactive = $this->db->escape($dateactive);
            $idesc = $this->db->escape($id);
            $strSQL = "SELECT idvilles FROM grp_cedules WHERE id = $idesc";
            $result = $this->db->query($strSQL)->getRow(0);
            $idville = $this->db->escape($result->idvilles);
            $idfrom = $this->db->escape($idfrom);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO cedules
    	              (CodeUtil, DateJour, TypeCedule, idgrp, idvilles,TypeDeProduit, NOCAMION, idchauffeurs, HELPERS_1)
    	              VALUES ('web', $dteactive,  $typecedule, $idesc, $idville, 'DECHETS', $camesc,0,0 )";
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET NOCAMION = '',
                        IsStart = 0
    	                  WHERE ID = ".$idfrom;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function setSecteur($id, $idfrom, $dateactive)
    {    
            $secteur = $this->db->escape($this->input->post_get('secteur'));
            $SQL = "SELECT id FROM secteurs WHERE Nom = $secteur";
            $result = $this->db->query($SQL)->getRow(0);
            $idsecteur = $this->db->escape($result->id);
            $idesc = $this->db->escape($id);
            $idfrom = $this->db->escape($idfrom);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET Secteur = $secteur,
                        idsecteur = $idsecteur
    	                  WHERE ID = ".$idesc;
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET Secteur = '',
                        idsecteur = 0,
                        IsStart = 0
    	                  WHERE ID = ".$idfrom;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function setChauffeur($id, $idchauffeur, $dateactive, $oldid)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $idchauffeuresc = $this->db->escape($idchauffeur);
            $idesc = $this->db->escape($id);
            $oldid = $this->db->escape($oldid);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET idchauffeurs = 0,
                        chaufconfirme = 0,
                        IsStart = 0
    	                  WHERE ID = $oldid";
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET idchauffeurs = $idchauffeuresc,
                        chaufconfirme = 0,
                        IsStart = 0
    	                  WHERE ID = ".$idesc;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function delEmploye($id, $idchauffeur, $dateactive, $fieldname)
    {    
            if (($fieldname == 'idchauffeurs') or ($fieldname == 'HELPERS_1')) {
                $this->load->model("Creationcd_model", "creationcd");
                $idchauffeuresc = $this->db->escape($idchauffeur);
                $idesc = $this->db->escape($id);
                /** @var string  $strSQL */
                $strSQL = "UPDATE cedules
    	                    SET $fieldname = 27,
                          chaufconfirme = 0,
                          IsStart = 0
    	                    WHERE ID = ".$idesc;
                $this->db->query($strSQL);
                $this->showToday(4, $dateactive);
            }
    }


    public function delCam($id, $dateactive)
    {    
            $idesc = $this->db->escape($id);
            /** @var string  $strSQL */
            $strSQL = "UPDATE cedules
    	                  SET NOCAMION = 'Cam',
                            IsStart = 0,
                            chaufconfirme = 0
    	                  WHERE ID = ".$idesc;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function addLigneChauffeur($id, $idchauffeur, $dateactive, $oldid)
    {    
            $typecedule = $this->input->post_get('typecedule');
            $typecedule = ((isset($typecedule)) ? $this->db->escape($this->input->post_get('typecedule')) : 1);
            $idchauffeuresc = $this->db->escape($idchauffeur);
            $dteactive = $this->db->escape($dateactive);
            $idesc = $this->db->escape($id);
            $strSQL = "SELECT idvilles FROM grp_cedules WHERE id = $idesc";
            $result = $this->db->query($strSQL)->getRow(0);
            $idville = $this->db->escape($result->idvilles);
            $oldid = $this->db->escape($oldid);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO cedules
    	              (CodeUtil, DateJour, TypeCedule, idgrp, idvilles,TypeDeProduit, NOCAMION, idchauffeurs, HELPERS_1)
    	              VALUES ('web', $dteactive,  $typecedule, $idesc, $idville, 'DECHETS', 'Cam',$idchauffeuresc,0 )";
            $this->db->query($strSQL);
            $strSQL = "UPDATE cedules
    	                  SET idchauffeurs = 0
    	                  WHERE ID = ".$oldid;
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function AddRow($id, $idgroupe, $from, $typecedule, $ActiveDrop, $fieldvalue, $dateactive)
    {    
            /** @var string  $fieldname */
            /** @var string  $fields */
            /** @var string  $fieldsval */
            switch ($from) {
                case 1:
                    $fieldname = 'NOCAMION';
                    $fields = 'idchauffeurs, HELPERS_1';
                    $fieldsval = '0, 0';
                    break;
                case 2:
                    $fieldname = 'idchauffeurs';
                    $fields = 'NOCAMION, HELPERS_1';
                    $fieldsval = "'', 0";
                    break;
                case 3:
                case 4:
                    $fieldname = 'HELPERS_1';
                    $fields = 'NOCAMION, idchauffeurs';
                    $fieldsval = "'', 0";
                    break;
            }
            $this->load->model("Creationcd_model", "creationcd");
            $fieldvalue = $this->db->escape($fieldvalue);
            $dateactiveesc = $this->db->escape($dateactive);
            $idesc = $this->db->escape($id);
            $idgroupe = $this->db->escape($idgroupe);
            $typeceduleesc = $this->db->escape($typecedule);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO cedules
    	              (CodeUtil, DateJour, $fieldname, $fields, TypeCedule, idgrp,idvilles)
    	              VALUES ('web', $dateactiveesc, $fieldvalue, $fieldsval, $typeceduleesc, $idgroupe, $idesc)";
            $this->db->query($strSQL);
            $this->showToday(4, $dateactive);
    }


    public function AddFirstRow($id, $idgroupe, $from, $typecedule, $ActiveDrop, $fieldvalue, $dateactive)
    {    
            /** @var string  $fieldname */
            switch ($from) {
                case 1:
                    $fieldname = 'NOCAMION';
                    break;
                case 2:
                    $fieldname = 'idchauffeurs';
                    break;
                case 3:
                case 4:
                    $fieldname = 'HELPERS_1';
                    break;
            }
            $this->load->model("Creationcd_model", "creationcd");
    
            $fieldvalue = $this->db->escape($fieldvalue);
            $dateactiveesc = $this->db->escape($dateactive);
            $idesc = $this->db->escape($id);
            $idgroupe = $this->db->escape($idgroupe);
            $typecedule = $this->db->escape($typecedule);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO cedules
    	              (CodeUtil, DateJour, $fieldname, TypeCedule, idgrp,idvilles)
    	              VALUES ('web', $dateactiveesc, $fieldvalue, $typecedule, $idgroupe, $idesc)";
            $this->db->query($strSQL);
            $strSQL = 'SELECT LAST_INSERT_ID() as id';
            $date = $this->db->escape($dateactive);
            $data = $this->creationcd->LoadCreateToday($dateactive, $typecedule);
            $data['ActiveDate'] = $dateactive;
            $data['ActiveDrop'] = $ActiveDrop;
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            echo view('sanivan/createcedule', $data);
    }


    public function AddVille($idville, $ACol, $typecedule, $dateactive, $from = 0)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $this->creationcd->AddVilleToCedule($dateactive, $idville, $typecedule, $ACol);
            if ($from > 0) {
                $this->showToday(99, $dateactive);
            } else {
                $this->showToday(4, $dateactive);
            }
    }


    public function UpdVille($idville, $ACol, $OldCol, $typecedule, $dateactive)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $this->creationcd->UpdVilleToCedule($dateactive, $idville, $typecedule, $ACol, $OldCol);
            $this->showToday(4, $dateactive);
    }


    public function updRow($id, $oldrang, $newrang, $oldcol, $newcol, $from, $dateactive)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $this->creationcd->updRow($id, $oldrang, $newrang, $oldcol, $newcol);
            if ($oldcol == $newcol) {
                echo $this->creationcd->LoadColCedules($this->db->escape($dateactive), $from, $newcol);
            } else {
                echo $this->creationcd->LoadColCedules($this->db->escape($dateactive), $from, $newcol);
            }
    }


    public function updRang($id, $oldrang, $newrang, $oldcol, $dateactive, $typecedule)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $this->load->model("Cedules_model", "cedules");
            $idsup = $this->input->post_get('idsup');
            if (($idsup !== null) and ($idsup !== '') and $this->db->fieldExists('idsuperviseur', 'grp_cedules')) {
                // Rang local a la bande superviseur (page aujourd'hui).
                $this->cedules->ReorderVilleRangSup($id, $oldrang, $newrang, $dateactive);
            } else {
                $this->creationcd->updTableRang($id, $oldrang, $newrang, $this->db->escape($dateactive), $oldcol, $typecedule);
            }
            //$data['createtoday'] = $data = $this->creationcd->LoadCreateToday($dateactive, $typecedule);
            //$data['today'] = $this->cedules->LoadToday($this->db->escape( $dateactive ), 5);
            $this->showToday(4, $dateactive);
            //echo json_encode($data);
    }


    public function setVilleSuperviseur($idgrp, $idsup, $dateactive)
    {    
            $this->load->model("Cedules_model", "cedules");
            $this->cedules->SetVilleSuperviseur($idgrp, $idsup, $dateactive);
            $this->showToday(4, $dateactive);
    }


    public function moveVilleGroupe($idgrp, $idgrptarget, $dateactive)
    {    
            $this->load->model("Cedules_model", "cedules");
            $this->cedules->MoveVilleToGroupPosition($idgrp, $idgrptarget, $dateactive);
            $this->showToday(4, $dateactive);
    }


    public function setSupVisible($supidx, $visible, $dateactive)
    {    
            // Reglage global partage par tous les usagers (optionssys).
            $this->load->model("Cedules_model", "cedules");
            $this->cedules->SetSupVisibilite($supidx, $visible, $dateactive);
            $this->showToday(4, $dateactive);
    }


    /**
     * CI3 : co_gestion/getnewDate — jour precedent/suivant. PORTE.
     */
    public function getnewDate($nbjour = null, $dateactive = null)
    {
        $date = date_create($dateactive);
        switch ($nbjour) {
            case -1:
                date_sub($date, date_interval_create_from_date_string("1 day"));
                $ActiveDate = $date->format('Y-m-d');
                break;
            case 1:
                date_add($date, date_interval_create_from_date_string("1 day"));
                $ActiveDate = $date->format('Y-m-d');
                break;
            default:
                $ActiveDate = $dateactive;
        }
        return $this->response->setBody($ActiveDate);
    }

    public function chkLoad($ACol, $typecedule, $dateactive)
    {    
            $this->load->model("Creationcd_model", "creationcd");
            $date = $this->db->escape($dateactive);
            /** @var string  $strSQL */
            $strSQL = "SELECT * FROM grp_cedules WHERE datejour = $date AND ACol = $ACol AND TypeCedule = $typecedule ";
            $query = $this->db->query($strSQL);
            $row = $query->getRow();
            if (isset($row)) {
                echo '0';
            } else {
                echo '1';
            }
    }


    public function DeleteRow($id, $typecedule, $dateactive)
    {    
            //--------------------------------------------------------------
            $this->load->model("Creationcd_model", "creationcd");
            $idesc = $this->db->escape($id);
            $this->db->query("DELETE FROM cedules WHERE ID = $idesc");
            $this->showToday(4, $dateactive);
    }


    public function DeleteVille($idgrp, $typecedule, $ACol, $dateactive)
    {    
            //--------------------------------------------------------------
            $idgrp = $this->db->escape($idgrp);
            $dteactive = $this->db->escape($dateactive);
            $this->db->query("DELETE FROM cedules WHERE idgrp = $idgrp");
            $this->db->query("DELETE FROM grp_cedules WHERE ID = $idgrp");
            $this->load->model("Creationcd_model", "creationcd");
            $this->load->model("Cedules_model", "cedules");
            $iterrow = 1;
            $strSQL = "SELECT * FROM  grp_cedules  WHERE  datejour = $dteactive AND
                                    typecedule = $typecedule and ACol = $ACol  order by ARow";
            $result = $this->db->query($strSQL);
            foreach ($result->getResult() as $row) {
                $strSQL = "UPDATE grp_cedules
    	                SET
                        ARow = $iterrow
    	                WHERE  id = ".$row->id;
                $this->db->query($strSQL);
                $iterrow++;
            }
            $this->showToday(4, $dateactive);
    }


    /**
     * CI3 : co_gestion/copieCedule — page de copie de cedules.
     * PORTE (la vue sanivan/copy_cedules est absente aussi en CI3 :
     * meme comportement d'erreur).
     */
    public function copieCedule(...$args)
    {
        $ci3 = new Ci3Session();
        helper('ci3view');
        $data['nom'] = $ci3->get('nom');
        $data['email'] = $ci3->get('email');
        $ci3->set('LAST_PAGE', 3);
        return $this->response->setBody(view('sanivan/copy_cedules', $data));
    }

    public function excCedule(...$args)
    {
        return $this->toCi3();
    }

    /**
     * CI3 : co_gestion/getWeekCedule — fragment HTML des cedules de
     * la semaine. PORTE.
     */
    public function getWeekCedule($radval = null, $dateactive = null)
    {
        $cedules = new CedulesModel();
        return $this->response->setBody($cedules->LoadWeekCedules($radval, $dateactive));
    }

    /**
     * CI3 : co_gestion/postCopyCedule — copie de cedules d'une semaine
     * a l'autre. PORTE.
     */
    public function postCopyCedule(...$args)
    {
        $input = $this->request;
        $testCheck = $input->getPostGet('r1');
        if ($testCheck == 1) {
            $chkRearEnd = 1;
            $chkRobot = 0;
        } else {
            $chkRearEnd = 0;
            $chkRobot = 1;
        }
        $TypeCopy = $input->getPostGet('r2');
        $dteFrom = $input->getPostGet('dteFrom');
        $dteTo = $input->getPostGet('dteTo');
        $cedules = new CedulesModel();
        $result = $cedules->CopyCeduleTo($chkRearEnd, $chkRobot, $TypeCopy, $dteFrom, $dteTo);
        $date = date_create($input->getPostGet('dteTo'));
        date_add($date, date_interval_create_from_date_string("1 days"));
        $newDate = date_format($date, "Y-m-d");
        if ($result) {
            return $this->response->setBody('Votre copie est terminée avec succès pour la semaine du ' . $dteTo);
        }
        return $this->response->setBody('Une erreur est survenue. Vous devez supprimer les données qui ont été transféré: semaine du ' . $dteTo);
    }

    /**
     * CI3 : co_gestion/removeCedule — suppression de cedules sur une
     * plage de dates (rearend/robot). PORTE.
     */
    public function removeCedule($rearend = null, $robot = null, $datedeb = null, $datefin = null)
    {
        $db = \Config\Database::connect();
        $typecedule = '';
        $datedeb = $db->escape($datedeb);
        $datefin = $db->escape($datefin);
        if ($rearend == 1) {
            if ($robot == 1) {
                $typecedule = ' IN ( 1, 2) ';
            } else {
                $typecedule = ' = 1 ';
            }
        } else {
            $typecedule = ' = 2 ';
        }
        $strSQL = "DELETE FROM cedules WHERE TypeCedule $typecedule and  DateJour BETWEEN $datedeb AND $datefin";
        $db->query($strSQL);
        $strSQL = "DELETE FROM grp_cedules WHERE TypeCedule $typecedule and datejour BETWEEN $datedeb AND $datefin";
        $db->query($strSQL);
        return $this->response->setBody('');
    }

    public function camionInfo()
    {    
            $nocamion = $this->db->escape($this->input->post_get('camion'));
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->GetCamionInfo($nocamion);
    }


    public function setIsDeletedCamion($nocamion)
    {    
            //--------------------------------------------------------------
            $nocamion = $this->db->escape($nocamion);
            $this->db->query("UPDATE camions
    	                          SET
    		                          isdeleted=0
    	                          WHERE NoCamion =$nocamion");
            /* $this->remote_db->query("UPDATE camions
    	                          SET
    		                          isdeleted=0
    	                          WHERE NoCamion =$nocamion"); */ // Désactivé - connexion distante bloquée sur WHC
            echo 'ok';
    }


    public function sendCamionInfo($typecamion, $isdel, $auto, $manuel, $treuil, $verseur)
    {    
            $this->load->model("Cedules_model", "cedules");
            $nocamion = $this->input->get('camion');
            $nocamion = $this->db->escape($nocamion);
            $strSQL = "SELECT NoCamion FROM camions WHERE  NoCamion = $nocamion";
            $query = $this->db->query($strSQL);
            $row = $query->getRow();
            if ($row->NoCamion == '') {
                $typecamion = $this->db->escape($typecamion);
                /** @var string  $strSQL */
                $strSQL = "INSERT INTO camions
    	                      (NoCamion, TypeCamion,IsAutomatique,IsManuel,IsVerseur,HaveWinch)
    	                      VALUES ($nocamion, $typecamion,$auto,$manuel,$verseur,$treuil)";
                $this->db->query($strSQL);
                // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
            } else {
                $typecamion = $this->db->escape($typecamion);
                /** @var string  $strSQL */
                $strSQL = "UPDATE camions
    	                      SET
    		                      TypeCamion=$typecamion,
    		                      IsAutomatique=$auto,
    		                      IsManuel=$manuel,
    		                      IsVerseur=$verseur,
    		                      HaveWinch=$treuil,
    		                      isdeleted=$isdel
    	                      WHERE  NoCamion = $nocamion";
                $this->db->query($strSQL);
                // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
            }
            echo '';
    }


    public function getAllCamion(...$args)
    {
        return $this->toCi3();
    }

    public function showRH($isdelete)
    {    
            //--------------------------------------------------------------
            $data['isdelete'] = $isdelete;
            $showdeleted = " WHERE isdeleted = ".$data['isdelete'];
            $typrh = $this->input->post_get('typerh');
            if (is_numeric($typrh)) {
                $typrh = ' AND idreshum = '.$typrh;
            } else {
                $typrh = '';
            }
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND (Nom LIKE $valtofind OR Prenom LIKE $valtofind OR email LIKE $valtofind)";
            }
            $from = $this->input->get('from');
            $this->load->model("Cedules_model", "cedules");
            //$data = $this->cedules->GetAllVillesCal();
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['CamionList'] = $this->cedules->GetCamions();
            $data['listrh'] = $this->cedules->GetRHList($showdeleted.$typrh.$valtofind);
            $data['addcamion'] = $this->cedules->ShowAddCamions();
            $data['updrh'] = $this->cedules->GetTypeRH('');
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            $this->session->set_userdata('LAST_PAGE', 9);
            if ($from == '1') {
                echo $data['listrh'];
            } else {
                echo view('sanivan/rh', $data);
            }
    }


    public function showSuivisRHList($dateactive)
    {    
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['privilege'] = $this->session->userdata('privilege');
            $dtedeb = $dateactive;
            $dtefin = $this->input->post_get('dtefin');
            $absence = (($this->input->post_get('absence') == 'all') ? '' : $this->input->post_get('absence'));
            $order = $this->input->post_get('order');
            $sens = $this->input->post_get('sens');
            $data['datestart'] = $dtedeb;
            $data['dateend'] = $dtefin;
            $data['absence'] = $absence;
            $data['fieldorder'] = $order;
            $data['sens'] = $sens;
            $data['orderby'] = (($order == 'idabsence') ? 'absence' : $order).' '.$sens;
            $data['order'] = '<a href="#" onclick="ReOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\')"><i class="fa fa-sort-'.(($sens == 'desc') ? 'asc' : 'desc').'" aria-hidden="true"></i></a>';
            //-----------------------------
            $data['entete'] = '<tr  style="text-align:right;">
                            <td class="cellule" align="center"></td>
        							      <td width="200" class="cellule" align="center">'.(($data['fieldorder'] == 'idrh') ? '<a  href="#" onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'idrh\' )" style="color:red;font-weight:bold;">Nom</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'idrh\' )">Nom</a>').'</td>
        							      <td width="10" class="cellule" align="center">'.(($data['fieldorder'] == 'datestart') ? '<a  href="#" onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'datestart\' )" style="color:red;font-weight:bold;">Date début</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'datestart\' )">Date début</a>').'</td>
        							      <td width="120" class="cellule" align="center">'.(($data['fieldorder'] == 'dateend') ? '<a  href="#" onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'dateend\' )" style="color:red;font-weight:bold;">Date fin</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'dateend\' )">Date fin</a>').'</td>
        							      <td width="200" class="cellule" align="center">'.(($data['fieldorder'] == 'idabsence') ? '<a  href="#" onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'idabsence\' )" style="color:red;font-weight:bold;">Type d\'absence</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderRH(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'idabsence\' )">Type d\'absence</a>').'</td>
        							      <td width="300" class="cellule" align="center">'.(($data['fieldorder'] == 'note') ? '<span style="color:red;font-weight:bold;">Note</span>&nbsp;&nbsp;'.$order : 'Note').'</td>
                            <td class="cellule" align="center"></td>
                          </tr>';
            if ($dtefin != '') {
                $valtofind = " (datestart >=".$this->db->escape($dtedeb)." or dateend <=".$this->db->escape($dtefin).") ";
            } else {
                $valtofind = " (datestart >=".$this->db->escape($dtedeb)." or dateend >".$this->db->escape($dtedeb).") ";
            }
            if ($this->input->post_get('valtofind') != "") {
                $fields = " (SELECT CONCAT( reshumaine.Nom,', ', reshumaine.Prenom) FROM reshumaine WHERE reshumaine.id = suivisrh.idrh) ";
                $sqlWhere = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind .= " AND $fields LIKE $sqlWhere ";
            }
            if ($absence != '') {
                $valtofind .= " AND idabsence = ".$this->db->escape($absence);
            }
            $data['typeabsences'] = $this->cedules->GetTypeAbsences($absence);
            $data['listsuivisrh'] = $this->cedules->GetSuivisRHList($valtofind, $data['orderby']);
            $from = $this->input->post_get('from');
            if ($from == 1) {
                foreach ($data as $key => $value) {
                    $data[$key] = utf8_encode(utf8_decode($value));
                }
                $show_json = json_encode($data);
                echo $show_json;
            } else {
                echo view('sanivan/list_suivisrh', $data);
            }
    }


    public function showSuivisCamList($dateactive)
    {    
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $dtedeb = $dateactive;
            $dtefin = $this->input->post_get('dtefin');
            $statut = $this->input->post_get('statut');
            $order = $this->input->post_get('order');
            $sens = $this->input->post_get('sens');
            //------------------------------
            $data['statut'] = $statut;
            $data['datestart'] = $dtedeb;
            $data['dateend'] = $dtefin;
            $data['fieldorder'] = $order;
            $data['sens'] = $sens;
            $data['orderby'] = (($order == 'idstatut') ? 'TypeDechet' : $order).' '.$sens;
            $data['order'] = '<a href="#" onclick="ReOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\')"><i class="fa fa-sort-'.(($sens == 'desc') ? 'asc' : 'desc').'" aria-hidden="true"></i></a>';
            //-----------------------------
            $data['entete'] = '<tr  style="text-align:right;">
                            <td class="cellule" align="center"></td>
        							      <td width="200" class="cellule" align="center">'.(($data['fieldorder'] == 'nocamion') ? '<a  href="#" onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'nocamion\' )" style="color:red;font-weight:bold;">Camion</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'nocamion\' )">Camion</a>').'</td>
        							      <td width="10" class="cellule" align="center">'.(($data['fieldorder'] == 'datestart') ? '<a  href="#" onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'datestart\' )" style="color:red;font-weight:bold;">Date début</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'datestart\' )">Date début</a>').'</td>
        							      <td width="120" class="cellule" align="center">'.(($data['fieldorder'] == 'dateend') ? '<a  href="#" onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'dateend\' )" style="color:red;font-weight:bold;">Date fin</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'dateend\' )">Date fin</a>').'</td>
        							      <td width="200" class="cellule" align="center">'.(($data['fieldorder'] == 'idstatut') ? '<a  href="#" onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'idstatut\' )" style="color:red;font-weight:bold;">Statut</a>&nbsp;&nbsp;'.$data['order'] : '<a  href="#"  onclick="ChangeFieldOrderCam(\''.(($sens == 'desc') ? 'asc' : 'desc').'\', \'idstatut\' )">Statut</a>').'</td>
        							      <td width="300" class="cellule" align="center">'.(($data['fieldorder'] == 'note') ? '<span style="color:red;font-weight:bold;">Note</span>&nbsp;&nbsp;'.$order : 'Note').'</td>
                            <td class="cellule" align="center"></td>
                          </tr>';
            //-----------------------------
            if ($dtefin != '') {
                $valtofind = " (datestart >=".$this->db->escape($dtedeb)." or dateend <=".$this->db->escape($dtefin).") ";
            } else {
                $valtofind = " (datestart >=".$this->db->escape($dtedeb)." or dateend >".$this->db->escape($dtedeb).") ";
            }
            if ($this->input->post_get('valtofind') != "") {
                $sqlWhere = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind .= " AND nocamion LIKE $sqlWhere ";
            }
            if ($statut != '') {
                $valtofind .= " AND idstatut = ".$this->db->escape($statut);
            }
            $data['listsuiviscam'] = $this->cedules->GetSuivisCamList($valtofind, $data['orderby']);
            $data['typededechet'] = $this->cedules->GetAllTypeProduits();
            $from = $this->input->post_get('from');
            if ($from == 1) {
                foreach ($data as $key => $value) {
                    $data[$key] = utf8_encode(utf8_decode($value));
                }
                $show_json = json_encode($data);
                echo $show_json;
            } else {
                echo view('sanivan/list_suiviscam', $data);
            }
            //--------------------------------------------------------------
    }


    public function getSuivisRH($idsuivis)
    {    
            /** @var string $returnval */
            $returnval = '';
            /** @var array $data */
            $data = '';
            $idsuivis = $this->db->escape($idsuivis);
            /** @var string $strSQL */
            $strSQL = "SELECT id, idrh, idabsence,  dateend, note, left(datestart, 10) AS datestart,
                          (SELECT reshumaine.idreshum FROM reshumaine WHERE reshumaine.id = suivisrh.idrh) AS idtyperh,
    		                          left(dateretourprevu, 10) AS dateretourprevu, showavis, avistoshow
    	                          FROM suivisrh WHERE id =  $idsuivis ";
            // $suivis = $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
            $suivis = $this->db->query($strSQL);
            foreach ($suivis->getResultArray() as $row) {
                $typerh = $row['idtyperh'];
                $idrh = $row['idrh'];
                $this->load->model("Cedules_model", "cedules");
                /** @var array $data */
                $data = array('listtype' => $this->cedules->GetAllEmployes($typerh, $idrh));
                $data['lstabsence'] = $this->cedules->GetTypeAbsences($row['idabsence']);
                $returnval = json_encode(array_merge($row, $data));
            }
            echo $returnval;
    }


    public function edtCam($isdelete)
    {    
            //--------------------------------------------------------------
            $data['isdelete'] = $isdelete;
            $showdeleted = " WHERE isdeleted = ".$data['isdelete'].' ';
            $typcam = $this->input->post_get('typecam');
            if (is_numeric($typcam)) {
                $typcam = ' AND TypeCamion = '.$typcam;
            } else {
                $typcam = '';
            }
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND NoCamion LIKE $valtofind ";
            }
            $from = $this->input->post_get('from');
    
            $this->load->model("Cedules_model", "cedules");
            //$data = $this->cedules->GetAllVillesCal();
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['CamionList'] = $this->cedules->GetCamions();
            $data['listcamions'] = $this->cedules->GetCamionList($showdeleted.$typcam.$valtofind);
            $data['addcamion'] = $this->cedules->ShowAddCamions();
            $data['updcamion'] = $this->cedules->GetAllTypeCamion('');
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            $this->session->set_userdata('LAST_PAGE', 10);
            if ($from == '1') {
                echo $data['listcamions'];
            } else {
                echo view('sanivan/editcamion', $data);
            }
    }


    public function rhInfo($id)
    {    
            $id = $this->db->escape($id);
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->GetRHInfo($id);
    }


    public function DeleteRH($id)
    {    
            //--------------------------------------------------------------
            $id = $this->db->escape($id);
            // $this->remote_db->query("DELETE FROM reshumaine WHERE id = $id"); // Désactivé - connexion distante bloquée sur WHC
            $this->db->query("DELETE FROM reshumaine WHERE id = $id");
            echo 'ok';
    }


    public function DeleteSuivisRH($id)
    {    
            //--------------------------------------------------------------
            $id = $this->db->escape($id);
            $this->db->query("DELETE FROM suivisrh WHERE id = $id");
            echo 'ok';
    }


    public function setNbCam($id)
    {    
            $id = $this->db->escape($id);
            $value = $this->db->escape($this->input->post_get('value'));
            /** @var string  $strSQL */
            $strSQL = "UPDATE grp_cedules
    	                    SET
    		                    nb_cam=$value
    	                    WHERE id = $id";
            $this->db->query($strSQL);
            echo 'OK';
    }


    public function sendRHInfo($id, $nom, $prenom, $typerh, $courriel, $tel1, $tel2, $codeusager, $pass,
        $isdel, $dim, $lun, $mar, $mer, $jeu, $ven, $sam)
    {    
            $this->load->model("Cedules_model", "cedules");
            //$this->load->library('LibInscription');
            $nom = $this->db->escape($prenom);
            $nom = $this->db->escape($nom);
            $typerh = $this->db->escape($typerh);
            $courriel = $this->db->escape($courriel);
            $tel1 = $this->db->escape($tel1);
            $codeusager = $this->db->escape($codeusager);
            $strSQL = "SELECT id FROM reshumaine WHERE id = $id";
            $query = $this->db->query($strSQL);
            $row = $query->getRow();
            if (! isset($row)) {
                //$pass = ((trim($pass) == '')? 'NULL' : $this->libinscription->getPass($pass)) ;
                /** @var string  $strSQL */
                $strSQL = "INSERT INTO reshumaine
    	                (Nom, Prenom, idreshum,
    	                DIM, LUN, MAR, MER, JEU, VEN, SAM, isdeleted,
    	                tel1, tel2, courriel, login, pass)
    	                VALUES ($nom, $prenom,$typerh,
    				                $dim, $lun, $mar, $mer, $jeu, $ven, $sam, $isdel,
    				                $tel1, $tel2, $courriel, $codeusager, $pass)";
                $this->db->query($strSQL);            
                // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
            } else {
                //$pass = ((trim($pass) == '')? 'NULL' : ",pass=".$this->libinscription->getPass($pass)) ;
                /** @var string  $strSQL */
                $strSQL = "UPDATE reshumaine
    	                        SET
    		                        Nom=$nom,
    		                        Prenom=$prenom,
    		                        idreshum=$typerh,
    		                        DIM=$dim,
    		                        LUN=$lun,
    		                        MAR=$mar,
    		                        MER=$mer,
    		                        JEU=$jeu,
    		                        VEN=$ven,
    		                        SAM=$sam,
    		                        isdeleted=$isdel,
    		                        tel1=$tel1,
    		                        courriel=$courriel,
    		                        login=$codeusager
                                $pass
    	                        WHERE id=$id";
                $this->db->query($strSQL);
                // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
                if ($isdel == 1) {
                    $strSQL = "UPDATE cedules
    	                          SET
    		                          idchauffeurs=0
    	                          WHERE DateJour >= CURDATE() and idchauffeurs = $id";
                    $this->db->query($strSQL);
                    $strSQL = "UPDATE cedules
    	                          SET
    		                          HELPERS_1=0
    	                          WHERE DateJour >= CURDATE() HELPERS_1 = $id";
                    $this->db->query($strSQL);
                }
            }
            echo $this->cedules->GetRHList();
    }


    public function postRH($id, $isdel, $dim, $lun, $mar, $mer, $jeu, $ven, $sam, $isbroker)
    {    
            $this->load->model("Cedules_model", "cedules");
            $this->load->library('LibInscription');
            //-------------------
            //$id = $this->input->post_get('id') ;
            if (is_numeric($id)) {
                $id = $this->db->escape($id);
                $prenom = $this->db->escape($this->input->get('prenom'));
                $nom = $this->db->escape($this->input->get('nom'));
                $typerh = $this->db->escape($this->input->get('typerh'));
                $email = $this->db->escape($this->input->get('email'));
                $tel1 = $this->db->escape($this->input->get('tel1'));
                $tel2 = $this->db->escape($this->input->get('tel2'));
                $login = $this->db->escape($this->input->get('login'));
                $havescan = $this->input->get('havescan');
                if (! is_numeric($havescan)) {
                    $havescan = 0;
                }
                //-------------------
                $strSQL = "SELECT id FROM reshumaine WHERE id = $id";
                $query = $this->db->query($strSQL);
                $row = $query->getRow();
                if (! isset($row)) {
                    $pass = ((trim($this->input->get('pass')) == '') ? 'NULL' : $this->libinscription->getPass($this->input->get('pass')));
                    /** @var string  $strSQL */
                    $strSQL = "INSERT INTO reshumaine
    										(Nom, Prenom, idreshum,
    										DIM, LUN, MAR, MER, JEU, VEN, SAM, isdeleted, isbroker, email,tel1, tel2, login, pass, havescan)
    										VALUES ($nom, $prenom,$typerh,
    													$dim, $lun, $mar, $mer, $jeu, $ven, $sam, $isdel,$isbroker,$email, $tel1, $tel2, $login,$pass, $havescan)";
                    $this->db->query($strSQL);                                   
                    // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
                } else {
                    $test = $this->input->get('pass');
                    $pass = ((trim($this->input->get('pass')) == '') ? '' : 'pass='.$this->libinscription->getPass($this->input->get('pass')).',');
                    /** @var string  $strSQL */
                    $strSQL = "UPDATE reshumaine
    														SET
    															Nom=$nom,
    															Prenom=$prenom,
    															idreshum=$typerh,
    															DIM=$dim,
    															LUN=$lun,
    															MAR=$mar,
    															MER=$mer,
    															JEU=$jeu,
    															VEN=$ven,
    															SAM=$sam,
    															isbroker=$isbroker,
                                  tel1=$tel1,
                                  tel2=$tel2,
                                  login=$login,
                                  havescan=$havescan,
                                  $pass
    															email=$email
    														WHERE id=$id";
                    $this->db->query($strSQL);
                    // $this->remote_db->query($strSQL); // Désactivé - connexion distante bloquée sur WHC
                    if ($isdel == 1) {
                        $strSQL = "UPDATE cedules
    	                          SET
    		                          idchauffeurs=0
    	                          WHERE DateJour >= CURDATE() and idchauffeurs = $id";
                        $this->db->query($strSQL);
                        $strSQL = "UPDATE cedules
    	                          SET
    		                          HELPERS_1=0
    	                          WHERE DateJour >= CURDATE() and HELPERS_1 = $id";
                        $this->db->query($strSQL);
                    }
                }
            }
            echo $this->cedules->GetRHList();
    }


    public function getReloadTV($dateactive)
    {    
    
            $this->load->model("Cedules_model", "cedules");
            //$data['today'] = $this->cedules->LoadToday($this->db->escape( $dateactive ), 3, $activedrop);
            //echo $data['today'];
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

    public function showVille($isdelete)
    {    
            //--------------------------------------------------------------
            $data['isdelete'] = $isdelete;
            $showdeleted = " WHERE isdeleted = ".$data['isdelete'];
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND Nom LIKE $valtofind ";
            }
            $from = $this->input->post_get('from');
            $this->load->model("Cedules_model", "cedules");
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['listville'] = $this->cedules->GetVilleList($showdeleted.$valtofind);
            $data['addcamion'] = $this->cedules->ShowAddCamions();
            $data['updcamion'] = $this->cedules->GetTypeCamion('');
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            $this->session->set_userdata('LAST_PAGE', 8);
            if ($from == '1') {
                echo $data['listville'];
            } else {
    
                echo view('sanivan/villes', $data);
            }
            //--------------------------------------------------------------
    }


    public function postVille($id, $isdel)
    {    
            $this->load->model("Cedules_model", "cedules");
            $id = $this->db->escape($id);
            $nom = $this->input->get('nom');
            $nom = $this->db->escape(str_replace('%20', ' ', $nom));
            $color = $this->db->escape('#'.$this->input->post_get('couleur'));
            $sendinfo = $this->db->escape($this->input->post_get('sendinfo'));
            $strSQL = "SELECT Nom FROM ville WHERE  id = $id";
            $query = $this->db->query($strSQL);
            $row = $query->getRow();
            if (! isset($row)) {
    
                /** @var string  $strSQL */
                $strSQL = "INSERT INTO ville
    	                      (Nom,  colorhex, sendinfo)
    	                      VALUES ($nom, $color, $sendinfo)";
                $this->db->query($strSQL);
            } else {
                /** @var string  $strSQL */
                $strSQL = "UPDATE ville
    	                      SET
    		                      Nom=$nom,
    		                      colorhex=$color,
    		                      isdeleted=$isdel,
    		                      sendinfo=$sendinfo
    	                      WHERE  id = $id";
                $this->db->query($strSQL);
                $this->showVille($isdel);
            }
            echo $this->cedules->GetVilleList();
    }


    public function showDictionnaire($table, $from)
    {    
            //--------------------------------------------------------------
            $this->session->set_userdata('LAST_PAGE', 7);
            $this->session->set_userdata('CHAMP_2', $from);
            $this->session->set_userdata('CHAMP_1', $table);
            $tablename = '';
            $this->load->model("Cedules_model", "cedules");
            //$data = $this->cedules->GetAllVillesCal();
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
    
            switch ($table) {
                case 1:
                    $tablename = ' typecamion ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">TYPES DE CAMIONS </th></tr>
                        <tr style="text-align:center;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:center;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionnaire($tablename, $table);
                    break;
                case 2:
                    $tablename = ' typerh ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">TYPES DE RESSOURCES HUMAINES </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionnaire($tablename, $table);
                    break;
                case 3:
                    $tablename = ' typeabsence ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">TYPES D\'ABSENCES </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Couleur de fond</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Couleur du texte</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionnaire($tablename, $table);
                    break;
                case 4:
                    $tablename = ' typededechet ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">TYPES DE DÉCHETS </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Couleur de fond</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Couleur du texte</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetTypeDechets($tablename, $table);
                    break;
                case 5:
                    $tablename = ' statutcamion ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">STATUTS DE CAMIONS </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionnaire($tablename, $table);
                    break;
                case 6:
                    $tablename = ' pertetemps ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">PERTES DE TEMPS </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetTypeDechets($tablename, $table);
                    break;
                case 7:
                    $tablename = ' depotoire ';
                    $data['entete'] = '<tr  style="text-align:center;"><th colspan="7" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">DÉPOTOIRS </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Sanivan</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Type de Dépôt</th>
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Prix <br />RollOff</th>
                                     <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Prix <br />Sani.</th>
                                      <th class="cellule" width="500" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Adresse</th>
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th colspan="7" class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionDepotoire($tablename, $table);
                    break;
                case 8:
                    $tablename = ' emplacements ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">EMPLACEMENTS </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionEmplacement($tablename, $table);
                    break;
                case 9:
                    $tablename = ' dictionprix ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">FRAIS DIVERS </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Type de Ressource</th>
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Prix</th>
                                      <th class="cellule" width="150" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Date de début</th>
                                      <th class="cellule" width="150" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Date de fin</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th colspan="7" class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listitem'] = $this->cedules->GetListItem('');
                    $data['listdict'] = $this->cedules->GetDictionPrix($tablename, $table);
                    break;
                case 10:
                    $tablename = ' typecontenant ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="11" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">TYPE DE CONTENANT </th></tr><tr style="text-align:right;">
                                      <th class="thdiction" width="100"  align="center">Supprimé</th>
        							                <th class="thdiction" width="300"  align="center">Nom</th>
        							                <th class="thdiction" width="300"  align="center">Tonnage</th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:130px">Prix</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:130px">Redevance</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:130px">Carburant</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:200px">Carburant Site</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:130px">Démolition</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:130px">Bardeau asphalte</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:130px">Béton</label></th>
        							                <th class="thdiction" width="300"  align="center"><label style="width:200px">Béton armé</label></th>
                                      <th class="thdiction" width="300"  align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th colspan="12" class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionContenant($tablename, $table);
                    break;
                case 11:
                    $tablename = ' statutrequete ';
                    $data['entete'] = '<tr colspan="4" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">STATUT DE REQUÊTE </th></tr><tr style="text-align:right;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Couleur</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:right;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionnaire($tablename, $table);
                    break;
                case 12:
                    $tablename = ' typeextra ';
                    $data['entete'] = '<tr colspan="3" style="text-align:center;"><th colspan="3" style="font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">TYPES HRES EXTRA </th></tr>
                        <tr style="text-align:center;">
                                      <th class="cellule" width="100" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Supprimé</th>
        							                <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Nom</th>
                                      <th class="cellule" width="300" style="background-color: darkslateblue;color: white; font-weight:bold;line-height: 30px;font-size:medium;text-align:center;" align="center">Enregistrer</th>
                                    </tr>
                                    <tr style="text-align:center;">
                                      <th class="cellule"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
        							                <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <th class="cellule" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>';
                    $data['listdict'] = $this->cedules->GetDictionnaire($tablename, $table);
                    break;
            }
            $data['addtodiction'] = $this->cedules->getAddDiction($table);
            $data['addcamion'] = $this->cedules->ShowAddCamions();
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            switch ($from) {
                case 1:
                    echo view('sanivan/dictionnaires', $data);
                    break;
                case 2:
                    echo '<table >
                        <thead>
                            '.$data['entete'].'
                        </thead>
                        <tbody id="tboProd">
                          '.$data['listdict'].'
                        </tbody>
                  </table>';
                    break;
                case 3:
                    echo $this->cedules->getAddDiction($table);
            }
    }


    public function postDiction($isdel)
    {    
            $this->load->model("Cedules_model", "cedules");
            $notype = $this->input->post_get('notype');
            $id = $this->input->get('id');
            if (is_numeric($notype)) {
                switch ($notype) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 8:
                    case 12:
                        $nom = $this->input->get('nom');
                        $this->cedules->postDiction($id, $notype, $isdel, $nom, '', '', '', '');
                        break;
                    case 7:
                        $nom = $this->input->post_get('nom');
                        $typedepot = $this->input->post_get('typedepot');
                        $prix = $this->input->post_get('prix');
                        $prix_sani = $this->input->post_get('prix_sani');
                        $redevance = $this->input->post_get('redevance');
                        $adresse = $this->input->post_get('adresse');
                        $issanivan = $this->input->post_get('issanivan');
                        $this->cedules->postDiction($id, $notype, $isdel, $nom, $typedepot, $prix, $redevance, $adresse, $prix_sani, $issanivan);
                        break;
                    case 9:
                        $nom = $this->input->get('typeitem');
                        $datestart = $this->input->get('datestart');
                        $prix = $this->input->get('prix');
                        $dateend = $this->input->get('dateend');
                        $this->cedules->postDiction($id, $notype, $isdel, $nom, $datestart, $prix, $dateend, '');
                        break;
    
                }
                echo 'OK';
            }
    }


    public function postStatutRequete($isdel)
    {    
            #todo
            // faire la logique pour Francais et Anglais
            $description = $this->db->escape($this->input->get('description'));
            $couleur = $this->db->escape('#'.$this->input->get('couleur'));
            $id = $this->db->escape($this->input->get('id'));
            $strSQL = "SELECT * FROM statutrequete WHERE  id = $id";
            $query = $this->db->query($strSQL);
            $row = $query->getRow();
            if (! isset($row)) {
    
                /** @var string  $strSQL */
                $strSQL = "INSERT INTO statutrequete (description_fr,couleur)
                      VALUES ($description,$couleur)";
                $this->db->query($strSQL);
            } else {
                /** @var string  $strSQL */
                $strSQL = "UPDATE  statutrequete SET
                              description_fr=$description,
                              couleur=$couleur,
                              isdeleted=$isdel
                        WHERE  id = $id";
                $this->db->query($strSQL);
            }
    }


    public function postContenant($isdel)
    {    
            //$this->load->model("Cedules_model", "cedules");
            //$notype = $this->input->post_get('notype');
            $id = $this->db->escape($this->input->get('id'));
            //    prix, redevance, carburant, carburant_site, demolition, asphalte, beton, beton_arme, tonnage
            $nom = $this->db->escape($this->input->post_get('nom'));
            $tonnage = $this->db->escape($this->input->post_get('tonnage'));
            $prix = $this->db->escape(str_replace(',', '.', $this->input->post_get('prix')));
            $redevance = $this->db->escape(str_replace(',', '.', $this->input->post_get('redevance')));
            $carburant = $this->db->escape(str_replace(',', '.', $this->input->post_get('carburant')));
            $carburant_site = $this->db->escape(str_replace(',', '.', $this->input->post_get('carburant_site')));
            $demolition = $this->db->escape(str_replace(',', '.', $this->input->post_get('demolition')));
            $asphalte = $this->db->escape(str_replace(',', '.', $this->input->post_get('asphalte')));
            $beton = $this->db->escape(str_replace(',', '.', $this->input->post_get('beton')));
            $beton_arme = $this->db->escape(str_replace(',', '.', $this->input->post_get('beton_arme')));
            //-------------------------------------------------------------------------
            switch ($this->input->get('id')) {
                case 0:
                    break;
                default:
                    $strSQL = "UPDATE typecontenant
    	                      SET
    		                      nom=$nom,
    		                      tonnage=$tonnage,
    		                      prix=$prix,
    		                      redevance=$redevance,
    		                      carburant=$carburant,
    		                      carburant_site=$carburant_site,
    		                      demolition=$demolition,
    		                      asphalte=$asphalte,
    		                      beton=$beton,
    		                      beton_arme=$beton_arme,
    		                      isdeleted=$isdel
    	                      WHERE id = $id";
                    $this->db->query($strSQL);
                    break;
            }
            echo 'OK';
    }


    public function getCamFromType($idtypecam)
    {    
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Creationcd_model", "creationcd");
            $activedate = $this->db->escape($this->input->post_get('activedate'));
            $returnval = $this->creationcd->GetCamions($activedate, $idtypecam);
            echo $returnval;
    }


    public function getCamFromTypeTDB($idtypecam)
    {    
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Creationcd_model", "creationcd");
            $returnval = $this->creationcd->GetHomeCamions($idtypecam);
            echo $returnval;
    }


    public function getTypeCam($idtypecam)
    {    
            $activedate = $this->input->post_get('activedate');
            $nocamion = $this->input->post_get('camion');
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Creationcd_model", "creationcd");
            $returnval = $this->creationcd->GetAllCamions($activedate, $nocamion, $idtypecam);
            echo $returnval;
    }


    public function getRHFromType($idtyperh)
    {    
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Creationcd_model", "creationcd");
            $returnval = $this->creationcd->GetChauffeursToDropHome(date('Y-m-d'), $idtyperh);
            echo $returnval;
    }


    public function getAvisVerif($dteTo, $dteFrom, $typecopie)
    {    
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Cedules_model", "cedules");
            /** @var array $arrDteFrom */
            $arrDteFrom = explode('-', $dteFrom);
            if (checkdate($arrDteFrom[1], $arrDteFrom[2], $arrDteFrom[0])) {
                /** @var array $arrDteTo */
                $arrDteTo = explode('-', $dteTo);
                if (checkdate($arrDteTo[1], $arrDteTo[2], $arrDteTo[0])) {
                    $returnval = $this->cedules->getAvisVerif($dteFrom, $dteTo, $typecopie);
                }
            }
            echo $returnval;
            return $returnval;
    }


    public function sendMailAvis()
    {    
            $dteFrom = $this->input->post_get('dtefrom');
            $dteTo = $this->input->post_get('dteto');
            $typecopie = $this->input->post_get('typecopie');
            $avis = $this->getAvisVerif($dteTo, $dteFrom, $typecopie);
            $html = "Bonjour, <br><br>
                    Vous trouverez ci-joint le d&eacute;tail des ressources dont une action est requise
                     avant qu'ils puissent reprendre <br>  <b> leurs tâches habituelles.</b><br> <br> <br> <b>
                    <table class=\"display responsive nowrap\" width=\"100%\">
                                <thead>
                                    <tr style=\"text-align:center;\">
        							                <th class=\"cellule\" align=\"center\">Nom des employ&eacute;s</th>
                                    </tr>
                                    <tr style=\"text-align:center;\">
        							                <th class=\"cellule\" align=\"center\"><label >&nbsp;&nbsp;&nbsp;&nbsp;</label></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  $avis
                                </tbody>
                      </table>
                     </b> <br> <br> <br> <br>Commentaires / Comments:
                    Ce courriel ainsi que toute pi&egrave;ce jointe s''adresse seulement au(x) destinataire(s) design&eacute;(es) et
                    peut contenir des renseignements confidentiels ou privilegies.
                    Si vous avez re&ccedil;u ce message par erreur ou que vous n''&ecirc;tes pas un destinataire design&eacute;
                    veuillez en avertir immediatement l''exp&eacute;diteur en repondant au message et
                    effacez-en toutes les copies dans votre ordinateur.
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;<br>
                    This e-mail message including any attachments is intended only for the named recipients above and may contain
                    confidential or privileged information.
                    If you have received this message in error, or are not the named recipient(s), please immediately notify the sender
                    by replying to the message and deleting all copies of it from your computer.";
            $subject = "Avis pour ressource";
            $to = $this->session->userdata('email');
            $headers = 'From: nepasrepondre@robert-daoust.ca'."\r\n".
                "MIME-Version: 1.0"."\r\n".
                'Reply-To: nepasrepondre@robert-daoust.ca'."\r\n".
                "Content-type:text/html;charset=UTF-8"."\r\n".
                'X-Mailer: PHP/'.phpversion();
            mail($to, $subject, $html, $headers);
            echo 'Courriel envoyé.';
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

    public function postAllHresAide()
    {    
            //--------------------------------------------------------------
            $idsuivis = $this->db->escape($this->input->post_get('idsuivis'));
            $start = $this->db->escape($this->input->post_get('edtdeb_'.$this->input->post_get('idsuivis')));
            $end = $this->db->escape($this->input->post_get('edtfin_'.$this->input->post_get('idsuivis')));
            $pause = $this->db->escape($this->input->post_get('edtpause_'.$this->input->post_get('idsuivis')));
            $note = $this->db->escape($this->input->post_get('edtnote_'.$this->input->post_get('idsuivis')));
            $ActiveDate = $this->input->post_get('activedate');
            $strSQL = "UPDATE suivis
    	                    SET
    		                    StartTime = $start,
    		                    EndTime = $end,
    		                    pause = $pause,
    		                    note = $note
    	                    WHERE id = $idsuivis";
            $this->db->query($strSQL);
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->getHresAides($ActiveDate, '');
    }


    public function postViewHS()
    {    
            //--------------------------------------------------------------
            $iduser = $this->db->escape($this->session->userdata('user_id'));
            $strSQL = "UPDATE messages
    	              SET
    		              datecreate=NOW()
    	              WHERE iduser = $iduser";
            $this->db->query($strSQL);
    }


    public function reLoadCedule()
    {    
            $data = array();
            //-----------------------------------------------------------------------
            $typecedule = $this->input->post_get('typecedule');
            $typecedule = ((isset($typecedule)) ? $this->input->post_get('typecedule') : 1);
            $activedrop = $this->input->post_get('activedrop');
            $activedrop = ((isset($activedrop)) ? $this->input->post_get('activedrop') : 0);
            $notab = $this->input->post_get('notab');
            $notab = ((isset($notab)) ? $this->input->post_get('notab') : 1);
            $dateactive = $this->input->post_get('dateactive');
            //	--------------------------------------------------------------
    
            $this->load->model("Creationcd_model", "creationcd");
            $data = $this->creationcd->LoadCreateToday($dateactive, $typecedule);
    
            $this->load->model("Cedules_model", "cedules");
            $test = $this->cedules->LoadToday($dateactive, 4, $activedrop);
            $test['listhresaides'] = $this->cedules->getHresAides($dateactive, '');
            $dataall = (object) array_merge($data, $test);
            echo json_encode($dataall);
            //
    
    
    
    
            //$test =$this->cedules->LoadToday($dateactive, 4);
            //echo  $test;
            //-----------------------------------------------------------------------
            //$this->showToday(4, $dateactive);
    }


    public function loadCedule()
    {    
            $dateactive = $this->db->escape($this->input->post_get('dateactive'));
            $this->load->model("Cedules_model", "cedules");
            //$data = $this->cedules->LoadToday($dateactive, 4, $activedrop);
            //$data['listhresaides'] = $this->cedules->getHresAides($dateactive,'');
            //return $data['today'];
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

    public function DeleteSecteur($id)
    {    
            //--------------------------------------------------------------
            $id = $this->db->escape($id);
            $this->db->query("DELETE FROM secteurs WHERE id = $id");
            echo 'ok';
    }


    public function showSecteur($idville)
    {    
            //--------------------------------------------------------------
            $data['listsecteur'] = $this->cedules->GetSecteursList($idville);
            echo $data['listsecteur'];
    }


    public function postSecteur($id, $isdel)
    {    
            $this->load->model("Cedules_model", "cedules");
            $id = $this->db->escape($id);
            $nom = $this->input->get('nom');
            $nom = $this->db->escape(str_replace('%20', ' ', $nom));
            $idville = $this->db->escape($this->input->get('idville'));
            $polylines = $this->input->get('polylines'); // Array des IDs de polylines
    
            $strSQL = "SELECT Nom FROM secteurs WHERE  id = $id";
            $query = $this->db->query($strSQL);
            $row = $query->getRow();
    
            if (!isset($row)) {
                $strSQL = "INSERT INTO secteurs
                          (Nom, idville)
                          VALUES ($nom, $idville)";
                $this->db->query($strSQL);
                $secteurId = $this->db->insertID();
            } else {
                $strSQL = "UPDATE secteurs
                          SET
                              Nom=$nom,
                              idville=$idville,
                              isdeleted=$isdel
                          WHERE  id = $id";
                $this->db->query($strSQL);
                $secteurId = $id;
    
                // Supprimer les anciennes associations polylines
                $this->db->delete('secteur_polylines', array('idsecteur' => $secteurId));
            }
    
            // Insérer les nouvelles associations polylines
            if (!empty($polylines) && is_array($polylines)) {
                foreach ($polylines as $idpolyline) {
                    $this->db->table('secteur_polylines')->insert(array(
                        'idsecteur' => $secteurId,
                        'idpolyline' => $this->db->escape($idpolyline)
                    ));
                }
            }
    
            echo $this->cedules->GetSecteursList();
    }


    public function getHSExist($cam, $status)
    {    
            $returnval = 0;
            $camesc = $this->db->escape($cam);
            $strSQL = "SELECT nocamion FROM suiviscamion where nocamion = $camesc and datestart <= CURDATE() and ( dateend > CURDATE() or dateend is null) and idstatut = $status";
            $optsys = $this->db->query($strSQL);
            foreach ($optsys->getResult() as $row) {
                if ($row->nocamion == $cam)
                    $returnval = 1;
                else
                    $returnval = 0;
            }
            echo $returnval;
    }


    public function getRHExist($idrh)
    {    
            $returnval = 0;
            $idrhesc = $this->db->escape($idrh);
            $strSQL = "SELECT id,idrh FROM suivisrh where idrh = $idrhesc and datestart <= CURDATE() and ( dateend > CURDATE() or dateend is null)";
            $optsys = $this->db->query($strSQL);
            foreach ($optsys->getResult() as $row) {
                if ($row->idrh == $idrh)
                    $returnval = $row->id;
                else
                    $returnval = 0;
            }
            echo $returnval;
    }


    public function setTransfertVille($id, $idcedule, $group, $date)
    {    
            $idesc = $this->db->escape($id);
            $idceduleesc = $this->db->escape($idcedule);
            $group = $this->db->escape($group);
            // Transfere TOUTES les lignes du groupe (camions, chauffeurs, aides)
            // et non seulement la ligne cliquee.
            $strSQL = "UPDATE cedules
    	                SET
    		                TypeCedule=$idceduleesc
    	                WHERE idgrp = $group";
            $this->db->query($strSQL);
            $strSQL = "UPDATE grp_cedules
    	              SET
    		              TypeCedule=$idceduleesc
    	              WHERE id =$group";
            $this->db->query($strSQL);
    
            //----------------------------------------------------------------------
            $this->showToday(1, $date);
    }


    public function showPolyline()
    {    
            //--------------------------------------------------------------
            $from = $this->input->get('from');
            $this->load->model("Cedules_model", "cedules");
            $data['listpolyline'] = $this->cedules->getListPolyline();
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['privilege'] = $this->session->userdata('privilege');
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            $this->session->set_userdata('LAST_PAGE', 9);
            echo view('sanivan/list_polyline', $data);
    }


    public function getReduitPoly($id)
    {    
            $this->load->model("Polyline_model", "polyline");
            $data = $this->polyline->ReduitPolyline($id);
            //var_dump( $data);
            return redirect()->to('/sanivan/listpolyline');
    }


    public function getSecteursByVille()
    {    
            $idville = (int) $this->input->get('idville');
    
            if ($idville <= 0) {
                echo json_encode(['ok' => false, 'error' => 'ID ville invalide']);
                return;
            }
    
            $this->load->model("Cedules_model", "cedules");
    
            $strSQL = "SELECT id, idville, nom
                   FROM  polilyne_ville
                   WHERE idville = $idville
                   ORDER BY nom";
    
            $query = $this->db->query($strSQL);
            $secteurs = [];
    
            foreach ($query->getResult() as $row) {
                $secteurs[] = [
                    'id' => (int) $row->id,
                    'nom' => $row->nom
                ];
            }
    
            echo json_encode([
                'ok' => true,
                'secteurs' => $secteurs
            ]);
    }


    public function getPolylinesBySecteurVille()
    {    
            $idville = $this->input->get('idville');
            $idsecteur = $this->input->get('idsecteur');
            $selectedId = $this->input->get('selectedId');
    
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->GetOptPolylinesBySecteurVille($idville, $idsecteur, $selectedId);
    }


    public function setPolylineCedule()
    {    
            $idcedule = $this->input->post('idcedule');
            $idpolyline = $this->input->post('idpolyline');
    
            if (!$idcedule || !$idpolyline) {
                echo 'ERROR: Paramètres manquants';
                return;
            }
    
            $idcedule = $this->db->escape($idcedule);
            $idpolyline = $this->db->escape($idpolyline);
    
            // Récupérer l'idsecteur et le nom depuis la table polilyne_ville
            $strSQL = "SELECT idsecteur, nom FROM polilyne_ville WHERE id = $idpolyline";
            $query = $this->db->query($strSQL);
    
            if ($query->getNumRows() > 0) {
                $row = $query->getRow();
                $idsecteur = $this->db->escape($row->idsecteur);
                $nomPolyline = $this->db->escape($row->nom);
    
                // Mettre à jour la cédule
                $strSQL = "UPDATE cedules 
                       SET idsecteur = $idsecteur,
                           idpolyline = $idpolyline,
                           Secteur = $nomPolyline
                       WHERE ID = $idcedule";
    
                $this->db->query($strSQL);
                echo 'OK';
            } else {
                echo 'ERROR: Polyline introuvable';
            }
    }


    public function getPolylinesGeoJson($idsecteur)
    {    
            $idsecteur = (int) $idsecteur;
    
            if ($idsecteur <= 0) {
                echo json_encode(['ok' => false, 'error' => 'ID secteur invalide']);
                return;
            }
    
            $this->load->model("Cedules_model", "cedules");
    
            // ⬇️ ÉTAPE 1 : Récupérer la ville du secteur
            $strSQL = "SELECT idville FROM secteurs WHERE id = " . $this->db->escape($idsecteur);
            $secteur = $this->db->query($strSQL);
    
            if ($secteur->getNumRows() === 0) {
                echo json_encode(['ok' => false, 'error' => 'Secteur introuvable']);
                return;
            }
    
            $idville = $secteur->getRow()->idville;
    
            // ⬇️ ÉTAPE 2 : Récupérer les polylines du secteur ET de la ville
            $strSQL = "SELECT p.id, p.nom, p.parcours, p.idville, p.idsecteur,
                          v.Nom as ville_nom,
                          s.Nom as secteur_nom
                   FROM polilyne_ville p
                   LEFT JOIN ville v ON p.idville = v.id
                   LEFT JOIN secteurs s ON p.idsecteur = s.id
                   WHERE p.idsecteur = " . $this->db->escape($idsecteur) . "
                     AND p.idville = " . $this->db->escape($idville) . "
    
                   ORDER BY p.nom";
    
            $query = $this->db->query($strSQL);
    
            $polylines = [];
            foreach ($query->getResult() as $row) {
                $coords = [];
    
                // Parser le parcours (format : "lat,lng_lat,lng_...")
                if (!empty($row->parcours)) {
                    $points = explode('_', $row->parcours);
    
                    foreach ($points as $point) {
                        $point = trim($point);
                        if (empty($point))
                            continue;
    
                        $latLng = explode(',', $point);
    
                        if (count($latLng) === 2) {
                            $lat = (float) trim($latLng[0]);
                            $lng = (float) trim($latLng[1]);
    
                            // Validation basique des coordonnées
                            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                                $coords[] = [$lat, $lng];
                            }
                        }
                    }
                }
    
                if (count($coords) > 0) {
                    $polylines[] = [
                        'id' => (int) $row->id,
                        'nom' => $row->nom,
                        'ville' => $row->ville_nom,
                        'secteur' => $row->secteur_nom,
                        'coords' => $coords
                    ];
                }
            }
    
            // Log pour debug
            error_log('Secteur ' . $idsecteur . ' (Ville ' . $idville . ') : ' . count($polylines) . ' polylines trouvées');
    
            header('Content-Type: application/json');
            echo json_encode([
                'ok' => true,
                'secteur' => [
                    'id' => $idsecteur,
                    'idville' => $idville
                ],
                'polylines' => $polylines
            ]);
    }


    public function getPolylinesBySecteur()
    {    
            $idville = $this->input->get('idville');
            $idsecteur = $this->input->get('idsecteur');
            $selectedId = $this->input->get('selectedId');
    
            $this->load->model("Cedules_model", "cedules");
    
            $options = '<option value="0">-- Parcours --</option>';
    
            if ($idsecteur > 0) {
                $options .= $this->cedules->GetOptPolylinesBySecteurVille($idville, $idsecteur, $selectedId);
            }
    
            echo $options;
    }


    public function setSecteurCedule()
    {    
            $idcedule = $this->input->post('idcedule');
            $idsecteur = $this->input->post('idsecteur');
    
            if (!$idcedule) {
                echo 'ERROR: ID cédule manquant';
                return;
            }
    
            $idcedule = $this->db->escape($idcedule);
            $idsecteur = ($idsecteur > 0) ? $this->db->escape($idsecteur) : 'NULL';
    
            // Mettre à jour le secteur ET réinitialiser la polyline
            $strSQL = "UPDATE cedules 
                   SET idsecteur = $idsecteur,
                       idpolyline = 0,
                       Secteur = ''
                   WHERE ID = $idcedule";
    
            $this->db->query($strSQL);
    
            $error = $this->db->error();
            if (!empty($error['code'])) {
                echo 'ERROR: ' . $error['message'];
            } else {
                echo 'OK';
            }
    }


    public function togglePolylineCedule()
    {    
            $idcedule = $this->input->post('idcedule');
            $idpolyline = $this->input->post('idpolyline');
            $isChecked = $this->input->post('isChecked');
    
            log_message('debug', 'togglePolylineCedule - idcedule: ' . var_export($idcedule, true) . ', idpolyline: ' . var_export($idpolyline, true) . ', isChecked: ' . var_export($isChecked, true));
    
            if (!$idcedule || !$idpolyline) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Paramètres manquants']));
                return;
            }
    
            try {
                if ($isChecked === 'true') {
                    $strSQL = "SELECT COUNT(*) as count FROM cedule_polylines 
                           WHERE idcedule = " . $this->db->escape($idcedule) . " 
                           AND idpolyline = " . $this->db->escape($idpolyline);
                    $result = $this->db->query($strSQL);
                    if (!$result) {
                        $err = $this->db->error();
                        log_message('error', 'togglePolylineCedule SQL select error: ' . $err['message'] . ' SQL: ' . $this->db->getLastQuery());
                        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Erreur SQL (select): ' . $err['message']]));
                        return;
                    }
    
                    $count = (int) $result->getRow()->count;
                    if ($count > 0) {
                        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Cette polyline est déjà sélectionnée']));
                        return;
                    }
    
                    $strSQL = "SELECT IFNULL(MAX(ordre), 0) as maxordre 
                           FROM cedule_polylines 
                           WHERE idcedule = " . $this->db->escape($idcedule);
                    $result = $this->db->query($strSQL);
                    if (!$result) {
                        $err = $this->db->error();
                        log_message('error', 'togglePolylineCedule SQL maxordre error: ' . $err['message'] . ' SQL: ' . $this->db->getLastQuery());
                        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Erreur SQL (maxordre): ' . $err['message']]));
                        return;
                    }
    
                    $maxOrdre = (int) $result->getRow()->maxordre;
                    $data = [
                        'idcedule' => (int) $idcedule,
                        'idpolyline' => (int) $idpolyline,
                        'ordre' => (int) ($maxOrdre + 1)
                    ];
    
                    $inserted = $this->db->table('cedule_polylines')->insert($data);
                    if (!$inserted) {
                        $err = $this->db->error();
                        log_message('error', 'togglePolylineCedule insertion error: ' . $err['message'] . ' SQL: ' . $this->db->getLastQuery());
                        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Erreur insertion: ' . $err['message']]));
                        return;
                    }
                    log_message('debug', 'togglePolylineCedule: polyline ajoutée', $data);
                } else {
                    $this->db->where('idcedule', (int) $idcedule);
                    $this->db->table('cedule_polylines')->where('idpolyline', (int) $idpolyline)->delete();
                    $affected = $this->db->affectedRows();
                    if ($affected === 0) {
                        log_message('warning', 'togglePolylineCedule: suppression a affect_rows = 0. SQL: ' . $this->db->getLastQuery());
                        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Aucune ligne supprimée (déjà supprimée ?)']));
                        return;
                    }
                    log_message('debug', 'togglePolylineCedule: polyline supprimée - affected_rows: ' . $affected);
                }
    
                // Mettre à jour la polyline principale de la cédule
                $this->updateMainPolyline($idcedule);
    
                $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true]));
            } catch (Exception $e) {
                log_message('error', 'togglePolylineCedule exception: ' . $e->getMessage());
                $this->output
                    ->set_status_header(500)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['ok' => false, 'error' => 'Exception serveur', 'message' => $e->getMessage()]));
            }
    }


    public function getPolylineCheckboxes()
    {    
            $idville = $this->input->get('idville');
            $idsecteur = $this->input->get('idsecteur');
            $idcedule = $this->input->get('idcedule');
    
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->GetPolylineCheckboxes($idville, $idsecteur, $idcedule);
    }


    public function getSelectedPolylines()
    {    
            $idcedule = $this->input->get('idcedule');
            $idcedule = (int) $idcedule;
    
            if ($idcedule <= 0) {
                echo json_encode(['ok' => false, 'error' => 'ID cédule invalide']);
                return;
            }
    
            // Joindre polilyne_ville pour obtenir les parcours (parcours -> coords)
            $strSQL = "SELECT cp.id AS assoc_id, pv.id AS id, pv.nom, pv.parcours, pv.idville, v.Nom as ville_nom
                   FROM cedule_polylines cp
                   INNER JOIN polilyne_ville pv ON cp.idpolyline = pv.id
                   LEFT JOIN ville v ON pv.idville = v.id
                   WHERE cp.idcedule = " . $this->db->escape($idcedule) . "
                   ORDER BY cp.ordre";
            $query = $this->db->query($strSQL);
            $polylines = [];
    
            foreach ($query->getResult() as $row) {
                $coords = [];
    
                if (!empty($row->parcours)) {
                    $points = explode('_', $row->parcours);
                    foreach ($points as $point) {
                        $point = trim($point);
                        if ($point === '')
                            continue;
                        $latLng = explode(',', $point);
                        if (count($latLng) === 2) {
                            $lat = (float) trim($latLng[0]);
                            $lng = (float) trim($latLng[1]);
                            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                                $coords[] = [$lat, $lng];
                            }
                        }
                    }
                }
    
                // Ne renvoyer que si on a des coordonnées valides
                if (count($coords) > 0) {
                    $polylines[] = [
                        'assoc_id' => (int) $row->assoc_id,
                        'id' => (int) $row->id,
                        'nom' => $row->nom,
                        'ville' => $row->ville_nom,
                        'coords' => $coords
                    ];
                }
            }
            header('Content-Type: application/json');
            echo json_encode([
                'ok' => true,
                'polylines' => $polylines
            ]);
    }


    public function clearAllPolylines()
    {    
            $idcedule = $this->input->post('idcedule');
    
            if (!$idcedule) {
                echo json_encode(['ok' => false, 'error' => 'ID cédule manquant']);
                return;
            }
    
            $this->db->table('cedule_polylines')->where('idcedule', $idcedule)->delete();
    
            echo json_encode(['ok' => true]);
    }


    public function removePolyline($id)
    {    
            if (!$id) {
                echo 'ERROR: ID manquant';
                return;
            }
    
            $id = $this->db->escape($id);
            $strSQL = "DELETE FROM cedule_polylines WHERE id=$id";
            $this->db->query($strSQL);
            $data['result'] = 'ok';
            echo json_encode($data);
    }


    public function removeSctPolyline($id)
    {    
            if (!$id) {
                echo json_encode(['ok' => false, 'error' => 'ID manquant']);
                return;
            }
            $strSQL = "DELETE FROM polilyne_ville WHERE id=$id";
            $this->db->query($strSQL);
            $data['result'] = 'ok';
            echo json_encode($data);
    }


    public function movePolylineToFirst()
    {    
            $assocId = $this->input->post('id');
            if (!$assocId || !ctype_digit(strval($assocId))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Paramètre manquant ou invalide']));
                return;
            }
    
            $this->load->model('Cedules_model', 'cedules');
            $ok = $this->cedules->MovePolylineToFirstByAssocId($assocId);
    
            if ($ok) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true]));
            } else {
                $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'error' => 'Impossible de repositionner.']));
            }
    }


    public function getPolylineGeoJsonById($idpolyline)
    {    
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            $idpolyline = (int) $idpolyline;
            if ($idpolyline <= 0) {
                echo json_encode(['ok' => false, 'error' => 'ID polyline invalide']);
                return;
            }
    
            $strSQL = "SELECT p.id, p.nom, p.parcours, p.idville, p.idsecteur,
                          v.Nom as ville_nom, s.Nom as secteur_nom
                   FROM polilyne_ville p
                   LEFT JOIN ville v ON p.idville = v.id
                   LEFT JOIN secteurs s ON p.idsecteur = s.id
                   WHERE p.id = " . $this->db->escape($idpolyline) . " LIMIT 1";
    
            $query = $this->db->query($strSQL);
            if ($query->getNumRows() === 0) {
                echo json_encode(['ok' => false, 'error' => 'Parcours introuvable']);
                return;
            }
    
            $row = $query->getRow();
            $coords = [];
    
            if (!empty($row->parcours)) {
                $points = explode('_', $row->parcours);
                foreach ($points as $point) {
                    $point = trim($point);
                    if ($point === '')
                        continue;
                    $latLng = explode(',', $point);
                    if (count($latLng) === 2) {
                        $lat = (float) trim($latLng[0]);
                        $lng = (float) trim($latLng[1]);
                        if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                            $coords[] = [$lat, $lng];
                        }
                    }
                }
            }
    
            header('Content-Type: application/json');
            echo json_encode([
                'ok' => true,
                'polyline' => [
                    'id' => (int) $row->id,
                    'nom' => $row->nom,
                    'idville' => (int) $row->idville,
                    'idsecteur' => (int) $row->idsecteur,
                    'ville_nom' => $row->ville_nom,
                    'secteur_nom' => $row->secteur_nom,
                    'coords' => $coords
                ]
            ]);
    }


    public function Requete($id)
    {    
            $from = $this->input->post_get('from', TRUE);
            $this->load->model("Requetes_model", "requetes");
            $data = $this->requetes->getRequete($id);
            $data['msgerror'] = $this->message_error;
            $data['retour'] = ($from == 0) ? '/sanivan/aujourdhui/1/'.date("Y-m-d") : '/villes/liste/requetes';
            switch ($from) {
                case 0:
                    $data['retour'] = '/sanivan/aujourdhui/1/'.date("Y-m-d");
                    break;
                case 1:
                    $data['retour'] = '/villes/liste/requetes';
                    break;
                case 2:
                    $data['retour'] = '/villes/requete/liste';
                    break;
            }
            $data['menu'] = view('sections/inc_menu_2', $data);
            if ($id != 0) {
                $data['optMatiereStat'] = $this->requetes->getOptDechets();
                echo view('requetes/requete_reception', $data);
            } else {
                echo view('requetes/requete_ville', $data);
            }
    }


    public function postRequete()
    {    
            $this->load->model("Requetes_model", "requetes");
            $data = $this->requetes->postRequete();
            $this->message_error = $data['msgerr'];
            $this->Requete($data['id']);
    }


    public function getNomSecteur($idsecteur)
    {    
            //--------------------------------------------------------------
            $nom = '';
            $idsecteur = $this->db->escape($idsecteur);
            $strSQL = "SELECT * FROM  ville  WHERE  id = $idsecteur";
            $result = $this->db->query($strSQL);
            foreach ($result->getResult() as $row) {
                $nom = $row->Nom;
            }
            echo $nom;
    }


    public function setConfirmePresence()
    {    
            /** @var string  $strSQL */
            /** @var array  $data */
            $data = array();
            $chaufresp = $this->input->post_get('chaufresp');
            if (trim($chaufresp) != '') {
                $arrConfirme = explode('_', $chaufresp);
                $id = $this->db->escape($arrConfirme[0]);
                $idrh = $this->db->escape($arrConfirme[1]);
                $ActiveDate = $arrConfirme[2];
                $strSQL = "UPDATE cedules
    											SET
    												chaufconfirme = 1
    											WHERE ID = ".$id;
                $this->db->query($strSQL);
            }
            $aideresp = $this->input->post_get('aideresp');
            if (trim($aideresp) != '') {
                $arrConfirme = explode('_', $aideresp);
                $id = $this->db->escape($arrConfirme[0]);
                $idrh = $this->db->escape($arrConfirme[1]);
                $ActiveDate = $arrConfirme[2];
                $strSQL = "UPDATE cedules
    											SET
    												helperconfirme = 1
    											WHERE ID = ".$id;
                $this->db->query($strSQL);
            }
            $strSQL = "Select CONCAT (Prenom, ' ', Nom ) as NomComplet FROM reshumaine where id = $idrh";
            $result = $this->db->query($strSQL);
            foreach ($result->getResultArray() as $row) {
                $data = $row;
            }
            $data['ActiveDate'] = $ActiveDate;
            echo view('confirme/confirme', $data);
    }


    public function sendConfirm()
    {    
            /** @var string  $strSQL */
            /** @var string  $nomConfChauf */
            $nomConfChauf = '';
            /** @var string  $nomConfAide */
            $nomConfAide = '';		/** @var array  $data */
            $data = array();
            /** @var string  $strWhere */
            $strWhere = '';
            $idcedule = $this->input->post_get('idcedule');
            $typecedule = $this->input->post_get('typecedule');
            $dteenvoiesc = (($this->input->post_get('dteenvoi') == '') ? ' CURDATE() ' : $this->db->escape($this->input->post_get('dteenvoi')));
            $dteenvoi = (($this->input->post_get('dteenvoi') == '') ? ' CURDATE() ' : $this->input->post_get('dteenvoi'));
            $nonConfirme = $this->input->post_get('nonconfirme');
            if ($nonConfirme == 1) {
                $nomConfAide = ' and helperconfirme = 0';
                $nomConfChauf = ' and chaufconfirme = 0';
            }
            $note = $this->input->post_get('note');
            if (trim($note) != '') {
                $note = '<br /><b>PRENEZ AVIS DE:</b> <br />'.str_replace('%20', ' ', $note);
            }
            if (is_numeric($idcedule)) {
                $strWhere .= " AND c.ID = $idcedule ";
            }
            if (is_numeric($typecedule)) {
                $strWhere .= " AND c.TypeCedule = $typecedule ";
            }
            $html = '';
            /** @var string $strSQL */
            $strSQL = "SELECT c.ID, c.NOCAMION,c.HELPERS_1, c.idchauffeurs, $dteenvoiesc as DayCedule, c.chaufconfirme, c.helperconfirme,
                                ( Select CONCAT (Prenom, ' ', Nom ) FROM reshumaine where id = c.idchauffeurs ) As chauffeur,
                                ( Select email FROM reshumaine where id = c.idchauffeurs ) As mailchauffeur,
                                ( Select CONCAT (Prenom, ' ', Nom ) FROM reshumaine where id = c.HELPERS_1 ) As aide1,
                                ( Select email FROM reshumaine where id = c.HELPERS_1 ) As mailaide1,
                                IFNULL(( Select Nom FROM ville where id = g.idvilles ), ' ')  As nomville,Secteur,
                                (SELECT CONCAT(suiviscamion.TypeDechet, '<br>', suiviscamion.note)   FROM suiviscamion WHERE suiviscamion.nocamion = c.NOCAMION
    												     AND  suiviscamion.dateend is NULL AND  suiviscamion.datestart <= $dteenvoiesc
                                   AND suiviscamion.idstatut = 2 ) AS 'dechet'
                  FROM grp_cedules g  LEFT JOIN cedules c  ON        c.idgrp = g.id
                            WHERE g.datejour = $dteenvoiesc $strWhere
                            AND c.idchauffeurs > 0 and c.NOCAMION <> '' and (IsStart = 0) Order by  g.ARow, g.id, c.ID";
            $res = $this->db->query($strSQL);
            foreach ($res->getResultArray() as $row) {
                //  Courriel au Chauffeur
                if ((trim($row['mailchauffeur']) != '') and ($row['chaufconfirme'] == 0)) {
                    $lienconfirmation = '<a href="https://www.robert-daoust.ca/envoi/confirm?chaufresp='.
                        $row['ID'].'_'.$row['idchauffeurs'].'_'.$row['DayCedule'].'" > Comfirmer votre présence</a>';
                    $html = sprintf(MSG, $row['chauffeur'], $row['nomville'],
                        ((trim($row['Secteur']) != '') ? ', Votre Secteur: '.$row['Secteur'] : ''),
                        $row['NOCAMION'], '', '').((trim($row['dechet']) != '') ? ' Camion avec déchet: '.$row['dechet'] : '').$note;
                    //---------------------------------------------------------
                    $this->load->library('email');
                    $this->email->from('nepasrepondre@robert-daoust.ca');
                    $this->email->to($row['mailchauffeur']);
                    $this->email->subject("Votre cédule pour le ".$row['DayCedule']);
                    $html = sprintf(MSG, $row['chauffeur'], $row['nomville'],
                        ((trim($row['Secteur']) != '') ? ', Votre Secteur: '.$row['Secteur'] : ''),
                        $row['NOCAMION'], '', '').((trim($row['dechet']) != '') ? ' Camion avec déchet: '.$row['dechet'] : '').$note;
                    $this->email->message($html);
                    $this->email->priority = 1;
                    $this->email->send();
                    //--------------------------------------
                    //$strSQL = "UPDATE cedules
                    //    SET
                    //      chaufconfirme = 1
                    //    WHERE ID = " . $row['ID'];
                    // $this->db->query($strSQL);
                    //---------------------------------------------------------
                }
    
                if ((trim($row['aide1']) != '') and (trim($row['mailaide1']) != '') and ($row['helperconfirme'] == 0)) {
                    $lienconfirmation = '<a href="https://www.robert-daoust.ca/envoi/confirm?aideresp='.
                        $row['ID'].'_'.$row['HELPERS_1'].'_'.$row['DayCedule'].'" > Comfirmer votre présence<"a>';
                    $html = sprintf(MSG_AIDE, $row['aide1'], $row['nomville'],
                        ((trim($row['Secteur']) != '') ? ', Votre Secteur: '.$row['Secteur'] : ''), $row['NOCAMION'], '', '');
                    $this->email->from('nepasrepondre@robert-daoust.ca');
                    $this->email->to($row['mailaide1']);
                    $this->email->subject("Votre cédule pour le ".$row['DayCedule']);
                    $html = sprintf(MSG, $row['aide1'], $row['nomville'],
                        '', '', '', '');
                    $this->email->message($html);
                    $this->email->priority = 1;
                    $this->email->send();
                    //--------------------------------------
                    //try {
    
                    //  $strSQL = "UPDATE cedules
                    //              SET
                    //                helperconfirme = 1
                    //              WHERE ID = " . $row['ID'];
                    //  $this->db->query($strSQL);
                    //}
                    //catch (Exception $e) {
                    //  //echo 'Exception reçue : ',  $e->getMessage(), "\n";
                    //}
                }
            }
            echo ('Votre demande d\'envoi a été complétée.');
    }


    public function expCeduleToExcel($dateactive)
    {    
            $this->load->model("Excel_model", "excel");
            $this->excel->LoadToday($dateactive);
    }


    public function expHresAidesToExcel($dateactive)
    {    
            $strSQL = '';
            $this->load->model("Excel_model", "excel");
            $this->excel->getHresAides($dateactive, $strSQL);
    }


    public function resetPassW()
    {    
            $datausager['email'] = $this->session->userdata('email');
            $datausager['from'] = 0;
            echo view('partials/chgpassw', $datausager);
    }


    public function ValidOldPassW()
    {    
            $this->load->model("login_model", "login");
            /** @var array $data */
    
            $data = [
                'email' => urldecode($this->input->post_get('email', TRUE)),
                'password' => urldecode($this->input->post_get('password', TRUE))
            ];
            /** @var object $result */
            $result = new \stdClass();
            $result = $this->login->validate_user($data);
            if (isset($result)) {
                echo 1;
            } else {
                echo 0;
            }
    }


    public function PostresetPassW()
    {    
            $this->load->model("login_model", "login");
            $valPass = urldecode($this->input->post_get('passnew', TRUE));
            $pwd_peppered = hash_hmac("sha256", $valPass, $this->login->keysha);
            $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);
            $valPass = 'pass='.$this->db->escape($pwd_hashed);
            $email = $this->db->escape(urldecode($this->input->post_get('email', TRUE)));
            $strSQL = "UPDATE webuser
                      SET
                        forcechange=0,
                        $valPass
                      WHERE email = $email";
            $this->db->query($strSQL);
            $this->load->model("Cedules_model", "cedules");
            $date = date_create(date('Y-m-d'));
            $ActiveDate = $date->format('Y-m-d');
            $this->showToday(1, $ActiveDate);
    }


    public function ValidStrongPassW()
    {    
            $password = urldecode($this->input->post_get('password', TRUE));
            // Validate password strength
            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);
    
            if (! $uppercase || ! $lowercase || ! $number || ! $specialChars || strlen($password) < 8) {
                echo 0;
            } else {
                echo 1;
            }
    }


    public function sendPassword()
    {    
            $rep = ci3lang('send_pass_reponse_err');
            $email = $this->db->escape($this->input->post_get('email', TRUE));
            $sql = "SELECT CONCAT(prenom, ' ', nom) AS NomComplet, email, pass, id_user FROM webuser WHERE email = $email";
            if ($user = $this->db->query($sql)->getRow(0)) {
                $data['nom'] = $user->NomComplet;
                $data['password'] = '<a href="https://www.robert-daoust.ca/login/confirmeinfo?specco='.$user->pass.'&no='.$user->id_user.date('is').'">Cliquer ici pour r&eacute;initialiser votre mot de passe</a>';
    
                $html = view('partials/password', $data);
                $this->load->library('email');
                $this->email->from('nepasrepondre@robert-daoust.ca');
                $this->email->to($user->email);
                $this->email->subject('Robert Daoust: information demandée.');
                $this->email->message($html);
                $this->email->priority = 1;
                $this->email->send();
                try {
                    //--------------------------------------
                    $id_user = $user->id_user;
                    $sql = "UPDATE webuser
                        SET
                          timetoreset=now()
                        WHERE id_user = $id_user";
                    $this->db->query($sql);
                    //--------------------------------------
                    $rep = 'Vos accès vous ont éé envoyés.';
                } catch (Exception $e) {
                    $rep = utf8_encode('Exception reçue : ').$e->getMessage();
                }
            }
            $data = array();
            $data['msgerror'] = '';
            $data['msgreset'] = $rep;
            echo view('partials/login2', $data);
    }


    public function excRestPassw()
    {    
            $datausager['showerr'] = '';
            $passw = $this->input->post_get('specco', TRUE);
            $id_user = $this->db->escape(substr($this->input->post_get('no', TRUE), 0, -4));
            $sql = "SELECT pass, id_user, timetoreset, email  FROM webuser WHERE id_user = $id_user";
            if ($user = $this->db->query($sql)->getRow(0)) {
                if ($passw == $user->pass) {
                    //----------------------------------------
                    $time1 = new \DateTime($user->timetoreset);
                    $time2 = new \DateTime(date('Y-m-d H:i:s'));
                    $timediff = $time1->diff($time2);
                    $diffhre = $timediff->format('%h');
                    $diffmin = $timediff->format('%i');
                    $diff = $diffmin;
                    if ($diffhre > 0)
                        $diff = 60 + $diffmin;
                    if (intval($diff) > 15) {
                        $datausager['showerr'] = 'Le d&eacute;lai pour la r&eacute;initialisation est expir&eacute; ou l\'information de validation est invalide.';
                        $datausager['menu'] = view('partials/menu_motpasse', $datausager);
                        echo view('partials/confirmation', $datausager);
                    } else {
                        $datausager['email'] = $user->email;
                        $datausager['from'] = 2;
                        echo view('partials/chgpassw', $datausager);
                    }
                    //----------------------------------------
                }
            }
    }


    public function getSuivisCamExist($id)
    {    
            $suivis = '';
            $id = $this->db->escape($id);
            $strSQL = "SELECT suiviscamion.*, camions.TypeCamion, typecamion.Nom,
                        left(suiviscamion.datestart, 10) as startStr FROM suiviscamion
    		                INNER JOIN camions ON suiviscamion.nocamion = camions.NoCamion
    		                INNER JOIN typecamion ON camions.TypeCamion = typecamion.id
                    WHERE suiviscamion.id = $id";
            $suivis = $this->db->query($strSQL);
            if ($suivis->getNumRows() == 0) {
                echo '1';
            } else {
                foreach ($suivis->getResult() as $row) {
                    echo json_encode($row);
                }
            }
    }


    public function getCalendrier()
    {    
            $valtofind = '';
            $typrh = $this->input->post_get('typerh');
            if (is_numeric($typrh)) {
                $typrh = ' AND reshumaine.idreshum = '.$typrh;
            } else {
                $typrh = '';
            }
            $typrhdrop = $this->input->post_get('typerhdrop');
            if (! is_numeric($typrhdrop)) {
                $typrhdrop = 1;
            }
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND (reshumaine.Nom LIKE $valtofind OR reshumaine.Prenom LIKE $valtofind OR reshumaine.email LIKE $valtofind)";
            }
            $this->load->model("Calendrier_model", "calendrier");
            $absence = (($this->input->post_get('absence') == 'all') ? '' : $this->input->post_get('absence'));
            if ($absence != '') {
                $valtofind .= " AND idabsence = ".$this->db->escape($absence);
            }
            $valtofind .= $typrh;
            /** @var array  $data */
            $data = $this->calendrier->getSuivis($valtofind);
            $data['absence'] = $absence;
            $data['typeabsences'] = $this->cedules->GetTypeAbsences($absence);
            $data['updrh'] = $this->cedules->GetTypeRH($this->input->post_get('typerh'));
            $typechauf = 1;
            if (is_numeric($this->input->post_get('typechauf'))) {
                $typechauf = $this->input->post_get('typechauf');
            }
            $data['typerh'] = $this->cedules->GetTypeRH($typechauf);
            $data['ChauffeurDrop'] = $this->calendrier->GetChauffeursToDrop('1', $this->valtofinddrop);
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['mode'] = '';
            $data['base_url'] = ci3_url();
            $data['html'] = '';
            $data['nbshowevent'] = 0;
            echo view('partials/calendrier', $data);
    }


    public function getRHCal(...$args)
    {
        return $this->toCi3();
    }

    public function getCalendrierCam()
    {    
            $valtofind = '';
            $this->load->model("Cedules_model", "cedules");
            $this->load->model("Calendrier_model", "calendrier");
            $this->load->model("Creationcd_model", "creationcd");
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND camions.nocamion LIKE $valtofind  ";
            }
            $typecam = $this->input->post_get('typecam');
            if (is_numeric($typecam)) {
                $valtofind .= ' AND camions.TypeCamion = '.$this->db->escape($typecam);
            }
            $statut = (($this->input->post_get('statut') == 'all') ? '' : $this->input->post_get('statut'));
            if ($statut != '') {
                $valtofind .= " AND idstatut = ".$this->db->escape($statut);
            }
            /** @var array  $data */
            $data = $this->calendrier->getSuivisCam($valtofind);
            $data['typededechet'] = $this->cedules->GetAllTypeProduits();
            $data['nbshowcam'] = $this->input->post_get('nbshow');
            $data['updcamion'] = $this->cedules->GetTypeCamion(((($typecam == '') or ($typecam == 'all')) ? '1' : $typecam));
            $data['CamionDrop'] = $this->calendrier->GetDropCamions(((($typecam == '') or ($typecam == 'all')) ? '1' : $typecam));
            $data['statut'] = $this->input->post_get('statut');
            $data['typecam'] = $this->input->post_get('typecam');
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
            $data['mode'] = '';
            $data['base_url'] = ci3_url();
            $data['html'] = '';
            echo view('partials/calendrier_cam', $data);
    }


    public function getCamCal(...$args)
    {
        return $this->toCi3();
    }

    public function makeCamPDF()
    {    
            $valtofind = '';
            $this->load->model("Cedules_model", "cedules");
            $this->load->model("Calendrier_model", "calendrier");
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND nocamion LIKE $valtofind  ";
            }
            $typecam = $this->input->post_get('typecam');
            if (is_numeric($typecam)) {
                $valtofind = ' AND camions.TypeCamion = '.$this->db->escape($typecam);
                $typecamion = (($typecam == 1) ? 'REAR END' : 'ROBOT');
            } else {
                $typecamion = 'REAR END et ROBOT';
            }
            $statut = (($this->input->post_get('statut') == 'all') ? '' : $this->input->post_get('statut'));
            if ($statut != '') {
                $valtofind .= " AND idstatut = ".$this->db->escape($statut);
            }
            /** @var array  $data */
            $dateEnd = '';
            $date = $this->db->escape(date('Y-m-d'));
            $returnval = '';
            $activedate = $this->input->get_post('dtedeb');
            $dateend = $this->input->get_post('dtefin');
            $this->data['ActiveDate'] = $this->input->get_post('dtedeb');
            $this->data['dateend'] = $this->input->get_post('dtefin');
            if (! isset($activedate)) {
                $date = new \DateTime(date('Y-m-d'));
                date_sub($date, date_interval_create_from_date_string("1 year"));
                $this->data['ActiveDate'] = date_format($date, "Y-m-d");
            }
            if (! isset($dateend)) {
                $date = new \DateTime(date('Y-m-d'));
                date_add($date, date_interval_create_from_date_string("1 year"));
                $this->data['dateend'] = date_format($date, "Y-m-d");
            }
            $arrDateFields = explode('-', $this->data['ActiveDate']);
            $this->data['ActiveDate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
            $arrDateFields = explode('-', date('Y-m-d'));
            $this->data['caldate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
            $date = ' >= '.$this->db->escape($this->data['ActiveDate']).' and ( dateend > CURDATE() or dateend is null) ';
            $strSQL = "SELECT suiviscamion.*,
    	              CASE
                      WHEN idstatut = 1 THEN 'Hors service'
                      WHEN idstatut = 2 THEN 'Avec Mat&eacute;riel: '
                      WHEN idstatut = 3 THEN 'EN bris de service'
                      ELSE ''
    	              END AS statut, left(datestart, 10) as startStr, left(dateend, 10) as dateend,
    	              if(dateretourprevu is null, '',left(dateretourprevu, 10)) as endStr,
    	              CASE
                      WHEN idstatut = 1 THEN 'darkcyan'
                      WHEN idstatut = 2 THEN 'chocolate'
                      ELSE ''
    	              END AS backcolor
                  FROM suiviscamion  INNER JOIN camions ON suiviscamion.nocamion = camions.NoCamion
                  WHERE datestart  $date $valtofind order by datestart";
            $cal = $this->db->query($strSQL);
            $statut = '';
            //-------------------------------------------------------------------------
            switch ($this->input->get_post('printwhere')) {
                case 1:
                    /** @var array $data */
                    foreach ($cal->getResult() as $row) {
                        if ($row->idstatut == 2) {
                            $statut = $row->statut.$row->TypeDechet;
                        } else {
                            $statut = utf8_encode($row->statut);
                        }
                        $data[] = array($row->nocamion, $row->startStr, $row->dateend, $statut, utf8_encode($row->note));
                    }
                    chdir(rtrim((string) env('ci3.webroot'), '/\\')); require_once rtrim((string) env('ci3.webroot'), '/\\') . '/application/controllers/pdf/libcalendrier.php';
                    $pdf = new \PDF('L');
                    // Titres des colonnes
                    $header = array('Camions', utf8_decode('Début'), 'Fin', 'Statuts', 'Notes');
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->Titre = 'Calendrier des Camions '.$typecamion;
                    $pdf->headerCol = $header;
                    $pdf->from = 'CAM';
                    $pdf->AddPage();
                    //$pdf->HeaderBol('Calendrier des Camions '.$typecamion);
                    $pdf->FancyTable($data);
                    $filename = './upload/calendrier.pdf';
                    $pdf->Output($filename, 'F');
                    //---------------------------------------------------------------------
                    force_download($filename, NULL);
                    delete_files($filename);
                    $this->getCalendrierCam();
                    break;
                case 2:
                    /** @var string $mois */
                    $mois = '';
                    $dtedeb = $this->db->escape($this->input->get_post('dtedeb'));
                    $dtefin = $this->db->escape($this->input->get_post('dtefin'));
                    /** @var string $returnvall */
                    $returnvall = '';
                    /** @var string $strSQL */
                    $strSQL = "SELECT DateActivite, DateEnd, Heure, HreEnd, Titre, Description,  DATE_FORMAT(DateActivite, '%w') as calDay,
                          DATE_FORMAT(DateActivite, '%e') as dayMonth,DATE_FORMAT(DateActivite, '%c') as Mois, left(DateActivite, 4) as Annee,
    	                      CONCAT(DateActivite, ' ', Heure) AS DateHeure FROM tbl_activites WHERE DateActivite
                              BETWEEN curdate() AND $dtefin ORDER BY DateActivite, Heure";
                    $cal = $this->db->query($strSQL);
                    $returnvall .= '   <div class="row" style="margin-top:20px;"><div class="col-lg-12  box box-info"><table class="table-striped" border="1" width="1400"> <thead>
    
                        <tr style="text-align:right;">
    
                          <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;font-size:24px;" align="center">Heure</td>
                          <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;font-size:24px;" align="center">Activit&eacute;s</td>
                          <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;font-size:24px;" align="center">Description</td>
                        </tr>
                      </thead>
                      <tbody id="tboProd">';
                    foreach ($cal->getResult() as $row) {
                        if ($mois != $row->Mois) {
                            $returnvall .= '                    <tr>
                          <td colspan="3" style="text-align : center;font-weight: bold;color: white;line-height:35px;font-size: 28px;background-image:url(/assets/00_images/background_month_2.png);background-size: 100% 100%;" align="center">
                            '.$this->Mois[$row->Mois - 1].' '.$row->Annee.'
                          </td>
                        </tr>';
                            $mois = $row->Mois;
                        }
                        if ($activeDate != $row->DateActivite) {
                            $returnvall .= '<tr>
                                 <td colspan="3" align="Center" style="width:100%;font-size:28px;background-image:url(/assets/00_images/background_separateur_2.png);background-size: 100% 100%;font-weight:bold;line-height:35px;color: white;"><label>'.$this->Jour[$row->calDay].' le '.$row->dayMonth.' '.$this->Mois[$row->Mois - 1].'</label></td>
                         </tr>';
                            $activeDate = $row->DateActivite;
                        }
                        $returnvall .= '<tr>
                                 <td valign="top" align="left" style="width:80px;font-size:24px;line-height:30px;"><label>'.$row->Heure.'</label></td>
                                <td valign="top" align="left" style="width:350px;font-size:24px;line-height:30px;"><label ><b>'.$row->Titre.'</b></label></td>
    
                                <td valign="top"  align="left"><div style="width:800px"white-space:normal;font-size:x-large;line-height:30px;>'.$row->Description.'</div></td>
                         </tr>';
                    }
                    //-------------------------------------------------------------------------
                    $data['listactivites'] = $returnvall.'</tbody></table></div></div>';
                    $data['mois'] = $mois;
                    echo view('pages/print_calendrier', $data);
                    //-------------------------------------------------------------------
                    break;
            }
    
            //-------------------------------------------------------------------
    }


    public function makeRhPDF()
    {    
            $valtofind = '';
            $this->load->model("Cedules_model", "cedules");
            $this->load->model("Calendrier_model", "calendrier");
            $valtofind = '';
            $typrh = $this->input->post_get('typerh');
            if (is_numeric($typrh)) {
                $typrh = ' AND reshumaine.idreshum = '.$typrh;
            } else {
                $typrh = '';
            }
            $typrhdrop = $this->input->post_get('typerhdrop');
            if (! is_numeric($typrhdrop)) {
                $typrhdrop = 1;
            }
            if ($this->input->post_get('valtofind') == "") {
                $valtofind = "";
            } else {
                $valtofind = $this->db->escape('%'.$this->input->post_get('valtofind').'%');
                $valtofind = " AND (reshumaine.Nom LIKE $valtofind OR reshumaine.Prenom LIKE $valtofind OR reshumaine.email LIKE $valtofind)";
            }
            $this->load->model("Calendrier_model", "calendrier");
            $absence = (($this->input->post_get('absence') == 'all') ? '' : $this->input->post_get('absence'));
            if ($absence != '') {
                $valtofind .= " AND idabsence = ".$this->db->escape($absence);
            }
            $valtofind .= $typrh;
            $dateEnd = '';
            $dteprev = '';
            $date = $this->db->escape(date('Y-m-d'));
            $returnval = '';
            $activedate = $this->input->get_post('dtedeb');
            $dateend = $this->input->get_post('dtefin');
            $this->data['ActiveDate'] = $this->input->get_post('dtedeb');
            $this->data['dateend'] = $this->input->get_post('dtefin');
            if (! isset($activedate)) {
                $date = new \DateTime(date('Y-m-d'));
                date_sub($date, date_interval_create_from_date_string("1 year"));
                $this->data['ActiveDate'] = date_format($date, "Y-m-d");
            }
            if (! isset($dateend)) {
                $date = new \DateTime(date('Y-m-d'));
                date_add($date, date_interval_create_from_date_string("1 year"));
                $this->data['dateend'] = date_format($date, "Y-m-d");
            }
            $arrDateFields = explode('-', $this->data['ActiveDate']);
            $this->data['ActiveDate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
            $arrDateFields = explode('-', date('Y-m-d'));
            $this->data['caldate'] = $arrDateFields[0].'-'.$arrDateFields[1].'-01';
            $date = ' >= '.$this->db->escape($this->data['ActiveDate']).' and ( dateend > CURDATE() or dateend is null)';
            $strSQL = "SELECT suivisrh.id, CONCAT( reshumaine.Prenom, ' ', reshumaine.Nom ) AS  NomComplet,
                            typeabsence.Nom  AS absence, left(datestart, 10) as datestart,
                         ifnull(avistoshow, '') AS Description, if(dateretourprevu is null, '',left(dateretourprevu, 10)) as endStr,typeabsence.couleur  AS backcolor,typeabsence.textcolor  AS textcolor,
                         suivisrh.dateend, suivisrh.note, suivisrh.dateretourprevu, suivisrh.showavis, suivisrh.avistoshow, suivisrh.idabsence, suivisrh.idrh
    	                    FROM suivisrh INNER JOIN  reshumaine ON suivisrh.idrh = reshumaine.id
    							  INNER JOIN typeabsence ON suivisrh.idabsence = typeabsence.id  WHERE suivisrh.datestart  $date $valtofind order by suivisrh.datestart";
            /** @var object $cal */
            $cal = $this->db->query($strSQL);
            $statut = '';
            //-------------------------------------------------------------------------
            switch ($this->input->get_post('printwhere')) {
                case 1:
                    /** @var array $data */
                    foreach ($cal->getResult() as $row) {
                        $data[] = array(utf8_decode($row->NomComplet), $row->datestart, $row->endStr, $row->absence, $row->note);
                    }
                    chdir(rtrim((string) env('ci3.webroot'), '/\\')); require_once rtrim((string) env('ci3.webroot'), '/\\') . '/application/controllers/pdf/libcalendrier.php';
                    $pdf = new \PDF('L');
                    // Titres des colonnes
                    $header = array('Nom', utf8_decode('Début'), 'Fin', 'Absence', 'Notes');
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->Titre = 'Calendrier des Ressources humaines';
                    $pdf->headerCol = $header;
                    $pdf->from = 'RH';
                    $pdf->AddPage();
                    //$pdf->HeaderBol('Calendrier des Ressources humaines');
                    $pdf->FancyTable($data);
                    $filename = './upload/calendrier.pdf';
                    $pdf->Output($filename, 'F');
                    //---------------------------------------------------------------------
                    force_download($filename, NULL);
                    delete_files($filename);
                    $this->getCalendrierCam();
                    break;
                case 2:
                    /** @var string $mois */
                    $mois = '';
                    $dtedeb = $this->db->escape($this->input->get_post('dtedeb'));
                    $dtefin = $this->db->escape($this->input->get_post('dtefin'));
                    /** @var string $returnvall */
                    $returnvall = '';
                    /** @var string $strSQL */
                    $strSQL = "SELECT DateActivite, DateEnd, Heure, HreEnd, Titre, Description,  DATE_FORMAT(DateActivite, '%w') as calDay,
                          DATE_FORMAT(DateActivite, '%e') as dayMonth,DATE_FORMAT(DateActivite, '%c') as Mois, left(DateActivite, 4) as Annee,
    	                      CONCAT(DateActivite, ' ', Heure) AS DateHeure FROM tbl_activites WHERE DateActivite
                              BETWEEN curdate() AND $dtefin ORDER BY DateActivite, Heure";
                    $cal = $this->db->query($strSQL);
                    $returnvall .= '   <div class="row" style="margin-top:20px;"><div class="col-lg-12  box box-info"><table class="table-striped" border="1" width="1400"> <thead>
    
                        <tr style="text-align:right;">
    
                          <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;font-size:24px;" align="center">Heure</td>
                          <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;font-size:24px;" align="center">Activit&eacute;s</td>
                          <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;font-size:24px;" align="center">Description</td>
                        </tr>
                      </thead>
                      <tbody id="tboProd">';
                    foreach ($cal->getResult() as $row) {
                        if ($mois != $row->Mois) {
                            $returnvall .= '                    <tr>
                          <td colspan="3" style="text-align : center;font-weight: bold;color: white;line-height:35px;font-size: 28px;background-image:url(/assets/00_images/background_month_2.png);background-size: 100% 100%;" align="center">
                            '.$this->Mois[$row->Mois - 1].' '.$row->Annee.'
                          </td>
                        </tr>';
                            $mois = $row->Mois;
                        }
                        if ($activeDate != $row->DateActivite) {
                            $returnvall .= '<tr>
                                 <td colspan="3" align="Center" style="width:100%;font-size:28px;background-image:url(/assets/00_images/background_separateur_2.png);background-size: 100% 100%;font-weight:bold;line-height:35px;color: white;"><label>'.$this->Jour[$row->calDay].' le '.$row->dayMonth.' '.$this->Mois[$row->Mois - 1].'</label></td>
                         </tr>';
                            $activeDate = $row->DateActivite;
                        }
                        $returnvall .= '<tr>
                                 <td valign="top" align="left" style="width:80px;font-size:24px;line-height:30px;"><label>'.$row->Heure.'</label></td>
                                <td valign="top" align="left" style="width:350px;font-size:24px;line-height:30px;"><label ><b>'.$row->Titre.'</b></label></td>
    
                                <td valign="top"  align="left"><div style="width:800px"white-space:normal;font-size:x-large;line-height:30px;>'.$row->Description.'</div></td>
                         </tr>';
                    }
                    //-------------------------------------------------------------------------
                    $data['listactivites'] = $returnvall.'</tbody></table></div></div>';
                    $data['mois'] = $mois;
                    echo view('pages/print_calendrier', $data);
                    //-------------------------------------------------------------------
                    break;
            }
    
            //-------------------------------------------------------------------
    }


    public function getCamFromTypeCal($idtypecam)
    {    
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Calendrier_model", "calendrier");
            $activedate = $this->db->escape($this->input->post_get('activedate'));
            $returnval = $this->calendrier->GetDropCamions($idtypecam);
            echo $returnval;
    }


    public function getRHtoDropCal()
    {    
            $intype = 1;
            $valtofind = $this->db->escape('%'.urldecode($this->input->get_post('valtofinddrop')).'%');
            $valtofind = " AND ( LCASE(reshumaine.Nom) LIKE $valtofind OR LCASE(reshumaine.Prenom) LIKE $valtofind OR LCASE(reshumaine.email) LIKE $valtofind)";
            if (is_numeric($this->input->get_post('typechauf'))) {
                $intype = $this->input->get_post('typechauf');
            } else {
                $intype = 1;
            }
            $this->load->model("Calendrier_model", "calendrier");
            echo $this->calendrier->GetChauffeursToDrop($intype, $valtofind);
    }


    public function getTypeCamCal($idtypecam)
    {    
            $nocamion = $this->input->post_get('camion');
            /** @var string $returnval */
            $returnval = '';
            $this->load->model("Calendrier_model", "calendrier");
            $returnval = $this->calendrier->GetAllCamions($nocamion, $idtypecam);
            echo $returnval;
    }


    public function sendConfirmVille(...$args)
    {
        return $this->toCi3();
    }

    public function getListMailForVille($idville)
    {    
            $this->load->model("Villes_model", "villes");
            echo json_encode($this->villes->getInfoConnect($idville));
    }


    public function postMail($idville, $id)
    {    
            $email = $this->db->escape($this->input->post_get('email', TRUE));
            $rep = '';
            try {
                if ($id == 0) {
                    $idville = $this->db->escape($idville);
                    /** @var string  $strSQL */
                    $strSQL = "INSERT INTO villesrecipiendaire
    	                      (idville, email,)
    	                      VALUES ($idville, $email)";
                    $this->db->query($strSQL);
                } else {
                    $id = $this->db->escape($id);
                    /** @var string  $strSQL */
                    $strSQL = "UPDATE villesrecipiendaire
    	                    SET
    		                    email=$email
    	                    WHERE id = $id";
                    $this->db->query($strSQL);
                }
                $rep = 'Vos données ont été enregistrées';
            } catch (Exception $e) {
                $rep = utf8_encode('Exception reçue : ').$e->getMessage();
            }
            /** @var string  $strSQL */
            $strSQL = "SELECT count(villesrecipiendaire.email) as nbMail FROM villesrecipiendaire WHERE villesrecipiendaire.idville = $idville";
            $nbmail = $this->db->query($strSQL)->getRow(0);
            /** @var object  $strSQL */
            $obj = $nbmail;
            $obj->rep = $rep;
            $jsObj = json_encode($obj);
            echo $jsObj;
    }


    public function ConfirmeVilleMail($idville, $rang)
    {    
            $this->load->library('email');
            $this->load->model("Villes_model", "villes");
            //----------------------------------------------------------
            /** @var string  $strSQL */
            $strSQL = "SELECT id, idville, rang, email, `password`
    	                FROM villesrecipiendaire WHERE idville = $idville AND rang = $rang";
            $findville = $this->db->query($strSQL);
            if ($findville->getNumRows() > 0) {
                $data = $this->villes->update_data($idville, $rang);
            } else {
                $data = $this->villes->insert_data($idville, $rang);
            }
            //----------------------------------------------------------------------------------------------------------------
            $html = '';
            $key = $this->db->escape($this->villes->keysha);
            /** @var string $to */
            $to = '';
            $strSQL = "SELECT AES_DECRYPT(email, UNHEX(SHA2($key,512))) as mail FROM villesrecipiendaire WHERE idville = ".$idville;
            $res = $this->db->query($strSQL);
            foreach ($res->getResult() as $row) {
                $to .= $row->mail.',';
            }
            $to = substr($to, 0, -1);
            $html = view('villes/email', $data);
            if (trim($html) != '') {
    
                //--------------------------------------------------------------------------
                $this->email->from('nepasrepondre@robert-daoust.ca');
                $this->email->to($to);
                $this->email->subject("Vos informations pour vous conncecter ");
                $this->email->priority = 1;
                $this->email->message($html);
                $this->email->send();
                //--------------------------------------------------------------------------
            }
    
    
            echo ('Votre demande d\'envoi a été complétée.');
    }


    public function postReponse()
    {    
            $this->load->model("Requetes_model", "requetes");
            $data = $this->requetes->postReponse();
            $this->message_error = $data['msgerr'];
            return redirect()->to(ci3_url("sanivan/aujourdhui/1/".date("Y-m-d")));
    }


    public function Reponse($idrequete)
    {    
            $this->load->model("Requetes_model", "requetes");
            $data = $this->requetes->getReponseForReq($idrequete);
            $data['idrequete'] = $idrequete;
            $data['menu'] = view('sections/inc_menu_2', $data);
            echo view('requetes/lstereception', $data);
    }


    public function insertDte($idrequete, $idreponse, $idville, $dtecedule)
    {    
            $dtecedule = $this->db->escape($dtecedule);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO daterequete
    	                (dteCedule, idrequete,idreponse, idville)
    	                VALUES ($dtecedule, $idrequete, $idreponse, $idville)";
            $this->db->query($strSQL);
            $strSQL = "UPDATE requetes
    	                SET
    		                dteCedule= $dtecedule
    	                WHERE id = $idrequete";
            $this->db->query($strSQL);
            $this->Reponse($idrequete);
    }


    public function postAdresseRequete($idrequete, $idville, $adresse)
    {    
            $adresse = $this->db->escape($adresse);
            /** @var string  $strSQL */
            $strSQL = "INSERT INTO adrrequete
    	                (idrequete, adresse, idville, old_adresse)
    	                VALUES ($idrequete,$adresse, $idville,
                              (SELECT adresse FROM requetes WHERE id = $idrequete))";
            $this->db->query($strSQL);
            $strSQL = "UPDATE requetes
    	                SET
    		                adresse = $adresse
    	                WHERE id = $idrequete";
            $this->db->query($strSQL);
            $this->getListRequetes();
    }


    public function setDateRequete($idrequete)
    {    
            $this->load->model("Requetes_model", "requetes");
            $strSQL = "UPDATE daterequete
    	                SET
    		                IsVue = 1
    	                WHERE idrequete = $idrequete ";
            $this->db->query($strSQL);
    
            echo $this->requetes->GetActivesRequetes();
    }


    public function setReponseVue($idrequete)
    {    
            echo $this->Reponse($idrequete);
            $strSQL = "UPDATE reponses
    	                SET
    		                IsVue = 1
    	                WHERE idrequete = $idrequete ";
            $this->db->query($strSQL);
    }


    public function setRequeteVue($idrequete)
    {    
            echo $this->Reponse($idrequete);
            $strSQL = "UPDATE reponses
    	                SET
    		                IsVue = 1
    	                WHERE idrequete = $idrequete ";
            $this->db->query($strSQL);
    }


    public function getListRequetes()
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                $this->load->model("Requetes_model", "requetes");
                $from = trim($this->input->post_get('from', TRUE));
                //$data['ville'] = $this->session->ville;
                $data['listrequestes'] = $this->requetes->getAllRequetes();
                $data['optVilles'] = $this->cedules->GetOptionVilles(0);
                if ($from == 1) {
                    echo $data['listrequestes'];
                } else {
                    echo view('requetes/listrequetes', $data);
                }
    
            }
    }


    public function verifRequetes()
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                // ----------------------
                $datareq = array();
                $datarep = array();
                $strSql = "SELECT id as idreq, idville, (SELECT concat('Nouvelle Requête de ', Nom ) as Nom FROM ville WHERE ville.id = requetes.idville) AS NomVilleReq 
                            FROM requetes WHERE isdeleted = 0 AND IsVue = 0 AND statut = 1 and IsFrom = 0 limit 0,1";
                $request = $this->db->query($strSql);
                foreach ($request->getResultArray() as $row) {
                    $datareq = $row;
                    $strSQL = "UPDATE requetes
    	                      SET
    		                      IsVue=1
    	                      WHERE id = ".$row['idreq'];
                    $this->db->query($strSQL);
                }
                if (count($datareq) === 0) {
                    $datareq['idreq'] = '';
                    $datareq['NomVilleReq'] = '';
                }
                $strSql = "SELECT id as idrep, idville, (SELECT  Nom FROM ville WHERE ville.id = reponses.idville) AS NomVilleRep
                          FROM reponses WHERE isdeleted = 0 AND IsVue = 0 AND statut = 1 and IsFrom = 0 limit 0,1";
                $request = $this->db->query($strSql);
                foreach ($request->getResult() as $row) {
                    $datarep = $row;
                    $strSQL = "UPDATE reponses
    	                      SET
    		                      IsVue=1
    	                      WHERE id = ".$row['idrep'];
                    $this->db->query($strSQL);
                }
                if (count($datarep) === 0) {
                    $datarep['idrep'] = '';
                    $datarep['NomVilleRep'] = '';
                }
                $dataall = array_merge($datareq, $datarep);
                echo json_encode($dataall);
            }
    }


    public function RequetesPDF()
    {    
            chdir(rtrim((string) env('ci3.webroot'), '/\\')); require_once rtrim((string) env('ci3.webroot'), '/\\') . '/application/controllers/pdf/librequete.php';
            $pdf = new \PDF('L');
            $pdf->SetFont('Arial', '', 12);
            $returnval = '';
            $idville = trim($this->input->post_get('idville', TRUE));
            if ($idville > 0) {
                $idville = ' and idville = '.$this->db->escape($idville);
            } else {
                $idville = '';
            }
            $istraitees = trim($this->input->post_get('istraitees', TRUE));
            if ($istraitees == 1) {
                $istraitees = ' and statut = 7';
            } else {
                $istraitees = '';
            }
            $dtestart = trim($this->input->post_get('dtestart', TRUE));
            if ($dtestart != '') {
                $dtestart = ' and dtecreate >= '.$this->db->escape($dtestart);
            }
            $dteend = trim($this->input->post_get('dteend', TRUE));
            if ($dteend != '') {
                $dteend = $dteend.' 23:59:00';
                $dteend = ' and dtecreate <= '.$this->db->escape($dteend);
            }
            /** @var string  $strDesc */
            $strDesc = ($this->session->site_lang == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
            $strSQL = "SELECT id,CONCAT('R-', id) AS noRequete, idville, nom, adresse, IsFrom,
    										CONCAT('upload/', idville,'/',fichier) as url, TRIM(`description`) AS `description`,(SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS matiere,
                        (SELECT COUNT(id) FROM reponses WHERE IsVue = 0 and IsFrom = 0 AND idrequete = requetes.id) as RepVue,
                        (SELECT Count(reponses.id) FROM reponses WHERE reponses.idrequete = requetes.id) as NbRep,
    										                    (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statut,
                        (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
                        Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = requetes.idville) as NomVille,
    										createby, left(dtecreate, 10) as dtecreate, dteupdate, left(dteCedule, 10) as dteCedule
    							FROM requetes where Iscomplete = 0 and isdeleted = 0 $idville $istraitees $dtestart $dteend ORDER BY id";
            /** @var object $cal */
            $cal = $this->db->query($strSQL);
            $titre = '';
            $footer = '';
            $total = '';
            $col = 3;
            //-------------------------------------------------------------------------
            if ($cal->getNumRows() > 0) {
                /** @var array $data */
                foreach ($cal->getResult() as $row) {
                    $titre = utf8_decode('Requête ville '.$row->NomVille);
                    $nomreq = ($row->IsFrom == 0) ? $row->NomVille : 'RDF';
                    $nomreq = ($row->IsFrom == 0) ? $row->NomVille : 'RDF';
                    $maxlen = strlen(utf8_decode($row->description));
                    $row->description = preg_replace("/\r|\n/", " ", $row->description);
                    $maxField = utf8_decode(trim($row->description));
    
                    if (strlen(utf8_decode($row->adresse)) > $maxlen) {
                        $row->adresse = preg_replace("/\r|\n/", " ", $row->adresse);
                        $maxlen = strlen(utf8_decode($row->adresse));
                        $maxField = utf8_decode($row->adresse);
                    }
                    if (strlen(utf8_decode($row->nom)) > $maxlen) {
                        $row->nom = preg_replace("/\r|\n/", " ", $row->nom);
                        $maxlen = strlen(utf8_decode($row->nom));
                        $maxField = utf8_decode($row->nom);
                    }
                    if (strlen(utf8_decode($row->statut).' '.utf8_decode($row->matiere)) > $maxlen) {
                        //$row->nom = preg_replace("/\r|\n/", " ", $row->nom);
                        $maxlen = strlen(utf8_decode($row->statut).' '.utf8_decode($row->matiere));
                        $maxField = utf8_decode(utf8_decode($row->statut).' '.utf8_decode($row->matiere));
                    }
                    //$dataReq[] = array(utf8_decode($nom),
                    //                    $row->noRequete,
                    //                    ($row->dteCedule == '0000-00-00')? '' : $row->dteCedule,
                    //                    utf8_decode($row->nom),
                    //                    utf8_decode($row->adresse),
                    //                    utf8_decode($row->description),
                    //                    utf8_decode($row->statut).' '. utf8_decode($row->matiere),
                    //                    $row->dtecreate,
                    //                    ($pdf->NbLines(60, $maxField) * 6), $row->NbRep);
                    /** @var string  $strDesc */
                    $strDesc = ($this->session->site_lang == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
                    $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(trim(dteCedule), 10) as dteCedule,
    							    (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
                      (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
                      Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
    										    createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue, statut,
                      noSerie, couleur, grandeur, matiere
    							    FROM reponses WHERE idrequete = $row->id
    											    AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
                    /** @var object $cal */
                    $rep = $this->db->query($strSQL);
                    $dataRep = null;
                    $colrep = 2;
                    $maxFieldrep = 0;
                    $infoStatAdr = '';
                    foreach ($rep->getResult() as $rowrep) {
                        $titre = utf8_decode('Requête ville '.$rowrep->NomVille);
                        $nom = ($rowrep->IsFrom == 0) ? 'Ville' : 'RDF';
                        //-------------------------------------------------------
                        if ($rowrep->statut == 9) {
                            $infoStatAdr = '# Série: '.$rowrep->noSerie.', Grandeur: '.$rowrep->grandeur.
                                ', Couleur: '.$rowrep->couleur;
                        } else if ($row->statut == 8) {
                            $infoStatAdr = $row->adresse;
                        } else if ($row->statut == 10) {
                            $infoStatAdr = $row->matiere;
                        }
                        //-------------------------------------------------------
                        $maxlenrep = strlen(utf8_decode($rowrep->description));
                        $maxFieldrep = utf8_decode($rowrep->description);
                        if (strlen(utf8_decode($rowrep->adresse)) > $maxlenrep) {
                            $maxlenrep = strlen(utf8_decode($rowrep->adresse));
                            $maxFieldrep = utf8_decode($rowrep->adresse);
                        }
                        $dataRep[] = array(utf8_decode($nom), ($rowrep->dteCedule == '0000-00-00') ? '' : $rowrep->dteCedule, utf8_decode($rowrep->description), utf8_decode($infoStatAdr), utf8_decode($rowrep->NonStatut),
                            $rowrep->dtecreate, ($pdf->NbLines(92, $maxFieldrep) * 7), $colrep);
                    }
                    $dataReq[] = array(utf8_decode($nomreq),
                        $row->noRequete,
                        ($row->dteCedule == '0000-00-00') ? '' : $row->dteCedule,
                        utf8_decode($row->nom),
                        utf8_decode($row->adresse),
                        utf8_decode($row->description),
                        utf8_decode($row->statut).' '.utf8_decode($row->matiere),
                        $row->dtecreate,
                        $dataRep,
                        ($pdf->NbLines(60, $maxField) * 6),
                        $row->NbRep, $col);
    
                }
    
    
                // "id";"noRequete";"idville";"nom";"adresse";"IsFrom";"url";"description";"matiere";"RepVue";"NbRep";"statut";
                // "statCouleur";"Iscomplete";"isdeleted";"NomVille";"createby";"dtecreate";"dteupdate";"dteCedule"
                $headerReq = array(utf8_decode('REQUÉRANT'), utf8_decode('REQUÊTE'), utf8_decode('CÉDULÉE'), 'TITRE', 'ADRESSE',
                    'DESCRIPTION', 'STATUT/MAT.', 'DATE');
    
                $titre = '';
                $footer = '';
                $total = '';
                //-------------------------------------------------------------------------
    
                $headerRep = array('', utf8_decode('CÉDULÉE LE'), 'DESCRIPTION', utf8_decode('ADRESSE / BAC / MATIÈRE'), 'STATUT', 'DATE');
                $pdf->SetFont('Arial', '', 14);
                $h = $pdf->GetPageHeight();
                //$pdf->SetWidths(array(40, 20, 25, 50, 60, 60, 30, 25));
                //$pdf->dataREQ = $arrReq;
                //$pdf->textrep = utf8_decode($arrReq['titreRequete']);
                //$pdf->Titre = utf8_decode($arrReq['titre']);
                //$pdf->infoRequete = utf8_decode($arrReq['requete']);
                //$pdf->footergrid = 'Requête créée le ' . $arrReq['dtecreate'];
                $pdf->headerCol = $headerReq;
                $pdf->headerColRep = $headerRep;
                $pdf->from = 'REQ';
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->FancyTable($dataReq);
                $filename = './upload/requete.pdf';
                $pdf->Output($filename, 'F');
                //---------------------------------------------------------------------
                //force_download($filename, NULL);
                //delete_files($filename);
                //-------------------------------------------------------------------
                $download = ' <a href="'.ci3_url('upload/requete.pdf').'" target="_blank" download>
    						      <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;T&eacute;l&eacute;charger votre fichier</a>';
    
                echo $download;
            } else {
                echo 'none';
            }
    }


    public function ReponsesPDF($idrequete)
    {    
            chdir(rtrim((string) env('ci3.webroot'), '/\\')); require_once rtrim((string) env('ci3.webroot'), '/\\') . '/application/controllers/pdf/libreceptions.php';
            $pdf = new \PDF('L');
            $pdf->SetFont('Arial', '', 12);
            $arrReq = $this->getDescriptionRequete($idrequete);
            $returnval = '';
            /** @var string  $strDesc */
            $strDesc = ($this->session->site_lang == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
            $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(trim(dteCedule), 10) as dteCedule,
    							(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
    (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
    Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
    										createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue
    							FROM reponses WHERE idrequete = $idrequete
    											AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
            /** @var object $cal */
            $cal = $this->db->query($strSQL);
            $titre = '';
            $footer = '';
            $total = '';
            //-------------------------------------------------------------------------
            /** @var array $data */
            foreach ($cal->getResult() as $row) {
                $titre = utf8_decode('Requête ville '.$row->NomVille);
                $nom = ($row->IsFrom == 0) ? $row->NomVille : 'RDF';
                $data[] = array(utf8_decode($nom), ($row->dteCedule == '0000-00-00') ? '' : $row->dteCedule, utf8_decode($row->description), utf8_decode($row->adresse), utf8_decode($row->NonStatut), $row->dtecreate, ($pdf->NbLines(92, utf8_decode($row->description)) * 6));
            }
    
            $header = array(utf8_decode('REQUÉRANT'), utf8_decode('CÉDULÉE LE'), 'DESCRIPTION', 'CHANGEMENT ADRESSE', 'STATUT', 'DATE');
            $pdf->SetFont('Arial', '', 14);
            $pdf->dataREQ = $arrReq;
            $pdf->textrep = utf8_decode($arrReq['titreRequete'] ?? '');
            $pdf->Titre = utf8_decode($arrReq['titre'] ?? '');
            $pdf->infoRequete = utf8_decode($arrReq['requete'] ?? '');
            $pdf->footergrid = 'Requête créée le '.($arrReq['dtecreate'] ?? '');
            $pdf->headerCol = $header;
            $pdf->from = 'REQ';
            $pdf->AddPage();
            $pdf->FancyTable($data);
            $filename = './upload/'.$row->idville.'/requete.pdf';
            $pdf->Output($filename, 'F');
            //---------------------------------------------------------------------
            //force_download($filename, NULL);
            //delete_files($filename);
            //-------------------------------------------------------------------
            $download = ' <a href="'.ci3_url('upload/'.$row->idville.'/requete.pdf').'" target="_blank" download>
    						    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;T&eacute;l&eacute;charger votre fichier</a>';
    
            echo $download;
    }


    public function getVilleNom($id)
    {    
            /** @var string  $strSQL */
            $strSQL = "SELECT Nom FROM ville WHERE id = $id";
            $ville = $this->db->query($strSQL)->getRow(0);
            echo $ville->Nom;
    }


    public function RequetesEXCEL()
    {    
            $this->load->model("ExcelVille_model", "excel");
            $this->excel->getListRequetes(env('ci3.webroot') . '/formulaires/liste_requetes.xlsx');
    
            //-------------------------------------------------------------------
            $download = ' <a href="'.ci3_url('upload/liste_requetes.xlsx').'" target="_blank" download>
    						    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;T&eacute;l&eacute;charger votre fichier</a>';
    
            echo $download;
    }


    public function resetAllVille()
    {    
            $dtedeb = $this->db->escape($this->input->post_get('dtedeb'));
            $dtefin = $this->db->escape($this->input->post_get('dtefin'));
            $idville = $this->input->post_get('idville');
            $where = (intval($idville) > 0) ? ' and idville='.$this->db->escape($this->input->post_get('idville')) : '';
            /** @var int $strSQL */
            $strSQL = "SELECT Id, idville, Dateramassage, TypeRamassage, typecam, idsecteur
    	              FROM tblvillesdateramassage where Dateramassage between $dtedeb and $dtefin $where
    	              ORDER BY id";
            $result = $this->db->query($strSQL);
            $this->db->query("DELETE FROM cedules where DateJour between $dtedeb and $dtefin");
            $this->db->query("DELETE FROM grp_cedules WHERE datejour between $dtedeb and $dtefin");
            foreach ($result->getResult() as $row) {
                $date = $this->db->escape($row->Dateramassage);
                $ville = $this->db->escape($row->idville);
                $typecedule = $this->db->escape($row->typecam);
                $typedechet = $this->db->escape($row->TypeRamassage);
                $sql = "select ID from cedules where datejour = $date and idvilles = $ville and 
                                        TypeCedule = $typecedule and TypeDeProduit = $typedechet";
                $cedElement = $this->db->query($sql);
                if ($cedElement->getNumRows() == 0) {
                    //------------------------
                    $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules 
                                      WHERE datejour = $date AND `TypeCedule` = $typecedule ";
                    $grp = $this->db->query($strSQL)->getRow(0);
                    $arow = 0;
                    if (! isset($grp->MaxRow)) {
                        $arow = 1;
                    } else {
                        $arow = $grp->MaxRow;
                    }
                    $strSQL = "INSERT INTO grp_cedules
    	                  (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet) 
    	                  VALUES ($date, $ville, 1, $arow, $typecedule, $typedechet)";
                    $this->db->query($strSQL);
                    $strSQL = 'SELECT LAST_INSERT_ID() as id';
                    $result = $this->db->query($strSQL)->getRow(0);
                    $idgrp = $result->id;
                    $strSQL = "INSERT INTO cedules
    	                          (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
    	                        VALUES ($date, $ville, $typecedule, $idgrp, 
                                    (SELECT Nom FROM typededechet WHERE id = $typedechet), 27)";
                    $this->db->query($strSQL);
                    //---------------------------        
                }
            }
            $dateactive = date("Y-m").'-01';
            return redirect()->to(ci3_url("/sanivan/getinfoville?idville=$idville&typederamassage=1&selectedDay=$dateactive&manuel=0&moisseul=0&redep=1"));
    }


    public function moveCollecte($dateToScroll, $id, $jour)
    {    
            //--------------------------------
            /** @var string $year */
            $year = substr($dateToScroll, 0, 4);
            /** @var string $month */
            $month = substr($dateToScroll, 4, 2);
            /** @var string $day */
            $day = substr($dateToScroll, 6, 2);
            /** @var string $dteActive */
            $dteActive = $this->db->escape($year.'-'.$month.'-'.$jour);
            //--------------------------------
            /** @var string $strSQL */
            $strSQL = "SELECT idville, Dateramassage, TypeRamassage, typecam
    	                            FROM tblvillesdateramassage WHERE Id = $id";
            $tbram = $this->db->query($strSQL)->getRow(0);
            $idville = $this->db->escape($tbram->idville);
            $Dateramassage = $this->db->escape($tbram->Dateramassage);
            $TypeRamassage = $this->db->escape($tbram->TypeRamassage);
            $typecam = $this->db->escape($tbram->typecam);
            $strSQL = "UPDATE tblvillesdateramassage
    	              SET
    		              Dateramassage=$dteActive
    	              WHERE Id = $id";
            $this->db->query($strSQL);
            //--------------------------------
            $strSQL = "DELETE FROM grp_cedules WHERE datejour = $Dateramassage AND 
                      typecedule = $typecam AND TypeDechet = $TypeRamassage AND idvilles = $idville";
            $this->db->query($strSQL);
    
            $strSQL = "DELETE FROM cedules WHERE DateJour = $Dateramassage AND 
                    TypeDeProduit = $TypeRamassage AND idvilles = $idville AND typecedule = $typecam";
            $this->db->query($strSQL);
            //----------------------------------------------------------------------------
            $strSQL = "SELECT id FROM grp_cedules 
                                  WHERE datejour = $Dateramassage AND `TypeCedule` = $typecam ORDER BY ARow";
            $result = $this->db->query($strSQL);
            if ($result->getNumRows() > 0) {
                $iter = 1;
                foreach ($result->getResult() as $row) {
                    $sql = "UPDATE grp_cedules
    	                SET
    		                ARow=$iter
    	                WHERE id=".$row->id;
                    $this->db->query($sql);
                    $iter++;
                }
            }
            //------------------------
            $strSQL = "SELECT (MAX(ARow) + 1) AS MaxRow FROM grp_cedules 
                                  WHERE datejour = $dteActive AND `TypeCedule` = $typecam";
            $grp = $this->db->query($strSQL)->getRow(0);
            $arow = 0;
            if (! isset($grp->MaxRow)) {
                $arow = 1;
            } else {
                $arow = $grp->MaxRow;
            }
    
            $strSQL = "INSERT INTO grp_cedules
    	              (datejour, idvilles, ACol, ARow, TypeCedule, TypeDechet)
    	              VALUES ($dteActive, $idville, 1, $arow, $typecam, $TypeRamassage)";
            $this->db->query($strSQL);
            $strSQL = 'SELECT LAST_INSERT_ID() as id';
            $result = $this->db->query($strSQL)->getRow(0);
            $idgrp = $result->id;
            $strSQL = "INSERT INTO cedules
    	                      (DateJour, idvilles, TypeCedule, idgrp, TypeDeProduit, idchauffeurs)
    	                    VALUES ($dteActive, $idville, $typecam, $idgrp, 
                                (SELECT Nom FROM typededechet WHERE id = $TypeRamassage), 27)";
            $this->db->query($strSQL);
            //----------------------------------------------------------------------------
            $dateactive = substr($tbram->Dateramassage, 0, 7).'-01';
            $manuel = intval($this->input->post_get('manuel'));
            return redirect()->to(ci3_url("/sanivan/getinfoville?idville=".$tbram->idville."&typederamassage=1&selectedDay=$dateactive&manuel=$manuel&moisseul=0&redep=1&from=1"));
    }


    public function delCollecte($dateToScroll, $id)
    {    
            /** @var string $dteActive */
            //--------------------------------
            /** @var string $strSQL */
            $strSQL = "SELECT idville, Dateramassage, TypeRamassage, typecam
    	                            FROM tblvillesdateramassage WHERE Id = $id";
            $tbram = $this->db->query($strSQL)->getRow(0);
            $idville = $this->db->escape($tbram->idville);
            $Dateramassage = $this->db->escape($tbram->Dateramassage);
            $TypeRamassage = $this->db->escape($tbram->TypeRamassage);
            $typecam = $this->db->escape($tbram->typecam);
            //--------------------------------
            $strSQL = "DELETE FROM grp_cedules WHERE datejour = $Dateramassage AND 
                      typecedule = $typecam AND TypeDechet = $TypeRamassage AND idvilles = $idville";
            $this->db->query($strSQL);
    
            $strSQL = "DELETE FROM cedules WHERE DateJour = $Dateramassage AND 
                    TypeDeProduit = $TypeRamassage AND idvilles = $idville AND typecedule = $typecam";
            $this->db->query($strSQL);
            $strSQL = "DELETE FROM tblvillesdateramassage WHERE Id = $id";
            $this->db->query($strSQL);
            //----------------------------------------------------------------------------
            $dateactive = substr($tbram->Dateramassage, 0, 7).'-01';
            $manuel = intval($this->input->post_get('manuel'));
            return redirect()->to(ci3_url("/sanivan/getinfoville?idville=".$tbram->idville."&typederamassage=1&selectedDay=$dateactive&manuel=$manuel&moisseul=0&redep=1&from=1"));
    }



    public function index()
    {    
            $data = array();
            $data['msgerror'] = $this->message_error;
            $data['msgreset'] = $this->message_error;
            echo view('partials/login2', $data);
    }

    private function updateActivy()
    {    
    
            //$user_id = $this->db->escape($this->session->userdata('user_id'));
            //$session_id = $this->db->escape($this->session->userdata('session_id'));
            //$strSQL = "UPDATE session_active
            //              SET
            //                session_last_upd=NOW()
            //           WHERE idsession = $session_id AND user_id = $user_id";
            //$this->db->query($strSQL);
    }

    public function logout()
    {    
            /** @var array  $sessionvar */
            $sessionvar = $this->session->all_userdata();
            $this->session->unset_userdata($sessionvar);
            $this->session->unset_userdata(['email', 'nom']);
            $this->session->set_userdata('login_in', FALSE);
            $this->session->sess_destroy();
            $this->output->set_header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_header("Expires: ".gmdate("D, d M Y H:i:s").' GMT');
            $this->index();
    }

    public function pageLogin()
    {    
            echo view('sanivan/loginchauf');
    }

    public function loginChauffeur()
    {    
            $this->load->model("login_model", "login");
            $code = $this->input->post_get('code', TRUE);
            if (isset($code)) {
                //$email = $this->input->post('email');
                /** @var array $data */
                $data = [
                    'login' => $this->input->post_get('code', TRUE),
                    'password' => $this->input->post_get('password', TRUE)
                ];
                /** @var object $result */
                $result = new \stdClass();
                $result = $this->login->validate_chauffeur($data);
                //$result = $this->login->updateUser();
                if (! empty($result)) {
                    $this->session->set_userdata('nom', $result->Prenom.' '.$result->Nom);
                    $this->session->set_userdata('email', '');
                    $this->session->set_userdata('user_id', $result->id);
                    $this->session->set_userdata('login_in', TRUE);
                    $this->validChauffeur($result->id);
                } else {
                    $this->pageLogin();
                }
    
            } else {
                $this->pageLogin();
            }
    }

    public function validChauffeur($idchauffeur)
    {    
            $this->load->model("Cedules_model", "cedules");
    
            $date = date_create(date('Y-m-d'));
            $data['ActiveDate'] = $date->format('Y-m-d');
            $data['validsanivan'] = $this->cedules->getValidChauffeurJour($data['ActiveDate'], $idchauffeur);
            $data['html'] = view('sanivan/inc_rapport', $data);
            $data['nom'] = $this->session->userdata('nom');
            $data['email'] = $this->session->userdata('email');
    
            $data['navtop'] = view('partials/navtop', $data);
            $data['navleft'] = view('partials/navleft', $data);
            echo view('sanivan/Validationchauffeur', $data);
    }

    private function updateMainPolyline($idcedule)
    {    
            $idcedule = $this->db->escape($idcedule);
    
            $strSQL = "UPDATE cedules c
                   SET c.idpolyline = (
                       SELECT idpolyline 
                       FROM cedule_polylines 
                       WHERE idcedule = $idcedule 
                       ORDER BY ordre 
                       LIMIT 1
                   )
                   WHERE c.ID = $idcedule";
    
            $this->db->query($strSQL);
    }

    public function updLigneCedule($id)
    {    
            $id = $this->db->escape($id);
            //--------------------------------------------------------------
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->getCedule($id);
    }

    public function setRoute($id, $idroute, $dateactive)
    {    
            $idesc = $this->db->escape($id);
            $routeesc = $this->db->escape($idroute);
            /** @var string  $strSQL */
            $strSQL = "Select * from cedules where ID = $idesc";
            $result = $this->db->query($strSQL);
            $row = $result->getRowArray();
            for ($i = 1; $i < 5; $i++) {
                if (trim($row['Route'.$i]) == '') {
                    $strSQL = "UPDATE cedules
    	                    SET Route$i = $routeesc
    	                    WHERE ID = ".$idesc;
                    $this->db->query($strSQL);
                    break;
                }
            }
            $strSQL = "SELECT (SELECT routes.`Description` FROM routes WHERE routes.id = c.Route1) as DescRte1,
                          (SELECT routes.`Description` FROM routes WHERE routes.id = c.Route2) as DescRte2,
                          (SELECT routes.`Description` FROM routes WHERE routes.id = c.Route3) as DescRte3,
                          (SELECT routes.`Description` FROM routes WHERE routes.id = c.Route4) as DescRte4,
                          (SELECT routes.`Description` FROM routes WHERE routes.id = c.Route5) as DescRte5,
                          (SELECT 1 FROM routes WHERE routes.id = c.Route1) as Route1,
                          (SELECT 2 FROM routes WHERE routes.id = c.Route2) as Route2,
                          (SELECT 3 FROM routes WHERE routes.id = c.Route3) as Route3,
                          (SELECT 4 FROM routes WHERE routes.id = c.Route4) as Route4,
                          (SELECT 5 FROM routes WHERE routes.id = c.Route5) as Route5 FROM cedules c WHERE ID = $idesc";
            $result = $this->db->query($strSQL)->getRow(0);
            $route = $result->Route1.((trim($result->Route2) == '') ? '' : '/'.$result->Route2);
            $route .= ((trim($result->Route3) == '') ? '' : '/'.$result->Route3);
            $route .= ((trim($result->Route4) == '') ? '' : '/'.$result->Route4);
            $route .= ((trim($result->Route5) == '') ? '' : '/'.$result->Route5);
            //-------------------
            $title = $result->DescRte1.((trim($result->DescRte2) == '') ? '' : '/'.$result->DescRte2);
            $title .= ((trim($result->DescRte3) == '') ? '' : '/'.$result->DescRte3);
            $title .= ((trim($result->DescRte4) == '') ? '' : '/'.$result->DescRte4);
            $title .= ((trim($result->DescRte5) == '') ? '' : '/'.$result->DescRte5);
            //--------------------------------
            $data['title'] = $title;
            $data['route'] = $route;
            echo $title.'_'.$route;
    }

    public function removePolylineCedule()
    {    
            $idcedule = $this->input->post('idcedule');
    
            if (!$idcedule) {
                echo 'ERROR: ID cédule manquant';
                return;
            }
    
            $idcedule = $this->db->escape($idcedule);
    
            $strSQL = "UPDATE cedules 
                   SET idpolyline = 0,
                       Secteur = ''
                   WHERE ID = $idcedule";
    
            $this->db->query($strSQL);
    
            $error = $this->db->error();
            if (!empty($error['code'])) {
                echo 'ERROR: ' . $error['message'];
            } else {
                echo 'OK';
            }
    }

    public function getSuivisCamionFromList($id)
    {    
            /** @var string $returnval */
            $returnval = '';
            $id = $this->db->escape($id);
            $typehs = $this->input->get('typehs');
            /** @var string $strSQL */
            switch ($typehs) {
                case 1:
                case 3:
                    $strSQL = "Select id, nocamion, left(datestart, 10) AS datestart, note, left(dateretourprevu, 10) AS dateretourprevu
                                            from suiviscamion where id = $id";
                    break;
                case 2:
                    $strSQL = "Select id, nocamion, left(datestart, 10) AS datestart, note, left(dateretourprevu, 10) AS dateretourprevu, TypeDechet, QteDechet
                                            from suiviscamion where id = $id";
                    break;
            }
            $suivis = $this->db->query($strSQL);
            foreach ($suivis->getResultArray() as $row) {
                $returnval = json_encode($row);
            }
            echo $returnval;
    }

    public function getEmployes()
    {    
            /** @var string $returnval */
            $returnval = '';
            $typerh = $this->db->escape($this->input->post('typerh'));
            $this->load->model("Cedules_model", "cedules");
            echo $this->cedules->GetAllEmployes($typerh, '0');
            echo $returnval;
    }

    private function CalculJour()
    {    
            /** @var int  $hour */
            $hour = intval(date('h'));
            /** @var string  $dateactive */
            switch (intval(date('w'))) {
                case 0:
                    $dateactive = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
                    break;
                case 5:
                    if ($hour > 11) {
                        $dateactive = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 3, date("Y")));
                    }
                    break;
                case 6:
                    $dateactive = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));
                    break;
                default:
                    $dateactive = date('Y-m-d');
            }
            return $dateactive;
    }

    public function getSuivisCamionCalendrier($id)
    {    
            /** @var string $returnval */
            $returnval = '';
            $id = $this->db->escape($id);
            /** @var string $strSQL */
    
            $strSQL = "Select id, nocamion, left(datestart, 10) AS datestart, note, left(dateretourprevu, 10) AS dateretourprevu
                                        from suiviscamion where id = $id";
            $suivis = $this->db->query($strSQL);
            foreach ($suivis->getResultArray() as $row) {
                $returnval = json_encode($row);
            }
            echo $returnval;
    }

    public function makePDF()
    {    
            $order = $this->input->post_get('orderby');
            $listtoprint = $this->input->post_get('listtoprint');
            $header = array();
            $strSQL = '';
            switch ($listtoprint) {
                case 1:
                    $strSQL = "SELECT Code, Titre, Auteur, TypeDeLivre
    	                                    FROM livres ORDER BY $order";
                    $header = array('Code', 'Titre', 'Auteur');
                    $titre = 'Liste des livres en date du '.date('Y-m-d');
                    break;
                case 2:
                    $strSQL = "SELECT pretlivres.CodeLivres, (SELECT Titre FROM livres WHERE livres.Code = pretlivres.CodeLivres) AS titre,
    		(SELECT CONCAT(Prenom, ' ', Nom) FROM membres WHERE membres.ID = pretlivres.IdMembre) as Nom,
    		left(DATE_ADD(DateSortie, INTERVAL 56 DAY), 10) AS Retour
    		 FROM pretlivres WHERE DateRetour IS NULL OR DateRetour = '' ORDER BY DATE_ADD(DateSortie, INTERVAL 56 DAY), pretlivres.CodeLivres";
                    $header = array('Code', 'Titre', 'Nom', 'Retour le');
                    $titre = 'Liste des prêts en date du '.date('Y-m-d');
                    break;
                case 3:
                    $strSQL = "SELECT Code, Titre, SousTitre, Auteur, TypeDeLivre, Notes, Prix,
                          LEFT(DateAchat, 10) as DateAchat
    	                FROM livres  where DateAchat >= (select DateStartNew from options) ORDER BY Code";
                    $header = array('Code', 'Titre', 'Auteur');
                    $titre = 'Liste des nouveautés en date du '.date('Y-m-d');
                    break;
            }
            $returnval = '';
            /** @var object $cal */
            $cal = $this->db->query($strSQL);
            $total = '';
            //-------------------------------------------------------------------------
            chdir(rtrim((string) env('ci3.webroot'), '/\\')); require_once rtrim((string) env('ci3.webroot'), '/\\') . '/application/controllers/pdf/requetespdf.php';
            /** @var array $data */
            foreach ($cal->getResult() as $row) {
                switch ($listtoprint) {
                    case 1:
                        $data[] = array(
                            $row->Code,
                            utf8_decode($row->Titre),
                            utf8_decode($row->Auteur)
                        );
                        break;
                    case 2:
                        $data[] = array(
                            $row->CodeLivres,
                            utf8_decode($row->titre),
                            utf8_decode($row->Nom),
                            $row->Retour
                        );
                        break;
                    case 3:
                        $data[] = array(
                            $row->Code,
                            utf8_decode($row->Titre),
                            utf8_decode($row->Auteur)
                        );
    
                        break;
                }
    
            }
            if ($listtoprint == 2) {
                $pdf = new \PDF('L');
            } else {
                $pdf = new \PDF('P');
            }
            $pdf->SetTopMargin(4);
            $pdf->SetAutoPageBreak(true, 2);
            $pdf->SetFont('Arial', '', 14);
            $pdf->Titre = $titre;
            $pdf->listToPrint = $listtoprint;
            $pdf->headerCol = $header;
            $pdf->AddPage();
            $pdf->FancyTable($data);
            $filename = './upload/livres.pdf';
            $pdf->Output('F', $filename);
            //$pdf->Output('I',$filename );
            //---------------------------------------------------------------------
            $download = ' <a href="'.ci3_url('upload/livres.pdf').'" target="_blank" download>
    						    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;T&eacute;l&eacute;charger votre fichier</a>';
    
            echo $download;
            //-------------------------------------------------------------------
    }

    private function getDescriptionRequete($idrequete)
    {    
            /** @var array  $returnval */
            $returnval = array();
            /** @var string  $strDesc */
            $strDesc = ($this->session->site_lang == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
            /** @var string  $strSQL */
            $strSQL = "SELECT id,CONCAT('R-', id) AS noRequete, idville, nom, adresse, IsFrom, `description`,(SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS matiere,
    				(SELECT COUNT(id) FROM reponses WHERE IsVue = 0 and IsFrom = 0 AND idrequete = requetes.id) as RepVue,
    				(SELECT Count(reponses.id) FROM reponses WHERE reponses.idrequete = requetes.id) as NbRep,
    														(SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statut,
    				(SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
    				Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = requetes.idville) as NomVille,
    														createby, dtecreate, dteupdate, left(dteCedule, 10) as dteCedule
    							FROM requetes where id = $idrequete";
            $req = $this->db->query($strSQL);
            $row = $req->getRowArray();
    
            if (isset($row)) {
                $returnval = $row;
                $returnval['titreRequete'] = 'Ville: '.$row['NomVille'].' Requête: '.$row['noRequete'].' Titre '.$row['nom'];
                $returnval['titre'] = $row['NbRep'].' Réponse(s) et Ajout(s) pour la requête '.$row['noRequete'];
                $returnval['requete'] = " <b> ".$row['dteCedule'].' # Requête '.$row['noRequete']." </b><br/>
    																	  Statut:<b>".$row['statut']."  </b><br/>
                                                   <b> Adresse: </b> <br/>
                                                    ".$row['adresse'].' '.$row['matiere']."<br/>
                                                   <b> Description de la requête: </b> <br/>
                                                    ".$row['description']." <br/>";
            }
    
            //'<div class="input-group" style="width:100%;font-size:36px;font-weight:bold;color:darkblue;">
    //                    <span class="form-control" style="width:200px;font-size:28px;font-weight:bold;color:darkblue;">' . $row->NomVille . '</span>
    //                    <span class="form-control" style="width:100px;font-weight:bold;">REQUÊTE</span>
    //                    <span class="form-control" style="font-size:20px;font-weight:bold;color:darkblue;">R-' . $row->id . '</span>
    //                    <span class="form-control" style="width:80px;font-weight:bold;">STATUT </span>
    //                    <span class="form-control" style="width:130px;text-align:center;background-color:
    //                    ' . $row->statCouleur . ';font-size:20px;font-weight:bold;color:darkblue;">' . $row->statut . ' </span>
    //                    <span class="form-control" style="width:300px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->description . '</span>
    //                    <span class="form-control" style="width:100px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->matiere . '</span>
    //                    <span class="form-control" style="width:300px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->dtecreate . '</span>
    //                    <span class="form-control" style="width:100px;font-size:20px;font-weight:bold;color:darkblue;">' . $row->dteCedule . '</span>
            //</div>';
            return $returnval;
    }

    public function summernoteImage()
    {    
            if ($_FILES['image']['name']) {
                if (! $_FILES['image']['error']) {
                    //$ext = explode('.', $_FILES['image']['name']);
                    //$filename = $ext;
                    $destination = './userfiles/'.$_FILES['image']['name']; //change path of the folder...
                    $location = $_FILES["image"]["tmp_name"];
                    move_uploaded_file($location, $destination);
                    echo ci3_url().'userfiles/'.$_FILES['image']['name'];
                } else {
                    //echo $message = 'The following error occured:  ' . $_FILES['image']['error'];
                }
            }
    }

    private function downLoadPDF($filename)
    {    
    
            // Get parameters
            //$file = urldecode($_REQUEST["file"]); // Decode URL-encoded string
    
            /* Check if the file name includes illegal characters
            like "../" using the regular expression */
            // if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $filename)){
            //$filepath = "images/" . $file;
    
            // Process download
            if (file_exists($filename)) {
                header('Content-type: application/pdf');
                header('Content-Disposition: attachment; filename="'.basename($filename).'"');
                readfile($filename);
            } else {
                http_response_code(404);
                die();
            }
            //} else {
            //  die("Invalid file name!");
            //}
    }

    public function getVerifInfo($from)
    {    
            if (! $this->session->has_userdata('email')) {
                $this->index();
            } else {
                // ----------------------
                $this->load->model("Requetes_model", "requetes");
    
                //-----------------------
    
                $data['requetes'] = $this->requetes->GetActivesRequetes();
                echo json_encode($data);
            }
    }

    public function getAdresseFromLatLng()
    {    
            $this->load->model("Google_model", "google");
            $this->google->getAdresseFromLatLng();
    }

    public function __destruct()
    {    
            $this->db->close();
    }
}
