<?php

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo ci3lang('commande_title');?><?= ($numero_commande ?? '') ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style type="text/css">
	body {color: #111; font-family:Arial, Helvetica, sans-serif;}
	a:link {color: #009ddc; font-size:14px;}
	a:visited {color: #009ddc;}
	a:hover {color: #ca006c;}
	.table {width:100%; max-width:800px; font-size:13px;}
	.table p {font-size:11px; line-height:15px; margin:2px 0; color:#444}
	.table.main th {border-bottom:2px #555 solid; padding-bottom:10px;}
	.table.main td {height:30px;}
	.logo {padding-top:15px;}
	.commande {font-size:12px; padding:8px 0 25px 0;}
	.details {font-size:17px; color: black; font-weight:bold; pointer-events: none;}
	.details > a { text-decoration:none; color:inherit; }
	.client {font-size:13px; line-height:18px; pointer-events: none;}
	.client > a {text-decoration:none; color:inherit; }
	.article {width:60%;}
	.prix {width:15%;}
	.quantite {width:10%;}
	.montant {width:15%; padding-right:3%;}
	.nom {color:#777; pointer-events: none;}
	.nom > a { text-decoration:none; color:inherit; }
	.total {color:#000; font-size:16px; font-weight:bold; height:50px;}
	.paiement {border-top:1px #dfdfdf solid; border-bottom:1px #dfdfdf solid; height:40px; color:#CCC;}
	h2 {color:#555; margin:20px 0 5px 0;}
	h3 {font-size:16px; color:black; margin-bottom:5px;}
	h4 {font-size:13px; color:#222; margin:7px 0 3px 0;}
  .s11 {
 font-family: Arial;
 font-size: 13px;
 color: #000000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
}
.s12 {
 font-family: Arial;
 font-size: 9px;
 color: #000000; font-style: normal;
 background-color: transparent;
 text-align: Left; vertical-align: Top;
 padding-left: 20px;
}
.s13 {
 font-family: Arial;
 font-size: 10px;
 color: #800000; font-weight: bold; font-style: normal;
 background-color: transparent;
 text-align: Center; vertical-align: Bottom;
}
</style>

</head>

<body >


  <table class="table" align="center">
    <tr>
      <td valign="top" align="left" class="logo">
        <div>
          <a href="https://www.robertdaoustetfils.com/">
            <img src="https://www.robert-daoust.ca/assets/img/logo.png" alt="Robert Daoust et fils" width="190" height="73" />
          </a>
        </div>
      </td>
      <td valign="top" align="right" class="commande">
        <h4>Date</h4>
        <div class="details">
          <?= ($date ?? '') ?>
        </div>
        <h4>
          <?php echo ci3lang('facture_title');?>
        </h4>
        <div class="details">
          <?= ($numero_commande ?? '') ?>
        </div>
      </td>
    </tr>
    <tr>
      <td valign="top" xalign="center">
        <h3>Client</h3>
        <div class="client">
          <?= ($client_info ?? '') ?>
        </div>
      </td>
      <td valign="top" align="right">
        <h3>
          <?php echo ci3lang('facturation_title');?>
        </h3>
        <div class="client">
          <?= ($livraison_info ?? '') ?>
        </div>
      </td>
    </tr>
  </table>

  <div style="margin-top:10px;">
    <?php echo ($facturedetail ?? ''); ?>
  </div>
  <div style="margin-top:120px;">
    <table  class="table" align="center">
      <tr style="height:30px;">
        <td colspan="9" align="center" style="line-height: 30px;font-size:large; font-weight:bolder;text-transform:uppercase;">CONDITIONS GÉNÉRALES / Terms and conditions</td>
        <td></td>
      </tr>
      <tr style="height:30px;">
        <td colspan="9" align="center" style="line-height: 30px;font-size:large; font-weight:bolder;"></td>
        <td></td>
      </tr>
                      <tr>
                        <td colspan="4" align="center"> <span class="s11">LE CLIENT RECONNA&Icirc;T:</span></td>
                        <td></td>
                        <td colspan="4" align="center">
                          <span class="s11">THE CLIENT MUST:</span>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <ul class="s12">
                            <li>Qu'il doit nous aviser afin de faire effectuer la lev&eacute;e du contenant.</li>
                            <li>Le conteneur doit &ecirc;tre accessible &agrave; nos v&eacute;hicules en tout temps.</li>
                            <li>Assumer le co&ucirc;t si un service de d&eacute;gla&ccedil;age s'av&eacute;rait essentiel.</li>
                            <li>
                              Devoir r&eacute;partir la charge &agrave; l'int&eacute;rieur du contenant defa&ccedil;on &agrave; ce que le poids
                              <br /> soit r&eacute;parti &eacute;quitablement tout en respectant les limites de charges permises
                              <br /> par le code de s&eacute;curit&eacute; routi&egrave;re.
                            </li>
                            <li>
                              Devoir payer pour tous dommages autres qu'usage normal, incluant feu, vol
                              <br /> ou autre cause.
                            </li>
                            <li>
                              Devoir payer 1-1/2% d'int&eacute;r&ecirc;t par mois ou 18% par ann&eacute;e sur comptes
                              <br /> pass&eacute;s d&ucirc;s de plus de 30 jours.
                            </li>
                            <li>
                              Les prix sont faits pour livraison &agrave; la cha&icirc;ne du trottoir. Nous n'assumons
                              <br /> aucune responsabilit&eacute; pour dommages caus&eacute;s, quand la livraison est faite
                              <br /> en dedans du trottoir.
                            </li>
                            <li>Il est strictement d&eacute;fendu de jeter pneus et d&eacute;chets toxiques.</li>
                            <li>L'exc&eacute;dent du tonnage allou&eacute; par conteneur sera factur&eacute; aux taux en vigueur.</li>
                          </ul>
                        </td>
                        <td></td>
                        <td colspan="4">
                            <ul class="s12">
                              <li>
                                Notify us for the emptying of the container
                              </li>
                              <li>Container must be accessible to our vehicules at all times.</li>
                              <li>Assume the cost of the defrosting service if necessary.</li>
                              <li>
                                Verify that the weight of the container's content is well
                             <br />distributed while respecting load limits permitted by
                             the Road Safety Code.
                              </li>
                              <li>
                                Pay for repairs other than ordinary wear and tear, including
                                <br /> fire, theft of else.
                              </li>
                              <li>
                                Pay a monthly 1-1/2% interest or a yearly 18% interest charge
                                <br /> on all 30 days passed due accounts.
                              </li>
                              <li>
                                All delivered prices are for street curb delivery. We will assume
                                <br /> no responsibility for any damages where delivery is made
                                <br /> inside the curb.
                              </li>
                              <li> It is strictly forbidden to throw tires and toxic waste.</li>
                              <li>Excess tonnage allocated per container will be charged at current rates.</li>
                            </ul>
                        </td>
                        
                      </tr>
                      <tr style="height:30px;">
                        <td colspan="9" style="line-height: 30px;">
                        </td>
                        <td></td>
                      </tr>
                      <tr style="height:15px;">
                        <td colspan="9" class="s13">
                          * Le client ne disposera dans le contenant aucune mati&egrave;re autre que celle permise, advenant pr&eacute;sence de contaminants
                          <br /> qui exige une manutention sp&eacute;ciale , une surcharge minimale de 250$ sera applicable et ce sans exception.
                          <br />
                          <br />* The customer will have in the container no matter other than that permitted in the event of contaminants
                          <br /> that requires special handling a minimum surcharge of 250$ will be applicable and without exception.
                        </td>
                        <td></td>
                      </tr>
                    </table>
  </div>


</body>
</html>
