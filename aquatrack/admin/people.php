<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$staff = $pdo->query("SELECT * FROM users WHERE role IN ('staff','admin') ORDER BY last_login DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">People — Staff on Shift</h2>
<div class="card-soft"><table class="table table-aqua align-middle">
  <thead><tr><th>Name</th><th>Role</th><th>Username</th><th>Last Login</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($staff as $s): ?>
    <tr>
      <td><?= e($s['full_name']) ?></td>
      <td><span class="badge bg-secondary"><?= $s['role'] ?></span></td>
      <td><?= e($s['username']) ?></td>
      <td><?= $s['last_login'] ? date('M d Y H:i', strtotime($s['last_login'])) : '—' ?></td>
      <td>
        <?php $on = $s['last_login'] && strtotime($s['last_login']) > time() - 8*3600; ?>
        <span class="badge <?= $on ? 'bg-success' : 'bg-secondary' ?>"><?= $on ? 'On shift' : 'Off' ?></span>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>