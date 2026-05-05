<?php

declare(strict_types=1);

namespace App\Http;

final class Kernel
{
    /**
     * Boot the HTTP pipeline and dispatch the request to web routes.
     */
    public function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => $secure,
                'cookie_samesite' => 'Lax',
            ]);
        }

        require base_path('routes/web.php');
    }
}
