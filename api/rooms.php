<?php
require_once 'controllers/RoomController.php';

// Xử lý request
$controller = new RoomController();
$method = $_SERVER['REQUEST_METHOD'];

// Hỗ trợ cả path và id parameter
$path = $_GET['path'] ?? '';
$id = $_GET['id'] ?? '';

// Nếu có id parameter, ưu tiên dùng nó
if ($id) {
    $path = $id;
}

switch ($path) {
    case '':
        if ($method === 'GET') {
            $controller->getRooms();
        } elseif ($method === 'POST') {
            $controller->createRoom();
        }
        break;
    case 'available':
        if ($method === 'GET') {
            $controller->getAvailableRooms();
        }
        break;
    default:
        $roomId = (int)$path;
        if ($roomId > 0) {
            if ($method === 'GET') {
                $controller->getRoom($roomId);
            } elseif ($method === 'PUT') {
                $controller->updateRoom($roomId);
            } elseif ($method === 'DELETE') {
                $controller->deleteRoom($roomId);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid room ID']);
            exit;
        }
        break;
}
?>
