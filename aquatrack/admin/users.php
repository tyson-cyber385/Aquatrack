<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$users = $pdo->query("SELECT * FROM users ORDER BY role, full_name")->fetchAll();
$customers = $pdo->query("SELECT * FROM customers ORDER BY name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">User Accounts</h2>
  <button class="btn btn-aqua" data-bs-toggle="modal" data-bs-target="#userModal"><i class="eva eva-plus-outline"></i> Add User</button>
</div>
<div class="card-soft"><table class="table table-aqua align-middle">
  <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= e($u['full_name']) ?></td><td><?= e($u['username']) ?></td>
      <td><span class="badge bg-secondary"><?= $u['role'] ?></span></td>
      <td><?= $u['account_status'] ?></td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#userModal"
          data-id="<?= $u['user_id'] ?>" data-name="<?= e($u['full_name']) ?>" data-uname="<?= e($u['username']) ?>"
          data-role="<?= $u['role'] ?>" data-cid="<?= $u['customer_id'] ?>" data-status="<?= $u['account_status'] ?>">
          <i class="eva eva-edit-outline"></i></button>
        <?php if ($u['role'] !== 'admin'): ?>
          <form class="d-inline" method="post" action="<?= BASE_URL ?>actions/user_action.php"
            onsubmit="return confirm('Delete this user?')">
            <?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="eva eva-trash-2-outline"></i></button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody></table></div>

<div class="modal fade" id="userModal" tabindex="-1"><div class="modal-dialog"><form method="post" action="<?= BASE_URL ?>actions/user_action.php" class="modal-content">
  <?= csrf_field() ?>
  <div class="modal-header"><h5 class="modal-title">User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="user_id" id="u_id">
    <label class="form-label">Full Name</label><input name="full_name" id="u_name" class="form-control mb-2" required>
    <label class="form-label">Username</label><input name="username" id="u_uname" class="form-control mb-2" required>
    <label class="form-label">Password (leave blank to keep)</label><input name="password" class="form-control mb-2">
    <label class="form-label">Role</label>
    <select name="role" id="u_role" class="form-select mb-2">
      <option value="admin">Admin</option><option value="staff">Staff</option><option value="customer">Customer</option>
    </select>
    <label class="form-label">Link to Customer record (for customer role)</label>
    <select name="customer_id" id="u_cid" class="form-select mb-2">
      <option value="">— None —</option>
      <?php foreach ($customers as $c): ?><option value="<?= $c['customer_id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
    </select>
    <label class="form-label">Status</label>
    <select name="account_status" id="u_status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
  </div>
  <div class="modal-footer"><button class="btn btn-aqua">Save</button></div>
</form></div></div>

<script>
document.getElementById('userModal').addEventListener('show.bs.modal', e => {
  const b = e.relatedTarget; const m = document.getElementById('userModal');
  m.querySelector('#u_id').value = b.dataset.id || '';
  m.querySelector('#u_name').value = b.dataset.name || '';
  m.querySelector('#u_uname').value = b.dataset.uname || '';
  m.querySelector('#u_role').value = b.dataset.role || 'staff';
  m.querySelector('#u_cid').value = b.dataset.cid || '';
  m.querySelector('#u_status').value = b.dataset.status || 'active';
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>