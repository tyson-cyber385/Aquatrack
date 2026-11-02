<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin','staff']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(($_SESSION['user']['role']) . '/customers.php');
verify_csrf();

$op = $_POST['op'] ?? 'save';
$id = (int)($_POST['customer_id'] ?? 0);

if ($op === 'delete') {
    if (!has_role('admin')) { flash('danger','Only admin can delete customers.'); redirect('staff/customers.php'); }
    $pdo->prepare("DELETE FROM customers WHERE customer_id=?")->execute([$id]);
    flash('success','Customer deleted.'); redirect('admin/customers.php');
}

$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact_no'] ?? '');
$address = trim($_POST['address'] ?? '');
$containers = max(0, (int)($_POST['containers_borrowed'] ?? 0));
if ($name === '') { flash('danger','Name is required.'); redirect(($_SESSION['user']['role']) . '/customers.php'); }

if ($id > 0) {
    $pdo->prepare("UPDATE customers SET name=?, contact_no=?, address=?, containers_borrowed=? WHERE customer_id=?")
        ->execute([$name,$contact,$address,$containers,$id]);
    flash('success','Customer updated.');
} else {
    $pdo->prepare("INSERT INTO customers (name,contact_no,address,containers_borrowed) VALUES (?,?,?,?)")
        ->execute([$name,$contact,$address,$containers]);
    flash('success','Customer added.');
}
redirect($_SESSION['user']['role'] . '/customers.php');