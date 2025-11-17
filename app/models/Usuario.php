<?php
/**
 * Modelo Usuario
 */
class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (email, password_hash, first_name, last_name, role, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        return $stmt->execute([
            $data['email'],
            $passwordHash,
            $data['first_name'],
            $data['last_name'],
            $data['role'] ?? 'empleado'
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
        }
        if (isset($data['first_name'])) {
            $fields[] = "first_name = ?";
            $values[] = $data['first_name'];
        }
        if (isset($data['last_name'])) {
            $fields[] = "last_name = ?";
            $values[] = $data['last_name'];
        }
        if (isset($data['role'])) {
            $fields[] = "role = ?";
            $values[] = $data['role'];
        }
        if (isset($data['password'])) {
            $fields[] = "password_hash = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM usuarios WHERE is_active = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE usuarios SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

