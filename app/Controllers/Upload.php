<?php

namespace App\Controllers;

/**
 * Port CI3 -> CI4 de upload : import de fichiers CSV/Excel par table
 * (charger/{table} -> do_upload, formulaire partials/upload_form).
 * Port mecanique via port_controller.php.
 */
class Upload extends BaseController
{
    public function do_upload($table)
    {    
        /** @var array  $config */
        $config = array();
        $config['upload_path']          = './uploads/'.$table.'/';
        $config['allowed_types']        = 'csv|xls|xlsx';
        $config['max_size']             = 10000;
        $config['file_name'] = $table.'_'. date('Y-m-d His');
        $config['file_ext_tolower'] = TRUE ;
        $this->load->library('upload', $config);
        $this->load->model('Upload_model', 'getupload');
        /** @var array  $data */
    
        if ( ! $this->upload->do_upload('userfile'))
        {
          $error = $this->upload->display_errors();
    
          $this->load->model("menu_model", "menu");
          $this->load->library('LibInscription');
          $data = $this->menu->getMenu();
          $data['dtestart'] = date('Y-m-d');
          $data['dteend'] = date('Y-m-d');
          $data['navtop'] = view('partials/navtop', $data);
          $data['navleft'] = view('partials/navleft', $data);
          $data['error'] = $error;
          $data['table'] = $table;
          $data['route'] = 'admin/charger/'.$table;
        }
        else
        {
          $data = $this->getupload->Getupload($table);
        }
        echo view('partials/upload_form', $data);
    }


    public function index($table)
    {
        if ($this->session->email == '' )
        {
          // CI3 : require co_gestion.php + new co_admin()->index()
          // (hack in-process pour rendre le login). En CI4 : le
          // formulaire de connexion est /admin/connexion — divergence
          // documentee, meme effet pour l'usager (doit se connecter).
          return redirect()->to('/admin/connexion');
        }
    		else
        {

        $this->load->model("menu_model", "menu");
    		$this->load->library('LibInscription');
        /** @var array  $datamenu */
    		$datamenu = $this->menu->getMenu();
        $datamenu['dtestart'] = date('Y-m-d');
        $datamenu['dteend'] = date('Y-m-d');
    		$datamenu['navtop'] = view('partials/navtop', $datamenu);
    		$datamenu['navleft'] = view('partials/navleft', $datamenu);
        $datamenu['error'] = ' ';
        $datamenu['table'] = $table;
        $datamenu['route'] = 'admin/charger/'.$table;
        echo view('partials/upload_form', $datamenu);
        }
    }


}
