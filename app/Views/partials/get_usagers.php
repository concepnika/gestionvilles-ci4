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
        <div class="container-fluid">
                <h3 class="text-dark mb-4">Liste des employés</h3>
                <div class="card shadow">
                    <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold">Employés</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-nowrap">
                                <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable"><label>Show&nbsp;<select class="form-control form-control-sm custom-select custom-select-sm"><option value="10" selected="">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>&nbsp;</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-right dataTables_filter" id="dataTable_filter"><label><input type="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search"></label></div>
                            </div>
                        </div>
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                            <table class="table dataTable my-0" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Courriel</th>
                                        <th>Privilège</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php echo ($tbody ?? '');?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Nom</strong></td>
                                        <td><strong>Prénom</strong></td>
                                        <td><strong>Courriel</strong></td>
                                        <td><strong>Privilège</strong></td>
                                        <td><strong></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
      </section>


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
                          <label style="font-weight: bold; ">Date hors service </label> <br />
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
      function removeUser(id) {
        var urlName = "/admin/supprimer/" + id;
    		$.ajax({
    			async: false,
    			type: "POST",
    			url: urlName
    		}).done(function(msg){
    			document.location = "/admin/liste_usagers" ;
    		});
	             
      }
  </script>
    </div>
</body>
</html>
