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
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "";
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

  <div class="wrapper" style="margin-top: 7%;">
    <div class="row" >

      </div>  
            <div class="row" >
            <div class="col-12  box box-info" id="myNavbar" style="margin-left: 5%; width: 100%">
                <label  style="font-size:24px;font-weight:600;width:600px;" >Gestion des polylines
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </label> 
            </div>
                <div class="col-lg-1">
                </div>
            	<div class="col-lg-12  box box-info">
		            <section class="panel panel-featured-left panel-featured-primary">
			            <div class="panel-body">
				            <section class="card">
           
						            <div class="card-body" style="overflow-x:scroll; width:100%; text-align: center;">
                          <table id="tblboites" class="display responsive nowrap" border="1" style="margin: 0 auto;">
                            <thead>
                                <tr style="text-align:right;">
    							                
    							    <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;width:220px">VILLE</td>
    							    <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;width:250px">TITRE</td>
                                    <td style="text-align : center;font-weight: bold;color: darkblue;line-height:35px;width:220px"">PARCOURS</td>
                                </tr>
                            </thead>
                            <tbody id="tboProd">
                              <?php echo ($listpolyline ?? '') ; ?>
                            </tbody>
                          </table>
						            </div>
				            </section>
			            </div>
		            </section>
	            </div>
            </div>
</div>
  <div class="modal fade" id="modalReduit" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-block-md">
      <div class="modal-content">
        <div  class="modal-header btn-danger">
          <h2 class="modal-title">Alerte(s) Requête / Réponse</h2>
        </div>
        <div  class="modal-body">
                      <div id="divInfoReq" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        <input type="hidden" id="edtIdReqVille" value="" />
                        <input type="button" onclick="showNewRequete()" class="btn btn-info" id="btnModalReq" style="display:none"  value="" />
                        <br />
                        <input type="hidden" id="edtIdRepVille" value=""  />
                        <input type="button" class="btn btn-info" id="btnModalRep" style="display:none" value="" />
                      </div> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>
<div class="modal fade" id="modalMapPolylinesCedule" tabindex="-1" role="dialog" aria-labelledby="modalMapPolylinesCeduleLabel">
    <div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;" role="document">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalMapPolylinesCeduleLabel" style="color:white;">
                    <i class="fa fa-map"></i> Visualisation du parcours - <span id="spSecteurNameCedule"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div id="polylineMapCedule" style="height: 600px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<?php echo view('sections/inc_pop_up_gen');?>
    <!-- Modal pour visualiser une polyline (utilisé par showPolylineSingle) -->

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
    selectedDay = "";
      function ShowReduitPolyline() {
          $('#modalReduit').modal("show");
      }

    
  </script>
</body>
</html>
