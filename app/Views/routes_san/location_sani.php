
<?php

?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Robert Daoust</title>

		<meta name="keywords" content="Robert Daoust" />
		<meta name="description" content="Sanivan routes - Responsive HTML5 Template">
		<meta name="author" content="Conceptnika">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<?php echo view('requetes/incfile/inc_head'); ?>
    <style>

      ul li {
        display: inline;
      }
        /*
        * Always set the map height explicitly to define the size of the div element
        * that contains the map.
        */
      #map {
        height: 900px;
        width: 100%;
      }

      /*
      * Optional: Makes the sample page fill the window.
      */
      .test {
        height: 35px;
        width: 200px;
        font-size: 16px;
      }

      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: "Roboto", "sans-serif";
        line-height: 30px;
        padding-left: 10px;
      }
    </style>


    <script type="text/javascript">

      //-------------------------------------------------------
      var centerMap = <?php echo ($center ?? ''); ?>;
      var parcoursMap = <?php //echo ($parcours ?? ''); ?>;

    </script>



  </head>
<body >
  <div id="map"></div>
  <div id="divimgtruck" style="display:none;">
                          <img id="ImgTruckSani" src="<?php echo ci3_url('/assets/img/google/truck2.png'); ?>" width="30" alt="" />
    </div>




  <script src="/assets/pronto/vendor/jquery/jquery.js"></script>
  <script src="/assets/pronto/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
  <script src="/assets/pronto/vendor/popper/umd/popper.min.js"></script>
  <script src="/assets/pronto/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/pronto/vendor/common/common.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.1.0/addons/bootstrap/jquery.smartmenus.bootstrap.min.js" integrity="sha256-yQjrAGXVhM4+j4+qFh1W4wg7dPC3IjJ9BId2RO3peEY=" crossorigin="anonymous"></script>
  <!-- Specific Page Vendor -->
  <script>

</script>
  <script src="/assets/js/track_location.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdPmCQLUxygBW27yZb9ZNRAc9XwIY_skU&callback=initMap&libraries=geometry"
    async defer></script>  
  
  <script src="/assets/js/sanivan.js"></script>

  

</body>
</html>