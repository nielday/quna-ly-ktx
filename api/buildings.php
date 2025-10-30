<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/Building.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new Building();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $building = $controller->getBuildingById($id);
                if ($building) {
                    echo json_encode(['success' => true, 'data' => $building]);
                } else {
                    throw new Exception('Building not found');
                }
            } else {
                $buildings = $controller->getBuildings();
                echo json_encode(['success' => true, 'data' => $buildings]);
            }
            break;
            
        case 'POST':
            if ($action === 'create') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['name']) || !isset($input['total_floors'])) {
                    throw new Exception('Missing required fields');
                }
                
                $result = $controller->createBuilding($input);
                
                if ($result) {
                    echo json_encode(['success' => true, 'building_id' => $result]);
                } else {
                    throw new Exception('Failed to create building');
                }
            }
            break;
            
        case 'PUT':
            if ($id) {
                $input = json_decode(file_get_contents('php://input'), true);
                $result = $controller->updateBuilding($id, $input);
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'DELETE':
            if ($id) {
                $result = $controller->deleteBuilding($id);
                echo json_encode(['success' => $result]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
