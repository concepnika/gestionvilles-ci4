<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_osrm.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Osrm extends BaseController
{
    public function showLeaflet(...$args)
    {
        return $this->toCi3();
    }

}
