<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Auth\SessionAuth;

final class LogoutController
{
    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /');
            exit;
        }

        if (!csrf_validate($_POST['_token'] ?? null)) {
            header('Location: /login', true, 303);
            exit;
        }

        SessionAuth::logout();
        header('Location: /', true, 303);
        exit;
    }
}
