<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');
$today = date('Y-m-d');
$salesToday = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid' AND DATE(created_at)='$today'")->fetchColumn();
$unpaid = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='unpaid' AND delivery_status<>'cancelled'")->fetchColumn();
$paidCnt = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='paid' AND DATE(created_at)='$today'")->fetchColumn();
$unpaidCnt = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='unpaid' AND DATE(created_at)='$today'")->fetchColumn();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Reports (Limited)</h2>
<div class="cards-grid">
  <div class="stat-card"><i class="eva eva-trending-up-outline"></i><div><span>Today's Sales</span><strong><?= money($salesToday) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-credit-card-outline"></i><div><span>Unpaid Balances</span><strong><?= money($unpaid) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-checkmark-circle-outline"></i><div><span>Paid Orders Today</span><strong><?= $paidCnt ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-clock-outline"></i><div><span>Unpaid Orders Today</span><strong><?= $unpaidCnt ?></strong></div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>