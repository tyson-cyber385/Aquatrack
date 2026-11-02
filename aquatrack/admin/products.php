<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$q = trim($_GET['q'] ?? '');
$rows = $q ? $pdo->prepare("SELECT * FROM products WHERE product_name LIKE ? ORDER BY product_id DESC") : null;
if ($rows) { $rows->execute(["%$q%"]); } else { $rows = $pdo->query("SELECT * FROM products ORDER BY product_id DESC"); }

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">Products</h2>
  <button class="btn btn-aqua" data-bs-toggle="modal" data-bs-target="#prodModal"><i class="eva eva-plus-outline"></i> Add Product</button>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-md-6"><input name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search products"></div>
  <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Search</button></div>
</form>

<div class="card-soft"><div class="table-responsive"><table class="table table-aqua align-middle">
  <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Price</th><th>Stock</th><th>Threshold</th><th></th></tr></thead>
  <tbody>
  <?php if (!$rows->rowCount()): ?>
    <tr><td colspan="7" class="text-center text-muted py-3">No products found.</td></tr>
  <?php endif; ?>
  <?php foreach ($rows as $p): ?>
    <tr>
      <td>#<?= $p['product_id'] ?></td>
      <td><?= e($p['product_name']) ?></td>
      <td><?= str_replace('_',' ', $p['product_type']) ?></td>
      <td><?= money($p['unit_price']) ?></td>
      <td><?= $p['stock_quantity'] ?><?php if ($p['stock_quantity'] <= $p['low_stock_threshold']): ?> <span class="badge bg-warning text-dark">Low</span><?php endif; ?></td>
      <td><?= $p['low_stock_threshold'] ?></td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#prodModal"
          data-id="<?= $p['product_id'] ?>" data-name="<?= e($p['product_name']) ?>"
          data-type="<?= e($p['product_type']) ?>" data-price="<?= $p['unit_price'] ?>"
          data-stock="<?= $p['stock_quantity'] ?>" data-thr="<?= $p['low_stock_threshold'] ?>"><i class="eva eva-edit-outline"></i></button>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#restockModal"
          data-id="<?= $p['product_id'] ?>" data-name="<?= e($p['product_name']) ?>"><i class="eva eva-refresh-outline"></i></button>
        <form class="d-inline" method="post" action="<?= BASE_URL ?>actions/product_action.php"
          onsubmit="return confirm('Delete product?')">
          <?= csrf_field() ?><input type="hidden" name="op" value="delete">
          <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="eva eva-trash-2-outline"></i></button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>

<div class="modal fade" id="prodModal" tabindex="-1"><div class="modal-dialog"><form method="post" action="<?= BASE_URL ?>actions/product_action.php" class="modal-content">
  <?= csrf_field() ?>
  <div class="modal-header"><h5 class="modal-title">Product</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="product_id" id="p_id">
    <label class="form-label">Name</label><input name="product_name" id="p_name" class="form-control mb-2" required>
    <label class="form-label">Type</label>
    <select name="product_type" id="p_type" class="form-select mb-2"><option value="refill">Refill</option><option value="new_gallon">New Gallon</option><option value="dispenser">Dispenser</option></select>
    <label class="form-label">Unit Price</label><input type="number" step="0.01" min="0" name="unit_price" id="p_price" class="form-control mb-2" required>
    <label class="form-label">Stock Quantity</label><input type="number" min="0" name="stock_quantity" id="p_stock" class="form-control mb-2">
    <label class="form-label">Low Stock Threshold</label><input type="number" min="0" name="low_stock_threshold" id="p_thr" class="form-control">
  </div>
  <div class="modal-footer"><button class="btn btn-aqua">Save</button></div>
</form></div></div>

<div class="modal fade" id="restockModal" tabindex="-1"><div class="modal-dialog"><form method="post" action="<?= BASE_URL ?>actions/product_action.php" class="modal-content">
  <?= csrf_field() ?><input type="hidden" name="op" value="restock">
  <div class="modal-header"><h5 class="modal-title">Restock <span id="rs_name"></span></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="product_id" id="rs_id">
    <label class="form-label">Quantity to add</label>
    <input type="number" min="1" name="quantity" class="form-control" value="10" required>
  </div>
  <div class="modal-footer"><button class="btn btn-aqua">Restock</button></div>
</form></div></div>

<script>
document.getElementById('prodModal').addEventListener('show.bs.modal', e => {
  const b = e.relatedTarget; const m = document.getElementById('prodModal');
  m.querySelector('#p_id').value = b.dataset.id || '';
  m.querySelector('#p_name').value = b.dataset.name || '';
  m.querySelector('#p_type').value = b.dataset.type || 'refill';
  m.querySelector('#p_price').value = b.dataset.price || 0;
  m.querySelector('#p_stock').value = b.dataset.stock || 0;
  m.querySelector('#p_thr').value = b.dataset.thr || 10;
});
document.getElementById('restockModal').addEventListener('show.bs.modal', e => {
  const b = e.relatedTarget;
  document.getElementById('rs_id').value = b.dataset.id;
  document.getElementById('rs_name').textContent = b.dataset.name;
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>