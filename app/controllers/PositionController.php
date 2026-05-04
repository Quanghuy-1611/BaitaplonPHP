<?php
class PositionController extends Controller
{
    public function index()
    {
        $this->requireRole('positions');
        $model = $this->model('PositionModel');
        $deptModel = $this->model('DepartmentModel');

        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'create') {
                $model->create([
                    'name'          => $this->post('name'),
                    'department_id' => intval($this->post('department_id')),
                    'base_salary'   => floatval(str_replace(['.', ','], '', $this->post('base_salary'))),
                    'description'   => $this->post('description'),
                ]);
                $this->logActivity('Thêm', 'positions', 'Thêm chức vụ: ' . $this->post('name'));
                $this->setFlash('success', 'Thêm chức vụ thành công.');
            } elseif ($action === 'update') {
                $id = intval($this->post('id'));
                $model->update($id, [
                    'name'          => $this->post('name'),
                    'department_id' => intval($this->post('department_id')),
                    'base_salary'   => floatval(str_replace(['.', ','], '', $this->post('base_salary'))),
                    'description'   => $this->post('description'),
                ]);
                $this->logActivity('Sửa', 'positions', 'Sửa chức vụ ID: ' . $id);
                $this->setFlash('success', 'Cập nhật chức vụ thành công.');
            } elseif ($action === 'delete') {
                $id = intval($this->post('id'));
                if ($model->hasEmployees($id)) {
                    $this->setFlash('error', 'Không thể xóa chức vụ đang có nhân viên.');
                } else {
                    $model->delete($id);
                    $this->logActivity('Xóa', 'positions', 'Xóa chức vụ ID: ' . $id);
                    $this->setFlash('success', 'Xóa chức vụ thành công.');
                }
            }
            $this->redirect('positions');
        }

        $positions = $model->getAllWithCount();
        $departments = $deptModel->getSelectList();
        $this->view('positions/index', [
            'title' => 'Quản lý chức vụ',
            'positions' => $positions,
            'departments' => $departments,
        ]);
    }
}
