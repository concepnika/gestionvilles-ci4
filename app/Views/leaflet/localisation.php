<?php

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Camion en temps réel</title>
  <link 
    rel="stylesheet" 
    href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" 
  />
  <style>
    #map { height: 100vh; }
  </style>
</head>
<body>
  <div id="map"></div>
  <!--<img id="imgCam" src="/assets/img/google/truck2.png"  />--> 
  <script 
    src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js">
  </script>
  <script>
    // 1. Initialisation de la carte
    const map = L.map('map').setView([46.8, -71.2], 12);
    const truckIcon = L.icon({
      iconUrl: '/assets/img/google/truck.png',
      iconSize: [40, 40],
      iconAnchor: [20, 20]
    });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 2. Données de test (lat, lng)
    let path = [
      [46.800, -71.200],
      [46.810, -71.210],
      [46.815, -71.225],
      [46.820, -71.240]
    ];

    // 3. Afficher la polyline
    const route = L.polyline(path, {
      color: 'blue',
      weight: 4,
      opacity: 0.7
    }).addTo(map);

    // 4. Afficher le marker du camion (au dernier point)
    let truckIcon = L.icon({
      iconUrl: 'camion.png',
      iconSize: [40, 40],
      iconAnchor: [20, 20]
    });

    const marker = L.marker(path[path.length - 1], { icon: truckIcon })
      .addTo(map)
      .bindPopup('Camion #42');

    // Ajuster la vue pour englober toute la route
    map.fitBounds(route.getBounds());
	function refreshPosition() {
	  fetch('/api/position')
		.then(res => res.json())
		.then(pos => {
		  // 1. Ajouter au chemin
		  path.push([pos.lat, pos.lng]);
		  route.setLatLngs(path);

		  // 2. Déplacer le marker
		  marker.setLatLng([pos.lat, pos.lng]);

		  // (Optionnel) recentrer la carte
		  // map.panTo([pos.lat, pos.lng]);
		})
		.catch(console.error);
	}

// toutes les 10 secondes
setInterval(refreshPosition, 10000);
  </script>
</body>
</html>