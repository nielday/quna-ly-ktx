<?php
require_once '../config/database.php';
require_once '../config/logger.php';
require_once '../models/Student.php';

class StudentController {
    private $studentModel;
    private $logger;
    
    public function __construct() {
        $this->studentModel = new Student();
        $this->logger = new Logger();
    }
    
    // Lấy danh sách sinh viên
    public function getStudents() {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
        $faculty = $_GET['faculty'] ?? null;
        $gender = $_GET['gender'] ?? null;
        
        $students = $this->studentModel->getStudents($page, $limit, $faculty, $gender);
        $total = $this->studentModel->countStudents($faculty, $gender);
        
        return $this->jsonResponse([
            'success' => true,
            'data' => $students,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    // Lấy thông tin sinh viên theo ID
    public function getStudent($id) {
        $student = $this->studentModel->getStudentById($id);
        
        if ($student) {
            return $this->jsonResponse([
                'success' => true,
                'data' => $student
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Student not found'], 404);
        }
    }
    
    // Tạo sinh viên mới
    public function createStudent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $requiredFields = ['username', 'password', 'email', 'full_name', 'student_code', 'faculty', 'gender'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                return $this->jsonResponse(['error' => "Field $field is required"], 400);
            }
        }
        
        $studentId = $this->studentModel->createStudent($input);
        
        if ($studentId) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Student created successfully',
                'student_id' => $studentId
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to create student'], 500);
        }
    }
    
    // Cập nhật sinh viên
    public function updateStudent($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $success = $this->studentModel->updateStudent($id, $input);
        
        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Student updated successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to update student'], 500);
        }
    }
    
    // Xóa sinh viên
    public function deleteStudent($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $success = $this->studentModel->deleteStudent($id);
        
        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to delete student'], 500);
        }
    }
    
    // Tìm kiếm sinh viên
    public function searchStudents() {
        $keyword = $_GET['keyword'] ?? '';
        $faculty = $_GET['faculty'] ?? null;
        $gender = $_GET['gender'] ?? null;
        
        if (empty($keyword)) {
            return $this->jsonResponse(['error' => 'Keyword is required'], 400);
        }
        
        $students = $this->studentModel->searchStudents($keyword, $faculty, $gender);
        
        return $this->jsonResponse([
            'success' => true,
            'data' => $students
        ]);
    }
    
    // Lấy danh sách khoa
    public function getFaculties() {
        $faculties = $this->studentModel->getFaculties();
        
        return $this->jsonResponse([
            'success' => true,
            'data' => $faculties
        ]);
    }
    
    // Lấy thông tin sinh viên hiện tại (từ session)
    public function getCurrentStudent() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            return $this->jsonResponse(['error' => 'Not authenticated as student'], 401);
        }
        
        $student = $this->studentModel->getStudentByUserId($_SESSION['user_id']);
        
        if ($student) {
            return $this->jsonResponse([
                'success' => true,
                'data' => $student
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Student profile not found'], 404);
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
$controller = new StudentController();
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

switch ($path) {
    case '':
        if ($method === 'GET') {
            $controller->getStudents();
        } elseif ($method === 'POST') {
            $controller->createStudent();
        }
        break;
    case 'search':
        if ($method === 'GET') {
            $controller->searchStudents();
        }
        break;
    case 'faculties':
        if ($method === 'GET') {
            $controller->getFaculties();
        }
        break;
    case 'get_current_student':
        if ($method === 'GET') {
            $controller->getCurrentStudent();
        }
        break;
    default:
        $id = (int)$path;
        if ($method === 'GET') {
            $controller->getStudent($id);
        } elseif ($method === 'PUT') {
            $controller->updateStudent($id);
        } elseif ($method === 'DELETE') {
            $controller->deleteStudent($id);
        }
        break;
}

// Nếu không match với case nào
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
?>
