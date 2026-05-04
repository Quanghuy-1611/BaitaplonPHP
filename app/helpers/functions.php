<?php

function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function formatMoney($amount)
{
    return number_format($amount ?? 0, 0, ',', '.') . ' ₫';
}

function formatNumber($amount)
{
    return number_format($amount ?? 0, 0, ',', '.');
}

function url($path = '')
{
    return BASE_URL . $path;
}

function getInitials($name)
{
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return mb_strtoupper(mb_substr(end($parts), 0, 1));
    }
    return mb_strtoupper(mb_substr($name, 0, 1));
}

function showAlert()
{
    $html = '';
    if (!empty($_SESSION['success'])) {
        $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . '<i class="fas fa-check-circle me-1"></i>' . e($_SESSION['success'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['success']);
    }
    if (!empty($_SESSION['error'])) {
        $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
            . '<i class="fas fa-exclamation-circle me-1"></i>' . e($_SESSION['error'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['error']);
    }
    return $html;
}

// Tính thuế TNCN lũy tiến, giảm trừ bản thân 11tr
function calculateTax($taxableIncome, $dependents = 0)
{
    $personalDeduction = 11000000;
    $dependentDeduction = $dependents * 4400000;
    $income = $taxableIncome - $personalDeduction - $dependentDeduction;

    if ($income <= 0) return 0;

    $brackets = [
        [5000000, 0.05],
        [5000000, 0.10],
        [8000000, 0.15],
        [14000000, 0.20],
        [20000000, 0.25],
        [28000000, 0.30],
        [PHP_INT_MAX, 0.35],
    ];

    $tax = 0;
    $remaining = $income;

    foreach ($brackets as $bracket) {
        if ($remaining <= 0) break;
        $taxable = min($remaining, $bracket[0]);
        $tax += $taxable * $bracket[1];
        $remaining -= $taxable;
    }

    return round($tax);
}

function csrfField()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function hasRole($permission)
{
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') return true;

    $rolePermissions = [
        'hr' => [
            'dashboard', 'employees', 'departments', 'positions',
            'attendance', 'allowances', 'rewards', 'leaves', 'contracts',
            'reports', 'profile'
        ],
        'accountant' => [
            'dashboard', 'employees.index', 'employees.show',
            'salary', 'allowances.index', 'reports', 'profile'
        ],
        'employee' => [
            'dashboard', 'profile',
            'attendance.index',
            'leaves.index', 'leaves.create',
            'salary.payslip',
        ],
    ];

    $permissions = $rolePermissions[$role] ?? [];
    if (in_array($permission, $permissions)) return true;

    $parent = explode('.', $permission)[0];
    return in_array($parent, $permissions);
}

function roleName($role)
{
    $names = [
        'admin'      => 'Quản trị viên',
        'hr'         => 'Nhân sự',
        'accountant' => 'Kế toán',
        'employee'   => 'Nhân viên',
    ];
    return $names[$role] ?? $role;
}

function formatDate($date, $format = 'd/m/Y')
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}

function workingDaysInMonth($month, $year)
{
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $workDays = 0;
    for ($d = 1; $d <= $days; $d++) {
        $dayOfWeek = date('N', mktime(0, 0, 0, $month, $d, $year));
        if ($dayOfWeek < 6) $workDays++;
    }
    return $workDays;
}
