<?php
session_start();
require_once '../config.php';
if (!isset($pdo)) {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', 'Nestor667');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_stmt = $pdo->prepare('SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?');
$order_stmt->execute([$order_id]);
$order = $order_stmt->fetch();
if (!$order) {
    header('Location: users.php');
    exit;
}
$item_stmt = $pdo->prepare('SELECT oi.*, p.title, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
$item_stmt->execute([$order_id]);
$order_items = $item_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #343a40; padding-top: 20px; }
        .sidebar a { color: #fff; padding: 15px 20px; display: block; transition: background 0.3s ease; }
        .sidebar a:hover { background: #495057; }
        .sidebar .nav-link.active { background: #007bff; }
        .content { padding: 30px; }
        .table { background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.07); }
        .order-image { max-width: 50px; max-height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>
<div class="d-flex">
    <div class="sidebar">
        <h4 class="text-white text-center mb-4">Admin Panel</h4>
        <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="add_product.php" class="nav-link"><i class="fas fa-plus-circle me-2"></i>Add Product</a>
        <a href="manage_products.php" class="nav-link"><i class="fas fa-boxes me-2"></i>Manage Products</a>
        <a href="add_category.php" class="nav-link"><i class="fas fa-tags me-2"></i>Add/Delete Categories</a>
        <a href="users.php" class="nav-link active"><i class="fas fa-users me-2"></i>Users</a>
         <a href="../messages.php" class="nav-link"><i class="fas fa-comments me-2"></i>Chat</a>
        <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </div>
    <div class="flex-grow-1">
        <div class="content">
            <h2 class="mb-4">Order #<?= $order['id'] ?> for <?= htmlspecialchars($order['username']) ?></h2>
            <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
            <p><strong>Status:</strong> <?= $order['status'] ?></p>
            <p><strong>Total:</strong> Fcfa <?= $order['total'] ?></p>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
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
                            <td><?= $item['quantity'] ?></td>
                            <td>Fcfa <?= $item['price'] * $item['quantity'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="user_orders.php?user_id=<?= $order['user_id'] ?>" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-2"></i>Back to Orders</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
