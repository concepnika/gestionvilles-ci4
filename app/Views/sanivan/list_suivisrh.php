<?php

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>FASER</title>

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
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
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
        <div id="myNavbar" style="margin-left:5%;">
        <ul class="nav navbar-nav form-inline">
          <li><label  style="font-size:24px;font-weight:600;">Gestion des Suivis RH</label> </li>
          <li> &nbsp;&nbsp;</li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>DE&nbsp;&nbsp; </label>&nbsp;&nbsp;
          </li>
          <li>
            <input type="date" class="form-control" onchange="LoadSuivisRH()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($datestart ?? '') ; ?>" id="dtedeb" />
          </li>
          <li>&nbsp;&nbsp;</li><li>&nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>À&nbsp;&nbsp;</label>&nbsp;&nbsp;
          </li>
          <li>&nbsp;&nbsp;</li>
          <li>
            <input type="date" class="form-control" onchange="LoadSuivisRH()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($dateend ?? '') ; ?>" id="dtefin" />
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:140px">Type d'absence</label>
              <select id="selStatutRH" onchange="SelectTypeStatutRH(this.value)" class="form-control" style="width:150px;">
                <?php echo ($typeabsences ?? '') ; ?>
              </select>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li style="display:none;">
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:120px">Ordre de tri</label>
              <select id="selOrderRH" onchange="SelectOrderSuivisRH(this.value)" class="form-control" style="width:150px;">
                <option <?php echo (($fieldorder == 'idrh')? 'selected' : '' )  ; ?> value="idrh"># Camion</option>
                <option <?php echo (($fieldorder == 'datestart')? 'selected' : '' )  ; ?> value="datestart">Date Début</option>
                <option <?php echo (($fieldorder == 'dateend')? 'selected' : '' )  ; ?>  value="dateend">Date Fin</option>
                <option <?php echo (($fieldorder == 'idabsence')? 'selected' : '' )  ; ?> value="idabsence">Statut</option>
              </select>
              </span>
              <input id="edtsens" type="hidden" value="<?php echo ($sens ?? '') ; ?>" />
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
                <input type="text" id="edtFind" class="form-control" onkeyup="EntersearchTableSuivis(this.value, event)" placeholder="Rechercher" style="width:200px;" />

                <button type="button" class="btn btn-info btn-flat" id="sr" onclick="searchInTableSuivis()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affiche la liste selon le critère.">
                  <i class='fa fa-search' style='font-size:18px;'></i>&nbsp;
                </button>
                <button type="button" class="btn btn-danger btn-flat" onclick="ClearsearchTableSuivis()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affacer la recherche.">
                  <i class='fa fa-remove' style='font-size:18px;'></i>&nbsp;
                </button>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <button type="button" onclick="window.print()" class="btn btn-primary" style="margin-left: 25px;">
                  <i class="fa fa-download"></i> Imprimer
            </button>
          </li>
        </ul>

      </div> 
             <div class="row" style="margin-top: 70px;" >
              <div class="col-lg-1">
                </div>
              <div class="col-lg-7 box box-info">
		            <section class="panel panel-featured-left panel-featured-primary">
			            <div class="panel-body">
				            <section class="card">

						            <div class="card-body">
                <div id="divAddSecteur"  style="display:none; margin-left:50px;margin-top:20px">
                <form  method="post" enctype="multipart/form-data" id="form_0"  name="form_0" >
														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
															 <tr>
                                 <td colspan="2"><h4>Nom :</h4><br> <input class="form-control"  id="edtNomAdd" type="text" value="" style="width:100%">


                              <td> &nbsp;&nbsp;&nbsp;</td>
                            </tr>

                              <tr>
                                <td colspan="3">&nbsp;</td>
															</tr>
                            <tr>
                              <td ><button type="button" onclick="sendSecteurInfo(0)" class="btn-success btn" > Enregistrer </button></td>
																	<td >		<button type="button" onclick="ShowHideNika('divAddSecteur')" class="btn-danger btn" > Annuler </button></td>
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
                              <div class="col-lg-1">
                </div>
            	<div class="col-lg-8">
		            <section >
			            <div class="panel-body">
				            <section class="card">

						            <div class="card-body" >
                          <table id="tblproduits"  class="table table-bordered table-striped" style="width: 800px;">
                            <thead id="tbhProd">
                              <?php echo ($entete ?? '') ; ?>
                            </thead>
                            <tbody id="tboProd">
                              <?php echo ($listsuivisrh ?? '') ; ?>
                            </tbody>
                          </table>
						            </div>
				            </section>
			            </div>
		            </section>
	            </div>
              <div class="col-lg-2">
                </div>
            </div>
</div>
  <?php echo view('sections/inc_pop_up_gen');?>

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
     $(function () {
    $('#tblproduits').DataTable({
      'paging': true,
      'responsive': true,
      "pagingType": "full",
      'lengthChange': true,
      'pageLength': 20,
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
    function ShowDeleted() {
    //  var showisdeleted = ' WHERE isdeleted = 0 ';
		  //if ($("#chkShowIsDeleted").prop('checked') == true) {
			 // showisdeleted = '';
		  //}
		  //urlName = "/sanivan/editcam?showdeleted=" + showisdeleted + "&from=1";
		  //$.ajax({
			 // async: false,
			 // type: "POST",
			 // url: urlName
		  //}).done(function (msg) {
			 // //----------------
    //    $("#tboProd").html(msg);
		  //});
    }
        mySessionCheck = setTimeout( "checkSession()", 20 * 60 * 1000 );
  </script>
</body>
</html>
