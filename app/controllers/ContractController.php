<?php
class ContractController extends Controller
{
    public function index()
    {
        $this->requireRole('contracts');

        $contractModel = $this->model('ContractModel');
        $employeeModel = $this->model('EmployeeModel');

        // Handle POST actions
        if ($this->isPost()) {
            $this->verifyCsrf();
            $action = $this->post('action');

            switch ($action) {
                case 'create':
                    $this->createContract($contractModel);
                    break;
                case 'update':
                    $this->updateContract($contractModel);
                    break;
                case 'delete':
                    $this->deleteContract($contractModel);
                    break;
            }

            $this->redirect('contracts');
            return;
        }

        // Filters
        $filters = [
            'status' => $this->get('status') ?: '',
            'contract_type' => $this->get('contract_type') ?: '',
        ];

        $contracts = $contractModel->getList($filters);
        $employees = $employeeModel->getActiveEmployees();

        // Stats
        $contractStats = $contractModel->getStats();
        $stats = [
            'active' => $contractStats['active'] ?? 0,
            'expired' => $contractStats['expired'] ?? 0,
            'expiring' => $contractStats['expiring'] ?? 0,
        ];

        $contractTypes = [
            'Không xác định thời hạn',
            'Xác định thời hạn',
            'Thử việc',
        ];

        $this->view('contracts/index', [
            'title' => 'Quản lý hợp đồng',
            'contracts' => $contracts,
            'employees' => $employees,
            'filters' => $filters,
            'stats' => $stats,
            'contractTypes' => $contractTypes,
        ]);
    }

    private function createContract($contractModel)
    {
        $data = [
            'employee_id' => $this->post('employee_id'),
            'contract_number' => $this->post('contract_number'),
            'contract_type' => $this->post('contract_type'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'base_salary' => str_replace(['.', ','], '', $this->post('base_salary')),
            'note' => $this->post('note'),
        ];

        if ($contractModel->create($data)) {
            $this->logActivity('Thêm', 'contracts', 'Tạo hợp đồng: ' . $data['contract_number']);
            $this->setFlash('success', 'Tạo hợp đồng thành công.');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi tạo hợp đồng.');
        }
    }

    private function updateContract($contractModel)
    {
        $id = intval($this->post('id'));

        $data = [
            'contract_number' => $this->post('contract_number'),
            'contract_type' => $this->post('contract_type'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'base_salary' => str_replace(['.', ','], '', $this->post('base_salary')),
            'note' => $this->post('note'),
        ];

        if ($contractModel->update($id, $data)) {
            $this->logActivity('Sửa', 'contracts', 'Cập nhật hợp đồng ID: ' . $id);
            $this->setFlash('success', 'Cập nhật hợp đồng thành công.');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi cập nhật hợp đồng.');
        }
    }

    private function deleteContract($contractModel)
    {
        $id = intval($this->post('id'));

        if ($contractModel->delete($id)) {
            $this->logActivity('Xóa', 'contracts', 'Xóa hợp đồng ID: ' . $id);
            $this->setFlash('success', 'Xóa hợp đồng thành công.');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi xóa hợp đồng.');
        }
    }
}
