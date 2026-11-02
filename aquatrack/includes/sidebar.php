<?php
$u = current_user();
$role = $u['role'] ?? 'guest';
$active = basename($_SERVER['PHP_SELF']);
$nav = [
  'admin' => [
    ['dashboard.php','Dashboard','eva eva-grid-outline'],
    ['customers.php','Customers','eva eva-people-outline'],
    ['products.php','Products','eva eva-shopping-bag-outline'],
    ['orders.php','Orders','eva eva-file-text-outline'],
    ['deliveries.php','Deliveries','eva eva-car-outline'],
    ['balances.php','Balances','eva eva-credit-card-outline'],
    ['reports.php','Reports','eva eva-bar-chart-outline'],
    ['people.php','People','eva eva-activity-outline'],
    ['messages.php','Messages','eva eva-message-circle-outline'],
    ['restock_requests.php','Restock Requests','eva eva-refresh-outline'],
    ['users.php','User Accounts','eva eva-person-add-outline'],
    ['settings.php','Settings','eva eva-settings-2-outline'],
  ],
  'staff' => [
    ['dashboard.php','Dashboard','eva eva-grid-outline'],
    ['customers.php','Customers','eva eva-people-outline'],
    ['products.php','Products','eva eva-shopping-bag-outline'],
    ['orders.php','Orders','eva eva-file-text-outline'],
    ['deliveries.php','Deliveries','eva eva-car-outline'],
    ['balances.php','Balances','eva eva-credit-card-outline'],
    ['reports.php','Reports','eva eva-bar-chart-outline'],
    ['messages.php','Messages','eva eva-message-circle-outline'],
    ['settings.php','Settings','eva eva-settings-2-outline'],
  ],
  'customer' => [
    ['dashboard.php','Dashboard','eva eva-grid-outline'],
    ['new_order.php','New Order','eva eva-plus-circle-outline'],
    ['my_orders.php','My Orders','eva eva-file-text-outline'],
    ['track_delivery.php','Track Delivery','eva eva-car-outline'],
    ['balances.php','Balances','eva eva-credit-card-outline'],
    ['containers.php','My Containers','eva eva-cube-outline'],
    ['notifications.php','Notifications','eva eva-bell-outline'],
    ['profile.php','Profile','eva eva-person-outline'],
  ],
][$role] ?? [];
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="logo" class="brand-logo">
    <div><div class="brand-name">AquaTrack</div><div class="brand-sub">Refilling Station</div></div>
  </div>
  <nav class="sidebar-nav">
    <?php foreach ($nav as $item): ?>
      <a href="<?= BASE_URL . $role . '/' . $item[0] ?>" class="nav-item <?= $active === $item[0] ? 'active' : '' ?>">
        <i class="<?= $item[2] ?>"></i> <span><?= e($item[1]) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= BASE_URL ?>logout.php" class="nav-item logout">
      <i class="eva eva-log-out-outline"></i> <span>Logout</span>
    </a>
  </div>
</aside>