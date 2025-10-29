<?php
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/UtilityReading.php';

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$controller = new UtilityReading();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$roomId = $_GET['room_id'] ?? null;

header('Content-Type: application/json');

try {
    switch ($method) {
        case 'POST':
            if ($action === 'create') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['room_id']) || !isset($input['reading_date']) || 
                    !isset($input['electricity_reading']) || !isset($input['water_reading'])) {
                    throw new Exception('Missing required fields');
                }
                
                $electricityRate = $input['electricity_rate'] ?? 0;
                $waterRate = $input['water_rate'] ?? 0;
                
                $result = $controller->createReading(
                    $input['room_id'],
                    $input['reading_date'],
                    $input['electricity_reading'],
                    $input['water_reading'],
                    $electricityRate,
                    $waterRate
                );
                
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    throw new Exception('Failed to create reading');
                }
            }
            // ============================================
            // CHỨC NĂNG MÔ PHỎNG - CHỈ ĐỂ DEMO/TESTING
            // ============================================
            elseif ($action === 'simulate') {
                // Mô phỏng chỉ số cho 1 phòng 1 tháng
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['room_id'])) {
                    throw new Exception('Missing room_id');
                }
                
                $readingDate = $input['reading_date'] ?? date('Y-m-01');
                $electricityRate = $input['electricity_rate'] ?? 3500;
                $waterRate = $input['water_rate'] ?? 15000;
                
                $result = $controller->simulateMonthlyReading(
                    $input['room_id'],
                    $readingDate,
                    $electricityRate,
                    $waterRate
                );
                
                if ($result) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Đã mô phỏng chỉ số thành công',
                        'data' => $result
                    ]);
                } else {
                    throw new Exception('Failed to simulate reading');
                }
            }
            elseif ($action === 'simulate-multiple') {
                // Mô phỏng nhiều tháng cho 1 phòng
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['room_id'])) {
                    throw new Exception('Missing room_id');
                }
                
                $months = $input['months'] ?? 6;
                $electricityRate = $input['electricity_rate'] ?? 3500;
                $waterRate = $input['water_rate'] ?? 15000;
                
                $results = $controller->simulateMultipleMonths(
                    $input['room_id'],
                    $months,
                    $electricityRate,
                    $waterRate
                );
                
                if ($results) {
                    $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
                    echo json_encode([
                        'success' => true,
                        'message' => "Đã mô phỏng {$successCount}/{$months} tháng",
                        'data' => $results
                    ]);
                } else {
                    throw new Exception('Failed to simulate multiple months');
                }
            }
            elseif ($action === 'simulate-all') {
                // Mô phỏng cho tất cả phòng
                $input = json_decode(file_get_contents('php://input'), true);
                
                $readingDate = $input['reading_date'] ?? date('Y-m-01');
                $electricityRate = $input['electricity_rate'] ?? 3500;
                $waterRate = $input['water_rate'] ?? 15000;
                
                $results = $controller->autoGenerateForAllRooms(
                    $readingDate,
                    $electricityRate,
                    $waterRate
                );
                
                if ($results) {
                    $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
                    $totalRooms = count($results);
                    echo json_encode([
                        'success' => true,
                        'message' => "Đã mô phỏng cho {$successCount}/{$totalRooms} phòng",
                        'data' => $results
                    ]);
                } else {
                    throw new Exception('Failed to simulate for all rooms');
                }
            }
            elseif ($action === 'create-invoice') {
                // Tạo hóa đơn từ reading
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['reading_id'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Missing reading_id']);
                    exit();
                }
                
                try {
                    $result = $controller->createInvoiceForReading($input['reading_id']);
                    
                    if ($result) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Đã tạo hóa đơn thành công!'
                        ]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'error' => 'Failed to create invoice']);
                    }
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
                exit();
            }
            break;
            
        case 'GET':
            if ($roomId) {
                if ($action === 'history') {
                    $limit = $_GET['limit'] ?? 12;
                    $history = $controller->getReadingHistory($roomId, $limit);
                    echo json_encode(['success' => true, 'data' => $history]);
                } elseif ($action === 'last') {
                    $last = $controller->getLastReading($roomId);
                    echo json_encode(['success' => true, 'data' => $last]);
                } elseif ($action === 'unpaid') {
                    $amount = $controller->getUnpaidAmount($roomId);
                    echo json_encode(['success' => true, 'amount' => $amount]);
                }
            } else {
                // Get all utility readings
                $limit = $_GET['limit'] ?? 50;
                $offset = $_GET['offset'] ?? 0;
                $readings = $controller->getAllReadings($limit, $offset);
                echo json_encode(['success' => true, 'data' => $readings]);
            }
            break;
            
        case 'PUT':
            if ($action === 'mark-paid') {
                $input = json_decode(file_get_contents('php://input'), true);
                $result = $controller->markAsPaid($input['reading_id']);
                echo json_encode(['success' => $result]);
            }
            break;
            
        case 'DELETE':
            if ($action === 'clear-simulated') {
                // Xóa dữ liệu mô phỏng - CHỈ DÙNG TRONG DEV/TESTING
                $input = json_decode(file_get_contents('php://input'), true);
                $roomId = $input['room_id'] ?? null;
                
                $result = $controller->clearSimulatedData($roomId);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => $roomId 
                            ? "Đã xóa dữ liệu mô phỏng của phòng #{$roomId}" 
                            : "Đã xóa tất cả dữ liệu mô phỏng"
                    ]);
                } else {
                    throw new Exception('Failed to clear simulated data');
                }
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
