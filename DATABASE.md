# ODTS Database Schema

## Overview
This document describes the database structure for ODTS (Öğrenci Disiplin Takip Sistemi).

Database Engine: MySQL 5.7+
Character Set: utf8mb4_unicode_ci (for Turkish character support)

## Tables

### 1. users
Stores user accounts (admin, teachers, vice principals)

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| username | VARCHAR(50) | Unique username |
| password | VARCHAR(255) | Hashed password (bcrypt) |
| full_name | VARCHAR(100) | User's full name |
| email | VARCHAR(100) | Email address |
| role | VARCHAR(20) | User role (admin, mudur_yardimcisi, ogretmen) |
| auth_token | VARCHAR(255) | Cookie authentication token |
| token_expire | INT | Token expiration timestamp |
| created_at | TIMESTAMP | Account creation date |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (username)

---

### 2. roles
Defines permissions for each role

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| role_name | VARCHAR(50) | Role name (unique) |
| can_manage_users | TINYINT(1) | Permission to manage users |
| can_manage_students | TINYINT(1) | Permission to manage students |
| can_manage_warnings | TINYINT(1) | Permission to manage warnings |
| can_manage_classes | TINYINT(1) | Permission to manage classes |
| can_view_reports | TINYINT(1) | Permission to view reports |
| can_manage_settings | TINYINT(1) | Permission to manage settings |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (role_name)

**Default Roles:**
- admin: All permissions enabled
- mudur_yardimcisi: All except user/settings management
- ogretmen: Student, warning, and report access only

---

### 3. students
Stores student information

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| student_no | VARCHAR(20) | Unique student number |
| first_name | VARCHAR(50) | First name |
| last_name | VARCHAR(50) | Last name |
| class_id | INT | Foreign key to classes table |
| birth_date | DATE | Birth date |
| parent_name | VARCHAR(100) | Parent/guardian name |
| parent_phone | VARCHAR(20) | Parent phone number |
| address | TEXT | Home address |
| created_at | TIMESTAMP | Record creation date |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (student_no)
- FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL

---

### 4. classes
Stores class information

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| class_name | VARCHAR(20) | Class name (e.g., 9-A, 10-B) |
| grade | INT | Grade level (9, 10, 11, 12) |
| section | VARCHAR(5) | Section (A, B, C, etc.) |
| teacher_id | INT | Homeroom teacher (FK to users) |
| created_at | TIMESTAMP | Record creation date |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (class_name)
- FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL

---

### 5. warnings
Stores disciplinary warning records

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| student_id | INT | Foreign key to students table |
| category_id | INT | Foreign key to warning_categories |
| level | INT | Warning level (1-5) |
| description | TEXT | Warning description |
| action_taken | TEXT | Action taken by staff |
| user_id | INT | User who created the warning |
| warning_date | DATE | Date of the warning |
| created_at | TIMESTAMP | Record creation date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
- FOREIGN KEY (category_id) REFERENCES warning_categories(id)
- FOREIGN KEY (user_id) REFERENCES users(id)

**Warning Levels:**
- Level 1: Minor offense (Hafif)
- Level 2: Moderate offense (Orta)
- Level 3: Serious offense (Ciddi)
- Level 4: Very serious offense (Çok Ciddi)
- Level 5: Critical offense (Kritik)

---

### 6. warning_categories
Defines warning categories

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| category_name | VARCHAR(50) | Category name (unique) |
| description | TEXT | Category description |
| created_at | TIMESTAMP | Record creation date |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (category_name)

**Default Categories:**
- Davranış (Behavior)
- Devamsızlık (Absence)
- Kıyafet (Dress code)
- Ders Düzeni (Class order)
- Diğer (Other)

---

### 7. settings
Stores system settings

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| setting_key | VARCHAR(100) | Setting key (unique) |
| value | TEXT | Setting value |
| created_at | TIMESTAMP | Record creation date |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (setting_key)

**Common Settings:**
- school_name: School name
- school_address: School address
- school_phone: Contact phone
- school_email: Contact email
- theme_color: UI theme color
- install_date: Installation date

---

### 8. activity_logs
Logs user activities for audit trail

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| user_id | INT | User who performed the action |
| action | VARCHAR(100) | Action type |
| description | TEXT | Action description |
| ip_address | VARCHAR(45) | IP address of user |
| created_at | TIMESTAMP | Action timestamp |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

**Common Actions:**
- Öğrenci Eklendi
- Öğrenci Güncellendi
- Öğrenci Silindi
- Uyarı Eklendi
- Uyarı Güncellendi
- Uyarı Silindi
- Sınıf Eklendi
- Kullanıcı Eklendi
- Ayarlar Güncellendi

---

## Relationships

```
users (1) ----< (∞) warnings
users (1) ----< (∞) classes
users (1) ----< (∞) activity_logs

classes (1) ----< (∞) students

students (1) ----< (∞) warnings

warning_categories (1) ----< (∞) warnings
```

## Installation

The database tables are automatically created during the installation wizard at `/install/index.php`.

The installation process:
1. Creates database if not exists
2. Creates all tables with proper charset (utf8mb4)
3. Inserts default roles
4. Inserts default warning categories
5. Creates first admin user

## Backup Recommendations

1. **Daily backups**: Export database daily
2. **Before updates**: Always backup before system updates
3. **User data**: Special attention to students and warnings tables
4. **Export format**: Use SQL format with DROP TABLE statements

## Performance Considerations

1. **Indexes**: All foreign keys are indexed
2. **Query optimization**: Use appropriate WHERE clauses
3. **Pagination**: Large datasets use LIMIT/OFFSET
4. **Archiving**: Consider archiving old warnings annually

## Security

1. **Password hashing**: Uses PHP password_hash() with bcrypt
2. **SQL injection**: All queries use real_escape_string()
3. **Cascading deletes**: Proper ON DELETE actions set
4. **Token expiration**: Auth tokens expire after 24 hours

---

**Database Version**: 1.0.0
**Last Updated**: 2025-12-24
