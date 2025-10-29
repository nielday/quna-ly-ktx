<?php
require_once '../config/database.php';
require_once '../config/logger.php';

class Feedback {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Tạo phản hồi mới
    public function createFeedback($studentId, $subject, $message, $category = 'other') {
        try {
            $query = "INSERT INTO feedback (student_id, subject, message, category) 
                     VALUES (:student_id, :subject, :message, :category)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':category', $category);
            
            if ($stmt->execute()) {
                $feedbackId = $this->conn->lastInsertId();
                
                $this->logger->info("Feedback created", [
                    'feedback_id' => $feedbackId,
                    'student_id' => $studentId,
                    'category' => $category
                ]);
                
                return $feedbackId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create feedback error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy danh sách phản hồi
    public function getFeedbacks($page = 1, $limit = RECORDS_PER_PAGE, $status = null, $category = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];
            
            if ($status) {
                $whereConditions[] = "f.status = :status";
                $params[':status'] = $status;
            }
            
            if ($category) {
                $whereConditions[] = "f.category = :category";
                $params[':category'] = $category;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT f.*, 
                     s.student_code, u.full_name as student_name, u.email as student_email,
                     resp.full_name as responded_by_name, resp.email as responded_by_email
                     FROM feedback f
                     JOIN students s ON f.student_id = s.id
                     JOIN users u ON s.user_id = u.id
                     LEFT JOIN users resp ON f.responded_by = resp.id
                     $whereClause
                     ORDER BY f.created_at DESC
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
            $this->logger->error("Get feedbacks error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Trả lời phản hồi
    public function respondToFeedback($feedbackId, $responseText, $respondedBy) {
        try {
            $query = "UPDATE feedback 
                     SET response = :response,
                         responded_by = :responded_by,
                         responded_at = NOW(),
                         status = 'resolved',
                         updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':response', $responseText);
            $stmt->bindParam(':responded_by', $respondedBy);
            $stmt->bindParam(':id', $feedbackId);
            
            if ($stmt->execute()) {
                $this->logger->info("Feedback responded", [
                    'feedback_id' => $feedbackId,
                    'responded_by' => $respondedBy
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Respond to feedback error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật trạng thái phản hồi
    public function updateFeedbackStatus($feedbackId, $status) {
        try {
            $query = "UPDATE feedback 
                     SET status = :status, updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $feedbackId);
            
            if ($stmt->execute()) {
                $this->logger->info("Feedback status updated", [
                    'feedback_id' => $feedbackId,
                    'status' => $status
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update feedback status error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy phản hồi của sinh viên
    public function getStudentFeedbacks($studentId, $limit = 50) {
        try {
            $query = "SELECT f.*
                     FROM feedback f
                     WHERE f.student_id = :student_id
                     ORDER BY f.created_at DESC
                     LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get student feedbacks error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đếm phản hồi chưa xử lý
    public function countUnresolvedFeedbacks() {
        try {
            $query = "SELECT COUNT(*) as total FROM feedback WHERE status = 'new'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch()['total'];
        } catch (Exception $e) {
            $this->logger->error("Count unresolved feedbacks error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
?>
