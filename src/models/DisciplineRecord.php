<?php
/**
 * DisciplineRecord Model
 * Disiplin kayıtları yönetimi için model sınıfı
 */

class DisciplineRecord {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * ID'ye göre disiplin kaydı getir
     */
    public function getById($id) {
        $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                u.full_name as teacher_name
                FROM discipline_records dr
                JOIN students s ON dr.student_id = s.id
                JOIN users u ON dr.teacher_id = u.id
                WHERE dr.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Tüm disiplin kayıtlarını getir
     */
    public function getAll($limit = null, $offset = 0) {
        if ($limit) {
            $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                    u.full_name as teacher_name
                    FROM discipline_records dr
                    JOIN students s ON dr.student_id = s.id
                    JOIN users u ON dr.teacher_id = u.id
                    ORDER BY dr.record_date DESC, dr.created_at DESC
                    LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        } else {
            $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                    u.full_name as teacher_name
                    FROM discipline_records dr
                    JOIN students s ON dr.student_id = s.id
                    JOIN users u ON dr.teacher_id = u.id
                    ORDER BY dr.record_date DESC, dr.created_at DESC";
            return $this->db->fetchAll($sql);
        }
    }
    
    /**
     * Öğrenciye göre disiplin kayıtlarını getir
     */
    public function getByStudentId($studentId, $limit = null, $offset = 0) {
        if ($limit) {
            $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                    u.full_name as teacher_name
                    FROM discipline_records dr
                    JOIN students s ON dr.student_id = s.id
                    JOIN users u ON dr.teacher_id = u.id
                    WHERE dr.student_id = ?
                    ORDER BY dr.record_date DESC, dr.created_at DESC
                    LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$studentId, $limit, $offset]);
        } else {
            $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                    u.full_name as teacher_name
                    FROM discipline_records dr
                    JOIN students s ON dr.student_id = s.id
                    JOIN users u ON dr.teacher_id = u.id
                    WHERE dr.student_id = ?
                    ORDER BY dr.record_date DESC, dr.created_at DESC";
            return $this->db->fetchAll($sql, [$studentId]);
        }
    }
    
    /**
     * Öğretmene göre disiplin kayıtlarını getir
     */
    public function getByTeacherId($teacherId) {
        $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                u.full_name as teacher_name
                FROM discipline_records dr
                JOIN students s ON dr.student_id = s.id
                JOIN users u ON dr.teacher_id = u.id
                WHERE dr.teacher_id = ?
                ORDER BY dr.record_date DESC, dr.created_at DESC";
        return $this->db->fetchAll($sql, [$teacherId]);
    }
    
    /**
     * Filtreleme ile kayıtları getir
     */
    public function getFiltered($filters = []) {
        $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                u.full_name as teacher_name
                FROM discipline_records dr
                JOIN students s ON dr.student_id = s.id
                JOIN users u ON dr.teacher_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['student_id'])) {
            $sql .= " AND dr.student_id = ?";
            $params[] = $filters['student_id'];
        }
        
        if (!empty($filters['teacher_id'])) {
            $sql .= " AND dr.teacher_id = ?";
            $params[] = $filters['teacher_id'];
        }
        
        if (!empty($filters['record_type'])) {
            $sql .= " AND dr.record_type = ?";
            $params[] = $filters['record_type'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND dr.record_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND dr.record_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['class'])) {
            $sql .= " AND s.class = ?";
            $params[] = $filters['class'];
        }
        
        if (!empty($filters['severity'])) {
            $sql .= " AND dr.severity = ?";
            $params[] = $filters['severity'];
        }
        
        $sql .= " ORDER BY dr.record_date DESC, dr.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
            
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET ?";
                $params[] = $filters['offset'];
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Arama yap
     */
    public function search($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                u.full_name as teacher_name
                FROM discipline_records dr
                JOIN students s ON dr.student_id = s.id
                JOIN users u ON dr.teacher_id = u.id
                WHERE s.full_name LIKE ? OR s.student_number LIKE ? OR dr.description LIKE ?
                ORDER BY dr.record_date DESC, dr.created_at DESC";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Yeni disiplin kaydı oluştur
     */
    public function create($data) {
        $sql = "INSERT INTO discipline_records (student_id, teacher_id, record_type, record_date, 
                description, action_taken, severity) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->insert($sql, [
            $data['student_id'],
            $data['teacher_id'],
            $data['record_type'],
            $data['record_date'],
            $data['description'],
            $data['action_taken'] ?? null,
            $data['severity'] ?? 'orta'
        ]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Disiplin kaydı başarıyla oluşturuldu.', 'id' => $result];
        }
        
        return ['success' => false, 'message' => 'Disiplin kaydı oluşturulurken bir hata oluştu.'];
    }
    
    /**
     * Disiplin kaydını güncelle
     */
    public function update($id, $data) {
        $sql = "UPDATE discipline_records 
                SET student_id = ?, record_type = ?, record_date = ?, description = ?, 
                    action_taken = ?, severity = ?
                WHERE id = ?";
        
        $result = $this->db->execute($sql, [
            $data['student_id'],
            $data['record_type'],
            $data['record_date'],
            $data['description'],
            $data['action_taken'] ?? null,
            $data['severity'] ?? 'orta',
            $id
        ]);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Disiplin kaydı başarıyla güncellendi.'];
        }
        
        return ['success' => false, 'message' => 'Disiplin kaydı güncellenirken bir hata oluştu.'];
    }
    
    /**
     * Disiplin kaydını sil
     */
    public function delete($id) {
        $sql = "DELETE FROM discipline_records WHERE id = ?";
        $result = $this->db->execute($sql, [$id]);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Disiplin kaydı başarıyla silindi.'];
        }
        
        return ['success' => false, 'message' => 'Disiplin kaydı silinirken bir hata oluştu.'];
    }
    
    /**
     * Toplam kayıt sayısı
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM discipline_records";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Öğrenciye ait kayıt sayısı
     */
    public function getCountByStudentId($studentId) {
        $sql = "SELECT COUNT(*) as count FROM discipline_records WHERE student_id = ?";
        $result = $this->db->fetchOne($sql, [$studentId]);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Kayıt tipine göre sayıları getir
     */
    public function getCountByType() {
        $sql = "SELECT record_type, COUNT(*) as count 
                FROM discipline_records 
                GROUP BY record_type";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Son eklenen kayıtları getir
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT dr.*, s.full_name as student_name, s.student_number, s.class,
                u.full_name as teacher_name
                FROM discipline_records dr
                JOIN students s ON dr.student_id = s.id
                JOIN users u ON dr.teacher_id = u.id
                ORDER BY dr.created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
}
