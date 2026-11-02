<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/constants.php';

function redirect($path) {
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}
function current_user() {
    return $_SESSION['user'] ?? null;
}
function is_logged_in() { return isset($_SESSION['user']); }
function has_role($roles) {
    $u = current_user();
    if (!$u) return false;
    $roles = is_array($roles) ? $roles : [$roles];
    return in_array($u['role'], $roles, true);
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return CURRENCY . number_format((float)$n, 2); }
function flash($type, $msg) { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }
function get_flashes() {
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function verify_csrf() {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
        http_response_code(419);
        die('Invalid CSRF token.');
    }
}
function verify_login() {
    if (!is_logged_in()) redirect('login.php');
}
function require_role($roles) {
    verify_login();
    if (!has_role($roles)) {
        http_response_code(403);
        die('Access denied.');
    }
}
function notify($pdo, $userId, $title, $msg) {
    $st = $pdo->prepare("INSERT INTO notifications (user_id,title,message) VALUES (?,?,?)");
    $st->execute([$userId, $title, $msg]);
}
function unread_notification_count($pdo, $userId) {
    $st = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
    $st->execute([$userId]);
    return (int)$st->fetchColumn();
}
function unread_message_count($pdo, $userId) {
    $st = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_user_id=? AND is_read=0");
    $st->execute([$userId]);
    return (int)$st->fetchColumn();
}
function dashboard_counts($pdo) {
    $today = date('Y-m-d');
    $salesToday = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid' AND DATE(created_at)='$today'")->fetchColumn();
    $pendingDeliveries = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE delivery_status IN ('pending','out_for_delivery')")->fetchColumn();
    $unpaid = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='unpaid' AND delivery_status<>'cancelled'")->fetchColumn();
    $customers = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $lowStock = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= low_stock_threshold")->fetchColumn();
    $staffOnShift = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='staff' AND account_status='active' AND last_login >= NOW() - INTERVAL 8 HOUR")->fetchColumn();
    return compact('salesToday','pendingDeliveries','unpaid','customers','lowStock','staffOnShift');
}
function next_delivery_status($current) {
    return $current === 'pending' ? 'out_for_delivery'
         : ($current === 'out_for_delivery' ? 'delivered' : $current);
}