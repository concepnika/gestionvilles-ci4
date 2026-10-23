<?php
/**
 * Convertisseur mecanique de vues CI3 -> CI4.
 *
 * Usage : php port_view.php <chemin/vue> [<chemin/vue> ...]
 * Source : E:/Serveur Cloud/GestionVilles/application/views/<arg>.php
 * Cible  : app/Views/<arg>.php
 *
 * Transformations :
 *   defined('BASEPATH') ...            -> retire
 *   $this->load->view('x', $d, TRUE)   -> view('x', $d)
 *   $this->load->view('x'[, $d])       -> echo view('x'[, $d])
 *   $this->session->userdata['x']      -> ci3sess('x')
 *   $this->session->userdata('x')      -> ci3sess('x')
 *   $this->session->has_userdata('x')  -> (ci3sess('x') !== null)
 *   $this->session->set_userdata(      -> ci3set(
 *   $this->lang->line(                 -> ci3lang(
 *   base_url( / site_url(              -> ci3_url(
 *   $this->input->post_get(            -> ci3in(
 */

const CI3_VIEWS = 'E:/Serveur Cloud/GestionVilles/application/views/';
const CI4_VIEWS = __DIR__ . '/app/Views/';

$rules = [
    // garde CI3 (on conserve la fermeture du bloc PHP pour ne pas
    // casser le bascule PHP -> HTML)
    ['/defined\s*\(\s*[\'"]BASEPATH[\'"]\s*\)[^;]*;/s' => ''],
    // load->view avec retour TRUE (avant la forme echo)
    ['/\$this->load->view\(\s*(.*?),\s*TRUE\s*\)/s' => 'view($1)'],
    ['/\$this->load->view\(\s*(.*?)\)/s' => 'echo view($1)'],
    // session
    ['/\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]/' => 'ci3sess(\'$1\')'],
    ['/\$this->session->userdata\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => 'ci3sess(\'$1\')'],
    ['/\$this->session->has_userdata\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '(ci3sess(\'$1\') !== null)'],
    ['/\$this->session->set_userdata\(/' => 'ci3set('],
    // lang / url / input
    ['/\$this->lang->line\(/' => 'ci3lang('],
    ['/\bbase_url\s*\(/' => 'ci3_url('],
    ['/\bsite_url\s*\(/' => 'ci3_url('],
    ['/\$this->input->post_get\(/' => 'ci3in('],
    ['/\$this->input->get_post\(/' => 'ci3in('],
    // Tolerance aux variables indefinies : CI3 affichait vide
    // (notice silencieuse), CI4 leve ErrorException. On force ?? ''
    // sur les echoes de variables simples ($x, $x->prop, $x['k']) —
    // ?? supprime les acces indefinis sur proprietes/cles. Les appels
    // de methodes ($x->m()) sont exclus (lookahead !\( ).
    ['/echo\s+(\$\w+(?:->\w+|\[[^\]]*\])*)(?!\s*\()/'
        => 'echo ($1 ?? \'\')'],
    ['/<\?=\s*(\$\w+(?:->\w+|\[[^\]]*\])*)(?!\s*\()\s*\?>/'
        => '<?= ($1 ?? \'\') ?>'],
    // if ($x) / elseif ($x) — bare var : non defini -> notice CI3,
    // ErrorException CI4 -> !empty()
    ['/\b(if|elseif)\s*\(\s*!\s*\$([A-Za-z_]\w*)\s*\)/'
        => '$1 (empty($$2))'],
    ['/\b(if|elseif)\s*\(\s*\$([A-Za-z_]\w*)\s*\)/'
        => '$1 (!empty($$2))'],
    // foreach ($x as ...) — tableau indefini -> []
    ['/foreach\s*\(\s*(\$\w+(?:->\w+|\[[^\]]*\])*)(?!\s*\()\s+as\b/'
        => 'foreach (($1 ?? []) as'],
];

foreach (array_slice($argv, 1) as $arg) {
    $rel = preg_replace('/\.php$/i', '', trim($arg));
    $src = CI3_VIEWS . $rel . '.php';
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
