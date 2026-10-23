<?php

namespace App\Libraries;

use Config\Database;

/**
 * Adaptateurs reproduisant l'environnement CI3 dans les controleurs
 * portes : $this->session, $this->input, $this->load, $this->output,
 * $this->email. Instancies par BaseController::initController().
 */

/**
 * $this->email CI3 : wrapper autour du service email CI4
 * (API quasi identique : from/to/subject/message/send/attach).
 */
class Ci3Email
{
    private $email;

    public function __construct(?array $config = null)
    {
        $this->email = \Config\Services::email($config ?? null, false);
    }

    public function initialize(array $config = []): bool
    {
        $this->email->initialize($config);
        return true;
    }

    public function from(string $from, string $name = '', ?string $return = null)
    {
        $this->email->setFrom($from, $name, $return);
        return $this;
    }

    public function to($to) { $this->email->setTo($to); return $this; }
    public function cc($cc) { $this->email->setCC($cc); return $this; }
    public function bcc($bcc) { $this->email->setBCC($bcc); return $this; }
    public function reply_to($to) { $this->email->setReplyTo($to); return $this; }
    public function subject(string $s) { $this->email->setSubject($s); return $this; }
    public function message(string $s) { $this->email->setMessage($s); return $this; }
    public function attach(string $f) { $this->email->attach($f); return $this; }
    public function set_mailtype(string $t) { $this->email->setMailType($t); return $this; }

    public function send(bool $autoClear = true): bool
    {
        return $this->email->send($autoClear);
    }

    public function clear(bool $attach = false) { $this->email->clear($attach); return $this; }

    public function print_debugger($include = ['headers', 'subject', 'body'])
    {
        return $this->email->printDebugger($include);
    }

    public function __call(string $m, array $args)
    {
        // relais des autres methodes (noms identiques ou camelCase)
        $alt = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $m))));
        foreach ([$m, $alt] as $try) {
            if (method_exists($this->email, $try)) {
                $this->email->{$try}(...$args);
                return $this;
            }
        }
        return $this;
    }
}
