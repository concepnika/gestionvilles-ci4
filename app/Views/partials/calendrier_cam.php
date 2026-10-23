<?php

?>
<!DOCTYPE html>
<html lang="<?php echo ci3sess('site_lang');?>" xml:lang="<?php echo ci3sess('site_lang');?>" xmlns="http://www.w3.org/1999/xhtml">

<head>


<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" lang="fr" content=" ">
  <title>
    Robert Daoust
  </title>



  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap">    
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans|Open+Sans+Condensed:300,700">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" integrity="sha256-PHcOkPmOshsMBC+vtJdVr5Mwb7r0LkSVJPlPrp/IMpU=" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw==" crossorigin="anonymous" />
  <link rel="stylesheet" href="/assets/css/style.css">   
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
  <script src='/assets/fullcalendar/lib/moment.min.js'></script>
  <script src='/assets/fullcalendar/locale-all.js'></script>
  <script src='/assets/fullcalendar/theme-chooser.js'></script>
  
  <script src='https://unpkg.com/popper.js/dist/umd/popper.min.js'></script>
  <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    var containerEl = document.getElementById('external-events');
    var calendarEl = document.getElementById('calendar');
    var initialLocaleCode = 'fr-ca';
    // initialize the external events
    // -----------------------------------------------------------------

    new Draggable(containerEl, {
      itemSelector: '.fc-event',
      eventData: function(eventEl) {
        return {
          title: eventEl.innerText,
          idact: eventEl.id
        };
      }
    });
  var calendar = new Calendar(calendarEl, {
          header: {
            center: 'title,prev,next today,month,agendaWeek,agendaDay'
          },
        defaultDate: '<?php  echo ($caldate ?? '');?>',
        locale: initialLocaleCode,
      editable: true,
        weekends: true,
    selectable: true,
      dayMaxEvents: 10,
      events: [
            <?php
            echo ($dataCD ?? '');
            ?>
      ],
      eventClick: function (info) {
        ShowCamModalCal(info.event.extendedProps.idsuivis,'cam','statut','date','type');
        info.el.style.borderColor = 'red';
        },
      dateClick: function(info) {
        //alert('clicked ' + info.dateStr);
    },
      droppable: true, // this allows things to be dropped onto the calendar
    drop: function (info) {
      var infocam = info.draggedEl.id;
      var arrCam = infocam.split('_');
      ShowCamModalCal(0,arrCam[1], arrCam[2], info.dateStr, arrCam[3]);
      }
    });
    calendar.render();
  });
	$(document).ready(function() {
		var cur_stus;
		$('#accordion dd').hide();
		$('#accordion dt').attr('stus', '');
	
		$('#accordion dt').click(function(){
			cur_stus = $(this).attr('stus');
			if(cur_stus != "active")
			{
				//reset everthing - content and attribute
				$('#accordion dd').slideUp();
				$('#accordion dt').attr('stus', '');
				$('#accordion dt').removeClass('active');
				
				//then open the clicked data
				$(this).next().slideDown();
				$(this).attr('stus', 'active');
				$(this).addClass('active');
			}
			//Remove else part if do not want to close the current opened data
			else
			{
				$(this).next().slideUp();
				$(this).attr('stus', '');
				$(this).removeClass('active');
			}
			return false;
		});
	});
</script>
<style>
  .card {
    position: relative;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: lightgray;
    background-clip: border-box;
    border: 2px solid red;
    border-radius: 0.25rem;
}

    .card > hr {
        margin-right: 0;
        margin-left: 0;
    }

    .card > .list-group {
        border-top: inherit;
        border-bottom: inherit;
    }

        .card > .list-group:first-child {
            border-top-width: 0;
            border-top-left-radius: calc(0.25rem - 0);
            border-top-right-radius: calc(0.25rem - 0);
        }

        .card > .list-group:last-child {
            border-bottom-width: 0;
            border-bottom-right-radius: calc(0.25rem - 0);
            border-bottom-left-radius: calc(0.25rem - 0);
        }

        .card > .card-header + .list-group,
        .card > .list-group + .card-footer {
            border-top: 0;
        }

.card-body {
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1.25rem;
}

.card-title {
    margin-bottom: 0.75rem;
    background-color: darkred;
    color:whitesmoke;
}

.card-subtitle {
    margin-top: -0.375rem;
    margin-bottom: 0;
}

.card-text:last-child {
    margin-bottom: 0;
}

.card-link:hover {
    text-decoration: none;
}

.card-link + .card-link {
    margin-left: 1.25rem;
}

.card-header {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 0 solid rgba(0, 0, 0, 0.125);
}

    .card-header:first-child {
        border-radius: calc(0.25rem - 0) calc(0.25rem - 0) 0 0;
    }

.card-footer {
    padding: 0.75rem 1.25rem;
    background-color: rgba(0, 0, 0, 0.03);
    border-top: 0 solid rgba(0, 0, 0, 0.125);
}

    .card-footer:last-child {
        border-radius: 0 0 calc(0.25rem - 0) calc(0.25rem - 0);
    }

.card-header-tabs {
    margin-right: -0.625rem;
    margin-bottom: -0.75rem;
    margin-left: -0.625rem;
    border-bottom: 0;
}

.card-header-pills {
    margin-right: -0.625rem;
    margin-left: -0.625rem;
}

.card-img-overlay {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    padding: 1.25rem;
    border-radius: calc(0.25rem - 0);
}

.card-img,
.card-img-top,
.card-img-bottom {
    -webkit-flex-shrink: 0;
    -ms-flex-negative: 0;
    flex-shrink: 0;
    width: 100%;
}

.card-img,
.card-img-top {
    border-top-left-radius: calc(0.25rem - 0);
    border-top-right-radius: calc(0.25rem - 0);
}

.card-img,
.card-img-bottom {
    border-bottom-right-radius: calc(0.25rem - 0);
    border-bottom-left-radius: calc(0.25rem - 0);
}

.card-deck .card {
    margin-bottom: 7.5px;
}

  #calendar {
    width: 100%;
    margin: 0 auto;
  }

</style>
</head>
<body id="page">
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <!-- Sidebar toggle button-->
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>

    <?php 
      switch(ci3sess('privilege') )
      {
        case 1:
          echo view('sections/menu_supersuser');
          break;
        case 2:
          echo view('sections/menu_superviseur');
          break;
        case 3:
          echo view('sections/menu_repartition');
          break;
        case 7:
          echo view('sections/menu_garage');
          break;
        default:
          echo view('sections/menu');
      }

    ?>
  </nav>
<section class="container main" style="margin-left:5px;width:100%">
         <div id="myNavbar" style="margin-top:75px;">
        <ul class="nav navbar-nav form-inline">
          <li><label  style="font-size:24px;font-weight:600;">Calendrier des camions:&nbsp;</label> </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>DE&nbsp;&nbsp; </label>&nbsp;&nbsp;
          </li>
          <li>
            <input type="date" class="form-control" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ; ?>" id="dtedeb" />
          </li>
          <li>&nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>À&nbsp;&nbsp;</label>&nbsp;
          </li>
          <li>
            <input type="date" class="form-control" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($dateend ?? '') ; ?>" id="dtefin" />
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:120px">Type Statut</label>
              <select id="selStatutCam" class="form-control" style="width:150px;">
                <option <?php echo (($statut == 'all')? 'selected' : '' )  ; ?> value="all">Tous les types</option>
                <option <?php echo (($statut == '2')? 'selected' : '' )  ; ?>  value="2">Avec Matériel</option>
                <option <?php echo (($statut == '1')? 'selected' : '' )  ; ?> value="1">Hors Service</option>
              </select>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:120px">Type camion</label>
              <select id="selTypeCam" class="form-control" style="width:150px;">
                <option <?php echo (($typecam == 'all')? 'selected' : '' )  ; ?> value="all">Tous les types</option>
                <option <?php echo (($typecam == '1')? 'selected' : '' )  ; ?>  value="1">REAR END</option>
                <option <?php echo (($typecam == '2')? 'selected' : '' )  ; ?> value="2">ROBOT</option>
              </select>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
                <input type="text" id="edtFind" class="form-control"  placeholder="#Camion" style="width:100px;" />
              </span>
            </div>
          </li>
          <li>
            <button type="button" onclick="LoadCalendrierCam()" class="btn btn-danger">
              <i class="fa fa-refresh"></i>
            </button>
          </li>
          <li>
            <button type="button" onclick="printCalendrier()" class="btn btn-primary">
                  <i class="fa fa-print"></i>
            </button>
          </li>
        </ul>

      </div> 
 

</section>
        <div  id='external-events'  style="position:absolute;left: 0px;display: block;height: 1000px; width: 230px">

          <div id="divcam">
            <div class="box-header with-border">
            <h4>Camions à draguer</h4>
              <select class="form-control" style="width:200px" id="typecam" onchange="getCamForTypeCal(this.value)">
                <option value="">Choisir</option>
                  <?php
                  echo ($updcamion ?? '');
                  ?>
            </div>
          <div class="box box-solid" style="overflow: scroll; height: 600px; width: 200px;margin-top:10px;">
            <div id="divDropCamion" class="box-body" style="padding-left:7px;">

                <?php
                echo ($CamionDrop ?? '');
                ?>

            </div>
            </div>
          </div>

        </div>
  <div id='calendar' style="left:235px;width: 85vw;overflow:scroll;height: 100%;position:absolute;"></div>
  <div class="modal fade" id="printCalendrier" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header btn-default">
       <input type="hidden" id="empl_poste" />
        <h3 class="modal-title" id="plTitreMsg" style="color:black;text-align:center;"><img src="/assets/img/logo.png" width="200" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Impression du calendrier</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>


      </div>
      <div class="modal-body" style="min-height:100px;">
        <table align="center" border="0" cellspacing="0" cellpadding="10" width="400" class="table-bordered">
          <tr>
            <td style="width: 150px;" align="left">
              <h4>Date du</h4>
              <input type="date"  class="form-control" style="width:200px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtePrintFrom" />


            </td>

            <td style="width: 150px;" align="left">
              <h4>Date au</h4>
              <input type="date"  class="form-control" style="width:200px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtePrintTo" />
 
            </td>
          </tr>
          <tr>
            <td colspan="2" style="width: 400px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;</td>
          </tr>
          <tr style="display:none;">
            <td colspan="2" style="width: 400px;" align="center"><label><input type="radio" checked   name="radPrint" value="1" />&nbsp;&nbsp;Exporter PDF</label>&nbsp;&nbsp;<label><input type="radio"  name="radPrint" value="2"  />&nbsp;&nbsp;Imprimer HTML</label></td>
          </tr>
        </table>


      </div>
      <div class="modal-footer">
        <button type="button" onclick="printCal()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;"><i class="glyphicon glyphicon-print"></i>&nbsp;&nbsp;Exécuter</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
    </div>
  </div>
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
														<h3 id="titrecam"></h3>
                          <select class="form-control" onchange="getTypeCamPopupCal(this.value, 2)" id="selTypeCamMat">
                            <option value="1" selected>REAR END</option>
                              <option value="2">ROBOT</option>
                           </select>
                            <select class="form-control" name="camion" id="camion" disabled>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                            <input id="idsuiviscam" type="hidden" />
                        </div>     
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Date avec matériel </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestart" id="dtestart" />
                        </div>   
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Type de matériel</label> <br />
                            <select class="form-control" name="typemat" id="typemat" required>
                                <option value="">Choisir</option>
                                <?php echo ($typededechet ?? '') ; ?>
                            </select>
                        </div>
                        <div class="col-sm-7 col-xs-12"  style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="4" cols="50"  id="notemat"></textarea>
                        </div>    
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="UpdViderMatCal()" id="btnEndMat" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:<?php echo ((ci3sess('privilege') == 5)? 'bloc' : 'none') ?>;"></button>
                <button type="button" onclick="PostSuivisCamMatCal()" class="btn btn-primary" data-dismiss="modal" style="display:<?php echo ((ci3sess('privilege') == 5)? 'bloc' : 'none') ?>;margin:4px 5px 0 0;">Enregistrer</button>
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
                           <select class="form-control" onchange="getTypeCamPopupCal(this.value, 1)" id="selTypeCamHS">
                            <option value="1" selected >REAR END</option>
                              <option value="2">ROBOT</option>
                           </select>
                            <select class="form-control" name="camionhs" id="camionhs" disabled>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date hors service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestarths" id="dtestarths" />
                        </div>     
                           <br /> <br />    
                        
                        <div class="col-sm-7 col-xs-12" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="4" cols="50" id="notehs" > 

                            </textarea>
                        </div>    
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="UpdRetourCal()" id="btnEndHS" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:<?php echo ((ci3sess('privilege') == 5)? 'bloc' : 'none') ?>;">Retour immédiat</button>
                <button type="button" onclick="PostSuivisCamHSCal()" class="btn btn-primary" data-dismiss="modal" style="display:<?php echo ((ci3sess('privilege') == 5)? 'bloc' : 'none') ?>;margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalbris" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Camion Bris de service&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:500px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; "># Camions  </label> <br />
                           <select class="form-control" onchange="getTypeCamPopupCal(this.value, 1)" id="selTypeCamBris">
                              <option value="1" selected >FRONT LOAD</option>
                              <option value="2">MOLOK</option>
                              <option value="3">REAR LOAD</option>
                           </select>
                            <select class="form-control" name="camionBris" id="camionBris" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date et heure en bris de service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestarBris" id="dtestartBris" />
                           <input type="time" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="hrestartBris" id="hrestartBris" />
                        </div>     
                           <br /> <br />    
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date et heure retour en service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendBris" id="dteendBris" />
                           <input type="time" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="hreendBris" id="hreendBris" />
                        </div>                            
                        <div class="col-sm-7 col-xs-12" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="4" cols="50" id="noteBris" > 

                            </textarea>
                        </div>    
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="UpdRetourHome()"  class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:<?php echo ((ci3sess('privilege') == 5)? 'bloc' : 'none') ?>;">Retour immédiat</button>
                <button type="button" onclick="PostSuivisCamBris()" class="btn btn-primary" data-dismiss="modal" style="display:<?php echo ((ci3sess('privilege') == 5)? 'bloc' : 'none') ?>;margin:4px 5px 0 0;">Enregistrer</button>
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
    function printCalendrier() {
      var d = new Date();
      var strDate = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
      $("#dtePrintFrom").val(strDate);
      $("#dtePrintTo").val(strDate);
      $("#printCalendrier").modal("show");
    }
    function printCal() {
      var printwhere = $('input[name="radPrint"]:checked').val();
      if (printwhere == '1') {
        document.location = "/sanivan/calpdf" + '?dtedeb=' + $("#dtePrintFrom").val() + "&dtefin=" + $("#dtePrintTo").val() +
          '&printwhere=' + $('input[name="radPrint"]:checked').val() + "&valtofind=" + $("#edtFind").val() +
		      "&from=1&statut=" + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
      }
      else {
        var urlName = "/sanivan/calpdf" + '?dtedeb=' + $("#dtePrintFrom").val() + "&dtefin=" + $("#dtePrintTo").val() +
          '&printwhere=' + $('input[name="radPrint"]:checked').val() + "&valtofind=" + $("#edtFind").val() +
		      "&statut=" + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
        window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
      }
    }
  </script>
</body>
</html>
