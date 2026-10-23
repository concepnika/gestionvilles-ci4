<?php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <title>Tags de la ville</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
    <style>
        body { font-family: system-ui, Arial, sans-serif; }
        .tag-card { border-left: 6px solid #9e9e9e; }
        .status-PENDING { border-left-color: #ef6c00; }
        .status-ACK { border-left-color: #1976d2; }
        .status-DONE { border-left-color: #2e7d32; opacity:.85; }
        .status-REJECTED { border-left-color: #c62828; }
        .discussion-item { border-left: 3px solid #ddd; padding-left:8px; margin:6px 0; font-size: 13px; }
        .img-thumb { max-width: 220px; height:auto; border-radius:6px; cursor:pointer; box-shadow:0 1px 6px rgba(0,0,0,.25); }
        .small-muted { color:#777; font-size:12px; }
        .filters { position: sticky; top: 0; z-index: 10; background: #fff; padding:10px 0; border-bottom:1px solid #eee; }
    </style>
</head>
<body>
    <?php if (!empty($menu)) echo ($menu ?? ''); ?>

    <div class="container my-3">
        <h4 class="mb-3">Tags de la ville</h4>

        <div class="filters row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Ville</label>
                <input id="idville" class="form-control form-control-sm" type="number" value="<?php echo (int)($IdVille ?? 0); ?>" />
            </div>
            <div class="col-md-3">
                <label class="form-label">Date (optionnel)</label>
                <input id="dte" class="form-control form-control-sm" type="date" value="<?php echo htmlspecialchars($ActiveDate ?? date('Y-m-d'), ENT_QUOTES); ?>" />
            </div>
            <div class="col-md-3">
                <label class="form-label">Portée</label>
                <select id="scope" class="form-select form-select-sm">
                    <option value="open" selected>Tags actifs (statut ≠ DONE)</option>
                    <option value="date">Par date</option>
                    <option value="all">Tous</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button id="btnLoad" class="btn btn-primary btn-sm">Charger</button>
                <button id="btnReasons" class="btn btn-outline-secondary btn-sm">Raisons</button>
            </div>
            <div class="col-12 d-flex align-items-center gap-2 mt-2">
                <input id="search" type="text" class="form-control form-control-sm" placeholder="Recherche (adresse, raison, description, code)" />
                <span class="small-muted">Discussions repliées par défaut.</span>
            </div>
        </div>

        <div id="listHost" class="mt-3"></div>
    </div>

    <!-- Overlay image limité à la fenêtre -->
    <div id="imgOverlay" style="position:fixed;inset:0;background:rgba(0,0,0,.82);display:none;align-items:center;justify-content:center;z-index:1050;">
        <button id="ovClose" class="btn btn-sm btn-light" style="position:absolute;top:10px;right:14px;">X</button>
        <img id="ovImg" alt="Preview" style="max-width:92vw;max-height:92vh;border-radius:8px;border:4px solid #fff;box-shadow:0 6px 24px rgba(0,0,0,.5);" />
    </div>

    <script>
        const API_LIST = "<?php echo ci3_url('ville/maptag/list'); ?>";
        const API_RESPOND = "<?php echo ci3_url('ville/maptag/respond'); ?>";
        const API_UPDATE = "<?php echo ci3_url('ville/maptag/update'); ?>";
        const API_REASONS = "<?php echo ci3_url('ville/maptag/reasons'); ?>";
        const PHOTO_HOST = "https://robert-daoust.ca";

        function escapeHtml(v){ if(v==null) return ""; return String(v).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;").replace(/'/g,"&#39;"); }
        function statusLabel(st){ switch((st||"").toUpperCase()){ case "DONE": return "Terminé"; case "ACK": return "Accusé"; case "REJECTED": return "Rejeté"; default: return "En attente"; } }
        function buildPhotoUrl(rel){ if(!rel) return ""; rel=String(rel).replace(/^(\.\/|\/)+/,""); return PHOTO_HOST.replace(/\/+$/,"") + "/" + rel; }

        const overlay = document.getElementById("imgOverlay");
        const ovImg = document.getElementById("ovImg");
        document.getElementById("ovClose").onclick = () => { overlay.style.display="none"; ovImg.src=""; };
        overlay.addEventListener("click", e => { if(e.target === overlay){ overlay.style.display="none"; ovImg.src=""; } });

        function tagCardHtml(t){
            const status = String(t.status||"PENDING").toUpperCase();
            const img = t.image_path ? buildPhotoUrl(t.image_path) : "";
            const discussions = Array.isArray(t.discussions) ? t.discussions : [];
            const id = String(t.id);

            const header =
                '<div class="d-flex justify-content-between align-items-start">'
               + '  <div>'
               + '    <div><b>Code:</b> '+escapeHtml(t.tag_code||"")+'</div>'
               + (t.address_text ? ('    <div><b>Adresse:</b> '+escapeHtml(t.address_text)+'</div>') : '')
               + (t.reason ? ('    <div><b>Raison:</b> '+escapeHtml(t.reason)+'</div>') : '')
               + (t.description ? ('    <div><b>Description:</b> '+escapeHtml(t.description)+'</div>') : '')
               + '  </div>'
               + '  <div class="text-end">'
               + '    <div><b>Statut:</b> <span class="badge bg-secondary">'+escapeHtml(statusLabel(status))+'</span></div>'
               + '    <div class="mt-2 d-flex gap-1">'
               + '      <button class="btn btn-sm btn-info" data-action="ACK" data-id="'+id+'">Accuser</button>'
               + '      <button class="btn btn-sm btn-success" data-action="DONE" data-id="'+id+'">Terminer</button>'
               + '      <button class="btn btn-sm btn-danger" data-action="REJECTED" data-id="'+id+'">Rejeter</button>'
               + '    </div>'
               + '  </div>'
               + '</div>';

            const edit =
               '<details class="mt-2">'
               + '  <summary class="small-muted">Modifier le tag</summary>'
               + '  <div class="mt-2">'
               + '    <div class="mb-2"><label class="form-label">Adresse</label><input class="form-control form-control-sm" name="address_text" value="'+escapeHtml(t.address_text||"")+'"/></div>'
               + '    <div class="mb-2"><label class="form-label">Raison</label><input class="form-control form-control-sm" name="reason" value="'+escapeHtml(t.reason||"")+'"/></div>'
               + '    <div class="mb-2"><label class="form-label">Description</label><textarea class="form-control form-control-sm" name="description" rows="2">'+escapeHtml(t.description||"")+'</textarea></div>'
               + '    <div class="text-end"><button class="btn btn-sm btn-primary" data-action="SAVE_EDIT" data-id="'+id+'">Enregistrer</button></div>'
               + '  </div>'
               + '</details>';

            const imgHtml = img ? ('<div class="mt-2"><img class="img-thumb" src="'+escapeHtml(img)+'" alt="Image" data-full="'+escapeHtml(img)+'"/></div>') : '';

            const discHeader =
               '<div class="mt-2 d-flex justify-content-between align-items-center">'
               + '  <div><b>Discussions</b> <span class="small-muted">('+discussions.length+')</span></div>'
               + '  <button class="btn btn-sm btn-outline-secondary" data-action="TOGGLE_DISC" data-id="'+id+'">Afficher/Masquer</button>'
               + '</div>';

            const discList =
               '<div class="mt-2" data-disc="'+id+'" style="display:none;">'
               + (discussions.length > 0
                   ? discussions.map(r => {
                        const a = escapeHtml(r.author||"");
                        const d = escapeHtml(r.time||"");
                        const s = escapeHtml(statusLabel(r.status||""));
                        const txt = escapeHtml(r.text||"");
                        const meta = [a,d,s].filter(x => x && x !== "-").join(' · ');
                        return '<div class="discussion-item"><div>'+txt+'</div><div class="small-muted">'+meta+'</div></div>';
                     }).join("")
                   : '<div class="small-muted">Aucune réponse.</div>'
                 )
               + '</div>';

            const reply =
               '<div class="mt-2">'
               + '  <label class="form-label">Ajouter une réponse</label>'
               + '  <textarea class="form-control form-control-sm" rows="2" name="reply_'+id+'" placeholder="Votre réponse..."></textarea>'
               + '  <div class="text-end mt-2"><button class="btn btn-sm btn-success" data-action="POST_REPLY" data-id="'+id+'">Envoyer</button></div>'
               + '</div>';

            return '<div class="card tag-card status-'+status+' mb-3"><div class="card-body">'
                 + header
                 + imgHtml
                 + edit
                 + discHeader
                 + discList
                 + reply
                 + '</div></div>';
        }

        async function fetchTags(idville, scope, dte){
            let url = API_LIST + "?idville=" + encodeURIComponent(idville);
            if(scope === "open"){ url += "&open=1"; }
            else if(scope === "date" && dte){ url += "&dte=" + encodeURIComponent(dte); }
            const r = await fetch(url, { cache: "no-store" });
            const j = await r.json();
            if(!j || !j.ok || !Array.isArray(j.data)) return [];
            return j.data;
        }

        async function respondTag(id, status, responseText){
            const fd = new FormData();
            fd.append("id", id);
            fd.append("id_ville", document.getElementById("idville").value || "0");
            fd.append("status", status);
            if(responseText) fd.append("response_text", responseText);
            const r = await fetch(API_RESPOND, { method: "POST", body: fd });
            const j = await r.json();
            if(!j || !j.ok) throw new Error(j?.error || "Réponse non envoyée");
        }

        async function updateTag(id, addr, reason, desc){
            const fd = new FormData();
            fd.append("id", id);
            fd.append("id_ville", document.getElementById("idville").value || "0");
            fd.append("address_text", addr || "");
            fd.append("reason", reason || "");
            fd.append("description", desc || "");
            const r = await fetch(API_UPDATE, { method: "POST", body: fd });
            const j = await r.json();
            if(!j || !j.ok) throw new Error(j?.error || "Échec mise à jour");
        }

        async function loadReasons(){
            try {
                const r = await fetch(API_REASONS + "?_ts=" + Date.now());
                const j = await r.json();
                if(j && j.ok && Array.isArray(j.data)){
                    alert("Raisons:\n- " + j.data.join("\n- "));
                } else {
                    alert("Raisons non disponibles");
                }
            } catch { alert("Erreur lors du chargement des raisons"); }
        }

        function renderList(items){
            const host = document.getElementById("listHost");
            host.innerHTML = "";
            const q = (document.getElementById("search").value||"").toLowerCase().trim();
            const filtered = q
                ? items.filter(t => [t.tag_code, t.address_text, t.reason, t.description].some(v => String(v||"").toLowerCase().includes(q)))
                : items;

            if(filtered.length === 0){
                host.innerHTML = '<div class="alert alert-info">Aucun tag.</div>';
                return;
            }
            filtered.forEach(t => {
                const div = document.createElement("div");
                div.innerHTML = tagCardHtml(t);
                host.appendChild(div);
            });

            // Wire interactions
            host.querySelectorAll("button[data-action]").forEach(btn => {
                btn.onclick = async () => {
                    const action = btn.getAttribute("data-action");
                    const id = btn.getAttribute("data-id");
                    const scope = document.getElementById("scope").value;
                    const idville = document.getElementById("idville").value;
                    const dte = document.getElementById("dte").value;
                    if(action === "TOGGLE_DISC"){
                        const el = host.querySelector('[data-disc="'+id+'"]');
                        if(el) el.style.display = (el.style.display === "none" ? "" : "none");
                        return;
                    }
                    if(action === "ACK" || action === "DONE"){
                        try { await respondTag(id, action, ""); } catch(e){ alert(e.message||e); return; }
                        const list = await fetchTags(idville, scope, dte); renderList(list); return;
                    }
                    if(action === "REJECTED"){
                        const txt = prompt("Raison du rejet:", "") || "";
                        try { await respondTag(id, "REJECTED", txt); } catch(e){ alert(e.message||e); return; }
                        const list = await fetchTags(idville, scope, dte); renderList(list); return;
                    }
                    if(action === "POST_REPLY"){
                        const ta = host.querySelector('textarea[name="reply_'+id+'"]');
                        const txt = ta?.value?.trim() || "";
                        if(!txt){ alert("Veuillez saisir une réponse."); return; }
                        try { await respondTag(id, "ACK", txt); ta.value=""; } catch(e){ alert(e.message||e); return; }
                        const list = await fetchTags(idville, scope, dte); renderList(list); return;
                    }
                    if(action === "SAVE_EDIT"){
                        const card = btn.closest(".card");
                        const addr = card.querySelector('input[name="address_text"]')?.value || "";
                        const reason = card.querySelector('input[name="reason"]')?.value || "";
                        const desc = card.querySelector('textarea[name="description"]')?.value || "";
                        try { await updateTag(id, addr, reason, desc); } catch(e){ alert(e.message||e); return; }
                        const list = await fetchTags(idville, scope, dte); renderList(list); return;
                    }
                };
            });

            // Image overlay
            host.querySelectorAll("img.img-thumb[data-full], img.img-thumb").forEach(img => {
                img.onclick = () => { const src = img.getAttribute("data-full") || img.src; ovImg.src = src; overlay.style.display = "flex"; };
            });
        }

        document.getElementById("btnLoad").onclick = async () => {
            const idville = parseInt(document.getElementById("idville").value||"0",10)||0;
            if(!idville){ alert("Ville invalide"); return; }
            const scope = document.getElementById("scope").value;
            const dte = document.getElementById("dte").value;
            const list = await fetchTags(idville, scope, dte);
            renderList(list);
        };
        document.getElementById("btnReasons").onclick = loadReasons;
        document.getElementById("search").addEventListener("input", () => {
            // simple re-filter current DOM by reloading last data (could cache last items)
            document.getElementById("btnLoad").click();
        });

        // Initial load
        document.getElementById("btnLoad").click();
    </script>
</body>
</html>