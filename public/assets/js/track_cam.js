var outTimer;
var map;
var markers = [];
var polylines = [];
var haightAshbury = '';
var defaultLoc = '';
var position = null;
var colors = ['red', 'blue', 'orange', 'pink', 'lightgreen', 'yellow'];
var colorInUse = [];
var IsBusy = false;
var iter = 0;
var allCam = [];
function getDateChange(dte) {
    //--------------
    $("#dtedeb").val(dte);   
    $("#allchauffeur").val(1);
    $("#idchauffeur").val(0);
    var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?idchauffeur=0' + '&allchauffeur=' + $("#allchauffeur").val();
    document.location = urlName;
}
function addMarker(position, cam) {
    $('#ImgTruckSani').attr("src", 'http://www.robert-daoust.ca/assets/img/google/' + cam.trim() + '.png');
    const rdfCam = document.getElementById('ImgTruckSani').src;
    const marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: rdfCam,
    });
    const infowindow = new google.maps.InfoWindow({
        position: haightAshbury,

    });
    if (parseInt($("#allchauffeur").val()) == 0) {
        map.setCenter(haightAshbury)
        //map.setZoom(18);
    }
    else {
        map.setCenter(haightAshbury);
        //map.setZoom(10);
    }

    google.maps.event.addListener(marker, "click", () => {
        infowindow.open(map, marker);
    });
    markers.push(marker);
}

function addOrUpdateMarker(position, cam) {
    // Recherche d'un marqueur pour ce camion
    var marker = markers.find(m => m.camId === cam);
    if (marker) {
        // Le marqueur existe, on met � jour sa position
        marker.setPosition(position);
    } else {
        // Cr�e le marqueur en ajoutant la propri�t� camId
        marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: {
                url: "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==",
                size: new google.maps.Size(40, 20),         // Taille de l'image (largeur x hauteur)
                anchor: new google.maps.Point(25, 25),        // Point d'ancrage de l'image sur la carte (ici, le bas du centre)
                labelOrigin: new google.maps.Point(-12, 5)    // Point d'ancrage du label (plac� sous l'image)
            },
            label: {
                text: cam,         // Le texte � afficher.
                color: "black",      // La couleur du texte.
                fontSize: "18px",  // La taille de la police.
                fontWeight: "bold" // L'�paisseur du texte.
            }

        });
        marker.camId = cam;  // Propri�t� personnalis�e pour l'identification
        //marker.idchauffeur = chauf;
        // Ajoute le listener pour le clic
        //marker.addListener('click', function () {
        //    // Appelle une fonction pour g�rer l'�v�nement click
        //    handleMarkerClick(marker);
        //});
        //// Ajout de l'�v�nement "dblclick" pour g�rer le double-clic
        //marker.addListener('dblclick', function () {
        //    handleMarkerDoubleClick(marker);
        //});

        markers.push(marker);
    }
}

function showAdresseInfoChauf() {
    var urlName = "/rapport/sani/" + $("#dtedeb").val() + "/1?nocamion=" + $("#nocamion").val();
    window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" +
        screen.width + ",height=" + screen.height);
}
function getNextColorDispo() {
    for (var iter = 0; iter < colors.length; iter++) {
        const index = colorInUse.indexOf(colors[iter]);
        if (index == -1) {
            colorInUse.push(colors[iter]);
            return colors[iter];
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
            polylines[nocamion].setMap(null);
            delete polylines[nocamion];
            var index = colorInUse.indexOf(btn.style.backgroundColor);
            colorInUse.splice(index, 1);
            btn.style.backgroundColor = "darkgrey"
        }
        else {
            $("#allchauffeur").val(0);

            document.getElementById("lbl_" + nocamion).style.backgroundColor = getNextColorDispo();
            ShowCamionPolyline(document.getElementById("lbl_" + nocamion).style.backgroundColor);
        }
    }
    else {
        map.setZoom(8);
        colorInUse.splice(0, colorInUse.length);
        colorInUse = [];
        clearPolylines();
        $("#divChauffeur").empty();
        $("#divChauffeur").html('');
        deleteMarkers();
        IsBusy = false;
        boucleShowCamionSan();
    }
}
function ShowCamionPolyline(color) {
    idchauffeur = parseInt($("#idchauffeur").val());
    nocammion = parseInt($("#nocamion").val());
    IsBusy = true;
    var urlName = '/sanivan/getpolyline/' + $("#dtedeb").val() + '?idchauffeur=' + $("#idchauffeur").val() + '&nocamion=' + $("#nocamion").val();
    $.ajax({
        url: urlName,
        async: false,
        context: document.body
    }).done(function (data) {
        
        try {
            if (data == "") {
                alert("Aucune position trouvee dans une ville pour le camion " + nocammion+ ".");
                IsBusy = false;
                return;
            }
            var newPos = data.split('_');
            if (newPos.length > 0) {
                var polyline = new google.maps.Polyline({
                    
                    strokeColor: color,
                    strokeOpacity: 1.0,
                    strokeWeight: $("#edtEpaisseur").val(), 
                    map: map
                });


                var arPos = [];
                for (var i = 0; i < newPos.length; i++) {
                    arPos = newPos[i].split(',');
                    polyline.getPath().push(new google.maps.LatLng(parseFloat(arPos[0]), parseFloat(arPos[1])));
                }
            }
                //google.maps.event.addListener(polyline, 'click', function (event) {
                //    var lat = event.latLng.lat();
                //    var lng = event.latLng.lng();
                //    var urlName = '/sanivan/getadresse?lat=' + lat + '&lng=' + lng +
                //        '&dteactive=' + $("#dtedeb").val() + '&nocamion=' + nocammion;
                //    $.ajax({
                //        async: false,
                //        type: "POST",
                //        url: urlName
                //    }).done(function (msg) {
                //        //-----------------------
                //        var objData = JSON.parse(msg);
                //        var adresse = objData.adresse;
                //        var heure = objData.hre;
                //        var place = 'Date: ' + heure + ', Adresse: ' + adresse;
                //        alert(place);
                //    });
                //});
            polylines[nocammion] = polyline;

            const path = polyline.getPath();
            // Supposons que 'map' soit votre objet Google Map et que 'polyline' soit votre google.maps.Polyline.
            const bounds = new google.maps.LatLngBounds();
            // R�cup�rer le chemin de la polyline (un MVCArray de LatLng)
            
            //var lastPosition = path.getAt(path.getLength() - 1);
            //addOrUpdateMarker(lastPosition, nocammion);
            
            path.forEach(function (latLng) {
                bounds.extend(latLng);
            });
            const paddingOptions = { top: 50, right: 50, bottom: 50, left: 50 };
            map.fitBounds(bounds, paddingOptions);
            map.setCenter(bounds.getCenter());

        } catch (error) {
            // Code qui s'ex�cute en cas d'erreur  
        } finally {
            //
        }
    });
}
function ShowCamionSan(idchauffeur) {
    idchauffeur = parseInt($("#idchauffeur").val());
    if (idchauffeur == 0) {
        $("#allchauffeur").val(1);
    } else {
        $("#allchauffeur").val(0);
    }
    var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?idchauffeur=' + idchauffeur + '&camchauf=&allchauffeur=' + $("#allchauffeur").val() + '&from=1';
    $.ajax({
        url: urlName,
        async: false,
        context: document.body
    }).done(function (data) {
        var objData = JSON.parse(data);
        $("#divChauffeur").html(objData.chauffeursRte);
        let position;
        var dataurl = objData.latlng;
        if (dataurl.includes('_')) {
            deleteMarkers();
            var arMarkers = dataurl.split(';');
            if (arMarkers.length > 0) {
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
                        const lat = parseFloat(arPos[0]);
                        const lng = parseFloat(arPos[1]);

                        if (!isNaN(lat) && !isNaN(lng)) {
                            position = new google.maps.LatLng(lat, lng);
                            addMarker(position, cam);
                        } else {
                            console.error("Les coordonn�es ne sont pas valides :", lat, lng);
                        }
                    } else {
                        console.error("Format arPos invalide :", arPos);
                    }
                }
                
            }
            

        }

    });
}
async function boucleShowCamionSan() {
    while (true) {
        try {
            //if (parseInt($("#idchauffeur").val()) > 0) {
            //    await ShowCamionPolyline();
            //    break;
            //}
            //else {
            //clearPolylines();
            if (IsBusy) {
                break;
            }
            await ShowCamionSan(0);

            //}
        } catch (error) {
            console.error("Erreur lors de l'ex�cution de ShowCamionSan :", error);
        }
        var delai = parseInt($("#edtLapRefresh").val()) * 1000; // 10 secondes
        await new Promise(resolve => setTimeout(resolve, delai ));
    }
}
// La fonction qui sera appel�e lors du clic sur un marker
//function handleMarkerClick(marker) {
//    showCam(marker.idchauffeur);
//}
//// Fonction appel�e lors du double-clic sur le marker
//function handleMarkerDoubleClick(marker) {
//    showCam(0);
//}
function hideMarkers() {
    setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
    setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
    hideMarkers();
    markers = [];
}
function setMapOnAll(map) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}
function clearPolylines() {
    for (var key in polylines) {
        if (polylines.hasOwnProperty(key)) {
            // Retire la polyline de la carte
            polylines[key].setMap(null);
        }
    }
    // R�initialisation de l'objet pour supprimer toutes les r�f�rences
    polylines = {};

}


function centerMapOnMarkers() {
    // Cr�e un objet bounds qui va englober toutes les positions
    const bounds = new google.maps.LatLngBounds();

    // Pour chaque marker, �tend les limites � sa position
    markers.forEach((marker) => {
        bounds.extend(marker.getPosition());
    });

    // Option 1 : Ajuste automatiquement le zoom pour inclure tous les markers
    map.fitBounds(bounds);

    // Option 2 : Si vous pr�f�rez simplement centrer la carte sans changer le zoom actuel,
    // vous pouvez obtenir le centre calcul� et l'appliquer :
    // map.setCenter(bounds.getCenter());
}
function showFindAdresse() {
    var selectOptions = '';
    allCam.forEach(function (value, index) {
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
    var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?&allchauffeur=' + $("#allchauffeur").val();
    document.location = urlName;
}
function getAdresseFromCam(value){
    var urlName = '/sanivan/adressefromcam/' + value + '/' + $("#dtedeb").val();
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        //-----------------------
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
        //-----------------------
        //$("#txtAdrModal").empty();
        $("#selAdrModal").html(msg);
    });
}
function createMarker(position, infoadresse) {
    const marker = new google.maps.Marker({
        position: position,
        map: map,
    });
    const infowindow = new google.maps.InfoWindow({
        content: infoadresse,
    });

    google.maps.event.addListener(marker, "click", () => {
        infowindow.open(map, marker);
    });
    markers.push(marker);
}
function goToAdresse(value) {
    var urlName = '/sanivan/gotoadresse/' + value;
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        //-----------------------
        IsBusy = true;
        var objData = JSON.parse(msg);
        
        var lat = parseFloat(objData.lat);
        var lng = parseFloat(objData.lng);
        const position = new google.maps.LatLng(lat, lng);
        var adresse = objData.adresse;
        var heure = objData.hre;
        var place = 'Date: ' + heure + ', Adresse: ' + adresse;
        $("#modalFindAdr").modal('hide');
        createMarker(position, place);
        map.setCenter(position);
        map.setZoom(18);
    });
}
//---------------------------------------------------------------------------
