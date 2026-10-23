<?php

namespace App\Models;

/**
 * Port mecanique de Pdf_ticket_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * Modèle pour la table pdf_tickets.
 * Gère la persistance des billets de dépotoir envoyés depuis l'application MAUI.
 */
class Pdf_ticketModel extends Ci3Model
{
    protected $_table = 'pdf_tickets';

    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Insère un nouveau billet PDF.
     *
     * @param array $data Données à insérer (id_ville, nocamion, etc.)
     * @return int|false  ID inséré ou false en cas d'échec
     */
    public function insert_ticket(array $data)
    {
        $allowed = [
            'id_ville',
            'nocamion',
            'id_chauffeur',
            'latitude',
            'longitude',
            'address_text',
            'pdf_filename',
            'pdf_path',
            'file_size',
        ];

        $filtered = [];
        foreach ($allowed as $key) {
            if (isset($data[$key])) {
                $filtered[$key] = $data[$key];
            }
        }

        if (empty($filtered['pdf_filename']) || empty($filtered['pdf_path'])) {
            return FALSE;
        }

        $this->db->insert($this->_table, $filtered);
        return $this->db->insertID() ?: FALSE;
    }

    /**
     * Récupère un billet par son ID.
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get($this->_table)->getRow();
    }

    /**
     * Liste les billets pour un camion et une date donnée.
     *
     * @param string $nocamion
     * @param string $date Format YYYY-MM-DD
     * @return array
     */
    public function get_by_camion_date($nocamion, $date)
    {
        $this->db->where('nocamion', $nocamion);
        $this->db->where('DATE(created_at)', $date);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->_table)->getResultArray();
    }
}