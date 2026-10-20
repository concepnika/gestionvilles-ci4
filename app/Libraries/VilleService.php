<?php

namespace App\Libraries;

use Config\Database;

/**
 * Port de Villes_model / Map_tags_model (CI3) pour le portail citoyen
 * Co_ville. Le reste de Villes_model (insert_data/update_data/
 * getInfoConnect, gestion des comptes citoyens par l'admin) sera
 * porté avec co_gestion.
 *
 * L'authentification citoyenne utilise AES_ENCRYPT/AES_DECRYPT MySQL
 * avec la clé SHA2(keysha, 512) — différente du pepper admin
 * (Login::PEPPER). Reproduite telle quelle pour rester compatible
 * avec les comptes existants.
 */
class VilleService
{
    /** Villes_model::$keysha (CI3). */
    public const KEYSHA = 'niKa64982c4edeb0brdf23vf';

    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /** Expression SQL de la clé AES (dans un escape, comme CI3). */
    public function aesKey(): string
    {
        return 'UNHEX(SHA2(' . $this->db->escape(self::KEYSHA) . ',512))';
    }

    /** Expression SQL d'une valeur chiffrée AES. */
    public function aes(string $value): string
    {
        return 'AES_ENCRYPT(' . $this->db->escape($value) . ', ' . $this->aesKey() . ')';
    }

    /**
     * Co_ville::ValidePass — vérifie le login citoyen (email + password
     * AES). Retourne la ligne ou null.
     */
    public function validePass(string $email, string $password)
    {
        $key      = $this->db->escape(self::KEYSHA);
        $emailSql    = 'AES_ENCRYPT(' . $this->db->escape($email) . ', UNHEX(SHA2(' . $key . ',512)))';
        $passwordSql = 'AES_ENCRYPT(' . $this->db->escape($password) . ', UNHEX(SHA2(' . $key . ',512)))';

        $row = $this->db->query(
            "SELECT id, idville, rang,
                    (SELECT Nom FROM ville WHERE ville.id = villesrecipiendaire.idville) as NomVille,
                    AES_DECRYPT(email, UNHEX(SHA2({$key},512))) as email,
                    nom, prenom, phone, cell, forcechange
             FROM villesrecipiendaire
             WHERE password = {$passwordSql} AND email = {$emailSql}"
        )->getRow();
        return $row ?: null;
    }

    // ──────────────────────────────────────────── Villes_model::zones

    /**
     * Toutes les zones actives pour une ville (ou globales si null).
     */
    public function getZonesByVille($idville = null): array
    {
        $builder = $this->db->table('villes_zones');
        if ($idville === null) {
            $builder->where('idville IS NULL', null, false);
        } else {
            $builder->where('idville', (int) $idville);
        }
        $builder->where('is_active', 1)
            ->orderBy('created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Villes_model::insert_zone — insère une zone (polygone GeoJSON).
     */
    public function insertZone($name, $geojson, $idville = null, $createdBy = null)
    {
        $now  = date('Y-m-d H:i:s');
        $data = [
            'name'       => $name,
            'geojson'    => $geojson,
            'idville'    => $idville,
            'is_active'  => 1,
            'created_by' => ($createdBy !== null ? $createdBy : null),
            'created_at' => $now,
            'updated_at' => $now,
        ];
        if (! $this->db->table('villes_zones')->insert($data)) {
            $err = $this->db->error();
            return ['ok' => false, 'error' => $err['message'] ?? 'insert failed'];
        }
        return ['ok' => true, 'id' => (int) $this->db->insertID()];
    }

    // ───────────────────────────────────────── Map_tags_model::reasons

    /**
     * Raisons des tags (map_tag_reasons), actives seulement si demandé.
     */
    public function getReasons($activeOnly = true): array
    {
        $builder = $this->db->table('map_tag_reasons');
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        return $builder->orderBy('sort_order', 'ASC')
            ->orderBy('label_fr', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Table existe ? (map_tag_responses est optionnelle).
     */
    public function tableExists(string $table): bool
    {
        return $this->db->tableExists($table);
    }
}
