<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Action non encore portee : proxy interne vers l'application CI3.
     *
     * Execute la meme requete sur le vhost CI3 (gestionvilles.local)
     * et relaie la reponse. Les URLs gestionvilles.local generees par
     * CI3 (base_url = HTTP_HOST) sont reecrites en gestionvillesci4.local
     * dans les corps HTML/JSON et les Location pour rester sur l'hote
     * CI4 ; le cookie ci_session est relaie et s'applique a cet hote :
     * la session est reellement partagee entre les deux apps.
     */
    protected function toCi3()
    {
        $uri = $this->request->getUri();
        // getPath() peut inclure /index.php/ (rewrite vhost) — on le retire
        $target = 'http://gestionvilles.local' . preg_replace('#^/index\.php#i', '', $uri->getPath());
        if (($query = $uri->getQuery()) !== '') {
            $target .= '?' . $query;
        }

        $headers = [];
        foreach (['Cookie', 'Content-Type', 'Accept', 'Accept-Language',
                  'X-Requested-With', 'Referer', 'User-Agent'] as $h) {
            $header = $this->request->header($h);
            if ($header !== null) {
                $headers[$h] = $header->getValue();
            }
        }

        $options = ['headers' => $headers];
        $body = $this->request->getBody();
        if ($body !== null && $body !== '') {
            $options['body'] = $body;
        }

        $resp = \Config\Services::curlrequest([
            'timeout' => 120,
            'http_errors' => false,
            'allow_redirects' => false,
        ])
            ->request($this->request->getMethod(true), $target, $options);

        $response = $this->response->setStatusCode($resp->getStatusCode());
        $skip = ['transfer-encoding', 'connection', 'content-length',
                 'content-encoding', 'keep-alive'];
        foreach ($resp->getHeaders() as $name => $headers) {
            $ln = strtolower($name);
            if (in_array($ln, $skip, true)) {
                continue;
            }
            // getHeaders() : array<nom, Header|Header[]> ; PAS de cast
            // (array) sur un objet Header (expose ses proprietes).
            $values = [];
            foreach (is_array($headers) ? $headers : [$headers] as $h) {
                if ($h instanceof \CodeIgniter\HTTP\Header) {
                    foreach ((array) $h->getValue() as $v) {
                        $values[] = (string) $v;
                    }
                } else {
                    $values[] = (string) $h;
                }
            }
            foreach ($values as $v) {
                if ($ln === 'set-cookie') {
                    // domain retire : le cookie s'applique a l'hote CI4
                    $response->appendHeader('Set-Cookie', preg_replace('/;\s*domain=[^;]*/i', '', $v));
                } elseif ($ln === 'location') {
                    $response->setHeader($name, str_replace('gestionvilles.local', 'gestionvillesci4.local', $v));
                } elseif ($ln === 'content-type' || $ln === 'content-disposition') {
                    $response->setHeader($name, $v);
                } else {
                    $response->appendHeader($name, $v);
                }
            }
        }
        $bodyOut = $resp->getBody();
        $ctype = $response->getHeaderLine('Content-Type');
        if (preg_match('#text/html|application/json|text/#i', $ctype)) {
            // Garder la navigation sur l'hote CI4 : reecriture des URLs
            // absolues generees par CI3 (base_url = gestionvilles.local).
            $bodyOut = str_replace('gestionvilles.local', 'gestionvillesci4.local', $bodyOut);
        }
        return $response->setBody($bodyOut);
    }
}
