<!-- Bộ lọc -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('rewards') ?>" class="filter-bar">
            <select name="type" class="form-select">
                <option value="">Tất cả loại</option>
                <option value="Khen thưởng" <?= $filters['type'] === 'Khen thưởng' ? 'selected' : '' ?>>Khen thưởng</option>
                <option value="Kỷ luật" <?= $filters['type'] === 'Kỷ luật' ? 'selected' : '' ?>>Kỷ luật</option>
            </select>
            <select name="month" class="form-select">
                <option value="">Tất cả tháng</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $filters['month'] == $m ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $filters['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Lọc</button>
            <a href="<?= url('rewards') ?>" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
        </form>
    </div>
</div>

<!-- Thống kê -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-trophy"></i></div>
            <div class="stat-info">
                <h3><?= $stats['reward_count'] ?? 0 ?></h3>
                <p>Lượt khen thưởng</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="stat-info">
                <h3><?= formatMoney($stats['reward_total'] ?? 0) ?></h3>
                <p>Tổng tiền thưởng</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info">
                <h3><?= $stats['disc_count'] ?? 0 ?></h3>
                <p>Lượt kỷ luật</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <h3><?= formatMoney($stats['disc_total'] ?? 0) ?></h3>
                <p>Tổng tiền kỷ luật</p>
            </div>
        </div>
    </div>
</div>

<!-- Bảng khen thưởng / kỷ luật -->
<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Danh sách khen thưởng & kỷ luật</h6>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#rewardModal" onclick="resetRewardForm()">
        <i class="fas fa-plus me-1"></i>Thêm mới
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ngày</th>
                        <th>Nhân viên</th>
                        <th class="text-center">Loại</th>
                        <th>Lý do</th>
                        <th>Số quyết định</th>
                        <th class="text-end">Số tiền</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($rewards && $rewards->num_rows > 0): ?>
                    <?php $i = 1; while ($r = $rewards->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= formatDate($r['date']) ?></td>
                        <td>
                            <strong><?= e($r['full_name']) ?></strong>
                            <br><small class="text-muted"><?= e($r['employee_code']) ?></small>
                        </td>
                        <td class="text-center">
                            <?php if ($r['type'] === 'Khen thưởng'): ?>
                                <span class="badge bg-success">Khen thưởng</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Kỷ luật</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($r['reason']) ?></td>
                        <td><?= e($r['decision_number'] ?? '-') ?></td>
                        <td class="text-end text-money"><?= formatMoney($r['amount']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-outline-warning btn-action" onclick="editReward(<?= htmlspecialchars(json_encode($r)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-outline-danger btn-action"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Không có dữ liệu</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="rewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="rAction" value="create">
                <input type="hidden" name="id" id="rId">
                <div class="modal-header">
                    <h5 class="modal-title" id="rTitle">Thêm khen thưởng / kỷ luật</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                        <select name="employee_id" id="rEmployee" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <?php while ($emp = $employees->fetch_assoc()): ?>
                                <option value="<?= $emp['id'] ?>"><?= e($emp['employee_code']) ?> - <?= e($emp['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại <span class="text-danger">*</span></label>
                        <select name="type" id="rType" class="form-select" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="Khen thưởng">Khen thưởng</option>
                            <option value="Kỷ luật">Kỷ luật</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <textarea name="reason" id="rReason" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số tiền</label>
                            <input type="text" name="amount" id="rAmount" class="form-control money-input" placeholder="VD: 1.000.000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="rDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số quyết định</label>
                        <input type="text" name="decision_number" id="rDecision" class="form-control" placeholder="VD: QĐ-001/2024">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetRewardForm() {
    document.getElementById('rAction').value = 'create';
    document.getElementById('rId').value = '';
    document.getElementById('rTitle').textContent = 'Thêm khen thưởng / kỷ luật';
    document.getElementById('rEmployee').value = '';
    document.getElementById('rType').value = '';
    document.getElementById('rReason').value = '';
    document.getElementById('rAmount').value = '';
    document.getElementById('rDate').value = '';
    document.getElementById('rDecision').value = '';
}
function editReward(r) {
    document.getElementById('rAction').value = 'update';
    document.getElementById('rId').value = r.id;
    document.getElementById('rTitle').textContent = 'Sửa khen thưởng / kỷ luật';
    document.getElementById('rEmployee').value = r.employee_id || '';
    document.getElementById('rType').value = r.type || '';
    document.getElementById('rReason').value = r.reason || '';
    document.getElementById('rAmount').value = r.amount ? Number(r.amount).toLocaleString('vi-VN') : '';
    document.getElementById('rDate').value = r.date || '';
    document.getElementById('rDecision').value = r.decision_number || '';
    new bootstrap.Modal(document.getElementById('rewardModal')).show();
}
</script>
