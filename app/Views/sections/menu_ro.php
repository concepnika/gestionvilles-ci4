<?php

?>

<div class="collapse navbar-collapse" id="myNavbar">
  <ul class="nav navbar-nav">
    <li style="display:<?php echo ($visible ?? ''); ?>">
      <a href="/admin" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Accueil" data-content="Aller à l'Accueil">
        <i class="fa fa-home" style="font-size:28px;color:white"></i>
      </a>
    </li>

    <li style="display:<?php echo ($visible ?? ''); ?>" class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='	fas fa-truck-pickup' style='font-size:24px;color:white'></i>&nbsp;ROLL OFF
      </a>

      <ul class="dropdown-menu">
        <li>
          <a href="/rolloff/aujourdhui/1/<?php echo date("Y-m-d"); ?>" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journée active">
            <i class='fas fa-calendar-check' style='font-size:24px;'></i>&nbsp;&nbsp;Aujourd'hui
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="#" onclick="openRouteCam()">
            <i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Info Route Chauffeur
          </a>

        </li>
        <li class="divider"></li>
        <li>

          <a href="/rolloff/edition_boites?typeboite=tous&champ=&valtofind=&isdispo=all">
            <i class='fas fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Ajouter/Éditer des Boîtes
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="/rapport/dayro/none/1/0" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation RollOff" data-content="Afficher la page de validation pour les employés, les camions et les commandes.">
            <i class='fas fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Vérification des revenus et dépenses
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="#" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation RollOff" data-content="Afficher la page de rapport.">
            <i class='fas fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Tableau de bord des ROLL OFF
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a  href="/rolloff/routecam/<?php echo date("Y-m-d"); ?>?idchauffeur=0&camchauf=" target="_blank">
            <i class='fas fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Routes RollOff
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="#" id="HideEnd" style="color:darkblue;background-color:ghostwhite;">
        
        <label><input type="checkbox" name="chkHideEnd" id="chkHideEnd" onclick="ToogleTerminee()" value="3" />&nbsp; &nbsp;Cacher Terminées</label>

      </a>

    </li>
    <li>
      <a id="show_1" href="javascript:AfficherRollOff(1)" style="color:white">Non Assignées&nbsp;
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
      <a id="show_3" href="javascript:AfficherRollOff(3)" style="color:white">Complétées&nbsp;
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
        <i class='fa fa-users' style='font-size:24px;color:white'></i>&nbsp;Afficher les Chauffeurs
      </a>

      <ul id="ulChauffeur" class="dropdown-menu">

        <?php echo ($actifchauffeur ?? '') ?>
      </ul>
    </li>
    <li>
      <div class="input-group input-group-sm" style="width:200px;margin-top:12px;">
        <input type="text" class="form-control" style="width:240px;height:35px;" id="edtFindGen" onkeyup="EnterearchSite(this.value, event)" placeholder="Rechercher" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Cliquer pour rechercher dans le site" />
        <div class="input-group-btn">
          <button class="btn btn-info" onclick="searchInSite()" style="height:35px" type="button">
            <i class="glyphicon glyphicon-search"></i>
          </button>
        </div>
      </div>
    </li>
  </ul>
  <ul class="nav navbar-nav">
    <li style="display:<?php echo ($visible ?? ''); ?>">
      <a style="color:white;float:right" href="/admin/quitter" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter l'application" data-content="Cliquer pour quitter de façon sécuritaire.">

        <i class="fas fa-user-alt-slash" style="font-size:24px; color:red"></i>
      </a>

    </li>
    <li>
       <?php echo ($boitesmeloche ?? ''); ?>

    </li>
  </ul>
</div>
