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

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
// Search and filter logic
$selected_category = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($selected_category && $search) {
    $products_query = "SELECT * FROM products WHERE category_id = ? AND (title LIKE ? OR description LIKE ?)";
    $stmt = $pdo->prepare($products_query);
    $stmt->execute([$selected_category, "%$search%", "%$search%"]);
} elseif ($selected_category) {
    $products_query = "SELECT * FROM products WHERE category_id = ?";
    $stmt = $pdo->prepare($products_query);
    $stmt->execute([$selected_category]);
} elseif ($search) {
    $products_query = "SELECT * FROM products WHERE title LIKE ? OR description LIKE ?";
    $stmt = $pdo->prepare($products_query);
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $products_query = "SELECT * FROM products";
    $stmt = $pdo->prepare($products_query);
    $stmt->execute();
}
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }
        .bg-anim {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: 0.45;
            filter: blur(40px);
            mix-blend-mode: lighten;
            animation: floatBlob 18s infinite linear;
        }
        @keyframes floatBlob {
            0%   { transform: translateY(0) scale(1); }
            50%  { transform: translateY(-60px) scale(1.1); }
            100% { transform: translateY(0) scale(1); }
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
        }
        .nav-link {
            color: #fff !important;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #007bff !important;
        }
        .product-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s cubic-bezier(.25,.8,.25,1), box-shadow 0.3s cubic-bezier(.25,.8,.25,1);
            will-change: transform;
            perspective: 600px;
        }
        .product-card:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.18);
        }
        .product-card:active {
            transform: scale(0.98);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .btn-primary, .btn-success {
            border-radius: 10px;
            padding: 8px 15px;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success:hover {
            background: #218838;
        }
        .form-select {
            border-radius: 10px;
            border: 1px solid #ced4da;
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
    <div class="bg-anim">
        <div class="blob" style="width: 400px; height: 400px; background: radial-gradient(circle at 30% 30%, #007bff 60%, transparent 100%); left: -120px; top: -100px; animation-delay: 0s;"></div>
        <div class="blob" style="width: 350px; height: 350px; background: radial-gradient(circle at 70% 70%, #00c3ff 60%, transparent 100%); right: -100px; top: 120px; animation-delay: 3s;"></div>
        <div class="blob" style="width: 300px; height: 300px; background: radial-gradient(circle at 50% 50%, #ff6ec4 60%, transparent 100%); left: 50vw; bottom: -120px; animation-delay: 6s;"></div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="img/logo.jpg" alt="Logo" style="height:100px;width:100px;object-fit:contain;">
                Comparable
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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
    <div class="container mt-5" style="position: relative; z-index: 1;">
        <h2 class="mb-4">Our Products</h2>
        <div class="mb-4 d-flex align-items-end gap-3">
            <div>
                <label for="category" class="form-label"><i class="fas fa-filter me-2"></i>Filter by Category</label>
                <select onchange="location = this.value;" class="form-select w-100" id="category" style="min-width:180px;">
                    <option value="products.php">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="products.php?category_id=<?= $category['id'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>" <?= $selected_category == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <form method="get" class="flex-grow-1 d-flex gap-2">
                <?php if ($selected_category): ?>
                    <input type="hidden" name="category_id" value="<?= htmlspecialchars($selected_category) ?>">
                <?php endif; ?>
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text text-muted">Price: Fcfa <?= $product['price'] ?> <br>Offer: Fcfa <?= $product['offer'] ?></p>
                            <?php if (isset($product['status']) && $product['status'] === 'out_of_stock'): ?>
                                <span class="badge bg-danger mb-2">Out of Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success mb-2">In Stock</span>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mt-2">
                                <a href="product_details.php?id=<?= $product['id'] ?>" class="btn btn-primary"><i class="fas fa-eye me-2"></i>View Details</a>
                                <?php if (!isset($product['status']) || $product['status'] !== 'out_of_stock'): ?>
                                    <a href="cart.php?action=add&product_id=<?= $product['id'] ?>" class="btn btn-success"><i class="fas fa-cart-plus me-2"></i>Add to Cart</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled><i class="fas fa-ban me-2"></i>Unavailable</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="text-center">
        <div class="container">
            <p>&copy; Nestor. All Rights Reserved.</p>
            <P><b>contact +237 690640554</b></P>
            <p><b><u>help email: bongnitambe@gmail.com</u></b></p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Animate blobs horizontally for more dynamic background
    document.querySelectorAll('.blob').forEach(function(blob, i) {
        const base = i % 2 === 0 ? -120 : window.innerWidth - 100;
        const dir = i % 2 === 0 ? 1 : -1;
        setInterval(() => {
            const t = Date.now() / 4000 + i * 1.5;
            const x = base + Math.sin(t) * 80 * dir;
            blob.style.left = dir === 1 ? `${x}px` : '';
            blob.style.right = dir === -1 ? `${window.innerWidth - x - blob.offsetWidth}px` : '';
        }, 60);
    });
    </script>
    <script>
    // Mouse sensor animation for product cards
    document.querySelectorAll('.product-card').forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * 8; // tilt up/down
            const rotateY = ((x - centerX) / centerX) * -8; // tilt left/right
            card.style.transform = `translateY(-5px) scale(1.03) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            card.style.boxShadow = '0 16px 32px rgba(0,0,0,0.18)';
        });
        card.addEventListener('mouseleave', function() {
            card.style.transform = '';
            card.style.boxShadow = '';
        });
        card.addEventListener('mousedown', function() {
            card.style.transform += ' scale(0.98)';
        });
        card.addEventListener('mouseup', function() {
            card.style.transform = card.style.transform.replace(' scale(0.98)', '');
        });
    });
    </script>
</body>
</html>