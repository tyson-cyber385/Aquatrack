<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['staff','admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($_SESSION['user']['role'] . '/products.php');
verify_csrf();
$u = current_user();
$op = $_POST['op'] ?? 'request';

if ($op === 'reject') {
    require_role('admin');
    $rid = (int)$_POST['request_id'];
    $pdo->prepare("UPDATE restock_requests SET status='rejected', resolved_at=NOW() WHERE request_id=?")->execute([$rid]);
    $r = $pdo->prepare("SELECT requested_by_user_id FROM restock_requests WHERE request_id=?"); $r->execute([$rid]);
    if ($row = $r->fetch()) notify($pdo, $row['requested_by_user_id'],'Restock rejected','Your restock request was rejected.');
    flash('success','Request rejected.'); redirect('admin/restock_requests.php');
}

if ($op === 'approve') {
    require_role('admin');
    $rid = (int)$_POST['request_id'];
    $q = $pdo->prepare("SELECT * FROM restock_requests WHERE request_id=?"); $q->execute([$rid]); $r = $q->fetch();
    if ($r) {
        $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id=?")
            ->execute([$r['quantity_requested'], $r['product_id']]);
        $pdo->prepare("UPDATE restock_requests SET status='approved', resolved_at=NOW() WHERE request_id=?")->execute([$rid]);
        notify($pdo, $r['requested_by_user_id'],'Restock approved','Your restock request was approved.');
    }
    flash('success','Request approved and stock updated.'); redirect('admin/restock_requests.php');
}

// staff send request
$pid = (int)$_POST['product_id'];
$qty = max(1,(int)$_POST['quantity_requested']);
$msg = trim($_POST['message'] ?? '');
$pdo->prepare("INSERT INTO restock_requests (requested_by_user_id,product_id,quantity_requested,message) VALUES (?,?,?,?)")
    ->execute([$u['user_id'],$pid,$qty,$msg]);
// notify admin
$admins = $pdo->query("SELECT user_id FROM users WHERE role='admin'")->fetchAll();
foreach ($admins as $a) notify($pdo, $a['user_id'],'New restock request',"Staff {$u['full_name']} requested $qty of a product.");
flash('success','Restock request sent.');
redirect('staff/restock_request.php');