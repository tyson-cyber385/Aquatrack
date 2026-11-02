<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin','staff']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($_SESSION['user']['role'] . '/balances.php');
verify_csrf();
$u = current_user();
$orderId = (int)$_POST['order_id'];
$amount = (float)$_POST['amount_paid'];
$method = $_POST['payment_method'] ?? 'cash';

$st = $pdo->prepare("SELECT * FROM orders WHERE order_id=?"); $st->execute([$orderId]); $o = $st->fetch();
if (!$o) { flash('danger','Order not found.'); redirect($u['role'] . '/balances.php'); }
if ($amount <= 0) { flash('danger','Invalid amount.'); redirect($u['role'] . '/balances.php'); }

$pdo->prepare("INSERT INTO payments (order_id,customer_id,amount_paid,payment_method,recorded_by_user_id) VALUES (?,?,?,?,?)")
    ->execute([$orderId,$o['customer_id'],$amount,$method,$u['user_id']]);
$pdo->prepare("UPDATE orders SET payment_status='paid' WHERE order_id=?")->execute([$orderId]);
flash('success','Payment recorded.');
redirect($u['role'] . '/balances.php');