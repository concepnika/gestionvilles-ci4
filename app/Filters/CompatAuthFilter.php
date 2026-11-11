<?php

namespace App\Filters;

use App\Libraries\CompatSession;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Exige une session bureau Legacy active (cookie ci_session).
 * Sinon, redirige vers /login?next=<uri>.
 *
 * Quelques chemins admin sont publics (formulaire / POST de connexion)
 * et quelques POST sanivan appartiennent a l'API tablette, pas au
 * calendrier bureau — ils sont ignores ici.
 */
class CompatAuthFilter implements FilterInterface
{
    /** @var list<string> Chemins exacts ou prefixes a ne pas filtrer. */
    private array $skip = [
        'admin/connexion',
        'admin/tableau_de_bord',
        'sanivan/post/depot',
        'sanivan/post/camions',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $path = ltrim($request->getUri()->getPath(), '/');
        $path = preg_replace('#^index\.php/#i', '', $path) ?? $path;

        if ($this->isSkipped($path)) {
            return;
        }

        $compatSession = new CompatSession();
        if (! $compatSession->isLoggedIn()) {
            return redirect()->to('/login?next=' . urlencode('/' . $path));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function isSkipped(string $path): bool
    {
        foreach ($this->skip as $skip) {
            if ($path === $skip || str_starts_with($path, $skip . '/')) {
                return true;
            }
        }

        return false;
    }
}
