<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
$u = current_user();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="page-title">Settings</h2>
<div class="row g-4">
  <div class="col-md-6"><div class="card-soft">
    <h5><i class="eva eva-person-outline"></i> Profile</h5>
    <form method="post" action="<?= BASE_URL ?>actions/profile_action.php">
      <?= csrf_field() ?>
      <label class="form-label">Full name</label>
      <input name="full_name" value="<?= e($u['full_name']) ?>" class="form-control mb-2" required>
      <label class="form-label">New password (leave blank to keep)</label>
      <input name="password" class="form-control mb-2" type="password">
      <button class="btn btn-aqua">Update Profile</button>
    </form>
  </div></div>
  <div class="col-md-6"><div class="card-soft">
    <h5><i class="eva eva-info-outline"></i> System Information</h5>
    <p class="mb-1"><strong><?= APP_NAME ?></strong> — <?= SYSTEM_SUBTITLE ?></p>
    <p class="mb-1 small">CC105 – Information Management · Final Requirement</p>
    <p class="mb-1 small">Instructor: Sir Samuel S. Mindaro, Jr.</p>
    <p class="mb-1 small">Front-end: Mark Joaqui G. Pedro</p>
    <p class="mb-0 small">Back-end: Kian Lorenz D. Galario</p>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>