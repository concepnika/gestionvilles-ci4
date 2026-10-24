<?php

namespace App\Controllers;

use App\Libraries\Ci3Session;
use App\Libraries\KalmanFilter;
use App\Libraries\OsrmSnapper;
use App\Libraries\TabletteHtml;
use App\Libraries\TabletteService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Port de Co_tablette_sani (CI3, ~4 960 lignes).
 *
 * Mêmes URIs que CI3 sous le préfixe /ci4/ — les tablettes ne changent
 * que la base URL. Session partagée CI3 via Ci3Session (lecture ET
 * écriture : le flow login écrit camion/kmdepart/user_id en session).
 *
 * Non porté (reste sur CI3 — voir MIGRATION.md) :
 *  - getInfoCeduleRTE : code mort en CI3 (ne produit rien)
 *  - osrmMatchWithGeometry / osrmMatchChunk : remplacés par OsrmSnapper
 *
 * exportSaniExcel porté sur PhpSpreadsheet (2026-10-24) — PHPExcel est
 * incompatible PHP 8. getSaniReport porté (2026-10-24) : la machinerie
 * CamDay était déjà portée dans SanivanModel.
 */
class TabletteSani extends BaseController
{
    private Ci3Session $ci3;
    private TabletteService $svc;
    private TabletteHtml $html;
    private $latFilter;
    private $lonFilter;

    /** Miroir de $this->data (CI3) — transmis par référence à TabletteHtml. */
    private array $data = [];

    public function __construct()
    {
        $this->ci3 = new Ci3Session();
        $this->svc = new TabletteService();
        $this->html = new TabletteHtml();
        $this->latFilter = new KalmanFilter(0.1, 0.01);
        $this->lonFilter = new KalmanFilter(0.1, 0.01);

        if (! $this->ci3->has('site_lang')) {
            $this->ci3->set('site_lang', 'fr');
        }

        $this->data = [
            'idcamionjour' => '', 'KiloDeb' => '', 'KiloFin' => '', 'Fuel' => '',
            'HreDeb_cam' => '', 'HreFin_cam' => '', 'idcedule' => '',
            'TypeDeProduit' => '', 'couleur' => '', 'NomVille' => '',
            'HreDeb' => '', 'HreFin' => '', 'idsanivanrh' => '', 'idrh' => '',
            'HreDeb_extra' => '', 'HreFin_extra' => '', 'TypeExtra' => '',
            'aides' => '', 'villes' => '', 'camions' => '', 'chauffeurs' => '',
            'nav_tabs' => '', 'tabsville' => '', 'contentville' => '',
            'activeCedule' => 0, 'idville' => '', 'idinfoville' => '',
            'camchauf' => '', 'nocamion' => '', 'latlng' => '', 'center' => '',
            'chauffeursRte' => '', 'allchauffeur' => 0, 'idchauffeur' => 0,
        ];
    }

    /**
     * Port de exportSaniExcel (CI3) : validation Sanivan en Excel.
     * Adaptations PhpSpreadsheet : colonnes 0-based (PHPExcel) ->
     * 1-based ([col, row]), cles de style type/startcolor ->
     * fillType/startColor, chemin ./upload -> webroot CI3.
     */
    public function exportSaniExcel($dateactive)
    {
        $dteactive = $this->db->escape($dateactive);
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $sheet->setTitle('Rapport Sanivan');

        // ---- Styles ----
        $headerStyle = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 11),
            'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb' => '1B3A6B')),
            'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        );
        $summaryStyle = array(
            'font' => array('bold' => true, 'size' => 11),
            'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb' => 'FFFF00')),
            'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        );

        // ---- Titre ----
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'Validation Sanivan - ' . $dateactive);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ---- En-têtes camions (ligne 3) ----
        $headers = array('CHAUFFEUR', 'CAMION', 'KM DÉPART', 'KM FIN', 'KM', 'CARBURANT',
                         'HRE DÉBUT', 'HRE FIN', 'HRES TRAV.', 'TRANSPORT', 'COLLECTE',
                         'DISPOSITION', 'REVENU', 'REV. NET', 'BROKER');
        $col = 0;
        foreach ($headers as $h) {
            $sheet->setCellValue([$col + 1, 3], $h);
            $sheet->getStyle([$col + 1, 3])->applyFromArray($headerStyle);
            $sheet->getColumnDimensionByColumn($col + 1)->setAutoSize(true);
            $col++;
        }

        // ---- Données par camion ----
        $strSQL = "SELECT DISTINCT
                    cj.NOCAMION,
                    (SELECT CONCAT(rh.Prenom, ' ', rh.Nom) FROM reshumaine rh WHERE rh.id = c.idchauffeurs) AS Chauffeur,
                    IFNULL((SELECT rh2.isbroker FROM reshumaine rh2 WHERE rh2.id = c.idchauffeurs), 0) AS isbroker,
                    cj.KiloDeb, cj.KiloFin, (cj.KiloFin - cj.KiloDeb) AS KM,
                    cj.Fuel, cj.HreDeb, cj.HreFin,
                    LEFT(TIMEDIFF(cj.HreFin, cj.HreDeb), 5) AS HrsTrav,
                    IFNULL(sd.hresCam, 0) AS hresCam,
                    IFNULL(sd.carburant, 0) AS sdCarburant,
                    IFNULL(sd.coutCarburant, 0) AS coutCarburant,
                    IFNULL(sd.hresChauf, 0) AS hresChauf,
                    IFNULL(sd.coutChauf, 0) AS coutChauf,
                    IFNULL(sd.hresAide, 0) AS hresAide,
                    IFNULL(sd.coutAide, 0) AS coutAide,
                    IFNULL(sd.disposition, 0) AS disposition,
                    IFNULL(sd.coutDisposition, 0) AS coutDisposition,
                    IFNULL(sd.nbBac, 0) AS nbBac,
                    IFNULL((SELECT srh.montant_forfaitaire FROM sanivan_rh srh
                            WHERE srh.idcedule = c.ID AND srh.TypeRh = 0 LIMIT 1), 0) AS montant_broker
                FROM camionjour cj
                    INNER JOIN cedules c ON c.NOCAMION = cj.NOCAMION AND c.DateJour = cj.DateJour
                    LEFT JOIN sanivanday sd ON sd.NOCAMION = cj.NOCAMION AND sd.DateJour = cj.DateJour
                WHERE cj.DateJour = $dteactive
                    AND (SELECT cam.TypeCamion FROM camions cam WHERE cam.NoCamion = cj.NOCAMION) IN (1,7)
                GROUP BY cj.NOCAMION
                ORDER BY cj.NOCAMION";

        $result = $this->db->query($strSQL);
        $row = 4;

        // Totaux
        $totHresChauf = 0; $totCoutChauf = 0; $totHresAide = 0; $totCoutAide = 0;
        $totHresCam = 0; $totFuel = 0; $totCoutFuel = 0;
        $totTransport = 0; $totCollecte = 0; $totDisposition = 0; $totCoutDispo = 0;
        $totNbBac = 0; $totBroker = 0; $totRevenu = 0; $totNet = 0;

        foreach ($result->getResult() as $r) {
            $transport = floatval($r->hresCam) - floatval($r->disposition);
            if ($transport < 0) $transport = 0;
            $collecteVal = floatval($r->hresCam) - $transport - floatval($r->disposition);
            if ($collecteVal < 0) $collecteVal = 0;

            $revenu = floatval($r->coutChauf) + floatval($r->coutAide) + floatval($r->coutCarburant) + floatval($r->coutDisposition);
            $net = $revenu;

            $sheet->setCellValue([1, $row], $r->Chauffeur);
            $sheet->setCellValue([2, $row], $r->NOCAMION);
            $sheet->setCellValue([3, $row], floatval($r->KiloDeb));
            $sheet->setCellValue([4, $row], floatval($r->KiloFin));
            $sheet->setCellValue([5, $row], floatval($r->KM));
            $sheet->setCellValue([6, $row], floatval($r->Fuel));
            $sheet->setCellValue([7, $row], $r->HreDeb);
            $sheet->setCellValue([8, $row], $r->HreFin);
            $sheet->setCellValue([9, $row], $r->HrsTrav);
            $sheet->setCellValue([10, $row], floatval($r->hresCam));
            $sheet->setCellValue([11, $row], floatval($r->disposition));
            $sheet->setCellValue([12, $row], floatval($r->disposition));
            $sheet->setCellValue([13, $row], floatval($r->coutDisposition));
            $sheet->setCellValue([14, $row], floatval($r->coutDisposition));
            $sheet->setCellValue([15, $row], ($r->isbroker == 1) ? floatval($r->montant_broker) : 0);

            // Accumulate totals
            $totHresChauf += floatval($r->hresChauf);
            $totCoutChauf += floatval($r->coutChauf);
            $totHresAide += floatval($r->hresAide);
            $totCoutAide += floatval($r->coutAide);
            $totHresCam += floatval($r->hresCam);
            $totFuel += floatval($r->Fuel);
            $totCoutFuel += floatval($r->coutCarburant);
            $totDisposition += floatval($r->disposition);
            $totCoutDispo += floatval($r->coutDisposition);
            $totNbBac += intval($r->nbBac);
            $totBroker += ($r->isbroker == 1) ? floatval($r->montant_broker) : 0;

            $row++;
        }

        // ---- Ligne RAPPORT DE LA JOURNÉE ----
        $row += 1;
        $sheet->mergeCells('A' . $row . ':O' . $row);
        $sheet->setCellValue('A' . $row, 'RAPPORT DE LA JOURNÉE');
        $sheet->getStyle('A' . $row)->applyFromArray($summaryStyle);
        $sheet->getStyle('A' . $row)->getFont()->setSize(12);
        $row++;

        $sumHeaders = array('HRES CHAUF', '$ CHAUF', 'HRES AIDE', '$ AIDE', 'HRES CAM',
                            'FUEL', '$ FUEL', 'TRANSPORT', 'COLLECTE', 'DISPOSITION',
                            '$ DISPO', 'NB BACS', '$ BROKER', 'REVENU BRUT', 'REVENU NET');
        $col = 0;
        foreach ($sumHeaders as $sh) {
            $sheet->setCellValue([$col + 1, $row], $sh);
            $sheet->getStyle([$col + 1, $row])->applyFromArray($headerStyle);
            $col++;
        }
        $row++;

        $sumValues = array(
            $totHresChauf, number_format($totCoutChauf, 2, '.', ''),
            $totHresAide, number_format($totCoutAide, 2, '.', ''),
            $totHresCam, $totFuel, number_format($totCoutFuel, 2, '.', ''),
            $totTransport, $totCollecte, $totDisposition,
            number_format($totCoutDispo, 2, '.', ''),
            $totNbBac, number_format($totBroker, 2, '.', ''),
            number_format($totRevenu, 2, '.', ''),
            number_format($totNet, 2, '.', '')
        );
        $col = 0;
        foreach ($sumValues as $sv) {
            $sheet->setCellValue([$col + 1, $row], $sv);
            $sheet->getStyle([$col + 1, $row])->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // ---- Génération du fichier ----
        $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
        $objWriter->setPreCalculateFormulas(false);
        $filename = env('ci3.webroot') . '/upload/rapport_sani_' . $dateactive . '.xlsx';
        $objWriter->save($filename);
        force_download($filename, NULL);
        delete_files($filename);
    }

    /**
     * Port de getSaniReport (CI3) : rapport Sanivan du jour. La
     * machinerie CamDay est déjà portée (SanivanModel::getCamDay,
     * ~850 lignes) ainsi que la vue sanivan/rap_sani_jour — il
     * manquait seulement cette méthode de contrôleur.
     * $from == 0 : la veille de $dateactive ; sinon $dateactive.
     */
    public function getSaniReport($dateactive, $from)
    {
        $sanivan = new \App\Models\SanivanModel();
        if ($from == 0) {
            $date = date_create($dateactive);
            date_sub($date, date_interval_create_from_date_string('1 days'));
            $data = $sanivan->getCamDay(date_format($date, 'Y-m-d'));
            $data['ActiveDate'] = date_format($date, 'Y-m-d');
        } else {
            $data = $sanivan->getCamDay($dateactive);
            $data['ActiveDate'] = $dateactive;
        }
        $data['optChauffeurs'] = $this->html->getOptionChauffeur(0, $this->db->escape($data['ActiveDate']));
        $data['raprojour'] = '';
        return $this->out(view('sanivan/rap_sani_jour', $data));
    }

    //=================================================================
    // Helpers de réponse
    //=================================================================

    private function out(string $body, int $code = 200, string $contentType = 'text/html')
    {
        return $this->response
            ->setStatusCode($code)
            ->setContentType($contentType, 'utf-8')
            ->setBody($body);
    }

    private function outJson($data, int $code = 200)
    {
        return $this->response
            ->setStatusCode($code)
            ->setContentType('application/json', 'utf-8')
            ->setBody(is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function esc($v)
    {
        return $this->db->escape($v);
    }

    private function postGet(string $key, $default = null)
    {
        return $this->request->getPostGet($key) ?? $default;
    }

    private function uploadDir(string $sub = ''): string
    {
        $base = rtrim((string) env('sanivan.uploadDir', ''), '/');
        if ($base === '') {
            $base = rtrim(FCPATH, '/') . '/upload/sanivan';
        }
        return $base . ($sub !== '' ? '/' . ltrim($sub, '/') : '');
    }

    //=================================================================
    // Pages / flow session (vues)
    //=================================================================

    public function index()
    {
        return $this->out('');
    }

    public function getcam()
    {
        return $this->out(view('routes_san/testcam', ['msgerror' => '', 'msgreset' => '']));
    }

    public function TestGoogle()
    {
        return $this->out(view('routes_san/test_google'));
    }

    public function TestSSE()
    {
        return $this->out(view('routes_san/ssecaminfo'));
    }

    public function storeimage()
    {
        $post = (array) $this->request->getPost();
        $img = $_POST['image'] ?? '';
        $folderPath = $this->uploadDir(
            ($post['nocamion'] ?? '') . '/' . ($post['dteactive'] ?? '') . '/' . ($post['idinfoville'] ?? '') . '/'
        );
        if (! is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $image_parts = explode(';base64,', $img);
        $image_base64 = base64_decode($image_parts[1] ?? '');
        $fileName = 'pic_' . date('YmdHis') . '.jpeg';
        file_put_contents($folderPath . '/' . $fileName, $image_base64);
        $relUrl = '/upload/sanivan/' . ($post['nocamion'] ?? '') . '/' . ($post['dteactive'] ?? '') . '/' . ($post['idinfoville'] ?? '') . '/' . $fileName;
        $this->ci3->set('urlTicket', $relUrl);
        return $this->out(view('confirme/confirme', ['test' => '']));
    }

    public function DownloadFile($file_path = '')
    {
        // CI3 : force_download(basename($file_path), NULL) — fichier vide
        // téléchargé sous le nom du ticket (le vrai fichier est récupéré
        // par la tablette via l'URL /upload/sanivan/... servie par CI3).
        $name = basename((string) $file_path);
        return $this->response->download($name, '')->setFileName($name);
    }

    public function scanBillet($idinfoville, $deportoire, $dteActive, $iddepotoire)
    {
        $data = [
            'idinfoville' => $idinfoville,
            'depotoire' => str_replace('%20', ' ', $deportoire),
            'iddepotoire' => $iddepotoire,
            'nocamion' => $this->ci3->get('camion'),
            'dteActive' => $dteActive,
        ];
        return $this->out(view('routes_san/scanticket', $data));
    }

    public function logout()
    {
        $this->ci3->destroy();
        $this->response
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT');
        return $this->pageLogin();
    }

    public function pageLogin()
    {
        $data = [
            'msgerror' => '',
            'camion' => $this->html->getAllCamions('', $this->data),
            'chauffeurs' => $this->html->getChauffeurs($this->data),
            'ActiveDate' => date('Y-m-d'),
        ];
        return $this->out(view('routes_san/loginsan', $data));
    }

    public function pageAddLogin($dte)
    {
        $data = [
            'msgerror' => '',
            'camion' => $this->html->getAllCamions('', $this->data),
            'chauffeurs' => $this->html->getChauffeurs($this->data),
            'ActiveDate' => $dte,
        ];
        return $this->out(view('routes_san/loginforadd', $data));
    }

    public function addChauffeur()
    {
        $date = $this->esc($this->postGet('dtetoadd'));
        $code = $this->postGet('code');
        $this->ci3->set('camion', $this->postGet('edtcamion'));
        $this->ci3->set('kmdepart', $this->postGet('edtkmDepart'));
        $message_error = 'Mot de passe ou courriel erronn&eacute;.';
        $render = null;

        if (isset($code)) {
            $result = $this->db->query('SELECT * FROM reshumaine WHERE login = ' . $this->esc($code))->getRow();
            if (! empty($result)) {
                $strSQL = "SELECT cedules.ID, cedules.TypeDeProduit, cedules.NOCAMION, DateJour,
                   CASE TypeDeProduit
                     WHEN 'ORGANIQUE' THEN '#ebccaa'
                     WHEN 'RECYCLAGE' THEN '#9FF3F9'
                     WHEN 'DECHETS' THEN '#E5E5E5'
                     WHEN 'RESIDUS_VERT' THEN '#2DBC96'
                     WHEN 'GROS_REBUTS' THEN '#2B3B7AF'
                     WHEN 'BRANCHES' THEN '#D1B546'
                     WHEN 'FEUILLES' THEN '#2DBC96'
                     ELSE '#F7F1C5'
                   END AS couleur, HELPERS_1, HELPERS_2, HELPERS_3, HELPERS_4,
                        cedules.idchauffeurs, cedules.idvilles, cedules.remplace_cam, cedules.idremplace_chauf,
                        (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille
                   FROM cedules
                   WHERE cedules.DateJour = $date AND cedules.idchauffeurs = " . (int) $result->id;
                $res = $this->db->query($strSQL);
                if ($res->getNumRows() == 0) {
                    $message_error = 'Vous n\'&ecirc;tes pas cedul&eacute; pour aujourd\'hui consulter votre superviseur.';
                } else {
                    $cedule = $res->getRow();
                    $chauf = $cedule->idchauffeurs;
                    if ($cedule->idremplace_chauf > 0) {
                        $chauf = $cedule->idremplace_chauf;
                    }
                    $this->ci3->set('nom', $result->Prenom . ' ' . $result->Nom);
                    $this->ci3->set('email', '');
                    $this->ci3->set('user_id', $chauf);
                    $this->ci3->set('login_in', true);
                    $this->ci3->set('idchauffeur', $chauf);
                    return $this->validChauffeur($result->id, 2, (string) $this->postGet('dtetoadd'));
                }
            }
        }
        $data = [
            'msgerror' => $message_error,
            'camion' => $this->html->getAllCamions('', $this->data),
            'chauffeurs' => $this->html->getChauffeurs($this->data),
            'ActiveDate' => $this->postGet('dtetoadd'),
        ];
        return $this->out(view('routes_san/loginforadd', $data));
    }

    public function loginChauffeur()
    {
        $code = $this->postGet('code');
        $this->ci3->set('camion', $this->postGet('edtcamion'));
        $this->ci3->set('kmdepart', $this->postGet('edtkmDepart'));
        $message_error = 'Mot de passe ou courriel erronn&eacute;.';

        if (isset($code)) {
            $result = $this->svc->validateChauffeur(
                (string) $this->postGet('code'),
                (string) $this->postGet('password')
            );
            if (! empty($result)) {
                if ($this->postGet('from') == 1) {
                    $date = $this->esc($this->postGet('dtetoadd'));
                } else {
                    $date = $this->esc(date('Y-m-d'));
                }
                $strSQL = "SELECT cedules.ID, cedules.TypeDeProduit, cedules.NOCAMION, DateJour,
                   CASE TypeDeProduit
                     WHEN 'ORGANIQUE' THEN '#ebccaa'
                     WHEN 'RECYCLAGE' THEN '#9FF3F9'
                     WHEN 'DECHETS' THEN '#E5E5E5'
                     WHEN 'RESIDUS_VERT' THEN '#2DBC96'
                     WHEN 'GROS_REBUTS' THEN '#2B3B7AF'
                     WHEN 'BRANCHES' THEN '#D1B546'
                     WHEN 'FEUILLES' THEN '#2DBC96'
                     ELSE '#F7F1C5'
                   END AS couleur, HELPERS_1, HELPERS_2, HELPERS_3, HELPERS_4,
                        cedules.idchauffeurs, cedules.idvilles, cedules.idsecteur, cedules.remplace_cam, cedules.idremplace_chauf,
                        (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille
                   FROM cedules
                   WHERE cedules.DateJour = $date AND cedules.idchauffeurs = " . (int) $result->id;
                $res = $this->db->query($strSQL);
                if ($res->getNumRows() == 0) {
                    $message_error = 'Vous n\'&ecirc;tes pas cedul&eacute; pour aujourd\'hui consulter votre superviseur.';
                } else {
                    $cedule = $res->getRow();
                    $chauf = $cedule->idchauffeurs;
                    if ($cedule->idremplace_chauf > 0) {
                        $chauf = $cedule->idremplace_chauf;
                    }
                    $this->ci3->set('nom', $result->Prenom . ' ' . $result->Nom);
                    $this->ci3->set('email', '');
                    $this->ci3->set('user_id', $chauf);
                    $this->ci3->set('login_in', true);
                    $this->ci3->set('idchauffeur', $chauf);
                    if ($this->postGet('from') == 2) {
                        return $this->out(view('routes_san/location_sani', ['center' => '', 'parcours' => '']));
                    }
                    return $this->validChauffeur($result->id, 0);
                }
            }
        }
        $data = [
            'msgerror' => $message_error,
            'camion' => $this->html->getAllCamions('', $this->data),
            'chauffeurs' => $this->html->getChauffeurs($this->data),
            'ActiveDate' => date('Y-m-d'),
        ];
        return $this->out(view('routes_san/loginsan', $data));
    }

    public function showTabletteMap()
    {
        // CI3 charge la vue sans données ($center vide → JS no-op)
        return $this->out(view('routes_san/location_sani', ['center' => '', 'parcours' => '']));
    }

    public function gotoPage()
    {
        // chekSession n'existe plus en CI3 — on lit la session directement
        return $this->validChauffeur((int) $this->ci3->get('user_id'));
    }

    public function validChauffeur($idchauffeur, $from = 0, $dteactive = '')
    {
        $date = '';
        if ($dteactive == '') {
            $date = date_create(date('Y-m-d'));
            $this->getValidChauffeurJour($date->format('Y-m-d'), $idchauffeur);
        } else {
            $this->getValidChauffeurJour($dteactive, $idchauffeur);
        }
        $data = $this->data;
        $data['villes'] = $this->html->getInfoVille('', $data['activeCedule'] ?? 0, $this->data);
        $data['idville'] = $this->data['idville'] ?? '';
        $data['idinfoville'] = $this->data['idinfoville'] ?? '';
        $data['msgerror'] = '';
        $data['ActiveDate'] = ($dteactive != '') ? $dteactive : $date->format('Y-m-d');
        $data['nom'] = $this->ci3->get('nom');
        $data['email'] = $this->ci3->get('email');
        $data['camion'] = $this->ci3->get('camion');
        $data['kmdepart'] = $this->ci3->get('kmdepart');
        $data['typeextra'] = $this->html->getTypeExtra($this->data['TypeExtra'] ?? '');
        if ($from == '1') {
            return $this->out((string) $data['villes']);
        }
        return $this->out(view('routes_san/routes_san', $data));
    }

    public function setChauffeurOnRoad($dteactive, $idchauffeur)
    {
        $dteesc = $this->esc($dteactive);
        $idchauffeur = (int) $idchauffeur;
        $strSQL = "SELECT cedules.ID, cedules.NOCAMION, cedules.TypeDeProduit, reshumaine.login,
                reshumaine.Nom, reshumaine.Prenom, cedules.idchauffeurs
                FROM cedules INNER JOIN reshumaine ON reshumaine.id = cedules.idchauffeurs
                WHERE cedules.DateJour = $dteesc AND cedules.idchauffeurs = $idchauffeur";
        $chauf = $this->db->query($strSQL)->getRow();
        if ($chauf) {
            $this->ci3->set('nom', $chauf->Prenom . ' ' . $chauf->Nom);
            $this->ci3->set('email', '');
            $this->ci3->set('user_id', $chauf->idchauffeurs);
            $this->ci3->set('login_in', true);
            $this->ci3->set('idchauffeur', $chauf->idchauffeurs);
            $camj = $this->db->query(
                "SELECT * FROM camionjour WHERE DateJour = $dteesc AND idchauffeur = $idchauffeur AND idcedule = " . $chauf->ID
            )->getRow();
            if (! $camj) {
                $NoCam = $this->esc($chauf->NOCAMION);
                $produit = $this->esc($chauf->TypeDeProduit);
                $this->db->query("INSERT INTO camionjour (idchauffeur, idcedule, NOCAMION, TypeProduit, DateJour)
                    VALUES ($idchauffeur, {$chauf->ID}, $NoCam, $produit, $dteesc)");
            }
            $this->validChauffeur($chauf->idchauffeurs, 0, $dteactive);
            return $this->out('OK');
        }
        return $this->out('');
    }

    //=================================================================
    // Fragments HTML (ramassages, dépotoires)
    //=================================================================

    public function getVilleForSani($idville, $idcedule)
    {
        return $this->out($this->html->getInfoVille($idville, $idcedule, $this->data));
    }

    public function getNewDepot($idville, $idcedule)
    {
        return $this->out($this->html->getNewDepotHtml((int) $idville, (int) $idcedule, $this->data));
    }

    public function getInfoCeduleRTE($idcedule)
    {
        // Code mort en CI3 (la méthode requête puis n'affiche rien)
        $this->db->query(
            "SELECT cedules.ID FROM cedules INNER JOIN camionjour ON cedules.ID = camionjour.idcedule
             WHERE cedules.ID = " . (int) $idcedule . " GROUP BY cedules.ID ORDER BY cedules.NOCAMION, cedules.ID"
        );
        return $this->out('');
    }

    //=================================================================
    // POST infos (camion, dépotoire, chauffeur, aide, ville)
    //=================================================================

    public function saveToFile()
    {
        $post = (array) $this->request->getPost();
        $filename = 'pic_' . date('YmdHis') . '.jpeg';
        $sub = ($post['nocamion'] ?? '') . '/' . ($post['dteactive'] ?? '') . '/' . ($post['idinfoville'] ?? '');
        $dir = $this->uploadDir($sub);
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $this->request->getFile('image');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $file->move($dir, $filename);
            $rel = '/upload/sanivan/' . $sub . '/' . $filename;
            $this->ci3->set('urlTicket', $rel);
            return $this->out($rel);
        }
        return $this->out('');
    }

    public function postInfoCamion($NoCamion, $KiloDeb, $KiloFin, $Fuel, $dteActive, $idcamjour, $hredeb, $hrefin)
    {
        $NoCamion = $this->esc(urldecode($NoCamion));
        $KiloDeb = str_replace(',', '.', $this->esc(urldecode($KiloDeb)));
        $KiloFin = str_replace(',', '.', $this->esc(urldecode($KiloFin)));
        $Fuel = str_replace(',', '.', $this->esc(urldecode($Fuel)));
        $idcamjour = $this->esc($idcamjour);
        $hredeb = $this->esc(urldecode($hredeb));
        $hrefin = ($hrefin == 'none') ? 'NULL' : $this->esc(urldecode($hrefin));
        $this->db->query("UPDATE camionjour
            SET NOCAMION=$NoCamion, KiloDeb=$KiloDeb, KiloFin=$KiloFin,
                HreDeb=$hredeb, HreFin=$hrefin, Fuel=$Fuel
            WHERE id = $idcamjour");
        $error = $this->db->error();
        $dataResult = [];
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        $dataResult['id'] = $this->db->insertID();
        return $this->out((string) $dataResult['id']);
    }

    public function postInfoDepotoire($id, $Tonnage, $Depotoire, $boite, $istransbo, $IsAvecMat)
    {
        $id = $this->esc($id);
        $Ticket = $this->esc($this->ci3->get('urlTicket'));
        $Tonnage = str_replace(',', '.', $this->esc(urldecode($Tonnage)));
        $Depotoire = $this->esc(urldecode($Depotoire));
        $boite = ($boite == 'none') ? '' : ',boite=' . $this->esc(urldecode($boite));
        $istransbo = (int) $istransbo;
        $IsAvecMat = (int) $IsAvecMat;
        $this->db->query("UPDATE info_depotoire
            SET IsTransBo=$istransbo, IsAvecMat=$IsAvecMat, Ticket=$Ticket,
                Tonnage=$Tonnage, Depotoire=$Depotoire $boite
            WHERE id = $id");
        $error = $this->db->error();
        $dataResult = [];
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        $dataResult['ticket'] = '<span class="input-group-btn">
                          <a href="' . $this->ci3->get('urlTicket') . '" target="_blank" class="form-control" style="font-size:16px;" download>
                                        <i class="fa fa-download btn btn-success" style="font-size:16px;height:30px;">
                                  </i>&nbsp;&nbsp;Voir le BILLET</a>
                          </span>';
        return $this->outJson($dataResult);
    }

    public function postInfoChauffeur($idchauffeur, $HreDeb, $HreFin, $TypeRh, $dteActive, $idsanivanrh)
    {
        $idchauffeur = $this->esc($idchauffeur);
        $HreDeb = $this->esc(urldecode($HreDeb));
        $HreFin = $this->esc(urldecode($HreFin));
        $TypeRh = $this->esc($TypeRh);
        $idsanivanrh = $this->esc($idsanivanrh);
        $result = $this->db->query(
            "SELECT id FROM sanivan_rh WHERE id = $idsanivanrh AND TypeRh = $TypeRh AND idrh = $idchauffeur"
        )->getRow();
        if ($result) {
            $this->db->query("UPDATE sanivan_rh
                SET idrh=$idchauffeur, HreDeb=$HreDeb, HreFin=$HreFin
                WHERE id = $idsanivanrh");
        }
        $error = $this->db->error();
        $dataResult = [];
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        return $this->out((string) ($dataResult['id'] ?? ''));
    }

    public function postInfoExtra($idchauffeur, $HreDeb, $HreFin, $TypeRh, $typeextra, $idsanivanrh)
    {
        $idchauffeur = $this->esc($idchauffeur);
        $HreDeb = $this->esc(urldecode($HreDeb));
        $HreFin = $this->esc(urldecode($HreFin));
        $TypeRh = $this->esc($TypeRh);
        $typeextra = $this->esc($typeextra);
        $idsanivanrh = $this->esc($idsanivanrh);
        $result = $this->db->query(
            "SELECT id FROM sanivan_rh WHERE id = $idsanivanrh AND TypeRh = $TypeRh AND idrh = $idchauffeur"
        )->getRow();
        if ($result) {
            $this->db->query("UPDATE sanivan_rh
                SET idrh=$idchauffeur, HreDeb_extra=$HreDeb, HreFin_extra=$HreFin, TypeExtra=$typeextra
                WHERE id = $idsanivanrh");
        }
        $error = $this->db->error();
        $dataResult = [];
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        return $this->out((string) ($dataResult['id'] ?? ''));
    }

    public function postInfoAide($idaide, $HreDeb, $HreFin, $TypeRh, $dteActive, $idsanivanrh)
    {
        $idaide = $this->esc($idaide);
        $HreDeb = $this->esc(urldecode($HreDeb));
        $HreFin = $this->esc(urldecode($HreFin));
        $idsanivanrh = $this->esc($idsanivanrh);
        $this->db->query("UPDATE sanivan_rh
            SET idrh=$idaide, HreDeb=$HreDeb, HreFin=$HreFin
            WHERE id = $idsanivanrh");
        $error = $this->db->error();
        $dataResult = [];
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        $dataResult['id'] = $this->db->insertID();
        return $this->out((string) $dataResult['id']);
    }

    public function postInfoVille($idinfoville, $HreDeb, $HreFin, $nbbac)
    {
        $idinfoville = $this->esc($idinfoville);
        $nbbac = ($nbbac == 0) ? 'NULL' : $this->esc($nbbac);
        $HreDeb = $this->esc(urldecode($HreDeb));
        $HreFin = $this->esc(urldecode($HreFin));
        $this->db->query("UPDATE info_villes
            SET HreDeb=$HreDeb, HreFin=$HreFin, nbBac=$nbbac
            WHERE id = $idinfoville");
        $error = $this->db->error();
        $dataResult = [];
        if (! empty($error['code'])) {
            $dataResult['msgerr'] = "Une erreur s'est produite.";
            $dataResult['id'] = 0;
        } else {
            $dataResult['msgerr'] = "Vos changements ont &eacute;t&eacute; enregistr&eacute;s.";
        }
        return $this->out((string) ($dataResult['id'] ?? ''));
    }

    public function postLocation()
    {
        $track_lat = $this->esc($this->postGet('track_lat'));
        $track_lng = $this->esc($this->postGet('track_lng'));
        $nocamion = $this->esc($this->postGet('nocamion'));
        $idchauffeur = $this->esc($this->postGet('idchauffeur'));
        $adresse = $this->esc($this->postGet('Adresse'));
        $Statutpoly = $this->esc($this->postGet('Statutpoly'));
        $IsInZone = $this->esc($this->postGet('IsInZone'));
        $this->db->query("INSERT INTO gps_sani
            (idchauffeur, nocamion, track_time, track_lng, track_lat, adresse, Statutpoly, IsInZone)
            VALUES ($idchauffeur, $nocamion, NOW(), $track_lng, $track_lat, $adresse, $Statutpoly, $IsInZone)");
        $this->db->query("INSERT INTO gps_sani_last
            (nocamion, idchauffeur, track_time, track_lng, track_lat, adresse, Statutpoly, IsInZone)
            VALUES ($nocamion, $idchauffeur, NOW(), $track_lng, $track_lat, $adresse, $Statutpoly, $IsInZone)
            ON DUPLICATE KEY UPDATE
              idchauffeur=VALUES(idchauffeur), track_time=VALUES(track_time),
              track_lng=VALUES(track_lng), track_lat=VALUES(track_lat),
              adresse=VALUES(adresse), Statutpoly=VALUES(Statutpoly), IsInZone=VALUES(IsInZone)");
        return $this->out('');
    }

    //=================================================================
    // Divers data / options
    //=================================================================

    public function GetTypeExtra($typeext = '')
    {
        return $this->out($this->html->getTypeExtra($typeext));
    }

    public function GetCamionAdresse($camion, $dateactive)
    {
        $camion = $this->esc($camion);
        $dateStart = $this->esc($dateactive . ' 00:00:00');
        $dateEnd = $this->esc($dateactive . ' 23:59:59');
        $returnval = '';
        $strSQL = "SELECT id, adresse FROM gps_sani
            WHERE track_time BETWEEN $dateStart AND $dateEnd AND nocamion = $camion
              AND IsInZone = 1 ORDER BY track_time";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            $returnval .= '<option  value="' . $row->id . '" >' . $row->adresse . '</option>';
        }
        return $this->out($returnval);
    }

    public function GetAdresseToFind($camion, $dateactive, $findstr)
    {
        $findstr = str_replace(' ', '%', strtolower(urldecode($findstr)));
        $findstr = $this->esc('%' . $findstr . '%');
        $camion = $this->esc($camion);
        $dateStart = $this->esc($dateactive . ' 00:00:00');
        $dateEnd = $this->esc($dateactive . ' 23:59:59');
        $returnval = '';
        $strSQL = "SELECT id, adresse, track_time FROM gps_sani
            WHERE track_time BETWEEN $dateStart AND $dateEnd AND nocamion = $camion
              AND IsInZone = 1 AND LOWER(adresse) LIKE $findstr ORDER BY track_time";
        foreach ($this->db->query($strSQL)->getResult() as $row) {
            $returnval .= '<option value="' . $row->id . '">Date: ' . $row->track_time . ': ' . $row->adresse . '</Option>';
        }
        return $this->out($returnval);
    }

    public function showAdresse($id)
    {
        $id = (int) $id;
        $data = [];
        $result = $this->db->query(
            "SELECT id, track_time, track_lng, track_lat, adresse, IsInZone FROM gps_sani WHERE id = $id"
        );
        foreach ($result->getResult() as $row) {
            $data['hre'] = $row->track_time;
            $data['adresse'] = $row->adresse;
            $data['lng'] = str_replace(',', '.', (string) $row->track_lng);
            $data['lat'] = str_replace(',', '.', (string) $row->track_lat);
        }
        return $this->outJson($data);
    }

    public function getAdresseToShow()
    {
        $lat = $this->esc($this->postGet('lat'));
        $lng = $this->esc($this->postGet('lng'));
        $nocamion = $this->esc($this->postGet('nocamion'));
        $rawDate = (string) $this->postGet('dteactive');
        $dateStart = $this->esc($rawDate . ' 00:00:00');
        $dateEnd = $this->esc($rawDate . ' 23:59:59');
        $data = [];
        $result = $this->db->query(
            "SELECT id, nocamion, track_time, track_lng, track_lat, adresse, IsInZone
             FROM gps_sani WHERE track_time BETWEEN $dateStart AND $dateEnd
               AND track_lat = $lat AND track_lng = $lng AND nocamion = $nocamion"
        );
        foreach ($result->getResult() as $row) {
            $data['hre'] = $row->track_time;
            $data['adresse'] = $row->adresse;
            $data['IsInZone'] = (int) $row->IsInZone;
        }
        return $this->outJson($data);
    }

    //=================================================================
    // Polylines dynamiques
    //=================================================================

    public function getPolylineDynamiqueForAll($idchauffeur, $dateactive, $nocamion): array
    {
        $segmentsOut = [];
        $dateStart = $this->esc($dateactive . ' 00:00:00');
        $dateEnd = $this->esc($dateactive . ' 23:59:59');
        $camEsc = $this->esc($nocamion);
        $chFilter = ((int) $idchauffeur > 0) ? ' AND g.idchauffeur = ' . $this->esc($idchauffeur) : '';
        $baseSql = "SELECT g.id, g.idchauffeur, g.track_lng, g.track_lat, g.track_time, g.adresse,
                CONCAT(UPPER(TRIM(r.NOM)), ' ', UPPER(TRIM(r.PRENOM))) AS chauffeur
           FROM gps_sani g
           LEFT JOIN reshumaine r ON r.id = g.idchauffeur
          WHERE g.track_time BETWEEN $dateStart AND $dateEnd
            AND g.nocamion = $camEsc
            AND (g.IsInZone = 1 OR (g.Statutpoly = 0 AND g.IsInZone = 1))";
        $q = $this->db->query($baseSql . $chFilter . ' ORDER BY g.track_time');
        if ($q->getNumRows() === 0 && $chFilter !== '') {
            $q = $this->db->query($baseSql . ' ORDER BY g.track_time');
        }
        if ($q->getNumRows() < 2) {
            return [];
        }
        $buf = '';
        foreach ($q->getResult() as $row) {
            $buf .= str_replace(',', '.', (string) $row->track_lat) . ',' .
                str_replace(',', '.', (string) $row->track_lng) .
                ', timespan: ' . $row->track_time .
                ', chauffeur: ' . $row->chauffeur .
                ', adresse: ' . $row->adresse . '_';
        }
        if ($buf !== '') {
            $segmentsOut[] = substr($buf, 0, -1);
        }
        return $segmentsOut;
    }

    public function getPolylineDynamiqueForAllWithoutZoneFilter($idchauffeur, $dateactive, $nocamion): array
    {
        $segmentsOut = [];
        $dateStart = $this->esc($dateactive . ' 00:00:00');
        $dateEnd = $this->esc($dateactive . ' 23:59:59');
        $camEsc = $this->esc($nocamion);
        $chFilter = ((int) $idchauffeur > 0) ? ' AND g.idchauffeur = ' . $this->esc($idchauffeur) : '';
        $baseSql = "SELECT g.id, g.idchauffeur, g.track_lng, g.track_lat, g.track_time, g.adresse, g.IsInZone,
                CONCAT(UPPER(TRIM(r.NOM)), ' ', UPPER(TRIM(r.PRENOM))) AS chauffeur
           FROM gps_sani g
           LEFT JOIN reshumaine r ON r.id = g.idchauffeur
          WHERE g.track_time BETWEEN $dateStart AND $dateEnd
            AND g.nocamion = $camEsc";
        $q = $this->db->query($baseSql . $chFilter . ' ORDER BY g.track_time');
        if ($q->getNumRows() === 0 && $chFilter !== '') {
            $q = $this->db->query($baseSql . ' ORDER BY g.track_time');
        }
        if ($q->getNumRows() < 2) {
            return [];
        }
        $buf = '';
        foreach ($q->getResult() as $row) {
            $buf .= str_replace(',', '.', (string) $row->track_lat) . ',' .
                str_replace(',', '.', (string) $row->track_lng) .
                ', timespan: ' . $row->track_time .
                ', chauffeur: ' . $row->chauffeur .
                ', adresse: ' . $row->adresse .
                ', isinzone: ' . (int) $row->IsInZone . '_';
        }
        if ($buf !== '') {
            $segmentsOut[] = substr($buf, 0, -1);
        }
        return $segmentsOut;
    }

    public function getPolylineDynamique($idchauffeur, $dateactive, $nocamion): array
    {
        $segmentsOut = [];
        $dateStart = $this->esc($dateactive . ' 00:00:00');
        $dateEnd = $this->esc($dateactive . ' 23:59:59');
        $camEsc = $this->esc($nocamion);
        $chFilter = ((int) $idchauffeur > 0) ? ' AND g.idchauffeur = ' . $this->esc($idchauffeur) : '';
        $selectGps = "SELECT g.id, g.idchauffeur, g.track_lng, g.track_lat, g.track_time, g.adresse,
                        CONCAT(UPPER(TRIM(r.NOM)), ' ', UPPER(TRIM(r.PRENOM))) AS chauffeur
                   FROM gps_sani g
                   LEFT JOIN reshumaine r ON r.id = g.idchauffeur
                  WHERE g.track_time BETWEEN $dateStart AND $dateEnd
                    AND g.nocamion = $camEsc
                    AND g.IsInZone = 1";
        $q = $this->db->query($selectGps . $chFilter . ' ORDER BY g.track_time');
        if ($q->getNumRows() === 0 && $chFilter !== '') {
            $q = $this->db->query($selectGps . ' ORDER BY g.track_time');
        }
        if ($q->getNumRows() < 2) {
            return [];
        }
        $buf = '';
        foreach ($q->getResult() as $row) {
            $buf .= str_replace(',', '.', (string) $row->track_lat) . ',' . str_replace(',', '.', (string) $row->track_lng)
                . ', timespan: ' . $row->track_time . ', chauffeur: ' . $row->chauffeur . ', adresse: ' . $row->adresse . '_';
        }
        if ($buf !== '') {
            $segmentsOut[] = substr($buf, 0, -1);
        }
        return $segmentsOut;
    }

    public function GetPolylineDynamiquePublic($idchauffeur, $dateactive, $nocamion, $snap = 0): array
    {
        $segments = $this->getPolylineDynamique($idchauffeur, $dateactive, $nocamion);
        if (! is_array($segments) || empty($segments)) {
            return [];
        }
        if ((int) $snap !== 1) {
            return $segments;
        }
        $snapped = (new OsrmSnapper())->snapSegments($segments);
        if (! is_string($snapped) || $snapped === '') {
            return $segments;
        }
        $out = explode('|', $snapped);
        return is_array($out) ? $out : $segments;
    }

    public function GetCamionPolyline($dateactive)
    {
        $idchauffeur = $this->postGet('idchauffeur');
        $nocamion = $this->postGet('nocamion');
        $snap = (int) $this->postGet('snap');
        $segments = $this->getPolylineDynamique($idchauffeur, $dateactive, $nocamion);
        if (! is_array($segments) || empty($segments)) {
            return $this->out('');
        }
        if ($snap === 1) {
            return $this->out((new OsrmSnapper())->snapSegments($segments));
        }
        return $this->out(rtrim(implode('|', $segments), '|'));
    }

    public function GetCamionPolylineAll($dateactive)
    {
        @set_time_limit(120);
        @ini_set('memory_limit', '256M');
        $nocamion = $this->request->getGet('nocamion');
        $idchauffeur = $this->request->getGet('idchauffeur');
        if ($nocamion === null || trim((string) $nocamion) === '') {
            return $this->out('');
        }
        $segments = $this->getPolylineDynamiqueForAllWithoutZoneFilter($idchauffeur ?? 0, $dateactive, $nocamion);
        if (! is_array($segments) || empty($segments)) {
            return $this->out('');
        }
        $snap = new OsrmSnapper();
        $outSegs = [];
        foreach ($segments as $seg) {
            [$pts, $metas, $timestamps, $chauffeur] = $snap->parseSegmentChauffeur($seg);
            if (empty($pts)) {
                $outSegs[] = $seg;
                continue;
            }
            $chunks = $snap->splitByGapsPublic($pts, $timestamps, 200, 120, 160, 50);
            if (empty($chunks)) {
                $outSegs[] = $seg;
                continue;
            }
            foreach ($chunks as $chunk) {
                $cPts = $chunk[0];
                $cIdx = $chunk[2] ?? null;
                if (count($cPts) < 2) {
                    continue;
                }
                $rebuilt = [];
                for ($i = 0; $i < count($cPts); $i++) {
                    $lat = number_format($cPts[$i][0], 6, '.', '');
                    $lng = number_format($cPts[$i][1], 6, '.', '');
                    $origIdx = ($cIdx !== null && isset($cIdx[$i])) ? $cIdx[$i] : $i;
                    $mraw = $metas[$origIdx] ?? '';
                    $rebuilt[] = $lat . ',' . $lng . $mraw;
                }
                $outSegs[] = implode('_', $rebuilt);
            }
        }
        return $this->out(rtrim(implode('|', $outSegs), '|'));
    }

    public function GetDayRoad($dateactive)
    {
        $idchauffeur = $this->postGet('idchauffeur');
        if ($this->postGet('allchauffeur') == 1) {
            $data = $this->html->getCamOnRoad($dateactive, 0, $this->data);
        } else {
            $data = $this->html->getCamOnRoad($dateactive, $idchauffeur, $this->data);
        }
        $data['from'] = $this->postGet('from');
        $data['ActiveDate'] = $dateactive;
        $data['allchauffeur'] = $this->postGet('allchauffeur');
        $data['typeextra'] = '';
        $data['privilege'] = '';
        $data['newzoom'] = '';
        if ($this->postGet('from') == 1) {
            $data['locations'] = '';
            if (($data['allchauffeur'] == 0) && ($idchauffeur == 23)) {
                $data['locations'] = $this->getPolylineDynamique($idchauffeur, $dateactive, $data['nocamion'] ?? '');
            }
            return $this->outJson($data);
        }
        return $this->out(view('leaflet/map_nav_all', $data));
    }

    public function getPolylineTest()
    {
        $returnval = '';
        $result = $this->db->query(
            'SELECT id, idville, idsecteur, nom, parcours, parcours_reduit, urlfile FROM polilyne_ville WHERE id = 1'
        )->getRow();
        if (! $result) {
            return $this->out('');
        }
        $arrPoints = explode('_', (string) $result->parcours_reduit);
        for ($iter = 0; $iter < count($arrPoints); $iter++) {
            $returnval .= '[' . $arrPoints[$iter] . '],';
        }
        return $this->out(substr($returnval, 0, -1));
    }

    //=================================================================
    // Endpoints carte / JSON
    //=================================================================

    public function GetBridgeRoute()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        $fromLat = $this->request->getGet('fromLat');
        $fromLng = $this->request->getGet('fromLng');
        $toLat = $this->request->getGet('toLat');
        $toLng = $this->request->getGet('toLng');

        if ($from && $to) {
            $fa = explode(',', $from);
            $ta = explode(',', $to);
            if (count($fa) === 2 && count($ta) === 2) {
                $fromLat = $fa[0];
                $fromLng = $fa[1];
                $toLat = $ta[0];
                $toLng = $ta[1];
            }
        }
        if (! is_numeric($fromLat) || ! is_numeric($fromLng) || ! is_numeric($toLat) || ! is_numeric($toLng)) {
            return $this->outJson(['ok' => false, 'error' => 'paramètres invalides'], 400);
        }
        $coords = (new OsrmSnapper())->routeBetween((float) $fromLat, (float) $fromLng, (float) $toLat, (float) $toLng);
        return $this->outJson(['ok' => true, 'coords' => $coords]);
    }

    public function GetCityPolylinesRaw($dateactive)
    {
        $idville = $this->request->getGet('idville');
        if ($idville === null || ! ctype_digit((string) $idville)) {
            return $this->outJson(['ok' => false, 'error' => 'idville invalide'], 400);
        }
        $idvEsc = (int) $idville;
        $dteEsc = $this->esc($dateactive);
        $sql = "SELECT DISTINCT
                  COALESCE(NULLIF(TRIM(remplace_cam), ''), NOCAMION) AS NOCAMION,
                  COALESCE(NULLIF(idremplace_chauf, 0), idchauffeurs)  AS idchauffeurs,
                  TypeDeProduit AS Ramassage,
                  (SELECT CONCAT(UPPER(TRIM(Nom)), ' ', UPPER(LEFT(TRIM(Prenom),1)))
                     FROM reshumaine WHERE id = COALESCE(NULLIF(cedules.idremplace_chauf,0), cedules.idchauffeurs)
                  ) AS Chauffeur
                FROM cedules
               WHERE DateJour = $dteEsc AND idvilles = $idvEsc";
        $rows = $this->db->query($sql)->getResult();
        if (empty($rows)) {
            return $this->outJson(['ok' => true, 'polylines' => []]);
        }
        $snap = new OsrmSnapper();
        $out = [];
        foreach ($rows as $r) {
            $cam = trim((string) $r->NOCAMION);
            $chf = (int) $r->idchauffeurs;
            if ($cam === '' || $chf === 0) {
                continue;
            }
            $segments = $this->getPolylineDynamiqueForAllWithoutZoneFilter($chf, $dateactive, $cam);
            if (! is_array($segments) || empty($segments)) {
                continue;
            }
            foreach ($segments as $seg) {
                [$pts, $metas, $timestamps, $lastChf] = $snap->parseSegmentChauffeur($seg);
                if (count($pts) < 2) {
                    continue;
                }
                $coords = [];
                $meta = [];
                $n = count($pts);
                for ($i = 0; $i < $n; $i++) {
                    $lat = (float) $pts[$i][0];
                    $lng = (float) $pts[$i][1];
                    $coords[] = [$lat, $lng];
                    $mraw = (string) ($metas[$i] ?? '');
                    $hre = null;
                    $adresse = null;
                    $chauffeur = $lastChf ?: (string) ($r->Chauffeur ?? '');
                    if (preg_match('/timespan\s*:\s*([^,]+)/i', $mraw, $mt)) {
                        $hre = trim($mt[1]);
                    }
                    if (preg_match('/adresse\s*:\s*(.+)$/i', $mraw, $ma)) {
                        $adresse = trim($ma[1]);
                    }
                    $meta[] = ['lat' => $lat, 'lng' => $lng, 'hre' => $hre, 'adresse' => $adresse, 'chauffeur' => $chauffeur];
                }
                if (count($coords) >= 2) {
                    $out[] = ['cam' => $cam, 'coords' => $coords, 'meta' => $meta];
                }
            }
        }
        return $this->outJson(['ok' => true, 'polylines' => $out]);
    }

    public function GetPolylineForDate()
    {
        $dateactive = $this->request->getGet('dateactive');
        $idchauffeur = $this->request->getGet('idchauffeur');
        $nocamion = $this->request->getGet('nocamion');
        if (empty($dateactive) || empty($nocamion)) {
            return $this->outJson(['ok' => false, 'error' => 'dateactive et nocamion sont requis'], 400);
        }
        $cam = trim((string) $nocamion);
        $chf = (int) $idchauffeur;
        $chauffeurName = '';
        if ($chf > 0) {
            $row = $this->db->query(
                "SELECT CONCAT(UPPER(TRIM(Nom)), ' ', UPPER(LEFT(TRIM(Prenom),1))) AS Chauffeur
                 FROM reshumaine WHERE id = " . $this->esc($chf)
            )->getRow();
            if ($row) {
                $chauffeurName = (string) $row->Chauffeur;
            }
        }
        $segments = $this->getPolylineDynamiqueForAll($chf, $dateactive, $cam);
        if (! is_array($segments) || empty($segments)) {
            return $this->outJson(['ok' => true, 'polylines' => []]);
        }
        $snapper = new OsrmSnapper();
        $snappedStr = $snapper->snapSegments($segments);
        if (is_string($snappedStr) && $snappedStr !== '') {
            $segments = explode('|', $snappedStr);
        }
        $out = [];
        foreach ($segments as $seg) {
            [$pts, $metas, $timestamps, $lastChf] = $snapper->parseSegmentChauffeur($seg);
            if (count($pts) < 2) {
                continue;
            }
            $coords = [];
            $meta = [];
            $n = count($pts);
            for ($i = 0; $i < $n; $i++) {
                $lat = (float) $pts[$i][0];
                $lng = (float) $pts[$i][1];
                $coords[] = [$lat, $lng];
                $mraw = (string) ($metas[$i] ?? '');
                $hre = null;
                $adresse = null;
                $chauffeur = $lastChf ?: $chauffeurName;
                if (preg_match('/timespan\s*:\s*([^,]+)/i', $mraw, $mt)) {
                    $hre = trim($mt[1]);
                }
                if (preg_match('/adresse\s*:\s*(.+)$/i', $mraw, $ma)) {
                    $adresse = trim($ma[1]);
                }
                $meta[] = ['lat' => $lat, 'lng' => $lng, 'hre' => $hre, 'adresse' => $adresse, 'chauffeur' => $chauffeur];
            }
            if (count($coords) >= 2) {
                $out[] = ['cam' => $cam, 'coords' => $coords, 'meta' => $meta];
            }
        }
        return $this->outJson(['ok' => true, 'polylines' => $out]);
    }

    public function GetRamassagesForCity($dateactive)
    {
        $idville = $this->request->getGet('idville');
        if ($idville === null || ! ctype_digit((string) $idville)) {
            return $this->outJson(['ok' => false, 'error' => 'idville invalide'], 400);
        }
        $idvEsc = (int) $idville;
        $dteEsc = $this->esc($dateactive);
        $sql = "SELECT
              TypeDeProduit AS Ramassage,
              COALESCE(NULLIF(TRIM(remplace_cam), ''), NOCAMION) AS NOCAMION,
              COALESCE(NULLIF(idremplace_chauf, 0), idchauffeurs)  AS idchauffeur_effectif,
              (SELECT CONCAT(UPPER(TRIM(Nom)), ' ', UPPER(LEFT(TRIM(Prenom),1)))
                 FROM reshumaine
                WHERE id = COALESCE(NULLIF(cedules.idremplace_chauf,0), cedules.idchauffeurs)
              ) AS Chauffeur
            FROM cedules
            WHERE DateJour = $dteEsc AND idvilles = $idvEsc";
        $rows = $this->db->query($sql)->getResult();
        $items = [];
        foreach ($rows as $r) {
            $cam = trim((string) $r->NOCAMION);
            if ($cam === '') {
                continue;
            }
            $items[] = [
                'cam' => $cam,
                'ramassage' => (string) $r->Ramassage,
                'idchauffeur' => (int) $r->idchauffeur_effectif,
                'chauffeur' => (string) ($r->Chauffeur ?? ''),
            ];
        }
        return $this->outJson(['ok' => true, 'items' => $items]);
    }

    public function GetInfoFrom()
    {
        $adresse = $this->esc($this->postGet('adresse'));
        $result = $this->db->query(
            "SELECT (SELECT CONCAT(Prenom, ' ', Nom) FROM reshumaine WHERE id = gps_sani.idchauffeur) AS chauffeur,
                    track_time AS timespan
             FROM gps_sani WHERE adresse = $adresse"
        )->getRowArray();
        return $this->outJson($result);
    }

    public function GetCityPolylines($dateactive)
    {
        @set_time_limit(120);
        $idville = $this->request->getGet('idville');
        if ($idville === null || ! ctype_digit((string) $idville)) {
            return $this->outJson(['ok' => false, 'error' => 'idville invalide'], 400);
        }
        $idvEsc = (int) $idville;
        $dteEsc = $this->esc($dateactive);
        $sql = "SELECT DISTINCT
                  COALESCE(NULLIF(TRIM(remplace_cam), ''), NOCAMION) AS NOCAMION,
                  COALESCE(NULLIF(idremplace_chauf, 0), idchauffeurs)  AS idchauffeurs
                FROM cedules
               WHERE DateJour = $dteEsc AND idvilles = $idvEsc";
        $rows = $this->db->query($sql)->getResult();
        if (empty($rows)) {
            return $this->outJson(['ok' => true, 'polylines' => []]);
        }
        $snap = new OsrmSnapper();
        $out = [];
        foreach ($rows as $r) {
            $cam = trim((string) $r->NOCAMION);
            $chf = (int) $r->idchauffeurs;
            if ($cam === '' || $chf === 0) {
                continue;
            }
            $segments = $this->getPolylineDynamiqueForAll($chf, $dateactive, $cam);
            if (! is_array($segments) || empty($segments)) {
                continue;
            }
            foreach ($segments as $seg) {
                [$pts, $metas, $timestamps, $lastChf] = $snap->parseSegmentChauffeur($seg);
                if (count($pts) < 2) {
                    continue;
                }
                $chunks = $snap->splitByGapsPublic($pts, $timestamps, 200, 120, 160, 50);
                if (empty($chunks)) {
                    $chunks = [[$pts, $timestamps]];
                }
                foreach ($chunks as $chunk) {
                    $cPts = $chunk[0];
                    $cIdx = $chunk[2] ?? null;
                    if (count($cPts) < 2) {
                        continue;
                    }
                    $coords = [];
                    $meta = [];
                    for ($i = 0; $i < count($cPts); $i++) {
                        $lat = (float) $cPts[$i][0];
                        $lng = (float) $cPts[$i][1];
                        $coords[] = [$lat, $lng];
                        $origIdx = ($cIdx !== null && isset($cIdx[$i])) ? $cIdx[$i] : $i;
                        $mraw = (string) ($metas[$origIdx] ?? '');
                        $hre = null;
                        $adresse = null;
                        $chauffeur = $lastChf ?: null;
                        if (preg_match('/timespan\s*:\s*([^,]+)/i', $mraw, $mt)) {
                            $hre = trim($mt[1]);
                        }
                        if (preg_match('/adresse\s*:\s*(.+)$/i', $mraw, $ma)) {
                            $adresse = trim($ma[1]);
                        }
                        if (preg_match('/chauffeur\s*:\s*([^,]+)/i', $mraw, $mc)) {
                            $chauffeur = trim($mc[1]);
                        }
                        $meta[] = ['lat' => $lat, 'lng' => $lng, 'hre' => $hre, 'adresse' => $adresse, 'chauffeur' => $chauffeur];
                    }
                    if (count($coords) >= 2) {
                        $out[] = ['cam' => $cam, 'coords' => $coords, 'meta' => $meta];
                    }
                }
            }
        }
        return $this->outJson(['ok' => true, 'polylines' => $out]);
    }

    public function GetLastPositionsCity(string $dateactive)
    {
        $idville = $this->request->getGet('idville');
        if ($idville === null || ! ctype_digit((string) $idville)) {
            return $this->outJson(['ok' => false, 'error' => 'idville invalide'], 400);
        }
        $idv = (int) $idville;
        $dte = $this->esc($dateactive);
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $rows = $this->db->query(
            "SELECT DISTINCT
                 COALESCE(NULLIF(TRIM(remplace_cam), ''), NOCAMION) AS NOCAMION,
                 COALESCE(NULLIF(idremplace_chauf, 0), idchauffeurs) AS idch,
                 (SELECT CONCAT(UPPER(TRIM(Nom)), ' ', UPPER(LEFT(TRIM(Prenom),1)))
                    FROM reshumaine
                   WHERE id = COALESCE(NULLIF(cedules.idremplace_chauf,0), cedules.idchauffeurs)
                 ) AS Chauffeur
               FROM cedules
              WHERE DateJour = $dte AND idvilles = $idv"
        )->getResult();
        if (empty($rows)) {
            return $this->outJson(['ok' => true, 'items' => []]);
        }
        $items = [];
        foreach ($rows as $r) {
            $cam = trim((string) $r->NOCAMION);
            if ($cam === '') {
                continue;
            }
            $escCam = $this->esc($cam);
            $q = $this->db->query(
                "SELECT track_lat, track_lng, track_time, adresse, IsInZone
                 FROM gps_sani
                 WHERE track_time BETWEEN $dteStart AND $dteEnd AND nocamion = $escCam
                   AND IsInZone = 1
                 ORDER BY track_time DESC LIMIT 1"
            );
            if ($q->getNumRows() === 0) {
                continue;
            }
            $p = $q->getRow();
            $lat = (float) str_replace(',', '.', (string) $p->track_lat);
            $lng = (float) str_replace(',', '.', (string) $p->track_lng);
            if (! is_finite($lat) || ! is_finite($lng)) {
                continue;
            }
            $items[] = [
                'cam' => $cam,
                'lat' => $lat,
                'lng' => $lng,
                'iz' => (int) $p->IsInZone,
                'hre' => (string) $p->track_time,
                'adresse' => (string) $p->adresse,
                'chauffeur' => (string) ($r->Chauffeur ?? ''),
            ];
        }
        return $this->outJson(['ok' => true, 'items' => $items]);
    }

    public function ExportGpsCamion(string $dateactive)
    {
        $cam = trim((string) $this->request->getGet('cam'));
        $format = strtolower(trim((string) $this->request->getGet('format'))) ?: 'json';
        if ($cam === '') {
            return $this->outJson(['ok' => false, 'error' => 'camion invalide'], 400);
        }
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $escCam = $this->esc($cam);
        $q = $this->db->query(
            "SELECT g.id, g.track_lat, g.track_lng, g.track_time, g.adresse, g.IsInZone, g.Statutpoly,
                    CONCAT(UPPER(TRIM(r.NOM)), ' ', UPPER(TRIM(r.PRENOM))) AS chauffeur
             FROM gps_sani g
             LEFT JOIN reshumaine r ON r.id = g.idchauffeur
             WHERE g.track_time BETWEEN $dteStart AND $dteEnd AND g.nocamion = $escCam
             ORDER BY g.track_time ASC"
        );
        if ($q->getNumRows() === 0) {
            return $this->outJson(['ok' => true, 'data' => [], 'count' => 0]);
        }
        $data = [];
        foreach ($q->getResult() as $row) {
            $data[] = [
                'id' => (int) $row->id,
                'lat' => (float) str_replace(',', '.', (string) $row->track_lat),
                'lng' => (float) str_replace(',', '.', (string) $row->track_lng),
                'track_time' => (string) $row->track_time,
                'adresse' => (string) $row->adresse,
                'IsInZone' => (int) $row->IsInZone,
                'Statutpoly' => (int) $row->Statutpoly,
                'chauffeur' => (string) ($row->chauffeur ?? ''),
            ];
        }
        if ($format === 'csv') {
            $out = fopen('php://temp', 'w');
            fputcsv($out, ['id', 'lat', 'lng', 'track_time', 'adresse', 'IsInZone', 'Statutpoly', 'chauffeur']);
            foreach ($data as $row) {
                fputcsv($out, $row);
            }
            rewind($out);
            $csv = stream_get_contents($out);
            fclose($out);
            return $this->response
                ->setContentType('text/csv', 'utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="gps_camion_' . $cam . '_' . $dateactive . '.csv"')
                ->setBody($csv);
        }
        return $this->response
            ->setContentType('application/json', 'utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="gps_camion_' . $cam . '_' . $dateactive . '.json"')
            ->setBody(json_encode(['ok' => true, 'data' => $data, 'count' => count($data)], JSON_PRETTY_PRINT));
    }

    public function GetGpsCountCity(string $dateactive)
    {
        $idville = $this->request->getGet('idville');
        if ($idville === null || ! ctype_digit((string) $idville)) {
            return $this->outJson(['ok' => false, 'error' => 'idville invalide'], 400);
        }
        $idv = (int) $idville;
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $rows = $this->db->query(
            "SELECT DISTINCT
                 COALESCE(NULLIF(TRIM(remplace_cam), ''), NOCAMION) AS NOCAMION
             FROM cedules
             WHERE DateJour = " . $this->esc($dateactive) . " AND idvilles = $idv"
        )->getResult();
        if (empty($rows)) {
            return $this->outJson(['ok' => true, 'count' => 0]);
        }
        $totalCount = 0;
        foreach ($rows as $r) {
            $cam = trim((string) $r->NOCAMION);
            if ($cam === '') {
                continue;
            }
            $escCam = $this->esc($cam);
            $q = $this->db->query(
                "SELECT COUNT(*) AS cnt FROM gps_sani
                 WHERE track_time BETWEEN $dteStart AND $dteEnd AND nocamion = $escCam
                   AND IsInZone = 1"
            );
            if ($q->getNumRows() > 0) {
                $totalCount += (int) $q->getRow()->cnt;
            }
        }
        return $this->outJson(['ok' => true, 'count' => $totalCount]);
    }

    public function GetLastPosCamion(string $dateactive)
    {
        $cam = trim((string) $this->request->getGet('cam'));
        if ($cam === '') {
            return $this->outJson(['ok' => false, 'error' => 'camion invalide'], 400);
        }
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $escCam = $this->esc($cam);
        $q = $this->db->query(
            "SELECT track_lat, track_lng, track_time, adresse, IsInZone
             FROM gps_sani
             WHERE track_time BETWEEN $dteStart AND $dteEnd AND nocamion = $escCam
             ORDER BY track_time DESC LIMIT 1"
        );
        if ($q->getNumRows() === 0) {
            return $this->outJson(['ok' => true, 'cam' => $cam, 'lat' => null, 'lng' => null]);
        }
        $p = $q->getRow();
        return $this->outJson([
            'ok' => true,
            'cam' => $cam,
            'lat' => (float) str_replace(',', '.', (string) $p->track_lat),
            'lng' => (float) str_replace(',', '.', (string) $p->track_lng),
            'iz' => (int) $p->IsInZone,
            'hre' => (string) $p->track_time,
            'adresse' => (string) $p->adresse,
        ]);
    }

    //=================================================================
    // Coordonnées / suivi
    //=================================================================

    public function getCoordRtes($idchauffeur, $dateactive)
    {
        $this->ci3->set('camion', '');
        $this->data['camchauf'] = trim((string) $this->postGet('camchauf'));
        $data = $this->html->getCamOnRoad($dateactive, $idchauffeur, $this->data);
        $data['script'] = '';
        if ($idchauffeur == 0) {
            $idchauffeur = $data['idchauffeur'];
        }
        $allchauffeur = $this->postGet('allchauffeur');
        $chauffeur = ($allchauffeur == 0) ? " AND idchauffeur = $idchauffeur " : '';
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $this->db->query(
            "SELECT CONCAT('{ lat: ', gps_sani.track_lat, ', lng: ', gps_sani.track_lng, '}') AS latlng,
                    DATE(gps_sani.track_time) AS dateactive, idchauffeur
             FROM gps_sani
             WHERE gps_sani.track_time BETWEEN $dteStart AND $dteEnd $chauffeur
             ORDER BY gps_sani.track_time"
        )->getRow();
        $data['script'] = $this->html->getScriptRteAllMarker();
        $this->getValidChauffeurJour($dateactive, $idchauffeur);
        // CI3 retourne le tableau (usage interne) — exposé en JSON via
        // le routage par défaut co_tablette_sani/getCoordRtes.
        return $this->outJson(array_merge($data, $this->data));
    }

    public function getCoordOnly($idchauffeur, $dateactive)
    {
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $allchauffeur = $this->postGet('allchauffeur');
        $chauffeur = ($allchauffeur == 0) ? " AND idchauffeur = " . (int) $idchauffeur . " " : '';
        $result = $this->db->query(
            "SELECT CONCAT('{ lat: ', gps_sani.track_lat, ', lng: ', gps_sani.track_lng, '}') AS latlng
             FROM gps_sani
             WHERE gps_sani.track_time BETWEEN $dteStart AND $dteEnd $chauffeur
             ORDER BY gps_sani.track_time DESC LIMIT 0,1"
        )->getRow();
        return $this->out((string) ($result->latlng ?? ''));
    }

    public function getCoordOnlyOnOffice($idchauffeur, $dateactive)
    {
        $latlng = '';
        $dteStart = $this->esc($dateactive . ' 00:00:00');
        $dteEnd = $this->esc($dateactive . ' 23:59:59');
        $result = $this->db->query(
            "SELECT DISTINCT idchauffeur FROM gps_sani
             WHERE gps_sani.track_time BETWEEN $dteStart AND $dteEnd
             ORDER BY gps_sani.track_time"
        );
        foreach ($result->getResult() as $row) {
            $idchauf = $row->idchauffeur;
            $res = $this->db->query(
                "SELECT nocamion, gps_sani.track_lat, gps_sani.track_lng, idchauffeur FROM gps_sani
                 WHERE gps_sani.track_time BETWEEN $dteStart AND $dteEnd AND idchauffeur = $idchauf
                 ORDER BY gps_sani.track_time DESC LIMIT 0,1"
            );
            foreach ($res->getResult() as $row2) {
                $lat = str_replace(',', '.', (string) $row2->track_lat);
                $lng = str_replace(',', '.', (string) $row2->track_lng);
                $latlng .= $row2->nocamion . '_' . $lat . ',' . $lng . ';';
            }
        }
        $latlng = substr($latlng, 0, -1);
        return $this->out(urlencode($latlng));
    }

    //=================================================================
    // Suivis / posts jour / session chauffeur
    //=================================================================

    public function setLastView()
    {
        $email = $this->ci3->get('email');
        if (isset($email) && $email !== '') {
            $result = $this->svc->validateUser((string) $email, (string) $this->postGet('pass'));
            if (! empty($result)) {
                // GET_TABLETTE = 1
                if ($this->ci3->get('LAST_PAGE') == 1) {
                    return $this->out('/tablettes/acces');
                }
            } else {
                return $this->out('/session/bloque');
            }
        }
        return $this->out('');
    }

    public function getSuivisCamions($cam)
    {
        return $this->outJson($this->svc->getMaxKiloCamions($cam, (array) $this->request->getPost()));
    }

    public function getSuivisChauffeurs()
    {
        $idchauffeur = $this->ci3->get('user_id');
        return $this->out($this->svc->getSuivisChauffeur($idchauffeur, (string) $this->ci3->get('camion')));
    }

    public function PostCamionJour()
    {
        return $this->out($this->svc->postCamionJour((array) $this->request->getPost() + (array) $this->request->getGet()));
    }

    public function postRHDay()
    {
        return $this->out($this->svc->postRHDay((array) $this->request->getPost() + (array) $this->request->getGet()));
    }

    public function postRHDayBroker()
    {
        return $this->out($this->svc->postRHDayBroker((array) $this->request->getPost() + (array) $this->request->getGet()));
    }

    public function postVilleDay()
    {
        return $this->out($this->svc->postVilleDay((array) $this->request->getPost() + (array) $this->request->getGet()));
    }

    public function postDepDay()
    {
        return $this->out($this->svc->postDepDay((array) $this->request->getPost() + (array) $this->request->getGet()));
    }

    public function deleteSanivanDay()
    {
        return $this->out($this->svc->deleteSanivanDay((array) $this->request->getPost() + (array) $this->request->getGet()));
    }

    public function postNewCamion($camion)
    {
        $cam = $this->esc($camion);
        $obj = $this->svc->getMaxKiloCamions($camion, (array) $this->request->getPost());
        $oldcam = $this->esc($this->ci3->get('camion'));
        $this->db->query("UPDATE cedules SET remplace_cam = $cam
            WHERE DateJour = CURDATE() AND NOCAMION = $oldcam");
        return $this->outJson($obj);
    }

    public function postNewChauffeur($idchauffeur)
    {
        $chauffeur = (int) $this->ci3->get('user_id');
        $idchauffeur = (int) $idchauffeur;
        $result = $this->db->query(
            "SELECT ID FROM cedules WHERE idchauffeurs = $chauffeur AND DateJour = CURDATE() ORDER BY ID DESC LIMIT 0, 1"
        )->getRow();
        $idcedule = $result->ID ?? 0;
        $this->db->query("INSERT INTO sanivan_rh (idcedule, idrh, TypeRh, datejour)
            VALUES ($idcedule, $idchauffeur, 0, CURDATE())");
        $this->db->query("UPDATE cedules SET idremplace_chauf = $idchauffeur
            WHERE DateJour = CURDATE() AND idchauffeurs = $chauffeur");
        $this->ci3->set('user_id', $idchauffeur);
        return $this->out('OK');
    }

    public function setValForSan($value, $field)
    {
        $camion = $this->esc($this->ci3->get('camion'));
        $value = str_replace(',', '.', (string) $value);
        $idinfoville = $this->postGet('idinfoville');
        switch ($field) {
            case 'KiloDeb':
            case 'KiloFin':
            case 'Fuel':
            case 'HreDeb_cam':
            case 'HreFin_cam':
                if (($field == 'HreDeb_cam') or ($field == 'HreFin_cam')) {
                    $field = substr($field, 0, 6);
                }
                $value = $this->esc($value);
                $this->db->query("UPDATE camionjour SET $field = $value
                    WHERE NOCAMION = $camion AND DateJour = CURDATE()");
                break;
            case 'HreDeb':
            case 'HreFin':
            case 'HreDeb_extra':
            case 'HreFin_extra':
                $value = $this->esc($value);
                $idrh = $this->esc($this->ci3->get('user_id'));
                $this->db->query("UPDATE sanivan_rh SET $field = $value
                    WHERE idrh = $idrh AND DateJour = CURDATE() AND TypeRh = 0");
                break;
            case 'HreDeb_1':
            case 'HreDeb_2':
            case 'HreDeb_3':
            case 'HreDeb_4':
            case 'HreFin_1':
            case 'HreFin_2':
            case 'HreFin_3':
            case 'HreFin_4':
                $value = $this->esc($value);
                $idrh = $this->esc($this->postGet('idaide'));
                $field = substr($field, 0, 6);
                $this->db->query("UPDATE sanivan_rh SET $field = $value
                    WHERE idrh = $idrh AND DateJour = CURDATE() AND TypeRh = 1");
                break;
            case 'HreDeb_' . $idinfoville:
            case 'HreFin_' . $idinfoville:
            case 'nbBac_' . $idinfoville:
                $value = $this->esc($value);
                $arrField = explode('_', $field);
                $field = $arrField[0];
                $this->db->query("UPDATE info_villes SET $field = $value
                    WHERE id = " . $this->esc($idinfoville));
                break;
        }
        return $this->out('');
    }

    public function setAvecMat($ischecked, $id, $idcedule)
    {
        $ischecked = (int) $ischecked;
        $id = (int) $id;
        $idcedule = (int) $idcedule;
        $this->db->query("UPDATE info_depotoire SET IsAvecMat = $ischecked WHERE id = $id");
        if ($ischecked == 1) {
            $this->db->query("INSERT INTO suiviscamion (nocamion, datestart, TypeDechet, idstatut)
                SELECT NOCAMION, DateJour, TypeDeProduit, 2
                FROM cedules WHERE ID = $idcedule");
        }
        return $this->out('');
    }

    public function trackChauffeur()
    {
        $returnval = '';
        $idchauffeur = $this->esc($this->ci3->get('user_id'));
        $camion = $this->esc($this->ci3->get('camion'));
        $lng = $this->esc($this->postGet('lng'));
        $lat = $this->esc($this->postGet('lat'));
        $gpstime = $this->db->query(
            "SELECT TIMESTAMPDIFF(SECOND, track_time, CURRENT_TIMESTAMP()) AS DivSec
             FROM gps_sani_last WHERE nocamion = $camion"
        )->getRow();
        $strLast = "INSERT INTO gps_sani_last
            (nocamion, idchauffeur, track_time, track_lng, track_lat, adresse)
            VALUES ($camion, $idchauffeur, NOW(), $lng, $lat, 'WEB')
            ON DUPLICATE KEY UPDATE
              idchauffeur=VALUES(idchauffeur), track_time=VALUES(track_time),
              track_lng=VALUES(track_lng), track_lat=VALUES(track_lat), adresse=VALUES(adresse)";
        if ($gpstime === null) {
            $this->db->query("INSERT INTO gps_sani
                (idchauffeur, nocamion, track_lng, track_lat, adresse, track_time)
                VALUES ($idchauffeur, $camion, $lng, $lat, 'WEB', NOW())");
            $this->db->query($strLast);
            $returnval = 'OUI';
        } else {
            if ($gpstime->DivSec > 12) {
                $this->db->query("INSERT INTO gps_sani
                    (idchauffeur, nocamion, track_lng, track_lat, adresse, track_time)
                    VALUES ($idchauffeur, $camion, $lng, $lat, 'WEB', NOW())");
                $this->db->query($strLast);
            }
            $returnval = 'OUI';
        }
        return $this->out($returnval);
    }

    //=================================================================
    // Import / exports / upload / tags / filtre
    //=================================================================

    public function import_gps_json()
    {
        $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        if ($this->request->getMethod(true) === 'OPTIONS') {
            return $this->out('', 200);
        }
        if ($this->request->getMethod(true) !== 'POST') {
            return $this->outJson(['ok' => false, 'error' => 'Method not allowed'], 405);
        }
        $raw = null;
        $file = $this->request->getFile('file');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $raw = file_get_contents($file->getTempName());
        } else {
            $raw = trim((string) $this->request->getBody());
            if ($raw === '' && $this->request->getPost('json')) {
                $raw = $this->request->getPost('json');
            }
        }
        if (empty($raw)) {
            return $this->outJson(['ok' => false, 'error' => 'Empty payload'], 400);
        }
        $arr = json_decode($raw, true);
        if (! is_array($arr)) {
            return $this->outJson(['ok' => false, 'error' => 'Invalid JSON'], 400);
        }
        $this->db->transStart();
        $count = 0;
        foreach ($arr as $item) {
            if (! is_array($item)) {
                continue;
            }
            $time = null;
            if (! empty($item['time'])) {
                $ts = strtotime($item['time']);
                if ($ts !== false) {
                    $time = date('Y-m-d H:i:s', $ts);
                }
            }
            $row = [
                'time' => $time,
                'latitude' => isset($item['latitude']) ? (float) $item['latitude'] : null,
                'longitude' => isset($item['longitude']) ? (float) $item['longitude'] : null,
                'heading_degrees' => isset($item['headingDegrees']) ? (int) $item['headingDegrees'] : null,
                'speed_mph' => isset($item['speedMilesPerHour']) ? (float) $item['speedMilesPerHour'] : null,
                'formatted_location' => $item['reverseGeo']['formattedLocation'] ?? null,
                'is_ecu_speed' => isset($item['isEcuSpeed']) ? ($item['isEcuSpeed'] ? 1 : 0) : null,
                'raw_json' => json_encode($item, JSON_UNESCAPED_UNICODE),
            ];
            $this->db->table('gps_points')->insert($row);
            $count++;
        }
        $this->db->transComplete();
        $ok = $this->db->transStatus() !== false;
        return $this->outJson(['ok' => $ok, 'inserted' => $count]);
    }

    public function getGpxFile()
    {
        $data = (array) $this->request->getPost();
        $idville = $this->esc($data['selVilleModal'] ?? '');
        $idsecteur = $this->esc($data['selSecteurModal'] ?? '');
        $titre = $this->esc($data['titre'] ?? '');
        // CI3 écrit dans ./upload/gpx (docroot CI3) — même destination via ci3.webroot
        $uploadPath = rtrim((string) env('ci3.webroot', FCPATH), '/') . '/upload/gpx';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        $file = $this->request->getFile('fileGPX');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $file->move($uploadPath);
            $savedPath = $uploadPath . '/' . $file->getName();
            $xml = @simplexml_load_file($savedPath);
            if ($xml !== false) {
                $locations = '';
                foreach ($xml->trk->trkseg->trkpt as $point) {
                    $lat = (string) $point['lat'];
                    $lon = (string) $point['lon'];
                    $locations .= "$lat,$lon" . '_';
                }
                $locations = $this->esc($locations);
                $url = $this->esc($savedPath);
                $this->db->query("INSERT INTO polilyne_ville
                    (idville, idsecteur, nom, parcours, urlfile)
                    VALUES ($idville, $idsecteur, $titre, $locations, $url)");
            }
        }
        // CI3 : redirect('/') — racine du site (CI3), pas /ci4/
        return $this->response->setStatusCode(302)->setHeader('Location', '/');
    }

    public function PostPolyline($polyline)
    {
        $polyline = $this->esc($polyline);
        // La table `polilyne` n'existe pas en prod : en CI3 l'insert
        // échouait silencieusement. On reproduit ce comportement.
        try {
            $this->db->query("INSERT INTO polilyne (parcours) VALUES ($polyline)");
        } catch (\Throwable $e) {
            log_message('warning', 'PostPolyline: ' . $e->getMessage());
        }
        return $this->out('');
    }

    public function setNewPolyline()
    {
        return $this->out('');
    }

    public function getNavigationSan($iville, $idsecteur)
    {
        return $this->out((string) $this->html->getPolylineBase($iville, $idsecteur));
    }

    public function getRoadTo($adresse, $lat, $lng)
    {
        return $this->out(view('leaflet/itineraire', [
            'adresse' => $adresse,
            'lat' => $lat,
            'lng' => $lng,
        ]));
    }

    public function getOptionVille()
    {
        return $this->outJson($this->html->getOptionVilleJson());
    }

    public function GetOptionChauffeur($idchauffeur, $dtejour)
    {
        return $this->out($this->html->getOptionChauffeur($idchauffeur, $dtejour));
    }

    public function tags_for_city()
    {
        $idVille = (int) $this->request->getGet('idville');
        if ($idVille <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'idville requis']);
        }
        $rows = $this->svc->listOpenTagsForCity($idVille);
        return $this->outJson(['ok' => true, 'data' => $rows]);
    }

    public function tags_for_chauffeur()
    {
        $idVille = (int) $this->request->getGet('idville');
        $idCh = (int) $this->request->getGet('idchauffeur');
        if ($idVille <= 0 || $idCh <= 0) {
            return $this->outJson(['ok' => false, 'error' => 'params requis']);
        }
        $rows = $this->svc->listOpenTagsForChauffeur($idCh, $idVille);
        return $this->outJson(['ok' => true, 'data' => $rows]);
    }

    public function filterLocation($latitude, $longitude)
    {
        return [
            'latitude' => $this->latFilter->filter($latitude),
            'longitude' => $this->lonFilter->filter($longitude),
        ];
    }

    //=================================================================
    // Interne : getValidChauffeurJour (remplit $this->data)
    //=================================================================

    private function getValidChauffeurJour($date, $idchauffeur): void
    {
        $date = $this->esc($date);
        $idced = $this->postGet('idced');
        if (trim((string) $idced) != '') {
            $idced = ' AND cedules.ID = ' . $idced;
        } else {
            $idced = trim((string) $idced);
        }
        $Chauffeur = ($idchauffeur == 0) ? '' : " AND cedules.idchauffeurs = $idchauffeur ";
        $strSQL = "SELECT cedules.ID, cedules.TypeDeProduit, cedules.NOCAMION, DateJour,
                  remplace_cam, idremplace_chauf,
                   CASE TypeDeProduit
                     WHEN 'ORGANIQUE' THEN '#ebccaa'
                     WHEN 'RECYCLAGE' THEN '#9FF3F9'
                     WHEN 'DECHETS' THEN '#E5E5E5'
                     WHEN 'RESIDUS_VERT' THEN '#2DBC96'
                     WHEN 'GROS_REBUTS' THEN '#2B3B7AF'
                     WHEN 'BRANCHES' THEN '#D1B546'
                     WHEN 'FEUILLES' THEN '#2DBC96'
                     ELSE '#F7F1C5'
                   END AS couleur, HELPERS_1, HELPERS_2, HELPERS_3, HELPERS_4,
                        cedules.idchauffeurs, cedules.idvilles,
                        (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles) AS NomVille
                   FROM cedules
                   WHERE cedules.DateJour = $date $Chauffeur $idced AND
                         (SELECT typecamion FROM camions WHERE NoCamion = cedules.NOCAMION) != 4
                   GROUP BY cedules.ID
                   ORDER BY cedules.NOCAMION, (SELECT ville.Nom FROM ville WHERE ville.id = cedules.idvilles)";
        $result = $this->db->query($strSQL);
        $iter = 1;
        $NOCAMION = '';
        foreach ($result->getResultArray() as $row) {
            $this->data['idcedule'] = $row['ID'];
            $this->data['TypeDeProduit'] = $row['TypeDeProduit'];
            $this->data['couleur'] = $row['couleur'];
            $this->data['NomVille'] = $row['NomVille'];
            if ($iter == 1) {
                $this->html->getinfoAides($result, $this->data);
                $iter++;
            }
            $infoChauffeur = $this->html->getaide($row['ID'], $this->ci3->get('user_id'), 0, $this->esc($row['DateJour']));
            $this->data['HreDeb'] = $infoChauffeur[0]['HreDeb'];
            $this->data['HreFin'] = $infoChauffeur[0]['HreFin'];
            $this->data['HreDeb_extra'] = $infoChauffeur[0]['HreDeb_extra'];
            $this->data['HreFin_extra'] = $infoChauffeur[0]['HreFin_extra'];
            $this->data['TypeExtra'] = $infoChauffeur[0]['TypeExtra'];
            $this->data['idsanivanrh'] = $infoChauffeur[0]['idsanivanrh'];
            $this->data['idrh'] = $infoChauffeur[0]['idrh'];
            $NOCAMION = (trim((string) $row['remplace_cam']) != '') ? $row['remplace_cam'] : $row['NOCAMION'];
            $this->ci3->set('camion', $NOCAMION);

            $ESCcam = $this->esc($NOCAMION);
            $rest = $this->db->query(
                "SELECT id, camionjour.KiloDeb, camionjour.KiloFin, camionjour.Fuel, camionjour.HreDeb, camionjour.HreFin
                 FROM camionjour WHERE camionjour.NOCAMION = $ESCcam AND DateJour = $date"
            );
            $camj = $rest->getRow();
            if ($rest->getNumRows() > 0) {
                $this->data['idcamionjour'] = $camj->id;
                $this->data['KiloDeb'] = str_replace('.', ',', (string) $camj->KiloDeb);
                $this->ci3->set('kmdepart', $this->data['KiloDeb']);
                $this->data['KiloFin'] = str_replace('.', ',', (string) $camj->KiloFin);
                $this->data['Fuel'] = str_replace('.', ',', (string) $camj->Fuel);
                $this->data['HreDeb_cam'] = str_replace('.', ',', (string) $camj->HreDeb);
                $this->data['HreFin_cam'] = str_replace('.', ',', (string) $camj->HreFin);
            } else {
                $idcedule = $this->data['idcedule'];
                $KiloDeb = $this->esc($this->ci3->get('kmdepart'));
                $TypeProduit = $this->esc($this->data['TypeDeProduit']);
                $chauf = $row['idchauffeurs'];
                $this->db->query("INSERT INTO camionjour
                    (idchauffeur, NOCAMION, TypeProduit, KiloDeb, DateJour)
                    VALUES ($chauf, $ESCcam, $TypeProduit, $KiloDeb, $date)");
                $this->data['idcamionjour'] = $this->db->insertID();
                $this->data['KiloDeb'] = $this->ci3->get('kmdepart');
                $this->data['KiloFin'] = '';
                $this->data['Fuel'] = '';
                $this->data['HreDeb_cam'] = '';
                $this->data['HreFin_cam'] = '';
            }
        }
        $this->html->getHeadrerRte($date, $idchauffeur, $this->data);
        $this->html->getAllCamions($NOCAMION, $this->data);
        $this->data['chauffeurs'] = $this->html->getAllEmployes(1, $idchauffeur);
    }

    //=================================================================
    // Réduction de points (Douglas-Peucker + Kalman)
    //=================================================================

    private function boucleReduce($arrSimply): array
    {
        $arrReduce = [];
        $data = $this->douglasPeucker($arrSimply, 0.0000000001);
        $arrNew = '';
        foreach ($data as $value) {
            $arrNew .= $value['x'] . ',' . $value['y'] . '_';
        }
        $arrNew = substr($arrNew, 0, -1);
        $arr = [];
        $valSimply = '';
        foreach (explode('_', $arrNew) as $value) {
            $arrRed = explode(',', $value);
            $arr = $this->filterLocation(
                (float) str_replace(',', '.', (string) (float) $arrRed[0]),
                (float) str_replace(',', '.', (string) (float) $arrRed[1])
            );
            $valSimply .= $arr['latitude'] . ',' . $arr['longitude'] . '_';
            $arrReduce[] = [
                'x' => (float) str_replace(',', '.', (string) $arr['latitude']),
                'y' => (float) str_replace(',', '.', (string) $arr['longitude']),
            ];
        }
        $valSimply = substr($valSimply, 0, -1);
        return ['0' => $valSimply, '1' => $arrReduce];
    }

    private function perpendicularDistance(array $point, array $lineStart, array $lineEnd): float
    {
        $dx = $lineEnd['x'] - $lineStart['x'];
        $dy = $lineEnd['y'] - $lineStart['y'];
        if (abs($dx) < 1e-10 && abs($dy) < 1e-10) {
            return sqrt(pow($point['x'] - $lineStart['x'], 2) + pow($point['y'] - $lineStart['y'], 2));
        }
        $numerator = abs($dy * $point['x'] - $dx * $point['y'] + $lineEnd['x'] * $lineStart['y'] - $lineEnd['y'] * $lineStart['x']);
        $denom = sqrt($dx * $dx + $dy * $dy);
        return $numerator / $denom;
    }

    private function douglasPeucker(array $points, float $tolerance): array
    {
        $n = count($points);
        if ($n < 3) {
            return $points;
        }
        $maxDistance = 0.0;
        $index = 1;
        $endIndex = $n - 1;
        for ($i = 1; $i < $endIndex; $i++) {
            $arrX = $points[$i];
            $arrY = $points[1];
            $arrZ = $points[$endIndex];
            $distance = $this->perpendicularDistance($arrX, $arrY, $arrZ);
            if ($distance > $maxDistance) {
                $maxDistance = $distance;
                $index = $i;
            }
        }
        if ($maxDistance > $tolerance) {
            $firstHalf = $this->douglasPeucker(array_slice($points, 1, $index + 1), $tolerance);
            $secondHalf = $this->douglasPeucker(array_slice($points, $index), $tolerance);
            return array_merge(
                array_slice($firstHalf, 1, count($firstHalf) - 1),
                $secondHalf
            );
        }
        return [$points[1], $points[$endIndex]];
    }
}
