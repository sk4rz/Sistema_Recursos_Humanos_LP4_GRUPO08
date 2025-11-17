<?php
/**
 * Clase Base Controller
 * 
 * Esta es la clase base de la que heredan todos los controladores del sistema.
 * Proporciona métodos útiles para manejar vistas, redirecciones, validaciones
 * y respuestas JSON.
 * 
 * Todos los controladores deben extender esta clase para tener acceso a estas
 * funcionalidades comunes.
 */
abstract class Controller {
    /**
     * Conexión a la base de datos (PDO)
     * Disponible para todos los controladores que hereden de esta clase
     */
    protected $db;

    /**
     * Constructor de la clase Controller
     * Inicializa la conexión a la base de datos automáticamente
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Cargar y mostrar una vista
     * Ejemplo: $this->view('dashboard/index', ['usuarios' => $usuarios]);
     */
    protected function view($view, $data = []) {
        // Hacer disponible el controlador en la vista por si se necesita
        $data['controller'] = $this;
        
        // Extraer los datos del array como variables individuales
        // Esto permite usar $usuarios en lugar de $data['usuarios'] en la vista
        extract($data);
        
        // Construir la ruta completa del archivo de vista
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        
        // Verificar que la vista exista antes de intentar cargarla
        if (!file_exists($viewFile)) {
            die("Error: No se encontró la vista '{$view}'. Verifica que el archivo exista en app/views/");
        }

        // Cargar la vista
        require_once $viewFile;
    }

    /**
     * Redirigir a otra página
     * Ejemplo: $this->redirect('/dashboard');
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit; // Importante: detener la ejecución después de redirigir
    }

    /**
     * Enviar una respuesta JSON (útil para AJAX)
     * Ejemplo: $this->json(['success' => true, 'message' => 'Operación exitosa'], 200);
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit; // Detener la ejecución después de enviar la respuesta
    }

    /**
     * Verificar que el usuario esté autenticado
     * Si no está autenticado, lo redirige a la página de login
     * 
     * Úsalo al inicio de métodos que requieren que el usuario haya iniciado sesión
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Usuario no autenticado, redirigir al login
            $this->redirect('/auth/login');
        }
    }

    /**
     * Verificar que el usuario tenga uno de los roles permitidos
     * Ejemplo: $this->requireRole('administrador') o $this->requireRole(['administrador', 'gerente'])
     */
    protected function requireRole($roles) {
        // Primero verificar que esté autenticado
        $this->requireAuth();
        
        // Convertir a array si es un string
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // Verificar si el rol del usuario está en la lista de roles permitidos
        if (!in_array($_SESSION['user_role'], $roles)) {
            // Usuario no tiene permisos, redirigir al dashboard
            $this->redirect('/dashboard');
        }
    }

    /**
     * Validar token CSRF (protege formularios contra ataques)
     * Úsalo en métodos POST
     */
    protected function validateCSRF($token) {
        // Verificar que exista el token en la sesión y que coincida
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $_SESSION[CSRF_TOKEN_NAME] !== $token) {
            return false; // Token inválido o no existe
        }
        return true; // Token válido
    }

    /**
     * Generar un token CSRF único
     * Úsalo en formularios: <input type="hidden" name="csrf_token" value="<?= $this->generateCSRF() ?>">
     */
    protected function generateCSRF() {
        // Generar token solo si no existe uno ya
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            // Generar un token aleatorio seguro de 64 caracteres
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Sanitizar datos de entrada (previene ataques XSS)
     * Ejemplo: $nombre = $this->sanitize($_POST['nombre']);
     */
    protected function sanitize($data) {
        // Si es un array, sanitizar cada elemento recursivamente
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        // Limpiar espacios en blanco, eliminar etiquetas HTML y escapar caracteres especiales
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar formato de email
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

