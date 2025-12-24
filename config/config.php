<?php
/**
 * Database Configuration File
 * 
 * This file contains database connection settings for the application.
 * Created: 2025-12-24 13:00:12
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ogrenci_disiplin_sistemi');
define('DB_CHARSET', 'utf8mb4');

// Optional: Database Port (default is 3306)
define('DB_PORT', 3306);

// Optional: Database Connection Timeout
define('DB_TIMEOUT', 10);

// Application Settings
define('APP_ENV', 'development'); // development or production
define('APP_DEBUG', true);

// Database Connection Error Handling
try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die('Database Connection Error: ' . $e->getMessage());
    } else {
        die('Database connection failed. Please contact administrator.');
    }
}
?>
