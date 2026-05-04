<?php
class App
{
    private $controllerName = 'AuthController';
    private $actionName = 'login';
    private $params = [];

    private $controllerMap = [
        'auth'        => 'AuthController',
        'dashboard'   => 'DashboardController',
        'departments' => 'DepartmentController',
        'positions'   => 'PositionController',
        'employees'   => 'EmployeeController',
        'attendance'  => 'AttendanceController',
        'allowances'  => 'AllowanceController',
        'rewards'     => 'RewardController',
        'salary'      => 'SalaryController',
        'leaves'      => 'LeaveController',
        'contracts'   => 'ContractController',
        'reports'     => 'ReportController',
        'users'       => 'UserController',
        'profile'     => 'ProfileController',
        'activity-log'=> 'ActivityLogController',
    ];

    public function run()
    {
        $url = $this->parseUrl();

        if (empty($url) || empty($url[0])) {
            if (isset($_SESSION['user_id'])) {
                $this->controllerName = 'DashboardController';
                $this->actionName = 'index';
            } else {
                $this->controllerName = 'AuthController';
                $this->actionName = 'login';
            }
        } else {
            $segment = strtolower($url[0]);

            if ($segment === 'login') {
                $this->controllerName = 'AuthController';
                $this->actionName = 'login';
            } elseif ($segment === 'logout') {
                $this->controllerName = 'AuthController';
                $this->actionName = 'logout';
            } elseif (isset($this->controllerMap[$segment])) {
                $this->controllerName = $this->controllerMap[$segment];

                if (isset($url[1]) && !empty($url[1])) {
                    $this->actionName = $this->toCamelCase($url[1]);
                } else {
                    $this->actionName = 'index';
                }

                $this->params = array_slice($url, 2);
            } else {
                $this->controllerName = 'AuthController';
                $this->actionName = 'notFound';
            }
        }

        $controllerFile = ROOT_PATH . '/app/controllers/' . $this->controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die('Controller không tồn tại: ' . $this->controllerName);
        }

        require_once $controllerFile;
        $controller = new $this->controllerName();

        if (method_exists($controller, $this->actionName)) {
            call_user_func_array([$controller, $this->actionName], $this->params);
        } else {
            die('Action không tồn tại: ' . $this->actionName);
        }
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }

    // kebab-case → camelCase (vd: salary-advance → salaryAdvance)
    private function toCamelCase($str)
    {
        $parts = explode('-', $str);
        $result = $parts[0];
        for ($i = 1; $i < count($parts); $i++) {
            $result .= ucfirst($parts[$i]);
        }
        return $result;
    }
}
