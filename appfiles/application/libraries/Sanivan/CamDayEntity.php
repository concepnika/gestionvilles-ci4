<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CamDayEntity — Entité de données pour un camion/jour.
 *
 * Contrairement à l'ancien CamDay, cette classe ne fait AUCUN appel à la base
 * de données dans son constructeur. La persistance est gérée séparément
 * par SanivanRepository::saveSanivanDay().
 */
class CamDayEntity
{
    /** @var float Heures camion */
    public $hresCam = 0;
    /** @var float Litres de carburant */
    public $carburant = 0;
    /** @var float Coût du carburant */
    public $coutCarburant = 0;
    /** @var float Heures chauffeur */
    public $hresChauf = 0;
    /** @var float Coût chauffeur */
    public $coutChauf = 0;
    /** @var float Heures aide */
    public $hresAide = 0;
    /** @var float Coût aide */
    public $coutAide = 0;
    /** @var float Temps de transport (heures) */
    public $transport = 0;
    /** @var float Temps de collecte (heures) */
    public $collecte = 0;
    /** @var float Tonnage de disposition */
    public $disposition = 0;
    /** @var float Coût de disposition */
    public $coutDispot = 0;
    /** @var int Nombre de bacs */
    public $nbBac = 0;
    /** @var float Revenu */
    public $revenu = 0;
    /** @var float Net */
    public $net = 0;
    /** @var float Net par heure */
    public $netByHres = 0;
    /** @var int ID sanivanday (0 = pas encore persisté) */
    public $id = 0;

    /**
     * @param float  $transport  Temps transport
     * @param float  $collecte   Temps collecte
     * @param float  $hresCam    Heures camion
     */
    public function __construct($transport = 0, $collecte = 0, $hresCam = 0)
    {
        $this->transport  = $transport;
        $this->collecte   = $collecte;
        $this->hresCam    = $hresCam;
    }

    /**
     * Remet toutes les valeurs de dépense à 0 (utilisé pour les brokers).
     *
     * @return void
     */
    public function resetExpenses()
    {
        $this->collecte       = 0;
        $this->transport      = 0;
        $this->hresCam        = 0;
        $this->carburant      = 0;
        $this->coutCarburant  = 0;
        $this->hresChauf      = 0;
        $this->coutChauf      = 0;
        $this->hresAide       = 0;
        $this->coutAide       = 0;
    }

    /**
     * Retourne un tableau associatif prêt pour l'INSERT/UPDATE sanivanday.
     *
     * @return array
     */
    public function toDbArray()
    {
        return [
            'hresCam'          => $this->hresCam,
            'coutCarburant'    => $this->coutCarburant,
            'carburant'        => $this->carburant,
            'hresChauf'        => $this->hresChauf,
            'coutChauf'        => $this->coutChauf,
            'hresAide'         => $this->hresAide,
            'coutAide'         => $this->coutAide,
            'disposition'      => $this->disposition,
            'coutDisposition'  => $this->coutDispot,
            'nbBac'            => $this->nbBac,
        ];
    }
}
