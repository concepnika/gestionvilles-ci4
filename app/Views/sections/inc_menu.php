<?php

?>

<div class="collapse navbar-collapse"  id="myNavbar">
  <ul class="nav navbar-nav" style="margin-top:5px">
    <li>
      <a href="/admin" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Accueil" data-content="Aller � l'Accueil">
        <i class="fa fa-home" style="font-size:40px;color:whitesmoke;margin-top: -20px;"></i>
      </a>
    </li>
    <!--<li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:100px">
        <span>
          <i class='fa fa-eye-slash' style='font-size:18px;margin-top: -5px;color:whitesmoke;'>&nbsp;Menu</i>
        </span>
      </a>

      <ul class="dropdown-menu">

        <?php echo $dropmenu ; ?>
      </ul>
    </li>-->

    <li>
      <div class="form-group">
        <a href="#" onclick="loadToday('-1')" id="divShowHideEmp" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journ&eacute;e pr&eacute;c&eacute;dente" data-content="Affiche la c&eacute;dule de la journ&eacute;e pr&eacute;c&eacute;dente">
          <img src="/assets/img/fleche_left.png" width="30" />&nbsp;
        </a>
        <input type="date" style="height:28px" onchange="loadToday('0')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="<?php echo $ActiveDate ?>" id="dteActivePage1" />

        <a href="#" onclick="loadToday('1')" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="journ&eacute;e suivante" data-content="Affiche la c&eacute;dule de la journ&eacute;e suivante">
          <img src="/assets/img/fleche_right.png" width="30" />&nbsp;
        </a>
        <!--<a href="#" onclick="toExcel()" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Jour actif" data-content="Afficher la journ&eacute;e active">
          <img src="/assets/img/excel.png" width="30" />
        </a>-->
      </div>
    </li>


    <li>
      <div ondrop="dropville(event, 1)" ondragover="allowDrop(event)" style="font-weight:bold;font-size:14px;color:black;width: 120px;">
        <label class="form-control" style="width:120px;background-color:aqua;">

          <input id="chkRearLoad" onclick="selGetCedule(this.id,1)" <?php echo (($ActiveDrop == 1)? 'checked' : '' ); ?> type="checkbox" />&nbsp;REAREND
        </label>
      </div>
    </li>
    <li>
      <div style="font-weight:bold;font-size:16px;color:black;width: 120px;" ondrop="dropville(event, 2)" ondragover="allowDrop(event)">
        <label class="form-control" style="width:120px;background-color:chocolate;">
          &nbsp;&nbsp;
          <input id="chkRobot" onclick="selGetCedule(this.id,2)" <?php echo (($ActiveDrop == 2)? 'checked' : '' ); ?> type="checkbox" />&nbsp;ROBOT&nbsp;
        </label>
      </div>
    </li>
    <?php echo (isset($mnusups) ? $mnusups : ''); ?>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:130px">
        <span>
          <i class='fa fa-exchange' style='font-size:18px;margin-top: -5px;color:whitesmoke;'>&nbsp;CAM-RH</i>
        </span>
      </a>

      <ul class="dropdown-menu">
    <li>
      <div ondrop="dropREHors(event, 1, 2)" ondragover="allowDrop(event)" style="font-weight:bold;font-size:14px;color:black;margin-top:10px;">
        <a id="btnCamMat" href="#" style="margin-left: 5px;" onclick="ShowHide('non_dispo_cam')">
          <i class="fa fa-truck" style='font-size:18px;margin-top: -5px;color:black;'>&nbsp;Mat.</i>
        </a>
      </div>
    </li>
    <li>
      <div ondrop="dropREHors(event, 1, 1)" ondragover="allowDrop(event)" style="font-weight:bold;font-size:14px;color:black;margin-top:10px;">
        <a id="btnCamHS" href="#" style="margin-left: 5px;" onclick="ShowHide('hs_dispo_cam')">
          <i class="fa fa-truck" style='font-size: 18px; margin-top: -5px; color: black;'>&nbsp;HS</i>
        </a>
      </div>
    </li>
    <li>
      <div ondrop="dropRHNonDispo(event)" ondragover="allowDrop(event)" style="font-weight:bold;font-size:14px;color:black;margin-top:10px;">
        <a id="btnChaufOut" href="#" style="margin-left: 5px;" onclick="ShowHide('non_dispo_chauf')">
          <i class='fa fa-arrow-circle-down' style='font-size: 18px; margin-top: -5px; color: black;'>&nbsp;Chauf.</i>
        </a>
      </div>
    </li>
    <li>
      <div ondrop="dropRHNonDispo(event)" ondragover="allowDrop(event)" style="font-weight:bold;font-size:14px;color:black;margin-top:10px;">
        <a id="btnAideOut" href="#" style="margin-left: 5px;cursor:pointer;" onclick="ShowHide('non_dispo_aide')">
          <i class='fa fa-arrow-circle-down' style='font-size: 18px; margin-top: -5px; color: black;'>&nbsp;Aides</i>
        </a>
      </div>
    </li>
      </ul>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="width:25px">
        <span>
          <img src="/assets/img/requetes.png" width="30" style="margin-top:-10px" />
        </span>
      </a>

      <ul class="dropdown-menu">
        <?php echo $mnureq ?>
      </ul>
    </li>
    <!--<li>
<a  style="margin-top:5px;width:40px;" href="/admin/quitter" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Quitter l'application" data-content="Cliquer pour quitter de fa&ccedil;on s&eacute;curitaire.">

            <i class="glyphicon glyphicon-user" style="color: whitesmoke; margin-top: -5px; font-size: 18px;">&nbsp;Quitter</i>
          </a>
    </li>
  </ul>-->
  <li>&nbsp;&nbsp;</li>
  <li>&nbsp;&nbsp;</li>
  <ul class="nav navbar-nav pull-right">
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
