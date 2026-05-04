<!-- Chọn loại báo cáo -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('reports') ?>" class="filter-bar">
            <select name="type" class="form-select">
                <option value="salary" <?= $reportType === 'salary' ? 'selected' : '' ?>>Báo cáo lương theo tháng</option>
                <option value="department" <?= $reportType === 'department' ? 'selected' : '' ?>>Báo cáo lương theo phòng ban</option>
                <option value="attendance" <?= $reportType === 'attendance' ? 'selected' : '' ?>>Báo cáo chấm công</option>
                <option value="employee" <?= $reportType === 'employee' ? 'selected' : '' ?>>Thống kê nhân sự</option>
            </select>
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
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-chart-bar me-1"></i>Xem báo cáo</button>
        </form>
    </div>
</div>

<?php if ($reportType === 'salary'): ?>
<!-- BÁO CÁO LƯƠNG THEO THÁNG -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="fas fa-table"></i> Báo cáo lương tháng <?= $month ?>/<?= $year ?></span>
        <span class="text-muted">Tổng: <?= $totals['t_count'] ?? 0 ?> nhân viên</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Mã NV</th><th>Họ tên</th><th>Phòng ban</th>
                        <th class="text-end">Lương CB</th><th class="text-end">Phụ cấp</th>
                        <th class="text-end">Tăng ca</th><th class="text-end">Thưởng</th>
                        <th class="text-end">Phạt</th><th class="text-end">Gross</th>
                        <th class="text-end">BH</th><th class="text-end">Thuế</th>
                        <th class="text-end">Tạm ứng</th><th class="text-end">Thực nhận</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($salaryReport): while ($r = $salaryReport->fetch_assoc()):
                    $ins = $r['bhxh'] + $r['bhyt'] + $r['bhtn'];
                ?>
                    <tr>
                        <td><code><?= e($r['employee_code']) ?></code></td>
                        <td><?= e($r['full_name']) ?></td>
                        <td><?= e($r['dept_name'] ?? '-') ?></td>
                        <td class="text-end"><?= formatNumber($r['base_salary']) ?></td>
                        <td class="text-end"><?= formatNumber($r['total_allowance']) ?></td>
                        <td class="text-end"><?= formatNumber($r['overtime_pay']) ?></td>
                        <td class="text-end"><?= formatNumber($r['total_reward']) ?></td>
                        <td class="text-end"><?= formatNumber($r['total_discipline']) ?></td>
                        <td class="text-end"><strong><?= formatNumber($r['gross_salary']) ?></strong></td>
                        <td class="text-end"><?= formatNumber($ins) ?></td>
                        <td class="text-end"><?= formatNumber($r['tax']) ?></td>
                        <td class="text-end"><?= formatNumber($r['advance_salary']) ?></td>
                        <td class="text-end text-money positive"><strong><?= formatNumber($r['net_salary']) ?></strong></td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light" style="font-weight:bold;">
                        <td colspan="3">TỔNG CỘNG</td>
                        <td class="text-end"><?= formatNumber($totals['t_base'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_allowance'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_ot'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_reward'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_disc'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_gross'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_insurance'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_tax'] ?? 0) ?></td>
                        <td class="text-end"><?= formatNumber($totals['t_advance'] ?? 0) ?></td>
                        <td class="text-end text-money positive"><?= formatNumber($totals['t_net'] ?? 0) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php elseif ($reportType === 'department'): ?>
<!-- BÁO CÁO LƯƠNG THEO PHÒNG BAN -->
<div class="card">
    <div class="card-header"><i class="fas fa-building"></i> Báo cáo lương theo phòng ban - Tháng <?= $month ?>/<?= $year ?></div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th>Phòng ban</th><th class="text-center">Số NV</th><th class="text-end">Tổng Gross</th>
                <th class="text-end">Tổng thực nhận</th><th class="text-end">TB thực nhận</th>
                <th class="text-end">Cao nhất</th><th class="text-end">Thấp nhất</th></tr>
            </thead>
            <tbody>
            <?php
            $chartItems = [];
            $maxVal = 0;
            if ($deptReport): while ($d = $deptReport->fetch_assoc()):
                $chartItems[] = $d;
                if ($d['total_net'] > $maxVal) $maxVal = $d['total_net'];
            ?>
                <tr>
                    <td><strong><?= e($d['dept_name'] ?? 'Chưa phân phòng') ?></strong></td>
                    <td class="text-center"><?= $d['emp_count'] ?></td>
                    <td class="text-end text-money"><?= formatMoney($d['total_gross']) ?></td>
                    <td class="text-end text-money"><?= formatMoney($d['total_net']) ?></td>
                    <td class="text-end text-money"><?= formatMoney(round($d['avg_net'])) ?></td>
                    <td class="text-end text-money positive"><?= formatMoney($d['max_net']) ?></td>
                    <td class="text-end text-money"><?= formatMoney($d['min_net']) ?></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Biểu đồ -->
<div class="card mt-3">
    <div class="card-header"><i class="fas fa-chart-bar"></i> Biểu đồ chi phí lương theo phòng ban</div>
    <div class="card-body">
        <?php foreach ($chartItems as $item):
            $width = $maxVal > 0 ? ($item['total_net'] / $maxVal * 100) : 0;
        ?>
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:13px;font-weight:600;"><?= e($item['dept_name'] ?? 'Khác') ?></span>
                <span style="font-size:13px;"><?= formatMoney($item['total_net']) ?></span>
            </div>
            <div class="chart-bar-track">
                <div class="chart-bar-fill" style="width:<?= $width ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php elseif ($reportType === 'attendance'): ?>
<!-- BÁO CÁO CHẤM CÔNG -->
<div class="card">
    <div class="card-header"><i class="fas fa-calendar-check"></i> Báo cáo chấm công tháng <?= $month ?>/<?= $year ?></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Mã NV</th><th>Họ tên</th><th>Phòng ban</th>
                    <th class="text-center">Đi làm</th><th class="text-center">Nghỉ phép</th>
                    <th class="text-center">Nghỉ KP</th><th class="text-center">Nghỉ lễ</th>
                    <th class="text-center">Tăng ca (h)</th><th class="text-center">Tổng</th></tr>
                </thead>
                <tbody>
                <?php if ($attReport): while ($a = $attReport->fetch_assoc()):
                    $total = $a['di_lam'] + $a['nghi_phep'] + $a['nghi_kp'] + $a['nghi_le'];
                ?>
                    <tr>
                        <td><code><?= e($a['employee_code']) ?></code></td>
                        <td><?= e($a['full_name']) ?></td>
                        <td><?= e($a['dept_name'] ?? '-') ?></td>
                        <td class="text-center"><strong><?= $a['di_lam'] ?></strong></td>
                        <td class="text-center"><?= $a['nghi_phep'] ?: '-' ?></td>
                        <td class="text-center <?= $a['nghi_kp'] > 0 ? 'text-danger' : '' ?>"><?= $a['nghi_kp'] ?: '-' ?></td>
                        <td class="text-center"><?= $a['nghi_le'] ?: '-' ?></td>
                        <td class="text-center"><?= $a['total_ot'] > 0 ? $a['total_ot'] : '-' ?></td>
                        <td class="text-center"><strong><?= $total ?></strong></td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php elseif ($reportType === 'employee'): ?>
<!-- THỐNG KÊ NHÂN SỰ -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-info"><h3><?= $empStats['total_active'] ?></h3><p>Nhân viên đang làm</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-money-bill"></i></div>
            <div class="stat-info"><h3><?= formatMoney(round($empStats['avg_salary'])) ?></h3><p>Lương CB trung bình</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-coins"></i></div>
            <div class="stat-info"><h3><?= formatMoney($empStats['total_salary_fund']) ?></h3><p>Tổng quỹ lương CB</p></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-building"></i> Nhân viên theo phòng ban</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Phòng ban</th><th class="text-center">Số nhân viên</th></tr></thead>
                    <tbody>
                    <?php while ($d = $empStats['by_department']->fetch_assoc()): ?>
                        <tr><td><?= e($d['name']) ?></td><td class="text-center"><span class="badge bg-primary rounded-pill"><?= $d['cnt'] ?></span></td></tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header"><i class="fas fa-venus-mars"></i> Theo giới tính</div>
            <div class="card-body p-0">
                <table class="table mb-0"><tbody>
                <?php while ($g = $empStats['by_gender']->fetch_assoc()): ?>
                    <tr><td><?= e($g['gender']) ?></td><td class="text-center"><strong><?= $g['cnt'] ?></strong></td></tr>
                <?php endwhile; ?>
                </tbody></table>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header"><i class="fas fa-file-contract"></i> Theo hợp đồng</div>
            <div class="card-body p-0">
                <table class="table mb-0"><tbody>
                <?php while ($c = $empStats['by_contract']->fetch_assoc()): ?>
                    <tr><td><?= e($c['contract_type']) ?></td><td class="text-center"><strong><?= $c['cnt'] ?></strong></td></tr>
                <?php endwhile; ?>
                </tbody></table>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle"></i> Theo trạng thái</div>
            <div class="card-body p-0">
                <table class="table mb-0"><tbody>
                <?php while ($st = $empStats['by_status']->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php $sc = $st['status'] == 'Đang làm' ? 'badge-active' : ($st['status'] == 'Đã nghỉ' ? 'badge-inactive' : 'badge-pending'); ?>
                            <span class="badge-status <?= $sc ?>"><?= e($st['status']) ?></span>
                        </td>
                        <td class="text-center"><strong><?= $st['cnt'] ?></strong></td>
                    </tr>
                <?php endwhile; ?>
                </tbody></table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
