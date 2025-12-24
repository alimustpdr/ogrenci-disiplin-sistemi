<?php
/**
 * User Model
 * Kullanıcı (Yönetici ve Öğretmen) yönetimi için model sınıfı
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Kullanıcı girişi
     */
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1";
        $user = $this->db->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * ID'ye göre kullanıcı getir
     */
    public function getById($id) {
        $sql = "SELECT id, username, full_name, email, role, created_at, is_active 
                FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Tüm kullanıcıları getir
     */
    public function getAll($role = null) {
        if ($role) {
            $sql = "SELECT id, username, full_name, email, role, created_at, is_active 
                    FROM users WHERE role = ? ORDER BY full_name ASC";
            return $this->db->fetchAll($sql, [$role]);
        } else {
            $sql = "SELECT id, username, full_name, email, role, created_at, is_active 
                    FROM users ORDER BY full_name ASC";
            return $this->db->fetchAll();
        }
    }
    
    /**
     * Kullanıcı ara
     */
    public function search($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $sql = "SELECT id, username, full_name, email, role, created_at, is_active 
                FROM users 
                WHERE full_name LIKE ? OR username LIKE ? OR email LIKE ?
                ORDER BY full_name ASC";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Yeni kullanıcı oluştur
     */
    public function create($data) {
        // Kullanıcı adı kontrolü
        if ($this->usernameExists($data['username'])) {
            return ['success' => false, 'message' => 'Bu kullanıcı adı zaten kullanılıyor.'];
        }
        
        // Email kontrolü
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Bu e-posta adresi zaten kullanılıyor.'];
        }
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, full_name, email, role, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->insert($sql, [
            $data['username'],
            $hashedPassword,
            $data['full_name'],
            $data['email'],
            $data['role'],
            isset($data['is_active']) ? $data['is_active'] : 1
        ]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Kullanıcı başarıyla oluşturuldu.', 'id' => $result];
        }
        
        return ['success' => false, 'message' => 'Kullanıcı oluşturulurken bir hata oluştu.'];
    }
    
    /**
     * Kullanıcı güncelle
     */
    public function update($id, $data) {
        // Mevcut kullanıcıyı al
        $currentUser = $this->getById($id);
        if (!$currentUser) {
            return ['success' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Kullanıcı adı değiştiyse kontrol et
        if ($data['username'] != $currentUser['username'] && $this->usernameExists($data['username'])) {
            return ['success' => false, 'message' => 'Bu kullanıcı adı zaten kullanılıyor.'];
        }
        
        // Email değiştiyse kontrol et
        if ($data['email'] != $currentUser['email'] && $this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Bu e-posta adresi zaten kullanılıyor.'];
        }
        
        // Şifre güncelleme
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, role = ?, is_active = ? 
                    WHERE id = ?";
            $params = [
                $data['username'],
                $hashedPassword,
                $data['full_name'],
                $data['email'],
                $data['role'],
                isset($data['is_active']) ? $data['is_active'] : 1,
                $id
            ];
        } else {
            $sql = "UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, is_active = ? 
                    WHERE id = ?";
            $params = [
                $data['username'],
                $data['full_name'],
                $data['email'],
                $data['role'],
                isset($data['is_active']) ? $data['is_active'] : 1,
                $id
            ];
        }
        
        $result = $this->db->execute($sql, $params);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Kullanıcı başarıyla güncellendi.'];
        }
        
        return ['success' => false, 'message' => 'Kullanıcı güncellenirken bir hata oluştu.'];
    }
    
    /**
     * Kullanıcı sil
     */
    public function delete($id) {
        // Admin kullanıcıyı kontrol et (ID 1 olan admin silinemez)
        if ($id == 1) {
            return ['success' => false, 'message' => 'Varsayılan yönetici kullanıcısı silinemez.'];
        }
        
        $sql = "DELETE FROM users WHERE id = ?";
        $result = $this->db->execute($sql, [$id]);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Kullanıcı başarıyla silindi.'];
        }
        
        return ['success' => false, 'message' => 'Kullanıcı silinirken bir hata oluştu.'];
    }
    
    /**
     * Kullanıcı adı kontrolü
     */
    private function usernameExists($username) {
        $sql = "SELECT id FROM users WHERE username = ?";
        $result = $this->db->fetchOne($sql, [$username]);
        return $result ? true : false;
    }
    
    /**
     * Email kontrolü
     */
    private function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $result = $this->db->fetchOne($sql, [$email]);
        return $result ? true : false;
    }
    
    /**
     * Öğretmen sayısı
     */
    public function getTeacherCount() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'teacher'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Aktif kullanıcı sayısı
     */
    public function getActiveUserCount() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE is_active = 1";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['count'] : 0;
    }
}
