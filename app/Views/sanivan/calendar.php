<?php

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/Ionicons/css/ionicons.min.css" />
  <!-- fullCalendar -->
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.min.css" />
  <link rel="stylesheet" href="/assets/fullcalendar/dist/fullcalendar.print.min.css" media="print" />
  <!-- Theme style -->
  <link rel="stylesheet" href="/assets/css/AdminLTE.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
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
  <script src="/assets/css/fontawesome/all.css"></script>
  <script>
    var base_url = "<?php echo ci3_url()?>";
  </script>
  </head>
<body class="hold-transition skin-blue">

    




<div class="wrapper"> 
  <nav class="navbar navbar-inverse navbar-fixed-top">
        <!-- Sidebar toggle button-->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
        <?php echo view('sections/menu');?>
      </nav>
  <section class="content" style=" margin-top:3%">
	
<div class="row">
        <div id="divVilles" class="col-md-3" style="display: none;overflow: scroll; max-height: 700px; width: 200px">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h4 class="box-title">Villes à ajouter</h4>
            </div>
            <div class="box-body">
              <!-- the events -->
              <div id="external-events" >
                <?php
                    echo ($ville ?? '');
                ?>

              </div>
            </div>
            <!-- /.box-body -->
          </div>

        </div>
        <!-- /.col -->
        <div id="divCalendar" class="col-md-12">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <!-- THE CALENDAR -->
              <div id="calendar"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>	
</div>

<div class="modal fade" id="calVilles" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitre" style="color:white">&nbsp;!</h3>
                
			</div>
            <div class="modal-body">
                <div class="container-fluid" >
                    <div class="form-group">
                         <br /> <br />
                            <label style="font-weight: bold; ">Types de produit  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="typeprod" id="typeprod" required>
                                <option value="DECHETS">Choisir</option>
                                <?php echo ($typededechet ?? '') ; ?>
                            </select>
                        </div>
                    </div>   
                           <br /><br />
                           <label style="font-weight: bold; ">Chauffeurs  </label> <br />
                       
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" onchange="changeChauffeur()" name="chauffeur" id="chauffeur" required>
                                <option value="25">Choisir</option>
                                <?php echo ($chauffeurs ?? '') ; ?>
                            </select>
                        </div>
                        <br /><br />
                           <label style="font-weight: bold; ">Aides  </label> <br />
                       
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="aide" id="aide" required>
                                <option value="24">Choisir</option>
                                <?php echo ($aides ?? '') ; ?>
                            </select>
                        </div>
                             <br />
                             <br />
                    
                           <label style="font-weight: bold; "># Camions  </label> <br />
                        
                        <div class="col-sm-7 col-xs-12">
                            <select class="form-control" name="camion" id="camion" required>
                                <option value="Cam_choisir">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>                    
				</div>
            </div>
			  <div class="modal-footer">
            <button type="button" onclick="PostEvent()" class="btn btn-default" data-dismiss="modal" title="Enregistrer" style="margin:4px 5px 0 0;"><i class="fa fa-save fa-2x btn-primary" style="font-weight:bold;"></i></button>
            <button type="button" class="btn btn-default" onclick="DeleteLigneCedule()" title="Supprimer cette Ligne" data-dismiss="modal" style="margin:4px 5px 0 0;"><i  class="fa fa-trash fa-2x btn-danger" style="font-weight:bold;" aria-hidden='true'></i></button>
            <button type="button" class="btn btn-default" data-dismiss="modal" title="Annuler"  style="margin:4px 5px 0 0;"><i class='fa fa-remove fa-2x btn-warning' aria-hidden='true' style="font-weight:bold;" ></i></button>
        </div>
        </div>
    </div>
</div>


<!--Modal ends here--->

  <script src="/assets/jquery-ui/jquery-ui.min.js"></script>
  <!-- Slimscroll -->
  <script src="/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="/assets/fastclick/lib/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="/assets/js/adminlte.min.js"></script>
<script src="/assets/fullcalendar/dist/fullcalendar.js"></script>
  <!-- fullCalendar -->
  <script src="/assets/moment/moment.js"></script>
  <script src="/assets/js/nika.js"></script>
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
<script>
  var selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
  var dftView = 'basicWeek';
  $(function () {

    /* initialize the external events
     -----------------------------------------------------------------*/
    function init_events(ele) {
      ele.each(function () {

        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        }

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject)

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex        : 1070,
          revert        : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        })

      })
    }

    init_events($('#external-events div.external-event'))

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()
    var myCalendar = $('#calendar').fullCalendar({
      header    : {
        left  : 'prev,next today',
        center: 'title',
        right : 'month,basicWeek,agendaDay'
      },
      buttonText: {
        today: "Aujourd'hui",
        month: 'Mois',
        week : 'Semaine',
        day  : 'Jour'
      },
		eventClick: function (event, jsEvent, view) {
			evenPost = event;
			var arrTitle = event.title.split(', ');
			$("#plTitre").html(arrTitle[0]);
			//$("#startTime").html(moment(event.start).format('MMM Do h:mm A'));
			//$("#endTime").html(moment(event.end).format('MMM Do h:mm A'));
      var arrDescript = event.description.split(', ');
      ActiveVille = 0;
			$("#typeprod").val(arrTitle[1]);
			$("#chauffeur").val(arrDescript[2]);
      $("#aide").val(arrDescript[4]);
      $("#camion").val(arrDescript[0]);
			$("#calVilles").modal("show");
      },
    dayClick: function(date, jsEvent, view) {

        selectedDay = date.format();
    },
    select: function(start, end, allDay) {

      var title = "serge"
      if (title) {
        calendar.fullCalendar('renderEvent', {
            title: title,
            allDay: allDay
          },

          true // make the event "stick"
        );
      }


      calendar.fullCalendar('unselect');
    },
    eventRender: function (event, element, view) {
            $(element).css("font-weight", "bold");
    },
      //Random default events
      events    : [
        <?php
        echo ($dataCD ?? '');
        ?>
      ],  
      defaultView: dftView,
      eventTextColor: 'black',
      editable: true,
      eventDrop: function (event, date, view) {
       

        // we need to copy it, so that multiple events don't have a reference to the same object
        evenPost = event;
        $('#spville').html(event.title);
        $("#transfertModal").modal("show");
      },
      droppable : true, // this allows things to be dropped onto the calendar !!!
      eventLimit: false, // allow "more" link when too many events
      //defaultView: 'month',
      selectable: true, //permite sa selectezi mai multe zile
      selectHelper: true, //coloreaza selctia ta
      drop      : function (date, allDay) { // this function is called when something is dropped

        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject')
       
        // we need to copy it, so that multiple events don't have a reference to the same object
        evenPost = $.extend({}, originalEventObject)
        var dt = new Date(date).toLocaleString("en-US", {timeZone: "America/New_York"});
        // assign it the date that was reported
        evenPost.id = 0
        evenPost.start           = date
        evenPost.allDay          = allDay
        evenPost.backgroundColor = $(this).css('background-color')
        evenPost.borderColor = $(this).css('border-color')
        $("#plTitre").html(evenPost.title);
        $("#typeprod").val('');
			  $("#chauffeur").val(0);
        $("#aide").val(0);
        $("#camion").val('');
        $("#calVilles").modal("show");
      }
    })

    /* ADDING EVENTS */
    var currColor = '#3c8dbc' //Red by default
    //Color chooser button
    var colorChooser = $('#color-chooser-btn')
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      //Save color
      currColor = $(this).css('color')
      //Add color effect to button
      $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
    })
    $('#add-new-event').click(function (e) {
      e.preventDefault()
      //Get value and make sure it is not null
      var val = $('#new-event').val()
      if (val.length == 0) {
        return
      }

      //Create events
      var event = $('<div />')
      event.css({
        'background-color': currColor,
        'border-color'    : currColor,
        'color'           : '#fff'
      }).addClass('external-event')
      event.html(val)
      $('#external-events').prepend(event)

      //Add draggable funtionality
      init_events(event)

      //Remove event from text input
      $('#new-event').val('')
    })
  })
$(function() { // document ready


  $("#edit").click(function(e) {
    e.preventDefault();

    var title = $("#required-input").val().trim();
    if (!title) {
      return;
    }
    var event = $(".cont").data('event'),
      isAdd = !event;
    if (isAdd) {
      event = {};
      event.start = '2014-11-12';
    }
    event.title = title;
    if (isAdd) {
      $('#calendar').fullCalendar('renderEvent', event, true);
    } else {
      $('#calendar').fullCalendar('updateEvent', event);
    }
    $(".cont").hide().removeData('event');
  });

  $("#add").click(function(e) {
    $(".cont").show();
  });
});

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
