# Plan de test superviseur — Cédules + patch assets (CI3 prod)

> Session guidée ~45 min. Objectifs : (1) mesurer le problème AVANT,
> (2) appliquer le patch, (3) valider le flux de cédule APRÈS.
> Remplir les cases [ ] et noter les temps en secondes.

---

## 0. Préparation (10 min avant la session)

- [ ] F12 ouvert dans un onglet → onglet **Network** → cocher **Preserve log** et **Disable cache**
- [ ] Être connecté en superviseur sur https://robert-daoust.ca
- [ ] Garder ce plan ouvert (imprimé ou 2e écran)
- [ ] Vérifier que `patch_ci3_assets.php` est téléversé à la racine (PAS encore exécuté)

---

## 1. Mesures AVANT le patch (~10 min)

Le superviseur fait ses manipulations HABITUELLES. On mesure seulement.

### 1.1 Chargement de la cédule du jour
- [ ] Ouvrir `https://robert-daoust.ca/sanivan/aujourdhui/1/2026-10-26`
- [ ] Chronomètre (ou F12 → ligne du haut) : **temps total ≈ ____ s**
- [ ] Coller ce snippet dans la **Console** F12 (après chargement) :

```js
(() => {
  const nav = performance.getEntriesByType('navigation')[0];
  const res = performance.getEntriesByType('resource');
  const phantoms = res.filter(r => /style\.css|favicon|_all-skins/.test(r.name));
  console.log(`Page chargée en : ${Math.round(nav.loadEventEnd)} ms`);
  console.log(`Requêtes réseau : ${res.length}`);
  console.log(`%cRequêtes FANTÔMES : ${phantoms.length}`, 'color:red;font-weight:bold');
  phantoms.forEach(p => console.log('  ', Math.round(p.duration)+'ms', p.name));
})();
```

- [ ] Noter : **____ requêtes fantômes** (attendu AVANT : 3-4, en rouge/404 :
      `style.css`, `../favicon.ico`, `_all-skins.min.css`)
- [ ] Dans l'onglet Network : filtrer par `style.css` → capture d'écran des lignes rouges

### 1.2 Workflow normal du superviseur (temps de chaque action)
| Action | Temps (s) | Bloque/timeouts ? |
|---|---|---|
| Changer le dropdown **type camion** | ____ | ☐ non ☐ oui |
| Dropdown **chauffeur** (la liste se remplit) | ____ | ☐ non ☐ oui |
| Dropdown **aide** | ____ | ☐ non ☐ oui |
| **Glisser/déposer** un camion sur une ville | ____ | ☐ non ☐ oui |
| **Sauvegarder** un changement de cédule | ____ | ☐ non ☐ oui |
| **Valider** la journée (`sanivan/validation`) | ____ | ☐ non ☐ oui |
| Changer de **date** (calendrier) | ____ | ☐ non ☐ oui |

- [ ] Combien de timeouts en 5 minutes d'usage normal ? **____**

---

## 2. Appliquer le patch (5 min)

1. [ ] `https://robert-daoust.ca/patch_ci3_assets.php?dry=1`
       → vérifier : **63 fichiers / 226 lignes** (sinon STOP, m'envoyer le rapport)
2. [ ] `https://robert-daoust.ca/patch_ci3_assets.php` → appliqué + sauvegardes
3. [ ] **Supprimer** `patch_ci3_assets.php` du serveur (File Manager)
4. [ ] Sauvegardes créées dans `application/views/_backup_assets_<horodatage>/`
       (noter le nom exact : ______________________)

---

## 3. Mesures APRÈS le patch (~10 min)

### 3.1 Vérification fantômes
- [ ] Recharger `/sanivan/aujourdhui/1/2026-10-26` (Ctrl+F5)
- [ ] Recoller le snippet console :
      → **Requêtes FANTÔMES : ____** (attendu APRÈS : **0**)
      → **Page chargée en : ____ ms** (à comparer avec §1.1)

### 3.2 Refaire le tableau du §1.2 (mêmes actions, temps APRÈS)
| Action | Temps (s) | Commentaire vs avant |
|---|---|---|
| Type camion | ____ | plus rapide / pareil / plus lent |
| Chauffeur | ____ | |
| Aide | ____ | |
| Glisser/déposer | ____ | |
| Sauvegarder | ____ | |
| Valider la journée | ____ | |
| Changer de date | ____ | |

---

## 4. Validation fonctionnelle du flux complet (~15 min)

Le superviseur construit la cédule de demain comme en vraie journée :

- [ ] Ouvrir `/sanivan/aujourdhui/1/2026-10-26`
- [ ] Affecter un camion à chaque ville prévue
- [ ] Affecter chauffeur + aide sur au moins 2 lignes
- [ ] Modifier un **secteur** et vérifier la carte polylines (boutons secteurs)
- [ ] Sauvegarder puis **recharger la page** → les affectations **persistent** ✓
- [ ] `sanivan/validation` → la validation du jour fonctionne
- [ ] Export **Excel cédules** (icône Excel) → le fichier se télécharge
- [ ] Export **heures aides** → se télécharge
- [ ] Rapport **Sanivan du jour** (`rapport/sani/...` si utilisé) → s'affiche
- [ ] Visuel : **rien n'a changé à l'écran** (le patch ne supprime que des
      liens vers des fichiers qui n'existaient pas)
- [ ] Aucune erreur dans la Console F12 en rouge (hors habituelles)

---

## 5. En cas de problème

| Symptôme | Action |
|---|---|
| Page blanche / erreur PHP | File Manager → `application/views/_backup_assets_*/` → recopier le(s) fichier(s) en cause à leur place (rollback) |
| Chiffres dry-run ≠ 63/226 | Ne PAS appliquer → m'envoyer le rapport |
| Un élément visuel disparaît | Screenshot + rollback de ce seul fichier (voir sauvegardes) |

Après la session : m'envoyer les tableaux §1 et §3 + le nombre de
requêtes fantômes avant/après — je documente et j'ajuste si besoin.

---

## Annexe — ce que fait le patch (rappel)

Les vues référençaient `style.css`, `../favicon.ico` et
`dist/css/skins/_all-skins.min.css` en chemins RELATIFS : sous
`/sanivan/aujourdhui/1/{date}`, le navigateur demandait
`/sanivan/aujourdhui/1/style.css` → **routé vers `showToday(1,'style.css')`**
→ tout le pipeline de cédule (~118 requêtes BD) s'exécutait pour un
fichier **inexistant** — 3-4 fois par chargement. Les fichiers n'ont
jamais existé au webroot : aucun ne s'est jamais chargé → la
suppression n'a **aucun effet visuel**.
