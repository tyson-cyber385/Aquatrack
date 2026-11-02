<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');
$u = current_user();
$cid = $u['customer_id'];
$active = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE customer_id=$cid AND delivery_status IN ('pending','out_for_delivery')")->fetchColumn();
$unpaid = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE customer_id=$cid AND payment_status='unpaid' AND delivery_status<>'cancelled'")->fetchColumn();
$containers = (int)$pdo->query("SELECT containers_borrowed FROM customers WHERE customer_id=$cid")->fetchColumn();
$last = $pdo->query("SELECT delivery_status FROM orders WHERE customer_id=$cid ORDER BY order_id DESC LIMIT 1")->fetchColumn() ?: '—';
$orders = $pdo->query("SELECT * FROM orders WHERE customer_id=$cid ORDER BY order_id DESC LIMIT 6")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Welcome back, <?= e($u['full_name']) ?></h2>
<p class="text-muted">Here is your AquaTrack summary.</p>
<div class="cards-grid">
  <div class="stat-card"><i class="eva eva-file-text-outline"></i><div><span>Active Orders</span><strong><?= $active ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-car-outline"></i><div><span>Next Delivery</span><strong style="font-size:1rem"><?= str_replace('_',' ', $last) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-credit-card-outline"></i><div><span>Unpaid Balance</span><strong><?= money($unpaid) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-cube-outline"></i><div><span>Containers Borrowed</span><strong><?= $containers ?></strong></div></div>
</div>
<div class="card-soft mt-4"><h5>My Recent Orders</h5>
  <div class="table-responsive"><table class="table table-aqua align-middle">
    <thead><tr><th>#</th><th>Total</th><th>Delivery</th><th>Payment</th><th>Date</th></tr></thead><tbody>
    <?php foreach ($orders as $o): ?>
      <tr><td>#<?= $o['order_id'] ?></td><td><?= money($o['total_amount']) ?></td>
        <td><span class="badge badge-<?= e($o['delivery_status']) ?>"><?= str_replace('_',' ', $o['delivery_status']) ?></span></td>
        <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= $o['payment_status'] ?></span></td>
        <td><?= date('M d Y', strtotime($o['created_at'])) ?></td></tr>
    <?php endforeach; ?>
  </tbody></table></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>