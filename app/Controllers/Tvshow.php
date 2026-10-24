<?php

namespace App\Controllers;

// Constantes de fichier de co_tvshow CI3 (utilisees par les methodes
// secured_encrypt/secured_decrypt — legacy, jamais appelees en prod).
const FIRSTKEY = 'm7ASkt7ZGNemL7YFYgedcYgp5YnWQIdkF1ev+3tQ1IE=';
const SECONDKEY = 'o3jhqw6GOJaVUvYSvN8APpuIBjIfMZcwurNpZ+msLGhDUWcUmL4148LeUH9yonmQWTtiB3pasj6Fut4w9pxTIA==';
const CRYPT_TEXT = 'IJZoUzzS1Ec03Z9zvg==';
const PLAIN_TEXT = 'tv_admin_nika';

/**
 * Port CI3 -> CI4 de co_tvshow : affichage TV (robertdaoust,
 * robertdaoust/tv). Pose une session "TV" puis rend la page du
 * jour (sanivan/today_tv) — port mecanique via port_controller.php.
 */
class Tvshow extends BaseController
{
    private $idiom = 'fr';
    private $history;

    private $JourDeSemaine = [
        'DIMANCHE', 'LUNDI', 'MARDI',
        'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI',
    ];

    public function index()
    {    
    		//$data = $this->input->post_get('load');
        //if ($this->secured_decrypt($data))
        //{
          $this->session->set_userdata('nom', 'TV');
          $this->session->set_userdata('email', '');
          $this->session->set_userdata('login_in', TRUE);
          $this->session->set_userdata('user_id', '0');
          $this->session->set_userdata('session_id', $this->session->session_id);
          $this->session->set_userdata('privilege','');
    
          /** @var array  $data */
          $this->load->model("Cedules_model", "cedules");
          //$ActiveDate = $this->CalculJour();
          $dateactive = $this->CalculJour();
          $this->showToday(8, $dateactive);
        //}
    }



    public function showToday($from, $dateactive)
    {    
        $datacd = array();
    		$ActiveDate = $dateactive;
    		$dateactive = $this->db->escape($dateactive);
    		//--------------------------------------------------------------
        $refreshOnly = $this->input->post_get('refreshonly');
        $typerh = $this->input->post_get('typerh');
        $typerh = ((isset($typerh))? $this->input->post_get('typerh') : 1);
        $typecam = $this->input->post_get('typecam');
        $typecam = ((isset($typecam))? $this->input->post_get('typecam') : 1);
        $typecedule = $this->input->post_get('typecedule');
        $typecedule = ((isset($typecedule))? $this->input->post_get('typecedule') : 1);
        $activedrop = $this->input->post_get('activedrop');
        $activedrop = ((isset($activedrop))? $this->input->post_get('activedrop') : 0);
        $limit = $this->input->post_get('limit');
        $limit = ((isset($activedrop))? $this->input->post_get('limit') : 0);
        $notab = $this->input->post_get('notab');
        $notab = ((isset($notab))? $this->input->post_get('notab') : 1);
        $AideIsShow = $this->input->post_get('AideIsShow');
        $AideIsShow = ((isset($AideIsShow))? $this->input->post_get('AideIsShow') : 1);
    		//--------------------------------------------------------------
        $this->load->model("Creationcd_model", "creationcd");
        $this->load->model("Cedules_model", "cedules");
    		$data = $this->creationcd->LoadCreateToday($ActiveDate, $typecedule );
        $data['employes'] = $this->cedules->GetAllEmployes('0','0');
        $datacd = $this->cedules->LoadToday($ActiveDate, $from,$activedrop);
        $data['listhresaides'] = $this->cedules->getHresAides($ActiveDate,'');
        $data['updcamion'] = $this->cedules->GetTypeCamion($typecam);
        $data['camions'] = $this->cedules->GetAllCamions($typecam);
    		$data['notab'] = $notab;
    		$data['ActiveDate'] = $ActiveDate;
        $date = date_create($ActiveDate);
        $data['jour'] = strtoupper($this->JourDeSemaine[$date->format('w')])  ;
        $data['typecedule'] = $typecedule;
        $data['ActiveDrop'] = $activedrop;
        $data['limitcedule'] = $limit;
        $data['AideIsShow'] = $AideIsShow;
        $data['from'] = $from;
        $data['nom'] = $this->session->userdata('nom');
        $data['email'] = $this->session->userdata('email');
        $data['typerh'] = $this->cedules->GetTypeRH($typerh);
        $dataall = array_merge($data, $datacd);
        if ($from == 8)
        {
          if($refreshOnly == 1)
          {
            foreach($dataall  as $key => $value)
            {
              $dataall[$key] = utf8_encode( utf8_decode($value));
            }
            $show_json = json_encode($dataall);
            echo $show_json;
          }
          else
          {
    			  echo view('sanivan/today_tv', $dataall);
          }
        }
    }

    public function secured_encrypt($data)
    {    
        $first_key = base64_decode(FIRSTKEY);
        $second_key = base64_decode(SECONDKEY);
    
        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
    
        $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
    
        $output = base64_encode($iv.$second_encrypted.$first_encrypted);
        return $output;
    }

    public function secured_decrypt($ciphertext)
    {    
        $cipher = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
          $ivlen = openssl_cipher_iv_length($cipher);
          $iv = openssl_random_pseudo_bytes($ivlen);
          $ciphertext = openssl_encrypt(PLAIN_TEXT, $cipher, FIRSTKEY, $options=0, $iv, $tag);
          //store $cipher, $iv, and $tag for decryption later
          $original_plaintext = openssl_decrypt($ciphertext, $cipher, FIRSTKEY, $options=0, $iv, $tag);
          return ($original_plaintext == PLAIN_TEXT);
        }
        return false;
    }

    private function CalculJour()
    {    
        /** @var int  $hour */
        $hour = intval(date('G'));
        /** @var string  $dateactive */
        $jour = date('w');
        switch (intval(date('w')))
        {
          case 0:
    
            $dateactive  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
            break;
          case 5:
            if ($hour > 12)
            {
              $dateactive  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+3, date("Y")));
            }
            else
            {
              $dateactive = date('Y-m-d');
            }
            break;
          case 6:
    
              $dateactive  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")));
    
            break;
          default:
            if ($hour > 12)
            {
              $dateactive  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
            }
            else
            {
              $dateactive = date('Y-m-d');
            }
        }
        return $dateactive;
    }

    public function __destruct()
    {    
        $this->db->close();
    }
}
