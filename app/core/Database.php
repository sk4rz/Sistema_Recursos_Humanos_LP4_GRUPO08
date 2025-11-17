<?php
/**
 * Clase Database - Gestor de Conexión a Base de Datos
 * 
 * Esta clase implementa el patrón Singleton para asegurar que solo exista
 * una conexión a la base de datos en toda la aplicación.
 * 
 * Utiliza PDO (PHP Data Objects) para realizar consultas de forma segura
 * y prevenir inyecciones SQL.
 * 
 * Características:
 * - Una sola conexión compartida en toda la aplicación
 * - Configuración automática de UTF-8 (utf8mb4) para soportar tildes y caracteres especiales
 * - Manejo de errores robusto
 * - Consultas preparadas por defecto
 */
class Database {
    /**
     * Instancia única de la clase (patrón Singleton)
     * @var Database|null
     */
    private static $instance = null;
    
    /**
     * Conexión PDO a la base de datos
     * @var PDO
     */
    private $connection;

    /**
     * Constructor privado para prevenir instanciación directa
     * Solo se puede crear una instancia a través de getInstance()
     * 
     * Este método:
     * 1. Verifica que la extensión PDO MySQL esté disponible
     * 2. Crea la conexión usando las credenciales de config/config.php
     * 3. Configura opciones de PDO para mayor seguridad
     * 4. Establece UTF-8 (utf8mb4) para soportar tildes y caracteres especiales
     */
    private function __construct() {
        try {
            // Verificar que la extensión PDO MySQL esté instalada y habilitada
            // Si no está, mostrar un mensaje de error claro
            if (!extension_loaded('pdo_mysql')) {
                throw new Exception(
                    "ERROR: La extensión PDO MySQL no está habilitada.\n" .
                    "Por favor, habilita 'pdo_mysql' en tu archivo php.ini y reinicia el servidor."
                );
            }

            // Construir el DSN (Data Source Name) para la conexión
            // Formato: mysql:host=localhost;dbname=rrhh_system;charset=utf8mb4
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // Configurar opciones de PDO para mayor seguridad y mejor manejo de errores
            $options = [
                // Lanzar excepciones cuando ocurra un error (en lugar de solo warnings)
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                
                // Devolver resultados como arrays asociativos (más fácil de usar)
                // Ejemplo: $row['nombre'] en lugar de $row[0]
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Desactivar emulación de prepared statements (más seguro)
                // Esto fuerza a MySQL a usar prepared statements reales
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            // Intentar agregar el comando de inicialización para establecer el charset
            // Algunas versiones de PHP pueden no tener esta constante, por eso usamos try-catch
            try {
                $mysqlInitCommand = PDO::MYSQL_ATTR_INIT_COMMAND;
                $options[$mysqlInitCommand] = "SET NAMES " . DB_CHARSET;
            } catch (Error $e) {
                // Si la constante no existe, no pasa nada, lo haremos manualmente después
            }

            // Crear la conexión PDO usando las credenciales de config/config.php
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Establecer charset y collation para utf8mb4
            // utf8mb4 es necesario para soportar correctamente:
            // - Tildes (á, é, í, ó, ú, ñ)
            // - Caracteres especiales (¿, ¡, etc.)
            // - Emojis (si se necesitan en el futuro)
            $this->connection->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->connection->exec("SET CHARACTER SET utf8mb4");
            $this->connection->exec("SET character_set_connection = utf8mb4");
            
        } catch (PDOException $e) {
            // Error de conexión a la base de datos
            // Registrar el error en el log para debugging
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Mostrar mensaje amigable al usuario
            die("Error de conexión a la base de datos.\n" .
                "Por favor, verifica la configuración en config/config.php\n" .
                "Contacta al administrador si el problema persiste.");
        } catch (Exception $e) {
            // Otros errores (como extensión faltante)
            error_log("Database Error: " . $e->getMessage());
            die($e->getMessage());
        }
    }

    /**
     * Obtener la instancia única de la clase (patrón Singleton)
     * Ejemplo: $db = Database::getInstance()->getConnection();
     */
    public static function getInstance() {
        // Si no existe una instancia, crear una nueva
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        // Devolver la instancia (nueva o existente)
        return self::$instance;
    }

    /**
     * Obtener la conexión PDO para realizar consultas
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prevenir clonación de la instancia
     * Esto asegura que solo exista una instancia de la clase
     */
    private function __clone() {}

    /**
     * Prevenir deserialización de la instancia
     * Esto previene que se cree una nueva instancia al deserializar
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar una instancia de Database. Usa getInstance() en su lugar.");
    }
}

