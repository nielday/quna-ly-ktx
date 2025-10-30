<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/logger.php';

class RoomRegistration {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Tạo đăng ký phòng mới
    public function createRegistration($studentId, $roomId, $startDate, $endDate, $notes = '') {
        try {
            $this->conn->beginTransaction();
            
            // Kiểm tra phòng có đang trống không
            $roomQuery = "SELECT capacity, current_occupancy FROM rooms WHERE id = :room_id AND status = 'available'";
            $roomStmt = $this->conn->prepare($roomQuery);
            $roomStmt->bindParam(':room_id', $roomId);
            $roomStmt->execute();
            $room = $roomStmt->fetch();
            
            if (!$room) {
                throw new Exception("Phòng đã đầy hoặc đang bảo trì");
            }
            
            if ($room['current_occupancy'] >= $room['capacity']) {
                throw new Exception("Phòng đã đầy");
            }
            
            // Kiểm tra sinh viên đã đăng ký phòng nào chưa
            $checkQuery = "SELECT id FROM room_registrations 
                          WHERE student_id = :student_id 
                          AND status IN ('pending', 'approved', 'active')";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':student_id', $studentId);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                throw new Exception("Sinh viên đã có đăng ký phòng");
            }
            
            // Tạo đăng ký
            $query = "INSERT INTO room_registrations (student_id, room_id, registration_date, start_date, end_date, notes, status) 
                     VALUES (:student_id, :room_id, CURDATE(), :start_date, :end_date, :notes, 'pending')";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->bindParam(':notes', $notes);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create registration");
            }
            
            $registrationId = $this->conn->lastInsertId();
            
            $this->conn->commit();
            
            $this->logger->info("Room registration created", [
                'registration_id' => $registrationId,
                'student_id' => $studentId,
                'room_id' => $roomId
            ]);
            
            return $registrationId;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Create registration error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Duyệt đăng ký
    public function approveRegistration($registrationId, $approvedBy) {
        try {
            $this->conn->beginTransaction();
            
            // Lấy thông tin đăng ký
            $query = "SELECT * FROM room_registrations WHERE id = :id AND status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $registrationId);
            $stmt->execute();
            $registration = $stmt->fetch();
            
            if (!$registration) {
                throw new Exception("Registration not found or already processed");
            }
            
            // Cập nhật trạng thái đăng ký
            $updateQuery = "UPDATE room_registrations 
                           SET status = 'active', 
                               approved_by = :approved_by, 
                               approved_at = NOW() 
                           WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':approved_by', $approvedBy);
            $updateStmt->bindParam(':id', $registrationId);
            $updateStmt->execute();
            
            // Cập nhật occupancy của phòng
            $roomQuery = "UPDATE rooms 
                         SET current_occupancy = current_occupancy + 1,
                             status = CASE 
                                 WHEN current_occupancy + 1 >= capacity THEN 'full'
                                 ELSE 'available'
                             END
                         WHERE id = :room_id";
            $roomStmt = $this->conn->prepare($roomQuery);
            $roomStmt->bindParam(':room_id', $registration['room_id']);
            $roomStmt->execute();
            
            $this->conn->commit();
            
            $this->logger->info("Registration approved", [
                'registration_id' => $registrationId,
                'approved_by' => $approvedBy
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Approve registration error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Từ chối đăng ký
    public function rejectRegistration($registrationId, $reason = '') {
        try {
            $notesToAdd = !empty($reason) ? ' - Rejected: ' . $reason : '';
            
            $query = "UPDATE room_registrations 
                     SET status = 'rejected', 
                         notes = CONCAT(COALESCE(notes, ''), :notes),
                         updated_at = NOW() 
                     WHERE id = :id AND status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $registrationId);
            $stmt->bindParam(':notes', $notesToAdd);
            
            if ($stmt->execute()) {
                $this->logger->info("Registration rejected", [
                    'registration_id' => $registrationId,
                    'reason' => $reason
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Reject registration error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy danh sách đăng ký
    public function getRegistrations($page = 1, $limit = RECORDS_PER_PAGE, $status = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = $status ? "WHERE rr.status = :status" : "";
            $params = [];
            
            if ($status) {
                $params[':status'] = $status;
            }
            
            $query = "SELECT rr.*, 
                     u.full_name as student_name, u.email as student_email,
                     s.student_code, s.faculty,
                     r.room_number, b.name as building_name,
                     app.full_name as approved_by_name
                     FROM room_registrations rr
                     JOIN students s ON rr.student_id = s.id
                     JOIN users u ON s.user_id = u.id
                     JOIN rooms r ON rr.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     LEFT JOIN users app ON rr.approved_by = app.id
                     $whereClause
                     ORDER BY rr.created_at DESC 
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
            $this->logger->error("Get registrations error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Gia hạn hợp đồng
    public function extendContract($registrationId, $newEndDate) {
        try {
            $query = "UPDATE room_registrations 
                     SET end_date = :new_end_date, updated_at = NOW() 
                     WHERE id = :id AND status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':new_end_date', $newEndDate);
            $stmt->bindParam(':id', $registrationId);
            
            if ($stmt->execute()) {
                $this->logger->info("Contract extended", ['registration_id' => $registrationId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Extend contract error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Trả phòng
    public function checkOut($registrationId) {
        try {
            $this->conn->beginTransaction();
            
            // Lấy thông tin đăng ký
            $query = "SELECT * FROM room_registrations WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $registrationId);
            $stmt->execute();
            $registration = $stmt->fetch();
            
            if (!$registration) {
                throw new Exception("Registration not found");
            }
            
            // Cập nhật trạng thái đăng ký
            $updateQuery = "UPDATE room_registrations 
                           SET status = 'completed', updated_at = NOW() 
                           WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':id', $registrationId);
            $updateStmt->execute();
            
            // Giảm occupancy của phòng
            $roomQuery = "UPDATE rooms 
                         SET current_occupancy = current_occupancy - 1,
                             status = 'available'
                         WHERE id = :room_id AND current_occupancy > 0";
            $roomStmt = $this->conn->prepare($roomQuery);
            $roomStmt->bindParam(':room_id', $registration['room_id']);
            $roomStmt->execute();
            
            $this->conn->commit();
            
            $this->logger->info("Check out completed", ['registration_id' => $registrationId]);
            
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Check out error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy đăng ký của sinh viên hiện tại
    public function getMyRegistrations($studentId) {
        try {
            $query = "SELECT rr.*, 
                     r.room_number, r.capacity, r.current_occupancy, r.monthly_fee, r.room_type,
                     b.name as building_name,
                     app.full_name as approved_by_name
                     FROM room_registrations rr
                     JOIN rooms r ON rr.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     LEFT JOIN users app ON rr.approved_by = app.id
                     WHERE rr.student_id = :student_id
                     ORDER BY rr.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get my registrations error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
