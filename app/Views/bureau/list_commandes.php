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
  <link rel="icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />

  <link href="style.css" rel="stylesheet" type="text/css" />
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
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
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
  </style>
  <script src="/assets/css/fontawesome/all.css"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo date('Y-m-d') ?>";
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover();   
    });
  </script>
</head>
<body class="hold-transition skin-blue">

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <!-- Sidebar toggle button-->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>

      <?php
      switch ($privilege)
      {
        case 1:
        case 6:
          echo view('sections/menu_bureau');
          break;
        default:
          echo view('sections/menu');
      }
      ?>
    </nav>

  <div class="wrapper" style="margin-top: 5%;">
            <header class="page-header">
              <div id="myNavbar" style="margin-left:5%;">
                <ul class="nav navbar-nav">
                  <li>
                    <label class="form-control" style="font-size:medium;font-weight:bold;">Liste des commandes</label>
                  </li>
                  <li>&nbsp;&nbsp;</li>
                  <li>
                    <div class="btn-group-horizontal">
                      <input type="radio" name="radtype" id="radNormal" value="1" class="btn btn-primary" />&nbsp;Normal&nbsp;&nbsp;
                      <input type="radio" name="radtype" id="radMultiple" value="2" class="btn btn-primary" />&nbsp;Multiple&nbsp;&nbsp;
                      <input type="radio" checked name="radtype" id="radAll" value="3" class="btn btn-primary" />&nbsp;Tous les types
                    </div>
                  </li>
                  <li>&nbsp;&nbsp;</li>
                  <li>
                    <div class="input-group input-group-sm" style="width:200px; ">
                      <span class="input-group-btn">
                        <a class="btn btn-info btn-flat" href="javascript:LoadTodayListCommande('-1')" id="divShowHideEmp" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journée précédente" data-content="Affiche la cédule de la journée précédente">
                          <i class='fa fa-angle-left' style='font-size:24px;'></i>&nbsp;
                        </a>
                      </span>
                      <input type="date" class="form-control" onchange="LoadTodayListCommande('0')" data-toggle="popover" style="width:130px;" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ?>" id="dteActive" />
                      <span class="input-group-btn">
                        <a class="btn btn-info btn-flat" href="javascript:LoadTodayListCommande('1')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journée suivante" data-content="Affiche la cédule de la journée suivante">
                          <i class='fa fa-angle-right' style='font-size:24px;'></i>&nbsp;
                        </a>
                      </span>
                    </div>
                  </li>
                  <li>&nbsp;&nbsp;</li>
                  <li>&nbsp;&nbsp;</li>
                  <li>
                    <div class="input-group input-group-sm" style="width:230px">
                      <input type="text" id="edtFind" class="form-control" onkeyup="ReturnsearchTable(this.value, event)" placeholder="Rechercher" style="width:200px;" />
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" id="search" onclick="searchTable()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affiche la cédule selon le critère.">
                          <i class='fa fa-search' style='font-size:24px;'></i>&nbsp;
                        </button>
                        <button type="button" class="btn btn-danger btn-flat" id="search" onclick="ClearsearchTable()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affacer la recherche.">
                          <i class='fa fa-remove' style='font-size:24px;'></i>&nbsp;
                        </button>
                      </span>
                    </div>
                  </li>
                  <li>
                    <button type="button" onclick="window.print()" class="btn btn-primary" style="margin-left: 25px;">
                      <i class="fa fa-download"></i> Imprimer
                    </button>
                  </li>
                </ul>

              </div>
        </header>

            <div class="row" >
                <div class="col-lg-1">
                </div>
            	<div class="col-lg-12  box box-info">
		            <section class="panel panel-featured-left panel-featured-primary">
			            <div class="panel-body">
				            <section class="card">

						            <div class="card-body" style="width:100%">
                          <table id="tblboites" class="display responsive nowrap" border="1" width="1200">
                            <thead>
                                <tr style="text-align:right;">

    							                <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center"># Commande</td>
    							                <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center"># Boîte en place</td>
    							                <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Type de demande</td>
    							                <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Type de commande</td>
                                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Date de livraison</td>
                                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Notes</td>
    							                <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Notes bureau</td>
                                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Adresse de livraison</td>
    							                <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Chauffeur</td>
                                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Camion</td>
                                </tr>
                            </thead>
                            <tbody id="tboProd">
                              <?php echo ($listcommandes ?? '') ; ?>
                            </tbody>
                          </table>
						            </div>
				            </section>
			            </div>
		            </section>
	            </div>
            </div>
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
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
  
    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })
    //------------------------------------------------------------------------------
function searchTableList() {
  var urlName = "/rolloff/recherche/" + $('#edtFind').val() + "/" + $('#dteActive').val() + "?typecom=" + $('input[name="radtype"]:checked').val() +
                          "&endhide=" + EndIsChecked;
  var urlName = "/bureau/listeallcommandes?dteactive=" + $('#dteActive').val() + "&typecom=" + $('input[name="radtype"]:checked').val() +
                                      '&find=' + $('#edtFind').val();
	document.location = urlName;
}
//------------------------------------------------------------------------------
function ClearsearchTableList() {
	$('#edtFind').val('');
	var urlName = "/rolloff/effacer_recherche/" + $('#dteActive').val() + "?show=" + ToShow + "&endhide=" + EndIsChecked;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$("#tboRO").html(msg);
			$("#divMsgChauffeur").hide();
		}
	});
	getStatutCommande();
}
//------------------------------------------------------------------------------
function ReturnsearchTableList(value, e) {

	var keycode = e.keyCode ? e.keyCode : e.charCode;

	if (keycode == 13) {
		$("*").css("cursor", "progress");
		var optsel = $("#cbFields").val();
		var urlName = "/rolloff/recherche/" + value + "/" + $('#dteActive').val() + "?show=" + ToShow + "&endhide=" + EndIsChecked;
		$.ajax({
			type: "POST",
			url: urlName
		}).done(function (msg) {

			if (msg != "") {
				$("#tboRO").html(msg);
				$("#divMsgChauffeur").hide();
			}
			$("*").css("cursor", "auto");
		});
	}
	getStatutCommande();
}
  </script>
</body>
</html>
