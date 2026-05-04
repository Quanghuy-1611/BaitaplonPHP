<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Tổng: <?= $result['total'] ?> nhân viên</h6>
    <?php if ($canEdit): ?>
    <a href="<?= url('employees/create') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-user-plus me-1"></i>Thêm nhân viên
    </a>
    <?php endif; ?>
</div>

<!-- Bộ lọc -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('employees') ?>" class="filter-bar">
            <div class="search-box">
                <input type="text" name="search" class="form-control" placeholder="Tìm tên, mã NV, SĐT..."
                       value="<?= e($filters['search']) ?>">
            </div>
            <select name="department_id" class="form-select">
                <option value="">Tất cả phòng ban</option>
                <?php while ($dept = $departments->fetch_assoc()): ?>
                    <option value="<?= $dept['id'] ?>" <?= $filters['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                        <?= e($dept['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="Đang làm" <?= $filters['status'] === 'Đang làm' ? 'selected' : '' ?>>Đang làm</option>
                <option value="Đã nghỉ" <?= $filters['status'] === 'Đã nghỉ' ? 'selected' : '' ?>>Đã nghỉ</option>
                <option value="Thử việc" <?= $filters['status'] === 'Thử việc' ? 'selected' : '' ?>>Thử việc</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Lọc</button>
            <a href="<?= url('employees') ?>" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
        </form>
    </div>
</div>

<!-- Bảng nhân viên -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Chức vụ</th>
                        <th>SĐT</th>
                        <th class="text-end">Lương CB</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($e = $result['data']->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="employee-info">
                                <div class="employee-avatar"><?= getInitials($e['full_name']) ?></div>
                                <div>
                                    <div class="employee-name">
                                        <a href="<?= url('employees/show/' . $e['id']) ?>"><?= e($e['full_name']) ?></a>
                                    </div>
                                    <div class="employee-code"><?= e($e['employee_code']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= e($e['dept_name'] ?? '-') ?></td>
                        <td><?= e($e['pos_name'] ?? '-') ?></td>
                        <td><?= e($e['phone']) ?></td>
                        <td class="text-end text-money"><?= formatMoney($e['base_salary']) ?></td>
                        <td class="text-center">
                            <?php
                            $badge = 'badge-active';
                            if ($e['status'] === 'Đã nghỉ') $badge = 'badge-inactive';
                            elseif ($e['status'] === 'Thử việc') $badge = 'badge-pending';
                            ?>
                            <span class="badge-status <?= $badge ?>"><?= e($e['status']) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="<?= url('employees/show/' . $e['id']) ?>" class="btn btn-outline-info btn-action" title="Xem">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($canEdit): ?>
                            <a href="<?= url('employees/edit/' . $e['id']) ?>" class="btn btn-outline-warning btn-action" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= url('employees/delete/' . $e['id']) ?>" class="btn btn-outline-danger btn-action"
                               onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Phân trang -->
<?php if ($result['totalPages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php
        $queryParams = $filters;
        for ($p = 1; $p <= $result['totalPages']; $p++):
            $queryParams['page'] = $p;
            $qs = http_build_query(array_filter($queryParams));
        ?>
            <li class="page-item <?= $p == $result['page'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= url('employees?' . $qs) ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
