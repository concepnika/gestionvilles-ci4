<?php

namespace App\Models;

/**
 * Port mecanique de Profile_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class ProfileModel extends Ci3Model
{

    function __construct() {
        parent::__construct();
        
				
    }

    public function getUserProfil($file) {
			if (($this->sess('email') !== null))
			{
				/** @var array $data */
				/** @var string $returnval */
				$this->db->where('email', $this->sess('email'));
				/** @var object $userdata */
				$userdata = new \stdClass();
				/** @var object $row */
				$userdata =  $this->db->get('utilisateurs')->getRow();
				if(isset($userdata))
				{
					$data['user'] = $userdata;
					/** @var array $optiondata */
					$optiondata = $this->db->get('optionssys')->getRow();
					if(isset( $optiondata))
					{
						$data['option'] = $optiondata;
						$this->load->view($file, $data);
					}
				}
			}
    }

    function __destruct() {
        $this->db->close();
    }
}
