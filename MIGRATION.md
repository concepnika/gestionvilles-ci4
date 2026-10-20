# Migration CI3 → CI4 (strangler)

> **Mise à jour 2026-10-20** : le développement local utilise désormais
> un vhost dédié `gestionvillesci4.local` (hôte séparé de CI3) avec
> authentification CI4 propre. Voir `AGENTS.md` pour la config actuelle ;
> la section « Déploiement local de test » ci-dessous décrit l'ancien
> montage (Alias `/ci4`), conservé à titre de référence.

Approche : les deux applications cohabitent en production. CI4 répond
sous `/ci4/`; CI3 continue de servir le reste. Chaque module est porté
et basculé indépendamment; la bascule finale remplace CI3 par CI4 à
la racine.

## État actuel (pilote validé en local)

- `ci4/` = squelette CodeIgniter **4.7.5** (requiert PHP ≥ 8.2)
- Pont d'authentification : `app/Libraries/Ci3Session.php` lit le cookie
  `ci_session` → ligne dans `gestionvilles.ci_sessions` → décode `data`
  (format session PHP). Filtre `ci3auth` (`app/Filters/Ci3AuthFilter.php`)
  redirige vers `/` si non connecté. **Aucun re-login requis** tant que
  les deux apps sont sur le même hôte.
- Endpoints pilotes :
  - `GET /whoami` → session CI3 décodée (JSON)
  - `GET /cedules/{AAAA-MM-JJ}` → cédule du jour, lecture seule (JSON)
- **Phase 1 portée — API tablettes (Co_Rest_Sani)** : `app/Controllers/Api/SaniRest.php`
  reproduit les ~40 actions de `index_get` + `index_post`/`postCamion`/`saveBatch`/`saveToFile`,
  avec `app/Libraries/SaniRestService.php` (port LibSaniRest), `OsrmSnapper.php`
  (snapping OSRM, cache `writable/cache/osrm`) et `SamsaraClient.php`.
  Mêmes URIs qu'en CI3 (`sanivan/get`, `sanivan/post/depot`, `api/add_to_polyline`…)
  → les tablettes ne changeront que la base URL. **Pas de filtre ci3auth**
  (auth par paramètres comme CI3). Réponses JSON-identiques à CI3
  (chaînes quotées, scalaires nus).
- `.env` : `database.default` = gestionvilles (local), `samsara.apiKey`/`samsara.baseUrl`
  (repris de config/samsara.php), `osrm.base` = http://127.0.0.1:5000,
  `sanivan.uploadDir` = chemin absolu vers `upload/sanivan` du docroot CI3
  (les URLs `/upload/sanivan/...` retournées aux tablettes restent servies
  par CI3), `ci3.webroot` = docroot CI3 (pour `upload/gpx`).
  Pour prod, dupliquer avec baseURL `https://robert-daoust.ca/ci4/`.

- **Phase 2 portée — Tablettes Sanivan (Co_tablette_sani, ~63 routes)** :
  `app/Controllers/TabletteSani.php` reproduit les ~60 méthodes du
  contrôleur CI3 (~4 960 lignes) : pages login/tablette, fragments HTML
  ramassages/dépotoires, endpoints carte/polylines, writes jour
  (`postchauf`, `postrh`, `postville`, `postdep`, `sendcaminfo`…),
  import GPS Samsara (`sanivan/import_gps`), tags carte, upload billets
  (`saveimg` → même arborescence `upload/sanivan/<cam>/<date>/<id>/`).
  - `app/Libraries/TabletteService.php` : méthodes de `Sanivan_model`,
    `login_model` (HMAC-SHA256 + password_verify), `Tablet_tags_model`,
    `Cedules_model::GetAllDepotoires`.
  - `app/Libraries/TabletteHtml.php` : générateurs de fragments HTML
    (getInfoVille, getInfoDepotoire, getinfoAides, getHeadrerRte,
    scripts Google Maps, GetCamOnRoad, menus).
  - `app/Helpers/ci3view_helper.php` : `ci3sess()`, `ci3_url()`,
    `ci3lang()` — compatibilité des vues CI3 copiées dans `app/Views/`
    (`load->view` → `view`, `base_url` → `ci3_url` root-absolu,
    `session->userdata` → `ci3sess`, `lang->line` → `ci3lang`).
  - `app/Config/Constants.php` définit `BASEPATH` (garde des vues CI3).
  - `app/Config/App.php` : `permittedURIChars` élargi (`,@+`) pour les
    URLs contenant des coordonnées polyline.
  - Catch-all `co_tablette_sani/<methode>` reproduit le routage par
    défaut de CI3 pour les méthodes sans route déclarée.
  - Session CI3 en **écriture** : `loginChauffeur`/`addChauffeur`/
    `setChauffeurOnRoad` écrivent `nom`, `user_id`, `camion`, `kmdepart`,
    `login_in` dans `ci_sessions` via `Ci3Session::set()`.
  - **Non portés (restent sur CI3)** : `rapport/sani/*` (`getSaniReport`,
    machinerie CamDay ~850 lignes), `rapport/sani/excel/*`
    (`exportSaniExcel`, PHPExcel → PhpSpreadsheet à faire séparément),
    `sanivan/getpolyline/*` (contrôleur `co_polyline` distinct),
    `sanivan/ssecam` (vue `ssecaminfo` avec credentials externes en
    clair — à réécrire si encore nécessaire).

### Vérifications phase 2 (spark serve, local)

- Login tablette `tablettes_san/acces` → page 200 ; `addChauffeur` par
  code → page `routes_san` complète + session CI3 écrite
- `sanivan/getpolyline_all/{date}?nocamion=X` → tracé au format CI3
  (`lat,lng, timespan:…, chauffeur:…, adresse:…`)
- `getpolylines_city(_raw)`, `get_ramassages_city`,
  `get_last_positions_city`, `get_last_pos_camion`, `get_gps_count_city`
  → JSON réels (données 2026-08-06)
- Écritures : `postLocation` (gps_sani + gps_sani_last), `postville`,
  `postdep`, `postchauf`, `sendvilleinfo`, `setValForSan`, `track` → OK
- Uploads : `saveimg` → `upload/sanivan/<cam>/<date>/<id>/pic_*.jpeg`
  dans le docroot CI3, URL `/upload/sanivan/...` retournée
- `sanivan/import_gps` : CORS OPTIONS → 200, POST JSON → insert `gps_points`
- Pages : `routecam` (map_nav_all), `scan/ticket`, `add_acces` → 200

### Pièges rencontrés (phase 2)

- `permittedURIChars` CI4 rejette les virgules → 400 sur les URLs de
  coordonnées (élargi dans `App.php`).
- CI4 convertit les warnings PHP en exceptions : les variables de vue
  non définies (notices silencieuses en CI3) deviennent fatales →
  initialiser toutes les clés `$data` et neutraliser les `echo` dans
  les commentaires des includes (`inc_javascript.php`).
- `request->getMethod()` retourne la méthode en minuscules par défaut —
  utiliser `getMethod(true)` pour les comparaisons OPTIONS/POST.
- Les tables `polilyne` (sans `_ville`) n'existent pas même en prod →
  `PostPolyline` est du code mort en CI3 (erreur silencieuse reproduite).

## Inventaire CI3 (409 routes dans application/config/routes.php)

| Contrôleur | ~Routes | Contenu | Priorité migration |
|---|---|---|---|
| `co_gestion` | ~203 | Supervisor grid, admin, cédules, validation | **Dernier** (le plus couplé) |
| `co_tablette_sani` | ~63 | Endpoints tablettes camions | Phase 2 (JSON pur, idéal tôt) |
| `Co_ville` | ~51 | Interface villes (requêtes citoyennes) | Phase 2 |
| `Co_web_chauffeur` | ~37 | Portail chauffeurs web | Phase 2 |
| `Co_Rest_Sani` | 7 | REST GPS | Phase 1 (API, testable) |
| `Co_villes_admin` | 7 | Admin villes | Phase 3 |
| `Co_admin_tags` | 5 | Admin tags | Phase 3 |
| `Polyline`/`co_polyline` | 6 | Polylines parcours | Phase 2 |
| `Co_samsara_ifta` | 3 | Rapports IFTA | Phase 3 |
| `co_tvshow` | 2 | Affichage TV | Phase 3 |
| `co_sessions` | 2 | Sessions/lock screen | **En dernier** (auth) |
| Divers | ~20 | upload, excel, rapport, samsara sync | Phase 3 |

## Ordre de migration recommandé

1. **Endpoints JSON/REST** (`Co_Rest_Sani`, `co_tablette_sani`) — pas de
   vues, testables, couplage faible. Attention : ce sont les chemins
   d'écriture GPS → `gps_sani_last` (upserts déjà en place).
2. **Portails externes** (`Co_web_chauffeur`, `Co_ville`) — vues
   contenues, logique bornée.
3. **Affichages TV** (`co_tvshow`, `showToday` from=3/8) — lecture
   seule, bon candidat pour refondre le rendu.
4. **Pages admin** (`Co_villes_admin`, `Co_admin_tags`).
5. **Le gros morceau** (`co_gestion` : cédule superviseur, validation,
   calendrier) — en dernier, possiblement en sous-étapes. C'est là que
   vit le code qui gèle (voir analyse perf).
6. **Sessions/auth** (`co_sessions`, login) — bascule finale.

## Points d'attention connus

- **PHP prod** : vérifier ≥ 8.2 dans cPanel (MultiPHP). Sinon pin CI 4.6.x
  (≥ 8.1) ou monter le PHP d'abord.
- **Déploy prod** : contenu de `ci4/public/` → `public_html/ci4/` et le
  reste de `ci4/` hors web root; ajuster les chemins dans `index.php`.
- **Sessions CI3** : `sess_expiration = 7200`; le pont vérifie
  `timestamp > now()-7200`. Si CI3 passe en session file un jour,
  adapter `Ci3Session`.
- **PHPExcel** → PhpSpreadsheet dans les modules export Excel.
- **HTML généré dans les modèles** (Cedules_model etc.) : porter vers
  des vues CI4, ne pas traduire tel quel.
- **SQL concaténé** : CI4 Query Builder ou requêtes préparées —
  corriger au passage (le code CI3 échappe à la main).
- **`ci_sessions` est MyISAM** : à convertir en InnoDB avec le reste.
- **CSRF** : les POST des tablettes/REST n'ont pas de token CI4 —
  exempter ces routes ou gérer en `Filters` config.
- Le cookie `ci_session` est posé par CI3 au login ; les pages CI4 ne
  doivent PAS écrire la session CI3 (lecture seule du bridge).

## Déploiement local de test (WampServer)

Vhost `gestionvilles.local` configuré dans `httpd-vhosts.conf` :
CI3 à `/` (DocumentRoot = racine projet) et CI4 à `/ci4/`
(Alias → `E:/devin_projet/gestionvilles-ci4/public` — CI4 vit dans
son propre dossier, hors de l'arborescence CI3). Activation unique : lancer
`setup_gestionvilles_local.bat` en administrateur (ajoute l'entrée
hosts + redémarre Apache). Ensuite :

```
http://gestionvilles.local/              → CI3 (app complète)
http://gestionvilles.local/ci4/whoami    → CI4 (partage la session)
http://gestionvilles.local/ci4/sanivan/get?action=2
```

Le cookie `ci_session` (path `/`, même hôte) est partagé CI3 ↔ CI4 —
miroir exact du déploiement prod prévu.

**Validé sous Apache (2025)** : `/ci4/whoami` → 302 login,
`sanivan/get?action=2/8` → JSON, `POST action=5` → insert gps_sani +
upsert gps_sani_last.

**Piège routage Apache + Alias** : les `.htaccess` per-dir ne routent
PAS correctement sous `Alias` + mod_fcgid ("No input file specified" /
404). Le routage est fait dans le **vhost** (`AllowOverride None` sur
`ci4/public`) :

```apache
Alias /ci4 "e:/devin_projet/gestionvilles-ci4/public"
RewriteEngine On
RewriteRule ^/ci4$ /ci4/ [R=301,L]
# Le (?!index\.php) est OBLIGATOIRE : sinon la règle se ré-applique
# sur sa propre sortie (boucle interne -> chemin cassé)
RewriteRule ^/ci4/(?!index\.php)(.+)$ /ci4/index.php/$1 [L]

# CI3 : meme probleme fcgid+PATH_INFO — le front-controller est
# route au niveau vhost (le .htaccess racine reste pour la prod).
# REQUEST_FILENAME n est pas mappe en contexte vhost -> DOCUMENT_ROOT.
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-d
RewriteRule ^/(?!ci4|index\.php)(.+)$ /index.php/$1 [L]
```

Note : `Co_ville::index()` redirige vers `/villes/requetes/{site_lang}` où
`site_lang` vient de `Accept-Language` — une requête sans cet en-tête
boucle en 307 (comportement normal CI3, pas un bug du vhost).

En prod (où `ci4/` sera un vrai sous-répertoire du DocumentRoot), le
`.htaccess` standard de `ci4/public` suffira — pas besoin de ce vhost.

Notes schéma local : `gestionvilles.gps_sani` avait des partitions
périmées (ajout de `pmax` fait) ; `gps_sani_last` et `gps_polyline`
créées en local pour les tests.
