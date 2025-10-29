<?php
// Suppress errors if not in debug mode
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once dirname(__FILE__) . '/../../config/database.php';
require_once dirname(__FILE__) . '/../../config/logger.php';

class AdminActivities {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy hoạt động gần đây
    public function getRecentActivities($limit = 10) {
        try {
            $query = "SELECT al.*, u.username, u.full_name
                     FROM activity_logs al
                     LEFT JOIN users u ON al.user_id = u.id
                     ORDER BY al.created_at DESC
                     LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $activities = $stmt->fetchAll();
            
            // Format activities
            $formattedActivities = [];
            foreach ($activities as $activity) {
                $formattedActivities[] = [
                    'id' => $activity['id'],
                    'action' => $activity['action'],
                    'description' => $this->formatActivityDescription($activity),
                    'user' => $activity['full_name'] ?: $activity['username'] ?: 'Hệ thống',
                    'created_at' => $activity['created_at'],
                    'ip_address' => $activity['ip_address']
                ];
            }
            
            return $formattedActivities;
            
        } catch (Exception $e) {
            $this->logger->error("Get recent activities error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Format activity description
    private function formatActivityDescription($activity) {
        $action = $activity['action'];
        $tableName = $activity['table_name'];
        $recordId = $activity['record_id'];
        
        $descriptions = [
            'login' => 'Đăng nhập hệ thống',
            'logout' => 'Đăng xuất hệ thống',
            'create_user' => 'Tạo tài khoản mới',
            'update_user' => 'Cập nhật thông tin tài khoản',
            'delete_user' => 'Xóa tài khoản',
            'create_room' => 'Tạo phòng mới',
            'update_room' => 'Cập nhật thông tin phòng',
            'delete_room' => 'Xóa phòng',
            'create_student' => 'Thêm sinh viên mới',
            'update_student' => 'Cập nhật thông tin sinh viên',
            'delete_student' => 'Xóa sinh viên',
            'approve_registration' => 'Duyệt đăng ký phòng',
            'reject_registration' => 'Từ chối đăng ký phòng',
            'create_payment' => 'Tạo thanh toán',
            'update_payment' => 'Cập nhật thanh toán',
            'create_maintenance' => 'Tạo yêu cầu bảo trì',
            'update_maintenance' => 'Cập nhật yêu cầu bảo trì',
            'create_notification' => 'Tạo thông báo mới',
            'create_feedback' => 'Tạo phản hồi'
        ];
        
        $baseDescription = $descriptions[$action] ?? $action;
        
        if ($tableName && $recordId) {
            $baseDescription .= " (ID: $recordId)";
        }
        
        return $baseDescription;
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = $_GET['limit'] ?? 10;
    
    $adminActivities = new AdminActivities();
    $activities = $adminActivities->getRecentActivities($limit);
    
    if ($activities !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'activities' => $activities
        ]);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Failed to get activities'
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
