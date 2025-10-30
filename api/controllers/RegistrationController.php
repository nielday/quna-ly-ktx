<?php
require_once dirname(__FILE__) . '/../../config/config.php';
require_once dirname(__FILE__) . '/../../config/database.php';
require_once dirname(__FILE__) . '/../../config/logger.php';
require_once dirname(__FILE__) . '/../../models/RoomRegistration.php';
require_once dirname(__FILE__) . '/../../models/Room.php';
require_once dirname(__FILE__) . '/../../models/Student.php';
require_once dirname(__FILE__) . '/../../config/activity_helper.php';

class RegistrationController {
    private $registrationModel;
    private $roomModel;
    private $studentModel;
    private $logger;
    
    public function __construct() {
        $this->registrationModel = new RoomRegistration();
        $this->roomModel = new Room();
        $this->studentModel = new Student();
        $this->logger = new Logger();
    }
    
    /**
     * Tạo đăng ký phòng mới
     */
    public function createRegistration() {
        // Kiểm tra method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        // Lấy dữ liệu từ input
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate dữ liệu
        $requiredFields = ['student_id', 'room_id', 'start_date', 'end_date'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                return $this->jsonResponse(['error' => "Field $field is required"], 400);
            }
        }
        
        // Validate dates
        if (strtotime($input['start_date']) >= strtotime($input['end_date'])) {
            return $this->jsonResponse(['error' => 'End date must be after start date'], 400);
        }
        
        // Validate start date không được quá khứ
        if (strtotime($input['start_date']) < strtotime('today')) {
            return $this->jsonResponse(['error' => 'Start date cannot be in the past'], 400);
        }
        
        try {
            // Kiểm tra room tồn tại và available
            $room = $this->roomModel->getRoomById($input['room_id']);
            if (!$room) {
                return $this->jsonResponse(['error' => 'Room not found'], 404);
            }
            
            if ($room['status'] !== 'available') {
                return $this->jsonResponse(['error' => 'Room is not available'], 400);
            }
            
            // Tạo đăng ký
            $registrationId = $this->registrationModel->createRegistration(
                $input['student_id'],
                $input['room_id'],
                $input['start_date'],
                $input['end_date'],
                $input['notes'] ?? ''
            );
            
            if ($registrationId) {
                // Ghi log activity (try-catch để không làm gián đoạn nếu lỗi)
                try {
                    autoLogActivity('create', 'room_registrations', $registrationId, null, $input);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Registration created successfully',
                    'registration_id' => $registrationId
                ], 201);
            } else {
                return $this->jsonResponse(['error' => 'Failed to create registration. Room may be full or student already has a registration.'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Create registration error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Lấy danh sách đăng ký (cho admin/staff)
     */
    public function getRegistrations($status = null) {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? RECORDS_PER_PAGE;
        
        try {
            $registrations = $this->registrationModel->getRegistrations($page, $limit, $status);
            
            if ($registrations !== false) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $registrations,
                    'pagination' => [
                        'current_page' => (int)$page,
                        'per_page' => (int)$limit
                    ]
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to get registrations'], 500);
            }
        } catch (Exception $e) {
            $this->logger->error('Get registrations error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Lấy đăng ký của sinh viên hiện tại
     */
    public function getMyRegistrations($userId) {
        try {
            // Lấy student profile
            $student = $this->studentModel->getStudentByUserId($userId);
            
            if (!$student) {
                return $this->jsonResponse(['error' => 'Student profile not found'], 404);
            }
            
            $registrations = $this->registrationModel->getMyRegistrations($student['id']);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $registrations
            ]);
        } catch (Exception $e) {
            $this->logger->error('Get my registrations error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Duyệt đăng ký
     */
    public function approveRegistration($registrationId, $approvedBy) {
        // Kiểm tra method
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        try {
            $result = $this->registrationModel->approveRegistration($registrationId, $approvedBy);
            
            if ($result) {
                // Ghi log activity (try-catch để không làm gián đoạn nếu lỗi)
                try {
                    autoLogActivity('approve', 'room_registrations', $registrationId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Registration approved successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to approve registration'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Approve registration error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Từ chối đăng ký
     */
    public function rejectRegistration($registrationId) {
        // Kiểm tra method
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        // Lấy lý do từ chối (optional)
        $input = json_decode(file_get_contents('php://input'), true);
        $reason = $input['reason'] ?? '';
        
        try {
            $result = $this->registrationModel->rejectRegistration($registrationId, $reason);
            
            if ($result) {
                // Ghi log activity (try-catch để không làm gián đoạn nếu lỗi)
                try {
                    autoLogActivity('reject', 'room_registrations', $registrationId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Registration rejected successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to reject registration'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Reject registration error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Gia hạn hợp đồng
     */
    public function extendContract($registrationId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['new_end_date'])) {
            return $this->jsonResponse(['error' => 'new_end_date is required'], 400);
        }
        
        try {
            $result = $this->registrationModel->extendContract($registrationId, $input['new_end_date']);
            
            if ($result) {
                // Ghi log activity (try-catch để không làm gián đoạn nếu lỗi)
                try {
                    autoLogActivity('extend', 'room_registrations', $registrationId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Contract extended successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to extend contract'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Extend contract error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Trả phòng
     */
    public function checkOut($registrationId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        try {
            $result = $this->registrationModel->checkOut($registrationId);
            
            if ($result) {
                // Ghi log activity (try-catch để không làm gián đoạn nếu lỗi)
                try {
                    autoLogActivity('checkout', 'room_registrations', $registrationId);
                } catch (Exception $e) {
                    // Silent fail for logging
                }
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Check out completed successfully'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to check out'], 400);
            }
        } catch (Exception $e) {
            $this->logger->error('Check out error', ['error' => $e->getMessage()]);
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Trả về JSON response
     */
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>

