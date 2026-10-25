<?php
/**
 * PATCH CI3 (prod) : suppression des liens d'assets morts en chemins
 * relatifs qui causent les requetes fantomes (timeouts superviseurs).
 *
 * Contexte : les vues CI3 referencent style.css, ../favicon.ico et
 * dist/css/skins/... en RELATIF. Sous /sanivan/aujourdhui/1/{date},
 * le navigateur demande /sanivan/aujourdhui/1/style.css, ROUTE vers
 * co_gestion/showToday(1,'style.css') : chaque chargement executait
 * le pipeline cedule complet 3-4 fois pour des fichiers INEXISTANTS.
 * Aucun de ces CSS/favicon n'a JAMAIS charge (fichiers absents du
 * webroot) -> suppression = ZERO changement visuel.
 *
 * Usage :
 *   1. Uploader ce fichier a la racine du webroot CI3 (a cote d'index.php)
 *   2. https://votre-domaine/patch_ci3_assets.php?dry=1   -> apercu (rien n'est modifie)
 *   3. https://votre-domaine/patch_ci3_assets.php         -> application + sauvegardes
 *   4. SUPPRIMER ce fichier du serveur apres usage
 *
 * Sauvegardes : chaque fichier modifie est copie tel quel sous
 * application/views/_backup_assets_YYYYMMDD_HHMMSS/ (structure conservee).
 *
 * Compatible PHP 5.x (prod CI3). Aucune dependance.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

/* Debut exact (apres trim) des lignes <link> mortes a supprimer.
   Les refs ABSOLUES valides (/assets/..., /assets/pronto/...) sont
   conservees : elles pointent vers des fichiers existants. */
$deadPatterns = array(
    '<link rel="shortcut icon" href="../favicon.ico"',
    '<link rel="icon" href="../favicon.ico"',
    '<link href="style.css"',
    '<link href="styles/style.css"',
    '<link href="/asset/css/style.css"',
    '<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css"',
    '<link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css"',
    '<link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css"',
);

$dryRun = (isset($_GET['dry']) && $_GET['dry'] == '1')
       || (isset($argv) && is_array($argv) && isset($argv[1]) && $argv[1] === 'dry');
$viewsDir = __DIR__ . '/application/views';

header('Content-Type: text/plain; charset=utf-8');

if (! is_dir($viewsDir)) {
    echo "ERREUR : application/views/ introuvable a partir de " . __DIR__ . "\n";
    echo "Placez ce fichier a la racine du webroot CI3 (a cote d'index.php).\n";
    exit(1);
}

$backupDir = null;
if (! $dryRun) {
    $backupDir = $viewsDir . '/_backup_assets_' . date('Ymd_His');
    if (! mkdir($backupDir, 0755, true)) {
        echo "ERREUR : impossible de creer le dossier de sauvegarde $backupDir\n";
        exit(1);
    }
}

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsDir, FilesystemIterator::SKIP_DOTS)
);

$filesChanged = 0;
$linesRemoved = 0;
$report = array();

foreach ($it as $file) {
    $path = $file->getPathname();
    if (substr($path, -4) !== '.php') {
        continue;
    }
    if (strpos($path, '_backup_assets_') !== false) {
        continue; // ne jamais toucher aux sauvegardes
    }

    $content = file_get_contents($path);
    if ($content === false || strpos($content, '<link') === false) {
        continue;
    }

    $lines = explode("\n", $content);
    $kept = array();
    $removed = 0;
    $removedLines = array();
    foreach ($lines as $line) {
        $t = trim($line);
        $dead = false;
        foreach ($deadPatterns as $p) {
            if (strpos($t, $p) === 0) {
                $dead = true;
                break;
            }
        }
        if ($dead) {
            $removed++;
            $removedLines[] = $t;
        } else {
            $kept[] = $line;
        }
    }

    if ($removed === 0) {
        continue;
    }

    $rel = substr($path, strlen($viewsDir) + 1);
    $filesChanged++;
    $linesRemoved += $removed;

    foreach ($removedLines as $rl) {
        $report[] = sprintf('  %s : %s', $rel, $rl);
    }

    if (! $dryRun) {
        /* sauvegarde de l'original (structure conservee) */
        $dest = $backupDir . '/' . $rel;
        $destDir = dirname($dest);
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        copy($path, $dest);
        /* ecriture du fichier nettoye (byte-exact hors lignes retirees) */
        file_put_contents($path, implode("\n", $kept));
    }
}

echo "============================================================\n";
echo $dryRun ? "APERCU (dry-run) - RIEN n'a ete modifie\n"
            : "PATCH APPLIQUE\n";
echo "============================================================\n";
echo "Fichiers modifies : $filesChanged\n";
echo "Lignes mortes supprimees : $linesRemoved\n\n";
echo "Detail :\n" . implode("\n", $report) . "\n\n";
if (! $dryRun) {
    echo "Sauvegardes : $backupDir\n";
    echo ">>> SUPPRIMEZ patch_ci3_assets.php du serveur maintenant <<<\n";
    echo ">>> Testez ensuite : la page cedule du jour (sanivan/aujourdhui) <<<\n";
} else {
    echo "Pour appliquer : rechargez cette page SANS ?dry=1\n";
}
