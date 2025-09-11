<?php
session_start();
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user data from the session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';

// Get parameters for filtering and conversation selection
$selected_conversation_id = $_GET['conversation_id'] ?? null;
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Handle send message logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $receiver_id = $_POST['receiver_id'];
    $content = trim($_POST['content']);
    if ($receiver_id && !empty($content)) {
        // Prepare and execute the SQL statement to insert a new message
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content, status) VALUES (?, ?, ?, 'sent')");
        $stmt->execute([$user_id, $receiver_id, $content]);
        
        // Redirect back to the conversation after sending the message
        header("Location: messages.php?conversation_id=$receiver_id");
        exit;
    }
}

// Mark messages as 'delivered' when the user opens the messages page
$mark_as_delivered_stmt = $pdo->prepare("
    UPDATE messages
    SET status = 'delivered'
    WHERE receiver_id = ? AND status = 'sent'
");
$mark_as_delivered_stmt->execute([$user_id]);

// Fetch all conversation partners for the inbox list, including unread count
$conversations_query = $pdo->prepare("
    SELECT DISTINCT
        u.id AS partner_id,
        u.username AS partner_name,
        (SELECT COUNT(*) FROM messages m WHERE m.sender_id = u.id AND m.receiver_id = ? AND m.status IN ('sent', 'delivered')) AS unread_count
    FROM messages m
    JOIN users u ON u.id = CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY u.username ASC
");
$conversations_query->execute([$user_id, $user_id, $user_id, $user_id]);
$conversations = $conversations_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch messages for the selected conversation, with date filtering
$selected_messages = [];
$selected_partner_name = '';
if ($selected_conversation_id) {
    // Mark messages as read when the conversation is opened
    $mark_as_read_stmt = $pdo->prepare("
        UPDATE messages
        SET status = 'read', read_at = NOW()
        WHERE sender_id = ? AND receiver_id = ? AND status IN ('sent', 'delivered')
    ");
    $mark_as_read_stmt->execute([$selected_conversation_id, $user_id]);

    $messages_sql = "
        SELECT m.*, s.username AS sender_name, r.username AS receiver_name
        FROM messages m
        JOIN users s ON m.sender_id = s.id
        JOIN users r ON m.receiver_id = r.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
    ";
    $params = [$user_id, $selected_conversation_id, $selected_conversation_id, $user_id];

    if ($start_date) {
        $messages_sql .= " AND m.sent_at >= ?";
        $params[] = $start_date . " 00:00:00";
    }
    if ($end_date) {
        $messages_sql .= " AND m.sent_at <= ?";
        $params[] = $end_date . " 23:59:59";
    }

    $messages_sql .= " ORDER BY m.sent_at ASC";

    $messages_query = $pdo->prepare($messages_sql);
    $messages_query->execute($params);
    $selected_messages = $messages_query->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$selected_conversation_id]);
    $partner = $stmt->fetch();
    if ($partner) {
        $selected_partner_name = $partner['username'];
    }
}

// Fetch all users for the toggle box if the user is an admin or super admin
$all_users = [];
if ($user_role === 'admin' || $user_role === 'super_admin') {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE role IN ('user', 'admin', 'superadmin') AND id != ? ORDER BY username ASC");
    $stmt->execute([$user_id]);
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container-fluid { padding: 0; margin: 0; }
        .messages-container { display: flex; height: 100vh; }
        .inbox-pane { flex: 0 0 300px; border-right: 1px solid #e9ecef; background-color: #fff; padding: 1rem; overflow-y: auto; }
        .viewer-pane { flex: 1; padding: 1rem; display: flex; flex-direction: column; }
        .message-bubble { max-width: 80%; padding: 10px 15px; border-radius: 20px; word-wrap: break-word; }
        .sent-message { background-color: #0d6efd; color: white; align-self: flex-end; margin-left: auto; }
        .received-message { background-color: #e9ecef; color: #212529; align-self: flex-start; margin-right: auto; }
        .messages-history { flex-grow: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding-bottom: 15px; }
        .card.viewer-card { display: flex; flex-direction: column; height: 100%; }
        .read-status { font-size: 0.7em; opacity: 0.8; }
        .read-status .fas { color: #888; }
        .read-status .fa-check-double { color: #007bff; }
    </style>
</head>
<body>
    <div class="container-fluid messages-container">
        <div class="inbox-pane">
            <h4 class="mb-3">Conversations</h4>
            <?php if ($user_role === 'admin' || $user_role === 'super_admin'): ?>
                <div class="mb-3">
                    <h5 id="toggleUsers" class="mb-2" style="cursor: pointer;">
                        <i class="fas fa-users me-2"></i>All Users
                    </h5>
                    <div id="userList" class="list-group d-none">
                        <?php foreach ($all_users as $user): ?>
                            <a href="messages.php?conversation_id=<?= $user['id'] ?>" class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($user['username']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="list-group">
                <?php if (empty($conversations)): ?>
                    <p class="text-center text-muted">You have no conversations.</p>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="messages.php?conversation_id=<?= $conv['partner_id'] ?>" class="list-group-item list-group-item-action <?= $selected_conversation_id == $conv['partner_id'] ? 'active' : '' ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= htmlspecialchars($conv['partner_name']) ?></h6>
                                <?php if ($conv['unread_count'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><?= $conv['unread_count'] ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="viewer-pane">
            <?php if ($selected_conversation_id): ?>
                <div class="card viewer-card">
                    <div class="card-header bg-primary text-white d-flex align-items-center">
                        <a href="messages.php" class="btn btn-sm btn-outline-light me-3"><i class="fas fa-arrow-left"></i></a>
                        <h5 class="my-0"><i class="fas fa-envelope me-2"></i>Conversation with <strong><?= htmlspecialchars($selected_partner_name) ?></strong></h5>
                    </div>
                    <div class="card-body messages-history">
                        <?php if (empty($selected_messages)): ?>
                            <p class="text-center text-muted">No messages found for this date range.</p>
                        <?php else: ?>
                            <?php foreach ($selected_messages as $message): ?>
                                <div class="d-flex mb-2 <?= ($message['sender_id'] == $user_id) ? 'justify-content-end' : 'justify-content-start' ?>">
                                    <div class="message-bubble <?= ($message['sender_id'] == $user_id) ? 'sent-message' : 'received-message' ?>">
                                        <small class="d-block text-end read-status">
                                            <?= date("M d, Y h:i A", strtotime($message['sent_at'])) ?>
                                            <?php if ($message['sender_id'] == $user_id): // Only show status for messages you sent ?>
                                                <?php if ($message['status'] == 'read'): ?>
                                                    <i class="fas fa-check-double text-info"></i> <?php elseif ($message['status'] == 'delivered'): ?>
                                                    <i class="fas fa-check-double"></i> <?php else: ?>
                                                    <i class="fas fa-check"></i> <?php endif; ?>
                                            <?php endif; ?>
                                        </small>
                                        <?= nl2br(htmlspecialchars($message['content'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <h5 class="mb-3">Filter by Date</h5>
                        <form method="get" action="messages.php" class="mb-4">
                            <input type="hidden" name="conversation_id" value="<?= htmlspecialchars($selected_conversation_id) ?>">
                            <div class="row g-3">
                                <div class="col">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                                </div>
                                <div class="col">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                                </div>
                                <div class="col-auto d-flex align-items-end">
                                    <button type="submit" class="btn btn-secondary"><i class="fas fa-filter me-2"></i>Filter</button>
                                </div>
                            </div>
                        </form>
                        <form method="POST" action="messages.php" class="d-flex">
                            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($selected_conversation_id) ?>">
                            <textarea name="content" class="form-control me-2" rows="1" placeholder="Reply..." required></textarea>
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-2"></i>Send</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-5">
                    Select a conversation from the left to view messages.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleUsers');
            const userList = document.getElementById('userList');
            if (toggleButton && userList) {
                toggleButton.addEventListener('click', function() {
                    userList.classList.toggle('d-none');
                });
            }
        });
    </script>
</body>
</html>