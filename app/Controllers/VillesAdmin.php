<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_villes_admin.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class VillesAdmin extends BaseController
{
    public function zones_public(...$args)
    {
        return $this->toCi3();
    }

    public function zones(...$args)
    {
        return $this->toCi3();
    }

    public function saveZone(...$args)
    {
        return $this->toCi3();
    }

    public function zonesByVille(...$args)
    {
        return $this->toCi3();
    }

    public function updateZone(...$args)
    {
        return $this->toCi3();
    }

    public function listVilles(...$args)
    {
        return $this->toCi3();
    }

}
