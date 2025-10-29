<?php
// Tắt hiển thị lỗi để tránh in HTML ra JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'controllers/RegistrationController.php';

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new RegistrationController();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

header('Content-Type: application/json');

try {
    // Handle GET with my=true first, before other GET cases
    if ($method === 'GET' && isset($_GET['my']) && $_GET['my'] === 'true') {
        // Sinh viên xem đăng ký của mình
        $controller->getMyRegistrations($_SESSION['user_id']);
        exit();
    }
    
    switch ($method) {
        case 'POST':
            // Sinh viên: Tạo đăng ký phòng mới
            if ($action === 'create') {
                $controller->createRegistration();
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
            }
            break;
            
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Registration ID is required']);
                exit();
            }
            
            // Admin/Staff: Duyệt đăng ký
            if ($action === 'approve') {
                $controller->approveRegistration($id, $_SESSION['user_id']);
            } 
            // Admin/Staff: Từ chối đăng ký
            elseif ($action === 'reject') {
                $controller->rejectRegistration($id);
            } 
            // Admin/Staff: Gia hạn hợp đồng
            elseif ($action === 'extend') {
                $controller->extendContract($id);
            } 
            // Admin/Staff: Trả phòng
            elseif ($action === 'checkout') {
                $controller->checkOut($id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
            }
            break;
            
        case 'GET':
            // Admin/Staff: Xem danh sách đăng ký (có thể filter theo status)
            $status = $_GET['status'] ?? null;
            $controller->getRegistrations($status);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
