<?php
class SalaryController extends Controller
{
    public function index()
    {
        $this->requireRole('salary');
        $model = $this->model('SalaryModel');

        $month = intval($this->get('month', date('n')));
        $year  = intval($this->get('year', date('Y')));

        // Xử lý POST actions
        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'approve') {
                $model->approve(intval($this->post('id')));
                $this->logActivity('Duyệt', 'salary', 'Duyệt lương ID: ' . $this->post('id'));
                $this->setFlash('success', 'Đã duyệt bảng lương.');
            } elseif ($action === 'approve_all') {
                $model->approveAll($month, $year);
                $this->logActivity('Duyệt tất cả', 'salary', "Duyệt tất cả lương T$month/$year");
                $this->setFlash('success', 'Đã duyệt tất cả bảng lương.');
            } elseif ($action === 'mark_paid') {
                $model->markPaid(intval($this->post('id')));
                $this->logActivity('Thanh toán', 'salary', 'Thanh toán lương ID: ' . $this->post('id'));
                $this->setFlash('success', 'Đã đánh dấu thanh toán.');
            } elseif ($action === 'delete') {
                $model->delete(intval($this->post('id')));
                $this->logActivity('Xóa', 'salary', 'Xóa lương ID: ' . $this->post('id'));
                $this->setFlash('success', 'Đã xóa bản ghi lương.');
            }
            $this->redirect("salary?month=$month&year=$year");
        }

        $salaries = $model->getByMonth($month, $year);
        $totals   = $model->getTotals($month, $year);

        $this->view('salary/index', [
            'title' => 'Bảng lương tháng ' . $month . '/' . $year,
            'salaries' => $salaries,
            'totals' => $totals,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function calculate()
    {
        $this->requireRole('salary');

        if (!$this->isPost()) {
            // Hiển thị form chọn tháng/năm
            $month = intval($this->get('month', date('n')));
            $year  = intval($this->get('year', date('Y')));
            $this->view('salary/calculate', [
                'title' => 'Tính lương',
                'month' => $month,
                'year' => $year,
            ]);
            return;
        }

        $this->verifyCsrf();
        $month = intval($this->post('month'));
        $year  = intval($this->post('year'));

        $empModel   = $this->model('EmployeeModel');
        $attModel   = $this->model('AttendanceModel');
        $allowModel = $this->model('AllowanceModel');
        $rewardModel= $this->model('RewardModel');
        $salModel   = $this->model('SalaryModel');

        $employees = $empModel->getActiveEmployees();
        $workingDays = workingDaysInMonth($month, $year);
        $count = 0;

        while ($emp = $employees->fetch_assoc()) {
            $eid = $emp['id'];
            $baseSalary = $emp['base_salary'];

            // Ngày công thực tế
            $attStats = $attModel->getEmployeeStats($eid, $month, $year);
            $actualDays = $attStats['ngay_cong'] ?? 0;
            $overtimeHours = $attStats['overtime'] ?? 0;

            // Phụ cấp
            $totalAllowance = $allowModel->getTotalAllowance($eid);

            // Khen thưởng / Kỷ luật
            $rdTotals = $rewardModel->getEmployeeTotals($eid, $month, $year);
            $totalReward = $rdTotals['total_reward'];
            $totalDiscipline = $rdTotals['total_discipline'];

            // Tăng ca (1.5x)
            $hourlyRate = ($workingDays > 0) ? $baseSalary / ($workingDays * 8) : 0;
            $overtimePay = round($overtimeHours * $hourlyRate * 1.5);

            // Gross = lương theo ngày công + phụ cấp + tăng ca + thưởng - phạt
            $salaryByDays = ($workingDays > 0) ? round($baseSalary * $actualDays / $workingDays) : $baseSalary;
            $grossSalary = $salaryByDays + $totalAllowance + $overtimePay + $totalReward - $totalDiscipline;

            // Bảo hiểm (tính trên lương cơ bản)
            $bhxh = round($baseSalary * 0.08);
            $bhyt = round($baseSalary * 0.015);
            $bhtn = round($baseSalary * 0.01);
            $totalInsurance = $bhxh + $bhyt + $bhtn;

            // Thuế TNCN
            $taxableIncome = $grossSalary - $totalInsurance;
            $tax = calculateTax($taxableIncome);

            // Tạm ứng
            $advanceSalary = $salModel->getApprovedAdvance($eid, $month, $year);

            // Net
            $netSalary = $grossSalary - $totalInsurance - $tax - $advanceSalary;

            $salModel->calculate($eid, $month, $year, [
                'base_salary'         => $baseSalary,
                'working_days'        => $workingDays,
                'actual_working_days' => $actualDays,
                'total_allowance'     => $totalAllowance,
                'overtime_hours'      => $overtimeHours,
                'overtime_pay'        => $overtimePay,
                'total_reward'        => $totalReward,
                'total_discipline'    => $totalDiscipline,
                'gross_salary'        => $grossSalary,
                'bhxh'                => $bhxh,
                'bhyt'                => $bhyt,
                'bhtn'                => $bhtn,
                'tax'                 => $tax,
                'advance_salary'      => $advanceSalary,
                'other_deduction'     => 0,
                'net_salary'          => $netSalary,
            ]);
            $count++;
        }

        $this->logActivity('Tính lương', 'salary', "Tính lương T$month/$year cho $count nhân viên");
        $this->setFlash('success', "Đã tính lương tháng $month/$year cho $count nhân viên.");
        $this->redirect("salary?month=$month&year=$year");
    }

    public function detail($id = 0)
    {
        $this->requireRole('salary');
        $id = intval($id);
        $model = $this->model('SalaryModel');
        $allowModel = $this->model('AllowanceModel');

        $salary = $model->getDetail($id);
        if (!$salary) {
            $this->setFlash('error', 'Bản ghi lương không tồn tại.');
            $this->redirect('salary');
        }

        $allowances = $allowModel->getEmployeeAllowances($salary['employee_id']);
        $totalDeductions = $salary['bhxh'] + $salary['bhyt'] + $salary['bhtn']
                         + $salary['tax'] + $salary['other_deduction'] + $salary['advance_salary'];

        $this->view('salary/detail', [
            'title' => 'Chi tiết lương: ' . $salary['full_name'] . ' - T' . $salary['month'] . '/' . $salary['year'],
            'salary' => $salary,
            'allowances' => $allowances,
            'totalDeductions' => $totalDeductions,
        ]);
    }

    public function advance()
    {
        $this->requireRole('salary');
        $model = $this->model('SalaryModel');
        $empModel = $this->model('EmployeeModel');

        $month = intval($this->get('month', date('n')));
        $year  = intval($this->get('year', date('Y')));

        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'create') {
                $model->createAdvance([
                    'employee_id' => intval($this->post('employee_id')),
                    'month'       => intval($this->post('month')),
                    'year'        => intval($this->post('year')),
                    'amount'      => floatval(str_replace(['.', ','], '', $this->post('amount'))),
                    'reason'      => $this->post('reason'),
                ]);
                $this->logActivity('Thêm', 'salary_advance', 'Tạo tạm ứng lương');
                $this->setFlash('success', 'Tạo phiếu tạm ứng thành công.');
            } elseif ($action === 'approve') {
                $model->approveAdvance(intval($this->post('id')));
                $this->logActivity('Duyệt', 'salary_advance', 'Duyệt tạm ứng ID: ' . $this->post('id'));
                $this->setFlash('success', 'Đã duyệt tạm ứng.');
            } elseif ($action === 'reject') {
                $model->rejectAdvance(intval($this->post('id')));
                $this->logActivity('Từ chối', 'salary_advance', 'Từ chối tạm ứng ID: ' . $this->post('id'));
                $this->setFlash('success', 'Đã từ chối tạm ứng.');
            }
            $this->redirect("salary/advance?month=$month&year=$year");
        }

        $advances = $model->getAdvances($month, $year);
        $employees = $empModel->getActiveEmployees();

        $this->view('salary/advance', [
            'title' => 'Tạm ứng lương',
            'advances' => $advances,
            'employees' => $employees,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function payslip($id = 0)
    {
        $this->requireRole('salary.payslip');
        $id = intval($id);

        $role = $_SESSION['role'] ?? '';
        $isEmployee = ($role === 'employee');
        $model = $this->model('SalaryModel');

        // Nhân viên: tự động lấy phiếu lương của mình
        if ($isEmployee) {
            $employeeId = $_SESSION['employee_id'] ?? 0;
            $month = intval($this->get('month', date('n')));
            $year  = intval($this->get('year', date('Y')));

            $salary = $model->getByEmployeeId($employeeId, $month, $year);

            // Nếu tháng này chưa có, thử tháng trước
            if (!$salary) {
                $prevMonth = $month - 1;
                $prevYear = $year;
                if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
                $salary = $model->getByEmployeeId($employeeId, $prevMonth, $prevYear);
            }

            if (!$salary) {
                $this->view('salary/payslip_select', [
                    'title' => 'Phiếu lương',
                    'employees' => null,
                    'isEmployee' => true,
                    'noData' => true,
                ]);
                return;
            }
            $id = $salary['id'];
        }

        // Nếu không có ID, hiển thị form chọn (cho admin/hr/accountant)
        if ($id <= 0) {
            $empId = intval($this->get('employee_id'));
            $month = intval($this->get('month'));
            $year  = intval($this->get('year'));

            if ($empId > 0 && $month > 0 && $year > 0) {
                $salary = $model->getByEmployeeId($empId, $month, $year);
                if ($salary) {
                    $id = $salary['id'];
                }
            }
        }

        if ($id <= 0) {
            $empModel = $this->model('EmployeeModel');
            $employees = $empModel->getActiveEmployees();
            $this->view('salary/payslip_select', [
                'title' => 'In phiếu lương',
                'employees' => $employees,
                'isEmployee' => false,
            ]);
            return;
        }

        $allowModel = $this->model('AllowanceModel');

        $salary = $model->getDetail($id);
        if (!$salary) {
            $this->setFlash('error', 'Không tìm thấy phiếu lương.');
            $this->redirect($isEmployee ? 'dashboard' : 'salary/payslip');
            return;
        }

        // Nhân viên chỉ xem phiếu lương của chính mình
        if ($isEmployee && $salary['employee_id'] != ($_SESSION['employee_id'] ?? 0)) {
            $this->setFlash('error', 'Bạn không có quyền xem phiếu lương này.');
            $this->redirect('dashboard');
            return;
        }

        $allowances = $allowModel->getEmployeeAllowances($salary['employee_id']);
        $totalDeductions = $salary['bhxh'] + $salary['bhyt'] + $salary['bhtn']
                         + $salary['tax'] + $salary['other_deduction'] + $salary['advance_salary'];

        $this->viewOnly('salary/payslip', compact('salary', 'allowances', 'totalDeductions'));
    }

    public function export()
    {
        $this->requireRole('salary');
        $month = intval($this->get('month', date('n')));
        $year  = intval($this->get('year', date('Y')));

        $reportModel = $this->model('ReportModel');
        $data = $reportModel->exportSalaryCsv($month, $year);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="bang_luong_T' . $month . '_' . $year . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8

        fputcsv($output, ['Mã NV', 'Họ tên', 'Phòng ban', 'Chức vụ', 'Lương CB',
            'Phụ cấp', 'Tăng ca', 'Thưởng', 'Phạt', 'Gross', 'BHXH', 'BHYT', 'BHTN',
            'Thuế', 'Tạm ứng', 'Thực nhận', 'Trạng thái']);

        while ($row = $data->fetch_assoc()) {
            fputcsv($output, [
                $row['employee_code'], $row['full_name'], $row['dept_name'], $row['pos_name'],
                $row['base_salary'], $row['total_allowance'], $row['overtime_pay'],
                $row['total_reward'], $row['total_discipline'], $row['gross_salary'],
                $row['bhxh'], $row['bhyt'], $row['bhtn'], $row['tax'],
                $row['advance_salary'], $row['net_salary'], $row['status'],
            ]);
        }
        fclose($output);
        exit;
    }
}
