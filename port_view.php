<?php
/**
 * Convertisseur mecanique de vues Legacy -> CI4.
 *
 * Usage : php port_view.php <chemin/vue> [<chemin/vue> ...]
 * Source : E:/Serveur Cloud/GestionVilles/application/views/<arg>.php
 * Cible  : app/Views/<arg>.php
 *
 * Transformations :
 *   defined('BASEPATH') ...            -> retire
 *   $this->load->view('x', $d, TRUE)   -> view('x', $d)
 *   $this->load->view('x'[, $d])       -> echo view('x'[, $d])
 *   $this->session->userdata['x']      -> gv_sess('x')
 *   $this->session->userdata('x')      -> gv_sess('x')
 *   $this->session->has_userdata('x')  -> (gv_sess('x') !== null)
 *   $this->session->set_userdata(      -> gv_set(
 *   $this->lang->line(                 -> gv_lang(
 *   base_url( / site_url(              -> gv_url(
 *   $this->input->post_get(            -> gv_in(
 */

const Legacy_VIEWS = 'E:/Serveur Cloud/GestionVilles/application/views/';
const CI4_VIEWS = __DIR__ . '/app/Views/';

$rules = [
    // garde Legacy (on conserve la fermeture du bloc PHP pour ne pas
    // casser le bascule PHP -> HTML)
    ['/defined\s*\(\s*[\'"]BASEPATH[\'"]\s*\)[^;]*;/s' => ''],
    // load->view avec retour TRUE (avant la forme echo)
    ['/\$this->load->view\(\s*(.*?),\s*TRUE\s*\)/s' => 'view($1)'],
    ['/\$this->load->view\(\s*(.*?)\)/s' => 'echo view($1)'],
    // session — $_SESSION direct aussi (CI4 n'a pas de session PHP
    // demarree ; gv_sess() pont vers ci_sessions)
    ['/isset\(\s*\$_SESSION\[\s*[\'"]([^\'"]+)[\'"]\s*\]\s*\)/' => '(gv_sess(\'$1\') !== null)'],
    ['/unset\(\s*\$_SESSION\[\s*[\'"]([^\'"]+)[\'"]\s*\][^)]*\)/' => 'gv_unset(\'$1\')'],
    ['/\$_SESSION\s*\[\s*[\'"]([^\'"]+)[\'"]\s*\]\s*(?<![=<>!])=(?![=>])\s*([^;]+);/' => 'gv_set(\'$1\', $2);'],
    ['/\$_SESSION\[\s*[\'"]([^\'"]+)[\'"]\s*\]/' => 'gv_sess(\'$1\')'],
    // userdata['x'] (forme tableau) : en Legacy c'est un acces tableau
    // sur la methode userdata -> toujours null (via __get). Fidele :
    // gv_sess('userdata') lit la cle 'userdata' absente -> null.
    ['/\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]/'
        => '(gv_sess(\'userdata\')[\'$1\'] ?? null)'],
    ['/\$this->session->userdata\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => 'gv_sess(\'$1\')'],
    ['/\$this->session->has_userdata\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '(gv_sess(\'$1\') !== null)'],
    ['/\$this->session->set_userdata\(/' => 'gv_set('],
    // lang / url / input
    ['/\$this->lang->line\(/' => 'gv_lang('],
    ['/\bbase_url\s*\(/' => 'gv_url('],
    ['/\bsite_url\s*\(/' => 'gv_url('],
    ['/\$this->input->post_get\(/' => 'gv_in('],
    ['/\$this->input->get_post\(/' => 'gv_in('],
    // Tolerance aux variables indefinies : Legacy affichait vide
    // (notice silencieuse), CI4 leve ErrorException. On force ?? ''
    // sur les echoes de variables simples ($x, $x->prop, $x['k']) —
    // ?? supprime les acces indefinis sur proprietes/cles. Les appels
    // de methodes ($x->m()) sont exclus (lookahead !\( ).
    ['/echo\s+(\$\w+(?:->\w+|\[[^\]]*\])*)(?!\s*\()/'
        => 'echo ($1 ?? \'\')'],
    ['/<\?=\s*(\$\w+(?:->\w+|\[[^\]]*\])*)(?!\s*\()\s*\?>/'
        => '<?= ($1 ?? \'\') ?>'],
    // if ($x) / elseif ($x) — bare var : non defini -> notice Legacy,
    // ErrorException CI4 -> !empty()
    ['/\b(if|elseif)\s*\(\s*!\s*\$([A-Za-z_]\w*)\s*\)/'
        => '$1 (empty($$2))'],
    ['/\b(if|elseif)\s*\(\s*\$([A-Za-z_]\w*)\s*\)/'
        => '$1 (!empty($$2))'],
    // foreach ($x as ...) — tableau indefini -> []
    ['/foreach\s*\(\s*(\$\w+(?:->\w+|\[[^\]]*\])*)(?!\s*\()\s+as\b/'
        => 'foreach (($1 ?? []) as'],
    // jeton Mapbox code en dur dans la vue Legacy -> variable d'env
    // (GitHub push protection le bloque sinon)
    ['/mapboxgl\.accessToken\s*=\s*[\'"][^\'"]+[\'"]/'
        => 'mapboxgl.accessToken = \'<?= env(\'mapbox.accessToken\', \'\') ?>\''],
];

foreach (array_slice($argv, 1) as $arg) {
    $rel = preg_replace('/\.php$/i', '', trim($arg));
    $src = Legacy_VIEWS . $rel . '.php';
    $dst = CI4_VIEWS . $rel . '.php';
    if (! is_file($src)) {
        fwrite(STDERR, "INTROUVABLE: $src\n");
        continue;
    }
    $code = file_get_contents($src);
    foreach ($rules as $r) {
        $code = preg_replace(array_keys($r), array_values($r), $code);
    }
    if (! is_dir(dirname($dst))) {
        mkdir(dirname($dst), 0777, true);
    }
    file_put_contents($dst, $code);
    echo "$rel.php -> " . str_replace('\\', '/', $dst) . "\n";
}
