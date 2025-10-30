<?php
require_once dirname(__FILE__) . '/../config/config.php';
require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/logger.php';

class ActivityLog {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Ghi log hoạt động
    public function logActivity($userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
        try {
            $query = "INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
                     VALUES (:user_id, :action, :table_name, :record_id, :old_values, :new_values, :ip_address, :user_agent)";
            $stmt = $this->conn->prepare($query);
            
            // Convert bindParam to bindValue to avoid reference issues with computed values
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':action', $action, PDO::PARAM_STR);
            $stmt->bindValue(':table_name', $tableName, PDO::PARAM_STR);
            $stmt->bindValue(':record_id', $recordId, PDO::PARAM_INT);
            $stmt->bindValue(':old_values', $oldValues ? json_encode($oldValues) : null, PDO::PARAM_STR);
            $stmt->bindValue(':new_values', $newValues ? json_encode($newValues) : null, PDO::PARAM_STR);
            $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? null, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (Exception $e) {
            $this->logger->error("Log activity error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy lịch sử hoạt động
    public function getActivityLogs($page = 1, $limit = RECORDS_PER_PAGE, $userId = null, $action = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];
            
            if ($userId) {
                $whereConditions[] = "al.user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            
            if ($action) {
                $whereConditions[] = "al.action = :action";
                $params[':action'] = $action;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT al.*, u.username, u.full_name
                     FROM activity_logs al
                     LEFT JOIN users u ON al.user_id = u.id
                     $whereClause
                     ORDER BY al.created_at DESC
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get activity logs error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đếm tổng số logs
    public function countActivityLogs($userId = null, $action = null) {
        try {
            $whereConditions = [];
            $params = [];
            
            if ($userId) {
                $whereConditions[] = "user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            
            if ($action) {
                $whereConditions[] = "action = :action";
                $params[':action'] = $action;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM activity_logs $whereClause";
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'];
        } catch (Exception $e) {
            $this->logger->error("Count activity logs error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
?>
