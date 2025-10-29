<?php
// Suppress errors if not in debug mode
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once dirname(__FILE__) . '/../../config/database.php';
require_once dirname(__FILE__) . '/../../config/logger.php';

class AdminPendingTasks {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy các nhiệm vụ cần xử lý
    public function getPendingTasks() {
        try {
            $tasks = [];
            
            // Đăng ký phòng chờ duyệt
            $query = "SELECT COUNT(*) as count FROM room_registrations WHERE status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $pendingRegistrations = $stmt->fetch()['count'];
            
            if ($pendingRegistrations > 0) {
                $tasks[] = [
                    'type' => 'registration',
                    'title' => 'Đăng ký phòng chờ duyệt',
                    'description' => "Có $pendingRegistrations đăng ký phòng đang chờ duyệt",
                    'count' => $pendingRegistrations,
                    'priority' => 'high'
                ];
            }
            
            // Thanh toán chờ xử lý
            $query = "SELECT COUNT(*) as count FROM payments WHERE status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $pendingPayments = $stmt->fetch()['count'];
            
            if ($pendingPayments > 0) {
                $tasks[] = [
                    'type' => 'payment',
                    'title' => 'Thanh toán chờ xử lý',
                    'description' => "Có $pendingPayments thanh toán đang chờ xử lý",
                    'count' => $pendingPayments,
                    'priority' => 'medium'
                ];
            }
            
            // Yêu cầu bảo trì chờ xử lý
            $query = "SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $pendingMaintenance = $stmt->fetch()['count'];
            
            if ($pendingMaintenance > 0) {
                $tasks[] = [
                    'type' => 'maintenance',
                    'title' => 'Yêu cầu bảo trì',
                    'description' => "Có $pendingMaintenance yêu cầu bảo trì đang chờ xử lý",
                    'count' => $pendingMaintenance,
                    'priority' => 'medium'
                ];
            }
            
            // Phản hồi chưa trả lời
            $query = "SELECT COUNT(*) as count FROM feedback WHERE status = 'new'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $pendingFeedback = $stmt->fetch()['count'];
            
            if ($pendingFeedback > 0) {
                $tasks[] = [
                    'type' => 'feedback',
                    'title' => 'Phản hồi chưa trả lời',
                    'description' => "Có $pendingFeedback phản hồi chưa được trả lời",
                    'count' => $pendingFeedback,
                    'priority' => 'low'
                ];
            }
            
            // Sinh viên sắp hết hạn hợp đồng (trong 30 ngày tới)
            $query = "SELECT COUNT(*) as count 
                     FROM room_registrations 
                     WHERE status = 'active' 
                     AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $expiringContracts = $stmt->fetch()['count'];
            
            if ($expiringContracts > 0) {
                $tasks[] = [
                    'type' => 'contract',
                    'title' => 'Hợp đồng sắp hết hạn',
                    'description' => "Có $expiringContracts hợp đồng sẽ hết hạn trong 30 ngày tới",
                    'count' => $expiringContracts,
                    'priority' => 'medium'
                ];
            }
            
            // Thanh toán quá hạn
            $query = "SELECT COUNT(*) as count 
                     FROM payments 
                     WHERE status = 'pending' 
                     AND payment_date < CURDATE()";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $overduePayments = $stmt->fetch()['count'];
            
            if ($overduePayments > 0) {
                $tasks[] = [
                    'type' => 'overdue',
                    'title' => 'Thanh toán quá hạn',
                    'description' => "Có $overduePayments thanh toán đã quá hạn",
                    'count' => $overduePayments,
                    'priority' => 'high'
                ];
            }
            
            // Sắp xếp theo độ ưu tiên
            $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
            usort($tasks, function($a, $b) use ($priorityOrder) {
                return $priorityOrder[$a['priority']] - $priorityOrder[$b['priority']];
            });
            
            return $tasks;
            
        } catch (Exception $e) {
            $this->logger->error("Get pending tasks error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $adminPendingTasks = new AdminPendingTasks();
    $tasks = $adminPendingTasks->getPendingTasks();
    
    if ($tasks !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'tasks' => $tasks
        ]);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Failed to get pending tasks'
        ]);
    }
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>
