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
    <link rel="stylesheet" href="/assets/plugins/timepicker/bootstrap-timepicker.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/assets/css/AdminLTE.css" />
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>  
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="/assets/plugins/iCheck/all.css">
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
  </style>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var activecedule = "<?php echo ($typecedule ?? '') ?>";
    var ActiveDrop = "<?php echo ($ActiveDrop ?? '') ?>";
    var notab = "<?php echo ($notab ?? '') ?>";
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

  </script>
</head>
<body class="hold-transition skin-blue"  onload="window.print();">
        
    <div class="box box-solid">
      <div style="margin-top:15px;text-align:center;">
        <img src="/assets/img/logo.png" />
      </div>
      <div class="box-body">
          <table id="tblhresaides" class="display responsive nowrap"  style="width: 800px;border-width: 2px;border-color: black;border:thin;" width="800">
            <thead>
                <tr style="text-align:right;border-width: 2px;border-color: black;border:thin;">
    							<td class="cellule" align="center">Nom</td>
    							<td class="cellule" align="center">Début</td>
                  <td class="cellule" align="center">Fin</td>
                  <td class="cellule" align="center">Pause</td>
                  <td class="cellule" align="center">Note</td>
                </tr>
            </thead>
            <tbody id="tboProd">
              <?php echo ($listhresaides ?? '') ; ?>
            </tbody>
          </table>
      </div>
      <!-- /.box-body -->
    </div>
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
     $(function () {
    $('#tblhresaides').DataTable({
      'paging': true,
      'responsive': true,
      "pagingType": "full",
      'lengthChange': true,
      'pageLength': 10,
      'searching': true,
      'ordering': true,
      'info': true,
      'data-page-length': '10',
      'autoWidth': false,
       'dom': "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
         "<'row'<'col-sm-12'tr>>" +
         "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      "language": {
        "sProcessing": "Traitement en cours...",
        "sSearch": "Rechercher&nbsp;:&nbsp;&nbsp;",
        "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
        "sInfo": "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
        "sInfoEmpty": "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
        "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
        "sInfoPostFix": "",
        "sLoadingRecords": "Chargement en cours...",
        "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
        "sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
        "oPaginate": {
          "sFirst": "<span><b><i class='fas fa-angle-double-left' style='font-size:28px;'></i></b>&nbsp;</span>",
          "sPrevious": "<span><b><i class='fas fa-angle-left' style='font-size:28px;'></i></b>&nbsp;</span>",
          "sNext": "<span><b><i class='fas fa-angle-right' style='font-size:28px;'></i></b>&nbsp;</span>",
          "sLast": "<span><b><i class='fas fa-angle-double-right' style='font-size:28px;'></i></b>&nbsp;</span>"
        }
      }
    })
     })

  </script>
</body>
</html>



















