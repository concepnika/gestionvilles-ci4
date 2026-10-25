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
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.0/lang-all.js "></script>  
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
  <link rel="stylesheet" href="/assets/css/jquery-ui.multidatespicker.css" />
  <style>

		.tdcalendrier{
			width:190px;
			height:160px;
			background-color:#FFFFCC;
			border: 1px solid #708090;
		}
		.tdcalendrierHeader{
			width:190px;
			height:30px;
			background-color:#812B2C;
			color:white;
			text-align:center;
			font-size:1.5em;
		}
		.tdcalendrierHeaderMonth{
			width:190px;
			height:42px;
			background-color:#812B2C;
			color:white;
			text-align:center;
			font-size:2.5em;
		}
		.tdcalendrierComboMonth{
			width:180px;
			height:38px;
			background-color:#FFFFCC;
			font-size:1.0em;
		}

		.tableCalendrier{
			border: 1px solid #800000;
			width: 1100px;
			height: 1060px;
		}
		.tdjour{
			width:40px;
			height:20px;
			background-color:#812B2C;
			color:white;
			text-align:center;
			font-size:1.5em;
		}
		.tdinfo{
			width:150px;
			height:20px;
			font-size:medium;
		}
		.tdheure{
			width:40px;
			height:20px;
			border: 1px solid #800000;
			font-family:Arial, Helvetica, sans-serif;
		}
		.tdheureVide{
			width:40px;
			height:20px;
			font-family:Arial, Helvetica, sans-serif;
		}
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
  <script src="/assets/js/jquery-ui.multidatespicker.js"></script>
  <script src="/assets/css/fontawesome/all.css"></script>
  <script type='text/javascript'>
    var base_url = "<?php echo ci3_url()?>";
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    var ActiveDrop = "<?php echo ($ActiveDrop ?? '') ?>";
    function ConfirmeLoad(Route)
    {
        $("#td_" + Route).css("background-color","#80FF9F");
        $("#chk_" + Route).css("background-color","#80FF9F");
    }
    $(document).ready(function () {
		$('[data-toggle="popover"]').popover();  
		var getPosition = function (e, ui) {
       // var value = (ui.item.index() + 1).toString();
       // var arrData = ui.item.context.id.split('_');
	      //var idproduit = arrData[1];
       // var oldrang = arrData[2];
       // var tableid = arrData[3];
       // document.location = '<?php //echo ci3_url(); ?>/*/gestion/rang/' + idproduit + '/' + oldrang + '/' + value + '/' + tableid;*/
      };
			$("#body_1").sortable({
        beforeStop: getPosition,
        update: function(event, ui) {
          var value = (ui.item.index() + 1).toString();
          var arrData = ui.item.attr('id').split('_');
          var idgroup = arrData[1];
          var acol = arrData[3];
          var typecedule = arrData[2];
          var oldrang = arrData[4];
          updTableRang(idgroup, oldrang, value, ActiveDate, acol, typecedule);
        }
      });
      $("#body_2").sortable({
        beforeStop: getPosition,
        update: function(event, ui) {
          var value = (ui.item.index() + 1).toString();
          var arrData = ui.item.attr('id').split('_');
          var idgroup = arrData[1];
          var acol = arrData[2];
          var typecedule = arrData[3];
          var oldrang = arrData[4];
          updTableRang(idgroup, oldrang, value, ActiveDate, acol, typecedule);
        }
      });
      $("#body_3").sortable({
        beforeStop: getPosition,
        update: function(event, ui) {
          var value = (ui.item.index() + 1).toString();
          var arrData = ui.item.attr('id').split('_');
          var idgroup = arrData[1];
          var acol = arrData[2];
          var typecedule = arrData[3];
          var oldrang = arrData[4];
          updTableRang(idgroup, oldrang, value, ActiveDate, acol, typecedule);
        }
      });
    });

  </script>
</head>
<body class="hold-transition skin-blue">

  <div class="wrapper">


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

 

        <div class="row" style="position:absolute;left: 0px;top: 70px;width:100%; border-bottom:solid; ">

           <div class="col-md-7">
		        <ul class="nav nav-pills">
			        <li <?php echo(($typederamassage == 1)? 'class="active"' : '' );?>  id="liPage_1">
				        <a href="javascript:ShowDechet(1)">DÉCHETS</a>
			        </li>
			        <li <?php echo(($typederamassage == 2)? 'class="active"' : '' );?> id="liPage_2">
				        <a href="javascript:ShowDechet(2)">RECYCLAGES</a>
			        </li>
              <li <?php echo(($typederamassage == 3)? 'class="active"' : '' );?> id="liPage_3">
				        <a href="javascript:ShowDechet(3)">ORGANIQUES</a>
			        </li>
              <li <?php echo(($typederamassage == 4)? 'class="active"' : '' );?> id="liPage_4">
				        <a href="javascript:ShowDechet(4)">GROS DÉCHETS</a>
			        </li>
              <li>
                <div class="input-group input-group-sm"  style="width:250px">
                  <div class="input-group-addon"><b>VILLES</b></div>
                  <select class="form-control" name="optVilles" id="optVilles" onchange="getVille(this.value)">
                      <option value="">Choisir</option>
                      <?php echo ($optvilles ?? '') ; ?>
                  </select>
                </div>
			        </li>   
                            
		        </ul>
	        </div>       
        </div>
          <div id="divVilles"  style="position:absolute;left: 0px;top: 130px;width: 100%; border-bottom:solid; ">
              <form  class="form-inline">

            <div class="box-header with-border">
              <h4 class="separateur">Informations de déploiement</h4>
            </div>
            
                <div class="form-group">
                <div class="input-group input-group-sm"  style="width:75px;margin-left:20px;">
                    <button type="button" id="btnEdit" onclick="editCalendrier()" style="height:30px" class="btn btn-primary" disabled>Éditer</button>
                  </div>
                </div>
                <div class="form-group">
                <div class="input-group input-group-sm"  style="width:250px;">
                  <div class="input-group-addon"><b>Nb REAR END</b></div>
                    <input type="number" class="form-control" name="nbRearEnd" id="nbRearEnd" disabled />
                  </div>
                </div>
                <div class="form-group" style="margin-left:10px;">
                <div class="input-group input-group-sm"  style="width:250px">
                  <div class="input-group-addon"><b>Nb ROBOT</b></div>
                    <input type="number" class="form-control" name="nbRobot" id="nbRobot" disabled />
                  </div>
                </div>
                <div class="form-group">
                  
                    <button type="button" id="btnPost" onclick="postCalendrier()" style="height:30px" class="btn btn-success" disabled>Enregistrer</button>
                  
                </div>
                <div class="form-group">
                <div class="input-group input-group-sm"  style="width:75px;margin-left:20px;">
                    <button type="button" id="btnCancel" onclick="cancelCalendrier()" style="height:30px" class="btn btn-danger" disabled>Annuler</button>
                  </div>
                </div>
                <div class="form-group" style="margin-left:10px;">
                  <div class="input-group input-group-sm"  style="width:350px">
                    <div class="input-group-addon"><b>Déploiement Débutant le </b></div>
                    <input type="date" class="form-control" name="dteDeploie" id="dteDeploie" disabled/>
                    <div class="input-group-addon"><b><button type="button" id="btnDeploie" onclick="deploieCalendrier()" style="height:18px" class="btn-link" disabled>Déployer tout</button></b></div>
                  </div>
                </div>              
            </form>
            <!-- /.box-body -->
          </div>
    <div class="row" style="margin-top: 260px;border-bottom:solid;">
            <div class="col-md-2"> </div>
            <div id="divCalendar" class="col-md-8">
            <div class="box box-primary">
            <div class="box-body no-padding">
              <div id="calendar"></div>
            </div>
          </div>
          <script>
              document.addEventListener('DOMContentLoaded', function() {
                
            var myCalendar = $('#calendar').fullCalendar({
              lang: 'fr',
              header: {
                left: 'prev,next today',
                center: 'title'
              },
              buttonText: {
                today: "Aujourd'hui"
              },
              height: 850,
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
              dayClick: function (date, jsEvent, view) {
                selectedDay = date.format();
                var urlName = "/sanivan/postCalendrier/" + $("#optVilles").val() + "?selectedDay=" + selectedDay + "&typederamassage=" + typederamassage;
                document.location = urlName;
                // var urlName = "/sanivan/postCalendrier/" + $("#optVilles").val() + "?selectedDay=" + selectedDay + "&typederamassage=" + typederamassage;
                //$.ajax({
                // async: false,
                // type: "POST",villescalendrier
                // url: urlName
                // }).done(function (msg) {
                //   if (msg == "") {
                //     alert('Une erreur est survenue. Recommencer.');
                //   }
                //   else {
                //     //var arrTitle = msg.split(';');
                //     this.addEvent({
                //       title: 'dynamic event',
                //       start: selectedDay,
                //       allDay: true
                //     });
                //   }
                //});
              },
              select: function (start, end, allDay) {

                var title = "serge"
                if (title) {
                  myCalendar.fullCalendar('renderEvent', {
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
                    <?php echo ($dataCD ?? ''); ?>

              defaultView: 'month',
              eventTextColor: 'black',
              editable: true,
              eventDrop: function (event, date, view) {
                evenPost = event;
              },
              droppable: true, // this allows things to be dropped onto the calendar !!!
              eventLimit: false, // allow "more" link when too many events
              //defaultView: 'month',
              selectable: true, //permite sa selectezi mai multe zile
              selectHelper: true, //coloreaza selctia ta
              drop: function (date, allDay) { // this function is called when something is dropped

                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject')

                // we need to copy it, so that multiple events don't have a reference to the same object
                evenPost = $.extend({}, originalEventObject)
                var dt = new Date(date).toLocaleString("en-US", { timeZone: "America/New_York" });
                // assign it the date that was reported
                evenPost.id = 0
                evenPost.start = date
                evenPost.allDay = allDay
                evenPost.backgroundColor = $(this).css('background-color')
                evenPost.borderColor = $(this).css('border-color')
                $("#plTitre").html(evenPost.title);
                $("#typeprod").val('');
                $("#chauffeur").val(0);
                $("#aide").val(0);
                $("#camion").val('');
                $("#calVilles").modal("show");
              }
            });
                    myCalendar.render();
  });
          </script>
        </div>
        

        </div>
      <div class="row" style="margin-top: 30px;">
        <div class="col-md-12"> </div>
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
    <script src="/assets/fullcalendar/dist/locale/fr-ca.js"></script>
    <script src="/assets/fullcalendar/dist/locale/fr.js"></script>
  <script>
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";
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
    //-------------------------------------------------------------------------------
    var objData = "";
    var typederamassage = "<?php echo ($typederamassage ?? ''); ?>";
   //function getVille(value) {
   //   if (value == '') {
   //     $('#btnDeploie').prop("disabled", true);
   //     $('#btnPost').prop("disabled", true);
   //     $("#nbRearEnd").val('');
   //     $("#nbRobot").val('');
   //     cancelCalendrier();
   //   }
   //   else {
   //     var urlName = "/sanivan/getinfoville/" + value;
	  //    $.ajax({
		 //     async: false,
		 //     type: "POST",
		 //     url: urlName
   //     }).done(function (msg) {
   //       alert(msg);
   //       if (msg == "") {
   //         alert('Une erreur est survenue. Recommencer.');
   //       }
   //       else {
   //         $("#divCalendar").html(msg);
   //       }
	  //    });
   //   }

   // }
    function getVille(value) {
      var urlName = "/sanivan/getinfoville/" + value + "?typederamassage=" + typederamassage;
      document.location = urlName;
    }
    //-------------------------------------------------------------------------------
    function editCalendrier() {
      $('#btnEdit').prop("disabled", true);
      $('#btnDeploie').prop("disabled", false);
      $('#btnPost').prop("disabled", false);
      $('#btnCancel').prop("disabled", false);
      $('#dteDeploie').prop("disabled", false);
      $('#nbRearEnd').prop("disabled", false);
      $('#nbRobot').prop("disabled", false);
      $('#calVille').show();
      $('#calVilleDisabled').hide();
    }
    //-------------------------------------------------------------------------------
    function cancelCalendrier() {
      $('#btnEdit').prop("disabled", false);
      $('#btnDeploie').prop("disabled", true);
      $('#btnPost').prop("disabled", true);
      $('#btnCancel').prop("disabled", true);
      $('#dteDeploie').prop("disabled", true);
      $('#nbRearEnd').prop("disabled", true);
      $('#nbRobot').prop("disabled", true);
      $('#calVille').prop('disabled', true);
      ShowDechet(typederamassage);
      $('#calVille').hide();
      $('#calVilleDisabled').show();
    }
    //-------------------------------------------------------------------------------
    function ShowDechet(typedechet) {
      var urlName = "/sanivan/getinfoville/" + $("#optVilles").val();
	    $.ajax({
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
        if (msg !== "") {
          objData = JSON.parse(msg);
          $('#calVille').multiDatesPicker('destroy');
          $('#calVilleDisabled').multiDatesPicker('destroy');
          switch (typedechet) {
            case 1:
              $("#nbRearEnd").val(objData.rbCamiondechetsRE);
              $("#nbRobot").val(objData.rbCamiondechetsROBOT);
              if (objData.dechets != '') {

                $('#calVille').multiDatesPicker('value', objData.dechets);
                $('#calVilleDisabled').multiDatesPicker('value', objData.dechets);
              }
              break;
            case 2:
              $("#nbRearEnd").val(objData.rbCamionrecyclageRE);
              $("#nbRobot").val(objData.rbCamionrecyclageROBOT);
              if (objData.recyclage != '') {
                $('#calVille').multiDatesPicker('value', objData.recyclage);
                $('#calVilleDisabled').multiDatesPicker('value', objData.recyclage);
              }
              break;
            case 3:
              $("#nbRearEnd").val(objData.rbCamionorganiqueRE);
              $("#nbRobot").val(objData.rbCamionorganiqueROBOT);
              if (objData.organique != '') {
                $('#calVille').multiDatesPicker('value', objData.organique);
                $('#calVilleDisabled').multiDatesPicker('value', objData.organique);
              }
              break;
            case 4:
              $("#nbRearEnd").val(objData.rbCamiongrosrebutsRE);
              $("#nbRobot").val(objData.rbCamiongrosrebutsROBOT);
              if (objData.grosrebuts != '') {
                $('#calVille').multiDatesPicker('value', objData.grosrebuts);
                $('#calVilleDisabled').multiDatesPicker('value', objData.grosrebuts);
              }
              break;
          }
		    }
	    });
      typederamassage = typedechet;
      $('#liPage_1').removeClass("active");
      $('#liPage_2').removeClass("active");
      $('#liPage_3').removeClass("active");
      $('#liPage_4').removeClass("active");
      $('#liPage_' + typedechet).addClass("active");
    }
    function postCalendrier() {
      var urlName = "/sanivan/postCalendrier/" + $("#optVilles").val() + "?nbRearEnd=" + (($("#nbRearEnd").val() == '')? '0' : $("#nbRearEnd").val()) +
                      "&nbRobot=" + (($("#nbRobot").val() == '')? '0' : $("#nbRobot").val()) + "&calVille=" + $('#calVille').multiDatesPicker('value') +
                      "&typederamassage=" + typederamassage;
      alert(urlName);
	    $.ajax({
		    async: false,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
        if (msg !== "OK") {
          alert('Une erreur est survenue. Recommencer.');
        }
        else {
          ShowDechet(typederamassage);
          cancelCalendrier();
        }
	    });
    }

  </script>
</body>
</html>
