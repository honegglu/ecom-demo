<?php
/**
 * Home / Product Listing Page
 */
$allProducts = get_products();
$currentCategory = $_GET['category'] ?? '';
$currentSearch = $_GET['search'] ?? '';
$currentSort = $_GET['sort'] ?? 'name';

// Filter
if ($currentCategory) {
    $allProducts = array_filter($allProducts, fn($p) => $p['category'] === $currentCategory);
}
if ($currentSearch) {
    $s = mb_strtolower($currentSearch);
    $allProducts = array_filter($allProducts, fn($p) =>
        mb_strpos(mb_strtolower($p['name']), $s) !== false ||
        mb_strpos(mb_strtolower($p['short_description'] ?? ''), $s) !== false ||
        mb_strpos(mb_strtolower($p['category'] ?? ''), $s) !== false
    );
}

// Sort
$allProducts = array_values($allProducts);
usort($allProducts, function($a, $b) use ($currentSort) {
    return match($currentSort) {
        'price_asc' => get_effective_price($a) <=> get_effective_price($b),
        'price_desc' => get_effective_price($b) <=> get_effective_price($a),
        default => strcmp($a['name'], $b['name']),
    };
});
?>

<!-- USABILITY-HOOK: Breadcrumb-Navigation -->
<nav class="breadcrumb">
    <a href="/">Start</a>
    <?php if ($currentCategory): ?>
        <span class="sep">›</span>
        <span><?= htmlspecialchars($currentCategory) ?></span>
    <?php elseif ($currentSearch): ?>
        <span class="sep">›</span>
        <span>Suche: «<?= htmlspecialchars($currentSearch) ?>»</span>
    <?php else: ?>
        <span class="sep">›</span>
        <span>Alle Produkte</span>
    <?php endif; ?>
</nav>

<div class="listing-header">
    <h1>
        <?php if ($currentCategory): ?>
            <?= htmlspecialchars($currentCategory) ?>
        <?php elseif ($currentSearch): ?>
            Suchergebnisse für «<?= htmlspecialchars($currentSearch) ?>»
        <?php else: ?>
            Alle Produkte
        <?php endif; ?>
        <span class="product-count">(<?= count($allProducts) ?> Produkte)</span>
    </h1>

    <!-- USABILITY-HOOK: Sortierung -->
    <div class="listing-controls">
        <select class="sort-select" id="sortSelect" onchange="applySort(this.value)">
            <option value="name" <?= $currentSort === 'name' ? 'selected' : '' ?>>Name A–Z</option>
            <option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>Preis aufsteigend</option>
            <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>Preis absteigend</option>
        </select>
    </div>
</div>

<?php if (empty($allProducts)): ?>
    <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M8 11h6"/></svg>
        <h2>Keine Produkte gefunden</h2>
        <p>Versuche es mit einem anderen Suchbegriff oder einer anderen Kategorie.</p>
        <a href="/" class="btn btn-primary">Alle Produkte anzeigen</a>
    </div>
<?php else: ?>
    <!-- USABILITY-HOOK: Produktraster -->
    <div class="product-grid">
        <?php foreach ($allProducts as $product): ?>
            <?php
            $priceRange = get_price_range($product);
            $effectivePrice = get_effective_price($product);
            $hasVariations = !empty($product['variations']);
            $isOnSale = $product['sale_price'] !== null;
            ?>
            <article class="product-card" data-category="<?= htmlspecialchars($product['category']) ?>">
                <a href="/product/<?= htmlspecialchars($product['slug']) ?>" class="product-card-link">
                    <div class="product-card-image">
                        <?php if ($isOnSale): ?>
                            <span class="badge badge-sale">Sale</span>
                        <?php endif; ?>
                        <?php if ($product['featured'] ?? false): ?>
                            <span class="badge badge-featured">Beliebt</span>
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($product['images'][0] ?? '') ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy">
                    </div>
                    <div class="product-card-info">
                        <span class="product-card-category"><?= htmlspecialchars($product['category']) ?></span>
                        <h3 class="product-card-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-card-desc"><?= htmlspecialchars($product['short_description']) ?></p>
                        <div class="product-card-price">
                            <?php if ($hasVariations && $priceRange['min'] !== $priceRange['max']): ?>
                                <span class="price">ab <?= format_price($priceRange['min'], $settings) ?></span>
                            <?php elseif ($isOnSale): ?>
    <span class="price"><?= format_price($product['regular_price'], $settings) ?></span>
    <span class="price-savings">8% Rabatt</span>
                            <?php else: ?>
                                <span class="price"><?= format_price($effectivePrice, $settings) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($product['attributes'])): ?>
                            <div class="product-card-variants">
                                <?php foreach ($product['attributes'] as $attr): ?>
                                    <?php if ($attr['name'] === 'Farbe'): ?>
                                        <div class="color-dots">
                                            <?php foreach ($attr['options'] as $color): ?>
                                                <span class="color-dot" title="<?= htmlspecialchars($color) ?>" style="background:<?= getColorHex($color) ?>"></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
function getColorHex(string $name): string {
    $map = [
        'Rot' => '#e74c3c', 'Grün' => '#27ae60', 'Blau' => '#2980b9',
        'Grau' => '#95a5a6', 'Gelb' => '#f1c40f', 'Schwarz' => '#2c3e50',
        'Weiss' => '#ecf0f1',
    ];
    return $map[$name] ?? '#ccc';
}
?>

<script>
function applySort(val) {
    const url = new URL(window.location);
    url.searchParams.set('sort', val);
    window.location = url;
}
</script>
