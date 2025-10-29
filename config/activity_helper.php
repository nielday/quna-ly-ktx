<?php
/**
 * Activity Logging Helper
 * 
 * Helper functions để ghi log hoạt động của hệ thống
 * Sử dụng trong các API để tự động ghi log các thao tác
 */

require_once 'database.php';
require_once 'logger.php';
require_once __DIR__ . '/../models/ActivityLog.php';

/**
 * Ghi log hoạt động
 * 
 * @param int $userId User ID thực hiện hành động
 * @param string $action Tên hành động (create, update, delete, login, etc.)
 * @param string $tableName Tên bảng (optional)
 * @param int $recordId ID record (optional)
 * @param array $oldValues Giá trị cũ (optional)
 * @param array $newValues Giá trị mới (optional)
 * @return bool
 */
function logActivity($userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    try {
        $activityLog = new ActivityLog();
        return $activityLog->logActivity($userId, $action, $tableName, $recordId, $oldValues, $newValues);
    } catch (Exception $e) {
        error_log("Activity logging failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Ghi log đăng nhập
 */
function logLogin($userId) {
    return logActivity($userId, 'login');
}

/**
 * Ghi log đăng xuất
 */
function logLogout($userId) {
    return logActivity($userId, 'logout');
}

/**
 * Ghi log tạo record
 */
function logCreate($userId, $tableName, $recordId, $newValues = null) {
    return logActivity($userId, 'create', $tableName, $recordId, null, $newValues);
}

/**
 * Ghi log cập nhật record
 */
function logUpdate($userId, $tableName, $recordId, $oldValues = null, $newValues = null) {
    return logActivity($userId, 'update', $tableName, $recordId, $oldValues, $newValues);
}

/**
 * Ghi log xóa record
 */
function logDelete($userId, $tableName, $recordId, $oldValues = null) {
    return logActivity($userId, 'delete', $tableName, $recordId, $oldValues, null);
}

/**
 * Ghi log thanh toán
 */
function logPayment($userId, $tableName, $recordId, $newValues = null) {
    return logActivity($userId, 'payment', $tableName, $recordId, null, $newValues);
}

/**
 * Ghi log phê duyệt
 */
function logApprove($userId, $tableName, $recordId, $newValues = null) {
    return logActivity($userId, 'approve', $tableName, $recordId, null, $newValues);
}

/**
 * Ghi log từ chối
 */
function logReject($userId, $tableName, $recordId, $oldValues = null) {
    return logActivity($userId, 'reject', $tableName, $recordId, $oldValues, null);
}

/**
 * Lấy current user ID từ session
 * Helper để lấy user ID hiện tại
 */
function getCurrentUserId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null;
}

/**
 * Check và ghi log nếu có user trong session
 */
function autoLogActivity($action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    $userId = getCurrentUserId();
    if ($userId) {
        return logActivity($userId, $action, $tableName, $recordId, $oldValues, $newValues);
    }
    return false;
}

/**
 * Ghi log với mô tả chi tiết
 */
function logWithDescription($userId, $action, $description, $tableName = null, $recordId = null) {
    $context = ['description' => $description];
    return logActivity($userId, $action, $tableName, $recordId, null, $context);
}

?>

