# Hệ thống quản lý ký túc xá

Hệ thống quản lý ký túc xá được xây dựng bằng PHP và MySQL, cung cấp đầy đủ các chức năng quản lý cho ký túc xá trường đại học.

## 🚀 Tính năng chính

### 1. Phân quyền & Tài khoản người dùng
- ✅ Hệ thống đăng nhập với mã hóa mật khẩu
- ✅ Phân quyền: Admin, Cán bộ, Sinh viên
- ✅ Đổi mật khẩu, quản lý tài khoản
- ✅ Ghi log hoạt động

### 2. Quản lý phòng ở
- ✅ Quản lý tòa nhà, tầng, phòng
- ✅ Theo dõi trạng thái phòng (trống, đầy, bảo trì)
- ✅ Gợi ý phòng trống khi sinh viên đăng ký
- ✅ Thêm/sửa/xóa phòng

### 3. Quản lý sinh viên
- ✅ Lưu trữ hồ sơ sinh viên đầy đủ
- ✅ Tìm kiếm, lọc theo khoa, lớp, giới tính
- ✅ Cập nhật thông tin sinh viên
- ✅ Quản lý trạng thái sinh viên

### 4. Đăng ký – Gia hạn – Trả phòng
- 🔄 Sinh viên đăng ký phòng trống
- 🔄 Cán bộ duyệt/huỷ yêu cầu
- 🔄 Gia hạn hợp đồng
- 🔄 Thủ tục trả phòng

### 5. Quản lý điện – nước
- 🔄 Nhập chỉ số điện, nước
- 🔄 Tự động tính tiền từng phòng
- 🔄 Lưu lịch sử chỉ số và hóa đơn

### 6. Quản lý thu phí & hóa đơn
- 🔄 Tính tổng phí (phòng + điện + nước)
- 🔄 Ghi nhận thanh toán
- 🔄 Xuất/in hóa đơn
- 🔄 Thống kê doanh thu

### 7. Quản lý thiết bị & bảo trì
- 🔄 Danh sách thiết bị theo phòng
- 🔄 Ghi nhận hư hỏng, yêu cầu sửa
- 🔄 Lưu lịch sử bảo trì/thay thế

### 8. Thông báo & Phản hồi
- 🔄 Quản lý gửi thông báo
- 🔄 Sinh viên gửi phản hồi/khiếu nại
- 🔄 Quản lý trả lời trực tiếp

### 9. Báo cáo – Thống kê
- ✅ Dashboard với biểu đồ thống kê
- ✅ Số sinh viên đang ở/đã trả
- ✅ Doanh thu từng tháng
- ✅ Phòng trống, lượng điện nước
- ✅ Thống kê vi phạm nội quy

## 🛠️ Công nghệ sử dụng

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: Bootstrap 5
- **Charts**: Chart.js
- **Icons**: Font Awesome 6

## 📁 Cấu trúc thư mục

```
baitaplon/
├── api/                    # API endpoints
│   ├── auth.php           # Xác thực
│   ├── rooms.php          # Quản lý phòng
│   ├── students.php       # Quản lý sinh viên
│   └── admin/             # API admin
├── config/                # Cấu hình
│   ├── config.php         # Cấu hình chung
│   ├── database.php       # Kết nối database
│   └── logger.php         # Hệ thống log
├── database/              # Database
│   └── schema.sql         # Schema và dữ liệu mẫu
├── models/                # Models
│   ├── User.php           # Model User
│   ├── Room.php           # Model Room
│   └── Student.php        # Model Student
├── views/                 # Views
│   ├── auth/              # Đăng nhập
│   └── admin/             # Dashboard admin
├── logs/                  # Log files
├── uploads/               # File uploads
└── index.php              # Entry point
```

## 🚀 Cài đặt

### 1. Yêu cầu hệ thống
- PHP 7.4 hoặc cao hơn
- MySQL 8.0 hoặc cao hơn
- Web server (Apache/Nginx)
- XAMPP/WAMP/LAMP

### 2. Cài đặt database
```sql
-- Chạy file schema.sql trong MySQL
mysql -u root -p < database/schema.sql
```

### 3. Cấu hình
Chỉnh sửa file `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dormitory_management');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Truy cập hệ thống
- URL: `http://localhost/baitaplon`
- Tài khoản admin mặc định:
  - Username: `admin`
  - Password: `password`

## 🔐 Bảo mật

- ✅ Mã hóa mật khẩu với `password_hash()`
- ✅ Xác thực session
- ✅ Ghi log hoạt động
- ✅ Validate input
- ✅ Prepared statements (SQL injection protection)

## 📊 API Endpoints

### Authentication
- `POST /api/auth.php?action=login` - Đăng nhập
- `GET /api/auth.php?action=logout` - Đăng xuất
- `GET /api/auth.php?action=check` - Kiểm tra trạng thái
- `POST /api/auth.php?action=change-password` - Đổi mật khẩu

### Rooms
- `GET /api/rooms.php` - Lấy danh sách phòng
- `POST /api/rooms.php` - Tạo phòng mới
- `GET /api/rooms.php?path={id}` - Lấy phòng theo ID
- `PUT /api/rooms.php?path={id}` - Cập nhật phòng
- `DELETE /api/rooms.php?path={id}` - Xóa phòng
- `GET /api/rooms.php?path=available` - Lấy phòng trống

### Students
- `GET /api/students.php` - Lấy danh sách sinh viên
- `POST /api/students.php` - Tạo sinh viên mới
- `GET /api/students.php?path={id}` - Lấy sinh viên theo ID
- `PUT /api/students.php?path={id}` - Cập nhật sinh viên
- `DELETE /api/students.php?path={id}` - Xóa sinh viên
- `GET /api/students.php?path=search` - Tìm kiếm sinh viên
- `GET /api/students.php?path=faculties` - Lấy danh sách khoa

### Admin
- `GET /api/admin/stats.php` - Thống kê tổng quan
- `GET /api/admin/activities.php` - Hoạt động gần đây
- `GET /api/admin/pending-tasks.php` - Nhiệm vụ chờ xử lý

## 🎨 Giao diện

- **Responsive Design**: Tương thích mobile và desktop
- **Modern UI**: Sử dụng Bootstrap 5 và Font Awesome
- **Interactive Charts**: Biểu đồ thống kê với Chart.js
- **User-friendly**: Giao diện thân thiện, dễ sử dụng

## 📝 Ghi chú phát triển

### Đã hoàn thành ✅
- Cấu trúc dự án MVC
- Database schema hoàn chỉnh
- Hệ thống đăng nhập và phân quyền
- API endpoints cơ bản
- Dashboard admin với thống kê
- Giao diện đăng nhập đẹp

### Đang phát triển 🔄
- Module đăng ký phòng
- Quản lý điện nước
- Hệ thống thanh toán
- Quản lý thiết bị và bảo trì
- Thông báo và phản hồi
- Dashboard cho cán bộ và sinh viên

### Kế hoạch 📋
- Import/Export Excel
- Hệ thống backup tự động
- API documentation
- Unit tests
- Performance optimization

## 🤝 Đóng góp

Nếu bạn muốn đóng góp vào dự án:
1. Fork repository
2. Tạo feature branch
3. Commit changes
4. Push và tạo Pull Request

## 📄 License

Dự án này được phát hành dưới MIT License.

## 📞 Liên hệ

Nếu có thắc mắc hoặc cần hỗ trợ, vui lòng liên hệ qua email hoặc tạo issue trên GitHub.

---

**Lưu ý**: Đây là phiên bản demo với các chức năng cơ bản. Các tính năng nâng cao sẽ được phát triển trong các phiên bản tiếp theo.
