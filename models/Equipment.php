<?php
require_once dirname(__FILE__) . '/../config/config.php';
require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/logger.php';

class Equipment {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy tất cả thiết bị
    public function getAllEquipment($page = 1, $limit = RECORDS_PER_PAGE, $roomId = null, $status = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];
            
            if ($roomId) {
                $whereConditions[] = "e.room_id = :room_id";
                $params[':room_id'] = $roomId;
            }
            
            if ($status) {
                $whereConditions[] = "e.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT e.*, r.room_number, b.name as building_name
                     FROM equipment e
                     JOIN rooms r ON e.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     $whereClause
                     ORDER BY e.created_at DESC
                     LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                // Determine parameter type based on the parameter name
                $type = PDO::PARAM_STR;
                if ($key === ':room_id') {
                    $type = PDO::PARAM_INT;
                } elseif ($key === ':status') {
                    $type = PDO::PARAM_STR;
                }
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get all equipment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy danh sách thiết bị theo phòng
    public function getEquipmentByRoom($roomId) {
        try {
            $query = "SELECT e.*, r.room_number, b.name as building_name
                     FROM equipment e
                     JOIN rooms r ON e.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     WHERE e.room_id = :room_id
                     ORDER BY e.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':room_id', $roomId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get equipment by room error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tạo thiết bị mới
    public function createEquipment($data) {
        try {
            $query = "INSERT INTO equipment (room_id, equipment_name, equipment_type, brand, model, 
                     serial_number, purchase_date, warranty_expiry, status) 
                     VALUES (:room_id, :equipment_name, :equipment_type, :brand, :model, 
                             :serial_number, :purchase_date, :warranty_expiry, 'working')";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindValue(':room_id', $data['room_id'], PDO::PARAM_INT);
            $stmt->bindValue(':equipment_name', $data['equipment_name'], PDO::PARAM_STR);
            $stmt->bindValue(':equipment_type', $data['equipment_type'], PDO::PARAM_STR);
            $stmt->bindValue(':brand', $data['brand'] ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':model', $data['model'] ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':serial_number', $data['serial_number'] ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':purchase_date', $data['purchase_date'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':warranty_expiry', $data['warranty_expiry'] ?? null, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $equipmentId = $this->conn->lastInsertId();
                
                $this->logger->info("Equipment created", [
                    'equipment_id' => $equipmentId,
                    'room_id' => $data['room_id'],
                    'equipment_name' => $data['equipment_name']
                ]);
                
                return $equipmentId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create equipment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật trạng thái thiết bị
    public function updateEquipmentStatus($equipmentId, $status, $notes = '') {
        try {
            $query = "UPDATE equipment 
                     SET status = :status, updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            // Explicitly bind values with type hints to avoid reference issues
            $statusValue = (string)$status;
            $idValue = (int)$equipmentId;
            
            $stmt->bindValue(':status', $statusValue, PDO::PARAM_STR);
            $stmt->bindValue(':id', $idValue, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->logger->info("Equipment status updated", [
                    'equipment_id' => $equipmentId,
                    'status' => $status,
                    'notes' => $notes
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update equipment status error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Xóa thiết bị
    public function deleteEquipment($equipmentId) {
        try {
            $query = "DELETE FROM equipment WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $equipmentId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->logger->info("Equipment deleted", ['equipment_id' => $equipmentId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Delete equipment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy tất cả thiết bị cần bảo trì
    public function getEquipmentNeedingMaintenance() {
        try {
            $query = "SELECT e.*, r.room_number, b.name as building_name
                     FROM equipment e
                     JOIN rooms r ON e.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     WHERE e.status IN ('broken', 'maintenance')
                     ORDER BY e.updated_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get equipment needing maintenance error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đếm thiết bị theo trạng thái
    public function countEquipmentByStatus($status = null) {
        try {
            $whereClause = $status ? "WHERE status = :status" : "";
            $params = [];
            
            if ($status) {
                $params[':status'] = $status;
            }
            
            $query = "SELECT COUNT(*) as total FROM equipment $whereClause";
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                // Determine parameter type based on the parameter name
                $type = PDO::PARAM_STR;
                if ($key === ':status') {
                    $type = PDO::PARAM_STR;
                }
                $stmt->bindValue($key, $value, $type);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'];
        } catch (Exception $e) {
            $this->logger->error("Count equipment by status error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
?>
