<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

	<?php echo view('requetes/incfile/inc_head'); ?>

</head>
<body id="page-top">

	<section role="main" class="content-body content-body-modern mt-0">
		<header class="page-header page-header-left-inline-breadcrumb"  style="margin-top:40px;margin-bottom:20px">
			<h2 class="font-weight-bold text-6">Envoi d'une Requête à une Ville</h2>
			<?php echo ($noreq ?? '') ?>
			<div id="msgErreur" class="btn btn-danger" style="padding-left:4px;display:none;">

			</div>
		</header>

		<!-- start: page -->
    <form class="ecommerce-form action-buttons-fixed" action="/send-requete" method="post" enctype="multipart/form-data">
      <div class="row mt-2">
        <div class="col">
          <section class="card card-modern card-big-info">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-2-5 col-xl-1-5">
                  <i class="card-big-info-icon bx bx-camera"></i>
                  <h2 class="card-big-info-title">Télécharger votre fichier</h2>
                </div>
                <div class="col-lg-3-5">
                  <div class="form-group row align-items-center">
                    <input class="form-control" style="width:450px"
                      name="fileRequete" id="fileRequete" type="file" />
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <section class="card card-modern card-big-info">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-2-5 col-xl-1-5">
                  <i class="card-big-info-icon bx bx-slider"></i>
                  <h2 class="card-big-info-title">Détails de la requête</h2>
                  <p class="card-big-info-desc"> Ajoutez ici la description de la catégorie avec tous les détails et informations nécessaires.
                  </p>
                </div>
                <div class="col-lg-3-5 col-xl-4-5">
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Titre de cette requête</label>
                    <div class="col-lg-7 col-xl-6">
                      <input type="text" class="form-control" name="nom" id="nom" value="<?php echo ($nom ?? '') ?>" required />
                      <input type="hidden" name="id" id="id" value="<?php echo ($id ?? '') ?>" />
                      <input type="hidden" name="idville" id="idville" value="<?php echo ($idville ?? '') ?>" />
                      <input type="hidden" name="IsFrom" id="IsFrom" value="1" />
                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Ville</label>

                    <div class="col-lg-7 col-xl-6">
                      <select class="form-control" name="selVilles" id="selVilles" style="width:300px" required>
                        <option value="">Choisir</option>
                        <?php echo ($optVilles ?? '') ?>
                      </select>

                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Adresse</label>

                    <div class="col-lg-7 col-xl-6">
                      <input type="text" class="form-control" name="adresse" id="adresse" value="<?php echo ($adresse ?? '') ?>" required />
                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Matière</label>

                    <div class="col-lg-7 col-xl-6">
                      <select class="form-control" name="matiere" id="matiere" style="width:200px" required>
                        <option value="">Choisir</option>
                        <?php echo ($optMatiere ?? '') ?>
                      </select>

                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Description</label>

                    <div>
                      <textarea name="description" id="description" rows="4" required><?php echo ($description ?? '') ?></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
      <div class="row action-buttons">
        <div class="col-12 col-md-auto">
          <button type="submit" class="submit-button btn btn-primary btn-px-4 py-3 d-flex align-items-center font-weight-semibold line-height-1" data-loading-text="Loading...">
            <i class="bx bx-save text-4 me-2"></i>
            Envoyer la requête
          </button>
        </div>

      </div>
    </form>


		<!-- end: page -->
	</section>


  <?php echo view('requetes/incfile/inc_javascript'); ?>
  <script>
		//----------------------------------------------------------


		//----------------------------------------------------------
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
				hide: true,
				buttons: {
					sticker: false
				}
			});
		}
	</script>
</body>
</html>
