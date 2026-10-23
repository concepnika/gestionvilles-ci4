<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

	<?php echo view('villes/incfile/inc_head'); ?>

</head>
<body id="page-top">

	<?php echo ($menu ?? ''); ?>
  <section role="main" class="content-body content-body-modern mt-0">
    <header class="page-header page-header-left-inline-breadcrumb" style="margin-top:40px;margin-bottom:20px">
      <h2 class="font-weight-bold text-6"><?php echo ci3lang('mne_requete'); ?></h2>
      <?php echo ($noreq ?? '') ?>
    </header>

    <div class="row">
      <div class="col-2"></div>
      <div class="col-md-10">
        <section class="card card-featured card-featured-primary mb-4">
          <header class="card-header">
            <div class="card-actions">
              <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
              <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
            </div>

            <h2 class="card-title">
              <?php echo ($villenom ?? '') ?>
            </h2>
          </header>
          <div class="card-body">
            <div style="scroll-margin-bottom:10px;">
              <label class="control-label font-weight-bold" style="font-size: 16px"><?php echo ci3lang('pgereq_titrereq'); ?></label>
              <div>
                <input type="text" class="form-control" style="width:100%;" id="nom" value="<?php echo ($nom ?? '') ?>" disabled />

                <input type="hidden" name="idrequete" id="idrequete" value="<?php echo ($id ?? '') ?>" />
                <input type="hidden" name="idville" id="idville" value="<?php echo ($idville ?? '') ?>" />
              </div>
              <br />
            </div>

            <?php echo ($inforequete ?? '') ?>
            <div>
              <select class="form-control" name="matiere" id="matiere" style="width:100%" disabled>
                <option value=""><?php echo ci3lang('pgereq_enrreq'); ?><?php echo ci3lang('pgereq_choisir'); ?></option>
                <?php echo ($optMatiere ?? '') ?>
              </select>

            </div>

          </div>
        </section>
      </div>
    </div>
    <div class="row">
      <div class="col-2"></div>


        <?php echo ($fichiers ?? '') ?>



    </div>

    <!-- start: page -->
    <div class="row">
      <div class="col-2"></div>
      <div class="col-md-10">
        <section class="card card-featured card-featured-primary mb-4">
          <header class="card-header">
            <div class="card-actions">
              <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
              <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
            </div>

            <h2 class="card-title"> <?php echo ci3lang('pgeajout_send'); ?>
            </h2>
          </header>
          <div class="card-body">
            <form action="/villes/send/reponse" method="post" enctype="multipart/form-data">
              <input type="hidden" name="idrequete" id="idrequete" value="<?php echo ($id ?? '') ?>" />
              <input type="hidden" name="idville" id="idville" value="<?php echo ($idville ?? '') ?>" />
              
              <div class="row mt-2">
                <div>
                  <section class="card card-modern card-big-info">
                    <i class="card-big-info-icon bx bx-camera"></i>
                    <h2 class="card-big-info-title"><?php echo ci3lang('pgereq_upload'); ?></h2>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-3-7">
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
                  <section>
                    <div class="card-body">
                      <div class="row">

                        <div>
                          <div style="display:<?php echo ($fileinclude ?? '') ?>">
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold mb-0" style="font-size: 16px;"><?php echo ci3lang('btn_telecharger'); ?></label>
                            <div class="col-lg-3-5">
                              <div>

                                <input class="form-control" style="width:100%"
                                  name="fileRequete" id="fileRequete" type="file" />
                              </div>
                            </div>
                          </div>



                          <div>
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold mb-0" style="font-size: 16px;"><?php echo ci3lang('pgeajout_statut'); ?>label>

                            <div>
                              <select class="form-control" name="statut" id="statut" style="width:100%" onchange="setInfoStatut(this.value)">
                                <option value="0"><?php echo ci3lang('pgeajout_nochange'); ?></option>
                                <option value="4">Recéduler la requête</option>
                                <option value="6">Annuler la requête</option>
                                <option value="8">Adresse modifiée </option>
                                <option value="9">Action bac</option>
                                <option value="10">Matière</option>
                              </select>

                            </div>
                          </div>
                          <div class="form-group row mb-6" id="divMat" style="display:none;margin-top:5px;">
                            <br /><label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px"><?php echo ci3lang('pgeajout_mat'); ?></label>

                            <div class="col-lg-7 col-xl-6">
                              <select class="form-control" name="matiere" id="matiere" style="width:200px" required>
                                <option value=""><?php echo ci3lang('pgereq_choisir'); ?></option>
                                <?php echo ($optMatiereStat ?? '') ?>
                              </select>

                            </div>
                          </div>
                          <div class="form-group row mb-6" id="divBac" style="display:none;margin-top:5px;">
                            <div class="input-group" style="width:700px;">
                              <span class="form-control" style="width:150px;font-weight:bold;"><?php echo ci3lang('pgeajout_noserie'); ?></span>
                              <input type="text" id="noSerie" name="noSerie" class="form-control"
                                placeholder="<?php echo ci3lang('pgeajout_noserie'); ?>" style="width:150px" value="" />
                              <span class="form-control" style="width:100px;font-weight:bold;"><?php echo ci3lang('pgeajout_grandeur'); ?> </span>
                              <select class="form-control" name="grandeur" id="grandeur" style="width:80px">
                                <option value=""><?php echo ci3lang('pgereq_choisir'); ?></option>
                                <option value="45">45</option>
                                <option value="80">80</option>
                                <option value="120">120</option>
                                <option value="240">240</option>
                                <option value="360">360</option>
                              </select>
                              <span class="form-control" style="width:100px;font-weight:bold;"><?php echo ci3lang('pgeajout_couleur'); ?> </span>
                              <select class="form-control" name="couleur" id="couleur" style="width:80px">
                                <option value=""><?php echo ci3lang('pgereq_choisir'); ?></option>
                                <option value="VERT" style="color:forestgreen;font-weight:bold;"><?php echo ci3lang('pgeajout_vert'); ?>VERT</option>
                                <option value="BLEU" style="color:blue;font-weight:bold;"><?php echo ci3lang('pgeajout_bleu'); ?>BLEU</option>
                                <option value="NOIR" style="color:black;font-weight:bold;"><?php echo ci3lang('pgeajout_noir'); ?>NOIR</option>
                                <option value="BRUN" style="color:saddlebrown;font-weight:bold;"><?php echo ci3lang('pgeajout_brun'); ?>BRUN</option>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row mb-6" id="divAdr" style="display:none;margin-top:5px;">
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px;"><?php echo ci3lang('pgereq_adresse'); ?>Adresse</label>
                            <div class="input-group" style="width:550px;">

                              <textarea name="adresse" placeholder="Adresse: Adresse, Code postal" id="adresse" rows="3" cols="40" style="height:100%;"></textarea>
                              <a href="javascript:GoogleVille(<?php echo ($idville ?? '') ?>)" style="width:35px;height:35px;">
                                <img src="/assets/img/googlemap.png" height="35" />
                              </a>
                            </div>
                          </div>
                          <div class="form-group row mb-3" id="divDte" style="display:none">
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px;text-align:left"><?php echo ci3lang('pgeajout_dtecedule'); ?></label>

                            <div class="col-lg-7 col-xl-6">
                              <div class="input-group">
                                <span class="input-group-text">
                                  <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="date" class="form-control" name="dteCedule" id="dteCedule" value="" />
                              </div>

                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold mb-0" style="font-size: 16px"><?php echo ci3lang('pgereq_description'); ?></label>

                            <div>
                              <textarea class="summernote" name="description" id="description" rows="12" cols="50" style="height:100%;" required></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <button type="submit" class="submit-button btn btn-primary btn-px-4 py-3 d-flex align-items-center font-weight-semibold line-height-1" data-loading-text="Loading...">
                    <i class="bx bx-save text-4 me-2"></i>
                    <?php echo ci3lang('pgeajout_send_ajout'); ?>
                  </button>



                </div>
              </div>
            </form>



          </div>
        </section>
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
      $(document).ready(function () {
        $('#description_1').summernote({
          toolbar: [
            // [groupName, [list of button]]
            ['style', ['style']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['height', ['height']]

          ],
          callbacks: {
            onImageUpload: function (image) {
              sendFile(image[0]);
            }
          }
        });

      });
      function sendFile(image) {
        var data = new FormData();
        data.append("image", image);
        //if you are using CI 3 CSRF
        $.ajax({
          data: data,
          type: "POST",
          url: "/summernote-image-upload",
          cache: false,
          contentType: false,
          processData: false,
          success: function (url) {
            var image = url;
            $('#description').summernote("insertImage", image);
          },
          error: function (data) {
            console.log(data);
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
      var chkRequeteTimer = setInterval(updateInfoRequete, 20000);
    </script>

</body>
</html>
