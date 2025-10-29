<?php
// Bắt đầu output buffering để tránh output trước đó
ob_start();

// Tắt hiển thị lỗi trên màn hình
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'controllers/EquipmentController.php';

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set header JSON
header('Content-Type: application/json');

$controller = new EquipmentController();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) && $_GET['id'] ? (int)$_GET['id'] : null;

try {
    switch ($method) {
        case 'GET':
            if ($action === 'maintenance') {
                // Lấy thiết bị cần bảo trì
                $controller->getEquipmentNeedingMaintenance();
                exit();
            } elseif ($action === 'stats') {
                // Lấy thống kê thiết bị
                $controller->getEquipmentStats();
                exit();
            } elseif ($id) {
                // Lấy thiết bị theo ID
                // TODO: Implement get equipment by ID
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                http_response_code(404);
                echo json_encode(['error' => 'Not implemented']);
                exit();
            } else {
                // Lấy tất cả thiết bị hoặc theo phòng
                $controller->getAllEquipment();
                exit();
            }
            break;
            
        case 'POST':
            // Tạo thiết bị mới
            if ($action === 'create') {
                $controller->createEquipment();
                // Controller outputs its own response
                exit();
            } else {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
                exit();
            }
            break;
            
        case 'PUT':
            if (!$id) {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                http_response_code(400);
                echo json_encode(['error' => 'Equipment ID is required']);
                exit();
            }
            
            if ($action === 'update-status') {
                // Cập nhật trạng thái thiết bị
                // Clean buffer trước khi gọi controller
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                try {
                    $controller->updateEquipmentStatus($id);
                } catch (Throwable $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Controller exception', 'message' => $e->getMessage()]);
                }
                exit();
            } else {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
                exit();
            }
            break;
            
        case 'DELETE':
            if (!$id) {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                http_response_code(400);
                echo json_encode(['error' => 'Equipment ID is required']);
                exit();
            }
            
            // Xóa thiết bị
            $controller->deleteEquipment($id);
            // Controller outputs its own response
            exit();
            
        default:
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit();
            break;
    }
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}
