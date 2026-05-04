<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 32px;
        }
        .login-box h4 {
            font-weight: 600;
            margin-bottom: 6px;
        }
        .login-box .desc {
            color: #888;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .login-box .form-label {
            font-size: 14px;
            font-weight: 500;
        }
        .login-box .form-control {
            font-size: 14px;
        }
        .login-box .btn-primary {
            font-size: 14px;
            padding: 8px;
        }
        .login-box .text-muted {
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h4>Đăng nhập</h4>
    <p class="desc">Quản lý Nhân sự - Tiền lương</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2" style="font-size:14px;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('login') ?>">
        <?= csrfField() ?>
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required autofocus
                   value="<?= e($username ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
    </form>

    <p class="text-muted text-center mt-3 mb-0">Quên mật khẩu? Liên hệ quản trị viên.</p>
</div>
</body>
</html>
