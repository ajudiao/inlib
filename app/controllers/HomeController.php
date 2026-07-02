<?php

namespace Anderdev\Inlib\controllers;

use Anderdev\Inlib\core\Controller;
use Anderdev\Inlib\repositories\BookRepository;

class HomeController extends Controller
{
    protected BookRepository $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    public function index(): void
    {
        $books = $this->bookRepository->all();
        $this->view('public.index', ['title' => 'Inlib | Home', 'books' => $books]);
    }

    public function sobre(): void
    {
        $this->view('public.sobre', ['title' => 'Inlib | Sobre']);
    }

    public function contato(): void
    {
        $this->view('public.contato', ['title' => 'Inlib | Contato']);
    }

    public function livros(): void
    {
        $this->view('public.livros', ['title' => 'Inlib | Livros']);
    }
}
