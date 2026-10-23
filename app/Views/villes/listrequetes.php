<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

	<?php echo view('villes/incfile/inc_head'); ?>
  <link rel="stylesheet" href="/assets/vendor/datatables/media/css/dataTables.bootstrap5.css" />



</head>
<body id="page-top">

	<?php echo ($menu ?? ''); ?>
  <section role="main" class="content-body content-body-modern mt-0">
    <header class="page-header page-header-left-inline-breadcrumb"  style="margin-top:40px">

      <div class="input-group" style="width:1250px;">
        <h2 class="font-weight-bold text-6">
          Requêtes&nbsp;&nbsp;
        </h2>

        <a href="#" style="height:32px;width:32px;margin-left:5px;margin-top:0px" onclick="PrintRequete()">

          <i class="fa fa-file-pdf" style="font-size:34px;color:red;height:32px;width:33px;"></i>
        </a>&nbsp;&nbsp;
        <!--<a href="#" onclick="PrintExcelRequete()" style="margin-top:-5px">
            <img src="/assets/img/excel.png" width="47" />
          </a>-->

        <label class="input-group-text border-start-0 border-end-0 rounded-0" style="width:185px;height:35px;font-weight:bold;">Afficher les traitées</label>
        <input type="checkbox" id="chkShowTraitees" onchange="showHideVillesTraitees('<?php echo ci3sess('site_lang'); ?>')" style="width:35px;height:35px;margin-top:0px;" />&nbsp;&nbsp;

        <span class="input-group-text border-start-0 border-end-0 rounded-0" style="height:35px"> De
        </span>
        <input type="date" class="form-control" style="width:130px;height:35px" name="start" id="start" onchange="showHideVillesTraitees(<?php echo ci3sess('site_lang'); ?>)" />
        <span class="input-group-text border-start-0 border-end-0 rounded-0" style="height:35px"> Au
        </span>
        <input type="date" class="form-control" style="width:130px;height:35px"
          name="end" id="end" onchange="showHideVillesTraitees(<?php echo ci3sess('site_lang'); ?>)" />



      </div>
      <input type="hidden" id="idville" value="<?php echo ($idville ?? ''); ?>" />
    </header>
		<div class="row">
			<div class="col-lg-1"></div>
			<div class="col-lg-12  box box-info">
				<section class="panel panel-featured-left panel-featured-primary">
					<div class="panel-body">
						<section class="card">
                            <div class="alert alert-secondary py-2" role="alert">
                                <span class="badge-origin ville">Ville</span>
                                <span class="badge-origin rdf">RDF</span>
                              <span class="badge bg-light text-dark" style="border:1px solid #ddd;">Fond rose</span> <!--= non vues (Ville)-->
                            </div>
							<div class="card-body">
								<table class="table table-bordered table-striped mb-0">
                  <thead>
                    <tr>
                      <th align="center"><i class="fa fa-users" style="font-size:24px"></i></th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">Requérant</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">#Requête</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">Date Cédulée / Créée</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">Titre</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">Adresse</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">Matériel</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">description</th>
                      <th align="center" style="text-transform: uppercase;text-align:center;">Statut</th>

                    </tr>
                  </thead>
                  <tbody id="tboDelCom">
                    <?php echo ($listrequestes ?? ''); ?>
                  </tbody>
                </table>

              </div>
            </section>
          </div>
        </section>
      </div>
    </div>

    <!-- end: page -->
  </section>
  <div class="modal fade" id="showDownloadRepPDF" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header btn-danger">
          <h1 id="mytitre" class="modal-title">Votre Liste des requêtes</h1>
          <button type="button" class="close" onclick="closePrintReq()">&times;</button>
        </div>
        <div>
          <p class="card-text" style="margin-left: 10px;"></p>
        </div>
        <!-- Modal body -->
        <div id="divModalPDF" class="modal-body" style="margin-left: 14px;font-style:italic;font-size:large"></div>

        <!-- Modal footer -->
        <div class="modal-footer" style="background-color:Window;">
          <button type="button" id="btnConfirme" class="btn btn-default" onclick="closePrintReq()" data-dismiss="modal">Terminer</button>
        </div>

      </div>
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
  <?php echo view('villes/incfile/inc_javascript'); ?>




  <script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js"></script>
  <script src="/assets/pronto/vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js"></script>
  <script src="/assets/pronto/js/theme.js"></script>

  <script src="/assets/pronto/js/examples/examples.datatables.default.js"></script>
  <script src="/assets/pronto/js/examples/examples.datatables.tabletools.js"></script>



	<script>
		var id = <?php echo ($id ?? ''); ?>;
    //
    var elementfourn = document.getElementById("trreq_" + $('#id').val());
    elementfourn.scrollIntoView(true);
    var typeerr = 'success';
		var msgerr = "<?php echo ($msgerror ?? ''); ?>";
		if (msgerr.trim() != '') {
			if (msgerr == 'Une erreur s\'est produite.') {
				typeerr = 'error';
			}
			new PNotify({
				title: 'Vos données',
				text: msgerr,
				type: typeerr,
				addclass: 'icon-nb'
			});
    }
    
    var chkRequeteTimer = setInterval(updateInfoRequete, 20000);
	</script>
</body>
</html>
