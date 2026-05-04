<?php
class PositionModel extends Model
{
    protected $table = 'positions';

    public function getAllWithCount()
    {
        return $this->query(
            "SELECT p.*, d.name as dept_name, COUNT(e.id) as emp_count
             FROM positions p
             LEFT JOIN departments d ON p.department_id = d.id
             LEFT JOIN employees e ON p.id = e.position_id AND e.status = 'Đang làm'
             GROUP BY p.id ORDER BY d.name, p.name"
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO positions (name, department_id, base_salary, description) VALUES (?, ?, ?, ?)",
            [$data['name'], $data['department_id'], $data['base_salary'], $data['description']],
            'sids'
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE positions SET name=?, department_id=?, base_salary=?, description=? WHERE id=?",
            [$data['name'], $data['department_id'], $data['base_salary'], $data['description'], $id],
            'sidsi'
        );
    }

    public function hasEmployees($id)
    {
        $row = $this->queryOne(
            "SELECT COUNT(*) as cnt FROM employees WHERE position_id = ?",
            [$id], 'i'
        );
        return $row['cnt'] > 0;
    }

    public function getSelectList()
    {
        return $this->query("SELECT id, name, base_salary FROM positions ORDER BY name");
    }

    public function getByDepartment($deptId)
    {
        return $this->query(
            "SELECT id, name, base_salary FROM positions WHERE department_id = ? ORDER BY name",
            [$deptId], 'i'
        );
    }
}
