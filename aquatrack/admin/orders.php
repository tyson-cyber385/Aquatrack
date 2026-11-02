<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$q = trim($_GET['q'] ?? '');
$sql = "SELECT o.*, c.name AS customer FROM orders o JOIN customers c ON c.customer_id=o.customer_id";
$params = [];
if ($q !== '') { $sql .= " WHERE c.name LIKE ? OR o.order_id = ? OR o.delivery_status LIKE ? OR o.payment_status LIKE ?"; $params = ["%$q%", (int)$q, "%$q%", "%$q%"]; }
$sql .= " ORDER BY o.order_id DESC";
$st = $pdo->prepare($sql); $st->execute($params); $rows = $st->fetchAll();
$products = $pdo->query("SELECT * FROM products ORDER BY product_name")->fetchAll();
$customers = $pdo->query("SELECT * FROM customers ORDER BY name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">Orders</h2>
  <button class="btn btn-aqua" data-bs-toggle="modal" data-bs-target="#orderModal"><i class="eva eva-plus-outline"></i> Create Order</button>
</div>
<form class="row g-2 mb-3" method="get"><div class="col-md-6"><input name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search by customer, order ID, status, payment"></div><div class="col-md-2"><button class="btn btn-outline-secondary w-100">Search</button></div></form>

<div class="card-soft"><div class="table-responsive"><table class="table table-aqua align-middle">
  <thead><tr><th>#</th><th>Customer</th><th>Delivery</th><th>Payment</th><th>Total</th><th>Created</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $o): ?>
    <tr>
      <td>#<?= $o['order_id'] ?></td>
      <td><?= e($o['customer']) ?></td>
      <td><span class="badge badge-<?= e($o['delivery_status']) ?>"><?= str_replace('_',' ', $o['delivery_status']) ?></span></td>
      <td><span class="badge badge-<?= e($o['payment_status']) ?>"><?= $o['payment_status'] ?></span></td>
      <td><?= money($o['total_amount']) ?></td>
      <td><small><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></small></td>
      <td class="text-end">
        <?php if ($o['delivery_status'] === 'pending'): ?>
          <form class="d-inline" method="post" action="<?= BASE_URL ?>actions/order_action.php" onsubmit="return confirm('Cancel order?')">
            <?= csrf_field() ?><input type="hidden" name="op" value="cancel"><input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="eva eva-close-circle-outline"></i></button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>

<div class="modal fade" id="orderModal" tabindex="-1"><div class="modal-dialog modal-lg"><form method="post" action="<?= BASE_URL ?>actions/order_action.php" class="modal-content">
  <?= csrf_field() ?><input type="hidden" name="op" value="create">
  <div class="modal-header"><h5 class="modal-title">Create Order</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <label class="form-label">Customer</label>
    <select name="customer_id" class="form-select mb-2" required>
      <option value="">— Select —</option>
      <?php foreach ($customers as $c): ?><option value="<?= $c['customer_id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
    </select>
    <label class="form-label">Items</label>
    <?php foreach ($products as $p): ?>
      <div class="d-flex align-items-center mb-2">
        <div class="flex-grow-1"><?= e($p['product_name']) ?> <small class="text-muted">(<?= money($p['unit_price']) ?>, stock <?= $p['stock_quantity'] ?>)</small></div>
        <input type="number" min="0" name="items[<?= $p['product_id'] ?>]" value="0" class="form-control" style="width:100px">
      </div>
    <?php endforeach; ?>
    <label class="form-label mt-2">Delivery Address</label>
    <input name="delivery_address" class="form-control mb-2">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control"></textarea>
  </div>
  <div class="modal-footer"><button class="btn btn-aqua">Create Order</button></div>
</form></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>