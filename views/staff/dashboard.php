<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cán bộ - Hệ thống quản lý ký túc xá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
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
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
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
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
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
                        <i class="fas fa-user-shield me-2"></i>
                        Cán bộ
                    </h4>
                </div>
                
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="#" data-section="dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="#" data-section="registrations">
                        <i class="fas fa-clipboard-check me-2"></i>Duyệt đăng ký
                    </a>
                    <a class="nav-link" href="#" data-section="utilities">
                        <i class="fas fa-tint me-2"></i>Nhập chỉ số
                    </a>
                    <a class="nav-link" href="#" data-section="maintenance">
                        <i class="fas fa-wrench me-2"></i>Yêu cầu bảo trì
                    </a>
                    <a class="nav-link" href="#" data-section="payments">
                        <i class="fas fa-credit-card me-2"></i>Thanh toán
                    </a>
                    <a class="nav-link" href="#" data-section="equipment">
                        <i class="fas fa-boxes me-2"></i>Quản lý thiết bị
                    </a>
                    <a class="nav-link" href="#" data-section="notifications">
                        <i class="fas fa-bullhorn me-2"></i>Thông báo
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
                            <span class="me-3">Xin chào, <strong id="userName">Cán bộ</strong></span>
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
                                    <i class="fas fa-clipboard-list stat-icon mb-2"></i>
                                    <h3 id="pendingRegistrations">0</h3>
                                    <p class="mb-0">Đăng ký chờ duyệt</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-wrench stat-icon mb-2"></i>
                                    <h3 id="pendingMaintenance">0</h3>
                                    <p class="mb-0">Yêu cầu bảo trì</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-circle stat-icon mb-2"></i>
                                    <h3 id="pendingFeedback">0</h3>
                                    <p class="mb-0">Phản hồi mới</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card stat-icon mb-2"></i>
                                    <h3 id="pendingPayments">0</h3>
                                    <p class="mb-0">Thanh toán chờ xử lý</p>
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
                                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Nhiệm vụ cần xử lý</h5>
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
        });

        // Check authentication
        async function checkAuth() {
            try {
                const response = await fetch('../../api/auth.php?action=check');
                const data = await response.json();
                
                if (data.authenticated && data.user.role === 'staff') {
                    currentUser = data.user;
                    document.getElementById('userName').textContent = currentUser.full_name;
                } else {
                    window.location.href = 'auth/login.php';
                }
            } catch (error) {
                console.error('Auth check error:', error);
                window.location.href = 'auth/login.php';
            }
        }

        // Load dashboard data
        async function loadDashboardData() {
            try {
                // Load statistics
                const statsResponse = await fetch('../../api/admin/pending-tasks.php');
                const stats = await statsResponse.json();
                
                if (stats.success) {
                    document.getElementById('pendingRegistrations').textContent = stats.registration_count || 0;
                    document.getElementById('pendingMaintenance').textContent = stats.maintenance_count || 0;
                    document.getElementById('pendingFeedback').textContent = stats.feedback_count || 0;
                    document.getElementById('pendingPayments').textContent = stats.payment_count || 0;
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
                    container.innerHTML = data.activities.slice(0, 5).map(activity => `
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div>${activity.description}</div>
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
                    container.innerHTML = data.tasks.slice(0, 5).map(task => `
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-circle text-warning" style="font-size: 8px;"></i>
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
                case 'registrations':
                    pageTitle.textContent = 'Duyệt đăng ký phòng';
                    loadRegistrationsSection();
                    break;
                case 'utilities':
                    pageTitle.textContent = 'Nhập chỉ số điện nước';
                    loadUtilitiesSection();
                    break;
                case 'maintenance':
                    pageTitle.textContent = 'Quản lý bảo trì';
                    loadMaintenanceSection();
                    break;
                case 'payments':
                    pageTitle.textContent = 'Thanh toán';
                    loadPaymentsSection();
                    break;
                case 'equipment':
                    pageTitle.textContent = 'Quản lý thiết bị';
                    loadEquipmentSection();
                    break;
                case 'notifications':
                    pageTitle.textContent = 'Thông báo';
                    loadNotificationsSection();
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
        function loadRegistrationsSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Đăng ký phòng chờ duyệt</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã SV</th>
                                        <th>Họ tên</th>
                                        <th>Phòng đăng ký</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
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

        function loadUtilitiesSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nhập chỉ số mới</h6>
                            </div>
                            <div class="card-body">
                                <form id="utilityReadingForm">
                                    <div class="mb-3">
                                        <label class="form-label">Tòa nhà</label>
                                        <select class="form-select" id="utilitiesBuildingSelect" required>
                                            <option value="">Chọn tòa nhà...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phòng</label>
                                        <select class="form-select" id="utilitiesRoomSelect" required>
                                            <option value="">Chọn tòa nhà trước</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ngày đọc</label>
                                        <input type="date" class="form-control" id="readingDate" value="${new Date().toISOString().split('T')[0]}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Chỉ số điện (kWh)</label>
                                        <input type="number" class="form-control" id="electricityReading" min="0" step="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Chỉ số nước (m³)</label>
                                        <input type="number" class="form-control" id="waterReading" min="0" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Đơn giá điện (VNĐ/kWh)</label>
                                        <input type="number" class="form-control" id="electricityRate" value="2500" min="0" step="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Đơn giá nước (VNĐ/m³)</label>
                                        <input type="number" class="form-control" id="waterRate" value="15000" min="0" step="1000" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Lưu chỉ số
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử chỉ số</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Phòng</th>
                                                <th>Điện (kWh)</th>
                                                <th>Nước (m³)</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="utilityHistoryTable">
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
                    </div>
                </div>
            `;
            
            loadBuildingsForUtilities();
            loadUtilityHistory();
            
            // Listen to building change
            setTimeout(() => {
                const buildingSelect = document.getElementById('utilitiesBuildingSelect');
                const roomSelect = document.getElementById('utilitiesRoomSelect');
                if (buildingSelect) {
                    buildingSelect.addEventListener('change', function() {
                        loadRoomsForUtilities(this.value, roomSelect);
                    });
                }
                
                // Form submission
                const form = document.getElementById('utilityReadingForm');
                if (form) {
                    form.addEventListener('submit', submitUtilityReading);
                }
            }, 100);
        }
        
        async function loadBuildingsForUtilities() {
            try {
                const response = await fetch('../../api/buildings.php');
                const data = await response.json();
                
                const select = document.getElementById('utilitiesBuildingSelect');
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
        
        async function loadRoomsForUtilities(buildingId, selectElement) {
            if (!buildingId) {
                selectElement.innerHTML = '<option value="">Chọn tòa nhà trước</option>';
                return;
            }
            
            try {
                const response = await fetch(`../../api/rooms.php?building_id=${buildingId}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    selectElement.innerHTML = '<option value="">Chọn phòng...</option>' + 
                        data.data.map(room => 
                            `<option value="${room.id}">${room.room_number}</option>`
                        ).join('');
                } else {
                    selectElement.innerHTML = '<option value="">Không có phòng</option>';
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                selectElement.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            }
        }
        
        async function submitUtilityReading(e) {
            e.preventDefault();
            
            const roomId = document.getElementById('utilitiesRoomSelect').value;
            const readingDate = document.getElementById('readingDate').value;
            const electricityReading = document.getElementById('electricityReading').value;
            const waterReading = document.getElementById('waterReading').value;
            const electricityRate = document.getElementById('electricityRate').value;
            const waterRate = document.getElementById('waterRate').value;
            
            if (!roomId || !readingDate || !electricityReading || !waterReading) {
                alert('Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            try {
                const response = await fetch('../../api/utilities.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_id: roomId,
                        reading_date: readingDate,
                        electricity_reading: electricityReading,
                        water_reading: waterReading,
                        electricity_rate: electricityRate,
                        water_rate: waterRate
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Nhập chỉ số thành công!');
                    document.getElementById('utilityReadingForm').reset();
                    loadUtilityHistory();
                } else {
                    alert('Lỗi: ' + (result.error || 'Nhập chỉ số thất bại'));
                }
            } catch (error) {
                console.error('Error submitting utility reading:', error);
                alert('Có lỗi xảy ra khi nhập chỉ số');
            }
        }
        
        async function loadUtilityHistory() {
            try {
                const response = await fetch('../../api/utilities.php');
                const data = await response.json();
                
                const tbody = document.getElementById('utilityHistoryTable');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(reading => {
                        const statusBadge = reading.is_paid 
                            ? '<span class="badge bg-success">Đã thanh toán</span>'
                            : '<span class="badge bg-warning">Chưa thanh toán</span>';
                        
                        return `
                            <tr>
                                <td>${formatDate(reading.reading_date)}</td>
                                <td>${reading.room_number}</td>
                                <td>${reading.electricity_reading}</td>
                                <td>${reading.water_reading}</td>
                                <td>${formatCurrency(reading.total_amount)}</td>
                                <td>${statusBadge}</td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading utility history:', error);
                document.getElementById('utilityHistoryTable').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }
        
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND' 
            }).format(amount);
        }

        function loadMaintenanceSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wrench me-2"></i>Yêu cầu bảo trì</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Chức năng này đang được phát triển...</p>
                    </div>
                </div>
            `;
        }

        function loadPaymentsSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Ghi nhận thanh toán</h6>
                            </div>
                            <div class="card-body">
                                <form id="paymentForm">
                                    <div class="mb-3">
                                        <label class="form-label">Sinh viên</label>
                                        <select class="form-select" id="paymentStudentSelect" required>
                                            <option value="">Chọn sinh viên...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Loại thanh toán</label>
                                        <select class="form-select" id="paymentType" required>
                                            <option value="room_fee">Tiền phòng</option>
                                            <option value="utility">Tiền điện nước</option>
                                            <option value="deposit">Tiền cọc</option>
                                            <option value="penalty">Tiền phạt</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Số tiền (VNĐ)</label>
                                        <input type="number" class="form-control" id="paymentAmount" min="0" step="1000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ngày thanh toán</label>
                                        <input type="date" class="form-control" id="paymentDate" value="${new Date().toISOString().split('T')[0]}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phương thức thanh toán</label>
                                        <select class="form-select" id="paymentMethod" required>
                                            <option value="cash">Tiền mặt</option>
                                            <option value="bank_transfer">Chuyển khoản</option>
                                            <option value="card">Thẻ</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Số tham chiếu (nếu có)</label>
                                        <input type="text" class="form-control" id="referenceNumber" placeholder="Mã giao dịch, số thẻ...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ghi chú</label>
                                        <textarea class="form-control" id="paymentNotes" rows="2" placeholder="Ghi chú về thanh toán..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check-circle me-2"></i>Xác nhận thanh toán
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lịch sử thanh toán</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Lọc theo sinh viên</label>
                                    <select class="form-select" id="filterStudentForPayments">
                                        <option value="">Tất cả sinh viên</option>
                                    </select>
                                </div>
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Sinh viên</th>
                                                <th>Loại</th>
                                                <th>Số tiền</th>
                                                <th>Phương thức</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paymentHistoryTable">
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
                        
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Thống kê thanh toán</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h3 class="text-success" id="totalPayments">0 VNĐ</h3>
                                        <p class="text-muted mb-0">Tổng đã thu</p>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="text-danger" id="pendingPayments">0 VNĐ</h3>
                                        <p class="text-muted mb-0">Còn thiếu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            loadStudentsForPayment();
            loadPaymentHistory();
            
            // Listen to form submission and filter change
            setTimeout(() => {
                const form = document.getElementById('paymentForm');
                if (form) {
                    form.addEventListener('submit', submitPayment);
                }
                
                const filterSelect = document.getElementById('filterStudentForPayments');
                if (filterSelect) {
                    filterSelect.addEventListener('change', loadPaymentHistory);
                }
            }, 100);
        }
        
        async function loadStudentsForPayment() {
            try {
                const response = await fetch('../../api/students.php');
                const data = await response.json();
                
                const select = document.getElementById('paymentStudentSelect');
                const filterSelect = document.getElementById('filterStudentForPayments');
                
                if (data.success && data.data) {
                    const options = '<option value="">Chọn sinh viên...</option>' + 
                        data.data.map(student => 
                            `<option value="${student.id}">${student.student_code} - ${student.full_name}</option>`
                        ).join('');
                    
                    select.innerHTML = options;
                    
                    if (filterSelect) {
                        filterSelect.innerHTML = '<option value="">Tất cả sinh viên</option>' + 
                            data.data.map(student => 
                                `<option value="${student.id}">${student.student_code} - ${student.full_name}</option>`
                            ).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading students:', error);
            }
        }
        
        async function submitPayment(e) {
            e.preventDefault();
            
            const studentId = document.getElementById('paymentStudentSelect').value;
            const paymentType = document.getElementById('paymentType').value;
            const amount = document.getElementById('paymentAmount').value;
            const paymentDate = document.getElementById('paymentDate').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const referenceNumber = document.getElementById('referenceNumber').value;
            const notes = document.getElementById('paymentNotes').value;
            
            if (!studentId || !amount || !paymentDate) {
                alert('Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            try {
                const response = await fetch('../../api/payments.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        payment_type: paymentType,
                        amount: amount,
                        payment_date: paymentDate,
                        payment_method: paymentMethod,
                        reference_number: referenceNumber,
                        notes: notes,
                        status: 'completed'
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Ghi nhận thanh toán thành công!');
                    document.getElementById('paymentForm').reset();
                    loadPaymentHistory();
                    loadPaymentStats();
                } else {
                    alert('Lỗi: ' + (result.error || 'Thanh toán thất bại'));
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                alert('Có lỗi xảy ra khi ghi nhận thanh toán');
            }
        }
        
        async function loadPaymentHistory() {
            try {
                const studentFilter = document.getElementById('filterStudentForPayments')?.value || '';
                const url = studentFilter 
                    ? `../../api/payments.php?student_id=${studentFilter}`
                    : '../../api/payments.php';
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('paymentHistoryTable');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(payment => {
                        const typeLabels = {
                            'room_fee': '<span class="badge bg-primary">Tiền phòng</span>',
                            'utility': '<span class="badge bg-info">Điện nước</span>',
                            'deposit': '<span class="badge bg-warning">Tiền cọc</span>',
                            'penalty': '<span class="badge bg-danger">Tiền phạt</span>'
                        };
                        
                        const methodLabels = {
                            'cash': '<span class="text-success">Tiền mặt</span>',
                            'bank_transfer': '<span class="text-info">Chuyển khoản</span>',
                            'card': '<span class="text-primary">Thẻ</span>'
                        };
                        
                        const statusBadge = payment.status === 'completed' 
                            ? '<span class="badge bg-success">Hoàn thành</span>'
                            : '<span class="badge bg-warning">Chờ xử lý</span>';
                        
                        return `
                            <tr>
                                <td>${formatDate(payment.payment_date)}</td>
                                <td>${payment.student_name || payment.student_code}</td>
                                <td>${typeLabels[payment.payment_type] || payment.payment_type}</td>
                                <td>${formatCurrency(payment.amount)}</td>
                                <td>${methodLabels[payment.payment_method] || payment.payment_method}</td>
                                <td>${statusBadge}</td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                document.getElementById('paymentHistoryTable').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }
        
        async function loadPaymentStats() {
            try {
                const response = await fetch('../../api/payments.php?stats=true');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('totalPayments').textContent = formatCurrency(data.total_paid || 0);
                    document.getElementById('pendingPayments').textContent = formatCurrency(data.pending_amount || 0);
                }
            } catch (error) {
                console.error('Error loading payment stats:', error);
            }
        }

        function loadEquipmentSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Quản lý thiết bị</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Chức năng này đang được phát triển...</p>
                    </div>
                </div>
            `;
        }

        function loadNotificationsSection() {
            document.getElementById('dynamicContent').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Thông báo</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Chức năng này đang được phát triển...</p>
                    </div>
                </div>
            `;
        }

        async function loadRegistrationsData() {
            try {
                const response = await fetch('../../api/registrations.php?status=pending');
                const data = await response.json();
                
                const tbody = document.getElementById('registrationsTableBody');
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(reg => `
                        <tr>
                            <td>${reg.student_code || 'N/A'}</td>
                            <td>${reg.student_name || 'N/A'}</td>
                            <td>${reg.room_number || 'N/A'}</td>
                            <td>${formatDate(reg.start_date)}</td>
                            <td>${formatDate(reg.end_date)}</td>
                            <td>
                                <button class="btn btn-sm btn-success me-1" onclick="approveRegistration(${reg.id})">
                                    <i class="fas fa-check"></i> Duyệt
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectRegistration(${reg.id})">
                                    <i class="fas fa-times"></i> Từ chối
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Không có đăng ký nào chờ duyệt</td></tr>';
                }
            } catch (error) {
                console.error('Error loading registrations:', error);
                document.getElementById('registrationsTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        function approveRegistration(id) {
            alert(`Duyệt đăng ký ${id} - Chức năng đang được phát triển`);
        }

        function rejectRegistration(id) {
            if (confirm('Bạn có chắc chắn muốn từ chối đăng ký này?')) {
                alert(`Từ chối đăng ký ${id} - Chức năng đang được phát triển`);
            }
        }

        // Utility functions
        function formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('vi-VN');
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('vi-VN');
        }
    </script>
</body>
</html>

