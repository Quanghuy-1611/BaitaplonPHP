<?php
class DashboardController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        $role = $_SESSION['role'] ?? '';

        // Nhân viên → dashboard riêng
        if ($role === 'employee') {
            $this->employeeDashboard();
            return;
        }

        // Dashboard cho admin/hr/accountant
        $empModel      = $this->model('EmployeeModel');
        $salaryModel   = $this->model('SalaryModel');
        $leaveModel    = $this->model('LeaveModel');
        $contractModel = $this->model('ContractModel');

        $month = date('n');
        $year  = date('Y');

        $totalEmployees  = $empModel->getActiveCount();
        $newThisMonth    = $empModel->getNewThisMonth();
        $salaryTotals    = $salaryModel->getTotals($month, $year);
        $pendingLeaves   = $leaveModel->getPendingCount();
        $expiringContracts = $contractModel->getExpiringContracts(30);
        $contractStats   = $contractModel->getStats();
        $empByDept       = $empModel->getByDepartmentStats();
        $recentEmployees = $empModel->getRecentEmployees(5);
        $salaryTrend     = $salaryModel->getMonthlyTrend(6);

        $advResult = $this->conn->query("SELECT COUNT(*) as cnt FROM salary_advance WHERE status='Chờ duyệt'");
        $pendingAdvances = $advResult ? $advResult->fetch_assoc()['cnt'] : 0;

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'totalEmployees' => $totalEmployees,
            'newThisMonth' => $newThisMonth,
            'salaryTotals' => $salaryTotals,
            'pendingLeaves' => $pendingLeaves,
            'expiringContracts' => $expiringContracts,
            'contractStats' => $contractStats,
            'empByDept' => $empByDept,
            'recentEmployees' => $recentEmployees,
            'salaryTrend' => $salaryTrend,
            'pendingAdvances' => $pendingAdvances,
            'month' => $month,
            'year' => $year,
        ]);
    }

    private function employeeDashboard()
    {
        $employeeId = $_SESSION['employee_id'] ?? 0;

        if (!$employeeId) {
            $this->view('dashboard/employee', [
                'title' => 'Trang cá nhân',
                'employee' => null,
            ]);
            return;
        }

        $empModel    = $this->model('EmployeeModel');
        $attModel    = $this->model('AttendanceModel');
        $leaveModel  = $this->model('LeaveModel');
        $salaryModel = $this->model('SalaryModel');

        $month = date('n');
        $year  = date('Y');

        // Thông tin cá nhân
        $employee = $empModel->getDetail($employeeId);

        // Chấm công tháng này
        $attStats = $attModel->getEmployeeStats($employeeId, $month, $year);

        // Phép năm
        $leaveBalance = $leaveModel->getLeaveBalance($employeeId, $year);

        // Đơn nghỉ phép gần nhất
        $recentLeaves = $leaveModel->getEmployeeLeaves($employeeId, $year);

        // Lương tháng gần nhất
        $latestSalary = $salaryModel->getByEmployeeId($employeeId, $month, $year);
        if (!$latestSalary) {
            $prevMonth = $month - 1;
            $prevYear = $year;
            if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
            $latestSalary = $salaryModel->getByEmployeeId($employeeId, $prevMonth, $prevYear);
        }

        $this->view('dashboard/employee', [
            'title' => 'Trang cá nhân',
            'employee' => $employee,
            'attStats' => $attStats ?? ['ngay_cong' => 0, 'overtime' => 0],
            'leaveBalance' => $leaveBalance ?? ['annual' => 12, 'used' => 0, 'remaining' => 12],
            'recentLeaves' => $recentLeaves,
            'latestSalary' => $latestSalary,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
