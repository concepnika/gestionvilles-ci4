<?php

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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

      <section id="secContent" class="content">

        <!------ FORMULAIRE --------------------------------------------------------------------------------------------->

        <div class="container">

          <div class="section_standard">

            <h2>
              <?php echo ci3lang('inscription_compte');?>
            </h2>

            <div class="row">
              <div class="col-xs-12" style="text-align:center; margin:0px 0 50px 0;">
                <h1>
                  <?php echo ci3lang('inscription_adhesion');?>
                </h1>
              </div><!-- Fin col-12-xs -->
            </div><!--Fin row #1 -->
            <form  class="form-horizontal" action="/admin/usager/ajout" method="post" role="form">

              <!-- Identification -->

              <div class="form-group B10">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12 titre">
                  <?php echo ci3lang('inscription_identification');?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-6 col-xs-11 texte">
                  <?php echo ci3lang('inscription_text_1');?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_adresse_courriel');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">
                    <i class="fa fa-at" style="font-size: 24px; margin-top: 10px; margin-left: 15px;"></i>
                  </span>
                  <input type="email" class="form-control" name="email" id="email" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>"  autocomplete="off" readonly
            onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
                </div>

              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_creer_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">
                    <i class="fa fa-question" style="font-size: 24px; margin-top: 10px; margin-left: 15px;"></i>
                  </span>
                  <input type="password" class="form-control" name="pass" id="pass" placeholder="<?php echo ci3lang('inscription_creer_passw');?>"  autocomplete="off" readonly
            onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
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
                  <input type="password" class="form-control" id="passconf" placeholder="<?php echo ci3lang('inscription_valide_passw');?>" data-match="#pass" data-match-error="<?php echo ci3lang('inscription_erreur_passw');?>" required />
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
                  <input type="text" class="form-control" name="prenom" id="prenom" required />
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_nom');?>
                </div>
                <div class="col-sm-7 col-xs-12">
                  <input type="text" class="form-control" name="nom" id="nom" required />
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4 col-xs-12"></div>
                <div class="col-sm-7 col-xs-12">
                  <button type="submit" class="btn btn-principal" style="outline:none">
                    <?php echo ci3lang('inscription_creer_compte');?>
                  </button>
                </div>
              </div>

              <!-- Fin information personnelle -->





            </form>


          </div><!-- Fin section standard -->
        </div><!-- Fin container -->
      </section>

  <?php echo view('incfile/inc_javascript'); ?>
  <script>
    
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

  </script>
    </div>
</body>
</html>
