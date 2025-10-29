<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sinh viên - Hệ thống quản lý ký túc xá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        .info-card {
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-user-graduate me-2"></i>
                        Sinh viên
                    </h4>
                </div>
                
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="#" data-section="dashboard">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="#" data-section="my-room">
                        <i class="fas fa-door-open me-2"></i>Phòng của tôi
                    </a>
                    <a class="nav-link" href="#" data-section="registration">
                        <i class="fas fa-clipboard-list me-2"></i>Đăng ký phòng
                    </a>
                    <a class="nav-link" href="#" data-section="utilities">
                        <i class="fas fa-chart-line me-2"></i>Điện nước
                    </a>
                    <a class="nav-link" href="#" data-section="payments">
                        <i class="fas fa-credit-card me-2"></i>Hóa đơn & Thanh toán
                    </a>
                    <a class="nav-link" href="#" data-section="maintenance">
                        <i class="fas fa-tools me-2"></i>Yêu cầu sửa chữa
                    </a>
                    <a class="nav-link" href="#" data-section="feedback">
                        <i class="fas fa-comment me-2"></i>Gửi phản hồi
                    </a>
                </nav>
                
                <div class="mt-auto p-3">
                    <a href="../../api/auth.php?action=logout" class="btn btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <h5 class="mb-0" id="pageTitle">Dashboard</h5>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Xin chào, <strong id="userName">Sinh viên</strong></span>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-section="profile"><i class="fas fa-user me-2"></i>Thông tin cá nhân</a></li>
                                    <li><a class="dropdown-item" href="#" data-section="change-password"><i class="fas fa-key me-2"></i>Đổi mật khẩu</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="../../api/auth.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Dashboard Content -->
                <div id="dashboardContent">
                    <!-- My Room Info -->
                    <div class="card info-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-door-open me-2 text-primary"></i>Thông tin phòng
                            </h5>
                            <div class="row" id="roomInfo">
                                <div class="col-md-6 mb-3">
                                    <strong>Bạn chưa đăng ký phòng nào</strong>
                                    <p class="text-muted">Vui lòng đăng ký phòng để tiếp tục sử dụng hệ thống.</p>
                                </div>
                                <div class="col-md-6">
                                    <a href="#" class="btn btn-primary" onclick="loadSection('registration')">
                                        <i class="fas fa-plus me-2"></i>Đăng ký phòng ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card stat-icon mb-2"></i>
                                    <h3 id="pendingInvoices">0</h3>
                                    <p class="mb-0">Hóa đơn chưa thanh toán</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-circle stat-icon mb-2"></i>
                                    <h3 id="maintenanceRequests">0</h3>
                                    <p class="mb-0">Yêu cầu đang chờ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-bell stat-icon mb-2"></i>
                                    <h3 id="notifications">0</h3>
                                    <p class="mb-0">Thông báo mới</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-tools stat-icon mb-2"></i>
                                    <h3 id="equipmentIssues">0</h3>
                                    <p class="mb-0">Thiết bị cần báo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Điện nước gần đây</h5>
                                </div>
                                <div class="card-body">
                                    <div id="utilityHistory">
                                        <p class="text-muted">Chưa có dữ liệu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Thanh toán gần đây</h5>
                                </div>
                                <div class="card-body">
                                    <div id="paymentHistory">
                                        <p class="text-muted">Chưa có dữ liệu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Other sections will be loaded here -->
                <div id="dynamicContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let currentUser = null;
        let studentId = null;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadDashboardData();
            
            // Sidebar navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    loadSection(section);
                    
                    // Update active state
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Handle dropdown menu items with data-section
            document.querySelectorAll('.dropdown-item[data-section]').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    loadSection(section);
                    
                    // Remove active state from nav links
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                });
            });
        });

        // Check authentication và load student info
        async function checkAuth() {
            try {
                const response = await fetch('../../api/auth.php?action=check');
                const data = await response.json();
                
                if (data.authenticated && data.user.role === 'student') {
                    currentUser = data.user;
                    studentId = data.user.id;
                    document.getElementById('userName').textContent = currentUser.full_name;
                    
                    // Load thông tin sinh viên chi tiết
                    await loadStudentInfo();
                } else {
                    window.location.href = '../auth/login.php';
                }
            } catch (error) {
                console.error('Auth check error:', error);
                window.location.href = '../auth/login.php';
            }
        }
        
        // Load thông tin sinh viên chi tiết
        async function loadStudentInfo() {
            try {
                const response = await fetch('../../api/students.php?action=get_current_student');
                const data = await response.json();
                
                if (data.success && data.student) {
                    window.currentStudent = data.student;
                    console.log('Student info loaded:', data.student);
                }
            } catch (error) {
                console.error('Error loading student info:', error);
            }
        }

        // Load dashboard data
        async function loadDashboardData() {
            try {
                // Load room information
                await loadRoomInfo();
                
                // Load statistics thực tế
                await loadDashboardStats();
                
                // Load recent data cho dashboard
                await loadRecentUtilities();
                await loadRecentPayments();
                
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }
        
        // Load các số liệu thống kê dashboard
        async function loadDashboardStats() {
            try {
                // Load số hóa đơn chưa thanh toán
                const paymentsResponse = await fetch('../../api/payments.php?my=true&status=pending');
                const paymentsData = await paymentsResponse.json();
                if (paymentsData.success && paymentsData.data) {
                    document.getElementById('pendingInvoices').textContent = paymentsData.data.length;
                } else {
                    document.getElementById('pendingInvoices').textContent = '0';
                }
                
                // Load số yêu cầu bảo trì
                const maintenanceResponse = await fetch('../../api/maintenance.php?action=my');
                const maintenanceData = await maintenanceResponse.json();
                if (maintenanceData.success && maintenanceData.data) {
                    const pendingMaintenance = maintenanceData.data.filter(m => 
                        m.status === 'pending' || m.status === 'in_progress'
                    );
                    document.getElementById('maintenanceRequests').textContent = pendingMaintenance.length;
                } else {
                    document.getElementById('maintenanceRequests').textContent = '0';
                }
                
                // Load số thông báo (có thể thêm sau)
                document.getElementById('notifications').textContent = '0';
                
                // Load thiết bị hỏng trong phòng
                if (window.currentRoomId) {
                    const equipmentResponse = await fetch(`../../api/equipment.php?room_id=${window.currentRoomId}`);
                    const equipmentData = await equipmentResponse.json();
                    if (equipmentData.success && equipmentData.data) {
                        const brokenEquipment = equipmentData.data.filter(e => 
                            e.status === 'broken' || e.status === 'maintenance'
                        );
                        document.getElementById('equipmentIssues').textContent = brokenEquipment.length;
                    } else {
                        document.getElementById('equipmentIssues').textContent = '0';
                    }
                } else {
                    document.getElementById('equipmentIssues').textContent = '0';
                }
                
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
                // Set default values on error
                document.getElementById('pendingInvoices').textContent = '0';
                document.getElementById('maintenanceRequests').textContent = '0';
                document.getElementById('notifications').textContent = '0';
                document.getElementById('equipmentIssues').textContent = '0';
            }
        }

        // Load điện nước gần đây cho dashboard
        async function loadRecentUtilities() {
            try {
                if (!window.currentRoomId) {
                    document.getElementById('utilityHistory').innerHTML = `
                        <p class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Bạn chưa có phòng để xem dữ liệu điện nước
                        </p>
                    `;
                    return;
                }
                
                const response = await fetch(`../../api/utilities.php?action=history&room_id=${window.currentRoomId}&limit=3`);
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    const utilities = data.data;
                    let html = '<div class="list-group list-group-flush">';
                    
                    utilities.forEach(util => {
                        const isPaid = util.is_paid;
                        const statusClass = isPaid ? 'text-success' : 'text-warning';
                        const statusIcon = isPaid ? 'check-circle' : 'clock';
                        
                        html += `
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            ${new Date(util.reading_date).toLocaleDateString('vi-VN')}
                                        </small>
                                        <div class="mt-1">
                                            <span class="me-3">
                                                <i class="fas fa-bolt text-warning"></i> 
                                                ${util.electricity_reading} kWh
                                            </span>
                                            <span>
                                                <i class="fas fa-tint text-info"></i> 
                                                ${util.water_reading} m³
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-danger">${formatCurrency(util.total_amount)}</strong>
                                        <div>
                                            <small class="${statusClass}">
                                                <i class="fas fa-${statusIcon}"></i>
                                                ${isPaid ? 'Đã trả' : 'Chưa trả'}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    html += `
                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="loadSection('utilities')">
                                <i class="fas fa-arrow-right me-2"></i>Xem tất cả
                            </button>
                        </div>
                    `;
                    
                    document.getElementById('utilityHistory').innerHTML = html;
                } else {
                    document.getElementById('utilityHistory').innerHTML = `
                        <p class="text-muted">
                            <i class="fas fa-inbox me-2"></i>
                            Chưa có dữ liệu điện nước
                        </p>
                    `;
                }
            } catch (error) {
                console.error('Error loading recent utilities:', error);
                document.getElementById('utilityHistory').innerHTML = `
                    <p class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Lỗi tải dữ liệu
                    </p>
                `;
            }
        }
        
        // Load thanh toán gần đây cho dashboard
        async function loadRecentPayments() {
            try {
                const response = await fetch('../../api/payments.php?my=true&limit=3');
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    const payments = data.data;
                    let html = '<div class="list-group list-group-flush">';
                    
                    payments.forEach(payment => {
                        const typeLabels = {
                            'utility': '<span class="badge bg-info">Điện nước</span>',
                            'room_fee': '<span class="badge bg-primary">Tiền phòng</span>',
                            'penalty': '<span class="badge bg-warning">Phạt</span>',
                            'deposit': '<span class="badge bg-secondary">Đặt cọc</span>'
                        };
                        
                        const statusClass = payment.status === 'completed' ? 'text-success' : 'text-warning';
                        const statusIcon = payment.status === 'completed' ? 'check-circle' : 'clock';
                        const statusText = payment.status === 'completed' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        
                        html += `
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="mb-1">
                                            ${typeLabels[payment.payment_type] || ''}
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            ${new Date(payment.payment_date).toLocaleDateString('vi-VN')}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-danger">${formatCurrency(payment.amount)}</strong>
                                        <div>
                                            <small class="${statusClass}">
                                                <i class="fas fa-${statusIcon}"></i>
                                                ${statusText}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    html += `
                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="loadSection('payments')">
                                <i class="fas fa-arrow-right me-2"></i>Xem tất cả
                            </button>
                        </div>
                    `;
                    
                    document.getElementById('paymentHistory').innerHTML = html;
                } else {
                    document.getElementById('paymentHistory').innerHTML = `
                        <p class="text-muted">
                            <i class="fas fa-inbox me-2"></i>
                            Chưa có hóa đơn thanh toán
                        </p>
                    `;
                }
            } catch (error) {
                console.error('Error loading recent payments:', error);
                document.getElementById('paymentHistory').innerHTML = `
                    <p class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Lỗi tải dữ liệu
                    </p>
                `;
            }
        }

        async function loadRoomInfo() {
            try {
                const response = await fetch('../../api/registrations.php?my=true');
                const data = await response.json();
                
                const roomInfoContainer = document.getElementById('roomInfo');
                
                if (data.success && data.data && data.data.length > 0) {
                    // Tìm đăng ký đang active hoặc approved
                    const activeRegistration = data.data.find(reg => 
                        reg.status === 'active' || reg.status === 'approved'
                    );
                    
                    if (activeRegistration) {
                        // Set global room ID for other sections
                        window.currentRoomId = activeRegistration.room_id;
                        window.currentBuildingId = activeRegistration.building_id;
                        
                        roomInfoContainer.innerHTML = `
                            <div class="col-md-6 mb-3">
                                <h6 class="text-primary"><i class="fas fa-bed me-2"></i>Phòng của tôi</h6>
                                <div class="info-item mb-2">
                                    <strong>Số phòng:</strong> ${activeRegistration.room_number}
                                </div>
                                <div class="info-item mb-2">
                                    <strong>Tòa nhà:</strong> ${activeRegistration.building_name}
                                </div>
                                <div class="info-item mb-2">
                                    <strong>Số người ở:</strong> ${activeRegistration.current_occupancy}/${activeRegistration.capacity} người
                                </div>
                                <div class="info-item mb-2">
                                    <strong>Giá phòng:</strong> <span class="text-danger fw-bold">${formatCurrency(activeRegistration.monthly_fee)}/tháng</span>
                                </div>
                                <div class="info-item mb-2">
                                    <strong>Trạng thái:</strong> 
                                    <span class="badge bg-success">Đang ở</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-primary mb-2" onclick="loadSection('my-room')" style="width: 100%;">
                                    <i class="fas fa-eye me-2"></i>Xem chi tiết phòng
                                </button>
                                <button class="btn btn-outline-secondary" onclick="loadSection('utilities')" style="width: 100%;">
                                    <i class="fas fa-chart-line me-2"></i>Điện nước
                                </button>
                            </div>
                        `;
                    } else {
                        // Có đăng ký nhưng chưa được duyệt - Clear room ID
                        window.currentRoomId = null;
                        window.currentBuildingId = null;
                        
                        roomInfoContainer.innerHTML = `
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Đang chờ duyệt:</strong> 
                                    ${data.data.map(reg => `Phòng ${reg.room_number} - ${reg.building_name}`).join(', ')}
                                </div>
                                <button class="btn btn-primary" onclick="loadSection('registration')">
                                    <i class="fas fa-clipboard-list me-2"></i>Xem đăng ký của tôi
                                </button>
                            </div>
                        `;
                    }
                } else {
                    // Chưa có đăng ký - giữ nguyên UI mặc định
                    window.currentRoomId = null;
                    window.currentBuildingId = null;
                }
            } catch (error) {
                console.error('Error loading room info:', error);
                window.currentRoomId = null;
                window.currentBuildingId = null;
            }
        }

        // Load section
        function loadSection(section) {
            const pageTitle = document.getElementById('pageTitle');
            const dynamicContent = document.getElementById('dynamicContent');
            const dashboardContent = document.getElementById('dashboardContent');
            
            // Hide dashboard content
            dashboardContent.style.display = 'none';
            dynamicContent.style.display = 'block';
            
            switch(section) {
                case 'dashboard':
                    dashboardContent.style.display = 'block';
                    dynamicContent.style.display = 'none';
                    pageTitle.textContent = 'Dashboard';
                    loadDashboardData();
                    break;
                case 'my-room':
                    pageTitle.textContent = 'Phòng của tôi';
                    loadMyRoomSection();
                    break;
                case 'registration':
                    pageTitle.textContent = 'Đăng ký phòng';
                    loadRegistrationSection();
                    break;
                case 'utilities':
                    pageTitle.textContent = 'Điện nước';
                    loadUtilitiesSection();
                    break;
                case 'payments':
                    pageTitle.textContent = 'Hóa đơn & Thanh toán';
                    loadPaymentsSection();
                    break;
                case 'maintenance':
                    pageTitle.textContent = 'Yêu cầu sửa chữa';
                    loadMaintenanceSection();
                    break;
                case 'feedback':
                    pageTitle.textContent = 'Gửi phản hồi';
                    loadFeedbackSection();
                    break;
                case 'profile':
                    pageTitle.textContent = 'Thông tin cá nhân';
                    loadProfileSection();
                    break;
                case 'change-password':
                    pageTitle.textContent = 'Đổi mật khẩu';
                    loadChangePasswordSection();
                    break;
                default:
                    pageTitle.textContent = 'Chức năng đang phát triển';
                    dynamicContent.innerHTML = `
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <h5>Chức năng đang được phát triển</h5>
                                <p class="text-muted">Chức năng này sẽ có sẵn trong phiên bản tiếp theo.</p>
                            </div>
                        </div>
                    `;
            }
        }

        // Section loaders
        function loadMyRoomSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>Thông tin phòng</h5>
                    </div>
                    <div class="card-body" id="myRoomContent">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                            <p>Đang tải thông tin...</p>
                        </div>
                    </div>
                </div>
            `;
            
            loadMyRoomData();
        }

        async function loadMyRoomData() {
            try {
                const response = await fetch('../../api/registrations.php?my=true');
                const data = await response.json();
                
                const container = document.getElementById('myRoomContent');
                
                if (data.success && data.data && data.data.length > 0) {
                    // Tìm đăng ký đang active hoặc approved
                    const activeRegistration = data.data.find(reg => 
                        reg.status === 'active' || reg.status === 'approved'
                    );
                    
                    if (activeRegistration) {
                        container.innerHTML = `
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fas fa-bed me-2"></i>Thông tin phòng</h6>
                                    <div class="info-item mb-2">
                                        <strong>Số phòng:</strong> ${activeRegistration.room_number}
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Tòa nhà:</strong> ${activeRegistration.building_name}
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Loại phòng:</strong> ${activeRegistration.room_type || 'Standard'}
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Sức chứa:</strong> ${activeRegistration.capacity} người
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Số người đang ở:</strong> ${activeRegistration.current_occupancy}/${activeRegistration.capacity} người
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Giá phòng:</strong> <span class="text-danger fw-bold">${formatCurrency(activeRegistration.monthly_fee)}/tháng</span>
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Trạng thái:</strong> 
                                        <span class="badge bg-success">Đang ở</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fas fa-calendar me-2"></i>Thời gian</h6>
                                    <div class="info-item mb-2">
                                        <strong>Ngày bắt đầu:</strong> ${formatDate(activeRegistration.start_date)}
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Ngày kết thúc:</strong> ${formatDate(activeRegistration.end_date)}
                                    </div>
                                    <div class="info-item mb-2">
                                        <strong>Ngày đăng ký:</strong> ${formatDate(activeRegistration.registration_date || activeRegistration.created_at)}
                                    </div>
                                </div>
                            </div>
                            ${activeRegistration.notes ? `
                                <div class="mt-3 p-3 bg-light rounded">
                                    <strong><i class="fas fa-sticky-note me-2"></i>Ghi chú:</strong>
                                    <p class="mb-0 mt-2">${activeRegistration.notes}</p>
                                </div>
                            ` : ''}
                        `;
                    } else {
                        // Có đăng ký nhưng chưa được duyệt
                        container.innerHTML = `
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-clock me-2"></i>Đang chờ duyệt</h6>
                                <p>Đăng ký của bạn đang chờ được duyệt. Vui lòng đợi cán bộ xử lý.</p>
                            </div>
                            ${data.data.map(reg => `
                                <div class="card bg-light mb-2">
                                    <div class="card-body">
                                        <strong>Phòng ${reg.room_number}</strong> - ${reg.building_name}
                                        <span class="badge bg-warning float-end">${reg.status === 'pending' ? 'Chờ duyệt' : reg.status}</span>
                                    </div>
                                </div>
                            `).join('')}
                        `;
                    }
                } else {
                    // Chưa có đăng ký
                    container.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Bạn chưa có phòng được gán. Vui lòng đăng ký phòng để tiếp tục.
                        </div>
                        <div class="text-center">
                            <a href="#" class="btn btn-primary btn-lg" onclick="loadSection('registration')">
                                <i class="fas fa-clipboard-list me-2"></i>Đăng ký phòng ngay
                            </a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading my room data:', error);
                document.getElementById('myRoomContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Không thể tải thông tin phòng.
                    </div>
                `;
            }
        }

        function loadRegistrationSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Đăng ký phòng</h5>
                        <button class="btn btn-primary" onclick="showRegisterRoomModal()">
                            <i class="fas fa-plus me-2"></i>Đăng ký phòng mới
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ngày đăng ký</th>
                                        <th>Phòng</th>
                                        <th>Tòa nhà</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="registrationTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            loadMyRegistrations();
        }
        
        async function loadMyRegistrations() {
            try {
                const response = await fetch('../../api/registrations.php?my=true');
                const data = await response.json();
                
                const tbody = document.getElementById('registrationTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(reg => {
                        const statusBadge = {
                            'pending': '<span class="badge bg-warning">Chờ duyệt</span>',
                            'approved': '<span class="badge bg-success">Đã duyệt</span>',
                            'rejected': '<span class="badge bg-danger">Đã từ chối</span>',
                            'active': '<span class="badge bg-primary">Đang ở</span>',
                            'completed': '<span class="badge bg-secondary">Đã kết thúc</span>'
                        }[reg.status] || '<span class="badge bg-secondary">' + reg.status + '</span>';
                        
                        return `
                            <tr>
                                <td>${formatDate(reg.registration_date)}</td>
                                <td>${reg.room_number}</td>
                                <td>${reg.building_name}</td>
                                <td>${formatDate(reg.start_date)}</td>
                                <td>${formatDate(reg.end_date)}</td>
                                <td>${statusBadge}</td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có đăng ký nào</td></tr>';
                }
            } catch (error) {
                console.error('Error loading registrations:', error);
                document.getElementById('registrationTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        async function loadUtilitiesSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quản lý Điện Nước</h5>
                                <div>
                                    <button class="btn btn-sm btn-light me-2" onclick="resetUtilityData()">
                                        <i class="fas fa-trash-alt me-2"></i>Reset
                                    </button>
                                    <button class="btn btn-sm btn-light" onclick="simulateUtilityReading()">
                                        <i class="fas fa-robot me-2"></i>Mô phỏng
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="utilityLoading" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Đang tải dữ liệu...</p>
                                </div>
                                <div id="utilityContent" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Thống kê -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-bolt stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="totalElectricity">0 kWh</h3>
                                <p class="mb-0">Điện tháng này</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-tint stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="totalWater">0 m³</h3>
                                <p class="mb-0">Nước tháng này</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="totalAmount">0 đ</h3>
                                <p class="mb-0">Tổng tiền</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-triangle stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="unpaidAmount">0 đ</h3>
                                <p class="mb-0">Chưa thanh toán</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lịch sử điện nước -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử 12 tháng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Chỉ số điện</th>
                                        <th>Tiêu thụ điện</th>
                                        <th>Chỉ số nước</th>
                                        <th>Tiêu thụ nước</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="utilityHistoryTable">
                                    <tr>
                                        <td colspan="8" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            `;
            
            await loadUtilityData();
        }
        
        async function loadUtilityData() {
            try {
                // Lấy thông tin phòng hiện tại
                const regResponse = await fetch('../../api/registrations.php?my=true');
                const regData = await regResponse.json();
                
                if (!regData.success || !regData.data || regData.data.length === 0) {
                    document.getElementById('utilityLoading').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn chưa có phòng. Vui lòng đăng ký phòng trước.
                        </div>
                    `;
                    return;
                }
                
                const activeRegistration = regData.data.find(reg => 
                    reg.status === 'active' || reg.status === 'approved'
                );
                
                if (!activeRegistration || !activeRegistration.room_id) {
                    document.getElementById('utilityLoading').innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Đăng ký của bạn đang chờ duyệt.
                        </div>
                    `;
                    return;
                }
                
                // Lưu room_id để dùng sau
                window.currentRoomId = activeRegistration.room_id;
                
                // Hiển thị thông tin phòng
                document.getElementById('utilityContent').innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-door-open me-2"></i>
                        Phòng: <strong>${activeRegistration.room_number}</strong> - ${activeRegistration.building_name}
                        (${activeRegistration.current_occupancy}/${activeRegistration.capacity} người)
                    </div>
                `;
                document.getElementById('utilityContent').style.display = 'block';
                document.getElementById('utilityLoading').style.display = 'none';
                
                // Lấy lịch sử điện nước
                const utilityResponse = await fetch(`../../api/utilities.php?action=history&room_id=${activeRegistration.room_id}&limit=12`);
                const utilityData = await utilityResponse.json();
                
                if (utilityData.success && utilityData.data && utilityData.data.length > 0) {
                    displayUtilityHistory(utilityData.data);
                    calculateUtilityStats(utilityData.data);
                } else {
                    document.getElementById('utilityHistoryTable').innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Chưa có dữ liệu điện nước</p>
                                    <button class="btn btn-primary btn-sm" onclick="simulateUtilityReading()">
                                        <i class="fas fa-robot me-2"></i>Mô phỏng dữ liệu
                                    </button>
                </div>
                            </td>
                        </tr>
                    `;
                }
                
                // Lấy số tiền chưa thanh toán
                const unpaidResponse = await fetch(`../../api/utilities.php?action=unpaid&room_id=${activeRegistration.room_id}`);
                const unpaidData = await unpaidResponse.json();
                
                if (unpaidData.success) {
                    document.getElementById('unpaidAmount').textContent = formatCurrency(unpaidData.amount);
                }
                
            } catch (error) {
                console.error('Error loading utility data:', error);
                document.getElementById('utilityLoading').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Lỗi khi tải dữ liệu. Vui lòng thử lại sau.
                    </div>
                `;
            }
        }
        
        function displayUtilityHistory(data) {
            const tbody = document.getElementById('utilityHistoryTable');
            
            // Đảo ngược data để tính từ cũ → mới
            const sortedData = [...data].reverse();
            
            tbody.innerHTML = data.map((reading, index) => {
                // Tìm reading trước đó theo thứ tự thời gian (tháng trước)
                let previousReading = null;
                const currentDate = new Date(reading.reading_date);
                
                // Tìm reading có ngày nhỏ hơn gần nhất
                for (let i = 0; i < data.length; i++) {
                    const compareDate = new Date(data[i].reading_date);
                    if (compareDate < currentDate) {
                        if (!previousReading || new Date(previousReading.reading_date) < compareDate) {
                            previousReading = data[i];
                        }
                    }
                }
                
                // Tính tiêu thụ ĐÚNG: hiện tại - trước đó
                const electricityUsage = previousReading ? 
                    Math.max(0, reading.electricity_reading - previousReading.electricity_reading) : 
                    reading.electricity_reading;
                    
                const waterUsage = previousReading ? 
                    Math.max(0, reading.water_reading - previousReading.water_reading) : 
                    reading.water_reading;
                
                const statusBadge = reading.is_paid 
                    ? '<span class="badge bg-success">Đã thanh toán</span>'
                    : '<span class="badge bg-warning">Chưa thanh toán</span>';
                
                return `
                    <tr>
                        <td><strong>${formatDate(reading.reading_date)}</strong></td>
                        <td>${reading.electricity_reading} kWh</td>
                        <td class="text-info fw-bold">${electricityUsage} kWh</td>
                        <td>${reading.water_reading} m³</td>
                        <td class="text-info fw-bold">${waterUsage} m³</td>
                        <td class="text-danger fw-bold">${formatCurrency(reading.total_amount)}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-success me-1" onclick="createInvoiceFromReading(${reading.id}, '${formatDate(reading.reading_date)}', ${reading.total_amount})" title="Tạo hóa đơn">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewUtilityDetail(${reading.id})" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        function calculateUtilityStats(data) {
            if (data.length === 0) return;
            
            // Lấy dữ liệu tháng gần nhất
            const latestReading = data[0];
            
            // Tìm tháng trước đó theo thứ tự thời gian
            let previousReading = null;
            const latestDate = new Date(latestReading.reading_date);
            
            for (let i = 1; i < data.length; i++) {
                const compareDate = new Date(data[i].reading_date);
                if (compareDate < latestDate) {
                    if (!previousReading || new Date(previousReading.reading_date) < compareDate) {
                        previousReading = data[i];
                    }
                }
            }
            
            const electricityUsage = previousReading 
                ? Math.max(0, latestReading.electricity_reading - previousReading.electricity_reading)
                : latestReading.electricity_reading;
            
            const waterUsage = previousReading
                ? Math.max(0, latestReading.water_reading - previousReading.water_reading)
                : latestReading.water_reading;
            
            document.getElementById('totalElectricity').textContent = `${electricityUsage} kWh`;
            document.getElementById('totalWater').textContent = `${waterUsage} m³`;
            document.getElementById('totalAmount').textContent = formatCurrency(latestReading.total_amount);
        }
        
        async function simulateUtilityReading() {
            if (!window.currentRoomId) {
                alert('Không tìm thấy thông tin phòng!');
                return;
            }
            
            // Hiển thị modal chọn số tháng
            const modal = `
                <div class="modal fade" id="simulateModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-robot me-2"></i>Mô phỏng chỉ số điện nước</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Hệ thống sẽ tự động tạo dữ liệu mô phỏng dựa trên số người ở trong phòng với mức tiêu thụ gần thực tế.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Chọn số tháng muốn mô phỏng:</label>
                                    <select class="form-select" id="monthsToSimulate">
                                        <option value="1">1 tháng (tháng hiện tại)</option>
                                        <option value="3">3 tháng gần nhất</option>
                                        <option value="6" selected>6 tháng gần nhất</option>
                                        <option value="12">12 tháng gần nhất</option>
                                    </select>
                                    <small class="text-muted">* Các tháng đã có dữ liệu sẽ được bỏ qua</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giá điện (VNĐ/kWh):</label>
                                    <input type="number" class="form-control" id="electricityRate" value="3500">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giá nước (VNĐ/m³):</label>
                                    <input type="number" class="form-control" id="waterRate" value="15000">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-primary" onclick="executeSimulation()">
                                    <i class="fas fa-play me-2"></i>Bắt đầu mô phỏng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('simulateModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modal);
            const modalElement = new bootstrap.Modal(document.getElementById('simulateModal'));
            modalElement.show();
        }
        
        async function executeSimulation() {
            const months = parseInt(document.getElementById('monthsToSimulate').value);
            const electricityRate = parseFloat(document.getElementById('electricityRate').value);
            const waterRate = parseFloat(document.getElementById('waterRate').value);
            
            // Close modal
            const modalElement = bootstrap.Modal.getInstance(document.getElementById('simulateModal'));
            modalElement.hide();
            
            // Show loading
            const loadingAlert = document.createElement('div');
            loadingAlert.className = 'alert alert-info position-fixed top-0 start-50 translate-middle-x mt-3';
            loadingAlert.style.zIndex = '9999';
            loadingAlert.innerHTML = `
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Đang mô phỏng ${months} tháng...
            `;
            document.body.appendChild(loadingAlert);
            
            try {
                const response = await fetch('../../api/utilities.php?action=simulate-multiple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_id: window.currentRoomId,
                        months: months,
                        electricity_rate: electricityRate,
                        water_rate: waterRate
                    })
                });
                
                const data = await response.json();
                
                loadingAlert.remove();
                
                if (data.success) {
                    const successCount = data.data.filter(r => r.status === 'success').length;
                    const skippedCount = data.data.filter(r => r.status === 'skipped').length;
                    
                    let message = `✅ Mô phỏng thành công!\n\n`;
                    message += `📊 Dữ liệu điện nước:\n`;
                    message += `- Đã tạo: ${successCount} tháng\n`;
                    if (skippedCount > 0) {
                        message += `- Đã bỏ qua: ${skippedCount} tháng (đã có dữ liệu)\n`;
                    }
                    
                    // Hiển thị chi tiết
                    const details = data.data
                        .filter(r => r.status === 'success')
                        .slice(0, 3)
                        .map(r => `\n📅 ${r.date}: ${formatCurrency(r.data.total_amount)}`)
                        .join('');
                    
                    if (details) {
                        message += `\nChi tiết:${details}`;
                        if (successCount > 3) {
                            message += `\n... và ${successCount - 3} tháng khác`;
                        }
                    }
                    
                    if (successCount > 0) {
                        message += `\n\n💡 Bước tiếp theo:\n`;
                        message += `Click button "💰" ở cột Thao tác để tạo hóa đơn cho từng tháng!`;
                    }
                    
                    alert(message);
                    loadUtilityData(); // Reload dữ liệu
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể mô phỏng'));
                }
            } catch (error) {
                loadingAlert.remove();
                console.error('Error simulating utility:', error);
                alert('Lỗi khi mô phỏng dữ liệu: ' + error.message);
            }
        }
        
        async function createInvoiceFromReading(readingId, readingDate, amount) {
            if (!confirm(`Tạo hóa đơn cho tháng ${readingDate}?\n\nSố tiền: ${formatCurrency(amount)}\n\nHóa đơn sẽ được chia đều cho các sinh viên trong phòng.`)) {
                return;
            }
            
            try {
                console.log('Creating invoice for reading:', readingId);
                
                const response = await fetch('../../api/utilities.php?action=create-invoice', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        reading_id: readingId
                    })
                });
                
                console.log('Response status:', response.status);
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    alert('✅ ' + data.message + '\n\nVào "Hóa đơn & Thanh toán" để xem và thanh toán!');
                    loadUtilityData(); // Reload utility data
                    
                    // Chỉ reload payment nếu payment section đã được load
                    const paymentList = document.getElementById('paymentList');
                    if (paymentList) {
                        loadPaymentData();
                    }
                } else {
                    alert('❌ Lỗi: ' + (data.error || 'Không thể tạo hóa đơn'));
                }
            } catch (error) {
                console.error('Error creating invoice:', error);
                alert('❌ Lỗi khi tạo hóa đơn: ' + error.message);
            }
        }
        
        function viewUtilityDetail(readingId) {
            alert('Chi tiết chỉ số #' + readingId + '\n\nChức năng đang phát triển...');
        }
        
        async function resetUtilityData() {
            if (!window.currentRoomId) {
                alert('Không tìm thấy thông tin phòng!');
                return;
            }
            
            if (!confirm('⚠️ Cảnh báo!\n\nBạn có chắc muốn XÓA TẤT CẢ dữ liệu điện nước của phòng này?\n\nHành động này không thể hoàn tác!')) {
                return;
            }
            
            try {
                const response = await fetch('../../api/utilities.php?action=clear-simulated', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_id: window.currentRoomId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Đã xóa tất cả dữ liệu điện nước!\n\nBạn có thể mô phỏng lại từ đầu.');
                    loadUtilityData(); // Reload
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể xóa dữ liệu'));
                }
            } catch (error) {
                console.error('Error resetting utility data:', error);
                alert('Lỗi khi xóa dữ liệu: ' + error.message);
            }
        }

        async function loadPaymentsSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <!-- Thống kê nhanh -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-invoice-dollar stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="totalInvoices">0</h3>
                                <p class="mb-0">Tổng số hóa đơn</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-circle stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="unpaidInvoices">0 đ</h3>
                                <p class="mb-0">Chưa thanh toán</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle stat-icon"></i>
                                <h3 class="mt-2 mb-0" id="paidInvoices">0 đ</h3>
                                <p class="mb-0">Đã thanh toán</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Danh sách hóa đơn -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách hóa đơn</h5>
                        <div>
                            <select class="form-select form-select-sm d-inline-block w-auto" id="filterStatus" onchange="loadPaymentData()">
                                <option value="">Tất cả</option>
                                <option value="pending">Chưa thanh toán</option>
                                <option value="completed">Đã thanh toán</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã HĐ</th>
                                        <th>Loại</th>
                                        <th>Ngày tạo</th>
                                        <th>Số tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ghi chú</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            await loadPaymentData();
        }
        
        async function loadPaymentData() {
            try {
                const filterStatus = document.getElementById('filterStatus')?.value || '';
                let url = '../../api/payments.php?my=true';
                if (filterStatus) {
                    url += `&status=${filterStatus}`;
                }
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && data.data) {
                    displayPayments(data.data);
                    calculatePaymentStats(data.data);
                } else {
                    document.getElementById('paymentsTableBody').innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có hóa đơn nào</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading payments:', error);
                document.getElementById('paymentsTableBody').innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">Lỗi tải dữ liệu</td>
                    </tr>
                `;
            }
        }
        
        function displayPayments(payments) {
            const tbody = document.getElementById('paymentsTableBody');
            
            if (payments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có hóa đơn nào</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = payments.map(payment => {
                const typeLabels = {
                    'utility': '<span class="badge bg-info">Điện nước</span>',
                    'room_fee': '<span class="badge bg-primary">Tiền phòng</span>',
                    'penalty': '<span class="badge bg-warning">Phạt</span>',
                    'deposit': '<span class="badge bg-secondary">Đặt cọc</span>'
                };
                
                const statusBadges = {
                    'pending': '<span class="badge bg-warning">Chưa thanh toán</span>',
                    'completed': '<span class="badge bg-success">Đã thanh toán</span>',
                    'failed': '<span class="badge bg-danger">Thất bại</span>'
                };
                
                const paymentButton = payment.status === 'pending' 
                    ? `<button class="btn btn-sm btn-success" onclick="showPaymentModal(${payment.id}, ${payment.amount})">
                        <i class="fas fa-credit-card me-1"></i>Thanh toán
                       </button>`
                    : `<button class="btn btn-sm btn-outline-info" onclick="viewPaymentDetail(${payment.id})">
                        <i class="fas fa-eye"></i>
                       </button>`;
                
                return `
                    <tr>
                        <td><strong>#${payment.id}</strong></td>
                        <td>${typeLabels[payment.payment_type] || payment.payment_type}</td>
                        <td>${formatDate(payment.payment_date)}</td>
                        <td class="text-danger fw-bold">${formatCurrency(payment.amount)}</td>
                        <td>${statusBadges[payment.status] || payment.status}</td>
                        <td>${payment.notes || '-'}</td>
                        <td>${paymentButton}</td>
                    </tr>
                `;
            }).join('');
        }
        
        function calculatePaymentStats(payments) {
            const total = payments.length;
            const unpaid = payments.filter(p => p.status === 'pending').reduce((sum, p) => sum + parseFloat(p.amount), 0);
            const paid = payments.filter(p => p.status === 'completed').reduce((sum, p) => sum + parseFloat(p.amount), 0);
            
            document.getElementById('totalInvoices').textContent = total;
            document.getElementById('unpaidInvoices').textContent = formatCurrency(unpaid);
            document.getElementById('paidInvoices').textContent = formatCurrency(paid);
        }
        
        function showPaymentModal(paymentId, amount) {
            const modal = `
                <div class="modal fade" id="paymentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Thanh toán hóa đơn</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                        <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Số tiền cần thanh toán: <strong class="fs-5 text-danger">${formatCurrency(amount)}</strong>
                        </div>
                                <div class="mb-3">
                                    <label class="form-label">Phương thức thanh toán:</label>
                                    <select class="form-select" id="paymentMethod">
                                        <option value="cash">Tiền mặt</option>
                                        <option value="bank_transfer">Chuyển khoản</option>
                                        <option value="card">Thẻ</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mã giao dịch (nếu có):</label>
                                    <input type="text" class="form-control" id="referenceNumber" placeholder="VD: 123456789">
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small>Đây là chức năng demo. Trong thực tế sẽ tích hợp cổng thanh toán.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-success" onclick="executePayment(${paymentId})">
                                    <i class="fas fa-check me-2"></i>Xác nhận thanh toán
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            const existingModal = document.getElementById('paymentModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modal);
            const modalElement = new bootstrap.Modal(document.getElementById('paymentModal'));
            modalElement.show();
        }
        
        async function executePayment(paymentId) {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const referenceNumber = document.getElementById('referenceNumber').value;
            
            try {
                const response = await fetch('../../api/payments.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_id: paymentId,
                        payment_method: paymentMethod,
                        reference_number: referenceNumber,
                        status: 'completed'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Thanh toán thành công!');
                    const modalElement = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    modalElement.hide();
                    loadPaymentData(); // Reload payments
                    
                    // Reload utilities nếu utility section đã load
                    const utilityList = document.getElementById('utilityList');
                    if (utilityList) {
                        loadUtilityData();
                    }
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể thanh toán'));
                }
            } catch (error) {
                console.error('Error executing payment:', error);
                alert('Lỗi khi thanh toán: ' + error.message);
            }
        }
        
        function viewPaymentDetail(paymentId) {
            alert('Chi tiết hóa đơn #' + paymentId + '\n\nChức năng đang phát triển...');
        }

        async function loadMaintenanceSection() {
            // Ensure room ID is loaded
            if (!window.currentRoomId) {
                await loadRoomInfo();
            }
            
            document.getElementById('dynamicContent').innerHTML = `
                <!-- Thiết bị trong phòng -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>Thiết bị trong phòng</h5>
                    </div>
                    <div class="card-body">
                        <div id="equipmentList">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yêu cầu sửa chữa -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Yêu cầu sửa chữa của tôi</h5>
                        <button class="btn btn-primary" onclick="showMaintenanceModal()">
                            <i class="fas fa-plus me-2"></i>Tạo yêu cầu mới
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Loại</th>
                                        <th>Thiết bị</th>
                                        <th>Mô tả</th>
                                        <th>Ưu tiên</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody id="maintenanceTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            await loadEquipmentList();
            await loadMaintenanceRequests();
        }
        
        async function loadEquipmentList() {
            try {
                if (!window.currentRoomId) {
                    document.getElementById('equipmentList').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn chưa có phòng. Vui lòng đăng ký phòng trước.
                        </div>
                    `;
                    return;
                }
                
                const response = await fetch(`../../api/equipment.php?room_id=${window.currentRoomId}`);
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    const equipmentHTML = data.data.map(equipment => {
                        const statusBadges = {
                            'working': '<span class="badge bg-success">Hoạt động tốt</span>',
                            'broken': '<span class="badge bg-danger">Hỏng</span>',
                            'maintenance': '<span class="badge bg-warning">Đang sửa</span>',
                            'replaced': '<span class="badge bg-secondary">Đã thay thế</span>'
                        };
                        
                        return `
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 ${equipment.status === 'broken' ? 'border-danger' : ''}">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-${getEquipmentIcon(equipment.equipment_type)} me-2"></i>
                                            ${equipment.equipment_name}
                                        </h6>
                                        <p class="card-text small text-muted mb-2">${equipment.equipment_type}</p>
                                        <div class="mb-2">${statusBadges[equipment.status] || equipment.status}</div>
                                        ${equipment.brand ? `<small class="text-muted">Hãng: ${equipment.brand}</small><br>` : ''}
                                        ${equipment.model ? `<small class="text-muted">Model: ${equipment.model}</small>` : ''}
                                        ${equipment.status === 'working' ? `
                                            <button class="btn btn-sm btn-outline-danger mt-2 w-100" onclick="reportBrokenEquipment(${equipment.id}, '${equipment.equipment_name}')">
                                                <i class="fas fa-exclamation-circle me-1"></i>Báo hỏng
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    document.getElementById('equipmentList').innerHTML = `<div class="row">${equipmentHTML}</div>`;
                } else {
                    document.getElementById('equipmentList').innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Phòng của bạn chưa có thiết bị nào được đăng ký.
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading equipment:', error);
                document.getElementById('equipmentList').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>Lỗi tải danh sách thiết bị
                    </div>
                `;
            }
        }
        
        function getEquipmentIcon(type) {
            const icons = {
                'Giường': 'bed',
                'Bàn': 'table',
                'Tủ': 'archive',
                'Quạt': 'fan',
                'Điều hòa': 'snowflake',
                'default': 'box'
            };
            return icons[type] || icons['default'];
        }
        
        async function loadMaintenanceRequests() {
            try {
                const response = await fetch('../../api/maintenance.php?action=my');
                const data = await response.json();
                
                if (data.success && data.data) {
                    displayMaintenanceRequests(data.data);
                } else {
                    document.getElementById('maintenanceTableBody').innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có yêu cầu sửa chữa nào</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading maintenance requests:', error);
                document.getElementById('maintenanceTableBody').innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">Lỗi tải dữ liệu</td>
                    </tr>
                `;
            }
        }
        
        function displayMaintenanceRequests(requests) {
            const tbody = document.getElementById('maintenanceTableBody');
            
            if (requests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có yêu cầu sửa chữa nào</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            const typeLabels = {
                'equipment': '<span class="badge bg-info">Thiết bị</span>',
                'room': '<span class="badge bg-primary">Phòng</span>',
                'utility': '<span class="badge bg-warning">Điện nước</span>'
            };
            
            const priorityBadges = {
                'low': '<span class="badge bg-secondary">Thấp</span>',
                'medium': '<span class="badge bg-info">Trung bình</span>',
                'high': '<span class="badge bg-warning">Cao</span>',
                'urgent': '<span class="badge bg-danger">Khẩn cấp</span>'
            };
            
            const statusBadges = {
                'pending': '<span class="badge bg-warning">Chờ xử lý</span>',
                'in_progress': '<span class="badge bg-primary">Đang xử lý</span>',
                'completed': '<span class="badge bg-success">Hoàn thành</span>',
                'cancelled': '<span class="badge bg-secondary">Đã hủy</span>'
            };
            
            tbody.innerHTML = requests.map(req => `
                <tr>
                    <td><strong>#${req.id}</strong></td>
                    <td>${typeLabels[req.request_type] || req.request_type}</td>
                    <td>${req.equipment_name || '-'}</td>
                    <td><small>${req.description.substring(0, 50)}${req.description.length > 50 ? '...' : ''}</small></td>
                    <td>${priorityBadges[req.priority] || req.priority}</td>
                    <td>${statusBadges[req.status] || req.status}</td>
                    <td><small>${formatDate(req.created_at)}</small></td>
                </tr>
            `).join('');
        }
        
        function reportBrokenEquipment(equipmentId, equipmentName) {
            showMaintenanceModal(equipmentId, equipmentName);
        }
        
        async function showMaintenanceModal(equipmentId = null, equipmentName = null) {
            if (!window.currentRoomId) {
                alert('Bạn chưa có phòng. Vui lòng đăng ký phòng trước.');
                return;
            }
            
            // Load danh sách thiết bị trong phòng
            let equipmentOptions = '';
            try {
                const response = await fetch(`../../api/equipment.php?room_id=${window.currentRoomId}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    equipmentOptions = data.data.map(eq => 
                        `<option value="${eq.id}" ${equipmentId == eq.id ? 'selected' : ''}>${eq.equipment_name} - ${eq.equipment_type}</option>`
                    ).join('');
                }
            } catch (error) {
                console.error('Error loading equipment:', error);
            }
            
            const modal = `
                <div class="modal fade" id="maintenanceModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-tools me-2"></i>
                                    ${equipmentId ? 'Báo hỏng thiết bị' : 'Tạo yêu cầu sửa chữa'}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="maintenanceForm">
                                    <div class="mb-3">
                                        <label class="form-label">Loại yêu cầu <span class="text-danger">*</span></label>
                                        <select class="form-select" id="requestType" required onchange="toggleEquipmentField()">
                                            <option value="">Chọn loại...</option>
                                            <option value="equipment" ${equipmentId ? 'selected' : ''}>Thiết bị hỏng hóc</option>
                                            <option value="room">Sửa chữa phòng (tường, cửa, sàn...)</option>
                                            <option value="utility">Điện nước (mất điện, rò nước...)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="equipmentFieldWrapper" style="display: ${equipmentId ? 'block' : 'none'};">
                                        <label class="form-label">Thiết bị</label>
                                        <select class="form-select" id="equipmentId">
                                            <option value="">Không chọn thiết bị</option>
                                            ${equipmentOptions}
                                        </select>
                                        <small class="text-muted">Chọn thiết bị bị hỏng (nếu có)</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Mức độ ưu tiên <span class="text-danger">*</span></label>
                                        <select class="form-select" id="priority" required>
                                            <option value="medium" selected>Trung bình</option>
                                            <option value="low">Thấp</option>
                                            <option value="high">Cao</option>
                                            <option value="urgent">Khẩn cấp</option>
                                        </select>
                                        <small class="text-muted">
                                            <strong>Khẩn cấp:</strong> Ảnh hưởng nghiêm trọng (mất điện, rò nước lớn...)<br>
                                            <strong>Cao:</strong> Cần sửa sớm (thiết bị quan trọng hỏng)<br>
                                            <strong>Trung bình:</strong> Có thể chờ vài ngày<br>
                                            <strong>Thấp:</strong> Không gấp
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" rows="5" required 
                                            placeholder="Mô tả rõ ràng vấn đề:&#10;- Thiết bị/vị trí nào bị hỏng?&#10;- Triệu chứng gì?&#10;- Khi nào bắt đầu?&#10;- Ảnh hưởng như thế nào?"></textarea>
                                        <small class="text-muted">Mô tả càng chi tiết càng giúp xử lý nhanh hơn</small>
                                    </div>
                                    
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Lưu ý:</strong> Yêu cầu của bạn sẽ được xử lý trong vòng 24-48 giờ. 
                                        Trường hợp khẩn cấp sẽ được ưu tiên xử lý ngay.
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-primary" onclick="submitMaintenanceRequest()">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('maintenanceModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modal);
            
            const modalElement = new bootstrap.Modal(document.getElementById('maintenanceModal'));
            modalElement.show();
        }
        
        function toggleEquipmentField() {
            const requestType = document.getElementById('requestType').value;
            const equipmentField = document.getElementById('equipmentFieldWrapper');
            
            if (requestType === 'equipment') {
                equipmentField.style.display = 'block';
            } else {
                equipmentField.style.display = 'none';
                document.getElementById('equipmentId').value = '';
            }
        }
        
        async function submitMaintenanceRequest() {
            const requestType = document.getElementById('requestType').value;
            const equipmentId = document.getElementById('equipmentId').value;
            const priority = document.getElementById('priority').value;
            const description = document.getElementById('description').value;
            
            if (!requestType || !priority || !description) {
                alert('Vui lòng điền đầy đủ các trường bắt buộc (*)');
                return;
            }
            
            if (requestType === 'equipment' && !equipmentId) {
                if (!confirm('Bạn chưa chọn thiết bị cụ thể. Tiếp tục?')) {
                    return;
                }
            }
            
            try {
                const requestData = {
                    room_id: window.currentRoomId,
                    request_type: requestType,
                    description: description,
                    priority: priority
                };
                
                if (equipmentId) {
                    requestData.equipment_id = parseInt(equipmentId);
                }
                
                const response = await fetch('../../api/maintenance.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Đã gửi yêu cầu sửa chữa thành công!\n\nMã yêu cầu: #' + data.request_id + '\n\nChúng tôi sẽ xử lý trong thời gian sớm nhất.');
                    
                    const modalElement = bootstrap.Modal.getInstance(document.getElementById('maintenanceModal'));
                    modalElement.hide();
                    
                    // Reload maintenance section
                    loadMaintenanceRequests();
                    loadEquipmentList();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể tạo yêu cầu'));
                }
            } catch (error) {
                console.error('Error submitting maintenance request:', error);
                alert('Lỗi khi gửi yêu cầu: ' + error.message);
            }
        }

        function loadFeedbackSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Gửi phản hồi & Khiếu nại</h5>
                    </div>
                    <div class="card-body">
                        <form id="feedbackForm" onsubmit="submitFeedback(event)">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="feedbackSubject" 
                                    maxlength="200" minlength="5" required 
                                    placeholder="VD: Điều hòa hỏng, Nhà vệ sinh bẩn, Góp ý về dịch vụ...">
                                <small class="text-muted">
                                    <span id="subjectCounter">0</span>/200 ký tự
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Loại phản hồi <span class="text-danger">*</span></label>
                                <select class="form-control" id="feedbackCategory" required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="complaint">Khiếu nại</option>
                                    <option value="suggestion">Đề xuất</option>
                                    <option value="compliment">Khen ngợi</option>
                                    <option value="other">Khác</option>
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Khiếu nại: vấn đề cần giải quyết | Đề xuất: ý kiến cải thiện | Khen ngợi: đánh giá tích cực
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="feedbackMessage" rows="6" 
                                    maxlength="2000" minlength="10" required
                                    placeholder="Mô tả chi tiết vấn đề hoặc ý kiến của bạn...&#10;&#10;VD:&#10;- Vấn đề gì xảy ra?&#10;- Khi nào bắt đầu?&#10;- Mức độ ảnh hưởng?&#10;- Đề xuất giải pháp (nếu có)"></textarea>
                                <small class="text-muted">
                                    Mô tả chi tiết giúp xử lý tốt hơn. 
                                    <span id="messageCounter">0</span>/2000 ký tự
                                </small>
                            </div>
                            
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Thời gian xử lý:</strong> Phản hồi của bạn sẽ được xem xét trong vòng 24-48 giờ làm việc.
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Gửi phản hồi
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="loadMyFeedbacks()">
                                <i class="fas fa-history me-2"></i>Xem lịch sử phản hồi
                            </button>
                        </form>
                    </div>
                </div>
                
                <div id="feedbackHistory" class="mt-4"></div>
            `;
            
            // Thêm event listener cho character counter
            setTimeout(() => {
                const subjectInput = document.getElementById('feedbackSubject');
                const messageInput = document.getElementById('feedbackMessage');
                const subjectCounter = document.getElementById('subjectCounter');
                const messageCounter = document.getElementById('messageCounter');
                
                if (subjectInput && subjectCounter) {
                    subjectInput.addEventListener('input', () => {
                        subjectCounter.textContent = subjectInput.value.length;
                    });
                }
                
                if (messageInput && messageCounter) {
                    messageInput.addEventListener('input', () => {
                        messageCounter.textContent = messageInput.value.length;
                    });
                }
            }, 100);
        }
        
        async function submitFeedback(event) {
            event.preventDefault();
            
            const subject = document.getElementById('feedbackSubject').value.trim();
            const category = document.getElementById('feedbackCategory').value;
            const message = document.getElementById('feedbackMessage').value.trim();
            
            // Validation
            if (!subject || !category || !message) {
                alert('⚠️ Vui lòng điền đầy đủ các trường bắt buộc (*)');
                return;
            }
            
            if (subject.length < 5 || subject.length > 200) {
                alert('⚠️ Tiêu đề phải từ 5-200 ký tự');
                return;
            }
            
            if (message.length < 10 || message.length > 2000) {
                alert('⚠️ Nội dung phản hồi phải từ 10-2000 ký tự');
                return;
            }
            
            // Kiểm tra category hợp lệ
            const validCategories = ['complaint', 'suggestion', 'compliment', 'other'];
            if (!validCategories.includes(category)) {
                alert('⚠️ Loại phản hồi không hợp lệ');
                return;
            }
            
            try {
                const response = await fetch('../../api/feedback.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        subject: subject,
                        message: message,
                        category: category
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Đã gửi phản hồi thành công!\n\nMã phản hồi: #' + data.feedback_id + '\n\nChúng tôi sẽ xem xét và phản hồi lại trong thời gian sớm nhất.');
                    
                    // Reset form
                    document.getElementById('feedbackForm').reset();
                    
                    // Load lịch sử
                    loadMyFeedbacks();
                } else {
                    alert('❌ Lỗi: ' + (data.error || 'Không thể gửi phản hồi'));
                }
            } catch (error) {
                console.error('Error submitting feedback:', error);
                alert('❌ Lỗi khi gửi phản hồi: ' + error.message);
            }
        }
        
        async function loadMyFeedbacks() {
            try {
                const response = await fetch('../../api/feedback.php?action=student&limit=10');
                const data = await response.json();
                
                if (data.success && data.data) {
                    const feedbacks = data.data;
                    
                    let html = `
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử phản hồi của bạn</h5>
                            </div>
                            <div class="card-body">
                    `;
                    
                    if (feedbacks.length === 0) {
                        html += `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Bạn chưa có phản hồi nào. Hãy gửi phản hồi đầu tiên!
                            </div>
                        `;
                    } else {
                        html += '<div class="list-group">';
                        feedbacks.forEach(feedback => {
                            const categoryLabels = {
                                'complaint': '<span class="badge bg-danger">Khiếu nại</span>',
                                'suggestion': '<span class="badge bg-info">Đề xuất</span>',
                                'compliment': '<span class="badge bg-success">Khen ngợi</span>',
                                'other': '<span class="badge bg-secondary">Khác</span>'
                            };
                            
                            const statusLabels = {
                                'new': '<span class="badge bg-warning">Mới</span>',
                                'in_progress': '<span class="badge bg-primary">Đang xử lý</span>',
                                'resolved': '<span class="badge bg-success">Đã giải quyết</span>',
                                'closed': '<span class="badge bg-secondary">Đã đóng</span>'
                            };
                            
                            const hasResponse = feedback.response && feedback.response.trim() !== '';
                            
                            html += `
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">
                                            <i class="fas fa-comment-dots me-2"></i>${feedback.subject}
                                        </h6>
                                        <div>
                                            ${categoryLabels[feedback.category] || ''}
                                            ${statusLabels[feedback.status] || ''}
                                        </div>
                                    </div>
                                    <p class="mb-2 text-muted">${feedback.message}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>${new Date(feedback.created_at).toLocaleString('vi-VN')}
                                    </small>
                                    
                                    ${hasResponse ? `
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <strong class="text-primary"><i class="fas fa-reply me-2"></i>Phản hồi từ quản lý:</strong>
                                            <p class="mb-1 mt-2">${feedback.response}</p>
                                            ${feedback.responded_at ? `<small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>${new Date(feedback.responded_at).toLocaleString('vi-VN')}
                                            </small>` : ''}
                                        </div>
                                    ` : ''}
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    html += `
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('feedbackHistory').innerHTML = html;
                }
            } catch (error) {
                console.error('Error loading feedbacks:', error);
            }
        }

        function showRegisterRoomModal() {
            const modal = `
                <div class="modal fade" id="registerRoomModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-clipboard-list me-2"></i>Đăng ký phòng mới</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="registerRoomForm">
                                    <div class="mb-3">
                                        <label class="form-label">Tòa nhà</label>
                                        <select class="form-select" id="buildingSelect" required>
                                            <option value="">Chọn tòa nhà...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phòng</label>
                                        <select class="form-select" id="roomSelect" required>
                                            <option value="">Chọn tòa nhà trước</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ngày bắt đầu</label>
                                            <input type="date" class="form-control" id="startDate" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ngày kết thúc</label>
                                            <input type="date" class="form-control" id="endDate" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ghi chú (tùy chọn)</label>
                                        <textarea class="form-control" id="notes" rows="3" placeholder="Nhập ghi chú nếu có..."></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-primary" onclick="submitRegistration()">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi đăng ký
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('registerRoomModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modal);
            
            const modalElement = new bootstrap.Modal(document.getElementById('registerRoomModal'));
            loadBuildingsForRegistration();
            
            // Listen to building change
            document.getElementById('buildingSelect').addEventListener('change', function() {
                loadAvailableRooms(this.value);
            });
            
            modalElement.show();
        }
        
        async function loadBuildingsForRegistration() {
            try {
                const response = await fetch('../../api/buildings.php');
                const data = await response.json();
                
                const select = document.getElementById('buildingSelect');
                if (data.success && data.data) {
                    select.innerHTML = '<option value="">Chọn tòa nhà...</option>' + 
                        data.data.map(building => 
                            `<option value="${building.id}">${building.name}</option>`
                        ).join('');
                }
            } catch (error) {
                console.error('Error loading buildings:', error);
            }
        }
        
        async function loadAvailableRooms(buildingId) {
            const select = document.getElementById('roomSelect');
            
            if (!buildingId) {
                select.innerHTML = '<option value="">Chọn tòa nhà trước</option>';
                return;
            }
            
            try {
                const response = await fetch(`../../api/rooms.php?path=available&building_id=${buildingId}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    select.innerHTML = '<option value="">Chọn phòng...</option>' + 
                        data.data.map(room => 
                            `<option value="${room.id}">${room.room_number} - Trống ${room.capacity - room.current_occupancy}/${room.capacity} người</option>`
                        ).join('');
                } else {
                    select.innerHTML = '<option value="">Không có phòng trống</option>';
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                select.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            }
        }
        
        async function submitRegistration() {
            const roomId = document.getElementById('roomSelect').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const notes = document.getElementById('notes').value;
            
            if (!roomId || !startDate || !endDate) {
                alert('Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            if (new Date(startDate) >= new Date(endDate)) {
                alert('Ngày kết thúc phải sau ngày bắt đầu');
                return;
            }
            
            try {
                // Get student ID from session or user data
                const userResponse = await fetch('../../api/auth.php?action=check');
                const userData = await userResponse.json();
                
                if (!userData.authenticated) {
                    alert('Bạn chưa đăng nhập');
                    return;
                }
                
                // We need to get the student ID - this is a simplified version
                // In reality, you'd need to get it from the current user data
                const response = await fetch('../../api/students.php?path=get_current_student');
                const studentData = await response.json();
                
                if (!studentData.success) {
                    alert('Không tìm thấy thông tin sinh viên');
                    return;
                }
                
                const registrationResponse = await fetch('../../api/registrations.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_id: studentData.data.id,
                        room_id: roomId,
                        start_date: startDate,
                        end_date: endDate,
                        notes: notes
                    })
                });
                
                const result = await registrationResponse.json();
                
                if (result.success) {
                    alert('Đăng ký phòng thành công! Vui lòng chờ cán bộ duyệt.');
                    bootstrap.Modal.getInstance(document.getElementById('registerRoomModal')).hide();
                    loadMyRegistrations();
                } else {
                    alert('Lỗi: ' + (result.error || 'Đăng ký thất bại'));
                }
            } catch (error) {
                console.error('Error submitting registration:', error);
                alert('Có lỗi xảy ra khi gửi đăng ký');
            }
        }

        // Load Profile Section
        async function loadProfileSection() {
            try {
                const response = await fetch('../../api/profile.php?action=get');
                const data = await response.json();
                
                if (!data.success) {
                    dynamicContent.innerHTML = '<div class="alert alert-danger">Không thể tải thông tin</div>';
                    return;
                }
                
                const student = data.student || {};
                
                dynamicContent.innerHTML = `
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Chỉnh sửa thông tin cá nhân</h5>
                        </div>
                        <div class="card-body">
                            <div id="profileAlert"></div>
                            
                            <form id="profileForm">
                                <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Thông tin tài khoản</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tên đăng nhập</label>
                                        <input type="text" class="form-control" value="${data.user.username}" disabled>
                                        <small class="text-muted">Tên đăng nhập không thể thay đổi</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" value="${data.user.email}" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Họ và tên *</label>
                                        <input type="text" class="form-control" id="fullName" value="${data.user.full_name}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Số điện thoại *</label>
                                        <input type="tel" class="form-control" id="phone" value="${data.user.phone || ''}" required>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Thông tin sinh viên</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mã số sinh viên</label>
                                        <input type="text" class="form-control" value="${student.student_code || ''}" disabled>
                                        <small class="text-muted">Mã SV không thể thay đổi</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Giới tính</label>
                                        <input type="text" class="form-control" value="${student.gender === 'male' ? 'Nam' : 'Nữ'}" disabled>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Khoa *</label>
                                        <input type="text" class="form-control" id="faculty" value="${student.faculty || ''}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lớp</label>
                                        <input type="text" class="form-control" id="className" value="${student.class_name || ''}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ngày sinh</label>
                                        <input type="date" class="form-control" id="dateOfBirth" value="${student.date_of_birth || ''}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quê quán</label>
                                        <input type="text" class="form-control" id="hometown" value="${student.hometown || ''}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Người liên hệ khẩn cấp</label>
                                        <input type="text" class="form-control" id="emergencyContact" value="${student.emergency_contact || ''}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SĐT liên hệ khẩn cấp</label>
                                        <input type="tel" class="form-control" id="emergencyPhone" value="${student.emergency_phone || ''}">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Số CMND/CCCD</label>
                                    <input type="text" class="form-control" id="idCard" value="${student.id_card || ''}">
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
                
                // Handle form submission
                document.getElementById('profileForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const profileData = {
                        email: document.getElementById('email').value,
                        full_name: document.getElementById('fullName').value,
                        phone: document.getElementById('phone').value,
                        faculty: document.getElementById('faculty').value,
                        class_name: document.getElementById('className').value,
                        date_of_birth: document.getElementById('dateOfBirth').value,
                        hometown: document.getElementById('hometown').value,
                        emergency_contact: document.getElementById('emergencyContact').value,
                        emergency_phone: document.getElementById('emergencyPhone').value,
                        id_card: document.getElementById('idCard').value
                    };
                    
                    try {
                        const updateResponse = await fetch('../../api/profile.php?action=update', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(profileData)
                        });
                        
                        const result = await updateResponse.json();
                        
                        const alertDiv = document.getElementById('profileAlert');
                        if (result.success) {
                            alertDiv.innerHTML = `
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="fas fa-check-circle me-2"></i>${result.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                        } else {
                            alertDiv.innerHTML = `
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fas fa-exclamation-circle me-2"></i>${result.error}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                        }
                        
                        // Scroll to alert
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        
                    } catch (error) {
                        console.error('Error updating profile:', error);
                        document.getElementById('profileAlert').innerHTML = `
                            <div class="alert alert-danger">Có lỗi xảy ra khi cập nhật thông tin</div>
                        `;
                    }
                });
                
            } catch (error) {
                console.error('Error loading profile:', error);
                dynamicContent.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải thông tin</div>';
            }
        }

        // Load Change Password Section
        function loadChangePasswordSection() {
            dynamicContent.innerHTML = `
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <div id="passwordAlert"></div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Mật khẩu mới phải có ít nhất 6 ký tự</li>
                                <li>Nên sử dụng kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                                <li>Không chia sẻ mật khẩu với người khác</li>
                            </ul>
                        </div>
                        
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="oldPassword" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('oldPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="newPassword" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Xác nhận mật khẩu mới *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fas fa-undo me-2"></i>Đặt lại
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            // Handle form submission
            document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const oldPassword = document.getElementById('oldPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                // Validate
                if (newPassword !== confirmPassword) {
                    document.getElementById('passwordAlert').innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>Mật khẩu xác nhận không khớp!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    return;
                }
                
                if (newPassword.length < 6) {
                    document.getElementById('passwordAlert').innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>Mật khẩu mới phải có ít nhất 6 ký tự!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    return;
                }
                
                try {
                    const response = await fetch('../../api/profile.php?action=change-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            old_password: oldPassword,
                            new_password: newPassword
                        })
                    });
                    
                    const result = await response.json();
                    
                    const alertDiv = document.getElementById('passwordAlert');
                    if (result.success) {
                        alertDiv.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>${result.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        document.getElementById('changePasswordForm').reset();
                    } else {
                        alertDiv.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>${result.error}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    }
                    
                    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                } catch (error) {
                    console.error('Error changing password:', error);
                    document.getElementById('passwordAlert').innerHTML = `
                        <div class="alert alert-danger">Có lỗi xảy ra khi đổi mật khẩu</div>
                    `;
                }
            });
        }

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = event.target.closest('button').querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }


        // Utility functions
        function formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleString('vi-VN');
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('vi-VN');
        }

        function formatCurrency(amount) {
            if (!amount) return '0 VNĐ';
            return new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND' 
            }).format(amount);
        }

        // Make loadSection global
        window.loadSection = loadSection;
    </script>
</body>
</html>

