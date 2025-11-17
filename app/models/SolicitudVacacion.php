<?php
/**
 * Modelo SolicitudVacacion
 */
class SolicitudVacacion {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "
            SELECT 
                lr.*,
                e.employee_id,
                u.first_name,
                u.last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name
            FROM solicitudes_vacaciones lr
            INNER JOIN empleados e ON lr.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            LEFT JOIN usuarios approver ON lr.approved_by = approver.id
            WHERE 1=1
        ";

        $params = [];

        if (isset($filters['employee_id'])) {
            $sql .= " AND lr.employee_id = ?";
            $params[] = $filters['employee_id'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND lr.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY lr.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                lr.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM solicitudes_vacaciones lr
            INNER JOIN empleados e ON lr.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE lr.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO solicitudes_vacaciones (
                    employee_id, leave_type, start_date, end_date, reason, status
                ) VALUES (?, ?, ?, ?, ?, 'pendiente')
            ");
            $stmt->execute([
                $data['employee_id'],
                $data['leave_type'],
                $data['start_date'],
                $data['end_date'],
                $data['reason'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error creating leave request: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($id, $status, $approvedBy) {
        $stmt = $this->db->prepare("
            UPDATE solicitudes_vacaciones 
            SET status = ?, approved_by = ?, approval_date = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $approvedBy, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM solicitudes_vacaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPendingCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM solicitudes_vacaciones WHERE status = 'pendiente'");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}

