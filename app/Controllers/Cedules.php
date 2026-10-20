<?php

namespace App\Controllers;

use Config\Database;

/**
 * Module pilote de la migration CI3 -> CI4 (strangler).
 * Lecture seule : retourne la cedule d'une journee en JSON.
 * L'equivalent CI3 (Co_gestion::showToday) genere du HTML et fait des
 * ecritures pendant le rendu; cette version ne fait que lire.
 */
class Cedules extends BaseController
{
    public function day(string $date)
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Format de date attendu : AAAA-MM-JJ']);
        }

        $db = Database::connect();

        $groupes = $db->table('grp_cedules g')
            ->select('g.id, g.TypeCedule, g.ACol, g.ARow, g.idvilles,
                      v.Nom AS nomville, v.colorhex AS couleurville')
            ->join('ville v', 'v.id = g.idvilles', 'left')
            ->where('g.datejour', $date)
            ->orderBy('g.TypeCedule, g.ACol, g.ARow')
            ->get()
            ->getResultArray();

        $lines = [];
        if ($groupes !== []) {
            $lines = $db->table('cedules c')
                ->select('c.idgrp, c.ID, c.NOCAMION, c.idchauffeurs, c.HELPERS_1,
                          c.TypeDeProduit, c.Secteur, c.IsStart, c.chaufconfirme,
                          r.Prenom AS chauf_prenom, r.Nom AS chauf_nom')
                ->join('reshumaine r', 'r.id = c.idchauffeurs', 'left')
                ->whereIn('c.idgrp', array_column($groupes, 'id'))
                ->orderBy('c.ID')
                ->get()
                ->getResultArray();
        }

        $parGroupe = [];
        foreach ($lines as $l) {
            $parGroupe[$l['idgrp']][] = $l;
            unset($parGroupe[$l['idgrp']][count($parGroupe[$l['idgrp']]) - 1]['idgrp']);
        }

        $out = [
            'date' => $date,
            'nb_groupes' => count($groupes),
            'nb_lignes' => count($lines),
            'groupes' => [],
        ];
        foreach ($groupes as $g) {
            $g['lignes'] = array_values($parGroupe[$g['id']] ?? []);
            $out['groupes'][] = $g;
        }

        return $this->response->setJSON($out);
    }
}
