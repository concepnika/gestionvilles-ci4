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
    .active-diction{
      background-color: darkblue;
      color: white;
      font-size: medium;
      font-weight:bold;
    }
    .diction{
      background-color: lightgray;
      color: darkblue;
      font-size: medium;
      font-weight:normal;
    }
    .thdiction {
      background-color: darkslateblue;
      color: white;
      font-weight: bold;
      line-height: 30px;
      font-size: medium;
      text-align: center;
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
            <header class="page-header">
          <h2>Les Dictionnaires</h2>
        </header>
            <div class="row">

           <div class="col-md-12">
		            <ul class="nav nav-tabs">

                  <li>

                    <a href="#" id="diction_1" class="active-diction" onclick="ShowDictionnaire(1)">TYPES CAMIONS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_10" class="diction" onclick="ShowDictionnaire(10)">TYPES BOÎTES
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_2" class="diction" onclick="ShowDictionnaire(2)">TYPES RH
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_3" class="diction" onclick="ShowDictionnaire(3)">TYPES ABSENCES
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_4" class="diction" onclick="ShowDictionnaire(4)">TYPES DÉCHETS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_5" class="diction" onclick="ShowDictionnaire(5)">STATUTS CAMIONS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_6" class="diction" onclick="ShowDictionnaire(6)">PERTES DE TEMPS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_7" class="diction" onclick="ShowDictionnaire(7)">DÉPOTOIRS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_8" class="diction" onclick="ShowDictionnaire(8)">EMPLACEMENTS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_9" class="diction" onclick="ShowDictionnaire(9)">FRAIS DIVERS
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_11" class="diction" onclick="ShowDictionnaire(11)">STATUT REQUÊTES
                    </a>
                  </li>
                  <li>

                    <a href="#" id="diction_12" class="diction" onclick="ShowDictionnaire(12)">TYPES HRES EXTRA
                    </a>
                  </li>
		            </ul>
	        </div>       
        </div>
             <div class="row" >
              <div class="col-lg-1">
                </div>
              <div class="col-lg-8">
		            <section class="panel panel-featured-left">
			            <div class="panel-body">
				            <section class="card">

						            <div class="card-body">
                <button type="button" id="btnShowHide" style="margin-top:5px;margin-bottom: 5px;" onclick="ShowHideNika('divAddDiction')" class="btn btn-info" >Ajouter</button>
                <div id="divAddDiction"  style="display:none">
                  <form action="/sanivan/adddictionnaire?0" method="post" enctype="multipart/form-data" id="form_0"  name="form_0" >
                      <div id="divToAdd" class="table-responsive-md">
                            <?php echo ($addtodiction ?? '') ; ?>
                      </div>
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
            	<div class="col-lg-11">
		            <section class="panel panel-featured-left panel-featured-primary">
			            <div class="panel-body">
				            <section class="card">

						            <div id="divDiction" class="table-responsive" >
                          <table class="table">
                            <thead>
                                <?php echo ($entete ?? '') ; ?>
                            </thead>
                            <tbody id="tboProd">
                              <?php echo ($listdict ?? '') ; ?>
                            </tbody>
                          </table>
						            </div>
				            </section>
			            </div>
		            </section>
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
