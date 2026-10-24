<?php

$_isAdmin = isset($isAdmin) ? $isAdmin : false;
$_pageId  = isset($page_id) ? $page_id : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>SaniVan Web – <?php echo isset($page_title) ? $page_title : 'Accueil'; ?></title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/font-awesome/css/font-awesome.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="/assets/pronto/css/theme-admin-extension.css">

    <style>
        body { font-family: 'Source Sans Pro', sans-serif; background: #ecf0f5; margin: 0; }

        /* ═══ Reproduit MainPage.xaml → Border + staTopBar ═══ */
        .sani-topbar-wrap {
            padding: 10px 10px 6px 10px;
        }
        .sani-topbar {
            border: 4px solid DarkBlue;
            border-radius: 8px;
            padding: 6px 12px;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 0;
            flex-wrap: wrap;
            min-height: 60px;
        }
        .sani-topbar .sani-logo {
            height: 50px;
            width: auto;
            max-width: 150px;
            object-fit: contain;
            margin-right: 4px;
        }
        /* Boutons reproduisant exactement MainPage.xaml */
        .sani-topbar .sani-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            margin: 4px 3px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            padding: 0 14px;
            white-space: nowrap;
            transition: opacity .15s, transform .1s;
            position: relative;
        }
        .sani-topbar .sani-btn:hover { opacity: .88; }
        .sani-topbar .sani-btn:active { transform: scale(.96); }
        .sani-topbar .sani-btn.active-page {
            outline: 3px solid #f39c12;
            outline-offset: -3px;
        }
        /* CAMION/RH – Red bg, white text (WidthRequest=130) */
        .sani-btn-camrh { background: Red; color: #fff !important; min-width: 130px; }
        /* Ville/Dump – Orange bg, black text (WidthRequest=140) */
        .sani-btn-ville { background: Orange; color: #000 !important; min-width: 140px; }
        /* GPS – DarkBlue bg, Orange text (WidthRequest=90) */
        .sani-btn-gps { background: DarkBlue; color: Orange !important; min-width: 90px; }
        /* Tag – DarkRed bg, white text (WidthRequest=80) */
        .sani-btn-tag { background: DarkRed; color: #fff !important; min-width: 80px; }
        /* Menu Nav (admin) – Transparent bg (WidthRequest=80) */
        .sani-btn-nav { background: transparent; color: DarkBlue !important; min-width: 60px; font-size: 28px; padding: 0 8px; }
        .sani-btn-nav img { height: 50px; width: auto; }
        /* Camera – Transparent bg */
        .sani-btn-camera { background: transparent; color: #333 !important; min-width: 60px; font-size: 15px; padding: 0 8px; }
        .sani-btn-camera i { font-size: 28px; margin-right: 4px; }
        /* Help – Transparent bg */
        .sani-btn-help { background: transparent; color: #555 !important; min-width: 50px; font-size: 24px; padding: 0 8px; }
        /* FERMER – LightGrey bg, Red text (WidthRequest=100) */
        .sani-btn-fermer { background: LightGrey; color: Red !important; min-width: 100px; }
        /* Horloge – DarkBlue, 40px (identique lblHorloge) */
        .sani-clock {
            font-size: 36px;
            color: DarkBlue;
            padding: 0 8px;
            font-weight: normal;
            margin-left: auto;
            white-space: nowrap;
        }
        /* Badge tag (reproduit tagBadge XAML) */
        .sani-tag-wrap { position: relative; display: inline-flex; }
        .sani-tag-badge {
            position: absolute;
            top: -4px; right: -6px;
            background: #FF4500;
            color: #fff;
            border: 1px solid #fff;
            border-radius: 12px;
            min-width: 22px; height: 22px;
            font-size: 11px; font-weight: 700;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            z-index: 10;
            pointer-events: none;
        }
        /* Flash animation pour le bouton Tag quand il y a des tags non vus */
        @keyframes saniTagFlash {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; background: #ff0; color: #333; }
        }
        .sani-btn-tag.flashing {
            animation: saniTagFlash 0.8s ease-in-out infinite;
        }
        /* Nom utilisateur (compact) */
        .sani-user {
            display: inline-flex;
            align-items: center;
            color: DarkBlue;
            font-size: 13px;
            margin: 0 6px;
            white-space: nowrap;
        }
        .sani-user i { margin-right: 4px; }
        /* Menu Nav dropdown */
        .sani-nav-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0,0,0,.2);
            z-index: 9000;
            min-width: 200px;
            overflow: hidden;
        }
        .sani-nav-dropdown a {
            display: block;
            padding: 10px 16px;
            color: #333;
            text-decoration: none;
            font-size: 15px;
            border-bottom: 1px solid #eee;
        }
        .sani-nav-dropdown a:last-child { border-bottom: none; }
        .sani-nav-dropdown a:hover { background: #e8f0fe; }
        .sani-nav-dropdown a i { width: 22px; text-align: center; margin-right: 8px; }
        .sani-btn-nav-wrap { position: relative; display: inline-flex; }

        .web-content { padding: 12px 20px; }
        .card-sanivan {
            background: #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,.12);
            margin-bottom: 20px; padding: 20px;
        }
        .badge-cedule {
            display: inline-block; padding: 6px 14px; border-radius: 4px;
            font-weight: bold; margin-right: 6px; cursor: pointer; color: #fff;
        }

        /* ═══════════════════════════════════════════════════════════
           MOBILE : Header compact + menu hamburger (≤768px)
           ═══════════════════════════════════════════════════════════ */

        /* Hamburger bouton – caché sur desktop */
        .sani-hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            z-index: 9100;
            -webkit-tap-highlight-color: transparent;
        }
        .sani-hamburger .bar {
            display: block;
            width: 26px;
            height: 3px;
            background: DarkBlue;
            border-radius: 2px;
            margin: 5px 0;
            transition: transform .3s ease, opacity .2s ease;
        }
        /* Animation X quand menu ouvert */
        .sani-hamburger.open .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .sani-hamburger.open .bar:nth-child(2) { opacity: 0; }
        .sani-hamburger.open .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        /* Menu mobile overlay – caché par défaut */
        .sani-mobile-menu {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,.5);
            z-index: 9000;
            opacity: 0;
            transition: opacity .25s ease;
        }
        .sani-mobile-menu.open {
            opacity: 1;
        }
        .sani-mobile-drawer {
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 280px;
            max-width: 85vw;
            background: #fff;
            box-shadow: 4px 0 20px rgba(0,0,0,.3);
            transform: translateX(-100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .sani-mobile-menu.open .sani-mobile-drawer {
            transform: translateX(0);
        }

        /* En-tête du drawer */
        .sani-drawer-header {
            background: DarkBlue;
            color: #fff;
            padding: 20px 16px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sani-drawer-header img {
            height: 36px;
            width: auto;
            border-radius: 4px;
        }
        .sani-drawer-header .drawer-user {
            font-size: 14px;
            font-weight: 600;
        }
        .sani-drawer-header .drawer-clock {
            font-size: 12px;
            opacity: .8;
            margin-left: auto;
        }

        /* Items du menu */
        .sani-drawer-nav {
            padding: 8px 0;
        }
        .sani-drawer-nav a,
        .sani-drawer-nav button {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 14px 20px;
            border: none;
            background: none;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            transition: background .15s;
            text-align: left;
            -webkit-tap-highlight-color: rgba(0,0,0,.05);
        }
        .sani-drawer-nav a:hover,
        .sani-drawer-nav a:active,
        .sani-drawer-nav button:hover,
        .sani-drawer-nav button:active {
            background: #f0f4fa;
        }
        .sani-drawer-nav a.active-item {
            background: #e8f0fe;
            font-weight: 700;
            border-left: 4px solid #f39c12;
        }
        .sani-drawer-nav .nav-color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .sani-drawer-nav .nav-icon {
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            font-size: 18px;
        }
        .sani-drawer-sep {
            height: 1px;
            background: #e0e0e0;
            margin: 6px 16px;
        }
        /* Fermer en rouge */
        .sani-drawer-nav .nav-fermer {
            color: Red;
            font-weight: 600;
        }

        /* ═══ Breakpoint ≤768px : basculer en mode mobile ═══ */
        @media (max-width: 768px) {
            /* Cacher la barre desktop complète */
            .sani-topbar {
                display: none !important;
            }
            /* Afficher le header mobile compact */
            .sani-mobile-header {
                display: flex !important;
            }
            /* Afficher le hamburger */
            .sani-hamburger {
                display: block;
            }
            /* Réduire padding contenu */
            .web-content {
                padding: 8px 10px;
            }
            /* Topbar wrap compact */
            .sani-topbar-wrap {
                padding: 6px 8px 4px 8px;
            }
        }

        /* Header mobile compact – caché sur desktop */
        .sani-mobile-header {
            display: none;
            align-items: center;
            gap: 8px;
            border: 3px solid DarkBlue;
            border-radius: 8px;
            padding: 6px 10px;
            min-height: 50px;
            background: transparent;
        }
        .sani-mobile-header .sani-logo-sm {
            height: 34px;
            width: auto;
            object-fit: contain;
        }
        .sani-mobile-header .mobile-page-title {
            font-size: 15px;
            font-weight: 700;
            color: DarkBlue;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sani-mobile-header .mobile-clock {
            font-size: 16px;
            color: DarkBlue;
            font-weight: 500;
            white-space: nowrap;
        }

        <?php if (isset($extra_css)) echo ($extra_css ?? ''); ?>
    </style>
    <?php if (isset($head_extra)) echo ($head_extra ?? ''); ?>
</head>
<body>

<?php if (isset($nom) && $nom): ?>
<!-- ═══ Top Bar – Reproduit MainPage.xaml staTopBar ═══ -->
<div class="sani-topbar-wrap">
    <!-- ═══ Header mobile compact (visible ≤768px) ═══ -->
    <div class="sani-mobile-header">
        <button class="sani-hamburger" id="saniHamburger" aria-label="Menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <a href="/web/dashboard">
            <img src="/assets/img/logo.jpg" class="sani-logo-sm" alt="SaniVan" />
        </a>
        <span class="mobile-page-title"><?php echo isset($page_title) ? htmlspecialchars($page_title) : ''; ?></span>
        <span class="mobile-clock" id="saniClockMobile"></span>
    </div>

    <!-- ═══ Barre desktop (visible >768px, identique MainPage.xaml) ═══ -->
    <div class="sani-topbar">
        <!-- Logo (logo.jpg 150px) -->
        <a href="/web/dashboard">
            <img src="/assets/img/logo.jpg" class="sani-logo" alt="SaniVan" />
        </a>

        <!-- CAMION/RH (Red) -->
        <a href="/web/dashboard"
           class="sani-btn sani-btn-camrh <?php echo ($_pageId ?? '') === 'dashboard' ? 'active-page' : ''; ?>">
            CAMION/RH
        </a>

        <!-- Ville/Dump (Orange) -->
        <a href="/web/villes"
           class="sani-btn sani-btn-ville <?php echo ($_pageId ?? '') === 'villes' ? 'active-page' : ''; ?>">
            Ville/Dump
        </a>

        <!-- GPS (DarkBlue/Orange) -->
        <a href="/web/truckmap"
           class="sani-btn sani-btn-gps <?php echo ($_pageId ?? '') === 'truckmap' ? 'active-page' : ''; ?>">
            GPS
        </a>

        <!-- Tag (DarkRed) + badge -->
        <span class="sani-tag-wrap">
            <?php if (!empty($_isAdmin)): ?>
            <a href="javascript:void(0)" onclick="openSaniTagsPopup()" class="sani-btn sani-btn-tag">Tag</a>
            <?php else: ?>
            <a href="/web/truckmap" class="sani-btn sani-btn-tag">Tag</a>
            <?php endif; ?>
            <span class="sani-tag-badge" id="saniTagBadge">0</span>
        </span>

        <?php if (!empty($_isAdmin)): ?>
        <!-- Menu Nav (admin only) – reproduit btnMenuNav + OnMenuNavClicked -->
        <span class="sani-btn-nav-wrap">
            <button type="button" class="sani-btn sani-btn-nav" id="btnSaniMenuNav" title="Navigation">
                <i class="fa fa-th-large" style="font-size: 48px"></i>
            </button>
            <div class="sani-nav-dropdown" id="saniNavDropdown">
                <a href="/sanivan/routecam/<?php echo date("Y-m-d"); ?>?idchauffeur=0&camchauf="><i class="fa fa-truck"></i> Tous les camions</a>
                <a href="/ville/nav_zone_hub/<?php echo ci3sess('site_lang') ?: 'fr'; ?>?dte=<?php echo date('Y-m-d'); ?>"><i class="fa fa-info-circle"></i> Info Villes</a>
                <a href="/admin/tableau_de_bord?email=sdallaire@hotmail.com&password=serge%23123"><i class="fa fa-desktop"></i> Bureau</a>
            </div>
        </span>
        <?php endif; ?>

        <!-- Camera -->
        <a href="/web/truckmap" class="sani-btn sani-btn-camera" title="Caméra / Anomalies">
            <i class="fa fa-camera" style="font-size: 48px"></i>
        </a>

        <!-- Help -->
        <a  class="sani-btn sani-btn-help" onclick="showAideForWEB()" id="btnSaniHelp" title="Aide">
            <i class="fa fa-question-circle" style="font-size: 48px"></i>
        </a>


        <!-- FERMER (LightGrey/Red) -->
        <a href="/web/logout" class="sani-btn sani-btn-fermer">FERMER</a>

        <!-- Horloge (DarkBlue 40px – identique lblHorloge) -->
        <span class="sani-clock" id="saniClock"></span>
    </div>
</div>

<!-- ═══ Menu mobile drawer (visible ≤768px via JS) ═══ -->
<div class="sani-mobile-menu" id="saniMobileMenu">
    <div class="sani-mobile-drawer">
        <div class="sani-drawer-header">
            <img src="/assets/img/logo.jpg" alt="SaniVan" />
            <div>
                <div class="drawer-user"><i class="fa fa-user"></i> <?php echo htmlspecialchars($nom); ?></div>
                <div class="drawer-clock" id="saniClockDrawer"></div>
            </div>
        </div>
        <div class="sani-drawer-nav">
            <a href="/web/dashboard" class="<?php echo ($_pageId ?? '') === 'dashboard' ? 'active-item' : ''; ?>">
                <span class="nav-color-dot" style="background:Red"></span> CAMION/RH
            </a>
            <a href="/web/villes" class="<?php echo ($_pageId ?? '') === 'villes' ? 'active-item' : ''; ?>">
                <span class="nav-color-dot" style="background:Orange"></span> Ville/Dump
            </a>
            <a href="/web/truckmap" class="<?php echo ($_pageId ?? '') === 'truckmap' ? 'active-item' : ''; ?>">
                <span class="nav-color-dot" style="background:DarkBlue"></span> GPS
            </a>
            <?php if (!empty($_isAdmin)): ?>
            <a href="javascript:void(0)" onclick="closeSaniMobileMenu(); openSaniTagsPopup();" class="sani-btn-tag">
                <span class="nav-color-dot" style="background:DarkRed"></span> Tag
            </a>
            <?php else: ?>
            <a href="/web/truckmap" class="sani-btn-tag">
                <span class="nav-color-dot" style="background:DarkRed"></span> Tag
            </a>
            <?php endif; ?>
            <div class="sani-drawer-sep"></div>
            <a href="/web/truckmap">
                <i class="fa fa-camera nav-icon"></i> Caméra
            </a>
            <button type="button" id="btnSaniHelpMobile"  onclick="showAideForWEB()">
                <i class="fa fa-question-circle nav-icon"></i> Aide
            </button>
            <?php if (!empty($_isAdmin)): ?>
            <div class="sani-drawer-sep"></div>
            <a href="/sanivan/routecam/<?php echo date("Y-m-d"); ?>?idchauffeur=0&camchauf=">
                <i class="fa fa-truck nav-icon"></i> Tous les camions
            </a>
            <a href="/ville/nav_zone_hub/<?php echo ci3sess('site_lang') ?: 'fr'; ?>?dte=<?php echo date('Y-m-d'); ?>">
                <i class="fa fa-info-circle nav-icon"></i> Info Villes
            </a>
            <a href="/admin/tableau_de_bord?email=sdallaire@hotmail.com&password=serge%23123">
                <i class="fa fa-desktop nav-icon"></i> Bureau
            </a>
            <?php endif; ?>
            <div class="sani-drawer-sep"></div>
            <a href="/web/logout" class="nav-fermer">
                <i class="fa fa-sign-out nav-icon"></i> FERMER
            </a>
        </div>
    </div>
</div>

<?php if (!empty($_isAdmin)): ?>
<!-- ═══ Modal Tags Admin (reproduit AdminTagsPopup.cs MAUI) ═══ -->
<div class="modal fade" id="saniTagsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:DarkRed;color:#fff;border-radius:6px 6px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.9;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-tags"></i> Tags actifs</h4>
            </div>
            <div class="modal-body" id="saniTagsModalBody" style="max-height:650px;overflow-y:auto;padding:10px;">
                <div class="text-center"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<!-- Contenu -->
<div class="web-content">
    <?php echo ($content ?? ''); ?>
</div>

<!-- Scripts -->
<script src="/assets/pronto/vendor/jquery/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
// ═══ Horloge – reproduit OnTimerTick (desktop + mobile) ═══
(function(){
    var elD  = document.getElementById('saniClock');
    var elM  = document.getElementById('saniClockMobile');
    var elDr = document.getElementById('saniClockDrawer');
    function tick(){
        var d = new Date();
        var hh = ('0'+d.getHours()).slice(-2);
        var mm = ('0'+d.getMinutes()).slice(-2);
        var ss = ('0'+d.getSeconds()).slice(-2);
        var full = hh+':'+mm+':'+ss;
        var short_ = hh+':'+mm;
        if (elD)  elD.textContent  = full;
        if (elM)  elM.textContent  = short_;
        if (elDr) elDr.textContent = full;
    }
    tick();
    setInterval(tick, 1000);
})();

// ═══ Menu Nav dropdown (admin – desktop) ═══
(function(){
    var btn = document.getElementById('btnSaniMenuNav');
    var dd  = document.getElementById('saniNavDropdown');
    if (!btn || !dd) return;
    btn.addEventListener('click', function(e){
        e.stopPropagation();
        dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function(){ dd.style.display = 'none'; });
})();

// ═══ Menu mobile hamburger – slide drawer ═══
var closeSaniMobileMenu; // accessible depuis help mobile
(function(){
    var hamburger = document.getElementById('saniHamburger');
    var menu      = document.getElementById('saniMobileMenu');
    if (!hamburger || !menu) return;

    function openMenu(){
        menu.style.display = 'block';
        // Force reflow pour déclencher la transition CSS
        menu.offsetHeight;
        menu.classList.add('open');
        hamburger.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeMenu(){
        menu.classList.remove('open');
        hamburger.classList.remove('open');
        document.body.style.overflow = '';
        setTimeout(function(){ menu.style.display = 'none'; }, 300);
    }
    closeSaniMobileMenu = closeMenu;

    hamburger.addEventListener('click', function(e){
        e.stopPropagation();
        if (menu.classList.contains('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Clic sur le backdrop (zone sombre) ferme le menu
    menu.addEventListener('click', function(e){
        if (e.target === menu) closeMenu();
    });
})();

// ═══ Tags popup admin (reproduit AdminTagsPopup.cs MAUI) ═══
function _escHtml(v){ if(v==null) return '-'; return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function _tagStatusColor(s){
    switch((s||'').toUpperCase()){
        case 'DONE': return '#2e7d32';
        case 'ACK':  return '#1976d2';
        case 'REJECTED': return '#c62828';
        default: return '#ef6c00';
    }
}
function _tagStatusLabel(s){
    switch((s||'').toUpperCase()){
        case 'DONE': return 'Terminé';
        case 'ACK':  return 'Accusé';
        case 'REJECTED': return 'Rejeté';
        default: return 'En attente';
    }
}
function openSaniTagsPopup(){
    var modal = $('#saniTagsModal');
    if (!modal.length) return;
    modal.modal('show');
    var body = document.getElementById('saniTagsModalBody');
    if (!body) return;
    body.innerHTML = '<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br/>Chargement des tags...</div>';
    fetch('https://www.robert-daoust.ca/ville/maptag/list_all_open?_ts=' + Date.now())
    .then(function(r){ return r.json(); })
    .then(function(j){
        var list = [];
        if (Array.isArray(j)) list = j;
        else if (j && Array.isArray(j.data)) list = j.data;
        else if (j && j.data && Array.isArray(j.data.data)) list = j.data.data;
        else { for (var k in j){ if (Array.isArray(j[k])){ list = j[k]; break; } } }
        list = list.filter(function(t){
            var st = (t.status||t.Status||'').toUpperCase();
            return ['DONE','TERMINE','TERMIN\u00c9','CLOSED'].indexOf(st) === -1;
        });
        if (list.length === 0) {
            body.innerHTML = '<p class="text-muted text-center" style="padding:30px;">Aucun tag actif.</p>';
            return;
        }
        var html = '<div class="list-group" style="margin:0;">';
        list.forEach(function(t){
            var st = (t.status||t.Status||'').toUpperCase();
            var col = _tagStatusColor(st);
            var lat = parseFloat(t.latitude||t.lat||0);
            var lng = parseFloat(t.longitude||t.lng||t.lon||0);
            var title = t.title||t.tag_code||'Tag';
            var addr = t.address_text||t.address||'';
            var chauffeur = t.chauffeur||t.driver||t.driver_name||'';
            html += '<a href="javascript:void(0)" class="list-group-item" '
                + 'onclick="saniCenterOnTag(' + lat + ',' + lng + ')" '
                + 'style="border-left:5px solid ' + col + ';cursor:pointer;">' 
                + '<div style="display:flex;align-items:center;gap:10px;">'
                + '<span style="width:12px;height:12px;border-radius:50%;background:' + col + ';flex-shrink:0;"></span>'
                + '<div style="flex:1;min-width:0;">'
                + '<div style="font-weight:600;font-size:14px;">' + _escHtml(title) + '</div>'
                + (addr ? '<div style="font-size:12px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + _escHtml(addr) + '</div>' : '')
                + (chauffeur ? '<div style="font-size:11px;color:#999;"><i class="fa fa-user"></i> ' + _escHtml(chauffeur) + '</div>' : '')
                + '</div>'
                + '<span class="label" style="background:' + col + ';font-size:11px;">' + _tagStatusLabel(st) + '</span>'
                + '</div></a>';
        });
        html += '</div>';
        body.innerHTML = html;
    })
    .catch(function(e){
        body.innerHTML = '<p class="text-danger text-center" style="padding:30px;"><i class="fa fa-exclamation-triangle"></i> Erreur chargement des tags.</p>';
    });
}
function saniCenterOnTag(lat, lng){
    $('#saniTagsModal').modal('hide');
    if (window.TruckMap && typeof window.TruckMap.ensureFirstCenter === 'function') {
        try { window.TruckMap.setFollowTruck(false); } catch(e){}
        window.TruckMap.ensureFirstCenter(lat, lng);
    } else {
        window.location.href = '/web/truckmap?tagLat=' + lat + '&tagLng=' + lng;
    }
}
function showAideForWEB(){
    window.open('/web/aide',"_blank");
}

// ═══ Tag Badge Flash – Polling + détection "vu" par le chauffeur ═══
(function(){
    var badge = document.getElementById('saniTagBadge');
    var tagBtns = document.querySelectorAll('.sani-btn-tag');
    if (!badge || !tagBtns.length) return;

    var POLL_MS = 30000;
    var STORAGE_KEY = 'saniTagsSeen';
    var currentPageId = '<?php echo ($_pageId ?? ''); ?>';
    var _lastTagIds = [];

    function getSeenIds() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); }
        catch(e) { return {}; }
    }
    function setSeenIds(obj) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(obj)); } catch(e) {}
    }
    function markAllSeen(tagIds) {
        var seen = getSeenIds();
        for (var i = 0; i < tagIds.length; i++) { seen[String(tagIds[i])] = Date.now(); }
        setSeenIds(seen);
        setFlash(false);
    }
    function cleanOldSeen(currentIds) {
        var seen = getSeenIds();
        var currentSet = {};
        for (var i = 0; i < currentIds.length; i++) currentSet[String(currentIds[i])] = true;
        var cleaned = {};
        for (var k in seen) { if (currentSet[k]) cleaned[k] = seen[k]; }
        setSeenIds(cleaned);
    }
    function setFlash(on) {
        for (var i = 0; i < tagBtns.length; i++) {
            if (on) tagBtns[i].classList.add('flashing');
            else tagBtns[i].classList.remove('flashing');
        }
    }
    function updateBadge(count) {
        if (count > 0) {
            badge.textContent = String(count);
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    function pollTags() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/web/api_tag_badge?_ts=' + Date.now(), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4 || xhr.status !== 200) return;
            try {
                var data = JSON.parse(xhr.responseText);
                if (!data || !data.ok) return;
                updateBadge(data.count);
                if (data.count === 0) { setFlash(false); return; }
                var tagIds = data.tag_ids || [];
                _lastTagIds = tagIds;
                cleanOldSeen(tagIds);
                var seen = getSeenIds();
                var hasUnseen = false;
                for (var i = 0; i < tagIds.length; i++) {
                    if (!seen[String(tagIds[i])]) { hasUnseen = true; break; }
                }
                if (hasUnseen) {
                    // Si on est sur la page truckmap, marquer comme vu automatiquement
                    if (currentPageId === 'truckmap') {
                        markAllSeen(tagIds);
                    } else {
                        setFlash(true);
                    }
                } else {
                    setFlash(false);
                }
            } catch(e) {}
        };
        xhr.send();
    }

    // Quand le chauffeur clique sur le bouton Tag → marquer comme vu
    for (var i = 0; i < tagBtns.length; i++) {
        tagBtns[i].addEventListener('click', function() {
            if (_lastTagIds.length > 0) markAllSeen(_lastTagIds);
        });
    }

    // Démarrer le polling
    pollTags();
    setInterval(pollTags, POLL_MS);
})();
</script>
<?php if (isset($foot_extra)) echo ($foot_extra ?? ''); ?>

<?php
// ═══════════════════════════════════════════════════════════════
// Background GPS Persist – Polling Samsara sur TOUTES les pages
// sauf truckmap (qui a déjà son propre polling à 9s).
// Garantit que le traçage GPS continue même quand le chauffeur
// navigue vers dashboard, villes, ou une autre application.
// ═══════════════════════════════════════════════════════════════
$_bgPageId     = isset($page_id) ? $page_id : '';
$_bgNocamion   = ci3sess('web_nocamion');
$_bgVehicleId  = ci3sess('web_id_samsara');
$_bgIdChauf    = ci3sess('web_id_chauffeur');
$_bgLoggedIn   = ci3sess('web_logged_in');
// Réactivé – le polling JS côté client appelle api_samsara_location (endpoint prouvé par truckmap).
// Le mécanisme _autoCronGps() fsockopen a été désactivé côté contrôleur.
if ($_bgLoggedIn && $_bgPageId !== 'truckmap' && (!empty($_bgNocamion) || !empty($_bgVehicleId))):
?>
<script>
// ═══ Background GPS Persist (toutes pages sauf truckmap) ═══
// Polling intelligent : 15s quand l'onglet est actif, 60s en arrière-plan.
// Garantit le traçage GPS même sur dashboard/villes.
(function(){
    var _bgVehicleId  = "<?php echo addslashes($_bgVehicleId ?: ''); ?>";
    var _bgNocamion   = "<?php echo addslashes($_bgNocamion ?: ''); ?>";
    var _bgIdChauf    = "<?php echo addslashes($_bgIdChauf ?: ''); ?>";
    var _bgPollMsFast = 15000;  // 15s – onglet visible
    var _bgPollMsSlow = 60000;  // 60s – onglet en arrière-plan
    var _bgInterval   = null;

    function _bgPoll() {
        var params = '';
        if (_bgVehicleId) params = 'vehicleId=' + encodeURIComponent(_bgVehicleId);
        if (_bgNocamion)  params += (params ? '&' : '') + 'nocamion=' + encodeURIComponent(_bgNocamion);
        if (!params) return;
        if (_bgIdChauf) params += '&idchauffeur=' + encodeURIComponent(_bgIdChauf);
        params += '&IsInZone=0';
        $.getJSON('/web/api_samsara_location?' + params, function(d) {
            if (d && d.ok && d.vehicleId && !_bgVehicleId) {
                _bgVehicleId = d.vehicleId;
            }
        });
    }
    function _bgStart(ms) {
        _bgStop();
        _bgPoll();
        _bgInterval = setInterval(_bgPoll, ms);
    }
    function _bgStop() {
        if (_bgInterval) { clearInterval(_bgInterval); _bgInterval = null; }
    }
    // Démarrer avec l'intervalle approprié selon la visibilité actuelle
    _bgStart(document.hidden ? _bgPollMsSlow : _bgPollMsFast);
    // Adapter l'intervalle quand l'onglet change de visibilité
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            _bgStart(_bgPollMsSlow);
        } else {
            _bgStart(_bgPollMsFast);
        }
    });
})();
</script>
<?php endif; ?>

<script>
// ═══ Anti-cache: empêcher le bouton Retour d'afficher une page protégée après déconnexion ═══
(function(){
    // Vérifie si la page affichée est un cache (pageshow event persisted = true = bfcache)
    window.addEventListener('pageshow', function(e){
        if (e.persisted) {
            // La page vient du cache navigateur (bouton Retour) → recharger depuis le serveur
            window.location.reload();
        }
    });
})();
</script>

</body>
</html>
