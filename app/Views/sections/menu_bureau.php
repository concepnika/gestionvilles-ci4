<?php

?>

<div class="collapse navbar-collapse" id="myNavbar">
  <ul class="nav navbar-nav">
    
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white">
        <i class='fa fa-phone' style='font-size:24px;color:white;'></i>&nbsp;&nbsp;Bureau
      </a>

      <ul class="dropdown-menu">

        <li>
          <a href="/bureau/commandes/1/<?php echo date("Y-m-d") ; ?>" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Commandes" data-content="Afficher les Commandes">
            <i class='fa fa-plus' style='font-size:24px;'></i>&nbsp;&nbsp;Commandes
          </a>

        </li>
        <!--<li>
          <a href="/bureau/commandes_front/1/<?php echo date("Y-m-d") ; ?>" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Clients" data-content="Afficher les clients">
            <i class='fa fa-plus' style='font-size:24px;'></i>&nbsp;&nbsp;Commandes Front
          </a>
        </li>-->
        <li>
					<a href="/bureau/commandes/2/<?php echo date("Y-m-d") ; ?>" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Clients" data-content="Afficher les clients">
            <i class='fa fa-plus' style='font-size:24px;'></i>&nbsp;&nbsp;Clients
					</a>
        </li>

      </ul>
    </li>
    <li>
      <a href="/rolloff/surplus_boites" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journée active" style="color:white">
        <i class='fa fa-inbox' style='font-size:24px;'></i>&nbsp;&nbsp;Boites en surplus location
      </a>
    </li>
    <li>
      <a href="/rolloff/aujourdhui/1/<?php echo date("Y-m-d") ; ?>" target="_blank" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journée active" style="color:white">
        <i class='fa fa-truck' style='font-size:24px;'></i>&nbsp;&nbsp;Roll Off
      </a>
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
    <li>
      <a style="color:white;float:right" href="/admin/quitter" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter l'application" data-content="Cliquer pour quitter de façon sécuritaire.">

        <i class="glyphicon glyphicon-user" style="font-size:24px; color:red"></i>
      </a>

    </li>
    <li>
      <h3 style="color:white;">&nbsp;&nbsp;&nbsp;Version 4.3.1</h3>

    </li>
  </ul>
</div>
