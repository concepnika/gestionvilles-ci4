			<header class="header header-nav-menu">
  <div class="logo-container">
    <a href="/villes/accueil" class="logo">
      <img src="/assets/img/logo.png" width="145" alt="Robert Daoust" />
    </a>
    <button class="btn header-btn-collapse-nav d-lg-none" data-bs-toggle="collapse" data-bs-target=".header-nav">
      <i class="fas fa-bars"></i>
    </button>

    <!-- start: header nav menu -->
    <div class="header-nav collapse">
      <div class="header-nav-main header-nav-main-effect-1 header-nav-main-sub-effect-1">
        <nav>
          <ul class="nav nav-pills" id="mainNav">

            <!--<li class="">-->
              <?php
              $lang = isset($this->session->site_lang) ? $this->session->site_lang : 'fr';
              $idv = isset($this->session->idville) ? (int) $this->session->idville : 0;
              $today = date('Y-m-d');
              $q = ($idv > 0 ? ('?idville='.$idv.'&dte='.$today) : ('?dte='.$today));
              ?>
              <!--<a class="nav-link" style="font-size:16px" href="/ville/nav_zone/<?php echo ($lang ?? ''); ?><?php echo ($q ?? ''); ?>" target="_blank">
                <?php echo ci3lang('localisation'); ?>
              </a>
            </li>-->
            <li class="">
              <a class="nav-link" target="_blank"  style="font-size:16px" href="/profil/<?php echo ($this->session->site_lang ?? ''); ?>"><?php echo ci3lang('mne_profil'); ?></a>

            </li>
            <li class="">
                <a class="nav-link"  style="font-size:16px" href="/villes/quitter/<?php echo ($this->session->site_lang ?? ''); ?>">
                    
                    <?php echo ci3lang('mne_quitter'); ?>
                </a>
            </li>
            <!--<li class="dropdown">
              <a class="nav-link dropdown-toggle" style="font-size:16px" href="#">
                <?php echo ($nomcomplet ?? '') ?>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/profil/<?php echo ($this->session->site_lang ?? ''); ?>"><?php echo ci3lang('mne_profil'); ?></a></li>
                <li class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item" href="/villes/quitter/<?php echo ($this->session->site_lang ?? ''); ?>">
                    <i class="fa fa-user" style="color:red;font-size:30px;margin-top:12px;"></i>
                    <?php echo ci3lang('mne_quitter'); ?>
                  </a>
                </li>
              </ul>
            </li>-->
            <li class="">
              <a class="nav-link" style="font-size:16px" href="javascript:setLanguage('<?php echo ci3lang('menu_link_2'); ?>')"><?php echo ci3lang('inc_menu_langue'); ?></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <!-- end: header nav menu -->
  </div>

</header>
			<!-- end: header -->
