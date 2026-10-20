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
portées redirigent vers CI3 en 307.

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
| `co_gestion` | `Gestion` (extends `Login`) | auth portée ; ~175 méthodes en stubs |
| `co_tablette_sani` | `TabletteSani` | **porté (phase 2)** |
| `Co_Rest_Sani` | `Api\SaniRest` | **porté (phase 1)** |
| `Co_ville` | `Ville` | stubs |
| `Co_web_chauffeur` | `WebChauffeur` | stubs |
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
