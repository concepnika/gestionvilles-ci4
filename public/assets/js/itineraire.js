/* --- Initialisation --- */
// Position initiale par défaut (exemple : Blainville) – elle sera mise à jour par la géolocalisation.
let truckLat = 45.68372026551515, truckLng = -73.8978711515665;

// Variables globales pour la destination et l'itinéraire.
let destinationCoords = null;
let destinationMarker = null;
let routingControl = null;
let routePolyline = null; // Variable pour la polyline de l'itinéraire

// Création de la carte
const map = L.map('map').setView([truckLat, truckLng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Marqueur pour le camion
const truckMarker = L.marker([truckLat, truckLng])
    .addTo(map)
    .bindPopup("Camion (position actuelle)")
    .openPopup();

/* --- Fonction pour tracer l'itinéraire --- */
function tracerItineraire(destLat, destLng) {
    // Supprime l'itinéraire précédent s'il existe.
    if (routingControl !== null) {
        map.removeControl(routingControl);
    }
    if (routePolyline !== null) {
        map.removeLayer(routePolyline);
    }

    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(truckLat, truckLng),
            L.latLng(destLat, destLng)
        ],
        itineraryContainer: document.getElementById('hidden-itinerary'),
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1'
        }),
        routeWhileDragging: true,
        lineOptions: {
            styles: [
                { color: 'darkblue', opacity: 1.0, weight: 8 }
            ]
        },
        draggableWaypoints: false,
        addWaypoints: false
    }).addTo(map);

    // Lorsqu'un itinéraire est trouvé, on en extrait la géométrie pour créer une polyline.
    routingControl.on('routesfound', function (e) {
        // e.routes est un tableau contenant les itinéraires possibles
        const route = e.routes[0];
        // Les coordonnées de l'itinéraire sont généralement disponibles dans route.coordinates
        // (celles-ci sont des objets L.LatLng ou des objets {lat, lng})
        const coords = route.coordinates;
        var latlngs = coords.join('_');
        var urlName = base_url + 'sanivan/postpolyline/' +  latlngs;
        $.ajax({
            url: urlName,
            context: document.body
        }).done(function (data) {
            //transfert seulement;
        });
        if (routePolyline !== null) {
            map.removeLayer(routePolyline);
        }
        routePolyline = L.polyline(coords, { color: 'red', opacity: 0.7, weight: 4 }).addTo(map);
    });
}

/* --- Fonction pour mettre à jour la position du camion et recalculer l'itinéraire --- */
function updateRoute(newTruckLat, newTruckLng) {
    truckLat = newTruckLat;
    truckLng = newTruckLng;
    truckMarker.setLatLng([newTruckLat, newTruckLng]);

    // Si un itinéraire est déjà tracé et la destination définie, on le met à jour.
    if (routingControl && destinationCoords) {
        routingControl.setWaypoints([
            L.latLng(newTruckLat, newTruckLng),
            L.latLng(destinationCoords.lat, destinationCoords.lng)
        ]);
    }
}

/* --- Suivi en temps réel via la géolocalisation --- */
if (navigator.geolocation) {
    // 'watchPosition' permet de suivre en continu la position.
    navigator.geolocation.watchPosition(function (position) {
        const newTruckLat = position.coords.latitude;
        const newTruckLng = position.coords.longitude;
        updateRoute(newTruckLat, newTruckLng);
    }, function (error) {
        console.error("Erreur lors de la géolocalisation : ", error);
    }, {
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 5000
    });
} else {
    alert("La géolocalisation n'est pas supportée par ce navigateur.");
}

/* --- Gestion de l'événement pour tracer l'itinéraire vers l'adresse saisie --- */
document.getElementById('submit').addEventListener('click', function () {
    const address = document.getElementById('address').value;
    if (address.trim() === "") {
        alert("Veuillez saisir une adresse.");
        return;
    }

    // Utilisation de Nominatim pour convertir l'adresse en coordonnées.
    fetch("https://nominatim.openstreetmap.org/search?format=json&q=" + encodeURIComponent(address))
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const destLat = parseFloat(data[0].lat);
                const destLng = parseFloat(data[0].lon);
                destinationCoords = { lat: destLat, lng: destLng };

                // Ajout ou mise à jour du marqueur de destination.
                if (destinationMarker) {
                    destinationMarker.setLatLng([destLat, destLng])
                        .bindPopup("Destination : " + address)
                        .openPopup();
                } else {
                    destinationMarker = L.marker([destLat, destLng]).addTo(map)
                        .bindPopup("Destination : " + address)
                        .openPopup();
                }

                // Ajustement de la vue pour englober les deux points.
                const group = new L.featureGroup([truckMarker, destinationMarker]);
                map.fitBounds(group.getBounds(), { padding: [50, 50] });

                // Tracé de l'itinéraire et création de la polyline associée.
                tracerItineraire(destLat, destLng);

            } else {
                alert("Adresse introuvable !");
            }
        })
        .catch(error => console.error("Erreur lors de la géocodification :", error));
});