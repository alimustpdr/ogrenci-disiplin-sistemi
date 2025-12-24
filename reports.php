<?php
$page_title = 'Raporlar';
require_once __DIR__ . '/includes/header.php';
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
$student_filter = isset($_GET['student']) ? intval($_GET['student']) : 0;
$class_filter = isset($_GET['class']) ? intval($_GET['class']) : 0;
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$level_filter = isset($_GET['level']) ? intval($_GET['level']) : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$where = [];
if ($student_filter > 0) {
$where[] = "w.student_id = $student_filter";
}
if ($class_filter > 0) {
$where[] = "s.class_id = $class_filter";
}
if ($category_filter > 0) {
$where[] = "w.category_id = $category_filter";
}
if ($level_filter > 0) {
$where[] = "w.level = $level_filter";
}
if ($date_from) {
$where[] = "w.warning_date >= '$date_from'";
}
if ($date_to) {
$where[] = "w.warning_date <= '$date_to'";
}
$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$warnings = db_fetch_all("SELECT w.warning_date, s.student_no, s.first_name, s.last_name, cl.class_name, c.category_name, w.level, w.description, w.action_taken, u.full_name as user_name FROM warnings w JOIN students s ON w.student_id = s.id LEFT JOIN classes cl ON s.class_id = cl.id JOIN warning_categories c ON w.category_id = c.id JOIN users u ON w.user_id = u.id $where_clause ORDER BY w.warning_date DESC");
$data = [];
foreach ($warnings as $warning) {
$data[] = [
format_date($warning['warning_date'], 'd.m.Y'),
$warning['student_no'],
$warning['first_name'] . ' ' . $warning['last_name'],
$warning['class_name'] ?: '-',
$warning['category_name'],
'Seviye ' . $warning['level'],
$warning['description'],
$warning['action_taken'],
$warning['user_name']
];
}
$headers = ['Tarih', 'Öğrenci No', 'Öğrenci', 'Sınıf', 'Kategori', 'Seviye', 'Açıklama', 'Alınan Önlem', 'Ekleyen'];
export_to_excel($data, 'uyari_raporu_' . date('Y-m-d'), $headers);
exit;
}
$student_filter = isset($_GET['student']) ? intval($_GET['student']) : 0;
$class_filter = isset($_GET['class']) ? intval($_GET['class']) : 0;
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$level_filter = isset($_GET['level']) ? intval($_GET['level']) : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$where = [];
if ($student_filter > 0) {
$where[] = "w.student_id = $student_filter";
}
if ($class_filter > 0) {
$where[] = "s.class_id = $class_filter";
}
if ($category_filter > 0) {
$where[] = "w.category_id = $category_filter";
}
if ($level_filter > 0) {
$where[] = "w.level = $level_filter";
}
if ($date_from) {
$date_from_escaped = db_escape($date_from);
$where[] = "w.warning_date >= '$date_from_escaped'";
}
if ($date_to) {
$date_to_escaped = db_escape($date_to);
$where[] = "w.warning_date <= '$date_to_escaped'";
}
$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$warnings = db_fetch_all("SELECT w.*, s.first_name, s.last_name, s.student_no, c.category_name, cl.class_name, u.full_name as user_name FROM warnings w JOIN students s ON w.student_id = s.id LEFT JOIN classes cl ON s.class_id = cl.id JOIN warning_categories c ON w.category_id = c.id JOIN users u ON w.user_id = u.id $where_clause ORDER BY w.warning_date DESC LIMIT 100");
$students = db_fetch_all("SELECT * FROM students ORDER BY first_name, last_name");
$classes = db_fetch_all("SELECT * FROM classes ORDER BY grade, section");
$categories = db_fetch_all("SELECT * FROM warning_categories ORDER BY category_name");
$stats = [];
if (count($warnings) > 0) {
$total_warnings = count($warnings);
$level_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$category_counts = [];
foreach ($warnings as $warning) {
$level_counts[$warning['level']]++;
if (!isset($category_counts[$warning['category_name']])) {
$category_counts[$warning['category_name']] = 0;
}
$category_counts[$warning['category_name']]++;
}
$stats = [
'total' => $total_warnings,
'levels' => $level_counts,
'categories' => $category_counts
];
}
?>
<div class="card">
<div class="card-header">
<h2>Rapor Filtreleri</h2>
</div>
<form method="GET">
<div class="form-row">
<div class="form-group">
<label>Öğrenci:</label>
<select name="student">
<option value="0">Tüm Öğrenciler</option>
<?php foreach ($students as $student): ?>
<option value="<?php echo $student['id']; ?>" <?php echo $student_filter == $student['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($student['student_no'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label>Sınıf:</label>
<select name="class">
<option value="0">Tüm Sınıflar</option>
<?php foreach ($classes as $class): ?>
<option value="<?php echo $class['id']; ?>" <?php echo $class_filter == $class['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($class['class_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<div class="form-row">
<div class="form-group">
<label>Kategori:</label>
<select name="category">
<option value="0">Tüm Kategoriler</option>
<?php foreach ($categories as $category): ?>
<option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['category_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label>Seviye:</label>
<select name="level">
<option value="0">Tüm Seviyeler</option>
<option value="1" <?php echo $level_filter == 1 ? 'selected' : ''; ?>>Seviye 1</option>
<option value="2" <?php echo $level_filter == 2 ? 'selected' : ''; ?>>Seviye 2</option>
<option value="3" <?php echo $level_filter == 3 ? 'selected' : ''; ?>>Seviye 3</option>
<option value="4" <?php echo $level_filter == 4 ? 'selected' : ''; ?>>Seviye 4</option>
<option value="5" <?php echo $level_filter == 5 ? 'selected' : ''; ?>>Seviye 5</option>
</select>
</div>
</div>
<div class="form-row">
<div class="form-group">
<label>Başlangıç Tarihi:</label>
<input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
</div>
<div class="form-group">
<label>Bitiş Tarihi:</label>
<input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
</div>
</div>
<div class="form-row">
<div class="form-group">
<button type="submit" class="btn btn-primary">🔍 Rapor Oluştur</button>
</div>
<div class="form-group">
<button type="submit" name="export" value="excel" class="btn btn-success">📥 Excel'e Aktar</button>
</div>
</div>
</form>
</div>
<?php if (!empty($stats)): ?>
<div class="card">
<div class="card-header">
<h2>İstatistikler</h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
<div style="padding: 20px; background: #f8f9fa; border-radius: 5px;">
<h4 style="color: #666; margin-bottom: 10px;">Toplam Uyarı</h4>
<div style="font-size: 32px; font-weight: bold; color: #667eea;"><?php echo $stats['total']; ?></div>
</div>
<?php foreach ($stats['levels'] as $level => $count): ?>
<div style="padding: 20px; background: #f8f9fa; border-radius: 5px;">
<h4 style="color: #666; margin-bottom: 10px;">Seviye <?php echo $level; ?></h4>
<div style="font-size: 32px; font-weight: bold; color: #667eea;"><?php echo $count; ?></div>
</div>
<?php endforeach; ?>
</div>
<div style="margin-top: 30px;">
<h3 style="margin-bottom: 15px; color: #333;">Kategorilere Göre Dağılım</h3>
<?php foreach ($stats['categories'] as $cat => $count): ?>
<div style="margin-bottom: 10px;">
<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
<span><?php echo htmlspecialchars($cat); ?></span>
<span><strong><?php echo $count; ?></strong></span>
</div>
<div style="height: 10px; background: #e0e0e0; border-radius: 5px; overflow: hidden;">
<div style="height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: <?php echo ($count / $stats['total'] * 100); ?>%;"></div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
<?php if (count($warnings) > 0): ?>
<div class="card">
<div class="card-header">
<h2>Uyarı Listesi (Son 100 Kayıt)</h2>
</div>
<table>
<thead>
<tr>
<th>Tarih</th>
<th>Öğrenci</th>
<th>Sınıf</th>
<th>Kategori</th>
<th>Seviye</th>
<th>Açıklama</th>
<th>Ekleyen</th>
</tr>
</thead>
<tbody>
<?php foreach ($warnings as $warning): ?>
<tr>
<td><?php echo format_date($warning['warning_date'], 'd.m.Y'); ?></td>
<td><?php echo htmlspecialchars($warning['first_name'] . ' ' . $warning['last_name']); ?><br><small><?php echo htmlspecialchars($warning['student_no']); ?></small></td>
<td><?php echo $warning['class_name'] ? htmlspecialchars($warning['class_name']) : '-'; ?></td>
<td><?php echo htmlspecialchars($warning['category_name']); ?></td>
<td><?php echo get_warning_level_badge($warning['level']); ?></td>
<td><?php echo htmlspecialchars(substr($warning['description'], 0, 50)) . (strlen($warning['description']) > 50 ? '...' : ''); ?></td>
<td><?php echo htmlspecialchars($warning['user_name']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php elseif (!empty($_GET)): ?>
<div class="card">
<p style="color: #999; text-align: center; padding: 20px;">Seçilen kriterlere göre uyarı bulunamadı.</p>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>