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
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var ActiveDrop = "<?php echo ($ActiveDrop ?? '') ?>";
    function ConfirmeLoad(Route)
    {
        $("#td_" + Route).css("background-color","#80FF9F");
        $("#chk_" + Route).css("background-color","#80FF9F");
    }
    $(document).ready(function () {
		$('[data-toggle="popover"]').popover();  
		var getPosition = function (e, ui) {
       // var value = (ui.item.index() + 1).toString();
       // var arrData = ui.item.context.id.split('_');
	      //var idproduit = arrData[1];
       // var oldrang = arrData[2];
       // var tableid = arrData[3];
       // document.location = '<?php //echo ci3_url(); ?>/*/gestion/rang/' + idproduit + '/' + oldrang + '/' + value + '/' + tableid;*/
      };
			$("#body_1").sortable({
        beforeStop: getPosition,
        update: function(event, ui) {
          var value = (ui.item.index() + 1).toString();
          var arrData = ui.item.attr('id').split('_');
          var idgroup = arrData[1];
          var acol = arrData[3];
          var typecedule = arrData[2];
          var oldrang = arrData[4];
          updTableRang(idgroup, oldrang, value, ActiveDate, acol, typecedule);
        }
      });
      $("#body_2").sortable({
        beforeStop: getPosition,
        update: function(event, ui) {
          var value = (ui.item.index() + 1).toString();
          var arrData = ui.item.attr('id').split('_');
          var idgroup = arrData[1];
          var acol = arrData[2];
          var typecedule = arrData[3];
          var oldrang = arrData[4];
          updTableRang(idgroup, oldrang, value, ActiveDate, acol, typecedule);
        }
      });
      $("#body_3").sortable({
        beforeStop: getPosition,
        update: function(event, ui) {
          var value = (ui.item.index() + 1).toString();
          var arrData = ui.item.attr('id').split('_');
          var idgroup = arrData[1];
          var acol = arrData[2];
          var typecedule = arrData[3];
          var oldrang = arrData[4];
          updTableRang(idgroup, oldrang, value, ActiveDate, acol, typecedule);
        }
      });
    });

  </script>
</head>
<body class="hold-transition skin-blue">

  <div class="wrapper">


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

 

        <div class="row" style="position:absolute;left: 0px;top: 70px;width:100%; border-bottom:solid; ">

           <div class="col-md-7">
		            <ul class="nav nav-pills">
			            <li id="liPageRearEnd" <?php echo (($typecedule == 1)? 'class="active"' : ''); ?> >
				            <a href="javascript:ShowREAREND()">REAR END</a>
			            </li>
			            <li id="liPageRobot" <?php echo (($typecedule == 2)? 'class="active"' : ''); ?>>
				            <a href="javascript:ShowROBOT()">ROBOT</a>
			            </li>
           <li class="pull-left">
            <a  href="#" id="divShowHide"  onclick="ShowHideCreate('divVilles')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Villes" data-content="Afficher la liste des villes et des secteurs">
              <i class='fa fa-building' style='font-size:24px;'></i>&nbsp;Villes
            </a>

          </li>
          <li class="pull-left">
            <a  href="#" id="divShowHideEmp" onclick="ShowHideCreateEmp('divChauffeurs')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Emloyés" data-content="Afficher la liste des employés">
              <i class='fas fa-user-check' style='font-size:24px;'></i>&nbsp;Employés
            </a>

          </li>
          <li class="pull-left">
              <form class="form-inline" style="padding-top:10px">
               <div class="form-group">
                  <a  href="javascript:LoadCreateCedule(<?php echo ($typecedule ?? ''); ?>, '-1')" id="divShowHideEmp" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journée précédente" data-content="Affiche la cédule de la journée précédente">
                    <i class='fas fa-arrow-alt-circle-left' style='font-size:24px;'></i>&nbsp;
                  </a>
                  <input type="date"  onchange="LoadCreateCedule(<?php echo ($typecedule ?? ''); ?>, '0')" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ?>" id="dteActive" />
                </div>
                  <a  href="javascript:LoadCreateCedule(<?php echo ($typecedule ?? ''); ?>, '1')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journée suivante" data-content="Affiche la cédule de la journée suivante">
                    <i class='fas fa-arrow-alt-circle-right' style='font-size:24px;'></i>&nbsp;
                  </a>
             </form>
          </li>
          <li class="pull-left">
            <a  href="#" id="divShowCeduleJour" onclick="ShowCeduleInNewWin()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Cédule" data-content="Afficher la cédule de la journée sélectionnée">
              <i class='far fa-list-alt' style='font-size:24px;'></i>&nbsp;Cédule du jour
            </a>

          </li>
		            </ul>
	        </div>       
        </div>
          <div id="divVilles"  style="position:absolute;left: 0px;top: 130px; display:<?php echo (($ActiveDrop == 1)? 'block' : 'none'); ?>;overflow: scroll; max-height: 1000px; width: 200px">
          <div class="box box-solid" style="overflow: scroll; max-height: 400px; width: 200px">
            <div class="box-header with-border">
              <h4 class="separateur">Villes à ajouter</h4>
            </div>
            <div class="box-body">
             
                <?php
                echo ($ville ?? '');
                ?>

            </div>
            <!-- /.box-body -->
          </div>
          <div class="box box-solid" style="overflow: scroll; max-height: 400px; width: 200px">
            <div class="box-header with-border">
              <h4 class="separateur">Secteurs à ajouter</h4>
            </div>
            <div class="box-body">
             
                <?php
                echo ($SecteurDrop ?? '');
                ?>

            </div>
            <!-- /.box-body -->
          </div>
        </div>
        <div id="divChauffeurs"  style="position:absolute;left: 0px;top: 130px;display: <?php echo (($ActiveDrop == 2)? 'block' : 'none'); ?>;overflow: scroll; height: 100%; width: 200px">


          <div class="box box-solid" style="overflow: scroll; max-height: 400px; width: 200px">
            <div class="box-header with-border">
              <h4 class="separateur">Camions</h4>
            </div>
            <div class="box-body" style="padding-left:7px;">
             
                <?php
                echo ($CamionDrop ?? '');
                ?>

            </div>
            </div>
            <div class="box-header with-border">
                <ul class="nav nav-pills">
			            <li id="liChauf" class="active" >
				            <a href="javascript:ShowChauf()">Chauffeurs</a>
			            </li>
			            <li id="liAide" >
				            <a href="javascript:ShowAide()">Aides</a>
			            </li>
		            </ul>
            </div>
            <div class="box box-solid" id="divChauf" style="padding-left:7px;display:block;overflow: scroll; max-height: 400px; width: 200px">
             
                <?php
                echo ($ChauffeurDrop ?? '');
                ?>

            </div>
            <div class="box box-solid" id="divAide" style="padding-left:7px;display:none;overflow: scroll; max-height: 400px; width: 200px">
             
                <?php
                echo ($AideDrop ?? '');
                ?>

            </div>
            <!-- /.box-body -->
          

        </div>
            <div class="box box-primary">

              <div id="divCedule" class="box-body no-padding" style="position:absolute;left: <?php echo (($ActiveDrop > 0)? '210px' : '0px'); ?>;top: 130px;min-height:800px;width:100%; overflow: scroll;">
                <?php
                echo ($today ?? '');
                ?>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /. box -->
          </div>
          

  
<div class="modal fade" id="modalCedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitre" style="color:white;">Proticlub, c'est social&nbsp;!</h3>
                
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
                <button type="button" onclick="PostLigneCedule()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
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
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
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
    setTimeout("checkSession()", 20 * 60 * 1000);
    //-------------------------------------------------------------------------------
    function ShowCeduleInNewWin() {
	    var urlName = '/sanivan/aujourdhui/1/' + $('#dteActive').val();
	    window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
    }
  </script>
</body>
</html>
