<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $ci3  = new Ci3Session();
        $user = $ci3->user();

        return $this->response->setBody(view('status', [
            'loggedIn'  => $ci3->isLoggedIn(),
            'nom'       => $user['nom'] ?? '',
            'email'     => $user['email'] ?? '',
            'privilege' => $user['privilege'] ?? '',
        ]));
    }

    public function whoami(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON((new Ci3Session())->user());
    }
}
