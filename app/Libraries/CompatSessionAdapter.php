<?php

namespace App\Libraries;

use Config\Database;

/**
 * Adaptateurs reproduisant l'environnement Legacy dans les controleurs
 * portes : $this->session, $this->input, $this->load, $this->output,
 * $this->email. Instancies par BaseController::initController().
 */

/** $this->session Legacy : userdata/set_userdata/has_userdata/sess_destroy. */
class CompatSessionAdapter
{
    private CompatSession $s;

    public function __construct(?CompatSession $s = null)
    {
        $this->s = $s ?? new CompatSession();
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
        // Legacy auto-cree la session des la premiere requete (cookie
        // ci_session sur tout visiteur) : set_userdata n'y echoue
        // jamais. Sur le vhost CI4 dedie, le cookie Legacy n'arrive pas
        // — sans ceci, set() serait un no-op silencieux et le login
        // ne persisterait pas (pattern ensureSession, cf. AGENTS.md).
        if ($this->s->user() === null) {
            $this->s->create([]);
        }
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
        // no-op : CompatSession cree la ligne a create()/login
    }

    /**
     * Flashdata Legacy (message one-shot) : emulation par cle de session
     * prefixee '__flash_' consommee a la lecture (meme pattern que le
     * port WebChauffeur, cf. AGENTS.md).
     */
    public function set_flashdata(string $key, $value): void
    {
        $this->set_userdata('__flash_' . $key, $value);
    }

    public function flashdata(?string $key = null)
    {
        if ($key === null) {
            return null;
        }
        $v = $this->s->get('__flash_' . $key);
        $this->s->remove('__flash_' . $key);
        return $v;
    }

    public function keep_flashdata(string $key): void
    {
        // no-op : la cle n'est consommee qu'a la lecture
    }

    public function sess_regenerate(bool $destroy = false): void
    {
        // no-op : la ligne ci_sessions existe deja
    }
}
