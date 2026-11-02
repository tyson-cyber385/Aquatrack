<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin','staff','customer']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($_SESSION['user']['role'] . '/dashboard.php');
verify_csrf();
$u = current_user();
$role = $u['role'];
$op = $_POST['op'] ?? 'create';

// Cancel
if ($op === 'cancel') {
    $id = (int)$_POST['order_id'];
    $pdo->prepare("UPDATE orders SET delivery_status='cancelled' WHERE order_id=? AND delivery_status='pending'")->execute([$id]);
    $pdo->prepare("INSERT INTO order_status_history (order_id,status,changed_by_user_id) VALUES (?,?,?)")
        ->execute([$id,'cancelled',$u['user_id']]);
    flash('success','Order cancelled.');
    redirect($role . '/' . ($role === 'customer' ? 'my_orders.php' : 'orders.php'));
}

// Update delivery status (staff/admin)
if ($op === 'advance') {
    $id = (int)$_POST['order_id'];
    $st = $pdo->prepare("SELECT * FROM orders WHERE order_id=?"); $st->execute([$id]); $o = $st->fetch();
    if ($o) {
        $next = next_delivery_status($o['delivery_status']);
        if ($next !== $o['delivery_status']) {
            $pdo->prepare("UPDATE orders SET delivery_status=?, processed_by_user_id=?, updated_at=NOW() WHERE order_id=?")
                ->execute([$next,$u['user_id'],$id]);
            $pdo->prepare("INSERT INTO order_status_history (order_id,status,changed_by_user_id) VALUES (?,?,?)")
                ->execute([$id,$next,$u['user_id']]);
            if ($next === 'delivered') {
                // Face-to-face: auto-record payment
                $pdo->prepare("UPDATE orders SET payment_status='paid' WHERE order_id=?")->execute([$id]);
                $pdo->prepare("INSERT INTO payments (order_id,customer_id,amount_paid,payment_method,recorded_by_user_id) VALUES (?,?,?,'cash',?)")
                    ->execute([$id,$o['customer_id'],$o['total_amount'],$u['user_id']]);
                $custUser = $pdo->prepare("SELECT user_id FROM users WHERE customer_id=? LIMIT 1");
                $custUser->execute([$o['customer_id']]);
                if ($cu = $custUser->fetch()) notify($pdo, $cu['user_id'],'Order delivered & paid',"Order #$id has been delivered and recorded as paid.");
            }
        }
    }
    flash('success','Order status updated.');
    redirect($role . '/' . ($role === 'customer' ? 'my_orders.php' : 'deliveries.php'));
}

// Create
$customerId = (int)($_POST['customer_id'] ?? 0);
if ($role === 'customer') $customerId = $u['customer_id'];
if (!$customerId) { flash('danger','Select a customer.'); redirect($role . '/orders.php'); }

$items = $_POST['items'] ?? []; // product_id => qty
$address = trim($_POST['delivery_address'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$total = 0; $lineItems = [];
foreach ($items as $pid => $qty) {
    $qty = (int)$qty; if ($qty <= 0) continue;
    $st = $pdo->prepare("SELECT * FROM products WHERE product_id=?"); $st->execute([(int)$pid]); $p = $st->fetch();
    if (!$p) continue;
    $sub = $p['unit_price'] * $qty;
    $total += $sub;
    $lineItems[] = ['pid'=>(int)$pid,'qty'=>$qty,'price'=>$p['unit_price'],'sub'=>$sub];
}
if (!$lineItems) { flash('danger','Add at least one item.'); redirect($role . '/orders.php'); }

$pdo->beginTransaction();
try {
    $pdo->prepare("INSERT INTO orders (customer_id,created_by_user_id,delivery_status,payment_status,total_amount,delivery_address,notes) VALUES (?,?,'pending','unpaid',?,?,?)")
        ->execute([$customerId,$u['user_id'],$total,$address,$notes]);
    $oid = (int)$pdo->lastInsertId();
    foreach ($lineItems as $li) {
        $pdo->prepare("INSERT INTO order_items (order_id,product_id,quantity,unit_price,subtotal) VALUES (?,?,?,?,?)")
            ->execute([$oid,$li['pid'],$li['qty'],$li['price'],$li['sub']]);
        $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE product_id=?")
            ->execute([$li['qty'],$li['pid']]);
    }
    $pdo->prepare("INSERT INTO order_status_history (order_id,status,changed_by_user_id) VALUES (?,?,?)")
        ->execute([$oid,'pending',$u['user_id']]);
    $pdo->commit();
    flash('success','Order created.');
} catch (Exception $e) {
    $pdo->rollBack();
    flash('danger','Failed to create order: ' . $e->getMessage());
}
redirect($role . '/' . ($role === 'customer' ? 'my_orders.php' : 'orders.php'));