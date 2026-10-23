<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

  <?php echo view('villes/incfile/inc_head'); ?>

</head>
<body id="page-top">

  <?php echo ($menu ?? ''); ?>
      <div class="row">
        <div class="col-2"></div>
        <div class="col-md-6">
          <section class="card card-featured card-featured-primary mb-4">
            <header class="card-header" style="margin-top:40px;margin-bottom:20px;">
          <h2 class="font-weight-bold" style="width:350px">Réception d'une Requête</h2>
          <div class="card-body">
            <?php echo ($noreq ?? '') ?>

          </div>
            </header>
            </section>
        </div>
      </div>


    <div class="row">
      <div class="col-2"></div>
      <div class="col-md-6">
        <section class="card card-featured card-featured-primary mb-4">
          <header class="card-header">
            <div class="card-actions">
              <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
              <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
            </div>

            <h2 class="card-title">
              <span id="nomVille"><?php echo ($villenom ?? '') ?></span>
            </h2>
          </header>
          <div class="card-body">
            <div style="scroll-margin-bottom:10px;">
              <label class="control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px;">Titre de cette requête</label>
              <div>
                <input type="text" class="form-control" style="width:100%;" value="<?php echo ($nom ?? '') ?>" disabled />


              </div>
              <br />
            </div>

            <?php echo ($inforequete ?? '') ?>
            <div>
              <select class="form-control" style="width:100%;" disabled>
                <option value="">Choisir</option>
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
      <div class="col-md-6">
        <section class="card card-featured card-featured-primary mb-4">
          <header class="card-header">
            <div class="card-actions">
              <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
              <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
            </div>

            <h2 class="card-title">
              Envoyer une réponse pour cette requête
            </h2>
          </header>
          <div class="card-body">
            <form id="frmReponse" action="/villes/send-reponse" method="post" onsubmit="verifFields()" enctype="multipart/form-data">
              <input type="hidden" name="idrequete" id="idrequete" value="<?php echo ($id ?? '') ?>" />
              <input type="hidden" name="idville" id="idville" value="<?php echo ($idville ?? '') ?>" />
              <input type="hidden" name="IsFrom" id="IsFrom" value="1" />
              <div class="row mt-2">
                <div class="col">
                  <section class="card card-modern card-big-info">
                    <i class="card-big-info-icon bx bx-camera"></i>
                    <h2 class="card-big-info-title">Télécharger vos fichiers si requis</h2>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-3-7">
                          <div class="form-group row align-items-center">
                            <input class="form-control" style="width:450px;"
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
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold mb-0" style="font-size: 16px;">Télécharger</label>
                            <div class="col-lg-3-5">
                              <div>

                                <input class="form-control" style="width:100%"
                                  name="fileRequete" id="fileRequete" type="file" />
                              </div>
                            </div>
                          </div>
                          <div>
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold mb-0" style="font-size: 16px;">Statut</label>

                            <div>
                              <select class="form-control" name="statut" id="statut" onchange="setInfoStatut(this.value)" style="width:100%">
                                <option value="">Choisir</option>
                                <?php echo ($optStatut ?? '') ?>
                              </select>

                            </div>
                          </div>
                          <div class="form-group row mb-6" id="divMat" style="display:none;margin-top:5px;">
                            <br /><label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px">Matière</label>

                            <div class="col-lg-7 col-xl-6">
                              <select class="form-control" name="matiere" id="matiere" style="width:200px" required>
                                <option value="">Choisir</option>
                                <?php echo ($optMatiereStat ?? '') ?>
                              </select>

                            </div>
                          </div>
                          <div class="form-group row mb-6" id="divBac" style="display:none;margin-top:5px;">
                            <div class="input-group" style="width:700px;">
                              <span class="form-control" style="width:150px;font-weight:bold;">Numéro de série</span>
                              <input type="text" id="noSerie" name="noSerie" class="form-control" 
                                     placeholder="Numéro de série" style="width:150px" value="" />
                              <span class="form-control" style="width:100px;font-weight:bold;">Grandeur </span>
                              <select class="form-control" name="grandeur" id="grandeur" style="width:80px">
                                <option value="">Choisir</option>
                                <option value="45">45</option>   
                                <option value="80">80</option>                                 
                                <option value="120">120</option>   
                                <option value="240">240</option>     
                                <option value="360">360</option> 
                              </select>
                              <span class="form-control" style="width:100px;font-weight:bold;">Couleur </span>
                              <select class="form-control" name="couleur" id="couleur" style="width:80px">
                                <option value="">Choisir</option>
                                <option value="VERT" style="color:forestgreen;font-weight:bold;">VERT</option>
                                <option value="BLEU" style="color:blue;font-weight:bold;">BLEU</option>
                                <option value="NOIR" style="color:black;font-weight:bold;">NOIR</option>
                                <option value="BRUN" style="color:saddlebrown;font-weight:bold;">BRUN</option>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row mb-6" id="divAdr" style="display:none;margin-top:5px;">
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px;">Adresse</label>
                            <div class="input-group" style="width:550px;">

                              <textarea name="adresse" placeholder="Adresse: Adresse, Code postal" id="adresse" rows="3" cols="40" style="height:100%;"></textarea>
                              <a href="javascript:GoogleVille(<?php echo ($idville ?? '') ?>)" style="width:35px;height:35px;">
                                <img src="/assets/img/googlemap.png" height="35" />
                              </a>
                            </div>
                          </div>
                          <div class="form-group row mb-3" id="divDte" style="display:none">
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold text-lg-end mb-0" style="font-size: 16px;text-align:left">Date cédulée</label>

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
                            <label class="col-lg-5 col-xl-3 control-label font-weight-bold mb-0" style="font-size: 16px">Description</label>

                            <div>
                              <textarea name="description" id="description" rows="12" cols="50" style="height:100%;" required></textarea>
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
                    Envoyer la réponse
                  </button>
                </div>
              </div>
            </form>
          </div>
        </section>
      </div>
    </div>



    <!-- end: page -->




  <?php echo view('villes/incfile/inc_javascript'); ?>
  <script>
    //----------------------------------------------------------

    //----------------------------------------------------------
    $(document).ready(function() {
      $('#description1').summernote({
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
    //-------------------------------------------------------------------
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
  </script>
</body>
</html>
