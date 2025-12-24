<?php
$page_title = 'Ayarlar';
require_once __DIR__ . '/includes/header.php';
if ($user['role'] != 'admin') {
header('Location: /dashboard.php');
exit;
}
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['action']) && $_POST['action'] == 'save_settings') {
$school_name = $_POST['school_name'];
$school_address = $_POST['school_address'];
$school_phone = $_POST['school_phone'];
$school_email = $_POST['school_email'];
$theme_color = $_POST['theme_color'];
set_setting('school_name', $school_name);
set_setting('school_address', $school_address);
set_setting('school_phone', $school_phone);
set_setting('school_email', $school_email);
set_setting('theme_color', $theme_color);
log_activity($user['id'], 'Ayarlar Güncellendi', 'Sistem ayarları güncellendi');
$message = 'Ayarlar başarıyla kaydedildi!';
$message_type = 'success';
}
}
$school_name = get_setting('school_name', 'Okul Adı');
$school_address = get_setting('school_address', '');
$school_phone = get_setting('school_phone', '');
$school_email = get_setting('school_email', '');
$theme_color = get_setting('theme_color', 'purple-blue');
?>
<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>
<div class="card">
<div class="card-header">
<h2>Genel Ayarlar</h2>
</div>
<form method="POST">
<input type="hidden" name="action" value="save_settings">
<div class="form-group">
<label>Okul Adı:</label>
<input type="text" name="school_name" value="<?php echo htmlspecialchars($school_name); ?>" required>
</div>
<div class="form-group">
<label>Okul Adresi:</label>
<textarea name="school_address" rows="3"><?php echo htmlspecialchars($school_address); ?></textarea>
</div>
<div class="form-row">
<div class="form-group">
<label>Telefon:</label>
<input type="text" name="school_phone" value="<?php echo htmlspecialchars($school_phone); ?>">
</div>
<div class="form-group">
<label>E-posta:</label>
<input type="email" name="school_email" value="<?php echo htmlspecialchars($school_email); ?>">
</div>
</div>
<div class="form-group">
<label>Tema Rengi:</label>
<select name="theme_color">
<option value="purple-blue" <?php echo $theme_color == 'purple-blue' ? 'selected' : ''; ?>>Mor-Mavi (Varsayılan)</option>
<option value="blue-green" <?php echo $theme_color == 'blue-green' ? 'selected' : ''; ?>>Mavi-Yeşil</option>
<option value="red-orange" <?php echo $theme_color == 'red-orange' ? 'selected' : ''; ?>>Kırmızı-Turuncu</option>
<option value="purple-pink" <?php echo $theme_color == 'purple-pink' ? 'selected' : ''; ?>>Mor-Pembe</option>
</select>
</div>
<button type="submit" class="btn btn-success">💾 Ayarları Kaydet</button>
</form>
</div>
<div class="card">
<div class="card-header">
<h2>Sistem Bilgileri</h2>
</div>
<table>
<tr>
<td style="width: 200px;"><strong>PHP Sürümü:</strong></td>
<td><?php echo phpversion(); ?></td>
</tr>
<tr>
<td><strong>Veritabanı:</strong></td>
<td>MySQL</td>
</tr>
<tr>
<td><strong>Oturum Yönetimi:</strong></td>
<td>Cookie Tabanlı (InfinityFree Uyumlu)</td>
</tr>
<tr>
<td><strong>Karakter Seti:</strong></td>
<td>UTF-8</td>
</tr>
<tr>
<td><strong>Kurulum Tarihi:</strong></td>
<td><?php echo format_date(get_setting('install_date', date('Y-m-d H:i:s')), 'd.m.Y H:i'); ?></td>
</tr>
</table>
</div>
<div class="card">
<div class="card-header">
<h2>Veritabanı İstatistikleri</h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
<?php
$conn = db_connect();
$tables = ['users' => 'Kullanıcılar', 'students' => 'Öğrenciler', 'classes' => 'Sınıflar', 'warnings' => 'Uyarılar', 'warning_categories' => 'Kategoriler', 'activity_logs' => 'Aktivite Logları'];
foreach ($tables as $table => $label) {
$result = $conn->query("SELECT COUNT(*) as count FROM $table");
$count = $result ? $result->fetch_assoc()['count'] : 0;
echo '<div style="padding: 20px; background: #f8f9fa; border-radius: 5px; text-align: center;">';
echo '<h4 style="color: #666; margin-bottom: 10px;">' . $label . '</h4>';
echo '<div style="font-size: 28px; font-weight: bold; color: #667eea;">' . $count . '</div>';
echo '</div>';
}
$conn->close();
?>
</div>
</div>
<div class="card">
<div class="card-header">
<h2>Son Aktiviteler</h2>
</div>
<?php
$activities = db_fetch_all("SELECT a.*, u.full_name FROM activity_logs a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 10");
if (count($activities) > 0):
?>
<table>
<thead>
<tr>
<th>Tarih</th>
<th>Kullanıcı</th>
<th>İşlem</th>
<th>Açıklama</th>
</tr>
</thead>
<tbody>
<?php foreach ($activities as $activity): ?>
<tr>
<td><?php echo format_date($activity['created_at']); ?></td>
<td><?php echo htmlspecialchars($activity['full_name']); ?></td>
<td><?php echo htmlspecialchars($activity['action']); ?></td>
<td><?php echo htmlspecialchars($activity['description']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p style="color: #999; text-align: center; padding: 20px;">Henüz aktivite kaydı bulunmamaktadır.</p>
<?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>