<div class="mb-3 d-flex gap-2">
    <a href="<?= url('employees') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Quay lại
    </a>
    <?php if ($canEdit): ?>
    <a href="<?= url('employees/edit/' . $employee['id']) ?>" class="btn btn-outline-warning btn-sm">
        <i class="fas fa-edit me-1"></i>Chỉnh sửa
    </a>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Thông tin cá nhân -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="profile-header">
                    <div class="profile-avatar"><?= getInitials($employee['full_name']) ?></div>
                    <h3><?= e($employee['full_name']) ?></h3>
                    <p class="text-muted"><?= e($employee['employee_code']) ?> | <?= e($employee['pos_name'] ?? '-') ?></p>
                    <?php
                    $badge = 'badge-active';
                    if ($employee['status'] === 'Đã nghỉ') $badge = 'badge-inactive';
                    elseif ($employee['status'] === 'Thử việc') $badge = 'badge-pending';
                    ?>
                    <span class="badge-status <?= $badge ?>"><?= e($employee['status']) ?></span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-id-card"></i> Thông tin cá nhân</div>
            <div class="card-body">
                <?php
                $info = [
                    'Giới tính'   => $employee['gender'],
                    'Ngày sinh'   => formatDate($employee['birth_date']),
                    'CCCD/CMND'   => $employee['id_card'],
                    'SĐT'         => $employee['phone'],
                    'Email'        => $employee['email'],
                    'Địa chỉ'     => $employee['address'],
                ];
                foreach ($info as $label => $val): ?>
                <div class="info-row">
                    <div class="info-label"><?= $label ?></div>
                    <div class="info-value"><?= e($val ?: '-') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-briefcase"></i> Thông tin công việc</div>
            <div class="card-body">
                <?php
                $workInfo = [
                    'Phòng ban'   => $employee['dept_name'] ?? '-',
                    'Chức vụ'     => $employee['pos_name'] ?? '-',
                    'Ngày vào'    => !empty($employee['hire_date']) ? formatDate($employee['hire_date']) : '-',
                    'Hợp đồng'    => $employee['contract_type'] ?? '-',
                    'Lương CB'     => formatMoney($employee['base_salary'] ?? 0),
                    'Ngân hàng'   => (($employee['bank_name'] ?? '') ?: '-') . ' - ' . (($employee['bank_account'] ?? '') ?: '-'),
                ];
                foreach ($workInfo as $label => $val): ?>
                <div class="info-row">
                    <div class="info-label"><?= $label ?></div>
                    <div class="info-value"><?= e($val) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nghỉ phép -->
        <div class="card">
            <div class="card-header"><i class="fas fa-calendar-minus"></i> Phép năm <?= date('Y') ?></div>
            <div class="card-body text-center">
                <div class="d-flex justify-content-around">
                    <div>
                        <h4 class="text-primary"><?= $leaveBalance['annual'] ?></h4>
                        <small class="text-muted">Tổng phép</small>
                    </div>
                    <div>
                        <h4 class="text-warning"><?= $leaveBalance['used'] ?></h4>
                        <small class="text-muted">Đã dùng</small>
                    </div>
                    <div>
                        <h4 class="text-success"><?= $leaveBalance['remaining'] ?></h4>
                        <small class="text-muted">Còn lại</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết tabs -->
    <div class="col-md-8">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tabAllowance">Phụ cấp</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabReward">Khen thưởng/Kỷ luật</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabSalary">Lịch sử lương</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabLeave">Nghỉ phép</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabContract">Hợp đồng</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Phụ cấp -->
            <div class="tab-pane fade show active" id="tabAllowance">
                <div class="card border-top-0" style="border-radius:0 0 10px 10px;">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>Loại phụ cấp</th><th class="text-end">Số tiền</th></tr></thead>
                            <tbody>
                            <?php
                            $totalAllow = 0;
                            if ($allowances): while ($a = $allowances->fetch_assoc()):
                                $totalAllow += $a['amount'];
                            ?>
                                <tr>
                                    <td><?= e($a['allowance_name']) ?></td>
                                    <td class="text-end text-money"><?= formatMoney($a['amount']) ?></td>
                                </tr>
                            <?php endwhile; endif; ?>
                            <tr class="table-light">
                                <td><strong>Tổng phụ cấp</strong></td>
                                <td class="text-end text-money"><strong><?= formatMoney($totalAllow) ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Khen thưởng / Kỷ luật -->
            <div class="tab-pane fade" id="tabReward">
                <div class="card border-top-0" style="border-radius:0 0 10px 10px;">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>Ngày</th><th>Loại</th><th>Lý do</th><th class="text-end">Số tiền</th></tr></thead>
                            <tbody>
                            <?php if ($rewards): while ($r = $rewards->fetch_assoc()): ?>
                                <tr>
                                    <td><?= formatDate($r['date']) ?></td>
                                    <td>
                                        <span class="badge-status <?= $r['type'] === 'Khen thưởng' ? 'badge-active' : 'badge-inactive' ?>">
                                            <?= e($r['type']) ?>
                                        </span>
                                    </td>
                                    <td><?= e($r['reason']) ?></td>
                                    <td class="text-end text-money <?= $r['type'] === 'Khen thưởng' ? 'positive' : 'negative' ?>">
                                        <?= $r['type'] === 'Khen thưởng' ? '+' : '-' ?><?= formatMoney($r['amount']) ?>
                                    </td>
                                </tr>
                            <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lịch sử lương -->
            <div class="tab-pane fade" id="tabSalary">
                <div class="card border-top-0" style="border-radius:0 0 10px 10px;">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr><th>Kỳ lương</th><th class="text-end">Gross</th><th class="text-end">Khấu trừ</th>
                                <th class="text-end">Thực nhận</th><th class="text-center">Trạng thái</th><th></th></tr>
                            </thead>
                            <tbody>
                            <?php if ($salaryHistory): while ($s = $salaryHistory->fetch_assoc()):
                                $deductions = $s['bhxh'] + $s['bhyt'] + $s['bhtn'] + $s['tax'] + $s['advance_salary'];
                            ?>
                                <tr>
                                    <td>T<?= $s['month'] ?>/<?= $s['year'] ?></td>
                                    <td class="text-end text-money"><?= formatMoney($s['gross_salary']) ?></td>
                                    <td class="text-end text-money negative">-<?= formatMoney($deductions) ?></td>
                                    <td class="text-end text-money positive"><strong><?= formatMoney($s['net_salary']) ?></strong></td>
                                    <td class="text-center">
                                        <?php
                                        $sb = 'badge-pending';
                                        if ($s['status'] === 'Đã duyệt') $sb = 'badge-approved';
                                        elseif ($s['status'] === 'Đã thanh toán') $sb = 'badge-paid';
                                        ?>
                                        <span class="badge-status <?= $sb ?>"><?= e($s['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= url('salary/detail/' . $s['id']) ?>" class="btn btn-outline-info btn-action">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nghỉ phép -->
            <div class="tab-pane fade" id="tabLeave">
                <div class="card border-top-0" style="border-radius:0 0 10px 10px;">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>Loại</th><th>Từ ngày</th><th>Đến ngày</th><th class="text-center">Số ngày</th><th>Lý do</th><th class="text-center">Trạng thái</th></tr></thead>
                            <tbody>
                            <?php if ($leaves): while ($l = $leaves->fetch_assoc()): ?>
                                <tr>
                                    <td><?= e($l['leave_type']) ?></td>
                                    <td><?= formatDate($l['start_date']) ?></td>
                                    <td><?= formatDate($l['end_date']) ?></td>
                                    <td class="text-center"><?= $l['days'] ?></td>
                                    <td><?= e($l['reason']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $lb = 'badge-pending';
                                        if ($l['status'] === 'Đã duyệt') $lb = 'badge-approved';
                                        elseif ($l['status'] === 'Từ chối') $lb = 'badge-rejected';
                                        ?>
                                        <span class="badge-status <?= $lb ?>"><?= e($l['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hợp đồng -->
            <div class="tab-pane fade" id="tabContract">
                <div class="card border-top-0" style="border-radius:0 0 10px 10px;">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>Số HĐ</th><th>Loại</th><th>Từ ngày</th><th>Đến ngày</th><th class="text-end">Lương</th></tr></thead>
                            <tbody>
                            <?php if ($contracts): while ($c = $contracts->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?= e($c['contract_number']) ?></code></td>
                                    <td><?= e($c['contract_type']) ?></td>
                                    <td><?= formatDate($c['start_date']) ?></td>
                                    <td><?= formatDate($c['end_date']) ?></td>
                                    <td class="text-end text-money"><?= formatMoney($c['base_salary']) ?></td>
                                </tr>
                            <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
