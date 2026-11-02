<?php 
require_once __DIR__ . '/config/session.php'; 
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php'; // <-- ADD THIS LINE

if (!empty($_SESSION['user'])) { redirect($_SESSION['user']['role'] . '/dashboard.php'); }
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= APP_NAME ?> — <?= SYSTEM_SUBTITLE ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/eva-icons@1.1.3/style/eva-icons.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/login.css" rel="stylesheet">
</head><body class="front-body">
  <div class="front-hero">
    <div class="front-left">
      <img src="<?= BASE_URL ?>assets/images/logo.png" alt="AquaTrack" class="front-logo">
      <h1 class="front-station">AquaTrack</h1>
      <p class="front-tagline"><?= TAGLINE ?></p>
    </div>
    <div class="front-right">
      <h2 class="front-title"><?= SYSTEM_SUBTITLE ?></h2>
      <p class="front-desc">Manage customers, orders, deliveries, gallon refills, and balances — all in one place. Built for owners, staff, and customers of your water refilling station.</p>
      <a href="<?= BASE_URL ?>login.php" class="btn-aqua"><i class="eva eva-log-in-outline"></i> Login to your account</a>
      <div class="front-foot">CC105 – Information Management · Final Requirement</div>
    </div>
  </div>
</body></html>