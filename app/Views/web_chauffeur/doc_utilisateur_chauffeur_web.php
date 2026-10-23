<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide du chauffeur — SaniVan Web</title>
    <style>
        :root {
            --primary: #00008B;
            --accent: #2e86c1;
            --accent-light: #d6eaf8;
            --bg: #fafbfc;
            --text: #2c3e50;
            --border: #d5dbdb;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;
            --orange: #FF8C00;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: var(--text); background: var(--bg); line-height: 1.75; }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        header.hero {
            background: linear-gradient(135deg, var(--primary) 0%, #1a5276 60%, var(--accent) 100%);
            color: #fff; text-align: center; padding: 50px 20px 40px;
        }
        header.hero h1 { font-size: 2.2rem; margin-bottom: 10px; }
        header.hero p { font-size: 1.1rem; opacity: .9; max-width: 700px; margin: 0 auto; }
        header.hero .hero-icon { font-size: 3rem; margin-bottom: 12px; }

        nav.toc {
            max-width: 900px; margin: -25px auto 30px; background: #fff; border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,.1); padding: 25px 30px;
        }
        nav.toc h2 { font-size: 1.2rem; color: var(--primary); margin-bottom: 12px; }
        nav.toc ol { padding-left: 20px; columns: 2; column-gap: 30px; }
        nav.toc ol li { margin-bottom: 6px; font-size: .92rem; break-inside: avoid; }

        .container { max-width: 900px; margin: 0 auto; padding: 0 20px 60px; }

        h2 {
            font-size: 1.6rem; color: var(--primary); margin: 45px 0 15px;
            padding-bottom: 8px; border-bottom: 3px solid var(--accent);
        }
        h3 { font-size: 1.2rem; color: var(--accent); margin: 25px 0 10px; }
        h4 { font-size: 1.05rem; color: #555; margin: 18px 0 8px; }
        p, li { margin-bottom: 8px; }
        ul, ol { padding-left: 25px; }

        .step-box {
            background: #fff; border: 1px solid var(--border); border-left: 5px solid var(--accent);
            border-radius: 0 8px 8px 0; padding: 18px 22px; margin: 15px 0;
        }
        .step-box .step-num {
            display: inline-block; background: var(--accent); color: #fff;
            width: 28px; height: 28px; text-align: center; line-height: 28px;
            border-radius: 50%; font-weight: 700; font-size: .85rem; margin-right: 10px;
        }
        .step-box .step-title { font-weight: 600; color: var(--primary); }

        .tip {
            background: #eafaf1; border-left: 5px solid var(--success); border-radius: 0 8px 8px 0;
            padding: 14px 18px; margin: 15px 0;
        }
        .tip::before { content: '💡 Astuce : '; font-weight: 700; color: var(--success); }

        .warning {
            background: #fdf2e9; border-left: 5px solid var(--warning); border-radius: 0 8px 8px 0;
            padding: 14px 18px; margin: 15px 0;
        }
        .warning::before { content: '⚠️ Attention : '; font-weight: 700; color: var(--warning); }

        .danger {
            background: #fdedec; border-left: 5px solid var(--danger); border-radius: 0 8px 8px 0;
            padding: 14px 18px; margin: 15px 0;
        }
        .danger::before { content: '🚫 Important : '; font-weight: 700; color: var(--danger); }

        .info-box {
            background: var(--accent-light); border-left: 5px solid var(--info); border-radius: 0 8px 8px 0;
            padding: 14px 18px; margin: 15px 0;
        }

        .btn-demo {
            display: inline-block; padding: 6px 18px; border-radius: 4px;
            font-weight: 700; font-size: .9rem; color: #fff; margin: 3px;
            cursor: default; text-decoration: none;
        }
        .btn-red { background: Red; }
        .btn-orange { background: Orange; color: #000 !important; }
        .btn-blue { background: DarkBlue; color: Orange !important; }
        .btn-darkred { background: DarkRed; }
        .btn-grey { background: LightGrey; color: Red !important; }

        table { width: 100%; border-collapse: collapse; margin: 15px 0 25px; font-size: .9rem; }
        th, td { border: 1px solid var(--border); padding: 10px 12px; text-align: left; }
        th { background: var(--primary); color: #fff; font-weight: 600; }
        tr:nth-child(even) { background: #f1f3f5; }

        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: .88em; color: #c0392b; }

        .field-table td:first-child { font-weight: 600; width: 180px; white-space: nowrap; }

        .section-visual {
            background: #fff; border: 2px solid var(--primary); border-radius: 10px; padding: 20px;
            margin: 15px 0; text-align: center;
        }
        .section-visual .header-bar {
            background: var(--primary); color: Orange; padding: 8px 14px; font-size: 16px;
            font-weight: bold; border-radius: 4px; margin-bottom: 12px;
        }

        footer {
            background: var(--primary); color: #fff; text-align: center; padding: 30px 20px;
            margin-top: 50px;
        }
        footer p { margin: 4px 0; font-size: .85rem; opacity: .8; }

        @media (max-width: 768px) {
            header.hero { padding: 30px 15px 25px; }
            header.hero h1 { font-size: 1.6rem; }
            nav.toc ol { columns: 1; }
            .container { padding: 0 15px 40px; }
            h2 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════════ -->
<header class="hero">
    <div class="hero-icon">🚛</div>
    <h1>Guide du chauffeur — SaniVan Web</h1>
    <p>Documentation complète de l'interface web pour les chauffeurs et aides de Robert Daoust Inc.</p>
</header>

<!-- ═══════════════════════════════════════════════════════════ -->
<nav class="toc">
    <h2>📋 Table des matières</h2>
    <ol>
        <li><a href="#intro">Introduction</a></li>
        <li><a href="#connexion">Connexion</a></li>
        <li><a href="#barre-navigation">Barre de navigation</a></li>
        <li><a href="#cedules">Badges de cédules</a></li>
        <li><a href="#dashboard">CAMION / RH (Tableau de bord)</a></li>
        <li><a href="#section-camion">Section CAMION</a></li>
        <li><a href="#section-chauffeur">Section CHAUFFEUR</a></li>
        <li><a href="#section-aide">Section AIDE</a></li>
        <li><a href="#changer-camion">Changer de camion</a></li>
        <li><a href="#changer-chauffeur">Changer de chauffeur</a></li>
        <li><a href="#villes">Page Ville / Dump</a></li>
        <li><a href="#villes-saisie">Saisie des villes</a></li>
        <li><a href="#depotoires">Saisie des dépotoires</a></li>
        <li><a href="#upload-ticket">Photo / Ticket de pesée</a></li>
        <li><a href="#gps">Page GPS (Carte)</a></li>
        <li><a href="#gps-polylines">Parcours (Polylines)</a></li>
        <li><a href="#gps-zone">Zone Start / Stop</a></li>
        <li><a href="#tags">Tags</a></li>
        <li><a href="#broker">Mode Courtier (Broker)</a></li>
        <li><a href="#admin-nav">Navigation Admin</a></li>
        <li><a href="#mobile">Utilisation sur mobile</a></li>
        <li><a href="#deconnexion">Déconnexion</a></li>
        <li><a href="#faq">Questions fréquentes</a></li>
    </ol>
</nav>

<!-- ═══════════════════════════════════════════════════════════ -->
<div class="container">

<!-- 1 ──────────────────────────────────────────────────────── -->
<h2 id="intro">1. Introduction</h2>
<p>
    <strong>SaniVan Web</strong> est l'interface web accessible depuis un navigateur (téléphone, tablette ou ordinateur)
    qui permet aux chauffeurs et aides de saisir leurs données de collecte quotidienne :
    kilométrage, heures de travail, informations des villes, dépotoires et tonnages.
</p>
<p>Cette interface reproduit les mêmes fonctionnalités que l'application mobile MAUI, mais directement dans votre navigateur web.</p>

<div class="info-box">
    <strong>Adresse d'accès :</strong> Ouvrez votre navigateur et allez à l'adresse <code>/ramassage-ville</code> du site de l'entreprise.
</div>

<h3>Principales fonctions</h3>
<ul>
    <li>📝 Saisir les données du <strong>camion</strong> (KM départ/fin, fuel, heures)</li>
    <li>👤 Saisir les <strong>heures du chauffeur</strong> (début, fin, extras)</li>
    <li>🤝 Saisir les <strong>heures des aides</strong></li>
    <li>🏘️ Gérer les <strong>villes et dépotoires</strong> (heures, tonnages, tickets)</li>
    <li>🗺️ Voir la <strong>carte GPS</strong> avec les parcours de collecte</li>
    <li>📷 <strong>Photographier les tickets</strong> de pesée</li>
</ul>

<!-- 2 ──────────────────────────────────────────────────────── -->
<h2 id="connexion">2. Connexion</h2>
<p>La page de connexion ressemble à un formulaire avec le logo SaniVan en haut. Vous devez remplir 4 champs :</p>

<div class="step-box">
    <span class="step-num">1</span>
    <span class="step-title">Camions</span>
    <p style="margin-top:6px;">
        Entrez le <strong>numéro de votre camion</strong> (ex: <code>25</code>, <code>031</code>).
        Si vous entrez un numéro de moins de 3 chiffres, un zéro sera ajouté automatiquement (ex: <code>25</code> → <code>025</code>).
    </p>
</div>

<div class="step-box">
    <span class="step-num">2</span>
    <span class="step-title">KM de Départ</span>
    <p style="margin-top:6px;">
        Le kilométrage de départ sera <strong>pré-rempli automatiquement</strong> avec le dernier KM de fin enregistré pour ce camion.
        Vous pouvez le modifier si nécessaire.
    </p>
</div>

<div class="step-box">
    <span class="step-num">3</span>
    <span class="step-title">Code usager</span>
    <p style="margin-top:6px;">
        Entrez votre <strong>code d'utilisateur</strong> fourni par votre superviseur.
    </p>
</div>

<div class="step-box">
    <span class="step-num">4</span>
    <span class="step-title">Mot de passe</span>
    <p style="margin-top:6px;">
        Entrez votre <strong>mot de passe</strong>. Appuyez ensuite sur le bouton <strong style="color:DarkBlue;">Connecter</strong>.
    </p>
</div>

<div class="tip">
    Vous pouvez naviguer entre les champs en appuyant sur la touche <strong>Entrée</strong> (Enter). L'ordre est : Camion → KM → Code → Mot de passe → Connexion automatique.
</div>

<div class="warning">
    Si vous n'êtes pas cédulé pour aujourd'hui, vous ne pourrez pas vous connecter. Un message « Vous n'êtes pas autorisé » s'affichera. Contactez votre superviseur.
</div>

<!-- 3 ──────────────────────────────────────────────────────── -->
<h2 id="barre-navigation">3. Barre de navigation</h2>
<p>Après la connexion, une barre de navigation apparaît en haut de l'écran avec des boutons colorés :</p>

<table>
    <thead><tr><th>Bouton</th><th>Couleur</th><th>Description</th></tr></thead>
    <tbody>
        <tr>
            <td><span class="btn-demo btn-red">CAMION/RH</span></td>
            <td>Rouge</td>
            <td>Tableau de bord principal — informations du camion, du chauffeur et des aides</td>
        </tr>
        <tr>
            <td><span class="btn-demo btn-orange">Ville/Dump</span></td>
            <td>Orange</td>
            <td>Gestion des villes et dépotoires — heures d'arrivée/départ, tonnages, tickets</td>
        </tr>
        <tr>
            <td><span class="btn-demo btn-blue">GPS</span></td>
            <td>Bleu foncé</td>
            <td>Carte GPS avec le suivi en temps réel du camion et les parcours de collecte</td>
        </tr>
        <tr>
            <td><span class="btn-demo btn-darkred">Tag</span></td>
            <td>Rouge foncé</td>
            <td>Tags assignés (anomalies, signalements). Un badge numéroté peut apparaître</td>
        </tr>
        <tr>
            <td>📷 Caméra</td>
            <td>—</td>
            <td>Accès rapide à la caméra pour signaler des anomalies</td>
        </tr>
        <tr>
            <td>❓ Aide</td>
            <td>—</td>
            <td>Affiche un résumé des fonctions de chaque bouton</td>
        </tr>
        <tr>
            <td><span class="btn-demo btn-grey">FERMER</span></td>
            <td>Gris/Rouge</td>
            <td>Déconnexion — ferme votre session</td>
        </tr>
    </tbody>
</table>

<p>Le bouton de la page active est mis en évidence avec un <strong>contour jaune/orange</strong>.</p>
<p>Votre <strong>nom</strong> et l'<strong>horloge</strong> en temps réel sont affichés à droite de la barre.</p>

<!-- 4 ──────────────────────────────────────────────────────── -->
<h2 id="cedules">4. Badges de cédules</h2>
<p>
    Si vous avez <strong>plusieurs cédules</strong> pour la journée (ex: un parcours Déchets et un parcours Recyclage),
    elles apparaissent sous forme de <strong>badges colorés</strong> en haut de chaque page.
</p>
<p>Chaque badge affiche le <strong>numéro du camion</strong> et le <strong>nom de la ville</strong>. La couleur correspond au type de déchet :</p>
<ul>
    <li>Le badge de la cédule <strong>active</strong> a un contour jaune/orange</li>
    <li>Cliquez sur un autre badge pour <strong>changer de cédule</strong></li>
</ul>

<div class="tip">
    Quand vous changez de cédule, toutes les données affichées (camion, chauffeur, villes) se mettent à jour pour correspondre à la cédule sélectionnée.
</div>

<!-- 5 ──────────────────────────────────────────────────────── -->
<h2 id="dashboard">5. CAMION / RH (Tableau de bord)</h2>
<p>
    Le tableau de bord est la page principale. Il est divisé en <strong>3 sections</strong> disposées côte à côte sur un grand écran
    (ou empilées sur mobile) :
</p>
<ol>
    <li><strong>INFORMATIONS DU CAMION</strong> — données du véhicule</li>
    <li><strong>INFO DU CHAUFFEUR</strong> — heures du chauffeur et extras</li>
    <li><strong>INFORMATION AIDE</strong> — heures de chaque aide assigné</li>
</ol>

<div class="info-box">
    Toutes les données sont <strong>sauvegardées automatiquement</strong> dès que vous modifiez un champ et quittez celui-ci (blur/change). Pas besoin de cliquer sur un bouton « Sauvegarder ».
</div>

<!-- 6 ──────────────────────────────────────────────────────── -->
<h2 id="section-camion">6. Section CAMION</h2>
<p>Cette section (fond bleu foncé en en-tête) affiche le numéro du camion et contient les champs suivants :</p>

<table class="field-table">
    <thead><tr><th>Champ</th><th>Description</th><th>Format</th></tr></thead>
    <tbody>
        <tr><td>KM / DEBUT</td><td>Kilométrage au départ (pré-rempli depuis la connexion)</td><td>Nombre décimal</td></tr>
        <tr><td>KM / FIN</td><td>Kilométrage à la fin de la journée</td><td>Nombre décimal</td></tr>
        <tr><td>FUEL (🛢️)</td><td>Quantité de carburant (litres). Remplissez seulement si vous le pouvez.</td><td>Nombre décimal</td></tr>
        <tr><td>HEURES / DEBUT</td><td>Heure de début de travail du camion</td><td>HH:MM</td></tr>
        <tr><td>HEURES / FIN</td><td>Heure de fin de travail du camion</td><td>HH:MM</td></tr>
    </tbody>
</table>

<p>Le bouton <strong>flèche</strong> (↔) sous le numéro de camion permet de <strong>changer de camion</strong> en cours de journée (voir section 9).</p>

<div class="tip">
    Les champs sur fond <strong style="background:Yellow; padding:2px 8px;">jaune</strong> sont des champs de saisie modifiables.
</div>

<!-- 7 ──────────────────────────────────────────────────────── -->
<h2 id="section-chauffeur">7. Section CHAUFFEUR</h2>
<p>Cette section affiche votre <strong>nom</strong> et permet de saisir vos heures :</p>

<h4>Bloc HEURES</h4>
<table class="field-table">
    <thead><tr><th>Champ</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>DEBUT</td><td>Heure de début de votre quart de travail</td></tr>
        <tr><td>FIN</td><td>Heure de fin de votre quart de travail</td></tr>
    </tbody>
</table>

<h4>Bloc EXTRA</h4>
<p>Si vous avez effectué des heures supplémentaires ou un travail spécial :</p>
<table class="field-table">
    <thead><tr><th>Champ</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>CHOISIR TYPE</td><td>Sélectionnez le type d'extra dans la liste déroulante</td></tr>
        <tr><td>DEBUT</td><td>Heure de début de l'extra</td></tr>
        <tr><td>FIN</td><td>Heure de fin de l'extra</td></tr>
    </tbody>
</table>

<div class="tip">
    Les champs Extra sont <strong>optionnels</strong>. Remplissez-les seulement si vous avez des heures supplémentaires ou un travail spécial à déclarer.
</div>

<p>Le bouton <strong>flèche</strong> (↔) sous votre nom permet de <strong>changer de chauffeur</strong> (voir section 10).</p>

<!-- 8 ──────────────────────────────────────────────────────── -->
<h2 id="section-aide">8. Section AIDE</h2>
<p>
    Si un ou plusieurs <strong>aides</strong> (helpers) sont assignés à votre cédule, leurs noms et champs d'heures apparaissent dans cette section (fond orange en en-tête).
</p>
<p>Pour chaque aide :</p>
<table class="field-table">
    <thead><tr><th>Champ</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>DEBUT</td><td>Heure de début de l'aide</td></tr>
        <tr><td>FIN</td><td>Heure de fin de l'aide</td></tr>
    </tbody>
</table>

<div class="info-box">
    Jusqu'à <strong>4 aides</strong> peuvent être assignés à une même cédule. Chacun a ses propres champs d'heures. Les données sont sauvegardées automatiquement.
</div>

<!-- 9 ──────────────────────────────────────────────────────── -->
<h2 id="changer-camion">9. Changer de camion</h2>
<p>Si vous devez changer de véhicule en cours de journée :</p>

<div class="step-box">
    <span class="step-num">1</span>
    <span class="step-title">Remplir les champs obligatoires</span>
    <p style="margin-top:6px;">
        Avant de pouvoir changer de camion, vous devez avoir rempli les champs <strong>KM/DEBUT</strong>, <strong>KM/FIN</strong>,
        <strong>HEURES/DEBUT</strong> et <strong>HEURES/FIN</strong>. Sinon, un message d'alerte vous rappellera de les remplir.
    </p>
</div>

<div class="step-box">
    <span class="step-num">2</span>
    <span class="step-title">Cliquer sur la flèche (↔)</span>
    <p style="margin-top:6px;">
        Cliquez sur le bouton flèche sous le numéro du camion actuel.
    </p>
</div>

<div class="step-box">
    <span class="step-num">3</span>
    <span class="step-title">Choisir le nouveau camion</span>
    <p style="margin-top:6px;">
        Une fenêtre popup s'ouvre avec la <strong>liste des camions disponibles</strong>. Cliquez sur le camion désiré.
        La page se rechargera automatiquement avec le nouveau camion.
    </p>
</div>

<div class="warning">
    Le changement de camion s'applique à <strong>toutes vos cédules</strong> de la journée.
</div>

<!-- 10 ─────────────────────────────────────────────────────── -->
<h2 id="changer-chauffeur">10. Changer de chauffeur</h2>
<p>Si un autre chauffeur prend votre relève en cours de journée :</p>

<div class="step-box">
    <span class="step-num">1</span>
    <span class="step-title">Remplir vos heures</span>
    <p style="margin-top:6px;">
        Les champs <strong>HEURES DEBUT</strong> et <strong>HEURES FIN</strong> du chauffeur doivent être remplis avant le changement.
    </p>
</div>

<div class="step-box">
    <span class="step-num">2</span>
    <span class="step-title">Cliquer sur la flèche (↔)</span>
    <p style="margin-top:6px;">
        Cliquez sur le bouton flèche sous votre nom dans la section Chauffeur.
    </p>
</div>

<div class="step-box">
    <span class="step-num">3</span>
    <span class="step-title">Authentification + sélection</span>
    <p style="margin-top:6px;">
        Une fenêtre popup s'ouvre avec des champs <strong>Code usager</strong> et <strong>Mot de passe</strong>,
        suivis de la <strong>liste des chauffeurs disponibles</strong>. Le nouveau chauffeur doit entrer ses identifiants
        et cliquer sur son nom dans la liste.
    </p>
</div>

<!-- 11 ─────────────────────────────────────────────────────── -->
<h2 id="villes">11. Page Ville / Dump</h2>
<p>
    Cliquez sur le bouton <span class="btn-demo btn-orange">Ville/Dump</span> dans la barre de navigation
    pour accéder à la gestion des villes et des dépotoires.
</p>
<p>En haut de la page, des <strong>boutons de cédules</strong> permettent de basculer entre vos cédules du jour. La cédule active est identifiée par une bordure noire et une étoile jaune ⭐.</p>

<h3>Bouton « Ajouter un ramassage »</h3>
<p>
    Si vous effectuez un ramassage supplémentaire non prévu, cliquez sur le bouton
    <strong style="color:#fff; background:#00008B; padding:4px 12px; border-radius:4px;">Ajouter un ramassage</strong>.
    Une liste de villes disponibles apparaîtra pour en sélectionner une.
</p>

<!-- 12 ─────────────────────────────────────────────────────── -->
<h2 id="villes-saisie">12. Saisie des villes</h2>
<p>Pour chaque ville assignée à votre cédule, une ligne apparaît dans le tableau avec les éléments suivants :</p>

<table class="field-table">
    <thead><tr><th>Élément</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>Nom de la ville</td><td>Identifiant et nom de la ville collectée</td></tr>
        <tr>
            <td>▶ Bouton Start</td>
            <td>Enregistre automatiquement l'heure actuelle comme <strong>heure d'arrivée</strong> dans la ville</td>
        </tr>
        <tr><td>Heure début</td><td>Heure d'arrivée dans la ville (saisie manuelle ou via bouton Start)</td></tr>
        <tr>
            <td>⏹ Bouton Stop</td>
            <td>Enregistre automatiquement l'heure actuelle comme <strong>heure de départ</strong> de la ville</td>
        </tr>
        <tr><td>Heure fin</td><td>Heure de départ de la ville (saisie manuelle ou via bouton Stop)</td></tr>
        <tr><td>Bacs</td><td>Nombre de bacs collectés dans cette ville</td></tr>
    </tbody>
</table>

<div class="tip">
    Utilisez les boutons <strong>Start</strong> et <strong>Stop</strong> pour enregistrer rapidement vos heures d'arrivée et de départ sans avoir à les taper manuellement.
</div>

<!-- 13 ─────────────────────────────────────────────────────── -->
<h2 id="depotoires">13. Saisie des dépotoires</h2>
<p>Sous chaque ville, une ou plusieurs <strong>lignes de dépotoire</strong> permettent de saisir les informations de disposition :</p>

<table class="field-table">
    <thead><tr><th>Champ</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>Dépotoire</td><td>Sélectionnez le dépotoire dans la liste déroulante</td></tr>
        <tr><td>H/deb</td><td>Heure d'arrivée au dépotoire</td></tr>
        <tr><td>H/fin</td><td>Heure de départ du dépotoire</td></tr>
        <tr><td>📷 Scanner</td><td>Bouton pour prendre en photo le ticket de pesée (voir section 14)</td></tr>
        <tr><td>Ton.</td><td>Tonnage (poids en tonnes)</td></tr>
        <tr><td>Boîte</td><td>Numéro ou identification de la boîte</td></tr>
        <tr><td>TrBo</td><td>Case à cocher — Transbordement de boîte</td></tr>
        <tr><td>Mat.</td><td>Case à cocher — Avec matériel</td></tr>
    </tbody>
</table>

<div class="info-box">
    Tous les champs sont sauvegardés automatiquement. Lorsqu'un champ est sauvegardé avec succès, son fond passe brièvement au <strong style="background:#d5f5e3; padding:2px 6px;">vert clair</strong> avant de revenir au jaune.
</div>

<!-- 14 ─────────────────────────────────────────────────────── -->
<h2 id="upload-ticket">14. Photo / Ticket de pesée</h2>
<p>Pour photographier un ticket de pesée au dépotoire :</p>

<div class="step-box">
    <span class="step-num">1</span>
    <span class="step-title">Cliquer sur l'icône 📷</span>
    <p style="margin-top:6px;">
        Dans la ligne du dépotoire, cliquez sur le bouton caméra. Votre appareil ouvrira la caméra
        ou le sélecteur de fichiers.
    </p>
</div>

<div class="step-box">
    <span class="step-num">2</span>
    <span class="step-title">Prendre la photo</span>
    <p style="margin-top:6px;">
        Prenez une photo du ticket de pesée. La photo est <strong>envoyée automatiquement</strong> au serveur.
    </p>
</div>

<div class="step-box">
    <span class="step-num">3</span>
    <span class="step-title">Confirmation</span>
    <p style="margin-top:6px;">
        Une fois le téléversement réussi, l'icône caméra est remplacée par un bouton <strong style="color:#fff; background:#27ae60; padding:2px 8px; border-radius:3px;">vert</strong>
        avec une icône de fichier. Vous pouvez cliquer dessus pour voir la photo envoyée.
    </p>
</div>

<div class="tip">
    Sur mobile, l'option « Prendre une photo » avec l'appareil arrière s'ouvre par défaut grâce à l'attribut <code>capture="environment"</code>.
</div>

<!-- 15 ─────────────────────────────────────────────────────── -->
<h2 id="gps">15. Page GPS (Carte)</h2>
<p>
    Cliquez sur le bouton <span class="btn-demo btn-blue">GPS</span> pour accéder à la carte en temps réel.
</p>
<p>Cette page affiche :</p>
<ul>
    <li>🗺️ Une <strong>carte interactive Leaflet</strong> occupant la quasi-totalité de l'écran</li>
    <li>📍 La <strong>position GPS de votre camion</strong> mise à jour automatiquement via le système Samsara</li>
    <li>📊 Un indicateur de statut GPS (en ligne / hors ligne) en haut à droite des badges cédules</li>
</ul>

<h3>Barre d'outils de la carte</h3>
<table class="field-table">
    <thead><tr><th>Bouton</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>▶ Start</td><td>Démarre un parcours manuel — le camion laisse une trace sur la carte</td></tr>
        <tr><td>⏹ Stop</td><td>Termine et fige le parcours manuel</td></tr>
        <tr><td>Stop Route</td><td>Arrête l'itinéraire en cours</td></tr>
    </tbody>
</table>

<h3>Option « Suivre automatiquement le camion »</h3>
<p>
    Une case à cocher en bas de la carte permet d'activer ou désactiver le <strong>suivi automatique</strong>.
    Lorsqu'activé, la carte se recentre automatiquement sur la position du camion à chaque mise à jour.
</p>

<!-- 16 ─────────────────────────────────────────────────────── -->
<h2 id="gps-polylines">16. Parcours (Polylines)</h2>
<p>
    Si des parcours de collecte (polylines) sont associés à vos cédules, des <strong>boutons numérotés</strong>
    apparaissent sur le côté de la carte.
</p>
<ul>
    <li>Cliquez sur un bouton de parcours pour <strong>afficher uniquement ce parcours</strong> sur la carte</li>
    <li>Le parcours est affiché en <strong>rouge</strong> avec des flèches de direction</li>
    <li>Les retours arrière sont affichés en <strong>orange pointillé</strong></li>
</ul>

<h4>Légende du parcours</h4>
<table>
    <thead><tr><th>Couleur</th><th>Signification</th></tr></thead>
    <tbody>
        <tr><td><span style="display:inline-block; width:40px; height:4px; background:#FF0000; vertical-align:middle;"></span> Rouge</td><td>Direction normale du parcours</td></tr>
        <tr><td><span style="display:inline-block; width:40px; height:4px; background:#FF8C00; border-top:2px dashed #FF8C00; vertical-align:middle;"></span> Orange pointillé</td><td>Retour arrière</td></tr>
    </tbody>
</table>

<!-- 17 ─────────────────────────────────────────────────────── -->
<h2 id="gps-zone">17. Zone Start / Stop</h2>
<p>
    Le système détecte automatiquement votre entrée et sortie de zone de collecte et peut enregistrer les heures correspondantes :
</p>
<ul>
    <li><strong>Entrée en zone</strong> → enregistre l'heure de début (<code>HreDeb</code>) sur la dernière ville de la cédule</li>
    <li><strong>Sortie de zone</strong> → enregistre l'heure de fin (<code>HreFin</code>) sur cette même ville</li>
</ul>

<div class="info-box">
    Cette fonctionnalité est automatique et se déclenche en arrière-plan lorsque la page GPS est ouverte et que le suivi est actif.
</div>

<!-- 18 ─────────────────────────────────────────────────────── -->
<h2 id="tags">18. Tags</h2>
<p>
    Le bouton <span class="btn-demo btn-darkred">Tag</span> dans la barre de navigation affiche les <strong>tags actifs</strong>
    (anomalies, signalements) qui vous sont assignés.
</p>
<ul>
    <li>Un <strong>badge numéroté</strong> rouge/orange peut apparaître sur le bouton Tag indiquant le nombre de tags ouverts</li>
    <li>Les tags peuvent avoir différents statuts : <strong>En attente</strong>, <strong>Accusé</strong>, <strong>Terminé</strong>, <strong>Rejeté</strong></li>
</ul>

<div class="info-box">
    Pour les administrateurs, cliquer sur le bouton Tag ouvre une fenêtre modale avec tous les tags actifs et leur détail. Pour les chauffeurs non-admin, le bouton redirige vers la page GPS.
</div>

<!-- 19 ─────────────────────────────────────────────────────── -->
<h2 id="broker">19. Mode Courtier (Broker)</h2>
<p>
    Si vous êtes un <strong>chauffeur sous-traitant (courtier)</strong>, l'interface est simplifiée :
</p>
<ul>
    <li>Les champs <strong>KM/DEBUT</strong>, <strong>KM/FIN</strong> et <strong>FUEL</strong> sont <strong>masqués</strong> dans la section Camion</li>
    <li>Le bouton de <strong>changement de camion</strong> (flèche) est également masqué</li>
    <li>La section <strong>EXTRA</strong> du chauffeur est masquée</li>
    <li>Vous conservez l'accès aux heures du camion (début/fin), aux heures du chauffeur et à toutes les autres fonctionnalités</li>
</ul>

<div class="info-box">
    Le mode courtier est activé automatiquement selon votre profil dans le système. Vous n'avez aucune action à effectuer.
</div>

<!-- 20 ─────────────────────────────────────────────────────── -->
<h2 id="admin-nav">20. Navigation Admin</h2>
<p>
    Si votre compte a des <strong>droits administrateur</strong>, un bouton de menu supplémentaire (icône grille ▦)
    apparaît dans la barre de navigation. En cliquant dessus, un menu déroulant propose :
</p>

<table class="field-table">
    <thead><tr><th>Option</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>🚛 Tous les camions</td><td>Ouvre la page de suivi GPS de tous les camions sur une carte Google Maps</td></tr>
        <tr><td>ℹ️ Info Villes</td><td>Ouvre le hub des villes cédulées du jour sur une carte Leaflet</td></tr>
        <tr><td>🖥️ Bureau</td><td>Ouvre l'interface d'administration (bureau admin) dans un nouvel onglet</td></tr>
    </tbody>
</table>

<div class="info-box">
    Ces options sont réservées aux administrateurs et ne sont pas visibles pour les chauffeurs réguliers.
</div>

<!-- 21 ─────────────────────────────────────────────────────── -->
<h2 id="mobile">21. Utilisation sur mobile</h2>
<p>
    L'interface SaniVan Web est <strong>entièrement responsive</strong> et optimisée pour une utilisation sur téléphone ou tablette.
</p>

<h3>En-tête mobile</h3>
<p>
    Sur un écran de moins de 768 pixels de large, la barre de navigation complète est remplacée par un <strong>en-tête compact</strong> contenant :
</p>
<ul>
    <li>Un <strong>bouton hamburger</strong> (☰) pour ouvrir le menu</li>
    <li>Le <strong>logo SaniVan</strong></li>
    <li>Le <strong>titre de la page</strong> actuelle</li>
    <li>L'<strong>horloge</strong></li>
</ul>

<h3>Menu mobile (drawer)</h3>
<p>En cliquant sur le bouton hamburger, un panneau latéral glisse depuis la gauche avec tous les liens de navigation :</p>
<ul>
    <li><span style="display:inline-block; width:12px; height:12px; background:Red; border-radius:50%;"></span> <strong>CAMION/RH</strong> — Tableau de bord</li>
    <li><span style="display:inline-block; width:12px; height:12px; background:Orange; border-radius:50%;"></span> <strong>Ville/Dump</strong> — Villes et dépotoires</li>
    <li><span style="display:inline-block; width:12px; height:12px; background:DarkBlue; border-radius:50%;"></span> <strong>GPS</strong> — Carte</li>
    <li><span style="display:inline-block; width:12px; height:12px; background:DarkRed; border-radius:50%;"></span> <strong>Tag</strong> — Tags</li>
    <li>📷 <strong>Caméra</strong></li>
    <li>❓ <strong>Aide</strong></li>
    <li style="color:Red;">🚪 <strong>FERMER</strong> — Déconnexion</li>
</ul>

<div class="tip">
    Vous pouvez fermer le menu mobile en cliquant sur la zone sombre à droite du panneau, ou en appuyant de nouveau sur le bouton hamburger.
</div>

<h3>Page GPS sur mobile</h3>
<p>
    Sur la page GPS en mode mobile, un bouton flottant <strong>« Outils »</strong> apparaît en bas de l'écran.
    En cliquant dessus, un panneau s'ouvre avec les contrôles de parcours (Start, Stop, Stop Route)
    et les boutons de polylines, optimisés pour le tactile.
</p>

<!-- 22 ─────────────────────────────────────────────────────── -->
<h2 id="deconnexion">22. Déconnexion</h2>
<p>Pour vous déconnecter :</p>

<div class="step-box">
    <span class="step-num">1</span>
    <span class="step-title">Cliquer sur FERMER</span>
    <p style="margin-top:6px;">
        Cliquez sur le bouton <span class="btn-demo btn-grey">FERMER</span> dans la barre de navigation
        (ou dans le menu mobile).
    </p>
</div>

<div class="step-box">
    <span class="step-num">2</span>
    <span class="step-title">Retour à la page de connexion</span>
    <p style="margin-top:6px;">
        Votre session est détruite et vous êtes redirigé vers la page de connexion.
        Vos données saisies sont déjà sauvegardées sur le serveur.
    </p>
</div>

<div class="danger">
    Ne partagez jamais votre code usager et mot de passe avec une autre personne. Si vous pensez que vos identifiants sont compromis, contactez votre superviseur immédiatement.
</div>

<!-- 23 ─────────────────────────────────────────────────────── -->
<h2 id="faq">23. Questions fréquentes</h2>

<h4>❓ Je ne peux pas me connecter, que faire ?</h4>
<p>
    Vérifiez que vous êtes <strong>cédulé pour aujourd'hui</strong>. Si le message « Vous n'êtes pas autorisé » s'affiche,
    contactez votre superviseur pour confirmer votre assignation.
</p>

<h4>❓ Mon KM de départ n'est pas bon, comment le changer ?</h4>
<p>
    Le KM de départ est automatiquement rempli avec le dernier KM de fin du camion.
    Vous pouvez le modifier manuellement à tout moment dans le champ <strong>KM/DEBUT</strong> sur le tableau de bord.
</p>

<h4>❓ Je ne vois pas la section AIDE, est-ce normal ?</h4>
<p>
    La section Aide n'apparaît que si un ou plusieurs aides sont <strong>assignés à votre cédule</strong>.
    Si aucun aide n'est prévu, la section est automatiquement masquée.
</p>

<h4>❓ Comment ajouter un dépotoire supplémentaire ?</h4>
<p>
    Un nouveau dépotoire est automatiquement ajouté lorsque vous remplissez complètement les informations du dépotoire précédent
    et lorsqu'un nouveau ramassage est ajouté. Vous pouvez aussi utiliser le bouton « Ajouter un ramassage ».
</p>

<h4>❓ La carte GPS ne montre pas ma position, que faire ?</h4>
<p>
    Assurez-vous que le numéro de camion saisi à la connexion correspond bien au véhicule enregistré dans le système <strong>Samsara</strong>.
    Si le problème persiste, vérifiez votre connexion internet et contactez votre superviseur.
</p>

<h4>❓ Je suis courtier et je ne vois pas les champs KM/FUEL, est-ce normal ?</h4>
<p>
    Oui, c'est normal. En mode courtier (sous-traitant), les champs KM, FUEL et EXTRA sont masqués
    car cette information est gérée différemment pour les sous-traitants.
</p>

<h4>❓ Comment changer de cédule si j'en ai plusieurs ?</h4>
<p>
    Cliquez simplement sur le <strong>badge coloré</strong> de la cédule souhaitée en haut de n'importe quelle page.
    Toutes les données se mettent à jour automatiquement.
</p>

<h4>❓ La photo du ticket ne s'envoie pas, que faire ?</h4>
<p>
    Vérifiez votre connexion internet. Assurez-vous que votre navigateur a les <strong>permissions d'accès à la caméra</strong>.
    Si le problème persiste, prenez la photo avec l'application caméra de votre téléphone et envoyez-la à votre superviseur.
</p>

<h4>❓ Mes données sont-elles sauvegardées si je perds la connexion ?</h4>
<p>
    Les données sont sauvegardées <strong>au moment où vous quittez un champ</strong> (blur/change).
    Si vous perdez la connexion avant de quitter le champ, la dernière modification pourrait ne pas être enregistrée.
    Assurez-vous d'avoir une connexion stable lors de la saisie.
</p>

</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<footer>
    <p><strong>SaniVan Web — Guide du chauffeur</strong></p>
    <p>Robert Daoust Inc. — Application de gestion des collectes municipales</p>
    <p>© 2025 — Documentation interne</p>
</footer>

</body>
</html>
