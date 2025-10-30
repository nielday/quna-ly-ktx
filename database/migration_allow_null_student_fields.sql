-- Migration: Cho phép các trường student có thể NULL khi admin tạo user
-- Sinh viên sẽ tự điền sau khi đăng nhập lần đầu
-- Date: 2025-10-29

USE dormitory_management;

-- Sửa bảng students để cho phép NULL (tránh duplicate index)
-- Bước 1: Sửa các cột thành NULL (không thêm UNIQUE vào student_code vì đã có sẵn)
ALTER TABLE students 
MODIFY COLUMN student_code VARCHAR(20) NULL,
MODIFY COLUMN faculty VARCHAR(100) NULL,
MODIFY COLUMN gender ENUM('male', 'female') NULL;

-- Bước 2: Kiểm tra xem có duplicate index không
-- SHOW INDEX FROM students WHERE Column_name = 'student_code';

-- Bước 3: Nếu có index duplicate (student_code_2), xóa nó
-- ALTER TABLE students DROP INDEX student_code_2;

-- Cập nhật các record hiện có nếu có (optional)
-- UPDATE students SET student_code = NULL WHERE student_code = '';
-- UPDATE students SET faculty = NULL WHERE faculty = '';
-- UPDATE students SET gender = NULL WHERE gender = '';

