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
// Delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$user_id]);
    $pdo->prepare('DELETE FROM cart WHERE user_id = ?')->execute([$user_id]);
    $pdo->prepare('DELETE FROM orders WHERE user_id = ?')->execute([$user_id]);
    header('Location: users.php');
    exit;
}
// Fetch all users (except admins)
$users = $pdo->query("SELECT * FROM users WHERE role != 'admin'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
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
        .btn-danger { border-radius: 8px; }
        .btn-info { border-radius: 8px; }
    </style>
</head>
<body>
    <!-- Toggle button for sidebar collapse on small screens -->
    <div class="d-md-none p-2">
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse">
            Menu
        </button>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar wrapped in collapse -->
            <div class="collapse d-md-block col-12 col-md-3" id="sidebarCollapse">
                <nav class="sidebar p-3">
                    <h4 class="text-white text-center mb-4">Admin Panel</h4>
                    <!-- Hiding the Dashboard link -->
                    <a href="index.php" class="nav-link d-none"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="add_product.php" class="nav-link"><i class="fas fa-plus-circle me-2"></i>Add Product</a>
                    <a href="manage_products.php" class="nav-link"><i class="fas fa-boxes me-2"></i>Manage Products</a>
                    <a href="add_category.php" class="nav-link"><i class="fas fa-tags me-2"></i>Add/Delete Categories</a>
                    <a href="users.php" class="nav-link active"><i class="fas fa-users me-2"></i>Users</a>
                    <a href="../messages.php" class="nav-link"><i class="fas fa-comments me-2"></i>Chat</a>
                    <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>
            <main class="col-12 col-md-9">
                <div class="content">
                    <h2 class="mb-4">Users Management</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    
                                    <th>Commands</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    
                                    <td><a href="user_orders.php?user_id=<?= $user['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a></td>
                                    <td><a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i> Delete</a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <footer class="text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> Your Company. All rights reserved.</p>
                </footer>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
