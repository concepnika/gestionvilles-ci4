<?php

namespace App\Controllers;

use App\Models\PdfTicketModel;

/**
 * Port CI3 -> CI4 de co_sanivan : SEULE la methode routee
 * upload_pdf_ticket est portee (sanivan/upload_pdf_ticket, POST) —
 * les autres methodes de co_sanivan ne sont routées nulle part en
 * CI3 (supplantées par co_gestion / co_tablette_sani).
 *
 * Les PDF sont ecrits dans uploads/pdf_tickets/YYYY/MM/ du docroot
 * CI3 (env ci3.webroot) — URLs /uploads/... servies par CI3 en prod
 * (Alias vhost /uploads localement), meme convention que les autres
 * uploads du port.
 */
class Sanivan extends BaseController
{
    public function upload_pdf_ticket()
    {
        // --- Autoriser uniquement POST ---
        if ($this->input->method() !== 'post') {
            $this->_json_response(FALSE, 'Méthode non autorisée (POST requis)', 405);
            return;
        }

        // --- Lecture des champs texte ---
        $id_ville = $this->input->post('id_ville');
        $nocamion = trim($this->input->post('nocamion') ?? '');
        $id_chauffeur = $this->input->post('id_chauffeur');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $address_text = $this->input->post('address_text');

        // --- Validation minimale ---
        if (empty($nocamion)) {
            $this->_json_response(FALSE, 'Le champ nocamion est obligatoire');
            return;
        }

        // --- Vérifier la présence du fichier ---
        if (empty($_FILES['pdf']['name'])) {
            $this->_json_response(FALSE, 'Aucun fichier PDF reçu');
            return;
        }

        // --- Préparer le dossier de destination ---
        // Structure : uploads/pdf_tickets/YYYY/MM/
        // CI3 : FCPATH . "uploads/..." -> docroot CI3 en CI4.
        $year = date('Y');
        $month = date('m');
        $webroot = rtrim((string) env('ci3.webroot'), '/\\');
        $upload_dir = $webroot . "/uploads/pdf_tickets/{$year}/{$month}/";

        if (! is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, TRUE);
        }

        // APRÈS — garder le nom original envoyé par l'application
        $originalName = $_FILES['pdf']['name'];                    // ex: Blainville_D_07.43_2026-04-06.pdf
        $safeName = preg_replace('/[^A-Za-z0-9_.\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $safeName = $safeName . '.pdf';

        $config = [
            'upload_path' => $upload_dir,
            'allowed_types' => 'pdf',
            'max_size' => 10240,
            'file_ext_tolower' => TRUE,
            'encrypt_name' => FALSE,   // ← garder le nom original
            'file_name' => $safeName, // ← nom nettoyé de l'app
        ];

        // CI3 : $this->load->library('upload', $config)
        $this->upload = new \App\Libraries\Ci3Upload($config);

        if (! $this->upload->do_upload('pdf')) {
            $error = strip_tags($this->upload->display_errors());
            log_message('error', '[upload_pdf_ticket] Échec upload: ' . $error);
            $this->_json_response(FALSE, 'Échec upload : ' . $error);
            return;
        }

        // --- Données du fichier uploadé ---
        $file_data = $this->upload->data();
        $pdf_filename = $file_data['file_name'];
        $pdf_relative = "uploads/pdf_tickets/{$year}/{$month}/{$pdf_filename}";

        // --- Insertion en base de données ---
        // CI3 : load->model('Pdf_ticket_model') sans alias -> instance directe.
        $pdfTicketModel = new PdfTicketModel();

        $insert_data = [
            'id_ville' => ! empty($id_ville) ? (int) $id_ville : NULL,
            'nocamion' => $nocamion,
            'id_chauffeur' => ! empty($id_chauffeur) ? (int) $id_chauffeur : NULL,
            'latitude' => is_numeric($lat) ? (float) $lat : NULL,
            'longitude' => is_numeric($lng) ? (float) $lng : NULL,
            'address_text' => ! empty($address_text) ? mb_substr($address_text, 0, 500) : NULL,
            'pdf_filename' => $pdf_filename,
            'pdf_path' => $pdf_relative,
            'file_size' => (int) $file_data['file_size'] * 1024, // CI retourne en Ko
        ];

        $inserted_id = $pdfTicketModel->insert_ticket($insert_data);

        if (! $inserted_id) {
            log_message('error', '[upload_pdf_ticket] Échec insertion DB pour camion=' . $nocamion);
            $this->_json_response(FALSE, 'Erreur lors de la sauvegarde en base de données');
            return;
        }

        // --- Réponse succès ---
        $pdf_url = ci3_url($pdf_relative);

        log_message('info', "[upload_pdf_ticket] OK id={$inserted_id} camion={$nocamion} fichier={$pdf_filename}");

        $this->_json_response(TRUE, NULL, 200, [
            'id' => (int) $inserted_id,
            'pdf_url' => $pdf_url,
            'filename' => $pdf_filename,
        ]);
    }

    /**
     * Réponse JSON standardisée (réutilisable par d'autres méthodes).
     *
     * @param bool        $ok
     * @param string|null $error
     * @param int         $http_code
     * @param array|null  $data
     */
    private function _json_response($ok, $error = NULL, $http_code = 200, $data = NULL)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok' => (bool) $ok,
                'error' => $error,
                'data' => $data,
            ], JSON_UNESCAPED_UNICODE));
    }
}
