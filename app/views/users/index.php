<div class="d-flex justify-content-between mb-3">
    <h6 class="text-muted mb-0">Quản lý tài khoản hệ thống</h6>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetUserForm()">
        <i class="fas fa-plus me-1"></i>Thêm tài khoản
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th class="text-center">Vai trò</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><code><?= e($u['username']) ?></code></td>
                    <td><strong><?= e($u['full_name']) ?></strong></td>
                    <td><?= e($u['email'] ?? '-') ?></td>
                    <td class="text-center">
                        <?php
                        $rb = 'badge-approved';
                        if ($u['role'] === 'hr') $rb = 'badge-active';
                        elseif ($u['role'] === 'accountant') $rb = 'badge-pending';
                        ?>
                        <span class="badge-status <?= $rb ?>"><?= roleName($u['role']) ?></span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-outline-warning btn-action" onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn btn-outline-danger btn-action"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><i class="fas fa-info-circle"></i> Phân quyền vai trò</div>
    <div class="card-body">
        <table class="table table-sm mb-0" style="font-size:13px;">
            <thead>
                <tr><th>Chức năng</th><th class="text-center">Quản trị viên</th><th class="text-center">Nhân sự</th><th class="text-center">Kế toán</th></tr>
            </thead>
            <tbody>
                <tr><td>Dashboard</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td></tr>
                <tr><td>Phòng ban / Chức vụ</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td></tr>
                <tr><td>Nhân viên (CRUD)</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-warning"><i class="fas fa-eye"></i> Xem</td></tr>
                <tr><td>Chấm công / Nghỉ phép</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td></tr>
                <tr><td>Phụ cấp / Khen thưởng</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-warning"><i class="fas fa-eye"></i> Xem</td></tr>
                <tr><td>Lương / Tạm ứng</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td></tr>
                <tr><td>Hợp đồng</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td></tr>
                <tr><td>Báo cáo</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-success"><i class="fas fa-check"></i></td></tr>
                <tr><td>Quản lý tài khoản</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td></tr>
                <tr><td>Nhật ký hoạt động</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="uAction" value="create">
                <input type="hidden" name="id" id="uId">
                <div class="modal-header">
                    <h5 class="modal-title" id="uTitle">Thêm tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="uUsername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span id="pwNote" class="text-danger">*</span></label>
                        <input type="password" name="password" id="uPassword" class="form-control">
                        <small class="text-muted" id="pwHint" style="display:none;">Để trống nếu không đổi mật khẩu</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" id="uFullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="uEmail" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" id="uRole" class="form-select" required>
                            <option value="admin">Quản trị viên</option>
                            <option value="hr">Nhân sự (HR)</option>
                            <option value="accountant">Kế toán</option>
                        </select>
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
function resetUserForm() {
    document.getElementById('uAction').value = 'create';
    document.getElementById('uId').value = '';
    document.getElementById('uTitle').textContent = 'Thêm tài khoản';
    document.getElementById('uUsername').value = '';
    document.getElementById('uPassword').value = '';
    document.getElementById('uPassword').required = true;
    document.getElementById('uFullname').value = '';
    document.getElementById('uEmail').value = '';
    document.getElementById('uRole').value = 'hr';
    document.getElementById('pwNote').style.display = '';
    document.getElementById('pwHint').style.display = 'none';
}
function editUser(u) {
    document.getElementById('uAction').value = 'update';
    document.getElementById('uId').value = u.id;
    document.getElementById('uTitle').textContent = 'Sửa tài khoản';
    document.getElementById('uUsername').value = u.username || '';
    document.getElementById('uPassword').value = '';
    document.getElementById('uPassword').required = false;
    document.getElementById('uFullname').value = u.full_name || '';
    document.getElementById('uEmail').value = u.email || '';
    document.getElementById('uRole').value = u.role || 'hr';
    document.getElementById('pwNote').style.display = 'none';
    document.getElementById('pwHint').style.display = '';
    new bootstrap.Modal(document.getElementById('userModal')).show();
}
</script>
