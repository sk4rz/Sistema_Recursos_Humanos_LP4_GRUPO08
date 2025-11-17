<?php
/**
 * Controlador de Autenticación
 */
class AutenticacionController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new Usuario();
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validaciones
            if (empty($email) || empty($password)) {
                Session::flash('error', 'Email y contraseña son requeridos');
                $this->view('auth/login', ['error' => 'Email y contraseña son requeridos']);
                return;
            }

            if (!$this->validateEmail($email)) {
                Session::flash('error', 'Email inválido');
                $this->view('auth/login', ['error' => 'Email inválido']);
                return;
            }

            // Buscar usuario
            $user = $this->userModel->findByEmail($email);

            if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
                Session::flash('error', 'Credenciales inválidas');
                $this->view('auth/login', ['error' => 'Credenciales inválidas']);
                return;
            }

            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

            // Registrar en auditoría
            $this->logAudit($user['id'], 'login', 'usuarios', $user['id']);

            $this->redirect('/dashboard');
        } else {
            $error = Session::flash('error');
            $this->view('auth/login', ['error' => $error]);
        }
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logAudit($_SESSION['user_id'], 'logout', 'usuarios', $_SESSION['user_id']);
        }
        Session::destroy();
        $this->redirect('/auth/login');
    }

    private function logAudit($userId, $action, $entityType, $entityId) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO registros_auditoria (user_id, action, entity_type, entity_id, ip_address)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $entityType,
                $entityId,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Error logging audit: " . $e->getMessage());
        }
    }
}

