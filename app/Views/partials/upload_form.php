<?php

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>
    <?php echo ci3lang('home_top');?>
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
      <small></small>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>-->


      <!-- Main content -->
      <section id="secContent" class="content">
        <div class="container">
          <h2>
            Chargement
          </h2>

          <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
              
                <h4>
                  <?php echo ($route ?? ''); ?>
                </h4>


              <?php echo ($error ?? ''); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
              <form method="post" class="form-horizontal" action="/charger/<?php echo ($table ?? ''); ?>" enctype="multipart/form-data">

                <div class="form-group">
                  <div class="box-header">
                    <h3 class="box-title">Sélectionner les champs requis du fichier</h3>
                  </div>
                  <input type="file" name="userfile"  size="33" />
                </div>
                <br />
                <br />

                <input type="submit" class="btn btn-primary" value="Charger" />
              </form>
            </div>
          </div>
          <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
              <br />
              <br />
            </div>
          </div>
          <!--<div class="row">
            <div class="col-3"></div>
            <div class="col-6">
              <div class="box box-success">
                <div class="box-header">
                  <h3 class="box-title">Sélectionner les champs requis du fichier</h3>
                </div>
                <div class="box-body">


                 
                  <div class="form-group">

                    <label>
                      <input type="checkbox" class="flat-red" checked />
                      Flat green skin checkbox
                    </label>
                  </div>

                </div>
                
                <div class="box-footer">
                  Many more skins available.
                  <a href="http://fronteed.com/iCheck/">Documentation</a>
                </div>
              </div>
            </div>
          </div>-->


        </div>
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
