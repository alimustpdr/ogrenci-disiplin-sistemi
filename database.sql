-- Öğrenci Disiplin Takip Sistemi - Veritabanı Şeması
-- MySQL / MariaDB

-- Veritabanı oluşturma (isteğe bağlı)
-- CREATE DATABASE IF NOT EXISTS student_discipline CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE student_discipline;

-- Kullanıcılar tablosu (Yönetici ve Öğretmenler)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'teacher') NOT NULL DEFAULT 'teacher',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Öğrenciler tablosu
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    class VARCHAR(50),
    birth_date DATE,
    parent_name VARCHAR(100),
    parent_phone VARCHAR(20),
    parent_email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_student_number (student_number),
    INDEX idx_full_name (full_name),
    INDEX idx_class (class)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Disiplin kayıtları tablosu
CREATE TABLE IF NOT EXISTS discipline_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL,
    record_type ENUM('uyarı', 'kınama', 'teşekkür', 'takdir', 'diğer') NOT NULL DEFAULT 'uyarı',
    record_date DATE NOT NULL,
    description TEXT NOT NULL,
    action_taken TEXT,
    severity ENUM('düşük', 'orta', 'yüksek') DEFAULT 'orta',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_record_date (record_date),
    INDEX idx_record_type (record_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Oturum tablosu (güvenlik için)
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    student_id INT,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session_token (session_token),
    INDEX idx_user_id (user_id),
    INDEX idx_student_id (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan yönetici kullanıcısı ekleme
-- Kullanıcı adı: admin
-- Şifre: admin123 (Lütfen ilk girişten sonra değiştirin!)
INSERT INTO users (username, password, full_name, email, role, is_active) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistem Yöneticisi', 'admin@example.com', 'admin', 1);

-- Demo öğretmen kullanıcısı (isteğe bağlı)
-- Kullanıcı adı: ogretmen
-- Şifre: ogretmen123
INSERT INTO users (username, password, full_name, email, role, is_active) 
VALUES ('ogretmen', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo Öğretmen', 'teacher@example.com', 'teacher', 1);

-- Demo öğrenci (isteğe bağlı)
-- Öğrenci No: 2024001
-- Şifre: ogrenci123
INSERT INTO students (student_number, password, full_name, email, class, birth_date, parent_name, parent_phone, is_active) 
VALUES ('2024001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo Öğrenci', 'student@example.com', '9-A', '2008-01-15', 'Demo Veli', '05551234567', 1);
