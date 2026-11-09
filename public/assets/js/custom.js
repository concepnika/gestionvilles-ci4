const geoOptions = {
    enableHighAccuracy: true, // Utilise le GPS pour une grande précision
    timeout: 5000,            // Abandonne la recherche après 5 secondes sans réponse
    maximumAge: 0             // N'accepte aucune position mise en cache (toujours en temps réel)
};

const watchID = navigator.geolocation.watchPosition(
    (position) => {
        const { latitude, longitude, heading, speed } = position.coords;
        console.log(`Latitude: ${latitude}, Longitude: ${longitude}`);
        // Vous pouvez également exploiter heading et speed si disponibles
        // Par exemple, mettre à jour l’orientation d'un marqueur sur la carte.
    },
    (error) => {
        console.error(`Erreur (${error.code}): ${error.message}`);
    },
    geoOptions
);



/////////--------------------------------------------------------------////////////////////
// Pour arrêter le suivi, utilisez
// navigator.geolocation.clearWatch(watchID);

let watchID;

// Options pour une haute précision
const geoOptionsAccurate = {
    enableHighAccuracy: true,
    timeout: 5000,      // 5 secondes maximum
    maximumAge: 0       // Aucune réutilisation de la position mise en cache
};

// Options plus conservatrices pour économiser la batterie
const geoOptionsConservative = {
    enableHighAccuracy: false,
    timeout: 10000,     // On se donne un peu plus de temps
    maximumAge: 20000   // Position mise en cache jusqu'à 1 minute
};

function startTracking(options) {
    // Si un suivi est déjà en cours, l'arrêter avant de le redémarrer
    if (watchID !== undefined) {
        navigator.geolocation.clearWatch(watchID);
    }
    watchID = navigator.geolocation.watchPosition(
        handlePosition,
        handleError,
        options
    );
}

function handlePosition(position) {
    const { latitude, longitude, speed } = position.coords;
    //console.log(`Position mise à jour: lat=${latitude}, lng=${longitude}, speed=${speed}`);

    // Exemple d'adaptation: si la vitesse est supérieure à 1 m/s, on suppose que le véhicule est en mouvement
    if (speed !== null && speed > 5) {
        // Si besoin de précision, redémarrer le suivi avec les options haute précision
        startTracking(geoOptionsAccurate);
    } else {
        // Si l'utilisateur est presque immobile, on passe en mode économie d'énergie
        startTracking(geoOptionsConservative);
    }

    // Ici, vous pouvez également mettre à jour une carte ou d'autres aspects de l'interface utilisateur
}

function handleError(error) {
    console.error(`Erreur (${error.code}): ${error.message}`);
}

// Lancer le suivi initial
startTracking(geoOptionsAccurate);


/////////--------------------------------------------------------------////////////////////
let lastUpdateTime = 0;
const updateInterval = 2000; // En millisecondes

function handlePosition(position) {
    const now = Date.now();
    if (now - lastUpdateTime < updateInterval) {
        // Ignorer cette mise à jour trop rapide
        return;
    }
    lastUpdateTime = now;

    // Continuez avec l'adaptation des options et la mise à jour de l'interface...
    const { latitude, longitude, speed } = position.coords;
    console.log(`Position mise à jour: lat=${latitude}, lng=${longitude}, speed=${speed}`);

    // Adaptation dynamique basée sur la vitesse
    if (speed !== null && speed > 1) {
        startTracking(geoOptionsAccurate);
    } else {
        startTracking(geoOptionsConservative);
    }
}