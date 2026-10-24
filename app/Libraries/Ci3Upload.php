<?php

namespace App\Libraries;

/**
 * Adaptateur minimal de la librairie `upload` CI3 pour les modeles
 * portes mecaniquement (Requetes_model notamment). API utilisee :
 * do_upload($field), display_errors(), file_name.
 *
 * Les chemins './upload/...' de CI3 sont relatifs au webroot CI3 :
 * ils sont resolus contre `ci3.webroot` (.env) pour que les deux
 * applications voient les memes fichiers.
 */
class Ci3Upload
{
    /** @var string Nom du fichier depose (equiv. CI3 ->file_name) */
    public $file_name = '';

    /** @var string Nom original fourni par le client (CI3 client_name) */
    private $clientName = '';

    /** @var string Derniere erreur d'upload */
    private $error = '';

    /** @var array Config passee a load->library('upload', $config) */
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Equiv. CI3 : $this->upload->do_upload($field)
     */
    public function do_upload(string $field): bool
    {
        $this->error = '';
        $this->file_name = '';
        $file = \Config\Services::request()->getFile($field);
        if ($file === null || ! $file->isValid()) {
            $this->error = $file !== null ? $file->getErrorString() : 'no file uploaded';
            return false;
        }
        $this->clientName = $file->getClientName();
        $path = (string) ($this->config['upload_path'] ?? './upload');
        if (str_starts_with($path, './')) {
            $webroot = env('ci3.webroot') ?: FCPATH;
            $path = rtrim($webroot, '/\\') . substr($path, 1);
        }
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        try {
            $name = $file->getName();
            if (! empty($this->config['remove_spaces'])) {
                $name = str_replace(' ', '_', $name);
            }
            if (! empty($this->config['file_ext_tolower'])) {
                $name = preg_replace('/\.[^.]+$/', '', $name) . strtolower(strrchr($name, '.') ?: '');
            }
            // CI3 : nom impose par config['file_name'] (ex. ticket PDF
            // nettoye par upload_pdf_ticket). L'extension du fichier
            // source est ajoutee seulement si le nom force n'en a pas.
            if (! empty($this->config['file_name'])) {
                $forced = (string) $this->config['file_name'];
                $srcExt = strrchr($name, '.') ?: '';
                if ($srcExt !== '' && ! str_contains($forced, '.')) {
                    $forced .= $srcExt;
                }
                $name = $forced;
            }
            // CI3 : allowed_types — verif d'extension (liste, ex. 'pdf').
            if (! empty($this->config['allowed_types'])) {
                $allowed = preg_split('/[\|,]/', (string) $this->config['allowed_types']);
                $ext     = strtolower(ltrim(strrchr($name, '.') ?: '', '.'));
                if ($ext === '' || ! in_array($ext, array_map('strtolower', $allowed), true)) {
                    $this->error = 'The filetype you are attempting to upload is not allowed.';
                    return false;
                }
            }
            // CI3 : max_size en Ko.
            if (! empty($this->config['max_size']) && $file->getSize() > ((int) $this->config['max_size']) * 1024) {
                $this->error = 'The file you are attempting to upload is larger than the permitted size.';
                return false;
            }
            $file->move($path, $name, true);
            $this->file_name = $name;
            return true;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Equiv. CI3 : $this->upload->data() — cles utilisees par les ports
     * (file_name, file_size en Ko, ...).
     */
    public function data(): array
    {
        $ext = strrchr($this->file_name, '.') ?: '';
        $raw = $ext !== '' ? preg_replace('/\.[^.]+$/', '', $this->file_name) : $this->file_name;
        $path = (string) ($this->config['upload_path'] ?? './upload');
        if (str_starts_with($path, './')) {
            $webroot = env('ci3.webroot') ?: FCPATH;
            $path = rtrim($webroot, '/\\') . substr($path, 1);
        }
        $full = rtrim($path, '/\\') . '/' . $this->file_name;
        $sizeKb = 0;
        if (is_file($full)) {
            $sizeKb = round(filesize($full) / 1024, 2);
        }
        return [
            'file_name'   => $this->file_name,
            'file_type'   => mime_content_type($full) ?: 'application/octet-stream',
            'file_path'   => rtrim($path, '/\\') . '/',
            'full_path'   => $full,
            'raw_name'    => $raw,
            'orig_name'   => $this->file_name,
            'client_name' => $this->clientName,
            'file_ext'    => $ext,
            'file_size'   => $sizeKb,
            'is_image'   => false,
        ];
    }

    /**
     * Equiv. CI3 : $this->upload->display_errors()
     */
    public function display_errors(): string
    {
        return $this->error;
    }
}
