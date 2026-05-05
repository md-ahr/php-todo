<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class ContactController
{
    public function index(): void
    {
        $errors = [];
        $name = '';
        $email = '';
        $subject = '';
        $message = '';
        $sent = (($_GET['sent'] ?? '') === '1');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $subject = trim((string) ($_POST['subject'] ?? ''));
            $message = trim((string) ($_POST['message'] ?? ''));

            if ($name === '') {
                $errors['name'] = 'Please enter your name.';
            }
            if ($email === '') {
                $errors['email'] = 'Please enter your email.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Enter a valid email address.';
            }
            if ($message === '') {
                $errors['message'] = 'Please enter a message.';
            }

            if ($errors === []) {
                header('Location: /contact?sent=1', true, 303);
                exit;
            }
        }

        require view_path('contact.view.php');
    }
}
