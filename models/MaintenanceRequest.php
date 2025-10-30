<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/logger.php';

class MaintenanceRequest {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Tạo yêu cầu bảo trì
    public function createRequest($data) {
        try {
            // Chuẩn bị giá trị để bind
            $equipmentId = $data['equipment_id'] ?? null;
            $studentId = $data['student_id'] ?? null;
            $priority = $data['priority'] ?? 'medium';
            
            $query = "INSERT INTO maintenance_requests 
                     (room_id, equipment_id, student_id, request_type, description, priority, status) 
                     VALUES (:room_id, :equipment_id, :student_id, :request_type, :description, :priority, 'pending')";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':room_id', $data['room_id']);
            $stmt->bindParam(':equipment_id', $equipmentId);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':request_type', $data['request_type']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':priority', $priority);
            
            if ($stmt->execute()) {
                $requestId = $this->conn->lastInsertId();
                
                // Nếu là thiết bị thì cập nhật trạng thái
                if ($equipmentId) {
                    $updateQuery = "UPDATE equipment SET status = 'broken' WHERE id = :id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $equipmentId);
                    $updateStmt->execute();
                }
                
                $this->logger->info("Maintenance request created", [
                    'request_id' => $requestId,
                    'room_id' => $data['room_id'],
                    'type' => $data['request_type']
                ]);
                
                return $requestId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create maintenance request error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy danh sách yêu cầu bảo trì
    public function getRequests($page = 1, $limit = RECORDS_PER_PAGE, $status = null, $priority = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];
            
            if ($status) {
                $whereConditions[] = "mr.status = :status";
                $params[':status'] = $status;
            }
            
            if ($priority) {
                $whereConditions[] = "mr.priority = :priority";
                $params[':priority'] = $priority;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT mr.*, 
                     r.room_number, b.name as building_name,
                     e.equipment_name, e.equipment_type,
                     u.full_name as student_name, u.email as student_email,
                     assigned.full_name as assigned_to_name
                     FROM maintenance_requests mr
                     JOIN rooms r ON mr.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     LEFT JOIN equipment e ON mr.equipment_id = e.id
                     LEFT JOIN students s ON mr.student_id = s.id
                     LEFT JOIN users u ON s.user_id = u.id
                     LEFT JOIN users assigned ON mr.assigned_to = assigned.id
                     $whereClause
                     ORDER BY 
                         CASE mr.priority 
                             WHEN 'urgent' THEN 1 
                             WHEN 'high' THEN 2 
                             WHEN 'medium' THEN 3 
                             WHEN 'low' THEN 4 
                         END,
                         mr.created_at DESC
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
            $this->logger->error("Get maintenance requests error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Phân công yêu cầu bảo trì
    public function assignRequest($requestId, $assignedTo, $estimatedCost = null) {
        try {
            $query = "UPDATE maintenance_requests 
                     SET assigned_to = :assigned_to, 
                         status = 'in_progress',
                         estimated_cost = :estimated_cost,
                         updated_at = NOW() 
                     WHERE id = :id AND status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':assigned_to', $assignedTo);
            $stmt->bindParam(':estimated_cost', $estimatedCost);
            $stmt->bindParam(':id', $requestId);
            
            if ($stmt->execute()) {
                $this->logger->info("Request assigned", [
                    'request_id' => $requestId,
                    'assigned_to' => $assignedTo
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Assign request error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Hoàn thành yêu cầu bảo trì
    public function completeRequest($requestId, $actualCost, $notes = '') {
        try {
            $this->conn->beginTransaction();
            
            // Cập nhật trạng thái request
            $query = "UPDATE maintenance_requests 
                     SET status = 'completed',
                         actual_cost = :actual_cost,
                         notes = :notes,
                         completion_date = CURDATE(),
                         updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':actual_cost', $actualCost);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':id', $requestId);
            $stmt->execute();
            
            // Lấy thông tin request
            $getQuery = "SELECT equipment_id FROM maintenance_requests WHERE id = :id";
            $getStmt = $this->conn->prepare($getQuery);
            $getStmt->bindParam(':id', $requestId);
            $getStmt->execute();
            $request = $getStmt->fetch();
            
            // Cập nhật trạng thái thiết bị
            if ($request && $request['equipment_id']) {
                $updateQuery = "UPDATE equipment SET status = 'working', updated_at = NOW() WHERE id = :id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':id', $request['equipment_id']);
                $updateStmt->execute();
            }
            
            $this->conn->commit();
            
            $this->logger->info("Request completed", [
                'request_id' => $requestId,
                'actual_cost' => $actualCost
            ]);
            
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Complete request error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Hủy yêu cầu bảo trì
    public function cancelRequest($requestId, $reason = '') {
        try {
            $query = "UPDATE maintenance_requests 
                     SET status = 'cancelled',
                         notes = CONCAT(COALESCE(notes, ''), ' - Cancelled: ', :reason),
                         updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reason', $reason);
            $stmt->bindParam(':id', $requestId);
            
            if ($stmt->execute()) {
                $this->logger->info("Request cancelled", ['request_id' => $requestId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Cancel request error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Thống kê bảo trì
    public function getMaintenanceStats() {
        try {
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                        SUM(COALESCE(actual_cost, 0)) as total_cost
                     FROM maintenance_requests";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get maintenance stats error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
