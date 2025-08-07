<?php
session_start();
require_once 'config.php';
if (!isset($pdo)) {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', 'Nestor667');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();
if (!$order) {
    header("Location: order_history.php");
    exit;
}
// Fetch order items
$item_stmt = $pdo->prepare("SELECT oi.*, p.title, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$item_stmt->execute([$order_id]);
$order_items = $item_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Comparable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { color: #007bff !important; }
        .table { background: #fff; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .table thead { background: #007bff; color: #fff; }
        .order-image { max-width: 50px; max-height: 50px; object-fit: cover; border-radius: 5px; }
        footer { background: #343a40; color: #fff; padding: 20px 0; margin-top: 40px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Comparable</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="order_history.php"><i class="fas fa-arrow-left me-2"></i>Back to Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php"><i class="fas fa-box-open me-2"></i>Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_cart.php"><i class="fas fa-shopping-cart me-2"></i>View Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="mb-4">Order #<?= $order['id'] ?> Details</h2>
    <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
    <p><strong>Total:</strong> Fcfa <?= $order['total'] ?></p>
    <table class="table table-bordered table-hover mt-4">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Offer</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($item['image']) ?>" class="order-image" alt="<?= htmlspecialchars($item['title']) ?>"></td>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td>Fcfa <?= $item['price'] ?></td>
                    <td>Fcfa <?= $item['offer'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Fcfa <?= $item['offer'] * $item['quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<footer class="text-center">
    <div class="container">
        <p>&copy; Nestor. All Rights Reserved.</p>
        <P><b>contact +237 690640554</b></P>
        <p><b><u>help email: bongnitambe@gmail.com</u></b></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
