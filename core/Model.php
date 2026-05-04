<?php
class Model
{
    protected $conn;
    protected $table;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    protected function emptyResult()
    {
        return $this->conn->query("SELECT 1 WHERE 0");
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        if (!$stmt) return null;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findAll($orderBy = 'id DESC')
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY $orderBy";
        $result = $this->conn->query($sql);
        if ($result === false) {
            error_log("SQL Error in findAll(): " . $this->conn->error . " | SQL: $sql");
            return $this->emptyResult();
        }
        return $result;
    }

    public function count($conditions = '1=1', $params = [], $types = '')
    {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} WHERE $conditions";
        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("SQL Error in count(): " . $this->conn->error . " | SQL: $sql");
                return 0;
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['cnt'];
        }
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc()['cnt'] : 0;
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    protected function query($sql, $params = [], $types = '')
    {
        if (empty($params)) {
            $result = $this->conn->query($sql);
            if ($result === false) {
                error_log("SQL Error in query(): " . $this->conn->error . " | SQL: $sql");
                return $this->emptyResult();
            }
            return $result;
        }
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("SQL Error in query(): " . $this->conn->error . " | SQL: $sql");
            return $this->emptyResult();
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ?: $this->emptyResult();
    }

    protected function queryOne($sql, $params = [], $types = '')
    {
        $result = $this->query($sql, $params, $types);
        return $result->fetch_assoc();
    }

    protected function execute($sql, $params = [], $types = '')
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("SQL Error in execute(): " . $this->conn->error . " | SQL: $sql");
            return false;
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }

    protected function insert($sql, $params = [], $types = '')
    {
        $this->execute($sql, $params, $types);
        return $this->conn->insert_id;
    }

    public function exists($conditions, $params = [], $types = '')
    {
        return $this->count($conditions, $params, $types) > 0;
    }
}
