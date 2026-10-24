<?php

?>
<li class="treeview" style="min-width:220px">
  
  <a href="#">
    <i class="fa fa-user"></i>
    <span>Usagers</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    <li>
      <a class="nav-link active" href="/admin/inscription">
        <i class="fa fa-circle-o"></i>Créer un usager
      </a>
      <a class="nav-link active" href="/admin/liste_usagers">
        <i class="fa fa-circle-o"></i>Liste des usagers
      </a>
    </li>
  </ul>
    <a href="#">
      <i class="fa fa-table"></i>
      <span>Sanivan</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    </a>
    <ul class="treeview-menu">
      <li>
        <a href="/sanivan/calendrier">
          <i class="fa fa-circle-o"></i> Calendrier des villes
        </a>
      </li>
      <li>
        <a href="/sanivan/aujourdhui/1/<?php echo date("Y-m-d") ; ?>">
          <i class="fa fa-circle-o"></i> Aujourd'hui
        </a>
      </li>
      <li>
        <a href="/sanivan/validation">
          <i class="fa fa-circle-o"></i> Validation
        </a>
      </li>
    </ul>
</li>

<li class="treeview" style="min-width:220px">
  <a href="#">
    <i class="fa fa-pie-chart"></i>
    <span>Métriques</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    <li>
      <a href="/admin/tableau_de_bord/commandes/">
        <i class="fa fa-circle-o"></i> Commandes
      </a>
    </li>
    <li>
      <a href="#">
        <i class="fa fa-circle-o"></i> Revenus
      </a>
    </li>
    <li>
      <a href="#">
        <i class="fa fa-circle-o"></i> Dépenses
      </a>
    </li>
    <li>
      <a href="<?php echo ci3_url('samsara_ifta'); ?>">
        <i class="fa fa-circle-o"></i> Km Ontario (Samsara)
      </a>
    </li>

  </ul>
</li>
