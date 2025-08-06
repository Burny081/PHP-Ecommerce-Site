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

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $offer = $_POST['offer'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    $image_path = "uploads/" . basename($image);

    $stmt = $pdo->prepare("INSERT INTO products (title, description, price, offer, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $price, $offer, $category_id, $image_path]);
    header("Location: manage_products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            color: #fff;
            padding: 15px 20px;
            display: block;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .sidebar .nav-link.active {
            background: #007bff;
        }
        .content {
            padding: 30px;
        }
        .form-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .btn-primary {
            border-radius: 10px;
            padding: 10px 20px;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: #0056b3;
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
    <div class="d-flex">
        <div class="sidebar">
            <h4 class="text-white text-center mb-4">Admin Panel</h4>
            <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="add_product.php" class="nav-link active"><i class="fas fa-plus-circle me-2"></i>Add Product</a>
            <a href="manage_products.php" class="nav-link"><i class="fas fa-boxes me-2"></i>Manage Products</a>
            <a href="add_category.php" class="nav-link"><i class="fas fa-tags me-2"></i>Add/Delete Categories</a>
            <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </div>
        <div class="flex-grow-1">
            <div class="content">
                <h2 class="mb-4">Add Product</h2>
                <div class="form-card">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label"><i class="fas fa-heading me-2"></i>Product Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fas fa-info-circle me-2"></i>Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label"><i class="fas fa-dollar-sign me-2"></i>Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="offer" class="form-label"><i class="fas fa-tags me-2"></i>Offer Price</label>
                            <input type="number" step="0.01" class="form-control" id="offer" name="offer" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label"><i class="fas fa-tag me-2"></i>Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label"><i class="fas fa-image me-2"></i>Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Product</button>
                    </form>
                </div>
            </div>
            <footer class="text-center">
                <div class="container">
                    <p>&copy; Nestor. All Rights Reserved.</p>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>