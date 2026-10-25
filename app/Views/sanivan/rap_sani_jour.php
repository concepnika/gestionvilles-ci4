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
  <!-- Morris chart -->
  <link rel="stylesheet" href="/assets/bower_components/morris.js/morris.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css" />
  <link rel="stylesheet" href="/assets/plugins/iCheck/all.css">

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
    .titreville{
      color: black;
      width: 100%;
      height:100%;
      font-size: 24px;
      text-align:center;
      font-weight:bolder;
    }
    .titredepot{
      color: darkblue;
      width: 100%;
      height:100%;
      font-size: 24px;
      text-align:center;
      font-weight:bolder;
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
      alert(trnb);
	    var urlName = "/tablettes_san/postchauf?KiloDeb=" + $('#KiloDeb_' + trnb).val() + '&KiloFin=' + $('#KiloFin_' + trnb).val() +
	    	'&Fuel=' + $('#Fuel_' + trnb).val() + '&HreDeb=' + $('#HreDeb_' + trnb).val() + '&HreFin=' + $('#HreFin_' + trnb).val() +
        '&idcam=' + trnb;
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
	function postRHDay(trnb) {
	  var urlName = "/tablettes_san/postrh?HreDeb=" + $('#HreDeb_' + trnb).val() + '&HreFin=' + $('#HreFin_' + trnb).val() +
		"&HreDeb_extra=" + $('#HreDeb_extra_' + trnb).val() + '&HreFin_extra=' + $('#HreFin_extra_' + trnb).val() +
		"&TypeExtra=" + $('#TypeExtra_' + trnb).val() + '&idrh=' + trnb;
		$.ajax({
			async: false,
			type: "POST",
			url: urlName
	  }).done(function (msg) {
			var objData = JSON.parse(msg);

		});
	}
	function postRHDayBroker(trnb) {
	  var montant = $('#montant_' + trnb).val();
	  if (montant == '') montant = '0';
	  var urlName = "/tablettes_san/postrhbroker?montant=" + montant + '&idrh=' + trnb;
		$.ajax({
			async: false,
			type: "POST",
			url: urlName
	  }).done(function (msg) {
		var objData = JSON.parse(msg);
		});
	}
    function postVilleDay(trnb) {
      var urlName = "/tablettes_san/postville?HreDeb=" + $('#HreDeb_' + trnb).val() + '&HreFin=' + $('#HreFin_' + trnb).val() +
        "&nbBac=" + $('#nbBac_' + trnb).val() + '&id=' + trnb;
	    $.ajax({
	    	async: false,
	    	type: "POST",
	    	url: urlName
      }).done(function (msg) {
	    	var objData = JSON.parse(msg);

	    });
    }
	function postDepDay(trnb) {
	  var boite = (($("#Boite_" + trnb).val() != '') ? $("#Boite_" + trnb).val() : 'none');
	  var istransbo = (($("#chkTransbo_" + trnb).is(':checked')) ? 1 : 0);
	  var urlName = "/tablettes_san/postdep?Tonnage=" + $('#Tonnage_' + trnb).val() + '&istransbo=' + istransbo +
		'&boite=' + boite +
		'&debut=' + $('#debut_' + trnb).val() + '&fin=' + $('#fin_' + trnb).val() +
		'&Depotoire=' + $('#Depotoire_' + trnb).val() + '&iddeb=' + trnb;
		$.ajax({
			async: false,
			type: "POST",
			url: urlName
	  }).done(function (msg) {
			var objData = JSON.parse(msg);

		});
	}
	function deleteSanivanDay(nocamion) {
	  if (confirm("Voulez-vous supprimer les donn\u00e9es de ce camion pour cette journ\u00e9e?")) {
		var urlName = "/tablettes_san/deletesanivanday?nocamion=" + nocamion + "&date=" + $("#dtedeb").val();
		alert(urlName);
        $.ajax({
		  async: false,
		  type: "POST",
		  url: urlName
		}).done(function (msg) {
		  location.reload();
		});
	  }
	}
  </script>
</head>
<body class="hold-transition skin-blue">

  <!-- Header Navbar: style can be found in header.less -->

  <div class="wrapper" style="margin-top: 5%;">
    <div class="row">
      <div class="col-md-12" id="myNavbar">
        <ul class="nav navbar-nav form-inline">
          <li><label  style="font-size:24px;font-weight:600;">Validation San.</label> </li>
          <li> &nbsp;&nbsp;</li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>DU&nbsp;&nbsp; </label>&nbsp;&nbsp;
          </li>
          <li>
            <div class="form-group">

              <input type="date" class="form-control" onchange="LoadSaniRH('0')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ; ?>" id="dtedeb" />

            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <button type="button" onclick="addChaufOnRoad()" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Cliquer pour ajouter un chauffeur" title="AJOUT" class="btn btn-success" style="height: 33px;width:33px">
              <i class="fa fa-plus" style="margin-left: -5px"></i>
            </button>
          </li>
          <li>

            <button type="button" onclick="PrintRapSani()" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Cliquer pour exporter dans EXCEL" title="EXCEL" class="btn btn-default" style="height: 34px; width: 34px">
              <img src="/assets/img/excel.png" width="30" style="margin-left: -10px;margin-top:-5px" />
            </button>
          </li>

          <li id="liRapJour">
            <button type="button" class="btn btn-default" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Cliquer pour afficher le module de Rapport" title="Module de RAPPORT" onclick="ShowHideDivRap('divRapJour', 'liRapJour')" style="height: 34px; width: 34px;">
              <i class="fa fa-bar-chart" style="margin-left: -5px"></i>
            </button>
          </li>
          <li>
            <select class="form-control" onchange="createDayRH(this.value)" style="width: 180px;">
              <option value="0">Choisir Chauffeur </option>
              <?php echo ($optChauffeurs ?? '') ?>
            </select>
          </li>
        </ul>

      </div> 
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
            <th align="center">TRANSPORT</th>
            <th align="center">COLLECTE</th>
            <th align="center">DISPOSITION</th>
            <th align="center" style="background-color:yellow;">REVENU</th>
            <th align="center" style="background-color:yellow;">REV. NET</th>
            <th>
              <button type="button" class="btn btn-primary" onclick="addSuivis()">
                <i class='fa fa-road' style='font-size:18px;'>Routes</i>&nbsp;
              </button>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php echo ($rapdaychauf_cam ?? '') ; ?>
        </tbody>
      </table>
      <div style="margin-left: 20px">
        <table class="table table-bordered">

          <thead>
            <tr><th colspan="16"><label class="titrenb">RAPPORT DE LA JOURNÉE</label></th></tr>
            <tr style="text-align:right;color:darkblue;font-weight:bold;">
              <th style="text-align:center">HRES CHAUF</th>
              <th style="text-align:center">$ CHAUF</th>
              <th style="text-align:center">HRES AIDE</th>
              <th style="text-align:center">$ AIDE</th>
              <th style="text-align:center">HRES CAM</th>
              <th style="text-align:center">FUEL</th>
              <th style="text-align:center">$ FUEL</th>
              <th style="text-align:center">TRANSPORT</th>
              <th style="text-align:center">COLLECTE</th>
              <th style="text-align:center">DISPOSITION</th>
              <th style="text-align:center">$ DISPO</th>
              <th style="text-align:center">NB BACS</th>
              <th style="text-align:center;background-color:#FFF3E0;">$ BROKER</th>
              <th style="text-align:center">REVENU BRUT</th>
              <th style="text-align:center">REVENU NET</th>
              <th style="text-align:center">REV. / HRES</th>
            </tr>
          </thead>
          <tbody>
            <?php echo ($raprojoursani ?? ''); ?>
          </tbody>
        </table>
      </div>

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
    <script src="/assets/js/sanivan.js"></script>
    <script src="/assets/js/loadfile.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
  var ActiveRow = 0;
  var ActiveRowPrix = 0;
  function PrintRapSani() {
    var urlName = '/rapport/sani/excel/' + ActiveDate;
    document.location = urlName;
  }
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
  function postValCam(value, id) {
    var urlName = '/tablettes_san/postinfosani/' + value + '/' + id +
      '?idinfoville=' + $("#idinfoville").val() +
      '&idaide=' + $("#HELPERS_" + id.substr(id.length - 1)).val();
    $.ajax({
      async: false,
      type: "POST",
      url: urlName
    }).done(function (msg) {
      //--------------
      //if (msg == 'OK') {
      //	alert('Informations enregistrees');
      //}
    });
  }

  </script>
</body>
</html>



















