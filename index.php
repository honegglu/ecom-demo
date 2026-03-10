<?php
/**
 * MODO Demo Webshop – Main Router
 */
session_start();
require_once __DIR__ . '/includes/functions.php';

$settings = get_settings();
$route = trim($_GET['route'] ?? '', '/');

// Parse route
switch (true) {
    case $route === '' || $route === 'home':
        $page = 'home';
        $pageTitle = 'Startseite';
        break;

    case $route === 'cart':
        $page = 'cart';
        $pageTitle = 'Warenkorb';
        break;

    case $route === 'checkout':
        $page = 'checkout';
        $pageTitle = 'Kasse';
        break;

    case $route === 'confirmation':
        $page = 'confirmation';
        $pageTitle = 'Bestellbestätigung';
        break;

    case $route === 'settings':
        $page = 'settings';
        $pageTitle = 'Einstellungen';
        break;

    case str_starts_with($route, 'product/'):
        $slug = substr($route, 8);
        $product = get_product_by_slug($slug);
        if (!$product) {
            http_response_code(404);
            $page = '404';
            $pageTitle = 'Nicht gefunden';
        } else {
            $page = 'product';
            $pageTitle = $product['name'];
        }
        break;

    default:
        http_response_code(404);
        $page = '404';
        $pageTitle = 'Nicht gefunden';
        break;
}

// Render
include __DIR__ . '/templates/layout.php';
