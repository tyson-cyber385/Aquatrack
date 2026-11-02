<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');
$st = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid' AND DATE(created_at) BETWEEN ? AND ?");
$st->execute([$from,$to]); $totalSales = (float)$st->fetchColumn();
$byProduct = $pdo->prepare("SELECT p.product_name, SUM(oi.quantity) qty, SUM(oi.subtotal) amt
  FROM order_items oi JOIN products p ON p.product_id=oi.product_id
  JOIN orders o ON o.order_id=oi.order_id
  WHERE o.payment_status='paid' AND DATE(o.created_at) BETWEEN ? AND ?
  GROUP BY p.product_id ORDER BY amt DESC");
$byProduct->execute([$from,$to]);
$byStaff = $pdo->prepare("SELECT u.full_name, COUNT(p.payment_id) cnt, SUM(p.amount_paid) amt
  FROM payments p JOIN users u ON u.user_id=p.recorded_by_user_id
  WHERE DATE(p.payment_date) BETWEEN ? AND ? GROUP BY u.user_id");
$byStaff->execute([$from,$to]);
$unpaidTotal = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='unpaid' AND delivery_status<>'cancelled'")->fetchColumn();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Reports</h2>
<form class="row g-2 mb-3">
  <div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="<?= e($from) ?>" class="form-control"></div>
  <div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="<?= e($to) ?>" class="form-control"></div>
  <div class="col-md-2 align-self-end"><button class="btn btn-aqua w-100">Filter</button></div>
</form>
<div class="cards-grid mb-3">
  <div class="stat-card"><i class="eva eva-trending-up-outline"></i><div><span>Total Sales (<?= e($from) ?> → <?= e($to) ?>)</span><strong><?= money($totalSales) ?></strong></div></div>
  <div class="stat-card"><i class="eva eva-credit-card-outline"></i><div><span>Unpaid Balances</span><strong><?= money($unpaidTotal) ?></strong></div></div>
</div>
<div class="row g-4">
  <div class="col-lg-6"><div class="card-soft"><h5>Sales by Product</h5>
    <table class="table table-sm"><thead><tr><th>Product</th><th>Qty</th><th>Amount</th></tr></thead><tbody>
    <?php foreach ($byProduct as $p): ?><tr><td><?= e($p['product_name']) ?></td><td><?= $p['qty'] ?></td><td><?= money($p['amt']) ?></td></tr><?php endforeach; ?>
    </tbody></table>
  </div></div>
  <div class="col-lg-6"><div class="card-soft"><h5>Sales by Staff</h5>
    <table class="table table-sm"><thead><tr><th>Staff</th><th>Transactions</th><th>Amount</th></tr></thead><tbody>
    <?php foreach ($byStaff as $s): ?><tr><td><?= e($s['full_name']) ?></td><td><?= $s['cnt'] ?></td><td><?= money($s['amt']) ?></td></tr><?php endforeach; ?>
    </tbody></table>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>