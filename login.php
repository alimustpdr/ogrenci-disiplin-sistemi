<?php
require_once __DIR__ . '/config/config.php';
if (!$kurulum_tamamlandi) {
header('Location: /install/index.php');
exit;
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
$user = get_cookie_auth();
if ($user) {
header('Location: /dashboard.php');
exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = $_POST['username'];
$password = $_POST['password'];
$conn = db_connect();
$username = $conn->real_escape_string($username);
$result = $conn->query("SELECT * FROM users WHERE username='$username'");
if ($result && $result->num_rows > 0) {
$user = $result->fetch_assoc();
if (password_verify($password, $user['password'])) {
set_cookie_auth($user['id'], $user['username'], $user['role']);
$conn->close();
header('Location: /dashboard.php');
exit;
} else {
$error = 'Kullanıcı adı veya şifre hatalı!';
}
} else {
$error = 'Kullanıcı adı veya şifre hatalı!';
}
$conn->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Giriş - <?php echo $site_baslik; ?></title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
.login-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 400px; width: 90%; }
.logo { text-align: center; margin-bottom: 30px; }
.logo h1 { color: #667eea; font-size: 28px; margin-bottom: 5px; }
.logo p { color: #999; font-size: 14px; }
.form-group { margin-bottom: 20px; }
label { display: block; margin-bottom: 5px; color: #333; font-weight: 600; }
input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 14px; }
input[type="text"]:focus, input[type="password"]:focus { outline: none; border-color: #667eea; }
.btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; margin-top: 10px; }
.btn:hover { opacity: 0.9; }
.alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
</head>
<body>
<div class="login-container">
<div class="logo">
<h1>🎓 ODTS</h1>
<p>Öğrenci Disiplin Takip Sistemi</p>
</div>
<?php if ($error): ?>
<div class="alert"><?php echo $error; ?></div>
<?php endif; ?>
<form method="POST" action="">
<div class="form-group">
<label>Kullanıcı Adı:</label>
<input type="text" name="username" required autofocus>
</div>
<div class="form-group">
<label>Şifre:</label>
<input type="password" name="password" required>
</div>
<button type="submit" class="btn">Giriş Yap</button>
</form>
</div>
</body>
</html>