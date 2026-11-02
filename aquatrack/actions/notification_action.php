<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
verify_login();
verify_csrf();
$u = current_user();
$pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$u['user_id']]);
redirect($u['role'] . '/notifications.php');