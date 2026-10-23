<?php

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>
    Robert Daoust
  </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
  <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" />

  <?php echo view('sections/heads');?>
  <?php echo view('sections/footers');?>
</head>


<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <header id="navMenuTop" class="main-header">
      <?php echo ($navtop ?? '');?>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree" id="accordionSidebar">
          <?php echo ($navleft ?? '');?>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <!--<section class="content-header">
      <h1>
        Tableau de bord
        
      </h1><small></small>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>-->

      <!-- Main content -->
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
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">@</span>
                  <input type="email" class="form-control" name="email" id="email" placeholder="<?php echo ci3lang('inscription_adresse_courriel');?>" autocomplete="off" readonly
                    onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
                </div>

              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_creer_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">?</span>
                  <input type="password" class="form-control" name="pass" id="pass" placeholder="<?php echo ci3lang('inscription_creer_passw');?>" autocomplete="off" readonly
                    onfocus="this.removeAttribute('readonly');" />
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-xs-12 control-label">
                  <?php echo ci3lang('inscription_valide_passw');?>
                </div>
                <div class="col-sm-7 col-xs-12 input-group champ-acces">
                  <span class="input-group-addon" style="width:50px; background-color:#009ddc; color:#FFF;">!</span>
                  <input type="password" class="form-control" name="passconf" id="passconf" placeholder="<?php echo ci3lang('inscription_valide_passw');?>" data-match="#pass" data-match-error="<?php echo ci3lang('inscription_erreur_passw');?>" required />
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
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 1.0.00
      </div>
      <strong>
        Copyright &copy; 2019
        <a href="https://www.conceptnika.com">Concept nika </a>.
      </strong>
    </footer>


    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div>


</body>
</html>
