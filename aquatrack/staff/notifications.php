<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');

$u = current_user();

$stmt = $pdo->prepare("
    SELECT * FROM notifications
    WHERE user_id = ?
    ORDER BY notification_id DESC
    LIMIT 100
");
$stmt->execute([$u['user_id']]);
$rows = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">Notifications</h2>
  <form method="post" action="<?= BASE_URL ?>actions/notification_action.php">
    <?= csrf_field() ?>
    <button class="btn btn-sm btn-outline-secondary">
      <i class="eva eva-checkmark-circle-outline"></i> Mark all as read
    </button>
  </form>
</div>

<div class="card-soft">
  <?php if (!$rows): ?>
    <p class="text-muted mb-0">No notifications.</p>
  <?php endif; ?>

  <?php foreach ($rows as $n): ?>
    <div class="list-item <?= $n['is_read'] ? '' : 'fw-semibold' ?>">
      <strong><?= e($n['title']) ?></strong>
      <small class="text-muted">
        · <?= date('M d, Y H:i', strtotime($n['created_at'])) ?>
      </small><br>
      <span><?= e($n['message']) ?></span>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>