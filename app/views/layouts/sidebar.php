<?php
// Xác định trang hiện tại từ URL
$currentUrl = $_GET['url'] ?? 'dashboard';
$currentPage = explode('/', $currentUrl)[0];
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Quản lý Nhân sự</h3>
        <small>Hệ thống tiền lương</small>
    </div>

    <div class="sidebar-nav">
        <!-- TỔNG QUAN -->
        <div class="nav-section">Tổng quan</div>
        <a href="<?= url('dashboard') ?>" class="nav-item <?= $currentPage == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <!-- QUẢN LÝ NHÂN SỰ -->
        <?php if (hasRole('employees') || hasRole('employees.index')): ?>
        <div class="nav-section">Quản lý nhân sự</div>
        <?php endif; ?>

        <?php if (hasRole('departments')): ?>
        <a href="<?= url('departments') ?>" class="nav-item <?= $currentPage == 'departments' ? 'active' : '' ?>">
            <i class="fas fa-building"></i> Phòng ban
        </a>
        <?php endif; ?>

        <?php if (hasRole('positions')): ?>
        <a href="<?= url('positions') ?>" class="nav-item <?= $currentPage == 'positions' ? 'active' : '' ?>">
            <i class="fas fa-user-tie"></i> Chức vụ
        </a>
        <?php endif; ?>

        <?php if (hasRole('employees') || hasRole('employees.index')): ?>
        <a href="<?= url('employees') ?>" class="nav-item <?= $currentPage == 'employees' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Nhân viên
        </a>
        <?php endif; ?>

        <?php if (hasRole('contracts')): ?>
        <a href="<?= url('contracts') ?>" class="nav-item <?= $currentPage == 'contracts' ? 'active' : '' ?>">
            <i class="fas fa-file-contract"></i> Hợp đồng
        </a>
        <?php endif; ?>

        <!-- CHẤM CÔNG & NGHỈ PHÉP -->
        <?php if (hasRole('attendance') || hasRole('attendance.index') || hasRole('leaves') || hasRole('leaves.index')): ?>
        <div class="nav-section">Chấm công & Nghỉ phép</div>
        <?php endif; ?>

        <?php if (hasRole('attendance') || hasRole('attendance.index')): ?>
        <a href="<?= url('attendance') ?>" class="nav-item <?= $currentPage == 'attendance' ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Chấm công
        </a>
        <?php endif; ?>

        <?php if (hasRole('leaves') || hasRole('leaves.index')): ?>
        <a href="<?= url('leaves') ?>" class="nav-item <?= $currentPage == 'leaves' ? 'active' : '' ?>">
            <i class="fas fa-calendar-minus"></i> Nghỉ phép
        </a>
        <?php endif; ?>

        <!-- KHEN THƯỞNG & PHỤ CẤP -->
        <?php if (hasRole('rewards') || hasRole('allowances') || hasRole('allowances.index')): ?>
        <div class="nav-section">Phụ cấp & Khen thưởng</div>
        <?php endif; ?>

        <?php if (hasRole('allowances') || hasRole('allowances.index')): ?>
        <a href="<?= url('allowances') ?>" class="nav-item <?= $currentPage == 'allowances' ? 'active' : '' ?>">
            <i class="fas fa-hand-holding-usd"></i> Phụ cấp
        </a>
        <?php endif; ?>

        <?php if (hasRole('rewards')): ?>
        <a href="<?= url('rewards') ?>" class="nav-item <?= $currentPage == 'rewards' ? 'active' : '' ?>">
            <i class="fas fa-award"></i> Khen thưởng / Kỷ luật
        </a>
        <?php endif; ?>

        <!-- TIỀN LƯƠNG -->
        <?php if (hasRole('salary') || hasRole('salary.payslip')): ?>
        <div class="nav-section">Tiền lương</div>

        <?php if (hasRole('salary')): ?>
        <a href="<?= url('salary') ?>" class="nav-item <?= $currentPage == 'salary' && $currentUrl != 'salary/advance' && $currentUrl != 'salary/payslip' ? 'active' : '' ?>">
            <i class="fas fa-money-check-alt"></i> Bảng lương
        </a>
        <a href="<?= url('salary/advance') ?>" class="nav-item <?= $currentUrl == 'salary/advance' ? 'active' : '' ?>">
            <i class="fas fa-hand-holding-usd"></i> Tạm ứng lương
        </a>
        <?php endif; ?>
        <a href="<?= url('salary/payslip') ?>" class="nav-item <?= $currentUrl == 'salary/payslip' ? 'active' : '' ?>">
            <i class="fas fa-file-invoice-dollar"></i> Phiếu lương
        </a>
        <?php endif; ?>

        <!-- BÁO CÁO -->
        <?php if (hasRole('reports')): ?>
        <div class="nav-section">Báo cáo</div>
        <a href="<?= url('reports') ?>" class="nav-item <?= $currentPage == 'reports' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i> Báo cáo thống kê
        </a>
        <?php endif; ?>

        <!-- HỆ THỐNG -->
        <div class="nav-section">Hệ thống</div>

        <?php if (hasRole('users')): ?>
        <a href="<?= url('users') ?>" class="nav-item <?= $currentPage == 'users' ? 'active' : '' ?>">
            <i class="fas fa-user-cog"></i> Quản lý tài khoản
        </a>
        <?php endif; ?>

        <?php if (hasRole('activity-log')): ?>
        <a href="<?= url('activity-log') ?>" class="nav-item <?= $currentPage == 'activity-log' ? 'active' : '' ?>">
            <i class="fas fa-history"></i> Nhật ký hoạt động
        </a>
        <?php endif; ?>

        <a href="<?= url('profile') ?>" class="nav-item <?= $currentPage == 'profile' ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i> Hồ sơ cá nhân
        </a>
        <a href="<?= url('logout') ?>" class="nav-item text-danger">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="fas fa-bars"></i>
            </button>
            <h4><?= e($title ?? 'Dashboard') ?></h4>
        </div>
        <div class="user-info">
            <div>
                <div class="user-name"><?= e($currentUser['full_name'] ?? '') ?></div>
                <div class="user-role"><?= roleName($currentUser['role'] ?? '') ?></div>
            </div>
            <div class="user-avatar">
                <?= getInitials($currentUser['full_name'] ?? 'U') ?>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-content">
        <?= showAlert() ?>
