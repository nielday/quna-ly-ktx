<?php
require_once dirname(__FILE__) . '/../../config/config.php';
require_once dirname(__FILE__) . '/../../config/database.php';
require_once dirname(__FILE__) . '/../../config/logger.php';
require_once dirname(__FILE__) . '/../../models/Equipment.php';
require_once dirname(__FILE__) . '/../../models/Room.php';
require_once dirname(__FILE__) . '/../../config/activity_helper.php';

class EquipmentController {
    private $equipmentModel;
    private $roomModel;
    private $logger;
    
    public function __construct() {
        $this->equipmentModel = new Equipment();
        $this->roomModel = new Room();
        $this->logger = new Logger();
    }
    
    /**
     * Lấy danh sách thiết bị
     */
    public function getAllEquipment() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
            $roomId = $_GET['room_id'] ?? null;
            $status = $_GET['status'] ?? null;
            
            $equipment = $this->equipmentModel->getAllEquipment($page, $limit, $roomId, $status);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $equipment,
                'pagination' => [
                    'current_page' => (int)$page,
                    'per_page' => (int)$limit
                ]
            ]);
        } catch (Exception $e) {
            $this->logger->error('Get all equipment error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Lấy thiết bị theo phòng
     */
    public function getEquipmentByRoom() {
        $roomId = $_GET['room_id'] ?? null;
        
        if (!$roomId) {
            return $this->jsonResponse(['error' => 'Room ID is required'], 400);
        }
        
        try {
            $equipment = $this->equipmentModel->getEquipmentByRoom($roomId);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $equipment
            ]);
        } catch (Exception $e) {
            $this->logger->error('Get equipment by room error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Lấy thiết bị cần bảo trì
     */
    public function getEquipmentNeedingMaintenance() {
        try {
            $equipment = $this->equipmentModel->getEquipmentNeedingMaintenance();
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $equipment
            ]);
        } catch (Exception $e) {
            $this->logger->error('Get equipment needing maintenance error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Tạo thiết bị mới
     */
    public function createEquipment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $requiredFields = ['room_id', 'equipment_name', 'equipment_type'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                return $this->jsonResponse(['error' => "Field $field is required"], 400);
            }
        }
        
        try {
            $equipmentId = $this->equipmentModel->createEquipment($input);
            
            if ($equipmentId) {
                // Ghi log activity
                try {
                    autoLogActivity('create', 'equipment', $equipmentId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Equipment created successfully',
                    'equipment_id' => $equipmentId
                ], 201);
            } else {
                return $this->jsonResponse(['error' => 'Failed to create equipment'], 500);
            }
        } catch (Exception $e) {
            $this->logger->error('Create equipment error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Cập nhật trạng thái thiết bị
     */
    public function updateEquipmentStatus($equipmentId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($input === null) {
            $this->logger->error('Failed to parse JSON input');
            $this->jsonResponse(['error' => 'Invalid JSON input'], 400);
        }
        
        if (!isset($input['status'])) {
            $this->jsonResponse(['error' => 'Status is required'], 400);
        }
        
        // Validate status
        $validStatuses = ['working', 'broken', 'maintenance', 'replaced'];
        if (!in_array($input['status'], $validStatuses)) {
            $this->jsonResponse(['error' => 'Invalid status. Must be one of: ' . implode(', ', $validStatuses)], 400);
        }
        
        try {
            $result = $this->equipmentModel->updateEquipmentStatus(
                $equipmentId, 
                $input['status'], 
                $input['notes'] ?? ''
            );
            
            if ($result) {
                try {
                    autoLogActivity('update', 'equipment', $equipmentId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Equipment status updated successfully'
                ]);
            } else {
                $this->jsonResponse(['error' => 'Failed to update equipment status'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Update equipment status error', [
                'error' => $e->getMessage(),
                'equipment_id' => $equipmentId,
                'status' => $input['status']
            ]);
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Xóa thiết bị
     */
    public function deleteEquipment($equipmentId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        try {
            $result = $this->equipmentModel->deleteEquipment($equipmentId);
            
            if ($result) {
                try {
                    autoLogActivity('delete', 'equipment', $equipmentId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Equipment deleted successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to delete equipment'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Delete equipment error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Lấy thống kê thiết bị
     */
    public function getEquipmentStats() {
        try {
            $stats = [
                'working' => $this->equipmentModel->countEquipmentByStatus('working'),
                'broken' => $this->equipmentModel->countEquipmentByStatus('broken'),
                'maintenance' => $this->equipmentModel->countEquipmentByStatus('maintenance'),
                'replaced' => $this->equipmentModel->countEquipmentByStatus('replaced'),
                'total' => $this->equipmentModel->countEquipmentByStatus()
            ];
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            $this->logger->error('Get equipment stats error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Trả về JSON response và dừng script
     */
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
        exit();
    }
}
