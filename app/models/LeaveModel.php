<?php
class LeaveModel extends Model
{
    protected $table = 'leaves';

    public function getList($filters = [])
    {
        $where = '1=1';
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            $where .= " AND l.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }
        if (!empty($filters['department_id'])) {
            $where .= " AND e.department_id = ?";
            $params[] = $filters['department_id'];
            $types .= 'i';
        }
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where .= " AND (MONTH(l.start_date) = ? OR MONTH(l.end_date) = ?) AND (YEAR(l.start_date) = ? OR YEAR(l.end_date) = ?)";
            $params = array_merge($params, [$filters['month'], $filters['month'], $filters['year'], $filters['year']]);
            $types .= 'iiii';
        }

        return $this->query(
            "SELECT l.*, e.employee_code, e.full_name, d.name as dept_name
             FROM leaves l
             JOIN employees e ON l.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE $where ORDER BY l.created_at DESC",
            $params, $types
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO leaves (employee_id, leave_type, start_date, end_date, days, reason, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'Chờ duyệt', NOW())",
            [$data['employee_id'], $data['leave_type'], $data['start_date'],
             $data['end_date'], $data['days'], $data['reason']],
            'isssds'
        );
    }

    public function approve($id, $approvedBy)
    {
        return $this->execute(
            "UPDATE leaves SET status = 'Đã duyệt', approved_by = ?, approved_at = NOW() WHERE id = ?",
            [$approvedBy, $id], 'ii'
        );
    }

    public function reject($id, $approvedBy)
    {
        return $this->execute(
            "UPDATE leaves SET status = 'Từ chối', approved_by = ?, approved_at = NOW() WHERE id = ?",
            [$approvedBy, $id], 'ii'
        );
    }

    public function getEmployeeLeaves($empId, $year = 0)
    {
        if ($year) {
            return $this->query(
                "SELECT * FROM leaves WHERE employee_id = ? AND YEAR(start_date) = ? ORDER BY start_date DESC",
                [$empId, $year], 'ii'
            );
        }
        return $this->query(
            "SELECT * FROM leaves WHERE employee_id = ? ORDER BY start_date DESC",
            [$empId], 'i'
        );
    }

    public function getLeaveBalance($empId, $year)
    {
        $row = $this->queryOne(
            "SELECT COALESCE(SUM(days), 0) as used_days
             FROM leaves WHERE employee_id = ? AND YEAR(start_date) = ? AND status = 'Đã duyệt'",
            [$empId, $year], 'ii'
        );
        $annualLeave = 12; // 12 ngày phép/năm
        return [
            'annual'    => $annualLeave,
            'used'      => $row['used_days'],
            'remaining' => $annualLeave - $row['used_days'],
        ];
    }

    public function getPendingCount()
    {
        return $this->count("status = 'Chờ duyệt'");
    }

    public function getStats($month, $year)
    {
        return $this->queryOne(
            "SELECT
                COUNT(*) as total,
                COUNT(CASE WHEN status='Chờ duyệt' THEN 1 END) as pending,
                COUNT(CASE WHEN status='Đã duyệt' THEN 1 END) as approved,
                COUNT(CASE WHEN status='Từ chối' THEN 1 END) as rejected
             FROM leaves
             WHERE MONTH(start_date) = ? AND YEAR(start_date) = ?",
            [$month, $year], 'ii'
        );
    }
}
