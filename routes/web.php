<?php

declare(strict_types=1);

/**
 * Web routes — path → controller (views loaded from controllers).
 */

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\NotFoundController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TodosController;

$request = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($request === false || $request === '' || $request === null) {
  $request = '/';
}

match ($request) {
  '/' => new HomeController()->index(),
  '/about' => new AboutController()->index(),
  '/contact' => new ContactController()->index(),
  '/todos' => new TodosController()->index(),
  '/login' => new LoginController()->index(),
  '/logout' => new LogoutController()->logout(),
  '/register' => new RegisterController()->index(),
  default => new NotFoundController()->index(),
};
