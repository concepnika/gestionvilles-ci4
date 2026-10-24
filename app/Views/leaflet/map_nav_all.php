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
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
  <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="/assets/js/nav_all.js?v=<?php echo time(); ?>"></script>
  <!-- Mise en page: pas de scroll page, carte plein écran à droite, liste camions scrollable -->
  <style>
    html, body { height: 100%; overflow: hidden; }
    #divShowCed { position: fixed !important; top: 0; left: 260px; right: 0; bottom: 0; margin: 0 !important; width: auto !important; height: auto !important; overflow: hidden; }
    #map { width: 100% !important; height: 100% !important; }
    /* Bloc supérieur gauche (date, recherche, tag) */
    #leftPanel {
      display: block; position: fixed; left: 10px; top: 10px; width: 210px; height: auto;
      min-height: 210px; border: 2px red solid; padding: 10px; background: #fff; z-index: 1001;
    }
    /* Liste des chauffeurs sous le panneau */
    #divChauffeur {
      position: fixed !important; left: 10px !important; top: 230px !important; bottom: 10px;
      width: 210px !important; height: auto !important; overflow-y: auto !important; overflow-x: hidden;
      padding: 16px !important; border: 2px #555 solid; background: #fff; z-index: 1000;
    }
    #divChauffeur > div { overflow: visible !important; height: auto !important; }
    #divShowCed { z-index: 1; }
    .btn-create-tag { display:block; margin-top:8px; width:100%; }

    /* Style amélioré pour l'overlay d'image */
    #imgOverlay {
      position: fixed;
      z-index: 5000;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: none;
      background: rgba(0, 0, 0, 0.9);
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
      font-size: 18px;
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
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
      border-radius: 8px;
      background: #000;
    }
    /* Style pour les tooltips d'adresses */
    .address-tooltip {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 4px 8px;
      font-size: 12px;
      font-weight: 500;
      color: #333;
      white-space: nowrap;
      max-width: none !important;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body id="page">
  <!-- Overlay pour afficher les images en plein écran -->
  <div id="imgOverlay">
    <button class="closeBtn" title="Fermer (Échap)">✕ Fermer</button>
    <img src="" alt="Image agrandie" />
  </div>

  <div id="divimgtruck" style="display:none;">
    <img id="ImgTruckSani" src="" width="60" alt="" />
    <input type="hidden" id="allchauffeur" value="1" />
  </div>
  <div class="row">
    <div id="leftPanel">
      <label style="font-size:24px;font-weight:600;min-width:220px">LOCALISATION</label>
      <label style="width:220px;">Date
        <input type="date" style="width:160px" class="form-control" onchange="getDateChange(this.value)" value="<?php echo ($ActiveDate ?? ''); ?>" id="dtedeb" />
      </label>
      <input type="hidden" id="edtLapRefresh" value="30" />
      <input type="hidden" id="edtEpaisseur" value="4" />

      <label for="edtFind" style="margin-top:8px;">Rechercher</label>
      <div class="form-group" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
        <input id="edtFind" type="text" class="form-control" style="flex:1;min-width:140px;" placeholder="numéro, rue, ville" />
        <button id="btnFind" class="btn btn-sm btn-primary" type="button">Chercher</button>
        <button id="btnCreateTag" class="btn btn-sm btn-warning" type="button" title="Créer un tag (rapport de collecte)">Créer tag</button>
        <!-- bouton toggle pour afficher les cercles d'adresses -->
        <button id="btnToggleAddressCircles" class="btn btn-sm btn-info" type="button" title="Afficher les adresses des parcours">Afficher adresses</button>
      </div>
      
    </div>
    <div id="divChauffeur" class="border border-secondary">
      <div><!-- contenu injecté --></div>
    </div>

    <div id="divShowCed" style="margin-top: 10px;margin-left:260px;height: 1900px; width: 100%;position:relative;">
      <div id="map" style="width: 100%;height:1900px"></div>
    </div>
  </div>
  <input type="hidden" id="idchauffeur" value="0" />
  <input type="hidden" id="nocamion" value="" />
  <input type="hidden" id="NewZoom" value="8" />
  <input type="hidden" id="idville" value="<?php echo (int) (ci3sess('idville') ?? 0); ?>" />

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
            <select id="selCamModal" name="selCamModal" class="form-control" style="width:200px;"></select>
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
    function baseUrl(p){ return '<?php echo rtrim(ci3_url(), "/"); ?>' + '/' + String(p||'').replace(/^\/+/, ''); }

    // ===================== GESTION OVERLAY IMAGE =====================
    (function(){
      var overlay = document.getElementById('imgOverlay');
      var overlayImg = overlay.querySelector('img');
      var closeBtn = overlay.querySelector('.closeBtn');

      // Fonction pour afficher l'image en plein écran
      window.showFullscreen = function(url) {
        if (!url) return;
        overlayImg.src = url;
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // empêche le scroll du body
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

    document.addEventListener('DOMContentLoaded', function(){
      var findBtn=document.getElementById('btnFind');
      var findInput=document.getElementById('edtFind');
      function performSearch(){
        if(!window.trucksByCam) return;
        var q=(findInput && findInput.value ? findInput.value : '').trim().toLowerCase();
        if(!q) return;
        var matches=[];
        Object.keys(trucksByCam).forEach(function(cam){
          var store=trucksByCam[cam];
          if(!store||!Array.isArray(store.points)) return;
          store.points.forEach(function(meta){
            var adr=(meta && meta.adresse ? String(meta.adresse).toLowerCase() : '');
            if(adr && adr.indexOf(q)!==-1){ matches.push({cam:cam, meta:meta}); }
          });
        });
        if(matches.length===0){ alert('Aucune correspondance trouvée.'); return; }
        matches.sort(function(a,b){ return Date.parse(b.meta.hre||0)-Date.parse(a.meta.hre||0); });
        var pick=matches[0];
        if(typeof pick.meta.lat==='number' && typeof pick.meta.lng==='number'){
          if(!window.map){ alert('Carte non prête, réessayez dans un instant.'); return; }
          map.setView([pick.meta.lat,pick.meta.lng],17);
          var popupAddress=pick.meta.adresse||'N/A';
          var popupHtml=
            '<div style="min-width:280px;"><b>Camion:</b> '+escapeHtml(pick.cam||'')+'<br/>'
            + '<b>Chauffeur:</b> '+escapeHtml(pick.meta.chauffeur||'N/A')+'<br/>'
            + '<b>Heure:</b> '+escapeHtml(pick.meta.hre||'N/A')+'<br/>'
            + '<b>Adresse:</b> '+escapeHtml(popupAddress)+'<br/>'
            + '<button type="button" class="btn btn-sm btn-secondary popup-create-tag" data-address="'+encodeURIComponent(popupAddress)+'" data-lat="'+pick.meta.lat+'" data-lng="'+pick.meta.lng+'">Créer tag</button>'
            + '</div>';
          var popup=L.popup({maxWidth:600,className:'wide-popup'}).setLatLng([pick.meta.lat,pick.meta.lng]).setContent(popupHtml);
          map.once('popupopen', function(e){
            if(e.popup!==popup) return;
            var btn=e.popup.getElement().querySelector('.popup-create-tag');
            if(!btn) return;
            btn.addEventListener('click', function(){
              var addr=decodeURIComponent(btn.getAttribute('data-address')||'');
              var lat=parseFloat(btn.getAttribute('data-lat'));
              var lng=parseFloat(btn.getAttribute('data-lng'));
              openCreateTagPopup(addr, lat, lng);
            });
          });
          popup.openOn(map);
        }
      }
      if(findBtn){ findBtn.addEventListener('click', performSearch); }
      if(findInput){
        findInput.addEventListener('keydown', function(e){
          if(e.key==='Enter'){ e.preventDefault(); performSearch(); }
        });
      }
    });
  </script>
</body>
</html>