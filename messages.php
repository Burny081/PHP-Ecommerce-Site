<?php
// messages.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle send message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $receiver_id = $_POST['receiver_id'];
    $content = trim($_POST['content']);
    if ($receiver_id && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $receiver_id, $content]);
        // Redirect to prevent form resubmission on refresh
        header("Location: messages.php?sent=1");
        exit;
    }
}

// Get all users except self
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ? ORDER BY username ASC");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll();

// Get conversations for the user, fetching both sender and receiver names
$messages = $pdo->prepare("
    SELECT
        m.content, m.sent_at, m.sender_id,
        s.username AS sender_name,
        r.username AS receiver_name
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    JOIN users r ON m.receiver_id = r.id
    WHERE m.receiver_id = ? OR m.sender_id = ?
    ORDER BY m.sent_at DESC LIMIT 50
");
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
    <?php if (isset($_GET['sent'])): ?><div class='alert alert-success'>Message sent!</div><?php endif; ?>
    <form method="POST" action="messages.php" class="mb-4">
        <div class="mb-3">
            <label for="receiver_id" class="form-label">To:</label>
            <select name="receiver_id" id="receiver_id" class="form-select" required>
                <option value="" disabled selected>Select a user...</option>
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
    <div class="list-group">
        <?php if (empty($messages)): ?>
            <p class="text-center">You have no messages.</p>
        <?php else: ?>
            <?php foreach ($messages as $m): ?>
                <div class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1">
                            <?php if ($m['sender_id'] == $user_id): ?>
                                <strong class="text-primary">You to <?= htmlspecialchars($m['receiver_name']) ?></strong>
                            <?php else: ?>
                                <strong class="text-success">From <?= htmlspecialchars($m['sender_name']) ?></strong>
                            <?php endif; ?>
                        </p>
                        <small class="text-muted"><?= date("M d, Y h:i A", strtotime($m['sent_at'])) ?></small>
                    </div>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($m['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
