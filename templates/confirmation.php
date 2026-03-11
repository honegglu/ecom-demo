<?php
/**
 * Order Confirmation Page
 */
?>

<div class="confirmation-page" id="confirmationPage">
    <div class="confirmation-icon">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--color-secondary)" stroke-width="1.5">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
    </div>
    <h1 class="confirmation-title">Vielen Dank für deine Bestellung!</h1>
    <p class="confirmation-subtitle">Deine Bestellung wurde erfolgreich aufgegeben.</p>

    <div class="confirmation-details" id="confirmationDetails">
        <!-- Filled by JS -->
    </div>

    <div class="confirmation-email-notice">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <div>
            <strong>Bestätigungs-E-Mail versendet</strong>
            <p>Eine Bestellbestätigung wurde an deine E-Mail-Adresse gesendet.</p>
        </div>
    </div>

    <a href="/" class="btn btn-primary">Weiter einkaufen</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const order = JSON.parse(sessionStorage.getItem('lastOrder') || 'null');
    if (!order) {
        window.location.href = '/';
        return;
    }

    const paymentLabels = {twint: 'TWINT', credit_card: 'Kreditkarte', invoice: 'Rechnung'};

    const container = document.getElementById('confirmationDetails');
    container.innerHTML = `
        <div class="confirmation-card">
            <div class="confirmation-row">
                <span class="confirmation-label">Bestellnummer</span>
                <span class="confirmation-value"><strong>${escapeHtml(order.order_number)}</strong></span>
            </div>
            <div class="confirmation-row">
                <span class="confirmation-label">Zahlungsart</span>
                <span class="confirmation-value">${paymentLabels[order.payment_method] || order.payment_method}</span>
            </div>
            <div class="confirmation-row">
                <span class="confirmation-label">Lieferadresse</span>
                <span class="confirmation-value">
                    ${escapeHtml(order.address.firstName)} ${escapeHtml(order.address.lastName)}<br>
                    ${escapeHtml(order.address.street)}<br>
                    ${order.address.street2 ? escapeHtml(order.address.street2) + '<br>' : ''}
                    ${escapeHtml(order.address.zip)} ${escapeHtml(order.address.city)}
                </span>
            </div>
        </div>

        <div class="confirmation-card">
            <h3>Bestellte Artikel</h3>
            ${order.items.map(item => {
                const attrText = Object.entries(item.attributes || {}).map(([k,v]) => v).join(', ');
                return `<div class="confirmation-item">
                    <img src="${escapeHtml(item.image)}" alt="" class="confirmation-item-img">
                    <div class="confirmation-item-info">
                        <span>${escapeHtml(item.name)}</span>
                        ${attrText ? `<span class="text-muted">${escapeHtml(attrText)}</span>` : ''}
                        <span>Menge: ${item.qty}</span>
                    </div>
                    <span>${formatPrice(item.price * item.qty)}</span>
                </div>`;
            }).join('')}
            <div class="summary-divider"></div>
            <div class="summary-row"><span>Zwischensumme</span><span>${formatPrice(order.subtotal)}</span></div>
            <div class="summary-row"><span>Versand</span><span>${order.shipping === 0 ? 'Kostenlos' : formatPrice(order.shipping)}</span></div>
            <div class="summary-row"><span>MwSt.</span><span>${formatPrice(order.vat)}</span></div>
            <div class="summary-row summary-total"><span>Total</span><span>${formatPrice(order.total)}</span></div>
        </div>
    `;

    // Clear order from session
    sessionStorage.removeItem('lastOrder');
});
</script>
