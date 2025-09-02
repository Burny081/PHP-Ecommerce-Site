<?php
$pdo = require_once '../config.php'; // Ensure $pdo is defined and assigned
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Super Admin Dashboard</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- ...existing CSS... -->
</head>
<body>
	<!-- Navigation Bar -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			<a class="navbar-brand" href="#">Super Admin Dashboard</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSuperadmin" aria-controls="navbarSuperadmin" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSuperadmin">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
	
	<!-- Main Container with Tabs -->
	<div class="container mt-4">
		<ul class="nav nav-tabs" id="superadminTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Manage Users</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab" aria-controls="logs" aria-selected="false">Audit Logs</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">Manage Orders</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab" aria-controls="content" aria-selected="false">Manage Content</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="messaging-tab" data-bs-toggle="tab" data-bs-target="#messaging" type="button" role="tab" aria-controls="messaging" aria-selected="false">Messaging</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button" role="tab" aria-controls="backup" aria-selected="false">Database Backup</button>
			</li>
		</ul>
		
		<div class="tab-content" id="superadminTabContent">
			<!-- Manage Users Tab -->
			<div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
				<h3 class="mt-3">Manage Users</h3>
				<!-- Inline Add User Form -->
				<form method="POST" action="add_user.php">
					<div class="mb-3">
						<label class="form-label">Username:</label>
						<input type="text" name="username" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Password:</label>
						<input type="password" name="password" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Email:</label>
						<input type="email" name="email" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Role:</label>
						<select name="role" class="form-select">
							<option value="user">User</option>
							<option value="admin">Admin</option>
							<option value="superadmin">Super Admin</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary">Add User</button>
				</form>
                <!-- Display Existing Users -->
                <?php
                $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['role'] ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
			</div>
			
			<!-- Settings Tab -->
			<div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
				<h3 class="mt-3">Manage Settings</h3>
				<form method="POST" action="manage_settings.php">
					<div class="mb-3">
						<label class="form-label">Site Title:</label>
						<input type="text" name="settings[site_title]" class="form-control">
					</div>
					<div class="mb-3">
						<label class="form-label">Contact Email:</label>
						<input type="email" name="settings[contact_email]" class="form-control">
					</div>
					<!-- ...add more settings fields as needed... -->
					<button type="submit" class="btn btn-primary">Update Settings</button>
				</form>
			</div>
			
			<!-- Audit Logs Tab -->
			<div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
				<h3 class="mt-3">Audit Logs</h3>
				<table class="table table-bordered">
					<thead>
						<tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Event</th>
                            <th>Details</th>
                            <th>Timestamp</th>
						</tr>
					</thead>
					<tbody>
                        <?php
                        try {
                            // Assuming an 'audit_logs' table exists with columns like: id, user_id, action, details, created_at
                            $log_stmt = $pdo->query("SELECT id, user_id, action, details, created_at FROM audit_logs ORDER BY created_at DESC LIMIT 100");
                            $logs = $log_stmt->fetchAll(PDO::FETCH_ASSOC);
                            if ($logs) {
                                foreach ($logs as $log) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($log['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['user_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['action']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['details']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['created_at']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No audit logs found.</td></tr>';
                            }
                        } catch (PDOException $e) {
                            echo '<tr><td colspan="5" class="text-danger">Error fetching logs: ' . $e->getMessage() . '</td></tr>';
                        }
                        ?>
					</tbody>
				</table>
			</div>
			
			<!-- Manage Orders Tab -->
			<div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
				<h3 class="mt-3">Manage Orders</h3>
                <?php
                try {
                    // Fetch all orders, joining with the users table to get customer username
                    $order_stmt = $pdo->query("
                        SELECT orders.id, orders.user_id, users.username, orders.total, orders.status, orders.created_at 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        ORDER BY orders.created_at DESC
                    ");
                    $orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error fetching orders: ' . $e->getMessage() . '</div>';
                    $orders = []; // Ensure $orders is an array to prevent errors
                }
                ?>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['username']) ?> (ID: <?= htmlspecialchars($order['user_id']) ?>)</td>
                                    <td>$<?= htmlspecialchars(number_format($order['total'], 2)) ?></td>
                                    <td><span class="badge bg-<?php switch ($order['status']) { case 'pending': echo 'warning'; break; case 'processing': echo 'info'; break; case 'shipped': echo 'primary'; break; case 'delivered': echo 'success'; break; case 'cancelled': echo 'danger'; break; default: echo 'secondary'; } ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></td>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                    <td><a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
			</div>
			
			<!-- Manage Content Tab -->
			<div class="tab-pane fade" id="content" role="tabpanel" aria-labelledby="content-tab">
				<h3 class="mt-3">Manage Content</h3>
				<!-- Inline Content Management Form -->
				<form method="POST" action="manage_content.php">
					<div class="mb-3">
						<label class="form-label">Content Title:</label>
						<input type="text" name="title" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Content Body:</label>
						<textarea name="body" class="form-control" required></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Status:</label>
						<select name="status" class="form-select">
							<option value="pending">Pending</option>
							<option value="approved">Approved</option>
							<option value="rejected">Rejected</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary">Save Content</button>
				</form>
			</div>
			
			<!-- Messaging Tab -->
			<div class="tab-pane fade" id="messaging" role="tabpanel" aria-labelledby="messaging-tab">
				<h3 class="mt-3">Messaging</h3>
				<!-- Inline Send Message Form -->
				<form method="POST" action="send_message.php">
					<div class="mb-3">
						<label class="form-label">Receiver ID:</label>
						<input type="number" name="receiver_id" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Message:</label>
						<textarea name="content" class="form-control" required></textarea>
					</div>
					<button type="submit" class="btn btn-primary">Send Message</button>
				</form>
				<!-- Placeholder for listing messages -->
				<p class="mt-3">Messages list goes here. (Use getMessages() from superadmin_functions.php)</p>
			</div>
			
			<!-- Database Backup Tab -->
			<div class="tab-pane fade" id="backup" role="tabpanel" aria-labelledby="backup-tab">
				<h3 class="mt-3">Database Backup</h3>
				<!-- Button to trigger backup function -->
				<form method="POST" action="backup.php">
					<button type="submit" class="btn btn-warning">Run Database Backup</button>
				</form>
				<!-- Optionally show backup result -->
				<p class="mt-3">Latest backup file info goes here.</p>
			</div>
		</div>
	</div>
	
	<!-- ...existing footer code if needed... -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
