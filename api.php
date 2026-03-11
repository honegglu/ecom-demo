<?php
/**
 * MODO Demo Webshop – API endpoints for AJAX calls
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/includes/functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // --- Product data ---
    case 'get_products':
        $products = get_products();
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'name';

        if ($category) {
            $products = array_filter($products, fn($p) => $p['category'] === $category);
        }
        if ($search) {
            $s = mb_strtolower($search);
            $products = array_filter($products, fn($p) =>
                mb_strpos(mb_strtolower($p['name']), $s) !== false ||
                mb_strpos(mb_strtolower($p['short_description']), $s) !== false ||
                mb_strpos(mb_strtolower($p['category']), $s) !== false
            );
        }

        // Sort
        $products = array_values($products);
        usort($products, function($a, $b) use ($sort) {
            return match($sort) {
                'price_asc' => get_effective_price($a) <=> get_effective_price($b),
                'price_desc' => get_effective_price($b) <=> get_effective_price($a),
                'name' => strcmp($a['name'], $b['name']),
                default => strcmp($a['name'], $b['name']),
            };
        });

        echo json_encode(['success' => true, 'products' => $products]);
        break;

    case 'get_product':
        $slug = $_GET['slug'] ?? '';
        $product = get_product_by_slug($slug);
        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Product not found']);
        }
        break;

    // --- Settings ---
    case 'get_settings':
        echo json_encode(['success' => true, 'settings' => get_settings()]);
        break;

    case 'save_settings':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $current = get_settings();
            $merged = array_merge($current, $input);
            save_settings($merged);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid input']);
        }
        break;

    case 'save_product':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['id'])) {
            $products = get_products();
            $found = false;
            foreach ($products as &$p) {
                if ($p['id'] === (int)$input['id']) {
                    if (isset($input['name'])) $p['name'] = $input['name'];
                    if (isset($input['regular_price'])) $p['regular_price'] = (float)$input['regular_price'];
                    if (isset($input['sale_price'])) $p['sale_price'] = $input['sale_price'] === '' ? null : (float)$input['sale_price'];
                    if (isset($input['stock_qty'])) $p['stock_qty'] = (int)$input['stock_qty'];
                    if (isset($input['in_stock'])) $p['in_stock'] = (bool)$input['in_stock'];
                    if (isset($input['short_description'])) $p['short_description'] = $input['short_description'];
                    if (isset($input['variations'])) $p['variations'] = $input['variations'];
                    $found = true;
                    break;
                }
            }
            unset($p);
            if ($found) {
                save_products($products);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Product not found']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid input']);
        }
        break;

    // --- Upload logo ---
    case 'upload_logo':
        if (isset($_FILES['logo'])) {
            $allowed = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'];
            if (in_array($_FILES['logo']['type'], $allowed)) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $ext;
                $dest = __DIR__ . '/assets/images/' . $filename;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                    $settings = get_settings();
                    $settings['logo_url'] = 'assets/images/' . $filename;
                    save_settings($settings);
                    echo json_encode(['success' => true, 'url' => 'assets/images/' . $filename]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Upload failed']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No file']);
        }
        break;

    // --- Upload product image ---
    case 'upload_product_image':
        if (isset($_FILES['image']) && isset($_POST['product_id'])) {
            $allowed = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];
            $fileType = $_FILES['image']['type'];
            if (in_array($fileType, $allowed)) {
                $productId = (int)$_POST['product_id'];
                $filename = 'product_' . $productId . '_' . time() . '.jpg';
                $dest = __DIR__ . '/assets/images/' . $filename;
                $tmpPath = $_FILES['image']['tmp_name'];

                // Server-side optimization with GD
                $saved = false;
                if (function_exists('imagecreatefromstring')) {
                    $imgData = file_get_contents($tmpPath);
                    $src = @imagecreatefromstring($imgData);
                    if ($src) {
                        $w = imagesx($src);
                        $h = imagesy($src);
                        // Max width 1200px for web
                        $maxW = 1200;
                        if ($w > $maxW) {
                            $newH = (int)round($h * ($maxW / $w));
                            $resized = imagecreatetruecolor($maxW, $newH);
                            imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxW, $newH, $w, $h);
                            imagedestroy($src);
                            $src = $resized;
                        }
                        // Save as JPEG with 82% quality (good balance)
                        $saved = imagejpeg($src, $dest, 82);
                        imagedestroy($src);
                    }
                }
                // Fallback: just move the file if GD failed
                if (!$saved) {
                    $saved = move_uploaded_file($tmpPath, $dest);
                }

                if ($saved) {
                    $imageUrl = '/assets/images/' . $filename;
                    $products = get_products();
                    foreach ($products as &$p) {
                        if ($p['id'] === $productId) {
                            if (!isset($p['images']) || !is_array($p['images'])) {
                                $p['images'] = [];
                            }
                            $p['images'][] = $imageUrl;
                            break;
                        }
                    }
                    unset($p);
                    save_products($products);
                    echo json_encode(['success' => true, 'url' => $imageUrl]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Upload fehlgeschlagen']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Ungültiger Dateityp']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Keine Datei oder Produkt-ID']);
        }
        break;

    // --- Delete product image ---
    case 'delete_product_image':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['product_id']) && isset($input['image_url'])) {
            $products = get_products();
            $found = false;
            foreach ($products as &$p) {
                if ($p['id'] === (int)$input['product_id']) {
                    $p['images'] = array_values(array_filter($p['images'], fn($img) => $img !== $input['image_url']));
                    $found = true;
                    break;
                }
            }
            unset($p);
            if ($found) {
                save_products($products);
                // Delete file if it's a local upload
                $localPath = __DIR__ . $input['image_url'];
                if (str_starts_with($input['image_url'], '/assets/images/') && file_exists($localPath)) {
                    unlink($localPath);
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Produkt nicht gefunden']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Ungültige Eingabe']);
        }
        break;

    // --- Place order (simulated) ---
    case 'place_order':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $orderNumber = generate_order_number();
            // Simulate processing delay
            echo json_encode([
                'success' => true,
                'order_number' => $orderNumber,
                'message' => 'Bestellung erfolgreich aufgegeben'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid order data']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
        break;
}
