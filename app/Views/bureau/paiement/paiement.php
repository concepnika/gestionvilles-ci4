
<!DOCTYPE HTML>
<html lang="fr" xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">

<head>


<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" lang="fr" content=" ">

<meta name="robots" content="noindex,nofollow" />
<meta name="googlebot" content="noindex,nofollow" />

  <title>
    <?php echo ci3lang('titre_site');?>
  </title>
  <?php  echo ($heads ?? '');?>  
	<style>
	.modal-dialog {padding-top: 25%;}
	.alerte {
		text-align: center;
		font-size: 12px;
		line-height: 14px;
		font-weight: bold;
		background-color: #C00;
		color: #FFF;
		padding: 7px 10px;
		margin: 20px 20px;
		border-radius: 5px;}
</style>

<script>
	var error_paiement = "<?php echo ($error_paiement ?? ''); ?>";
	//if (error_paiement != '') {
	//	alert("L'erreur suivante est survenu: " + errorpaiement + ': Recommencer S.V.P.');
	//	location.reload();
 // }
</script>
</head>


<body  id="page" class="<?php  echo ($clspage ?? '');?> fr">

<?php echo ($menu ?? '');?>


<!------ SECTION PRINCIPALE --------------------------------------------------------------------------------------------->            

	<main>
    <section class="container-fluid theme">
      <div class="row">
        <div class="col-xs-12">
          <div class="container">
            <div class="row">
              <div class="col-xs-12">
                <div class="titre wow fadeInDown" data-wow-delay="500ms" data-wow-duration="2000ms">
                  <img src="/assets/00_images/<?php  echo ($titre_img ?? '');?>" />
                </div>
                <h1 class="wow fadeIn" data-wow-delay="1500ms" data-wow-duration="2000ms">
                  <?php  echo ($titrepage ?? '');?>
                </h1>
                <div class="intro wow fadeIn" data-wow-delay="2000ms" data-wow-duration="2000ms">
                  <?php  echo ($texte ?? '');?>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<section id="secPaiement" class="container">

	<div class="section_standard">
		<form name="cardDataForm" id="cardDataForm" action="<?= ci3_url('daoust/send/paiement'); ?>" onsubmit="return validateForm()" method="POST" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12" style="margin-bottom: 30px;">
				<div class="cadre"><span class="paiementtitre"><?php echo ($paiementpour ?? '');?></span></div>
				<div class="cadre"><?php echo ($client_info ?? '');?></div>
			</div>       
		</div> 
		<div class="row">
			<div class="col-xs-12 col-sm-12" style="margin-bottom: 30px;">
				<div class="cadre"><span class="paiementtitre"><?php echo ($montant_total ?? '');?> <?php echo ($numero_commande ?? '');?></span></div>
				<div class="total"><?php echo ((ci3sess('site_lang') == 'fr')? number_format($Total,2,'.',' ') .' $' : '$'.number_format($Total,2,'.',' ') ) ?></div>
			</div>       
		</div> 
		<div class="row">
			<div class="col-xs-12 col-sm-12">

			</div>
			<div class="col-xs-12 col-sm-12">
				<div class="cadre"></div>
				<div class="total"><?php //echo ((ci3sess('site_lang') == 'fr')? number_format($totalTTC,2,'.',' ') .' $' : '$'.number_format($totalTTC,2,'.',' ') ) ?></div>
			</div>
			<div class="col-xs-12 col-sm-12">
				<div class="cadre paiementtitre" style="text-align:center;margin-bottom: 10px;"><?php echo ci3lang('paiement_information');?></div>
				<div class="info">
                    <fieldset>
											<input type="hidden" name="montant" id="montant" value="<?php echo ($Total ?? '');?>">
                        <div class="row espace B10">
                            <div class="col-xs-12" style="text-align:center;margin-bottom: 20px;">
                                <div class="cartes"><img style="width:200px;" src="/assets/00_images/cartes.png"></div>
                            </div>
                        </div>
                        <div class="row espace">
                            <div class="col-xs-4 col-sm-5"><label for="card_name"><?php echo ci3lang('detenteur_carte');?></label></div>
													<input type="hidden" name="codepaiement" id="codepaiement" value="<?php echo ($numero_commande ?? '');?>">
													<input type="hidden" name="page_fr" id="page_fr" value="<?php echo ($page_fr ?? '');?>">
													<input type="hidden" name="client_info" id="client_info" value="<?php echo ($client_info ?? '');?>">
                            <div class="col-xs-8 col-sm-7" xstyle="background-color:#CF0"><input type="text" name="transaction[detenteur]" id="detenteur" value=""  required></div>
                        </div>
                        <div class="row espace">
                            <div class="col-xs-4 col-sm-5"><label for="cc_number"><?php echo ci3lang('paiement_numero_carte');?></label></div>
                            <div class="col-xs-8 col-sm-7" xstyle="background-color:#CF0"><input type="text" name="transaction[cc_number]" id="cc_number" value="" size="25"  minlength="15" required></div>
                        </div>
                        <div class="row espace">
                            <div class="col-xs-4 col-sm-5"><label for="cvd"><?php echo ci3lang('paiement_code_securite');?></label></div>
                            <div class="col-xs-8 col-sm-7"><input type="text" name="transaction[cvd]" id="cvd" value=""size="6" required></div>
                        </div>
                        <div class="row">
                            <input type="hidden" name="amount" value="0" id="amount"><br>
                            <div class="col-xs-4 col-sm-5"><label><?php echo ci3lang('paiement_date_expiration');?></label></div>
                            <div class="col-xs-8 col-sm-7">
                                <select name="transaction[expiry_month]" id="expiry_month">
                                <option value="01">01 - <?php echo ci3lang('cal_january');?></option>
                                <option value="02">02 - <?php echo ci3lang('cal_february');?></option>
                                <option value="03">03 - <?php echo ci3lang('cal_march');?></option>
                                <option value="04">04 - <?php echo ci3lang('cal_april');?></option>
                                <option value="05">05 - <?php echo ci3lang('cal_mayl');?></option>
                                <option value="06">06 - <?php echo ci3lang('cal_june');?></option>
                                <option value="07">07 - <?php echo ci3lang('cal_july');?></option>
                                <option value="08">08 - <?php echo ci3lang('cal_august');?></option>
                                <option value="09">09 - <?php echo ci3lang('cal_september');?></option>
                                <option value="10">10 - <?php echo ci3lang('cal_october');?></option>
                                <option value="11">11 - <?php echo ci3lang('cal_november');?></option>
                                <option value="12">12 - <?php echo ci3lang('cal_december');?></option>
                                </select>&nbsp;<br class="hidden-lg hidden-md"><br class="hidden-lg hidden-md">
                                <select name="transaction[expiry_year]" id="expiry_year">
                                <?php 
                                $year = (int) date('Y');
                                for ($i = $year; $i < $year + 20; $i++ ): ?>
                                <option value="<?= substr((string) $i, -2); ?>"><?= $i; ?></option>
                                <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                            <?php echo ((!isset($error_paiement))? '' : $error_paiement); ?>
                        
                    </fieldset>                   
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12">

			</div>
			<div class="col-xs-12 col-md-12">

			</div>

						<div class="col-xs-12 col-md-3">
			</div>
			<div class="col-xs-12 col-md-6 choix" style="margin-top:30px;text-align:center;">
				
				<button type="submit" class="btn btn-success valider" style="outline:none;" id="valideroff"><?php echo ci3lang('paiement_bouton_valider');?></button>
			</div>  
	        </div>			
		<div class="row">
						<div class="col-xs-12 col-md-5"></div>
            <div class="col-xs-12 col-md-6 choix">
                <div><img src="/assets/00_images/gpaiement.png" height="100"></div>
                <div><?php echo ci3lang('paiement_transaction');?></div>
            </div>				
		</div>
		</form>
		</div>

    </section> <!-- Fin section standard -->
		 <section class="container-fluid outro"></section>
  </main>


<div class="modal" id="paiement_popup" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
        		<div><i class="fa fa-spinner fa-spin"></i></div>
                <div class="traitement"><?php echo ci3lang('paiement_traitement');?></div>
			</div>
		</div>
	</div>
</div>


<?php  echo ($footers ?? '');?>
<?php  echo ($inc_javascript ?? '');?>

<script>
	
	$('#cbxShowHideINFORMATION').click(function(){
	this.checked?$('#information').show(500):$('#information').hide(500); //time for show
	});
	
	$('#cbxShowHideLIVRAISON').click(function(){
	this.checked?$('#livraison').show(500):$('#livraison').hide(500); //time for show
	});

	verifier=0;




	function validateForm() {
		alert('On valide');
	let detenteur = $('#detenteur').val();
  if (detenteur == "") {
    alert("Name must be filled out");
    return false;
	}
	//------------------------------------------
	let cc_number = $('#cc_number').val();
  if (cc_number == "") {
    alert("Card numbert must be filled out");
    return false;
	}
  if (cc_number.length < 25) {
    alert("Card numbert must be 25 caracter minimum");
    return false;
		}
	//------------------------------------------
	let cvd = $('#cvd').val();
  if (cvd == "") {
    alert("CVD must be filled out");
    return false;
	}
	//------------------------------------------
	$('#valideroff').prop('disabled', true);
	$('#valideroff').css("background-color", "#ff0000");
	$('#valideroff').css("width", "350px");
	$('#valideroff').html('<i class="fa fa-spinner fa-spin"></i> <?php echo lang('paiement_etape-finale');?>');
	$('#paiement_popup').modal('show');
	return(true);
  }
</script>




</body>
</html>
