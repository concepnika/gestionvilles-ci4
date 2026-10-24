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
            $file->move($path, $name, true);
            $this->file_name = $name;
            return true;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Equiv. CI3 : $this->upload->display_errors()
     */
    public function display_errors(): string
    {
        return $this->error;
    }
}
