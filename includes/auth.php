<?php
require_once __DIR__ . '/db.php';
function set_cookie_auth($user_id, $username, $role) {
global $secret_key, $cookie_lifetime, $cookie_domain, $cookie_path;
$token = hash('sha256', $user_id . $username . $role . $secret_key . time());
$cookie_value = base64_encode(json_encode([
'user_id' => $user_id,
'username' => $username,
'role' => $role,
'token' => $token,
'expire' => time() + $cookie_lifetime
]));
setcookie('odts_auth', $cookie_value, time() + $cookie_lifetime, $cookie_path, $cookie_domain, false, true);
$conn = db_connect();
$token_escaped = $conn->real_escape_string($token);
$user_id_escaped = intval($user_id);
$expire_time = time() + $cookie_lifetime;
$conn->query("UPDATE users SET auth_token='$token_escaped', token_expire=$expire_time WHERE id=$user_id_escaped");
$conn->close();
return true;
}
function get_cookie_auth() {
if (!isset($_COOKIE['odts_auth'])) {
return null;
}
$cookie_data = json_decode(base64_decode($_COOKIE['odts_auth']), true);
if (!$cookie_data || !isset($cookie_data['user_id']) || !isset($cookie_data['token'])) {
return null;
}
if (isset($cookie_data['expire']) && $cookie_data['expire'] < time()) {
clear_cookie_auth();
return null;
}
$conn = db_connect();
$user_id = intval($cookie_data['user_id']);
$token = $conn->real_escape_string($cookie_data['token']);
$result = $conn->query("SELECT * FROM users WHERE id=$user_id AND auth_token='$token' AND token_expire > " . time());
$user = null;
if ($result && $result->num_rows > 0) {
$user = $result->fetch_assoc();
}
$conn->close();
return $user;
}
function clear_cookie_auth() {
global $cookie_domain, $cookie_path;
setcookie('odts_auth', '', time() - 3600, $cookie_path, $cookie_domain);
if (isset($_COOKIE['odts_auth'])) {
$cookie_data = json_decode(base64_decode($_COOKIE['odts_auth']), true);
if ($cookie_data && isset($cookie_data['user_id'])) {
$conn = db_connect();
$user_id = intval($cookie_data['user_id']);
$conn->query("UPDATE users SET auth_token=NULL, token_expire=0 WHERE id=$user_id");
$conn->close();
}
}
}
function check_auth($required_role = null) {
$user = get_cookie_auth();
if (!$user) {
header('Location: /login.php');
exit;
}
if ($required_role && $user['role'] != $required_role && $user['role'] != 'admin') {
header('Location: /dashboard.php');
exit;
}
return $user;
}
function has_permission($user, $permission) {
if ($user['role'] == 'admin') {
return true;
}
$conn = db_connect();
$role = $conn->real_escape_string($user['role']);
$perm = $conn->real_escape_string($permission);
$result = $conn->query("SELECT * FROM roles WHERE role_name='$role' AND $perm=1");
$has_perm = $result && $result->num_rows > 0;
$conn->close();
return $has_perm;
}
?>