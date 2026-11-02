<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');

$filter = $_GET['status'] ?? '';
$sql = "SELECT o.*, c.name AS customer
        FROM orders o
        JOIN customers c ON c.customer_id = o.customer_id
        WHERE 1=1";
$params = [];
if ($filter !== '') {
    $sql .= " AND o.delivery_status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY o.order_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">Deliveries</h2>

<form class="row g-2 mb-3" method="get">
  <div class="col-md-4">
    <select name="status" class="form-select" onchange="this.form.submit()">
      <option value="">All statuses</option>
      <?php foreach (['pending','out_for_delivery','delivered','cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $filter === $s ? 'selected' : '' ?>>
          <?= str_replace('_', ' ', $s) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</form>

<div class="card-soft">
  <div class="table-responsive">
    <table class="table table-aqua align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Address</th>
          <th>Status</th>
          <th>Payment</th>
          <th>Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="7" class="text-muted text-center py-3">No deliveries found.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $o): ?>
        <tr>
          <td>#<?= $o['order_id'] ?></td>
          <td><?= e($o['customer']) ?></td>
          <td><?= e($o['delivery_address']) ?></td>
          <td><span class="badge badge-<?= e($o['delivery_status']) ?>"><?= str_replace('_', ' ', $o['delivery_status']) ?></span></td>
          <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= $o['payment_status'] ?></span></td>
          <td><?= money($o['total_amount']) ?></td>
          <td class="text-end">
            <?php if (in_array($o['delivery_status'], ['pending','out_for_delivery'], true)): ?>
              <form method="post" action="<?= BASE_URL ?>actions/order_action.php">
                <?= csrf_field() ?>
                <input type="hidden" name="op" value="advance">
                <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                <button class="btn btn-sm btn-aqua">
                  <i class="eva eva-arrow-forward-outline"></i>
                  <?= $o['delivery_status'] === 'pending' ? 'Mark Out for Delivery' : 'Mark Delivered' ?>
                </button>
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