<?php

?>
<!DOCTYPE html>
<html lang="<?php echo ci3sess('site_lang');?>" xml:lang="<?php echo ci3sess('site_lang');?>" xmlns="http://www.w3.org/1999/xhtml">

<head>


<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" lang="fr" content=" ">
  <title>
    Robert Daoust
  </title>




  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>
<script>


</script>
<style>
  .card {
    position: relative;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: lightgray;
    background-clip: border-box;
    border: 2px solid red;
    border-radius: 0.25rem;
}

    .card > hr {
        margin-right: 0;
        margin-left: 0;
    }

    .card > .list-group {
        border-top: inherit;
        border-bottom: inherit;
    }

        .card > .list-group:first-child {
            border-top-width: 0;
            border-top-left-radius: calc(0.25rem - 0);
            border-top-right-radius: calc(0.25rem - 0);
        }

        .card > .list-group:last-child {
            border-bottom-width: 0;
            border-bottom-right-radius: calc(0.25rem - 0);
            border-bottom-left-radius: calc(0.25rem - 0);
        }

        .card > .card-header + .list-group,
        .card > .list-group + .card-footer {
            border-top: 0;
        }

.card-body {
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1.25rem;
}

.card-title {
    margin-bottom: 0.75rem;
    background-color: darkred;
    color:whitesmoke;
}

.card-subtitle {
    margin-top: -0.375rem;
    margin-bottom: 0;
}

.card-text:last-child {
    margin-bottom: 0;
}

.card-link:hover {
    text-decoration: none;
}

.card-link + .card-link {
    margin-left: 1.25rem;
}

.card-header {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 0 solid rgba(0, 0, 0, 0.125);
}

    .card-header:first-child {
        border-radius: calc(0.25rem - 0) calc(0.25rem - 0) 0 0;
    }

.card-footer {
    padding: 0.75rem 1.25rem;
    background-color: rgba(0, 0, 0, 0.03);
    border-top: 0 solid rgba(0, 0, 0, 0.125);
}

    .card-footer:last-child {
        border-radius: 0 0 calc(0.25rem - 0) calc(0.25rem - 0);
    }

.card-header-tabs {
    margin-right: -0.625rem;
    margin-bottom: -0.75rem;
    margin-left: -0.625rem;
    border-bottom: 0;
}

.card-header-pills {
    margin-right: -0.625rem;
    margin-left: -0.625rem;
}

.card-img-overlay {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    padding: 1.25rem;
    border-radius: calc(0.25rem - 0);
}

.card-img,
.card-img-top,
.card-img-bottom {
    -webkit-flex-shrink: 0;
    -ms-flex-negative: 0;
    flex-shrink: 0;
    width: 100%;
}

.card-img,
.card-img-top {
    border-top-left-radius: calc(0.25rem - 0);
    border-top-right-radius: calc(0.25rem - 0);
}

.card-img,
.card-img-bottom {
    border-bottom-right-radius: calc(0.25rem - 0);
    border-bottom-left-radius: calc(0.25rem - 0);
}

.card-deck .card {
    margin-bottom: 7.5px;
}

  #calendar {
    width: 100%;
    margin: 0 auto;
  }

  /* ═══ Boutons flottants mobile ═══ */
  .btn-toggle-sidebar, .btn-back-mobile {
    display: none;
    position: fixed;
    z-index: 1100;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid DarkBlue;
    background: #fff;
    color: DarkBlue;
    font-size: 20px;
    line-height: 36px;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,.3);
    padding: 0;
  }
  .btn-toggle-sidebar { top: 125px; left: 8px; }
  .btn-back-mobile { top: 125px; left: 56px; }
  .btn-toggle-sidebar:active, .btn-back-mobile:active { transform: scale(.9); }

  @media (max-width: 768px) {
    .btn-toggle-sidebar, .btn-back-mobile { display: block !important; }
    #divChauffeur {
      display: none !important;
      z-index: 1050;
      width: 200px !important;
      top: 170px !important;
      left: 8px !important;
      height: auto !important;
      max-height: 70vh;
      overflow-y: auto;
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 4px 16px rgba(0,0,0,.3);
    }
    #divChauffeur.sidebar-open {
      display: block !important;
    }
    #divShowCed {
      margin-left: 0 !important;
      width: 100vw !important;
    }
    #divShowCed #map {
      width: 100vw !important;
    }
  }

</style>
  <script>
    var urlserver = "<?php echo ($_SERVER['SERVER_NAME'] ?? '') ?>" + ":5001";
  </script>
<script src="/assets/js/track_cam.js"></script>
</head>
<body id="page">
 
<section class="container"  style="margin-left:5px;width:100%">
        <div id="myNavbar" style="margin-top:35px;">
        <ul class="nav navbar-nav form-inline">
          <li>
            <a href="/admin" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Accueil" data-content="Aller à l'Accueil">
              <i class="fa fa-home" style="font-size:22px;margin-top: -17px;"></i>
            </a>
          </li>
          <li><label  style="font-size:24px;font-weight:600;">Route: Camion / RH</label> </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <input type="date" class="form-control" onchange="getDateChange(this.value)" value="<?php echo ($ActiveDate ?? '') ; ?>" id="dtedeb" />
          </li>
          <li> &nbsp;&nbsp;</li>
          <li>
            <label >Délai en seconde</label>
            <input type="number" class="form-control" value="5" id="edtLapRefresh" />
          </li>
          <li> &nbsp;&nbsp;</li>
          <li style="margin-left:10px;">
            <a href="javascript:showAdresseInfoChauf()" style="width: 250px;height:35px;">
              <i class="fa fa-user-circle-o" style="width: 250px;margin-top:-20px;height:35px; font-weight:bold;font-size:22px">&nbsp;Info chauffeur</i>
            </a>
          </li>
        </ul>

      </div> 

</section>

  <div id="divimgtruck" style="display:none;">
    <img id="ImgTruckSani" src="<?php echo ci3_url('/assets/00_images/truck2.png'); ?>" width="60" alt="" />
    <input type="hidden" id="allchauffeur" value="<?php echo ($allchauffeur ?? ''); ?>" />
  </div>
  <button type="button" class="btn-toggle-sidebar" id="btnToggleSidebar" title="Chauffeurs">
    <i class="fa fa-users"></i>
  </button>
  <button type="button" class="btn-back-mobile" id="btnBackMobile" title="Retour" onclick="history.back()">
    <i class="fa fa-arrow-left"></i>
  </button>
  <div class="row" >
    <label id="adresse"></label>
    <div id="divChauffeur" class="border border-secondary" style="display:block;position:fixed;left: 10px;top: 120px; height: 75vh; width: 180px;">
      <?php echo ($chauffeursRte ?? '') ?>
    </div>
    <div id="divShowCed" style="margin-top: 60px;margin-left:185px;height: 85vh; width: 87vw;position:page;">
        <div id='map' style="width: 85vw;height:80vh"></div>
    </div>
  </div>
  <input type="hidden" id="idchauffeur" value="<?php echo ($idchauffeur ?? ''); ?>" />
  <input type="hidden" id="nocamion" value="" />
  <input type="hidden" id="NewZoom" value="<?php echo ($newzoom ?? ''); ?>" />


  <script src="/assets/jquery-ui/jquery-ui.min.js"></script>
  <!-- AdminLTE App -->
  <script src="/assets/js/adminlte.min.js"></script>
  
  <script src="/assets/js/nika.js"></script>
  <script src="/assets/js/sanivan.js"></script>

  <script>
    
    //----------------------------------
    var base_url = '<?php echo ci3_url(); ?>';

    function chkrhexisteCal( idrh ) {
	    //-----------------------------------------
	    var urlName = "/sanivan/chkrhexist/" + idrh;
	    $.ajax({
		    async: true,
		    type: "POST",
		    url: urlName
	    }).done(function (msg) {
        if (msg != 0) {
          alert(msg);
          return false;
		    }
        else {
          return true;
        }
	    });
    }
    function showAllCauffeurs() {
      $("#allchauffeur").val(1);
      var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?idchauffeur=0&camchauf=0&allchauffeur=' + $("#allchauffeur").val()+
                      '&newzoom=' + $("#NewZoom").val();
      document.location = urlName;
    }
    function printCal() {
      var printwhere = $('input[name="radPrint"]:checked').val();
      if (printwhere == '1') {
        document.location = "/sanivan/calrhpdf?dtedeb=" + $("#dtePrintFrom").val() + "&valtofind=" + $("#edtFind").val() + 
                        "&from=1&dtefin=" + $("#dtePrintTo").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val()+
                          '&printwhere=' + $('input[name="radPrint"]:checked').val();
      }
      else {
        var urlName = "/sanivan/calpdf" + '?dtedeb=' + $("#dtePrintFrom").val() + "&dtefin=" + $("#dtePrintTo").val() +
          '&printwhere=' + $('input[name="radPrint"]:checked').val() + "&valtofind=" + $("#edtFind").val() +
          "&statut=" + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
        window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
      }
    }
    
  </script>

  <script
          
    src=
          'https://maps.googleapis.com/maps/api/js?key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4&callback=initMap&libraries=geometry,marker&v=weekly&loading=async'
          async
          defer></script>
<script>
(function(){
    var btn = document.getElementById('btnToggleSidebar');
    var div = document.getElementById('divChauffeur');
    if (!btn || !div) return;
    btn.addEventListener('click', function(){
        div.classList.toggle('sidebar-open');
        btn.innerHTML = div.classList.contains('sidebar-open')
            ? '<i class="fa fa-times"></i>'
            : '<i class="fa fa-users"></i>';
    });
    document.addEventListener('click', function(e){
        if (window.innerWidth > 768) return;
        if (!div.classList.contains('sidebar-open')) return;
        if (div.contains(e.target) || btn.contains(e.target)) return;
        div.classList.remove('sidebar-open');
        btn.innerHTML = '<i class="fa fa-users"></i>';
    });
})();
</script>
</body>
</html>
