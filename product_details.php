<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}


$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 3");
$related_stmt->execute([$product['category_id'], $product_id]);
$related_products = $related_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Mera-Shop</title>
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
        .product-image {
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .product-details {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            border-radius: 10px;
            padding: 10px 20px;
            transition: background 0.3s ease;
        }
        .btn-success:hover {
            background: #218838;
        }
        .related-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .related-card:hover {
            transform: translateY(-3px);
        }
        .related-image {
            height: 150px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
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
            <a class="navbar-brand" href="#">Mera-Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="products.php"><i class="fas fa-arrow-left me-2"></i>Back to Products</a>
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
        <h2 class="mb-4">Product Details</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid product-image" alt="<?= htmlspecialchars($product['title']) ?>">
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h3><?= htmlspecialchars($product['title']) ?></h3>
                    <p class="text-muted"><i class="fas fa-tag me-2"></i><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                    <p><i class="fas fa-info-circle me-2"></i><strong>Description:</strong> <?= htmlspecialchars($product['description']) ?></p>
                    <p><i class="fas fa-dollar-sign me-2"></i><strong>Price:</strong> Fcfa <?= $product['price'] ?></p>
                    <p><i class="fas fa-tags me-2"></i><strong>Offer:</strong> Fcfa <?= $product['offer'] ?></p>
                    <a href="cart.php?action=add&product_id=<?= $product['id'] ?>" class="btn btn-success"><i class="fas fa-cart-plus me-2"></i>Add to Cart</a>
                </div>
            </div>
        </div>
        <?php if (!empty($related_products)): ?>
            <h3 class="mt-5 mb-4">Related Products</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card related-card">
                            <img src="<?= htmlspecialchars($related['image']) ?>" class="card-img-top related-image" alt="<?= htmlspecialchars($related['title']) ?>">
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($related['title']) ?></h6>
                                <p class="card-text text-muted">Offer: Fcfa <?= $related['offer'] ?></p>
                                <a href="product_details.php?id=<?= $related['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye me-2"></i>View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <footer class="text-center">
        <div class="container">
            <p>&copy; 2025 Mera-Shop. All Rights Reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
