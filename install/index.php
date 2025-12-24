<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ODTS Kurulum Sihirbazı</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
.container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 600px; width: 90%; }
h1 { color: #667eea; margin-bottom: 30px; text-align: center; }
.form-group { margin-bottom: 20px; }
label { display: block; margin-bottom: 5px; color: #333; font-weight: 600; }
input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 14px; }
input[type="text"]:focus, input[type="password"]:focus { outline: none; border-color: #667eea; }
.btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; margin-top: 20px; }
.btn:hover { opacity: 0.9; }
.alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.step { display: none; }
.step.active { display: block; }
.progress { height: 10px; background: #e0e0e0; border-radius: 5px; margin-bottom: 30px; overflow: hidden; }
.progress-bar { height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transition: width 0.3s; }
</style>
</head>
<body>
<div class="container">
<h1>🎓 ODTS Kurulum Sihirbazı</h1>
<div class="progress">
<div class="progress-bar" id="progressBar" style="width: 0%"></div>
</div>
<?php
$config_file = '../config/config.php';
require_once $config_file;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['step']) && $_POST['step'] == '1') {
$db_host = $_POST['db_host'];
$db_user = $_POST['db_user'];
$db_pass = $_POST['db_pass'];
$db_name = $_POST['db_name'];
$site_url = $_POST['site_url'];
$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
echo '<div class="alert alert-danger">Veritabanı bağlantı hatası: ' . $conn->connect_error . '</div>';
} else {
$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db_name);
$conn->set_charset("utf8mb4");
$tables_sql = "
CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
full_name VARCHAR(100) NOT NULL,
email VARCHAR(100),
role VARCHAR(20) NOT NULL,
auth_token VARCHAR(255),
token_expire INT DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS roles (
id INT AUTO_INCREMENT PRIMARY KEY,
role_name VARCHAR(50) UNIQUE NOT NULL,
can_manage_users TINYINT(1) DEFAULT 0,
can_manage_students TINYINT(1) DEFAULT 1,
can_manage_warnings TINYINT(1) DEFAULT 1,
can_manage_classes TINYINT(1) DEFAULT 0,
can_view_reports TINYINT(1) DEFAULT 1,
can_manage_settings TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS students (
id INT AUTO_INCREMENT PRIMARY KEY,
student_no VARCHAR(20) UNIQUE NOT NULL,
first_name VARCHAR(50) NOT NULL,
last_name VARCHAR(50) NOT NULL,
class_id INT,
birth_date DATE,
parent_name VARCHAR(100),
parent_phone VARCHAR(20),
address TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS classes (
id INT AUTO_INCREMENT PRIMARY KEY,
class_name VARCHAR(20) UNIQUE NOT NULL,
grade INT NOT NULL,
section VARCHAR(5) NOT NULL,
teacher_id INT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS warnings (
id INT AUTO_INCREMENT PRIMARY KEY,
student_id INT NOT NULL,
category_id INT NOT NULL,
level INT NOT NULL,
description TEXT NOT NULL,
action_taken TEXT,
user_id INT NOT NULL,
warning_date DATE NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
FOREIGN KEY (category_id) REFERENCES warning_categories(id),
FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS warning_categories (
id INT AUTO_INCREMENT PRIMARY KEY,
category_name VARCHAR(50) UNIQUE NOT NULL,
description TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
id INT AUTO_INCREMENT PRIMARY KEY,
setting_key VARCHAR(100) UNIQUE NOT NULL,
value TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activity_logs (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
action VARCHAR(100) NOT NULL,
description TEXT,
ip_address VARCHAR(45),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
$sql_statements = explode(';', $tables_sql);
foreach ($sql_statements as $sql) {
$sql = trim($sql);
if (!empty($sql)) {
$conn->query($sql);
}
}
$conn->query("INSERT IGNORE INTO roles (role_name, can_manage_users, can_manage_students, can_manage_warnings, can_manage_classes, can_view_reports, can_manage_settings) VALUES 
('admin', 1, 1, 1, 1, 1, 1),
('mudur_yardimcisi', 0, 1, 1, 1, 1, 0),
('ogretmen', 0, 1, 1, 0, 1, 0)");
$conn->query("INSERT IGNORE INTO warning_categories (category_name, description) VALUES 
('Davranış', 'Davranış ile ilgili uyarılar'),
('Devamsızlık', 'Devamsızlık ile ilgili uyarılar'),
('Kıyafet', 'Kıyafet ile ilgili uyarılar'),
('Ders Düzeni', 'Ders düzeni ile ilgili uyarılar'),
('Diğer', 'Diğer uyarılar')");
$conn->close();
$config_content = file_get_contents($config_file);
$config_content = preg_replace("/\$db_host = '[^']*';/", "\$db_host = '$db_host';", $config_content);
$config_content = preg_replace("/\$db_user = '[^']*';/", "\$db_user = '$db_user';", $config_content);
$config_content = preg_replace("/\$db_pass = '[^']*';/", "\$db_pass = '$db_pass';", $config_content);
$config_content = preg_replace("/\$db_name = '[^']*';/", "\$db_name = '$db_name';", $config_content);
$config_content = preg_replace("/\$site_url = '[^']*';/", "\$site_url = '$site_url';", $config_content);
$config_content = preg_replace("/\$secret_key = '[^']*';/", "\$secret_key = '" . bin2hex(random_bytes(32)) . "';", $config_content);
file_put_contents($config_file, $config_content);
echo '<script>document.getElementById("progressBar").style.width = "50%";</script>';
echo '<div class="alert alert-success">Veritabanı başarıyla oluşturuldu! Şimdi admin hesabı oluşturun.</div>';
echo '<div class="step active">';
echo '<form method="POST" action="">';
echo '<input type="hidden" name="step" value="2">';
echo '<div class="form-group"><label>Kullanıcı Adı:</label><input type="text" name="admin_username" required></div>';
echo '<div class="form-group"><label>Şifre:</label><input type="password" name="admin_password" required></div>';
echo '<div class="form-group"><label>Ad Soyad:</label><input type="text" name="admin_fullname" required></div>';
echo '<div class="form-group"><label>E-posta:</label><input type="text" name="admin_email" required></div>';
echo '<button type="submit" class="btn">Admin Hesabı Oluştur</button>';
echo '</form>';
echo '</div>';
}
exit;
} elseif (isset($_POST['step']) && $_POST['step'] == '2') {
require_once '../includes/db.php';
$username = $_POST['admin_username'];
$password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
$fullname = $_POST['admin_fullname'];
$email = $_POST['admin_email'];
$conn = db_connect();
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);
$fullname = $conn->real_escape_string($fullname);
$email = $conn->real_escape_string($email);
$conn->query("INSERT INTO users (username, password, full_name, email, role) VALUES ('$username', '$password', '$fullname', '$email', 'admin')");
$conn->close();
$config_content = file_get_contents($config_file);
$config_content = preg_replace("/\$kurulum_tamamlandi = false;/", "\$kurulum_tamamlandi = true;", $config_content);
file_put_contents($config_file, $config_content);
echo '<script>document.getElementById("progressBar").style.width = "100%";</script>';
echo '<div class="alert alert-success">Kurulum başarıyla tamamlandı! Sisteme giriş yapabilirsiniz.</div>';
echo '<a href="/login.php" class="btn">Giriş Yap</a>';
exit;
}
}
if (!$kurulum_tamamlandi) {
?>
<div class="step active">
<div class="alert alert-info">Hoş geldiniz! ODTS kurulumuna başlamak için veritabanı bilgilerinizi girin.</div>
<form method="POST" action="">
<input type="hidden" name="step" value="1">
<div class="form-group">
<label>Veritabanı Sunucusu:</label>
<input type="text" name="db_host" value="localhost" required>
</div>
<div class="form-group">
<label>Veritabanı Kullanıcı Adı:</label>
<input type="text" name="db_user" required>
</div>
<div class="form-group">
<label>Veritabanı Şifresi:</label>
<input type="password" name="db_pass">
</div>
<div class="form-group">
<label>Veritabanı Adı:</label>
<input type="text" name="db_name" value="odts_db" required>
</div>
<div class="form-group">
<label>Site URL:</label>
<input type="text" name="site_url" placeholder="https://gulayazim.gt.tc" required>
</div>
<button type="submit" class="btn">Kuruluma Başla</button>
</form>
</div>
<?php
} else {
echo '<div class="alert alert-info">Kurulum zaten tamamlanmış. <a href="/login.php">Giriş yapın</a></div>';
}
?>
</div>
</body>
</html>