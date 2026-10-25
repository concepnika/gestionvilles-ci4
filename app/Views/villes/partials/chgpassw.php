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
	<?php echo view('villes/incfile/inc_head'); ?>
  <link rel="stylesheet" href="/assets/vendor/datatables/media/css/dataTables.bootstrap5.css" />
  <style>
    .popover-title {
      background: darkblue;
      color: white;
      font-weight: bold;
      min-width: 200px;
    }

    .popover-content {
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
      width: 700px;
    }

      .login-box-body .form-control-feedback,
      .register-box-body .form-control-feedback {
        color: #777;
      }

  </style>
  <script type='text/javascript'>
    function ConfirmeLoad(Route) {
      $("#td_" + Route).css("background-color", "#80FF9F");
      $("#chk_" + Route).css("background-color", "#80FF9F");
    }
    $(document).ready(function () {
      $('[data-toggle="popover"]').popover();
    });
  </script>
</head>
<body>

  <div class="login-box">

    <!-- /.login-logo -->
    <div class="row">
      <div class="col-3"> </div>
      <div class="col-6 login-box-body" style="margin-left:300px;">
        <h2 class="login-box-msg" style="margin-left:20px;margin-top:40px;">
          <?php echo ci3lang('changepass_reinitialise');?>
        </h2>
        <div class="login-logo" style="align-content:center">
          <img src="/assets/img/logo.png" width="400" />

        </div>
        <form method="post">
          <div class="form-group  has-feedback"></div>
          <div style="display:<?php echo (($from == 2)? 'none' : 'block');?>" class="form-group">
            <div class="col-sm-4 col-xs-12 control-label">
              <?php echo ci3lang('inscription_adresse_courriel');?>
            </div>
            <div class="col-sm-7 col-xs-12 input-group champ-acces">
              <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">
                <i class="fa fa-at" style="font-size: 24px; margin-top: 10px; margin-left: 15px;"></i>
              </span>
              <input type="email" class="form-control" name="email" id="email" value="<?php echo ($email ?? '');?>" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>" />
            </div>

          </div>
          <div style="display:<?php echo (($from == 2)? 'none' : 'block');?>" class="form-group">
            <div class="col-sm-4 col-xs-12 control-label">
              <?php echo ci3lang('changepass_passw_now');?>
            </div>
            <div class="col-sm-7 col-xs-12 input-group champ-acces">
              <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">
                <i class="fa fa-sign-in" style="font-size: 24px; margin-top: 10px; margin-left: 15px;"></i>

              </span>
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
              <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">
                <i class="fa fa-question" style="font-size:24px;margin-top:10px;margin-left:15px;"></i>

              </span>
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
              <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">
                <i class="fa fa-exclamation" style="font-size:24px;margin-top:10px;margin-left:15px;"></i>
              </span>
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
            <div class="col-sm-4 col-xs-12 control-label"></div><br />
            <div class="col-sm-7 col-xs-12 input-group champ-acces">

              <button type="button" id="btnSubmit" onclick="SubmitChangePass()" class="btn btn-primary btn-block btn-flat" style="width:300px;" disabled>
                <?php echo ci3lang('changepass_send');?>
              </button>
            </div>
          </div>
        </form>

      </div>
    </div>

    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->

  <!-- iCheck -->
  <?php echo view('villes/incfile/inc_javascript'); ?>
  <script>
    $(document).ready(function () {
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
        else if (($('#passchange').val() != '') && ($('#email').val() != '') && ($('#passnew').val() != '')) {
          $('#btnSubmit').prop("disabled", false);
        }
      }
    }
    function ValidStrongPassW() {
      var urlName = "/villes/login/validstrong?password=" + encodeURIComponent($('#passnew').val());
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        if (parseInt(msg) == 0) {
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
      var urlName = "/villes/login/validold?password=" + encodeURIComponent($('#passchange').val()) + '&email=' + encodeURIComponent($('#email').val());
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        if (parseInt(msg) == 0) {
          $('#divValidOld').show();
          $('#passchange').focus();
          $('#passchange').select();
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
      var from = '<?php echo ($from ?? ''); ?>';
      var urlName = '';
      if (from == '2') {
        urlName = "/villes/login/postchange?passnew=" + encodeURIComponent($('#passnew').val()) + '&email=' + encodeURIComponent($('#email').val());
      }
      else {
        urlName = "/villes/login/postchange?passnew=" + encodeURIComponent($('#passnew').val()) + '&passchange=' + encodeURIComponent($('#passchange').val()) +
          '&email=' + encodeURIComponent($('#email').val());
      }
      document.location = urlName;
    }
  </script>
</body>

</html>
