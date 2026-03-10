/**
 * MODO Demo Webshop – Main JavaScript
 * Cart management, UI helpers, and global functionality
 */

// ============================================
// Cart (LocalStorage-based)
// ============================================
const Cart = {
    STORAGE_KEY: 'modo_cart',

    getItems() {
        try {
            return JSON.parse(localStorage.getItem(this.STORAGE_KEY)) || [];
        } catch {
            return [];
        }
    },

    save(items) {
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(items));
        this.updateBadge();
    },

    add(item) {
        const items = this.getItems();
        // Check if same product + variation already in cart
        const existingIndex = items.findIndex(i =>
            i.productId === item.productId &&
            i.variationId === item.variationId &&
            JSON.stringify(i.attributes) === JSON.stringify(item.attributes)
        );

        if (existingIndex >= 0) {
            items[existingIndex].qty += item.qty;
        } else {
            items.push(item);
        }

        this.save(items);
        showToast('Artikel zum Warenkorb hinzugefügt');
        this.animateBadge();
    },

    updateQty(index, delta) {
        const items = this.getItems();
        if (items[index]) {
            items[index].qty += delta;
            if (items[index].qty < 1) items[index].qty = 1;
            this.save(items);
        }
    },

    remove(index) {
        const items = this.getItems();
        items.splice(index, 1);
        this.save(items);
    },

    clear() {
        localStorage.removeItem(this.STORAGE_KEY);
        this.updateBadge();
    },

    getCount() {
        return this.getItems().reduce((sum, item) => sum + item.qty, 0);
    },

    getTotal() {
        return this.getItems().reduce((sum, item) => sum + item.price * item.qty, 0);
    },

    updateBadge() {
        const badge = document.getElementById('cartCount');
        if (badge) {
            const count = this.getCount();
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    },

    animateBadge() {
        const badge = document.getElementById('cartCount');
        if (badge) {
            badge.classList.add('pulse');
            setTimeout(() => badge.classList.remove('pulse'), 400);
        }
    }
};

// Initialize cart badge on every page
document.addEventListener('DOMContentLoaded', () => {
    Cart.updateBadge();
});


// ============================================
// Toast notifications
// ============================================
function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = 'toast visible' + (type ? ' ' + type : '');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.remove('visible');
    }, 2500);
}


// ============================================
// Mobile menu
// ============================================
function toggleMobileMenu() {
    const nav = document.getElementById('mainNav');
    const btn = document.querySelector('.mobile-menu-toggle');
    nav.classList.toggle('open');
    btn.classList.toggle('active');
}


// ============================================
// Price formatting
// ============================================
function formatPrice(amount) {
    const s = window.SHOP_SETTINGS || {};
    const currency = s.currency || 'CHF';
    const formatted = Number(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, "'");
    return currency + ' ' + formatted;
}


// ============================================
// HTML escaping
// ============================================
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}


// ============================================
// Search functionality
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');

    // Pre-fill search input from URL
    if (searchInput) {
        const params = new URLSearchParams(window.location.search);
        const q = params.get('search');
        if (q) searchInput.value = q;
    }

    // USABILITY-HOOK: Suchverhalten
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const val = searchInput.value.trim();
            if (val) {
                window.location.href = '/?search=' + encodeURIComponent(val);
            } else {
                window.location.href = '/';
            }
        });
    }
});


// ============================================
// Badge pulse animation (CSS injected)
// ============================================
(function() {
    const style = document.createElement('style');
    style.textContent = `
        .cart-count.pulse {
            animation: badgePulse .4s ease;
        }
        @keyframes badgePulse {
            0% { transform: translate(4px, -4px) scale(1); }
            50% { transform: translate(4px, -4px) scale(1.4); }
            100% { transform: translate(4px, -4px) scale(1); }
        }
        .cart-count { display: none; }
    `;
    document.head.appendChild(style);
})();
