<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Quản lý các loại phụ cấp và gán cho nhân viên</h6>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#allowanceModal" onclick="resetForm()">
        <i class="fas fa-plus me-1"></i>Thêm loại phụ cấp
    </button>
</div>

<!-- Bảng loại phụ cấp -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-list me-1"></i>Danh sách loại phụ cấp
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên phụ cấp</th>
                    <th class="text-end">Mức mặc định</th>
                    <th>Mô tả</th>
                    <th class="text-center">Số NV</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($a = $allowances->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= e($a['name']) ?></strong></td>
                    <td class="text-end text-money"><?= formatMoney($a['default_amount']) ?></td>
                    <td><?= e($a['description'] ?? '-') ?></td>
                    <td class="text-center"><span class="badge bg-primary rounded-pill"><?= $a['emp_count'] ?></span></td>
                    <td class="text-center">
                        <button class="btn btn-outline-warning btn-action" onclick="editAllowance(<?= htmlspecialchars(json_encode($a)) ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa loại phụ cấp này?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button class="btn btn-outline-danger btn-action"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Gán phụ cấp cho nhân viên -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-user-plus me-1"></i>Gán phụ cấp cho nhân viên
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('allowances/assign') ?>" class="row g-3 align-items-end">
            <?= csrfField() ?>
            <div class="col-md-3">
                <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select" required>
                    <option value="">-- Chọn nhân viên --</option>
                    <?php while ($emp = $employees->fetch_assoc()): ?>
                        <option value="<?= $emp['id'] ?>"><?= e($emp['employee_code']) ?> - <?= e($emp['full_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Loại phụ cấp <span class="text-danger">*</span></label>
                <select name="allowance_id" id="assignAllowanceSelect" class="form-select" required onchange="fillDefaultAmount()">
                    <option value="">-- Chọn phụ cấp --</option>
                    <?php
                    // Reset con trỏ allowances để dùng lại
                    if ($allowances instanceof mysqli_result) {
                        $allowances->data_seek(0);
                        while ($a2 = $allowances->fetch_assoc()):
                    ?>
                        <option value="<?= $a2['id'] ?>" data-amount="<?= $a2['default_amount'] ?>"><?= e($a2['name']) ?></option>
                    <?php endwhile; } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                <input type="text" name="amount" id="assignAmount" class="form-control money-input" placeholder="VD: 500.000" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-plus-circle me-1"></i>Gán phụ cấp
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bảng phụ cấp đã gán -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-users me-1"></i>Danh sách phụ cấp nhân viên
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nhân viên</th>
                        <th>Loại phụ cấp</th>
                        <th class="text-end">Số tiền</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($assignments && $assignments->num_rows > 0): ?>
                    <?php $j = 1; while ($as = $assignments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $j++ ?></td>
                        <td>
                            <strong><?= e($as['full_name']) ?></strong>
                            <br><small class="text-muted"><?= e($as['employee_code']) ?></small>
                        </td>
                        <td><?= e($as['allowance_name']) ?></td>
                        <td class="text-end text-money"><?= formatMoney($as['amount']) ?></td>
                        <td class="text-center">
                            <form method="POST" action="<?= url('allowances/remove') ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn gỡ phụ cấp này?')">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $as['id'] ?>">
                                <button class="btn btn-outline-danger btn-action"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Chưa có phụ cấp nào được gán</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa loại phụ cấp -->
<div class="modal fade" id="allowanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="formId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm loại phụ cấp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên phụ cấp <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="fName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức mặc định</label>
                        <input type="text" name="default_amount" id="fAmount" class="form-control money-input" placeholder="VD: 500.000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="fDesc" class="form-control" rows="2"></textarea>
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
    document.getElementById('modalTitle').textContent = 'Thêm loại phụ cấp';
    document.getElementById('fName').value = '';
    document.getElementById('fAmount').value = '';
    document.getElementById('fDesc').value = '';
}
function editAllowance(a) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('formId').value = a.id;
    document.getElementById('modalTitle').textContent = 'Sửa loại phụ cấp';
    document.getElementById('fName').value = a.name || '';
    document.getElementById('fAmount').value = a.default_amount ? Number(a.default_amount).toLocaleString('vi-VN') : '';
    document.getElementById('fDesc').value = a.description || '';
    new bootstrap.Modal(document.getElementById('allowanceModal')).show();
}
function fillDefaultAmount() {
    var sel = document.getElementById('assignAllowanceSelect');
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.amount) {
        document.getElementById('assignAmount').value = Number(opt.dataset.amount).toLocaleString('vi-VN');
    }
}
</script>
