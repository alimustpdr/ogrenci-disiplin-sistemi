<?php
/**
 * Teacher Management (Admin Only)
 */

if (!isAdmin()) {
    redirect('index.php?page=dashboard');
}

$pageTitle = 'Öğretmen Yönetimi';
$userModel = new User();
$action = get('action', 'list');
$teacherId = get('id');

// Handle delete
if ($action === 'delete' && $teacherId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = $userModel->delete($teacherId);
        setFlash($result['success'] ? 'success' : 'error', $result['message']);
        redirect('index.php?page=teachers');
    }
}

// Handle create/edit
if (($action === 'create' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => post('username'),
        'full_name' => post('full_name'),
        'email' => post('email'),
        'role' => post('role', 'teacher'),
        'is_active' => post('is_active', 1)
    ];
    
    if ($action === 'create') {
        $data['password'] = post('password');
        $result = $userModel->create($data);
    } else {
        if (!empty(post('password'))) {
            $data['password'] = post('password');
        }
        $result = $userModel->update($teacherId, $data);
    }
    
    setFlash($result['success'] ? 'success' : 'error', $result['message']);
    
    if ($result['success']) {
        redirect('index.php?page=teachers');
    }
}

// Get teacher for edit
$teacher = null;
if ($action === 'edit' && $teacherId) {
    $teacher = $userModel->getById($teacherId);
    if (!$teacher) {
        setFlash('error', 'Kullanıcı bulunamadı.');
        redirect('index.php?page=teachers');
    }
}

// Search
$search = get('search');
if ($search) {
    $teachers = $userModel->search($search);
} else {
    $teachers = $userModel->getAll();
}

ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-badge me-2"></i><?php echo $pageTitle; ?></h2>
        <?php if ($action === 'list'): ?>
            <a href="index.php?page=teachers&action=create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Yeni Kullanıcı Ekle
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($action === 'list'): ?>
        <!-- Teacher List -->
        <div class="card">
            <div class="card-header">
                <form method="GET" action="" class="row g-3">
                    <input type="hidden" name="page" value="teachers">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="search" placeholder="Kullanıcı adı, ad soyad veya e-posta ile ara..." value="<?php echo clean($search ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Ara
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <?php if (empty($teachers)): ?>
                    <p class="text-muted text-center mb-0">Kullanıcı bulunamadı.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kullanıcı Adı</th>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th>Durum</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $t): ?>
                                    <tr>
                                        <td><strong><?php echo clean($t['username']); ?></strong></td>
                                        <td><?php echo clean($t['full_name']); ?></td>
                                        <td><?php echo clean($t['email']); ?></td>
                                        <td>
                                            <?php if ($t['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Yönetici</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Öğretmen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($t['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo formatDate($t['created_at']); ?></td>
                                        <td>
                                            <a href="index.php?page=teachers&action=edit&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($t['id'] != 1): // Varsayılan admin silinemez ?>
                                                <form method="POST" action="index.php?page=teachers&action=delete&id=<?php echo $t['id']; ?>" style="display:inline;" onsubmit="return confirmDelete();">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif ($action === 'create' || $action === 'edit'): ?>
        <!-- Teacher Form -->
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <?php echo $action === 'create' ? 'Yeni Kullanıcı Ekle' : 'Kullanıcı Düzenle'; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo clean($teacher['username'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Ad Soyad *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo clean($teacher['full_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo clean($teacher['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Şifre <?php echo $action === 'create' ? '*' : '(Değiştirmek için doldurun)'; ?>
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       <?php echo $action === 'create' ? 'required' : ''; ?>>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="teacher" <?php echo (isset($teacher) && $teacher['role'] === 'teacher') ? 'selected' : ''; ?>>Öğretmen</option>
                                    <option value="admin" <?php echo (isset($teacher) && $teacher['role'] === 'admin') ? 'selected' : ''; ?>>Yönetici</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           <?php echo (!isset($teacher) || $teacher['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="index.php?page=teachers" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>İptal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <?php echo $action === 'create' ? 'Ekle' : 'Güncelle'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>
