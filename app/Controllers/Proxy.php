<?php

namespace App\Controllers;

/**
 * Route catch-all en fin de Routes.php : tout ce qui ne correspond
 * a aucune route CI4 (assets du docroot CI3, chemins non declares,
 * upload/*, etc.) est relaye a CI3 via le proxy interne.
 */
class Proxy extends BaseController
{
    public function pass()
    {
        return $this->toCi3();
    }
}
