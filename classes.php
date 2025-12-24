<?php
$page_title = 'Sınıflar';
require_once __DIR__ . '/includes/header.php';
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['action'])) {
$conn = db_connect();
if ($_POST['action'] == 'add') {
$class_name = $conn->real_escape_string($_POST['class_name']);
$grade = intval($_POST['grade']);
$section = $conn->real_escape_string($_POST['section']);
$teacher_id = intval($_POST['teacher_id']);
$teacher_id_val = $teacher_id > 0 ? $teacher_id : 'NULL';
$result = $conn->query("INSERT INTO classes (class_name, grade, section, teacher_id) VALUES ('$class_name', $grade, '$section', $teacher_id_val)");
if ($result) {
$message = 'Sınıf başarıyla eklendi!';
$message_type = 'success';
log_activity($user['id'], 'Sınıf Eklendi', "$class_name eklendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'edit') {
$id = intval($_POST['id']);
$class_name = $conn->real_escape_string($_POST['class_name']);
$grade = intval($_POST['grade']);
$section = $conn->real_escape_string($_POST['section']);
$teacher_id = intval($_POST['teacher_id']);
$teacher_id_val = $teacher_id > 0 ? $teacher_id : 'NULL';
$result = $conn->query("UPDATE classes SET class_name='$class_name', grade=$grade, section='$section', teacher_id=$teacher_id_val WHERE id=$id");
if ($result) {
$message = 'Sınıf başarıyla güncellendi!';
$message_type = 'success';
log_activity($user['id'], 'Sınıf Güncellendi', "$class_name güncellendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'delete') {
$id = intval($_POST['id']);
$class = $conn->query("SELECT class_name FROM classes WHERE id=$id")->fetch_assoc();
$result = $conn->query("DELETE FROM classes WHERE id=$id");
if ($result) {
$message = 'Sınıf başarıyla silindi!';
$message_type = 'success';
log_activity($user['id'], 'Sınıf Silindi', $class['class_name'] . ' silindi');
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
}
$conn->close();
}
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$grade_filter = isset($_GET['grade']) ? intval($_GET['grade']) : 0;
$where = [];
if ($search) {
$search_escaped = db_escape($search);
$where[] = "(class_name LIKE '%$search_escaped%' OR section LIKE '%$search_escaped%')";
}
if ($grade_filter > 0) {
$where[] = "grade = $grade_filter";
}
$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$classes = db_fetch_all("SELECT c.*, u.full_name as teacher_name, (SELECT COUNT(*) FROM students WHERE class_id = c.id) as student_count FROM classes c LEFT JOIN users u ON c.teacher_id = u.id $where_clause ORDER BY c.grade, c.section");
$teachers = db_fetch_all("SELECT * FROM users WHERE role IN ('ogretmen', 'mudur_yardimcisi', 'admin') ORDER BY full_name");
?>
<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>
<div class="card">
<div class="card-header">
<h2>Sınıf Listesi</h2>
<button class="btn btn-primary" onclick="openModal('addModal')">➕ Sınıf Ekle</button>
</div>
<form method="GET" style="margin-bottom: 20px;">
<div class="form-row">
<div class="form-group">
<input type="text" name="search" placeholder="Sınıf adı ile ara..." value="<?php echo htmlspecialchars($search); ?>">
</div>
<div class="form-group">
<select name="grade">
<option value="0">Tüm Sınıflar</option>
<option value="9" <?php echo $grade_filter == 9 ? 'selected' : ''; ?>>9. Sınıf</option>
<option value="10" <?php echo $grade_filter == 10 ? 'selected' : ''; ?>>10. Sınıf</option>
<option value="11" <?php echo $grade_filter == 11 ? 'selected' : ''; ?>>11. Sınıf</option>
<option value="12" <?php echo $grade_filter == 12 ? 'selected' : ''; ?>>12. Sınıf</option>
</select>
</div>
<div class="form-group">
<button type="submit" class="btn btn-primary">🔍 Ara</button>
</div>
</div>
</form>
<?php if (count($classes) > 0): ?>
<table>
<thead>
<tr>
<th>Sınıf</th>
<th>Seviye</th>
<th>Şube</th>
<th>Danışman Öğretmen</th>
<th>Öğrenci Sayısı</th>
<th>İşlemler</th>
</tr>
</thead>
<tbody>
<?php foreach ($classes as $class): ?>
<tr>
<td><strong><?php echo htmlspecialchars($class['class_name']); ?></strong></td>
<td><?php echo $class['grade']; ?>. Sınıf</td>
<td><?php echo htmlspecialchars($class['section']); ?></td>
<td><?php echo $class['teacher_name'] ? htmlspecialchars($class['teacher_name']) : '-'; ?></td>
<td><?php echo $class['student_count']; ?> öğrenci</td>
<td>
<button class="btn btn-sm btn-primary" onclick='editClass(<?php echo json_encode($class); ?>)'>✏️ Düzenle</button>
<button class="btn btn-sm btn-danger" onclick="deleteClass(<?php echo $class['id']; ?>, '<?php echo htmlspecialchars($class['class_name']); ?>')">🗑️ Sil</button>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p style="color: #999; text-align: center; padding: 20px;">Sınıf bulunamadı.</p>
<?php endif; ?>
</div>
<div id="addModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('addModal')">&times;</span>
<h3>Yeni Sınıf Ekle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="form-group">
<label>Sınıf Adı (örn: 9-A, 10-B):</label>
<input type="text" name="class_name" required>
</div>
<div class="form-row">
<div class="form-group">
<label>Seviye:</label>
<select name="grade" required>
<option value="">Seviye Seçin</option>
<option value="9">9. Sınıf</option>
<option value="10">10. Sınıf</option>
<option value="11">11. Sınıf</option>
<option value="12">12. Sınıf</option>
</select>
</div>
<div class="form-group">
<label>Şube:</label>
<input type="text" name="section" placeholder="A, B, C..." required>
</div>
</div>
<div class="form-group">
<label>Danışman Öğretmen:</label>
<select name="teacher_id">
<option value="0">Öğretmen Seçin</option>
<?php foreach ($teachers as $teacher): ?>
<option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['full_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<button type="submit" class="btn btn-success">💾 Kaydet</button>
</form>
</div>
</div>
<div id="editModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('editModal')">&times;</span>
<h3>Sınıf Düzenle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="edit_id">
<div class="form-group">
<label>Sınıf Adı:</label>
<input type="text" name="class_name" id="edit_class_name" required>
</div>
<div class="form-row">
<div class="form-group">
<label>Seviye:</label>
<select name="grade" id="edit_grade" required>
<option value="">Seviye Seçin</option>
<option value="9">9. Sınıf</option>
<option value="10">10. Sınıf</option>
<option value="11">11. Sınıf</option>
<option value="12">12. Sınıf</option>
</select>
</div>
<div class="form-group">
<label>Şube:</label>
<input type="text" name="section" id="edit_section" required>
</div>
</div>
<div class="form-group">
<label>Danışman Öğretmen:</label>
<select name="teacher_id" id="edit_teacher_id">
<option value="0">Öğretmen Seçin</option>
<?php foreach ($teachers as $teacher): ?>
<option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['full_name']); ?></option>
<?php endforeach; ?>
</select>
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
function editClass(classData) {
document.getElementById('edit_id').value = classData.id;
document.getElementById('edit_class_name').value = classData.class_name;
document.getElementById('edit_grade').value = classData.grade;
document.getElementById('edit_section').value = classData.section;
document.getElementById('edit_teacher_id').value = classData.teacher_id || 0;
openModal('editModal');
}
function deleteClass(id, name) {
if (confirm('Sınıfı silmek istediğinizden emin misiniz?\n\n' + name + '\n\nBu sınıfa kayıtlı öğrencilerin sınıf bilgisi silinecektir.')) {
document.getElementById('delete_id').value = id;
document.getElementById('deleteForm').submit();
}
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>