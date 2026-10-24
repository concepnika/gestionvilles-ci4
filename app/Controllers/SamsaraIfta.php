<?php

namespace App\Controllers;

use App\Libraries\SamsaraClient;

/**
 * Port CI3 -> CI4 de Co_samsara_ifta : rapport IFTA (kilométrage
 * Ontario/Québec par camion via l'API Samsara).
 *
 * index + getData portés nativement (SamsaraClient::getKmByCamion —
 * port de SamsaraLib). exportExcel porté nativement sur
 * PhpSpreadsheet (PHPExcel est incompatible PHP 8, la route
 * plantait en 500 côté CI3 sous PHP >= 8).
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

    public function exportExcel()
    {
        $year  = (int) ($this->input->get('year') ?: date('Y'));
        $month = (int) ($this->input->get('month') ?: date('n'));

        $startDate = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
        $endDate   = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

        $jurisLabels = array('ON' => 'Ontario', 'QC' => 'Québec');

        $monthNames = array(
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        );
        $periodLabel = $monthNames[$month] . ' ' . $year;

        if (empty(env('samsara.apiKey'))) {
            echo 'Token API Samsara non configuré.';
            return;
        }

        // Générer un fichier Excel avec PhpSpreadsheet
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $samsaralib = new SamsaraClient();
        $jurisdictions = array('ON', 'QC');
        $sheetIndex = 0;

        foreach ($jurisdictions as $juris) {
            $jurisLabel = isset($jurisLabels[$juris]) ? $jurisLabels[$juris] : $juris;

            $rows = $samsaralib->getKmByCamion($startDate, $endDate, $juris);
            if (isset($rows['error'])) {
                continue;
            }

            if ($sheetIndex == 0) {
                $sheet = $objPHPExcel->getActiveSheet();
            } else {
                $sheet = $objPHPExcel->createSheet($sheetIndex);
            }
            $sheet->setTitle('Km ' . $jurisLabel);

            $totalKm = 0;
            $taxableKm = 0;
            foreach ($rows as $r) {
                $totalKm += $r->totalKm;
                $taxableKm += $r->taxableKm;
            }

            // Titre
            $sheet->mergeCells('A1:E1');
            $sheet->setCellValue('A1', 'Kilomètres parcourus au ' . $jurisLabel . ' - ' . $periodLabel);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('0B3B70');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension(1)->setRowHeight(28);

            // En-têtes
            $headers = array('Camion', 'Nom véhicule Samsara', 'Km total ' . $jurisLabel, 'Km taxable ' . $jurisLabel, 'Juridiction');
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '2', $h);
                $sheet->getStyle($col . '2')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle($col . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0B3B70');
                $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col . '2')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $col++;
            }
            $sheet->getRowDimension(2)->setRowHeight(22);

            // Données
            $row = 3;
            foreach ($rows as $r) {
                $sheet->setCellValue('A' . $row, $r->noCamion ?: '(non lié)');
                $sheet->setCellValue('B' . $row, $r->vehicleName);
                $sheet->setCellValue('C' . $row, (float) $r->totalKm);
                $sheet->setCellValue('D' . $row, (float) $r->taxableKm);
                $sheet->setCellValue('E' . $row, $r->jurisdiction);

                // Centrer Camion et Nom véhicule
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Format numérique avec séparateur de milliers et 2 décimales
                $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle('A' . $row . ':E' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('CCCCCC');
                $row++;
            }

            // Ligne des totaux
            if (count($rows) > 0) {
                $sheet->mergeCells('A' . $row . ':B' . $row);
                $sheet->setCellValue('A' . $row, 'TOTAL (' . count($rows) . ' véhicules)');
                $sheet->setCellValue('C' . $row, (float) $totalKm);
                $sheet->setCellValue('D' . $row, (float) $taxableKm);
                $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true)->getColor()->setRGB('8B0000');
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');
                $sheet->getStyle('A' . $row . ':E' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // Auto-size
            foreach (range('A', 'E') as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        $filename = 'km_ifta_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';

        // Stream : en-têtes sur la Response CI4 partagée, corps via
        // php://output (passe par ob -> corps de la réponse)
        $this->response
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0');

        $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
        $objWriter->save('php://output');
    }
}
