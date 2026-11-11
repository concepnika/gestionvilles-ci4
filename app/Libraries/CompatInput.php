<?php

namespace App\Libraries;

use Config\Database;

/**
 * Adaptateurs reproduisant l'environnement Legacy dans les controleurs
 * portes : $this->session, $this->input, $this->load, $this->output,
 * $this->email. Instancies par BaseController::initController().
 */

/** $this->input Legacy : post_get/get_post/get/post/server/cookie/ip_address. */
class CompatInput
{
    public function post_get(?string $key = null)
    {
        return service('request')->getPostGet($key);
    }

    public function get_post(?string $key = null)
    {
        return service('request')->getGetPost($key);
    }

    public function get(?string $key = null, $xss = null)
    {
        $r = service('request');
        return $key === null ? $r->getGet() : $r->getGet($key);
    }

    public function post(?string $key = null, $xss = null)
    {
        $r = service('request');
        return $key === null ? $r->getPost() : $r->getPost($key);
    }

    public function server(string $key)
    {
        return service('request')->getServer($key);
    }

    public function cookie(string $key)
    {
        return $_COOKIE[$key] ?? null;
    }

    public function ip_address(): string
    {
        return service('request')->getIPAddress() ?? '';
    }

    public function method(bool $upper = false): string
    {
        $m = service('request')->getMethod();
        return $upper ? strtoupper($m) : strtolower($m);
    }
}
