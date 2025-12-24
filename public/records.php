<?php
/**
 * Discipline Records Management
 * For Admin and Teacher
 */

if (isStudent()) {
    redirect('index.php?page=dashboard');
}

$pageTitle = 'Disiplin Kayıtları';
$recordModel = new DisciplineRecord();
$studentModel = new Student();
$action = get('action', 'list');
$recordId = get('id');

// Handle delete
if ($action === 'delete' && $recordId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $record = $recordModel->getById($recordId);
        
        // Check permissions
        if (!$record) {
            setFlash('error', 'Kayıt bulunamadı.');
            redirect('index.php?page=records');
        }
        
        // Teacher can only delete their own records
        if (isTeacher() && $record['teacher_id'] != getUserId()) {
            setFlash('error', 'Bu kaydı silme yetkiniz yok.');
            redirect('index.php?page=records');
        }
        
        $result = $recordModel->delete($recordId);
        setFlash($result['success'] ? 'success' : 'error', $result['message']);
        redirect('index.php?page=records');
    }
}

// Handle create/edit
if (($action === 'create' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'student_id' => post('student_id'),
        'teacher_id' => isTeacher() ? getUserId() : post('teacher_id'),
        'record_type' => post('record_type'),
        'record_date' => post('record_date'),
        'description' => post('description'),
        'action_taken' => post('action_taken'),
        'severity' => post('severity')
    ];
    
    if ($action === 'create') {
        $result = $recordModel->create($data);
    } else {
        $record = $recordModel->getById($recordId);
        
        // Check permissions
        if (!$record) {
            setFlash('error', 'Kayıt bulunamadı.');
            redirect('index.php?page=records');
        }
        
        // Teacher can only edit their own records
        if (isTeacher() && $record['teacher_id'] != getUserId()) {
            setFlash('error', 'Bu kaydı düzenleme yetkiniz yok.');
            redirect('index.php?page=records');
        }
        
        $result = $recordModel->update($recordId, $data);
    }
    
    setFlash($result['success'] ? 'success' : 'error', $result['message']);
    
    if ($result['success']) {
        redirect('index.php?page=records');
    }
}

// Get record for edit
$record = null;
if ($action === 'edit' && $recordId) {
    $record = $recordModel->getById($recordId);
    if (!$record) {
        setFlash('error', 'Kayıt bulunamadı.');
        redirect('index.php?page=records');
    }
    
    // Check permissions
    if (isTeacher() && $record['teacher_id'] != getUserId()) {
        setFlash('error', 'Bu kaydı düzenleme yetkiniz yok.');
        redirect('index.php?page=records');
    }
}

// Get all students for dropdown
$allStudents = $studentModel->getAll();

// Filter and search
$filters = [];
if (isTeacher()) {
    // Teachers see all records but can only edit/delete their own
    $records = $recordModel->getAll();
} else {
    $search = get('search');
    $filterStudent = get('filter_student');
    $filterType = get('filter_type');
    $filterStartDate = get('filter_start_date');
    $filterEndDate = get('filter_end_date');
    
    if ($search) {
        $records = $recordModel->search($search);
    } elseif ($filterStudent || $filterType || $filterStartDate || $filterEndDate) {
        if ($filterStudent) $filters['student_id'] = $filterStudent;
        if ($filterType) $filters['record_type'] = $filterType;
        if ($filterStartDate) $filters['start_date'] = $filterStartDate;
        if ($filterEndDate) $filters['end_date'] = $filterEndDate;
        $records = $recordModel->getFiltered($filters);
    } else {
        $records = $recordModel->getAll();
    }
}

ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-journal-text me-2"></i><?php echo $pageTitle; ?></h2>
        <?php if ($action === 'list'): ?>
            <a href="index.php?page=records&action=create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Yeni Kayıt Ekle
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($action === 'list'): ?>
        <!-- Records List -->
        <div class="card mb-3">
            <div class="card-header">
                <form method="GET" action="" class="row g-3">
                    <input type="hidden" name="page" value="records">
                    
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Öğrenci adı veya açıklama..." value="<?php echo clean(get('search') ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <select class="form-select" name="filter_student">
                            <option value="">Tüm Öğrenciler</option>
                            <?php foreach ($allStudents as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo get('filter_student') == $s['id'] ? 'selected' : ''; ?>>
                                    <?php echo clean($s['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select class="form-select" name="filter_type">
                            <option value="">Tüm Tipler</option>
                            <option value="uyarı" <?php echo get('filter_type') === 'uyarı' ? 'selected' : ''; ?>>Uyarı</option>
                            <option value="kınama" <?php echo get('filter_type') === 'kınama' ? 'selected' : ''; ?>>Kınama</option>
                            <option value="teşekkür" <?php echo get('filter_type') === 'teşekkür' ? 'selected' : ''; ?>>Teşekkür</option>
                            <option value="takdir" <?php echo get('filter_type') === 'takdir' ? 'selected' : ''; ?>>Takdir</option>
                            <option value="diğer" <?php echo get('filter_type') === 'diğer' ? 'selected' : ''; ?>>Diğer</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="filter_start_date" placeholder="Başlangıç" value="<?php echo clean(get('filter_start_date') ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="filter_end_date" placeholder="Bitiş" value="<?php echo clean(get('filter_end_date') ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (empty($records)): ?>
                    <p class="text-muted text-center mb-0">Kayıt bulunamadı.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Öğrenci</th>
                                    <th>Sınıf</th>
                                    <th>Tip</th>
                                    <th>Şiddet</th>
                                    <th>Açıklama</th>
                                    <th>Öğretmen</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $r): ?>
                                    <tr>
                                        <td><?php echo formatDate($r['record_date']); ?></td>
                                        <td><?php echo clean($r['student_name']); ?></td>
                                        <td><span class="badge bg-info"><?php echo clean($r['class']); ?></span></td>
                                        <td><span class="badge <?php echo getRecordTypeBadge($r['record_type']); ?>"><?php echo clean($r['record_type']); ?></span></td>
                                        <td><span class="badge <?php echo getSeverityBadge($r['severity']); ?>"><?php echo clean($r['severity']); ?></span></td>
                                        <td><?php echo clean(substr($r['description'], 0, 60)) . (strlen($r['description']) > 60 ? '...' : ''); ?></td>
                                        <td><?php echo clean($r['teacher_name']); ?></td>
                                        <td>
                                            <?php if (isAdmin() || (isTeacher() && $r['teacher_id'] == getUserId())): ?>
                                                <a href="index.php?page=records&action=edit&id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="index.php?page=records&action=delete&id=<?php echo $r['id']; ?>" style="display:inline;" onsubmit="return confirmDelete();">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
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
        <!-- Record Form -->
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <?php echo $action === 'create' ? 'Yeni Disiplin Kaydı' : 'Disiplin Kaydı Düzenle'; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Öğrenci *</label>
                                    <select class="form-select" id="student_id" name="student_id" required>
                                        <option value="">Öğrenci Seçin</option>
                                        <?php foreach ($allStudents as $s): ?>
                                            <option value="<?php echo $s['id']; ?>" <?php echo (isset($record) && $record['student_id'] == $s['id']) ? 'selected' : ''; ?>>
                                                <?php echo clean($s['full_name']) . ' (' . clean($s['student_number']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="record_date" class="form-label">Tarih *</label>
                                    <input type="date" class="form-control" id="record_date" name="record_date" 
                                           value="<?php echo clean($record['record_date'] ?? date('Y-m-d')); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="record_type" class="form-label">Kayıt Tipi *</label>
                                    <select class="form-select" id="record_type" name="record_type" required>
                                        <option value="uyarı" <?php echo (isset($record) && $record['record_type'] === 'uyarı') ? 'selected' : ''; ?>>Uyarı</option>
                                        <option value="kınama" <?php echo (isset($record) && $record['record_type'] === 'kınama') ? 'selected' : ''; ?>>Kınama</option>
                                        <option value="teşekkür" <?php echo (isset($record) && $record['record_type'] === 'teşekkür') ? 'selected' : ''; ?>>Teşekkür</option>
                                        <option value="takdir" <?php echo (isset($record) && $record['record_type'] === 'takdir') ? 'selected' : ''; ?>>Takdir</option>
                                        <option value="diğer" <?php echo (isset($record) && $record['record_type'] === 'diğer') ? 'selected' : ''; ?>>Diğer</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="severity" class="form-label">Şiddet Seviyesi *</label>
                                    <select class="form-select" id="severity" name="severity" required>
                                        <option value="düşük" <?php echo (isset($record) && $record['severity'] === 'düşük') ? 'selected' : ''; ?>>Düşük</option>
                                        <option value="orta" <?php echo (!isset($record) || $record['severity'] === 'orta') ? 'selected' : ''; ?>>Orta</option>
                                        <option value="yüksek" <?php echo (isset($record) && $record['severity'] === 'yüksek') ? 'selected' : ''; ?>>Yüksek</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Açıklama *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo clean($record['description'] ?? ''); ?></textarea>
                                <div class="form-text">Olayı detaylı bir şekilde açıklayın.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="action_taken" class="form-label">Alınan Önlem/Aksiyon</label>
                                <textarea class="form-control" id="action_taken" name="action_taken" rows="3"><?php echo clean($record['action_taken'] ?? ''); ?></textarea>
                                <div class="form-text">Bu olay sonucunda alınan önlemleri belirtin.</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="index.php?page=records" class="btn btn-secondary">
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
