<?php

namespace App\Filters;

use App\Libraries\Ci3Session;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Exige une session CI3 active (meme usager que l'application
 * principale). Sinon, redirige vers la page de login CI3 a la racine
 * du site — l'app CI4 vit dans un sous-chemin du meme hote.
 */
class Ci3AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ci3 = new Ci3Session();
        if (! $ci3->isLoggedIn()) {
            $uri = $request->getUri();
            return redirect()->to(
                $uri->getScheme() . '://' . $uri->getHost() . '/'
            );
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
