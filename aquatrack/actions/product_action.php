<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/products.php');
verify_csrf();

$op = $_POST['op'] ?? 'save';
$id = (int)($_POST['product_id'] ?? 0);

if ($op === 'delete') {
    $pdo->prepare("DELETE FROM products WHERE product_id=?")->execute([$id]);
    flash('success','Product deleted.'); redirect('admin/products.php');
}
if ($op === 'restock') {
    $qty = max(0,(int)$_POST['quantity']);
    $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id=?")->execute([$qty,$id]);
    if (!empty($_POST['request_id'])) {
        $pdo->prepare("UPDATE restock_requests SET status='approved', resolved_at=NOW() WHERE request_id=?")
            ->execute([(int)$_POST['request_id']]);
        $req = $pdo->prepare("SELECT requested_by_user_id FROM restock_requests WHERE request_id=?");
        $req->execute([(int)$_POST['request_id']]); $r = $req->fetch();
        if ($r) notify($pdo, $r['requested_by_user_id'],'Restock approved','Your restock request has been approved.');
    }
    flash('success','Stock updated.'); redirect('admin/products.php');
}

$name = trim($_POST['product_name'] ?? '');
$type = $_POST['product_type'] ?? 'refill';
$price = (float)($_POST['unit_price'] ?? 0);
$stock = max(0,(int)($_POST['stock_quantity'] ?? 0));
$thr   = max(0,(int)($_POST['low_stock_threshold'] ?? 10));
if ($name === '') { flash('danger','Product name required.'); redirect('admin/products.php'); }

if ($id > 0) {
    $pdo->prepare("UPDATE products SET product_name=?,product_type=?,unit_price=?,stock_quantity=?,low_stock_threshold=? WHERE product_id=?")
        ->execute([$name,$type,$price,$stock,$thr,$id]);
    flash('success','Product updated.');
} else {
    $pdo->prepare("INSERT INTO products (product_name,product_type,unit_price,stock_quantity,low_stock_threshold) VALUES (?,?,?,?,?)")
        ->execute([$name,$type,$price,$stock,$thr]);
    flash('success','Product added.');
}
redirect('admin/products.php');