
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
  <link rel="stylesheet" href="/assets/vendor/pnotify/pnotify.custom.css" />
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

    <?php echo ($classstat ?? '') ?>

    button:hover {
      opacity: 0.8;
    }
  </style>

  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'><?php echo ci3_url() ?><?php echo ($ActiveDate ?? '') ?><?php echo ($typecedule ?? '') ?><?php echo ($ActiveDrop ?? '') ?><?php echo ($LastRow ?? '') ?><?php echo ($notab ?? '') ?><?php echo ($AideIsShow ?? '') ?><?php echo ($from ?? '') ?></script>
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
      echo view('sections/inc_menu');
      ?>
    </nav>


    <div id="divShowCed" style="margin-top: 60px;margin-left:10px;<?php echo (($from != 3) ? 'height: 75vh; width: 98vw;overflow:scroll; position:page;' : ''); ?>">
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

    <div id="divFooterSan" class="footer" style="margin-top:5px;width:100%">

      <?php
      echo ($requetes ?? '');
      ?>

    </div>
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
                <select class="form-control" name="aide" id="aide" required>
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
              <div class="col-sm-7 col-xs-12">
                <select class="form-control" name="chauffeur" id="chauffeur" required>
                  <option value="25">Choisir</option>

                </select>
              </div>
              <br /><br />


              <div class="col-sm-7 col-xs-12">
                <label id="lblAides" style="font-weight: bold; ">Aides <span id="spAide" style="color: darkblue;font-weight:bold;"></span></label><br />
                <select class="form-control" name="aide" id="aide" required>
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

    <div class="modal fade" id="modalAddVille" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-block-md">
        <div class="modal-content">
          <div class="modal-header btn-info">
            <h3 class="modal-title" id="plTitreMsg" style="text-align:center;">Ajout de ville</h3>

          </div>
          <div class="modal-body">
            <form class="form-group" style="margin-bottom:20px;">

              <div>

                <div class="col-sm-7 col-xs-12">
                  <label style="font-weight: bold; ">Ville</label><br />
                  <select class="form-control" name="selVilleslist" id="selVilleslist" required>
                    <option value="">Choisir</option>
                    <?php echo ($optville ?? ''); ?>
                  </select>
                </div>


                <div class="col-sm-7 col-xs-12" style="margin-top:25px;">
                  <div class="input-group" style="width:240px">
                    <label class="form-control" style="font-weight: bold;width:130px">REAR AND</label>
                    <span class="input-group-btn">&nbsp;&nbsp;
                      <input type="radio"  style="font-size:medium;font-weight:bold;height:40px;width:40px;margin-top:-5px" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="1" name="radTypeCam" id="typeREREND" />
                    </span>
                    <label class="form-control" style="font-weight: bold;margin-left:20px;width:130px">ROBOT</label>
                    <span class="input-group-btn">&nbsp;&nbsp;
                      <input type="radio"  style="font-size:medium;font-weight:bold;height:40px;width:40px;margin-top:-5px" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="2" name="radTypeCam" id="typeROBOT" />
                    </span>
                  </div>
                </div>

              </div>
            </form>
          </div>
          <div class="modal-footer" style="margin-top:40px;">
            <button type="button" onclick="VilleCreate()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Ajouter</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
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
    <script src="/assets/js/nika.js"></script>
    <script>
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
        async: false,
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
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        $("#sendwait_popup").modal("hide");
        alert(msg);
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
function loadTodaySani(nbjour) {
  var urlName = "/sanivan/getdate/" + nbjour + "/" + $('#dteActivePage1').val();
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$('#dteActivePage1').val(msg);
			ActiveDate = $('#dteActivePage1').val();
      var urlName = '/sanivan/aujourdhui/99/' + $('#dteActivePage1').val();
      document.location = urlName;
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
  </body>
</html>



















