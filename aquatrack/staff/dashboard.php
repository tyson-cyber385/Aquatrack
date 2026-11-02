<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');
$c = dashboard_counts($pdo);
$u = current_user();
$today = date('Y-m-d');
$myOrders = $pdo->prepare("SELECT o.*, c.name AS customer FROM orders o JOIN customers c ON c.customer_id=o.customer_id WHERE DATE(o.created_at)=? ORDER BY o.order_id DESC LIMIT 8");
$myOrders->execute([$today]); $myOrders = $myOrders->fetchAll();
$queue = $pdo->query("SELECT o.*, c.name AS customer FROM orders o JOIN customers c ON c.customer_id=o.customer_id WHERE o.delivery_status IN ('pending','out_for_delivery') ORDER BY o.order_id DESC LIMIT 8")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Welcome back, <?= e($u['full_name']) ?></h2>
<p class="text-muted">Here is what's happening at your station today.</p>
<div class="cards-grid">
  <div class="stat-card"><i class="eva eva-trending-up-outline"></i><div><span>Today's Sales</span><strong><?= money($c['salesToday']) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-car-outline"></i><div><span>Pending Deliveries</span><strong><?= $c['pendingDeliveries'] ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-credit-card-outline"></i><div><span>Unpaid Balances</span><strong><?= money($c['unpaid']) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-people-outline"></i><div><span>Total Customers</span><strong><?= $c['customers'] ?></strong></div></div>
</div>
<div class="row g-4 mt-1">
  <div class="col-lg-6"><div class="card-soft"><h5>Today's Orders</h5>
    <?php if (!$myOrders): ?><p class="text-muted small">No orders today.</p><?php endif; ?>
    <?php foreach ($myOrders as $o): ?>
      <div class="list-item d-flex justify-content-between">
        <div><strong>#<?= $o['order_id'] ?></strong> — <?= e($o['customer']) ?></div>
        <div><?= money($o['total_amount']) ?></div>
      </div>
    <?php endforeach; ?>
  </div></div>
  <div class="col-lg-6"><div class="card-soft"><h5>Delivery Queue</h5>
    <?php if (!$queue): ?><p class="text-muted small">No pending deliveries.</p><?php endif; ?>
    <?php foreach ($queue as $o): ?>
      <div class="list-item d-flex justify-content-between align-items-center">
        <div><strong>#<?= $o['order_id'] ?></strong> — <?= e($o['customer']) ?> <span class="badge badge-<?= e($o['delivery_status']) ?>"><?= str_replace('_',' ', $o['delivery_status']) ?></span></div>
        <a href="<?= BASE_URL ?>staff/deliveries.php" class="btn btn-sm btn-aqua">Open</a>
      </div>
    <?php endforeach; ?>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>