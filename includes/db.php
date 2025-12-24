<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../config/config.php';
function db_connect() {
global $db_host, $db_user, $db_pass, $db_name;
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
return $conn;
}
function db_query($query) {
$conn = db_connect();
$result = $conn->query($query);
$conn->close();
return $result;
}
function db_fetch_all($query) {
$conn = db_connect();
$result = $conn->query($query);
$data = [];
if ($result && $result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
$data[] = $row;
}
}
$conn->close();
return $data;
}
function db_fetch_one($query) {
$conn = db_connect();
$result = $conn->query($query);
$data = null;
if ($result && $result->num_rows > 0) {
$data = $result->fetch_assoc();
}
$conn->close();
return $data;
}
function db_insert($query) {
$conn = db_connect();
$result = $conn->query($query);
$insert_id = $conn->insert_id;
$conn->close();
return $insert_id;
}
function db_escape($string) {
$conn = db_connect();
$escaped = $conn->real_escape_string($string);
$conn->close();
return $escaped;
}
?>