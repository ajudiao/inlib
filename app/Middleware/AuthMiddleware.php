<?php

namespace App\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $allowedProfiles = ['admin', 'bibliotecario'];
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_perfil']) || !in_array($_SESSION['user_perfil'], $allowedProfiles, true)) {
            header('Location: /login');
            exit;
        }
    }
}