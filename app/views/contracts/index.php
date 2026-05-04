<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý hợp đồng</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contractModal"
            onclick="openContractModal()">
            <i class="fas fa-plus me-1"></i> Thêm hợp đồng
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('contracts') ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Còn hiệu lực</option>
                        <option value="expired" <?= $filters['status'] === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                        <option value="expiring" <?= $filters['status'] === 'expiring' ? 'selected' : '' ?>>Sắp hết hạn</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Loại hợp đồng</label>
                    <select name="contract_type" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($contractTypes as $type): ?>
                            <option value="<?= e($type) ?>" <?= $filters['contract_type'] === $type ? 'selected' : '' ?>>
                                <?= e($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Còn hiệu lực</div>
                            <div class="h2 mb-0"><?= $stats['active'] ?></div>
                        </div>
                        <div class="text-success"><i class="fas fa-file-contract fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Hết hạn</div>
                            <div class="h2 mb-0"><?= $stats['expired'] ?></div>
                        </div>
                        <div class="text-danger"><i class="fas fa-file-excel fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Sắp hết hạn</div>
                            <div class="h2 mb-0"><?= $stats['expiring'] ?></div>
                        </div>
                        <div class="text-warning"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nhân viên</th>
                        <th>Số hợp đồng</th>
                        <th>Loại HĐ</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th class="text-end">Lương cơ bản</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$contracts || ($contracts instanceof mysqli_result && $contracts->num_rows === 0)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Không có dữ liệu hợp đồng.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($contract = $contracts->fetch_assoc()): ?>
                            <?php
                                $today = date('Y-m-d');
                                $endDate = $contract['end_date'];
                                $daysLeft = (strtotime($endDate) - strtotime($today)) / 86400;

                                if ($endDate < $today) {
                                    $statusClass = 'bg-danger';
                                    $statusText = 'Hết hạn';
                                } elseif ($daysLeft <= 30) {
                                    $statusClass = 'bg-warning text-dark';
                                    $statusText = 'Sắp hết hạn';
                                } else {
                                    $statusClass = 'bg-success';
                                    $statusText = 'Còn hiệu lực';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($contract['employee_code']) ?></div>
                                    <div class="text-muted small"><?= e($contract['full_name']) ?></div>
                                </td>
                                <td><?= e($contract['contract_number']) ?></td>
                                <td><?= e($contract['contract_type']) ?></td>
                                <td><?= formatDate($contract['start_date']) ?></td>
                                <td><?= formatDate($contract['end_date']) ?></td>
                                <td class="text-end"><?= formatMoney($contract['base_salary']) ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Sửa"
                                        onclick="editContract(<?= htmlspecialchars(json_encode($contract), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="<?= url('contracts') ?>" class="d-inline">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $contract['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                            onclick="return confirm('Bạn có chắc muốn xóa hợp đồng này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Contract Modal (Add/Edit) -->
<div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= url('contracts') ?>" id="contractForm">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="contractAction" value="create">
                <input type="hidden" name="id" id="contractId">
                <div class="modal-header">
                    <h5 class="modal-title" id="contractModalLabel">Thêm hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                            <select name="employee_id" id="contractEmployeeId" class="form-select" required>
                                <option value="">-- Chọn nhân viên --</option>
                                <?php while ($emp = $employees->fetch_assoc()): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= e($emp['employee_code']) ?> - <?= e($emp['full_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số hợp đồng <span class="text-danger">*</span></label>
                            <input type="text" name="contract_number" id="contractNumber" class="form-control"
                                placeholder="VD: HD-2024-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                            <select name="contract_type" id="contractType" class="form-select" required>
                                <option value="">-- Chọn loại --</option>
                                <?php foreach ($contractTypes as $type): ?>
                                    <option value="<?= e($type) ?>"><?= e($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lương cơ bản <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="base_salary" id="contractSalary" class="form-control money-input"
                                    placeholder="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="contractStartDate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="contractEndDate" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" id="contractNote" class="form-control" rows="3"
                                placeholder="Nhập ghi chú..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="contractSubmitBtn">
                        <i class="fas fa-save me-1"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Money input formatting
    document.querySelectorAll('.money-input').forEach(function (input) {
        input.addEventListener('input', function () {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                value = parseInt(value, 10).toLocaleString('vi-VN');
            }
            this.value = value;
        });
    });
});

function openContractModal() {
    document.getElementById('contractAction').value = 'create';
    document.getElementById('contractId').value = '';
    document.getElementById('contractModalLabel').textContent = 'Thêm hợp đồng';
    document.getElementById('contractSubmitBtn').innerHTML = '<i class="fas fa-save me-1"></i> Lưu';
    document.getElementById('contractForm').reset();

    var modal = new bootstrap.Modal(document.getElementById('contractModal'));
    modal.show();
}

function editContract(contract) {
    document.getElementById('contractAction').value = 'update';
    document.getElementById('contractId').value = contract.id;
    document.getElementById('contractModalLabel').textContent = 'Cập nhật hợp đồng';
    document.getElementById('contractSubmitBtn').innerHTML = '<i class="fas fa-save me-1"></i> Cập nhật';

    document.getElementById('contractEmployeeId').value = contract.employee_id;
    document.getElementById('contractNumber').value = contract.contract_number;
    document.getElementById('contractType').value = contract.contract_type;
    document.getElementById('contractStartDate').value = contract.start_date;
    document.getElementById('contractEndDate').value = contract.end_date;
    document.getElementById('contractNote').value = contract.note || '';

    // Format salary
    var salary = parseInt(contract.base_salary, 10);
    document.getElementById('contractSalary').value = salary ? salary.toLocaleString('vi-VN') : '';

    var modal = new bootstrap.Modal(document.getElementById('contractModal'));
    modal.show();
}
</script>
