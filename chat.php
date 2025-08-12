<?php
// chat.php
session_start();
require_once 'config.php';
if (!isset($pdo)) {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', 'Nestor667');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
// Get admin id
$admin = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetch();
$admin_id = $admin ? $admin['id'] : null;
// Determine chat partner
if ($_SESSION['role'] == 'admin') {
    $partner_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    if (!$partner_id) {
        $users = $pdo->query("SELECT id, username FROM users WHERE role = 'user'")->fetchAll();
    }
} else {
    $partner_id = $admin_id;
}
// Handle send message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content']) && $partner_id) {
    $content = trim($_POST['content']);
    if ($content) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $partner_id, $content]);
    }
}
// Fetch chat messages
if ($partner_id) {
    $chat = $pdo->prepare("SELECT m.*, u.username AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.sent_at ASC");
    $chat->execute([$user_id, $partner_id, $partner_id, $user_id]);
    $chat = $chat->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #b3e0ff 30%, #e0e0e0 70%, #ffe0e0 100%);
        }
        .chat-box {
            background: linear-gradient(120deg, #f7fafd 50%, #e0e0e0 80%, #fff5f5 100%);
            border-radius: 10px;
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
            border: 2px solid #3399ff;
            box-shadow: 0 4px 16px rgba(51,153,255,0.10), 0 2px 8px rgba(255,77,77,0.08), 0 1px 4px rgba(160,160,160,0.10);
        }
        .chat-msg { margin-bottom: 12px; }
    .chat-msg .sender { font-weight: bold; color: #88a0b8ff; }
    .chat-msg .me { text-align: right; }
    .chat-msg .admin { color: #ff4d4d; }
    .chat-msg .user { color: #3399ff; }
    .chat-msg .time { font-size: 0.85em; color: #3399ff; }
    .list-group-item { background: linear-gradient(90deg, #eaf6ff 40%, #e0e0e0 80%, #ffe0e0 100%); border: 1px solid #b3e0ff; color: #444; }
    .chat-msg { background: #f5f5f5; border-radius: 6px; padding: 6px 10px; }
    .btn-outline-primary { border-color: #3399ff; color: #3399ff; }
    .btn-outline-primary:hover { background: #3399ff; color: #fff; }
    .btn-outline-danger { border-color: #ff4d4d; color: #ff4d4d; }
    .btn-outline-danger:hover { background: #ff4d4d; color: #fff; }
    </style>
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <div class="mb-3">
    <a href="<?= ($_SESSION['role']=='admin' ? 'index.php' : 'products.php') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
    </div>
    <h2 class="mb-4">Chat <?= ($_SESSION['role']=='admin' && $partner_id) ? 'with User' : 'with Admin' ?></h2>
    <?php if ($_SESSION['role']=='admin' && !isset($partner_id)): ?>
        <form method="get" class="mb-4">
            <label for="user_id" class="form-label">Select User:</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">Choose...</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2">Start Chat</button>
        </form>
        <?php
        // Show users with existing conversations
        $conv_users = $pdo->query("SELECT DISTINCT CASE WHEN sender_id = $admin_id THEN receiver_id ELSE sender_id END AS user_id FROM messages WHERE sender_id = $admin_id OR receiver_id = $admin_id")->fetchAll();
        if ($conv_users):
        ?>
        <div class="mb-4">
            <label class="form-label">Users with Conversation:</label>
            <ul class="list-group">
            <?php foreach ($conv_users as $cu):
                $uinfo = $pdo->query("SELECT username FROM users WHERE id = " . intval($cu['user_id']))->fetch();
                if ($uinfo): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($uinfo['username']) ?></span>
                    <a href="?user_id=<?= intval($cu['user_id']) ?>" class="btn btn-sm btn-outline-primary">Open Chat</a>
                </li>
                <?php endif;
            endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    <?php elseif ($partner_id): ?>
        <div class="chat-box mb-3">
            <?php foreach ($chat as $msg): ?>
                <div class="chat-msg <?= $msg['sender_id']==$user_id ? 'me' : '' ?>">
                    <span class="sender <?= $msg['sender_id']==$admin_id ? 'admin' : 'user' ?>">
                        <?= htmlspecialchars($msg['sender_name']) ?>
                    </span>:
                    <?= htmlspecialchars($msg['content']) ?>
                    <span class="time ms-2"><?= $msg['sent_at'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" class="d-flex gap-2">
            <input type="text" name="content" class="form-control" placeholder="Type your message..." required>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
