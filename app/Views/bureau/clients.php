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
                              <button class="btn btn-danger" onclick="ClearsearchTableCom()" style="height:35px" type="button"  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Réinitialiser" data-content="Cliquer pour réinitialiser la recherche">
                                <i class="glyphicon glyphicon-remove"></i>
                              </button>
                              <button class="btn btn-success" onclick="AddClient()" style="height:35px" type="button" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour créer un nouveau client">
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
				    </section>
			    </div>
		    </section>
	    </div>
    </div>

    <div class="row" >
      <div class="col-lg-10  box box-info" style="position:absolute;left: 350px;top: 130px;min-height:800px;width:1500px;">
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
            <button type="button" onclick="AddNew()" id="btnAddCom" class="btn btn-success" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter" data-content="Cliquer pour créer une nouvelle commande">
                  <i class="glyphicon glyphicon-plus"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="EditCom()" id="btnEditCom" class="btn btn-default" disabled style="height:50px;width:50px;font-size:larger;margin-left:2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Edition" data-content="Cliquer pour éditer la commande">
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
            <button type="button" onclick="window.print()" disabled class="btn btn-primary" style="height:50px;width:50px;font-size:larger;margin-left: 2px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Immprimer" data-content="Cliquer pour imprimer la commande">
                  <i class="glyphicon glyphicon-print"></i> 
            </button>
          </li>
          <li>
            <button type="button" style="height:50px;width:250px;font-size:larger;margin-left: 25px;color:red;font-weight:bold" class="btn btn-default"  onclick="AfficheBoites()">
                  <i class="far fa-share-square"></i> Afficher les boîtes
            </button>
          </li>
        </ul>

      </div></th>

                      </tr>
                      </thead>
                      <tbody id="tbodyCom">
                      <tr>
                        <td colspan="3" align="left"><label id="nomclient" style="padding-bottom:2px;font-size:larger;font-weight:bold;width:100%;text-align:left;">CLIENT:&nbsp;&nbsp; <span class="titre"> <?php echo ($actifnomclient ?? '') ; ?></span></label>
                          <input name="idclient"   id="idclient" type="hidden" value="<?php echo ($actifclient ?? '') ; ?>" />  
                        </td>
                      
                      </tr>
                      <tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Compagnie</label>
                        <input class="form-control" name="nom"   id="nom" type="text" value="<?php echo ($nom ?? '') ; ?>" style="width:400px;color:darkred;font-weight:bold;font-size:large; "  />
                        </td>
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
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de demande</label> <select class="form-control" id="TypeDemande" name="TypeDemande" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($TypeDemande == 0)? 'selected' : '' ) ; ?>  value="0" selected>Livraison</option>
                                      <option <?php echo (($TypeDemande == 1)? 'selected' : '' ) ; ?>  value="1">Vider</option>
                                      <option <?php echo (($TypeDemande == 5)? 'selected' : '' ) ; ?>  value="5" >Vider avec retour</option>
                                      <option <?php echo (($TypeDemande == 4)? 'selected' : '' ) ; ?>  value="4" >Transfert</option>
                                      <option <?php echo (($TypeDemande == 2)? 'selected' : '' ) ; ?>  value="2">Transport</option>
                                      <option <?php echo (($TypeDemande == 3)? 'selected' : '' ) ; ?>  value="3" >Terminer</option>
                                      <option <?php echo (($TypeDemande == 7)? 'selected' : '' ) ; ?>  value="7" >Multiple</option>
                                    </select></div>
                        </td>                            
                        <td ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de contenant</label><select class="form-control" id="FormatContenant" name="FormatContenant" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <?php echo ($typecontenant ?? '') ; ?>
                                    </select></div></td>                        
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Tonnage</label><input class="form-control" id="Tonnage" name="Tonnage" type="number" value="<?php echo ($Tonnage ?? '') ; ?>" style="width:120px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Boîte en place</label><input class="form-control" name="BoiteEnPlace"  id="BoiteEnPlace" type="tel" value="<?php echo ($BoiteEnPlace ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>

                        
                       
                      </tr>

                      <tr>
                        <td colspan="5"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de matériaux</label>   
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBeton == 1)? 'checked' : '' ) ; ?> id="IsBeton" name="IsBeton"  value="1">Béton</label>
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsAsphalte == 1)? 'checked' : '' ) ; ?> id="IsAsphalte" name="IsAsphalte" value="1">Asphalte</label>
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBranches == 1)? 'checked' : '' ) ; ?> id="IsBranches" name="IsBranches" value="1">Organique</label>
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsFumier == 1)? 'checked' : '' ) ; ?> id="IsFumier" name="IsFumier" value="1">Fumier</label>
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsTerre == 1)? 'checked' : '' ) ; ?> id="IsFumier" name="IsFumier" value="1">Terre</label>
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBois == 1)? 'checked' : '' ) ; ?> id="IsBois" name="IsBois" onclick="ShowHide('Recyclage')" value="1">Recyclage</label>
                          <select  class="form-control" id="Recyclage" name="Recyclage" style="width:200px;display:<?php echo ((($Recyclage == '') or (!isset($Recyclage )))? 'none' : 'block' ) ; ?>;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($Recyclage == 'Bois')? 'selected' : '' ) ; ?> value="Bois">Bois</option>
                                      <option <?php echo (($Recyclage == 'Carton')? 'selected' : '' ) ; ?> value="Carton">Carton</option>
                                      <option <?php echo (($Recyclage == 'Verre')? 'selected' : '' ) ; ?> value="Verre" >Verre</option>
                                    </select>      
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsMateriaux == 1)? 'checked' : '' ) ; ?> id="IsMateriaux" name="IsMateriaux"  type="checkbox" value="1">Matériaux secs</label>
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsDechets == 1)? 'checked' : '' ) ; ?> id="IsDechets" name="IsDechets" type="checkbox" value="1">Déchets</label>
                   
                          <label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input <?php echo (($IsBardeau == 1)? 'checked' : '' ) ; ?> id="IsBardeau" name="IsBardeau" type="checkbox" value="1">Bardeaux</label></div>
                        </td>
                        <td colspan="2" ><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Type de camion</label><select class="form-control" id="TypeCamion" name="TypeCamion" style="width:200px;font-size:medium;font-weight:bold;color:darkblue; ">
                                      <option <?php echo (($TypeCamion == 2)? 'selected' : '' ) ; ?>  value="2">Indifférent</option>
                                      <option <?php echo (($TypeCamion == 0)? 'selected' : '' ) ; ?>  value="0">10 roues</option>
                                      <option <?php echo ((($TypeCamion == 1) or ($TypeCamion == ''))? 'selected' : '' ) ; ?>  value="1" >12 roues</option>
                                    </select></div>
                        </td>  
                        
                      </tr>
                      <tr>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Téléphone</label><input class="form-control" id="phone_jour"  name="phone_jour" type="tel" value="<?php echo ($phone_jour ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td ><label style="font-size:medium;font-weight:bold;width:100%">Cellulaire</label><input class="form-control" id="phone_soir"  name="phone_soir" type="tel" value="<?php echo ($phone_soir ?? '') ; ?>" style="width:150px;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="5"><label style="font-size:medium;font-weight:bold;width:100%">Référence</label><input class="form-control" name="nom"  id="nom" type="text" value="<?php echo ($nom ?? '') ; ?>" style="width:650px;font-size:medium;font-weight:bold;color:darkblue;" /></td>

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
                                <label style="font-size:medium;font-weight:bold;width:100%">Vérifier l'adresse dans Google
                                <a href="javascript:ShowGoogle()" style="width:35px;height:35px;" >
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

                                  <label><input type="radio" <?php echo (($TypePaiement == 3)? 'checked' : '' ) ; ?> value="3" id="TypePaiement_3" name="TypePaiement" >&nbsp;&nbsp;Chèque</label><br /><br />

                                  <label><input type="radio" <?php echo (($TypePaiement == 4)? 'checked' : '' ) ; ?> value="4" id="TypePaiement_4" name="TypePaiement">&nbsp;&nbsp;Interac</label><br /><br />

                                  <label><input type="radio" <?php echo (($TypePaiement == 5)? 'checked' : '' ) ; ?> value="5"" id="TypePaiement_5" name="TypePaiement" >&nbsp;&nbsp;Comptant</label><br /><br />
                                  <label><input type="radio" <?php echo (($TypePaiement == 6)? 'checked' : '' ) ; ?> value="6" id="TypePaiement_6" name="TypePaiement" >&nbsp;&nbsp;Autre</label><br /><br /> </div>
                              </td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label style="text-align:left;font-size:medium;font-weight:bold">Redevance :</label>&nbsp;&nbsp;</td>                              
                              <td class="no-line" >
                                 <div class="input-group"> 
                                   <input class="form-control" id="Redevance" name="Redevance" onblur="CalculPrix()" type="text" value="<?php echo ($Redevance ?? '') ; ?>" style="width:130px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                   &nbsp;&nbsp;<i class='fas fa-dollar-sign' style='font-size:24px;color:darkblue;margin-top:3px;'></i> <span id="spRedevance" style="color:red; display:none;">Valeur non numérique.</span>
                                </div>
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
                              <td  class="no-line"><label style="text-align:center;font-size:medium;font-weight:bold">TPS :</label>&nbsp;&nbsp;</td>                              
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="TPS" name="TPS" type="text" value="<?php echo ($TPS ?? '') ; ?>" style="width:130px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                &nbsp;&nbsp;<i class='fas fa-dollar-sign' style='font-size:24px;color:darkblue;margin-top:3px;'></i>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line">&nbsp;&nbsp;</td>
                              <td  class="no-line"><label  style="text-align:left;font-size:medium;font-weight:bold" >TVQ :</label>&nbsp;&nbsp;</td>                              
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="TVQ" name="TVQ" type="text" value="<?php echo ($TVQ ?? '') ; ?>" style="width:130px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                &nbsp;&nbsp;<i class='fas fa-dollar-sign' style='font-size:24px;color:darkblue;margin-top:3px;'></i>
                                  </div>
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
                              <td colspan="2" class="no-line"><label class="checkbox-inline" style="text-align:left;font-size:medium;font-weight:bold" ><input type="checkbox" <?php echo (($IsPaid == 1)? 'checked' : '' ) ; ?> id="IsPaid" name="IsPaid" value="">&nbsp;&nbsp;Paiement effectué</label></td>                             
                              <td class="no-line" >
                                <div class="input-group">
                                  <input class="form-control" id="MontPaye" name="MontPaye" type="text" value="<?php echo ($MontPaye ?? '') ; ?>" style="width:130px;font-size:medium;font-weight:bold;color:darkblue;text-align:right;" />
                                &nbsp;&nbsp;<i class='fas fa-dollar-sign' style='font-size:24px;color:darkblue;margin-top:3px;'></i>
                                  </div>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        
                       <td colspan="4"><label style="font-size:medium;font-weight:bold;width:100%">Courriel</label><input class="form-control" name="courriel"  id="courriel" type="email" value="<?php echo ($courriel ?? '') ; ?>" style="width:100%;font-size:medium;font-weight:bold;color:darkblue;" /></td>
                        <td colspan="3"><div style="border:solid;padding: 4px 4px 4px 4px;border-width:1px;border-color:lightgray">
                          <label style="border-bottom:solid;border-bottom-width:1px;border-bottom-color:lightgray;padding-bottom:2px;font-size:medium;font-weight:bold;width:100%">Délai de la commande</label>
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
</div>
  
<div class="modal fade" id="modalCedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitre" style="color:white;background-color:cornflowerblue">Proticlub, c'est social&nbsp;!</h3>
                
			</div>
            <div class="modal-body">
                <div class="container-fluid" >
                  <div class="form-group">
                    
                    <div class="col-sm-7 col-xs-12">
                       <label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkStart" />&nbsp; &nbsp; A débuté </label>
                    </div>
                  </div>  
                    <div class="form-group">
                         <br /> <br />
                            <label style="font-weight: bold; ">Types de produit  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="typeprod" id="typeprod" required>
                                <option value="DECHETS">Choisir</option>
                                <?php echo ($typededechet ?? '') ; ?>
                            </select>
                        </div>
                    </div>   
                           <br /><br />
                           <label style="font-weight: bold; ">Chauffeurs  </label> <br />
                       
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="chauffeur" id="chauffeur" required>
                                <option value="25">Choisir</option>
                                <?php echo ($chauffeurs ?? '') ; ?>
                            </select>
                        </div>
                        <br /><br />
                           <label style="font-weight: bold; ">Aides  </label> <br />
                       
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="aide" id="aide" required>
                                <option value="24">Choisir</option>
                                <?php echo ($aides ?? '') ; ?>
                            </select>
                        </div>
                             <br />
                             <br />
                    
                           <label style="font-weight: bold; "># Camions  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="camion" id="camion" required>
                                <option value="Cam: 1">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>                  
				</div>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="PostCedule()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>


  <div class="modal fade" id="modalmat" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Camion Avec Matériel&nbsp;!</h3>
                
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
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteendmat" />
                        </div>    
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Type de matériel</label> <br />
                            <select class="form-control" name="typemat" id="typemat" required>
                                <option value="">Choisir</option>
                                <?php echo ($typededechet ?? '') ; ?>
                            </select>
                        </div> 
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                           <label style="font-weight: bold; ">Quantité</label> <br />
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
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
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

<div class="modal fade" id="modalrh" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Absence Ressource Humaine&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:500px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Resources humaines</label> <br />
                            <select class="form-control" name="selRH" id="selRH" required>
                                <option value="">Choisir</option>
                                <?php echo ($employes ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date hors service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtestartrh" />
                        </div>    
                           <br /> <br />
                          
                        
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteendrh" />
                        </div>    
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type d'absence</label> <br />
                            <select class="form-control"  id="selRHabsence" required>
                                <option value="">Choisir</option>
                                <?php echo ($typeabsences ?? '') ; ?>
                            </select>
                        </div> 
                           <br /> <br />  
                        
                        <div class="col-sm-7 col-xs-12" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="7" cols="50" id="noterh" > 

                            </textarea>
                        </div>    
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="PostSuivisRH()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
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
  var arrNumAccept = [48,49,50,51,52,53,54,55,56,57,188,189,190];
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
  //------------------------------------------------------------------------------

  //---------------------------------------------------
  AjusteButton('0');
  $('#td_' + $("#idclient").val()).removeClass("client");
  $('#td_' + $("#idclient").val()).addClass("Actifclient");
  $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
  $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
  //---------------------------------------------------
</script>
</body>
</html>



















