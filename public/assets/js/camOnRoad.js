let truckImg = document.getElementById('ImgTruckSani').src;
let map;
let marker;
const options = {
    timeout: 8000,
    enableHighAccuracy: true,
    maximumAge: 1000
};
// Définition d'une icône de base. Remplacez "truck.png" par le chemin vers votre image de véhicule.
const truckIcon = {
    url: truckImg,
    // On définit ici les dimensions de base de l'image
    scaledSize: new google.maps.Size(50, 50),
    anchor: new google.maps.Point(25, 25)
};

        // Initialisation de la carte au centre d’une position par défaut
function initMap() {
    haightAshbury = { lat: 45.683697424829006, lng: -73.89782882295549 };
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
        center: initialPos,
        // Vous pouvez contrôler le zoom ici
    });
    marker = new google.maps.Marker({
        position: initialPos,
        map: map,
        icon: truckIcon,
    });

    // Simuler la mise à jour du véhicule
    simulateVehicleMovement();
}

        // Mise à jour de la position, orientation (cap) et zoom de la carte
function updateVehicle(position, heading, zoomLevel = 15) {
    // Mise à jour de la position du marqueur
    marker.setPosition(position);

    // Recentrer la carte sur le nouveau véhicule et ajuster le zoom
    map.setCenter(position);
    map.setZoom(zoomLevel);

    // Pour changer l’orientation, nous utilisons la création d’un nouvel icône à partir d’un canevas.
    marker.setIcon(createRotatedIcon(truckIcon.url, heading, 50, 50));
}

        // Création d'une icône tournée dynamiquement grâce à un canvas
        function createRotatedIcon(url, heading, width, height) {
const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const context = canvas.getContext('2d');

        const image = new Image();
        image.src = url;
        // Note importante : comme le chargement de l'image est asynchrone, il peut être utile de précharger votre image
        // ou d'utiliser une bibliothèque pour gérer la rotation des marqueurs de manière plus fluide.
        if (!image.complete) {
    // Si l'image n'est pas encore chargée, renvoie l'icône par défaut
    return truckIcon;
}

        // Effacer le canvas
        context.clearRect(0, 0, width, height);
        // Se positionner au centre du canvas
        context.translate(width / 2, height / 2);
        // Convertir le cap en radians et appliquer la rotation
        context.rotate((heading * Math.PI) / 180);
        // Dessiner l'image en la recentrant
        context.drawImage(image, -width / 2, -height / 2, width, height);
        // Réinitialiser la transformation (optionnel)
        context.setTransform(1, 0, 0, 1, 0, 0);

        return {
            url: canvas.toDataURL(),
        scaledSize: new google.maps.Size(width, height),
        anchor: new google.maps.Point(width / 2, height / 2)
};
}

        // Simulation du mouvement du véhicule avec des mises à jour régulières
function simulateVehicleMovement() {
// Un tableau de positions et de caps pour simuler le mouvement du véhicule
    const positions = [
            {lat: 45.5, lng: -73.55, heading: 0 },
            {lat: 45.51, lng: -73.56, heading: 45 },
            {lat: 45.52, lng: -73.57, heading: 90 },
            {lat: 45.53, lng: -73.58, heading: 135 }
            ];
            let i = 0;

    setInterval(() => {
        const pos = positions[i];
            updateVehicle({lat: pos.lat, lng: pos.lng }, pos.heading);
            i = (i + 1) % positions.length;
    }, 2000); // mise à jour toutes les 2 secondes
}
