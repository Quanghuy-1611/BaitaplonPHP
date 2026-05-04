<?php $isEmployee = $isEmployee ?? false; ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $isEmployee ? 'Nghỉ phép của tôi' : 'Quản lý nghỉ phép' ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLeaveModal">
            <i class="fas fa-plus me-1"></i> Tạo đơn nghỉ phép
        </button>
    </div>

    <?php if (!$isEmployee): ?>
    <!-- Filter Bar (chỉ admin/hr) -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('leaves') ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Chờ duyệt" <?= ($filters['status'] ?? '') === 'Chờ duyệt' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="Đã duyệt" <?= ($filters['status'] ?? '') === 'Đã duyệt' ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="Từ chối" <?= ($filters['status'] ?? '') === 'Từ chối' ? 'selected' : '' ?>>Từ chối</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phòng ban</label>
                    <select name="department_id" class="form-select">
                        <option value="">Tất cả</option>
                        <?php if ($departments): while ($dept = $departments->fetch_assoc()): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($filters['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                <?= e($dept['name']) ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tháng</label>
                    <select name="month" class="form-select">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>" <?= $filters['month'] == sprintf('%02d', $m) ? 'selected' : '' ?>>
                                Tháng <?= $m ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Năm</label>
                    <select name="year" class="form-select">
                        <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?= $y ?>" <?= $filters['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards (chỉ admin/hr) -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Chờ duyệt</div>
                            <div class="h2 mb-0"><?= $stats['pending'] ?></div>
                        </div>
                        <div class="text-warning"><i class="fas fa-clock fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Đã duyệt</div>
                            <div class="h2 mb-0"><?= $stats['approved'] ?></div>
                        </div>
                        <div class="text-success"><i class="fas fa-check-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Từ chối</div>
                            <div class="h2 mb-0"><?= $stats['rejected'] ?></div>
                        </div>
                        <div class="text-danger"><i class="fas fa-times-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Leave Requests Table -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <?php if (!$isEmployee): ?>
                        <th>Nhân viên</th>
                        <?php endif; ?>
                        <th>Loại nghỉ</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th class="text-center">Số ngày</th>
                        <th>Lý do</th>
                        <th class="text-center">Trạng thái</th>
                        <?php if (!$isEmployee): ?>
                        <th class="text-center">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$leaves || ($leaves instanceof mysqli_result && $leaves->num_rows === 0)): ?>
                        <tr>
                            <td colspan="<?= $isEmployee ? 6 : 8 ?>" class="text-center text-muted py-4">Không có dữ liệu nghỉ phép.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($leave = $leaves->fetch_assoc()): ?>
                            <tr>
                                <?php if (!$isEmployee): ?>
                                <td>
                                    <div class="fw-semibold"><?= e($leave['employee_code'] ?? '') ?></div>
                                    <div class="text-muted small"><?= e($leave['full_name'] ?? '') ?></div>
                                </td>
                                <?php endif; ?>
                                <td><?= e($leave['leave_type']) ?></td>
                                <td><?= formatDate($leave['start_date']) ?></td>
                                <td><?= formatDate($leave['end_date']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $leave['days'] ?></span>
                                </td>
                                <td>
                                    <span title="<?= e($leave['reason'] ?? '') ?>">
                                        <?= e(mb_strlen($leave['reason'] ?? '') > 50 ? mb_substr($leave['reason'], 0, 50) . '...' : ($leave['reason'] ?? '')) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($leave['status'] === 'Chờ duyệt'): ?>
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    <?php elseif ($leave['status'] === 'Đã duyệt'): ?>
                                        <span class="badge bg-success">Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Từ chối</span>
                                    <?php endif; ?>
                                </td>
                                <?php if (!$isEmployee): ?>
                                <td class="text-center">
                                    <?php if ($leave['status'] === 'Chờ duyệt'): ?>
                                        <form method="POST" action="<?= url('leaves') ?>" class="d-inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success" title="Duyệt"
                                                onclick="return confirm('Bạn có chắc muốn duyệt đơn này?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= url('leaves') ?>" class="d-inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Từ chối"
                                                onclick="return confirm('Bạn có chắc muốn từ chối đơn này?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Leave Modal -->
<div class="modal fade" id="createLeaveModal" tabindex="-1" aria-labelledby="createLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= url('leaves') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLeaveModalLabel">Tạo đơn nghỉ phép</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <?php if (!$isEmployee): ?>
                        <div class="col-md-6">
                            <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Chọn nhân viên --</option>
                                <?php if ($employees): while ($emp = $employees->fetch_assoc()): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= e($emp['employee_code']) ?> - <?= e($emp['full_name']) ?>
                                    </option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="<?= $isEmployee ? 'col-12' : 'col-md-6' ?>">
                            <label class="form-label">Loại nghỉ <span class="text-danger">*</span></label>
                            <select name="leave_type" class="form-select" required>
                                <option value="">-- Chọn loại nghỉ --</option>
                                <?php foreach ($leaveTypes as $type): ?>
                                    <option value="<?= e($type) ?>"><?= e($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="leaveStartDate" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="leaveEndDate" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số ngày</label>
                            <input type="number" name="days" id="leaveDays" class="form-control" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Lý do</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Nhập lý do nghỉ phép..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Tạo đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('leaveStartDate');
    const endDateInput = document.getElementById('leaveEndDate');
    const daysInput = document.getElementById('leaveDays');

    function calculateDays() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);

            if (end >= start) {
                const diffTime = end.getTime() - start.getTime();
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                daysInput.value = diffDays;
            } else {
                daysInput.value = '';
            }
        } else {
            daysInput.value = '';
        }
    }

    startDateInput.addEventListener('change', calculateDays);
    endDateInput.addEventListener('change', calculateDays);
});
</script>
