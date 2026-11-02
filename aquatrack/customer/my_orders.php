<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();
$cid = $u['customer_id'];

// Fetch only this customer's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_id DESC");
$stmt->execute([$cid]);
$orders = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">My Orders</h2>
  <a href="<?= BASE_URL ?>customer/new_order.php" class="btn btn-aqua">
    <i class="eva eva-plus-outline"></i> New Order
  </a>
</div>

<div class="card-soft">
  <div class="table-responsive">
    <table class="table table-aqua align-middle">
      <thead>
        <tr>
          <th>Order #</th>
          <th>Date</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Delivery Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$orders): ?>
        <tr><td colspan="6" class="text-muted text-center py-3">You haven't placed any orders yet.</td></tr>
      <?php endif; ?>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= $o['order_id'] ?></td>
          <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
          <td><?= money($o['total_amount']) ?></td>
          <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= $o['payment_status'] ?></span></td>
          <td><span class="badge badge-<?= e($o['delivery_status']) ?>"><?= str_replace('_', ' ', $o['delivery_status']) ?></span></td>
          <td class="text-end">
            <?php if ($o['delivery_status'] === 'pending'): ?>
              <form method="post" action="<?= BASE_URL ?>actions/order_action.php" onsubmit="return confirm('Cancel this order?')">
                <?= csrf_field() ?>
                <input type="hidden" name="op" value="cancel">
                <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Cancel</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>