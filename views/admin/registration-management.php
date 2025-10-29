<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đăng ký - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-approved { background-color: #198754; }
        .badge-rejected { background-color: #dc3545; }
        .badge-active { background-color: #0dcaf0; color: #000; }
        .badge-completed { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-clipboard-list me-2"></i>Quản lý Đăng ký Phòng</h4>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" id="filterStatus" onchange="loadRegistrations()">
                            <option value="">Tất cả</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="completed">Đã hoàn thành</option>
                            <option value="rejected">Bị từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Tìm theo tên, mã SV..." onkeyup="loadRegistrations()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="filterStartDate" onchange="loadRegistrations()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="filterEndDate" onchange="loadRegistrations()">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách đăng ký</h5>
                <span class="badge bg-primary" id="totalCount">0 đăng ký</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Sinh viên</th>
                                <th>Phòng</th>
                                <th>Ngày đăng ký</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="registrationsTableBody">
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
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Từ chối đăng ký</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <input type="hidden" id="rejectRegistrationId">
                        <div class="mb-3">
                            <label class="form-label">Lý do từ chối *</label>
                            <textarea class="form-control" id="rejectReason" rows="3" 
                                      placeholder="Nhập lý do từ chối đăng ký..." required></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Hành động này không thể hoàn tác!
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">
                        <i class="fas fa-times me-2"></i>Từ chối
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentRegistrationId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadRegistrations();
        });

        async function loadRegistrations() {
            try {
                const status = document.getElementById('filterStatus').value;
                const search = document.getElementById('searchInput').value;
                
                let url = '../../api/registrations.php?page=1&limit=100';
                if (status) url += `&status=${status}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('registrationsTableBody');
                document.getElementById('totalCount').textContent = `${data.data?.length || 0} đăng ký`;
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(reg => `
                        <tr>
                            <td><strong>${reg.student_code || 'N/A'}</strong></td>
                            <td>
                                <div>${reg.student_name || 'N/A'}</div>
                                <small class="text-muted">${reg.student_email || ''}</small>
                            </td>
                            <td>
                                <strong>${reg.room_number}</strong><br>
                                <small class="text-muted">${reg.building_name}</small>
                            </td>
                            <td>${formatDate(reg.registration_date || reg.created_at)}</td>
                            <td>${formatDate(reg.start_date)}</td>
                            <td>${formatDate(reg.end_date)}</td>
                            <td><span class="badge badge-${reg.status}">${getStatusText(reg.status)}</span></td>
                            <td>
                                ${getActionButtons(reg)}
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading registrations:', error);
                document.getElementById('registrationsTableBody').innerHTML = 
                    '<tr><td colspan="8" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>';
            }
        }

        function getStatusText(status) {
            const statusTexts = {
                'pending': 'Chờ duyệt',
                'approved': 'Đã duyệt',
                'active': 'Đang hoạt động',
                'completed': 'Đã hoàn thành',
                'rejected': 'Bị từ chối'
            };
            return statusTexts[status] || status;
        }

        function getActionButtons(reg) {
            let buttons = '';
            
            if (reg.status === 'pending') {
                buttons = `
                    <button class="btn btn-sm btn-success me-1" onclick="approveRegistration(${reg.id})" title="Duyệt">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="showRejectModal(${reg.id})" title="Từ chối">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            } else if (reg.status === 'active') {
                buttons = `
                    <button class="btn btn-sm btn-info" onclick="showDetails(${reg.id})" title="Chi tiết">
                        <i class="fas fa-eye"></i>
                    </button>
                `;
            } else {
                buttons = `
                    <button class="btn btn-sm btn-secondary" onclick="showDetails(${reg.id})" title="Chi tiết">
                        <i class="fas fa-eye"></i>
                    </button>
                `;
            }
            
            return buttons;
        }

        async function approveRegistration(id) {
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
                    loadRegistrations();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể duyệt đăng ký'));
                }
            } catch (error) {
                console.error('Error approving registration:', error);
                alert('Lỗi khi duyệt đăng ký');
            }
        }

        function showRejectModal(id) {
            currentRegistrationId = id;
            document.getElementById('rejectRegistrationId').value = id;
            document.getElementById('rejectReason').value = '';
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }

        async function confirmReject() {
            const reason = document.getElementById('rejectReason').value.trim();
            
            if (!reason) {
                alert('Vui lòng nhập lý do từ chối!');
                return;
            }

            try {
                const response = await fetch(`../../api/registrations.php?id=${currentRegistrationId}&action=reject`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Từ chối đăng ký thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                    loadRegistrations();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể từ chối đăng ký'));
                }
            } catch (error) {
                console.error('Error rejecting registration:', error);
                alert('Lỗi khi từ chối đăng ký');
            }
        }

        function showDetails(id) {
            alert('Chi tiết đăng ký #' + id);
            // TODO: Implement detail view
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }
    </script>
</body>
</html>

