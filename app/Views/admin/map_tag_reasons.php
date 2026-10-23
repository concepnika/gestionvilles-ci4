<?php  ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin - Raisons des tags</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 16px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    form.inline { display:inline; }
    .row { display:flex; gap:8px; flex-wrap:wrap; margin: 8px 0; }
    input[type="text"] { width: 260px; }
  </style>
</head>
<body>
  <h2>Raisons des tags (Map)</h2>

  <h3>Ajouter / Modifier</h3>
  <form method="post" action="<?php echo ci3_url('admin/tags/reasons/save'); ?>">
    <div class="row">
      <input type="hidden" name="id" value="0" />
      <label>Français<br><input type="text" name="label_fr" required></label>
      <label>Anglais<br><input type="text" name="label_en" required></label>
      <label>Ordre<br><input type="number" name="sort_order" value="0" style="width:100px"></label>
      <label>Actif<br>
        <select name="is_active">
          <option value="1" selected>Oui</option>
          <option value="0">Non</option>
        </select>
      </label>
      <button type="submit">Enregistrer</button>
    </div>
  </form>

  <h3>Liste</h3>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Français</th>
        <th>Anglais</th>
        <th>Ordre</th>
        <th>Actif</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach (($reasons ?? []) as $r): ?>
      <tr>
        <td><?php echo (int)$r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['label_fr'], ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($r['label_en'], ENT_QUOTES); ?></td>
        <td><?php echo (int)$r['sort_order']; ?></td>
        <td><?php echo ((int)$r['is_active'] === 1 ? 'Oui' : 'Non'); ?></td>
        <td>
          <form class="inline" method="post" action="<?php echo ci3_url('admin/tags/reasons/save'); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
            <input type="text" name="label_fr" value="<?php echo htmlspecialchars($r['label_fr'], ENT_QUOTES); ?>">
            <input type="text" name="label_en" value="<?php echo htmlspecialchars($r['label_en'], ENT_QUOTES); ?>">
            <input type="number" name="sort_order" style="width:100px" value="<?php echo (int)$r['sort_order']; ?>">
            <select name="is_active">
              <option value="1" <?php echo ((int)$r['is_active']===1?'selected':''); ?>>Oui</option>
              <option value="0" <?php echo ((int)$r['is_active']===0?'selected':''); ?>>Non</option>
            </select>
            <button type="submit">Mettre à jour</button>
          </form>
          <form class="inline" method="post" action="<?php echo ci3_url('admin/tags/reasons/delete/'.(int)$r['id']); ?>" onsubmit="return confirm('Supprimer cette raison ?');">
            <button type="submit" style="color:#b00">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>