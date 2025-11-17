<?php
/**
 * Modelo Asistencia
 */
class Asistencia {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "
            SELECT 
                a.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM asistencia a
            INNER JOIN empleados e ON a.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE 1=1
        ";

        $params = [];

        if (isset($filters['date'])) {
            $sql .= " AND a.date = ?";
            $params[] = $filters['date'];
        }

        if (isset($filters['employee_id'])) {
            $sql .= " AND a.employee_id = ?";
            $params[] = $filters['employee_id'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY a.date DESC, a.check_in_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                a.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM asistencia a
            INNER JOIN empleados e ON a.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO asistencia (employee_id, date, check_in_time, check_out_time, status, notes)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                check_in_time = VALUES(check_in_time),
                check_out_time = VALUES(check_out_time),
                status = VALUES(status),
                notes = VALUES(notes)
        ");
        return $stmt->execute([
            $data['employee_id'],
            $data['date'],
            $data['check_in_time'] ?? null,
            $data['check_out_time'] ?? null,
            $data['status'] ?? 'ausente',
            $data['notes'] ?? null
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        $allowedFields = ['check_in_time', 'check_out_time', 'status', 'notes'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE asistencia SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM asistencia WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getMonthlyStats($month, $year) {
        $stmt = $this->db->prepare("
            SELECT 
                status,
                COUNT(*) as count
            FROM asistencia
            WHERE MONTH(date) = ? AND YEAR(date) = ?
            GROUP BY status
        ");
        $stmt->execute([$month, $year]);
        return $stmt->fetchAll();
    }

    public function getAttendanceRate($month, $year) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT employee_id) as total_employees,
                COUNT(DISTINCT CASE WHEN status IN ('presente', 'retrasado', 'remoto') THEN employee_id END) as present_employees
            FROM asistencia
            WHERE MONTH(date) = ? AND YEAR(date) = ?
        ");
        $stmt->execute([$month, $year]);
        $result = $stmt->fetch();
        
        if ($result['total_employees'] > 0) {
            return round(($result['present_employees'] / $result['total_employees']) * 100, 2);
        }
        return 0;
    }
}

