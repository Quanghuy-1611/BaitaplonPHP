<?php
class UserController extends Controller
{
    public function index()
    {
        $this->requireRole('users');
        $model = $this->model('UserModel');

        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'create') {
                if ($model->usernameExists($this->post('username'))) {
                    $this->setFlash('error', 'Tên đăng nhập đã tồn tại.');
                } else {
                    $model->create([
                        'username'  => $this->post('username'),
                        'password'  => $this->post('password'),
                        'full_name' => $this->post('full_name'),
                        'email'     => $this->post('email'),
                        'role'      => $this->post('role'),
                    ]);
                    $this->logActivity('Thêm', 'users', 'Thêm tài khoản: ' . $this->post('username'));
                    $this->setFlash('success', 'Thêm tài khoản thành công.');
                }
            } elseif ($action === 'update') {
                $id = intval($this->post('id'));
                if ($model->usernameExists($this->post('username'), $id)) {
                    $this->setFlash('error', 'Tên đăng nhập đã tồn tại.');
                } else {
                    $model->update($id, [
                        'username'  => $this->post('username'),
                        'full_name' => $this->post('full_name'),
                        'email'     => $this->post('email'),
                        'role'      => $this->post('role'),
                        'password'  => $this->post('password'),
                    ]);
                    $this->logActivity('Sửa', 'users', 'Sửa tài khoản ID: ' . $id);
                    $this->setFlash('success', 'Cập nhật tài khoản thành công.');
                }
            } elseif ($action === 'delete') {
                $id = intval($this->post('id'));
                if ($id == $_SESSION['user_id']) {
                    $this->setFlash('error', 'Không thể xóa tài khoản đang đăng nhập.');
                } else {
                    $model->delete($id);
                    $this->logActivity('Xóa', 'users', 'Xóa tài khoản ID: ' . $id);
                    $this->setFlash('success', 'Xóa tài khoản thành công.');
                }
            }
            $this->redirect('users');
        }

        $users = $model->getAllUsers();
        $pageTitle = 'Quản lý tài khoản';
        $this->view('users/index', compact('pageTitle', 'users'));
    }
}
