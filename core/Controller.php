<?php
class Controller
{
    protected $conn;
    protected $currentUser = null;

    protected $rolePermissions = [
        'admin' => ['*'],
        'hr' => [
            'dashboard', 'employees', 'departments', 'positions',
            'attendance', 'allowances', 'rewards', 'leaves', 'contracts',
            'reports', 'profile'
        ],
        'accountant' => [
            'dashboard', 'employees.index', 'employees.show',
            'salary', 'allowances.index', 'reports', 'profile'
        ],
        'employee' => [
            'dashboard', 'profile',
            'attendance.index',
            'leaves.index', 'leaves.create',
            'salary.payslip',
        ],
    ];

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;

        if (isset($_SESSION['user_id'])) {
            $this->currentUser = [
                'id'       => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'full_name'=> $_SESSION['full_name'],
                'role'     => $_SESSION['role'],
            ];
        }
    }

    protected function view($viewPath, $data = [])
    {
        extract($data);
        $currentUser = $this->currentUser;

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
        require_once ROOT_PATH . '/app/views/' . $viewPath . '.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    protected function viewOnly($viewPath, $data = [])
    {
        extract($data);
        $currentUser = $this->currentUser;

        require_once ROOT_PATH . '/app/views/' . $viewPath . '.php';
    }

    protected function redirect($path)
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
            $this->redirect('login');
        }
    }

    protected function requireRole($permission)
    {
        $this->requireAuth();
        $role = $_SESSION['role'];

        if ($role === 'admin') return;

        $permissions = $this->rolePermissions[$role] ?? [];
        $permArray = (array) $permission;

        foreach ($permArray as $perm) {
            if (in_array($perm, $permissions)) return;

            $parent = explode('.', $perm)[0];
            if (in_array($parent, $permissions)) return;
        }

        $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này.';
        $this->redirect('dashboard');
    }

    protected function hasPermission($permission)
    {
        if (!isset($_SESSION['role'])) return false;
        $role = $_SESSION['role'];

        if ($role === 'admin') return true;

        $permissions = $this->rolePermissions[$role] ?? [];

        if (in_array($permission, $permissions)) return true;

        $parent = explode('.', $permission)[0];
        return in_array($parent, $permissions);
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post($key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

    protected function get($key, $default = '')
    {
        return $_GET[$key] ?? $default;
    }

    protected function setFlash($type, $message)
    {
        $_SESSION[$type] = $message;
    }

    protected function verifyCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Phiên làm việc không hợp lệ. Vui lòng thử lại.';
            $this->redirect('dashboard');
        }
    }

    protected function generateCsrf()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function logActivity($action, $module, $detail = '')
    {
        if (!$this->currentUser) return;

        $stmt = $this->conn->prepare(
            "INSERT INTO activity_log (user_id, action, module, detail, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt->bind_param('issss',
            $this->currentUser['id'], $action, $module, $detail, $ip
        );
        $stmt->execute();
    }

    protected function model($modelName)
    {
        $file = ROOT_PATH . '/app/models/' . $modelName . '.php';
        if (file_exists($file)) {
            require_once $file;
            return new $modelName();
        }
        die('Model không tồn tại: ' . $modelName);
    }
}
