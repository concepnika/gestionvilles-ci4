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
     <h2 style="margin-left:150px">Clients code : <?php echo ($code ?? '') ; ?></h2> <br />   
    <form id="frmClient">

      <div class="row">
        <div class="col-lg-2">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div class="col-lg-10">
          <select id="selClients" name="selClients" onchange="setClient(this.value)" class="form-control" style="width:600px">
            <?php echo ($optclients ?? '') ; ?>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-1">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div class="col-lg-10  box box-info">
          <section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
              <section class="card">

                <div class="card-body" style="width:100%">
                  <table id="tblboites" class="display responsive nowrap" border="1" width="1200">
                    <thead>
                      <tr>
                        <th colspan="9" style="text-align:center;">
                          <div id="myNavbar" style="margin-left:5%;">
                            <ul class="nav navbar-nav">
                              <li>
                                <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                              </li>
                              <li>
                                <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                              </li>

                              <li>
                                <button type="button" class="btn btn-success" onclick="PostClient()" id="btnPostClient" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Enregistrer" data-content="Cliquer pour enregistrer le client">
                                  <i class="glyphicon glyphicon-ok"></i>
                                </button>
                              </li>
                              <li>
                                <button type="button" onclick="CancelClient()" id="btnCancelClient" class="btn btn-warning" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Annuler" data-content="Cliquer pour annuler les modifications du client">
                                  <i class="glyphicon glyphicon-remove"></i>
                                </button>
                              </li>
                            </ul>

                          </div>
                        </th>

                      </tr>
                    </thead>
                    <tbody id="tboClientsPrix">
                      <?php echo ($listclients ?? '') ; ?>
                    </tbody>

                  </table>
                </div>
              </section>
            </div>
          </section>
        </div>
      </div>
  </form>
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
    function setClient(value) {
      var urlName = '/bureau/getcodecinq/' + value;
	    $.ajax({
		    url: urlName,
		    context: document.body
      }).done(function (data) {
    		    $("#tboClientsPrix").html(data);

	    });
    }
    function CancelClient() {
      var urlName = '/bureau/getcodecinq/' + $("#selClients").val();
	    $.ajax({
		    url: urlName,
		    context: document.body
      }).done(function (data) {
    		    $("#tboClientsPrix").html(data);

	    });
    }
  function PostClient() {
    var formval = $("#frmClient").serialize();
	  var urlName = "/bureau/postclientcinq?" + formval;
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
		  //if (msg != "") {
    //    GetClients(msg);
        AjusteButtonClient('0');
    //  }
    //  else {
    //    AjusteButtonClient('0');

    //  }
    });
  }
  </script>
</body>
</html>
