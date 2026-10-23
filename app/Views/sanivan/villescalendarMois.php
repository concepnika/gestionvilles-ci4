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

  <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js" ie.="" data-type=""></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/fullcalendar.min.css" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/fullcalendar.min.js "></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/lang-all.js "></script>  
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <script type="text/javascript" src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
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
  <link rel="stylesheet" href="/assets/css/jquery-ui.multidatespicker.css" />
  <style>

		.tdcalendrier{
			width:190px;
			height:110px;
			background-color:ivory;
			border: 1px solid #708090;
		}
		.tdcalendrierHeader{
			width:190px;
      max-height:20px;
			height:20px;
			background-color:darkblue;
			color:white;
			text-align:center;
			font-size:1.5em;
		}
		.tdcalendrierHeaderMonth{
			width:190px;
			height:25px;
			background-color:darkblue;
			color:white;
			text-align:center;
			font-size:2.5em;
		}
		.tdcalendrierComboMonth{
			width:180px;
			height:38px;
			background-color:#FFFFCC;
			font-size:1.0em;
		}

		.tableCalendrier{
			border: 1px solid #800000;
			width: 1700px;
      height:100%;
		}
		.tdjour{
			width:40px;
			height:35px;
			background-color:darkblue;
			color:white;
			text-align:center;
			font-size:1.5em;
		}
		.tdinfo{
			height:20px;
			font-size:medium;
		}
		.tdheure{
			width:40px;
			height:20px;
			border: 1px solid #800000;
			font-family:Arial, Helvetica, sans-serif;
		}
		.tdheureVide{
			width:40px;
			height:20px;
			font-family:Arial, Helvetica, sans-serif;
		}
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
  <script src="/assets/js/jquery-ui.multidatespicker.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo $ActiveDate ?>";
    var moisseul = "<?php echo $moisseul ?>";
    typederamassage = "<?php echo $typederamassage; ?>";
    function ConfirmeLoad(Route)
    {
        $("#td_" + Route).css("background-color","#80FF9F");
        $("#chk_" + Route).css("background-color","#80FF9F");
    }
    $(document).ready(function () {
		$('[data-toggle="popover"]').popover();  
		
    });

  </script>
</head>
<body class="hold-transition skin-blue" style="margin-left:34px">

  <div class="wrapper" style="margin-left: 20px;">


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

<div class="row" style="margin-left: 20px; margin-top: 70px; width: 100%; border-bottom: solid;">

           <div class="col-12" style="margin-left: -50px;">
		        <ul class="nav nav-pills">
              <li>
                <div class="input-group input-group-sm"  style="width:250px">
                  <div class="input-group-addon" style="height:42px"><b>VILLES</b></div>
                  <select class="form-control" style="height:42px" name="optVilles" id="optVilles" onchange="getVille(this.value)">
                      <option value="0">Choisir</option>
                      <?php echo $optvilles; ?>
                  </select>
                </div>
			        </li>  
              
              <li >
				        &nbsp;
			        </li> 
              <li>
                <div class="input-group input-group-sm"  style="width:250px">
                  <div class="input-group-addon" style="height:42px"><b>RAMASSAGES</b></div>
                  <select class="form-control" style="height:42px" name="optRamassages" id="optRamassages">
                      <option value="">Choisir</option>
                      <?php echo $optramassages; ?>
                  </select>
                </div>
			        </li>  
              
              <li >
				        &nbsp;
			        </li>                
              <li >
				        <input id="radRearLoad" name="rad" value="1" checked style="width:32px;height:32px;margin-top:0px"  type="radio" /><span style="font-weight:bold;font-size:large;margin-top:-10px">&nbsp;REAR END</span> 
			        </li> 
              <li >
				        &nbsp;
			        </li>                 
              <li >
				        <input id="radRobotLoad" name="rad" value="2" style="width:32px;height:32px;margin-top:0px"  type="radio" /><span style="font-weight:bold;font-size:large;margin-top:-10px">&nbsp;ROBOT</span> 
			        </li> 
              <li >
				        &nbsp;
			        </li>  
              <li >
				        <input id="chkManuel" value="2" style="width:32px;height:32px;margin-top:0px" onchange="setManuel()"  type="checkbox" /><span style="font-weight:bold;font-size:large;margin-top:-10px;color:orange">&nbsp;Déployement MANUEL</span> 
			        </li> 

              <li >
				        <button class="btn btn-info" type="button"  id="btnDeploie" style="height:42px;margin-top:0px" onclick="toCedule()" >Voir la Cédule</button>
			        </li>              
		        </ul>
	        </div>   
         <div class="col-12" style="margin-left: -50px;">&nbsp; &nbsp;</div>
           <div id="divDeploye" class="col-12" style="margin-left: -50px;">
		        <ul class="nav nav-pills">
              <li >
				        <h2 style="margin-top: -5px">Déploiement&nbsp; &nbsp;</h2>
			        </li> 

              <li >
                <div class="form-group" style="margin-left:0px;">
                  <div class="input-group input-group-sm"  style="width:350px">
                    <div class="input-group-addon" style="width:42px;height:42px;margin-top:0px"><b>Du</b></div>
                    <input type="date" class="form-control" style="width: 130px; height: 42px;" name="dteDu" id="dteStartDeploie" />
                    <div class="input-group-addon" style="width:42px;height:42px;margin-top:0px"><b>Au</b></div>
                    <input type="date" class="form-control" style="width: 130px; height: 42px;" name="dteDu" id="dteEndDeploie" />
                    
                  </div>
                </div>
			        </li> 

               <li>
                <table border="1" cellspacing="0" cellpadding="10" class="txtnormal" style="margin-top:-1px">
                  <tr>
		            
                    <td> <label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" style="width:28px;height:28px;" type="checkbox" id="chkLUNAdd" />&nbsp;LUN &nbsp; &nbsp;</label></td>
		                  <td><label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" style="width: 28px; height: 28px; " type="checkbox" id="chkMARAdd" />&nbsp;MAR &nbsp; &nbsp;</label></td>
                      <td><label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" style="width: 28px; height: 28px; " type="checkbox" id="chkMERAdd" />&nbsp;MER &nbsp; &nbsp;</label></td>
                      <td> <label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" style="width: 28px; height: 28px; " type="checkbox" id="chkJEUAdd" />&nbsp;JEU &nbsp; &nbsp;</label></td>
		                  <td><label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" style="width: 28px; height: 28px; " type="checkbox" id="chkVENAdd" />&nbsp;VEN &nbsp; &nbsp;</label></td>
                  </tr>
                </table>
              </li> 
              <li >
                <div class="form-group">
                  <div class="input-group input-group-sm" style="width:293px">
                    <div class="input-group-addon" style="width:92px;height:42px;max-height:20px;margin-top:0px"><b>Secteurs</b></div>
                      <select class="form-control" style="height:42px;width:200px" name="optFrequences" id="optFrequences">
                          <option value="0">Choisir</option>
                        <?php echo $secteurs; ?>

                      </select>
                    
                  </div>
                </div>
			        </li> 
              <li >
                <div class="form-group">
                  <div class="input-group input-group-sm" style="width:293px">
                    <div class="input-group-addon" style="width:92px;height:42px;max-height:20px;margin-top:0px"><b>Fréquence</b></div>
                      <select class="form-control" style="height:42px;width:200px" name="optFrequences" id="optFrequences">
                          <option value="">Choisir</option>
                          <option value="1">Toutes les semaines</option>
                          <option value="2">Toutes les 2 semaines</option>
                          <option value="3">Première semaine du mois</option>
                          <option value="4">Deuxième semaine du mois</option>
                          <option value="5">Dernière semaine du mois</option>                          
                      </select>
                    
                  </div>
                </div>
			        </li>  
               <li >
				        <button class="btn btn-success" type="button"  id="btnDeploie" style="height:42px;margin-top:0px" onclick="deploieCalendrier()" >Déployer</button>
			        </li> 
               <li >
				        <button type="button" id="btnCancel" onclick="cancelCalendrier()" class="btn btn-danger" style="height:42px;margin-top:0px">Annuler</button>
			        </li> 
		        </ul>
	        </div>            
        </div>
          
    <div class="row" style="margin-left: 3px;margin-top: 50px;" >

           <div class="col-md-12">
		        <ul class="nav nav-pills">
			                  <li>
            <div class="input-group input-group-sm"  style="width:230px">
               <span class="input-group-btn">
              <button type="button" id="btnPrevAnnee" class="btn btn-info btn-flat" onclick="ChangeAnnee(1)" >
                  <i class='fas fa-angle-left' style='font-size:24px;'></i>&nbsp;
              </button>
              </span>
              <select class="form-control" name="optAnnee" id="optAnnee" onchange="ChangeAnnee(this.value)" >
                  <?php echo $listyear; ?>
              </select>
              <span class="input-group-btn">
                <button type="button" id="btnNextAnnee" class="btn btn-info btn-flat" onclick="ChangeAnnee(2)" >
                  <i class='fa fa-angle-right' style='font-size:24px;'></i>&nbsp;
                </button>
              </span>
            </div>
          </li>
              <li>&nbsp;&nbsp;</li><li>&nbsp;&nbsp;</li><li>&nbsp;&nbsp;</li><li>&nbsp;&nbsp;</li>
          <li>
            <div class="input-group input-group-sm"  style="width:230px">
               <span class="input-group-btn">
              <button type="button" id="btnPrevMois" class="btn btn-info btn-flat" onclick="ChangeMois('prev')" data-toggle="popover" >
                  <i class='fa fa-angle-left' style='font-size:24px;'></i>&nbsp;
              </button>
              </span>
              <select class="form-control" name="optMois" id="optMois" onchange="ChangeMois(this.value)" >
                  <?php echo $listmois ; ?>
              </select>
              <span class="input-group-btn">
 
                <button type="button" id="btnNextMois" class="btn btn-info btn-flat"  onclick="ChangeMois('next')" >
                  <i class='fa fa-angle-right' style='font-size:24px;'></i>&nbsp;
                </button>
              </span>
            </div>
          </li> 
          <li>&nbsp;&nbsp;</li>  
            <li >
				    <button class="btn btn-info" type="button"  id="btnDeploie" style="margin-top:0px"  >MOIS</button>
			    </li> 
            <li >
				    <button type="button" id="btnCancel" onclick="gotoAnnee()" class="btn btn-default" style="margin-top:0px">ANNÉE</button>
			    </li>    
          <li >
				    <?php echo $nomenclature; ?>
			    </li>          
		        </ul>
	        </div>       
        </div>
    <div class="row" style="margin-left: -55px;margin-top: 5px; border-bottom: solid; width: 1800px;">
            <div class="col-md-1"> </div>
            <div id="divCalendar" class="col-md-8">
            <div class="box box-primary">
            <div class="box-body no-padding">
              <div id="calendar" style="width:100%">
                <?php echo $calendrier; ?>
              </div>
            </div>
          </div>
          
        </div>
        

        </div>
      <div class="row" style="margin-top: 30px;">
        <div class="col-md-12"> </div>
      </div>
</div>
  
<div class="modal fade" id="modalDeploie" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitre" style="color:white;">Informations de déploiement</h3>
                
			</div>
            <div class="modal-body">
                <div class="container-fluid" >
                    <div class="form-group" style="margin-left:10px;">
                      <div class="input-group input-group-sm"  style="width:350px">
                        <div class="input-group-addon"><b>Déploiement Débutant le </b></div>
                        <input type="date" class="form-control" name="dteDeploieDeb" id="dteDeploieDeb" />
                      </div>
                    </div>  
                    <div class="form-group" style="margin-left:10px;">
                      <div class="input-group input-group-sm"  style="width:350px">
                        <div class="input-group-addon"><b>Déploiement se terminant le</b></div>
                        <input type="date" class="form-control" name="dteDeploieFin" id="dteDeploieFin" />
                      </div>
                    </div>  
                    <div class="form-group">
                         <br /> <br />
                            <label style="font-weight: bold; ">Types de déploiement  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="typeprod" id="typeprod" required>
                                <option value="0">Choisir</option>
                                <option value="1">Remassage Actif pour la ville active</option>
                                <option value="2">Tous les remassages pour la ville active</option>
                                <option value="3" >Toutes Les villes</option>
                            </select>
                        </div>
                    </div>   
                           <br /><br />
                           <label style="font-weight: bold; ">Chauffeurs  </label> <br />
                       
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="chauffeur" id="chauffeur" required>
                                <option value="25">Choisir</option>
                                <?php echo $chauffeurs ; ?>
                            </select>
                        </div>
                        <br /><br />
                           <label style="font-weight: bold; ">Aides  </label> <br />
                       
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="aide" id="aide" required>
                                <option value="24">Choisir</option>
                                <?php echo $aides ; ?>
                            </select>
                        </div>
                             <br />
                             <br />
                    
                           <label style="font-weight: bold; "># Camions  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="camion" id="camion" required>
                                <option value="Cam: 1">Choisir</option>
                                <?php echo $camions ; ?>
                            </select>
                        </div>                  
				</div>
            </div>
			<div class="modal-footer">
                <button type="button" onclick="excDeploiement()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Déployer</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
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
    <script src="/assets/js/calendrierville.js"></script>
    <script src="/assets/fullcalendar/dist/locale/fr-ca.js"></script>
    <script src="/assets/fullcalendar/dist/locale/fr.js"></script>
  <script>
    selectedDay = "<?php echo $ActiveDate ?>";
    var dftView = 'basicWeek';
    //-------------------------------------------------------------------------------
    var objData = "";
    

  </script>
</body>
</html>
