<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_tablette.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Tablette extends BaseController
{
    public function index(...$args)
    {
        return $this->toCi3();
    }

    public function loginChauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function savelocation(...$args)
    {
        return $this->toCi3();
    }

}
