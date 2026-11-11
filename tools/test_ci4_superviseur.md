# Script de tests CI4 — dev.robert-daoust.ca

> Session guidée ~60-90 min pour le superviseur.
> Objectif : valider que l'application migrée (CI4) reproduit le
> comportement de l'application legacy sur toutes les sections.
> Cocher les cases, noter les anomalies avec : URL exacte + ce qui
> s'affiche + ce qui devrait s'afficher + capture d'écran si possible.
>
> **Repère visuel** : dev.robert-daoust.ca = application migrée.
> Si un écran semble « différent », vérifier le même écran sur
> robert-daoust.ca (legacy) — une différence volontaire de version
> Bootstrap peut exister (menus), le reste doit être identique.

---

## 0. Préparation (5 min)

- [ ] Ouvrir https://dev.robert-daoust.ca dans Chrome/Edge
- [ ] F12 → onglet **Console** ouvert (les erreurs en rouge sont à noter)
- [ ] F12 → onglet **Réseau/Network** → cocher **Preserve log**
- [ ] Avoir sous la main un compte superviseur/admin valide
- [ ] Noter la version du navigateur : ________________

**Bugs à rapporter — gabarit** : pour chaque anomalie, noter
`URL | Action faite | Résultat obtenu | Résultat attendu | Erreur console?`

---

## 1. Connexion / session (~5 min)

- [ ] `dev.robert-daoust.ca/admin/connexion` → formulaire s'affiche
- [ ] Mauvais mot de passe → message d'erreur, pas de plantage
- [ ] Bon mot de passe → redirigé vers `/admin` (tableau de bord)
- [ ] F5 sur `/admin` → la session **persiste** (pas renvoyé au login)
- [ ] Ouvrir un nouvel onglet sur `/admin` → toujours connecté
- [ ] `admin/quitter` → déconnecté → `/admin` renvoie au login
- [ ] **Reconnecter** avant la suite

## 2. Tableau de bord `/admin` (~3 min)

- [ ] Les dropdowns **Camions / Chauffeurs / Aides** sont remplis
- [ ] Liste **HORS SERVICES** s'affiche (ou « aucun »)
- [ ] Liste **INDISPONIBLE** s'affiche
- [ ] Le menu de navigation (gauche/haut) ressemble au legacy
- [ ] Pas de contenu manquant évident vs robert-daoust.ca/admin

## 3. Cédule du jour (~10 min)

- [ ] `/sanivan/aujourdhui/1/<date>` → la cédule s'affiche
      (camions, chauffeurs, aides, villes)
- [ ] Changer un **chauffeur** via dropdown → sauvegarde OK
- [ ] Changer un **camion** via dropdown → sauvegarde OK
- [ ] Changer les **aides** (multi) → sauvegarde OK
- [ ] **Drag&drop** : déplacer une ligne de cédule → persiste après F5
- [ ] Bouton heures aides (`posthresaides`) → le formulaire s'enregistre
- [ ] `sanivan/printhresaides/<date>` → page d'impression des heures
- [ ] Changer de **date** → la cédule du nouveau jour s'affiche
- [ ] Console F12 : pas de nouvelle erreur rouge

## 4. Calendrier des villes (~8 min)

- [ ] `/sanivan/calendrier` → page des calendriers 12 mois
- [ ] `sanivan/calendriervilles/0` → vue par ville
- [ ] `sanivan/getinfoville` (clic sur une ville/date) → infos du mois
- [ ] **Déployer une cédule** (`postcaldeploie`) : fréquence hebdo
      sur une ville de test → les dates se créent au calendrier
- [ ] **Supprimer** (`delcaldeploie`) → les dates disparaissent
- [ ] `sanivan/getweek/<idville>/<date>` → semaine affichée
- [ ] `sanivan/copie` + `excopie` → copie de cédule si utilisé au quotidien
- [ ] `sanivan/getdate/<...>` → nouvelle date calculée correctement

## 5. Validation (~8 min)

- [ ] `/sanivan/validation` → rapport de validation complet
      (même contenu que legacy : camions, chauffeurs, heures)
- [ ] `sanivan/jour_validation/<date>` → rapport du jour
- [ ] `sanivan/jour_validation_chauffeur/<date>` → rapport par chauffeur
- [ ] Modifier des heures puis `postsuivis` → rapport se met à jour
- [ ] `deletesuivis` sur une entrée de test → supprimée

## 6. Ressources humaines (~8 min)

- [ ] `/sanivan/showrh/1` → liste RH affichée
- [ ] `sanivan/showmodalrh/<id>` → fiche employé s'ouvre
- [ ] `sanivan/rhinfo/<id>` → infos RH
- [ ] `sanivan/showsuivisrhlist/<mois>` (avec filtres si besoin)
      → liste des suivis RH
- [ ] `sanivan/showsuiviscamlist/<mois>` → liste des suivis camions
- [ ] `sanivan/calrh` → calendrier RH s'affiche
- [ ] `sanivan/calcam` → calendrier camions s'affiche
- [ ] `sanivan/calrhpdf` + `sanivan/calpdf` → les PDF se génèrent/téléchargent

## 7. Camions & villes (~8 min)

- [ ] `/sanivan/editcam/<id>` → écran d'édition camion
- [ ] `sanivan/sendcamioninfo/...` (sauvegarde d'un champ) → persisté
- [ ] `sanivan/camioninfo` → infos camion
- [ ] `/sanivan/showville/1` → page ville complète
- [ ] `sanivan/postvilleinfo/<idville>/...` (éditer un champ) → persisté
- [ ] `sanivan/showsecteur/<id>` → secteur affiché
- [ ] `sanivan/postsecteurinfo` (édition) → persisté
- [ ] `sanivan/getsecteursbyville` → secteurs de la ville listés

## 8. Dictionnaires (~5 min)

- [ ] `/sanivan/dictionnaires/0/0` → page des dictionnaires
- [ ] Chaque onglet (types camion, statuts, contenant, etc.) s'affiche
- [ ] `sanivan/postdictionnaire/<id>` → ajout/modif persistée
- [ ] `sanivan/postdictStatutRequete/<id>` → persisté
- [ ] `sanivan/postcontenant/<id>` → persisté
- [ ] `sanivan/typecam/<id>`, `camfromtype/<id>`, `rhfromtype/<id>` → listes

## 9. Polylines / secteurs (~5 min)

- [ ] `/sanivan/listpolyline` → liste des polylines + carte
- [ ] `sanivan/getpolylinesgeojson/<id>` → GeoJSON (réponse technique OK)
- [ ] `sanivan/getpolylinesbysecteurville` → polylines par ville/secteur
- [ ] `sanivan/getpolylinecheckboxes` → cases à cocher affichées
- [ ] Cocher/décocher + `setpolylinecedule` / `togglepolylinecedule`
      → association persistée
- [ ] `sanivan/executeTolerance/<id>` → réduction OK (si utilisé)
- [ ] `sanivan/removepolyline/<id>` sur une polyline de test → supprimée
- [ ] `sanivan/movepolylinefirst` → réordonnancement

## 10. Requêtes (~8 min)

- [ ] `villes/liste/requetes` → liste des requêtes
- [ ] `villes/show/requete/<id>` → détail d'une requête
- [ ] `villes/show/reponse/<id>` → détail d'une réponse
- [ ] `requete/send/<id>` → envoi d'une requête
- [ ] `villes/post/requete` (création) → nouvelle requête persistée
- [ ] `villes/send-reponse` (réponse) → réponse persistée + redirect
- [ ] `villes/post-dtecedule/<idreq>/<idrep>/<idville>/<date>` → date cédulée
- [ ] `villes/upd-adresse/<id>/<adresse>` → adresse mise à jour
- [ ] `villes/reponse/vue/<id>` + `villes/requete/vue/<id>` → marqués vus
- [ ] `villes/requetepdf` → PDF des requêtes généré
- [ ] `villes/reponsepdf/<id>` → PDF d'une réponse
- [ ] `villes/requeteexcel` → ⚠️ **peut échouer** (PHPExcel non
      compatible PHP 8 — bug connu aussi en legacy, à ignorer)

## 11. Usagers (~5 min)

- [ ] `admin/liste_usagers` → liste des usagers
- [ ] `admin/show?id_usager=<id>` → fiche usager
- [ ] `admin/usager/ajout` → création d'un usager de test
- [ ] `admin/update` → modification persistée
- [ ] `admin/supprimer/<id>` ou `admin/remove` → suppression du test
- [ ] `admin/inscription` + `inscription/getinfo` → formulaire/email
- [ ] `admin/optionssys` → options système affichées
- [ ] `login/change` + `login/postchange` → changement de mot de passe
      (sur compte de test)

## 12. Session / divers (~3 min)

- [ ] `session/relog` → pas de plantage
- [ ] `envoi`, `envoiville` → statut attendu (identique à legacy)
- [ ] `ville/showemail/<id>` → liste courriels ville
- [ ] Recharger n'importe quelle page → la session tient (7200 s)

## 13. Régression visuelle rapide (~5 min)

Comparer côte à côte (2 onglets) sur **les mêmes données** :

| Écran | Legacy (robert-daoust.ca) | dev.robert-daoust.ca | Identique ? |
|---|---|---|---|
| Tableau de bord | | | ☐ |
| Cédule du jour | | | ☐ |
| Calendrier | | | ☐ |
| Validation | | | ☐ |
| Liste RH | | | ☐ |
| Liste requêtes | | | ☐ |
| Édition ville | | | ☐ |

*(Différences tolérées : styles de menus Bootstrap 5 ; tout le reste
doit être identique — listes, boutons, contenu, ordre des lignes.)*

---

## Problèmes connus — NE PAS rapporter

- `villes/requeteexcel`, `excel/cedule`, `excel/hres` : erreur 500 —
  PHPExcel incompatible PHP 8 (même bug en legacy, remplacement prévu)
- `villes/reponsepdf/<id>` sans données de requête en BD : erreur attendue
- `sanivan/calrh`/`calcam` peuvent afficher une erreur 500 par
  intermittence — **même bug intermittent en legacy** (données RH)
- `admin/page_de_site/<x>`, `envoiville` : 404 identique au legacy

## Rapport final

- Nombre d'anomalies : ________
- Sections bloquantes (rien ne s'affiche) : ________
- Envoyer ce document rempli + captures à l'équipe.
