<?php
$shopName = $settings['shop_name'] ?? 'MODO';
$slogan = $settings['slogan'] ?? '';
$primaryColor = $settings['primary_color'] ?? '#1a1a2e';
$secondaryColor = $settings['secondary_color'] ?? '#e94560';
$fontFamily = $settings['font_family'] ?? 'Inter';
$fontSizeBase = $settings['font_size_base'] ?? '16';
$fontSizeHeading = $settings['font_size_heading'] ?? '28';
$logoUrl = $settings['logo_url'] ?? '';
$categories = get_categories();
?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> – <?= htmlspecialchars($shopName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($fontFamily) ?>:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root {
            --color-primary: <?= htmlspecialchars($primaryColor) ?>;
            --color-secondary: <?= htmlspecialchars($secondaryColor) ?>;
            --font-family: '<?= htmlspecialchars($fontFamily) ?>', system-ui, sans-serif;
            --font-size-base: <?= (int)$fontSizeBase ?>px;
            --font-size-heading: <?= (int)$fontSizeHeading ?>px;
        }
    </style>
</head>
<body>

<!-- USABILITY-HOOK: Header-Navigation -->
<header class="site-header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-inner">
                <span class="header-info">Kostenloser Versand ab CHF 75.00</span>
                <span class="header-info">Lieferung in 2–3 Werktagen</span>
            </div>
        </div>
    </div>
    <div class="header-main">
        <div class="container">
            <div class="header-main-inner">
                <button class="mobile-menu-toggle" aria-label="Menü öffnen" onclick="toggleMobileMenu()">
                    <span></span><span></span><span></span>
                </button>

                <a href="/" class="logo">
                    <?php if ($logoUrl): ?>
                        <img src="/<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($shopName) ?>">
                    <?php else: ?>
                        <span class="logo-text"><?= htmlspecialchars($shopName) ?></span>
                    <?php endif; ?>
                </a>

                <!-- USABILITY-HOOK: Suchfunktion -->
                <div class="header-search">
                    <form action="/" method="GET" class="search-form" id="searchForm">
                        <input type="text" name="search" placeholder="Produkte suchen..." class="search-input" id="searchInput" autocomplete="off">
                        <button type="submit" class="search-btn" aria-label="Suchen">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </button>
                    </form>
                </div>

                <div class="header-actions">
                    <!-- USABILITY-HOOK: Warenkorb-Icon -->
                    <a href="/cart" class="cart-link" aria-label="Warenkorb">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                       
                    </a>
                </div>
            </div>
        </div>
    </div>
    <nav class="header-nav" id="mainNav">
        <div class="container">
            <ul class="nav-list">
                <li><a href="/" class="<?= $page === 'home' ? 'active' : '' ?>">Alle Produkte</a></li>
                <?php foreach ($categories as $cat): ?>
                    <li><a href="/?category=<?= urlencode($cat) ?>" class="<?= (isset($_GET['category']) && $_GET['category'] === $cat) ? 'active' : '' ?>"><?= htmlspecialchars($cat) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<main class="site-main">
    <div class="container">
        <?php
        $templateFile = __DIR__ . '/' . $page . '.php';
        if (file_exists($templateFile)) {
            include $templateFile;
        } else {
            echo '<div class="page-not-found"><h1>404</h1><p>Seite nicht gefunden</p><a href="/" class="btn btn-primary">Zurück zum Shop</a></div>';
        }
        ?>
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4><?= htmlspecialchars($shopName) ?></h4>
                <p><?= htmlspecialchars($slogan) ?></p>
            </div>
            <div class="footer-col">
                <h4>Kundenservice</h4>
                <ul>
                    <li><a href="#">Versand & Lieferung</a></li>
                    <li><a href="#">Rückgabe & Umtausch</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Kontakt</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Rechtliches</h4>
                <ul>
                    <li><a href="#">AGB</a></li>
                    <li><a href="#">Datenschutz</a></li>
                    <li><a href="#">Impressum</a></li>
                    <li><a href="#">Widerrufsrecht</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Kontakt</h4>
                <p><?= htmlspecialchars($settings['contact_email'] ?? '') ?></p>
                <p><?= htmlspecialchars($settings['contact_phone'] ?? '') ?></p>
                <p><?= htmlspecialchars($settings['contact_address'] ?? '') ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($shopName) ?>. Alle Rechte vorbehalten.</p>
            <div class="payment-icons">
                <span class="payment-icon">TWINT</span>
                <span class="payment-icon">VISA</span>
                <span class="payment-icon">MC</span>
            </div>
        </div>
    </div>
</footer>

<!-- Toast notification -->
<div class="toast" id="toast"></div>

<script>
    window.SHOP_SETTINGS = <?= json_encode([
        'currency' => $settings['currency'] ?? 'CHF',
        'vat_rate' => (float)($settings['vat_rate'] ?? 8.1),
        'shipping_cost' => (float)($settings['shipping_cost'] ?? 7.90),
        'free_shipping_threshold' => (float)($settings['free_shipping_threshold'] ?? 75.00),
        'payment_methods' => $settings['payment_methods'] ?? ['twint' => true, 'credit_card' => true, 'invoice' => true],
    ]) ?>;
</script>
<script src="/assets/js/app.js"></script>
</body>
</html>
