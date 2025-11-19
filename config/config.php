<?php
/**
 * Configuración del Sistema
 */

// Errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Zona horaria
date_default_timezone_set('America/Lima');

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('MODELS_PATH', APP_PATH . '/models');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('CORE_PATH', APP_PATH . '/core');

// URLs del sistema (se detectan automáticamente)
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $scriptDir = dirname($scriptName);
    $scriptDir = rtrim($scriptDir, '/\\');
    
    if ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '') {
        $baseUrl = $protocol . '://' . $host;
    } else {
        $baseUrl = $protocol . '://' . $host . $scriptDir;
    }
    
    define('BASE_URL', $baseUrl);
}

define('ASSETS_URL', BASE_URL . '/assets');

// Base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'rrhh_system');
define('DB_USER', 'root');
define('DB_PASS', 'unknown');
define('DB_CHARSET', 'utf8mb4');

// Seguridad
define('SESSION_NAME', 'HR_SESSION');
define('SESSION_LIFETIME', 86400); // 24 horas
define('CSRF_TOKEN_NAME', 'csrf_token');

// Información de la aplicación
define('APP_NAME', 'Sistema de Recursos Humanos');
define('APP_VERSION', '1.0.0');

// Directorios temporales
define('REPORTS_PATH', ROOT_PATH . '/reports');
define('TEMP_PATH', ROOT_PATH . '/temp');

