<?php
// BS3 -> BS5 : transformation mecanique des vues (GestionVilles)
// Usage: php tools/bs5_transform.php [--dry] [fichier|dossier]
// Renomme les attributs data-* et les classes BS3. Les classes de style
// sans equivalent comportemental (panel, well, close, form-group...) sont
// conservees : couvertes par vendor5/bs5-compat.css.

$rules = [
    // ---- attributs JS (API Bootstrap) ----
    '/data-toggle="(modal|tooltip|popover|collapse|dropdown|tab|pill|button|buttons|carousel|offcanvas)"/' => 'data-bs-toggle="$1"',
    "/data-toggle='(modal|tooltip|popover|collapse|dropdown|tab|pill|button|buttons|carousel|offcanvas)'/" => "data-bs-toggle='$1'",
    '/data-dismiss="(modal|alert|offcanvas)"/' => 'data-bs-dismiss="$1"',
    "/data-dismiss='(modal|alert|offcanvas)'/" => "data-bs-dismiss='$1'",
    '/data-target=/' => 'data-bs-target=',
    '/data-placement=/' => 'data-bs-placement=',
    '/data-trigger=/' => 'data-bs-trigger=',
    '/data-content=/' => 'data-bs-content=',
    '/data-backdrop=/' => 'data-bs-backdrop=',
    '/data-keyboard=/' => 'data-bs-keyboard=',
    '/data-slide-to=/' => 'data-bs-slide-to=',
    '/data-ride=/' => 'data-bs-ride=',
    '/data-parent=/' => 'data-bs-parent=',

    // ---- grille ----
    '/\bcol-xs-(\d)/' => 'col-$1',
    '/\bcol-xs-offset-(\d)/' => 'offset-$1',
    '/\bcol-(sm|md|lg)-offset-(\d)/' => 'offset-$1-$2',

    // ---- utilitaires renommes ----
    '/\bpull-left\b/' => 'float-start',
    '/\bpull-right\b/' => 'float-end',
    '/\bnav-stacked\b/' => 'flex-column',
    '/\bnav-justified\b/' => 'nav-fill',
    '/\bsr-only\b/' => 'visually-hidden',
    '/\bimg-responsive\b/' => 'img-fluid',
    '/\bimg-rounded\b/' => 'rounded',
    '/\bimg-circle\b/' => 'rounded-circle',
    '/\btable-condensed\b/' => 'table-sm',
    '/\bform-control-static\b/' => 'form-control-plaintext',
    '/\balert-error\b/' => 'alert-danger',
    '/\bdropdown-menu-right\b/' => 'dropdown-menu-end',
    '/\bdropdown-menu-left\b/' => 'dropdown-menu-start',

    // ---- composants ----
    '/\bbtn-default\b/' => 'btn-outline-secondary',
    '/\bbtn-group-xs\b/' => 'btn-group-sm',
    '/\bbtn-xs\b/' => 'btn-sm',
    '/\bbtn-block\b/' => 'd-block w-100',
    '/\binput-group-addon\b/' => 'input-group-text',
    '/\binput-sm\b/' => 'form-control-sm',
    '/\binput-lg\b/' => 'form-control-lg',
    '/\blabel label-default\b/' => 'badge bg-secondary',
    '/\blabel label-(primary|success|info|warning|danger)\b/' => 'badge bg-$1',
    '/\blabel-default\b/' => 'bg-secondary',
    '/\blabel-(primary|success|info|warning|danger)\b/' => 'bg-$1',
    '/\bbadge-important\b/' => 'bg-danger',
    '/\bbadge-(success|warning|info)\b/' => 'bg-$1',
    '/\bbadge-inverse\b/' => 'bg-dark',

    // ---- navbar ----
    '/\bnavbar-toggle\b/' => 'navbar-toggler',
    '/\bnavbar-inverse\b/' => 'navbar-dark bg-dark',
    '/\bnavbar-fixed-top\b/' => 'fixed-top',
    '/\bnavbar-fixed-bottom\b/' => 'fixed-bottom',
    '/\bnavbar-right\b/' => 'ms-auto',
    '/\bnavbar-left\b/' => 'me-auto',

    // ---- visibilite responsive ----
    '/\bhidden-xs\b/' => 'd-none d-sm-block',
    '/\bhidden-sm\b/' => 'd-block d-sm-none d-md-block',
    '/\bhidden-md\b/' => 'd-block d-md-none d-lg-block',
    '/\bhidden-lg\b/' => 'd-block d-lg-none d-xl-block',
    '/\bvisible-xs-block\b/' => 'd-block d-sm-none',
    '/\bvisible-xs-inline-block\b/' => 'd-inline-block d-sm-none',
    '/\bvisible-xs-inline\b/' => 'd-inline d-sm-none',
    '/\bvisible-xs\b/' => 'd-block d-sm-none',

    // ---- etats animes (in -> show) ----
    '/\bcollapse in\b/' => 'collapse show',
    '/\bfade in\b/' => 'fade show',
];

$args = array_slice($argv, 1);
$dry = in_array('--dry', $args, true);
$args = array_values(array_filter($args, fn($a) => $a !== '--dry'));
$target = $args[0] ?? 'app/Views';

$files = [];
if (is_dir($target)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target));
    foreach ($it as $f) {
        if ($f->isFile() && $f->getExtension() === 'php') { $files[] = (string) $f; }
    }
} else {
    $files[] = $target;
}

$totalFiles = 0; $totalSubs = 0;
foreach ($files as $file) {
    $src = file_get_contents($file);
    $out = $src; $fileSubs = 0;
    foreach ($rules as $re => $rep) {
        $out = preg_replace($re, $rep, $out, -1, $n);
        $fileSubs += $n;
    }
    if ($out !== $src) {
        $totalFiles++; $totalSubs += $fileSubs;
        if ($dry) {
            echo "[$fileSubs] $file\n";
        } else {
            file_put_contents($file, $out);
            echo "[$fileSubs] $file\n";
        }
    }
}
echo ($dry ? 'DRY — ' : '') . "fichiers modifies: $totalFiles | substitutions: $totalSubs\n";
