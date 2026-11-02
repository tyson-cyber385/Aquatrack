<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$c = dashboard_counts($pdo);
$recentOrders = $pdo->query("SELECT o.*, c.name AS customer FROM orders o JOIN customers c ON c.customer_id=o.customer_id ORDER BY o.order_id DESC LIMIT 6")->fetchAll();
$restocks = $pdo->query("SELECT r.*, p.product_name, u.full_name FROM restock_requests r JOIN products p ON p.product_id=r.product_id JOIN users u ON u.user_id=r.requested_by_user_id WHERE r.status='pending' ORDER BY r.request_id DESC LIMIT 5")->fetchAll();
$week = $pdo->query("SELECT DATE(created_at) d, SUM(total_amount) t FROM orders WHERE payment_status='paid' AND created_at >= NOW()-INTERVAL 6 DAY GROUP BY DATE(created_at)")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Welcome back, Admin</h2>
<p class="text-muted">Here is what's happening at your station today.</p>

<div class="cards-grid">
  <div class="stat-card"><i class="eva eva-trending-up-outline"></i><div><span>Today's Sales</span><strong><?= money($c['salesToday']) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-car-outline"></i><div><span>Pending Deliveries</span><strong><?= $c['pendingDeliveries'] ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-credit-card-outline"></i><div><span>Unpaid Balances</span><strong><?= money($c['unpaid']) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-people-outline"></i><div><span>Total Customers</span><strong><?= $c['customers'] ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-alert-triangle-outline"></i><div><span>Low Stock Alerts</span><strong><?= $c['lowStock'] ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-activity-outline"></i><div><span>Staff On Shift</span><strong><?= $c['staffOnShift'] ?></strong></div></div>
</div>

<div class="row g-4 mt-1">
  <div class="col-lg-8"><div class="card-soft"><h5>Weekly Sales Overview</h5><canvas id="salesChart" height="90"></canvas></div></div>
  <div class="col-lg-4"><div class="card-soft"><h5>Restock Requests</h5>
    <?php if (!$restocks): ?><p class="text-muted small">No pending requests.</p><?php endif; ?>
    <?php foreach ($restocks as $r): ?>
      <div class="list-item">
        <div><strong><?= e($r['product_name']) ?></strong> × <?= $r['quantity_requested'] ?><br>
          <small class="text-muted">by <?= e($r['full_name']) ?></small></div>
        <a class="btn btn-sm btn-aqua" href="<?= BASE_URL ?>admin/restock_requests.php">Review</a>
      </div>
    <?php endforeach; ?>
  </div></div>
</div>

<div class="card-soft mt-4"><h5>Recent Orders</h5>
  <div class="table-responsive"><table class="table table-aqua align-middle">
    <thead><tr><th>#</th><th>Customer</th><th>Delivery</th><th>Payment</th><th>Total</th></tr></thead>
    <tbody>
    <?php foreach ($recentOrders as $o): ?>
      <tr>
        <td>#<?= $o['order_id'] ?></td>
        <td><?= e($o['customer']) ?></td>
        <td><span class="badge badge-<?= e($o['delivery_status']) ?>"><?= str_replace('_',' ', $o['delivery_status']) ?></span></td>
        <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= $o['payment_status'] ?></span></td>
        <td><?= money($o['total_amount']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>

<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_map(fn($r) => date('M d', strtotime($r['d'])), $week)) ?>,
    datasets: [{ label: 'Sales', data: <?= json_encode(array_map(fn($r) => (float)$r['t'], $week)) ?>, backgroundColor: '#4d8af0', borderRadius: 6 }]
  },
  options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>