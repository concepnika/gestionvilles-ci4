<?php

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <meta name="description" lang="fr" content=" ">
  <title>Robert Daoust</title>

  <!-- Font Awesome & Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" 
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" 
        crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
        crossorigin="anonymous">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <!-- jQuery & Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" 
          integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" 
          crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" 
          integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" 
          crossorigin="anonymous"></script>
  <script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
  
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Après Leaflet JS -->
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
  <!-- Mise en page: pas de scroll page, carte plein écran à droite, liste camions scrollable -->
  <style>
    html, body {
      height: 100%;
      overflow: hidden; /* bloque le scroll de la page */
    }
    #divShowCed {
      position: fixed !important;
      top: 0;
      left: 260px; /* espace pour les panneaux de gauche */
      right: 0;
      bottom: 0;
      margin: 0 !important;
      width: auto !important;
      height: auto !important;
      overflow: hidden; /* la carte gère le zoom/pan, pas de scroll conteneur */
    }
    #map {
      width: 100% !important;
      height: 100% !important; /* carte plein écran dans son conteneur */
    }
    #divChauffeur {
      position: fixed !important;
      left: 10px !important;
      top: 240px !important;
      bottom: 10px;             /* colle au bas de la fenêtre */
      width: 210px !important;
      height: auto !important;
      overflow-y: auto !important; /* scroll vertical pour grande flotte */
      overflow-x: hidden;
      padding: 16px !important;
    }
    #divChauffeur > div {
      overflow: visible !important; /* évite un scroll imbriqué */
      height: auto !important;
    }
  </style>
</head>
<body id="page">

  <div id="divimgtruck" style="display:none;">
    <img id="ImgTruckSani" src="" width="60" alt="" />
    <input type="hidden" id="allchauffeur" value="1" />
  </div>
  <div class="row">
    <div style="display: block; position: fixed; left: 10px; top: 10px; height: 25vh; width: 210px; border: 2px red solid; padding:10px;">
      <label style="font-size:24px;font-weight:600;min-width:220px">LOCALISATION</label>
      <label style="width:220px;">Date
        <input type="date" style="width:160px" class="form-control" onchange="getDateChange(this.value)" value="<?php echo ($ActiveDate ?? ''); ?>" id="dtedeb" />
      </label>
      <table >
          <tr>
              <td>
                  <label style="width: 100px;">Délai
                    <input type="number" style="width:80px" class="form-control" value="4" id="edtLapRefresh" />
                  </label>
              </td>
              <td style="display:none">
                  <label style="width: 100px;">Ligne
                    <input type="number" style="width:80px" class="form-control" value="4" id="edtEpaisseur" />
                  </label>
              </td>
          </tr>
      </table>

      <br />
      <a href="javascript:showFindAdresse()" style="width: 250px;height:35px;">
        <i class="fa fa-search" style="width: 250px;margin-top:-10px;height:35px; font-weight:bold;font-size:22px">
          &nbsp;Rechercher
        </i>
      </a>
    </div>
    <div id="divChauffeur" class="border border-secondary" 
         style="display: block; position: fixed; left: 10px; top: 240px; height: 700px; width: 210px; border: 2px #555 solid;padding:32px;">
      <div>
        <!-- Le contenu des chauffeurs sera injecté ici -->
      </div>
    </div>

    <div id="divShowCed" style="margin-top: 10px;margin-left:260px;height: 1900px; width: 100%;position:relative;">
      <div id="map" style="width: 100%;height:1900px"></div>
    </div>
  </div>
  <input type="hidden" id="idchauffeur" value="0" />
  <input type="hidden" id="nocamion" value="" />
  <input type="hidden" id="NewZoom" value="8" />

  <div class="modal fade" id="modalFindAdr" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-block-md">
      <div class="modal-content">
        <div class="modal-header btn-info">
          <h2 class="modal-title">Rechercher une adresse</h2>
        </div>
        <div class="modal-body">
          <div id="divInfoReq" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;"> 
            <h3>Date</h3>
            <input type="date" id="edtDateModal" value="" />
            <h3>Camions</h3>
            <select id="selCamModal" name="selCamModal" class="form-control" style="width:200px;">
            </select>
            <div id="divAdresse" style="display:block">
              <h3>Adresse de la ville</h3>
              <h4>Adresse recherchée (no civique rue, ville)</h4>
              <div class="input-group" id="divFind">
                <input type="text" class="form-control" id="edtFind" placeholder="numéro rue, ville" value="" />
                <span class="input-group-addon">
                  <a href="#" onclick="findAdresse()" style="width:35px;height:33px">
                    <i class="fa fa-search" style="font-weight:bold;font-size:18px"></i>
                  </a>
                </span>
              </div>
              <select id="selAdrModal" onclick="goToAdresse(this.value)" size="7" name="selAdrModal" class="form-control"></select>
            </div>
            <br />
            <input type="hidden" id="edtIdRepVille" value="" />
            <input type="button" class="btn btn-info" id="btnModalRep" style="display:none" value="" />
          </div> 
        </div>
        <div class="modal-footer btn-info">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Rechercher</button>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Variables globales
    var map;
    var markers = [];
    var polylines = {}; // nocamion -> polyline (compat)
    var trucksByCam = {}; // nocamion -> { marker, polyline, color, lastMeta, lastLatLng, points[] }
    var IsBusy = false;
    var colors = ['red', 'blue', 'orange', 'fuchsia', 'black', 'lightgreen', 'darkred'];
    var colorInUse = [];
    var allCam = [];
    var continuePolling = true;
    var polylinesPreloaded = false;
    //var trucksByCam = {}; // nocamion -> { marker, polyline, color, lastMeta, lastLatLng, points[], bufferLayer }

    function initMap() {
      var defaultLoc = [45.3991735, -74.0780621];
      map = L.map('map').setView(defaultLoc, 10, { animate: true, duration: 1.0 });
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(map);

      // Recalage de la taille après application du CSS (plein écran)
      setTimeout(function(){ map.invalidateSize(false); }, 0);
      window.addEventListener('resize', function(){ map.invalidateSize(false); });

      boucleShowCamionSan();
    }

    // Helper: crée/maj un polygone buffer autour d'une polyline
    function upsertBufferForCam(cam, latlngs, bufferMeters) {
      if (!Array.isArray(latlngs) || latlngs.length < 2 || typeof turf === 'undefined') return;

      // GeoJSON attend [lng, lat]
      var coords = latlngs.map(function (ll) { return [ll[1], ll[0]]; });
      var line = turf.lineString(coords);
      var bufferKm = (bufferMeters || 50) / 1000.0; // Turf utilise des km
      var buffered = turf.buffer(line, bufferKm, { units: 'kilometers' });
      if (!buffered) return;

      // Style du polygone
      var style = { color: '#d32f2f', weight: 2, opacity: 0.8, fillColor: '#ff5252', fillOpacity: 0.15 };

      // Créer/MàJ la couche
      if (trucksByCam[cam].bufferLayer) {
        try { map.removeLayer(trucksByCam[cam].bufferLayer); } catch(e) {}
      }
      trucksByCam[cam].bufferLayer = L.geoJSON(buffered, { style: style }).addTo(map);
    }

    // Helper: crée/maj un polygone "enveloppe convexe" autour de la polyline
    function upsertHullForCam(cam, latlngs) {
      if (!Array.isArray(latlngs) || latlngs.length < 3 || typeof turf === 'undefined') return;

      // GeoJSON attend [lng, lat]
      var coords = latlngs.map(function (ll) { return [ll[1], ll[0]]; });
      var points = turf.points(coords);
      var hull = turf.convex(points);
      if (!hull) return;

      // Style du polygone
      var style = { color: '#1976d2', weight: 2, opacity: 0.8, fillColor: '#64b5f6', fillOpacity: 0.12 };

      // Créer/MàJ la couche (réutilise bufferLayer)
      if (trucksByCam[cam].bufferLayer) {
        try { map.removeLayer(trucksByCam[cam].bufferLayer); } catch(e) {}
      }
      trucksByCam[cam].bufferLayer = L.geoJSON(hull, { style: style }).addTo(map);
    }

    function getDateChange(dte) {
      $("#dtedeb").val(dte);   
      $("#allchauffeur").val(1);
      $("#idchauffeur").val(0);
      resetAllTrucks();
      var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?idchauffeur=0&allchauffeur=1';
      document.location = urlName;
    }

    function createTruckMarker(position, cam) {
      var imgUrl = 'https://www.robert-daoust.ca/assets/img/numcam/cam' + cam.trim() + '.png';
      var truckIcon = L.icon({
        iconUrl: imgUrl,
        iconSize: [40, 40],
        iconAnchor: [20, 30]
      });
      var marker = L.marker([position.lat, position.lng], { icon: truckIcon }).addTo(map);
      marker.bindPopup(buildTruckPopup(cam, null));
      return marker;
    }

    function buildTruckPopup(cam, meta) {
      var heure = meta && meta.hre ? meta.hre : 'N/A';
      var adresse = meta && meta.adresse ? meta.adresse : 'N/A';
      var chauffeur = meta && meta.chauffeur ? meta.chauffeur : 'N/A';
      return '<div style="min-width:330px;">' +
             '<div><span style="font-size:24px"><b>Camion:</b> ' + cam + '</span></div>' +
             '<div><span style="font-size:24px"><b>Chauffeur:</b> ' + chauffeur + '</span></div>' +
             '<div><span style="font-size:24px"><b>Heure:</b> ' + heure + '</span></div>' +
             '<div><span style="font-size:24px"><b>Adresse:</b> ' + adresse + '</span></div>' +
             '</div>';
    }

    function getNextColorDispo() {
      for (var i = 0; i < colors.length; i++) {
        if (colorInUse.indexOf(colors[i]) === -1) {
          colorInUse.push(colors[i]);
          return colors[i];
        }
      }
      var alt = '#' + Math.floor(Math.random() * 16777215).toString(16);
      colorInUse.push(alt);
      return alt;
    }

    function getColorForCam(cam) {
      if (trucksByCam[cam] && trucksByCam[cam].color) return trucksByCam[cam].color;
      return getNextColorDispo();
    }

    function samePoint(a, b) {
      if (!a || !b) return false;
      var eps = 1e-8;
      return Math.abs(a.lat - b.lat) < eps && Math.abs(a.lng - b.lng) < eps;
    }

    // 2) upsertTruck: ne pas alimenter la polyline si iz=0
    function upsertTruck(cam, position) {
      var truck = trucksByCam[cam];
      if (!truck) {
        var color = getColorForCam(cam);
        var marker = createTruckMarker(position, cam);
        markers.push(marker);

        // IMPORTANT: polyline vide au départ
        var polyline = L.polyline([], { color: color, weight: parseInt($("#edtEpaisseur").val()) }).addTo(map);
        polyline.on("click", function (e) { onTruckPolylineClick(cam, e); });

        trucksByCam[cam] = { marker: marker, polyline: polyline, color: color, lastMeta: null, lastLatLng: {lat: position.lat, lng: position.lng}, points: [] };
        polylines[cam] = polyline;

        // Ajoute le point initial au polyline SEULEMENT si en zone
        if (position.iz === 1) {
          polyline.addLatLng([position.lat, position.lng]);
        }

        // Met à jour le popup (facultatif pour IsInZone)
        fetchAddressForLatLng(cam, position.lat, position.lng, function (meta) {
          trucksByCam[cam].lastMeta = meta;
          marker.setPopupContent(buildTruckPopup(cam, meta));
        });

      } else {
        // Mise à jour du marker et de la polyline selon IsInZone
        if (!samePoint(truck.lastLatLng, position)) {
          truck.lastLatLng = {lat: position.lat, lng: position.lng};
          truck.marker.setLatLng([position.lat, position.lng]);

          // Ajoute au polyline seulement si en zone
          if (position.iz === 1) {
            truck.polyline.addLatLng([position.lat, position.lng]);
          }

          fetchAddressForLatLng(cam, position.lat, position.lng, function (meta) {
            trucksByCam[cam].lastMeta = meta;
            truck.marker.setPopupContent(buildTruckPopup(cam, meta));
          });
        } else {
          truck.marker.setLatLng([position.lat, position.lng]);
        }
      }
    }

    function preloadAllPolylinesIfPossible() {
      if (polylinesPreloaded) return;
      var $labels = $("#divChauffeur").find("label[id^='lbl_']");
      if ($labels.length === 0) return;

      $labels.each(function () {
        var $lbl = $(this);
        var cam = $lbl.attr("id").replace("lbl_", "");
        var onclickAttr = $lbl.attr("onclick") || "";
        var match = onclickAttr.match(/showCam\((\d+),\s*'([^']+)'\)/);
        if (!match) return;
        var idchauffeur = parseInt(match[1], 10);
        var nocamion = match[2];
        if (!nocamion || isNaN(idchauffeur)) return;

        fetchAndSetPolylineForTruck(nocamion, idchauffeur);
      });

      polylinesPreloaded = true;
    }

    function parsePointWithMeta(raw) {
      var parts = raw.split(',');
      if (parts.length < 2) return null;
      var lat = parseFloat(parts[0]);
      var lng = parseFloat(parts[1]);
      if (isNaN(lat) || isNaN(lng)) return null;

      var rest = raw.substring(raw.indexOf(parts[1]) + parts[1].length + 1).trim();
      var meta = {};
      if (rest.length > 0) {
        var mTime = rest.match(/timespan\s*:\s*([^,]+)(?:,|$)/i);
        var mChf  = rest.match(/chauffeur\s*:\s*([^,]+)(?:,|$)/i);
        var mAdr  = rest.match(/adresse\s*:\s*(.+)$/i); // adresse peut contenir des virgules
        if (mTime) meta.hre = mTime[1].trim();
        if (mChf)  meta.chauffeur = mChf[1].trim();
        if (mAdr)  meta.adresse = mAdr[1].trim();
      }
      return { lat: lat, lng: lng, meta: meta };
    }

    function fetchAndSetPolylineForTruck(cam, idchauffeur) {
      var urlName = '/sanivan/getpolyline/' + $("#dtedeb").val()
            + '?idchauffeur=' + idchauffeur
            + '&nocamion=' + encodeURIComponent(cam)
            + '&snap=1';
      $.ajax({ url: urlName, async: true, context: document.body })
      .done(function (data) {
        if (!data) return;
        var groups = String(data).split('|');
        var allLatLngs = [];
        var metaPoints = [];

        for (var g = 0; g < groups.length; g++) {
          var newPos = groups[g].split('_');
          for (var i = 0; i < newPos.length; i++) {
            var p = parsePointWithMeta(newPos[i]);
            if (p) {
              allLatLngs.push([p.lat, p.lng]);
              metaPoints.push({ lat: p.lat, lng: p.lng, hre: p.meta.hre || null, chauffeur: p.meta.chauffeur || null, adresse: p.meta.adresse || null });
            }
          }
        }
        if (allLatLngs.length === 0) return;

        var last = allLatLngs[allLatLngs.length - 1];
        upsertTruck(cam, { lat: last[0], lng: last[1] });

        if (trucksByCam[cam]) {
          // MàJ polyline + meta
          trucksByCam[cam].polyline.setLatLngs(allLatLngs);
          trucksByCam[cam].points = metaPoints;

          var lastMeta = metaPoints[metaPoints.length - 1] || null;
          if (lastMeta && (lastMeta.hre || lastMeta.adresse || lastMeta.chauffeur)) {
            trucksByCam[cam].lastMeta = { hre: lastMeta.hre, adresse: lastMeta.adresse, chauffeur: lastMeta.chauffeur };
            trucksByCam[cam].marker.setPopupContent(buildTruckPopup(cam, trucksByCam[cam].lastMeta));
          }

          // IMPORTANT: dessiner/mettre à jour le polygone englobant (ex. 50 m)
            //upsertBufferForCam(cam, allLatLngs, 50);
          upsertHullForCam(cam, allLatLngs); 
        }
      })
      .fail(function (err) {
        console.error("Erreur GetPolyline pour camion " + cam, err);
      });
    }

    function findNearestPointMeta(cam, latlng) {
      var truck = trucksByCam[cam];
      if (!truck || !truck.points || truck.points.length === 0) return null;
      var best = null, bestD = Number.POSITIVE_INFINITY;
      for (var i = 0; i < truck.points.length; i++) {
        var p = truck.points[i];
        var d = Math.hypot(p.lat - latlng.lat, p.lng - latlng.lng);
        if (d < bestD) { bestD = d; best = p; }
      }
      return best;
    }

    function showPopupAt(latlng, cam, meta) {
      var content = buildTruckPopup(cam, meta || trucksByCam[cam].lastMeta);
      L.popup().setLatLng(latlng).setContent(content).openOn(map);
    }

    function onTruckPolylineClick(cam, e) {
      var nearest = findNearestPointMeta(cam, e.latlng);
      if (nearest && (nearest.hre || nearest.adresse || nearest.chauffeur)) {
        showPopupAt(e.latlng, cam, {
          hre: nearest.hre || 'N/A',
          adresse: nearest.adresse || 'N/A',
          chauffeur: nearest.chauffeur || (trucksByCam[cam] && trucksByCam[cam].lastMeta ? trucksByCam[cam].lastMeta.chauffeur : 'N/A')
        });
      } else {
        fetchAddressForLatLng(cam, e.latlng.lat, e.latlng.lng, function(meta) {
          if (!meta.chauffeur && trucksByCam[cam] && trucksByCam[cam].lastMeta) {
            meta.chauffeur = trucksByCam[cam].lastMeta.chauffeur || 'N/A';
          }
          showPopupAt(e.latlng, cam, meta);
        });
      }
    }

    function fetchAddressForLatLng(cam, lat, lng, cb) {
      $.ajax({
        url: '/sanivan/getadressetoshow',
        type: 'POST',
        dataType: 'json',
        data: { lat: lat, lng: lng, nocamion: cam, dteactive: $("#dtedeb").val() }
      }).done(function (data) {
        var meta = { hre: data && data.hre ? data.hre : 'N/A', adresse: data && data.adresse ? data.adresse : 'N/A' };
        var truck = trucksByCam[cam];
        if (truck) {
          // Merge: garder chauffeur existant si présent
          var merged = Object.assign({}, truck.lastMeta || {}, meta);
          truck.lastMeta = merged;
          truck.points.push({ lat: lat, lng: lng, hre: merged.hre, adresse: merged.adresse, chauffeur: merged.chauffeur });
        }
        if (typeof cb === 'function') cb(truck ? truck.lastMeta : meta);
      }).fail(function () {
        if (typeof cb === 'function') cb({ hre: 'N/A', adresse: 'N/A', chauffeur: (trucksByCam[cam] && trucksByCam[cam].lastMeta && trucksByCam[cam].lastMeta.chauffeur) || 'N/A' });
      });
    }

    function ShowCamionSan() {
      var idchauffeur = parseInt($("#idchauffeur").val());
      $("#allchauffeur").val(idchauffeur == 0 ? 1 : 0);

      var urlName = '/sanivan/routecam/' + $("#dtedeb").val() +
                    '?idchauffeur=' + idchauffeur + '&camchauf=&allchauffeur=' + $("#allchauffeur").val() + '&from=1';

      return $.ajax({
        url: urlName,
        async: true,
        context: document.body
      }).done(function (data) {
        var objData = JSON.parse(data);
        $("#divChauffeur").html(objData.chauffeursRte);

        preloadAllPolylinesIfPossible();

        var dataurl = objData.latlng;
        if (dataurl && dataurl.includes('_')) {
          var arMarkers = dataurl.split(';');
          allCam.length = 0;
          for (var i = 0; i < arMarkers.length; i++) {
            var ardata = arMarkers[i].split('_');
            var cam = ardata[0];
            allCam.push(cam);

            var arPos = (ardata[1] || '').split(',');
            if (arPos.length >= 2) {
              var lat = parseFloat(arPos[0]);
              var lng = parseFloat(arPos[1]);
              var iz  = (arPos.length >= 3) ? parseInt(arPos[2], 10) : 0; // IsInZone optionnel

              if (!isNaN(lat) && !isNaN(lng)) {
                var position = { lat: lat, lng: lng, iz: iz };
                upsertTruck(cam, position);
              } else {
                console.error('Coordonnées invalides :', arPos[0], arPos[1]);
              }
            } else {
              console.error('Format arPos invalide :', arPos);
            }
          }
        }
      }).fail(function (err) {
        console.error('Erreur routecam:', err);
      });
    }

    async function boucleShowCamionSan() {
      while (continuePolling) {
        try {
          if (!IsBusy) {
            await ShowCamionSan();
          }
        } catch (error) {
          console.error("Erreur lors de l'exécution de ShowCamionSan :", error);
        }
        var delai = parseInt($("#edtLapRefresh").val()) * 1000;
        await new Promise(resolve => setTimeout(resolve, delai));
      }
    }

    function showFindAdresse() {
      var selectOptions = '';
      allCam.forEach(function (value) {
        selectOptions += '<option value="' + value + '">' + value + '</option>';
      });
      selectOptions = '<option value="">Choisir un camion</option>' + selectOptions;
      $("#selCamModal").html(selectOptions);
      $("#edtDateModal").val($("#dtedeb").val());
      $("#selAdrModal").html('');
      $("#modalFindAdr").modal('show');
    }

    function refresh() {
      $("#allchauffeur").val(1);
      resetAllTrucks();
      var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?&allchauffeur=1';
      document.location = urlName;
    }

    function getAdresseFromCam(value){
      var urlName = '/sanivan/adressefromcam/' + value + '/' + $("#dtedeb").val();
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        $("#selAdrModal").empty();
        $("#selAdrModal").html(msg);
      });
    }

    function findAdresse() {
      var urlName = '/sanivan/adressetofind/' + $("#selCamModal").val() + '/' + $("#dtedeb").val()
                    + '/' + encodeURIComponent($("#edtFind").val());
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        $("#selAdrModal").html(msg);
      });
    }

    function createMarker(position, infoadresse) {
      var marker = L.marker([position.lat, position.lng]).addTo(map);
      marker.bindPopup(infoadresse);
      markers.push(marker);
    }

    function goToAdresse(value) {
      var urlName = '/sanivan/gotoadresse/' + value;
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        IsBusy = true;
        var objData = JSON.parse(msg);
        var lat = parseFloat(objData.lat);
        var lng = parseFloat(objData.lng);
        var position = { lat: lat, lng: lng };
        var place = 'Date: ' + objData.hre + ', Adresse: ' + objData.adresse;
        $("#modalFindAdr").modal('hide');
        createMarker(position, place);
        map.setView([lat, lng], 18);
        setTimeout(() => { IsBusy = false; }, 500);
      });
    }

    function resetAllTrucks() {
        for (var cam in trucksByCam) {
        if (!trucksByCam.hasOwnProperty(cam)) continue;
        try { map.removeLayer(trucksByCam[cam].marker); } catch (e) {}
        try { map.removeLayer(trucksByCam[cam].polyline); } catch (e) {}
        try { if (trucksByCam[cam].bufferLayer) map.removeLayer(trucksByCam[cam].bufferLayer); } catch (e) {}
        }
        trucksByCam = {};
        markers.forEach(function(m) { try { map.removeLayer(m); } catch (e) {} });
        markers = [];
        polylines = {};
        colorInUse = [];
        polylinesPreloaded = false;
    }

    function centerMapOnMarkers() {
      if (markers.length === 0) return;
      var group = L.featureGroup(markers);
      map.fitBounds(group.getBounds(), { padding: [50, 50] });
    }

    function showCam(idchauffeur, nocamion) {
      IsBusy = true;
      try {
        $("#idchauffeur").val(idchauffeur);
        $("#nocamion").val(nocamion);

        if (idchauffeur > 0) {
          var truck = trucksByCam[nocamion];
          if (truck && truck.polyline) {
            var bounds = truck.polyline.getBounds();
            map.fitBounds(bounds, { padding: [70, 50] });
          } else if (truck && truck.marker) {
            map.setView(truck.marker.getLatLng(), 14);
          }
        } else {
          centerMapOnMarkers();
        }
      } finally {
        setTimeout(() => { IsBusy = false; }, 200);
      }
    }

    document.addEventListener("DOMContentLoaded", initMap);
  </script>
</body>
</html>