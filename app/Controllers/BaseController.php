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

        helper(['ci3view', 'ci3compat', 'url']);
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
        // Cible du proxy :
        // - local (ci3.proxyBase absent) : 127.0.0.1 + Host
        //   gestionvilles.local — l'IP source est TOUJOURS 127.0.0.1,
        //   indispensable car CI3 a sess_match_ip = true (le hosts a a
        //   la fois ::1 et 127.0.0.1 ; en IPv6 la session serait
        //   invalidee a chaque bascule).
        // - dev/prod (ci3.proxyBase dans .env) : URL complete de l'app
        //   CI3, ex. https://robert-daoust.ca — le Host est derive de
        //   l'URL par cURL (sess_match_ip ne peut pas etre satisfait a
        //   distance ; sans impact car seuls les assets publics et les
        //   routes mortes passent par le proxy).
        $proxyBase = rtrim((string) env('ci3.proxyBase'), '/');
        $localMode = $proxyBase === '';
        $ci3Host   = $localMode
            ? 'gestionvilles.local'
            : (parse_url($proxyBase, PHP_URL_HOST) ?: 'gestionvilles.local');
        $ourHost   = $uri->getHost();

        $target = ($localMode ? 'http://127.0.0.1' : $proxyBase)
            . preg_replace('#^/index\.php#i', '', $uri->getPath());
        if (($query = $uri->getQuery()) !== '') {
            $target .= '?' . $query;
        }

        // Reecrit les URLs absolues generees par CI3 vers notre hote :
        // www.x d'abord, puis x avec lookbehind pour ne pas retoucher
        // les URLs deja sur notre hote (ex. dev.robert-daoust.ca).
        $fixHost = static function (string $s) use ($ci3Host, $ourHost): string {
            if ($ci3Host === $ourHost) {
                return $s;
            }
            $s = str_replace('www.' . $ci3Host, $ourHost, $s);
            return preg_replace('#(?<![\w.-])' . preg_quote($ci3Host, '#') . '#', $ourHost, $s);
        };

        $headers = [];
        if ($localMode) {
            $headers['Host'] = 'gestionvilles.local';
        }
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
                    $response->setHeader($name, $fixHost($v));
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
            // absolues generees par CI3 (base_url = hote CI3).
            $bodyOut = $fixHost($bodyOut);
        }
        return $response->setBody($bodyOut);
    }
}
