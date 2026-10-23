<?php

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <title><?php echo ci3lang('home_top');?></title>
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css?h=8d42cb70c4021d3148d6e4bd41b5c0dd">
	<?php echo view('sections/heads');?>  


</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-tachometer-alt"></i></div>
                    <div class="sidebar-brand-text mx-3"><span><?php echo ci3lang('home_top');?></span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="nav navbar-nav text-light" id="accordionSidebar">
									<?php echo ($navleft ?? '');?>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
			<nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
				<div class="container-fluid">
					<button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop" type="button">
						<i class="fas fa-bars"></i>
					</button>

					<ul class="nav navbar-nav flex-nowrap ml-auto" id="navMenuTop">
						<?php echo ($navtop ?? '');?>
					</ul>
				</div>
			</nav>
      <div id="content">
				         
			</div>
			<footer class="bg-white sticky-footer">
				<div class="container my-auto">
					<div class="text-center my-auto copyright"><span>Copyright © Concept nika 2019</span></div>
				</div>
			</footer>
		</div>
		<a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fa fa-angle-up"></i></a>
	</div>
	<?php echo view('sections/footers');?>
</body>

</html>
