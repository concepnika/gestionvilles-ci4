<?php

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" /><title>Condo phase 3</title>
  <?php echo view('incfile/inc_head'); ?>
  <style>
    th{text-align: center;}
  </style>
</head>
<body>
  <?php echo ($menu ?? ''); ?>
  <div class="wrapper" style="margin-top: 5%;">



    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!------ FORMULAIRE --------------------------------------------------------------------------------------------->
        <header class="page-header">

        </header>
            <div class="row">
              <div class="col-2">
              </div>
              <div class="col-8">
            <h2>Modifications au profil</h2>        
            <div id="msgErreur" class="form-group btn-danger" style="display:none;width:400px;height:30px;text-align:center;"></div>

            <form id="frmUsers" name="frmUpdateUser" class="form-horizontal" action="" method="post" role="form">

              <!-- Identification -->

              <div class="form-group B10">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('inscription_identification');?>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_adresse_courriel');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">@</span>
                  <input type="email" class="form-control" name="email" id="email" value="<?php echo ($emailmodif ?? '');?>" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>" disabled />
                  <div class="help-block with-errors"></div>
                </div>

              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_creer_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">?</span>
                  <input type="password" class="form-control" name="pass" id="pass" value="" placeholder="<?php echo ci3lang('inscription_creer_passw');?>" />
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_valide_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">!</span>
                  <input type="password" class="form-control" name="passconf" id="passconf" placeholder="<?php echo ci3lang('inscription_valide_passw');?>" data-match="#pass" data-match-error="<?php echo ci3lang('inscription_erreur_passw');?>" autocomplete="off" 
                          onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
                </div>
              </div>

              <!-- Fin identification -->


              <!-- Information personnelle -->

              <div class="form-group T40 B20">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('inscription_info_perso');?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  Privilège
                </div>
                <div class="col-sm-7 col-xs-12">
                  <select class="form-control" name="privilege" id="privilege" required>
                      <option value="0">Choisir</option>
                      <?php echo ($privilege ?? ''); ?>          
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_prenom');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" value="<?php echo ($prenommodif ?? '');?>" name="prenom" id="prenom" required />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_nom');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="nom" value="<?php echo ($nommodif ?? '');?>" id="nom" required />
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12">
                  <button type="button" class="btn btn-warning" onclick="document.location='/admin/liste_usagers'">
                    Retour à la liste
                  </button>
                  <button type="button" class="btn btn-success" onclick="ModifieUsager(<?php echo ($id ?? '');?>)">
                    Mettre à jour
                  </button>
                  <button type="button" class="btn btn-danger" onclick="DeleteUsager(<?php echo ($id ?? '');?>)">
                    Supprimer
                  </button>
                </div>
              </div>

              <!-- Fin information personnelle -->
            </form>
                </div><!--Fin row #1 -->
      <!-- /.content -->
    </div>
  </div>
  <?php echo view('incfile/inc_javascript'); ?>

  <script>
    selectedDay = "<?php echo ($ActiveDate ?? '') ?>";

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
    //mySessionCheck = setTimeout( "checkSession()", 20 * 60 * 1000 );
  </script>

</body>
</html>
