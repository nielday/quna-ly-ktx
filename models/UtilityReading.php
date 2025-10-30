<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/logger.php';

class UtilityReading {
    private $conn;
    private $logger;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }
    
    // Lấy chỉ số điện nước gần nhất
    public function getLastReading($roomId) {
        try {
            $query = "SELECT * FROM utility_readings 
                     WHERE room_id = :room_id 
                     ORDER BY reading_date DESC 
                     LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            $this->logger->error("Get last reading error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Nhập chỉ số điện nước mới
    public function createReading($roomId, $readingDate, $electricityReading, $waterReading, $electricityRate, $waterRate) {
        try {
            // Kiểm tra đã có reading cho tháng này chưa
            $checkQuery = "SELECT id FROM utility_readings 
                          WHERE room_id = :room_id AND reading_date = :reading_date";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':room_id', $roomId);
            $checkStmt->bindParam(':reading_date', $readingDate);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                throw new Exception("Đã nhập chỉ số cho tháng này");
            }
            
            // Lấy chỉ số tháng trước
            $lastReading = $this->getLastReading($roomId);
            
            $electricityUsage = $electricityReading - ($lastReading ? $lastReading['electricity_reading'] : 0);
            $waterUsage = $waterReading - ($lastReading ? $lastReading['water_reading'] : 0);
            
            // Tính tổng tiền
            $electricityAmount = $electricityUsage * $electricityRate;
            $waterAmount = $waterUsage * $waterRate;
            $totalAmount = $electricityAmount + $waterAmount;
            
            // Tạo reading mới
            $query = "INSERT INTO utility_readings 
                     (room_id, reading_date, electricity_reading, water_reading, 
                      electricity_rate, water_rate, total_amount) 
                     VALUES (:room_id, :reading_date, :electricity_reading, :water_reading, 
                             :electricity_rate, :water_rate, :total_amount)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':reading_date', $readingDate);
            $stmt->bindParam(':electricity_reading', $electricityReading);
            $stmt->bindParam(':water_reading', $waterReading);
            $stmt->bindParam(':electricity_rate', $electricityRate);
            $stmt->bindParam(':water_rate', $waterRate);
            $stmt->bindParam(':total_amount', $totalAmount);
            
            if ($stmt->execute()) {
                $readingId = $this->conn->lastInsertId();
                
                $this->logger->info("Utility reading created", [
                    'reading_id' => $readingId,
                    'room_id' => $roomId,
                    'total_amount' => $totalAmount
                ]);
                
                return [
                    'id' => $readingId,
                    'electricity_usage' => $electricityUsage,
                    'water_usage' => $waterUsage,
                    'total_amount' => $totalAmount
                ];
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Create reading error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy lịch sử chỉ số
    public function getReadingHistory($roomId, $limit = 12) {
        try {
            $query = "SELECT ur.*, r.room_number, b.name as building_name
                     FROM utility_readings ur
                     JOIN rooms r ON ur.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     WHERE ur.room_id = :room_id
                     ORDER BY ur.reading_date DESC
                     LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get reading history error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Đánh dấu đã thanh toán
    public function markAsPaid($readingId) {
        try {
            $query = "UPDATE utility_readings 
                     SET is_paid = TRUE, updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $readingId);
            
            if ($stmt->execute()) {
                $this->logger->info("Reading marked as paid", ['reading_id' => $readingId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Mark as paid error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy tổng tiền điện nước chưa thanh toán của phòng
    public function getUnpaidAmount($roomId) {
        try {
            $query = "SELECT COALESCE(SUM(total_amount), 0) as unpaid_amount
                     FROM utility_readings
                     WHERE room_id = :room_id AND is_paid = FALSE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['unpaid_amount'];
        } catch (Exception $e) {
            $this->logger->error("Get unpaid amount error", ['error' => $e->getMessage()]);
            return 0;
        }
    }
    
    // Lấy tất cả chỉ số điện nước
    public function getAllReadings($limit = 50, $offset = 0) {
        try {
            $query = "SELECT ur.*, r.room_number, b.name as building_name
                     FROM utility_readings ur
                     JOIN rooms r ON ur.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     ORDER BY ur.reading_date DESC, ur.created_at DESC
                     LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get all readings error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // ============================================
    // CHỨC NĂNG MÔ PHỎNG - CHỈ ĐỂ DEMO/TESTING
    // ============================================
    
    /**
     * Mô phỏng chỉ số điện nước cho 1 tháng
     * Tạo dữ liệu gần với thực tế để demo
     * 
     * @param int $roomId ID phòng
     * @param string $readingDate Ngày đọc chỉ số (Y-m-d)
     * @param float $electricityRate Giá điện (mặc định 3500 VNĐ/kWh)
     * @param float $waterRate Giá nước (mặc định 15000 VNĐ/m³)
     * @return array|false Kết quả mô phỏng
     */
    public function simulateMonthlyReading($roomId, $readingDate, $electricityRate = 3500, $waterRate = 15000) {
        try {
            $this->conn->beginTransaction();
            
            // Kiểm tra phòng có bao nhiêu người
            $roomQuery = "SELECT current_occupancy FROM rooms WHERE id = :room_id";
            $roomStmt = $this->conn->prepare($roomQuery);
            $roomStmt->bindParam(':room_id', $roomId);
            $roomStmt->execute();
            $room = $roomStmt->fetch();
            
            if (!$room) {
                throw new Exception("Phòng không tồn tại");
            }
            
            $occupancy = max(1, $room['current_occupancy']); // Ít nhất 1 người
            
            // Lấy chỉ số tháng trước
            $lastReading = $this->getLastReading($roomId);
            
            // Tính chỉ số mới dựa trên số người và thêm random
            // Điện: trung bình 50-80 kWh/người/tháng
            $electricityUsagePerPerson = rand(50, 80);
            $electricityUsage = $electricityUsagePerPerson * $occupancy + rand(-20, 20); // Thêm biến động
            $electricityUsage = max(30, $electricityUsage); // Tối thiểu 30 kWh
            
            // Nước: trung bình 3-5 m³/người/tháng  
            $waterUsagePerPerson = rand(3, 5);
            $waterUsage = $waterUsagePerPerson * $occupancy + rand(-2, 2); // Thêm biến động
            $waterUsage = max(5, $waterUsage); // Tối thiểu 5 m³
            
            // Tính chỉ số mới (số tích lũy)
            $lastElectricity = $lastReading ? $lastReading['electricity_reading'] : 0;
            $lastWater = $lastReading ? $lastReading['water_reading'] : 0;
            
            $newElectricityReading = $lastElectricity + $electricityUsage;
            $newWaterReading = $lastWater + $waterUsage;
            
            // Tạo reading mới
            $result = $this->createReading(
                $roomId, 
                $readingDate, 
                $newElectricityReading, 
                $newWaterReading, 
                $electricityRate, 
                $waterRate
            );
            
            if ($result) {
                $this->conn->commit();
                
                $this->logger->info("Simulated reading created", [
                    'room_id' => $roomId,
                    'occupancy' => $occupancy,
                    'electricity_usage' => $electricityUsage,
                    'water_usage' => $waterUsage,
                    'total_amount' => $result['total_amount']
                ]);
                
                return $result;
            }
            
            $this->conn->rollback();
            return false;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Simulate monthly reading error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Tạo hóa đơn cho 1 reading cụ thể
     * Gọi từ ngoài để tạo hóa đơn thủ công
     */
    public function createInvoiceForReading($readingId) {
        try {
            // Lấy thông tin reading
            $query = "SELECT * FROM utility_readings WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $readingId);
            $stmt->execute();
            $reading = $stmt->fetch();
            
            if (!$reading) {
                $this->logger->warning("Reading not found", ['reading_id' => $readingId]);
                throw new Exception("Không tìm thấy dữ liệu điện nước");
            }
            
            // Kiểm tra đã tạo hóa đơn cho reading này chưa
            $checkQuery = "SELECT COUNT(*) as count FROM payments 
                          WHERE payment_type = 'utility' 
                          AND notes LIKE :pattern";
            $checkStmt = $this->conn->prepare($checkQuery);
            $monthYear = date('m/Y', strtotime($reading['reading_date']));
            $pattern = "%tháng " . $monthYear . "%";
            $checkStmt->bindParam(':pattern', $pattern);
            $checkStmt->execute();
            
            $existingCount = $checkStmt->fetch()['count'];
            if ($existingCount > 0) {
                $this->logger->info("Invoice already exists", [
                    'reading_id' => $readingId,
                    'month' => $monthYear
                ]);
                throw new Exception("Đã có hóa đơn cho tháng " . $monthYear);
            }
            
            $this->logger->info("Creating invoice for reading", [
                'reading_id' => $readingId,
                'room_id' => $reading['room_id'],
                'amount' => $reading['total_amount'],
                'date' => $reading['reading_date']
            ]);
            
            // Tạo hóa đơn
            $result = $this->createUtilityInvoice(
                $reading['room_id'],
                $reading['id'],
                $reading['total_amount'],
                $reading['reading_date']
            );
            
            if (!$result) {
                throw new Exception("Không có sinh viên active trong phòng");
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error("Create invoice for reading error", [
                'error' => $e->getMessage(),
                'reading_id' => $readingId
            ]);
            throw $e;
        }
    }
    
    /**
     * Tạo hóa đơn điện nước cho sinh viên trong phòng
     */
    public function createUtilityInvoice($roomId, $readingId, $amount, $readingDate) {
        try {
            // Lấy danh sách sinh viên đang ở trong phòng
            // Bỏ điều kiện ngày tháng vì sinh viên hiện tại trong phòng là sinh viên phải trả
            $query = "SELECT DISTINCT s.id as student_id, rr.id as registration_id, u.full_name, rr.status
                     FROM room_registrations rr
                     JOIN students s ON rr.student_id = s.id
                     JOIN users u ON s.user_id = u.id
                     WHERE rr.room_id = :room_id 
                     AND rr.status IN ('approved', 'active', 'checked_in')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->execute();
            $students = $stmt->fetchAll();
            
            $this->logger->info("Found students for invoice", [
                'room_id' => $roomId,
                'reading_date' => $readingDate,
                'student_count' => count($students),
                'students' => $students
            ]);
            
            if (empty($students)) {
                // Không có sinh viên active, không tạo hóa đơn
                $this->logger->warning("No active students in room for utility invoice", [
                    'room_id' => $roomId,
                    'reading_date' => $readingDate
                ]);
                
                // Thử tìm sinh viên pending/approved (chưa active)
                $pendingQuery = "SELECT COUNT(*) as count FROM room_registrations 
                                WHERE room_id = :room_id AND status IN ('pending', 'approved')";
                $pendingStmt = $this->conn->prepare($pendingQuery);
                $pendingStmt->bindParam(':room_id', $roomId);
                $pendingStmt->execute();
                $pendingCount = $pendingStmt->fetch()['count'];
                
                $this->logger->info("Room registration status", [
                    'room_id' => $roomId,
                    'pending_or_approved' => $pendingCount
                ]);
                
                return false;
            }
            
            // Chia đều tiền điện nước cho số sinh viên
            $amountPerStudent = round($amount / count($students), 2);
            
            // Tạo hóa đơn cho mỗi sinh viên
            $createdCount = 0;
            foreach ($students as $student) {
                $invoiceQuery = "INSERT INTO payments 
                    (student_id, room_registration_id, payment_type, amount, payment_date, payment_method, status, notes) 
                    VALUES 
                    (:student_id, :registration_id, 'utility', :amount, :payment_date, 'cash', 'pending', :notes)";
                $invoiceStmt = $this->conn->prepare($invoiceQuery);
                
                $notes = "Hóa đơn điện nước tháng " . date('m/Y', strtotime($readingDate));
                
                $invoiceStmt->bindParam(':student_id', $student['student_id']);
                $invoiceStmt->bindParam(':registration_id', $student['registration_id']);
                $invoiceStmt->bindParam(':amount', $amountPerStudent);
                $invoiceStmt->bindParam(':payment_date', $readingDate);
                $invoiceStmt->bindParam(':notes', $notes);
                
                if ($invoiceStmt->execute()) {
                    $createdCount++;
                }
            }
            
            $this->logger->info("Created utility invoices", [
                'room_id' => $roomId,
                'reading_id' => $readingId,
                'student_count' => count($students),
                'created_count' => $createdCount,
                'amount_per_student' => $amountPerStudent,
                'total_amount' => $amount,
                'students' => array_column($students, 'full_name')
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error("Create utility invoice error", [
                'error' => $e->getMessage(),
                'room_id' => $roomId
            ]);
            return false;
        }
    }
    
    /**
     * Mô phỏng chỉ số điện nước cho nhiều tháng
     * 
     * @param int $roomId ID phòng
     * @param int $months Số tháng muốn mô phỏng (từ tháng hiện tại lùi về trước)
     * @param float $electricityRate Giá điện
     * @param float $waterRate Giá nước
     * @return array Danh sách kết quả
     */
    public function simulateMultipleMonths($roomId, $months = 6, $electricityRate = 3500, $waterRate = 15000) {
        try {
            $results = [];
            $currentDate = new DateTime();
            
            // Lùi về tháng trước để bắt đầu
            $currentDate->modify('-' . $months . ' months');
            
            for ($i = 0; $i < $months; $i++) {
                // Đọc chỉ số vào ngày 1 mỗi tháng
                $currentDate->modify('+1 month');
                $readingDate = $currentDate->format('Y-m-01');
                
                // Kiểm tra đã có chưa
                $checkQuery = "SELECT id FROM utility_readings 
                              WHERE room_id = :room_id AND reading_date = :reading_date";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->bindParam(':room_id', $roomId);
                $checkStmt->bindParam(':reading_date', $readingDate);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() > 0) {
                    $results[] = [
                        'date' => $readingDate,
                        'status' => 'skipped',
                        'message' => 'Đã có chỉ số cho tháng này'
                    ];
                    continue;
                }
                
                // Mô phỏng
                $result = $this->simulateMonthlyReading($roomId, $readingDate, $electricityRate, $waterRate);
                
                if ($result) {
                    $results[] = [
                        'date' => $readingDate,
                        'status' => 'success',
                        'data' => $result
                    ];
                } else {
                    $results[] = [
                        'date' => $readingDate,
                        'status' => 'failed'
                    ];
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $this->logger->error("Simulate multiple months error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Tự động tạo chỉ số cho tất cả phòng đang có người ở
     * 
     * @param string $readingDate Ngày đọc chỉ số
     * @param float $electricityRate Giá điện
     * @param float $waterRate Giá nước
     * @return array Kết quả cho tất cả phòng
     */
    public function autoGenerateForAllRooms($readingDate = null, $electricityRate = 3500, $waterRate = 15000) {
        try {
            if (!$readingDate) {
                $readingDate = date('Y-m-01'); // Ngày 1 tháng hiện tại
            }
            
            // Lấy tất cả phòng đang có người ở
            $query = "SELECT id, room_number, current_occupancy 
                     FROM rooms 
                     WHERE current_occupancy > 0 AND status = 'available'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $rooms = $stmt->fetchAll();
            
            $results = [];
            foreach ($rooms as $room) {
                $result = $this->simulateMonthlyReading($room['id'], $readingDate, $electricityRate, $waterRate);
                
                $results[] = [
                    'room_id' => $room['id'],
                    'room_number' => $room['room_number'],
                    'occupancy' => $room['current_occupancy'],
                    'status' => $result ? 'success' : 'failed',
                    'data' => $result ?: null
                ];
            }
            
            $this->logger->info("Auto-generated readings for all rooms", [
                'date' => $readingDate,
                'total_rooms' => count($rooms),
                'success' => count(array_filter($results, fn($r) => $r['status'] === 'success'))
            ]);
            
            return $results;
            
        } catch (Exception $e) {
            $this->logger->error("Auto generate for all rooms error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Xóa tất cả dữ liệu mô phỏng (để reset)
     * CHÚ Ý: Chỉ dùng trong môi trường dev/testing
     * 
     * @param int $roomId ID phòng (null = xóa tất cả)
     * @return bool
     */
    public function clearSimulatedData($roomId = null) {
        try {
            if ($roomId) {
                $query = "DELETE FROM utility_readings WHERE room_id = :room_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':room_id', $roomId);
            } else {
                $query = "DELETE FROM utility_readings";
                $stmt = $this->conn->prepare($query);
            }
            
            if ($stmt->execute()) {
                $this->logger->warning("Cleared simulated utility data", [
                    'room_id' => $roomId ?? 'all'
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Clear simulated data error", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
