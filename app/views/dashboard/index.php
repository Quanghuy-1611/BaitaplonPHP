<!-- Thống kê tổng quan -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Nhân viên đang làm</div>
            <div class="fs-4 fw-bold"><?= $totalEmployees ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Mới tháng này</div>
            <div class="fs-4 fw-bold"><?= $newThisMonth ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Tổng lương T<?= $month ?></div>
            <div class="fs-4 fw-bold"><?= formatMoney($salaryTotals['t_net'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Đơn nghỉ phép chờ duyệt</div>
            <div class="fs-4 fw-bold"><?= $pendingLeaves ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Hợp đồng sắp hết hạn</div>
            <div class="fs-4 fw-bold"><?= $contractStats['expiring'] ?? 0 ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Tạm ứng chờ duyệt</div>
            <div class="fs-4 fw-bold"><?= $pendingAdvances ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Tổng Gross T<?= $month ?></div>
            <div class="fs-4 fw-bold"><?= formatMoney($salaryTotals['t_gross'] ?? 0) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Cột trái -->
    <div class="col-md-8">
        <!-- Lương 6 tháng -->
        <div class="card mb-3">
            <div class="card-header">Chi phí lương 6 tháng gần nhất</div>
            <div class="card-body">
                <?php
                $chartData = [];
                if ($salaryTrend) {
                    while ($row = $salaryTrend->fetch_assoc()) {
                        $chartData[] = $row;
                    }
                }
                $chartData = array_reverse($chartData);
                $maxVal = 1;
                foreach ($chartData as $c) {
                    if ($c['total_net'] > $maxVal) $maxVal = $c['total_net'];
                }
                ?>
                <?php if (empty($chartData)): ?>
                    <p class="text-muted text-center py-3">Chưa có dữ liệu lương</p>
                <?php else: ?>
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Tháng</th>
                                <th>Số NV</th>
                                <th>Tổng lương Net</th>
                                <th style="width:40%"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($chartData as $c):
                            $pct = round($c['total_net'] / $maxVal * 100);
                        ?>
                            <tr>
                                <td>T<?= $c['month'] ?>/<?= $c['year'] ?></td>
                                <td><?= $c['emp_count'] ?></td>
                                <td class="text-money"><?= formatMoney($c['total_net']) ?></td>
                                <td>
                                    <div class="progress" style="height:18px;">
                                        <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nhân viên mới -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Nhân viên mới nhất</span>
                <a href="<?= url('employees') ?>" class="text-primary small">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Mã NV</th>
                            <th>Họ tên</th>
                            <th>Phòng ban</th>
                            <th>Chức vụ</th>
                            <th>Ngày vào</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($recentEmployees): while ($e = $recentEmployees->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($e['employee_code']) ?></td>
                            <td><?= e($e['full_name']) ?></td>
                            <td><?= e($e['dept_name'] ?? '-') ?></td>
                            <td><?= e($e['pos_name'] ?? '-') ?></td>
                            <td><?= !empty($e['hire_date']) ? formatDate($e['hire_date']) : '-' ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cột phải -->
    <div class="col-md-4">
        <!-- Phòng ban -->
        <div class="card mb-3">
            <div class="card-header">Nhân viên theo phòng ban</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                    <?php if ($empByDept): while ($d = $empByDept->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($d['name']) ?></td>
                            <td class="text-end"><strong><?= $d['cnt'] ?></strong></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- HĐ sắp hết hạn -->
        <?php if ($expiringContracts && $expiringContracts->num_rows > 0): ?>
        <div class="card mb-3">
            <div class="card-header text-danger">Hợp đồng sắp hết hạn</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                    <?php while ($c = $expiringContracts->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= e($c['full_name']) ?>
                                <br><small class="text-muted"><?= e($c['employee_code']) ?></small>
                            </td>
                            <td class="text-end text-danger"><?= formatDate($c['end_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Thao tác nhanh -->
        <div class="card">
            <div class="card-header">Thao tác nhanh</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (hasRole('employees')): ?>
                    <a href="<?= url('employees/create') ?>" class="btn btn-outline-primary btn-sm">Thêm nhân viên</a>
                    <?php endif; ?>
                    <?php if (hasRole('attendance')): ?>
                    <a href="<?= url('attendance') ?>" class="btn btn-outline-primary btn-sm">Chấm công</a>
                    <?php endif; ?>
                    <?php if (hasRole('salary')): ?>
                    <a href="<?= url('salary') ?>" class="btn btn-outline-primary btn-sm">Bảng lương</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
