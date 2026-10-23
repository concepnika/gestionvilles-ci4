<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Suivi camion</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #map-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: 1;
            background: #fff;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw; /* Pour la rotation, sinon mets 100vw */
            height: 100vh; /* Pour la rotation, sinon mets 100vh */
            /* Pour centrer la carte lors de la rotation */
            transform-origin: 50% 50%;
            will-change: transform;
            background: #fff;
            z-index: 1;
        }
        #itineraireBtn {
            position: absolute;
            top: 10px;
            right: 10px; /* <-- Corrigé ici */
            z-index: 1000;
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .leaflet-routing-container {
            display: none !important;
        }
    </style>
    <script>
        // On s'assure que la variable est bien formatée pour JS
        var polylinePoints = "<?php echo isset($polyline) ? $polyline : ''; ?>";
        
    </script>
</head>
<body>
    <button id="btnTestGeo" style="position:absolute;top:120px;left:10px;z-index:1001;">Test GPS</button>
    <button id="btnResetRotation" style="position:absolute;top:100px;left:10px;z-index:1001;">Nord en haut</button>
    <button id="itineraireBtn" onclick="tracerItineraire()">Itinéraire</button>    
    <div id="map-container"><div id="map"></div></div>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Plugin pour marker rotatif -->
    <script src="https://unpkg.com/leaflet-rotatedmarker@0.2.0/leaflet.rotatedMarker.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>      
    <script src="/assets/js/track_location.js"></script>

</body>
</html>