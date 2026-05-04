<?php
class DepartmentModel extends Model
{
    protected $table = 'departments';

    public function getAllWithCount()
    {
        return $this->query(
            "SELECT d.*, COUNT(e.id) as emp_count
             FROM departments d
             LEFT JOIN employees e ON d.id = e.department_id AND e.status = 'Đang làm'
             GROUP BY d.id ORDER BY d.name"
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO departments (name, description, manager_name, phone) VALUES (?, ?, ?, ?)",
            [$data['name'], $data['description'], $data['manager_name'], $data['phone']],
            'ssss'
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE departments SET name=?, description=?, manager_name=?, phone=? WHERE id=?",
            [$data['name'], $data['description'], $data['manager_name'], $data['phone'], $id],
            'ssssi'
        );
    }

    public function hasEmployees($id)
    {
        $row = $this->queryOne(
            "SELECT COUNT(*) as cnt FROM employees WHERE department_id = ?",
            [$id], 'i'
        );
        return $row['cnt'] > 0;
    }

    public function getSelectList()
    {
        return $this->query("SELECT id, name FROM departments ORDER BY name");
    }
}
