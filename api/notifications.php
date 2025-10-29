<?php
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/Notification.php';

session_start();

$controller = new Notification();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'POST':
            if ($action === 'create') {
                if (!isset($_SESSION['user_id'])) {
                    throw new Exception('Unauthorized');
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['title']) || !isset($input['content'])) {
                    throw new Exception('Missing required fields');
                }
                
                $input['created_by'] = $_SESSION['user_id'];
                $input['target_audience'] = $input['target_audience'] ?? 'all';
                $input['type'] = $input['type'] ?? 'general';
                $input['is_urgent'] = $input['is_urgent'] ?? false;
                
                $result = $controller->createNotification($input);
                
                if ($result) {
                    echo json_encode(['success' => true, 'notification_id' => $result]);
                } else {
                    throw new Exception('Failed to create notification');
                }
            }
            break;
            
        case 'GET':
            $targetAudience = $_GET['target_audience'] ?? (isset($_SESSION['role']) ? $_SESSION['role'] : 'all');
            $type = $_GET['type'] ?? null;
            $limit = $_GET['limit'] ?? 50;
            
            $notifications = $controller->getNotifications($targetAudience, $type, $limit);
            echo json_encode(['success' => true, 'data' => $notifications]);
            break;
            
        case 'PUT':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Unauthorized');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if ($action === 'update' && isset($input['notification_id'])) {
                $result = $controller->updateNotification($input['notification_id'], $input);
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'DELETE':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Unauthorized');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (isset($input['notification_id'])) {
                $result = $controller->deleteNotification($input['notification_id']);
                echo json_encode(['success' => $result]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
