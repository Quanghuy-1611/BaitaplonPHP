<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Danh sách phòng ban trong công ty</h6>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#deptModal" onclick="resetForm()">
        <i class="fas fa-plus me-1"></i>Thêm phòng ban
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên phòng ban</th>
                    <th>Mô tả</th>
                    <th>Trưởng phòng</th>
                    <th>Điện thoại</th>
                    <th class="text-center">Số NV</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($d = $departments->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= e($d['name']) ?></strong></td>
                    <td><?= e($d['description'] ?? '-') ?></td>
                    <td><?= e($d['manager_name'] ?? '-') ?></td>
                    <td><?= e($d['phone'] ?? '-') ?></td>
                    <td class="text-center"><span class="badge bg-primary rounded-pill"><?= $d['emp_count'] ?></span></td>
                    <td class="text-center">
                        <button class="btn btn-outline-warning btn-action" onclick="editDept(<?= htmlspecialchars(json_encode($d)) ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa phòng ban này?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
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
<div class="modal fade" id="deptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="formId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm phòng ban</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="fName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="fDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trưởng phòng</label>
                        <input type="text" name="manager_name" id="fManager" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Điện thoại</label>
                        <input type="text" name="phone" id="fPhone" class="form-control">
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
function resetForm() {
    document.getElementById('formAction').value = 'create';
    document.getElementById('formId').value = '';
    document.getElementById('modalTitle').textContent = 'Thêm phòng ban';
    document.getElementById('fName').value = '';
    document.getElementById('fDesc').value = '';
    document.getElementById('fManager').value = '';
    document.getElementById('fPhone').value = '';
}
function editDept(d) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('formId').value = d.id;
    document.getElementById('modalTitle').textContent = 'Sửa phòng ban';
    document.getElementById('fName').value = d.name || '';
    document.getElementById('fDesc').value = d.description || '';
    document.getElementById('fManager').value = d.manager_name || '';
    document.getElementById('fPhone').value = d.phone || '';
    new bootstrap.Modal(document.getElementById('deptModal')).show();
}
</script>
