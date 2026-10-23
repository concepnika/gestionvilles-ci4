
<?php

?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Robert Daoust</title>

		<meta name="keywords" content="Robert Daoust" />
		<meta name="description" content="Sanivan routes - Responsive HTML5 Template">
		<meta name="author" content="Conceptnika">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<?php echo view('requetes/incfile/inc_head'); ?>
    <style>

      ul li {
        display: inline;
      }
        /*
        * Always set the map height explicitly to define the size of the div element
        * that contains the map.
        */
      #map {
        height: 500px;
        width: 1500px;
      }

      /*
        * Optional: Makes the sample page fill the window.
        */
      .test {
        height: 35px;
        width: 200px;
        font-size: 16px;
      }

      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: "Roboto", "sans-serif";
        line-height: 30px;
        padding-left: 10px;
      }
    </style>


    
    <script src="/assets/js/track_san.js"></script>
    <script type="text/javascript">

      //-------------------------------------------------------
   
      var selectDepotoire = '';
      var base_url = "<?php echo ci3_url() ?>";
      var ActiveDate = "<?php echo date("Y-m-d") ?>";
      $(document).ready(function () {
          $('[data-toggle="popover"]').popover();
      });
  </script>
  </head>
<body onfocus="RefreshTrack()">
  <section class="body">

    <!-- start: header -->
    <header class="header" style="margin-bottom:0px;">
      <div class="logo-container">
        <a href="../4.1.0" class="logo">
          <img src="/assets/img/logo.png" width="75" height="35" alt="Robert Daoust" />

        </a>
        <div class="header-nav collapse">
          <div class="header-nav-main header-nav-main-effect-1 header-nav-main-sub-effect-1 header-nav-main-square">
            <nav>
              <ul class="nav nav-pills" id="mainNav">
                <li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
                <?php echo ($nav_tabs ?? ''); ?>

                <li> &nbsp;&nbsp;&nbsp;&nbsp;</li>
                <li>
                  <input type="date" class="form-control" style="height:28px;" onchange="loadSaniDay()" value="<?php echo date("Y-m-d") ?>" id="dteActive" />
                  <input type="hidden" id="idActiveCedule" value="<?php echo ($idcedule ?? ''); ?>" />
                  <input type="hidden" id="idcamjour" value="<?php echo ($idcamionjour ?? ''); ?>" />
                  <input type="hidden" id="idsanivanrh" value="<?php echo ($idsanivanrh ?? ''); ?>" />
                  <input type="hidden" id="idinfoville" value="<?php echo ($idinfoville ?? ''); ?>" />
                  <input type="hidden" id="idrh" value="<?php echo ($idrh ?? ''); ?>" />
                </li>
                <li> &nbsp;&nbsp;&nbsp;&nbsp;</li>
                <li style="display:<?php echo (($idrh == 27 or $idrh == 23 or $idrh == 76) ? 'block' : 'none'); ?>">
                  <a href="/sanivan/aujourdhui/1/<?php echo date("Y-m-d")?>" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Cédule" data-content="Affiche la gestion des cédules.">
                    <i class='fa fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Cédule
                  </a>
                  
                </li>
                <li> &nbsp;&nbsp;&nbsp;&nbsp;</li>
                <li>
                  <a style="color: red; margin-top: -5px" href="/tablettes_san/quitter">

                    <i class="fa fa-user" style="font-size:28px;"></i> Quitter
                  </a>

                </li>

              </ul>
            </nav>
          </div>
        </div>
      </div>

    </header>
    <!-- end: header -->

    <div class="inner-wrapper" style="margin-left:10px;margin-top:15px">

      <section style="margin-left:10px;">


        <div class="row">
          <div class="col">
            <section>
              <div class="card-body" style="min-height: 600px;">

                <div class="tabs-modern row" style="width:1000px;margin-top:6px;">
                  <div class="col-lg-3-5 col-xl-4-5">
                    <ul>
                      <li>
                        <button class="btn btn-primary" style="width:150px" id="btnRhCam" onclick="ShowTabSan(this.id)">CAM/RH</button>
                      </li>
                      <li>
                        <button class="btn btn-outline-info" style="width:150px" id="btnDepotoire" onclick="ShowTabSan(this.id)">VILLE</button>
                      </li>
                      <li>
                        <button class="btn btn-outline-info" style="width:150px" id="btnLocalisation" onclick="ShowTabSan(this.id)">GPS</button>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="tabs-modern row" style="min-height: 490px;width: 100%;">
                  <div id="msgErreur" class="btn btn-danger" style="padding-left:4px;display:none;"></div>
                  <div class="col-lg-3-5 col-xl-4-5">
                    <div class="tab-content" id="tabContent" style="margin-top:0px">
                      <div id="divRhCam" style="display:block;">
                        <div style="border:solid;border-color:darkblue;padding:10px;width:950px">
                          <h2>Information du Camion</h2>
                          <div class="input-group" style="width:1200px;margin-top:25px">
                            <label class="form-control" style="width: 100px; max-width: 100px; font-weight: bold; ">CAM</label>
                            <span class="input-group-btn">
                              <select class="form-control form-control-modern" id="NOCAMION" onchange="chgCamion(this.value)" style="width: 100px; color: red; font-weight: bold;">
                                <?php echo ($camions ?? ''); ?>
                              </select>
                            </span>
                            <label class="form-control" style="width: 100px; max-width: 100px; font-weight: bold; ">Km deb</label>
                            <span class="input-group-btn">
                              <input type="text" class="form-control form-control-modern" onchange="postTextSani(this.value, this.id)" id="KiloDeb" value="<?php echo ($KiloDeb ?? ''); ?>" style="width: 100px; background-color: yellow; font-weight: bold;" required />
                            </span>
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold; ">Km fin</label>
                            <span class="input-group-btn">
                              <input type="text" class="form-control form-control-modern" id="KiloFin" onchange="postTextSani(this.value, this.id)" value="<?php echo ($KiloFin ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold;" required />
                            </span>
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold; ">Fuel</label>
                            <span class="input-group-btn">
                              <input type="text" class="form-control form-control-modern" id="Fuel" onchange="postTextSani(this.value, this.id)" value="<?php echo ($Fuel ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold;" required />
                            </span>
                          </div>

                          <div class="input-group" style="width:1200px;margin-top:25px">
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold; ">Hre deb</label>
                            <span class="input-group-btn">
                              <input type="time" class="form-control form-control-modern" id="HreDeb_cam" onchange="postTextSani(this.value, this.id)"  value="<?php echo ($HreDeb_cam ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold;" required />
                            </span>
                            <label class="form-control" style="width: 100px; max-width: 100px; font-weight: bold; ">Hre fin</label>
                            <span class="input-group-btn">
                              <input type="time" class="form-control form-control-modern" id="HreFin_cam" onchange="postTextSani(this.value, this.id)" value="<?php echo ($HreFin_cam ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold; " required />
                            </span>
                            <button type='button'
                              class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
                              onclick="postCamSani()" title='Enregistrer'>
                              <i class='fa fa-check  btn btn-success' aria-hidden='true'></i>
                            </button>
                          </div>
                        </div>

                        <div style="border: solid; border-color: darkblue; padding: 10px; margin-top: 15px;width:950px">
                          <h2>Information Chauffeur - Aide(s)</h2>
                          <div class="input-group" style="width:800px;margin-top:20px">

                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold; ">Chauffeur</label>
                            <span class="input-group-btn">
                              <select class="form-control form-control-modern" id="idchauffeur" onchange="chgChauffeur(this.value)" style="width: 180px; color: red; font-weight: bold; ">
                                <?php echo ($chauffeurs ?? ''); ?>
                              </select>
                            </span>
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold;color:lightblue ">Hre deb</label>
                            <span class="input-group-btn">
                              <input type="time" class="form-control form-control-modern" id="HreDeb" onchange="postTextSani(this.value, this.id)" value="<?php echo ($HreDeb ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold; " required />
                            </span>
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold; ">Hre fin</label>
                            <span class="input-group-btn">
                              <input type="time" class="form-control form-control-modern" onchange="postTextSani(this.value, this.id)" id="HreFin" value="<?php echo ($HreFin ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold;" required />
                            </span>

                            <button type='button'
                              class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
                              onclick="PostChaufSani()" title='Enregistrer'>
                              <i class='fa fa-check  btn btn-success' aria-hidden='true'></i>
                            </button>
                            <label class="form-control" style="max-width: 150px; width: 150px; font-weight: bold; ">HRES EXTRA</label>
                            <span class="input-group-btn">
                              <select class="form-control form-control-modern" id="idtypeextra" style="width: 180px; color: red; font-weight: bold; ">
                                <option value="">Choisir</option>
                                <?php echo ($typeextra ?? ''); ?>
                              </select>
                            </span>
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold;color:lightblue ">Hre deb</label>
                            <span class="input-group-btn">
                              <input type="time" class="form-control form-control-modern" id="HreDeb_extra" value="<?php echo ($HreDeb_extra ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold; " required />
                            </span>
                            <label class="form-control" style="max-width: 100px; width: 100px; font-weight: bold; ">Hre fin</label>
                            <span class="input-group-btn">
                              <input type="time" class="form-control form-control-modern" id="HreFin_extra" value="<?php echo ($HreFin_extra ?? ''); ?>" style="width: 100px; background-color: yellow; color: blue; font-weight: bold;" required />
                            </span>

                            <button type='button'
                              class='btn btn-default btn-sm' style='font-size:18px;' xclass='btn-primary btn'
                              onclick="PostChaufExtra()" title='Enregistrer'>
                              <i class='fa fa-check  btn btn-success' aria-hidden='true'></i>
                            </button>
                          </div>

                          <div id="divAide">
                            <?php echo ($aides ?? ''); ?>
                          </div>

                        </div>
                      </div>
                    </div>
                    <div id="divDepotoire" style="display:none;">

                      <div class="row">
                        <div class="col-lg-6">
                          <div class="tabs" style="margin-left:20px;width:1050px">
                            <ul class="nav nav-tabs">
                              <?php echo ($tabsville ?? ''); ?>
                              
                            </ul>
                            <div class="tab-content">
                              <?php echo ($contentville ?? ''); ?>
                              
                            </div>
                          </div>
                        </div>
                       
                      </div>

                    </div>
                    <div id="divLocalisation" style="display:none;width:1150px;">
                      <div id="floating-panel">
                        <div id="divimgtruck" style="display:none;">
                          <img id="ImgTruckSani" src="<?php echo ci3_url('/assets/img/google/point.png'); ?>" width="20" alt="" />

                          <input id="hide-markers" type="button" value="Hide Markers" />
                          <input id="show-markers" type="button" value="Show Markers" />
                          <input id="delete-markers" type="button" value="Delete Markers" />
                        </div>
                      </div>
                      <!--<input id="submit" type="button" value="Adresse actuelle" />
                      <input id="adactuel" style="width:600px" type="text" value="" />-->
                      <div class="tabs-modern row" style="width: 100%;margin-top:6px;display:none;">
                        <div class="col-lg-3-5 col-xl-4-5">
                          <ul>
                            <li>
                              <button class="btn btn-outline-info" style="width:200px" onclick="adjustMap('rotate', 90)"> Rotation à gauche</button>
                            </li>
                            <li>
                              <button class="btn btn-outline-info" style="width:200px" onclick="adjustMap('rotate', -90)"> Rotation à droite</button>
                            </li>
                            <li>
                              <button class="btn btn-outline-info" style="width:200px" onclick="adjustMap('tilt', 90)">Incliner vers le bas</button>
                            </li>
                            <li>
                              <button class="btn btn-outline-info" onclick="adjustMap('tilt', -90)" style="width:200px" id="btnLocalisation">Incliner vers le haut</button>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div id="map" style="width:100%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </section>

    </div>
    <!-- end: page -->
  </section>

  <div class="modal fade" id="chgCamModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Changement de Camion</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div id="divChgChauf" class="modal-body">
          <p style="font-weight:bold;color:red">Les champs suivants doivent être remplis:
            <nav>
              <ul>
                <li>Kilo deb.</li>
                <li>Kilo fin</li>
                <li>Heure deb.</li>
                <li>Heure fin</li>
              </ul>
            </nav>
            avant de pouvoir mettre un autre camion.
          </p>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade" id="chgChaufModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Changement de Chauffeur</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div id="divChgChauf" class="modal-body">
          <p style="font-weight:bold;color:red">
            Les champs suivants doivent être remplis:
            <nav>
              <ul>
                <li>Heure deb.</li>
                <li>Heure fin</li>
              </ul>
            </nav>
            avant de pouvoir mettre un autre camion.
          </p>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
        </div>

      </div>
    </div>
  </div>

  <script src="/assets/pronto/vendor/jquery/jquery.js"></script>
  <script src="/assets/pronto/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
  <script src="/assets/pronto/vendor/popper/umd/popper.min.js"></script>
  <script src="/assets/pronto/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/pronto/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
  <script src="/assets/pronto/vendor/common/common.js"></script>
  <script src="/assets/pronto/vendor/nanoscroller/nanoscroller.js"></script>
  <script src="/assets/pronto/vendor/magnific-popup/jquery.magnific-popup.js"></script>
  <script src="/assets/pronto/vendor/jquery-placeholder/jquery.placeholder.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.1.0/jquery.smartmenus.min.js" integrity="sha256-L+Zo9Q4bGfdY06BqwMYLDoacazH6GrQxkLavPdT0a44=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.1.0/addons/bootstrap/jquery.smartmenus.bootstrap.min.js" integrity="sha256-yQjrAGXVhM4+j4+qFh1W4wg7dPC3IjJ9BId2RO3peEY=" crossorigin="anonymous"></script>
  <!-- Specific Page Vendor -->
  <script src="/assets/pronto/js/examples/examples.header.menu.js"></script>
  <script src="/assets/pronto/js/examples/examples.ecommerce.form.js"></script>
  <script src="/assets/pronto/vendor/dropzone/dropzone.js"></script>
  <script src="/assets/pronto/vendor/pnotify/pnotify.custom.js"></script>
  <script src="/assets/pronto/vendor/summernote/summernote-bs4.js"></script>
  <script src="/assets/pronto/vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
  <script src="/assets/vendor/pnotify/pnotify.custom.js"></script>
  <script src="/assets/pronto/vendor/dropzone/dropzone.js"></script>
  <script src="/assets/pronto/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
  <!-- Theme Base, Components and Settings -->
  <script src="/assets/pronto/js/theme.js"></script>

  <!-- Theme Custom -->
  <script src="/assets/pronto/js/custom.js"></script>

  <!-- Theme Initialization Files -->
  <script src="/assets/pronto/js/theme.init.js"></script>


  <script src="/assets/js/sanivan.js"></script>
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4&callback=initMap&v=weekly"
    defer></script>


  <script>
    navigator.permissions.query({ name: 'geolocation' }).then(function(result) { console.log('Permission state is ', result.state); });

    var typeerr = 'success';
    var msgerr = "<?php echo ($msgerror ?? ''); ?>";
    if (msgerr.trim() != '') {
      if (msgerr == 'Une erreur s\'est produite.') {
        typeerr = 'error';
      }
      $('#msgErreur').html(msgerr);
      $("#msgErreur").fadeIn(4000, function () {
        $("#msgErreur").fadeOut(6000);
      });
    }
    //var  adminTimeout = setTimeout("RefreshTrack", 30000);
  </script>

</body>
</html>