<?php
/**
 * Product Detail Page
 */
if (!isset($product)) return;

$hasVariations = !empty($product['variations']);
$effectivePrice = get_effective_price($product);
$priceRange = get_price_range($product);

// Color map for swatches
$colorHexMap = [
    'Rot' => '#e74c3c', 'Grün' => '#27ae60', 'Blau' => '#2980b9',
    'Grau' => '#95a5a6', 'Gelb' => '#f1c40f', 'Schwarz' => '#2c3e50',
    'Weiss' => '#ecf0f1',
];
?>

<nav class="breadcrumb">
    <a href="/">Start</a>
    <span class="sep">›</span>
    <a href="/?category=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category']) ?></a>
    <span class="sep">›</span>
    <span><?= htmlspecialchars($product['name']) ?></span>
</nav>

<div class="product-detail">
    <!-- Image Gallery -->
    <!-- USABILITY-HOOK: Bildergalerie -->
    <div class="product-gallery">
        <div class="gallery-main">
            <img src="<?= htmlspecialchars($product['images'][0] ?? '') ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 id="mainImage"
                 class="gallery-main-img">
            <?php if ($product['sale_price'] !== null): ?>
                <span class="badge badge-sale badge-lg">Sale</span>
            <?php endif; ?>
        </div>
        <?php if (count($product['images']) > 1): ?>
            <div class="gallery-thumbs">
                <?php foreach ($product['images'] as $i => $img): ?>
                    <button class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                            onclick="setMainImage('<?= htmlspecialchars($img) ?>', this)">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Bild <?= $i + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Info -->
    <div class="product-info">
        <span class="product-category-label"><?= htmlspecialchars($product['category']) ?></span>
        <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

        <div class="product-price-display" id="priceDisplay">
            <?php if ($hasVariations && $priceRange['min'] !== $priceRange['max']): ?>
                <span class="price-current" id="currentPrice">ab <?= format_price($priceRange['min'], $settings) ?></span>
            <?php elseif ($product['sale_price'] !== null): ?>
                <span class="price-old-detail"><?= format_price($product['regular_price'], $settings) ?></span>
                <span class="price-current price-sale"><?= format_price($product['sale_price'], $settings) ?></span>
                <span class="price-savings">Du sparst <?= format_price($product['regular_price'] - $product['sale_price'], $settings) ?></span>
            <?php else: ?>
                <span class="price-current"><?= format_price($effectivePrice, $settings) ?></span>
            <?php endif; ?>
        </div>

        <p class="product-short-desc"><?= htmlspecialchars($product['short_description']) ?></p>

        <!-- USABILITY-HOOK: Variantenauswahl -->
        <?php if (!empty($product['attributes'])): ?>
            <div class="variant-selectors" id="variantSelectors">
                <?php foreach ($product['attributes'] as $attr): ?>
                    <div class="variant-group">
                        <label class="variant-label">
                            <?= htmlspecialchars($attr['name']) ?>:
                            <span class="variant-selected" id="selected_<?= htmlspecialchars($attr['name']) ?>">Bitte wählen</span>
                        </label>
                        <?php if ($attr['name'] === 'Farbe'): ?>
                            <div class="variant-options color-swatches">
                                <?php foreach ($attr['options'] as $opt): ?>
                                    <button class="color-swatch"
                                            data-attr="<?= htmlspecialchars($attr['name']) ?>"
                                            data-value="<?= htmlspecialchars($opt) ?>"
                                            title="<?= htmlspecialchars($opt) ?>"
                                            style="background-color: <?= $colorHexMap[$opt] ?? '#ccc' ?>"
                                            onclick="selectVariant(this)">
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="variant-options size-buttons">
                                <?php foreach ($attr['options'] as $opt): ?>
                                    <button class="size-btn"
                                            data-attr="<?= htmlspecialchars($attr['name']) ?>"
                                            data-value="<?= htmlspecialchars($opt) ?>"
                                            onclick="selectVariant(this)">
                                        <?= htmlspecialchars($opt) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Stock display -->
        <!-- USABILITY-HOOK: Lagerstatus -->
        <div class="stock-info" id="stockInfo">
            <?php if ($product['in_stock']): ?>
                <?php if ($product['stock_qty'] <= 5): ?>
                    <span class="stock-low">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Nur noch <?= $product['stock_qty'] ?> Stück verfügbar
                    </span>
                <?php else: ?>
                    <span class="stock-ok">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Auf Lager
                    </span>
                <?php endif; ?>
            <?php else: ?>
                <span class="stock-out">Nicht verfügbar</span>
            <?php endif; ?>
        </div>

        <!-- Quantity & Add to cart -->
        <div class="add-to-cart-section">
            <div class="quantity-selector">
                <button class="qty-btn" onclick="changeQty(-1)" aria-label="Menge verringern">−</button>
                <input type="number" id="qtyInput" value="1" min="1" max="99" class="qty-input">
                <button class="qty-btn" onclick="changeQty(1)" aria-label="Menge erhöhen">+</button>
            </div>
            <button class="btn btn-primary btn-add-cart" id="addToCartBtn"
                    onclick="addToCart()"
                    <?= !$product['in_stock'] ? 'disabled' : '' ?>>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                In den Warenkorb
            </button>
        </div>

        <!-- Shipping info -->
        <div class="product-meta">
            <div class="meta-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <span>Lieferung in 2–3 Werktagen</span>
            </div>
            <div class="meta-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <span>Kostenloser Versand ab CHF 75.00</span>
            </div>
            <div class="meta-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <span>30 Tage Rückgaberecht</span>
            </div>
        </div>

        <!-- Description -->
        <details class="product-description-details" open>
            <summary>Beschreibung</summary>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </details>

        <details class="product-description-details">
            <summary>Versand & Rückgabe</summary>
            <p>Wir liefern innerhalb der Schweiz in 2–3 Werktagen. Ab einem Bestellwert von CHF 75.00 ist der Versand kostenlos. Rückgaben sind innerhalb von 30 Tagen nach Erhalt der Ware möglich.</p>
        </details>
    </div>
</div>

<script>
const productData = <?= json_encode($product) ?>;
const settings = window.SHOP_SETTINGS;
const selectedAttrs = {};

function setMainImage(src, thumb) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    if (thumb) thumb.classList.add('active');
}

function selectVariant(btn) {
    const attr = btn.dataset.attr;
    const value = btn.dataset.value;
    selectedAttrs[attr] = value;

    // Update UI
    btn.closest('.variant-options').querySelectorAll('button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('selected_' + attr).textContent = value;

    updateVariation();
}

function updateVariation() {
    if (!productData.variations || productData.variations.length === 0) return;

    const attrCount = productData.attributes.length;
    if (Object.keys(selectedAttrs).length < attrCount) return;

    // Find matching variation
    const match = productData.variations.find(v => {
        return Object.keys(selectedAttrs).every(k => v.attributes[k] === selectedAttrs[k]);
    });

    if (match) {
        // Update price
        const priceEl = document.getElementById('priceDisplay');
        const price = match.sale_price || match.price;
        priceEl.innerHTML = '<span class="price-current">' + settings.currency + ' ' + formatNumber(price) + '</span>';

        // Update stock
        const stockEl = document.getElementById('stockInfo');
        if (match.stock_qty <= 0) {
            stockEl.innerHTML = '<span class="stock-out">Nicht verfügbar</span>';
            document.getElementById('addToCartBtn').disabled = true;
        } else if (match.stock_qty <= 5) {
            stockEl.innerHTML = '<span class="stock-low"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Nur noch ' + match.stock_qty + ' Stück verfügbar</span>';
            document.getElementById('addToCartBtn').disabled = false;
        } else {
            stockEl.innerHTML = '<span class="stock-ok"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Auf Lager</span>';
            document.getElementById('addToCartBtn').disabled = false;
        }

        // Update image
        if (match.image) {
            document.getElementById('mainImage').src = match.image;
        }
    }
}

function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    input.value = val;
}

function addToCart() {
    // Check if all variants selected
    if (productData.attributes && productData.attributes.length > 0) {
        if (Object.keys(selectedAttrs).length < productData.attributes.length) {
            showToast('Bitte wähle alle Optionen aus', 'warning');
            return;
        }
    }

    const qty = parseInt(document.getElementById('qtyInput').value) || 1;

    // Find price
    let price = productData.sale_price || productData.regular_price;
    let variationId = null;

    if (productData.variations && productData.variations.length > 0) {
        const match = productData.variations.find(v =>
            Object.keys(selectedAttrs).every(k => v.attributes[k] === selectedAttrs[k])
        );
        if (match) {
            price = match.sale_price || match.price;
            variationId = match.id;
        }
    }

    const cartItem = {
        productId: productData.id,
        variationId: variationId,
        name: productData.name,
        slug: productData.slug,
        image: document.getElementById('mainImage').src,
        price: price,
        attributes: {...selectedAttrs},
        qty: qty
    };

    Cart.add(cartItem);

    // Button animation
    const btn = document.getElementById('addToCartBtn');
    btn.classList.add('added');
    btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Hinzugefügt!';
    setTimeout(() => {
        btn.classList.remove('added');
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> In den Warenkorb';
    }, 1500);
}

function formatNumber(n) {
    return Number(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, "'");
}
</script>
