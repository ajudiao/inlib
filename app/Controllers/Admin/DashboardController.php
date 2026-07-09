<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\Aluno;
use App\Model\Categoria;
use App\Model\Livro;
use App\Model\Professor;
use App\Model\Usuario;
use App\Repository\AlunoRepository;
use App\Repository\CategoriaRepository;
use App\Repository\LivroRepository;
use App\Repository\ProfessorRepository;
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

        $searchTerm = trim($_GET['q'] ?? '');
        $categorias = $categoriaRepo->listarTodas();
        $categoriesById = [];
        foreach ($categorias as $categoria) {
            $categoriesById[$categoria->id] = $categoria->nome;
        }

        $livros = array_map(function (Livro $livro) use ($categoriesById): Livro {
            $livro->categoriaNome = $categoriesById[$livro->categoriaId] ?? null;
            return $livro;
        }, $livroRepo->listarTodos());

        if ($searchTerm !== '') {
            $searchTermNormalized = strtolower($searchTerm);
            $livros = array_values(array_filter($livros, static function (Livro $livro) use ($searchTermNormalized): bool {
                $haystack = strtolower(implode(' ', array_filter([
                    $livro->titulo,
                    $livro->autor,
                    $livro->editora,
                    $livro->isbn,
                    $livro->categoriaNome,
                    $livro->anoPublicacao !== null ? (string) $livro->anoPublicacao : '',
                ])));

                return strpos($haystack, $searchTermNormalized) !== false;
            }));
        }

        $this->view('admin/livros', [
            'title' => 'INLIB - Gestão de Livros',
            'livros' => $livros,
            'searchTerm' => $searchTerm,
            'flash' => $this->getFlash(),
        ]);
    }

    public function categorias()
    {
        $categoriaRepo = new CategoriaRepository();
        $searchTerm = trim($_GET['q'] ?? '');
        $categories = $categoriaRepo->listarComContagem(100);

        if ($searchTerm !== '') {
            $searchTermNormalized = strtolower($searchTerm);
            $categories = array_values(array_filter($categories, static function (array $categoria) use ($searchTermNormalized): bool {
                $haystack = strtolower(implode(' ', array_filter([
                    $categoria['nome'] ?? '',
                    $categoria['descricao'] ?? '',
                ])));

                return strpos($haystack, $searchTermNormalized) !== false;
            }));
        }

        $this->view('admin/categorias', [
            'title' => 'INLIB - Categorias',
            'categories' => $categories,
            'formError' => null,
            'formSuccess' => null,
            'formOld' => [],
            'searchTerm' => $searchTerm,
            'flash' => $this->getFlash(),
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
        $searchTerm = trim($_GET['q'] ?? '');
        $usuarios = $usuarioRepo->listarTodos();

        if ($searchTerm !== '') {
            $searchTermNormalized = strtolower($searchTerm);
            $usuarios = array_values(array_filter($usuarios, static function (Usuario $usuario) use ($searchTermNormalized): bool {
                $haystack = strtolower(implode(' ', array_filter([
                    $usuario->nome,
                    $usuario->email,
                    $usuario->perfil,
                ])));

                return strpos($haystack, $searchTermNormalized) !== false;
            }));
        }

        $this->view('admin/usuarios', [
            'title' => 'INLIB - Usuários',
            'usuarios' => $usuarios,
            'searchTerm' => $searchTerm,
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
        $livroRepo = new LivroRepository();
        $livro = $livroRepo->buscarPorId($id);

        if ($livro === null) {
            $this->setFlash('error', 'Livro não encontrado.');
            header('Location: /admin/livros');
            exit;
        }

        $categoriaRepo = new CategoriaRepository();

        $this->view('admin/editar-livro', [
            'title' => 'INLIB - Editar Livro',
            'livro' => $livro,
            'categories' => $categoriaRepo->listarTodas(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function atualizarLivro(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/livros');
            exit;
        }

        $livroRepo = new LivroRepository();
        $livro = $livroRepo->buscarPorId($id);

        if ($livro === null) {
            $this->setFlash('error', 'Livro não encontrado.');
            header('Location: /admin/livros');
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
            'ativo' => isset($_POST['ativo']),
        ];

        $errors = $this->validarDadosLivro($data);

        $capaPath = $livro->capa;
        $pdfPath = $livro->urlLivro;
        $uploadBasePath = BASE_PATH . '/public/uploads/books/';
        $coverDir = $uploadBasePath . '/covers';
        $pdfDir = $uploadBasePath . '/pdf';

        if (!is_dir($coverDir)) {
            mkdir($coverDir, 0755, true);
        }
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }

        if (empty($errors) && isset($_FILES['capa']) && $_FILES['capa']['error'] !== UPLOAD_ERR_NO_FILE) {
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
        }

        if (empty($errors) && isset($_FILES['url_livro']) && $_FILES['url_livro']['error'] !== UPLOAD_ERR_NO_FILE) {
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
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            header('Location: /admin/livros/editar/' . $id);
            exit;
        }

        $livro->titulo = $data['titulo'];
        $livro->autor = $data['autor'];
        $livro->editora = $data['editora'];
        $livro->isbn = $data['isbn'] !== '' ? $data['isbn'] : null;
        $livro->categoriaId = $data['categoria_id'];
        $livro->sinopse = $data['sinopse'];
        $livro->anoPublicacao = $data['ano_publicacao'];
        $livro->edicao = $data['edicao'];
        $livro->capa = $capaPath;
        $livro->urlLivro = $pdfPath;
        $livro->quantidadeEstoque = $data['quantidade_estoque'];
        $livro->ativo = $data['ativo'];
        $livro->atualizadoEm = date('Y-m-d H:i:s');

        $livroRepo->atualizar($livro);

        $this->setFlash('success', 'Livro atualizado com sucesso.');
        header('Location: /admin/livros');
        exit;
    }

    public function apagarLivro(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/livros');
            exit;
        }

        $livroRepo = new LivroRepository();
        if (!$livroRepo->existe($id)) {
            $this->setFlash('error', 'Livro não encontrado.');
            header('Location: /admin/livros');
            exit;
        }

        $livroRepo->delete($id);
        $this->setFlash('success', 'Livro removido com sucesso.');
        header('Location: /admin/livros');
        exit;
    }

    public function editarCategoria(int $id)
    {
        $categoriaRepo = new CategoriaRepository();
        $categoria = $categoriaRepo->buscarPorId($id);

        if ($categoria === null) {
            $this->setFlash('error', 'Categoria não encontrada.');
            header('Location: /admin/categorias');
            exit;
        }

        $this->view('admin/editar-categoria', [
            'title' => 'INLIB - Editar Categoria',
            'categoria' => $categoria,
            'flash' => $this->getFlash(),
        ]);
    }

    public function atualizarCategoria(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/categorias');
            exit;
        }

        $categoriaRepo = new CategoriaRepository();
        $categoria = $categoriaRepo->buscarPorId($id);

        if ($categoria === null) {
            $this->setFlash('error', 'Categoria não encontrada.');
            header('Location: /admin/categorias');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $errors = [];

        if ($nome === '') {
            $errors[] = 'O nome da categoria é obrigatório.';
        } elseif ($categoriaRepo->buscarPorNomeExcluindoId($nome, $id) !== null) {
            $errors[] = 'Essa categoria já existe.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            header('Location: /admin/categorias/editar/' . $id);
            exit;
        }

        $categoria->nome = $nome;
        $categoria->descricao = $descricao !== '' ? $descricao : null;
        $categoriaRepo->atualizar($categoria);

        $this->setFlash('success', 'Categoria atualizada com sucesso.');
        header('Location: /admin/categorias');
        exit;
    }

    public function apagarCategoria(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/categorias');
            exit;
        }

        $categoriaRepo = new CategoriaRepository();
        $livroRepo = new LivroRepository();

        if (!$categoriaRepo->existe($id)) {
            $this->setFlash('error', 'Categoria não encontrada.');
            header('Location: /admin/categorias');
            exit;
        }

        if ($livroRepo->contarPorCategoria($id) > 0) {
            $this->setFlash('error', 'Não é possível apagar uma categoria com livros associados.');
            header('Location: /admin/categorias');
            exit;
        }

        $categoriaRepo->delete($id);
        $this->setFlash('success', 'Categoria removida com sucesso.');
        header('Location: /admin/categorias');
        exit;
    }

    public function editarUsuario(int $id)
    {
        $usuarioRepo = new UsuarioRepository();
        $usuario = $usuarioRepo->buscarPorId($id);

        if ($usuario === null) {
            $this->setFlash('error', 'Usuário não encontrado.');
            header('Location: /admin/usuarios');
            exit;
        }

        $alunoRepo = new AlunoRepository();
        $professorRepo = new ProfessorRepository();

        $this->view('admin/editar-usuario', [
            'title' => 'INLIB - Editar Usuário',
            'usuario' => $usuario,
            'aluno' => $alunoRepo->buscarPorUsuarioId($id),
            'professor' => $professorRepo->buscarPorUsuarioId($id),
            'flash' => $this->getFlash(),
        ]);
    }

    public function atualizarUsuario(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        $usuarioRepo = new UsuarioRepository();
        $usuario = $usuarioRepo->buscarPorId($id);

        if ($usuario === null) {
            $this->setFlash('error', 'Usuário não encontrado.');
            header('Location: /admin/usuarios');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senhaNova = trim($_POST['senha_nova'] ?? '');
        $perfil = trim($_POST['perfil'] ?? '');
        $ativo = isset($_POST['ativo']);
        $allowedProfiles = ['admin', 'bibliotecario', 'professor', 'aluno'];

        $errors = [];

        if ($nome === '') {
            $errors[] = 'Nome é obrigatório.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail inválido.';
        } elseif ($usuarioRepo->buscarPorEmailExcluindoId($email, $id) !== null) {
            $errors[] = 'Este e-mail já está em uso.';
        }

        if (!in_array($perfil, $allowedProfiles, true)) {
            $errors[] = 'Perfil inválido.';
        }

        if ($perfil === 'aluno') {
            $matricula = trim($_POST['matricula'] ?? '');
            if ($matricula === '') {
                $errors[] = 'Matrícula é obrigatória para alunos.';
            }
        }

        if ($perfil === 'professor') {
            $registro = trim($_POST['registro'] ?? '');
            if ($registro === '') {
                $errors[] = 'Registro funcional é obrigatório para professores.';
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            header('Location: /admin/usuarios/editar/' . $id);
            exit;
        }

        $usuario->nome = $nome;
        $usuario->email = $email;
        $usuario->perfil = $perfil;
        $usuario->ativo = $ativo;
        $usuario->atualizadoEm = date('Y-m-d H:i:s');
        $usuarioRepo->atualizar($usuario);

        if ($senhaNova !== '') {
            $usuarioRepo->atualizarSenha($id, password_hash($senhaNova, PASSWORD_DEFAULT));
        }

        $alunoRepo = new AlunoRepository();
        $professorRepo = new ProfessorRepository();

        if ($perfil === 'aluno') {
            $professorRepo->deletePorUsuarioId($id);
            $aluno = $alunoRepo->buscarPorUsuarioId($id);
            $matricula = trim($_POST['matricula'] ?? '');
            $curso = trim($_POST['curso'] ?? '');

            if ($aluno !== null) {
                $aluno->nome = $nome;
                $aluno->matricula = $matricula;
                $aluno->curso = $curso !== '' ? $curso : null;
                $alunoRepo->atualizar($aluno);
            } else {
                $alunoRepo->criar(new Aluno(null, $id, $nome, $matricula, $curso !== '' ? $curso : null));
            }
        } elseif ($perfil === 'professor') {
            $alunoRepo->deletePorUsuarioId($id);
            $professor = $professorRepo->buscarPorUsuarioId($id);
            $registro = trim($_POST['registro'] ?? '');
            $departamento = trim($_POST['departamento'] ?? '');

            if ($professor !== null) {
                $professor->nome = $nome;
                $professor->registro = $registro;
                $professor->departamento = $departamento !== '' ? $departamento : null;
                $professorRepo->atualizar($professor);
            } else {
                $professorRepo->criar(new Professor(null, $id, $nome, $registro, $departamento !== '' ? $departamento : null));
            }
        } else {
            $alunoRepo->deletePorUsuarioId($id);
            $professorRepo->deletePorUsuarioId($id);
        }

        $this->setFlash('success', 'Usuário atualizado com sucesso.');
        header('Location: /admin/usuarios');
        exit;
    }

    public function apagarUsuario(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        $usuarioRepo = new UsuarioRepository();

        if (!$usuarioRepo->existe($id)) {
            $this->setFlash('error', 'Usuário não encontrado.');
            header('Location: /admin/usuarios');
            exit;
        }

        $this->startSession();
        if (isset($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] === $id) {
            $this->setFlash('error', 'Não é possível apagar o seu próprio usuário.');
            header('Location: /admin/usuarios');
            exit;
        }

        $alunoRepo = new AlunoRepository();
        $professorRepo = new ProfessorRepository();
        $alunoRepo->deletePorUsuarioId($id);
        $professorRepo->deletePorUsuarioId($id);
        $usuarioRepo->delete($id);

        $this->setFlash('success', 'Usuário removido com sucesso.');
        header('Location: /admin/usuarios');
        exit;
    }

    /** @return string[] */
    private function validarDadosLivro(array $data): array
    {
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

        return $errors;
    }
}
