<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();
$cid = $u['customer_id'];

// Get customer address to pre-fill
$stmt = $pdo->prepare("SELECT address FROM customers WHERE customer_id = ?");
$stmt->execute([$cid]);
$defaultAddress = $stmt->fetchColumn() ?: '';

// Get available products
$products = $pdo->query("SELECT * FROM products WHERE stock_quantity > 0 ORDER BY product_name")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">Place a New Order</h2>
<p class="text-muted mb-4">Select the items you need, and we will deliver them to your address.</p>

<div class="row">
  <div class="col-lg-8">
    <div class="card-soft">
      <form method="post" action="<?= BASE_URL ?>actions/order_action.php">
        <?= csrf_field() ?>
        <input type="hidden" name="op" value="create">
        
        <h5 class="mb-3">1. Select Items</h5>
        <?php if (!$products): ?>
          <div class="alert alert-warning">Sorry, we are currently out of stock on all items.</div>
        <?php endif; ?>
        
        <?php foreach ($products as $p): ?>
          <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
            <div class="flex-grow-1">
              <strong><?= e($p['product_name']) ?></strong><br>
              <small class="text-muted"><?= money($p['unit_price']) ?> per item (Stock: <?= $p['stock_quantity'] ?>)</small>
            </div>
            <div style="width: 120px;">
              <label class="form-label small text-muted mb-1">Quantity</label>
              <input type="number" min="0" max="<?= $p['stock_quantity'] ?>" 
                     name="items[<?= $p['product_id'] ?>]" 
                     value="0" class="form-control text-center">
            </div>
          </div>
        <?php endforeach; ?>

        <h5 class="mb-3 mt-4">2. Delivery Details</h5>
        
        <label class="form-label">Delivery Address</label>
        <textarea name="delivery_address" class="form-control mb-3" rows="2" required><?= e($defaultAddress) ?></textarea>

        <label class="form-label">Notes (Optional)</label>
        <textarea name="notes" class="form-control mb-4" rows="2" placeholder="e.g., Please deliver before 5 PM, or leave at the gate."></textarea>

        <button type="submit" class="btn btn-aqua w-100 py-2" <?= !$products ? 'disabled' : '' ?>>
          <i class="eva eva-shopping-cart-outline"></i> Place Order
        </button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>