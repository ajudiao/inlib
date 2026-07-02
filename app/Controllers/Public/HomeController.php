<?php

namespace App\Controllers\Public;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->view('public/index', [
            'title' => 'INLIB - Home',
            'description' => 'Página inicial da biblioteca digital.'
        ]);
    }

    public function livros()
    {
        $this->view('public/livros', [
            'title' => 'INLIB - Livros'
        ]);
    }

    public function categorias()
    {
        $this->view('public/categorias', [
            'title' => 'INLIB - Categorias'
        ]);
    }

    public function sobre()
    {
        $this->view('public/sobre', [
            'title' => 'INLIB - Sobre'
        ]);
    }

    public function contato()
    {
        $this->view('public/contato', [
            'title' => 'INLIB - Contato'
        ]);
    }

    public function login()
    {
        $this->view('public/login', [
            'title' => 'INLIB - Login'
        ]);
    }

    public function pesquisa()
    {
        $this->view('public/pesquisa', [
            'title' => 'INLIB - Pesquisa'
        ]);
    }

    public function livro($id)
    {
        $this->view('public/livro', [
            'title' => 'INLIB - Livro',
            'id' => $id
        ]);
    }
}
