var map;
var markers = [];
var polylines = {};
var IsBusy = false;
var colors = ['red', 'blue', 'orange', 'pink', 'lightgreen', 'yellow'];
var colorInUse = [];
var allCam = [];
var continuePolling = true;

// Tableau pour stocker les groupes
var groups = [];

// Fonction utilitaire pour créer un divIcon
function createCustomDivIcon(label) {
    return L.divIcon({
        html: '<div class="custom-div-icon">' + label + '</div>',
        iconSize: [80, 30],
        className: "" // On utilise notre propre classe pour le style
    });
}
//const today = new Date();
//const isoDate = today.toISOString().split('T')[0]; 
//$("#dtedeb").val(isoDate);
// Initialisation de la carte avec Leaflet
function initMap() {
    var defaultLoc = [45.3991735, -74.0780621]; // [lat, lng]
    //var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    //      maxZoom: 18,
    //      attribution: 'Tiles © Esri — Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    //  });
    //map = L.map('map').setView(defaultLoc, 14,
    //    layers: [streetLayer]);
    // Définition des couches de tuiles pour la carte
    var streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    });

    var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    });
    // Initialisation de la carte avec la couche "route"
    map = L.map('map', {
        center: defaultLoc, // Exemple Montréal
        zoom: 13,
        layers: [streetLayer] // Couche active au départ
    });

    // Ajout d'un bouton personnalisé pour basculer entre satellite et route
    var toggleButton = L.control({ position: 'topright' });

    toggleButton.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        div.innerHTML = '🌍 Satellite';

        // Styles pour agrandir le bouton
        div.style.backgroundColor = 'white';
        div.style.padding = '10px 20px'; // Augmente l'espace interne
        div.style.fontSize = '18px'; // Taille du texte plus grande
        div.style.fontWeight = 'bold'; // Rend le texte en gras
        div.style.borderRadius = '8px'; // Arrondi les coins
        div.style.cursor = 'pointer';
        div.style.boxShadow = '0px 2px 5px rgba(0,0,0,0.3)'; // Ajoute une ombre légère
        div.style.border = '2px solid #000'; // Bordure noire visible

        div.onclick = function () {
            if (map.hasLayer(streetLayer)) {
                map.removeLayer(streetLayer);
                map.addLayer(satelliteLayer);
                div.innerHTML = '🛣 Route';
            } else {
                map.removeLayer(satelliteLayer);
                map.addLayer(streetLayer);
                div.innerHTML = '🌍 Satellite';
            }
        };

        return div;
    };

    // Ajout du bouton sur la carte
    toggleButton.addTo(map);
    boucleShowCamionSan();
}

function getDateChange(dte) {
    $("#dtedeb").val(dte);
    $("#allchauffeur").val(1);
    $("#idchauffeur").val(0);
    var urlName = base_url + 'rolloff/routecam/' + $("#dtedeb").val() + '?idchauffeur=0&allchauffeur=' + $("#allchauffeur").val();
    document.location = urlName;
}

// Ajoute un marqueur sur la carte avec une icône personnalisée pour le camion
function addMarker(position, cam) {
    var imgUrl = 'https://www.robert-daoust.ca/assets/img/google/' + cam.trim() + '.png';
    var truckIcon = L.icon({
        iconUrl: imgUrl,
        iconSize: [40, 60],
        iconAnchor: [20, 30]
    });
    var marker = L.marker([position.lat, position.lng], { icon: truckIcon }).addTo(map);
    marker.bindPopup("Camion: " + cam);
    markers.push(marker);
}

// Ajoute ou met à jour un marqueur en fonction d'un identifiant de camion
function addOrUpdateMarker(position, cam) {
    var marker = markers.find(m => m.options.camId === cam);
    if (marker) {
        marker.setLatLng([position.lat, position.lng]);
    } else {
        // Création d'un marqueur avec un label intégré via un tooltip permanent
        marker = L.marker([position.lat, position.lng]).addTo(map);
        marker.options.camId = cam;
        marker.bindTooltip(cam, { permanent: true, direction: 'bottom', className: 'leaflet-label' });
        markers.push(marker);
    }
}

// Affiche une polyline pour un camion en récupérant les coordonnées via AJAX
function ShowCamionPolyline() {
    var idchauffeur = parseInt($("#idchauffeur").val());
    var nocamion = $("#nocamion").val();
    IsBusy = true;
    var urlName = base_url + 'rolloff/getpolyline/' + $("#dtedeb").val() +
        '?idchauffeur=' + idchauffeur + '&nocamion=' + nocamion;
    $.ajax({
        url: urlName,
        async: false,
        context: document.body
    }).done(function (data) {
        try {
            if (data == "") {
                alert("Aucune position trouvée dans une ville pour le camion " + nocamion + ".");
                IsBusy = false;
                return;
            }
            var pointsPoly = data.split('|');
            if (pointsPoly.length > 0) {
                for (var iterpoly = 0; iterpoly < pointsPoly.length; iterpoly++) {
                    var group = L.layerGroup();
                    var newPos = pointsPoly[iterpoly].split('_');
                    if (newPos.length > 0) {

                        var latlngs = [];
                        for (var i = 0; i < newPos.length; i++) {
                            var arPos = newPos[i].split(',');
                            if (arPos.length >= 2) {
                                latlngs.push([parseFloat(arPos[0]), parseFloat(arPos[1])]);
                            }
                        }
                        document.getElementById("lbl_" + nocamion).style.backgroundColor = getNextColorDispo();
                        var color = document.getElementById("lbl_" + nocamion).style.backgroundColor;
                        var polyline = L.polyline(latlngs, { color: color, weight: parseInt($("#edtEpaisseur").val()) }).addTo(map);
                        polyline.addTo(group);
                        var startMarker = L.marker(latlngs[0], { icon: createCustomDivIcon("Camion 129<br>Début") });
                        startMarker.bindPopup("Camion 129 – Début (Groupe " + (i + 1) + ")");
                        startMarker.addTo(group);

                        var endMarker = L.marker(latlngs[latlngs.length - 1], { icon: createCustomDivIcon("Camion 129<br>Fin") });
                        endMarker.bindPopup("Camion 129 – Fin (Groupe " + (i + 1) + ")");
                        endMarker.addTo(group);
                        group.addTo(map);
                        groups.push(group);
                    }
                }
                // Calcul des limites englobant toutes les polylines
                var overallBounds = L.latLngBounds([]);
                groups.forEach(function (group) {
                    group.eachLayer(function (layer) {
                        // Seule la polyline étend ses limites sur le LatLngBounds.
                        if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                            overallBounds.extend(layer.getLatLngs());
                        }
                    });
                });
                // Centrer la carte sur l'ensemble des polylines et ajuster le zoom
                if (overallBounds.isValid()) {
                    map.fitBounds(overallBounds, { padding: [50, 50] });
                } else {
                    console.warn("Aucune polyline n'a été trouvée pour calculer le centre");
                }
            }

        } catch (error) {
            console.error("Erreur dans ShowCamionPolyline :", error);
        }
    });
}

function deleteMarkers() {
    markers.forEach(function (marker) {
        map.removeLayer(marker);
    });
    markers = [];
}

function clearPolylines() {
    for (var key in polylines) {
        if (polylines.hasOwnProperty(key)) {
            map.removeLayer(polylines[key]);
        }
    }
    polylines = {};
}
function removeAllGroups() {
    groups.forEach(function (group) {
        // Option 1 : Retirer le groupe de la carte
        map.removeLayer(group);
        // Option 2 : Vider le groupe des couches si tu souhaites le garder pour réutilisation ultérieure
        // group.clearLayers();
    });
    // Si nécessaire, vider également le tableau des groupes
    groups = [];
}
function centerMapOnMarkers() {
    if (markers.length === 0) return;
    var group = L.featureGroup(markers);
    map.fitBounds(group.getBounds(), { padding: [50, 50] });
}

function showAdresseInfoChauf() {
    var urlName = "/rapport/sani/" + $("#dtedeb").val() + "/1?nocamion=" + $("#nocamion").val();
    window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" +
        screen.width + ",height=" + screen.height);
}

function getNextColorDispo() {
    for (var i = 0; i < colors.length; i++) {
        if (colorInUse.indexOf(colors[i]) === -1) {
            colorInUse.push(colors[i]);
            return colors[i];
        }
    }
}

function showCam(idchauffeur, nocamion) {
    IsBusy = true;
    $("#idchauffeur").val(idchauffeur);
    $("#nocamion").val(nocamion);
    if (idchauffeur > 0) {
        var btn = document.getElementById("lbl_" + nocamion);
        if (btn.style.backgroundColor != "darkgrey") {
            if (polylines[nocamion])
                map.removeLayer(polylines[nocamion]);
            var index = colorInUse.indexOf(btn.style.backgroundColor);
            colorInUse.splice(index, 1);
            btn.style.backgroundColor = "darkgrey";
        } else {
            $("#allchauffeur").val(0);
            
            ShowCamionPolyline();
        }
    } else {
        map.setView([45.3991735, -74.0780621], 8);
        colorInUse.splice(0, colorInUse.length);
        removeAllGroups();
        $("#divChauffeur").empty();
        //deleteMarkers();
        IsBusy = false;
        boucleShowCamionSan();
    }
}

function ShowCamionSan(idchauffeur) {
    idchauffeur = parseInt($("#idchauffeur").val());
    if (idchauffeur == 0) {
        $("#allchauffeur").val(1);
    } else {
        $("#allchauffeur").val(0);
    }
    var urlName = base_url + 'rolloff/routecam/' + $("#dtedeb").val() +
        '?idchauffeur=' + idchauffeur + '&camchauf=&allchauffeur=' + $("#allchauffeur").val() + '&from=1';
    $.ajax({
        url: urlName,
        async: false,
        context: document.body
    }).done(function (data) {
        var objData = JSON.parse(data);
        $("#divChauffeur").html(objData.chauffeursRte);
        var dataurl = objData.latlng;
        if (dataurl.includes('_')) {
            deleteMarkers();
            var arMarkers = dataurl.split(';');
            allCam.length = 0;
            for (var i = 0; i < arMarkers.length; i++) {
                var ardata = arMarkers[i].split('_');
                var cam = ardata[0];
                allCam.push(cam);
                if ($("#allchauffeur").val() == 0) {
                    $("#nocamion").val(cam);
                }
                var arPos = ardata[1].split(',');
                if (arPos.length >= 2) {
                    var lat = parseFloat(arPos[0]);
                    var lng = parseFloat(arPos[1]);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        var position = { lat: lat, lng: lng };
                        addMarker(position, cam);
                    } else {
                        console.error("Coordonnées invalides :", lat, lng);
                    }
                } else {
                    console.error("Format arPos invalide :", arPos);
                }
            }
        }
    });
}

async function boucleShowCamionSan() {
    while (continuePolling) {
        try {
            if (IsBusy) {
                break;
            }
            await ShowCamionSan(0);
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
    var urlName = base_url + 'rolloff/routecam/' + $("#dtedeb").val() + '?&allchauffeur=' + $("#allchauffeur").val();
    document.location = urlName;
}

function getAdresseFromCam(value) {
    var urlName = base_url + 'rolloff/adressefromcam/' + value + '/' + $("#dtedeb").val();
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
    var urlName = base_url + 'rolloff/adressetofind/' + $("#selCamModal").val() + '/' + $("#dtedeb").val()
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
    var urlName = base_url + 'rolloff/gotoadresse/' + value;
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
    });
}

// Lancement de l'initialisation une fois le DOM chargé
document.addEventListener("DOMContentLoaded", initMap);