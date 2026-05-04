<?php
class ActivityLogModel extends Model
{
    protected $table = 'activity_log';

    public function getRecent($limit = 50, $filters = [])
    {
        $where = '1=1';
        $params = [];
        $types = '';

        if (!empty($filters['user_id'])) {
            $where .= " AND al.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        if (!empty($filters['module'])) {
            $where .= " AND al.module = ?";
            $params[] = $filters['module'];
            $types .= 's';
        }
        if (!empty($filters['date_from'])) {
            $where .= " AND al.created_at >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        if (!empty($filters['date_to'])) {
            $where .= " AND al.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
            $types .= 's';
        }

        return $this->query(
            "SELECT al.*, u.username, u.full_name
             FROM activity_log al
             JOIN users u ON al.user_id = u.id
             WHERE $where ORDER BY al.created_at DESC LIMIT $limit",
            $params, $types
        );
    }

    public function getModules()
    {
        return $this->query(
            "SELECT DISTINCT module FROM activity_log ORDER BY module"
        );
    }
}
