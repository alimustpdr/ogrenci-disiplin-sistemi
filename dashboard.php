<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$user = check_auth();
$conn = db_connect();
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_warnings = $conn->query("SELECT COUNT(*) as count FROM warnings")->fetch_assoc()['count'];
$total_classes = $conn->query("SELECT COUNT(*) as count FROM classes")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$recent_warnings = db_fetch_all("SELECT w.*, s.first_name, s.last_name, s.student_no, c.category_name, u.full_name as user_name FROM warnings w JOIN students s ON w.student_id = s.id JOIN warning_categories c ON w.category_id = c.id JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC LIMIT 5");
$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ana Panel - ODTS</title>
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
.sidebar-menu a i { margin-right: 10px; }
.main-content { margin-left: 250px; flex: 1; padding: 30px; }
.header { background: white; padding: 20px 30px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.header h1 { font-size: 28px; color: #333; }
.user-info { display: flex; align-items: center; gap: 15px; }
.user-info span { color: #666; }
.user-info a { color: #667eea; text-decoration: none; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: relative; overflow: hidden; }
.stat-card::before { content: ''; position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); opacity: 0.1; border-radius: 50%; transform: translate(30px, -30px); }
.stat-card h3 { color: #999; font-size: 14px; font-weight: 600; margin-bottom: 10px; text-transform: uppercase; }
.stat-card .number { font-size: 36px; font-weight: bold; color: #333; }
.stat-card.students .number { color: #667eea; }
.stat-card.warnings .number { color: #f093fb; }
.stat-card.classes .number { color: #4facfe; }
.stat-card.users .number { color: #43e97b; }
.card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
.card-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
.card-header h2 { color: #333; font-size: 20px; }
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
@media (max-width: 768px) {
.sidebar { width: 100%; height: auto; position: relative; }
.main-content { margin-left: 0; }
.stats-grid { grid-template-columns: 1fr; }
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
<a href="/dashboard.php" class="active">📊 Ana Panel</a>
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
<h1>Ana Panel</h1>
<div class="user-info">
<span>Hoş geldiniz, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></span>
<a href="/logout.php">Çıkış</a>
</div>
</div>
<div class="stats-grid">
<div class="stat-card students">
<h3>Toplam Öğrenci</h3>
<div class="number"><?php echo $total_students; ?></div>
</div>
<div class="stat-card warnings">
<h3>Toplam Uyarı</h3>
<div class="number"><?php echo $total_warnings; ?></div>
</div>
<div class="stat-card classes">
<h3>Toplam Sınıf</h3>
<div class="number"><?php echo $total_classes; ?></div>
</div>
<div class="stat-card users">
<h3>Toplam Kullanıcı</h3>
<div class="number"><?php echo $total_users; ?></div>
</div>
</div>
<div class="card">
<div class="card-header">
<h2>Son Uyarılar</h2>
</div>
<?php if (count($recent_warnings) > 0): ?>
<table>
<thead>
<tr>
<th>Öğrenci</th>
<th>Öğrenci No</th>
<th>Kategori</th>
<th>Seviye</th>
<th>Tarih</th>
<th>Ekleyen</th>
</tr>
</thead>
<tbody>
<?php foreach ($recent_warnings as $warning): ?>
<tr>
<td><?php echo htmlspecialchars($warning['first_name'] . ' ' . $warning['last_name']); ?></td>
<td><?php echo htmlspecialchars($warning['student_no']); ?></td>
<td><?php echo htmlspecialchars($warning['category_name']); ?></td>
<td><?php echo get_warning_level_badge($warning['level']); ?></td>
<td><?php echo format_date($warning['warning_date'], 'd.m.Y'); ?></td>
<td><?php echo htmlspecialchars($warning['user_name']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p style="color: #999; text-align: center; padding: 20px;">Henüz uyarı kaydı bulunmamaktadır.</p>
<?php endif; ?>
</div>
</div>
</div>
</body>
</html>