<?php

namespace App\Models;

/**
 * Port mecanique de Revenu_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * Revenu_model
 * 
 * Calcule et stocke les revenus par camion, par jour de collecte,
 * par ville et par type de déchet.
 * 
 * Sources de données:
 *   - cedules          : lien camion/ville/type déchet/date
 *   - info_villes      : HreDeb, HreFin, nbBac (par cédule)
 *   - info_depotoire   : Tonnage (par info_villes)
 *   - camionjour       : HreDeb, HreFin du camion
 *   - revenuville      : taux configurés par ville/type déchet
 */
class RevenuModel extends Ci3Model
{
    function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Calcule et enregistre les revenus pour une date donnée.
     * On peut filtrer par ville (optionnel).
     *
     * @param string   $datejour  Date au format Y-m-d
     * @param int|null $idville   Filtrer par ville (null = toutes)
     * @return array   Résumé des lignes calculées
     */
    public function calculerRevenus($datejour, $idville = null)
    {
        $dteactive = $this->db->escape($datejour);
        $whereVille = '';
        if ($idville !== null && is_numeric($idville) && $idville > 0) {
            $whereVille = ' AND c.idvilles = ' . $this->db->escape($idville);
        }

        // ---------------------------------------------------------------
        // Étape 1: Récupérer toutes les données de collecte du jour
        //   Chaque ligne = 1 cédule avec ses infos ville + dépotoire
        // ---------------------------------------------------------------
        $strSQL = "SELECT 
                c.ID AS idcedule,
                c.NOCAMION AS nocamion,
                c.idvilles AS idville,
                c.TypeDeProduit AS nom_dechet,
                IFNULL(td.id, 0) AS type_ramassage,
                
                -- Heures du camion (de camionjour)
                cj.HreDeb AS cam_hre_deb,
                cj.HreFin AS cam_hre_fin,
                ROUND(TIME_TO_SEC(TIMEDIFF(cj.HreFin, cj.HreDeb)) / 3600, 2) AS hres_camion,
                
                -- Heures collecte en ville (de info_villes)
                iv.HreDeb AS ville_hre_deb,
                iv.HreFin AS ville_hre_fin,
                ROUND(TIME_TO_SEC(TIMEDIFF(iv.HreFin, iv.HreDeb)) / 3600, 2) AS hres_collecte,
                
                -- Nombre de bacs (de info_villes)
                IFNULL(iv.nbBac, 0) AS nb_bac,
                
                -- Tonnage (de info_depotoire, somme si plusieurs dépotoires)
                IFNULL(SUM(CAST(REPLACE(idep.Tonnage, ',', '.') AS DECIMAL(10,2))), 0) AS tonnage,
                
                -- Taux de revenuville
                IFNULL(rv.MontFixe, 0) AS taux_collecte,
                IFNULL(rv.MontRedevance, 0) AS taux_redevance,
                IFNULL(rv.Disposition, 0) AS taux_disposition,
                IFNULL(rv.MontTonne, 0) AS taux_tonne,
                IFNULL(rv.inHour, 0) AS taux_heure,
                IFNULL(rv.AuBac, 0) AS taux_bac
                
            FROM cedules c
            
            -- Joindre le type de déchet pour obtenir son ID
            LEFT JOIN typededechet td ON td.Nom = c.TypeDeProduit
            
            -- Joindre les infos de la ville pour cette cédule
            LEFT JOIN info_villes iv ON iv.idcedule = c.ID
            
            -- Joindre les dépotoires pour obtenir le tonnage
            LEFT JOIN info_depotoire idep ON idep.idinfoville = iv.id
            
            -- Joindre les heures du camion
            LEFT JOIN camionjour cj ON cj.NOCAMION = c.NOCAMION AND cj.DateJour = c.DateJour
            
            -- Joindre les taux de revenu par ville et type
            LEFT JOIN revenuville rv ON rv.idville = c.idvilles AND rv.TypeRamassage = td.id
            
            WHERE c.DateJour = $dteactive
              AND c.NOCAMION <> '' 
              AND c.NOCAMION <> 'Cam'
              AND c.idvilles > 0
              $whereVille
              
            GROUP BY c.ID, c.NOCAMION, c.idvilles, c.TypeDeProduit, td.id,
                     cj.HreDeb, cj.HreFin, iv.HreDeb, iv.HreFin, iv.nbBac,
                     rv.MontFixe, rv.MontRedevance, rv.Disposition, rv.MontTonne, rv.inHour, rv.AuBac
                     
            ORDER BY c.NOCAMION, c.idvilles";

        $result = $this->db->query($strSQL);
        $lignes = array();

        foreach ($result->getResult() as $row) {
            // ---------------------------------------------------------------
            // Étape 2: Calculer les montants pour chaque ligne
            // ---------------------------------------------------------------
            $tonnage     = floatval($row->tonnage);
            $hres_camion = floatval($row->hres_camion);
            $nb_bac      = intval($row->nb_bac);
            
            $taux_collecte    = floatval($row->taux_collecte);
            $taux_redevance   = floatval($row->taux_redevance);
            $taux_disposition = floatval($row->taux_disposition);
            $taux_tonne       = floatval($row->taux_tonne);
            $taux_heure       = floatval($row->taux_heure);
            $taux_bac         = floatval($row->taux_bac);

            // Collecte = montant forfaitaire (+ somme des autres montants > 0)
            $mont_collecte = $taux_collecte;

            // Redevance: si taux > 0, multiplier par tonnage
            $mont_redevance = ($taux_redevance > 0) ? round($taux_redevance * $tonnage, 2) : 0;

            // Disposition: si taux > 0, multiplier par tonnage
            $mont_disposition = ($taux_disposition > 0) ? round($taux_disposition * $tonnage, 2) : 0;

            // A la tonne: si taux > 0, multiplier par tonnage
            $mont_tonne = ($taux_tonne > 0) ? round($taux_tonne * $tonnage, 2) : 0;

            // A l'heure: multiplier par heures camion
            $mont_heure = ($taux_heure > 0) ? round($taux_heure * $hres_camion, 2) : 0;

            // Au bac: si taux > 0, multiplier par nombre de bacs
            $mont_bac = ($taux_bac > 0) ? round($taux_bac * $nb_bac, 2) : 0;

            // Total revenu pour cette ligne
            $total_revenu = round($mont_collecte + $mont_redevance + $mont_disposition 
                                + $mont_tonne + $mont_heure + $mont_bac, 2);

            // ---------------------------------------------------------------
            // Étape 3: Enregistrer dans revenu_jour_camion (INSERT ou UPDATE)
            // ---------------------------------------------------------------
            $data = array(
                'datejour'         => $datejour,
                'nocamion'         => $row->nocamion,
                'idville'          => intval($row->idville),
                'idcedule'         => intval($row->idcedule),
                'type_ramassage'   => intval($row->type_ramassage),
                'nb_collecte'      => 1,
                'tonnage'          => $tonnage,
                'hres_camion'      => $hres_camion,
                'nb_bac'           => $nb_bac,
                'taux_collecte'    => $taux_collecte,
                'taux_redevance'   => $taux_redevance,
                'taux_disposition' => $taux_disposition,
                'taux_tonne'       => $taux_tonne,
                'taux_heure'       => $taux_heure,
                'taux_bac'         => $taux_bac,
                'mont_collecte'    => $mont_collecte,
                'mont_redevance'   => $mont_redevance,
                'mont_disposition' => $mont_disposition,
                'mont_tonne'       => $mont_tonne,
                'mont_heure'       => $mont_heure,
                'mont_bac'         => $mont_bac,
                'total_revenu'     => $total_revenu
            );

            $this->_upsertRevenu($data);

            $lignes[] = $data;
        }

        return $lignes;
    }

    /**
     * INSERT ou UPDATE une ligne de revenu_jour_camion
     *
     * @param array $data
     * @return void
     */
    private function _upsertRevenu($data)
    {
        $datejour       = $this->db->escape($data['datejour']);
        $nocamion       = $this->db->escape($data['nocamion']);
        $idville        = $this->db->escape($data['idville']);
        $idcedule       = $this->db->escape($data['idcedule']);
        $type_ramassage = $this->db->escape($data['type_ramassage']);

        // Vérifier si la ligne existe déjà
        $strSQL = "SELECT id FROM revenu_jour_camion 
                   WHERE datejour = $datejour 
                     AND nocamion = $nocamion 
                     AND idville = $idville 
                     AND type_ramassage = $type_ramassage 
                     AND idcedule = $idcedule";
        $existe = $this->db->query($strSQL);

        if ($existe->getNumRows() > 0) {
            $id = $existe->getRow()->id;
            $strSQL = "UPDATE revenu_jour_camion SET
                nb_collecte      = " . $this->db->escape($data['nb_collecte']) . ",
                tonnage          = " . $this->db->escape($data['tonnage']) . ",
                hres_camion      = " . $this->db->escape($data['hres_camion']) . ",
                nb_bac           = " . $this->db->escape($data['nb_bac']) . ",
                taux_collecte    = " . $this->db->escape($data['taux_collecte']) . ",
                taux_redevance   = " . $this->db->escape($data['taux_redevance']) . ",
                taux_disposition = " . $this->db->escape($data['taux_disposition']) . ",
                taux_tonne       = " . $this->db->escape($data['taux_tonne']) . ",
                taux_heure       = " . $this->db->escape($data['taux_heure']) . ",
                taux_bac         = " . $this->db->escape($data['taux_bac']) . ",
                mont_collecte    = " . $this->db->escape($data['mont_collecte']) . ",
                mont_redevance   = " . $this->db->escape($data['mont_redevance']) . ",
                mont_disposition = " . $this->db->escape($data['mont_disposition']) . ",
                mont_tonne       = " . $this->db->escape($data['mont_tonne']) . ",
                mont_heure       = " . $this->db->escape($data['mont_heure']) . ",
                mont_bac         = " . $this->db->escape($data['mont_bac']) . ",
                total_revenu     = " . $this->db->escape($data['total_revenu']) . "
                WHERE id = $id";
            $this->db->query($strSQL);
        } else {
            $this->db->table('revenu_jour_camion')->insert($data);
        }
    }

    /**
     * Récupère le résumé des revenus pour une ville sur une période.
     * Groupé par type de ramassage.
     *
     * @param int    $idville
     * @param string $date_debut  Format Y-m-d
     * @param string $date_fin    Format Y-m-d
     * @return array
     */
    public function getRevenuVilleResume($idville, $date_debut, $date_fin)
    {
        $idville    = $this->db->escape($idville);
        $date_debut = $this->db->escape($date_debut);
        $date_fin   = $this->db->escape($date_fin);

        $strSQL = "SELECT 
                r.type_ramassage,
                IFNULL(td.Nom, 'Inconnu') AS nom_dechet,
                IFNULL(td.couleur, '#CCCCCC') AS couleur,
                IFNULL(td.textcolor, '#000000') AS textcolor,
                
                SUM(r.nb_collecte) AS total_collectes,
                SUM(r.tonnage) AS total_tonnage,
                SUM(r.hres_camion) AS total_hres,
                SUM(r.nb_bac) AS total_bacs,
                
                SUM(r.mont_collecte) AS total_mont_collecte,
                SUM(r.mont_redevance) AS total_mont_redevance,
                SUM(r.mont_disposition) AS total_mont_disposition,
                SUM(r.mont_tonne) AS total_mont_tonne,
                SUM(r.mont_heure) AS total_mont_heure,
                SUM(r.mont_bac) AS total_mont_bac,
                SUM(r.total_revenu) AS grand_total
                
            FROM revenu_jour_camion r
            LEFT JOIN typededechet td ON td.id = r.type_ramassage
            WHERE r.idville = $idville
              AND r.datejour BETWEEN $date_debut AND $date_fin
            GROUP BY r.type_ramassage, td.Nom, td.couleur, td.textcolor
            ORDER BY td.Nom";

        return $this->db->query($strSQL)->getResult();
    }

    /**
     * Récupère le détail des revenus par camion pour une ville/date.
     *
     * @param int    $idville
     * @param string $datejour  Format Y-m-d
     * @return array
     */
    public function getRevenuDetailJour($idville, $datejour)
    {
        $idville  = $this->db->escape($idville);
        $datejour = $this->db->escape($datejour);

        $strSQL = "SELECT 
                r.*,
                IFNULL(td.Nom, 'Inconnu') AS nom_dechet,
                IFNULL(td.couleur, '#CCCCCC') AS couleur,
                IFNULL(td.textcolor, '#000000') AS textcolor,
                IFNULL((SELECT ville.Nom FROM ville WHERE ville.id = r.idville), '') AS nom_ville
                
            FROM revenu_jour_camion r
            LEFT JOIN typededechet td ON td.id = r.type_ramassage
            WHERE r.idville = $idville
              AND r.datejour = $datejour
            ORDER BY r.nocamion, td.Nom";

        return $this->db->query($strSQL)->getResult();
    }

    /**
     * Génère le HTML du tableau résumé des revenus pour une ville/période.
     * Utilisé par le bouton "Revenu" dans le calendrier des villes.
     *
     * @param int    $idville
     * @param string $date_debut
     * @param string $date_fin
     * @return string HTML
     */
    public function getRevenuTableHTML($idville, $date_debut, $date_fin)
    {
        $resume = $this->getRevenuVilleResume($idville, $date_debut, $date_fin);

        $html = '<table class="table table-bordered table-striped mb-0" id="tblRevenuResultat">
                    <thead>
                        <tr style="color:white;background-color:darkblue;">
                            <th style="text-transform:uppercase;text-align:center;">Type de collecte</th>
                            <th style="text-transform:uppercase;text-align:center;">Nb Collectes</th>
                            <th style="text-transform:uppercase;text-align:center;">Tonnage</th>
                            <th style="text-transform:uppercase;text-align:center;">Collecte $</th>
                            <th style="text-transform:uppercase;text-align:center;">Redevance $</th>
                            <th style="text-transform:uppercase;text-align:center;">Disposition $</th>
                            <th style="text-transform:uppercase;text-align:center;">A la tonne $</th>
                            <th style="text-transform:uppercase;text-align:center;">A l\'heure $</th>
                            <th style="text-transform:uppercase;text-align:center;">Au Bac $</th>
                            <th style="text-transform:uppercase;text-align:center;background-color:#FFD700;">Total $</th>
                        </tr>
                    </thead>
                    <tbody>';

        $grandTotal = 0;
        foreach ($resume as $row) {
            $grandTotal += floatval($row->grand_total);
            $html .= '<tr>
                <td style="background-color:' . $row->couleur . ';color:' . $row->textcolor . ';font-weight:bold;text-align:center;">' 
                    . $row->nom_dechet . '</td>
                <td style="text-align:center;">' . intval($row->total_collectes) . '</td>
                <td style="text-align:center;">' . number_format($row->total_tonnage, 2, ',', ' ') . ' tn</td>
                <td style="text-align:right;">' . number_format($row->total_mont_collecte, 2, ',', ' ') . '</td>
                <td style="text-align:right;">' . number_format($row->total_mont_redevance, 2, ',', ' ') . '</td>
                <td style="text-align:right;">' . number_format($row->total_mont_disposition, 2, ',', ' ') . '</td>
                <td style="text-align:right;">' . number_format($row->total_mont_tonne, 2, ',', ' ') . '</td>
                <td style="text-align:right;">' . number_format($row->total_mont_heure, 2, ',', ' ') . '</td>
                <td style="text-align:right;">' . number_format($row->total_mont_bac, 2, ',', ' ') . '</td>
                <td style="text-align:right;font-weight:bold;background-color:#FFFACD;">' 
                    . number_format($row->grand_total, 2, ',', ' ') . '</td>
            </tr>';
        }

        $html .= '<tr style="font-weight:bold;background-color:#f0f0f0;">
                    <td colspan="9" style="text-align:right;">GRAND TOTAL:</td>
                    <td style="text-align:right;background-color:#FFD700;">' 
                        . number_format($grandTotal, 2, ',', ' ') . ' $</td>
                  </tr>';

        $html .= '</tbody></table>';

        return $html;
    }

    // ===================================================================
    //  MÉTHODES POUR LES GRAPHIQUES DU TABLEAU DE BORD FINANCIER
    // ===================================================================

    /**
     * Graphique 1: Revenus vs Dépenses par mois
     * Retourne revenus, dépenses et profit net groupés par mois.
     *
     * @param string $date_debut  Format Y-m-d
     * @param string $date_fin    Format Y-m-d
     * @return array
     */
    public function getRevenusVsDepensesMois($date_debut, $date_fin)
    {
        $date_debut = $this->db->escape($date_debut);
        $date_fin   = $this->db->escape($date_fin);

        $strSQL = "SELECT 
                DATE_FORMAT(r.datejour, '%Y-%m') AS mois,
                ROUND(SUM(r.total_revenu), 2) AS revenus,
                IFNULL((SELECT ROUND(SUM(s.coutCarburant + s.coutChauf + s.coutAide + s.coutDisposition), 2)
                        FROM sanivanday s
                        WHERE DATE_FORMAT(s.DateJour, '%Y-%m') = DATE_FORMAT(r.datejour, '%Y-%m')
                          AND s.DateJour BETWEEN $date_debut AND $date_fin
                ), 0) AS depenses
            FROM revenu_jour_camion r
            WHERE r.datejour BETWEEN $date_debut AND $date_fin
            GROUP BY DATE_FORMAT(r.datejour, '%Y-%m')
            ORDER BY mois";

        return $this->db->query($strSQL)->getResult();
    }

    /**
     * Graphique 2: Répartition des dépenses (donut)
     *
     * @param string $date_debut  Format Y-m-d
     * @param string $date_fin    Format Y-m-d
     * @return object
     */
    public function getRepartitionDepenses($date_debut, $date_fin)
    {
        $date_debut = $this->db->escape($date_debut);
        $date_fin   = $this->db->escape($date_fin);

        $strSQL = "SELECT 
                ROUND(SUM(s.coutCarburant), 2) AS carburant,
                ROUND(SUM(s.coutChauf), 2) AS chauffeurs,
                ROUND(SUM(s.coutAide), 2) AS aides,
                ROUND(SUM(s.coutDisposition), 2) AS disposition
            FROM sanivanday s
            WHERE s.DateJour BETWEEN $date_debut AND $date_fin";

        return $this->db->query($strSQL)->getRow();
    }

    /**
     * Graphique 3: Revenus par type de collecte par mois (stacked bar)
     *
     * @param string $date_debut  Format Y-m-d
     * @param string $date_fin    Format Y-m-d
     * @return array
     */
    public function getRevenuParTypeMois($date_debut, $date_fin)
    {
        $date_debut = $this->db->escape($date_debut);
        $date_fin   = $this->db->escape($date_fin);

        $strSQL = "SELECT 
                DATE_FORMAT(r.datejour, '%Y-%m') AS mois,
                IFNULL(td.Nom, 'Inconnu') AS nom_dechet,
                IFNULL(td.couleur, '#CCCCCC') AS couleur,
                ROUND(SUM(r.total_revenu), 2) AS total
            FROM revenu_jour_camion r
            LEFT JOIN typededechet td ON td.id = r.type_ramassage
            WHERE r.datejour BETWEEN $date_debut AND $date_fin
            GROUP BY DATE_FORMAT(r.datejour, '%Y-%m'), r.type_ramassage, td.Nom, td.couleur
            ORDER BY mois, td.Nom";

        return $this->db->query($strSQL)->getResult();
    }

    /**
     * Graphique 4: Rentabilité par camion (horizontal bar)
     *
     * @param string $date_debut  Format Y-m-d
     * @param string $date_fin    Format Y-m-d
     * @return array
     */
    public function getRentabiliteCamion($date_debut, $date_fin)
    {
        $date_debut = $this->db->escape($date_debut);
        $date_fin   = $this->db->escape($date_fin);

        $strSQL = "SELECT 
                r.nocamion,
                ROUND(SUM(r.total_revenu), 2) AS revenus,
                IFNULL((SELECT ROUND(SUM(s.coutCarburant + s.coutChauf + s.coutAide + s.coutDisposition), 2)
                        FROM sanivanday s
                        WHERE s.NOCAMION = r.nocamion
                          AND s.DateJour BETWEEN $date_debut AND $date_fin
                ), 0) AS depenses
            FROM revenu_jour_camion r
            WHERE r.datejour BETWEEN $date_debut AND $date_fin
            GROUP BY r.nocamion
            ORDER BY (SUM(r.total_revenu) - IFNULL((SELECT SUM(s.coutCarburant + s.coutChauf + s.coutAide + s.coutDisposition)
                        FROM sanivanday s
                        WHERE s.NOCAMION = r.nocamion
                          AND s.DateJour BETWEEN $date_debut AND $date_fin
                ), 0)) DESC";

        return $this->db->query($strSQL)->getResult();
    }

    /**
     * Graphique 5: Évolution du profit net par mois (area chart)
     * Réutilise les mêmes données que getRevenusVsDepensesMois
     *
     * @param string $date_debut
     * @param string $date_fin
     * @return array
     */
    public function getProfitNetMois($date_debut, $date_fin)
    {
        $rows = $this->getRevenusVsDepensesMois($date_debut, $date_fin);
        $result = array();
        foreach ($rows as $row) {
            $obj = new \stdClass();
            $obj->mois = $row->mois;
            $obj->profit = round($row->revenus - $row->depenses, 2);
            $result[] = $obj;
        }
        return $result;
    }

    /**
     * Graphique 6: Coût par tonne (KPI radialBar)
     *
     * @param string $date_debut
     * @param string $date_fin
     * @return object
     */
    public function getCoutParTonne($date_debut, $date_fin)
    {
        $date_debut = $this->db->escape($date_debut);
        $date_fin   = $this->db->escape($date_fin);

        $strSQL = "SELECT 
                ROUND(SUM(r.tonnage), 2) AS total_tonnage,
                ROUND(SUM(r.total_revenu), 2) AS total_revenus,
                IFNULL((SELECT ROUND(SUM(s.coutCarburant + s.coutChauf + s.coutAide + s.coutDisposition), 2)
                        FROM sanivanday s
                        WHERE s.DateJour BETWEEN $date_debut AND $date_fin
                ), 0) AS total_depenses
            FROM revenu_jour_camion r
            WHERE r.datejour BETWEEN $date_debut AND $date_fin";

        $row = $this->db->query($strSQL)->getRow();

        $obj = new \stdClass();
        $obj->total_tonnage  = floatval($row->total_tonnage);
        $obj->total_revenus  = floatval($row->total_revenus);
        $obj->total_depenses = floatval($row->total_depenses);
        $obj->cout_par_tonne = ($obj->total_tonnage > 0) 
            ? round($obj->total_depenses / $obj->total_tonnage, 2) 
            : 0;
        $obj->revenu_par_tonne = ($obj->total_tonnage > 0) 
            ? round($obj->total_revenus / $obj->total_tonnage, 2) 
            : 0;
        $obj->profit_net = round($obj->total_revenus - $obj->total_depenses, 2);

        return $obj;
    }
}
