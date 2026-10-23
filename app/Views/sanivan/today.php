
<?php


?>
<!DOCTYPE HTML>
<html lang="fr" xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">
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
  <script src="/assets/pronto/vendor/pnotify/pnotify.custom.js"></script>
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

  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/Ionicons/css/ionicons.min.css" />
  <!-- fullCalendar -->
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.min.css" />
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.print.min.css" media="print" />
  <!-- Theme style -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/bower_components/Ionicons/css/ionicons.min.css" />
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css" />
  <!-- Morris chart -->
  <link rel="stylesheet" href="/assets/bower_components/morris.js/morris.css" />
  <!-- jvectormap -->
  <link rel="stylesheet" href="/assets/bower_components/jvectormap/jquery-jvectormap.css" />
  <!-- Date Picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css" />
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" />
  <link rel="stylesheet" href="/assets/plugins/iCheck/all.css" />
  <link rel="stylesheet" href="/assets/pronto/css/theme-admin-extension.css" /> 
  <link rel="stylesheet" href="/assets/pronto/vendor/pnotify/pnotify.custom.css" /> 
    <link rel="stylesheet" href="/assets/select2/css/select2.min.css">
  <link rel="stylesheet" href="/assets/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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

    @media (max-width: 967px) {
      .titre-grille {
        border-bottom: none;
        border-top: none;
        margin: 5px 0;
      }

      .filtre {
        visibility: visible;
      }

        .filtre .navbar-toggle {
          float: none;
          padding: 9px 10px;
          margin-top: 0px;
          margin-bottom: 8px;
          background-color: #009ddc;
          border-radius: 4px;
        }

        .filtre .navbar-header {
          padding: 5px 0px 10px 0px;
        }

          .filtre .navbar-header a {
            font-size: 11px;
            font-weight: bold;
            color: #ca006c;
            cursor: pointer;
            text-decoration: none;
          }

            .filtre .navbar-header a .fa {
              font-size: 24px;
              color: #ca006c;
              cursor: pointer;
            }

        .filtre .navbar-toggle:focus {
          outline: 0;
        }

        .filtre .navbar-toggle:hover {
          background-color: #999;
        }

        .filtre .navbar-collapse {
          padding-right: 0px;
          padding-left: 0px;
          border-bottom: thin solid #999
        }

          .filtre .navbar-collapse li {
            border-top: thin solid #999
          }

        .filtre .nav > li > a {
          padding: 10px 0px;
        }

        .filtre .selection {
          height: 25px;
          font-size: 15px;
          font-weight: bold;
          color: #009ddc;
          text-transform: uppercase;
        }
    }

    input[type="time"]::-ms-clear {
      display: none;
    }

    .footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      background-color: lightgray;
      color: white;
      text-align: center;
    }

    <?php echo (($from != 3) ? 'body {overflow: hidden;}' : ''); ?>
    .dropbtn {
      background-color: #04AA6D;
      color: white;
      padding: 16px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .dropbtn:hover, .dropbtn:focus {
      background-color: #3e8e41;
    }

    .dropdown {
      float: right;
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f1f1f1;
      min-width: 160px;
      overflow: auto;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      right: 0;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown a:hover {background-color: #ddd;}

    .show {display: block;}
    button {
      background-color: #4CAF50;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      cursor: pointer;
      width: 100%;
    }
      .polyline-clickable {
          font-size: 9px;
          color: #4a90e2;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          max-width: 120px;
          cursor: pointer;
          transition: color 0.2s;
      }

          .polyline-clickable:hover {
              color: #2c3e50;
              text-decoration: underline;
          }
    <?php echo ($classstat ?? '') ?>

    button:hover {
      opacity: 0.8;
    }

    /* ═══ Boutons flottants mobile ═══ */
    .btn-toggle-sidebar, .btn-back-mobile {
      display: none;
      position: fixed;
      z-index: 1100;
      width: 40px !important;
      height: 40px;
      border-radius: 50%;
      border: 2px solid DarkBlue !important;
      background: #fff !important;
      color: DarkBlue !important;
      font-size: 20px;
      line-height: 36px;
      text-align: center;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0,0,0,.3);
      padding: 0 !important;
      margin: 0 !important;
    }
    .btn-toggle-sidebar { top: 58px; left: 8px; }
    .btn-back-mobile { top: 58px; left: 56px; }
    .btn-toggle-sidebar:active, .btn-back-mobile:active { transform: scale(.9); }

    @media (max-width: 768px) {
      .btn-toggle-sidebar, .btn-back-mobile { display: block !important; }
      #divDropCedule {
        display: none !important;
        z-index: 1050;
        width: 220px !important;
        top: 100px !important;
        left: 8px !important;
        height: auto !important;
        max-height: 75vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 4px 16px rgba(0,0,0,.3);
      }
      #divDropCedule.sidebar-open {
        display: block !important;
      }
      #divVilles {
        width: 200px !important;
      }
      #divDropVille, #divDropSecteur {
        width: 200px !important;
      }
      #divChauffeurs {
        width: 200px !important;
      }
      #divDropCamion, #divDropRH {
        width: 190px !important;
      }
      #divHresAides {
        display: none !important;
      }
      #divShowCed {
        margin-left: 0 !important;
        width: 100vw !important;
      }
    }
  </style>
  <!-- Leaflet CSS et JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url() ?>";
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var activecedule = "<?php echo ($typecedule ?? '') ?>";
    var ActiveDrop = "<?php echo ($ActiveDrop ?? '') ?>";
    var ActiveRow = 1;
    var LastRow =  "<?php echo ($LastRow ?? '') ?>";
    var notab = "<?php echo ($notab ?? '') ?>";
    var AideIsShow = "<?php echo ($AideIsShow ?? '') ?>";
    var from = "<?php echo ($from ?? '') ?>";
    var activeRH = 1;
    function ConfirmeLoad(Route) {
      $("#td_" + Route).css("background-color", "#80FF9F");
      $("#chk_" + Route).css("background-color", "#80FF9F");
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
  <body class="hold-transition skin-blue" onresize="ChangeSizeOfDiv(this)">
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <!-- Sidebar toggle button-->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <?php
      switch ($from) {
      case 3:
      echo '<form class="navbar-form navbar-left" action="/action_page.php">
      <div class="form-group">
      <label style="margin-top:5px; font-weight: bold;font-size:16px;color:white">Robert DaoustS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&Eacute;DULES REAR-END ET ROBOT DU</label>
      <label id="lblDate" style="margin-top:5px; font-weight: bold;font-size:16px;color: white">' . $ActiveDate . '</label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <span class="form-control" style="background-color:aqua;font-weight:bold;font-size:medium;color:black;">
      REAR END &nbsp;&nbsp;<span id="spRearEnd">' . $spRearEnd . '</span>
      </span>&nbsp;&nbsp;&nbsp;&nbsp;
      <span class="form-control" style="background-color:chocolate;font-weight:bold;font-size:medium;color:black;">
      ROBOT &nbsp;&nbsp;<span id="spRobot">' . $nbRobot . '</span>
      </span>&nbsp;&nbsp;&nbsp;&nbsp;
      <span  class="form-control" style="background-color:darkred;font-weight:bold;font-size:medium;color:white;">
      TOTAL &nbsp;&nbsp;<span id="spTotalCam">' . $nbAllCam . '</span>
      </span>
      </div>
      </form>';
      break;
      default:
      echo view('sections/inc_menu');
      }
      ?>
    </nav>

    <div id="divHresAides" style="position:fixed;left: 10px;top: 60px; height: 75vh; display:<?php echo ((($AideIsShow == 1) and ($from != 3)) ? 'block' : 'none'); ?>; width: 500px;">
      <div class="box-header with-border"></div>
      <div class="box box-solid" style="overflow: scroll; height: 100%; width: 500px; ">
        <div class="btn-group form-inline" role="group" style="margin-bottom:12px;">
          <div>

            <div class="input-group">
              <input type="date" style="margin-left: 25px;height: 28px;margin-top:3px;" onchange="LoadRHhres()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ?>" id="dteRH" />
              <span class="input-group-btn">
                <input type="text" id="edtFind" class="form-control" onkeyup="EntersearchTableRHhres(this.value, event)" placeholder="Rechercher" style="width:100px;height: 28px;margin-left: 10px;margin-top:0px" />

                <button type="button" style="height: 28px;font-weight:blod;" class="btn btn-info btn-flat" id="search" onclick="searchInTableRHhres()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affiche la c&eacute;dule selon le crit&egrave;re.">
                  <i class='fa fa-search'></i>&nbsp;
                </button>
                <button type="button" style="height: 28px;" class="btn btn-danger btn-flat" onclick="ClearsearchTableRHhres()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affacer la recherche.">
                  <i class='fa fa-remove'></i>&nbsp;
                </button>
                <button type="button" style="height: 28px;" class="btn btn-default btn-flat" onclick="ShowDivDrop()">Cr&eacute;ation</button>
              </span>

            </div>


          </div>

        </div>
        <div class="box-body" style="margin-left:20px;">
          <button type="button" onclick="PrintHresAides()" class="btn btn-primary">
            <i class="fa fa-download"></i> Imprimer
          </button>
          <a href="#" onclick="HrestoExcel()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Heures des Aides" data-content="Exporter vers EXCEL les heures de la journée active.">
            <img src="/assets/img/excel.png" width="30" />
          </a>
          <a href="#" onclick="ShowPageHres()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
            <img src="/assets/img/find2.jpg" width="30" />
          </a>
          <table id="tblhresaides" class="display responsive nowrap" style="width: 450px;border-width: 2px;border-color: black;border:thin;" width="500">
            <thead>
              <tr style="text-align:right;border-width: 2px;border-color: black;border:thin;">
                <td class="cellule" align="center">Nom</td>
                <td class="cellule" align="center">D&eacute;but</td>
                <td class="cellule" align="center">Fin</td>
                <td class="cellule" align="center">Pause</td>
                <td class="cellule" align="center">Note</td>
              </tr>
            </thead>
            <tbody id="tboRHhres">
              <?php echo ($listhresaides ?? ''); ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    <button type="button" class="btn-toggle-sidebar" id="btnToggleSidebar" title="Menu">
      <i class="fa fa-bars"></i>
    </button>
    <button type="button" class="btn-back-mobile" id="btnBackMobile" title="Retour" onclick="history.back()">
      <i class="fa fa-arrow-left"></i>
    </button>
    <div id="divDropCedule" class="border border-secondary" style="display:block;position:fixed;left: 10px;top: 53px; height: 75vh; width: 230px;">
      <div class="box-header with-border" style="margin-left: 2px">
        <div class="btn-group form-inline" role="group" style="margin-bottom:12px;">
          <div class="input-group">
            <button type="button" id="btnShowRouteCD" class="btn btn-info btn-flat" onclick="ShowHideRouteToday('divVilles')">
              Afficher Villes
            </button>
            <button type="button" class="btn btn-default btn-flat" onclick="ShowHresAides()">Hres Aides</button>
          </div>
        </div>
      </div>
      <div id="divVilles" style="position:absolute;left: 0px;display:none; height: 500px; width: 300px">

        <div class="box" style="margin-bottom:20px;border: 2px solid black;width: 210px;padding:2px;">
          <select class="form-control" style="width:200px" onchange="ShowHideVilleSecteur(this.value)">
            <option value="1">Villes</option>
            <option value="2">Secteurs</option>
          </select>
        </div>
        <div class="box box-solid" style="width: 350px">
          <div id="divDropVille" class="box-body" style="overflow: scroll;height: 350px;">

            <?php
            echo ($ville ?? '');
            ?>

          </div>


          <!-- /.box-body -->
        </div>

        <div class="box box-solid" style="width: 300px;">
          <div id="divDropSecteur" class="box-body" style="display: none; overflow: scroll; height: 350px;">

            <?php
            echo ($SecteurDrop ?? '');
            ?>

          </div>
          <!-- /.box-body -->
        </div>
      </div>
      <div id="divChauffeurs" style="position:absolute;left: 0px;display: block;height: 430px; width: 230px">
        <div class="box" style="margin-bottom:20px;border: 2px solid black;width: 210px;padding:2px;">
          <label>
            <input id="chkCamDrop" type="checkbox" checked style='font-size:16px;color: #03F;cursor:pointer;'
                   onchange="ShowHideDrop(this.id)" />&nbsp;CAM
          </label>&nbsp;&nbsp;
          <label>
            <input id="chkRHDrop" type="checkbox" style='font-size:16px;color: #03F;cursor:pointer;'
                   onchange="ShowHideDrop(this.id)" />&nbsp;RH
          </label>
        </div>
        <div id="divcam">
          <div class="box box-solid" style="overflow: scroll; height: 380px; width: 200px;margin-top:10px;">
            <div id="divDropCamion" class="box-body" style="padding-left:7px;">

              <?php
              echo ($CamionDrop ?? '');
              ?>

            </div>
          </div>
        </div>
        <div id="divRH" style="display: none;">
          <div class="box-header with-border">
            <div class="checkbox">
              <label class="btn btn-default" id="chkShowAllRH" style="font-size: medium;font-weight:bold;width:180px;text-align:center;">
                <input type="checkbox" onchange="ShowAllRH()" id="chkShowConge" /> Afficher en cong&eacute;
              </label>

            </div>
            <select class="form-control" onchange="getRhToDrop(this.value)" style="width:200px" name="typechauf" id="typechauf" required>
              <option value="chauf">Chauffeurs</option>
              <option value="2">Aide</option>
              <option value="4">Roll Off</option>
            </select>
          </div>
          <div class="box box-solid" id="divDropRH" style="padding-left:7px;display:block;overflow: scroll; height: 290px; width: 200px">

            <?php
            echo ($ChauffeurDrop ?? '');
            ?>

          </div>
          <!-- /.box-body -->
        </div>

      </div>
    </div>


    <div id="divShowCed" style="margin-top: 60px;margin-left:<?php echo ((($AideIsShow == 1) and ($from != 3)) ? ' 510px' : '235px'); ?>;<?php echo (($from != 3) ? 'height: 85vh; width: 87vw;overflow:scroll; position:page;' : ''); ?>">
      <!--<div class="box box-primary"  style="position:absolute;left:5px;top: 100px;min-height:800px;width:100%;">-->
      <div id="hs_dispo_cam" style="margin-top: 10px;display: none;border:solid; border-color: black;border-width:1px;" class="row">
        <?php
        echo ($hs_cam ?? '');
        ?>
      </div>

      <div id="non_dispo_chauf" style="margin-left:10px;display: none;margin-top:5px;border:solid; border-color: black;border-width:1px;" class="row">
        <?php
        echo ($non_dispo_chauf ?? '');
        ?>
      </div>
      <div id="non_dispo_aide" style="margin-left:10px;display: none;margin-top:5px;border:solid; border-color: black;border-width:1px;" class="row">
        <?php
        echo ($non_dispo_aide ?? '');
        ?>
      </div>
      <div id="divCedule">
        <?php
        echo ($today ?? '');
        ?>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /. box -->

    <div id="non_dispo_cam" style="margin-left:5px;margin-left:5px;display: block;margin-top:5px;border:solid; border-color: black;border-width:1px;" class="row">
      <?php
      echo ($non_dispo_cam ?? '');
      ?>
    </div>
    <!--<div id="divFooterSan" class="footer" style="margin-top:5px;width:100%">

      <?php
      echo ($requetes ?? '');
      ?>

    </div>-->
    <!--//---------------------------------------------------------------------MODAL-->
    <div class="modal fade" id="modalRequete" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header btn-primary">
            <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="RequeteTitre" style="color:white;"></h3>

          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <label style="font-weight: bold; ">R-<span id="spReq"></span> </label><br />
              <input type="hidden" id="IdRequete" value="" />
              <div class="col-sm-7 col-xs-12">
                <select class="form-control" name="selTypeReq" id="selTypeReq" required>
                  <option value="0">Choisir</option>

                </select>
              </div>
              <br /><br />
              <!--nom, adresse, fichier, description, matiere, statut-->
              <div class="col-sm-7 col-xs-12">
                <label style="font-weight: bold; ">Matières </label><br />
                <select class="form-control" onchange="getChauffeur(this.value)" name="selMateriel" id="selMateriel" required>
                </select>
              </div><br /><br />

              <div class="col-sm-7 col-xs-12">
                <label id="lblAides" style="font-weight: bold; ">Aides <span id="spAide" style="color: darkblue;font-weight:bold;"></span></label><br />
                <select class="form-control" name="aides" id="aides" required>
                  <option value="0">Choisir</option>

                </select>
              </div>
              <div class="form-group">
                <br /><br />


                <div class="col-sm-7 col-xs-12">
                  <label style="font-weight: bold; ">Secteurs  </label><br />
                  <select class="form-control" name="secteur" id="secteur" required>
                    <option value="">Choisir</option>
                    <?php echo ($SecteurRobo ?? ''); ?>
                  </select>
                </div>
              </div>
              <div class="form-group box-solid">
                <br /><br />

                <div class="col-sm-7 col-xs-12">
                  <label style="font-weight: bold; ">Note d'envoi et confirmation par courriel </label><br />
                  <textarea class="form-control" name="noteenvoi" id="noteenvoi" rows="3"></textarea>
                  <button type="button" onclick="sendConfirm(2)" class="btn btn-info" style="margin:4px 5px 0 0;width:285px"><i class="glyphicon glyphicon-send" style="color:lightyellow;margin-top:-5px;font-size:18px;"></i>&nbsp;&nbsp;Envoyer maintenant</button>
                </div>
              </div>
            </div>

            <br />
            <br />


          </div>
          <div class="modal-footer">
            <button type="button" onclick="PostCedule()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
            <button type="button" onclick="ShowRemoveLigne()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer cette ligne</button>
            <button type="button" onclick="CloseModalCedule()" class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
          </div>
        </div>

      </div>
    </div>

    <div class="modal fade" id="modalCedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header btn-primary">
            <button type="button" class="close" onclick="CloseModalCedule()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="plTitre" style="color:white;"></h3>

          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <label style="font-weight: bold; "># Camions  </label><br />
              <input type="hidden" id="activeIdCedule" />
              <div class="col-sm-7 col-xs-12">


                <select class="form-control" name="camionligne" id="camionligne" required>
                  <option value="">Choisir</option>

                </select>
              </div>
              <br /><br />

              <div class="col-sm-7 col-xs-12">
                <label style="font-weight: bold; ">Chauffeurs <span id="spChauffeur" style="color: darkblue;font-weight:bold;"></span></label><br />
                <select class="form-control" onchange="getChauffeur(this.value)" name="typechaufModal" id="typechaufModal" required>
                  <option value="chauf">Chauffeurs</option>
                  <option value="4">Chauffeurs Roll Off</option>
                </select>
              </div><br /><br />
              <br /><br />

              <div class="col-sm-7 col-xs-12">
                <label id="lblAides" style="font-weight: bold; ">Aides <span id="spAide" style="color: darkblue;font-weight:bold;"></span></label><br />
                <select class="form-control" style="height:270px" multiple name="aide" id="aide" required>
                  

                </select>
              </div>
              <div class="form-group">
                <br /><br />


                <div class="col-sm-7 col-xs-12">
                  <label style="font-weight: bold; ">Secteurs  </label><br />
                  <select class="form-control" name="secteur" id="secteur" required>
                    <option value="">Choisir</option>
                    <?php echo ($SecteurRobo ?? ''); ?>
                  </select>
                </div>
              </div>
              <div class="form-group box-solid">
                <br /><br />

                <div class="col-sm-7 col-xs-12">
                  <label style="font-weight: bold; ">Note d'envoi et confirmation par courriel </label><br />
                  <textarea class="form-control" name="noteenvoi" id="noteenvoi" rows="3"></textarea>
                  <button type="button" onclick="sendConfirm(2)" class="btn btn-info" style="margin:4px 5px 0 0;width:285px"><i class="glyphicon glyphicon-send" style="color:lightyellow;margin-top:-5px;font-size:18px;"></i>&nbsp;&nbsp;Envoyer maintenant</button>
                </div>
              </div>
            </div>

            <br />
            <br />


          </div>
          <div class="modal-footer">
            <button type="button" onclick="PostCedule()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
            <button type="button" onclick="ShowRemoveLigne()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer cette ligne</button>
            <button type="button" onclick="CloseModalCedule()" class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
          </div>
        </div>

      </div>
    </div>

    <div class="modal fade" id="modalRemoveLigneCedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header btn-danger">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">ATTENTION&nbsp;</h3>

          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">

                <div style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">

                  <h3 class="box-title">Attention! &Ecirc;tes-vous certain de vouloir supprimer cette ligne?</h3>
                  <input type="hidden" id="idlignetodelete" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="RemoveLigneCedule()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalRemoveVille" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header btn-danger">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">ATTENTION&nbsp;</h3>

          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">

                <div style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">

                  <h3 class="box-title">Attention! &Ecirc;tes-vous certain de vouloir supprimer cette VILLE et toutes ses lignes?</h3>
                  <input type="hidden" id="idgrptodelete" />
                  <input type="hidden" id="colgrptodelete" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="DeleteVilleCreate()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="modalShowNewHS" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog  modal-lg">
        <div class="modal-content">
          <div class="modal-header btn-danger">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" style="color:white;text-align:center;">CAMIONS HORS SERVICE&nbsp;</h3>

          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">

                <div style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">

                  <h3 class="box-title">Nouveaux changements: <span id="idNbRearEnd">10</span>&nbsp;&nbsp;REAR END&nbsp;&nbsp;<span id="idNbRobot">10</span>&nbsp;&nbsp;ROBOT</h3>

                </div>
              </div>
              <div class="form-group" style="overflow: scroll;max-height: 500px;">
                <table id="tblnewhs" class="table table-striped" style="width: 650px;" width="600">
                  <thead>
                    <tr style="text-align:right;border-width: 2px;border-color: black;border:thin;font-size:medium;font-weight:bold;">
                      <td class="cellule" align="center"># CAMION</td>
                      <td class="cellule" align="center">Date H.S.</td>
                      <td class="cellule" align="center">Retour</td>
                      <td class="cellule" align="center">TypeCam.</td>
                      <td class="cellule" align="center">Note</td>
                    </tr>
                  </thead>
                  <tbody id="tboNewHS"></tbody>
                </table>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="setMessageView()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Fermer</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modalShowDrop" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header btn-danger">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="titreShowDrop" style="color:white;text-align:center;">CAMIONS HORS SERVICE&nbsp;</h3>
            <input type="hidden" id="edtCeduleDrop" />
            <input type="hidden" id="edtVilleDrop" />
            <input type="hidden" id="edtOldColDrop" />
          </div>
          <div class="modal-body">
            <form>
              <div class="box-header with-border">
                <h3>S&eacute;lectionner dans quelle Colonne mettre le secteur</h3>
                <select id="selColonnes" class="form-control" style="width: 140px">
                  <option value="0" selected>Choisir</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="setNewVille()" class="btn btn-success" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modalShowSend" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header btn-danger">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="titreShowDrop" style="color:white;text-align:center;">ENVOI DE CONFIRMATION&nbsp;</h3>
            <input type="hidden" id="edtCeduletoSend" />
          </div>
          <div class="modal-body">
            <form>
              <div class="box-header with-border">
                <h3>S&eacute;lectionner la date d'avis</h3>
                <input type="date" class="form-control" style="height: 35px;font-weight:bold;width:150px" onchange="loadToday()" value="<?php echo ($ActiveDate ?? ''); ?>" id="dteEnvoi" />
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="sendConfirm(1)" class="btn btn-success" data-dismiss="modal" style="margin:4px 5px 0 0;"><i class="glyphicon glyphicon-send"></i>&nbsp;ENVOYER</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modalShowHres" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header btn-danger">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="titreShowAidesHres" style="color:white;text-align:center;">Heures des Aides du &nbsp;</h3>
            <input type="hidden" id="edtCeduletoSend" />
          </div>
          <div class="modal-body">
            <div class="box-body" style="margin-left:20px;">
              <button type="button" onclick="PrintHresAides()" class="btn btn-primary">
                <i class="fa fa-download"></i> Imprimer
              </button>
              <a href="#" onclick="HrestoExcel()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Heures des Aides" data-content="Exporter vers EXCEL les heures de la journée active.">
                <img src="/assets/img/excel.png" width="30" />
              </a>
              <table id="tblmodalhresaides" class="display responsive nowrap" style="width: 450px;border-width: 2px;border-color: black;border:thin;" width="400">
                <thead>
                  <tr style="text-align:right;border-width: 2px;border-color: black;border:thin;">
                    <td class="cellule" align="center">Nom</td>
                    <td class="cellule" align="center">D&eacute;but</td>
                    <td class="cellule" align="center">Fin</td>
                    <td class="cellule" align="center">Pause</td>
                    <td class="cellule" align="center">Note</td>
                  </tr>
                </thead>
                <tbody id="tbomodalRHhres"></tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Fermer</button>
          </div>
        </div>
      </div>
    </div>
    <?php echo view('sections/inc_pop_up_gen'); ?>
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
    <script src="/assets/pronto/vendor/pnotify/pnotify.custom.js"></script>
    <script src="/assets/select2/js/select2.full.min.js"></script>
    <script src="/assets/js/requetes.js"></script>
    <script src="/assets/js/nika.js"></script>
    <script>
      $(function () {
        $('.select2').select2()
      })
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";

    function PrintHresAides() {
      var urlName = '/sanivan/printhresaides/' + $("#dteRH").val();
      window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
    }

    function ShowHresAides() {
      if ($('#divHresAides').css('display') !== 'none') {
        $('#divHresAides').hide();
        $('#divShowCed').css('margin-left', '5px');
        $('#divShowCed').css('width', '99vw');
        if (ActiveDrop == 1) {
          $('#divShowCed').css('margin-left', '225px');
          $('#divShowCed').css('width', '87vw');
          $('#divDropCedule').show();
        }
        AideIsShow = '0';
      }
      else {
        $('#divDropCedule').hide();
        $('#divHresAides').show();
        $('#divShowCed').css('margin-left', '515px');
        $('#divShowCed').css('width', '72vw');
        AideIsShow = '1';
        ActiveDrop = 0;
      }
      var urlName = '/sanivan/aujourdhui/4/' + $('#dteActivePage1').val() + '?activedrop=' + ActiveDrop + '&AideIsShow=' + AideIsShow + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val();
      $.ajax({
        url: urlName,
        context: document.body
      }).done(function (data) {
        var objData = JSON.parse(data);

        $("#divCreate").html(objData.createtoday);
        $("#divCedule").html(objData.today);
        $("#spRearEnd").html(objData.spRearEnd);
        $("#spRobot").html(objData.nbRobot);
        $("#spTotalCam").html(objData.nbAllCam);
        if (objData.limitcedule === "1") {
          $("#chkRearLoad").prop('checked', true);
        }
        else if (objData.limitcedule === "2") {
          $("#chkRobot").prop('checked', true);
        }
      });
    }
    function ShowDivDrop() {
      if ($('#divDropCedule').css('display') !== 'none') {
        $('#divDropCedule').hide();
        ActiveDrop = 0;
        AideIsShow = '0';
        $('#divHresAides').hide();
        $('#divShowCed').css('margin-left', '5px');
        $('#divShowCed').css('width', '99vw');
      }
      else {
        $('#divHresAides').hide();
        $('#divDropCedule').show();
        ActiveDrop = 1;
        $('#divShowCed').css('margin-left', '225px');
        $('#divShowCed').css('width', '87vw');
      }
      var urlName = '/sanivan/aujourdhui/4/' + $('#dteActivePage1').val() + '?activedrop=' + ActiveDrop + '&AideIsShow=' + AideIsShow + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val();
      $.ajax({
        url: urlName,
        context: document.body
      }).done(function (data) {
        var objData = JSON.parse(data);

        //$("#divCreate").html(objData.createtoday);
        $("#divCedule").html(objData.today);
        $("#spRearEnd").html(objData.spRearEnd);
        $("#spRobot").html(objData.nbRobot);
        $("#spTotalCam").html(objData.nbAllCam);
        if (objData.limitcedule === "1") {
          $("#chkRearLoad").prop('checked', true);
        }
        else if (objData.limitcedule === "2") {
          $("#chkRobot").prop('checked', true);
        }
      });
    }
    function ClickHresAides(idsuivis, field, from) {
      var d = new Date();
      var minutes = ((d.getMinutes() < 10) ? '0' + d.getMinutes() : d.getMinutes());
      var heures = ((d.getHours() < 10) ? '0' + d.getHours() : d.getHours());
      var value = heures + ":" + minutes;
      var urlName = '/sanivan/posthresaides?field=' + field + '&idsuivis=' + idsuivis + '&value=' + value + '&from=' + from;
      switch (field) {
        case 1:
          $("#idstar_" + idsuivis).val(value);
          break;
        case 2:
          $("#idend_" + idsuivis).val(value);
          break;
        case 3:
          $("#idpause_" + idsuivis).val(value);
          break;
      }
      $.ajax({
        async: true,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        if (msg != '') {
          //alert(msg);
        }
      });
    }
    function getChauffeur(value) {
      //--------------
      var urlName = '/sanivan/getchauffeur/' + value + '/' + $("#dteActivePage1").val() + '?id=' + ActiveCedule;
      $.ajax({
        async: true,
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
        async: true,
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
        '&activedrop=' + ActiveDrop + '&notab=' + notab + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val();
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
    function sendConfirm(from) {
      $("*").css("cursor", "progress");
      $("#sendwait_popup").modal("show");
      var urlName = '';
      switch (from) {
        case 1:
          urlName = base_url + 'envoi?dteenvoi=' + $("#dteEnvoi").val();
          break;
        case 2:
          urlName = base_url + 'envoi?idcedule=' + $("#activeIdCedule").val() + '&note=' + $("#noteenvoi").val() + '&dteenvoi=' + $("#dteActivePage1").val();
          break;
        case 3:
          urlName = base_url + 'envoi?idcedule=&note=&dteenvoi=' + $("#dteActivePage1").val();
        case 4:
          urlName = base_url + 'envoi?idcedule=&note=&dteenvoi=' + $("#dteActivePage1").val();
      }

      //alert(urlName);
      $.ajax({
        async: true,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        $("#sendwait_popup").modal("hide");
        //alert(msg);
        $("*").css("cursor", "auto");
      });
    }
    //function sendConfirm(from) {
    //  var urlName = '';
    //  switch (from) {
    //    case 1:
    //      urlName = '/envoi?dteenvoi=' + $("#dteEnvoi").val();
    //      break;
    //    case 2:
    //      urlName = '/envoi?idcedule=' +  $("#activeIdCedule").val() + '&note=' + $("#noteenvoi").val() + '&dteenvoi=';
    //      break;
    //    case 3:
    //      urlName = '/envoi?idcedule=&note=&dteenvoi=' + $("#dteActivePage1").val();
    //  }
    // $.ajax({
    //  async: false,
    //  type: "POST",
    //  url: urlName
    // }).done(function (msg) {
    //    alert(msg);
    // });
    //}
    //function modalConfirm() {
    //  $("#dteEnvoi").val('');
    //  $("#modalShowSend").modal("show");
    //}
    function toExcel() {
      document.location = "/excel/cedule/" + $('#dteActivePage1').val();
    }
    function HrestoExcel() {
      document.location = "/excel/hres/" + $('#dteRH').val();
    }
    function ShowVillesCachees() {
      activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
      var urlName = "/sanivan/aujourdhui/5/" + ActiveDate + '?typecedule=' + activecedule + '&activedrop=' + ActiveDrop +
        '&notab=' + notab + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val() +
        '&typecam=' + typecam + '&action=' + activeRH + '&showallvilles=1';
      $.ajax({
        async: true,
        type: "POST",
        url: urlName
      }).done(function (data) {
        if (data !== "") {
          var objData = JSON.parse(data);
          $("#divCedule").html(objData.today);
          $("#spRearEnd").html(objData.spRearEnd);
          $("#spRobot").html(objData.nbRobot);
          $("#divDropCamion").html(objData.CamionDrop);
          $("#divDropRH").html(objData.ChauffeurDrop);
          $("#non_dispo_chauf").html(objData.non_dispo_chauf);
          $("#hs_dispo_cam").html(objData.hs_cam);
          $("#non_dispo_cam").html(objData.non_dispo_cam);
          $("#non_dispo_aide").html(objData.non_dispo_aide);
          $("#spTotalCam").html(objData.nbAllCam);
          $("#tboRHhres").html(objData.listhresaides);
          $("#dteRH").val(objData.dtetoday);
          if (objData.limitcedule === "1") {
            $("#chkRearLoad").prop('checked', true);
          }
          else if (objData.limitcedule === "2") {
            $("#chkRobot").prop('checked', true);
          }

        }
      });
    }
    SaniTimer = setTimeout( "_verifReq()", 10 * 1000 );
    //setInterval(_TodaySessionExpired(), 40000);
    //window.onload = function() {
    //myTimer = setInterval( function () {
    //  _TodaySessionExpired();
    //}, 60000); //Then set it to run again after five minutes
    //}
    </script>
    <!-- Modal pour gérer les polylines -->
<!-- Modal pour gérer les polylines -->
<div class="modal fade" id="modalPolyline" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="color:white;">
                    <i class="fa fa-route"></i> Gestion des parcours
                </h4>
            </div>
            <div class="modal-body">
                <!-- Champs cachés -->
                <input type="hidden" id="modalPolylineIdCedule">
                <input type="hidden" id="modalPolylineIdVille">
                <input type="hidden" id="modalPolylineId">

                <div class="row">
                    <!-- Colonne gauche : Parcours disponibles -->
                    <div class="col-md-6">
                        <label><i class="fa fa-list" style="margin-right:4px;"></i>Parcours disponibles :</label>
                        <div id="polylineAvailableList" 
                             style="max-height:400px; overflow-y:auto; border:1px solid #ddd; padding:10px; background:#fafafa;">
                            <p class="text-muted">Chargement...</p>
                        </div>
                    </div>
                    <!-- Colonne droite : Parcours sélectionnés -->
                    <div class="col-md-6">
                        <label><i class="fa fa-check-circle" style="margin-right:4px;color:green;"></i>Parcours sélectionnés :</label>
                        <div id="selectedPolylinesList" class="well well-sm" 
                             style="min-height:50px; max-height:400px; overflow-y:auto;">
                            <span class="text-muted">Aucun parcours sélectionné</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" 
                        onclick="clearAllPolylines()">
                    <i class="fa fa-trash"></i> Tout supprimer
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pour visualiser les polylines sur carte -->
<div class="modal fade" id="modalMapPolylinesCedule" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" style="color:white;">
                    <i class="fa fa-map"></i> Visualisation des parcours - <span id="spSecteurNameCedule"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div id="polylineMapCedule" style="height: 600px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var btn = document.getElementById('btnToggleSidebar');
    var div = document.getElementById('divDropCedule');
    if (!btn || !div) return;
    btn.addEventListener('click', function(e){
        e.stopPropagation();
        div.classList.toggle('sidebar-open');
        btn.innerHTML = div.classList.contains('sidebar-open')
            ? '<i class="fa fa-times"></i>'
            : '<i class="fa fa-bars"></i>';
    });
    document.addEventListener('click', function(e){
        if (window.innerWidth > 768) return;
        if (!div.classList.contains('sidebar-open')) return;
        if (div.contains(e.target) || btn.contains(e.target)) return;
        div.classList.remove('sidebar-open');
        btn.innerHTML = '<i class="fa fa-bars"></i>';
    });

    // Rendre le modal déplaçable en mode mobile
    if (window.innerWidth <= 768) {
        $(div).draggable({
            handle: '.box-header',
            containment: 'window',
            scroll: false
        });

        // Rendre le modal redimensionnable
        $(div).resizable({
            handles: 'se',
            minWidth: 200,
            maxWidth: 350,
            minHeight: 300,
            maxHeight: 600,
            containment: 'window'
        });

        // Fermer le modal automatiquement après 30 secondes si l'utilisateur n'interagit pas
        var autoCloseTimer;
        div.addEventListener('touchstart', function() {
            clearTimeout(autoCloseTimer);
        });
        div.addEventListener('touchend', function() {
            clearTimeout(autoCloseTimer);
            autoCloseTimer = setTimeout(function() {
                div.classList.remove('sidebar-open');
                btn.innerHTML = '<i class="fa fa-bars"></i>';
            }, 30000);
        });
    }
})();
</script>
  </body>
</html>



















