<?php
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/Feedback.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new Feedback();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'POST':
            if ($action === 'create') {
                // Lấy student_id từ user_id
                $database = new Database();
                $conn = $database->getConnection();
                
                $getStudentQuery = "SELECT id FROM students WHERE user_id = :user_id";
                $stmt = $conn->prepare($getStudentQuery);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $student = $stmt->fetch();
                
                if (!$student) {
                    throw new Exception('Student not found');
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['subject']) || !isset($input['message'])) {
                    throw new Exception('Missing required fields');
                }
                
                $result = $controller->createFeedback(
                    $student['id'],
                    $input['subject'],
                    $input['message'],
                    $input['category'] ?? 'other'
                );
                
                if ($result) {
                    echo json_encode(['success' => true, 'feedback_id' => $result]);
                } else {
                    throw new Exception('Failed to create feedback');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if ($action === 'respond' && isset($input['feedback_id'])) {
                $result = $controller->respondToFeedback(
                    $input['feedback_id'],
                    $input['response'],
                    $_SESSION['user_id']
                );
                echo json_encode(['success' => $result]);
            } elseif ($action === 'update-status' && isset($input['feedback_id'])) {
                $result = $controller->updateFeedbackStatus(
                    $input['feedback_id'],
                    $input['status']
                );
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'GET':
            if ($action === 'list') {
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
                $status = $_GET['status'] ?? null;
                $category = $_GET['category'] ?? null;
                
                $feedbacks = $controller->getFeedbacks($page, $limit, $status, $category);
                echo json_encode(['success' => true, 'data' => $feedbacks]);
            } elseif ($action === 'student') {
                // Lấy student_id từ user_id
                $database = new Database();
                $conn = $database->getConnection();
                
                $getStudentQuery = "SELECT id FROM students WHERE user_id = :user_id";
                $stmt = $conn->prepare($getStudentQuery);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $student = $stmt->fetch();
                
                if (!$student) {
                    throw new Exception('Student not found');
                }
                
                $limit = $_GET['limit'] ?? 50;
                $feedbacks = $controller->getStudentFeedbacks($student['id'], $limit);
                echo json_encode(['success' => true, 'data' => $feedbacks]);
            } elseif ($action === 'unresolved-count') {
                $count = $controller->countUnresolvedFeedbacks();
                echo json_encode(['success' => true, 'count' => $count]);
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
