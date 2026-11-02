<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$u = current_user();
$cid = $u['customer_id'];

// Fetch current customer record
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$cid]);
$customer = $stmt->fetch();

include __DIR__ . '/../includes/header.php';
?>

<h2 class="page-title">My Profile</h2>

<div class="row g-4">
  <div class="col-md-8 col-lg-6">
    <div class="card-soft">
      <h5><i class="eva eva-person-outline"></i> Profile Information</h5>
      
      <form method="post" action="<?= BASE_URL ?>actions/profile_action.php">
        <?= csrf_field() ?>
        
        <label class="form-label mt-2">Full Name</label>
        <input name="full_name" value="<?= e($customer['name']) ?>" class="form-control mb-2" required>
        
        <label class="form-label">Contact Number</label>
        <input name="contact_no" value="<?= e($customer['contact_no']) ?>" class="form-control mb-2" required>
        
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control mb-3" rows="2" required><?= e($customer['address']) ?></textarea>
        
        <label class="form-label">New Password (leave blank to keep current)</label>
        <input name="password" type="password" class="form-control mb-3" autocomplete="new-password">
        
        <button class="btn btn-aqua w-100">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>