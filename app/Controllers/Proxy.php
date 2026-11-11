<?php

namespace App\Controllers;

/**
 * Route catch-all en fin de Routes.php : tout ce qui ne correspond
 * a aucune route CI4 (assets du docroot Legacy, chemins non declares,
 * upload/*, etc.) est relaye a Legacy via le proxy interne.
 */
class Proxy extends BaseController
{
    public function pass()
    {
        return $this->toCompat();
    }
}
