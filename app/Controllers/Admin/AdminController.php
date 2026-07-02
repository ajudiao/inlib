<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->view('admin.dashboard', ['title' => 'Inlib | Admin']);
    }

    public function livros(): void
    {
        $this->view('admin.livros', ['title' => 'Inlib | Gerenciar livros']);
    }
}
