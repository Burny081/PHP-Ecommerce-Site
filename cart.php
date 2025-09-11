<?php
// cart.php
session_start();

require_once 'config.php';
if (!isset($pdo)) {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', 'Nestor667');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

$action     = $_POST['action'] ?? $_GET['action'] ?? null;
$product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
$quantity   = $_POST['quantity'] ?? $_GET['quantity'] ?? 1;
$lang       = $_POST['lang'] ?? $_GET['lang'] ?? 'en';

// Validate quantity
$quantity = (is_numeric($quantity) && $quantity > 0) ? (int)$quantity : 1;

// Logged-in?
$is_logged_in = isset($_SESSION['user_id']);
$user_id      = $is_logged_in ? $_SESSION['user_id'] : null;

// Initialize session cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add/remove/update actions
if ($action === 'add' && $product_id) {
    if ($is_logged_in) {
        // --- DB cart handling ---
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
    } else {
        // --- Session cart handling ---
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
    header("Location: products.php?lang=$lang&message=Product added to cart");
    exit;
}

if ($action === 'remove' && $product_id) {
    if ($is_logged_in) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: view_cart.php?lang=$lang&message=Product removed from cart");
    exit;
}

if ($action === 'update' && $product_id) {
    if ($is_logged_in) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $user_id, $product_id]);
    } else {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header("Location: view_cart.php?lang=$lang&message=Cart updated");
    exit;
}

// Default fallback
header("Location: products.php?lang=$lang");
exit;
