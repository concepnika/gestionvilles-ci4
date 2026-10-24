<?php

namespace App\Models;

/**
 * Port mecanique de Map_tags_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



class MapTagsModel extends Ci3Model
{
    public function get_reasons($activeOnly = true)
    {
        if ($activeOnly) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('sort_order', 'ASC')->order_by('label_fr', 'ASC');
        return $this->db->get('map_tag_reasons')->getResultArray();
    }

    public function create_reason($labelFr, $labelEn, $sortOrder = 0, $isActive = 1)
    {
        $data = [
            'label_fr' => $labelFr,
            'label_en' => $labelEn,
            'sort_order' => (int)$sortOrder,
            'is_active' => (int)$isActive
        ];
        $this->db->table('map_tag_reasons')->insert($data);
        return $this->db->insertID();
    }

    public function update_reason($id, $labelFr, $labelEn, $sortOrder, $isActive)
    {
        $data = [
            'label_fr' => $labelFr,
            'label_en' => $labelEn,
            'sort_order' => (int)$sortOrder,
            'is_active' => (int)$isActive
        ];
        $this->db->where('id', (int)$id);
        return $this->db->update('map_tag_reasons', $data);
    }

    public function delete_reason($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete('map_tag_reasons');
    }
}