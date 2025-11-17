<?php
/**
 * Punto de Entrada Principal del Sistema
 * 
 * Este es el archivo que se ejecuta cuando alguien accede al sistema.
 * Todas las peticiones pasan por aquí (Front Controller Pattern).
 * 
 * Flujo de ejecución:
 * 1. Iniciar sesión PHP
 * 2. Cargar configuración del sistema
 * 3. Registrar el autoloader (carga automática de clases)
 * 4. Iniciar sesión segura
 * 5. Cargar el router
 * 6. Cargar las rutas definidas
 * 7. Procesar la petición y ejecutar el controlador correspondiente
 */

// ============================================
// 1. INICIAR SESIÓN PHP
// ============================================
// Esto permite usar $_SESSION para almacenar datos del usuario
session_start();

// ============================================
// 2. CARGAR CONFIGURACIÓN
// ============================================
// Carga todas las constantes y configuraciones del sistema
// (rutas, base de datos, URLs, etc.)
require_once dirname(__DIR__) . '/config/config.php';

// ============================================
// 3. REGISTRAR AUTOLOADER
// ============================================
// El autoloader carga automáticamente las clases cuando se necesitan
// Sin esto, tendríamos que hacer require_once manualmente para cada clase
require_once APP_PATH . '/core/Autoloader.php';
Autoloader::register();

// ============================================
// 4. INICIAR SESIÓN SEGURA
// ============================================
// Configura la sesión con opciones de seguridad (HttpOnly, SameSite, etc.)
require_once APP_PATH . '/core/Session.php';
Session::start();

// ============================================
// 5. CARGAR ROUTER
// ============================================
// El router es el encargado de decidir qué controlador ejecutar
// según la URL que el usuario solicita
require_once APP_PATH . '/core/Router.php';

// ============================================
// 6. CREAR INSTANCIA DEL ROUTER
// ============================================
// Crear el objeto router que manejará las rutas
$router = new Router();

// ============================================
// 7. CARGAR RUTAS
// ============================================
// Este archivo registra todas las rutas del sistema
// Ejemplo: $router->get('/dashboard', 'TableroController@index');
// IMPORTANTE: Debe ejecutarse DESPUÉS de crear $router
require_once APP_PATH . '/routes/web.php';

// ============================================
// 8. PROCESAR LA PETICIÓN
// ============================================
// El router compara la URL solicitada con las rutas registradas
// y ejecuta el controlador y método correspondiente
// Si no encuentra la ruta, muestra error 404
$router->dispatch();

