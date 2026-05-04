<?php
class AttendanceModel extends Model
{
    protected $table = 'attendance';

    public function getByMonth($month, $year, $deptId = 0)
    {
        $where = "MONTH(a.work_date) = ? AND YEAR(a.work_date) = ?";
        $params = [$month, $year];
        $types = 'ii';

        if ($deptId > 0) {
            $where .= " AND e.department_id = ?";
            $params[] = $deptId;
            $types .= 'i';
        }

        return $this->query(
            "SELECT a.*, e.employee_code, e.full_name, d.name as dept_name
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE $where
             ORDER BY a.work_date DESC, e.employee_code",
            $params, $types
        );
    }

    public function getStats($month, $year)
    {
        return $this->queryOne(
            "SELECT
                COUNT(CASE WHEN status IN ('Đi làm','Đi muộn') THEN 1 END) as di_lam,
                COUNT(CASE WHEN status='Nghỉ phép' THEN 1 END) as nghi_phep,
                COUNT(CASE WHEN status='Vắng' THEN 1 END) as nghi_kp,
                SUM(COALESCE(overtime_hours,0)) as total_ot
             FROM attendance
             WHERE MONTH(work_date) = ? AND YEAR(work_date) = ?",
            [$month, $year], 'ii'
        );
    }

    public function addSingle($data)
    {
        $exists = $this->queryOne(
            "SELECT id FROM attendance WHERE employee_id = ? AND work_date = ?",
            [$data['employee_id'], $data['work_date']], 'is'
        );

        if ($exists) {
            return $this->execute(
                "UPDATE attendance SET status=?, check_in=?, check_out=?, overtime_hours=?, note=?
                 WHERE employee_id=? AND work_date=?",
                [$data['status'], $data['check_in'], $data['check_out'],
                 $data['overtime_hours'], $data['note'], $data['employee_id'], $data['work_date']],
                'sssdsis'
            );
        }

        return $this->execute(
            "INSERT INTO attendance (employee_id, work_date, status, check_in, check_out, overtime_hours, note)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$data['employee_id'], $data['work_date'], $data['status'],
             $data['check_in'], $data['check_out'], $data['overtime_hours'], $data['note']],
            'issssds'
        );
    }

    public function addBulk($date, $employeeIds, $status, $checkIn, $checkOut)
    {
        $count = 0;
        foreach ($employeeIds as $empId) {
            $this->addSingle([
                'employee_id' => $empId,
                'work_date' => $date,
                'status' => $status,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'overtime_hours' => 0,
                'note' => '',
            ]);
            $count++;
        }
        return $count;
    }

    public function getEmployeeAttendance($empId, $month, $year)
    {
        return $this->query(
            "SELECT * FROM attendance
             WHERE employee_id = ? AND MONTH(work_date) = ? AND YEAR(work_date) = ?
             ORDER BY work_date",
            [$empId, $month, $year], 'iii'
        );
    }

    public function getEmployeeStats($empId, $month, $year)
    {
        return $this->queryOne(
            "SELECT
                COUNT(CASE WHEN status IN ('Đi làm','Đi muộn') THEN 1 END) as ngay_cong,
                COUNT(CASE WHEN status='Nghỉ phép' THEN 1 END) as nghi_phep,
                COUNT(CASE WHEN status='Vắng' THEN 1 END) as nghi_kp,
                SUM(COALESCE(overtime_hours,0)) as overtime
             FROM attendance
             WHERE employee_id = ? AND MONTH(work_date) = ? AND YEAR(work_date) = ?",
            [$empId, $month, $year], 'iii'
        );
    }

    public function deleteRecord($id)
    {
        return $this->delete($id);
    }
}
