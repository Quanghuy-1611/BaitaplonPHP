<?php $isEmployee = $isEmployee ?? false; $noData = $noData ?? false; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-file-invoice-dollar"></i> <?= $isEmployee ? 'Phiếu lương' : 'Chọn phiếu lương để in' ?></div>
            <div class="card-body">
                <?php if ($isEmployee && $noData): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có phiếu lương cho tháng này.</p>
                        <a href="<?= url('dashboard') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Về trang chính
                        </a>
                    </div>
                <?php elseif ($isEmployee): ?>
                    <form method="GET" action="<?= url('salary/payslip') ?>">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Tháng</label>
                                <select name="month" class="form-select">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Năm</label>
                                <select name="year" class="form-select">
                                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                        <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-search me-1"></i>Xem phiếu lương
                        </button>
                    </form>
                <?php else: ?>
                <form method="GET" action="<?= url('salary/payslip') ?>">
                    <div class="mb-3">
                        <label class="form-label">Nhân viên</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <?php if ($employees): while ($emp = $employees->fetch_assoc()): ?>
                                <option value="<?= $emp['id'] ?>"><?= e($emp['employee_code']) ?> - <?= e($emp['full_name']) ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Tháng</label>
                            <select name="month" class="form-select">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Năm</label>
                            <select name="year" class="form-select">
                                <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                    <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-search me-1"></i>Xem phiếu lương
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
