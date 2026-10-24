<?php

namespace App\Controllers;

/**
 * Port CI3 -> CI4 de co_sessions : verrouillage d'ecran (lockScreen)
 * et check de session (chekSession — corps vide en CI3, reponse vide).
 * Port mecanique via port_controller.php.
 */
class Sessions extends BaseController
{
    public function chekSession()
    {    
    		//--------------------------------------------------------------
        //$id_user = $this->db->escape($this->session->userdata('user_id'));
        //$strSQL = "SELECT id_user,SessionStart,SessionLastUpd,email,
        //  (TIMEDIFF( NOW(), SessionLastUpd ) > '00:00:10') AS LockApp
        //    FROM webuser WHERE id_user = $id_user";
        //$query = $this->db->query($strSQL);
        //$row = $query->getRow();
        //if (isset($row))
        //{
        //  echo $row->LockApp;
        //}
    }


    public function lockScreen()
    {    
    		$data['nom'] = $this->session->userdata('nom');
    		$data['code'] = $this->session->userdata('code');
        //$this->session->set_userdata('last_page','partials/lockScreen');
    		echo view('partials/lockScreen',$data);
    }


}
