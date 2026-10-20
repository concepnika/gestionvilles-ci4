<?php
defined('BASEPATH') or exit('No direct script access allowed');

?>
<!DOCTYPE HTML>
<html data-bs-theme="light" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html" />
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

  <!-- Bootstrap 3.3.7 -->
  <script src="/assets/pronto/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/media/js/dataTables.bootstrap5.min.js"></script>

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
    var titrediscution = <?php echo $titreNB ?>

  </script>
</head>
<body id="page-top">
  <section role="main" class="content-body content-body-modern mt-0">
    <header class="page-header page-header-left-inline-breadcrumb" style="margin-top:40px">
      <div class="input-group" style="width:550px;">
        <h2 class="font-weight-bold text-6">
          Requête&nbsp;&nbsp;
          <?php echo $retour; ?>&nbsp;&nbsp;&nbsp;&nbsp;
        </h2>

        <a href="#" style="height:32px;width:32px;margin-left:5px;margin-top:0px" onclick="PrintReponse(<?php echo $idrequete; ?>)">
          
          <i class="fa fa-file-pdf-o" style="font-size:34px;color:red;height:32px;width:33px;"></i>
        </a>
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
                  id="datatable-tabletools" width="90%">
                  <thead>
                    <tr>
                      <th align="center" colspan="7" style="text-align:center">
                        <h2><?php echo $titreNB; ?></h2>
                      </th>
                    </tr>
                    <tr>
                      <th align="center">REQUÉRANT</th>
                      <th>DATE CÉDULÉE</th>
                      <th>FICHIER</th>
                      <th>DESCRIPTION</th>
                      <th>CHANGEMENT ADRESSE</th>
                      <th>STATUT</th>
                      <th>DATE</th>
                    </tr>
                  </thead>
                  <tbody id="tboDelCom">
                    <?php echo $listreceptions; ?>
                  </tbody>
                </table>

              </div>
            </section>
          </div>
        </section>
      </div>
    </div>
    <div class="modal fade" id="confirmeChangeDte" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Liste des réponses et des ajouts</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Modal body -->
          <div id="divRepAjout" class="modal-body"></div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Imprimer</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
          </div>

        </div>
      </div>
    </div>
    <div class="modal fade" id="showDownloadRepPDF" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header btn-danger">
            <h1 id="mytitre" class="modal-title">Votre Liste des discustions</h1>
            <button type="button" class="close" onclick="closePrintRep()">&times;</button>
          </div>
          <div>
            <p class="card-text" style="margin-left: 10px;"></p>
          </div>
          <!-- Modal body -->
          <div id="divModalPDF" class="modal-body" style="margin-left: 14px;font-style:italic;font-size:large"></div>

          <!-- Modal footer -->
          <div class="modal-footer" style="background-color:Window;">
            <button type="button" id="btnConfirme" class="btn btn-default" onclick="closePrintRep()" data-dismiss="modal">Terminer</button>
          </div>

        </div>
      </div>
    </div>
    <!-- end: page -->
  </section>
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
  <script src="/assets/js/examples/examples.datatables.tabletools.req.js"></script>
  
  <script src="/assets/js/nika.js"></script>
  <script>
    selectedDay = "<?php //echo $ActiveDate ?>";
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

