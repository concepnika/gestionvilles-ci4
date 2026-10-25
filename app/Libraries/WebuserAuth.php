<?php

namespace App\Libraries;

use Config\Database;

/**
 * Authentification unique des usagers bureau (table webuser).
 *
 * Remplace les deux chemins paralleles Login::attempt et
 * Gestion::login / LoginModel::validate_user. Le hachage est celui
 * de CI3 : password_verify(hash_hmac('sha256', mdp, pepper), pass).
 */
class WebuserAuth
{
    public const DEFAULT_PEPPER = 'nikajhdgsjgdsdssd';

    public function pepper(): string
    {
        return (string) env('auth.webuserPepper', self::DEFAULT_PEPPER);
    }

    /**
     * @return object|null Ligne webuser, ou null si identifiants invalides.
     */
    public function verify(string $email, string $password): ?object
    {
        $email = trim($email);
        if ($email === '' || $password === '') {
            return null;
        }

        $user = Database::connect()->table('webuser')
            ->where('email', $email)
            ->get()
            ->getRow();

        if (! $user) {
            return null;
        }

        $peppered = hash_hmac('sha256', $password, $this->pepper());
        if (! password_verify($peppered, (string) $user->pass)) {
            return null;
        }

        return $user;
    }

    /**
     * Ouvre une session CI3 (ligne ci_sessions + cookie) comme
     * co_gestion/login : email, nom, privilege, user_id, login_in, code.
     */
    public function createSession(object $user): void
    {
        $email = (string) ($user->email ?? '');
        (new Ci3Session())->create([
            'email'      => $email,
            'nom'        => trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')),
            'privilege'  => $user->privilege ?? null,
            'user_id'    => $user->id_user ?? null,
            'login_in'   => true,
            'code'       => $email,
        ]);
    }

    public function mustChangePassword(object $user): bool
    {
        $raw = $user->forcechange ?? 0;
        if (is_string($raw) && strlen($raw) === 1 && ! ctype_digit($raw)) {
            $raw = ord($raw);
        }

        return (int) $raw === 1;
    }
}
