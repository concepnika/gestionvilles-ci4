<?php

?>

<div class="collapse navbar-collapse" id="myNavbar">
  <ul class="nav navbar-nav">
    <li>
      <a href="/admin" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Accueil" data-content="Aller &agrave; l'Accueil">
        <i class="fa fa-home" style="font-size:28px;color:white"></i>
      </a>
    </li>

    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='	fas fa-truck-pickup' style='font-size:24px;color:white'></i>&nbsp;ROLL OFF
      </a>

      <ul class="dropdown-menu">
        <li>
          <a href="/rolloff/aujourdhui/1/<?php echo date("Y-m-d") ; ?>" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
            <i class='fas fa-calendar-check' style='font-size:24px;'></i>&nbsp;&nbsp;Aujourd'hui
          </a>
        </li>
        <li class="divider"></li>
        <li>

          <a href="/rolloff/edition_boites">
            <i class='fas fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Ajouter/&Eacute;diter des Bo&icirc;tes
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="/sanivan/validation" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation Sanivan" data-content="Afficher la page de validation pour les employ&eacute;s et les camions.">
            <i class='fas fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;V&eacute;rification des revenus et d&eacute;penses
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="/sanivan/validation" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation Sanivan" data-content="Afficher la page de validation pour les employ&eacute;s et les camions.">
            <i class='fas fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Tableau de bord des ROLL OFF
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="#" id="HideEnd" style="color:darkblue;background-color:ghostwhite;">

        <label><input type="checkbox" name="chkHideEnd" id="chkHideEnd" onclick="ToogleTerminee()" value="3" />&nbsp; &nbsp;Cacher Termin&eacute;es</label>

      </a>

    </li>
    <li>
      <a id="show_1" href="javascript:AfficherRollOff(1)" style="color:white">Non Assign&eacute;es&nbsp;
<span class="badge badge-danger" id="badge_1" style="background-color:darkred;font-size:large;font-weight:bold;"><?php echo ($btnNotAss ?? '') ?></span>
      </a>

    </li>
    <li>
      <a id="show_2" href="javascript:AfficherRollOff(2)" style="color:white">En Route&nbsp;
        <span class="badge badge-danger" id="badge_2" style="background-color:greenyellow;color:darkblue;font-size:large;font-weight:bold;">
          <?php echo ($btnInRoute ?? '') ?>
        </span>
      </a>

    </li>
    <li>
      <a id="show_3" href="javascript:AfficherRollOff(3)" style="color:white">Compl&eacute;t&eacute;es&nbsp;
        <span class="badge badge-danger" id="badge_3" style="background-color:orange;color:darkblue;font-size:large;font-weight:bold;">
          <?php echo ($btnTerminer ?? '') ?>
        </span>
      </a>

    </li>
    <li >
      <a id="show_4" href="javascript:AfficherRollOff(4)" style="color:white;background-color:indianred">
        Toutes les Commandes&nbsp;
        <span class="badge badge-light" id="badge_4" style="font-size:large;font-weight:bold;">
          <?php echo ($btntotal ?? '') ?>
        </span>
      </a>

    </li>

    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='fas fa-users' style='font-size:24px;color:white'></i>&nbsp;Afficher les Chauffeurs
      </a>

      <ul id="ulChauffeur" class="dropdown-menu">

        <?php echo ($actifchauffeur ?? '') ?>
      </ul>
    </li>
  </ul>
  <ul class="nav navbar-nav">
    <li>
      <a style="color:white;float:right" href="/admin/quitter" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter l'application" data-content="Cliquer pour quitter de fa&ccedil;on s&eacute;curitaire.">

        <i class="fas fa-user-alt-slash" style="font-size:24px; color:red"></i>
      </a>

    </li>
  </ul>
</div>
