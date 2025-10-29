<?php
// Suppress errors if not in debug mode
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once dirname(__FILE__) . '/../../config/database.php';
require_once dirname(__FILE__) . '/../../config/logger.php';

class AdminStats {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy thống kê tổng quan
    public function getStats() {
        try {
            $stats = [];
            
            // Tổng số phòng
            $query = "SELECT COUNT(*) as total FROM rooms";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_rooms'] = $stmt->fetch()['total'];
            
            // Sinh viên đang ở
            $query = "SELECT COUNT(DISTINCT rr.student_id) as total 
                     FROM room_registrations rr 
                     WHERE rr.status = 'active' AND rr.end_date >= CURDATE()";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_students'] = $stmt->fetch()['total'];
            
            // Phòng trống
            $query = "SELECT COUNT(*) as total FROM rooms WHERE status = 'available'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['available_rooms'] = $stmt->fetch()['total'];
            
            // Doanh thu tháng hiện tại
            $query = "SELECT COALESCE(SUM(amount), 0) as total 
                     FROM payments 
                     WHERE MONTH(payment_date) = MONTH(CURDATE()) 
                     AND YEAR(payment_date) = YEAR(CURDATE()) 
                     AND status = 'completed'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['monthly_revenue'] = $stmt->fetch()['total'];
            
            // Doanh thu theo tháng (12 tháng gần nhất)
            $query = "SELECT MONTH(payment_date) as month, COALESCE(SUM(amount), 0) as revenue
                     FROM payments 
                     WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                     AND status = 'completed'
                     GROUP BY YEAR(payment_date), MONTH(payment_date)
                     ORDER BY YEAR(payment_date), MONTH(payment_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $monthlyRevenue = $stmt->fetchAll();
            
            // Tạo array 12 tháng
            $revenueByMonth = array_fill(1, 12, 0);
            foreach ($monthlyRevenue as $revenue) {
                $revenueByMonth[$revenue['month']] = (float)$revenue['revenue'];
            }
            $stats['monthly_revenue_data'] = array_values($revenueByMonth);
            
            // Thống kê trạng thái phòng
            $query = "SELECT status, COUNT(*) as count FROM rooms GROUP BY status";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $roomStatus = $stmt->fetchAll();
            
            $roomStatusData = [
                'available' => 0,
                'full' => 0,
                'maintenance' => 0,
                'reserved' => 0
            ];
            
            foreach ($roomStatus as $status) {
                $roomStatusData[$status['status']] = (int)$status['count'];
            }
            $stats['room_status_data'] = $roomStatusData;
            
            return $stats;
            
        } catch (Exception $e) {
            $this->logger->error("Get admin stats error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $adminStats = new AdminStats();
    $stats = $adminStats->getStats();
    
    if ($stats) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Failed to get statistics'
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
