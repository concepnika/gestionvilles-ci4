<?php


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
    .titre{
      color: darkblue;
      width: 100%;
      height:100%;
      font-size: large;
      font-weight:bold;
    }
    .separateur{
      color: white;
      width: 100%;
      height:35px;
      padding-top:5px;
      font-size: large;
      font-weight:bold;
      background-color: black;
      text-align:center;
    }
    .li-info{
      background-color: lightblue;
      text-align:center;
    }
  </style>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var IsLoaded = <?php echo ($IsLoaded ?? '') ?>;
    var IsRap = <?php echo ($IsRap ?? '') ?>;
    var myTimer = 0;
    var ToShow = 4;
    var EndIsChecked = 0;
    
  </script>
</head>
<body class="hold-transition skin-blue" onload="window.print();">


    <div class="wrapper" style="margin-top: 5%;margin-left:10px;margin-right:15px;">
      <div stlye="left:500px">
        <img src="https://www.robert-daoust.ca/assets/img/logo.png" alt="Robert Daoust et fils" width="250" />
      </div>

      <h1><?php echo ($Titre ?? '') ; ?></h1>
      <br /> <br />
      <div id="divChauffeurs" style="max-width: 700px;margin-top:30px;display:<?php echo (($IsRap == 1)? 'none' : 'block') ; ?>" class="column">
        <table width="700" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th align="center"></th>
              <th align="center">CHAUFFEUR</th>
              <th align="center">CAMION</th>
              <th align="center">KM DÉPART</th>
              <th align="center">KM FIN</th>
              <th align="center">KM</th>
              <th align="center">CARBURANT</th>
              <th>HRE DÉBUT</th>
              <th>HRE FIN</th>
              <th align="center">HRES TRAV.</th>
              <th>
                <button type="button" class="btn btn-primary" onclick="addSuivis()" data-toggle="popover" data-trigger="hover" data-placement="left" title="Confirmer" data-content="Cliquer pour confirmer la journée et créer le rapport du jour.">
                  <i class='fa fa-save' style='font-size:18px;'></i>&nbsp;
                </button>
              </th>
              <th align="center"></th>
            </tr>
          </thead>
          <tbody>
            <?php echo ($rapdaychauf_cam ?? '') ; ?>
          </tbody>
        </table>
      </div>

      <div id="divRapJour" style="display:<?php echo (($IsRap == 1)? 'block' : 'none') ; ?>" class="column">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th align="center"></th>
              <th align="center"># CAMION</th>
              <th align="center">
                KM<br />DÉPART
              </th>
              <th align="center">
                KM<br />ARRIVÉE
              </th>
              <th align="center">
                KM<br />PAR.
              </th>
              <th align="center">
                REV.<br />BRUT
              </th>
              <th align="center">
                ESS.<br />LITRES
              </th>
              <th align="center">
                ESS.<br />COÛT
              </th>
              <th>
                HRE<br />CHAUF.
              </th>
              <th>
                HRE<br />COÛT
              </th>
              <th align="center">ENF.</th>
              <th align="center">
                REV<br />NET
              </th>
              <th align="center">
                MOYENNE<br />REV/NET
              </th>
            </tr>
          </thead>
          <tbody>
            <?php echo ($raprojour ?? '') ; ?>
          </tbody>
        </table>

      </div>

  </div>
  
    <script src="/assets/jquery-ui/jquery-ui.min.js"></script>
    <!-- Slimscroll -->
    <script src="/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="/assets/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="/assets/js/adminlte.min.js"></script>
    <script src="/assets/js/jquery.dialog.js"></script>
    <!-- fullCalendar -->
    <script src="/assets/moment/moment.js"></script>
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
    <script src="/assets/js/nika.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
    var ActiveRow = 0; 

  </script>
</body>
</html>



















