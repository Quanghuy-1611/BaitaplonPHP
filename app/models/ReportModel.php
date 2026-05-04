<?php
class ReportModel extends Model
{
    protected $table = 'salary';

    public function salaryByMonth($month, $year)
    {
        return $this->query(
            "SELECT s.*, e.employee_code, e.full_name, d.name as dept_name, p.name as pos_name
             FROM salary s
             JOIN employees e ON s.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN positions p ON e.position_id = p.id
             WHERE s.month = ? AND s.year = ?
             ORDER BY d.name, e.employee_code",
            [$month, $year], 'ii'
        );
    }

    public function salaryByDepartment($month, $year)
    {
        return $this->query(
            "SELECT d.name as dept_name,
                COUNT(s.id) as emp_count,
                SUM(s.gross_salary) as total_gross,
                SUM(s.net_salary) as total_net,
                AVG(s.net_salary) as avg_net,
                MAX(s.net_salary) as max_net,
                MIN(s.net_salary) as min_net
             FROM salary s
             JOIN employees e ON s.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE s.month = ? AND s.year = ?
             GROUP BY d.id, d.name ORDER BY total_net DESC",
            [$month, $year], 'ii'
        );
    }

    public function attendanceReport($month, $year)
    {
        return $this->query(
            "SELECT e.employee_code, e.full_name, d.name as dept_name,
                COUNT(CASE WHEN a.status IN ('Đi làm','Công tác') THEN 1 END) as di_lam,
                COUNT(CASE WHEN a.status='Nghỉ phép' THEN 1 END) as nghi_phep,
                COUNT(CASE WHEN a.status='Nghỉ không phép' THEN 1 END) as nghi_kp,
                COUNT(CASE WHEN a.status='Nghỉ lễ' THEN 1 END) as nghi_le,
                SUM(COALESCE(a.overtime_hours, 0)) as total_ot
             FROM employees e
             LEFT JOIN attendance a ON e.id = a.employee_id
                AND MONTH(a.date) = ? AND YEAR(a.date) = ?
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE e.status = 'Đang làm'
             GROUP BY e.id ORDER BY e.employee_code",
            [$month, $year], 'ii'
        );
    }

    public function employeeStats()
    {
        $result = [];

        $result['total_active'] = $this->queryOne(
            "SELECT COUNT(*) as cnt FROM employees WHERE status='Đang làm'"
        )['cnt'];

        $result['avg_salary'] = $this->queryOne(
            "SELECT AVG(base_salary) as val FROM employees WHERE status='Đang làm'"
        )['val'] ?? 0;

        $result['total_salary_fund'] = $this->queryOne(
            "SELECT SUM(base_salary) as val FROM employees WHERE status='Đang làm'"
        )['val'] ?? 0;

        $result['by_department'] = $this->query(
            "SELECT d.name, COUNT(e.id) as cnt FROM departments d
             LEFT JOIN employees e ON d.id = e.department_id AND e.status='Đang làm'
             GROUP BY d.id ORDER BY cnt DESC"
        );

        $result['by_gender'] = $this->query(
            "SELECT gender, COUNT(*) as cnt FROM employees WHERE status='Đang làm' GROUP BY gender"
        );

        $result['by_contract'] = $this->query(
            "SELECT contract_type, COUNT(*) as cnt FROM employees WHERE status='Đang làm' GROUP BY contract_type"
        );

        $result['by_status'] = $this->query(
            "SELECT status, COUNT(*) as cnt FROM employees GROUP BY status"
        );

        return $result;
    }

    public function salaryTotals($month, $year)
    {
        return $this->queryOne(
            "SELECT
                COALESCE(SUM(base_salary),0) as t_base,
                COALESCE(SUM(total_allowance),0) as t_allowance,
                COALESCE(SUM(overtime_pay),0) as t_ot,
                COALESCE(SUM(total_reward),0) as t_reward,
                COALESCE(SUM(total_discipline),0) as t_disc,
                COALESCE(SUM(gross_salary),0) as t_gross,
                COALESCE(SUM(bhxh+bhyt+bhtn),0) as t_insurance,
                COALESCE(SUM(tax),0) as t_tax,
                COALESCE(SUM(advance_salary),0) as t_advance,
                COALESCE(SUM(net_salary),0) as t_net,
                COUNT(*) as t_count
             FROM salary WHERE month=? AND year=?",
            [$month, $year], 'ii'
        );
    }

    public function exportSalaryCsv($month, $year)
    {
        return $this->query(
            "SELECT e.employee_code, e.full_name, d.name as dept_name, p.name as pos_name,
                    s.base_salary, s.total_allowance, s.overtime_pay, s.total_reward,
                    s.total_discipline, s.gross_salary, s.bhxh, s.bhyt, s.bhtn, s.tax,
                    s.advance_salary, s.net_salary, s.status
             FROM salary s
             JOIN employees e ON s.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN positions p ON e.position_id = p.id
             WHERE s.month = ? AND s.year = ?
             ORDER BY e.employee_code",
            [$month, $year], 'ii'
        );
    }
}
