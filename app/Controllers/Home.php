<?php

namespace App\Controllers;

use App\Libraries\CompatSession;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $compatSession  = new CompatSession();
        $user = $compatSession->user();

        return $this->response->setBody(view('status', [
            'loggedIn'  => $compatSession->isLoggedIn(),
            'nom'       => $user['nom'] ?? '',
            'email'     => $user['email'] ?? '',
            'privilege' => $user['privilege'] ?? '',
        ]));
    }

    public function whoami(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON((new CompatSession())->user());
    }
}
