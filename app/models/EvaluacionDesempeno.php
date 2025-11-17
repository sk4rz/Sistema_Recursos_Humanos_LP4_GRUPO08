<?php
/**
 * Modelo EvaluacionDesempeno
 */
class EvaluacionDesempeno {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "
            SELECT 
                pe.*,
                e.employee_id,
                u.first_name,
                u.last_name,
                evaluator.first_name as evaluator_first_name,
                evaluator.last_name as evaluator_last_name
            FROM evaluaciones_desempeno pe
            INNER JOIN empleados e ON pe.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            INNER JOIN usuarios evaluator ON pe.evaluator_id = evaluator.id
            WHERE 1=1
        ";

        $params = [];

        if (isset($filters['employee_id'])) {
            $sql .= " AND pe.employee_id = ?";
            $params[] = $filters['employee_id'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND pe.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY pe.evaluation_period_end DESC, pe.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                pe.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM evaluaciones_desempeno pe
            INNER JOIN empleados e ON pe.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE pe.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO evaluaciones_desempeno (
                employee_id, evaluator_id, evaluation_period_start, evaluation_period_end,
                rating, comments, strengths, areas_for_improvement, goals, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['employee_id'],
            $data['evaluator_id'],
            $data['evaluation_period_start'],
            $data['evaluation_period_end'],
            $data['rating'],
            $data['comments'] ?? null,
            $data['strengths'] ?? null,
            $data['areas_for_improvement'] ?? null,
            $data['goals'] ?? null,
            $data['status'] ?? 'completado'
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        $allowedFields = ['rating', 'comments', 'strengths', 'areas_for_improvement', 'goals', 'status'];
        
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
        $sql = "UPDATE evaluaciones_desempeno SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM evaluaciones_desempeno WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAverageRating($employeeId = null) {
        $sql = "SELECT AVG(rating) as average FROM evaluaciones_desempeno WHERE status = 'completado'";
        $params = [];

        if ($employeeId) {
            $sql .= " AND employee_id = ?";
            $params[] = $employeeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return round($result['average'] ?? 0, 2);
    }

    public function getIncompleteCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM evaluaciones_desempeno WHERE status = 'borrador'");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}

