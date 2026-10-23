<?php

namespace App\Models;

/**
 * Port mecanique de Tablet_tags_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



class Tablet_tagsModel extends Ci3Model
{
    public function get_active_chauffeurs_for_city($idVille, $dateYmd)
    {
        // Adaptez à votre schéma. Idéalement réutiliser co_tablette_sani pour "chauffeurs affectés à la ville à la date".
        // Ici, on retourne juste les IDs nécessaires à l’app (ex: idchauffeur, nom, no_camion).
        return $this->db->query(
            "SELECT idchauffeur, nom, no_camion
               FROM chauffeurs_affectations
              WHERE id_ville = ? AND date_jour = ? AND actif = 1",
            [(int)$idVille, $dateYmd]
        )->getResultArray();
    }

    public function list_open_tags_for_city($idVille)
    {
        return $this->db->query(
            "SELECT id, id_ville, id_chauffeur, title, tag_code, address_text, latitude, longitude, reason,
                    description, status, latitude, longitude, image_path, created_at, updated_at
               FROM map_tags
              WHERE id_ville = ? AND status <> 'DONE'
           ORDER BY created_at DESC",
            [(int)$idVille]
        )->getResultArray();
    }

    public function list_open_tags_for_chauffeur($idChauffeur, $idVille)
    {
        // Filtre simple par ville; si vous avez des secteurs/camions, affinez ici.
        return $this->list_open_tags_for_city($idVille);
    }
}