<?php
require_once __DIR__ . '/includes/auth.php';
clear_cookie_auth();
header('Location: /login.php');
exit;
?>