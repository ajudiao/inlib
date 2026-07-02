<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $this->view('admin/dashboard', [
            'title' => 'INLIB - Dashboard'
        ]);
    }

    public function livros()
    {
        $this->view('admin/livros', [
            'title' => 'INLIB - Gestão de Livros'
        ]);
    }

    public function categorias()
    {
        $this->view('admin/categorias', [
            'title' => 'INLIB - Categorias'
        ]);
    }

    public function usuarios()
    {
        $this->view('admin/usuarios', [
            'title' => 'INLIB - Usuários'
        ]);
    }

    public function configuracoes()
    {
        $this->view('admin/configuracoes', [
            'title' => 'INLIB - Configurações'
        ]);
    }

    public function adicionarLivro()
    {
        $this->view('admin/adicionar-livro', [
            'title' => 'INLIB - Adicionar Livro'
        ]);
    }

    public function editarLivro($id)
    {
        $this->view('admin/editar-livro', [
            'title' => 'INLIB - Editar Livro',
            'id' => $id
        ]);
    }
}
