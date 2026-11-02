<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();
$cid = $u['customer_id'];

// Get unpaid orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? AND payment_status = 'unpaid' AND delivery_status <> 'cancelled' ORDER BY order_id DESC");
$stmt->execute([$cid]);
$unpaid = $stmt->fetchAll();

// Get payment history
$stmt = $pdo->prepare("SELECT p.*, o.total_amount FROM payments p JOIN orders o ON o.order_id = p.order_id WHERE p.customer_id = ? ORDER BY p.payment_id DESC");
$stmt->execute([$cid]);
$payments = $stmt->fetchAll();

// Calculate total balance
$totalBalance = 0;
foreach ($unpaid as $o) { $totalBalance += $o['total_amount']; }

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">Balances & Payments</h2>

<div class="stat-card mb-4" style="max-width: 300px;">
  <i class="eva eva-credit-card-outline"></i>
  <div>
    <span>Total Unpaid Balance</span>
    <strong><?= money($totalBalance) ?></strong>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card-soft">
      <h5>Unpaid Orders</h5>
      <?php if (!$unpaid): ?>
        <p class="text-muted small mb-0">You have no unpaid balances. Great!</p>
      <?php endif; ?>
      <?php foreach ($unpaid as $o): ?>
        <div class="list-item d-flex justify-content-between align-items-center">
          <div>
            <strong>#<?= $o['order_id'] ?></strong><br>
            <small class="text-muted"><?= date('M d, Y', strtotime($o['created_at'])) ?></small>
          </div>
          <div class="text-end">
            <span class="badge badge-unpaid mb-1">Unpaid</span><br>
            <strong><?= money($o['total_amount']) ?></strong>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card-soft">
      <h5>Payment History</h5>
      <?php if (!$payments): ?>
        <p class="text-muted small mb-0">No payments recorded yet.</p>
      <?php endif; ?>
      <?php foreach ($payments as $p): ?>
        <div class="list-item">
          <div class="d-flex justify-content-between">
            <strong>Order #<?= $p['order_id'] ?></strong>
            <strong class="text-success"><?= money($p['amount_paid']) ?></strong>
          </div>
          <small class="text-muted">
            Paid via <?= ucfirst($p['payment_method']) ?> on <?= date('M d, Y h:i A', strtotime($p['payment_date'])) ?>
          </small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>