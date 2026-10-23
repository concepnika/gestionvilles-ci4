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

      <section id="secContent" class="content">

        <!------ FORMULAIRE --------------------------------------------------------------------------------------------->

        <div class="container">

          <div class="section_standard">

            <h2>
              <?php echo ci3lang('option_syst');?>
            </h2>

            <div class="row">
              <div class="col-xs-12" style="text-align:center; margin:0px 0 50px 0;">
                <h1>
                  <?php echo ci3lang('inscription_adhesion');?>
                </h1>
              </div><!-- Fin col-12-xs -->
            </div><!--Fin row #1 -->
            <form  class="form-horizontal" action="" method="post" role="form" name="frmOption" id="frmOption">

              <!-- Identification -->

              <div class="form-group B10">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('inscription_identification');?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_cie');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="cie" id="cie" value="<?php echo ($Cie ?? '');?>" required />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_adresse_courriel');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="email" class="form-control"  name="email" id="email" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>"  autocomplete="off" readonly
            onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
                </div>

              </div>
               <!--<div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_logo');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="file" name="logo"  size="33" />
                </div>
                 <div class="col-sm-7 col-xs-12">
                  <img src="<?php echo ($Logo ?? '');?>" width="200" />
                </div>
              </div>-->             
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
                  <?php echo ci3lang('inscription_adresse');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="Adresse" id="Adresse"  value="<?php echo ($Adresse ?? '');?>" />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_ville');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="ville" id="ville"   value="<?php echo ($Ville ?? '');?>" />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_province');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="province" id="province"   value="<?php echo ($Province ?? '');?>" />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_tps');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="number" class="form-control" style="width:100px;" name="TPS" id="TPS"   value="<?php echo ($TPS ?? '');?>" />
                </div>
              </div>              
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_tvq');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="number" class="form-control" style="width:100px;" name="TVQ" id="TVQ"   value="<?php echo ($TVQ ?? '');?>" />
                </div>
              </div>           
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_notps');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" style="width:200px;" name="NoTPS" id="NoTPS"   value="<?php echo ($NoTPS ?? '');?>" />
                </div>
              </div>           
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_notvq');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" style="width:200px;" name="NoTVQ" id="NoTVQ"   value="<?php echo ($NoTVQ ?? '');?>" />
                </div>
              </div>    
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                </div>
                <div class="col-sm-7 col-xs-12">
                  <table id="tblfrais"  class="table table-bordered table-striped" style="width: 500px;">
                            <thead>
                                <tr align="center">
    							                <th colspan="2" style="text-align:center;color:darkblue;">Frais pour les commandes</th>
                                </tr>
                                <tr>
    							                <td width="100" class="cellule" style="font-size:medium;font-weight:bold;">Description</td>
    							                <td width="10" class="cellule"  style="font-size:medium;font-weight:bold;">Montant</td>
                                </tr>
                            </thead>
                            <tbody id="tboFrais">
                              <tr>
                                
                                <td colspan="2" align="center"><div class="input-group"><label class="form-control" style="font-size:medium;font-weight:bold;width:170px;">Durée de location</label>
                                <input class="form-control"  type="text" style="width:50px;text-align:right" id="DureeLocation" name="DureeLocation" value="<?php echo ($DureeLocation ?? '');?>" />
                                   <span class="input-group-btn form-control" style="width: 210px;">
                                     <span  style='font-size:18px;color:darkblue;'> / semaine additionnelle</span>
                                   </span></div>
                                  </td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:220px;">Frais surplus livraison</label></td>
                                <td><div class="input-group">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="FraisLivraison" name="FraisLivraison" value="<?php echo number_format($FraisLivraison,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:220px;">Frais Redeance TM</label></td>
                                <td><div class="input-group">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="FraisRedevanceTM" name="FraisRedevanceTM" value="<?php echo number_format($FraisRedevanceTM,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:220px;">Surcharge poids</label></td>
                                <td><div class="input-group">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="FraisPoids" name="FraisPoids" value="<?php echo number_format($FraisPoids,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Frais carburant enfouissement</label></td>
                                <td><div class="input-group">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="SurchargeSite" name="SurchargeSite" value="<?php echo number_format($SurchargeSite,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Montant assigné pour la livraison</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="montantLivraison" name="montantLivraison" value="<?php echo number_format($montantLivraison,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr> 
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Montant assigné pour le déglaçage</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="FraisDeglacage" name="FraisDeglacage" value="<?php echo number_format($FraisDeglacage,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr> 
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Montant assigné pour le lavage</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="FraisLavage" name="FraisLavage" value="<?php echo number_format($FraisLavage,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr> 
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">KM pour milage Ontario</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="MilageOntario" name="MilageOntario" value="<?php echo number_format($MilageOntario,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr> 
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Montant assigné pour TRANS BO</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="montantTransBo" name="montantTransBo" value="<?php echo number_format($montantTransBo,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>    
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Salaire chauffeur ROLLOFF</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="salairechauffeur" name="salairechauffeur" value="<?php echo number_format($salairechauffeur,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr> 
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Salaire chauffeur SANIVAN</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                      <input class="form-control" type="text" style="width:90px;text-align:right" id="salairechauffeur_san" name="salairechauffeur_san" value="<?php echo number_format($salairechauffeur_san,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr> 
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Salaire aide</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="salalaireaide" name="salalaireaide" value="<?php echo number_format($salalaireaide,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>      
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;width:360px;">Montant Gazeline</label></td>
                                <td><div class="input-group" style="width: 150px;">
                                <input class="form-control"  type="text" style="width:90px;text-align:right" id="montantfuel" name="montantfuel" value="<?php echo number_format($montantfuel,2,'.',' ');?>" />
                                   <span class="input-group-btn form-control" style="width: 30px;">
                                     <i class='fa fa-dollar' style='font-size:18px;color:darkblue;'></i>
                                   </span></div>
                                  </td>
                              </tr>                                
                            </tbody>
                          </table>
                 
                </div>

              </div>
              <!-- Fin information personnelle -->
              <div class="form-group T40 B20">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('option_syst_info_envoi');?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_typeenvoi');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <select class="form-control" style="width:200px" name="autosend" id="autosend" >
                      <option value="0">Choisir</option>
                      <option <?php echo (($autosend == 1)? ' selected ' : '' ) ;?> value="1"><?php echo ci3lang('envoi_auto');?></option>     
                      <option <?php echo (($autosend == 2)? ' selected ' : '' ) ;?> value="2"><?php echo ci3lang('envoi_manuel');?></option>                   
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_heureenvoi');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="time" class="form-control" style="width:200px" value="<?php echo ($timetosend ?? '') ;?>" name="timetosend" id="timetosend" />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('option_syst_confirmation');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <select class="form-control" style="width:200px" name="NeedConfirm" id="NeedConfirm" >
                      <option value="0">Choisir</option>
                      <option <?php echo (($NeedConfirm == 1)? ' selected ' : '' ) ;?> value="1"><?php echo ci3lang('option_syst_confirm_rh');?></option>     
                      <option <?php echo (($NeedConfirm == 2)? ' selected ' : '' ) ;?> value="2"><?php echo ci3lang('option_syst_confirm_auto');?></option>                   
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                </div>
                <div class="col-sm-7 col-xs-12">
                  <table id="tblproduits"  class="table table-bordered table-striped" style="width: 400px;">
                            <thead>
                                <tr align="center">
    							                <th colspan="2" style="text-align:center;color:darkblue;">Clignotant des déchets Roll Off</th>
                                </tr>
                                <tr>
    							                <td width="100" class="cellule" style="font-size:medium;font-weight:bold;">Déchet</td>
    							                <td width="10" class="cellule"  style="font-size:medium;font-weight:bold;">Couleur</td>
                                </tr>
                            </thead>
                            <tbody id="tboProd">
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBeton == 1)? 'checked' : '' ) ; ?> id="IsBeton" name="IsBeton"  value="1">B&eacute;ton</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_beton" name="color_beton" value="<?php echo ($color_beton ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsAsphalte == 1)? 'checked' : '' ) ; ?> id="IsAsphalte" name="IsAsphalte" value="1">Asphalte</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_asphalte" name="color_asphalte" value="<?php echo ($color_asphalte ?? '')?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBranches == 1)? 'checked' : '' ) ; ?> id="IsBranches" name="IsBranches" value="1">Organique</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_branches" name="color_branches" value="<?php echo ($color_branches ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsFumier == 1)? 'checked' : '' ) ; ?> id="IsFumier" name="IsFumier" value="1">Fumier</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_fumier" name="color_fumier" value="<?php echo ($color_fumier ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsTerre == 1)? 'checked' : '' ) ; ?> id="IsTerre" name="IsTerre" value="1">Terre</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_terre" name="color_terre" value="<?php echo ($color_terre ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBois == 1)? 'checked' : '' ) ; ?> id="IsBois" name="IsBois" value="1">Recyclage</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_bois" name="color_bois" value="<?php echo ($color_bois ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsAmiante == 1)? 'checked' : '' ) ; ?> id="IsAmiante" name="IsAmiante"  value="1">Amiante</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_amiante" name="color_amiante" value="<?php echo ($color_amiante ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBardeau == 1)? 'checked' : '' ) ; ?> id="IsBardeau" name="IsBardeau"  value="1">Bardeau</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_bardeau" name="color_bardeau" value="<?php echo ($color_bardeau ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsMateriaux == 1)? 'checked' : '' ) ; ?> id="IsMateriaux" name="IsMateriaux"  value="1">Mat&eacute;riau</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_bardeau" name="color_materiaux" value="<?php echo ($color_materiaux ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsDemolition == 1)? 'checked' : '' ) ; ?> id="IsDemolition" name="IsDemolition"  value="1">D&eacute;molition</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_demolition" name="color_demolition" value="<?php echo ($color_demolition ?? '');?>" /></td>
                              </tr>
                              <tr>
                                <td><label class="checkbox-inline" style="font-size:medium;font-weight:bold;"><input type="checkbox" <?php echo (($IsBetonArme == 1)? 'checked' : '' ) ; ?> id="IsBetonArme" name="IsBetonArme"  value="1">B&eacute;ton arm&eacute;</label></td>
                                <td align="center"><input class="form-control"  type="color" id="color_betonarme" name="color_betonarme" value="<?php echo ($color_betonarme ?? '');?>" /></td>
                              </tr>
                            </tbody>
                          </table>
                 
                </div>

              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12">
                  <button type="button" onclick="SubmitForm()" class="btn btn-success" style="outline:none">
                    <?php echo ci3lang('enregister');?>
                  </button>
                </div>
              </div>

            </form>


          </div><!-- Fin section standard -->
        </div><!-- Fin container -->
      </section>
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


  </script>

</body>
</html>
