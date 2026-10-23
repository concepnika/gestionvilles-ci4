<?php


$zonesFeatures = isset($zonesFeatures) ? $zonesFeatures : []; // vide au chargement
$villes = isset($villes) ? $villes : [];

function buildVilleOptions($villes)
{
    $opts = '<option value="">Choisir une ville</option>';
    foreach (($villes ?? []) as $v) {
        $id = '';
        $nom = '';
        if (is_array($v)) {
            if (isset($v['id']))
                $id = $v['id'];
            elseif (isset($v['IdVille']))
                $id = $v['IdVille'];
            elseif (isset($v['Id']))
                $id = $v['Id'];

            if (isset($v['nom']))
                $nom = $v['nom'];
            elseif (isset($v['Nom']))
                $nom = $v['Nom'];
            elseif (isset($v['Ville']))
                $nom = $v['Ville'];
        } else {
            if (isset($v->id))
                $id = $v->id;
            elseif (isset($v->IdVille))
                $id = $v->IdVille;
            elseif (isset($v->Id))
                $id = $v->Id;

            if (isset($v->nom))
                $nom = $v->nom;
            elseif (isset($v->Nom))
                $nom = $v->Nom;
            elseif (isset($v->Ville))
                $nom = $v->Ville;
        }
        if ($id === '' || $nom === '')
            continue;
        $opts .= '<option value="'.htmlspecialchars((string) $id, ENT_QUOTES).'">'.htmlspecialchars((string) $nom, ENT_QUOTES).'</option>';
    }
    return $opts;
}
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Zones</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

  <style>
    html, body { height: 100%; margin: 0; overflow: hidden; }
    #map { position: fixed; top: 0; bottom: 0; left: 0; right: 0; }
    .toolbar {
      position: fixed; z-index: 1000; top: 12px; left: 12px;
      background: rgba(255,255,255,.95); border-radius: 6px; padding: 10px 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,.2); font-family: Arial, sans-serif; font-size: 13px;
      max-width: 430px;
    }
    .toolbar label { display: block; margin-bottom: 6px; }
    .toolbar input[type="text"], .toolbar select { width: 280px; }
    .toolbar .row { display: flex; gap: 8px; align-items: center; margin: 6px 0; flex-wrap: wrap; }
    .hint { color: #555; font-size: 12px; line-height: 1.25; }
    .ok { color: #2e7d32; }
    .err { color: #c62828; }

    /* Poignées d'édition (divIcon) */
    .edit-handle {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #ff4081;
      border: 2px solid #b0003a;
      box-sizing: border-box;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <div class="toolbar">
    <div class="row">
      <label>Ville
        <select id="ddlVille"><?php echo buildVilleOptions($villes); ?></select>
      </label>
    </div>
    <div class="row">
      <input type="text" id="txtZoneName" placeholder="Nom de la zone (optionnel)" />
    </div>
    <div class="row">
      <button id="btnUndo" title="Retirer le dernier point (création)">Annuler dernier</button>
      <button id="btnClear" title="Vider tous les points (création)">Vider</button>
    </div>
    <div class="row">
      <button id="btnAccept" title="Construire le polygone brouillon">Accepter</button>
      <button id="btnSave" title="Enregistrer le polygone">Enregistrer</button>
      <button id="btnEditSave" style="display:none;" title="Sauver les modifications">Sauver modifs</button>
      <button id="btnEditCancel" style="display:none;" title="Annuler les modifications">Annuler modifs</button>
    </div>
    <div class="hint">
      - Sélectionnez une ville pour afficher ses secteurs.<br/>
      - Clic sur un polygone pour activer l'édition (déplacer les sommets).<br/>
      - Maintenez le clic sur le polygone et glissez pour agrandir/réduire globalement.<br/>
      - Création: cliquez sur la carte pour ajouter des points, Accepter puis Enregistrer.
    </div>
    <div id="saveMsg" class="hint"></div>
  </div>

  <script>
    var zonesData = []; // chargé après sélection
    var saveZoneUrl   = '/admin/zones/save';
    var fetchZonesUrl = '/admin/zones/byVille';
    var updateZoneUrl = '/admin/zones/update';

    var map, zoneLayers = [];
    var adminPoints = [], adminMarkers = [], adminLine = null;
    var draftPolygonLayer = null, draftPolygonGeo = null;

    // Edition d’un polygone existant
    var editing = {
      active: false,
      layer: null,
      zoneId: null,
      zoneName: '',
      original: [],
      handles: []
    };

    // Redimensionnement global (buffer interactif)
    var resize = {
      active: false,
      layer: null,
      startLatLng: null,
      centerLatLng: null,
      startDistM: 0,
      origFeature: null
    };

    function initMap() {
      map = L.map('map').setView([46.8, -71.2], 8);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(map);

      $('#btnUndo').on('click', undoLastPoint);
      $('#btnClear').on('click', clearAdminPoints);
      $('#btnAccept').on('click', acceptPolygon);
      $('#btnSave').on('click', savePolygon);
      $('#btnEditSave').on('click', saveEdits);
      $('#btnEditCancel').on('click', cancelEdits);
      $('#ddlVille').on('change', onVilleChanged);

      map.on('click', function(e){ addAdminPoint(e.latlng.lat, e.latlng.lng); });
    }

    async function onVilleChanged() {
      var villeId = ($('#ddlVille').val() || '').trim();
      // Reset affichage et édition
      drawZonesFromFeatures([]);
      zonesData = [];
      endEditing();

      if (!villeId) return;

      try {
        var res = await $.ajax({
          url: fetchZonesUrl,
          method: 'GET',
          dataType: 'json',
          data: { villeId: villeId }
        });
        if (res && res.ok && Object.prototype.toString.call(res.features) === '[object Array]') {
          zonesData = res.features;
          drawZonesFromFeatures(zonesData);
        }
      } catch (e) {
        console.error('Erreur chargement zones:', e);
      }
    }

    function drawZonesFromFeatures(features) {
      // Clear anciens layers
      zoneLayers.forEach(function(l){ try { map.removeLayer(l); } catch(e){} });
      zoneLayers = [];

      if (!features || features.length === 0) return;

      var group = [];
      features.forEach(function(f) {
        if (!f || !f.geometry || f.geometry.type !== 'Polygon') return;
        var coords = f.geometry.coordinates && f.geometry.coordinates[0] || null;
        if (!Object.prototype.toString.call(coords) === '[object Array]' || coords.length < 3) return;

        var latlngs = coords.map(function (c) { return [c[1], c[0]]; });
        var name = (f.properties && f.properties.name) ? f.properties.name : 'Zone';
        var zoneId = (f.properties && typeof f.properties.id !== 'undefined') ? f.properties.id : null;
        var ville = (f.properties && (f.properties.ville || f.properties.cityName)) ? (f.properties.ville || f.properties.cityName) : '';
        var popup = (ville ? (name + ' — ' + ville) : name) + (zoneId ? (' (id ' + zoneId + ')') : '');

        var layer = L.polygon(latlngs, { color: '#1976d2', weight: 2, fillColor: '#64b5f6', fillOpacity: 0.15 })
                      .bindPopup(popup)
                      .addTo(map);
        // Mémorise métadonnées utiles pour édition
        layer.zoneId = zoneId;
        layer.zoneName = name;

        // Clic pour activer l’édition
        layer.on('click', function() {
          beginEditing(layer);
        });

        zoneLayers.push(layer);
        group.push(layer);
      });

      if (group.length > 0) {
        var fg = L.featureGroup(group);
        map.fitBounds(fg.getBounds(), { padding: [20, 20] });
      }
    }

    // ---------------- Création simple (brouillon) ----------------

    function addAdminPoint(lat, lng) {
      // désactiver l’édition lors de la création de nouveaux points
      if (editing.active) return;

      var m = L.marker([lat, lng], { draggable: true }).addTo(map);
      m.on('dragend', function(ev){
        var idx = adminMarkers.indexOf(m);
        if (idx >= 0) {
          var p = ev.target.getLatLng();
          adminPoints[idx] = { lat: p.lat, lng: p.lng };
          redrawAdminLine();
        }
      });
      m.on('contextmenu', function(){ removeAdminMarker(m); });

      adminMarkers.push(m);
      adminPoints.push({ lat: lat, lng: lng });
      redrawAdminLine();
    }

    function removeAdminMarker(marker) {
      var idx = adminMarkers.indexOf(marker);
      if (idx >= 0) {
        try { map.removeLayer(adminMarkers[idx]); } catch(e){}
        adminMarkers.splice(idx, 1);
        adminPoints.splice(idx, 1);
        redrawAdminLine();
      }
    }

    function redrawAdminLine() {
      if (adminLine) { try { map.removeLayer(adminLine); } catch(e){} adminLine = null; }
      if (draftPolygonLayer) { try { map.removeLayer(draftPolygonLayer); } catch(e){} draftPolygonLayer = null; draftPolygonGeo = null; }

      if (adminPoints.length > 0) {
        var latlngs = adminPoints.map(function(p){ return [p.lat, p.lng]; });
        adminLine = L.polyline(latlngs, { color: '#ff6f00', weight: 2, dashArray: '6 6' }).addTo(map);
      }
    }

    function undoLastPoint() {
      if (editing.active) return;
      if (adminMarkers.length === 0) return;
      var m = adminMarkers.pop();
      try { map.removeLayer(m); } catch(e){}
      adminPoints.pop();
      redrawAdminLine();
    }

    function clearAdminPoints() {
      if (editing.active) return;
      adminMarkers.forEach(function(m){ try { map.removeLayer(m); } catch(e){} });
      adminMarkers = [];
      adminPoints = [];
      if (adminLine) { try { map.removeLayer(adminLine); } catch(e){} adminLine = null; }
      if (draftPolygonLayer) { try { map.removeLayer(draftPolygonLayer); } catch(e){} draftPolygonLayer = null; }
      draftPolygonGeo = null;
    }

    function acceptPolygon() {
      if (editing.active) return;
      if (adminPoints.length < 3) {
        setMsg('Au moins 3 points sont requis pour créer un polygone.', true);
        return;
      }
      var latlngs = adminPoints.map(function(p){ return [p.lat, p.lng]; });
      if (draftPolygonLayer) { try { map.removeLayer(draftPolygonLayer); } catch(e){} }
      draftPolygonLayer = L.polygon(latlngs, { color: '#ef6c00', weight: 2, fillColor: '#ffb74d', fillOpacity: 0.20 }).addTo(map);
      map.fitBounds(draftPolygonLayer.getBounds(), { padding: [25,25] });

      setMsg('Polygone accepté. Vous pouvez l\'enregistrer.', false);
    }

    function polygonToGeoJson(latlngs) {
      var ring = latlngs.map(function (ll) { return [ll[1], ll[0]]; });
      if (ring.length > 0 && (ring[0][0] !== ring[ring.length-1][0] || ring[0][1] !== ring[ring.length-1][1])) {
        ring.push([ring[0][0], ring[0][1]]);
      }
      return { type: 'Feature', properties: {}, geometry: { type: 'Polygon', coordinates: [ ring ] } };
    }

    function savePolygon() {
      if (editing.active) return;

      setMsg('', false);
      var villeId = ($('#ddlVille').val() || '').trim();
      if (!villeId) { setMsg('Choisir une ville avant d\'enregistrer.', true); return; }
      if (!draftPolygonLayer) { setMsg('Aucun polygone brouillon à enregistrer.', true); return; }

      var latlngs = draftPolygonLayer.getLatLngs();
      var outer = Object.prototype.toString.call(latlngs) === '[object Array]' ? (Object.prototype.toString.call(latlngs[0]) === '[object Array]' ? latlngs[0] : latlngs) : [];
      if (!outer || outer.length < 3) { setMsg('Le polygone est invalide.', true); return; }

      var arr = outer.map(function(ll){ return [ll.lat, ll.lng]; });
      var feature = polygonToGeoJson(arr);

      var name = ($('#txtZoneName').val() || '').trim();
      if (name.length === 0) name = 'Zone ' + new Date().toISOString().substring(0,19).replace('T',' ');

      $.ajax({
        url: saveZoneUrl,
        method: 'POST',
        data: { name: name, villeId: villeId, geojson: JSON.stringify(feature) }
      }).done(function(){
        setMsg('Zone enregistrée.', false);
        onVilleChanged();
        clearAdminPoints();
        if (draftPolygonLayer) { try { map.removeLayer(draftPolygonLayer); } catch(e){} draftPolygonLayer = null; draftPolygonGeo = null; }
        $('#txtZoneName').val('');
      }).fail(function(){
        setMsg('Erreur lors de l\'enregistrement.', true);
      });
    }

    // ---------------- Outils conversion pour resize ----------------
    function latLngsToFeatureFromLeaflet(outerLatLngs) {
      var ring = outerLatLngs.map(function(ll){ return [ll.lng, ll.lat]; });
      if (ring.length > 0) {
        var f = ring[0], l = ring[ring.length-1];
        if (f[0] !== l[0] || f[1] !== l[1]) ring.push([f[0], f[1]]);
      }
      return { type: 'Feature', properties: {}, geometry: { type: 'Polygon', coordinates: [ ring ] } };
    }
    function featureToLatLngs(feature) {
      if (!feature || !feature.geometry) return [];
      var geom = feature.geometry;
      if (geom.type === 'MultiPolygon' && Object.prototype.toString.call(geom.coordinates) === '[object Array]' && geom.coordinates.length > 0) {
        geom = { type: 'Polygon', coordinates: geom.coordinates[0] };
      }
      if (geom.type !== 'Polygon') return [];
      var ring = (geom.coordinates && geom.coordinates[0]) ? geom.coordinates[0] : [];
      if (ring.length > 1) {
        var f = ring[0], l = ring[ring.length-1];
        if (f[0] === l[0] && f[1] === l[1]) {
          // garder la fermeture implicite côté Leaflet
          ring = ring.slice(0, ring.length - 1);
        }
      }
      return ring.map(function(c){ return L.latLng(c[1], c[0]); });
    }

    // ---------------- Redimensionnement global (buffer) ----------------
    function attachResizeOnLayer(layer) {
      layer.on('mousedown', onResizeStart);
      layer.on('touchstart', onResizeStart);
    }
    function detachResizeOnLayer(layer) {
      layer.off('mousedown', onResizeStart);
      layer.off('touchstart', onResizeStart);
    }
    function onResizeStart(e) {
      // Ne redimensionne que si ce layer est en édition
      if (!editing.active || editing.layer !== this) return;
      L.DomEvent.stopPropagation(e);
      L.DomEvent.preventDefault(e);

      var outer = editing.layer.getLatLngs()[0] || [];
      if (!outer || outer.length < 3) return;

      resize.active = true;
      resize.layer = editing.layer;
      resize.startLatLng = e.latlng || (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? map.mouseEventToLatLng(e.originalEvent.touches[0]) : null);
      if (!resize.startLatLng) return;

      var feat = latLngsToFeatureFromLeaflet(outer);
      resize.origFeature = feat;

      var c = turf.centroid(feat);
      var cLL = L.latLng(c.geometry.coordinates[1], c.geometry.coordinates[0]);
      resize.centerLatLng = cLL;

      resize.startDistM = map.distance(cLL, resize.startLatLng);

      map.on('mousemove', onResizeMove);
      map.on('mouseup', onResizeEnd);
      map.on('touchmove', onResizeMove);
      map.on('touchend', onResizeEnd);

      setMsg('Redimensionnement: glissez vers l’extérieur/intérieur, puis “Sauver modifs”.', false);
    }
    function onResizeMove(e) {
      if (!resize.active || !resize.origFeature) return;
      var currentLL = e.latlng || (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? map.mouseEventToLatLng(e.originalEvent.touches[0]) : null);
      if (!currentLL) return;

      var distNow = map.distance(resize.centerLatLng, currentLL);
      var deltaM = distNow - resize.startDistM; // + agrandit, - réduit

      var km = deltaM / 1000.0;
      try {
        var buff = turf.buffer(resize.origFeature, km, { units: 'kilometers', steps: 32 });
        var newLatLngs = featureToLatLngs(buff);
        if (newLatLngs.length >= 3) {
          resize.layer.setLatLngs([ newLatLngs ]);
          rebuildHandlesFromLayer();
        }
      } catch (ex) {
        // erreurs transitoires OK
      }
    }
    function onResizeEnd() {
      if (!resize.active) return;
      resize.active = false;
      resize.layer = null;
      resize.startLatLng = null;
      resize.centerLatLng = null;
      resize.startDistM = 0;
      resize.origFeature = null;

      map.off('mousemove', onResizeMove);
      map.off('mouseup', onResizeEnd);
      map.off('touchmove', onResizeMove);
      map.off('touchend', onResizeEnd);
    }

    function rebuildHandlesFromLayer() {
      if (!editing.active || !editing.layer) return;
      // supprime anciennes poignées
      (editing.handles || []).forEach(function(h){
        try { map.removeLayer(h); } catch(e){}
      });
      editing.handles = [];

      var outer = editing.layer.getLatLngs()[0] || [];
      var divIcon = L.divIcon({ className: 'edit-handle', iconSize: [12,12], iconAnchor: [6,6] });

      editing.handles = outer.map(function(ll, idx) {
        var h = L.marker([ll.lat, ll.lng], { icon: divIcon, draggable: true }).addTo(map);
        h.on('dragend', function(e) {
          var p = e.target.getLatLng();
          var current = editing.layer.getLatLngs()[0];
          current[idx] = L.latLng(p.lat, p.lng);
          editing.layer.setLatLngs([current]);
        });
        h.on('contextmenu', function() {
          var current = editing.layer.getLatLngs()[0];
          if (current.length <= 3) return;
          try { map.removeLayer(h); } catch(ex){}
          editing.handles.splice(idx,1);
          current.splice(idx,1);
          editing.layer.setLatLngs([current]);
          // reconstruire index -> poignées
          rebuildHandlesFromLayer();
        });
        return h;
      });
    }

    // ---------------- Edition d’un polygone existant ----------------

    function beginEditing(layer) {
      endEditing(); // si autre édition en cours

      if (!layer || !layer.getLatLngs) return;
      var latlngs = layer.getLatLngs();
      var outer   = Object.prototype.toString.call(latlngs) === '[object Array]' ? (Object.prototype.toString.call(latlngs[0]) === '[object Array]' ? latlngs[0] : latlngs) : [];
      if (!outer || outer.length < 3) return;

      editing.active   = true;
      editing.layer    = layer;
      editing.zoneId   = (typeof layer.zoneId !== 'undefined') ? layer.zoneId : null;
      editing.zoneName = layer.zoneName || '';
      editing.original = outer.map(function(ll){ return L.latLng(ll.lat, ll.lng); });
      $('#txtZoneName').val(editing.zoneName);

      rebuildHandlesFromLayer();
      attachResizeOnLayer(layer);

      $('#btnEditSave').show();
      $('#btnEditCancel').show();
      setMsg('Edition activée. Déplacez les sommets ou glissez sur le polygone pour agrandir/réduire, puis Sauver modifs.', false);
    }

    function saveEdits() {
      if (!editing.active || !editing.layer || !editing.zoneId) return;

      var latlngs = editing.layer.getLatLngs();
      var outer   = Object.prototype.toString.call(latlngs) === '[object Array]' ? (Object.prototype.toString.call(latlngs[0]) === '[object Array]' ? latlngs[0] : latlngs) : [];
      if (!outer || outer.length < 3) { setMsg('Polygone invalide.', true); return; }

      var arr = outer.map(function(ll){ return [ll.lat, ll.lng]; });
      var feature = polygonToGeoJson(arr);
      var name = ($('#txtZoneName').val() || '').trim();

      $.ajax({
        url: updateZoneUrl,
        method: 'POST',
        data: { id: editing.zoneId, name: name, geojson: JSON.stringify(feature) }
      }).done(function(){
        setMsg('Modifications enregistrées.', false);
        var keepVille = $('#ddlVille').val();
        endEditing();
        onVilleChanged();
        $('#ddlVille').val(keepVille);
      }).fail(function(){
        setMsg('Erreur lors de la mise à jour.', true);
      });
    }

    function cancelEdits() {
      if (!editing.active || !editing.layer) return;
      // Restaure
      editing.layer.setLatLngs([ editing.original.map(function(ll){ return L.latLng(ll.lat, ll.lng); }) ]);
      endEditing();
      setMsg('Modifications annulées.', false);
    }

    function endEditing() {
      if (!editing.active) return;
      (editing.handles || []).forEach(function(h){
        try { map.removeLayer(h); } catch(e){}
      });
      editing.handles = [];
      if (editing.layer) detachResizeOnLayer(editing.layer);
      onResizeEnd();

      editing.active = false;
      editing.layer = null;
      editing.zoneId = null;
      editing.zoneName = '';
      editing.original = [];
      $('#btnEditSave').hide();
      $('#btnEditCancel').hide();
    }

    function setMsg(msg, isErr) {
      var el = $('#saveMsg');
      el.removeClass('ok err').text(msg || '');
      if (!msg) return;
      el.addClass(isErr ? 'err' : 'ok');
    }

    document.addEventListener('DOMContentLoaded', initMap);
  </script>
</body>
</html>