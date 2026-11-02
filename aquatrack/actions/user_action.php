<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/users.php');
verify_csrf();
$op = $_POST['op'] ?? 'save';
$id = (int)($_POST['user_id'] ?? 0);
if ($op === 'delete') {
    $pdo->prepare("DELETE FROM users WHERE user_id=? AND role<>'admin'")->execute([$id]);
    flash('success','User deleted.'); redirect('admin/users.php');
}
$full = trim($_POST['full_name']);
$uname = trim($_POST['username']);
$pass = $_POST['password'] ?: null;
$role = $_POST['role'] ?? 'staff';
$cid = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
$status = $_POST['account_status'] ?? 'active';
if ($id > 0) {
    if ($pass) $pdo->prepare("UPDATE users SET full_name=?,username=?,password=?,role=?,customer_id=?,account_status=? WHERE user_id=?")
        ->execute([$full,$uname,$pass,$role,$cid,$status,$id]);
    else $pdo->prepare("UPDATE users SET full_name=?,username=?,role=?,customer_id=?,account_status=? WHERE user_id=?")
        ->execute([$full,$uname,$role,$cid,$status,$id]);
    flash('success','User updated.');
} else {
    $pdo->prepare("INSERT INTO users (full_name,username,password,role,customer_id,account_status) VALUES (?,?,?,?,?,?)")
        ->execute([$full,$uname,$pass ?: '123',$role,$cid,$status]);
    flash('success','User created.');
}
redirect('admin/users.php');