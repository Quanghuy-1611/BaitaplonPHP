<?php
class RewardModel extends Model
{
    protected $table = 'rewards';

    public function getList($filters = [])
    {
        $where = '1=1';
        $params = [];
        $types = '';

        if (!empty($filters['type'])) {
            $where .= " AND r.type = ?";
            $params[] = $filters['type'];
            $types .= 's';
        }
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where .= " AND MONTH(r.date) = ? AND YEAR(r.date) = ?";
            $params[] = $filters['month'];
            $params[] = $filters['year'];
            $types .= 'ii';
        }

        return $this->query(
            "SELECT r.*, e.employee_code, e.full_name, d.name as dept_name
             FROM rewards r
             JOIN employees e ON r.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE $where ORDER BY r.date DESC",
            $params, $types
        );
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO rewards (employee_id, type, reason, amount, date, decision_number)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$data['employee_id'], $data['type'], $data['reason'],
             $data['amount'], $data['date'], $data['decision_number'] ?? null],
            'issdss'
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE rewards SET employee_id=?, type=?, reason=?, amount=?,
             date=?, decision_number=? WHERE id=?",
            [$data['employee_id'], $data['type'], $data['reason'],
             $data['amount'], $data['date'], $data['decision_number'] ?? null, $id],
            'issdssi'
        );
    }

    public function getByEmployee($empId, $month = 0, $year = 0)
    {
        if ($month && $year) {
            return $this->query(
                "SELECT * FROM rewards
                 WHERE employee_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
                 ORDER BY date DESC",
                [$empId, $month, $year], 'iii'
            );
        }
        return $this->query(
            "SELECT * FROM rewards WHERE employee_id = ? ORDER BY date DESC",
            [$empId], 'i'
        );
    }

    public function getStats($month, $year)
    {
        return $this->queryOne(
            "SELECT
                COUNT(CASE WHEN type='Khen thưởng' THEN 1 END) as reward_count,
                COALESCE(SUM(CASE WHEN type='Khen thưởng' THEN amount END), 0) as reward_total,
                COUNT(CASE WHEN type='Kỷ luật' THEN 1 END) as disc_count,
                COALESCE(SUM(CASE WHEN type='Kỷ luật' THEN amount END), 0) as disc_total
             FROM rewards
             WHERE MONTH(date) = ? AND YEAR(date) = ?",
            [$month, $year], 'ii'
        );
    }

    public function getEmployeeTotals($empId, $month, $year)
    {
        return $this->queryOne(
            "SELECT
                COALESCE(SUM(CASE WHEN type='Khen thưởng' THEN amount END), 0) as total_reward,
                COALESCE(SUM(CASE WHEN type='Kỷ luật' THEN amount END), 0) as total_discipline
             FROM rewards
             WHERE employee_id = ? AND MONTH(date) = ? AND YEAR(date) = ?",
            [$empId, $month, $year], 'iii'
        );
    }
}
