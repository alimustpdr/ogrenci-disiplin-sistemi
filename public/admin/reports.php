<?php
/**
 * Reports Page (Admin Only)
 */

if (!isAdmin()) {
    redirect('index.php?page=dashboard');
}

$pageTitle = 'Raporlama';
$recordModel = new DisciplineRecord();
$studentModel = new Student();

$action = get('action', 'form');

// Get all students for dropdown
$allStudents = $studentModel->getAll();

// Generate report
$reportData = null;
$filters = [];
if ($action === 'generate') {
    $studentId = get('student_id');
    $startDate = get('start_date');
    $endDate = get('end_date');
    $recordType = get('record_type');
    
    if ($studentId) {
        $filters['student_id'] = $studentId;
        $student = $studentModel->getById($studentId);
    }
    if ($startDate) $filters['start_date'] = $startDate;
    if ($endDate) $filters['end_date'] = $endDate;
    if ($recordType) $filters['record_type'] = $recordType;
    
    $reportData = $recordModel->getFiltered($filters);
}

ob_start();
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-graph-up me-2"></i><?php echo $pageTitle; ?></h2>
    
    <?php if ($action === 'form' || $action === 'generate'): ?>
        <!-- Report Form -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-filter me-2"></i>Rapor Filtreleri
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <input type="hidden" name="page" value="reports">
                            <input type="hidden" name="action" value="generate">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="student_id" class="form-label">Öğrenci</label>
                                    <select class="form-select" id="student_id" name="student_id">
                                        <option value="">Tüm Öğrenciler</option>
                                        <?php foreach ($allStudents as $s): ?>
                                            <option value="<?php echo $s['id']; ?>" <?php echo get('student_id') == $s['id'] ? 'selected' : ''; ?>>
                                                <?php echo clean($s['full_name']) . ' (' . clean($s['student_number']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?php echo clean(get('start_date') ?? ''); ?>">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="end_date" class="form-label">Bitiş Tarihi</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?php echo clean(get('end_date') ?? ''); ?>">
                                </div>
                                
                                <div class="col-md-2 mb-3">
                                    <label for="record_type" class="form-label">Kayıt Tipi</label>
                                    <select class="form-select" id="record_type" name="record_type">
                                        <option value="">Tümü</option>
                                        <option value="uyarı" <?php echo get('record_type') === 'uyarı' ? 'selected' : ''; ?>>Uyarı</option>
                                        <option value="kınama" <?php echo get('record_type') === 'kınama' ? 'selected' : ''; ?>>Kınama</option>
                                        <option value="teşekkür" <?php echo get('record_type') === 'teşekkür' ? 'selected' : ''; ?>>Teşekkür</option>
                                        <option value="takdir" <?php echo get('record_type') === 'takdir' ? 'selected' : ''; ?>>Takdir</option>
                                        <option value="diğer" <?php echo get('record_type') === 'diğer' ? 'selected' : ''; ?>>Diğer</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Rapor Oluştur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($action === 'generate' && $reportData !== null): ?>
            <!-- Report Results -->
            <div class="card" id="printable-report">
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <span><i class="bi bi-file-earmark-text me-2"></i>Rapor Sonuçları</span>
                    <div>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-printer me-2"></i>Yazdır / PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Report Header -->
                    <div class="text-center mb-4 print-header">
                        <h3><?php echo SITE_NAME; ?></h3>
                        <h5>Disiplin Kayıtları Raporu</h5>
                        <p class="text-muted">
                            <?php if (isset($student)): ?>
                                Öğrenci: <strong><?php echo clean($student['full_name']); ?></strong> (<?php echo clean($student['student_number']); ?>)
                            <?php else: ?>
                                <strong>Tüm Öğrenciler</strong>
                            <?php endif; ?>
                            <br>
                            <?php if (get('start_date') || get('end_date')): ?>
                                Tarih Aralığı: 
                                <?php echo get('start_date') ? formatDate(get('start_date')) : 'Başlangıç'; ?> - 
                                <?php echo get('end_date') ? formatDate(get('end_date')) : 'Bitiş'; ?>
                            <?php endif; ?>
                            <br>
                            Rapor Tarihi: <?php echo formatDate(date('Y-m-d')); ?>
                        </p>
                    </div>
                    
                    <?php if (empty($reportData)): ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            Seçili kriterlere uygun kayıt bulunamadı.
                        </div>
                    <?php else: ?>
                        <!-- Summary Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Özet İstatistikler</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Toplam Kayıt Sayısı</th>
                                        <td><strong><?php echo count($reportData); ?></strong></td>
                                    </tr>
                                    <?php
                                    $typeCounts = [];
                                    foreach ($reportData as $r) {
                                        if (!isset($typeCounts[$r['record_type']])) {
                                            $typeCounts[$r['record_type']] = 0;
                                        }
                                        $typeCounts[$r['record_type']]++;
                                    }
                                    foreach ($typeCounts as $type => $count):
                                    ?>
                                        <tr>
                                            <th><?php echo ucfirst($type); ?></th>
                                            <td><?php echo $count; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Detailed Records -->
                        <div class="table-responsive">
                            <h5>Detaylı Kayıtlar</h5>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <?php if (!isset($student)): ?>
                                            <th>Öğrenci</th>
                                            <th>Sınıf</th>
                                        <?php endif; ?>
                                        <th>Tip</th>
                                        <th>Şiddet</th>
                                        <th>Açıklama</th>
                                        <th>Öğretmen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reportData as $r): ?>
                                        <tr>
                                            <td><?php echo formatDate($r['record_date']); ?></td>
                                            <?php if (!isset($student)): ?>
                                                <td><?php echo clean($r['student_name']); ?></td>
                                                <td><?php echo clean($r['class']); ?></td>
                                            <?php endif; ?>
                                            <td><span class="badge <?php echo getRecordTypeBadge($r['record_type']); ?>"><?php echo clean($r['record_type']); ?></span></td>
                                            <td><span class="badge <?php echo getSeverityBadge($r['severity']); ?>"><?php echo clean($r['severity']); ?></span></td>
                                            <td><?php echo clean($r['description']); ?></td>
                                            <td><?php echo clean($r['teacher_name']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-report, #printable-report * {
            visibility: visible;
        }
        #printable-report {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .sidebar, .top-navbar, .no-print {
            display: none !important;
        }
        main {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }
        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background-color: #fff !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>
