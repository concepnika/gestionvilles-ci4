<?php

namespace App\Libraries;

use Config\Database;

/**
 * Adaptateurs reproduisant l'environnement Legacy dans les controleurs
 * portes : $this->session, $this->input, $this->load, $this->output,
 * $this->email. Instancies par BaseController::initController().
 */

/** $this->output Legacy : set_content_type/set_output/set_header. */
class CompatOutput
{
    public function set_content_type(string $type, ?string $charset = null)
    {
        $ct = str_contains($type, '/') ? $type : 'application/' . $type;
        header('Content-Type: ' . $ct);
        return $this;
    }

    public function set_output(string $output)
    {
        echo $output;
        return $this;
    }

    public function set_header(string $header, bool $replace = true)
    {
        header($header, $replace);
        return $this;
    }

    /** Legacy Output::set_status_header — utilisé par les ports
     *  (ex. Co_villes_admin saveZone/updateZone, Polyline::postPolyline,
     *  Gestion::togglePolylineCedule). NB : passer par la Response CI4
     *  — http_response_code() brut serait écrasé par le statut par
     *  défaut (200) au moment de l'envoi. */
    public function set_status_header(int $code, string $text = '')
    {
        service('response')->setStatusCode($code);
        return $this;
    }
}
