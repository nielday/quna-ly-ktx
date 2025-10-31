<?php
/**
 * Background daemon để tự động mô phỏng chỉ số điện nước
 * Chạy độc lập, không cần browser
 * 
 * Cách sử dụng:
 * - Start: php scripts/utility_demo_daemon.php
 * - Hoặc dùng: scripts/start_demo_daemon.bat
 * - Stop: Ctrl+C hoặc: scripts/stop_demo_daemon.bat
 */

// Đặt đường dẫn gốc
$basePath = dirname(__DIR__);

require_once $basePath . '/config/config.php';
require_once $basePath . '/config/database.php';
require_once $basePath . '/config/logger.php';
require_once $basePath . '/models/UtilityReading.php';
require_once $basePath . '/models/Room.php';

// Cấu hình
$intervalSeconds = 60; // Cập nhật mỗi 60 giây (1 phút)
$minutesPerInterval = 1; // Mỗi interval = 1 phút thời gian thực

// File lock để tránh chạy nhiều instance cùng lúc
$lockFile = $basePath . '/logs/utility_demo_daemon.lock';
$pidFile = $basePath . '/logs/utility_demo_daemon.pid';

// Kiểm tra lock file
if (file_exists($lockFile)) {
    $oldPid = file_get_contents($lockFile);
    // Kiểm tra process có còn chạy không
    if (isWindows()) {
        // Windows: tasklist /FI "PID eq $oldPid"
        $processRunning = false;
        if ($oldPid) {
            $output = shell_exec("tasklist /FI \"PID eq $oldPid\" 2>nul");
            $processRunning = strpos($output, (string)$oldPid) !== false;
        }
    } else {
        // Linux: ps -p $oldPid
        $processRunning = posix_kill($oldPid, 0);
    }
    
    if ($processRunning) {
        echo "Daemon đã chạy (PID: $oldPid). Dừng process cũ trước khi start lại.\n";
        exit(1);
    } else {
        // Process không còn chạy, xóa lock file cũ
        @unlink($lockFile);
        @unlink($pidFile);
    }
}

// Tạo lock file
$currentPid = getmypid();
file_put_contents($lockFile, $currentPid);
file_put_contents($pidFile, $currentPid);

// Signal handler để cleanup khi dừng
if (function_exists('pcntl_signal')) {
    declare(ticks = 1);
    pcntl_signal(SIGTERM, 'shutdown_handler');
    pcntl_signal(SIGINT, 'shutdown_handler');
}

function shutdown_handler($signo) {
    global $lockFile, $pidFile, $logger;
    echo "\nNhận tín hiệu dừng. Đang cleanup...\n";
    @unlink($lockFile);
    @unlink($pidFile);
    if ($logger) {
        $logger->info("Utility demo daemon stopped");
    }
    exit(0);
}

// Cleanup khi script kết thúc
register_shutdown_function(function() use ($lockFile, $pidFile) {
    @unlink($lockFile);
    @unlink($pidFile);
});

function isWindows() {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

// Khởi tạo
try {
    $utilityModel = new UtilityReading();
    $roomModel = new Room();
    $database = new Database();
    $conn = $database->getConnection();
    $logger = new Logger();
    
    echo "========================================\n";
    echo "Utility Demo Daemon - Đã khởi động\n";
    echo "========================================\n";
    echo "PID: $currentPid\n";
    echo "Interval: $intervalSeconds giây ($minutesPerInterval phút thời gian thực)\n";
    echo "Lock file: $lockFile\n";
    echo "========================================\n";
    echo "Nhấn Ctrl+C để dừng\n\n";
    
    $logger->info("Utility demo daemon started", [
        'pid' => $currentPid,
        'interval' => $intervalSeconds
    ]);
    
    $updateCount = 0;
    $startTime = time();
    $lastMonthCheck = date('Y-m'); // Để kiểm tra chuyển tháng
    
    // Loop vô hạn
    while (true) {
        try {
            // Tỷ lệ tăng
            $electricityIncreaseRate = 0.01; // kWh/phút/người
            $waterIncreaseRate = 0.0001; // m³/phút/người
            
            // Đơn giá mặc định
            $defaultElectricityRate = 3500; // VNĐ/kWh
            $defaultWaterRate = 15000; // VNĐ/m³
            
            // Lấy tất cả phòng có người ở
            $query = "SELECT r.id, r.room_number, r.current_occupancy, r.building_id,
                             b.name as building_name
                      FROM rooms r
                      JOIN buildings b ON r.building_id = b.id
                      WHERE r.current_occupancy > 0 
                      AND r.status IN ('available', 'occupied', 'full')";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalUpdated = 0;
            $today = date('Y-m-d'); // Ngày hôm nay
            
            foreach ($rooms as $room) {
                $occupancy = max(1, (int)$room['current_occupancy']);
                
                // Tính tăng chỉ số
                $electricityIncrease = $electricityIncreaseRate * $occupancy * $minutesPerInterval;
                $waterIncrease = $waterIncreaseRate * $occupancy * $minutesPerInterval;
                
                // Lấy chỉ số của NGÀY HÔM NAY (mốc ngày hiện tại)
                $todayReadingQuery = "SELECT id, electricity_reading, water_reading, electricity_rate, water_rate
                                     FROM utility_readings
                                     WHERE room_id = :room_id 
                                     AND reading_date = :today
                                     LIMIT 1";
                $todayReadingStmt = $conn->prepare($todayReadingQuery);
                $todayReadingStmt->bindParam(':room_id', $room['id']);
                $todayReadingStmt->bindParam(':today', $today);
                $todayReadingStmt->execute();
                $todayReading = $todayReadingStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($todayReading) {
                    // Đã có reading hôm nay, cập nhật (cộng thêm vào mốc ngày)
                    $currentElectricity = (float)$todayReading['electricity_reading'];
                    $currentWater = (float)$todayReading['water_reading'];
                    
                    $newElectricity = $currentElectricity + $electricityIncrease;
                    $newWater = $currentWater + $waterIncrease;
                    
                    // Tính tiền điện nước cho phần tăng thêm
                    $electricityCost = $electricityIncrease * ((float)$todayReading['electricity_rate'] ?: $defaultElectricityRate);
                    $waterCost = $waterIncrease * ((float)$todayReading['water_rate'] ?: $defaultWaterRate);
                    $additionalAmount = $electricityCost + $waterCost;
                    
                    // Cập nhật chỉ số của ngày hôm nay
                    $updateQuery = "UPDATE utility_readings 
                                  SET electricity_reading = :elec, 
                                      water_reading = :water,
                                      total_amount = total_amount + :amount,
                                      updated_at = NOW()
                                  WHERE id = :id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(':elec', $newElectricity);
                    $updateStmt->bindParam(':water', $newWater);
                    $updateStmt->bindParam(':amount', $additionalAmount);
                    $updateStmt->bindParam(':id', $todayReading['id']);
                    
                    if ($updateStmt->execute()) {
                        $totalUpdated++;
                    }
                } else {
                    // Chưa có reading hôm nay, tạo mới (mốc ngày mới)
                    // Lấy chỉ số cuối cùng của ngày hôm qua để làm mốc bắt đầu
                    $yesterday = date('Y-m-d', strtotime('-1 day'));
                    $lastReadingQuery = "SELECT electricity_reading, water_reading
                                        FROM utility_readings
                                        WHERE room_id = :room_id 
                                        AND reading_date <= :yesterday
                                        ORDER BY reading_date DESC, id DESC
                                        LIMIT 1";
                    $lastReadingStmt = $conn->prepare($lastReadingQuery);
                    $lastReadingStmt->bindParam(':room_id', $room['id']);
                    $lastReadingStmt->bindParam(':yesterday', $yesterday);
                    $lastReadingStmt->execute();
                    $lastReading = $lastReadingStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Chỉ số bắt đầu ngày hôm nay = chỉ số cuối ngày hôm qua + phần tăng
                    $baseElectricity = $lastReading ? (float)$lastReading['electricity_reading'] : 0;
                    $baseWater = $lastReading ? (float)$lastReading['water_reading'] : 0;
                    
                    $newElectricity = $baseElectricity + $electricityIncrease;
                    $newWater = $baseWater + $waterIncrease;
                    
                    // Tính tiền
                    $electricityCost = $electricityIncrease * $defaultElectricityRate;
                    $waterCost = $waterIncrease * $defaultWaterRate;
                    $totalAmount = $electricityCost + $waterCost;
                    
                    // Tạo reading mới cho ngày hôm nay
                    $insertQuery = "INSERT INTO utility_readings 
                                   (room_id, reading_date, electricity_reading, water_reading, 
                                    electricity_rate, water_rate, total_amount, is_paid)
                                   VALUES (:room_id, :reading_date, :elec, :water, 
                                           :elec_rate, :water_rate, :amount, FALSE)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bindParam(':room_id', $room['id']);
                    $insertStmt->bindParam(':reading_date', $today);
                    $insertStmt->bindParam(':elec', $newElectricity);
                    $insertStmt->bindParam(':water', $newWater);
                    $insertStmt->bindValue(':elec_rate', $defaultElectricityRate);
                    $insertStmt->bindValue(':water_rate', $defaultWaterRate);
                    $insertStmt->bindParam(':amount', $totalAmount);
                    
                    if ($insertStmt->execute()) {
                        $totalUpdated++;
                    }
                }
            }
            
            $updateCount++;
            $elapsed = time() - $startTime;
            $minutes = floor($elapsed / 60);
            $currentMonth = date('Y-m');
            
            // Kiểm tra nếu đã chuyển sang tháng mới → tổng hợp tháng trước
            if ($currentMonth !== $lastMonthCheck) {
                $previousMonth = $lastMonthCheck;
                echo "[" . date('Y-m-d H:i:s') . "] Phát hiện chuyển tháng! Đang tổng hợp tháng $previousMonth...\n";
                
                try {
                    // Tổng hợp tất cả readings của tháng trước cho mỗi phòng
                    $aggregateQuery = "SELECT room_id, 
                                      SUM(total_amount) as monthly_total,
                                      MAX(electricity_reading) as final_electricity,
                                      MAX(water_reading) as final_water,
                                      AVG(electricity_rate) as avg_electricity_rate,
                                      AVG(water_rate) as avg_water_rate
                                     FROM utility_readings
                                     WHERE reading_date >= :month_start 
                                     AND reading_date < :current_month_start
                                     GROUP BY room_id";
                    $aggregateStmt = $conn->prepare($aggregateQuery);
                    $monthStart = $previousMonth . '-01';
                    $currentMonthStart = $currentMonth . '-01';
                    $aggregateStmt->bindParam(':month_start', $monthStart);
                    $aggregateStmt->bindParam(':current_month_start', $currentMonthStart);
                    $aggregateStmt->execute();
                    $aggregatedRooms = $aggregateStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $aggregatedCount = 0;
                    foreach ($aggregatedRooms as $aggRoom) {
                        // Tạo record tổng hợp cuối tháng (ngày cuối cùng của tháng trước)
                        $lastDayOfMonth = date('Y-m-t', strtotime($monthStart));
                        
                        // Kiểm tra đã có record tổng hợp chưa
                        $checkQuery = "SELECT id FROM utility_readings 
                                      WHERE room_id = :room_id 
                                      AND reading_date = :date";
                        $checkStmt = $conn->prepare($checkQuery);
                        $checkStmt->bindParam(':room_id', $aggRoom['room_id']);
                        $checkStmt->bindParam(':date', $lastDayOfMonth);
                        $checkStmt->execute();
                        
                        if ($checkStmt->rowCount() == 0) {
                            // Chưa có record, tạo mới
                            $insertAggQuery = "INSERT INTO utility_readings 
                                              (room_id, reading_date, electricity_reading, water_reading,
                                               electricity_rate, water_rate, total_amount, is_paid)
                                              VALUES (:room_id, :date, :elec, :water,
                                                      :elec_rate, :water_rate, :amount, FALSE)";
                            $insertAggStmt = $conn->prepare($insertAggQuery);
                            $insertAggStmt->bindParam(':room_id', $aggRoom['room_id']);
                            $insertAggStmt->bindParam(':date', $lastDayOfMonth);
                            $insertAggStmt->bindParam(':elec', $aggRoom['final_electricity']);
                            $insertAggStmt->bindParam(':water', $aggRoom['final_water']);
                            $insertAggStmt->bindParam(':amount', $aggRoom['monthly_total']);
                            $insertAggStmt->bindParam(':elec_rate', $aggRoom['avg_electricity_rate']);
                            $insertAggStmt->bindParam(':water_rate', $aggRoom['avg_water_rate']);
                            $insertAggStmt->execute();
                            $aggregatedCount++;
                        } else {
                            // Đã có record, cập nhật
                            $updateAggQuery = "UPDATE utility_readings 
                                              SET electricity_reading = :elec,
                                                  water_reading = :water,
                                                  total_amount = :amount,
                                                  electricity_rate = :elec_rate,
                                                  water_rate = :water_rate,
                                                  updated_at = NOW()
                                              WHERE room_id = :room_id 
                                              AND reading_date = :date";
                            $updateAggStmt = $conn->prepare($updateAggQuery);
                            $updateAggStmt->bindParam(':room_id', $aggRoom['room_id']);
                            $updateAggStmt->bindParam(':date', $lastDayOfMonth);
                            $updateAggStmt->bindParam(':elec', $aggRoom['final_electricity']);
                            $updateAggStmt->bindParam(':water', $aggRoom['final_water']);
                            $updateAggStmt->bindParam(':amount', $aggRoom['monthly_total']);
                            $updateAggStmt->bindParam(':elec_rate', $aggRoom['avg_electricity_rate']);
                            $updateAggStmt->bindParam(':water_rate', $aggRoom['avg_water_rate']);
                            $updateAggStmt->execute();
                            $aggregatedCount++;
                        }
                    }
                    
                    echo "[" . date('Y-m-d H:i:s') . "] Đã tổng hợp $aggregatedCount phòng cho tháng $previousMonth\n";
                    $logger->info("Monthly aggregation completed", [
                        'month' => $previousMonth,
                        'rooms_aggregated' => $aggregatedCount
                    ]);
                    
                    $lastMonthCheck = $currentMonth; // Cập nhật tháng đã kiểm tra
                    
                } catch (Exception $e) {
                    echo "[" . date('Y-m-d H:i:s') . "] Lỗi khi tổng hợp tháng: " . $e->getMessage() . "\n";
                    $logger->error("Monthly aggregation error", ['error' => $e->getMessage()]);
                }
            }
            
            if ($totalUpdated > 0) {
                echo "[" . date('Y-m-d H:i:s') . "] Đã cập nhật $totalUpdated phòng (Total: $updateCount updates, Runtime: {$minutes}m)\n";
                
                $logger->info("Utility demo update", [
                    'rooms_updated' => $totalUpdated,
                    'total_rooms' => count($rooms),
                    'update_count' => $updateCount,
                    'runtime_minutes' => $minutes
                ]);
            }
            
        } catch (Exception $e) {
            echo "[" . date('Y-m-d H:i:s') . "] Lỗi: " . $e->getMessage() . "\n";
            $logger->error("Utility demo daemon error", ['error' => $e->getMessage()]);
        }
        
        // Đợi đến interval tiếp theo
        sleep($intervalSeconds);
    }
    
} catch (Exception $e) {
    echo "Lỗi khởi tạo: " . $e->getMessage() . "\n";
    @unlink($lockFile);
    @unlink($pidFile);
    exit(1);
}
?>

