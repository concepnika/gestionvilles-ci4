<?php

namespace App\Libraries;

/**
 * Port de application/libraries/KalmanFilter.php (CI3) — filtre de
 * Kalman simple pour lisser les coordonnées GPS.
 */
class KalmanFilter
{
    private $Q;              // Bruit de processus (variance de processus)
    private $R;              // Bruit de la mesure (variance de la mesure)
    private $X;              // Estimation actuelle
    private $P;              // Covariance de l'estimation actuelle
    private $isInitialized;  // Indique si le filtre a été initialisé

    public function __construct($processNoise, $measurementNoise)
    {
        $this->Q = $processNoise;
        $this->R = $measurementNoise;
        $this->isInitialized = false;
    }

    public function filter($measurement)
    {
        if (! $this->isInitialized) {
            // Initialisation avec la première mesure reçue
            $this->X = $measurement;
            $this->P = 500;  // Covariance initiale
            $this->isInitialized = true;
        } else {
            // Étape de prédiction
            $this->P = $this->P + $this->Q;

            // Calcul du gain de Kalman
            $K = $this->P / ($this->P + $this->R);

            // Mise à jour de l'estimation
            $this->X = $this->X + $K * ($measurement - $this->X);

            // Mise à jour de la covariance
            $this->P = (1 - $K) * $this->P;
        }

        return $this->X;
    }
}
