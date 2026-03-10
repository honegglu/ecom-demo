<?php
/**
 * Checkout Page – 3-step process
 */
$paymentMethods = $settings['payment_methods'] ?? ['twint' => true, 'credit_card' => true, 'invoice' => true];
?>

<nav class="breadcrumb">
    <a href="/">Start</a>
    <span class="sep">›</span>
    <a href="/cart">Warenkorb</a>
    <span class="sep">›</span>
    <span>Kasse</span>
</nav>

<!-- USABILITY-HOOK: Checkout-Schrittanzeige -->
<div class="checkout-steps">
    <div class="step active" data-step="1">
        <span class="step-number">1</span>
        <span class="step-label">Lieferadresse</span>
    </div>
    <div class="step-line"></div>
    <div class="step" data-step="2">
        <span class="step-number">2</span>
        <span class="step-label">Zahlung</span>
    </div>
    <div class="step-line"></div>
    <div class="step" data-step="3">
        <span class="step-number">3</span>
        <span class="step-label">Bestätigung</span>
    </div>
</div>

<div class="checkout-layout">
    <div class="checkout-form-area">

        <!-- Step 1: Shipping Address -->
        <!-- USABILITY-HOOK: Adressformular -->
        <div class="checkout-step-content" id="step1" style="display:block">
            <h2>Lieferadresse</h2>
            <form id="addressForm" class="checkout-form" onsubmit="return goToStep(2)">
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="firstName">Vorname *</label>
                        <input type="text" id="firstName" name="firstName" required autocomplete="given-name">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Nachname *</label>
                        <input type="text" id="lastName" name="lastName" required autocomplete="family-name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">E-Mail-Adresse *</label>
                    <input type="email" id="email" name="email" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="phone">Telefon</label>
                    <input type="tel" id="phone" name="phone" placeholder="+41 79 123 45 67" autocomplete="tel">
                </div>
                <div class="form-group">
                    <label for="street">Strasse und Hausnummer *</label>
                    <input type="text" id="street" name="street" required autocomplete="street-address">
                </div>
                <div class="form-group">
                    <label for="street2">Adresszusatz</label>
                    <input type="text" id="street2" name="street2" placeholder="c/o, Postfach, etc.">
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="zip">PLZ *</label>
                        <!-- USABILITY-HOOK: PLZ-Validierung -->
                        <input type="text" id="zip" name="zip" required pattern="[0-9]{4}" maxlength="4" placeholder="8001" autocomplete="postal-code">
                    </div>
                    <div class="form-group">
                        <label for="city">Ort *</label>
                        <input type="text" id="city" name="city" required autocomplete="address-level2">
                    </div>
                </div>
                <div class="form-group">
                    <label for="canton">Kanton *</label>
                    <select id="canton" name="canton" required>
                        <option value="">Bitte wählen</option>
                        <option value="AG">Aargau</option>
                        <option value="AI">Appenzell Innerrhoden</option>
                        <option value="AR">Appenzell Ausserrhoden</option>
                        <option value="BE">Bern</option>
                        <option value="BL">Basel-Landschaft</option>
                        <option value="BS">Basel-Stadt</option>
                        <option value="FR">Freiburg</option>
                        <option value="GE">Genf</option>
                        <option value="GL">Glarus</option>
                        <option value="GR">Graubünden</option>
                        <option value="JU">Jura</option>
                        <option value="LU">Luzern</option>
                        <option value="NE">Neuenburg</option>
                        <option value="NW">Nidwalden</option>
                        <option value="OW">Obwalden</option>
                        <option value="SG">St. Gallen</option>
                        <option value="SH">Schaffhausen</option>
                        <option value="SO">Solothurn</option>
                        <option value="SZ">Schwyz</option>
                        <option value="TG">Thurgau</option>
                        <option value="TI">Tessin</option>
                        <option value="UR">Uri</option>
                        <option value="VD">Waadt</option>
                        <option value="VS">Wallis</option>
                        <option value="ZG">Zug</option>
                        <option value="ZH">Zürich</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="sameAsBilling" checked>
                        Rechnungsadresse entspricht der Lieferadresse
                    </label>
                </div>
                <div class="form-actions">
                    <a href="/cart" class="btn btn-outline">Zurück</a>
                    <button type="submit" class="btn btn-primary">Weiter zur Zahlung</button>
                </div>
            </form>
        </div>

        <!-- Step 2: Payment -->
        <!-- USABILITY-HOOK: Zahlungsauswahl -->
        <div class="checkout-step-content" id="step2" style="display:none">
            <h2>Zahlungsart wählen</h2>

            <div class="payment-methods">
                <?php if ($paymentMethods['twint'] ?? true): ?>
                <label class="payment-method" data-method="twint">
                    <input type="radio" name="payment" value="twint">
                    <div class="payment-method-inner">
                        <div class="payment-method-header">
                            <span class="payment-logo payment-logo-twint">TWINT</span>
                            <span class="payment-method-name">TWINT</span>
                        </div>
                        <p class="payment-method-desc">Bezahle schnell und sicher mit TWINT</p>
                    </div>
                </label>
                <?php endif; ?>

                <?php if ($paymentMethods['credit_card'] ?? true): ?>
                <label class="payment-method" data-method="credit_card">
                    <input type="radio" name="payment" value="credit_card">
                    <div class="payment-method-inner">
                        <div class="payment-method-header">
                            <span class="payment-logo">VISA / MC</span>
                            <span class="payment-method-name">Kreditkarte</span>
                        </div>
                        <p class="payment-method-desc">Visa oder Mastercard</p>
                    </div>
                </label>
                <?php endif; ?>

                <?php if ($paymentMethods['invoice'] ?? true): ?>
                <label class="payment-method" data-method="invoice">
                    <input type="radio" name="payment" value="invoice">
                    <div class="payment-method-inner">
                        <div class="payment-method-header">
                            <span class="payment-logo">Rechnung</span>
                            <span class="payment-method-name">Kauf auf Rechnung</span>
                        </div>
                        <p class="payment-method-desc">Bezahle bequem innert 30 Tagen</p>
                    </div>
                </label>
                <?php endif; ?>
            </div>

            <!-- TWINT Payment Area -->
            <div class="payment-detail" id="paymentTwint" style="display:none">
                <div class="twint-container">
                    <p>Scanne den QR-Code mit deiner TWINT-App:</p>
                    <div class="twint-qr" id="twintQr">
                        <!-- Generated QR placeholder -->
                        <svg viewBox="0 0 200 200" width="200" height="200">
                            <rect width="200" height="200" fill="white"/>
                            <g fill="black">
                                <!-- QR-like pattern -->
                                <rect x="10" y="10" width="50" height="50"/>
                                <rect x="140" y="10" width="50" height="50"/>
                                <rect x="10" y="140" width="50" height="50"/>
                                <rect x="20" y="20" width="30" height="30" fill="white"/>
                                <rect x="150" y="20" width="30" height="30" fill="white"/>
                                <rect x="20" y="150" width="30" height="30" fill="white"/>
                                <rect x="27" y="27" width="16" height="16"/>
                                <rect x="157" y="27" width="16" height="16"/>
                                <rect x="27" y="157" width="16" height="16"/>
                                <!-- Data pattern -->
                                <rect x="70" y="10" width="10" height="10"/>
                                <rect x="90" y="10" width="10" height="10"/>
                                <rect x="110" y="10" width="10" height="10"/>
                                <rect x="70" y="30" width="10" height="10"/>
                                <rect x="100" y="30" width="10" height="10"/>
                                <rect x="120" y="30" width="10" height="10"/>
                                <rect x="80" y="50" width="10" height="10"/>
                                <rect x="110" y="50" width="10" height="10"/>
                                <rect x="10" y="70" width="10" height="10"/>
                                <rect x="30" y="70" width="10" height="10"/>
                                <rect x="50" y="70" width="10" height="10"/>
                                <rect x="70" y="70" width="10" height="10"/>
                                <rect x="90" y="70" width="10" height="10"/>
                                <rect x="110" y="70" width="10" height="10"/>
                                <rect x="130" y="70" width="10" height="10"/>
                                <rect x="150" y="70" width="10" height="10"/>
                                <rect x="170" y="70" width="10" height="10"/>
                                <rect x="20" y="90" width="10" height="10"/>
                                <rect x="50" y="90" width="10" height="10"/>
                                <rect x="80" y="90" width="10" height="10"/>
                                <rect x="100" y="90" width="10" height="10"/>
                                <rect x="130" y="90" width="10" height="10"/>
                                <rect x="160" y="90" width="10" height="10"/>
                                <rect x="70" y="110" width="10" height="10"/>
                                <rect x="90" y="110" width="10" height="10"/>
                                <rect x="120" y="110" width="10" height="10"/>
                                <rect x="70" y="130" width="10" height="10"/>
                                <rect x="100" y="130" width="10" height="10"/>
                                <rect x="140" y="130" width="10" height="10"/>
                                <rect x="160" y="130" width="10" height="10"/>
                                <rect x="180" y="130" width="10" height="10"/>
                                <rect x="140" y="150" width="10" height="10"/>
                                <rect x="170" y="150" width="10" height="10"/>
                                <rect x="140" y="170" width="10" height="10"/>
                                <rect x="160" y="170" width="10" height="10"/>
                                <rect x="180" y="170" width="10" height="10"/>
                            </g>
                        </svg>
                    </div>
                    <div class="twint-waiting" id="twintWaiting" style="display:none">
                        <div class="spinner"></div>
                        <p>Warte auf Bestätigung in der TWINT-App...</p>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="simulateTwint()">
                        Zahlung in TWINT-App bestätigen
                    </button>
                </div>
            </div>

            <!-- Credit Card Payment Area -->
            <!-- USABILITY-HOOK: Kreditkartenformular -->
            <div class="payment-detail" id="paymentCreditCard" style="display:none">
                <form id="ccForm" class="cc-form" onsubmit="return processCreditCard(event)">
                    <div class="form-group">
                        <label for="ccName">Karteninhaber *</label>
                        <input type="text" id="ccName" required placeholder="Wie auf der Karte">
                    </div>
                    <div class="form-group">
                        <label for="ccNumber">Kartennummer *</label>
                        <div class="cc-number-wrap">
                            <input type="text" id="ccNumber" required maxlength="19" placeholder="1234 5678 9012 3456"
                                   oninput="formatCardNumber(this); detectCardType(this.value)">
                            <span class="cc-type-icon" id="ccTypeIcon"></span>
                        </div>
                    </div>
                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label for="ccExpiry">Gültig bis *</label>
                            <input type="text" id="ccExpiry" required maxlength="5" placeholder="MM/JJ"
                                   oninput="formatExpiry(this)">
                        </div>
                        <div class="form-group">
                            <label for="ccCvv">CVV *</label>
                            <input type="text" id="ccCvv" required maxlength="4" placeholder="123" inputmode="numeric">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Jetzt bezahlen</button>
                </form>
            </div>

            <!-- Invoice Payment Area -->
            <div class="payment-detail" id="paymentInvoice" style="display:none">
                <div class="invoice-form">
                    <p>Für den Kauf auf Rechnung benötigen wir dein Geburtsdatum zur Bonitätsprüfung.</p>
                    <div class="form-group">
                        <label for="invoiceDob">Geburtsdatum *</label>
                        <input type="date" id="invoiceDob" required max="2008-01-01">
                    </div>
                    <p class="legal-text">Mit dem Klick auf «Auf Rechnung bestellen» akzeptierst du die Zahlungsbedingungen. Die Rechnung wird innert 30 Tagen fällig. Es gelten die AGB.</p>
                    <button class="btn btn-primary btn-block" onclick="processInvoice()">Auf Rechnung bestellen</button>
                </div>
            </div>

            <div class="form-actions" id="paymentActions">
                <button class="btn btn-outline" onclick="goToStep(1)">Zurück</button>
            </div>
        </div>

        <!-- Step 3 is handled by confirmation page -->
    </div>

    <!-- Order Summary Sidebar -->
    <aside class="checkout-summary" id="checkoutSummary">
        <h3>Deine Bestellung</h3>
        <div id="checkoutItems"></div>
        <div class="summary-divider"></div>
        <div class="summary-row">
            <span>Zwischensumme</span>
            <span id="checkoutSubtotal">CHF 0.00</span>
        </div>
        <div class="summary-row">
            <span>Versand</span>
            <span id="checkoutShipping">CHF 0.00</span>
        </div>
        <div class="summary-row">
            <span>MwSt. (<?= $settings['vat_rate'] ?? 8.1 ?>%)</span>
            <span id="checkoutVat">CHF 0.00</span>
        </div>
        <div class="summary-row summary-total">
            <span>Total</span>
            <span id="checkoutTotal">CHF 0.00</span>
        </div>
    </aside>
</div>

<!-- 3D Secure Modal -->
<div class="modal-overlay" id="threeDSecureModal" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3>3D Secure Verifizierung</h3>
        </div>
        <div class="modal-body">
            <div class="three-d-secure">
                <div class="bank-logo">Ihre Bank</div>
                <p>Bitte bestätigen Sie die Zahlung von <strong id="threeDAmount"></strong></p>
                <p class="small-text">Eine Push-Benachrichtigung wurde an Ihre Banking-App gesendet.</p>
                <div class="spinner"></div>
                <p class="small-text" id="threeDStatus">Warte auf Freigabe...</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
let selectedPayment = '';

document.addEventListener('DOMContentLoaded', () => {
    // Check cart not empty
    if (Cart.getItems().length === 0) {
        window.location.href = '/cart';
        return;
    }
    renderCheckoutSummary();
    setupPaymentListeners();
});

function setupPaymentListeners() {
    document.querySelectorAll('input[name="payment"]').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedPayment = this.value;
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
            this.closest('.payment-method').classList.add('selected');

            // Show relevant detail
            document.querySelectorAll('.payment-detail').forEach(d => d.style.display = 'none');
            const detailMap = {twint: 'paymentTwint', credit_card: 'paymentCreditCard', invoice: 'paymentInvoice'};
            if (detailMap[selectedPayment]) {
                document.getElementById(detailMap[selectedPayment]).style.display = 'block';
            }
        });
    });
}

function goToStep(step) {
    if (step === 2) {
        // Validate step 1
        const form = document.getElementById('addressForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }
    }

    currentStep = step;
    document.querySelectorAll('.checkout-step-content').forEach(el => el.style.display = 'none');
    document.getElementById('step' + step).style.display = 'block';

    // Update step indicators
    document.querySelectorAll('.checkout-steps .step').forEach(s => {
        const n = parseInt(s.dataset.step);
        s.classList.toggle('active', n === step);
        s.classList.toggle('completed', n < step);
    });

    window.scrollTo({top: 0, behavior: 'smooth'});
    return false;
}

function renderCheckoutSummary() {
    const items = Cart.getItems();
    const s = window.SHOP_SETTINGS;
    const container = document.getElementById('checkoutItems');

    container.innerHTML = items.map(item => {
        const attrText = Object.entries(item.attributes || {}).map(([k,v]) => v).join(', ');
        return `<div class="checkout-item">
            <div class="checkout-item-img"><img src="${escapeHtml(item.image)}" alt=""></div>
            <div class="checkout-item-info">
                <span class="checkout-item-name">${escapeHtml(item.name)}</span>
                ${attrText ? `<span class="checkout-item-attrs">${escapeHtml(attrText)}</span>` : ''}
                <span class="checkout-item-qty">Menge: ${item.qty}</span>
            </div>
            <span class="checkout-item-price">${formatPrice(item.price * item.qty)}</span>
        </div>`;
    }).join('');

    const subtotal = items.reduce((sum, i) => sum + i.price * i.qty, 0);
    const shipping = subtotal >= s.free_shipping_threshold ? 0 : s.shipping_cost;
    const vat = Math.round(subtotal * s.vat_rate / 100 * 100) / 100;
    const total = subtotal + shipping + vat;

    document.getElementById('checkoutSubtotal').textContent = formatPrice(subtotal);
    document.getElementById('checkoutShipping').textContent = shipping === 0 ? 'Kostenlos' : formatPrice(shipping);
    document.getElementById('checkoutVat').textContent = formatPrice(vat);
    document.getElementById('checkoutTotal').textContent = formatPrice(total);
}

// --- TWINT ---
function simulateTwint() {
    const qr = document.getElementById('twintQr');
    const waiting = document.getElementById('twintWaiting');
    qr.style.opacity = '0.3';
    waiting.style.display = 'flex';

    setTimeout(() => {
        placeOrder('twint');
    }, 2500);
}

// --- Credit Card ---
function formatCardNumber(input) {
    let val = input.value.replace(/\D/g, '');
    val = val.replace(/(.{4})/g, '$1 ').trim();
    input.value = val;
}

function formatExpiry(input) {
    let val = input.value.replace(/\D/g, '');
    if (val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
    input.value = val;
}

function detectCardType(number) {
    const num = number.replace(/\D/g, '');
    const icon = document.getElementById('ccTypeIcon');
    if (/^4/.test(num)) {
        icon.textContent = 'VISA';
        icon.className = 'cc-type-icon cc-visa';
    } else if (/^(5[1-5]|2[2-7])/.test(num)) {
        icon.textContent = 'MC';
        icon.className = 'cc-type-icon cc-mc';
    } else {
        icon.textContent = '';
        icon.className = 'cc-type-icon';
    }
}

function luhnCheck(num) {
    num = num.replace(/\D/g, '');
    if (num.length < 13) return false;
    let sum = 0, alt = false;
    for (let i = num.length - 1; i >= 0; i--) {
        let n = parseInt(num[i]);
        if (alt) { n *= 2; if (n > 9) n -= 9; }
        sum += n;
        alt = !alt;
    }
    return sum % 10 === 0;
}

function processCreditCard(e) {
    e.preventDefault();

    const number = document.getElementById('ccNumber').value;
    if (!luhnCheck(number)) {
        showToast('Ungültige Kartennummer', 'error');
        return false;
    }

    // Show 3D Secure modal
    const items = Cart.getItems();
    const s = window.SHOP_SETTINGS;
    const subtotal = items.reduce((sum, i) => sum + i.price * i.qty, 0);
    const shipping = subtotal >= s.free_shipping_threshold ? 0 : s.shipping_cost;
    const vat = Math.round(subtotal * s.vat_rate / 100 * 100) / 100;
    const total = subtotal + shipping + vat;

    document.getElementById('threeDAmount').textContent = formatPrice(total);
    document.getElementById('threeDSecureModal').style.display = 'flex';

    setTimeout(() => {
        document.getElementById('threeDStatus').textContent = 'Zahlung bestätigt!';
        setTimeout(() => {
            document.getElementById('threeDSecureModal').style.display = 'none';
            placeOrder('credit_card');
        }, 800);
    }, 3000);

    return false;
}

// --- Invoice ---
function processInvoice() {
    const dob = document.getElementById('invoiceDob').value;
    if (!dob) {
        showToast('Bitte gib dein Geburtsdatum ein', 'error');
        return;
    }
    placeOrder('invoice');
}

// --- Place Order ---
function placeOrder(method) {
    const items = Cart.getItems();
    const s = window.SHOP_SETTINGS;
    const subtotal = items.reduce((sum, i) => sum + i.price * i.qty, 0);
    const shipping = subtotal >= s.free_shipping_threshold ? 0 : s.shipping_cost;
    const vat = Math.round(subtotal * s.vat_rate / 100 * 100) / 100;
    const total = subtotal + shipping + vat;

    const orderData = {
        items: items,
        address: {
            firstName: document.getElementById('firstName').value,
            lastName: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            street: document.getElementById('street').value,
            street2: document.getElementById('street2').value,
            zip: document.getElementById('zip').value,
            city: document.getElementById('city').value,
            canton: document.getElementById('canton').value,
        },
        payment_method: method,
        subtotal: subtotal,
        shipping: shipping,
        vat: vat,
        total: total,
    };

    fetch('/api.php?action=place_order', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(orderData),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Save order info for confirmation page
            sessionStorage.setItem('lastOrder', JSON.stringify({
                ...orderData,
                order_number: data.order_number,
            }));
            Cart.clear();
            window.location.href = '/confirmation';
        }
    });
}
</script>
