<?php
/**
 * Archivo de Configuración del Sistema
 * 
 * Este archivo contiene todas las configuraciones importantes del sistema.
 * Si usaste el instalador automático (install.php), este archivo fue generado automáticamente.
 * 
 * IMPORTANTE: Si cambias algo aquí, asegúrate de saber lo que estás haciendo.
 * Algunos cambios pueden romper el funcionamiento del sistema.
 */

// ============================================
// CONFIGURACIÓN DE ERRORES
// ============================================
// En producción, deberías tener display_errors en 0 para no mostrar errores al usuario
error_reporting(E_ALL);           // Reportar todos los errores
ini_set('display_errors', 0);     // No mostrar errores en pantalla (0 = ocultar, 1 = mostrar)
ini_set('log_errors', 1);         // Registrar errores en el log de PHP

// ============================================
// ZONA HORARIA
// ============================================
// Ajusta según tu ubicación. Lista completa: https://www.php.net/manual/es/timezones.php
// Ejemplos:
// - 'America/Lima' (Perú)
// - 'America/Mexico_City' (México)
// - 'America/Bogota' (Colombia)
// - 'America/Buenos_Aires' (Argentina)
date_default_timezone_set('America/Lima');

// ============================================
// RUTAS DEL SISTEMA
// ============================================
// Estas rutas se calculan automáticamente según la ubicación del proyecto
// Generalmente NO necesitas cambiarlas, a menos que muevas carpetas
define('ROOT_PATH', dirname(__DIR__));                    // Carpeta raíz del proyecto
define('APP_PATH', ROOT_PATH . '/app');                   // Carpeta de la aplicación
define('PUBLIC_PATH', ROOT_PATH . '/public');             // Carpeta pública (DocumentRoot)
define('VIEWS_PATH', APP_PATH . '/views');                // Carpeta de vistas
define('MODELS_PATH', APP_PATH . '/models');              // Carpeta de modelos
define('CONTROLLERS_PATH', APP_PATH . '/controllers');    // Carpeta de controladores
define('CORE_PATH', APP_PATH . '/core');                  // Carpeta de clases base

// ============================================
// URLs DEL SISTEMA
// ============================================
// BASE_URL: Se calcula automáticamente según la URL actual
// Esto permite que el sistema funcione en cualquier máquina sin configuración manual
if (!defined('BASE_URL')) {
    // Detectar protocolo (http o https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    
    // Detectar host
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Obtener el directorio base del script
    // $_SERVER['SCRIPT_NAME'] contiene la ruta del script actual (ej: /RRHH/public/index.php)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
    
    // Obtener el directorio del script (ej: /RRHH/public o /public)
    $scriptDir = dirname($scriptName);
    
    // Normalizar: remover barras finales y asegurar que empiece con /
    $scriptDir = rtrim($scriptDir, '/\\');
    if (empty($scriptDir) || $scriptDir === '.') {
        $scriptDir = '/public';
    }
    if (substr($scriptDir, 0, 1) !== '/') {
        $scriptDir = '/' . $scriptDir;
    }
    
    // Si estamos en el directorio raíz o no detectamos /public, asumir /public
    // Esto es útil cuando se accede directamente a index.php sin la ruta completa
    if (strpos($scriptDir, '/public') === false && strpos($scriptName, 'index.php') !== false) {
        // Intentar detectar desde REQUEST_URI si está disponible
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (!empty($requestUri)) {
            // Extraer la parte del path antes de los parámetros
            $uriPath = parse_url($requestUri, PHP_URL_PATH);
            if ($uriPath && strpos($uriPath, '/public') !== false) {
                // Encontrar la posición de /public
                $publicPos = strpos($uriPath, '/public');
                $scriptDir = substr($uriPath, 0, $publicPos + 6); // +6 para incluir '/public'
            } else {
                $scriptDir = '/public';
            }
        } else {
            $scriptDir = '/public';
        }
    }
    
    // Construir BASE_URL automáticamente
    // Ejemplos:
    // - http://localhost/public
    // - http://localhost:8000/public
    // - http://localhost/RRHH/public
    $baseUrl = $protocol . '://' . $host . $scriptDir;
    
    define('BASE_URL', $baseUrl);
}

// URL para los archivos estáticos (CSS, JS, imágenes)
define('ASSETS_URL', BASE_URL . '/assets');

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
// IMPORTANTE: Cambia estos valores según tu configuración de MySQL/MariaDB
// Si usaste el instalador automático, estos valores ya están configurados

define('DB_HOST', 'localhost');        // Host de MySQL (generalmente 'localhost')
define('DB_NAME', 'rrhh_system');      // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario de MySQL
define('DB_PASS', 'unknown');          // Contraseña de MySQL (¡cambia esto!)
define('DB_CHARSET', 'utf8mb4');       // Charset para soportar tildes y caracteres especiales

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================
// SESSION_NAME: Nombre de la cookie de sesión
define('SESSION_NAME', 'HR_SESSION');

// SESSION_LIFETIME: Tiempo de vida de la sesión en segundos
// 86400 = 24 horas (1 día)
// 3600 = 1 hora
// 1800 = 30 minutos
define('SESSION_LIFETIME', 86400);

// CSRF_TOKEN_NAME: Nombre del token CSRF en la sesión
// No cambiar a menos que sepas lo que haces
define('CSRF_TOKEN_NAME', 'csrf_token');

// ============================================
// INFORMACIÓN DE LA APLICACIÓN
// ============================================
define('APP_NAME', 'Sistema de Recursos Humanos');
define('APP_VERSION', '1.0.0');

// ============================================
// DIRECTORIOS PARA ARCHIVOS TEMPORALES
// ============================================
// Estos directorios se crean automáticamente si no existen
// Asegúrate de que tengan permisos de escritura
define('REPORTS_PATH', ROOT_PATH . '/reports');  // Carpeta para reportes PDF generados
define('TEMP_PATH', ROOT_PATH . '/temp');        // Carpeta para archivos temporales

