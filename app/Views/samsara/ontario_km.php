<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Km IFTA - Samsara</title>
    <link href="<?php echo ci3_url(); ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .table th { background: #0B3B70; color: #fff; font-size: .85rem; }
        .subtle { color: #6c757d; }
        .nav-tabs .nav-link { color: #6c757d; }
        .nav-tabs .nav-link.active { color: #0B3B70; font-weight: 600; }
    </style>
</head>
<body>
<div class="container-fluid py-4" style="max-width: 1400px;">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1"><i class="bi bi-truck me-2"></i>Kilomètres IFTA (Samsara)</h1>
                    <div class="opacity-75">Rapport de kilométrage par camion par juridiction</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="small subtle mb-0">Année</label>
                    <select class="form-control form-control-sm" id="iftaYear" style="width:100px;">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?php echo ($y ?? ''); ?>" <?php echo ($y == $iftaYear) ? 'selected' : ''; ?>><?php echo ($y ?? ''); ?></option>
                        <?php endfor; ?>
                    </select>
                    <label class="small subtle mb-0">Mois</label>
                    <select class="form-control form-control-sm" id="iftaMonth" style="width:130px;">
                        <?php
                        $months = array(
                            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                        );
                        foreach (($months ?? []) as $num => $label): ?>
                            <option value="<?php echo ($num ?? ''); ?>" <?php echo ($num == $iftaMonth) ? 'selected' : ''; ?>><?php echo ($label ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary btn-sm" type="button" onclick="loadData()"><i class="bi bi-calculator me-1"></i>Calculer</button>
                    <button class="btn btn-outline-success btn-sm" type="button" onclick="exportExcel()"><i class="bi bi-file-earmark-excel me-1"></i>Exporter Excel</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($isConfigured)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Token API Samsara non configuré.</strong>
        Ajoutez votre token dans <code>application/config/samsara.php</code>.
    </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-0" id="jurisTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-on" data-jurisdiction="ON" data-bs-toggle="tab" data-bs-target="#pane-on" type="button" role="tab">Ontario</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-qc" data-jurisdiction="QC" data-bs-toggle="tab" data-bs-target="#pane-qc" type="button" role="tab">Québec</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pane-on" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <strong><i class="bi bi-table me-2 text-primary"></i>Résultats Ontario</strong>
                    <span class="small subtle ms-2" id="resultCount-on"></span>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Camion</th>
                                <th>Nom véhicule Samsara</th>
                                <th>VIN</th>
                                <th class="text-end">Km total Ontario</th>
                                <th class="text-end">Km taxable Ontario</th>
                                <th>Juridiction</th>
                            </tr>
                        </thead>
                        <tbody id="tbResults-on">
                            <tr><td colspan="6" class="text-center subtle py-4">Cliquez sur "Calculer" pour charger les données.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pane-qc" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <strong><i class="bi bi-table me-2 text-primary"></i>Résultats Québec</strong>
                    <span class="small subtle ms-2" id="resultCount-qc"></span>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Camion</th>
                                <th>Nom véhicule Samsara</th>
                                <th>VIN</th>
                                <th class="text-end">Km total Québec</th>
                                <th class="text-end">Km taxable Québec</th>
                                <th>Juridiction</th>
                            </tr>
                        </thead>
                        <tbody id="tbResults-qc">
                            <tr><td colspan="6" class="text-center subtle py-4">Cliquez sur "Calculer" pour charger les données.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo ci3_url(); ?>assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var currentJurisdiction = 'ON';

function getActiveJurisdiction() {
    var activeTab = document.querySelector('#jurisTabs .nav-link.active');
    return activeTab ? activeTab.getAttribute('data-jurisdiction') : 'ON';
}

function loadData() {
    var year = document.getElementById('iftaYear').value;
    var month = document.getElementById('iftaMonth').value;
    if (!year || !month) { alert('Veuillez sélectionner l\'année et le mois.'); return; }

    // Vérifier si le mois sélectionné est terminé
    var now = new Date();
    var currentYear = now.getFullYear();
    var currentMonth = now.getMonth() + 1;
    if (parseInt(year) > currentYear || (parseInt(year) === currentYear && parseInt(month) >= currentMonth)) {
        var msg = 'Le mois sélectionné n\'est pas encore terminé. Samsara ne peut retourner les données IFTA que pour les mois complétés. Veuillez choisir un mois antérieur.';
        ['on', 'qc'].forEach(function(j) {
            var tbody = document.getElementById('tbResults-' + j);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-warning py-4"><i class="bi bi-exclamation-triangle me-2"></i>' + msg + '</td></tr>';
            document.getElementById('resultCount-' + j).textContent = '';
        });
        return;
    }

    var jurisdictions = ['ON', 'QC'];
    jurisdictions.forEach(function(juris) {
        var tbody = document.getElementById('tbResults-' + juris.toLowerCase());
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Chargement...</td></tr>';

        $.ajax({
            url: '<?php echo ci3_url("samsara_ifta/getData"); ?>',
            type: 'POST',
            dataType: 'json',
            data: { year: year, month: month, jurisdiction: juris }
        })
        .done(function(data) {
            if (data.error) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>' + data.error + '</td></tr>';
                document.getElementById('resultCount-' + juris.toLowerCase()).textContent = '';
                return;
            }
            var rows = data.rows || [];
            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center subtle py-4">Aucune donnée pour cette période.</td></tr>';
                document.getElementById('resultCount-' + juris.toLowerCase()).textContent = '';
                return;
            }
            var html = '';
            rows.forEach(function(r) {
                html += '<tr>' +
                    '<td><span class="badge text-bg-primary">' + (r.noCamion || '(non lié)') + '</span></td>' +
                    '<td>' + (r.vehicleName || '') + '</td>' +
                    '<td class="small">' + (r.vin || '') + '</td>' +
                    '<td class="text-end fw-bold">' + Number(r.totalKm).toFixed(2) + ' km</td>' +
                    '<td class="text-end">' + Number(r.taxableKm).toFixed(2) + ' km</td>' +
                    '<td>' + r.jurisdiction + '</td>' +
                    '</tr>';
            });
            if (data.summary) {
                html += '<tr class="table-danger fw-bold">' +
                    '<td colspan="3">TOTAL (' + data.summary.vehicleCount + ' véhicules)</td>' +
                    '<td class="text-end">' + Number(data.summary.totalKm).toFixed(2) + ' km</td>' +
                    '<td class="text-end">' + Number(data.summary.taxableKm).toFixed(2) + ' km</td>' +
                    '<td></td></tr>';
            }
            tbody.innerHTML = html;
            document.getElementById('resultCount-' + juris.toLowerCase()).textContent = rows.length + ' camion(s)';
        })
        .fail(function(xhr) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Erreur: ' + (xhr.responseText || xhr.statusText) + '</td></tr>';
        });
    });
}

function exportExcel() {
    var year = document.getElementById('iftaYear').value;
    var month = document.getElementById('iftaMonth').value;
    if (!year || !month) { alert('Veuillez sélectionner l\'année et le mois.'); return; }

    // Vérifier si le mois sélectionné est terminé
    var now = new Date();
    var currentYear = now.getFullYear();
    var currentMonth = now.getMonth() + 1;
    if (parseInt(year) > currentYear || (parseInt(year) === currentYear && parseInt(month) >= currentMonth)) {
        alert('Le mois sélectionné n\'est pas encore terminé. Samsara ne peut retourner les données IFTA que pour les mois complétés. Veuillez choisir un mois antérieur.');
        return;
    }

    window.location.href = '<?php echo ci3_url("samsara_ifta/exportExcel"); ?>?year=' + year + '&month=' + month;
}
</script>
</body>
</html>
