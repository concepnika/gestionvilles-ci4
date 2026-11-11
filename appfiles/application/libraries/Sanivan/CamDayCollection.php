<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/CamDayEntity.php');

/**
 * CamDayCollection — Collection indexée de CamDayEntity, une par camion.
 *
 * Remplace l'ancienne classe Collection du Sanivan_model.
 * La persistance (INSERT/UPDATE sanivanday) est déléguée à SanivanRepository.
 */
class CamDayCollection
{
    /** @var CamDayEntity[] */
    private $items = [];

    /**
     * Ajoute une entité dans la collection.
     *
     * @param CamDayEntity $entity
     * @param string|null  $key  Clé (généralement NOCAMION)
     * @return void
     */
    public function addItem(CamDayEntity $entity, $key = null)
    {
        if ($key === null) {
            $this->items[] = $entity;
        } else {
            if (!isset($this->items[$key])) {
                $this->items[$key] = $entity;
            }
        }
    }

    /**
     * Récupère une entité par sa clé.
     *
     * @param string $key
     * @return CamDayEntity|null
     */
    public function getItem($key)
    {
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    /**
     * Vérifie si une clé existe dans la collection.
     *
     * @param string $key
     * @return bool
     */
    public function keyExists($key)
    {
        return isset($this->items[$key]);
    }

    /**
     * Retourne toutes les clés de la collection.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * Retourne le nombre d'éléments.
     *
     * @return int
     */
    public function length()
    {
        return count($this->items);
    }

    /**
     * Retourne toutes les entités.
     *
     * @return CamDayEntity[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Agrège les totaux de tous les camions et retourne un tableau associatif.
     *
     * @return array Clés : hresChauf, coutChauf, hresAide, coutAide, hresCam,
     *               carburant, coutCarburant, transport, collecte, disposition,
     *               coutDispot, nbBac, revenu, net, netByHres
     */
    public function getTotals()
    {
        $totals = [
            'hresChauf'     => 0,
            'coutChauf'     => 0,
            'hresAide'      => 0,
            'coutAide'      => 0,
            'hresCam'       => 0,
            'carburant'     => 0,
            'coutCarburant' => 0,
            'transport'     => 0,
            'collecte'      => 0,
            'disposition'   => 0,
            'coutDispot'    => 0,
            'nbBac'         => 0,
            'revenu'        => 0,
            'net'           => 0,
            'netByHres'     => 0,
        ];

        foreach ($this->items as $entity) {
            $totals['hresChauf']     += $entity->hresChauf;
            $totals['coutChauf']     += $entity->coutChauf;
            $totals['hresAide']      += $entity->hresAide;
            $totals['coutAide']      += $entity->coutAide;
            $totals['hresCam']       += $entity->hresCam;
            $totals['carburant']     += $entity->carburant;
            $totals['coutCarburant'] += $entity->coutCarburant;
            $totals['transport']     += $entity->transport;
            $totals['collecte']      += $entity->collecte;
            $totals['disposition']   += $entity->disposition;
            $totals['coutDispot']    += $entity->coutDispot;
            $totals['nbBac']         += $entity->nbBac;
            $totals['revenu']        += $entity->revenu;
            $totals['net']           += $entity->net;
            $totals['netByHres']     += $entity->netByHres;
        }

        return $totals;
    }
}
