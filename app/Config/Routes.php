<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('whoami', 'Home::whoami', ['filter' => 'ci3auth']);
$routes->get('cedules/(:segment)', 'Cedules::day/$1', ['filter' => 'ci3auth']);

// ─── API tablettes Sanivan (port de Co_Rest_Sani — phase 1) ───────────
// Pas de filtre ci3auth : les tablettes s'authentifient par paramètres
// (CodeUser/password) comme dans CI3.
$routes->match(['get', 'post'], 'sanivan/get/login/(:segment)/(:segment)', 'Api\SaniRest::login/$1/$2');
$routes->match(['get', 'post'], 'sanivan/get', 'Api\SaniRest::indexGet');
$routes->match(['get', 'post'], 'sanivan/post/depot', 'Api\SaniRest::indexPost');
$routes->match(['get', 'post'], 'api/upload', 'Api\SaniRest::indexPost');
$routes->post('sanivan/post/camions', 'Api\SaniRest::postCamion');
$routes->post('api/add_to_polyline', 'Api\SaniRest::saveBatch');
$routes->post('sanivan/send_polyline', 'Api\SaniRest::saveBatch');
$routes->match(['get', 'post'], 'co_rest_sani/saveToFile', 'Api\SaniRest::saveToFile');

// ─── Tablettes Sanivan (port de Co_tablette_sani — phase 2) ──────────
// Mêmes URIs que CI3. Pas de filtre ci3auth : les pages tablettes
// gèrent leur propre flow de session (loginChauffeur écrit dans la
// session CI3 partagée). Les endpoints JSON suivent le comportement CI3.
$routes->get('sanivan/getpolylines_city/(:any)', 'TabletteSani::GetCityPolylines/$1');
$routes->get('sanivan/getpolylines_city_raw/(:any)', 'TabletteSani::GetCityPolylinesRaw/$1');
$routes->get('sanivan/get_polyline_for_date', 'TabletteSani::GetPolylineForDate');
$routes->get('sanivan/get_ramassages_city/(:any)', 'TabletteSani::GetRamassagesForCity/$1');
$routes->get('sanivan/export_gps_camion/(:any)', 'TabletteSani::ExportGpsCamion/$1');
$routes->get('sanivan/get_gps_count_city/(:any)', 'TabletteSani::GetGpsCountCity/$1');
$routes->get('sanivan/bridge_route', 'TabletteSani::GetBridgeRoute');
$routes->get('sanivan/get_last_positions_city/(:any)', 'TabletteSani::GetLastPositionsCity/$1');
$routes->get('sanivan/get_last_pos_camion/(:any)', 'TabletteSani::GetLastPosCamion/$1');
$routes->get('sanivan/routecam/(:any)', 'TabletteSani::GetDayRoad/$1');
$routes->get('sanivan/coors_cam/(:num)/(:any)', 'TabletteSani::getCoordOnly/$1/$2');
$routes->get('sanivan/coors_cam_bureau/(:num)/(:any)', 'TabletteSani::getCoordOnlyOnOffice/$1/$2');
$routes->get('sanivan/getinfolatlng', 'TabletteSani::GetInfoFrom');
$routes->get('sanivan/camera', 'TabletteSani::getcam');
$routes->post('sanivan/storeimage', 'TabletteSani::storeimage');
$routes->match(['get', 'post', 'options'], 'sanivan/import_gps', 'TabletteSani::import_gps_json');
$routes->post('sanivan/loadgpx', 'TabletteSani::getGpxFile');
$routes->post('sanivan/postloctopoly', 'TabletteSani::setNewPolyline');
$routes->get('sanivan/getvillesecteur', 'TabletteSani::getOptionVille');
$routes->get('sanivan/testgoogle', 'TabletteSani::TestGoogle');
$routes->get('sanivan/adressetofind/(:any)/(:any)/(:any)', 'TabletteSani::GetAdresseToFind/$1/$2/$3');
$routes->get('sanivan/adressefromcam/(:any)/(:any)', 'TabletteSani::GetCamionAdresse/$1/$2');
$routes->get('sanivan/gotoadresse/(:num)', 'TabletteSani::showAdresse/$1');
$routes->match(['get', 'post'], 'sanivan/getadresse', 'TabletteSani::getAdresseToShow');
$routes->get('sanivan/getitineraire/(:any)/(:any)/(:any)', 'TabletteSani::getRoadTo/$1/$2/$3');
$routes->get('sanivan/getlocation_san/(:num)/(:num)', 'TabletteSani::getNavigationSan/$1/$2');
$routes->get('sanivan/getpolylinetest', 'TabletteSani::getPolylineTest');
$routes->post('sanivan/sendpolyline/(:any)', 'TabletteSani::PostPolyline/$1');
$routes->get('sanivan/getpolyline_all/(:any)', 'TabletteSani::GetCamionPolylineAll/$1');
$routes->get('sanirest', 'TabletteSani::showTabletteMap');

// Pages / flow session tablettes
$routes->get('tablettes_san/acces', 'TabletteSani::pageLogin');
$routes->get('tablettes_san/add_acces/(:any)', 'TabletteSani::pageAddLogin/$1');
$routes->post('tablettes_san/addtoroutes', 'TabletteSani::addChauffeur');
$routes->post('tablettes_san/routes', 'TabletteSani::loginChauffeur');
$routes->get('tablettes_san/routes/travail', 'TabletteSani::gotoPage');
$routes->get('tablettes_san/quitter', 'TabletteSani::logout');
$routes->get('tablettes_san/inforoute/(:num)', 'TabletteSani::getInfoCeduleRTE/$1');
$routes->get('tablettes_san/camion/suivis/(:any)', 'TabletteSani::getSuivisCamions/$1');
$routes->get('tablettes_san/cedule/(:num)/(:num)', 'TabletteSani::getVilleForSani/$1/$2');
$routes->match(['get', 'post'], 'tablettes_san/sendcaminfo/(:any)/(:any)/(:any)/(:any)/(:any)/(:num)/(:any)/(:any)', 'TabletteSani::postInfoCamion/$1/$2/$3/$4/$5/$6/$7/$8');
$routes->match(['get', 'post'], 'tablettes_san/sendchaufinfo/(:num)/(:any)/(:any)/(:num)/(:any)/(:num)', 'TabletteSani::postInfoChauffeur/$1/$2/$3/$4/$5/$6');
$routes->match(['get', 'post'], 'tablettes_san/sendchaufextra/(:num)/(:any)/(:any)/(:num)/(:num)/(:num)', 'TabletteSani::postInfoExtra/$1/$2/$3/$4/$5/$6');
$routes->match(['get', 'post'], 'tablettes_san/sendaideinfo/(:num)/(:any)/(:any)/(:num)/(:any)/(:num)', 'TabletteSani::postInfoAide/$1/$2/$3/$4/$5/$6');
$routes->match(['get', 'post'], 'tablettes_san/sendvilleinfo/(:num)/(:any)/(:any)/(:num)', 'TabletteSani::postInfoVille/$1/$2/$3/$4');
$routes->match(['get', 'post'], 'tablettes_san/senddepotinfo/(:num)/(:any)/(:any)/(:any)/(:num)/(:num)', 'TabletteSani::postInfoDepotoire/$1/$2/$3/$4/$5/$6');
$routes->get('tablettes_san/scan/ticket/(:num)/(:any)/(:any)/(:num)', 'TabletteSani::scanBillet/$1/$2/$3/$4');
$routes->get('tablettes_san/adddepot/(:num)/(:num)', 'TabletteSani::getNewDepot/$1/$2');
$routes->match(['get', 'post'], 'tablettes_san/postinfosani/(:any)/(:any)', 'TabletteSani::setValForSan/$1/$2');
$routes->match(['get', 'post'], 'tablettes_san/setAvecMat/(:num)/(:num)/(:num)', 'TabletteSani::setAvecMat/$1/$2/$3');
$routes->match(['get', 'post'], 'tablettes_san/setboite/(:num)/(:num)/(:num)', 'TabletteSani::setAvecMat/$1/$2/$3');
$routes->match(['get', 'post'], 'tablettes_san/postchauf', 'TabletteSani::PostCamionJour');
$routes->match(['get', 'post'], 'tablettes_san/postrh', 'TabletteSani::postRHDay');
$routes->match(['get', 'post'], 'tablettes_san/postrhbroker', 'TabletteSani::postRHDayBroker');
$routes->match(['get', 'post'], 'tablettes_san/postville', 'TabletteSani::postVilleDay');
$routes->match(['get', 'post'], 'tablettes_san/postdep', 'TabletteSani::postDepDay');
$routes->match(['get', 'post'], 'tablettes_san/deletesanivanday', 'TabletteSani::deleteSanivanDay');
$routes->post('tablettes_san/saveimg', 'TabletteSani::saveToFile');
$routes->match(['get', 'post'], 'tablettes_san/chgcam/(:any)', 'TabletteSani::postNewCamion/$1');
$routes->match(['get', 'post'], 'tablettes_san/chgchauf/(:any)', 'TabletteSani::postNewChauffeur/$1');
$routes->match(['get', 'post'], 'tablettes_san/addchauf/(:any)/(:num)', 'TabletteSani::setChauffeurOnRoad/$1/$2');
$routes->get('tablettes_san/download/(:num)', 'TabletteSani::DownloadFile/$1');
$routes->get('tablettes_san/tags/city', 'TabletteSani::tags_for_city');
$routes->get('tablettes_san/tags/chauffeur', 'TabletteSani::tags_for_chauffeur');
// Appelé par la carte JS (404 silencieux en CI3 — routé ici car c'est l'intention)
$routes->match(['get', 'post'], 'tablettes_san/track', 'TabletteSani::trackChauffeur');

// Compatibilité routage par défaut CI3 : /co_tablette_sani/<methode>[/args]
$routes->match(['get', 'post'], 'co_tablette_sani', 'TabletteSani::index');
$routes->match(['get', 'post'], 'co_tablette_sani/(:segment)', 'TabletteSani::$1');
$routes->match(['get', 'post'], 'co_tablette_sani/(:segment)/(:any)', 'TabletteSani::$1/$2');

// Non portés — restent servis par CI3 (voir MIGRATION.md) :
//   rapport/sani/(:any)/(:num)      → getSaniReport (machinerie CamDay ~850 lignes)
//   rapport/sani/excel/(:any)       → exportSaniExcel (PHPExcel → PhpSpreadsheet séparé)
//   sanivan/getpolyline/(:any)      → co_polyline/GetCamionPolyline (autre contrôleur)
//   sanivan/ssecam                  → ssecaminfo (credentials en dur — à réécrire)
