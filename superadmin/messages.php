<?php
session_start();
$pdo = require_once '../config.php';

// --- Contrôle d'accès basique ---
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'superadmin') {
    // header("Location: ../login.php"); exit;
}
$me = (int)($_SESSION['user_id'] ?? 1); // fallback 1 si besoin pendant l'intégration

// Envoi d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to_id'], $_POST['content'])) {
    $to = (int)$_POST['to_id'];
    $content = trim($_POST['content']);
    if ($to > 0 && $content !== '') {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?,?,?)");
        $stmt->execute([$me, $to, $content]);
        header("Location: messages.php?with_user_id=".$to);
        exit;
    }
}

// Filtre rôle
$role = $_GET['role'] ?? ''; // '', 'user', 'admin', 'superadmin'
$whereRole = '';
$params = [];
if ($role !== '') {
    $whereRole = " AND u.role = ? ";
    $params[] = $role;
}

// Liste des correspondants (conversations uniques)
$sql = "
SELECT u.id, u.username, u.email, u.role,
       MAX(m.sent_at) AS last_time
FROM users u
JOIN messages m
  ON (m.sender_id = u.id OR m.receiver_id = u.id)
WHERE u.id <> ? 
  $whereRole
GROUP BY u.id, u.username, u.email, u.role
ORDER BY (last_time IS NULL), last_time DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge([$me], $params));
$conversations = $stmt->fetchAll();

// Sélection du fil
$withId = isset($_GET['with_user_id']) ? (int)$_GET['with_user_id'] : (count($conversations) ? (int)$conversations[0]['id'] : 0);
$thread = [];
$withUser = null;
if ($withId > 0) {
    $u = $pdo->prepare("SELECT id,username,email,role FROM users WHERE id=?");
    $u->execute([$withId]);
    $withUser = $u->fetch();

    $t = $pdo->prepare("
        SELECT m.*, 
               su.username AS sender_name,
               ru.username AS receiver_name
        FROM messages m
        JOIN users su ON su.id = m.sender_id
        JOIN users ru ON ru.id = m.receiver_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at ASC, m.id ASC
    ");
    $t->execute([$me, $withId, $withId, $me]);
    $thread = $t->fetchAll();

    // Marquer tous les messages reçus comme lus
    $markRead = $pdo->prepare("
        UPDATE messages 
        SET read_at = NOW() 
        WHERE receiver_id = ? AND sender_id = ? AND read_at IS NULL
    ");
    $markRead->execute([$me, $withId]);
}

// Pour l’envoi à n’importe qui
$allUsers = $pdo->query("SELECT id,username,role FROM users ORDER BY role, username")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>SuperAdmin — Messagerie</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f6f7fb}
    .list-item{cursor:pointer;border-radius:12px}
    .list-item.active{background:#e9f3ff}
    .bubble{padding:.6rem .9rem;border-radius:14px;max-width:80%}
    .bubble.me{background:#0d6efd;color:#fff;margin-left:auto}
    .bubble.them{background:#f1f3f5}
    .small.check{margin-left:4px;font-size:0.8rem;}
    .check-muted{color:#999;}
    .check-read{color:#0dcaf0;}
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">← SuperAdmin</a>
    <div class="text-white-50 small">Centre de messagerie unifié</div>
  </div>
</nav>

<div class="container py-3">
  <div class="row g-3">
    <!-- LISTE CONVOS -->
    <div class="col-12 col-md-4">
      <div class="card p-3">
        <form class="d-flex gap-2 mb-3" method="get">
          <input type="hidden" name="with_user_id" value="<?= (int)$withId ?>">
          <select class="form-select" name="role" onchange="this.form.submit()">
            <option value="">Tous les rôles</option>
            <option value="user" <?= $role==='user'?'selected':'' ?>>Clients</option>
            <option value="admin" <?= $role==='admin'?'selected':'' ?>>Admins</option>
            <option value="superadmin" <?= $role==='superadmin'?'selected':'' ?>>SuperAdmins</option>
          </select>
        </form>

        <div class="list-group">
          <?php if(empty($conversations)): ?>
            <div class="text-muted">Aucune conversation</div>
          <?php else: ?>
            <?php foreach($conversations as $c): 
                $active = ((int)$c['id'] === $withId) ? 'active' : '';
            ?>
              <a class="list-group-item list-item <?= $active ?>"
                 href="?with_user_id=<?= (int)$c['id'] ?>&role=<?= htmlspecialchars($role) ?>">
                <div class="d-flex justify-content-between">
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($c['username']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($c['role']) ?></small>
                  </div>
                  <small class="text-muted"><?= htmlspecialchars($c['last_time'] ?? '') ?></small>
                </div>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- THREAD -->
    <div class="col-12 col-md-8">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">Fil de discussion</h5>
          <form method="post" class="d-flex gap-2">
            <select name="to_id" class="form-select" required style="max-width:260px">
              <?php foreach($allUsers as $u): ?>
                <option value="<?= (int)$u['id'] ?>" <?= $withId===(int)$u['id']?'selected':'' ?>>
                  <?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['role']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="content" class="form-control" placeholder="Écrire un message…" required>
            <button class="btn btn-primary">Envoyer</button>
          </form>
        </div>

        <hr>

        <div style="min-height:320px">
          <?php if(!$withUser): ?>
            <div class="text-muted">Sélectionne un correspondant à gauche.</div>
          <?php else: ?>
            <?php foreach($thread as $m): 
              $isMe = ((int)$m['sender_id'] === $me);
            ?>
              <div class="d-flex <?= $isMe ? 'justify-content-end' : 'justify-content-start' ?> mb-2">
                <div class="bubble <?= $isMe ? 'me' : 'them' ?>">
                  <div class="small mb-1">
                    <?= htmlspecialchars($m['sender_name']) ?>
                    <span class="text-white-50 text-muted">· <?= htmlspecialchars($m['sent_at']) ?></span>
                    <?php if($isMe): ?>
                      <?php if($m['read_at']): ?>
                        <span class="check check-read">✓✓</span>
                      <?php else: ?>
                        <span class="check check-muted">✓</span>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                  <div><?= nl2br(htmlspecialchars($m['content'])) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
