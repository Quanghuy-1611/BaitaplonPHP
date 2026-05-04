<?php
$isEdit = !empty($employee);
$action = $isEdit ? url('employees/update/' . $employee['id']) : url('employees/store');
?>

<div class="mb-3">
    <a href="<?= url('employees') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<form method="POST" action="<?= $action ?>">
    <?= csrfField() ?>

    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-user"></i> Thông tin cá nhân</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Mã nhân viên <span class="text-danger">*</span></label>
                            <input type="text" name="employee_code" class="form-control"
                                   value="<?= e($employee['employee_code'] ?? $nextCode) ?>"
                                   <?= $isEdit ? 'readonly' : 'required' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required
                                   value="<?= e($employee['full_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="Nam" <?= ($employee['gender'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                                <option value="Nữ" <?= ($employee['gender'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birth_date" class="form-control"
                                   value="<?= e($employee['birth_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CCCD/CMND</label>
                            <input type="text" name="id_card" class="form-control"
                                   value="<?= e($employee['id_card'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= e($employee['phone'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= e($employee['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2"><?= e($employee['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin công việc -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-briefcase"></i> Thông tin công việc</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Phòng ban <span class="text-danger">*</span></label>
                            <select name="department_id" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <?php while ($d = $departments->fetch_assoc()): ?>
                                    <option value="<?= $d['id'] ?>"
                                        <?= ($employee['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                                        <?= e($d['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chức vụ <span class="text-danger">*</span></label>
                            <select name="position_id" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <?php while ($p = $positions->fetch_assoc()): ?>
                                    <option value="<?= $p['id'] ?>" data-salary="<?= $p['base_salary'] ?>"
                                        <?= ($employee['position_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                        <?= e($p['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày vào làm</label>
                            <input type="date" name="hire_date" class="form-control"
                                   value="<?= e($employee['hire_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại hợp đồng</label>
                            <select name="contract_type" class="form-select">
                                <?php foreach (['Chính thức', 'Thử việc', 'Thời vụ', 'Cộng tác viên'] as $ct): ?>
                                    <option value="<?= $ct ?>" <?= ($employee['contract_type'] ?? '') === $ct ? 'selected' : '' ?>>
                                        <?= $ct ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lương cơ bản</label>
                            <input type="text" name="base_salary" class="form-control money-input"
                                   value="<?= $isEdit ? number_format($employee['base_salary'], 0, ',', '.') : '' ?>"
                                   placeholder="VD: 15.000.000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <?php foreach (['Đang làm', 'Thử việc', 'Đã nghỉ'] as $st): ?>
                                    <option value="<?= $st ?>" <?= ($employee['status'] ?? 'Đang làm') === $st ? 'selected' : '' ?>>
                                        <?= $st ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-university"></i> Thông tin ngân hàng</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên ngân hàng</label>
                            <input type="text" name="bank_name" class="form-control"
                                   value="<?= e($employee['bank_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số tài khoản</label>
                            <input type="text" name="bank_account" class="form-control"
                                   value="<?= e($employee['bank_account'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mt-3">
        <a href="<?= url('employees') ?>" class="btn btn-secondary btn-sm">Hủy</a>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i><?= $isEdit ? 'Cập nhật' : 'Thêm nhân viên' ?>
        </button>
    </div>
</form>

<script>
// Tự động điền lương khi chọn chức vụ (chỉ khi thêm mới)
<?php if (!$isEdit): ?>
document.querySelector('[name="position_id"]').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var salary = opt.getAttribute('data-salary');
    if (salary) {
        document.querySelector('[name="base_salary"]').value = Number(salary).toLocaleString('vi-VN');
    }
});
<?php endif; ?>
</script>
