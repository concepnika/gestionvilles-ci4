<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

  <?php echo view('villes/incfile/inc_head');?>

</head>
<body id="page-top">

                 <?php echo ($menu ?? ''); ?>
  <div style="margin-top:5%;width:100%">
     <div style="margin-top:25%;width:100%">
        <?php echo ($map_nav ?? ''); ?> 
     </div>
</div>
 	  <div class="modal fade" id="newActionModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Réponse - Requête</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div id="divRepNewAjout" class="modal-body">
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
      </div>

    </div>
  </div>
</div>
  <?php echo view('villes/incfile/inc_javascript');?>
  <script>
    var chkRequeteTimer = setInterval(updateInfoRequete, 20000);
  </script>
</body>
</html>
