<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use App\Libraries\SamsaraClient;
use App\Libraries\WebChauffeurService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Port de Co_web_chauffeur (CI3, ~2 240 lignes) – interface web
 * navigateur pour chauffeurs SaniVan.
 *
 * Session : clés web_* dans la session CI3 partagée (Ci3Session).
 * Sur l'hôte CI4, le login/bootstrap MAUI CRÉE la session via
 * Ci3Session::create() (le cookie CI3 n'arrive jamais ici).
 *
 * Uploads (tickets/PDF) : écrits dans le docroot CI3 (env ci3.webroot)
 * — les URLs /uploads/... retournées restent servies par CI3 en prod
 * (Alias vhost /uploads localement, voir AGENTS.md).
 *
 * Divergences volontaires par rapport à CI3 :
 *  - nav_bureau : redirige vers /admin/connexion (CI3 hardcodait des
 *    identifiants en clair dans l'URL — non reproduits dans le repo)
 *  - api_get_polyline_dynamique : route AJOUTÉE (le JS de truckmap
 *    l'appelle mais CI3 n'avait pas de route — bug latent corrigé)
 *  - api_samsara_diag / api_autorisation_spectateur : non portés
 *    (jamais routés en CI3, jamais appelés par les vues)
 */
class WebChauffeur extends BaseController
{
    private Ci3Session $ci3;
    private WebChauffeurService $web;
    private SamsaraClient $samsara;

    public function __construct()
    {
        date_default_timezone_set('America/Toronto');
        $this->ci3     = new Ci3Session();
        $this->web     = new WebChauffeurService();
        $this->samsara = new SamsaraClient();
    }

    //=================================================================
    // Helpers session (clés web_* au format CI3)
    //=================================================================

    /** Lit une clé de session CI3. */
    private function sess(string $key)
    {
        return $this->ci3->get($key);
    }

    /**
     * Garantit qu'une session CI3 existe avant des set() — sinon les
     * écritures sont des no-ops (voir AGENTS.md § TabletteSani).
     */
    private function ensureSession(): void
    {
        if ($this->ci3->user() === null) {
            $this->ci3->create([]);
        }
    }

    private function sessSet(array $kvs): void
    {
        $this->ensureSession();
        foreach ($kvs as $k => $v) {
            $this->ci3->set($k, $v);
        }
    }

    /**
     * Flashdata CI3 (message une seule requête) : paire set/remove
     * sur la clé web_flash_error.
     */
    private function flashError(string $msg): void
    {
        $this->sessSet(['web_flash_error' => $msg]);
    }

    private function flashTake(): string
    {
        $msg = (string) ($this->ci3->get('web_flash_error') ?? '');
        if ($msg !== '') {
            $this->ci3->remove('web_flash_error');
        }
        return $msg;
    }

    //=================================================================
    // Helpers réponse
    //=================================================================

    private function cors(): static
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $this;
    }

    private function noCache(): void
    {
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    private function outJson($data, int $code = 200): ResponseInterface
    {
        return $this->cors()->response
            ->setStatusCode($code)
            ->setContentType('application/json', 'utf-8')
            ->setBody(is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function outPage(string $body): ResponseInterface
    {
        return $this->cors()->response
            ->setStatusCode(200)
            ->setContentType('text/html', 'utf-8')
            ->setBody($body);
    }

    //=================================================================
    // Auth
    //=================================================================

    /**
     * Vérifie si l'usager est connecté, sinon redirige au login.
     * Retourne null si OK, une ResponseInterface de redirection sinon.
     */
    private function checkAuth(): ?ResponseInterface
    {
        if (! $this->sess('web_logged_in')) {
            $this->noCache();
            return redirect()->to('/ramassage-ville');
        }
        $this->noCache();

        // Heartbeat : signale que ce camion web est actif (throttlé à 60s)
        $this->registerWebHeartbeat();

        return null;
    }

    /**
     * Enregistre le heartbeat du chauffeur web actif dans
     * web_chauffeur_active. Throttlé à 60s via session.
     */
    private function registerWebHeartbeat(): void
    {
        $lastBeat = (int) $this->sess('web_heartbeat_ts');
        if ((time() - $lastBeat) < 60) {
            return;
        }

        $nocamion    = $this->sess('web_nocamion');
        $idchauffeur = $this->sess('web_id_chauffeur');
        $vehicleId   = $this->sess('web_id_samsara');

        if (! empty($nocamion)) {
            $this->web->registerWebHeartbeat(
                $nocamion,
                $idchauffeur ? $idchauffeur : 0,
                $vehicleId ? $vehicleId : ''
            );
            $this->sessSet(['web_heartbeat_ts' => time()]);
        }
    }

    //=================================================================
    // AUTH (pages)
    //=================================================================

    /** Page de connexion. Route CI3 : ramassage-ville. */
    public function login()
    {
        if ($this->sess('web_logged_in')) {
            return redirect()->to('/web/dashboard');
        }
        return $this->outPage(view('web_chauffeur/login', [
            'error' => $this->flashTake(),
        ]));
    }

    /**
     * Traitement du formulaire de login.
     * Reproduit le flux MAUI MainPage.OnButtonClicked → GetDataAsyncLogin :
     * 1) Camion + KM départ  2) CodeUser + Password  3) VERIF_LOGIN
     * 4) Cherche l'ID Samsara du camion.
     */
    public function doLogin()
    {
        $nocamion = (string) $this->request->getPost('nocamion');
        $kmdepart = (string) $this->request->getPost('kmdepart');
        $codeUser = (string) $this->request->getPost('code_user');
        $password = (string) $this->request->getPost('password');

        if (empty($nocamion) || empty($codeUser) || empty($password)) {
            $this->flashError('Veuillez remplir tous les champs.');
            return redirect()->to('/ramassage-ville');
        }

        // Pad camion number (même logique que OnCamCompleted)
        if (strlen($nocamion) < 3) {
            $nocamion = '0' . $nocamion;
        }

        // VERIF_LOGIN – même logique que Co_Rest_Sani::loginChauffeur()
        $dteactive   = date('Y-m-d');
        $loginResult = $this->web->loginChauffeur($codeUser, $password, $dteactive);

        if ($loginResult === null) {
            $this->flashError("Vous n'êtes pas autorisé.\nVeuillez communiquer avec votre supérieur.");
            return redirect()->to('/ramassage-ville');
        }

        $isAdmin   = (isset($loginResult['isadmin']) && $loginResult['isadmin'] == '1');
        $isBroker  = (isset($loginResult['isbroker']) && $loginResult['isbroker'] == '1');

        // POST_KM_DEPART – enregistrer le km si fourni
        if (! empty($kmdepart)) {
            $this->web->postKmDepart($nocamion, $kmdepart);
        }

        $idSamsara = $this->samsara->findVehicleIdByName($nocamion);

        $this->sessSet([
            'web_logged_in'     => true,
            'web_user_id'       => $loginResult['id'],
            'web_id_chauffeur'  => $loginResult['id'],
            'web_nom'           => $loginResult['Nom'],
            'web_is_admin'      => $isAdmin,
            'web_is_broker'     => $isBroker,
            'web_code_user'     => $codeUser,
            'web_nocamion'      => $nocamion,
            'web_kmdepart'      => $kmdepart,
            'web_id_samsara'    => $idSamsara ? $idSamsara : '',
            'web_active_cedule' => 0,
            'web_login_ts'      => time(),
        ]);

        return redirect()->to('/web/dashboard');
    }

    /** Déconnexion. */
    public function logout()
    {
        $nocamion = $this->sess('web_nocamion');
        if (! empty($nocamion)) {
            $this->web->removeWebHeartbeat($nocamion);
        }

        $this->ci3->remove([
            'web_logged_in', 'web_user_id', 'web_id_chauffeur',
            'web_nom', 'web_is_admin', 'web_code_user', 'web_active_cedule',
            'web_nocamion', 'web_kmdepart', 'web_id_samsara',
            'web_tracking_active', 'web_tracking_start_ts', 'web_login_ts',
            'web_from_maui',
        ]);
        $this->ci3->destroy();
        $this->noCache();
        return redirect()->to('/ramassage-ville');
    }

    //=================================================================
    // DASHBOARD
    //=================================================================

    /**
     * Tableau de bord principal – reproduit SaniView.xaml.
     * 3 colonnes : Camion | Chauffeur | Aide.
     */
    public function dashboard()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }

        $idChauffeur     = $this->sess('web_id_chauffeur');
        $isAdmin         = $this->sess('web_is_admin');
        $today           = date('Y-m-d');
        $nocamionSession = $this->sess('web_nocamion');
        $kmDepartSession = $this->sess('web_kmdepart');

        $cedules = $this->loadCedules($idChauffeur, $today);

        $activeCedule = (int) $this->sess('web_active_cedule');
        if ($activeCedule >= count($cedules)) {
            $activeCedule = 0;
        }

        $currentCedule = $cedules[$activeCedule] ?? null;
        $nocamion      = $currentCedule['NoCamion'] ?? '';
        if (empty($nocamion) && ! empty($nocamionSession)) {
            $nocamion = $nocamionSession;
        }

        // GET_CAMION – même logique que SaniView::GetCamion()
        $camion = null;
        if (! empty($nocamion) && $currentCedule) {
            $camion = $this->web->getOrCreateCamionJour(
                $nocamion,
                $currentCedule['IdChauffeur'],
                $today,
                $kmDepartSession ?? '0'
            );
        }

        // GET_CHAUFFEUR
        $chauffeurRH = null;
        if ($currentCedule) {
            $chauffeurRH = $this->web->getSanivanChauffeur(
                $currentCedule['IdChauffeur'],
                $currentCedule['DateJour'],
                $currentCedule['Id']
            );
        }

        // GET_EXTRA
        $extras = $this->web->getTypeExtras();

        // GET_NB_AIDES + GET_AIDES
        $aides = [];
        if ($currentCedule) {
            $helpers = $this->web->getNbAides($currentCedule['Id']);
            foreach (['HELPERS_1', 'HELPERS_2', 'HELPERS_3', 'HELPERS_4'] as $h) {
                if (! empty($helpers[$h])) {
                    $aide = $this->web->getSanivanAide(
                        $helpers[$h],
                        $currentCedule['DateJour'],
                        $currentCedule['Id']
                    );
                    if ($aide) {
                        $aides[] = $aide;
                    }
                }
            }
        }

        $idSamsara = $this->sess('web_id_samsara');
        if (empty($idSamsara)) {
            $idSamsara = '';
        }

        return $this->outPage(view('web_chauffeur/dashboard', [
            'cedules'       => $cedules,
            'activeCedule'  => $activeCedule,
            'currentCedule' => $currentCedule,
            'nocamion'      => $nocamion,
            'camion'        => $camion,
            'chauffeurRH'   => $chauffeurRH,
            'extras'        => $extras,
            'aides'         => $aides,
            'isAdmin'       => $isAdmin,
            'isBroker'      => $this->sess('web_is_broker'),
            'idSamsara'     => $idSamsara,
            'nom'           => $this->sess('web_nom'),
            'today'         => $today,
        ]));
    }

    /** Changer la cédule active. */
    public function changeCedule($index = 0)
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->sessSet(['web_active_cedule' => (int) $index]);
        return redirect()->to('/web/dashboard');
    }

    /**
     * Charge les cédules du jour pour un chauffeur (getInfoCedule
     * pour chaque id, même logique que dashboard/truckmap/villes).
     */
    private function loadCedules($idChauffeur, string $today): array
    {
        $cedules = [];
        foreach ($this->web->getCeduleIds($idChauffeur, $today) as $id) {
            $info = $this->web->getInfoCedule($id);
            if (! empty($info)) {
                $cedules[] = $info;
            }
        }
        return $cedules;
    }

    /**
     * Construit les boutons de parcours (polylines des cédules,
     * fallback legacy cedules.idpolyline) — partagé truckmap/villes.
     */
    private function buildPolylineButtons(array $cedules): array
    {
        $polylineButtons = [];
        $seenPolyIds     = [];
        foreach ($cedules as $i => $ced) {
            $polys = $this->web->getPolylinesForCedule($ced['Id']);

            if (empty($polys) && ! empty($ced['idpolyline'])) {
                $polyInfo = $this->web->getPolylineById($ced['idpolyline']);
                if ($polyInfo) {
                    $polys = [$polyInfo];
                }
            }

            foreach ($polys as $poly) {
                $polyId = $poly['id'];
                if (in_array($polyId, $seenPolyIds)) {
                    continue;
                }
                $seenPolyIds[] = $polyId;
                $polylineButtons[] = [
                    'id'          => $polyId,
                    'label'       => 'Parcours ' . count($seenPolyIds),
                    'ceduleIndex' => $i,
                    'color'       => '#FF0000',
                    'visible'     => false,
                ];
            }
        }
        return $polylineButtons;
    }

    //=================================================================
    // CARTE (TRUCKMAP)
    //=================================================================

    /** Page carte principale avec Leaflet + GPS Samsara. */
    public function truckmap()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }

        $idChauffeur = $this->sess('web_id_chauffeur');
        $isAdmin     = $this->sess('web_is_admin');
        $today       = date('Y-m-d');

        $cedules = $this->loadCedules($idChauffeur, $today);

        $activeCedule  = (int) $this->sess('web_active_cedule');
        $currentCedule = $cedules[$activeCedule] ?? null;
        $nocamion      = $currentCedule['NoCamion'] ?? '';

        $idVille   = $currentCedule['IdVilles']  ?? '';
        $idSecteur = $currentCedule['idsecteur'] ?? '';

        // ID Samsara – valeur en session, retry si vide
        $idSamsara = $this->sess('web_id_samsara');
        if (empty($idSamsara) && ! empty($nocamion)) {
            $idSamsara = $this->samsara->findVehicleIdByName($nocamion);
            if (! empty($idSamsara)) {
                $this->sessSet(['web_id_samsara' => $idSamsara]);
            }
        }
        if (empty($idSamsara)) {
            $idSamsara = '';
        }

        $nocamionSession = $this->sess('web_nocamion');
        if (! empty($nocamionSession) && empty($nocamion)) {
            $nocamion = $nocamionSession;
        }

        return $this->outPage(view('web_chauffeur/truckmap', [
            'nocamion'        => $nocamion,
            'idSamsara'       => $idSamsara,
            'idChauffeur'     => $idChauffeur,
            'isAdmin'         => $isAdmin,
            'idVille'         => $idVille,
            'idSecteur'       => $idSecteur,
            'polylineButtons' => $this->buildPolylineButtons($cedules),
            'cedules'         => $cedules,
            'activeCedule'    => $activeCedule,
            'nom'             => $this->sess('web_nom'),
        ]));
    }

    /**
     * Page carte simplifiée sans layout/menu pour l'application
     * .NET MAUI : ?idchauffeur=...&km=...&isadmin=0|1&nocamion=...&idcedule=...
     * Bootstrappe la session depuis ces paramètres (validation BD :
     * le chauffeur doit avoir une cédule active aujourd'hui).
     */
    public function truckmap_embed()
    {
        // ── 1. Paramètres MAUI ──
        $qIdChauffeur = (string) $this->request->getGet('idchauffeur');
        $qKm          = (string) $this->request->getGet('km');
        $qIsAdmin     = (string) $this->request->getGet('isadmin');
        $qNoCamion    = (string) $this->request->getGet('nocamion');
        $qIdCedule    = (string) $this->request->getGet('idcedule');

        // ── 2. Bootstrap session depuis MAUI (si pas déjà loggé) ──
        $boot = $this->bootstrapMauiSession($qIdChauffeur, $qNoCamion, $qIsAdmin, $qKm, $qIdCedule, 'truckmap_embed');
        if ($boot instanceof ResponseInterface) {
            return $boot;
        }

        if ($r = $this->checkAuth()) {
            return $r;
        }

        $idChauffeur = $this->sess('web_id_chauffeur');
        $isAdmin     = $this->sess('web_is_admin');
        $today       = date('Y-m-d');

        $cedules = $this->loadCedules($idChauffeur, $today);

        // ── 3. MAUI a fourni un idcedule précis → cédule active ──
        $activeCedule = (int) $this->sess('web_active_cedule');
        if (! empty($qIdCedule)) {
            foreach ($cedules as $i => $c) {
                if (isset($c['Id']) && (string) $c['Id'] === (string) $qIdCedule) {
                    $activeCedule = $i;
                    $this->sessSet(['web_active_cedule' => $i]);
                    break;
                }
            }
        }

        $currentCedule = $cedules[$activeCedule] ?? null;
        $nocamion      = $currentCedule['NoCamion'] ?? '';

        $idVille   = $currentCedule['IdVilles']  ?? '';
        $idSecteur = $currentCedule['idsecteur'] ?? '';

        $idSamsara = $this->sess('web_id_samsara');
        if (empty($idSamsara) && ! empty($nocamion)) {
            $idSamsara = $this->samsara->findVehicleIdByName($nocamion);
            if (! empty($idSamsara)) {
                $this->sessSet(['web_id_samsara' => $idSamsara]);
            }
        }
        if (empty($idSamsara)) {
            $idSamsara = '';
        }

        // Camion : priorité MAUI, sinon session, sinon cédule
        $nocamionSession = $this->sess('web_nocamion');
        if (! empty($qNoCamion)) {
            $nocamion = $qNoCamion;
        } elseif (! empty($nocamionSession) && empty($nocamion)) {
            $nocamion = $nocamionSession;
        }

        return $this->outPage(view('web_chauffeur/truckmap', [
            'nocamion'        => $nocamion,
            'idSamsara'       => $idSamsara,
            'idChauffeur'     => $idChauffeur,
            'isAdmin'         => $isAdmin,
            'idVille'         => $idVille,
            'idSecteur'       => $idSecteur,
            'polylineButtons' => $this->buildPolylineButtons($cedules),
            'cedules'         => $cedules,
            'activeCedule'    => $activeCedule,
            'nom'             => $this->sess('web_nom'),
            'embed'           => true, // vue sans layout
        ]));
    }

    /**
     * Bootstrap session MAUI partagé par truckmap_embed et villes.
     * Retourne null si OK (ou déjà connecté), une erreur 403 sinon.
     */
    private function bootstrapMauiSession($qIdChauffeur, $qNoCamion, $qIsAdmin, $qKm, $qIdCedule, string $tag): ?ResponseInterface
    {
        if ($this->sess('web_logged_in') || empty($qIdChauffeur) || empty($qNoCamion)) {
            return null;
        }

        $today = date('Y-m-d');
        $ceduleIdsCheck = $this->web->getCeduleIds($qIdChauffeur, $today);
        if (empty($ceduleIdsCheck)) {
            log_message('error', "[{$tag}] Accès refusé : chauffeur {$qIdChauffeur} sans cédule active le {$today}");
            return $this->response->setStatusCode(403)->setBody(
                'Accès refusé : aucune cédule active pour ce chauffeur aujourd\'hui.'
            );
        }

        if (! empty($qIdCedule) && ! in_array($qIdCedule, $ceduleIdsCheck)) {
            log_message('error', "[{$tag}] Accès refusé : cédule {$qIdCedule} n'appartient pas au chauffeur {$qIdChauffeur}");
            return $this->response->setStatusCode(403)->setBody(
                'Accès refusé : cédule invalide pour ce chauffeur.'
            );
        }

        if (strlen($qNoCamion) < 3) {
            $qNoCamion = '0' . $qNoCamion;
        }

        $ch           = $this->web->getChauffeurNom($qIdChauffeur);
        $nomChauffeur = ($ch && isset($ch['NomComplet'])) ? $ch['NomComplet'] : '';

        $idSamsara = $this->samsara->findVehicleIdByName($qNoCamion);

        if (! empty($qKm)) {
            $this->web->postKmDepart($qNoCamion, $qKm);
        }

        $this->sessSet([
            'web_logged_in'     => true,
            'web_user_id'       => $qIdChauffeur,
            'web_id_chauffeur'  => $qIdChauffeur,
            'web_nom'           => $nomChauffeur,
            'web_is_admin'      => ($qIsAdmin === '1' || $qIsAdmin === 1),
            'web_is_broker'     => false,
            'web_code_user'     => '',
            'web_nocamion'      => $qNoCamion,
            'web_kmdepart'      => $qKm,
            'web_id_samsara'    => $idSamsara ? $idSamsara : '',
            'web_active_cedule' => 0,
            'web_login_ts'      => time(),
            'web_from_maui'     => true, // marqueur d'origine
        ]);

        log_message('debug', "[{$tag}] Session bootstrappée depuis MAUI: chauffeur={$qIdChauffeur}, camion={$qNoCamion}, samsara={$idSamsara}");
        return null;
    }

    //=================================================================
    // VILLES
    //=================================================================

    /** Liste des villes avec dépotoires de la cédule active. */
    public function villes()
    {
        $qIdChauffeur = (string) $this->request->getGet('idchauffeur');
        $qKm          = (string) $this->request->getGet('km');
        $qIsAdmin     = (string) $this->request->getGet('isadmin');
        $qNoCamion    = (string) $this->request->getGet('nocamion');
        $qIdCedule    = (string) $this->request->getGet('idcedule');
        $qEmbed       = (string) $this->request->getGet('embed');
        $isEmbed      = ($qEmbed === '1' || $this->sess('web_from_maui'));

        $boot = $this->bootstrapMauiSession($qIdChauffeur, $qNoCamion, $qIsAdmin, $qKm, $qIdCedule, 'villes');
        if ($boot instanceof ResponseInterface) {
            return $boot;
        }

        // CI3 : si le bootstrap MAUI vient de s'effectuer → mode embed
        if (! empty($qIdChauffeur) && ! empty($qNoCamion) && $this->sess('web_logged_in') && $this->sess('web_from_maui')) {
            $isEmbed = true;
        }

        if ($r = $this->checkAuth()) {
            return $r;
        }

        // Si idcedule est fourni en GET, l'activer
        if (! empty($qIdCedule)) {
            $cedIdsAll = $this->web->getCeduleIds($this->sess('web_id_chauffeur'), date('Y-m-d'));
            foreach ($cedIdsAll as $idx => $cid) {
                if ((string) $cid === (string) $qIdCedule) {
                    $this->sessSet(['web_active_cedule' => $idx]);
                    break;
                }
            }
        }

        $idChauffeur = $this->sess('web_id_chauffeur');
        $today       = date('Y-m-d');

        $cedules = $this->loadCedules($idChauffeur, $today);

        $activeCedule  = (int) $this->sess('web_active_cedule');
        $currentCedule = $cedules[$activeCedule] ?? null;

        $villes = [];
        if ($currentCedule) {
            $villesRaw = $this->web->getVillesForCedule($currentCedule['Id']);

            // ── Auto-création du premier ramassage si aucun info_villes ──
            // Reproduit EnsureNewVilleBeforeStartAsync() + AddDepotToVilleAsync()
            if (empty($villesRaw) && ! empty($currentCedule['IdVilles'])) {
                $newVille = $this->web->addInfoVille(
                    $currentCedule['IdVilles'],
                    $currentCedule['Id']
                );
                if ($newVille) {
                    $villesRaw = $this->web->getVillesForCedule($currentCedule['Id']);
                }
            }

            foreach ($villesRaw as $v) {
                $v['depots'] = $this->web->getDepotsForVille($v['Id']);
                $villes[] = $v;
            }
        }

        return $this->outPage(view('web_chauffeur/villes', [
            'cedules'       => $cedules,
            'activeCedule'  => $activeCedule,
            'currentCedule' => $currentCedule,
            'villes'        => $villes,
            'depotoires'    => $this->web->getListDepotoires(),
            'listeVilles'   => $this->web->getListVilles(),
            'nom'           => $this->sess('web_nom'),
            'isAdmin'       => $this->sess('web_is_admin'),
            'embed'         => $isEmbed,
        ]));
    }

    //=================================================================
    // API AJAX (JSON)
    //=================================================================

    /** GET – Dernier KM fin pour un camion (login form). */
    public function api_getmaxkilo()
    {
        $nocamion = (string) $this->request->getGet('nocamion');
        if (empty($nocamion)) {
            return $this->outJson(['km' => '']);
        }
        return $this->outJson(['km' => $this->web->getMaxEndKilo($nocamion)]);
    }

    /**
     * GET – Position Samsara d'un véhicule.
     * Persiste dans gps_sani (reproduit LocationSenderService.cs
     * PushLocationAsync).
     */
    public function api_samsara_location()
    {
        $vehicleId   = (string) $this->request->getGet('vehicleId');
        $nocamion    = (string) $this->request->getGet('nocamion');
        $idchauffeur = (string) $this->request->getGet('idchauffeur');
        $isInZone    = (string) $this->request->getGet('IsInZone');

        // Reproduit GetDataAsyncLogin : résoudre vehicleId par nocamion
        if (empty($vehicleId) && ! empty($nocamion)) {
            $vehicleId = (string) $this->samsara->findVehicleIdByName($nocamion);
            if (! empty($vehicleId)) {
                $this->sessSet(['web_id_samsara' => $vehicleId]);
                log_message('debug', "[Samsara] vehicleId résolu par nocamion '{$nocamion}' => {$vehicleId}");
            }
        }

        if (empty($vehicleId)) {
            return $this->outJson([
                'ok'    => false,
                'error' => 'vehicleId manquant – nocamion "' . ($nocamion ?: '') . '" non trouvé dans Samsara',
            ]);
        }

        $loc = $this->samsara->getLatestLocation($vehicleId);
        if ($loc === null) {
            return $this->outJson(['ok' => false, 'error' => 'Position non disponible']);
        }

        // Persister dans gps_sani avec déduplication (temps + distance)
        if (! empty($nocamion) && isset($loc['lat']) && isset($loc['lng'])) {
            $this->persistGpsIfNeeded($nocamion, $loc['lat'], $loc['lng'], $idchauffeur, $isInZone, $loc);
        }

        return $this->outJson([
            'ok'        => true,
            'lat'       => $loc['lat'],
            'lng'       => $loc['lng'],
            'ts'        => $loc['ts'],
            'vehicleId' => $vehicleId,
            'heading'   => $loc['heading'] ?? null,
        ]);
    }

    /**
     * Persiste la position GPS dans gps_sani si assez de temps/distance
     * depuis la dernière (déduplication de LocationSenderService.cs).
     * TOUJOURS sauvegarder les points avec leur statut IsInZone correct.
     */
    private function persistGpsIfNeeded($nocamion, $lat, $lng, $idchauffeur, $isInZone, $samsaraInfo = null)
    {
        // Traçage contrôlé par les boutons Start/Stop
        $trackingActive = (bool) $this->sess('web_tracking_active');
        if ($trackingActive) {
            $isInZone = '1';
        }

        try {
            $last = $this->web->getLastGpsPosition($nocamion);

            if ($last !== null) {
                $lastTime = strtotime($last['track_time']);
                $elapsed  = time() - $lastTime;

                // Minimum 10 secondes entre deux enregistrements
                if ($elapsed < 10) {
                    return;
                }

                $dist = $this->distanceMeters(
                    (float) $last['track_lat'], (float) $last['track_lng'],
                    (float) $lat, (float) $lng
                );

                // Pas assez de mouvement (< 10m) sauf si > 60s écoulées
                if ($dist < 10.0 && $elapsed < 60) {
                    return;
                }
            }

            // ── Résolution de l'adresse ──
            // 1. Adresse Samsara (gratuite, même appel API).
            // 2. Sinon Nominatim seulement si le camion est lent (< 20 km/h).
            $adresse    = '';
            $samsaraAddr = is_array($samsaraInfo) && ! empty($samsaraInfo['address'])
                            ? (string) $samsaraInfo['address'] : '';
            $speedMph    = (is_array($samsaraInfo) && isset($samsaraInfo['speedMph']))
                            ? (float) $samsaraInfo['speedMph'] : null;
            $SLOW_MPH = 12.4274; // ≈ 20 km/h

            if ($samsaraAddr !== '') {
                $adresse = $samsaraAddr;
            } elseif ($speedMph === null || $speedMph < $SLOW_MPH) {
                $adresse = $this->reverseGeocodeThrottled($nocamion, $lat, $lng);
            }

            // Déduplication d'adresse (éviter N fois la même consécutivement)
            if ($adresse !== '' && $last !== null
                && isset($last['adresse']) && (string) $last['adresse'] === $adresse) {
                $adresse = '';
            }

            $this->web->saveGpsPosition([
                'nocamion'    => $nocamion,
                'idchauffeur' => ! empty($idchauffeur) ? $idchauffeur : '',
                'track_lat'   => $lat,
                'track_lng'   => $lng,
                'track_time'  => date('Y-m-d H:i:s'),
                'adresse'     => $adresse,
                'IsInZone'    => ! empty($isInZone) ? $isInZone : '0',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[GPS] persistGpsIfNeeded error: ' . $e->getMessage());
        }
    }

    /**
     * Adresse via Nominatim (OpenStreetMap), throttle 1 appel / 60s
     * par camion (cache en session).
     */
    private function reverseGeocodeThrottled($nocamion, $lat, $lng)
    {
        $cacheKey = 'geo_last_' . md5($nocamion);
        $last     = (int) ($this->ci3->get($cacheKey . '_ts') ?? 0);

        if ((time() - $last) < 60) {
            return (string) ($this->ci3->get($cacheKey . '_addr') ?? '');
        }

        $url = 'https://nominatim.openstreetmap.org/reverse?format=json'
             . '&lat=' . urlencode($lat)
             . '&lon=' . urlencode($lng)
             . '&zoom=18&addressdetails=0&email=truckmap@robert-daoust.ca';

        $address = '';
        try {
            $ctx = stream_context_create([
                'http' => [
                    'method'  => 'GET',
                    'header'  => "User-Agent: GestionVilles/1.0 (truckmap@robert-daoust.ca)\r\n",
                    'timeout' => 2,
                ],
            ]);
            $resp = @file_get_contents($url, false, $ctx);
            if ($resp !== false) {
                $json = json_decode($resp, true);
                if (is_array($json) && ! empty($json['display_name'])) {
                    $address = (string) $json['display_name'];
                }
            }
        } catch (\Throwable $e) {
            log_message('debug', '[ReverseGeocode] ' . $e->getMessage());
        }

        $this->sessSet([
            $cacheKey . '_ts'   => time(),
            $cacheKey . '_addr' => $address,
        ]);
        return $address;
    }

    /** Distance en mètres entre deux coordonnées GPS (Haversine). */
    private function distanceMeters($lat1, $lng1, $lat2, $lng2)
    {
        $R    = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $la1  = deg2rad($lat1);
        $la2  = deg2rad($lat2);
        $a    = sin($dLat / 2) * sin($dLat / 2)
              + cos($la1) * cos($la2) * sin($dLng / 2) * sin($dLng / 2);
        return 2 * $R * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * GET – Restaure le tracé IsInZone du jour (tous les points GPS
     * avec leur statut pour éviter les lignes droites indésirables).
     */
    public function api_inzone_history()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $nocamion = $this->sess('web_nocamion');
        if (empty($nocamion)) {
            return $this->outJson(['ok' => false, 'error' => 'nocamion manquant', 'points' => []]);
        }

        $rows   = $this->web->getAllGpsToday($nocamion);
        $source = 'all';

        $points = [];
        foreach ($rows as $r) {
            // Tolérer les coordonnées stockées avec virgule décimale
            $lat = (float) str_replace(',', '.', (string) $r['track_lat']);
            $lng = (float) str_replace(',', '.', (string) $r['track_lng']);
            if ($lat == 0.0 && $lng == 0.0) {
                continue;
            }
            $isInZone = isset($r['IsInZone']) && ($r['IsInZone'] === '1' || $r['IsInZone'] === 1 || strtolower((string) $r['IsInZone']) === 'true');
            $points[] = [
                'lat'      => $lat,
                'lng'      => $lng,
                'ts'       => $r['track_time'],
                'isInZone' => $isInZone ? 1 : 0,
            ];
        }

        log_message('debug', "[api_inzone_history] nocamion={$nocamion}, count=" . count($points) . ", source={$source}");

        return $this->outJson([
            'ok'              => true,
            'tracking_active' => (bool) $this->sess('web_tracking_active'),
            'source'          => $source,
            'count'           => count($points),
            'points'          => $points,
        ]);
    }

    /**
     * GET – Historique GPS Samsara d'un véhicule.
     * Params : vehicleId (ou nocamion), startTime, endTime.
     */
    public function api_samsara_history()
    {
        $vehicleId   = (string) $this->request->getGet('vehicleId');
        $nocamion    = (string) $this->request->getGet('nocamion');
        $idchauffeur = (string) $this->request->getGet('idchauffeur');
        $isInZone    = (string) $this->request->getGet('IsInZone');
        $startTime   = (string) $this->request->getGet('startTime');
        $endTime     = (string) $this->request->getGet('endTime');

        if (empty($vehicleId) && ! empty($nocamion)) {
            $vehicleId = (string) $this->samsara->findVehicleIdByName($nocamion);
            if (! empty($vehicleId)) {
                $this->sessSet(['web_id_samsara' => $vehicleId]);
            }
        }

        if (empty($vehicleId)) {
            return $this->outJson(['ok' => false, 'error' => 'vehicleId manquant']);
        }

        // Défaut : depuis l'activation du traçage (bouton Start)
        if (empty($startTime)) {
            $trackTs = (int) $this->sess('web_tracking_start_ts');
            if ($trackTs > 0) {
                $startTime = date('c', $trackTs);
            } else {
                $startTime = date('c', time() - 60);
            }
        }
        if (empty($endTime)) {
            $endTime = date('c');
        }

        $points = $this->samsara->getLocationHistory($vehicleId, $startTime, $endTime);

        // Persister les points d'historique dans gps_sani (combler les lacunes)
        $persisted = 0;
        if (! empty($nocamion) && ! empty($points)) {
            $persisted = $this->persistGpsHistoryBatch($nocamion, $points, $idchauffeur, $isInZone);
        }

        return $this->outJson([
            'ok'        => true,
            'count'     => count($points),
            'persisted' => $persisted,
            'points'    => $points,
        ]);
    }

    /**
     * Persiste un lot de points GPS historiques dans gps_sani.
     * « Buckets » de 10s (±1) pour éviter les doublons avec le polling
     * temps réel.
     */
    private function persistGpsHistoryBatch($nocamion, $points, $idchauffeur, $isInZone)
    {
        $inserted = 0;
        try {
            $existingTimes = $this->web->getGpsTimesToday($nocamion);

            $coveredBuckets = [];
            foreach ($existingTimes as $t) {
                $bucket = intval($t / 10);
                $coveredBuckets[$bucket - 1] = true;
                $coveredBuckets[$bucket]     = true;
                $coveredBuckets[$bucket + 1] = true;
            }

            $historyPoints = [];
            foreach ($points as $pt) {
                $lat = isset($pt['lat']) ? (float) $pt['lat'] : null;
                $lng = isset($pt['lng']) ? (float) $pt['lng'] : null;
                $ts  = isset($pt['ts'])  ? $pt['ts']          : null;
                if ($lat === null || $lng === null || empty($ts)) {
                    continue;
                }
                $epoch = strtotime($ts);
                if ($epoch === false || $epoch <= 0) {
                    continue;
                }
                $historyPoints[] = ['lat' => $lat, 'lng' => $lng, 'epoch' => $epoch];
            }
            usort($historyPoints, function ($a, $b) {
                return $a['epoch'] - $b['epoch'];
            });

            if (empty($historyPoints)) {
                return 0;
            }

            $prevLat  = null;
            $prevLng  = null;
            $prevTime = 0;

            foreach ($historyPoints as $hp) {
                $bucket = intval($hp['epoch'] / 10);
                if (isset($coveredBuckets[$bucket])) {
                    $prevLat  = $hp['lat'];
                    $prevLng  = $hp['lng'];
                    $prevTime = $hp['epoch'];
                    continue;
                }

                if ($prevTime > 0) {
                    $elapsed = $hp['epoch'] - $prevTime;
                    if ($elapsed < 10) {
                        continue;
                    }
                    $dist = $this->distanceMeters($prevLat, $prevLng, $hp['lat'], $hp['lng']);
                    if ($dist < 10.0 && $elapsed < 60) {
                        continue;
                    }
                }

                $this->web->saveGpsPosition([
                    'nocamion'    => $nocamion,
                    'idchauffeur' => ! empty($idchauffeur) ? $idchauffeur : '',
                    'track_lat'   => $hp['lat'],
                    'track_lng'   => $hp['lng'],
                    'track_time'  => date('Y-m-d H:i:s', $hp['epoch']),
                    'adresse'     => '',
                    'IsInZone'    => ! empty($isInZone) ? $isInZone : '0',
                ]);

                $coveredBuckets[$bucket - 1] = true;
                $coveredBuckets[$bucket]     = true;
                $coveredBuckets[$bucket + 1] = true;

                $prevLat  = $hp['lat'];
                $prevLng  = $hp['lng'];
                $prevTime = $hp['epoch'];
                $inserted++;
            }

            if ($inserted > 0) {
                log_message('info', "[GPS] Historique batch persisté: {$inserted} points pour nocamion={$nocamion}");
            }
        } catch (\Throwable $e) {
            log_message('error', '[GPS] persistGpsHistoryBatch error: ' . $e->getMessage());
        }
        return $inserted;
    }

    /** GET – Liste des véhicules Samsara. */
    public function api_samsara_vehicles()
    {
        return $this->outJson($this->samsara->listVehicles());
    }

    /** GET – Polyline (parcours) par id : [[lat,lng], ...]. */
    public function api_polyline()
    {
        $id = (int) $this->request->getGet('id');

        $row = $this->db->query(
            'SELECT parcours FROM polilyne_ville WHERE id = ' . (int) $id
        )->getRowArray();

        if (empty($row) || empty($row['parcours'])) {
            return $this->outJson(['ok' => false, 'error' => 'Polyline non trouvée']);
        }

        $coords = $this->parseParcoursString($row['parcours']);

        if (empty($coords)) {
            return $this->outJson(['ok' => false, 'error' => 'Parcours vide ou invalide']);
        }

        return $this->outJson([
            'ok'       => true,
            'parcours' => $coords,
            'couleur'  => '#ff0000',
        ]);
    }

    /**
     * Parse une chaîne parcours ("lat,lng_lat,lng_...") en tableau
     * [[lat,lng], ...]. Séparateurs supportés : _ ; | espace.
     */
    private function parseParcoursString($parcours)
    {
        if (empty($parcours)) {
            return [];
        }

        $parcours = trim(str_replace('"', '', $parcours));
        $coords   = [];

        $pairs = preg_split('/[_;\| ]+/', $parcours, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($pairs as $pair) {
            $parts = explode(',', $pair);
            if (count($parts) >= 2) {
                $lat = floatval(str_replace(',', '.', trim($parts[0])));
                $lng = floatval(str_replace(',', '.', trim($parts[1])));
                if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    $coords[] = ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Fallback: extraire tous les nombres et les appairer
        if (empty($coords)) {
            preg_match_all('/-?\d+(?:[.,]\d+)?/', $parcours, $matches);
            $nums = $matches[0] ?? [];
            for ($i = 0; $i + 1 < count($nums); $i += 2) {
                $lat = floatval(str_replace(',', '.', $nums[$i]));
                $lng = floatval(str_replace(',', '.', $nums[$i + 1]));
                if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    $coords[] = ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        return $coords;
    }

    /** GET – Tags ouverts. */
    public function api_tags()
    {
        return $this->outJson(['ok' => true, 'data' => $this->web->getOpenTags()]);
    }

    /** GET – Camions sur la route. */
    public function api_camions_road()
    {
        $date = (string) $this->request->getGet('date');
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        return $this->outJson(['ok' => true, 'data' => $this->web->getCamionsOnRoad($date)]);
    }

    // ─── API SaniView (CAMION/RH) ───

    /** POST – Sauvegarde infos camion (POST_CAMION action 7). */
    public function api_post_camion()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $err = $this->web->postCamionInfo(
            $this->request->getPost('id'),
            $this->request->getPost('KiloDeb'),
            $this->request->getPost('KiloFin'),
            $this->request->getPost('Fuel'),
            $this->request->getPost('HreDeb'),
            $this->request->getPost('HreFin')
        );
        if (! empty($err['code'])) {
            return $this->outJson(['ok' => false, 'error' => $err['message']]);
        }
        return $this->outJson(['ok' => true]);
    }

    /** POST – Sauvegarde infos chauffeur (POST_CHAUFFEURS action 6). */
    public function api_post_chauffeur()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $err = $this->web->postChauffeurInfo(
            $this->request->getPost('id'),
            $this->request->getPost('HreDeb'),
            $this->request->getPost('HreFin'),
            $this->request->getPost('HreDeb_extra'),
            $this->request->getPost('HreFin_extra'),
            $this->request->getPost('TypeExtra')
        );
        if (! empty($err['code'])) {
            return $this->outJson(['ok' => false, 'error' => $err['message']]);
        }
        return $this->outJson(['ok' => true]);
    }

    /** POST – Sauvegarde infos aide (POST_AIDES action 24). */
    public function api_post_aide()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $err = $this->web->postAideInfo(
            $this->request->getPost('id'),
            $this->request->getPost('HreDeb'),
            $this->request->getPost('HreFin')
        );
        if (! empty($err['code'])) {
            return $this->outJson(['ok' => false, 'error' => $err['message']]);
        }
        return $this->outJson(['ok' => true]);
    }

    /** GET – Liste camions disponibles (LIST_CAMIONS action 1). */
    public function api_list_camions()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        return $this->outJson(['ok' => true, 'data' => $this->web->getListCamionsDisponibles()]);
    }

    /** GET – Liste chauffeurs disponibles (LIST_CHAUFFEUR action 2). */
    public function api_list_chauffeurs()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        return $this->outJson(['ok' => true, 'data' => $this->web->getListChauffeursDisponibles()]);
    }

    /** POST – Changer le camion d'une cédule (ADD_ITEM_CEDULE action 38). */
    public function api_change_camion()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->web->changeCamionCedule(
            $this->request->getPost('idcedule'),
            $this->request->getPost('camion'),
            $this->request->getPost('oldcamion')
        );
        return $this->outJson(['ok' => true]);
    }

    /** POST – Changer le chauffeur d'une cédule (action 40). */
    public function api_change_chauffeur()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $newId = $this->web->changeChauffeurCedule(
            $this->request->getPost('idcedule'),
            $this->request->getPost('chauffeur'),
            $this->request->getPost('oldchauffeur')
        );
        return $this->outJson(['ok' => ($newId !== null), 'idchauffeur' => $newId]);
    }

    /** GET – Types extra disponibles (GET_EXTRA action 33). */
    public function api_type_extras()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        return $this->outJson(['ok' => true, 'data' => $this->web->getTypeExtras()]);
    }

    // ─── API VillesView (VILLES / DÉPOTOIRES) ───

    /** POST – Sauvegarde un champ de ville (POST_VILLE action 19). */
    public function api_post_ville()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->web->postVilleField(
            $this->request->getPost('id'),
            $this->request->getPost('champ'),
            $this->request->getPost('value')
        );
        return $this->outJson(['ok' => true]);
    }

    /** POST – HreDeb+HreFin+nbBac d'une ville (action 29). */
    public function api_post_all_ville()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->web->postAllVilleDepotEntry(
            $this->request->getPost('id'),
            $this->request->getPost('HreDeb'),
            $this->request->getPost('HreFin'),
            $this->request->getPost('nbBac')
        );
        return $this->outJson(['ok' => true]);
    }

    /** POST – Sauvegarde un champ de dépotoire (POST_DEPOT_ENTRY action 25). */
    public function api_post_depot()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->web->postDepotField(
            $this->request->getPost('id'),
            $this->request->getPost('champ'),
            $this->request->getPost('value')
        );
        return $this->outJson(['ok' => true]);
    }

    /** POST – Sauvegarde le nom du dépotoire (POST_DEPOTOIRE action 20). */
    public function api_post_depotoire()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->web->postDepotoire(
            $this->request->getPost('id'),
            $this->request->getPost('depotoire')
        );
        return $this->outJson(['ok' => true]);
    }

    /** POST – Tous les champs du dépotoire (POST_ALL_DEPOT_ENTRY action 26). */
    public function api_post_all_depot()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->web->postAllDepotEntry(
            $this->request->getPost('id'),
            $this->request->getPost('Depotoire'),
            $this->request->getPost('HreDeb'),
            $this->request->getPost('HreFin'),
            $this->request->getPost('Tonnage'),
            $this->request->getPost('Boite'),
            $this->request->getPost('IsTransBo'),
            $this->request->getPost('IsAvecMat')
        );
        return $this->outJson(['ok' => true]);
    }

    /** POST – Ajouter un ramassage (ADD_INFO_VILLE action 31). */
    public function api_add_ville()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $result = $this->web->addInfoVille(
            $this->request->getPost('idville'),
            $this->request->getPost('idcedule')
        );
        if ($result) {
            return $this->outJson(['ok' => true, 'ville' => $result]);
        }
        return $this->outJson(['ok' => false, 'error' => 'Impossible d\'ajouter la ville.']);
    }

    /** GET – Recharge les villes de la cédule active (refresh AJAX). */
    public function api_reload_villes()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $idcedule = (string) $this->request->getGet('idcedule');
        if (empty($idcedule)) {
            return $this->outJson(['ok' => false, 'error' => 'idcedule manquant']);
        }

        $villes = [];
        foreach ($this->web->getVillesForCedule($idcedule) as $v) {
            $v['depots'] = $this->web->getDepotsForVille($v['Id']);
            $villes[] = $v;
        }

        return $this->outJson([
            'ok'         => true,
            'villes'     => $villes,
            'depotoires' => $this->web->getListDepotoires(),
        ]);
    }

    /** POST – Change cédule active en AJAX (sans redirect). */
    public function api_change_cedule()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $this->sessSet(['web_active_cedule' => (int) $this->request->getPost('index')]);
        return $this->outJson(['ok' => true]);
    }

    /**
     * POST – Toggle zone (Start/Stop).
     * Reproduit LocationMapPage.HandleZoneToggle : active/désactive le
     * traçage GPS et sauvegarde HreDeb/HreFin sur la ville courante.
     */
    public function api_zone_toggle()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }

        $inZone = (string) $this->request->getPost('inZone');
        $isIn   = ($inZone === '1' || strtolower($inZone) === 'true');

        if ($isIn) {
            $this->sessSet([
                'web_tracking_active'   => 1,
                'web_tracking_start_ts' => time(),
            ]);
        } else {
            $this->ci3->remove(['web_tracking_active', 'web_tracking_start_ts']);
        }

        $idChauffeur  = $this->sess('web_id_chauffeur');
        $activeCedule = (int) $this->sess('web_active_cedule');
        $today        = date('Y-m-d');

        $cedules      = $this->loadCedules($idChauffeur, $today);
        $currentCedule = $cedules[$activeCedule] ?? null;
        if (! $currentCedule) {
            return $this->outJson(['ok' => false, 'error' => 'Aucune cédule active']);
        }

        $villesRaw = $this->web->getVillesForCedule($currentCedule['Id']);
        if (empty($villesRaw)) {
            return $this->outJson(['ok' => false, 'error' => 'Aucune ville']);
        }

        $ville = end($villesRaw);
        $time  = date('H:i');

        if ($isIn) {
            if (empty($ville['HreDeb'])) {
                $this->web->postVilleField($ville['Id'], 'HreDeb', $time);
            }
        } else {
            if (! empty($ville['HreDeb']) && empty($ville['HreFin'])) {
                $this->web->postVilleField($ville['Id'], 'HreFin', $time);
            }
        }

        return $this->outJson(['ok' => true, 'inZone' => $isIn, 'villeId' => $ville['Id'], 'time' => $time]);
    }

    /** GET – Badge tag count pour le chauffeur connecté. */
    public function api_tag_badge()
    {
        if (! $this->sess('web_logged_in')) {
            return $this->outJson(['ok' => true, 'count' => 0, 'tag_ids' => []]);
        }

        $idChauffeur = (int) $this->sess('web_id_chauffeur');
        $today       = date('Y-m-d');

        $cedules      = $this->loadCedules($idChauffeur, $today);
        $activeCedule = (int) $this->sess('web_active_cedule');
        $idVille      = isset($cedules[$activeCedule]['IdVilles']) ? (int) $cedules[$activeCedule]['IdVilles'] : 0;

        if (! $idVille) {
            return $this->outJson(['ok' => true, 'count' => 0, 'tag_ids' => []]);
        }

        $rows = $this->db->query(
            "SELECT id FROM map_tags WHERE id_ville = ? AND status <> 'DONE' ORDER BY created_at DESC",
            [$idVille]
        )->getResultArray();

        $tagIds = [];
        foreach ($rows as $r) {
            $tagIds[] = $r['id'];
        }

        return $this->outJson([
            'ok'      => true,
            'count'   => count($tagIds),
            'tag_ids' => $tagIds,
        ]);
    }

    /**
     * GET – Récupère les polylines dynamiques (GPS historique) pour
     * le camion. Même logique que
     * Co_tablette_sani::getPolylineDynamiqueForAllWithoutZoneFilter.
     */
    public function api_get_polyline_dynamique()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }

        $dateactive  = (string) ($this->request->getGet('dateactive') ?: date('Y-m-d'));
        $nocamion    = (string) $this->sess('web_nocamion');
        $idchauffeur = $this->sess('web_id_chauffeur');

        if (empty($nocamion)) {
            return $this->outJson(['ok' => false, 'error' => 'No camion found in session']);
        }

        $dateStart = $this->db->escape($dateactive . ' 00:00:00');
        $dateEnd   = $this->db->escape($dateactive . ' 23:59:59');
        $camEsc    = $this->db->escape($nocamion);

        $chFilter = ((int) $idchauffeur > 0)
            ? " AND g.idchauffeur = " . $this->db->escape($idchauffeur)
            : "";

        $baseSql = "SELECT g.id, g.idchauffeur, g.track_lng, g.track_lat, g.track_time, g.adresse, g.IsInZone, g.Statutpoly,
                CONCAT(UPPER(TRIM(r.NOM)), ' ', UPPER(TRIM(r.PRENOM))) AS chauffeur
           FROM gps_sani g
           LEFT JOIN reshumaine r ON r.id = g.idchauffeur
          WHERE g.track_time BETWEEN $dateStart AND $dateEnd
            AND g.nocamion = $camEsc";

        $q = $this->db->query($baseSql . $chFilter . " ORDER BY g.track_time");
        if ($q->getNumRows() === 0 && $chFilter !== "") {
            $q = $this->db->query($baseSql . " ORDER BY g.track_time");
        }

        if ($q->getNumRows() < 2) {
            return $this->outJson(['ok' => true, 'segments' => []]);
        }

        $buf = '';
        foreach ($q->getResult() as $row) {
            $buf .= str_replace(',', '.', $row->track_lat) . "," .
                str_replace(',', '.', $row->track_lng) .
                ", timespan: " . $row->track_time .
                ", chauffeur: " . $row->chauffeur .
                ", adresse: " . $row->adresse .
                ", isinzone: " . (int) $row->IsInZone .
                ", statutpoly: " . (int) $row->Statutpoly . "_";
        }

        $segmentsOut = [];
        if ($buf !== '') {
            $segmentsOut[] = substr($buf, 0, -1);
        }

        return $this->outJson(['ok' => true, 'segments' => $segmentsOut]);
    }

    //=================================================================
    // UPLOAD TICKET (photo → PDF)
    //=================================================================

    /**
     * POST – Upload photo/ticket dépotoire → génère un PDF avec
     * en-tête et pied de page.
     */
    public function api_upload_ticket()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $idDepotoire = (string) $this->request->getPost('referenceId');
        $idVille     = (string) $this->request->getPost('villeId');

        if (empty($_FILES['file']['name'])) {
            return $this->outJson(['ok' => false, 'error' => 'Aucun fichier.']);
        }

        // Écriture dans le docroot CI3 (URLs /uploads/... servies par CI3)
        $docroot    = rtrim((string) env('ci3.webroot'), '/\\');
        $uploadDir  = $docroot . '/uploads/tickets/';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Sauvegarder la photo originale
        $ext     = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $imgName = $idVille . '_' . $idDepotoire . '_' . time() . '.' . $ext;
        $imgDest = $uploadDir . $imgName;

        if (! move_uploaded_file($_FILES['file']['tmp_name'], $imgDest)) {
            return $this->outJson(['ok' => false, 'error' => 'Échec upload.']);
        }

        // Infos pour le PDF
        $depotInfo    = $this->web->getDepotFullInfo($idDepotoire);
        $chauffeurNom = '';
        $idChauffeur  = $this->sess('web_id_chauffeur');
        if (! empty($idChauffeur)) {
            $ch = $this->web->getChauffeurNom($idChauffeur);
            if ($ch) {
                $chauffeurNom = $ch['NomComplet'];
            }
        }

        // Convertir HEIC/WEBP en JPEG (FPDF ne supporte que JPG, PNG, GIF)
        $supportedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (! in_array($ext, $supportedExts) && function_exists('imagecreatefromstring')) {
            $imgData = @file_get_contents($imgDest);
            $gdImg   = ($imgData !== false) ? @imagecreatefromstring($imgData) : false;
            if ($gdImg !== false) {
                $jpgName = $idVille . '_' . $idDepotoire . '_' . time() . '.jpg';
                $jpgDest = $uploadDir . $jpgName;
                if (@imagejpeg($gdImg, $jpgDest, 85)) {
                    imagedestroy($gdImg);
                    $imgName = $jpgName;
                    $imgDest = $jpgDest;
                    $ext     = 'jpg';
                } else {
                    imagedestroy($gdImg);
                }
            }
        }

        // Générer le PDF avec la même nomenclature que upload.php
        $dateCollecte = (string) $this->request->getPost('date_collecte');
        if (empty($dateCollecte) && ! empty($depotInfo['DateJour'])) {
            $dateCollecte = $depotInfo['DateJour'];
        }
        $dateTs = ! empty($dateCollecte) ? strtotime($dateCollecte) : time();
        if ($dateTs === false) {
            $dateTs = time();
        }
        $annee   = date('Y', $dateTs);
        $moisNum = date('m', $dateTs);
        $dateStr = date('Y-m-d', $dateTs);

        $nomVille = (string) $this->request->getPost('nomville');
        if (empty($nomVille) && ! empty($depotInfo['NomVille'])) {
            $nomVille = $depotInfo['NomVille'];
        }
        $villeSafe = preg_replace('~[^a-zA-Z0-9\x{00C0}-\x{00D6}\x{00D8}-\x{00F6}\x{00F8}-\x{00FF} _\-]~u', '', $nomVille);
        $villeSafe = str_replace([' ', '-'], '_', trim((string) $villeSafe));
        if ($villeSafe === '') {
            $villeSafe = 'ticket';
        }

        $matiere = (string) $this->request->getPost('matiere');
        if (empty($matiere) && ! empty($depotInfo['TypeDeProduit'])) {
            $matiere = $depotInfo['TypeDeProduit'];
        }
        $matiereCode = ! empty($matiere) ? mb_strtoupper(mb_substr($matiere, 0, 1, 'UTF-8'), 'UTF-8') : '';

        $tonnage = (string) $this->request->getPost('tonnage');
        if (($tonnage === '' || $tonnage === null) && isset($depotInfo['Tonnage'])) {
            $tonnage = $depotInfo['Tonnage'];
        }
        $tonnagePart = ($tonnage !== '' && $tonnage !== null) ? $tonnage : '0';
        $tonnagePart = str_replace(['.', ','], '_', (string) $tonnagePart);
        $pdfName = $villeSafe
            . ($matiereCode !== '' ? '_' . $matiereCode : '')
            . '_' . $tonnagePart
            . '_' . $dateStr . '.pdf';
        $pdfName = preg_replace('~[\\/:*?"<>|]~', '_', $pdfName);

        $pdfRelDir = 'uploads/pdf_tickets/' . $annee . '/' . $moisNum . '/';
        $pdfDir    = $docroot . '/' . $pdfRelDir;
        if (! is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }
        $pdfDest = $pdfDir . $pdfName;

        // Capturer toute sortie parasite pour ne pas corrompre le JSON
        ob_start();
        try {
            $this->generateTicketPdf($pdfDest, $imgDest, $depotInfo, $chauffeurNom);
            ob_end_clean();
            $ticketUrl = $pdfRelDir . $pdfName;
        } catch (\Throwable $e) {
            // Fallback : utiliser la photo si la génération PDF échoue
            ob_end_clean();
            log_message('error', '[api_upload_ticket] PDF error: ' . $e->getMessage());
            $ticketUrl = 'uploads/tickets/' . $imgName;
        }

        $this->web->saveDepotTicket($idDepotoire, $ticketUrl);
        return $this->outJson(['ok' => true, 'ticket' => $ticketUrl]);
    }

    /**
     * Génère un PDF avec en-tête, photo du billet et pied de page.
     * (FPDF copié de CI3 : app/Libraries/Pdf/fpdf.php + font/)
     */
    private function generateTicketPdf($pdfPath, $imagePath, $depotInfo, $chauffeurNom)
    {
        // Augmenter la mémoire pour les grandes photos de tablette
        $prevMemory = ini_get('memory_limit');
        ini_set('memory_limit', '256M');

        if (! is_array($depotInfo)) {
            $depotInfo = [];
        }

        require_once APPPATH . 'Libraries/Pdf/fpdf.php';

        $pdf = new \FPDF('P', 'mm', 'Letter');
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetXY(10, 12);
        $pdf->Cell(65, 10, utf8_decode('Billet de dépotoir'), 0, 0, 'L');
        $logoPath = rtrim((string) env('ci3.webroot'), '/\\') . '/assets/img/logo.jpg';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 80, 8, 55);
        }
        $pdf->SetXY(120, 12);
        $pdf->Cell(86, 10, date('Y-m-d H:i'), 0, 1, 'R');
        $pdf->SetY(32);

        // Ligne séparatrice
        $pdf->SetDrawColor(0, 0, 139);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 206, $pdf->GetY());
        $pdf->Ln(6);

        if (! empty($depotInfo['NomVille'])) {
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->SetTextColor(0, 0, 139);
            $pdf->Cell(0, 10, utf8_decode($depotInfo['NomVille']), 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(2);
        }

        $leftRows  = [];
        $rightRows = [];

        if (! empty($depotInfo['NoCamion'])) {
            $leftRows[] = ['Camion :', $depotInfo['NoCamion'], false, false];
        }
        if (! empty($depotInfo['NomDepotoire'])) {
            $leftRows[] = ['Dépotoir :', $depotInfo['NomDepotoire'], true, true];
        }
        if (! empty($depotInfo['TypeDeProduit'])) {
            $rightRows[] = ['Matière :', $depotInfo['TypeDeProduit'], true, true];
        }
        if (isset($depotInfo['Tonnage']) && $depotInfo['Tonnage'] !== '' && $depotInfo['Tonnage'] !== null) {
            $rightRows[] = ['Tonnage :', $depotInfo['Tonnage'] . ' t', false, false];
        }
        if (! empty($depotInfo['HreDeb']) || ! empty($depotInfo['HreFin'])) {
            $hreDeb = $depotInfo['HreDeb'] ?? '';
            $hreFin = $depotInfo['HreFin'] ?? '';
            $heures = trim($hreDeb . ' - ' . $hreFin, ' -');
            $rightRows[] = ['Horaire :', $heures, false, false];
        }

        $this->addPdfTwoColumns($pdf, $leftRows, $rightRows);

        $pdf->Ln(4);

        // ── Photo du billet ──
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, utf8_decode('Photo du billet :'), 0, 1);
        $pdf->Ln(2);

        $pageW   = 196; // largeur utile (216 - 2*10 marges)
        $maxImgH = 140;
        $imgInfo = @getimagesize($imagePath);

        if ($imgInfo !== false) {
            $imgW = $imgInfo[0];
            $imgH = $imgInfo[1];

            $ratio = min($pageW / $imgW, $maxImgH / $imgH);
            $dispW = $imgW * $ratio;
            $dispH = $imgH * $ratio;

            $x = 10 + ($pageW - $dispW) / 2;
            $pdf->Image($imagePath, $x, $pdf->GetY(), $dispW, $dispH);
            $pdf->SetY($pdf->GetY() + $dispH + 4);
        }

        // ── Pied de page ──
        $pdf->Ln(6);
        $pdf->SetDrawColor(0, 0, 139);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), 206, $pdf->GetY());
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(0, 5, utf8_decode('Robert Daoust & Fil Inc. — Document généré le ') . date('Y-m-d à H:i'), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Ce document est une preuve de dépôt au dépotoir.'), 0, 1, 'C');

        $pdf->Output('F', $pdfPath);

        ini_set('memory_limit', $prevMemory);
    }

    private function addPdfTwoColumns($pdf, $leftRows, $rightRows)
    {
        $startY  = $pdf->GetY();
        $leftX  = 10;
        $rightX = 110;
        $labelW = 28;
        $valueW = 68;
        $rowH   = 7;

        $pdf->SetY($startY);
        foreach ($leftRows as $row) {
            $this->addPdfColumnRow($pdf, $leftX, $labelW, $valueW, $rowH, $row[0], $row[1], $row[2], $row[3]);
        }

        $pdf->SetY($startY);
        foreach ($rightRows as $row) {
            $this->addPdfColumnRow($pdf, $rightX, $labelW, $valueW, $rowH, $row[0], $row[1], $row[2], $row[3]);
        }

        $pdf->SetY($startY + max(count($leftRows), count($rightRows)) * $rowH);
    }

    private function addPdfColumnRow($pdf, $x, $labelW, $valueW, $rowH, $label, $value, $decodeValue = false, $decodeLabel = false)
    {
        $pdf->SetX($x);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($labelW, $rowH, $decodeLabel ? utf8_decode($label) : $label, 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($valueW, $rowH, $decodeValue ? utf8_decode($value) : $value, 0, 1);
    }

    //=================================================================
    // NAVIGATION ADMIN
    //=================================================================

    /**
     * Tous les camions – routecam du jour (porté par TabletteSani →
     * sert l'hôte courant, pas l'URL prod hardcodée de CI3).
     */
    public function nav_all_trucks()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $dte = date('Y-m-d');
        return redirect()->to('/sanivan/routecam/' . $dte . '?action=' . $dte . '&idchauffeur=0&camchauf=');
    }

    /**
     * Info Villes – stub CI4 → 307 vers CI3 (Co_ville non porté).
     */
    public function nav_info_villes()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        $dte = date('Y-m-d');
        return redirect()->to('/ville/nav_zone_hub/fr?dte=' . $dte);
    }

    /**
     * Bureau – diverge de CI3 : redirige vers le formulaire de
     * connexion admin (CI3 hardcodait des identifiants en clair
     * dans l'URL — non reproduits dans un repo versionné).
     */
    public function nav_bureau()
    {
        if ($r = $this->checkAuth()) {
            return $r;
        }
        return redirect()->to('/admin/connexion');
    }

    //=================================================================
    // DOC UTILISATEUR
    //=================================================================

    public function showAidePourWEB()
    {
        return $this->outPage(view('web_chauffeur/doc_utilisateur_chauffeur_web'));
    }

    //=================================================================
    // CRON GPS – Polling Samsara côté serveur
    //=================================================================

    /**
     * Cron GPS Poll – interroge Samsara pour tous les camions web
     * actifs et persiste dans gps_sani.
     *
     * Accès HTTP : /web/cron_gps_poll?key=SaniVanGpsCron2025
     */
    public function cron_gps_poll()
    {
        $cronKey = 'SaniVanGpsCron2025';

        if (! is_cli()) {
            $key = (string) $this->request->getGet('key');
            if ($key !== $cronKey) {
                return $this->response->setStatusCode(403)->setBody('Access denied');
            }
        }

        // Nettoyage des heartbeats expirés (> 30 min)
        $this->web->cleanExpiredHeartbeats();

        // 1. Camions web actifs (heartbeat < 5 min)
        $trucks = $this->web->getActiveWebTrucks(5);
        if (empty($trucks)) {
            if (is_cli()) {
                echo date('H:i:s') . " – Aucun chauffeur web actif.\n";
                return;
            }
            return $this->outJson(['ok' => true, 'msg' => 'Aucun chauffeur web actif', 'polled' => 0]);
        }

        // 2. Séparer les camions avec/sans vehicleId connu
        $toQuery    = [];
        $unresolved = [];

        foreach ($trucks as $t) {
            $vid = isset($t['vehicle_id']) ? trim((string) $t['vehicle_id']) : '';
            if (! empty($vid)) {
                $toQuery[$vid] = [
                    'nocamion'    => $t['nocamion'],
                    'idchauffeur' => $t['idchauffeur'],
                ];
            } else {
                $unresolved[] = $t;
            }
        }

        // 3. Résoudre les vehicleId manquants via Samsara (un seul appel)
        if (! empty($unresolved)) {
            $vehicles = $this->samsara->listVehicles();
            if (isset($vehicles['data']) && is_array($vehicles['data'])) {
                $vehMap = [];
                foreach ($vehicles['data'] as $v) {
                    $name = isset($v['name']) ? strtolower(trim($v['name'])) : '';
                    $id   = isset($v['id'])   ? $v['id'] : '';
                    if ($name !== '' && $id !== '') {
                        $vehMap[$name] = $id;
                    }
                }

                foreach ($unresolved as $t) {
                    $needle  = strtolower(trim((string) $t['nocamion']));
                    $foundId = null;

                    if (isset($vehMap[$needle])) {
                        $foundId = $vehMap[$needle];
                    } else {
                        foreach ($vehMap as $name => $vid) {
                            if (strpos($name, $needle) !== false || strpos($needle, $name) !== false) {
                                $foundId = $vid;
                                break;
                            }
                        }
                    }

                    if ($foundId && ! isset($toQuery[$foundId])) {
                        $toQuery[$foundId] = [
                            'nocamion'    => $t['nocamion'],
                            'idchauffeur' => $t['idchauffeur'],
                        ];
                        $this->web->registerWebHeartbeat($t['nocamion'], $t['idchauffeur'], $foundId);
                    }
                }
            }
        }

        if (empty($toQuery)) {
            if (is_cli()) {
                echo date('H:i:s') . " – Aucun camion trouvé dans Samsara.\n";
                return;
            }
            return $this->outJson(['ok' => true, 'msg' => 'Aucun match Samsara', 'polled' => 0]);
        }

        // 4. Toutes les positions en un seul appel Samsara (batch)
        $locations = $this->samsara->getMultiVehicleLocations(array_keys($toQuery));

        // 5. Persister chaque position
        $count = 0;
        foreach ($toQuery as $vehicleId => $info) {
            if (isset($locations[$vehicleId])) {
                $loc = $locations[$vehicleId];
                $this->persistGpsIfNeeded(
                    $info['nocamion'],
                    $loc['lat'],
                    $loc['lng'],
                    $info['idchauffeur'],
                    '0',
                    $loc
                );
                $count++;
            }
        }

        if (is_cli()) {
            echo date('H:i:s') . " – {$count}/" . count($toQuery) . " camions web mis à jour.\n";
            return;
        }
        return $this->outJson(['ok' => true, 'polled' => $count, 'total' => count($toQuery)]);
    }
}
