<?php

$page_id    = 'villes';
$page_title = 'Villes';

ob_start();
?>

<!-- ══════════════════════════════════════════════════════
     BOUTONS CÉDULES (btnVille1..4) – Reproduit VillesView.xaml
     ══════════════════════════════════════════════════════ -->
<div class="row" style="margin-bottom:10px;">
    <div class="col-md-12 text-center" id="cedule-buttons">
        <?php foreach (($cedules ?? []) as $i => $ced):
            $bgColor   = ! empty($ced['BackCouleurDechet']) ? $ced['BackCouleurDechet'] : '#3498db';
            $txtColor  = ! empty($ced['TextColorDechet'])   ? $ced['TextColorDechet']   : '#fff';
            $label     = htmlspecialchars(($ced['NomVille'] ?? '') . ' / ' . substr($ced['TypeDeProduit'] ?? '', 0, 2));
            $isActive  = ($i == $activeCedule);
        ?>
        <button type="button"
                class="btn btn-cedule-ville <?php if (!empty($isActive)) echo 'active-cedule'; ?>"
                data-index="<?php echo ($i ?? ''); ?>"
                data-idcedule="<?php echo ($ced['Id'] ?? ''); ?>"
                data-idville="<?php echo ($ced['IdVilles'] ?? '') ?? ''; ?>"
                style="background:<?php echo ($bgColor ?? ''); ?>; color:<?php echo ($txtColor ?? ''); ?>;
                       font-size:16px; font-weight:bold; min-width:250px; height:50px; margin:3px;
                       <?php if (!empty($isActive)) echo 'border:3px solid #000; border-radius:8px;'; ?>">
            <?php if (!empty($isActive)): ?><img src="/assets/img/etoilejaune.png" style="height:20px; margin-right:5px;"><?php endif; ?>
            <?php echo ($label ?? ''); ?>
        </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bouton Ajouter un ramassage -->
<div class="row" style="margin-bottom:15px;">
    <div class="col-md-12 text-center">
        <button type="button" id="btnAddRamassage" class="btn"
                style="background:#00008B; color:#fff; font-size:22px; width:300px; height:55px;">
            Ajouter un ramassage
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     GRILLE VILLES + DÉPOTOIRES (grdXDepotoire) – Reproduit le grid dynamique
     ══════════════════════════════════════════════════════ -->
<div class="table-responsive" id="grille-container">
    <table class="table table-bordered" id="tblVillesDepots" style="font-size:14px; font-weight:bold;">
        <tbody id="tbodyVilles">
        <?php if ( ! empty($villes)): ?>
            <?php foreach (($villes ?? []) as $ville): ?>
            <!-- ── Ligne VILLE ── -->
            <tr class="row-ville" data-idville="<?php echo ($ville['Id'] ?? ''); ?>">
                <td style="width:200px; background:#fff; vertical-align:middle; text-align:center;">
                    <?php echo ($ville['Id'] ?? ''); ?> - <?php echo htmlspecialchars($ville['NomVille'] ?? 'Ville #' . $ville['IdVille']); ?>
                </td>
                <td style="width:80px; text-align:center; vertical-align:middle;">
                    <button class="btn btn-sm btn-start" data-id="<?php echo ($ville['Id'] ?? ''); ?>"
                            title="Start" style="background:transparent; border:none;">
                        <img src="/assets/img/start.png" style="height:30px;">
                    </button>
                </td>
                <td style="width:80px;">
                    <input type="time" class="form-control input-sm ville-field"
                           data-id="<?php echo ($ville['Id'] ?? ''); ?>" data-champ="HreDeb"
                           value="<?php echo ! empty($ville['HreDeb']) ? substr($ville['HreDeb'], 0, 5) : ''; ?>"
                           style="background:#ff0; font-weight:bold;">
                </td>
                <td style="width:80px; text-align:center; vertical-align:middle;">
                    <button class="btn btn-sm btn-stop" data-id="<?php echo ($ville['Id'] ?? ''); ?>"
                            title="Stop" style="background:transparent; border:none;">
                        <img src="/assets/img/stop.png" style="height:30px;">
                    </button>
                </td>
                <td style="width:80px;">
                    <input type="time" class="form-control input-sm ville-field"
                           data-id="<?php echo ($ville['Id'] ?? ''); ?>" data-champ="HreFin"
                           value="<?php echo ! empty($ville['HreFin']) ? substr($ville['HreFin'], 0, 5) : ''; ?>"
                           style="background:#ff0; font-weight:bold;">
                </td>
                <td style="width:80px; text-align:center; vertical-align:middle;">Bacs</td>
                <td style="width:80px;">
                    <input type="number" class="form-control input-sm ville-field"
                           data-id="<?php echo ($ville['Id'] ?? ''); ?>" data-champ="nbBac"
                           value="<?php echo ($ville['nbBac'] ?? '') ?? '0'; ?>" min="0"
                           style="background:#ff0; font-weight:bold; width:70px;">
                </td>
            </tr>
            <!-- ── Ligne(s) DÉPOTOIRE ── -->
            <?php if ( ! empty($ville['depots'])): ?>
                <?php foreach (($ville['depots'] ?? []) as $dep): ?>
                <tr class="row-depot" data-iddepot="<?php echo ($dep['Id'] ?? ''); ?>" data-idville="<?php echo ($ville['Id'] ?? ''); ?>">
                    <!-- Col 0 : Picker dépotoire -->
                    <td style="width:200px;">
                        <select class="form-control input-sm depot-picker"
                                data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-idville="<?php echo ($ville['Id'] ?? ''); ?>">
                            <option value="">-- Dépotoire --</option>
                            <?php foreach (($depotoires ?? []) as $d): ?>
                                <option value="<?php echo htmlspecialchars($d['Nom']); ?>"
                                    <?php if (($dep['NomDepotoire'] ?? '') == $d['Nom']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($d['Nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <!-- Col 1 : Label H/deb -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">H/deb</td>
                    <!-- Col 2 : TimePicker debut -->
                    <td style="width:80px;">
                        <input type="time" class="form-control input-sm depot-field"
                               data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-champ="debut"
                               data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                               value="<?php echo ! empty($dep['HreDeb']) ? substr($dep['HreDeb'], 0, 5) : ''; ?>"
                               style="background:#ff0; font-weight:bold;">
                    </td>
                    <!-- Col 3 : Label H/fin -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">H/fin</td>
                    <!-- Col 4 : TimePicker fin -->
                    <td style="width:80px;">
                        <input type="time" class="form-control input-sm depot-field"
                               data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-champ="fin"
                               data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                               value="<?php echo ! empty($dep['HreFin']) ? substr($dep['HreFin'], 0, 5) : ''; ?>"
                               style="background:#ff0; font-weight:bold;">
                    </td>
                    <!-- Col 5 : Label Ton. -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">Ton.</td>
                    <!-- Col 6 : Entry Tonnage -->
                    <td style="width:80px;">
                        <input type="number" step="0.01" class="form-control input-sm depot-field tonnage-field"
                               data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-champ="Tonnage"
                               data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                               value="<?php echo ($dep['Tonnage'] ?? '') ?? ''; ?>"
                               style="background:#ff0; font-weight:bold; width:75px;">
                    </td>
                    <!-- Col 7 : Scanner -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">
                        <?php if ( ! empty($dep['Ticket'])): ?>
                            <?php $isPdf = (strtolower(pathinfo($dep['Ticket'], PATHINFO_EXTENSION)) === 'pdf'); ?>
                            <a href="<?php echo htmlspecialchars($dep['Ticket']); ?>" target="_blank"
                               class="btn btn-xs btn-success" title="Voir ticket<?php echo ($isPdf ?? '') ? ' PDF' : ''; ?>">
                                <i class="fa <?php echo ($isPdf ?? '') ? 'fa-file-pdf-o' : 'fa-file-image-o'; ?>"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-xs btn-default btn-upload-ticket"
                                    data-iddepot="<?php echo ($dep['Id'] ?? ''); ?>"
                                    data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                                    title="Scanner / Photo">
                                <i class="fa fa-camera"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                    <!-- Col 8 : vide (séparateur) -->
                    <td style="width:45px;"></td>
                    <!-- Col 9 : Label Boîte -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">Boîte</td>
                    <!-- Col 10 : Entry Boîte -->
                    <td style="width:80px;">
                        <input type="text" class="form-control input-sm depot-field"
                               data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-champ="boite"
                               data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                               value="<?php echo htmlspecialchars($dep['Boite'] ?? ''); ?>"
                               style="background:#ff0; font-weight:bold; width:75px;">
                    </td>
                    <!-- Col 11 : Label TrBo -->
                    <td style="width:45px; text-align:center; vertical-align:middle; font-size:12px;">TrBo</td>
                    <!-- Col 12 : CheckBox IsTransBo -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">
                        <input type="checkbox" class="depot-check"
                               data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-champ="IsTransBo"
                               data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                               <?php if ( ! empty($dep['IsTransbo'])) echo 'checked'; ?>>
                    </td>
                    <!-- Col 13 : Label Mat. -->
                    <td style="width:45px; text-align:center; vertical-align:middle; font-size:12px;">Mat.</td>
                    <!-- Col 14 : CheckBox IsAvecMat -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">
                        <input type="checkbox" class="depot-check"
                               data-id="<?php echo ($dep['Id'] ?? ''); ?>" data-champ="IsAvecMat"
                               data-idville="<?php echo ($ville['Id'] ?? ''); ?>"
                               <?php if ( ! empty($dep['IsAvecMat'])) echo 'checked'; ?>>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- ── Ligne séparation ── -->
            <tr class="row-separator"><td colspan="15" style="height:8px; background:#ecf0f5; border:none;"></td></tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="15" class="text-center text-muted" style="padding:20px;">
                <i class="fa fa-info-circle"></i> Aucune ville associée à cette cédule.
            </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Input file caché pour upload photo -->
<input type="file" id="fileTicketInput" accept="image/*" capture="environment" style="display:none;">

<?php
$content = ob_get_clean();

// Données JSON pour JS
$jsCedules    = json_encode($cedules);
$jsDepotoires = json_encode($depotoires);
$jsActiveCed  = (int) $activeCedule;
$jsCurrentCed = $currentCedule ? json_encode($currentCedule) : 'null';

$foot_extra = <<<SCRIPT
<script>
$(function(){

    // ═══════════════════════════════════════════
    // Données injectées depuis PHP
    // ═══════════════════════════════════════════
    var cedules       = {$jsCedules};
    var depotoires    = {$jsDepotoires};
    var activeCedule  = {$jsActiveCed};
    var currentCedule = {$jsCurrentCed};

    // ═══════════════════════════════════════════
    // Flash visuel de confirmation
    // ═══════════════════════════════════════════
    function flashOk(el) {
        var \$el = $(el);
        \$el.css('background-color', '#d5f5e3');
        setTimeout(function(){ \$el.css('background-color', '#ff0'); }, 1200);
    }

    // ═══════════════════════════════════════════
    // BOUTONS CÉDULES (OnButtonVilleClicked)
    // ═══════════════════════════════════════════
    $(document).on('click', '.btn-cedule-ville', function(){
        var idx = $(this).data('index');
        // Changer la cédule active via AJAX, puis recharger
        $.post('/web/api_change_cedule', { index: idx }, function(resp){
            if (resp.ok) {
                window.location.reload();
            }
        }, 'json');
    });

    // ═══════════════════════════════════════════
    // Helper : vérifie si le dernier ramassage est complet
    // (ShouldCreateNewRamassage dans VillesView.xaml.cs)
    // ═══════════════════════════════════════════
    function shouldCreateNewRamassage() {
        var rows = $('#tbodyVilles .row-ville');
        if (rows.length === 0) return false;
        var lastRow = rows.last();
        var hreDeb = lastRow.find('.ville-field[data-champ="HreDeb"]').val();
        var hreFin = lastRow.find('.ville-field[data-champ="HreFin"]').val();
        return (hreDeb && hreDeb !== '' && hreFin && hreFin !== '');
    }

    // ═══════════════════════════════════════════
    // Helper : ajouter une nouvelle ville via AJAX (AddDepotToVilleAsync)
    // Retourne un Deferred/Promise
    // ═══════════════════════════════════════════
    function addVilleAsync() {
        var d = $.Deferred();
        if (!currentCedule) { d.reject(); return d.promise(); }
        $.post('/web/api_add_ville', {
            idville:  currentCedule.IdVilles,
            idcedule: currentCedule.Id
        }, function(resp){
            if (resp.ok) d.resolve(resp.ville);
            else         d.reject();
        }, 'json').fail(function(){ d.reject(); });
        return d.promise();
    }

    // ═══════════════════════════════════════════
    // Helper : insérer le HTML d'une nouvelle ville+dépôt dans la grille
    // (reproduit AddElementsToGridVille + AddElementsToGridDepotoire1)
    // ═══════════════════════════════════════════
    function appendVilleRow(ville) {
        var dep = ville.depot || {};
        var depId    = dep.Id || '';
        var villeId  = ville.Id;
        var nomVille = ville.NomVille || ('Ville #' + ville.IdVille);

        // Options dépotoires
        var optionsHtml = '<option value="">-- Dépotoire --</option>';
        $.each(depotoires, function(i, d){
            optionsHtml += '<option value="' + d.Nom + '">' + d.Nom + '</option>';
        });

        var villeRow = '<tr class="row-ville" data-idville="' + villeId + '">'
            + '<td style="width:200px; background:#fff; vertical-align:middle; text-align:center;">' + villeId + ' - ' + nomVille + '</td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">'
            +   '<button class="btn btn-sm btn-start" data-id="' + villeId + '" title="Start" style="background:transparent; border:none;">'
            +     '<img src="/assets/img/start.png" style="height:30px;"></button></td>'
            + '<td style="width:80px;"><input type="time" class="form-control input-sm ville-field" data-id="' + villeId + '" data-champ="HreDeb" value="" style="background:#ff0; font-weight:bold;"></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">'
            +   '<button class="btn btn-sm btn-stop" data-id="' + villeId + '" title="Stop" style="background:transparent; border:none;">'
            +     '<img src="/assets/img/stop.png" style="height:30px;"></button></td>'
            + '<td style="width:80px;"><input type="time" class="form-control input-sm ville-field" data-id="' + villeId + '" data-champ="HreFin" value="" style="background:#ff0; font-weight:bold;"></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">Bacs</td>'
            + '<td style="width:80px;"><input type="number" class="form-control input-sm ville-field" data-id="' + villeId + '" data-champ="nbBac" value="0" min="0" style="background:#ff0; font-weight:bold; width:70px;"></td>'
            + '</tr>';

        var depotRow = '<tr class="row-depot" data-iddepot="' + depId + '" data-idville="' + villeId + '">'
            + '<td style="width:200px;"><select class="form-control input-sm depot-picker" data-id="' + depId + '" data-idville="' + villeId + '">' + optionsHtml + '</select></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">H/deb</td>'
            + '<td style="width:80px;"><input type="time" class="form-control input-sm depot-field" data-id="' + depId + '" data-champ="debut" data-idville="' + villeId + '" value="" style="background:#ff0; font-weight:bold;"></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">H/fin</td>'
            + '<td style="width:80px;"><input type="time" class="form-control input-sm depot-field" data-id="' + depId + '" data-champ="fin" data-idville="' + villeId + '" value="" style="background:#ff0; font-weight:bold;"></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">Ton.</td>'
            + '<td style="width:80px;"><input type="number" step="0.01" class="form-control input-sm depot-field tonnage-field" data-id="' + depId + '" data-champ="Tonnage" data-idville="' + villeId + '" value="" style="background:#ff0; font-weight:bold; width:75px;"></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">'
            +   '<button class="btn btn-xs btn-default btn-upload-ticket" data-iddepot="' + depId + '" data-idville="' + villeId + '" title="Scanner / Photo"><i class="fa fa-camera"></i></button></td>'
            + '<td style="width:45px;"></td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;">Boîte</td>'
            + '<td style="width:80px;"><input type="text" class="form-control input-sm depot-field" data-id="' + depId + '" data-champ="boite" data-idville="' + villeId + '" value="" style="background:#ff0; font-weight:bold; width:75px;"></td>'
            + '<td style="width:45px; text-align:center; vertical-align:middle; font-size:12px;">TrBo</td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;"><input type="checkbox" class="depot-check" data-id="' + depId + '" data-champ="IsTransBo" data-idville="' + villeId + '"></td>'
            + '<td style="width:45px; text-align:center; vertical-align:middle; font-size:12px;">Mat.</td>'
            + '<td style="width:80px; text-align:center; vertical-align:middle;"><input type="checkbox" class="depot-check" data-id="' + depId + '" data-champ="IsAvecMat" data-idville="' + villeId + '"></td>'
            + '</tr>';

        var sepRow = '<tr class="row-separator"><td colspan="15" style="height:8px; background:#ecf0f5; border:none;"></td></tr>';

        // Enlever le message "Aucune ville" s'il existe
        $('#tbodyVilles tr td.text-muted').closest('tr').remove();

        $('#tbodyVilles').append(villeRow + depotRow + sepRow);

        return villeId;
    }

    // ═══════════════════════════════════════════
    // START RAMASSAGE (OnStartClicked)
    // Reproduit : EnsureNewVilleBeforeStartAsync + OnStartClicked
    // ═══════════════════════════════════════════
    $(document).on('click', '.btn-start', function(){
        var btnStart = $(this);
        var idVille  = btnStart.data('id');
        var row      = btnStart.closest('tr');
        var now      = new Date();
        var hh       = ('0' + now.getHours()).slice(-2);
        var mm       = ('0' + now.getMinutes()).slice(-2);
        var timeVal  = hh + ':' + mm;

        // EnsureNewVilleBeforeStartAsync : si dernier ramassage complet → créer nouveau
        function doStart(targetIdVille, targetRow) {
            targetRow.find('.ville-field[data-champ="HreDeb"]').val(timeVal);
            $.post('/web/api_post_ville', {
                id:    targetIdVille,
                champ: 'HreDeb',
                value: timeVal
            }, function(resp){
                if (resp.ok) {
                    targetRow.find('.btn-start').css('background-color', '#e74c3c');
                    alert('Votre ramassage commence');
                }
            }, 'json');
        }

        if (shouldCreateNewRamassage()) {
            // Le dernier ramassage a HreDeb + HreFin → créer nouvelle ville
            addVilleAsync().done(function(newVille){
                var newVilleId = appendVilleRow(newVille);
                var newRow = $('#tbodyVilles .row-ville[data-idville="' + newVilleId + '"]');
                doStart(newVilleId, newRow);
            }).fail(function(){
                // Fallback : démarrer sur la ligne actuelle
                doStart(idVille, row);
            });
        } else {
            doStart(idVille, row);
        }
    });

    // ═══════════════════════════════════════════
    // STOP RAMASSAGE (OnStopClicked)
    // ═══════════════════════════════════════════
    $(document).on('click', '.btn-stop', function(){
        var idVille = $(this).data('id');
        var row     = $(this).closest('tr');
        var now     = new Date();
        var hh      = ('0' + now.getHours()).slice(-2);
        var mm      = ('0' + now.getMinutes()).slice(-2);
        var timeVal = hh + ':' + mm;

        row.find('.ville-field[data-champ="HreFin"]').val(timeVal);

        $.post('/web/api_post_ville', {
            id:    idVille,
            champ: 'HreFin',
            value: timeVal
        }, function(resp){
            if (resp.ok) {
                row.find('.btn-stop').css('background-color', '#e74c3c');
                alert('Votre ramassage est terminé.');
            }
        }, 'json');
    });

    // ═══════════════════════════════════════════
    // AUTO-SAVE : champs ville (OnVilleTimeChanged / OnVilleEntryChanged)
    // ═══════════════════════════════════════════
    $(document).on('change', '.ville-field', function(){
        var \$el = $(this);
        $.post('/web/api_post_ville', {
            id:    \$el.data('id'),
            champ: \$el.data('champ'),
            value: \$el.val()
        }, function(resp){
            if (resp.ok) flashOk(\$el);
        }, 'json');
    });

    // ═══════════════════════════════════════════
    // AUTO-SAVE : Picker dépotoire (OnDepotoireSelectedIndexChanged)
    // ═══════════════════════════════════════════
    $(document).on('change', '.depot-picker', function(){
        var \$el = $(this);
        $.post('/web/api_post_depotoire', {
            id:        \$el.data('id'),
            depotoire: \$el.val()
        }, function(resp){
            if (resp.ok) flashOk(\$el);
        }, 'json');
    });

    // ═══════════════════════════════════════════
    // AUTO-SAVE : champs dépotoire (OnDepotTimeChanged / OnDepotEntryChanged)
    // ═══════════════════════════════════════════
    $(document).on('change', '.depot-field', function(){
        var \$el = $(this);
        $.post('/web/api_post_depot', {
            id:    \$el.data('id'),
            champ: \$el.data('champ'),
            value: \$el.val()
        }, function(resp){
            if (resp.ok) flashOk(\$el);
        }, 'json');
    });

    // ═══════════════════════════════════════════
    // AUTO-SAVE : Checkboxes dépotoire (OnDepotCheckedChanged)
    // ═══════════════════════════════════════════
    $(document).on('change', '.depot-check', function(){
        var \$el = $(this);
        $.post('/web/api_post_depot', {
            id:    \$el.data('id'),
            champ: \$el.data('champ'),
            value: \$el.is(':checked') ? 1 : 0
        }, function(resp){
            if (resp.ok) flashOk(\$el);
        }, 'json');
    });

    // ═══════════════════════════════════════════
    // AJOUTER UN RAMASSAGE (OnAddDepotToVille)
    // ═══════════════════════════════════════════
    $('#btnAddRamassage').on('click', function(){
        if (!currentCedule) {
            alert('Aucune cédule active.');
            return;
        }
        var btn = $(this);
        btn.prop('disabled', true).text('Ajout...');

        addVilleAsync().done(function(newVille){
            btn.prop('disabled', false).text('Ajouter un ramassage');
            appendVilleRow(newVille);
        }).fail(function(){
            btn.prop('disabled', false).text('Ajouter un ramassage');
            alert('Erreur lors de l\'ajout.');
        });
    });

    // ═══════════════════════════════════════════
    // UPLOAD PHOTO TICKET (OnTakePhotoButtonClicked)
    // ═══════════════════════════════════════════
    var uploadDepotId  = '';
    var uploadVilleId  = '';
    var uploadBtnRef   = null;

    $(document).on('click', '.btn-upload-ticket', function(){
        uploadDepotId = $(this).data('iddepot');
        uploadVilleId = $(this).data('idville');
        uploadBtnRef  = $(this);
        
        // Vérifier si le tonnage a une valeur avant de permettre l'upload
        var tonnageField = $(this).closest('tr').find('.tonnage-field');
        var tonnageValue = tonnageField.val();
        
        if (!tonnageValue || tonnageValue === '' || parseFloat(tonnageValue) <= 0) {
            alert('Veuillez entrer le tonnage avant de scanner le billet.');
            tonnageField.focus();
            return;
        }
        
        $('#fileTicketInput').trigger('click');
    });

    $('#fileTicketInput').on('change', function(){
        if (!this.files || !this.files[0]) return;
        var fd = new FormData();
        fd.append('file', this.files[0]);
        fd.append('referenceId', uploadDepotId);
        fd.append('villeId', uploadVilleId);

        if (uploadBtnRef) {
            uploadBtnRef.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        }

        $.ajax({
            url: '/web/api_upload_ticket',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(resp){
                if (resp.ok && uploadBtnRef) {
                    var isPdf = (resp.ticket || '').toLowerCase().indexOf('.pdf') !== -1;
                    var icon  = isPdf ? 'fa-file-pdf-o' : 'fa-file-image-o';
                    uploadBtnRef.replaceWith(
                        '<a href="' + resp.ticket + '" target="_blank" class="btn btn-xs btn-success" title="Voir ticket">' +
                        '<i class="fa ' + icon + '"></i></a>'
                    );
                }
            },
            error: function(xhr){
                if (uploadBtnRef) {
                    uploadBtnRef.prop('disabled', false).html('<i class="fa fa-camera"></i>');
                }
                alert('Erreur lors de l\'envoi du billet. Veuillez réessayer.');
            }
        });

        // Reset pour permettre un second upload
        $(this).val('');
    });
});
</script>
SCRIPT;

// Auto-scroll vers la derniere ville (utile en mode embed apres Stop sur truckmap)
$autoScrollScript = <<<SCROLL
<script>
$(function(){
    setTimeout(function(){
        var rows = document.querySelectorAll('#tbodyVilles .row-ville');
        if (rows.length > 0) {
            var last = rows[rows.length - 1];
            try {
                last.scrollIntoView({ behavior: 'smooth', block: 'center' });
                last.style.transition = 'background 0.4s';
                var firstCell = last.querySelector('td');
                if (firstCell) {
                    var orig = firstCell.style.background;
                    firstCell.style.background = '#fff3cd';
                    setTimeout(function(){ firstCell.style.background = orig; }, 1800);
                }
            } catch(e){}
        }
    }, 250);
});
</script>
SCROLL;

$foot_extra = $foot_extra . $autoScrollScript;

$embedMode = isset($embed) && $embed;
if (empty($embedMode)) {
    echo view('web_chauffeur/layout', array(
        'content'    => $content,
        'page_id'    => $page_id,
        'page_title' => $page_title,
        'nom'        => $nom,
        'isAdmin'    => isset($isAdmin) ? $isAdmin : false,
        'head_extra' => '',
        'foot_extra' => $foot_extra,
    ));
} else {
    // Mode embed : sortie standalone pour WebView MAUI
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo '<title>' . htmlspecialchars($page_title) . '</title>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
    echo '<link rel="stylesheet" href="/assets/css/web_chauffeur.css">';
    echo '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>';
    echo '<style>body{padding:8px;background:#ecf0f5;}.btn-cedule-ville{margin:3px;}</style>';
    echo '</head><body>';
    // Bouton retour vers la carte
    echo '<div style="margin-bottom:10px;text-align:left;">'
        . '<a href="/web/truckmap_embed?embed=1" class="btn btn-secondary btn-sm">'
        . '<i class="fa fa-arrow-left"></i> Retour a la carte</a></div>';
    echo ($content ?? '');
    echo ($foot_extra ?? '');
    echo '</body></html>';
}
?>
