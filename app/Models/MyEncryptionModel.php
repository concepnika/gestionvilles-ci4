<?php

namespace App\Models;

/**
 * Port mecanique de MyEncryption_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Encryption model
 * @author: Serge Dallaire
 */
class MyEncryptionModel extends Ci3Model
{
  protected $keysha = 'niKa64982c4edeb0brdf23vf';
	function __construct() {
		parent::__construct();

		
		
	}
  public function update_client_info()
  {
    $strSQL = "SELECT email, password, idclient
	    FROM clients ";//WHERE idclient = ".$this->db->escape($this->session->userdata('idclient'));
    $result = $this->db->query($strSQL);
    foreach ($result->getResultArray() as $row) {
      $this->updateUser($row['idclient'], $row);
    }
  }
  public function update_ship1()
  {
    $strSQL = "SELECT ship_adresse, ship_code_postal, ship_ville, ship_telephone,idclient
	            FROM clients
              ";//WHERE idclient = " . $this->db->escape($this->session->userdata('idclient'));
    $result = $this->db->query($strSQL);
    foreach ($result->getResultArray() as $row) {
      $this->encrypt_ship1($row['idclient'], $row);
    }
  }
  public function update_ship2()
  {
    $strSQL = "SELECT  ship2_adresse, ship2_code_postal, ship2_ville, ship2_telephone, idclient
	    FROM clients ";//ORDER BY idclient";
    $result = $this->db->query($strSQL);
    foreach ($result->getResultArray() as $row) {
      $this->encrypt_ship2($row['idclient'], $row);
    }
  }
  public function update_all_decrypt_client()
  {
    $strSQL = "SELECT email, nom, prenom, adresse, ville, code_postal, ship_nom, ship_prenom, ship_adresse, ship_province, ship_code_postal, password, ship_ville, telephone, ship_telephone, telephone2, ship2_nom, ship2_prenom, ship2_adresse, ship2_province, ship2_code_postal, ship2_ville, ship2_telephone, ship2_pays, ship2_titre, idclient
	    FROM clients ORDER BY idclient";
    $result = $this->db->query($strSQL);
    foreach ($result->getResultArray() as $row) {
      $this->updateUser($row['idclient']);
    }
  }
  private function updateclient_encrypt($idclient, $data)
  {
    /** @var array $dataResult */
    $dataResult = array();
    $this->db->table('clients')->where('idclient', $idclient)->update($data);
    $error = $this->db->error();
    if (!empty($error['code'])) {
      $dataResult['msgerr'] = "Une erreur s'est produite.";
      $dataResult['id'] = $idclient;
    } else {
      $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
    }
    $dataResult['idclient'] = $idclient;
    return $dataResult;
  }
  public function encrypt_client($idclient,$data)
  {
    //$data['email'] = $this->encrypt_decrypt('encrypt', $data['email']);

    //$data['telephone'] = $this->encrypt_decrypt('encrypt', $data['telephone']);
    //$data['adresse'] = $this->encrypt_decrypt('encrypt', $data['adresse']);
    //$data['ville'] = $this->encrypt_decrypt('encrypt', $data['ville']);
    //$data['code_postal'] = $this->encrypt_decrypt('encrypt', $data['code_postal']);

    $data['password'] = $this->encrypt_decrypt('encrypt', $data['password']);

    $this->updateclient_encrypt($idclient, $data);
  }
  public function decrypt_client($idclient,$data)
  {
    $data['email'] = $this->encrypt_decrypt('decrypt', $data['email']);
    //$data['telephone'] = $this->encrypt_decrypt('decrypt', $data['telephone']);
    //$data['adresse'] = $this->encrypt_decrypt('decrypt', $data['adresse']);
    //$data['ville'] = $this->encrypt_decrypt('decrypt', $data['ville']);
    //$data['code_postal'] = $this->encrypt_decrypt('decrypt', $data['code_postal']);

    //$data['password'] = $this->encrypt_decrypt('decrypt', $data['password']);

    $this->updateclient_encrypt($idclient, $data);
  }
  public function encrypt_ship1($idclient,$data)
  {

    $data['ship_adresse'] = $this->encrypt_decrypt('encrypt', $data['ship_adresse']);
    $data['ship_code_postal'] = $this->encrypt_decrypt('encrypt', $data['ship_code_postal']);
    $data['ship_ville'] = $this->encrypt_decrypt('encrypt', $data['ship_ville']);
    $data['ship_telephone'] = $this->encrypt_decrypt('encrypt', $data['ship_telephone']);

    $this->updateclient_encrypt($idclient, $data);
  }
  public function decrypt_ship1($idclient,$data)
  {
    $data['ship_adresse'] = $this->encrypt_decrypt('decrypt', $data['ship_adresse']);
    $data['ship_code_postal'] = $this->encrypt_decrypt('decrypt', $data['ship_code_postal']);
    $data['ship_ville'] = $this->encrypt_decrypt('decrypt', $data['ship_ville']);
    $data['ship_telephone'] = $this->encrypt_decrypt('decrypt', $data['ship_telephone']);
    $this->updateclient_encrypt($idclient, $data);
  }
  public function encrypt_ship2($idclient,$data)
  {
    $data['ship2_adresse'] = $this->encrypt_decrypt('encrypt', $data['ship2_adresse']);
    $data['ship2_code_postal'] = $this->encrypt_decrypt('encrypt', $data['ship2_code_postal']);
    $data['ship2_ville'] = $this->encrypt_decrypt('encrypt', $data['ship2_ville']);
    $data['ship2_telephone'] = $this->encrypt_decrypt('encrypt', $data['ship2_ville']);
    $this->updateclient_encrypt($idclient, $data);
  }
  public function decrypt_ship2($idclient,$data)
  {
    $data['ship2_adresse'] = $this->encrypt_decrypt('decrypt', $data['ship2_adresse']);
    $data['ship2_code_postal'] = $this->encrypt_decrypt('decrypt', $data['ship2_code_postal']);
    $data['ship2_ville'] = $this->encrypt_decrypt('decrypt', $data['ship2_ville']);
    $data['ship2_telephone'] = $this->encrypt_decrypt('decrypt', $data['ship2_ville']);
    $this->updateclient_encrypt($idclient, $data);
  }
  public function decrypt_clientall($idclient,$data)
  {
    $data['email'] = $this->encrypt_decrypt('decrypt', $data['email']);
    $data['adresse'] = $this->encrypt_decrypt('decrypt', $data['adresse']);
    $data['ville'] = $this->encrypt_decrypt('decrypt', $data['ville']);
    $data['code_postal'] = $this->encrypt_decrypt('decrypt', $data['code_postal']);
    $data['ship_adresse'] = $this->encrypt_decrypt('decrypt', $data['ship_adresse']);
    $data['ship_province'] = $this->encrypt_decrypt('decrypt', $data['ship_province']);
    $data['ship_code_postal'] = $this->encrypt_decrypt('decrypt', $data['ship_code_postal']);
    $data['password'] = $this->encrypt_decrypt('decrypt', $data['password']);
    $data['ship_ville'] = $this->encrypt_decrypt('decrypt', $data['ship_ville']);
    $data['telephone'] = $this->encrypt_decrypt('decrypt', $data['telephone']);
    $data['ship_telephone'] = $this->encrypt_decrypt('decrypt', $data['ship_telephone']);
    $data['telephone2'] = $this->encrypt_decrypt('decrypt', $data['telephone2']);
    $data['ship2_adresse'] = $this->encrypt_decrypt('decrypt', $data['ship2_adresse']);
    $data['ship2_province'] = $this->encrypt_decrypt('decrypt', $data['ship2_province']);
    $data['ship2_code_postal'] = $this->encrypt_decrypt('decrypt', $data['ship2_code_postal']);
    $data['ship2_ville'] = $this->encrypt_decrypt('decrypt', $data['ship2_ville']);
    $data['ship2_telephone'] = $this->encrypt_decrypt('decrypt', $data['ship2_ville']);
    $this->updateclient_encrypt($idclient, $data);
  }
  public function verifClientLogin($fromadmin)
  {
    /** @var string  $strSQL */
    $strSQL = '';
    /** @var string  $email */
    //$email = $this->db->escape($this->input->post_get('email', TRUE));
    $email = $this->input->post_get('email', TRUE) ;
    $email = $this->db->escape(trim($this->encrypt_decrypt('encrypt', $email))) ;
    /** @var string  $password */
    $pass = $this->input->post_get('password', TRUE);
    $password = $this->db->escape(trim($this->encrypt_decrypt('encrypt', $pass)));
    if ($fromadmin == 0) {
      $strSQL = "SELECT * FROM `clients` WHERE  trim(`password`) = $password";
    } else {
      $idclient = $this->db->escape($this->input->post_get('idclient', TRUE));
      $strSQL = "SELECT * FROM `clients` WHERE `idclient` = $idclient";
    }
    return $this->db->query($strSQL)->getRow(0);
  }
  public function encrypt_decrypt($action, $string)
  {
    $output = '';

    $encrypt_method = "AES-256-CBC";
    $secret_key =
      'SIW3FypXCCq0XrRzifsxwxBFDZMroou1kRhbAEkMXSq2e3s+sF7Dc9aTG2J1IerORCGnPisyEYPjx3tWktjBYw==';
    $secret_iv = 'niKa64982c4edeb0bPhase3synd';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
      $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
      $output = base64_encode($output);
    } else if ($action == 'decrypt') {
      $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
  }
  public function validate_client($data)
  {
    /** @var string $password */
    $password = $data['password'];
    $pwd_peppered = hash_hmac("sha256", $password, $this->keysha);
    $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);
    $this->db->where('email', $data['email']);
    $result = $this->db->get('clients')->getRow();
    if (isset($result)) {
      if (password_verify($pwd_peppered, $result->password))
      {
        return $result;
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  public function updateUser($idclient,$data)
  {
    $valPass = '';
    if (trim($data['password']) != '') {
      $valPass = $data['password'];
      $pwd_peppered = hash_hmac("sha256", $valPass, $this->keysha);
      $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);
      $valPass = 'password=' . $this->db->escape($pwd_hashed);

    }
    /** @var string $chaineSQL */
    $chaineSQL = "UPDATE clients
											SET
												$valPass
											WHERE idclient = $idclient";
    $result = $this->db->query($chaineSQL);
    //return (($this->db->query($chaineSQL)) ? 'OK' : 'NON');
    //--------------------------------------------------
    //return (($result) ? 'OK' : '');
  }
  public function verifLogin($fromadmin)
  {
    //------------------------------------
  }
	function __destruct() {
		$this->db->close();
	}
}
