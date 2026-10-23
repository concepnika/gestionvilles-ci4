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
  <link rel="stylesheet" href="/assets/pronto/vendor/bootstrap/css/bootstrap.css" />
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
  <link rel="stylesheet" href="/assets/pronto/vendor/pnotify/pnotify.custom.css" />
  <!-- Bootstrap 3.3.7 -->
  <script src="/assets/pronto/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/media/js/dataTables.bootstrap5.min.js"></script>
  <link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

  <style>
    .popover-title {
      background: darkblue;
      color: white;
      font-weight: bold;
      min-width: 200px;
    }

    .popover-content {
      background: lightgray;
      color: darkblue;
      min-width: 200px;
    }

    .titre {
      color: darkblue;
      width: 100%;
      height: 100%;
      font-size: large;
      font-weight: bold;
    }

    .separateur {
      color: white;
      width: 100%;
      height: 35px;
      padding-top: 5px;
      font-size: large;
      font-weight: bold;
      background-color: black;
      text-align: center;
    }

  </style>
  <script src="/assets/css/fontawesome/all.css"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url() ?>";
    var ActiveDate = "<?php echo date('Y-m-d') ?>";
    $(document).ready(function () {
      $('[data-toggle="popover"]').popover();
    });
  </script>
</head>
<body id="page-top">
  <section role="main" class="content-body content-body-modern mt-0">
    <header class="page-header page-header-left-inline-breadcrumb" style="margin-top:40px">

        <div class="input-group" style="width:1250px;">
          <h2 class="font-weight-bold text-6">
            Requêtes&nbsp;&nbsp;
            <a href="/sanivan/aujourdhui/1/<?php echo date("Y-m-d"); ?>">RETOUR</a>&nbsp;&nbsp;
          </h2>

          <a href="#" style="height:32px;width:32px;margin-left:5px;margin-top:0px" onclick="PrintRequete()">

            <i class="fa fa-file-pdf-o" style="font-size:34px;color:red;height:32px;width:33px;"></i>
          </a>&nbsp;&nbsp;
          <a href="#" onclick="PrintExcelRequete()" style="margin-top:-5px">
            <img src="/assets/img/excel.png" width="47" />
          </a>
          <select class="form-control" onclick="LimitVille(this.value)" name="idville" id="idville" style="width:120px;height:35px" required>
            <option value="0">Toute les villes</option>
            <?php echo ($optVilles ?? '') ?>
          </select>&nbsp;&nbsp;

          <label class="input-group-text border-start-0 border-end-0 rounded-0" style="width:185px;height:35px;font-weight:bold;">Afficher les traitées</label>
            <input type="checkbox" id="chkShowTraitees" onchange="showHideTraitees()" style="width:35px;height:35px;margin-top:0px;" />&nbsp;&nbsp;

            <span class="input-group-text border-start-0 border-end-0 rounded-0" style="height:35px"> De
            </span>
          <input type="date" class="form-control" style="width:130px;height:35px" name="start" id="start" onchange="showHideTraitees()" />
            <span class="input-group-text border-start-0 border-end-0 rounded-0" style="height:35px"> Au
            </span>
          <input type="date" class="form-control" style="width:130px;height:35px" 
                 name="end" id="end" onchange="showHideTraitees()" />



        </div>


    </header>
    <div class="row">
              <div class="col-lg-1"></div>
              <div class="col-lg-12  box box-info">
                <section class="panel panel-featured-left panel-featured-primary">
                  <div class="panel-body">
                    <section class="card">

                      <div class="card-body" style="width:100%">
                        <table class="table table-bordered table-striped mb-0"
                          id="datatable-tabletools">
                          <thead>
                            <tr style="color:white;background-color:darkblue;">
                              <th align="center"><i class="fa fa-users" style="font-size:24px"></i></th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Requérant</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">#Requête</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Date Cédulée / Créée</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Titre</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Adresse</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Matériel</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">description</th>
                              <th align="center" style="text-transform: uppercase;text-align:center;">Statut</th>

                            </tr>
                          </thead>
                          <tbody id="tboDelCom">
                            <?php echo ($listrequestes ?? ''); ?>
                          </tbody>
                        </table>

                      </div>
                    </section>
                  </div>
                </section>
              </div>
</div>
    <div class="modal fade" id="showDownloadRepPDF" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header btn-danger">
            <h1 id="mytitre" class="modal-title">Votre Liste des requêtes</h1>
            <button type="button" class="close" onclick="closePrintReq()">&times;</button>
          </div>
          <div>
            <p class="card-text" style="margin-left: 10px;"></p>
          </div>
          <!-- Modal body -->
          <div id="divModalPDF" class="modal-body" style="margin-left: 14px;font-style:italic;font-size:large"></div>

          <!-- Modal footer -->
          <div class="modal-footer" style="background-color:Window;">
            <button type="button" id="btnConfirme" class="btn btn-default" onclick="closePrintReq()" data-dismiss="modal">Terminer</button>
          </div>

        </div>
      </div>
    </div>

    <!-- end: page -->
  </section>
  <script src="/assets/pronto/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/assets/pronto/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
  <script src="/assets/js/examples/examples.datatables.tabletools.js"></script>
  <script src="/assets/pronto/vendor/pnotify/pnotify.custom.js"></script>


  <script src="/assets/js/nika.js"></script>
  <script>
    selectedDay = "<?php //echo ($ActiveDate ?? '') ?>";
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

