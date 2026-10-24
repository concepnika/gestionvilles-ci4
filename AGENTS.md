# AGENTS.md — État du projet GestionVilles CI4

> Point de départ pour une nouvelle session de travail.
> Voir aussi `MIGRATION.md` (historique phases 1–2, analyse du code CI3).
> Attention : la section « Déploiement local » de MIGRATION.md décrit
> l'ANCIEN montage (même hôte + Alias /ci4). Le montage actuel est
> décrit ici.

## Contexte

Migration strangler CI3 → CI4 de l'app GestionVilles.

- **Repo** : `https://github.com/concepnika/gestionvilles-ci4` (branche `main`)
- **App CI3 (source de référence)** : `E:/Serveur Cloud/GestionVilles`
  (non versionnée avec CI4 — c'est l'app legacy en prod)
- **Prod cible** : CI4 servi sous `https://robert-daoust.ca/ci4/`
- **PHP local** : 8.2.29 (fcgid), WampServer Apache 2.4.65

## Décision d'architecture (2026-10-20) : vhosts séparés

Abandon du montage « même hôte » (`gestionvilles.local/ci4`) au profit de
deux vhosts distincts :

```
gestionvilles.local      → CI3  (DocumentRoot e:/Serveur Cloud/GestionVilles)
gestionvillesci4.local   → CI4  (DocumentRoot e:/devin_projet/gestionvilles-ci4/public)
```

Conséquence : le cookie `ci_session` ne traverse PAS les hôtes. CI4 a
donc sa **propre authentification** (voir plus bas). Les méthodes non
portées sont **relayées à CI3 par un proxy interne** (voir plus bas) :
toute l'application fonctionne sur `gestionvillesci4.local` pendant
la migration, avec session partagée.

### Config Apache à recréer sur une nouvelle machine

`C:\wamp64\bin\apache\apache2.4.65\conf\extra\httpd-vhosts.conf` :

```apache
<VirtualHost *:80>
    ServerName gestionvillesci4.local
    DocumentRoot "e:/devin_projet/gestionvilles-ci4/public"

    # Routage front-controller AU NIVEAU VHOST : le .htaccess de
    # public/ garde RewriteBase /ci4/ pour la prod.
    RewriteEngine On
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-d
    RewriteRule ^/(?!index\.php)(.+)$ /index.php/$1 [L]

    <Directory "e:/devin_projet/gestionvilles-ci4/public">
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

Fichier `C:\Windows\System32\drivers\etc\hosts` (déjà présent sur la
machine d'origine) :

```
127.0.0.1	gestionvillesci4.local
::1	gestionvillesci4.local
```

Puis redémarrer Apache (`wampapache64`).

**Pièges déjà rencontrés** :
- `RewriteBase /ci4/` dans `public/.htaccess` doit rester (prod) → ne
  PAS utiliser le .htaccess pour le routage local, d'où le routage
  vhost ci-dessus. Sinon : boucle de redirections internes → 500
  « Request exceeded the limit of 10 internal redirects ».
- En contexte vhost, `%{REQUEST_FILENAME}` n'est pas encore mappé →
  tester `%{DOCUMENT_ROOT}%{REQUEST_URI}`.
- `AcceptPathInfo On` requis avec mod_fcgid + PATH_INFO.

### `.env` (non commité — à recréer)

```ini
app.baseURL = 'http://gestionvillesci4.local/'
database.default.hostname = localhost
database.default.database = gestionvilles
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
ci3.webroot = 'E:/Serveur Cloud/GestionVilles'
mapbox.accessToken = '<jeton pk.…>'
# + samsara.apiKey, samsara.baseUrl, osrm.base, sanivan.uploadDir
#   (voir MIGRATION.md § ".env")
```

### Checklist « nouvelle machine »

1. `git clone https://github.com/concepnika/gestionvilles-ci4`
2. `composer install`
3. Recréer `.env` ci-dessus (adapter `ci3.webroot` au chemin local
   de l'app CI3 — **pas en version CI4 dans ce repo**)
4. Copier l'app CI3 `E:/Serveur Cloud/GestionVilles` (non versionnée)
   + importer la BD `gestionvilles` dans MySQL
5. Vhost `gestionvillesci4.local` + hosts (bloc Apache ci-dessus)
6. Schéma local : s'assurer que `camionjour` a les colonnes
   `debut1..4`, `fin1..4`, `Depotoire1..4`, `Ticket1..4`, `Tonnage1..4`
   (ajoutées sur la machine d'origine — voir note plus bas)
7. Test : `php tools/mksess.php` → cookie `ci_session`, puis
   `./sweep.sh` (balayage CI3 vs CI4)

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
- `app/Views/auth/login.php` : formulaire simple (la vraie vue CI3
  `partials/login2` n'est pas encore portée).
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
| `co_gestion` | `Gestion` (extends `Login`) | **entièrement porté** (146 méthodes via `port_controller.php`, parité balayage `sweep.sh`) |
| `co_tablette_sani` | `TabletteSani` | **porté (phase 2)** + `getSaniReport`/`exportSaniExcel` (2026-10-24) |
| `Co_Rest_Sani` | `Api\SaniRest` | **porté (phase 1)** |
| `Co_ville` | `Ville` | **porté (phase 2)** |
| `Co_web_chauffeur` | `WebChauffeur` | **porté (phase 2)** |
| `co_polyline`, `polyline`, `Polyline` | `Polyline` | **porté (2026-10-24)** — fusion des deux fichiers CI3 |
| `co_sessions` | `Sessions` | **porté (2026-10-24)** |
| `Co_admin_tags` | `AdminTags` | **porté (2026-10-24)** |
| `Co_villes_admin` | `VillesAdmin` | **porté (2026-10-24)** |
| `co_tvshow` | `Tvshow` | **porté (2026-10-24)** |
| `co_osrm` | `Osrm` | **porté (2026-10-24)** |
| `co_rapport` | `Rapport` | **porté (2026-10-24)** — seule `ShowTDBFinancier` est routée en CI3, seule méthode portée |
| `co_sanivan` | `Sanivan` | **porté (2026-10-24)** — seul `upload_pdf_ticket` est routé en CI3, seule méthode portée |
| `Sse` | `Sse` | **porté (2026-10-24)** — voir divergences SSE |
| `Upload` | `Upload` | **porté (2026-10-24)** |
| `Upload_ticket` | `UploadTicket` | **porté (2026-10-24)** |
| `Co_samsara_ifta` | `SamsaraIfta` | **porté (2026-10-24)** — 3 méthodes, exportExcel sur PhpSpreadsheet |
| `co_admin`, `co_tablette`, `ssecaminfo`, `upload_collecte` | `Admin`, `Tablette`, `Ssecaminfo`, `UploadCollecte` | **routes mortes en CI3** — stubs qui relaient le même 404/500 (voir « Routes mortes CI3 ») |

### Routes mortes CI3 (vérifiées 2026-10-24 — rien à porter)

Ces routes sont référencées par routes.php CI3 mais les
contrôleurs/méthodes n'existent pas ou plantent : CI3 rend 404/500,
les stubs CI4 relaient exactement la même réponse via le proxy.

- `tablettes`, `tablettes/acces`, `tablettes/savelocation` :
  contrôleur `co_tablette` **inexistant** (404).
- `sanivan/ssecam` : contrôleur `ssecaminfo` **inexistant** (404).
- `api/upload-collecte` : fichier renommé `Upload_collecte_none.php`
  (fonction désactivée, 404).
- `admin/tableau_de_bord/commandes/(:any)` : `Clientscharts_model`
  **inexistant** (500).
- `admin/page_de_site`, `sanivan/setcamion`, `sanivan/excopie`,
  `sanivan/getallcamion`, `sanivan/getrh`, `sanivan/getcam`,
  `envoiville` : méthodes `getPage`/`updCamion`/`excCedule`/
  `getAllCamion`/`getRHCal`/`getCamCal`/`sendConfirmVille` définies
  **nulle part** dans l'app CI3 (404) — stubs correspondants dans
  `Gestion`.
- co_rapport/co_sanivan : toutes les AUTRES méthodes de ces
  contrôleurs ne sont routées nulle part (module roll-off/bureau
  jamais branché) — non portées.

**Mécanisme de stub — proxy interne** : `BaseController::toCi3()`
exécute la même requête sur `http://gestionvilles.local` via
`Services::curlrequest()` (méthode, corps et en-têtes Cookie/
Content-Type/Accept/X-Requested-With/Referer/User-Agent relayés) puis
renvoie statut + en-têtes + corps de la réponse CI3 :

- `Location` et les URLs `gestionvilles.local` dans les corps
  HTML/JSON/texte sont réécrites en `gestionvillesci4.local` → la
  navigation reste sur l'hôte CI4.
- `Set-Cookie` est relayé sans `domain` → `ci_session` s'applique à
  l'hôte CI4 → **session partagée CI3↔CI4** (une ligne `ci_sessions`
  lue par les deux apps).
- Route catch-all `(:any)` → `Proxy::pass` en fin de `Routes.php` :
  relaie aussi les assets du docroot CI3 (`upload/*`, images, JS) et
  toute URL non déclarée.
- **Piège sess_match_ip** : CI3 a `sess_match_ip = true` et le hosts
  local mappe `gestionvilles.local` sur `::1` ET `127.0.0.1`. Le proxy
  appelle donc `http://127.0.0.1` + `Host: gestionvilles.local` pour
  garantir une IP source constante — sinon CI3 invalide la session à
  chaque bascule IPv4/IPv6 (redirection vers `/`).
- **Format id session** : les ids `ci_sessions` CI3 font 32 chars
  `[0-9a-v]` (sid_bits=5). `Ci3Session::create()` reproduit ce format
  et enregistre `ip_address = '127.0.0.1'` (l'IP que CI3 voit via le
  proxy), sinon CI3 rejetterait la session lors des appels proxies.
  Une session créée avant ce correctif (ip `::1`) est invalide →
  se reconnecter.

**Pour porter une méthode** : remplacer `return $this->toCi3();` par
l'implémentation réelle dans le contrôleur CI4 — les routes sont déjà
en place, aucune modif de Routes.php nécessaire.

### Chaîne de port (outillage en place)

- **`port_view.php`** (racine) : `php port_view.php partials/home ...`
  copie une vue CI3 vers `app/Views/` avec transformation mécanique
  (`$this->load->view` → `echo view()`, `userdata` → `ci3sess`,
  `base_url`/`site_url` → `ci3_url`, `lang->line` → `ci3lang`,
  `input->post_get` → `ci3in`, garde BASEPATH retirée).
- **`app/Helpers/ci3view_helper.php`** : `ci3sess()`/`ci3set()`/
  `ci3lang()`/`ci3_url()`/`ci3in()` — penser à `helper('ci3view')`
  dans le contrôleur avant `view()`.
- **`App\Models\Ci3Model`** : base des modèles portés — fournit
  `$this->db` (connexion CI4, API quasi identique CI3), `$this->input`
  (post_get/get/post), `$this->session` (adaptateur userdata/set_userdata/
  has_userdata + magic `->email`), `$this->data`/`objData`,
  `JourDeSemaine`, `$this->sess()`.
- **Session auto-créée (2026-10-24)** : `Ci3SessionAdapter::set_userdata`
  matérialise la ligne `ci_sessions` + cookie si absente. CI3 auto-créait
  la session à chaque visite (le cookie existe toujours) ; sur le vhost
  CI4, sans ceci `set()` est un no-op silencieux — le login rendait le
  dashboard sans session persistante, puis chaque clic sur le menu
  ramenait à la page de connexion. `BaseController::initController`
  écrit `site_lang` → la session existe avant tout code de contrôleur.
- ⚠️ Ne pas redéclarer `$db` (ni `$input`/`$session`/`$load`/`$output`)
  dans les contrôleurs : `BaseController` les fournit (`Ci3Db`, etc.)
  — une redéclaration `private` est un fatal PHP (visibilité réduite).
- ⚠️ `Gestion::login` : CI3 testait `isset($result)` alors que
  `validate_user` retourne `FALSE` sur échec (`isset(FALSE)` vaut TRUE
  → CI3 rendait le dashboard avec identité nulle ; en CI4 le warning
  « property on bool » devient une exception 500). Corrigé en
  `!empty($result)` (pattern du `loginChauffeur` CI3) — **divergence
  voulue** : mauvais identifiants → « Courriel ou mot de passe
  invalide » au lieu d'un faux login.
- **`port_model.php`** (racine) : `php port_model.php Cedules_model`
  copie un modèle CI3 ENTIER vers `app/Models/XModel.php` avec
  transformation mécanique (classe, loaders, `load->model` →
  `new XModel()`, session, `result()/row()/num_rows()` → API CI4,
  `private $` → `protected $`, `new stdClass` → `new \stdClass`,
  Query Builder `db->insert/update/set/get_where` → `table()`).
  `CedulesModel`/`CreationcdModel`/`RequetesModel` sont des ports
  complets ainsi générés — **NE PAS éditer à la main**, relancer le
  script (les modifs manuelles sont perdues).
- **`App\Libraries\Ci3Upload`** : adaptateur de `$this->upload` CI3
  (`do_upload`/`display_errors`/`file_name` + `allowed_types`,
  `max_size`, `file_name` imposé, `data()` → file_name/file_type/
  file_size Ko comme CI3). Les chemins `./upload/…` sont résolus
  contre `ci3.webroot` (.env) pour que CI3 et CI4 voient les mêmes
  fichiers. `port_model.php` convertit
  `load->library('upload', $config)` en `new Ci3Upload($config)`.
- ⚠️ **Codes HTTP en CI4** : `http_response_code()`/`set_status_header()`
  bruts sont **écrasés** par la Response CI4 au rendu (statut 200).
  Passer par `$this->output->set_status_header($n)` (l'adaptateur
  `Ci3Output` pose le statut sur la Response partagée) ou
  `return $this->response->setStatusCode($n)->setBody(...)`.
- `Ci3SessionAdapter` supporte `flashdata`/`keep_flashdata` (marqueur
  `__flash` en fin de payload, expiré comme CI3 à l'itération suivante).
- `port_controller.php` : la regex d'extraction des méthodes accepte
  les **types de retour** (`: array`, `: ?string` — sinon les helpers
  privés typés étaient perdus). Ne porte pas les propriétés de classe
  ni les constructeurs CI3 → les transposer dans `initController`
  (ex. filtres Kalman de `Polyline`, `$JourDeSemaine` de `Tvshow`).
- Modèles renommés PSR-4 (2026-10-24) : `MapTagsModel.php` contenait
  `class Map_tagsModel` (mismatch autoload fatal) — renommés en
  `MapTagsModel`/`PdfTicketModel`/`TblDepotModel`/`TblUserModel`/
  `RolloffParamsModel` + `SanivanModel.php` contenait une classe
  utilitaire `Collection` en tête (le modèle réel est plus bas).
- ⚠️ `catch (Exception $e)` dans un namespace : attrape
  `App\Controllers\Exception` (inexistante) → jamais attrapée.
  Toujours `catch (\Exception $e)` (fix appliqué dans `Gestion`,
  `UploadTicket`).
- `Ci3Db::get()` : n'appelle `BaseBuilder::get($limit, $offset)` qu'avec
  des entiers — passer `null` lève un `TypeError` (param non-nullable).
- **SSE (`Sse::index`)** : flux `while(true)+sleep(10)` — le port vide
  tous les niveaux de buffer (`ob_flush`+`flush` itératif) et lève
  `set_time_limit(0)` car CI4 bufferise contrairement à CI3. Test
  local : 0 octet des deux côtés sous 16 s (scan `gps_sani` lent) —
  parité, à re-tester avec un vrai client longue durée.
- **`SamsaraClient`** : `getIftaVehicleReport()` (POST API Samsara
  `/beta/ifta/vehicle-report`) + `getKmByCamion()` (agrégation BD
  par camion) — port de `Samsaralib` CI3 pour `SamsaraIfta::getData`.
- `UploadTicket::_generateTicketPdf` : upload + PDF écrivent sous
  `env('ci3.webroot')` (pas `FCPATH`, qui pointe sur `public/` CI4)
  pour que CI3 serve les mêmes fichiers.
- ⚠️ `strpos($x, ',', 2)` : PHP 8 lève `ValueError` si offset >
  longueur (CI3/PHP7 retournait false) — garder avec `strlen() > 2`
  lors du port.
- ⚠️ Variables de vues non fournies : CI3 tolérait les `undefined`,
  CI4 lève `ErrorException` — initialiser les clés lues par la vue.
- ⚠️ `explode('-', $d)[1]` : `+ [1 => null]` pour reproduire
  l'index manquant toléré par CI3.
- **`App\Libraries\LibCalendrier`** : port de la librairie CI3
  (`getCalendrierVille` génère le HTML des calendriers par ville).
- ⚠️ `FrequenceDeploie` case 5 : CI3 utilisait `DateInterval('1 third')`
  (false → crash) ; le port utilise `'1 month'` + `third X of this
  month` — intention réelle, déviation assumée.
- ⚠️ Schéma local : `camionjour` manquait `debut1..4`, `fin1..4`,
  `Depotoire1..4`, `Ticket1..4`, `Tonnage1..4` (prod les a) —
  ajoutées localement le 2026-10-23 pour tester la validation.
- `port_model.php` a une section `$patches` (par modèle) pour les
  corrections non génériques — ex. init de `$backColor1..3` avant les
  boucles de `getRapportJour`/`getRapportJourRes`/`getValidChauffeurJour`
  (CI3 réutilisait la valeur de l'itération précédente).
- **Vues portées** : ~180 vues via `port_view.php` (toutes sauf
  `errors/` et `sanivan/index.php` — ce dernier dépend de classes
  legacy inexistantes `ClassCedule`/`ClassConnect`).
- Debugbar CI4 **désactivée** (`Filters::$required`) — elle injectait
  ~28 Ko dans chaque réponse, fragments AJAX inclus.
- ⚠️ `$this->session->userdata['x']` (forme tableau) en CI3 = accès
  tableau sur la méthode → **toujours null** (via `__get`). Les vues
  convertissent vers `ci3sess('userdata')['x'] ?? null` pour garder
  ce comportement (ex. switch de menu de `partials/calendrier*.php`,
  dont les vues `menu_supersuser`/`menu_superviseur`/… n'existent
  pas même en CI3).
- ⚠️ `Ci3Db` : `query()`/terminaux QB retournent `false` sur
  `DatabaseException` (contrat CI3 `db_debug=false`) ; erreurs dans
  `writable/logs`. `getResult()` sur `false` = fatal, comme CI3.
- Libs PDF legacy (`application/controllers/pdf/*.php`) : incluses
  via `env('ci3.webroot')` + `chdir()` préalable pour résoudre les
  chemins relatifs `./assets/`/`./upload/` ; `new \PDF()` global.
- `APPPATH` CI3 → `env('ci3.webroot') . '/application/'`.
- **PhpSpreadsheet (2026-10-24)** remplace PHPExcel (incompatible
  PHP 8, 500 côté CI3 local) : `composer require
  phpoffice/phpspreadsheet` (^5.10). Les 5 exports Excel sont
  natifs et testés : `excel/cedule`, `excel/hres` (+`modal/hres`),
  `villes/requeteexcel`, `samsara_ifta/exportExcel`,
  `rapport/sani/excel` (port `TabletteSani::exportSaniExcel`).
  `port_model.php` mappe les classes PHPExcel → PhpSpreadsheet et
  réécrit `./upload|assets|formulaires/…` contre `ci3.webroot`
  (CWD CI4 = public/, pas le docroot CI3). ⚠️ Divergences d'API :
  colonnes 0-based PHPExcel → 1-based (`setCellValue([$col+1, $row])`),
  clés de style `type/startcolor` → `fillType/startColor`,
  `setCellValueByColumnAndRow` supprimé en v5. Les noms de fichiers
  exotiques (`cedule_'.DATE.xlsx` avec apostrophe+point) sont
  fidèles à CI3 (vérifiés contre les fichiers produits par CI3).
- **`app/Helpers/ci3compat_helper.php`** : polyfills `force_download`
  (headers sur la Response CI4 partagée + corps via echo→ob) et
  `delete_files` (no-op sur un chemin de fichier, comme CI3 —
  les fichiers exports restent dans upload/, comportement CI3).
  Chargé par `BaseController::initController` et `Ci3Model`.
- Classes utilitaires legacy dans les modèles (ex. `CamDay` dans
  SanivanModel) : `$this->CI =& get_instance()` → `new \stdClass()`
  avec `->db = new Ci3Db()` (règle `port_model.php`) — les appels
  `$this->CI->db->…` restent valides. ⚠️ Le `__get` magique CI3
  des modèles transférait `$this->x` vers le super-objet global
  (propriétés chargées par le contrôleur) : en CI4 il faut
  instancier dans le modèle (patch Sanivan : `$this->cedules =
  new CedulesModel()` dans le constructeur).
- ⚠️ Table `requetes` vide en local → `villes/reponsepdf/*` échoue
  dans les deux apps (parité 500).
- `sweep.sh` : balayage comparatif CI3 vs CI4 (~60 routes,
  codes + tailles). `./sweep.sh [filtre]`.

### Limites connues du proxy

- Le proxy dépend de la config DNS locale : `gestionvilles.local` doit
  résoudre vers 127.0.0.1 (fichier hosts) et le vhost CI3 doit tourner.
- `Accept-Language` est relayé — `Co_ville::index` boucle en 307 sans
  cet en-tête (comportement CI3 normal, voir MIGRATION.md).
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

## Reste à faire

- **Ports terminés (2026-10-24)** : **toutes les routes vivantes CI3
  sont portées nativement** — `getSaniReport` (rapport Sanivan du
  jour, parité 558 Ko à 26 o près) était la dernière. Le proxy ne
  reste que pour les routes mortes CI3 (voir « Routes mortes CI3 »)
  et le catch-all de sécurité (assets du docroot CI3, chemins non
  déclarés). Divergences du balayage final, toutes voulues :
  - `sanivan/calrh`, `sanivan/calcam` : CI3 500 flaky → CI4 200 ✓
  - `villes/send-reponse` en GET : CI3 500 → CI4 302 propre ✓
  - `villes/charger/*` : CI4 renvoie une 302 explicite au lieu du
    rendu `upload_form` de CI3 (fonctionnellement équivalent).
  - **Les 5 exports Excel** (`excel/cedule`, `excel/hres`,
    `villes/requeteexcel`, `samsara_ifta/exportExcel`,
    `rapport/sani/excel`) : CI3 500 (PHPExcel mort sous PHP 8) →
    CI4 200 avec fichiers valides (PhpSpreadsheet) ✓ — données
    réelles vérifiées (cedules, hres aides, rapport sani 13 camions,
    liste requetes template+logo). Note locale : l'API Samsara
    échoue en SSL sur WAMP (pas de bundle CA) — même erreur côté
    CI3, parité ; la prod a un CA correct.
- Éprouver les POST réels côté UI (formulaires complets avec données
  valides) — les tests automatisés couvrent surtout les GET et
  quelques POST représentatifs (login, upload-ticket, polyline).
- SSE : re-tester `sanivan/sse` avec un client longue durée (parité
  0 octet constatée localement, scan BD lent des deux côtés).
- CSRF : les POST CI3 n'ont pas de token — exempter en `Filters` si le
  filtre csrf est activé un jour (actuellement non global).
- Dettes techniques CI3 héritées : SQL concaténé dans les modèles,
  HTML généré dans les modèles, `ci_sessions` MyISAM → InnoDB.
- `.env` prod : `app.baseURL = 'https://robert-daoust.ca/ci4/'` et
  `.htaccess` `RewriteBase /ci4/` déjà corrects pour ce déploiement.

### Bascule finale en production (fin de la migration)

1. Vérifier PHP ≥ 8.2 sur le cPanel de prod (les exports Excel sont
   maintenant sur PhpSpreadsheet, compatibles PHP 8).
2. Router **tout** le trafic vers CI4 : aujourd'hui tablettes,
   chauffeurs et citoyens atteignent CI3 à la racine du domaine —
   CI4 ne vit que sous `/ci4/`.
3. Rapatrier les **assets** (`/assets` servi par le docroot CI3) et
   les **uploads** (CI4 écrit déjà dans le docroot CI3 via
   `ci3.webroot` — basculer cette config vers le docroot CI4).
4. Retirer le proxy `toCi3()` + le catch-all `Proxy::pass`.
5. Éteindre CI3.
