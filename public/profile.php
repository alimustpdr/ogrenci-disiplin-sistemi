<?php
/**
 * Profile Page
 */

$pageTitle = 'Profil';

if (isStudent()) {
    $studentModel = new Student();
    $studentId = getStudentId();
    $profile = $studentModel->getById($studentId);
} else {
    $userModel = new User();
    $userId = getUserId();
    $profile = $userModel->getById($userId);
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'change_password') {
    $currentPassword = post('current_password');
    $newPassword = post('new_password');
    $confirmPassword = post('confirm_password');
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        setFlash('error', 'Lütfen tüm alanları doldurun.');
    } elseif ($newPassword !== $confirmPassword) {
        setFlash('error', 'Yeni şifreler eşleşmiyor.');
    } elseif (strlen($newPassword) < 6) {
        setFlash('error', 'Şifre en az 6 karakter olmalıdır.');
    } else {
        // Verify current password
        if (isStudent()) {
            $studentModel = new Student();
            $student = $studentModel->login($_SESSION['student_number'], $currentPassword);
            if ($student) {
                $data = ['password' => $newPassword];
                $result = $studentModel->update($studentId, $data);
                setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'Şifreniz başarıyla değiştirildi.' : $result['message']);
            } else {
                setFlash('error', 'Mevcut şifre hatalı.');
            }
        } else {
            $userModel = new User();
            $user = $userModel->login($_SESSION['username'], $currentPassword);
            if ($user) {
                $data = ['password' => $newPassword];
                $result = $userModel->update($userId, array_merge($profile, $data));
                setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'Şifreniz başarıyla değiştirildi.' : $result['message']);
            } else {
                setFlash('error', 'Mevcut şifre hatalı.');
            }
        }
    }
}

ob_start();
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-person-circle me-2"></i><?php echo $pageTitle; ?></h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Profil Bilgileri
                </div>
                <div class="card-body">
                    <?php if (isStudent()): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Öğrenci Numarası</label>
                            <p class="form-control-plaintext"><strong><?php echo clean($profile['student_number']); ?></strong></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Ad Soyad</label>
                            <p class="form-control-plaintext"><?php echo clean($profile['full_name']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">E-posta</label>
                            <p class="form-control-plaintext"><?php echo clean($profile['email'] ?? '-'); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Sınıf</label>
                            <p class="form-control-plaintext"><?php echo clean($profile['class'] ?? '-'); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Doğum Tarihi</label>
                            <p class="form-control-plaintext"><?php echo formatDate($profile['birth_date']); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Kullanıcı Adı</label>
                            <p class="form-control-plaintext"><strong><?php echo clean($profile['username']); ?></strong></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Ad Soyad</label>
                            <p class="form-control-plaintext"><?php echo clean($profile['full_name']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">E-posta</label>
                            <p class="form-control-plaintext"><?php echo clean($profile['email']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Rol</label>
                            <p class="form-control-plaintext">
                                <?php if ($profile['role'] === 'admin'): ?>
                                    <span class="badge bg-danger">Yönetici</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Öğretmen</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-key me-2"></i>Şifre Değiştir
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mevcut Şifre</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Yeni Şifre</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="form-text">Şifre en az 6 karakter olmalıdır.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-2"></i>Şifreyi Değiştir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>
