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
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/lang-all.js "></script>  
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
  <link rel="stylesheet" href="/assets/css/jquery-ui.multidatespicker.css" />
  <style>

		.tdcalendrier{
			width:190px;
			height:160px;
			background-color:#FFFFCC;
			border: 1px solid #708090;
		}
		.tdcalendrierHeader{
			width:190px;
			height:30px;
			background-color:#812B2C;
			color:white;
			text-align:center;
			font-size:1.5em;
		}
		.tdcalendrierHeaderMonth{
			width:190px;
			height:42px;
			background-color:#812B2C;
			color:white;
			text-align:center;
			font-size:2.5em;
		}
		.tdcalendrierComboMonth{
			width:180px;
			height:38px;
			background-color:#FFFFCC;
			font-size:1.0em;
		}

		.tableCalendrier{
			border: 1px solid #800000;
			width: 1100px;
			height: 1060px;
		}
		.tdjour{
			width:40px;
			height:20px;
			background-color:#812B2C;
			color:white;
			text-align:center;
			font-size:1.5em;
		}
		.tdinfo{
			width:150px;
			height:20px;
			font-size:medium;
		}
		.tdheure{
			width:40px;
			height:20px;
			border: 1px solid #800000;
			font-family:Arial, Helvetica, sans-serif;
		}
		.tdheureVide{
			width:40px;
			height:20px;
			font-family:Arial, Helvetica, sans-serif;
		}
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

  </script>
</head>
<body class="hold-transition skin-blue">

  <div class="wrapper">


      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-inverse navbar-fixed-top">
        <!-- Sidebar toggle button-->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <?php echo view('sections/menu');?>
	    
      </nav>

    <div class="wrapper" style="margin-top: 5%;">

      <div class="row" style="left: 10px;top: 70px;width:100%; border-bottom:solid; ">
        <div class="col-lg-2"></div>
        <div class="col-md-7">
          <ul class="nav nav-pills">

            <li>
              <div class="input-group input-group-sm" style="width:250px">
                <div class="input-group-addon"><b>VILLES</b></div>
                <select class="form-control" name="optVilles" id="optVilles" onchange="getRtesVille(this.value)">
                  <option value="0">Choisir</option>
                  <?php echo ($optvilles ?? '') ; ?>
                </select>
              </div>
            </li>
            <li> &nbsp;&nbsp;</li>
            <li>
              <form class="form-inline" role="form">
                <div class="form-group">
                  <label style='font-size:18px;'>Afficher les supprimées&nbsp;&nbsp;</label>
                  <input type="checkbox" style="height:25px;width:25px;" onchange="SetDisponible()" id="chkDispoOnly" />
                </div>

              </form>
            </li>
            <li> &nbsp;&nbsp;</li>
            <li>
              <button type="button" onclick="window.print()" class="btn btn-primary" style="margin-left: 25px;">
                <i class="fa fa-download"></i> Imprimer
              </button>
            </li>

          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2"></div>
          <div class="col-lg-8">

            <div class="table-responsive" style="margin-top:37px;">
              <div class="box-body" style="margin-left:20px;">


                <table id="tblroutes" class="table" width="800">
                  <thead>
                    <tr>
                      <td scope="col" width="90" align="center">
                        <label style="width:90px;">Supprimer</label>
                      </td>
                      <td scope="col" width="230" align="center">
                        <label style="width:230px;">Nom</label>
                      </td>
                      <td scope="col" width="400" align="center">
                        <label style="width:400px;">Description</label>
                      </td>
                      <td scope="col" width="100" align="center">
                        <label style="width:100px;">GéoRoute</label>
                      </td>

                      <td scope="col" align="center">
                        <button type="button" class="btn btn-primary" onclick="addRoute()" title="Ajouter une route.">
                          <i class='fa fa-plus-circle' style='font-size:18px;'></i>&nbsp;
                        </button>
                      </td>
                    </tr>
                  </thead>
                  <tbody id="tboRoutes">
                    <?php echo ($lisroutes ?? '') ; ?>
                  </tbody>
                </table>

              </div>
            </div>
          </div>
        </div>
      <div class="row" style="margin-top: 30px;">
        <div class="col-md-12"></div>
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
    <script src="/assets/fullcalendar/dist/locale/fr-ca.js"></script>
    <script src="/assets/fullcalendar/dist/locale/fr.js"></script>
  <script>
    function postRoute(id) {
      var isdel = (($("#chkdel_" + id).is(':checked')) ? 1 : 0);
      var urlName = '/sanivan/sendinforoutes/' + id + '?Nom=' +
        encodeURIComponent($("#edtNom_" + id).val()) + '&Description=' + encodeURIComponent($("#edtDescription_" + id).val()) + '&idville=' +
        $("#optVilles").val() + '&isdel=' + isdel;
      $.ajax({
        url: urlName,
        context: document.body
      }).done(function (data) {
        $("#tboRoutes").html(data);
      });
    }
    function getRtesVille(idville) {
      var urlName = '/sanivan/routes/' + idville + '?from=1';
      $.ajax({
        url: urlName,
        context: document.body
      }).done(function (data) {
        if (data == "") {
          var ligneToAdd = '<tr id="tr_0"><td><label style="font-size: 14px;">NON</label></td>' +
            '<td align="center"> <input class="form-control" style="width: 230px" id="edtNom_0" type="text" value="" /></td>' +
            '<td align="center"><input class="form-control" style="width: 400px" id="edtDescription_0"' +
            'type="text" value="" /></td>' +
            '<td  style="max-width:90px" align="center"><a class="btn btn-info" title="Cliquer pour creer ou afficher la route" disabled>' +
            '<i class="fa fa-road" style="font-size:16px;"></i> Route</a></td>' +
            '<td style="max-width:90px"  align="center"><a  title="Cliquer pour enregistrer" onclick="postRoute(0)">' +
            '<i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a ></td></tr>';
          $("#tboRoutes").html(ligneToAdd);
        }
        else {
          $("#tboRoutes").html(data);
        }

      });
    }
  </script>
</body>
</html>
