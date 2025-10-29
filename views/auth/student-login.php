<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Sinh viên - Hệ thống quản lý ký túc xá</title>
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
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
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
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
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
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .switch-login {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .btn-switch {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-switch:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .badge-role {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-top: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
            <h3 class="mb-0">Đăng nhập Sinh viên</h3>
            <p class="mb-0 mt-2">Hệ thống quản lý ký túc xá</p>
            <span class="badge-role">
                <i class="fas fa-user-graduate me-1"></i>Student Portal
            </span>
        </div>
        
        <div class="login-body">
            <div id="alertContainer"></div>
            
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Nhập tên đăng nhập" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Nhập mật khẩu" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                </button>
                
                <div class="text-center">
                    <a href="register.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Đăng ký tài khoản mới
                    </a>
                </div>
            </form>
        </div>
        
        <div class="switch-login">
            <p class="mb-2"><i class="fas fa-user-shield me-2"></i>Bạn là Admin hoặc Cán bộ?</p>
            <a href="admin-login.php" class="btn-switch">
                <i class="fas fa-arrow-right me-1"></i>Đăng nhập tại đây
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng nhập...';
            
            try {
                const response = await fetch('../../api/auth.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Kiểm tra role - chỉ cho phép student
                    if (data.user.role === 'student') {
                        showAlert('success', 'Đăng nhập thành công! Đang chuyển hướng...');
                        setTimeout(() => {
                            window.location.href = '../../views/student/dashboard.php';
                        }, 1500);
                    } else {
                        // Phân biệt message theo role
                        let roleMessage = '';
                        if (data.user.role === 'admin') {
                            roleMessage = '<i class="fas fa-user-shield me-2"></i><strong>Tài khoản Quản trị viên</strong><br>Bạn đang sử dụng tài khoản Admin. Vui lòng đăng nhập tại trang quản trị.';
                        } else if (data.user.role === 'staff') {
                            roleMessage = '<i class="fas fa-user-tie me-2"></i><strong>Tài khoản Cán bộ</strong><br>Bạn đang sử dụng tài khoản Staff. Vui lòng đăng nhập tại trang quản trị.';
                        } else {
                            roleMessage = 'Tài khoản này không có quyền đăng nhập tại đây.';
                        }
                        
                        showAlertWithLink('danger', roleMessage, 'admin-login.php', 'Chuyển đến trang Admin/Staff');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Đăng nhập';
                    }
                } else {
                    showAlert('danger', data.error || 'Đăng nhập thất bại!');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Đăng nhập';
                }
            } catch (error) {
                showAlert('danger', 'Lỗi kết nối! Vui lòng thử lại.');
                console.error('Login error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Đăng nhập';
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

        // Show alert with link
        function showAlertWithLink(type, message, link, linkText) {
            const alertContainer = document.getElementById('alertContainer');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div class="mb-2">${message}</div>
                    <a href="${link}" class="btn btn-sm btn-outline-${type === 'danger' ? 'danger' : 'primary'}">
                        <i class="fas fa-arrow-right me-1"></i>${linkText}
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
        }

        // Check if logout requested
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('logout') === 'success') {
            // Clear any cached data
            localStorage.clear();
            sessionStorage.clear();
            
            // Show logout success message
            showAlert('success', 'Đăng xuất thành công!');
            
            // Remove logout parameter from URL
            window.history.replaceState({}, document.title, 'student-login.php');
        } else if (urlParams.get('logout') === 'true') {
            // Old logout flow - redirect to API
            fetch('../../api/auth.php?action=logout')
                .then(() => {
                    localStorage.clear();
                    sessionStorage.clear();
                    window.location.href = 'student-login.php?logout=success';
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    window.location.href = 'student-login.php?logout=success';
                });
        }

        // Check if already logged in (only if not logging out)
        if (!urlParams.get('logout')) {
            window.addEventListener('load', async function() {
                try {
                    const response = await fetch('../../api/auth.php?action=check');
                    const data = await response.json();
                    
                    if (data.authenticated && data.user.role === 'student') {
                        window.location.href = '../../views/student/dashboard.php';
                    }
                } catch (error) {
                    console.error('Auth check error:', error);
                }
            });
        }
    </script>
</body>
</html>

