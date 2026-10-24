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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
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
    .table-bordered > tbody > tr > td.no-line {
      border:none !important;
      border-right: none !important;
      border-left: none !important;
      border-top: none !important;
      border-right-style: none !important;
      border-left-style: none !important;
      }
    .titreclient{
      color: darkblue;
      width: 100%;
      height:100%;
      font-size: larger;
      font-weight:900;
    }
    .Actifclient{
      color: white;
      height:35px;
      background-color:yellowgreen;
    }
    .client{
      color: black;
      background-color:white;
    }
    .Actifcommande{
      color: black;
      height:35px;
      background-color:yellow;
    }
    .commande{
      color: black;
      background-color:white;
    }
    .progress {cursor: progress;}
    .default {cursor: default;}
  </style>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="/assets/js/nika.js"></script>
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
  <div class="wrapper" id="divContainer" style="margin-top: 5%;">

  <div class="container-fluid" style="position:absolute;left: 0px;top: 160px;max-height: 1000px; width: 300px">

      <div class="col-lg-2  box box-info pull-left">
		    <section class="panel panel-featured-left panel-featured-primary">
			    <div class="panel-body">
				    <section class="card">
                <h2>Clients</h2>
              <div class="input-group">
                            <input type="text" class="form-control" style="width:180px;" id="edtFind" onkeyup="EnterearchTable(this.value, event)" placeholder="Rechercher" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Cliquer pour rechercher le client">
                            <div class="input-group-btn">
                              <button class="btn btn-info" onclick="searchInTable()" style="height:35px" type="button">
                                <i class="glyphicon glyphicon-search"></i>
                              </button>
                              <button class="btn btn-danger" onclick="ClearsearchTableCom()" style="height:35px" type="button"  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="R&eacute;initialiser" data-content="Cliquer pour r&eacute;initialiser la recherche">
                                <i class="glyphicon glyphicon-remove"></i>
                              </button>
                            </div>
                          </div>
						    <div class="card-body" style="width:100%;min-width:300px;overflow:scroll; height:740px;margin-top:10px">
                  <table id="tblclient" class="table  table-hover" width="90%" height="100%">

                    <tbody id="tboclients">
                      <?php echo ($listclients ?? '') ; ?>
                    </tbody>
                  </table>
						    </div>
				    </section>
			    </div>
		    </section>
	    </div>
    </div>

		<div class="row" style="position:absolute;left: 350px;top: 90px;width:820px; border-bottom:solid; ">

				<div class="col-md-12">
						<ul class="nav nav-pills">
							<li id="liPageCommande"  >
								<h2>Commandes FRONT</h2>
							</li>
						</ul>
			</div>
		</div>

    <div id="divcommande" class="row" style="display:block;" >
      <div class="col-lg-10  box box-info" style="position:absolute;left: 350px;top: 160px;min-height:800px;width:1500px;">
		    <form id="frmCommande">
        <section class="panel panel-featured-left panel-featured-primary">
			    <div class="panel-body">
				    <section class="card">

						    <div class="card-body" style="width:100%;">
                  <div class="col-xs-12 table-responsive">
                    <table class="table table-striped">
                      <thead>
                      <tr>
                        <th colspan="7" style="text-align:center;"><div id="myNavbar" style="margin-left:5%;">
        <ul class="nav navbar-nav">
          <li>
            <label >&nbsp;&nbsp;&nbsp;&nbsp;</label>
          </li>
          <li>
            <label >&nbsp;&nbsp;&nbsp;&nbsp;</label>
          </li>
          <li>
            <button type="button" onclick="AddNew()" id="btnAddCom" class="btn btn-success" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er une nouvelle commande">
                  <i class="glyphicon glyphicon-plus"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="EditCom()" id="btnEditCom" class="btn btn-default" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Edition" data-content="Cliquer pour &eacute;diter la commande">
                  <i class="glyphicon glyphicon-edit"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="DeleteCom()" id="btnDeleteCom" class="btn btn-danger" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Supprimer" data-content="Cliquer pour supprimer la commande">
                  <i class="glyphicon glyphicon-trash"></i>
            </button>
          </li>
          <li>
            <button type="button" class="btn btn-success" disabled  onclick="PostCom()" id="btnPostCom" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Enregistrer" data-content="Cliquer pour enregistrer la commande">
                 <i class="glyphicon glyphicon-ok"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="CancelCom()" id="btnCancelCom" class="btn btn-warning" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;"  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Annuler" data-content="Cliquer pour annuler les modifications de la commande">
                  <i class="glyphicon glyphicon-remove"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="" class="btn btn-default" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Signature" data-content="Cliquer pour afficher la signature de la commande.">
                  <i class="glyphicon glyphicon-pencil"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="" class="btn btn-default" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;"  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Courriel" data-content="Cliquer pour envoyer la commande par courriel.">
                  <i class="glyphicon glyphicon-send"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="PrintCommande()"  class="btn btn-primary" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Immprimer" data-content="Cliquer pour imprimer la commande">
                  <i class="glyphicon glyphicon-print"></i>
            </button>
          </li>
          <li>
            <a type="button" style="height:50px;width:250px;font-size:larger;margin-left: 25px;color:red;font-weight:bold" class="btn btn-default" href="javascript:AfficheBoitesForCom()">
                  <i class="fa fa-share-square"></i> Afficher les bo&icirc;tes
            </a>
          </li>
        </ul>

      </div></th>

                      </tr>
                      </thead>
                      <tbody id="tbodyCom">
                      <tr>
                        <td colspan="7" align="left"><label id="nomclient" style="padding-bottom:2px;font-size:larger;font-weight:bold;width:100%;text-align:left;">CLIENT:&nbsp;&nbsp; <span class="titre"> RDF-ROLL OFF</span></label>
                          <input name="idclient"   id="idclient" type="hidden" value="<?php echo ($actifclient ?? '') ; ?>" />
                          <input id="actifnomclient" type="hidden" value="<?php echo ($actifnomclient ?? '') ; ?>" />
													<input id="actifcodeclient" type="hidden" value="<?php echo ($actifcodeclient ?? '') ; ?>" />
													<input id="actifcommande" type="hidden" value="<?php echo ($id ?? '') ; ?>" />
                        </td>

                      </tr>
                      <tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%"># Demande</label>
                        <input class="form-control" name="id"   id="id" type="text" value="<?php echo ($id ?? '') ; ?>" style="width:130px;color:darkred;font-weight:bold;font-size:large; " readonly /></td>
                        <td ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Code</label>
                          <select name="Code" class="form-control" id="Code" style="width:90px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($Code == 5)? 'selected' : '' ) ; ?> value="5">5</option>
                                      <option <?php echo (($Code == 6)? 'selected' : '' ) ; ?> value="6">6</option>
                                      <option value="7" <?php echo ((($Code == 7) or ($Code == 0))? 'selected' : '' ) ; ?> >7</option>
                                    </select></div></td>

                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Date de livraison</label>
                        <input class="form-control" name="date_fact"  id="date_fact" type="date" value="<?php echo ($date_fact ?? '') ; ?>" style="width:160px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Date de retour</label>
                        <input class="form-control" name="date_retour"  id="date_retour" type="date" value="<?php echo ($date_retour ?? '') ; ?>" style="width:160px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="2"> <div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de demande</label>
                                <select class="form-control" onchange="ChangeTypeDemande(this.value)" id="TypeDemande" name="TypeDemande" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option  value="-1" selected>Choisir</option>
                                      <option <?php echo (($TypeDemande == 8)? 'selected' : '' ) ; ?>  value="0" selected>Livraison</option>
                                      <option <?php echo (($TypeDemande == 3)? 'selected' : '' ) ; ?>  value="3" >Terminer</option>
                                    </select></div>
                        </td>
                        <td ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de contenant</label><select class="form-control" id="FormatContenant" name="FormatContenant" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                            <option  value="" selected>Choisir</option>          
														<?php echo ($typecontenant ?? '') ; ?>
                                    </select></div></td>
                        
                      </tr>
                        <tr>
                          <td colspan="7">
                            <label id="nomclient" style="padding-bottom:2px;font-size:larger;font-weight:bold;width:100%;text-align:center;">Sous-Commande FRONT</label>
                          </td>
                        </tr>
                      <tr>
                        <td colspan="7">
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">Rang</th>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">#Sous-commande</th>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">Clients</th>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">Adresse</th>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">Contenant</th>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">Téléphone</th>
                                <th style="text-align:center;color:darkblue;font-weight:bold;">Notes</th>
                                <th>&nbsp;&nbsp; </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php echo ($listcommandesfront ?? '') ; ?>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      
                      <tr>
                        <td colspan="3"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">D&eacute;lai de la commande</label>
                          <label class="checkbox-inline"style="text-align:left;font-size:large;color:red;font-weight:bolder;" ><input type="radio" name="Delai" id="Delai_1" <?php echo (($Delai == 1)? 'checked' : '' ) ; ?>  value="1">&nbsp;&nbsp;Urgent</label>&nbsp;&nbsp;&nbsp;&nbsp;
                          <label class="checkbox-inline"style="text-align:left;font-size:large;color:blue;font-weight:bolder;" ><input type="radio" name="Delai" id="Delai_2" <?php echo (($Delai == 2)? 'checked' : '' ) ; ?>  value="2">&nbsp;&nbsp;Voir la note</label>&nbsp;&nbsp;&nbsp;&nbsp;
                          <label class="checkbox-inline"style="text-align:left;font-size:large;color:green;font-weight:bolder;" ><input type="radio" name="Delai" id="Delai_3" <?php echo ((($Delai == 3) or ($Delai == ''))? 'checked' : '' ) ; ?>   value="3">&nbsp;&nbsp;Courant </label>&nbsp;&nbsp; </div>
                        </td>
                        <td colspan="4">
                          &nbsp;&nbsp;
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div>

						    </div>

				    </section>
			    </div>
		    </section>
        </form>
	    </div>
    </div>

</div>


<?php echo view('sections/inc_pop_up_ro');?>
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

<script>
  alert('1');
  selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
  var HaveCom = "<?php echo 0 ?>";
  var optTPS = "<?php echo ($optTPS ?? '') ?>";
  var optTVQ = "<?php echo ($optTVQ ?? '') ?>";
  var typetoshow = "<?php echo ($typetoshow ?? '') ?>";
  function GetClients(id) {
    var urlName = "/bureau/getclient/" + id;
      $.ajax({
          type: "POST",
          url: urlName
      }).done(function (msg) {
        if (msg != "") {
            $('#td_' + $("#idclient").val()).removeClass("Actifclient");
            //$('#td_' + $("#idclient").val()).addClass("client");
            var objData = JSON.parse(msg);
          $("#tbocommandes").html(objData.listcommandes);
          $("#ulListeCommandes").html(objData.dopcommande);
            $('#divlistdem135').html(objData.listdem135);
            AddNew();
            AddNewClient();
            SetActiveCommande(objData);
            SetActiveClient(objData);
						AjusteButton('0');
						AjusteButtonClient('0');
          }
          else {
						AjusteButton('0');
						AjusteButtonClient('0');
          }
      });
  }
  function ChangeTypeDemande(value) {
    switch (value) {
      case '1':
      case '3':
      case '5':
        $("#modaltypedemande").modal("show");
        $("#tdTransfert").hide();
        break;
      case '2':
      case '4':
        $("#tdTransfert").show();
        break;
      default:
        $("#tdTransfert").hide();
    }
  }
  function CreateNewCom() {
    var urlName = "/bureau/getcommandeno/" + $('#selComDispo').val();
    var typedem = $('#TypeDemande').val();
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
      if (msg != "") {
        var objData = JSON.parse(msg);
        AddNew();
        objData.TypeDemande = typedem;
        // "adressetransfert" => "", "villetransfert" => "", 
        // "provtransfert" => "QC", "codepostaltransfert"
		if ((typedem == '2') || (typedem == '4'))
		{
			objData.adresse = objData.adressetransfert;
			objData.ville = objData.villetransfert;
			objData.prov = objData.provtransfert;
			objData.codepostal = objData.codepostaltransfert;
			objData.adressetransfert = '';
			objData.villetransfert = '';
			objData.codepostaltransfert = '';			
		}
        SetActiveCommande(objData);
        SetActiveClient(objData);
        $("#actifcommande").val(0);
        //$("#Prix").val('');
        //$("#Redevance").val('');
        //$("#TPS").val('');
        //$("#TVQ").val('');
        //$("#Total").val('');
        $("#MontPaye").val('');
        $("#IsPaid").prop('checked', false);
		  $("#id").val('')
					DateActive = "<?php echo date('Y-m-d'); ?>";
		  $("#date_fact").val(DateActive);
					DateActive = "<?php echo ($dteNewRetour ?? ''); ?>";
    $("#date_retour").val(DateActive);
          $('#TypeDemande').val(typedem);
          AjusteButton('1');
        }
        else {
            AjusteButton('0');
        }
    });
  }
    var urlName = '/bureau/printcommande/' + $("#actifcommande").val();
    var strTemp = $("#actifcommande").val();
    if ($.trim(strTemp) != '') {
      window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
    }
  }
$(function () {
    $('#tblboites').DataTable({
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
$('input[type="checkbox"].minimal, input[type="checkbox"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue'
})
//Red color scheme for iCheck
$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
    checkboxClass: 'icheckbox_minimal-red',
    radioClass: 'iradio_minimal-red'
})
//Flat red color scheme for iCheck
$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass: 'iradio_flat-green'
})
//------------------------------------------------------------------------------
function searchInTableFront() {
  var urlName = "/bureau/recherche_commande?valuetofind=" + $('#edtFind').val();
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg != "") {
          var objData = JSON.parse(msg);
            $("#tboclients").html(objData.listclients);
          $('#td_' + objData.idclient).removeClass("Actifclient");
          $('#divlistdem135').html(objData.listdem135);
          AddNew();
          AddNewClient();
          SetActiveCommande(objData);
          $("#ulListeCommandes").html(objData.dopcommande);
          SetActiveClient(objData);
						$("#tbocommandes").html(objData.listcommandes);
						AjusteButton('0');
						AjusteButtonClient('0');
        }
        else {
            alert('Aucun résultat pour votre recherche');
            AjusteButton('0');
						AjusteButtonClient('0');
        }
    });
}
function EnterearchTableFront(value, e) {

    var keycode = e.keyCode ? e.keyCode : e.charCode;

    if (keycode == 13) {
      var urlName = "/bureau/recherche_commande?valuetofind=" + value;
        $.ajax({
            type: "POST",
            url: urlName
        }).done(function (msg) {
            if (msg != "") {
                var objData = JSON.parse(msg);
                $("#tboclients").html(objData.listclients);
              $('#td_' + objData.idclient).removeClass("Actifclient");
              $("#ulListeCommandes").html(objData.dopcommande);
              $('#divlistdem135').html(objData.listdem135);
              AddNew();
              AddNewClient();
              SetActiveCommande(objData);
              SetActiveClient(objData);
				$("#tbocommandes").html(objData.listcommandes);
								AjusteButton('0');
						AjusteButtonClient('0');
            }
            else {
                alert('Aucun résultat pour votre recherche');
                AjusteButton('0');
						AjusteButtonClient('0');
            }
        });
    }
}
function ClearsearchTableFront() {
    $('#edtFind').val('');
    var urlName = "/bureau/recherche_commande/none";
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg != "") {
            var objData = JSON.parse(msg);
            $("#tboclients").html(objData.listclients);
            $('#td_' + objData.idclient).removeClass("Actifclient");
          AddNew();
          AddNewClient();
          SetActiveCommande(objData);
          SetActiveClient(objData);
          $("#ulListeCommandes").html(objData.dopcommande);
          $('#divlistdem135').html(objData.listdem135);
			$("#tbocommandes").html(objData.listcommandes);
						AjusteButton('0');
						AjusteButtonClient('0');
        }
        else {
            AjusteButton('0');
						AjusteButtonClient('0');
        }
    });
}

function GetCommande(id) {
    var urlName = "/bureau/getcommande/" + id;
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg != "") {
            $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
            $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
            var objData = JSON.parse(msg);
          AddNew();
          AddNewClient();
          $("#ulListeCommandes").html(objData.dopcommande);
          SetActiveCommande(objData);
          SetActiveClient(objData);
            AjusteButton('0');
						AjusteButtonClient('0');
        }
        else {
            AjusteButton('0');
						AjusteButtonClient('0');
        }
    });
}
function SetActiveCommande(objData) {

    HaveCom = objData.havecom;
    $("#Recyclage").hide();
    $("#actifnomclient").val(objData.actifnomclient);
    $("#nomclient").html('CLIENT:&nbsp;&nbsp; <span class="titre">' + $("#actifnomclient").val() + '</span>');
    $("#id").val(objData.id);
    $("#actifcommande").val(((objData.id == '') ? '0' : objData.id));
    $("#idclient").val(objData.idclient);
    $("#clientid").val(objData.idclient);
    //$('#td_' + $("#idclient").val()).removeClass("client");
    //$('#td_' + $("#idclient").val()).addClass("Actifclient");
    $('#tdcom_' + objData.id).removeClass("commande");
    $('#tdcom_' + objData.id).addClass("Actifcommande");
    $("#nom").val(objData.nom);
    $("#prenom").val(objData.prenom);
    $("#adresse").val(objData.adresse);
    $("#ville").val(objData.ville);
    $("#prov").val(objData.prov);
    $("#codepostal").val(objData.codepostal);
      $("#adressetransfert").val(objData.adressetransfert);
    $("#villetransfert").val(objData.villetransfert);
    $("#provtransfert").val(objData.provtransfert);
    $("#codepostaltransfert").val(objData.codepostaltransfert);
    $("#phone_jour").val(objData.phone_jour);
    $("#phone_soir").val(objData.phone_soir);
    $("#courriel").val(objData.courriel);
  $("#TypeDemande").val(objData.TypeDemande);
  switch (objData.TypeDemande) {
    case '2':
    case '4':
      $("#tdTransfert").show();
      break;
    default:
      $("#tdTransfert").hide();
  }
    $("#FormatContenant").val(objData.FormatContenant);
    $("#date_fact").val(objData.date_fact);
    $("#date_retour").val(objData.date_retour);
    $("#notes").val(objData.notes);
    $("#note_bureau").val(objData.note_bureau);
    $("#Tonnage").val(objData.Tonnage);
    $("#Prix").val(objData.Prix);
    $("#Redevance").val(objData.Redevance);
    $("#TPS").val(objData.TPS);
    $("#TVQ").val(objData.TVQ);
    $("#Total").val(objData.Total);
    $("#MontPaye").val(objData.MontPaye);
    if (objData.IsBeton === "1") {
        $("#IsBeton").prop('checked', true);
    }
    else {
        $("#IsBeton").prop('checked', false);
    }
    var TypePaiement = "#TypePaiement_" + objData.TypePaiement;
    $(TypePaiement).prop('checked', true);
    if (objData.IsBeton === "1") {
        $("#IsBeton").prop('checked', true);
    }
    else {
        $("#IsBeton").prop('checked', false);
    }
    if (objData.IsFumier === "1") {
        $("#IsFumier").prop('checked', true);
    }
    else {
        $("#IsFumier").prop('checked', false);
    }
    if (objData.IsBranches === "1") {
        $("#IsBranches").prop('checked', true);
    }
    else {
        $("#IsBranches").prop('checked', false);
    }
    if (objData.IsAsphalte === "1") {
        $("#IsAsphalte").prop('checked', true);
    }
    else {
        $("#IsAsphalte").prop('checked', false);
    }
    if (objData.IsTerre === "1") {
        $("#IsTerre").prop('checked', true);
    }
    else {
        $("#IsTerre").prop('checked', false);
    }
    if (objData.IsDechets === "1") {
        $("#IsDechets").prop('checked', true);
    }
    else {
        $("#IsDechets").prop('checked', false);
    }
    if (objData.IsBois === "1") {
        $("#IsBois").prop('checked', true);
        $("#Recyclage").show();
    }
    else {
        $("#IsBois").prop('checked', false);
    }
    if (objData.IsMateriaux === "1") {
        $("#IsMateriaux").prop('checked', true);
    }
    else {
        $("#IsMateriaux").prop('checked', false);
    }
    if (objData.IsBardeau === "1") {
        $("#IsBardeau").prop('checked', true);
    }
    else {
        $("#IsBardeau").prop('checked', false);
    }
    if (objData.IsDelete === "1") {
        $("#IsDelete").prop('checked', true);
    }
    else {
        $("#IsDelete").prop('checked', false);
    }
    if (objData.IsPaid === "1") {
        $("#IsPaid").prop('checked', true);
    }
    else {
        $("#IsPaid").prop('checked', false);
    }
    $("#BoiteEnPlace").val(objData.BoiteEnPlace);
    $("#Recyclage").val(objData.Recyclage);
    $("#Code").val(objData.Code);
    //$("#TypeVidage").val(objData.TypeVidage);
    $("#Delai_" + objData.Delai).prop('checked', true);
    $("#TypeCamion").val(objData.TypeCamion);
    //-------------------
    SetActiveClient(objData);
}

	function ShowGoogle(from) {
		var adresse = '';
		if (from == '1') {
			adresse = $("#adresse").val() + ", " + $("#ville").val() + " " + $("#codepostal").val();
		}
		else {
			adresse = $("#adressetransfert").val() + ", " + $("#villetransfert").val() + " " + $("#codepostaltransfert").val();
		}
    ShowInGoogle(adresse);
}
//---------------------------------------------------
function AjusteButton(Flag) {
  $("#btnCancelCom").attr("disabled", false);
  alert('2');
  $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', false);
  alert('3');
    $("#btnPostCom").attr("disabled", false);
    $("#btnAddCom").attr("disabled", false);
    //---------------------------------------------
    $("#btnEditCom").attr("disabled", true);
    $("#btnDeleteCom").attr("disabled", true)
    if (Flag == '0') {
        $("#btnCancelCom").attr("disabled", true);
      $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', true);
      alert('OK');
        $("#btnPostCom").attr("disabled", true);
        if (HaveCom == '1') {
            $("#btnEditCom").attr("disabled", false);
            $("#btnDeleteCom").attr("disabled", false);
        }
    }
    else {
        $("#btnAddCom").attr("disabled", true);
    }
}
function AddNew() {
 //   //$("#Code").val($("#actifcodeclient").val());
 //   //$('#tdcom_' + $("#actifcommande").val()).addClass("commande");
 //   //$('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
 //   //$("#actifcommande").val(0);
 //   //$("#Delai_3").prop('checked', true);
 //   //$("#TypeCamion").val(1);
 //// //  DateActive = "<?php echo date('Y-m-d'); ?>";
	////	$("#date_fact").val(DateActive);
	////	$("#TypeDemande").val(-1);
	////	$("#tdTransfert").hide();
 ////   $("#prov").val('QC');
 ////   var TypePaiement = "#TypePaiement_" + '3';
 ////   $(TypePaiement).prop('checked', true);
 ////   ------------------------------------------------------
 ////   $("#Recyclage").hide();
 ////   $("#id").val('');
 ////   $("#nom").val('');
 ////   $("#prenom").val('');
 ////   $("#adresse").val('');
 ////   $("#ville").val('');
	////$("#codepostal").val('');
	////	$("#adressetransfert").val('');
 ////   $("#villetransfert").val('');
	////	$("#codepostaltransfert").val('');
	////	$("#provtransfert").val('QC');
 ////   $("#phone_jour").val('');
 ////   $("#phone_soir").val('');
 ////   $("#courriel").val('');
 ////   $("#typecommande").val(1);
 ////   $("#FormatContenant").val('');
 ////   //DateActive = "<?php echo ($dteNewRetour ?? ''); ?>";
 //   //$("#date_retour").val(DateActive);
 //   //$("#notes").val('');
 //   //$("#note_bureau").val('');
 //   //$("#Tonnage").val('');
 //   //$("#Prix").val('');
 //   //$("#Redevance").val('');
 //   //$("#TPS").val('');
 //   //$("#TVQ").val('');
 //   //$("#Total").val('');
 //   //$("#MontPaye").val('');
 //   //$("#IsBeton").prop('checked', false);
 //   //$("#IsBeton").prop('checked', false);
 //   //$("#IsFumier").prop('checked', false);
 //   //$("#IsBranches").prop('checked', false);
 //   //$("#IsAsphalte").prop('checked', false);
 //   //$("#IsTerre").prop('checked', true);
 //   //$("#IsTerre").prop('checked', false);
 //   //$("#IsDechets").prop('checked', true);
 //   //$("#IsDechets").prop('checked', false);
 //   //$("#IsBois").prop('checked', false);
 //   //$("#IsMateriaux").prop('checked', false);
 //   //$("#IsBardeau").prop('checked', false);
 //   //$("#IsPaid").prop('checked', false);
 //   //$("#BoiteEnPlace").val('');
 //   //$("#Recyclage").val('');
    //------------------------------------------------------
    $("#btnAddCom").popover('hide');
    AjusteButton('1');
}
function EditCom() {
    AjusteButton('1');
    $("#btnEditCom").popover('hide');
}
function PostCom() {
  var formval = $("#frmCommande").serialize();
  var OldIdCommande = $("#actifcommande").val();
  var urlName = "/bureau/postcommande/" + $("#actifcommande").val() + '?' + formval;
  $("#btnPostCom").attr("disabled", true);
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
      if (msg != "") {
        var objData = JSON.parse(msg);
        if (OldIdCommande == 0) {
          GetClients($("#idclient").val());
        }
        else {
          GetClients($("#idclient").val());
          $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
          $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
          SetActiveCommande(objData);
        }
        AjusteButton('0');
        AjusteButtonClient('0');
        }
        else {
        AjusteButton('0');
        AjusteButtonClient('0');
        }
    });
    $("#btnPostCom").popover('hide');
}
function DeleteCom() {
    var message = '<span style="color: red;"> <b> Attention!</b></span> Êtes-vous certain de vouloir supprimer la commande <b>' +
        $("#actifcommande").val() + '</b>?' +
        ' <br /> <br /><span class="btn-warning"> Cette action ne pourra pas être annulée que par un Directeur.</span>';
    BootstrapDialog.confirm({
        title: 'ATTENTION',
        message: message,
        type: BootstrapDialog.TYPE_DANGER, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
        closable: true, // <-- Default value is false
        draggable: true, // <-- Default value is false
        btnCancelLabel: 'Annuler', // <-- Default value is 'Cancel',
        btnOKLabel: 'Supprimer', // <-- Default value is 'OK',
        btnOKClass: 'btn-danger', // <-- If you didn't specify it, dialog type will be used,
        callback: function (result) {
            // result will be true if button was click, while it will be false if users close the dialog directly.
            if (result) {
                var urlName = "/bureau/deletecommande/" + $("#actifcommande").val();
                $.ajax({
                    type: "POST",
                    url: urlName
                }).done(function (msg) {
                    if (msg != "") {
                        GetClients($("#idclient").val());
                        BootstrapDialog.show({
                            title: 'Suppression de cédule',
                            message: 'Votre de mande de suppression a été effectuée avec succès',
                            closable: true,
                            buttons: [{
                                label: 'Fermer',
                                action: function (dialogRef) {
                                    dialogRef.close();
                                }
                            }]
                        });
                    }
                    else {
                        AjusteButton('0');
                    }
                });
            } else {
                //alert('Nope.');
            }
        }
    });
    $("#btnDeleteClose").popover('hide');
}
function CancelCom() {
    if ($("#id").val() == 0) {
        GetClients($("#idclient").val())
    }
    else {
        GetCommande($("#id").val());
    }
    AjusteButton('0');
    $("#btnCancelCom").popover('hide');
}
//------------------------------------------------------------------------------
function searchInTableCom() {
    var urlName = "/bureau/recherche_commandeno/" + $('#edtFindno').val() + "/1";
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg != "") {
            var objData = JSON.parse(msg);
            $("#tboclients").html(objData.listclients);
            $('#td_' + objData.idclient).removeClass("Actifclient");
          AddNew();
          AddNewClient();
          SetActiveCommande(objData);
          SetActiveClient(objData);
          $("#tbocommandes").html(objData.listcommandes);
          AjusteButton('0');
						AjusteButtonClient('0');
        }
        else {
            alert('Aucun résultat pour votre recherche');
            AjusteButton('0');
						AjusteButtonClient('0');
        }
    });
}
function EnterearchTableCom(value, e) {

    var keycode = e.keyCode ? e.keyCode : e.charCode;

    if (keycode == 13) {
        var urlName = "/bureau/recherche_commandeno/" + value + "/1";
        $.ajax({
            type: "POST",
            url: urlName
        }).done(function (msg) {
            if (msg != "") {
                var objData = JSON.parse(msg);
                $("#tboclients").html(objData.listclients);
                $('#td_' + objData.idclient).removeClass("Actifclient");
                AddNew();
                AddNewClient();
                SetActiveCommande(objData);
                SetActiveClient(objData);
                $("#tbocommandes").html(objData.listcommandes);
                AjusteButton('0');
						AjusteButtonClient('0');

            }
            else {
                alert('Aucun résultat pour votre recherche');
                AjusteButton('0');
						AjusteButtonClient('0');
            }
        });
    }
}
//---------------------------------------------------
  var arrNumAccept = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 188, 189, 190];
  alert('ajuste');
  AjusteButton('0');
  $('#td_' + $("#idclient").val()).removeClass("client");
  $('#td_' + $("#idclient").val()).addClass("Actifclient");
  $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
  $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
  ShowCommandeClient('divcommande');
</script>
</body>
</html>



















