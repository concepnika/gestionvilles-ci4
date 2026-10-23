<?php

namespace App\Models;

/**
 * Port mecanique de Menu_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class MenuModel extends Ci3Model
{
	/** @var array $data */
	protected $data = array();
	function __construct() {
		parent::__construct();
		
		
	}
	public function getMenu(){
		if (($this->sess('nom') !== null))
		{


			$this->data = [
								'email' => $this->sess('email'),
								'nom' => $this->sess('nom')
							];

		}
		return $this->data ;
	}

	function __destruct() {
		$this->db->close();
	}
}
