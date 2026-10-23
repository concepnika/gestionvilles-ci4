<?php

?>

<div class="collapse navbar-collapse" id="myNavbar">
  <ul class="nav navbar-nav">
    <li>
      <a href="/admin" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Accueil" data-content="Aller à l'Accueil">
        <i class="fa fa-home" style="font-size:28px;color:white"></i>
      </a>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" style="color:white" title="Afficher la création des usagers et la liste des usagers" data-toggle="dropdown">
				<span class="glyphicon glyphicon-credit-card" style='font-size:24px;color:white;'></span>&nbsp;&nbsp;Gestion des Usagers
      </a>

      <ul class="dropdown-menu">
        <li>
          <a href="/admin/inscription">Créer un usager</a>
        </li>
        <li>
          <a href="/admin/liste_usagers">Liste des usagers</a>
        </li>
        <!--<li>
          <a href="/chauffeurs/login" target="_blank">Chauffeurs</a>
        </li>-->
      </ul>
    </li>
 
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='fa fa-calendar-plus-o' style='font-size:24px;color:white;'></i>&nbsp;&nbsp;Sanivan
      </a>

      <ul class="dropdown-menu">

        <li style="display:flex;align-items:center;justify-content:space-between;padding:3px 10px;">
          <a href="/sanivan/aujourdhui/1/<?php echo date("Y-m-d") ; ?>" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journée active" style="flex:1;">
						<i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Aujourd'hui
          </a>
          <input type="date" id="menuSanivanDate" value="<?php echo date('Y-m-d'); ?>" onchange="if(this.value){window.location.href='/sanivan/aujourdhui/1/'+this.value;}" style="width:130px;height:28px;margin-left:8px;border:1px solid #ccc;border-radius:3px;font-size:12px;" title="Choisir une autre date" />
        </li>
        <li>
          <a href="/sanivan/aujourdhui/8/<?php echo date("Y-m-d") ; ?>" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journée active">
            <i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Aujourd'hui (Vers TV)
          </a>

        </li>
        <li class="divider"></li>
        <li>
          <a href="/sanivan/routecam/<?php echo date("Y-m-d"); ?>?idchauffeur=0&camchauf=" target="_blank">
            <i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Info Route Chauffeur
          </a>

        </li>
        <li>
          <a href="/admin/map-tags" target="_blank">
            <i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Liste des Tags
          </a>

        </li>
          <li class="divider"></li>
        <li>
          <a href="/admin/zones" target="_blank">
            <i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Zone Localisation Villes
          </a>

        </li>
        <li>
          <a href="/ville/nav_zone_hub/<?php echo ci3sess('site_lang') ?: 'fr'; ?>?dte=<?php echo date('Y-m-d'); ?>" target="_blank">
            <i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Localisation Villes
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="/sanivan/getinfoville?idville=0&typederamassage=1&selectedDay=<?php echo date("Y-m").'-01'; ?>&manuel=0&moisseul=0" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Afficher le calendrier des villes">
						<i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Calendrier Villes Année
          </a>

        </li>
        <li>
          <a href="/sanivan/getinfoville?idville=0&typederamassage=1&selectedDay=<?php echo date("Y-m").'-01'; ?>&manuel=0&moisseul=1" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Afficher le calendrier des villes">
						<i class='fa fa-calendar' style='font-size:24px;'></i>&nbsp;&nbsp;Calendrier Villes Mois
          </a>

        </li>
        <li class="divider"></li>
        <li>

          <a href="/sanivan/listpolyline">
            <i class='fa fa-truck' style='font-size:24px'></i>&nbsp;&nbsp;Liste Polylines
          </a>
        </li>
        
        <li class="divider"></li>
        <li>
          <a href="<?php echo ci3_url('samsara_ifta'); ?>" target="_blank">
            <i class="fa fa-circle-o"></i> Km Ontario et Québec (Samsara)
          </a>
        </li>
        <li>
          <a href="/rapport/sani/<?php echo date('Y-m-d') ;?>/0" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Validation Sanivan" data-content="Afficher la page de validation pour les employés et les camions."> 
            <i class='fa fa-tasks' style='font-size:24px;'></i>&nbsp;&nbsp;Validation des routes
          </a>
        </li>
      </ul>
    </li>
    <li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='fa fa-edit' style='font-size:24px;color:white'></i>&nbsp;Gestion
      </a>

      <ul class="dropdown-menu">
        <li>

          <a href="/sanivan/editcam/0?from=0">
            <i class='fa fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Ajouter/Editer des Camions
          </a>
        </li>
        <li class="divider"></li>
        <li>

          <a href="/sanivan/showrh/0?from=0">
            <i class='fa fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Ajouter/&Eacute;diter des Ressources
          </a>
        </li>
        <li class="divider"></li>
        <li>

          <a href="/sanivan/calcam?statut=all">
            <i class='fa fa-calendar' style='font-size:24px'></i>&nbsp;&nbsp;Calendrier des suivis Camions
          </a>
        </li>
        <li>
          <a href="/sanivan/calrh?absence=all">
            <i class='fa fa-calendar' style='font-size:24px'></i>&nbsp;&nbsp;Calendrier des suivis RH
          </a>
        </li>
        <li class="divider"></li>
        <li>

          <a href="/sanivan/showsuivisrhlist/<?php echo date("Y-m-d") ; ?>?from=0&absence=all&order=dateend&sens=desc">
            <i class='fa fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Liste des suivis RH
          </a>
        </li>
        <li>

          <a href="/sanivan/showsuiviscamlist/<?php echo date("Y-m-d") ; ?>?from=0&statut=2&order=dateend&sens=desc">
            <i class='fa fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Liste des suivis Camion
          </a>
        </li>
        <li class="divider"></li>
        <li>

          <a href="/sanivan/showville/0">
            <i class='fa fa-plus' style='font-size:24px'></i>&nbsp;&nbsp;Ajouter/&Eacute;diter des villes
          </a>
        </li>

      </ul>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class="fa fa-bars" style="font-size:24px"></i>
      </a>

      <ul class="dropdown-menu">

        <li>
          <a href="/admin/optionssys" 
            <i class='glyphicon glyphicon-list-alt' style='font-size:24px;'></i>&nbsp;Options sys
          </a>
        </li>
        <li>
          <a href="/sanivan/dictionnaires/1/1" >
            <i class='fa fa-book' style='font-size:24px;'></i>&nbsp;Dictionnaire
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="/rapport/financier">
            <i class='fa fa-line-chart' style='font-size:24px;'></i>&nbsp;Tableau de bord financier
          </a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="/admin/quitter" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter l'application" data-content="Cliquer pour quitter de façon sécuritaire.">

				    <i class="glyphicon glyphicon-user" style="font-size:24px; color:red"></i>&nbsp;Quitter
          </a>

        </li>
      </ul>
    </li>


    <!--<li>
      <div class="input-group input-group-sm"  style="width:200px;margin-top:12px;">
        <input type="text" class="form-control" style="width:240px;height:35px;" id="edtFindGen" onkeyup="EnterearchSite(this.value, event)" placeholder="Rechercher" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Rechercher" data-content="Cliquer pour rechercher dans le site">
        <div class="input-group-btn">
          <button class="btn btn-info" onclick="searchInSite()" style="height:35px" type="button">
            <i class="glyphicon glyphicon-search"></i>
          </button>
        </div>
      </div>
    </li>-->
    <!--<li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='fa fa-bar-chart' style='font-size:24px;color:white'></i>&nbsp;Rapports
      </a>

      <ul class="dropdown-menu">
        <li>
          <a href="/rapport/tdb/none/none" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Avec Matériel" data-content="Afficher la page pour les camions avec matériel">
            <i class='fa fa-object-group' style='font-size:24px;'></i>&nbsp;&nbsp;Roll Off
          </a>

        </li>
        <li>
          <a onclick="ShowRapport(1)" href="#" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Avec Matériel" data-content="Afficher la page pour les camions avec matériel">
            <i class='fa fa-area-chart' style='font-size:24px;'></i>&nbsp;&nbsp;Sanivan
          </a>

        </li>
        <li class="divider"></li>
        <li>
          <a onclick="ShowRapport(2)" href="#" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Hors-service" data-content="Afficher la page pour les camions Hors-service">
            <i class='fa fa-area-chart' style='font-size:24px;'></i>&nbsp;&nbsp;Les deux
          </a>
        </li>
      </ul>
    </li>-->
    <!--<li>
      <a href="/localisation/rolloff" style="color:white">
        <i class='fa fa-book' style='font-size:24px;color:white'></i>&nbsp;Map
      </a>

    </li>-->
  </ul>
</div>
