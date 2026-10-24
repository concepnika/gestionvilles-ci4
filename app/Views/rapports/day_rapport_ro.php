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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="
    stylesheet" href="/assets/Ionicons/css/ionicons.min.css" />
  <!-- fullCalendar -->
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.min.css" />
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.print.min.css" media="print" />
  <!-- Theme style -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
   <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css" />
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
      width: 350px;
    }
    .popover-content{
      background: lightgray;
      color: darkblue;
      width: 350px;
      font-size:large;
    }
    .titre{
      color: darkblue;
      width: 100%;
      height:100%;
      font-size: large;
      font-weight:bold;
    }
    .titrenb{
      color: darkred;
      width: 100%;
      height:100%;
      font-size: 24px;
      text-align:center;
      font-weight:bold;
    }
    .separateur{
      width: 100%;
      height:35px;
      padding-top:5px;
      background-color: darkblue;
    }
    .li-info{
      background-color: lightblue;
      text-align:center;
    }
    .checkbox{
    width: 50px;
    padding: 3px 0;
}
.checkbox input[type=checkbox]{
    margin: 0;
    visibility: hidden;
    left: 1px;
    top: 1px;
}
.checkbox label{
    background: #702020;
    width: 50px;
    height: 20px;
    border-radius: 40px;
    box-shadow: inset 0 3px 3px rgba(0, 0, 0, 0.25);
    cursor: pointer;
    position: relative;
    transition: all 0.25s;
}
.checkbox label:before{
    content: "\f00d";
    color: red;
    background: #fff;
    font-family: "Font Awesome 5 Free";
    font-size: 16px;
    font-weight: 900;
    text-align: center;
    line-height: 25px;
    width: 25px;
    height: 25px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    border-radius: 50px;
    transform: translateY(-50%);
    position: absolute;
    top: 50%;
    left: 0;
    transition: all 0.25s;
}
.checkbox input[type=checkbox]:checked+label{ background: #008040; }
.checkbox input[type=checkbox]:checked+label:before{
    content: "\f00c";
    color: #fff;
    background: #00ff80;
    left: 25px;
}
@media only screen and (max-width:767px){
    .checkbox{ margin: 0 0 20px; }
}
  </style>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var IsLoaded = "<?php echo ($IsLoaded ?? '') ?>";
    var IsRap = 2;
    var myTimer = 0;
    var ToShow = 4;
    var EndIsChecked = 0;
    $(document).ready(function () {
      $('[data-toggle="popover"]').popover({ container: 'body' });
    });
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    function DayCamChauf(trnb) {
	    var urlName = "/rapport/postchauf?camion=" + $('#lb_cam_' + trnb).html() + '&idrh=' + $('#idrh_' + trnb).val() +
	    	'&KiloDeb=' + $('#KiloDeb_' + trnb).val() + '&KiloFin=' + $('#KiloFin_' + trnb).val() +
	    	'&Fuel=' + $('#Fuel_' + trnb).val() + '&StartTime=' + $('#StartTime_' + trnb).val() + '&EndTime=' + $('#EndTime_' + trnb).val() +
	    	'&datejour=' + $('#dtedeb').val() + '&idsuivis=' + $('#idsuivis_' + trnb).val() + '&idcam=' + $('#idcam_' + trnb).val();
	    $.ajax({
	    	async: false,
	    	type: "POST",
	    	url: urlName
      }).done(function (msg) {
	    	var objData = JSON.parse(msg);
	    	$('#Km_' + trnb).html(objData.KM);
	    	$("#HrsTrav_" + trnb).val(objData.HresTrav);
	    });
    }

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


  </nav>
  <div class="wrapper" style="margin-top: 5%;">
    <div class="row">
      <div class="col-md-12" id="myNavbar">
        <ul class="nav navbar-nav form-inline">
          <li><label  style="font-size:24px;font-weight:600;">Rapport Commandes RollOff</label> </li>
          <li> &nbsp;&nbsp;</li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>DE&nbsp;&nbsp; </label>&nbsp;&nbsp;
          </li>
          <li>
            <div class="form-group">
              <a href="#" onclick="LoadHeuresRH('-1')" id="divShowHideEmp" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journ&eacute;e pr&eacute;c&eacute;dente" data-content="Affiche la c&eacute;dule de la journ&eacute;e pr&eacute;c&eacute;dente">
                <img src="/assets/img/fleche_left.png" width="30" />&nbsp;
              </a>
              <input type="date" class="form-control" onchange="LoadHeuresRH('0')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ; ?>" id="dtedeb" />

              <a href="#" onclick="LoadHeuresRH('1')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journ&eacute;e suivante" data-content="Affiche la c&eacute;dule de la journ&eacute;e suivante">
                <img src="/assets/img/fleche_right.png" width="30" />&nbsp;
              </a>
            </div>
          </li>
          <li> <button type="button" id="btnAddDayRap" onclick="AddDayRap()" class="btn btn-success" style="display:<?php echo (($IsLoaded == 0)? 'block' : 'none') ; ?>;width:40px;height:35px" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ajouter la journée" data-content="Cliquer pour ajouter la validation des commandes">
  <i class="fa fa-plus" style='font-size:18px;'></i>
</button></li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
                <input type="text" id="edtFind" class="form-control" onkeyup="EntersearchTableSuivis(this.value, event)" placeholder="Rechercher" style="width:200px;" />

                <button type="button" class="btn btn-info btn-flat" id="sr" onclick="searchInTableSuivis()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affiche la liste selon le critère.">
                  <i class='fa fa-search' style='font-size:18px;'></i>&nbsp;
                </button>
                <button type="button" class="btn btn-danger btn-flat" onclick="ClearsearchTableSuivis()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affacer la recherche.">
                  <i class='fa fa-remove' style='font-size:18px;'></i>&nbsp;
                </button>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <button type="button" onclick="PrintRapRO()" class="btn btn-primary" style="margin-left: 25px;">
              <i class="fa fa-print"></i> Imprimer
            </button>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <button type="button" onclick="refreshAllCommande()" class="btn btn-warning" style="margin-left: 25px;">
              <i class="fa fa-print"></i> Recharger la journée
            </button>
          </li>
        </ul>

      </div> 
    </div>
    <div class="col-md-12">
    <input id="edtsens" type="hidden" value="<?php //echo ($sens ?? '') ; ?>" />
    <ul class="nav nav-tabs" style="top: 320px;">
      <li id="liChauf" class="li-info">
        <a href="#" onclick="ShowHideDivRap('divChauffeurs', 'liChauf')">Validation Camions-Chauffeurs</a>
      </li>
      <li id="liRapJour">
        <a href="#" onclick="ShowHideDivRap('divRapJour', 'liRapJour')">Rapport de la journée</a>
      </li>
    </ul>
    </div>

    <br />
    <div id="divChauffeurs" style="max-width: 700px" class="column">
      <table width="700" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th align="center"></th>
            <th align="center">CHAUFFEUR</th>
            <th align="center">CAMION</th>
            <th align="center">KM DÉPART</th>
            <th align="center">KM FIN</th>
            <th align="center">KM</th>
            <th align="center">CARBURANT</th>
            <th>HRE DÉBUT</th>
            <th>HRE FIN</th>
            <th align="center">HRES TRAV.</th>
            <th>
              <!--<button type="button" class="btn btn-primary" onclick="addSuivis()" data-toggle="popover" data-trigger="hover" data-placement="left" title="Confirmer" data-content="Cliquer pour confirmer la journée et créer le rapport du jour.">
                <i class='fa fa-save' style='font-size:18px;'></i>&nbsp;
              </button>-->
            </th>
          </tr>
        </thead>
        <tbody>
          <?php echo ($rapdaychauf_cam ?? '') ; ?>
        </tbody>
      </table>
    </div>
    <div id="divRapJour" style="display:none" class="column">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th colspan="13">
              <label style="width:100%;text-align:center;"><?php echo ($ActiveDate ?? '') ; ?></label>
            </th>
          </tr>
          <tr>
            <th align="center"></th>
            <th align="center" valign="top"><label style="width:100%;text-align:center;color:darkblue; font-weight: bold;"># CAMION</label></th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                KM<br />DÉPART
              </label>
            </th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                KM<br />ARRIVÉE
              </label>
            </th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                KM<br />PAR.
              </label>
            </th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                REV.<br />BRUT
              </label>
            </th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                ESS.<br />LITRES
              </label>
            </th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                ESS.<br />COÛT
              </label>
            </th>
            <th>
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                HRE<br />CHAUF.
              </label>
            </th>
            <th>
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                HRE<br />COÛT
              </label>
            </th>
            <th align="center"><label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
  ENF.
</label>
</th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                REV<br />NET
              </label>
            </th>
            <th align="center">
              <label style="width:100%;text-align:center;color:darkblue; font-weight: bold;">
                MOYENNE<br />REV / NET
              </label>
            </th>
          </tr>
        </thead>
        <tbody id="tbRapDay">
          <?php echo ($raprojour ?? '') ; ?>
        </tbody>
      </table>

    </div>

  </div>
  
  <div class="modal fade" id="modalSelCode" data-backdrop="static"  tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <br />
              <h3 class="modal-title" id="plTitreMsgBoite_1" style="color:white;text-align:center;">CODE CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsgBoite_2" style="color:white;text-align:center;">Sélection du code!</h4>
                
			</div>
            <div class="modal-body" style="min-height:150px;min-width:200px">
              <form>
                    <div class="form-group">
                      <input id="codeidcommande" type="hidden" value="" />
                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                           <label style="font-weight: bold; ">Boîtes RollOff</label> <br />                       
                        <div id="divBoiteROEP" class="col-sm-7 col-xs-12">
                            <select class="form-control" name="selCode" id="selCode" style="width:80px"  size="2" required>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="setCodeClient()" id="btnPostEP" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>      
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>

  <?php  echo view('sections/inc_pop_up_ro');?>
    <script src="/assets/jquery-ui/jquery-ui.min.js"></script>
    <!-- Slimscroll -->
    <script src="/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="/assets/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="/assets/js/adminlte.min.js"></script>
    <script src="/assets/js/jquery.dialog.js"></script>
    <!-- fullCalendar -->
    <script src="/assets/moment/moment.js"></script>
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
    <script src="/assets/js/nika.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
  var ActiveRow = 0;
  var ActiveRowPrix = 0;
  function PrintRapRO() {
    var titre = 'Validation Camions-Chauffeurs Roll Off en date du ' + ActiveDate;
    if (IsRap == 1) {
      var urlName = '/rapport/dayraprooloff/' + ActiveDate;
      document.location = urlName;
    }
    else {
      var urlName = '/rapport/dayro/' + ActiveDate + '/0/0?titre=' + titre + '&IsRap=' + IsRap;
        window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
    }

    
  }
  function ShowCommandeRap(idcommande) {
    var urlName = '/bureau/commandes/rolloff?idcommande=' + idcommande + '&dateactive=' + selectedDay;
    //document.location = urlName;
    window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
  }
  function showCodeToSelect(iddemande) {
    $("#codeidcommande").val(iddemande);
    $("#modalSelCode").modal("show");
  }
  function setCodeClient() {
	  urlName = "/rapport/setcode/" + $("#codeidcommande").val() + '/' + $("#selCode").val();
	  $.ajax({
		  async: false,
		  type: "POST",
		  url: urlName
	  }).done(function (msg) {
		  var objData = JSON.parse(msg);
		  $("#Ontario_" + id).val(objData.Ontario);
		  $("#revenu_" + id).html(objData.Revenu);
	  });
  }
  function refreshRapRO(idcommande) {
    var urlName = '/rapport/refreshcommande/' + idcommande + '/' + $("#dtedeb").val();
    document.location = urlName;
  }
  function refreshAllCommande() {
    var urlName = '/rapport/refreshday/' + $("#dtedeb").val();
    document.location = urlName;
  }
  </script>
</body>
</html>



















