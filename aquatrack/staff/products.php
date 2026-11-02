<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');
$products = $pdo->query("SELECT * FROM products ORDER BY product_name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Products (View Only)</h2>
<div class="card-soft"><table class="table table-aqua align-middle">
  <thead><tr><th>Name</th><th>Type</th><th>Price</th><th>Stock</th></tr></thead><tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?= e($p['product_name']) ?></td>
      <td><?= str_replace('_',' ', $p['product_type']) ?></td>
      <td><?= money($p['unit_price']) ?></td>
      <td><?= $p['stock_quantity'] ?><?php if ($p['stock_quantity'] <= $p['low_stock_threshold']): ?> <span class="badge bg-warning text-dark">Low</span><?php endif; ?></td>
    </tr>
  <?php endforeach; ?>
</tbody></table></div>
<a href="<?= BASE_URL ?>staff/restock_request.php" class="btn btn-aqua mt-3"><i class="eva eva-refresh-outline"></i> Request Restock</a>
<?php include __DIR__ . '/../includes/footer.php'; ?>