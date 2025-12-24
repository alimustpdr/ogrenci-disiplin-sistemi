<?php
/**
 * Helper Functions
 * Genel yardımcı fonksiyonlar
 */

/**
 * XSS koruması için HTML karakterlerini temizle
 */
function clean($data) {
    if (is_array($data)) {
        return array_map('clean', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * POST verisi kontrolü
 */
function post($key, $default = null) {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * GET verisi kontrolü
 */
function get($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Oturum başlat
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

/**
 * Oturum kontrolü
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) || isset($_SESSION['student_id']);
}

/**
 * Giriş yapan kullanıcının rolünü al
 */
function getUserRole() {
    startSession();
    return $_SESSION['role'] ?? null;
}

/**
 * Giriş yapan kullanıcının ID'sini al
 */
function getUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Giriş yapan öğrencinin ID'sini al
 */
function getStudentId() {
    startSession();
    return $_SESSION['student_id'] ?? null;
}

/**
 * Yönlendirme
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Flash mesaj ayarla
 */
function setFlash($type, $message) {
    startSession();
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Flash mesaj göster
 */
function getFlash() {
    startSession();
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * CSRF token oluştur
 */
function generateCsrfToken() {
    startSession();
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * CSRF token doğrula
 */
function verifyCsrfToken($token) {
    startSession();
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Rol kontrolü
 */
function hasRole($role) {
    return getUserRole() === $role;
}

/**
 * Yetki kontrolü - Admin mi?
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Yetki kontrolü - Öğretmen mi?
 */
function isTeacher() {
    return hasRole('teacher');
}

/**
 * Yetki kontrolü - Öğrenci mi?
 */
function isStudent() {
    return hasRole('student');
}

/**
 * Tarih formatla (Türkçe)
 */
function formatDate($date, $format = 'd.m.Y') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Tarih ve saat formatla (Türkçe)
 */
function formatDateTime($datetime, $format = 'd.m.Y H:i') {
    if (empty($datetime)) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Sayfalama için sayfa numarasını hesapla
 */
function paginate($totalRecords, $recordsPerPage, $currentPage = 1) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset
    ];
}

/**
 * Bootstrap alert class'ını belirle
 */
function getAlertClass($type) {
    $classes = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    return $classes[$type] ?? 'alert-info';
}

/**
 * Dosya dahil et
 */
function includeView($view, $data = []) {
    extract($data);
    include VIEWS_PATH . '/' . $view . '.php';
}

/**
 * Layout ile görünüm render et
 */
function render($view, $data = []) {
    extract($data);
    ob_start();
    include VIEWS_PATH . '/' . $view . '.php';
    $content = ob_get_clean();
    include VIEWS_PATH . '/layouts/main.php';
}

/**
 * JSON yanıt döndür
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Kayıt tipi rengini belirle
 */
function getRecordTypeBadge($type) {
    $badges = [
        'uyarı' => 'bg-warning',
        'kınama' => 'bg-danger',
        'teşekkür' => 'bg-success',
        'takdir' => 'bg-primary',
        'diğer' => 'bg-secondary'
    ];
    return $badges[$type] ?? 'bg-secondary';
}

/**
 * Şiddet seviyesi rengini belirle
 */
function getSeverityBadge($severity) {
    $badges = [
        'düşük' => 'bg-info',
        'orta' => 'bg-warning',
        'yüksek' => 'bg-danger'
    ];
    return $badges[$severity] ?? 'bg-secondary';
}
