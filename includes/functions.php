<?php
require_once __DIR__ . '/db.php';
function sanitize_input($data) {
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
return $data;
}
function log_activity($user_id, $action, $description) {
$conn = db_connect();
$user_id = intval($user_id);
$action = $conn->real_escape_string($action);
$description = $conn->real_escape_string($description);
$ip_address = $_SERVER['REMOTE_ADDR'];
$conn->query("INSERT INTO activity_logs (user_id, action, description, ip_address, created_at) VALUES ($user_id, '$action', '$description', '$ip_address', NOW())");
$conn->close();
}
function get_setting($key, $default = '') {
$conn = db_connect();
$key = $conn->real_escape_string($key);
$result = $conn->query("SELECT value FROM settings WHERE setting_key='$key'");
$value = $default;
if ($result && $result->num_rows > 0) {
$row = $result->fetch_assoc();
$value = $row['value'];
}
$conn->close();
return $value;
}
function set_setting($key, $value) {
$conn = db_connect();
$key = $conn->real_escape_string($key);
$value = $conn->real_escape_string($value);
$result = $conn->query("SELECT id FROM settings WHERE setting_key='$key'");
if ($result && $result->num_rows > 0) {
$conn->query("UPDATE settings SET value='$value' WHERE setting_key='$key'");
} else {
$conn->query("INSERT INTO settings (setting_key, value) VALUES ('$key', '$value')");
}
$conn->close();
}
function format_date($date, $format = 'd.m.Y H:i') {
return date($format, strtotime($date));
}
function get_warning_level_badge($level) {
$badges = [
1 => '<span class="badge badge-info">Seviye 1</span>',
2 => '<span class="badge badge-primary">Seviye 2</span>',
3 => '<span class="badge badge-warning">Seviye 3</span>',
4 => '<span class="badge badge-orange">Seviye 4</span>',
5 => '<span class="badge badge-danger">Seviye 5</span>'
];
return isset($badges[$level]) ? $badges[$level] : '';
}
function redirect($url) {
header("Location: $url");
exit;
}
function show_message($type, $message) {
return "<div class='alert alert-$type'>$message</div>";
}
function generate_csrf_token() {
global $secret_key;
$token = hash('sha256', $secret_key . time() . rand());
setcookie('csrf_token', $token, time() + 3600, '/');
return $token;
}
function verify_csrf_token($token) {
return isset($_COOKIE['csrf_token']) && $_COOKIE['csrf_token'] === $token;
}
function paginate($total, $per_page, $current_page) {
$total_pages = ceil($total / $per_page);
$prev = $current_page > 1 ? $current_page - 1 : 1;
$next = $current_page < $total_pages ? $current_page + 1 : $total_pages;
return [
'total' => $total,
'per_page' => $per_page,
'current_page' => $current_page,
'total_pages' => $total_pages,
'prev' => $prev,
'next' => $next
];
}
function export_to_excel($data, $filename, $headers) {
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');
echo "\xEF\xBB\xBF";
echo '<table border="1">';
echo '<tr>';
foreach ($headers as $header) {
echo '<th>' . $header . '</th>';
}
echo '</tr>';
foreach ($data as $row) {
echo '<tr>';
foreach ($row as $cell) {
echo '<td>' . $cell . '</td>';
}
echo '</tr>';
}
echo '</table>';
exit;
}
?>