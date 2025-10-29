-- Fix cho schema.sql
-- Chạy file này để cập nhật database với các sửa đổi cần thiết

USE dormitory_management;

-- 1. Thêm status 'checked_in' vào room_registrations
ALTER TABLE room_registrations 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'checked_in', 'completed') DEFAULT 'pending';

-- 2. Thêm missing indexes để tối ưu performance
CREATE INDEX IF NOT EXISTS idx_registrations_room ON room_registrations(room_id);
CREATE INDEX IF NOT EXISTS idx_equipment_status ON equipment(status);
CREATE INDEX IF NOT EXISTS idx_equipment_room ON equipment(room_id);

-- 3. Thêm index cho bảng utility_readings (cải thiện query performance)
CREATE INDEX IF NOT EXISTS idx_utility_paid ON utility_readings(is_paid);

-- Kiểm tra kết quả
SELECT 'Schema fixes applied successfully!' as message;

