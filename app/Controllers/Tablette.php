<?php

namespace App\Controllers;

/**
 * Squelette du port Legacy -> CI4 de co_tablette.
 * Chaque methode redirige vers gestionvilles.local (Legacy) via
 * toCompat() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Tablette extends BaseController
{
    public function index(...$args)
    {
        return $this->toCompat();
    }

    public function loginChauffeur(...$args)
    {
        return $this->toCompat();
    }

    public function savelocation(...$args)
    {
        return $this->toCompat();
    }

}
