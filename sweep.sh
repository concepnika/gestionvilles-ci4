#!/bin/bash
# Balayage CI3 vs CI4 : compare les codes HTTP des routes.
# Usage: ./sweep.sh [filtre]
SESSION=$(/c/wamp64/bin/php/php8.2.29/php.exe /tmp/mksess.php)
FILTRE="${1:-}"
test_url() {
  local u="$1"
  [ -n "$FILTRE" ] && [[ "$u" != *"$FILTRE"* ]] && return
  c4=$(curl -s -o /tmp/c4.out -w "%{http_code} %{size_download}" -b "ci_session=$SESSION" "http://gestionvillesci4.local/$u")
  c3=$(curl -s -o /tmp/c3.out -w "%{http_code} %{size_download}" -b "ci_session=$SESSION" "http://gestionvilles.local/$u")
  s4=${c4%% *}; s3=${c3%% *}
  flag=""; [ "$s4" != "$s3" ] && flag="  <<<<< DIFF"
  printf "CI3=%-13s CI4=%-13s %s%s\n" "$c3" "$c4" "$u" "$flag"
}

# --- pages principales ---
test_url "admin"
test_url "admin/liste_usagers"
test_url "admin/show?id_usager=1"
test_url "admin/inscription"
test_url "admin/optionssys"
test_url "admin/page_de_site/1"
# --- cedule du jour ---
test_url "sanivan/aujourdhui/1/2026-10-23"
test_url "sanivan/cedule/1/2026-10-23/1"
test_url "sanivan/afficher/1"
test_url "sanivan/calendrier"
test_url "sanivan/calendriervilles/0"
test_url "sanivan/getinfoville"
test_url "sanivan/validation"
test_url "sanivan/jour_validation/2026-10-23"
test_url "sanivan/jour_validation_chauffeur/2026-10-23"
# --- RH ---
test_url "sanivan/showrh/1"
test_url "sanivan/showsuivisrhlist/2026-10"
test_url "sanivan/showsuiviscamlist/2026-10"
test_url "sanivan/showmodalrh/1"
test_url "sanivan/rhinfo/1"
test_url "sanivan/calrh"
test_url "sanivan/calcam"
test_url "sanivan/camfromtypecal/1"
test_url "sanivan/typecamcal/1"
test_url "sanivan/findrh"
# --- camions / villes / dictionnaires ---
test_url "sanivan/editcam/1"
test_url "sanivan/showville/1"
test_url "sanivan/dictionnaires/0/0"
test_url "sanivan/showsecteur/1"
test_url "sanivan/camfromtype/1"
test_url "sanivan/camfromtypetdb/1"
test_url "sanivan/typecam/1"
test_url "sanivan/rhfromtype/1"
test_url "sanivan/camioninfo"
test_url "sanivan/showcam/1"
# --- polylines ---
test_url "sanivan/listpolyline"
test_url "sanivan/getsecteursbyville"
test_url "sanivan/getpolylinesbysecteurville"
test_url "sanivan/getpolylinesbysecteur"
test_url "sanivan/getpolylinesgeojson/1"
test_url "sanivan/getpolylinegeojsonbyid/1"
test_url "sanivan/getpolylinecheckboxes"
test_url "sanivan/getselectedpolylines"
# --- requetes ---
test_url "villes/show/requete/1"
test_url "villes/show/reponse/1"
test_url "villes/liste/requetes"
test_url "villes/requetes/verifnewinfo"
test_url "villes/requetepdf"
test_url "villes/reponsepdf/1"
test_url "villes/getnom/1"
test_url "villes/requeteexcel"
test_url "villes/send-reponse"
test_url "requete/send/1"
# --- rapports / exports ---
test_url "excel/cedule/2026-10-23"
test_url "excel/hres/2026-10-23"
test_url "sanivan/calpdf"
test_url "sanivan/calrhpdf"
test_url "sanivan/printhresaides/2026-10-23"
# --- usagers / session ---
test_url "sanivan/getchauffeur/1/2026-10-23"
test_url "sanivan/rhtodrop/1/2026-10-23"
test_url "sanivan/showhresaides/2026-10-23"
test_url "sanivan/needreload/0"
test_url "envoi"
test_url "envoiville"
test_url "ville/showemail/1"
test_url "login/change"
test_url "inscription/getinfo"
