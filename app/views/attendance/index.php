<?php $isEmployee = $isEmployee ?? false; ?>
<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Chấm công tháng <?= $filters['month'] ?>/<?= $filters['year'] ?></h6>
</div>

<!-- Bộ lọc -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('attendance') ?>" class="filter-bar">
            <select name="month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $filters['month'] == $m ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select">
                <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?= $y ?>" <?= $filters['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <?php if (!$isEmployee && $departments): ?>
            <select name="department_id" class="form-select">
                <option value="">Tất cả phòng ban</option>
                <?php while ($dept = $departments->fetch_assoc()): ?>
                    <option value="<?= $dept['id'] ?>" <?= $filters['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                        <?= e($dept['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Lọc</button>
            <a href="<?= url('attendance') ?>" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
        </form>
    </div>
</div>

<!-- Thống kê -->
<div class="row g-3 mb-4">
    <?php if ($isEmployee): ?>
    <!-- Thống kê cho nhân viên -->
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h3><?= intval($stats['ngay_cong'] ?? 0) ?></h3>
                <p>Ngày công</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-calendar-minus"></i></div>
            <div class="stat-info">
                <h3><?= intval($stats['nghi_phep'] ?? 0) ?></h3>
                <p>Nghỉ phép</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <h3><?= intval($stats['nghi_kp'] ?? 0) ?></h3>
                <p>Vắng</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3><?= floatval($stats['overtime'] ?? 0) ?>h</h3>
                <p>Tăng ca</p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Thống kê cho admin/hr -->
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h3><?= intval($stats['di_lam'] ?? 0) ?></h3>
                <p>Ngày đi làm</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-calendar-minus"></i></div>
            <div class="stat-info">
                <h3><?= intval($stats['nghi_phep'] ?? 0) ?></h3>
                <p>Ngày nghỉ phép</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3><?= floatval($stats['total_ot'] ?? 0) ?> giờ</h3>
                <p>Tổng tăng ca</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!$isEmployee): ?>
<!-- Chấm công đơn lẻ (chỉ admin/hr) -->
<div class="card mb-3">
    <div class="card-header">
        <i class="fas fa-user-clock me-1"></i>Chấm công đơn lẻ
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('attendance/store') ?>">
            <?= csrfField() ?>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Chọn nhân viên --</option>
                        <?php while ($emp = $employees->fetch_assoc()): $empList[] = $emp; ?>
                            <option value="<?= $emp['id'] ?>"><?= e($emp['employee_code'] . ' - ' . $emp['full_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ngày <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="Đi làm">Đi làm</option>
                        <option value="Đi muộn">Đi muộn</option>
                        <option value="Nghỉ phép">Nghỉ phép</option>
                        <option value="Vắng">Vắng</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Giờ vào</label>
                    <input type="time" name="check_in" class="form-control" value="08:00">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Giờ ra</label>
                    <input type="time" name="check_out" class="form-control" value="17:00">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Tăng ca (h)</label>
                    <input type="number" name="overtime_hours" class="form-control" value="0" min="0" max="8" step="0.5">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ghi chú</label>
                    <input type="text" name="note" class="form-control" placeholder="Ghi chú...">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Lưu chấm công</button>
            </div>
        </form>
    </div>
</div>

<!-- Chấm công hàng loạt (chỉ admin/hr) -->
<div class="card mb-3">
    <div class="card-header">
        <i class="fas fa-users me-1"></i>Chấm công hàng loạt
        <button class="btn btn-sm btn-outline-primary float-end" type="button" data-bs-toggle="collapse" data-bs-target="#bulkForm">
            <i class="fas fa-chevron-down"></i> Mở rộng
        </button>
    </div>
    <div class="collapse" id="bulkForm">
        <div class="card-body">
            <form method="POST" action="<?= url('attendance/bulk-store') ?>">
                <?= csrfField() ?>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Ngày chấm công <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="Đi làm">Đi làm</option>
                            <option value="Đi muộn">Đi muộn</option>
                            <option value="Nghỉ phép">Nghỉ phép</option>
                            <option value="Vắng">Vắng</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giờ vào</label>
                        <input type="time" name="check_in" class="form-control" value="08:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giờ ra</label>
                        <input type="time" name="check_out" class="form-control" value="17:00">
                    </div>
                </div>

                <label class="form-label">Chọn nhân viên <span class="text-danger">*</span></label>
                <div class="mb-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllCheckboxes(true)">
                        <i class="fas fa-check-double me-1"></i>Chọn tất cả
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllCheckboxes(false)">
                        <i class="fas fa-times me-1"></i>Bỏ chọn tất cả
                    </button>
                </div>
                <div class="row g-2 mb-3" style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 6px; padding: 10px;">
                    <?php if (!empty($empList)): foreach ($empList as $emp): ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input emp-checkbox" type="checkbox" name="employee_ids[]"
                                       value="<?= $emp['id'] ?>" id="emp_<?= $emp['id'] ?>">
                                <label class="form-check-label" for="emp_<?= $emp['id'] ?>">
                                    <?= e($emp['employee_code'] . ' - ' . $emp['full_name']) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>

                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save me-1"></i>Lưu chấm công hàng loạt</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Bảng danh sách chấm công -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="fas fa-list me-1"></i>Danh sách chấm công</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <?php if (!$isEmployee): ?>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <?php endif; ?>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Giờ vào</th>
                        <th class="text-center">Giờ ra</th>
                        <th class="text-center">Tăng ca (h)</th>
                        <th>Ghi chú</th>
                        <?php if (!$isEmployee): ?>
                        <th class="text-center">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if ($records && $records->num_rows > 0): ?>
                    <?php while ($r = $records->fetch_assoc()): ?>
                    <tr>
                        <td><?= formatDate($r['work_date']) ?></td>
                        <?php if (!$isEmployee): ?>
                        <td>
                            <div class="employee-info">
                                <div class="employee-avatar"><?= getInitials($r['full_name']) ?></div>
                                <div>
                                    <div class="employee-name"><?= e($r['full_name']) ?></div>
                                    <div class="employee-code"><?= e($r['employee_code']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= e($r['dept_name'] ?? '-') ?></td>
                        <?php endif; ?>
                        <td class="text-center">
                            <?php
                            $badgeClass = 'bg-success';
                            if ($r['status'] === 'Đi muộn') $badgeClass = 'bg-warning text-dark';
                            elseif ($r['status'] === 'Nghỉ phép') $badgeClass = 'bg-info';
                            elseif ($r['status'] === 'Vắng') $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= e($r['status']) ?></span>
                        </td>
                        <td class="text-center"><?= e($r['check_in'] ?? '-') ?></td>
                        <td class="text-center"><?= e($r['check_out'] ?? '-') ?></td>
                        <td class="text-center"><?= floatval($r['overtime_hours'] ?? 0) ?></td>
                        <td><?= e($r['note'] ?? '-') ?></td>
                        <?php if (!$isEmployee): ?>
                        <td class="text-center">
                            <a href="<?= url('attendance/delete/' . $r['id']) ?>" class="btn btn-outline-danger btn-action"
                               onclick="return confirm('Bạn có chắc muốn xóa bản ghi chấm công này?')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $isEmployee ? 6 : 9 ?>" class="text-center text-muted py-4">
                            <i class="fas fa-inbox me-1"></i>Chưa có dữ liệu chấm công trong tháng này.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!$isEmployee): ?>
<script>
function toggleAllCheckboxes(checked) {
    document.querySelectorAll('.emp-checkbox').forEach(cb => cb.checked = checked);
}
</script>
<?php endif; ?>
