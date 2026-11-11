<?php

namespace App\Controllers;

/**
 * Squelette du port Legacy -> CI4 de co_admin.
 * Chaque methode redirige vers gestionvilles.local (Legacy) via
 * toCompat() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Admin extends BaseController
{
    public function getClientsCharts(...$args)
    {
        return $this->toCompat();
    }

}
