<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SanivanCalculator — Logique métier pour le rapport Sanivan.
 *
 * Calculs de transport, heures, coûts, arrondis.
 * Aucun accès à la BD, aucune génération HTML.
 */
class SanivanCalculator
{
    /**
     * Calcule le transport et la collecte à partir des lignes de transport (résultat SQL).
     *
     * Reproduit exactement la logique de Sanivan_model::setTransportCam().
     *
     * @param object $dbResult  Résultat CI_DB_result de SanivanRepository::getTransportData()
     * @return array [
     *   'hreDebCam'      => string,  Heure de début camion
     *   'hreFinCam'      => string,  Heure de fin camion
     *   'tonnage'        => float,   Tonnage total
     *   'collecteHours'  => float,   Heures de collecte
     *   'transportHours' => float,   Heures de transport
     * ]
     */
    public function calculateTransportFromRows($dbResult)
    {
        $hreDebCam     = '';
        $hreFinCam     = '';
        $tonnage       = 0;
        $collecteMin   = 0;
        $transportVille = [];
        $hreDepFin     = '';
        $lastrow       = $dbResult->num_rows();
        $idcedule      = '';

        foreach ($dbResult->result() as $row) {
            $hreDebCam = $row->HreDeb;
            $hreFinCam = $row->HreFin;

            $ton = floatval(str_replace(',', '.', $row->Tonnage));
            if (is_float($ton) && $idcedule != $row->ID) {
                $tonnage += $ton;
            }

            // Collecte = temps passé en ville
            $to   = strtotime($row->HreVilleFin);
            $from = strtotime($row->HreVilleDeb);
            $collecteMin += round(abs($to - $from) / 60, 2);

            // Transport ville
            $transportVille[] = $row->Transport_RDF + $row->Transport_DEP;

            if ($row->Transport_RDF == 0) {
                $to   = strtotime($row->HreVilleDeb);
                $from = strtotime($hreDepFin);
                $min  = 0;
                if ($to && $from) {
                    $min = round(abs($to - $from) / 60, 2);
                }
                $transportVille[] = $min;
            }

            if ($row->numero_ligne == $lastrow) {
                $transportVille[] = ($row->Transport_TO_RDF === null) ? 0 : intval($row->Transport_TO_RDF);
            }

            $hreDepFin = $row->FinDepotoire;
        }

        // Somme transport → heures
        $transportMin = 0;
        foreach ($transportVille as $v) {
            $transportMin += intval($v);
        }

        return [
            'hreDebCam'      => $hreDebCam,
            'hreFinCam'      => $hreFinCam,
            'tonnage'        => $tonnage,
            'collecteHours'  => round($collecteMin / 60, 2),
            'transportHours' => round($transportMin / 60, 2),
        ];
    }

    /**
     * Calcule les heures totales du camion et le transport résiduel.
     *
     * @param string $hreDebCam     Heure de début
     * @param string $hreFinCam     Heure de fin
     * @param float  $collecteHours Heures de collecte
     * @return array ['hresCam' => float, 'transport' => float]
     */
    public function calculateTruckHours($hreDebCam, $hreFinCam, $collecteHours)
    {
        $to   = strtotime($hreFinCam);
        $from = strtotime($hreDebCam);
        $min  = round(abs($to - $from) / 60, 2);
        $hrs  = round($min / 60, 2);

        return [
            'hresCam'   => $hrs,
            'transport' => $hrs - $collecteHours,
        ];
    }

    /**
     * Arrondit les heures au quart d'heure le plus proche.
     * Reproduit exactement Sanivan_model::roundHres().
     *
     * @param mixed $hres
     * @return string Heures arrondies (ex: "7.50")
     */
    public function roundHours($hres)
    {
        if ((intval($hres) == 0) || ($hres == '')) {
            return '0.00';
        }
        $len = strlen($hres);
        if ($len < 4) {
            $hres = $hres . '.00';
        }
        if (strpos($hres, '.') !== false) {
            $arr = explode('.', $hres);
        } else {
            return round($hres, 2);
        }
        $dec = intval($arr[1]);
        if ($dec < 13)                   return $arr[0] . '.00';
        if ($dec >= 13 && $dec < 26)     return $arr[0] . '.25';
        if ($dec >= 26 && $dec < 38)     return $arr[0] . '.25';
        if ($dec >= 38 && $dec < 52)     return $arr[0] . '.50';
        if ($dec >= 52 && $dec < 63)     return $arr[0] . '.50';
        if ($dec >= 63 && $dec < 76)     return $arr[0] . '.75';
        if ($dec >= 76 && $dec < 87)     return $arr[0] . '.75';
        if ($dec >= 87)                  return ($arr[0] + 1) . '.00';

        return round($hres, 2);
    }

    /**
     * Calcule le coût du carburant.
     *
     * @param float $fuel       Litres
     * @param float $montantFuel Prix par litre
     * @return string Coût formaté (ex: "125.40")
     */
    public function calculateFuelCost($fuel, $montantFuel)
    {
        $cost = floatval($fuel) * floatval($montantFuel);
        return number_format((float) $cost, 2, '.', '');
    }

    /**
     * Calcule le coût et les heures du chauffeur (incluant les extras).
     *
     * @param string $hreDeb      Heure début normale
     * @param string $hreFin      Heure fin normale
     * @param string $hreDebExtra Heure début extra
     * @param string $hreFinExtra Heure fin extra
     * @param float  $salaireRate Taux horaire
     * @return array ['hours' => string, 'cost' => string]
     */
    public function calculateDriverCost($hreDeb, $hreFin, $hreDebExtra, $hreFinExtra, $salaireRate)
    {
        $minutes = 0;

        $to   = strtotime($hreFin);
        $from = strtotime($hreDeb);
        $minutes += round(abs($from - $to) / 60, 2);

        $to   = strtotime($hreFinExtra);
        $from = strtotime($hreDebExtra);
        $minutes += round(abs($from - $to) / 60, 2);

        $rawHours = round($minutes / 60, 2);
        $hours = $this->roundHours($rawHours);
        $cost  = number_format((float) round($hours * $salaireRate, 2), 2, '.', '');

        return ['hours' => $hours, 'cost' => $cost];
    }

    /**
     * Calcule le coût et les heures d'un aide.
     *
     * @param string $hreDeb     Heure début
     * @param string $hreFin     Heure fin
     * @param float  $salaireRate Taux horaire
     * @return array ['hours' => string, 'cost' => string]
     */
    public function calculateAideCost($hreDeb, $hreFin, $salaireRate)
    {
        $to   = strtotime($hreFin);
        $from = strtotime($hreDeb);
        $min  = round(abs($from - $to) / 60, 2);
        $raw  = round($min / 60, 2);
        $hours = $this->roundHours($raw);
        $cost  = number_format((float) round($hours * $salaireRate, 2), 2, '.', '');

        return ['hours' => $hours, 'cost' => $cost];
    }

    /**
     * Calcule le coût de disposition.
     *
     * @param float $tonnage  Tonnage total
     * @param float $prixSani Prix par tonne
     * @return float Coût arrondi
     */
    public function calculateDispositionCost($tonnage, $prixSani)
    {
        return round($tonnage * $prixSani, 2);
    }

    /**
     * Calcule les heures de remplacement pour un camion de remplacement.
     *
     * @param string $hreDeb Heure début
     * @param string $hreFin Heure fin
     * @return array ['hresCam' => float, 'transport' => float]
     */
    public function calculateReplacementHours($hreDeb, $hreFin)
    {
        $to   = strtotime($hreFin);
        $from = strtotime($hreDeb);
        $min  = round(abs($to - $from) / 60, 2);
        $hrs  = round($min / 60, 2);

        return ['hresCam' => $hrs, 'transport' => $hrs];
    }
}
