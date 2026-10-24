<?php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Administration - Tags</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        th.sortable { cursor: pointer; user-select: none; white-space: nowrap; }
        th.sortable .sort-indicator { font-size: 0.75rem; margin-left: 4px; color: #6c757d; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Tags (table map_tags)</h2>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <?php if (empty($tags)) : ?>
        <div class="alert alert-info">Aucun tag à afficher.</div>
    <?php else : ?>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table id="tagTable" class="table table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th class="sortable" data-index="0" data-type="num">ID</th>
                    <th class="sortable" data-index="1" data-type="num">Ville</th>
                    <th class="sortable" data-index="2" data-type="string">Code</th>
                    <th class="sortable" data-index="3" data-type="string">Adresse</th>
                    <th class="sortable" data-index="4" data-type="string">Raison</th>
                    <th class="sortable" data-index="5" data-type="string">Description</th>
                    <th class="sortable" data-index="6" data-type="string">Statut</th>
                    <th class="sortable" data-index="7" data-type="date">Créé le</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($tags ?? []) as $tag) : ?>
                    <tr>
                        <td><?php echo (int) $tag['id']; ?></td>
                        <td><?php echo htmlspecialchars($tag['id_ville'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($tag['tag_code'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($tag['address_text'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($tag['reason'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($tag['description'], ENT_QUOTES); ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($tag['status'], ENT_QUOTES); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($tag['created_at'], ENT_QUOTES); ?></td>
                        <td class="text-center">
                            <form method="post" action="<?php echo ci3_url('admin/map-tags/delete'); ?>"
                                  onsubmit="return confirm('Supprimer ce tag ?');">
                                <input type="hidden" name="id" value="<?php echo (int) $tag['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script>
(function () {
    const table = document.getElementById('tagTable');
    if (!table) { return; }
    const tbody = table.querySelector('tbody');

    function getCellValue(row, index) {
        const cell = row.children[index];
        return cell ? cell.textContent.trim() : '';
    }

    function coerce(value, type) {
        if (type === 'num') {
            return parseFloat(value.replace(/[^0-9.\-]/g, '')) || 0;
        }
        if (type === 'date') {
            return Date.parse(value) || 0;
        }
        return value.toLowerCase();
    }

    table.querySelectorAll('th.sortable').forEach(th => {
        const indicator = document.createElement('span');
        indicator.className = 'sort-indicator';
        th.appendChild(indicator);

        th.addEventListener('click', () => {
            const index = parseInt(th.dataset.index, 10);
            const type = th.dataset.type || 'string';
            const currentOrder = th.dataset.order === 'asc' ? 'asc' : th.dataset.order === 'desc' ? 'desc' : null;
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

            table.querySelectorAll('th.sortable').forEach(other => {
                other.dataset.order = '';
                const span = other.querySelector('.sort-indicator');
                if (span) { span.textContent = ''; }
            });
            th.dataset.order = newOrder;
            indicator.textContent = newOrder === 'asc' ? '▲' : '▼';

            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                const va = coerce(getCellValue(a, index), type);
                const vb = coerce(getCellValue(b, index), type);
                if (va < vb) return -1;
                if (va > vb) return 1;
                return 0;
            });
            if (newOrder === 'desc') {
                rows.reverse();
            }
            rows.forEach(row => tbody.appendChild(row));
        });
    });
})();
</script>
</body>
</html>