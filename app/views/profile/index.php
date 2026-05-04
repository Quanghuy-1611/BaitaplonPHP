<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="row g-3">
            <!-- Thông tin tài khoản -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><i class="fas fa-user-circle"></i> Thông tin tài khoản</div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="profile-avatar mx-auto"><?= getInitials($user['full_name']) ?></div>
                            <h5 class="mt-2 mb-0"><?= e($user['full_name']) ?></h5>
                            <span class="badge-status badge-approved"><?= roleName($user['role']) ?></span>
                        </div>

                        <form method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="update_profile">
                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Họ tên</label>
                                <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vai trò</label>
                                <input type="text" class="form-control" value="<?= roleName($user['role']) ?>" disabled>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-save me-1"></i>Cập nhật
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Đổi mật khẩu -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><i class="fas fa-key"></i> Đổi mật khẩu</div>
                    <div class="card-body">
                        <form method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="change_password">
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-key me-1"></i>Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
