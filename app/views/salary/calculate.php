<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-calculator"></i> Tính lương hàng tháng</div>
            <div class="card-body">
                <form method="POST" action="<?= url('salary/calculate') ?>" onsubmit="return confirm('Bạn có chắc muốn tính lương? Hệ thống sẽ tính lương cho tất cả nhân viên đang làm việc.')">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Tháng</label>
                        <select name="month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Năm</label>
                        <select name="year" class="form-select">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="alert alert-info py-2" style="font-size:13px;">
                        <i class="fas fa-info-circle me-1"></i>
                        Hệ thống sẽ tự động tính lương dựa trên:
                        <ul class="mb-0 mt-1">
                            <li>Lương cơ bản theo chức vụ</li>
                            <li>Ngày công thực tế (từ bảng chấm công)</li>
                            <li>Phụ cấp được gán</li>
                            <li>Tăng ca (hệ số 1.5)</li>
                            <li>Khen thưởng / Kỷ luật</li>
                            <li>BHXH 8%, BHYT 1.5%, BHTN 1%</li>
                            <li>Thuế TNCN (giảm trừ bản thân 11.000.000 ₫)</li>
                            <li>Tạm ứng lương đã duyệt</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-calculator me-1"></i>Tính lương ngay
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
