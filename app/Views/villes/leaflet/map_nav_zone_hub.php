<?php

$ActiveDate = isset($ActiveDate) ? $ActiveDate : date('Y-m-d');
$Lang = isset($Lang) ? $Lang : 'fr';
$Cities = isset($Cities) && is_array($Cities) ? $Cities : [];
// ParamÃ¨tres passÃ©s par l'app C# (admin) via l'URL du hub
$NoCamion   = isset($NoCamion)   ? $NoCamion   : (isset($_GET['nocamion'])   ? $_GET['nocamion']   : '');
$IdChauffeur = isset($IdChauffeur) ? $IdChauffeur : (isset($_GET['idchauffeur']) ? $_GET['idchauffeur'] : '');
$IdVilleAdmin = isset($IdVilleAdmin) ? $IdVilleAdmin : (isset($_GET['idville']) ? (int)$_GET['idville'] : 0);
// Sur tablette si un camion ou un chauffeur est spÃ©cifiÃ© (app C#)
$IsTablette = (!empty($NoCamion) || !empty($IdChauffeur));
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Localisation â€“ Villes (Bureau)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      overflow: hidden;
      font-family: Arial, sans-serif;
    }

    /* Sidebar transformÃ©e en dropdown : cachÃ©e par dÃ©faut, visible sur toggle */
    .sidebar {
      position: fixed;
      z-index: 1050;
      top: 60px;
      right: 12px;
      width: 260px;
      max-height: calc(100vh - 72px);
      overflow-y: auto;
      background: rgba(255, 255, 255, .96);
      border: 1px solid #bbb;
      border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
      display: none;
      flex-direction: column;
      padding: 12px;
    }

    .sidebar.sidebar-open {
      display: flex !important;
    }

    .sidebar h3 {
      margin: 0 0 10px 0;
      font-size: 16px;
    }

    .form-row {
      margin: 6px 0;
    }

    .form-row label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .form-row input[type="date"],
    .form-row input[type="text"] {
      width: 100%;
      box-sizing: border-box;
    }

    .cities {
      margin-top: 10px;
      overflow-y: auto;
      border-top: 1px solid #ddd;
      padding-top: 10px;
    }

    .city-item {
      display: block;
      width: 100%;
      text-align: left;
      padding: 8px 10px;
      margin-bottom: 6px;
      background: #f7f7f7;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
    }

    .city-item:hover {
      background: #eaf3ff;
      border-color: #9ec5fe;
    }

    .city-item.active {
      background: #dbeafe;
      border-color: #60a5fa;
    }

    /* â•â•â• Filtres camions dans la sidebar â•â•â• */
    .truck-filter-container {
      display: none;
      padding: 6px 0 4px 0;
    }

    .truck-filter-container.visible {
      display: block;
    }

    .truck-filter-container .tf-label {
      font-size: 11px;
      font-weight: 700;
      color: #555;
      margin-bottom: 4px;
      text-transform: uppercase;
    }

    .truck-filter-container .tf-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
    }

    .truck-filter-container .tf-btn {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
      border: 2px solid #ccc;
      border-radius: 4px;
      background: #fff;
      cursor: pointer;
      font-size: 11px;
      font-weight: 600;
      transition: all .15s;
      white-space: nowrap;
    }

    .truck-filter-container .tf-btn:hover {
      background: #f0f4fa;
      border-color: #90caf9;
    }

    .truck-filter-container .tf-btn.active {
      background: #e3f2fd;
      border-color: #1976d2;
      box-shadow: 0 0 0 2px rgba(25, 118, 210, .25);
    }

    .truck-filter-container .tf-btn .tf-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      border: 1px solid rgba(0, 0, 0, .15);
      flex-shrink: 0;
    }

    .truck-filter-container .tf-btn.tf-tous {
      background: #f5f5f5;
      font-weight: 700;
    }

    .truck-filter-container .tf-btn.tf-tous.active {
      background: #c8e6c9;
      border-color: #2e7d32;
    }

    .content {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: #f5f5f5;
    }

    /* â•â•â• Barre d'onglets â•â•â• */
    .tab-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 42px;
      background: #1a3a5c;
      display: flex;
      align-items: stretch;
      z-index: 5100;
      box-shadow: 0 2px 6px rgba(0, 0, 0, .3);
    }

    .tab-btn {
      padding: 0 20px;
      border: none;
      background: transparent;
      color: #a0b4c8;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
      border-bottom: 3px solid transparent;
      white-space: nowrap;
    }

    .tab-btn:hover {
      background: rgba(255, 255, 255, .08);
      color: #fff;
    }

    .tab-btn.active {
      background: rgba(255, 255, 255, .12);
      color: #fff;
      border-bottom-color: #4fc3f7;
    }

    .tab-btn .tab-icon {
      font-size: 16px;
    }

    /* Espace flexible pour pousser les contrôles à droite */
    .tab-spacer {
      flex: 1;
    }

    /* Conteneur des boutons admin à droite dans la barre */
    .tab-controls {
      display: flex;
      align-items: center;
      gap: 4px;
      padding: 0 8px;
    }

    /* Boutons icônes dans la barre d'onglets */
    .tab-icon-btn {
      width: 36px;
      height: 36px;
      border: none;
      background: rgba(255, 255, 255, .1);
      color: #fff;
      font-size: 18px;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all .2s ease;
      padding: 0;
    }

    .tab-icon-btn:hover {
      background: rgba(255, 255, 255, .25);
    }

    .tab-icon-btn:active {
      transform: scale(.9);
    }

    .tab-icon-btn:disabled {
      opacity: .4;
      cursor: default;
    }

    .tab-start-btn {
      background: #4caf50;
      color: #fff;
    }

    .tab-start-btn:hover {
      background: #66bb6a;
    }

    .tab-stop-btn {
      background: #f44336;
      color: #fff;
    }

    .tab-stop-btn:hover {
      background: #ef5350;
    }

    /* Boutons texte (Start/Stop) style onglets */
    .tab-text-btn {
      padding: 0 16px;
      height: 32px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all .2s ease;
      color: #fff;
    }

    .tab-text-btn:active {
      transform: scale(.95);
    }

    .tab-text-btn:disabled {
      opacity: .4;
      cursor: default;
    }

    .tab-truck-btn {
      background: rgba(25, 118, 210, .4);
      font-size: 16px;
    }

    .tab-truck-btn:hover {
      background: rgba(25, 118, 210, .6);
    }

    /* â•â•â• Iframes des onglets â•â•â• */
    .tab-frame {
      position: absolute;
      top: 42px;
      /* sous la barre d'onglets */
      left: 0;
      right: 0;
      bottom: 0;
      width: 100vw;
      border: 0;
      background: white;
      display: none;
    }

    .tab-frame.active {
      display: block;
    }

    /* L'iframe carte de ville garde son plein Ã©cran mais dÃ©cale sous les onglets */
    #cityMapFrame {
      position: absolute;
      top: 42px;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100vw;
      height: calc(100% - 42px);
      border: 0;
      background: white;
    }

    #routecamFrame,
    #ceduleFrame {
      position: absolute;
      top: 42px;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100vw;
      height: calc(100% - 42px);
      border: 0;
      background: white;
    }

    #emptyState {
      position: absolute;
      top: 42px;
      left: 0;
      right: 0;
      bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #666;
      font-size: 16px;
      text-align: center;
      padding: 20px;
      pointer-events: none;
      z-index: 1;
    }

    /* Anciens boutons supprimés (déplacés dans la barre d'onglets) */

    /* â•â•â• Mode bureau (non-tablette) : sidebar fixe Ã  gauche, pas d'onglets â•â•â• */
    body.mode-bureau .tab-bar { display: none !important; }
    body.mode-bureau .sidebar {
      position: fixed;
      top: 12px;
      left: 12px;
      bottom: 12px;
      right: auto;
      width: 260px;
      max-height: none;
      display: flex !important;
      box-shadow: 0 2px 8px rgba(0,0,0,.2);
    }
    body.mode-bureau .content {
      left: 292px;
      top: 0;
    }
    body.mode-bureau #cityMapFrame,
    body.mode-bureau #routecamFrame,
    body.mode-bureau #ceduleFrame {
      top: 0;
      height: 100%;
    }
    body.mode-bureau #emptyState {
      top: 0;
    }
    /* Boutons toggle/back visibles seulement en mobile (mode bureau) */
    body.mode-bureau .btn-toggle-sidebar,
    body.mode-bureau .btn-back {
      display: none;
      position: fixed;
      top: 12px;
      z-index: 1100;
      width: 42px;
      height: 42px;
      border-radius: 50%;
      border: 2px solid #1976d2;
      background: #fff;
      color: #1976d2;
      font-size: 20px;
      line-height: 38px;
      text-align: center;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0,0,0,.3);
      padding: 0;
    }
    body.mode-bureau .btn-toggle-sidebar { left: 12px; }
    body.mode-bureau .btn-back { left: 62px; }
    body.mode-bureau .btn-toggle-sidebar:active,
    body.mode-bureau .btn-back:active { transform: scale(.9); }
    @media (max-width: 768px) {
      body.mode-bureau .btn-toggle-sidebar,
      body.mode-bureau .btn-back { display: block; }
      body.mode-bureau .sidebar {
        display: none !important;
        z-index: 1050;
        top: 60px !important;
        max-height: calc(100vh - 70px);
        overflow-y: auto;
      }
      body.mode-bureau .sidebar.sidebar-open { display: flex !important; }
      body.mode-bureau .content { left: 0 !important; }
    }
  </style>
</head>

<body<?php echo ($IsTablette ?? '') ? '' : ' class="mode-bureau"'; ?>>

  <?php if (empty($IsTablette)): ?>
  <button type="button" class="btn-toggle-sidebar" id="btnToggleSidebar" title="Villes">&#9776;</button>
  <button type="button" class="btn-back" id="btnBack" title="Retour" onclick="history.back()">&#8592;</button>
  <?php endif; ?>

  <div class="sidebar" id="sidebarPanel">
    <h3>LOCALISATION (Bureau)</h3>

    <div class="form-row">
      <label for="dtPicker">Date</label>
      <input id="dtPicker" type="date" value="<?php echo htmlspecialchars($ActiveDate, ENT_QUOTES); ?>" />
    </div>

    <div class="form-row">
      <label for="edtFilter">Rechercher une ville</label>
      <input id="edtFilter" type="text" placeholder="Nom de la ville" />
    </div>

    <div class="form-row">
      <label>
        <input id="chkShowPoly" type="checkbox" />
        Afficher le polygone
      </label>
    </div>

    <!-- Nouveau : contrÃ´le d'affichage des cercles d'adresses pour l'iframe -->
    <div class="form-row">
      <label>
        <input id="chkShowAddresses" type="checkbox" />
        Afficher adresses (cercles)
      </label>
    </div>

    <div class="form-row">
      <button type="button" id="btnReloadMapSide" class="btn btn-sm btn-outline-primary" style="width:100%;">&#8635; Recharger la carte</button>
    </div>

    <div class="cities" id="lstVilles">
      <?php if (empty($Cities)): ?>
        <div style="color:#999;">Aucune ville planifiÃ©e pour cette date.</div>
        <?php else:
        foreach (($Cities ?? []) as $c):
          $id = (int) $c['idvilles'];
          $nom = trim((string) $c['NomVille']); ?>
          <button class="city-item" data-id="<?php echo ($id ?? ''); ?>" data-nom="<?php echo htmlspecialchars($nom, ENT_QUOTES); ?>">
            <?php echo htmlspecialchars($nom, ENT_QUOTES); ?>
          </button>
      <?php endforeach;
      endif; ?>
    </div>
    <!-- Filtres par camion (rempli dynamiquement via postMessage) -->
    <div class="truck-filter-container" id="truckFilterContainer">
      <div class="tf-label">Camions</div>
      <div class="tf-wrap" id="truckFilterWrapHub"></div>
    </div>
  </div>

  <!-- â•â•â• Barre d'onglets (tablette seulement) â•â•â• -->
  <?php if (!empty($IsTablette)): ?>
  <div class="tab-bar">
    <button class="tab-btn active" data-tab="carte" title="Carte de localisation" onclick="window._switchTab('carte'); return false;">
      <span class="tab-icon">&#128205;</span> Carte
    </button>
    <button class="tab-btn" data-tab="routecam" title="Route des camions du jour" onclick="window._switchTab('routecam'); return false;">
      <span class="tab-icon">&#128666;</span> Route camions
    </button>
    <button class="tab-btn" data-tab="cedule" title="Cédule du jour" onclick="window._switchTab('cedule'); return false;">
      <span class="tab-icon">&#128197;</span> Cédule
    </button>
    <!-- Espace flexible pour pousser les boutons à droite -->
    <div class="tab-spacer"></div>
    <!-- Boutons admin à droite dans la barre -->
    <div class="tab-controls" id="tabControls">
      <button type="button" class="tab-icon-btn" id="btnToggleSidebarTab" title="Villes">&#9776;</button>
      <button type="button" class="tab-icon-btn" id="btnBackTab" title="Retour" onclick="history.back()">&#8592;</button>
      <button type="button" class="tab-icon-btn" id="btnReloadMap" title="Recharger la carte (code à jour)">&#8635;</button>
      <button id="manualBtn" class="tab-text-btn tab-start-btn" title="Démarrer un parcours manuel">Start</button>
      <button id="endManualBtn" class="tab-text-btn tab-stop-btn" title="Terminer et figer" disabled>Stop</button>
      <button type="button" class="tab-icon-btn tab-truck-btn" id="btnBackToTruck" title="Retour au camion (suivi live)" style="display:none;">
        &#128666;
      </button>
    </div>
  </div>
  <?php endif; ?>

  <div class="content">
    <!-- Onglet Carte (iframe map_nav_zone.php) -->
    <iframe id="cityMapFrame" class="tab-frame active" title="Carte ville" src="about:blank"></iframe>

    <!-- Onglet Route camions (GestionVilles /sanivan/routecam/) -->
    <iframe id="routecamFrame" class="tab-frame" title="Route camions" src="about:blank"></iframe>

    <!-- Onglet Cédule du jour (GestionVilles /sanivan/aujourdhui/1/) -->
    <iframe id="ceduleFrame" class="tab-frame" title="Cédule du jour" src="about:blank"></iframe>
  </div>

  <script>
    (function() {
      function qparam(name) {
        var m = new RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        return m && decodeURIComponent(m[1].replace(/\+/g, ' '));
      }

      var lang = "<?php echo htmlspecialchars($Lang, ENT_QUOTES); ?>";
      window.activeDate = "<?php echo htmlspecialchars($ActiveDate, ENT_QUOTES); ?>";
      var activeDate = window.activeDate;
      window.noCamion = "<?php echo htmlspecialchars($NoCamion, ENT_QUOTES); ?>";
      var noCamion = window.noCamion;
      window.idChauffeur = "<?php echo htmlspecialchars($IdChauffeur, ENT_QUOTES); ?>";
      var idChauffeur = window.idChauffeur;
      window.idVilleAdmin = <?php echo (int) $IdVilleAdmin; ?>;
      var idVilleAdmin = window.idVilleAdmin;
      var $frame = document.getElementById('cityMapFrame');
      var $empty = null;
      var $chkShowPoly = document.getElementById('chkShowPoly');

      /* â•â•â• Communication avec le host C# (js2csharp) â•â•â• */
      function sendToHost(payload) {
        try {
          var q = encodeURIComponent(JSON.stringify(payload));
          location.href = "js2csharp:" + String(payload && payload.type || "msg") + "?data=" + q;
        } catch (e) {
          try {
            if (window.chrome && window.chrome.webview && window.chrome.webview.postMessage) {
              window.chrome.webview.postMessage(payload);
            }
          } catch (e2) {}
          try {
            if (window.external && window.external.notify) {
              window.external.notify(JSON.stringify(payload));
            }
          } catch (e3) {}
        }
      }

      /* â•â•â• Shim window.TruckMap : intercepte les appels C# et les forward Ã  l'iframe â•â•â• */
      var _drawPolylineState = false;
      /* forceMapResize : no-op pour le hub (la carte est dans l'iframe qui se redimensionne via CSS) */
      window.forceMapResize = function() {
        try {
          if ($frame && $frame.contentWindow) {
            $frame.contentWindow.postMessage({
              type: 'FORCE_RESIZE'
            }, '*');
          }
        } catch (e) {}
      };
      window.TruckMap = {
        updateTruck: function(lat, lng, heading) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'LIVE_POSITION',
                cam: noCamion,
                lat: lat,
                lng: lng,
                heading: heading || 0
              }, '*');
            }
          } catch (e) {
            console.warn('TruckMap.updateTruck forward error', e);
          }
        },
        updateTruckPosition: function(lat, lng) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'LIVE_POSITION',
                cam: noCamion,
                lat: lat,
                lng: lng,
                heading: 0,
                positionOnly: true
              }, '*');
            }
          } catch (e) {
            console.warn('TruckMap.updateTruckPosition forward error', e);
          }
        },
        setDrawPolyline: function(enabled) {
          _drawPolylineState = !!enabled;
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'DRAW_POLYLINE',
                cam: noCamion,
                enabled: !!enabled
              }, '*');
            }
          } catch (e) {
            console.warn('TruckMap.setDrawPolyline forward error', e);
          }
        },
        setAdmin: function(isAdmin) {
          /* No-op : le hub est dÃ©jÃ  en mode admin */
        },
        setCityContext: function(idVille, idSecteur) {
          /* No-op : la ville est gÃ©rÃ©e par le hub via l'iframe */
        },
        setAutoBearing: function(enabled) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_AUTO_BEARING',
                enabled: !!enabled
              }, '*');
            }
          } catch (e) {}
        },
        setBearing: function(deg) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_BEARING',
                deg: deg
              }, '*');
            }
          } catch (e) {}
        },
        clearDynamicPolyline: function() {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'CLEAR_DYNAMIC_POLYLINE'
              }, '*');
            }
          } catch (e) {}
        },
        injectHistoryPoints: function(points) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'INJECT_HISTORY_POINTS',
                points: points,
                cam: noCamion
              }, '*');
            }
          } catch (e) {}
        },
        /* setPolylineButtons : forward les boutons de polylines de Cédule vers l'iframe */
        setPolylineButtons: function(buttons) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_POLYLINE_BUTTONS',
                buttons: buttons
              }, '*');
            }
          } catch (e) {
            console.warn('TruckMap.setPolylineButtons forward error', e);
          }
        },
        /* showPolylineById : demande Ã  l'iframe d'afficher une polyline spÃ©cifique */
        showPolylineById: function(id, color) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SHOW_POLYLINE_BY_ID',
                id: id,
                color: color || '#FF0000'
              }, '*');
            }
          } catch (e) {
            console.warn('TruckMap.showPolylineById forward error', e);
          }
        },
        setChauffeurContext: function(idChauf) {
          /* no-op */
        },
        setServerContext: function(cam, restUrl) {
          /* no-op */
        },
        setFollowTruck: function(follow, zoom) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_FOLLOW_TRUCK',
                follow: !!follow,
                zoom: zoom || 17
              }, '*');
            }
          } catch (e) {}
        },
        setAutoBearing: function(enabled) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_AUTO_BEARING',
                enabled: !!enabled
              }, '*');
            }
          } catch (e) {}
        },
        setBearing: function(deg) {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_BEARING',
                deg: deg || 0
              }, '*');
            }
          } catch (e) {}
        },
        resetBearing: function() {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'SET_BEARING',
                deg: 0
              }, '*');
            }
          } catch (e) {}
        },
        centerOnTruck: function() {
          try {
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'CENTER_ON_TRUCK'
              }, '*');
            }
          } catch (e) {}
        }
      };

      /* â•â•â• Boutons Start/Stop (traÃ§age du ramassage) â•â•â• */
      (function wireTraceButtons() {
        var startBtn = document.getElementById('manualBtn');
        var endBtn = document.getElementById('endManualBtn');
        var endManualInProgress = false;
        if (!startBtn || !endBtn) return;

        startBtn.addEventListener('click', function() {
          try {
            /* DÃ©marrer le traÃ§age cÃ´tÃ© JS */
            _drawPolylineState = true;
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'DRAW_POLYLINE',
                cam: noCamion,
                enabled: true
              }, '*');
            }
          } catch (e) {
            console.warn('startBtn error', e);
          }

          sendToHost({
            type: 'zone',
            inZone: true,
            source: 'manualBtn'
          });
          endBtn.disabled = false;
          startBtn.disabled = true;
        }, {
          passive: true
        });

        endBtn.addEventListener('click', function() {
          if (endManualInProgress) return;
          endManualInProgress = true;

          try {
            _drawPolylineState = false;
            if ($frame.contentWindow) {
              $frame.contentWindow.postMessage({
                type: 'DRAW_POLYLINE',
                cam: noCamion,
                enabled: false
              }, '*');
            }
          } catch (e) {}

          endBtn.disabled = true;
          startBtn.disabled = true;

          sendToHost({
            type: 'zone',
            inZone: false,
            source: 'endManualBtn',
            navigateToVilles: true
          });

          setTimeout(function() {
            endManualInProgress = false;
            if (startBtn) startBtn.disabled = false;
          }, 2000);
        }, {
          passive: true
        });
      })();

      // Init checkbox depuis lâ€™URL du hub (conserve lâ€™Ã©tat aprÃ¨s F5 / changement de date)
      var initialShowPoly = (qparam('showpoly') === '1');
      $chkShowPoly.checked = !!initialShowPoly;

      // Nouveau contrÃ´le : initial state for addresses (kept unchecked by default)
      var $chkShowAddresses = document.getElementById('chkShowAddresses');
      var initialShowAddresses = (qparam('showaddresses') === '1');
      $chkShowAddresses.checked = !!initialShowAddresses;

      function buildMapUrl(idville) {
        return '/ville/nav_zone' +
          '?dte=' + encodeURIComponent(window.activeDate || activeDate || '') +
          '&idville=' + encodeURIComponent(idville) +
          '&showpoly=' + ($chkShowPoly.checked ? '1' : '0') +
          '&source=city' +
          '&userType=admin' +
          '&nocamion=' + encodeURIComponent(noCamion) +
          '&embed=1&_ts=' + Date.now();
      }

      function clearActive() {
        Array.prototype.forEach.call(document.querySelectorAll('#lstVilles .city-item'), function(el) {
          el.classList.remove('active');
        });
      }

      function clearMap() {
        $frame.src = 'about:blank';
        if (!empty($empty)) $empty.style.display = 'flex';
        clearActive();
        hideHubTruckButtons();
      }

      var _currentCityId = 0; // ville actuellement chargÃ©e dans l'iframe
      var _isTruckCity = false; // true si la ville chargÃ©e est celle du camion admin

      function loadCity(idville, btn) {
        if (!idville) return;
        _currentCityId = parseInt(idville, 10) || 0;
        _isTruckCity = (_currentCityId === idVilleAdmin);
        $frame.src = buildMapUrl(idville);
        if (!empty($empty)) $empty.style.display = 'none';
        if (btn) {
          clearActive();
          btn.classList.add('active');
        }
        // Afficher/cacher le bouton "Retour camion" selon si on est sur la ville du camion
        var truckBtn = document.getElementById('btnBackToTruck');
        if (truckBtn) truckBtn.style.display = _isTruckCity ? 'none' : 'flex';
      }

      // Filtre client sur le nom de ville (liste)
      document.getElementById('edtFilter').addEventListener('input', function() {
        var q = (this.value || '').toLowerCase();
        Array.prototype.forEach.call(document.querySelectorAll('#lstVilles .city-item'), function(el) {
          var nom = (el.getAttribute('data-nom') || '').toLowerCase();
          el.style.display = nom.indexOf(q) !== -1 ? 'block' : 'none';
        });
      });

      // Changement de date â†’ recharge la page du hub (conserve showpoly + params admin)
      document.getElementById('dtPicker').addEventListener('change', function() {
        var d = (this.value || '').trim();
        if (!d) return;
        var url = '/ville/nav_zone_hub/' + encodeURIComponent(lang) +
          '?dte=' + encodeURIComponent(d) +
          '&showpoly=' + ($chkShowPoly.checked ? '1' : '0') +
          '&nocamion=' + encodeURIComponent(noCamion) +
          '&idchauffeur=' + encodeURIComponent(idChauffeur) +
          '&idville=' + idVilleAdmin +
          '&_ts=' + Date.now();
        window.location.href = url;
      });

      // Clic ville (liste) â†’ charge l'iframe
      document.getElementById('lstVilles').addEventListener('click', function(e) {
        var target = e.target.closest('.city-item');
        if (!target) return;
        var idville = target.getAttribute('data-id');
        if (!idville) return;
        loadCity(idville, target);
      });

      // Clic sur "Afficher le polygone" â†’ recharge la ville active uniquement
      $chkShowPoly.addEventListener('change', function() {
        var activeBtn = document.querySelector('#lstVilles .city-item.active');
        if (!activeBtn) return; // aucune ville sÃ©lectionnÃ©e
        var idville = activeBtn.getAttribute('data-id');
        if (!idville) return;
        loadCity(idville, activeBtn);
        // Met Ã  jour lâ€™URL du hub pour conserver lâ€™Ã©tat si on change la date ensuite
        var url = new URL(window.location.href);
        url.searchParams.set('showpoly', $chkShowPoly.checked ? '1' : '0');
        window.history.replaceState({}, '', url.toString());
      });

      // Envoi du postMessage vers l'iframe pour activer/dÃ©sactiver les cercles d'adresses
      function postToggleAddressCircles(active) {
        try {
          if (!$frame.contentWindow) return;
          $frame.contentWindow.postMessage({
            type: 'TOGGLE_ADDRESS_CIRCLES',
            active: !!active
          }, '*');
        } catch (e) {
          console.warn('postMessage error', e);
        }
      }

      // Quand on change la checkbox, on notifie l'iframe (si prÃ©sente)
      $chkShowAddresses.addEventListener('change', function() {
        postToggleAddressCircles(this.checked);
        // optionnel : garder param dans l'URL du hub
        var url = new URL(window.location.href);
        url.searchParams.set('showaddresses', this.checked ? '1' : '0');
        window.history.replaceState({}, '', url.toString());
      });

      // Quand l'iframe charge une carte, envoyer l'Ã©tat courant pour synchroniser
      $frame.addEventListener('load', function() {
        try {
          // petit dÃ©lai pour laisser l'iframe initialiser son DOM/JS
          setTimeout(function() {
            postToggleAddressCircles($chkShowAddresses.checked);
          }, 300);
        } catch (e) {}
        try {
          if ($frame.src === 'about:blank' || $frame.contentWindow.location.href === 'about:blank') {
            if (!empty($empty)) $empty.style.display = 'flex';
          } else {
            if (!empty($empty)) $empty.style.display = 'none';
          }
        } catch (e) {}
      });

      // Si l'utilisateur recharge la page, conserver l'Ã©tat du toggle dans l'iframe lors du premier chargement
      // (handled by load event above)

      // Chargement automatique de la ville de l'admin (passÃ©e via idVilleAdmin)
      if (idVilleAdmin > 0) {
        var adminBtn = document.querySelector('#lstVilles .city-item[data-id="' + idVilleAdmin + '"]');
        if (adminBtn) {
          loadCity(idVilleAdmin, adminBtn);
        } else {
          // La ville de l'admin n'est pas dans la liste des villes planifiÃ©es â†’ charger quand mÃªme
          loadCity(idVilleAdmin, null);
        }
      }

      // Boutons "Recharger la carte" : recharge l'iframe avec un _ts frais
      // (buildMapUrl ajoute _ts=Date.now() → force la dernière version du code)
      function reloadCityMap() {
        if (_currentCityId > 0) {
          var activeBtn = document.querySelector('#lstVilles .city-item[data-id="' + _currentCityId + '"]');
          loadCity(_currentCityId, activeBtn);
        } else {
          // Aucune ville chargée : recharger la page du hub complète
          window.location.reload();
        }
      }
      ['btnReloadMap', 'btnReloadMapSide'].forEach(function(id) {
        var rb = document.getElementById(id);
        if (rb) rb.addEventListener('click', reloadCityMap);
      });

      // Bouton "Retour camion" : recharger la ville du camion admin avec suivi live
      var btnBackToTruck = document.getElementById('btnBackToTruck');
      if (btnBackToTruck) {
        btnBackToTruck.addEventListener('click', function() {
          if (idVilleAdmin > 0) {
            var adminBtn = document.querySelector('#lstVilles .city-item[data-id="' + idVilleAdmin + '"]');
            loadCity(idVilleAdmin, adminBtn);
          }
        });
      }

      // Gestion des filtres camions (hub <-> iframe)
      var _hubTruckFilter = null;
      var $tfContainer = document.getElementById('truckFilterContainer');
      var $tfWrap = document.getElementById('truckFilterWrapHub');

      function buildHubTruckButtons(trucks) {
        if (!$tfContainer || !$tfWrap) return;
        $tfWrap.innerHTML = '';
        if (!Array.isArray(trucks) || trucks.length <= 1) {
          $tfContainer.classList.remove('visible');
          _hubTruckFilter = null;
          return;
        }
        // VÃ©rifier si le camion filtrÃ© existe encore dans la nouvelle liste
        var camKeys = trucks.map(function(t) {
          return t.cam;
        });
        if (_hubTruckFilter !== null && camKeys.indexOf(_hubTruckFilter) === -1) {
          _hubTruckFilter = null;
        }
        $tfContainer.classList.add('visible');
        var btnAll = document.createElement('button');
        btnAll.type = 'button';
        btnAll.className = 'tf-btn tf-tous' + (_hubTruckFilter === null ? ' active' : '');
        btnAll.innerHTML = '<span class="tf-dot" style="background:#666;"></span> Tous (' + trucks.length + ')';
        btnAll.addEventListener('click', function() {
          hubFilterTruck(null);
        });
        $tfWrap.appendChild(btnAll);
        trucks.forEach(function(t) {
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'tf-btn' + (_hubTruckFilter === t.cam ? ' active' : '');
          btn.setAttribute('data-cam', t.cam);
          var label = t.cam + (t.ramassage ? ' \u2013 ' + t.ramassage : '');
          btn.innerHTML = '<span class="tf-dot" style="background:' + (t.color || '#999') + ';"></span> ' + label;
          btn.addEventListener('click', function() {
            hubFilterTruck(t.cam);
          });
          $tfWrap.appendChild(btn);
        });
      }

      function hubFilterTruck(cam) {
        _hubTruckFilter = cam;
        try {
          if ($frame.contentWindow) {
            $frame.contentWindow.postMessage({
              type: 'FILTER_TRUCK',
              cam: cam
            }, '*');
          }
        } catch (e) {
          console.warn('postMessage FILTER_TRUCK error', e);
        }
        if (!empty($tfWrap)) {
          var btns = $tfWrap.querySelectorAll('.tf-btn');
          for (var i = 0; i < btns.length; i++) {
            var b = btns[i];
            var bCam = b.getAttribute('data-cam');
            if (b.classList.contains('tf-tous')) {
              b.classList.toggle('active', cam === null);
            } else {
              b.classList.toggle('active', cam === bCam);
            }
          }
        }
      }

      function hideHubTruckButtons() {
        if (!empty($tfContainer)) $tfContainer.classList.remove('visible');
        if (!empty($tfWrap)) $tfWrap.innerHTML = '';
        _hubTruckFilter = null;
      }

      window.addEventListener('message', function(e) {
        try {
          var data = e.data || {};
          if (data && data.type === 'TRUCK_LIST') {
            buildHubTruckButtons(data.trucks || []);
          }
          /* MAP_READY : l'iframe map_nav_zone.php signale que sa carte est prÃªte.
             Forwarder au C# avec un flag follow pour qu'il sache s'il doit
             activer le suivi du camion (only si on est sur la ville du camion admin). */
          if (data && data.type === 'MAP_READY') {
            try {
              sendToHost({
                type: 'mapReady',
                follow: _isTruckCity,
                isTruckCity: _isTruckCity
              });
            } catch (e2) {}
          }
        } catch (err) {
          console.warn('hub message handler error', err);
        }
      }, false);
      // Si lâ€™iframe est mis en about:blank (clearMap), on rÃ©-affiche lâ€™Ã©tat vide aprÃ¨s le load
      $frame.addEventListener('load', function() {
        try {
          if ($frame.src === 'about:blank' || $frame.contentWindow.location.href === 'about:blank') {
            if (!empty($empty)) $empty.style.display = 'flex';
          }
        } catch (e) {}
      });
    })();

    /* â•â•â• Gestion des onglets (Carte / Route camions / Cédule) â•â•â• */
    (function wireTabs() {
      var tabBtns = document.querySelectorAll('.tab-btn');
      var tabFrames = document.querySelectorAll('.tab-frame');
      var routecamFrame = document.getElementById('routecamFrame');
      var ceduleFrame = document.getElementById('ceduleFrame');
      var routecamLoaded = false;
      var ceduleLoaded = false;

      function buildUrl(base, extra) {
        var params = new URLSearchParams({
          dte: window.activeDate || activeDate || '',
          nocamion: window.noCamion || '',
          idchauffeur: window.idChauffeur || '',
          idville: window.idVilleAdmin || 0,
          allchauffeur: '1',
          from: '0'
        });
        if (extra) {
          for (var k in extra) params.set(k, extra[k]);
        }
        return base + '?' + params.toString();
      }

      function switchTab(tabName) {
        // Boutons
        tabBtns.forEach(function(b) {
          b.classList.toggle('active', b.getAttribute('data-tab') === tabName);
        });
        // Iframes
        tabFrames.forEach(function(f) {
          f.classList.remove('active');
        });
        var targetFrame = null;
        if (tabName === 'carte') {
          targetFrame = document.getElementById('cityMapFrame');
        } else if (tabName === 'routecam') {
          targetFrame = routecamFrame;
          // Charger paresseusement l'iframe routecam
          if (!routecamLoaded) {
            var routecamUrl = '/sanivan/routecam/' + encodeURIComponent(window.activeDate || activeDate || '');
            routecamFrame.src = buildUrl(routecamUrl, {
              from: '0',
              allchauffeur: '1'
            });
            routecamLoaded = true;
          }
        } else if (tabName === 'cedule') {
          targetFrame = ceduleFrame;
          // Charger paresseusement l'iframe Cédule
          // /admin affiche la page de connexion. Après login, l'admin peut
          // naviguer dans GestionVilles (cédule, etc.) directement dans l'iframe.
          if (!ceduleLoaded) {
            ceduleFrame.src = '/admin';
            ceduleLoaded = true;
          }
        }
        if (targetFrame) targetFrame.classList.add('active');

        // Afficher/cacher les contrôles admin seulement sur l'onglet Carte
        var showMapControls = (tabName === 'carte');
        var tc = document.getElementById('tabControls');
        if (tc) tc.style.display = showMapControls ? 'flex' : 'none';
      }

      // Exposer switchTab globalement pour les onclick
      window._switchTab = switchTab;

      tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
          switchTab(btn.getAttribute('data-tab'));
        });
      });

      // Exposer pour rechargement quand la date change
      window._reloadRoutecamTab = function() {
        routecamLoaded = false;
        if (routecamFrame.classList.contains('active')) {
          var routecamUrl = '/sanivan/routecam/' + encodeURIComponent(window.activeDate || activeDate || '');
          routecamFrame.src = buildUrl(routecamUrl, {
            from: '0',
            allchauffeur: '1'
          });
          routecamLoaded = true;
        }
      };
      window._reloadCeduleTab = function() {
        ceduleLoaded = false;
        if (ceduleFrame.classList.contains('active')) {
          ceduleFrame.src = '/admin';
          ceduleLoaded = true;
        }
      };
    })();
  </script>
  <script>
    (function() {
      var btn = document.getElementById('btnToggleSidebar') || document.getElementById('btnToggleSidebarTab');
      var sb = document.getElementById('sidebarPanel');
      if (!btn || !sb) return;
      btn.addEventListener('click', function() {
        sb.classList.toggle('sidebar-open');
        btn.textContent = sb.classList.contains('sidebar-open') ? '\u2715' : '\u2630';
      });
      document.addEventListener('click', function(e) {
        if (!sb.classList.contains('sidebar-open')) return;
        if (sb.contains(e.target) || btn.contains(e.target)) return;
        // En mode bureau, ne fermer que sur mobile
        if (document.body.classList.contains('mode-bureau') && window.innerWidth > 768) return;
        sb.classList.remove('sidebar-open');
        btn.textContent = '\u2630';
      });
    })();
  </script>
</body>

</html>