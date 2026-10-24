<?php

namespace App\Controllers;

use App\Models\MapTagsModel;

/**
 * Port CI3 -> CI4 de Co_admin_tags : gestion des tags de carte et de
 * leurs raisons (admin) — port mecanique via port_controller.php.
 */
class AdminTags extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        // CI3 __construct : load->model('Map_tags_model', 'tags')
        $this->tags = new MapTagsModel();
    }

    public function tags()
    {    
            $data['tags'] = $this->db
                ->order_by('created_at', 'DESC')
                ->get('map_tags')
                ->getResultArray();
    
            $data['message'] = $this->session->flashdata('admin_tag_msg');
            $data['error']   = $this->session->flashdata('admin_tag_err');
    
            echo view('admin/map_tags_manage', $data);
    }


    public function delete_tag()
    {    
            $id = (int) $this->input->post('id');
            if ($id <= 0) {
                $this->session->set_flashdata('admin_tag_err', 'Tag invalide.');
                return redirect()->to(ci3_url('admin/map-tags'));
                return;
            }
    
            $this->db->transStart();
            $this->db->where('id_map_tag', $id)->delete('map_tag_responses');
            $this->db->where('id', $id)->delete('map_tags');
            $this->db->transComplete();
    
            if ($this->db->transStatus()) {
                $this->session->set_flashdata('admin_tag_msg', 'Tag supprimé.');
            } else {
                $this->session->set_flashdata('admin_tag_err', "Impossible de supprimer le tag.");
            }
    
            return redirect()->to(ci3_url('admin/map-tags'));
    }


    public function reasons()
    {    
            $data['reasons'] = $this->tags->get_reasons(false);
            echo view('admin/map_tag_reasons', $data);
    }


    public function reasons_save()
    {    
            $id        = (int)$this->input->post('id');
            $labelFr   = trim($this->input->post('label_fr', true));
            $labelEn   = trim($this->input->post('label_en', true));
            $sortOrder = (int)$this->input->post('sort_order', true);
            $isActive  = (int)$this->input->post('is_active', true);
    
            if ($labelFr === '' || $labelEn === '') {
                return redirect()->to(ci3_url('admin/tags/reasons'));
                return;
            }
    
            if ($id > 0) {
                $this->tags->update_reason($id, $labelFr, $labelEn, $sortOrder, $isActive);
            } else {
                $this->tags->create_reason($labelFr, $labelEn, $sortOrder, $isActive);
            }
            return redirect()->to(ci3_url('admin/tags/reasons'));
    }


    public function reasons_delete($id = 0)
    {    
            $id = (int)$id;
            if ($id > 0) {
                $this->tags->delete_reason($id);
            }
            return redirect()->to(ci3_url('admin/tags/reasons'));
    }


}
