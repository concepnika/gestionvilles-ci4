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
#[\AllowDynamicProperties]
abstract class BaseController extends Controller
{
    /**
     * Adaptateurs CI3 pour les methodes portees mecaniquement :
     * $this->db / input / session / load / output / email / upload.
     * Les proprietes dynamiques (load->model('x','y')) sont permises.
     */

    /** @var \CodeIgniter\Database\BaseConnection equiv. $this->db CI3 */
    protected $db;

    /** @var \App\Libraries\Ci3Input */
    protected $input;

    /** @var \App\Libraries\Ci3SessionAdapter */
    protected $session;

    /** @var \App\Libraries\Ci3Loader */
    protected $load;

    /** @var \App\Libraries\Ci3Output */
    protected $output;

    /** @var \App\Libraries\Ci3Session pont direct (idem modeles) */
    protected $ci3session;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        helper(['ci3view', 'url']);
        $this->db         = new \App\Libraries\Ci3Db();
        $this->ci3session = new \App\Libraries\Ci3Session();
        $this->session    = new \App\Libraries\Ci3SessionAdapter($this->ci3session);
        $this->input      = new \App\Libraries\Ci3Input();
        $this->load       = new \App\Libraries\Ci3Loader($this);
        $this->output     = new \App\Libraries\Ci3Output();

        // Equiv. du __construct de co_gestion : langue par defaut
        if (! $this->session->has_userdata('site_lang')) {
            $this->session->set_userdata('site_lang', 'fr');
        }
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
        // 127.0.0.1 + Host: gestionvilles.local — l'IP source est
        // TOUJOURS 127.0.0.1 : indispensable car CI3 a sess_match_ip
        // = true (le hosts a a la fois ::1 et 127.0.0.1 ; en IPv6 la
        // session serait invalidee a chaque bascule).
        $target = 'http://127.0.0.1' . preg_replace('#^/index\.php#i', '', $uri->getPath());
        if (($query = $uri->getQuery()) !== '') {
            $target .= '?' . $query;
        }

        $headers = ['Host' => 'gestionvilles.local'];
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
