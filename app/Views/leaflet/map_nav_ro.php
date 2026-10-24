<?php


// Normalisation des données injectées
$initialBoites = [];
if (isset($boites)) {
    $initialBoites = $boites;
} elseif (isset($rows)) {
    $initialBoites = $rows;
} elseif (isset($row)) {
    // $row peut être une ligne associative ou déjà un tableau de lignes
    $initialBoites = (isset($row['NoBoite']) ? [$row] : (array) $row);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="description" lang="fr" content=" ">
  <title>Robert Daoust</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    html, body { height: 100%; overflow: hidden; }
    #divShowCed { position: fixed; top: 0; left: 260px; right: 0; bottom: 0; }
    #map { width: 100%; height: 100%; }
    #divBoites > div { max-height: 100%; overflow: auto; }
    .box-label { display: block; font-size: 14px; font-weight: 600; background: #f5f5f5; color: #333; padding: 6px 8px; margin-bottom: 6px; border: 1px solid #ddd; cursor: pointer; border-radius: 4px; }
    .box-label:hover { background: #e9e9e9; }

    /* Marqueur composite: icône + anneau rouge animé pour les retards */
    .box-marker { position: relative; width: 48px; height: 48px; }
    .box-marker img { position: absolute; left: 4px; top: 4px; width: 40px; height: 40px; display: block; }
    .box-marker .ring {
      position: absolute;
      left: 0; top: 0;
      width: 48px; height: 48px;
      border: 3px solid red;
      border-radius: 50%;
      box-shadow: 0 0 8px rgba(255,0,0,0.9);
      animation: ringPulse 1.2s ease-in-out infinite;
      pointer-events: none;
    }
    @keyframes ringPulse {
      0%   { transform: scale(1);   opacity: .9; }
      50%  { transform: scale(1.10); opacity: .6; }
      100% { transform: scale(1);   opacity: .9; }
    }
  </style>
</head>
<body id="page">

  <div class="row">
    <div style="display: block; position: fixed; left: 10px; top: 10px; height: 29vh; width: 210px; border: 2px red solid; padding:10px;">
      <label style="font-size:24px;font-weight:600;min-width:220px">LOCALISATION</label>
      <label>Date
        <input type="date" style="width:160px" class="form-control" onchange="onDateChange(this.value)" value="<?php echo isset($ActiveDate) ? $ActiveDate : date('Y-m-d'); ?>" id="dtedeb" />
      </label>
      <br /><br />
      <label>Types de boîte <br />
        <select id="selContaineurs" name="selContaineurs" onchange="reloadBoites()" class="form-control" style="width:160px;">
          <?php echo isset($selTypeBoites) ? $selTypeBoites : '<option value="tous">Tous</option>'; ?>
        </select>
      </label>
      <br /> 
      <label>Storages des boîtes <br />
        <select id="selStorages" name="selStorages" onchange="reloadStorage()" class="form-control" style="width:160px;">
          <option value="">Choisir</option>
          <option value="tous">Tous</option>            
          <option value="CIE MELOCHE KIRKLAND">Cie Meloche Kirkland</option>
          <option value="COUR (2117 HARWOOD)">Cours 2117 Hardwood</option>
          <option value="MARIO HART">Mario Hart</option>
          <option value="RABY SANDBLAST">Raby Sandblast</option>
          <option value="Sabliere">SABLIERE (VIDE)</option>
        </select>
      </label>
      <br />
      <!-- Filtre retards -->
      <div class="checkbox">
        <label>
          <input type="checkbox" id="chkOverdueOnly" onchange="onToggleOverdue()"> Afficher les retards
        </label>
      </div>
    </div>

    <div id="divRecherche" class="border border-secondary" 
         style="display: block; position: fixed; left: 10px; top: 320px; height: 120px; width: 210px; border: 2px #555 solid;padding:12px;">
      <label style="width:210px;font-size:18px;font-weight:bold">Rechercher une adresse</label>
      <div class="input-group" id="divFindBox">
        <input type="text" class="form-control" id="edtFindBox" placeholder="no, rue, ville" value="" />
        <span class="input-group-addon">
          <a href="#" onclick="findLocation()" style="width:35px;height:33px">
            <i class="fa fa-search" style="font-weight:bold;font-size:18px"></i>
          </a>
        </span>
      </div>
    </div>

    <div id="divBoites" class="border border-secondary" 
         style="display: block; position: fixed; left: 10px; top: 450px; bottom: 10px; width: 210px; border: 2px #555 solid; padding:12px;">
      <div id="listBoites"></div>
    </div>

    <div id="divShowCed">
      <div id="map"></div>
    </div>
  </div>

  <script>
    // Données injectées côté serveur (si présentes)
    var initialBoites = <?php echo json_encode($initialBoites, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    var map;
    var markersByBox = {};
    var layerGroup = null;

    // Etat filtre "retards seulement"
    var overdueOnly = false;
    // Dernier dataset chargé (pour re-render sans recharger côté serveur)
    var lastItems = [];

    function initMap() {
      var defaultLoc = [45.3991735, -74.0780621];
      map = L.map('map').setView(defaultLoc, 14, { animate: true, duration: 1.0 });
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
          maxZoom: 19
        }).addTo(map);
      layerGroup = L.layerGroup().addTo(map);

      setTimeout(function(){ map.invalidateSize(false); }, 0);
      window.addEventListener('resize', function(){ map.invalidateSize(false); });

      // Rendu initial
      if (Array.isArray(initialBoites) && initialBoites.length > 0) {
        lastItems = initialBoites;
        renderBoites(lastItems);
      } else {
        reloadBoites();
      }
    }

    function onDateChange(dte) {
      document.getElementById('dtedeb').value = dte;
      reloadBoites();
    }

    function onToggleOverdue() {
      overdueOnly = document.getElementById('chkOverdueOnly').checked;
      // Re-rendu instantané à partir du cache
      renderBoites(lastItems, true);
    }

    function getSelectedType() {
      var sel = document.getElementById('selContaineurs');
      return sel ? sel.value : 'tous';
    }
    function getSelectedStorage() {
      var sel = document.getElementById('selStorages');
      return sel ? sel.value : '';
    }

    function isOverdue(dateStr) {
      if (!dateStr) return false;
      var ds = String(dateStr).split(' ')[0];
      var parts = ds.split('-');
      if (parts.length < 3) return false;
      var d = new Date(parseInt(parts[0],10), parseInt(parts[1],10)-1, parseInt(parts[2],10));
      if (isNaN(d.getTime())) return false;
      var today = new Date();
      today.setHours(0,0,0,0);
      return d < today;
    }

    function createBoxIcon(noBoite, showRing) {
      var url = '/assets/img/boites/' + String(noBoite).trim() + '.png';
      if (showRing) {
        // Icône composite avec anneau animé (halo rouge)
        return L.divIcon({
          html: '<div class="box-marker"><img src="'+url+'" alt="boite '+noBoite+'"><span class="ring"></span></div>',
          className: '',
          iconSize: [48, 48],
          iconAnchor: [24, 36],
          popupAnchor: [0, -32]
        });
      }
      // Icône normale
      return L.icon({
        iconUrl: url,
        iconSize: [40, 40],
        iconAnchor: [20, 30],
        popupAnchor: [0, -28]
      });
    }

    function clearAll() {
      if (layerGroup) { layerGroup.clearLayers(); }
      markersByBox = {};
      document.getElementById('listBoites').innerHTML = '';
    }

    // Convertit un décalage (dx, dy) en mètres en delta lat/lng en degrés
    function offsetLatLng(lat, lng, meters, angleRad) {
      var dLat = (meters * Math.sin(angleRad)) / 111320;
      var dLng = (meters * Math.cos(angleRad)) / (111320 * Math.cos(lat * Math.PI / 180));
      return [lat + dLat, lng + dLng];
    }

    // Détecter le mode Storage (sélecteur non vide)
    function isStorageMode() {
      var v = $('#selStorages').val();
      return (v && v !== 'tous');
    }

    // Regroupe n points autour (lat,lng) – écart en mètres calculé une fois au rendu
    function computeAdjustedPositionsForGroup(lat, lng, n, baseMeters) {
      if (n <= 1) return [[lat, lng]];
      var positions = [];
      var ringCap = 12; // points par anneau
      for (var i = 0; i < n; i++) {
        var ring = Math.floor(i / ringCap);
        var idxInRing = i % ringCap;
        var pointsInThisRing = Math.min(n - ring * ringCap, ringCap);
        var angle = (idxInRing / pointsInThisRing) * 2 * Math.PI;
        var dist = baseMeters * (ring + 1);
        positions.push(offsetLatLng(lat, lng, dist, angle));
      }
      return positions;
    }

    function reloadBoites() {
      clearAll();
      // Si un storage est sélectionné, le remettre sur "Choisir"
      var $stor = $('#selStorages');
      if ($stor.length && $stor.val() !== '') {
        $stor.val('');
      }
      var params = { type: getSelectedType() };
      $.getJSON('/rolloff/boitesmap', params)
        .done(function(res) { 
          if (Array.isArray(res)) { lastItems = res; renderBoites(lastItems); }
        })
        .fail(function(xhr) { console.error('Erreur boitesmap:', xhr.responseText || xhr.statusText); });
    }

    function reloadStorage() {
      clearAll();
      // Si un type de boîte est sélectionné, le remettre sur "Choisir"
      var $types = $('#selContaineurs');
      if ($types.length && $types.val() !== '') {
        $types.val('');
      }
      var params = { place: getSelectedStorage() };
      $.getJSON('/rolloff/boitesstorage', params)
        .done(function(res) { 
          if (Array.isArray(res)) { lastItems = res; renderBoites(lastItems); }
        })
        .fail(function(xhr) { console.error('Erreur boitesmap:', xhr.responseText || xhr.statusText); });
    }

    // Clé de tri: si NoBoite comporte exactement 2 caractères numériques, préfixer d'un 0
    function sortKeyNoBoite(no) {
      var s = String(no).trim();
      if (/^\d{2}$/.test(s)) return '0' + s;
      return s;
    }

    // Affichage en espaçant légèrement les marqueurs qui partagent la même coordonnée
    function renderBoites(items, skipFitBounds) {
      clearAll();

      // 0) Appliquer le filtre "retards seulement" si actif
      var workingItems = Array.isArray(items) ? items : [];
      if (overdueOnly) {
        workingItems = workingItems.filter(function(b){ return isOverdue(b.date_retour); });
      }

      var bounds = [];

      // 1) Regrouper par coordonnée (arrondies à 6 décimales) pour le rendu carte
      var groups = {};
      workingItems.forEach(function(b) {
        var lat = parseFloat((b.track_lat !== undefined ? b.track_lat : b.lat));
        var lng = parseFloat((b.track_lng !== undefined ? b.track_lng : b.lng));
        if (isNaN(lat) || isNaN(lng)) return;
        var key = lat.toFixed(6) + ',' + lng.toFixed(6);
        if (!groups[key]) groups[key] = [];
        groups[key].push(b);
      });

      // 2) Rendu des marqueurs + collecte pour la liste
      var listMap = {}; // NoBoite -> { NoBoite, TypeBoite, showRing }
      Object.keys(groups).forEach(function(key) {
        var group = groups[key].slice().sort(function(a, b) {
          return String(a.NoBoite).localeCompare(String(b.NoBoite));
        });
        var baseLatLng = key.split(',').map(function(s){ return parseFloat(s); });
        var lat0 = baseLatLng[0], lng0 = baseLatLng[1];

        var baseMeters = isStorageMode() ? 8.5 : 8.0;
        var positions = computeAdjustedPositionsForGroup(lat0, lng0, group.length, baseMeters);

        for (var i = 0; i < group.length; i++) {
          var b = group[i];
          var adj = positions[i];

          var overdue = isOverdue(b.date_retour);
          var showRing = overdueOnly && overdue;
          var icon = createBoxIcon(b.NoBoite, showRing);
          var marker = L.marker(adj, { icon: icon });
          if (showRing) marker.setZIndexOffset(1000);

          var place = b.Place && b.Place.length ? b.Place : (b.LastPlace || '');
          var titre = '<div style="font-size:24px;"><b>Boîte:</b> ' + b.NoBoite
                    + (b.TypeBoite ? ' ('+b.TypeBoite+')' : '')
                    + (showRing ? ' <span style="color:red;font-weight:bold;">(retard)</span>' : '')
                    + '</div>';
          var info  = '<div style="min-width:260px;font-size:18px;">'
                    + '<div><b>Emplacement:</b> ' + (place || 'N/A') + '</div>'
                    + '<div><b>Disponible:</b> ' + ((b.IsDispo === "1" || b.IsDispo === 1) ? 'Oui' : 'Non') + '</div>'
                    + '<div><b>Matériel:</b> ' + ((b.HaveMat === "1" || b.HaveMat === 1) ? 'Oui' : 'Non') + '</div>'
                    + (b.track_time ? '<div><b>MAJ:</b> ' + b.track_time + '</div>' : '')
                    + (b.date_retour ? '<div><b>Date retour:</b> ' + b.date_retour + '</div>' : '')
                    + '</div>'
                    + '<div style="margin-top:8px;"><button class="btn btn-xs btn-primary"'
                  + ' onclick="showCommande(\'' + String(b.NoBoite).replace(/\'/g, "\\'") + '\')">Voir commande</button></div>'
                  + '</div>';
          marker.bindPopup(titre + info).addTo(layerGroup);

          markersByBox[b.NoBoite] = { marker: marker, data: b };
          bounds.push(adj);

          // Collecte unique pour la liste (affichée à gauche)
          if (!listMap.hasOwnProperty(b.NoBoite)) {
            listMap[b.NoBoite] = { NoBoite: b.NoBoite, TypeBoite: b.TypeBoite || '', showRing: showRing };
          }
        }
      });

      // 3) Générer la liste triée par NoBoite, en ajoutant un 0 devant les NoBoite à 2 chiffres pour le tri
      var listArr = Object.keys(listMap).map(function(k){ return listMap[k]; });
      listArr.sort(function(a, b) {
        var ak = sortKeyNoBoite(a.NoBoite);
        var bk = sortKeyNoBoite(b.NoBoite);
        return ak.localeCompare(bk, undefined, { numeric: true, sensitivity: 'base' });
      });

      var listHtml = listArr.map(function(it) {
        return '<a class="box-label" onclick="focusBox(\''+ it.NoBoite +'\')">'
             + '<i class="fa fa-dropbox"></i> ' + it.NoBoite
             + (it.TypeBoite ? ' <span style="color:#888">(' + it.TypeBoite + ')</span>' : '')
             + (it.showRing ? ' <span style="color:red;font-weight:bold;">•</span>' : '')
             + '</a>';
      }).join('');
      document.getElementById('listBoites').innerHTML = listHtml;

      if (!skipFitBounds && bounds.length > 0) {
        if (isStorageMode()) {
          var bb = L.latLngBounds(bounds);
          var center = bb.getCenter();
          var maxZ = (map.getMaxZoom && map.getMaxZoom()) || 19;
          map.setView(center, maxZ, { animate: true });
        } else {
          map.fitBounds(bounds, { padding: [40, 40] });
        }
      }
    }

    function focusBox(noBoite) {
      var rec = markersByBox[noBoite];
      if (!rec) return;
      var ll = rec.marker.getLatLng();
      map.setView(ll, 17);
      rec.marker.openPopup();
    }

    // Recherche d’adresse (géocodage)
    function findLocation() {
      var q = ($('#edtFindBox').val() || '').trim();
      if (!q) return;
      $.getJSON('/sanivan/geocode', { q: q })
        .done(function(res) {
          if (!res || !res.ok) { alert('Adresse introuvable'); return; }
          var latlng = [res.lat, res.lng];
          L.marker(latlng).addTo(map).bindPopup('<b>Adresse</b><br/>' + (res.label || q)).openPopup();
          map.setView(latlng, 18);
        })
        .fail(function(xhr) {
          alert('Erreur géocodage: ' + (xhr.responseText || 'réseau'));
        });
    }

    // Affichage de l'information de la commande
    function showCommande(noBoite) {
      var rec = markersByBox[noBoite];
      if (!rec) return;
      var ll = rec.marker.getLatLng();

      L.popup().setLatLng(ll).setContent('Chargement...').openOn(map);

      $.getJSON('/rolloff/commandeinfo', { noboite: noBoite })
        .done(function(res) {
          if (!res || !res.ok) {
            var msg = (res && res.error) ? res.error : 'Aucune information de commande';
            L.popup().setLatLng(ll).setContent('<b>' + noBoite + '</b><br/>' + msg).openOn(map);
            return;
          }
          L.popup().setLatLng(ll).setContent(res.html).openOn(map);
        })
        .fail(function(xhr) {
          var msg = xhr && xhr.responseText ? xhr.responseText : 'Erreur';
          L.popup().setLatLng(ll).setContent('<b>' + noBoite + '</b><br/>' + msg).openOn(map);
        });
    }

    document.addEventListener("DOMContentLoaded", initMap);
  </script>
</body>
</html>