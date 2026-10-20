<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GestionVilles — Connexion</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.15); width: 320px; }
        h1 { font-size: 1.2rem; margin: 0 0 1.5rem; }
        label { display: block; font-size: .85rem; margin-bottom: .25rem; }
        input[type=email], input[type=password] { width: 100%; box-sizing: border-box; padding: .5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: .6rem; background: #2f6fb2; color: #fff; border: 0; border-radius: 4px; font-size: 1rem; cursor: pointer; }
        button:hover { background: #265d96; }
        .error { color: #b00020; font-size: .85rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>GestionVilles — Connexion</h1>
        <?php if (! empty($error)) : ?>
            <div class="error"><?= esc($error) ?></div>
        <?php endif; ?>
        <form method="post" action="/login">
            <input type="hidden" name="next" value="<?= esc($next ?? '') ?>">
            <label for="email">Courriel</label>
            <input type="email" id="email" name="email" value="<?= esc($email ?? '') ?>" required autofocus>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
