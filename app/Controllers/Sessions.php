<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_sessions.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Sessions extends BaseController
{
    public function chekSession(...$args)
    {
        return $this->toCi3();
    }

    public function lockScreen(...$args)
    {
        return $this->toCi3();
    }

}
