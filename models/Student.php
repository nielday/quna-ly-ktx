<?php
require_once '../config/database.php';
require_once '../config/logger.php';

class Student {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy danh sách sinh viên với phân trang
    public function getStudents($page = 1, $limit = RECORDS_PER_PAGE, $faculty = null, $gender = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];
            
            if ($faculty) {
                $whereConditions[] = "s.faculty = :faculty";
                $params[':faculty'] = $faculty;
            }
            
            if ($gender) {
                $whereConditions[] = "s.gender = :gender";
                $params[':gender'] = $gender;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT s.*, u.username, u.email, u.full_name, u.phone, u.is_active
                     FROM students s 
                     JOIN users u ON s.user_id = u.id 
                     $whereClause
                     ORDER BY s.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get students error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy thông tin sinh viên theo ID
    public function getStudentById($studentId) {
        try {
            $query = "SELECT s.*, u.username, u.email, u.full_name, u.phone, u.is_active
                     FROM students s 
                     JOIN users u ON s.user_id = u.id 
                     WHERE s.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $studentId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get student by ID error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy thông tin sinh viên theo user_id
    public function getStudentByUserId($userId) {
        try {
            $query = "SELECT s.*, u.username, u.email, u.full_name, u.phone, u.is_active
                     FROM students s 
                     JOIN users u ON s.user_id = u.id 
                     WHERE s.user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get student by user ID error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tạo sinh viên mới
    public function createStudent($data) {
        try {
            $this->conn->beginTransaction();
            
            // Tạo user trước
            $userQuery = "INSERT INTO users (username, password, email, full_name, role, phone) 
                         VALUES (:username, :password, :email, :full_name, 'student', :phone)";
            $userStmt = $this->conn->prepare($userQuery);
            
            $hashedPassword = password_hash($data['password'], PASSWORD_HASH_ALGO);
            
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':password', $hashedPassword);
            $userStmt->bindParam(':email', $data['email']);
            $userStmt->bindParam(':full_name', $data['full_name']);
            $userStmt->bindParam(':phone', $data['phone']);
            
            if (!$userStmt->execute()) {
                throw new Exception("Failed to create user");
            }
            
            $userId = $this->conn->lastInsertId();
            
            // Tạo student
            $studentQuery = "INSERT INTO students (user_id, student_code, faculty, class_name, gender, date_of_birth, hometown, emergency_contact, emergency_phone, id_card) 
                           VALUES (:user_id, :student_code, :faculty, :class_name, :gender, :date_of_birth, :hometown, :emergency_contact, :emergency_phone, :id_card)";
            $studentStmt = $this->conn->prepare($studentQuery);
            
            $studentStmt->bindParam(':user_id', $userId);
            $studentStmt->bindParam(':student_code', $data['student_code']);
            $studentStmt->bindParam(':faculty', $data['faculty']);
            $studentStmt->bindParam(':class_name', $data['class_name']);
            $studentStmt->bindParam(':gender', $data['gender']);
            $studentStmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $studentStmt->bindParam(':hometown', $data['hometown']);
            $studentStmt->bindParam(':emergency_contact', $data['emergency_contact']);
            $studentStmt->bindParam(':emergency_phone', $data['emergency_phone']);
            $studentStmt->bindParam(':id_card', $data['id_card']);
            
            if (!$studentStmt->execute()) {
                throw new Exception("Failed to create student");
            }
            
            $studentId = $this->conn->lastInsertId();
            
            $this->conn->commit();
            
            $this->logger->info("Student created successfully", [
                'student_id' => $studentId,
                'user_id' => $userId,
                'student_code' => $data['student_code']
            ]);
            
            return $studentId;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Create student error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật thông tin sinh viên
    public function updateStudent($studentId, $data) {
        try {
            $this->conn->beginTransaction();
            
            // Cập nhật thông tin user
            $userFields = [];
            $userParams = [];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['email', 'full_name', 'phone'])) {
                    $userFields[] = "$key = :$key";
                    $userParams[":$key"] = $value;
                }
            }
            
            if (!empty($userFields)) {
                $userQuery = "UPDATE users SET " . implode(', ', $userFields) . ", updated_at = NOW() 
                             WHERE id = (SELECT user_id FROM students WHERE id = :student_id)";
                $userStmt = $this->conn->prepare($userQuery);
                $userParams[':student_id'] = $studentId;
                
                if (!$userStmt->execute($userParams)) {
                    throw new Exception("Failed to update user");
                }
            }
            
            // Cập nhật thông tin student
            $studentFields = [];
            $studentParams = [':id' => $studentId];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['student_code', 'faculty', 'class_name', 'gender', 'date_of_birth', 'hometown', 'emergency_contact', 'emergency_phone', 'id_card'])) {
                    $studentFields[] = "$key = :$key";
                    $studentParams[":$key"] = $value;
                }
            }
            
            if (!empty($studentFields)) {
                $studentQuery = "UPDATE students SET " . implode(', ', $studentFields) . ", updated_at = NOW() WHERE id = :id";
                $studentStmt = $this->conn->prepare($studentQuery);
                
                if (!$studentStmt->execute($studentParams)) {
                    throw new Exception("Failed to update student");
                }
            }
            
            $this->conn->commit();
            
            $this->logger->info("Student updated successfully", [
                'student_id' => $studentId,
                'fields' => array_keys($data)
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Update student error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Xóa sinh viên
    public function deleteStudent($studentId) {
        try {
            $this->conn->beginTransaction();
            
            // Lấy user_id trước khi xóa
            $query = "SELECT user_id FROM students WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $studentId);
            $stmt->execute();
            $student = $stmt->fetch();
            
            if (!$student) {
                throw new Exception("Student not found");
            }
            
            // Xóa student (sẽ cascade xóa user)
            $deleteQuery = "DELETE FROM students WHERE id = :id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':id', $studentId);
            
            if (!$deleteStmt->execute()) {
                throw new Exception("Failed to delete student");
            }
            
            $this->conn->commit();
            
            $this->logger->info("Student deleted successfully", [
                'student_id' => $studentId,
                'user_id' => $student['user_id']
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Delete student error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tìm kiếm sinh viên
    public function searchStudents($keyword, $faculty = null, $gender = null) {
        try {
            $whereConditions = ["(s.student_code LIKE :keyword OR u.full_name LIKE :keyword OR u.email LIKE :keyword)"];
            $params = [':keyword' => "%$keyword%"];
            
            if ($faculty) {
                $whereConditions[] = "s.faculty = :faculty";
                $params[':faculty'] = $faculty;
            }
            
            if ($gender) {
                $whereConditions[] = "s.gender = :gender";
                $params[':gender'] = $gender;
            }
            
            $whereClause = "WHERE " . implode(' AND ', $whereConditions);
            
            $query = "SELECT s.*, u.username, u.email, u.full_name, u.phone, u.is_active
                     FROM students s 
                     JOIN users u ON s.user_id = u.id 
                     $whereClause
                     ORDER BY s.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Search students error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đếm tổng số sinh viên
    public function countStudents($faculty = null, $gender = null) {
        try {
            $whereConditions = [];
            $params = [];
            
            if ($faculty) {
                $whereConditions[] = "faculty = :faculty";
                $params[':faculty'] = $faculty;
            }
            
            if ($gender) {
                $whereConditions[] = "gender = :gender";
                $params[':gender'] = $gender;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM students $whereClause";
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'];
        } catch (Exception $e) {
            $this->logger->error("Count students error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
    
    // Lấy danh sách khoa
    public function getFaculties() {
        try {
            $query = "SELECT DISTINCT faculty FROM students ORDER BY faculty";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            $this->logger->error("Get faculties error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
