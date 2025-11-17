<?php
/**
 * Modelo Planilla
 */
class Planilla {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "
            SELECT 
                p.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM planillas p
            INNER JOIN empleados e ON p.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE 1=1
        ";

        $params = [];

        if (isset($filters['employee_id'])) {
            $sql .= " AND p.employee_id = ?";
            $params[] = $filters['employee_id'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['period_start']) && isset($filters['period_end'])) {
            $sql .= " AND p.payment_period_start >= ? AND p.payment_period_end <= ?";
            $params[] = $filters['period_start'];
            $params[] = $filters['period_end'];
        }

        $sql .= " ORDER BY p.payment_period_end DESC, p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                e.employee_id,
                u.first_name,
                u.last_name
            FROM planillas p
            INNER JOIN empleados e ON p.employee_id = e.id
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $tax = ($data['base_salary'] + ($data['allowances'] ?? 0)) * 0.16; // 16% de impuestos
        $netSalary = ($data['base_salary'] + ($data['allowances'] ?? 0)) - ($data['deductions'] ?? 0) - $tax;

        $stmt = $this->db->prepare("
            INSERT INTO planillas (
                employee_id, payment_period_start, payment_period_end,
                base_salary, allowances, deductions, tax, net_salary,
                payment_date, payment_method, status, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['employee_id'],
            $data['payment_period_start'],
            $data['payment_period_end'],
            $data['base_salary'],
            $data['allowances'] ?? 0,
            $data['deductions'] ?? 0,
            $tax,
            $netSalary,
            $data['payment_date'] ?? null,
            $data['payment_method'] ?? 'transferencia_bancaria',
            $data['status'] ?? 'aprobado',
            $data['notes'] ?? null
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        $allowedFields = ['status', 'payment_date', 'payment_method', 'notes'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        // Recalcular si cambian valores financieros
        if (isset($data['base_salary']) || isset($data['allowances']) || isset($data['deductions'])) {
            $current = $this->findById($id);
            $baseSalary = $data['base_salary'] ?? $current['base_salary'];
            $allowances = $data['allowances'] ?? $current['allowances'];
            $deductions = $data['deductions'] ?? $current['deductions'];
            
            $tax = ($baseSalary + $allowances) * 0.16;
            $netSalary = ($baseSalary + $allowances) - $deductions - $tax;
            
            $fields[] = "base_salary = ?";
            $values[] = $baseSalary;
            $fields[] = "allowances = ?";
            $values[] = $allowances;
            $fields[] = "deductions = ?";
            $values[] = $deductions;
            $fields[] = "tax = ?";
            $values[] = $tax;
            $fields[] = "net_salary = ?";
            $values[] = $netSalary;
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE planillas SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM planillas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalByStatus($status) {
        $stmt = $this->db->prepare("
            SELECT SUM(net_salary) as total
            FROM planillas
            WHERE status = ?
        ");
        $stmt->execute([$status]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getMonthlyTotal($month, $year) {
        $stmt = $this->db->prepare("
            SELECT SUM(net_salary) as total
            FROM planillas
            WHERE MONTH(payment_period_end) = ? AND YEAR(payment_period_end) = ?
            AND status = 'pagado'
        ");
        $stmt->execute([$month, $year]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

