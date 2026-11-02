<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin','staff']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($_SESSION['user']['role'] . '/messages.php');
verify_csrf();
$u = current_user();
$to = (int)$_POST['receiver_user_id'];
$msg = trim($_POST['message'] ?? '');
if ($msg !== '') {
    $pdo->prepare("INSERT INTO messages (sender_user_id,receiver_user_id,message) VALUES (?,?,?)")
        ->execute([$u['user_id'],$to,$msg]);
    notify($pdo, $to, 'New message', "You received a new message from {$u['full_name']}.");
}
redirect($u['role'] . '/messages.php');