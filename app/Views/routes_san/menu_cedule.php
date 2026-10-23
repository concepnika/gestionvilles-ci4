<?php

?>

<div class="collapse navbar-collapse"  id="myNavbar">
  <ul class="nav navbar-nav" style="margin-top:5px">
    <li>
      <a href="/admin" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Accueil" data-content="Aller à l'Accueil">
        <i class="fa fa-home" style="font-size:18px;color:whitesmoke;margin-top: -5px;"></i>
      </a>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:100px">
        <span>
          <i class='fa fa-eye-slash' style='font-size:18px;margin-top: -5px;color:whitesmoke;'>&nbsp;Menu</i>
        </span>
      </a>

      <ul class="dropdown-menu">

        <?php echo ($dropmenu ?? '') ; ?>
      </ul>
    </li>
    <li>
      <div class="form-group" style="margin-top:10px">
        <a href="#" onclick="loadTodaySani('-1')" id="divShowHideEmp" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journ&eacute;e pr&eacute;c&eacute;dente" data-content="Affiche la c&eacute;dule de la journ&eacute;e pr&eacute;c&eacute;dente">
          <img src="/assets/img/fleche_left.png" width="30" />&nbsp;
        </a>
        <input type="date" style="height:28px" onchange="loadTodaySani('0')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? '') ?>" id="dteActivePage1" />

        <a href="#" onclick="loadTodaySani('1')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journ&eacute;e suivante" data-content="Affiche la c&eacute;dule de la journ&eacute;e suivante">
          <img src="/assets/img/fleche_right.png" width="30" />&nbsp;
        </a>
      </div>
    </li>
    <li>
      <span>&nbsp;&nbsp;</span>
    </li>

    <li>
      <a id="btnCamMat" href="#" style="margin-left: 5px;" onclick="getModalVille()">
        <i class="fa fa-truck" style='font-size:18px;margin-top: -5px;color:whitesmoke;'>&nbsp;Villes</i>
      </a>
    </li>
    
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:100px">
        <span>
          <i class="glyphicon glyphicon-level-up" style="color:lightyellow;margin-top:-5px;font-size:18px;">Requ&ecirc;tes</i>
        </span>
      </a>

      <ul class="dropdown-menu">
        <li>
          <a href="/villes/show/requete/0" target="_blank" style="margin:4px 5px 0 0;width:100%">Envoyer une Requ&ecirc;te</a>
        </li>
        <li>

          <a href="/villes/liste/requetes" style="margin:4px 5px 0 0;width:100%">Liste des requ&ecirc;tes</a>

        </li>

      </ul>
    </li>
    <li>
      <a href="/rapport/sani/<?php echo date('Y-m-d'); ?>/0" target="_blank" style="color:white" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation Sanivan" data-content="Afficher la page de validation pour les employ&eacute;s et les camions.">
        <i class='fa fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Validation des routes
      </a>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:100px">
        <span>
          <i class="glyphicon glyphicon-user" style="color:lawngreen;margin-top:-5px;font-size:18px;">&nbsp;<span style="color:white">Bienvenue!</span></i>
        </span>
      </a>

      <ul class="dropdown-menu">
        <li>
          <a href="#"><img src="/assets/img/add_user.jpg" width="50" alt="User Image" /><b> Votre Profil</b></a>
        </li>
        <li>
          <a  style="margin-top:5px;width:40px;" href="/admin/quitter" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter l'application" data-content="Cliquer pour quitter de fa&ccedil;on s&eacute;curitaire.">

            <i class="glyphicon glyphicon-user" style="color:red;margin-top:-5px;font-size:18px;">&nbsp;Quitter</i>
          </a>

        </li>

      </ul>
    </li>    
  </ul>

</div>
