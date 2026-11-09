var gLat = 0;
var gLng = 0;
var Timeout = -1;
var setInterval = '';
var track = {
  // (A) INIT
  rider : 999,   // rider id - fixed to 999 for demo
  delay : 8000, // delay between gps update (ms)
  timer : null,  // interval timer
  hDate : null,  // html date
  hLat : null,   // html latitude
  hLng : null,   // html longitude
  init : () => {
    // (A1) GET HTML
      track.hDate = new Date();

    // (A2) START TRACKING
    track.update();
    //track.timer = setInterval(track.update, track.delay);
  },

  // (B) SEND CURRENT LOCATION TO SERVER
  update : () => navigator.geolocation.getCurrentPosition(
    pos => {
        // (B1) LOCATION DATA
        var data = new FormData();
        gLat = pos.coords.latitude;
          gLng = pos.coords.longitude;
          if ((haightAshbury.lat == gLat) && (haightAshbury.lng == gLng)) {
              return;
          }
          else {
              haightAshbury = { lat: gLat, lng: gLng };
              map.setCenter(haightAshbury)
              deleteMarkers();
              addMarker(haightAshbury);

            data.append("req", "update");
            data.append("lat", gLat);
            data.append("lng", gLng);

            // (B2) AJAX SEND TO SERVER
              fetch("/tablettes_san/track", { method: "POST", body: data })
                .then(res => res.text())
                .then(txt => {
                    if (txt == "OK") {

                        //window.initMap = initMap;
                        //let now = new Date();
                        //track.hDate.innerHTML = now.toString();
                        //track.hLat.innerHTML = pos.coords.latitude;
                        //track.hLng.innerHTML = pos.coords.longitude;
                    } else { track.error(txt); }
                })
          }

        //.catch(err => track.error(err));
      },
    err => track.error(err)
  ),

  // (C) HELPER - ERROR HANDLER
  error : err => {
    console.error(err);
    //alert("An error has occured, open the developer's console.");
    //clearInterval(track.timer);
  }
};

const options = {
    timeout: 8000,
    enableHighAccuracy: true,
    maximumAge: 0
};

var map;
var markers = [];
var haightAshbury = '';


function initMap() {
    haightAshbury = { lat: 45.0562259, lng: -73.9026788 };
    //---------------------------------------------------------

    //const infowindow = new google.maps.InfoWindow();
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 16,
        heading: 320,
        tilt: 47.5,
        mapId: "90f87356969d889c",
    });
    //---------------------------------------------------------
    const buttons = [
        ["Rotate Left", "rotate", 20, google.maps.ControlPosition.LEFT_CENTER],
        ["Rotate Right", "rotate", -20, google.maps.ControlPosition.RIGHT_CENTER],
        ["Tilt Down", "tilt", 20, google.maps.ControlPosition.TOP_CENTER],
        ["Tilt Up", "tilt", -20, google.maps.ControlPosition.BOTTOM_CENTER],
    ];

    buttons.forEach(([text, mode, amount, position]) => {
        const controlDiv = document.createElement("div");
        const controlUI = document.createElement("button");

        controlUI.classList.add("ui-button");
        controlUI.innerText = `${text}`;
        controlUI.addEventListener("click", () => {
            adjustMap(mode, amount);
        });
        controlDiv.appendChild(controlUI);
        map.controls[position].push(controlDiv);
    });

    const adjustMap = function (mode, amount) {
        switch (mode) {
            case "tilt":
                map.setTilt(map.getTilt() + amount);
                break;
            case "rotate":
                map.setHeading(map.getHeading() + amount);
                break;
            default:
                break;
        }
    };
    //---------------------------------------------------------
    //document.getElementById("submit").addEventListener("click", () => {
    //    geocodeLatLng(geocoder, map, infowindow);
    //});
    //---------------------------------------------------------

    // This event listener will call addMarker() when the map is clicked.
    //map.addListener("click", (event) => {
    //    const latlng = event.latLng;
    //    var lat = latlng.lat();
    //    addMarker(event.latLng);
    //});
    map.addListener("zoom_changed", () => {
        const zoom = map.getZoom();
        map.setZoom(zoom);
    });
    // add event listeners for the buttons
    document
        .getElementById("show-markers")
        .addEventListener("click", showMarkers);
    document
        .getElementById("hide-markers")
        .addEventListener("click", hideMarkers);
    document
        .getElementById("delete-markers")
        .addEventListener("click", deleteMarkers);
    //map.setTilt(90);
    // Adds a marker at the center of the map.
    addMarker(haightAshbury);
}

// Adds a marker to the map and push to the array.
function addMarker(position) {
    //var lat = position.lat;
    //var lng = position.lng;
    //alert(lat);
    //alert(lng);
    const truckFlagImg = document.getElementById('ImgTruckSani').src;
    const marker = new google.maps.Marker({
        position,
        map,
        icon: truckFlagImg,
    });

    markers.push(marker);
}
//function adjustMap(mode, amount) {
//    switch (mode) {
//        case "tilt":
//            var tilt = map.getTilt();
//            map.setTilt(map.getTilt() + amount);
//            break;
//        case "rotate":
//            var heading = map.getHeading();
//            map.setHeading(map.getHeading() + amount);
//            break;
//        default:
//            break;
//    }
//}
// Sets the map on all markers in the array.
function setMapOnAll(map) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
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
function success(pos) {
    const crd = pos.coords;
    haightAshbury = { lat: crd.latitude, lng: crd.longitude };
    map.setCenter(haightAshbury)
    deleteMarkers();
    addMarker(haightAshbury);
    //var urlName = "/tablettes_san/track?lat=" + haightAshbury.lat + "&lng=" + haightAshbury.lng;
    //$.ajax({
    //    url: urlName,
    //    context: document.body
    //}).done(function (data) {
    //    //------------------

    //});
}
function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
}
//function RefreshTrack() {
//    idZoom = navigator.geolocation.watchPosition(
//        success,
//        error,
//        options
//    );
//}


//function postPosition() {
//    var urlName = "/tablettes_san/track?lat=" + haightAshbury.lat + "&lng=" + haightAshbury.lng;
//    $.ajax({
//        url: urlName,
//        context: document.body
//    }).done(function (data) {
//        //------------------

//    });
//    map.setCenter(haightAshbury)
//}
window.initMap = initMap;
//navigator.geolocation.watchPosition(
//            success,
//            error,
//            options
//);
//myInterval = setInterval(postPosition, 8000);