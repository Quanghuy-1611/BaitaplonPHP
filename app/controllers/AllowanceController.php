<?php
class AllowanceController extends Controller
{
    public function index()
    {
        $this->requireRole(['allowances', 'allowances.index']);
        $model = $this->model('AllowanceModel');
        $empModel = $this->model('EmployeeModel');

        // Xử lý thêm/sửa/xóa loại phụ cấp
        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'create') {
                $model->create([
                    'name'           => $this->post('name'),
                    'default_amount' => floatval(str_replace(['.', ','], '', $this->post('default_amount'))),
                    'description'    => $this->post('description'),
                ]);
                $this->logActivity('Thêm', 'allowances', 'Thêm loại phụ cấp: ' . $this->post('name'));
                $this->setFlash('success', 'Thêm loại phụ cấp thành công.');
            } elseif ($action === 'update') {
                $id = intval($this->post('id'));
                $model->update($id, [
                    'name'           => $this->post('name'),
                    'default_amount' => floatval(str_replace(['.', ','], '', $this->post('default_amount'))),
                    'description'    => $this->post('description'),
                ]);
                $this->logActivity('Sửa', 'allowances', 'Sửa loại phụ cấp ID: ' . $id);
                $this->setFlash('success', 'Cập nhật loại phụ cấp thành công.');
            } elseif ($action === 'delete') {
                $id = intval($this->post('id'));
                if ($model->hasEmployees($id)) {
                    $this->setFlash('error', 'Không thể xóa phụ cấp đang được gán cho nhân viên.');
                } else {
                    $model->delete($id);
                    $this->logActivity('Xóa', 'allowances', 'Xóa loại phụ cấp ID: ' . $id);
                    $this->setFlash('success', 'Xóa loại phụ cấp thành công.');
                }
            }
            $this->redirect('allowances');
        }

        $allowances  = $model->getAllWithCount();
        $employees   = $empModel->getActiveEmployees();
        $assignments = $model->getAllAssignments();

        $this->view('allowances/index', [
            'title' => 'Quản lý phụ cấp',
            'allowances' => $allowances,
            'employees' => $employees,
            'assignments' => $assignments,
        ]);
    }

    public function assign()
    {
        $this->requireRole('allowances');

        if ($this->isPost()) {
            $this->verifyCsrf();
            $model = $this->model('AllowanceModel');

            $employeeId  = intval($this->post('employee_id'));
            $allowanceId = intval($this->post('allowance_id'));
            $amount      = floatval(str_replace(['.', ','], '', $this->post('amount')));

            if ($model->isAssigned($employeeId, $allowanceId)) {
                $this->setFlash('error', 'Nhân viên đã được gán phụ cấp này.');
            } else {
                $model->assignToEmployee($employeeId, $allowanceId, $amount);
                $this->logActivity('Gán', 'allowances', 'Gán phụ cấp ID: ' . $allowanceId . ' cho NV ID: ' . $employeeId);
                $this->setFlash('success', 'Gán phụ cấp cho nhân viên thành công.');
            }
        }
        $this->redirect('allowances');
    }

    public function remove()
    {
        $this->requireRole('allowances');

        if ($this->isPost()) {
            $this->verifyCsrf();
            $model = $this->model('AllowanceModel');

            $id = intval($this->post('id'));
            $model->removeFromEmployee($id);
            $this->logActivity('Gỡ', 'allowances', 'Gỡ phụ cấp nhân viên ID: ' . $id);
            $this->setFlash('success', 'Gỡ phụ cấp nhân viên thành công.');
        }
        $this->redirect('allowances');
    }
}
