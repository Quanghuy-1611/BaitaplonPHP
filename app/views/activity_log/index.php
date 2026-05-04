<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-history"></i> Nhật ký hoạt động</span>
        <span class="badge bg-secondary"><?= ($logs instanceof mysqli_result) ? $logs->num_rows : 0 ?> bản ghi</span>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" action="<?= url('activity-log') ?>" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả người dùng --</option>
                    <?php if ($users): foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= e($u['full_name']) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="module" class="form-select form-select-sm">
                    <option value="">-- Module --</option>
                    <?php if ($modules): foreach ($modules as $m): ?>
                        <option value="<?= e($m['module']) ?>" <?= ($filters['module'] ?? '') == $m['module'] ? 'selected' : '' ?>>
                            <?= e($m['module']) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($filters['date_from'] ?? '') ?>" placeholder="Từ ngày">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($filters['date_to'] ?? '') ?>" placeholder="Đến ngày">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Lọc</button>
                <a href="<?= url('activity-log') ?>" class="btn btn-outline-secondary btn-sm">Xoá lọc</a>
            </div>
        </form>

        <!-- Bảng dữ liệu -->
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="160">Thời gian</th>
                        <th width="140">Người dùng</th>
                        <th width="100">Module</th>
                        <th>Hành động</th>
                        <th width="120">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$logs || ($logs instanceof mysqli_result && $logs->num_rows === 0)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">Không có dữ liệu</td></tr>
                    <?php else: ?>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><small><?= formatDate($log['created_at'], 'd/m/Y H:i') ?></small></td>
                                <td><?= e($log['full_name'] ?? $log['username'] ?? '-') ?></td>
                                <td><span class="badge bg-info"><?= e($log['module']) ?></span></td>
                                <td><?= e($log['action']) ?></td>
                                <td><small class="text-muted"><?= e($log['ip_address'] ?? '') ?></small></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
