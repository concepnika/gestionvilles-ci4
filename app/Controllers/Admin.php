<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_admin.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Admin extends BaseController
{
    public function getClientsCharts(...$args)
    {
        return $this->toCi3();
    }

}
