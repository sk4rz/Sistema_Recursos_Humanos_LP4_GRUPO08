<?php
/**
 * Autoloader para el sistema
 */
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            $paths = [
                APP_PATH . '/core/' . $class . '.php',
                MODELS_PATH . '/' . $class . '.php',
                CONTROLLERS_PATH . '/' . $class . '.php',
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    return;
                }
            }
        });
    }
}

