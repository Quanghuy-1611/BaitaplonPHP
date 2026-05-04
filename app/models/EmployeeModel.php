<?php
class EmployeeModel extends Model
{
    protected $table = 'employees';

    public function getList($filters = [], $page = 1, $perPage = 15)
    {
        $where = '1=1';
        $params = [];
        $types = '';

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $where .= " AND (e.full_name LIKE ? OR e.employee_code LIKE ? OR e.phone LIKE ?)";
            $params = array_merge($params, [$search, $search, $search]);
            $types .= 'sss';
        }
        if (!empty($filters['department_id'])) {
            $where .= " AND e.department_id = ?";
            $params[] = $filters['department_id'];
            $types .= 'i';
        }
        if (!empty($filters['status'])) {
            $where .= " AND e.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }
        if (!empty($filters['position_id'])) {
            $where .= " AND e.position_id = ?";
            $params[] = $filters['position_id'];
            $types .= 'i';
        }

        // Đếm tổng
        $countSql = "SELECT COUNT(*) as cnt FROM employees e WHERE $where";
        $total = $this->queryOne($countSql, $params, $types)['cnt'];

        // Lấy dữ liệu có phân trang
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT e.*, d.name as dept_name, p.name as pos_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN positions p ON e.position_id = p.id
                WHERE $where
                ORDER BY e.employee_code
                LIMIT $perPage OFFSET $offset";

        $rows = $this->query($sql, $params, $types);

        return [
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    public function getDetail($id)
    {
        return $this->queryOne(
            "SELECT e.*, d.name as dept_name, p.name as pos_name
             FROM employees e
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN positions p ON e.position_id = p.id
             WHERE e.id = ?",
            [$id], 'i'
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO employees (employee_code, full_name, gender, birth_date, id_card,
             phone, email, address, department_id, position_id, hire_date, contract_type,
             base_salary, bank_account, bank_name, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['employee_code'], $data['full_name'], $data['gender'],
                $data['birth_date'], $data['id_card'], $data['phone'],
                $data['email'], $data['address'], $data['department_id'],
                $data['position_id'], $data['hire_date'], $data['contract_type'],
                $data['base_salary'], $data['bank_account'], $data['bank_name'],
                $data['status'] ?? 'Đang làm'
            ],
            'ssssssssiissdsss'
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE employees SET full_name=?, gender=?, birth_date=?, id_card=?,
             phone=?, email=?, address=?, department_id=?, position_id=?,
             hire_date=?, contract_type=?, base_salary=?, bank_account=?,
             bank_name=?, status=? WHERE id=?",
            [
                $data['full_name'], $data['gender'], $data['birth_date'],
                $data['id_card'], $data['phone'], $data['email'], $data['address'],
                $data['department_id'], $data['position_id'], $data['hire_date'],
                $data['contract_type'], $data['base_salary'], $data['bank_account'],
                $data['bank_name'], $data['status'], $id
            ],
            'sssssssiissdsssi'
        );
    }

    public function codeExists($code, $excludeId = 0)
    {
        return $this->queryOne(
            "SELECT id FROM employees WHERE employee_code = ? AND id != ?",
            [$code, $excludeId], 'si'
        ) !== null;
    }

    public function getActiveEmployees()
    {
        return $this->query(
            "SELECT id, employee_code, full_name, department_id, base_salary
             FROM employees WHERE status = 'Đang làm' ORDER BY employee_code"
        );
    }

    public function getActiveCount()
    {
        return $this->count("status = 'Đang làm'");
    }

    public function getNewThisMonth()
    {
        return $this->count(
            "MONTH(hire_date) = ? AND YEAR(hire_date) = ?",
            [date('n'), date('Y')], 'ii'
        );
    }

    public function getByDepartmentStats()
    {
        return $this->query(
            "SELECT d.name, COUNT(e.id) as cnt
             FROM departments d
             LEFT JOIN employees e ON d.id = e.department_id AND e.status = 'Đang làm'
             GROUP BY d.id ORDER BY cnt DESC"
        );
    }

    public function getRecentEmployees($limit = 5)
    {
        return $this->query(
            "SELECT e.*, d.name as dept_name, p.name as pos_name
             FROM employees e
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN positions p ON e.position_id = p.id
             ORDER BY e.id DESC LIMIT $limit"
        );
    }

    public function generateNextCode()
    {
        $row = $this->queryOne(
            "SELECT employee_code FROM employees ORDER BY id DESC LIMIT 1"
        );
        if ($row) {
            $num = intval(substr($row['employee_code'], 2)) + 1;
            return 'NV' . str_pad($num, 4, '0', STR_PAD_LEFT);
        }
        return 'NV0001';
    }
}
