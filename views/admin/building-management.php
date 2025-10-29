<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tòa nhà - Admin</title>
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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Quản lý Tòa nhà</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
                    <i class="fas fa-plus me-2"></i>Thêm tòa nhà
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tên tòa nhà</th>
                                <th>Địa chỉ</th>
                                <th>Số tầng</th>
                                <th>Số phòng</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="buildingsTableBody">
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

    <!-- Add Building Modal -->
    <div class="modal fade" id="addBuildingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm tòa nhà mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addBuildingForm">
                        <div class="mb-3">
                            <label class="form-label">Tên tòa nhà *</label>
                            <input type="text" class="form-control" id="buildingName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="buildingAddress" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số tầng *</label>
                            <input type="number" class="form-control" id="buildingFloors" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" id="buildingDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="buildingActive" checked>
                            <label class="form-check-label">Kích hoạt</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" form="addBuildingForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Building Modal -->
    <div class="modal fade" id="editBuildingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Chỉnh sửa tòa nhà</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBuildingForm">
                        <input type="hidden" id="editBuildingId">
                        <div class="mb-3">
                            <label class="form-label">Tên tòa nhà *</label>
                            <input type="text" class="form-control" id="editBuildingName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="editBuildingAddress" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số tầng *</label>
                            <input type="number" class="form-control" id="editBuildingFloors" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" id="editBuildingDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="editBuildingActive">
                            <label class="form-check-label">Kích hoạt</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" form="editBuildingForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load buildings
        async function loadBuildings() {
            try {
                const response = await fetch('../../api/buildings.php');
                const data = await response.json();
                
                const tbody = document.getElementById('buildingsTableBody');
                
                if (data.success && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(building => `
                        <tr>
                            <td><i class="fas fa-building me-2"></i>${building.name}</td>
                            <td>${building.address || 'N/A'}</td>
                            <td>${building.total_floors} tầng</td>
                            <td>${building.total_rooms || 0} phòng</td>
                            <td>
                                <span class="badge bg-${building.is_active ? 'success' : 'secondary'}">
                                    ${building.is_active ? 'Hoạt động' : 'Ngừng hoạt động'}
                                </span>
                            </td>
                            <td>${formatDate(building.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editBuilding(${building.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBuilding(${building.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Không có dữ liệu</td></tr>';
                }
            } catch (error) {
                console.error('Error loading buildings:', error);
                document.getElementById('buildingsTableBody').innerHTML = 
                    '<tr><td colspan="7" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }

        // Add building
        document.getElementById('addBuildingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const data = {
                name: document.getElementById('buildingName').value,
                address: document.getElementById('buildingAddress').value,
                total_floors: document.getElementById('buildingFloors').value,
                description: document.getElementById('buildingDescription').value,
                is_active: document.getElementById('buildingActive').checked ? 1 : 0
            };
            
            try {
                const response = await fetch('../../api/buildings.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Thêm tòa nhà thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + result.error);
                }
            } catch (error) {
                alert('Lỗi kết nối!');
                console.error(error);
            }
        });

        // Edit building
        async function editBuilding(id) {
            try {
                const response = await fetch(`../../api/buildings.php?id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('editBuildingId').value = data.data.id;
                    document.getElementById('editBuildingName').value = data.data.name;
                    document.getElementById('editBuildingAddress').value = data.data.address || '';
                    document.getElementById('editBuildingFloors').value = data.data.total_floors;
                    document.getElementById('editBuildingDescription').value = data.data.description || '';
                    document.getElementById('editBuildingActive').checked = data.data.is_active == 1;
                    
                    new bootstrap.Modal(document.getElementById('editBuildingModal')).show();
                }
            } catch (error) {
                alert('Lỗi tải dữ liệu!');
                console.error(error);
            }
        }

        document.getElementById('editBuildingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('editBuildingId').value;
            const data = {
                name: document.getElementById('editBuildingName').value,
                address: document.getElementById('editBuildingAddress').value,
                total_floors: document.getElementById('editBuildingFloors').value,
                description: document.getElementById('editBuildingDescription').value,
                is_active: document.getElementById('editBuildingActive').checked ? 1 : 0
            };
            
            try {
                const response = await fetch(`../../api/buildings.php?id=${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Cập nhật thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + result.error);
                }
            } catch (error) {
                alert('Lỗi kết nối!');
                console.error(error);
            }
        });

        // Delete building
        function deleteBuilding(id) {
            if (confirm('Bạn có chắc chắn muốn xóa tòa nhà này? Tất cả phòng trong tòa nhà cũng sẽ bị xóa!')) {
                fetch(`../../api/buildings.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Lỗi kết nối!');
                    console.error(error);
                });
            }
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('vi-VN');
        }

        // Load on page load
        loadBuildings();
    </script>
</body>
</html>

