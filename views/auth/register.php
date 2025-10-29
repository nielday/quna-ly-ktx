<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hệ thống quản lý ký túc xá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
            max-height: 80vh;
            overflow-y: auto;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        @media (max-height: 800px) {
            .register-body {
                max-height: 60vh;
            }
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus fa-3x mb-3"></i>
            <h3 class="mb-0">Đăng ký tài khoản sinh viên</h3>
            <p class="mb-0 mt-2">Điền thông tin để tạo tài khoản mới</p>
        </div>
        
        <div class="register-body">
            <div id="alertContainer"></div>
            
            <form id="registerForm">
                <!-- Thông tin đăng nhập -->
                <h5 class="mb-3 text-primary"><i class="fas fa-user me-2"></i>Thông tin đăng nhập</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên đăng nhập *</label>
                        <input type="text" class="form-control" id="username" required>
                        <small class="text-muted">Chỉ sử dụng chữ cái, số và dấu gạch dưới</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mật khẩu *</label>
                        <input type="password" class="form-control" id="password" required>
                        <small class="text-muted">Tối thiểu 6 ký tự</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Xác nhận mật khẩu *</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Thông tin cá nhân -->
                <h5 class="mb-3 text-primary"><i class="fas fa-id-card me-2"></i>Thông tin cá nhân</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên *</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại *</label>
                        <input type="tel" class="form-control" id="phone" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mã số sinh viên *</label>
                        <input type="text" class="form-control" id="studentCode" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giới tính *</label>
                        <select class="form-select" id="gender" required>
                            <option value="">Chọn giới tính</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Khoa *</label>
                        <input type="text" class="form-control" id="faculty" placeholder="Ví dụ: Công nghệ Thông tin" required>
                    </div>
                </div>
                
                <!-- Optional fields collapsed by default -->
                <div class="mb-3">
                    <button type="button" class="btn btn-link p-0" data-bs-toggle="collapse" data-bs-target="#moreInfo" aria-expanded="false">
                        <i class="fas fa-plus-circle me-1"></i>Thêm thông tin (tùy chọn)
                    </button>
                </div>
                
                <div class="collapse" id="moreInfo">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lớp</label>
                            <input type="text" class="form-control" id="className" placeholder="Ví dụ: CNTT2021">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="dateOfBirth">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quê quán</label>
                        <input type="text" class="form-control" id="hometown">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Người liên hệ khẩn cấp</label>
                            <input type="text" class="form-control" id="emergencyContact">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SĐT liên hệ khẩn cấp</label>
                            <input type="tel" class="form-control" id="emergencyPhone">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-register w-100 mt-3">
                    <i class="fas fa-user-plus me-2"></i>Đăng ký
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="student-login.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                </a>
            </div>
            <div class="text-center mt-3">
                <small class="text-muted">
                    Đã có tài khoản? 
                    <a href="student-login.php" class="text-primary fw-bold">Đăng nhập ngay</a>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle form submission
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get form values
            const formData = {
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                confirmPassword: document.getElementById('confirmPassword').value,
                fullName: document.getElementById('fullName').value,
                phone: document.getElementById('phone').value,
                studentCode: document.getElementById('studentCode').value,
                gender: document.getElementById('gender').value,
                dateOfBirth: document.getElementById('dateOfBirth').value,
                faculty: document.getElementById('faculty').value,
                className: document.getElementById('className').value,
                hometown: document.getElementById('hometown').value,
                emergencyContact: document.getElementById('emergencyContact').value,
                emergencyPhone: document.getElementById('emergencyPhone').value
            };
            
            // Validate password match
            if (formData.password !== formData.confirmPassword) {
                showAlert('danger', 'Mật khẩu xác nhận không khớp!');
                return;
            }
            
            // Validate password strength
            if (formData.password.length < 6) {
                showAlert('danger', 'Mật khẩu phải có ít nhất 6 ký tự!');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng ký...';
            
            try {
                const response = await fetch('../../api/auth.php?action=register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', 'Đăng ký thành công! Đang chuyển đến trang đăng nhập...');
                    
                    setTimeout(() => {
                        window.location.href = 'student-login.php';
                    }, 2000);
                } else {
                    showAlert('danger', data.error || 'Đăng ký thất bại!');
                }
            } catch (error) {
                showAlert('danger', 'Lỗi kết nối! Vui lòng thử lại.');
                console.error('Register error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Đăng ký';
            }
        });
        
        // Show alert function
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
        }
    </script>
</body>
</html>

