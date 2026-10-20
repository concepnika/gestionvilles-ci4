<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $ci3 = new Ci3Session();
        $user = $ci3->user();

        $status = [
            'app' => 'GestionVilles — CI4 (strangler)',
            'ci3_session' => $ci3->isLoggedIn() ? 'active' : 'absente ou expiree',
            'user' => $user ? [
                'email' => $user['email'] ?? null,
                'nom' => $user['nom'] ?? null,
                'privilege' => $user['privilege'] ?? null,
            ] : null,
            'endpoints' => [
                '/whoami' => 'session CI3 decodee (JSON)',
                '/cedules/{AAAA-MM-JJ}' => 'cedule du jour, lecture seule (JSON)',
            ],
        ];

        return $this->response->setJSON($status);
    }

    public function whoami(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON((new Ci3Session())->user());
    }
}
