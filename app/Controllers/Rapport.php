<?php

namespace App\Controllers;

use App\Models\RevenuModel;

/**
 * Port CI3 -> CI4 de co_rapport : SEULE la methode routee
 * ShowTDBFinancier est portee (rapport/financier) — les ~20 autres
 * methodes de co_rapport ne sont routées nulle part en CI3
 * (module roll-off/bureau jamais branché).
 */
class Rapport extends BaseController
{
    public function ShowTDBFinancier($datedebut = 'none', $datefin = 'none')
    {
        // Accepter aussi les paramètres GET du formulaire de dates
        if ($this->input->get('datedebut')) {
            $datedebut = $this->input->get('datedebut');
        }
        if ($this->input->get('datefin')) {
            $datefin = $this->input->get('datefin');
        }
        if ($datedebut == 'none') {
            $date = new \DateTime();
            date_sub($date, date_interval_create_from_date_string("1 year"));
            $datedebut = $date->format('Y-m-d');
        }
        if ($datefin == 'none') {
            $datefin = date('Y-m-d');
        }

        // CI3 : $this->load->model('Revenu_model', 'revenu')
        $revenu = new RevenuModel();

        // --- Graphique 1 : Revenus vs Dépenses par mois ---
        $revDepMois = $revenu->getRevenusVsDepensesMois($datedebut, $datefin);
        $chartMoisLabels = $chartRevenus = $chartDepenses = $chartProfit = array();
        foreach ($revDepMois as $row) {
            $chartMoisLabels[] = "'" . $row->mois . "'";
            $chartRevenus[]    = $row->revenus;
            $chartDepenses[]   = $row->depenses;
            $chartProfit[]     = round($row->revenus - $row->depenses, 2);
        }
        $data['chartMoisLabels'] = implode(',', $chartMoisLabels);
        $data['chartRevenus']    = implode(',', $chartRevenus);
        $data['chartDepenses']   = implode(',', $chartDepenses);
        $data['chartProfit']     = implode(',', $chartProfit);

        // --- Graphique 2 : Répartition des dépenses (donut) ---
        $repartition = $revenu->getRepartitionDepenses($datedebut, $datefin);
        $data['donutLabels']  = "'Carburant','Chauffeurs','Aides','Disposition'";
        $data['donutValues']  = implode(',', array(
            floatval($repartition->carburant),
            floatval($repartition->chauffeurs),
            floatval($repartition->aides),
            floatval($repartition->disposition)
        ));

        // --- Graphique 3 : Revenus par type de collecte (stacked) ---
        $revParType = $revenu->getRevenuParTypeMois($datedebut, $datefin);
        $typesMois = array();
        $typesData = array();
        $typesCouleurs = array();
        foreach ($revParType as $row) {
            if (!in_array("'" . $row->mois . "'", $typesMois)) {
                $typesMois[] = "'" . $row->mois . "'";
            }
            $typesData[$row->nom_dechet]['couleur'] = $row->couleur;
            $typesData[$row->nom_dechet]['data'][$row->mois] = $row->total;
        }
        // Construire les séries JSON
        $stackedSeries = array();
        $allMois = array();
        foreach ($typesMois as $m) {
            $allMois[] = str_replace("'", "", $m);
        }
        foreach ($typesData as $nom => $info) {
            $serieData = array();
            foreach ($allMois as $m) {
                $serieData[] = isset($info['data'][$m]) ? $info['data'][$m] : 0;
            }
            $stackedSeries[] = '{name:"' . addslashes($nom) . '",data:[' . implode(',', $serieData) . ']}';
            $typesCouleurs[] = "'" . $info['couleur'] . "'";
        }
        $data['stackedMoisLabels']  = implode(',', $typesMois);
        $data['stackedSeries']      = implode(',', $stackedSeries);
        $data['stackedCouleurs']    = implode(',', $typesCouleurs);

        // --- Graphique 4 : Rentabilité par camion ---
        $rentaCam = $revenu->getRentabiliteCamion($datedebut, $datefin);
        $camLabels = $camRevenus = $camDepenses = array();
        foreach ($rentaCam as $row) {
            $camLabels[]   = "'" . $row->nocamion . "'";
            $camRevenus[]  = $row->revenus;
            $camDepenses[] = $row->depenses;
        }
        $data['camLabels']   = implode(',', $camLabels);
        $data['camRevenus']  = implode(',', $camRevenus);
        $data['camDepenses'] = implode(',', $camDepenses);

        // --- Graphique 5 : Profit net (area) ---
        // Réutilise les données du graphique 1
        $data['areaLabels'] = $data['chartMoisLabels'];
        $data['areaProfit'] = $data['chartProfit'];

        // --- Graphique 6 : KPI Coût par tonne ---
        $kpi = $revenu->getCoutParTonne($datedebut, $datefin);
        $data['kpiTonnage']       = $kpi->total_tonnage;
        $data['kpiRevenus']       = $kpi->total_revenus;
        $data['kpiDepenses']      = $kpi->total_depenses;
        $data['kpiCoutParTonne']  = $kpi->cout_par_tonne;
        $data['kpiRevenuParTonne'] = $kpi->revenu_par_tonne;
        $data['kpiProfitNet']     = $kpi->profit_net;

        $data['nom']       = $this->session->userdata('nom');
        $data['email']     = $this->session->userdata('email');
        $data['privilege'] = $this->session->userdata('privilege');
        $data['datedebut'] = $datedebut;
        $data['datefin']   = $datefin;
        $data['ActiveDate'] = date('Y-m-d');

        echo view('rapports/tdbFinancier', $data);
    }
}
