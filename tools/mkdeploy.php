<?php
// Construit un ZIP de deploiement WHC.
//
//   php tools/mkdeploy.php          -> patch : fichiers modifies vs HEAD
//                                      sous app/ ou public/ + nouveaux non
//                                      commites sous app/
//   php tools/mkdeploy.php --since <ref>
//                                   -> patch : fichiers modifies entre
//                                      <ref> et HEAD (ex. hash du dernier
//                                      commit deja deploye)
//   php tools/mkdeploy.php --full   -> transfert global : tout le code
//                                      (git ls-files + nouveaux sous app/)
//
// Jamais embarques (quel que soit le mode) : les fichiers listes dans
// EXCLUS — .env n'est pas tracke donc deja exclu ; App.php (baseURL
// locale gestionvillesci4.local) et Database.php (credentials) sont
// propres a chaque serveur, on ne les ecrase pas.
chdir(__DIR__ . '/..');

const EXCLUS = [
    'app/Config/App.php',
    'app/Config/Database.php',
];

// Repertoires livrables en mode patch.
const PATCH_DIRS = '#^(app|public)/#';

$full  = in_array('--full', $argv, true);
$since = null;
$idx   = array_search('--since', $argv, true);
if ($idx !== false && isset($argv[$idx + 1])) {
    $since = $argv[$idx + 1];
}

// Nouveaux fichiers non commites sous app/ (gitignore respecte).
// public/* non commite n'est JAMAIS embarque : ce sont les donnees
// utilisateurs (public/upload*, plusieurs Go).
function nonCommitesSousApp(): array
{
    $out = [];
    foreach (explode("\n", (string) shell_exec('git ls-files --others --exclude-standard -- app/')) as $f) {
        $f = trim($f);
        if ($f !== '' && is_file($f)) {
            $out[] = str_replace('\\', '/', $f);
        }
    }
    return $out;
}

if ($full) {
    $files = array_filter(array_map(
        fn ($f) => str_replace('\\', '/', trim($f)),
        explode("\n", (string) shell_exec('git ls-files'))
    ));
    $files = array_values(array_unique(array_merge($files, nonCommitesSousApp())));
    $out   = 'gestionvilles-ci4_whc_' . date('Ymd-His') . '.zip';
} else {
    $range = $since !== null ? escapeshellarg($since . '..HEAD') : 'HEAD';
    $files = array_filter(
        array_map(
            fn ($f) => str_replace('\\', '/', trim($f)),
            explode("\n", (string) shell_exec('git diff --name-only ' . $range))
        ),
        fn ($f) => $f !== '' && preg_match(PATCH_DIRS, $f) === 1
    );
    $files = array_values(array_unique(array_merge($files, nonCommitesSousApp())));
    $out   = 'patch_whc_' . date('Ymd-His') . '.zip';
}

$files = array_values(array_diff($files, EXCLUS));

if ($files === []) {
    echo "Rien a zipper : aucune modification vs HEAD sous app/ ou public/.\n";
    exit(0);
}

$zip = new ZipArchive();
if ($zip->open($out, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Impossible de creer $out\n");
    exit(1);
}
$nb = 0;
foreach ($files as $f) {
    if (! is_file($f)) {
        continue; // fichier supprime localement
    }
    $zip->addFile($f, $f);
    echo "  $f\n";
    $nb++;
}
$zip->close();
printf("%s : %d fichiers, %.1f Mo\n", $out, $nb, filesize($out) / 1048576);
