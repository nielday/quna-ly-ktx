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
    
    // Lấy lịch sử chỉ số (tất cả records, bao gồm cả ngày và tổng hợp)
    public function getReadingHistory($roomId, $limit = 12) {
        try {
            $query = "SELECT ur.*, r.room_number, b.name as building_name,
                            CASE WHEN ur.reading_date = LAST_DAY(ur.reading_date) THEN 1 ELSE 0 END as is_monthly_summary
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
    
    // Lấy records nhóm theo tháng (cho sinh viên xem chi tiết)
    public function getReadingsGroupedByMonth($roomId, $limitMonths = 12) {
        try {
            $query = "SELECT ur.*, r.room_number, b.name as building_name,
                            DATE_FORMAT(ur.reading_date, '%Y-%m') as month_year,
                            CASE WHEN ur.reading_date = LAST_DAY(ur.reading_date) THEN 1 ELSE 0 END as is_monthly_summary
                     FROM utility_readings ur
                     JOIN rooms r ON ur.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     WHERE ur.room_id = :room_id
                     ORDER BY ur.reading_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->execute();
            
            $allRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nhóm theo tháng
            $grouped = [];
            foreach ($allRecords as $record) {
                $monthYear = $record['month_year'];
                if (!isset($grouped[$monthYear])) {
                    $grouped[$monthYear] = [
                        'month' => $monthYear,
                        'records' => [],
                        'summary' => null
                    ];
                }
                
                // Tách record tổng hợp và records ngày
                if ($record['is_monthly_summary']) {
                    $grouped[$monthYear]['summary'] = $record;
                } else {
                    $grouped[$monthYear]['records'][] = $record;
                }
            }
            
            // Giới hạn số tháng và sort
            $result = array_values($grouped);
            usort($result, function($a, $b) {
                return strcmp($b['month'], $a['month']);
            });
            
            return array_slice($result, 0, $limitMonths);
            
        } catch (Exception $e) {
            $this->logger->error("Get readings grouped by month error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Kiểm tra số records trong tháng và so sánh với số ngày trong tháng
    public function checkMonthlyRecordsCompleteness($roomId, $monthYear) {
        try {
            // Đếm số records trong tháng
            $countQuery = "SELECT COUNT(*) as record_count
                          FROM utility_readings
                          WHERE room_id = :room_id
                          AND DATE_FORMAT(reading_date, '%Y-%m') = :month_year
                          AND reading_date != LAST_DAY(reading_date)";
            $countStmt = $this->conn->prepare($countQuery);
            $countStmt->bindParam(':room_id', $roomId);
            $countStmt->bindParam(':month_year', $monthYear);
            $countStmt->execute();
            $countResult = $countStmt->fetch();
            $recordCount = (int)$countResult['record_count'];
            
            // Tính số ngày trong tháng
            $firstDay = $monthYear . '-01';
            $daysInMonth = (int)date('t', strtotime($firstDay));
            
            // Kiểm tra có record tổng hợp chưa
            $summaryQuery = "SELECT id FROM utility_readings
                            WHERE room_id = :room_id
                            AND DATE_FORMAT(reading_date, '%Y-%m') = :month_year
                            AND reading_date = LAST_DAY(reading_date)";
            $summaryStmt = $this->conn->prepare($summaryQuery);
            $summaryStmt->bindParam(':room_id', $roomId);
            $summaryStmt->bindParam(':month_year', $monthYear);
            $summaryStmt->execute();
            $hasSummary = $summaryStmt->rowCount() > 0;
            
            return [
                'month' => $monthYear,
                'record_count' => $recordCount,
                'days_in_month' => $daysInMonth,
                'is_complete' => $recordCount >= $daysInMonth,
                'has_summary' => $hasSummary,
                'missing_days' => max(0, $daysInMonth - $recordCount)
            ];
            
        } catch (Exception $e) {
            $this->logger->error("Check monthly records completeness error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // Lấy tổng tiền của tất cả records trong tháng (để tính chia đều)
    public function getMonthlyTotalAmount($roomId, $monthYear) {
        try {
            // Tính tổng từ tất cả records ngày trong tháng (không tính record tổng hợp)
            $query = "SELECT COALESCE(SUM(total_amount), 0) as monthly_total
                     FROM utility_readings
                     WHERE room_id = :room_id
                     AND DATE_FORMAT(reading_date, '%Y-%m') = :month_year
                     AND reading_date != LAST_DAY(reading_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':month_year', $monthYear);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return (float)$result['monthly_total'];
            
        } catch (Exception $e) {
            $this->logger->error("Get monthly total amount error", ['error' => $e->getMessage()]);
            return 0;
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
    // Chỉ tính từ record tổng hợp (ngày cuối tháng), không tính records ngày
    public function getUnpaidAmount($roomId) {
        try {
            $query = "SELECT COALESCE(SUM(total_amount), 0) as unpaid_amount
                     FROM utility_readings
                     WHERE room_id = :room_id 
                     AND is_paid = FALSE
                     AND reading_date = LAST_DAY(reading_date)";
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
    public function getAllReadings($limit = 50, $offset = 0, $isPaid = null) {
        try {
            $whereClause = "";
            if ($isPaid !== null) {
                $whereClause = "WHERE ur.is_paid = " . ($isPaid ? "TRUE" : "FALSE");
            }
            
            $query = "SELECT ur.*, r.room_number, b.name as building_name
                     FROM utility_readings ur
                     JOIN rooms r ON ur.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     $whereClause
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
    
    // Lấy tất cả hóa đơn điện nước chưa thanh toán
    // Chỉ lấy record tổng hợp (ngày cuối tháng), không lấy records ngày
    public function getUnpaidReadings($limit = 50, $offset = 0) {
        try {
            $query = "SELECT ur.*, 
                            r.room_number, 
                            r.current_occupancy,
                            b.name as building_name,
                            COUNT(DISTINCT rr.student_id) as student_count,
                            GROUP_CONCAT(DISTINCT u.full_name ORDER BY u.full_name SEPARATOR ', ') as student_names
                     FROM utility_readings ur
                     JOIN rooms r ON ur.room_id = r.id
                     JOIN buildings b ON r.building_id = b.id
                     LEFT JOIN room_registrations rr ON r.id = rr.room_id 
                         AND rr.status IN ('approved', 'active', 'checked_in')
                     LEFT JOIN students s ON rr.student_id = s.id
                     LEFT JOIN users u ON s.user_id = u.id
                     WHERE ur.is_paid = FALSE
                     AND ur.reading_date = LAST_DAY(ur.reading_date)
                     GROUP BY ur.id
                     ORDER BY ur.reading_date DESC, ur.created_at DESC
                     LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Get unpaid readings error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    // ============================================
    // CHỨC NĂNG MÔ PHỎNG - CHỈ ĐỂ DEMO/TESTING
    // ============================================
    
    /**
     * Mô phỏng chỉ số điện nước cho 1 tháng
     * Tạo records cho tất cả các ngày trong tháng (30/31 records)
     * 
     * @param int $roomId ID phòng
     * @param string $readingDate Ngày đọc chỉ số (Y-m-d) - ngày bắt đầu tháng (Y-m-01)
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
            
            // Lấy chỉ số tháng trước (record cuối cùng)
            $lastReading = $this->getLastReading($roomId);
            
            // Tính tổng tiêu thụ cả tháng dựa trên số người
            // Điện: trung bình 50-80 kWh/người/tháng
            $electricityUsagePerPerson = rand(50, 80);
            $totalElectricityUsage = $electricityUsagePerPerson * $occupancy + rand(-20, 20); // Thêm biến động
            $totalElectricityUsage = max(30, $totalElectricityUsage); // Tối thiểu 30 kWh
            
            // Nước: trung bình 3-5 m³/người/tháng  
            $waterUsagePerPerson = rand(3, 5);
            $totalWaterUsage = $waterUsagePerPerson * $occupancy + rand(-2, 2); // Thêm biến động
            $totalWaterUsage = max(5, $totalWaterUsage); // Tối thiểu 5 m³
            
            // Tính số ngày trong tháng
            $month = date('Y-m', strtotime($readingDate));
            $firstDay = $month . '-01';
            $daysInMonth = (int)date('t', strtotime($firstDay));
            
            // Chia đều tiêu thụ cho các ngày trong tháng
            $dailyElectricityUsage = $totalElectricityUsage / $daysInMonth;
            $dailyWaterUsage = $totalWaterUsage / $daysInMonth;
            
            // Bắt đầu từ chỉ số tháng trước
            $currentElectricity = $lastReading ? (float)$lastReading['electricity_reading'] : 0;
            $currentWater = $lastReading ? (float)$lastReading['water_reading'] : 0;
            
            $createdRecords = [];
            $monthlyTotalAmount = 0;
            
            // Tạo records cho từng ngày trong tháng (từ ngày 1 đến ngày cuối - 1)
            // Ngày cuối cùng sẽ là record tổng hợp
            $lastDayOfMonth = (int)date('t', strtotime($firstDay)); // Số ngày trong tháng
            
            for ($day = 1; $day < $lastDayOfMonth; $day++) {
                // Tính chỉ số cho ngày này (cộng thêm phần tiêu thụ)
                $currentElectricity += $dailyElectricityUsage;
                $currentWater += $dailyWaterUsage;
                
                // Tính tiền cho ngày này
                $dailyElectricityAmount = $dailyElectricityUsage * $electricityRate;
                $dailyWaterAmount = $dailyWaterUsage * $waterRate;
                $dailyTotalAmount = $dailyElectricityAmount + $dailyWaterAmount;
                
                $monthlyTotalAmount += $dailyTotalAmount;
                
                // Tạo reading cho ngày này
                $dayDate = sprintf('%s-%02d', $month, $day);
                
                $insertQuery = "INSERT INTO utility_readings 
                               (room_id, reading_date, electricity_reading, water_reading, 
                                electricity_rate, water_rate, total_amount) 
                               VALUES (:room_id, :reading_date, :electricity_reading, :water_reading, 
                                       :electricity_rate, :water_rate, :total_amount)";
                $insertStmt = $this->conn->prepare($insertQuery);
                
                $insertStmt->bindParam(':room_id', $roomId);
                $insertStmt->bindParam(':reading_date', $dayDate);
                $insertStmt->bindParam(':electricity_reading', $currentElectricity);
                $insertStmt->bindParam(':water_reading', $currentWater);
                $insertStmt->bindParam(':electricity_rate', $electricityRate);
                $insertStmt->bindParam(':water_rate', $waterRate);
                $insertStmt->bindParam(':total_amount', $dailyTotalAmount);
                
                if ($insertStmt->execute()) {
                    $createdRecords[] = $dayDate;
                }
            }
            
            // Tạo record tổng hợp cuối tháng (ngày cuối cùng)
            // Tính thêm phần tiêu thụ ngày cuối
            $currentElectricity += $dailyElectricityUsage;
            $currentWater += $dailyWaterUsage;
            
            $dailyElectricityAmount = $dailyElectricityUsage * $electricityRate;
            $dailyWaterAmount = $dailyWaterUsage * $waterRate;
            $dailyTotalAmount = $dailyElectricityAmount + $dailyWaterAmount;
            
            $monthlyTotalAmount += $dailyTotalAmount;
            
            $lastDayOfMonthDate = sprintf('%s-%02d', $month, $lastDayOfMonth);
            
            $summaryQuery = "INSERT INTO utility_readings 
                           (room_id, reading_date, electricity_reading, water_reading, 
                            electricity_rate, water_rate, total_amount) 
                           VALUES (:room_id, :reading_date, :electricity_reading, :water_reading, 
                                   :electricity_rate, :water_rate, :total_amount)";
            $summaryStmt = $this->conn->prepare($summaryQuery);
            
            $summaryStmt->bindParam(':room_id', $roomId);
            $summaryStmt->bindParam(':reading_date', $lastDayOfMonthDate);
            $summaryStmt->bindParam(':electricity_reading', $currentElectricity);
            $summaryStmt->bindParam(':water_reading', $currentWater);
            $summaryStmt->bindParam(':electricity_rate', $electricityRate);
            $summaryStmt->bindParam(':water_rate', $waterRate);
            $summaryStmt->bindParam(':total_amount', $monthlyTotalAmount);
            
            if ($summaryStmt->execute()) {
                $createdRecords[] = $lastDayOfMonthDate . ' (summary)';
            }
            
            $this->conn->commit();
            
            $this->logger->info("Simulated monthly reading created", [
                'room_id' => $roomId,
                'occupancy' => $occupancy,
                'month' => $month,
                'days_in_month' => $daysInMonth,
                'total_electricity_usage' => $totalElectricityUsage,
                'total_water_usage' => $totalWaterUsage,
                'monthly_total_amount' => $monthlyTotalAmount,
                'records_created' => count($createdRecords)
            ]);
            
            return [
                'id' => null,
                'electricity_usage' => $totalElectricityUsage,
                'water_usage' => $totalWaterUsage,
                'total_amount' => $monthlyTotalAmount,
                'records_created' => count($createdRecords),
                'days_in_month' => $daysInMonth
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logger->error("Simulate monthly reading error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Tạo hóa đơn cho 1 reading cụ thể (record tổng hợp)
     * Kiểm tra đủ records trong tháng, tính tổng từ tất cả records ngày, chia đều cho số thành viên
     */
    public function createInvoiceForReading($readingId) {
        try {
            // Lấy thông tin reading (phải là record tổng hợp)
            $query = "SELECT * FROM utility_readings WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $readingId);
            $stmt->execute();
            $reading = $stmt->fetch();
            
            if (!$reading) {
                $this->logger->warning("Reading not found", ['reading_id' => $readingId]);
                throw new Exception("Không tìm thấy dữ liệu điện nước");
            }
            
            // Kiểm tra đây có phải record tổng hợp không
            $isMonthlySummary = (date('Y-m-d', strtotime($reading['reading_date'])) === date('Y-m-t', strtotime($reading['reading_date'])));
            if (!$isMonthlySummary) {
                throw new Exception("Chỉ có thể tạo hóa đơn từ record tổng hợp cuối tháng");
            }
            
            $monthYear = date('Y-m', strtotime($reading['reading_date']));
            
            // Kiểm tra đã tạo hóa đơn cho tháng này chưa
            $checkQuery = "SELECT COUNT(*) as count FROM payments 
                          WHERE payment_type = 'utility' 
                          AND notes LIKE :pattern";
            $checkStmt = $this->conn->prepare($checkQuery);
            $pattern = "%tháng " . date('m/Y', strtotime($reading['reading_date'])) . "%";
            $checkStmt->bindParam(':pattern', $pattern);
            $checkStmt->execute();
            
            $existingCount = $checkStmt->fetch()['count'];
            if ($existingCount > 0) {
                $this->logger->info("Invoice already exists", [
                    'reading_id' => $readingId,
                    'month' => $monthYear
                ]);
                throw new Exception("Đã có hóa đơn cho tháng " . date('m/Y', strtotime($reading['reading_date'])));
            }
            
            // Kiểm tra đủ records (cảnh báo nhưng vẫn cho tạo)
            $completeness = $this->checkMonthlyRecordsCompleteness($reading['room_id'], $monthYear);
            $warning = null;
            if (!$completeness['is_complete']) {
                $warning = "Cảnh báo: Tháng này chỉ có {$completeness['record_count']}/{$completeness['days_in_month']} records. Thiếu {$completeness['missing_days']} ngày.";
                $this->logger->warning("Incomplete monthly records", [
                    'room_id' => $reading['room_id'],
                    'month' => $monthYear,
                    'record_count' => $completeness['record_count'],
                    'days_in_month' => $completeness['days_in_month']
                ]);
            }
            
            // Tính tổng tiền từ TẤT CẢ records ngày trong tháng (không dùng total_amount của record tổng hợp)
            $monthlyTotal = $this->getMonthlyTotalAmount($reading['room_id'], $monthYear);
            
            $this->logger->info("Creating invoice for reading", [
                'reading_id' => $readingId,
                'room_id' => $reading['room_id'],
                'month' => $monthYear,
                'monthly_total' => $monthlyTotal,
                'completeness' => $completeness
            ]);
            
            // Tạo hóa đơn với tổng tiền từ tất cả records ngày, chia đều cho số thành viên
            $result = $this->createUtilityInvoice(
                $reading['room_id'],
                $reading['id'],
                $monthlyTotal, // Dùng tổng từ records ngày, không dùng total_amount của record tổng hợp
                $reading['reading_date'],
                $warning
            );
            
            if (!$result) {
                throw new Exception("Không có sinh viên active trong phòng");
            }
            
            return [
                'success' => true,
                'warning' => $warning,
                'monthly_total' => $monthlyTotal,
                'completeness' => $completeness
            ];
            
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
     * Chia đều tổng tiền từ tất cả records ngày trong tháng cho số thành viên
     */
    public function createUtilityInvoice($roomId, $readingId, $monthlyTotalAmount, $readingDate, $warning = null) {
        try {
            // Lấy danh sách sinh viên đang ở trong phòng
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
                'monthly_total' => $monthlyTotalAmount,
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
            
            // Chia đều tổng tiền tháng cho số thành viên
            $amountPerStudent = round($monthlyTotalAmount / count($students), 2);
            
            // Tạo hóa đơn cho mỗi sinh viên
            $createdCount = 0;
            foreach ($students as $student) {
                $invoiceQuery = "INSERT INTO payments 
                    (student_id, room_registration_id, payment_type, amount, payment_date, payment_method, status, notes) 
                    VALUES 
                    (:student_id, :registration_id, 'utility', :amount, :payment_date, 'cash', 'pending', :notes)";
                $invoiceStmt = $this->conn->prepare($invoiceQuery);
                
                $notes = "Hóa đơn điện nước tháng " . date('m/Y', strtotime($readingDate));
                if ($warning) {
                    $notes .= " ($warning)";
                }
                
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
                // Đọc chỉ số vào ngày 1 mỗi tháng (để generate records cho cả tháng)
                $currentDate->modify('+1 month');
                $readingDate = $currentDate->format('Y-m-01');
                $monthYear = $currentDate->format('Y-m');
                
                // Kiểm tra đã có records cho tháng này chưa (kiểm tra ngày 1, ngày cuối tháng, hoặc bất kỳ ngày nào trong tháng)
                $checkQuery = "SELECT COUNT(*) as count FROM utility_readings 
                              WHERE room_id = :room_id 
                              AND DATE_FORMAT(reading_date, '%Y-%m') = :month_year";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->bindParam(':room_id', $roomId);
                $checkStmt->bindParam(':month_year', $monthYear);
                $checkStmt->execute();
                
                $count = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
                if ((int)$count > 0) {
                    $results[] = [
                        'date' => $monthYear,
                        'status' => 'skipped',
                        'message' => 'Đã có chỉ số cho tháng này'
                    ];
                    continue;
                }
                
                // Mô phỏng (sẽ tạo đủ records cho cả tháng + record tổng hợp)
                $result = $this->simulateMonthlyReading($roomId, $readingDate, $electricityRate, $waterRate);
                
                if ($result) {
                    $results[] = [
                        'date' => $monthYear,
                        'status' => 'success',
                        'data' => $result
                    ];
                } else {
                    $results[] = [
                        'date' => $monthYear,
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
