<?php
/**
 * Student Management (Admin Only)
 */

if (!isAdmin()) {
    redirect('index.php?page=dashboard');
}

$pageTitle = 'Öğrenci Yönetimi';
$studentModel = new Student();
$action = get('action', 'list');
$studentId = get('id');

// Handle delete
if ($action === 'delete' && $studentId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = $studentModel->delete($studentId);
        setFlash($result['success'] ? 'success' : 'error', $result['message']);
        redirect('index.php?page=students');
    }
}

// Handle create/edit
if (($action === 'create' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'student_number' => post('student_number'),
        'full_name' => post('full_name'),
        'email' => post('email'),
        'class' => post('class'),
        'birth_date' => post('birth_date'),
        'parent_name' => post('parent_name'),
        'parent_phone' => post('parent_phone'),
        'parent_email' => post('parent_email'),
        'address' => post('address'),
        'is_active' => post('is_active', 1)
    ];
    
    if ($action === 'create') {
        $data['password'] = post('password');
        $result = $studentModel->create($data);
    } else {
        if (!empty(post('password'))) {
            $data['password'] = post('password');
        }
        $result = $studentModel->update($studentId, $data);
    }
    
    setFlash($result['success'] ? 'success' : 'error', $result['message']);
    
    if ($result['success']) {
        redirect('index.php?page=students');
    }
}

// Get student for edit
$student = null;
if ($action === 'edit' && $studentId) {
    $student = $studentModel->getById($studentId);
    if (!$student) {
        setFlash('error', 'Öğrenci bulunamadı.');
        redirect('index.php?page=students');
    }
}

// Search
$search = get('search');
if ($search) {
    $students = $studentModel->search($search);
} else {
    $students = $studentModel->getAll();
}

ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people me-2"></i><?php echo $pageTitle; ?></h2>
        <?php if ($action === 'list'): ?>
            <a href="index.php?page=students&action=create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Yeni Öğrenci Ekle
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($action === 'list'): ?>
        <!-- Student List -->
        <div class="card">
            <div class="card-header">
                <form method="GET" action="" class="row g-3">
                    <input type="hidden" name="page" value="students">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="search" placeholder="Öğrenci adı, numarası, sınıf veya e-posta ile ara..." value="<?php echo clean($search ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Ara
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <p class="text-muted text-center mb-0">Öğrenci bulunamadı.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Öğrenci No</th>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th>Sınıf</th>
                                    <th>Veli</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td><strong><?php echo clean($s['student_number']); ?></strong></td>
                                        <td><?php echo clean($s['full_name']); ?></td>
                                        <td><?php echo clean($s['email'] ?? '-'); ?></td>
                                        <td><span class="badge bg-info"><?php echo clean($s['class'] ?? '-'); ?></span></td>
                                        <td><?php echo clean($s['parent_name'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($s['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?page=students&action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="index.php?page=students&action=delete&id=<?php echo $s['id']; ?>" style="display:inline;" onsubmit="return confirmDelete();">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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
        <!-- Student Form -->
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <?php echo $action === 'create' ? 'Yeni Öğrenci Ekle' : 'Öğrenci Düzenle'; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_number" class="form-label">Öğrenci Numarası *</label>
                                    <input type="text" class="form-control" id="student_number" name="student_number" 
                                           value="<?php echo clean($student['student_number'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Ad Soyad *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo clean($student['full_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-posta</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo clean($student['email'] ?? ''); ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Şifre <?php echo $action === 'create' ? '*' : '(Değiştirmek için doldurun)'; ?>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?php echo $action === 'create' ? 'required' : ''; ?>>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class" class="form-label">Sınıf</label>
                                    <input type="text" class="form-control" id="class" name="class" 
                                           value="<?php echo clean($student['class'] ?? ''); ?>" placeholder="Örn: 9-A">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label">Doğum Tarihi</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                           value="<?php echo clean($student['birth_date'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="parent_name" class="form-label">Veli Adı</label>
                                    <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                           value="<?php echo clean($student['parent_name'] ?? ''); ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="parent_phone" class="form-label">Veli Telefon</label>
                                    <input type="text" class="form-control" id="parent_phone" name="parent_phone" 
                                           value="<?php echo clean($student['parent_phone'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="parent_email" class="form-label">Veli E-posta</label>
                                <input type="email" class="form-control" id="parent_email" name="parent_email" 
                                       value="<?php echo clean($student['parent_email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Adres</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo clean($student['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           <?php echo (!isset($student) || $student['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="index.php?page=students" class="btn btn-secondary">
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
