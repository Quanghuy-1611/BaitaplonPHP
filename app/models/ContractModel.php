<?php
class ContractModel extends Model
{
    protected $table = 'contracts';

    public function getList($filters = [])
    {
        $where = '1=1';
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $where .= " AND c.end_date >= CURDATE()";
            } elseif ($filters['status'] === 'expired') {
                $where .= " AND c.end_date < CURDATE()";
            } elseif ($filters['status'] === 'expiring') {
                $where .= " AND c.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
            }
        }
        if (!empty($filters['contract_type'])) {
            $where .= " AND c.contract_type = ?";
            $params[] = $filters['contract_type'];
            $types .= 's';
        }

        return $this->query(
            "SELECT c.*, e.employee_code, e.full_name, d.name as dept_name
             FROM contracts c
             JOIN employees e ON c.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE $where ORDER BY c.end_date ASC",
            $params, $types
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO contracts (employee_id, contract_number, contract_type,
             start_date, end_date, base_salary, note, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [$data['employee_id'], $data['contract_number'], $data['contract_type'],
             $data['start_date'], $data['end_date'], $data['base_salary'], $data['note']],
            'issssds'
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE contracts SET contract_number=?, contract_type=?,
             start_date=?, end_date=?, base_salary=?, note=? WHERE id=?",
            [$data['contract_number'], $data['contract_type'], $data['start_date'],
             $data['end_date'], $data['base_salary'], $data['note'], $id],
            'ssssdsi'
        );
    }

    public function getByEmployee($empId)
    {
        return $this->query(
            "SELECT * FROM contracts WHERE employee_id = ? ORDER BY start_date DESC",
            [$empId], 'i'
        );
    }

    public function getExpiringContracts($days = 30)
    {
        return $this->query(
            "SELECT c.*, e.employee_code, e.full_name, d.name as dept_name
             FROM contracts c
             JOIN employees e ON c.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE c.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY c.end_date ASC",
            [$days], 'i'
        );
    }

    public function getStats()
    {
        return $this->queryOne(
            "SELECT
                COUNT(*) as total,
                COUNT(CASE WHEN end_date >= CURDATE() THEN 1 END) as active,
                COUNT(CASE WHEN end_date < CURDATE() THEN 1 END) as expired,
                COUNT(CASE WHEN end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as expiring
             FROM contracts"
        );
    }
}
