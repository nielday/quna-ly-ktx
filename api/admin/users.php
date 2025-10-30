<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../config/logger.php';
require_once '../../models/User.php';
require_once '../../models/Student.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

class UserManagementController {
    private $userModel;
    private $studentModel;
    private $logger;
    
    public function __construct() {
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->logger = new Logger();
    }
    
    // Lấy danh sách users
    public function getUsers() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $role = isset($_GET['role']) ? $_GET['role'] : null;
            $search = isset($_GET['search']) ? $_GET['search'] : null;
            
            if ($search) {
                $users = $this->searchUsers($search, $role);
                $total = count($users);
            } else {
                $users = $this->userModel->getUsers($page, $limit, $role);
                $total = $this->userModel->countUsers($role);
            }
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $users,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (Exception $e) {
            $this->logger->error("Get users error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to get users'], 500);
        }
    }
    
    // Tìm kiếm users
    private function searchUsers($keyword, $role = null) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $whereConditions = ["(u.username LIKE :keyword OR u.email LIKE :keyword OR u.full_name LIKE :keyword)"];
            $params = [':keyword' => "%$keyword%"];
            
            if ($role) {
                $whereConditions[] = "u.role = :role";
                $params[':role'] = $role;
            }
            
            $whereClause = "WHERE " . implode(' AND ', $whereConditions);
            
            $query = "SELECT id, username, email, full_name, role, phone, is_active, created_at 
                     FROM users $whereClause 
                     ORDER BY created_at DESC";
            
            $stmt = $conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Search users error", ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    // Lấy thông tin user theo ID
    public function getUserById() {
        try {
            $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if (!$userId) {
                return $this->jsonResponse(['error' => 'User ID is required'], 400);
            }
            
            $user = $this->userModel->getUserById($userId);
            
            if ($user) {
                // Nếu là student, lấy thêm thông tin sinh viên
                if ($user['role'] === 'student') {
                    $student = $this->studentModel->getStudentByUserId($userId);
                    if ($student) {
                        $user['student_info'] = $student;
                    }
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $user
                ]);
            } else {
                return $this->jsonResponse(['error' => 'User not found'], 404);
            }
        } catch (Exception $e) {
            $this->logger->error("Get user by ID error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to get user'], 500);
        }
    }
    
    // Tạo user mới
    public function createUser() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['username', 'email', 'password', 'full_name', 'role'];
            foreach ($required as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    return $this->jsonResponse(['error' => ucfirst($field) . ' is required'], 400);
                }
            }
            
            // Validate role
            if (!in_array($input['role'], ['admin', 'staff', 'student'])) {
                return $this->jsonResponse(['error' => 'Invalid role'], 400);
            }
            
            // Validate password strength
            if (strlen($input['password']) < 6) {
                return $this->jsonResponse(['error' => 'Password must be at least 6 characters'], 400);
            }
            
            // Nếu role là student, cần kiểm tra thêm
            if ($input['role'] === 'student') {
                // Kiểm tra username có tồn tại không
                if ($this->userModel->usernameExists($input['username'])) {
                    return $this->jsonResponse(['error' => 'Username already exists'], 400);
                }
                if ($this->userModel->emailExists($input['email'])) {
                    return $this->jsonResponse(['error' => 'Email already exists'], 400);
                }
                
                // Sử dụng method createStudent để tạo đồng thời user + student
                $studentData = [
                    'username' => $input['username'],
                    'email' => $input['email'],
                    'password' => $input['password'],
                    'full_name' => $input['full_name'],
                    'phone' => $input['phone'] ?? '',
                    // Các trường sinh viên gán giá trị tạm thời, sinh viên sẽ tự cập nhật sau
                    'student_code' => 'TEMP_' . substr(time(), -8), // 13 ký tự: TEMP_ + 8 số cuối timestamp
                    'faculty' => 'Chưa xác định',
                    'class_name' => null,
                    'gender' => null, // NULL để sinh viên tự chọn sau
                    'date_of_birth' => null,
                    'hometown' => null,
                    'emergency_contact' => null,
                    'emergency_phone' => null,
                    'id_card' => null
                ];
                
                $studentId = $this->studentModel->createStudent($studentData);
                
                if ($studentId) {
                    $this->logger->info("Admin created student user", [
                        'admin_id' => $_SESSION['user_id'],
                        'new_student_id' => $studentId,
                        'username' => $input['username']
                    ]);
                    
                    return $this->jsonResponse([
                        'success' => true,
                        'message' => 'Student user created successfully. Student can fill in additional info later.',
                        'student_id' => $studentId
                    ]);
                } else {
                    // Log chi tiết để debug
                    $this->logger->error("Failed to create student user", [
                        'input' => $input,
                        'student_data' => $studentData
                    ]);
                    return $this->jsonResponse(['error' => 'Failed to create student user. Check logs for details.'], 400);
                }
            } else {
                // Tạo user bình thường cho admin/staff
                $userData = [
                    'username' => $input['username'],
                    'email' => $input['email'],
                    'password' => $input['password'],
                    'full_name' => $input['full_name'],
                    'role' => $input['role'],
                    'phone' => $input['phone'] ?? ''
                ];
                
                $userId = $this->userModel->createUser($userData);
                
                if ($userId) {
                    $this->logger->info("Admin created user", [
                        'admin_id' => $_SESSION['user_id'],
                        'new_user_id' => $userId,
                        'role' => $input['role']
                    ]);
                    
                    return $this->jsonResponse([
                        'success' => true,
                        'message' => 'User created successfully',
                        'user_id' => $userId
                    ]);
                } else {
                    return $this->jsonResponse(['error' => 'Failed to create user. Username or email may already exist.'], 400);
                }
            }
        } catch (Exception $e) {
            $this->logger->error("Create user error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }
    
    // Cập nhật user
    public function updateUser() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || !$input['id']) {
                return $this->jsonResponse(['error' => 'User ID is required'], 400);
            }
            
            $userId = (int)$input['id'];
            
            // Không cho phép admin tự xóa quyền admin của chính mình
            if ($userId == $_SESSION['user_id'] && isset($input['role']) && $input['role'] !== 'admin') {
                return $this->jsonResponse(['error' => 'Cannot change your own role'], 400);
            }
            
            // Cập nhật thông tin cơ bản
            $updateData = [];
            $allowedFields = ['email', 'full_name', 'phone'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateData[$field] = $input[$field];
                }
            }
            
            if (!empty($updateData)) {
                $success = $this->userModel->updateUser($userId, $updateData);
                
                if ($success) {
                    $this->logger->info("Admin updated user", [
                        'admin_id' => $_SESSION['user_id'],
                        'updated_user_id' => $userId,
                        'fields' => array_keys($updateData)
                    ]);
                    
                    return $this->jsonResponse([
                        'success' => true,
                        'message' => 'User updated successfully'
                    ]);
                }
            }
            
            return $this->jsonResponse(['error' => 'No changes made or update failed'], 400);
        } catch (Exception $e) {
            $this->logger->error("Update user error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }
    
    // Xóa user
    public function deleteUser() {
        try {
            $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if (!$userId) {
                return $this->jsonResponse(['error' => 'User ID is required'], 400);
            }
            
            // Không cho phép xóa chính mình
            if ($userId == $_SESSION['user_id']) {
                return $this->jsonResponse(['error' => 'Cannot delete your own account'], 400);
            }
            
            $database = new Database();
            $conn = $database->getConnection();
            
            // Xóa user (cascade sẽ xóa các bản ghi liên quan)
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $userId);
            
            if ($stmt->execute()) {
                $this->logger->info("Admin deleted user", [
                    'admin_id' => $_SESSION['user_id'],
                    'deleted_user_id' => $userId
                ]);
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to delete user'], 500);
            }
        } catch (Exception $e) {
            $this->logger->error("Delete user error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to delete user: ' . $e->getMessage()], 500);
        }
    }
    
    // Toggle trạng thái active/inactive
    public function toggleUserStatus() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || !$input['id']) {
                return $this->jsonResponse(['error' => 'User ID is required'], 400);
            }
            
            $userId = (int)$input['id'];
            
            // Không cho phép vô hiệu hóa chính mình
            if ($userId == $_SESSION['user_id']) {
                return $this->jsonResponse(['error' => 'Cannot deactivate your own account'], 400);
            }
            
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "UPDATE users SET is_active = NOT is_active, updated_at = NOW() WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $userId);
            
            if ($stmt->execute()) {
                $this->logger->info("Admin toggled user status", [
                    'admin_id' => $_SESSION['user_id'],
                    'user_id' => $userId
                ]);
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'User status updated successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to update status'], 500);
            }
        } catch (Exception $e) {
            $this->logger->error("Toggle user status error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }
    
    // Reset password
    public function resetPassword() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || !$input['id']) {
                return $this->jsonResponse(['error' => 'User ID is required'], 400);
            }
            
            if (!isset($input['new_password']) || strlen($input['new_password']) < 6) {
                return $this->jsonResponse(['error' => 'New password must be at least 6 characters'], 400);
            }
            
            $userId = (int)$input['id'];
            $newPassword = $input['new_password'];
            
            $database = new Database();
            $conn = $database->getConnection();
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $query = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $userId);
            
            if ($stmt->execute()) {
                $this->logger->info("Admin reset user password", [
                    'admin_id' => $_SESSION['user_id'],
                    'user_id' => $userId
                ]);
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Password reset successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to reset password'], 500);
            }
        } catch (Exception $e) {
            $this->logger->error("Reset password error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to reset password: ' . $e->getMessage()], 500);
        }
    }
    
    // Thống kê users theo role
    public function getUserStats() {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "SELECT 
                        role,
                        COUNT(*) as total,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
                     FROM users
                     GROUP BY role";
            
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $stats = $stmt->fetchAll();
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            $this->logger->error("Get user stats error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Failed to get stats'], 500);
        }
    }
    
    // JSON response helper
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

// Xử lý request
$controller = new UserManagementController();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'stats') {
                $controller->getUserStats();
            } elseif (isset($_GET['id'])) {
                $controller->getUserById();
            } else {
                $controller->getUsers();
            }
            break;
            
        case 'POST':
            if ($action === 'toggle-status') {
                $controller->toggleUserStatus();
            } elseif ($action === 'reset-password') {
                $controller->resetPassword();
            } else {
                $controller->createUser();
            }
            break;
            
        case 'PUT':
            $controller->updateUser();
            break;
            
        case 'DELETE':
            $controller->deleteUser();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>

