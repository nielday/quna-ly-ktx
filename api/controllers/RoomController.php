<?php
require_once dirname(__FILE__) . '/../../config/config.php';
require_once dirname(__FILE__) . '/../../config/database.php';
require_once dirname(__FILE__) . '/../../config/logger.php';
require_once dirname(__FILE__) . '/../../models/Room.php';

class RoomController {
    private $roomModel;
    private $logger;
    
    public function __construct() {
        $this->roomModel = new Room();
        $this->logger = new Logger();
    }
    
    // Lấy danh sách phòng
    public function getRooms() {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
        $buildingId = $_GET['building_id'] ?? null;
        $status = $_GET['status'] ?? null;
        
        $rooms = $this->roomModel->getRooms($page, $limit, $buildingId, $status);
        $total = $this->roomModel->countRooms($buildingId, $status);
        
        return $this->jsonResponse([
            'success' => true,
            'data' => $rooms,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    // Lấy thông tin phòng theo ID
    public function getRoom($id) {
        $room = $this->roomModel->getRoomById($id);
        
        if ($room) {
            return $this->jsonResponse([
                'success' => true,
                'data' => $room
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Room not found'], 404);
        }
    }
    
    // Tạo phòng mới
    public function createRoom() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $requiredFields = ['building_id', 'room_number', 'floor_number', 'capacity', 'monthly_fee'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                return $this->jsonResponse(['error' => "Field $field is required"], 400);
            }
        }
        
        $roomId = $this->roomModel->createRoom($input);
        
        if ($roomId) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Room created successfully',
                'room_id' => $roomId
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to create room'], 500);
        }
    }
    
    // Cập nhật phòng
    public function updateRoom($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $success = $this->roomModel->updateRoom($id, $input);
        
        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Room updated successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to update room'], 500);
        }
    }
    
    // Xóa phòng
    public function deleteRoom($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $success = $this->roomModel->deleteRoom($id);
        
        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Room deleted successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to delete room'], 500);
        }
    }
    
    // Lấy phòng trống
    public function getAvailableRooms() {
        $buildingId = $_GET['building_id'] ?? null;
        
        $rooms = $this->roomModel->getAvailableRooms($buildingId);
        
        return $this->jsonResponse([
            'success' => true,
            'data' => $rooms
        ]);
    }
    
    // Trả về JSON response
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>

