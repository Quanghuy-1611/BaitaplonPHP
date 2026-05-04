<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Danh sách chức vụ và mức lương cơ bản</h6>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#posModal" onclick="resetPosForm()">
        <i class="fas fa-plus me-1"></i>Thêm chức vụ
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên chức vụ</th>
                    <th>Phòng ban</th>
                    <th class="text-end">Lương cơ bản</th>
                    <th>Mô tả</th>
                    <th class="text-center">Số NV</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($positions && ($p = $positions->fetch_assoc())): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= e($p['name']) ?></strong></td>
                    <td><?= e($p['dept_name'] ?? '-') ?></td>
                    <td class="text-end text-money"><?= formatMoney($p['base_salary']) ?></td>
                    <td><?= e($p['description'] ?? '-') ?></td>
                    <td class="text-center"><span class="badge bg-primary rounded-pill"><?= $p['emp_count'] ?></span></td>
                    <td class="text-center">
                        <button class="btn btn-outline-warning btn-action" onclick="editPos(<?= htmlspecialchars(json_encode($p)) ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button class="btn btn-outline-danger btn-action"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="posModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="posAction" value="create">
                <input type="hidden" name="id" id="posId">
                <div class="modal-header">
                    <h5 class="modal-title" id="posTitle">Thêm chức vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên chức vụ <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="pName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phòng ban</label>
                        <select name="department_id" id="pDept" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php while ($dept = $departments->fetch_assoc()): ?>
                                <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lương cơ bản</label>
                        <input type="text" name="base_salary" id="pSalary" class="form-control money-input" placeholder="VD: 15.000.000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="pDesc" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetPosForm() {
    document.getElementById('posAction').value = 'create';
    document.getElementById('posId').value = '';
    document.getElementById('posTitle').textContent = 'Thêm chức vụ';
    document.getElementById('pName').value = '';
    document.getElementById('pDept').value = '';
    document.getElementById('pSalary').value = '';
    document.getElementById('pDesc').value = '';
}
function editPos(p) {
    document.getElementById('posAction').value = 'update';
    document.getElementById('posId').value = p.id;
    document.getElementById('posTitle').textContent = 'Sửa chức vụ';
    document.getElementById('pName').value = p.name || '';
    document.getElementById('pDept').value = p.department_id || '';
    document.getElementById('pSalary').value = p.base_salary ? Number(p.base_salary).toLocaleString('vi-VN') : '';
    document.getElementById('pDesc').value = p.description || '';
    new bootstrap.Modal(document.getElementById('posModal')).show();
}
</script>
