-- Database schema cho hệ thống quản lý ký túc xá
-- Tạo database
CREATE DATABASE IF NOT EXISTS dormitory_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dormitory_management;

-- Bảng người dùng (users)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff', 'student') NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng tòa nhà (buildings)
CREATE TABLE buildings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    total_floors INT NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng phòng (rooms)
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    building_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    floor_number INT NOT NULL,
    capacity INT NOT NULL DEFAULT 4,
    current_occupancy INT DEFAULT 0,
    status ENUM('available', 'full', 'maintenance', 'reserved') DEFAULT 'available',
    room_type ENUM('standard', 'premium', 'vip') DEFAULT 'standard',
    monthly_fee DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_room (building_id, room_number)
);

-- Bảng sinh viên (students)
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    student_code VARCHAR(20) UNIQUE NOT NULL,
    faculty VARCHAR(100) NOT NULL,
    class_name VARCHAR(50),
    gender ENUM('male', 'female') NOT NULL,
    date_of_birth DATE,
    hometown VARCHAR(100),
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    id_card VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng đăng ký phòng (room_registrations)
CREATE TABLE room_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    registration_date DATE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'active', 'checked_in', 'completed') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Bảng chỉ số điện nước (utility_readings)
CREATE TABLE utility_readings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    reading_date DATE NOT NULL,
    electricity_reading INT NOT NULL DEFAULT 0,
    water_reading INT NOT NULL DEFAULT 0,
    electricity_rate DECIMAL(8,2) DEFAULT 0,
    water_rate DECIMAL(8,2) DEFAULT 0,
    total_amount DECIMAL(10,2) DEFAULT 0,
    is_paid BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reading (room_id, reading_date)
);

-- Bảng thanh toán (payments)
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    room_registration_id INT NOT NULL,
    payment_type ENUM('room_fee', 'utility', 'penalty', 'deposit') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'card') NOT NULL,
    reference_number VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_registration_id) REFERENCES room_registrations(id) ON DELETE CASCADE
);

-- Bảng thiết bị (equipment)
CREATE TABLE equipment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    equipment_name VARCHAR(100) NOT NULL,
    equipment_type VARCHAR(50) NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    serial_number VARCHAR(100),
    purchase_date DATE,
    warranty_expiry DATE,
    status ENUM('working', 'broken', 'maintenance', 'replaced') DEFAULT 'working',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Bảng bảo trì (maintenance_requests)
CREATE TABLE maintenance_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    equipment_id INT,
    student_id INT,
    request_type ENUM('equipment', 'room', 'utility') NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    assigned_to INT,
    estimated_cost DECIMAL(10,2),
    actual_cost DECIMAL(10,2),
    completion_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Bảng thông báo (notifications)
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('general', 'payment', 'maintenance', 'rule_violation') DEFAULT 'general',
    target_audience ENUM('all', 'students', 'staff', 'specific') DEFAULT 'all',
    target_users JSON,
    is_urgent BOOLEAN DEFAULT FALSE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng phản hồi (feedback)
CREATE TABLE feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    category ENUM('complaint', 'suggestion', 'compliment', 'other') DEFAULT 'other',
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    response TEXT,
    responded_by INT,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (responded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Bảng logs hoạt động (activity_logs)
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tạo indexes để tối ưu hiệu suất
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_rooms_building ON rooms(building_id);
CREATE INDEX idx_rooms_status ON rooms(status);
CREATE INDEX idx_students_faculty ON students(faculty);
CREATE INDEX idx_registrations_status ON room_registrations(status);
CREATE INDEX idx_registrations_room ON room_registrations(room_id);
CREATE INDEX idx_registrations_dates ON room_registrations(start_date, end_date);
CREATE INDEX idx_utility_room_date ON utility_readings(room_id, reading_date);
CREATE INDEX idx_utility_paid ON utility_readings(is_paid);
CREATE INDEX idx_payments_student ON payments(student_id);
CREATE INDEX idx_payments_date ON payments(payment_date);
CREATE INDEX idx_equipment_status ON equipment(status);
CREATE INDEX idx_equipment_room ON equipment(room_id);
CREATE INDEX idx_maintenance_status ON maintenance_requests(status);
CREATE INDEX idx_notifications_type ON notifications(type);
CREATE INDEX idx_feedback_status ON feedback(status);
CREATE INDEX idx_logs_user_action ON activity_logs(user_id, action);
CREATE INDEX idx_logs_created ON activity_logs(created_at);

-- Insert dữ liệu mẫu
-- Tạo admin mặc định
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@dormitory.com', 'Quản trị viên', 'admin');

-- Tạo tòa nhà mẫu
INSERT INTO buildings (name, address, total_floors) VALUES 
('Tòa A', '123 Đường ABC, Quận 1, TP.HCM', 5),
('Tòa B', '456 Đường DEF, Quận 2, TP.HCM', 4);

-- Tạo phòng mẫu
INSERT INTO rooms (building_id, room_number, floor_number, capacity, monthly_fee) VALUES 
(1, 'A101', 1, 4, 500000),
(1, 'A102', 1, 4, 500000),
(1, 'A201', 2, 4, 500000),
(2, 'B101', 1, 4, 450000),
(2, 'B102', 1, 4, 450000);
