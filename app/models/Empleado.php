<?php
/**
 * Modelo Empleado
 */
class Empleado {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT 
                e.*,
                u.email,
                u.first_name,
                u.last_name,
                u.role,
                d.name as department_name
            FROM empleados e
            INNER JOIN usuarios u ON e.user_id = u.id
            INNER JOIN departamentos d ON e.department_id = d.id
            WHERE e.is_active = 1
            ORDER BY e.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                e.*,
                u.email,
                u.first_name,
                u.last_name,
                u.role,
                d.name as department_name
            FROM empleados e
            INNER JOIN usuarios u ON e.user_id = u.id
            INNER JOIN departamentos d ON e.department_id = d.id
            WHERE e.id = ? AND e.is_active = 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmployeeId($employeeId) {
        $stmt = $this->db->prepare("SELECT * FROM empleados WHERE employee_id = ? AND is_active = 1");
        $stmt->execute([$employeeId]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            // Crear usuario primero
            $usuarioModel = new Usuario();
            $userId = $this->db->lastInsertId();
            
            if (!isset($data['user_id'])) {
                $userData = [
                    'email' => $data['email'],
                    'password' => $data['password'] ?? 'password123',
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'role' => 'empleado'
                ];
                $usuarioModel->create($userData);
                $userId = $this->db->lastInsertId();
            } else {
                $userId = $data['user_id'];
            }

            // Crear empleado
            $stmt = $this->db->prepare("
                INSERT INTO empleados (
                    user_id, employee_id, department_id, position, hire_date,
                    salary, contract_type, manager_id, phone, address, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");

            $stmt->execute([
                $userId,
                $data['employee_id'],
                $data['department_id'],
                $data['position'],
                $data['hire_date'],
                $data['salary'],
                $data['contract_type'] ?? 'tiempo_completo',
                $data['manager_id'] ?? null,
                $data['phone'] ?? null,
                $data['address'] ?? null
            ]);

            // Crear saldo de vacaciones
            $leaveBalanceStmt = $this->db->prepare("
                INSERT INTO saldos_vacaciones (employee_id, annual_leave, sick_leave, personal_leave, year)
                VALUES (?, 20, 10, 3, YEAR(CURDATE()))
            ");
            $leaveBalanceStmt->execute([$this->db->lastInsertId()]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error creating employee: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        $allowedFields = ['department_id', 'position', 'salary', 'contract_type', 'manager_id', 'phone', 'address'];
        
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
        $sql = "UPDATE empleados SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE empleados SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByDepartment($departmentId) {
        $stmt = $this->db->prepare("
            SELECT e.*, u.first_name, u.last_name, u.email
            FROM empleados e
            INNER JOIN usuarios u ON e.user_id = u.id
            WHERE e.department_id = ? AND e.is_active = 1
        ");
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll();
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM empleados WHERE is_active = 1");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

