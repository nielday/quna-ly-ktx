<?php
require_once '../config/database.php';
require_once '../config/logger.php';

class Notification {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Tạo thông báo mới
    public function createNotification($data) {
        try {
            $query = "INSERT INTO notifications 
                     (title, content, type, target_audience, target_users, is_urgent, created_by) 
                     VALUES (:title, :content, :type, :target_audience, :target_users, :is_urgent, :created_by)";
            $stmt = $this->conn->prepare($query);
            
            $targetUsersJson = isset($data['target_users']) ? json_encode($data['target_users']) : null;
            
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':type', $data['type']);
            $stmt->bindParam(':target_audience', $data['target_audience']);
            $stmt->bindParam(':target_users', $targetUsersJson);
            $stmt->bindParam(':is_urgent', $data['is_urgent']);
            $stmt->bindParam(':created_by', $data['created_by']);
            
            if ($stmt->execute()) {
                $notificationId = $this->conn->lastInsertId();
                
                $this->logger->info("Notification created", [
                    'notification_id' => $notificationId,
                    'type' => $data['type']
                ]);
                
                return $notificationId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create notification error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy thông báo theo target audience
    public function getNotifications($targetAudience = null, $type = null, $limit = 50) {
        try {
            $whereConditions = [];
            $params = [];
            
            if ($targetAudience) {
                $whereConditions[] = "(target_audience = :target OR target_audience = 'all')";
                $params[':target'] = $targetAudience;
            } else {
                $whereConditions[] = "target_audience = 'all'";
            }
            
            if ($type) {
                $whereConditions[] = "type = :type";
                $params[':type'] = $type;
            }
            
            $whereClause = "WHERE " . implode(' AND ', $whereConditions);
            
            $query = "SELECT n.*, u.full_name as created_by_name
                     FROM notifications n
                     JOIN users u ON n.created_by = u.id
                     $whereClause
                     ORDER BY n.is_urgent DESC, n.created_at DESC
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get notifications error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy thông báo theo ID
    public function getNotificationById($notificationId) {
        try {
            $query = "SELECT n.*, u.full_name as created_by_name
                     FROM notifications n
                     JOIN users u ON n.created_by = u.id
                     WHERE n.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $notificationId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get notification by ID error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật thông báo
    public function updateNotification($notificationId, $data) {
        try {
            $fields = [];
            $params = [':id' => $notificationId];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['title', 'content', 'type', 'is_urgent'])) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (!empty($fields)) {
                $query = "UPDATE notifications SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                
                if ($stmt->execute($params)) {
                    $this->logger->info("Notification updated", ['notification_id' => $notificationId]);
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update notification error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Xóa thông báo
    public function deleteNotification($notificationId) {
        try {
            $query = "DELETE FROM notifications WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $notificationId);
            
            if ($stmt->execute()) {
                $this->logger->info("Notification deleted", ['notification_id' => $notificationId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Delete notification error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
