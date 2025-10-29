<?php
require_once 'controllers/RoomController.php';

// Xử lý request
$controller = new RoomController();
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

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
        $id = (int)$path;
        if ($method === 'GET') {
            $controller->getRoom($id);
        } elseif ($method === 'PUT') {
            $controller->updateRoom($id);
        } elseif ($method === 'DELETE') {
            $controller->deleteRoom($id);
        }
        break;
}

// Nếu không match với case nào
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
?>
