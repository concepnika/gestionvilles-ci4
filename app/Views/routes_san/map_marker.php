<?php

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>
    Robert Daoust et fils
  </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

  <style type="text/css">
    .container{
      padding: 2%;
      text-align: center;

     }
     #map_wrapper_div {
      height: 400px;
    }

    #map {
        width: 100%;
        height: 100%;
    }
  </style>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

  
</head>

<body>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="alert alert-success">
          <h2>Localisation des Camions</h2>
        </div>
        <div id="map_wrapper_div">
          <div id="map"></div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyADU5c1vJHNLFq8s2n2eF4BjFm2XvSPc-Y&callback=initMap&v=weekly" defer></script>
  <script>
    let map;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(45.44315643210121,-73.87569408556601),
        zoom: 16,
    });

  var markers = JSON.parse(`<?php echo ($markers); ?>`);
  var infoWindowContent = JSON.parse(`<?php echo ($infowindow); ?>`);
            var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (let i = 0; i < markers.length; i++) {
        const marker = new google.maps.Marker({
            position: new google.maps.LatLng(markers[i][1], markers[i][2]),
            map: map,
        });
    }
}

window.initMap = initMap;
  </script>
</body>
</html>
