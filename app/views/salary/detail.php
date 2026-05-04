<div class="mb-3 no-print">
    <a href="<?= url('salary?month=' . $salary['month'] . '&year=' . $salary['year']) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
    <a href="<?= url('salary/payslip/' . $salary['id']) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
        <i class="fas fa-print"></i> In phiếu lương
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-user"></i> Thông tin nhân viên</div>
            <div class="card-body">
                <?php
                $infoFields = [
                    'Mã NV'     => '<code>' . e($salary['employee_code']) . '</code>',
                    'Họ tên'    => '<strong>' . e($salary['full_name']) . '</strong>',
                    'Phòng ban' => e($salary['dept_name'] ?? '-'),
                    'Chức vụ'   => e($salary['pos_name'] ?? '-'),
                    'Hợp đồng'  => e($salary['contract_type']),
                    'Ngân hàng' => e($salary['bank_name']) . ' - ' . e($salary['bank_account']),
                    'Kỳ lương'  => '<strong>Tháng ' . $salary['month'] . '/' . $salary['year'] . '</strong>',
                ];
                foreach ($infoFields as $label => $val): ?>
                <div class="info-row">
                    <div class="info-label"><?= $label ?></div>
                    <div class="info-value"><?= $val ?></div>
                </div>
                <?php endforeach; ?>
                <div class="info-row">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <?php
                        $badge = 'badge-pending';
                        if ($salary['status'] === 'Đã duyệt') $badge = 'badge-approved';
                        elseif ($salary['status'] === 'Đã thanh toán') $badge = 'badge-paid';
                        ?>
                        <span class="badge-status <?= $badge ?>"><?= e($salary['status']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Thu nhập -->
        <div class="card">
            <div class="card-header"><i class="fas fa-plus-circle text-success"></i> Chi tiết thu nhập</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td>Lương cơ bản</td>
                            <td class="text-end text-money"><?= formatMoney($salary['base_salary']) ?></td>
                        </tr>
                        <tr>
                            <td>Ngày công chuẩn / Thực tế</td>
                            <td class="text-end"><?= $salary['working_days'] ?> / <strong><?= $salary['actual_working_days'] ?> ngày</strong></td>
                        </tr>
                        <tr>
                            <td>Lương theo ngày công</td>
                            <td class="text-end text-money">
                                <?php
                                $salaryByDays = ($salary['working_days'] > 0)
                                    ? round($salary['base_salary'] * $salary['actual_working_days'] / $salary['working_days'])
                                    : $salary['base_salary'];
                                echo formatMoney($salaryByDays);
                                ?>
                            </td>
                        </tr>
                        <tr class="table-light"><td colspan="2"><strong>Phụ cấp</strong></td></tr>
                        <?php if ($allowances): while ($a = $allowances->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4"><?= e($a['allowance_name']) ?></td>
                            <td class="text-end text-money"><?= formatMoney($a['amount']) ?></td>
                        </tr>
                        <?php endwhile; endif; ?>
                        <tr>
                            <td><strong>Tổng phụ cấp</strong></td>
                            <td class="text-end text-money"><strong><?= formatMoney($salary['total_allowance']) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Tăng ca (<?= $salary['overtime_hours'] ?> giờ x 1.5)</td>
                            <td class="text-end text-money"><?= formatMoney($salary['overtime_pay']) ?></td>
                        </tr>
                        <tr>
                            <td>Khen thưởng</td>
                            <td class="text-end text-money positive">+<?= formatMoney($salary['total_reward']) ?></td>
                        </tr>
                        <tr>
                            <td>Kỷ luật</td>
                            <td class="text-end text-money negative">-<?= formatMoney($salary['total_discipline']) ?></td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>TỔNG THU NHẬP (Gross)</strong></td>
                            <td class="text-end text-money"><strong style="font-size:15px;"><?= formatMoney($salary['gross_salary']) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Khấu trừ -->
        <div class="card">
            <div class="card-header"><i class="fas fa-minus-circle text-danger"></i> Chi tiết khấu trừ</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr><td>BHXH (8%)</td><td class="text-end text-money negative">-<?= formatMoney($salary['bhxh']) ?></td></tr>
                        <tr><td>BHYT (1.5%)</td><td class="text-end text-money negative">-<?= formatMoney($salary['bhyt']) ?></td></tr>
                        <tr><td>BHTN (1%)</td><td class="text-end text-money negative">-<?= formatMoney($salary['bhtn']) ?></td></tr>
                        <tr><td>Thuế TNCN</td><td class="text-end text-money negative">-<?= formatMoney($salary['tax']) ?></td></tr>
                        <?php if ($salary['advance_salary'] > 0): ?>
                        <tr><td>Tạm ứng lương</td><td class="text-end text-money negative">-<?= formatMoney($salary['advance_salary']) ?></td></tr>
                        <?php endif; ?>
                        <?php if ($salary['other_deduction'] > 0): ?>
                        <tr><td>Khấu trừ khác</td><td class="text-end text-money negative">-<?= formatMoney($salary['other_deduction']) ?></td></tr>
                        <?php endif; ?>
                        <tr class="table-danger">
                            <td><strong>TỔNG KHẤU TRỪ</strong></td>
                            <td class="text-end text-money negative"><strong style="font-size:15px;">-<?= formatMoney($totalDeductions) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Thực nhận -->
        <div class="card" style="border: 2px solid #3b82f6;">
            <div class="card-body text-center py-4">
                <h5 class="text-muted mb-1">LƯƠNG THỰC NHẬN</h5>
                <h2 style="color: #3b82f6; font-size: 32px; font-weight: 700;">
                    <?= formatMoney($salary['net_salary']) ?>
                </h2>
            </div>
        </div>
    </div>
</div>
