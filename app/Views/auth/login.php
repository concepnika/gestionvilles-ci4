<?php
// Port de partials/login2.php (Legacy). Necessite helper('gv').
// $error    : message d'erreur de connexion ('' si aucun)
// $reset    : message apres reinitialisation du mot de passe ('' si aucun)
// $activeDate : date du jour (Y-m-d)
// $next     : cible post-login (champ ignore par Legacy, conserve pour plus tard)
$error      = $error ?? '';
$reset      = $reset ?? '';
$activeDate = $activeDate ?? date('Y-m-d');
$siteLang   = gv_sess('site_lang') ?? 'fr';
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
  <script type="text/javascript" src="/assets/vendor5/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/assets/vendor5/bootstrap.min.css" />
  <script type="text/javascript" src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/vendor5/bootstrap.min.css" />
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
  <link rel="stylesheet" href="/assets/vendor5/bootstrap.min.css" />
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
  </style>
  <script type='text/javascript'>
    var ActiveDate = "<?= esc($activeDate) ?>";
    function ConfirmeLoad(Route)
    {
        $("#td_" + Route).css("background-color","#80FF9F");
        $("#chk_" + Route).css("background-color","#80FF9F");
    }
    $(document).ready(function () {
        $('[data-bs-toggle="popover"]').popover();
    });
  </script>
</head>
<body class="hold-transition login-page">
  <div class="login-box" style="width:400px;">
    <div class="login-logo">
      <img width="400" src="/assets/img/logo.png" />
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body" >
        <h2 style="text-align:center;color:orange;font-weight:900">Gestion des villes</h2>
      <p class="login-box-msg" style="margin-top:10px; font-weight:bold">Connectez-vous pour d&eacute;buter votre session</p>

      <form action="/admin/tableau_de_bord?from=2" method="post">
        <div class="form-group has-feedback">
          <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="<?= esc(gv_lang('login_email')) ?>" name="email" />
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="form-control" id="password" placeholder="<?= esc(gv_lang('login_password')) ?>" name="password" />
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div style="border-color:red; border:double;padding-left:4px;display:<?= ($error !== '' ? 'block' : 'none') ?>;">
          <label style="font-size:small;color:darksalmon;">
            <?= esc($error) ?>
          </label>
        </div>
        <div class="row" style="margin-top:10px;">

          <!-- /.col -->
          <div class="col-4 float-end">
            <button type="submit" class="btn btn-primary d-block w-100 btn-flat">
              <?= esc(gv_lang('login_connect')) ?>
            </button>
          </div>
          <div class="col-12 float-start">
            <button type="button" onclick="getEmail()">
              <?= esc(gv_lang('login_forget')) ?>
            </button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box-body -->
  <!-- /.login-box -->
  <div class="modal fade confirmation" id="modalgetEmail">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h3 class="modal-title" id="mytitre">
            <?= ($siteLang === 'fr' ? 'Confirmation' : 'Confirmation') ?>
          </h3>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="card-text"></div>
          <?= esc(gv_lang('inscription_adresse_courriel')) ?>
          <input type="email" class="form-control" name="mailModal" id="mailModal" value="" placeholder=" <?= esc(gv_lang('inscription_adresse_courriel')) ?> " />
        </div>

        <!-- Modal footer -->
        <div class="modal-footer" style="background-color:Window;">
          <button type="button" id="btnConfirme" class="btn btn-sm btn-warning" onclick="SendInfo()" data-bs-dismiss="modal">
            <?= ($siteLang === 'fr' ? "Confirmer l'envoi" : 'Confirm shipment') ?>
          </button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
        </div>

      </div>
    </div>
  </div>
  <!-- jQuery 3 -->
  <script src="/assets/bower_components/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="/assets/vendor5/bootstrap.bundle.min.js"></script>
  <!-- iCheck -->
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
  <script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  function getEmail() {
	  $("#modalgetEmail").modal('show');
  }
  function SendInfo() {
    document.location = "/inscription/getinfo?email=" + $("#mailModal").val();

  }
    var isResetPass = '<?= esc($reset) ?>';
    if (isResetPass.trim() != '') {

      alert(isResetPass);

    }
  </script>
</html>
