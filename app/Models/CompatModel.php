<?php

namespace App\Models;

use Config\Database;

/**
 * Base des modeles portes de Legacy.
 *
 * Reproduit l'environnement attendu par le code Legacy copie tel quel :
 *  - $this->db : connexion CI4 (query()/escape()/result()/row()
 *    compatibles avec l'API Legacy)
 *  - $this->input->post_get()/get()/post() : relaie a la requete CI4
 *  - $this->data / $this->objData : accumulauteurs de sortie HTML
 *    (les modeles Legacy generent des fragments HTML)
 *  - $this->load->model('X_model', 'x') -> new XModel()
 *  - $this->session : pont CompatSession
 *  - $this->JourDeSemaine : colonnes reshumaine DIM..SAM
 */
#[\AllowDynamicProperties]
abstract class CompatModel
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $db;

    /** @var array accumulateur (equiv. $this->data Legacy) */
    protected $data = [];

    /** @var object (equiv. $this->objData Legacy) */
    protected $objData;

    /** @var object adaptateur de $this->input Legacy */
    protected $input;

    /** @var \App\Libraries\CompatSession */
    protected $gv_session;

    /** @var object adaptateur de $this->session Legacy (magic userdata) */
    protected $session;

    /** @var \App\Libraries\CompatUpload (equiv. $this->upload Legacy) */
    protected $upload;

    /** @var array (equiv. $this->JourDeSemaine Legacy — non type :
     * les classes portees le redeclarent sans type) */
    protected $JourDeSemaine = ['DIM', 'LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'];

    public function __construct()
    {
        $this->db = new \App\Libraries\CompatDb();
        $this->objData = new \stdClass();
        $this->gv_session = new \App\Libraries\CompatSession();
        helper(['gv', 'compat', 'text']);
        $sess = $this->gv_session;
        $this->session = new class ($sess) {
            private $s;

            public function __construct($s)
            {
                $this->s = $s;
            }

            /** Magic : $this->session->email (Legacy) -> userdata('email') */
            public function __get(string $key)
            {
                return $this->s->get($key);
            }

            public function userdata(?string $key = null)
            {
                return $key === null ? $this->s->user() : $this->s->get($key);
            }

            public function has_userdata(string $key): bool
            {
                return $this->s->get($key) !== null;
            }

            public function set_userdata($key, $value = null): void
            {
                foreach (is_array($key) ? $key : [$key => $value] as $k => $v) {
                    $this->s->set($k, $v);
                }
            }

            public function unset_userdata($keys): void
            {
                $this->s->remove($keys);
            }
        };
        $this->input = new class {
            public function post_get(?string $key = null)
            {
                return service('request')->getPostGet($key);
            }

            public function get_post(?string $key = null)
            {
                return service('request')->getPostGet($key);
            }

            public function get(?string $key = null)
            {
                return service('request')->getGet($key);
            }

            public function post(?string $key = null)
            {
                return service('request')->getPost($key);
            }
        };
    }

    /**
     * Equivalent de $this->session->userdata['x'] dans les modeles Legacy.
     */
    protected function sess(string $key, $default = null)
    {
        return $this->gv_session->get($key, $default);
    }
}
