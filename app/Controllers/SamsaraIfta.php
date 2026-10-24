<?php

namespace App\Controllers;

use App\Libraries\SamsaraClient;

/**
 * Port CI3 -> CI4 de Co_samsara_ifta : rapport IFTA (kilométrage
 * Ontario/Québec par camion via l'API Samsara).
 *
 * index + getData portés nativement (SamsaraClient::getKmByCamion —
 * port de SamsaraLib). exportExcel reste RELAYÉ à CI3 : il utilise
 * PHPExcel, incompatible PHP 8 — la route plante en 500 des deux
 * côtés (voir AGENTS.md « Reste à faire » : PhpSpreadsheet).
 */
class SamsaraIfta extends BaseController
{
    public function index()
    {
        $data = [];
        $data['iftaYear'] = date('Y');
        $data['iftaMonth'] = (int) date('n');
        // CI3 : $this->config->item('apiKey', 'samsara')
        $data['isConfigured'] = ! empty(env('samsara.apiKey'));
        echo view('samsara/ontario_km', $data);
    }

    public function getData()
    {
        $year  = (int) ($this->input->post_get('year') ?: date('Y'));
        $month = (int) ($this->input->post_get('month') ?: date('n'));
        $jurisdiction = $this->input->post_get('jurisdiction') ?: 'ON';

        $startDate = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
        $endDate   = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

        if (empty(env('samsara.apiKey'))) {
            echo json_encode(array('error' => 'Token API Samsara non configuré.'));
            return;
        }

        $samsaralib = new SamsaraClient();
        $rows = $samsaralib->getKmByCamion($startDate, $endDate, $jurisdiction);
        if (isset($rows['error'])) {
            echo json_encode(array('error' => $rows['error']));
            return;
        }

        $totalKm = 0;
        $taxableKm = 0;
        foreach ($rows as $r) {
            $totalKm += $r->totalKm;
            $taxableKm += $r->taxableKm;
        }

        $monthNames = array(
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        );

        $summary = (object) array(
            'totalKm' => $totalKm,
            'taxableKm' => $taxableKm,
            'vehicleCount' => count($rows),
        );

        echo json_encode(array(
            'rows' => $rows,
            'summary' => $summary,
            'period' => $monthNames[$month] . ' ' . $year,
        ));
    }

    public function exportExcel(...$args)
    {
        return $this->toCi3();
    }
}
