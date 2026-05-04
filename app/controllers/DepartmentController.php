<?php
class DepartmentController extends Controller
{
    public function index()
    {
        $this->requireRole('departments');
        $model = $this->model('DepartmentModel');

        // Xử lý thêm/sửa/xóa
        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'create') {
                $model->create([
                    'name'         => $this->post('name'),
                    'description'  => $this->post('description'),
                    'manager_name' => $this->post('manager_name'),
                    'phone'        => $this->post('phone'),
                ]);
                $this->logActivity('Thêm', 'departments', 'Thêm phòng ban: ' . $this->post('name'));
                $this->setFlash('success', 'Thêm phòng ban thành công.');
            } elseif ($action === 'update') {
                $id = intval($this->post('id'));
                $model->update($id, [
                    'name'         => $this->post('name'),
                    'description'  => $this->post('description'),
                    'manager_name' => $this->post('manager_name'),
                    'phone'        => $this->post('phone'),
                ]);
                $this->logActivity('Sửa', 'departments', 'Sửa phòng ban ID: ' . $id);
                $this->setFlash('success', 'Cập nhật phòng ban thành công.');
            } elseif ($action === 'delete') {
                $id = intval($this->post('id'));
                if ($model->hasEmployees($id)) {
                    $this->setFlash('error', 'Không thể xóa phòng ban đang có nhân viên.');
                } else {
                    $model->delete($id);
                    $this->logActivity('Xóa', 'departments', 'Xóa phòng ban ID: ' . $id);
                    $this->setFlash('success', 'Xóa phòng ban thành công.');
                }
            }
            $this->redirect('departments');
        }

        $departments = $model->getAllWithCount();
        $pageTitle = 'Quản lý phòng ban';
        $this->view('departments/index', compact('pageTitle', 'departments'));
    }
}
