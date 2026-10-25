<?php

namespace App\Models;

/**
 * Port mecanique de Login_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class LoginModel extends Ci3Model
{
	public $keysha = 'nikajhdgsjgdsdssd';
	function __construct() {
		parent::__construct();
		
		
	}

	public function validate_user($data) {
		$user = (new \App\Libraries\WebuserAuth())->verify(
			(string) ($data['email'] ?? ''),
			(string) ($data['password'] ?? '')
		);

		return $user ?? false;
	}
  public function validate_chauffeur($data) {
		/** @var string $password */
		$password = $data['password'];
		$pwd_peppered = hash_hmac("sha256", $password, $this->keysha);
		$this->db->where('login', $data['login']);
		$result = $this->db->get('reshumaine')->getRow();
		if (password_verify($pwd_peppered, $result->pass)) {
			return $result;
		}
		else {
			return FALSE;
		}
	}
	public function updateUser()
  {
    $valPass = '';
		if ( trim($this->input->post_get('password')) != '')
		{
			$valPass = $this->input->post_get('password');
      $pwd_peppered = hash_hmac("sha256", $valPass, $this->keysha);
      $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);
      $valPass = 'pass=' . $this->db->escape($pwd_hashed);

		}
		$nom = $this->db->escape($this->input->post_get('nom'));
		$prenom = $this->db->escape($this->input->post_get('prenom'));
    //$privilege = $this->db->escape($this->input->post_get('privilege'));
    //$privilege = "privilege= $privilege".((trim($valPass) == '')? '' : ',');
    /** @var string $chaineSQL */
		$chaineSQL = "UPDATE webuser
											SET
												nom= $nom,
												prenom= $prenom,
												$valPass
											WHERE email = " . $this->db->escape($this->input->post_get('email'));
		$result = $this->db->query($chaineSQL);
    //return (($this->db->query($chaineSQL)) ? 'OK' : 'NON');
    //--------------------------------------------------
		return (($result) ? 'OK' : '');
  }
	function __destruct() {
		$this->db->close();
	}
}
