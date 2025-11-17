<?php
/**
 * Modelo Departamento
 */
class Departamento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT 
                d.*,
                u.first_name as manager_first_name,
                u.last_name as manager_last_name,
                (SELECT COUNT(*) FROM empleados WHERE department_id = d.id AND is_active = 1) as employee_count
            FROM departamentos d
            LEFT JOIN usuarios u ON d.manager_id = u.id
            WHERE d.is_active = 1
            ORDER BY d.name
        ");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                d.*,
                u.first_name as manager_first_name,
                u.last_name as manager_last_name
            FROM departamentos d
            LEFT JOIN usuarios u ON d.manager_id = u.id
            WHERE d.id = ? AND d.is_active = 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO departamentos (name, description, manager_id, budget, is_active)
            VALUES (?, ?, ?, ?, 1)
        ");
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['manager_id'] ?? null,
            $data['budget'] ?? null
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        $allowedFields = ['name', 'description', 'manager_id', 'budget'];
        
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
        $sql = "UPDATE departamentos SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE departamentos SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

