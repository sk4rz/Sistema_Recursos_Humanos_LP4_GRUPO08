<?php
/**
 * Clase Base Controller
 * Todos los controladores heredan de esta clase
 */
abstract class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function view($view, $data = []) {
        $data['controller'] = $this;
        extract($data);
        
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("Error: No se encontró la vista '{$view}'. Verifica que el archivo exista en app/views/");
        }

        require_once $viewFile;
    }

    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * Enviar una respuesta JSON (útil para AJAX)
     * Ejemplo: $this->json(['success' => true, 'message' => 'Operación exitosa'], 200);
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
    }

    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!in_array($_SESSION['user_role'], $roles)) {
            $this->redirect('/dashboard');
        }
    }

    protected function validateCSRF($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $_SESSION[CSRF_TOKEN_NAME] !== $token) {
            return false;
        }
        return true;
    }

    protected function generateCSRF() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

