<?php

namespace App\Libraries;

use Config\Database;

/**
 * Adaptateurs reproduisant l'environnement CI3 dans les controleurs
 * portes : $this->session, $this->input, $this->load, $this->output,
 * $this->email. Instancies par BaseController::initController().
 */

/** $this->session CI3 : userdata/set_userdata/has_userdata/sess_destroy. */
class Ci3SessionAdapter
{
    private Ci3Session $s;

    public function __construct(?Ci3Session $s = null)
    {
        $this->s = $s ?? new Ci3Session();
    }

    /** Magic : $this->session->email -> userdata('email') */
    public function __get(string $key)
    {
        return $this->s->get($key);
    }

    public function __set(string $key, $value): void
    {
        // ex. sess_expire_on_close — inoffensif sous CI4
    }

    public function userdata(?string $key = null)
    {
        return $key === null ? $this->s->user() : $this->s->get($key);
    }

    public function has_userdata(string $key): bool
    {
        return $this->s->get($key) !== null;
    }

    public function set_userdata($key, $value = null): void
    {
        foreach (is_array($key) ? $key : [$key => $value] as $k => $v) {
            $this->s->set($k, $v);
        }
    }

    public function all_userdata(): array
    {
        return $this->s->user() ?? [];
    }

    public function unset_userdata($keys): void
    {
        $this->s->remove($keys);
    }

    public function sess_destroy(): void
    {
        $this->s->destroy();
    }

    public function sess_create(): void
    {
        // no-op : Ci3Session cree la ligne a create()/login
    }

    public function sess_regenerate(bool $destroy = false): void
    {
        // no-op : la ligne ci_sessions existe deja
    }
}
