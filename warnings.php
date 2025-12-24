<?php
$page_title = 'Uyarılar';
require_once __DIR__ . '/includes/header.php';
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['action'])) {
$conn = db_connect();
if ($_POST['action'] == 'add') {
$student_id = intval($_POST['student_id']);
$category_id = intval($_POST['category_id']);
$level = intval($_POST['level']);
$description = $conn->real_escape_string($_POST['description']);
$action_taken = $conn->real_escape_string($_POST['action_taken']);
$warning_date = $conn->real_escape_string($_POST['warning_date']);
$user_id = $user['id'];
$result = $conn->query("INSERT INTO warnings (student_id, category_id, level, description, action_taken, user_id, warning_date) VALUES ($student_id, $category_id, $level, '$description', '$action_taken', $user_id, '$warning_date')");
if ($result) {
$message = 'Uyarı başarıyla eklendi!';
$message_type = 'success';
log_activity($user['id'], 'Uyarı Eklendi', "Seviye $level uyarı eklendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'edit') {
$id = intval($_POST['id']);
$student_id = intval($_POST['student_id']);
$category_id = intval($_POST['category_id']);
$level = intval($_POST['level']);
$description = $conn->real_escape_string($_POST['description']);
$action_taken = $conn->real_escape_string($_POST['action_taken']);
$warning_date = $conn->real_escape_string($_POST['warning_date']);
$result = $conn->query("UPDATE warnings SET student_id=$student_id, category_id=$category_id, level=$level, description='$description', action_taken='$action_taken', warning_date='$warning_date' WHERE id=$id");
if ($result) {
$message = 'Uyarı başarıyla güncellendi!';
$message_type = 'success';
log_activity($user['id'], 'Uyarı Güncellendi', "Uyarı #$id güncellendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'delete') {
$id = intval($_POST['id']);
$result = $conn->query("DELETE FROM warnings WHERE id=$id");
if ($result) {
$message = 'Uyarı başarıyla silindi!';
$message_type = 'success';
log_activity($user['id'], 'Uyarı Silindi', "Uyarı #$id silindi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
}
$conn->close();
}
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$student_filter = isset($_GET['student']) ? intval($_GET['student']) : 0;
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$level_filter = isset($_GET['level']) ? intval($_GET['level']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;
$where = [];
if ($search) {
$search_escaped = db_escape($search);
$where[] = "(s.student_no LIKE '%$search_escaped%' OR s.first_name LIKE '%$search_escaped%' OR s.last_name LIKE '%$search_escaped%')";
}
if ($student_filter > 0) {
$where[] = "w.student_id = $student_filter";
}
if ($category_filter > 0) {
$where[] = "w.category_id = $category_filter";
}
if ($level_filter > 0) {
$where[] = "w.level = $level_filter";
}
$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$total = db_fetch_one("SELECT COUNT(*) as count FROM warnings w JOIN students s ON w.student_id = s.id $where_clause")['count'];
$warnings = db_fetch_all("SELECT w.*, s.first_name, s.last_name, s.student_no, c.category_name, u.full_name as user_name FROM warnings w JOIN students s ON w.student_id = s.id JOIN warning_categories c ON w.category_id = c.id JOIN users u ON w.user_id = u.id $where_clause ORDER BY w.warning_date DESC, w.created_at DESC LIMIT $per_page OFFSET $offset");
$students = db_fetch_all("SELECT * FROM students ORDER BY first_name, last_name");
$categories = db_fetch_all("SELECT * FROM warning_categories ORDER BY category_name");
$pagination = paginate($total, $per_page, $page);
?>
<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>
<div class="card">
<div class="card-header">
<h2>Uyarı Listesi</h2>
<button class="btn btn-primary" onclick="openModal('addModal')">➕ Uyarı Ekle</button>
</div>
<form method="GET" style="margin-bottom: 20px;">
<div class="form-row">
<div class="form-group">
<input type="text" name="search" placeholder="Öğrenci No, Ad veya Soyad ile ara..." value="<?php echo htmlspecialchars($search); ?>">
</div>
<div class="form-group">
<select name="category">
<option value="0">Tüm Kategoriler</option>
<?php foreach ($categories as $category): ?>
<option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['category_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<select name="level">
<option value="0">Tüm Seviyeler</option>
<option value="1" <?php echo $level_filter == 1 ? 'selected' : ''; ?>>Seviye 1</option>
<option value="2" <?php echo $level_filter == 2 ? 'selected' : ''; ?>>Seviye 2</option>
<option value="3" <?php echo $level_filter == 3 ? 'selected' : ''; ?>>Seviye 3</option>
<option value="4" <?php echo $level_filter == 4 ? 'selected' : ''; ?>>Seviye 4</option>
<option value="5" <?php echo $level_filter == 5 ? 'selected' : ''; ?>>Seviye 5</option>
</select>
</div>
<div class="form-group">
<button type="submit" class="btn btn-primary">🔍 Ara</button>
</div>
</div>
</form>
<?php if (count($warnings) > 0): ?>
<table>
<thead>
<tr>
<th>Tarih</th>
<th>Öğrenci</th>
<th>Kategori</th>
<th>Seviye</th>
<th>Açıklama</th>
<th>Ekleyen</th>
<th>İşlemler</th>
</tr>
</thead>
<tbody>
<?php foreach ($warnings as $warning): ?>
<tr>
<td><?php echo format_date($warning['warning_date'], 'd.m.Y'); ?></td>
<td><?php echo htmlspecialchars($warning['first_name'] . ' ' . $warning['last_name']); ?><br><small><?php echo htmlspecialchars($warning['student_no']); ?></small></td>
<td><?php echo htmlspecialchars($warning['category_name']); ?></td>
<td><?php echo get_warning_level_badge($warning['level']); ?></td>
<td><?php echo htmlspecialchars(substr($warning['description'], 0, 50)) . (strlen($warning['description']) > 50 ? '...' : ''); ?></td>
<td><?php echo htmlspecialchars($warning['user_name']); ?></td>
<td>
<button class="btn btn-sm btn-primary" onclick='editWarning(<?php echo json_encode($warning); ?>)'>✏️ Düzenle</button>
<button class="btn btn-sm btn-danger" onclick="deleteWarning(<?php echo $warning['id']; ?>)">🗑️ Sil</button>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php if ($pagination['total_pages'] > 1): ?>
<div class="pagination">
<?php if ($page > 1): ?>
<a href="?page=<?php echo $pagination['prev']; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&level=<?php echo $level_filter; ?>">← Önceki</a>
<?php endif; ?>
<span>Sayfa <?php echo $page; ?> / <?php echo $pagination['total_pages']; ?></span>
<?php if ($page < $pagination['total_pages']): ?>
<a href="?page=<?php echo $pagination['next']; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&level=<?php echo $level_filter; ?>">Sonraki →</a>
<?php endif; ?>
</div>
<?php endif; ?>
<?php else: ?>
<p style="color: #999; text-align: center; padding: 20px;">Uyarı bulunamadı.</p>
<?php endif; ?>
</div>
<div id="addModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('addModal')">&times;</span>
<h3>Yeni Uyarı Ekle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="form-group">
<label>Öğrenci:</label>
<select name="student_id" required>
<option value="">Öğrenci Seçin</option>
<?php foreach ($students as $student): ?>
<option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['student_no'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-row">
<div class="form-group">
<label>Kategori:</label>
<select name="category_id" required>
<option value="">Kategori Seçin</option>
<?php foreach ($categories as $category): ?>
<option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label>Seviye:</label>
<select name="level" required>
<option value="">Seviye Seçin</option>
<option value="1">Seviye 1 - Hafif</option>
<option value="2">Seviye 2 - Orta</option>
<option value="3">Seviye 3 - Ciddi</option>
<option value="4">Seviye 4 - Çok Ciddi</option>
<option value="5">Seviye 5 - Kritik</option>
</select>
</div>
</div>
<div class="form-group">
<label>Tarih:</label>
<input type="date" name="warning_date" value="<?php echo date('Y-m-d'); ?>" required>
</div>
<div class="form-group">
<label>Açıklama:</label>
<textarea name="description" rows="4" required></textarea>
</div>
<div class="form-group">
<label>Alınan Önlem:</label>
<textarea name="action_taken" rows="3"></textarea>
</div>
<button type="submit" class="btn btn-success">💾 Kaydet</button>
</form>
</div>
</div>
<div id="editModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('editModal')">&times;</span>
<h3>Uyarı Düzenle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="edit_id">
<div class="form-group">
<label>Öğrenci:</label>
<select name="student_id" id="edit_student_id" required>
<option value="">Öğrenci Seçin</option>
<?php foreach ($students as $student): ?>
<option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['student_no'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-row">
<div class="form-group">
<label>Kategori:</label>
<select name="category_id" id="edit_category_id" required>
<option value="">Kategori Seçin</option>
<?php foreach ($categories as $category): ?>
<option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label>Seviye:</label>
<select name="level" id="edit_level" required>
<option value="">Seviye Seçin</option>
<option value="1">Seviye 1 - Hafif</option>
<option value="2">Seviye 2 - Orta</option>
<option value="3">Seviye 3 - Ciddi</option>
<option value="4">Seviye 4 - Çok Ciddi</option>
<option value="5">Seviye 5 - Kritik</option>
</select>
</div>
</div>
<div class="form-group">
<label>Tarih:</label>
<input type="date" name="warning_date" id="edit_warning_date" required>
</div>
<div class="form-group">
<label>Açıklama:</label>
<textarea name="description" id="edit_description" rows="4" required></textarea>
</div>
<div class="form-group">
<label>Alınan Önlem:</label>
<textarea name="action_taken" id="edit_action_taken" rows="3"></textarea>
</div>
<button type="submit" class="btn btn-success">💾 Güncelle</button>
</form>
</div>
</div>
<form id="deleteForm" method="POST" style="display: none;">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" id="delete_id">
</form>
<script>
function openModal(id) {
document.getElementById(id).classList.add('active');
}
function closeModal(id) {
document.getElementById(id).classList.remove('active');
}
function editWarning(warning) {
document.getElementById('edit_id').value = warning.id;
document.getElementById('edit_student_id').value = warning.student_id;
document.getElementById('edit_category_id').value = warning.category_id;
document.getElementById('edit_level').value = warning.level;
document.getElementById('edit_warning_date').value = warning.warning_date;
document.getElementById('edit_description').value = warning.description;
document.getElementById('edit_action_taken').value = warning.action_taken;
openModal('editModal');
}
function deleteWarning(id) {
if (confirm('Uyarıyı silmek istediğinizden emin misiniz?')) {
document.getElementById('delete_id').value = id;
document.getElementById('deleteForm').submit();
}
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>