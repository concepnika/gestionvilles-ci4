<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_web_chauffeur.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class WebChauffeur extends BaseController
{
    public function login(...$args)
    {
        return $this->toCi3();
    }

    public function doLogin(...$args)
    {
        return $this->toCi3();
    }

    public function logout(...$args)
    {
        return $this->toCi3();
    }

    public function dashboard(...$args)
    {
        return $this->toCi3();
    }

    public function changeCedule(...$args)
    {
        return $this->toCi3();
    }

    public function truckmap(...$args)
    {
        return $this->toCi3();
    }

    public function truckmap_embed(...$args)
    {
        return $this->toCi3();
    }

    public function villes(...$args)
    {
        return $this->toCi3();
    }

    public function api_getmaxkilo(...$args)
    {
        return $this->toCi3();
    }

    public function api_samsara_location(...$args)
    {
        return $this->toCi3();
    }

    public function api_samsara_history(...$args)
    {
        return $this->toCi3();
    }

    public function api_samsara_vehicles(...$args)
    {
        return $this->toCi3();
    }

    public function api_polyline(...$args)
    {
        return $this->toCi3();
    }

    public function api_tags(...$args)
    {
        return $this->toCi3();
    }

    public function api_camions_road(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_camion(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_chauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_aide(...$args)
    {
        return $this->toCi3();
    }

    public function api_list_camions(...$args)
    {
        return $this->toCi3();
    }

    public function api_list_chauffeurs(...$args)
    {
        return $this->toCi3();
    }

    public function api_change_camion(...$args)
    {
        return $this->toCi3();
    }

    public function api_change_chauffeur(...$args)
    {
        return $this->toCi3();
    }

    public function api_type_extras(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_ville(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_all_ville(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_depot(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_depotoire(...$args)
    {
        return $this->toCi3();
    }

    public function api_post_all_depot(...$args)
    {
        return $this->toCi3();
    }

    public function api_add_ville(...$args)
    {
        return $this->toCi3();
    }

    public function api_reload_villes(...$args)
    {
        return $this->toCi3();
    }

    public function api_upload_ticket(...$args)
    {
        return $this->toCi3();
    }

    public function api_change_cedule(...$args)
    {
        return $this->toCi3();
    }

    public function api_zone_toggle(...$args)
    {
        return $this->toCi3();
    }

    public function api_inzone_history(...$args)
    {
        return $this->toCi3();
    }

    public function api_tag_badge(...$args)
    {
        return $this->toCi3();
    }

    public function showAidePourWEB(...$args)
    {
        return $this->toCi3();
    }

    public function cron_gps_poll(...$args)
    {
        return $this->toCi3();
    }

}
