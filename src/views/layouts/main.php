<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? clean($pageTitle) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            width: 250px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 0.5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .navbar-brand {
            padding: 0.5rem 1rem;
            color: white !important;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .top-navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 1030;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        main {
            margin-left: 250px;
            padding-top: 70px;
        }
        
        .content-wrapper {
            padding: 20px;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            padding: 15px 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            
            .top-navbar {
                left: 0;
            }
            
            main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-sticky">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock text-white" style="font-size: 3rem;"></i>
                <h5 class="text-white mt-2"><?php echo clean($_SESSION['full_name'] ?? 'Kullanıcı'); ?></h5>
                <span class="badge bg-light text-dark">
                    <?php 
                    if (isAdmin()) echo 'Yönetici';
                    elseif (isTeacher()) echo 'Öğretmen';
                    elseif (isStudent()) echo 'Öğrenci';
                    ?>
                </span>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                        <i class="bi bi-speedometer2"></i>Anasayfa
                    </a>
                </li>
                
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'students' ? 'active' : ''; ?>" href="index.php?page=students">
                            <i class="bi bi-people"></i>Öğrenciler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'teachers' ? 'active' : ''; ?>" href="index.php?page=teachers">
                            <i class="bi bi-person-badge"></i>Öğretmenler
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'records' ? 'active' : ''; ?>" href="index.php?page=records">
                        <i class="bi bi-journal-text"></i>Disiplin Kayıtları
                    </a>
                </li>
                
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>" href="index.php?page=reports">
                            <i class="bi bi-graph-up"></i>Raporlar
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'profile' ? 'active' : ''; ?>" href="index.php?page=profile">
                        <i class="bi bi-person-circle"></i>Profil
                    </a>
                </li>
                
                <li class="nav-item mt-4">
                    <a class="nav-link" href="index.php?page=logout">
                        <i class="bi bi-box-arrow-right"></i>Çıkış Yap
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Top Navbar -->
    <nav class="navbar top-navbar">
        <div class="container-fluid">
            <span class="navbar-brand"><?php echo SITE_NAME; ?></span>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="bi bi-calendar3"></i> <?php echo formatDate(date('Y-m-d')); ?>
                </span>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        <div class="content-wrapper">
            <?php
            // Flash mesajları göster
            $flash = getFlash();
            if ($flash):
            ?>
                <div class="alert <?php echo getAlertClass($flash['type']); ?> alert-dismissible fade show" role="alert">
                    <?php echo clean($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php echo $content ?? ''; ?>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirm delete
        function confirmDelete(message) {
            return confirm(message || 'Bu kaydı silmek istediğinizden emin misiniz?');
        }
    </script>
</body>
</html>
