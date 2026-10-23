<?php
/**
 * Convertisseur mecanique de controleurs CI3 -> CI4.
 *
 * Usage : php port_controller.php <co_xxx.php> <Cible.php>
 * Source : E:/Serveur Cloud/GestionVilles/application/controllers/
 * Cible  : app/Controllers/<Cible>.php
 *
 * Pour chaque methode du controleur CI3 :
 *  - si la cible a un stub `public function x(...$args) { return
 *    $this->toCi3(); }`  -> remplace par la version convertie
 *  - si la methode existe deja avec un vrai corps -> conservee
 *  - sinon -> ajoutee avant la derniere accolade (helpers prives)
 *
 * Adaptateurs requis sur le controleur (BaseController) :
 *   $this->db, $this->input, $this->session, $this->load,
 *   $this->output, $this->email, $this->uri
 */

$rules = [
    // session userdata['x'] : isset/empty d'abord (isset() sur un
    // appel de methode est une erreur de syntaxe)
    ['/isset\(\s*\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]\s*\)/'
        => '($this->session->userdata(\'$1\') !== null)'],
    ['/empty\(\s*\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]\s*\)/'
        => '(!$this->session->userdata(\'$1\'))'],
    // $this->session->userdata['x'] = v  -> set_userdata
    ['/\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]\s*=\s*/'
        => '$this->session->set_userdata(\'$1\', '],
    // lecture userdata['x'] -> userdata('x')
    ['/\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]/'
        => '$this->session->userdata(\'$1\')'],
    // has_userdata : l'adaptateur l'a deja
    // (laisse tel quel — la conversion ci-dessous le couvre aussi)
    // view : (v, d, TRUE) -> view(v, d)
    ['/\$this->load->view\(\s*(.*?)\s*,\s*TRUE\s*\)/s' => 'view($1)'],
    // return $this->load->view(...) — improbable mais eviter
    // `return echo` : le faire avant la regle statement
    ['/return\s+\$this->load->view\(\s*(.*?)\s*\);/s'
        => 'return view($1);'],
    // assignation : $x = $this->load->view(v, d); -> $x = view(v, d);
    // (en CI3 le retour est vide sans TRUE — l'intention est la
    // capture, view() CI4 la fournit)
    ['/=\s*\$this->load->view\(([^;]*?)\);/' => '= view($1);'],
    // statement nu : $this->load->view(v[,d]); -> echo view(...)
    ['/\$this->load->view\(([^;]*?)\);/' => 'echo view($1);'],
    // helper
    ['/\$this->load->helper\(([^;]*?)\);/' => 'helper($1);'],
    // redirect — CI3 fait exit : il faut `return` en CI4
    ['/(?<![\w:>\$])redirect\(([^;]*?)\);/' => 'return redirect()->to($1);'],
    // url helpers
    ['/(?<![\w:>\$])site_url\(/' => 'ci3_url('],
    ['/(?<![\w:>\$])base_url\(/' => 'ci3_url('],
    // lang
    ['/\$this->lang->line\(/' => 'ci3lang('],
    // show_404 / show_error
    ['/(?<![\w>\$])show_404\(\s*\)/' => 'throw new \\CodeIgniter\\Exceptions\\PageNotFoundException()'],
    ['/(?<![\w>\$])show_error\(([^;]*?)\);/' => 'throw new \\RuntimeException($1);'],
    // resultset
    ['/->result_array\(\)/' => '->getResultArray()'],
    ['/->result\(\)/' => '->getResult()'],
    ['/->row_array\(\)/' => '->getRowArray()'],
    ['/->row\(\s*(\d*)\s*\)/' => '->getRow($1)'],
    ['/->num_rows\(\)/' => '->getNumRows()'],
    ['/->insert_id\(\)/' => '->insertID()'],
    ['/->affected_rows\(\)/' => '->affectedRows()'],
    ['/->escape_str\(/' => '->escape('],
    ['/->last_query\(\)/' => '->getLastQuery()'],
    ['/->free_result\(\)/' => ''],
    // Query Builder
    ['/->table_exists\(/' => '->tableExists('],
    ['/->field_exists\(/' => '->fieldExists('],
    ['/->trans_start\(/' => '->transStart('],
    ['/->trans_complete\(/' => '->transComplete('],
    ['/->trans_status\(/' => '->transStatus('],
    ['/\$this->db->insert\(\s*[\'"](\w+)[\'"]\s*,\s*/' => '$this->db->table(\'$1\')->insert('],
    ['/\$this->db->where\(([^;]+)\);\s*\$this->db->update\(\s*[\'"](\w+)[\'"]\s*,\s*([^;]+)\);/' => '$this->db->table(\'$2\')->where($1)->update($3);'],
    ['/\$this->db->set\((.*?)\)\s*->where\((.*?)\)\s*->update\(\s*[\'"](\w+)[\'"]\s*\)/s' => '$this->db->table(\'$3\')->set($1)->where($2)->update()'],
    ['/\$this->db->where\(([^;]+)\)->update\(\s*[\'"](\w+)[\'"]\s*,\s*/' => '$this->db->table(\'$2\')->where($1)->update('],
    ['/\$this->db->get_where\(\s*([\'"]\w+[\'"])\s*,\s*/' => '$this->db->table($1)->getWhere('],
    ['/\$this->db->where\(([^;]+)\);\s*\$this->db->delete\(\s*[\'"](\w+)[\'"]\s*\)/' => '$this->db->table(\'$2\')->where($1)->delete()'],
    // uri
    ['/\$this->uri->segment\(/' => 'service(\'uri\')->getSegment('],
    ['/\$this->uri->segment_array\(\)/' => 'service(\'uri\')->getSegments()'],
    // classes globales en namespace
    ['/new\s+(?!\\\\)(stdClass|DateTime|Datetime|DateInterval|DatePeriod|Exception|DOMDocument|SimpleXMLElement|ZipArchive)\b/' => 'new \\\\$1'],
    ['/(?<![\w\\\\])(DateTime|DateInterval|DatePeriod)::/' => '\\\\$1::'],
];

/** Extrait les methodes (visibilite, nom, args, corps) d'une classe. */
function extractMethods(string $code): array
{
    $methods = [];
    $re = '/(public|private|protected)\s+(static\s+)?function\s+(\w+)\s*\(([^)]*)\)\s*\{/s';
    preg_match_all($re, $code, $m, PREG_OFFSET_CAPTURE);
    foreach ($m[0] as $i => $full) {
        $open = $full[1] + strlen($full[0]) - 1; // pos de '{'
        $depth = 1;
        $j = $open + 1;
        $len = strlen($code);
        while ($j < $len && $depth > 0) {
            $c = $code[$j];
            if ($c === '{') {
                $depth++;
            } elseif ($c === '}') {
                $depth--;
            } elseif ($c === '/' && $j + 1 < $len && $code[$j + 1] === '/') {
                // commentaire // jusqu'a la fin de ligne
                while ($j < $len && $code[$j] !== "\n") {
                    $j++;
                }
                continue;
            } elseif ($c === '#') {
                while ($j < $len && $code[$j] !== "\n") {
                    $j++;
                }
                continue;
            } elseif ($c === '/' && $j + 1 < $len && $code[$j + 1] === '*') {
                // commentaire /* ... */
                $j += 2;
                while ($j + 1 < $len && ! ($code[$j] === '*' && $code[$j + 1] === '/')) {
                    $j++;
                }
                $j++;
            } elseif ($c === '<' && substr($code, $j, 3) === '<<<') {
                // heredoc/nowdoc <<<TAG ou <<<'TAG' jusqu'a TAG en debut de ligne
                if (preg_match('/\A<<<[\'"]?(\w+)[\'"]?/', substr($code, $j), $hm)) {
                    $tag = $hm[1];
                    $end = strpos($code, "\n" . $tag, $j);
                    if ($end !== false) {
                        $j = $end;
                    }
                }
            } elseif ($c === "'" || $c === '"') {
                // sauter les chaines litterales (pas de { } dedans)
                $q = $c;
                $j++;
                while ($j < $len && $code[$j] !== $q) {
                    if ($code[$j] === '\\') {
                        $j++;
                    }
                    $j++;
                }
            }
            $j++;
        }
        $methods[$m[3][$i][0]] = [
            'vis'  => $m[1][$i][0],
            'args' => $m[4][$i][0],
            'body' => substr($code, $open + 1, $j - $open - 2),
            'pos'  => $full[1],
            'len'  => $j - $full[1],
        ];
    }
    return $methods;
}

function convertBody(string $body, array $rules): string
{
    foreach ($rules as $r) {
        $body = preg_replace(array_keys($r), array_values($r), $body);
    }
    return $body;
}

// ---- main ----
$srcPath = $argv[1] ?? '';
$dstPath = $argv[2] ?? '';
if (! is_file($srcPath) || ! is_file($dstPath)) {
    fwrite(STDERR, "Usage: php port_controller.php <src_ci3.php> <dst_ci4.php>\n");
    exit(1);
}

$src  = file_get_contents($srcPath);
$dst  = file_get_contents($dstPath);
$meth = extractMethods($src);
unset($meth['__construct']);

$report = ['converted' => [], 'kept' => [], 'appended' => [], 'nostub' => []];
$out = $dst;
$append = '';

foreach ($meth as $name => $m) {
    $newBody = convertBody($m['body'], $rules);
    $newMeth = "    {$m['vis']} function {$name}({$m['args']})\n    {"
        . preg_replace('/^/m', '    ', rtrim($newBody)) . "\n    }\n";
    $newMeth = preg_replace('/^    /m', '    ', $newMeth);

    // stub existant ?
    $stubRe = '/    public function ' . preg_quote($name, '/')
        . '\((?:\.\.\$args)?[^)]*\)\s*\{\s*return \$this->toCi3\(\);\s*\}/';
    if (preg_match($stubRe, $out)) {
        $out = preg_replace_callback(
            $stubRe, static fn () => $newMeth, $out, 1);
        $report['converted'][] = $name;
        continue;
    }
    // methode deja implementee (corps non-stub) ?
    if (preg_match('/function\s+' . preg_quote($name, '/') . '\s*\(/', $out)) {
        $report['kept'][] = $name;
        continue;
    }
    $append .= "\n" . $newMeth;
    $report['appended'][] = $name;
}

if ($append !== '') {
    $out = preg_replace_callback(
        '/\}\s*$/', static fn () => $append . "}\n", $out, 1);
}

file_put_contents($dstPath, $out);

echo 'converties : ', count($report['converted']), ' — ',
     implode(', ', $report['converted']), "\n";
echo 'conservees : ', count($report['kept']), ' — ',
     implode(', ', $report['kept']), "\n";
echo 'ajoutees   : ', count($report['appended']), ' — ',
     implode(', ', $report['appended']), "\n";
