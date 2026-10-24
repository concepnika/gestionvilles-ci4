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
        <div class="container-fluid">
                <h3 class="text-dark mb-4">Liste des usagers</h3>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">

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
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
    </div>
</body>
</html>
