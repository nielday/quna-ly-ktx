<?php
require_once dirname(__FILE__) . '/../config/config.php';
require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/logger.php';

class Room {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy danh sách phòng với phân trang
    public function getRooms($page = 1, $limit = RECORDS_PER_PAGE, $buildingId = null, $status = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];
            
            if ($buildingId) {
                $whereConditions[] = "r.building_id = :building_id";
                $params[':building_id'] = $buildingId;
            }
            
            if ($status) {
                $whereConditions[] = "r.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT r.*, b.name as building_name, b.address as building_address
                     FROM rooms r 
                     JOIN buildings b ON r.building_id = b.id 
                     $whereClause
                     ORDER BY r.building_id, r.floor_number, r.room_number 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                // Determine parameter type based on the parameter name
                $type = PDO::PARAM_STR;
                if ($key === ':building_id') {
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
            $this->logger->error("Get rooms error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy thông tin phòng theo ID
    public function getRoomById($roomId) {
        try {
            $query = "SELECT r.*, b.name as building_name, b.address as building_address
                     FROM rooms r 
                     JOIN buildings b ON r.building_id = b.id 
                     WHERE r.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $roomId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get room by ID error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tạo phòng mới
    public function createRoom($data) {
        try {
            $query = "INSERT INTO rooms (building_id, room_number, floor_number, capacity, room_type, monthly_fee, description) 
                     VALUES (:building_id, :room_number, :floor_number, :capacity, :room_type, :monthly_fee, :description)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':building_id', $data['building_id']);
            $stmt->bindParam(':room_number', $data['room_number']);
            $stmt->bindParam(':floor_number', $data['floor_number']);
            $stmt->bindParam(':capacity', $data['capacity']);
            $stmt->bindParam(':room_type', $data['room_type']);
            $stmt->bindParam(':monthly_fee', $data['monthly_fee']);
            $stmt->bindParam(':description', $data['description']);
            
            if ($stmt->execute()) {
                $roomId = $this->conn->lastInsertId();
                $this->logger->info("Room created successfully", ['room_id' => $roomId, 'room_number' => $data['room_number']]);
                return $roomId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create room error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật thông tin phòng
    public function updateRoom($roomId, $data) {
        try {
            $fields = [];
            $params = [':id' => $roomId];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['room_number', 'floor_number', 'capacity', 'status', 'room_type', 'monthly_fee', 'description'])) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (!empty($fields)) {
                $query = "UPDATE rooms SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                
                if ($stmt->execute($params)) {
                    $this->logger->info("Room updated successfully", ['room_id' => $roomId, 'fields' => array_keys($data)]);
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update room error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Xóa phòng
    public function deleteRoom($roomId) {
        try {
            $query = "DELETE FROM rooms WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $roomId);
            
            if ($stmt->execute()) {
                $this->logger->info("Room deleted successfully", ['room_id' => $roomId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Delete room error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy phòng trống
    public function getAvailableRooms($buildingId = null) {
        try {
            $whereClause = "WHERE r.status = 'available' AND r.current_occupancy < r.capacity";
            $params = [];
            
            if ($buildingId) {
                $whereClause .= " AND r.building_id = :building_id";
                $params[':building_id'] = $buildingId;
            }
            
            $query = "SELECT r.*, b.name as building_name
                     FROM rooms r 
                     JOIN buildings b ON r.building_id = b.id 
                     $whereClause
                     ORDER BY r.building_id, r.floor_number, r.room_number";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get available rooms error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật số lượng người ở
    public function updateOccupancy($roomId, $occupancy) {
        try {
            $query = "UPDATE rooms SET current_occupancy = :occupancy, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':occupancy', $occupancy);
            $stmt->bindParam(':id', $roomId);
            
            if ($stmt->execute()) {
                // Cập nhật trạng thái phòng dựa trên occupancy
                $this->updateRoomStatus($roomId);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update occupancy error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật trạng thái phòng
    private function updateRoomStatus($roomId) {
        try {
            $query = "UPDATE rooms SET status = CASE 
                     WHEN current_occupancy >= capacity THEN 'full'
                     WHEN current_occupancy = 0 THEN 'available'
                     ELSE 'available'
                     END
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $roomId);
            $stmt->execute();
        } catch (Exception $e) {
            $this->logger->error("Update room status error", ['error' => $e->getMessage()]);
        }
    }
    
    // Đếm tổng số phòng
    public function countRooms($buildingId = null, $status = null) {
        try {
            $whereConditions = [];
            $params = [];
            
            if ($buildingId) {
                $whereConditions[] = "building_id = :building_id";
                $params[':building_id'] = $buildingId;
            }
            
            if ($status) {
                $whereConditions[] = "status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM rooms $whereClause";
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'];
        } catch (Exception $e) {
            $this->logger->error("Count rooms error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
?>
