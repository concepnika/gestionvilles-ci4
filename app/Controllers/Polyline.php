<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de polyline et co_polyline.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Polyline extends BaseController
{
    public function save(...$args)
    {
        return $this->toCi3();
    }

    public function posPolyDynamique(...$args)
    {
        return $this->toCi3();
    }

    public function postPolyline(...$args)
    {
        return $this->toCi3();
    }

    // Methodes de co_polyline (CI3)
    public function GetDayRoad(...$args)
    {
        return $this->toCi3();
    }

    public function GetCamionPolyline(...$args)
    {
        return $this->toCi3();
    }

}
