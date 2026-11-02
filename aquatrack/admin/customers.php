<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$q = trim($_GET['q'] ?? '');
$rows = $q
  ? $pdo->prepare("SELECT * FROM customers WHERE name LIKE ? OR contact_no LIKE ? OR address LIKE ? ORDER BY customer_id DESC")
  : null;
if ($rows) { $like = "%$q%"; $rows->execute([$like,$like,$like]); }
else { $rows = $pdo->query("SELECT * FROM customers ORDER BY customer_id DESC"); }
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">Customers</h2>
  <button class="btn btn-aqua" data-bs-toggle="modal" data-bs-target="#custModal"><i class="eva eva-plus-outline"></i> Add Customer</button>
</div>
<form class="row g-2 mb-3" method="get">
  <div class="col-md-6"><input name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search by name, contact, or address"></div>
  <div class="col-md-2"><button class="btn btn-outline-secondary w-100"><i class="eva eva-search-outline"></i> Search</button></div>
</form>

<div class="card-soft"><div class="table-responsive"><table class="table table-aqua align-middle">
  <thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Address</th><th>Containers</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $c): ?>
    <tr>
      <td>#<?= $c['customer_id'] ?></td>
      <td><?= e($c['name']) ?></td>
      <td><?= e($c['contact_no']) ?></td>
      <td><?= e($c['address']) ?></td>
      <td><?= $c['containers_borrowed'] ?></td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#custModal"
          data-id="<?= $c['customer_id'] ?>" data-name="<?= e($c['name']) ?>"
          data-contact="<?= e($c['contact_no']) ?>" data-address="<?= e($c['address']) ?>"
          data-containers="<?= $c['containers_borrowed'] ?>"><i class="eva eva-edit-outline"></i></button>
        <form class="d-inline" method="post" action="<?= BASE_URL ?>actions/customer_action.php"
              onsubmit="return confirm('Delete this customer?')">
          <?= csrf_field() ?><input type="hidden" name="op" value="delete">
          <input type="hidden" name="customer_id" value="<?= $c['customer_id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="eva eva-trash-2-outline"></i></button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>

<div class="modal fade" id="custModal" tabindex="-1"><div class="modal-dialog"><form method="post" action="<?= BASE_URL ?>actions/customer_action.php" class="modal-content">
  <?= csrf_field() ?>
  <div class="modal-header"><h5 class="modal-title">Customer</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="customer_id" id="c_id">
    <label class="form-label">Name</label><input name="name" id="c_name" class="form-control mb-2" required>
    <label class="form-label">Contact</label><input name="contact_no" id="c_contact" class="form-control mb-2">
    <label class="form-label">Address</label><input name="address" id="c_address" class="form-control mb-2">
    <label class="form-label">Containers Borrowed</label><input type="number" min="0" name="containers_borrowed" id="c_containers" class="form-control" value="0">
  </div>
  <div class="modal-footer"><button class="btn btn-aqua">Save</button></div>
</form></div></div>

<script>
document.getElementById('custModal').addEventListener('show.bs.modal', e => {
  const b = e.relatedTarget; const m = document.getElementById('custModal');
  m.querySelector('#c_id').value = b.dataset.id || '';
  m.querySelector('#c_name').value = b.dataset.name || '';
  m.querySelector('#c_contact').value = b.dataset.contact || '';
  m.querySelector('#c_address').value = b.dataset.address || '';
  m.querySelector('#c_containers').value = b.dataset.containers || 0;
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>