<?php
/**
 * Controlador de Usuarios
 */
class UsuarioController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole(['administrador']);
        $this->userModel = new Usuario();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? 'all';
        
        $users = $this->userModel->getAll();
        $employeeModel = new Empleado();
        $employees = $employeeModel->getAll();
        
        // Filtrar por búsqueda
        if (!empty($search)) {
            $users = array_filter($users, function($user) use ($search) {
                return stripos($user['first_name'] . ' ' . $user['last_name'], $search) !== false ||
                       stripos($user['email'], $search) !== false;
            });
        }
        
        // Filtrar por rol
        if ($role !== 'all') {
            $users = array_filter($users, function($user) use ($role) {
                return $user['role'] === $role;
            });
        }
        
        $users = array_values($users);
        
        $this->view('users/index', [
            'users' => $users,
            'employees' => $employees,
            'search' => $search,
            'role' => $role
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $data = [
                'email' => $this->sanitize($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'first_name' => $this->sanitize($_POST['first_name'] ?? ''),
                'last_name' => $this->sanitize($_POST['last_name'] ?? ''),
                'role' => $_POST['role'] ?? 'empleado'
            ];

            // Validaciones
            if (empty($data['email']) || empty($data['password']) || 
                empty($data['first_name']) || empty($data['last_name'])) {
                $this->json(['error' => 'Todos los campos son requeridos'], 400);
                return;
            }

            if (!$this->validateEmail($data['email'])) {
                $this->json(['error' => 'Email inválido'], 400);
                return;
            }

            if (strlen($data['password']) < 6) {
                $this->json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
                return;
            }

            // Verificar si el email ya existe
            if ($this->userModel->findByEmail($data['email'])) {
                $this->json(['error' => 'El email ya está registrado'], 400);
                return;
            }

            if ($this->userModel->create($data)) {
                Session::flash('success', 'Usuario creado exitosamente');
                $this->json(['success' => true]);
            } else {
                $this->json(['error' => 'Error al crear usuario'], 500);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                $this->json(['error' => 'ID inválido'], 400);
                return;
            }

            $data = [];
            
            if (isset($_POST['email'])) {
                $data['email'] = $this->sanitize($_POST['email']);
            }
            if (isset($_POST['first_name'])) {
                $data['first_name'] = $this->sanitize($_POST['first_name']);
            }
            if (isset($_POST['last_name'])) {
                $data['last_name'] = $this->sanitize($_POST['last_name']);
            }
            if (isset($_POST['role'])) {
                $data['role'] = $_POST['role'];
            }
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $this->json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
                    return;
                }
                $data['password'] = $_POST['password'];
            }

            if (empty($data)) {
                $this->json(['error' => 'No hay datos para actualizar'], 400);
                return;
            }

            if ($this->userModel->update($id, $data)) {
                Session::flash('success', 'Usuario actualizado exitosamente');
                $this->json(['success' => true]);
            } else {
                $this->json(['error' => 'Error al actualizar usuario'], 500);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                $this->json(['error' => 'ID inválido'], 400);
                return;
            }

            // No permitir eliminar el propio usuario
            if ($id == $_SESSION['user_id']) {
                $this->json(['error' => 'No puedes eliminar tu propio usuario'], 400);
                return;
            }

            if ($this->userModel->delete($id)) {
                Session::flash('success', 'Usuario eliminado exitosamente');
                $this->json(['success' => true]);
            } else {
                $this->json(['error' => 'Error al eliminar usuario'], 500);
            }
        }
    }

    public function associateEmployee() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
                return;
            }

            $userId = intval($_POST['user_id'] ?? 0);
            $employeeId = !empty($_POST['employee_id']) ? intval($_POST['employee_id']) : null;
            
            if (!$userId) {
                $this->json(['error' => 'ID de usuario inválido'], 400);
                return;
            }

            $db = Database::getInstance()->getConnection();
            
            // Si se proporciona un employee_id, asociar
            if ($employeeId) {
                // Verificar que el empleado no esté ya asociado a otro usuario
                $checkStmt = $db->prepare("SELECT user_id FROM empleados WHERE id = ? AND is_active = 1");
                $checkStmt->execute([$employeeId]);
                $existing = $checkStmt->fetch();
                
                if ($existing && $existing['user_id'] && $existing['user_id'] != $userId) {
                    $this->json(['error' => 'Este empleado ya está asociado a otro usuario'], 400);
                    return;
                }
                
                // Actualizar o insertar la asociación
                $stmt = $db->prepare("UPDATE empleados SET user_id = ? WHERE id = ? AND is_active = 1");
                if ($stmt->execute([$userId, $employeeId])) {
                    Session::flash('success', 'Empleado asociado exitosamente');
                    $this->json(['success' => true]);
                } else {
                    $this->json(['error' => 'Error al asociar empleado'], 500);
                }
            } else {
                // Desasociar: poner user_id en NULL
                $stmt = $db->prepare("UPDATE empleados SET user_id = NULL WHERE user_id = ? AND is_active = 1");
                if ($stmt->execute([$userId])) {
                    Session::flash('success', 'Empleado desasociado exitosamente');
                    $this->json(['success' => true]);
                } else {
                    $this->json(['error' => 'Error al desasociar empleado'], 500);
                }
            }
        }
    }

    public function getEmployee() {
        $userId = intval($_GET['user_id'] ?? 0);
        if (!$userId) {
            $this->json(['error' => 'ID inválido'], 400);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, employee_id FROM empleados WHERE user_id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$userId]);
        $employee = $stmt->fetch();

        if ($employee) {
            $this->json(['employee_id' => $employee['id']]);
        } else {
            $this->json(['employee_id' => null]);
        }
    }
}

