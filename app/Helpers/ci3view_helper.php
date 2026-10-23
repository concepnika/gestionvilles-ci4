<?php

use App\Libraries\Ci3Session;

/**
 * Helpers de compatibilité pour les vues portées de CI3.
 *
 * Les vues CI3 appellent $this->session->userdata(), base_url() et
 * $this->lang->line() — remplacés mécaniquement lors du port par ces
 * fonctions. Les URLs générées restent root-absolues (servies par CI3
 * à la racine tant que le strangler est actif).
 */

if (! function_exists('ci3sess')) {
    /**
     * Équivalent de $this->session->userdata($key) — lit la session CI3
     * partagée (cookie ci_session → table ci_sessions).
     */
    function ci3sess(string $key)
    {
        static $sess = null;
        if ($sess === null) {
            $sess = new Ci3Session();
        }
        return $sess->get($key);
    }
}

if (! function_exists('ci3_url')) {
    /**
     * Équivalent de base_url() des vues CI3 : retourne un chemin
     * root-absolu. Les assets et liens internes continuent d'être
     * servis par CI3 (ou par CI4 une fois à la racine).
     */
    function ci3_url(string $path = ''): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (! function_exists('ci3set')) {
    /**
     * Équivalent de $this->session->set_userdata($key, $value).
     */
    function ci3set(string $key, $value): void
    {
        (new Ci3Session())->set($key, $value);
    }
}

if (! function_exists('ci3in')) {
    /**
     * Équivalent de $this->input->post_get($key) dans les vues CI3.
     */
    function ci3in(string $key)
    {
        return service('request')->getPostGet($key);
    }
}

if (! function_exists('ci3lang')) {
    /**
     * Équivalent de $this->lang->line($key). CI3 n'a pas de fichiers
     * de langue pour ces clés (elles s'affichaient vides) — on met
     * le libellé français attendu par les tablettes.
     */
    function ci3lang(string $key): string
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
