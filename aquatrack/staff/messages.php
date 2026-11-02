<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('staff');

$u = current_user();

// Staff can only message Admins
$admins = $pdo->query("
    SELECT * FROM users
    WHERE role = 'admin' AND account_status = 'active'
    ORDER BY full_name
")->fetchAll();

$otherId = (int)($_GET['with'] ?? ($admins[0]['user_id'] ?? 0));

$msgs = [];
if ($otherId) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.full_name AS sender
        FROM messages m
        JOIN users u ON u.user_id = m.sender_user_id
        WHERE (m.sender_user_id = ? AND m.receiver_user_id = ?)
           OR (m.sender_user_id = ? AND m.receiver_user_id = ?)
        ORDER BY m.message_id ASC
    ");
    $stmt->execute([$u['user_id'], $otherId, $otherId, $u['user_id']]);
    $msgs = $stmt->fetchAll();

    // Mark incoming as read
    $pdo->prepare("
        UPDATE messages SET is_read = 1
        WHERE sender_user_id = ? AND receiver_user_id = ?
    ")->execute([$otherId, $u['user_id']]);
}

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">Messages</h2>

<?php if (!$admins): ?>
  <div class="card-soft">
    <p class="text-muted mb-0">No active admin accounts available.</p>
  </div>
<?php else: ?>
<div class="row g-3">
  <!-- Admin list -->
  <div class="col-md-3">
    <div class="card-soft">
      <h6 class="text-muted mb-2">Admins</h6>
      <?php foreach ($admins as $a): ?>
        <a href="?with=<?= (int)$a['user_id'] ?>"
           class="list-item d-block text-decoration-none <?= $a['user_id'] == $otherId ? 'bg-light' : '' ?>">
          <i class="eva eva-shield-outline"></i>
          <?= e($a['full_name']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Conversation -->
  <div class="col-md-9">
    <div class="card-soft chat-box">
      <?php if (!$msgs): ?>
        <p class="text-muted small">No messages yet. Say hello to your admin.</p>
      <?php endif; ?>

      <?php foreach ($msgs as $m): ?>
        <div class="chat-msg <?= $m['sender_user_id'] == $u['user_id'] ? 'me' : 'them' ?>">
          <small class="text-muted">
            <?= e($m['sender']) ?> · <?= date('M d H:i', strtotime($m['created_at'])) ?>
          </small>
          <div><?= nl2br(e($m['message'])) ?></div>
        </div>
      <?php endforeach; ?>

      <form method="post" action="<?= BASE_URL ?>actions/message_action.php"
            class="d-flex gap-2 mt-3">
        <?= csrf_field() ?>
        <input type="hidden" name="receiver_user_id" value="<?= $otherId ?>">
        <input name="message" class="form-control"
               placeholder="Type a message..." required autocomplete="off">
        <button class="btn btn-aqua" title="Send">
          <i class="eva eva-paper-plane-outline"></i>
        </button>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>