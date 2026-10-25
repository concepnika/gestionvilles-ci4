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

  <script src="/assets/pronto/vendor/jquery/jquery.js"></script>
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/fullcalendar.min.css" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/fullcalendar.min.js "></script>
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <script type="text/javascript" src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
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
  <link rel="stylesheet" href="/assets/css/AdminLTE.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/bower_components/Ionicons/css/ionicons.min.css" />
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
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
  </style>
  <script src="/assets/css/fontawesome/all.css"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo date("Y-m-d") ?>";
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

      <?php echo view('sections/menu');?>
    </nav>

  <div class="wrapper" style="margin-top: 5%;">
            <header class="page-header">
          <h2>Gestion des villes</h2>
        </header>
             <div class="row" >
              <div class="col-lg-1">
                </div>
              <div class="col-lg-7 box box-info">
		            <section class="panel panel-featured-left panel-featured-primary">
			            <div class="panel-body">
				            <section class="card">

						            <div class="card-body">
                <button type="button" style="margin-top:5px;margin-bottom: 5px;" onclick="ShowHideNika('divAddVille')" class="btn btn-info" >Ajouter une ville</button>
                <div id="divAddVille"  style="display:none">
                  <form action="/sanivan/addville//0" method="post" enctype="multipart/form-data" id="form_0"  name="form_0" >
										<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
												<tr>
                          <td colspan="2"><h4>Nom :</h4><br> <input class="form-control"  id="edtNomAdd" type="text" value="" style="width:100%">
                          <td> &nbsp;&nbsp;&nbsp;</td>
                      <td colspan="2"><h4>Déplacement :</h4><br> <input class="form-control"  id="edtDeplacementAdd" type="text" value="" style="width:100%">
                      </td>
                              
                      <td> &nbsp;&nbsp;&nbsp;</td>
                      <td colspan="2"><h4>Couleur :</h4><br> <input type="color" id="edtColorAdd" name="edtColorAdd" value="#ff0000"></td>
                    </tr>
                            
                      <tr>
                        <td colspan="8">&nbsp;</td>
											</tr>
                    <tr>
                      <td ><button type="button" onclick="sendVilleInfo(0)" class="btn-success btn" > Enregistrer </button></td>
													<td >		<button type="button" onclick="ShowHideNika('divAddVille')" class="btn-danger btn" > Annuler </button></td>
                    </tr>
										</table>
                  
									</form>
                </div>
						            </div>
				            </section>
			            </div>
		            </section>
              </div>
                <div class="col-lg-3">
                </div>
              </div>
            <div class="row" >
            	<div class="col-lg-12  box box-info">
 

		            <section class="panel panel-featured-left panel-featured-primary">
			            <div class="panel-body">
				            <section class="card">

						            <div class="card-body" style="overflow:scroll" >
                          <table id="tblproduits" class="display responsive nowrap" style="min-width:800px">
                            <thead>
                                <tr style="text-align:right;">
                                  <td class="cellule" align="center">Supprim&eacute;</td>
    							                <td class="cellule" align="center">Nom</td>
                                  <td class="cellule" align="center">Couleur</td>
                                  <td class="cellule" align="center">Envoi</td>
                                  <td class="cellule" align="center" width="200"><label style="width: 200px;"> Affichier les courriels</label> </td>
                                  <td class="cellule" align="center">Enregistrer</td>
                                </tr>
                            </thead>
                            <tbody id="tboProd">
                              <?php echo ($listville ?? '') ; ?>
                            </tbody>
                          </table>
                        </div>
                      
                    </section>
                  </div>
                </section>
              </div>
            </div>
</div>
 
  <div class="modal fade" id="modal_invitation" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header btn-info">
          <input type="hidden" id="empl_poste" />
          <h2 class="card-title"><img src="/assets/img/logo.png" width="200" />&nbsp;&nbsp;Information des villes</h2>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="min-height:500px;">
          <form>
            <input type="hidden" value="" id="idville" />
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail1">Courriel</label>
                <input type="email" class="form-control" id="inputEmail1" placeholder="Courriel" />
              </div>
              <div class="form-group col-md-2 mb-2 mb-lg-0">
                <label for="inputPassword1">Mot de passe</label>
                <input type="password" class="form-control" id="inputPassword1" placeholder="Mot de passe" />
              </div>
              <div class="form-group col-md-3 mb-3 mb-lg-0">
                <div class="input-group" style="width:215px;margin-top:25px">
                  <label class="form-control" style="width:180px">Envoyer l'information</label>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-success" onclick="sendPostMail(1)" data-dismiss="modal" style="margin:0 0 0 0;height:33px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Envoi de l'information" data-content="Cliquer pour envoyer l'information de connexion."><i class="glyphicon glyphicon-send"></i></button>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail2">Courriel</label>
                <input type="email" class="form-control" id="inputEmail2" placeholder="Courriel" />
              </div>
              <div class="form-group col-md-2 mb-2 mb-lg-0">
                <label for="inputPassword2">Mot de passe</label>
                <input type="password" class="form-control" id="inputPassword2" placeholder="Mot de passe" />
              </div>
              <div class="form-group col-md-3 mb-3 mb-lg-0">
                <div class="input-group" style="width:215px;margin-top:25px">
                    <label class="form-control" style="width:180px" >Envoyer l'information</label>
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-success" onclick="sendPostMail(2)" data-dismiss="modal" style="margin:0 0 0 0;height:33px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Envoi de l'information" data-content="Cliquer pour envoyer l'information de connexion."><i class="glyphicon glyphicon-send"></i></button>
                    </span>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail3">Courriel</label>
                <input type="email" class="form-control" id="inputEmail3" placeholder="Courriel" />
              </div>
              <div class="form-group col-md-2 mb-2 mb-lg-0">
                <label for="inputPassword3">Mot de passe</label>
                <input type="password" class="form-control" id="inputPassword3" placeholder="Mot de passe" />
              </div>
              <div class="form-group col-md-3 mb-3 mb-lg-0">
                <div class="input-group" style="width:215px;margin-top:25px">
                  <label class="form-control" style="width:180px">Envoyer l'information</label>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-success" onclick="sendPostMail(3)" data-dismiss="modal" style="margin:0 0 0 0;height:33px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Envoi de l'information" data-content="Cliquer pour envoyer l'information de connexion."><i class="glyphicon glyphicon-send"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </form>
          <div class="card-body" style="margin-left:15px;margin-top:10px;">
            <table id="tblboites" class="display responsive nowrap" border="1" width="800">
              <thead>
                <tr>
                  <th colspan="3" style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;">Demande de correctifs actifs(non résolus)</th>
                </tr>
                <tr style="text-align:right;">
                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;width:300px;" align="center">Adresse</td>
                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Date du ramassage</td>
                  <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;" align="center">Date de la correction</td>

                </tr>
              </thead>
              <tbody id="tboDelCom">
                <?php //echo ($listnotedelete ?? ''); ?>
              </tbody>
            </table>
          </div>          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="postAllVilleMail()" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer tout</button>
          <button type="button" class="btn btn-primary" onclick="sendAllAccess()" data-dismiss="modal" style="margin:4px 5px 0 0;" data-toggle="popover" data-trigger="hover" data-placement="top" title="Envoi de l'information" data-content="Cliquer pour envoyer l'information de connexion.">Envoyer tout</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>

        </div>
      </div>

    </div>
  </div>
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
     $(function () {
    $('#tblproduits').DataTable({
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
          "sFirst": "<span><b><i class='fa fa-angle-double-left' style='font-size:28px;'></i></b>&nbsp;</span>",
          "sPrevious": "<span><b><i class='fa fa-angle-left' style='font-size:28px;'></i></b>&nbsp;</span>",
          "sNext": "<span><b><i class='fas fa-angle-right' style='font-size:28px;'></i></b>&nbsp;</span>",
          "sLast": "<span><b><i class='fas fa-angle-double-right' style='font-size:28px;'></i></b>&nbsp;</span>"
        }
      }
    })
  })
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

    function sendPostMailAll(noligne) {
      var urlName = '/ville/send/post/' + $("#idville").val() +
        "?email=" + encodeURIComponent($("#InputEmail" + noligne).val()) +
        "&pass=" + encodeURIComponent($("#inputPassword" + noligne).val());
    }
    setTimeout( "checkSession()", 20 * 60 * 1000 );
  </script>
</body>
</html>
