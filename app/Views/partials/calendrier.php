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
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
  <script src='/assets/fullcalendar/lib/moment.min.js'></script>
  <script src='/assets/fullcalendar/locale-all.js'></script>
  <script src='/assets/fullcalendar/theme-chooser.js'></script>

  <script src='https://unpkg.com/popper.js/dist/umd/popper.min.js'></script>
  <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>
<script>

  document.addEventListener('DOMContentLoaded', function() {
      var initialLocaleCode = 'fr-ca';
      //$('#external-events .fc-event').each(function() {

      //  // store data so the calendar knows to render an event upon drop
      //  $(this).data('event', {
      //    title: $.trim($(this).text()),
      //    idval: $(this).id,
      //    stick: true // maintain when user navigates (see docs on the renderEvent method)
      //  });

      //  // make the event draggable using jQuery UI
      //  $(this).draggable({
      //    zIndex: 10999,
      //    revert: true, // will cause the event to go back to its
      //    revertDuration: 0 //  original position after the drag
      //  });

      //});
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    var containerEl = document.getElementById('external-events');
    var calendarEl = document.getElementById('calendar');
    new Draggable(containerEl, {
      itemSelector: '.fc-event',
      eventData: function(eventEl) {
        return {
          title: eventEl.innerText,
          idact: eventEl.id
        };
      }
    });
      var calendar = new Calendar(calendarEl,{
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
          },
        defaultDate: '<?php  echo ($caldate ?? '');?>',
        locale: initialLocaleCode,
      editable: true,
        weekends: false,
      droppable: true,
      dayMaxEvents: 10,
      events: [
            <?php
            echo ($dataCD ?? '');
            ?>
        ],
      eventClick: function (info) {
        ShowRHModalCal(info.event.extendedProps.idsuivis, '');
      },
        drop: function (info) {
          var idinfo = info.draggedEl.id;
          var nom = info.draggedEl.innerText;
          var dteActive = info.dateStr;
          var arrID = idinfo.split('_');
          if (chkrhexisteCal( arrID[1])) {
            ShowRHModalCal(arrID[1], '');
          }
          else {
            ShowNewRHModalCal(arrID[1], nom, dteActive);
          }
      },
      eventDrop: function(event) {
        ShowRHModalCal(event.id,event.start.format());
        },
      });
    calendar.render();
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
    switch((ci3sess('userdata')['privilege'] ?? null) )
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
<section class="container"  style="margin-left:5px;width:100%">
        <div id="myNavbar" style="margin-top:75px;">
        <ul class="nav navbar-nav form-inline">
          <li><label  style="font-size:24px;font-weight:600;">Calendrier RH:</label> </li>
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
          <li>
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:140px">Type d'absence</label>
                <select id="selStatutRH" class="form-control" style="width:130px;">
                  <?php echo ($typeabsences ?? '') ; ?>
                </select>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
         <li>
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:100px">Type RH</label>
              <select id="selTypeRH" class="form-control" style="width:150px;">
                <option value="">Tous les types</option>
                <?php echo ($updrh ?? '') ; ?>
              </select>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
                <input type="text" id="edtFind" class="form-control" onkeyup="EntersearchTableCalendrierRH(this.value, event)" placeholder="Rechercher" style="width:100px;" />

                <!--<button type="button" class="btn btn-info btn-flat" id="sr" onclick="searchInTableCalendrierRH()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affiche la liste selon le critère.">
                  <i class='fa fa-search' style='font-size:18px;'></i>&nbsp;
                </button>-->
                <!--<button type="button" class="btn btn-danger btn-flat" onclick="ClearsearchTableCalendrierRH()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affacer la recherche.">
                  <i class='fa fa-remove' style='font-size:18px;'></i>&nbsp;
                </button>-->
              </span>
            </div>
          </li>
          <li>
            <button type="button" onclick="LoadCalendrierRH()" class="btn btn-danger">
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
        <div  style="position:absolute;left: 0px;height: 800px; width: 270px;">
              <select class="form-control" onchange="getRhToDropCal(this.value)" style="width:200px"  name="typechauf" id="typechauf" required>
                <option value="">Choisir</option>
                    <?php
                    echo ($typerh ?? '');
                    ?>
            <div class="input-group">
              <span class="input-group-btn">
                <input type="text" id="edtFindDrop" onkeyup="EntersearchTableCalendrierDropRH(this.value, event)" class="form-control"  placeholder="Rechercher" style="width:150px;" />

                <button type="button" class="btn btn-info btn-flat" id="sr" onclick="searchCalendrierRHCal()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affiche la liste selon le critère.">
                  <i class='fa fa-search' style='font-size:18px;'></i>&nbsp;
                </button>
                <button type="button" class="btn btn-danger btn-flat" onclick="ClearsearchCalendrierRHCal()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Affacer la recherche.">
                  <i class='fa fa-remove' style='font-size:18px;'></i>&nbsp;
                </button>
              </span>
            </div>    
            <div id='external-events' class="box-body" style="padding-left:7px; height: 700px;overflow:auto">
              <?php
              echo ($ChauffeurDrop ?? '');
              ?>

          </div>          

        </div>
  <div id='calendar' style="margin-top:20px;margin-left:280px; width: 70vw"></div>
<div class="modal fade" id="modalrhcal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Absence Ressource Humaine&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:570px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Resources humaines</label> <br />
                            <select class="form-control" name="selRHCal"Cal id="selRHCal" required>
                                <option value="">Choisir</option>
                                <?php echo ($employes ?? '') ; ?>
                            </select>
                            <input id="idsuivisrh" type="hidden" />
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date début de l'absence </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value=""name="dtestartrhCal" id="dtestartrhCal" />
                        </div>      
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendrhCal" id="dteendrhCal" />
                        </div>   
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type d'absence</label> <br />
                            <select class="form-control" name="selRHabsenceCal" id="selRHabsenceCal" required>
                                <option value="">Choisir</option>
                                <?php echo ($typeabsences ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />  
                        
                        <div class="col-sm-7 col-xs-12" style="margin-top:10px" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="3" cols="50" id="noterhCal" ></textarea>
                        </div> 
                      <br /> <br />
                      <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">&nbsp;&nbsp;&nbsp; </label> <br />
                           
                        </div> 
                      <br /> <br />
                        <div class="col-sm-12 col-xs-12" >
                          <label style="font-weight: bold; ">Avis pour retour</label>
                        <br />
                          <div class="btn-group"><label style="font-weight: bold;color:darkred; ">
                          <input id="showavisCal" type="checkbox"  /> Afficher l'avis de retour</label><br /></div>  
                           <div class="btn-group"> <textarea rows="3" cols="50" id="avistoshowCal" ></textarea>
                        </div>   
                      </div>  </div> 
				          </div>
					  </form>
            </div>
			      <div class="modal-footer">
                <button type="button" onclick="UpdRetourRHCal()" id="btnEndRHCal" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:<?php echo (((ci3sess('userdata')['privilege'] ?? null) == 5)? 'bloc' : 'none') ?>;">Retour immédiat</button>
                <button type="button" onclick="PostSuivisRHCal()" class="btn btn-primary" data-dismiss="modal" style="display:<?php echo (((ci3sess('userdata')['privilege'] ?? null) == 5)? 'bloc' : 'none') ?>;margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalnewrhcal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Absence Ressource Humaine&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:570px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Resources humaines</label> <br />
                            <input type="text" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"   value=""name="nomCal" id="nomCal" disabled />
                            <input id="idrhnew" type="hidden" value=""/>
                            <input id="idsuivisnewrh" type="hidden" value="0"/>
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date début de l'absence </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value=""name="dtestartrhnewCal" id="dtestartrhnewCal" />
                        </div>      
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendrhnewCal" id="dteendrhnewCal" />
                        </div>   
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type d'absence</label> <br />
                            <select class="form-control" name="selnewRHabsenceCal" id="selnewRHabsenceCal" required>
                                <option value="">Choisir</option>
                                <?php echo ($typeabsences ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />  
                        
                        <div class="col-sm-7 col-xs-12" style="margin-top:10px" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="3" cols="50" id="noterhnewCal" ></textarea>
                        </div> 
                      <br /> <br />
                      <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">&nbsp;&nbsp;&nbsp; </label> <br />
                           
                        </div> 
                      <br /> <br />
                        <div class="col-sm-12 col-xs-12" >
                          <label style="font-weight: bold; ">Avis pour retour</label>
                        <br />
                          <div class="btn-group"><label style="font-weight: bold;color:darkred; ">
                          <input id="showavisnewCal" type="checkbox"  /> Afficher l'avis de retour</label><br /></div>  
                           <div class="btn-group"> <textarea rows="3" cols="50" id="avistoshownewCal" ></textarea>
                        </div>   
                      </div>  </div> 
				          </div>
					  </form>
            </div>
			      <div class="modal-footer">
                <button type="button" onclick="PostSuivisnewRHCal()" class="btn btn-primary" data-dismiss="modal" style="display:<?php echo (((ci3sess('userdata')['privilege'] ?? null) == 5)? 'bloc' : 'none') ?>;margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
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
//----------------------------------
  function searchCalendrierRHCal() {
    //var urlName = "/sanivan/findrh?valtofinddrop=" + $("#edtFindDrop").val() + "&dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() + '&typerhdrop=' + $("#selTypeRHsearchDrop").val() +
		  //"&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val();
    //document.location = urlName;
  	var urlName =  "/sanivan/findrh?valtofinddrop=" + $("#edtFindDrop").val() + "&typechauf=" +  $("#typechauf").val();
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
      $("#external-events").html(msg);
		});
  }
function EntersearchCalendrierDropRH(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName =  "/sanivan/findrh?valtofinddrop=" + value;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
      $("#external-events").html(msg);
		});
	}
}
function EntersearchCalendrierRH(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName =  "/sanivan/findrh?" + value;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
      $("#divDropRH").html(msg);
		});
	}
}
function ClearsearchTableCalendrierRH() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(msg);
		}
	});
    }
    function chkrhexisteCal( idrh ) {
	    //-----------------------------------------
	    var urlName = "/sanivan/chkrhexist/" + idrh;
	    $.ajax({
		    async: true,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
        if (msg != 0) {
          alert(msg);
          return false;
		    }
        else {
          return true;
        }
	    });
    }
    function getRhToDropCal(value) {
      //--------------
      var urlName = "/sanivan/findrh?valtofinddrop=&typechauf=" + value;
	    $.ajax({
		    async: false,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
			    $("#external-events").html(msg);
	    });
    }
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
        document.location = "/sanivan/calrhpdf?dtedeb=" + $("#dtePrintFrom").val() + "&valtofind=" + $("#edtFind").val() + 
		                    "&from=1&dtefin=" + $("#dtePrintTo").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val()+
                          '&printwhere=' + $('input[name="radPrint"]:checked').val();
      }
      else {
        var urlName = "/sanivan/calpdf" + '?dtedeb=' + $("#dtePrintFrom").val() + "&dtefin=" + $("#dtePrintTo").val() +
          '&printwhere=' + $('input[name="radPrint"]:checked').val() + "&valtofind=" + $("#edtFind").val() +
		      "&statut=" + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
        window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
      }
    }
    function UpdRetourRHCal() {
	    alert(1);
	    var currentdate = new Date();
	    var mois = currentdate.getMonth() + 1;
	    var annee = currentdate.getFullYear();
	    var actived = annee + '-' + mois + '-' + currentdate.getDate();
	    alert(actived);
	    var urlName = "/sanivan/retourrh/" + $('#idsuivisrh').val() + '/' + actived;
	    alert(urlName);
	    $.ajax({
		    async: false,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
		    var urlName = "/sanivan/calrh?dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() + '&typerhdrop=' + $("#selTypeRHsearchDrop").val() +
			    "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val();
		    document.location = urlName;
	    });
    }
  </script>
</body>
</html>
