<?php

$page_id = 'truckmap';
$page_title = 'Carte';

// ═══════════════════════════════════════════════════════════════
// CONTENT
// ═══════════════════════════════════════════════════════════════
ob_start();
?>

<!-- Badges cédules + GPS -->
<div style="margin-bottom:8px; display:flex; align-items:center; flex-wrap:wrap; gap:6px;">
    <?php foreach (($cedules ?? []) as $i => $ced): ?>
    <a href="/web/changeCedule/<?php echo ($i ?? ''); ?>"
       class="badge-cedule"
       style="background:<?php echo !empty($ced['BackCouleurDechet']) ? $ced['BackCouleurDechet'] : '#3498db'; ?>;
              color:<?php echo !empty($ced['TextColorDechet']) ? $ced['TextColorDechet'] : '#fff'; ?>;
              <?php if ($i == $activeCedule)
                  echo 'outline:3px solid #f39c12;'; ?>">
        <?php echo htmlspecialchars(($ced['NoCamion'] ?? '') . ' – ' . ($ced['NomVille'] ?? '')); ?>
    </a>
    <?php endforeach; ?>
    <span class="pull-right text-muted" id="gpsStatus" style="margin-left:auto;">
        <i class="fa fa-circle text-danger"></i> Hors ligne
    </span>
</div>

<!-- ═══ Conteneur carte (position relative pour overlays) ═══ -->
<div id="mapContainer" style="position:relative; height:calc(100vh - 130px); border-radius:6px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.12);">

    <div id="map"></div>

    <!-- Toolbar Start / Stop / StopRoute / Retour -->
    <div class="trace-toolbar">
        <button id="btnBack" class="trace-btn" title="Retour page précédente" onclick="window.history.back();">
            <span style="font-size:20px;">←</span>
        </button>
        <button id="manualBtn" class="trace-btn" title="Démarrer un parcours manuel">
            <img src="https://www.robert-daoust.ca/assets/img/start.png" alt="Start" />
        </button>
        <button id="endManualBtn" class="trace-btn" title="Terminer et figer" disabled>
            <img src="https://www.robert-daoust.ca/assets/img/stop.png" alt="Stop" />
        </button>
        <button id="stopRouteBtn" class="trace-btn" title="Arrêter l'itinéraire" disabled>
            <span>Stop<br/>Route</span>
        </button>
    </div>

    <!-- Panneau boutons polylines -->
    <div class="polyline-control-panel" id="polylineControlPanel" style="display:none;">
        <div id="polylineButtons" style="display:flex;flex-direction:column;gap:6px;">
            <button id="btnPoly1" class="polyline-toggle-btn" style="display:none;" data-polyline-id="" onclick="showOnlyPolyline(this.getAttribute('data-polyline-id'))">
                <div id="btnPoly1Color" class="polyline-color-indicator" style="background:#FF0000;"></div>
                <span id="btnPoly1Num" class="polyline-number">1</span>
                <span id="btnPoly1Label" style="flex:1;">Parcours 1</span>
            </button>
            <button id="btnPoly2" class="polyline-toggle-btn" style="display:none;" data-polyline-id="" onclick="showOnlyPolyline(this.getAttribute('data-polyline-id'))">
                <div id="btnPoly2Color" class="polyline-color-indicator" style="background:#FF0000;"></div>
                <span id="btnPoly2Num" class="polyline-number">2</span>
                <span id="btnPoly2Label" style="flex:1;">Parcours 2</span>
            </button>
            <button id="btnPoly3" class="polyline-toggle-btn" style="display:none;" data-polyline-id="" onclick="showOnlyPolyline(this.getAttribute('data-polyline-id'))">
                <div id="btnPoly3Color" class="polyline-color-indicator" style="background:#FF0000;"></div>
                <span id="btnPoly3Num" class="polyline-number">3</span>
                <span id="btnPoly3Label" style="flex:1;">Parcours 3</span>
            </button>
        </div>
    </div>

    <!-- Suivre camion -->
    <label id="followToggle" style="position:absolute;left:120px;top:105px;z-index:5000;background:#fff;padding:6px 10px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.25);font-size:13px;">
        <input type="checkbox" id="chkFollowTruck" checked style="margin-right:6px;" />
        Suivre automatiquement le camion
    </label>

    <!-- Orienter la carte selon le cap -->
    <label id="bearingToggle" style="position:absolute;left:120px;top:145px;z-index:5000;background:#fff;padding:6px 10px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.25);font-size:13px;">
        <input type="checkbox" id="chkAutoBearing" style="margin-right:6px;" />
        Orienter la carte selon le cap
    </label>

    <!-- Légende polyline -->
    <div id="polylineLegend" style="position:absolute;left:45px;bottom:20px;z-index:5000;background:rgba(255,255,255,0.95);padding:10px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.25);font-size:12px;display:none;">
        <div style="font-weight:600;margin-bottom:6px;">Légende du parcours</div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <div style="width:30px;height:3px;background:#FF0000;"></div>
            <span>Direction normale</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:30px;height:3px;background:#FF8C00;border-top:3px dashed #FF8C00;"></div>
            <span>Retour arrière</span>
        </div>
    </div>

    <!-- Panneau tags chauffeur -->
    <div id="chauffeurTagsPanel" class="chauffeur-tags" style="display:none;">
        <div style="font-weight:600;margin-bottom:6px;">Tags chauffeur</div>
        <div id="chauffeurTagsList"></div>
    </div>

    <!-- ═══ Mobile : Bouton dropdown outils + panneau (visible ≤768px) ═══ -->
    <div class="mobile-tools-backdrop" id="mobileToolsBackdrop"></div>
    <button type="button" class="mobile-tools-fab" id="mobileToolsFab" title="Outils carte">
        <i class="fa fa-cogs"></i> Outils <i class="fa fa-caret-down mobile-tools-caret"></i>
    </button>

    <div class="mobile-tools-panel" id="mobileToolsPanel">
        <div class="mobile-tools-header">
            <span style="font-weight:700;font-size:14px;"><i class="fa fa-cogs"></i> Outils carte</span>
            <button type="button" class="mobile-tools-close" id="mobileToolsClose">&times;</button>
        </div>
        <div class="mobile-tools-body">
            <!-- Trace buttons -->
            <div class="mobile-tools-section">
                <div class="mobile-tools-label">Parcours</div>
                <div class="mobile-tools-row">
                    <button id="manualBtnMob" class="mob-trace-btn mob-trace-start" title="Démarrer">
                        <img src="https://www.robert-daoust.ca/assets/img/start.png" alt="Start" /> Start
                    </button>
                    <button id="endManualBtnMob" class="mob-trace-btn mob-trace-stop" title="Terminer" disabled>
                        <img src="https://www.robert-daoust.ca/assets/img/stop.png" alt="Stop" /> Stop
                    </button>
                    <button id="stopRouteBtnMob" class="mob-trace-btn mob-trace-stopr" title="Arrêter itinéraire" disabled>
                        Stop Route
                    </button>
                </div>
            </div>
            <!-- Follow toggle -->
            <div class="mobile-tools-section">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin:0;cursor:pointer;">
                    <input type="checkbox" id="chkFollowTruckMob" checked style="width:18px;height:18px;" />
                    Suivre le camion
                </label>
            </div>
            <!-- Polyline buttons -->
            <div class="mobile-tools-section" id="mobilePolySection" style="display:none;">
                <div class="mobile-tools-label">Parcours polylines</div>
                <div id="mobilePolyButtons" class="mobile-tools-row" style="flex-direction:column;"></div>
            </div>
        </div>
    </div>

</div><!-- /mapContainer -->

<!-- Lightbox image (fixed, hors container) -->
<div id="imgLightbox">
    <span id="lbClose" aria-label="Fermer">&times;</span>
    <img id="lbImg" alt="Preview agrandie" />
</div>

<?php
$content = ob_get_clean();

// ═══════════════════════════════════════════════════════════════
// HEAD EXTRA (CSS)
// ═══════════════════════════════════════════════════════════════
ob_start();
?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    /* ═══ Styles carte (identiques à truckmap.html MAUI) ═══ */

    #map {
        position: absolute;
        inset: 0;
        height: 100%;
        width: 100%;
        background: #f0f0f0;
        z-index: 1;
    }

    .trace-toolbar {
        position: absolute;
        top: 20px;
        left: 120px;
        z-index: 5000;
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .trace-btn {
        width: 64px;
        height: 64px;
        border: none;
        padding: 0;
        margin: 0;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 4px 14px rgba(0,0,0,.35);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .trace-btn img { width: 48px; height: 48px; }
    .trace-btn span { font-size: 11px; font-weight: 600; color: #333; }
    .trace-btn:hover { transform: scale(1.06); box-shadow: 0 6px 18px rgba(0,0,0,.45); }
    .trace-btn:active { transform: scale(.94); }
    .trace-btn[disabled] { opacity: .45; cursor: not-allowed; filter: grayscale(.6); }

    .chauffeur-tags {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3000;
        width: 300px;
        max-height: 60vh;
        overflow: auto;
        background: rgba(255,255,255,.95);
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.25);
        font-size: 13px;
    }

    .tag-item {
        border: 1px solid #ddd;
        border-left-width: 6px;
        border-radius: 6px;
        padding: 8px;
        margin-bottom: 8px;
        background: #fff;
    }

    .tag-actions { display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap; }
    .tag-actions button {
        flex: 1;
        font-size: 11px;
        padding: 4px 6px;
        background: #1976d2;
        color: #fff;
        border: none;
        border-radius: 4px;
    }
    .tag-actions button:hover { filter: brightness(1.1); }

    .img-preview {
        margin-top: 4px; border: 1px solid #ccc; padding: 2px;
        background: #fafafa; border-radius: 4px; text-align: center;
        max-height: 80px; overflow: hidden; position: relative;
    }
    .img-preview img {
        max-width: 100%; height: 70px; object-fit: cover;
        border-radius: 4px; cursor: pointer;
    }
    .img-preview::after {
        content: "Agrandir"; position: absolute; bottom: 2px; right: 4px;
        background: rgba(0,0,0,.55); color: #fff; font-size: 9px;
        padding: 1px 4px; border-radius: 8px;
    }

    /* Popup tags */
    .popup-wrap {
        max-height: 300px; overflow-y: auto; overflow-x: hidden;
        min-width: 420px; max-width: 600px; padding: 4px;
    }
    .leaflet-popup-content { min-width: 420px; max-width: 620px !important; margin: 6px 10px; }
    .leaflet-popup-content-wrapper { max-height: 340px; overflow: hidden; border-radius: 8px; }
    .popup-wrap div[style*="display:flex"][style*="gap"] { flex-wrap: wrap; }
    .popup-wrap .btn { padding: 3px 6px; font-size: 10px; }
    .popup-wrap .btn-sm { padding: 2px 5px; font-size: 10px; }
    .popup-wrap > div { margin-bottom: 3px; }
    .popup-wrap b { font-size: 11px; }

    /* CSS pour les tooltips d'adresses - éviter la troncation */
    .address-tooltip {
        white-space: nowrap;
        overflow: visible;
        max-width: none;
        font-size: 12px;
        font-weight: 500;
    }
    .popup-wrap input, .popup-wrap select, .popup-wrap textarea { font-size: 11px; padding: 3px 5px; }
    .popup-wrap textarea { max-height: 40px; resize: none; }
    .popup-wrap div[style*="border-left:3px"] { padding-left: 5px; margin-bottom: 3px; font-size: 10px; }
    .popup-wrap .edit-zone, .popup-wrap .reply-zone { max-height: 120px; overflow-y: auto; }

    .leaflet-top .leaflet-control, .leaflet-bottom .leaflet-control {
        z-index: 6000; pointer-events: auto;
    }

    #imgLightbox {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.85);
        display: none; z-index: 7000;
        align-items: center; justify-content: center;
    }
    #imgLightbox img {
        max-width: 92vw; max-height: 92vh;
        border: 4px solid #fff; border-radius: 8px;
        box-shadow: 0 6px 30px rgba(0,0,0,.6);
    }
    #imgLightbox span {
        position: absolute; top: 12px; right: 18px;
        color: #fff; font-size: 28px; cursor: pointer; font-weight: 600;
    }
    .reply-zone textarea { height: 40px; resize: none; font-size: 11px; }

    /* ═══ Panneau polylines ═══ */
    .polyline-control-panel {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 6000;
        background: rgba(255,255,255,0.98);
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
        min-width: 200px;
        max-width: 280px;
        max-height: 50vh;
        overflow-y: auto;
        overflow-x: hidden;
    }
    #polylineButtons {
        display: flex; flex-direction: column; gap: 8px;
        max-height: 40vh; overflow-y: auto; padding-right: 4px;
    }
    #polylineButtons::-webkit-scrollbar { width: 6px; }
    #polylineButtons::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 3px; }
    #polylineButtons::-webkit-scrollbar-thumb { background: #aaa; border-radius: 3px; }
    #polylineButtons::-webkit-scrollbar-thumb:hover { background: #888; }

    .polyline-toggle-btn {
        padding: 10px 14px;
        border: 2px solid #ddd;
        border-radius: 6px;
        background: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        flex-shrink: 0;
        min-height: 48px;
    }
    .polyline-toggle-btn:hover {
        background: #f0f5fb;
        border-color: #4a90e2;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(74,144,226,0.3);
    }
    .polyline-toggle-btn:active { transform: translateY(0); }
    .polyline-toggle-btn.active {
        background: #e3f2fd;
        border-color: #2196f3;
        font-weight: 600;
        box-shadow: 0 0 0 3px rgba(33,150,243,0.2);
    }
    .polyline-color-indicator {
        width: 22px; height: 22px; border-radius: 4px;
        border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }
    .polyline-number { font-weight: 700; min-width: 26px; text-align: center; font-size: 15px; flex-shrink: 0; }
    .polyline-toggle-btn.loading::after { content: '⏳'; margin-left: auto; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .polyline-start-marker {
        width: 32px !important; height: 32px !important;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; background: #fff; border: 3px solid;
        font-weight: 700; font-size: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4); z-index: 1000;
    }

    /* Boutons BS3 compat pour popups tags */
    .btn-info { background-color: #5bc0de; border-color: #46b8da; color: #fff; }
    .btn-success { background-color: #5cb85c; border-color: #4cae4c; color: #fff; }
    .btn-warning { background-color: #f0ad4e; border-color: #eea236; color: #fff; }
    .btn-danger { background-color: #d9534f; border-color: #d43f3a; color: #fff; }
    .btn-outline-primary { background: transparent; border: 1px solid #337ab7; color: #337ab7; }
    .btn-outline-primary:hover { background: #337ab7; color: #fff; }
    .form-select { display: block; width: 100%; padding: 6px 12px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; }
    .form-select-sm { padding: 3px 8px; font-size: 12px; }
    .form-control-sm { padding: 3px 8px; font-size: 12px; }

    /* ═══════════════════════════════════════════════════════════
       MOBILE TOOLS : Dropdown outils (masqués desktop)
       ═══════════════════════════════════════════════════════════ */
    .mobile-tools-fab,
    .mobile-tools-panel,
    .mobile-tools-backdrop {
        display: none;
    }

    @media (max-width: 768px) {
        /* Masquer les contrôles desktop sur la carte */
        .trace-toolbar       { display: none !important; }
        #followToggle        { display: none !important; }
        .polyline-control-panel { display: none !important; }
        #polylineLegend      { display: none !important; }
        .chauffeur-tags      { top: 4px !important; right: 4px !important; width: 240px !important; font-size: 11px !important; max-height: 40vh !important; }

        /* Agrandir la carte sur mobile */
        #mapContainer { height: calc(100vh - 100px) !important; }

        /* Backdrop semi-transparent pour fermer en tapant dehors */
        .mobile-tools-backdrop {
            display: none;
            position: absolute;
            inset: 0;
            z-index: 6400;
            background: rgba(0,0,0,.25);
        }
        .mobile-tools-backdrop.open {
            display: block;
        }

        /* Bouton dropdown "Outils" visible en haut-centre de la carte */
        .mobile-tools-fab {
            display: flex;
            align-items: center;
            gap: 6px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 6500;
            padding: 9px 14px;
            border-radius: 8px;
            border: 2px solid DarkBlue;
            background: rgba(255,255,255,.95);
            color: DarkBlue;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 2px 10px rgba(0,0,0,.35);
            cursor: pointer;
            transition: background .15s ease, color .15s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .mobile-tools-fab:active { background: DarkBlue; color: #fff; transform: translateX(-50%) scale(.97); }
        .mobile-tools-fab.open {
            background: DarkBlue;
            color: #fff;
            border-color: DarkBlue;
        }
        .mobile-tools-caret {
            transition: transform .25s ease;
        }
        .mobile-tools-fab.open .mobile-tools-caret {
            transform: rotate(180deg);
        }

        /* Panneau dropdown outils mobile – centré sous le bouton */
        .mobile-tools-panel {
            display: none;
            position: absolute;
            top: 52px;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            z-index: 6500;
            width: 270px;
            max-width: calc(100vw - 24px);
            max-height: calc(100% - 70px);
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0,0,0,.35);
            overflow: hidden;
            opacity: 0;
            transition: transform .2s cubic-bezier(.4,0,.2,1), opacity .15s ease;
            pointer-events: none;
        }
        .mobile-tools-panel.open {
            display: block;
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-tools-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: DarkBlue;
            color: #fff;
        }
        .mobile-tools-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 0 4px;
            line-height: 1;
        }
        .mobile-tools-body {
            padding: 10px 14px 14px;
            overflow-y: auto;
            max-height: calc(55vh - 48px);
        }
        .mobile-tools-section {
            margin-bottom: 12px;
        }
        .mobile-tools-section:last-child {
            margin-bottom: 0;
        }
        .mobile-tools-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 6px;
            letter-spacing: .5px;
        }
        .mobile-tools-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* Trace buttons mobile (compacts) */
        .mob-trace-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 8px 10px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .15s;
            color: #fff;
            -webkit-tap-highlight-color: transparent;
        }
        .mob-trace-btn img { width: 22px; height: 22px; }
        .mob-trace-btn[disabled] { opacity: .4; cursor: not-allowed; }
        .mob-trace-start { background: #27ae60; }
        .mob-trace-stop  { background: #c0392b; }
        .mob-trace-stopr { background: #7f8c8d; font-size: 11px; }

        /* Polyline buttons mobile */
        .mob-poly-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all .15s;
        }
        .mob-poly-btn:active { background: #e3f2fd; }
        .mob-poly-btn .mob-poly-color {
            width: 16px; height: 16px; border-radius: 3px;
            border: 1px solid rgba(0,0,0,.15); flex-shrink: 0;
        }
    }
</style>
<?php
$head_extra = ob_get_clean();

// ═══════════════════════════════════════════════════════════════
// FOOT EXTRA (JS) – Reproduction fidèle de truckmap.html
// ═══════════════════════════════════════════════════════════════
ob_start();
?>

<!-- Leaflet + plugins -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<!-- Plugin de rotation (gestion automatique du buffer de tuiles pour éviter les coins vides) -->
<script src="https://unpkg.com/leaflet-rotate@0.2.8/dist/leaflet-rotate.js"></script>

<script>
// ═══════════════════════════════════════════════════════════════
// Configuration injectée depuis PHP (reproduit LocationMapPage)
// ═══════════════════════════════════════════════════════════════
var WEB_CONFIG = {
    vehicleId:       "<?php echo addslashes($idSamsara ?? ''); ?>",
    nocamion:        "<?php echo addslashes($nocamion ?? ''); ?>",
    idVille:         <?php echo (int) ($idVille ?? 0); ?>,
    idSecteur:       "<?php echo addslashes($idSecteur ?? ''); ?>",
    idChauffeur:     <?php echo (int) ($idChauffeur ?? 0); ?>,
    isAdmin:         <?php echo !empty($isAdmin) ? 'true' : 'false'; ?>,
    polylineButtons: <?php echo json_encode($polylineButtons ?? array()); ?>,
    pollMs:          9000
};

// ═══════════════════════════════════════════════════════════════
// sendToHost – Version WEB (remplace le pont js2csharp:// MAUI)
// Reproduit la logique de LocationMapPage.Navigating + HandleJsMessage
// ═══════════════════════════════════════════════════════════════
function sendToHost(payload) {
    try { console.log("[TruckMap sendToHost]", payload); } catch(e) {}
    var type = (payload && payload.type) ? String(payload.type) : '';

    switch (type) {
        case 'zone':
            // Reproduit HandleZoneToggle + HandleZoneTimeFromMapAsync
            window._webIsInZone = !!payload.inZone;
            try { window.TruckMap.setDrawPolyline(!!payload.inZone); } catch(e){}
            $.post('/web/api_zone_toggle', {
                inZone: payload.inZone ? '1' : '0'
            });
            break;

        case 'ui':
            if (payload.action === 'showVilles') {
                // Detecter le mode embed (MAUI) via la query string ou l'absence de menu
                var isEmbed = false;
                try {
                    var sp = new URLSearchParams(window.location.search);
                    isEmbed = (sp.get('embed') === '1') || !document.querySelector('.main-sidebar, .navbar, .main-header');
                } catch(e) {}
                window.location.href = isEmbed ? '/web/villes?embed=1' : '/web/villes';
            }
            break;

        case 'requestPolyline':
            // Reproduit SendPolylineDataToJsAsync (lazy loading)
            if (payload.id) {
                _fetchAndDisplayPolyline(payload.id);
            }
            break;

        case 'requestAllPolylines':
            _fetchAllPolylines();
            break;

        case 'mapReady':
            // Reproduit LocationMapPage: InjectContextsAsync + LoadAndInjectCityPolylineAsync
            console.log('[TruckMap] Map ready (web)');
            try { window.TruckMap.setCityContext(String(WEB_CONFIG.idVille), WEB_CONFIG.idSecteur); } catch(e){}
            try { window.TruckMap.setChauffeurContext(String(WEB_CONFIG.idChauffeur)); } catch(e){}
            try { window.TruckMap.setAdmin(WEB_CONFIG.isAdmin); } catch(e){}
            if (WEB_CONFIG.polylineButtons && WEB_CONFIG.polylineButtons.length > 0) {
                try { window.TruckMap.setPolylineButtons(WEB_CONFIG.polylineButtons); } catch(e){}
                // ✅ Auto-charger toutes les polylines (reproduit MAUI LoadAndInjectCityPolylineAsync)
                setTimeout(function() { _fetchAllPolylines(); }, 300);
            }
            try { window.TruckMap.setFollowTruck(true, 17); } catch(e){}
            // Traçage démarré manuellement par le bouton Start (manualBtn).
            // Ne pas activer setDrawPolyline ici : on attend l'action du chauffeur.
            try { window.TruckMap.setDrawPolyline(false); } catch(e){}
            // Restaurer le tracé IsInZone=1 déjà fait aujourd'hui (après un refresh
            // ou une re-connexion). Si le traçage était actif avant le refresh,
            // on réactive automatiquement le dessin pour continuer.
            _restoreInZoneTrack();
            _startSamsaraPolling();
            // ✅ Charger les polylines dynamiques (GPS historique) immédiatement
            var dateactive = new Date().toISOString().split('T')[0];
            $.getJSON('/web/api_get_polyline_dynamique?dateactive=' + encodeURIComponent(dateactive), function(d) {
                if (d && d.ok && d.segments && d.segments.length > 0) {
                    console.log('[TruckMap] Loading dynamic polylines:', d.segments.length, 'segments');
                    if (window.loadDynamicPolylines) {
                        window.loadDynamicPolylines(d.segments);
                    }
                } else {
                    console.log('[TruckMap] No dynamic polylines to load');
                }
            }).fail(function(xhr) {
                console.warn('[TruckMap] Error loading dynamic polylines:', xhr.status);
            });
            // ✅ Centrer sur un tag si arrivé via le popup admin (tagLat/tagLng dans URL)
            (function(){
                try {
                    var sp = new URLSearchParams(window.location.search);
                    var tLat = parseFloat(sp.get('tagLat'));
                    var tLng = parseFloat(sp.get('tagLng'));
                    if (!isNaN(tLat) && !isNaN(tLng)) {
                        setTimeout(function(){
                            try { window.TruckMap.setFollowTruck(false); } catch(e){}
                            try { window.TruckMap.ensureFirstCenter(tLat, tLng); } catch(e){}
                        }, 600);
                    }
                } catch(e){}
            })();
            break;

        case 'tagResponded':
            console.log('[TruckMap] Tag responded:', payload.id, payload.status);
            break;

        case 'jsError':
            console.error('[TruckMap JS Error]', payload.message || payload);
            break;

        case 'debug':
            console.log('[TruckMap Debug]', payload.message || payload);
            break;

        default:
            console.log('[TruckMap] Message:', type, payload);
    }
}

// ═══════════════════════════════════════════════════════════════
// Helpers WEB – Samsara polling + polyline lazy loading
// (reproduit EnsureSamsaraStartedAsync + PollSamsaraAsync + SendPolylineDataToJsAsync)
// ═══════════════════════════════════════════════════════════════
var _samsaraInterval = null;
var _samsaraReady = false;
var _webIsInZone = false;
window._webIsInZone = false;

function _startSamsaraPolling() {
    if (_samsaraInterval) return;
    // Reproduit MAUI: même sans vehicleId, si nocamion est disponible,
    // le serveur PHP résoudra le vehicleId via listVehicles (comme MainPage.GetDataAsyncLogin)
    if (!WEB_CONFIG.vehicleId && !WEB_CONFIG.nocamion) {
        console.warn('[TruckMap] ⚠️ Ni vehicleId ni nocamion disponibles – polling GPS désactivé.');
        $('#gpsStatus').html('<i class="fa fa-circle text-danger"></i> Config Samsara manquante');
        return;
    }
    if (!WEB_CONFIG.vehicleId) {
        console.log('[TruckMap] vehicleId vide, le serveur résoudra via nocamion="' + WEB_CONFIG.nocamion + '"');
        $('#gpsStatus').html('<i class="fa fa-circle text-info"></i> Recherche Samsara...');
    } else {
        console.log('[TruckMap] Démarrage polling Samsara – vehicleId=' + WEB_CONFIG.vehicleId + ' nocamion=' + WEB_CONFIG.nocamion);
    }
    _samsaraReady = true;
    // Ne pas démarrer le polling si la page n'est pas visible
    if (document.hidden) {
        console.log('[TruckMap] Page masquée – polling Samsara différé jusqu\'à retour au premier plan.');
        $('#gpsStatus').html('<i class="fa fa-circle text-muted"></i> En pause (onglet masqué)');
        return;
    }
    _pollSamsara();
    _samsaraInterval = setInterval(_pollSamsara, WEB_CONFIG.pollMs);
}

function _stopSamsaraPolling() {
    if (_samsaraInterval) {
        clearInterval(_samsaraInterval);
        _samsaraInterval = null;
        console.log('[TruckMap] Polling Samsara mis en pause.');
    }
}

// ═══ Page Visibility API : le polling GPS continue même en arrière-plan ═══
// Le polling Samsara continue même si l'onglet perd le focus pour suivre le camion
// en permanence. L'historique est récupéré pour combler les lacunes au retour.
(function() {
    document.addEventListener('visibilitychange', function() {
        if (!_samsaraReady) return;
        if (document.hidden) {
            $('#gpsStatus').html('<i class="fa fa-circle text-muted"></i> En arrière-plan (polling continu)');
        } else {
            $('#gpsStatus').html('<i class="fa fa-circle text-success"></i> GPS OK');
        }
    });
})();

function _pollSamsara() {
    // Ne pas interroger Samsara si Leaflet ou la carte n'est pas disponible
    if (typeof L === 'undefined' || !window.TruckMap || !window.TruckMap.updateTruck) {
        console.warn('[TruckMap] Leaflet ou TruckMap non disponible – poll ignoré.');
        return;
    }
    // Construire les paramètres – vehicleId OU nocamion (le serveur résout si besoin)
    var params = '';
    if (WEB_CONFIG.vehicleId) {
        params = 'vehicleId=' + encodeURIComponent(WEB_CONFIG.vehicleId);
    }
    // Toujours envoyer nocamion (fallback côté serveur, comme MAUI MainPage.GetDataAsyncLogin)
    if (WEB_CONFIG.nocamion) {
        params += (params ? '&' : '') + 'nocamion=' + encodeURIComponent(WEB_CONFIG.nocamion);
    }
    if (!params) {
        $('#gpsStatus').html('<i class="fa fa-circle text-danger"></i> Config manquante');
        return;
    }
    if (WEB_CONFIG.idChauffeur) params += '&idchauffeur=' + encodeURIComponent(WEB_CONFIG.idChauffeur);
    params += '&IsInZone=' + (window._webIsInZone ? '1' : '0');
    $.getJSON('/web/api_samsara_location?' + params, function(d) {
        if (d && d.ok) {
            // Si le serveur a résolu le vehicleId, le cacher pour les prochains appels
            if (d.vehicleId && !WEB_CONFIG.vehicleId) {
                WEB_CONFIG.vehicleId = d.vehicleId;
                console.log('[TruckMap] vehicleId résolu par le serveur: ' + d.vehicleId);
            }
            try { window.TruckMap.updateTruck(d.lat, d.lng, d.heading); } catch(e) {}
            $('#gpsStatus').html('<i class="fa fa-circle text-success"></i> GPS OK' + (d.ts ? ' – ' + d.ts : ''));
            // Charger l'historique GPS Samsara pour combler les lacunes si l'utilisateur a quitté la page
            if (!_gpsHistoryLoaded) {
                _fetchGpsHistory();
            }
        } else {
            var errMsg = (d && d.error) ? d.error : 'Position non disponible';
            console.warn('[TruckMap] Samsara error: ' + errMsg);
            $('#gpsStatus').html('<i class="fa fa-circle text-warning"></i> ' + errMsg);
        }
    }).fail(function() {
        $('#gpsStatus').html('<i class="fa fa-circle text-danger"></i> Erreur GPS');
    });
}

// ═══ Historique GPS Samsara – Charge le parcours réel du jour ═══
var _gpsHistoryLoaded = false;

// ═══ Restauration du tracé GPS après refresh / re-login ═══
// Recharge depuis gps_sani TOUS les points GPS du jour
// et les redessine sous forme de segments bleus permanents.
// Le seuil RESTORE_GAP_METERS (200m) évite les lignes droites indésirables
// entre les points espacés (ex: dépotoir).
function _restoreInZoneTrack() {
    $.getJSON('/web/api_inzone_history', function(d) {
        if (!d || !d.ok) {
            console.warn('[TruckMap] api_inzone_history réponse invalide', d);
            return;
        }
        var hadPoints = d.points && d.points.length > 0;
        console.log('[TruckMap] api_inzone_history : ' + (hadPoints ? d.points.length : 0)
            + ' points (source=' + (d.source || 'inzone')
            + ', tracking_active=' + d.tracking_active + ')');

        if (hadPoints) {
            var pts = d.points.map(function(p) {
                return [parseFloat(p.lat), parseFloat(p.lng)];
            });
            try {
                var drawn = window.TruckMap.restoreHistoryTrack(pts, { fitBounds: true });
                console.log('[TruckMap] Tracé restauré : ' + drawn + ' points dessinés en segments');
            } catch(e) {
                console.warn('[TruckMap] restoreHistoryTrack error:', e);
            }
        }
        // Ne PAS réactiver automatiquement le tracking depuis localStorage
        // Le tracking ne doit être réactivé que si le serveur indique qu'il est actif (d.tracking_active)
        // Cela évite que le traçage commence automatiquement au chargement de la page
        if (d.tracking_active) {
            try { window.TruckMap.setDrawPolyline(true); } catch(e){}
            window._webIsInZone = true;
            try {
                var startBtn = document.getElementById('manualBtn');
                var endBtn   = document.getElementById('endManualBtn');
                if (startBtn) startBtn.disabled = true;
                if (endBtn)   endBtn.disabled   = false;
            } catch(e){}
        }
        // Sinon (tracking inactif) : drawPolyline reste à false, les anciens segments
        // restent visibles mais aucun nouveau point n'est ajouté tant que Start n'est pas pressé.
    }).fail(function(xhr) {
        console.warn('[TruckMap] Erreur chargement tracé InZone', xhr && xhr.status);
    });
}

function _fetchGpsHistory() {
    if (_gpsHistoryLoaded) return;
    var params = '';
    if (WEB_CONFIG.vehicleId) {
        params = 'vehicleId=' + encodeURIComponent(WEB_CONFIG.vehicleId);
    }
    if (WEB_CONFIG.nocamion) {
        params += (params ? '&' : '') + 'nocamion=' + encodeURIComponent(WEB_CONFIG.nocamion);
    }
    if (!params) return;
    if (WEB_CONFIG.idChauffeur) {
        params += '&idchauffeur=' + encodeURIComponent(WEB_CONFIG.idChauffeur);
    }
    params += '&IsInZone=' + (window._webIsInZone ? '1' : '0');
    $.getJSON('/web/api_samsara_history?' + params, function(d) {
        if (d && d.ok && d.points && d.points.length > 0) {
            _gpsHistoryLoaded = true;
            var pts = d.points.map(function(p) {
                return [parseFloat(p.lat), parseFloat(p.lng)];
            });
            try { window.TruckMap.injectHistoryPoints(pts); } catch(e) {
                console.warn('[TruckMap] injectHistoryPoints error:', e);
            }
            console.log('[TruckMap] Historique GPS chargé: ' + pts.length + ' points');
        } else {
            console.log('[TruckMap] Aucun historique GPS disponible');
        }
    }).fail(function() {
        console.warn('[TruckMap] Erreur chargement historique GPS');
    });
}

function _fetchAndDisplayPolyline(id) {
    $.getJSON('/web/api_polyline?id=' + encodeURIComponent(id), function(d) {
        if (d && d.ok && d.parcours) {
            try {
                var pts = d.parcours;
                // Le serveur renvoie maintenant un tableau [{lat,lng}, ...]
                // Fallback si c'est encore une string (ancien format)
                if (typeof pts === 'string') {
                    try { pts = JSON.parse(pts); } catch(e2) {
                        // Parser le format "lat,lng_lat,lng_..." manuellement
                        pts = _parseParcoursString(pts);
                    }
                }
                if (Array.isArray(pts) && pts.length > 0) {
                    var coords = pts.map(function(p) {
                        return [parseFloat(p.lat || p.Lat || p[0]), parseFloat(p.lng || p.Lng || p[1])];
                    });
                    var meta = (WEB_CONFIG.polylineButtons || []).find(function(b) { return String(b.id) === String(id); });
                    window.TruckMap.receivePolylineData({
                        id: String(id),
                        coords: coords,
                        color: (meta && meta.color) ? meta.color : (d.couleur || '#ff0000'),
                        ordre: (meta && meta.ordre) ? meta.ordre : 1
                    });
                } else {
                    console.warn('[TruckMap] Polyline vide pour id=' + id);
                }
            } catch(e) {
                console.warn('[TruckMap] Parse polyline error:', e);
            }
        } else {
            console.warn('[TruckMap] api_polyline réponse invalide pour id=' + id, d);
        }
    }).fail(function(xhr, status, err) {
        console.warn('[TruckMap] api_polyline AJAX error pour id=' + id, status, err);
    });
}

// Parser le format parcours brut "lat,lng_lat,lng_..." (fallback JS, reproduit MAUI ParsePolyline)
function _parseParcoursString(str) {
    if (!str) return [];
    str = str.replace(/"/g, '').trim();
    var pairs = str.split(/[_;\| ]+/);
    var result = [];
    for (var i = 0; i < pairs.length; i++) {
        var parts = pairs[i].split(',');
        if (parts.length >= 2) {
            var lat = parseFloat(parts[0].trim());
            var lng = parseFloat(parts[1].trim());
            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                result.push({ lat: lat, lng: lng });
            }
        }
    }
    return result;
}

function _fetchAllPolylines() {
    var totalButtons = (WEB_CONFIG.polylineButtons || []).length;
    if (totalButtons === 0) return;

    var loadedCount = 0;
    (WEB_CONFIG.polylineButtons || []).forEach(function(btn, i) {
        setTimeout(function() {
            _fetchAndDisplayPolyline(btn.id);
            loadedCount++;
            // Après que toutes les polylines ont été chargées, remettre les polylines dynamiques au premier plan
            if (loadedCount === totalButtons) {
                setTimeout(function() {
                    bringDynamicPolylinesToFront();
                }, 2000); // Délai augmenté pour s'assurer que toutes les polylines sont ajoutées à la carte et traitées
            }
        }, i * 80);
    });
}

// ═══════════════════════════════════════════════════════════════
// Fonctions utilitaires pour le filtrage des polylines (issues de map_nav_zone.php)
// ═══════════════════════════════════════════════════════════════
function calculateDistance(lat1, lng1, lat2, lng2) {
    var R = 6371000; // Rayon de la Terre en mètres
    var dLat = ((lat2 - lat1) * Math.PI) / 180;
    var dLng = ((lng2 - lng1) * Math.PI) / 180;
    var a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLng / 2) *
        Math.sin(dLng / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function calculateTimeDiff(time1, time2) {
    if (!time1 || !time2) return 0;
    var t1 = new Date(time1).getTime();
    var t2 = new Date(time2).getTime();
    if (isNaN(t1) || isNaN(t2)) return 0;
    return Math.abs(t2 - t1) / (1000 * 60); // Convertir en minutes
}

function calculateBearing(lat1, lng1, lat2, lng2) {
    var y = Math.sin(lng2 - lng1) * Math.cos(lat2);
    var x =
        Math.cos(lat1) * Math.sin(lat2) -
        Math.sin(lat1) * Math.cos(lat2) * Math.cos(lng2 - lng1);
    var brng = Math.atan2(y, x);
    return ((brng * 180) / Math.PI + 360) % 360;
}

function calculateAngle(p1, p2, p3) {
    var bearing1 = calculateBearing(p2[0], p2[1], p1[0], p1[1]);
    var bearing2 = calculateBearing(p2[0], p2[1], p3[0], p3[1]);
    var angle = Math.abs(bearing2 - bearing1);
    if (angle > 180) angle = 360 - angle;
    return angle;
}

// ═══════════════════════════════════════════════════════════════
// Fonction pour nettoyer les polylines dynamiques avant rechargement
// ═══════════════════════════════════════════════════════════════
function clearDynamicPolylines() {
    try {
        // Nettoyer les polylines passées
        if (Array.isArray(window.__dynPastPolylines)) {
            for (var i = 0; i < window.__dynPastPolylines.length; i++) {
                try {
                    map.removeLayer(window.__dynPastPolylines[i]);
                } catch (e) { }
            }
        }
        window.__dynPastPolylines = [];

        // Nettoyer les marqueurs numérotés
        if (Array.isArray(window.__dynPolylineMarkers)) {
            for (var i = 0; i < window.__dynPolylineMarkers.length; i++) {
                try {
                    map.removeLayer(window.__dynPolylineMarkers[i]);
                } catch (e) { }
            }
        }
        window.__dynPolylineMarkers = [];
        window.__dynPolylineMarkerCounter = 0;

        console.log("[TruckMap] clearDynamicPolylines: polylines et marqueurs effacés");
    } catch (e) {
        console.error("[TruckMap] Error in clearDynamicPolylines:", e);
    }
}

// ═══════════════════════════════════════════════════════════════
// Fonction pour remettre les polylines dynamiques au premier plan
// ═══════════════════════════════════════════════════════════════
function bringDynamicPolylinesToFront() {
    try {
        // Mettre les polylines de ville en arrière-plan
        for (var id in window.cityPolylines) {
            if (window.cityPolylines.hasOwnProperty(id)) {
                var polylineGroup = window.cityPolylines[id];
                try {
                    polylineGroup.eachLayer(function (pl) {
                        pl.bringToBack();
                    });
                } catch (e) { }
            }
        }

        // Remettre la polyline live au premier plan
        if (window.__dynPolyline) {
            try {
                window.__dynPolyline.bringToFront();
            } catch (e) { }
        }

        // Remettre aussi les polylines passées au premier plan
        if (Array.isArray(window.__dynPastPolylines)) {
            window.__dynPastPolylines.forEach(function (pl) {
                try {
                    pl.bringToFront();
                } catch (e) { }
            });
        }

        // Remettre les marqueurs au premier plan
        if (Array.isArray(window.__dynPolylineMarkers)) {
            window.__dynPolylineMarkers.forEach(function (marker) {
                try {
                    marker.bringToFront();
                } catch (e) { }
            });
        }

        console.log("[TruckMap] bringDynamicPolylinesToFront: polylines remises au premier plan");
    } catch (e) {
        console.error("[TruckMap] Error in bringDynamicPolylinesToFront:", e);
    }
}

// ═══════════════════════════════════════════════════════════════
// Fonction pour charger et afficher les polylines dynamiques (GPS historique)
// ═══════════════════════════════════════════════════════════════
window.loadDynamicPolylines = function(segments) {
    try {
        console.log("[TruckMap] loadDynamicPolylines called with", segments.length, "segments");

        if (!segments || segments.length === 0) {
            console.log("[TruckMap] No segments to load");
            return;
        }

        // Nettoyer les polylines existantes avant de recharger
        clearDynamicPolylines();

        // Seuils de filtrage (identiques à map_nav_zone.php)
        var MAX_DISTANCE_METERS = 200;
        var MAX_TIME_MINUTES = 2;
        var MAX_SPEED_KMH = 100;
        var MAX_ANGLE_DEGREES = 90;

        // Accumuler tous les points et métadonnées pour les marqueurs IsInZone et adresses
        var allParsedPoints = [];
        var allParsedMeta = [];

        // Parser chaque segment avec filtrage
        segments.forEach(function(segmentStr, index) {
            try {
                // Format: "lat,lng, timespan: ..., chauffeur: ..., adresse: ..., isinzone: ..."
                var parts = segmentStr.split("_");
                var parsedPoints = [];
                var parsedMeta = [];

                // Parser les points et métadonnées
                for (var i = 0; i < parts.length; i++) {
                    var pt = parts[i];
                    var coords = pt.split(",");
                    if (coords.length >= 2) {
                        var lat = parseFloat(coords[0]);
                        var lng = parseFloat(coords[1]);
                        // Filtrer les coordonnées invalides (lat=0, lng=0)
                        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                            // Extraire timespan, isinzone, statutpoly et adresse si disponibles
                            var timespan = null;
                            var isinzone = 0;
                            var statutpoly = 0;
                            var adresse = '';
                            var chauffeur = '';
                            var timespanMatch = pt.match(/timespan:\s*([^\s,]+)/);
                            var isinzoneMatch = pt.match(/isinzone:\s*([^\s,]+)/);
                            var statutpolyMatch = pt.match(/statutpoly:\s*([^\s,]+)/);
                            var adresseMatch = pt.match(/adresse\s*:\s*(.*)$/i);
                            var chauffeurMatch = pt.match(/chauffeur\s*:\s*([^,]+)/);
                            if (timespanMatch) {
                                timespan = timespanMatch[1];
                            }
                            if (isinzoneMatch) {
                                isinzone = parseInt(isinzoneMatch[1], 10) || 0;
                            }
                            if (statutpolyMatch) {
                                statutpoly = parseInt(statutpolyMatch[1], 10) || 0;
                            }
                            if (adresseMatch) {
                                adresse = adresseMatch[1].trim();
                            }
                            if (chauffeurMatch) {
                                chauffeur = chauffeurMatch[1].trim();
                            }
                            parsedMeta.push({ timespan: timespan, isinzone: isinzone, statutpoly: statutpoly, adresse: adresse, chauffeur: chauffeur });
                            parsedPoints.push([lat, lng]);
                            // Accumuler pour les marqueurs IsInZone et adresses
                            allParsedPoints.push([lat, lng]);
                            allParsedMeta.push({ timespan: timespan, isinzone: isinzone, statutpoly: statutpoly, adresse: adresse, chauffeur: chauffeur });
                        }
                    }
                }

                if (parsedPoints.length < 2) return;

                // ═══════════════════════════════════════════════════════════════
                // CRÉER DES SEGMENTS SÉPARÉS BASÉS SUR Statutpoly
                // Statutpoly = 0 : Start, Statutpoly = 2 : Stop
                // Chaque segment start->stop est indépendant
                // ═══════════════════════════════════════════════════════════════
                var zoneSegments = [];
                var currentSegment = [];
                var currentMeta = [];
                var inSegment = false;

                for (var k = 0; k < parsedPoints.length; k++) {
                    var currentStatutpoly = (parsedMeta[k] && parsedMeta[k].statutpoly) || 0;

                    if (!inSegment && currentStatutpoly === 0) {
                        // Début d'un segment (Start)
                        inSegment = true;
                        currentSegment = [parsedPoints[k]];
                        currentMeta = [parsedMeta[k]];
                    } else if (inSegment && currentStatutpoly !== 2) {
                        // Continuer le segment (pas encore Stop)
                        currentSegment.push(parsedPoints[k]);
                        currentMeta.push(parsedMeta[k]);
                    } else if (inSegment && currentStatutpoly === 2) {
                        // Fin d'un segment (Stop)
                        inSegment = false;
                        currentSegment.push(parsedPoints[k]); // Inclure le point Stop
                        currentMeta.push(parsedMeta[k]);
                        if (currentSegment.length >= 2) {
                            zoneSegments.push({ points: currentSegment.slice(), meta: currentMeta.slice() });
                        }
                        currentSegment = [];
                        currentMeta = [];
                    }
                }

                // Si le dernier point est encore dans un segment (pas de Stop)
                if (inSegment && currentSegment.length >= 2) {
                    zoneSegments.push({ points: currentSegment, meta: currentMeta });
                }

                if (zoneSegments.length === 0) {
                    console.log("[TruckMap] No Statutpoly segments in segment", index);
                    return;
                }

                // ═══════════════════════════════════════════════════════════════
                // FILTRAGE DES SAUTS DE DISTANCE/TEMPS À L'INTÉRIEUR DE CHAQUE SEGMENT
                // ═══════════════════════════════════════════════════════════════
                var filteredSegments = [];

                for (var s = 0; s < zoneSegments.length; s++) {
                    var segment = zoneSegments[s];
                    var currentFiltered = [];
                    var lastPoint = null;
                    var lastTime = null;

                    for (var m = 0; m < segment.points.length; m++) {
                        var lat = segment.points[m][0];
                        var lng = segment.points[m][1];
                        var meta = segment.meta[m];
                        var currentTime = meta ? meta.timespan : null;

                        if (lastPoint && lastTime && currentTime) {
                            var distance = calculateDistance(
                                lastPoint[0],
                                lastPoint[1],
                                lat,
                                lng
                            );
                            var timeDiff = calculateTimeDiff(lastTime, currentTime);
                            var speedKmh = 0;
                            if (timeDiff > 0) {
                                speedKmh = distance / 1000 / (timeDiff / 60);
                            }

                            // Si saut trop important ou vitesse excessive, on crée un nouveau segment
                            if (
                                distance > MAX_DISTANCE_METERS ||
                                timeDiff > MAX_TIME_MINUTES ||
                                speedKmh > MAX_SPEED_KMH
                            ) {
                                if (currentFiltered.length >= 2) {
                                    filteredSegments.push(currentFiltered.slice());
                                }
                                currentFiltered = [[lat, lng]];
                            } else {
                                currentFiltered.push([lat, lng]);
                            }
                        } else {
                            currentFiltered.push([lat, lng]);
                        }

                        lastPoint = [lat, lng];
                        lastTime = currentTime;
                    }

                    if (currentFiltered.length >= 2) {
                        filteredSegments.push(currentFiltered);
                    }
                }

                // Filtrage supplémentaire basé sur les angles anormaux
                var segmentsWithAngleFilter = [];
                for (var k = 0; k < filteredSegments.length; k++) {
                    var segment = filteredSegments[k];
                    if (segment.length < 3) {
                        segmentsWithAngleFilter.push(segment);
                        continue;
                    }

                    var angleSegment = [segment[0], segment[1]];
                    for (var m = 2; m < segment.length; m++) {
                        var p1 = segment[m - 2];
                        var p2 = segment[m - 1];
                        var p3 = segment[m];

                        var angle = calculateAngle(p1, p2, p3);
                        if (angle > MAX_ANGLE_DEGREES) {
                            if (angleSegment.length >= 2) {
                                segmentsWithAngleFilter.push(angleSegment.slice());
                            }
                            angleSegment = [p2, p3];
                        } else {
                            angleSegment.push(p3);
                        }
                    }

                    if (angleSegment.length >= 2) {
                        segmentsWithAngleFilter.push(angleSegment);
                    }
                }

                // Dessiner chaque segment filtré
                for (var n = 0; n < segmentsWithAngleFilter.length; n++) {
                    var latlngs = segmentsWithAngleFilter[n].map(function(pt) {
                        return [pt[1], pt[0]]; // [lng, lat] pour Leaflet
                    });

                    if (latlngs.length >= 2) {
                        const polyline = L.polyline(latlngs, {
                            color: "blue", // Bleu pour les polylines dynamiques (transparent avec sani_ipad)
                            weight: 5,
                            opacity: 0.8,
                            zIndexOffset: 20000 // z-index très élevé pour rester au premier plan
                        }).addTo(map);

                        // Forcer la polyline au premier plan
                        try {
                            polyline.bringToFront();
                        } catch(e) {}

                        // Stocker la polyline dans __dynPastPolylines pour persistance
                        window.__dynPastPolylines.push(polyline);

                        // Ajouter des marqueurs numérotés au début et à la fin du segment
                        window.__dynPolylineMarkerCounter++;
                        var segmentNumber = window.__dynPolylineMarkerCounter;

                        // Marqueur de début (vert)
                        var startIcon = createNumberedCircleIcon(segmentNumber, 'green');
                        var startMarker = L.marker([latlngs[0][0], latlngs[0][1]], {
                            icon: startIcon,
                            interactive: true,
                            zIndexOffset: 2000
                        }).bindPopup("Parcours " + segmentNumber + " - Début");
                        startMarker.addTo(map);
                        window.__dynPolylineMarkers.push(startMarker);

                        // Marqueur de fin (rouge)
                        var endIcon = createNumberedCircleIcon(segmentNumber, 'red');
                        var endMarker = L.marker([latlngs[latlngs.length - 1][0], latlngs[latlngs.length - 1][1]], {
                            icon: endIcon,
                            interactive: true,
                            zIndexOffset: 2000
                        }).bindPopup("Parcours " + segmentNumber + " - Fin");
                        endMarker.addTo(map);
                        window.__dynPolylineMarkers.push(endMarker);

                        console.log(
                            `[TruckMap] Dynamic polyline ${index + 1} segment ${n + 1} added with ${latlngs.length} points`
                        );
                    }
                }
            } catch (e) {
                console.error(`[TruckMap] Error parsing segment ${index}:`, e);
            }
        });

        // Créer les marqueurs IsInZone numérotés avec tous les points accumulés
        createZoneMarkers(allParsedPoints, allParsedMeta);

        // Créer les marqueurs d'adresses avec popup formaté
        createAddressMarkers(allParsedPoints, allParsedMeta);

        // Remettre les polylines dynamiques au premier plan
        bringDynamicPolylinesToFront();

        console.log("[TruckMap] loadDynamicPolylines completed");
    } catch (e) {
        console.error("[TruckMap] Error in loadDynamicPolylines:", e);
    }
};

// ═══════════════════════════════════════════════════════════════
// Fonction pour créer une icône de cercle numéroté
// ═══════════════════════════════════════════════════════════════
function createNumberedCircleIcon(number, color) {
    var cssColor = color === 'green' ? '#28a745' : '#dc3545';
    var iconHtml = '<div style="'
        + 'width: 24px; height: 24px; border-radius: 50%;'
        + 'background-color: ' + cssColor + ';'
        + 'color: white; font-weight: bold; font-size: 14px;'
        + 'display: flex; align-items: center; justify-content: center;'
        + 'border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);'
        + '">' + number + '</div>';

    return L.divIcon({
        className: 'numbered-circle-marker',
        html: iconHtml,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
}

// ═══════════════════════════════════════════════════════════════
// Fonction pour créer les marqueurs numérotés sur les segments IsInZone=1
// ═══════════════════════════════════════════════════════════════
function createZoneMarkers(points, meta) {
    if (!points || points.length < 2 || !meta) return;

    // Identifier le chauffeur pour le compteur de segments
    var chauffeurKey = WEB_CONFIG.idChauffeur || 'default';
    if (!window.zoneSegmentCounters) {
        window.zoneSegmentCounters = {};
    }
    if (!window.zoneSegmentCounters[chauffeurKey]) {
        window.zoneSegmentCounters[chauffeurKey] = 0;
    }

    var segmentNumber = 0;
    var inZone = false;
    var segmentStart = null;

    for (var i = 0; i < points.length; i++) {
        var p = points[i];
        var currentIsInZone = (meta[i] && meta[i].isinzone === 1);

        if (!inZone && currentIsInZone) {
            // Début d'un segment IsInZone=1
            inZone = true;
            segmentStart = p;
        } else if (inZone && !currentIsInZone) {
            // Fin d'un segment IsInZone=1
            inZone = false;
            segmentNumber++;
            window.zoneSegmentCounters[chauffeurKey] = segmentNumber;

            // Créer le marqueur vert au début
            if (segmentStart) {
                var greenIcon = createNumberedCircleIcon(segmentNumber, 'green');
                var greenMarker = L.marker([segmentStart[1], segmentStart[0]], { icon: greenIcon }).addTo(map);
            }

            // Créer le marqueur rouge à la fin (point précédent)
            var prevPoint = points[i - 1];
            if (prevPoint) {
                var redIcon = createNumberedCircleIcon(segmentNumber, 'red');
                var redMarker = L.marker([prevPoint[1], prevPoint[0]], { icon: redIcon }).addTo(map);
            }

            segmentStart = null;
        }
    }

    // Si le dernier point est encore dans la zone
    if (inZone && segmentStart) {
        segmentNumber++;
        window.zoneSegmentCounters[chauffeurKey] = segmentNumber;

        var greenIcon = createNumberedCircleIcon(segmentNumber, 'green');
        var greenMarker = L.marker([segmentStart[1], segmentStart[0]], { icon: greenIcon }).addTo(map);

        var lastPoint = points[points.length - 1];
        if (lastPoint) {
            var redIcon = createNumberedCircleIcon(segmentNumber, 'red');
            var redMarker = L.marker([lastPoint[1], lastPoint[0]], { icon: redIcon }).addTo(map);
        }
    }
}

// ═══════════════════════════════════════════════════════════════
// Fonction pour créer les marqueurs d'adresses avec popup formaté
// ═══════════════════════════════════════════════════════════════
function createAddressMarkers(points, meta) {
    if (!points || !meta) return;

    for (var i = 0; i < points.length; i++) {
        var p = points[i];
        var m = meta[i];
        var addr = (m && m.adresse) ? m.adresse.trim() : '';
        var chauffeur = (m && m.chauffeur) ? m.chauffeur.trim() : '';
        var hre = (m && m.timespan) ? m.timespan : 'N/A';

        if (!addr) continue; // Ne créer que si adresse présente

        var circle = L.circleMarker([p[1], p[0]], {
            radius: 6,
            color: '#ff5722',
            weight: 1,
            fillColor: '#ffccbc',
            fillOpacity: 0.95,
            pane: 'markerPane'
        });

        // clic direct sur le cercle -> afficher le popup avec le format de map_nav_zone.php
        circle.on('click', function (e) {
            try {
                var content = '<div style="min-width:260px"><b>Camion: </b>' + escapeHtml(WEB_CONFIG.nocamion || 'N/A') + '<br/>';
                content += '<b>Chauffeur: </b>' + escapeHtml(chauffeur) + '<br/>';
                content += '<b>Heure: </b>' + escapeHtml(hre) + '<br/><b>Adresse: </b>' + escapeHtml(addr) + '</div>';
                L.popup({ maxWidth: 360 }).setLatLng(e.latlng).setContent(content).openOn(map);
            } catch (err) { console.warn('show popup circle', err); }
        });

        circle.bindTooltip(addr, { direction: 'top', offset: [0, -8], className: 'address-tooltip' });
        circle.addTo(map);
    }
}

// Fonction utilitaire pour échapper le HTML
function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ═══════════════════════════════════════════════════════════════
// SCRIPT PRINCIPAL (identique à truckmap.html MAUI)
// ═══════════════════════════════════════════════════════════════
(function() {

    var userZoomLocked = false;
    window.cityPolylinesData = [];
    window.cityPolylineMarkers = window.cityPolylineMarkers || [];
    window.visiblePolylineIds = new Set();

    window.TruckMap = window.TruckMap || {};
    var idville = 0, idchauffeur = 0;
    var isAdmin = false;

    var API_BASE = "https://www.robert-daoust.ca";
    var TAG_ENDPOINTS = {
        list:          API_BASE + "/ville/maptag/list",
        listAllOpen:   API_BASE + "/ville/maptag/list_all_open",
        reasons:       API_BASE + "/ville/maptag/reasons",
        chauffeurs:    API_BASE + "/ville/maptag/chauffeurs",
        create:        API_BASE + "/ville/maptag/create",
        respond:       API_BASE + "/ville/maptag/respond",
        tagsByDriver:  API_BASE + "/tablettes_san/tags/chauffeur",
        update:        API_BASE + "/ville/maptag/update"
    };

    if (typeof L === "undefined") {
        sendToHost({ type: "jsError", message: "Leaflet non chargé" });
        return;
    }

    // ─── Init carte avec rotation (leaflet-rotate) ───
    var map = L.map('map', {
        zoomControl: true,
        attributionControl: false,
        rotate: true,
        bearing: 0,
        touchRotate: true,
        rotateControl: false
    }).setView([45.399276, -74.077881], 17);

    map.on('zoomstart', function() { userZoomLocked = true; });

    /* ===== Rotation de la carte (leaflet-rotate) ============================= */
    /**
     * Oriente la carte selon un cap (degrés, 0 = nord). Sans paramètre, remet à 0.
     */
    window.TruckMap.setBearing = function(deg) {
        try {
            if (typeof map.setBearing !== 'function') return; // plugin non chargé
            var b = Number(deg);
            if (!isFinite(b)) b = 0;
            map.setBearing(b);
        } catch(e) {
            console.warn('[TruckMap] setBearing error:', e);
        }
    };

    window.TruckMap.resetBearing = function() {
        window.TruckMap.setBearing(0);
    };

    // Activation/désactivation du suivi automatique du cap (cap du camion en haut).
    window.__autoBearing = false;
    window.TruckMap.setAutoBearing = function(enabled) {
        window.__autoBearing = !!enabled;
        if (!window.__autoBearing) window.TruckMap.resetBearing();
    };

    // ✅ Configuration avec serveurs de tuiles multiples pour éviter les erreurs 403
    var layerOsm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 20, attribution: "© OpenStreetMap", subdomains: ['a','b','c'],
        errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
    });

    // ✅ Alternative OpenStreetMap (serveur français HOT)
    var layerOsmFr = L.tileLayer("https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png", {
        maxZoom: 20, attribution: "© OpenStreetMap France", subdomains: ['a','b']
    });

    // ✅ CartoDB (très fiable, pas de restrictions)
    var layerCarto = L.tileLayer("https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png", {
        maxZoom: 20, attribution: "© CartoDB © OpenStreetMap", subdomains: ['a','b','c','d']
    });

    var layerSat = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
        maxZoom: 20, attribution: "© Esri"
    });

    // Par défaut: CartoDB (plus fiable)
    layerCarto.addTo(map);

    L.control.layers({
        "CartoDB": layerCarto,
        "OpenStreetMap": layerOsm,
        "OSM France": layerOsmFr,
        "Satellite Esri": layerSat
    }).addTo(map);

    setTimeout(function() { try { map.invalidateSize(true); } catch(e){} }, 150);
    window.forceMapResize = function() { try { map.invalidateSize(true); } catch(e){} };

    function makeLeafletDefaultIcon() {
        return L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25,41], iconAnchor: [12,41], popupAnchor: [1,-34],
            tooltipAnchor: [16,-28], shadowSize: [41,41]
        });
    }

    window.__truckMarker = null;
    window.__dynCoords = window.__dynCoords || [];
    window.__dynPolyline = window.__dynPolyline || null;
    window.__dynSegments = window.__dynSegments || [];
    window.__dynPastPolylines = window.__dynPastPolylines || []; // Stocke les polylines dynamiques passées
    window.__dynPolylineMarkers = window.__dynPolylineMarkers || []; // Stocke les marqueurs numérotés des polylines dynamiques
    window.__dynPolylineMarkerCounter = 0; // Compteur pour les numéros de polylines
    window.TruckMap.__truckIcon = makeLeafletDefaultIcon();

    window.TruckMap.setTruckIconUrl = function(url) {
        try {
            window.TruckMap.__truckIcon = L.icon({ iconUrl: url, iconSize: [40,40], iconAnchor: [20,30], popupAnchor: [0,-28] });
            if (window.__truckMarker) window.__truckMarker.setIcon(window.TruckMap.__truckIcon);
        } catch(e) { sendToHost({ type:'jsError', message:String(e), source:'setTruckIconUrl' }); }
    };

    // ✅ Créer une icône camion avec le numéro (nocamion) affiché
    function makeTruckDivIcon(label) {
        var txt = label || 'CAM';
        return L.divIcon({
            className: 'truck-div-icon',
            html: '<div style="background:#1565C0;color:#fff;font-weight:700;font-size:12px;'
                + 'padding:4px 8px;border-radius:6px;border:2px solid #fff;'
                + 'box-shadow:0 2px 8px rgba(0,0,0,0.45);white-space:nowrap;'
                + 'display:flex;align-items:center;gap:4px;">'
                + '<span style="font-size:16px;">🚛</span>' + txt + '</div>',
            iconSize: [60, 30],
            iconAnchor: [30, 15],
            popupAnchor: [0, -18]
        });
    }

    // Marqueur camion : icône standard Leaflet bleue
    // (makeTruckDivIcon() disponible si besoin mais non utilisé par défaut)

    // Créer une icône de flèche orientée selon la direction du camion
    function makeRotatableTruckIcon(bearing) {
        return L.divIcon({
            className: 'rotatable-arrow-icon',
            html: '<div style="transform: rotate(' + bearing + 'deg);'
                  + 'width:0;height:0;'
                  + 'border-left:12px solid transparent;'
                  + 'border-right:12px solid transparent;'
                  + 'border-bottom:24px solid #1565C0;'
                  + 'filter:drop-shadow(0 2px 4px rgba(0,0,0,0.4));'
                  + '"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 24],
            popupAnchor: [0, -24]
        });
    }

    // ─── Suivi camion ───
    var followTruck = true;
    var followZoom = null;
    var lastTruckLatLng = null;
    var lastTruckBearing = null;
    var drawPolyline = false;
    var routePolyline = null;
    var routeDestinationLatLng = null;
    var routingControl = null;

    var followCheckbox = document.getElementById('chkFollowTruck');
    var autoBearingCheckbox = document.getElementById('chkAutoBearing');
    var stopRouteBtn = document.getElementById('stopRouteBtn');

    // Event listener pour la checkbox d'orientation automatique du cap
    if (autoBearingCheckbox) {
        autoBearingCheckbox.addEventListener('change', function() {
            window.TruckMap.setAutoBearing(this.checked);
        });
    }

    function syncFollowCheckboxState(state) {
        if (followCheckbox && followCheckbox.checked !== state) followCheckbox.checked = state;
    }
    function updateRouteButtonState() {
        if (stopRouteBtn) stopRouteBtn.disabled = !routeDestinationLatLng && !routingControl;
    }
    function hexToRgba(hex, alpha) {
        if (!hex) return 'rgba(0,0,0,' + alpha + ')';
        var h = hex.replace('#','');
        var bigint = parseInt(h.length===3 ? h.split('').map(function(c){return c+c;}).join('') : h, 16);
        return 'rgba(' + ((bigint>>16)&255) + ',' + ((bigint>>8)&255) + ',' + (bigint&255) + ',' + alpha + ')';
    }
    function clearRoutePolyline() {
        if (routePolyline) { try { map.removeLayer(routePolyline); } catch(e){} routePolyline = null; }
        if (routingControl) { try { map.removeControl(routingControl); } catch(e){} routingControl = null; }
        routeDestinationLatLng = null;
        updateRouteButtonState();
    }
    function recenterTruck(forceSetView) {
        if (!lastTruckLatLng) return;
        var zoomToUse = userZoomLocked ? (map.getZoom() || 17) : (followZoom != null ? followZoom : (map.getZoom() || 17));
        if (forceSetView) { map.setView(lastTruckLatLng, zoomToUse); }
        else if (userZoomLocked || followZoom == null) { map.panTo(lastTruckLatLng, { animate:true, duration:0.35 }); }
        else { map.setView(lastTruckLatLng, zoomToUse); }
    }
    function setFollowState(state, options) {
        followTruck = !!state;
        syncFollowCheckboxState(followTruck);
        if (followTruck && (options && options.forceCenter)) recenterTruck(true);
    }
    map.on('dragstart', function() { if (followTruck) setFollowState(false); });

    window.TruckMap.setFollowTruck = function(enabled, zoom) {
        setFollowState(enabled);
        if (!userZoomLocked) followZoom = (zoom == null) ? null : (+zoom || null);
        if (followTruck && lastTruckLatLng) recenterTruck(true);
    };
    window.TruckMap.centerOnTruck = function() { recenterTruck(true); };

    if (followCheckbox) {
        followCheckbox.addEventListener('change', function() {
            setFollowState(followCheckbox.checked, { forceCenter: followCheckbox.checked });
        }, { passive: true });
    }
    if (autoBearingCheckbox) {
        autoBearingCheckbox.addEventListener('change', function() {
            window.TruckMap.setAutoBearing(autoBearingCheckbox.checked);
        }, { passive: true });
    }
    setFollowState(true, { forceCenter: true });

    // Seuil de distance (mètres) pour détecter un saut/gap dans le tracé GPS
    // Augmenté à 200m pour éviter les coupures inutiles dans le traçage en temps réel
    // (les points GPS sont naturellement espacés de plusieurs secondes à plusieurs minutes)
    var GAP_THRESHOLD_METERS = 200;

    function _haversineDistance(lat1, lng1, lat2, lng2) {
        var R = 6371000;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng/2) * Math.sin(dLng/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // Calcul du bearing (direction) entre deux points GPS en degrés
    function _calculateBearing(lat1, lng1, lat2, lng2) {
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var lat1Rad = lat1 * Math.PI / 180;
        var lat2Rad = lat2 * Math.PI / 180;
        var y = Math.sin(dLng) * Math.cos(lat2Rad);
        var x = Math.cos(lat1Rad) * Math.sin(lat2Rad) -
                Math.sin(lat1Rad) * Math.cos(lat2Rad) * Math.cos(dLng);
        var bearing = Math.atan2(y, x) * 180 / Math.PI;
        return (bearing + 360) % 360; // Normaliser entre 0 et 360
    }

    function _finalizeCurrentSegment() {
        if (window.__dynPolyline) {
            // Mémoriser le dernier point du segment courant pour pouvoir décider
            // au prochain Start si l'on relie (< GAP_THRESHOLD_METERS) ou si l'on
            // commence un nouveau polyline (> GAP_THRESHOLD_METERS).
            if (window.__dynCoords.length) {
                window.__dynLastBeforeStop = window.__dynCoords[window.__dynCoords.length - 1];
                window.__dynLastSegment    = window.__dynPolyline;
            }
            window.__dynSegments.push(window.__dynPolyline);
            window.__dynPolyline = null;
        }
        window.__dynCoords = [];
    }

    window.TruckMap.setDrawPolyline = function(enabled) {
        var wasEnabled = drawPolyline;
        drawPolyline = !!enabled;
        if (wasEnabled && !drawPolyline) {
            // Suspendre : finaliser le segment courant mais tout garder visible
            _finalizeCurrentSegment();
        }
    };

    // ─── Itinéraire via Leaflet Routing Machine ───
    function ensureRoutingControl() {
        if (routingControl) return routingControl;
        routingControl = L.Routing.control({
            waypoints: [], addWaypoints: false, routeWhileDragging: false,
            draggableWaypoints: false, fitSelectedRoutes: false, show: false,
            createMarker: function() { return null; },
            lineOptions: { styles: [{ color:'#ff7043', weight:6, opacity:0.9 }] }
        }).addTo(map);
        return routingControl;
    }
    function showRouteToTag(destLat, destLng) {
        if (typeof destLat !== 'number' || typeof destLng !== 'number' || isNaN(destLat) || isNaN(destLng)) { alert("Coordonnées du tag invalides."); return; }
        if (!lastTruckLatLng) { alert("Position du camion inconnue."); return; }
        var from = lastTruckLatLng, to = L.latLng(destLat, destLng);
        routeDestinationLatLng = to;
        if (routingControl) { try { map.removeControl(routingControl); } catch(e){} routingControl = null; }
        var rc = ensureRoutingControl();
        rc.setWaypoints([from, to]);
        updateRouteButtonState();
        setFollowState(true, { forceCenter: true });
    }

    // ─── TruckMap API: position camion ───
    window.TruckMap.updateTruckPosition = function(lat, lng, heading) {
        try {
            if (typeof lat !== 'number' || typeof lng !== 'number') return;

            // Utiliser le heading de Samsara si disponible, sinon calculer le bearing
            var bearing = 0;
            if (typeof heading === 'number' && !isNaN(heading)) {
                // Utiliser le heading de Samsara
                bearing = heading;
                lastTruckBearing = bearing;
            } else if (lastTruckLatLng) {
                // Calculer le bearing si on a une position précédente (fallback)
                bearing = _calculateBearing(lastTruckLatLng.lat, lastTruckLatLng.lng, lat, lng);
                // Mettre à jour le bearing seulement si le camion a bougé significativement (> 10m)
                if (_haversineDistance(lastTruckLatLng.lat, lastTruckLatLng.lng, lat, lng) > 10) {
                    lastTruckBearing = bearing;
                }
            } else {
                bearing = lastTruckBearing || 0;
            }

            // Faire pivoter la carte selon la direction du camion si le suivi automatique du cap est activé
            if (window.__autoBearing) {
                window.TruckMap.setBearing(bearing); // Utiliser le bearing tel quel (pas d'inversion)
            }

            // Créer une icône de cercle vert pour le camion (sans emoji)
            var icon = L.divIcon({
                className: 'truck-circle-icon',
                html: '<div style="background:#4CAF50;color:#fff;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.4);">●</div>',
                iconSize: [30,30],
                iconAnchor: [15,15]
            });

            if (!window.__truckMarker) {
                window.__truckMarker = L.marker([lat,lng], { title:'Camion', icon:icon, zIndexOffset:1000 }).addTo(map);
                try { window.__truckMarker.bringToFront(); } catch(e){}
            } else {
                window.__truckMarker.setLatLng([lat,lng]);
                window.__truckMarker.setIcon(icon);
                try { window.__truckMarker.bringToFront(); } catch(e){}
            }
            lastTruckLatLng = L.latLng(lat, lng);
            if (routingControl && routeDestinationLatLng) { ensureRoutingControl().setWaypoints([lastTruckLatLng, routeDestinationLatLng]); }
            if (followTruck) recenterTruck();
        } catch(e) { sendToHost({ type:'jsError', message:String(e), source:'updateTruckPosition' }); }
    };

    window.TruckMap.updateTruck = function(lat, lng, heading) {
        try {
            window.TruckMap.updateTruckPosition(lat, lng, heading);
            if (!drawPolyline) return;
            if (typeof lat === 'number' && typeof lng === 'number') {
                // Filtrer les coordonnées invalides (lat=0, lng=0)
                if (lat === 0 && lng === 0) return;

                var last = window.__dynCoords.length ? window.__dynCoords[window.__dynCoords.length-1] : null;

                // ── Reprise après un Stop : si on est proche du dernier point avant Stop,
                // relier au segment précédent ; sinon commencer un nouveau polyline.
                if (!last && window.__dynLastBeforeStop) {
                    var prev = window.__dynLastBeforeStop;
                    var dResume = _haversineDistance(prev[0], prev[1], lat, lng);
                    if (dResume <= GAP_THRESHOLD_METERS && window.__dynLastSegment) {
                        // Continuité : reprendre le polyline précédent (retiré de __dynSegments)
                        var idxSeg = window.__dynSegments.indexOf(window.__dynLastSegment);
                        if (idxSeg !== -1) window.__dynSegments.splice(idxSeg, 1);
                        window.__dynPolyline = window.__dynLastSegment;
                        try { window.__dynCoords = window.__dynPolyline.getLatLngs().map(function(ll){ return [ll.lat, ll.lng]; }); } catch(e) { window.__dynCoords = [prev]; }
                        last = window.__dynCoords[window.__dynCoords.length - 1];
                    }
                    // Consommer la référence : un seul essai de reprise par Stop/Start
                    window.__dynLastBeforeStop = null;
                    window.__dynLastSegment    = null;
                }

                if (!last || last[0] !== lat || last[1] !== lng) {
                    // Détecter un saut : si la distance dépasse le seuil, couper le segment
                    if (last && _haversineDistance(last[0], last[1], lat, lng) > GAP_THRESHOLD_METERS) {
                        _finalizeCurrentSegment();
                    }
                    window.__dynCoords.push([lat,lng]);
                    if (window.__dynPolyline) { window.__dynPolyline.setLatLngs(window.__dynCoords); }
                    else if (window.__dynCoords.length >= 2) { window.__dynPolyline = L.polyline(window.__dynCoords, { color:'blue', weight:6, opacity:0.9, zIndexOffset: 1000 }).addTo(map); try { window.__dynPolyline.bringToFront(); } catch(e){} }
                }
            }
        } catch(e) { sendToHost({ type:'jsError', message:String(e), source:'updateTruck' }); }
    };

    window.TruckMap.injectHistoryPoints = function(points) {
        try {
            if (!Array.isArray(points) || points.length === 0) return;
            if (drawPolyline) {
                for (var i = 0; i < points.length; i++) {
                    var lat = points[i][0], lng = points[i][1];
                    // Filtrer les coordonnées invalides (lat=0, lng=0)
                    if (typeof lat === 'number' && typeof lng === 'number' && !(lat === 0 && lng === 0)) {
                        var last = window.__dynCoords.length ? window.__dynCoords[window.__dynCoords.length-1] : null;
                        if (!last || last[0] !== lat || last[1] !== lng) {
                            // Détecter un saut dans l'historique : couper le segment
                            if (last && _haversineDistance(last[0], last[1], lat, lng) > GAP_THRESHOLD_METERS) {
                                _finalizeCurrentSegment();
                            }
                            window.__dynCoords.push([lat,lng]);
                        }
                    }
                }
                if (window.__dynPolyline) window.__dynPolyline.setLatLngs(window.__dynCoords);
                else if (window.__dynCoords.length >= 2) window.__dynPolyline = L.polyline(window.__dynCoords, { color:'blue', weight:6, opacity:0.9, zIndexOffset: 1000 }).addTo(map);
                try { if (window.__dynPolyline) window.__dynPolyline.bringToFront(); } catch(e){}
            }
            var lastPt = points[points.length-1];
            if (lastPt) window.TruckMap.updateTruckPosition(lastPt[0], lastPt[1]);
        } catch(e) { sendToHost({ type:'jsError', message:String(e), source:'injectHistoryPoints' }); }
    };

    /**
     * Restaure un parcours historique sous forme de segments bleus permanents.
     * Indépendant de drawPolyline (fonctionne même si le traçage live est désactivé).
     * Utilise un seuil plus large (RESTORE_GAP_METERS) que le traçage live pour ne pas
     * découper inutilement le parcours historique — les points GPS historiques sont
     * naturellement espacés (1 toutes les 10-60s) donc 30m est trop strict.
     * Le dernier point devient la référence de reprise (__dynLastBeforeStop).
     *
     * @param {Array<[lat,lng]>} points
     * @param {Object} [opts] { fitBounds: bool }
     */
    var RESTORE_GAP_METERS = 200; // seuil de coupure pour les tracés restaurés
    window.TruckMap.restoreHistoryTrack = function(points, opts) {
        try {
            if (!Array.isArray(points) || points.length === 0) return 0;
            opts = opts || {};
            var allLatLngs = [];
            var currentSeg = [];
            var totalDrawn = 0;

            function flushSegment() {
                if (currentSeg.length >= 2) {
                    var pl = L.polyline(currentSeg.slice(), { color:'blue', weight:6, opacity:0.9, zIndexOffset: 1000 }).addTo(map);
                    try { pl.bringToFront(); } catch(e){}
                    window.__dynSegments.push(pl);
                    totalDrawn += currentSeg.length;
                }
                currentSeg = [];
            }

            for (var i = 0; i < points.length; i++) {
                var lat = parseFloat(points[i][0]);
                var lng = parseFloat(points[i][1]);
                // Filtrer les coordonnées invalides (lat=0, lng=0)
                if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) continue;
                allLatLngs.push([lat, lng]);
                if (currentSeg.length > 0) {
                    var prev = currentSeg[currentSeg.length - 1];
                    if (prev[0] === lat && prev[1] === lng) continue; // doublon exact
                    if (_haversineDistance(prev[0], prev[1], lat, lng) > RESTORE_GAP_METERS) {
                        flushSegment();
                    }
                }
                currentSeg.push([lat, lng]);
            }
            flushSegment();

            // Mémoriser le dernier point pour la logique de reprise (Start après refresh)
            if (allLatLngs.length > 0) {
                window.__dynLastBeforeStop = allLatLngs[allLatLngs.length - 1];
                window.__dynLastSegment    = window.__dynSegments[window.__dynSegments.length - 1] || null;
                // Centrer le camion sur le dernier point connu
                try { window.TruckMap.updateTruckPosition(window.__dynLastBeforeStop[0], window.__dynLastBeforeStop[1]); } catch(e){}
                // Ajuster la vue pour montrer tout le parcours
                if (opts.fitBounds && allLatLngs.length >= 2) {
                    try {
                        var bounds = L.latLngBounds(allLatLngs);
                        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
                        userZoomLocked = true; // empêcher recentrage automatique
                    } catch(e){}
                }
            }
            return totalDrawn;
        } catch(e) {
            sendToHost({ type:'jsError', message:String(e), source:'restoreHistoryTrack' });
            return 0;
        }
    };

    window.TruckMap.setCityPolyline = function(coords) {
        try {
            if (window.cityPolyline) { try { map.removeLayer(window.cityPolyline); } catch(e){} window.cityPolyline = null; }
            if (Array.isArray(coords) && coords.length >= 2) {
                window.cityPolyline = L.polyline(coords, { color:'red', weight:5, opacity:0.7, dashArray:'10, 5', interactive:true }).addTo(map);
                try { window.cityPolyline.bringToBack(); } catch(e){}
                return true;
            }
            return false;
        } catch(e) { console.error('[TruckMap] setCityPolyline error:', e); return false; }
    };

    // ═══════════════════════════════════════════════════════════════
    // GESTION POLYLINES LÉGERS (lazy loading)
    // ═══════════════════════════════════════════════════════════════
    window.cityPolylines = window.cityPolylines || {};
    window.cityPolylineMarkers = window.cityPolylineMarkers || {};
    window.cityPolylineButtons = [];
    window.cityPolylineVisibility = window.cityPolylineVisibility || {};
    window.cityPolylineMeta = window.cityPolylineMeta || {};
    window.activePolylineId = null;

    var REVERSE_SHOW_AHEAD_POINTS = 6;

    window.TruckMap.setPolylineButtons = function(buttons) {
        try {
            Object.values(window.cityPolylines || {}).forEach(function(pl) { try { map.removeLayer(pl); } catch(e){} });
            Object.values(window.cityPolylineMarkers || {}).forEach(function(markers) { try { if (markers && markers.start) map.removeLayer(markers.start); } catch(e){} });
            if (window.cityPolyline) { try { map.removeLayer(window.cityPolyline); } catch(e){} window.cityPolyline = null; }
            window.cityPolylines = {};
            window.cityPolylineMarkers = {};
            window.cityPolylineVisibility = {};
            window.cityPolylineMeta = {};
            window.activePolylineId = null;
            window.cityPolylineButtons = buttons || [];

            var panel = document.getElementById('polylineControlPanel');
            document.getElementById('btnPoly1').style.display = 'none';
            document.getElementById('btnPoly2').style.display = 'none';
            document.getElementById('btnPoly3').style.display = 'none';
            document.getElementById('btnPoly1').classList.remove('active');
            document.getElementById('btnPoly2').classList.remove('active');
            document.getElementById('btnPoly3').classList.remove('active');

            if (!buttons || buttons.length === 0) { panel.style.display = 'none'; return; }

            var maxBtns = Math.min(buttons.length, 3);
            for (var i = 0; i < maxBtns; i++) {
                var meta = buttons[i];
                var btnNum = i + 1;
                var btn = document.getElementById('btnPoly' + btnNum);
                if (btn) {
                    btn.setAttribute('data-polyline-id', meta.id);
                    btn.style.display = 'flex';
                    var colorDiv = document.getElementById('btnPoly' + btnNum + 'Color');
                    var numSpan = document.getElementById('btnPoly' + btnNum + 'Num');
                    var labelSpan = document.getElementById('btnPoly' + btnNum + 'Label');
                    if (colorDiv) colorDiv.style.background = '#FF0000';
                    if (numSpan) numSpan.textContent = String(meta.ordre || (i + 1));
                    if (labelSpan) labelSpan.textContent = meta.label || ('Secteur ' + (meta.ordre || (i + 1)));
                }
            }
            panel.style.display = 'block';
        } catch(e) { console.error('[TruckMap] setPolylineButtons error:', e); }
    };

    function getPolylineButtons() {
        return [document.getElementById('btnPoly1'), document.getElementById('btnPoly2'), document.getElementById('btnPoly3')].filter(Boolean);
    }
    function deactivateButtonByPolylineId(polylineId) {
        getPolylineButtons().forEach(function(btn) { if (btn.getAttribute('data-polyline-id') === polylineId) btn.classList.remove('active'); });
    }
    function activateButtonExclusive(polylineId) {
        getPolylineButtons().forEach(function(btn) {
            if (btn.getAttribute('data-polyline-id') === polylineId) btn.classList.add('active');
            else btn.classList.remove('active');
        });
    }
    function hidePolylineById(polylineId) {
        var pl = window.cityPolylines[polylineId];
        var meta = window.cityPolylineMeta[polylineId];
        try { if (pl) map.removeLayer(pl); } catch(e){}
        try { if (meta && meta.reverseLayers) meta.reverseLayers.forEach(function(r) { try { map.removeLayer(r.layer); } catch(e){} }); } catch(e){}
        try { var mk = window.cityPolylineMarkers[polylineId]; if (mk && mk.start) map.removeLayer(mk.start); } catch(e){}
        var legend = document.getElementById('polylineLegend');
        if (legend) legend.style.display = 'none';
        window.cityPolylineVisibility[polylineId] = false;
        deactivateButtonByPolylineId(polylineId);
        if (window.activePolylineId === String(polylineId)) window.activePolylineId = null;
    }
    function hideAllCityPolylines(exceptId) {
        Object.keys(window.cityPolylines || {}).forEach(function(id) {
            if (!exceptId || id !== String(exceptId)) hidePolylineById(id);
        });
    }

    function detectReverseSegments(latlngs) {
        if (latlngs.length < 3) return [{ points: latlngs, isReverse: false, startIndex: 0, endIndex: latlngs.length - 1 }];
        var segments = [];
        var currentSegment = { points: [latlngs[0]], isReverse: false, startIndex: 0, endIndex: 0 };
        for (var i = 1; i < latlngs.length - 1; i++) {
            var angle = calculateTurnAngle(latlngs[i-1], latlngs[i], latlngs[i+1]);
            var isReverse = angle > 120;
            if (isReverse !== currentSegment.isReverse) {
                currentSegment.points.push(latlngs[i]);
                currentSegment.endIndex = i;
                segments.push(currentSegment);
                currentSegment = { points: [latlngs[i]], isReverse: isReverse, startIndex: i, endIndex: i };
            } else {
                currentSegment.points.push(latlngs[i]);
            }
        }
        currentSegment.points.push(latlngs[latlngs.length - 1]);
        currentSegment.endIndex = latlngs.length - 1;
        segments.push(currentSegment);
        return segments;
    }
    function calculateTurnAngle(p1, p2, p3) {
        var dx1 = p2.lng - p1.lng, dy1 = p2.lat - p1.lat;
        var dx2 = p3.lng - p2.lng, dy2 = p3.lat - p2.lat;
        var dot = dx1*dx2 + dy1*dy2;
        var norm1 = Math.sqrt(dx1*dx1 + dy1*dy1);
        var norm2 = Math.sqrt(dx2*dx2 + dy2*dy2);
        if (norm1 === 0 || norm2 === 0) return 0;
        var cos = Math.max(-1, Math.min(1, dot / (norm1 * norm2)));
        return Math.acos(cos) * 180 / Math.PI;
    }

    function getClosestPolylinePointIndex(latlngs, truckLatLng) {
        if (!Array.isArray(latlngs) || latlngs.length === 0 || !truckLatLng) return -1;
        var bestIndex = -1, bestDist = Number.MAX_VALUE;
        for (var i = 0; i < latlngs.length; i++) {
            var d = map.distance(latlngs[i], truckLatLng);
            if (d < bestDist) { bestDist = d; bestIndex = i; }
        }
        return bestIndex;
    }
    function updateActivePolylineGuidance(truckLatLng) {
        try {
            var activeId = window.activePolylineId;
            if (!activeId || !window.cityPolylineVisibility[activeId]) return;
            var meta = window.cityPolylineMeta[activeId];
            if (!meta) return;
            var closestIdx = getClosestPolylinePointIndex(meta.latlngs, truckLatLng);
            (meta.reverseLayers || []).forEach(function(r) {
                var shouldShow = closestIdx >= 0 && closestIdx >= (r.startIndex - REVERSE_SHOW_AHEAD_POINTS);
                var isShown = map.hasLayer(r.layer);
                if (shouldShow && !isShown) { try { r.layer.addTo(map); } catch(e){} }
                else if (!shouldShow && isShown) { try { map.removeLayer(r.layer); } catch(e){} }
            });
        } catch(e) {}
    }

    window.TruckMap.receivePolylineData = function(data) {
        try {
            if (!data || !data.id || !Array.isArray(data.coords) || data.coords.length < 2) return;
            var id = String(data.id);
            var ordre = data.ordre || 1;
            var latlngs = [];
            for (var i = 0; i < data.coords.length; i++) {
                var pt = data.coords[i];
                var lat = Array.isArray(pt) ? Number(pt[0]) : Number(pt.lat || pt.Lat);
                var lng = Array.isArray(pt) ? Number(pt[1]) : Number(pt.lng || pt.Lng);
                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    latlngs.push(L.latLng(lat, lng));
                }
            }
            if (latlngs.length < 2) return;

            var segments = detectReverseSegments(latlngs);
            var polylineGroup = L.layerGroup();
            var reverseLayers = [];
            var hasReverse = false;

            segments.forEach(function(segment) {
                if (segment.points.length < 2) return;
                var segColor = segment.isReverse ? '#FF8C00' : '#FF0000';
                var segPoly = L.polyline(segment.points, {
                    color: segColor, weight: 6, opacity: 0.8,
                    dashArray: segment.isReverse ? '10, 5' : '5, 5', interactive: true,
                    zIndexOffset: 100 // z-index inférieur aux polylines dynamiques (10000)
                });
                if (segment.isReverse) {
                    reverseLayers.push({ layer: segPoly, startIndex: segment.startIndex, endIndex: segment.endIndex });
                    hasReverse = true;
                } else {
                    polylineGroup.addLayer(segPoly);
                }
            });

            var startIcon = L.divIcon({
                className: 'polyline-start-marker',
                html: '<div style="background:#4CAF50;color:#fff;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.4);">START</div>',
                iconSize: [40,40], iconAnchor: [20,20]
            });
            var popupText = '<b>Début du parcours ' + ordre + '</b>';
            if (hasReverse) popupText += '<br><span style="color:#FF8C00;">⚠️ Contient des retours</span>';
            var startMarker = L.marker(latlngs[0], { icon: startIcon, interactive: true, zIndexOffset: 2000 }).bindPopup(popupText);

            window.cityPolylines[id] = polylineGroup;
            window.cityPolylineMarkers[id] = { start: startMarker };
            window.cityPolylineMeta[id] = { latlngs: latlngs, segments: segments, reverseLayers: reverseLayers };
            window.cityPolylineVisibility[id] = false;

            // Remettre les polylines dynamiques au premier plan après chargement de la polyline de ville
            bringDynamicPolylinesToFront();

            showOnlyPolyline(id);
        } catch(e) { console.error('[TruckMap] receivePolylineData error:', e); }
    };

    window.showOnlyPolyline = function(polylineId) {
        try {
            if (!polylineId) return;
            polylineId = String(polylineId);
            if (window.cityPolylines[polylineId]) {
                var pl = window.cityPolylines[polylineId];
                var isVisible = window.cityPolylineVisibility[polylineId] || false;
                if (isVisible) { hidePolylineById(polylineId); return; }
                hideAllCityPolylines(polylineId);
                try { pl.addTo(map); } catch(e){}
                // Ne pas utiliser bringToBack() - compter sur zIndexOffset pour gérer l'ordre des couches
                // Les polylines de ville ont zIndexOffset: 100, les polylines dynamiques ont zIndexOffset: 10000
                // Remettre les polylines dynamiques au premier plan après avoir affiché la polyline de ville
                bringDynamicPolylinesToFront();
                window.activePolylineId = polylineId;
                var markers = window.cityPolylinesMarkers[polylineId];
                try { if (markers && markers.start) markers.start.addTo(map); } catch(e){}
                var legend = document.getElementById('polylineLegend');
                if (legend) legend.style.display = 'block';
                window.cityPolylineVisibility[polylineId] = true;
                updateActivePolylineGuidance(lastTruckLatLng || null);
                activateButtonExclusive(polylineId);
                try { map.fitBounds(pl.getBounds(), { padding: [50,50], maxZoom: 15 }); } catch(e){}
            } else {
                hideAllCityPolylines(null);
                sendToHost({ type: 'requestPolyline', id: polylineId });
            }
        } catch(e) { console.error('[TruckMap] showOnlyPolyline error:', e); }
    };

    window.TruckMap.setNavigationPolyline = function(pointsArray) {
        try {
            if (window.navigationPolyline) { map.removeLayer(window.navigationPolyline); window.navigationPolyline = null; }
            if (!Array.isArray(pointsArray) || pointsArray.length < 2) return;
            window.navigationPolyline = L.polyline(pointsArray, { color:'red', weight:5, opacity:0.7, dashArray:'10, 5' }).addTo(map);
            try { map.fitBounds(window.navigationPolyline.getBounds(), { padding: [50,50] }); } catch(e){}
        } catch(e) { sendToHost({ type:'jsError', message:'setNavigationPolyline: ' + String(e) }); }
    };
    window.TruckMap.clearNavigationPolyline = function() {
        try { if (window.navigationPolyline) { map.removeLayer(window.navigationPolyline); window.navigationPolyline = null; } } catch(e){}
    };

    // ═══════════════════════════════════════════════════════════════
    // TAGS
    // ═══════════════════════════════════════════════════════════════
    function escapeHtml(v) {
        if (v == null) return "-";
        return String(v).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;").replace(/'/g,"&#39;");
    }
    function statusColor(st) {
        switch((st||"").toUpperCase()) {
            case "DONE": return "#2e7d32"; case "ACK": return "#1976d2";
            case "REJECTED": return "#c62828"; default: return "#ef6c00";
        }
    }
    function statusLabel(st) {
        switch((st||"").toUpperCase()) {
            case "DONE": return "Terminé"; case "ACK": return "Accusé";
            case "REJECTED": return "Rejeté"; default: return "En attente";
        }
    }
    function makeTagIcon(c) {
        return L.divIcon({
            className: "tag-marker",
            html: '<div style="width:18px;height:18px;border-radius:50%;background:'+c+';border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.35)"></div>',
            iconSize: [18,18], iconAnchor: [9,9]
        });
    }

    var tagCluster = L.markerClusterGroup({ maxClusterRadius:60, disableClusteringAtZoom:18, showCoverageOnHover:false }).addTo(map);
    var tagMarkersById = {};
    var TAG_REASONS = [];
    var tagReasonsLoaded = false;
    var TAG_CHAUFFEURS = [];
    var tagChauffeursLoaded = false;
    var tagScope = 'ALL_OPEN';

    window.TruckMap.setAdmin = function(v) { isAdmin = !!v; };
    window.TruckMap.setTagsScope = async function(scope) {
        try { var s = String(scope||'').toUpperCase(); if (['ALL_OPEN','CITY_OPEN','CITY_ALL'].includes(s)) { tagScope = s; await loadTags(); } } catch(e){}
    };

    async function loadTagReasons() {
        try { var r = await fetch(TAG_ENDPOINTS.reasons+"?_ts="+Date.now()); var j = await r.json(); if (j&&j.ok&&Array.isArray(j.data)) { TAG_REASONS = j.data.slice(); tagReasonsLoaded = true; } } catch(e){}
    }
    async function loadTagChauffeurs() {
        if (tagChauffeursLoaded) return;
        try {
            var resp = await fetch(TAG_ENDPOINTS.chauffeurs+"?_ts="+Date.now()); var j = await resp.json();
            if (j&&j.ok&&Array.isArray(j.data)) {
                TAG_CHAUFFEURS = j.data.map(function(r) {
                    var id = r.id||r.ID||r.id_chauffeur||r.idchauffeur||r.value||r.Id||null;
                    var name = r.name||r.Nom||r.nom||r.login||r.fullname||(r.Prenom?(r.Prenom+' '+(r.Nom||'')):'')||String(id||'');
                    return { id: id!=null?String(id):'', name: String(name||'').trim() };
                }).filter(function(c){return c.id;});
                tagChauffeursLoaded = TAG_CHAUFFEURS.length > 0;
            }
        } catch(e){}
    }
    function buildChauffeurOptionsHtml(selectedId) {
        var selId = selectedId==null?'':String(selectedId);
        var html = '<option value="">— (aucun) —</option>';
        TAG_CHAUFFEURS.forEach(function(ch) {
            var idStr = String(ch.id||'');
            var sel = selId && idStr === selId ? ' selected' : '';
            html += '<option'+sel+' value="'+escapeHtml(idStr)+'">'+escapeHtml(ch.name||idStr)+'</option>';
        });
        return html;
    }
    async function ensureChauffeurSelectOptions(selects) {
        var list = Array.isArray(selects)?selects.filter(Boolean):Array.from(selects||[]).filter(Boolean);
        if (!list.length) return;
        if (!tagChauffeursLoaded) await loadTagChauffeurs();
        list.forEach(function(sel) {
            var selected = sel.getAttribute('data-selected')||sel.value||'';
            sel.innerHTML = tagChauffeursLoaded?buildChauffeurOptionsHtml(selected):'<option value="">— (aucun) —</option>';
            if (selected) sel.value = selected;
        });
    }
    function extractResponses(tag) {
        var list = [];
        var add = function(o) { if (!o) return; list.push({ text:o.text||o.message||o.comment||"", author:o.author||o.user||o.by||"", time:o.time||o.date||o.at||"", status:o.status||"" }); };
        try { if (Array.isArray(tag&&tag.responses)) tag.responses.forEach(add);
        else if (Array.isArray(tag&&tag.response_list)) tag.response_list.forEach(add);
        else if (Array.isArray(tag&&tag.comments)) tag.comments.forEach(add);
        else if (Array.isArray(tag&&tag.discussions)) tag.discussions.forEach(add);
        else if (tag&&tag.last_response) add(tag.last_response); } catch(e){}
        return list;
    }

    function tagPopupHtml(tag) {
        var bg = statusColor(tag.status);
        var chauffeurId = tag.id_chauffeur||tag.idChauffeur||tag.driver_id||"";
        var chauffeurText = tag.chauffeur||tag.driver||tag.driver_name||tag.author_name||"";
        var tagVille = tag.id_ville!=null?tag.id_ville:(idville||0);
        var titleValue = tag.title||tag.tag_code||"";
        var imgHtml = "";
        if (tag&&tag.image_path) {
            var url = String(tag.image_path).startsWith("http")?tag.image_path:(API_BASE.replace(/\/+$/,"")+"/"+String(tag.image_path).replace(/^\/+/,""));
            imgHtml = '<div class="img-preview"><img data-full="'+escapeHtml(url)+'" src="'+escapeHtml(url)+'"/></div>';
        }
        var responses = extractResponses(tag);
        var discussionHtml = '<div style="margin-top:6px;"><div style="font-weight:600;margin-bottom:4px;">Discussions / réponses</div>'
            + (responses.length>0?responses.map(function(r) { return '<div style="border-left:3px solid #ddd;padding-left:8px;margin-bottom:6px;font-size:12px;">'+(r.text?'<div>'+escapeHtml(r.text)+'</div>':'')+'<div style="color:#777;">'+[escapeHtml(r.author),escapeHtml(r.time),statusLabel(r.status)].filter(function(x){return x&&x!=="-";}).join(' · ')+'</div></div>'; }).join(''):'<div style="font-size:12px;color:#777;">Aucune réponse.</div>')+'</div>';
        var buttonsHtml = '<div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">'
            +'<button data-action="EDIT" data-id="'+tag.id+'" class="btn btn-primary btn-sm" style="flex:1;">Modifier</button>'
            +'<button data-action="ACK" data-id="'+tag.id+'" class="btn btn-info btn-sm" style="flex:1;">Accuser</button>'
            +'<button data-action="DONE" data-id="'+tag.id+'" class="btn btn-success btn-sm" style="flex:1;">Terminer</button>'
            +'<button data-action="ROUTE" data-id="'+tag.id+'" data-lat="'+escapeHtml(String(tag.latitude||tag.lat||""))+'" data-lng="'+escapeHtml(String(tag.longitude||tag.lng||""))+'" class="btn btn-outline-primary btn-sm" style="flex:1;">Itinéraire</button>'
            +'<button data-action="REJECTED" data-id="'+tag.id+'" class="btn btn-danger btn-sm" style="flex:1;">Rejeter</button></div>';
        var assignSection = '<div style="margin-top:10px;border-top:1px solid #eee;padding-top:10px;"><div style="font-weight:600;margin-bottom:6px;">Assigner chauffeur</div><input type="hidden" name="popup_id_ville" value="'+escapeHtml(String(tagVille||""))+'"/><div style="display:flex;flex-direction:column;gap:6px;"><select name="popup_id_chauffeur" class="form-control input-sm" data-selected="'+escapeHtml(String(chauffeurId||""))+'">'+buildChauffeurOptionsHtml(chauffeurId)+'</select><input type="text" name="popup_chauffeur_text" class="form-control input-sm" placeholder="Nom libre" value="'+escapeHtml(chauffeurText||"")+'"/><div style="display:flex;justify-content:flex-end;"><button data-action="UPDATE_CHAUFFEUR" data-id="'+escapeHtml(String(tag.id))+'" class="btn btn-warning btn-sm">Mettre à jour</button></div></div></div>';
        var reasonsOptions = (tagReasonsLoaded?TAG_REASONS:["—"]).map(function(r) { var sel = String(r||"")===String(tag.reason||"")?' selected':''; return '<option'+sel+' value="'+escapeHtml(r)+'">'+escapeHtml(r)+'</option>'; }).join('');
        var replySection = '<div class="reply-zone" style="margin-top:10px;border-top:1px solid #eee;padding-top:8px;"><div style="font-weight:600;margin-bottom:6px;">Ajouter une réponse</div><textarea name="reply_text" rows="3" style="width:100%;" placeholder="Votre réponse..."></textarea><div style="display:flex;gap:6px;justify-content:flex-end;margin-top:6px;"><button data-action="POST_REPLY" data-id="'+escapeHtml(String(tag.id))+'" class="btn btn-success btn-sm">Envoyer</button></div></div>';

        return '<div class="popup-wrap" style="min-width:360px;max-width:560px;">'
            +'<div style="background:'+bg+';color:#fff;padding:8px 10px;border-radius:6px 6px 0 0;font-weight:600;margin-bottom:8px;">'+escapeHtml(tag.address_text||tag.address||"")+'</div>'
            +'<div style="padding:6px;">'
            +'<div><b>Statut:</b> <span style="color:'+statusColor(tag.status)+'">'+escapeHtml(statusLabel(tag.status))+'</span></div>'
            +'<div style="margin-bottom:6px;"><b>Titre:</b><br/><input type="text" name="title" value="'+escapeHtml(titleValue)+'" class="form-control input-sm"/></div>'
            +(tag.address_text?'<div><b>Adresse:</b> '+escapeHtml(tag.address_text)+'</div>':'')
            +(tag.reason?'<div><b>Raison:</b> '+escapeHtml(tag.reason)+'</div>':'')
            +(tag.description?'<div><b>Description:</b> '+escapeHtml(tag.description)+'</div>':'')
            +(chauffeurText?'<div><b>Chauffeur:</b> '+escapeHtml(chauffeurText)+'</div>':'')
            +assignSection+'</div>'+imgHtml+discussionHtml+buttonsHtml
            +'<div class="edit-zone" data-id="'+tag.id+'" style="display:none;margin-top:8px;border-top:1px solid #eee;padding-top:8px;"><div style="font-weight:600;margin-bottom:6px;">Modifier le tag</div><div style="margin-bottom:6px;"><b>Titre:</b><br/><input type="text" name="title" value="'+escapeHtml(titleValue)+'" style="width:100%"/></div><div style="margin-bottom:6px;"><b>Adresse:</b><br/><input type="text" name="address_text" value="'+escapeHtml(tag.address_text||"")+'" style="width:100%"/></div><div style="margin-bottom:6px;"><b>Raison:</b><br/><select name="reason" style="width:100%">'+reasonsOptions+'</select></div><div style="margin-bottom:6px;"><b>Chauffeur:</b><br/><select name="id_chauffeur" class="form-control input-sm" data-selected="'+escapeHtml(String(chauffeurId||""))+'">'+buildChauffeurOptionsHtml(chauffeurId)+'</select></div><div style="margin-bottom:6px;"><b>Description:</b><br/><textarea name="description" rows="3" style="width:100%">'+escapeHtml(tag.description||"")+'</textarea></div><div style="display:flex;gap:6px;justify-content:flex-end;"><button data-action="SAVE_EDIT" data-id="'+tag.id+'" class="btn btn-primary btn-sm">Enregistrer</button><button data-action="CANCEL_EDIT" data-id="'+tag.id+'" class="btn btn-default btn-sm">Annuler</button></div></div>'
            +replySection+'</div>';
    }

    function wirePopupButtons(root) {
        try { var selects = root.querySelectorAll('select[name="popup_id_chauffeur"],select[name="id_chauffeur"]'); if (selects&&selects.length) ensureChauffeurSelectOptions(selects).catch(function(){}); } catch(e){}
        root.querySelectorAll("button[data-action]").forEach(function(btn) {
            btn.onclick = async function() {
                var action = btn.getAttribute("data-action"), id = btn.getAttribute("data-id");
                if (!action||!id) return;
                if (action==="UPDATE_CHAUFFEUR") {
                    var sel=root.querySelector('select[name="popup_id_chauffeur"]'), txt=root.querySelector('input[name="popup_chauffeur_text"]'), hv=root.querySelector('input[name="popup_id_ville"]');
                    await updateTagChauffeur(id,sel?sel.value:'',txt?txt.value.trim():'',hv?hv.value:'',btn);
                    try{map.closePopup();}catch(e){} return;
                }
                if (action==="REJECTED") { var t=prompt("Raison du rejet:","")||""; await respondTag(id,"REJECTED",t); return; }
                if (action==="ACK"||action==="DONE") { await respondTag(id,action==="ACK"?"ACK":"DONE",""); return; }
                if (action==="ROUTE") { showRouteToTag(parseFloat(btn.getAttribute("data-lat")||"NaN"),parseFloat(btn.getAttribute("data-lng")||"NaN")); return; }
                if (action==="EDIT") { var z=root.querySelector('.edit-zone[data-id="'+id+'"]'); if(z)z.style.display="block"; return; }
                if (action==="CANCEL_EDIT") { var z=root.querySelector('.edit-zone[data-id="'+id+'"]'); if(z)z.style.display="none"; return; }
                if (action==="SAVE_EDIT") {
                    var zone=root.querySelector('.edit-zone[data-id="'+id+'"]'); if(!zone) return;
                    var address=zone.querySelector('input[name="address_text"]'); var reason=zone.querySelector('select[name="reason"]'); var descr=zone.querySelector('textarea[name="description"]'); var title=zone.querySelector('input[name="title"]'); var selCh=zone.querySelector('select[name="id_chauffeur"]');
                    await updateTag(id,address?address.value:'',reason?reason.value:'',descr?descr.value:'',selCh?selCh.value:'','',title?title.value:'');
                    map.closePopup(); return;
                }
                if (action==="POST_REPLY") {
                    var rz=root.querySelector('.reply-zone'), txt=rz?rz.querySelector('textarea[name="reply_text"]'):null;
                    if(!txt||!txt.value.trim()){alert("Veuillez saisir une réponse.");return;}
                    await respondTag(id,"ACK",txt.value.trim());
                    try{txt.value="";}catch(e){} map.closePopup(); return;
                }
            };
        });
        root.querySelectorAll(".img-preview img[data-full]").forEach(function(img) {
            img.onclick = function() {
                document.getElementById("lbImg").src = img.getAttribute("data-full");
                document.getElementById("imgLightbox").style.display = "flex";
            };
        });
    }

    async function updateTag(id,address_text,reason,description,idChauffeur,chauffeurText,title) {
        try {
            var fd = new FormData(); fd.append("id",id); fd.append("id_ville",String(idville||0));
            if(address_text!==undefined) fd.append("address_text",address_text||"");
            if(reason!==undefined) fd.append("reason",reason||"");
            if(description!==undefined) fd.append("description",description||"");
            if(title!==undefined) fd.append("title",title||"");
            if(idChauffeur) fd.append("id_chauffeur",String(idChauffeur));
            var r = await fetch(TAG_ENDPOINTS.update,{method:"POST",body:fd}); var j = await r.json();
            if(!j||!j.ok){alert(j&&j.error||"Échec mise à jour"); return;}
            await loadTags(); if(idville&&idchauffeur) await loadChauffeurTags();
        } catch(e){ alert("Erreur: "+(e&&e.message||e)); }
    }
    async function updateTagChauffeur(id,idChauffeur,chauffeurText,idVilleOverride,button) {
        var btnState=null; if(button){btnState={btn:button,label:button.textContent};button.disabled=true;button.textContent='Envoi...';}
        try {
            var fd=new FormData(); fd.append('id',String(id)); fd.append('id_ville',String(idVilleOverride||idville||0));
            if(idChauffeur)fd.append('id_chauffeur',String(idChauffeur)); else if(chauffeurText)fd.append('chauffeur_text',chauffeurText);
            var resp=await fetch(TAG_ENDPOINTS.update,{method:'POST',body:fd}); var json=null; try{json=await resp.json();}catch(e){} if(!resp.ok||!json||!json.ok){alert(json&&json.error||'Erreur update');return;}
            await loadTags(); if(idville&&idchauffeur) await loadChauffeurTags();
        } catch(e){alert('Erreur: '+(e&&e.message||e));} finally {if(btnState){btnState.btn.disabled=false;btnState.btn.textContent=btnState.label;}}
    }
    async function respondTag(id,status,responseText) {
        try {
            var fd=new FormData(); fd.append("id",id); fd.append("id_ville",String(idville||0)); fd.append("status",status);
            if(responseText) fd.append("response_text",responseText);
            var r=await fetch(TAG_ENDPOINTS.respond,{method:"POST",body:fd}); var j=await r.json();
            if(!j||!j.ok){alert("Réponse non envoyée");return;}
            await loadTags(); if(idville&&idchauffeur) await loadChauffeurTags();
            sendToHost({type:"tagResponded",id:id,status:status});
        } catch(e){alert("Erreur: "+(e&&e.message||e));}
    }

    function parseCoord(v) { if(v==null) return NaN; if(typeof v==="string") v=v.replace(",","."); return parseFloat(v); }

    async function loadTags() {
        try {
            var url = "";
            if (tagScope==="ALL_OPEN") url = TAG_ENDPOINTS.listAllOpen+"?_ts="+Date.now();
            else if (idville) { var onlyOpen=(tagScope==="CITY_OPEN")?"&open=1":""; url=TAG_ENDPOINTS.list+"?idville="+encodeURIComponent(idville)+onlyOpen+"&_ts="+Date.now(); }
            else url = TAG_ENDPOINTS.listAllOpen+"?_ts="+Date.now();

            var resp = await fetch(url,{cache:"no-store"}); if(!resp.ok) return;
            var j = await resp.json();
            var list = [];
            if(Array.isArray(j)) list=j; else if(Array.isArray(j&&j.data)) list=j.data; else if(j&&j.data&&Array.isArray(j.data.data)) list=j.data.data;
            else { for(var k in j){if(Array.isArray(j[k])){list=j[k];break;}} }

            try{tagCluster.clearLayers();}catch(e){} tagMarkersById={};
            for(var t of list) {
                try {
                    var st=(t.status||t.Status||t.state||"").toString().toUpperCase();
                    if(["DONE","TERMINE","TERMINÉ","CLOSED","CLOSE"].indexOf(st)!==-1) continue;
                    var id=String(t.id||t.Id||t.ID||Math.random());
                    var lat=parseCoord(t.latitude||t.lat||t.lat_gps);
                    var lng=parseCoord(t.longitude||t.lng||t.lon||t.long);
                    if(isNaN(lat)||isNaN(lng)) continue;
                    var tagObj={id:id,latitude:lat,longitude:lng,status:t.status||t.Status||"",address_text:t.address_text||t.address||"",title:t.title||t.Title||t.tag_code||"",description:t.description||t.Description||"",image_path:t.image_path||t.ImagePath||null,reason:t.reason||t.Reason||null,id_chauffeur:t.id_chauffeur||t.idChauffeur||t.driver_id||null,chauffeur:t.chauffeur||t.driver||t.driver_name||"",id_ville:t.id_ville||t.idVille||idville||0,responses:t.responses||null,response_list:t.response_list||null,comments:t.comments||null,discussions:t.discussions||null,last_response:t.last_response||null};
                    var marker=L.marker([lat,lng],{icon:makeTagIcon(statusColor(tagObj.status)),title:tagObj.title||"Tag"});
                    marker.bindPopup(tagPopupHtml(tagObj));
                    marker.on("popupopen",function(e){try{wirePopupButtons(e.popup.getElement());}catch(e){}});
                    try{tagCluster.addLayer(marker);tagMarkersById[id]=marker;}catch(e){try{map.addLayer(marker);tagMarkersById[id]=marker;}catch(ex){}}
                } catch(inner){}
            }
            try{if(!map.hasLayer(tagCluster))map.addLayer(tagCluster);}catch(e){}
            try{if(idville&&idchauffeur) await loadChauffeurTags();}catch(e){}
        } catch(e) { console.warn("loadTags",e); }
    }

    async function geocodeAddress(q) {
        var encoded = encodeURIComponent(q);
        var providers = [
            {name:'nominatim',url:'https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=ca&email=truckmap@robert-daoust.ca&q='+encoded,mapData:function(data){return Array.isArray(data)&&data.length?{lat:parseFloat(data[0].lat),lng:parseFloat(data[0].lon),name:data[0].display_name||q}:null;}},
            {name:'photon',url:'https://photon.komoot.io/api/?lang=fr&limit=1&q='+encoded,mapData:function(data){return Array.isArray(data&&data.features)&&data.features.length?{lat:parseFloat(data.features[0].geometry&&data.features[0].geometry.coordinates&&data.features[0].geometry.coordinates[1]),lng:parseFloat(data.features[0].geometry&&data.features[0].geometry.coordinates&&data.features[0].geometry.coordinates[0]),name:(data.features[0].properties&&data.features[0].properties.label)||q}:null;}}
        ];
        var lastError = new Error('Adresse introuvable');
        for (var i=0;i<providers.length;i++) {
            var provider=providers[i];
            try { var resp=await fetch(provider.url,{headers:{'Accept':'application/json'}}); if(!resp.ok) throw new Error(provider.name+' HTTP '+resp.status); var json=await resp.json(); var result=provider.mapData(json); if(result&&!isNaN(result.lat)&&!isNaN(result.lng)) return result; lastError=new Error('Adresse introuvable via '+provider.name); } catch(err){lastError=err instanceof Error?err:new Error(String(err));}
        }
        throw lastError;
    }

    function openCreateTagPopupWithUpload(lat, lng, address) {
        var formId = "frmTag_" + Date.now();
        var opts = (tagReasonsLoaded?TAG_REASONS:["—"]).map(function(r){return '<option value="'+escapeHtml(r)+'">'+escapeHtml(r)+'</option>';}).join('');
        var html = '<div class="popup-wrap" style="min-width:360px;max-width:600px;"><div style="font-weight:600;margin-bottom:6px;">Créer un tag</div><form id="'+formId+'" enctype="multipart/form-data" onsubmit="return false;"><div style="margin-bottom:6px;"><b>Adresse:</b><br/><input name="address_text" type="text" value="'+escapeHtml(address||"")+'" class="form-control input-sm"/></div><div style="margin-bottom:6px;"><b>Raison:</b><br/><select name="reason" class="form-control input-sm">'+opts+'</select></div><div style="margin-bottom:6px;"><b>Chauffeur:</b><br/><select name="id_chauffeur" class="form-control input-sm" data-selected="">'+buildChauffeurOptionsHtml('')+'</select></div><div style="margin-bottom:6px;"><b>Description:</b><br/><textarea name="description" rows="3" class="form-control input-sm"></textarea></div><div style="margin-bottom:6px;"><b>Image:</b><br/><input type="file" name="image" accept="image/*" class="form-control input-sm"/></div><div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" id="btnSendTag" class="btn btn-primary btn-sm">Envoyer</button></div></form></div>';
        var pop = L.popup({maxWidth:640}).setLatLng([lat,lng]).setContent(html).openOn(map);
        setTimeout(async function(){
            try{var fe=document.getElementById(formId);if(fe){var selects=fe.querySelectorAll('select[name="id_chauffeur"]');if(selects&&selects.length) await ensureChauffeurSelectOptions(selects);}}catch(e){}
            var btn=document.getElementById("btnSendTag"); if(!btn) return;
            btn.onclick = async function(){
                if(btn.dataset.submitting==='1')return; btn.dataset.submitting='1'; btn.disabled=true; btn.textContent='Envoi...';
                try {
                    var fe=document.getElementById(formId); if(!fe)return;
                    var fd=new FormData(fe); fd.append("id_ville",String(idville||0)); fd.append("lat",String(lat)); fd.append("lng",String(lng)); fd.append("status",isAdmin?"PENDING":"VALIDER"); fd.append("isadmin",isAdmin?"1":"0"); fd.append("id_chauffeur",String(idchauffeur||0));
                    var resp=await fetch(TAG_ENDPOINTS.create,{method:"POST",body:fd,cache:"no-store"}); var text=await resp.text(); var json=null; try{json=text?JSON.parse(text):null;}catch(e){}
                    if(!resp.ok){alert("Erreur HTTP: "+resp.status); btn.disabled=false;btn.textContent='Envoyer';btn.dataset.submitting='0';return;}
                    if(!json||!json.ok){alert("Erreur: "+(json&&json.error||text||"Réponse invalide")); btn.disabled=false;btn.textContent='Envoyer';btn.dataset.submitting='0';return;}
                    map.closePopup(pop); await loadTags();
                } catch(err){alert("Erreur envoi: "+(err&&err.message||String(err))); btn.disabled=false;btn.textContent='Envoyer';btn.dataset.submitting='0';}
            };
        },40);
    }

    // ─── Lightbox ───
    var lb = document.getElementById("imgLightbox");
    var lbClose = document.getElementById("lbClose");
    if(lbClose) lbClose.onclick = function(){ lb.style.display="none"; };
    if(lb) lb.onclick = function(e){ if(e.target===lb) lb.style.display="none"; };

    // ─── Panneau tags chauffeur ───
    async function loadChauffeurTags() {
        try {
            var panel=document.getElementById("chauffeurTagsPanel"), host=document.getElementById("chauffeurTagsList");
            if(!idville||!idchauffeur){if(panel)panel.style.display="none";if(host)host.innerHTML="";return;}
            var url=TAG_ENDPOINTS.tagsByDriver+"?idville="+encodeURIComponent(idville)+"&idchauffeur="+encodeURIComponent(idchauffeur);
            var resp=await fetch(url,{cache:"no-store"}); if(!resp.ok){if(panel)panel.style.display="none";return;}
            var items=await resp.json(); if(!Array.isArray(items)) items=[];
            if(items.length===0){if(panel)panel.style.display="none";if(host)host.innerHTML="";return;}
            if(panel)panel.style.display="block"; if(host)host.innerHTML="";
            var colors={PENDING:"#ef6c00",ACK:"#1976d2",DONE:"#2e7d32",REJECTED:"#c62828"};
            items.forEach(function(t){
                var div=document.createElement("div"); div.className="tag-item";
                var stat=(t.Status||t.status||"").toUpperCase(); var col=colors[stat]||"#9e9e9e";
                div.style.borderLeftColor=col; div.style.background=hexToRgba(col,0.06);
                div.innerHTML='<div><b>'+escapeHtml(t.Title||t.title||t.tag_code||"Tag")+'</b></div><div style="font-size:12px;margin-top:4px;">'+escapeHtml(t.Description||t.description||t.address_text||"")+'</div>';
                var actions=document.createElement("div"); actions.className="tag-actions";
                var bAck=document.createElement("button"); bAck.textContent="Accuser"; bAck.onclick=async function(){await respondTag(t.Id||t.id,"ACK","");}; actions.appendChild(bAck);
                if(isAdmin){var bDone=document.createElement("button"); bDone.textContent="Terminer"; bDone.onclick=async function(){await respondTag(t.Id||t.id,"DONE","");}; actions.appendChild(bDone);}
                var bRej=document.createElement("button"); bRej.textContent="Rejeter"; bRej.onclick=async function(){var txt=prompt("Raison du rejet:","")||""; await respondTag(t.Id||t.id,"REJECTED",txt);}; actions.appendChild(bRej);
                div.appendChild(actions); if(host) host.appendChild(div);
            });
        } catch(e){}
    }

    // ─── API host ───
    window.TruckMap.setCityContext = async function(ville,secteur) {
        idville = parseInt(ville||0,10)||0;
        await loadTags(); if(idchauffeur) await loadChauffeurTags();
    };
    window.TruckMap.setChauffeurContext = async function(ch) {
        idchauffeur = parseInt(ch||0,10)||0;
        await loadChauffeurTags();
    };
    window.TruckMap.reloadTags = async function(){ await loadTags(); };
    window.TruckMap.openCreateTagPopup = function(lat,lng,address) {
        var la=parseFloat(lat),lo=parseFloat(lng); if(isNaN(la)||isNaN(lo)){alert("Coordonnées invalides");return;}
        openCreateTagPopupWithUpload(la,lo,address||"");
    };
    window.TruckMap.ensureFirstCenter = function(lat,lng) {
        try { var z=map.getZoom(); if(!z||z<12) z=17; map.setView([lat,lng],z); } catch(e){}
    };

    // ─── Boutons Start / Stop / StopRoute ───
    (function wireTraceButtons() {
        try {
            var startBtn = document.getElementById('manualBtn');
            var endBtn = document.getElementById('endManualBtn');
            var endManualInProgress = false;

            if (startBtn) {
                startBtn.addEventListener('click', function() {
                    // NE PAS effacer l'ancien parcours : on le conserve pour permettre
                    // la « reprise » (< GAP_THRESHOLD_METERS) ou la création d'un nouveau
                    // segment indépendant (> 30m, p. ex. retour de dépotoir) selon la
                    // distance au dernier point. La logique est gérée dans updateTruck().
                    sendToHost({ type:'zone', inZone:true, source:'manualBtn' });
                    if (endBtn) endBtn.disabled = false;
                    startBtn.disabled = true;
                }, { passive:true });
            }
            if (endBtn) {
                endBtn.addEventListener('click', function() {
                    if (endManualInProgress) return;
                    endManualInProgress = true;
                    endBtn.disabled = true;
                    if (startBtn) startBtn.disabled = true;
                    sendToHost({ type:'zone', inZone:false, source:'endManualBtn' });
                    // Arrêter le polling Samsara pour éviter que le traçage continue
                    _stopSamsaraPolling();
                    // NE PAS sauvegarder l'état comme actif - on vient d'arrêter le tracking
                    try { localStorage.removeItem('truckmap_tracking_was_active'); } catch(e){}
                    setTimeout(function() { sendToHost({ type:'ui', action:'showVilles' }); }, 900);
                    setTimeout(function() { endManualInProgress = false; if(startBtn) startBtn.disabled = false; }, 2000);
                }, { passive:true });
            }
            if (stopRouteBtn) {
                stopRouteBtn.addEventListener('click', function() { clearRoutePolyline(); }, { passive:true });
            }
        } catch(e) { sendToHost({ type:'jsError', message:String(e) }); }
    })();

    // ─── Init ───
    (async function init() {
        try { await loadTagReasons(); } catch(e){}
        try { await loadTagChauffeurs(); } catch(e){}
        try { await window.TruckMap.setTagsScope('ALL_OPEN'); } catch(e){}
        sendToHost({ type: "mapReady" });
    })();

    // ═══ Mobile Tools Dropdown ═══
    (function initMobileTools(){
        var fab      = document.getElementById('mobileToolsFab');
        var panel    = document.getElementById('mobileToolsPanel');
        var closeBtn = document.getElementById('mobileToolsClose');
        var backdrop = document.getElementById('mobileToolsBackdrop');
        if (!fab || !panel) return;

        function openPanel(){
            panel.style.display = 'block';
            panel.offsetHeight; // reflow
            panel.classList.add('open');
            fab.classList.add('open');
            if (backdrop) backdrop.classList.add('open');
        }
        function closePanel(){
            panel.classList.remove('open');
            fab.classList.remove('open');
            if (backdrop) backdrop.classList.remove('open');
            setTimeout(function(){ if(!panel.classList.contains('open')) panel.style.display='none'; }, 220);
        }
        function togglePanel(){
            panel.classList.contains('open') ? closePanel() : openPanel();
        }
        fab.addEventListener('click', function(e){ e.stopPropagation(); togglePanel(); });
        if (closeBtn) closeBtn.addEventListener('click', function(){ closePanel(); });
        if (backdrop) backdrop.addEventListener('click', function(){ closePanel(); });

        // ─── Sync mobile trace buttons with desktop ones ───
        var startD = document.getElementById('manualBtn');
        var stopD  = document.getElementById('endManualBtn');
        var stopRD = document.getElementById('stopRouteBtn');
        var startM = document.getElementById('manualBtnMob');
        var stopM  = document.getElementById('endManualBtnMob');
        var stopRM = document.getElementById('stopRouteBtnMob');

        if (startM && startD) startM.addEventListener('click', function(){ startD.click(); syncMobTraceBtns(); });
        if (stopM && stopD)   stopM.addEventListener('click', function(){ stopD.click(); syncMobTraceBtns(); });
        if (stopRM && stopRD) stopRM.addEventListener('click', function(){ stopRD.click(); });

        function syncMobTraceBtns(){
            setTimeout(function(){
                if(startM && startD) startM.disabled = startD.disabled;
                if(stopM && stopD)   stopM.disabled  = stopD.disabled;
                if(stopRM && stopRD) stopRM.disabled  = stopRD.disabled;
            }, 100);
        }
        // Observe desktop button disabled attribute changes
        if (window.MutationObserver){
            [startD, stopD, stopRD].forEach(function(el){
                if(!el) return;
                new MutationObserver(function(){ syncMobTraceBtns(); })
                    .observe(el, { attributes:true, attributeFilter:['disabled'] });
            });
        }

        // ─── Sync follow checkboxes ───
        var followD = document.getElementById('chkFollowTruck');
        var followM = document.getElementById('chkFollowTruckMob');
        if (followD && followM){
            followM.addEventListener('change', function(){ followD.checked = followM.checked; followD.dispatchEvent(new Event('change')); });
            followD.addEventListener('change', function(){ followM.checked = followD.checked; });
        }

        // ─── Mirror polyline buttons into mobile panel ───
        window.TruckMap._syncMobilePolyButtons = function(){
            var section = document.getElementById('mobilePolySection');
            var container = document.getElementById('mobilePolyButtons');
            if (!section || !container) return;
            var btns = document.querySelectorAll('#polylineButtons .polyline-toggle-btn');
            var anyVisible = false;
            container.innerHTML = '';
            btns.forEach(function(b){
                if (b.style.display === 'none') return;
                anyVisible = true;
                var color = b.querySelector('.polyline-color-indicator');
                var num   = b.querySelector('.polyline-number');
                var label = b.querySelector('[style*="flex:1"]');
                var pid   = b.getAttribute('data-polyline-id');
                var btn = document.createElement('button');
                btn.className = 'mob-poly-btn';
                btn.innerHTML = '<span class="mob-poly-color" style="background:' + (color ? color.style.background : '#FF0000') + ';"></span>'
                    + '<span>' + (num ? num.textContent : '') + '</span> '
                    + '<span style="flex:1;">' + (label ? label.textContent : 'Parcours') + '</span>';
                btn.addEventListener('click', function(){ showOnlyPolyline(pid); });
                container.appendChild(btn);
            });
            section.style.display = anyVisible ? '' : 'none';
        };
        // Hook into setPolylineButtons to auto-sync
        var origSetPoly = window.TruckMap.setPolylineButtons;
        if (origSetPoly){
            window.TruckMap.setPolylineButtons = function(){
                origSetPoly.apply(this, arguments);
                setTimeout(function(){ if(window.TruckMap._syncMobilePolyButtons) window.TruckMap._syncMobilePolyButtons(); }, 200);
            };
        }
    })();

})();
</script>
<?php
$foot_extra = ob_get_clean();

// ═══════════════════════════════════════════════════════════════
// Chargement du layout (seulement si pas en mode embed)
// ═══════════════════════════════════════════════════════════════
$embed = isset($embed) && $embed === true;
if (empty($embed)) {
    echo view('web_chauffeur/layout', array(
        'content'    => $content,
        'page_id'    => $page_id,
        'page_title' => $page_title,
        'head_extra' => $head_extra,
        'foot_extra' => $foot_extra,
    ));
} else {
    // Mode embed : afficher directement sans layout
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo '<title>' . htmlspecialchars($page_title) . '</title>';
    echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
    echo '<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css">';
    echo '<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">';
    echo '<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
    echo '<link rel="stylesheet" href="/assets/css/web_chauffeur.css">';
    echo ($head_extra ?? ''); // Inclure le CSS personnalisé
    echo '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>';
    echo '<style>html,body{height:100%;margin:0;padding:0;}#map{position:absolute;inset:0;height:100%;width:100%;}</style>';
    echo '</head><body style="overflow:hidden;">';
    echo ($content ?? '');
    echo ($foot_extra ?? '');
    echo '</body></html>';
}
?>
