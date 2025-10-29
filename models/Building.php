<?php
require_once '../config/database.php';
require_once '../config/logger.php';

class Building {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy danh sách tòa nhà
    public function getBuildings() {
        try {
            $query = "SELECT * FROM buildings ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get buildings error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy tòa nhà theo ID
    public function getBuildingById($buildingId) {
        try {
            $query = "SELECT * FROM buildings WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $buildingId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get building by ID error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tạo tòa nhà mới
    public function createBuilding($data) {
        try {
            $query = "INSERT INTO buildings (name, address, total_floors, description) 
                     VALUES (:name, :address, :total_floors, :description)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':total_floors', $data['total_floors']);
            $stmt->bindParam(':description', $data['description']);
            
            if ($stmt->execute()) {
                $buildingId = $this->conn->lastInsertId();
                $this->logger->info("Building created successfully", ['building_id' => $buildingId, 'name' => $data['name']]);
                return $buildingId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create building error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật tòa nhà
    public function updateBuilding($buildingId, $data) {
        try {
            $fields = [];
            $params = [':id' => $buildingId];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['name', 'address', 'total_floors', 'description', 'is_active'])) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (!empty($fields)) {
                $query = "UPDATE buildings SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                
                if ($stmt->execute($params)) {
                    $this->logger->info("Building updated successfully", ['building_id' => $buildingId]);
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update building error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Xóa tòa nhà
    public function deleteBuilding($buildingId) {
        try {
            $query = "DELETE FROM buildings WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $buildingId);
            
            if ($stmt->execute()) {
                $this->logger->info("Building deleted successfully", ['building_id' => $buildingId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Delete building error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy số phòng theo tòa nhà
    public function getRoomCountByBuilding($buildingId) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_rooms,
                        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms,
                        SUM(CASE WHEN status = 'full' THEN 1 ELSE 0 END) as full_rooms,
                        SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_rooms
                     FROM rooms 
                     WHERE building_id = :building_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':building_id', $buildingId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get room count by building error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
