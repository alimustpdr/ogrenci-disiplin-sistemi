<?php
/**
 * Öğrenci Disiplin Takip Sistemi - Yapılandırma Dosyası
 * 
 * Bu dosyayı config.php olarak kopyalayın ve kendi bilgilerinizi girin.
 */

// Veritabanı Ayarları
define('DB_HOST', 'localhost');          // Veritabanı sunucu adresi
define('DB_USER', 'root');                // Veritabanı kullanıcı adı
define('DB_PASS', '');                    // Veritabanı şifresi
define('DB_NAME', 'student_discipline');  // Veritabanı adı
define('DB_CHARSET', 'utf8mb4');

// Site Ayarları
define('SITE_NAME', 'Öğrenci Disiplin Takip Sistemi');
define('SITE_URL', 'http://localhost');   // Site URL'i (sonunda / olmadan)

// Güvenlik Ayarları
define('SESSION_NAME', 'SDTS_SESSION');
define('SESSION_LIFETIME', 3600);         // Oturum süresi (saniye cinsinden)
define('CSRF_TOKEN_NAME', 'csrf_token');

// Tarih ve Saat Ayarları
date_default_timezone_set('Europe/Istanbul');

// Hata Raporlama (Geliştirme ortamı için)
// Canlı ortamda bu ayarları kapatın!
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sayfalama
define('RECORDS_PER_PAGE', 10);

// Dosya Yolu Ayarları
define('ROOT_PATH', dirname(__FILE__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', SRC_PATH . '/views');
define('MODELS_PATH', SRC_PATH . '/models');
define('CONTROLLERS_PATH', SRC_PATH . '/controllers');
