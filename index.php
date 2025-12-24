<?php
require_once __DIR__ . '/config/config.php';
if (!$kurulum_tamamlandi) {
header('Location: /install/index.php');
exit;
}
require_once __DIR__ . '/includes/auth.php';
$user = get_cookie_auth();
if ($user) {
header('Location: /dashboard.php');
} else {
header('Location: /login.php');
}
exit;
?>