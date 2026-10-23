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
  <script src="/assets/css/fontawesome/all.css"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url();?>";
    var ActiveDate = "<?php echo date("Y-m-d"); ?>";
    var typetoshow = "<?php echo ($typetoshow ?? ''); ?>";
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
                              <button class="btn btn-success" onclick="AddClient()" style="height:35px" type="button" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er un nouveau client">
                                <i class="glyphicon glyphicon-plus"></i>
                              </button>
                            </div>
                          </div>
						    <div class="card-body" style="width:100%;min-width:300px;overflow:scroll; height:500px;margin-top:10px">
                  <table id="tblclient" class="table  table-hover" width="90%" height="100%">

                    <tbody id="tboclients">
                      <?php echo ($listclients ?? '') ; ?>
                    </tbody>
                  </table>
						    </div>
                              <h2>Commandes</h2>
              <div class="input-group">
                            <input type="text" class="form-control" style="width:225px;" id="edtFind" onkeyup="EnterearchTable(this.value, event)" placeholder="Rechercher pour ce client">
                            <div class="input-group-btn">
                              <button class="btn btn-info" onclick="searchInTable()" style="height:35px" type="button">
                                <i class="glyphicon glyphicon-search"></i>
                              </button>
                              <button class="btn btn-danger" onclick="ClearsearchTableCom()" style="height:35px" type="button">
                                <i class="glyphicon glyphicon-remove"></i>
                              </button>
                            </div>
                          </div>
              	<div class="card-body" style="width:100%;min-width:300px;max-height:500px;margin-top:10px;">
                  <table id="tblcommandes" class="table  table-hover" width="90%">
                    <tbody id="tbocommandes">
                      <?php echo ($listcommandes ?? '') ; ?>
                    </tbody>
                  </table>
						    </div>
				    </section>
			    </div>
		    </section>
	    </div>
    </div>

		<div class="row" style="position:absolute;left: 350px;top: 90px;width:420PX; border-bottom:solid; ">

				<div class="col-md-7">
						<ul class="nav nav-pills">
							<li id="liPageCommande"  >
								<a href="javascript:ShowCommandeClient('divcommande')"  <?php echo (($typetoshow == 1)? 'class="active"' : ''); ?> >COMMANDES</a>
							</li>
							<li id="liPageClient">
								<a href="javascript:ShowCommandeClient('divclient')"  <?php echo (($typetoshow == 2)? 'class="active"' : ''); ?>>CLIENTS</a>
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
                        <th colspan="7" style="text-align:center;"><div id="myNavbar" >
        <ul class="nav navbar-nav">
          <!--<li>
            <button type="button" onclick="AddTransBo()" id="btnAddTransBo" class="btn btn-warning" style="height:50px;width:150px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er une commande TRANS BO">
                  TRANS BO
            </button>
          </li>-->
          <li>
            <label >&nbsp;&nbsp;&nbsp;&nbsp;</label>
          </li>
          <li>
            <button type="button" onclick="AddNew()" id="btnAddCom" disabled class="btn btn-success" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er une nouvelle commande">
                  <i class="glyphicon glyphicon-plus"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="EditCom()" id="btnEditCom" class="btn btn-default" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Edition" data-content="Cliquer pour &eacute;diter la commande">
                  <i class="glyphicon glyphicon-edit"></i>
            </button>
          </li>
          <li style="display: none;">
            <button type="button" onclick="DeleteCom()" id="btnDeleteCom" class="btn btn-danger" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Supprimer" data-content="Cliquer pour supprimer la commande">
                  <i class="glyphicon glyphicon-trash"></i>
            </button>
          </li>
          <li>
            <button type="button" class="btn btn-success"  onclick="PostTransBo()" id="btnPostCom" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Enregistrer" data-content="Cliquer pour enregistrer la commande">
                 <i class="glyphicon glyphicon-ok"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="CancelComTransbo()" id="btnCancelCom" class="btn btn-warning"  style="height:50px;width:50px;font-size:larger;margin-left:2px;"  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Annuler" data-content="Cliquer pour annuler les modifications de la commande">
                  <i class="glyphicon glyphicon-remove"></i>
            </button>
          </li>
          <li style="display: none;">
            <button type="button" onclick="" class="btn btn-default" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Signature" data-content="Cliquer pour afficher la signature de la commande.">
                  <i class="glyphicon glyphicon-pencil"></i>
            </button>
          </li>
          <li style="display: none;">
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
            <button type="button" onclick="CancelComTransbo()"  class="btn btn-danger" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter" data-content="Cliquer pour fermer la commande et retourner aux RollOff">
                  <i class="glyphicon glyphicon-log-out"></i>
            </button>
          </li>
          <li>
            <button type="button" style="height:50px;width:250px;font-size:larger;margin-left: 25px;color:red;font-weight:bold" class="btn btn-default"  onclick="AfficheBoites()">
                  <i class="far fa-share-square"></i> Afficher les bo&icirc;tes
            </button>
          </li>
          <li>
                          
                            <select class="form-control"  id="selActifRH" style="width:250px;" >
                                <?php echo ($employes ?? '') ; ?>
                            </select>
          </li>

        </ul>

      </div></th>

                      </tr>
                      </thead>
<tbody id="tbodyCom">
                      <tr>
                        <td colspan="7" align="left"><label id="nomclient" style="padding-bottom:2px;font-size:larger;font-weight:bold;width:100%;text-align:left;">CLIENT:&nbsp;&nbsp; <span class="titre"> <?php echo ($actifnomclient ?? '') ; ?></span></label>
                          <input name="idclient"   id="idclient" type="hidden" value="<?php echo ($actifclient ?? '') ; ?>" />
                          <input id="actifnomclient" type="hidden" value="<?php echo ($actifnomclient ?? '') ; ?>" />
													<input id="actifcodeclient" type="hidden" value="<?php echo ($actifcodeclient ?? '') ; ?>" />
													<input id="actifcommande" type="hidden" value="0" />
													<input id="idtransbo" type="hidden" value="<?php echo ($idtransbo ?? '') ; ?>" />													
                        </td>

                      </tr>
                      <tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%"># Demande</label>
                        <input class="form-control" name="id"   id="id" type="text" value="" style="width:130px;color:darkred;font-weight:bold;font-size:large; " readonly /></td>
                        <td ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Code</label>
                          <select name="Code" class="form-control" id="Code" style="width:90px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($Code == 5)? 'selected' : '' ) ; ?> value="5">5</option>
                                      <option <?php echo (($Code == 6)? 'selected' : '' ) ; ?> value="6">6</option>
                                      <option value="7" <?php echo ((($Code == 7) or ($Code == 0))? 'selected' : '' ) ; ?> >7</option>
                                    </select></div></td>
                      <td colspan="5"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                      </tr>
                      <tr>

                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Date de livraison</label>
                        <input class="form-control" name="date_fact"  id="date_fact" type="date" value="<?php echo ($date_fact ?? '') ; ?>" style="width:160px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Date de retour</label>
                        <input class="form-control" name="date_retour"  id="date_retour" type="date" value="<?php echo ($date_retour ?? '') ; ?>" style="width:160px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="2"> <div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de demande</label>
                                <select class="form-control" onchange="ChangeTypeDemande(this.value)" id="TypeDemande" name="TypeDemande" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option  value="-1" selected>Choisir</option>
                                      <option <?php echo (($TypeDemande == 0)? 'selected' : '' ) ; ?>  value="0" selected>Livraison</option>
                                      <option <?php echo (($TypeDemande == 1)? 'selected' : '' ) ; ?>  value="1">Vider</option>
                                      <option <?php echo (($TypeDemande == 5)? 'selected' : '' ) ; ?>  value="5" >Vider avec retour</option>
                                      <option <?php echo (($TypeDemande == 4)? 'selected' : '' ) ; ?>  value="4" >Transfert</option>
                                      <option <?php echo (($TypeDemande == 2)? 'selected' : '' ) ; ?>  value="2">Transport</option>
                                      <option <?php echo (($TypeDemande == 3)? 'selected' : '' ) ; ?>  value="3" >Terminer</option>
                                      <option <?php echo (($TypeDemande == 6)? 'selected' : '' ) ; ?>  value="6" >TRANS BO</option>
                                      <option <?php echo (($TypeDemande == 7)? 'selected' : '' ) ; ?>  value="7" >Multiple</option>
                                  </select></div>
                        </td>
                        <td ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de contenant</label><select class="form-control" id="FormatContenant" name="FormatContenant" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">        
														<?php echo ($typecontenant ?? '') ; ?>
                                    </select></div></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Tonnage</label><input class="form-control" id="Tonnage" name="Tonnage" type="number" value="<?php echo ($Tonnage ?? '') ; ?>" style="width:120px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Bo&icirc;te en place</label><input class="form-control" name="BoiteEnPlace"  id="BoiteEnPlace" type="tel" value="<?php echo ($BoiteEnPlace ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>



                      </tr>

                      <tr>
                        
                        <td colspan="7" ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de camion</label><select class="form-control" id="TypeCamion" name="TypeCamion" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($TypeCamion == 2)? 'selected' : '' ) ; ?>  value="2">Indiff&eacute;rent</option>
                                      <option <?php echo (($TypeCamion == 0)? 'selected' : '' ) ; ?>  value="0">10 roues</option>
                                      <option <?php echo ((($TypeCamion == 1) or ($TypeCamion == ''))? 'selected' : '' ) ; ?>  value="1" >12 roues</option>
                                    </select></div>
                        </td>

                      </tr>
                        <tr>
                        <td colspan="7">
                          <table  class="table table-bordered">
                            <tr>
                            <td colspan="11" align="center"><label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de mat&eacute;riaux</label></td>
                            </tr>
                            <tr>

                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" onchange="setInfoContenant(this.id)"  <?php echo (($IsBeton == 1)? 'checked' : '' ) ; ?> id="IsBeton" name="IsBeton"  value="1" disbled >B&eacute;ton</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox"  onchange="setInfoContenant(this.id)" <?php echo (($IsBetonArme == 1)? 'checked' : '' ) ; ?> id="IsBetonArme" name="IsBetonArme"  value="1" disabled>B&eacute;ton arm&eacute;</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsAsphalte == 1)? 'checked' : '' ) ; ?> id="IsAsphalte" name="IsAsphalte" value="1">Asphalte</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsOrganique == 1)? 'checked' : '' ) ; ?> id="IsOrganique" name="IsOrganique" value="1">Organique</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsFumier == 1)? 'checked' : '' ) ; ?> id="IsFumier" name="IsFumier" value="1">Fumier</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsTerre == 1)? 'checked' : '' ) ; ?> id="IsTerre" name="IsTerre" value="1">Terre</label></td>
                            <td><div class="input-group">  
                            <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBois == 1)? 'checked' : '' ) ; ?> id="IsBois" name="IsBois" onclick="ShowHide('Recyclage')" value="1">Recyclage&nbsp;&nbsp;</label>
	                            <span class="input-group-btn" style="width: 100px;">
		                            <select  class="form-control" id="Recyclage" name="Recyclage" style="width:80px;display:<?php echo ((($Recyclage == '') or (!isset($Recyclage )))? 'none' : 'block' ) ; ?>;font-size:medium;font-weight:bold;color:darkblue; ">
			                              <option <?php echo (($Recyclage == 'Bois')? 'selected' : '' ) ; ?> value="Bois">Bois</option>
			                              <option <?php echo (($Recyclage == 'Carton')? 'selected' : '' ) ; ?> value="Carton">Carton</option>
			                              <option <?php echo (($Recyclage == 'Verre')? 'selected' : '' ) ; ?> value="Verre" >Verre</option>
		                            </select>
	                            </span></div>
                            </td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsMateriaux == 1)? 'checked' : '' ) ; ?> id="IsMateriaux" name="IsMateriaux"  type="checkbox" value="1">Mat&eacute;riaux secs</label>
                            <td> <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsDechets == 1)? 'checked' : '' ) ; ?> id="IsDechets" name="IsDechets" type="checkbox" value="1">D&eacute;chets</label></td>

                            <td>  <label class="checkbox-inline" onchange="setInfoContenant(this.id)" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsBardeau == 1)? 'checked' : '' ) ; ?> id="IsBardeau" name="IsBardeau" type="checkbox" value="1" disabled>Bardeaux</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsAmiante == 1)? 'checked' : '' ) ; ?> id="IsAmiante" name="IsAmiante" type="checkbox" value="1">Amiante</label></td>

                            </tr>
                            <tr>

                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" onchange="setInfoContenant(this.id)"  <?php echo (($IsMetal == 1)? 'checked' : '' ) ; ?> id="IsMetal" name="IsMetal"  value="1" disbled >M&eacute;tal</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox"  onchange="setInfoContenant(this.id)" <?php echo (($IsCompost == 1)? 'checked' : '' ) ; ?> id="IsCompost" name="IsCompost"  value="1" disabled>Compost</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBranches == 1)? 'checked' : '' ) ; ?> id="IsBranches" name="IsBranches" value="1">Branches</label></td>
                            <td>  <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsSapins == 1)? 'checked' : '' ) ; ?> id="IsSapins" name="IsBranches" value="1">Sapins</label></td>
                            <td>  <label class="checkbox-inline"  style="font-size:medium;font-weight:bold;"><input onchange="setInfoContenant(this.id)" <?php echo (($IsDemolition == 1)? 'checked' : '' ) ; ?> id="IsDemolition" name="IsDemolition" type="checkbox" value="1" disabled >Démolition</label></td>
                            <td colspan="7">  </td>
                            
                            </tr>
                          </table>
                        </td>


                      </tr>
                      <tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">T&eacute;l&eacute;phone</label><input class="form-control" id="phone_jour"  name="phone_jour" type="tel" value="<?php echo ($phone_jour ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Cellulaire</label><input class="form-control" id="phone_soir"  name="phone_soir" type="tel" value="<?php echo ($phone_soir ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="5"><label style="font-size:medium;font-weight:bold;width:100%">R&eacute;f&eacute;rence</label><input class="form-control" name="nom"  id="nom" type="text" value="<?php echo ($nom ?? '') ; ?>" style="width:650px;font-size:medium;font-weight:bold;color:darkblue;" /></td>

                      </tr>
                      <tr>
                        <td  colspan="3">
                          <table  class="table table-bordered">
                            <tr>
                              <th colspan="2" style="text-align:center;font-size:medium;font-weight:bold">
                                Adresse de livraison
                              </th>
                            </tr>

                            <tr>
                              <td colspan="2" class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Adresse</label>
                                <textarea class="form-control"  style="width:450px;font-size:medium;font-weight:bold;color:darkblue;" rows="5" id="adresse" name="adresse"><?php echo ($adresse ?? '') ; ?></textarea>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" class="no-line">
                                <div class="input-group">
                                <label style="font-size:medium;font-weight:bold;width:100%">V&eacute;rifier l'adresse dans Google
                                <a href="javascript:ShowGoogle(1)" style="width:35px;height:35px;" >
                                <img src="/assets/img/googlemap.png" height="35" /></a></label>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Ville</label>
                                <input  id="ville" name="ville" type="text" value="<?php echo ($ville ?? '') ; ?>" style="width:450px;font-size:medium;font-weight:bold;color:darkblue;" />
                              </td>
                            </tr>
                            <tr>
                              <td class="no-line"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Province</label>
                                <select class="form-control" id="prov" name="prov" style="width:140px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option value="AB">AB</option>
                                      <option value="BC">BC</option>
                                      <option value="PE" >PE</option>
                                      <option value="MB" >MB</option>
                                      <option value="NS">NS</option>
                                      <option value="ON" >ON</option>
                                      <option value="QC" selected >QC</option>
                                      <option value="SK">SK</option>
                                      <option value="NL" >NL</option>
                                    </select></div>
                              </td>
                              <td class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Code postal</label>  <input class="form-control" id="codepostal" name="codepostal" type="text" value="<?php echo ($codepostal ?? '') ; ?>" style="width:100px;font-size:medium;font-weight:bold;color:darkblue;" />
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td id="tdTransfert" style="display:none;"  colspan="3">
                          <table  class="table table-bordered">
                            <tr>
                              <th colspan="2" style="text-align:center;font-size:medium;font-weight:bold; color:darkred">
                                Adresse de TRANSFERT
                              </th>
                            </tr>

                            <tr>
                              <td colspan="2" class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Adresse</label>
                                <textarea class="form-control"  style="width:450px;font-size:medium;font-weight:bold;color:darkblue;" rows="5" id="adressetransfert" name="adressetransfert"><?php echo ($adressetransfert ?? '') ; ?></textarea>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" class="no-line">
                                <div class="input-group">
                                <label style="font-size:medium;font-weight:bold;width:100%">V&eacute;rifier l'adresse dans Google
                                <a href="javascript:ShowGoogle(2)" style="width:35px;height:35px;" >
                                <img src="/assets/img/googlemap.png" height="35" /></a></label>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Ville</label>
                                <input  id="villetransfert" name="villetransfert" type="text" value="<?php echo ($villetransfert ?? '') ; ?>" style="width:450px;font-size:medium;font-weight:bold;color:darkblue;" />
                              </td>
                            </tr>
                            <tr>
                              <td class="no-line"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Province</label>
                                <select class="form-control" id="provtransfert" name="provtransfert" style="width:140px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option value="AB">AB</option>
                                      <option value="BC">BC</option>
                                      <option value="PE" >PE</option>
                                      <option value="MB" >MB</option>
                                      <option value="NS">NS</option>
                                      <option value="ON" >ON</option>
                                      <option value="QC" selected >QC</option>
                                      <option value="SK">SK</option>
                                      <option value="NL" >NL</option>
                                    </select></div>
                              </td>
                              <td class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Code postal</label>  <input class="form-control" id="codepostaltransfert" name="codepostaltransfert" type="text" value="<?php echo ($codepostaltransfert ?? '') ; ?>" style="width:100px;font-size:medium;font-weight:bold;color:darkblue;" />
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td  colspan="4">
                          <table  class="table table-bordered">
                            <tr>
                              <th colspan="5" style="text-align:center;font-size:medium;font-weight:bold">
                                Prix et Paiement
                              </th>
                            </tr>
                            <tr>
                              <td rowspan="8"  class="no-line"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de paiement</label>

                                  <label><input type="radio" <?php echo (($TypePaiement == 0)? 'checked' : '' ) ; ?> value="0" id="TypePaiement_0" name="TypePaiement" >&nbsp;&nbsp;MasterCard</label><br /><br />


                                  <label><input type="radio" <?php echo ((($TypePaiement == 1) or ($TypePaiement == ''))? 'checked' : '' ) ; ?> value="1" id="TypePaiement_1" name="TypePaiement">&nbsp;&nbsp;Visa</label><br /><br />

                                  <label><input type="radio" <?php echo (($TypePaiement == 2)? 'checked' : '' ) ; ?> value="2" id="TypePaiement_2" name="TypePaiement" >&nbsp;&nbsp;Paypal</label><br /><br />

                                  <label><input type="radio" <?php echo (($TypePaiement == 3)? 'checked' : '' ) ; ?> value="3" id="TypePaiement_3" name="TypePaiement" >&nbsp;&nbsp;Ch&egrave;que</label><br /><br />

                                  <label><input type="radio" <?php echo (($TypePaiement == 4)? 'checked' : '' ) ; ?> value="4" id="TypePaiement_4" name="TypePaiement">&nbsp;&nbsp;Interac</label><br /><br />

                                  <label><input type="radio" <?php echo (($TypePaiement == 5)? 'checked' : '' ) ; ?> value="5"" id="TypePaiement_5" name="TypePaiement" >&nbsp;&nbsp;Comptant</label><br /><br />
                                  <label><input type="radio" <?php echo (($TypePaiement == 6)? 'checked' : '' ) ; ?> value="6" id="TypePaiement_6" name="TypePaiement" >&nbsp;&nbsp;Autre</label><br /><br /> </div>
                              </td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label style="text-align:left;font-size:medium;font-weight:bold"></label>&nbsp;&nbsp;</td>
                              <td class="no-line" >

                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >Prix :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="Prix" name="Prix" onblur="CalculPrix()" type="text" value="<?php echo ($Prix ?? '') ; ?>" style="width:130px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                &nbsp;&nbsp;<i class='fas fa-dollar-sign' style='font-size:24px;color:darkblue;margin-top:3px;'></i>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label style="text-align:center;font-size:medium;font-weight:bold"></label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" ></label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label style="text-align:center;font-size:medium;font-weight:bold"></label>&nbsp;&nbsp;</td>
                              <td class="no-line" >&nbsp;&nbsp;
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label style="text-align:left;font-size:medium;font-weight:bold;color:red">Total :</label>&nbsp;&nbsp;</td>
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="Total" name="Total" type="text" value="<?php echo ($Total ?? '') ; ?>" style="width:130px;font-size:medium;font-weight:bold;color:darkred;text-align:right;" />
                                &nbsp;&nbsp;<i class='fas fa-dollar-sign' style='font-size:24px;color:darkblue;margin-top:3px;'></i>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td colspan="2" class="no-line"></td>
                              <td class="no-line" >

                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>

                       <td colspan="4"><label style="font-size:medium;font-weight:bold;width:100%">Courriel</label><input class="form-control" name="courriel"  id="courriel" type="email" value="<?php echo ($courriel ?? '') ; ?>" style="width:100%;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="3"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">D&eacute;lai de la commande</label>
                          <label class="checkbox-inline"style="text-align:left;font-size:large;color:red;font-weight:bolder;" ><input type="radio" name="Delai" id="Delai_1" <?php echo (($Delai == 1)? 'checked' : '' ) ; ?>  value="1">&nbsp;&nbsp;Urgent</label>&nbsp;&nbsp;&nbsp;&nbsp;
                          <label class="checkbox-inline"style="text-align:left;font-size:large;color:blue;font-weight:bolder;" ><input type="radio" name="Delai" id="Delai_2" <?php echo (($Delai == 2)? 'checked' : '' ) ; ?>  value="2">&nbsp;&nbsp;Voir la note</label>&nbsp;&nbsp;&nbsp;&nbsp;
                          <label class="checkbox-inline"style="text-align:left;font-size:large;color:green;font-weight:bolder;" ><input type="radio" name="Delai" id="Delai_3" <?php echo ((($Delai == 3) or ($Delai == ''))? 'checked' : '' ) ; ?>   value="3">&nbsp;&nbsp;Courant </label>&nbsp;&nbsp; </div>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4" ><label style="font-size:medium;font-weight:bold;width:100%">Note</label><textarea class="form-control"  style="width:100%;font-size:medium;font-weight:bold;color:darkblue;" rows="5" id="notes" name="notes"><?php echo ($notes ?? '') ; ?></textarea></td>
                        <td colspan="3" ><label style="font-size:medium;font-weight:bold;width:100%">Note bureau</label><textarea class="form-control"  style="width:100%;font-size:medium;font-weight:bold;color:darkblue;" rows="5" id="note_bureau" name="note_bureau"><?php echo ($note_bureau ?? '') ; ?></textarea></td>
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

    <div id="divclient" class="row" style="display:none">
      <div class="col-lg-10  box box-info" style="position:absolute;left: 350px;top: 130px;min-height:800px;width:900px;">
		    <form id="frmClient">
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
            <button type="button" onclick="AddNewClient()" id="btnAddClient" class="btn btn-success" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour cr&eacute;er un nouveau client">
                  <i class="glyphicon glyphicon-plus"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="EditClient()" id="btnEditClient" class="btn btn-default" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Edition" data-content="Cliquer pour &eacute;diter le client">
                  <i class="glyphicon glyphicon-edit"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="DeleteClient()" id="btnDeleteClient" class="btn btn-danger" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Supprimer" data-content="Cliquer pour supprimer le client">
                  <i class="glyphicon glyphicon-trash"></i>
            </button>
          </li>
          <li>
            <button type="button" class="btn btn-success" disabled  onclick="PostClient()" id="btnPostClient" style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Enregistrer" data-content="Cliquer pour enregistrer le client">
                 <i class="glyphicon glyphicon-ok"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="CancelClient()" id="btnCancelClient" class="btn btn-warning" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;"  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Annuler" data-content="Cliquer pour annuler les modifications du client">
                  <i class="glyphicon glyphicon-remove"></i>
            </button>
          </li>
        </ul>

      </div></th>

                      </tr>
                      </thead>
                      <tbody id="tbodyClient">
                      <tr>
                        <td colspan="7" align="left"><label id="client" style="padding-bottom:2px;font-size:larger;font-weight:bold;width:100%;text-align:left;">CLIENT:&nbsp;&nbsp; <span class="titre"> <?php echo ($actifnomclient ?? '') ; ?></span></label>
                        <input type="hidden" id="clientid" name="clientid" value="<?php echo ($idclient ?? '') ; ?>" />
                        </td>

                      </tr>
                      <tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%"># Client</label>
                        <input class="form-control" name="noclient"   id="noclient" type="text" value="<?php echo ($noclient ?? '') ; ?>" style="width:130px;color:darkred;font-weight:bold;font-size:large; " /></td>
                        <td ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Code</label>
                          <select name="codeclient" class="form-control" id="codeclient" style="width:90px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($actifcodeclient == 5)? 'selected' : '' ) ; ?> value="5">5</option>
                                      <option <?php echo (($actifcodeclient == 6)? 'selected' : '' ) ; ?> value="6">6</option>
                                      <option value="7" <?php echo ((($actifcodeclient == 7) or ($codeclient == 0))? 'selected' : '' ) ; ?> >7</option>
                                    </select></div></td>
												<td ><label style="font-size:medium;font-weight:bold;width:100%">Compagnie</label>
                        <input class="form-control" name="cie"   id="cie" type="text" value="<?php echo ($cie ?? '') ; ?>" style="width:380px;color:darkred;font-weight:bold;font-size:large; "  /></td>

                      </tr>
											<tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">T&eacute;l&eacute;phone</label><input class="form-control" id="phoneclient"  name="phoneclient" type="tel" value="<?php echo ($phoneclient ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="2"><label style="font-size:medium;font-weight:bold;width:100%">Cellulaire</label><input class="form-control" id="faxclient"  name="faxclient" type="tel" value="<?php echo ($faxclient ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>


                      </tr>
                      <tr>
                        <td  colspan="3">
                          <table  class="table table-bordered">
                            <tr>
                              <th colspan="2" style="text-align:center;font-size:medium;font-weight:bold">
                                Adresse
                              </th>
                            </tr>

                            <tr>
                              <td colspan="2" class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Adresse</label>
                                <textarea class="form-control"  style="width:450px;font-size:medium;font-weight:bold;color:darkblue;" rows="5" id="adresseclient" name="adresseclient"><?php echo ($adresseclient ?? '') ; ?></textarea>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" class="no-line">
                                <div class="input-group">
                                <label style="font-size:medium;font-weight:bold;width:100%">V&eacute;rifier l'adresse dans Google
                                <a href="javascript:ShowGoogle('client')" style="width:35px;height:35px;" >
                                <img src="/assets/img/googlemap.png" height="35" /></a></label>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Ville</label>
                                <input  id="villeclient" name="villeclient" type="text" value="<?php echo ($villeclient ?? '') ; ?>" style="width:450px;font-size:medium;font-weight:bold;color:darkblue;" />
                              </td>
                            </tr>
                            <tr>
                              <td class="no-line"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Province</label>
                                <select class="form-control" id="provclient" name="provclient" style="width:140px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option value="AB">AB</option>
                                      <option value="BC">BC</option>
                                      <option value="PE" >PE</option>
                                      <option value="MB" >MB</option>
                                      <option value="NS">NS</option>
                                      <option value="ON" >ON</option>
                                      <option value="QC" selected >QC</option>
                                      <option value="SK">SK</option>
                                      <option value="NL" >NL</option>
                                    </select></div>
                              </td>
                              <td class="no-line">
                                <label style="font-size:medium;font-weight:bold;width:100%">Code postal</label>
																	<input class="form-control" id="codepostalclient" name="codepostalclient" type="text" value="<?php echo ($codepostalclient ?? '') ; ?>" style="width:100px;font-size:medium;font-weight:bold;color:darkblue;" />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>

                       <td colspan="4"><label style="font-size:medium;font-weight:bold;width:100%">Courriel</label><input class="form-control" name="emailclient"  id="emailclient" type="email" value="<?php echo ($emailclient ?? '') ; ?>" style="width:100%;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                      </tr>
                      <tr>
                        <td colspan="4" ><label style="font-size:medium;font-weight:bold;width:100%">Note</label><textarea class="form-control"  style="width:100%;font-size:medium;font-weight:bold;color:darkblue;" rows="5" id="notesclient" name="notesclient"><?php echo ($notesclient ?? '') ; ?></textarea></td>
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

  <div class="modal fade" id="modalmat" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Camion Avec Mat&eacute;riel&nbsp;!</h3>

			</div>
            <div class="modal-body" style="min-height:600px;">
              <form>

                    <div class="form-group">

                      <div div="divMat" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">

                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; "># Camions  </label> <br />
                            <select class="form-control" name="camionmat" id="camionmat" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%;">
                          <label style="font-weight: bold; ">Date hors service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtestartmat" />
                        </div>
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                           <label style="font-weight: bold; ">Date retour pr&eacute;vu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteendmat" />
                        </div>
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Type de mat&eacute;riel</label> <br />
                            <select class="form-control" name="typemat" id="typemat" required>
                                <option value="">Choisir</option>
                                <?php echo ($typededechet ?? '') ; ?>
                            </select>
                        </div>
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                           <label style="font-weight: bold; ">Quantit&eacute;</label> <br />
                           <input type="number" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" placeholder="Entrer le tonnage" value="" id="qtemat" />
                        </div>
                        <div class="col-sm-7 col-xs-12"  style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="7" cols="50" id="notemat" >

                            </textarea>
                        </div>
                      </div>
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="PostSuivisCamMat()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalhs" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Camion Hors-service&nbsp;!</h3>

			</div>
            <div class="modal-body" style="min-height:500px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">

                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; "># Camions  </label> <br />
                            <select class="form-control" name="camionhs" id="camionhs" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>
                           <br /> <br />


                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date hors service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtestarths" />
                        </div>
                           <br /> <br />


                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour pr&eacute;vu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteendhs" />
                        </div>

                           <br /> <br />

                        <div class="col-sm-7 col-xs-12" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="7" cols="50" id="notehs" >

                            </textarea>
                        </div>
                      </div>
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="PostSuivisCamHS()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="modaltypedemande" tabindex="-1" role="dialog"  data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true" style="min-height:500px;min-width:950px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Selectionner la commande qui doit &ecirc;tre Termin&eacute;e ou Vid&eacute;e</h3>

			</div>
            <div class="modal-body" style="min-height:500px;min-width:850px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-bottom:10px;padding-top:10px;margin-top:10px;">

                        <div id="divlistdem135" class="col-sm-7 col-xs-12">
                          <?php echo ($listdem135 ?? '') ; ?>

                        </div>
                           <br /> <br />


                      </div>
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="CreateNewCom()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">S&eacute;lectionner</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
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
<script src="/assets/js/nika.js"></script>
<script>
  selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
  var HaveCom = "<?php echo ($havecom ?? '') ?>";
  var optTPS = "<?php echo ($optTPS ?? '') ?>";
  var optTVQ = "<?php echo ($optTVQ ?? '') ?>";
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
  function PostClient() {
    var formval = $("#frmClient").serialize();
  $("#btnPostClient").popover('hide');
	  var urlName = "/bureau/postclient?" + formval;
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
		if (msg != "") {
        GetClients(msg);
        AjusteButtonClient('0');
      }
      else {
          AjusteButtonClient('0');
      }
    });
  }
  function PrintCommande() {
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
                "sNext": "<span><b><i class='fa fa-angle-right' style='font-size:28px;'></i></b>&nbsp;</span>",
                "sLast": "<span><b><i class='fa fa-angle-double-right' style='font-size:28px;'></i></b>&nbsp;</span>"
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
function searchInTable() {
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
function EnterearchTable(value, e) {

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
function ClearsearchTableCom() {
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
  alert('active');
    HaveCom = objData.havecom;
    $("#Recyclage").hide();
    $("#actifnomclient").val(objData.actifnomclient);
    $("#nomclient").html('CLIENT:&nbsp;&nbsp; <span class="titre">' + $("#actifnomclient").val() + '</span>');
    $("#id").val(objData.id);
    $("#actifcommande").val(((objData.id == '') ? '0' : objData.id));
    $("#idclient").val(objData.idclient);
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
function SetActiveClient(objData) {
    //----------------------------------------------------------
    $("#actifnomclient").val(objData.actifnomclient);
    $("#client").html('CLIENT:&nbsp;&nbsp; <span class="titre">' + $("#cie").val() + '</span>');
    $("#noclient").val(objData.noclient);
    $("#codeclient").val(objData.codeclient);
  $("#idclient").val(objData.idclient);
  $("clientid").val(objData.idclient);
    $("#cie").val(objData.cie);
    $('#td_' + $("#idclient").val()).removeClass("client");
    $('#td_' + $("#idclient").val()).addClass("Actifclient");
    $('#tdcom_' + objData.id).removeClass("commande");
    $('#tdcom_' + objData.id).addClass("Actifcommande");
    $("#adresseclient").val(objData.adresseclient);
    $("#villeclient").val(objData.villeclient);
    $("#provclient").val(objData.provclient).change();
    $("#codepostalclient").val(objData.codepostalclient);
    $("#phoneclient").val(objData.phoneclient);
    $("#faxclient").val(objData.faxclient);
    $("#emailclient").val(objData.emailclient);
    $("#notesclient").html(objData.notesclient);
    if (objData.IsDelete === "1") {
        $("#IsDelete").prop('checked', true);
    }
    else {
        $("#IsDelete").prop('checked', false);
    }
    $("#Code").val(objData.Code);
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
    $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', false);
    $("#btnPostCom").attr("disabled", false);
    $("#btnAddCom").attr("disabled", false);
    //---------------------------------------------
    $("#btnEditCom").attr("disabled", true);
    $("#btnDeleteCom").attr("disabled", true)
    if (Flag == '0') {
        $("#btnCancelCom").attr("disabled", true);
        $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', true);
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
function AjusteButtonClient(Flag) {
    $("#btnCancelClient").attr("disabled", false);
    $('#tbodyClient').find('a, input, select, button, textarea').prop('disabled', false);
    $("#btnPostClient").attr("disabled", false);
    $("#btnAddClient").attr("disabled", false);
    //---------------------------------------------
    $("#btnEditClient").attr("disabled", true);
    $("#btnDeleteClient").attr("disabled", true)
    if (Flag == '0') {
        $("#btnCancelClient").attr("disabled", true);
        $('#tbodyClient').find('a, input, select, button, textarea').prop('disabled', true);
        $("#btnPostClient").attr("disabled", true);
        $("#btnEditClient").attr("disabled", false);
        $("#btnDeleteClient").attr("disabled", false);
    }
    else {
        $("#btnAddClient").attr("disabled", true);
    }
}
function AddNew() {
    $("#Code").val($("#actifcodeclient").val());
    $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
    $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
    $("#actifcommande").val(0);
    $("#Delai_3").prop('checked', true);
    $("#TypeCamion").val(1);
    DateActive = "<?php echo date('Y-m-d'); ?>";
    $("#date_fact").val(DateActive);
    if ($("#idclient").val() == '12958') {
      $("#TypeDemande").val(6);
    }
    else {
		  $("#TypeDemande").val(-1);
    }
		$("#tdTransfert").hide();
    $("#prov").val('QC');
    var TypePaiement = "#TypePaiement_" + '3';
    $(TypePaiement).prop('checked', true);
    //------------------------------------------------------
    $("#Recyclage").hide();
    $("#id").val('');
    $("#nom").val('');
    $("#prenom").val('');
    $("#adresse").val('');
    $("#ville").val('');
	$("#codepostal").val('');
		$("#adressetransfert").val('');
    $("#villetransfert").val('');
		$("#codepostaltransfert").val('');
		$("#provtransfert").val('QC');
    $("#phone_jour").val('');
    $("#phone_soir").val('');
    $("#courriel").val('');
    $("#FormatContenant").val('');
    DateActive = "<?php echo ($dteNewRetour ?? ''); ?>";
    $("#date_retour").val(DateActive);
    $("#notes").val('');
    $("#note_bureau").val('');
    $("#Tonnage").val('');
  $("#Prix").val(<?php echo ($Prix ?? ''); ?>);
    $("#BoiteEnPlace").val(<?php echo ($BoiteEnPlace ?? ''); ?>);
    $("#Redevance").val('');
    $("#TPS").val('');
    $("#TVQ").val('');
    $("#Total").val(<?php echo ($Total ?? ''); ?>);
    $("#btnCancelCom").attr("disabled", false);
    $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', false);
    $("#btnPostCom").attr("disabled", false);
    $("#btnAddCom").attr("disabled", false);
    $("#MontPaye").val('');
    $("#IsBeton").prop('checked', false);
    $("#IsBeton").prop('checked', false);
    $("#IsFumier").prop('checked', false);
    $("#IsBranches").prop('checked', false);
    $("#IsAsphalte").prop('checked', false);
    $("#IsTerre").prop('checked', true);
    $("#IsTerre").prop('checked', false);
    $("#IsDechets").prop('checked', true);
    $("#IsDechets").prop('checked', <?php echo (($IsDechets)? 'true' : 'false'); ?> );
    $("#IsBois").prop('checked', false);
    $("#IsMateriaux").prop('checked', false);
    $("#IsBardeau").prop('checked', false);
    $("#IsPaid").prop('checked', false);
    $("#BoiteEnPlace").val('');
    $("#Recyclage").val('');
    $("#btnCancelCom").attr("disabled", false);
    $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', false);
    $("#btnPostCom").attr("disabled", false);
    $("#btnAddCom").attr("disabled", false);
    //------------------------------------------------------
  //$("#btnAddCom").popover('hide');
   // AjusteButton('1');
}
function EditCom() {
    AjusteButton('1');
    $("#btnEditCom").popover('hide');
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

//------------------------------------------------------------------------------
  function AddTransBo() {
    var urlName = "/bureau/commandesliste/12958";
    $.ajax({
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg != "") {
            var objData = JSON.parse(msg);
            $("#tboclients").html(objData.listclients);
            $('#td_' + objData.idclient).removeClass("Actifclient");
          
          AddNewClient();
          SetActiveClient(objData);
          AddNew();
          $("#TypeDemande").val('6');
          $("#tbocommandes").html(objData.listcommandes);
          //AjusteButton('0');
					AjusteButtonClient('0');
        }
        else {
            alert('Aucun résultat pour votre recherche');
            AjusteButton('0');
						AjusteButtonClient('0');
        }
    });
  }
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
function CalculPrix() {
    //--------------------------------------
    var TPS = 0;
    var TVQ = 0;
    var total = 0;

    if ($.isNumeric($("#Prix").val())) {
        $("#Total").val(parseFloat($("#Prix").val()).toFixed(2));
    }

}
function ShowCommandeClient(id) {
    if (id == 'divcommande') {
        $('#liPageClient').removeClass("active");
        $('#liPageCommande').addClass("active");
        $('#divclient').hide();
        $('#divcommande').show();
    }
    else {
        $('#liPageCommande').removeClass("active");
        $('#liPageClient').addClass("active");
        $('#divcommande').hide();
        $('#divclient').show();
    }

}
//---------------------------------------------------
function AddNewClient() {
    $("#Code").val('6');
    //----------------------------------------------------------
    $("#actifnomclient").val('');
    $("#client").html('NOUVEAU CLIENT');
    $("#noclient").val('');
    $("#codeclient").val('');
    $("#clientid").val(0);
    $("#cie").val('');
    //$('#td_' + $("#idclient").val()).removeClass("client");
    //$('#td_' + $("#idclient").val()).addClass("Actifclient");
    $("#adresseclient").val('');
    $("#villeclient").val('');
    $("#provclient").val('QC').change();
    $("#codepostalclient").val('');
    $("#phoneclient").val('');
    $("#faxclient").val('');
    $("#emailclient").val('');
    $("#notesclient").val('');
    //------------------------------------------------------
    $("#btnAddClient").popover('hide');
    AjusteButtonClient('1');
}
function EditClient() {
    AjusteButtonClient('1');
    $("#btnEditClient").popover('hide');
}

function DeleteClient() {
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
                        AjusteButtonClient('0');
                    }
                });
            } else {
                //alert('Nope.');
            }
        }
    });
    $("#btnDeleteClient").popover('hide');
}
function CancelClient() {
    if ($("#id").val() == 0) {
        GetClients($("#idclient").val())
    }
    else {
      GetClients($("#idclient").val())
        GetCommande($("#id").val());
    }
    AjusteButtonClient('0');
    $("#btnCancelClient").popover('hide');
}
function AfficheBoitesForCom() {
	var urlName = '/rolloff/edition_boites?typeboite=tous&champ=&valtofind=&isdispo=all';
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
  }


//---------------------------------------------------
  var arrNumAccept = [48,49,50,51,52,53,54,55,56,57,188,189,190];
  AjusteButton('0');
  AjusteButtonClient('0')
  $('#td_' + $("#idclient").val()).removeClass("client");
  $('#td_' + $("#idclient").val()).addClass("Actifclient");
  $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
  $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
  if (typetoshow == 1) {
      ShowCommandeClient('divcommande');
  }
  else {
      ShowCommandeClient('divclient');
  }
  if ($("#idclient").val() == '12958') {
    var typeCont = "<?php echo ($typecont ?? ''); ?>";
    //AddNew();
    $("#FormatContenant").val(typeCont);
        $("#btnCancelCom").attr("disabled", false);
    $('#tbodyCom').find('a, input, select, button, textarea').prop('disabled', false);

  }
</script>
</body>
</html>



















