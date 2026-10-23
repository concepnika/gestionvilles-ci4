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
# + samsara.apiKey, samsara.baseUrl, osrm.base, sanivan.uploadDir,
#   ci3.webroot (voir MIGRATION.md § ".env")
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
| `co_gestion` | `Gestion` (extends `Login`) | auth, `GotoHome`, `showToday` portés ; ~173 stubs |
| `co_tablette_sani` | `TabletteSani` | **porté (phase 2)** |
| `Co_Rest_Sani` | `Api\SaniRest` | **porté (phase 1)** |
| `Co_ville` | `Ville` | stubs |
| `Co_web_chauffeur` | `WebChauffeur` | stubs |
| `co_polyline`, `polyline`, `Polyline` | `Polyline` | stubs (fusionnés) |
| `co_admin`, `co_sessions`, `Co_admin_tags`, `Co_villes_admin`, `co_tablette`, `co_rapport`, `co_tvshow`, `co_sanivan`, `co_osrm`, `Sse`, `ssecaminfo`, `Upload`, `upload_collecte`, `Upload_ticket`, `Co_samsara_ifta` | mêmes noms PascalCase | stubs |

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
- **`port_model.php`** (racine) : `php port_model.php Cedules_model`
  copie un modèle CI3 ENTIER vers `app/Models/XModel.php` avec
  transformation mécanique (classe, loaders, `load->model` →
  `new XModel()`, session, `result()/row()/num_rows()` → API CI4,
  `private $` → `protected $`, `new stdClass` → `new \stdClass`,
  Query Builder `db->insert/update/set/get_where` → `table()`).
  `CedulesModel`/`CreationcdModel`/`RequetesModel` sont des ports
  complets ainsi générés — **NE PAS éditer à la main**, relancer le
  script (les modifs manuelles sont perdues).
- ⚠️ Reste dans les modèles portés : `$this->upload` (Requetes) non
  adapté — les méthodes upload fatalement si appelées.
- **Vues portées** : `auth/login` (= login2), `partials/{home,navtop,
  navleft}`, `sections/{menu,inc_pop_up_gen}`.
- ⚠️ La debugbar CI4 s'injecte dans toutes les réponses HTML
  (portées ET proxifiées) — `CI_ENVIRONMENT=production` dans `.env`
  la désactive si elle gêne les tests.

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

- Porter les méthodes stub (ordre recommandé : voir MIGRATION.md
  « Ordre de migration » — `Co_web_chauffeur`, `Co_ville` en priorité
  phase 2 ; `co_gestion` en dernier).
- Porter la vue de login CI3 (`partials/login2` : logo, « Connectez-vous
  pour débuter votre session », « Mot de passe oublié? »).
- `TabletteSani::loginChauffeur` : `$this->ci3->set()` est un no-op sans
  session existante — appeler `Ci3Session::create()` si `user()` est
  null avant les `set()` (le flow tablette sur l'hôte CI4).
- CSRF : les POST CI3 n'ont pas de token — exempter en `Filters` si le
  filtre csrf est activé un jour (actuellement non global).
- `.env` prod : `app.baseURL = 'https://robert-daoust.ca/ci4/'` et
  `.htaccess` `RewriteBase /ci4/` déjà corrects pour ce déploiement.
