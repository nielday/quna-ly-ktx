<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phòng - Admin</title>
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
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bed me-2"></i>Quản lý Phòng</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus me-2"></i>Thêm phòng
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select class="form-select" id="filterBuilding">
                            <option value="">Tất cả tòa nhà</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filterStatus">
                            <option value="">Tất cả trạng thái</option>
                            <option value="available">Trống</option>
                            <option value="full">Đầy</option>
                            <option value="maintenance">Bảo trì</option>
                            <option value="reserved">Đã đặt</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary w-100" onclick="loadRooms()">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Số phòng</th>
                                <th>Tòa nhà</th>
                                <th>Tầng</th>
                                <th>Sức chứa</th>
                                <th>Đã ở</th>
                                <th>Trạng thái</th>
                                <th>Loại phòng</th>
                                <th>Giá</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="roomsTableBody">
                            <tr><td colspan="9" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm phòng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoomForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tòa nhà *</label>
                                <select class="form-select" id="roomBuildingId" required></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số phòng *</label>
                                <input type="text" class="form-control" id="roomNumber" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Tầng *</label>
                                <input type="number" class="form-control" id="floorNumber" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sức chứa *</label>
                                <input type="number" class="form-control" id="capacity" min="1" value="4" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Loại phòng *</label>
                                <select class="form-select" id="roomType" required>
                                    <option value="standard">Tiêu chuẩn</option>
                                    <option value="premium">Premium</option>
                                    <option value="vip">VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giá thuê/tháng (VNĐ) *</label>
                                <input type="number" class="form-control" id="monthlyFee" step="1000" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" id="roomDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" form="addRoomForm" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Chỉnh sửa phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editRoomForm">
                        <input type="hidden" id="editRoomId">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tòa nhà *</label>
                                <select class="form-select" id="editRoomBuildingId" required></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số phòng *</label>
                                <input type="text" class="form-control" id="editRoomNumber" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Tầng *</label>
                                <input type="number" class="form-control" id="editFloorNumber" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sức chứa *</label>
                                <input type="number" class="form-control" id="editCapacity" min="1" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Loại phòng *</label>
                                <select class="form-select" id="editRoomType" required>
                                    <option value="standard">Tiêu chuẩn</option>
                                    <option value="premium">Premium</option>
                                    <option value="vip">VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giá thuê/tháng (VNĐ) *</label>
                                <input type="number" class="form-control" id="editMonthlyFee" step="1000" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Trạng thái *</label>
                                <select class="form-select" id="editRoomStatus" required>
                                    <option value="available">Trống</option>
                                    <option value="full">Đầy</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="reserved">Đã đặt</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" id="editRoomDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" form="editRoomForm" class="btn btn-primary"><i class="fas fa-save me-2"></i>Cập nhật</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let buildings = [];
        
        async function loadBuildings() {
            try {
                const response = await fetch('../../api/buildings.php');
                const data = await response.json();
                buildings = data.data || [];
                
                const filterSelect = document.getElementById('filterBuilding');
                const modalSelect = document.getElementById('roomBuildingId');
                const editSelect = document.getElementById('editRoomBuildingId');
                
                filterSelect.innerHTML = '<option value="">Tất cả tòa nhà</option>';
                modalSelect.innerHTML = '<option value="">Chọn tòa nhà</option>';
                editSelect.innerHTML = '<option value="">Chọn tòa nhà</option>';
                
                buildings.forEach(b => {
                    filterSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                    modalSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                    editSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                });
            } catch (error) {
                console.error('Error loading buildings:', error);
            }
        }

        async function loadRooms() {
            try {
                const buildingId = document.getElementById('filterBuilding').value;
                const status = document.getElementById('filterStatus').value;
                
                let url = '../../api/rooms.php?page=1&limit=100';
                if (buildingId) url += `&building_id=${buildingId}`;
                if (status) url += `&status=${status}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('roomsTableBody');
                
                if (data.success && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(room => `
                        <tr>
                            <td><strong>${room.room_number}</strong></td>
                            <td>${room.building_name}</td>
                            <td>Tầng ${room.floor_number}</td>
                            <td>${room.capacity} người</td>
                            <td>${room.current_occupancy} người</td>
                            <td><span class="badge bg-${getStatusColor(room.status)}">${getStatusText(room.status)}</span></td>
                            <td>${getRoomTypeText(room.room_type)}</td>
                            <td>${formatCurrency(room.monthly_fee)}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editRoom(${room.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteRoom(${room.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                document.getElementById('roomsTableBody').innerHTML = '<tr><td colspan="9" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        document.getElementById('addRoomForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const data = {
                building_id: document.getElementById('roomBuildingId').value,
                room_number: document.getElementById('roomNumber').value,
                floor_number: document.getElementById('floorNumber').value,
                capacity: document.getElementById('capacity').value,
                room_type: document.getElementById('roomType').value,
                monthly_fee: document.getElementById('monthlyFee').value,
                description: document.getElementById('roomDescription').value
            };
            
            try {
                const response = await fetch('../../api/rooms.php?path=', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Thêm phòng thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (result.error || 'Không thể thêm phòng'));
                }
            } catch (error) {
                alert('Lỗi kết nối!');
                console.error(error);
            }
        });

        document.getElementById('editRoomForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const roomId = document.getElementById('editRoomId').value;
            const data = {
                building_id: document.getElementById('editRoomBuildingId').value,
                room_number: document.getElementById('editRoomNumber').value,
                floor_number: document.getElementById('editFloorNumber').value,
                capacity: document.getElementById('editCapacity').value,
                room_type: document.getElementById('editRoomType').value,
                monthly_fee: document.getElementById('editMonthlyFee').value,
                status: document.getElementById('editRoomStatus').value,
                description: document.getElementById('editRoomDescription').value
            };
            
            try {
                const response = await fetch(`../../api/rooms.php?path=${roomId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Cập nhật phòng thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (result.error || 'Không thể cập nhật phòng'));
                }
            } catch (error) {
                alert('Lỗi kết nối!');
                console.error(error);
            }
        });

        async function editRoom(id) {
            try {
                const response = await fetch(`../../api/rooms.php?path=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    const room = data.data;
                    document.getElementById('editRoomId').value = room.id;
                    document.getElementById('editRoomBuildingId').value = room.building_id;
                    document.getElementById('editRoomNumber').value = room.room_number;
                    document.getElementById('editFloorNumber').value = room.floor_number;
                    document.getElementById('editCapacity').value = room.capacity;
                    document.getElementById('editRoomType').value = room.room_type;
                    document.getElementById('editMonthlyFee').value = room.monthly_fee;
                    document.getElementById('editRoomStatus').value = room.status;
                    document.getElementById('editRoomDescription').value = room.description || '';
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editRoomModal'));
                    modal.show();
                } else {
                    alert('Lỗi tải dữ liệu phòng!');
                }
            } catch (error) {
                console.error('Error loading room:', error);
                alert('Lỗi kết nối!');
            }
        }

        function deleteRoom(id) {
            if (confirm('Bạn có chắc chắn muốn xóa phòng này?')) {
                fetch(`../../api/rooms.php?path=${id}`, {
                    method: 'DELETE'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa thành công!');
                        loadRooms();
                    } else {
                        alert('Lỗi: ' + (data.error || 'Không thể xóa'));
                    }
                });
            }
        }

        function getStatusColor(status) {
            const colors = { 'available': 'success', 'full': 'danger', 'maintenance': 'warning', 'reserved': 'info' };
            return colors[status] || 'secondary';
        }

        function getStatusText(status) {
            const texts = { 'available': 'Trống', 'full': 'Đầy', 'maintenance': 'Bảo trì', 'reserved': 'Đã đặt' };
            return texts[status] || status;
        }

        function getRoomTypeText(type) {
            const types = { 'standard': 'Tiêu chuẩn', 'premium': 'Premium', 'vip': 'VIP' };
            return types[type] || type;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }

        loadBuildings();
        loadRooms();
    </script>
</body>
</html>

