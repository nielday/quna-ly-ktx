<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/User.php';

class AuthController {
    private $userModel;
    private $logger;
    
    public function __construct() {
        $this->userModel = new User();
        $this->logger = new Logger();
    }
    
    // Xử lý đăng nhập
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['username']) || !isset($input['password'])) {
            return $this->jsonResponse(['error' => 'Username and password are required'], 400);
        }
        
        $user = $this->userModel->login($input['username'], $input['password']);
        
        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            return $this->jsonResponse([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Invalid username or password'], 401);
        }
    }
    
    // Xử lý đăng xuất
    public function logout() {
        session_start();
        $role = $_SESSION['role'] ?? 'student';
        
        // Xóa tất cả session variables
        $_SESSION = array();
        
        // Xóa session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect về trang login phù hợp với role
        if ($role === 'admin' || $role === 'staff') {
            header('Location: ../views/auth/admin-login.php?logout=success');
        } else {
            header('Location: ../views/auth/student-login.php?logout=success');
        }
        exit();
    }
    
    // Kiểm tra trạng thái đăng nhập
    public function checkAuth() {
        session_start();
        
        if (isset($_SESSION['user_id'])) {
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            
            if ($user) {
                return $this->jsonResponse([
                    'authenticated' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'full_name' => $user['full_name'],
                        'role' => $user['role']
                    ]
                ]);
            }
        }
        
        return $this->jsonResponse(['authenticated' => false], 401);
    }
    
    // Đổi mật khẩu
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return $this->jsonResponse(['error' => 'Not authenticated'], 401);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['old_password']) || !isset($input['new_password'])) {
            return $this->jsonResponse(['error' => 'Old password and new password are required'], 400);
        }
        
        $success = $this->userModel->changePassword(
            $_SESSION['user_id'],
            $input['old_password'],
            $input['new_password']
        );
        
        if ($success) {
            return $this->jsonResponse(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            return $this->jsonResponse(['error' => 'Failed to change password'], 400);
        }
    }
    
    // Đăng ký tài khoản sinh viên
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        require_once '../models/Student.php';
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['username', 'email', 'password', 'fullName', 'phone', 'studentCode', 'gender', 'dateOfBirth', 'faculty'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                return $this->jsonResponse(['error' => ucfirst($field) . ' is required'], 400);
            }
        }
        
        // Check password match
        if ($input['password'] !== ($input['confirmPassword'] ?? '')) {
            return $this->jsonResponse(['error' => 'Passwords do not match'], 400);
        }
        
        // Check password strength
        if (strlen($input['password']) < 6) {
            return $this->jsonResponse(['error' => 'Password must be at least 6 characters'], 400);
        }
        
        // Prepare student data
        $studentData = [
            'username' => $input['username'],
            'password' => $input['password'],
            'email' => $input['email'],
            'full_name' => $input['fullName'],
            'phone' => $input['phone'],
            'student_code' => $input['studentCode'],
            'gender' => $input['gender'],
            'date_of_birth' => $input['dateOfBirth'],
            'faculty' => $input['faculty'],
            'class_name' => $input['className'] ?? '',
            'hometown' => $input['hometown'] ?? '',
            'emergency_contact' => $input['emergencyContact'] ?? '',
            'emergency_phone' => $input['emergencyPhone'] ?? '',
            'id_card' => $input['idCard'] ?? ''
        ];
        
        try {
            $studentModel = new Student();
            $result = $studentModel->createStudent($studentData);
            
            if ($result) {
                $this->logger->info("Student registered successfully", [
                    'username' => $input['username'],
                    'student_code' => $input['studentCode']
                ]);
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Registration successful',
                    'user_id' => $result
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Registration failed. Username or email may already exist.'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error("Registration error", ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Registration failed: ' . $e->getMessage()], 400);
        }
    }
    
    // Trả về JSON response
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

// Xử lý request
$controller = new AuthController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $controller->login();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'check':
        $controller->checkAuth();
        break;
    case 'change-password':
        $controller->changePassword();
        break;
    case 'register':
        $controller->register();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Action not found']);
        break;
}
?>
