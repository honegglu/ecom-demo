<?php
/**
 * Cart Page
 */
?>

<nav class="breadcrumb">
    <a href="/">Start</a>
    <span class="sep">›</span>
    <span>Warenkorb</span>
</nav>

<h1 class="page-title">Warenkorb</h1>

<!-- USABILITY-HOOK: Warenkorb-Anzeige -->
<div class="cart-page" id="cartPage">
    <div class="cart-empty" id="cartEmpty" style="display:none">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <h2>Dein Warenkorb ist leer</h2>
        <p>Füge Produkte hinzu, um mit dem Einkauf zu beginnen.</p>
        <a href="/" class="btn btn-primary">Weiter einkaufen</a>
    </div>

    <div class="cart-content" id="cartContent" style="display:none">
        <div class="cart-items" id="cartItems">
            <!-- Filled by JS -->
        </div>
        <aside class="cart-summary">
            <h3>Bestellübersicht</h3>
            <div class="summary-row">
                <span>Zwischensumme</span>
                <span id="cartSubtotal">CHF 0.00</span>
            </div>
            <div class="summary-row">
                <span>Versand</span>
                <span id="cartShipping">CHF 0.00</span>
            </div>
            <div class="summary-row summary-hint" id="freeShippingHint" style="display:none">
                <span></span>
                <span class="hint-text">Kostenloser Versand ab CHF <?= number_format($settings['free_shipping_threshold'] ?? 75, 2, '.', "'") ?></span>
            </div>
            <div class="summary-row">
                <span>MwSt. (<?= $settings['vat_rate'] ?? 8.1 ?>%)</span>
                <span id="cartVat">CHF 0.00</span>
            </div>
            <div class="summary-row summary-total">
                <span>Gesamtbetrag</span>
                <span id="cartTotal">CHF 0.00</span>
            </div>
            <!-- USABILITY-HOOK: Checkout-Button -->
            <a href="/checkout" class="btn btn-primary btn-block btn-checkout">Zur Kasse</a>
            <a href="/" class="btn btn-outline btn-block">Weiter einkaufen</a>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => renderCartPage());

function renderCartPage() {
    const items = Cart.getItems();
    const emptyEl = document.getElementById('cartEmpty');
    const contentEl = document.getElementById('cartContent');

    if (items.length === 0) {
        emptyEl.style.display = 'flex';
        contentEl.style.display = 'none';
        return;
    }

    emptyEl.style.display = 'none';
    contentEl.style.display = 'grid';

    const container = document.getElementById('cartItems');
    container.innerHTML = items.map((item, i) => {
        const attrText = Object.entries(item.attributes || {}).map(([k,v]) => k + ': ' + v).join(', ');
        return `
        <div class="cart-item" data-index="${i}">
            <div class="cart-item-image">
                <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">
            </div>
            <div class="cart-item-details">
                <a href="/product/${escapeHtml(item.slug)}" class="cart-item-name">${escapeHtml(item.name)}</a>
                ${attrText ? `<span class="cart-item-attrs">${escapeHtml(attrText)}</span>` : ''}
                <span class="cart-item-price-mobile">${formatPrice(item.price)}</span>
            </div>
    <span style="font-weight:500">${item.qty}x</span>
            <div class="cart-item-price">${formatPrice(item.price * item.qty)}</div>
            <button class="cart-item-remove" onclick="removeCartItem(${i})" aria-label="Entfernen">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>`;
    }).join('');

    updateCartSummary();
}

function updateCartQty(index, delta) {
    Cart.updateQty(index, delta);
    renderCartPage();
}

function setCartQty(index, val) {
    const qty = parseInt(val);
    if (qty < 1) return;
    const items = Cart.getItems();
    items[index].qty = qty;
    Cart.save(items);
    renderCartPage();
}

function removeCartItem(index) {
    Cart.remove(index);
    renderCartPage();
    showToast('Artikel entfernt');
}

function updateCartSummary() {
    const items = Cart.getItems();
    const s = window.SHOP_SETTINGS;
    const subtotal = items.reduce((sum, item) => sum + item.price * item.qty, 0);
    const shipping = subtotal >= s.free_shipping_threshold ? 0 : s.shipping_cost;
    const vatAmount = Math.round(subtotal * s.vat_rate / 100 * 100) / 100;
    const total = subtotal + shipping + vatAmount;

    document.getElementById('cartSubtotal').textContent = formatPrice(subtotal);
    document.getElementById('cartShipping').textContent = shipping === 0 ? 'Kostenlos' : formatPrice(shipping);
    document.getElementById('cartVat').textContent = formatPrice(vatAmount);
    document.getElementById('cartTotal').textContent = formatPrice(total);

    const hint = document.getElementById('freeShippingHint');
    if (subtotal < s.free_shipping_threshold && subtotal > 0) {
        const diff = s.free_shipping_threshold - subtotal;
        hint.querySelector('.hint-text').textContent = `Noch ${formatPrice(diff)} bis zum kostenlosen Versand`;
        hint.style.display = 'flex';
    } else {
        hint.style.display = 'none';
    }
}
</script>
