<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();
$cid = $u['customer_id'];

// Get latest order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_id DESC LIMIT 1");
$stmt->execute([$cid]);
$order = $stmt->fetch();

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">Track Delivery</h2>

<?php if (!$order): ?>
  <div class="card-soft">
    <p class="text-muted mb-0">No active orders to track.</p>
  </div>
<?php else: ?>
  <div class="card-soft mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="mb-0">Order #<?= $order['order_id'] ?></h5>
        <small class="text-muted">Placed on <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></small>
      </div>
      <span class="badge badge-<?= e($order['delivery_status']) ?> fs-6">
        <?= str_replace('_', ' ', $order['delivery_status']) ?>
      </span>
    </div>

    <?php if ($order['delivery_status'] === 'cancelled'): ?>
      <div class="alert alert-danger">This order was cancelled.</div>
    <?php else: ?>
      <!-- Visual Timeline -->
      <div class="d-flex justify-content-between position-relative mt-5 mb-4">
        <!-- Line behind -->
        <div style="position: absolute; top: 15px; left: 10%; right: 10%; height: 4px; background: #e2e8f0; z-index: 1;"></div>
        
        <?php 
          $statuses = ['pending' => 'Pending', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered'];
          $currentStatus = $order['delivery_status'];
          $reached = true;
          foreach($statuses as $key => $label): 
            if ($currentStatus !== $key && $reached) {
                $color = 'var(--primary)';
            } elseif ($currentStatus === $key) {
                $color = 'var(--primary)';
                $reached = false; // Stop coloring after this
            } else {
                $color = '#cbd5e1';
            }
        ?>
          <div class="text-center position-relative" style="z-index: 2; width: 33%;">
            <div style="width: 34px; height: 34px; background: <?= $color ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white;">
              <i class="eva eva-checkmark-outline"></i>
            </div>
            <div class="mt-2 fw-semibold" style="color: <?= $color ?>; font-size: 0.85rem;"><?= $label ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="mt-4 pt-3 border-top">
      <p class="mb-1"><strong>Delivery Address:</strong> <?= e($order['delivery_address']) ?></p>
      <p class="mb-1"><strong>Notes:</strong> <?= e($order['notes'] ?: 'None') ?></p>
      <p class="mb-0"><strong>Total Amount:</strong> <?= money($order['total_amount']) ?> (<?= ucfirst($order['payment_status']) ?>)</p>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>