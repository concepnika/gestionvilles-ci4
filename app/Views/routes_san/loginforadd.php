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
  </style>
  <script type='text/javascript'>
    var ActiveDate = "<?php echo ($ActiveDate ?? '') ?>";
    function ConfirmeLoad(Route)
    {
        $("#td_" + Route).css("background-color","#80FF9F");
        $("#chk_" + Route).css("background-color","#80FF9F");
    }
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover();
    });
  </script>
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <img src="/assets/img/logo.png" width="355" alt="logo proticlub">
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">Connectez-vous pour d&eacute;buter votre session</p>
      <div style="border-color:red; border:double;padding-left:4px;display:<?php echo (($msgerror != '') ? 'block' : 'none'); ?>;">
        <label style="font-size:small;color:darksalmon;">
          <?php echo ($msgerror ?? ''); ?>
        </label>
      </div>
      <form action="/tablettes_san/addtoroutes" method="post" id="frmLogin">
        <input type="hidden" id="from" name="from" value="1"/>
        <input type="hidden" id="dtetoadd" name="dtetoadd" value="<?php echo ($ActiveDate ?? ''); ?>"/>
        <div class="form-group  has-feedback">
          <div class="input-group">
            <div class="input-group-addon"><label>Camion:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></div>
            <select class="form-control" onchange="getCamSani()" id="edtcamion" name="edtcamion" style="font-size:medium;font-weight:bold;width:90%;">
              <option value="">Choisir</option><?php echo ($camion ?? '');?>
            </select>
          </div>
        </div>
        <div class="form-group has-feedback">
          <div class="input-group">
            <div class="input-group-addon"><label>KM de D&eacute;part:&nbsp;&nbsp;</label></div>
            <input type="text" class="form-control" onb id="edtkmDepart" placeholder="Km d&eacute;part" name="edtkmDepart" onblur="VerifKM(this.value)" style="font-size:medium;font-weight:bold;width:90%;" />
            <input type="hidden" class="form-control" id="edtKMFin" placeholder="Km d&eacute;part" name="edtKMFin" />
            <input type="hidden" class="form-control" id="edtCarburant" placeholder="Km d&eacute;part" name="edtCarburant" />
          </div>
        </div>
        <div class="form-group has-feedback">
          <div class="input-group">
            <div class="input-group-addon"><label>Code usager:&nbsp;&nbsp;</label></div>
            <!--<input type="text" class="form-control" id="code" placeholder="Code usager" name="code" style="font-size:medium;font-weight:bold;width:90%;" />-->
            <select class="form-control" id="code" name="code" style="font-size:medium;font-weight:bold;width:90%;">
              
              <?php echo ($chauffeurs ?? '');?>
            </select>
          </div>
        </div>
        <!--<div class="form-group has-feedback">
          <div class="input-group">
            <div class="input-group-addon"><label><?php echo ci3lang('login_password');?>:&nbsp;&nbsp;</label></div>
            <input type="password" class="form-control" id="password" name="password" autocomplete="off" style="font-size:medium;font-weight:bold;width:90%;" />
          </div>
        </div>-->
        <div class="row">

          <!-- /.col -->
          <div class="col-xs-4 pull-right">
            <button id="btnLogin" type="submit" class="btn btn-primary btn-block btn-flat" >
              <?php echo ci3lang('login_connect');?>
            </button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <br />

    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->
  <script src="/assets/bower_components/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- iCheck -->
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
    <!-- keyboard widget css & script (required) -->
  <link href="/assets/keyboard/css/keyboard.css" rel="stylesheet" />
  <link href="/assets/keyboard/css/keyboard-previewkeyset.css" rel="stylesheet" />
  <script src="/assets/keyboard/js/jquery.keyboard.js"></script>

  <!-- keyboard extensions (optional) -->
  <script src="/assets/keyboard/js/jquery.mousewheel.js"></script>
  <script src="/assets/keyboard/js/jquery.keyboard.extension-typing.js"></script>
  <script src="/assets/keyboard/js/jquery.keyboard.extension-previewkeyset.js"></script>

  <!-- keyboard layout -->
  <script src="/assets/keyboard/layouts/keyboard-layouts-combined.js" charset="utf-8"></script>
  <script src="/assets/js/sanivan.js"></script>
  <script>
    $("#password").val('');
    $("#code").val('');

		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' /* optional */
			});
		});
  </script>
</body>
</html>
