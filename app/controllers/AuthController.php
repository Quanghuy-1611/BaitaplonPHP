<?php
class AuthController extends Controller
{
    public function login()
    {
        // Nếu đã đăng nhập rồi
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }

        $error = '';
        $username = '';

        if ($this->isPost()) {
            $username = trim($this->post('username'));
            $password = $this->post('password');

            $userModel = $this->model('UserModel');
            $user = $userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['username']    = $user['username'];
                $_SESSION['full_name']   = $user['full_name'];
                $_SESSION['role']        = $user['role'];
                $_SESSION['employee_id'] = $user['employee_id'] ?? 0;

                $this->logActivity('Đăng nhập', 'auth', 'Đăng nhập thành công');
                $this->redirect('dashboard');
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
            }
        }

        $this->viewOnly('auth/login', [
            'error'    => $error,
            'username' => $username,
        ]);
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity('Đăng xuất', 'auth', 'Đăng xuất hệ thống');
        }
        session_destroy();
        $this->redirect('login');
    }

    public function notFound()
    {
        $this->requireAuth();
        $this->view('auth/not_found', ['title' => 'Không tìm thấy trang']);
    }
}
