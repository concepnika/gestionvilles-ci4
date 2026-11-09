    //textarea = document.getElementById('txtPoly');

    // Get all lines as an array
document.addEventListener('DOMContentLoaded', function () {
    let truckLat = 45.68372026551515, truckLng = -73.8978711515665;
    var polylineBase = null;
    var routingControl = null;
    var routePolyline = null;
    const options = {
        timeout: 10000,
        enableHighAccuracy: true,
        maximumAge: 0
    };
    // 1) Initialise la carte
    window.map = L.map('map', {
        zoomControl: true
    }).setView([45.68362, -73.89781], 15);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        { maxZoom: 19, attribution: '&copy; OSM' }
    ).addTo(map);
    ShowCamionPolyline(polylinePoints);
    // 2) Crée l'icône flèche
    //window.arrowIcon = L.divIcon({
    //    html:
    //        '<svg viewBox="0 0 24 24" width="32" height="32">' +
    //        '<path d="M12 2 L19 21 H12 L12 15 L5 21 Z" fill="#d00"/>' +
    //        '</svg>',
    //    iconAnchor: [12, 12]
    //});
    var imgUrl = 'https://www.robert-daoust.ca/assets/img/navigation.png';
    window.truckIcon = L.icon({
        iconUrl: imgUrl,
        iconSize: [75, 75],
        iconAnchor: [20, 30]
    });
    // 3) Place le marker
    window.marker = L.marker(map.getCenter(), {
        icon: truckIcon,
        rotationAngle: 0,
        rotationOrigin: 'center center'
    }).addTo(map);

    window.lastPos = null;

    // 4) Calcul du cap entre deux points
    window.getBearing = function (a, b) {
        const toRad = Math.PI / 180,
            toDeg = 180 / Math.PI;
        const φ1 = a.lat * toRad,
            φ2 = b.lat * toRad,
            Δλ = (b.lng - a.lng) * toRad;
        const y = Math.sin(Δλ) * Math.cos(φ2),
            x = Math.cos(φ1) * Math.sin(φ2)
                - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
        return (Math.atan2(y, x) * toDeg + 360) % 360;
    };
    /* --- Fonction pour tracer l'itinéraire --- */
    function tracerItineraire() {
        // Supprime l'itinéraire précédent s'il existe.
        var btn = document.getElementById("itineraireBtn");
        if (btn.innerText === "Supprimer l'itinéraire.") {
            btn.innerText = "Itinéraire";
            if (routingControl !== null) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            if (routePolyline !== null) {
                map.removeLayer(routePolyline);
                routePolyline = null;
            }
            return;
        };
        btn.innerText = "Supprimer l'itinéraire.";
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
            //itineraryContainer: document.getElementById('hidden-itinerary'),
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
            addWaypoints: false,
            createMarker: function () { return null; }
        }).addTo(map);
        
        // Lorsqu'un itinéraire est trouvé, on en extrait la géométrie pour créer une polyline.
        routingControl.on('routesfound', function (e) {
            // e.routes est un tableau contenant les itinéraires possibles
            const route = e.routes[0];
            // Les coordonnées de l'itinéraire sont généralement disponibles dans route.coordinates
            // (celles-ci sont des objets L.LatLng ou des objets {lat, lng})
            const coords = route.coordinates;
            if (routePolyline !== null) {
                map.removeLayer(routePolyline);
            }
            routePolyline = L.polyline(coords, { color: 'red', opacity: 0.7, weight: 4 }).addTo(map);
        });
    }
    window.tracerItineraire = tracerItineraire;
    function ShowCamionPolyline(plp) {
        if (plp === undefined)
            return;
        var allPos = [];
        var newPos = plp.split('_');
        if (newPos.length > 0) {
            
            var latlngs = [];
            var allPos = [];
            var arPos = [];
            for (var i = 0; i < newPos.length - 1; i++) {
                arPos = newPos[i].split(',');
                latlngs.push([parseFloat(arPos[0]), parseFloat(arPos[1])]);
                if (i === 0) {
                    destLat = parseFloat(arPos[0]);
                    destLng = parseFloat(arPos[1]);
                }
            }
            allPos.push( latlngs);
        }
        polylineBase = L.polyline(allPos, { color: 'red' }).addTo(map);
        //map.fitBounds(polyline.getBounds());

        
    }
    function rotateMap(angle) {
        var mapContainer = document.getElementById('map');
        mapContainer.style.transition = 'transform 0.3s';
        mapContainer.style.transform = 'rotate(' + angle + 'deg)';
        mapContainer.style.transformOrigin = '50% 50%';
    }
    function recenterMapAfterRotation() {
        var container = document.getElementById('map-container');
        if (!container) return; // Sécurité
        var rect = container.getBoundingClientRect();
        var centerPoint = L.point(rect.width / 2, rect.height / 2);
        var centerLatLng = map.containerPointToLatLng(centerPoint);
        map.setView(centerLatLng, map.getZoom(), { animate: false });
    }
    document.getElementById('btnResetRotation').addEventListener('click', function () {
        var mapContainer = document.getElementById('map');
        mapContainer.style.transform = 'rotate(0deg)';
    });
    function success(pos) {
        const crd = pos.coords;
        const latLng = L.latLng(crd.latitude, crd.longitude);
        truckLat = crd.latitude;
        truckLng = crd.longitude;
        if (lastPos) {
            // oriente le marker
            const cap = getBearing(lastPos, latLng);
            marker.setRotationAngle(cap);
            // Tourne la carte pour que la direction soit vers le haut
            //rotateMap(-cap);
            //recenterMapAfterRotation();
        }

        // déplace le marker
        marker.setLatLng(latLng);

        // recentre sans modifier le zoom
        map.panTo(latLng, {
            animate: true,
            duration: 0.5
        });

        lastPos = latLng;
    }
    document.getElementById('btnTestGeo').addEventListener('click', function () {
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                alert("Position reçue : " + pos.coords.latitude + ", " + pos.coords.longitude);
            },
            function (err) {
                alert("Erreur GPS : " + err.message);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
    function error(err) {
        //console.warn(`ERROR(${err.code}): ${err.message}`);
    }
    setInterval(function () {
        navigator.geolocation.getCurrentPosition(success, error, options);
    }, 5000); // toutes les 5 secondes
});