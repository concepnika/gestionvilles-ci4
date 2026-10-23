<?php


?>
<!DOCTYPE html><html data-bs-theme="light" lang="fr">
<head>

  <?php echo view('villes/incfile/inc_head'); ?>
  <style>
    #my_camera {
      width: 1290px;
      height: 870px;
      border: 1px solid black;
    }
  </style>
  <!-- Configure a few settings and attach camera -->
  <?php echo view('villes/incfile/inc_javascript'); ?>
  <script src="/assets/js/sanivan.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
</head>
<body id="page-top" onresize="setWebcamSize()">

  <header class="header header-nav-menu">
    <div class="logo-container">
      <a href="#" class="logo">
        <img src="/assets/img/logo.png" width="145" alt="Robert Daoust" />
      </a>
      <button class="btn header-btn-collapse-nav d-lg-none" data-bs-toggle="collapse" data-bs-target=".header-nav">
        <i class="fas fa-bars"></i>
      </button>

      <!-- start: header nav menu -->
      <div class="header-nav collapse">
        <div class="header-nav-main header-nav-main-effect-1 header-nav-main-sub-effect-1">
          <nav>
            <ul class="nav nav-pills" id="mainNav">
              <li class="">
                <a class="nav-link" style="font-size:16px" href="javascript:closeWin()"> Fermer
                </a>

              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- end: header nav menu -->
    </div>

  </header>



    <div class="row" style="margin-top:70px;">
      <div class="col-1"></div>
      <div class="col-md-10">
        <section class="card card-featured card-featured-primary mb-4">
          <header class="card-header">
            <div class="card-actions">
              <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
              <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
            </div>
            
            <h2 class="card-title">
              <span id="nomVille">Scan pour le Billet du dépotoire</span>
            </h2>
          </header>
          <div class="card-body">
            <form method="POST" id="frmImgSend" action="/sanivan/storeimage">
              <div id="pre_take_buttons">
                <button id="idsnapshot" type=button class="btn btn-primary" onclick="take_snapshot()">Confirmer la photo</button>
                <input type="hidden" name="image" class="image-tag">
                <input id="idinfoville" name="idinfoville" type="hidden" value="<?php echo ($idinfoville ?? ''); ?>" />
                <input id="depotoire" name="depotoire" type="hidden" value="<?php echo ($depotoire ?? ''); ?>" />
                <input id="nocamion" name="nocamion" type="hidden" value="<?php echo ($nocamion ?? ''); ?>" />
                <input id="iddepotoire" type="hidden" value="<?php echo ($iddepotoire ?? ''); ?>" />
                <input id="dteactive" name="dteactive" type="hidden" value="<?php echo ($dteActive ?? ''); ?>" />
                <button type="submit" class="btn btn-success">Enregistrer &gt;</button>
                
              </div>

              <div id="my_camera"></div>
              <script>
                  Webcam.set({
                    width: 1290,
                    height: 870,
                    image_format: 'jpeg',
                    jpeg_quality: 90
                  });
                  Webcam.attach('#my_camera');
              </script>
              <div id="results"></div>
          </form>
          </div>
        </section>
      </div>
    </div>

	<script language="JavaScript">
   //----------------------------------------------------------
    //function take_snapshot() {
    //  // take snapshot and get image data
    //  Webcam.snap(function (data_uri) {
    //    // display results in page
    //    $("#results").css('display', 'block');
    //    $("#my_camera").css('display', 'none');
    //            $(".image-tag").val(data_uri);
    //    document.getElementById('results').innerHTML =
    //      '<img id="imageprev" src="' + data_uri + '"/>';
    //  });

    //}
    function take_snapshot() {
      var imgcode = '';
      Webcam.snap(function (data_uri) {
        $("#results").css('display', 'block');
        $("#my_camera").css('display', 'none');
        $(".image-tag").val(data_uri);
        document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
        imgcode = data_uri;
      });
        //var fd = new FormData();
        //fd.append("imgcode", data_uri);
        //fd.append("depotoire", $("#depotoire").val());
        //fd.append("dteactive", $("#dteactive").val());
        ////var imageData = data_uri.replace(/^data:image\/(png|jpg);base64,/, '');
        //var urlName = '/tablettes_san/saveimg';
        //$.ajax({
        //  url: urlName,
        //  data: fd,
        //  processData: false,
        //  contentType: false,
        //  type: 'POST',
        //}).done(function (data) {
        //  $("#divtest").html(data);
        //});
    }
    function saveSnap() {
      // Get base64 value from <img id='imageprev'> source
      var base64image = document.getElementById("imageprev").src;


      Webcam.upload(base64image, '<?php echo ci3_url('tablettes_san/saveimg'); ?>', function (code, text) {
        
        console.log('Save successfully');
        //console.log(text);
      });

    }
    function SubmitIMG() {
      $("#frmImgSend").submit();
      
      window.open('', '_self').close();
    }
    function closeCurrentTab() {
    window.open('', '_self').close();
}

    //----------------------------------------------------------
  </script>
</body>
</html>
