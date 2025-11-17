<?php
/**
 * Manejo de sesiones seguras
 */
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuración de seguridad de sesiones
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
            ini_set('session.cookie_samesite', 'Lax');
            
            session_name(SESSION_NAME);
            session_start();

            // Regenerar ID de sesión periódicamente
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            session_destroy();
        }
    }

    public static function flash($key, $value = null) {
        if ($value === null) {
            $message = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        $_SESSION['_flash'][$key] = $value;
    }
}

