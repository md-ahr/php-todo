<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Validation\RegisterValidator;
use PDOException;
use Throwable;

final class RegisterController
{
    public function index(): void
    {
        $errors = [];
        $name = '';
        $email = '';
        $termsChecked = false;

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $termsChecked = isset($_POST['terms']);
            $password = (string) ($_POST['password'] ?? '');
            $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

            if (!csrf_validate($_POST['_token'] ?? null)) {
                $errors['_csrf'] = 'This form expired. Refresh the page and try again.';
            } else {
                $errors = array_merge(
                    $errors,
                    RegisterValidator::validate([
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'password_confirmation' => $passwordConfirmation,
                        'terms_accepted' => $termsChecked,
                    ]),
                );
            }

            $emailNormalized = strtolower($email);

            try {
                if ($errors === []) {
                    $users = new UserRepository(db());

                    if ($users->existsByEmail($emailNormalized)) {
                        $errors['email'] = 'This email is already registered.';
                    }

                    if ($errors === []) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $users->create($name, $emailNormalized, $hash);
                        header('Location: /login?registered=1', true, 303);
                        exit;
                    }
                }
            } catch (PDOException $e) {
                $errno = $e->errorInfo[1] ?? null;
                $missingTable = ($errno === 1146)
                    || str_contains($e->getMessage(), 'Base table or view not found')
                    || str_contains($e->getMessage(), "doesn't exist");

                if ($missingTable) {
                    $errors['_form'] = 'The database is not set up yet. From the project root run: php database/migrate.php — then try registering again.';
                } elseif ($errno === 1062 || str_contains($e->getMessage(), 'Duplicate entry')) {
                    $errors['email'] = 'This email is already registered.';
                } elseif ((bool) ($GLOBALS['config']['debug'] ?? false)) {
                    throw $e;
                } else {
                    $errors['_form'] = 'We could not create your account. Please try again later.';
                }
            } catch (Throwable $e) {
                if ((bool) ($GLOBALS['config']['debug'] ?? false)) {
                    throw $e;
                }
                $errors['_form'] = 'Something went wrong. Please try again later.';
            }
        }

        require view_path('register.view.php');
    }
}
