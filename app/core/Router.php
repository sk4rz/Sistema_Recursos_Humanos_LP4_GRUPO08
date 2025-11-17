<?php
/**
 * Router - Sistema de Enrutamiento
 * 
 * Esta clase maneja todas las rutas del sistema. Se encarga de:
 * - Registrar rutas (GET, POST, PUT, DELETE)
 * - Comparar la URL solicitada con las rutas registradas
 * - Ejecutar el controlador y método correspondiente
 * - Mostrar página 404 si no encuentra la ruta
 * 
 * El formato de las rutas es: 'Controlador@metodo'
 * Ejemplo: 'EmpleadoController@index'
 */
class Router {
    /**
     * Array que almacena todas las rutas registradas
     * Cada ruta tiene: method (GET, POST, etc.), path (ruta URL) y handler (controlador@método)
     */
    private $routes = [];
    
    /**
     * Array para middlewares (no usado actualmente, pero disponible para futuras mejoras)
     */
    private $middlewares = [];

    /**
     * Registrar una ruta GET
     * Ejemplo: $router->get('/dashboard', 'TableroController@index');
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Registrar una ruta POST (para formularios)
     * Ejemplo: $router->post('/employees/create', 'EmpleadoController@create');
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Registrar una ruta PUT
     * Usada para actualizaciones (no muy común en este sistema)
     */
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Registrar una ruta DELETE
     * Usada para eliminar recursos
     */
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Agregar una ruta al array de rutas
     * 
     * @param string $method Método HTTP (GET, POST, PUT, DELETE)
     * @param string $path Ruta URL
     * @param string|callable $handler Controlador@método o función anónima
     */
    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Procesar la petición actual y ejecutar la ruta correspondiente
     * 
     * Este método se llama automáticamente desde index.php
     * Busca la ruta que coincida con la URL solicitada y ejecuta el controlador correspondiente
     */
    public function dispatch() {
        // Obtener el método HTTP de la petición (GET, POST, etc.)
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Obtener la URI completa y extraer solo la ruta (sin parámetros GET)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Obtener el directorio base del script (ej: /RRHH/public)
        // Esto es necesario porque el proyecto puede estar en un subdirectorio
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = rtrim($scriptDir, '/');
        
        // Remover el directorio base de la URI para obtener solo la ruta relativa
        // Ejemplo: /RRHH/public/dashboard -> /dashboard
        if ($scriptDir && strpos($uri, $scriptDir) === 0) {
            $path = substr($uri, strlen($scriptDir));
        } else {
            $path = $uri;
        }
        
        // Si la ruta está vacía, usar '/' como ruta por defecto
        $path = $path ?: '/';

        // Buscar una ruta que coincida con la petición actual
        foreach ($this->routes as $route) {
            // Verificar que el método HTTP coincida y que la ruta coincida
            if ($route['method'] === $method && $this->matchRoute($route['path'], $path)) {
                $handler = $route['handler'];
                
                // Si el handler es un string (formato: 'Controlador@método')
                if (is_string($handler)) {
                    // Separar el controlador del método
                    list($controller, $method) = explode('@', $handler);
                    
                    // Construir la ruta del archivo del controlador
                    $controllerFile = CONTROLLERS_PATH . '/' . $controller . '.php';
                    
                    // Verificar que el archivo del controlador exista
                    if (file_exists($controllerFile)) {
                        // Cargar el archivo del controlador
                        require_once $controllerFile;
                        
                        // Crear una instancia del controlador
                        $controllerInstance = new $controller();
                        
                        // Ejecutar el método correspondiente
                        $controllerInstance->$method();
                        
                        // Salir después de ejecutar (importante)
                        return;
                    }
                } 
                // Si el handler es una función anónima (callable)
                elseif (is_callable($handler)) {
                    // Ejecutar la función directamente
                    call_user_func($handler);
                    return;
                }
            }
        }

        // Si no se encontró ninguna ruta que coincida, mostrar error 404
        http_response_code(404);
        require_once VIEWS_PATH . '/errors/404.php';
    }

    /**
     * Comparar una ruta registrada con la ruta solicitada
     */
    private function matchRoute($route, $path) {
        // Convertir parámetros de ruta (ej: {id}) a expresión regular
        // Ejemplo: '/employees/{id}' -> '/employees/([^/]+)'
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        
        // Agregar delimitadores de inicio y fin a la expresión regular
        $routePattern = '#^' . $routePattern . '$#';
        
        // Comparar la ruta solicitada con el patrón
        return preg_match($routePattern, $path);
    }
}

