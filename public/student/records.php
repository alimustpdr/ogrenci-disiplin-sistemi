<?php
/**
 * Student's Discipline Records View
 */

if (!isStudent()) {
    redirect('index.php?page=dashboard');
}

$pageTitle = 'Disiplin Kayıtlarım';
$recordModel = new DisciplineRecord();
$studentId = getStudentId();

// Get student's records
$records = $recordModel->getByStudentId($studentId);

ob_start();
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-journal-text me-2"></i><?php echo $pageTitle; ?></h2>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($records)): ?>
                <div class="alert alert-success text-center mb-0">
                    <i class="bi bi-check-circle fs-1 d-block mb-3"></i>
                    <h4>Harika!</h4>
                    <p class="mb-0">Henüz hiçbir disiplin kaydınız bulunmamaktadır. Böyle devam edin!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Kayıt Tipi</th>
                                <th>Şiddet Seviyesi</th>
                                <th>Açıklama</th>
                                <th>Alınan Önlem</th>
                                <th>Öğretmen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td><strong><?php echo formatDate($r['record_date']); ?></strong></td>
                                    <td>
                                        <span class="badge <?php echo getRecordTypeBadge($r['record_type']); ?> fs-6">
                                            <?php echo clean($r['record_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo getSeverityBadge($r['severity']); ?>">
                                            <?php echo clean($r['severity']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo clean($r['description']); ?></td>
                                    <td><?php echo clean($r['action_taken'] ?? '-'); ?></td>
                                    <td><?php echo clean($r['teacher_name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Not:</strong> Disiplin kayıtlarınız hakkında sorularınız varsa, lütfen öğretmenleriniz veya okul yönetimiyle iletişime geçin.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>
