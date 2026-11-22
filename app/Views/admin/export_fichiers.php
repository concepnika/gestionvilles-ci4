<?php
// $sources (cle => libelle), $zips, $apercu?, $sel?, $debut?, $fin?,
// $message?, $error?
$sel   = $sel   ?? array_keys($sources ?? []);
$debut = $debut ?? '';
$fin   = $fin   ?? '';
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
    <h2 class="mb-4">Export de fichiers (ZIP)</h2>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?php echo gv_url('admin/export-fichiers'); ?>">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Repertoires sources</label>
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
                                class="btn btn-outline-primary">Apercu</button>
                        <button type="submit" name="action" value="zip"
                                class="btn btn-primary"
                                onclick="this.textContent='Generation en cours (patientez)...';">
                            Creer le ZIP</button>
                        <span class="text-muted ms-2">
                            La creation peut prendre plusieurs minutes pour de gros volumes.
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (! empty($apercu)) : ?>
        <h4>Apercu — <?php echo htmlspecialchars($debut, ENT_QUOTES); ?>
            au <?php echo htmlspecialchars($fin, ENT_QUOTES); ?></h4>
        <div class="table-responsive bg-white shadow-sm rounded mb-4">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Repertoire</th>
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
            // Regroupe les fichiers par repertoire d'origine.
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
        <h4>Archives disponibles <small class="text-muted fs-6">(conservees 48 h)</small></h4>
        <div class="alert alert-warning py-2 mb-3">
            La suppression d'une archive efface <strong>l'archive ET les fichiers
            originaux</strong> qu'elle contient. Assurez-vous que le transfert a bien
            reussi et que les donnees sont correctes avant de supprimer.
        </div>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Archive</th>
                    <th>Creee le</th>
                    <th class="text-end">Fichiers</th>
                    <th class="text-end">Taille</th>
                    <th class="text-center">Telechargee</th>
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
                                Telecharger
                            </a>
                            <form method="post" class="d-inline"
                                  action="<?php echo gv_url('admin/export-fichiers/supprimer'); ?>"
                                  onsubmit="return confirm('ATTENTION : cette action supprime l\'archive ZIP ET les <?php echo (int) ($z['nbFichiers'] ?? 0); ?> fichier(s) originaux qu\'elle contient.\n\nAssurez-vous que le transfert a bien reussi et que les donnees sont correctes.\n\nConfirmer la suppression definitive ?');">
                                <input type="hidden" name="fichier"
                                       value="<?php echo htmlspecialchars($z['nom'], ENT_QUOTES); ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        <?php echo empty($z['telecharge']) ? 'disabled' : ''; ?>
                                        title="<?php echo empty($z['telecharge']) ? 'Telechargez d\'abord l\'archive' : ''; ?>">
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
</div>
</body>
</html>
