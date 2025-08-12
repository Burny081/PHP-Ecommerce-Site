<?php
// messages.php
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
// Handle send message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $content = trim($_POST['content']);
    if ($receiver_id && $content) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $receiver_id, $content]);
        $msg = "Message sent!";
    }
}
// Get all users except self
$users = $pdo->query("SELECT id, username FROM users WHERE id != $user_id")->fetchAll();
// Get messages sent or received by user
$messages = $pdo->prepare("SELECT m.*, u.username AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? OR m.sender_id = ? ORDER BY m.sent_at DESC LIMIT 20");
$messages->execute([$user_id, $user_id]);
$messages = $messages->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <h2 class="mb-4">Send a Message</h2>
    <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="receiver_id" class="form-label">To:</label>
            <select name="receiver_id" id="receiver_id" class="form-select" required>
                <option value="">Select user</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Message:</label>
            <textarea name="content" id="content" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
    <h3 class="mb-3">Recent Messages</h3>
    <ul class="list-group">
        <?php foreach ($messages as $m): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($m['sender_name']) ?>:</strong> <?= htmlspecialchars($m['content']) ?>
                <span class="text-muted float-end" style="font-size:0.9em;"><?= $m['sent_at'] ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
