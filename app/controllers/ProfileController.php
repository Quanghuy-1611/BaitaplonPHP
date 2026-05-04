<?php
class ProfileController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        $model = $this->model('UserModel');
        $user = $model->findById($_SESSION['user_id']);

        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'update_profile') {
                $model->updateProfile($_SESSION['user_id'], [
                    'full_name' => $this->post('full_name'),
                    'email'     => $this->post('email'),
                ]);
                $_SESSION['full_name'] = $this->post('full_name');
                $this->logActivity('Cập nhật', 'profile', 'Cập nhật hồ sơ cá nhân');
                $this->setFlash('success', 'Cập nhật hồ sơ thành công.');
            } elseif ($action === 'change_password') {
                $currentPw = $this->post('current_password');
                $newPw     = $this->post('new_password');
                $confirmPw = $this->post('confirm_password');

                if (!password_verify($currentPw, $user['password'])) {
                    $this->setFlash('error', 'Mật khẩu hiện tại không đúng.');
                } elseif (strlen($newPw) < 6) {
                    $this->setFlash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
                } elseif ($newPw !== $confirmPw) {
                    $this->setFlash('error', 'Xác nhận mật khẩu không khớp.');
                } else {
                    $model->changePassword($_SESSION['user_id'], $newPw);
                    $this->logActivity('Đổi mật khẩu', 'profile', 'Đổi mật khẩu thành công');
                    $this->setFlash('success', 'Đổi mật khẩu thành công.');
                }
            }
            $this->redirect('profile');
        }

        $pageTitle = 'Hồ sơ cá nhân';
        $this->view('profile/index', compact('pageTitle', 'user'));
    }
}
