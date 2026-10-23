<?php

namespace App\Models;

/**
 * Port mecanique de Sanivan_report_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



require_once(APPPATH . 'libraries/Sanivan/CamDayEntity.php');
require_once(APPPATH . 'libraries/Sanivan/CamDayCollection.php');
require_once(APPPATH . 'libraries/Sanivan/SanivanRepository.php');
require_once(APPPATH . 'libraries/Sanivan/SanivanCalculator.php');
require_once(APPPATH . 'libraries/Sanivan/SanivanHtmlBuilder.php');

/**
 * Sanivan_report_model — Orchestrateur du rapport quotidien Sanivan.
 *
 * Version refactorisée de Sanivan_model::getCamDay() et méthodes associées.
 * Sépare les responsabilités :
 *   - SanivanRepository   → accès aux données (SQL)
 *   - SanivanCalculator   → logique métier (calculs)
 *   - SanivanHtmlBuilder  → génération HTML
 *   - CamDayEntity        → entité de données
 *   - CamDayCollection    → agrégation des entités
 *
 * Retourne les mêmes clés de données que l'ancien modèle
 * (rapdaychauf_cam, raprojoursani, IsLoaded) pour compatibilité avec la vue.
 */
class Sanivan_reportModel extends Ci3Model
{
    /** @var SanivanRepository */
    protected $repo;
    /** @var SanivanCalculator */
    protected $calc;
    /** @var SanivanHtmlBuilder */
    protected $html;
    /** @var CamDayCollection */
    protected $collection;
    /** @var object Options système (taux, prix, etc.) */
    protected $optSys;

    public function __construct()
    {
        parent::__construct();
        
        
        $this->cedules = new CedulesModel();

        $this->repo = new SanivanRepository();
        $this->calc = new SanivanCalculator();
        $this->html = new SanivanHtmlBuilder();
        $this->collection = new CamDayCollection();
        $this->optSys = $this->repo->getOptionsSys();
    }

    // ===================================================================
    //  MÉTHODE PRINCIPALE — getCamDay (remplace Sanivan_model::getCamDay)
    // ===================================================================

    /**
     * Construit le rapport quotidien Sanivan.
     *
     * @param string $dateactive Date Y-m-d
     * @return array ['rapdaychauf_cam' => string, 'raprojoursani' => string, 'IsLoaded' => string]
     */
    public function getCamDay($dateactive)
    {
        $data = [
            'IsLoaded'        => '2',
            'rapdaychauf_cam' => '',
            'raprojoursani'   => '',
        ];

        // Filtre optionnel par camion (POST/GET)
        $nocamion = $this->input->post_get('nocamion', TRUE);
        $camionFilter = isset($nocamion) ? $nocamion : null;
        $print = (bool) $this->input->post_get('titre', TRUE);

        // 1. Récupérer les camions du jour
        $camions = $this->repo->getCamionsJour($dateactive, $camionFilter);
        if (empty($camions)) {
            return $data;
        }

        $returnval = '';
        $trIter = 1;

        // 2. Boucle par camion
        foreach ($camions as $rowcam) {
            $cam = $rowcam->NOCAMION;

            // 2a. Vérifier si broker
            $isBroker = $this->repo->isBrokerForCam($cam, $dateactive);

            // 2b. Calculer transport/collecte (sauf broker)
            $transportHours = 0;
            $collecteHours  = 0;
            $tonnage         = 0;
            $hresCam         = 0;

            if (!$isBroker) {
                $transportResult = $this->repo->getTransportData($cam, $dateactive);
                $transportData   = $this->calc->calculateTransportFromRows($transportResult);
                $truckHours      = $this->calc->calculateTruckHours(
                    $transportData['hreDebCam'],
                    $transportData['hreFinCam'],
                    $transportData['collecteHours']
                );

                $transportHours = $truckHours['transport'];
                $collecteHours  = $transportData['collecteHours'];
                $tonnage        = $transportData['tonnage'];
                $hresCam        = $truckHours['hresCam'];
            } else {
                // Calculer le tonnage depuis les villes (même source que buildVilleSection)
                $brokerVilles = $this->repo->getVillesForTruck($dateactive, $cam);
                foreach ($brokerVilles->getResult() as $vRow) {
                    $ton = str_replace(',', '.', $vRow->Tonnage);
                    if (is_numeric($ton)) {
                        $tonnage += floatval($ton);
                    }
                }
            }

            // 2c. Créer l'entité CamDay
            $entity = new CamDayEntity($transportHours, $collecteHours, $hresCam);
            if ($isBroker) {
                $entity->resetExpenses();
            }
            $this->collection->addItem($entity, $cam);

            // 2d. Récupérer la ligne cédule/camion principale
            $sanicamRows = $this->repo->getCeduleForTruck($cam, $dateactive);
            $haveInfo = 1;

            foreach ($sanicamRows as $row) {

                // 2e. Construire la ligne camion
                if ($haveInfo == 1) {
                    if ($row->isbroker == 1) {
                        $entity->resetExpenses();
                        $returnval .= $this->html->buildBrokerTruckRow($row, $rowcam, $trIter, $tonnage);
                    } else {
                        // Carburant
                        $entity->carburant     = $row->Fuel;
                        $entity->coutCarburant = $this->calc->calculateFuelCost($row->Fuel, $this->optSys->montantfuel);

                        $returnval .= $this->html->buildTruckRow(
                            $row, $rowcam, $trIter,
                            $transportHours, $collecteHours, $tonnage
                        );
                    }

                    // 2f. Camion de remplacement
                    $returnval .= $this->buildReplacementSection($cam, $dateactive);

                    $haveInfo++;
                } else {
                    $returnval .= $this->html->buildDuplicateTruckRow($row, $rowcam, $trIter);
                }

                // 2g. Section RH (chauffeur + aides) + Sommaire + Villes
                $returnval .= '</tr>';
                $returnval .= $this->buildRHAndSommaireSection($dateactive, $trIter, $row, $cam, $entity, $print);

                if (!$print) {
                    $returnval .= " <script> \$('#tr_" . $trIter . "').hide(); </script>";
                }

                // 2h. Persister dans sanivanday
                $this->repo->saveSanivanDay($cam, $dateactive, $entity->toDbArray());

                $trIter++;
            }
        }

        // 3. Assembler le résultat
        $data['rapdaychauf_cam'] = trim($returnval);
        $data['raprojoursani']   = $this->html->buildDayReportRow($this->collection->getTotals());

        return $data;
    }

    // ===================================================================
    //  SECTIONS PRIVÉES
    // ===================================================================

    /**
     * Construit la section de remplacement de camion.
     *
     * @param string $cam       NOCAMION
     * @param string $dateactive Date
     * @return string HTML
     */
    private function buildReplacementSection($cam, $dateactive)
    {
        $camEsc = $this->db->escape($cam);
        $dteEsc = $this->db->escape($dateactive);
        $replRows = $this->repo->getReplacementTruck($camEsc, $dteEsc);
        $html = '';

        foreach ($replRows as $rowRepl) {
            // Calculer heures du remplacement
            $replHours = $this->calc->calculateReplacementHours($rowRepl->HreDeb, $rowRepl->HreFin);

            $replEntity = new CamDayEntity($replHours['transport'], 0, $replHours['hresCam']);
            $this->collection->addItem($replEntity, $rowRepl->remplace_cam);

            // Carburant du remplacement
            $replEntity->carburant     = $rowRepl->Fuel;
            $replEntity->coutCarburant = $this->calc->calculateFuelCost($rowRepl->Fuel, $this->optSys->montantfuel);

            $html .= $this->html->buildReplacementRow($rowRepl);
        }

        return $html;
    }

    /**
     * Construit la section RH, le sommaire et les villes.
     *
     * @param string       $dateactive  Date
     * @param int          $trIter      Compteur
     * @param object       $rowCedule   Ligne cédule
     * @param string       $cam         NOCAMION
     * @param CamDayEntity $entity      Entité camion
     * @param bool         $print       Afficher/masquer
     * @return string HTML
     */
    private function buildRHAndSommaireSection($dateactive, $trIter, $rowCedule, $cam, CamDayEntity $entity, $print)
    {
        // Récupérer les données RH
        $rhResult = $this->repo->getRHData($dateactive, $rowCedule);
        $rhRows   = $rhResult->getResult();
        $numRows  = $rhResult->getNumRows();

        // Options type extra
        $typeExtraOptions = $this->repo->getTypeExtraOptions();

        // Calculer les coûts chauffeur/aide à partir des données RH
        $idsanrh = '';
        foreach ($rhRows as $rhRow) {
            $idsanrh = $rhRow->id;
            if ($rhRow->isbroker != 1) {
                if ($rhRow->TypeRh == "0") {
                    // Chauffeur
                    $driverCost = $this->calc->calculateDriverCost(
                        $rhRow->HreDeb, $rhRow->HreFin,
                        $rhRow->HreDeb_extra, $rhRow->HreFin_extra,
                        $this->optSys->salairechauffeur_san
                    );
                    $entity->hresChauf = $driverCost['hours'];
                    $entity->coutChauf = $driverCost['cost'];
                } else {
                    // Aide
                    $aideCost = $this->calc->calculateAideCost(
                        $rhRow->HreDeb, $rhRow->HreFin,
                        $this->optSys->salalaireaide
                    );
                    $entity->hresAide = $aideCost['hours'];
                    $entity->coutAide = $aideCost['cost'];
                }
            }
        }

        // Construire le HTML des lignes RH (sauf broker — le montant est dans l'en-tête camion)
        $rhSection = '';
        if ($rowCedule->isbroker != 1) {
            $rhRowsHtml = '';
            if ($numRows > 0) {
                foreach ($rhRows as $rhRow) {
                    $lineTypeExtra = $this->repo->getTypeExtraOptions($rhRow->TypeExtra);
                    $rhRowsHtml .= $this->buildRHNormalRowHtml($rhRow, $lineTypeExtra);
                }
            } else {
                $rhRowsHtml = '<td colspan="7" align="center">Information des RH sur autre ligne.</td>';
            }
            $rhSection = $this->html->wrapRHSection($rhRowsHtml);
        }

        // Villes
        $villesHtml = $this->buildVilleSection($dateactive, $cam, $entity);

        // Sommaire
        $sommaireHtml = $this->html->buildSommaireSection($trIter, $entity, $idsanrh, $print);

        // Assembler: Sommaire → RH (sauf broker) → Villes
        $result  = $sommaireHtml;
        if ($rowCedule->isbroker != 1) {
            $result .= $rhSection . '</td></tr>';
        }
        $result .= $villesHtml . '</td></tr></tbody></table></td></tr>';

        return $result;
    }

    /**
     * Construit une ligne RH broker (délègue au HtmlBuilder).
     */
    private function buildRHBrokerRowHtml($row)
    {
        $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postRHDayBroker(' . $row->id . ')">
            <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';
        return '<tr id="trligne_' . $row->id . '" style="background-color:#FFF3E0;">
            <td align="center"><label style="width:250px;background-color:#FFA500;color:white;font-weight:bold;padding:2px 8px;border-radius:4px;">' . $row->Chauffeur . '</label></td>
            <td align="center" colspan="5">
                <div class="input-group input-group-sm" style="width:250px;">
                    <span class="input-group-addon" style="font-weight:bold;">Montant $</span>
                    <input class="form-control" style="font-size:14px;font-weight:bold;text-align:right;" type="number" step="0.01"
                        id="montant_' . $row->id . '" name="montant_' . $row->id . '"
                        value="' . $row->montant_forfaitaire . '" placeholder="0.00" />
                </div>
            </td>
            <td>&nbsp;</td>
            <td>' . $post . '</td></tr>';
    }

    /**
     * Construit une ligne RH normal (délègue au HtmlBuilder).
     */
    private function buildRHNormalRowHtml($row, $typeExtraOptions)
    {
        $post = ' <a aria-label="Edit" title="Cliquer pour enregistrer" onclick="postRHDay(' . $row->id . ')">
            <i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a>';

        $html = '<tr id="trligne_' . $row->id . '">
            <td align="center"><label style="width:250px">' . $row->Chauffeur . '</label></td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                    id="HreDeb_' . $row->id . '" name="HreDeb_' . $row->id . '" value="' . $row->HreDeb . '" /></td>
            <td align="center">
                <input class="form-control" style="width: 100px;font-size:14px;" type="time"
                    id="HreFin_' . $row->id . '" name="HreFin_' . $row->id . '" value="' . $row->HreFin . '" />';

        if ($row->TypeRh == "0") {
            $html .= '</td>
                <td align="center">
                    <select class="form-control form-control-modern" id="TypeExtra_' . $row->id . '"
                        style="width:180px;color:red;font-weight:bold;">
                        <option value="">Choisir</option>' . $typeExtraOptions . '
                    </select></td>
                <td align="center">
                    <input class="form-control" style="width: 100px;font-size:14px;font-weight:bold;" type="time"
                        id="HreDeb_extra_' . $row->id . '" name="HreDeb_extra_' . $row->id . '"
                        value="' . $row->HreDeb_extra . '" /></td>
                <td align="center">
                    <input class="form-control" style="width: 100px;font-size:14px;font-weight:bold;" type="time"
                        id="HreFin_extra_' . $row->id . '" name="HreFin_extra_' . $row->id . '"
                        value="' . $row->HreFin_extra . '" /></td>';
        } else {
            $html .= '<td></td><td></td><td></td>';
        }

        $html .= '<td align="center">
                <input class="form-control" type="time" id="HrsTrav_' . $row->id . '"
                    style="width:100px;text-align: center;font-size:14px" value="' . $row->HrsTrav . '" /></td>
            <td>' . $post . '</td></tr>';

        return $html;
    }

    /**
     * Construit la section villes + dépotoires et met à jour l'entité.
     *
     * @param string       $dateactive Date
     * @param string       $cam        NOCAMION
     * @param CamDayEntity $entity     Entité à mettre à jour
     * @return string HTML
     */
    private function buildVilleSection($dateactive, $cam, CamDayEntity $entity)
    {
        $villeResult = $this->repo->getVillesForTruck($dateactive, $cam);
        $tonnageTotal = 0;
        $nbBacTotal   = 0;
        $depot        = null;
        $html         = '';

        foreach ($villeResult->getResult() as $row) {
            // Accumuler tonnage et bacs
            if (is_numeric(str_replace(',', '.', $row->Tonnage))) {
                $tonnageTotal += str_replace(',', '.', $row->Tonnage);
            }
            if (is_numeric($row->nbBac)) {
                $nbBacTotal += $row->nbBac;
            }

            // Récupérer le prix du dépotoire
            $depot = $this->repo->getDepotoirePrix($row->Depotoire);

            // Construire le HTML dépotoire
            $depResult = $this->repo->getDepotoireForVille($row->id);
            $depRows   = $depResult->getResult();
            $depOptions = $this->cedules->GetAllDepotoires($row->Depotoire);
            $depHtml = $this->html->buildDepotoireSection($depRows, $depOptions);

            // Construire la ligne ville
            $html .= $this->html->buildVilleRow($row, $depHtml);
        }

        // Calcul coût disposition
        $coutDispot = 0;
        if (isset($depot) && isset($depot->prix_sani)) {
            $coutDispot = $this->calc->calculateDispositionCost($tonnageTotal, $depot->prix_sani);
        }

        // Mettre à jour l'entité
        $entity->disposition = $tonnageTotal;
        $entity->coutDispot  = $coutDispot;
        $entity->nbBac       = $nbBacTotal;

        return $html;
    }

    // ===================================================================
    //  POST HANDLERS (délèguent au Repository)
    // ===================================================================

    public function PostCamionJour()   { return $this->repo->postCamionJour(); }
    public function postRHDay()        { return $this->repo->postRHDay(); }
    public function postRHDayBroker()  { return $this->repo->postRHDayBroker(); }
    public function postDepDay()       { return $this->repo->postDepDay(); }
    public function postVilleDay()     { return $this->repo->postVilleDay(); }
}
