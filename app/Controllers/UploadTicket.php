<?php

namespace App\Controllers;

use App\Models\WebChauffeurModel;

/**
 * Port CI3 -> CI4 de upload_ticket : réception des photos de billets
 * des tablettes MAUI (api/upload-ticket, POST) — photo stockée dans
 * upload/sanivan/ du docroot CI3, PDF généré via FPDF (lib du webroot
 * CI3), mise à jour info_depotoire.Ticket.
 */
class UploadTicket extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        // CI3 __construct : load->model('Web_chauffeur_model', 'webmodel')
        $this->webmodel = new WebChauffeurModel();
        $this->output->set_content_type('application/json', 'utf-8');
    }

    public function index()
    {    
            $resp = ['success' => false, 'message' => ''];
    
            try {
                if ($this->input->method(TRUE) !== 'POST') {
                    throw new \Exception('Méthode HTTP invalide');
                }
                if (empty($_FILES['file'])) {
                    throw new \Exception('Fichier manquant (clé file)');
                }
                if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('Erreur upload fichier code=' . $_FILES['file']['error']);
                }
    
                // nocam (requis)
                $noCamion = trim((string)$this->input->post('nocam', TRUE));
                if ($noCamion === '') {
                    throw new \Exception('Champ nocam obligatoire');
                }
                // Sanitize dossier
                $noCamion = preg_replace('~[^a-zA-Z0-9_\-]~', '_', $noCamion);
    
                // Date dossier = date serveur (comme le script original)
                $dateDir = date('Y-m-d');
    
                // Préparation du répertoire
                $baseRelDir = 'upload/sanivan/';
                $targetRelDir = $baseRelDir . $noCamion . '/' . $dateDir . '/';
                // CI3 : FCPATH . $targetRelDir -> docroot CI3 en CI4
            // (URLs servies par CI3 en prod, Alias /uploads localement).
            $targetFsDir  = rtrim((string) env('ci3.webroot'), '/\\') . '/' . $targetRelDir;
    
                if (!is_dir($targetFsDir) && !mkdir($targetFsDir, 0755, TRUE)) {
                    throw new \Exception('Impossible de créer le dossier de destination');
                }
    
                // Extraction iddepotoire: priorité au champ POST referenceId, sinon parsing du nom de fichier
                $referenceId = trim((string)$this->input->post('referenceId',  TRUE));
                if ($referenceId !== '' && ctype_digit($referenceId)) {
                    $iddepotoire = (int)$referenceId;
                } else {
                    // Fallback: extraction depuis le nom de fichier (format xxx_<id>.ext)
                    $originalName = basename($_FILES['file']['name']);
                    $parts = explode('_', $originalName);
                    if (count($parts) < 2) {
                        throw new \Exception('referenceId manquant et nom de fichier invalide: id introuvable');
                    }
                    $sub = explode('.', $parts[1]);
                    $iddepotoire = $sub[0] ?? null;
                    if ($iddepotoire === null || !ctype_digit($iddepotoire)) {
                        throw new \Exception('Identifiant depotoire invalide (referenceId manquant et nom de fichier non parsable)');
                    }
                    $iddepotoire = (int)$iddepotoire;
                }
                $originalName = basename($_FILES['file']['name']);
    
                // Optionnel: contrôle extension basique (peut être élargi si besoin)
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp','gif'], TRUE)) {
                    // Si vous devez accepter d’autres formats, ajoutez-les ici.
                    throw new \Exception('Extension de fichier non autorisée');
                }
    
                $targetFsPath  = $targetFsDir . $originalName;      // chemin absolu sur disque
                $targetRelPath = $targetRelDir . $originalName;      // chemin relatif photo
    
                if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetFsPath)) {
                    throw new \Exception('Échec du déplacement du fichier');
                }
    
                // Convertir HEIC/WEBP en JPEG si nécessaire (FPDF supporte JPG, PNG, GIF)
                $supportedExts = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($ext, $supportedExts, TRUE) && function_exists('imagecreatefromstring')) {
                    $imgData = @file_get_contents($targetFsPath);
                    $gdImg   = ($imgData !== false) ? @imagecreatefromstring($imgData) : false;
                    if ($gdImg !== false) {
                        $jpgBaseName = pathinfo($originalName, PATHINFO_FILENAME) . '.jpg';
                        $jpgFsPath   = $targetFsDir . $jpgBaseName;
                        if (@imagejpeg($gdImg, $jpgFsPath, 85)) {
                            imagedestroy($gdImg);
                            $targetFsPath  = $jpgFsPath;
                            $targetRelPath = $targetRelDir . $jpgBaseName;
                            $ext = 'jpg';
                        } else {
                            imagedestroy($gdImg);
                        }
                    }
                }
    
                // Champs POST supplémentaires envoyés par la tablette MAUI
                $nomVille    = trim((string)$this->input->post('nomville',     TRUE));
                $matiere     = trim((string)$this->input->post('matiere',      TRUE));
                $tonnage     = trim((string)$this->input->post('tonnage',      TRUE));
                $dateCollecte = trim((string)$this->input->post('date_collecte', TRUE));
                $isTransBo   = trim((string)$this->input->post('isTransBo',    TRUE));
                $boite       = trim((string)$this->input->post('boite',        TRUE));
                $villeId     = trim((string)$this->input->post('villeId',      TRUE));
    
                // Générer le PDF côté serveur à partir de la photo reçue
                $depotInfo = $this->webmodel->getDepotFullInfo($iddepotoire);
                $chauffeurNom = '';
                $idChauffeur = (int) $this->input->post('id_chauffeur', TRUE);
                if ($idChauffeur > 0) {
                    $ch = $this->webmodel->getChauffeurNom($idChauffeur);
                    if ($ch && !empty($ch['NomComplet'])) {
                        $chauffeurNom = $ch['NomComplet'];
                    }
                }
    
                // Calcul de la date à partir de date_collecte
                $dateTs = !empty($dateCollecte) ? strtotime($dateCollecte) : time();
                if ($dateTs === false) { $dateTs = time(); }
                $annee   = date('Y', $dateTs);
                $moisNum = date('m', $dateTs);
                $dateStr = date('Y-m-d', $dateTs); // pour le nom de fichier
    
                // Sanitize nom de ville
                $villeSafe = preg_replace('~[^a-zA-Z0-9\x{00C0}-\x{00D6}\x{00D8}-\x{00F6}\x{00F8}-\x{00FF} _\-]~u', '', $nomVille);
                $villeSafe = str_replace([' ', '-'], '_', trim($villeSafe));
                if ($villeSafe === '') { $villeSafe = $noCamion; }
    
                $matiereCode  = ($matiere !== '') ? mb_strtoupper(mb_substr($matiere, 0, 1, 'UTF-8'), 'UTF-8') : '';
                $tonnagePart  = ($tonnage !== '') ? $tonnage : '0';
    
                // Nom de fichier: NomVille_D_0_2026-04-24.pdf
                $pdfBaseName = $villeSafe
                    . ($matiereCode !== '' ? '_' . $matiereCode : '')
                    . '_' . $tonnagePart
                    . '_' . $dateStr . '.pdf';
                $pdfBaseName = preg_replace('~[\\/:*?"<>|]~', '_', $pdfBaseName);
    
                // Répertoire PDF public: uploads/pdf_tickets/YYYY/MM/
                $pdfRelDir  = 'uploads/pdf_tickets/' . $annee . '/' . $moisNum . '/';
                // CI3 : FCPATH . $pdfRelDir -> docroot CI3 en CI4
            $pdfFsDir   = rtrim((string) env('ci3.webroot'), '/\\') . '/' . $pdfRelDir;
                if (!is_dir($pdfFsDir) && !mkdir($pdfFsDir, 0755, TRUE)) {
                    throw new \Exception('Impossible de créer le dossier PDF');
                }
    
                $pdfFsPath      = $pdfFsDir . $pdfBaseName;
                $pdfPublicUrl   = 'https://www.robert-daoust.ca/' . $pdfRelDir . $pdfBaseName;
                
                $ticketPathToSave = $targetRelPath;
                $pdfError = null;
                ob_start();
                try {
                    $this->_generateTicketPdf($pdfFsPath, $targetFsPath, $depotInfo, $chauffeurNom);
                    ob_end_clean();
                    if (file_exists($pdfFsPath)) {
                        $ticketPathToSave = $pdfPublicUrl;
                    } else {
                        $pdfError = 'PDF non créé sur disque malgré absence d\'exception';
                        log_message('error', '[Upload_ticket] ' . $pdfError);
                    }
                } catch (\Throwable $e) {
                    ob_end_clean();
                    $pdfError = $e->getMessage();
                    log_message('error', '[Upload_ticket] PDF error: ' . $pdfError);
                }
                // Vérifier que l'enregistrement existe avant update
                $rowExists = $this->db->where('id', $iddepotoire)->count_all_results('info_depotoire');
                if ($rowExists === 0) {
                    throw new \Exception("Enregistrement introuvable dans info_depotoire (id={$iddepotoire})");
                }
    
                // Mise à jour DB: info_depotoire.Ticket
                //$this->db->transStart();
                //$this->db->set('Ticket', $ticketPathToSave)
                //         ->where('id', $iddepotoire)
                //         ->update('info_depotoire');
                //$this->db->transComplete();
    
                //if (!$this->db->transStatus()) {
                //    throw new \Exception('Échec transaction DB');
                //}
                $sql = "UPDATE info_depotoire SET Ticket = '$ticketPathToSave' WHERE id = '$iddepotoire'";
                $this->db->query($sql);
                $resp['success'] = true;
                $resp['message'] = 'Ticket enregistré';
                $resp['id'] = $iddepotoire;
                $resp['ticket_url'] = $ticketPathToSave;
                $resp['photo_path'] = $targetRelPath;
                $resp['debug_referenceId'] = $referenceId;
                $resp['debug_iddepotoire'] = $iddepotoire;
                $resp['debug_pdf_exists'] = file_exists($pdfFsPath);
                $resp['debug_pdf_path'] = $pdfFsPath;
                $resp['debug_ticket_saved'] = $ticketPathToSave;
                if ($pdfError !== null) {
                    $resp['pdf_warning'] = $pdfError;
                }
    
            } catch (\Exception $e) {
                $resp['message'] = $e->getMessage();
            }
    
            echo json_encode($resp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }



    private function _generateTicketPdf($pdfPath, $imagePath, $depotInfo, $chauffeurNom)
    {    
            $prevMemory = ini_get('memory_limit');
            ini_set('memory_limit', '256M');
    
            if (!is_array($depotInfo)) {
                $depotInfo = array();
            }
    
            require_once env('ci3.webroot') . '/application/' . 'controllers/pdf/fpdf.php';
    
            $pdf = new \FPDF('P', 'mm', 'Letter');
            $pdf->SetAutoPageBreak(true, 20);
            $pdf->AddPage();
    
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(0, 10, utf8_decode('Services Sanitaires Robert Daoust Inc.'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 6, utf8_decode('Billet de dépotoir'), 0, 1, 'C');
            $pdf->Ln(4);
    
            $pdf->SetDrawColor(0, 0, 139);
            $pdf->SetLineWidth(0.8);
            $pdf->Line(10, $pdf->GetY(), 206, $pdf->GetY());
            $pdf->Ln(6);
    
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(45, 7, 'Date :', 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, date('Y-m-d H:i'), 0, 1);
    
            if (!empty($depotInfo['NoCamion'])) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, 'Camion :', 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(0, 7, $depotInfo['NoCamion'], 0, 1);
            }
    
            if (!empty($chauffeurNom)) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, 'Chauffeur :', 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(0, 7, utf8_decode($chauffeurNom), 0, 1);
            }
    
            if (!empty($depotInfo['NomVille'])) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, 'Ville :', 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(0, 7, utf8_decode($depotInfo['NomVille']), 0, 1);
            }
    
            if (!empty($depotInfo['NomDepotoire'])) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, utf8_decode('Dépotoir :'), 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(0, 7, utf8_decode($depotInfo['NomDepotoire']), 0, 1);
            }
    
            if (!empty($depotInfo['TypeDeProduit'])) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, 'Produit :', 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(0, 7, utf8_decode($depotInfo['TypeDeProduit']), 0, 1);
            }
    
            if (isset($depotInfo['Tonnage']) && $depotInfo['Tonnage'] !== '' && $depotInfo['Tonnage'] !== null) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, 'Tonnage :', 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(0, 7, $depotInfo['Tonnage'] . ' t', 0, 1);
            }
    
            if (!empty($depotInfo['HreDeb']) || !empty($depotInfo['HreFin'])) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(45, 7, 'Horaire :', 0, 0);
                $pdf->SetFont('Arial', '', 11);
                $heures = trim(($depotInfo['HreDeb'] ?? '') . ' - ' . ($depotInfo['HreFin'] ?? ''), ' -');
                $pdf->Cell(0, 7, $heures, 0, 1);
            }
    
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, utf8_decode('Photo du billet :'), 0, 1);
            $pdf->Ln(2);
    
            $pageW   = 196;
            $maxImgH = 140;
            $imgInfo = @getimagesize($imagePath);
    
            if ($imgInfo !== false) {
                $imgW = $imgInfo[0];
                $imgH = $imgInfo[1];
                $ratio = min($pageW / $imgW, $maxImgH / $imgH);
                $dispW = $imgW * $ratio;
                $dispH = $imgH * $ratio;
                $x = 10 + ($pageW - $dispW) / 2;
                $pdf->Image($imagePath, $x, $pdf->GetY(), $dispW, $dispH);
                $pdf->SetY($pdf->GetY() + $dispH + 4);
            }
    
            $pdf->Ln(6);
            $pdf->SetDrawColor(0, 0, 139);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(10, $pdf->GetY(), 206, $pdf->GetY());
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 5, utf8_decode('Services Sanitaires Robert Daoust Inc. — Document généré le ') . date('Y-m-d à H:i'), 0, 1, 'C');
            $pdf->Cell(0, 5, utf8_decode('Ce document est une preuve de dépôt au dépotoir.'), 0, 1, 'C');
    
            // Output('S') retourne le contenu en string — plus fiable que Output('F') selon les versions FPDF
            $pdfContent = $pdf->Output('F');
            if ($pdfContent === false || $pdfContent === '') {
                throw new \Exception('FPDF Output(S) a retourné un contenu vide');
            }
            move_uploaded_file($pdfPath, $pdfContent);
            //$written = file_put_contents($pdfPath, $pdfContent);
            //if ($written === false) {
            //    throw new \Exception('file_put_contents impossible: ' . $pdfPath);
            //}
            ini_set('memory_limit', $prevMemory);
    }
}
