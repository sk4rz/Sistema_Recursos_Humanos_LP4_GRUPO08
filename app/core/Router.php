<?php
/**
 * Router - Maneja las rutas del sistema
 */
class Router {
    private $routes = [];
    private $middlewares = [];

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $uri = rtrim($uri, '/') ?: '/';
        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }
        
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $scriptDir = dirname($scriptName);
        $scriptDir = rtrim($scriptDir, '/\\');
        if ($scriptDir === '.' || $scriptDir === '') {
            $scriptDir = '/';
        }
        if ($scriptDir !== '/' && substr($scriptDir, 0, 1) !== '/') {
            $scriptDir = '/' . $scriptDir;
        }
        
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $path = substr($uri, strlen($scriptDir));
            if (substr($path, 0, 1) !== '/') {
                $path = '/' . $path;
            }
        } else {
            $path = $uri;
        }
        
        $path = $path ?: '/';
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $path)) {
                $handler = $route['handler'];
                
                if (is_string($handler)) {
                    list($controller, $method) = explode('@', $handler);
                    $controllerFile = CONTROLLERS_PATH . '/' . $controller . '.php';
                    
                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        $controllerInstance = new $controller();
                        $controllerInstance->$method();
                        return;
                    }
                } elseif (is_callable($handler)) {
                    call_user_func($handler);
                    return;
                }
            }
        }

        http_response_code(404);
        require_once VIEWS_PATH . '/errors/404.php';
    }

    private function matchRoute($route, $path) {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        return preg_match($routePattern, $path);
    }
}

