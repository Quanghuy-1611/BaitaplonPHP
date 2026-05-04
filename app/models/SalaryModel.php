<?php
class SalaryModel extends Model
{
    protected $table = 'salary';

    public function getByMonth($month, $year, $status = '')
    {
        $where = "s.month = ? AND s.year = ?";
        $params = [$month, $year];
        $types = 'ii';

        if ($status) {
            $where .= " AND s.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        return $this->query(
            "SELECT s.*, e.employee_code, e.full_name, d.name as dept_name, p.name as pos_name
             FROM salary s
             JOIN employees e ON s.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN positions p ON e.position_id = p.id
             WHERE $where ORDER BY d.name, e.employee_code",
            $params, $types
        );
    }

    public function getDetail($id)
    {
        return $this->queryOne(
            "SELECT s.*, e.employee_code, e.full_name, e.gender, e.birth_date, e.id_card,
                    e.phone, e.email, e.bank_account, e.bank_name, e.contract_type,
                    d.name as dept_name, p.name as pos_name
             FROM salary s
             JOIN employees e ON s.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN positions p ON e.position_id = p.id
             WHERE s.id = ?",
            [$id], 'i'
        );
    }

    public function getTotals($month, $year)
    {
        return $this->queryOne(
            "SELECT
                COUNT(*) as total_count,
                COALESCE(SUM(base_salary), 0) as t_base,
                COALESCE(SUM(total_allowance), 0) as t_allowance,
                COALESCE(SUM(overtime_pay), 0) as t_ot,
                COALESCE(SUM(total_reward), 0) as t_reward,
                COALESCE(SUM(total_discipline), 0) as t_disc,
                COALESCE(SUM(gross_salary), 0) as t_gross,
                COALESCE(SUM(bhxh + bhyt + bhtn), 0) as t_insurance,
                COALESCE(SUM(tax), 0) as t_tax,
                COALESCE(SUM(advance_salary), 0) as t_advance,
                COALESCE(SUM(net_salary), 0) as t_net
             FROM salary WHERE month = ? AND year = ?",
            [$month, $year], 'ii'
        );
    }

    public function calculate($empId, $month, $year, $data)
    {
        // Kiểm tra đã tồn tại chưa
        $exists = $this->queryOne(
            "SELECT id FROM salary WHERE employee_id = ? AND month = ? AND year = ?",
            [$empId, $month, $year], 'iii'
        );

        $sql = $exists
            ? "UPDATE salary SET base_salary=?, working_days=?, actual_working_days=?,
               total_allowance=?, overtime_hours=?, overtime_pay=?, total_reward=?,
               total_discipline=?, gross_salary=?, bhxh=?, bhyt=?, bhtn=?, tax=?,
               advance_salary=?, other_deduction=?, net_salary=?, status='Chờ duyệt'
               WHERE employee_id=? AND month=? AND year=?"
            : "INSERT INTO salary (base_salary, working_days, actual_working_days,
               total_allowance, overtime_hours, overtime_pay, total_reward,
               total_discipline, gross_salary, bhxh, bhyt, bhtn, tax,
               advance_salary, other_deduction, net_salary, employee_id, month, year)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->execute($sql, [
            $data['base_salary'], $data['working_days'], $data['actual_working_days'],
            $data['total_allowance'], $data['overtime_hours'], $data['overtime_pay'],
            $data['total_reward'], $data['total_discipline'], $data['gross_salary'],
            $data['bhxh'], $data['bhyt'], $data['bhtn'], $data['tax'],
            $data['advance_salary'], $data['other_deduction'], $data['net_salary'],
            $empId, $month, $year
        ], 'diddddddddddddddiii');
    }

    public function approve($id)
    {
        return $this->execute(
            "UPDATE salary SET status = 'Đã duyệt' WHERE id = ?",
            [$id], 'i'
        );
    }

    public function approveAll($month, $year)
    {
        return $this->execute(
            "UPDATE salary SET status = 'Đã duyệt' WHERE month = ? AND year = ? AND status = 'Chờ duyệt'",
            [$month, $year], 'ii'
        );
    }

    public function markPaid($id)
    {
        return $this->execute(
            "UPDATE salary SET status = 'Đã thanh toán' WHERE id = ?",
            [$id], 'i'
        );
    }

    public function getByEmployeeId($empId, $month, $year)
    {
        return $this->queryOne(
            "SELECT * FROM salary WHERE employee_id = ? AND month = ? AND year = ?",
            [$empId, $month, $year], 'iii'
        );
    }

    public function getEmployeeSalaryHistory($empId, $limit = 12)
    {
        return $this->query(
            "SELECT * FROM salary WHERE employee_id = ?
             ORDER BY year DESC, month DESC LIMIT $limit",
            [$empId], 'i'
        );
    }

    public function getMonthlyTrend($months = 6)
    {
        return $this->query(
            "SELECT month, year, SUM(net_salary) as total_net, COUNT(*) as emp_count
             FROM salary GROUP BY year, month
             ORDER BY year DESC, month DESC LIMIT ?",
            [$months], 'i'
        );
    }

    // Tạm ứng lương
    public function getAdvances($month = 0, $year = 0, $status = '')
    {
        $where = '1=1';
        $params = [];
        $types = '';

        if ($month) { $where .= " AND sa.month = ?"; $params[] = $month; $types .= 'i'; }
        if ($year) { $where .= " AND sa.year = ?"; $params[] = $year; $types .= 'i'; }
        if ($status) { $where .= " AND sa.status = ?"; $params[] = $status; $types .= 's'; }

        return $this->query(
            "SELECT sa.*, e.employee_code, e.full_name, d.name as dept_name
             FROM salary_advance sa
             JOIN employees e ON sa.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE $where ORDER BY sa.created_at DESC",
            $params, $types
        );
    }

    public function createAdvance($data)
    {
        return $this->insert(
            "INSERT INTO salary_advance (employee_id, month, year, amount, reason, status, created_at)
             VALUES (?, ?, ?, ?, ?, 'Chờ duyệt', NOW())",
            [$data['employee_id'], $data['month'], $data['year'],
             $data['amount'], $data['reason']],
            'iiids'
        );
    }

    public function approveAdvance($id)
    {
        return $this->execute(
            "UPDATE salary_advance SET status = 'Đã duyệt', approved_at = NOW() WHERE id = ?",
            [$id], 'i'
        );
    }

    public function rejectAdvance($id)
    {
        return $this->execute(
            "UPDATE salary_advance SET status = 'Từ chối' WHERE id = ?",
            [$id], 'i'
        );
    }

    public function getApprovedAdvance($empId, $month, $year)
    {
        $row = $this->queryOne(
            "SELECT COALESCE(SUM(amount), 0) as total
             FROM salary_advance
             WHERE employee_id = ? AND month = ? AND year = ? AND status = 'Đã duyệt'",
            [$empId, $month, $year], 'iii'
        );
        return $row['total'] ?? 0;
    }
}
