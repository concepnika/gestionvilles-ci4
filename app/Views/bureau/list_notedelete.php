<?php

?>

<!DOCTYPE HTML>
<html data-bs-theme="light" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Robert Daoust</title>
  <link rel="icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />

  <link href="style.css" rel="stylesheet" type="text/css" />
  <script src="/assets/pronto/vendor/jquery/jquery.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
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
  <link rel="stylesheet" href="/assets/pronto/vendor/datatables/media/css/dataTables.bootstrap5.css" />

   <!-- Bootstrap 3.3.7 -->
    		<script src="/assets/pronto/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/media/js/dataTables.bootstrap5.min.js"></script>

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

    </nav>

  <div class="wrapper" style="margin-top: 5%;">
            <header class="page-header">
              <div id="myNavbar" style="margin-left:5%;">
                <ul class="nav navbar-nav">
                <li style="margin-right:10px;margin-top:-15px">
                  <h3>Commandes supprimées</h3>
                </li>

                <li>
                  <div class="input-group input-group-sm" style="width:400px; ">
                    <span class="input-group-btn">
                      <a class="btn btn-info btn-flat" href="javascript:ListRemoveCom('-1')" id="divShowHideEmp" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journée précédente" data-content="Affiche la cédule de la journée précédente">
                        <i class='fa fa-angle-left' style='font-size:24px;'></i>
                        &nbsp;
                      </a>



                    </span>
                    <input type="date" class="form-control" onchange="ListRemoveCom('0')" data-toggle="popover" style="width:130px;margin-top:3px" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ?>" id="dteActive" />

                    <span class="input-group-btn">
                      <a class="btn btn-info btn-flat" href="javascript:ListRemoveCom('1')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journée suivante" data-content="Affiche la cédule de la journée suivante">
                        <i class='fa fa-angle-right' style='font-size:24px;'></i>
                        &nbsp;
                      </a>
                    </span>
                    <label class="form-control" style="width:170px">Afficher les validées</label>
                    <span class="input-group-btn">
                      <input type="checkbox" id="chkShowVal" <?php echo (($delcheck == 1) ? 'checked' : '');?> onchange="showHideValidees()" style="width:30px;height:30px;margin-top:3px;" />


                    </span>
                  </div>
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
                          <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">
                            <thead>
                              <tr>
                                <td align="center">#Commande</td>
                                <td align="center">Client</td>
                                <td align="center">Date Facture</td>
                                <td align="center">Note de suppression</td>
                                <td align="center">Date de suppression</td>
                                <td align="center">Valider</td>

                              </tr>
                            </thead>
                            <tbody id="tboDelCom">
                              <?php echo ($listnotedelete ?? '') ; ?>
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

		<script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js"></script>
		<script src="/assets/pronto/vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js"></script>
  <script src="/assets/pronto/js/theme.js"></script>

  <!-- Theme Custom -->
  <script src="/assets/pronto/js/custom.js"></script>

  <!-- Theme Initialization Files -->
  <script src="/assets/pronto/js/theme.init.js"></script>

  <script src="/assets/pronto/js/examples/examples.datatables.default.js"></script>
  <script src="/assets/pronto/js/examples/examples.datatables.row.with.details.js"></script>
  <script src="/assets/pronto/js/examples/examples.datatables.tabletools.js"></script>

    <script src="/assets/js/nika.js"></script>
  <script>
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
    function setIsDelChecked(id) {
      var urlName = '/bureau/setcheck/' + id;
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        $("#tboDelCom").html(msg);
      });
    }
    function showHideValidees() {
      var ischeck = (($("#chkShowVal").is(':checked')) ? 1 : 0);
      var urlName = '/bureau/listenotedelete/' + ischeck;
      document.location = urlName;
    }
    function ListRemoveCom(nbjour) {
      var ischeck = 0;
      if ($('#chkShowVal').prop('checked') == true) {
        ischeck = 1;
      }
      var urlName = "/bureau/listenotedelete/" + ischeck + "?nbjour=" + nbjour +
        '&date=' + $('#dteActive').val();
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        if (msg != "") {
          $('#tboDelCom').val(msg);

        }
      });
    }
  </script>
</body>
</html>
