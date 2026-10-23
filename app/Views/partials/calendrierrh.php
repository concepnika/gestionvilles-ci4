<?php


?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enviro Connexion Laval</title>


  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
  <link rel="stylesheet" href="/assets/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="/assets/css/nikaadmin.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="/assets/summernote/summernote-bs4.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw==" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">    
  <script src="/assets/js/modernizr.custom.js"></script>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <style>
    .item .photo {padding-top:30px;}
    .item .photo img {width:100%; border-radius:100%; height: 130px; opacity: 1; -webkit-transition: opacity 1.5s ease-in-out; -moz-transition: opacity 1.5s ease-in-out; transition: opacity 1.5s ease-in-out;}
    .item .photo img:hover {opacity: 0.4; -webkit-transition: opacity .5s ease-in-out; -moz-transition: opacity .5s ease-in-out; transition: opacity .5s ease-in-out;}
    .item .photo .fa {position:absolute; font-size:30px; top:130px; right:34px; color: #111;}
    .item .description {font-size:14px; color:#666; line-height: 24px; padding:0 30px 10px 10px;}
    .item .description h2 {font-size:16px; color:#666; line-height: 24px;}
    .item .description .avantages {font-weight:bold; color: #000; border-top:1px solid #333; margin:15px 0; padding:15px 0 15px 0;}
    .item .description .avantages .mot {font-weight:bold; color:#C00;}
    .navadmin {
      font-size: large;
        }
    .activemenu {
      font-size: x-large;
      font-weight:bold;
      color: #FC0;
        }
  </style>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.js" integrity="sha256-399DNRyfIpWIy1ZV0KmEIIuIQ5sBHdLu9MBtVHrNtt8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js" integrity="sha256-NXRS8qVcmZ3dOv3LziwznUHPegFhPZ1F/4inU7uC8h0=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous"></script>
  <script src="/assets/js/main.js" type="text/javascript"></script>
  <script src="/assets/js/toucheffects.js"></script>


  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
  <script src='/assets/fullcalendar/lib/moment.min.js'></script>
  <script src="/assets/fullcalendar/locale-all.js" type="text/javascript"></script>
   <script src='/assets/fullcalendar/theme-chooser.js'></script>
  <script>
    var base_url = '<?php echo ($base_url ?? ''); ?>';
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

    // initialize the calendar
    // -----------------------------------------------------------------

    var calendar = new Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      buttonText:{
        today:    'Aujourd\'hui',
        month:    'Mois',
        week:     'Semaine',
        day:      'Jour',
        list:     'Liste'
      },
            events: [
          <?php
          echo ($dataCD ?? '');
          ?>
      ],
      defaultDate: '<?php echo ($ActiveDate ?? ''); ?>',
      locale: initialLocaleCode,
      weekends: false,
      eventClick: function (info) {
        //var idactivite = info.event.id;
        //var posDescript = idactivite.indexOf('_');
        //var actDescription = idactivite.substring(posDescript + 1);
        //idactivite = info.event.id.substring(0, posDescript);
        var urlName = base_url + 'admin/getcalinfonew?titre=' + info.event.title + '&datestart=' + info.event.startStr.substring(0, 10);
        $.ajax({
          method: "POST",
          url: urlName
        })
        .done(function (msg) {
			    var objData = JSON.parse(msg);
			    var posHreStart = new Date( info.event.startStr);
			    var actHreStart = posHreStart.getHours() + ":" + posHreStart.getMinutes();
			    var posHreEnd = new Date( info.event.endStr);
			    var actHreEnd = posHreEnd.getHours() + ":" + posHreEnd.getMinutes();
				  $("#chkRepeter").prop('checked', false);
				  $("#chkLUN").prop('checked', false);
				  $("#chkMAR").prop('checked', false);
				  $("#chkMER").prop('checked', false);
			    $("#chkJEU").prop('checked', false);
			    $("#chkVEN").prop('checked', false);
			    $("#dteRepStart").val('');
			    $('#dteRepEnd').val('');
			    $("#idTbl_activite").val(objData.Id);
			    $("#nomActivite").html(info.event.title);
			    $('#descriptActivite').val(objData.Description);
			    $("#dteStart").val(info.event.startStr.substring(0, 10));
			    $("#dteEnd").val(info.event.endStr.substring(0, 10));
			    $("#hreStart").val(actHreStart);
			    $("#hreEnd").val(actHreEnd);
			    if (objData.allDay) {
				    $("#divHeure").hide();
			    }
			    else {
				    $("#divHeure").show();
			    }
			    $("#calActivites").modal("show");
        });
        // change the border color just for fun
        info.el.style.borderColor = 'red';
      },
      editable: true,
      droppable: true, // this allows things to be dropped onto the calendar
      drop: function (info) {
        var idactivite = info.draggedEl.id.toString();
        var urlName = base_url + 'admin/getinfo/' + info.draggedEl.id+ '_activite';

        $.ajax({
          method: "POST",
          url: urlName
        })
        .done(function (msg) {
          var objData = JSON.parse(msg);
          var posHreStart = new Date( info.date);
          var actHreStart = ((posHreStart.getHours() < 10) ? '0' + posHreStart.getHours() : posHreStart.getHours()) + ":" +
            ((posHreStart.getMinutes() < 10) ? '0' + posHreStart.getMinutes() : posHreStart.getMinutes());
          $("#chkRepeter").prop('checked', false);
			    $("#chkLUN").prop('checked', false);
			    $("#chkMAR").prop('checked', false);
			    $("#chkMER").prop('checked', false);
          $("#chkJEU").prop('checked', false);
          $("#chkVEN").prop('checked', false);
          $("#dteRepStart").val('');
          $('#dteRepEnd').val('');
          $("#nomActivite").html(objData.Nom);
          $('#descriptActivite').val(objData.description);
          $("#dteStart").val(info.dateStr.substring(0, 10));
          $("#dteEnd").val(info.dateStr.substring(0, 10));
          $("#idActivite").val(info.draggedEl.id);
          $("#idTbl_activite").val('0');
          $("#hreStart").val('');
          $("#hreEnd").val('');
          $("#hreStart").val(actHreStart);
          $("#hreEnd").val(actHreStart);
          if (info.allDay) {
            $("#divHeure").hide();
          }
          else {
            $("#divHeure").show();
          }
          $("#chkAllDay").prop('checked', info.allDay);
          $("#calActivites").modal("show");
        });
      }
    });

    calendar.render();
  });

  </script>
  <style>

  #external-events {
   
    z-index: 2;
    width: 250px;
    padding: 0 10px;
    border: 1px solid #ccc;
    background: #eee;
    color:white;
  }

  .demo-topbar + #external-events { /* will get stripped out */
    top: 60px;
  }

  #external-events .fc-event {
    cursor: move;
    margin: 3px 0;
  }

  #calendar-container {
    position: relative;
    z-index: 1;
    margin-left: 200px;
  }

  #calendar {
    max-width: 2000px;
    margin: 20px auto;
  }

  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->

  
          <?php echo view('sections/menu'); ?>        
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin:0 25px;">
            <div id="myNavbar" style="margin-left:5%;">
        <ul class="nav navbar-nav form-inline">
          <li><label  style="font-size:24px;font-weight:600;">Calendrier des Suivis RH</label> </li>
          <li> &nbsp;&nbsp;</li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <label style='font-size:24px;'>Début&nbsp;&nbsp; </label>&nbsp;&nbsp;
          </li>
          <li>
            <input type="date" class="form-control" onchange="LoadSuivisRH()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ; ?>" id="dtedeb" />
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <div class="input-group">
              <span class="input-group-btn">
              <label class="form-control" style="font-size:medium;font-weight:bold;width:140px">Type d'absence</label>
              <select id="selStatutRH" onchange="SelectTypeStatutRH(this.value)" class="form-control" style="width:150px;">
                <?php //echo ($typeabsences ?? '') ; ?>
              </select>
              </span>
            </div>
          </li>
          <li> &nbsp;&nbsp;</li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <button type="button" onclick="window.print()" class="btn btn-primary" style="margin-left: 25px;">
                  <i class="fa fa-download"></i> Imprimer
            </button>
          </li>
        </ul>

      </div> 
    <!-- Main content -->
    <div class="content">
      <div class="row">
        
        <div id="divPageDeSite" class="container-fluid col-sm-12" style="margin-right:10px;margin-left:5px;">
          <div id=\'calendar\' style="padding-right:10px"></div>
        </div><!-- /.container-fluid -->
      </div>

    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- /.control-sidebar -->
  <?php  //echo view('sections/inc_popup');?>
  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Pour votre gestion
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2021 <a href="https://conceptnika.com" target="_blank">Concept nika </a>.</strong> Tous droits réservés.
  </footer>
</div>
<!-- ./wrapper -->

<div class="modal fade" id="calActivites" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header btn-primary"><h3 class="modal-title" id="plTitre" style="color:white">Activité&nbsp;!</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                
                
			</div>
            <div class="modal-body">
                <div class="container-fluid" >

                         <br />
                            <h3 style="color:royalblue;">Activité  </h3>
                        <input type="hidden" class="form-control" id="idTbl_activite" />
                        <input type="hidden" class="form-control" id="idActivite" />
                        <div class="form-group">
                            <label id="nomActivite"></label>
                        </div>
                     
                           <h3 style="color:royalblue;">Description  </h3>

                        <div class="form-group">
                        <input type="text" class="form-control" id="descriptActivite" />
                        </div>
                   <h3 style="color:royalblue;">Date  </h3>    
                    <div class="form-group">
                          
                          <label style="font-weight: bold; "><input id="chkAllDay" type="checkbox" /> Toute la journée  </label>

                      </div>
                      <div class="input-group">
                        <span class="input-group-btn">
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:100px">Date début</label>
                        <input type="date"  class="form-control" style="width:150px;" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteStart" />
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:100px">Date fin</label>
                        <input type="date"  class="form-control" style="width:150px" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteEnd" />                        
                        </span>
                      </div> 
                      <div id="divHeure">
                        <h3 style="color:royalblue;">Heure  </h3>                    
                        <div class="input-group">
                          <span class="input-group-btn">
                          <label class="form-control" style="font-size:medium;font-weight:bold;width:130px">Heure début</label>
                          <input type="time"  class="form-control" style="width:100px" value="" id="hreStart" />                        
                          <label class="form-control" style="font-size:medium;font-weight:bold;width:130px">Heure fin</label>
                          <input type="time"  class="form-control" style="width:100px" value="" id="hreEnd" />                        
                          </span>
                        </div> 
                      </div>
                   <h3 style="color:royalblue;">Répéter&nbsp;&nbsp;<input id="chkRepeter" onclick="showHideRepeter(this.id)" type="checkbox" />  </h3>  
                  <div id="divRepeter" style="display:none">
                    <div class="form-group">
                          
                          
                        <select>
                          <option>Toutes les semaine</option>
                          <option>Toutes les semaine</option>
                        </select>
                      </div>
                      <div class="input-group">

                        <span class="input-group-btn">
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:80px">LUN&nbsp;&nbsp;<input class="form-check-input"  type="checkbox" id="chkLUN" /></label>
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:80px">MAR&nbsp;&nbsp;<input class="form-check-input"  type="checkbox" id="chkMAR" /></label>
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:80px">MER&nbsp;&nbsp;<input class="form-check-input"  type="checkbox" id="chkMER" /></label>
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:80px">JEN&nbsp;&nbsp;<input class="form-check-input"  type="checkbox" id="chkJEU" /></label>
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:80px">VEN&nbsp;&nbsp;<input class="form-check-input"  type="checkbox" id="chkVEN" /></label>

                        </span>

                      </div>     
                      <div class="input-group">
                        <span class="input-group-btn">
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:100px">Début</label>
                        <input type="date"  class="form-control" style="width:180px" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteRepStart" />
                        <label class="form-control" style="font-size:medium;font-weight:bold;width:100px">Fin</label>
                        <input type="date"  class="form-control" style="width:180px" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteRepEnd" />                        
                        </span>
                      </div>                   
                  </div>
   

                    </div>                                            
				</div>
			  <div class="modal-footer">
            <button type="button" onclick="PostEvent()" class="btn btn-primary" data-dismiss="modal" title="Enregistrer" style="margin:4px 5px 0 0;">Enregistrer</button>
            <button type="button" class="btn btn-warning" data-dismiss="modal" title="Annuler"  style="margin:4px 5px 0 0;">Annuler</button>
          <button type="button" class="btn btn-danger" onclick="" title="Supprimer cette Ligne" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>
        </div>
            </div>

        </div>
    </div>



<!-- REQUIRED SCRIPTS -->



<!-- AdminLTE App -->
  <script src="/assets/summernote/summernote-bs4.min.js"></script>
  <script src="/assets/js/adminlte.js"></script>
  <script src="/assets/js/nika.js"></script>
  <script>
    $('#editor_fr').summernote({
      toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        //['fontsize', ['fontsize']],
        //['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ['height', ['height']]
      ]
    });
    $('#editor_en').summernote({
      toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        //['fontsize', ['fontsize']],
        //['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ['height', ['height']]
      ]
    });
    //$('#descriptActivite').summernote({
    //  toolbar: [
    //    // [groupName, [list of button]]
    //    ['style', ['bold', 'italic', 'underline', 'clear']],
    //    ['font', ['strikethrough', 'superscript', 'subscript']],
    //    //['fontsize', ['fontsize']],
    //    //['color', ['color']],
    //    ['para', ['ul', 'ol', 'paragraph']],
    //    ['table', ['table']],
    //    ['insert', ['link', 'picture', 'video']],
    //    ['view', ['fullscreen', 'codeview', 'help']],
    //    ['height', ['height']]
    //  ]
    //});
        <?php echo ($listscript ?? ''); ?>
    function showmenu(ulId,btnId) {
      $("#ulProjet").hide();
      $("#ulUsager").hide();
      $("#ulPageSite").hide();
      $('#btnProjets').removeClass('active');
      $('#btnUsager').removeClass('active');
      $('#btnPageSite').removeClass('active');
      $("#" + btnId).addClass('active');
      $("#" + ulId).show();      
    }
    //showmenu('<?php echo ($ulTitre ?? ''); ?>','<?php echo ($btnTitre ?? ''); ?>')
  </script>
</body>
</html>
