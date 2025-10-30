<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/Payment.php';

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new Payment();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$studentId = $_GET['student_id'] ?? null;
$registrationId = $_GET['registration_id'] ?? null;

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'POST':
            if ($action === 'create') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['student_id']) || !isset($input['room_registration_id']) || 
                    !isset($input['payment_type']) || !isset($input['amount'])) {
                    throw new Exception('Missing required fields');
                }
                
                $result = $controller->createPayment(
                    $input['student_id'],
                    $input['room_registration_id'],
                    $input['payment_type'],
                    $input['amount'],
                    $input['payment_date'] ?? date('Y-m-d'),
                    $input['payment_method'],
                    $input['reference_number'] ?? '',
                    $input['notes'] ?? ''
                );
                
                if ($result) {
                    echo json_encode(['success' => true, 'payment_id' => $result]);
                } else {
                    throw new Exception('Failed to create payment');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            if ($action === 'confirm' && isset($input['payment_id'])) {
                $result = $controller->confirmPayment($input['payment_id']);
                echo json_encode(['success' => $result]);
            } elseif (isset($input['payment_id'])) {
                // Thanh toán hóa đơn
                $result = $controller->processPayment(
                    $input['payment_id'],
                    $input['payment_method'] ?? 'cash',
                    $input['reference_number'] ?? '',
                    $input['status'] ?? 'completed'
                );
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'GET':
            if (isset($_GET['my']) && $_GET['my'] === 'true') {
                // Lấy hóa đơn của sinh viên hiện tại
                require_once '../models/Student.php';
                $studentModel = new Student();
                $student = $studentModel->getStudentByUserId($_SESSION['user_id']);
                
                if ($student) {
                    $filterStatus = $_GET['status'] ?? null;
                    $payments = $controller->getMyPayments($student['id'], $filterStatus);
                    
                    // Apply limit nếu có
                    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
                    if ($limit && is_array($payments)) {
                        $payments = array_slice($payments, 0, $limit);
                    }
                    
                    echo json_encode(['success' => true, 'data' => $payments]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Student not found']);
                }
            } elseif ($studentId && $action === 'history') {
                $limit = $_GET['limit'] ?? 50;
                $history = $controller->getPaymentHistory($studentId, $limit);
                echo json_encode(['success' => true, 'data' => $history]);
            } elseif ($registrationId && $action === 'calculate') {
                $fee = $controller->calculateTotalFee($studentId, $registrationId);
                echo json_encode(['success' => true, 'data' => $fee]);
            } elseif ($action === 'revenue') {
                $year = $_GET['year'] ?? null;
                $month = $_GET['month'] ?? null;
                $revenue = $controller->getMonthlyRevenue($year, $month);
                echo json_encode(['success' => true, 'data' => $revenue]);
            } else {
                // Get all payments
                $limit = $_GET['limit'] ?? 50;
                $offset = $_GET['offset'] ?? 0;
                $payments = $controller->getAllPayments($limit, $offset);
                echo json_encode(['success' => true, 'data' => $payments]);
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
