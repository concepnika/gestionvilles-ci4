<?php
defined('BASEPATH') or exit('No direct script access allowed');

$zonesFeatures = isset($zonesFeatures) ? $zonesFeatures : [];
$polylinesData = isset($polylinesData) ? $polylinesData : [];
$ActiveDate = isset($ActiveDate) ? $ActiveDate : date('Y-m-d');
$IdVille = isset($IdVille) ? (int) $IdVille : 0;
$Lang = isset($Lang) ? $Lang : 'fr';
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Zones - Localisation</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <!-- Plugin de rotation (bearing) pour suivre la direction du camion admin -->
  <script src="https://unpkg.com/leaflet-rotate@0.2.8/dist/leaflet-rotate.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
  <script src="https://unpkg.com/leaflet-polylinedecorator@1.7.0/dist/leaflet.polylineDecorator.min.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      overflow: hidden;
    }

    #map {
      position: fixed;
      top: 80px;
      bottom: 0;
      left: 260px;
      right: 0;
    }

    .toolbar {
      position: fixed;
      z-index: 1000;
      top: 100px;
      left: 12px;
      background: rgba(255, 255, 255, .95);
      border-radius: 6px;
      padding: 10px 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
      font-family: Arial, sans-serif;
      font-size: 13px;
      width: 230px;
    }

    .toolbar label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .toolbar input[type="date"],
    .toolbar input[type="text"] {
      width: 180px;
    }

    .toolbar .row {
      display: flex;
      gap: 8px;
      align-items: center;
      margin: 6px 0;
      flex-wrap: wrap;
    }

    .leaflet-popup-content img {
      box-shadow: 0 1px 4px rgba(0, 0, 0, .25);
      cursor: pointer;
    }

    .wide-popup .leaflet-popup-content {
      width: 600px !important;
      max-width: 600px !important;
      max-height: 70vh;
      overflow: auto;
    }

    /* Style amélioré pour l'overlay d'image plein écran */
    #imgOverlay {
      position: fixed;
      z-index: 5000;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: none;
      background: rgba(0, 0, 0, 0.92);
      align-items: center;
      justify-content: center;
      padding: 20px;
      box-sizing: border-box;
    }

    #imgOverlay .closeBtn {
      position: absolute;
      top: 15px;
      right: 20px;
      background: #dc3545;
      color: #fff;
      border: 0;
      padding: 10px 18px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      border-radius: 6px;
      z-index: 5001;
    }

    #imgOverlay .closeBtn:hover {
      background: #c82333;
    }

    #imgOverlay img {
      max-width: 95%;
      max-height: 95%;
      object-fit: contain;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.8);
      border-radius: 8px;
      background: #000;
    }

    /* ═══ Boutons flottants mobile ═══ */
    /* ═══ Filtres camions ═══ */
    .truck-filter-section {
      margin-top: 10px;
      border-top: 1px solid #ddd;
      padding-top: 8px;
      display: none;
    }

    .truck-filter-section.visible {
      display: block;
    }

    .truck-filter-section label {
      margin-bottom: 4px;
    }

    .truck-filter-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
    }

    .truck-filter-btn {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border: 2px solid #ccc;
      border-radius: 5px;
      background: #fff;
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      transition: all .15s;
      white-space: nowrap;
    }

    .truck-filter-btn:hover {
      background: #f0f4fa;
      border-color: #90caf9;
    }

    .truck-filter-btn.active {
      background: #e3f2fd;
      border-color: #1976d2;
      box-shadow: 0 0 0 2px rgba(25, 118, 210, .25);
    }

    .truck-filter-btn .color-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      border: 1px solid rgba(0, 0, 0, .15);
      flex-shrink: 0;
    }

    .truck-filter-btn.btn-tous {
      background: #f5f5f5;
      font-weight: 700;
    }

    .truck-filter-btn.btn-tous.active {
      background: #c8e6c9;
      border-color: #2e7d32;
    }

    /* ═══ Panneau des polylines de cédule (admin via hub) ═══ */
    .cedule-polyline-panel {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 6000;
      background: rgba(255, 255, 255, 0.98);
      padding: 12px;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
      min-width: 180px;
      max-width: 260px;
      max-height: 50vh;
      overflow-y: auto;
    }

    .cedule-polyline-title {
      font-weight: 600;
      margin-bottom: 10px;
      font-size: 14px;
      color: #333;
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 8px;
    }

    .cedule-polyline-buttons {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .cedule-polyline-btn {
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
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      flex-shrink: 0;
      min-height: 48px;
    }

    .cedule-polyline-btn:hover {
      background: #f0f5fb;
      border-color: #4a90e2;
      transform: translateY(-1px);
    }

    .cedule-polyline-btn.active {
      background: #e3f2fd;
      border-color: #2196f3;
      font-weight: 600;
      box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
    }

    .cedule-poly-color {
      width: 22px;
      height: 22px;
      border-radius: 4px;
      border: 2px solid #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
      flex-shrink: 0;
    }

    .cedule-poly-label {
      flex: 1;
      text-align: left;
    }

    /* ═══ Boutons flottants mobile ═══ */
    .btn-toggle-toolbar,
    .btn-back {
      display: none;
      position: fixed;
      z-index: 1100;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid DarkBlue;
      background: #fff;
      color: DarkBlue;
      font-size: 18px;
      line-height: 36px;
      text-align: center;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0, 0, 0, .3);
      padding: 0;
    }

    .btn-toggle-toolbar {
      top: 12px;
      left: 12px;
    }

    .btn-back {
      top: 12px;
      left: 60px;
      font-size: 20px;
    }

    .btn-toggle-toolbar:active,
    .btn-back:active {
      transform: scale(.9);
    }

    @media (max-width: 768px) {

      .btn-toggle-toolbar,
      .btn-back {
        display: block;
      }

      .toolbar {
        display: none !important;
        z-index: 1050;
        top: 58px !important;
        max-height: calc(100vh - 68px);
        overflow-y: auto;
      }

      .toolbar.toolbar-open {
        display: block !important;
      }

      #map {
        left: 0 !important;
        top: 58px !important;
      }
    }
  </style>
</head>

<body>
  <button type="button" class="btn-toggle-toolbar" id="btnToggleToolbar" title="Outils">&#9776;</button>
  <button type="button" class="btn-back" id="btnBack" title="Retour" onclick="history.back()">&#8592;</button>
  <div id="map"></div>
  <!-- Contrôle flottant "Rotation auto" (mode admin embed uniquement) -->
  <div id="floatingAutoBearing" style="display:none;position:fixed;top:12px;right:12px;z-index:1000;background:rgba(255,255,255,.92);border:1px solid #bbb;border-radius:6px;padding:6px 10px;box-shadow:0 2px 6px rgba(0,0,0,.2);font-size:13px;font-family:Arial,sans-serif;">
    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:600;color:#333;">
      <input id="chkAutoBearing" type="checkbox" /> Rotation auto
    </label>
  </div>

  <!-- Overlay pour afficher les images en plein écran -->
  <div id="imgOverlay">
    <button class="closeBtn" title="Fermer (Échap)">✕ Fermer</button>
    <img src="" alt="Image agrandie" />
  </div>

  <div class="toolbar">
    <label>LOCALISATION (Zone)</label>
    <label for="dtPicker">Date</label>
    <input id="dtPicker" type="date" value="<?php echo htmlspecialchars($ActiveDate, ENT_QUOTES); ?>" />
    <label for="edtFind">Rechercher</label>
    <div class="row">
      <input id="edtFind" type="text" placeholder="numéro, rue, ville" />
      <button id="btnFind" class="btn btn-sm btn-primary">Chercher</button>
      <button id="btnCreateTag" class="btn btn-sm btn-secondary" style="display:none;">Créer tag</button>
      <!-- Toggle adresses -->
      <button id="btnToggleAddressCircles" class="btn btn-sm btn-info" type="button">Afficher adresses</button>
    </div>
    <div class="row" style="margin-top:8px;">
      <label style="display:flex;align-items:center;gap:6px;font-weight:normal;">
        <input id="chkPhotos" type="checkbox" checked /> Photos collecte
      </label>
    </div>
    <div class="row" style="margin-top:8px;">
      <button id="btnRefresh" class="btn btn-sm btn-outline-primary" style="width:100%;">Rafraîchir positions</button>
    </div>
    <div class="row" style="margin-top:8px;">
      <label style="font-size:12px;color:#666;">Points GPS: <span id="gpsPointsCount">-</span></label>
    </div>
    <!-- Filtres par camion -->
    <div class="truck-filter-section" id="truckFilterSection">
      <label>Camions</label>
      <div class="truck-filter-wrap" id="truckFilterWrap"></div>
    </div>
  </div>

  <script>
    /******************** PARAMÈTRES INIT *************************/
    var zonesData = <?php echo json_encode($zonesFeatures, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var activeDate = "<?php echo htmlspecialchars($ActiveDate, ENT_QUOTES); ?>";
    // Date locale du client (toISOString() serait en UTC → décalage le soir)
    var todayDate = (function() {
      var d = new Date();
      return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    })();
    var isTodayView = (activeDate === todayDate);
    var lang = "<?php echo htmlspecialchars($Lang, ENT_QUOTES); ?>";
    var collectePoints = <?php echo json_encode(isset($CollectePoints) ? $CollectePoints : [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var fromPhpVille = <?php echo (int) $IdVille; ?>;
    var isSupervisor = <?php echo isset($IsSupervisor) ? (int) $IsSupervisor : 0; ?>;
    var isEmbed = (qparam('embed') === '1');
    var userType = qparam('userType') || 'ville'; // ⬅️ AJOUT
    var isAdmin = (userType === 'supervisor' || userType === 'admin'); // ⬅️ AJOUT

    // Ajouter cette variable globale au début de la section script (après les autres variables)
    var secteursList = [];

    // ⬇️ AJOUTER CETTE FONCTION ICI (après la déclaration de secteursList)
    async function loadSecteursByVille() {
      if (idVille <= 0) return Promise.resolve();
      try {
        const url = '/sanivan/getsecteurs_by_ville?idville=' + encodeURIComponent(idVille) + '&_ts=' + Date.now();
        const r = await fetch(url);
        const j = await r.json();
        if (j && j.ok && Array.isArray(j.secteurs)) {
          secteursList = j.secteurs;
        }
      } catch (e) {
        console.warn('loadSecteursByVille error', e);
        // Ne pas bloquer l'exécution si le chargement des secteurs échoue
        secteursList = [];
      }
    }

    function qparam(name) {
      var m = new RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
      return m && decodeURIComponent(m[1].replace(/\+/g, ' '));
    }
    var showPolyParam = qparam('showpoly');
    var displayZones = (showPolyParam == null || showPolyParam === '') ? true : (showPolyParam === '1');
    var sourceMode = qparam('source') || 'raw';
    var zoneTraceMode = (function() {
      var m = (qparam('zonemode') || '').toLowerCase();
      return (m === 'flux') ? 'flux' : 'clip';
    })();
    var isEmbed = (qparam('embed') === '1');

    (function resolveVille() {
      var fromUrl = qparam('idville');
      var parsed = 0;
      if (fromUrl && /^\d+$/.test(fromUrl)) parsed = parseInt(fromUrl, 10);
      else if (fromPhpVille > 0) parsed = fromPhpVille;
      window.idVille = parsed || 0;
    })();
    var currentSecteur = (function() {
      var val = qparam('idsecteur');
      var parsed = (val && /^-?\d+$/.test(val)) ? parseInt(val, 10) : 0;
      return isNaN(parsed) ? 0 : parsed;
    })();

    /******************** OVERLAY IMAGE PLEIN ÉCRAN *************************/
    (function() {
      var overlay = document.getElementById('imgOverlay');
      var overlayImg = overlay.querySelector('img');
      var closeBtn = overlay.querySelector('.closeBtn');

      // Fonction globale pour afficher l'image en plein écran
      window.showFullscreen = function(url) {
        if (!url) return;
        overlayImg.src = url;
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      };

      // Fonction pour fermer l'overlay
      function closeOverlay() {
        overlay.style.display = 'none';
        overlayImg.src = '';
        document.body.style.overflow = '';
      }

      // Clic sur le bouton Fermer
      closeBtn.addEventListener('click', closeOverlay);

      // Clic en dehors de l'image (sur le fond noir)
      overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
          closeOverlay();
        }
      });

      // Touche Échap pour fermer
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.style.display === 'flex') {
          closeOverlay();
        }
      });
    })();

    /******************** CARTE *************************/
    var map = L.map('map', {
      maxZoom: 19,
      rotate: true,
      touchRotate: true,
      zoomControl: false
    }).setView([46.8, -71.2], 7);
    // Contrôles zoom en haut à gauche (le hub a ses boutons à droite maintenant)
    L.control.zoom({
      position: 'topleft'
    }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map);

    /* ═══ Signal MAP_READY au hub parent (pour que le C# réinjecte le contexte) ═══ */
    map.whenReady(function() {
      try {
        if (window.parent && window.parent !== window) {
          window.parent.postMessage({
            type: 'MAP_READY'
          }, '*');
        }
      } catch (e) {}
    });

    /******************** ZONES *************************/
    var zoneLayers = [],
      zonePolyGeoJson = [],
      zonePolyBuffered = [];

    function drawZones(features) {
      zoneLayers.forEach(function(l) {
        try {
          map.removeLayer(l);
        } catch (e) {}
      });
      zonePolyGeoJson = [];
      zonePolyBuffered = [];
      zoneLayers = [];
      var group = [];

      function addPolygon(coords) {
        if (!Array.isArray(coords) || coords.length < 3) return;
        var latlngs = coords.map(function(c) {
          return [c[1], c[0]];
        });
        if (displayZones) {
          var poly = L.polygon(latlngs, {
            color: '#1976d2',
            weight: 2,
            fillColor: '#64b5f6',
            fillOpacity: 0.15
          }).addTo(map);
          zoneLayers.push(poly);
          group.push(poly);
        }
        var ring = coords.slice(0);
        var first = coords[0],
          last = coords[coords.length - 1];
        if (first[0] !== last[0] || first[1] !== last[1]) ring.push([first[0], first[1]]);
        var gj = turf.polygon([ring]);
        zonePolyGeoJson.push(gj);
        try {
          var buf = turf.buffer(gj, 0.02, {
            units: 'kilometers'
          });
          if (buf) zonePolyBuffered.push(buf);
        } catch (e) {}
      }

      (features || []).forEach(function(f) {
        if (!f || !f.geometry) return;
        if (f.geometry.type === 'Polygon') {
          addPolygon((f.geometry.coordinates && f.geometry.coordinates[0]) || null);
        } else if (f.geometry.type === 'MultiPolygon') {
          (f.geometry.coordinates || []).forEach(function(poly) {
            addPolygon((poly && poly[0]) || null);
          });
        }
      });

      if (displayZones && group.length > 0) {
        var fg = L.featureGroup(group);
        map.fitBounds(fg.getBounds(), {
          padding: [20, 20]
        });
      }
    }
    drawZones(zonesData);

    function zoneContains(lat, lng) {
      if (zonePolyGeoJson.length === 0) return true;
      var pt = turf.point([lng, lat]);
      if (zonePolyBuffered.length > 0) {
        for (var i = 0; i < zonePolyBuffered.length; i++) {
          if (turf.booleanPointInPolygon(pt, zonePolyBuffered[i])) return true;
        }
        return false;
      }
      for (var j = 0; j < zonePolyGeoJson.length; j++) {
        if (turf.booleanPointInPolygon(pt, zonePolyGeoJson[j])) return true;
      }
      return false;
    }

    /******************** CAMIONS / POLYLINES *************************/
    var trucksByCam = {};
    var ramassageByCam = {},
      chauffeurByCam = {};

    /* ═══ Variables pour la polyline dynamique live (camion admin via hub) ═══ */
    var _liveDrawActive = false; // true = traçage en cours
    var _liveDrawCam = ''; // camion dont on trace le parcours live
    var _livePolyline = null; // objet L.polyline en cours de dessin
    var _liveCoords = []; // points [lat,lng] de la polyline live
    var noCamionFromUrl = qparam('nocamion') || ''; // camion admin passé dans l'URL
    window._userZoomLocked = false;
    var _isZooming = false;
    map.on('zoomstart', function() {
      _isZooming = true;
      window._userZoomLocked = true;
    });
    map.on('zoomend', function() {
      _isZooming = false;
    });

    /* ═══ Variables de suivi camion admin (follow + bearing) ═══ */
    var _followTruck = false; // suivre le camion (recentrer la carte)
    var _followZoom = 17; // zoom de suivi
    var _autoBearing = false; // rotation auto de la carte selon le cap du camion
    var _lastTruckLatLng = null; // dernière position connue du camion admin
    var _lastTruckHeading = 0; // dernier cap connu du camion admin
    var _desiredBearing = 0; // bearing cible à réappliquer après setView

    // Case à cocher "Rotation auto" (contrôle flottant, mode admin uniquement)
    (function setupAutoBearingCheckbox() {
      var panel = document.getElementById('floatingAutoBearing');
      var chk = document.getElementById('chkAutoBearing');
      if (!panel || !chk) return;
      // Afficher seulement en mode admin (tablette)
      if (!isAdmin) { panel.style.display = 'none'; return; }
      panel.style.display = 'block';
      chk.addEventListener('change', function() {
        _autoBearing = !!chk.checked;
        if (!_autoBearing) {
          // Arrêter la rotation ET le recentrage pour laisser le chauffeur naviguer librement
          _followTruck = false;
          _desiredBearing = 0;
          try {
            if (typeof map.stopHeadingUp === 'function') map.stopHeadingUp();
            if (typeof map.setBearing === 'function') map.setBearing(0);
          } catch (e) {}
        } else {
          // Réactiver le suivi et la rotation selon le cap du camion
          _followTruck = true;
          if (_lastTruckLatLng) {
            try { map.panTo(_lastTruckLatLng); } catch (e) {}
          }
          if (_lastTruckHeading && typeof map.setBearing === 'function') {
            _desiredBearing = -_lastTruckHeading;
            try { map.setBearing(_desiredBearing); } catch (e) {}
          }
        }
      });
    })();

    // Ne PAS désactiver le follow sur drag (l'utilisateur veut que ça suive toujours)

    /* Recentre la carte sur le camion admin (au centre).
       Utilise panTo au lieu de setView pour ne pas réinitialiser le bearing. */
    function recenterAdminTruck(forceSetView) {
      if (!_lastTruckLatLng) return;
      var curZoom = map.getZoom() || _followZoom;
      var zoomToUse = window._userZoomLocked ? curZoom : (_followZoom || curZoom);
      // Ajuster le zoom si nécessaire (sans setView pour préserver le bearing)
      if (map.getZoom() !== zoomToUse) {
        try {
          map.setZoom(zoomToUse, {
            animate: false
          });
        } catch (e) {}
      }
      // panTo ne réinitialise pas le bearing
      try {
        map.panTo(_lastTruckLatLng, {
          animate: !forceSetView,
          duration: 0.3
        });
      } catch (e) {
        try {
          map.setView(_lastTruckLatLng, zoomToUse, {
            animate: false
          });
        } catch (e2) {}
      }
    }

    /* Crée une icône de camion : fond rouge avec rond vert au centre */
    function createDirectionalTruckIcon(heading, color) {
      var c = color || '#1976d2';
      // SVG : cercle rouge avec rond vert au centre (pas de flèche)
      var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">' +
        '<circle cx="20" cy="20" r="18" fill="#d32f2f" fill-opacity="0.85" stroke="#fff" stroke-width="2"/>' +
        '<circle cx="20" cy="20" r="6" fill="#4caf50" stroke="#fff" stroke-width="1.5"/>' +
        '</svg>';
      return L.divIcon({
        html: svg,
        className: 'truck-directional-icon',
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20]
      });
    }

    function normCamKey(cam) {
      return (cam == null) ? '' : String(cam).trim();
    }

    function ensureCam(cam) {
      var k = normCamKey(cam);
      if (!trucksByCam[k]) trucksByCam[k] = {
        marker: null,
        polylines: [],
        points: [],
        allFullCoords: []
      };
    }
    var colors = ['#2e7d32', '#1976d2', '#ef6c00', '#6a1b9a', '#00838f', '#c62828', '#4e342e', '#455a64', '#7cb342', '#5e35b1'];
    var colorForCam = {};

    function getColorForCam(cam) {
      var k = normCamKey(cam);
      if (!k) return '#2e7d2';
      if (!colorForCam[k]) colorForCam[k] = colors[Object.keys(colorForCam).length % colors.length];
      return colorForCam[k];
    }

    function getTruckIcon(cam) {
      var url = 'https://www.robert-daoust.ca/assets/img/numcam/cam' + normCamKey(cam) + '.png';
      return L.icon({
        iconUrl: url,
        iconSize: [40, 40],
        iconAnchor: [20, 30]
      });
    }

    function attachPolylineClick(cam, polyline) {
      polyline.on('click', function(e) {
        var key = normCamKey(cam);
        var pts = trucksByCam[key].points || [];
        var best = null,
          bestD = Infinity;
        pts.forEach(function(p) {
          if (typeof p.lat !== 'number' || typeof p.lng !== 'number') return;
          var d = Math.hypot(p.lat - e.latlng.lat, p.lng - e.latlng.lng);
          if (d < bestD) {
            bestD = d;
            best = p;
          }
        });
        var chauffeur = (best && best.chauffeur) || chauffeurByCam[key] || '';
        var heure = (best && best.hre) || '';
        var adresse = (best && best.adresse) || '';

        // 🔹 Conditionnement : ne pas afficher le chauffeur si usager ville
        var popupHtml = '<div style="min-width:300px;">' +
          '<div><b>Camion:</b> ' + key + '</div>';

        if (isSupervisor) { // ⬅️ AJOUT CONDITION
          popupHtml += '<div><b>Chauffeur:</b> ' + (chauffeur || 'N/A') + '</div>';
        }

        popupHtml += '<div><b>Heure:</b> ' + (heure || 'N/A') + '</div>' +
          '<div><b>Adresse:</b> ' + (adresse || 'N/A') + '</div>';

        // ⬅️ MODIFICATION : seuls les admins peuvent enregistrer des polylines
        var allowSave = (idVille > 0 && isAdmin);
        var saveBtnId = null;
        if (allowSave) {
          saveBtnId = 'save-polyline-' + Date.now();
          popupHtml += '<div class="mt-2">' +
            '<button type="button" class="btn btn-sm btn-outline-success" id="' + saveBtnId + '">' +
            (lang === 'fr' ? 'Enregistrer ce trajet' : 'Save this route') +
            '</button>' +
            '</div>';
        }

        popupHtml += '</div>';

        var infoPopup = L.popup({
            maxWidth: 600,
            className: 'wide-popup'
          })
          .setLatLng(e.latlng)
          .setContent(popupHtml)
          .openOn(map);

        if (allowSave && saveBtnId) {
          setTimeout(function() {
            var btn = document.getElementById(saveBtnId);
            if (!btn) return;
            btn.addEventListener('click', function(ev) {
              ev.preventDefault();
              ev.stopPropagation();
              openSavePolylinePrompt(polyline, key, e.latlng);
            });
          }, 0);
        }
      });
    }

    function loadRamassages() {
      if (idVille <= 0) return Promise.resolve();
      var url = '/sanivan/get_ramassages_city/' + encodeURIComponent(activeDate) + '?idville=' + encodeURIComponent(idVille);
      return fetch(url).then(r => r.json()).then(j => {
        if (j && j.ok && Array.isArray(j.items)) {
          j.items.forEach(function(it) {
            var k = normCamKey(it.cam);
            if (!k) return;
            ramassageByCam[k] = it.ramassage || '';
            chauffeurByCam[k] = it.chauffeur || '';
          });
        }
      }).catch(e => console.warn('Ramassages error', e));
    }

    // --- Circles d'adresses (global) ---
    window.addressCirclesByCam = window.addressCirclesByCam || {};
    window.addressCirclesGlobalToggleActive = window.addressCirclesGlobalToggleActive || false;

    function createAddressCirclesForCam(cam) {
      cam = normCamKey(cam);
      if (!cam) return;
      if (window.addressCirclesByCam[cam]) return; // déjà
      var t = trucksByCam[cam];
      window.addressCirclesByCam[cam] = [];
      if (!t || !Array.isArray(t.points) || t.points.length === 0) return;
      t.points.forEach(function(p) {
        if (!p) return;
        var lat = Number(p.lat),
          lng = Number(p.lng);
        if (!isFinite(lat) || !isFinite(lng)) return;
        // Filtrer les coordonnées invalides (lat=0, lng=0)
        if (lat === 0 && lng === 0) return;
        var addr = (p.adresse || p.address || '').trim();
        if (!addr) return;
        var circle = L.circleMarker([lat, lng], {
          radius: 6,
          color: '#ff5722',
          weight: 1,
          fillColor: '#ffccbc',
          fillOpacity: 0.95
        });
        circle.on('click', function(e) {
          try {
            var meta = {
              hre: p.hre || 'N/A',
              adresse: addr,
              chauffeur: p.chauffeur || (t && t.chauffeur) || 'N/A'
            };
            var content = '<div style="min-width:260px"><b>Camion: </b>' + escapeHtml(cam) + '<br/>';
            if (isSupervisor) content += '<b>Chauffeur: </b>' + escapeHtml(meta.chauffeur) + '<br/>';
            content += '<b>Heure: </b>' + escapeHtml(meta.hre) + '<br/><b>Adresse: </b>' + escapeHtml(meta.adresse) + '</div>';
            L.popup({
              maxWidth: 360
            }).setLatLng(e.latlng).setContent(content).openOn(map);
          } catch (err) {
            console.warn('show popup circle', err);
          }
        });
        circle.bindTooltip(addr, {
          direction: 'top',
          offset: [0, -8]
        });
        window.addressCirclesByCam[cam].push(circle);
      });
    }

    function showAddressCirclesForCam(cam) {
      cam = normCamKey(cam);
      createAddressCirclesForCam(cam);
      var arr = window.addressCirclesByCam[cam] || [];
      arr.forEach(function(c) {
        try {
          if (!map.hasLayer(c)) c.addTo(map);
        } catch (e) {}
      });
    }

    function hideAddressCirclesForCam(cam) {
      cam = normCamKey(cam);
      var arr = window.addressCirclesByCam[cam] || [];
      arr.forEach(function(c) {
        try {
          if (map.hasLayer(c)) map.removeLayer(c);
        } catch (e) {}
      });
    }

    function hideAllAddressCircles() {
      Object.keys(window.addressCirclesByCam || {}).forEach(function(k) {
        hideAddressCirclesForCam(k);
      });
    }

    // drawRawPolylines : ajoute la création automatique des cercles si toggle actif
    function drawRawPolylines(list) {
      (list || []).forEach(function(item) {
        var key = normCamKey(item.cam || '');
        if (!key || !Array.isArray(item.coords) || item.coords.length < 2) return;
        ensureCam(key);
        var color = getColorForCam(key);
        if (Array.isArray(item.meta)) trucksByCam[key].points = trucksByCam[key].points.concat(item.meta);

        var latlngsFull = item.coords.map(function(p) {
          return [p[0], p[1]];
        });

        // Accumuler TOUTES les coordonnées (avant filtrage par zone) au niveau du camion
        // pour permettre la sauvegarde du parcours complet même si splitByGaps a découpé en segments
        if (!Array.isArray(trucksByCam[key].allFullCoords)) trucksByCam[key].allFullCoords = [];
        for (var ci = 0; ci < latlngsFull.length; ci++) {
          trucksByCam[key].allFullCoords.push(latlngsFull[ci]);
        }

        // Détection et filtrage des sauts de temps/distance trop importants
        // Seuils augmentés pour éviter le découpage excessif des segments
        var MAX_DISTANCE_METERS = 2000; // 2km au lieu de 200m
        var MAX_TIME_MINUTES = 30; // 30 minutes au lieu de 2min
        var MAX_SPEED_KMH = 200; // 200 km/h au lieu de 100

        // Dessiner la polyline avec toutes les coordonnées
        var latlngsRender = latlngsFull.slice();
        if (zonePolyGeoJson.length > 0) {
          latlngsRender = latlngsRender.filter(function(ll) {
            return zoneContains(ll[0], ll[1]);
          });
          if (latlngsRender.length < 2) return;
        }
        var pl = L.polyline(latlngsRender, {
          color: color,
          weight: 3,
          opacity: 0.9
        });
        var isVisible = (_activeTruckFilter === null || _activeTruckFilter === key);
        if (isVisible) pl.addTo(map);
        // Stocker les coordonnées complètes avant filtrage par zone pour la sauvegarde
        pl._fullCoords = latlngsFull.slice();
        attachPolylineClick(key, pl);
        trucksByCam[key].polylines.push(pl);

        // --- Si le toggle est actif, (re)créer et afficher les cercles pour ce camion ---
        try {
          if (window.addressCirclesGlobalToggleActive && isVisible) {
            try {
              hideAddressCirclesForCam(key);
            } catch (e) {}
            try {
              delete window.addressCirclesByCam[key];
            } catch (e) {}
            try {
              createAddressCirclesForCam(key);
              showAddressCirclesForCam(key);
            } catch (e) {
              console.warn('address circles create error', e);
            }
          }
        } catch (e) {}
      });
    }

    // Calcul de l'angle en degrés entre trois points (p1-p2-p3)
    function calculateAngle(p1, p2, p3) {
      var bearing1 = calculateBearing(p2[0], p2[1], p1[0], p1[1]);
      var bearing2 = calculateBearing(p2[0], p2[1], p3[0], p3[1]);
      var angle = Math.abs(bearing2 - bearing1);
      if (angle > 180) angle = 360 - angle;
      return angle;
    }

    // Calcul du bearing en degrés entre deux points
    function calculateBearing(lat1, lng1, lat2, lng2) {
      var y = Math.sin(lng2 - lng1) * Math.cos(lat2);
      var x = Math.cos(lat1) * Math.sin(lat2) -
        Math.sin(lat1) * Math.cos(lat2) * Math.cos(lng2 - lng1);
      var brng = Math.atan2(y, x);
      return (brng * 180 / Math.PI + 360) % 360;
    }

    // Calcul de la distance en mètres entre deux points (formule Haversine)
    function calculateDistance(lat1, lng1, lat2, lng2) {
      var R = 6371000; // Rayon de la Terre en mètres
      var dLat = (lat2 - lat1) * Math.PI / 180;
      var dLng = (lng2 - lng1) * Math.PI / 180;
      var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }

    // Calcul de la différence de temps en minutes entre deux timestamps
    function calculateTimeDiff(time1, time2) {
      if (!time1 || !time2) return 0;
      var t1 = new Date(time1).getTime();
      var t2 = new Date(time2).getTime();
      if (isNaN(t1) || isNaN(t2)) return 0;
      return Math.abs(t2 - t1) / (1000 * 60); // Convertir en minutes
    }

    function loadCityPolylines() {
      if (idVille <= 0) return Promise.resolve();
      var url = (sourceMode === 'raw') ?
        '/sanivan/getpolylines_city_raw/' + encodeURIComponent(activeDate) + '?idville=' + encodeURIComponent(idVille) :
        '/sanivan/getpolylines_city/' + encodeURIComponent(activeDate) + '?idville=' + encodeURIComponent(idVille) + '&snap=1';
      return fetch(url).then(r => r.json()).then(j => {
        if (j && j.ok && Array.isArray(j.polylines)) drawRawPolylines(j.polylines);
        buildTruckFilterButtons();
      }).catch(e => console.error('Polylines error', e));
    }

    function loadGpsCount() {
      if (idVille <= 0) return;
      var url = '/sanivan/get_gps_count_city/' + encodeURIComponent(activeDate) + '?idville=' + encodeURIComponent(idVille) + '&_ts=' + Date.now();
      fetch(url).then(r => r.json()).then(j => {
        if (j && j.ok && typeof j.count === 'number') {
          var countEl = document.getElementById('gpsPointsCount');
          if (countEl) countEl.textContent = j.count;
        }
      }).catch(e => console.error('GPS count error', e));
    }

    var liveTimer = null;

    /* Trails live par camion : la polyline grandit à chaque position reçue,
       sans attendre le rechargement complet des polylines depuis le serveur. */
    var liveTrails = {}; // cam -> { pl: L.polyline|null, coords: [[lat,lng]], lastHre: '' }

    function extendLiveTrail(key, pos, isVisible) {
      var hre = pos.hre || '';
      var tr = liveTrails[key];
      if (!tr) tr = liveTrails[key] = {
        pl: null,
        coords: [],
        lastHre: ''
      };
      // Ignorer les positions déjà vues ('YYYY-MM-DD HH:MM:SS' → tri lexical ok)
      if (hre && tr.lastHre && hre <= tr.lastHre) return;
      if (hre) tr.lastHre = hre;

      var ll = [pos.lat, pos.lng];
      var lastPt = tr.coords[tr.coords.length - 1];
      if (lastPt && lastPt[0] === ll[0] && lastPt[1] === ll[1]) return;

      // Amorcer le trail depuis le dernier point connu du parcours serveur
      if (tr.coords.length === 0) {
        var t0 = trucksByCam[key];
        var full = t0 && t0.allFullCoords;
        if (full && full.length > 0) {
          var seed = full[full.length - 1];
          tr.coords.push([seed[0], seed[1]]);
        }
      }
      tr.coords.push(ll);

      // Garder allFullCoords complet pour la sauvegarde du parcours
      var tk = trucksByCam[key];
      if (tk && Array.isArray(tk.allFullCoords)) tk.allFullCoords.push([ll[0], ll[1]]);

      if (tr.coords.length < 2) return;
      if (!tr.pl) {
        tr.pl = L.polyline(tr.coords, {
          color: getColorForCam(key),
          weight: 4,
          opacity: 0.9
        });
        attachPolylineClick(key, tr.pl);
        if (tk) tk.polylines.push(tr.pl);
      } else {
        tr.pl.setLatLngs(tr.coords);
      }
      if (isVisible && !map.hasLayer(tr.pl)) tr.pl.addTo(map);
    }

    function refreshLive() {
      if (idVille <= 0) return;
      var url = '/sanivan/get_last_positions_city/' + encodeURIComponent(activeDate) +
        '?idville=' + encodeURIComponent(idVille) + '&_ts=' + Date.now();
      fetch(url).then(r => r.json()).then(j => {
        if (!j || !j.ok || !Array.isArray(j.items)) return;
        j.items.forEach(function(pos) {
          var key = normCamKey(pos.cam);
          if (!key || typeof pos.lat !== 'number' || typeof pos.lng !== 'number') return;
          ensureCam(key);
          if (!zoneContains(pos.lat, pos.lng)) return;
          var isVisible = (_activeTruckFilter === null || _activeTruckFilter === key);
          if (trucksByCam[key].marker) {
            trucksByCam[key].marker.setLatLng([pos.lat, pos.lng]);
            try {
              if (isVisible && !map.hasLayer(trucksByCam[key].marker)) trucksByCam[key].marker.addTo(map);
              else if (!isVisible && map.hasLayer(trucksByCam[key].marker)) map.removeLayer(trucksByCam[key].marker);
            } catch (e) {}
          } else {
            trucksByCam[key].marker = L.marker([pos.lat, pos.lng], {
              icon: getTruckIcon(key),
              title: 'Camion ' + key
            });
            if (isVisible) trucksByCam[key].marker.addTo(map);
          }
          // Étendre la polyline live du camion (tracé continu entre les refreshs)
          extendLiveTrail(key, pos, isVisible);
        });
      }).catch(e => console.warn('Live error', e));
    }

    const COLLECTE_POINTS_API = "<?php echo ci3_url('ville/collecte_points'); ?>";

    function clearRenderedRoutes() {
      Object.keys(trucksByCam).forEach(function(k) {
        var t = trucksByCam[k];
        (t && t.polylines || []).forEach(function(pl) {
          try {
            map.removeLayer(pl);
          } catch (e) {}
        });
        if (t) {
          t.polylines = [];
          t.points = [];
          t.allFullCoords = [];
        }
      });
      // Réinitialiser les trails live (le layer est déjà retiré via t.polylines) ;
      // on conserve lastHre pour ne pas réajouter la dernière position connue
      Object.keys(liveTrails).forEach(function(k) {
        var tr = liveTrails[k];
        liveTrails[k] = {
          pl: null,
          coords: [],
          lastHre: (tr && tr.lastHre) || ''
        };
      });
      // also clear circles cache to avoid stale markers
      try {
        hideAllAddressCircles();
        window.addressCirclesByCam = {};
      } catch (e) {}
    }

    /******************** FILTRE PAR CAMION *************************/
    var _activeTruckFilter = null; // null = tous, sinon clé camion

    function buildTruckFilterButtons() {
      var section = document.getElementById('truckFilterSection');
      var wrap = document.getElementById('truckFilterWrap');
      if (!section || !wrap) return;
      wrap.innerHTML = '';

      var camKeys = Object.keys(trucksByCam).filter(function(k) {
        return k && trucksByCam[k] && trucksByCam[k].polylines.length > 0;
      });

      // Si le filtre actif pointe vers un camion qui n'existe plus, réinitialiser
      if (_activeTruckFilter !== null && camKeys.indexOf(_activeTruckFilter) === -1) {
        _activeTruckFilter = null;
        // Remettre toutes les polylines sur la carte
        camKeys.forEach(function(k) {
          var t = trucksByCam[k];
          if (!t) return;
          (t.polylines || []).forEach(function(pl) {
            try {
              if (!map.hasLayer(pl)) pl.addTo(map);
            } catch (e) {}
          });
        });
      }

      // Masquer la section s'il y a 0 ou 1 camion
      if (camKeys.length <= 1) {
        section.classList.remove('visible');
        try {
          if (window.parent && window.parent !== window) {
            window.parent.postMessage({
              type: 'TRUCK_LIST',
              trucks: []
            }, '*');
          }
        } catch (e) {}
        return;
      }
      section.classList.add('visible');

      // Bouton "Tous"
      var btnAll = document.createElement('button');
      btnAll.type = 'button';
      btnAll.className = 'truck-filter-btn btn-tous' + (_activeTruckFilter === null ? ' active' : '');
      btnAll.innerHTML = '<span class="color-dot" style="background:#666;"></span> Tous (' + camKeys.length + ')';
      btnAll.addEventListener('click', function() {
        filterTruckPolylines(null);
      });
      wrap.appendChild(btnAll);

      // Un bouton par camion
      var truckListForParent = [];
      camKeys.forEach(function(cam) {
        var color = getColorForCam(cam);
        var ramassage = ramassageByCam[cam] || '';
        var label = cam + (ramassage ? ' – ' + ramassage : '');
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'truck-filter-btn' + (_activeTruckFilter === cam ? ' active' : '');
        btn.setAttribute('data-cam', cam);
        btn.innerHTML = '<span class="color-dot" style="background:' + color + ';"></span> ' + escapeHtml(label);
        btn.addEventListener('click', function() {
          filterTruckPolylines(cam);
        });
        wrap.appendChild(btn);
        truckListForParent.push({
          cam: cam,
          color: color,
          ramassage: ramassage
        });
      });

      // Notifier le parent (hub) de la liste des camions
      try {
        if (window.parent && window.parent !== window) {
          window.parent.postMessage({
            type: 'TRUCK_LIST',
            trucks: truckListForParent
          }, '*');
        }
      } catch (e) {}
    }

    function reapplyTruckFilter() {
      if (_activeTruckFilter === null) return;
      Object.keys(trucksByCam).forEach(function(k) {
        var t = trucksByCam[k];
        if (!t) return;
        var show = (_activeTruckFilter === k);
        (t.polylines || []).forEach(function(pl) {
          try {
            if (show && !map.hasLayer(pl)) pl.addTo(map);
            else if (!show && map.hasLayer(pl)) map.removeLayer(pl);
          } catch (e) {}
        });
        if (t.marker) {
          try {
            if (show && !map.hasLayer(t.marker)) t.marker.addTo(map);
            else if (!show && map.hasLayer(t.marker)) map.removeLayer(t.marker);
          } catch (e) {}
        }
        if (window.addressCirclesGlobalToggleActive) {
          if (show) showAddressCirclesForCam(k);
          else hideAddressCirclesForCam(k);
        }
      });
    }

    function filterTruckPolylines(camFilter) {
      _activeTruckFilter = camFilter;
      var camKeys = Object.keys(trucksByCam);

      camKeys.forEach(function(k) {
        var t = trucksByCam[k];
        if (!t) return;
        var show = (camFilter === null || camFilter === k);

        // Polylines
        (t.polylines || []).forEach(function(pl) {
          try {
            if (show && !map.hasLayer(pl)) pl.addTo(map);
            else if (!show && map.hasLayer(pl)) map.removeLayer(pl);
          } catch (e) {}
        });

        // Marqueur camion
        if (t.marker) {
          try {
            if (show && !map.hasLayer(t.marker)) t.marker.addTo(map);
            else if (!show && map.hasLayer(t.marker)) map.removeLayer(t.marker);
          } catch (e) {}
        }

        // Cercles d'adresses
        if (window.addressCirclesGlobalToggleActive) {
          if (show) showAddressCirclesForCam(k);
          else hideAddressCirclesForCam(k);
        }
      });

      // Fit bounds sur les polylines visibles
      var visibleBounds = [];
      camKeys.forEach(function(k) {
        if (camFilter !== null && camFilter !== k) return;
        var t = trucksByCam[k];
        if (!t) return;
        (t.polylines || []).forEach(function(pl) {
          try {
            visibleBounds.push(pl.getBounds());
          } catch (e) {}
        });
      });
      if (visibleBounds.length > 0) {
        try {
          var combined = visibleBounds[0];
          for (var i = 1; i < visibleBounds.length; i++) {
            combined.extend(visibleBounds[i]);
          }
          map.fitBounds(combined, {
            padding: [30, 30],
            maxZoom: 16
          });
        } catch (e) {}
      }

      // Mise à jour état actif des boutons
      var wrap = document.getElementById('truckFilterWrap');
      if (wrap) {
        var btns = wrap.querySelectorAll('.truck-filter-btn');
        btns.forEach(function(b) {
          var bCam = b.getAttribute('data-cam');
          if (b.classList.contains('btn-tous')) {
            b.classList.toggle('active', camFilter === null);
          } else {
            b.classList.toggle('active', camFilter === bCam);
          }
        });
      }
    }

    function isPopupOpen() {
      return !!map._popup && typeof map._popup.isOpen === 'function' && map._popup.isOpen();
    }
    async function refreshCollectePhotos() {
      if (idVille <= 0) return;
      try {
        if (isPopupOpen()) return;
        const url = COLLECTE_POINTS_API + '?idville=' + idVille + '&dte=' + encodeURIComponent(activeDate) + '&_ts=' + Date.now();
        const res = await fetch(url);
        const json = await res.json();
        if (json && json.ok && Array.isArray(json.data)) {
          collectePoints = json.data.slice();
          renderCollecteMarkers();
        }
      } catch (e) {
        console.warn('refreshCollectePhotos error', e);
      }
    }

    async function softRefresh() {
      try {
        await loadRamassages();
        clearRenderedRoutes();
        await loadCityPolylines();
        loadGpsCount();
        reapplyTruckFilter();
        await refreshLive();
        await loadMapTags();
        await refreshCollectePhotos();
      } catch (e) {
        console.warn('softRefresh error', e);
      }
    }

    var pageHidden = false,
      pageRefreshTimer = null;
    document.addEventListener('visibilitychange', function() {
      pageHidden = document.hidden;
    });

    function startAutoRefresh() {
      if (pageRefreshTimer) clearInterval(pageRefreshTimer);
      if (!isTodayView) return;
      pageRefreshTimer = setInterval(function() {
        if (!pageHidden) softRefresh();
      }, 30000);
    }
    startAutoRefresh();
    window.addEventListener('beforeunload', function() {
      if (pageRefreshTimer) clearInterval(pageRefreshTimer);
      if (liveTimer) clearInterval(liveTimer);
    });

    /******************** RECHERCHE *************************/
    document.getElementById('btnFind').addEventListener('click', performSearch);
    document.getElementById('edtFind').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performSearch();
      }
    });

    function performSearch() {
      var q = (document.getElementById('edtFind').value || '').trim().toLowerCase();
      if (!q) return;
      var matches = [];
      Object.keys(trucksByCam).forEach(function(cam) {
        (trucksByCam[cam].points || []).forEach(function(m) {
          var adr = (m && m.adresse) ? String(m.adresse).toLowerCase() : '';
          if (adr && adr.indexOf(q) !== -1) matches.push({
            cam: cam,
            meta: m
          });
        });
      });
      if (matches.length === 0) {
        alert('Aucune correspondance trouvée.');
        return;
      }
      matches.sort(function(a, b) {
        return Date.parse(b.meta.hre || 0) - Date.parse(a.meta.hre || 0);
      });
      var pick = matches[0];
      if (typeof pick.meta.lat === 'number' && typeof pick.meta.lng === 'number') {
        map.setView([pick.meta.lat, pick.meta.lng], 17);
        var popupAddress = pick.meta.adresse || 'N/A';
        var popupHtml =
          '<div style="min-width:280px;"><b>Camion:</b> ' + escapeHtml(pick.cam || '') + '<br/>' +
          (isSupervisor ? '<b>Chauffeur:</b> ' + escapeHtml(pick.meta.chauffeur || 'N/A') + '<br/>' : '') +
          '<b>Heure:</b> ' + escapeHtml(pick.meta.hre || 'N/A') + '<br/>' +
          '<b>Adresse:</b> ' + escapeHtml(popupAddress) + '<br/>' +
          '<button type="button" class="btn btn-sm btn-secondary popup-create-tag" data-address="' + encodeURIComponent(popupAddress) + '" data-lat="' + (pick.meta.lat) + '" data-lng="' + (pick.meta.lng) + '">' +
          (lang === 'fr' ? 'Créer tag' : 'Create tag') + '</button>' +
          '</div>';
        var popup = L.popup({
          maxWidth: 600,
          className: 'wide-popup'
        }).setLatLng([pick.meta.lat, pick.meta.lng]).setContent(popupHtml);
        map.once('popupopen', function(e) {
          if (e.popup !== popup) return;
          var btn = e.popup.getElement().querySelector('.popup-create-tag');
          if (!btn) return;
          btn.addEventListener('click', function() {
            var addr = decodeURIComponent(btn.getAttribute('data-address') || '');
            var lat = parseFloat(btn.getAttribute('data-lat'));
            var lng = parseFloat(btn.getAttribute('data-lng'));
            openCreateTagForm(addr, lat, lng);
          });
        });
        popup.openOn(map);
      }
    }

    /******************** UTILITAIRES *************************/
    const PHOTO_HOST = 'https://robert-daoust.ca';
    const PHOTO_MIN_ZOOM = 13;

    function buildPhotoUrl(rel) {
      if (!rel) return '';
      rel = String(rel).replace(/^(\.\/|\/)+/, '');
      return PHOTO_HOST.replace(/\/+$/, '') + '/' + rel;
    }

    function escapeHtml(v) {
      if (v === null || v === undefined) return '-';
      return String(v)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function extractPolylineLatLngs(raw) {
      var out = [];
      (function dive(target) {
        if (!target) return;
        if (Array.isArray(target)) {
          target.forEach(dive);
          return;
        }
        if (typeof target.lat === 'number' && typeof target.lng === 'number') {
          out.push([target.lat, target.lng]);
        }
      })(raw);
      return out;
    }

    function gatherFullCamCoords(camKey) {
      var key = normCamKey(camKey);
      if (!key || !trucksByCam[key]) return null;
      var result = [];
      var store = function(pt) {
        if (!pt) return;
        if (Array.isArray(pt) && pt.length >= 2) {
          result.push([Number(pt[0]), Number(pt[1])]);
        } else if (typeof pt.lat === 'number' && typeof pt.lng === 'number') {
          result.push([pt.lat, pt.lng]);
        }
      };
      // Priorité 1 : allFullCoords (accumulé dans drawRawPolylines, avant filtrage par zone)
      if (Array.isArray(trucksByCam[key].allFullCoords) && trucksByCam[key].allFullCoords.length > 0) {
        trucksByCam[key].allFullCoords.forEach(store);
      } else {
        // Priorité 2 : _fullCoords de chaque polyline (avant filtrage par zone)
        trucksByCam[key].polylines.forEach(function(pl) {
          var arr = Array.isArray(pl._fullCoords) && pl._fullCoords.length ? pl._fullCoords : extractPolylineLatLngs(pl.getLatLngs());
          arr.forEach(store);
        });
      }
      if (result.length === 0) return null;
      // remove consecutive duplicates
      var compact = [];
      for (var i = 0; i < result.length; i++) {
        var last = compact[compact.length - 1];
        var current = result[i];
        if (!last || last[0] !== current[0] || last[1] !== current[1]) {
          compact.push(current);
        }
      }
      return compact;
    }

    function openSavePolylinePrompt(polyline, camKey, anchorLatLng) {
      if (idVille <= 0) {
        alert(lang === 'fr' ? 'Ville inconnue' : 'Unknown city');
        return;
      }
      // Récupérer TOUTES les coordonnées du camion (tous segments confondus, avant filtrage par zone)
      // pour sauvegarder le parcours complet, pas seulement le segment cliqué
      var coords = gatherFullCamCoords(camKey);
      if (!coords || coords.length < 2) {
        // Fallback : utiliser les coords de la polyline cliquée
        coords = Array.isArray(polyline._fullCoords) && polyline._fullCoords.length ?
          polyline._fullCoords.slice() :
          extractPolylineLatLngs(polyline.getLatLngs());
      }
      if (coords.length < 2) {
        alert(lang === 'fr' ? 'Trajet invalide.' : 'Route is invalid.');
        return;
      }
      try {
        map.closePopup();
      } catch (e) {}
      var formId = 'polyline-form-' + Date.now();

      // Construire le select des secteurs
      var secteurOptions = '<option value="0">' + (lang === 'fr' ? 'Aucun secteur' : 'No sector') + '</option>';
      secteursList.forEach(function(s) {
        var selected = (currentSecteur > 0 && s.id === currentSecteur) ? ' selected' : '';
        secteurOptions += '<option value="' + s.id + '"' + selected + '>' + escapeHtml(s.nom) + '</option>';
      });

      var html = '<div style="min-width:360px;">' +
        '<div class="mb-2"><label class="form-label">' + (lang === 'fr' ? 'Nom du parcours' : 'Route name') + '</label>' +
        '<input type="text" class="form-control form-control-sm" id="' + formId + '_name" value="" /></div>' +
        '<div class="text-end" style="display:flex;gap:8px;justify-content:flex-end;">' +
        '<button type="button" class="btn btn-sm btn-secondary" id="' + formId + '_cancel">' + (lang === 'fr' ? 'Annuler' : 'Cancel') + '</button>' +
        '<button type="button" class="btn btn-sm btn-primary" id="' + formId + '_save">' + (lang === 'fr' ? 'Enregistrer' : 'Save') + '</button>' +
        '</div>' +
        '</div>';
      var anchor = anchorLatLng || L.latLng(coords[Math.floor(coords.length / 2)][0], coords[Math.floor(coords.length / 2)][1]);
      var popup = L.popup({
        maxWidth: 400,
        className: 'wide-popup'
      }).setLatLng(anchor).setContent(html).openOn(map);
      setTimeout(function() {
        var input = document.getElementById(formId + '_name');

        var cancel = document.getElementById(formId + '_cancel');
        var save = document.getElementById(formId + '_save');
        if (input) input.focus();
        if (cancel) {
          cancel.addEventListener('click', function() {
            map.closePopup(popup);
          });
        }
        if (save) {
          save.addEventListener('click', function() {
            var name = input ? input.value.trim() : '';
            if (!name) {
              alert(lang === 'fr' ? 'Veuillez saisir un nom.' : 'Please enter a name.');
              if (input) input.focus();
              return;
            }
            savePolylineToServer(name, coords)
              .then(function() {
                map.closePopup(popup);
              })
              .catch(function(err) {
                alert(err && err.message ? err.message : err);
              });
          });
        }
      }, 20);
    }

    function savePolylineToServer(name, coords) {
      if (idVille <= 0) {
        return Promise.reject(new Error(lang === 'fr' ? 'Ville inconnue.' : 'Unknown city.'));
      }
      var payload = {
        idville: idVille,
        nom: name,
        coords: (coords || []).map(function(p) {
          return {
            lat: Number(p[0]),
            lng: Number(p[1])
          };
        })
      };
      return fetch('/ville/save_polyline', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      }).then(function(res) {
        if (!res.ok) {
          throw new Error('HTTP ' + res.status);
        }
        return res.json();
      }).then(function(j) {
        if (!j || !j.ok) {
          throw new Error(j && j.error ? j.error : (lang === 'fr' ? 'Enregistrement impossible.' : 'Unable to save.'));
        }
        alert(j.message || (lang === 'fr' ? 'Parcours enregistré.' : 'Route saved.'));
      });
    }

    /******************** PHOTOS COLLECTE *************************/
    var collecteCluster = L.markerClusterGroup({
      maxClusterRadius: 60,
      disableClusteringAtZoom: 17,
      spiderfyOnEveryZoom: false,
      showCoverageOnHover: false
    }).addTo(map);

    function createPhotoMarker(p) {
      var lat = parseFloat(p.latitude),
        lng = parseFloat(p.longitude);
      if (isNaN(lat) || isNaN(lng)) return null;
      var photoUrl = buildPhotoUrl(p.photo_path);
      var root = document.createElement('div');
      root.style.minWidth = '260px';
      root.style.maxWidth = '480px';

      function line(label, val, bold) {
        var d = document.createElement('div');
        d.innerHTML = (bold ? '<b>' + escapeHtml(label) + '</b>' : '<b>' + escapeHtml(label) + ':</b> ' + escapeHtml(val || ''));
        root.appendChild(d);
      }
      line(p.type_ramassage || 'Type', '', true);
      line('Date', p.date_collecte);
      line('Ville', p.ville);
      line('Camion', p.no_camion);
      line('Adresse', p.address);
      if (p.note) {
        var nd = document.createElement('div');
        nd.style.marginTop = '4px';
        nd.innerHTML = '<b>Note:</b> ' + escapeHtml(p.note);
        root.appendChild(nd);
      }
      var box = document.createElement('div');
      box.style.cssText = 'margin-top:8px;border:1px solid #ccc;padding:4px;background:#fafafa;border-radius:4px;text-align:center;';
      if (photoUrl) {
        var img = document.createElement('img');
        img.src = photoUrl;
        img.alt = 'Photo collecte';
        img.style.maxWidth = '180px';
        img.style.height = 'auto';
        img.style.borderRadius = '4px';
        img.style.cursor = 'pointer';
        img.title = 'Cliquer pour agrandir';
        img.onerror = function() {
          box.textContent = 'Image introuvable';
        };
        img.addEventListener('click', function() {
          showFullscreen(photoUrl);
        });
        box.appendChild(img);
      } else {
        box.textContent = 'Aucune image';
      }
      root.appendChild(box);
      var link = document.createElement('div');
      link.style.marginTop = '6px';
      link.style.textAlign = 'right';
      link.innerHTML = '<a href="' + escapeHtml(photoUrl) + '" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Ouvrir original</a>';
      root.appendChild(link);
      return L.marker([lat, lng]).bindPopup(root, {
        maxWidth: 600,
        className: 'wide-popup'
      });
    }

    function renderCollecteMarkers() {
      if (isPopupOpen()) return;
      collecteCluster.clearLayers();
      var show = document.getElementById('chkPhotos').checked;
      var zOk = map.getZoom() >= PHOTO_MIN_ZOOM;
      if (!show || !zOk) return;
      (collectePoints || []).forEach(function(p) {
        if (!p.latitude || !p.longitude) return;
        var mk = createPhotoMarker(p);
        if (mk) collecteCluster.addLayer(mk);
      });
    }
    document.getElementById('chkPhotos').addEventListener('change', renderCollecteMarkers);
    map.on('zoomend', renderCollecteMarkers);
    renderCollecteMarkers();

    /******************** TAGS *************************/
    var TAG_REASONS = [];
    var tagReasonsLoaded = false;
    async function loadTagReasons() {
      try {
        const url = "<?php echo ci3_url('ville/maptag/reasons'); ?>?_ts=" + Date.now();
        const r = await fetch(url);
        const j = await r.json();
        if (j && j.ok && Array.isArray(j.data)) {
          TAG_REASONS = j.data.slice();
          tagReasonsLoaded = true;
        }
      } catch (e) {
        console.warn('Erreur raisons', e);
      }
    }
    loadTagReasons();

    var tagCluster = L.markerClusterGroup({
      maxClusterRadius: 60,
      disableClusteringAtZoom: 18,
      spiderfyOnEveryZoom: false,
      showCoverageOnHover: false
    }).addTo(map);
    var tagMarkersById = {};

    function statusColor(st) {
      switch ((st || '').toUpperCase()) {
        case 'DONE':
        case 'VALIDER':
          return '#2e7d32';
        case 'ACK':
          return '#1976d2';
        case 'REJECTED':
          return '#c62828';
        default:
          return '#ef6c00';
      }
    }

    function statusLabel(st) {
      var s = (st || '').toUpperCase();
      if (lang === 'fr') {
        switch (s) {
          case 'DONE':
            return 'Terminé';
          case 'VALIDER':
            return 'Validé';
          case 'ACK':
            return 'Accusé';
          case 'REJECTED':
            return 'Rejeté';
          default:
            return 'En attente';
        }
      } else {
        switch (s) {
          case 'DONE':
            return 'Done';
          case 'VALIDER':
            return 'Validated';
          case 'ACK':
            return 'Acknowledged';
          case 'REJECTED':
            return 'Rejected';
          default:
            return 'Pending';
        }
      }
    }

    function makeTagIcon(color) {
      return L.divIcon({
        className: 'tag-marker',
        html: '<div style="width:18px;height:18px;border-radius:50%;background:' + color + ';border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.35)"></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9]
      });
    }

    function extractResponses(tag) {
      var list = [];

      function add(o) {
        if (!o) return;
        list.push({
          text: o.text || o.message || o.comment || '',
          author: o.author || o.user || o.by || '',
          time: o.time || o.date || o.at || '',
          status: o.status || ''
        });
      }
      try {
        if (Array.isArray(tag.responses)) tag.responses.forEach(add);
        else if (Array.isArray(tag.response_list)) tag.response_list.forEach(add);
        else if (Array.isArray(tag.comments)) tag.comments.forEach(add);
        else if (Array.isArray(tag.discussions)) tag.discussions.forEach(add);
        else if (tag.last_response) add(tag.last_response);
      } catch (e) {}
      return list;
    }

    function tagPopupHtml(tag) {
      var imgHtml = '';
      if (tag.image_path) {
        var imgUrl = buildPhotoUrl(tag.image_path);
        imgHtml = '<div class="img-preview" style="margin-top:8px;border:1px solid #ccc;padding:4px;background:#fafafa;border-radius:4px;text-align:center;">' +
          '<img data-full="' + escapeHtml(imgUrl) + '" src="' + escapeHtml(imgUrl) + '" alt="img" style="max-width:180px;height:auto;border-radius:4px;cursor:pointer;" title="Cliquer pour agrandir" />' +
          '</div>';
      }
      var responses = extractResponses(tag);
      var discussionHtml =
        '<div style="margin-top:6px;">' +
        '<div style="font-weight:600;margin-bottom:4px;">' + (lang === 'fr' ? 'Discussions / réponses' : 'Discussions / replies') + '</div>' +
        (responses.length > 0 ?
          responses.map(function(r) {
            var t = escapeHtml(r.text || '');
            var a = escapeHtml(r.author || '');
            var d = escapeHtml(r.time || '');
            var s = escapeHtml(statusLabel(r.status || ''));
            return '<div style="border-left:3px solid #ddd;padding-left:8px;margin-bottom:6px;font-size:12px;">' +
              (t ? '<div>' + t + '</div>' : '') +
              '<div style="color:#777;">' + [a, d, s].filter(function(x) {
                return x && x !== '-';
              }).join(' · ') + '</div>' +
              '</div>';
          }).join('') :
          '<div style="font-size:12px;color:#777;">' + (lang === 'fr' ? 'Aucune réponse.' : 'No replies.') + '</div>'
        ) +
        '</div>';

      var editZone =
        '<div class="edit-zone" data-id="' + tag.id + '" style="display:none;margin-top:8px;border-top:1px solid #eee;padding-top:8px;">' +
        '<div style="font-weight:600;margin-bottom:6px;">' + (lang === 'fr' ? 'Modifier le tag' : 'Edit tag') + '</div>' +
        '<div style="margin-bottom:6px;"><b>' + (lang === 'fr' ? 'Adresse' : 'Address') + ':</b><br/><input type="text" name="address_text" value="' + escapeHtml(tag.address_text || '') + '" class="form-control form-control-sm"/></div>' +
        '<div style="margin-bottom:6px;"><b>' + (lang === 'fr' ? 'Raison' : 'Reason') + ':</b><br/>' +
        '<select name="reason" class="form-select form-select-sm">' + ((tagReasonsLoaded ? TAG_REASONS : ['—']).map(function(r) {
          var sel = (String(r || '') === String(tag.reason || '')) ? ' selected' : '';
          return '<option' + sel + ' value="' + escapeHtml(r) + '">' + escapeHtml(r) + '</option>';
        }).join('')) +
        '</select></div>' +
        '<div style="margin-bottom:6px;"><b>' + (lang === 'fr' ? 'Description' : 'Description') + ':</b><br/><textarea name="description" rows="3" class="form-control form-control-sm">' + escapeHtml(tag.description || '') + '</textarea></div>' +
        '<div style="display:flex;gap:6px;justify-content:flex-end;">' +
        '<button data-action="SAVE_EDIT" data-id="' + tag.id + '" class="btn btn-primary btn-sm">Enregistrer</button>' +
        '<button data-action="CANCEL_EDIT" data-id="' + tag.id + '" class="btn btn-secondary btn-sm">Annuler</button>' +
        '</div>' +
        '</div>';

      var replySection =
        '<div class="reply-zone" style="margin-top:10px;border-top:1px solid #eee;padding-top:8px;display:none;">' +
        '<div style="font-weight:600;margin-bottom:6px;">' + (lang === 'fr' ? 'Ajouter une réponse' : 'Add a reply') + '</div>' +
        '<textarea name="reply_text" rows="3" class="form-control form-control-sm" placeholder="' + (lang === 'fr' ? 'Votre réponse...' : 'Your reply...') + '"></textarea>' +
        '<div style="display:flex;gap:6px;justify-content:flex-end;margin-top:6px;">' +
        ' <button data-action="POST_REPLY" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-success btn-sm">' + (lang === 'fr' ? 'Envoyer' : 'Send') + '</button>' +
        '</div>' +
        '</div>';

      var lblAddReply = (lang === 'fr' ? 'Ajouter une réponse' : 'Add reply');
      var lblEdit = (lang === 'fr' ? 'Modifier' : 'Edit');
      var lblAck = (lang === 'fr' ? 'Accuser' : 'Acknowledge');
      var lblDone = (lang === 'fr' ? 'Terminer' : 'Done');
      var lblReject = (lang === 'fr' ? 'Rejeter' : 'Reject');

      return '<div class="popup-wrap" style="min-width:360px;max-width:560px;">' +
        '<div><b>' + (lang === 'fr' ? 'Statut' : 'Status') + ':</b> <span style="color:' + statusColor(tag.status) + '">' + escapeHtml(statusLabel(tag.status)) + '</span></div>' +
        (tag.address_text ? '<div><b>' + (lang === 'fr' ? 'Adresse' : 'Address') + ':</b> ' + escapeHtml(tag.address_text) + '</div>' : '') +
        (tag.reason ? '<div><b>' + (lang === 'fr' ? 'Raison' : 'Reason') + ':</b> ' + escapeHtml(tag.reason) + '</div>' : '') +
        (tag.description ? '<div><b>' + (lang === 'fr' ? 'Description' : 'Description') + ':</b> ' + escapeHtml(tag.description) + '</div>' : '') +
        imgHtml +
        discussionHtml +
        '<div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">' +
        '<button data-action="OPEN_REPLY" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-secondary btn-sm" style="flex:1;">' + lblAddReply + '</button>' +
        '<button data-action="EDIT" data-id="' + tag.id + '" class="btn btn-primary btn-sm" style="flex:1;">' + lblEdit + '</button>' +
        '<button data-action="ACK" data-id="' + tag.id + '" class="btn btn-info btn-sm" style="flex:1;">' + lblAck + '</button>' +
        '<button data-action="DONE" data-id="' + tag.id + '" class="btn btn-success btn-sm" style="flex:1;">' + lblDone + '</button>' +
        '<button data-action="REJECTED" data-id="' + tag.id + '" class="btn btn-danger btn-sm" style="flex:1;">' + lblReject + '</button>' +
        '</div>' +
        editZone +
        replySection +
        '</div>';
    }

    async function respondTag(id, status, responseText) {
      try {
        var fd = new FormData();
        fd.append('id', String(id));
        fd.append('status', String(status));
        if (responseText) fd.append('response_text', responseText);

        const r = await fetch("<?php echo ci3_url('ville/maptag/respond'); ?>", {
          method: 'POST',
          body: fd
        });
        const raw = await r.text();
        let j;
        try {
          j = JSON.parse(raw);
        } catch {
          j = null;
        }

        if (!r.ok) {
          alert((lang === 'fr' ? 'Erreur HTTP ' : 'HTTP error ') + r.status);
          return;
        }
        if (!j || !j.ok) {
          alert((lang === 'fr' ? 'Réponse non envoyée: ' : 'Reply not sent: ') + (j && j.error ? j.error : raw));
          return;
        }

        alert(lang === 'fr' ? 'Réponse envoyée.' : 'Reply sent.');

        await loadMapTags();
        try {
          map.closePopup();
        } catch (e) {}
      } catch (e) {
        alert('Erreur: ' + (e && e.message ? e.message : e));
      }
    }

    async function updateTag(id, address_text, reason, description) {
      try {
        var fd = new FormData();
        fd.append('id', id);
        fd.append('id_ville', String(idVille || 0));
        fd.append('address_text', address_text || '');
        fd.append('reason', reason || '');
        fd.append('description', description || '');
        const r = await fetch("<?php echo ci3_url('ville/maptag/update'); ?>", {
          method: 'POST',
          body: fd
        });
        const j = await r.json();
        if (!j || !j.ok) {
          alert(j && j.error ? j.error : (lang === 'fr' ? 'Échec mise à jour' : 'Update failed'));
          return;
        }
        await loadMapTags();
      } catch (e) {
        alert('Erreur: ' + (e && e.message ? e.message : e));
      }
    }

    function wirePopupButtons(root) {
      root.querySelectorAll('button[data-action]').forEach(function(btn) {
        btn.onclick = async function() {
          var action = btn.getAttribute('data-action');
          var id = btn.getAttribute('data-id');

          if (!action) return;

          if (action === 'OPEN_REPLY') {
            var rz = root.querySelector('.reply-zone');
            if (rz) {
              rz.style.display = 'block';
              var ta = rz.querySelector('textarea[name="reply_text"]');
              if (ta) ta.focus();
            }
            return;
          }

          if (!id) return;

          if (action === 'REJECTED') {
            var txt = prompt(lang === 'fr' ? 'Raison du rejet:' : 'Reject reason:', '') || '';
            await respondTag(id, 'REJECTED', txt);
            map.closePopup();
            return;
          }
          if (action === 'ACK' || action === 'DONE') {
            await respondTag(id, action, '');
            map.closePopup();
            return;
          }
          if (action === 'EDIT') {
            var zone = root.querySelector('.edit-zone[data-id="' + id + '"]');
            if (zone) zone.style.display = 'block';
            return;
          }
          if (action === 'CANCEL_EDIT') {
            var zone = root.querySelector('.edit-zone[data-id="' + id + '"]');
            if (zone) zone.style.display = 'none';
            return;
          }
          if (action === 'SAVE_EDIT') {
            var zone = root.querySelector('.edit-zone[data-id="' + id + '"]');
            if (!zone) return;
            var address = zone.querySelector('input[name="address_text"]')?.value || '';
            var reason = zone.querySelector('select[name="reason"]')?.value || '';
            var descr = zone.querySelector('textarea[name="description"]')?.value || '';
            await updateTag(id, address, reason, descr);
            map.closePopup();
            return;
          }
          if (action === 'POST_REPLY') {
            var rz = root.querySelector('.reply-zone');
            var txt = rz?.querySelector('textarea[name="reply_text"]')?.value?.trim() || '';
            if (!txt) {
              alert(lang === 'fr' ? 'Veuillez saisir une réponse.' : 'Please enter a reply.');
              return;
            }
            await respondTag(id, 'ACK', txt);
            try {
              rz.querySelector('textarea[name="reply_text"]').value = '';
            } catch (e) {}
            map.closePopup();
            return;
          }
        };
      });
      // Gestion du clic sur les images de tags pour affichage plein écran
      root.querySelectorAll('.img-preview img[data-full]').forEach(function(img) {
        img.onclick = function() {
          showFullscreen(img.getAttribute('data-full'));
        };
      });
    }

    function upsertTagMarker(tag) {
      var id = tag.id;
      var lat = parseFloat(tag.latitude),
        lng = parseFloat(tag.longitude);
      if (isNaN(lat) || isNaN(lng)) return null;
      if (tag.status && (tag.status.toUpperCase() === 'DONE' || tag.status.toUpperCase() === 'VALIDER')) {
        if (tagMarkersById[id]) {
          tagCluster.removeLayer(tagMarkersById[id]);
          delete tagMarkersById[id];
        }
        return null;
      }
      if (tagMarkersById[id]) {
        var mk = tagMarkersById[id];
        mk.setIcon(makeTagIcon(statusColor(tag.status)));
        mk.setPopupContent(tagPopupHtml(tag));
        return mk;
      }
      var mk = L.marker([lat, lng], {
        icon: makeTagIcon(statusColor(tag.status)),
        title: 'Tag ' + (tag.tag_code || '')
      });
      mk.bindPopup(tagPopupHtml(tag), {
        maxWidth: 620,
        className: 'wide-popup'
      });
      mk.on('popupopen', function(e) {
        wirePopupButtons(e.popup.getElement());
      });
      tagCluster.addLayer(mk);
      tagMarkersById[id] = mk;
      return mk;
    }

    // Déduplication
    function normalizeCoord(v) {
      var n = Number(v);
      if (!isFinite(n)) return null;
      return Number(n.toFixed(6));
    }

    function normalizeAddress(v) {
      if (v === undefined || v === null) return '';
      return String(v).trim().replace(/\s+/g, ' ').toLowerCase();
    }

    function makeTagKeyByCoordAddress(t) {
      var lat = normalizeCoord(t.latitude);
      var lng = normalizeCoord(t.longitude);
      if (lat === null || lng === null) return null;
      var addr = normalizeAddress(t.address_text);
      return lat + '|' + lng + '|' + addr;
    }

    function dedupeTags(rows) {
      var byId = {};
      var byCoordAddress = {};
      var out = [];
      for (var i = 0; i < rows.length; i++) {
        var t = rows[i];
        var id = Number(t.id || 0);
        if (id > 0) {
          if (byId[id]) continue;
          byId[id] = true;
        }
        var key = makeTagKeyByCoordAddress(t);
        if (key) {
          if (byCoordAddress[key]) continue;
          byCoordAddress[key] = true;
        }
        out.push(t);
      }
      return out;
    }
    async function loadMapTags() {
      if (idVille <= 0) return;
      if (isPopupOpen()) return;
      const url = "<?php echo ci3_url('ville/maptag/list'); ?>?idville=" + idVille + "&open=1&_ts=" + Date.now();
      try {
        const r = await fetch(url);
        const j = await r.json();
        if (!j || !j.ok || !Array.isArray(j.data)) return;
        tagCluster.clearLayers();
        tagMarkersById = {};
        dedupeTags(j.data).forEach(function(t) {
          upsertTagMarker(t);
        });
      } catch (e) {
        console.warn('loadMapTags error', e);
      }
    }

    /******************** GÉOCODAGE *************************/
    async function geocodeAddress(q) {
      const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q) + '&countrycodes=ca';
      const r = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'User-Agent': 'RDF-Map/1.0'
        }
      });
      if (!r.ok) throw new Error('Geocoding HTTP ' + r.status);
      const j = await r.json();
      if (!Array.isArray(j) || j.length === 0) throw new Error('Adresse introuvable');
      return {
        lat: parseFloat(j[0].lat),
        lng: parseFloat(j[0].lon),
        name: j[0].display_name || q
      };
    }

    /******************** CRÉATION TAG *************************/
    async function openCreateTagForm(addressText, presetLat, presetLng) {
      const q = (addressText || '').trim();
      if (!q) {
        alert('Entrez une adresse.');
        return;
      }
      try {
        var targetLat = Number(presetLat);
        var targetLng = Number(presetLng);
        if (!isFinite(targetLat) || !isFinite(targetLng)) {
          const pos = await geocodeAddress(q);
          targetLat = pos.lat;
          targetLng = pos.lng;
        }
        if (isNaN(targetLat) || isNaN(targetLng)) throw new Error('Coordonnées invalides');
        if (!zoneContains(targetLat, targetLng)) {
          if (!confirm("Point hors polygone. Continuer ?")) return;
        }
        var formId = 'frmTag_' + Date.now();
        if (!tagReasonsLoaded) await loadTagReasons();
        var optionsHtml = TAG_REASONS.map(r => '<option value="' + escapeHtml(r) + '">' + escapeHtml(r) + '</option>').join('');
        var html =
          '<div style="min-width:320px;max-width:600px;">' +
          '<div style="font-weight:600;margin-bottom:6px;">Créer un tag</div>' +
          '<form id="' + formId + '" enctype="multipart/form-data" onsubmit="return false;">' +
          '<div class="mb-2"><b>Adresse:</b><br/><input type="text" name="address_text" value="' + escapeHtml(q) + '" class="form-control form-control-sm" /></div>' +
          '<div class="mb-2"><b>Raison:</b><br/><select name="reason" class="form-select form-select-sm">' + optionsHtml + '</select></div>' +
          '<div class="mb-2"><b>Description:</b><br/><textarea name="description" rows="3" class="form-control form-control-sm"></textarea></div>' +
          '<div class="mb-2"><b>Image:</b><br/><input type="file" name="image" accept="image/*" class="form-control form-control-sm" /></div>' +
          '<div style="display:flex;gap:8px;justify-content:flex-end;">' +
          '<button type="button" id="btnSendTag" class="btn btn-primary btn-sm">Envoyer</button>' +
          '</div>' +
          '</form>' +
          '</div>';
        var pop = L.popup({
          maxWidth: 640,
          className: 'wide-popup'
        }).setLatLng([targetLat, targetLng]).setContent(html).openOn(map);
        setTimeout(function() {
          var btn = document.getElementById('btnSendTag');
          if (!btn) return;
          btn.onclick = async function() {
            if (btn.dataset.submitting === '1') return;
            btn.dataset.submitting = '1';
            var originalLabel = btn.textContent;
            btn.disabled = true;
            btn.textContent = (lang === 'fr' ? 'Envoi...' : 'Sending...');
            try {
              var fe = document.getElementById(formId);
              if (!fe) return;
              var fd = new FormData(fe);
              fd.append('id_ville', String(idVille));
              fd.append('lat', String(targetLat));
              fd.append('lng', String(targetLng));
              const r = await fetch("<?php echo ci3_url('ville/maptag/create'); ?>", {
                method: 'POST',
                body: fd
              });
              const j = await r.json();
              if (!j || !j.ok) {
                alert('Erreur: ' + (j && j.error ? j.error : ''));
                btn.disabled = false;
                btn.textContent = originalLabel;
                btn.dataset.submitting = '0';
                return;
              }
              upsertTagMarker(j.data);
              map.closePopup(pop);
            } catch (err) {
              alert('Erreur envoi: ' + (err && err.message ? err.message : err));
              btn.disabled = false;
              btn.textContent = originalLabel;
              btn.dataset.submitting = '0';
            }
          };
        }, 40);
      } catch (e) {
        alert(e.message || 'Erreur géocodage');
      }
    }

    document.getElementById('btnCreateTag').addEventListener('click', function() {
      openCreateTagForm(document.getElementById('edtFind').value || '');
    });

    /******************** RAFRAÎCHIR *************************/
    document.getElementById('btnRefresh').addEventListener('click', function() {
      clearRenderedRoutes();
      Promise.resolve()
        .then(loadRamassages)
        .then(loadCityPolylines)
        .then(loadGpsCount)
        .then(function() {
          reapplyTruckFilter();
          return refreshLive();
        })
        .then(function() {
          renderCollecteMarkers();
          loadMapTags();
        });
    });

    /******************** INITIALISATION *************************/
    function init() {
      Promise.resolve()
        .then(function() {
          return loadSecteursByVille().catch(function(err) {
            console.warn('Secteurs non chargés:', err);
            // Continuer même si le chargement des secteurs échoue
            return Promise.resolve();
          });
        })
        .then(loadRamassages)
        .then(loadCityPolylines)
        .then(loadGpsCount)
        .then(function() {
          refreshLive();
          loadMapTags();
          if (liveTimer) clearInterval(liveTimer);
          if (isTodayView) {
            liveTimer = setInterval(function() {
              refreshLive();
            }, 8000);
          }
        })
        .catch(function(err) {
          console.error('Erreur lors de l\'initialisation:', err);
        });
    }
    init();

    (function autoFitIfOnlyCollecte() {
      var anyPolyline = false;
      Object.keys(trucksByCam).forEach(function(k) {
        if (trucksByCam[k].polylines.length > 0) anyPolyline = true;
      });
      if (anyPolyline) return;
      var pts = collectePoints.filter(p => p.latitude && p.longitude).map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]);
      if (pts.length > 0) {
        try {
          var b = L.latLngBounds(pts);
          map.fitBounds(b, {
            padding: [20, 20]
          });
        } catch (e) {}
      }
    })();

    /******************** CHANGEMENT DATE *************************/
    $('#dtPicker').on('change', function() {
      var d = (this.value || '').trim();
      if (!d) return;
      var qs = '?dte=' + encodeURIComponent(d) +
        (idVille > 0 ? ('&idville=' + encodeURIComponent(idVille)) : '') +
        '&source=' + encodeURIComponent(sourceMode) +
        '&zonemode=' + encodeURIComponent(zoneTraceMode) +
        '&showpoly=' + (displayZones ? '1' : '0') +
        '&embed=' + (isEmbed ? '1' : '0');
      window.location.href = '/ville/nav_zone/' + encodeURIComponent(lang) + qs;
    });

    /******************** MODE EMBED *************************/
    if (isEmbed) {
      try {
        document.querySelector('.toolbar').style.display = 'none';
        document.getElementById('map').style.left = '0';
        document.getElementById('map').style.top = '0';
        // Cacher les boutons flottants en mode embed (iframe du hub)
        var _btnTT = document.getElementById('btnToggleToolbar');
        var _btnBk = document.getElementById('btnBack');
        if (_btnTT) _btnTT.style.display = 'none';
        if (_btnBk) _btnBk.style.display = 'none';
        // Recalcule la taille de la carte
        setTimeout(function() {
          try {
            map.invalidateSize();
            console.log('Mode embed activé - toolbar masquée');
          } catch (e) {
            console.error('Erreur invalidateSize:', e);
          }
        }, 100);
      } catch (e) {
        console.error('Erreur mode embed:', e);
      }
    }

    /******************** TOGGLE ADDRESS CIRCLES (postMessage et bouton) **********/
    // bouton local (standalone)
    document.getElementById('btnToggleAddressCircles').addEventListener('click', function() {
      window.addressCirclesGlobalToggleActive = !window.addressCirclesGlobalToggleActive;
      this.textContent = window.addressCirclesGlobalToggleActive ? 'Masquer adresses' : 'Afficher adresses';
      if (window.addressCirclesGlobalToggleActive) {
        // Afficher pour les camions visibles (respecte le filtre actif)
        Object.keys(trucksByCam).forEach(function(k) {
          try {
            if (_activeTruckFilter !== null && _activeTruckFilter !== k) return;
            if (trucksByCam[k] && Array.isArray(trucksByCam[k].points) && trucksByCam[k].points.length > 0) showAddressCirclesForCam(k);
          } catch (e) {}
        });
      } else {
        hideAllAddressCircles();
      }
    });

    // réception des commandes du parent (hub) via postMessage
    window.addEventListener('message', function(e) {
      try {
        // Optionnel: whitelist origin if needed, e.g. if(e.origin !== window.location.origin) return;
        var data = e.data || {};
        if (data && data.type === 'TOGGLE_ADDRESS_CIRCLES') {
          var active = !!data.active;
          window.addressCirclesGlobalToggleActive = active;
          // Mettre à jour UI si visible
          var btn = document.getElementById('btnToggleAddressCircles');
          if (btn) btn.textContent = active ? 'Masquer adresses' : 'Afficher adresses';
          if (active) {
            Object.keys(trucksByCam).forEach(function(k) {
              try {
                if (_activeTruckFilter !== null && _activeTruckFilter !== k) return;
                if (trucksByCam[k] && Array.isArray(trucksByCam[k].points) && trucksByCam[k].points.length > 0) showAddressCirclesForCam(k);
              } catch (e) {}
            });
          } else {
            hideAllAddressCircles();
          }
        }
        // Commande de filtre camion reçue du hub parent
        if (data && data.type === 'FILTER_TRUCK') {
          var cam = (data.cam === null || data.cam === undefined) ? null : String(data.cam);
          filterTruckPolylines(cam);
        }

        // Force le redimensionnement de la carte (depuis le hub parent)
        if (data && data.type === 'FORCE_RESIZE') {
          try {
            if (typeof map !== 'undefined' && map.invalidateSize) map.invalidateSize(true);
          } catch (e) {}
        }

        /* ═══ Messages live depuis le hub admin (C# → shim TruckMap → iframe) ═══ */
        /* Sécurité : seuls les admins traitent ces messages. Les utilisateurs ville
           (userType=ville) ignorent ces messages même si un parent les envoie. */
        if (!isAdmin) return;

        // Position live du camion admin
        if (data && data.type === 'LIVE_POSITION') {
          var liveCam = normCamKey(data.cam || '');
          var liveLat = Number(data.lat),
            liveLng = Number(data.lng);
          var liveHeading = Number(data.heading);
          if (!liveCam || !isFinite(liveLat) || !isFinite(liveLng)) return;
          if (liveLat === 0 && liveLng === 0) return;
          ensureCam(liveCam);
          var liveVisible = (_activeTruckFilter === null || _activeTruckFilter === liveCam);

          // ① Calculer le heading à partir de l'historique des positions.
          //    On utilise une moyenne mobile sur les dernières positions pour
          //    éliminer le bruit GPS. On garde un buffer des positions récentes.
          if (!window._posHistory) window._posHistory = [];
          window._posHistory.push([liveLat, liveLng]);
          if (window._posHistory.length > 6) window._posHistory.shift();

          var calcHeading = 0;
          // Calculer le cap entre le point le plus ancien et le plus récent
          // du buffer (plus stable que point-à-point)
          if (window._posHistory.length >= 3) {
            var p0 = window._posHistory[0];
            var pN = window._posHistory[window._posHistory.length - 1];
            var dLat = pN[0] - p0[0];
            var dLng = pN[1] - p0[1];
            var dist = Math.sqrt(dLat * dLat + dLng * dLng);
            if (dist > 0.0001) { // ~10m minimum sur la distance totale
              var φ1 = p0[0] * Math.PI / 180;
              var φ2 = pN[0] * Math.PI / 180;
              var Δλ = (pN[1] - p0[1]) * Math.PI / 180;
              var y = Math.sin(Δλ) * Math.cos(φ2);
              var x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
              calcHeading = Math.atan2(y, x) * 180 / Math.PI;
              if (calcHeading < 0) calcHeading += 360;
            }
          }
          // Utiliser le heading calculé si Samsara renvoie 0
          if (liveHeading === 0 && calcHeading !== 0) {
            liveHeading = calcHeading;
          }

          // ② Lissage du heading : interpolation douce vers le nouveau cap.
          //    On ne change que de 10° max par update pour éviter les oscillations.
          if (_lastTruckHeading && liveHeading) {
            var diff = liveHeading - _lastTruckHeading;
            while (diff > 180) diff -= 360;
            while (diff < -180) diff += 360;
            // Ignorer les petits changements (< 15°) = dead zone
            if (Math.abs(diff) < 15) {
              liveHeading = _lastTruckHeading; // garder l'ancien cap
            } else {
              // Limiter le changement à 10° par update
              var maxChange = 10;
              liveHeading = _lastTruckHeading + (diff > 0 ? Math.min(maxChange, diff) : Math.max(-maxChange, diff));
              if (liveHeading < 0) liveHeading += 360;
              if (liveHeading >= 360) liveHeading -= 360;
            }
          }

          // Mémoriser la position et le cap du camion admin
          _lastTruckLatLng = L.latLng(liveLat, liveLng);
          if (typeof liveHeading === 'number' && isFinite(liveHeading) && liveHeading !== 0) {
            _lastTruckHeading = liveHeading;
          }

          // ① Mettre à jour le marqueur du camion avec icône directionnelle
          var dirIcon = createDirectionalTruckIcon(_lastTruckHeading, getColorForCam(liveCam));
          if (trucksByCam[liveCam].marker) {
            try {
              trucksByCam[liveCam].marker.setLatLng([liveLat, liveLng]);
              trucksByCam[liveCam].marker.setIcon(dirIcon);
              try {
                trucksByCam[liveCam].marker.bringToFront();
              } catch (e) {}
              if (liveVisible && !map.hasLayer(trucksByCam[liveCam].marker)) trucksByCam[liveCam].marker.addTo(map);
              else if (!liveVisible && map.hasLayer(trucksByCam[liveCam].marker)) map.removeLayer(trucksByCam[liveCam].marker);
            } catch (e) {}
          } else {
            trucksByCam[liveCam].marker = L.marker([liveLat, liveLng], {
              icon: dirIcon,
              title: 'Camion ' + liveCam,
              zIndexOffset: 1000
            });
            if (liveVisible) trucksByCam[liveCam].marker.addTo(map);
          }

          // ② Suivi du camion : centrer avec panTo (préserve le bearing)
          if (_followTruck) {
            try {
              var curZoom = map.getZoom() || _followZoom;
              var zoomToUse = window._userZoomLocked ? curZoom : (_followZoom || curZoom);
              if (map.getZoom() !== zoomToUse) {
                try {
                  map.setZoom(zoomToUse, {
                    animate: false
                  });
                } catch (eZ) {}
              }
              map.panTo(_lastTruckLatLng, {
                animate: false
              });
            } catch (e) {
              try {
                map.setView(_lastTruckLatLng, _followZoom, {
                  animate: false
                });
              } catch (e2) {}
            }
          }

          // ③ Rotation de la carte (setBearing ne conflict pas avec panTo)
          //    setBearing(-heading) : 0=Nord→haut, 90=Est→haut, 180=Sud→haut
          if (_autoBearing && typeof liveHeading === 'number' && isFinite(liveHeading)) {
            _desiredBearing = -liveHeading;
            try {
              if (typeof map.setBearing === 'function') {
                map.setBearing(_desiredBearing);
              }
            } catch (e) {}
          }

          // Si le traçage dynamique est actif pour ce camion, étendre la polyline live
          if (_liveDrawActive && _liveDrawCam === liveCam && !data.positionOnly) {
            if (!_livePolyline) {
              _liveCoords = [
                [liveLat, liveLng]
              ];
              _livePolyline = L.polyline(_liveCoords, {
                color: getColorForCam(liveCam),
                weight: 5,
                opacity: 0.9
              }).addTo(map);
            } else {
              // Éviter les doublons consécutifs
              var last = _liveCoords[_liveCoords.length - 1];
              if (!last || last[0] !== liveLat || last[1] !== liveLng) {
                _liveCoords.push([liveLat, liveLng]);
                _livePolyline.setLatLngs(_liveCoords);
              }
            }
          }
        }

        // Activer/désactiver le suivi automatique du cap (bearing)
        if (data && data.type === 'SET_AUTO_BEARING') {
          _autoBearing = !!data.enabled;
          // Synchroniser la case à cocher de la toolbar
          var chkAB = document.getElementById('chkAutoBearing');
          if (chkAB) chkAB.checked = _autoBearing;
          if (!_autoBearing) {
            _desiredBearing = 0;
            try {
              // Arrêter le mode heading-up et remettre le nord en haut
              if (typeof map.stopHeadingUp === 'function') map.stopHeadingUp();
              if (typeof map.setBearing === 'function') map.setBearing(0);
            } catch (e) {}
          } else {
            // Appliquer immédiatement le dernier cap connu
            if (_lastTruckHeading && typeof map.setBearing === 'function') {
              _desiredBearing = -_lastTruckHeading;
              try {
                map.setBearing(_desiredBearing);
              } catch (e) {}
            }
          }
        }

        // Définir le bearing de la carte manuellement
        if (data && data.type === 'SET_BEARING') {
          try {
            if (typeof map.setBearing === 'function') {
              var b = Number(data.deg);
              if (!isFinite(b)) b = 0;
              map.setBearing(b);
            }
          } catch (e) {}
        }

        // Centrer la carte sur le camion admin
        if (data && data.type === 'CENTER_ON_TRUCK') {
          // Ignoré pendant l'inspection d'une polyline cédule
          if (Object.keys(_cedulePolylines).length > 0) return;
          recenterAdminTruck(true);
        }

        // Activer/désactiver le traçage dynamique pour le camion admin
        if (data && data.type === 'DRAW_POLYLINE') {
          var drawCam = normCamKey(data.cam || '');
          var enabled = !!data.enabled;
          _liveDrawActive = enabled;
          _liveDrawCam = drawCam;
          if (!enabled) {
            // Arrêter le traçage : figer la polyline live (la garder visible)
            if (_livePolyline) {
              try {
                _livePolyline.setStyle({
                  opacity: 0.6
                });
              } catch (e) {}
            }
            // La polyline live sera rechargée depuis le serveur au prochain refresh
            _livePolyline = null;
            _liveCoords = [];
          } else {
            // Démarrer un nouveau segment
            _livePolyline = null;
            _liveCoords = [];
          }
        }

        // Vider la polyline dynamique
        if (data && data.type === 'CLEAR_DYNAMIC_POLYLINE') {
          if (_livePolyline) {
            try {
              map.removeLayer(_livePolyline);
            } catch (e) {}
            _livePolyline = null;
          }
          _liveCoords = [];
          _liveDrawActive = false;
        }

        // Injection de points historiques
        if (data && data.type === 'INJECT_HISTORY_POINTS') {
          var histPts = data.points || [];
          var histCam = normCamKey(data.cam || noCamionFromUrl || '');
          if (histCam && Array.isArray(histPts) && histPts.length > 0) {
            ensureCam(histCam);
            var histLatLngs = [];
            histPts.forEach(function(pt) {
              if (Array.isArray(pt) && pt.length >= 2) {
                var hl = Number(pt[0]),
                  hg = Number(pt[1]);
                if (isFinite(hl) && isFinite(hg) && !(hl === 0 && hg === 0)) histLatLngs.push([hl, hg]);
              }
            });
            if (histLatLngs.length >= 2) {
              var histPl = L.polyline(histLatLngs, {
                color: getColorForCam(histCam),
                weight: 4,
                opacity: 0.8,
                dashArray: '8,4'
              });
              var histVisible = (_activeTruckFilter === null || _activeTruckFilter === histCam);
              if (histVisible) histPl.addTo(map);
              trucksByCam[histCam].polylines.push(histPl);
            }
          }
        }

        /* ═══ Boutons de polylines de cédule (depuis le hub admin) ═══ */
        if (data && data.type === 'SET_POLYLINE_BUTTONS') {
          var buttons = data.buttons || [];
          if (!Array.isArray(buttons) || buttons.length === 0) return;
          buildCedulePolylinePanel(buttons);
        }

        /* Afficher une polyline de cédule spécifique par son ID */
        if (data && data.type === 'SHOW_POLYLINE_BY_ID') {
          var polyId = data.id;
          var polyColor = data.color || '#FF0000';
          if (polyId) showCedulePolylineById(polyId, polyColor);
        }

        /* Suivi automatique du camion */
        if (data && data.type === 'SET_FOLLOW_TRUCK') {
          // Ignorer la réactivation du suivi pendant l'inspection d'une polyline cédule
          if (data.follow && Object.keys(_cedulePolylines).length > 0) return;
          _followTruck = !!data.follow;
          _followZoom = data.zoom || 17;
          if (_followTruck && _lastTruckLatLng) {
            recenterAdminTruck(true);
          }
        }
      } catch (err) {
        console.warn('message handler error', err);
      }
    });

    /* ═══ Panneau des polylines de cédule (similaire à truckmap.html) ═══ */
    var _cedulePolylinePanel = null;
    var _cedulePolylines = {}; // id → L.polyline
    var _ceduleLoading = {}; // id → fetch en cours (évite doublons sur clic rapide)
    var _ceduleButtonsData = [];

    function buildCedulePolylinePanel(buttons) {
      _ceduleButtonsData = buttons;
      // Supprimer l'ancien panneau
      if (_cedulePolylinePanel) {
        try {
          _cedulePolylinePanel.remove();
        } catch (e) {}
        _cedulePolylinePanel = null;
      }
      if (!buttons || buttons.length === 0) return;

      // Créer le panneau
      _cedulePolylinePanel = document.createElement('div');
      _cedulePolylinePanel.className = 'cedule-polyline-panel';
      _cedulePolylinePanel.innerHTML = '<div class="cedule-polyline-title">Parcours assignés</div>';
      var btnContainer = document.createElement('div');
      btnContainer.className = 'cedule-polyline-buttons';

      buttons.forEach(function(btn) {
        var el = document.createElement('button');
        // Restaurer l'état actif si la polyline est déjà affichée (panneau reconstruit)
        el.className = 'cedule-polyline-btn' + (_cedulePolylines[btn.id] ? ' active' : '');
        el.setAttribute('data-poly-id', btn.id);
        el.setAttribute('data-poly-color', btn.color || '#FF0000');
        el.innerHTML = '<span class="cedule-poly-color" style="background:' + (btn.color || '#FF0000') + ';"></span>' +
          '<span class="cedule-poly-label">' + escapeHtml(btn.label || ('Secteur ' + btn.ordre)) + '</span>';
        el.addEventListener('click', function() {
          // Anti double-tap (les WebViews tactiles peuvent doubler le clic)
          var now = Date.now();
          if (el._lastToggle && now - el._lastToggle < 350) return;
          el._lastToggle = now;
          var id = el.getAttribute('data-poly-id');
          var color = el.getAttribute('data-poly-color');
          // Toggle : si déjà visible, cacher; sinon afficher
          if (_cedulePolylines[id]) {
            try {
              map.removeLayer(_cedulePolylines[id]);
            } catch (e) {}
            delete _cedulePolylines[id];
            el.classList.remove('active');
          } else {
            showCedulePolylineById(id, color);
            el.classList.add('active');
          }
        });
        btnContainer.appendChild(el);
      });

      _cedulePolylinePanel.appendChild(btnContainer);
      document.body.appendChild(_cedulePolylinePanel);
    }

    function showCedulePolylineById(id, color) {
      if (!id) return;
      // Si déjà chargée, juste l'afficher
      if (_cedulePolylines[id]) {
        try {
          if (!map.hasLayer(_cedulePolylines[id])) _cedulePolylines[id].addTo(map);
        } catch (e) {}
        return;
      }
      // Éviter les fetchs concurrents pour le même id (double-clic / message répété)
      if (_ceduleLoading[id]) return;
      _ceduleLoading[id] = true;
      // Charger depuis le serveur (action=35 = GET_POLYLINE)
      var url = '<?php echo ci3_url("sanivan/get"); ?>?action=35&idpolyline=' + encodeURIComponent(id);
      fetch(url).then(function(r) {
        return r.text();
      }).then(function(txt) {
        delete _ceduleLoading[id];
        if (!txt || txt.trim() === '""' || txt.trim() === '') return;
        if (_cedulePolylines[id]) return; // créée entre-temps par un autre appel
        txt = txt.trim().replace(/^"|"$/g, '');
        // Parser les coordonnées (format: lat,lng_lat,lng;... ou lat,lng|lat,lng|...)
        var pts = [];
        var pairs = txt.split(/[_;| ]/);
        pairs.forEach(function(pair) {
          var parts = pair.split(',');
          if (parts.length >= 2) {
            var lat = parseFloat(parts[0].trim().replace(',', '.'));
            var lng = parseFloat(parts[1].trim().replace(',', '.'));
            if (isFinite(lat) && isFinite(lng) && lat !== 0 && lng !== 0 && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
              pts.push([lat, lng]);
            }
          }
        });
        if (pts.length < 2) {
          console.warn('showCedulePolylineById: pas assez de points pour id=' + id);
          return;
        }
        var pl = L.polyline(pts, {
          color: color || '#FF0000',
          weight: 5,
          opacity: 0.85
        }).addTo(map);
        // La rouge doit rester SOUS les tracés camion : envoyer au fond,
        // puis renvoyer les zones encore plus bas pour qu'elle reste au-dessus d'elles
        try {
          pl.bringToBack();
          zoneLayers.forEach(function(z) {
            try {
              z.bringToBack();
            } catch (e) {}
          });
        } catch (e) {}
        _cedulePolylines[id] = pl;

        // Suspendre le suivi camion pendant l'inspection du parcours assigné,
        // sinon la prochaine position live ramène la vue sur le camion admin
        // et la polyline semble « disparaître » aussitôt affichée
        _followTruck = false;

        // Ajuster la vue pour voir la polyline
        try {
          map.fitBounds(pl.getBounds(), {
            padding: [30, 30],
            maxZoom: 16
          });
        } catch (e) {}
      }).catch(function(e) {
        delete _ceduleLoading[id];
        console.warn('showCedulePolylineById fetch error', e);
      });
    }

    window.addEventListener('beforeunload', function() {
      if (liveTimer) clearInterval(liveTimer);
    });

    /******************** TOGGLE TOOLBAR MOBILE *************************/
    (function() {
      var btn = document.getElementById('btnToggleToolbar');
      var tb = document.querySelector('.toolbar');
      if (!btn || !tb) return;
      btn.addEventListener('click', function() {
        tb.classList.toggle('toolbar-open');
        btn.innerHTML = tb.classList.contains('toolbar-open') ? '&#10005;' : '&#9776;';
      });
      document.addEventListener('click', function(e) {
        if (window.innerWidth > 768) return;
        if (!tb.classList.contains('toolbar-open')) return;
        if (tb.contains(e.target) || btn.contains(e.target)) return;
        tb.classList.remove('toolbar-open');
        btn.innerHTML = '&#9776;';
      });
    })();
  </script>
</body>

</html>