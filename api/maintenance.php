<?php
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/MaintenanceRequest.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new MaintenanceRequest();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'POST':
            if ($action === 'create') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['room_id']) || !isset($input['request_type']) || !isset($input['description'])) {
                    throw new Exception('Missing required fields');
                }
                
                // Nếu sinh viên tạo thì thêm student_id
                if ($_SESSION['role'] === 'student') {
                    $database = new Database();
                    $conn = $database->getConnection();
                    
                    // Lấy student_id từ session
                    $stmt = $conn->prepare("SELECT id FROM students WHERE user_id = :user_id");
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                    $student = $stmt->fetch();
                    
                    if (!$student) {
                        throw new Exception('Student not found');
                    }
                    
                    $input['student_id'] = $student['id'];
                }
                
                $result = $controller->createRequest($input);
                
                if ($result) {
                    echo json_encode(['success' => true, 'request_id' => $result]);
                } else {
                    throw new Exception('Failed to create request');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if ($action === 'assign' && isset($input['request_id'])) {
                $result = $controller->assignRequest(
                    $input['request_id'],
                    $input['assigned_to'],
                    $input['estimated_cost'] ?? null
                );
                echo json_encode(['success' => $result]);
            } elseif ($action === 'complete' && isset($input['request_id'])) {
                $result = $controller->completeRequest(
                    $input['request_id'],
                    $input['actual_cost'],
                    $input['notes'] ?? ''
                );
                
                // Nếu yêu cầu tạo hóa đơn
                if ($result && isset($input['create_invoice']) && $input['create_invoice'] === true) {
                    try {
                        // Lấy thông tin maintenance request
                        $database = new Database();
                        $conn = $database->getConnection();
                        
                        $query = "SELECT mr.*, s.id as student_id, s.user_id
                                 FROM maintenance_requests mr
                                 LEFT JOIN students s ON mr.student_id = s.id
                                 WHERE mr.id = :request_id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':request_id', $input['request_id']);
                        $stmt->execute();
                        $request = $stmt->fetch();
                        
                        if ($request && $request['student_id']) {
                            // Lấy room_registration_id active của sinh viên
                            $regQuery = "SELECT id FROM room_registrations 
                                        WHERE student_id = :student_id 
                                        AND status IN ('active', 'approved')
                                        ORDER BY created_at DESC 
                                        LIMIT 1";
                            $regStmt = $conn->prepare($regQuery);
                            $regStmt->bindParam(':student_id', $request['student_id']);
                            $regStmt->execute();
                            $registration = $regStmt->fetch();
                            
                            if ($registration) {
                                // Tạo payment
                                require_once '../models/Payment.php';
                                $paymentModel = new Payment();
                                
                                $paymentId = $paymentModel->createPayment(
                                    $request['student_id'],
                                    $registration['id'],
                                    'penalty', // Loại: chi phí bảo trì
                                    $input['actual_cost'],
                                    date('Y-m-d'),
                                    'cash',
                                    'MAINT-' . $input['request_id'],
                                    'Chi phí bảo trì: ' . ($input['notes'] ?? 'Yêu cầu #' . $input['request_id'])
                                );
                                
                                if ($paymentId) {
                                    echo json_encode([
                                        'success' => true,
                                        'payment_id' => $paymentId,
                                        'message' => 'Hoàn thành và đã tạo hóa đơn'
                                    ]);
                                    break;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Vẫn trả về success vì đã complete, chỉ thông báo lỗi tạo hóa đơn
                        echo json_encode([
                            'success' => true,
                            'warning' => 'Hoàn thành nhưng không thể tạo hóa đơn: ' . $e->getMessage()
                        ]);
                        break;
                    }
                }
                
                echo json_encode(['success' => $result]);
            } elseif ($action === 'cancel' && isset($input['request_id'])) {
                $result = $controller->cancelRequest($input['request_id'], $input['reason'] ?? '');
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'GET':
            if ($action === 'list') {
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
                $status = $_GET['status'] ?? null;
                $priority = $_GET['priority'] ?? null;
                
                $requests = $controller->getRequests($page, $limit, $status, $priority);
                echo json_encode(['success' => true, 'data' => $requests]);
            } elseif ($action === 'my') {
                // Lấy yêu cầu sửa chữa của sinh viên hiện tại
                if ($_SESSION['role'] !== 'student') {
                    throw new Exception('Only students can access this endpoint');
                }
                
                $database = new Database();
                $conn = $database->getConnection();
                
                // Lấy student_id
                $stmt = $conn->prepare("SELECT id FROM students WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $student = $stmt->fetch();
                
                if (!$student) {
                    throw new Exception('Student not found');
                }
                
                // Lấy danh sách requests
                $query = "SELECT mr.*, 
                         r.room_number, b.name as building_name,
                         e.equipment_name, e.equipment_type,
                         assigned.full_name as assigned_to_name
                         FROM maintenance_requests mr
                         JOIN rooms r ON mr.room_id = r.id
                         JOIN buildings b ON r.building_id = b.id
                         LEFT JOIN equipment e ON mr.equipment_id = e.id
                         LEFT JOIN users assigned ON mr.assigned_to = assigned.id
                         WHERE mr.student_id = :student_id
                         ORDER BY mr.created_at DESC";
                
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':student_id', $student['id']);
                $stmt->execute();
                $requests = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $requests]);
            } elseif ($action === 'stats') {
                $stats = $controller->getMaintenanceStats();
                echo json_encode(['success' => true, 'data' => $stats]);
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
