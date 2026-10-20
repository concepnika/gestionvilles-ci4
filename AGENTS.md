# AGENTS.md — État du projet GestionVilles CI4

> Point de départ pour une nouvelle session de travail.
> Voir aussi `MIGRATION.md` (historique phases 1–2, analyse du code CI3).
> Attention : la section « Déploiement local » de MIGRATION.md décrit
> l'ANCIEN montage (même hôte + Alias /ci4). Le montage actuel est
> décrit ici.

## Contexte

Migration strangler CI3 → CI4 de l'app GestionVilles.

- **Repo** : `https://github.com/concepnika/gestionvilles-ci4` (branche `main`)
- **Machine de dév actuelle** : Surface Pro 7 — CI3 et CI4 vivent tous
  deux sous `D:\Programmations\php appl\`
- **App CI3 (source de référence)** : `D:\Programmations\php appl\GestionVilles`
  (non versionnée avec CI4 — c'est l'app legacy en prod ; CodeIgniter 3.1.11,
  pas de `.htaccess` racine local → routage vhost requis)
- **Prod cible** : CI4 servi sous `https://robert-daoust.ca/ci4/`
- **PHP local** : 8.2.29 (fcgid), WampServer Apache 2.4.65, MySQL 8.4.7
- **Ancienne machine** : les chemins `e:/devin_projet/...` et
  `E:/Serveur Cloud/...` qui apparaissent dans MIGRATION.md et l'historique
  sont OBSOLÈTES — toujours utiliser les chemins D: ci-dessus.

## Décision d'architecture (2026-10-20) : vhosts séparés

Abandon du montage « même hôte » (`gestionvilles.local/ci4`) au profit de
deux vhosts distincts :

```
gestionvilles.local      → CI3  (DocumentRoot e:/Serveur Cloud/GestionVilles)
gestionvillesci4.local   → CI4  (DocumentRoot e:/devin_projet/gestionvilles-ci4/public)
```

Conséquence : le cookie `ci_session` ne traverse PAS les hôtes. CI4 a
donc sa **propre authentification** (voir plus bas). Les méthodes non
portées redirigent vers CI3 en 307.

### Config Apache à recréer sur une nouvelle machine

`C:\wamp64\bin\apache\apache2.4.65\conf\extra\httpd-vhosts.conf` :

```apache
<VirtualHost *:80>
    ServerName gestionvillesci4.local
    DocumentRoot "d:/Programmations/php appl/gestionvilles-ci4/public"

    # Assets partages avec CI3 : en prod, CI4 vit sous /ci4/ du meme
    # domaine et /assets est servi par CI3 a la racine — l'Alias
    # reproduit ce montage localement (logo, AdminLTE, bootstrap...).
    Alias /assets "d:/Programmations/php appl/GestionVilles/assets"
    <Directory "d:/Programmations/php appl/GestionVilles/assets">
        Require local
    </Directory>

    # Routage front-controller AU NIVEAU VHOST : le .htaccess de
    # public/ garde RewriteBase /ci4/ pour la prod.
    RewriteEngine On
    RewriteRule ^/assets/ - [L]
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-d
    RewriteRule ^/(?!index\.php)(.+)$ /index.php/$1 [L]

    <Directory "d:/Programmations/php appl/gestionvilles-ci4/public">
        Options +Includes +FollowSymLinks +MultiViews
        AllowOverride All
        AcceptPathInfo On
        Require local
    </Directory>

    <IfModule fcgid_module>
        Define FCGIPHPVERSION "8.2.29"
        FcgidInitialEnv PHPRC ${PHPROOT}${FCGIPHPVERSION}
        <Files ~ "\.php$">
            Options +Indexes +Includes +FollowSymLinks +MultiViews +ExecCGI
            AddHandler fcgid-script .php
            FcgidWrapper "${PHPROOT}${FCGIPHPVERSION}/php-cgi.exe" .php
        </Files>
    </IfModule>
</VirtualHost>
```

Un vhost similaire est requis pour CI3 (`gestionvilles.local`,
DocumentRoot `d:/Programmations/php appl/GestionVilles`, même
routage front-controller vhost car pas de `.htaccess` racine local,
PHP 8.2.29).

Fichier `C:\Windows\System32\drivers\etc\hosts` :

```
127.0.0.1	gestionvilles.local
::1	gestionvilles.local
127.0.0.1	gestionvillesci4.local
::1	gestionvillesci4.local
```

Puis redémarrer Apache (`wampapache64`).

### Procédure fresh-clone (Surface Pro 7)

Dans `D:\Programmations\php appl\gestionvilles-ci4` :

1. `composer install` (vendor/ non commité — phar dispo :
   `/c/wamp64/bin/php/php8.2.29/php.exe "/d/Programmations/php appl/composer.phar" install`).
   Si échec `installed.php: Resource temporarily unavailable` (verrou
   antivirus/OneDrive sur les fichiers fraîchement extraits) : relancer.
2. Créer `writable/{cache,logs,session,uploads,cache/osrm}` avec un
   `.gitkeep` dans chacun (sinon : `CacheException` silencieuse →
   `spark` sort en 1 SANS message en environnement production).
3. Créer `.env` (non commité, gitignore) — modèle ci-dessous.
4. Vhosts (ci-dessus) + hosts + démarrage services : lancer
   `setup_gestionvilles_local.bat` **en administrateur** (idempotent :
   entrées hosts + `net start wampmysqld64`/`wampapache64`).
5. Vérifier la config Apache : `httpd.exe -t` → `Syntax OK`.

**Pièges déjà rencontrés** :
- `RewriteBase /ci4/` dans `public/.htaccess` doit rester (prod) → ne
  PAS utiliser le .htaccess pour le routage local, d'où le routage
  vhost ci-dessus. Sinon : boucle de redirections internes → 500
  « Request exceeded the limit of 10 internal redirects ».
- En contexte vhost, `%{REQUEST_FILENAME}` n'est pas encore mappé →
  tester `%{DOCUMENT_ROOT}%{REQUEST_URI}`.
- `AcceptPathInfo On` requis avec mod_fcgid + PATH_INFO.
- `App::$indexPage = ''` requis : avec `'index.php'`, les `redirect()`
  de CI4 pointent vers `/index.php/...` (double redirection sale).
  Le montage vhost (local) et le .htaccess (prod) gèrent les URLs
  propres.

### `.env` (non commité — à recréer)

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://gestionvillesci4.local/'
database.default.hostname = localhost
database.default.database = gestionvilles
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
samsara.apiKey = '<clé reprise de GestionVilles/application/config/samsara.php>'
samsara.baseUrl = 'https://api.samsara.com'
osrm.base = 'http://127.0.0.1:5000'
sanivan.uploadDir = 'D:/Programmations/php appl/GestionVilles/upload/sanivan'
ci3.webroot = 'D:/Programmations/php appl/GestionVilles'
```

## Authentification CI4 (portée)

CI4 authentifie lui-même contre la table partagée `webuser` et crée une
session **au format CI3** (même mécanisme que CI3, même table).

- `app/Libraries/Ci3Session.php`
  - `user()`/`get()`/`isLoggedIn()` : lit cookie `ci_session` → ligne
    `gestionvilles.ci_sessions` → décode `data` (format PHP
    `cle|serialize(valeur)`), `timestamp > now()-7200`.
  - **`create(array $data)`** (nouveau) : insère une ligne
    `ci_sessions` (id hex 40, ip, timestamp, data encodé) + pose le
    cookie `ci_session` sur l'hôte courant. Requis parce que le cookie
    CI3 n'arrive jamais sur le vhost CI4.
  - `set()`/`remove()` : écriture (utilisé par TabletteSani).
  - `destroy()` : supprime la ligne ET expire le cookie.
- `app/Controllers/Login.php` : `index` (formulaire, toujours affiché
  comme `co_gestion/index`), `attempt` (POST), `admin` (≈ `GotoHome`),
  `logout`, `safeNext` (anti open-redirect).
- `app/Views/auth/login.php` : **portée de `partials/login2`** (logo,
  « Gestion des villes », « Connectez-vous pour débuter votre session »,
  « Mot de passe oublié? » → modal → `/inscription/getinfo`). POST vers
  `/admin/tableau_de_bord?from=2` (routes CI3 conservées) ; libellés
  via `ci3lang()` (CI3 n'avait pas de fichiers de langue — clés
  `login_email`, `login_forget`, ... ajoutées au helper) ; assets servis
  par l'Alias vhost `/assets` (miroir prod).
- `app/Filters/Ci3AuthFilter.php` : non connecté → `/login?next=<uri>`.
- **Hachage identique à CI3** :
  `password_verify(hash_hmac('sha256', $mdp, 'nikajhdgsjgdsdssd'), webuser.pass)`
  (pepper = `Login_model::$keysha`, codé en dur dans `Login::PEPPER`
  comme dans CI3).
- Données écrites en session : `email`, `nom` (= `prenom . ' ' . nom`
  comme CI3), `privilege`, `user_id`, `login_in` — clés conformes à
  `co_gestion/login` CI3.

## Routes : table CI3 complète répliquée

`app/Config/Routes.php` reproduit **toutes** les routes de
`application/config/routes.php` CI3 (~350), groupées par contrôleur avec
la correspondance CI3 → classe CI4 :

| CI3 | CI4 | État |
|---|---|---|
| `co_gestion` | `Gestion` (extends `Login`) | auth portée ; ~175 méthodes en stubs |
| `co_tablette_sani` | `TabletteSani` | **porté (phase 2)** |
| `Co_Rest_Sani` | `Api\SaniRest` | **porté (phase 1)** |
| `Co_web_chauffeur` | `WebChauffeur` | **porté (phase 2)** |
| `Co_ville` | `Ville` | **porté (phase 2)** |
| `co_polyline`, `polyline`, `Polyline` | `Polyline` | stubs (fusionnés) |
| `co_admin`, `co_sessions`, `Co_admin_tags`, `Co_villes_admin`, `co_tablette`, `co_rapport`, `co_tvshow`, `co_sanivan`, `co_osrm`, `Sse`, `ssecaminfo`, `Upload`, `upload_collecte`, `Upload_ticket`, `Co_samsara_ifta` | mêmes noms PascalCase | stubs |

**Mécanisme de stub** : `BaseController::toCi3()` → `redirect()->to(
'http://gestionvilles.local' + path + query, 307)` (307 préserve méthode
et corps POST). Chaque méthode non portée = `public function x(...$args) { return $this->toCi3(); }`.

**Pour porter une méthode** : remplacer `return $this->toCi3();` par
l'implémentation réelle dans le contrôleur CI4 — les routes sont déjà
en place, aucune modif de Routes.php nécessaire.

### Limites connues du stub

- Le 307 renvoie vers CI3 qui exigera **sa propre session** (cookie par
  hôte) → l'usager devra être connecté sur `gestionvilles.local` aussi.
- Les POST AJAX cross-origin vers `gestionvilles.local` échoueront sans
  CORS côté CI3 (les POST de formulaire classiques suivent le 307).
- Routes exclues volontairement : `admin/(:any)` (commentée en CI3),
  `default_controller` (CI3 → `Co_ville`; CI4 garde `/` → `Home::index`,
  page statut).

## Vérifications utiles

```bash
# syntaxe
/c/wamp64/bin/php/php8.2.29/php.exe -l app/Controllers/X.php
# liste des routes
/c/wamp64/bin/php/php8.2.29/php.exe spark routes
# test réel
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" \
  http://gestionvillesci4.local/sanivan/calendrier   # → 307 vers CI3
curl http://gestionvillesci4.local/admin/connexion  # → formulaire login
```

## Port Co_web_chauffeur → WebChauffeur (phase 2, 2026-10-20)

Portail web navigateur des chauffeurs SaniVan (~40 routes `web/*`,
`ramassage-ville`). Testé de bout en bout en local avec un chauffeur
de test (login, dashboard, villes, truckmap, APIs JSON, écritures,
bootstrap MAUI, heartbeat, logout) — données de test purgées.

- `app/Controllers/WebChauffeur.php` : port des ~40 méthodes +
  helpers (`checkAuth`, heartbeat throttlé 60s, CORS, flashdata via
  clé `web_flash_error`, dédup GPS Haversine/buckets 10s, Nominatim
  throttlé 60s/camion).
- `app/Libraries/WebChauffeurService.php` : port de
  `Web_chauffeur_model` (SQL verbatim CI3).
- `app/Libraries/SamsaraClient.php` : étendu avec stats GPS
  (`getLatestLocation` avec retry stats→feed→history, batch multi
  véhicules, historique).
- `app/Views/web_chauffeur/` : 6 vues CI3 copiées avec remplacement
  mécanique (`session->userdata` → `ci3sess`, `load->view` → `view`).
- `app/Libraries/Pdf/` : FPDF + font/ copiés de CI3 pour les billets
  de dépotoir (photo → PDF).
- **Session** : clés `web_*` en session CI3 partagée ; le login et le
  bootstrap MAUI **créent** la session via `Ci3Session::create()` si
  absente (garanti par `ensureSession()` — voir `sessSet`).
- **Uploads** : `api_upload_ticket` écrit dans le docroot CI3
  (`env ci3.webroot`), URLs `/uploads/...` servies par CI3 en prod ;
  localement via `Alias /uploads` du vhost (comme `/assets`).
- **Divergences documentées** :
  - `nav_bureau` → `/admin/connexion` (CI3 hardcodait des
    identifiants en clair dans l'URL — non reproduits en repo).
  - `web/api_get_polyline_dynamique` : route AJOUTÉE (appelée par le
    JS de truckmap mais absente de la table CI3 — bug latent corrigé).
  - `api_samsara_diag` / `api_autorisation_spectateur` : non portés
    (jamais routés en CI3, jamais appelés par les vues).

## Port Co_ville → Ville (phase 2, 2026-10-20)

Portail citoyen (~40 routes `villes/*` + `ville/*`), zones de collecte
et tags de carte. Testé de bout en bout en local avec un citoyen de
test (login AES, accueil + carte, liste/création requête, réponse,
profil, polling, tags, hub, logout) — données de test purgées.

- `app/Controllers/Ville.php` : port des ~40 méthodes routées
  (auth citoyenne AES contre `villesrecipiendaire`, requêtes/réponses
  avec fichiers joints, mot de passe oublié par courriel, profil,
  zones (`nav_zone`, `zones_api`, `saveZone`, `save_drawn_polyline`),
  tags de collecte et de carte, `NavZoneHub`, langue fr/en).
- `app/Libraries/VilleService.php` : AES (clé SHA2 de
  `Villes_model::$keysha` — **différente du pepper admin**), zones,
  raisons de tags.
- `app/Config/MapTags.php` : fallback des raisons (config CI3).
- `app/Views/` : 18 vues copiées (villes/*, requetes/lstereception,
  sections/inc_menu_2, partials/menu_motpasse, leaflet/map_nav_zone
  2 800 lignes + map_nav_zone_hub) avec remplacements mécaniques
  (`ci3sess`, `ci3lang`, `ci3_url`, `view`). `villes/incfile/*`
  conservés (déjà adaptés CI4 au port TabletteSani).
- **Session citoyenne** : clés `idville`, `ville`, `nom`, `email`,
  `id`, `site_lang`, `last_page_{fr,en}` — créée par le constructeur
  (détection langue) et le login comme CI3.
- **Uploads** : docroot CI3 (`env ci3.webroot`) —
  `upload/{idville}/{id}` (requêtes), `.../reponse/{id}` (réponses),
  `userfiles/` (summernote), `upload/sanivan/tags/` (images de tags).
- **Divergences par rapport à CI3** (documentées dans le code) :
  - Détection langue : CI3 testait `strpos('/fr/')` (jamais trouvé
    sur les routes finissant par `/fr`) + comparait `FALSE > -1`
    (toujours vrai) → forçait 'fr' en permanence. Le port teste
    `/fr` et `/en` (intention d'origine restaurée).
  - `set_tempdata('codemail')` → clé de session normale (Ci3Session
    n'a pas de tempdata).
  - `getOptStatut` : `ORDER BY description` (colonne absente — bug
    latent CI3) → `ORDER BY description_fr`.
  - Non portés (jamais routés en CI3 ni CI4) : `resetPassword`,
    `mapTagChauffeurs`, `SlugUrl`, `getReponseForReq` est public
    (helper interne), `current_idville` porté mais non routé.
  - `base_url(null)` (CI3 silencieux) → cast string (CI4 strict).

## Reste à faire

- Porter les méthodes stub (ordre recommandé : voir MIGRATION.md
  « Ordre de migration » — `Polyline`, TV, admin ensuite ;
  `co_gestion` en dernier). Les portails externes
  (`Co_web_chauffeur`, `Co_ville`) sont PORTÉS.
- `co_gestion/login` : le tableau de bord n'étant pas porté, un login
  réussi (`from=2`) redirige vers `/` (page statut) au lieu de rendre
  `partials/home` ; `from=1` redirige vers `/sanivan/routecam/{date}`
  (porté). Brancher sur le vrai tableau de bord quand il sera porté.
- Flow `forcechange` : `Login::attempt` redirige vers
  `/login/change?email=...` (stub → 307 CI3) — le flow complet
  (`resetPassW`, vue `chgpassw`, `ValidOldPassW`, `ValidStrongPassW`,
  `PostresetPassW`) est à porter si des comptes ont `forcechange=1`.
- « Mot de passe oublié? » → `/inscription/getinfo` : stub → 307 vers
  CI3 (GET, fonctionne cross-host sans session). Porter `sendPassword`
  + vue `forgot-password` éventuellement.
- `TabletteSani::loginChauffeur` : `$this->ci3->set()` est un no-op sans
  session existante — appeler `Ci3Session::create()` si `user()` est
  null avant les `set()` (le flow tablette sur l'hôte CI4).
- CSRF : les POST CI3 n'ont pas de token — exempter en `Filters` si le
  filtre csrf est activé un jour (actuellement non global).
- `.env` prod : `app.baseURL = 'https://robert-daoust.ca/ci4/'` et
  `.htaccess` `RewriteBase /ci4/` déjà corrects pour ce déploiement.
