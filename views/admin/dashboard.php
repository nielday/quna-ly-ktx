<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Hệ thống quản lý ký túc xá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
                        <i class="fas fa-university me-2"></i>
                        Admin Panel
                    </h4>
                </div>
                
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="#" data-section="dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="#" data-section="users">
                        <i class="fas fa-users me-2"></i>Danh sách người dùng
                    </a>
                    <a class="nav-link" href="#" data-section="rooms">
                        <i class="fas fa-bed me-2"></i>Danh sách phòng
                    </a>
                    <a class="nav-link" href="#" data-section="students">
                        <i class="fas fa-user-graduate me-2"></i>Quản lý sinh viên
                    </a>
                    <a class="nav-link" href="#" data-section="registrations">
                        <i class="fas fa-clipboard-list me-2"></i>Đăng ký phòng
                    </a>
                    <a class="nav-link" href="#" data-section="utilities">
                        <i class="fas fa-bolt me-2"></i>Điện nước
                    </a>
                    <a class="nav-link" href="#" data-section="payments">
                        <i class="fas fa-credit-card me-2"></i>Thanh toán
                    </a>
                    <a class="nav-link" href="#" data-section="equipment">
                        <i class="fas fa-tools me-2"></i>Thiết bị
                    </a>
                    <a class="nav-link" href="#" data-section="maintenance">
                        <i class="fas fa-wrench me-2"></i>Bảo trì
                    </a>
                    <a class="nav-link" href="#" data-section="feedback">
                        <i class="fas fa-comments me-2"></i>Phản hồi
                    </a>
                    <a class="nav-link" href="#" data-section="notifications">
                        <i class="fas fa-bell me-2"></i>Thông báo
                    </a>
                    <a class="nav-link" href="#" data-section="reports">
                        <i class="fas fa-chart-bar me-2"></i>Báo cáo
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
                            <span class="me-3">Xin chào, <strong id="userName">Admin</strong></span>
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
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-bed stat-icon mb-2"></i>
                                    <h3 id="totalRooms">0</h3>
                                    <p class="mb-0">Tổng số phòng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users stat-icon mb-2"></i>
                                    <h3 id="totalStudents">0</h3>
                                    <p class="mb-0">Sinh viên đang ở</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-home stat-icon mb-2"></i>
                                    <h3 id="availableRooms">0</h3>
                                    <p class="mb-0">Phòng trống</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign stat-icon mb-2"></i>
                                    <h3 id="monthlyRevenue">0</h3>
                                    <p class="mb-0">Doanh thu tháng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Thống kê theo tháng</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Phân bố phòng</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="roomStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activities -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Hoạt động gần đây</h5>
                                </div>
                                <div class="card-body">
                                    <div id="recentActivities">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Cần xử lý</h5>
                                </div>
                                <div class="card-body">
                                    <div id="pendingTasks">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Global variables
        let currentUser = null;
        let monthlyChart = null;
        let roomStatusChart = null;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadDashboardData();
            initializeCharts();
            
            // Sidebar navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    const section = this.getAttribute('data-section');
                    
                    // Chỉ preventDefault cho các link có data-section
                    if (section) {
                        e.preventDefault();
                        loadSection(section);
                        
                        // Update active state
                        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                    }
                    // Các link không có data-section sẽ hoạt động bình thường (như user-management.php)
                });
            });
        });

        // Check authentication
        async function checkAuth() {
            try {
                const response = await fetch('../../api/auth.php?action=check');
                const data = await response.json();
                
                if (data.authenticated && data.user.role === 'admin') {
                    currentUser = data.user;
                    document.getElementById('userName').textContent = currentUser.full_name;
                } else {
                    window.location.href = '../views/auth/login.php';
                }
            } catch (error) {
                console.error('Auth check error:', error);
                window.location.href = '../views/auth/login.php';
            }
        }

        // Load dashboard data
        async function loadDashboardData() {
            try {
                // Load statistics
                const statsResponse = await fetch('../../api/admin/stats.php');
                const stats = await statsResponse.json();
                
                if (stats.success) {
                    document.getElementById('totalRooms').textContent = stats.data.total_rooms || 0;
                    document.getElementById('totalStudents').textContent = stats.data.total_students || 0;
                    document.getElementById('availableRooms').textContent = stats.data.available_rooms || 0;
                    document.getElementById('monthlyRevenue').textContent = formatCurrency(stats.data.monthly_revenue || 0);
                }
                
                // Load recent activities
                loadRecentActivities();
                
                // Load pending tasks
                loadPendingTasks();
                
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        // Load recent activities
        async function loadRecentActivities() {
            try {
                const response = await fetch('../../api/admin/activities.php');
                const data = await response.json();
                
                const container = document.getElementById('recentActivities');
                
                if (data.success && data.activities.length > 0) {
                    container.innerHTML = data.activities.map(activity => `
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-${getActivityIcon(activity.action)} text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">${activity.description}</div>
                                <small class="text-muted">${formatDateTime(activity.created_at)}</small>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center text-muted">Không có hoạt động nào</div>';
                }
            } catch (error) {
                console.error('Error loading activities:', error);
                document.getElementById('recentActivities').innerHTML = '<div class="text-center text-danger">Lỗi tải dữ liệu</div>';
            }
        }

        // Load pending tasks
        async function loadPendingTasks() {
            try {
                const response = await fetch('../../api/admin/pending-tasks.php');
                const data = await response.json();
                
                const container = document.getElementById('pendingTasks');
                
                if (data.success && data.tasks.length > 0) {
                    container.innerHTML = data.tasks.map(task => `
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-${getTaskIcon(task.type)} text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">${task.title}</div>
                                <small class="text-muted">${task.description}</small>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center text-muted">Không có nhiệm vụ nào</div>';
                }
            } catch (error) {
                console.error('Error loading pending tasks:', error);
                document.getElementById('pendingTasks').innerHTML = '<div class="text-center text-danger">Lỗi tải dữ liệu</div>';
            }
        }

        // Initialize charts
        function initializeCharts() {
            // Monthly chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });

            // Room status chart
            const roomStatusCtx = document.getElementById('roomStatusChart').getContext('2d');
            roomStatusChart = new Chart(roomStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Trống', 'Đầy', 'Bảo trì'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
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
                    break;
                case 'users':
                    pageTitle.textContent = 'Danh sách người dùng';
                    loadUsersSection();
                    break;
                case 'rooms':
                    pageTitle.textContent = 'Quản lý phòng';
                    loadRoomsSection();
                    break;
                case 'students':
                    pageTitle.textContent = 'Quản lý sinh viên';
                    loadStudentsSection();
                    break;
                case 'registrations':
                    pageTitle.textContent = 'Đăng ký phòng';
                    loadRegistrationsSection();
                    break;
                case 'utilities':
                    pageTitle.textContent = 'Điện nước';
                    loadUtilitiesSection();
                    break;
                case 'payments':
                    pageTitle.textContent = 'Thanh toán';
                    loadPaymentsSection();
                    break;
                case 'equipment':
                    pageTitle.textContent = 'Quản lý thiết bị';
                    loadEquipmentSection();
                    break;
                case 'maintenance':
                    pageTitle.textContent = 'Yêu cầu bảo trì';
                    loadMaintenanceSection();
                    break;
                case 'feedback':
                    pageTitle.textContent = 'Quản lý phản hồi';
                    loadFeedbackSection();
                    break;
                case 'notifications':
                    pageTitle.textContent = 'Thông báo';
                    loadNotificationsSection();
                    break;
                case 'reports':
                    pageTitle.textContent = 'Báo cáo';
                    loadReportsSection();
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

        // Utility functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        function formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('vi-VN');
        }

        function getActivityIcon(action) {
            const icons = {
                'login': 'sign-in-alt',
                'logout': 'sign-out-alt',
                'create': 'plus',
                'update': 'edit',
                'delete': 'trash',
                'payment': 'credit-card'
            };
            return icons[action] || 'circle';
        }

        function getTaskIcon(type) {
            const icons = {
                'registration': 'clipboard-list',
                'payment': 'credit-card',
                'maintenance': 'wrench',
                'complaint': 'exclamation-triangle'
            };
            return icons[type] || 'circle';
        }

        // Placeholder functions for other sections
        function loadUsersSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách người dùng</h5>
                        <a href="user-management.php" class="btn btn-primary">
                            <i class="fas fa-user-cog me-2"></i>Quản lý người dùng
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-select" id="userRoleFilter" onchange="loadUsersData()">
                                        <option value="">Tất cả vai trò</option>
                                        <option value="admin">Admin</option>
                                        <option value="staff">Cán bộ</option>
                                        <option value="student">Sinh viên</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="userSearchInput" 
                                           placeholder="Tìm kiếm theo username, email, họ tên..."
                                           onkeyup="handleUserSearch()">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Avatar</th>
                                        <th>Username</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            loadUsersData();
        }

        function loadRoomsSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-bed me-2"></i>Danh sách phòng</h5>
                        <a href="room-management.php" class="btn btn-primary">
                            <i class="fas fa-cog me-2"></i>Quản lý phòng
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Số phòng</th>
                                        <th>Tòa nhà</th>
                                        <th>Tầng</th>
                                        <th>Sức chứa</th>
                                        <th>Trạng thái</th>
                                        <th>Giá thuê</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="roomsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            loadRoomsData();
        }

        function loadStudentsSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách sinh viên</h5>
                        <button class="btn btn-primary" onclick="showAddStudentModal()">
                            <i class="fas fa-plus me-2"></i>Thêm sinh viên
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã SV</th>
                                        <th>Họ tên</th>
                                        <th>Khoa</th>
                                        <th>Lớp</th>
                                        <th>Giới tính</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            loadStudentsData();
        }

        async function loadUsersData() {
            try {
                const role = document.getElementById('userRoleFilter')?.value || '';
                const search = document.getElementById('userSearchInput')?.value || '';
                
                let url = '../../api/admin/users.php?limit=50';
                if (role) url += `&role=${role}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('usersTableBody');
                
                if (data.success && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(user => {
                        const roleColors = {
                            'admin': 'danger',
                            'staff': 'warning',
                            'student': 'info'
                        };
                        const roleColor = roleColors[user.role] || 'secondary';
                        const roleText = user.role === 'admin' ? 'Admin' : 
                                        user.role === 'staff' ? 'Cán bộ' : 'Sinh viên';
                        const statusBadge = user.is_active == 1 ? 
                            '<span class="badge bg-success">Hoạt động</span>' : 
                            '<span class="badge bg-secondary">Không hoạt động</span>';
                        
                        // Avatar placeholder
                        const avatarInitial = user.full_name ? user.full_name.charAt(0).toUpperCase() : 'U';
                        const avatarColors = {
                            'admin': 'bg-danger',
                            'staff': 'bg-warning',
                            'student': 'bg-info'
                        };
                        const avatarColor = avatarColors[user.role] || 'bg-secondary';
                        
                        return `
                        <tr>
                            <td>${user.id}</td>
                            <td>
                                <div class="rounded-circle ${avatarColor} text-white d-inline-flex align-items-center justify-content-center" 
                                     style="width: 35px; height: 35px; font-weight: bold;">
                                    ${avatarInitial}
                                </div>
                            </td>
                            <td>${user.username}</td>
                            <td>${user.full_name || 'N/A'}</td>
                            <td>${user.email || 'N/A'}</td>
                            <td><span class="badge bg-${roleColor}">${roleText}</span></td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewUserDetails(${user.id})" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="user-management.php" class="btn btn-sm btn-primary" title="Quản lý">
                                    <i class="fas fa-user-cog"></i>
                                </a>
                            </td>
                        </tr>`;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading users:', error);
                document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        let userSearchTimeout = null;
        function handleUserSearch() {
            clearTimeout(userSearchTimeout);
            userSearchTimeout = setTimeout(() => {
                loadUsersData();
            }, 500);
        }

        async function loadRoomsData() {
            try {
                const response = await fetch('../../api/rooms.php');
                const data = await response.json();
                
                const tbody = document.getElementById('roomsTableBody');
                
                if (data.success && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(room => {
                        const statusColor = getStatusColor(room.status);
                        const statusText = getStatusText(room.status);
                        const formattedFee = formatCurrency(room.monthly_fee);
                        return `
                        <tr>
                            <td>${room.room_number}</td>
                            <td>${room.building_name}</td>
                            <td>${room.floor_number}</td>
                            <td>${room.current_occupancy}/${room.capacity}</td>
                            <td>
                                <span class="badge bg-${statusColor}">
                                    ${statusText}
                                </span>
                            </td>
                            <td>${formattedFee}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewRoomDetails(${room.id})" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="room-management.php?id=${room.id}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>`;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                document.getElementById('roomsTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        async function loadStudentsData() {
            try {
                const response = await fetch('../../api/students.php');
                const data = await response.json();
                
                const tbody = document.getElementById('studentsTableBody');
                
                if (data.success && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(student => `
                        <tr>
                            <td>${student.student_code}</td>
                            <td>${student.full_name}</td>
                            <td>${student.faculty}</td>
                            <td>${student.class_name || 'N/A'}</td>
                            <td>${student.gender === 'male' ? 'Nam' : 'Nữ'}</td>
                            <td>
                                <span class="badge bg-${student.is_active ? 'success' : 'danger'}">
                                    ${student.is_active ? 'Hoạt động' : 'Không hoạt động'}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editStudent(${student.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(${student.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading students:', error);
                document.getElementById('studentsTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        function getStatusColor(status) {
            const colors = {
                'available': 'success',
                'full': 'danger',
                'maintenance': 'warning',
                'reserved': 'info'
            };
            return colors[status] || 'secondary';
        }

        function getStatusText(status) {
            const texts = {
                'available': 'Trống',
                'full': 'Đầy',
                'maintenance': 'Bảo trì',
                'reserved': 'Đã đặt'
            };
            return texts[status] || status;
        }

        // View room details in modal
        async function viewRoomDetails(roomId) {
            try {
                const response = await fetch(`../../api/rooms.php?id=${roomId}`);
                const data = await response.json();
                
                console.log('Room details response:', data); // Debug
                
                if (data.success && data.data) {
                    const room = data.data;
                    const statusColor = getStatusColor(room.status);
                    const statusText = getStatusText(room.status);
                    
                    const modalContent = `
                        <div class="modal fade" id="roomDetailsModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-door-open me-2"></i>Chi tiết phòng ${room.room_number}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Tòa nhà:</strong> ${room.building_name || 'N/A'}</p>
                                                <p><strong>Địa chỉ:</strong> ${room.building_address || 'N/A'}</p>
                                                <p><strong>Tầng:</strong> ${room.floor_number}</p>
                                                <p><strong>Sức chứa:</strong> ${room.capacity} người</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Đang ở:</strong> ${room.current_occupancy || 0} người</p>
                                                <p><strong>Loại phòng:</strong> ${room.room_type ? room.room_type.toUpperCase() : 'Standard'}</p>
                                                <p><strong>Giá thuê:</strong> ${formatCurrency(room.monthly_fee)}/tháng</p>
                                                <p><strong>Trạng thái:</strong> 
                                                    <span class="badge bg-${statusColor}">${statusText}</span>
                                                </p>
                                            </div>
                                        </div>
                                        ${room.description ? `<div class="mt-3"><strong>Mô tả:</strong><p>${room.description}</p></div>` : ''}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <a href="room-management.php?id=${room.id}" class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Remove old modal if exists
                    const oldModal = document.getElementById('roomDetailsModal');
                    if (oldModal) oldModal.remove();
                    
                    // Add new modal
                    document.body.insertAdjacentHTML('beforeend', modalContent);
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('roomDetailsModal'));
                    modal.show();
                } else {
                    alert('Không thể tải thông tin phòng');
                }
            } catch (error) {
                console.error('Error loading room details:', error);
                alert('Lỗi khi tải thông tin phòng');
            }
        }

        // View user details in modal
        async function viewUserDetails(userId) {
            try {
                const response = await fetch(`../../api/admin/users.php?id=${userId}`);
                const data = await response.json();
                
                console.log('User details response:', data); // Debug
                
                if (data.success && data.data) {
                    const user = data.data;
                    
                    const roleColors = {
                        'admin': 'danger',
                        'staff': 'warning',
                        'student': 'info'
                    };
                    const roleColor = roleColors[user.role] || 'secondary';
                    const roleText = user.role === 'admin' ? 'Admin' : 
                                    user.role === 'staff' ? 'Cán bộ' : 'Sinh viên';
                    
                    const statusBadge = user.is_active == 1 ? 
                        '<span class="badge bg-success">Hoạt động</span>' : 
                        '<span class="badge bg-secondary">Không hoạt động</span>';
                    
                    const avatarInitial = user.full_name ? user.full_name.charAt(0).toUpperCase() : 'U';
                    const avatarColor = roleColors[user.role] ? `bg-${roleColors[user.role]}` : 'bg-secondary';
                    
                    const modalContent = `
                        <div class="modal fade" id="userDetailsModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-user me-2"></i>Chi tiết người dùng
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="text-center mb-4">
                                            <div class="rounded-circle ${avatarColor} text-white d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                                                ${avatarInitial}
                                            </div>
                                            <h4 class="mt-3">${user.full_name || 'N/A'}</h4>
                                            <span class="badge bg-${roleColor}">${roleText}</span>
                                            ${statusBadge}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>ID:</strong> ${user.id}</p>
                                                <p><strong>Username:</strong> ${user.username}</p>
                                                <p><strong>Email:</strong> ${user.email || 'N/A'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Số điện thoại:</strong> ${user.phone || 'N/A'}</p>
                                                <p><strong>Vai trò:</strong> <span class="badge bg-${roleColor}">${roleText}</span></p>
                                                <p><strong>Ngày tạo:</strong> ${user.created_at ? new Date(user.created_at).toLocaleDateString('vi-VN') : 'N/A'}</p>
                                            </div>
                                        </div>
                                        ${user.student_info ? `
                                        <hr>
                                        <h6><i class="fas fa-graduation-cap me-2"></i>Thông tin sinh viên</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Mã sinh viên:</strong> ${user.student_info.student_code || 'N/A'}</p>
                                                <p><strong>Khoa:</strong> ${user.student_info.faculty || 'N/A'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Lớp:</strong> ${user.student_info.class || 'N/A'}</p>
                                                <p><strong>Giới tính:</strong> ${user.student_info.gender === 'male' ? 'Nam' : user.student_info.gender === 'female' ? 'Nữ' : 'Khác'}</p>
                                            </div>
                                        </div>
                                        ` : ''}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <a href="user-management.php" class="btn btn-primary">
                                            <i class="fas fa-user-cog me-2"></i>Quản lý người dùng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Remove old modal if exists
                    const oldModal = document.getElementById('userDetailsModal');
                    if (oldModal) oldModal.remove();
                    
                    // Add new modal
                    document.body.insertAdjacentHTML('beforeend', modalContent);
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
                    modal.show();
                } else {
                    alert('Không thể tải thông tin người dùng');
                }
            } catch (error) {
                console.error('Error loading user details:', error);
                alert('Lỗi khi tải thông tin người dùng');
            }
        }

        // Placeholder functions for modals and actions
        function showAddStudentModal() {
            alert('Chức năng thêm sinh viên đang được phát triển');
        }

        function editStudent(id) {
            alert(`Chỉnh sửa sinh viên ${id} - Chức năng đang được phát triển`);
        }

        function deleteStudent(id) {
            if (confirm('Bạn có chắc chắn muốn xóa sinh viên này?')) {
                alert(`Xóa sinh viên ${id} - Chức năng đang được phát triển`);
            }
        }

        // Load other sections
        function loadRegistrationsSection() {
            dynamicContent.innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Đăng ký phòng</h5>
                        <a href="registration-management.php" class="btn btn-primary">
                            <i class="fas fa-cog me-2"></i>Quản lý đăng ký
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sinh viên</th>
                                        <th>Phòng</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="registrationsTableBody">
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
            loadRegistrationsData();
        }

        async function loadRegistrationsData() {
            try {
                const response = await fetch('../../api/registrations.php?status=pending');
                const data = await response.json();
                const tbody = document.getElementById('registrationsTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(reg => `
                        <tr>
                            <td>${reg.student_name || reg.student_code}</td>
                            <td>${reg.room_number} - ${reg.building_name}</td>
                            <td>${formatDate(reg.start_date)}</td>
                            <td>${formatDate(reg.end_date)}</td>
                            <td><span class="badge bg-warning">${reg.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-success me-1" onclick="approveReg(${reg.id})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectReg(${reg.id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Không có đăng ký chờ duyệt</td></tr>';
                }
            } catch (error) {
                console.error('Error loading registrations:', error);
            }
        }

        function loadUtilitiesSection() {
            dynamicContent.innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Lịch sử điện nước</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Phòng</th>
                                        <th>Điện (kWh)</th>
                                        <th>Nước (m³)</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="utilitiesTableBody">
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
            loadUtilitiesData();
        }

        async function loadUtilitiesData() {
            try {
                const response = await fetch('../../api/utilities.php');
                const data = await response.json();
                const tbody = document.getElementById('utilitiesTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(ut => `
                        <tr>
                            <td>${formatDate(ut.reading_date)}</td>
                            <td>${ut.room_number}</td>
                            <td>${ut.electricity_reading}</td>
                            <td>${ut.water_reading}</td>
                            <td>${formatCurrency(ut.total_amount)}</td>
                            <td><span class="badge bg-${ut.is_paid ? 'success' : 'warning'}">${ut.is_paid ? 'Đã thanh toán' : 'Chưa thanh toán'}</span></td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading utilities:', error);
            }
        }

        function loadPaymentsSection() {
            dynamicContent.innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Lịch sử thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Sinh viên</th>
                                        <th>Loại</th>
                                        <th>Số tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
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
            loadPaymentsData();
        }

        async function loadPaymentsData() {
            try {
                const response = await fetch('../../api/payments.php');
                const data = await response.json();
                const tbody = document.getElementById('paymentsTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(pay => `
                        <tr>
                            <td>${formatDate(pay.payment_date)}</td>
                            <td>${pay.student_name || pay.student_code}</td>
                            <td><span class="badge bg-primary">${getPaymentType(pay.payment_type)}</span></td>
                            <td>${formatCurrency(pay.amount)}</td>
                            <td>${getPaymentMethod(pay.payment_method)}</td>
                            <td><span class="badge bg-${pay.status === 'completed' ? 'success' : 'warning'}">${getPaymentStatus(pay.status)}</span></td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading payments:', error);
            }
        }

        function loadEquipmentSection() {
            dynamicContent.innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Quản lý Thiết bị</h5>
                        <a href="equipment-management.php" class="btn btn-primary">
                            <i class="fas fa-cog me-2"></i>Quản lý thiết bị
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Quản lý toàn bộ thiết bị trong hệ thống. Theo dõi trạng thái, sửa chữa và thay thế thiết bị.
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-success text-white mb-3">
                                    <div class="card-body">
                                        <h6>Thiết bị hoạt động</h6>
                                        <h3 id="workingEquipment">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white mb-3">
                                    <div class="card-body">
                                        <h6>Thiết bị hỏng</h6>
                                        <h3 id="brokenEquipment">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-dark mb-3">
                                    <div class="card-body">
                                        <h6>Đang sửa chữa</h6>
                                        <h3 id="maintenanceEquipment">-</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            loadEquipmentStats();
        }

        async function loadEquipmentStats() {
            try {
                const response = await fetch('../../api/equipment.php?action=stats');
                const data = await response.json();
                
                if (data.success && data.data) {
                    document.getElementById('workingEquipment').textContent = data.data.working || 0;
                    document.getElementById('brokenEquipment').textContent = data.data.broken || 0;
                    document.getElementById('maintenanceEquipment').textContent = data.data.maintenance || 0;
                }
            } catch (error) {
                console.error('Error loading equipment stats:', error);
            }
        }

        function loadMaintenanceSection() {
            dynamicContent.innerHTML = `
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h3 id="pendingMaintenanceCount">0</h3>
                                <p class="mb-0">Chờ xử lý</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-spinner fa-2x text-info mb-2"></i>
                                <h3 id="inProgressMaintenanceCount">0</h3>
                                <p class="mb-0">Đang xử lý</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h3 id="completedMaintenanceCount">0</h3>
                                <p class="mb-0">Hoàn thành</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-2x text-danger mb-2"></i>
                                <h3 id="totalMaintenanceCost">0đ</h3>
                                <p class="mb-0">Tổng chi phí</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Lọc theo trạng thái:</label>
                                <select class="form-select" id="maintenanceStatusFilter" onchange="loadMaintenanceData()">
                                    <option value="">Tất cả</option>
                                    <option value="pending">Chờ xử lý</option>
                                    <option value="in_progress">Đang xử lý</option>
                                    <option value="completed">Hoàn thành</option>
                                    <option value="cancelled">Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lọc theo độ ưu tiên:</label>
                                <select class="form-select" id="maintenancePriorityFilter" onchange="loadMaintenanceData()">
                                    <option value="">Tất cả</option>
                                    <option value="urgent">Khẩn cấp</option>
                                    <option value="high">Cao</option>
                                    <option value="medium">Trung bình</option>
                                    <option value="low">Thấp</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="loadMaintenanceData()">
                                    <i class="fas fa-sync me-2"></i>Làm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Requests Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wrench me-2"></i>Danh sách yêu cầu bảo trì</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Phòng</th>
                                        <th>Loại</th>
                                        <th>Mô tả</th>
                                        <th>Ưu tiên</th>
                                        <th>Trạng thái</th>
                                        <th>Người gửi</th>
                                        <th>Phân công</th>
                                        <th>Chi phí</th>
                                        <th>Ngày tạo</th>
                                        <th style="min-width: 150px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="maintenanceTableBody">
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Assign Modal -->
                <div class="modal fade" id="assignMaintenanceModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Phân công yêu cầu bảo trì</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="assignRequestId">
                                <div class="mb-3">
                                    <label class="form-label">Phân công cho:</label>
                                    <select class="form-select" id="assignedTo" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ước tính chi phí (VNĐ):</label>
                                    <input type="number" class="form-control" id="estimatedCost" placeholder="Nhập chi phí ước tính">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary" onclick="submitAssignMaintenance()">
                                    <i class="fas fa-check me-2"></i>Xác nhận
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complete Modal -->
                <div class="modal fade" id="completeMaintenanceModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Hoàn thành yêu cầu bảo trì</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="completeRequestId">
                                <div class="mb-3">
                                    <label class="form-label">Chi phí thực tế (VNĐ):</label>
                                    <input type="number" class="form-control" id="actualCost" required placeholder="Nhập chi phí thực tế">
                                    <small class="text-muted">Nếu có chi phí, sinh viên sẽ nhận hóa đơn thanh toán</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú:</label>
                                    <textarea class="form-control" id="completionNotes" rows="3" placeholder="Nhập ghi chú (nếu có)"></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="createInvoice" checked>
                                        <label class="form-check-label" for="createInvoice">
                                            <strong>Tạo hóa đơn cho sinh viên</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Tự động tạo hóa đơn thanh toán cho sinh viên đã gửi yêu cầu
                                            </small>
                                        </label>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <small>
                                        Hóa đơn sẽ được gán cho sinh viên gửi yêu cầu với loại thanh toán: 
                                        <strong>Chi phí bảo trì</strong>
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-success" onclick="submitCompleteMaintenance()">
                                    <i class="fas fa-check-circle me-2"></i>Hoàn thành
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            loadMaintenanceStats();
            loadMaintenanceData();
            loadStaffList();
        }

        // Load maintenance statistics
        async function loadMaintenanceStats() {
            try {
                const response = await fetch('../../api/maintenance.php?action=stats');
                const data = await response.json();
                
                if (data.success && data.data) {
                    document.getElementById('pendingMaintenanceCount').textContent = data.data.pending || 0;
                    document.getElementById('inProgressMaintenanceCount').textContent = data.data.in_progress || 0;
                    document.getElementById('completedMaintenanceCount').textContent = data.data.completed || 0;
                    document.getElementById('totalMaintenanceCost').textContent = formatCurrency(data.data.total_cost || 0);
                }
            } catch (error) {
                console.error('Error loading maintenance stats:', error);
            }
        }

        // Load maintenance requests data
        async function loadMaintenanceData() {
            try {
                const status = document.getElementById('maintenanceStatusFilter')?.value || '';
                const priority = document.getElementById('maintenancePriorityFilter')?.value || '';
                
                let url = '../../api/maintenance.php?action=list';
                if (status) url += `&status=${status}`;
                if (priority) url += `&priority=${priority}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('maintenanceTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(req => {
                        const priorityBadge = getPriorityBadge(req.priority);
                        const statusBadge = getMaintenanceStatusBadge(req.status);
                        const requestType = getRequestTypeBadge(req.request_type);
                        
                        return `
                            <tr>
                                <td>#${req.id}</td>
                                <td>
                                    <strong>${req.room_number}</strong><br>
                                    <small class="text-muted">${req.building_name}</small>
                                </td>
                                <td>${requestType}</td>
                                <td>
                                    ${req.description.substring(0, 50)}${req.description.length > 50 ? '...' : ''}
                                    ${req.equipment_name ? `<br><small class="text-muted">Thiết bị: ${req.equipment_name}</small>` : ''}
                                </td>
                                <td>${priorityBadge}</td>
                                <td>${statusBadge}</td>
                                <td>${req.student_name || '<span class="text-muted">N/A</span>'}</td>
                                <td>${req.assigned_to_name || '<span class="text-muted">Chưa phân công</span>'}</td>
                                <td>
                                    ${req.estimated_cost ? 'DT: ' + formatCurrency(req.estimated_cost) : ''}
                                    ${req.actual_cost ? '<br>TT: ' + formatCurrency(req.actual_cost) : ''}
                                </td>
                                <td>${formatDateTime(req.created_at)}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        ${req.status === 'pending' ? `
                                            <button class="btn btn-outline-info" onclick="showAssignModal(${req.id})" title="Phân công">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="cancelMaintenance(${req.id})" title="Hủy">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        ` : ''}
                                        ${req.status === 'in_progress' ? `
                                            <button class="btn btn-outline-success" onclick="showCompleteModal(${req.id})" title="Hoàn thành">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        ` : ''}
                                        <button class="btn btn-outline-primary" onclick="viewMaintenanceDetails(${req.id})" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted">Không có yêu cầu bảo trì nào</td></tr>';
                }
            } catch (error) {
                console.error('Error loading maintenance data:', error);
                document.getElementById('maintenanceTableBody').innerHTML = 
                    '<tr><td colspan="11" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        // Load staff list for assignment
        async function loadStaffList() {
            try {
                const response = await fetch('../../api/students.php'); // Should be users API with role=staff
                // For now, using a placeholder - you should create a proper API endpoint
                const select = document.getElementById('assignedTo');
                if (select) {
                    // Placeholder options - replace with actual API call
                    select.innerHTML = `
                        <option value="">-- Chọn nhân viên --</option>
                        <option value="1">Admin</option>
                    `;
                }
            } catch (error) {
                console.error('Error loading staff list:', error);
            }
        }

        // Show assign modal
        function showAssignModal(requestId) {
            document.getElementById('assignRequestId').value = requestId;
            document.getElementById('assignedTo').value = '';
            document.getElementById('estimatedCost').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('assignMaintenanceModal'));
            modal.show();
        }

        // Submit assign maintenance
        async function submitAssignMaintenance() {
            const requestId = document.getElementById('assignRequestId').value;
            const assignedTo = document.getElementById('assignedTo').value;
            const estimatedCost = document.getElementById('estimatedCost').value;
            
            if (!assignedTo) {
                alert('Vui lòng chọn nhân viên!');
                return;
            }
            
            try {
                const response = await fetch('../../api/maintenance.php?action=assign', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        request_id: requestId,
                        assigned_to: assignedTo,
                        estimated_cost: estimatedCost || null
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Phân công thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('assignMaintenanceModal')).hide();
                    loadMaintenanceData();
                    loadMaintenanceStats();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể phân công'));
                }
            } catch (error) {
                console.error('Error assigning maintenance:', error);
                alert('Lỗi khi phân công yêu cầu');
            }
        }

        // Show complete modal
        function showCompleteModal(requestId) {
            document.getElementById('completeRequestId').value = requestId;
            document.getElementById('actualCost').value = '';
            document.getElementById('completionNotes').value = '';
            document.getElementById('createInvoice').checked = true; // Mặc định tạo hóa đơn
            
            const modal = new bootstrap.Modal(document.getElementById('completeMaintenanceModal'));
            modal.show();
        }

        // Submit complete maintenance
        async function submitCompleteMaintenance() {
            const requestId = document.getElementById('completeRequestId').value;
            const actualCost = document.getElementById('actualCost').value;
            const notes = document.getElementById('completionNotes').value;
            const createInvoice = document.getElementById('createInvoice').checked;
            
            if (!actualCost) {
                alert('Vui lòng nhập chi phí thực tế!');
                return;
            }
            
            try {
                const response = await fetch('../../api/maintenance.php?action=complete', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        request_id: requestId,
                        actual_cost: actualCost,
                        notes: notes,
                        create_invoice: createInvoice
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    let message = 'Hoàn thành yêu cầu thành công!';
                    
                    // Nếu có tạo hóa đơn
                    if (data.payment_id) {
                        message += '\n✅ Đã tạo hóa đơn #' + data.payment_id + ' cho sinh viên';
                    } else if (data.warning) {
                        message += '\n⚠️ ' + data.warning;
                    }
                    
                    alert(message);
                    bootstrap.Modal.getInstance(document.getElementById('completeMaintenanceModal')).hide();
                    loadMaintenanceData();
                    loadMaintenanceStats();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể hoàn thành'));
                }
            } catch (error) {
                console.error('Error completing maintenance:', error);
                alert('Lỗi khi hoàn thành yêu cầu');
            }
        }

        // Cancel maintenance
        async function cancelMaintenance(requestId) {
            const reason = prompt('Vui lòng nhập lý do hủy:');
            
            if (reason === null) return;
            if (!reason || reason.trim() === '') {
                alert('Vui lòng nhập lý do hủy!');
                return;
            }
            
            try {
                const response = await fetch('../../api/maintenance.php?action=cancel', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        request_id: requestId,
                        reason: reason.trim()
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Hủy yêu cầu thành công!');
                    loadMaintenanceData();
                    loadMaintenanceStats();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể hủy yêu cầu'));
                }
            } catch (error) {
                console.error('Error cancelling maintenance:', error);
                alert('Lỗi khi hủy yêu cầu');
            }
        }

        // View maintenance details
        function viewMaintenanceDetails(requestId) {
            alert(`Xem chi tiết yêu cầu #${requestId} - Chức năng sẽ được bổ sung`);
        }

        // Helper functions for maintenance
        function getPriorityBadge(priority) {
            const badges = {
                'urgent': '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Khẩn cấp</span>',
                'high': '<span class="badge bg-warning text-dark"><i class="fas fa-arrow-up"></i> Cao</span>',
                'medium': '<span class="badge bg-info"><i class="fas fa-minus"></i> Trung bình</span>',
                'low': '<span class="badge bg-secondary"><i class="fas fa-arrow-down"></i> Thấp</span>'
            };
            return badges[priority] || priority;
        }

        function getMaintenanceStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-warning">Chờ xử lý</span>',
                'in_progress': '<span class="badge bg-info">Đang xử lý</span>',
                'completed': '<span class="badge bg-success">Hoàn thành</span>',
                'cancelled': '<span class="badge bg-danger">Đã hủy</span>'
            };
            return badges[status] || status;
        }

        function getRequestTypeBadge(type) {
            const badges = {
                'equipment': '<span class="badge bg-primary"><i class="fas fa-tools"></i> Thiết bị</span>',
                'room': '<span class="badge bg-success"><i class="fas fa-door-open"></i> Phòng</span>',
                'utility': '<span class="badge bg-warning text-dark"><i class="fas fa-bolt"></i> Tiện ích</span>'
            };
            return badges[type] || type;
        }

        function loadFeedbackSection() {
            dynamicContent.innerHTML = `
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-inbox fa-2x text-warning mb-2"></i>
                                <h3 id="newFeedbackCount">0</h3>
                                <p class="mb-0">Phản hồi mới</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-hourglass-half fa-2x text-info mb-2"></i>
                                <h3 id="inProgressFeedbackCount">0</h3>
                                <p class="mb-0">Đang xử lý</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h3 id="resolvedFeedbackCount">0</h3>
                                <p class="mb-0">Đã giải quyết</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-secondary">
                            <div class="card-body text-center">
                                <i class="fas fa-archive fa-2x text-secondary mb-2"></i>
                                <h3 id="closedFeedbackCount">0</h3>
                                <p class="mb-0">Đã đóng</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Lọc theo trạng thái:</label>
                                <select class="form-select" id="feedbackStatusFilter" onchange="loadFeedbackData()">
                                    <option value="">Tất cả</option>
                                    <option value="new" selected>Mới</option>
                                    <option value="in_progress">Đang xử lý</option>
                                    <option value="resolved">Đã giải quyết</option>
                                    <option value="closed">Đã đóng</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lọc theo loại:</label>
                                <select class="form-select" id="feedbackCategoryFilter" onchange="loadFeedbackData()">
                                    <option value="">Tất cả</option>
                                    <option value="complaint">Khiếu nại</option>
                                    <option value="suggestion">Đề xuất</option>
                                    <option value="compliment">Khen ngợi</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="loadFeedbackData()">
                                    <i class="fas fa-sync me-2"></i>Làm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Danh sách phản hồi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sinh viên</th>
                                        <th>Loại</th>
                                        <th>Tiêu đề</th>
                                        <th>Nội dung</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày gửi</th>
                                        <th style="min-width: 120px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="feedbackTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- View Feedback Modal -->
                <div class="modal fade" id="viewFeedbackModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Chi tiết phản hồi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="viewFeedbackId">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Sinh viên:</strong>
                                        <p id="viewStudentName" class="mb-0"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Mã SV:</strong>
                                        <p id="viewStudentCode" class="mb-0"></p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Loại:</strong>
                                        <p id="viewCategory" class="mb-0"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Ngày gửi:</strong>
                                        <p id="viewCreatedAt" class="mb-0"></p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Tiêu đề:</strong>
                                    <p id="viewSubject" class="mb-0"></p>
                                </div>

                                <div class="mb-3">
                                    <strong>Nội dung:</strong>
                                    <div id="viewMessage" class="border rounded p-3 bg-light"></div>
                                </div>

                                <div id="responseSection" class="mb-3" style="display: none;">
                                    <hr>
                                    <strong>Phản hồi từ Admin:</strong>
                                    <div id="viewResponse" class="border rounded p-3 bg-success bg-opacity-10 mt-2"></div>
                                    <small class="text-muted">
                                        Phản hồi bởi: <span id="viewRespondedBy"></span> - 
                                        <span id="viewRespondedAt"></span>
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary" onclick="showRespondModal()">
                                    <i class="fas fa-reply me-2"></i>Trả lời
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Respond Modal -->
                <div class="modal fade" id="respondFeedbackModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Trả lời phản hồi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="respondFeedbackId">
                                <div class="mb-3">
                                    <label class="form-label">Nội dung trả lời:</label>
                                    <textarea class="form-control" id="responseText" rows="5" required 
                                        placeholder="Nhập nội dung trả lời cho sinh viên..."></textarea>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Sau khi trả lời, trạng thái sẽ được cập nhật thành "Đã giải quyết"</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-success" onclick="submitResponse()">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi phản hồi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            loadFeedbackStats();
            loadFeedbackData();
        }

        // Load feedback statistics
        async function loadFeedbackStats() {
            try {
                const statuses = ['new', 'in_progress', 'resolved', 'closed'];
                
                for (const status of statuses) {
                    const response = await fetch(`../../api/feedback.php?action=list&status=${status}&limit=1000`);
                    const data = await response.json();
                    
                    if (data.success) {
                        const count = data.data ? data.data.length : 0;
                        const elementId = status === 'in_progress' ? 'inProgressFeedbackCount' : 
                                         `${status}FeedbackCount`;
                        document.getElementById(elementId).textContent = count;
                    }
                }
            } catch (error) {
                console.error('Error loading feedback stats:', error);
            }
        }

        // Load feedback data
        async function loadFeedbackData() {
            try {
                const status = document.getElementById('feedbackStatusFilter')?.value || '';
                const category = document.getElementById('feedbackCategoryFilter')?.value || '';
                
                let url = '../../api/feedback.php?action=list';
                if (status) url += `&status=${status}`;
                if (category) url += `&category=${category}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('feedbackTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(feedback => {
                        const categoryBadge = getFeedbackCategoryBadge(feedback.category);
                        const statusBadge = getFeedbackStatusBadge(feedback.status);
                        
                        return `
                            <tr>
                                <td>#${feedback.id}</td>
                                <td>
                                    <strong>${feedback.student_name}</strong><br>
                                    <small class="text-muted">${feedback.student_email}</small>
                                </td>
                                <td>${categoryBadge}</td>
                                <td>${feedback.subject}</td>
                                <td>
                                    ${feedback.message.substring(0, 50)}${feedback.message.length > 50 ? '...' : ''}
                                </td>
                                <td>${statusBadge}</td>
                                <td>${formatDateTime(feedback.created_at)}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" onclick='viewFeedback(${JSON.stringify(feedback)})' title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        ${feedback.status === 'new' || feedback.status === 'in_progress' ? `
                                            <button class="btn btn-outline-success" onclick="quickRespond(${feedback.id})" title="Trả lời">
                                                <i class="fas fa-reply"></i>
                                            </button>
                                        ` : ''}
                                        ${feedback.status === 'resolved' ? `
                                            <button class="btn btn-outline-success" onclick="closeFeedback(${feedback.id})" title="Đóng (Hoàn tất)">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        ` : ''}
                                        ${feedback.status === 'new' ? `
                                            <button class="btn btn-outline-danger" onclick="closeFeedbackWithReason(${feedback.id})" title="Từ chối/Đóng">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Không có phản hồi nào</td></tr>';
                }
            } catch (error) {
                console.error('Error loading feedback data:', error);
                document.getElementById('feedbackTableBody').innerHTML = 
                    '<tr><td colspan="8" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        // View feedback details
        function viewFeedback(feedback) {
            document.getElementById('viewFeedbackId').value = feedback.id;
            document.getElementById('viewStudentName').textContent = feedback.student_name;
            document.getElementById('viewStudentCode').textContent = feedback.student_code;
            document.getElementById('viewCategory').innerHTML = getFeedbackCategoryBadge(feedback.category);
            document.getElementById('viewCreatedAt').textContent = formatDateTime(feedback.created_at);
            document.getElementById('viewSubject').textContent = feedback.subject;
            document.getElementById('viewMessage').textContent = feedback.message;
            
            // Show response if exists
            const responseSection = document.getElementById('responseSection');
            if (feedback.response) {
                document.getElementById('viewResponse').textContent = feedback.response;
                document.getElementById('viewRespondedBy').textContent = feedback.responded_by_name || 'N/A';
                document.getElementById('viewRespondedAt').textContent = feedback.responded_at ? formatDateTime(feedback.responded_at) : 'N/A';
                responseSection.style.display = 'block';
            } else {
                responseSection.style.display = 'none';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('viewFeedbackModal'));
            modal.show();
        }

        // Show respond modal
        function showRespondModal() {
            const feedbackId = document.getElementById('viewFeedbackId').value;
            document.getElementById('respondFeedbackId').value = feedbackId;
            document.getElementById('responseText').value = '';
            
            // Hide view modal
            bootstrap.Modal.getInstance(document.getElementById('viewFeedbackModal')).hide();
            
            // Show respond modal
            const modal = new bootstrap.Modal(document.getElementById('respondFeedbackModal'));
            modal.show();
        }

        // Quick respond
        function quickRespond(feedbackId) {
            document.getElementById('respondFeedbackId').value = feedbackId;
            document.getElementById('responseText').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('respondFeedbackModal'));
            modal.show();
        }

        // Submit response
        async function submitResponse() {
            const feedbackId = document.getElementById('respondFeedbackId').value;
            const responseText = document.getElementById('responseText').value;
            
            if (!responseText || responseText.trim() === '') {
                alert('Vui lòng nhập nội dung phản hồi!');
                return;
            }
            
            try {
                const response = await fetch('../../api/feedback.php?action=respond', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        feedback_id: feedbackId,
                        response: responseText.trim()
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Gửi phản hồi thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('respondFeedbackModal')).hide();
                    loadFeedbackData();
                    loadFeedbackStats();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể gửi phản hồi'));
                }
            } catch (error) {
                console.error('Error submitting response:', error);
                alert('Lỗi khi gửi phản hồi');
            }
        }

        // Close feedback (sau khi resolved - hoàn tất)
        async function closeFeedback(feedbackId) {
            if (!confirm('Đóng phản hồi này?\n\nPhản hồi đã được giải quyết và sẽ được lưu trữ.')) {
                return;
            }
            
            try {
                const response = await fetch('../../api/feedback.php?action=update-status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        feedback_id: feedbackId,
                        status: 'closed'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Đã đóng và lưu trữ phản hồi!');
                    loadFeedbackData();
                    loadFeedbackStats();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể đóng phản hồi'));
                }
            } catch (error) {
                console.error('Error closing feedback:', error);
                alert('Lỗi khi đóng phản hồi');
            }
        }

        // Close feedback with reason (từ chối/spam)
        async function closeFeedbackWithReason(feedbackId) {
            const reason = prompt('Lý do đóng phản hồi này:\n(Ví dụ: Spam, Trùng lặp, Không hợp lệ...)');
            
            if (reason === null) return; // User cancelled
            
            if (!reason || reason.trim() === '') {
                alert('Vui lòng nhập lý do!');
                return;
            }
            
            try {
                // Cập nhật status thành closed
                const response = await fetch('../../api/feedback.php?action=update-status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        feedback_id: feedbackId,
                        status: 'closed'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(`✅ Đã đóng phản hồi!\nLý do: ${reason.trim()}`);
                    loadFeedbackData();
                    loadFeedbackStats();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể đóng phản hồi'));
                }
            } catch (error) {
                console.error('Error closing feedback:', error);
                alert('Lỗi khi đóng phản hồi');
            }
        }

        // Helper functions for feedback
        function getFeedbackCategoryBadge(category) {
            const badges = {
                'complaint': '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Khiếu nại</span>',
                'suggestion': '<span class="badge bg-info"><i class="fas fa-lightbulb"></i> Đề xuất</span>',
                'compliment': '<span class="badge bg-success"><i class="fas fa-thumbs-up"></i> Khen ngợi</span>',
                'other': '<span class="badge bg-secondary"><i class="fas fa-question"></i> Khác</span>'
            };
            return badges[category] || category;
        }

        function getFeedbackStatusBadge(status) {
            const badges = {
                'new': '<span class="badge bg-warning"><i class="fas fa-inbox"></i> Mới</span>',
                'in_progress': '<span class="badge bg-info"><i class="fas fa-hourglass-half"></i> Đang xử lý</span>',
                'resolved': '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Đã giải quyết</span>',
                'closed': '<span class="badge bg-secondary"><i class="fas fa-archive"></i> Đã đóng</span>'
            };
            return badges[status] || status;
        }

        async function loadNotificationsSection() {
            dynamicContent.innerHTML = `
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bell me-2 text-primary"></i>
                                        Trung tâm thông báo
                                    </h5>
                                    <button class="btn btn-sm btn-outline-primary" onclick="loadNotificationsSection()">
                                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                                    </button>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tổng hợp các hoạt động và yêu cầu cần xử lý từ sinh viên
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê nhanh -->
                <div class="row mb-4" id="notificationStats">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bộ lọc -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-primary active" data-filter="all" onclick="filterNotifications('all')">
                                        <i class="fas fa-list me-1"></i>Tất cả
                                    </button>
                                    <button class="btn btn-outline-info" data-filter="registrations" onclick="filterNotifications('registrations')">
                                        <i class="fas fa-user-plus me-1"></i>Đăng ký phòng
                                    </button>
                                    <button class="btn btn-outline-warning" data-filter="feedback" onclick="filterNotifications('feedback')">
                                        <i class="fas fa-comments me-1"></i>Phản hồi
                                    </button>
                                    <button class="btn btn-outline-danger" data-filter="maintenance" onclick="filterNotifications('maintenance')">
                                        <i class="fas fa-wrench me-1"></i>Bảo trì
                                    </button>
                                    <button class="btn btn-outline-success" data-filter="payments" onclick="filterNotifications('payments')">
                                        <i class="fas fa-dollar-sign me-1"></i>Thanh toán
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách thông báo -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div id="notificationsList">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Load dữ liệu
            await loadNotificationData();
        }

        let currentNotificationFilter = 'all';
        let allNotificationsData = [];

        async function loadNotificationData() {
            try {
                console.log('Loading notification data...');
                
                // Helper function to safely fetch
                const safeFetch = async (url, name) => {
                    try {
                        console.log(`Fetching ${name} from ${url}`);
                        const response = await fetch(url);
                        const text = await response.text();
                        
                        console.log(`${name} response status:`, response.status);
                        console.log(`${name} response text:`, text.substring(0, 200));
                        
                        if (!response.ok) {
                            console.warn(`${name} API returned status ${response.status}`);
                            return { success: false, data: [] };
                        }
                        
                        if (!text || text.trim() === '') {
                            console.warn(`${name} API returned empty response`);
                            return { success: false, data: [] };
                        }
                        
                        const json = JSON.parse(text);
                        console.log(`${name} parsed successfully:`, json);
                        return json;
                    } catch (e) {
                        console.error(`Error with ${name} API:`, e);
                        return { success: false, data: [] };
                    }
                };

                // Fetch data từ nhiều nguồn - thử tuần tự để dễ debug
                const registrations = await safeFetch('../../api/registrations.php?status=pending', 'Registrations');
                const feedback = await safeFetch('../../api/feedback.php?action=list&status=new', 'Feedback');
                const maintenance = await safeFetch('../../api/maintenance.php?action=list&status=pending', 'Maintenance');
                // Lấy hóa đơn điện nước chưa thanh toán từ utility_readings (không phải payments)
                const utilityBills = await safeFetch('../../api/utilities.php?is_paid=false&limit=50', 'Utility Bills');
                const activities = await safeFetch('../../api/admin/activities.php?limit=20', 'Activities');

                // Tạo array thông báo tổng hợp
                allNotificationsData = [];

                // Đăng ký phòng pending
                if (registrations.success && registrations.data) {
                    registrations.data.forEach(reg => {
                        allNotificationsData.push({
                            type: 'registrations',
                            icon: 'fa-user-plus',
                            iconColor: 'info',
                            title: 'Đăng ký phòng mới',
                            message: '<strong>' + reg.student_name + '</strong> (' + reg.student_code + ') đăng ký phòng <strong>' + reg.room_number + '</strong> - ' + reg.building_name,
                            time: reg.created_at,
                            action: 'registrations',
                            badge: 'Chờ duyệt',
                            badgeColor: 'warning',
                            urgent: false
                        });
                    });
                }

                // Feedback mới
                if (feedback.success && feedback.data) {
                    feedback.data.filter(f => f.status === 'new').forEach(fb => {
                        allNotificationsData.push({
                            type: 'feedback',
                            icon: 'fa-comments',
                            iconColor: 'warning',
                            title: 'Phản hồi mới',
                            message: '<strong>' + fb.student_name + '</strong>: ' + fb.subject + ' - <em class="text-muted">' + fb.category + '</em>',
                            time: fb.created_at,
                            action: 'feedback',
                            badge: 'Chưa xử lý',
                            badgeColor: 'danger',
                            urgent: fb.category === 'complaint'
                        });
                    });
                }

                // Yêu cầu bảo trì pending
                if (maintenance.success && maintenance.data) {
                    maintenance.data.forEach(maint => {
                        allNotificationsData.push({
                            type: 'maintenance',
                            icon: 'fa-wrench',
                            iconColor: 'danger',
                            title: 'Yêu cầu bảo trì',
                            message: 'Phòng <strong>' + maint.room_number + '</strong> - ' + maint.building_name + ': ' + maint.description.substring(0, 60) + '...',
                            time: maint.created_at,
                            action: 'maintenance',
                            badge: maint.priority === 'urgent' ? 'Khẩn cấp' : 'Chờ xử lý',
                            badgeColor: maint.priority === 'urgent' ? 'danger' : 'warning',
                            urgent: maint.priority === 'urgent' || maint.priority === 'high'
                        });
                    });
                }

                // Hóa đơn điện nước chưa thanh toán từ bảng utility_readings
                // Trạng thái: is_paid = FALSE (khác với payments table)
                if (utilityBills.success && utilityBills.data && Array.isArray(utilityBills.data) && utilityBills.data.length > 0) {
                    console.log('Found unpaid utility bills:', utilityBills.data.length);
                    
                    utilityBills.data.forEach(bill => {
                        const roomInfo = bill.room_number ? `Phòng ${bill.room_number}` : '';
                        const buildingInfo = bill.building_name ? ` - ${bill.building_name}` : '';
                        const amount = parseFloat(bill.total_amount || 0);
                        const readingDate = bill.reading_date ? new Date(bill.reading_date).toLocaleDateString('vi-VN') : '';
                        const studentCount = parseInt(bill.student_count || bill.current_occupancy || 0);
                        const studentNames = bill.student_names || '';
                        
                        // Hiển thị thông tin sinh viên nếu có
                        let studentInfo = '';
                        if (studentNames && studentNames.trim() !== '') {
                            const names = studentNames.split(', ');
                            if (names.length <= 2) {
                                studentInfo = ` - ${names.join(', ')}`;
                            } else {
                                studentInfo = ` - ${names[0]}, ${names[1]} và ${names.length - 2} người khác`;
                            }
                        } else if (studentCount > 0) {
                            studentInfo = ` - ${studentCount} sinh viên`;
                        }
                        
                        allNotificationsData.push({
                            type: 'utilities',
                            icon: 'fa-bolt',
                            iconColor: 'warning',
                            title: 'Hóa đơn điện nước chưa thanh toán',
                            message: `<strong>${roomInfo}${buildingInfo}</strong>${studentInfo}: <strong>${formatCurrency(amount)}</strong> (Tháng ${readingDate})`,
                            time: bill.created_at || bill.reading_date,
                            action: 'utilities',
                            badge: 'Chưa thanh toán',
                            badgeColor: 'warning',
                            urgent: amount > 500000 // Ưu tiên hóa đơn lớn hơn 500k
                        });
                    });
                } else {
                    console.log('No unpaid utility bills found. Response:', utilityBills);
                }

                // Activity logs quan trọng (API dùng key "activities" thay vì "data")
                const activityData = activities.activities || activities.data || [];
                if (activities.success && activityData.length > 0) {
                    activityData.forEach(act => {
                        if (act.action.includes('created') || act.action.includes('registered')) {
                            let type = 'activity';
                            let icon = 'fa-history';
                            let iconColor = 'secondary';
                            let title = 'Hoạt động mới';
                            
                            if (act.action.includes('student')) {
                                type = 'students';
                                icon = 'fa-user-graduate';
                                iconColor = 'success';
                                title = 'Sinh viên mới đăng ký';
                            }
                            // Bỏ phần payment từ activity logs vì đã load trực tiếp từ payments API

                            allNotificationsData.push({
                                type: type,
                                icon: icon,
                                iconColor: iconColor,
                                title: title,
                                message: act.action,
                                time: act.created_at,
                                action: type,
                                badge: 'Hoạt động',
                                badgeColor: 'info',
                                urgent: false
                            });
                        }
                    });
                }

                // Sắp xếp theo thời gian (mới nhất trước, ưu tiên urgent)
                allNotificationsData.sort((a, b) => {
                    if (a.urgent && !b.urgent) return -1;
                    if (!a.urgent && b.urgent) return 1;
                    return new Date(b.time) - new Date(a.time);
                });

                console.log('Total notifications:', allNotificationsData.length);
                
                // Hiển thị stats
                displayNotificationStats();
                
                // Hiển thị danh sách
                displayNotifications();

            } catch (error) {
                console.error('Error loading notifications:', error);
                console.error('Error stack:', error.stack);
                
                document.getElementById('notificationStats').innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lỗi khi tải thông báo</strong>
                            <p class="mb-0 mt-2 small">Vui lòng mở Console (F12) để xem chi tiết lỗi.</p>
                        </div>
                    </div>
                `;
                
                document.getElementById('notificationsList').innerHTML = `
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Không thể tải thông báo</h5>
                        <p class="mb-2">Có thể do:</p>
                        <ul class="mb-0">
                            <li>Bạn chưa đăng nhập</li>
                            <li>API server chưa chạy</li>
                            <li>Thiếu dữ liệu trong database</li>
                        </ul>
                        <hr>
                        <button class="btn btn-primary btn-sm mt-2" onclick="loadNotificationsSection()">
                            <i class="fas fa-sync-alt me-1"></i>Thử lại
                        </button>
                    </div>
                `;
            }
        }

        function displayNotificationStats() {
            const stats = {
                all: allNotificationsData.length,
                registrations: allNotificationsData.filter(n => n.type === 'registrations').length,
                feedback: allNotificationsData.filter(n => n.type === 'feedback').length,
                maintenance: allNotificationsData.filter(n => n.type === 'maintenance').length,
                payments: allNotificationsData.filter(n => n.type === 'payments').length,
                urgent: allNotificationsData.filter(n => n.urgent).length
            };

            document.getElementById('notificationStats').innerHTML = `
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h3 class="mb-1 text-primary">${stats.all}</h3>
                            <small class="text-muted">Tổng số</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center ${stats.registrations > 0 ? 'border-start border-info border-3' : ''}">
                        <div class="card-body">
                            <h3 class="mb-1 text-info">${stats.registrations}</h3>
                            <small class="text-muted">Đăng ký</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center ${stats.feedback > 0 ? 'border-start border-warning border-3' : ''}">
                        <div class="card-body">
                            <h3 class="mb-1 text-warning">${stats.feedback}</h3>
                            <small class="text-muted">Phản hồi</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center ${stats.maintenance > 0 ? 'border-start border-danger border-3' : ''}">
                        <div class="card-body">
                            <h3 class="mb-1 text-danger">${stats.maintenance}</h3>
                            <small class="text-muted">Bảo trì</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center ${stats.payments > 0 ? 'border-start border-success border-3' : ''}">
                        <div class="card-body">
                            <h3 class="mb-1 text-success">${stats.payments}</h3>
                            <small class="text-muted">Thanh toán</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center ${stats.urgent > 0 ? 'bg-danger text-white' : ''}">
                        <div class="card-body">
                            <h3 class="mb-1 ${stats.urgent > 0 ? 'text-white' : ''}">${stats.urgent}</h3>
                            <small class="${stats.urgent > 0 ? 'text-white' : 'text-muted'}">
                                <i class="fas fa-exclamation-triangle me-1"></i>Khẩn cấp
                            </small>
                        </div>
                    </div>
                </div>
            `;
        }

        function displayNotifications() {
            let filteredData;
            if (currentNotificationFilter === 'all') {
                filteredData = allNotificationsData;
            } else if (currentNotificationFilter === 'payments') {
                // Hiển thị cả payments và utilities (hóa đơn điện nước)
                filteredData = allNotificationsData.filter(n => 
                    n.type === 'payments' || n.type === 'utilities'
                );
            } else {
                filteredData = allNotificationsData.filter(n => n.type === currentNotificationFilter);
            }

            if (filteredData.length === 0) {
                document.getElementById('notificationsList').innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có thông báo nào</h5>
                        <p class="text-muted">Tất cả đều đã được xử lý!</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="list-group list-group-flush">';
            
            filteredData.forEach(notification => {
                const timeAgo = getTimeAgo(notification.time);
                const urgentClass = notification.urgent ? 'border-start border-danger border-3' : '';
                
                html += `
                    <div class="list-group-item list-group-item-action ${urgentClass}" 
                         style="cursor: pointer; transition: all 0.2s;"
                         onclick="handleNotificationClick('${notification.action}')"
                         onmouseover="this.style.backgroundColor='#f8f9fa'"
                         onmouseout="this.style.backgroundColor='white'">
                        <div class="d-flex w-100 align-items-start">
                            <div class="me-3">
                                <div class="rounded-circle bg-${notification.iconColor} bg-opacity-10 p-3">
                                    <i class="fas ${notification.icon} fa-lg text-${notification.iconColor}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            ${notification.urgent ? '<i class="fas fa-exclamation-circle text-danger me-1"></i>' : ''}
                                            ${notification.title}
                                        </h6>
                                        <span class="badge bg-${notification.badgeColor}">${notification.badge}</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>${timeAgo}
                                    </small>
                                </div>
                                <p class="mb-0 text-muted">${notification.message}</p>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            document.getElementById('notificationsList').innerHTML = html;
        }

        function filterNotifications(filter) {
            currentNotificationFilter = filter;
            
            // Update active button
            document.querySelectorAll('[data-filter]').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
            
            // Display filtered results
            displayNotifications();
        }

        function handleNotificationClick(action) {
            // Chuyển đến section tương ứng
            const sectionMap = {
                'registrations': 'registrations',
                'feedback': 'feedback',
                'maintenance': 'maintenance',
                'payments': 'payments',
                'utilities': 'utilities',
                'students': 'students',
                'activity': 'dashboard'
            };
            
            const section = sectionMap[action] || 'dashboard';
            
            // Trigger navigation
            document.querySelector(`[data-section="${section}"]`).click();
        }

        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return 'Vừa xong';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' phút trước';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' giờ trước';
            if (seconds < 604800) return Math.floor(seconds / 86400) + ' ngày trước';
            
            return date.toLocaleDateString('vi-VN');
        }

        function loadReportsSection() {
            dynamicContent.innerHTML = `
                <div class="reports-section">
                    <!-- Filter Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Lọc báo cáo</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Từ ngày</label>
                                    <input type="date" id="reportStartDate" class="form-control" value="${new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0]}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Đến ngày</label>
                                    <input type="date" id="reportEndDate" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Khoảng thời gian</label>
                                    <select id="reportPeriod" class="form-select">
                                        <option value="custom">Tùy chọn</option>
                                        <option value="this_month">Tháng này</option>
                                        <option value="last_month">Tháng trước</option>
                                        <option value="this_year">Năm nay</option>
                                        <option value="last_year">Năm trước</option>
                                        <option value="last_3_months">3 tháng gần đây</option>
                                        <option value="last_6_months">6 tháng gần đây</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary" onclick="loadReportsData()">
                                        <i class="fas fa-search me-2"></i>Xem báo cáo
                                    </button>
                                    <button class="btn btn-success" onclick="exportReport()">
                                        <i class="fas fa-file-excel me-2"></i>Xuất Excel
                                    </button>
                                    <button class="btn btn-danger" onclick="printReport()">
                                        <i class="fas fa-print me-2"></i>In báo cáo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4" id="reportSummaryCards">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                            <p>Đang tải dữ liệu...</p>
                        </div>
                    </div>

                    <!-- Charts Section (only registration status) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Trạng thái đăng ký phòng</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="registrationStatusChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Details -->
                    <div class="row" id="detailedStatsSection">
                        <!-- Sẽ được load động -->
                    </div>
                </div>
            `;
            
            // Initialize charts
            initializeReportCharts();
            // Load default data
            loadReportsData();
        }

        // Initialize report charts
        function initializeReportCharts() {
            // Registration Status Chart
            const regStatusCtx = document.getElementById('registrationStatusChart')?.getContext('2d');
            if (regStatusCtx) {
                window.registrationStatusChart = new Chart(regStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Chờ duyệt', 'Đã duyệt', 'Đang ở', 'Đã nhận phòng', 'Từ chối'],
                        datasets: [{
                            data: [0, 0, 0, 0, 0],
                            backgroundColor: [
                                '#ffc107', // pending - Vàng
                                '#17a2b8', // approved - Xanh dương
                                '#28a745', // active - Xanh lá
                                '#007bff', // checked_in - Xanh đậm
                                '#dc3545'  // rejected - Đỏ
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }

        // Load reports data
        async function loadReportsData() {
            const startDate = document.getElementById('reportStartDate').value;
            const endDate = document.getElementById('reportEndDate').value;
            
            try {
                const response = await fetch(`../../api/admin/stats.php?action=detailed&start_date=${startDate}&end_date=${endDate}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    const data = result.data;
                    
                    // Update summary cards
                    updateSummaryCards(data);
                    
                    // Update charts
                    updateCharts(data);
                    
                    // Update detailed stats
                    updateDetailedStats(data);
                } else {
                    alert('Không thể tải dữ liệu báo cáo');
                }
            } catch (error) {
                console.error('Error loading reports:', error);
                alert('Lỗi khi tải dữ liệu báo cáo');
            }
        }

        // Update summary cards
        function updateSummaryCards(data) {
            const cardsHtml = `
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <i class="fas fa-dollar-sign stat-icon mb-2"></i>
                            <h3>${formatCurrency(data.revenue?.total_revenue || 0)}</h3>
                            <p class="mb-0">Tổng doanh thu</p>
                            <small class="text-white-50">${data.revenue?.total_payments || 0} thanh toán</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-center">
                            <i class="fas fa-user-plus stat-icon mb-2"></i>
                            <h3>${data.registrations?.total || 0}</h3>
                            <p class="mb-0">Đăng ký phòng</p>
                            <small class="text-white-50">${data.registrations?.active || 0} đang ở</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-center">
                            <i class="fas fa-bolt stat-icon mb-2"></i>
                            <h3>${formatCurrency(data.utilities?.total_cost || 0)}</h3>
                            <p class="mb-0">Điện nước</p>
                            <small class="text-white-50">${data.utilities?.unpaid_amount ? 'Còn nợ: ' + formatCurrency(data.utilities.unpaid_amount) : 'Đã thanh toán đủ'}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body text-center">
                            <i class="fas fa-wrench stat-icon mb-2"></i>
                            <h3>${data.maintenance?.total_requests || 0}</h3>
                            <p class="mb-0">Yêu cầu bảo trì</p>
                            <small class="text-white-50">${data.maintenance?.urgent || 0} khẩn cấp</small>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('reportSummaryCards').innerHTML = cardsHtml;
        }

        // Update charts
        function updateCharts(data) {
            // Registration Status Chart
            if (window.registrationStatusChart && data.registrations) {
                window.registrationStatusChart.data.datasets[0].data = [
                    parseInt(data.registrations.pending || 0),      // Chờ duyệt
                    parseInt(data.registrations.approved || 0),     // Đã duyệt
                    parseInt(data.registrations.active || 0),       // Đang ở
                    parseInt(data.registrations.checked_in || 0),   // Đã nhận phòng
                    parseInt(data.registrations.rejected || 0)      // Từ chối
                ];
                window.registrationStatusChart.update();
            }
        }


        // Update detailed stats
        function updateDetailedStats(data) {
            const statsHtml = `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Chi tiết doanh thu</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Tiền phòng:</td>
                                    <td class="text-end"><strong>${formatCurrency(data.revenue?.room_revenue || 0)}</strong></td>
                                </tr>
                                <tr>
                                    <td>Điện nước:</td>
                                    <td class="text-end"><strong>${formatCurrency(data.revenue?.utility_revenue || 0)}</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong>${formatCurrency(data.revenue?.total_revenue || 0)}</strong></td>
                                </tr>
                                <tr>
                                    <td>Trung bình/thanh toán:</td>
                                    <td class="text-end">${formatCurrency(data.revenue?.avg_payment || 0)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Thống kê sinh viên</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Tổng số sinh viên:</td>
                                    <td class="text-end"><strong>${data.students?.total || 0}</strong></td>
                                </tr>
                                <tr>
                                    <td>Nam:</td>
                                    <td class="text-end">${data.students?.male || 0}</td>
                                </tr>
                                <tr>
                                    <td>Nữ:</td>
                                    <td class="text-end">${data.students?.female || 0}</td>
                                </tr> 
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Điện nước</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Tổng chỉ số điện:</td>
                                    <td class="text-end"><strong>${formatNumber(data.utilities?.total_electricity || 0)} kWh</strong></td>
                                </tr>
                                <tr>
                                    <td>Tổng chỉ số nước:</td>
                                    <td class="text-end"><strong>${formatNumber(data.utilities?.total_water || 0)} m³</strong></td>
                                </tr>
                                <tr>
                                    <td>Tổng chi phí:</td>
                                    <td class="text-end"><strong>${formatCurrency(data.utilities?.total_cost || 0)}</strong></td>
                                </tr>
                                <tr>
                                    <td>Đã thanh toán:</td>
                                    <td class="text-end text-success">${formatCurrency(data.utilities?.paid_amount || 0)}</td>
                                </tr>
                                <tr>
                                    <td>Chưa thanh toán:</td>
                                    <td class="text-end text-danger">${formatCurrency(data.utilities?.unpaid_amount || 0)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Phản hồi & Bảo trì</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Yêu cầu bảo trì:</td>
                                    <td class="text-end"><strong>${data.maintenance?.total_requests || 0}</strong></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;- Đang chờ:</td>
                                    <td class="text-end">${data.maintenance?.pending || 0}</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;- Khẩn cấp:</td>
                                    <td class="text-end text-danger">${data.maintenance?.urgent || 0}</td>
                                </tr>
                                <tr>
                                    <td>Phản hồi:</td>
                                    <td class="text-end"><strong>${data.feedback?.total || 0}</strong></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;- Chưa xử lý:</td>
                                    <td class="text-end text-warning">${data.feedback?.new || 0}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detailedStatsSection').innerHTML = statsHtml;
        }

        // Format number
        function formatNumber(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }

        // Export report to Excel (placeholder)
        function exportReport() {
            alert('Tính năng xuất Excel đang được phát triển. Vui lòng sử dụng chức năng in để lưu PDF.');
        }

        // Print report
        function printReport() {
            window.print();
        }

        // Handle period change
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'reportPeriod') {
                const period = e.target.value;
                const today = new Date();
                let startDate, endDate;
                
                switch (period) {
                    case 'this_month':
                        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        endDate = today;
                        break;
                    case 'last_month':
                        startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'this_year':
                        startDate = new Date(today.getFullYear(), 0, 1);
                        endDate = today;
                        break;
                    case 'last_year':
                        startDate = new Date(today.getFullYear() - 1, 0, 1);
                        endDate = new Date(today.getFullYear() - 1, 11, 31);
                        break;
                    case 'last_3_months':
                        startDate = new Date(today.getFullYear(), today.getMonth() - 3, 1);
                        endDate = today;
                        break;
                    case 'last_6_months':
                        startDate = new Date(today.getFullYear(), today.getMonth() - 6, 1);
                        endDate = today;
                        break;
                    default:
                        return; // Custom, don't change dates
                }
                
                document.getElementById('reportStartDate').value = startDate.toISOString().split('T')[0];
                document.getElementById('reportEndDate').value = endDate.toISOString().split('T')[0];
                
                // Auto load data if not custom
                if (period !== 'custom') {
                    loadReportsData();
                }
            }
        });

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('vi-VN');
        }

        function getPaymentType(type) {
            const types = {
                'room_fee': 'Tiền phòng',
                'utility': 'Điện nước',
                'deposit': 'Tiền cọc',
                'penalty': 'Tiền phạt'
            };
            return types[type] || type;
        }

        function getPaymentMethod(method) {
            const methods = {
                'cash': 'Tiền mặt',
                'bank_transfer': 'Chuyển khoản',
                'card': 'Thẻ'
            };
            return methods[method] || method;
        }

        function getPaymentStatus(status) {
            const statuses = {
                'completed': 'Hoàn thành',
                'pending': 'Chờ xử lý',
                'failed': 'Thất bại'
            };
            return statuses[status] || status;
        }

        async function approveReg(id) {
            if (!confirm('Bạn có chắc muốn duyệt đăng ký này?')) {
                return;
            }

            try {
                const response = await fetch(`../../api/registrations.php?id=${id}&action=approve`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Duyệt đăng ký thành công!');
                    loadRegistrationsData();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể duyệt đăng ký'));
                }
            } catch (error) {
                console.error('Error approving registration:', error);
                alert('Lỗi khi duyệt đăng ký');
            }
        }

        async function rejectReg(id) {
            const reason = prompt('Vui lòng nhập lý do từ chối:');
            
            if (reason === null) return; // User cancelled
            if (!reason || reason.trim() === '') {
                alert('Vui lòng nhập lý do từ chối!');
                return;
            }

            try {
                const response = await fetch(`../../api/registrations.php?id=${id}&action=reject`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason.trim() })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Từ chối đăng ký thành công!');
                    loadRegistrationsData();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể từ chối đăng ký'));
                }
            } catch (error) {
                console.error('Error rejecting registration:', error);
                alert('Lỗi khi từ chối đăng ký');
            }
        }
    </script>
</body>
</html>
