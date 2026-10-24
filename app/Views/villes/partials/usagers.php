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
    <div class="wrapper">

      

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

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
                    </div>
                </div>
            </div>
        </div>


        </section>
        <!-- /.content -->
      </div>

    </div>
    <?php echo view('incfile/inc_javascript'); ?>
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
