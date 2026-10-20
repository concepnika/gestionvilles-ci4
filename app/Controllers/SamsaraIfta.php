<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_samsara_ifta.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class SamsaraIfta extends BaseController
{
    public function index(...$args)
    {
        return $this->toCi3();
    }

    public function getData(...$args)
    {
        return $this->toCi3();
    }

    public function exportExcel(...$args)
    {
        return $this->toCi3();
    }

}
