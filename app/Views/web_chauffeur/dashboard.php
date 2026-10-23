<?php

$page_id    = 'dashboard';
$page_title = 'CAMION / RH';

// Données PHP → JS
$camId      = isset($camion['id'])      ? $camion['id']      : '';
$camKiloDeb = isset($camion['KiloDeb']) ? $camion['KiloDeb'] : '';
$camKiloFin = isset($camion['KiloFin']) ? $camion['KiloFin'] : '';
$camFuel    = isset($camion['Fuel'])    ? $camion['Fuel']    : '';
$camHreDeb  = isset($camion['HreDeb'])  ? substr($camion['HreDeb'],0,5) : '00:00';
$camHreFin  = isset($camion['HreFin'])  ? substr($camion['HreFin'],0,5) : '00:00';
$broker     = !empty($isBroker);

$chaufId        = isset($chauffeurRH['id'])          ? $chauffeurRH['id']          : '';
$chaufNom       = isset($chauffeurRH['Nom'])         ? $chauffeurRH['Nom']         : '';
$chaufHreDeb    = isset($chauffeurRH['HreDeb'])      ? substr($chauffeurRH['HreDeb'],0,5)      : '00:00';
$chaufHreFin    = isset($chauffeurRH['HreFin'])      ? substr($chauffeurRH['HreFin'],0,5)      : '00:00';
$chaufHreDebEx  = isset($chauffeurRH['HreDeb_extra'])? substr($chauffeurRH['HreDeb_extra'],0,5) : '00:00';
$chaufHreFinEx  = isset($chauffeurRH['HreFin_extra'])? substr($chauffeurRH['HreFin_extra'],0,5) : '00:00';
$chaufTypeExtra = isset($chauffeurRH['TypeExtra'])   ? $chauffeurRH['TypeExtra']   : '';

$ceduleIdsJson = json_encode(array_map(function($c){ return $c['Id']; }, $cedules));
$oldCamion     = htmlspecialchars($nocamion);
$oldChauffeurId = isset($currentCedule['IdChauffeur']) ? $currentCedule['IdChauffeur'] : '';

ob_start();
?>

<!-- ═══ Badges cédules ═══ -->
<?php if ( ! empty($cedules)): ?>
<div class="row" style="margin-bottom:10px;">
    <div class="col-xs-12">
        <?php foreach (($cedules ?? []) as $i => $ced): ?>
            <a href="/web/changeCedule/<?php echo ($i ?? ''); ?>"
               class="badge-cedule"
               style="display:inline-block;padding:6px 14px;margin:3px;border-radius:4px;text-decoration:none;font-weight:bold;
                      background:<?php echo !empty($ced['BackCouleurDechet']) ? $ced['BackCouleurDechet'] : '#3498db'; ?>;
                      color:<?php echo !empty($ced['TextColorDechet']) ? $ced['TextColorDechet'] : '#fff'; ?>;
                      <?php if ($i == $activeCedule) echo 'outline:3px solid #f39c12;'; ?>">
                <?php echo htmlspecialchars($ced['NoCamion'] . ' – ' . ($ced['NomVille'] ?? '')); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ═══ Grid 3 colonnes: Camion | Chauffeur | Aide ═══ -->
<!-- Reproduit exactement SaniView.xaml grdInfocam -->
<div class="row" id="grdInfocam" style="margin:0 10px;">

    <!-- ═══ SECTION CAMION (colonne 0, Width="2*") ═══ -->
    <div class="col-md-5" style="padding:0 8px;">
        <div class="sani-section" style="border:2px solid #000; border-radius:8px; padding:6px;">
            <div style="border:1px solid #d3d3d3; border-radius:8px; padding:6px;">

                <div class="sani-header" style="background:DarkBlue; color:Orange; padding:10px; font-size:18px; font-weight:bold;">
                    INFORMATIONS DU CAMION
                </div>

                <div style="text-align:center; margin:10px 0;">
                    <span id="lblCam" style="font-size:28px; color:DarkBlue; font-weight:bold;">
                        CAM: <?php echo htmlspecialchars($nocamion); ?>
                    </span>
                </div>
                <?php if (empty($broker)): ?>
                <div style="text-align:center; margin-bottom:10px;">
                    <button type="button" class="btn btn-default" id="btnChgCamion" style="background:transparent; border:none;">
                        <img src="/assets/img/fleche.jpg" style="width:60px; height:60px;" alt="Changer" />
                    </button>
                </div>

                <!-- KM/DEBUT -->
                <div class="form-group row sani-field">
                    <label class="col-xs-5 control-label" style="font-size:22px; font-weight:bold;">KM/DEBUT</label>
                    <div class="col-xs-7">
                        <input type="number" step="0.01" class="form-control sani-input" id="KiloDeb"
                               value="<?php echo htmlspecialchars($camKiloDeb); ?>" data-field="KiloDeb" />
                    </div>
                </div>
                <!-- KM/FIN -->
                <div class="form-group row sani-field">
                    <label class="col-xs-5 control-label" style="font-size:22px; font-weight:bold;">KM/FIN</label>
                    <div class="col-xs-7">
                        <input type="number" step="0.01" class="form-control sani-input" id="KiloFin"
                               value="<?php echo htmlspecialchars($camKiloFin); ?>" data-field="KiloFin" />
                    </div>
                </div>
                <!-- FUEL -->
                <div class="form-group row sani-field">
                    <label class="col-xs-5 control-label">
                        <img src="/assets/img/fuel.png" style="width:100px; height:60px;" alt="Fuel" />
                    </label>
                    <div class="col-xs-7">
                        <input type="number" step="0.01" class="form-control sani-input" id="Fuel"
                               value="<?php echo htmlspecialchars($camFuel); ?>" data-field="Fuel" />
                    </div>
                </div>
                <?php endif; ?>
                <!-- HEURES / DEBUT -->
                <div class="form-group row sani-field">
                    <label class="col-xs-5 control-label" style="font-size:18px; font-weight:bold;">HEURES / DEBUT:</label>
                    <div class="col-xs-7">
                        <input type="time" class="form-control sani-input" id="HreDeb_cam"
                               value="<?php echo ($camHreDeb ?? ''); ?>" data-field="HreDeb_cam" />
                    </div>
                </div>
                <!-- HEURES / FIN -->
                <div class="form-group row sani-field">
                    <label class="col-xs-5 control-label" style="font-size:18px; font-weight:bold;">HEURES / FIN:</label>
                    <div class="col-xs-7">
                        <input type="time" class="form-control sani-input" id="HreFin_cam"
                               value="<?php echo ($camHreFin ?? ''); ?>" data-field="HreFin_cam" />
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══ SECTION CHAUFFEUR (colonne 1, Width="1.2*") ═══ -->
    <div class="col-md-3" style="padding:0 8px;">
        <div class="sani-section" style="border:2px solid #000; border-radius:8px; padding:6px;">
            <div style="border:1px solid #d3d3d3; border-radius:8px; padding:6px; text-align:center;">

                <div class="sani-header" style="background:DarkBlue; color:White; padding:6px; font-size:18px; font-weight:bold;">
                    INFO DU CHAUFFEUR
                </div>

                <div style="margin:10px 0;">
                    <span id="lblChauffeur" style="font-size:20px; color:DarkBlue; font-weight:bold;">
                        <?php echo htmlspecialchars($chaufNom); ?>
                    </span>
                </div>
                <div style="margin-bottom:10px;">
                    <button type="button" class="btn btn-default" id="btnChgChauf" style="background:transparent; border:none;">
                        <img src="/assets/img/fleche.jpg" style="width:60px; height:60px;" alt="Changer" />
                    </button>
                </div>

                <!-- Bloc HEURES -->
                <div style="border:2px solid #000; border-radius:8px; padding:8px; margin-bottom:8px;">
                    <div style="font-size:26px; color:DarkBlue; font-weight:bold;">HEURES</div>
                    <div class="form-group sani-field" style="margin-top:8px;">
                        <label style="font-size:22px; font-weight:bold;">DEBUT</label>
                        <input type="time" class="form-control sani-input" id="HreDeb_chauf"
                               value="<?php echo ($chaufHreDeb ?? ''); ?>" style="background:Yellow; display:inline-block; width:auto;" />
                    </div>
                    <div class="form-group sani-field">
                        <label style="font-size:22px; font-weight:bold;">FIN</label>
                        <input type="time" class="form-control sani-input" id="HreFin_chauf"
                               value="<?php echo ($chaufHreFin ?? ''); ?>" style="background:Yellow; display:inline-block; width:auto;" />
                    </div>
                </div>

                <!-- Bloc EXTRA -->
                <?php if (empty($broker)): ?>
                <div style="border:2px solid #000; border-radius:8px; padding:8px;">
                    <div style="font-size:26px; color:Red; font-weight:bold;">EXTRA</div>
                    <div class="form-group sani-field" style="margin-top:8px;">
                        <select class="form-control sani-input" id="selExtra" style="font-size:18px; font-weight:bold;">
                            <option value="">CHOISIR TYPE</option>
                            <?php foreach (($extras ?? []) as $ext): ?>
                                <option value="<?php echo htmlspecialchars($ext); ?>"
                                    <?php if ($ext == $chaufTypeExtra) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($ext); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group sani-field">
                        <label style="font-size:22px; font-weight:bold;">DEBUT</label>
                        <input type="time" class="form-control sani-input" id="HreDeb_extra"
                               value="<?php echo ($chaufHreDebEx ?? ''); ?>" style="background:Yellow; display:inline-block; width:auto;" />
                    </div>
                    <div class="form-group sani-field">
                        <label style="font-size:22px; font-weight:bold;">FIN</label>
                        <input type="time" class="form-control sani-input" id="HreFin_extra"
                               value="<?php echo ($chaufHreFinEx ?? ''); ?>" style="background:Yellow; display:inline-block; width:auto;" />
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ═══ SECTION AIDE (colonne 2, Width="1.2*") ═══ -->
    <?php if ( ! empty($aides)): ?>
    <div class="col-md-4" id="sectionAide" style="padding:0 8px;">
        <div class="sani-section" style="border:2px solid #000; border-radius:8px; padding:6px;">
            <div style="border:1px solid #d3d3d3; border-radius:8px; padding:6px;">

                <div class="sani-header" style="background:Orange; padding:6px; font-size:18px; font-weight:bold;">
                    INFORMATION AIDE
                </div>

                <?php foreach (($aides ?? []) as $idx => $aide): ?>
                <div class="aide-frame" style="border:1px solid Orange; border-radius:8px; padding:6px; margin-top:8px; text-align:center;"
                     data-aide-id="<?php echo htmlspecialchars($aide['id']); ?>" data-aide-idx="<?php echo ($idx ?? ''); ?>">
                    <div style="font-size:18px; color:DarkBlue; font-weight:bold;">
                        <?php echo htmlspecialchars($aide['Nom'] ?? 'Aide ' . ($idx+1)); ?>
                    </div>
                    <div class="form-group sani-field" style="margin-top:6px;">
                        <label style="font-size:22px; font-weight:bold;">DEBUT:</label>
                        <input type="time" class="form-control aide-time" data-aide-id="<?php echo htmlspecialchars($aide['id']); ?>" data-field="HreDeb"
                               value="<?php echo substr($aide['HreDeb'] ?? '00:00', 0, 5); ?>"
                               style="background:Yellow; display:inline-block; width:auto;" />
                    </div>
                    <div class="form-group sani-field">
                        <label style="font-size:22px; font-weight:bold;">FIN:</label>
                        <input type="time" class="form-control aide-time" data-aide-id="<?php echo htmlspecialchars($aide['id']); ?>" data-field="HreFin"
                               value="<?php echo substr($aide['HreFin'] ?? '00:00', 0, 5); ?>"
                               style="background:Yellow; display:inline-block; width:auto;" />
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();

$head_extra = '<style>
    .sani-input { background: Yellow; font-size: 22px; }
    .sani-field { margin-bottom: 8px; }
    .sani-section { margin-bottom: 12px; }
    /* Modal popup camion/chauffeur */
    .sani-modal-list { max-height: 400px; overflow-y: auto; }
    .sani-modal-list .list-group-item { font-size: 18px; cursor: pointer; }
    .sani-modal-list .list-group-item:hover { background: #e8f5e9; }
</style>';

$foot_extra = '
<script>
$(function(){
    var camId  = "' . addslashes($camId) . '";
    var chaufId = "' . addslashes($chaufId) . '";
    var ceduleIds = ' . $ceduleIdsJson . ';
    var oldCamion = "' . addslashes($oldCamion) . '";
    var oldChauffeurId = "' . addslashes($oldChauffeurId) . '";

    // ─── AUTO-SAVE CAMION (OnButtonCamClicked → SendCamInfoToServer) ───
    function saveCamion(){
        $.post("/web/api_post_camion", {
            id: camId,
            KiloDeb: $("#KiloDeb").val(),
            KiloFin: $("#KiloFin").val(),
            Fuel:    $("#Fuel").val(),
            HreDeb:  $("#HreDeb_cam").val(),
            HreFin:  $("#HreFin_cam").val()
        });
    }
    $("#KiloDeb, #KiloFin, #Fuel").on("change blur", saveCamion);
    $("#HreDeb_cam, #HreFin_cam").on("change", saveCamion);

    // ─── AUTO-SAVE CHAUFFEUR (OnButtonChaufClicked → SendChaufInfoToServer) ───
    function saveChauffeur(){
        $.post("/web/api_post_chauffeur", {
            id: chaufId,
            HreDeb:      $("#HreDeb_chauf").val(),
            HreFin:      $("#HreFin_chauf").val(),
            HreDeb_extra: $("#HreDeb_extra").val(),
            HreFin_extra: $("#HreFin_extra").val(),
            TypeExtra:    $("#selExtra").val()
        });
    }
    $("#HreDeb_chauf, #HreFin_chauf, #HreDeb_extra, #HreFin_extra, #selExtra").on("change", saveChauffeur);

    // ─── AUTO-SAVE AIDE (OnButtonAideClicked → SendAideInfoToServer) ───
    $(".aide-time").on("change", function(){
        var $frame = $(this).closest(".aide-frame");
        var aideId = $(this).data("aide-id");
        var hd = $frame.find("[data-field=HreDeb]").val();
        var hf = $frame.find("[data-field=HreFin]").val();
        $.post("/web/api_post_aide", { id: aideId, HreDeb: hd, HreFin: hf });
    });

    // ─── POPUP CHANGER CAMION (OuvrirPopupCamion) ───
    $("#btnChgCamion").on("click", function(){
        // Validation (même que MAUI OuvrirPopupCamion)
        if($("#KiloDeb").val()==="0.00" || $("#KiloFin").val()==="0.00"
            || $("#HreDeb_cam").val()==="00:00" || $("#HreFin_cam").val()==="00:00"){
            alert("Veuillez remplir tous les champs avant de choisir un camion.\\n\\nPour le champ Fuel vous le remplissez seulement si vous pouvez.");
            return;
        }
        $.getJSON("/web/api_list_camions", function(d){
            if(!d.ok) return;
            var html = \'<div class="modal fade" id="camModal"><div class="modal-dialog"><div class="modal-content">\' +
                \'<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>\' +
                \'<h4>Choisir un camion</h4></div><div class="modal-body sani-modal-list"><div class="list-group">\';
            $.each(d.data, function(i,c){
                html += \'<a href="#" class="list-group-item cam-choice" data-cam="\'+c+\'">\'+c+\'</a>\';
            });
            html += \'</div></div></div></div></div>\';
            $("body").append(html);
            $("#camModal").modal("show");
            $(".cam-choice").on("click", function(e){
                e.preventDefault();
                var cam = $(this).data("cam");
                $("#camModal").modal("hide").on("hidden.bs.modal", function(){ $(this).remove(); });
                // ADD_ITEM_CEDULE pour chaque cédule
                var done = 0;
                $.each(ceduleIds, function(i, cid){
                    $.post("/web/api_change_camion", {idcedule: cid, camion: cam, oldcamion: oldCamion}, function(){
                        done++;
                        if(done >= ceduleIds.length) location.reload();
                    });
                });
            });
        });
    });

    // ─── POPUP CHANGER CHAUFFEUR (OuvrirPopupChauffeur) ───
    $("#btnChgChauf").on("click", function(){
        if($("#HreDeb_chauf").val()==="00:00" || $("#HreFin_chauf").val()==="00:00"){
            alert("Veuillez remplir tous les champs avant de choisir un chauffeur.\\n\\nPour les champs Extra vous les remplissez seulement si vous avez des Extras.");
            return;
        }
        $.getJSON("/web/api_list_chauffeurs", function(d){
            if(!d.ok) return;
            var html = \'<div class="modal fade" id="chaufModal"><div class="modal-dialog"><div class="modal-content">\' +
                \'<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>\' +
                \'<h4>Choisir un chauffeur</h4></div><div class="modal-body">\' +
                \'<div class="form-group"><label>Code usager</label><input type="text" class="form-control" id="chgCode" /></div>\' +
                \'<div class="form-group"><label>Mot de passe</label><input type="password" class="form-control" id="chgPass" /></div>\' +
                \'<div class="sani-modal-list"><div class="list-group">\';
            $.each(d.data, function(i,c){
                html += \'<a href="#" class="list-group-item chauf-choice" data-chauf="\'+c+\'">\'+c+\'</a>\';
            });
            html += \'</div></div></div></div></div></div>\';
            $("body").append(html);
            $("#chaufModal").modal("show");
            $(".chauf-choice").on("click", function(e){
                e.preventDefault();
                var ch = $(this).data("chauf");
                $("#chaufModal").modal("hide").on("hidden.bs.modal", function(){ $(this).remove(); });
                var done = 0;
                $.each(ceduleIds, function(i, cid){
                    $.post("/web/api_change_chauffeur", {idcedule: cid, chauffeur: ch, oldchauffeur: oldChauffeurId}, function(){
                        done++;
                        if(done >= ceduleIds.length) location.reload();
                    });
                });
            });
        });
    });
});
</script>';

echo view('web_chauffeur/layout', array(
    'content'    => $content,
    'page_id'    => $page_id,
    'page_title' => $page_title,
    'nom'        => $nom,
    'isAdmin'    => isset($isAdmin) ? $isAdmin : false,
    'head_extra' => $head_extra,
    'foot_extra' => $foot_extra,
));
?>
