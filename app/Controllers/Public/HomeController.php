<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Model\Usuario;
use App\Repository\CategoriaRepository;
use App\Repository\LivroRepository;
use App\Repository\UsuarioRepository;

class HomeController extends Controller
{
    public function index()
    {
        $livroRepo = new LivroRepository();
        $categoriaRepo = new CategoriaRepository();

        $featuredBooks = $livroRepo->listarDestaques(6);
        $recentBooks = $livroRepo->listarRecentes(6);
        $categoriesPreview = $categoriaRepo->listarComContagem(5);
        $monographs = $livroRepo->listarPorCategoriaNome('Monografias');

        $this->view('public/index', [
            'title' => 'INLIB - Home',
            'description' => 'Página inicial da biblioteca digital.',
            'featuredBooks' => $featuredBooks,
            'recentBooks' => $recentBooks,
            'categoriesPreview' => $categoriesPreview,
            'monographs' => $monographs,
            'totalBooks' => $livroRepo->countAtivos(),
            'totalCategories' => $categoriaRepo->count(),
        ]);
    }

    public function livros()
    {
        $livroRepo = new LivroRepository();
        $categoriaRepo = new CategoriaRepository();

        $categories = $categoriaRepo->listarTodas();
        $categoryMap = [];
        foreach ($categories as $category) {
            if ($category->id !== null) {
                $categoryMap[$category->id] = $category->nome;
            }
        }

        $books = array_map(function ($book) use ($categoryMap) {
            $categoryName = null;
            if ($book->categoriaId !== null && isset($categoryMap[$book->categoriaId])) {
                $categoryName = $categoryMap[$book->categoriaId];
            }

            return [
                'id' => $book->id,
                'title' => $book->titulo,
                'author' => $book->autor,
                'category' => $categoryName ?? 'Sem Categoria',
                'year' => $book->anoPublicacao,
                'description' => $book->sinopse ?? '',
                'cover' => $book->capa ?? 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80',
            ];
        }, $livroRepo->listarAtivos());

        $this->view('public/livros', [
            'title' => 'INLIB - Livros',
            'books' => $books,
            'categories' => array_map(function ($category) {
                return ['id' => $category->id, 'name' => $category->nome];
            }, $categories),
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

    public function loginPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $repo = new UsuarioRepository();
        $user = $repo->buscarPorEmail($email);

        if (!$user || !password_verify($password, $user->senhaHash)) {
            $this->view('public/login', [
                'title' => 'INLIB - Login',
                'loginError' => 'Email ou senha inválidos.',
                'showRegister' => false,
            ]);
            return;
        }

        if (!$user->ativo) {
            $this->view('public/login', [
                'title' => 'INLIB - Login',
                'loginError' => 'Conta desativada. Contate o administrador.',
                'showRegister' => false,
            ]);
            return;
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->nome;
        $_SESSION['user_perfil'] = $user->perfil;

        header('Location: /');
        exit;
    }

    public function registerPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($name === '') {
            $errors[] = 'Nome é obrigatório.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Senha deve ter pelo menos 6 caracteres.';
        }

        $repo = new UsuarioRepository();
        if ($email && $repo->buscarPorEmail($email)) {
            $errors[] = 'Este email já está cadastrado.';
        }

        if (!empty($errors)) {
            $this->view('public/login', [
                'title' => 'INLIB - Login',
                'registerError' => implode(' ', $errors),
                'showRegister' => true,
                'registerOld' => [
                    'name' => $name,
                    'email' => $email,
                ],
            ]);
            return;
        }

        $user = new Usuario(
            null,
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            'aluno',
            true,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );

        $repo->criar($user);

        $this->view('public/login', [
            'title' => 'INLIB - Login',
            'registerSuccess' => 'Conta criada com sucesso. Faça login para continuar.',
            'showRegister' => false,
            'registerOld' => [
                'name' => $name,
                'email' => $email,
            ],
        ]);
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        header('Location: /login');
        exit;
    }

    public function pesquisa()
    {
        $this->view('public/pesquisa', [
            'title' => 'INLIB - Pesquisa'
        ]);
    }

    public function livro(int $id)
    {
        return $this->verLivro($id);
    }

    public function verLivro(int $id)
    {
        $livroRepo = new LivroRepository();
        $categoriaRepo = new CategoriaRepository();

        $book = $livroRepo->buscarPorId((int) $id);
        if (!$book) {
            http_response_code(404);
            $this->view('errors/404', [
                'message' => 'Livro não encontrado.'
            ]);
            return;
        }

        $category = null;
        if ($book->categoriaId !== null) {
            $category = $categoriaRepo->buscarPorId($book->categoriaId);
        }

        $this->view('public/ver_livro', [
            'title' => 'INLIB - ' . $book->titulo,
            'book' => $book,
            'categoryName' => $category ? $category->nome : 'Categoria não informada',
        ]);
    }
}
