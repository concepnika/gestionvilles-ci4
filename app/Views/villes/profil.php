<?php
defined('BASEPATH') or exit('No direct script access allowed');

?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

	<?php echo view('villes/incfile/inc_head'); ?>

</head>
<body id="page-top">

	<?php echo $menu; ?>
	<section role="main" class="content-body content-body-modern mt-0">
		<header class="page-header page-header-left-inline-breadcrumb" style="margin-top:40px;margin-bottom:20px">

			<h2 class="font-weight-bold text-6">Informations de connexion</h2>
			<div id="msgErreur" class="btn btn-danger" style="padding-left:4px;display:none;"></div>
		</header>
		<div class="row">

			<div class="col-lg-2">
			</div>
			<div class="col-lg-8">
				<div class="card-body" style="padding: 40px 10px 10px 10px;">
					<form class="ecommerce-form action-buttons-fixed" action="/villes/post-profil" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-lg-10" style="width:1000px;">
								<div class="input-group">
									<span class="input-group-text">
										<label class="form-control" style="width: 120px">Courriel</label>
									</span>
                  <input type="email" id="email" class="form-control" value="<?php echo $email ?>" readonly />
									<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
								</div>
								<div class="input-group mb-3">
									<span class="input-group-text">
										<label class="form-control" style="width: 120px">Mot de passe</label>
									</span>
									<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" readonly
										onfocus="this.removeAttribute('readonly');" />
								</div>
								<div class="input-group mb-3">
									<span class="input-group-text">
										<label class="form-control" style="width: 120px">Nom</label>
									</span>
									<input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom ?>" />
								</div>
								<div class="input-group mb-3">
									<span class="input-group-text">
										<label class="form-control" style="width: 120px">Prénom</label>
									</span>
									<input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom ?>" />
								</div>
								<div class="input-group mb-3">
									<span class="input-group-text">
										<label class="form-control" style="width: 120px">Téléphone</label>
									</span>
									<input type="tel" name="phone" id="phone" data-plugin-masked-input data-input-mask="(999) 999-9999"  class="form-control" value="<?php echo $phone ?>" />
								</div>
								<div class="input-group mb-3">
									<span class="input-group-text">
										<label class="form-control" style="width: 120px">Cellulaire</label>
									</span>
									<input type="tel" name="cell" id="cell" class="form-control" value="<?php echo $cell ?>" data-plugin-masked-input data-input-mask="(999) 999-9999" />
								</div>
							</div>
						</div>
						<div class="row action-buttons">
							<div class="col-12 col-md-auto">
								<button type="submit" class="btn btn-primary btn-px-4 py-3 d-flex align-items-center font-weight-semibold line-height-1" data-loading-text="Loading..." >
									<i class="bx bx-save text-4 me-2"></i>
									Enregistrer
								</button>
							</div>
							<div class="col-12 col-md-auto px-md-0 mt-3 mt-md-0">
								<a href="/villes/accueil" class="cancel-button btn btn-light btn-px-4 py-3 border font-weight-semibold text-color-dark text-3">Annuler</a>

							</div>

						</div>
					</form>
				</div>
							</div>
						</div>
	</section>
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
	<script>
		//function SubmitProfil() {
		//	var urlName = "/post-profil?id=" + $('#id').val() + '&password=' + encodeURIComponent($('#password').val()) +
		//		'&nom=' + encodeURIComponent($('#nom').val()) + '&prenom=' + encodeURIComponent($('#prenom').val()) +
		//		'&phone=' + $('#phone').val() + '&cell=' + $('#cell').val();
		//	$.ajax({
		//		type: "POST",
		//		url: urlName
		//	}).done(function (data) {
		//		var objData = JSON.parse(data);
		//		$('#msgErreur').html(objData.msgerr);
		//		$("#msgErreur").fadeIn(4000, function () {
		//			$("#msgErreur").fadeOut(6000);
		//		});
		//	});			
		//}
		var typeerr = 'success';
		var msgerr = "<?php echo $msgerror; ?>";
		if (msgerr.trim() != '') {
			if (msgerr == 'Une erreur s\'est produite.') {
				typeerr = 'error';
			}
			$('#msgErreur').html(msgerr);
			$("#msgErreur").fadeIn(4000, function () {
      $("#msgErreur").fadeOut(6000);
      });
   //   new PNotify({
			//	title: 'Vos données',
			//	text: msgerr,
			//	type: typeerr,
			//	hide: true,
			//	delay: 2000,
			//	buttons: {
			//		sticker: false
			//	}
			//});
		}
		var chkRequeteTimer = setInterval(updateInfoRequete, 20000);
	</script>
</body>
</html>
