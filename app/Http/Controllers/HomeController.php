<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class HomeController
{
    public function index(): void
    {
        require view_path('index.view.php');
    }
}
