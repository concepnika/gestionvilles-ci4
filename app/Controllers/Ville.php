<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_ville.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class Ville extends BaseController
{
    public function index(...$args)
    {
        return $this->toCi3();
    }

    public function login(...$args)
    {
        return $this->toCi3();
    }

    public function logout(...$args)
    {
        return $this->toCi3();
    }

    public function goAccueil(...$args)
    {
        return $this->toCi3();
    }

    public function NavZoneHub(...$args)
    {
        return $this->toCi3();
    }

    public function getRequete(...$args)
    {
        return $this->toCi3();
    }

    public function Reponse(...$args)
    {
        return $this->toCi3();
    }

    public function getListRequetes(...$args)
    {
        return $this->toCi3();
    }

    public function setRepReqVue(...$args)
    {
        return $this->toCi3();
    }

    public function postRequete(...$args)
    {
        return $this->toCi3();
    }

    public function postReponse(...$args)
    {
        return $this->toCi3();
    }

    public function showProfil(...$args)
    {
        return $this->toCi3();
    }

    public function postProfil(...$args)
    {
        return $this->toCi3();
    }

    public function postAdresseRequete(...$args)
    {
        return $this->toCi3();
    }

    public function resetPassW(...$args)
    {
        return $this->toCi3();
    }

    public function ValidOldPassW(...$args)
    {
        return $this->toCi3();
    }

    public function PostresetPassW(...$args)
    {
        return $this->toCi3();
    }

    public function ValidStrongPassW(...$args)
    {
        return $this->toCi3();
    }

    public function sendPassword(...$args)
    {
        return $this->toCi3();
    }

    public function excRestPassw(...$args)
    {
        return $this->toCi3();
    }

    public function upload(...$args)
    {
        return $this->toCi3();
    }

    public function summernoteImage(...$args)
    {
        return $this->toCi3();
    }

    public function getNewReq(...$args)
    {
        return $this->toCi3();
    }

    public function setLanguage(...$args)
    {
        return $this->toCi3();
    }

    public function nav_zone(...$args)
    {
        return $this->toCi3();
    }

    public function zones_api(...$args)
    {
        return $this->toCi3();
    }

    public function saveZone(...$args)
    {
        return $this->toCi3();
    }

    public function save_drawn_polyline(...$args)
    {
        return $this->toCi3();
    }

    public function collecte_points(...$args)
    {
        return $this->toCi3();
    }

    public function createCollecteTag(...$args)
    {
        return $this->toCi3();
    }

    public function respondCollecteTag(...$args)
    {
        return $this->toCi3();
    }

    public function listCollecteTags(...$args)
    {
        return $this->toCi3();
    }

    public function createMapTag(...$args)
    {
        return $this->toCi3();
    }

    public function respondMapTag(...$args)
    {
        return $this->toCi3();
    }

    public function listMapTags(...$args)
    {
        return $this->toCi3();
    }

    public function updateMapTag(...$args)
    {
        return $this->toCi3();
    }

    public function mapTagReasons(...$args)
    {
        return $this->toCi3();
    }

    public function maptag_chauffeurs(...$args)
    {
        return $this->toCi3();
    }

    public function tagsVille(...$args)
    {
        return $this->toCi3();
    }

    public function listAllOpenMapTags(...$args)
    {
        return $this->toCi3();
    }

}
