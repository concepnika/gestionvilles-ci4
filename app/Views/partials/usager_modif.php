<?php

?>
<!DOCTYPE html>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css" />
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



    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!------ FORMULAIRE --------------------------------------------------------------------------------------------->
        <header class="page-header">
          <h2>Modifications au profil</h2>
        </header>
            <div class="row">
              <div class="col-xs-12" style="text-align:center; margin:0px 0 50px 0;">
                <h1>
                  <?php echo ci3lang('inscription_adhesion');?>
                </h1>
              </div><!-- Fin col-12-xs -->
            </div><!--Fin row #1 -->
            <form id="frmUsers" name="frmUpdateUser" class="form-horizontal" action="" method="post" role="form">

              <!-- Identification -->

              <div class="form-group B10">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('inscription_identification');?>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_adresse_courriel');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">@</span>
                  <input type="email" class="form-control" name="email" id="email" value="<?php echo ($emailmodif ?? '');?>" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>" disabled />
                  <div class="help-block with-errors"></div>
                </div>

              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_creer_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">?</span>
                  <input type="password" class="form-control" name="pass" id="pass" value="" placeholder="<?php echo ci3lang('inscription_creer_passw');?>" />
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_valide_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">!</span>
                  <input type="password" class="form-control" name="passconf" id="passconf" placeholder="<?php echo ci3lang('inscription_valide_passw');?>" data-match="#pass" data-match-error="<?php echo ci3lang('inscription_erreur_passw');?>" autocomplete="off" 
                          onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
                </div>
              </div>

              <!-- Fin identification -->


              <!-- Information personnelle -->

              <div class="form-group T40 B20">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('inscription_info_perso');?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_prenom');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" value="<?php echo ($prenommodif ?? '');?>" name="prenom" id="prenom" required />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_nom');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="nom" value="<?php echo ($nommodif ?? '');?>" id="nom" required />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  Privilège
                </div>
                <div class="col-sm-7 col-xs-12">
                  <select class="form-control" id="privilege" name="privilege" >
                    <option value="0">Choisir</option>
                    <?php echo ($privilege ?? '');?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12">
                  <button type="button" class="btn btn-warning" onclick="document.location='/admin/liste_usagers'">
                    Annuler
                  </button>
                  <button type="button" class="btn btn-success" onclick="ModifieUsager(<?php echo ($id ?? '');?>)">
                    Mettre à jour
                  </button>
                  <button type="button" class="btn btn-danger" onclick="DeleteUsager(<?php echo ($id ?? '');?>)">
                    Supprimer
                  </button>
                </div>
              </div>

              <!-- Fin information personnelle -->
            </form>
      <!-- /.content -->
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
                            <select class="form-control" name="camion" id="camion" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>     
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Date hors service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestart" />
                        </div>  
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteend" />
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
                           <input type="number" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" placeholder="Entrer le tonnage" value="" name="qte" />
                        </div>  
                        <div class="col-sm-7 col-xs-12"  style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="7" cols="50" > 

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
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestarths" />
                        </div>    
                           <br /> <br />
                          
                        
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendhs" />
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
                <button type="button" onclick="PostSuivisCam()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
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
                          <label style="font-weight: bold; ">Date début de l'absence </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestartrh" />
                        </div>    
                           <br /> <br />
                          
                        
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendrh" />
                        </div>    
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type d'absence</label> <br />
                            <select class="form-control" name="selRH" id="selRHabsence" required>
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

<div class="modal fade" id="modalRemoveCedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Suppression de cédule&nbsp;</h3>
                
			</div>
            <div class="modal-body" style="min-height:350px;">
              <form>
               <div class="form-group">

                <div style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">
                  <div class="col-sm-7 col-xs-12 box box-info">
                    <div class="box-header">
                      <h3 class="box-title">Sélection du type de cédule</h3>
                    </div>
                    <label>
                      <input type="checkbox" name="chkRearEnd" id="chkRearEnd" checked>&nbsp; &nbsp; REAR END&nbsp; &nbsp;
                    </label>
                    <label>
                      <input type="checkbox" name="chkRobot" id="chkRobot" checked>&nbsp; &nbsp;  ROBOT
                    </label>
                  </div>
                </div>
              </div>
                    <div class="form-group" style="margin-top:10px">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date de début </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtestartdel" />
                        </div>    
                           <br /> <br />
                          
                        
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date de fin </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteenddel" />
                        </div>    
                           <br /> <br />

                      </div>  
				          </div> <br /><br />


					  </form>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="RemoveCedule()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>
                <button type="button"  class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
  
<div class="modal fade" id="modalEditCamion" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="min-height:80%;width:1000px;">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsgUpd" style="color:white;text-align:center;">Edition du camion:&nbsp;</h3>
                
			</div>
      <div id="divEditCam">
        <section class="panel panel-featured-left">
        <div  class="panel-body">
          <section class="card">
				<form >
														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
															 <tr>
                                 <td> &nbsp;&nbsp;&nbsp;</td>
                              <td><h4># Camion :</h4><br> <input class="form-control"  id="edtCam" type="text" value="" style="width:100%" disabled>
                              </td>
                              
                              <td> &nbsp;&nbsp;&nbsp;</td>
                              <td><h4>Type de Camion :</h4><br> <select class="form-control" id="selTypeCamUpd">
                                    <option value="">Choisir</option>
                                      <?php echo ($updcamion ?? '') ; ?>
                                    </select></td>
                                <td> &nbsp;&nbsp;&nbsp;</td>
                            </tr>
															<tr>
                                <td>&nbsp;</td>
																<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
															</tr>
															<tr>
                                <td><label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkDelete" />&nbsp; &nbsp; Supprimé &nbsp; &nbsp;</label></td>
		                            <td> <label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkAuto" />&nbsp; &nbsp; Automatique </label></td>
                                <td> <label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkManuel" />&nbsp; &nbsp; Manuel &nbsp; &nbsp;</label></td>
		                             <td><label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkTreuil" />&nbsp; &nbsp; Trueil </label></td>
                                 <td><label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkVerseur" />&nbsp; &nbsp; Verseur </label></td>
															</tr>

														</table>
													</form>

          </section>
          </div>

      </section>
      </div>
			<div class="modal-footer">
            <button type="button" id="btnUpdCamion" onclick="sendCamionInfo()" class="btn-primary btn"  style="margin:4px 5px 0 0;" data-dismiss="modal"> Enregistrer </button>
            <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Fermer</button>
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
    setTimeout( "checkSession()", 20 * 60 * 1000 );
  </script>

</body>
</html>
