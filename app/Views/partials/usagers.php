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
    <div id="wrapper">


            <div class="container-fluid">
                <h3 class="text-dark mb-4">Liste des employés</h3>
                <div class="card shadow">
                    <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold">Employés</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-nowrap">
                                <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable"><label>Show&nbsp;<select class="form-control form-control-sm custom-select custom-select-sm"><option value="10" selected="">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>&nbsp;</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-right dataTables_filter" id="dataTable_filter"><label><input type="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search"></label></div>
                            </div>
                        </div>
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                            <table class="table dataTable my-0" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Courriel</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php echo ($tbody ?? '');?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Nom</strong></td>
                                        <td><strong>Prénom</strong></td>
                                        <td><strong>Courriel</strong></td>
                                        <td><strong></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!--<div class="row">
                            <div class="col-md-6 align-self-center">
                                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">Showing 1 to 10 of 27</p>
                            </div>
                            <div class="col-md-6">
                                <nav class="d-lg-flex justify-content-lg-end dataTables_paginate paging_simple_numbers">
                                    <ul class="pagination">
                                        <li class="page-item disabled"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
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
    <script>
      function removeUser(id) {
        var urlName = "/admin/supprimer/" + id;
    		$.ajax({
    			async: false,
    			type: "POST",
    			url: urlName
    		}).done(function(msg){
    			document.location = "/admin/liste_usagers" ;
    		});
	             
      }
    </script>

  </body>
</html>
