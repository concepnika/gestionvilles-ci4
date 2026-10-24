


  <!------ SECTION PRINCIPALE --------------------------------------------------------------------------------------------->



    <section class="container">
      <div class="row">
        <div class="col-sm-4">
          <!--<img src="https://dev.sitedaoust.robert-daoust.ca/assets/00_images/logo.png" width="250" />--></div>
        <div class="col-sm-6 rubriques">
          <h2>Confirmation de paiement Commande# <span style="color:darkred;"><?php  echo ($codepaiement ?? '');?></span> pour</h2>
          <p><?php  echo ($infoclient ?? '');?></p>
          <div>
            <p>
              <ul>
                <li><label><b>Type de carte: </b><span><?php  echo ($cardtype ?? '');?></span></label></li>
                <li><label><b>Montant payé: </b><span style="color:red;font-size:large;font-weight:bold;"><?php  echo ($montant ?? '');?></span></label></li>
                <li><label><b>Code d'autorisation: </b><span><?php  echo ($authCode ?? '');?></span></label></li>
                <li><label><b>Référence de paiement: </b><span><?php  echo ($paymentsReference ?? '');?></span></label></li>
                
              </ul>
            </p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <h2>(450) 458-4340</h2>
          <h4>VAUDREUL-DORION</h4>
          <div>
            2117, route Harwood Vaudreuil-Dorion, Qu&eacute;bec
          </div>
        </div>
      </div>

    </section>
