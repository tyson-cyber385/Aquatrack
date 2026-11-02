<?php
require_once __DIR__ . '/functions.php';
$u = current_user();
$role = $u['role'] ?? 'guest';
$roleLabels = ['admin' => 'System Admin', 'staff' => 'System Staff', 'customer' => 'Customer'];
$roleLabel = $roleLabels[$role] ?? 'Guest';
$unreadN = is_logged_in() ? unread_notification_count($pdo ?? null ?: $GLOBALS['pdo'], $u['user_id']) : 0;
$unreadM = is_logged_in() ? unread_message_count($pdo ?? null ?: $GLOBALS['pdo'], $u['user_id']) : 0;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= APP_NAME ?> — <?= ucfirst($role) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/eva-icons@1.1.3/style/eva-icons.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <button class="btn btn-icon d-lg-none" id="sidebarToggle"><i class="eva eva-menu-outline"></i></button>
      <div class="topbar-title">
        <i class="eva eva-droplet-outline"></i> <?= APP_NAME ?>
      </div>
      <div class="topbar-actions">
        <a href="<?= BASE_URL ?><?= $role ?>/messages.php" class="btn btn-icon position-relative" title="Messages">
          <i class="eva eva-message-circle-outline"></i>
          <?php if ($unreadM): ?><span class="notif-badge"><?= $unreadM ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?><?= $role ?>/notifications.php" class="btn btn-icon position-relative" title="Notifications">
          <i class="eva eva-bell-outline"></i>
          <?php if ($unreadN): ?><span class="notif-badge"><?= $unreadN ?></span><?php endif; ?>
        </a>
        <button class="btn btn-icon" data-bs-toggle="modal" data-bs-target="#helpModal" title="About this system">
          <i class="eva eva-question-mark-circle-outline"></i>
        </button>
        <span class="user-pill"><i class="eva eva-person-outline"></i> <?= e($roleLabel) ?></span>
      </div>
    </header>
    <main class="content">
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert alert-<?= $f['type'] === 'error' ? 'danger' : e($f['type']) ?>"><?= e($f['msg']) ?></div>
      <?php endforeach; ?>