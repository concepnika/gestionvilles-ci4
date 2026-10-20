<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Port de application/config/map_tags.php (CI3) — raisons de tags
 * de collecte, fallback quand la table map_tag_reasons est vide.
 */
class MapTags extends BaseConfig
{
    public array $map_tag_reasons = [
        'Bac manquant',
        'Ramassage non effectué',
        'Adresse introuvable',
        'Contenant brisé',
        'Contaminants / tri non conforme',
        'Accès bloqué',
        'Volume excédentaire',
        'Heures d’accès restreintes',
        'Endroit dangereux',
        'Autre',
    ];
}
