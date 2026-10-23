<?php
/**
 * Convertisseur mecanique de modeles CI3 -> CI4.
 *
 * Usage : php port_model.php <X_model> [<Y_model> ...]
 * Source : E:/Serveur Cloud/GestionVilles/application/models/<arg>.php
 * Cible  : app/Models/<PascalCase>Model.php
 *
 * Transformations (les requetes SQL et le HTML sont inchanges) :
 *   class X_model extends CI_Model     -> class XModel extends Ci3Model
 *   $this->load->database()/helper()/library() -> retires
 *   $this->load->model("y_model","y")  -> $this->y = new YModel()
 *   $this->load->view(v,d,TRUE)        -> view(v,d)
 *   $this->session->userdata['x']      -> $this->sess('x')
 *   $this->session->xxx (magic)        -> $this->sess('xxx')
 *   ->result()/row()/num_rows()/...    -> API CI4 (getResult/getRow/...)
 *   $this->lang->line(                 -> ci3lang(
 *   $this->input->                     -> conserve (adaptateur Ci3Model)
 *
 * Reste a verifier manuellement : db->get()/where()/insert() (Query
 * Builder), $this->upload, $this->email, helpers non portes.
 */

const CI3_MODELS = 'E:/Serveur Cloud/GestionVilles/application/models/';
const CI4_MODELS = __DIR__ . '/app/Models/';

$rules = [
    // garde CI3
    ['/defined\s*\(\s*[\'"]BASEPATH[\'"]\s*\)[^;]*;/s' => ''],
    // entete de classe
    ['/class\s+(\w+?)_model\s+extends\s+CI_Model/s' => 'class $1Model extends Ci3Model'],
    // loaders a retirer (database/helper/library sont fournis par Ci3Model)
    ['/\$this->load->(database|helper|library)\s*\([^;]*;/' => ''],
    // load->model("x_model","x") -> $this->x = new XModel()
    // (meme namespace App\Models : pas besoin du FQCN)
    ['/\$this->load->model\(\s*[\'"](\w+?)_model[\'"]\s*,\s*[\'"](\w+)[\'"]\s*\)/' => '$this->$2 = new $1Model()'],
    ['/\$this->load->model\(\s*[\'"](\w+?)_model[\'"]\s*\)/' => '$this->$1 = new $1Model()'],
    // load->view(v, d, TRUE) -> view(v, d)
    ['/\$this->load->view\(\s*(.*?),\s*TRUE\s*\)/s' => 'view($1)'],
    // session
    ['/\$this->session->userdata\[\s*[\'"]([^\'"]+)[\'"]\s*\]/' => '$this->sess(\'$1\')'],
    ['/\$this->session->has_userdata\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '($this->sess(\'$1\') !== null)'],
    ['/\$this->session->unset_userdata\(/' => '$this->session->unset_userdata('],
    ['/\$this->session->([a-zA-Z_]\w*)\b(?!\s*\()/' => '$this->sess(\'$1\')'],
    // lang
    ['/\$this->lang->line\(/' => 'ci3lang('],
    // API resultset CI3 -> CI4
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
    // classes globales dans le namespace App\Models
    ["/\\bnew\\s+stdClass\\b/" => 'new \\stdClass'],
    // visibilite : les props privees CI3 entrent en conflit avec les
    // protected de Ci3Model (data/objData/JourDeSemaine/...)
    ['/\bprivate\s+\$/' => 'protected $'],
    // Query Builder CI3 -> CI4 (rare, transforme les cas directs)
    ['/\$this->db->set\((.*?)\)\s*->where\((.*?)\)\s*->update\(\s*[\'"](\w+)[\'"]\s*\)/s' => '$this->db->table(\'$3\')->set($1)->where($2)->update()'],
    ['/\$this->db->where\(([^;]+)\)->update\(\s*[\'"](\w+)[\'"]\s*,\s*/' => '$this->db->table(\'$2\')->where($1)->update('],
    ['/\$this->db->insert\(\s*[\'"](\w+)[\'"]\s*,\s*/' => '$this->db->table(\'$1\')->insert('],
    ['/\$this->db->where\(([^;]+)\);\s*\$this->db->update\(\s*[\'"](\w+)[\'"]\s*,\s*([^;]+)\);/' => '$this->db->table(\'$2\')->where($1)->update($3);'],
    ['/\$this->db->get_where\(\s*([\'"]\w+[\'"])\s*,\s*/' => '$this->db->table($1)->getWhere('],
    ['/->table_exists\(/' => '->tableExists('],
    ['/->field_exists\(/' => '->fieldExists('],
    ['/->trans_start\(/' => '->transStart('],
    ['/->trans_complete\(/' => '->transComplete('],
    ['/->trans_status\(/' => '->transStatus('],
];

foreach (array_slice($argv, 1) as $arg) {
    $base = preg_replace('/_model$/i', '', trim($arg));
    $src = CI3_MODELS . $base . '_model.php';
    $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $base))) . 'Model';
    $dst = CI4_MODELS . $class . '.php';
    if (! is_file($src)) {
        fwrite(STDERR, "INTROUVABLE: $src\n");
        continue;
    }
    $code = file_get_contents($src);
    foreach ($rules as $r) {
        $code = preg_replace(array_keys($r), array_values($r), $code);
    }
    // entete : <?php + namespace + docblock
    $code = preg_replace(
        '/^\s*<\?php/s',
        "<?php\n\nnamespace App\\Models;\n\n/**\n * Port mecanique de {$base}_model (CI3) — requetes SQL et\n * HTML generes identiques. Base Ci3Model fournit db/input/\n * session/data/objData. A relire : Query Builder, upload, email.\n */\n",
        $code,
        1
    );
    file_put_contents($dst, $code);
    echo "$class -> $dst\n";

    // rappel des idiomes non convertis
    foreach (
        ['db->get', 'db->where', 'db->insert', 'db->update', 'db->delete',
         'db->select', 'db->join', 'this->upload', 'this->email',
         'this->encrypt', 'mysql_', '\$_SESSION', 'form_validation'] as $pat
    ) {
        if (preg_match('/' . preg_quote($pat, '/') . '/i', $code)) {
            echo "   ! contient: $pat\n";
        }
    }
}
