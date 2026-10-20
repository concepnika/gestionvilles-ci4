<?php

namespace App\Controllers;

/**
 * Squelette du port CI3 -> CI4 de co_admin_tags.
 * Chaque methode redirige vers gestionvilles.local (CI3) via
 * toCi3() tant qu'elle n'est pas portee — voir MIGRATION.md.
 */
class AdminTags extends BaseController
{
    public function tags(...$args)
    {
        return $this->toCi3();
    }

    public function delete_tag(...$args)
    {
        return $this->toCi3();
    }

    public function reasons(...$args)
    {
        return $this->toCi3();
    }

    public function reasons_save(...$args)
    {
        return $this->toCi3();
    }

    public function reasons_delete(...$args)
    {
        return $this->toCi3();
    }

}
