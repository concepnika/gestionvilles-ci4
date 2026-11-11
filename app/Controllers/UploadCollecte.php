<?php

namespace App\Controllers;

/**
 * Squelette du port Legacy -> CI4 de upload_collecte.
 * Chaque methode redirige vers gestionvilles.local (Legacy) via
 * toCompat() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class UploadCollecte extends BaseController
{
    public function index(...$args)
    {
        return $this->toCompat();
    }

}
