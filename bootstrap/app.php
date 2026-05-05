<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

if (!function_exists('base_path')) {
    require_once BASE_PATH . '/bootstrap/helpers.php';
}

require_once BASE_PATH . '/bootstrap/load-env.php';
load_env_file(BASE_PATH . '/.env');

if (!class_exists(App\Http\Kernel::class)) {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
        $file = BASE_PATH . '/app/' . $relative;

        if (is_file($file)) {
            require_once $file;
        }
    });
}

$GLOBALS['config'] = require BASE_PATH . '/config/app.php';

(new App\Http\Kernel())->handle();
