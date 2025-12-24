<?php
/**
 * Dashboard Page
 */

$pageTitle = 'Anasayfa';

// Modelleri oluştur
$userModel = new User();
$studentModel = new Student();
$recordModel = new DisciplineRecord();

// İstatistikleri al
if (isAdmin()) {
    $totalStudents = $studentModel->getTotalCount();
    $totalTeachers = $userModel->getTeacherCount();
    $totalRecords = $recordModel->getTotalCount();
    $recentRecords = $recordModel->getRecent(10);
    
    $recordsByType = $recordModel->getCountByType();
    $typeCounts = [];
    foreach ($recordsByType as $typeCount) {
        $typeCounts[$typeCount['record_type']] = $typeCount['count'];
    }
    
} elseif (isTeacher()) {
    $teacherId = getUserId();
    $myRecords = $recordModel->getByTeacherId($teacherId);
    $totalMyRecords = count($myRecords);
    $recentRecords = array_slice($myRecords, 0, 10);
    
} elseif (isStudent()) {
    $studentId = getStudentId();
    $student = $studentModel->getById($studentId);
    $myRecords = $recordModel->getByStudentId($studentId);
    $totalMyRecords = count($myRecords);
    
    // Kayıt tipine göre say
    $typeCounts = [];
    foreach ($myRecords as $record) {
        if (!isset($typeCounts[$record['record_type']])) {
            $typeCounts[$record['record_type']] = 0;
        }
        $typeCounts[$record['record_type']]++;
    }
}

ob_start();
?>

<div class="container-fluid">
    <h2 class="mb-4">
        <i class="bi bi-speedometer2 me-2"></i>
        Hoş Geldiniz, <?php echo clean($_SESSION['full_name']); ?>
    </h2>
    
    <?php if (isAdmin()): ?>
        <!-- Admin Dashboard -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Toplam Öğrenci</h6>
                                <h2 class="mb-0"><?php echo $totalStudents; ?></h2>
                            </div>
                            <div class="stat-icon text-primary">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Toplam Öğretmen</h6>
                                <h2 class="mb-0"><?php echo $totalTeachers; ?></h2>
                            </div>
                            <div class="stat-icon text-success">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Toplam Kayıt</h6>
                                <h2 class="mb-0"><?php echo $totalRecords; ?></h2>
                            </div>
                            <div class="stat-icon text-warning">
                                <i class="bi bi-journal-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Record Types -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-pie-chart me-2"></i>Kayıt Tipi Dağılımı
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <?php
                            $recordTypes = ['uyarı', 'kınama', 'teşekkür', 'takdir', 'diğer'];
                            foreach ($recordTypes as $type):
                                $count = $typeCounts[$type] ?? 0;
                            ?>
                                <div class="col">
                                    <span class="badge <?php echo getRecordTypeBadge($type); ?> fs-6 mb-2"><?php echo ucfirst($type); ?></span>
                                    <h3 class="mb-0"><?php echo $count; ?></h3>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Records -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history me-2"></i>Son Eklenen Kayıtlar</span>
                        <a href="index.php?page=records" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentRecords)): ?>
                            <p class="text-muted text-center mb-0">Henüz kayıt bulunmuyor.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Öğrenci</th>
                                            <th>Sınıf</th>
                                            <th>Tip</th>
                                            <th>Açıklama</th>
                                            <th>Öğretmen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentRecords as $record): ?>
                                            <tr>
                                                <td><?php echo formatDate($record['record_date']); ?></td>
                                                <td><?php echo clean($record['student_name']); ?></td>
                                                <td><span class="badge bg-info"><?php echo clean($record['class']); ?></span></td>
                                                <td><span class="badge <?php echo getRecordTypeBadge($record['record_type']); ?>"><?php echo clean($record['record_type']); ?></span></td>
                                                <td><?php echo clean(substr($record['description'], 0, 50)) . '...'; ?></td>
                                                <td><?php echo clean($record['teacher_name']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif (isTeacher()): ?>
        <!-- Teacher Dashboard -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Eklediğim Kayıtlar</h6>
                                <h2 class="mb-0"><?php echo $totalMyRecords; ?></h2>
                            </div>
                            <div class="stat-icon text-primary">
                                <i class="bi bi-journal-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <a href="index.php?page=records&action=create" class="btn btn-lg btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Yeni Disiplin Kaydı Ekle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Records -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history me-2"></i>Son Eklediğim Kayıtlar</span>
                        <a href="index.php?page=records" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentRecords)): ?>
                            <p class="text-muted text-center mb-0">Henüz kayıt eklemediniz.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Öğrenci</th>
                                            <th>Sınıf</th>
                                            <th>Tip</th>
                                            <th>Açıklama</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentRecords as $record): ?>
                                            <tr>
                                                <td><?php echo formatDate($record['record_date']); ?></td>
                                                <td><?php echo clean($record['student_name']); ?></td>
                                                <td><span class="badge bg-info"><?php echo clean($record['class']); ?></span></td>
                                                <td><span class="badge <?php echo getRecordTypeBadge($record['record_type']); ?>"><?php echo clean($record['record_type']); ?></span></td>
                                                <td><?php echo clean(substr($record['description'], 0, 50)) . '...'; ?></td>
                                                <td>
                                                    <a href="index.php?page=records&action=edit&id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif (isStudent()): ?>
        <!-- Student Dashboard -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Öğrenci No</h6>
                        <h4><?php echo clean($student['student_number']); ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Sınıf</h6>
                        <h4><?php echo clean($student['class'] ?? '-'); ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Toplam Kayıt</h6>
                        <h4><?php echo $totalMyRecords; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Record Types -->
        <?php if (!empty($typeCounts)): ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-pie-chart me-2"></i>Kayıt Tipi Dağılımı
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <?php foreach ($typeCounts as $type => $count): ?>
                                    <div class="col">
                                        <span class="badge <?php echo getRecordTypeBadge($type); ?> fs-6 mb-2"><?php echo ucfirst($type); ?></span>
                                        <h3 class="mb-0"><?php echo $count; ?></h3>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- My Records -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-journal-text me-2"></i>Disiplin Kayıtlarım</span>
                        <a href="index.php?page=records" class="btn btn-sm btn-outline-primary">Detaylı Görüntüle</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($myRecords)): ?>
                            <div class="alert alert-success text-center mb-0">
                                <i class="bi bi-check-circle fs-3 d-block mb-2"></i>
                                <h5>Harika!</h5>
                                <p class="mb-0">Henüz hiçbir disiplin kaydınız bulunmamaktadır.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Tip</th>
                                            <th>Şiddet</th>
                                            <th>Açıklama</th>
                                            <th>Öğretmen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($myRecords as $record): ?>
                                            <tr>
                                                <td><?php echo formatDate($record['record_date']); ?></td>
                                                <td><span class="badge <?php echo getRecordTypeBadge($record['record_type']); ?>"><?php echo clean($record['record_type']); ?></span></td>
                                                <td><span class="badge <?php echo getSeverityBadge($record['severity']); ?>"><?php echo clean($record['severity']); ?></span></td>
                                                <td><?php echo clean($record['description']); ?></td>
                                                <td><?php echo clean($record['teacher_name']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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
