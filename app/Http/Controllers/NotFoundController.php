<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class NotFoundController
{
    public function index(): void
    {
        require view_path('404.view.php');
    }
}
