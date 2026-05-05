<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class AboutController
{
    public function index(): void
    {
        require view_path('about.view.php');
    }
}
