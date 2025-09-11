<?php
session_start();
$pdo = require_once '../config.php';

// --- Sécurité basique ---
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'superadmin') {
    // header("Location: ../login.php");
    // exit;
}

// Gestion de l’ajout utilisateur/admin
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'user';

    if ($username && $email && $password && in_array($role, ['user','admin'])) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username,email,password,role,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$username,$email,$hash,$role]);
        $msg = "✅ $role ajouté avec succès.";
    } else {
        $msg = "⚠️ Veuillez remplir tous les champs.";
    }
}

// Stats rapides
$usersCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$adminsCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$superAdminsCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='superadmin'")->fetchColumn();
$productsCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$messagesCount = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$ordersCount = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Super Admin — Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f6f7fb}
    .card{border:0;border-radius:20px}
    .nav-link.active{font-weight:600}
    .table thead th {white-space:nowrap}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">SuperAdmin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="../index.php">Boutique</a></li>
        <li class="nav-item"><a class="nav-link" href="../admin/index.php">Admin</a></li>
        <li class="nav-item"><a class="nav-link" href="../superadmin/messages.php">Messagerie</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Déconnexion</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-4">
  <?php if($msg): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Stat cards -->
  <div class="row g-3">
    <div class="col-6 col-md-3"><div class="card p-3"><div class="text-muted">Clients</div><div class="h4 mb-0"><?= $usersCount ?></div></div></div>
    <div class="col-6 col-md-3"><div class="card p-3"><div class="text-muted">Admins</div><div class="h4 mb-0"><?= $adminsCount ?></div></div></div>
    <div class="col-6 col-md-3"><div class="card p-3"><div class="text-muted">SuperAdmins</div><div class="h4 mb-0"><?= $superAdminsCount ?></div></div></div>
    <div class="col-6 col-md-3"><div class="card p-3"><div class="text-muted">Produits</div><div class="h4 mb-0"><?= $productsCount ?></div></div></div>
    <div class="col-6 col-md-3"><div class="card p-3"><div class="text-muted">Commandes</div><div class="h4 mb-0"><?= $ordersCount ?></div></div></div>
    <div class="col-6 col-md-3"><div class="card p-3"><div class="text-muted">Messages</div><div class="h4 mb-0"><?= $messagesCount ?></div></div></div>
  </div>

  <!-- Onglets -->
  <ul class="nav nav-tabs mt-4" id="superadminTab" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-users">Utilisateurs</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-admins">Admins</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-add">Ajouter</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-messages">Messagerie</button></li>
  </ul>

  <div class="tab-content bg-white p-3 rounded-bottom shadow-sm">
    <!-- USERS -->
    <div class="tab-pane fade show active" id="tab-users">
      <?php $users = $pdo->query("SELECT id,username,email,created_at FROM users WHERE role='user' ORDER BY created_at DESC")->fetchAll(); ?>
      <h5 class="mb-3">Liste des clients</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Inscription</th><th></th></tr></thead>
          <tbody>
          <?php foreach($users as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td>
              <td><?= htmlspecialchars($u['username']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-primary" href="messages.php?with_user_id=<?= (int)$u['id'] ?>">Contacter</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ADMINS -->
    <div class="tab-pane fade" id="tab-admins">
      <?php $admins = $pdo->query("SELECT id,username,email,created_at FROM users WHERE role='admin' ORDER BY created_at DESC")->fetchAll(); ?>
      <h5 class="mb-3">Liste des admins</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Inscription</th><th></th></tr></thead>
          <tbody>
          <?php foreach($admins as $a): ?>
            <tr>
              <td><?= (int)$a['id'] ?></td>
              <td><?= htmlspecialchars($a['username']) ?></td>
              <td><?= htmlspecialchars($a['email']) ?></td>
              <td><?= htmlspecialchars($a['created_at'] ?? '') ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-primary" href="messages.php?with_user_id=<?= (int)$a['id'] ?>">Contacter</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ADD USER/ADMIN -->
    <div class="tab-pane fade" id="tab-add">
      <h5>Ajouter un utilisateur ou admin</h5>
      <form method="post" class="row g-3 mt-2">
        <input type="hidden" name="add_user" value="1">
        <div class="col-md-6">
          <label class="form-label">Nom d'utilisateur</label>
          <input type="text" class="form-control" name="username" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Mot de passe</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Rôle</label>
          <select class="form-select" name="role" required>
            <option value="user">Utilisateur</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="col-12">
          <button class="btn btn-success">Créer</button>
        </div>
      </form>
    </div>

    <!-- MESSAGES -->
    <div class="tab-pane fade" id="tab-messages">
      <h5>Messagerie</h5>
      <a href="messages.php" class="btn btn-primary">Ouvrir le hub de messagerie</a>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
