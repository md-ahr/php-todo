<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Auth\SessionAuth;
use App\Repositories\UserRepository;
use App\Validation\LoginValidator;
use PDOException;

final class LoginController
{
    public function index(): void
    {
        if (SessionAuth::check()) {
            header('Location: /todos', true, 303);
            exit;
        }

        $registered = ($_GET['registered'] ?? '') === '1';
        $errors = [];
        $credentialsMessage = '';
        $email = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if (!csrf_validate($_POST['_token'] ?? null)) {
                $errors['_csrf'] = 'This session expired. Refresh and try again.';
            } else {
                $errors = LoginValidator::validate([
                    'email' => $email,
                    'password' => $password,
                ]);

                if ($errors === []) {
                    $normalizedEmail = strtolower($email);
                    try {
                        $repo = new UserRepository(db());
                        $user = $repo->findForAuthByEmail($normalizedEmail);
                        $ok = $user !== null && password_verify($password, $user['password']);

                        if ($ok && $user !== null) {
                            SessionAuth::login($user['id'], $user['email'], $user['name']);
                            $_SESSION['_flash_welcome'] = 'Welcome back!';
                            header('Location: ' . $this->consumeIntendedUrl(), true, 303);
                            exit;
                        }

                        $credentialsMessage = "These credentials don’t match our records.";
                    } catch (PDOException $e) {
                        $missing = (($e->errorInfo[1] ?? null) === 1146)
                            || str_contains($e->getMessage(), 'Base table or view not found');

                        if ($missing) {
                            $credentialsMessage =
                                'The database isn’t migrated yet. From the project root run: php database/migrate.php';
                        } elseif ((bool) ($GLOBALS['config']['debug'] ?? false)) {
                            throw $e;
                        } else {
                            $credentialsMessage = 'Something went wrong. Please try again.';
                        }
                    }
                }
            }
        }

        require view_path('login.view.php');
    }

    /** Path-only redirect after login (never off-site); defaults to /todos. */
    private function consumeIntendedUrl(): string
    {
        $fallback = '/todos';
        $raw = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);

        if (!is_string($raw) || trim($raw) === '') {
            return $fallback;
        }

        $raw = strtok($raw, "\r\n");

        if (!str_starts_with($raw, '/') || str_starts_with($raw, '//')) {
            return $fallback;
        }

        $raw = strlen($raw) > 2048 ? $fallback : $raw;

        return $raw;
    }
}
