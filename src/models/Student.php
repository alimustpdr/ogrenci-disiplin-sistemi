<?php
/**
 * Student Model
 * Öğrenci yönetimi için model sınıfı
 */

class Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Öğrenci girişi
     */
    public function login($studentNumber, $password) {
        $sql = "SELECT * FROM students WHERE student_number = ? AND is_active = 1";
        $student = $this->db->fetchOne($sql, [$studentNumber]);
        
        if ($student && password_verify($password, $student['password'])) {
            return $student;
        }
        
        return false;
    }
    
    /**
     * ID'ye göre öğrenci getir
     */
    public function getById($id) {
        $sql = "SELECT * FROM students WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Öğrenci numarasına göre öğrenci getir
     */
    public function getByStudentNumber($studentNumber) {
        $sql = "SELECT * FROM students WHERE student_number = ?";
        return $this->db->fetchOne($sql, [$studentNumber]);
    }
    
    /**
     * Tüm öğrencileri getir
     */
    public function getAll($limit = null, $offset = 0) {
        if ($limit) {
            $sql = "SELECT * FROM students ORDER BY full_name ASC LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        } else {
            $sql = "SELECT * FROM students ORDER BY full_name ASC";
            return $this->db->fetchAll($sql);
        }
    }
    
    /**
     * Sınıfa göre öğrencileri getir
     */
    public function getByClass($class) {
        $sql = "SELECT * FROM students WHERE class = ? ORDER BY full_name ASC";
        return $this->db->fetchAll($sql, [$class]);
    }
    
    /**
     * Öğrenci ara
     */
    public function search($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $sql = "SELECT * FROM students 
                WHERE full_name LIKE ? OR student_number LIKE ? OR class LIKE ? OR email LIKE ?
                ORDER BY full_name ASC";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Yeni öğrenci oluştur
     */
    public function create($data) {
        // Öğrenci numarası kontrolü
        if ($this->studentNumberExists($data['student_number'])) {
            return ['success' => false, 'message' => 'Bu öğrenci numarası zaten kullanılıyor.'];
        }
        
        // Email kontrolü (eğer girilmişse)
        if (!empty($data['email']) && $this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Bu e-posta adresi zaten kullanılıyor.'];
        }
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO students (student_number, password, full_name, email, class, birth_date, 
                parent_name, parent_phone, parent_email, address, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->insert($sql, [
            $data['student_number'],
            $hashedPassword,
            $data['full_name'],
            $data['email'] ?? null,
            $data['class'] ?? null,
            $data['birth_date'] ?? null,
            $data['parent_name'] ?? null,
            $data['parent_phone'] ?? null,
            $data['parent_email'] ?? null,
            $data['address'] ?? null,
            isset($data['is_active']) ? $data['is_active'] : 1
        ]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Öğrenci başarıyla oluşturuldu.', 'id' => $result];
        }
        
        return ['success' => false, 'message' => 'Öğrenci oluşturulurken bir hata oluştu.'];
    }
    
    /**
     * Öğrenci güncelle
     */
    public function update($id, $data) {
        // Mevcut öğrenciyi al
        $currentStudent = $this->getById($id);
        if (!$currentStudent) {
            return ['success' => false, 'message' => 'Öğrenci bulunamadı.'];
        }
        
        // Öğrenci numarası değiştiyse kontrol et
        if ($data['student_number'] != $currentStudent['student_number'] && 
            $this->studentNumberExists($data['student_number'])) {
            return ['success' => false, 'message' => 'Bu öğrenci numarası zaten kullanılıyor.'];
        }
        
        // Email değiştiyse kontrol et
        if (!empty($data['email']) && $data['email'] != $currentStudent['email'] && 
            $this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Bu e-posta adresi zaten kullanılıyor.'];
        }
        
        // Şifre güncelleme
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE students SET student_number = ?, password = ?, full_name = ?, email = ?, 
                    class = ?, birth_date = ?, parent_name = ?, parent_phone = ?, parent_email = ?, 
                    address = ?, is_active = ? WHERE id = ?";
            $params = [
                $data['student_number'],
                $hashedPassword,
                $data['full_name'],
                $data['email'] ?? null,
                $data['class'] ?? null,
                $data['birth_date'] ?? null,
                $data['parent_name'] ?? null,
                $data['parent_phone'] ?? null,
                $data['parent_email'] ?? null,
                $data['address'] ?? null,
                isset($data['is_active']) ? $data['is_active'] : 1,
                $id
            ];
        } else {
            $sql = "UPDATE students SET student_number = ?, full_name = ?, email = ?, class = ?, 
                    birth_date = ?, parent_name = ?, parent_phone = ?, parent_email = ?, 
                    address = ?, is_active = ? WHERE id = ?";
            $params = [
                $data['student_number'],
                $data['full_name'],
                $data['email'] ?? null,
                $data['class'] ?? null,
                $data['birth_date'] ?? null,
                $data['parent_name'] ?? null,
                $data['parent_phone'] ?? null,
                $data['parent_email'] ?? null,
                $data['address'] ?? null,
                isset($data['is_active']) ? $data['is_active'] : 1,
                $id
            ];
        }
        
        $result = $this->db->execute($sql, $params);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Öğrenci başarıyla güncellendi.'];
        }
        
        return ['success' => false, 'message' => 'Öğrenci güncellenirken bir hata oluştu.'];
    }
    
    /**
     * Öğrenci sil
     */
    public function delete($id) {
        $sql = "DELETE FROM students WHERE id = ?";
        $result = $this->db->execute($sql, [$id]);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Öğrenci başarıyla silindi.'];
        }
        
        return ['success' => false, 'message' => 'Öğrenci silinirken bir hata oluştu.'];
    }
    
    /**
     * Öğrenci numarası kontrolü
     */
    private function studentNumberExists($studentNumber) {
        $sql = "SELECT id FROM students WHERE student_number = ?";
        $result = $this->db->fetchOne($sql, [$studentNumber]);
        return $result ? true : false;
    }
    
    /**
     * Email kontrolü
     */
    private function emailExists($email) {
        $sql = "SELECT id FROM students WHERE email = ?";
        $result = $this->db->fetchOne($sql, [$email]);
        return $result ? true : false;
    }
    
    /**
     * Toplam öğrenci sayısı
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM students";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Aktif öğrenci sayısı
     */
    public function getActiveCount() {
        $sql = "SELECT COUNT(*) as count FROM students WHERE is_active = 1";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Tüm sınıfları getir
     */
    public function getAllClasses() {
        $sql = "SELECT DISTINCT class FROM students WHERE class IS NOT NULL ORDER BY class ASC";
        return $this->db->fetchAll($sql);
    }
}
