<?php
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/ActivityLog.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new ActivityLog();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
                $userId = $_GET['user_id'] ?? null;
                $actionFilter = $_GET['action_filter'] ?? null;
                
                $logs = $controller->getActivityLogs($page, $limit, $userId, $actionFilter);
                $total = $controller->countActivityLogs($userId, $actionFilter);
                
                echo json_encode([
                    'success' => true,
                    'data' => $logs,
                    'pagination' => [
                        'current_page' => (int)$page,
                        'per_page' => (int)$limit,
                        'total' => $total,
                        'total_pages' => ceil($total / $limit)
                    ]
                ]);
            }
            break;
            
        case 'POST':
            if ($action === 'log') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['action'])) {
                    throw new Exception('Action is required');
                }
                
                $result = $controller->logActivity(
                    $_SESSION['user_id'],
                    $input['action'],
                    $input['table_name'] ?? null,
                    $input['record_id'] ?? null,
                    $input['old_values'] ?? null,
                    $input['new_values'] ?? null
                );
                
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
