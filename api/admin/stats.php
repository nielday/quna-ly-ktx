<?php
// Suppress errors if not in debug mode
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once dirname(__FILE__) . '/../../config/config.php';
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
    
    // Thống kê chi tiết cho báo cáo
    public function getDetailedStats($startDate = null, $endDate = null) {
        try {
            $stats = [];
            
            // Set default dates nếu không có
            if (!$startDate) {
                $startDate = date('Y-m-01'); // Đầu tháng hiện tại
            }
            if (!$endDate) {
                $endDate = date('Y-m-t'); // Cuối tháng hiện tại
            }
            
            // 1. THỐNG KÊ DOANH THU CHI TIẾT
            $query = "SELECT 
                        COUNT(*) as total_payments,
                        SUM(CASE WHEN payment_type = 'room_fee' THEN amount ELSE 0 END) as room_revenue,
                        SUM(CASE WHEN payment_type = 'utility' THEN amount ELSE 0 END) as utility_revenue,
                        SUM(CASE WHEN payment_type = 'deposit' THEN amount ELSE 0 END) as deposit_revenue,
                        SUM(amount) as total_revenue,
                        AVG(amount) as avg_payment
                     FROM payments 
                     WHERE payment_date BETWEEN :start_date AND :end_date 
                     AND status = 'completed'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $revenueStats = $stmt->fetch();
            $stats['revenue'] = [
                'total_payments' => (int)$revenueStats['total_payments'],
                'room_revenue' => (float)$revenueStats['room_revenue'],
                'utility_revenue' => (float)$revenueStats['utility_revenue'],
                'deposit_revenue' => (float)$revenueStats['deposit_revenue'],
                'total_revenue' => (float)$revenueStats['total_revenue'],
                'avg_payment' => (float)$revenueStats['avg_payment']
            ];
            
            // 2. THỐNG KÊ ĐĂNG KÝ PHÒNG
            // Hiển thị tất cả đăng ký theo trạng thái hiện tại (không filter theo ngày)
            // để biểu đồ phản ánh đúng trạng thái hiện tại của hệ thống
            // Bỏ trạng thái 'completed' vì đó là lịch sử, không phản ánh trạng thái hiện tại
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                     FROM room_registrations";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $registrationStats = $stmt->fetch();
            $stats['registrations'] = [
                'total' => (int)$registrationStats['total'],
                'pending' => (int)$registrationStats['pending'],
                'approved' => (int)$registrationStats['approved'],
                'active' => (int)$registrationStats['active'],
                'checked_in' => (int)$registrationStats['checked_in'],
                'rejected' => (int)$registrationStats['rejected']
            ];
            
            // 3. THỐNG KÊ SINH VIÊN
            $query = "SELECT 
                        COUNT(DISTINCT s.id) as total_students,
                        COUNT(DISTINCT CASE WHEN s.gender = 'male' THEN s.id END) as male_count,
                        COUNT(DISTINCT CASE WHEN s.gender = 'female' THEN s.id END) as female_count,
                        COUNT(DISTINCT s.faculty) as faculty_count
                     FROM students s
                     WHERE s.created_at BETWEEN :start_date AND :end_date";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $studentStats = $stmt->fetch();
            $stats['students'] = [
                'total' => (int)$studentStats['total_students'],
                'male' => (int)$studentStats['male_count'],
                'female' => (int)$studentStats['female_count'],
                'faculty_count' => (int)$studentStats['faculty_count']
            ];
            
            // 4. THỐNG KÊ ĐIỆN NƯỚC
            $query = "SELECT 
                        COUNT(*) as total_readings,
                        SUM(electricity_reading) as total_electricity,
                        SUM(water_reading) as total_water,
                        SUM(total_amount) as total_utility_cost,
                        AVG(total_amount) as avg_utility_cost,
                        SUM(CASE WHEN is_paid = 1 THEN total_amount ELSE 0 END) as paid_amount,
                        SUM(CASE WHEN is_paid = 0 THEN total_amount ELSE 0 END) as unpaid_amount
                     FROM utility_readings
                     WHERE reading_date BETWEEN :start_date AND :end_date";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $utilityStats = $stmt->fetch();
            $stats['utilities'] = [
                'total_readings' => (int)$utilityStats['total_readings'],
                'total_electricity' => (int)$utilityStats['total_electricity'],
                'total_water' => (int)$utilityStats['total_water'],
                'total_cost' => (float)$utilityStats['total_utility_cost'],
                'avg_cost' => (float)$utilityStats['avg_utility_cost'],
                'paid_amount' => (float)$utilityStats['paid_amount'],
                'unpaid_amount' => (float)$utilityStats['unpaid_amount']
            ];
            
            // 5. THỐNG KÊ BẢO TRÌ
            $query = "SELECT 
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent,
                        SUM(COALESCE(actual_cost, 0)) as total_maintenance_cost
                     FROM maintenance_requests
                     WHERE created_at BETWEEN :start_date AND :end_date";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $maintenanceStats = $stmt->fetch();
            $stats['maintenance'] = [
                'total_requests' => (int)$maintenanceStats['total_requests'],
                'pending' => (int)$maintenanceStats['pending'],
                'in_progress' => (int)$maintenanceStats['in_progress'],
                'completed' => (int)$maintenanceStats['completed'],
                'urgent' => (int)$maintenanceStats['urgent'],
                'total_cost' => (float)$maintenanceStats['total_maintenance_cost']
            ];
            
            // 6. THỐNG KÊ PHẢN HỒI
            $query = "SELECT 
                        COUNT(*) as total_feedback,
                        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_feedback,
                        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                        SUM(CASE WHEN category = 'complaint' THEN 1 ELSE 0 END) as complaints,
                        SUM(CASE WHEN category = 'suggestion' THEN 1 ELSE 0 END) as suggestions
                     FROM feedback
                     WHERE created_at BETWEEN :start_date AND :end_date";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $feedbackStats = $stmt->fetch();
            $stats['feedback'] = [
                'total' => (int)$feedbackStats['total_feedback'],
                'new' => (int)$feedbackStats['new_feedback'],
                'resolved' => (int)$feedbackStats['resolved'],
                'complaints' => (int)$feedbackStats['complaints'],
                'suggestions' => (int)$feedbackStats['suggestions']
            ];
            
            // 7. DOANH THU THEO NGÀY (cho biểu đồ)
            $query = "SELECT 
                        DATE(payment_date) as date,
                        SUM(amount) as daily_revenue,
                        COUNT(*) as payment_count
                     FROM payments 
                     WHERE payment_date BETWEEN :start_date AND :end_date 
                     AND status = 'completed'
                     GROUP BY DATE(payment_date)
                     ORDER BY date";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $stats['daily_revenue'] = $stmt->fetchAll();
            
            // 8. PHÂN BỐ SINH VIÊN THEO KHOA
            $query = "SELECT 
                        s.faculty,
                        COUNT(DISTINCT s.id) as student_count
                     FROM students s
                     JOIN room_registrations rr ON s.id = rr.student_id
                     WHERE rr.status = 'active' AND rr.end_date >= CURDATE()
                     GROUP BY s.faculty
                     ORDER BY student_count DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['students_by_faculty'] = $stmt->fetchAll();
            
            // 9. TOP PHÒNG CÓ DOANH THU CAO
            $query = "SELECT 
                        r.room_number,
                        b.name as building_name,
                        SUM(p.amount) as total_revenue,
                        COUNT(p.id) as payment_count
                     FROM payments p
                     JOIN room_registrations rr ON p.room_registration_id = rr.id
                     JOIN rooms r ON rr.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     WHERE p.payment_date BETWEEN :start_date AND :end_date 
                     AND p.status = 'completed'
                     GROUP BY r.id, r.room_number, b.name
                     ORDER BY total_revenue DESC
                     LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $stats['top_rooms'] = $stmt->fetchAll();
            
            return $stats;
            
        } catch (Exception $e) {
            $this->logger->error("Get detailed stats error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Thống kê theo khoảng thời gian
    public function getStatsByPeriod($period = 'month') {
        try {
            $stats = [];
            
            switch ($period) {
                case 'month':
                    // 12 tháng gần nhất
                    $query = "SELECT 
                                DATE_FORMAT(payment_date, '%Y-%m') as period,
                                SUM(amount) as revenue,
                                COUNT(*) as payment_count
                             FROM payments 
                             WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                             AND status = 'completed'
                             GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                             ORDER BY period";
                    break;
                case 'week':
                    // 12 tuần gần nhất
                    $query = "SELECT 
                                CONCAT(YEAR(payment_date), '-W', WEEK(payment_date)) as period,
                                SUM(amount) as revenue,
                                COUNT(*) as payment_count
                             FROM payments 
                             WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
                             AND status = 'completed'
                             GROUP BY YEAR(payment_date), WEEK(payment_date)
                             ORDER BY YEAR(payment_date), WEEK(payment_date)";
                    break;
                case 'year':
                    // 5 năm gần nhất
                    $query = "SELECT 
                                YEAR(payment_date) as period,
                                SUM(amount) as revenue,
                                COUNT(*) as payment_count
                             FROM payments 
                             WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
                             AND status = 'completed'
                             GROUP BY YEAR(payment_date)
                             ORDER BY period";
                    break;
                default:
                    throw new Exception("Invalid period");
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats = $stmt->fetchAll();
            
            return $stats;
            
        } catch (Exception $e) {
            $this->logger->error("Get stats by period error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $adminStats = new AdminStats();
    $action = $_GET['action'] ?? 'overview';
    
    switch ($action) {
        case 'overview':
            // Thống kê tổng quan (dashboard)
            $stats = $adminStats->getStats();
            break;
        case 'detailed':
            // Thống kê chi tiết (báo cáo)
            $startDate = $_GET['start_date'] ?? null;
            $endDate = $_GET['end_date'] ?? null;
            $stats = $adminStats->getDetailedStats($startDate, $endDate);
            break;
        case 'period':
            // Thống kê theo period
            $period = $_GET['period'] ?? 'month';
            $stats = $adminStats->getStatsByPeriod($period);
            break;
        default:
            $stats = $adminStats->getStats();
    }
    
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
