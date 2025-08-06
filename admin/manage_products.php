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

$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id")->fetchAll();

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $offer = $_POST['offer'];
    $category_id = $_POST['category_id'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_path = "uploads/" . basename($image);
        $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, offer = ?, category_id = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $description, $price, $offer, $category_id, $image_path, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, offer = ?, category_id = ? WHERE id = ?");
        $stmt->execute([$title, $description, $price, $offer, $category_id, $id]);
    }
    header("Location: manage_products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Mera-Shop</title>
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
        .table {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background: #007bff;
            color: #fff;
        }
        .product-image {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-warning, .btn-danger {
            border-radius: 10px;
            padding: 8px 15px;
            transition: background 0.3s ease;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .modal-content {
            border-radius: 15px;
        }
        .form-control, .form-select {
            border-radius: 10px;
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
            <a href="add_product.php" class="nav-link"><i class="fas fa-plus-circle me-2"></i>Add Product</a>
            <a href="manage_products.php" class="nav-link active"><i class="fas fa-boxes me-2"></i>Manage Products</a>
            <a href="add_category.php" class="nav-link"><i class="fas fa-tags me-2"></i>Add/Delete Categories</a>
            <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </div>
        <div class="flex-grow-1">
            <div class="content">
                <h2 class="mb-4">Manage Products</h2>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Offer</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><img src="../<?= htmlspecialchars($product['image']) ?>" class="product-image" alt="<?= htmlspecialchars($product['title']) ?>"></td>
                                <td><?= htmlspecialchars($product['title']) ?></td>
                                <td><?= htmlspecialchars($product['description']) ?></td>
                                <td>₹<?= $product['price'] ?></td>
                                <td>₹<?= $product['offer'] ?></td>
                                <td><?= htmlspecialchars($product['category_name']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $product['id'] ?>"><i class="fas fa-edit me-2"></i>Edit</button>
                                    <a href="manage_products.php?action=delete&id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt me-2"></i>Delete</a>
                                </td>
                            </tr>
                            <div class="modal fade" id="editModal<?= $product['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="price" value="<?= $product['price'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="offer" class="form-label">Offer Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="offer" value="<?= $product['offer'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Category</label>
                                                    <select class="form-select" name="category_id" required>
                                                        <?php
                                                        $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
                                                        foreach ($categories as $category): ?>
                                                            <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($category['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Update Image (Optional)</label>
                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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