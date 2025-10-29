<?php
require_once 'config/database.php';

echo "<h2>Test kết nối Database</h2>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<p style='color: green;'>✅ Kết nối database thành công!</p>";
    
    // Kiểm tra database có tồn tại không
    $query = "SELECT DATABASE() as current_db";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    
    echo "<p><strong>Database hiện tại:</strong> " . $result['current_db'] . "</p>";
    
    // Kiểm tra các bảng có tồn tại không
    $query = "SHOW TABLES";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p style='color: green;'>✅ Database đã có " . count($tables) . " bảng:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Database chưa có bảng nào. Cần import file schema.sql</p>";
        echo "<p><strong>Hướng dẫn:</strong></p>";
        echo "<ol>";
        echo "<li>Mở phpMyAdmin hoặc MySQL Workbench</li>";
        echo "<li>Tạo database tên 'dormitory_management'</li>";
        echo "<li>Import file database/schema.sql</li>";
        echo "</ol>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi kết nối database: " . $e->getMessage() . "</p>";
    echo "<p><strong>Cấu hình hiện tại:</strong></p>";
    echo "<ul>";
    echo "<li>Host: " . DB_HOST . "</li>";
    echo "<li>Port: " . DB_PORT . "</li>";
    echo "<li>Database: " . DB_NAME . "</li>";
    echo "<li>User: " . DB_USER . "</li>";
    echo "<li>Password: " . str_repeat('*', strlen(DB_PASS)) . "</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Quay lại trang chủ</a></p>";
?>
