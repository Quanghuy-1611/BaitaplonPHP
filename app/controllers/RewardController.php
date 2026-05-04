<?php
class RewardController extends Controller
{
    public function index()
    {
        $this->requireRole('rewards');
        $model = $this->model('RewardModel');
        $empModel = $this->model('EmployeeModel');

        // Xử lý thêm/sửa/xóa
        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            if ($action === 'create') {
                $model->create([
                    'employee_id'     => intval($this->post('employee_id')),
                    'type'            => $this->post('type'),
                    'reason'          => $this->post('reason'),
                    'amount'          => floatval(str_replace(['.', ','], '', $this->post('amount'))),
                    'date'            => $this->post('date'),
                    'decision_number' => $this->post('decision_number'),
                ]);
                $this->logActivity('Thêm', 'rewards', 'Thêm ' . $this->post('type') . ' cho NV ID: ' . $this->post('employee_id'));
                $this->setFlash('success', 'Thêm khen thưởng/kỷ luật thành công.');
            } elseif ($action === 'update') {
                $id = intval($this->post('id'));
                $model->update($id, [
                    'employee_id'     => intval($this->post('employee_id')),
                    'type'            => $this->post('type'),
                    'reason'          => $this->post('reason'),
                    'amount'          => floatval(str_replace(['.', ','], '', $this->post('amount'))),
                    'date'            => $this->post('date'),
                    'decision_number' => $this->post('decision_number'),
                ]);
                $this->logActivity('Sửa', 'rewards', 'Sửa khen thưởng/kỷ luật ID: ' . $id);
                $this->setFlash('success', 'Cập nhật thành công.');
            } elseif ($action === 'delete') {
                $id = intval($this->post('id'));
                $model->delete($id);
                $this->logActivity('Xóa', 'rewards', 'Xóa khen thưởng/kỷ luật ID: ' . $id);
                $this->setFlash('success', 'Xóa thành công.');
            }
            $this->redirect('rewards');
        }

        // Bộ lọc
        $filters = [
            'type'  => $this->get('type'),
            'month' => intval($this->get('month', date('m'))),
            'year'  => intval($this->get('year', date('Y'))),
        ];

        $rewards   = $model->getList($filters);
        $stats     = $model->getStats($filters['month'], $filters['year']);
        $employees = $empModel->getActiveEmployees();

        $this->view('rewards/index', [
            'title' => 'Khen thưởng & Kỷ luật',
            'rewards' => $rewards,
            'stats' => $stats,
            'employees' => $employees,
            'filters' => $filters,
        ]);
    }
}
