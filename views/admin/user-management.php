<?php
session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .badge {
            padding: 5px 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .role-admin { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .role-staff { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .role-student { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            margin: 10px 0;
        }
        .btn-action {
            padding: 5px 10px;
            margin: 2px;
        }
        .search-box {
            max-width: 400px;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Main Content -->
            <div class="col-12">
                <div class="main-content" style="max-width: 1400px; margin: 0 auto;">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center">
                            <a href="dashboard.php" class="btn btn-outline-primary me-3">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
                            </a>
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2 text-primary"></i>
                                Quản lý Người dùng
                            </h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong></span>
                        </div>
                    </div>
                </nav>
                
                <!-- Statistics Cards -->
                <div class="row mb-4" id="statsContainer">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 id="totalUsers">0</h3>
                            <p class="mb-0">Tổng người dùng</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-user-shield fa-2x mb-2"></i>
                            <h3 id="totalAdmins">0</h3>
                            <p class="mb-0">Admin</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-user-tie fa-2x mb-2"></i>
                            <h3 id="totalStaff">0</h3>
                            <p class="mb-0">Cán bộ</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-user-graduate fa-2x mb-2"></i>
                            <h3 id="totalStudents">0</h3>
                            <p class="mb-0">Sinh viên</p>
                        </div>
                    </div>
                </div>
                
                <!-- Filter and Search -->
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">Vai trò:</label>
                                <select class="form-select" id="filterRole" onchange="loadUsers()">
                                    <option value="">Tất cả</option>
                                    <option value="admin">Admin</option>
                                    <option value="staff">Cán bộ</option>
                                    <option value="student">Sinh viên</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tìm kiếm:</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="searchInput" 
                                           placeholder="Tìm theo username, email, họ tên..."
                                           onkeyup="handleSearch()">
                                    <button class="btn btn-primary" onclick="loadUsers()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-success w-100" onclick="showCreateUserModal()">
                                    <i class="fas fa-plus me-2"></i>Thêm người dùng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Danh sách người dùng
                        </h5>
                    </div>
                    <div class="card-body">
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
                                        <th>SĐT</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav id="paginationContainer"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">
                        <i class="fas fa-user-plus me-2"></i>Thêm người dùng mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" required>
                                    <option value="">-- Chọn vai trò --</option>
                                    <option value="admin">Admin</option>
                                    <option value="staff">Cán bộ</option>
                                    <option value="student">Sinh viên</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="passwordGroup">
                                <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password">
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Nếu chọn vai trò "Sinh viên", cần thêm thông tin sinh viên sau khi tạo tài khoản.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">
                        <i class="fas fa-save me-2"></i>Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>Đặt lại mật khẩu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="resetUserId">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="newPassword" required>
                        <small class="text-muted">Tối thiểu 6 ký tự</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="resetPassword()">
                        <i class="fas fa-check me-2"></i>Đặt lại mật khẩu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let searchTimeout = null;

        // Load statistics
        async function loadStats() {
            try {
                const response = await fetch('../../api/admin/users.php?action=stats');
                const data = await response.json();
                
                if (data.success) {
                    let totalUsers = 0;
                    let admins = 0, staff = 0, students = 0;
                    
                    data.data.forEach(stat => {
                        totalUsers += parseInt(stat.total);
                        if (stat.role === 'admin') admins = stat.total;
                        if (stat.role === 'staff') staff = stat.total;
                        if (stat.role === 'student') students = stat.total;
                    });
                    
                    document.getElementById('totalUsers').textContent = totalUsers;
                    document.getElementById('totalAdmins').textContent = admins;
                    document.getElementById('totalStaff').textContent = staff;
                    document.getElementById('totalStudents').textContent = students;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load users
        async function loadUsers(page = 1) {
            try {
                currentPage = page;
                const role = document.getElementById('filterRole').value;
                const search = document.getElementById('searchInput').value;
                
                let url = `../../api/admin/users.php?page=${page}`;
                if (role) url += `&role=${role}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success) {
                    displayUsers(data.data);
                    displayPagination(data.pagination);
                } else {
                    showError('Không thể tải danh sách người dùng');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showError('Lỗi khi tải dữ liệu');
            }
        }

        // Display users in table
        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            
            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không tìm thấy người dùng nào</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = users.map(user => {
                const roleClass = `role-${user.role}`;
                const roleName = {
                    'admin': 'Admin',
                    'staff': 'Cán bộ',
                    'student': 'Sinh viên'
                }[user.role];
                
                const statusBadge = user.is_active 
                    ? '<span class="badge bg-success">Hoạt động</span>'
                    : '<span class="badge bg-danger">Vô hiệu hóa</span>';
                
                const initials = user.full_name.split(' ').map(n => n[0]).join('').substring(0, 2);
                
                return `
                    <tr>
                        <td>${user.id}</td>
                        <td>
                            <div class="user-avatar ${roleClass}">
                                ${initials}
                            </div>
                        </td>
                        <td><strong>${user.username}</strong></td>
                        <td>${user.full_name}</td>
                        <td>${user.email}</td>
                        <td><span class="badge bg-primary">${roleName}</span></td>
                        <td>${user.phone || '-'}</td>
                        <td>${statusBadge}</td>
                        <td>${formatDate(user.created_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-info btn-action" 
                                    onclick="showEditUserModal(${user.id})"
                                    title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-warning btn-action" 
                                    onclick="showResetPasswordModal(${user.id})"
                                    title="Đặt lại mật khẩu">
                                <i class="fas fa-key"></i>
                            </button>
                            <button class="btn btn-sm ${user.is_active ? 'btn-secondary' : 'btn-success'} btn-action" 
                                    onclick="toggleUserStatus(${user.id})"
                                    title="${user.is_active ? 'Vô hiệu hóa' : 'Kích hoạt'}">
                                <i class="fas fa-${user.is_active ? 'ban' : 'check'}"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-action" 
                                    onclick="deleteUser(${user.id}, '${user.username}')"
                                    title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Display pagination
        function displayPagination(pagination) {
            const container = document.getElementById('paginationContainer');
            
            if (pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<ul class="pagination justify-content-center">';
            
            // Previous button
            html += `
                <li class="page-item ${pagination.page <= 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadUsers(${pagination.page - 1}); return false;">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            `;
            
            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.page - 2 && i <= pagination.page + 2)) {
                    html += `
                        <li class="page-item ${i === pagination.page ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="loadUsers(${i}); return false;">${i}</a>
                        </li>
                    `;
                } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            
            // Next button
            html += `
                <li class="page-item ${pagination.page >= pagination.total_pages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadUsers(${pagination.page + 1}); return false;">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            `;
            
            html += '</ul>';
            container.innerHTML = html;
        }

        // Show create user modal
        function showCreateUserModal() {
            document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Thêm người dùng mới';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordGroup').style.display = 'block';
            document.getElementById('password').required = true;
            
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }

        // Show edit user modal
        async function showEditUserModal(userId) {
            try {
                const response = await fetch(`../../api/admin/users.php?id=${userId}`);
                const data = await response.json();
                
                if (data.success) {
                    const user = data.data;
                    document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Chỉnh sửa người dùng';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('username').value = user.username;
                    document.getElementById('username').disabled = true;
                    document.getElementById('email').value = user.email;
                    document.getElementById('fullName').value = user.full_name;
                    document.getElementById('phone').value = user.phone || '';
                    document.getElementById('role').value = user.role;
                    document.getElementById('role').disabled = true;
                    document.getElementById('passwordGroup').style.display = 'none';
                    document.getElementById('password').required = false;
                    
                    const modal = new bootstrap.Modal(document.getElementById('userModal'));
                    modal.show();
                } else {
                    showError('Không thể tải thông tin người dùng');
                }
            } catch (error) {
                console.error('Error loading user:', error);
                showError('Lỗi khi tải dữ liệu');
            }
        }

        // Save user (create or update)
        async function saveUser() {
            const userId = document.getElementById('userId').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const fullName = document.getElementById('fullName').value;
            const phone = document.getElementById('phone').value;
            const role = document.getElementById('role').value;
            const password = document.getElementById('password').value;
            
            // Validation
            if (!username || !email || !fullName || !role) {
                showError('Vui lòng điền đầy đủ thông tin bắt buộc');
                return;
            }
            
            if (!userId && !password) {
                showError('Vui lòng nhập mật khẩu');
                return;
            }
            
            if (password && password.length < 6) {
                showError('Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }
            
            const userData = {
                id: userId || null,
                username: username,
                email: email,
                full_name: fullName,
                phone: phone || '',  // Đảm bảo phone luôn là string, không phải null
                role: role
            };
            
            if (!userId) {
                userData.password = password;
            }
            
            // Debug: log data trước khi gửi
            console.log('Sending user data:', userData);
            
            try {
                const method = userId ? 'PUT' : 'POST';
                const response = await fetch('../../api/admin/users.php', {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });
                
                // Debug: log response
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    showSuccess(userId ? 'Cập nhật thành công' : 'Tạo người dùng thành công');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    modal.hide();
                    loadUsers(currentPage);
                    loadStats();
                    
                    // Re-enable fields
                    document.getElementById('username').disabled = false;
                    document.getElementById('role').disabled = false;
                } else {
                    console.error('API Error:', data.error);
                    showError(data.error || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error saving user:', error);
                showError('Lỗi khi lưu dữ liệu');
            }
        }

        // Show reset password modal
        function showResetPasswordModal(userId) {
            document.getElementById('resetUserId').value = userId;
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        }

        // Reset password
        async function resetPassword() {
            const userId = document.getElementById('resetUserId').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!newPassword || newPassword.length < 6) {
                showError('Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showError('Mật khẩu xác nhận không khớp');
                return;
            }
            
            try {
                const response = await fetch('../../api/admin/users.php?action=reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: userId,
                        new_password: newPassword
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Đặt lại mật khẩu thành công');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                    modal.hide();
                } else {
                    showError(data.error || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error resetting password:', error);
                showError('Lỗi khi đặt lại mật khẩu');
            }
        }

        // Toggle user status
        async function toggleUserStatus(userId) {
            if (!confirm('Bạn có chắc muốn thay đổi trạng thái người dùng này?')) {
                return;
            }
            
            try {
                const response = await fetch('../../api/admin/users.php?action=toggle-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: userId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Cập nhật trạng thái thành công');
                    loadUsers(currentPage);
                    loadStats();
                } else {
                    showError(data.error || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error toggling status:', error);
                showError('Lỗi khi cập nhật trạng thái');
            }
        }

        // Delete user
        async function deleteUser(userId, username) {
            if (!confirm(`Bạn có chắc muốn xóa người dùng "${username}"?\n\nLưu ý: Hành động này không thể hoàn tác!`)) {
                return;
            }
            
            try {
                const response = await fetch(`../../api/admin/users.php?id=${userId}`, {
                    method: 'DELETE'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Xóa người dùng thành công');
                    loadUsers(currentPage);
                    loadStats();
                } else {
                    showError(data.error || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                showError('Lỗi khi xóa người dùng');
            }
        }

        // Handle search with debounce
        function handleSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadUsers(1);
            }, 500);
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        // Show success message
        function showSuccess(message) {
            alert('✅ ' + message);
        }

        // Show error message
        function showError(message) {
            alert('❌ ' + message);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadUsers();
        });
    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

