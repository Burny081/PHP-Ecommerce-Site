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
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar { display: none; }
            .container.mt-5 { margin-top: 2rem !important; }
        }
        @media (max-width: 768px) {
            .product-card { margin-bottom: 1.5rem; }
            .product-image { height: 140px; }
            h2 { font-size: 1.5rem; }
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #e0e0e0;
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
            opacity: 0.35;
            filter: blur(32px);
            mix-blend-mode: lighten;
            animation: floatBlob 18s infinite linear;
        }
        @keyframes floatBlob {
            0%   { transform: translateY(0) scale(1); }
            50%  { transform: translateY(-40px) scale(1.08); }
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
            border-radius: 8px; /* reduced radius */
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1); /* lighter shadow */
            transition: transform 0.3s cubic-bezier(.25, .8, .25, 1);
            padding: 8px; /* reduced padding */
        }
        .product-card:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }
        .product-image {
            height: 130px; /* reduced height */
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        /* Ensure 4 items per row on medium+ screens */
        @media (min-width: 768px) {
            .product-col {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }
        .btn-primary, .btn-success {
            padding: 4px 8px; /* smaller buttons */
            font-size: 0.8rem;
            border-radius: 8px;
        }
        .card-title {
            font-size: 1rem; /* reduced title size */
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 0.85rem; /* reduced text size */
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
    <!-- Background animation removed for solid gray color -->
    <?php
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
    $t = [
        'en' => [
            'products' => 'Our Products',
            'view_cart' => 'View Cart',
            'logout' => 'Logout',
            'order_history' => 'Order History',
            'search' => 'Search...',
            'category' => 'Category',
            'add_to_cart' => 'Add to Cart',
            'in_stock' => 'In Stock',
            'out_of_stock' => 'Out of Stock',
        ],
        'fr' => [
            'products' => 'Nos Produits',
            'view_cart' => 'Voir le Panier',
            'logout' => 'Déconnexion',
            'order_history' => 'Historique',
            'search' => 'Rechercher...',
            'category' => 'Catégorie',
            'add_to_cart' => 'Ajouter au Panier',
            'in_stock' => 'En Stock',
            'out_of_stock' => 'Rupture',
        ]
    ];
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" style="color:#212529;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-dark" href="#" style="color:#212529 !important;">
                <img src="img/logo.png" alt="Logo" style="height:100px;width:100px;object-fit:contain;">
                Comparable
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="order_history.php?lang=<?= $lang ?>" style="color:#212529 !important;"><i class="fas fa-history me-2 text-dark"></i><?= $t[$lang]['order_history'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="chat.php" style="color:#212529 !important;"><i class="fas fa-comments me-2 text-dark"></i>Chat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="view_cart.php?lang=<?= $lang ?>" style="color:#212529 !important;"><i class="fas fa-shopping-cart me-2 text-dark"></i><?= $t[$lang]['view_cart'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="logout.php?lang=<?= $lang ?>" style="color:#212529 !important;"><i class="fas fa-sign-out-alt me-2 text-dark"></i><?= $t[$lang]['logout'] ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5" style="position: relative; z-index: 1;">
        <div class="d-flex justify-content-end mb-2">
            <a href="?lang=en" class="btn btn-outline-primary btn-sm me-2">English</a>
            <a href="?lang=fr" class="btn btn-outline-secondary btn-sm">Français</a>
        </div>
        <h2 class="mb-4"><?= $t[$lang]['products'] ?></h2>
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
                <!-- Changed grid column to show 4 items per row -->
                <div class="col-6 col-md-3 mb-4 product-col">
                    <div class="card product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text text-muted">
                                Price: Fcfa <?= $product['price'] ?> <br>Offer: Fcfa <?= $product['offer'] ?>
                            </p>
                            <?php if (isset($product['status']) && $product['status'] === 'out_of_stock'): ?>
                                <span class="badge bg-danger mb-2">Out of Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success mb-2">In Stock</span>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mt-2">
                                <a href="product_details.php?id=<?= $product['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye me-1"></i>Details
                                </a>
                                <?php if (!isset($product['status']) || $product['status'] !== 'out_of_stock'): ?>
                                    <a href="cart.php?action=add&product_id=<?= $product['id'] ?>" class="btn btn-success">
                                        <i class="fas fa-cart-plus me-1"></i>Cart
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-ban me-1"></i>N/A
                                    </button>
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