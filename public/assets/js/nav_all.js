// Variables globales
// Variables globales
var map;
var markers = [];
var polylines = {}; // nocamion -> polyline (compat)
var trucksByCam = {}; // nocamion -> { marker, polyline, segPolylines[], color, lastMeta, lastLatLng, points[] }
var IsBusy = false;
var colors = ['red', 'blue', 'darkcyan', 'fuchsia', 'black', 'darkgreen',
    'darkred', 'forestgreen', 'purple', 'darkblue', 'black', 'royalblue',
    'firebrick',
    'maroon',
    'saddlebrown',
    'darkslateblue',
    'midnightblue',
    'teal'];
var colorInUse = [];
var allCam = [];
var continuePolling = true;
var polylinesPreloaded = false;
var tagStatusCache = {}; // id -> last known STATUS
var lastCamDriverByCam = {}; // nocamion -> dernier idchauffeur vu dans latlng
var currentSelectedVilleForTags = null;
// Helpers
// Helpers
function baseUrl(p) {
    // Décommentez cette ligne si vous avez base_url PHP, sinon utilisez le chemin relatif
    return '/' + String(p || '').replace(/^\/+/, '');
}
async function qFetchJson(url, init) { const r = await fetch(url, init); if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); }
function escapeHtml(v) { if (v === null || v === undefined) return ''; return String(v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
// Helper générique pour charger et parser une polyline depuis une URL donnée
function fetchPolylineUrl(url, cam, label) {
    return new Promise(function (resolve, reject) {
        $.ajax({ url: url, async: true, context: document.body })
            .done(function (data) {
                try {
                    
                    var parsed = parsePolylineAllResponse(data);
                    resolve(parsed);
                } catch (e) { reject(e); }
            })
            .fail(function (err) { reject(err); });
    });
}
// Normalise la clé camion (trim + string) — utiliser partout pour indexer trucksByCam
function normCamKey(cam) {
    if (cam === undefined || cam === null) return '';
    return String(cam).trim();
}
// Carte / camions
function initMap() {
    var defaultLoc = [45.3991735, -74.0780621];
    map = L.map('map').setView(defaultLoc, 10, { animate: true, duration: 1.0 });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' }).addTo(map);

    setTimeout(function () { try { map.invalidateSize(true); } catch (e) { } }, 100);
    window.addEventListener('resize', function () { try { map.invalidateSize(true); } catch (e) { } });

    initTagsAfterMap();
    boucleShowCamionSan();
}
// helper: affiche notification flottante listant les nouveaux tags VALIDER
function showValidatedNotification(newTags) {
    try {
        if (!Array.isArray(newTags) || newTags.length === 0) return;
        // supprimer notif existante
        var existing = document.getElementById('validated-notif');
        if (existing) existing.parentNode.removeChild(existing);

        var div = document.createElement('div');
        div.id = 'validated-notif';
        Object.assign(div.style, {
            position: 'fixed',
            left: '12px',
            bottom: '12px',
            width: '320px',
            maxHeight: '40vh',
            overflow: 'auto',
            zIndex: 100000,
            background: '#fff',
            border: '1px solid #ccc',
            boxShadow: '0 6px 18px rgba(0,0,0,.25)',
            padding: '10px',
            borderRadius: '6px',
            fontFamily: 'Arial, Helvetica, sans-serif',
            fontSize: '13px'
        });

        var title = document.createElement('div');
        title.style.fontWeight = '700';
        title.style.marginBottom = '8px';
        title.textContent = 'Nouveaux tags à valider';
        div.appendChild(title);

        newTags.slice(0, 10).forEach(function (t) {
            var row = document.createElement('div');
            row.style.display = 'flex';
            row.style.alignItems = 'center';
            row.style.marginBottom = '6px';

            var info = document.createElement('div');
            info.style.flex = '1';
            info.style.overflow = 'hidden';
            info.style.whiteSpace = 'nowrap';
            info.style.textOverflow = 'ellipsis';
            var label = (t.title || t.tag_code || '').trim() || (t.address_text || '').trim() || ('Tag ' + (t.id || ''));
            info.textContent = label;
            row.appendChild(info);

            var btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-primary';
            btn.style.marginLeft = '8px';
            btn.textContent = 'Localiser';
            btn.onclick = function () {
                // localiser et ouvrir popup
                if (!locateTagOnMap(String(t.id))) {
                    alert('Marqueur non trouvé pour le tag. Rafraîchir la page si nécessaire.');
                }
            };
            row.appendChild(btn);

            div.appendChild(row);
        });

        var footer = document.createElement('div');
        footer.style.display = 'flex';
        footer.style.justifyContent = 'space-between';
        footer.style.marginTop = '8px';

        var btnClose = document.createElement('button');
        btnClose.className = 'btn btn-sm btn-outline-secondary';
        btnClose.textContent = 'Fermer';
        btnClose.onclick = function () { try { div.parentNode.removeChild(div); } catch (e) { } };
        footer.appendChild(btnClose);

        var btnAll = document.createElement('button');
        btnAll.className = 'btn btn-sm btn-info';
        btnAll.textContent = 'Afficher tous';
        btnAll.onclick = function () {
            // centrer sur le premier tag de la liste
            if (newTags.length > 0) locateTagOnMap(String(newTags[0].id));
        };
        footer.appendChild(btnAll);

        div.appendChild(footer);

        document.body.appendChild(div);

        // auto dismiss after 40s
        setTimeout(function () { try { if (div.parentNode) div.parentNode.removeChild(div); } catch (e) { } }, 40000);
    } catch (e) {
        console.warn('showValidatedNotification error', e);
    }
}



function getDateChange(dte) {
    $("#dtedeb").val(dte);
    $("#allchauffeur").val(1);
    $("#idchauffeur").val(0);
    resetAllTrucks();
    var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?idchauffeur=0&allchauffeur=1';
    document.location = urlName;
}
// helper: localiser un tag par id (centre + ouvre popup)
function locateTagOnMap(tagId) {
    try {
        var mk = tagMarkersById[tagId];
        if (mk) {
            var latlng = mk.getLatLng();
            if (latlng) {
                try { map.setView(latlng, Math.max(14, (parseInt(document.getElementById('NewZoom').value || 8, 10)))); } catch (e) { }
                try { mk.openPopup(); } catch (e) { }
                return true;
            }
        }
    } catch (e) { console.warn('locateTagOnMap error', e); }
    return false;
}

function createTruckMarker(position, cam) {
    var imgUrl = 'https://www.robert-daoust.ca/assets/img/numcam/cam' + cam.trim() + '.png';
    var truckIcon = L.icon({ iconUrl: imgUrl, iconSize: [40, 40], iconAnchor: [20, 30] });
    var marker = L.marker([position.lat, position.lng], { icon: truckIcon }).addTo(map);
    marker.bindPopup(buildTruckPopup(cam, null));
    marker.on('click', function () {
        var t = trucksByCam[cam];

        // Si les segments ne sont pas encore chargés, forcer le chargement
        if (!t || !t.segPolylines || t.segPolylines.length === 0) {
            var idCh = lastCamDriverByCam[cam] || parseInt($("#idchauffeur").val()) || 0;
            fetchAndSetPolylineForTruck(cam, idCh).then(function () {
                // Une fois chargés, afficher
                hideOtherTrucksSegments(cam);
                showTruckSegments(cam);
                var truck = trucksByCam[cam];
                if (truck && truck.segPolylines && truck.segPolylines.length > 0) {
                    try { map.fitBounds(L.featureGroup(truck.segPolylines).getBounds(), { padding: [50, 50], maxZoom: 15 }); } catch (e) { }
                } else if (truck && truck.marker) {
                    try { map.setView(truck.marker.getLatLng(), 15); } catch (e) { }
                }
            }).catch(function (err) {
                console.error('Erreur chargement polyline au clic:', err);
            });
        } else {
            // Les segments sont déjà là, juste les afficher
            hideOtherTrucksSegments(cam);
            showTruckSegments(cam);
            if (t.segPolylines && t.segPolylines.length > 0) {
                try { map.fitBounds(L.featureGroup(t.segPolylines).getBounds(), { padding: [50, 50], maxZoom: 15 }); } catch (e) { }
            } else if (t.marker) {
                try { map.setView(t.marker.getLatLng(), 15); } catch (e) { }
            }
        }
    });
    return marker;
}

// Fonction pour afficher SEULEMENT le camion sélectionné
// Fonction pour afficher SEULEMENT le camion sélectionné
function showOnlyThisTruck(cam, idville) {
    cam = normCamKey(cam);
    if (!cam) return;

    // Mémoriser le camion sélectionné pour le rafraîchissement automatique
    _currentSelectedCam = cam;

    // Propager le contexte idville pour création de tags (préférence : param > DOM)
    if (typeof idville !== 'undefined' && idville !== null && String(idville).trim() !== '') {
        currentSelectedVilleForTags = String(idville).trim();
    } else {
        // si pas fourni, on garde le contexte existant (null ou précédent)
    }

    console.log('[showOnlyThisTruck] Affichage exclusif pour cam=' + cam + ' idville=' + (currentSelectedVilleForTags || '(none)'));

    // 1. Cacher TOUS les autres camions (markers ET segments)
    Object.keys(trucksByCam).forEach(function (otherCam) {
        if (normCamKey(otherCam) === cam) return; // Sauf celui sélectionné

        var t = trucksByCam[otherCam];
        if (!t) return;

        try { if (t.marker && map.hasLayer(t.marker)) map.removeLayer(t.marker); } catch (e) { }
        if (Array.isArray(t.segPolylines)) {
            t.segPolylines.forEach(function (pl) { try { if (map.hasLayer(pl)) map.removeLayer(pl); } catch (e) { } });
        }
        if (t.segPolylinesByVille) {
            Object.keys(t.segPolylinesByVille).forEach(function (v) {
                (t.segPolylinesByVille[v] || []).forEach(function (pl) { try { if (map.hasLayer(pl)) map.removeLayer(pl); } catch (e) { } });
            });
        }
        try { if (t.polyline && map.hasLayer(t.polyline)) map.removeLayer(t.polyline); } catch (e) { }
        try { if (t.polylineOut && map.hasLayer(t.polylineOut)) map.removeLayer(t.polylineOut); } catch (e) { }

        // Cacher les marqueurs de zone des autres camions
        if (Array.isArray(t.zoneMarkers)) {
            t.zoneMarkers.forEach(function (mk) { try { if (map.hasLayer(mk)) map.removeLayer(mk); } catch (e) { } });
        }

        t._segmentsHidden = true;
    });

    // 2. S'assurer que le camion sélectionné existe
    var truck = trucksByCam[cam];
    if (!truck) {
        console.warn('[showOnlyThisTruck] Camion non trouvé:', cam);
        // Forcer le chargement (fetch utilisera le flag _segmentsHidden et ne réaffichera pas si caché)
        var idCh = lastCamDriverByCam[cam] || parseInt($("#idchauffeur").val()) || 0;
        fetchAndSetPolylineForTruck(cam, idCh).then(function () {
            // une fois chargé, re-appeler en s'assurant de propager le contexte ville
            showOnlyThisTruck(cam, currentSelectedVilleForTags);
        }).catch(function (err) {
            console.error('Erreur chargement polyline:', err);
        });
        return;
    }

    // 2b. Si le truck existe mais n'a pas de segments chargés, charger à la demande
    if (!truck.segPolylines || truck.segPolylines.length === 0) {
        var idCh2 = lastCamDriverByCam[cam] || parseInt($("#idchauffeur").val()) || 0;
        truck._segmentsHidden = false;
        fetchAndSetPolylineForTruck(cam, idCh2).then(function () {
            // Afficher les segments une fois chargés
            showTruckSegments(cam);
            centerOnTruck(cam);
        }).catch(function (err) {
            console.error('Erreur chargement polyline:', err);
        });
    }

    // stocker le contexte ville côté camion (utile si besoin)
    if (!truck._meta) truck._meta = {};
    truck._meta.selectedVille = currentSelectedVilleForTags || null;

    // 3. Afficher le camion sélectionné
    try { if (truck.marker && !map.hasLayer(truck.marker)) truck.marker.addTo(map); } catch (e) { }
    // Les polylines live (polyline/polylineOut) ne sont pas affichées.
    // Seules les segPolylines (road-snapped) chargées par fetchAndSetPolylineForTruck sont utilisées.

    if (Array.isArray(truck.segPolylines)) {
        truck.segPolylines.forEach(function (pl) { try { if (!map.hasLayer(pl)) pl.addTo(map); } catch (e) { } });
    }
    if (truck.segPolylinesByVille) {
        Object.keys(truck.segPolylinesByVille).forEach(function (v) {
            (truck.segPolylinesByVille[v] || []).forEach(function (pl) { try { if (!map.hasLayer(pl)) pl.addTo(map); } catch (e) { } });
        });
    }

    // Afficher les marqueurs de zone
    if (Array.isArray(truck.zoneMarkers)) {
        truck.zoneMarkers.forEach(function (mk) { try { if (!map.hasLayer(mk)) mk.addTo(map); } catch (e) { } });
    }

    truck._segmentsHidden = false;

    // 4. Centrer la carte sur ce camion
    centerOnTruck(cam);

    console.log('[showOnlyThisTruck] ✅ Camion ' + cam + ' affiché seul, contexte idville=' + (currentSelectedVilleForTags || '(none)'));
}

// Fonction pour centrer sur un camion spécifique
function centerOnTruck(cam) {
    var truck = trucksByCam[cam];
    if (!truck) return;

    // Priorité 1: Centrer sur les segments s'ils existent
    var allLayers = [];
    if (Array.isArray(truck.segPolylines)) {
        allLayers = allLayers.concat(truck.segPolylines);
    }
    if (truck.segPolylinesByVille) {
        Object.keys(truck.segPolylinesByVille).forEach(function (v) {
            allLayers = allLayers.concat(truck.segPolylinesByVille[v] || []);
        });
    }

    if (allLayers.length > 0) {
        try {
            var group = L.featureGroup(allLayers);
            map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 15 });
            return;
        } catch (e) {
            console.warn('[centerOnTruck] Erreur fitBounds:', e);
        }
    }

    // Priorité 2: Centrer sur le marker
    if (truck.marker) {
        try {
            map.setView(truck.marker.getLatLng(), 15);
        } catch (e) {
            console.warn('[centerOnTruck] Erreur setView:', e);
        }
    }
}

// Fonction pour ré-afficher tous les camions
function showAllTrucks() {
    _currentSelectedCam = null; // Aucun camion sélectionné
    console.log('[showAllTrucks] Affichage de tous les camions');

    Object.keys(trucksByCam).forEach(function (cam) {
        var t = trucksByCam[cam];
        if (!t) return;

        // Afficher markers
        try {
            if (t.marker && !map.hasLayer(t.marker)) {
                t.marker.addTo(map);
            }
        } catch (e) { }

        // Les polylines live (polyline/polylineOut) ne sont pas affichées.
        // Seules les segPolylines (road-snapped) sont utilisées.

        // Afficher segments
        if (Array.isArray(t.segPolylines)) {
            t.segPolylines.forEach(function (pl) {
                try {
                    if (!map.hasLayer(pl)) {
                        pl.addTo(map);
                    }
                } catch (e) { }
            });
        }

        if (t.segPolylinesByVille) {
            Object.keys(t.segPolylinesByVille).forEach(function (v) {
                (t.segPolylinesByVille[v] || []).forEach(function (pl) {
                    try {
                        if (!map.hasLayer(pl)) {
                            pl.addTo(map);
                        }
                    } catch (e) { }
                });
            });
        }

        // Afficher les marqueurs de zone
        if (Array.isArray(t.zoneMarkers)) {
            t.zoneMarkers.forEach(function (mk) {
                try {
                    if (!map.hasLayer(mk)) {
                        mk.addTo(map);
                    }
                } catch (e) { }
            });
        }

        t._segmentsHidden = false;
    });

    centerMapOnMarkers();
}
function buildTruckPopup(cam, meta) {
    var heure = meta && meta.hre ? meta.hre : 'N/A';
    var adresse = meta && meta.adresse ? meta.adresse : 'N/A';
    var chauffeur = meta && meta.chauffeur ? meta.chauffeur : 'N/A';
    var speed = meta && meta.speed ? meta.speed : null;
    var speedHtml = speed !== null ? '<div><span style="font-size:24px"><b>Vitesse:</b> ' + speed.toFixed(1) + ' km/h</span></div>' : '';
    return '<div style="min-width:330px;">'
        + '<div><span style="font-size:24px"><b>Camion:</b> ' + cam + '</span></div>'
        + '<div><span style="font-size:24px"><b>Chauffeur:</b> ' + chauffeur + '</span></div>'
        + '<div><span style="font-size:24px"><b>Heure:</b> ' + heure + '</span></div>'
        + '<div><span style="font-size:24px"><b>Adresse:</b> ' + adresse + '</span></div>'
        + speedHtml
        + '</div>';
}
function getNextColorDispo() {
    for (var i = 0; i < colors.length; i++) {
        if (colorInUse.indexOf(colors[i]) === -1) { colorInUse.push(colors[i]); return colors[i]; }
    }
    var alt = '#' + Math.floor(Math.random() * 16777215).toString(16);
    colorInUse.push(alt);
    return alt;
}
function getColorForCam(cam) {
    cam = normCamKey(cam);
    if (!cam) return getNextColorDispo();
    if (trucksByCam[cam] && trucksByCam[cam].color) return trucksByCam[cam].color;
    return getNextColorDispo();
}
function samePoint(a, b) {
    if (!a || !b) return false;
    var eps = 1e-8;
    return Math.abs(a.lat - b.lat) < eps && Math.abs(a.lng - b.lng) < eps;
}
function upsertTruck(cam, position) {
    cam = String(cam || '').trim();
    if (!cam) return;

    var inZone = (position && position.iz !== 0);
    var truck = trucksByCam[cam];

    if (!truck) {
        var color = getColorForCam(cam);
        var marker = createTruckMarker(position, cam);
        markers.push(marker);
        // Les polylines live ne sont plus créées ni affichées.
        // Seules les segPolylines (road-snapped) chargées à la demande sont utilisées.

        trucksByCam[cam] = {
            marker: marker,
            polyline: null,
            polylineOut: null,
            segPolylines: [],
            color: color,
            lastMeta: null,
            lastLatLng: { lat: position.lat, lng: position.lng },
            points: [],
            _segmentsHidden: false,
            zoneMarkers: []
        };

    } else {
        if (!samePoint(truck.lastLatLng, position)) {
            truck.lastLatLng = { lat: position.lat, lng: position.lng };
            truck.marker.setLatLng([position.lat, position.lng]);
            fetchAddressForLatLng(cam, position.lat, position.lng, function (meta) {
                trucksByCam[cam].lastMeta = meta;
                truck.marker.setPopupContent(buildTruckPopup(cam, meta));
            });
        } else {
            truck.marker.setLatLng([position.lat, position.lng]);
        }
    }
}
// IMPORTANT : Transformer fetchAndSetPolylineForTruck pour retourner une Promise
function fetchAndSetPolylineForTruck(cam, idchauffeur) {
    cam = normCamKey(cam);
    if (!cam) {
        console.warn('[fetchAndSetPolylineForTruck] cam vide, abort');
        return Promise.resolve();
    }

    var idCh = parseInt(idchauffeur, 10);
    if (!isFinite(idCh)) { idCh = 0; }

    var baseUrl = '/sanivan/getpolyline_all/' + $("#dtedeb").val()
        + '?idchauffeur=' + encodeURIComponent(idCh)
        + '&nocamion=' + encodeURIComponent(cam);

    console.log('[fetchAndSetPolylineForTruck] 🚀 Chargement pour cam=' + cam);

    return new Promise(function (resolveMain, rejectMain) {
        function loadOnce(url, isFallback) {
            $.ajax({ url: url, async: true, context: document.body })
                .done(function (data) {
                    console.log('[fetchAndSetPolylineForTruck] 📦 Données reçues:', {
                        cam: cam,
                        dataType: typeof data,
                        dataLength: data ? String(data).length : 0,
                        preview: data ? String(data).substring(0, 100) : 'null'
                    });

                    if (!data || data === '' || data === '|' || data === '||') {
                        console.warn('[fetchAndSetPolylineForTruck] ⚠️ Données vides/invalides');
                        if (!isFallback) {
                            return loadOnce(baseUrl + '&snap=0', true);
                        }
                        console.error('[fetchAndSetPolylineForTruck] ❌ Aucune donnée disponible');
                        resolveMain();
                        return;
                    }

                    try {
                        var parsed = parsePolylineAllResponse(data);
                        console.log('[fetchAndSetPolylineForTruck] 🔍 Parsing résultat:', {
                            segments: parsed.segments.length,
                            points: parsed.points.length
                        });

                        if (!parsed.segments || parsed.segments.length === 0) {
                            console.warn('[fetchAndSetPolylineForTruck] ⚠️ Aucun segment parsé');
                            if (!isFallback) {
                                return loadOnce(baseUrl + '&snap=0', true);
                            }
                            resolveMain();
                            return;
                        }

                        if (!trucksByCam[cam]) {
                            var lastSeg = parsed.segments[parsed.segments.length - 1];
                            if (!lastSeg || lastSeg.length === 0) {
                                console.error('[fetchAndSetPolylineForTruck] ❌ Impossible de créer truck');
                                resolveMain();
                                return;
                            }
                            var last = lastSeg[lastSeg.length - 1];
                            console.log('[fetchAndSetPolylineForTruck] 🚛 Création truck:', last);
                            upsertTruck(cam, { lat: last[0], lng: last[1], iz: 0 });
                        }

                        var truck = trucksByCam[cam];
                        if (!truck) {
                            console.error('[fetchAndSetPolylineForTruck] ❌ Truck introuvable');
                            resolveMain();
                            return;
                        }

                        // ✅ CORRECTION : Nettoyer anciens segments
                        if (Array.isArray(truck.segPolylines)) {
                            truck.segPolylines.forEach(function (pl) {
                                try {
                                    if (map.hasLayer(pl)) map.removeLayer(pl);
                                } catch (e) {
                                    console.warn('[fetchAndSetPolylineForTruck] Erreur removeLayer:', e);
                                }
                            });
                        }
                        truck.segPolylines = [];

                        var color = truck.color || getColorForCam(cam);
                        var weight = parseInt($("#edtEpaisseur").val() || 3);

                        // ✅ NOUVELLE LOGIQUE : Respecter _segmentsHidden
                        var shouldDisplay = !truck._segmentsHidden;
                        console.log('[fetchAndSetPolylineForTruck] 🎯 shouldDisplay=', shouldDisplay, 'pour cam=', cam);

                        var added = 0, skipped = 0;
                        parsed.segments.forEach(function (seg, idx) {
                            if (!seg || seg.length < 2) {
                                skipped++;
                                console.warn('[fetchAndSetPolylineForTruck] ⏭️ Segment ' + idx + ' ignoré (trop court)');
                                return;
                            }

                            try {
                                var segPolyline = L.polyline(seg, {
                                    color: color,
                                    weight: weight,
                                    opacity: 0.7
                                });

                                // ✅ N'ajouter à la carte QUE si shouldDisplay === true
                                if (shouldDisplay) {
                                    segPolyline.addTo(map);
                                }

                                segPolyline.on("click", function (e) {
                                    hideOtherTrucksSegments(cam);
                                    showTruckSegments(cam);
                                    onTruckPolylineClick(cam, e);
                                });

                                truck.segPolylines.push(segPolyline);
                                added++;
                            } catch (e) {
                                skipped++;
                                console.error('[fetchAndSetPolylineForTruck] ❌ Erreur segment ' + idx + ':', e);
                            }
                        });

                        console.log('[fetchAndSetPolylineForTruck] ✅ Résultat:', {
                            cam: cam,
                            ajoutés: added,
                            ignorés: skipped,
                            total: parsed.segments.length,
                            affichés: shouldDisplay ? added : 0
                        });

                        truck.points = parsed.points;

                        // Créer les marqueurs numérotés pour les segments IsInZone=1
                        try {
                            createZoneMarkers(cam, parsed.points);
                        } catch (e) {
                            console.warn('[fetchAndSetPolylineForTruck] Erreur création marqueurs zone:', e);
                        }

                        try {
                            // Si le toggle d'affichage des adresses est actif, (re)créer et afficher les cercles pour ce camion
                            if (window.addressCirclesGlobalToggleActive) {
                                try { hideAddressCirclesForTruck(cam); } catch (e) { /* ignore */ }
                                // forcer recréation : supprimer la cache locale
                                try { delete window.addressCirclesByTruck[cam]; } catch (e) { /* ignore */ }
                                try {
                                    createAddressCirclesForTruck(cam);
                                    showAddressCirclesForTruck(cam);
                                    console.log('[addressCircles] mises à jour pour', cam);
                                } catch (e) {
                                    console.warn('[addressCircles] erreur création/affichage pour', cam, e);
                                }
                            }
                        } catch (e) {
                            console.warn('[addressCircles] erreur générale', e);
                        }
                        // Mise à jour metadata
                        var lastMeta = parsed.points[parsed.points.length - 1];
                        if (lastMeta && (lastMeta.hre || lastMeta.adresse || lastMeta.chauffeur)) {
                            truck.lastMeta = lastMeta;
                            if (truck.marker) {
                                truck.marker.setPopupContent(buildTruckPopup(cam, lastMeta));
                            }
                        }

                        console.log('[fetchAndSetPolylineForTruck] 🎉 Succès pour cam=' + cam);
                        resolveMain();

                    } catch (err) {
                        console.error('[fetchAndSetPolylineForTruck] 💥 Erreur parsing:', err);
                        rejectMain(err);
                    }
                })
                .fail(function (err) {
                    console.error('[fetchAndSetPolylineForTruck] 🔥 Ajax fail:', err);
                    if (!isFallback) {
                        return loadOnce(baseUrl + '&snap=0', true);
                    }
                    rejectMain(err);
                });
        }

        loadOnce(baseUrl + '&snap=1', false);
    });
}
function showTruckSegments(cam) {
    var t = trucksByCam[cam];
    if (!t) return;

    // ré-ajoute les segments globaux
    if (Array.isArray(t.segPolylines)) {
        t.segPolylines.forEach(function (pl) { try { if (!map.hasLayer(pl)) pl.addTo(map); } catch (e) { } });
    }

    // ré-ajoute les segments par ville si présents
    if (t.segPolylinesByVille) {
        Object.keys(t.segPolylinesByVille).forEach(function (v) {
            (t.segPolylinesByVille[v] || []).forEach(function (pl) { try { if (!map.hasLayer(pl)) pl.addTo(map); } catch (e) { } });
        });
    }

    // Les polylines live (polyline/polylineOut) ne sont pas ré-affichées.
    // Seules les segPolylines (road-snapped) sont utilisées.

    t._segmentsHidden = false;
}

// Fonction pour créer les marqueurs numérotés sur les segments IsInZone=1
function createZoneMarkers(cam, points) {
    var truck = trucksByCam[cam];
    if (!truck) return;

    // Nettoyer les anciens marqueurs de zone
    if (Array.isArray(truck.zoneMarkers)) {
        truck.zoneMarkers.forEach(function (mk) {
            try { if (map.hasLayer(mk)) map.removeLayer(mk); } catch (e) { }
        });
    }
    truck.zoneMarkers = [];

    if (!points || points.length < 2) return;

    // Identifier le chauffeur pour le compteur de segments
    var chauffeurKey = truck.lastMeta && truck.lastMeta.chauffeur ? truck.lastMeta.chauffeur : cam;
    if (!window.zoneSegmentCounters) {
        window.zoneSegmentCounters = {};
    }
    if (!window.zoneSegmentCounters[chauffeurKey]) {
        window.zoneSegmentCounters[chauffeurKey] = 0;
    }

    var segmentNumber = 0;
    var inZone = false;
    var segmentStart = null;

    for (var i = 0; i < points.length; i++) {
        var p = points[i];
        var currentIsInZone = (p.isinzone === 1);

        if (!inZone && currentIsInZone) {
            // Début d'un segment IsInZone=1
            inZone = true;
            segmentStart = p;
        } else if (inZone && !currentIsInZone) {
            // Fin d'un segment IsInZone=1
            inZone = false;
            segmentNumber++;
            window.zoneSegmentCounters[chauffeurKey] = segmentNumber;

            // Créer le marqueur vert au début
            if (segmentStart) {
                var greenIcon = createNumberedCircleIcon(segmentNumber, 'green');
                var greenMarker = L.marker([segmentStart.lat, segmentStart.lng], { icon: greenIcon }).addTo(map);
                truck.zoneMarkers.push(greenMarker);
            }

            // Créer le marqueur rouge à la fin (point précédent)
            var prevPoint = points[i - 1];
            if (prevPoint) {
                var redIcon = createNumberedCircleIcon(segmentNumber, 'red');
                var redMarker = L.marker([prevPoint.lat, prevPoint.lng], { icon: redIcon }).addTo(map);
                truck.zoneMarkers.push(redMarker);
            }

            segmentStart = null;
        }
    }

    // Si le dernier point est encore dans la zone
    if (inZone && segmentStart) {
        segmentNumber++;
        window.zoneSegmentCounters[chauffeurKey] = segmentNumber;

        var greenIcon = createNumberedCircleIcon(segmentNumber, 'green');
        var greenMarker = L.marker([segmentStart.lat, segmentStart.lng], { icon: greenIcon }).addTo(map);
        truck.zoneMarkers.push(greenMarker);

        var lastPoint = points[points.length - 1];
        if (lastPoint) {
            var redIcon = createNumberedCircleIcon(segmentNumber, 'red');
            var redMarker = L.marker([lastPoint.lat, lastPoint.lng], { icon: redIcon }).addTo(map);
            truck.zoneMarkers.push(redMarker);
        }
    }
}

// Fonction pour créer une icône de cercle numéroté
function createNumberedCircleIcon(number, color) {
    var cssColor = color === 'green' ? '#28a745' : '#dc3545';
    var iconHtml = '<div style="'
        + 'width: 24px; height: 24px; border-radius: 50%;'
        + 'background-color: ' + cssColor + ';'
        + 'color: white; font-weight: bold; font-size: 14px;'
        + 'display: flex; align-items: center; justify-content: center;'
        + 'border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);'
        + '">' + number + '</div>';

    return L.divIcon({
        className: 'numbered-circle-marker',
        html: iconHtml,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
}

// Helper: parse la réponse brute de /getpolyline_all et renvoie { segments: [ [ [lat,lng], ... ], ... ], points: [ {lat,lng,hre,chauffeur,adresse}, ... ] }
function parsePolylineAllResponse(raw) {
    var outSegments = [];
    var outPoints = [];
    if (!raw) return { segments: outSegments, points: outPoints };
    var groups = String(raw).split('|');

    // Seuils de filtrage GPS (identiques à map_nav_zone.php)
    var MAX_DISTANCE_METERS = 200;
    var MAX_TIME_MINUTES = 2;
    var MAX_SPEED_KMH = 100;
    var MAX_ANGLE_DEGREES = 90;

    for (var g = 0; g < groups.length; g++) {
        var newPos = groups[g].split('_');
        var segmentLatLngs = [];
        var segmentPoints = [];

        for (var i = 0; i < newPos.length; i++) {
            var p = parsePointWithMeta(newPos[i]);
            if (!p) continue;

            segmentLatLngs.push([p.lat, p.lng]);
            segmentPoints.push({ lat: p.lat, lng: p.lng, hre: p.meta.hre || null, chauffeur: p.meta.chauffeur || null, adresse: p.meta.adresse || null, isinzone: p.meta.isinzone || 0 });
        }

        if (segmentLatLngs.length > 0) {
            // Appliquer le filtrage GPS sur le segment
            var filteredSegments = filterGPSSegment(segmentLatLngs, segmentPoints, MAX_DISTANCE_METERS, MAX_TIME_MINUTES, MAX_SPEED_KMH, MAX_ANGLE_DEGREES);
            outSegments = outSegments.concat(filteredSegments.segments);
            outPoints = outPoints.concat(filteredSegments.points);
        }
    }

    return { segments: outSegments, points: outPoints };
}

// Fonction de filtrage GPS (distance, temps, vitesse, angles)
function filterGPSSegment(coords, points, maxDistMeters, maxTimeMinutes, maxSpeedKmh, maxAngleDegrees) {
    var filteredSegments = [];
    var filteredPoints = [];

    if (coords.length < 2) {
        return { segments: filteredSegments, points: filteredPoints };
    }

    // Étape 1: Filtrage par distance, temps et vitesse
    var segments = [];
    var currentSegment = [coords[0]];
    var currentPoints = [points[0]];

    for (var i = 1; i < coords.length; i++) {
        var lastPoint = coords[i - 1];
        var currentPoint = coords[i];
        var lastMeta = points[i - 1];
        var currentMeta = points[i];

        var distance = calculateDistance(lastPoint[0], lastPoint[1], currentPoint[0], currentPoint[1]);
        var timeDiff = calculateTimeDiff(lastMeta.hre, currentMeta.hre);
        var speedKmh = 0;
        if (timeDiff > 0) {
            speedKmh = (distance / 1000) / (timeDiff / 60);
        }

        if (distance > maxDistMeters || timeDiff > maxTimeMinutes || speedKmh > maxSpeedKmh) {
            if (currentSegment.length >= 2) {
                segments.push({ coords: currentSegment.slice(), points: currentPoints.slice() });
            }
            currentSegment = [currentPoint];
            currentPoints = [currentMeta];
        } else {
            currentSegment.push(currentPoint);
            currentPoints.push(currentMeta);
        }
    }

    if (currentSegment.length >= 2) {
        segments.push({ coords: currentSegment.slice(), points: currentPoints.slice() });
    }

    // Étape 2: Filtrage par angles anormaux
    segments.forEach(function(seg) {
        if (seg.coords.length < 3) {
            filteredSegments.push(seg.coords);
            filteredPoints = filteredPoints.concat(seg.points);
            return;
        }

        var angleSegment = [seg.coords[0], seg.coords[1]];
        var anglePoints = [seg.points[0], seg.points[1]];

        for (var i = 2; i < seg.coords.length; i++) {
            var p1 = seg.coords[i - 2];
            var p2 = seg.coords[i - 1];
            var p3 = seg.coords[i];

            var angle = calculateAngle(p1, p2, p3);
            if (angle > maxAngleDegrees) {
                if (angleSegment.length >= 2) {
                    filteredSegments.push(angleSegment.slice());
                    filteredPoints = filteredPoints.concat(anglePoints);
                }
                angleSegment = [p2, p3];
                anglePoints = [seg.points[i - 1], seg.points[i]];
            } else {
                angleSegment.push(p3);
                anglePoints.push(seg.points[i]);
            }
        }

        if (angleSegment.length >= 2) {
            filteredSegments.push(angleSegment);
            filteredPoints = filteredPoints.concat(anglePoints);
        }
    });

    return { segments: filteredSegments, points: filteredPoints };
}

// Calcul de la distance en mètres entre deux points (formule Haversine)
function calculateDistance(lat1, lng1, lat2, lng2) {
    var R = 6371000; // Rayon de la Terre en mètres
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLng = (lng2 - lng1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Calcul de la différence de temps en minutes entre deux timestamps
function calculateTimeDiff(time1, time2) {
    if (!time1 || !time2) return 0;
    var t1 = new Date(time1).getTime();
    var t2 = new Date(time2).getTime();
    if (isNaN(t1) || isNaN(t2)) return 0;
    return Math.abs(t2 - t1) / (1000 * 60); // Convertir en minutes
}

// Calcul de l'angle en degrés entre trois points (p1-p2-p3)
function calculateAngle(p1, p2, p3) {
    var bearing1 = calculateBearing(p2[0], p2[1], p1[0], p1[1]);
    var bearing2 = calculateBearing(p2[0], p2[1], p3[0], p3[1]);
    var angle = Math.abs(bearing2 - bearing1);
    if (angle > 180) angle = 360 - angle;
    return angle;
}

// Calcul du bearing en degrés entre deux points
function calculateBearing(lat1, lng1, lat2, lng2) {
    var y = Math.sin(lng2 - lng1) * Math.cos(lat2);
    var x = Math.cos(lat1) * Math.sin(lat2) -
        Math.sin(lat1) * Math.cos(lat2) * Math.cos(lng2 - lng1);
    var brng = Math.atan2(y, x);
    return (brng * 180 / Math.PI + 360) % 360;
}
function parsePointWithMeta(raw) {
    raw = String(raw || '').trim();
    if (!raw) return null;

    // Trouve uniquement les deux premières virgules pour ne pas casser l'adresse qui peut contenir des virgules
    var idx1 = raw.indexOf(',');
    if (idx1 === -1) return null;
    var idx2 = raw.indexOf(',', idx1 + 1);

    var latStr, lngStr, rest;
    if (idx2 === -1) {
        // cas improbable : lat,lng sans meta
        latStr = raw.substring(0, idx1).trim();
        lngStr = raw.substring(idx1 + 1).trim();
        rest = '';
    } else {
        latStr = raw.substring(0, idx1).trim();
        lngStr = raw.substring(idx1 + 1, idx2).trim();
        rest = raw.substring(idx2 + 1).trim();
    }

    var lat = parseFloat(latStr);
    var lng = parseFloat(lngStr);
    if (isNaN(lat) || isNaN(lng)) return null;

    // Si le fournisseur envoie "lng,lat" (ex: -74.13,45.26) on détecte et on inverse
    if (lat < -90 || lat > 90) {
        var tmp = lat; lat = lng; lng = tmp;
    }

    var meta = {};
    if (rest.length > 0) {
        var mTime = rest.match(/timespan\s*:\s*([^,]+)(?:,|$)/i);
        var mChf = rest.match(/chauffeur\s*:\s*([^,]+)(?:,|$)/i);
        var mIz = rest.match(/isinzone\s*:\s*([^,]+)(?:,|$)/i);
        var mAdr = rest.match(/adresse\s*:\s*(.*)$/i);
        if (mTime) meta.hre = mTime[1].trim();
        if (mChf) meta.chauffeur = mChf[1].trim();
        if (mAdr) meta.adresse = mAdr[1].trim();
        if (mIz) meta.isinzone = parseInt(mIz[1].trim(), 10) || 0;
    }

    return { lat: lat, lng: lng, meta: meta };
}

function findNearestPointMeta(cam, latlng) {
    var truck = trucksByCam[cam];
    if (!truck || !truck.points || truck.points.length === 0) return null;
    var best = null, bestD = Number.POSITIVE_INFINITY;
    for (var i = 0; i < truck.points.length; i++) {
        var p = truck.points[i];
        var d = Math.hypot(p.lat - latlng.lat, p.lng - latlng.lng);
        if (d < bestD) { bestD = d; best = p; }
    }
    return best;
}
function calculateSpeedAtPoint(cam, point) {
    var truck = trucksByCam[cam];
    if (!truck || !truck.points || truck.points.length < 2) return null;
    
    // Trouver l'index du point dans le tableau
    var idx = -1;
    for (var i = 0; i < truck.points.length; i++) {
        if (truck.points[i] === point) {
            idx = i;
            break;
        }
    }
    if (idx === -1) return null;
    
    // Calculer la vitesse en utilisant le point précédent et suivant si disponibles
    var prevIdx = idx > 0 ? idx - 1 : idx;
    var nextIdx = idx < truck.points.length - 1 ? idx + 1 : idx;
    
    var prev = truck.points[prevIdx];
    var next = truck.points[nextIdx];
    
    // Si on a un point précédent et suivant, utiliser la moyenne
    if (prevIdx !== idx && nextIdx !== idx) {
        var dist1 = haversineDistance(prev.lat, prev.lng, point.lat, point.lng);
        var dist2 = haversineDistance(point.lat, point.lng, next.lat, next.lng);
        var totalDist = dist1 + dist2;
        
        var time1 = parseTime(prev.hre);
        var time2 = parseTime(point.hre);
        var time3 = parseTime(next.hre);
        
        if (time1 !== null && time2 !== null && time3 !== null) {
            var totalTimeSec = (time3 - time1) / 1000;
            if (totalTimeSec > 0) {
                var speedKmh = (totalDist / 1000) / (totalTimeSec / 3600);
                return speedKmh;
            }
        }
    }
    
    // Sinon, utiliser seulement le point précédent
    if (prevIdx !== idx) {
        var dist = haversineDistance(prev.lat, prev.lng, point.lat, point.lng);
        var time1 = parseTime(prev.hre);
        var time2 = parseTime(point.hre);
        
        if (time1 !== null && time2 !== null) {
            var timeDiffSec = (time2 - time1) / 1000;
            if (timeDiffSec > 0) {
                var speedKmh = (dist / 1000) / (timeDiffSec / 3600);
                return speedKmh;
            }
        }
    }
    
    // Sinon, utiliser seulement le point suivant
    if (nextIdx !== idx) {
        var dist = haversineDistance(point.lat, point.lng, next.lat, next.lng);
        var time1 = parseTime(point.hre);
        var time2 = parseTime(next.hre);
        
        if (time1 !== null && time2 !== null) {
            var timeDiffSec = (time2 - time1) / 1000;
            if (timeDiffSec > 0) {
                var speedKmh = (dist / 1000) / (timeDiffSec / 3600);
                return speedKmh;
            }
        }
    }
    
    return null;
}
function haversineDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000; // Rayon de la Terre en mètres
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}
function parseTime(timeStr) {
    if (!timeStr) return null;
    // Format attendu: "YYYY-MM-DD HH:MM:SS" ou "HH:MM:SS"
    var parts = timeStr.split(' ');
    var timePart = parts.length > 1 ? parts[1] : parts[0];
    var timeParts = timePart.split(':');
    if (timeParts.length >= 3) {
        var hours = parseInt(timeParts[0], 10);
        var minutes = parseInt(timeParts[1], 10);
        var seconds = parseInt(timeParts[2], 10);
        if (!isNaN(hours) && !isNaN(minutes) && !isNaN(seconds)) {
            return hours * 3600000 + minutes * 60000 + seconds * 1000;
        }
    }
    return null;
}
function showPopupAt(latlng, cam, meta) {
    var content = buildTruckPopup(cam, meta || trucksByCam[cam].lastMeta);
    L.popup().setLatLng(latlng).setContent(content).openOn(map);
}
function onTruckPolylineClick(cam, e) {
    var nearest = findNearestPointMeta(cam, e.latlng);
    if (nearest && (nearest.hre || nearest.adresse || nearest.chauffeur)) {
        var speed = calculateSpeedAtPoint(cam, nearest);
        showPopupAt(e.latlng, cam, {
            hre: nearest.hre || 'N/A',
            adresse: nearest.adresse || 'N/A',
            chauffeur: nearest.chauffeur || (trucksByCam[cam] && trucksByCam[cam].lastMeta ? trucksByCam[cam].lastMeta.chauffeur : 'N/A'),
            speed: speed
        });
    } else {
        fetchAddressForLatLng(cam, e.latlng.lat, e.latlng.lng, function (meta) {
            if (!meta.chauffeur && trucksByCam[cam] && trucksByCam[cam].lastMeta) {
                meta.chauffeur = trucksByCam[cam].lastMeta.chauffeur || 'N/A';
            }
            var speed = calculateSpeedAtPoint(cam, meta);
            meta.speed = speed;
            showPopupAt(e.latlng, cam, meta);
        });
    }
}
function fetchAddressForLatLng(cam, lat, lng, cb) {
    $.ajax({
        url: '/sanivan/getadresse', // aligne avec la route déclarée
        type: 'POST',
        dataType: 'json',
        data: { lat: lat, lng: lng, nocamion: cam, dteactive: $("#dtedeb").val() }
    }).done(function (data) {
        var meta = { hre: data && data.hre ? data.hre : 'N/A', adresse: data && data.adresse ? data.adresse : 'N/A' };
        var truck = trucksByCam[cam];
        if (truck) {
            var merged = Object.assign({}, truck.lastMeta || {}, meta);
            truck.lastMeta = merged;
            truck.points.push({ lat: lat, lng: lng, hre: merged.hre, adresse: merged.adresse, chauffeur: merged.chauffeur });
        }
        if (typeof cb === 'function') cb(truck ? truck.lastMeta : meta);
    }).fail(function (xhr) {
        
        if (typeof cb === 'function') cb({ hre: 'N/A', adresse: 'N/A', chauffeur: (trucksByCam[cam] && trucksByCam[cam].lastMeta && trucksByCam[cam].lastMeta.chauffeur) || 'N/A' });
    });
}
// Parse polyvalent: accepte objet JSON {cam/nocamion, lat/lng, iz, idchauffeur} ou ancienne string "cam_lat,lng[,iz]"
function parseLatLngItem(raw) {
    if (!raw) { return null; }

    // Objet déjà parsé
    if (typeof raw === 'object') {
        var camObj = normCamKey(raw.cam || raw.nocamion || raw.nocam || raw.idcamion || '');
        var latObj = parseFloat(raw.lat || raw.latitude);
        var lngObj = parseFloat(raw.lng || raw.lon || raw.longitude || raw.long);
        if (isNaN(latObj) || isNaN(lngObj)) { return null; }
        if (latObj < -90 || latObj > 90) { var t = latObj; latObj = lngObj; lngObj = t; }

        // prise en compte de toutes les variantes IsZone
        var izRaw = raw.iz || raw.IsZone || raw.isZone || raw.iszone || raw.in_zone || raw.inZone || raw.inzone;
        var izVal = parseInt(izRaw, 10);
        if (!isFinite(izVal)) { izVal = 0; }

        return {
            cam: camObj,
            lat: latObj,
            lng: lngObj,
            iz: izVal,
            idCh: parseInt(raw.idchauffeur || raw.id_chauffeur || raw.driver || 0, 10) || 0
        };
    }

    // Ancien format string "cam_lat,lng[,iz]"
    if (typeof raw === 'string') {
        var parts = raw.split('_');
        if (parts.length < 2) { return null; }
        var camStr = normCamKey(parts[0]);
        var arPos = (parts[1] || '').split(',');
        if (arPos.length < 2) { return null; }
        var v0 = parseFloat(arPos[0]);
        var v1 = parseFloat(arPos[1]);
        if (isNaN(v0) || isNaN(v1)) { return null; }
        var latStr = v0, lngStr = v1;
        if (latStr < -90 || latStr > 90) { var tmp2 = latStr; latStr = lngStr; lngStr = tmp2; }
        var izStr = (arPos.length >= 3) ? parseInt(arPos[2], 10) : 0;
        return { cam: camStr, lat: latStr, lng: lngStr, iz: isNaN(izStr) ? 0 : izStr, idCh: 0 };
    }

    return null;
}
// Précharge optionnel des polylines (stub : évite l'erreur si non défini ailleurs)
function preloadAllPolylinesIfPossible() {
    // Désactivé pour performance tablette: les polylines sont chargées à la demande
    polylinesPreloaded = true;
}
// Rafraîchit la polyline du camion actuellement sélectionné (non caché) seulement
var _currentSelectedCam = null;
function refreshSelectedTruckPolyline() {
    if (!_currentSelectedCam) return;
    var cam = normCamKey(_currentSelectedCam);
    var truck = trucksByCam[cam];
    if (!truck || truck._segmentsHidden) return;
    var idCh = lastCamDriverByCam[cam] || parseInt($("#idchauffeur").val()) || 0;
    fetchAndSetPolylineForTruck(cam, idCh);
}
// dans ShowCamionSan() — remplacement du parsing arPos => prise en compte possible ordre (lng,lat)
function ShowCamionSan() {
    var idchauffeur = parseInt($("#idchauffeur").val());
    $("#allchauffeur").val(idchauffeur == 0 ? 1 : 0);

    var urlName = '/sanivan/routecam/' + $("#dtedeb").val()
        + '?idchauffeur=' + idchauffeur
        + '&camchauf='
        + '&allchauffeur=' + $("#allchauffeur").val()
        + '&from=1';

    return $.ajax({ url: urlName, async: true, context: document.body })
        .done(function (data) {
            var objData;
            try {
                objData = (typeof data === 'string') ? JSON.parse(data) : data;
            } catch (e) {
                console.error('Erreur JSON routecam', e, data);
                return;
            }
            if (!objData) {
                console.error('Réponse routecam vide');
                return;
            }

            $("#divChauffeur").html(objData.chauffeursRte);
            preloadAllPolylinesIfPossible();

            var payload = objData.latlng || objData.latlngs || objData.positions;
            if (!payload) { return; }

            allCam.length = 0;

            // Cas tableau (objets ou strings)
            if (Array.isArray(payload)) {
                payload.forEach(function (item) {
                    var p = parseLatLngItem(item);
                    if (!p || !p.cam) { return; }
                    allCam.push(p.cam);
                    upsertTruck(p.cam, { lat: p.lat, lng: p.lng, iz: p.iz });

                    if (p.idCh > 0) { lastCamDriverByCam[p.cam] = p.idCh; }
                });
                // Charger la polyline UNIQUEMENT du camion actuellement sélectionné
                refreshSelectedTruckPolyline();
                return;
            }

            // Cas string concaténé historique "cam_lat,lng[,iz];cam_lat,lng[,iz]"
            if (typeof payload === 'string' && payload.includes('_')) {
                var arMarkers = payload.split(';');
                for (var i = 0; i < arMarkers.length; i++) {
                    var parsed = parseLatLngItem(arMarkers[i]);
                    if (!parsed || !parsed.cam) { continue; }

                    allCam.push(parsed.cam);
                    upsertTruck(parsed.cam, { lat: parsed.lat, lng: parsed.lng, iz: parsed.iz });

                    if (parsed.idCh > 0) { lastCamDriverByCam[parsed.cam] = parsed.idCh; }
                }
                // Charger la polyline UNIQUEMENT du camion actuellement sélectionné
                refreshSelectedTruckPolyline();
            }
        })
        .fail(function (err) {
            console.error('Erreur routecam:', err);
        });
}

async function boucleShowCamionSan() {
    while (continuePolling) {
        try { if (!IsBusy) { await ShowCamionSan(); } }
        catch (error) { console.error("Erreur lors de l'exécution de ShowCamionSan :", error); }
        var delai = parseInt($("#edtLapRefresh").val()) * 1000;
        await new Promise(resolve => setTimeout(resolve, delai));
    }
}
function showFindAdresse() {
    var selectOptions = '<option value="">Choisir un camion</option>';
    allCam.forEach(function (value) { selectOptions += '<option value="' + value + '">' + value + '</option>'; });
    $("#selCamModal").html(selectOptions);
    $("#edtDateModal").val($("#dtedeb").val());
    $("#selAdrModal").html('');
    $("#modalFindAdr").modal('show');
}
function refresh() {
    $("#allchauffeur").val(1);
    resetAllTrucks();
    var urlName = '/sanivan/routecam/' + $("#dtedeb").val() + '?&allchauffeur=1';
    document.location = urlName;
}
function getAdresseFromCam(value) {
    var urlName = '/sanivan/adressefromcam/' + value + '/' + $("#dtedeb").val();
    $.ajax({ async: false, type: "POST", url: urlName })
        .done(function (msg) { $("#selAdrModal").empty(); $("#selAdrModal").html(msg); });
}
function findAdresse() {
    var urlName = '/sanivan/adressetofind/' + $("#selCamModal").val() + '/' + $("#dtedeb").val()
        + '/' + encodeURIComponent($("#edtFind").val());
    $.ajax({ async: false, type: "POST", url: urlName })
        .done(function (msg) { $("#selAdrModal").html(msg); });
}
function createMarker(position, infoadresse) {
    var marker = L.marker([position.lat, position.lng]).addTo(map);
    marker.bindPopup(infoadresse);
    markers.push(marker);
}
function goToAdresse(value) {
    var urlName = '/sanivan/gotoadresse/' + value;
    $.ajax({ async: false, type: "POST", url: urlName })
        .done(function (msg) {
            IsBusy = true;
            var objData = JSON.parse(msg);
            var lat = parseFloat(objData.lat);
            var lng = parseFloat(objData.lng);
            var position = { lat: lat, lng: lng };
            var place = 'Date: ' + objData.hre + ', Adresse: ' + objData.adresse;
            $("#modalFindAdr").modal('hide');
            createMarker(position, place);
            map.setView([lat, lng], 18);
            setTimeout(() => { IsBusy = false; }, 500);
        });
}
function resetAllTrucks() {
    _currentSelectedCam = null;
    for (var cam in trucksByCam) {
        if (!trucksByCam.hasOwnProperty(cam)) continue;
        try { map.removeLayer(trucksByCam[cam].marker); } catch (e) { }
        try { map.removeLayer(trucksByCam[cam].polyline); } catch (e) { }
        try { map.removeLayer(trucksByCam[cam].polylineOut); } catch (e) { }
        try {
            if (Array.isArray(trucksByCam[cam].segPolylines)) {
                trucksByCam[cam].segPolylines.forEach(function (pl) { try { map.removeLayer(pl); } catch (e) { } });
            }
        } catch (e) { }
        try { if (trucksByCam[cam].bufferLayer) map.removeLayer(trucksByCam[cam].bufferLayer); } catch (e) { }
    }
    trucksByCam = {};
    markers.forEach(function (m) { try { map.removeLayer(m); } catch (e) { } });
    markers = [];
    polylines = {};
    colorInUse = [];
    polylinesPreloaded = false;
}
function centerMapOnMarkers() {
    if (markers.length === 0) return;
    var group = L.featureGroup(markers);
    map.fitBounds(group.getBounds(), { padding: [50, 50] });
}
function addPolylinesForVille(cam, idVille, segments) {
    var truck = trucksByCam[cam];
    if (!truck) {
        // créer structure minimale si absente
        var color = getColorForCam(cam);
        trucksByCam[cam] = truck = { marker: null, polyline: null, segPolylines: [], segPolylinesByVille: {}, color: color, lastMeta: null, lastLatLng: null, points: [], pointsByVille: {} };
    }
    truck.segPolylinesByVille = truck.segPolylinesByVille || {};
    // retirer anciens segments de cette ville s'il en existe
    if (Array.isArray(truck.segPolylinesByVille[idVille])) {
        truck.segPolylinesByVille[idVille].forEach(function (pl) { try { map.removeLayer(pl); } catch (e) { } });
    }
    truck.segPolylinesByVille[idVille] = [];
    var color = truck.color || getColorForCam(cam);
    segments.forEach(function (seg) {
        var segPolyline = L.polyline(seg, { color: color, weight: parseInt($("#edtEpaisseur").val()) }).addTo(map);
        (function (camRef) {
            segPolyline.on("click", function (e) { onTruckPolylineClick(camRef, e); });
        })(cam);
        truck.segPolylinesByVille[idVille].push(segPolyline);
    });
    // reconstruire liste agrégée segPolylines
    truck.segPolylines = [];
    Object.keys(truck.segPolylinesByVille || {}).forEach(function (k) {
        truck.segPolylines = truck.segPolylines.concat(truck.segPolylinesByVille[k]);
    });
}
function removeSegPolylinesNotIn(cam, keepVilles) {
    var truck = trucksByCam[cam];
    if (!truck || !truck.segPolylinesByVille) return;
    Object.keys(truck.segPolylinesByVille).forEach(function (v) {
        if (keepVilles.indexOf(v) === -1) {
            (truck.segPolylinesByVille[v] || []).forEach(function (pl) { try { map.removeLayer(pl); } catch (e) { } });
            delete truck.segPolylinesByVille[v];
            if (truck.pointsByVille) delete truck.pointsByVille[v];
        }
    });
    truck.segPolylines = [];
    Object.keys(truck.segPolylinesByVille || {}).forEach(function (k) {
        truck.segPolylines = truck.segPolylines.concat(truck.segPolylinesByVille[k]);
    });
    // reconstruire points agrégés
    truck.points = [];
    if (truck.pointsByVille) {
        Object.keys(truck.pointsByVille).forEach(function (k) { truck.points = truck.points.concat(truck.pointsByVille[k] || []); });
    }
}

// Remplacement de fetchPolylineSegments pour retourner la même structure via Promise
function fetchPolylineSegments(cam, idchauffeur, idVille) {
    cam = normCamKey(cam);
    return new Promise(function (resolve, reject) {
        var baseUrl = '/sanivan/getpolyline_all/' + $("#dtedeb").val()
            + '?idchauffeur=' + encodeURIComponent(idchauffeur)
            + '&nocamion=' + encodeURIComponent(cam);
        if (idVille !== undefined && idVille !== null && String(idVille).trim() !== '') {
            baseUrl += '&idville=' + encodeURIComponent(String(idVille));
        }

        function runOnce(url, isFallback) {
            $.ajax({ url: url, async: true, context: document.body })
                .done(function (data) {
                    try {
                        var parsed = parsePolylineAllResponse(data);
                        if ((!parsed.segments || parsed.segments.length === 0) && !isFallback) {
                            // retry without snap
                            return runOnce(baseUrl + '&snap=0', true);
                        }
                        resolve({ segments: parsed.segments, points: parsed.points });
                    } catch (e) { reject(e); }
                })
                .fail(function (err) { reject(err); });
        }

        // premier essai avec snap=1
        runOnce(baseUrl + '&snap=1', false);
    });
}
function showCam(idchauffeur, idvilleOrNocamion, maybeNocamion) {
    var nocamion, idville;
    
    alert(idvilleOrNocamion);
    if (typeof maybeNocamion === "undefined") {
        nocamion = idvilleOrNocamion;
        idville = idvilleOrNocamion || "";
    } else {
        idville = idvilleOrNocamion;
        nocamion = maybeNocamion;
    }

    nocamion = normCamKey(nocamion);

    // Si idchauffeur == 0, afficher tous les camions
    if (idchauffeur <= 0) {
        // reset éventuel du contexte ville
        currentSelectedVilleForTags = null;
        showAllTrucks();
        return;
    }

    // Sinon, afficher UNIQUEMENT ce camion et propager idville (si fourni)
    showOnlyThisTruck(nocamion, idville);
}

function hideOtherTrucksSegments(camToKeep) {
    camToKeep = normCamKey(camToKeep);
    console.log('[hideOtherTrucksSegments] Cacher tous sauf:', camToKeep);

    try {
        Object.keys(trucksByCam || {}).forEach(function (cam) {
            if (normCamKey(cam) === camToKeep) return;

            var t = trucksByCam[cam];  // ✅ Utiliser la clé telle quelle
            if (!t) return;

            // Retirer marker
            try { if (t.marker && map.hasLayer(t.marker)) map.removeLayer(t.marker); } catch (e) { }

            // Retirer segPolylines globaux
            if (Array.isArray(t.segPolylines)) {
                t.segPolylines.forEach(function (pl) {
                    try { if (map.hasLayer(pl)) map.removeLayer(pl); } catch (e) { }
                });
            }

            // Retirer segPolylinesByVille
            if (t.segPolylinesByVille) {
                Object.keys(t.segPolylinesByVille).forEach(function (v) {
                    (t.segPolylinesByVille[v] || []).forEach(function (pl) {
                        try { if (map.hasLayer(pl)) map.removeLayer(pl); } catch (e) { }
                    });
                });
            }

            // Retirer polylines « live »
            try { if (t.polyline && map.hasLayer(t.polyline)) map.removeLayer(t.polyline); } catch (e) { }
            try { if (t.polylineOut && map.hasLayer(t.polylineOut)) map.removeLayer(t.polylineOut); } catch (e) { }

            // Retirer marqueurs de zone
            if (Array.isArray(t.zoneMarkers)) {
                t.zoneMarkers.forEach(function (mk) {
                    try { if (map.hasLayer(mk)) map.removeLayer(mk); } catch (e) { }
                });
            }

            t._segmentsHidden = true;
        });
    } catch (e) {
        console.warn('hideOtherTrucksSegments error', e);
    }
}

document.addEventListener("DOMContentLoaded", initMap);

// ======= TAGS =======
var TAG_REASONS = []; var tagReasonsLoaded = false;
var TAG_CHAUFFEURS = []; var tagChauffeursLoaded = false;

var tagCluster = null;
var tagMarkersById = {};

async function loadTagReasons() {
    if (tagReasonsLoaded) return;
    try {
        const j = await qFetchJson(baseUrl('ville/maptag/reasons') + '?_ts=' + Date.now());
        if (j && j.ok && Array.isArray(j.data)) { TAG_REASONS = j.data.slice(); tagReasonsLoaded = true; }
    } catch (e) { console.warn('Erreur raisons', e); }
}

// Charger la liste des chauffeurs pour dropdown (fallback : reste mode texte)
async function loadTagChauffeurs() {
    if (tagChauffeursLoaded) return;
    try {
        const j = await qFetchJson(baseUrl('ville/maptag/chauffeurs') + '?_ts=' + Date.now());
        if (j && j.ok && Array.isArray(j.data)) {
            TAG_CHAUFFEURS = j.data.map(function (r) {
                // essayer plusieurs clés possibles
                var name = r.name || r.Nom || (r.Prenom ? (r.Prenom + ' ' + (r.Nom || '')) : '') || r.login || '';
                return { id: r.id || r.ID || r.idchauffeur || r.id_chauffeur || r.value || 0, name: name };
            });
            tagChauffeursLoaded = true;
        }
    } catch (e) {
        console.warn('Erreur chauffeurs', e);
    }
}

function statusColor(st) {
    switch ((st || '').toUpperCase()) {
        case 'DONE': return '#2e7d32';
        case 'ACK': return '#1976d2';
        case 'REJECTED': return '#c62828';
        default: return '#ef6c00';
    }
}
function statusLabel(st) {
    var s = (st || '').toUpperCase();
    return { DONE: 'Terminé', ACK: 'Accusé', REJECTED: 'Rejeté' }[s] || 'En attente';
}
function extractResponses(tag) {
    var list = []; function add(o) { if (!o) return; list.push({ text: o.text || o.message || o.comment || '', author: o.author || o.user || o.by || '', time: o.time || o.date || o.at || '', status: o.status || '' }); }
    try {
        if (Array.isArray(tag.responses)) tag.responses.forEach(add);
        else if (Array.isArray(tag.response_list)) tag.response_list.forEach(add);
        else if (Array.isArray(tag.comments)) tag.comments.forEach(add);
        else if (Array.isArray(tag.discussions)) tag.discussions.forEach(add);
        else if (tag.last_response) add(tag.last_response);
    } catch (e) { }
    return list;
}
function makeTagIcon(color) {
    return L.divIcon({
        className: 'tag-marker',
        html: '<div style="width:18px;height:18px;border-radius:50%;background:' + color + ';border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.35)"></div>',
        iconSize: [18, 18], iconAnchor: [9, 9]
    });
}

// Remplacer la fonction `tagPopupHtml` par ceci
function tagPopupHtml(tag) {
    var imgHtml = '';
    if (tag.image_path) {
        var imgUrl = (function (rel) { rel = String(rel || '').replace(/^(\.\/|\/)+/, ''); return 'https://robert-daoust.ca/' + rel; })(tag.image_path);
        imgHtml = '<div class="img-preview" style="margin-top:8px;border:1px solid #ccc;padding:4px;background:#fafafa;border-radius:4px;text-align:center;">'
            + '<img data-full="' + escapeHtml(imgUrl) + '" src="' + escapeHtml(imgUrl) + '" alt="img" style="max-width:180px;height:auto;border-radius:4px;cursor:pointer;" title="Cliquer pour agrandir" />'
            + '</div>';
    }

    var responses = extractResponses(tag);
    var discussionHtml = '<div style="margin-top:6px;"><div style="font-weight:600;margin-bottom:4px;">Discussions / réponses</div>'
        + (responses.length > 0
            ? responses.map(function (r) {
                var t = escapeHtml(r.text || ''); var a = escapeHtml(r.author || ''); var d = escapeHtml(r.time || ''); var s = escapeHtml(statusLabel(r.status || ''));
                return '<div style="border-left:3px solid #ddd;padding-left:8px;margin-bottom:6px;font-size:12px;">'
                    + (t ? '<div>' + t + '</div>' : '')
                    + '<div style="color:#777;">' + [a, d, s].filter(Boolean).join(' · ') + '</div>'
                    + '</div>';
            }).join('')
            : '<div style="font-size:12px;color:#777;">Aucune réponse.</div>'
        )
        + '</div>';

    var reasonsOptions = (tagReasonsLoaded ? TAG_REASONS.map(function (r) {
        var sel = (String(r || '') === String(tag.reason || '')) ? ' selected' : '';
        return '<option' + sel + ' value="' + escapeHtml(r) + '">' + escapeHtml(r) + '</option>';
    }).join('') : '<option>—</option>');
    var displayTitle = tag.title || tag.tag_code || '';
    var currentChText = tag.chauffeur || (tag.author_name || tag.created_by_name) || '';

    // valeur id_ville du tag (peut être undefined si serveur n'a pas renvoyé la colonne)
    var tagVille = (typeof tag.id_ville !== 'undefined' && tag.id_ville !== null) ? String(tag.id_ville) : '';

    return '<div class="popup-wrap" style="min-width:360px;max-width:560px;">'
        // en-tête statut / infos
        + '<div><b>Statut:</b> <span style="color:' + statusColor(tag.status) + '">' + escapeHtml(statusLabel(tag.status)) + '</span></div>'
        + '<div style="margin-bottom:6px;"><b>Titre:</b><br/><input type="text" name="title" value="' + escapeHtml(tag.title || tag.tag_code || '') + '" class="form-control form-control-sm"/></div>'
        + (tag.address_text ? '<div><b>Adresse:</b> ' + escapeHtml(tag.address_text) + '</div>' : '')
        + (tag.reason ? '<div><b>Raison:</b> ' + escapeHtml(tag.reason) + '</div>' : '')
        + (tag.description ? '<div><b>Description:</b> ' + escapeHtml(tag.description) + '</div>' : '')
        + imgHtml
        + discussionHtml

        // boutons d'action principaux (toujours visibles)
        + '<div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">'
        + ' <button data-action="OPEN_REPLY" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-outline-secondary btn-sm" style="flex:1;">Ajouter une réponse</button>'
        + ' <button data-action="EDIT" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-primary btn-sm" style="flex:1;">Modifier</button>'
        + ' <button data-action="ACK" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-info btn-sm" style="flex:1;">Accuser</button>'
        + ' <button data-action="DONE" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-success btn-sm" style="flex:1;">Terminer</button>'
        + ' <button data-action="REJECTED" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-danger btn-sm" style="flex:1;">Rejeter</button>'
        + '</div>'

        // zone d'assignation rapide du chauffeur (visible sans passer par "Modifier")
        + '<div style="margin-top:8px;border-top:1px solid #eee;padding-top:8px;">'
        + '<div style="font-weight:600;margin-bottom:6px;">Assigner / modifier le chauffeur</div>'
        // hidden field with tag's ville for reliable POST
        + '<input type="hidden" name="popup_id_ville" value="' + escapeHtml(tagVille) + '" />'
        + '<div><select name="popup_id_chauffeur" class="form-control form-control-sm" data-selected="' + escapeHtml(tag.id_chauffeur || '') + '" data-current-text="' + escapeHtml(currentChText || '') + '">'
        + '<option value="">— (aucun) —</option>'
        + '</select></div>'
        + '<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:6px;">'
        // also set data-ville on button (fallback)
        + '<button data-action="UPDATE_CHAUFFEUR" data-id="' + escapeHtml(String(tag.id)) + '" data-ville="' + escapeHtml(tagVille) + '" class="btn btn-warning btn-sm">Mettre à jour chauffeur</button>'
        + '</div>'
        + '<div style="margin-top:6px;font-size:12px;color:#666">Si le chauffeur n&#39;est pas dans la liste, utilisez la zone "Modifier" pour saisir un nom libre.</div>'
        + '</div>'

        // zone d'édition complète (conserve l'éditeur existant)
        + '<div class="edit-zone" data-id="' + escapeHtml(String(tag.id)) + '" style="display:none;margin-top:8px;border-top:1px solid #eee;padding-top:8px;">'
        + '<div style="font-weight:600;margin-bottom:6px;">Modifier le tag</div>'
        + '<div style="margin-bottom:6px;"><b>Adresse:</b><br/><input type="text" name="address_text" value="' + escapeHtml(tag.address_text || '') + '" class="form-control form-control-sm"/></div>'
        + '<div style="margin-bottom:6px;"><b>Raison:</b><br/><select name="reason" class="form-select form-control form-control-sm">' + reasonsOptions + '</select></div>'
        + '<div style="margin-bottom:6px;"><b>Chauffeur (si applicable):</b><br/>'
        + '<select name="id_chauffeur" class="form-control form-control-sm" data-chauffeur-text="' + escapeHtml(currentChText || '') + '" data-selected="' + escapeHtml(tag.id_chauffeur || '') + '">'
        + '<option value="">— (aucun) —</option>'
        + '</select>'
        + '<input type="text" name="chauffeur_text" class="form-control form-control-sm" style="margin-top:6px;" placeholder="Nom chauffeur (si non dans la liste)" value="' + escapeHtml(currentChText || '') + '"/>'
        + '<div style="margin-top:6px;font-size:12px;color:#666">Sélectionner un chauffeur ou saisir un nom libre.</div>'
        + '</div>'
        + '<div style="margin-bottom:6px;"><b>Description:</b><br/><textarea name="description" rows="3" class="form-control form-control-sm">' + escapeHtml(tag.description || '') + '</textarea></div>'
        + '<div style="display:flex;gap:6px;justify-content:flex-end;">'
        + '<button data-action="SAVE_EDIT" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-primary btn-sm">Enregistrer</button>'
        + '<button data-action="CANCEL_EDIT" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-outline-secondary btn-sm">Annuler</button>'
        + '</div>'
        + '</div>'

        // zone réponse (invariante)
        + '<div class="reply-zone" style="margin-top:10px;border-top:1px solid #eee;padding-top:8px;display:none;">'
        + '<div style="font-weight:600;margin-bottom:6px;">Ajouter une réponse</div>'
        + '<textarea name="reply_text" rows="3" class="form-control form-control-sm" placeholder="Votre réponse..."></textarea>'
        + '<div style="display:flex;gap:6px;justify-content:flex-end;margin-top:6px;">'
        + '<button data-action="POST_REPLY" data-id="' + escapeHtml(String(tag.id)) + '" class="btn btn-success btn-sm">Envoyer</button>'
        + '</div>'
        + '</div>'
        + '</div>';
}


// Remplacer la fonction `wirePopupButtons` par ceci
function wirePopupButtons(root) {
    // peupler selects chauffeur (pour popup rapide et pour zone d'édition)
    try {
        function populateSelect(sel) {
            if (sel.dataset._populated === '1') return;
            var opts = '<option value="">— (aucun) —</option>' + TAG_CHAUFFEURS.map(function (r) {
                return '<option value="' + escapeHtml(String(r.id)) + '">' + escapeHtml(String(r.name || r.id)) + '</option>';
            }).join('');
            sel.innerHTML = opts;

            // restaurer sélection si id existant ou tenter correspondance texte
            var selId = sel.getAttribute('data-selected') || '';
            var currentText = sel.getAttribute('data-current-text') || sel.getAttribute('data-chauffeur-text') || '';

            if (selId) {
                try { sel.value = selId; } catch (e) { }
                var txtInput = sel.closest('.edit-zone')?.querySelector('input[name="chauffeur_text"]');
                if (txtInput) txtInput.value = '';
            } else if (currentText) {
                var matched = TAG_CHAUFFEURS.find(function (c) { return String((c.name || '')).toLowerCase() === String(currentText || '').toLowerCase(); });
                if (matched) {
                    try { sel.value = matched.id; } catch (e) { }
                    var txtInput = sel.closest('.edit-zone')?.querySelector('input[name="chauffeur_text"]');
                    if (txtInput) txtInput.value = '';
                } else {
                    var txtInput = sel.closest('.edit-zone')?.querySelector('input[name="chauffeur_text"]');
                    if (txtInput) txtInput.value = currentText;
                }
            }
            sel.dataset._populated = '1';
        }

        if (tagChauffeursLoaded && TAG_CHAUFFEURS.length > 0) {
            root.querySelectorAll('select[name="id_chauffeur"], select[name="popup_id_chauffeur"]').forEach(populateSelect);
        } else {
            loadTagChauffeurs().then(function () {
                root.querySelectorAll('select[name="id_chauffeur"], select[name="popup_id_chauffeur"]').forEach(function (sel) { sel.dataset._populated = '0'; });
                try { wirePopupButtons(root); } catch (e) { }
            }).catch(function () { });
        }
    } catch (e) { console.warn('wirePopupButtons chauffeurs', e); }

    // Buttons actions (inclut UPDATE_CHAUFFEUR et conserve toutes les autres actions)
    root.querySelectorAll('button[data-action]').forEach(function (btn) {
        btn.onclick = async function () {
            var action = btn.getAttribute('data-action'), id = btn.getAttribute('data-id');
            if (!action) return;

            if (action === 'OPEN_REPLY') {
                var rz = root.querySelector('.reply-zone');
                if (rz) { rz.style.display = 'block'; var ta = rz.querySelector('textarea[name="reply_text"]'); if (ta) ta.focus(); }
                return;
            }
            if (!id) return;

            if (action === 'UPDATE_CHAUFFEUR') {
                var container = btn.closest('.popup-wrap') || btn.closest('.leaflet-popup-content');
                var sel = container && container.querySelector('select[name="popup_id_chauffeur"]');
                if (!sel) { alert('Sélecteur chauffeur introuvable.'); return; }
                var idCh = sel.value || '';
                var idVille = btn.getAttribute('data-ville') || (container && container.querySelector('input[name="popup_id_ville"]')?.value) || (document.getElementById('idville') ? document.getElementById('idville').value : '0');
                btn.disabled = true; var old = btn.textContent; btn.textContent = 'Envoi...';
                try {
                    await updateTagChauffeur(id, idCh, idVille);
                    alert('Chauffeur mis à jour.');
                    try { map.closePopup(); } catch (e) { }
                } catch (e) {
                    alert('Erreur mise à jour : ' + (e && e.message ? e.message : e));
                } finally { btn.disabled = false; btn.textContent = old; }
                return;
            }

            if (action === 'REJECTED') {
                var txt = prompt('Raison du rejet:', '') || '';
                await respondTag(id, 'REJECTED', txt, btn); try { map.closePopup(); } catch (e) { }; return;
            }
            if (action === 'ACK' || action === 'DONE') {
                await respondTag(id, action, '', btn); try { map.closePopup(); } catch (e) { }; return;
            }
            if (action === 'EDIT') {
                var zone = root.querySelector('.edit-zone[data-id="' + id + '"]'); if (zone) zone.style.display = 'block'; return;
            }
            if (action === 'CANCEL_EDIT') {
                var zone = root.querySelector('.edit-zone[data-id="' + id + '"]'); if (zone) zone.style.display = 'none'; return;
            }
            if (action === 'SAVE_EDIT') {
                var zone = root.querySelector('.edit-zone[data-id="' + id + '"]'); if (!zone) return;
                var title = zone.querySelector('input[name="title"]')?.value || '';
                var address = zone.querySelector('input[name="address_text"]')?.value || '';
                var reason = zone.querySelector('select[name="reason"]')?.value || '';
                var descr = zone.querySelector('textarea[name="description"]')?.value || '';
                var selCh = zone.querySelector('select[name="id_chauffeur"]');
                var idCh = selCh ? (selCh.value || null) : null;
                var txtCh = zone.querySelector('input[name="chauffeur_text"]')?.value || '';
                if ((!idCh || idCh === '') && txtCh) {
                    await updateTag(id, address, reason, descr, null, txtCh, title);
                } else {
                    await updateTag(id, address, reason, descr, idCh, '', title);
                }
                try { map.closePopup(); } catch (e) { }; return;
            }
            if (action === 'POST_REPLY') {
                var rz = root.querySelector('.reply-zone');
                var txt = rz?.querySelector('textarea[name="reply_text"]')?.value?.trim() || '';
                if (!txt) { alert('Veuillez saisir une réponse.'); return; }
                await respondTag(id, 'ACK', txt, btn);
                try { rz.querySelector('textarea[name="reply_text"]').value = ''; } catch (e) { }
                try { map.closePopup(); } catch (e) { }; return;
            }
        };
    });

    // images clickable
    root.querySelectorAll('.img-preview img[data-full]').forEach(function (img) {
        img.onclick = function () { showFullscreen(img.getAttribute('data-full')); };
    });
}

async function updateTagChauffeur(id, idChauffeur, idVille) {
    try {
        var fd = new FormData();
        fd.append('id', String(id));

        // utilise idVille passé (fallback sur #idville)
        var v = (typeof idVille !== 'undefined' && idVille !== null && String(idVille).trim() !== '') ? parseInt(idVille, 10) : (document.getElementById('idville') ? parseInt(document.getElementById('idville').value, 10) : 0);
        //alert(v);
        fd.append('id_ville', String(isNaN(v) ? 0 : v));
        if (typeof idChauffeur !== 'undefined' && idChauffeur && String(idChauffeur).trim() !== '') fd.append('id_chauffeur', String(idChauffeur));

        const r = await fetch(baseUrl('ville/maptag/update'), { method: 'POST', body: fd });
        let j = null;
        try { j = await r.json(); } catch (e) { j = null; }
        if (!r.ok || !j || !j.ok) throw new Error((j && j.error) ? j.error : 'Erreur update');
        await loadAllOpenMapTags();
        return true;
    } catch (e) {
        throw e;
    }
}



async function respondTag(id, status, responseText, triggerBtn) {
    var btnState = null;
    if (triggerBtn) {
        if (triggerBtn.dataset.submitting === '1') { return; }
        btnState = { btn: triggerBtn, label: triggerBtn.textContent };
        triggerBtn.dataset.submitting = '1';
        triggerBtn.disabled = true;
        triggerBtn.textContent = 'Envoi...';
    }
    try {
        var fd = new FormData();
        fd.append('id', String(id));
        fd.append('status', String(status));
        if (responseText) fd.append('response_text', responseText);

        const r = await fetch(baseUrl('ville/maptag/respond'), { method: 'POST', body: fd });
        const raw = await r.text();
        let j; try { j = JSON.parse(raw); } catch { j = null; }

        if (!r.ok) { alert('Erreur HTTP ' + r.status); return; }
        if (!j || !j.ok) { alert('Réponse non envoyée: ' + (j && j.error ? j.error : raw)); return; }

        alert('Réponse envoyée.');

        await loadAllOpenMapTags();
        try { map.closePopup(); } catch (e) { }
    } catch (e) {
        alert('Erreur: ' + (e && e.message ? e.message : e));
    } finally {
        if (btnState) {
            btnState.btn.disabled = false;
            btnState.btn.textContent = btnState.label;
            btnState.btn.dataset.submitting = '0';
        }
    }
}

// Mise à jour d'un tag (serveur: updateMapTag)
// Note: serveur attend probablement id_chauffeur numeric; si on n'a que le nom (chauffeur_text) on l'envoie dans 'chauffeur_text'
async function updateTag(id, address, reason, descr, idChauffeur, chauffeurText, title) {
    try {
        var fd = new FormData();
        fd.append('id', String(id));
        fd.append('id_ville', String(parseInt(document.getElementById('idville').value || '0', 10)));
        if (typeof address !== 'undefined') fd.append('address_text', address || '');
        if (typeof reason !== 'undefined') fd.append('reason', reason || '');
        if (typeof descr !== 'undefined') fd.append('description', descr || '');
        if (typeof idChauffeur !== 'undefined' && idChauffeur && String(idChauffeur).trim() !== '') fd.append('id_chauffeur', idChauffeur || '');
        if (typeof chauffeurText !== 'undefined' && chauffeurText) fd.append('chauffeur_text', chauffeurText || '');
        if (typeof title !== 'undefined') fd.append('title', title || '');
        const r = await fetch(baseUrl('ville/maptag/update'), { method: 'POST', body: fd });
        let j;
        try { j = await r.json(); } catch (e) { j = null; }
        if (!r.ok || !j || !j.ok) { alert('Erreur update: ' + (j && j.error ? j.error : r.status)); return; }
        alert('Tag mis à jour.');
        await loadAllOpenMapTags();
    } catch (e) {
        alert('Erreur: ' + (e && e.message ? e.message : e));
    }
}

// Cluster tags + map tags
function upsertTagMarker(tag) {
    var id = tag.id;
    var lat = parseFloat(tag.latitude), lng = parseFloat(tag.longitude);
    if (isNaN(lat) || isNaN(lng)) return null;
    if (tag.status && String(tag.status).toUpperCase() === 'DONE') {
        if (tagMarkersById[id]) { tagCluster.removeLayer(tagMarkersById[id]); delete tagMarkersById[id]; }
        return null;
    }
    if (tagMarkersById[id]) {
        var mk = tagMarkersById[id];
        mk.setIcon(makeTagIcon(statusColor(tag.status)));
        mk.setPopupContent(tagPopupHtml(tag));
        return mk;
    }
    var mk = L.marker([lat, lng], { icon: makeTagIcon(statusColor(tag.status)), title: 'Tag ' + (tag.tag_code || '') });
    mk.bindPopup(tagPopupHtml(tag), { maxWidth: 620, className: 'wide-popup' });
    mk.on('popupopen', function (e) { wirePopupButtons(e.popup.getElement()); });
    tagCluster.addLayer(mk);
    tagMarkersById[id] = mk;
    return mk;
}
function clearTagLayers() {
    if (tagCluster && tagCluster.clearLayers) tagCluster.clearLayers();
    tagMarkersById = {};
}

// Déduplication
function normalizeCoord(v) {
    var n = Number(v);
    if (!isFinite(n)) return null;
    return Number(n.toFixed(6));
}
function normalizeAddress(v) {
    if (v === undefined || v === null) return '';
    return String(v).trim().replace(/\s+/g, ' ').toLowerCase();
}
function makeTagKeyByCoordAddress(t) {
    var lat = normalizeCoord(t.latitude);
    var lng = normalizeCoord(t.longitude);
    if (lat === null || lng === null) return null;
    var addr = normalizeAddress(t.address_text);
    return lat + '|' + lng + '|' + addr;
}
function dedupeTags(rows) {
    var byId = {};
    var byCoordAddress = {};
    var out = [];
    for (var i = 0; i < rows.length; i++) {
        var t = rows[i];
        var id = Number(t.id || 0);
        if (id > 0) {
            if (byId[id]) continue;
            byId[id] = true;
        }
        var key = makeTagKeyByCoordAddress(t);
        if (key) {
            if (byCoordAddress[key]) continue;
            byCoordAddress[key] = true;
        }
        out.push(t);
    }
    return out;
}

async function loadMapTags() {
    try {
        const j = await qFetchJson(baseUrl('ville/maptag/list') + '?idville=' + encodeURIComponent($("#idville").val()) + '&open=1&_ts=' + Date.now());
        if (!j || !j.ok || !Array.isArray(j.data)) return;
        clearTagLayers();
        dedupeTags(j.data).forEach(function (t) { upsertTagMarker(t); });
    } catch (e) { console.warn('loadMapTags error', e); }
}
// REPLACE loadAllOpenMapTags with detection of newly validated tags
async function loadAllOpenMapTags() {
    try {
        const j = await qFetchJson(baseUrl('ville/maptag/list_all_open') + '?&_ts=' + Date.now());
        if (!j || !j.ok || !Array.isArray(j.data)) return;

        // dédoublonnage et insertion markers
        clearTagLayers();
        const rows = dedupeTags(j.data);
        rows.forEach(function (t) { upsertTagMarker(t); });

        // détecter nouveaux tags dont le statut est 'VALIDER'
        var newlyValidated = [];
        rows.forEach(function (t) {
            try {
                var id = String(t.id || '');
                var status = String(t.status || '').toUpperCase();
                var prev = tagStatusCache[id] || null;
                // stocker statut courant
                tagStatusCache[id] = status;
                // nouveau passage à VALIDER
                if (status === 'VALIDER' && prev !== 'VALIDER') {
                    newlyValidated.push(t);
                }
            } catch (e) { }
        });

        if (newlyValidated.length > 0) {
            // affiche notification et propose de localiser
            showValidatedNotification(newlyValidated);
        }
    } catch (e) {
        console.warn('loadAllOpenMapTags error', e);
    }
}

function initTagsAfterMap() {
    tagCluster = (L.markerClusterGroup
        ? L.markerClusterGroup({
            maxClusterRadius: 60,
            disableClusteringAtZoom: 18,
            spiderfyOnEveryZoom: false,
            showCoverageOnHover: false
        }).addTo(map)
        : L.featureGroup().addTo(map)
    );
    map.on('popupopen', function (e) { try { wirePopupButtons(e.popup.getElement()); } catch (err) { } });

    // Charger raisons puis chauffeurs puis tags
    loadTagReasons().then(loadTagChauffeurs).then(loadAllOpenMapTags).catch(function () { });
    var btn = document.getElementById('btnLoadTags');
    if (btn) { btn.addEventListener('click', function () { loadAllOpenMapTags(); }); }
    var btnAll = document.getElementById('btnLoadAllOpenTags');
    if (btnAll) { btnAll.addEventListener('click', function () { loadAllOpenMapTags(); }); }
}

// Overlay image
(function setupOverlay() {
    var overlay = document.getElementById('imgOverlay');
    if (!overlay) return;
    var img = overlay.querySelector('img');
    overlay.querySelector('.closeBtn').onclick = function () { overlay.style.display = 'none'; img.src = ''; };
    overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.style.display = 'none'; img.src = ''; } });
    window.showFullscreen = function (url) { if (!url) return; img.src = url; overlay.style.display = 'flex'; };
})();

// Création de tag
async function geocodeAddress(q) {
    const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q) + '&countrycodes=ca';
    const r = await fetch(url, { headers: { 'Accept': 'application/json', 'User-Agent': 'RDF-Map/1.0' } });
    if (!r.ok) throw new Error('Geocoding HTTP ' + r.status);
    const j = await r.json();
    if (!Array.isArray(j) || j.length === 0) throw new Error('Adresse introuvable');
    return { lat: parseFloat(j[0].lat), lng: parseFloat(j[0].lon), name: j[0].display_name || q };
}

document.addEventListener('DOMContentLoaded', function () {
    const btnCreateTag = document.getElementById('btnCreateTag');
    if (btnCreateTag) {
        btnCreateTag.addEventListener('click', async function () {
            var q = prompt('Adresse (no civique, rue, ville) :', '');
            if (!q) return;
            openCreateTagPopup(q);
        });
    }
});
async function openCreateTagPopup(addressQuery, presetLat, presetLng) {
    try {
        var hasPreset = typeof presetLat === 'number' && isFinite(presetLat) && typeof presetLng === 'number' && isFinite(presetLng);
        var pos;
        if (hasPreset) {
            pos = { lat: Number(presetLat), lng: Number(presetLng) };
        } else {
            pos = await geocodeAddress(addressQuery);
        }
        await loadTagReasons();
        await loadTagChauffeurs().catch(() => { });

        var optionsHtml = TAG_REASONS.length ? TAG_REASONS.map(r => '<option>' + escapeHtml(r) + '</option>').join('') : '<option>—</option>';
        // build chauffeur select if available
        var chauffeurSelectHtml = '<input id="tag_chauffeur" type="text" class="form-control" placeholder="Nom chauffeurs..."/>';
        if (tagChauffeursLoaded && TAG_CHAUFFEURS.length > 0) {
            chauffeurSelectHtml = '<select id="tag_chauffeur_select" class="form-control"><option value="">— (aucun) —</option>'
                + TAG_CHAUFFEURS.map(function (c) { return '<option value="' + escapeHtml(String(c.id)) + '">' + escapeHtml(String(c.name)) + '</option>'; }).join('')
                + '</select>'
                + '<div style="margin-top:6px;font-size:12px;color:#666">Si absent, utiliser le champ libre ci‑dessous.</div>'
                + '<input id="tag_chauffeur_free" type="text" class="form-control" placeholder="Nom libre (optionnel)" style="margin-top:6px" />';
        } else {
            chauffeurSelectHtml = '<input id="tag_chauffeur" type="text" class="form-control" placeholder="Nom chauffeurs..."/>';
        }

        var popupVille = currentSelectedVilleForTags || (typeof window.idVille !== 'undefined' && window.idVille) || (document.getElementById('idville') ? document.getElementById('idville').value : '0');
        var html =
            '<div style="min-width:320px;max-width:600px;">'
            + '<div style="font-weight:600;margin-bottom:6px;">Créer un tag</div>'
            + '<div class="mb-2"><b>Titre:</b><br/><input id="tag_title" type="text" class="form-control" value="" /></div>'
            + '<input type="hidden" id="popup_id_ville" value="'+escapeHtml(String(popupVille))+'" />'
            + '<div class="mb-2"><b>Adresse:</b><br/><input id="tag_addr" type="text" class="form-control" value="' + escapeHtml(addressQuery || '') + '" /></div>'
            + '<div class="mb-2"><b>Raison:</b><br/><select id="tag_reason" class="form-control">' + optionsHtml + '</select></div>'
            + '<div class="mb-2"><b>Chauffeur (optionnel):</b><br/>' + chauffeurSelectHtml + '</div>'
            + '<div class="mb-2"><b>Description:</b><br/><textarea id="tag_descr" class="form-control"></textarea></div>'
            + '<div class="mb-2"><b>Image (optionnelle):</b><br/><input id="tag_image" type="file" accept="image/*" class="form-control" /></div>'
            + '<div style="display:flex;gap:8px;justify-content:flex-end;"><button id="btnSendTag" class="btn btn-primary btn-sm">Envoyer</button></div>'
            + '</div>';
        var popup = L.popup({ maxWidth: 640, className: 'wide-popup' }).setLatLng([pos.lat, pos.lng]).setContent(html).openOn(map);

        setTimeout(function () {
            var btn = document.getElementById('btnSendTag');
            btn.onclick = async function () {
                if (btn.dataset.submitting === '1') return;
                btn.dataset.submitting = '1';
                var originalLabel = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Envoi...';
                try {
                    var fd = new FormData();
                    var idVille = parseInt(currentSelectedVilleForTags || (document.getElementById('idville') ? document.getElementById('idville').value : '') || '0', 10);
                    // Si idVille invalide côté client, demander au serveur la valeur de session
                    if (!isFinite(idVille) || idVille <= 0) {
                        try {
                            const srv = await qFetchJson(baseUrl('ville/current_idville') + '?_ts=' + Date.now());
                            if (srv && srv.ok && srv.idville) {
                                idVille = parseInt(srv.idville, 10) || 0;
                            }
                        } catch (e) {
                            console.warn('Impossible de récupérer idville depuis le serveur', e);
                        }
                    }
                    fd.append('id_ville', String(idVille));
                    fd.append('lat', String(Number(pos.lat)));
                    fd.append('lng', String(Number(pos.lng)));
                    fd.append('address_text', document.getElementById('tag_addr').value || '');
                    fd.append('reason', document.getElementById('tag_reason').value || '');
                    fd.append('description', document.getElementById('tag_descr').value || '');
                    // titre envoyé dans 'title'
                    var title = (document.getElementById('tag_title') && document.getElementById('tag_title').value) ? document.getElementById('tag_title').value : '';
                    if (title) fd.append('title', title);
                    // chauffeur: prefer select id if present, else free text
                    var selId = document.getElementById('tag_chauffeur_select');
                    var free = document.getElementById('tag_chauffeur_free') || document.getElementById('tag_chauffeur');
                    if (selId && selId.value) {
                        fd.append('id_chauffeur', String(selId.value));
                    } else if (free && free.value) {
                        fd.append('chauffeur_text', free.value);
                    }
                    var fileInput = document.getElementById('tag_image');
                    if (fileInput && fileInput.files && fileInput.files.length > 0) { fd.append('image', fileInput.files[0]); }

                    // Debug: show alert with the payload that will be sent to the server
                    try {
                        var fileInputDbg = fileInput && fileInput.files && fileInput.files.length > 0 ? fileInput.files[0].name : '';
                        var selVal = (selId && selId.value) ? selId.value : (free && free.value ? free.value : '');
                        var dbg = {
                            id_ville: String(idVille),
                            lat: String(Number(pos.lat)),
                            lng: String(Number(pos.lng)),
                            address_text: document.getElementById('tag_addr').value || '',
                            reason: document.getElementById('tag_reason').value || '',
                            description: document.getElementById('tag_descr').value || '',
                            title: title || '',
                            id_chauffeur: selVal || '',
                            image: fileInputDbg
                        };
                        alert('Données envoyées au serveur (debug):\n' + JSON.stringify(dbg, null, 2));
                    } catch (e) {
                        // ignore alert failure
                    }

                    const r = await fetch(baseUrl('ville/maptag/create'), { method: 'POST', body: fd });
                    const j = await r.json();
                    if (!j || !j.ok) {
                        alert('Erreur: ' + (j && j.error ? j.error : 'Create failed'));
                        return;
                    }
                    alert('Tag créé avec succès.');
                    map.closePopup(popup);
                    await loadAllOpenMapTags();
                } catch (err) {
                    alert('Erreur envoi: ' + (err && err.message ? err.message : 'Erreur'));
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalLabel;
                    btn.dataset.submitting = '0';
                }
            };
        }, 50);
    } catch (err) {
        alert('Erreur géocodage: ' + (err && err.message ? err.message : 'Adresse introuvable'));
    }
}
const btnCreateTag = document.getElementById('btnCreateTag');
if (btnCreateTag) {
    btnCreateTag.addEventListener('click', async function () {
        var q = prompt('Adresse (no civique, rue, ville) :', '');
        if (!q) return;
        openCreateTagPopup(q);
    });
}
// --- Cercles d'adresses liés aux polylines (ajouté) ---
window.addressCirclesByTruck = window.addressCirclesByTruck || {};
window.addressCirclesGlobalToggleActive = window.addressCirclesGlobalToggleActive || false;

/**
 * Crée (en mémoire) les circleMarkers pour un camion à partir de trucksByCam[cam].points
 * N'ajoute pas automatiquement à la carte.
 */
function createAddressCirclesForTruck(cam) {
    cam = normCamKey(cam);
    if (!cam) return;
    if (window.addressCirclesByTruck[cam]) return; // déjà créés

    var truck = trucksByCam[cam];
    window.addressCirclesByTruck[cam] = [];

    if (!truck || !Array.isArray(truck.points) || truck.points.length === 0) return;

    truck.points.forEach(function (p) {
        if (!p) return;
        var lat = Number(p.lat), lng = Number(p.lng);
        if (!isFinite(lat) || !isFinite(lng)) return;
        var addr = (p.adresse || p.address || '').trim();
        if (!addr) return; // ne créer que si adresse présente

        var circle = L.circleMarker([lat, lng], {
            radius: 6,
            color: '#ff5722',
            weight: 1,
            fillColor: '#ffccbc',
            fillOpacity: 0.95,
            pane: 'markerPane'
        });

        // Calculer la vitesse pour ce point
        var speed = calculateSpeedAtPoint(cam, p);
        var speedText = speed !== null ? ' (' + speed.toFixed(0) + ' km/h)' : '';

        // clic direct sur le cercle -> afficher le popup du point avec le format de map_nav_zone.php
        circle.on('click', function (e) {
            try {
                var meta = { hre: p.hre || 'N/A', adresse: addr, chauffeur: p.chauffeur || (truck.lastMeta && truck.lastMeta.chauffeur) || 'N/A', speed: speed };
                var content = '<div style="min-width:260px"><b>Camion: </b>' + escapeHtml(cam) + '<br/>';
                content += '<b>Chauffeur: </b>' + escapeHtml(meta.chauffeur) + '<br/>';
                content += '<b>Heure: </b>' + escapeHtml(meta.hre) + '<br/><b>Adresse: </b>' + escapeHtml(meta.adresse);
                if (speed !== null) {
                    content += '<br/><b>Vitesse: </b>' + speed.toFixed(1) + ' km/h';
                }
                content += '</div>';
                L.popup({ maxWidth: 360 }).setLatLng(e.latlng).setContent(content).openOn(map);
            } catch (err) { console.warn('show popup circle', err); }
        });

        circle.bindTooltip(addr + speedText, { direction: 'top', offset: [0, -8], className: 'address-tooltip' });
        window.addressCirclesByTruck[cam].push(circle);
    });
}

/**
 * Ajoute les cercles d'adresses d'un camion sur la carte
 */
function showAddressCirclesForTruck(cam) {
    cam = normCamKey(cam);
    createAddressCirclesForTruck(cam);
    var arr = window.addressCirclesByTruck[cam] || [];
    arr.forEach(function (c) {
        try { if (!map.hasLayer(c)) c.addTo(map); } catch (e) { /* ignore */ }
    });
}

/**
 * Retire les cercles d'adresses d'un camion
 */
function hideAddressCirclesForTruck(cam) {
    cam = normCamKey(cam);
    var arr = window.addressCirclesByTruck[cam] || [];
    arr.forEach(function (c) {
        try { if (map.hasLayer(c)) map.removeLayer(c); } catch (e) { }
    });
}

/**
 * Retire tous les cercles créés
 */
function hideAllAddressCircles() {
    Object.keys(window.addressCirclesByTruck || {}).forEach(function (c) { hideAddressCirclesForTruck(c); });
}

/**
 * Basculer affichage via bouton
 * - si activé, affiche les cercles du camion actuellement affiché (celui dont _segmentsHidden === false)
 * - sinon cache tous les cercles
 */
$(document).ready(function () {
    $('#btnToggleAddressCircles').off('click').on('click', function () {
        window.addressCirclesGlobalToggleActive = !window.addressCirclesGlobalToggleActive;
        $(this).text(window.addressCirclesGlobalToggleActive ? 'Masquer adresses' : 'Afficher adresses');

        if (window.addressCirclesGlobalToggleActive) {
            // trouver un camion "visible" (celui dont _segmentsHidden === false)
            var visibleCam = null;
            Object.keys(trucksByCam || {}).forEach(function (k) {
                try { if (trucksByCam[k] && trucksByCam[k]._segmentsHidden === false) visibleCam = k; } catch (e) { }
            });
            if (visibleCam) showAddressCirclesForTruck(visibleCam);
        } else {
            hideAllAddressCircles();
        }
    });

    // Wrapper non intrusif : si showOnlyThisTruck est défini, on l'enrobe pour mettre à jour les cercles automatiquement
    if (typeof window.showOnlyThisTruck === 'function') {
        var __orig_showOnlyThisTruck = window.showOnlyThisTruck;
        window.showOnlyThisTruck = function (cam, idville) {
            try { __orig_showOnlyThisTruck(cam, idville); } catch (e) { console.warn('showOnlyThisTruck original failed', e); }
            // délai court pour laisser Leaflet ajouter les couches
            setTimeout(function () {
                if (window.addressCirclesGlobalToggleActive) {
                    showAddressCirclesForTruck(cam);
                } else {
                    hideAllAddressCircles();
                }
            }, 50);
        };
    }
});