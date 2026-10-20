<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_sanivan.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Sanivan extends BaseController
{
    public function upload_pdf_ticket(...$args)
    {
        return $this->toCi3();
    }

}
