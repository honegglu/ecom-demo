<?php
/**
 * Core functions for the MODO demo webshop
 */

define('DATA_DIR', __DIR__ . '/../data/');

// --- Settings ---

function get_settings(): array {
    $file = DATA_DIR . 'settings.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function save_settings(array $settings): bool {
    $file = DATA_DIR . 'settings.json';
    return file_put_contents($file, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

// --- Products ---

function get_products(): array {
    $file = DATA_DIR . 'products.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function save_products(array $products): bool {
    $file = DATA_DIR . 'products.json';
    return file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function get_product_by_slug(string $slug): ?array {
    $products = get_products();
    foreach ($products as $product) {
        if ($product['slug'] === $slug) return $product;
    }
    return null;
}

function get_product_by_id(int $id): ?array {
    $products = get_products();
    foreach ($products as $product) {
        if ($product['id'] === $id) return $product;
    }
    return null;
}

function get_categories(): array {
    $products = get_products();
    $categories = [];
    foreach ($products as $p) {
        if (!empty($p['category']) && !in_array($p['category'], $categories)) {
            $categories[] = $p['category'];
        }
    }
    sort($categories);
    return $categories;
}

function get_effective_price(array $product): float {
    return $product['sale_price'] ?? $product['regular_price'];
}

function get_price_range(array $product): array {
    if (empty($product['variations'])) {
        return ['min' => get_effective_price($product), 'max' => get_effective_price($product)];
    }
    $prices = array_map(fn($v) => $v['sale_price'] ?? $v['price'], $product['variations']);
    return ['min' => min($prices), 'max' => max($prices)];
}

// --- Price formatting ---

function format_price(float $price, ?array $settings = null): string {
    if ($settings === null) $settings = get_settings();
    $currency = $settings['currency'] ?? 'CHF';
    return $currency . ' ' . number_format($price, 2, '.', "'");
}

// --- Orders ---

function generate_order_number(): string {
    return 'MO-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
}

// --- VAT calculation ---

function calculate_vat(float $subtotal, float $rate): float {
    return round($subtotal * $rate / 100, 2);
}

// --- Shipping ---

function calculate_shipping(float $subtotal, array $settings): float {
    $threshold = $settings['free_shipping_threshold'] ?? 75.00;
    if ($subtotal >= $threshold) return 0.00;
    return $settings['shipping_cost'] ?? 7.90;
}

// --- Luhn check (for credit card validation) ---

function luhn_check(string $number): bool {
    $number = preg_replace('/\D/', '', $number);
    $len = strlen($number);
    if ($len < 13 || $len > 19) return false;
    $sum = 0;
    $alt = false;
    for ($i = $len - 1; $i >= 0; $i--) {
        $n = (int)$number[$i];
        if ($alt) {
            $n *= 2;
            if ($n > 9) $n -= 9;
        }
        $sum += $n;
        $alt = !$alt;
    }
    return $sum % 10 === 0;
}

function detect_card_type(string $number): string {
    $number = preg_replace('/\D/', '', $number);
    if (preg_match('/^4/', $number)) return 'visa';
    if (preg_match('/^(5[1-5]|2[2-7])/', $number)) return 'mastercard';
    if (preg_match('/^3[47]/', $number)) return 'amex';
    return 'unknown';
}
