<?php
if (!isset($user)) {
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$user = check_auth();
}
$page_title = isset($page_title) ? $page_title : 'ODTS';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title; ?> - ODTS</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
.wrapper { display: flex; min-height: 100vh; }
.sidebar { width: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 0; position: fixed; height: 100vh; overflow-y: auto; }
.sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.2); }
.sidebar-header h2 { font-size: 24px; margin-bottom: 5px; }
.sidebar-header p { font-size: 12px; opacity: 0.8; }
.sidebar-menu { padding: 20px 0; }
.sidebar-menu a { display: block; padding: 12px 20px; color: white; text-decoration: none; transition: all 0.3s; }
.sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); }
.main-content { margin-left: 250px; flex: 1; padding: 30px; }
.header { background: white; padding: 20px 30px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.header h1 { font-size: 28px; color: #333; }
.user-info { display: flex; align-items: center; gap: 15px; }
.user-info span { color: #666; }
.user-info a { color: #667eea; text-decoration: none; }
.card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
.card-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
.card-header h2 { color: #333; font-size: 20px; }
.btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; font-size: 14px; transition: all 0.3s; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-primary:hover { opacity: 0.9; }
.btn-success { background: #28a745; color: white; }
.btn-success:hover { background: #218838; }
.btn-danger { background: #dc3545; color: white; }
.btn-danger:hover { background: #c82333; }
.btn-sm { padding: 6px 12px; font-size: 12px; }
table { width: 100%; border-collapse: collapse; }
table th { text-align: left; padding: 12px; background: #f8f9fa; color: #666; font-weight: 600; font-size: 13px; text-transform: uppercase; }
table td { padding: 12px; border-bottom: 1px solid #f0f0f0; color: #333; }
table tr:last-child td { border-bottom: none; }
.badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.badge-primary { background: #cfe2ff; color: #084298; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-orange { background: #ffe5d0; color: #cc5200; }
.badge-danger { background: #f8d7da; color: #721c24; }
.alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: 600; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 14px; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #667eea; }
.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
.modal.active { display: flex; justify-content: center; align-items: center; }
.modal-content { background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
.modal-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
.modal-header h3 { color: #333; font-size: 20px; }
.close { float: right; font-size: 28px; font-weight: bold; color: #999; cursor: pointer; }
.close:hover { color: #333; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 20px; }
.pagination a { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; color: #667eea; text-decoration: none; }
.pagination a:hover { background: #667eea; color: white; }
.pagination a.active { background: #667eea; color: white; }
@media (max-width: 768px) {
.sidebar { width: 100%; height: auto; position: relative; }
.main-content { margin-left: 0; }
.form-row { grid-template-columns: 1fr; }
.header { flex-direction: column; gap: 15px; text-align: center; }
}
</style>
</head>
<body>
<div class="wrapper">
<div class="sidebar">
<div class="sidebar-header">
<h2>🎓 ODTS</h2>
<p>Disiplin Takip Sistemi</p>
</div>
<div class="sidebar-menu">
<a href="/dashboard.php">📊 Ana Panel</a>
<a href="/students.php">👥 Öğrenciler</a>
<a href="/warnings.php">⚠️ Uyarılar</a>
<a href="/classes.php">🏫 Sınıflar</a>
<?php if ($user['role'] == 'admin'): ?>
<a href="/users.php">👤 Kullanıcılar</a>
<?php endif; ?>
<a href="/reports.php">📈 Raporlar</a>
<?php if ($user['role'] == 'admin'): ?>
<a href="/settings.php">⚙️ Ayarlar</a>
<?php endif; ?>
</div>
</div>
<div class="main-content">
<div class="header">
<h1><?php echo $page_title; ?></h1>
<div class="user-info">
<span>Hoş geldiniz, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></span>
<a href="/logout.php">Çıkış</a>
</div>
</div>