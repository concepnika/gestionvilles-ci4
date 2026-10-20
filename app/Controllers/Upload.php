<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de upload.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Upload extends BaseController
{
    public function do_upload(...$args)
    {
        return $this->toCi3();
    }

    public function index(...$args)
    {
        return $this->toCi3();
    }

}
