<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
verify_login();
verify_csrf();

$u = current_user();
$fullName = trim($_POST['full_name'] ?? '');
$pass = $_POST['password'] ?? '';

if ($fullName === '') {
    flash('danger', 'Name cannot be empty.');
    redirect($u['role'] . '/settings.php');
}

// 1. Update the Users table (Full Name & Password)
$pdo->prepare("UPDATE users SET full_name=? WHERE user_id=?")->execute([$fullName, $u['user_id']]);
if ($pass !== '') {
    $pdo->prepare("UPDATE users SET password=? WHERE user_id=?")->execute([$pass, $u['user_id']]);
}

// 2. If the user is a Customer, also update the Customers table
if ($u['role'] === 'customer' && $u['customer_id']) {
    $contact = trim($_POST['contact_no'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $pdo->prepare("UPDATE customers SET name=?, contact_no=?, address=? WHERE customer_id=?")
        ->execute([$fullName, $contact, $address, $u['customer_id']]);
}

// Update session name so the topbar updates immediately
$_SESSION['user']['full_name'] = $fullName;

flash('success', 'Profile updated successfully.');
redirect($u['role'] . '/settings.php');