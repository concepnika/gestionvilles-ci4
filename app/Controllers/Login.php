<?php

namespace App\Controllers;

use App\Libraries\CompatSession;
use App\Libraries\WebuserAuth;

/**
 * Authentification unique des usagers bureau (vhost CI4).
 *
 * Toutes les URLs de connexion admin (/login, /admin/connexion,
 * POST /admin/tableau_de_bord) passent ici. Le hachage est delegue
 * a WebuserAuth (identique a Login_model Legacy).
 */
class Login extends BaseController
{
    /**
     * Page de connexion — affiche toujours le formulaire, comme
     * co_gestion/index en Legacy (meme si une session est active).
     */
    public function index()
    {
        return $this->loginForm();
    }

    /**
     * Ancien relais Legacy de GotoHome. Le tableau de bord est natif.
     */
    public function admin()
    {
        if (! (new CompatSession())->isLoggedIn()) {
            return $this->index();
        }

        return redirect()->to('/admin');
    }

    public function attempt()
    {
        $email    = trim((string) $this->request->getPostGet('email'));
        $password = (string) $this->request->getPostGet('password');
        $next     = $this->safeNext((string) $this->request->getPostGet('next'));
        $from     = (string) $this->request->getPostGet('from');

        $auth = new WebuserAuth();
        $user = $auth->verify($email, $password);
        if ($user === null) {
            return $this->loginForm(
                'Courriel ou mot de passe invalide.',
                $email,
                401
            );
        }

        $auth->createSession($user);

        if ($auth->mustChangePassword($user)) {
            return redirect()->to(gv_url() . 'login/change?email=' . rawurlencode($email));
        }

        if ($from === '1') {
            $date = date('Y-m-d');

            return redirect()->to(gv_url("sanivan/routecam/{$date}?idchauffeur=0"));
        }

        return redirect()->to($next === '' ? '/admin' : $next);
    }

    public function logout()
    {
        (new CompatSession())->destroy();

        return redirect()->to('/admin/connexion');
    }

    /**
     * N'accepte que des cibles locales (chemin absolu commencant par
     * un seul '/') pour eviter les redirections ouvertes.
     */
    private function safeNext(string $next): string
    {
        if ($next === '' || $next[0] !== '/' || str_starts_with($next, '//')) {
            return '';
        }

        return $next;
    }

    private function loginForm(string $error = '', string $email = '', int $status = 200)
    {
        helper('gv');

        return $this->response->setStatusCode($status)->setBody(view('partials/login2', [
            'msgerror'    => $error,
            'msgreset'    => '',
            'error'       => $error,
            'reset'       => '',
            'email'       => $email,
            'activeDate'  => date('Y-m-d'),
            'next'        => $this->safeNext((string) $this->request->getGet('next')),
        ]));
    }
}
