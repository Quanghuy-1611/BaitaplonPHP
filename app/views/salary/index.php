<!-- Bộ lọc -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('salary') ?>" class="filter-bar">
            <select name="month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select">
                <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Xem</button>
            <a href="<?= url('salary/calculate?month=' . $month . '&year=' . $year) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-calculator me-1"></i>Tính lương
            </a>
            <a href="<?= url('salary/export?month=' . $month . '&year=' . $year) ?>" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-csv me-1"></i>Xuất CSV
            </a>
        </form>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3><?= $totals['total_count'] ?? 0 ?></h3>
                <p>Nhân viên</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <h3><?= formatMoney($totals['t_gross'] ?? 0) ?></h3>
                <p>Tổng Gross</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-minus-circle"></i></div>
            <div class="stat-info">
                <h3><?= formatMoney(($totals['t_insurance'] ?? 0) + ($totals['t_tax'] ?? 0)) ?></h3>
                <p>Tổng khấu trừ</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-wallet"></i></div>
            <div class="stat-info">
                <h3><?= formatMoney($totals['t_net'] ?? 0) ?></h3>
                <p>Tổng thực nhận</p>
            </div>
        </div>
    </div>
</div>

<!-- Approve All -->
<?php if (($totals['total_count'] ?? 0) > 0): ?>
<div class="mb-3">
    <form method="POST" class="d-inline" onsubmit="return confirm('Duyệt tất cả bảng lương tháng <?= $month ?>/<?= $year ?>?')">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="approve_all">
        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-check-double me-1"></i>Duyệt tất cả</button>
    </form>
</div>
<?php endif; ?>

<!-- Bảng lương -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Mã NV</th>
                        <th>Họ tên</th>
                        <th>Phòng ban</th>
                        <th class="text-center">Ngày công</th>
                        <th class="text-end">Lương CB</th>
                        <th class="text-end">Phụ cấp</th>
                        <th class="text-end">Tăng ca</th>
                        <th class="text-end">Gross</th>
                        <th class="text-end">Khấu trừ</th>
                        <th class="text-end">Thực nhận</th>
                        <th class="text-center">TT</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($s = $salaries->fetch_assoc()):
                    $deductions = $s['bhxh'] + $s['bhyt'] + $s['bhtn'] + $s['tax'] + $s['advance_salary'];
                    $badge = 'badge-pending';
                    if ($s['status'] === 'Đã duyệt') $badge = 'badge-approved';
                    elseif ($s['status'] === 'Đã thanh toán') $badge = 'badge-paid';
                ?>
                    <tr>
                        <td><code><?= e($s['employee_code']) ?></code></td>
                        <td><?= e($s['full_name']) ?></td>
                        <td><?= e($s['dept_name'] ?? '-') ?></td>
                        <td class="text-center"><?= intval($s['actual_working_days']) ?>/<?= intval($s['working_days']) ?></td>
                        <td class="text-end"><?= formatNumber($s['base_salary']) ?></td>
                        <td class="text-end"><?= formatNumber($s['total_allowance']) ?></td>
                        <td class="text-end"><?= formatNumber($s['overtime_pay']) ?></td>
                        <td class="text-end"><strong><?= formatNumber($s['gross_salary']) ?></strong></td>
                        <td class="text-end text-money negative">-<?= formatNumber($deductions) ?></td>
                        <td class="text-end text-money positive"><strong><?= formatNumber($s['net_salary']) ?></strong></td>
                        <td class="text-center"><span class="badge-status <?= $badge ?>"><?= e($s['status']) ?></span></td>
                        <td class="text-center">
                            <a href="<?= url('salary/detail/' . $s['id']) ?>" class="btn btn-outline-info btn-action" title="Chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($s['status'] === 'Chờ duyệt'): ?>
                            <form method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button class="btn btn-outline-success btn-action" title="Duyệt"><i class="fas fa-check"></i></button>
                            </form>
                            <?php elseif ($s['status'] === 'Đã duyệt'): ?>
                            <form method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="mark_paid">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button class="btn btn-outline-primary btn-action" title="Thanh toán"><i class="fas fa-dollar-sign"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light" style="font-weight:bold;">
                        <td colspan="3">TỔNG CỘNG</td>
                        <td></td>
                        <td class="text-end"><?= formatNumber($totals['t_base'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_allowance'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_ot'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_gross'] ?? 0) ?></td>
                        <td class="text-end text-money negative">-<?= formatNumber(($totals['t_insurance'] ?? 0) + ($totals['t_tax'] ?? 0) + ($totals['t_advance'] ?? 0)) ?></td>
                        <td class="text-end text-money positive"><?= formatNumber($totals['t_net'] ?? 0) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
