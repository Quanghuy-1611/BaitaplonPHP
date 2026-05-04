<!-- Bộ lọc -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('salary/advance') ?>" class="filter-bar">
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
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#advModal">
                <i class="fas fa-plus me-1"></i>Tạo phiếu tạm ứng
            </button>
        </form>
    </div>
</div>

<!-- Danh sách tạm ứng -->
<div class="card">
    <div class="card-header"><i class="fas fa-hand-holding-usd"></i> Danh sách tạm ứng lương</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Phòng ban</th>
                    <th class="text-center">Kỳ</th>
                    <th class="text-end">Số tiền</th>
                    <th>Lý do</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($a = $advances->fetch_assoc()):
                $sb = 'badge-pending';
                if ($a['status'] === 'Đã duyệt') $sb = 'badge-approved';
                elseif ($a['status'] === 'Từ chối') $sb = 'badge-rejected';
            ?>
                <tr>
                    <td>
                        <strong><?= e($a['full_name']) ?></strong><br>
                        <small class="text-muted"><?= e($a['employee_code']) ?></small>
                    </td>
                    <td><?= e($a['dept_name'] ?? '-') ?></td>
                    <td class="text-center">T<?= $a['month'] ?>/<?= $a['year'] ?></td>
                    <td class="text-end text-money"><?= formatMoney($a['amount']) ?></td>
                    <td><?= e($a['reason']) ?></td>
                    <td class="text-center"><span class="badge-status <?= $sb ?>"><?= e($a['status']) ?></span></td>
                    <td class="text-center">
                        <?php if ($a['status'] === 'Chờ duyệt'): ?>
                        <form method="POST" class="d-inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button class="btn btn-outline-success btn-action" title="Duyệt"><i class="fas fa-check"></i></button>
                        </form>
                        <form method="POST" class="d-inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button class="btn btn-outline-danger btn-action" title="Từ chối"><i class="fas fa-times"></i></button>
                        </form>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tạo tạm ứng -->
<div class="modal fade" id="advModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo phiếu tạm ứng lương</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <?php while ($emp = $employees->fetch_assoc()): ?>
                                <option value="<?= $emp['id'] ?>"><?= e($emp['employee_code']) ?> - <?= e($emp['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Tháng</label>
                            <select name="month" class="form-select">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Năm</label>
                            <select name="year" class="form-select">
                                <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Số tiền tạm ứng <span class="text-danger">*</span></label>
                        <input type="text" name="amount" class="form-control money-input" required placeholder="VD: 5.000.000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">Tạo phiếu</button>
                </div>
            </form>
        </div>
    </div>
</div>
