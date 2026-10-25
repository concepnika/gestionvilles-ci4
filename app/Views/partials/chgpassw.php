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
  <link rel="stylesheet" href="/assets/css/nikaadmin.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/bower_components/Ionicons/css/ionicons.min.css" />
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
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
        .login-box-body,
.register-box-body {
  background: #fff;
  padding: 20px;
  border-top: 0;
  color: #666;
  width:700px;
}
.login-box-body .form-control-feedback,
.register-box-body .form-control-feedback {
  color: #777;
}
  </style>
  <script type='text/javascript'>
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
    <div class="login-logo" style="margin-left: 200px;">
      <img src="/assets/img/logo.png" width="250" />
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg"><?php echo ci3lang('changepass_reinitialise');?></p>

      <form  method="post">
        <div class="form-group  has-feedback"></div>
        <div style="display:<?php echo (($from == 2)? 'none' : 'block');?>"  class="form-group">
          <div class="col-sm-4 col-xs-12 control-label">
            <?php echo ci3lang('inscription_adresse_courriel');?>
          </div>
          <div class="col-sm-7 col-xs-12 input-group champ-acces">
            <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">@</span>
            <input type="email" class="form-control" name="email" id="email" value="<?php echo ($email ?? '');?>" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>" />
          </div>

        </div>
        <div style="display:<?php echo (($from == 2)? 'none' : 'block');?>" class="form-group">
          <div class="col-sm-4 col-xs-12 control-label">
            <?php echo ci3lang('changepass_passw_now');?>
          </div>
          <div class="col-sm-7 col-xs-12 input-group champ-acces">
            <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">?</span>
            <input type="password" class="form-control" name="passchange" onblur="ValidOldPassW()" id="passchange" value="" placeholder="<?php echo ci3lang('changepass_passw_now');?>" autocomplete="new-password" />
            <div class="col-sm-4 col-xs-12 control-label"></div>
            <div class="col-sm-7 col-xs-12 input-group">

              <div id="divValidOld" style="display:none;">
                <p style="font-size:small;color:red; ">
                  <?php echo ci3lang('changepass_infovalidoldpass');?>
                </p>
              </div>
            </div>
          </div>

        </div>
        <div class="form-group">
          <div class="col-sm-4 col-xs-12 control-label">
            <?php echo ci3lang('changepass_passw_new');?>
          </div>
          <div class="col-sm-7 col-xs-12 input-group champ-acces">
            <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">?</span>
            <input type="password" class="form-control" name="passnew" id="passnew" onblur="ValidStrongPassW()" value="" placeholder="<?php echo ci3lang('changepass_passw_new');?>" autocomplete="new-password" />
            <div class="help-block with-errors"></div>
          </div>
          <div class="col-sm-4 col-xs-12 control-label"></div>
          <div class="col-sm-7 col-xs-12 input-group">

            <div id="divValidStrong" style="display:none;">
              <p style="font-size:small;color:red; ">
                <?php echo ci3lang('changepass_infovalidpass');?>
              </p>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 col-xs-12 control-label">
            <?php echo ci3lang('inscription_valide_passw');?>
          </div>
          <div class="col-sm-7 col-xs-12 input-group champ-acces">
            <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">!</span>
            <input type="password" class="form-control" name="passconf" id="passconf" placeholder="<?php echo ci3lang('inscription_valide_passw');?>" onblur="VerifIsSame()" data-match="#passnew" data-match-error="<?php echo ci3lang('inscription_erreur_passw');?>" autocomplete="new-password" />
          </div>
          <div class="col-sm-4 col-xs-12 control-label"></div>
          <div class="col-sm-7 col-xs-12 input-group">

            <div id="divValidSame" style="display:none;">
              <p style="font-size:small;color:red; ">
                <?php echo ci3lang('changepass_nonidentique');?>
              </p>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 col-xs-12 control-label"></div>
          <div class="col-sm-7 col-xs-12 input-group champ-acces">

            <button type="button" id="btnSubmit" onclick="SubmitChangePass()" class="btn btn-primary btn-block btn-flat" style="width:300px;" disabled>
              <?php echo ci3lang('changepass_send');?>
            </button>
          </div>
        </div>
      </form>

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
  <script>
    $(document).ready(function() {
        $('input').attr('autocomplete', 'off');
      });
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' /* optional */
			});
    });
    function VerifIsSame() {
      if ($('#passnew').val() != $('#passconf').val()) {
        $('#divValidSame').show();
      }
      else {
        $('#divValidSame').hide();
        var from = '<?php echo ($from ?? '');?>';
        if (from == '2') {
          $('#btnSubmit').prop("disabled", false);
        }
        else  if (($('#passchange').val() != '') && ($('#email').val() != '') && ($('#passnew').val() != '')) {
          $('#btnSubmit').prop("disabled", false);
        }
      }
    }
    function ValidStrongPassW() {
      var urlName = "/login/validstrong?password=" + encodeURIComponent($('#passnew').val());
		  $.ajax({
		  	async: false,
		  	type: "POST",
		  	url: urlName
      }).done(function (msg) {
        if (parseInt( msg ) == 0) {
          $('#divValidStrong').show();
          $('#passnew').focus();
          $('#passnew').select(); 
		  	}
        else {
          $('#divValidStrong').hide();
		  		//-------------------
		  	}
		  });
    }
    function ValidOldPassW() {
      var urlName = "/login/validold?password=" + encodeURIComponent($('#passchange').val()) + '&email=' + encodeURIComponent($('#email').val());
		  $.ajax({
		  	async: false,
		  	type: "POST",
		  	url: urlName
      }).done(function (msg) {
        if (parseInt( msg ) == 0) {
          $('#divValidOld').show();
          $('#passchange').focus();
          $('#passcha nge').select(); 
		  	}
        else {
          $('#divValidOld').hide();
          $('#passnew').focus();
          $('#passnew').select(); 
		  		//-------------------
		  	}
		  });
    }
    function SubmitChangePass() {
      var from = '<?php echo ($from ?? '');?>';
      var urlName = ''
      if (from == '2') {
        urlName = "/login/postchange?passnew=" + encodeURIComponent($('#passnew').val()) + '&email=' + encodeURIComponent($('#email').val());
      }
      else {
        urlName = "/login/postchange?passnew=" + encodeURIComponent($('#passnew').val()) + '&passchange=' + encodeURIComponent($('#passchange').val()) +
                          '&email=' + encodeURIComponent($('#email').val());
      }
      
      document.location = urlName;
    }
  </script>
</body>
</html>
