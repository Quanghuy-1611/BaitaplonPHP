<?php
class AttendanceController extends Controller
{
    public function index()
    {
        $this->requireRole('attendance.index');
        $attModel  = $this->model('AttendanceModel');
        $empModel  = $this->model('EmployeeModel');
        $deptModel = $this->model('DepartmentModel');

        $role = $_SESSION['role'] ?? '';
        $isEmployee = ($role === 'employee');

        // Bộ lọc tháng/năm/phòng ban
        $filters = [
            'month'         => intval($this->get('month', date('n'))),
            'year'          => intval($this->get('year', date('Y'))),
            'department_id' => intval($this->get('department_id')),
        ];

        // Nhân viên chỉ xem chấm công của mình
        if ($isEmployee) {
            $employeeId = $_SESSION['employee_id'] ?? 0;
            $records = $attModel->getEmployeeAttendance($employeeId, $filters['month'], $filters['year']);
            $stats   = $attModel->getEmployeeStats($employeeId, $filters['month'], $filters['year']);
            $employees = null;
            $departments = null;
        } else {
            $records     = $attModel->getByMonth($filters['month'], $filters['year'], $filters['department_id']);
            $stats       = $attModel->getStats($filters['month'], $filters['year']);
            $employees   = $empModel->getActiveEmployees();
            $departments = $deptModel->getSelectList();
        }

        $this->view('attendance/index', [
            'title' => $isEmployee ? 'Chấm công của tôi' : 'Quản lý chấm công',
            'records' => $records,
            'stats' => $stats,
            'employees' => $employees,
            'departments' => $departments,
            'filters' => $filters,
            'isEmployee' => $isEmployee,
        ]);
    }

    public function store()
    {
        $this->requireRole('attendance');
        $this->verifyCsrf();
        $model = $this->model('AttendanceModel');

        $employeeId = intval($this->post('employee_id'));
        $date       = $this->post('date');

        if (!$employeeId || !$date) {
            $this->setFlash('error', 'Vui lòng chọn nhân viên và ngày chấm công.');
            $this->redirect('attendance');
        }

        $model->addSingle([
            'employee_id'    => $employeeId,
            'work_date'      => $date,
            'status'         => $this->post('status', 'Đi làm'),
            'check_in'       => $this->post('check_in') ?: null,
            'check_out'      => $this->post('check_out') ?: null,
            'overtime_hours'  => floatval($this->post('overtime_hours', 0)),
            'note'           => $this->post('note', ''),
        ]);

        $this->logActivity('Thêm', 'attendance', 'Chấm công nhân viên ID: ' . $employeeId . ' ngày ' . $date);
        $this->setFlash('success', 'Chấm công thành công.');
        $this->redirect('attendance');
    }

    public function bulkStore()
    {
        $this->requireRole('attendance');
        $this->verifyCsrf();
        $model = $this->model('AttendanceModel');

        $date        = $this->post('date');
        $employeeIds = $_POST['employee_ids'] ?? [];
        $status      = $this->post('status', 'Đi làm');
        $checkIn     = $this->post('check_in') ?: null;
        $checkOut    = $this->post('check_out') ?: null;

        if (!$date || empty($employeeIds)) {
            $this->setFlash('error', 'Vui lòng chọn ngày và ít nhất một nhân viên.');
            $this->redirect('attendance');
        }

        $count = $model->addBulk($date, $employeeIds, $status, $checkIn, $checkOut);

        $this->logActivity('Thêm', 'attendance', 'Chấm công hàng loạt ' . $count . ' nhân viên ngày ' . $date);
        $this->setFlash('success', 'Đã chấm công cho ' . $count . ' nhân viên thành công.');
        $this->redirect('attendance');
    }

    public function delete($id = 0)
    {
        $this->requireRole('attendance');
        $id    = intval($id);
        $model = $this->model('AttendanceModel');

        $record = $model->findById($id);
        if ($record) {
            $model->deleteRecord($id);
            $this->logActivity('Xóa', 'attendance', 'Xóa chấm công ID: ' . $id);
            $this->setFlash('success', 'Xóa bản ghi chấm công thành công.');
        } else {
            $this->setFlash('error', 'Bản ghi chấm công không tồn tại.');
        }

        $this->redirect('attendance');
    }
}
