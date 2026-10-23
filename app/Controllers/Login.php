<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use Config\Database;

/**
 * Authentification propre a l'app CI4 (vhost dedie).
 *
 * Valide les identifiants contre la table webuser partagee avec CI3,
 * puis cree une session au format CI3 (ligne ci_sessions + cookie
 * 'ci_session') via Ci3Session::create(). Le hachage reproduit celui
 * de CI3 : password_verify(hash_hmac('sha256', mdp, cle), pass).
 */
class Login extends BaseController
{
    // Meme cle que Login_model::$keysha cote CI3.
    private const PEPPER = 'nikajhdgsjgdsdssd';

    /**
     * Page de connexion — affiche toujours le formulaire, comme
     * co_gestion/index en CI3 (meme si une session est active).
     */
    public function index()
    {
        helper('ci3view');
        return $this->response->setBody(view('auth/login', [
            'error' => null,
            'reset' => '',
            'email' => '',
            'activeDate' => date('Y-m-d'),
            'next' => $this->safeNext((string) $this->request->getGet('next')),
        ]));
    }

    /**
     * Equivalent de co_gestion/GotoHome : tableau de bord si session
     * active (relaye a CI3, qui rend partials/home), sinon le
     * formulaire de connexion.
     */
    public function admin()
    {
        if (! (new Ci3Session())->isLoggedIn()) {
            return $this->index();
        }
        return $this->toCi3();
    }

    public function attempt()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $next = $this->safeNext((string) $this->request->getPost('next'));

        $user = Database::connect()->table('webuser')
            ->where('email', $email)
            ->get()
            ->getRow();

        $peppered = hash_hmac('sha256', $password, self::PEPPER);
        if (! $user || ! password_verify($peppered, (string) $user->pass)) {
            helper('ci3view');
            return $this->response->setStatusCode(401)->setBody(view('auth/login', [
                'error' => 'Courriel ou mot de passe invalide.',
                'reset' => '',
                'email' => $email,
                'activeDate' => date('Y-m-d'),
                'next' => $next,
            ]));
        }

        (new Ci3Session())->create([
            'email' => $user->email,
            'nom' => trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')),
            'privilege' => $user->privilege ?? null,
            'user_id' => $user->id_user ?? null,
            'login_in' => true,
        ]);

        return redirect()->to($next === '' ? '/admin' : $next);
    }

    public function logout()
    {
        (new Ci3Session())->destroy();
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
}
