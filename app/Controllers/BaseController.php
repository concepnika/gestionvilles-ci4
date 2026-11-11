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
     * Adaptateurs Legacy pour les methodes portees mecaniquement :
     * $this->db / input / session / load / output / email / upload.
     * Les proprietes dynamiques (load->model('x','y')) sont permises.
     */

    /** @var \CodeIgniter\Database\BaseConnection equiv. $this->db Legacy */
    protected $db;

    /** @var \App\Libraries\CompatInput */
    protected $input;

    /** @var \App\Libraries\CompatSessionAdapter */
    protected $session;

    /** @var \App\Libraries\CompatLoader */
    protected $load;

    /** @var \App\Libraries\CompatOutput */
    protected $output;

    /** @var \App\Libraries\CompatSession pont direct (idem modeles) */
    protected $gv_session;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        helper(['gv', 'compat', 'url']);
        $this->db         = new \App\Libraries\CompatDb();
        $this->gv_session = new \App\Libraries\CompatSession();
        $this->session    = new \App\Libraries\CompatSessionAdapter($this->gv_session);
        $this->input      = new \App\Libraries\CompatInput();
        $this->load       = new \App\Libraries\CompatLoader($this);
        $this->output     = new \App\Libraries\CompatOutput();

        // Equiv. du __construct de co_gestion : langue par defaut
        if (! $this->session->has_userdata('site_lang')) {
            $this->session->set_userdata('site_lang', 'fr');
        }
    }

    /**
     * Action non encore portee : proxy interne vers l'application Legacy.
     *
     * Execute la meme requete sur le vhost Legacy (gestionvilles.local)
     * et relaie la reponse. Les URLs gestionvilles.local generees par
     * Legacy (base_url = HTTP_HOST) sont reecrites en gestionvillesci4.local
     * dans les corps HTML/JSON et les Location pour rester sur l'hote
     * CI4 ; le cookie ci_session est relaie et s'applique a cet hote :
     * la session est reellement partagee entre les deux apps.
     */
    protected function toCompat()
    {
        $uri = $this->request->getUri();
        // Cible du proxy :
        // - local (legacy.proxyBase absent) : 127.0.0.1 + Host
        //   gestionvilles.local — l'IP source est TOUJOURS 127.0.0.1,
        //   indispensable car Legacy a sess_match_ip = true (le hosts a a
        //   la fois ::1 et 127.0.0.1 ; en IPv6 la session serait
        //   invalidee a chaque bascule).
        // - dev/prod (legacy.proxyBase dans .env) : URL complete de l'app
        //   Legacy, ex. https://robert-daoust.ca — le Host est derive de
        //   l'URL par cURL (sess_match_ip ne peut pas etre satisfait a
        //   distance ; sans impact car seuls les assets publics et les
        //   routes mortes passent par le proxy).
        // 1) Fichier statique present sous la racine fichiers
        //    (legacy.webroot, defaut <projet>/appfiles) → servi directement.
        //    Couvre assets/*, upload/*, uploads/*, userfiles/*, icons/*...
        $served = $this->serveWebrootFile($uri->getPath());
        if ($served !== null) {
            return $served;
        }

        $proxyBase = rtrim((string) env('legacy.proxyBase'), '/');
        $localMode = $proxyBase === '';
        $proxyOff  = in_array(strtolower($proxyBase), ['off', 'none', 'disabled'], true);
        $compatHost   = $localMode
            ? 'gestionvilles.local'
            : (parse_url($proxyBase, PHP_URL_HOST) ?: 'gestionvilles.local');
        $ourHost   = $uri->getHost();

        // 2) Proxy desactive (legacy.proxyBase = 'off') : l'app Legacy n'existe
        //    pas/plus — les routes mortes et chemins inconnus rendent le
        //    meme 404 que Legacy.
        if ($proxyOff) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $target = ($localMode ? 'http://127.0.0.1' : $proxyBase)
            . preg_replace('#^/index\.php#i', '', $uri->getPath());
        if (($query = $uri->getQuery()) !== '') {
            $target .= '?' . $query;
        }

        // Reecrit les URLs absolues generees par Legacy vers notre hote :
        // www.x d'abord, puis x avec lookbehind pour ne pas retoucher
        // les URLs deja sur notre hote (ex. dev.robert-daoust.ca).
        $fixHost = static function (string $s) use ($compatHost, $ourHost): string {
            if ($compatHost === $ourHost) {
                return $s;
            }
            $s = str_replace('www.' . $compatHost, $ourHost, $s);
            return preg_replace('#(?<![\w.-])' . preg_quote($compatHost, '#') . '#', $ourHost, $s);
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
            // absolues generees par Legacy (base_url = hote Legacy).
            $bodyOut = $fixHost($bodyOut);
        }
        return $response->setBody($bodyOut);
    }

    /**
     * Sert un fichier statique depuis la racine fichiers
     * (legacy.webroot, defaut <projet>/appfiles). Retourne null si le
     * chemin ne correspond pas a un fichier servissable — le proxy ou
     * le 404 prennent le relais.
     *
     * Extensions en liste blanche uniquement (statiques/telecharges) :
     * un .php sous le webroot n'est JAMAIS servi (ni execute ni expose
     * en source — contrairement au proxy qui le ferait executer par Legacy).
     */
    private const WEBROOT_SERVE_EXT = [
        'css', 'js', 'map', 'json', 'xml', 'txt', 'csv', 'svg',
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'ico', 'bmp',
        'woff', 'woff2', 'ttf', 'eot', 'otf',
        'pdf', 'xlsx', 'xls', 'doc', 'docx', 'ppt', 'pptx',
        'zip', 'gpx', 'mp3', 'mp4', 'webm', 'wav', 'ogg', 'html', 'htm',
    ];

    private function serveWebrootFile(string $uriPath): ?\CodeIgniter\HTTP\Response
    {
        $rel = ltrim(rawurldecode($uriPath), '/');
        if ($rel === '' || strpos($rel, "\0") !== false) {
            return null;
        }
        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        if (! in_array($ext, self::WEBROOT_SERVE_EXT, true)) {
            return null;
        }

        $webroot = rtrim((string) env('legacy.webroot', ROOTPATH . 'appfiles'), '/\\');
        $base    = realpath($webroot);
        if ($base === false) {
            return null;
        }
        $full = realpath($base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel));
        // Containment : le chemin resolu doit rester sous la racine.
        if ($full === false || ! is_file($full)
            || strncmp($full, $base . DIRECTORY_SEPARATOR, strlen($base) + 1) !== 0) {
            return null;
        }

        $mime = \Config\Mimes::guessTypeFromExtension($ext) ?: 'application/octet-stream';
        return $this->response
            ->setStatusCode(200)
            ->setContentType($mime)
            ->setHeader('Content-Length', (string) filesize($full))
            ->setHeader('Cache-Control', 'public, max-age=300')
            ->setBody(file_get_contents($full));
    }
}
