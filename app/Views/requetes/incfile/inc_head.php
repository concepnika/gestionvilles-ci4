<?php
	
?>

<meta name="keywords" content="HTML5 Daoust Template" />
<meta name="description" content="Robert Daoust - Responsive HTML5 Template" />
<meta name="author" content="concept nika" />
<title>Robert Daoust</title>
<!-- Mobile Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

<!-- Web Fonts  -->
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css" />
<!-- Vendor CSS -->
<link rel="stylesheet" href="/assets/pronto/vendor/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/animate/animate.compat.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/font-awesome/css/all.min.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/boxicons/css/boxicons.min.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/magnific-popup/magnific-popup.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/dropzone/basic.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/dropzone/dropzone.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/bootstrap-markdown/css/bootstrap-markdown.min.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/pnotify/pnotify.custom.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/summernote/summernote-bs4.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css" />

<!-- Theme CSS -->
<link rel="stylesheet" href="/assets/pronto/css/theme.css" />

<!-- Skin CSS -->
<link rel="stylesheet" href="/assets/pronto/css/skins/default.css" />

<!-- Theme Custom CSS -->
<link rel="stylesheet" href="/assets/pronto/css/layouts/modern.css" />
<link rel="stylesheet" href="/assets/pronto/css/custom.css" />

<!-- Head Libs -->
<script src="/assets/pronto/vendor/modernizr/modernizr.js"></script>
<link rel="stylesheet" href="/assets/vendor/pnotify/pnotify.custom.css" />
<link rel="stylesheet" href="/assets/pronto/vendor/dropzone/dropzone.css" />
<script src="/assets/pronto/vendor/modernizr/modernizr.js"></script>

  <style>
    .menuchild {
      color:darkblue;
      font-weight:bold;
    }
    .menudiv {
      color: khaki;
      
    }
    .cat {
      color:black;
      background-color: #00c0ef;
      border-color: #00acd6;
      margin-bottom:10px;
      margin-top:10px;
    }

    .grp {
      display: none;
    }

  .special {
    font-weight: bold !important;
    color: #fff !important;
    background: #bc0000 !important;
    text-transform: uppercase;
  }
  .ssgroupe {
    font-weight: bold !important;
    color: #fff !important;
    background: #ffffff !important;
    
  }
  </style> 
<script>
  function setLeftHide() {
    if ($(window).width() < 769) {
      $("#divLeft").css('display', 'none');
      $("#divLeftDossier").css('display', 'none');
      $("#divLeftCoproprio").css('display', 'none');
      $("#divLeftFourniseur").css('display', 'none');
      $("#ariane").css('display', 'none');
      $("#selArchi").css('display', 'block');
    }
    else {
      $("#divLeft").css('display', 'block');
      $("#divLeftDossier").css('display', 'block');
      $("#divLeftCoproprio").css('display', 'block');
      $("#divLeftFourniseur").css('display', 'block');
      $("#ariane").css('display', 'block');
      $("#selArchi").css('display', 'none');
    }
  }
</script>
