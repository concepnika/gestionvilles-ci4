<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use App\Libraries\VilleService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Port de Co_ville (CI3, ~2 900 lignes) — portail citoyen
 * (requêtes citoyennes), zones et tags de carte.
 *
 * Authentification citoyenne : table villesrecipiendaire avec
 * AES_ENCRYPT/AES_DECRYPT MySQL (clé SHA2(Villes_model::$keysha,512))
 * — différente du pepper admin. Session : clés idville, ville, nom,
 * email, id, site_lang, last_page_{fr,en} en session CI3 partagée.
 *
 * Divergences par rapport à CI3 :
 *  - set_tempdata('codemail') → clé de session normale (codemail) :
 *    Ci3Session n'a pas de mécanisme tempdata.
 *  - routes CI3 inexistantes reproduites telles quelles
 *    (resetPassword, mapTagChauffeurs, SlugUrl, current_idville
 *    ne sont routés ni en CI3 ni en CI4 — non portés).
 *  - les libellés $this->lang->line() s'affichaient vides en CI3
 *    (aucun fichier de langue) — reproduit via ci3lang().
 *
 * Les uploads écrivent dans le docroot CI3 (env ci3.webroot) —
 * URLs /upload/... et /userfiles/... servies par CI3 en prod
 * (Alias vhost /uploads localement, voir AGENTS.md).
 */
class Ville extends BaseController
{
    private const KEYSHA = VilleService::KEYSHA;

    private Ci3Session $ci3;
    private VilleService $svc;
    /** Miroir de $this->message_error (CI3). */
    private string $messageError = '';

    public function __construct()
    {
        date_default_timezone_set('America/Toronto');

        $this->ci3 = new Ci3Session();
        $this->svc = new VilleService();

        // Détection de la langue (constructeur CI3) — NB : CI3 testait
        // strpos('/fr/') avec '/fr/' (slash final) qui ne matche JAMAIS
        // les URLs se terminant par /fr (toutes les routes lang) et
        // comparait FALSE > -1 (toujours vrai) → forçait 'fr' en
        // permanence. On teste '/fr' / '/en' (sans slash final) pour
        // restaurer l'intention (voir AGENTS.md § divergences).
        $test = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($test, '/fr') !== false) {
            $this->sessSet(['site_lang' => 'fr']);
        } elseif (strpos($test, '/en') !== false) {
            $this->sessSet(['site_lang' => 'en']);
        } elseif (! $this->ci3->has('site_lang')) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
            $this->sessSet(['site_lang' => $lang]);
        }
    }

    //=================================================================
    // Helpers session / réponse (conventions du port)
    //=================================================================

    private function sess(string $key)
    {
        return $this->ci3->get($key);
    }

    private function hasSess(string $key): bool
    {
        return $this->ci3->has($key);
    }

    /** Garantit une session CI3 avant écriture. */
    private function sessSet(array $kvs): void
    {
        if ($this->ci3->user() === null) {
            $this->ci3->create([]);
        }
        foreach ($kvs as $k => $v) {
            $this->ci3->set($k, $v);
        }
    }

    private function sessLang(): string
    {
        return (string) ($this->ci3->get('site_lang') ?? 'fr');
    }

    private function outPage(string $body): ResponseInterface
    {
        return $this->response->setStatusCode(200)->setContentType('text/html', 'utf-8')->setBody($body);
    }

    private function outJson($data, int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)
            ->setContentType('application/json', 'charset=utf-8')
            ->setBody(is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function outText(string $body): ResponseInterface
    {
        return $this->response->setStatusCode(200)->setContentType('text/plain', 'utf-8')->setBody($body);
    }

    /** Racine du docroot CI3 (uploads, userfiles). */
    private function docroot(): string
    {
        return rtrim((string) env('ci3.webroot'), '/\\');
    }

    //=================================================================
    // AUTH CITOYENNE
    //=================================================================

    public function index()
    {
        $test = $_SERVER['REQUEST_URI'] ?? '';
        if (! ((strpos($test, '/fr') !== false) || (strpos($test, '/en') !== false))) {
            return redirect()->to('/villes/requetes/' . $this->sessLang());
        }
        return $this->outPage(view('villes/login2', [
            'msgerror'  => $this->messageError,
            'msgreset'  => '',
            'ActiveDate' => date('Y-m-d'),
        ]));
    }

    public function goAccueil()
    {
        if (! $this->hasSess('email')) {
            return $this->index();
        }
        $this->sessSet([
            'last_page_fr' => '/villes/accueil/fr',
            'last_page_en' => '/villes/accueil/en',
        ]);
        $data = [
            'nomcomplet' => $this->sess('nom'),
            'menu'       => view('villes/inc_menu_2', ['nomcomplet' => $this->sess('nom')]),
        ];
        $datanav = $this->navZoneData(1);
        $data['map_nav'] = view('villes/leaflet/map_nav_zone', $datanav);
        return $this->outPage(view('villes/accueil_admin', $data));
    }

    /**
     * Traitement du login citoyen (villes/tdb/{fr|en}).
     * Valide contre villesrecipiendaire (AES) puis ouvre la session.
     */
    public function login()
    {
        $email = $this->request->getPostGet('email');

        if ($email !== null && $email !== false) {
            $result = $this->svc->validePass(
                (string) $email,
                (string) $this->request->getPostGet('password')
            );

            if ($result !== null) {
                $forcechange = is_string($result->forcechange)
                    ? (int) ord($result->forcechange) : (int) $result->forcechange;
                if ($forcechange === 1) {
                    $this->sessSet(['email' => (string) $email]);
                    return redirect()->to('/villes/login/change?email=' . urlencode((string) $email));
                }

                // CI3 : set_tempdata('codemail', email, 7200) → clé normale
                $this->sessSet(['codemail' => (string) $email]);

                $nomcomplet = trim($result->prenom . ' ' . $result->nom);
                $VoirProfil = ($nomcomplet == '');
                $nomcomplet = ($nomcomplet == '') ? 'Compléter votre profil' : $nomcomplet;

                $this->sessSet([
                    'nom'     => $nomcomplet,
                    'email'   => $result->email,
                    'idville' => $result->idville,
                    'ville'   => $result->NomVille,
                    'id'      => $result->id,
                    'last_page_fr' => '/villes/accueil/fr',
                    'last_page_en' => '/villes/accueil/en',
                ]);

                if ($VoirProfil) {
                    return $this->showProfil();
                }

                $data = [
                    'nomcomplet' => $nomcomplet,
                    'menu'       => view('villes/inc_menu_2', ['nomcomplet' => $nomcomplet]),
                ];
                $datanav = $this->navZoneData(1);
                $data['map_nav'] = view('villes/leaflet/map_nav_zone', $datanav);
                return $this->outPage(view('villes/accueil_admin', $data));
            }

            $this->messageError = ($this->sessLang() == 'fr')
                ? 'Courriel ou mot de passe invalide.'
                : 'Invalid email or password.';
            return redirect()->to('/villes/requetes');
        }

        $this->messageError = ($this->sessLang() == 'fr')
            ? 'Courriel ou mot de passe invalide.'
            : 'Invalid email or password.';
        return redirect()->to('/villes/requetes');
    }

    public function logout()
    {
        $this->ci3->destroy();
        return $this->index();
    }

    //=================================================================
    // MOT DE PASSE
    //=================================================================

    public function resetPassW()
    {
        $datausager = [
            'email' => $this->hasSess('email') ? (string) $this->sess('email') : '',
            'from'  => 0,
        ];
        return $this->outPage(view('villes/partials/chgpassw', $datausager));
    }

    public function PostresetPassW()
    {
        $email    = urldecode((string) $this->request->getPostGet('email'));
        $password = urldecode((string) $this->request->getPostGet('passnew'));
        $key      = $this->db->escape(self::KEYSHA);
        $email    = 'AES_ENCRYPT(' . $this->db->escape($email) . ', UNHEX(SHA2(' . $key . ',512)))';
        $password = 'AES_ENCRYPT(' . $this->db->escape($password) . ', UNHEX(SHA2(' . $key . ',512)))';

        $this->db->query(
            "UPDATE villesrecipiendaire
             SET forcechange = 0, password = $password
             WHERE email = $email"
        );

        $error = $this->db->error();
        if (! empty($error['code'])) {
            $msg = ($this->sessLang() == 'fr') ? "Une erreur s'est produite." : 'An error is this product.';
        } else {
            $msg = ($this->sessLang() == 'fr')
                ? "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.<br />Connectez-vous."
                : 'Your changes have been saved.<br />Log in.';
        }
        // CI3 affichait le message puis redirigeait ; on porte le
        // message en sortie avant la redirection via le body du 302.
        return redirect()->to('/villes/requetes')->setBody($msg);
    }

    public function ValidOldPassW()
    {
        $key      = $this->db->escape(self::KEYSHA);
        $email    = 'AES_ENCRYPT(' . $this->db->escape(urldecode((string) $this->request->getPostGet('email'))) . ', UNHEX(SHA2(' . $key . ',512)))';
        $password = 'AES_ENCRYPT(' . $this->db->escape(urldecode((string) $this->request->getPostGet('password'))) . ', UNHEX(SHA2(' . $key . ',512)))';

        $result = $this->db->query(
            "SELECT id, idville, password,
                    CONCAT(prenom, ' ', nom) AS NomComplet
             FROM villesrecipiendaire WHERE email = $email AND password = $password"
        );
        return $this->outText($result->getNumRows() > 0 ? '1' : '0');
    }

    public function ValidStrongPassW()
    {
        $password = urldecode((string) $this->request->getPostGet('password'));
        $uppercase    = preg_match('@[A-Z]@', $password);
        $lowercase    = preg_match('@[a-z]@', $password);
        $number       = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        return $this->outText((! $uppercase || ! $lowercase || ! $number || ! $specialChars || strlen($password) < 8) ? '0' : '1');
    }

    /**
     * « Mot de passe oublié » : envoie par courriel un lien de
     * réinitialisation (uniqid + place, expiré après 2h).
     */
    public function sendPassword()
    {
        $rep = '';
        $email = urldecode((string) $this->request->getPostGet('email'));
        $key   = $this->db->escape(self::KEYSHA);
        $email = 'AES_ENCRYPT(' . $this->db->escape($email) . ', UNHEX(SHA2(' . $key . ',512)))';

        $sql = "SELECT id, idville, password,
                       AES_DECRYPT(email, UNHEX(SHA2($key,512))) as emailD,
                       AES_DECRYPT(password, UNHEX(SHA2($key,512))) as passW,
                       CONCAT(prenom, ' ', nom) AS NomComplet
                FROM villesrecipiendaire WHERE email = $email";
        $user = $this->db->query($sql)->getRow();

        if ($user) {
            $uniq    = uniqid();
            $place   = (rand(1, 20) > 10) ? 2 : 1;
            $passW   = urlencode($user->password);
            $userPass = ($place == 2) ? $passW . $uniq : $uniq . $passW;

            $data = [
                'nom' => $user->NomComplet,
                'password' => '<a href="https://www.robert-daoust.ca/villes/login/confirmeinfo?specco='
                    . $userPass . '&no=' . $user->id . date('is') . '">'
                    . (($this->sessLang() == 'fr')
                        ? "Cliquer ici pour r&eacute;initialiser votre mot de passe"
                        : 'Click here to reset your password.') . '</a>',
            ];

            $html = view('villes/partials/password', $data);

            $emailSvc = service('email');
            $emailSvc->setFrom('nepasrepondre@robertdaoustetfils.com');
            $emailSvc->setTo($user->emailD);
            $emailSvc->setSubject('RDF requêtes: information demandée.');
            $emailSvc->setMessage($html);
            $emailSvc->send();

            try {
                $date = new \DateTime();
                date_add($date, date_interval_create_from_date_string('2 hours'));
                $this->db->query(
                    "UPDATE villesrecipiendaire
                     SET timetoreset = " . $this->db->escape($date->format('Y-m-d H:i:s')) . ",
                         place = " . (int) $place . ",
                         uniqueid = " . $this->db->escape($uniq) . "
                     WHERE id = " . (int) $user->id
                );
                $rep = ($this->sessLang() == 'fr')
                    ? 'Vos accès vous ont été envoyés.'
                    : 'Your accesses have been sent to you.';
            } catch (\Throwable $e) {
                $rep = (($this->sessLang() == 'fr') ? 'Exception reçue : ' : 'Exception received : ')
                    . $e->getMessage();
            }
        }

        return $this->outPage(view('villes/login2', [
            'msgerror'  => '',
            'msgreset'  => $rep,
            'ActiveDate' => date('Y-m-d'),
        ]));
    }

    /**
     * Réinitialisation depuis le lien courriel (specco + no) :
     * valide le jeton (place/uniqueid) et le délai (< 15 min),
     * puis affiche le formulaire de changement.
     */
    public function excRestPassw()
    {
        $datausager = ['showerr' => ''];
        $key        = $this->db->escape(self::KEYSHA);
        $id_user    = $this->db->escape(substr((string) $this->request->getPostGet('no'), 0, -4));

        $sql = "SELECT id, idville, password, place, uniqueid, timetoreset,
                       AES_DECRYPT(email, UNHEX(SHA2($key,512))) as email,
                       CONCAT(prenom, ' ', nom) AS NomComplet
                FROM villesrecipiendaire WHERE id = $id_user";
        $user = $this->db->query($sql)->getRow();

        if ($user) {
            if ((int) $user->place == 1) {
                $user_pass = $user->uniqueid . $user->password;
            } else {
                $user_pass = $user->password . $user->uniqueid;
            }
            $specco = (string) $this->request->getPostGet('specco');
            if ((int) $user->place == 1) {
                $specco = substr($user_pass, strlen($user->uniqueid), strlen($user_pass));
            } else {
                $specco = substr($user_pass, 0, strlen($user_pass) - strlen($user->uniqueid));
            }
            if ($specco == $user->password) {
                $time1 = new \DateTime($user->timetoreset);
                $time2 = new \DateTime(date('Y-m-d H:i:s'));
                $timediff = $time1->diff($time2);
                $diffhre = $timediff->format('%h');
                $diffmin = $timediff->format('%i');
                $diff = $diffmin;
                if ($diffhre > 0) {
                    $diff = 60 + $diffmin;
                }
                if (intval($diff) < 15) {
                    $datausager['showerr'] = ($this->sessLang() == 'fr')
                        ? 'Le d&eacute;lai pour la r&eacute;initialisation est expir&eacute; ou l\'information de validation est invalide.'
                        : 'The reset timeout has expired or the validation information is invalid.';
                    $datausager['menu'] = view('partials/menu_motpasse', $datausager);
                    return $this->outPage(view('villes/partials/confirmation', $datausager));
                }

                $datausager['email'] = $user->email;
                $datausager['from']  = 2;
                return $this->outPage(view('villes/partials/chgpassw', $datausager));
            }
        }
        return $this->response->setStatusCode(200)->setBody('');
    }

    //=================================================================
    // PROFIL
    //=================================================================

    public function showProfil(): ResponseInterface
    {
        if (! $this->hasSess('email')) {
            return $this->index();
        }

        $key = $this->db->escape(self::KEYSHA);
        $rows = $this->db->query(
            "SELECT id, idville, rang, AES_DECRYPT(email, UNHEX(SHA2($key,512))) as email,
                    password, nom, prenom, phone, cell, forcechange
             FROM villesrecipiendaire WHERE id = " . (int) $this->sess('id')
        )->getResultArray();

        $data = [];
        foreach ($rows as $row) {
            $data = $row;
            $data['msgerror']  = $this->messageError;
            $data['nomcomplet'] = $this->sess('nom');
            $data['menu']      = view('villes/inc_menu_2', $data);
        }
        $this->messageError = '';
        $this->sessSet([
            'last_page_fr' => '/profil/fr',
            'last_page_en' => '/profil/en',
        ]);
        return $this->outPage(view('villes/profil', $data));
    }

    public function postProfil()
    {
        $data = $this->request->getPostGet();
        $id   = $this->db->escape($data['id']);
        $key  = $this->db->escape(self::KEYSHA);

        if (trim((string) $data['password']) != '') {
            $password = 'password = AES_ENCRYPT(' . $this->db->escape(urldecode((string) $data['password'])) . ', UNHEX(SHA2(' . $key . ',512))),';
        } else {
            $password = '';
        }

        $nom    = $this->db->escape(urldecode((string) $data['nom']));
        $prenom = $this->db->escape(urldecode((string) $data['prenom']));
        $phone  = $this->db->escape($data['phone']);
        $cell   = $this->db->escape($data['cell']);

        $this->db->query(
            "UPDATE villesrecipiendaire
             SET $password nom = $nom, prenom = $prenom, phone = $phone, cell = $cell
             WHERE id = $id"
        );

        $error = $this->db->error();
        if (! empty($error['code'])) {
            $msg = ($this->sessLang() == 'fr') ? "Une erreur s'est produite." : 'An error has occurred.';
        } else {
            $msg = ($this->sessLang() == 'fr')
                ? "Vos changements ont &eacute;t&eacute; enregistr&eacute;s."
                : 'Your changes have been saved.';
        }
        $this->messageError = $msg;
        return $this->showProfil();
    }

    //=================================================================
    // REQUÊTES CITOYENNES
    //=================================================================

    public function getRequete($id = 0)
    {
        if (! $this->hasSess('email')) {
            return $this->index();
        }

        if ($id != 0) {
            $id = $this->db->escape($id);
            $requete = $this->db->query(
                "SELECT id, idville, nom, trim(adresse), description, matiere, fichier,
                        (select ville.Nom from ville where ville.id = requetes.idville) as villenom,
                        CONCAT('upload/', idville,'/',fichier) as url, adresse, dteCedule,
                        statut, dtecreate, Iscomplete
                 FROM requetes WHERE id = $id AND idville = " . (int) $this->sess('idville')
            );
            if ($requete->getNumRows() == 0) {
                return redirect()->to('/villes/requetes');
            }
            foreach ($requete->getResultArray() as $row) {
                $data = $row;
                $download = ' <a href="' . ci3_url((string) ($row['url'] ?? '')) . '" download>
                    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;Cliquer sur ce lien pour télécharger le fichier</a>';
                $data['noreq'] = '<label class="col-lg-5 col-xl-3 font-weight-bold text-lg-end mb-0" style="font-size: 16px">
                    # Requête&nbsp;&nbsp;
                </label>
                <label class="form-control" style="width:100px">
                    R-' . $row['id'] . '
                </label>';
                $data['inforequete'] = "
                    <b> Créée le:&nbsp;&nbsp; <span style='color:red'>" . $row['dtecreate'] . " </span><br/><br/> </b>
                    <b> Description de la requête: </b> <br/>
                    <div style='border:double;padding:10px;'>" . $row['description'] . "</div> <br/>
                    <b> Adresse: </b> <br/>
    <input type=\"text\" class=\"form-control\" id=\"nom\" value=\"" . $row['adresse'] . "\" disabled /> <br/>
    <b> Matière: </b> <br/>";
                $data['optMatiere']     = $this->getOptDechets($row['matiere']);
                $data['optMatiereStat'] = $this->getOptDechets();
                $data['optStatut']      = $this->getOptStatut();
                $data['download']       = $download;
                $data['fileinclude']    = ((trim((string) $row['fichier']) == '') ? 'none' : 'block');
                $data['nomcomplet']     = $this->sess('nom');
                $data['nomville']       = $this->sess('ville');
                $data['fichiers']       = $this->getFichiers($row['id'], 0);
                $data['msgerror']       = $this->messageError;
                $data['urlphoto']       = ci3_url('villes/summernoteUpload');
                $data['menu']           = view('villes/inc_menu_2', $data);
                $this->sessSet([
                    'last_page_fr' => '/villes/requete/fr',
                    'last_page_en' => '/villes/requete/en',
                ]);
                return $this->outPage(view('villes/requete_ajout', $data));
            }
        }

        // Nouvelle requête (id = 0)
        $datanew = [
            'noreq'      => '',
            'msgerror'   => '',
            'nomville'   => $this->sess('ville'),
            'optMatiere' => $this->getOptDechets(),
            'optStatut'  => $this->getOptStatut(),
            'nomcomplet' => $this->sess('nom'),
            'urlphoto'   => ci3_url('villes/summernoteUpload'),
            'menu'       => view('villes/inc_menu_2', [
                'nomville'   => $this->sess('ville'),
                'nomcomplet' => $this->sess('nom'),
            ]),
        ];
        $this->sessSet([
            'last_page_fr' => '/villes/requete/fr/' . $id,
            'last_page_en' => '/villes/requete/en/' . $id,
        ]);
        return $this->outPage(view('villes/requete', $datanew));
    }

    private function getResponseFile($idreponse)
    {
        $returnval = '';
        $btndrop = '<div class="input-group mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-primary">Fichier(s)</button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">';
        $query = $this->db->query(
            "SELECT id, fichier FROM reponse_files WHERE idreponse = " . (int) $idreponse
        );
        $iter = 1;
        foreach ($query->getResult() as $row) {
            $returnval .= '<option value="' . $row->id . '"><a class="dropdown-item" href="' . ci3_url($row->fichier) . '"  download >
                    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>fichier ' . $iter . '</a></option>';
            $iter++;
        }
        if ($returnval != '') {
            return $btndrop . $returnval . '</div>
                    </div>
                </div>
            </div>';
        }
        return '';
    }

    /** Fragment HTML : nouvelles réponses / requêtes non vues (polling 20s). */
    public function getNewReq()
    {
        $idville = (int) $this->sess('idville');
        $returnval = '';

        $result = $this->db->query(
            "SELECT id FROM reponses WHERE idville = {$idville} AND IsVue = 0"
        );
        if ($result->getNumRows() > 0) {
            $returnval .= '<br /><span style="color:red">Vous avez  ' . $result->getNumRows() . ' nouvelle(s) requête(s) </span><br />
                <a href="javascript:setRequeteVue(' . $idville . ')" >Cliquer ici pour vérifier vos requêtes.</a>';
        }

        return $this->outText($returnval);
    }

    private function getAllDate($idrequete, $idreponse)
    {
        $result = $this->db->query(
            "SELECT left(dteCedule, 10) as dteCedule, idrequete
             FROM daterequete WHERE idrequete = " . (int) $idrequete . "
             AND idreponse = " . (int) $idreponse . " ORDER BY dteCedule DESC"
        );
        $returnval = '';
        $iter = 1;
        foreach ($result->getResult() as $row) {
            $returnval .= ($iter == 1)
                ? '<span style="color:red">Cédulée le ' . $row->dteCedule . '</span>'
                : 'Anci. date ' . $row->dteCedule;
            $returnval .= '   ';
            $iter++;
        }
        return $returnval;
    }

    public function Reponse($idrequete)
    {
        $data = $this->getReponseForReq((int) $idrequete);
        $data['idrequete'] = $idrequete;
        $data['menu'] = view('sections/inc_menu_2', $data);
        $this->sessSet([
            'last_page_fr' => '/villes/reponse/fr/' . $idrequete,
            'last_page_en' => '/villes/reponse/en/' . $idrequete,
        ]);
        return $this->outPage(view('requetes/lstereception', $data));
    }

    public function getReponseForReq($noRequete)
    {
        $returnval = '';
        $strDesc = ($this->sessLang() == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
        $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(dteCedule, 10) as dteCedule,
                    (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
                    (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
                    Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
                    createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue
                FROM reponses WHERE idrequete = " . (int) $noRequete . "
                AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
        $reponse = $this->db->query($strSQL);
        $data['titreNB'] = $reponse->getNumRows() . ' Réponse(s) et Ajout(s) pour la requête ' . $noRequete;

        foreach ($reponse->getResult() as $row) {
            $isVille = ((int) $row->IsFrom === 0);
            $originLabel = $isVille ? ($row->NomVille ?: 'Ville') : 'RDF';

            $classes = [];
            $classes[] = $isVille ? 'resp-v' : 'resp-rdf';
            if ($isVille && (int) $row->IsVue === 0) {
                $classes[] = 'resp-unseen';
            }
            $classAttr = implode(' ', $classes);

            $returnval .= '<tr class="' . $classAttr . '">'
                . '<td align="center" style="width:150px;text-align:center;">'
                . '  <span class="badge-origin ' . ($isVille ? 'ville' : 'rdf') . '">' . $originLabel . '</span>'
                . '</td>'
                . '<td style="width:185px"><label style="width: 180px;">' . $this->getAllDate($noRequete, $row->id) . '</label></td>'
                . '<td width="120">' . $this->getResponseFile($row->id) . '</td>'
                . '<td width="350"><label style="width: 350px;min-height:60px;display:inline-block">' . $row->description . '</label></td>'
                . '<td width="200"><label style="width: 220px;">' . $row->adresse . '</label></td>'
                . '<td width="160"><label style="width: 160px;min-height:28px;display:inline-block;background-color:' . $row->statCouleur . '">' . $row->NonStatut . '</label></td>'
                . '<td style="width:90px"><label style="width: 90px;">' . $row->dtecreate . '</label></td>'
                . '</tr>';
        }
        $data['listreceptions'] = $returnval;
        $data['retour'] = '<a  href="/villes/requete/liste">Retour</a>';
        return $data;
    }

    private function getFichiers($id, $from)
    {
        $returnval = '';
        $result = $this->db->query(
            "SELECT id, idrequete, fichier,
                    CONCAT((SELECT concat('upload/', idville,'/') FROM requetes WHERE requetes.id = requetes_file.idrequete),fichier) as url,
                    (SELECT idville FROM requetes WHERE requetes.id = requetes_file.idrequete) as idville
             FROM requetes_file WHERE idrequete = " . (int) $id
        );
        foreach ($result->getResultArray() as $row) {
            $returnval .= ' <a href="' . $row['fichier'] . '" download>
                    <i class="fa fa-download btn btn-success" style="font-size:16px;height:35px;"></i>&nbsp;&nbsp;Fichier</a><br />';
        }
        if ((trim($returnval) != '') and ($from != 1)) {
            $returnval = '<div class="col-md-10">
              <section class="card card-featured card-featured-primary mb-4">
                <header class="card-header">
                  <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                  </div>
                  <h2 class="card-title">
                    Fichier(s) disponible(s) pour cette requête
                  </h2>
                </header>
                <div class="card-body">
                  ' . $returnval . '
                </div>
              </section>
            </div>';
        }
        return $returnval;
    }

    public function getListRequetes(): ResponseInterface
    {
        if (! $this->hasSess('email')) {
            return $this->index();
        }

        $data = [
            'ville'         => $this->sess('ville'),
            'idville'       => $this->sess('idville'),
            'listrequestes' => $this->getAllRequetes(),
            'id'            => $this->request->getPostGet('id'),
            'nomcomplet'    => $this->sess('nom'),
            'msgerror'      => $this->messageError,
        ];
        $data['menu'] = view('villes/inc_menu_2', $data);
        $this->sessSet([
            'last_page_fr' => '/villes/requete/liste/fr',
            'last_page_en' => '/villes/requete/liste/en',
        ]);

        $from = trim((string) $this->request->getPostGet('from'));
        if ($from == '1') {
            return $this->outText((string) $data['listrequestes']);
        }
        return $this->outPage(view('villes/listrequetes', $data));
    }

    public function postAdresseRequete($idrequete, $idville, $adresse)
    {
        $adresse = $this->db->escape(urldecode($adresse));
        $idrequete = (int) $idrequete;
        $idville   = (int) $idville;

        $this->db->query(
            "INSERT INTO adrrequete
             (idrequete, adresse, idville, old_adresse)
             VALUES ($idrequete, $adresse, $idville,
                     (SELECT adresse FROM requetes WHERE id = $idrequete))"
        );
        $this->db->query("UPDATE requetes SET adresse = $adresse WHERE id = $idrequete");
        return $this->getListRequetes();
    }

    public function getAllRequetes()
    {
        $idville = (int) $this->sess('idville');

        $istraitees = trim((string) $this->request->getPostGet('istraitees'));
        $istraitees = ($istraitees == '1') ? ' and statut = 7 ' : ' and statut != 7 ';

        $dtestart = trim((string) $this->request->getPostGet('dtestart'));
        if ($dtestart != '') {
            $dtestart = ' and dtecreate >= ' . $this->db->escape($dtestart);
        }
        $dteend = trim((string) $this->request->getPostGet('dteend'));
        if ($dteend != '') {
            $dteend = ' and dtecreate <= ' . $this->db->escape($dteend . ' 23:59:00');
        }

        $returnval = '';
        $trIter = 1;
        $strDesc = ($this->sessLang() == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
        $strSQL = "SELECT id,CONCAT('R-', id) AS noRequete, idville, nom, adresse, IsFrom,
                    CONCAT('upload/', idville,'/',fichier) as url, TRIM(`description`) AS `description`,
                    (SELECT typededechet.Nom FROM typededechet WHERE typededechet.id = requetes.matiere) AS matiere,
                    (SELECT COUNT(id) FROM reponses WHERE IsVue = 0 and IsFrom = 0 AND idrequete = requetes.id) as RepVue,
                    (SELECT Count(reponses.id) FROM reponses WHERE reponses.idrequete = requetes.id) as NbRep,
                    (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statut,
                    (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = requetes.statut) AS statCouleur,
                    Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = requetes.idville) as NomVille,
                    createby, left(dtecreate, 10) as dtecreate, dteupdate, left(dteCedule, 10) as dteCedule
                FROM requetes WHERE Iscomplete = 0 and isdeleted = 0 and idville = {$idville} {$istraitees} {$dtestart} {$dteend} ORDER BY id";
        $query = $this->db->query($strSQL);

        foreach ($query->getResult() as $row) {
            $vue = ($row->RepVue > 0) ? '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:red"></i>' : '';
            $discussion = ($row->NbRep < 1) ? '' : '<button type="button"
                class="btn btn-default btn-sm" aria-label="Edit" title="Afficher les donn&eacute;es" onclick="ShowHide(\'tr_' . $trIter . '\')" style="outline:none;">
                ' . $vue . '<i class="fa fa-chevron-down" style="font-size:24px"></i></button>';

            $description = '<label  style="width: 320px;height:60px">' . $row->description . '</label>';
            $nom = ($row->IsFrom == 0)
                ? '<span style="font-weight:bold;font-size:18px;color:red">' . $row->NomVille . '</span>'
                : '<img src="/assets/img/logo.png" width="145" alt="Robert Daoust" />';
            $dteCedule = (trim((string) $row->dteCedule) != '')
                ? '<span style="color:red">Cédulée le ' . $row->dteCedule . '</span>  ' : '';

            $returnval .= '<tr id="trreq_' . $row->id . '" style="line-height:15px;">
                <td style="width:40px;" >' . $discussion . '</td>
                <td style="width:150px;" ><label style="width: 210px;font-wight:bold;">' . $nom . '</label></td>
                <td width="160"><a href="/villes/requete/' . $this->sessLang() . '/' . $row->id . '?from=1"
                    style="width: 220px;">' . $row->noRequete . '</a><br /></td>
                <td width="180"><label style="width: 180px;font-wight:bold;">' . $dteCedule . 'Créée le ' . $row->dtecreate . '</label></td>
                <td><label style="width: 100px;">' . $row->nom . '</label></td>
                <td width="350"><label onclick="ShowInGoogle(\'' . $this->slug($row->adresse) . '\')" style="width: 350px;color:red;cursor:pointer">' . $row->adresse . '</label></td>
                <td width="200"><label class="form-control" style="width:200px">' . $row->matiere . '</label></td>
                <td width="300">' . $description . '</td>
                <td width="200"><label class="form-control" style="width:200px;background-color:' . $row->statCouleur . '">' . $row->statut . '</label></td>
            </tr>' . $this->getReponseInReq($row->id, $trIter);
            $trIter++;
        }
        return $returnval;
    }

    private function getReponseInReq($noRequete, $tr_iter)
    {
        $returnval = '';
        $iter = 1;
        $strDesc = ($this->sessLang() == 'fr') ? 'statutrequete.description_fr' : 'statutrequete.description_en';
        $strSQL = "SELECT id,CONCAT('R-', idrequete) AS noRequete, idville,IsFrom, `description`, left(dteCedule, 10) as dteCedule,
                    (SELECT $strDesc FROM statutrequete WHERE statutrequete.id = reponses.statut) AS NonStatut,
                    (SELECT statutrequete.couleur FROM statutrequete WHERE statutrequete.id = reponses.statut) AS statCouleur,
                    Iscomplete, isdeleted,(SELECT ville.Nom FROM ville WHERE ville.id = reponses.idville) as NomVille,
                    createby, left(dtecreate, 10) as dtecreate, dteupdate, adresse, IsVue, statut,
                    noSerie, couleur, grandeur, matiere
                FROM reponses WHERE idrequete = " . (int) $noRequete . "
                AND Iscomplete = 0 and isdeleted = 0 ORDER BY id";
        $reponse = $this->db->query($strSQL);

        $returnval = '<tr id="tr_' . $tr_iter . '"  style="padding:15px;" > <td colspan="9" >
            <table class="table table-bordered"  >
                <thead>';
        $returnval .= '
              <tr  style="text-align:right;color:darkblue;font-weight:bold;background-color:lightgray">
              <th align="center"></th>
              <th align="center">REQUÉRANT</th>
              <th>DATE CÉDULÉE</th>
              <th>FICHIER</th>
              <th>DESCRIPTION</th>
              <th>ADRESSE / BAC / MATIÈRE</th>
              <th>STATUT</th>
              <th>DATE</th>
              </tr></thead><tbody id="tb_' . $noRequete . '" >';

        foreach ($reponse->getResult() as $row) {
            $isVille = ((int) $row->IsFrom === 0);
            $originLabel = $isVille ? ($row->NomVille ?: 'Ville') : 'RDF';

            $classes = [];
            $classes[] = $isVille ? 'resp-v' : 'resp-rdf';
            if ($isVille && (int) $row->IsVue === 0) {
                $classes[] = 'resp-unseen';
            }
            $classAttr = implode(' ', $classes);

            if ($row->statut == 9) {
                $infoStatAdr = '# Série: ' . $row->noSerie . '<br />Grandeur: ' . $row->grandeur . '<br />Couleur: ' . $row->couleur;
            } elseif ($row->statut == 8) {
                $infoStatAdr = $row->adresse;
            } elseif ($row->statut == 10) {
                $infoStatAdr = $row->matiere;
            } else {
                $infoStatAdr = $row->adresse;
            }

            $returnval .= '<tr class="' . $classAttr . '">'
                . '<td width="20" style="font-weight:bold;text-align:center">' . $iter . '</td>'
                . '<td align="center" style="width:150px;text-align:center;">'
                . '  <span class="badge-origin ' . ($isVille ? 'ville' : 'rdf') . '">' . $originLabel . '</span>'
                . '</td>'
                . '<td style="width:185px"><label style="width: 200px;">' . $this->getAllDate($noRequete, $row->id) . '</label></td>'
                . '<td width="120">' . $this->getResponseFile($row->id) . '</td>'
                . '<td width="350"><label style="width: 350px;min-height:60px;display:inline-block">' . $row->description . '</label></td>'
                . '<td width="200"><label style="width: 220px;">' . $infoStatAdr . '</label></td>'
                . '<td width="160"><label style="width: 160px;min-height:28px;display:inline-block;background-color:' . $row->statCouleur . '">' . $row->NonStatut . '</label></td>'
                . '<td style="width:90px"><label style="width: 90px;">' . $row->dtecreate . '</label></td>'
                . '</tr>';
            $iter++;
        }
        return $returnval . ' </tbody></table> </td></tr>';
    }

    private function getOptStatut()
    {
        $returnval = '';
        // NB : CI3 ordonnait par `description` (colonne absente en
        // local — bug latent reproduit avec description_fr).
        $query = $this->db->query("SELECT id, description_fr, description_en  FROM statutrequete ORDER BY description_fr");
        foreach ($query->getResult() as $row) {
            $strDesc = ($this->sessLang() == 'fr') ? $row->description_fr : $row->description_en;
            $returnval .= '<option  value="' . $row->id . '">' . $strDesc . '</option>';
        }
        return $returnval;
    }

    private function getOptDechets($id = 0)
    {
        $returnval = '';
        $query = $this->db->query("SELECT id, Nom  FROM typededechet ORDER BY Nom");
        foreach ($query->getResult() as $row) {
            $selected = ($row->id == $id) ? 'selected' : '';
            $returnval .= '<option ' . $selected . '  value="' . $row->id . '">' . $row->Nom . '</option>';
        }
        return $returnval;
    }

    /** Marque toutes les requêtes/réponses d'une ville comme vues. */
    public function setRepReqVue($idville)
    {
        $idville = (int) $idville;
        $this->db->query("UPDATE reponses SET IsVue = 1 WHERE idville = {$idville} ");
        $this->db->query("UPDATE requetes SET IsVue = 1 WHERE idville = {$idville} ");
        return $this->response->setStatusCode(200)->setBody('');
    }

    /**
     * Création / modification d'une requête (POST) + jusqu'à 3
     * fichiers joints (upload/{idville}/{id}).
     */
    public function postRequete()
    {
        if (! $this->hasSess('email')) {
            return $this->logout();
        }

        $data = $this->request->getPost();
        $dataResult = ['id' => 0, 'msgerr' => ''];

        $id = (int) ($data['id'] ?? 0);
        if ($id === 0) {
            unset($data['id']);
            $data['createby'] = $this->sess('id');
            $data['idville']  = $this->sess('idville');
            $this->db->table('requetes')->insert($data);

            $error = $this->db->error();
            if (! empty($error['code'])) {
                $dataResult['msgerr'] = ($this->sessLang() == 'fr') ? "Une erreur s'est produite." : 'An error has occurred.';
            } else {
                $dataResult['msgerr'] = ($this->sessLang() == 'fr')
                    ? "Vos changements ont &eacute;t&eacute; enregistr&eacute;s."
                    : 'Your changes have been saved.';
            }
            $dataResult['id'] = $this->db->insertID();
        } else {
            unset($data['id']);
            $this->db->table('requetes')->where('id', $id)->update($data);

            $error = $this->db->error();
            if (! empty($error['code'])) {
                $dataResult['msgerr'] = ($this->sessLang() == 'fr') ? "Une erreur s'est produite." : 'An error has occurred.';
            } else {
                $dataResult['msgerr'] = ($this->sessLang() == 'fr')
                    ? "Vos changements ont &eacute;t&eacute; enregistr&eacute;s."
                    : 'Your changes have been saved.';
            }
            $dataResult['id'] = $id;
        }

        // Fichiers joints (fileRequete_1..3) — comme CI3 upload lib,
        // stockés dans le docroot CI3 : upload/{idville}/{id}
        $idville = (int) $this->sess('idville');
        $relDir  = 'upload/' . $idville . '/' . $dataResult['id'];
        $fsDir   = $this->docroot() . '/' . $relDir;
        if (! is_dir($fsDir)) {
            mkdir($fsDir, 0777, true);
        }

        foreach (['fileRequete_1', 'fileRequete_2', 'fileRequete_3'] as $field) {
            if (empty($_FILES[$field]['name'])) {
                continue;
            }
            if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $fname = str_replace(' ', '_', basename($_FILES[$field]['name']));
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $fsDir . '/' . $fname)) {
                $this->db->query(
                    "INSERT INTO requetes_file (idrequete, createby, fichier)
                     VALUES (" . (int) $dataResult['id'] . ", " . (int) $this->sess('id') . ",
                             " . $this->db->escape('/' . $relDir . '/' . $fname) . ")"
                );
            }
        }

        $this->messageError = $dataResult['msgerr'];
        return $this->getRequete($dataResult['id']);
    }

    private function findVille($id)
    {
        $query = $this->db->query("SELECT Nom FROM ville WHERE id = " . (int) $id)->getRow();
        return $query ? $query->Nom : '';
    }

    /**
     * Ajout d'une réponse citoyenne (POST) + fichiers joints
     * (upload/{idville}/{idrequete}/reponse/{idreponse}).
     */
    public function postReponse()
    {
        if (! $this->hasSess('email')) {
            return $this->logout();
        }

        $data = $this->request->getPost();
        $dataResult = [];

        $adr = (string) ($data['adresse'] ?? '');
        if (trim($adr) != '') {
            $nomVille = $this->findVille((int) ($data['idville'] ?? 0));
            if (strpos(strtolower($adr), strtolower((string) $nomVille)) === false) {
                $data['adresse'] .= ', ' . $nomVille;
            }
        }
        $data['IsFrom']   = 0;
        $idrequete        = (int) ($data['idrequete'] ?? 0);

        $this->db->table('reponses')->insert($data);
        $idreponse = $this->db->insertID();
        $error = $this->db->error();

        if (! empty($error['code'])) {
            $dataResult['msgerr'] = ($this->sessLang() == 'fr') ? "Une erreur s'est produite." : 'An error has occurred.';
        } else {
            $dataResult['msgerr'] = ($this->sessLang() == 'fr')
                ? "Vos changements ont &eacute;t&eacute; enregistr&eacute;s."
                : 'Your changes have been saved.';

            if (trim((string) ($data['adresse'] ?? '')) != '') {
                $adresse = $this->db->escape($data['adresse']);
                $idville = (int) ($data['idville'] ?? 0);
                $this->db->query(
                    "INSERT INTO adrrequete
                     (idrequete, idreponse, adresse, idville, old_adresse)
                     VALUES ($idrequete, $idreponse, $adresse, $idville,
                             (SELECT adresse FROM requetes WHERE id = $idrequete))"
                );
            }
            if (trim((string) ($data['dteCedule'] ?? '')) != '') {
                $dteCedule = $this->db->escape($data['dteCedule']);
                $idville   = (int) ($data['idville'] ?? 0);
                $this->db->query(
                    "INSERT INTO daterequete
                     (dteCedule, idrequete, idreponse, idville)
                     VALUES ($dteCedule, $idrequete, $idreponse, $idville)"
                );
            }
            $statut = (int) ($data['statut'] ?? 0);
            if ($statut != 0) {
                $this->db->query("UPDATE requetes SET statut = $statut WHERE id = $idrequete");
            }
        }

        $dataResult['id']        = $idreponse;
        $dataResult['idrequete'] = $idrequete;

        // Fichiers joints — upload/{idville}/{idrequete}/reponse/{idreponse}
        $idville = (int) ($data['idville'] ?? 0);
        $relDir  = 'upload/' . $idville . '/' . $idrequete . '/reponse/' . $idreponse;
        $fsDir   = $this->docroot() . '/' . $relDir;
        if (! is_dir($fsDir)) {
            mkdir($fsDir, 0777, true);
        }

        foreach (['fileRequete_1', 'fileRequete_2', 'fileRequete_3'] as $field) {
            if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $fname = str_replace(' ', '_', basename($_FILES[$field]['name']));
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $fsDir . '/' . $fname)) {
                // NB : createby = session user_id comme CI3 (null chez
                // les citoyens — reproduit le comportement legacy).
                $this->db->query(
                    "INSERT INTO reponse_files (idreponse, createby, fichier)
                     VALUES ($idreponse, " . $this->db->escape($this->sess('user_id')) . ",
                             " . $this->db->escape('/' . $relDir . '/' . $fname) . ")"
                );
            }
        }

        $this->messageError = $dataResult['msgerr'];
        return $this->getRequete($dataResult['idrequete']);
    }

    private function slug($str)
    {
        $rep = strtolower(
            preg_replace(
                array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'),
                array('', '-', ''),
                $this->removeAccent((string) $str)
            )
        );
        $rep = str_replace('--', '-', $rep);
        $rep = str_replace('--', '-', $rep);
        $rep = str_replace('--', '-', $rep);
        return $rep;
    }

    //=================================================================
    // UPLOADS (summernote)
    //=================================================================

    public function upload()
    {
        if (! empty($_FILES['image']['name']) && empty($_FILES['image']['error'])) {
            $name     = md5(rand(100, 200));
            $ext      = explode('.', $_FILES['image']['name']);
            $filename = $name . '.' . ($ext[1] ?? '');
            $destination = $this->docroot() . '/userfiles/' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            return $this->outText('userfiles/' . $filename);
        }
        if (! empty($_FILES['image']['error'])) {
            return $this->outText(
                'Ooops!  Your upload triggered the following error:  ' . $_FILES['image']['error']
            );
        }
        return $this->response->setStatusCode(200)->setBody('');
    }

    public function summernoteImage()
    {
        if (! empty($_FILES['image']['name']) && empty($_FILES['image']['error'])) {
            $destination = $this->docroot() . '/userfiles/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            return $this->outText(ci3_url('userfiles/' . basename($_FILES['image']['name'])));
        }
        return $this->response->setStatusCode(200)->setBody('');
    }

    //=================================================================
    // LANGUE
    //=================================================================

    public function setLanguage()
    {
        $idiom = (string) $this->request->getPostGet('langue');
        $this->sessSet(['site_lang' => $idiom]);
        return $this->outText((string) $this->sess('last_page_' . $idiom));
    }

    public function current_idville()
    {
        return $this->outJson(['ok' => true, 'idville' => (int) ($this->sess('idville') ?? 0)]);
    }

    //=================================================================
    // ZONES (carte)
    //=================================================================

    /**
     * Données de la carte zones (from=1 : retourne le tableau de
     * données pour la vue ; sinon rend la vue directement).
     */
    private function navZoneData($from = 0)
    {
        $data = ['ActiveDate' => date('Y-m-d')];

        $idParam = $this->request->getGet('idville');
        if ($idParam !== null && is_numeric($idParam)) {
            $idville = (int) $idParam;
            $this->sessSet(['idville' => $idville]);
        } else {
            $idville = $this->sess('idville');
        }

        $dte = (string) $this->request->getGet('dte');
        if (! empty($dte) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dte)) {
            $data['ActiveDate'] = $dte;
        }

        $rows = $this->svc->getZonesByVille($idville);
        $features = [];
        foreach ($rows as $r) {
            $f = json_decode($r['geojson'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($f)) {
                $f['properties'] = isset($f['properties']) && is_array($f['properties']) ? $f['properties'] : [];
                $f['properties']['id'] = (int) $r['id'];
                $f['properties']['name'] = $r['name'] ?? null;
                $features[] = $f;
            }
        }
        $data['zonesFeatures'] = $features;
        $data['IdVille'] = is_numeric($idville) ? (int) $idville : 0;
        $data['Lang'] = $this->sessLang() ?: 'fr';

        // Points de collecte (photos) pour la date/ville actives
        $data['CollectePoints'] = [];
        if (! empty($data['IdVille']) && ! empty($data['ActiveDate'])) {
            $data['CollectePoints'] = $this->db->query(
                "SELECT id, address, note, date_collecte, type_ramassage, ville, id_ville,
                        no_camion, id_chauffeur, Concat( photo_path, original_filename) as photo_path,
                        original_filename, latitude, longitude, created_at
                 FROM collecte_photos
                 WHERE date_collecte = ? AND id_ville = ?
                   AND latitude IS NOT NULL AND longitude IS NOT NULL
                 ORDER BY created_at DESC",
                [$data['ActiveDate'], $data['IdVille']]
            )->getResultArray();
        }

        $userType = (string) $this->request->getGet('userType');
        $data['IsSupervisor'] = ($userType === 'supervisor' || $userType === 'admin') ? 1 : 0;

        return $data;
    }

    public function nav_zone($from = 0)
    {
        $data = $this->navZoneData($from);
        return $this->outPage(view('villes/leaflet/map_nav_zone', $data));
    }

    /** API JSON : polygones de zones pour une ville. */
    public function zones_api()
    {
        $idville = (int) $this->request->getGet('idville');
        if ($idville <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'idville requis']);
        }
        $rows = $this->svc->getZonesByVille($idville);
        $features = [];
        foreach ($rows as $r) {
            $f = json_decode($r['geojson'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($f)) {
                $f['properties'] = isset($f['properties']) && is_array($f['properties']) ? $f['properties'] : [];
                $f['properties']['id'] = (int) $r['id'];
                $f['properties']['name'] = $r['name'] ?? null;
                $features[] = $f;
            }
        }
        return $this->outJson(['ok' => true, 'data' => $features]);
    }

    /** Enregistre un parcours dessiné (polilyne_ville). */
    public function save_drawn_polyline()
    {
        ini_set('memory_limit', '256M');
        if (strtoupper($this->request->getMethod(true)) !== 'POST') {
            return $this->outJson(['ok' => false, 'error' => 'Méthode non autorisée'], 405);
        }

        $payload = json_decode((string) $this->request->getBody(), true);
        if (! is_array($payload)) {
            return $this->outJson(['ok' => false, 'error' => 'Requête invalide'], 400);
        }

        $idville_raw    = (int) ($payload['idville'] ?? 0);
        $nom_raw        = trim((string) ($payload['nom'] ?? ''));
        $idsecteur_raw  = (int) ($payload['idsecteur'] ?? 0);
        $coords         = isset($payload['coords']) && is_array($payload['coords']) ? $payload['coords'] : [];

        if ($idville_raw <= 0 || $nom_raw === '' || count($coords) < 2) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres insuffisants'], 400);
        }
        if (count($coords) > 20000) {
            return $this->outJson(['ok' => false, 'error' => 'Trop de points (max 20000)'], 400);
        }

        $points = [];
        foreach ($coords as $pt) {
            if (! is_array($pt) || ! isset($pt['lat'], $pt['lng'])) {
                continue;
            }
            $points[] = number_format((float) $pt['lat'], 6, '.', '') . ',' . number_format((float) $pt['lng'], 6, '.', '');
        }
        if (count($points) < 2) {
            return $this->outJson(['ok' => false, 'error' => 'Coordonnées insuffisantes'], 400);
        }

        $point = implode('_', $points);
        $this->db->query(
            "INSERT INTO polilyne_ville (idville, idsecteur, nom, parcours)
             VALUES (" . (int) $idville_raw . ", " . (int) $idsecteur_raw . ",
                     " . $this->db->escape($nom_raw) . ", " . $this->db->escape($point) . ")"
        );
        if ($this->db->affectedRows() <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'Erreur lors de l\'enregistrement'], 500);
        }
        return $this->outJson(['ok' => true, 'message' => 'Parcours enregistré']);
    }

    /** Points de collecte (photos GPS) pour une date + ville. */
    public function collecte_points()
    {
        $dte     = (string) $this->request->getGet('dte');
        $idville = (int) $this->request->getGet('idville');

        if (! $dte || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dte) || $idville <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres invalides']);
        }

        $rows = $this->db->query(
            "SELECT id, address, note, date_collecte, type_ramassage, ville, id_ville,
                    no_camion, id_chauffeur, photo_path, original_filename,
                    latitude, longitude, created_at
             FROM collecte_photos
             WHERE date_collecte = ? AND id_ville = ?
               AND latitude IS NOT NULL AND longitude IS NOT NULL
             ORDER BY created_at DESC",
            [$dte, $idville]
        )->getResultArray();

        return $this->outJson(['ok' => true, 'data' => $rows]);
    }

    /** Enregistre un polygone (geojson Feature Polygon). */
    public function saveZone()
    {
        $name        = trim((string) $this->request->getPost('name'));
        $geojsonRaw  = $this->request->getPost('geojson');
        if ($geojsonRaw === null) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètre manquant: geojson'], 400);
        }
        $feature = json_decode($geojsonRaw, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($feature)) {
            return $this->outJson(['ok' => false, 'error' => 'GeoJSON invalide'], 422);
        }
        $type  = $feature['type'] ?? '';
        $geom  = $feature['geometry'] ?? null;
        $gType = $geom['type'] ?? '';
        $coords = $geom['coordinates'] ?? null;
        if ($type !== 'Feature' || $gType !== 'Polygon' || ! is_array($coords) || count($coords) === 0) {
            return $this->outJson(['ok' => false, 'error' => 'Le GeoJSON doit être un Feature de type Polygon'], 422);
        }

        $idville = $this->sess('idville');
        $res = $this->svc->insertZone($name !== '' ? $name : null, $geojsonRaw, $idville, $this->sess('user_id'));
        if (! $res['ok']) {
            return $this->outJson(['ok' => false, 'error' => $res['error'] ?? 'Erreur BD'], 500);
        }
        return $this->outJson(['ok' => true, 'id' => $res['id']]);
    }

    /** Hub « bureau » : liste des villes de la cédule du jour. */
    public function NavZoneHub($lang = 'fr')
    {
        // Anti-cache
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0');

        $activeDate = (string) $this->request->getGet('dte');
        if (! $activeDate) {
            $activeDate = date('Y-m-d');
        }

        $rows = $this->db->query(
            "SELECT DISTINCT c.idvilles,
                    (SELECT v.Nom FROM ville v WHERE v.id = c.idvilles) AS NomVille
               FROM cedules c
              WHERE c.DateJour = " . $this->db->escape($activeDate) . "
                AND c.isremove = 0
           ORDER BY NomVille"
        )->getResultArray();

        $data = [
            'ActiveDate'  => $activeDate,
            'Lang'        => $lang,
            'Cities'      => $rows,
            'NoCamion'    => (string) ($this->request->getGet('nocamion') ?: ''),
            'IdChauffeur' => (string) ($this->request->getGet('idchauffeur') ?: ''),
            'IdVilleAdmin' => (int) ($this->request->getGet('idville') ?: 0),
        ];
        return $this->outPage(view('villes/leaflet/map_nav_zone_hub', $data));
    }

    //=================================================================
    // TAGS DE COLLECTE (photos)
    //=================================================================

    public function createCollecteTag()
    {
        $photoId = (int) $this->request->getPost('photo_id');
        $tagCode = trim((string) $this->request->getPost('tag_code'));
        $idVille = (int) $this->request->getPost('id_ville');
        $status  = (string) $this->request->getPost('status');

        if ($photoId <= 0 || $idVille <= 0 || $tagCode === '') {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres invalides', 'received' => $_POST]);
        }

        $row = $this->db->query(
            "SELECT id FROM collecte_photos WHERE id = ? AND (id_ville = ? OR id_ville IS NULL)",
            [$photoId, $idVille]
        )->getRowArray();
        if (! $row) {
            return $this->outJson(['ok' => false, 'error' => 'Photo introuvable pour cette ville', 'received' => $_POST]);
        }

        // Forcer VALIDER pour chauffeurs non-admin côté serveur
        $isAdmin = ($this->sess('isadmin') === '1');
        if (! $isAdmin) {
            $status = 'VALIDER';
        } else {
            if (empty($status)) {
                $status = 'PENDING';
            }
        }

        log_message('debug', 'createCollecteTag -> final status: ' . $status . ' POST: ' . json_encode($_POST));

        $ins = [
            'id_collecte_photo' => $photoId,
            'id_ville'    => $idVille,
            'tag_code'    => $tagCode,
            'status'      => $status,
        ];
        if (! $this->db->table('collecte_tags')->insert($ins)) {
            $err = $this->db->error();
            return $this->outJson(['ok' => false, 'error' => $err['message'] ?? 'Erreur BD', 'received' => $_POST]);
        }

        return $this->outJson([
            'ok'       => true,
            'id'       => $this->db->insertID(),
            'status'   => $status,
            'received' => $_POST,
        ]);
    }

    public function respondCollecteTag()
    {
        $tagCode = trim((string) $this->request->getPost('tag_code'));
        $status  = strtoupper(trim((string) $this->request->getPost('status')));
        $respTxt = trim((string) $this->request->getPost('response_text'));
        $idVille = (int) $this->request->getPost('id_ville');

        if ($tagCode === '' || $idVille <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres invalides']);
        }
        if (! in_array($status, ['ACK', 'DONE', 'REJECTED'], true)) {
            return $this->outJson(['ok' => false, 'error' => 'Statut invalide']);
        }

        $tag = $this->db->query(
            "SELECT id, status FROM collecte_tags WHERE tag_code = ? AND id_ville = ?",
            [$tagCode, $idVille]
        )->getRowArray();
        if (! $tag) {
            return $this->outJson(['ok' => false, 'error' => 'Tag introuvable']);
        }

        $this->db->table('collecte_tags')->where('id', $tag['id'])->update([
            'status'        => $status,
            'response_text' => $respTxt !== '' ? $respTxt : null,
            'responded_at'  => date('Y-m-d H:i:s'),
        ]);
        return $this->outJson(['ok' => true]);
    }

    public function listCollecteTags()
    {
        $photoId = (int) $this->request->getGet('photo_id');
        $idVille = (int) $this->request->getGet('id_ville');
        if ($photoId <= 0 || $idVille <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres invalides']);
        }
        $rows = $this->db->query(
            "SELECT id, tag_code, status, response_text, responded_at, created_at
             FROM collecte_tags
             WHERE id_collecte_photo = ? AND id_ville = ?
             ORDER BY created_at DESC",
            [$photoId, $idVille]
        )->getResultArray();
        return $this->outJson(['ok' => true, 'data' => $rows]);
    }

    //=================================================================
    // TAGS DE CARTE (map_tags)
    //=================================================================

    public function maptag_chauffeurs()
    {
        $rows = $this->db->query(
            "SELECT id, Prenom, Nom, login FROM reshumaine ORDER BY Nom, Prenom"
        )->getResult();
        $out = [];
        foreach ($rows as $r) {
            $name = trim(((string) ($r->Prenom ?? '')) . ' ' . ((string) ($r->Nom ?? '')));
            if ($name === '') {
                $name = (string) ($r->login ?? ('#' . (int) $r->id));
            }
            $out[] = ['id' => (int) $r->id, 'name' => $name];
        }
        return $this->outJson(['ok' => true, 'data' => $out]);
    }

    public function createMapTag()
    {
        $idVille = (int) $this->request->getPost('id_ville');
        if ($idVille <= 0) {
            $g = $this->request->getGet('id_ville');
            if ($g !== null && is_numeric($g)) {
                $idVille = (int) $g;
            }
        }
        $addrText   = trim((string) $this->request->getPost('address_text'));
        $lat        = (float) $this->request->getPost('lat');
        $lng        = (float) $this->request->getPost('lng');
        $reason     = trim((string) $this->request->getPost('reason'));
        $desc       = trim((string) $this->request->getPost('description'));
        $title      = trim((string) $this->request->getPost('title'));
        $idChauffeur = (int) $this->request->getPost('id_chauffeur');
        $status     = strtoupper(trim((string) $this->request->getPost('status')));

        if (! in_array($status, ['PENDING', 'VALIDER'], true)) {
            $status = 'PENDING';
        }
        if ($idChauffeur <= 0) {
            $idChauffeur = null;
        }

        log_message('debug', 'createMapTag received POST: ' . print_r($_POST, true));
        log_message('debug', 'createMapTag received FILES: ' . print_r($_FILES, true));

        if ($idVille <= 0) {
            $popupV = trim((string) $this->request->getPost('popup_id_ville'));
            if ($popupV !== '' && is_numeric($popupV)) {
                $idVille = (int) $popupV;
            }
        }
        if ($idVille <= 0) {
            $g = $this->request->getGet('id_ville');
            if ($g !== null && is_numeric($g)) {
                $idVille = (int) $g;
            }
        }
        if ($idVille <= 0) {
            $sessId = (int) ($this->sess('idville') ?? 0);
            if ($sessId > 0) {
                $idVille = $sessId;
            }
        }

        if ($idVille <= 0 || ! is_numeric($lat) || ! is_numeric($lng)) {
            return $this->outJson([
                'ok'      => false,
                'error'   => 'Paramètres invalides',
                'received' => ['post' => $_POST, 'files' => array_keys($_FILES)],
            ]);
        }

        // Image optionnelle — upload/sanivan/tags/{ville}/{date}/
        $imagePath = null;
        if (! empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $baseDir = 'upload/sanivan/tags/' . $idVille . '/' . date('Y-m-d') . '/';
            $fsDir   = $this->docroot() . '/' . $baseDir;

            if (! is_dir($fsDir) && ! mkdir($fsDir, 0755, true)) {
                return $this->outJson(['ok' => false, 'error' => 'Impossible de créer le dossier']);
            }

            $orig = basename($_FILES['image']['name']);
            $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                return $this->outJson(['ok' => false, 'error' => 'Extension image non autorisée']);
            }

            $fname = 'tag_' . uniqid('', true) . '.' . $ext;
            if (! move_uploaded_file($_FILES['image']['tmp_name'], $fsDir . $fname)) {
                return $this->outJson(['ok' => false, 'error' => 'Échec upload image']);
            }
            $imagePath = $baseDir . $fname;
        }

        $tagCode = bin2hex(random_bytes(8));

        $ins = [
            'id_ville'     => $idVille,
            'tag_code'     => $tagCode,
            'title'        => $title !== '' ? $title : null,
            'address_text' => $addrText ?: null,
            'latitude'     => $lat,
            'longitude'    => $lng,
            'reason'       => $reason ?: null,
            'description'  => $desc ?: null,
            'image_path'   => $imagePath,
            'status'       => $status,
            'id_chauffeur' => $idChauffeur,
        ];

        if (! $this->db->table('map_tags')->insert($ins)) {
            $err = $this->db->error();
            return $this->outJson(['ok' => false, 'error' => $err['message'] ?? 'Erreur BD']);
        }

        return $this->outJson([
            'ok'   => true,
            'data' => [
                'id'           => (int) $this->db->insertID(),
                'id_ville'     => $idVille,
                'tag_code'     => $tagCode,
                'title'        => $ins['title'],
                'address_text' => $addrText,
                'latitude'     => $lat,
                'longitude'    => $lng,
                'reason'       => $reason,
                'description'  => $desc,
                'image_path'   => $imagePath,
                'status'       => $status,
                'id_chauffeur' => $idChauffeur,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function maptag_update()
    {
        $id = (int) $this->request->getPost('id');
        if ($id <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'id invalide']);
        }

        $data = [];
        if ($this->request->getPost('address_text') !== null) {
            $data['address_text'] = $this->request->getPost('address_text');
        }
        if ($this->request->getPost('reason') !== null) {
            $data['reason'] = $this->request->getPost('reason');
        }
        if ($this->request->getPost('description') !== null) {
            $data['description'] = $this->request->getPost('description');
        }
        if ($this->request->getPost('id_chauffeur') !== null) {
            $v = $this->request->getPost('id_chauffeur');
            $data['id_chauffeur'] = ($v === '' ? null : (int) $v);
        }
        if ($this->request->getPost('title') !== null) {
            $data['title'] = $this->request->getPost('title') ?: null;
        }

        if (empty($data)) {
            return $this->outJson(['ok' => false, 'error' => 'Aucune donnée à mettre à jour']);
        }

        $this->db->table('map_tags')->where('id', $id)->update($data);
        return $this->outJson(['ok' => true]);
    }

    /**
     * Liste des tags d'une ville (open=1 : non terminés ;
     * dte=YYYY-MM-DD : du jour). Injecte les discussions
     * (map_tag_responses) si la table existe.
     */
    public function listMapTags()
    {
        $idVille = (int) $this->request->getGet('idville');
        $dte     = (string) $this->request->getGet('dte');
        $open    = (int) $this->request->getGet('open');

        if ($idVille <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'idville requis']);
        }

        $cols = 'id, id_ville, tag_code, title, address_text, latitude, longitude, reason,
                 description, image_path, status, response_text, responded_at, created_at, updated_at, id_chauffeur';

        if ($open === 1) {
            $rows = $this->db->query(
                "SELECT $cols FROM map_tags WHERE id_ville = ? AND status <> 'DONE' ORDER BY created_at DESC",
                [$idVille]
            )->getResultArray();
        } elseif ($dte && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dte)) {
            $rows = $this->db->query(
                "SELECT $cols FROM map_tags WHERE id_ville = ? AND DATE(created_at) = ? ORDER BY created_at DESC",
                [$idVille, $dte]
            )->getResultArray();
        } else {
            $rows = $this->db->query(
                "SELECT $cols FROM map_tags WHERE id_ville = ? ORDER BY created_at DESC",
                [$idVille]
            )->getResultArray();
        }

        $rows = $this->injectDiscussions($rows);

        return $this->outJson(['ok' => true, 'data' => $rows]);
    }

    /** Tous les tags ouverts (toutes villes) + discussions. */
    public function listAllOpenMapTags()
    {
        $rows = $this->db->query(
            "SELECT id, id_ville, tag_code, title, address_text, latitude, longitude, reason,
                    description, image_path, status, response_text, responded_at, created_at, updated_at, id_chauffeur
             FROM map_tags WHERE status <> 'DONE' ORDER BY created_at DESC"
        )->getResultArray();

        $rows = $this->injectDiscussions($rows);

        return $this->outJson(['ok' => true, 'data' => $rows]);
    }

    /** Attache les discussions (map_tag_responses) aux tags. */
    private function injectDiscussions(array $rows): array
    {
        if (empty($rows) || ! $this->svc->tableExists('map_tag_responses')) {
            return $rows;
        }

        $tagIds = array_values(array_unique(array_filter(array_map(static fn($r) => (int) $r['id'], $rows))));
        if (empty($tagIds)) {
            return $rows;
        }

        $in = implode(',', array_map('intval', $tagIds));
        $respRows = $this->db->query(
            "SELECT r.id, r.id_map_tag, r.id_ville, r.status, r.response_text, r.created_at, r.created_by
             FROM map_tag_responses r
             WHERE r.id_map_tag IN ($in)
             ORDER BY r.created_at ASC, r.id ASC"
        )->getResultArray();

        $authors = [];
        $authorIds = array_values(array_unique(array_filter(array_map(static fn($r) => (int) ($r['created_by'] ?? 0), $respRows))));
        if (! empty($authorIds)) {
            $inA = implode(',', array_map('intval', $authorIds));
            $aRows = $this->db->query(
                "SELECT id, CONCAT(COALESCE(prenom,''),' ',COALESCE(nom,'')) AS NomComplet
                 FROM villesrecipiendaire WHERE id IN ($inA)"
            )->getResultArray();
            foreach ($aRows as $a) {
                $authors[(int) $a['id']] = trim((string) $a['NomComplet']);
            }
        }

        $byTag = [];
        foreach ($respRows as $rr) {
            $byTag[(int) $rr['id_map_tag']][] = [
                'text'   => (string) ($rr['response_text'] ?? ''),
                'author' => (string) ($authors[(int) ($rr['created_by'] ?? 0)] ?? ''),
                'time'   => (string) ($rr['created_at'] ?? ''),
                'status' => (string) ($rr['status'] ?? ''),
            ];
        }

        foreach ($rows as &$row) {
            $row['discussions'] = $byTag[(int) $row['id']] ?? [];
        }
        unset($row);
        return $rows;
    }

    /** Raisons des tags (map_tag_reasons) selon la langue. */
    public function mapTagReasons()
    {
        $rows = $this->svc->tableExists('map_tag_reasons') ? $this->svc->getReasons(true) : [];

        if (! empty($rows)) {
            $lang = $this->ci3->has('site_lang') ? $this->sessLang() : 'fr';
            $isFr = (strtolower($lang) === 'fr');
            $list = array_map(function ($r) use ($isFr) {
                return $isFr ? (string) $r['label_fr'] : (string) $r['label_en'];
            }, $rows);
            return $this->outJson(['ok' => true, 'data' => $list]);
        }

        // Fallback vers la config (application/config/map_tags.php CI3)
        $reasons = config('MapTags')->map_tag_reasons ?? null;
        if (! is_array($reasons)) {
            return $this->outJson(['ok' => false, 'error' => 'Raisons non disponibles']);
        }
        return $this->outJson(['ok' => true, 'data' => $reasons]);
    }

    /** Répondre à un tag : statut + texte + historisation. */
    public function respondMapTag()
    {
        $id      = (int) $this->request->getPost('id');
        $status  = strtoupper(trim((string) $this->request->getPost('status')));
        $respTxt = trim((string) $this->request->getPost('response_text'));

        if ($id <= 0 || ! in_array($status, ['ACK', 'DONE', 'REJECTED'], true)) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres invalides']);
        }

        $tag = $this->db->query("SELECT id, id_ville FROM map_tags WHERE id = ?", [$id])->getRowArray();
        if (! $tag) {
            return $this->outJson(['ok' => false, 'error' => 'Tag introuvable']);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('map_tags')->where('id', $id)->update([
            'status'        => $status,
            'response_text' => ($respTxt !== '') ? $respTxt : null,
            'responded_at'  => $now,
        ]);

        if ($this->svc->tableExists('map_tag_responses')) {
            try {
                $this->db->table('map_tag_responses')->insert([
                    'id_map_tag'    => $id,
                    'id_ville'      => (int) ($tag['id_ville'] ?? 0),
                    'status'        => $status,
                    'response_text' => ($respTxt !== '') ? $respTxt : null,
                    'created_at'    => $now,
                    'created_by'    => $this->sess('id') ?? null,
                ]);
            } catch (\Throwable $e) {
                // ne bloque pas la réponse si l'historisation échoue
            }
        }
        return $this->outJson(['ok' => true]);
    }

    /** Mise à jour d'un tag (champs fournis seulement). */
    public function updateMapTag()
    {
        $id      = (int) $this->request->getPost('id');
        $idVille = (int) $this->request->getPost('id_ville');

        if ($id <= 0 || $idVille <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'Paramètres invalides']);
        }

        $tag = $this->db->query(
            "SELECT id, id_ville FROM map_tags WHERE id = ? AND id_ville = ?",
            [$id, $idVille]
        )->getRowArray();
        if (! $tag) {
            return $this->outJson(['ok' => false, 'error' => 'Tag introuvable']);
        }

        $upd = [];
        foreach (['address_text', 'reason', 'description', 'title'] as $field) {
            if ($this->request->getPost($field) !== null) {
                $v = trim((string) $this->request->getPost($field));
                $upd[$field] = ($v !== '') ? $v : null;
            }
        }
        if ($this->request->getPost('id_chauffeur') !== null) {
            $v = $this->request->getPost('id_chauffeur');
            $upd['id_chauffeur'] = ($v === '' ? null : (int) $v);
        }

        if (empty($upd)) {
            return $this->outJson(['ok' => false, 'error' => 'Aucune donnée à mettre à jour']);
        }

        $this->db->table('map_tags')->where('id', $id)->where('id_ville', $idVille)->update($upd);

        $updated = $this->db->query(
            "SELECT id, id_ville, tag_code, title, address_text, latitude, longitude, reason,
                    description, image_path, status, response_text, responded_at, created_at, updated_at, id_chauffeur
             FROM map_tags WHERE id = ? AND id_ville = ?",
            [$id, $idVille]
        )->getRowArray();

        return $this->outJson(['ok' => true, 'data' => $updated]);
    }

    /** Page « Tags » de la ville (citoyen connecté). */
    public function tagsVille()
    {
        if (! $this->hasSess('email')) {
            return $this->index();
        }
        $idville = (int) ($this->request->getGet('idville') ?? $this->sess('idville'));
        $lang    = $this->ci3->has('site_lang') ? $this->sessLang() : 'fr';

        $data = [
            'IdVille'    => $idville,
            'Lang'       => $lang,
            'ActiveDate' => date('Y-m-d'),
            'menu'       => view('villes/inc_menu_2', []),
        ];
        $this->sessSet(['last_page_' . $lang => '/villes/tags/' . $lang]);
        return $this->outPage(view('villes/map_tags_list', $data));
    }

      private function removeAccent(string $str): string
      {
        $a = array(
        'À',
        'Á',
        'Â',
        'Ã',
        'Ä',
        'Å',
        'Æ',
        'Ç',
        'È',
        'É',
        'Ê',
        'Ë',
        'Ì',
        'Í',
        'Î',
        'Ï',
        'Ð',
        'Ñ',
        'Ò',
        'Ó',
        'Ô',
        'Õ',
        'Ö',
        'Ø',
        'Ù',
        'Ú',
        'Û',
        'Ü',
        'Ý',
        'ß',
        'à',
        'á',
        'â',
        'ã',
        'ä',
        'å',
        'æ',
        'ç',
        'è',
        'é',
        'ê',
        'ë',
        'ì',
        'í',
        'î',
        'ï',
        'ñ',
        'ò',
        'ó',
        'ô',
        'õ',
        'ö',
        'ø',
        'ù',
        'ú',
        'û',
        'ü',
        'ý',
        'ÿ',
        'Ā',
        'ā',
        'Ă',
        'ă',
        'Ą',
        'ą',
        'Ć',
        'ć',
        'Ĉ',
        'ĉ',
        'Ċ',
        'ċ',
        'Č',
        'č',
        'Ď',
        'ď',
        'Đ',
        'đ',
        'Ē',
        'ē',
        'Ĕ',
        'ĕ',
        'Ė',
        'ė',
        'Ę',
        'ę',
        'Ě',
        'ě',
        'Ĝ',
        'ĝ',
        'Ğ',
        'ğ',
        'Ġ',
        'ġ',
        'Ģ',
        'ģ',
        'Ĥ',
        'ĥ',
        'Ħ',
        'ħ',
        'Ĩ',
        'ĩ',
        'Ī',
        'ī',
        'Ĭ',
        'ĭ',
        'Į',
        'į',
        'İ',
        'ı',
        'Ĳ',
        'ĳ',
        'Ĵ',
        'ĵ',
        'Ķ',
        'ķ',
        'Ĺ',
        'ĺ',
        'Ļ',
        'ļ',
        'Ľ',
        'ľ',
        'Ŀ',
        'ŀ',
        'Ł',
        'ł',
        'Ń',
        'ń',
        'Ņ',
        'ņ',
        'Ň',
        'ň',
        'ŉ',
        'Ō',
        'ō',
        'Ŏ',
        'ŏ',
        'Ő',
        'ő',
        'Œ',
        'œ',
        'Ŕ',
        'ŕ',
        'Ŗ',
        'ŗ',
        'Ř',
        'ř',
        'Ś',
        'ś',
        'Ŝ',
        'ŝ',
        'Ş',
        'ş',
        'Š',
        'š',
        'Ţ',
        'ţ',
        'Ť',
        'ť',
        'Ŧ',
        'ŧ',
        'Ũ',
        'ũ',
        'Ū',
        'ū',
        'Ŭ',
        'ŭ',
        'Ů',
        'ů',
        'Ű',
        'ű',
        'Ų',
        'ų',
        'Ŵ',
        'ŵ',
        'Ŷ',
        'ŷ',
        'Ÿ',
        'Ź',
        'ź',
        'Ż',
        'ż',
        'Ž',
        'ž',
        'ſ',
        'ƒ',
        'Ơ',
        'ơ',
        'Ư',
        'ư',
        'Ǎ',
        'ǎ',
        'Ǐ',
        'ǐ',
        'Ǒ',
        'ǒ',
        'Ǔ',
        'ǔ',
        'Ǖ',
        'ǖ',
        'Ǘ',
        'ǘ',
        'Ǚ',
        'ǚ',
        'Ǜ',
        'ǜ',
        'Ǻ',
        'ǻ',
        'Ǽ',
        'ǽ',
        'Ǿ',
        'ǿ'
        );

        $b = array(
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'AE',
        'C',
        'E',
        'E',
        'E',
        'E',
        'I',
        'I',
        'I',
        'I',
        'D',
        'N',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'U',
        'U',
        'U',
        'U',
        'Y',
        's',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'ae',
        'c',
        'e',
        'e',
        'e',
        'e',
        'i',
        'i',
        'i',
        'i',
        'n',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'u',
        'u',
        'u',
        'u',
        'y',
        'y',
        'A',
        'a',
        'A',
        'a',
        'A',
        'a',
        'C',
        'c',
        'C',
        'c',
        'C',
        'c',
        'C',
        'c',
        'D',
        'd',
        'D',
        'd',
        'E',
        'e',
        'E',
        'e',
        'E',
        'e',
        'E',
        'e',
        'E',
        'e',
        'G',
        'g',
        'G',
        'g',
        'G',
        'g',
        'G',
        'g',
        'H',
        'h',
        'H',
        'h',
        'I',
        'i',
        'I',
        'i',
        'I',
        'i',
        'I',
        'i',
        'I',
        'i',
        'IJ',
        'ij',
        'J',
        'j',
        'K',
        'k',
        'L',
        'l',
        'L',
        'l',
        'L',
        'l',
        'L',
        'l',
        'L',
        'l',
        'N',
        'n',
        'N',
        'n',
        'N',
        'n',
        'n',
        'O',
        'o',
        'O',
        'o',
        'O',
        'o',
        'OE',
        'oe',
        'R',
        'r',
        'R',
        'r',
        'R',
        'r',
        'S',
        's',
        'S',
        's',
        'S',
        's',
        'S',
        's',
        'T',
        't',
        'T',
        't',
        'T',
        't',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'W',
        'w',
        'Y',
        'y',
        'Y',
        'Z',
        'z',
        'Z',
        'z',
        'Z',
        'z',
        's',
        'f',
        'O',
        'o',
        'U',
        'u',
        'A',
        'a',
        'I',
        'i',
        'O',
        'o',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'U',
        'u',
        'A',
        'a',
        'AE',
        'ae',
        'O',
        'o'
        );
        return str_replace($a, $b, $str);
      }
}
