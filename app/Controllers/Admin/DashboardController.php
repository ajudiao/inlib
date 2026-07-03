<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\Categoria;
use App\Model\Livro;
use App\Model\Usuario;
use App\Repository\CategoriaRepository;
use App\Repository\LivroRepository;
use App\Repository\UsuarioRepository;

class DashboardController extends Controller
{
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function setFlash(string $type, string $message): void
    {
        $this->startSession();
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    private function getFlash(): ?array
    {
        $this->startSession();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    public function index()
    {
        $livroRepo = new LivroRepository();
        $categoriaRepo = new CategoriaRepository();
        $usuarioRepo = new UsuarioRepository();

        $livros = $livroRepo->listarTodos();
        $categorias = $categoriaRepo->listarComContagem(100);
        $usuarios = $usuarioRepo->listarTodos();

        $livrosAtivos = $livroRepo->countAtivos();
        $totalLivros = count($livros);
        $livrosInativos = max(0, $totalLivros - $livrosAtivos);
        $usuariosAtivos = count(array_filter($usuarios, static fn($usuario) => $usuario->ativo));

        $this->view('admin/dashboard', [
            'title' => 'INLIB - Dashboard',
            'totalLivros' => $totalLivros,
            'livrosAtivos' => $livrosAtivos,
            'livrosInativos' => $livrosInativos,
            'totalCategorias' => count($categorias),
            'usuariosAtivos' => $usuariosAtivos,
            'categoriesSummary' => $categorias,
        ]);
    }

    public function livros()
    {
        $livroRepo = new LivroRepository();
        $categoriaRepo = new CategoriaRepository();

        $categorias = $categoriaRepo->listarTodas();
        $categoriesById = [];
        foreach ($categorias as $categoria) {
            $categoriesById[$categoria->id] = $categoria->nome;
        }

        $livros = array_map(function (Livro $livro) use ($categoriesById): Livro {
            $livro->categoriaNome = $categoriesById[$livro->categoriaId] ?? null;
            return $livro;
        }, $livroRepo->listarTodos());

        $this->view('admin/livros', [
            'title' => 'INLIB - Gestão de Livros',
            'livros' => $livros,
            'flash' => $this->getFlash(),
        ]);
    }

    public function categorias()
    {
        $categoriaRepo = new CategoriaRepository();

        $this->view('admin/categorias', [
            'title' => 'INLIB - Categorias',
            'categories' => $categoriaRepo->listarComContagem(100),
            'formError' => null,
            'formSuccess' => null,
            'formOld' => [],
        ]);
    }

    public function salvarCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/categorias');
            exit;
        }

        $categoriaRepo = new CategoriaRepository();
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $errors = [];

        if ($nome === '') {
            $errors[] = 'O nome da categoria é obrigatório.';
        } elseif ($categoriaRepo->buscarPorNome($nome) !== null) {
            $errors[] = 'Essa categoria já existe.';
        }

        if (!empty($errors)) {
            $this->view('admin/categorias', [
                'title' => 'INLIB - Categorias',
                'categories' => $categoriaRepo->listarComContagem(100),
                'formError' => implode(' ', $errors),
                'formSuccess' => null,
                'formOld' => [
                    'nome' => $nome,
                    'descricao' => $descricao,
                ],
            ]);
            return;
        }

        $categoria = new Categoria(
            null,
            $nome,
            $descricao !== '' ? $descricao : null,
            date('Y-m-d H:i:s')
        );

        $categoriaRepo->criar($categoria);

        $this->view('admin/categorias', [
            'title' => 'INLIB - Categorias',
            'categories' => $categoriaRepo->listarComContagem(100),
            'formError' => null,
            'formSuccess' => 'Categoria adicionada com sucesso.',
            'formOld' => [],
        ]);
    }

    public function usuarios()
    {
        $usuarioRepo = new UsuarioRepository();

        $this->view('admin/usuarios', [
            'title' => 'INLIB - Usuários',
            'usuarios' => $usuarioRepo->listarTodos(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function adicionarUsuario()
    {
        $this->view('admin/adicionar-usuarios', [
            'title' => 'INLIB - Adicionar Usuário',
            'flash' => $this->getFlash(),
        ]);
    }

    public function salvarUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        $repo = new UsuarioRepository();
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senhaInicial = trim($_POST['senha_inicial'] ?? '');
        $perfil = trim($_POST['perfil'] ?? '');
        $ativo = isset($_POST['ativo']) ? true : false;

        $errors = [];
        $allowedProfiles = ['admin', 'bibliotecario', 'professor', 'aluno'];

        if ($nome === '') {
            $errors[] = 'Nome é obrigatório.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail inválido.';
        } elseif ($repo->buscarPorEmail($email) !== null) {
            $errors[] = 'Este e-mail já está em uso.';
        }

        if ($senhaInicial === '') {
            $errors[] = 'A senha inicial é obrigatória.';
        }

        if (!in_array($perfil, $allowedProfiles, true)) {
            $errors[] = 'Perfil inválido.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            header('Location: /admin/usuarios');
            exit;
        }

        $usuario = new Usuario(
            null,
            $nome,
            $email,
            password_hash($senhaInicial, PASSWORD_DEFAULT),
            $perfil,
            $ativo,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );

        $repo->criar($usuario);
        $this->setFlash('success', 'Usuário adicionado com sucesso.');
        header('Location: /admin/usuarios');
        exit;
    }

    public function configuracoes()
    {
        $this->view('admin/configuracoes', [
            'title' => 'INLIB - Configurações'
        ]);
    }

    public function adicionarLivro()
    {
        $categoriaRepo = new CategoriaRepository();

        $this->view('admin/adicionar-livro', [
            'title' => 'INLIB - Adicionar Livro',
            'categories' => $categoriaRepo->listarTodas(),
        ]);
    }

    public function salvarLivro()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/livros/adicionar-livro');
            exit;
        }

        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'autor' => trim($_POST['autor'] ?? ''),
            'editora' => trim($_POST['editora'] ?? ''),
            'isbn' => trim($_POST['isbn'] ?? ''),
            'categoria_id' => isset($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : null,
            'sinopse' => trim($_POST['sinopse'] ?? ''),
            'ano_publicacao' => isset($_POST['ano_publicacao']) ? (int) $_POST['ano_publicacao'] : null,
            'edicao' => trim($_POST['edicao'] ?? ''),
            'quantidade_estoque' => isset($_POST['quantidade_estoque']) ? (int) $_POST['quantidade_estoque'] : 0,
            'ativo' => isset($_POST['ativo']) ? true : false,
        ];

        $errors = [];

        if ($data['titulo'] === '') {
            $errors[] = 'Título é obrigatório.';
        }
        if ($data['autor'] === '') {
            $errors[] = 'Autor é obrigatório.';
        }
        if ($data['editora'] === '') {
            $errors[] = 'Editora é obrigatória.';
        }
        if ($data['categoria_id'] === null || $data['categoria_id'] <= 0) {
            $errors[] = 'Categoria é obrigatória.';
        }
        if ($data['sinopse'] === '') {
            $errors[] = 'Sinopse é obrigatória.';
        }
        if ($data['ano_publicacao'] === null || $data['ano_publicacao'] < 1400) {
            $errors[] = 'Ano de publicação válido é obrigatório.';
        }
        if ($data['edicao'] === '') {
            $errors[] = 'Edição é obrigatória.';
        }
        if ($data['quantidade_estoque'] < 0) {
            $errors[] = 'Quantidade em estoque inválida.';
        }

        $capaPath = null;
        $pdfPath = null;
        $uploadBasePath = BASE_PATH . '/public/uploads/books/';
        $coverDir = $uploadBasePath . '/covers';
        $pdfDir = $uploadBasePath . '/pdf';

        if (!is_dir($coverDir)) {
            mkdir($coverDir, 0755, true);
        }
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }

        if (empty($errors)) {
            if (isset($_FILES['capa']) && $_FILES['capa']['error'] !== UPLOAD_ERR_NO_FILE) {
                $coverFile = $_FILES['capa'];
                $coverAllowed = ['image/jpeg', 'image/png', 'image/webp'];
                if ($coverFile['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Erro no upload da capa.';
                } elseif (!in_array($coverFile['type'], $coverAllowed, true)) {
                    $errors[] = 'Capa deve ser uma imagem JPG, PNG ou WEBP.';
                } elseif ($coverFile['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'Capa deve ter no máximo 5MB.';
                } else {
                    $coverExt = pathinfo($coverFile['name'], PATHINFO_EXTENSION);
                    $coverFilename = 'cover_' . uniqid() . '.' . strtolower($coverExt);
                    $coverDestination = $coverDir . '/' . $coverFilename;
                    if (move_uploaded_file($coverFile['tmp_name'], $coverDestination)) {
                        $capaPath = '/uploads/books/covers/' . $coverFilename;
                    } else {
                        $errors[] = 'Não foi possível salvar a capa.';
                    }
                }
            } else {
                $errors[] = 'Capa é obrigatória.';
            }

            if (isset($_FILES['url_livro']) && $_FILES['url_livro']['error'] !== UPLOAD_ERR_NO_FILE) {
                $pdfFile = $_FILES['url_livro'];
                if ($pdfFile['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Erro no upload do PDF.';
                } elseif ($pdfFile['type'] !== 'application/pdf') {
                    $errors[] = 'O arquivo do livro deve ser PDF.';
                } elseif ($pdfFile['size'] > 50 * 1024 * 1024) {
                    $errors[] = 'PDF deve ter no máximo 50MB.';
                } else {
                    $pdfExt = pathinfo($pdfFile['name'], PATHINFO_EXTENSION);
                    $pdfFilename = 'book_' . uniqid() . '.' . strtolower($pdfExt);
                    $pdfDestination = $pdfDir . '/' . $pdfFilename;
                    if (move_uploaded_file($pdfFile['tmp_name'], $pdfDestination)) {
                        $pdfPath = '/uploads/books/pdf/' . $pdfFilename;
                    } else {
                        $errors[] = 'Não foi possível salvar o PDF.';
                    }
                }
            } else {
                $errors[] = 'Arquivo PDF é obrigatório.';
            }
        }
        if ($data['titulo'] === '') {
            $errors[] = 'Título é obrigatório.';
        }
        if ($data['autor'] === '') {
            $errors[] = 'Autor é obrigatório.';
        }
        if ($data['editora'] === '') {
            $errors[] = 'Editora é obrigatória.';
        }
        if ($data['categoria_id'] === null || $data['categoria_id'] <= 0) {
            $errors[] = 'Categoria é obrigatória.';
        }
        if ($data['sinopse'] === '') {
            $errors[] = 'Sinopse é obrigatória.';
        }
        if ($data['ano_publicacao'] === null || $data['ano_publicacao'] < 1400) {
            $errors[] = 'Ano de publicação válido é obrigatório.';
        }
        if ($data['edicao'] === '') {
            $errors[] = 'Edição é obrigatória.';
        }
        if ($data['quantidade_estoque'] < 0) {
            $errors[] = 'Quantidade em estoque inválida.';
        }

        $categoriaRepo = new CategoriaRepository();
        $categories = $categoriaRepo->listarTodas();

        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            header('Location: /admin/livros');
            exit;
        }

        $livroRepo = new LivroRepository();
        $livro = new Livro(
            null,
            $data['titulo'],
            $data['autor'],
            $data['editora'],
            $data['isbn'],
            $data['categoria_id'],
            $data['sinopse'],
            $data['ano_publicacao'],
            $data['edicao'],
            $capaPath,
            $pdfPath,
            $data['quantidade_estoque'],
            $data['ativo'],
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );

        $livroRepo->criar($livro);

        $this->setFlash('success', 'Livro adicionado com sucesso.');
        header('Location: /admin/livros');
        exit;
    }

    public function editarLivro(int $id)
    {
        $this->view('admin/editar-livro', [
            'title' => 'INLIB - Editar Livro',
            'id' => $id
        ]);
    }
}
