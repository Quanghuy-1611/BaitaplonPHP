<?php
class ReportController extends Controller
{
    public function index()
    {
        $this->requireRole('reports');
        $model = $this->model('ReportModel');

        $reportType = $this->get('type', 'salary');
        $month = intval($this->get('month', date('n')));
        $year  = intval($this->get('year', date('Y')));

        $data = [];

        switch ($reportType) {
            case 'salary':
                $data['salaryReport'] = $model->salaryByMonth($month, $year);
                $data['totals'] = $model->salaryTotals($month, $year);
                break;
            case 'department':
                $data['deptReport'] = $model->salaryByDepartment($month, $year);
                break;
            case 'attendance':
                $data['attReport'] = $model->attendanceReport($month, $year);
                break;
            case 'employee':
                $data['empStats'] = $model->employeeStats();
                break;
        }

        $pageTitle = 'Báo cáo thống kê';
        $data = array_merge($data, compact('pageTitle', 'reportType', 'month', 'year'));
        $this->view('reports/index', $data);
    }
}
