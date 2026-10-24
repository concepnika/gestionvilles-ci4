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
  <script type='text/javascript'>
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
<body class="hold-transition skin-blue">

  <div class="wrapper">

    <header class="main-header  navbar-dark bg-primary fixed-top" style="position: fixed;top: 0; width: 100%;height:auto">

      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top pull-right">
        <!-- Sidebar toggle button-->

        <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li>
              <a href="/admin"  data-toggle="popover"  data-trigger="hover" data-placement="bottom" class="btn-primary" title="Accueil" data-content="Aller à l'Accueil">
               <i class="fa fa-home" style="font-size:28px;color:black"></i>
              </a>
          </li>
          <li >
            <a  class="btn-primary" href="/sanivan/calendrier"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Afficher le calendrier des villes"><i class='fa fa-calendar' style='font-size:24px'></i>&nbsp;Villes</a>
              
        </li>
                  <li>
          <a class="btn-primary"  href="/sanivan/validation"  data-toggle="popover"  data-trigger="hover" data-placement="bottom" title="Validation Sanivan" data-content="Afficher la page de validation pour les employés et les camions." ><i class='fa fa-check-square-o' style='font-size:24px;'></i>&nbsp;Vérif</a>
        </li>
        <li >
            <a  class="btn-primary" href="/sanivan/camions"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Camion H-S et Avec Matériel" data-content="Afficher la page pour la gestion des camions Sanivans et RollOff"><i class='fa fa-truck' style='font-size:24px'></i></a>
              
        </li>
                  <li>
          <a class="btn-primary"  href="/sanivan/validation"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Chauffeurs et Aides" data-content="Afficher la page pour la gestion des Chauffeurs et des Aides" ><i class='fa fa-user' style='font-size:24px'></i></a>
        </li>

          <li>
            <a href="#" onclick="ReLoadToday()" type="button"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Rafraichir" data-content="Rafraichir la page Aujourd'hui" class="btn btn-link"><img width="35" src="/assets/img/redo-solid.png" /></a>
          </li>
        </ul>
      </div>
      </nav>
    </header>

    <div class="wrapper" style=" margin-top:3%">
      <section class="content" >

        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
                <div class="container-fluid" >
                  <div class="form-group">
                    
                    <div class="col-sm-7 col-xs-12">
                       <label style="font-weight: bold; "> <input class="form-check-input" type="checkbox" id="chkStart" />&nbsp; &nbsp; A débuté </label>
                    </div>
                  </div>  
                    <div class="form-group">
     <br /> <br />
                           <label style="font-weight: bold; "># Camions  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="camion" id="camion" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           <label style="font-weight: bold; ">Date hors service </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestart" />
                        </div>    
                           <br /> <br />
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteend" />
                        </div>    
                           <br /> <br />
                           <label style="font-weight: bold; "># Camions  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <textarea rows="7" cols="50" > 

                            </textarea>
                        </div>    
				          </div>
              <!-- /.box-body -->
            </div>
            <!-- /. box -->
          </div>
            </div>
            <!-- /. box -->
          </div>  
      </section>
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
    <script src="/assets/js/nika.js"></script>

</body>
</html>



















