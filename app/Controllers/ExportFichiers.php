<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Export ZIP des fichiers uploades (tickets, PDF, photos sanivan) entre
 * deux dates — fonctionnalite nouvelle CI4, sans equivalent Legacy.
 *
 * La date retenue pour chaque fichier est, par ordre de priorite :
 *  1. la date AAAA-MM-JJ presente dans le chemin relatif
 *     (upload/sanivan/<cam>/<AAAA-MM-JJ>/..., pdf_tickets/.../*_<date>.pdf) ;
 *  2. le timestamp Unix du nom (uploads/tickets/<id>_<id>_<ts>.jpg) ;
 *  3. filemtime en dernier recours.
 *
 * Chaque source est cherchee sous public/ ET sous legacy.webroot
 * (defaut appfiles/ — c'est la que les uploads sont ecrits en prod WHC),
 * dedoublonnee par realpath puis par chemin relatif.
 *
 * Les ZIP sont stockes dans writable/zips et conserves 48 h
 * (telechargeables plusieurs fois, purge auto a l'affichage du form).
 */
class ExportFichiers extends BaseController
{
    /** Sources selectionnables : cle = chemin relatif au webroot. */
    private const SOURCES = [
        'uploads/tickets'     => 'Photos de tickets (uploads/tickets)',
        'uploads/pdf_tickets' => 'Tickets PDF (uploads/pdf_tickets)',
        'upload/sanivan'      => 'Photos Sanivan (upload/sanivan)',
        'upload/reponse'      => 'Reponses (upload/reponse)',
    ];

    /** Duree de conservation des ZIP generes (secondes). */
    private const ZIP_TTL = 172800; // 48 h

    public function index()
    {
        $this->nettoyerZips();

        echo view('admin/export_fichiers', [
            'sources' => self::SOURCES,
            'zips'    => $this->listerZips(),
            'message' => $this->session->flashdata('export_msg'),
            'error'   => $this->session->flashdata('export_err'),
        ]);
    }

    public function generer()
    {
        [$debut, $fin, $sel, $error] = $this->valider();
        if ($error !== null) {
            $this->session->set_flashdata('export_err', $error);
            return redirect()->to(gv_url('admin/export-fichiers'));
        }

        $parSource = [];
        $totalNb     = 0;
        $totalTaille = 0;
        foreach ($sel as $src) {
            $fichiers = $this->collecter($src, $debut, $fin);
            $taille   = 0;
            foreach ($fichiers as &$f) {
                $f['tailleFmt'] = $this->tailleHumaine($f['taille']);
                $taille        += $f['taille'];
            }
            unset($f);
            $parSource[$src] = [
                'fichiers'  => $fichiers,
                'nb'        => count($fichiers),
                'taille'    => $taille,
                'tailleFmt' => $this->tailleHumaine($taille),
            ];
            $totalNb     += count($fichiers);
            $totalTaille += $taille;
        }

        if ($this->input->post('action') === 'apercu') {
            $this->nettoyerZips();
            echo view('admin/export_fichiers', [
                'sources' => self::SOURCES,
                'zips'    => $this->listerZips(),
                'apercu'  => [
                    'parSource' => $parSource,
                    'total'     => ['nb' => $totalNb, 'tailleFmt' => $this->tailleHumaine($totalTaille)],
                ],
                'sel'   => $sel,
                'debut' => $debut,
                'fin'   => $fin,
            ]);
            return;
        }

        if ($totalNb === 0) {
            $this->session->set_flashdata('export_err', 'Aucun fichier dans cette plage de dates.');
            return redirect()->to(gv_url('admin/export-fichiers'));
        }

        // Les archives peuvent atteindre plusieurs Go.
        set_time_limit(0);
        ignore_user_abort(true);

        $nom  = sprintf('fichiers_%s_au_%s_%s.zip', $debut, $fin, date('Ymd-His'));
        $path = $this->zipDir() . DIRECTORY_SEPARATOR . $nom;

        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->session->set_flashdata('export_err', "Impossible de creer l'archive ZIP.");
            return redirect()->to(gv_url('admin/export-fichiers'));
        }
        foreach ($parSource as $info) {
            foreach ($info['fichiers'] as $f) {
                $zip->addFile($f['path'], $f['rel']);
            }
        }
        $zip->close();

        // Manifeste : la suppression du ZIP effacera aussi les fichiers
        // sources — il faut donc se souvenir de leur contenu.
        $manifest = ['debut' => $debut, 'fin' => $fin, 'fichiers' => []];
        foreach ($parSource as $info) {
            foreach ($info['fichiers'] as $f) {
                $manifest['fichiers'][] = $f['path'];
            }
        }
        file_put_contents(
            substr($path, 0, -4) . '.json',
            json_encode($manifest, JSON_UNESCAPED_SLASHES)
        );

        $this->session->set_flashdata('export_msg', sprintf(
            'Archive %s creee : %d fichiers, %s. Conservee 48 h.',
            $nom,
            $totalNb,
            $this->tailleHumaine((float) (filesize($path) ?: 0))
        ));
        return redirect()->to(gv_url('admin/export-fichiers'));
    }

    public function telecharger($fichier = '')
    {
        $path = $this->zipPathValide((string) $fichier);
        if ($path === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Marque l'archive comme telechargee dans le manifeste — requis
        // pour debloquer le bouton Supprimer. Ecrit au shutdown (apres
        // l'envoi du corps) et pas si le client a interrompu le transfert.
        $jsonPath = substr($path, 0, -4) . '.json';
        register_shutdown_function(static function () use ($jsonPath): void {
            if (connection_aborted()) {
                return;
            }
            $manif = is_file($jsonPath)
                ? (json_decode((string) file_get_contents($jsonPath), true) ?: [])
                : ['fichiers' => []];
            $manif['telecharge'] = date('c');
            file_put_contents($jsonPath, json_encode($manif, JSON_UNESCAPED_SLASHES));
        });

        return $this->response->download($path, null)->setFileName(basename($path));
    }

    /**
     * Supprime l'archive ZIP ET les fichiers originaux qu'elle contient
     * (d'apres le manifeste .json ecrit a la creation). Chaque chemin est
     * valide par realpath-containment sous une racine de source autorisee ;
     * les dossiers laisses vides sont purges jusqu'a la racine.
     */
    public function supprimer()
    {
        $path = $this->zipPathValide((string) $this->input->post('fichier'));
        if ($path === null) {
            return redirect()->to(gv_url('admin/export-fichiers'));
        }

        $jsonPath = substr($path, 0, -4) . '.json';
        $manifest = is_file($jsonPath)
            ? json_decode((string) file_get_contents($jsonPath), true)
            : null;

        // Ceinture+bretelles du bouton disabled : pas de suppression tant
        // que l'archive n'a pas ete telechargee (marqueur pose au shutdown).
        if (empty($manifest['telecharge'])) {
            $this->session->set_flashdata(
                'export_err',
                "Telechargez d'abord l'archive avant de la supprimer : elle detruit aussi les fichiers originaux."
            );
            return redirect()->to(gv_url('admin/export-fichiers'));
        }

        $racines  = $this->racinesAutorisees();
        $effaces  = 0;
        $absents  = 0;

        foreach (($manifest['fichiers'] ?? []) as $p) {
            if ($this->cheminAutorise($p, $racines) && is_file($p) && @unlink($p)) {
                $effaces++;
                $this->purgeDossiersVides(dirname($p), $racines);
            } else {
                $absents++;
            }
        }

        @unlink($jsonPath);
        @unlink($path);
        $this->session->set_flashdata('export_msg', sprintf(
            'Archive supprimee : %d fichier(s) source efface(s)%s.',
            $effaces,
            $absents > 0 ? sprintf(', %d introuvable(s) ou non autorise(s)', $absents) : ''
        ));
        return redirect()->to(gv_url('admin/export-fichiers'));
    }

    /**
     * Fichiers de $source (chemin relatif au webroot) dont la date est
     * dans [$debut, $fin] inclusif — comparaison lexicographique AAAA-MM-JJ.
     *
     * @return list<array{path:string, rel:string, taille:int}>
     */
    private function collecter(string $source, string $debut, string $fin): array
    {
        $out = [];
        $vus = []; // chemins relatifs deja pris (dedoublonnage des racines)
        foreach ($this->racines($source) as $root) {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY,
                \RecursiveIteratorIterator::CATCH_GET_CHILD // sous-dossier illisible : ignore
            );
            foreach ($it as $f) {
                if (! $f->isFile()) {
                    continue;
                }
                $rel = $source . '/' . str_replace('\\', '/', $it->getSubPathName());
                if (isset($vus[$rel])) {
                    continue;
                }
                $vus[$rel] = true;
                $full = $f->getPathname();
                $date = $this->dateDuFichier($rel, $full);
                if ($date === null || $date < $debut || $date > $fin) {
                    continue;
                }
                $out[] = ['path' => $full, 'rel' => $rel, 'date' => $date, 'taille' => $f->getSize()];
            }
        }
        usort($out, static fn ($a, $b) => strcmp($a['rel'], $b['rel']));
        return $out;
    }

    /**
     * Racines candidates pour une source : public/ puis legacy.webroot
     * (defaut appfiles/), sans doublon realpath (jonctions/symlinks).
     *
     * @return list<string>
     */
    private function racines(string $source): array
    {
        $roots  = [];
        $bases  = [
            FCPATH,
            rtrim((string) env('legacy.webroot', ROOTPATH . 'appfiles'), '/\\'),
        ];
        $suffix = DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $source);
        foreach ($bases as $base) {
            $real = realpath(rtrim($base, '/\\') . $suffix);
            if ($real === false || isset($roots[$real])) {
                continue;
            }
            $roots[$real] = $real;
        }
        return array_values($roots);
    }

    /**
     * Toutes les racines de toutes les sources (realpath) — utilise pour
     * autoriser la suppression des fichiers sources listes dans un
     * manifeste et comme borne de la purge des dossiers vides.
     *
     * @return list<string>
     */
    private function racinesAutorisees(): array
    {
        $roots = [];
        foreach (array_keys(self::SOURCES) as $src) {
            foreach ($this->racines($src) as $r) {
                $roots[$r] = $r;
            }
        }
        return array_values($roots);
    }

    /** Le fichier doit resider sous une racine de source autorisee. */
    private function cheminAutorise(string $p, array $racines): bool
    {
        $real = realpath($p);
        if ($real === false) {
            return false;
        }
        foreach ($racines as $root) {
            if (strncmp($real, $root . DIRECTORY_SEPARATOR, strlen($root) + 1) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remonte en supprimant les dossiers vides laisses par l'effacement
     * des fichiers, sans jamais toucher une racine de source ni monter
     * au-dessus (rmdir echoue sur dossier non vide -> arret naturel).
     */
    private function purgeDossiersVides(string $dir, array $racines): void
    {
        $stop = array_fill_keys($racines, true);
        $dir  = realpath($dir) ?: $dir;
        while (! isset($stop[$dir]) && @rmdir($dir)) {
            $dir = dirname($dir);
        }
    }

    private function dateDuFichier(string $rel, string $full): ?string
    {
        if (preg_match('#(?:19|20)\d{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[12]\d|3[01])#', $rel, $m) === 1
            && checkdate((int) substr($m[0], 5, 2), (int) substr($m[0], 8, 2), (int) substr($m[0], 0, 4))) {
            return $m[0];
        }
        if (preg_match('/_(\d{9,10})\.[a-z0-9]+$/i', basename($rel), $m) === 1) {
            return date('Y-m-d', (int) $m[1]);
        }
        $ts = @filemtime($full);
        return $ts === false ? null : date('Y-m-d', $ts);
    }

    /** @return array{0:?string,1:?string,2:list<string>,3:?string} */
    private function valider(): array
    {
        $debut = (string) $this->input->post('debut');
        $fin   = (string) $this->input->post('fin');
        if (! $this->dateValide($debut) || ! $this->dateValide($fin)) {
            return [null, null, [], 'Dates invalides (format AAAA-MM-JJ requis).'];
        }
        if ($debut > $fin) {
            return [null, null, [], 'La date de debut doit preceder la date de fin.'];
        }
        $sel = array_values(array_intersect(
            array_keys(self::SOURCES),
            (array) $this->input->post('sources')
        ));
        if ($sel === []) {
            return [null, null, [], 'Selectionnez au moins un repertoire source.'];
        }
        return [$debut, $fin, $sel, null];
    }

    private function dateValide(string $d): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) === 1
            && checkdate((int) substr($d, 5, 2), (int) substr($d, 8, 2), (int) substr($d, 0, 4));
    }

    private function zipPathValide(string $nom): ?string
    {
        $nom = basename($nom);
        if (preg_match('/^fichiers_\d{4}-\d{2}-\d{2}_au_\d{4}-\d{2}-\d{2}_\d{8}-\d{6}\.zip$/', $nom) !== 1) {
            return null;
        }
        $path = $this->zipDir() . DIRECTORY_SEPARATOR . $nom;
        return is_file($path) ? $path : null;
    }

    private function zipDir(): string
    {
        $dir = WRITEPATH . 'zips';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private function listerZips(): array
    {
        $out = [];
        foreach (glob($this->zipDir() . DIRECTORY_SEPARATOR . 'fichiers_*.zip') ?: [] as $p) {
            $mtime   = filemtime($p) ?: 0;
            $taille  = filesize($p) ?: 0;
            $json    = substr($p, 0, -4) . '.json';
            $manif   = is_file($json) ? json_decode((string) file_get_contents($json), true) : null;
            $out[] = [
                'nom'        => basename($p),
                'tailleFmt'  => $this->tailleHumaine((float) $taille),
                'cree'       => date('Y-m-d H:i', $mtime),
                'mtime'      => $mtime,
                'nbFichiers' => isset($manif['fichiers']) ? count($manif['fichiers']) : null,
                'telecharge' => ! empty($manif['telecharge']),
            ];
        }
        usort($out, static fn ($a, $b) => $b['mtime'] <=> $a['mtime']);
        return $out;
    }

    private function nettoyerZips(): void
    {
        foreach (glob($this->zipDir() . DIRECTORY_SEPARATOR . 'fichiers_*.zip') ?: [] as $p) {
            if ((filemtime($p) ?: 0) < time() - self::ZIP_TTL) {
                @unlink($p);
            }
        }
    }

    private function tailleHumaine(float $octets): string
    {
        $unites = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $i = 0;
        while ($octets >= 1024 && $i < count($unites) - 1) {
            $octets /= 1024;
            $i++;
        }
        return $i === 0
            ? sprintf('%d o', (int) $octets)
            : sprintf('%.1f %s', $octets, $unites[$i]);
    }
}
