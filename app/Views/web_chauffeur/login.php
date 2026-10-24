<?php  ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaniVan Web – Connexion</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/font-awesome/css/font-awesome.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Source Sans Pro', sans-serif;
        }
        /* ─── Reproduit le Grid grdConnect de MainPage.xaml ─── */
        .login-grid {
            width: 420px; background: #fff; border-radius: 0;
            box-shadow: 0 4px 24px rgba(0,0,0,.3);
            overflow: hidden;
        }
        .login-grid table {
            width: 100%; border-collapse: collapse;
        }
        .login-grid td {
            border: 1px solid #000; padding: 8px 12px;
            vertical-align: middle;
        }
        .login-grid td.label-cell {
            width: 50%; font-size: 18px; font-weight: normal;
            background: #fff;
        }
        .login-grid td.input-cell {
            width: 50%;
        }
        .login-grid input.form-control {
            font-size: 18px; border: none; box-shadow: none;
            padding: 6px 4px; height: auto;
        }
        .login-grid input.form-control:focus {
            outline: none; box-shadow: none;
        }
        .login-grid .logo-row td {
            text-align: center; padding: 12px;
        }
        .login-grid .spacer-row td {
            height: 60px;
        }
        .login-grid .btn-connecter {
            width: 100%; background: DarkBlue; border: none;
            color: #fff; font-size: 18px; padding: 12px;
            cursor: pointer;
        }
        .login-grid .btn-connecter:hover {
            background: #00008B; opacity: .9;
        }
        /* Spinner wait (même logique que gifWait) */
        #gifWait {
            display: none; text-align: center; padding: 40px;
        }
        #gifWait img.logo { width: 250px; margin-bottom: 10px; }
        #gifWait .spinner { margin: 15px auto; }
        #gifWait .wait-text {
            color: red; font-weight: bold; font-size: 18px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
    </style>
</head>
<body>

<!-- ═══ Formulaire grdConnect (reproduit MainPage.xaml) ═══ -->
<div class="login-grid" id="grdConnect">
    <table>
        <!-- Row 0 : Logo -->
        <tr class="logo-row">
            <td colspan="2" style="border:1px solid #000; padding:12px;">
                <img src="/assets/img/logo.jpg" alt="SaniVan" style="width:250px;" />
            </td>
        </tr>
        <!-- Row 1 : Spacer -->
        <tr class="spacer-row">
            <td colspan="2" style="border:1px solid #000;">&nbsp;</td>
        </tr>

        <?php if ( ! empty($error)): ?>
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:8px;">
                <div class="alert alert-danger" style="margin:0; font-size:14px;">
                    <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            </td>
        </tr>
        <?php endif; ?>

        <!-- Row 2 : Camions -->
        <tr>
            <td class="label-cell">Camions:</td>
            <td class="input-cell">
                <input type="text" class="form-control" onblur="" id="nocamion" name="nocamion"
                       placeholder="" autofocus />
            </td>
        </tr>
        <!-- Row 3 : KM de Départ -->
        <tr>
            <td class="label-cell">KM de Départ:</td>
            <td class="input-cell">
                <input type="text" class="form-control" id="kmdepart" name="kmdepart"
                       placeholder="" inputmode="decimal" />
            </td>
        </tr>
        <!-- Row 4 : Code usager -->
        <tr>
            <td class="label-cell">Code usager:</td>
            <td class="input-cell">
                <input type="text" class="form-control" id="code_user" name="code_user"
                       placeholder="" />
            </td>
        </tr>
        <!-- Row 5 : Mot de passe -->
        <tr>
            <td class="label-cell">Mot de passe:</td>
            <td class="input-cell">
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="" />
            </td>
        </tr>
        <!-- Row 6 : Bouton Connecter -->
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:0;">
                <button type="button" class="btn-connecter" id="btnConnecter">Connecter</button>
            </td>
        </tr>
    </table>
</div>

<!-- ═══ Spinner wait (reproduit gifWait) ═══ -->
<div id="gifWait">
    <img class="logo" src="/assets/img/logo.jpg" alt="SaniVan" /><br/>
    <img class="spinner" src="/assets/img/wait.gif" alt="Chargement..." style="height:80px;" /><br/>
    <span class="wait-text">Chargement en cours...</span>
</div>

<!-- ═══ Formulaire caché pour POST ═══ -->
<form id="loginForm" method="post" action="/web/doLogin" style="display:none;">
    <input type="hidden" name="nocamion"  id="h_nocamion" />
    <input type="hidden" name="kmdepart"  id="h_kmdepart" />
    <input type="hidden" name="code_user" id="h_code_user" />
    <input type="hidden" name="password"  id="h_password" />
</form>

<script src="/assets/pronto/vendor/jquery/jquery.js"></script>
<script>
$(function(){
    // ─── Reproduit les événements MAUI OnCamCompleted, OnKmCompleted, etc. ───

    var $cam  = $("#nocamion"),
        $km   = $("#kmdepart"),
        $code = $("#code_user"),
        $pass = $("#password");

    // OnCamCompleted : pad "0" si < 3 chars, puis charger GETMAXENDKILO
    $cam.on("change blur", function(){
        var v = $.trim($cam.val());
        if(v.length > 0 && v.length < 3) {
            v = "0" + v;
            $cam.val(v);
        }
        if(v.length >= 2){
            // AJAX GETMAXENDKILO (même appel que MainPage.GetKMDepart)
            $.getJSON("/web/api_getmaxkilo?nocamion=" + encodeURIComponent(v), function(d){
                if (d.km) $km.val(d.km);
            });
        }
    });

    // Navigation par Enter (Tab flow: Cam → KM → Code → Pass → submit)
    $cam.on("keydown", function(e){ if(e.which===13){ e.preventDefault(); $cam.trigger("change"); $km.focus(); }});
    $km.on("keydown",  function(e){ if(e.which===13){ e.preventDefault(); $code.focus(); }});
    $code.on("keydown", function(e){ if(e.which===13){ e.preventDefault(); $pass.focus(); }});
    $pass.on("keydown", function(e){ if(e.which===13){ e.preventDefault(); doSubmit(); }});

    // Bouton Connecter
    $("#btnConnecter").on("click", function(){ doSubmit(); });

    function doSubmit(){
        if(!$.trim($pass.val())) return;

        // Même logique que OnButtonClicked/OnPassCompleted
        $("#grdConnect").hide();
        $("#gifWait").show();

        // Copier vers hidden form
        $("#h_nocamion").val($.trim($cam.val()));
        $("#h_kmdepart").val($.trim($km.val()));
        $("#h_code_user").val($.trim($code.val()));
        $("#h_password").val($pass.val());

        // POST
        $("#loginForm").submit();
    }

    // Pré-remplir le camion sauvegardé (localStorage = Preferences MAUI)
    var savedCam = localStorage.getItem("sani_cle_cam");
    if(savedCam) $cam.val(savedCam);
    var savedChauf = localStorage.getItem("sani_cle_chauf");
    if(savedChauf) $code.val(savedChauf);

    // Sauvegarder les prefs au submit
    $("#loginForm").on("submit", function(){
        localStorage.setItem("sani_cle_cam",  $.trim($cam.val()));
        localStorage.setItem("sani_cle_chauf", $.trim($code.val()));
    });
});
</script>
</body>
</html>
