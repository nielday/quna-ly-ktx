<?php
require_once '../config/database.php';
require_once '../config/logger.php';

class User {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Đăng nhập
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, password, email, full_name, role, is_active 
                     FROM users WHERE username = :username AND is_active = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    // Ghi log đăng nhập thành công
                    $this->logger->info("User login successful", [
                        'user_id' => $user['id'],
                        'username' => $username,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                    
                    return $user;
                } else {
                    $this->logger->warning("Login failed - wrong password", [
                        'username' => $username,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                }
            } else {
                $this->logger->warning("Login failed - user not found", [
                    'username' => $username,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Login error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đổi mật khẩu
    public function changePassword($userId, $oldPassword, $newPassword) {
        try {
            // Kiểm tra mật khẩu cũ
            $query = "SELECT password FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if (password_verify($oldPassword, $user['password'])) {
                    // Cập nhật mật khẩu mới
                    $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO);
                    
                    $updateQuery = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->bindParam(':password', $hashedPassword);
                    $updateStmt->bindParam(':id', $userId);
                    
                    if ($updateStmt->execute()) {
                        $this->logger->info("Password changed successfully", ['user_id' => $userId]);
                        return true;
                    }
                } else {
                    $this->logger->warning("Password change failed - wrong old password", ['user_id' => $userId]);
                }
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Password change error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy thông tin user theo ID
    public function getUserById($userId) {
        try {
            $query = "SELECT id, username, email, full_name, role, phone, avatar, is_active, created_at 
                     FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get user by ID error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Cập nhật thông tin user
    public function updateUser($userId, $data) {
        try {
            $fields = [];
            $params = [':id' => $userId];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['email', 'full_name', 'phone', 'avatar'])) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (!empty($fields)) {
                $query = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                
                if ($stmt->execute($params)) {
                    $this->logger->info("User updated successfully", ['user_id' => $userId, 'fields' => array_keys($data)]);
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Update user error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tạo user mới
    public function createUser($data) {
        try {
            $hashedPassword = password_hash($data['password'], PASSWORD_HASH_ALGO);
            
            $query = "INSERT INTO users (username, password, email, full_name, role, phone) 
                     VALUES (:username, :password, :email, :full_name, :role, :phone)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':phone', $data['phone']);
            
            if ($stmt->execute()) {
                $userId = $this->conn->lastInsertId();
                $this->logger->info("User created successfully", ['user_id' => $userId, 'username' => $data['username']]);
                return $userId;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create user error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy danh sách users với phân trang
    public function getUsers($page = 1, $limit = RECORDS_PER_PAGE, $role = null) {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = $role ? "WHERE role = :role" : "";
            $params = [];
            
            if ($role) {
                $params[':role'] = $role;
            }
            
            $query = "SELECT id, username, email, full_name, role, phone, is_active, created_at 
                     FROM users $whereClause 
                     ORDER BY created_at DESC 
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
            $this->logger->error("Get users error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đếm tổng số users
    public function countUsers($role = null) {
        try {
            $whereClause = $role ? "WHERE role = :role" : "";
            $params = [];
            
            if ($role) {
                $params[':role'] = $role;
            }
            
            $query = "SELECT COUNT(*) as total FROM users $whereClause";
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'];
        } catch (Exception $e) {
            $this->logger->error("Count users error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
?>
