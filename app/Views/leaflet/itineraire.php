<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Suivi en temps réel du camion avec Leaflet</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Feuilles de style pour Leaflet et Leaflet Routing Machine -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    #map {
      height: 100%;
    }
    /* Panneau de contrôle pour saisir l'adresse */
    #control {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 1000;
      background-color: white;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    .leaflet-routing-container {
      display: none !important;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <!-- Panneau de contrôle pour saisir l'adresse -->
  <div id="control">
    <input type="hidden" id="adresse" placeholder="" value="<?php echo $adresse; ?>">
    <input type="hidden" id="camionLat" placeholder="" value="<?php echo $lat; ?>"> 
    <input type="hidden" id="camionLng" placeholder="" value="<?php echo $lng; ?>">     
  </div>
  
  <!-- Panneau de contrôle pour saisir l'adresse -->
  <div id="control">
    <input type="text" id="address" placeholder="Adresse de destination" size="40">
    <button id="submit">Tracer l'itinéraire</button>
  </div>
  <div id="hidden-itinerary" style="display: none;"></div>

  <!-- Chargement de Leaflet et Leaflet Routing Machine -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
  <script src="/assets/js/itineraire.js"></script>
  <script>

  </script>
</body>
</html>