<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$rows = $pdo->query("SELECT r.*, p.product_name, u.full_name FROM restock_requests r
  JOIN products p ON p.product_id=r.product_id
  JOIN users u ON u.user_id=r.requested_by_user_id
  WHERE r.status='pending' ORDER BY r.request_id DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Restock Requests</h2>
<div class="card-soft">
  <?php if (!$rows): ?><p class="text-muted">No pending requests.</p><?php endif; ?>
  <?php foreach ($rows as $r): ?>
    <div class="list-item d-flex justify-content-between align-items-center">
      <div>
        <strong><?= e($r['product_name']) ?></strong> × <?= $r['quantity_requested'] ?><br>
        <small class="text-muted">By <?= e($r['full_name']) ?> · <?= date('M d H:i', strtotime($r['created_at'])) ?><br><?= e($r['message']) ?></small>
      </div>
      <div class="d-flex gap-2">
        <form method="post" action="<?= BASE_URL ?>actions/restock_action.php">
          <?= csrf_field() ?><input type="hidden" name="op" value="approve"><input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
          <button class="btn btn-sm btn-aqua">Approve</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>actions/restock_action.php">
          <?= csrf_field() ?><input type="hidden" name="op" value="reject"><input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
          <button class="btn btn-sm btn-outline-danger">Reject</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>