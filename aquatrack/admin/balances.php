<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$unpaid = $pdo->query("SELECT o.*, c.name AS customer FROM orders o JOIN customers c ON c.customer_id=o.customer_id WHERE o.payment_status='unpaid' AND o.delivery_status<>'cancelled' ORDER BY o.order_id DESC")->fetchAll();
$payments = $pdo->query("SELECT p.*, c.name AS customer, u.full_name AS staff FROM payments p JOIN customers c ON c.customer_id=p.customer_id JOIN users u ON u.user_id=p.recorded_by_user_id ORDER BY p.payment_id DESC LIMIT 20")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Balances & Payments</h2>
<div class="row g-4">
  <div class="col-lg-7"><div class="card-soft"><h5>Unpaid Orders</h5>
    <?php if (!$unpaid): ?><p class="text-muted small">No unpaid orders.</p><?php endif; ?>
    <?php foreach ($unpaid as $o): ?>
      <div class="list-item d-flex justify-content-between align-items-center">
        <div><strong>#<?= $o['order_id'] ?> — <?= e($o['customer']) ?></strong><br>
          <small class="text-muted"><?= money($o['total_amount']) ?> · <?= $o['delivery_status'] ?></small></div>
        <form method="post" action="<?= BASE_URL ?>actions/payment_action.php" class="d-flex gap-2">
          <?= csrf_field() ?>
          <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
          <input type="number" step="0.01" name="amount_paid" value="<?= $o['total_amount'] ?>" class="form-control form-control-sm" style="width:110px">
          <select name="payment_method" class="form-select form-select-sm" style="width:110px">
            <option value="cash">Cash</option><option value="gcash">GCash</option><option value="bank_transfer">Bank</option>
          </select>
          <button class="btn btn-sm btn-aqua">Record</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div></div>
  <div class="col-lg-5"><div class="card-soft"><h5>Recent Payments</h5>
    <?php if (!$payments): ?><p class="text-muted small">No payments yet.</p><?php endif; ?>
    <?php foreach ($payments as $p): ?>
      <div class="list-item">
        <strong>#<?= $p['payment_id'] ?> — <?= e($p['customer']) ?></strong><br>
        <small class="text-muted"><?= money($p['amount_paid']) ?> · <?= $p['payment_method'] ?> · by <?= e($p['staff']) ?> · <?= date('M d H:i', strtotime($p['payment_date'])) ?></small>
      </div>
    <?php endforeach; ?>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>