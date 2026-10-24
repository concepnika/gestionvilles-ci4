<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Table de routage CI4 — replique la nomenclature CI3
 * (application/config/routes.php de GestionVilles).
 *
 * Chaque section correspond a un controleur CI3 et pointe vers le
 * controleur CI4 equivalent. Les methodes non portees sont des stubs
 * qui redirigent vers gestionvilles.local via BaseController::toCi3().
 *
 * Controleurs deja portes : Login/Gestion (auth), TabletteSani,
 * Api\SaniRest, Cedules.
 *
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('whoami', 'Home::whoami', ['filter' => 'ci3auth']);
$routes->get('cedules/(:segment)', 'Cedules::day/$1', ['filter' => 'ci3auth']);

// ─── Authentification (portee — Login) ───
$routes->get('login', 'Login::index');
$routes->post('login', 'Login::attempt');
$routes->get('logout', 'Login::logout');

// ─── co_gestion → Gestion (auth portee, reste = stubs) ───
$routes->get('admin/connexion', 'Gestion::index');
$routes->post('admin/connexion', 'Gestion::login');
$routes->add('admin', 'Gestion::GotoHome');
$routes->add('admin/tableau_de_bord', 'Gestion::login');
$routes->add('admin/quitter', 'Gestion::logout');
$routes->add('admin/quitterchauffeur', 'Gestion::logoutchauffeur');
$routes->add('admin/optionssys', 'Gestion::getOptionSys');
$routes->add('admin/optionssys/post', 'Gestion::setOptionSys');

// ─── co_sessions → Sessions ───
$routes->add('session/checksession', 'Sessions::chekSession');
$routes->add('session/bloque', 'Sessions::lockScreen');

// ─── co_gestion → Gestion ───
$routes->add('session/debloque', 'Gestion::setLastView');
$routes->add('session/relog', 'Gestion::index');

// ─── co_admin → Admin ───
$routes->add('admin/tableau_de_bord/commandes/(:any)', 'Admin::getClientsCharts/$1');

// ─── co_gestion → Gestion ───
$routes->add('admin/usager/ajout', 'Gestion::postNewUser/$1');
$routes->add('admin/inscription', 'Gestion::getInscription');
$routes->add('admin/page_de_site/(:any)', 'Gestion::getPage/$1');
$routes->add('admin/liste_usagers', 'Gestion::getUsagers');
$routes->add('admin/supprimer/(:any)', 'Gestion::removeUser/$1');
$routes->add('admin/remove', 'Gestion::deleteUser');
$routes->add('admin/update', 'Gestion::updateUser');
$routes->add('admin/show', 'Gestion::showUser');

// ─── Upload → Upload ───
$routes->add('charger/(:any)', 'Upload::do_upload/$1');

// ─── co_gestion → Gestion (sanivan/* admin) ───
$routes->add('sanivan/post/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)', 'Gestion::UpdCedule/$1/$2/$3/$4/$5/$6/$7/$8');
$routes->add('sanivan/gethrrh/(:any)', 'Gestion::getHresAides/$1');
$routes->add('sanivan/post', 'Gestion::UpdCedule');
$routes->add('sanivan/add_upd/(:any)/(:any)/(:num)/(:num)/(:num)/(:num)/(:any)/(:any)/(:any)', 'Gestion::PostCedule/$1/$2/$3/$4/$5/$6/$7/$8/$9');
$routes->add('sanivan/aujourdhui/(:num)/(:any)', 'Gestion::showToday/$1/$2');
$routes->add('sanivan/cedule/(:num)/(:any)/(:any)', 'Gestion::showCreateToday/$1/$2/$3');
$routes->add('sanivan/afficher/(:num)', 'Gestion::showLigneCedule/$1');
$routes->add('sanivan/calendrier', 'Gestion::showCedules');
$routes->add('sanivan/calendriervilles/(:any)', 'Gestion::CalendrierDesVilles/$1');
$routes->add('sanivan/getinfoville', 'Gestion::infoVille');
$routes->add('sanivan/senrev', 'Gestion::postRevenu');
$routes->add('sanivan/calculrevenu', 'Gestion::calculerRevenuVille');
$routes->add('sanivan/getrevresultat', 'Gestion::getRevenuResultat');
$routes->add('sanivan/postcaldeploie', 'Gestion::postCalDeploie');
$routes->add('sanivan/postcalendrier/(:num)', 'Gestion::postDaySanivan/$1');
$routes->add('sanivan/delcaldeploie', 'Gestion::removeCalDeploie');
$routes->add('sanivan/postCamionDay/(:num)', 'Gestion::postDayCamion/$1');
$routes->add('sanivan/validation', 'Gestion::showValidSanivan');
$routes->add('sanivan/jour_validation/(:any)', 'Gestion::getValidation/$1');
$routes->add('sanivan/jour_validation_chauffeur/(:any)', 'Gestion::getValidationChauffeur/$1');
$routes->add('sanivan/postsuivis/(:any)/(:any)/(:any)/(:any)/(:any)', 'Gestion::PostSuivis/$1/$2/$3/$4/$5');
$routes->add('sanivan/deletesuivis/(:num)', 'Gestion::DeleteSuivis/$1');
$routes->add('sanivan/postcam/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:num)/(:num)/(:any)/(:num)', 'Gestion::PostCam/$1/$2/$3/$4/$5/$6/$7/$8/$9/$10/$11/$12/$13/$14/$15/$16/$17/$18/$19/$20/$21/$22/$23');
$routes->add('sanivan/deletecam/(:num)', 'Gestion::DeleteCamion/$1');
$routes->add('sanivan/updevent/(:any)/(:any)', 'Gestion::UpdEvent/$1/$2');
$routes->add('sanivan/deleteevent/(:num)', 'Gestion::DeleteEvent/$1');
$routes->add('sanivan/showcam/(:any)', 'Gestion::getSuivisCamion/$1');
$routes->add('sanivan/setstart/(:num)/(:num)/(:any)', 'Gestion::updStart/$1/$2/$3');
$routes->add('sanivan/setemploye/(:num)/(:num)/(:num)/(:any)', 'Gestion::updEmloye/$1/$2/$3/$4');
$routes->add('sanivan/setcamion/(:num)/(:any)/(:any)', 'Gestion::updCamion/$1/$2/$3/');
$routes->add('sanivan/settypeprod/(:num)/(:any)/(:any)', 'Gestion::updTypeProd/$1/$2/$3/');
$routes->add('sanivan/camionout/(:num)', 'Gestion::AddCamionSuivis/$1');
$routes->add('sanivan/camion/(:any)/(:any)/(:any)/(:any)/(:num)/(:any)/(:num)', 'Gestion::AddCamionSuivis/$1/$2/$3/$4/$5/$6/$7');
$routes->add('sanivan/ressourse/(:num)/(:num)/(:any)', 'Gestion::AddRhSuivis/$1/$2/$3');
$routes->add('sanivan/vide_materiel/(:num)/(:any)', 'Gestion::UpdViderMat/$1/$2');
$routes->add('sanivan/retourrh/(:num)/(:any)', 'Gestion::UpdSuivisRHRetour/$1/$2');
$routes->add('sanivan/typeprod/(:num)/(:any)', 'Gestion::setProduit/$1/$2');
$routes->add('sanivan/aide/(:num)/(:num)/(:any)/(:num)', 'Gestion::setAide/$1/$2/$3/$4');
$routes->add('sanivan/aideligne/(:num)/(:num)/(:any)/(:num)', 'Gestion::addLigneAide/$1/$2/$3/$4');
$routes->add('sanivan/camion/(:num)/(:num)/(:any)/(:any)', 'Gestion::setCamion/$1/$2/$3/$4');
$routes->add('sanivan/camionligne/(:num)/(:num)/(:any)/(:any)', 'Gestion::addLigneCamion/$1/$2/$3/$4');
$routes->add('sanivan/secteur/(:num)/(:num)/(:any)', 'Gestion::setSecteur/$1/$2/$3');
$routes->add('sanivan/chauffeur/(:num)/(:num)/(:any)/(:num)', 'Gestion::setChauffeur/$1/$2/$3/$4');
$routes->add('sanivan/removeempl/(:num)/(:num)/(:any)/(:any)', 'Gestion::delEmploye/$1/$2/$3/$4');
$routes->add('sanivan/removecam/(:num)/(:any)', 'Gestion::delCam/$1/$2');
$routes->add('sanivan/chauffeurligne/(:num)/(:num)/(:any)/(:num)', 'Gestion::addLigneChauffeur/$1/$2/$3/$4');
$routes->add('sanivan/addrow/(:num)/(:num)/(:num)/(:num)/(:num)/(:any)/(:any)', 'Gestion::AddRow/$1/$2/$3/$4/$5/$6/$7');
$routes->add('sanivan/addfirstrow/(:num)/(:num)/(:num)/(:num)/(:num)/(:any)/(:any)', 'Gestion::AddFirstRow/$1/$2/$3/$4/$5/$6/$7');
$routes->add('sanivan/addville/(:num)/(:num)/(:num)/(:any)', 'Gestion::AddVille/$1/$2/$3/$4');
$routes->add('sanivan/updville/(:num)/(:num)/(:num)/(:num)/(:any)', 'Gestion::UpdVille/$1/$2/$3/$4/$5');
$routes->add('sanivan/row/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:any)', 'Gestion::updRow/$1/$2/$3/$4/$5/$6/$7');
$routes->add('sanivan/rang/(:num)/(:num)/(:num)/(:num)/(:any)/(:num)', 'Gestion::updRang/$1/$2/$3/$4/$5/$6');
$routes->add('sanivan/villesuperviseur/(:num)/(:any)/(:any)', 'Gestion::setVilleSuperviseur/$1/$2/$3');
$routes->add('sanivan/villereorder/(:num)/(:num)/(:any)', 'Gestion::moveVilleGroupe/$1/$2/$3');
$routes->add('sanivan/superviseurvisible/(:num)/(:num)/(:any)', 'Gestion::setSupVisible/$1/$2/$3');
$routes->add('sanivan/lignecedule/(:num)', 'Gestion::showLigneCedule/$1');
$routes->add('sanivan/getdate/(:any)/(:any)', 'Gestion::getnewDate/$1/$2');
$routes->add('sanivan/chkload/(:num)/(:num)/(:any)', 'Gestion::chkLoad/$1/$2/$3');
$routes->add('sanivan/deleterow/(:num)/(:num)/(:any)', 'Gestion::DeleteRow/$1/$2/$3');
$routes->add('sanivan/deleteville/(:num)/(:num)/(:num)/(:any)', 'Gestion::DeleteVille/$1/$2/$3/$4');
$routes->add('sanivan/copie', 'Gestion::copieCedule');
$routes->add('sanivan/excopie', 'Gestion::excCedule');
$routes->add('sanivan/getweek/(:num)/(:any)', 'Gestion::getWeekCedule/$1/$2');
$routes->add('sanivan/transfert_copie', 'Gestion::postCopyCedule');
$routes->add('sanivan/removecedule/(:num)/(:num)/(:any)/(:any)', 'Gestion::removeCedule/$1/$2/$3/$4');
$routes->add('sanivan/camioninfo', 'Gestion::camionInfo');
$routes->add('sanivan/setisdeletedcam/(:any)', 'Gestion::setIsDeletedCamion/$1');
$routes->add('sanivan/sendcamioninfo/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)', 'Gestion::sendCamionInfo/$1/$2/$3/$4/$5/$6');
$routes->add('sanivan/getallcamion', 'Gestion::getAllCamion');
$routes->add('sanivan/showrh/(:num)', 'Gestion::showRH/$1');
$routes->add('sanivan/showsuivisrhlist/(:any)', 'Gestion::showSuivisRHList/$1');
$routes->add('sanivan/showsuiviscamlist/(:any)', 'Gestion::showSuivisCamList/$1');
$routes->add('sanivan/showmodalrh/(:num)', 'Gestion::getSuivisRH/$1');
$routes->add('sanivan/editcam/(:num)', 'Gestion::edtCam/$1');
$routes->add('sanivan/rhinfo/(:num)', 'Gestion::rhInfo/$1');
$routes->add('sanivan/removerh/(:num)', 'Gestion::DeleteRH/$1');
$routes->add('sanivan/removesuivisrh/(:any)', 'Gestion::DeleteSuivisRH/$1');
$routes->add('sanivan/nbcam/(:num)', 'Gestion::setNbCam/$1');
$routes->add('sanivan/sendrhinfo/(:num)/(:any)/(:any)/(:num)/(:any)/(:any)/(:any)/(:any)/(:any)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)', 'Gestion::sendRHInfo/$1/$2/$3/$4/$5/$6/$7/$8/$9/$10/$11/$12/$13/$14/$15/$16/$17');
$routes->add('sanivan/postrhinfo/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)', 'Gestion::postRH/$1/$2/$3/$4/$5/$6/$7/$8/$9/$10');
$routes->add('sanivan/needreload/(:any)', 'Gestion::getReloadTV/$1');
$routes->add('sanivan/rhtodrop/(:any)/(:any)', 'Gestion::GetRHToDrop/$1/$2');
$routes->add('sanivan/printhresaides/(:any)', 'Gestion::showPrintHresAides/$1');
$routes->add('sanivan/showville/(:num)', 'Gestion::showVille/$1');
$routes->add('sanivan/postvilleinfo/(:num)/(:num)', 'Gestion::postVille/$1/$2');
$routes->add('sanivan/dictionnaires/(:num)/(:num)', 'Gestion::showDictionnaire/$1/$2');
$routes->add('sanivan/postdictionnaire/(:num)', 'Gestion::postDiction/$1');
$routes->add('sanivan/postdictStatutRequete/(:num)', 'Gestion::postStatutRequete/$1');
$routes->add('sanivan/postcontenant/(:num)', 'Gestion::postContenant/$1');
$routes->add('sanivan/camfromtype/(:num)', 'Gestion::getCamFromType/$1');
$routes->add('sanivan/camfromtypetdb/(:num)', 'Gestion::getCamFromTypeTDB/$1');
$routes->add('sanivan/typecam/(:num)', 'Gestion::getTypeCam/$1');
$routes->add('sanivan/rhfromtype/(:num)', 'Gestion::getRHFromType/$1');
$routes->add('sanivan/verifavis/(:any)/(:any)/(:num)', 'Gestion::getAvisVerif/$1/$2/$3');
$routes->add('sanivan/sendmailavis', 'Gestion::sendMailAvis');
$routes->add('sanivan/posthresaides', 'Gestion::postHHelpers');
$routes->add('sanivan/postallinfohres', 'Gestion::postAllHresAide');
$routes->add('sanivan/showhresaides/(:any)', 'Gestion::getHresAides/$1');
$routes->add('sanivan/postviewhs', 'Gestion::postViewHS');
$routes->add('sanivan/reloadcedule', 'Gestion::reLoadCedule');
$routes->add('sanivan/loadcedule', 'Gestion::loadCedule');
$routes->add('sanivan/getchauffeur/(:any)/(:any)', 'Gestion::loadChauffeur/$1/$2');
$routes->add('sanivan/removesecteur/(:num)', 'Gestion::DeleteSecteur/$1');
$routes->add('sanivan/showsecteur/(:num)', 'Gestion::showSecteur/$1');
$routes->add('sanivan/postsecteurinfo/(:num)/(:num)', 'Gestion::postSecteur/$1/$2');
$routes->add('sanivan/chkhsexist/(:any)/(:num)', 'Gestion::getHSExist/$1/$2');
$routes->add('sanivan/chkrhexist/(:num)', 'Gestion::getRHExist/$1');
$routes->add('sanivan/tranfertligne/(:num)/(:num)/(:num)/(:any)', 'Gestion::setTransfertVille/$1/$2/$3/$4');
$routes->add('sanivan/listpolyline', 'Gestion::showPolyline');
$routes->add('sanivan/executeTolerance/(:num)', 'Gestion::getReduitPoly/$1');
$routes->add('sanivan/getsecteurs_by_ville', 'Gestion::getSecteursByVille');
$routes->add('sanivan/getsecteursbyville', 'Gestion::getSecteursByVille');
$routes->add('sanivan/getpolylinesbysecteurville', 'Gestion::getPolylinesBySecteurVille');
$routes->add('sanivan/setpolylinecedule', 'Gestion::setPolylineCedule');
$routes->add('sanivan/getpolylinesgeojson/(:num)', 'Gestion::getPolylinesGeoJson/$1');
$routes->add('sanivan/getpolylinesbysecteur', 'Gestion::getPolylinesBySecteur');
$routes->add('sanivan/setsecteurcedule', 'Gestion::setSecteurCedule');
$routes->add('sanivan/togglepolylinecedule', 'Gestion::togglePolylineCedule');
$routes->add('sanivan/getpolylinecheckboxes', 'Gestion::getPolylineCheckboxes');
$routes->add('sanivan/getselectedpolylines', 'Gestion::getSelectedPolylines');
$routes->add('sanivan/clearallpolylines', 'Gestion::clearAllPolylines');
$routes->add('sanivan/removepolyline/(:num)', 'Gestion::removePolyline/$1');
$routes->add('sanivan/polylinesectremo/(:num)', 'Gestion::removeSctPolyline/$1');
$routes->add('sanivan/movepolylinefirst', 'Gestion::movePolylineToFirst');
$routes->add('sanivan/getpolylinegeojsonbyid/(:num)', 'Gestion::getPolylineGeoJsonById/$1');
$routes->add('requete/send/(:num)', 'Gestion::Requete/$1');
$routes->add('requete/post', 'Gestion::postRequete/$1');

// ─── Co_admin_tags → AdminTags ───
$routes->add('admin/map-tags', 'AdminTags::tags');
$routes->post('admin/map-tags/delete', 'AdminTags::delete_tag');

// ─── co_gestion → Gestion ───
$routes->add('cedule/addsecteur/(:num)', 'Gestion::getNomSecteur/$1');

// ─── co_tablette → Tablette ───
$routes->add('tablettes', 'Tablette::index');
$routes->add('tablettes/acces', 'Tablette::loginChauffeur');

// ─── co_gestion → Gestion ───
$routes->add('envoi/confirm', 'Gestion::setConfirmePresence');
$routes->add('envoi', 'Gestion::sendConfirm');
$routes->add('excel/cedule/(:any)', 'Gestion::expCeduleToExcel/$1');
$routes->add('excel/hres/(:any)', 'Gestion::expHresAidesToExcel/$1');
$routes->add('modal/hres/(:any)', 'Gestion::expHresAidesToExcel/$1');

// ─── co_tvshow → Tvshow ───
$routes->add('robertdaoust', 'Tvshow::index');
$routes->add('robertdaoust/tv', 'Tvshow::index');

// ─── co_gestion → Gestion (login/*, inscription) ───
$routes->add('login/change', 'Gestion::resetPassW');
$routes->add('login/validold', 'Gestion::ValidOldPassW');
$routes->add('login/postchange', 'Gestion::PostresetPassW');
$routes->add('login/validstrong', 'Gestion::ValidStrongPassW');
$routes->add('inscription/getinfo', 'Gestion::sendPassword');
$routes->add('login/confirmeinfo', 'Gestion::excRestPassw');
$routes->add('sanivan/chkrcammodalexist/(:num)', 'Gestion::getSuivisCamExist/$1');
$routes->add('sanivan/calrh', 'Gestion::getCalendrier');
$routes->add('sanivan/getrh', 'Gestion::getRHCal');
$routes->add('sanivan/calcam', 'Gestion::getCalendrierCam');
$routes->add('sanivan/getcam', 'Gestion::getCamCal');
$routes->add('sanivan/calpdf', 'Gestion::makeCamPDF');
$routes->add('sanivan/calrhpdf', 'Gestion::makeRhPDF');
$routes->add('sanivan/camfromtypecal/(:num)', 'Gestion::getCamFromTypeCal/$1');
$routes->add('sanivan/findrh', 'Gestion::getRHtoDropCal');
$routes->add('sanivan/typecamcal/(:num)', 'Gestion::getTypeCamCal/$1');
$routes->add('envoiville', 'Gestion::sendConfirmVille');
$routes->add('ville/showemail/(:num)', 'Gestion::getListMailForVille/$1');
$routes->add('ville/postmail/(:num)/(:num)', 'Gestion::postMail/$1/$2');
$routes->add('ville/send/post/(:num)/(:num)', 'Gestion::ConfirmeVilleMail/$1/$2');

// ─── Co_ville → Ville ───
$routes->add('villes/requetes', 'Ville::index');
$routes->add('villes/requetes/fr', 'Ville::index');
$routes->add('villes/requetes/en', 'Ville::index');
$routes->add('villes/tdb/fr', 'Ville::login');
$routes->add('villes/tdb/en', 'Ville::login');
$routes->add('villes/quitter/fr', 'Ville::logout');
$routes->add('villes/quitter/en', 'Ville::logout');
$routes->add('villes/accueil/fr', 'Ville::goAccueil');
$routes->add('villes/accueil/en', 'Ville::goAccueil');
$routes->add('ville/nav_zone_hub/(:any)', 'Ville::NavZoneHub/$1');
$routes->add('villes/requete/fr/(:num)', 'Ville::getRequete/$1');
$routes->add('villes/requete/en/(:num)', 'Ville::getRequete/$1');
$routes->add('villes/reponse/fr/(:num)', 'Ville::Reponse/$1');
$routes->add('villes/reponse/en/(:num)', 'Ville::Reponse/$1');
$routes->add('villes/requete/liste/fr', 'Ville::getListRequetes');
$routes->add('villes/requete/liste/en', 'Ville::getListRequetes');
$routes->add('villes/confirm/(:num)', 'Ville::setRepReqVue/$1');
$routes->add('villes/post-requete', 'Ville::postRequete');
$routes->add('villes/send/reponse', 'Ville::postReponse');
$routes->add('profil/fr', 'Ville::showProfil');
$routes->add('profil/en', 'Ville::showProfil');
$routes->add('villes/post-profil', 'Ville::postProfil');
$routes->add('villes/adresse/(:num)/(:num)/(:any)', 'Ville::postAdresseRequete/$1/$2/$3');
$routes->add('villes/login/change', 'Ville::resetPassW');
$routes->add('villes/login/validold', 'Ville::ValidOldPassW');
$routes->add('villes/login/postchange', 'Ville::PostresetPassW');
$routes->add('villes/login/validstrong', 'Ville::ValidStrongPassW');
$routes->add('villes/inscription/getinfo', 'Ville::sendPassword');
$routes->add('villes/login/confirmeinfo', 'Ville::excRestPassw');

// ─── co_gestion → Gestion (villes/*) ───
$routes->add('villes/post/requete', 'Gestion::postRequete');
$routes->add('villes/send-reponse', 'Gestion::postReponse');
$routes->add('villes/show/reponse/(:num)', 'Gestion::Reponse/$1');
$routes->add('villes/show/requete/(:num)', 'Gestion::Requete/$1');
$routes->add('villes/post-dtecedule/(:num)/(:num)/(:num)/(:any)', 'Gestion::insertDte/$1/$2/$3/$4');
$routes->add('villes/upd-adresse/(:num)/(:any)', 'Gestion::postAdresseRequete/$1/$2');
$routes->add('villes/confirm/date/(:num)', 'Gestion::setDateRequete/$1');
$routes->add('villes/reponse/vue/(:num)', 'Gestion::setReponseVue/$1');
$routes->add('villes/requete/vue/(:num)', 'Gestion::setRequeteVue/$1');
$routes->add('villes/liste/requetes', 'Gestion::getListRequetes');
$routes->add('villes/requetes/verifnewinfo', 'Gestion::verifRequetes');
$routes->add('villes/requetepdf', 'Gestion::RequetesPDF');
$routes->add('villes/reponsepdf/(:num)', 'Gestion::ReponsesPDF/$1');
$routes->add('villes/getnom/(:num)', 'Gestion::getVilleNom/$1');
$routes->add('villes/requeteexcel', 'Gestion::RequetesEXCEL');

// ─── Co_ville → Ville ───
$routes->add('villes/upload', 'Ville::upload');
$routes->add('villes/summernoteUpload', 'Ville::summernoteImage');
$routes->add('villes/getinforeq', 'Ville::getNewReq');
$routes->add('villes/setlangue', 'Ville::setLanguage');
$routes->add('ville/nav_zone', 'Ville::nav_zone');
$routes->add('ville/nav_zone/fr', 'Ville::nav_zone');
$routes->add('ville/nav_zone/en', 'Ville::nav_zone');
$routes->add('ville/zones_api', 'Ville::zones_api');
$routes->post('ville/saveZone', 'Ville::saveZone');
$routes->post('ville/save_polyline', 'Ville::save_drawn_polyline');

// ─── Co_villes_admin → VillesAdmin ───
$routes->add('villes/zones', 'VillesAdmin::zones_public');
$routes->add('admin/zones', 'VillesAdmin::zones');
$routes->add('admin/zones/save', 'VillesAdmin::saveZone');
$routes->add('admin/zones/byVille', 'VillesAdmin::zonesByVille');
$routes->add('admin/zones/update', 'VillesAdmin::updateZone');
$routes->add('admin/villes', 'VillesAdmin::zones');
$routes->add('admin/villes/list', 'VillesAdmin::listVilles');

// ─── Co_tablette → Tablette ───
$routes->add('tablettes/savelocation', 'Tablette::savelocation');

// ─── co_gestion → Gestion ───
$routes->add('sanivan/rePostAllVilles', 'Gestion::resetAllVille');
$routes->add('sanivan/transfert/collecte/(:any)/(:num)/(:num)', 'Gestion::moveCollecte/$1/$2/$3');
$routes->add('sanivan/remove/collecte/(:any)/(:num)', 'Gestion::delCollecte/$1/$2');

// ─── co_rapport → Rapport ───
$routes->add('rapport/financier', 'Rapport::ShowTDBFinancier/none/none');
$routes->add('rapport/financier/(:any)/(:any)', 'Rapport::ShowTDBFinancier/$1/$2');
$routes->add('rapport/sani/excel/(:any)', 'TabletteSani::exportSaniExcel/$1');

// ─── Sse → Sse ───
$routes->add('sanivan/star_sse', 'Sse::index');

// ─── ssecaminfo → Ssecaminfo ───
$routes->add('sanivan/ssecam', 'Ssecaminfo::index');

// ─── co_polyline → Polyline ───
$routes->add('sanivan/routeforallcam/(:any)', 'Polyline::GetDayRoad/$1');
$routes->add('sanivan/getpolyline/(:any)', 'Polyline::GetCamionPolyline/$1');
$routes->add('sanivan/getpolylineDayTruck/(:any)', 'Polyline::GetCamionPolyline/$1');

// ─── co_osrm → Osrm ───
$routes->add('sanivan/showleaflet', 'Osrm::showLeaflet');

// ─── Polyline → Polyline ───
$routes->add('sanivan/save_polyline', 'Polyline::save');
$routes->add('sanivan/sendlocation_poly/(:num)', 'Polyline::posPolyDynamique/$1');
$routes->add('sanivan/postlocation/(:num)', 'Polyline::posPolyDynamique/$1');
$routes->add('sanivan/postPolyline', 'Polyline::postPolyline');

// ─── Co_sanivan → Sanivan ───
$routes->add('sanivan/upload_pdf_ticket', 'Sanivan::upload_pdf_ticket');

// ─── upload_collecte → UploadCollecte ───
$routes->add('api/upload-collecte', 'UploadCollecte::index');

// ─── Upload_ticket → UploadTicket ───
$routes->add('api/upload-ticket', 'UploadTicket::index');

// ─── Co_ville → Ville (tags/collecte) ───
$routes->add('ville/collecte_points', 'Ville::collecte_points');
$routes->post('ville/tag/create', 'Ville::createCollecteTag');
$routes->post('ville/tag/respond', 'Ville::respondCollecteTag');
$routes->add('ville/tag/list', 'Ville::listCollecteTags');
$routes->post('ville/maptag/create', 'Ville::createMapTag');
$routes->post('ville/maptag/respond', 'Ville::respondMapTag');
$routes->add('ville/maptag/list', 'Ville::listMapTags');

// ─── Co_admin_tags → AdminTags ───
$routes->add('admin/tags/reasons', 'AdminTags::reasons');
$routes->post('admin/tags/reasons/save', 'AdminTags::reasons_save');
$routes->post('admin/tags/reasons/delete/(:num)', 'AdminTags::reasons_delete/$1');

// ─── Co_ville → Ville ───
$routes->post('ville/maptag/update', 'Ville::updateMapTag');
$routes->get('ville/maptag/reasons', 'Ville::mapTagReasons');
$routes->add('ville/maptag/chauffeurs', 'Ville::maptag_chauffeurs');
$routes->add('villes/tags/(:any)', 'Ville::tagsVille');
$routes->add('ville/maptag/list_all_open', 'Ville::listAllOpenMapTags');

// ─── Co_web_chauffeur → WebChauffeur ───
$routes->add('ramassage-ville', 'WebChauffeur::login');
$routes->add('web/doLogin', 'WebChauffeur::doLogin');
$routes->add('web/logout', 'WebChauffeur::logout');
$routes->add('web/dashboard', 'WebChauffeur::dashboard');
$routes->add('web/changeCedule/(:num)', 'WebChauffeur::changeCedule/$1');
$routes->add('web/truckmap', 'WebChauffeur::truckmap');
$routes->add('web/truckmap_embed', 'WebChauffeur::truckmap_embed');
$routes->add('web/villes', 'WebChauffeur::villes');
$routes->add('web/api_getmaxkilo', 'WebChauffeur::api_getmaxkilo');
$routes->add('web/api_samsara_location', 'WebChauffeur::api_samsara_location');
$routes->add('web/api_samsara_history', 'WebChauffeur::api_samsara_history');
$routes->add('web/api_samsara_vehicles', 'WebChauffeur::api_samsara_vehicles');
$routes->add('web/api_polyline', 'WebChauffeur::api_polyline');
$routes->add('web/api_tags', 'WebChauffeur::api_tags');
$routes->add('web/api_camions_road', 'WebChauffeur::api_camions_road');
$routes->add('web/api_post_camion', 'WebChauffeur::api_post_camion');
$routes->add('web/api_post_chauffeur', 'WebChauffeur::api_post_chauffeur');
$routes->add('web/api_post_aide', 'WebChauffeur::api_post_aide');
$routes->add('web/api_list_camions', 'WebChauffeur::api_list_camions');
$routes->add('web/api_list_chauffeurs', 'WebChauffeur::api_list_chauffeurs');
$routes->add('web/api_change_camion', 'WebChauffeur::api_change_camion');
$routes->add('web/api_change_chauffeur', 'WebChauffeur::api_change_chauffeur');
$routes->add('web/api_type_extras', 'WebChauffeur::api_type_extras');
$routes->add('web/api_post_ville', 'WebChauffeur::api_post_ville');
$routes->add('web/api_post_all_ville', 'WebChauffeur::api_post_all_ville');
$routes->add('web/api_post_depot', 'WebChauffeur::api_post_depot');
$routes->add('web/api_post_depotoire', 'WebChauffeur::api_post_depotoire');
$routes->add('web/api_post_all_depot', 'WebChauffeur::api_post_all_depot');
$routes->add('web/api_add_ville', 'WebChauffeur::api_add_ville');
$routes->add('web/api_reload_villes', 'WebChauffeur::api_reload_villes');
$routes->add('web/api_upload_ticket', 'WebChauffeur::api_upload_ticket');
$routes->add('web/api_change_cedule', 'WebChauffeur::api_change_cedule');
$routes->add('web/api_zone_toggle', 'WebChauffeur::api_zone_toggle');
$routes->add('web/api_inzone_history', 'WebChauffeur::api_inzone_history');
$routes->add('web/api_tag_badge', 'WebChauffeur::api_tag_badge');
// Route absente de la table CI3 mais appelée par le JS de truckmap.php
// (bug latent CI3 corrigé au passage — voir MIGRATION.md)
$routes->add('web/api_get_polyline_dynamique', 'WebChauffeur::api_get_polyline_dynamique');
$routes->add('web/aide', 'WebChauffeur::showAidePourWEB');
$routes->add('web/cron_gps_poll', 'WebChauffeur::cron_gps_poll');

// ─── Co_samsara_ifta → SamsaraIfta ───
$routes->add('samsara_ifta', 'SamsaraIfta::index');
$routes->add('samsara_ifta/getData', 'SamsaraIfta::getData');
$routes->add('samsara_ifta/exportExcel', 'SamsaraIfta::exportExcel');

// ─── API tablettes Sanivan (port de Co_Rest_Sani — phase 1) ───────────
// Pas de filtre ci3auth : les tablettes s'authentifient par paramètres
// (CodeUser/password) comme dans CI3.
$routes->match(['GET', 'POST'], 'sanivan/get/login/(:segment)/(:segment)', 'Api\SaniRest::login/$1/$2');
$routes->match(['GET', 'POST'], 'sanivan/get', 'Api\SaniRest::indexGet');
$routes->match(['GET', 'POST'], 'sanivan/post/depot', 'Api\SaniRest::indexPost');
$routes->match(['GET', 'POST'], 'api/upload', 'Api\SaniRest::indexPost');
$routes->post('sanivan/post/camions', 'Api\SaniRest::postCamion');
$routes->post('api/add_to_polyline', 'Api\SaniRest::saveBatch');
$routes->post('sanivan/send_polyline', 'Api\SaniRest::saveBatch');
$routes->match(['GET', 'POST'], 'co_rest_sani/saveToFile', 'Api\SaniRest::saveToFile');

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
$routes->match(['GET', 'POST', 'OPTIONS'], 'sanivan/import_gps', 'TabletteSani::import_gps_json');
$routes->post('sanivan/loadgpx', 'TabletteSani::getGpxFile');
$routes->post('sanivan/postloctopoly', 'TabletteSani::setNewPolyline');
$routes->get('sanivan/getvillesecteur', 'TabletteSani::getOptionVille');
$routes->get('sanivan/testgoogle', 'TabletteSani::TestGoogle');
$routes->get('sanivan/adressetofind/(:any)/(:any)/(:any)', 'TabletteSani::GetAdresseToFind/$1/$2/$3');
$routes->get('sanivan/adressefromcam/(:any)/(:any)', 'TabletteSani::GetCamionAdresse/$1/$2');
$routes->get('sanivan/gotoadresse/(:num)', 'TabletteSani::showAdresse/$1');
$routes->match(['GET', 'POST'], 'sanivan/getadresse', 'TabletteSani::getAdresseToShow');
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
$routes->match(['GET', 'POST'], 'tablettes_san/sendcaminfo/(:any)/(:any)/(:any)/(:any)/(:any)/(:num)/(:any)/(:any)', 'TabletteSani::postInfoCamion/$1/$2/$3/$4/$5/$6/$7/$8');
$routes->match(['GET', 'POST'], 'tablettes_san/sendchaufinfo/(:num)/(:any)/(:any)/(:num)/(:any)/(:num)', 'TabletteSani::postInfoChauffeur/$1/$2/$3/$4/$5/$6');
$routes->match(['GET', 'POST'], 'tablettes_san/sendchaufextra/(:num)/(:any)/(:any)/(:num)/(:num)/(:num)', 'TabletteSani::postInfoExtra/$1/$2/$3/$4/$5/$6');
$routes->match(['GET', 'POST'], 'tablettes_san/sendaideinfo/(:num)/(:any)/(:any)/(:num)/(:any)/(:num)', 'TabletteSani::postInfoAide/$1/$2/$3/$4/$5/$6');
$routes->match(['GET', 'POST'], 'tablettes_san/sendvilleinfo/(:num)/(:any)/(:any)/(:num)', 'TabletteSani::postInfoVille/$1/$2/$3/$4');
$routes->match(['GET', 'POST'], 'tablettes_san/senddepotinfo/(:num)/(:any)/(:any)/(:any)/(:num)/(:num)', 'TabletteSani::postInfoDepotoire/$1/$2/$3/$4/$5/$6');
$routes->get('tablettes_san/scan/ticket/(:num)/(:any)/(:any)/(:num)', 'TabletteSani::scanBillet/$1/$2/$3/$4');
$routes->get('tablettes_san/adddepot/(:num)/(:num)', 'TabletteSani::getNewDepot/$1/$2');
$routes->match(['GET', 'POST'], 'tablettes_san/postinfosani/(:any)/(:any)', 'TabletteSani::setValForSan/$1/$2');
$routes->match(['GET', 'POST'], 'tablettes_san/setAvecMat/(:num)/(:num)/(:num)', 'TabletteSani::setAvecMat/$1/$2/$3');
$routes->match(['GET', 'POST'], 'tablettes_san/setboite/(:num)/(:num)/(:num)', 'TabletteSani::setAvecMat/$1/$2/$3');
$routes->match(['GET', 'POST'], 'tablettes_san/postchauf', 'TabletteSani::PostCamionJour');
$routes->match(['GET', 'POST'], 'tablettes_san/postrh', 'TabletteSani::postRHDay');
$routes->match(['GET', 'POST'], 'tablettes_san/postrhbroker', 'TabletteSani::postRHDayBroker');
$routes->match(['GET', 'POST'], 'tablettes_san/postville', 'TabletteSani::postVilleDay');
$routes->match(['GET', 'POST'], 'tablettes_san/postdep', 'TabletteSani::postDepDay');
$routes->match(['GET', 'POST'], 'tablettes_san/deletesanivanday', 'TabletteSani::deleteSanivanDay');
$routes->post('tablettes_san/saveimg', 'TabletteSani::saveToFile');
$routes->match(['GET', 'POST'], 'tablettes_san/chgcam/(:any)', 'TabletteSani::postNewCamion/$1');
$routes->match(['GET', 'POST'], 'tablettes_san/chgchauf/(:any)', 'TabletteSani::postNewChauffeur/$1');
$routes->match(['GET', 'POST'], 'tablettes_san/addchauf/(:any)/(:num)', 'TabletteSani::setChauffeurOnRoad/$1/$2');
$routes->get('tablettes_san/download/(:num)', 'TabletteSani::DownloadFile/$1');
$routes->get('tablettes_san/tags/city', 'TabletteSani::tags_for_city');
$routes->get('tablettes_san/tags/chauffeur', 'TabletteSani::tags_for_chauffeur');
// Appelé par la carte JS (404 silencieux en CI3 — routé ici car c'est l'intention)
$routes->match(['GET', 'POST'], 'tablettes_san/track', 'TabletteSani::trackChauffeur');

// Compatibilité routage par défaut CI3 : /co_tablette_sani/<methode>[/args]
$routes->match(['GET', 'POST'], 'co_tablette_sani', 'TabletteSani::index');
$routes->match(['GET', 'POST'], 'co_tablette_sani/(:segment)', 'TabletteSani::$1');
$routes->match(['GET', 'POST'], 'co_tablette_sani/(:segment)/(:any)', 'TabletteSani::$1/$2');

// ─── Fallback : tout le reste est relaye a CI3 (proxy interne) ───────
// Doit rester la DERNIERE route. Couvre les assets du docroot CI3
// (upload/*, images, js...), les chemins non declares et toute URL
// qui n'a pas de correspondance CI4.
$routes->add('(:any)', 'Proxy::pass');

// Non portés — restent servis par CI3 (proxy interne) :
//   rapport/sani/(:any)/(:num)      → getSaniReport (machinerie CamDay ~850 lignes)
//   sanivan/ssecam                  → ssecaminfo (credentials en dur — à réécrire)
