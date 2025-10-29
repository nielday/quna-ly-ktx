<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thiết bị - Admin</title>
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
        .badge-working { background-color: #198754; }
        .badge-broken { background-color: #dc3545; }
        .badge-maintenance { background-color: #ffc107; color: #000; }
        .badge-replaced { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-tools me-2"></i>Quản lý Thiết bị</h4>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tòa nhà</label>
                        <select class="form-select" id="filterBuilding" onchange="onBuildingChange()">
                            <option value="">Tất cả</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phòng</label>
                        <select class="form-select" id="filterRoom" onchange="loadEquipment()">
                            <option value="">Tất cả</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" id="filterStatus" onchange="loadEquipment()">
                            <option value="">Tất cả</option>
                            <option value="working">Đang hoạt động</option>
                            <option value="broken">Hỏng</option>
                            <option value="maintenance">Đang sửa</option>
                            <option value="replaced">Đã thay thế</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                            <i class="fas fa-plus me-2"></i>Thêm thiết bị
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách thiết bị</h5>
                <span class="badge bg-primary" id="totalCount">0 thiết bị</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Thiết bị</th>
                                <th>Loại</th>
                                <th>Phòng</th>
                                <th>Nhãn hiệu</th>
                                <th>Ngày mua</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="equipmentTableBody">
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
    </div>

    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm thiết bị mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addEquipmentForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phòng *</label>
                                <select class="form-select" id="equipmentRoomId" required></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loại thiết bị *</label>
                                <select class="form-select" id="equipmentType" required>
                                    <option value="">Chọn loại...</option>
                                    <option value="Air Conditioner">Điều hòa</option>
                                    <option value="Fan">Quạt</option>
                                    <option value="TV">TV</option>
                                    <option value="Refrigerator">Tủ lạnh</option>
                                    <option value="Washing Machine">Máy giặt</option>
                                    <option value="Bed">Giường</option>
                                    <option value="Table">Bàn</option>
                                    <option value="Chair">Ghế</option>
                                    <option value="Cabinet">Tủ</option>
                                    <option value="Other">Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên thiết bị *</label>
                            <input type="text" class="form-control" id="equipmentName" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nhãn hiệu</label>
                                <input type="text" class="form-control" id="equipmentBrand">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" id="equipmentModel">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số serial</label>
                                <input type="text" class="form-control" id="equipmentSerial">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày mua</label>
                                <input type="date" class="form-control" id="equipmentPurchaseDate">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddEquipment()">
                        <i class="fas fa-save me-2"></i>Thêm thiết bị
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái thiết bị</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="statusEquipmentId">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái *</label>
                        <select class="form-select" id="statusSelect" required>
                            <option value="working">Đang hoạt động</option>
                            <option value="broken">Hỏng</option>
                            <option value="maintenance">Đang sửa</option>
                            <option value="replaced">Đã thay thế</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="updateStatus()">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentEquipmentId = null;

        document.addEventListener('DOMContentLoaded', async function() {
            await loadBuildings();
            await loadRoomsForFilter(); // Load all rooms initially
            await loadEquipment();
            loadRooms(); // Load rooms for the equipment modal
        });

        async function loadBuildings() {
            try {
                const response = await fetch('../../api/buildings.php');
                const data = await response.json();
                
                const select = document.getElementById('filterBuilding');
                
                if (data.success && data.data) {
                    select.innerHTML = '<option value="">Tất cả</option>' + 
                        data.data.map(building => 
                            `<option value="${building.id}">${building.name}</option>`
                        ).join('');
                }
            } catch (error) {
                console.error('Error loading buildings:', error);
            }
        }

        async function loadRooms() {
            try {
                const response = await fetch('../../api/rooms.php');
                const data = await response.json();
                
                const select = document.getElementById('equipmentRoomId');
                
                if (data.success && data.data) {
                    select.innerHTML = '<option value="">Chọn phòng...</option>' + 
                        data.data.map(room => 
                            `<option value="${room.id}">Phòng ${room.room_number} - ${room.building_name}</option>`
                        ).join('');
                } else {
                    select.innerHTML = '<option value="">Không có phòng nào</option>';
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                const select = document.getElementById('equipmentRoomId');
                select.innerHTML = '<option value="">Lỗi khi tải dữ liệu phòng</option>';
            }
        }

        async function loadRoomsForFilter(buildingId = null) {
            try {
                let url = '../../api/rooms.php?limit=1000';
                if (buildingId) {
                    url += `&building_id=${buildingId}`;
                }
                
                const response = await fetch(url);
                const data = await response.json();
                
                const select = document.getElementById('filterRoom');
                
                if (data.success && data.data) {
                    select.innerHTML = '<option value="">Tất cả</option>' + 
                        data.data.map(room => 
                            `<option value="${room.id}">Phòng ${room.room_number} - ${room.building_name}</option>`
                        ).join('');
                } else {
                    select.innerHTML = '<option value="">Tất cả</option>';
                }
            } catch (error) {
                console.error('Error loading rooms for filter:', error);
            }
        }

        async function onBuildingChange() {
            const buildingId = document.getElementById('filterBuilding').value;
            // Reset filter room dropdown
            const roomSelect = document.getElementById('filterRoom');
            roomSelect.innerHTML = '<option value="">Tất cả</option>';
            
            // Load rooms for this building
            if (buildingId) {
                await loadRoomsForFilter(buildingId);
            } else {
                // Load all rooms if no building selected
                await loadRoomsForFilter();
            }
            
            // Reload equipment
            loadEquipment();
        }

        async function loadEquipment() {
            try {
                const buildingId = document.getElementById('filterBuilding').value;
                const roomId = document.getElementById('filterRoom').value;
                const status = document.getElementById('filterStatus').value;
                
                let url = '../../api/equipment.php?page=1&limit=100';
                if (roomId) url += `&room_id=${roomId}`;
                if (status) url += `&status=${status}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('equipmentTableBody');
                document.getElementById('totalCount').textContent = `${data.data?.length || 0} thiết bị`;
                
                if (data.success && data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(eq => `
                        <tr>
                            <td><strong>${eq.equipment_name}</strong></td>
                            <td>${eq.equipment_type}</td>
                            <td>${eq.room_number} - ${eq.building_name}</td>
                            <td>${eq.brand || 'N/A'}</td>
                            <td>${eq.purchase_date ? formatDate(eq.purchase_date) : 'N/A'}</td>
                            <td><span class="badge badge-${eq.status}">${getStatusText(eq.status)}</span></td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick="showStatusModal(${eq.id})" title="Đổi trạng thái">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteEquipment(${eq.id})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading equipment:', error);
            }
        }

        function getStatusText(status) {
            const statusTexts = {
                'working': 'Đang hoạt động',
                'broken': 'Hỏng',
                'maintenance': 'Đang sửa',
                'replaced': 'Đã thay thế'
            };
            return statusTexts[status] || status;
        }

        async function showStatusModal(id) {
            currentEquipmentId = id;
            document.getElementById('statusEquipmentId').value = id;
            document.getElementById('statusSelect').value = 'working';
            new bootstrap.Modal(document.getElementById('statusModal')).show();
        }

        async function updateStatus() {
            const status = document.getElementById('statusSelect').value;

            try {
                const response = await fetch(`../../api/equipment.php?id=${currentEquipmentId}&action=update-status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                // Lấy text response trước để debug
                const text = await response.text();
                console.log('Response status:', response.status);
                console.log('Response text:', text);
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    alert('Lỗi: Không thể parse JSON từ server. Response: ' + text);
                    return;
                }

                if (data.success) {
                    alert('Cập nhật trạng thái thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                    loadEquipment();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể cập nhật'));
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Lỗi khi cập nhật trạng thái: ' + error.message);
            }
        }

        async function submitAddEquipment() {
            const roomId = document.getElementById('equipmentRoomId').value;
            const equipmentName = document.getElementById('equipmentName').value;
            const equipmentType = document.getElementById('equipmentType').value;

            if (!roomId || !equipmentName || !equipmentType) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }

            const formData = {
                room_id: roomId,
                equipment_name: equipmentName,
                equipment_type: equipmentType,
                brand: document.getElementById('equipmentBrand').value || '',
                model: document.getElementById('equipmentModel').value || '',
                serial_number: document.getElementById('equipmentSerial').value || '',
                purchase_date: document.getElementById('equipmentPurchaseDate').value || null
            };

            try {
                const response = await fetch('../../api/equipment.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Thêm thiết bị thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('addEquipmentModal')).hide();
                    document.getElementById('addEquipmentForm').reset();
                    loadEquipment();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể thêm thiết bị'));
                }
            } catch (error) {
                console.error('Error adding equipment:', error);
                alert('Lỗi khi thêm thiết bị');
            }
        }

        async function deleteEquipment(id) {
            if (!confirm('Bạn có chắc muốn xóa thiết bị này?')) {
                return;
            }

            try {
                const response = await fetch(`../../api/equipment.php?id=${id}`, {
                    method: 'DELETE'
                });

                const data = await response.json();

                if (data.success) {
                    alert('Xóa thiết bị thành công!');
                    loadEquipment();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể xóa'));
                }
            } catch (error) {
                console.error('Error deleting equipment:', error);
                alert('Lỗi khi xóa thiết bị');
            }
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }
    </script>
</body>
</html>

