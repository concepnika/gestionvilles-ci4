
<?php


?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="refresh" content="40" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Robert Daoust</title>

  <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js" ie.="" data-type=""></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/fullcalendar.min.css" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/fullcalendar.min.js "></script>
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <script type="text/javascript" src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/Ionicons/css/ionicons.min.css" />
  <!-- fullCalendar -->
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.min.css" />
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.print.min.css" media="print" />
  <!-- Theme style -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
   <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/bower_components/Ionicons/css/ionicons.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <!-- Morris chart -->
  <link rel="stylesheet" href="/assets/bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/assets/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="/assets/plugins/iCheck/all.css">
    <script src="/assets/datatables/dataTables.bootstrap.js"></script>
<script src="/assets/datatables/jquery.dataTables.js"></script>
<script src="/assets/datatables/extras/Buttons-1.6.1/js/dataTables.buttons.min.js"></script>
<script src="/assets/datatables/extras/Buttons-1.6.1/js/buttons.bootstrap4.min.js"></script>
<script src="/assets/datatables/extras/Buttons-1.6.1/js/buttons.html5.min.js"></script>
<script src="/assets/datatables/extras/Buttons-1.6.1/js/buttons.print.min.js"></script>
  <script src="/assets/datatables/extras/js/RowReorder-1.2.6/rowReorder.foundation.js"></script>
<script src="/assets/datatables/extras/js/RowReorder-1.2.6/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/pagination/four_button.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
  <style>
    .popover-title{
      background: darkblue;
      color: white;
      font-weight:bold;
      min-width: 200px;
    }
    .popover-content{
      background: lightgray;
      color: darkblue;
      min-width: 200px;
    }
    @media (max-width: 967px) {
	    .titre-grille {border-bottom: none; border-top: none; margin:5px 0;}
	    .filtre {visibility:visible;}
	    .filtre .navbar-toggle {float: none; padding: 9px 10px; margin-top:0px; margin-bottom: 8px; background-color: #009ddc; border-radius: 4px;}
	    .filtre .navbar-header {padding: 5px 0px 10px 0px;}
	    .filtre .navbar-header a {font-size:11px; font-weight:bold; color:#ca006c; cursor:pointer; text-decoration:none;}
	    .filtre .navbar-header a .fa {font-size:24px; color:#ca006c; cursor:pointer;}
	    .filtre .navbar-toggle:focus {outline: 0;}
	    .filtre .navbar-toggle:hover {background-color: #999;}
	    .filtre .navbar-collapse {padding-right: 0px; padding-left: 0px;  border-bottom:thin solid #999}
	    .filtre .navbar-collapse li {border-top:thin solid #999}
	    .filtre .nav > li > a {padding: 10px 0px;}
	    .filtre .selection {height:25px; font-size:15px; font-weight:bold; color:#009ddc; text-transform:uppercase;}
    }

    input[type="time"]::-ms-clear {
      display: none;
    }
    <?php echo (($from != 3)? 'body {overflow: hidden;}' : ''); ?>
    

  </style>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var activecedule = "<?php echo ($typecedule ?? '') ?>";
    var ActiveDrop = "<?php echo ($ActiveDrop ?? '') ?>";
    var notab = "<?php echo ($notab ?? '') ?>";
    var AideIsShow = "<?php echo ($AideIsShow ?? '') ?>";
    var from = "<?php echo ($from ?? '') ?>";
    var activeRH = 1;
    function ConfirmeLoad(Route)
    {
        $("#td_" + Route).css("background-color","#80FF9F");
        $("#chk_" + Route).css("background-color","#80FF9F");
    }
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover();
    });
    $(document).ready(function () {
      $('[data-toggle="popover"]').popover();

    });
    /* Get the documentElement (<html>) to display the page in fullscreen */
    var elem = document.documentElement;

    /* View in fullscreen */
    function openFullscreen() {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.webkitRequestFullscreen) { /* Safari */
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) { /* IE11 */
        elem.msRequestFullscreen();
      }
    }

    /* Close fullscreen */
    function closeFullscreen() {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) { /* Safari */
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) { /* IE11 */
        document.msExitFullscreen();
      }
    }
  </script>
</head>
<body class="hold-transition skin-blue" style="transform: scale(0.89);transform-origin: 2% 2%" onresize="ChangeSizeOfDiv(this)">
  <!-- Header Navbar: style can be found in header.less -->





  <div id="divShowCed" style="margin-left: 5px;">
    

    <div id="divCedule">
      <?php
                echo ($today ?? '');
      ?>
    </div>
    <!-- Horodatage du dernier rendu serveur: permet de verifier d'un
         coup d'oeil que la TV se recharge bien (heure + journee). -->
    <div style="text-align:right;font-size:11px;color:#777;margin-right:10px;">
      C&eacute;dule du <?php echo ($ActiveDate ?? ''); ?> &mdash; charg&eacute;e &agrave; <?php echo date('H:i:s'); ?>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /. box -->

  <script src="/assets/jquery-ui/jquery-ui.min.js"></script>
  <!-- Slimscroll -->
  <script src="/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="/assets/fastclick/lib/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="/assets/js/adminlte.min.js"></script>

  <!-- fullCalendar -->
  <script src="/assets/moment/moment.js"></script>
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
  <script src="/assets/js/nika.js"></script>
  <script>
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";


    function getChauffeur(value) {
      //--------------
      var urlName = '/sanivan/getchauffeur/' + value + '/' + $("#dteActivePage1").val() + '?id=' + ActiveCedule;
	    $.ajax({
		    async: false,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
		    if (msg != '') {
			    $("#chauffeur").html(msg);
		    }
	    });
    }
    function getRhToDrop(value) {
      //--------------
      activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
      var urlName = '/sanivan/rhtodrop/' + value + '/' + $("#dteActivePage1").val() + '?action=' + activeRH;
	    $.ajax({
		    async: false,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
			    $("#divDropRH").html(msg);
	    });
    }
    function selGetCedule(id, limit) {
      limit = (($("#" + id).is(':checked')) ? limit : 0);
      chkid = '';
      limitcedule = limit;
      if (limit > 0) {
        chkid = id;
      }
      var urlName = "/sanivan/aujourdhui/4/" + $("#dteActivePage1").val() + '?typecedule=' + activecedule +
        '&activedrop=' + ActiveDrop + '&notab=' + notab + '&limit=' + limitcedule + '&typerh=' +  $("#typechauf").val();
	    $.ajax({
		    async: true,
		    type: "POST",
		    url: urlName
	    }).done(function (data) {
		    if (data !== "") {
			    var objData = JSON.parse(data);
			    $("#divCreate").html(objData.createtoday);
			    $("#divCedule").html(objData.today);
			    $("#spRearEnd").html(objData.spRearEnd);
			    $("#spRobot").html(objData.nbRobot);
			    $("#spTotalCam").html(objData.nbAllCam);
			    $("#tboRHhres").html(objData.listhresaides);
          $("#dteRH").val(objData.dtetoday);
          $("#chkRobot").prop('checked', false);
          $("#chkRearLoad").prop('checked', false);
          if (limit > 0) {
            $("#" + id).prop('checked', true);
          }
		    }
	    });
    }
    function modalConfirm() {
      $("#dteEnvoi").val('');
      $("#modalShowSend").modal("show");
    }
    // Journee actuellement affichee (le serveur force toujours "aujourd'hui"
    // au rendu; cette valeur sert a detecter un changement de jour).
    var TV_DISPLAYED_DATE = "<?php echo ($ActiveDate ?? '') ?>";

    function tvReload() {
      window.location.href = window.location.pathname + '?ts=' + Date.now();
    }
    function tvTodayStr() {
      var d = new Date();
      var m = '' + (d.getMonth() + 1), j = '' + d.getDate();
      return d.getFullYear() + '-' + (m.length < 2 ? '0' + m : m) + '-' + (j.length < 2 ? '0' + j : j);
    }
    function tvCheckDay() {
      // Si la journee affichee n'est plus celle d'aujourd'hui, on recharge.
      // Couvre le cas ou la TV/WebView est suspendue la nuit: les
      // setInterval et le meta refresh ne s'executent plus pendant la
      // suspension; au reveil, ce controle declenche le rechargement.
      if (tvTodayStr() != TV_DISPLAYED_DATE) { tvReload(); }
    }

    // Le reload est enregistre AVANT le plein ecran: requestFullscreen()
    // peut lancer une exception si le navigateur le refuse (sans geste
    // utilisateur), ce qui empêchait l'enregistrement du setInterval et
    // donc le rafraichissement automatique de la page TV.
    setInterval(tvReload, 40 * 1000);
    setInterval(tvCheckDay, 15 * 1000);
    // Controle supplementaire quand la page redevient visible/active
    // (reveil de la TV, WebView relachee, retour de bfcache).
    document.addEventListener('visibilitychange', function () {
      if (!document.hidden) { tvCheckDay(); }
    });
    window.addEventListener('focus', tvCheckDay);
    window.addEventListener('pageshow', function (e) {
      if (e.persisted) { tvCheckDay(); }
    });
    function tryFullscreen() {
      try {
        var p = document.documentElement.requestFullscreen();
        if (p && p.catch) { p.catch(function () { }); }
      } catch (e) { }
    }
    tryFullscreen();
  </script>
</body>
</html>



















