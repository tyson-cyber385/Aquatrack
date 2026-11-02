<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();
$cid = $u['customer_id'];

$stmt = $pdo->prepare("SELECT containers_borrowed FROM customers WHERE customer_id = ?");
$stmt->execute([$cid]);
$containers = (int)$stmt->fetchColumn();

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">My Containers</h2>

<div class="card-soft text-center py-5" style="max-width: 500px; margin: 0 auto;">
  <i class="eva eva-cube-outline" style="font-size: 4rem; color: var(--primary); opacity: 0.5;"></i>
  <h4 class="mt-3">Containers Borrowed</h4>
  <div style="font-size: 4rem; font-weight: 700; color: var(--primary); line-height: 1;">
    <?= $containers ?>
  </div>
  <p class="text-muted mt-3 mb-0">
    This is the number of AquaTrack gallons you currently have at home. <br>
    Please return them upon your next delivery or visit to the station.
  </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>