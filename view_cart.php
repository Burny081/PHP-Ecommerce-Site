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
$stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.offer, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $cart_id = $_GET['cart_id'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    header("Location: view_cart.php");
    exit;
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['offer'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart - Comparable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .nav-link:hover {
            color: #007bff !important;
        }
        .cart-image {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .table {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background: #007bff;
            color: #fff;
        }
        .btn-danger, .btn-success {
            border-radius: 10px;
            padding: 8px 15px;
            transition: background 0.3s ease;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-success:hover {
            background: #218838;
        }
        footer {
            background: #343a40;
            color: #fff;
            padding: 20px 0;
            margin-top: 40px;
        }
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
                        <a class="nav-link" href="products.php"><i class="fas fa-arrow-left me-2"></i>Back to Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4">Your Cart</h2>
        <?php if (empty($cart_items)): ?>
            <p class="text-muted">Your cart is empty.</p>
        <?php else: ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Offer</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['image']) ?>" class="cart-image" alt="<?= htmlspecialchars($item['title']) ?>"></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td>Fcfa <?= $item['price'] ?></td>
                            <td>Fcfa <?= $item['offer'] ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Fcfa <?= $item['offer'] * $item['quantity'] ?></td>
                            <td>
                                <a href="view_cart.php?action=remove&cart_id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt me-2"></i>Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <h4>Total: Fcfa<?= $total ?></h4>
                <a href="#" class="btn btn-success"><i class="fas fa-check me-2"></i>Proceed to Checkout</a>
            </div>
        <?php endif; ?>
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