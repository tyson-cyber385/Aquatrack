<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY notification_id DESC LIMIT 100");
$stmt->execute([$u['user_id']]);
$rows = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="page-title mb-0">Notifications</h2>
  <?php if ($rows): ?>
    <form method="post" action="<?= BASE_URL ?>actions/notification_action.php">
      <?= csrf_field() ?>
      <button class="btn btn-sm btn-outline-secondary">
        <i class="eva eva-checkmark-circle-outline"></i> Mark all as read
      </button>
    </form>
  <?php endif; ?>
</div>

<div class="card-soft">
  <?php if (!$rows): ?>
    <p class="text-muted mb-0 text-center py-4">
      <i class="eva eva-bell-off-outline" style="font-size: 2rem;"></i><br>
      No notifications yet.
    </p>
  <?php endif; ?>

  <?php foreach ($rows as $n): ?>
    <div class="list-item <?= $n['is_read'] ? '' : 'fw-semibold' ?>" style="<?= $n['is_read'] ? 'opacity: 0.7;' : 'background-color: #f8fafc;' ?>">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <span style="color: var(--primary);"><i class="eva <?= $n['is_read'] ? 'eva-email-open-outline' : 'eva-email-unread-outline' ?>"></i></span>
          <strong class="ms-2"><?= e($n['title']) ?></strong>
        </div>
        <small class="text-muted" style="font-size: 0.75rem;"><?= date('M d, Y h:i A', strtotime($n['created_at'])) ?></small>
      </div>
      <div class="mt-1 ms-4" style="color: #475569;"><?= nl2br(e($n['message'])) ?></div>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>