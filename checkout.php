<?php
session_start();
require_once 'config.php';
if (!isset($pdo)) {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', 'Nestor667');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_cart.php");
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.offer FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header("Location: view_cart.php");
    exit;
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['offer'] * $item['quantity'];
}

// Insert order
$order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total, created_at) VALUES (?, ?, NOW())");
$order_stmt->execute([$user_id, $total]);
$order_id = $pdo->lastInsertId();

// Insert order items
$order_item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, offer) VALUES (?, ?, ?, ?, ?)");
foreach ($cart_items as $item) {
    $order_item_stmt->execute([
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['price'],
        $item['offer']
    ]);
}

// Clear cart
$clear_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
$clear_stmt->execute([$user_id]);

// Redirect to PDF or order confirmation
header("Location: checkout_pdf.php?order_id=" . $order_id);
exit;
?>
