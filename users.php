<?php
$page_title = 'Kullanıcılar';
require_once __DIR__ . '/includes/header.php';
if ($user['role'] != 'admin') {
header('Location: /dashboard.php');
exit;
}
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['action'])) {
$conn = db_connect();
if ($_POST['action'] == 'add') {
$username = $conn->real_escape_string($_POST['username']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$full_name = $conn->real_escape_string($_POST['full_name']);
$email = $conn->real_escape_string($_POST['email']);
$role = $conn->real_escape_string($_POST['role']);
$result = $conn->query("INSERT INTO users (username, password, full_name, email, role) VALUES ('$username', '$password', '$full_name', '$email', '$role')");
if ($result) {
$message = 'Kullanıcı başarıyla eklendi!';
$message_type = 'success';
log_activity($user['id'], 'Kullanıcı Eklendi', "$full_name eklendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'edit') {
$id = intval($_POST['id']);
$username = $conn->real_escape_string($_POST['username']);
$full_name = $conn->real_escape_string($_POST['full_name']);
$email = $conn->real_escape_string($_POST['email']);
$role = $conn->real_escape_string($_POST['role']);
if (!empty($_POST['password'])) {
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$result = $conn->query("UPDATE users SET username='$username', password='$password', full_name='$full_name', email='$email', role='$role' WHERE id=$id");
} else {
$result = $conn->query("UPDATE users SET username='$username', full_name='$full_name', email='$email', role='$role' WHERE id=$id");
}
if ($result) {
$message = 'Kullanıcı başarıyla güncellendi!';
$message_type = 'success';
log_activity($user['id'], 'Kullanıcı Güncellendi', "$full_name güncellendi");
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
} elseif ($_POST['action'] == 'delete') {
$id = intval($_POST['id']);
if ($id == $user['id']) {
$message = 'Kendi hesabınızı silemezsiniz!';
$message_type = 'danger';
} else {
$usr = $conn->query("SELECT full_name FROM users WHERE id=$id")->fetch_assoc();
$result = $conn->query("DELETE FROM users WHERE id=$id");
if ($result) {
$message = 'Kullanıcı başarıyla silindi!';
$message_type = 'success';
log_activity($user['id'], 'Kullanıcı Silindi', $usr['full_name'] . ' silindi');
} else {
$message = 'Hata: ' . $conn->error;
$message_type = 'danger';
}
}
}
$conn->close();
}
}
$users = db_fetch_all("SELECT * FROM users ORDER BY full_name");
?>
<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>
<div class="card">
<div class="card-header">
<h2>Kullanıcı Listesi</h2>
<button class="btn btn-primary" onclick="openModal('addModal')">➕ Kullanıcı Ekle</button>
</div>
<?php if (count($users) > 0): ?>
<table>
<thead>
<tr>
<th>Kullanıcı Adı</th>
<th>Ad Soyad</th>
<th>E-posta</th>
<th>Rol</th>
<th>Kayıt Tarihi</th>
<th>İşlemler</th>
</tr>
</thead>
<tbody>
<?php foreach ($users as $usr): ?>
<tr>
<td><?php echo htmlspecialchars($usr['username']); ?></td>
<td><?php echo htmlspecialchars($usr['full_name']); ?></td>
<td><?php echo htmlspecialchars($usr['email']); ?></td>
<td>
<?php
$role_names = [
'admin' => '<span class="badge badge-danger">Admin</span>',
'mudur_yardimcisi' => '<span class="badge badge-warning">Müdür Yardımcısı</span>',
'ogretmen' => '<span class="badge badge-primary">Öğretmen</span>'
];
echo isset($role_names[$usr['role']]) ? $role_names[$usr['role']] : $usr['role'];
?>
</td>
<td><?php echo format_date($usr['created_at'], 'd.m.Y'); ?></td>
<td>
<button class="btn btn-sm btn-primary" onclick='editUser(<?php echo json_encode($usr); ?>)'>✏️ Düzenle</button>
<?php if ($usr['id'] != $user['id']): ?>
<button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $usr['id']; ?>, '<?php echo htmlspecialchars($usr['full_name']); ?>')">🗑️ Sil</button>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p style="color: #999; text-align: center; padding: 20px;">Kullanıcı bulunamadı.</p>
<?php endif; ?>
</div>
<div id="addModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<span class="close" onclick="closeModal('addModal')">&times;</span>
<h3>Yeni Kullanıcı Ekle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="form-group">
<label>Kullanıcı Adı:</label>
<input type="text" name="username" required>
</div>
<div class="form-group">
<label>Şifre:</label>
<input type="password" name="password" required>
</div>
<div class="form-group">
<label>Ad Soyad:</label>
<input type="text" name="full_name" required>
</div>
<div class="form-group">
<label>E-posta:</label>
<input type="email" name="email" required>
</div>
<div class="form-group">
<label>Rol:</label>
<select name="role" required>
<option value="">Rol Seçin</option>
<option value="admin">Admin</option>
<option value="mudur_yardimcisi">Müdür Yardımcısı</option>
<option value="ogretmen">Öğretmen</option>
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
<h3>Kullanıcı Düzenle</h3>
</div>
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="edit_id">
<div class="form-group">
<label>Kullanıcı Adı:</label>
<input type="text" name="username" id="edit_username" required>
</div>
<div class="form-group">
<label>Şifre (boş bırakın değiştirmek istemiyorsanız):</label>
<input type="password" name="password" id="edit_password">
</div>
<div class="form-group">
<label>Ad Soyad:</label>
<input type="text" name="full_name" id="edit_full_name" required>
</div>
<div class="form-group">
<label>E-posta:</label>
<input type="email" name="email" id="edit_email" required>
</div>
<div class="form-group">
<label>Rol:</label>
<select name="role" id="edit_role" required>
<option value="">Rol Seçin</option>
<option value="admin">Admin</option>
<option value="mudur_yardimcisi">Müdür Yardımcısı</option>
<option value="ogretmen">Öğretmen</option>
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
function editUser(userData) {
document.getElementById('edit_id').value = userData.id;
document.getElementById('edit_username').value = userData.username;
document.getElementById('edit_full_name').value = userData.full_name;
document.getElementById('edit_email').value = userData.email;
document.getElementById('edit_role').value = userData.role;
document.getElementById('edit_password').value = '';
openModal('editModal');
}
function deleteUser(id, name) {
if (confirm('Kullanıcıyı silmek istediğinizden emin misiniz?\n\n' + name)) {
document.getElementById('delete_id').value = id;
document.getElementById('deleteForm').submit();
}
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>