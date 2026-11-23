<?php
// $sources (cle => libelle), $zips, $apercu?, $sel?, $debut?, $fin?,
// $message?, $error?
// GPS : $gpsTables, $gpsZips, $gpsApercu?, $gpsSel?, $gpsDebut?, $gpsFin?,
//       $gpsMsg?, $gpsErr?
$sel     = $sel     ?? array_keys($sources ?? []);
$debut   = $debut   ?? '';
$fin     = $fin     ?? '';
$gpsSel  = $gpsSel  ?? array_keys($gpsTables ?? []);
$gpsDebut = $gpsDebut ?? '';
$gpsFin   = $gpsFin   ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Administration - Export de fichiers</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Export de fichiers & données GPS</h2>

    <!-- Onglets -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-fichiers"
                    data-bs-toggle="tab" data-bs-target="#panel-fichiers"
                    type="button" role="tab">
                Export de fichiers (ZIP)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-gps"
                    data-bs-toggle="tab" data-bs-target="#panel-gps"
                    type="button" role="tab">
                Exportation de données GPS
            </button>
        </li>
    </ul>

    <div class="tab-content">

    <!-- ==================== Onglet 1 : Fichiers ==================== -->
    <div class="tab-pane fade show active" id="panel-fichiers" role="tabpanel">

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <div class="alert alert-primary py-2 mb-4">
        <strong>Export de fichiers (ZIP)</strong> — Sélectionnez les répertoires
        sources et une plage de dates, puis créez une archive ZIP des fichiers
        téléversés (tickets, photos Sanivan, réponses). Après téléchargement,
        supprimez l'archive pour <strong>effacer les fichiers originaux du
        serveur</strong> et libérer de l'espace disque.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?php echo gv_url('admin/export-fichiers'); ?>">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Répertoires sources</label>
                        <?php foreach (($sources ?? []) as $cle => $libelle) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="sources[]" id="src_<?php echo md5($cle); ?>"
                                       value="<?php echo htmlspecialchars($cle, ENT_QUOTES); ?>"
                                       <?php echo in_array($cle, $sel, true) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="src_<?php echo md5($cle); ?>">
                                    <?php echo htmlspecialchars($libelle, ENT_QUOTES); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-3">
                        <label for="debut" class="form-label fw-bold">Du</label>
                        <input type="date" class="form-control" id="debut" name="debut"
                               value="<?php echo htmlspecialchars($debut, ENT_QUOTES); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fin" class="form-label fw-bold">Au (inclus)</label>
                        <input type="date" class="form-control" id="fin" name="fin"
                               value="<?php echo htmlspecialchars($fin, ENT_QUOTES); ?>" required>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" name="action" value="apercu"
                                class="btn btn-outline-primary">Aperçu</button>
                        <button type="submit" name="action" value="zip"
                                class="btn btn-primary"
                                onclick="this.textContent='Génération en cours (patientez)...';">
                            Créer le ZIP</button>
                        <span class="text-muted ms-2">
                            La création peut prendre plusieurs minutes pour de gros volumes.
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (! empty($apercu)) : ?>
        <h4>Aperçu — <?php echo htmlspecialchars($debut, ENT_QUOTES); ?>
            au <?php echo htmlspecialchars($fin, ENT_QUOTES); ?></h4>
        <div class="table-responsive bg-white shadow-sm rounded mb-4">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Répertoire</th>
                    <th class="text-end">Fichiers</th>
                    <th class="text-end">Taille</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($apercu['parSource'] as $src => $info) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($src, ENT_QUOTES); ?></td>
                        <td class="text-end"><?php echo (int) $info['nb']; ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($info['tailleFmt'], ENT_QUOTES); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-secondary fw-bold">
                    <td>Total</td>
                    <td class="text-end"><?php echo (int) $apercu['total']['nb']; ?></td>
                    <td class="text-end"><?php echo htmlspecialchars($apercu['total']['tailleFmt'], ENT_QUOTES); ?></td>
                </tr>
                </tbody>
            </table>
        </div>

        <h5>Fichiers inclus</h5>
        <?php foreach ($apercu['parSource'] as $src => $info) : ?>
            <?php if ($info['nb'] === 0) { continue; } ?>
            <?php
            $groupes = [];
            foreach ($info['fichiers'] as $f) {
                $groupes[dirname($f['rel'])][] = $f;
            }
            ksort($groupes);
            ?>
            <h6 class="mt-3"><?php echo htmlspecialchars($src, ENT_QUOTES); ?>
                <small class="text-muted">(<?php echo (int) $info['nb']; ?> fichiers)</small></h6>
            <?php foreach ($groupes as $dir => $fichiers) : ?>
                <details class="mb-1">
                    <summary>
                        <code><?php echo htmlspecialchars($dir, ENT_QUOTES); ?></code>
                        <span class="badge bg-secondary"><?php echo count($fichiers); ?></span>
                    </summary>
                    <table class="table table-sm table-striped mb-3 ms-4" style="max-width:900px">
                        <tbody>
                        <?php foreach ($fichiers as $f) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(basename($f['rel']), ENT_QUOTES); ?></td>
                                <td class="text-end" style="width:110px"><?php echo htmlspecialchars($f['date'], ENT_QUOTES); ?></td>
                                <td class="text-end" style="width:110px"><?php echo htmlspecialchars($f['tailleFmt'], ENT_QUOTES); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </details>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (! empty($zips)) : ?>
        <h4>Archives disponibles <small class="text-muted fs-6">(conservées 48 h)</small></h4>
        <div class="alert alert-warning py-2 mb-3">
            La suppression d'une archive efface <strong>l'archive ET les fichiers
            originaux</strong> qu'elle contient. Assurez-vous que le transfert a bien
            réussi et que les données sont correctes avant de supprimer.
        </div>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Archive</th>
                    <th>Créée le</th>
                    <th class="text-end">Fichiers</th>
                    <th class="text-end">Taille</th>
                    <th class="text-center">Téléchargée</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($zips as $z) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($z['nom'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($z['cree'], ENT_QUOTES); ?></td>
                        <td class="text-end"><?php echo $z['nbFichiers'] === null ? '&mdash;' : (int) $z['nbFichiers']; ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($z['tailleFmt'], ENT_QUOTES); ?></td>
                        <td class="text-center">
                            <?php if (! empty($z['telecharge'])) : ?>
                                <span class="badge bg-success">Oui</span>
                            <?php else : ?>
                                <span class="badge bg-secondary">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-primary"
                               href="<?php echo gv_url('admin/export-fichiers/telecharger/' . $z['nom']); ?>">
                                Télécharger
                            </a>
                            <form method="post" class="d-inline"
                                  action="<?php echo gv_url('admin/export-fichiers/supprimer'); ?>"
                                  onsubmit="return confirm('ATTENTION : cette action supprime l\'archive ZIP ET les <?php echo (int) ($z['nbFichiers'] ?? 0); ?> fichier(s) originaux qu\'elle contient.\n\nAssurez-vous que le transfert a bien réussi et que les données sont correctes.\n\nConfirmer la suppression définitive ?');">
                                <input type="hidden" name="fichier"
                                       value="<?php echo htmlspecialchars($z['nom'], ENT_QUOTES); ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        <?php echo empty($z['telecharge']) ? 'disabled' : ''; ?>
                                        title="<?php echo empty($z['telecharge']) ? 'Téléchargez d\'abord l\'archive' : ''; ?>">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    </div><!-- /panel-fichiers -->

    <!-- ==================== Onglet 2 : Données GPS ==================== -->
    <div class="tab-pane fade" id="panel-gps" role="tabpanel">

    <?php if (! empty($gpsMsg)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($gpsMsg, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <?php if (! empty($gpsErr)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($gpsErr, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <div class="alert alert-info py-2 mb-4">
        <strong>Exportation de données GPS</strong> — Les tables GPS accumulent les
        points de navigation des camions (plus de 2 millions d'enregistrements).
        Exportez les données d'une plage de dates dans un fichier ZIP (un CSV par table),
        puis après téléchargement, supprimez l'archive pour <strong>effacer les
        enregistrements correspondants du serveur</strong> et alléger la base de données.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?php echo gv_url('admin/export-fichiers/gps'); ?>">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Tables GPS à exporter</label>
                        <?php foreach (($gpsTables ?? []) as $cle => $libelle) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="gps_tables[]" id="gps_<?php echo md5($cle); ?>"
                                       value="<?php echo htmlspecialchars($cle, ENT_QUOTES); ?>"
                                       <?php echo in_array($cle, $gpsSel, true) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="gps_<?php echo md5($cle); ?>">
                                    <?php echo htmlspecialchars($libelle, ENT_QUOTES); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-3">
                        <label for="gps_debut" class="form-label fw-bold">Du</label>
                        <input type="date" class="form-control" id="gps_debut" name="debut"
                               value="<?php echo htmlspecialchars($gpsDebut, ENT_QUOTES); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="gps_fin" class="form-label fw-bold">Au (inclus)</label>
                        <input type="date" class="form-control" id="gps_fin" name="fin"
                               value="<?php echo htmlspecialchars($gpsFin, ENT_QUOTES); ?>" required>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" name="action" value="apercu"
                                class="btn btn-outline-primary">Aperçu</button>
                        <button type="submit" name="action" value="zip"
                                class="btn btn-primary"
                                onclick="this.textContent='Génération en cours (patientez)...';">
                            Créer le ZIP</button>
                        <span class="text-muted ms-2">
                            L'export de gros volumes peut prendre plusieurs minutes.
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (! empty($gpsApercu)) : ?>
        <h4>Aperçu GPS — <?php echo htmlspecialchars($gpsDebut, ENT_QUOTES); ?>
            au <?php echo htmlspecialchars($gpsFin, ENT_QUOTES); ?></h4>
        <div class="table-responsive bg-white shadow-sm rounded mb-4">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Table</th>
                    <th class="text-end">Enregistrements</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($gpsApercu['parTable'] as $tbl => $nb) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tbl, ENT_QUOTES); ?></td>
                        <td class="text-end"><?php echo (int) $nb; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-secondary fw-bold">
                    <td>Total</td>
                    <td class="text-end"><?php echo (int) $gpsApercu['total']; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (! empty($gpsZips)) : ?>
        <h4>Archives GPS disponibles <small class="text-muted fs-6">(conservées 48 h)</small></h4>
        <div class="alert alert-warning py-2 mb-3">
            La suppression d'une archive GPS efface <strong>l'archive ET les
            enregistrements GPS</strong> de la plage de dates correspondante dans
            la base de données. Assurez-vous que le transfert a bien réussi avant
            de supprimer.
        </div>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Archive</th>
                    <th>Créée le</th>
                    <th class="text-end">Enregistrements</th>
                    <th class="text-end">Taille</th>
                    <th class="text-center">Téléchargée</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($gpsZips as $z) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($z['nom'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($z['cree'], ENT_QUOTES); ?></td>
                        <td class="text-end"><?php echo $z['nbRows'] === null ? '&mdash;' : (int) $z['nbRows']; ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($z['tailleFmt'], ENT_QUOTES); ?></td>
                        <td class="text-center">
                            <?php if (! empty($z['telecharge'])) : ?>
                                <span class="badge bg-success">Oui</span>
                            <?php else : ?>
                                <span class="badge bg-secondary">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-primary"
                               href="<?php echo gv_url('admin/export-fichiers/gps/telecharger/' . $z['nom']); ?>">
                                Télécharger
                            </a>
                            <form method="post" class="d-inline"
                                  action="<?php echo gv_url('admin/export-fichiers/gps/supprimer'); ?>"
                                  onsubmit="return confirm('ATTENTION : cette action supprime l\'archive ZIP ET les <?php echo (int) ($z['nbRows'] ?? 0); ?> enregistrement(s) GPS correspondants dans la base de données.\n\nAssurez-vous que le transfert a bien réussi et que les données sont correctes.\n\nConfirmer la suppression définitive ?');">
                                <input type="hidden" name="fichier"
                                       value="<?php echo htmlspecialchars($z['nom'], ENT_QUOTES); ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        <?php echo empty($z['telecharge']) ? 'disabled' : ''; ?>
                                        title="<?php echo empty($z['telecharge']) ? 'Téléchargez d\'abord l\'archive' : ''; ?>">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    </div><!-- /panel-gps -->

    </div><!-- /tab-content -->
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Ouvre l'onglet GPS si l'URL contient #gps ou s'il y a un message/erreur GPS.
(function () {
    var hash = window.location.hash;
    if (hash === '#gps' || document.querySelector('#panel-gps .alert')) {
        var tab = document.getElementById('tab-gps');
        if (tab) { var bs = new bootstrap.Tab(tab); bs.show(); }
    }
})();
</script>
</body>
</html>
