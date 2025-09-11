<?php
session_start();

require_once 'config.php'; // This now assumes config.php establishes the $pdo connection

// Search and filter logic
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
// Search and filter logic
$selected_category = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$products_query = "SELECT * FROM products";
$params = [];
$conditions = [];

if ($selected_category) {
    $conditions[] = "category_id = ?";
    $params[] = $selected_category;
}
if ($search) {
    $conditions[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($conditions)) {
    $products_query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $pdo->prepare($products_query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;
$unread_messages_count = 0;

if ($user_id) {
    // Count messages that have been sent or delivered but not yet read
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND status IN ('sent', 'delivered')");
    $stmt->execute([$user_id]);
    $unread_messages_count = $stmt->fetchColumn();
}

$cart_item_count = 0;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_item_count = $stmt->fetchColumn();
} elseif (isset($_SESSION['cart'])) {
    // Count the number of items in the session cart
    $cart_item_count = count($_SESSION['cart']);
}
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
            background: #554e54ff;
            overflow-x: hidden;
            position: relative; /* Needed for absolute positioning of blobs */
        }
        .bg-anim {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
            overflow: hidden; /* Prevent blobs from causing scrollbars */
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
            background-color: #ffffff !important; /* Ensure white background */
        }
        .navbar-brand {
            font-weight: 600;
            color: #212529 !important; /* Darker text for brand */
        }
        .nav-link {
            color: #212529 !important; /* Darker text for nav links */
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #a30303ff !important; /* Hover color matches background */
        }
        .product-card {
            border-radius: 8px; /* reduced radius */
            box-shadow: 0 3px 12px hsla(0, 100%, 65%, 1.00); /* lighter shadow */
            transition: transform 0.3s cubic-bezier(.25, .8, .25, 1);
            padding: 8px; /* reduced padding */
            background-color: #fff; /* Ensure card background is white */
        }
        .product-card:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.12);
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
            background-color: #a30303ff; /* Matching button color */
            border-color: #a30303ff;
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: #8c0202; /* Darker shade on hover */
            border-color: #8c0202;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .card-title {
            font-size: 1rem; /* reduced title size */
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 0.85rem; /* reduced text size */
        }
        footer {
            background: #574f4fff;
            color: #ffffffff;
            padding: 20px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="bg-anim">
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
    </div>

    <?php
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
    $t = [
        'en' => [
            'products' => 'Our Products',
            'view_cart' => 'View Cart',
            'logout' => 'Logout',
            'login' => 'Login',
            'signup' => 'Sign Up',
            'order_history' => 'Order History',
            'search' => 'Search...',
            'category' => 'Category',
            'add_to_cart' => 'Add to Cart',
            'login_to_add_to_cart' => 'Login to Add to Cart',
            'in_stock' => 'In Stock',
            'out_of_stock' => 'Out of Stock',
        ],
        'fr' => [
            'products' => 'Nos Produits',
            'view_cart' => 'Voir le Panier',
            'logout' => 'Déconnexion',
            'login' => 'Connexion',
            'signup' => 'Inscription',
            'order_history' => 'Historique',
            'search' => 'Rechercher...',
            'category' => 'Catégorie',
            'add_to_cart' => 'Ajouter au Panier',
            'login_to_add_to_cart' => 'Connectez-vous pour ajouter au panier',
            'in_stock' => 'En Stock',
            'out_of_stock' => 'Rupture',
        ]
    ];
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="img/logo.png" alt="Logo" style="height:100px;width:100px;object-fit:contain;">
                Comparable
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="order_history.php?lang=<?= $lang ?>"><i class="fas fa-history me-2"></i><?= $t[$lang]['order_history'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="messages.php">
                            <i class="fas fa-comments me-2"></i>Chat
                            <?php if ($unread_messages_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $unread_messages_count ?>
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="view_cart.php?lang=<?= $lang ?>">
                            <i class="fas fa-shopping-cart me-2"></i><?= $t[$lang]['view_cart'] ?>
                            <?php if ($cart_item_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $cart_item_count ?>
                                    <span class="visually-hidden">items in cart</span>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php?lang=<?= $lang ?>"><i class="fas fa-sign-out-alt me-2"></i><?= $t[$lang]['logout'] ?></a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php?lang=<?= $lang ?>"><i class="fas fa-sign-in-alt me-2"></i><?= $t[$lang]['login'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_cart.php?lang=<?= $lang ?>"><i class="fas fa-shopping-cart me-2"></i><?= $t[$lang]['view_cart'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php?lang=<?= $lang ?>"><i class="fas fa-user-plus me-2"></i><?= $t[$lang]['signup'] ?></a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-2"></i><?= $lang == 'en' ? 'English' : 'Français' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="?lang=en">English</a></li>
                            <li><a class="dropdown-item" href="?lang=fr">Français</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5" style="position: relative; z-index: 1;">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4 text-white"><?= $t[$lang]['products'] ?></h2>
        <div class="mb-4 d-flex align-items-end gap-3 flex-wrap">
            <div class="flex-grow-1 flex-md-grow-0">
                <label for="category" class="form-label text-white"><i class="fas fa-filter me-2"></i>Filter by Category</label>
                <select onchange="location = this.value;" class="form-select" id="category" style="min-width:180px;">
                    <option value="products.php?search=<?= urlencode($search) ?>&lang=<?= $lang ?>">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="products.php?category_id=<?= $category['id'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>&lang=<?= $lang ?>" <?= $selected_category == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <form method="get" class="flex-grow-1 d-flex gap-2">
                <?php if ($selected_category): ?>
                    <input type="hidden" name="category_id" value="<?= htmlspecialchars($selected_category) ?>">
                <?php endif; ?>
                <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
                <input type="text" name="search" class="form-control" placeholder="<?= $t[$lang]['search'] ?>" value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        No products found matching your criteria.
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-3 mb-4 product-col">
                    <div class="card product-card h-100">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text text-muted">
                                Price: Fcfa <?= number_format($product['price'], 0, ',', ' ') ?> <br>Offer: Fcfa <?= number_format($product['offer'], 0, ',', ' ') ?>
                            </p>
                            <?php
                                // Assuming 'status' column exists and is either 'in_stock' or 'out_of_stock'
                                $isInStock = (isset($product['status']) && $product['status'] === 'in_stock') || (!isset($product['status']) && $product['stock_quantity'] > 0); // Added stock_quantity check as fallback
                            ?>
                            <span class="badge <?= $isInStock ? 'bg-success' : 'bg-danger' ?> mb-2">
                                <?= $isInStock ? $t[$lang]['in_stock'] : $t[$lang]['out_of_stock'] ?>
                            </span>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="product_details.php?id=<?= $product['id'] ?>&lang=<?= $lang ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Details
                                </a>
                                <?php if ($isInStock): ?>
                                    <a href="cart.php?action=add&product_id=<?= $product['id'] ?>&lang=<?= $lang ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-cart-plus me-1"></i><?= $t[$lang]['add_to_cart'] ?>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fas fa-ban me-1"></i><?= $t[$lang]['out_of_stock'] ?>
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
        // Random initial position for a more varied start
        const initialLeft = Math.random() * (window.innerWidth - blob.offsetWidth);
        const initialTop = Math.random() * (window.innerHeight - blob.offsetHeight);
        blob.style.left = `${initialLeft}px`;
        blob.style.top = `${initialTop}px`;

        const baseLeft = parseInt(blob.style.left);
        const dir = i % 2 === 0 ? 1 : -1;
        const amplitude = Math.random() * 100 + 50; // Random amplitude for varied movement

        setInterval(() => {
            const t = Date.now() / (4000 + i * 500); // Vary animation speed
            const x = baseLeft + Math.sin(t) * amplitude * dir;
            blob.style.left = `${x}px`;
            // Add slight vertical movement as well
            const y = initialTop + Math.cos(t / 2) * (amplitude / 2);
            blob.style.top = `${y}px`;
        }, 50); // Slightly faster interval for smoother animation
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