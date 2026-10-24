





<!-- FENÊTRE CONFIRMATION -->
<div class="modal fade confirmation" id="confirme_add">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="mytitre"><?php echo ((ci3sess('site_lang') == 'fr')? 'Confirmation' : 'Confirmation');?></h3>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="card-text" ></div>
                <div class="voir" id="bodyConfirme"><?php echo ((ci3sess('site_lang') == 'fr')? 
                                                          'Votre demande d\'information sur nos services a été envoyée et un de nos conseillers vous contactera bientôt. <p>Merci de votre intérêt.</p>' : 
                                                          'Your request for information on our services has been sent and one of our advisers will contact you shortly. <p>Thanks for your interest.</p>');?></div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer" style="background-color:Window;">
                <button type="button" id="btnConfirme" class="btn btn-sm btn-warning" style="display: none;" onclick="SendInfo()" data-dismiss="modal"><?php echo ((ci3sess('site_lang') == 'fr')? 'Confirmer l\'envoi' : 'Confirm shipment');?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
            </div>

        </div>
    </div>
</div>

