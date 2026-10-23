<?php

namespace App\Models;

use Config\Database;

/**
 * Base des modeles portes de CI3.
 *
 * Reproduit l'environnement attendu par le code CI3 copie tel quel :
 *  - $this->db : connexion CI4 (query()/escape()/result()/row()
 *    compatibles avec l'API CI3)
 *  - $this->input->post_get()/get()/post() : relaie a la requete CI4
 *  - $this->data / $this->objData : accumulauteurs de sortie HTML
 *    (les modeles CI3 generent des fragments HTML)
 *  - $this->load->model('X_model', 'x') -> new XModel()
 *  - $this->session : pont Ci3Session
 *  - $this->JourDeSemaine : colonnes reshumaine DIM..SAM
 */
abstract class Ci3Model
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $db;

    /** @var array accumulateur (equiv. $this->data CI3) */
    protected $data = [];

    /** @var object (equiv. $this->objData CI3) */
    protected $objData;

    /** @var object adaptateur de $this->input CI3 */
    protected $input;

    /** @var \App\Libraries\Ci3Session */
    protected $ci3session;

    /** @var array (equiv. $this->JourDeSemaine CI3) */
    protected array $JourDeSemaine = ['DIM', 'LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'];

    public function __construct()
    {
        $this->db = Database::connect();
        $this->objData = new \stdClass();
        $this->ci3session = new \App\Libraries\Ci3Session();
        $this->input = new class {
            public function post_get(string $key)
            {
                return service('request')->getPostGet($key);
            }

            public function get_post(string $key)
            {
                return service('request')->getPostGet($key);
            }

            public function get(string $key)
            {
                return service('request')->getGet($key);
            }

            public function post(string $key)
            {
                return service('request')->getPost($key);
            }
        };
    }

    /**
     * Equivalent de $this->session->userdata['x'] dans les modeles CI3.
     */
    protected function sess(string $key, $default = null)
    {
        return $this->ci3session->get($key, $default);
    }
}
