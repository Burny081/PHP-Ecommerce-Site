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
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$user->execute([$user_id]);
$user = $user->fetch();
// Handle order deletion
if (isset($_GET['delete_order'])) {
    $delete_order_id = intval($_GET['delete_order']);
    // Delete order items first
    $pdo->prepare('DELETE FROM order_items WHERE order_id = ?')->execute([$delete_order_id]);
    // Delete the order
    $pdo->prepare('DELETE FROM orders WHERE id = ?')->execute([$delete_order_id]);
    // Redirect to avoid resubmission
    header("Location: user_orders.php?user_id=" . $user_id);
    exit;
}
// Handle order termination
if (isset($_GET['terminate_order'])) {
    $terminate_order_id = intval($_GET['terminate_order']);
    $pdo->prepare("UPDATE orders SET status = 'Finished' WHERE id = ?")->execute([$terminate_order_id]);
    header("Location: user_orders.php?user_id=" . $user_id);
    exit;
}

$orders = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->execute([$user_id]);
$orders = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders</title>
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
            <h2 class="mb-4">Orders for <?= htmlspecialchars($user['username'] ?? 'Unknown') ?></h2>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($orders): foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['created_at'] ?? '') ?></td>
                            <td><?= htmlspecialchars($order['status'] ?? '') ?></td>
                            <td>Fcfa <?= htmlspecialchars($order['total'] ?? '') ?></td>
                            <td>
                                <a href="user_orders.php?user_id=<?= $user_id ?>&delete_order=<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?')"><i class="fas fa-trash-alt me-2"></i>Delete</a>
                                <?php if ($order['status'] !== 'Finished'): ?>
                                    <a href="user_orders.php?user_id=<?= $user_id ?>&terminate_order=<?= $order['id'] ?>" class="btn btn-success btn-sm ms-2" onclick="return confirm('Mark this order as finished?')"><i class="fas fa-check me-2"></i>Terminate</a>
                                <?php endif; ?>
                                <a href="order_items.php?order_id=<?= $order['id'] ?>" class="btn btn-primary btn-sm ms-2"><i class="fas fa-eye me-2"></i>View</a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="5" class="text-center">No orders found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <a href="users.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-2"></i>Back to Users</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
