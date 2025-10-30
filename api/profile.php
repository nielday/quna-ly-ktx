<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/User.php';
require_once '../models/Student.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$action = $_GET['action'] ?? '';
$logger = new Logger();

switch ($action) {
    case 'get':
        getProfile();
        break;
    case 'update':
        updateProfile();
        break;
    case 'change-password':
        changePassword();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Action not found']);
        break;
}

// Lấy thông tin profile
function getProfile() {
    global $logger;
    
    try {
        $userModel = new User();
        $studentModel = new Student();
        
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        $response = [
            'success' => true,
            'user' => [
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone'],
                'role' => $user['role']
            ]
        ];
        
        // Nếu là sinh viên, lấy thêm thông tin sinh viên
        if ($user['role'] === 'student') {
            $student = $studentModel->getStudentByUserId($_SESSION['user_id']);
            if ($student) {
                $response['student'] = [
                    'student_code' => $student['student_code'],
                    'faculty' => $student['faculty'],
                    'class_name' => $student['class_name'],
                    'gender' => $student['gender'],
                    'date_of_birth' => $student['date_of_birth'],
                    'hometown' => $student['hometown'],
                    'emergency_contact' => $student['emergency_contact'],
                    'emergency_phone' => $student['emergency_phone'],
                    'id_card' => $student['id_card']
                ];
            }
        }
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        $logger->error("Get profile error", ['error' => $e->getMessage()]);
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// Cập nhật thông tin profile
function updateProfile() {
    global $logger;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            return;
        }
        
        $userModel = new User();
        $studentModel = new Student();
        
        // Cập nhật thông tin user
        $userData = [];
        if (isset($input['email'])) $userData['email'] = $input['email'];
        if (isset($input['full_name'])) $userData['full_name'] = $input['full_name'];
        if (isset($input['phone'])) $userData['phone'] = $input['phone'];
        
        if (!empty($userData)) {
            $userModel->updateUser($_SESSION['user_id'], $userData);
        }
        
        // Nếu là sinh viên, cập nhật thông tin sinh viên
        if ($_SESSION['role'] === 'student') {
            $student = $studentModel->getStudentByUserId($_SESSION['user_id']);
            if ($student) {
                $studentData = [];
                
                // Cho phép cập nhật student_code và gender CHỈ KHI chưa có hoặc là giá trị tạm
                // Kiểm tra xem có phải tài khoản mới tạo chưa điền thông tin không
                $isNewAccount = (strpos($student['student_code'], 'TEMP_') === 0) || 
                                ($student['faculty'] === 'Chưa xác định');
                
                // Student code: cho phép sửa nếu empty HOẶC bắt đầu bằng "TEMP_" (do admin tạo)
                if (isset($input['student_code']) && 
                    (empty($student['student_code']) || strpos($student['student_code'], 'TEMP_') === 0)) {
                    $studentData['student_code'] = $input['student_code'];
                }
                
                // Gender: cho phép sửa NẾU là tài khoản mới (faculty = "Chưa xác định")
                if (isset($input['gender']) && $isNewAccount) {
                    $studentData['gender'] = $input['gender'];
                }
                
                // Các trường khác luôn được phép cập nhật
                if (isset($input['faculty'])) $studentData['faculty'] = $input['faculty'];
                if (isset($input['class_name'])) $studentData['class_name'] = $input['class_name'];
                if (isset($input['date_of_birth'])) $studentData['date_of_birth'] = $input['date_of_birth'];
                if (isset($input['hometown'])) $studentData['hometown'] = $input['hometown'];
                if (isset($input['emergency_contact'])) $studentData['emergency_contact'] = $input['emergency_contact'];
                if (isset($input['emergency_phone'])) $studentData['emergency_phone'] = $input['emergency_phone'];
                if (isset($input['id_card'])) $studentData['id_card'] = $input['id_card'];
                
                if (!empty($studentData)) {
                    $studentModel->updateStudent($student['id'], $studentData);
                }
            }
        }
        
        $logger->info("Profile updated", ['user_id' => $_SESSION['user_id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!'
        ]);
        
    } catch (Exception $e) {
        $logger->error("Update profile error", ['error' => $e->getMessage()]);
        http_response_code(500);
        echo json_encode(['error' => 'Lỗi khi cập nhật thông tin']);
    }
}

// Đổi mật khẩu
function changePassword() {
    global $logger;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['old_password']) || !isset($input['new_password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin']);
            return;
        }
        
        if (strlen($input['new_password']) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'Mật khẩu mới phải có ít nhất 6 ký tự']);
            return;
        }
        
        $userModel = new User();
        $result = $userModel->changePassword(
            $_SESSION['user_id'],
            $input['old_password'],
            $input['new_password']
        );
        
        if ($result) {
            $logger->info("Password changed", ['user_id' => $_SESSION['user_id']]);
            echo json_encode([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Mật khẩu cũ không đúng']);
        }
        
    } catch (Exception $e) {
        $logger->error("Change password error", ['error' => $e->getMessage()]);
        http_response_code(500);
        echo json_encode(['error' => 'Lỗi khi đổi mật khẩu']);
    }
}
?>

