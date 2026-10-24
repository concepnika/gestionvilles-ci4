<?php

/**
 * Polyfills des helpers CI3 utilises par les modeles portes
 * (download::force_download, file::delete_files).
 *
 * Charge par BaseController et Ci3Model. Pattern : les en-tetes sont
 * poses sur la Response CI4 partagee (comme Ci3Output) et le corps
 * passe par echo — CI4 utilise la sortie bufferisee comme corps de
 * reponse quand le controleur ne retourne rien.
 */

if (! function_exists('force_download')) {
    /**
     * Port de CI3 force_download($filename, $data, $set_mime).
     * $data === NULL : lit le fichier $filename (usage des exports
     * Excel). N'interrompt pas l'execution (pas de exit) — le
     * contenu passe par ob vers le corps de la Response CI4.
     */
    function force_download($filename = '', $data = '', $set_mime = false)
    {
        if ($filename === '') {
            return;
        }
        if ($data !== null && $data !== '') {
            $name    = basename(str_replace('\\', '/', $filename));
            $content = $data;
        } elseif (is_file($filename)) {
            $name    = basename(str_replace('\\', '/', $filename));
            $content = file_get_contents($filename);
        } else {
            return;
        }

        $mime = 'application/octet-stream';
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $map  = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls'  => 'application/vnd.ms-excel',
            'pdf'  => 'application/pdf',
            'csv'  => 'text/csv',
        ];
        if (isset($map[$ext])) {
            $mime = $map[$ext];
        }

        service('response')
            ->setContentType($mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $name . '"')
            ->setHeader('Content-Length', (string) strlen($content))
            ->setHeader('Expires', '0')
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');

        echo $content;
    }
}

if (! function_exists('delete_files')) {
    /**
     * Port de CI3 delete_files($path, $del_dir, $htdocs) : vide un
     * repertoire. Les appels portes passent un chemin de FICHIER —
     * CI3 retournait alors false sans rien faire (opendir echoue),
     * comportement reproduit : les fichiers exports restent dans
     * upload/ comme en CI3.
     */
    function delete_files($path, $del_dir = false, $htdocs = 0)
    {
        $path = rtrim($path, '/\\') . '/';
        if (! is_dir($path)) {
            return false;
        }
        foreach (scandir($path) ?: [] as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (! is_dir($path . $file)) {
                @unlink($path . $file);
            }
        }
        if ($del_dir && is_dir($path)) {
            @rmdir($path);
        }

        return true;
    }
}
