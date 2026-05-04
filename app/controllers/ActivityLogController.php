<?php
class ActivityLogController extends Controller
{
    public function index()
    {
        $this->requireRole('activity-log');

        $model = $this->model('ActivityLogModel');
        $filters = [];

        if ($this->get('user_id')) $filters['user_id'] = $this->get('user_id');
        if ($this->get('module')) $filters['module'] = $this->get('module');
        if ($this->get('date_from')) $filters['date_from'] = $this->get('date_from');
        if ($this->get('date_to')) $filters['date_to'] = $this->get('date_to');

        $logs = $model->getRecent(200, $filters);
        $modules = $model->getModules();

        $userModel = $this->model('UserModel');
        $users = $userModel->findAll('full_name ASC');

        $this->view('activity_log/index', [
            'title' => 'Nhật ký hoạt động',
            'logs' => $logs,
            'modules' => $modules,
            'users' => $users,
            'filters' => $filters
        ]);
    }
}
