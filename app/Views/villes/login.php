<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<?php echo view('villes/incfile/inc_head');?>

	</head>
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a href="/" class="logo float-start" style="margin-top:13px">
					<img src="/assets/img/logo.png" height="70" alt="Robert Daoust" />
				</a>

				<div class="panel card-sign">
					<div class="card-title-sign mt-3 text-end">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i>Connectez-vous</h2>
					</div>
					<div class="card-body">
						<form action="" method="post">
							<div style="border-color:red; border:double;padding-left:4px;display:<?php echo (($msgerror != '') ? 'block' : 'none'); ?>;">
								<label style="font-size:small;color:darksalmon;">
									<?php echo $msgerror; ?>
								</label>
							</div>
							<div class="form-group mb-3">
								<label>Courriel</label>
								<div class="input-group">
									<input name="email" id="email" type="email" class="form-control form-control-lg" />
									<span class="input-group-text">
										<i class="bx bx-user text-4"></i>
									</span>
								</div>
							</div>

							<div class="form-group mb-3">
								<div class="clearfix">
									<label class="float-start">Mot de passe</label>
									<a href="/admin/pwd/reset" class="float-end">Mot de passe oublié?</a>
								</div>
								<div class="input-group">
									<input name="password" id="password" type="password" class="form-control form-control-lg" />
									<span class="input-group-text">
										<i class="bx bx-lock text-4"></i>
									</span>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-8">
								</div>
								<div class="col-sm-4 text-end">
									<button type="button" class="btn btn-primary mt-2" onclick="gotoTDB()">Connecter</button>
								</div>
							</div>


						</form>
					</div>
				</div>

				<p class="text-center text-muted mt-3 mb-3">&copy; Copyright 2024. Concept nika.</p>
			</div>
		</section>
		<!-- end: page -->
		<?php echo view('villes/incfile/inc_javascript');?>
	</body>
</html>
