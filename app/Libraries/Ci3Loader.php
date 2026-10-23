<?php

namespace App\Libraries;

use Config\Database;

/**
 * Adaptateurs reproduisant l'environnement CI3 dans les controleurs
 * portes : $this->session, $this->input, $this->load, $this->output,
 * $this->email. Instancies par BaseController::initController().
 */

/**
 * $this->load CI3 : model()/library() instancient et assignent une
 * propriete dynamique sur le controleur (AllowDynamicProperties sur
 * BaseController). view() delegue a view() CI4 (echo sauf $return).
 */
class Ci3Loader
{
    private object $ctl;

    public function __construct(object $ctl)
    {
        $this->ctl = $ctl;
    }

    public function model(string $name, ?string $as = null): void
    {
        $base = preg_replace('/_model$/i', '', $name);
        $class = 'App\\Models\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $base))) . 'Model';
        $prop  = $as ?? strtolower($base);
        $this->ctl->{$prop} = class_exists($class) ? new $class() : null;
    }

    public function library(string $name, $config = null): void
    {
        $prop  = strtolower($name);
        $class = 'App\\Libraries\\' . $name;
        $map   = [
            'email'          => Ci3Email::class,
            'upload'         => Ci3Upload::class,
            'libcalendrier'  => LibCalendrier::class,
            'libinscription' => LibInscription::class,
            'session'        => Ci3SessionAdapter::class,
        ];
        $cls = $map[$prop] ?? (class_exists($class) ? $class : null);
        if ($cls === null) {
            return;
        }
        $this->ctl->{$prop === 'session' ? 'session' : $prop} =
            $prop === 'upload' ? new $cls(is_array($config) ? $config : []) : new $cls();
    }

    public function view(string $name, array $data = [], bool $return = false)
    {
        $html = view($name, $data);
        if ($return) {
            return $html;
        }
        echo $html;
        return null;
    }

    public function helper($name): void
    {
        helper($name);
    }

    public function database()
    {
        return Database::connect();
    }

    public function vars($key, $val = null): void
    {
        // no-op : les donnees sont passees explicitement en CI4
    }
}
