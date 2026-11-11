<?php

use App\Libraries\CompatSession;

/**
 * Helpers de compatibilité pour les vues portées de Legacy.
 *
 * Les vues Legacy appellent $this->session->userdata(), base_url() et
 * $this->lang->line() — remplacés mécaniquement lors du port par ces
 * fonctions. Les URLs générées restent root-absolues (servies par Legacy
 * à la racine tant que le strangler est actif).
 */

if (! function_exists('gv_sess')) {
    /**
     * Équivalent de $this->session->userdata($key) — lit la session Legacy
     * partagée (cookie ci_session → table ci_sessions).
     */
    function gv_sess(string $key)
    {
        static $sess = null;
        if ($sess === null) {
            $sess = new CompatSession();
        }
        return $sess->get($key);
    }
}

if (! function_exists('gv_url')) {
    /**
     * Équivalent de base_url() des vues Legacy : retourne un chemin
     * root-absolu. Les assets et liens internes continuent d'être
     * servis par Legacy (ou par CI4 une fois à la racine).
     */
    function gv_url(string $path = ''): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (! function_exists('gv_set')) {
    /**
     * Équivalent de $this->session->set_userdata($key, $value).
     */
    function gv_set(string $key, $value): void
    {
        (new CompatSession())->set($key, $value);
    }
}

if (! function_exists('gv_unset')) {
    /**
     * Équivalent de unset($_SESSION[$key]) / session->unset_userdata().
     */
    function gv_unset(string $key): void
    {
        (new CompatSession())->remove($key);
    }
}

if (! function_exists('gv_in')) {
    /**
     * Équivalent de $this->input->post_get($key) dans les vues Legacy.
     */
    function gv_in(string $key)
    {
        return service('request')->getPostGet($key);
    }
}

if (! function_exists('gv_lang')) {
    /**
     * Équivalent de $this->lang->line($key). Legacy n'a pas de fichiers
     * de langue pour ces clés (elles s'affichaient vides) — on met
     * le libellé français attendu par les tablettes.
     */
    function gv_lang(string $key): string
    {
        static $map = [
            'login_email' => 'Courriel',
            'login_password' => 'Mot de passe',
            'login_connect' => 'Connexion',
            'login_forget' => 'Mot de passe oublié?',
            'inscription_adresse_courriel' => 'Adresse courriel',
        ];
        return $map[$key] ?? '';
    }
}
