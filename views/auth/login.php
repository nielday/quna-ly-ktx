<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn loại đăng nhập - Hệ thống quản lý ký túc xá</title>
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
        .container {
            max-width: 900px;
        }
        .page-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }
        .login-option {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        .login-option:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        .login-option.student {
            border-top: 5px solid #667eea;
        }
        .login-option.admin {
            border-top: 5px solid #f5576c;
        }
        .login-option i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        .login-option.student i {
            color: #667eea;
        }
        .login-option.admin i {
            color: #f5576c;
        }
        .login-option h3 {
            margin-bottom: 1rem;
            font-weight: 700;
        }
        .login-option p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        .btn-login-choice {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        .btn-student {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-student:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-admin {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-admin:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
        }
        .feature-list {
            text-align: left;
            margin: 1rem 0;
        }
        .feature-list li {
            margin-bottom: 0.5rem;
            color: #495057;
        }
        .feature-list i {
            font-size: 1rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <i class="fas fa-university fa-4x mb-3"></i>
            <h1>Hệ thống Quản lý Ký túc xá</h1>
            <p class="lead">Vui lòng chọn loại tài khoản để đăng nhập</p>
        </div>
        
        <div class="row g-4">
            <!-- Student Login -->
            <div class="col-md-6">
                <div class="login-option student" onclick="window.location.href='student-login.php'">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Sinh viên</h3>
                    <p>Đăng nhập dành cho sinh viên đang ở ký túc xá</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle text-success"></i>Đăng ký phòng</li>
                        <li><i class="fas fa-check-circle text-success"></i>Xem hóa đơn điện nước</li>
                        <li><i class="fas fa-check-circle text-success"></i>Gửi phản hồi & báo cáo</li>
                        <li><i class="fas fa-check-circle text-success"></i>Yêu cầu sửa chữa</li>
                    </ul>
                    <button class="btn btn-student btn-login-choice">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập Sinh viên
                    </button>
                </div>
            </div>
            
            <!-- Admin/Staff Login -->
            <div class="col-md-6">
                <div class="login-option admin" onclick="window.location.href='admin-login.php'">
                    <i class="fas fa-user-shield"></i>
                    <h3>Admin / Cán bộ</h3>
                    <p>Đăng nhập dành cho quản trị viên và cán bộ quản lý</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle text-danger"></i>Quản lý sinh viên & phòng</li>
                        <li><i class="fas fa-check-circle text-danger"></i>Duyệt đăng ký phòng</li>
                        <li><i class="fas fa-check-circle text-danger"></i>Quản lý điện nước</li>
                        <li><i class="fas fa-check-circle text-danger"></i>Thống kê & báo cáo</li>
                    </ul>
                    <button class="btn btn-admin btn-login-choice">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập Admin/Staff
                    </button>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white">
                <i class="fas fa-question-circle me-2"></i>
                Cần trợ giúp? Liên hệ quản trị viên
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
