<?php

namespace App\Libraries;

/**
 * Port CI4 minimal de application/libraries/SamsaraLib.php (CI3) —
 * uniquement ce que Co_Rest_Sani utilise : listVehicles +
 * findVehicleIdByName.
 *
 * Config : samsara.apiKey et samsara.baseUrl dans .env
 * (repris de application/config/samsara.php).
 */
class SamsaraClient
{
    private ?string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('samsara.apiKey') ?: null;
        $base = env('samsara.baseUrl') ?: 'https://api.samsara.com';
        $this->baseUrl = rtrim($base, '/');
    }

    public function listVehicles($limit = 512)
    {
        return $this->request("{$this->baseUrl}/fleet/vehicles?limit={$limit}");
    }

    /**
     * Trouve l'ID Samsara d'un véhicule par son nom de camion.
     * Correspondance exacte puis partielle (insensible à la casse).
     */
    public function findVehicleIdByName($nocamion): ?string
    {
        if (empty($nocamion)) {
            return null;
        }

        $data = $this->listVehicles();
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return null;
        }

        $needle = strtolower(trim($nocamion));
        $partial = null;

        foreach ($data['data'] as $v) {
            $name = isset($v['name']) ? strtolower(trim($v['name'])) : '';
            if ($name === '') {
                continue;
            }
            if ($name === $needle) {
                return $v['id'] ?? null;
            }
            if ($partial === null) {
                if (strpos($name, $needle) !== false || strpos($needle, $name) !== false) {
                    $partial = $v;
                }
            }
        }

        return $partial['id'] ?? null;
    }

    private function request($url, $method = 'GET', $body = null)
    {
        if ($this->apiKey === null) {
            return ['error' => 'samsara.apiKey non configuré'];
        }

        $ch = curl_init($url);
        $headers = [
            "Authorization: Bearer {$this->apiKey}",
            'Accept: application/json',
        ];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        if ($method === 'POST' && $body !== null) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => $headers,
            ]);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', "[Samsara] cURL error: {$error}");
            return ['error' => $error];
        }
        if ($httpCode >= 400) {
            log_message('error', "[Samsara] HTTP {$httpCode}: {$response}");
            return ['error' => "HTTP {$httpCode}", 'body' => $response];
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : ['raw' => $response];
    }
}
