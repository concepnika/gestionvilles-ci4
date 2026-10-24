<!DOCTYPE HTML>
<html lang="fr" xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">

<head>


  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="description" lang="fr" content=" " />

  <meta name="robots" content="noindex,nofollow" />
  <meta name="googlebot" content="noindex,nofollow" />

  <title>
    <?php echo ci3lang('titre_site');?>
  </title>
  <?php  echo ($heads ?? '');?>

</head>


<body id="page" class="<?php  echo ($clspage ?? '');?> fr">

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

    <section class="container">
      <div class="row">
        <div class="col-sm-4">
          
        </div>
        <div class="col-sm-6 rubriques">
          <h2><?php echo ((ci3sess('site_lang') == 'fr')? 'Merci pour votre paiement' : 'Thank you for your payment')  ?></h2>
          <p><?php echo ((ci3sess('site_lang') == 'fr')? 'Nous pouvons maintenant procéder avec votre commande.' : 'We can now proceed with your order.')  ?></p>
        </div>
      </div>



    </section>
      <!-- Outro -->
      <section class="container-fluid outro"></section>


</main>




  <!------ FOOTER --------------------------------------------------------------------------------------------------->

  <?php  echo ($footers ?? '');?>
  <?php  echo ($inc_javascript ?? '');?>

  <script></script>

</body>
</html>