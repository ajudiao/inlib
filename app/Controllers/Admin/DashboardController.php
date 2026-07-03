<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\Livro;
use App\Repository\CategoriaRepository;
use App\Repository\LivroRepository;

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
        $uploadBasePath = BASE_PATH . '/storage/books';
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
                        $capaPath = '/storage/books/covers/' . $coverFilename;
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
                        $pdfPath = '/storage/books/pdf/' . $pdfFilename;
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
            $this->view('admin/adicionar-livro', [
                'title' => 'INLIB - Adicionar Livro',
                'categories' => $categories,
                'formError' => implode(' ', $errors),
                'formOld' => $data,
            ]);
            return;
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

        $this->view('admin/adicionar-livro', [
            'title' => 'INLIB - Adicionar Livro',
            'categories' => $categories,
            'formSuccess' => 'Livro adicionado com sucesso.',
        ]);
    }

    public function editarLivro(int $id)
    {
        $this->view('admin/editar-livro', [
            'title' => 'INLIB - Editar Livro',
            'id' => $id
        ]);
    }
}
