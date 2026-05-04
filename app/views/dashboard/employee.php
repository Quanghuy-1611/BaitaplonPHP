<?php if (!$employee): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-1"></i>
    Tài khoản chưa được liên kết với hồ sơ nhân viên. Vui lòng liên hệ quản trị viên.
</div>
<?php return; endif; ?>

<!-- Thông tin cá nhân -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div class="employee-avatar" style="width:56px;height:56px;font-size:22px;">
                <?= getInitials($employee['full_name']) ?>
            </div>
            <div>
                <h5 class="mb-0"><?= e($employee['full_name']) ?></h5>
                <small class="text-muted">
                    <?= e($employee['employee_code'] ?? '') ?>
                    &middot; <?= e($employee['dept_name'] ?? '-') ?>
                    &middot; <?= e($employee['pos_name'] ?? '-') ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h3><?= $attStats['ngay_cong'] ?? 0 ?></h3>
                <p>Ngày công T<?= $month ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3><?= $attStats['overtime'] ?? 0 ?>h</h3>
                <p>Tăng ca T<?= $month ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-calendar-minus"></i></div>
            <div class="stat-info">
                <h3><?= $leaveBalance['remaining'] ?? 12 ?></h3>
                <p>Phép còn lại <?= $year ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <h3><?= $latestSalary ? formatMoney($latestSalary['net_salary']) : '-' ?></h3>
                <p>Lương thực nhận</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Cột trái: Chấm công + Nghỉ phép -->
    <div class="col-md-8">
        <!-- Chi tiết chấm công -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-check"></i> Chấm công tháng <?= $month ?>/<?= $year ?></span>
                <a href="<?= url('attendance') ?>" class="text-primary" style="font-size:13px;">Xem chi tiết →</a>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <div class="fw-bold text-primary fs-4"><?= $attStats['ngay_cong'] ?? 0 ?></div>
                        <small class="text-muted">Ngày công</small>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-success fs-4"><?= $attStats['nghi_phep'] ?? 0 ?></div>
                        <small class="text-muted">Nghỉ phép</small>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-danger fs-4"><?= $attStats['nghi_kp'] ?? 0 ?></div>
                        <small class="text-muted">Vắng</small>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-warning fs-4"><?= $attStats['overtime'] ?? 0 ?>h</div>
                        <small class="text-muted">Tăng ca</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn nghỉ phép gần đây -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-minus"></i> Đơn nghỉ phép <?= $year ?></span>
                <a href="<?= url('leaves') ?>" class="text-primary" style="font-size:13px;">Xem tất cả →</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Loại</th>
                            <th>Từ ngày</th>
                            <th>Đến ngày</th>
                            <th class="text-center">Số ngày</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $hasLeaves = false;
                    if ($recentLeaves && $recentLeaves instanceof mysqli_result && $recentLeaves->num_rows > 0):
                        $hasLeaves = true;
                        $count = 0;
                        while ($l = $recentLeaves->fetch_assoc()):
                            if ($count++ >= 5) break; // Chỉ hiện 5 dòng gần nhất
                            $statusClass = match($l['status']) {
                                'Đã duyệt' => 'success',
                                'Từ chối' => 'danger',
                                default => 'warning',
                            };
                    ?>
                        <tr>
                            <td><?= e($l['leave_type'] ?? '-') ?></td>
                            <td><?= formatDate($l['start_date']) ?></td>
                            <td><?= formatDate($l['end_date']) ?></td>
                            <td class="text-center"><?= $l['days'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= $statusClass ?>"><?= e($l['status']) ?></span>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    <?php if (!$hasLeaves): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">Chưa có đơn nghỉ phép</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cột phải -->
    <div class="col-md-4">
        <!-- Phép năm -->
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-umbrella-beach"></i> Phép năm <?= $year ?></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng phép năm</span>
                    <strong><?= $leaveBalance['annual'] ?? 12 ?> ngày</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Đã sử dụng</span>
                    <strong class="text-danger"><?= $leaveBalance['used'] ?? 0 ?> ngày</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Còn lại</span>
                    <strong class="text-success"><?= $leaveBalance['remaining'] ?? 12 ?> ngày</strong>
                </div>
                <?php
                $usedPct = ($leaveBalance['annual'] > 0)
                    ? round(($leaveBalance['used'] / $leaveBalance['annual']) * 100)
                    : 0;
                ?>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar bg-success" style="width:<?= 100 - $usedPct ?>%"></div>
                    <div class="progress-bar bg-danger" style="width:<?= $usedPct ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Lương gần nhất -->
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-file-invoice-dollar"></i> Lương gần nhất</div>
            <div class="card-body">
                <?php if ($latestSalary): ?>
                    <div class="text-muted mb-2">
                        Tháng <?= $latestSalary['month'] ?>/<?= $latestSalary['year'] ?>
                        <span class="badge bg-<?= $latestSalary['status'] === 'Đã thanh toán' ? 'success' : ($latestSalary['status'] === 'Đã duyệt' ? 'primary' : 'warning') ?> ms-1">
                            <?= e($latestSalary['status']) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Lương cơ bản</small>
                        <small><?= formatMoney($latestSalary['base_salary']) ?></small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Phụ cấp</small>
                        <small class="text-success">+<?= formatMoney($latestSalary['total_allowance']) ?></small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Tăng ca</small>
                        <small class="text-success">+<?= formatMoney($latestSalary['overtime_pay']) ?></small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Bảo hiểm</small>
                        <small class="text-danger">-<?= formatMoney(($latestSalary['bhxh'] ?? 0) + ($latestSalary['bhyt'] ?? 0) + ($latestSalary['bhtn'] ?? 0)) ?></small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Thuế TNCN</small>
                        <small class="text-danger">-<?= formatMoney($latestSalary['tax'] ?? 0) ?></small>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Thực nhận</strong>
                        <strong class="text-primary"><?= formatMoney($latestSalary['net_salary']) ?></strong>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center mb-0 py-2">Chưa có dữ liệu lương</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thao tác nhanh -->
        <div class="card">
            <div class="card-header"><i class="fas fa-bolt"></i> Thao tác nhanh</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('attendance') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-check me-1"></i>Xem chấm công
                    </a>
                    <a href="<?= url('leaves') ?>" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-calendar-minus me-1"></i>Xin nghỉ phép
                    </a>
                    <a href="<?= url('salary/payslip') ?>" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-file-invoice-dollar me-1"></i>Xem phiếu lương
                    </a>
                    <a href="<?= url('profile') ?>" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-user-circle me-1"></i>Hồ sơ cá nhân
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
