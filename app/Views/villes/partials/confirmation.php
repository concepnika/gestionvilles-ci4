<?php

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <title>Robert Daoust</title>
  <?php echo view('villes/incfile/inc_head'); ?>
  <style>
    th {
      text-align: center;
    }

  </style>
</head>
<body>

  <div class="login-box">
    <section class="container-fluid theme" style="margin-top:240px;">
      <div class="row">
        <div class="col-4"></div>
        <div class="col-6">
          <div class="container">
            <div class="login-logo" style="align-content:center">
              <img src="/assets/img/logo.png" width="400" />

            </div>

              <div style="margin-top:20px;">
                <h1 class="wow fadeIn" data-wow-delay="1500ms" data-wow-duration="2000ms">
                  Modification du mot de passe
                </h1>
              </div>
              <div style="width:500px">
                <h2 style="margin-left:30px;color:red">Erreur</h2>
                <p style="font-weight:bold;font-size:large">Le d&eacute;lai pour la r&eacute;initialisation est expir&eacute; ou l'information de validation est invalide. </p>
              </div>
            </div>

          </div>
        </div>

    </section>

  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->
  <?php echo view('villes/incfile/inc_javascript'); ?>
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
        else if (($('#passchange').val() != '') && ($('#email').val() != '') && ($('#passnew').val() != '')) {
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
      $.ajax({
        async: false,
        type: "POST",
        url: urlName
      }).done(function (msg) {
        if (msg == "Une erreur s'est produite.") {
          alert(msg);
        }
        else {
          document.location = "/villes/accueil";
          //-------------------
        }
      });
      
    }
  </script>
</body>

</html>
