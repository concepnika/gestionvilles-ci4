<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

  <?php echo view('villes/incfile/inc_head'); ?>

</head>
<body id="page-top">
  <?php echo ($menu ?? ''); ?>
	<section role="main" class="content-body content-body-modern mt-0">
		<header class="page-header page-header-left-inline-breadcrumb"  style="margin-top:40px;margin-bottom:20px">
			<h2 class="font-weight-bold text-6">Envoi d'une Requête à une Ville</h2>
			<?php echo ($noreq ?? '') ?>
      <div id="msgErreur" class="btn btn-danger" style="padding-left:4px;display:none;">

      </div>
    </header>

    <!-- start: page -->
    <form class="ecommerce-form action-buttons-fixed" action="/villes/post/requete" method="post" enctype="multipart/form-data">
      <div class="row mt-2">
        <div class="col">
          <section class="card card-modern card-big-info">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-2-5 col-xl-1-5">
                  <i class="card-big-info-icon bx bx-camera"></i>
                  <h2 class="card-big-info-title">Télécharger vos fichiers si requis</h2>
                </div>
                <div class="col-lg-3-5">
                  <div class="form-group row align-items-center">
                    <input class="form-control" style="width:450px"
                      name="fileRequete_1" id="fileRequete_1" type="file" />
                  </div>
                  <div class="form-group row align-items-center">
                    <input class="form-control" style="width:450px"
                      name="fileRequete_2" id="fileRequete_2" type="file" />
                  </div>
                  <div class="form-group row align-items-center">
                    <input class="form-control" style="width:450px"
                      name="fileRequete_3" id="fileRequete_3" type="file" />
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
                  <p class="card-big-info-desc">
                    Ajoutez ici la description de la catégorie avec tous les détails et informations nécessaires.
                  </p>
                </div>
                <div class="col-lg-3-5 col-xl-4-5">
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Titre de cette requête</label>
                    <div class="col-lg-7 col-xl-6">
                      <input type="text" class="form-control" name="nom" id="nom" value="<?php echo ($nom ?? '') ?>" required />
                      <input type="hidden" name="id" id="id" value="<?php echo ($id ?? '') ?>" />
                      <span style="display:none" id="nomVille"></span>
                      <input type="hidden" name="IsFrom" id="IsFrom" value="1" />
                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Ville</label>

                    <div class="col-lg-7 col-xl-6">
                      <select class="form-control" onclick="setNomVille(this.value)" name="idville" id="idville" style="width:300px" required>
                        <option value="">Choisir</option>
                        <?php echo ($optVilles ?? '') ?>
                      </select>

                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Adresse</label>
                    <div class="input-group" style="width:550px;">

                      <textarea name="adresse" placeholder="Adresse, Code postal" id="adresse" rows="3" cols="40" style="height:100%;" required></textarea>
                      <a href="javascript:GoogleVille()" style="width:35px;height:35px;">
                        <img src="/assets/img/googlemap.png" height="35" />
                      </a>
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

                    <div class="col-lg-7 col-xl-6">
                      <textarea class="form-control" name="description" id="description" rows="12" cols="75" required>
                        <?php echo ($description ?? '') ?>
                      </textarea>
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
        <!--<div class="col-12 col-md-auto px-md-0 mt-3 mt-md-0">
          <a href="/accueil" class="cancel-button btn btn-light btn-px-4 py-3 border font-weight-semibold text-color-dark text-3">Fermer</a>

        </div>-->

      </div>
    </form>



		<!-- end: page -->
	</section>


  <?php echo view('villes/incfile/inc_javascript'); ?>

 <script>
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
