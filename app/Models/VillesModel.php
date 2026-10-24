<?php

namespace App\Models;

/**
 * Port mecanique de Villes_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class VillesModel extends Ci3Model
{
    /** @var array $data */
    protected $data = array();
    public $keysha = 'niKa64982c4edeb0brdf23vf';
    function __construct()
    {
        parent::__construct();
        
        
    }
    /**
     *
     * @param int $idville
     * @param int $rang
     *
     * @return array
     *
     */
    public function insert_data($idville, $rang)
    {
        /** @var array $dataResult */
        $dataResult = array();
        $key = $this->db->escape($this->keysha);
        $email = $this->db->escape(urldecode($this->input->post_get('email')));
        $password = $this->db->escape(urldecode($this->input->post_get('pass')));
        $strSQL = "INSERT INTO villesrecipiendaire
	                  (idville, rang, email, `password`)
	                  VALUES ($idville, $rang,
                              AES_ENCRYPT($email, UNHEX(SHA2($key,512))),
                              AES_ENCRYPT($password, UNHEX(SHA2($key,512))))";
        $this->db->query($strSQL);
        $error = $this->db->error();
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        $dataResult['id'] = $this->db->insertID();
        return $dataResult;
    }

    public function update_data($idville, $rang)
    {
        $key = $this->db->escape($this->keysha);
        $email = $this->db->escape(urldecode($this->input->post_get('email')));
        $password = $this->db->escape(urldecode($this->input->post_get('pass')));
        $strSQL = "UPDATE villesrecipiendaire
                  SET
                    email=AES_ENCRYPT($email, UNHEX(SHA2($key,512))),
                    `password`=AES_ENCRYPT($password, UNHEX(SHA2($key,512)))
                WHERE idville = $idville AND rang = $rang";
        $this->db->query($strSQL);
        /** @var array $dataResult */
        $dataResult = array();
        $error = $this->db->error();
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        return $dataResult;
    }

    public function getInfoConnect($idville)
    {
        $this->data = array();
        $idville = $this->db->escape($idville);
        $key = $this->db->escape($this->keysha);
        /** @var string  $returnval */
        $returnval = '';
        /** @var string  $strSQL */
        $strSQL = "SELECT id, idville, AES_DECRYPT(email, UNHEX(SHA2($key,512))) AS mail, AES_DECRYPT(password, UNHEX(SHA2($key,512))) AS pass, rang, nom,prenom,cell,phone FROM villesrecipiendaire WHERE idville = $idville";
        $recipient = $this->db->query($strSQL);
        foreach ($recipient->getResult() as $row) {
            $this->data['inputEmail'.$row->rang] = $row->mail;
            $this->data['inputPassword'.$row->rang] = $row->pass;
        }
        if (count($this->data) < 3) {
            for ($iter = 1; $iter < 4; $iter++) {
                if (! isset($this->data['inputEmail'.$iter])) {
                    $this->data['inputEmail'.$iter] = '';
                    $this->data['inputPassword'.$iter] = '';
                }
            }
        }
        return $this->data;
    }

    /**
     * Retourne toutes les zones actives pour une ville (ou globales si $idville === null)
     * @param int|null $idville
     * @return array rows (id, idville, name, geojson, created_at, updated_at)
     */
    public function get_zones_by_ville($idville = null)
    {
        if ($idville === null) {
            $this->db->where('idville IS NULL', null, false);
        } else {
            $this->db->where('idville', (int) $idville);
        }
        $this->db->where('is_active', 1);
        $this->db->order_by('created_at', 'DESC');
        $q = $this->db->get('villes_zones');
        return $q->getResultArray();
    }

    /**
     * Insère une zone (polygone GeoJSON)
     */
    public function insert_zone($name, $geojson, $idville = null)
    {
        $now = date('Y-m-d H:i:s');
        $uid = $this->session->userdata('user_id');
        $data = [
            'name' => $name,
            'geojson' => $geojson,
            'idville' => $idville,
            'is_active' => 1,
            'created_by' => ($uid !== null ? $uid : null),
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $ok = $this->db->table('villes_zones')->insert($data);
        if (! $ok) {
            $err = $this->db->error();
            return ['ok' => false, 'error' => (isset($err['message']) ? $err['message'] : 'insert failed')];
        }
        return ['ok' => true, 'id' => (int) $this->db->insertID()];
    }

    function __destruct()
    {
        $this->db->close();
    }
}