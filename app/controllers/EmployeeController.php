<?php
class EmployeeController extends Controller
{
    public function index()
    {
        $this->requireRole(['employees', 'employees.index']);
        $model = $this->model('EmployeeModel');
        $deptModel = $this->model('DepartmentModel');

        $filters = [
            'search'        => $this->get('search'),
            'department_id' => intval($this->get('department_id')),
            'status'        => $this->get('status'),
        ];
        $page = max(1, intval($this->get('page', 1)));

        $result = $model->getList($filters, $page);
        $departments = $deptModel->getSelectList();

        $pageTitle = 'Quản lý nhân viên';
        $canEdit = $this->hasPermission('employees');
        $this->view('employees/index', compact('pageTitle', 'result', 'departments', 'filters', 'canEdit'));
    }

    public function create()
    {
        $this->requireRole('employees');
        $model = $this->model('EmployeeModel');
        $deptModel = $this->model('DepartmentModel');
        $posModel = $this->model('PositionModel');

        $employee = null;
        $nextCode = $model->generateNextCode();
        $departments = $deptModel->getSelectList();
        $positions = $posModel->getSelectList();

        $pageTitle = 'Thêm nhân viên mới';
        $this->view('employees/form', compact('pageTitle', 'employee', 'nextCode', 'departments', 'positions'));
    }

    public function store()
    {
        $this->requireRole('employees');
        $this->verifyCsrf();
        $model = $this->model('EmployeeModel');

        $code = trim($this->post('employee_code'));
        if ($model->codeExists($code)) {
            $this->setFlash('error', 'Mã nhân viên đã tồn tại.');
            $this->redirect('employees/create');
        }

        $model->create([
            'employee_code' => $code,
            'full_name'     => $this->post('full_name'),
            'gender'        => $this->post('gender'),
            'birth_date'    => $this->post('birth_date'),
            'id_card'       => $this->post('id_card'),
            'phone'         => $this->post('phone'),
            'email'         => $this->post('email'),
            'address'       => $this->post('address'),
            'department_id' => intval($this->post('department_id')),
            'position_id'   => intval($this->post('position_id')),
            'hire_date'     => $this->post('hire_date'),
            'contract_type' => $this->post('contract_type'),
            'base_salary'   => floatval(str_replace(['.', ','], '', $this->post('base_salary'))),
            'bank_account'  => $this->post('bank_account'),
            'bank_name'     => $this->post('bank_name'),
            'status'        => $this->post('status', 'Đang làm'),
        ]);

        $this->logActivity('Thêm', 'employees', 'Thêm nhân viên: ' . $this->post('full_name'));
        $this->setFlash('success', 'Thêm nhân viên thành công.');
        $this->redirect('employees');
    }

    public function edit($id = 0)
    {
        $this->requireRole('employees');
        $id = intval($id);
        $model = $this->model('EmployeeModel');
        $deptModel = $this->model('DepartmentModel');
        $posModel = $this->model('PositionModel');

        $employee = $model->getDetail($id);
        if (!$employee) {
            $this->setFlash('error', 'Nhân viên không tồn tại.');
            $this->redirect('employees');
        }

        $departments = $deptModel->getSelectList();
        $positions = $posModel->getSelectList();
        $nextCode = '';

        $pageTitle = 'Sửa thông tin: ' . $employee['full_name'];
        $this->view('employees/form', compact('pageTitle', 'employee', 'nextCode', 'departments', 'positions'));
    }

    public function update($id = 0)
    {
        $this->requireRole('employees');
        $this->verifyCsrf();
        $id = intval($id);
        $model = $this->model('EmployeeModel');

        $model->update($id, [
            'full_name'     => $this->post('full_name'),
            'gender'        => $this->post('gender'),
            'birth_date'    => $this->post('birth_date'),
            'id_card'       => $this->post('id_card'),
            'phone'         => $this->post('phone'),
            'email'         => $this->post('email'),
            'address'       => $this->post('address'),
            'department_id' => intval($this->post('department_id')),
            'position_id'   => intval($this->post('position_id')),
            'hire_date'     => $this->post('hire_date'),
            'contract_type' => $this->post('contract_type'),
            'base_salary'   => floatval(str_replace(['.', ','], '', $this->post('base_salary'))),
            'bank_account'  => $this->post('bank_account'),
            'bank_name'     => $this->post('bank_name'),
            'status'        => $this->post('status'),
        ]);

        $this->logActivity('Sửa', 'employees', 'Sửa nhân viên ID: ' . $id);
        $this->setFlash('success', 'Cập nhật nhân viên thành công.');
        $this->redirect('employees/show/' . $id);
    }

    public function show($id = 0)
    {
        $this->requireRole(['employees', 'employees.show']);
        $id = intval($id);

        $empModel     = $this->model('EmployeeModel');
        $allowModel   = $this->model('AllowanceModel');
        $rewardModel  = $this->model('RewardModel');
        $salaryModel  = $this->model('SalaryModel');
        $leaveModel   = $this->model('LeaveModel');
        $contractModel= $this->model('ContractModel');

        $employee = $empModel->getDetail($id);
        if (!$employee) {
            $this->setFlash('error', 'Nhân viên không tồn tại.');
            $this->redirect('employees');
        }

        $allowances    = $allowModel->getEmployeeAllowances($id);
        $rewards       = $rewardModel->getByEmployee($id);
        $salaryHistory = $salaryModel->getEmployeeSalaryHistory($id);
        $leaves        = $leaveModel->getEmployeeLeaves($id, date('Y'));
        $contracts     = $contractModel->getByEmployee($id);
        $leaveBalance  = $leaveModel->getLeaveBalance($id, date('Y'));

        $canEdit = $this->hasPermission('employees');

        $pageTitle = 'Hồ sơ: ' . $employee['full_name'];
        $this->view('employees/show', compact(
            'pageTitle', 'employee', 'allowances', 'rewards',
            'salaryHistory', 'leaves', 'contracts', 'leaveBalance', 'canEdit'
        ));
    }

    public function delete($id = 0)
    {
        $this->requireRole('employees');
        $id = intval($id);
        $model = $this->model('EmployeeModel');

        $emp = $model->findById($id);
        if ($emp) {
            $model->delete($id);
            $this->logActivity('Xóa', 'employees', 'Xóa nhân viên: ' . $emp['full_name']);
            $this->setFlash('success', 'Xóa nhân viên thành công.');
        }
        $this->redirect('employees');
    }
}
