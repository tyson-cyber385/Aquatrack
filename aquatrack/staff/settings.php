<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');

$u = current_user();
include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">Settings</h2>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card-soft">
      <h5><i class="eva eva-person-outline"></i> Profile</h5>
      <form method="post" action="<?= BASE_URL ?>actions/profile_action.php">
        <?= csrf_field() ?>
        <label class="form-label">Full name</label>
        <input name="full_name" value="<?= e($u['full_name']) ?>"
               class="form-control mb-2" required>

        <label class="form-label">Username</label>
        <input value="<?= e($u['username']) ?>" class="form-control mb-2" disabled>

        <label class="form-label">New password (leave blank to keep)</label>
        <input name="password" class="form-control mb-3" type="password" autocomplete="new-password">

        <button class="btn btn-aqua">Update Profile</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>