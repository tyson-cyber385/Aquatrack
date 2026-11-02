<?php
define('APP_NAME', 'AquaTrack');
define('TAGLINE',  'A cleaner way to manage your station.');
define('SYSTEM_SUBTITLE', 'A web-based water refilling station management system');

// Auto-detect base URL
$__script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$__base = preg_replace('#/(admin|staff|customer|actions)/.*$#', '/', $__script);
$__base = preg_replace('#/[^/]+\.php$#', '/', $__base);
if ($__base === '' || $__base === null) $__base = '/';
define('BASE_URL', $__base);

define('CURRENCY', '₱');