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

if (! function_exists('ci3lang')) {
    /**
     * Équivalent de $this->lang->line($key). CI3 chargeait
     * application/language/{french|english} selon site_lang — fichiers
     * présents sur la PROD mais absents de la copie locale CI3.
     *
     * Les clés connues sont stockées par langue ['fr' =>, 'en' =>] ;
     * une valeur scalaire reste valide pour toutes les langues.
     * Les clés absentes retournent '' (comme CI3 sans fichier).
     */
    function ci3lang(string $key): string
    {
        static $map = [
            // ─── Page de connexion (admin + citoyen) ───
            'login_email' => 'Courriel',
            'login_password' => 'Mot de passe',
            'login_connect' => 'Connexion',
            'login_forget' => 'Mot de passe oublié?',
            'inscription_adresse_courriel' => 'Adresse courriel',
            // ─── Menu citoyen (inc_menu_2) — la bascule affiche la
            //     langue CIBLE (en mode en : « FR ») ───
            'mne_profil' => ['fr' => 'Votre profil', 'en' => 'Your profile'],
            'mne_quitter' => ['fr' => 'Quitter', 'en' => 'Quit'],
            'inc_menu_langue' => ['fr' => 'EN', 'en' => 'FR'],
            'menu_link_2' => ['fr' => 'en', 'en' => 'fr'],
            'localisation' => ['fr' => 'Localisation', 'en' => 'Location'],
        ];

        if (! array_key_exists($key, $map)) {
            return '';
        }

        $val = $map[$key];
        if (is_array($val)) {
            $lang = (string) (ci3sess('site_lang') ?: 'fr');
            return (string) ($val[$lang] ?? $val['fr']);
        }
        return (string) $val;
    }
}
