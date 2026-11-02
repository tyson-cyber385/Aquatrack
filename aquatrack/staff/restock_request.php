<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');
$products = $pdo->query("SELECT * FROM products ORDER BY product_name")->fetchAll();
$u = current_user();
$mine = $pdo->prepare("SELECT r.*, p.product_name FROM restock_requests r JOIN products p ON p.product_id=r.product_id WHERE r.requested_by_user_id=? ORDER BY r.request_id DESC");
$mine->execute([$u['user_id']]); $mine = $mine->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Restock Request</h2>
<div class="row g-4">
  <div class="col-md-5"><div class="card-soft">
    <h5>Send Request</h5>
    <form method="post" action="<?= BASE_URL ?>actions/restock_action.php">
      <?= csrf_field() ?><input type="hidden" name="op" value="request">
      <label class="form-label">Product</label>
      <select name="product_id" class="form-select mb-2" required>
        <option value="">— Select —</option>
        <?php foreach ($products as $p): ?><option value="<?= $p['product_id'] ?>"><?= e($p['product_name']) ?> (stock <?= $p['stock_quantity'] ?>)</option><?php endforeach; ?>
      </select>
      <label class="form-label">Quantity</label>
      <input type="number" name="quantity_requested" min="1" value="10" class="form-control mb-2" required>
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control mb-2" placeholder="Why is a restock needed?"></textarea>
      <button class="btn btn-aqua">Send to Admin</button>
    </form>
  </div></div>
  <div class="col-md-7"><div class="card-soft">
    <h5>My Requests</h5>
    <?php if (!$mine): ?><p class="text-muted small">No requests yet.</p><?php endif; ?>
    <?php foreach ($mine as $r): ?>
      <div class="list-item">
        <strong><?= e($r['product_name']) ?></strong> × <?= $r['quantity_requested'] ?>
        <span class="badge bg-<?= $r['status']==='approved'?'success':($r['status']==='rejected'?'danger':'secondary') ?>"><?= $r['status'] ?></span>
        <br><small class="text-muted"><?= e($r['message']) ?></small>
      </div>
    <?php endforeach; ?>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>