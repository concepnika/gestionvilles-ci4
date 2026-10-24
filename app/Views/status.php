<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GestionVilles — CI4</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; padding: 2rem 2.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.15); width: 520px; }
        h1 { font-size: 1.25rem; margin: 0 0 .25rem; color: #00008b; }
        .sub { color: #666; font-size: .85rem; margin-bottom: 1.25rem; }
        .session { border: 1px solid #e0e0e0; border-radius: 6px; padding: 1rem; margin-bottom: 1.25rem; background: #fafafa; }
        .session .ok { color: #1b5e20; font-weight: bold; }
        .session .ko { color: #b00020; font-weight: bold; }
        .user { font-size: 1rem; margin: .25rem 0; }
        .user b { font-size: 1.05rem; }
        ul { padding-left: 1.1rem; margin: .5rem 0 1.25rem; }
        li { margin: .3rem 0; font-size: .9rem; }
        a { color: #2f6fb2; }
        .note { font-size: .8rem; color: #888; border-top: 1px solid #eee; padding-top: .8rem; }
        .btn { display: inline-block; padding: .45rem .9rem; background: #b00020; color: #fff; border: 0; border-radius: 4px; text-decoration: none; font-size: .85rem; }
        .btn:hover { background: #8d0019; }
    </style>
</head>
<body>
    <div class="card">
        <h1>GestionVilles — CI4</h1>
        <div class="sub">Migration strangler — l'app CI4 cohabite avec CI3 pendant le portage.</div>

        <?php if ($loggedIn) : ?>
            <div class="session">
                <span class="ok">Connecté</span> (session au format CI3)
                <?php if ($nom !== '') : ?>
                    <p class="user"><b><?= esc($nom) ?></b><?php if ($email !== '') : ?> — <?= esc($email) ?><?php endif; ?></p>
                <?php endif; ?>
                <?php if ($privilege !== '') : ?>
                    <p class="user" style="color:#666;margin-top:.1rem;">Privilège : <?= esc($privilege) ?></p>
                <?php endif; ?>
            </div>
            <p><a class="btn" href="/admin/quitter">Déconnexion</a></p>
        <?php else : ?>
            <div class="session">
                <span class="ko">Non connecté</span> — session CI3 absente ou expirée.
            </div>
            <p><a href="/admin/connexion">Se connecter</a></p>
        <?php endif; ?>

        <ul>
            <li><a href="/admin/connexion">Tableau de bord admin</a> (porté : GotoHome → partials/home)</li>
            <li><a href="/villes/requetes">Portail villes</a> (porté : /fr, /en)</li>
            <li><a href="/ramassage-ville">Portail chauffeurs (web)</a></li>
            <li><a href="/sanivan/get?action=2">API tablettes SaniVan (JSON)</a></li>
            <li><a href="/whoami">/whoami</a> — session CI4 décodée (JSON)</li>
        </ul>

        <div class="note">
            L'essentiel de co_gestion est porté mécaniquement (adaptateurs CI3 : db, input, session,
            load, output — voir AGENTS.md) ; les méthodes non portées restent relayées à CI3 par le
            proxy interne. Modules portés : tableau de bord admin, portail villes, portail chauffeurs
            web, tablettes Sanivan, API tablettes.
        </div>
    </div>
</body>
</html>
