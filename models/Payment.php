<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/logger.php';

class Payment {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Tạo thanh toán
    public function createPayment($studentId, $roomRegistrationId, $paymentType, $amount, $paymentDate, $paymentMethod, $referenceNumber = '', $notes = '') {
        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO payments (student_id, room_registration_id, payment_type, amount, 
                     payment_date, payment_method, reference_number, notes) 
                     VALUES (:student_id, :room_registration_id, :payment_type, :amount, :payment_date, 
                             :payment_method, :reference_number, :notes)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':room_registration_id', $roomRegistrationId);
            $stmt->bindParam(':payment_type', $paymentType);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_date', $paymentDate);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':reference_number', $referenceNumber);
            $stmt->bindParam(':notes', $notes);
            
            if ($stmt->execute()) {
                $paymentId = $this->conn->lastInsertId();
                
                // Nếu thanh toán tiền điện nước thì đánh dấu đã thanh toán
                if ($paymentType === 'utility') {
                    $this->markUtilityAsPaid($roomRegistrationId);
                }
                
                $this->conn->commit();
                
                $this->logger->info("Payment created", [
                    'payment_id' => $paymentId,
                    'student_id' => $studentId,
                    'amount' => $amount
                ]);
                
                return $paymentId;
            }
            
            $this->conn->rollBack();
            return false;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Create payment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đánh dấu điện nước đã thanh toán
    private function markUtilityAsPaid($roomRegistrationId, $paymentDate) {
        try {
            // Lấy room_id từ registration
            $query = "SELECT room_id FROM room_registrations WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $roomRegistrationId);
            $stmt->execute();
            $registration = $stmt->fetch();
            
            if ($registration) {
                // Chỉ update reading của tháng cụ thể (dựa vào payment_date)
                // Format: YYYY-MM (tháng và năm)
                $monthYear = date('Y-m', strtotime($paymentDate));
                
                $updateQuery = "UPDATE utility_readings 
                               SET is_paid = TRUE, updated_at = NOW() 
                               WHERE room_id = :room_id 
                               AND DATE_FORMAT(reading_date, '%Y-%m') = :month_year
                               AND is_paid = FALSE";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':room_id', $registration['room_id']);
                $updateStmt->bindParam(':month_year', $monthYear);
                $updateStmt->execute();
                
                $this->logger->info("Marked utility as paid", [
                    'room_id' => $registration['room_id'],
                    'month_year' => $monthYear,
                    'rows_affected' => $updateStmt->rowCount()
                ]);
            }
        } catch (Exception $e) {
            // Log nhưng không throw để không ảnh hưởng transaction chính
            $this->logger->warning("Mark utility as paid error", ['error' => $e->getMessage()]);
        }
    }
    
    // Xác nhận thanh toán
    public function confirmPayment($paymentId) {
        try {
            $query = "UPDATE payments 
                     SET status = 'completed', updated_at = NOW() 
                     WHERE id = :id AND status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $paymentId);
            
            if ($stmt->execute()) {
                $this->logger->info("Payment confirmed", ['payment_id' => $paymentId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Confirm payment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy lịch sử thanh toán của sinh viên
    public function getPaymentHistory($studentId, $limit = 50) {
        try {
            $query = "SELECT p.*, r.room_number, b.name as building_name
                     FROM payments p
                     JOIN room_registrations rr ON p.room_registration_id = rr.id
                     JOIN rooms r ON rr.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     WHERE p.student_id = :student_id
                     ORDER BY p.payment_date DESC, p.created_at DESC
                     LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get payment history error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Tính tổng tiền cần thanh toán của sinh viên
    public function calculateTotalFee($studentId, $roomRegistrationId) {
        try {
            // Lấy thông tin registration
            $query = "SELECT rr.*, r.monthly_fee, r.id as room_id
                     FROM room_registrations rr
                     JOIN rooms r ON rr.room_id = r.id
                     WHERE rr.id = :registration_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':registration_id', $roomRegistrationId);
            $stmt->execute();
            $registration = $stmt->fetch();
            
            if (!$registration) {
                return null;
            }
            
            // Tính số tháng ở
            $startDate = new DateTime($registration['start_date']);
            $endDate = new DateTime(min($registration['end_date'], date('Y-m-d')));
            $months = $startDate->diff($endDate)->m + ($startDate->diff($endDate)->y * 12);
            $months = max(1, $months);
            
            // Tính tiền phòng
            $roomFee = $registration['monthly_fee'] * $months;
            
            // Tính tiền điện nước chưa thanh toán
            $query = "SELECT COALESCE(SUM(total_amount), 0) as unpaid_amount
                     FROM utility_readings
                     WHERE room_id = :room_id AND is_paid = FALSE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $registration['room_id']);
            $stmt->execute();
            $unpaid = $stmt->fetch()['unpaid_amount'];
            
            return [
                'room_fee' => $roomFee,
                'utility_fee' => $unpaid,
                'total' => $roomFee + $unpaid
            ];
            
        } catch (Exception $e) {
            $this->logger->error("Calculate total fee error", ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    // Lấy thống kê thanh toán theo tháng
    public function getMonthlyRevenue($year = null, $month = null) {
        try {
            $year = $year ?? date('Y');
            $month = $month ?? date('m');
            
            $query = "SELECT 
                        COUNT(*) as total_payments,
                        SUM(amount) as total_revenue,
                        SUM(CASE WHEN payment_type = 'room_fee' THEN amount ELSE 0 END) as room_revenue,
                        SUM(CASE WHEN payment_type = 'utility' THEN amount ELSE 0 END) as utility_revenue
                     FROM payments
                     WHERE YEAR(payment_date) = :year 
                     AND MONTH(payment_date) = :month
                     AND status = 'completed'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':month', $month);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get monthly revenue error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy tất cả thanh toán
    public function getAllPayments($limit = 50, $offset = 0) {
        try {
            $query = "SELECT p.*, s.student_code, u.full_name as student_name
                     FROM payments p
                     LEFT JOIN students s ON p.student_id = s.id
                     LEFT JOIN users u ON s.user_id = u.id
                     ORDER BY p.payment_date DESC, p.created_at DESC
                     LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get all payments error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy hóa đơn của sinh viên
    public function getMyPayments($studentId, $status = null) {
        try {
            $whereClause = "WHERE p.student_id = :student_id";
            $params = [':student_id' => $studentId];
            
            if ($status) {
                $whereClause .= " AND p.status = :status";
                $params[':status'] = $status;
            }
            
            $query = "SELECT p.*, r.room_number, b.name as building_name
                     FROM payments p
                     JOIN room_registrations rr ON p.room_registration_id = rr.id
                     JOIN rooms r ON rr.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     $whereClause
                     ORDER BY p.payment_date DESC, p.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get my payments error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Xử lý thanh toán
    public function processPayment($paymentId, $paymentMethod, $referenceNumber, $status) {
        try {
            // Lấy thông tin payment trước khi update
            $getQuery = "SELECT * FROM payments WHERE id = :id";
            $getStmt = $this->conn->prepare($getQuery);
            $getStmt->bindParam(':id', $paymentId);
            $getStmt->execute();
            $payment = $getStmt->fetch();
            
            if (!$payment) {
                throw new Exception("Payment not found");
            }
            
            // Update payment
            $query = "UPDATE payments 
                     SET payment_method = :payment_method,
                         reference_number = :reference_number,
                         status = :status,
                         updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':reference_number', $referenceNumber);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $paymentId);
            
            if ($stmt->execute()) {
                // Nếu thanh toán thành công VÀ là hóa đơn điện nước, cập nhật utility_readings
                if ($status === 'completed' && $payment['payment_type'] === 'utility') {
                    $this->markUtilityAsPaid($payment['room_registration_id'], $payment['payment_date']);
                }
                
                $this->logger->info("Payment processed", [
                    'payment_id' => $paymentId,
                    'method' => $paymentMethod,
                    'status' => $status,
                    'payment_type' => $payment['payment_type']
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Process payment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
