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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
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
    .table-bordered > tbody > tr > td.no-line {
      border:none !important;
      border-right: none !important;
      border-left: none !important;
      border-top: none !important;
      border-right-style: none !important;
      border-left-style: none !important;
      }
    .titreclient{
      color: darkblue;
      width: 100%;
      height:100%;
      font-size: larger;
      font-weight:900;
    }
    .Actifclient{
      color: white;
      height:35px;
      background-color:yellowgreen;
    }
    .client{
      color: black;
      background-color:white;
    }
    .Actifcommande{
      color: black;
      height:35px;
      background-color:yellow;
    }
    .commande{
      color: black;
      background-color:white;
    }
.s0 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s1 {
 font-family: Arial;
 font-size: 13px;
 color: #000080; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Top;
}
.s2 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s3 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s4 {
 font-family: Arial;
 font-size: 1px;
 color: #000000; font-style: normal;
 background-color: transparent;
 border-left-width: 0px;
 border-right-width: 0px;
 border-top-color:#000000; border-top-style: solid; border-top-width: 1px;
 border-bottom-width: 0px;
 text-align: Left; vertical-align: Top;
}
.s5 {
 font-family: Arial;
 font-size: 13px;
 color: #000080; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s6 {
 font-family: Arial;
 font-size: 16px;
 color: #800000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Top;
}
.s7 {
 font-family: Arial;
 font-size: 1px;
 color: #000000; font-style: normal;
 background-color: transparent;
 border-left-color:#000000; border-left-style: solid; border-left-width: 1px;
 border-right-width: 0px;
 border-top-width: 0px;
 border-bottom-width: 0px;
 text-align: Left; vertical-align: Top;
}
.s8 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Middle;
}
.s9 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 border-left-color:#000000; border-left-style: solid; border-left-width: 1px;
 border-right-color:#000000; border-right-style: solid; border-right-width: 1px;
 border-top-color:#000000; border-top-style: solid; border-top-width: 1px;
 border-bottom-color:#000000; border-bottom-style: solid; border-bottom-width: 1px;
 text-align: Left; vertical-align: Top;
}
.s10 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Middle;
}
.s11 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s12 {
 font-family: Arial;
 font-size: 9px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s13 {
 font-family: Arial;
 font-size: 9px;
 color: #800000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Bottom;
}
.s14 {
 font-family: Arial;
 font-size: 13px;
 color: #800000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Middle;
}
.s15 {
 font-family: Arial;
 font-size: 11px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Middle;
}
.s16 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Middle;
}
.s17 {
 font-family: Arial;
 font-size: 16px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s18 {
 font-family: Arial;
 font-size: 13px;
 color: #800000; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s19 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Top;
}
.s20 {
 font-family: Arial;
 font-size: 16px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Right; vertical-align: Top;
}
.s21 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: #FFFF00;
 border-left-color:#000000; border-left-style: solid; border-left-width: 1px;
 border-right-color:#000000; border-right-style: solid; border-right-width: 1px;
 border-top-color:#000000; border-top-style: solid; border-top-width: 1px;
 border-bottom-color:#000000; border-bottom-style: solid; border-bottom-width: 1px;
 text-align: Center; vertical-align: Top;
}
.s22 {
 font-family: Arial;
 font-size: 13px;
 color: #800000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Top;
}
.s23 {
 font-family: Arial;
 font-size: 16px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Right; vertical-align: Top;
}
.s24 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Right; vertical-align: Top;
}
.s25 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Right; vertical-align: Top;
}
.s26 {
 font-family: Arial;
 font-size: 16px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Right; vertical-align: Top;
}
.s27 {
 font-family: Arial;
 font-size: 1px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
    .progress {cursor: progress;}
    .default {cursor: default;}
  </style>
  <script src="/assets/css/fontawesome/all.css"></script>
  <script type='text/javascript'><?php echo ci3_url()?><?php echo ($ActiveDate ?? '') ?></script>
</head>
<body bgcolor="#FFFFFF" text="#000000"  onload="window.print()">

  <div class="wrapper" id="divContainer">


    <div class="row" >
      <div class="col-lg-12  box box-info">
		    <form id="frmCommande">
        <section class="panel panel-featured-left panel-featured-primary">
			    <div class="panel-body">
				    <section class="card">

						    <div class="card-body" style="width:100%;">
                  <div class="col-xs-12 table-responsive">
                    <table class="table">
                      
                      <tbody id="tbodyCom">

                      <tr>
                        <td colspan="1" class="s3"> <h4> COMMANDE<span class="s6"> <?php echo ($id ?? '') ; ?> </span> </h4> 
                          <span class="s1"> SERVICES SANITAIRES  SANITARY SERVICES </span><br />
                          <span class="s3">&Eacute;mail: info@robertdaoustetfils.com</span><br />
                        </td>

                        <td colspan="4" align="center" valign="top">
                          <img src="/assets/img/logo.png" width="194" height="58" alt="" /> <br />
                          <span class="s2">93, rue Cameron, Hudson, Qu&eacute;bec J0P 1H0</span>
                        </td>

                        <td colspan="4" class="s5"><h4>DEMANDE<span class="s6"> <?php echo ($TypeDem ?? '') ; ?> </span> </h4>
                          <span class="s3"> T&eacute;l.: 450 458-4340  Fax: 450 455-4864 </span><br />
                          <span class="s3">Internet: www.robertdaoustetfils.com</span>
                        </td>
                      </tr>
                        <tr>
                          <td>
                            
                          </td>
                        </tr>
                      <tr>
                        <td colspan="2" ><span class="s10">NOM / NAME</span> <span class="s2"><br /> <?php echo ($CLIENT ?? '') ; ?></span></td>
                        <td colspan="3"><span class="s10">T&Eacute;L.:</span> <br /> <span class="s19"> <?php echo ($phone_jour ?? '') ; ?></span>
						<span class="s10">CEL.:</span> <br />  <span class="s19"><?php echo ($phone_soir ?? '') ; ?></span></td>
                        <td colspan="4"><span class="s10">Date livraison / Shipping Date</span><br /> <span class="s2"><?php echo ($date_fact ?? '') ; ?></span></td>
                        
                       
                      </tr>
                      <tr>
                        <td colspan="2" ><span class="s10">ADRESSE / ADDRESS</span> <span class="s2"><br /> <?php echo ($AdresseComplete ?? '') ; ?></span></td>

                        <td colspan="3" ><span class="s10">ADRESSE DE TRANSFERT</span> <span class="s2" style="font-size: 11px"><br /> <?php echo ($AdresseCompleteTransfert ?? '') ; ?></span></td>

                        <td colspan="4"><span class="s14">Date Retour/Return Date</span><br /> <span class="s18"><?php echo ($date_retour ?? '') ; ?></span></td>
                        
                       
                      </tr>
                      <tr>
                        <td>
                          <div class="input-group input-group mb-3">
                            <div class="input-group-text">
                              <label  class="s10">Bo&icirc;te en place</label>&nbsp;&nbsp;<input type="text" style="width:70px" value="<?php echo ($BoiteEnPlace ?? '') ; ?>">
                            </div>
                          </div>
                         </td>
                        <td colspan="7" align="center">
                          <div class="input-group input-group mb-3">
                            <div class="input-group-text"><span class="s10">Type de Camion:&nbsp;</span>
                              <input type="checkbox" <?php echo (($TypeCamion== 2)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" />&nbsp;&nbsp;Indiff&eacute;rent&nbsp;&nbsp;
                              <input type="checkbox" <?php echo (($TypeCamion== 0)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" />&nbsp;&nbsp;10 roues&nbsp;&nbsp;
                              <input type="checkbox" <?php echo (($TypeCamion== 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" />&nbsp;&nbsp;12 roues
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        
                        <td colspan="7" style="line-height:15px" align="center" ><label class="s10">Format contenant / Container size:</label><br />
                        <label class="s10">12 vc&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 0)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">15 vc&nbsp;&nbsp;<input class="form-control"  type="checkbox" <?php echo (($FormatContenant == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">20 vc&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 2)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">25 vc&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 3)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">30 vc&nbsp;&nbsp;<input class="form-control"  type="checkbox" <?php echo (($FormatContenant == 4)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">35 vc&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 5)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">40 vc&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 6)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">42 vc&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 51)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Compacteur combiné&nbsp;&nbsp;<input class="form-control"  type="checkbox" <?php echo (($FormatContenant == 52)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Compacteur&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($FormatContenant == 10)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        
                        </td>
                      </tr>
                        <tr style="line-height:15px">
                          <td style="line-height:15px" colspan="7" align="center"><label class="s22">Le tonnage maximun est de <?php echo ($Tonnage ?? '') ; ?> Tonne(s). Tout exc&eacute;dant sera factur&eacute; aux taux en vigueur.</label></td>
                        </tr>
                      <tr>
                        
                        <td colspan="7" align="center" ><label class="s16">Type de mat&eacute;riaux / Type of materials</label><br />
                        <label class="s10">B&eacute;ton / Concrete&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($IsBeton == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Asphalte / Asphalt&nbsp;&nbsp;<input class="form-control"  type="checkbox" <?php echo (($IsAsphalte == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Terre / Soil&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($IsTerre == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Mat&eacute;riaux secs / Dry materials&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($IsMateriaux == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Branches / Branches&nbsp;&nbsp;<input class="form-control"  type="checkbox" <?php echo (($IsBranches == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label><br />
                        <label class="s10">Fumier / Manure&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($IsFumier == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">D&eacute;chets / Waste&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo (($IsDechets == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Recyclage / Recycling&nbsp;&nbsp;<input class="form-control" type="checkbox" <?php echo ((($Recyclage != '')  or (!isset($Recyclage )))? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        <label class="s10">Bardeaux / Shingles&nbsp;&nbsp;<input class="form-control"  type="checkbox" <?php echo (($IsBardeau == 1)? 'checked' : '' ) ; ?> value="0" id="TypeCamio" name="TypeCamio" ></label>
                        
                        </td>
                      </tr>
                      <tr>
                        <td colspan="5" rowspan="7" ><span class="s10">NOTE</span><br />
                        <span class="form-control" style="font-size:medium;width:500px;height:80px"> <?php echo ($notes ?? '') ; ?></span>
                        <span class="form-control" style="font-size:medium;width:500px;height:80px"> <?php echo ($note_bureau ?? '') ; ?></span></td>
                        <td colspan="1" >Prix: </td>
                        <td colspan="2"><?php echo ($Prix ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">Redevance: </td>
                        <td colspan="2" class="s24"><?php echo ($Redevance ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">Carburant: </td>
                        <td colspan="2" class="s24"><?php echo ($gazeline ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">TPS: </td>
                        <td colspan="2" class="s24"><?php echo ($TPS ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">TVQ: </td>
                        <td colspan="2" class="s24"><?php echo ($TVQ ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">Total: </td>
                        <td colspan="2" class="s24"><?php echo ($Total ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">Montant payé: </td>
                        <td colspan="2" class="s24"><?php echo ($MontPaye ?? '') ; ?></td>
                       
                      </tr>
                      <tr>
                        
                        <td colspan="1" class="s17">Solde: </td>
                        <td colspan="2" class="s24"><?php //echo ($TPS ?? '') ; ?></td>
                       
                      </tr>

                      <tr>
                        <td colspan="4"> <span class="s11">LE CLIENT RECONNA&Icirc;T:</span><br />
                        <span  class="s12">
                          - Qu'il doit nous aviser afin de faire effectuer la lev&eacute;e du contenant.
                          <br />- Le conteneur doit &ecirc;tre accessible &agrave; nos v&eacute;hicules en tout temps.
                          <br />- Assumer le co&ucirc;t si un service de d&eacute;gla&ccedil;age s'av&eacute;rait essentiel.
                          <br />- Devoir r&eacute;partir la charge &agrave; l'int&eacute;rieur du contenant defa&ccedil;on &agrave; ce que le poids
                          <br /> soit r&eacute;parti &eacute;quitablement tout en respectant les limites de charges permises
                          <br /> par le code de s&eacute;curit&eacute; routi&egrave;re.
                          <br />- Devoir payer pour tous dommages autres qu'usage normal, incluant feu, vol
                          <br /> ou autre cause.
                          <br />- Devoir payer 1-1/2% d'int&eacute;r&ecirc;t par mois ou 18% par ann&eacute;e sur comptes
                          <br /> pass&eacute;s d&ucirc;s de plus de 30 jours.
                          <br />- Les prix sont faits pour livraison &agrave; la cha&icirc;ne du trottoir. Nous n'assumons
                          <br /> aucune responsabilit&eacute; pour dommages caus&eacute;s, quand la livraison est faite
                          <br /> en dedans du trottoir.
                          <br />- Il est strictement d&eacute;fendu de jeter pneus et d&eacute;chets toxiques.
                          <br />- L'exc&eacute;dent du tonnage allou&eacute; par conteneur sera factur&eacute; aux taux en vigueur.</span>
                        </td>
                        <td colspan="4"> <span class="s11">THE CLIENT MUST:</span><br />
                        <span  class="s12">
                          - Notify us for the emptying of the container.
                            <br />- Container must be accessible to our vehicules at all times.
                            <br />- Assume the cost of the defrosting service if necessary.
                            <br />- Verify that the weight of the container's content is well
                            <br /> distributed while respecting load limits permitted by
                            <br /> the Road Safety Code.
                            <br />- Pay for repairs other than ordinary wear and tear, including
                            <br /> fire, theft of else.
                            <br />- Pay a monthly 1-1/2% interest or a yearly 18% interest charge
                            <br /> on all 30 days passed due accounts.
                            <br />- All delivered prices are for street curb delivery. We will assume
                            <br /> no responsibility for any damages where delivery is made
                            <br /> inside the curb.
                            <br />-  It is strictly forbidden to throw tires and toxic waste.
                            <br />- Excess tonnage allocated per container will be charged at current rates.</span>
                        </td>
                        
                      </tr>
                      <tr style="height:15px">
                        <td colspan="9" class="s13">
                          * Le client ne disposera dans le contenant aucune mati&egrave;re autre que celle permise, advenant pr&eacute;sence de contaminants
                          <br /> qui exige une manutention sp&eacute;ciale , une surcharge minimale de 250$ sera applicable et ce sans exception.
                          <br />
                          <br />* The customer will have in the container no matter other than that permitted in the event of contaminants
                          <br /> that requires special handling a minimum surcharge of 250$ will be applicable and without exception.
                        </td>
                        <td></td>
                      </tr>
                      
                      </tbody>
                    </table>
                  </div>
        
						    </div>
              	
				    </section>
			    </div>
		    </section>
        </form>
	    </div>
    </div>
</div>


<?php echo view('sections/inc_pop_up_ro');?>
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
  var HaveCom = "<?php echo ($havecom ?? '') ?>"
  $(function () {
  $('#tblboites').DataTable({
    'paging': true,
    'responsive': true,
    "pagingType": "full",
    'lengthChange': true,
    'pageLength': 10,
    'searching': true,
    'ordering': true,
    'info': true,
    'data-page-length': '10',
    'autoWidth': false,
      'dom': "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    "language": {
      "sProcessing": "Traitement en cours...",
      "sSearch": "Rechercher&nbsp;:&nbsp;&nbsp;",
      "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
      "sInfo": "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
      "sInfoEmpty": "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
      "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
      "sInfoPostFix": "",
      "sLoadingRecords": "Chargement en cours...",
      "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
      "sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
      "oPaginate": {
        "sFirst": "<span><b><i class='fa fa-angle-double-left' style='font-size:28px;'></i></b>&nbsp;</span>",
        "sPrevious": "<span><b><i class='fa fa-angle-left' style='font-size:28px;'></i></b>&nbsp;</span>",
        "sNext": "<span><b><i class='fas fa-angle-right' style='font-size:28px;'></i></b>&nbsp;</span>",
        "sLast": "<span><b><i class='fas fa-angle-double-right' style='font-size:28px;'></i></b>&nbsp;</span>"
      }
    }
  })
})   
  //iCheck for checkbox and radio inputs
  $('input[type="checkbox"].minimal, input[type="checkbox"].minimal').iCheck({
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
  //------------------------------------------------------------------------------
 
  //---------------------------------------------------
  AjusteButton('0');
  $('#td_' + $("#idclient").val()).removeClass("client");
  $('#td_' + $("#idclient").val()).addClass("Actifclient");
  $('#tdcom_' + $("#actifcommande").val()).removeClass("Actifcommande");
  $('#tdcom_' + $("#actifcommande").val()).addClass("commande");
  //---------------------------------------------------
</script>
</body>
</html>



















