<?php
/**
 * Script de Instalación Automática
 * Sistema de Recursos Humanos
 * 
 * Este script facilita la instalación del sistema de forma automática.
 * Solo necesitas ejecutarlo una vez: php install.php
 */

// Configuración de errores para ver mensajes claros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "  INSTALADOR - Sistema de Recursos Humanos\n";
echo "========================================\n\n";

// Verificar PHP
echo "[OK] Verificando version de PHP...\n";
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die("[ERROR] Se requiere PHP 8.0 o superior. Version actual: " . PHP_VERSION . "\n");
}
echo "  [OK] PHP " . PHP_VERSION . " detectado\n\n";

// Verificar extensiones
echo "[OK] Verificando extensiones PHP...\n";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json'];
$missing = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
        echo "  [ERROR] $ext NO esta instalada\n";
    } else {
        echo "  [OK] $ext esta instalada\n";
    }
}

if (!empty($missing)) {
    echo "\n[ERROR] Faltan extensiones requeridas: " . implode(', ', $missing) . "\n";
    echo "Por favor, habilitalas en tu php.ini y reinicia el servidor.\n";
    exit(1);
}
echo "\n";

// Solicitar información de base de datos
echo "--- CONFIGURACIÓN DE BASE DE DATOS ---\n";
echo "Por favor, ingresa la información de tu base de datos MySQL/MariaDB:\n\n";

$db_host = readline("Host (presiona Enter para 'localhost'): ");
$db_host = empty($db_host) ? 'localhost' : $db_host;

$db_name = readline("Nombre de la base de datos (presiona Enter para 'rrhh_system'): ");
$db_name = empty($db_name) ? 'rrhh_system' : $db_name;

$db_user = readline("Usuario (presiona Enter para 'root'): ");
$db_user = empty($db_user) ? 'root' : $db_user;

$db_pass = readline("Contraseña: ");

echo "\n[OK] Intentando conectar a la base de datos...\n";

try {
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "  [OK] Conexion exitosa\n\n";
} catch (PDOException $e) {
    die("[ERROR] No se pudo conectar a MySQL: " . $e->getMessage() . "\n");
}

// Crear base de datos si no existe
echo "[OK] Verificando base de datos '$db_name'...\n";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "  [OK] Base de datos '$db_name' lista\n\n";
} catch (PDOException $e) {
    die("[ERROR] Error al crear la base de datos: " . $e->getMessage() . "\n");
}

// Seleccionar base de datos
$pdo->exec("USE `$db_name`");

// Leer y ejecutar schema
echo "[OK] Importando esquema de base de datos...\n";
$schema_file = __DIR__ . '/scripts/01-schema.sql';

if (!file_exists($schema_file)) {
    die("[ERROR] No se encontro el archivo $schema_file\n");
}

$schema_sql = file_get_contents($schema_file);
// Remover comandos SET NAMES del SQL (ya los manejamos en PHP)
$schema_sql = preg_replace('/^SET NAMES.*?;$/m', '', $schema_sql);
$schema_sql = preg_replace('/^SET CHARACTER SET.*?;$/m', '', $schema_sql);
$schema_sql = preg_replace('/^SET character_set_connection.*?;$/m', '', $schema_sql);

// Ejecutar cada statement por separado
$statements = array_filter(array_map('trim', explode(';', $schema_sql)));

foreach ($statements as $statement) {
    if (!empty($statement) && !preg_match('/^--/', $statement)) {
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            // Ignorar errores de "table already exists"
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "  [ADVERTENCIA] " . $e->getMessage() . "\n";
            }
        }
    }
}
echo "  [OK] Esquema importado correctamente\n\n";

// Preguntar si importar datos de prueba
echo "--- DATOS DE PRUEBA ---\n";
$import_seed = strtolower(readline("¿Deseas importar datos de prueba? (s/n, presiona Enter para 's'): "));
$import_seed = empty($import_seed) ? 's' : $import_seed;

if ($import_seed === 's' || $import_seed === 'si' || $import_seed === 'y' || $import_seed === 'yes') {
    echo "\n[OK] Importando datos de prueba...\n";
    $seed_file = __DIR__ . '/scripts/02-seed-data.sql';
    
    if (file_exists($seed_file)) {
        $seed_sql = file_get_contents($seed_file);
        // Remover comandos SET NAMES
        $seed_sql = preg_replace('/^SET NAMES.*?;$/m', '', $seed_sql);
        $seed_sql = preg_replace('/^SET CHARACTER SET.*?;$/m', '', $seed_sql);
        $seed_sql = preg_replace('/^SET character_set_connection.*?;$/m', '', $seed_sql);
        
        $seed_statements = array_filter(array_map('trim', explode(';', $seed_sql)));
        
        foreach ($seed_statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignorar errores de duplicados
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "  [ADVERTENCIA] " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        echo "  [OK] Datos de prueba importados\n\n";
    }
}

// Crear archivo de configuración
echo "[OK] Generando archivo de configuracion...\n";
$config_template = <<<PHP
<?php
/**
 * Configuración del Sistema de Recursos Humanos
 * 
 * Este archivo fue generado automáticamente por el instalador.
 * Puedes editarlo manualmente si necesitas cambiar alguna configuración.
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Zona horaria (ajusta según tu ubicación)
date_default_timezone_set('America/Lima');

// Rutas base del proyecto
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('MODELS_PATH', APP_PATH . '/models');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('CORE_PATH', APP_PATH . '/core');

// URLs del sistema
// BASE_URL se detecta automáticamente, no necesita configuración manual
if (!defined('BASE_URL')) {
    \$protocol = (!empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    \$host = \$_SERVER['HTTP_HOST'] ?? 'localhost';
    \$scriptName = \$_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
    \$scriptDir = dirname(\$scriptName);
    \$scriptDir = rtrim(\$scriptDir, '/\\\\');
    if (empty(\$scriptDir) || \$scriptDir === '.') {
        \$scriptDir = '/public';
    }
    if (substr(\$scriptDir, 0, 1) !== '/') {
        \$scriptDir = '/' . \$scriptDir;
    }
    if (strpos(\$scriptDir, '/public') === false && strpos(\$scriptName, 'index.php') !== false) {
        \$requestUri = \$_SERVER['REQUEST_URI'] ?? '';
        if (!empty(\$requestUri)) {
            \$uriPath = parse_url(\$requestUri, PHP_URL_PATH);
            if (\$uriPath && strpos(\$uriPath, '/public') !== false) {
                \$publicPos = strpos(\$uriPath, '/public');
                \$scriptDir = substr(\$uriPath, 0, \$publicPos + 6);
            } else {
                \$scriptDir = '/public';
            }
        } else {
            \$scriptDir = '/public';
        }
    }
    \$baseUrl = \$protocol . '://' . \$host . \$scriptDir;
    define('BASE_URL', \$baseUrl);
}
define('ASSETS_URL', BASE_URL . '/assets');

// Configuración de base de datos
define('DB_HOST', '$db_host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_CHARSET', 'utf8mb4');

// Configuración de seguridad
define('SESSION_NAME', 'HR_SESSION');
define('SESSION_LIFETIME', 86400); // 24 horas
define('CSRF_TOKEN_NAME', 'csrf_token');

// Información de la aplicación
define('APP_NAME', 'Sistema de Recursos Humanos');
define('APP_VERSION', '1.0.0');

// Directorios para reportes y archivos temporales
define('REPORTS_PATH', ROOT_PATH . '/reports');
define('TEMP_PATH', ROOT_PATH . '/temp');
PHP;

file_put_contents(__DIR__ . '/config/config.php', $config_template);
echo "  [OK] Archivo config/config.php creado\n\n";

// Crear directorios necesarios
echo "[OK] Creando directorios necesarios...\n";
$directories = ['temp', 'reports'];
foreach ($directories as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "  [OK] Directorio '$dir' creado\n";
    } else {
        echo "  [OK] Directorio '$dir' ya existe\n";
    }
}
echo "\n";

// Verificar permisos
echo "[OK] Verificando permisos...\n";
$writable_dirs = ['temp', 'reports'];
foreach ($writable_dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_writable($path)) {
        echo "  [OK] El directorio '$dir' tiene permisos de escritura\n";
    } else {
        echo "  [ADVERTENCIA] El directorio '$dir' NO tiene permisos de escritura\n";
        echo "    Ejecuta: chmod 777 $dir\n";
    }
}
echo "\n";

// Resumen final
echo "========================================\n";
echo "  INSTALACION COMPLETADA\n";
echo "========================================\n\n";

echo "Credenciales de acceso:\n";
echo "  Email: admin@example.com\n";
echo "  Contrasena: password123\n\n";

echo "Proximos pasos:\n";
echo "  1. Inicia el servidor web:\n";
echo "     - Servidor PHP integrado: cd public && php -S localhost:8000\n";
echo "     - Apache: Accede a http://localhost/RRHH/public\n";
echo "  2. Abre tu navegador y accede a la URL indicada\n";
echo "  3. Inicia sesion con las credenciales de arriba\n";
echo "  4. Cambia las contrasenas por defecto\n";
echo "  5. Configura los datos segun tus necesidades\n\n";

echo "Listo para usar!\n";

