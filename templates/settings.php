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
                            <th>Bild</th>
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
                            <td><img src="<?= htmlspecialchars($p['images'][0] ?? '') ?>" alt="" class="table-thumb"></td>
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
</script>
