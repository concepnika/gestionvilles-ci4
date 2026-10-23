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
    var urlserver = "<?php echo ($_SERVER['SERVER_NAME'] ?? '') ?>" + ":5001";
  </script>
<script src="/assets/js/track_cam.js"></script>
</head>
<body id="page">
 


  <div id="divimgtruck" style="display:none;">
    <img id="ImgTruckSani" src="<?php echo ci3_url('/assets/00_images/truck2.png'); ?>" width="60" alt="" />
    <input type="hidden" id="allchauffeur" value="<?php echo ($allchauffeur ?? ''); ?>" />
  </div>
  <div class="row" >
    <!--<label id="adresse"></label>-->
    <div  style="display: block; position: fixed; left: 10px; top: 10px; height: 24vh; width: 210px; border: 2px red solid; padding:10px;">
          <label  style="font-size:24px;font-weight:600;min-width:220px">LOCALISATION</label>
          <label style="220px;" >Date<input type="date" style="width:160px" class="form-control" onchange="getDateChange(this.value)" value="<?php echo ($ActiveDate ?? ''); ?>" id="dtedeb" /></label>
            <label style="110px" >Délai<input type="number" style="width:80px" class="form-control" value="10" id="edtLapRefresh" /></label>
            <label style="110px" >Ligne<input type="number" style="width:80px" class="form-control" value="5" id="edtEpaisseur" /></label><br /><br />
      <a href="javascript:showFindAdresse()" style="width: 250px;height:35px;">
              <i class="fa fa-search" style="width: 250px;margin-top:-10px;height:35px; font-weight:bold;font-size:22px">&nbsp;Rechercher</i></a>
            
    </div>
    <div id="divChauffeur" class="border border-secondary" style="display: block; position: fixed; left: 10px; top: 230px; height: 65vh; width: 210px; 
      border: 2px #555 solid;padding:32px; ">

      <div style="overflow: scroll;">
        <?php echo ($chauffeursRte ?? '') ?>
      </div>

    </div>

    <div id="divShowCed" style="margin-top: 10px;margin-left:260px;height: 80%; width: 100%;position:page;">
        <div id='map' style="width: 83%;height:97vh"></div>
    </div>
  </div>
  <input type="hidden" id="idchauffeur" value="<?php echo ($idchauffeur ?? ''); ?>" />
  <input type="hidden" id="nocamion" value="" />
  <input type="hidden" id="NewZoom" value="<?php echo ($newzoom ?? ''); ?>" />

    <div class="modal fade" id="modalFindAdr" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-block-md">
      <div class="modal-content">
        <div  class="modal-header btn-info">
          <h2 class="modal-title">Rechercher une adresse</h2>
        </div>
        <div  class="modal-body">
                      <div id="divInfoReq" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;"> 
                        <h3 >Date</h3>
                        <input type="date" id="edtDateModal" value="" />
                        <h3 >Camions</h3>
                        <select id="selCamModal"  name="selCamModal" class="form-control" style="width:200px;" >
                          
                        </select>
                        <div id="divAdresse" style="display:block">
                          <h3 >Adresse de la ville</h3>
                          <h4>Adresse recherchée(no civique rue, ville)</h4>
                          <div class="input-group" id="divFind">
                            <input type="text"  class="form-control" id="edtFind" placeholder="numéro rue, ville"  value="" />
                            <span class="input-group-addon">
                              <a href="#" onclick="findAdresse()" style="width:35px;height:33px" ><i class="fa fa-search" style="font-weight:bold;font-size:18px"></i></a>
                            </span>
                          </div>
                          <!--<textarea id="txtAdrModal" onclick="goToadresse()" class="form-control" rows="7" cols="50" placeholder="Adresse trouvée"></textarea>-->
                          <select onchange="" id="selAdrModal" onclick="goToAdresse(this.value)" size="7" name="selAdrModal" class="form-control" >
                          
                          </select>
                        </div>


                        <br />
                        <input type="hidden" id="edtIdRepVille" value=""  />
                        <input type="button" class="btn btn-info" id="btnModalRep" style="display:none" value="" />
                      </div> 
        </div>
        <div class="modal-footer btn-info">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Rechercher</button>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>
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

  <script>
    function initMap() {
    const geocoder = new google.maps.Geocoder();
    defaultLoc = { lat: 45.3991735, lng: -74.0780621 };
    
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 8,
        center: defaultLoc, 
        mapTypeId: "roadmap",
        mapId: "2173bf968f39041a",
    });
    map.addListener("zoom_changed", () => {
        const zoom = map.getZoom();
        //$("#NewZoom").val(zoom);
    });
    boucleShowCamionSan();
}
  </script>
  <script
          
    src=
          'https://maps.googleapis.com/maps/api/js?key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4&callback=initMap&libraries=geometry,marker&v=weekly&loading=async'
          async
          defer></script>
  <script>
    window.initMap = initMap;
  </script>
</body>
</html>
