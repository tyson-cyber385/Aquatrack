<?php 
require_once __DIR__ . '/config/session.php'; 
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php'; // <-- ADD THIS LINE

if (!empty($_SESSION['user'])) { redirect($_SESSION['user']['role'] . '/dashboard.php'); }
$err = $_SESSION['login_err'] ?? null; unset($_SESSION['login_err']);
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/eva-icons@1.1.3/style/eva-icons.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/login.css" rel="stylesheet">
</head><body class="login-body">
<div class="login-card">
  <div class="login-head">
    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="AquaTrack" class="login-logo">
    <h1 class="login-title">AquaTrack</h1>
    <p class="login-sub"><?= SYSTEM_SUBTITLE ?></p>
  </div>

  <div class="role-tabs" id="roleTabs">
    <button type="button" data-role="admin"    class="role-tab"><i class="eva eva-shield-outline"></i> Admin</button>
    <button type="button" data-role="staff"    class="role-tab active"><i class="eva eva-person-outline"></i> Staff</button>
    <button type="button" data-role="customer" class="role-tab"><i class="eva eva-people-outline"></i> Customer</button>
  </div>

  <?php if ($err): ?><div class="alert alert-danger py-2 small"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form method="post" action="<?= BASE_URL ?>actions/login_action.php" autocomplete="off">
    <input type="hidden" name="role" id="roleInput" value="staff">
    <label class="form-label">Username or Email</label>
    <div class="input-wrap"><i class="eva eva-person-outline"></i>
      <input type="text" name="username" class="form-control" placeholder="Enter your username" required autofocus>
    </div>

    <label class="form-label mt-3">Password</label>
    <div class="input-wrap">
      <i class="eva eva-lock-outline"></i>
      <input type="password" name="password" id="passwordField" class="form-control" placeholder="Enter your password" required>
      <button type="button" class="toggle-pass" id="togglePass" aria-label="Show password"><i class="eva eva-eye-outline"></i></button>
    </div>

    <div class="row mt-3 align-items-center">
      <div class="col-6"><label class="small"><input type="checkbox" name="remember"> Remember me</label></div>
      <div class="col-6 text-end"><a href="#" class="small link-muted" onclick="return false;">Forgot password?</a></div>
    </div>

    <button type="submit" class="btn-aqua w-100 mt-3"><i class="eva eva-log-in-outline"></i> Sign In</button>
  </form>

  <div class="login-foot">
    <a href="<?= BASE_URL ?>index.php" class="small link-muted"><i class="eva eva-arrow-back-outline"></i> Back to home</a>
  </div>
</div>
<script>
const tabs = document.querySelectorAll('.role-tab');
tabs.forEach(t => t.addEventListener('click', () => {
  tabs.forEach(x => x.classList.remove('active'));
  t.classList.add('active');
  document.getElementById('roleInput').value = t.dataset.role;
}));
document.getElementById('togglePass').addEventListener('click', function() {
  const f = document.getElementById('passwordField');
  const i = this.querySelector('i');
  if (f.type === 'password') { f.type = 'text'; i.className = 'eva eva-eye-off-outline'; }
  else { f.type = 'password'; i.className = 'eva eva-eye-outline'; }
});
</script>
</body></html>