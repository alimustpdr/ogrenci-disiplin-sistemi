<?php
$page_title = 'Öğrenciler';
require_once __DIR__ . '/includes/header.php';
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['action'])) {
$conn = db_connect();
if ($_POST['action'] == 'add') {
$student_no = $conn->real_escape_string($_POST['student_no']);
$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$class_id = intval($_POST['class_id']);
$birth_date = $conn->real_escape_string($_POST['birth_date']);
$parent_name = $conn->real_escape_string($_POST['parent_name']);
$parent_phone = $conn->real_escape_string($_POST['parent_phone']);
$address = $conn->real_escape_string($_POST['address']);
$class_id_val = $class_id > 0 ? $class_id : 'NULL';
$result = $conn->query("INSERT INTO students (student_no, first_name, last_name, class_id, birth_date, parent_name, parent_phone, address) VALUES ('$student_no', '$first_name', '$last_name', $class_id_val, '$birth_date', '$parent_name', '$parent_phone', '$address')");
if ($result) {
$message = 'Öğrenci başarıyla eklendi!';
$message_type = 'success';
log_activity($user['id'], 'Öğrenci Eklendi', "$first_name $last_name eklendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'edit') {
$id = intval($_POST['id']);
$student_no = $conn->real_escape_string($_POST['student_no']);
$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$class_id = intval($_POST['class_id']);
$birth_date = $conn->real_escape_string($_POST['birth_date']);
$parent_name = $conn->real_escape_string($_POST['parent_name']);
$parent_phone = $conn->real_escape_string($_POST['parent_phone']);
$address = $conn->real_escape_string($_POST['address']);
$class_id_val = $class_id > 0 ? $class_id : 'NULL';
$result = $conn->query("UPDATE students SET student_no='$student_no', first_name='$first_name', last_name='$last_name', class_id=$class_id_val, birth_date='$birth_date', parent_name='$parent_name', parent_phone='$parent_phone', address='$address' WHERE id=$id");
if ($result) {
$message = 'Öğrenci başarıyla güncellendi!';
$message_type = 'success';
log_activity($user['id'], 'Öğrenci Güncellendi', "$first_name $last_name güncellendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'delete') {
$id = intval($_POST['id']);
$student = $conn->query("SELECT first_name, last_name FROM students WHERE id=$id")->fetch_assoc();
$result = $conn->query("DELETE FROM students WHERE id=$id");
if ($result) {
$message = 'Öğrenci başarıyla silindi!';
$message_type = 'success';
log_activity($user['id'], 'Öğrenci Silindi', $student['first_name'] . ' ' . $student['last_name'] . ' silindi');
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
}
$conn->close();
}
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$class_filter = isset($_GET['class']) ? intval($_GET['class']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;
$where = [];
if ($search) {
$search_escaped = db_escape($search);
$where[] = "(student_no LIKE '%$search_escaped%' OR first_name LIKE '%$search_escaped%' OR last_name LIKE '%$search_escaped%')";
}
if ($class_filter > 0) {
$where[] = "s.class_id = $class_filter";
}
$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$total = db_fetch_one("SELECT COUNT(*) as count FROM students s $where_clause")['count'];
$students = db_fetch_all("SELECT s.*, c.class_name FROM students s LEFT JOIN classes c ON s.class_id = c.id $where_clause ORDER BY s.first_name, s.last_name LIMIT $per_page OFFSET $offset");
$classes = db_fetch_all("SELECT * FROM classes ORDER BY grade, section");
$pagination = paginate($total, $per_page, $page);
?>
<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>
<div class="card">
<div class="card-header">
<h2>Öğrenci Listesi</h2>
<button class="btn btn-primary" onclick="openModal('addModal')">➕ Öğrenci Ekle</button>
</div>
<form method="GET" style="margin-bottom: 20px;">
<div class="form-row">
<div class="form-group">
<input type="text" name="search" placeholder="Öğrenci No, Ad veya Soyad ile ara..." value="<?php echo htmlspecialchars($search); ?>">
</div>
<div class="form-group">
<select name="class">
<option value="0">Tüm Sınıflar</option>
<?php foreach ($classes as $class): ?>
<option value="<?php echo $class['id']; ?>" <?php echo $class_filter == $class['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($class['class_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<button type="submit" class="btn btn-primary">🔍 Ara</button>
</div>
</div>
</form>
<?php if (count($students) > 0): ?>
<table>
<thead>
<tr>
<th>Öğrenci No</th>
<th>Ad Soyad</th>
<th>Sınıf</th>
<th>Veli</th>
<th>Telefon</th>
<th>İşlemler</th>
</tr>
</thead>
<tbody>
<?php foreach ($students as $student): ?>
<tr>
<td><?php echo htmlspecialchars($student['student_no']); ?></td>
<td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
<td><?php echo $student['class_name'] ? htmlspecialchars($student['class_name']) : '-'; ?></td>
<td><?php echo htmlspecialchars($student['parent_name']); ?></td>
<td><?php echo htmlspecialchars($student['parent_phone']); ?></td>
<td>
<button class="btn btn-sm btn-primary" onclick='editStudent(<?php echo json_encode($student); ?>)'>✏️ Düzenle</button>
<button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>')">🗑️ Sil</button>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php if ($pagination['total_pages'] > 1): ?>
<div class="pagination">
<?php if ($page > 1): ?>
<a href="?page=<?php echo $pagination['prev']; ?>&search=<?php echo urlencode($search); ?>&class=<?php echo $class_filter; ?>">← Önceki</a>
<?php endif; ?>
<span>Sayfa <?php echo $page; ?> / <?php echo $pagination['total_pages']; ?></span>
<?php if ($page < $pagination['total_pages']): ?>
<a href="?page=<?php echo $pagination['next']; ?>&search=<?php echo urlencode($search); ?>&class=<?php echo $class_filter; ?>">Sonraki →</a>
<?php endif; ?>
</div>
<?php endif; ?>
<?php else: ?>
<p style="color: #999; text-align: center; padding: 20px;">Öğrenci bulunamadı.</p>
<?php endif; ?>
</div>
<div id="addModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('addModal')">&times;</span>
<h3>Yeni Öğrenci Ekle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="form-group">
<label>Öğrenci No:</label>
<input type="text" name="student_no" required>
</div>
<div class="form-row">
<div class="form-group">
<label>Ad:</label>
<input type="text" name="first_name" required>
</div>
<div class="form-group">
<label>Soyad:</label>
<input type="text" name="last_name" required>
</div>
</div>
<div class="form-row">
<div class="form-group">
<label>Sınıf:</label>
<select name="class_id">
<option value="0">Sınıf Seçin</option>
<?php foreach ($classes as $class): ?>
<option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label>Doğum Tarihi:</label>
<input type="date" name="birth_date">
</div>
</div>
<div class="form-group">
<label>Veli Adı:</label>
<input type="text" name="parent_name">
</div>
<div class="form-group">
<label>Veli Telefonu:</label>
<input type="text" name="parent_phone">
</div>
<div class="form-group">
<label>Adres:</label>
<textarea name="address" rows="3"></textarea>
</div>
<button type="submit" class="btn btn-success">💾 Kaydet</button>
</form>
</div>
</div>
<div id="editModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('editModal')">&times;</span>
<h3>Öğrenci Düzenle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="edit_id">
<div class="form-group">
<label>Öğrenci No:</label>
<input type="text" name="student_no" id="edit_student_no" required>
</div>
<div class="form-row">
<div class="form-group">
<label>Ad:</label>
<input type="text" name="first_name" id="edit_first_name" required>
</div>
<div class="form-group">
<label>Soyad:</label>
<input type="text" name="last_name" id="edit_last_name" required>
</div>
</div>
<div class="form-row">
<div class="form-group">
<label>Sınıf:</label>
<select name="class_id" id="edit_class_id">
<option value="0">Sınıf Seçin</option>
<?php foreach ($classes as $class): ?>
<option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label>Doğum Tarihi:</label>
<input type="date" name="birth_date" id="edit_birth_date">
</div>
</div>
<div class="form-group">
<label>Veli Adı:</label>
<input type="text" name="parent_name" id="edit_parent_name">
</div>
<div class="form-group">
<label>Veli Telefonu:</label>
<input type="text" name="parent_phone" id="edit_parent_phone">
</div>
<div class="form-group">
<label>Adres:</label>
<textarea name="address" id="edit_address" rows="3"></textarea>
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
function editStudent(student) {
document.getElementById('edit_id').value = student.id;
document.getElementById('edit_student_no').value = student.student_no;
document.getElementById('edit_first_name').value = student.first_name;
document.getElementById('edit_last_name').value = student.last_name;
document.getElementById('edit_class_id').value = student.class_id || 0;
document.getElementById('edit_birth_date').value = student.birth_date;
document.getElementById('edit_parent_name').value = student.parent_name;
document.getElementById('edit_parent_phone').value = student.parent_phone;
document.getElementById('edit_address').value = student.address;
openModal('editModal');
}
function deleteStudent(id, name) {
if (confirm('Öğrenciyi silmek istediğinizden emin misiniz?\n\n' + name)) {
document.getElementById('delete_id').value = id;
document.getElementById('deleteForm').submit();
}
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>