<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu lương - <?= e($salary['full_name']) ?> - T<?= $salary['month'] ?>/<?= $salary['year'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 14px; color: #333; }
        .payslip { max-width: 800px; margin: 20px auto; padding: 30px; background: #fff; }
        .payslip-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .payslip-header h2 { font-size: 22px; margin: 5px 0; }
        .payslip-header h3 { font-size: 18px; color: #555; }
        .payslip-header p { font-size: 13px; color: #777; }
        .info-section { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-section .col { flex: 1; }
        .info-section table { width: 100%; }
        .info-section td { padding: 3px 5px; font-size: 13px; }
        .info-section td:first-child { font-weight: bold; width: 120px; }
        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .salary-table th, .salary-table td { border: 1px solid #ddd; padding: 8px 12px; font-size: 13px; }
        .salary-table th { background: #f5f5f5; text-align: left; }
        .salary-table .text-right { text-align: right; }
        .salary-table .total-row { background: #f0f0f0; font-weight: bold; }
        .salary-table .net-row { background: #e8edff; font-weight: bold; font-size: 15px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; }
        .signatures .sig { width: 200px; }
        .signatures .sig p { font-size: 13px; margin-top: 5px; }
        .no-print { text-align: center; margin: 20px; }
        @media print { .no-print { display: none; } body { margin: 0; } .payslip { margin: 0; box-shadow: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding:10px 30px;font-size:16px;cursor:pointer;background:#3b82f6;color:#fff;border:none;border-radius:8px;">
            In phiếu lương
        </button>
        <a href="<?= url('salary?month=' . $salary['month'] . '&year=' . $salary['year']) ?>" style="margin-left:10px;color:#3b82f6;">Quay lại bảng lương</a>
    </div>

    <div class="payslip">
        <div class="payslip-header">
            <h3>CÔNG TY TNHH ABC</h3>
            <p>Địa chỉ: 123 Nguyễn Huệ, Q.1, TP.HCM | ĐT: 028-1234-5678</p>
            <h2>PHIẾU LƯƠNG THÁNG <?= $salary['month'] ?>/<?= $salary['year'] ?></h2>
        </div>

        <div class="info-section">
            <div class="col">
                <table>
                    <tr><td>Mã NV:</td><td><?= e($salary['employee_code']) ?></td></tr>
                    <tr><td>Họ tên:</td><td><strong><?= e($salary['full_name']) ?></strong></td></tr>
                    <tr><td>Phòng ban:</td><td><?= e($salary['dept_name'] ?? '-') ?></td></tr>
                    <tr><td>Chức vụ:</td><td><?= e($salary['pos_name'] ?? '-') ?></td></tr>
                </table>
            </div>
            <div class="col">
                <table>
                    <tr><td>Hợp đồng:</td><td><?= e($salary['contract_type']) ?></td></tr>
                    <tr><td>Ngân hàng:</td><td><?= e($salary['bank_name']) ?></td></tr>
                    <tr><td>Số TK:</td><td><?= e($salary['bank_account']) ?></td></tr>
                    <tr><td>Ngày công:</td><td><?= $salary['actual_working_days'] ?>/<?= $salary['working_days'] ?> ngày</td></tr>
                </table>
            </div>
        </div>

        <table class="salary-table">
            <thead><tr><th colspan="2" style="text-align:center;background:#e8edff;">I. THU NHẬP</th></tr></thead>
            <tbody>
                <tr><td>1. Lương cơ bản</td><td class="text-right"><?= formatMoney($salary['base_salary']) ?></td></tr>
                <tr>
                    <td>2. Lương theo ngày công (<?= $salary['actual_working_days'] ?>/<?= $salary['working_days'] ?> ngày)</td>
                    <td class="text-right"><?php
                        $sbd = ($salary['working_days'] > 0) ? round($salary['base_salary'] * $salary['actual_working_days'] / $salary['working_days']) : $salary['base_salary'];
                        echo formatMoney($sbd);
                    ?></td>
                </tr>
                <tr><td>3. Phụ cấp</td><td class="text-right"><?= formatMoney($salary['total_allowance']) ?></td></tr>
                <?php if ($allowances): while ($a = $allowances->fetch_assoc()): ?>
                <tr><td style="padding-left:30px;color:#666;">- <?= e($a['allowance_name']) ?></td><td class="text-right" style="color:#666;"><?= formatMoney($a['amount']) ?></td></tr>
                <?php endwhile; endif; ?>
                <tr><td>4. Tăng ca (<?= $salary['overtime_hours'] ?> giờ)</td><td class="text-right"><?= formatMoney($salary['overtime_pay']) ?></td></tr>
                <tr><td>5. Khen thưởng</td><td class="text-right"><?= formatMoney($salary['total_reward']) ?></td></tr>
                <tr><td>6. Kỷ luật (trừ)</td><td class="text-right">-<?= formatMoney($salary['total_discipline']) ?></td></tr>
                <tr class="total-row"><td><strong>TỔNG THU NHẬP (Gross)</strong></td><td class="text-right"><strong><?= formatMoney($salary['gross_salary']) ?></strong></td></tr>
            </tbody>
        </table>

        <table class="salary-table">
            <thead><tr><th colspan="2" style="text-align:center;background:#ffe8e8;">II. KHẤU TRỪ</th></tr></thead>
            <tbody>
                <tr><td>1. BHXH (8%)</td><td class="text-right">-<?= formatMoney($salary['bhxh']) ?></td></tr>
                <tr><td>2. BHYT (1.5%)</td><td class="text-right">-<?= formatMoney($salary['bhyt']) ?></td></tr>
                <tr><td>3. BHTN (1%)</td><td class="text-right">-<?= formatMoney($salary['bhtn']) ?></td></tr>
                <tr><td>4. Thuế TNCN</td><td class="text-right">-<?= formatMoney($salary['tax']) ?></td></tr>
                <?php if ($salary['advance_salary'] > 0): ?>
                <tr><td>5. Tạm ứng lương</td><td class="text-right">-<?= formatMoney($salary['advance_salary']) ?></td></tr>
                <?php endif; ?>
                <tr class="total-row"><td><strong>TỔNG KHẤU TRỪ</strong></td><td class="text-right"><strong>-<?= formatMoney($totalDeductions) ?></strong></td></tr>
            </tbody>
        </table>

        <table class="salary-table">
            <tbody>
                <tr class="net-row">
                    <td style="font-size:15px;">III. THỰC NHẬN (I - II)</td>
                    <td class="text-right" style="font-size:18px;color:#3b82f6;"><?= formatMoney($salary['net_salary']) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig"><strong>Người nhận</strong><p>(Ký, ghi rõ họ tên)</p><br><br><br><p><?= e($salary['full_name']) ?></p></div>
            <div class="sig"><strong>Kế toán</strong><p>(Ký, ghi rõ họ tên)</p></div>
            <div class="sig"><strong>Giám đốc</strong><p>(Ký, đóng dấu)</p></div>
        </div>
    </div>
</body>
</html>
