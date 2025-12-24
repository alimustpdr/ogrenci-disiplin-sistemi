<?php
/**
 * Öğrenci Disiplin Takip Sistemi
 * Ana Giriş Dosyası
 */

// Config dosyasını yükle
require_once __DIR__ . '/config/config.php';

// Helper fonksiyonları yükle
require_once SRC_PATH . '/helpers.php';

// Model sınıflarını yükle
require_once MODELS_PATH . '/Database.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Student.php';
require_once MODELS_PATH . '/DisciplineRecord.php';

// Oturumu başlat
startSession();

// Basit routing sistemi
$page = get('page', 'login');
$action = get('action', 'index');

// Giriş yapılmamışsa login sayfasına yönlendir
if (!isLoggedIn() && $page !== 'login' && $page !== 'logout') {
    redirect('index.php?page=login');
}

// Logout işlemi
if ($page === 'logout') {
    session_destroy();
    redirect('index.php?page=login');
}

// Sayfaları yükle
switch ($page) {
    case 'login':
        require_once PUBLIC_PATH . '/login.php';
        break;
        
    case 'dashboard':
        require_once PUBLIC_PATH . '/dashboard.php';
        break;
        
    // Admin modülleri
    case 'students':
        if (!isAdmin()) {
            setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            redirect('index.php?page=dashboard');
        }
        require_once PUBLIC_PATH . '/admin/students.php';
        break;
        
    case 'teachers':
        if (!isAdmin()) {
            setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            redirect('index.php?page=dashboard');
        }
        require_once PUBLIC_PATH . '/admin/teachers.php';
        break;
        
    // Disiplin kayıtları
    case 'records':
        if (isStudent()) {
            // Öğrenciler sadece kendi kayıtlarını görebilir
            require_once PUBLIC_PATH . '/student/records.php';
        } else {
            require_once PUBLIC_PATH . '/records.php';
        }
        break;
        
    // Raporlama
    case 'reports':
        if (!isAdmin()) {
            setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            redirect('index.php?page=dashboard');
        }
        require_once PUBLIC_PATH . '/admin/reports.php';
        break;
        
    // Profil
    case 'profile':
        require_once PUBLIC_PATH . '/profile.php';
        break;
        
    default:
        redirect('index.php?page=dashboard');
        break;
}
