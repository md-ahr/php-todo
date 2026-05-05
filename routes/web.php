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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\TodosController;

$request = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($request === false || $request === '' || $request === null) {
  $request = '/';
}

switch ($request) {
    case '/':
        (new HomeController())->index();
        break;
    case '/about':
        (new AboutController())->index();
        break;
    case '/contact':
        (new ContactController())->index();
        break;
    case '/todos':
        (new TodosController())->index();
        break;
    case '/login':
        (new LoginController())->index();
        break;
    case '/logout':
        (new LogoutController())->logout();
        break;
    case '/register':
        (new RegisterController())->index();
        break;
    case '/subscribe':
        (new SubscribeController())->index();
        break;
    case '/profile':
        (new ProfileController())->index();
        break;
    default:
        (new NotFoundController())->index();
        break;
}
