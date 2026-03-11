<?php
/**
 * Settings / Admin Page
 */
$products = get_products();
?>

<h1 class="page-title">Shop-Einstellungen</h1>

<div class="settings-page">
    <nav class="settings-nav">
        <button class="settings-tab active" data-tab="appearance" onclick="switchTab(this)">Erscheinungsbild</button>
        <button class="settings-tab" data-tab="shop-info" onclick="switchTab(this)">Shop-Infos</button>
        <button class="settings-tab" data-tab="products" onclick="switchTab(this)">Produkte</button>
        <button class="settings-tab" data-tab="checkout-settings" onclick="switchTab(this)">Checkout</button>
    </nav>

    <div class="settings-content">
        <!-- Appearance -->
        <div class="settings-panel active" id="tab-appearance">
            <h2>Erscheinungsbild</h2>
            <form onsubmit="return saveAppearance(event)">
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="setPrimaryColor">Primärfarbe</label>
                        <div class="color-input-wrap">
                            <input type="color" id="setPrimaryColor" value="<?= htmlspecialchars($settings['primary_color'] ?? '#1a1a2e') ?>">
                            <input type="text" id="setPrimaryColorText" value="<?= htmlspecialchars($settings['primary_color'] ?? '#1a1a2e') ?>" class="color-text-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="setSecondaryColor">Sekundärfarbe</label>
                        <div class="color-input-wrap">
                            <input type="color" id="setSecondaryColor" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#e94560') ?>">
                            <input type="text" id="setSecondaryColorText" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#e94560') ?>" class="color-text-input">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="setFont">Schriftart (Google Fonts)</label>
                    <select id="setFont">
                        <?php
                        $fonts = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Nunito', 'Raleway', 'Source Sans 3', 'Work Sans'];
                        foreach ($fonts as $f):
                        ?>
                            <option value="<?= $f ?>" <?= ($settings['font_family'] ?? 'Inter') === $f ? 'selected' : '' ?>><?= $f ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="setFontSize">Schriftgrösse (px)</label>
                        <input type="number" id="setFontSize" value="<?= (int)($settings['font_size_base'] ?? 16) ?>" min="12" max="24">
                    </div>
                    <div class="form-group">
                        <label for="setHeadingSize">Überschriftengrösse (px)</label>
                        <input type="number" id="setHeadingSize" value="<?= (int)($settings['font_size_heading'] ?? 28) ?>" min="18" max="48">
                    </div>
                </div>
                <div class="form-group">
                    <label>Logo</label>
                    <div class="logo-upload-area">
                        <?php if ($settings['logo_url'] ?? ''): ?>
                            <img src="/<?= htmlspecialchars($settings['logo_url']) ?>" alt="Logo" class="logo-preview" id="logoPreview">
                        <?php else: ?>
                            <div class="logo-placeholder" id="logoPreview">Kein Logo hochgeladen</div>
                        <?php endif; ?>
                        <input type="file" id="logoFile" accept="image/*" onchange="uploadLogo(this)">
                        <label for="logoFile" class="btn btn-outline btn-sm">Logo hochladen</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>

        <!-- Shop Info -->
        <div class="settings-panel" id="tab-shop-info">
            <h2>Shop-Informationen</h2>
            <form onsubmit="return saveShopInfo(event)">
                <div class="form-group">
                    <label for="setShopName">Shopname</label>
                    <input type="text" id="setShopName" value="<?= htmlspecialchars($settings['shop_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="setSlogan">Slogan</label>
                    <input type="text" id="setSlogan" value="<?= htmlspecialchars($settings['slogan'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="setEmail">Kontakt-E-Mail</label>
                    <input type="email" id="setEmail" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="setPhone">Telefon</label>
                    <input type="tel" id="setPhone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="setAddress">Adresse</label>
                    <input type="text" id="setAddress" value="<?= htmlspecialchars($settings['contact_address'] ?? '') ?>">
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="setCountry">Land</label>
                        <select id="setCountry">
                            <option value="CH" <?= ($settings['country'] ?? 'CH') === 'CH' ? 'selected' : '' ?>>Schweiz</option>
                            <option value="DE" <?= ($settings['country'] ?? '') === 'DE' ? 'selected' : '' ?>>Deutschland</option>
                            <option value="AT" <?= ($settings['country'] ?? '') === 'AT' ? 'selected' : '' ?>>Österreich</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="setCurrency">Währung</label>
                        <select id="setCurrency">
                            <option value="CHF" <?= ($settings['currency'] ?? 'CHF') === 'CHF' ? 'selected' : '' ?>>CHF</option>
                            <option value="EUR" <?= ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>

        <!-- Products -->
        <div class="settings-panel" id="tab-products">
            <h2>Produkte verwalten</h2>
            <div class="products-table-wrap">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Bilder</th>
                            <th>Name</th>
                            <th>Kategorie</th>
                            <th>Preis</th>
                            <th>Aktionspreis</th>
                            <th>Bestand</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr data-product-id="<?= $p['id'] ?>">
                            <td>
                                <div class="product-images-cell">
                                    <div class="product-thumbs" id="thumbs-<?= $p['id'] ?>">
                                        <?php foreach (($p['images'] ?? []) as $img): ?>
                                        <div class="product-thumb-wrap">
                                            <img src="<?= htmlspecialchars($img) ?>" alt="" class="table-thumb">
                                            <button type="button" class="thumb-delete" onclick="deleteProductImage(<?= $p['id'] ?>, '<?= htmlspecialchars($img, ENT_QUOTES) ?>', this)" title="Bild entfernen">&times;</button>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="file" id="imgUpload-<?= $p['id'] ?>" accept="image/*" class="product-img-input" onchange="openImageEditor(<?= $p['id'] ?>, this)">
                                    <label for="imgUpload-<?= $p['id'] ?>" class="btn btn-sm btn-outline product-img-btn">+ Bild</label>
                                </div>
                            </td>
                            <td><input type="text" class="table-input" value="<?= htmlspecialchars($p['name']) ?>" data-field="name"></td>
                            <td><?= htmlspecialchars($p['category']) ?></td>
                            <td><input type="number" step="0.01" class="table-input table-input-sm" value="<?= $p['regular_price'] ?>" data-field="regular_price"></td>
                            <td><input type="number" step="0.01" class="table-input table-input-sm" value="<?= $p['sale_price'] ?? '' ?>" data-field="sale_price" placeholder="–"></td>
                            <td><input type="number" class="table-input table-input-sm" value="<?= $p['stock_qty'] ?>" data-field="stock_qty"></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="saveProduct(<?= $p['id'] ?>, this)">Speichern</button>
                                <?php if (!empty($p['variations'])): ?>
                                    <button class="btn btn-sm btn-outline" onclick="toggleVariations(<?= $p['id'] ?>)">Varianten</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!empty($p['variations'])): ?>
                        <tr class="variations-row" id="variations-<?= $p['id'] ?>" style="display:none">
                            <td colspan="7">
                                <table class="variations-table">
                                    <thead>
                                        <tr>
                                            <th>Variante</th>
                                            <th>Preis</th>
                                            <th>Bestand</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($p['variations'] as $vi => $v): ?>
                                        <tr data-var-index="<?= $vi ?>">
                                            <td><?= htmlspecialchars(implode(', ', array_map(fn($k,$val) => "$k: $val", array_keys($v['attributes']), $v['attributes']))) ?></td>
                                            <td><input type="number" step="0.01" class="table-input table-input-sm" value="<?= $v['price'] ?>" data-var-field="price"></td>
                                            <td><input type="number" class="table-input table-input-sm" value="<?= $v['stock_qty'] ?>" data-var-field="stock_qty"></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Image Editor Modal -->
        <div class="image-editor-modal" id="imageEditorModal" style="display:none">
            <div class="image-editor-backdrop" onclick="closeImageEditor()"></div>
            <div class="image-editor-dialog">
                <div class="image-editor-header">
                    <h3>Bild bearbeiten</h3>
                    <button class="image-editor-close" onclick="closeImageEditor()">&times;</button>
                </div>
                <div class="image-editor-body">
                    <div class="image-editor-canvas-wrap">
                        <canvas id="editorCanvas"></canvas>
                    </div>
                    <div class="image-editor-tools">
                        <div class="editor-tool-group">
                            <label>Drehen</label>
                            <div class="editor-btn-row">
                                <button type="button" class="btn btn-sm btn-outline" onclick="editorRotate(-90)">↶ 90°</button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="editorRotate(90)">↷ 90°</button>
                            </div>
                        </div>
                        <div class="editor-tool-group">
                            <label>Zuschnitt</label>
                            <div class="editor-btn-row">
                                <button type="button" class="btn btn-sm btn-outline editor-crop-btn" data-ratio="free" onclick="setCropRatio('free', this)">Frei</button>
                                <button type="button" class="btn btn-sm btn-outline editor-crop-btn" data-ratio="1:1" onclick="setCropRatio('1:1', this)">1:1</button>
                                <button type="button" class="btn btn-sm btn-outline editor-crop-btn" data-ratio="4:3" onclick="setCropRatio('4:3', this)">4:3</button>
                                <button type="button" class="btn btn-sm btn-outline editor-crop-btn" data-ratio="16:9" onclick="setCropRatio('16:9', this)">16:9</button>
                            </div>
                        </div>
                        <div class="editor-tool-group">
                            <label>Max. Breite: <span id="editorMaxWidth">800</span>px</label>
                            <input type="range" id="editorMaxWidthSlider" min="200" max="1600" step="100" value="800" oninput="document.getElementById('editorMaxWidth').textContent=this.value">
                        </div>
                        <div class="editor-tool-group">
                            <label>Qualität: <span id="editorQuality">80</span>%</label>
                            <input type="range" id="editorQualitySlider" min="30" max="100" step="5" value="80" oninput="document.getElementById('editorQuality').textContent=this.value">
                        </div>
                        <div class="editor-info" id="editorFileInfo"></div>
                    </div>
                </div>
                <div class="image-editor-footer">
                    <button type="button" class="btn btn-outline" onclick="closeImageEditor()">Abbrechen</button>
                    <button type="button" class="btn btn-primary" onclick="editorUpload()">Hochladen</button>
                </div>
            </div>
        </div>

        <!-- Checkout Settings -->
        <div class="settings-panel" id="tab-checkout-settings">
            <h2>Checkout-Einstellungen</h2>
            <form onsubmit="return saveCheckoutSettings(event)">
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="setShipping">Versandkosten (CHF)</label>
                        <input type="number" step="0.01" id="setShipping" value="<?= (float)($settings['shipping_cost'] ?? 7.90) ?>">
                    </div>
                    <div class="form-group">
                        <label for="setFreeThreshold">Gratis-Versand ab (CHF)</label>
                        <input type="number" step="0.01" id="setFreeThreshold" value="<?= (float)($settings['free_shipping_threshold'] ?? 75) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="setVat">MwSt.-Satz (%)</label>
                    <input type="number" step="0.1" id="setVat" value="<?= (float)($settings['vat_rate'] ?? 8.1) ?>">
                </div>
                <div class="form-group">
                    <label>Zahlungsmittel</label>
                    <div class="payment-toggles">
                        <label class="toggle-label">
                            <input type="checkbox" id="setPayTwint" <?= ($settings['payment_methods']['twint'] ?? true) ? 'checked' : '' ?>>
                            TWINT
                        </label>
                        <label class="toggle-label">
                            <input type="checkbox" id="setPayCC" <?= ($settings['payment_methods']['credit_card'] ?? true) ? 'checked' : '' ?>>
                            Kreditkarte (Visa/Mastercard)
                        </label>
                        <label class="toggle-label">
                            <input type="checkbox" id="setPayInvoice" <?= ($settings['payment_methods']['invoice'] ?? true) ? 'checked' : '' ?>>
                            Rechnung
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(btn) {
    const tab = btn.dataset.tab;
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

function apiSave(data) {
    return fetch('/api.php?action=save_settings', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(r => r.json());
}

function saveAppearance(e) {
    e.preventDefault();
    apiSave({
        primary_color: document.getElementById('setPrimaryColor').value,
        secondary_color: document.getElementById('setSecondaryColor').value,
        font_family: document.getElementById('setFont').value,
        font_size_base: document.getElementById('setFontSize').value,
        font_size_heading: document.getElementById('setHeadingSize').value,
    }).then(() => {
        showToast('Erscheinungsbild gespeichert');
        setTimeout(() => location.reload(), 500);
    });
    return false;
}

function saveShopInfo(e) {
    e.preventDefault();
    apiSave({
        shop_name: document.getElementById('setShopName').value,
        slogan: document.getElementById('setSlogan').value,
        contact_email: document.getElementById('setEmail').value,
        contact_phone: document.getElementById('setPhone').value,
        contact_address: document.getElementById('setAddress').value,
        country: document.getElementById('setCountry').value,
        currency: document.getElementById('setCurrency').value,
    }).then(() => {
        showToast('Shop-Infos gespeichert');
        setTimeout(() => location.reload(), 500);
    });
    return false;
}

function saveCheckoutSettings(e) {
    e.preventDefault();
    apiSave({
        shipping_cost: parseFloat(document.getElementById('setShipping').value),
        free_shipping_threshold: parseFloat(document.getElementById('setFreeThreshold').value),
        vat_rate: parseFloat(document.getElementById('setVat').value),
        payment_methods: {
            twint: document.getElementById('setPayTwint').checked,
            credit_card: document.getElementById('setPayCC').checked,
            invoice: document.getElementById('setPayInvoice').checked,
        }
    }).then(() => showToast('Checkout-Einstellungen gespeichert'));
    return false;
}

function saveProduct(id, btn) {
    const row = btn.closest('tr');
    const data = {id: id};
    row.querySelectorAll('[data-field]').forEach(input => {
        const field = input.dataset.field;
        data[field] = input.value;
    });

    // Check for variations
    const varRow = document.getElementById('variations-' + id);
    if (varRow) {
        const variations = [];
        varRow.querySelectorAll('tr[data-var-index]').forEach(vr => {
            const vi = parseInt(vr.dataset.varIndex);
            const varData = {};
            vr.querySelectorAll('[data-var-field]').forEach(input => {
                varData[input.dataset.varField] = input.value;
            });
            variations.push({index: vi, ...varData});
        });
        if (variations.length > 0) {
            // Reconstruct full variations array – fetch current and merge
            data._variation_updates = variations;
        }
    }

    fetch('/api.php?action=save_product', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(r => r.json()).then(() => showToast('Produkt gespeichert'));
}

function toggleVariations(id) {
    const row = document.getElementById('variations-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

function uploadLogo(input) {
    if (!input.files[0]) return;
    const fd = new FormData();
    fd.append('logo', input.files[0]);
    fetch('/api.php?action=upload_logo', {method:'POST', body: fd})
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Logo hochgeladen');
                setTimeout(() => location.reload(), 500);
            }
        });
}

// Sync color pickers
document.getElementById('setPrimaryColor').addEventListener('input', function() {
    document.getElementById('setPrimaryColorText').value = this.value;
});
document.getElementById('setPrimaryColorText').addEventListener('input', function() {
    document.getElementById('setPrimaryColor').value = this.value;
});
document.getElementById('setSecondaryColor').addEventListener('input', function() {
    document.getElementById('setSecondaryColorText').value = this.value;
});
document.getElementById('setSecondaryColorText').addEventListener('input', function() {
    document.getElementById('setSecondaryColor').value = this.value;
});

// ============================================
// Product Image Upload & Editor
// ============================================
let editorState = {
    productId: null,
    originalImage: null,
    rotation: 0,
    cropRatio: 'free',
    cropRect: null,
    isDragging: false,
    dragStart: null,
    inputElement: null
};

function openImageEditor(productId, input) {
    if (!input.files[0]) return;
    const file = input.files[0];
    editorState.productId = productId;
    editorState.rotation = 0;
    editorState.cropRatio = 'free';
    editorState.cropRect = null;
    editorState.inputElement = input;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            editorState.originalImage = img;
            document.getElementById('imageEditorModal').style.display = 'flex';
            document.querySelectorAll('.editor-crop-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.editor-crop-btn[data-ratio="free"]').classList.add('active');
            renderEditor();
            updateFileInfo(file.size);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function closeImageEditor() {
    document.getElementById('imageEditorModal').style.display = 'none';
    editorState.originalImage = null;
    editorState.cropRect = null;
    if (editorState.inputElement) {
        editorState.inputElement.value = '';
    }
}

function updateFileInfo(originalSize) {
    const maxW = parseInt(document.getElementById('editorMaxWidthSlider').value);
    const quality = parseInt(document.getElementById('editorQualitySlider').value);
    const img = editorState.originalImage;
    const info = document.getElementById('editorFileInfo');
    const origW = (editorState.rotation % 180 !== 0) ? img.height : img.width;
    const origH = (editorState.rotation % 180 !== 0) ? img.width : img.height;
    const finalW = Math.min(origW, maxW);
    const ratio = finalW / origW;
    const finalH = Math.round(origH * ratio);
    info.textContent = `Original: ${img.width}×${img.height} • Ausgabe: ~${finalW}×${finalH} • Qualität: ${quality}%`;
}

function renderEditor() {
    const canvas = document.getElementById('editorCanvas');
    const ctx = canvas.getContext('2d');
    const img = editorState.originalImage;
    const rot = editorState.rotation;

    // Calculate display dimensions (fit within 500px)
    const maxDisplay = 500;
    let dw = img.width, dh = img.height;
    if (rot % 180 !== 0) { dw = img.height; dh = img.width; }

    const scale = Math.min(maxDisplay / dw, maxDisplay / dh, 1);
    canvas.width = Math.round(dw * scale);
    canvas.height = Math.round(dh * scale);

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();
    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate(rot * Math.PI / 180);

    const sw = (rot % 180 !== 0) ? canvas.height : canvas.width;
    const sh = (rot % 180 !== 0) ? canvas.width : canvas.height;
    ctx.drawImage(img, -sw / 2, -sh / 2, sw, sh);
    ctx.restore();

    // Draw crop overlay
    if (editorState.cropRect) {
        const cr = editorState.cropRect;
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        // Top
        ctx.fillRect(0, 0, canvas.width, cr.y);
        // Bottom
        ctx.fillRect(0, cr.y + cr.h, canvas.width, canvas.height - cr.y - cr.h);
        // Left
        ctx.fillRect(0, cr.y, cr.x, cr.h);
        // Right
        ctx.fillRect(cr.x + cr.w, cr.y, canvas.width - cr.x - cr.w, cr.h);
        // Border
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = 2;
        ctx.strokeRect(cr.x, cr.y, cr.w, cr.h);
        // Grid lines (rule of thirds)
        ctx.strokeStyle = 'rgba(255,255,255,0.3)';
        ctx.lineWidth = 1;
        for (let i = 1; i <= 2; i++) {
            ctx.beginPath();
            ctx.moveTo(cr.x + cr.w * i / 3, cr.y);
            ctx.lineTo(cr.x + cr.w * i / 3, cr.y + cr.h);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(cr.x, cr.y + cr.h * i / 3);
            ctx.lineTo(cr.x + cr.w, cr.y + cr.h * i / 3);
            ctx.stroke();
        }
    }

    editorState._scale = scale;
}

function editorRotate(deg) {
    editorState.rotation = (editorState.rotation + deg + 360) % 360;
    editorState.cropRect = null;
    renderEditor();
    updateFileInfo(0);
}

function setCropRatio(ratio, btn) {
    document.querySelectorAll('.editor-crop-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    editorState.cropRatio = ratio;

    if (ratio === 'free') {
        editorState.cropRect = null;
        renderEditor();
        return;
    }

    const canvas = document.getElementById('editorCanvas');
    const [rw, rh] = ratio.split(':').map(Number);
    const targetRatio = rw / rh;
    let cw, ch;
    if (canvas.width / canvas.height > targetRatio) {
        ch = canvas.height * 0.8;
        cw = ch * targetRatio;
    } else {
        cw = canvas.width * 0.8;
        ch = cw / targetRatio;
    }
    editorState.cropRect = {
        x: Math.round((canvas.width - cw) / 2),
        y: Math.round((canvas.height - ch) / 2),
        w: Math.round(cw),
        h: Math.round(ch)
    };
    renderEditor();
}

// Crop dragging
(function() {
    let dragType = null; // 'move' or 'resize'
    let startX, startY, startCrop;

    document.addEventListener('mousedown', function(e) {
        if (!editorState.cropRect) return;
        const canvas = document.getElementById('editorCanvas');
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const cr = editorState.cropRect;

        if (x >= cr.x && x <= cr.x + cr.w && y >= cr.y && y <= cr.y + cr.h) {
            // Check if near edge (resize) or interior (move)
            const edge = 12;
            const nearRight = Math.abs(x - (cr.x + cr.w)) < edge;
            const nearBottom = Math.abs(y - (cr.y + cr.h)) < edge;
            dragType = (nearRight || nearBottom) ? 'resize' : 'move';
            startX = x; startY = y;
            startCrop = {...cr};
            e.preventDefault();
        }
    });

    document.addEventListener('mousemove', function(e) {
        if (!dragType || !editorState.cropRect) return;
        const canvas = document.getElementById('editorCanvas');
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const dx = x - startX, dy = y - startY;
        const cr = editorState.cropRect;

        if (dragType === 'move') {
            cr.x = Math.max(0, Math.min(canvas.width - cr.w, startCrop.x + dx));
            cr.y = Math.max(0, Math.min(canvas.height - cr.h, startCrop.y + dy));
        } else {
            if (editorState.cropRatio !== 'free') {
                const [rw, rh] = editorState.cropRatio.split(':').map(Number);
                const ratio = rw / rh;
                let newW = Math.max(40, startCrop.w + dx);
                let newH = newW / ratio;
                if (cr.x + newW > canvas.width) newW = canvas.width - cr.x;
                newH = newW / ratio;
                if (cr.y + newH > canvas.height) { newH = canvas.height - cr.y; newW = newH * ratio; }
                cr.w = Math.round(newW);
                cr.h = Math.round(newH);
            } else {
                cr.w = Math.max(40, Math.min(canvas.width - cr.x, startCrop.w + dx));
                cr.h = Math.max(40, Math.min(canvas.height - cr.y, startCrop.h + dy));
            }
        }
        renderEditor();
    });

    document.addEventListener('mouseup', function() {
        dragType = null;
    });
})();

function getEditedImageBlob(callback) {
    const img = editorState.originalImage;
    const rot = editorState.rotation;
    const maxW = parseInt(document.getElementById('editorMaxWidthSlider').value);
    const quality = parseInt(document.getElementById('editorQualitySlider').value) / 100;

    // Create a full-resolution rotated canvas
    const tmpCanvas = document.createElement('canvas');
    const tmpCtx = tmpCanvas.getContext('2d');
    let fullW = img.width, fullH = img.height;
    if (rot % 180 !== 0) { fullW = img.height; fullH = img.width; }
    tmpCanvas.width = fullW;
    tmpCanvas.height = fullH;
    tmpCtx.translate(fullW / 2, fullH / 2);
    tmpCtx.rotate(rot * Math.PI / 180);
    const sw = (rot % 180 !== 0) ? fullH : fullW;
    const sh = (rot % 180 !== 0) ? fullW : fullH;
    tmpCtx.drawImage(img, -sw / 2, -sh / 2, sw, sh);

    // Apply crop
    let srcX = 0, srcY = 0, srcW = fullW, srcH = fullH;
    if (editorState.cropRect) {
        const scale = editorState._scale;
        srcX = Math.round(editorState.cropRect.x / scale);
        srcY = Math.round(editorState.cropRect.y / scale);
        srcW = Math.round(editorState.cropRect.w / scale);
        srcH = Math.round(editorState.cropRect.h / scale);
    }

    // Resize to max width
    let outW = srcW, outH = srcH;
    if (outW > maxW) {
        const r = maxW / outW;
        outW = maxW;
        outH = Math.round(outH * r);
    }

    const outCanvas = document.createElement('canvas');
    outCanvas.width = outW;
    outCanvas.height = outH;
    const outCtx = outCanvas.getContext('2d');
    outCtx.drawImage(tmpCanvas, srcX, srcY, srcW, srcH, 0, 0, outW, outH);

    outCanvas.toBlob(function(blob) {
        callback(blob, outW, outH);
    }, 'image/jpeg', quality);
}

function editorUpload() {
    const btn = document.querySelector('.image-editor-footer .btn-primary');
    btn.disabled = true;
    btn.textContent = 'Wird hochgeladen…';

    getEditedImageBlob(function(blob, w, h) {
        const fd = new FormData();
        fd.append('image', blob, 'product_image.jpg');
        fd.append('product_id', editorState.productId);

        fetch('/api.php?action=upload_product_image', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Hochladen';
                if (data.success) {
                    // Add thumbnail to the product row
                    const thumbsContainer = document.getElementById('thumbs-' + editorState.productId);
                    const wrap = document.createElement('div');
                    wrap.className = 'product-thumb-wrap';
                    wrap.innerHTML = `<img src="${data.url}" alt="" class="table-thumb"><button type="button" class="thumb-delete" onclick="deleteProductImage(${editorState.productId}, '${data.url}', this)" title="Bild entfernen">&times;</button>`;
                    thumbsContainer.appendChild(wrap);
                    closeImageEditor();
                    showToast('Bild hochgeladen (' + w + '×' + h + 'px)');
                } else {
                    showToast(data.error || 'Upload fehlgeschlagen', 'error');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Hochladen';
                showToast('Upload fehlgeschlagen', 'error');
            });
    });
}

function deleteProductImage(productId, imageUrl, btn) {
    if (!confirm('Bild wirklich entfernen?')) return;
    fetch('/api.php?action=delete_product_image', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ product_id: productId, image_url: imageUrl })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            btn.closest('.product-thumb-wrap').remove();
            showToast('Bild entfernt');
        }
    });
}
</script>
