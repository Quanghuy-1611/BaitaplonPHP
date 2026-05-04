<?php
class AllowanceModel extends Model
{
    protected $table = 'allowances';

    public function getAllWithCount()
    {
        return $this->query(
            "SELECT a.*, COUNT(ea.id) as emp_count
             FROM allowances a
             LEFT JOIN employee_allowances ea ON a.id = ea.allowance_id
             GROUP BY a.id ORDER BY a.name"
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO allowances (name, default_amount, description) VALUES (?, ?, ?)",
            [$data['name'], $data['default_amount'], $data['description']],
            'sds'
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE allowances SET name=?, default_amount=?, description=? WHERE id=?",
            [$data['name'], $data['default_amount'], $data['description'], $id],
            'sdsi'
        );
    }

    public function hasEmployees($allowanceId)
    {
        $row = $this->queryOne(
            "SELECT COUNT(*) as cnt FROM employee_allowances WHERE allowance_id = ?",
            [$allowanceId], 'i'
        );
        return ($row['cnt'] ?? 0) > 0;
    }

    public function isAssigned($empId, $allowanceId)
    {
        return $this->queryOne(
            "SELECT id FROM employee_allowances WHERE employee_id=? AND allowance_id=?",
            [$empId, $allowanceId], 'ii'
        ) !== null;
    }

    public function getAllAssignments()
    {
        return $this->query(
            "SELECT ea.*, e.employee_code, e.full_name, a.name as allowance_name
             FROM employee_allowances ea
             JOIN employees e ON ea.employee_id = e.id
             JOIN allowances a ON ea.allowance_id = a.id
             ORDER BY e.employee_code, a.name"
        );
    }

    public function getEmployeeAllowances($empId)
    {
        return $this->query(
            "SELECT ea.*, a.name as allowance_name
             FROM employee_allowances ea
             JOIN allowances a ON ea.allowance_id = a.id
             WHERE ea.employee_id = ?",
            [$empId], 'i'
        );
    }

    public function assignToEmployee($empId, $allowanceId, $amount)
    {
        $exists = $this->queryOne(
            "SELECT id FROM employee_allowances WHERE employee_id=? AND allowance_id=?",
            [$empId, $allowanceId], 'ii'
        );
        if ($exists) {
            return $this->execute(
                "UPDATE employee_allowances SET amount=? WHERE employee_id=? AND allowance_id=?",
                [$amount, $empId, $allowanceId], 'dii'
            );
        }
        return $this->execute(
            "INSERT INTO employee_allowances (employee_id, allowance_id, amount) VALUES (?, ?, ?)",
            [$empId, $allowanceId, $amount], 'iid'
        );
    }

    public function removeFromEmployee($id)
    {
        return $this->execute(
            "DELETE FROM employee_allowances WHERE id = ?",
            [$id], 'i'
        );
    }

    public function getTotalAllowance($empId)
    {
        $row = $this->queryOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM employee_allowances WHERE employee_id = ?",
            [$empId], 'i'
        );
        return $row['total'] ?? 0;
    }
}
