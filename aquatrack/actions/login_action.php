<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('login.php');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'staff';

$st = $pdo->prepare("SELECT * FROM users WHERE (username=? OR full_name=?) AND role=? AND account_status='active' LIMIT 1");
$st->execute([$username, $username, $role]);
$u = $st->fetch();

if (!$u || $u['password'] !== $password) {
    $_SESSION['login_err'] = 'Invalid credentials for the selected role.';
    redirect('login.php');
}

$_SESSION['user'] = [
    'user_id'  => (int)$u['user_id'],
    'full_name'=> $u['full_name'],
    'username' => $u['username'],
    'role'     => $u['role'],
    'customer_id' => $u['customer_id'] ? (int)$u['customer_id'] : null,
];
$pdo->prepare("UPDATE users SET last_login=NOW() WHERE user_id=?")->execute([$u['user_id']]);
redirect($u['role'] . '/dashboard.php');