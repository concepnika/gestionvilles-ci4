<?php

namespace App\Controllers;

/**
 * Port CI3 -> CI4 de Co_villes_admin : gestion des zones/polygones
 * par ville (carte admin + carte publique) — port mecanique via
 * port_controller.php.
 */
class VillesAdmin extends BaseController
{
    private $villeTable = 'ville';

    public function zones_public()
    {    
            $data = [];
            $data['zonesFeatures'] = $this->loadAllZonesFeatures();
            echo view('villes/leaflet/map_nav_zone', $data);
    }


    public function zones()
    {    
            $data = [];
            $data['ActiveDate'] = date('Y-m-d');
            $data['villes'] = $this->getVillesForDropdown();
            $data['zonesFeatures'] = []; // chargement à la sélection de la ville
            echo view('leaflet/map_nav_admin', $data);
    }


    public function saveZone()
    {    
            $this->output->set_content_type('application/json; charset=utf-8');
    
            $name = trim((string) $this->input->post('name', true));
            $villeId = trim((string) $this->input->post('villeId', true));
            $geojson = (string) $this->input->post('geojson');
    
            if ($name === '')
                $name = 'Zone '.date('Y-m-d H:i:s');
            if ($villeId === '' || ! ctype_digit($villeId)) {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'villeId invalide']));
            }
            if ($geojson === '') {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'geojson manquant']));
            }
    
            $decoded = json_decode($geojson, true);
            if (! $decoded || ! isset($decoded['geometry']['type']) || strtoupper($decoded['geometry']['type']) !== 'POLYGON') {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'geojson POLYGON attendu']));
            }
    
            $zonesTable = $this->getZonesTable();
            if ($zonesTable === null) {
                return $this->output->set_status_header(500)->set_output(json_encode(['ok' => false, 'error' => 'Table des zones introuvable']));
            }
    
            $fkCol = $this->detectVilleFk($zonesTable);
            $data = ['name' => $name, 'geojson' => $geojson];
            if ($fkCol) {
                $data[$fkCol] = (int) $villeId;
            }
    
            if ($this->hasColumnIn($zonesTable, 'created_at'))
                $data['created_at'] = date('Y-m-d H:i:s');
            if ($this->hasColumnIn($zonesTable, 'updated_at'))
                $data['updated_at'] = date('Y-m-d H:i:s');
    
            $ok = $this->db->insert($zonesTable, $data);
            if (! $ok) {
                $err = $this->db->error();
                return $this->output->set_status_header(500)->set_output(json_encode(['ok' => false, 'error' => (isset($err['message']) ? $err['message'] : 'Erreur BD')]));
            }
            return $this->output->set_output(json_encode(['ok' => true, 'id' => $this->db->insertID()]));
    }


    public function zonesByVille()
    {    
            $this->output->set_content_type('application/json; charset=utf-8');
            $villeId = trim((string) $this->input->get('villeId', true));
            if ($villeId === '' || ! ctype_digit($villeId)) {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'villeId invalide']));
            }
            $features = $this->loadAllZonesFeatures((int) $villeId);
            return $this->output->set_output(json_encode(['ok' => true, 'features' => $features]));
    }


    public function updateZone()
    {    
            $this->output->set_content_type('application/json; charset=utf-8');
    
            $id = trim((string) $this->input->post('id', true));
            $name = trim((string) $this->input->post('name', true));
            $geojson = (string) $this->input->post('geojson');
    
            if ($id === '' || ! ctype_digit($id)) {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'id invalide']));
            }
            if ($geojson === '') {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'geojson manquant']));
            }
            $decoded = json_decode($geojson, true);
            if (! $decoded || ! isset($decoded['geometry']['type']) || strtoupper($decoded['geometry']['type']) !== 'POLYGON') {
                return $this->output->set_status_header(400)->set_output(json_encode(['ok' => false, 'error' => 'geojson POLYGON attendu']));
            }
    
            $zonesTable = $this->getZonesTable();
            if ($zonesTable === null) {
                return $this->output->set_status_header(500)->set_output(json_encode(['ok' => false, 'error' => 'Table des zones introuvable']));
            }
    
            $data = ['geojson' => $geojson];
            if ($name !== '')
                $data['name'] = $name;
            if ($this->hasColumnIn($zonesTable, 'updated_at'))
                $data['updated_at'] = date('Y-m-d H:i:s');
    
            $this->db->where('id', (int) $id)->update($zonesTable, $data);
            if ($this->db->affectedRows() >= 0) {
                return $this->output->set_output(json_encode(['ok' => true]));
            }
            return $this->output->set_status_header(500)->set_output(json_encode(['ok' => false, 'error' => 'Erreur BD']));
    }


    public function listVilles()
    {    
            $this->output->set_content_type('application/json; charset=utf-8');
            return $this->output->set_output(json_encode($this->getVillesForDropdown()));
    }



    public function index()
    {    
            return $this->zones();
    }

    public function secteurs()
    {    
            $data = [];
            $data['ActiveDate'] = date('Y-m-d');
            $data['villes'] = $this->getVillesForDropdown();
            $data['zonesFeatures'] = []; // chargement à la sélection de la ville
            echo view('leaflet/map_nav_secteur', $data);
    }

    private function getVillesForDropdown(): array
    {    
            if (! $this->db->tableExists($this->villeTable))
                return [];
            $this->db->select('id, nom')->from($this->villeTable);
            if ($this->hasColumnIn($this->villeTable, 'isdeleted'))
                $this->db->where('isdeleted', 0);
            $this->db->order_by('nom', 'asc');
            return $this->db->get()->getResultArray();
    }

    private function getZonesTable(): ?string
    {    
            if ($this->db->tableExists('villes_zones'))
                return 'villes_zones';
            if ($this->db->tableExists('zones'))
                return 'zones';
            return null;
    }

    private function detectVilleFk(string $zonesTable): ?string
    {    
            foreach (['idville', 'ville_id', 'IdVille'] as $cand) {
                if ($this->hasColumnIn($zonesTable, $cand))
                    return $cand;
            }
            return null;
    }

    private function hasColumnIn(string $table, string $col): bool
    {    
            $fields = $this->db->list_fields($table);
            return in_array($col, $fields, true);
    }

    private function loadAllZonesFeatures(int $villeId = null): array
    {    
            $zonesTable = $this->getZonesTable();
            if ($zonesTable === null)
                return [];
    
            $fkCol = $this->detectVilleFk($zonesTable);
    
            $this->db->select("$zonesTable.id, $zonesTable.name, $zonesTable.geojson".($fkCol ? ", $zonesTable.$fkCol as ville_id" : ""))
                ->from($zonesTable);
    
            if ($villeId !== null && $fkCol !== null) {
                $this->db->where("$zonesTable.$fkCol", (int) $villeId);
            }
    
            if ($fkCol && $this->db->tableExists($this->villeTable) && $this->hasColumnIn($this->villeTable, 'nom')) {
                $this->db->select($this->villeTable.'.nom as ville_nom');
                $this->db->join($this->villeTable, $this->villeTable.".id = $zonesTable.$fkCol", 'left');
            }
    
            $rows = $this->db->get()->getResult();
    
            $features = [];
            foreach ($rows as $r) {
                $gj = json_decode($r->geojson, true);
                if (! $gj || ! isset($gj['type']) || strtoupper($gj['type']) !== 'FEATURE')
                    continue;
    
                if (! isset($gj['properties']) || ! is_array($gj['properties']))
                    $gj['properties'] = [];
                // Injecte métadonnées utiles
                $gj['properties']['id'] = (int) $r->id;
                if (! empty($r->name))
                    $gj['properties']['name'] = $r->name;
                if (isset($r->ville_id))
                    $gj['properties']['villeId'] = $r->ville_id;
                if (isset($r->ville_nom))
                    $gj['properties']['ville'] = $r->ville_nom;
    
                $features[] = $gj;
            }
            return $features;
    }
}
