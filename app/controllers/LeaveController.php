<?php
class LeaveController extends Controller
{
    public function index()
    {
        $this->requireRole('leaves.index');

        $leaveModel = $this->model('LeaveModel');
        $employeeModel = $this->model('EmployeeModel');
        $departmentModel = $this->model('DepartmentModel');

        $role = $_SESSION['role'] ?? '';
        $isEmployee = ($role === 'employee');

        // Handle POST actions
        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            switch ($action) {
                case 'create':
                    $this->createLeave($leaveModel, $isEmployee);
                    break;
                case 'approve':
                    $this->requireRole('leaves'); // Chỉ HR/admin mới duyệt
                    $this->approveLeave($leaveModel);
                    break;
                case 'reject':
                    $this->requireRole('leaves'); // Chỉ HR/admin mới từ chối
                    $this->rejectLeave($leaveModel);
                    break;
            }

            $this->redirect('leaves');
            return;
        }

        // Filters
        $filters = [
            'status' => $this->get('status') ?: '',
            'department_id' => $this->get('department_id') ?: '',
            'month' => $this->get('month') ?: date('m'),
            'year' => $this->get('year') ?: date('Y'),
        ];

        // Nhân viên chỉ xem đơn nghỉ phép của mình
        if ($isEmployee) {
            $employeeId = $_SESSION['employee_id'] ?? 0;
            $leaves = $leaveModel->getEmployeeLeaves($employeeId, $filters['year']);
            $employees = null;
            $departments = null;
        } else {
            $leaves = $leaveModel->getList($filters);
            $employees = $employeeModel->getActiveEmployees();
            $departments = $departmentModel->findAll('name ASC');
        }

        // Stats
        $leaveStats = $leaveModel->getStats($filters['month'], $filters['year']);
        $stats = [
            'pending' => $leaveStats['pending'] ?? 0,
            'approved' => $leaveStats['approved'] ?? 0,
            'rejected' => $leaveStats['rejected'] ?? 0,
        ];

        $leaveTypes = [
            'Nghỉ phép năm',
            'Nghỉ ốm',
            'Nghỉ việc riêng',
            'Nghỉ không lương',
        ];

        $this->view('leaves/index', [
            'title' => $isEmployee ? 'Nghỉ phép của tôi' : 'Quản lý nghỉ phép',
            'leaves' => $leaves,
            'employees' => $employees,
            'departments' => $departments,
            'filters' => $filters,
            'stats' => $stats,
            'leaveTypes' => $leaveTypes,
            'isEmployee' => $isEmployee,
        ]);
    }

    private function createLeave($leaveModel, $isEmployee = false)
    {
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');

        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $diff = $start->diff($end);
        $days = $diff->days + 1;

        // Nhân viên: luôn dùng employee_id của chính mình
        $employeeId = $isEmployee
            ? ($_SESSION['employee_id'] ?? 0)
            : $this->post('employee_id');

        $data = [
            'employee_id' => $employeeId,
            'leave_type' => $this->post('leave_type'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $days,
            'reason' => $this->post('reason'),
        ];

        if ($leaveModel->create($data)) {
            $this->logActivity('Thêm', 'leaves', 'Tạo đơn nghỉ phép cho NV ID: ' . $data['employee_id']);
            $this->setFlash('success', 'Tạo đơn nghỉ phép thành công.');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi tạo đơn nghỉ phép.');
        }
    }

    private function approveLeave($leaveModel)
    {
        $id = intval($this->post('id'));
        if ($leaveModel->approve($id, $_SESSION['user_id'])) {
            $this->logActivity('Duyệt', 'leaves', 'Duyệt đơn nghỉ phép ID: ' . $id);
            $this->setFlash('success', 'Đã duyệt đơn nghỉ phép.');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi duyệt đơn nghỉ phép.');
        }
    }

    private function rejectLeave($leaveModel)
    {
        $id = intval($this->post('id'));
        if ($leaveModel->reject($id, $_SESSION['user_id'])) {
            $this->logActivity('Từ chối', 'leaves', 'Từ chối đơn nghỉ phép ID: ' . $id);
            $this->setFlash('success', 'Đã từ chối đơn nghỉ phép.');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi từ chối đơn nghỉ phép.');
        }
    }
}
