<?php

?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta charset="utf-8" />
  <title>Robert Daoust</title>
  <style>
    html, body, #map {
      height: 100%;
      margin: 0px;
      padding: 0px
    }

    #panel {
      position: absolute;
      top: 5px;
      left: 50%;
      margin-left: -180px;
      z-index: 5;
      background-color: #fff;
      padding: 5px;
      border: 1px solid #999;
    }

    #bar {
      width: 240px;
      background-color: rgba(255, 255, 255, 0.75);
      margin: 8px;
      padding: 4px;
      border-radius: 4px;
    }

    #autoc {
      width: 100%;
      box-sizing: border-box;
    }

  </style>
  
  <script src="https://www.gstatic.com/external_hosted/jquery2.min.js"></script>
  <script
    src="https://maps.googleapis.com/maps/api/js?libraries=drawing,places&key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4"></script>
  <script src="/assets/js/polyline.js"></script>
</head>

<body>
  <input type="hidden" id="idville" value="<?php echo ($idville ?? ''); ?>" />
  <input type="hidden" id="idroute" value="<?php echo ($idroute ?? ''); ?>" />
  <div id="map"></div>
  <div id="bar">
    <p class="auto"><input type="text" id="autoc" value="<?php echo ($ville ?? ''); ?>" /></p>
    <p><a id="clear" href="#">Cliquer ici</a> pour effacer la carte.</p>
  </div>

</body>
</html>