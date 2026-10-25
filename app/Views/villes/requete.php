<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

	<?php echo view('villes/incfile/inc_head'); ?>
  <link rel="stylesheet" href="/assets/plugins/dropzone/dropzone.css" />

</head>
<body id="page-top">

	<?php echo ($menu ?? ''); ?>
	<section role="main" class="content-body content-body-modern mt-0">
		<header class="page-header page-header-left-inline-breadcrumb"  style="margin-top:40px;margin-bottom:20px">
			<h2 class="font-weight-bold text-6"><?php echo ci3lang('mne_requete'); ?></h2>
			<?php echo ($noreq ?? '') ?>
      <div id="msgErreur" class="btn btn-danger" style="padding-left:4px;display:none;">

      </div>
    </header>
		<!-- start: page -->
    <form name="frmRequete" id="frmRequete" class="ecommerce-form action-buttons-fixed" action="/villes/post-requete" method="post" enctype="multipart/form-data">
      <div class="row mt-2">
        <div class="col">
          <section class="card card-modern card-big-info">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-2-5 col-xl-1-5">
                  <i class="card-big-info-icon bx bx-camera"></i>
                  <h2 class="card-big-info-title"><?php echo ci3lang('pgereq_upload'); ?></h2>
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
                  <h2 class="card-big-info-title"><?php echo ci3lang('pgereq_detailreq'); ?></h2>
                  <p class="card-big-info-desc">
                    <?php echo ci3lang('pgereq_explication'); ?>
                  </p>
                </div>
                <div class="col-lg-3-5 col-xl-4-5">
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px"><?php echo ci3lang('pgereq_titrereq'); ?></label>
                    <div class="col-lg-7 col-xl-6">
                      <input type="text" class="form-control" name="nom" id="nom" value="" required />
                      <input type="hidden" name="id" id="id" value="<?php echo ($id ?? '') ?>" />
                      <input type="hidden" name="idville" id="idville" value="<?php echo ($idville ?? '') ?>" />
                      <span style="display:none" id="nomVille"><?php echo ($nomville ?? '') ?></span>
                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px"><?php echo ci3lang('pgereq_adresse'); ?></label>

                      <div class="input-group" style="width:550px;">

                        <textarea name="adresse" placeholder="<?php echo ci3lang('pgereq_placeadre'); ?>" id="adresse" rows="3" cols="40" style="height:100%;" required></textarea>
                        <a href="javascript:GoogleVille()" style="width:35px;height:35px;">
                          <img src="/assets/img/googlemap.png" height="35" />
                        </a>
                      </div>

                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px"><?php echo ci3lang('pgereq_matiere'); ?></label>

                    <div class="col-lg-7 col-xl-6">
                      <select class="form-control" name="matiere" id="matiere" style="width:200px" required>
                        <option value=""><?php echo ci3lang('pgereq_choisir'); ?></option>
                        <?php echo ($optMatiere ?? '') ?>
                      </select>

                    </div>
                  </div>
                  <div class="form-group row align-items-center mb-3">
                    <label class="col-lg-5 col-xl-3 border control-label font-weight-bold text-lg-center mb-0" style="font-size: 16px">
                      <input class="checkbox-primary" type="checkbox" id="chkReparationBac" />&nbsp;<?php echo ci3lang('pgereq_rep_bac'); ?></label>

                    <div class="col-lg-7 col-xl-6">
                      <select class="form-control" name="chkRepareBac" id="chkRepareBac" style="width: 100%"  multiple="multiple" data-plugin-multiselect data-plugin-options='{ "maxHeight": 200, "nonSelectedText": "<?php echo ci3lang('pgereq_choisir'); ?>" }'>
														<?php echo ($optMatiere ?? '') ?>
											</select>

                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-lg-5 col-xl-3  control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px"><?php echo ci3lang('pgereq_description'); ?></label>

                    <div class="col-lg-7 col-xl-6">
                      <textarea  name="description" id="description" rows="6" style="width: 100%" required></textarea>

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
            <?php echo ci3lang('pgereq_enrreq'); ?>
          </button>
        </div>
        <div class="col-12 col-md-auto px-md-0 mt-3 mt-md-0">
          <a href="/villes/accueil/<?php echo (ci3sess('site_lang') ?? ''); ?>" class="cancel-button btn btn-light btn-px-4 py-3 border font-weight-semibold text-color-dark text-3"><?php echo ci3lang('btn_annuler'); ?></a>

        </div>

      </div>
    </form>



		<!-- end: page -->
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
  <script src="/assets/plugins/dropzone/dropzone.js"></script>
  <script>
    // DropzoneJS Demo Code Start
    Dropzone.autoDiscover = false

    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#template")
    previewNode.id = ""
    var previewTemplate = previewNode.parentNode.innerHTML
    previewNode.parentNode.removeChild(previewNode)

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
      url: "upload.php?from=1", // Set the url
      thumbnailWidth: 80,
      thumbnailHeight: 80,
      parallelUploads: 20,
      previewTemplate: previewTemplate,
      autoQueue: false, // Make sure the files aren't queued until manually added
      previewsContainer: "#previews", // Define the container to display the previews
      clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
    })

    myDropzone.on("addedfile", function (file) {
      // Hookup the start button
      file.previewElement.querySelector(".start").onclick = function () { myDropzone.enqueueFile(file) }
    })

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
      document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
    })

    myDropzone.on("sending", function (file) {
      // Show the total progress bar when upload starts
      document.querySelector("#total-progress").style.opacity = "1"
      // And disable the start button
      file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
    })

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) {
      document.querySelector("#total-progress").style.opacity = "0"
      postDocument(1);
    })

    // Setup the buttons for all transfers
    // The "add files" button doesn't need to be setup because the config
    // `clickable` has already been specified.
    document.querySelector("#actions .start").onclick = function () {
      myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
    }
    document.querySelector("#actions .cancel").onclick = function () {
      myDropzone.removeAllFiles(true)
    }

  function postDocument() {
    var addFile = encodeURIComponent(myDropzone.files[0].name);
    var urlName = "/post-document?fichier=" + addFile + "&from=1&idrquete=" + $('#id').val();
    $.ajax({
      async: false,
      type: "POST",
      url: urlName
    }).done(function (msg) {
      if (msg.trim() != "") {
        var objData = JSON.parse(msg);
        //$('#tboDocuments').html(objData.listdocuments)
        //var element = document.getElementById("tr_" + objData.id);
        //element.scrollIntoView(true);
        //$('#msgErreur').html(objData.msgerr);
        //$("#msgErreur").fadeIn(4000, function () {
        //  $("#msgErreur").fadeOut(6000);
        //});
        myDropzone.removeAllFiles();
      }
    });
  }
   
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
  //var chkRequeteTimer = setInterval(updateInfoRequete, 20000);
  </script>


</body>
</html>
