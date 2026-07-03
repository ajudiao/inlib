<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Livro;

class LivroRepository extends AbstractRepository
{
    protected string $table = 'livros';

    protected function hydrate(array $row): Livro
    {
        return Livro::fromArray($row);
    }

    public function buscarPorId(int $id): ?Livro
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorIsbn(string $isbn): ?Livro
    {
        $stmt = $this->pdo->prepare("SELECT * FROM livros WHERE isbn = :isbn LIMIT 1");
        $stmt->execute(['isbn' => $isbn]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function listarTodos(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function listarAtivos(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM livros WHERE ativo = 1");
        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    public function listarPorCategoria(int $categoriaId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM livros WHERE categoria_id = :cid");
        $stmt->execute(['cid' => $categoriaId]);
        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    public function countAtivos(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM livros WHERE ativo = 1")->fetchColumn();
    }

    public function listarDestaques(int $limit = 6): array
    {
        $limit = max(1, (int) $limit);
        $stmt = $this->pdo->query("SELECT * FROM livros WHERE ativo = 1 ORDER BY criado_em DESC LIMIT {$limit}");
        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    public function listarRecentes(int $limit = 6): array
    {
        return $this->listarDestaques($limit);
    }

    public function listarPorCategoriaNome(string $nome): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT l.* FROM livros l
             INNER JOIN categorias c ON c.id = l.categoria_id
             WHERE c.nome = :nome AND l.ativo = 1
             ORDER BY l.criado_em DESC"
        );
        $stmt->execute(['nome' => $nome]);
        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    /** Pesquisa simples por título e/ou autor (usado na busca do catálogo) */
    public function buscarPorTituloOuAutor(string $termo): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM livros WHERE (titulo LIKE :termo OR autor LIKE :termo) AND ativo = 1"
        );
        $stmt->execute(['termo' => '%' . $termo . '%']);
        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    public function criar(Livro $livro): int
    {
        $sql = "INSERT INTO livros
                    (titulo, autor, editora, isbn, categoria_id, sinopse, ano_publicacao,
                     edicao, capa, url_livro, quantidade_estoque, ativo, criado_em, atualizado_em)
                VALUES
                    (:titulo, :autor, :editora, :isbn, :categoria_id, :sinopse, :ano_publicacao,
                     :edicao, :capa, :url_livro, :quantidade_estoque, :ativo, :criado_em, :atualizado_em)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'titulo'             => $livro->titulo,
            'autor'              => $livro->autor,
            'editora'            => $livro->editora,
            'isbn'               => $livro->isbn,
            'categoria_id'       => $livro->categoriaId,
            'sinopse'            => $livro->sinopse,
            'ano_publicacao'     => $livro->anoPublicacao,
            'edicao'             => $livro->edicao,
            'capa'               => $livro->capa,
            'url_livro'          => $livro->urlLivro,
            'quantidade_estoque' => $livro->quantidadeEstoque,
            'ativo'              => $livro->ativo ? 1 : 0,
            'criado_em'          => $livro->criadoEm,
            'atualizado_em'      => $livro->atualizadoEm,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Livro $livro): bool
    {
        $sql = "UPDATE livros SET
                    titulo = :titulo, autor = :autor, editora = :editora, isbn = :isbn,
                    categoria_id = :categoria_id, sinopse = :sinopse, ano_publicacao = :ano_publicacao,
                    edicao = :edicao, capa = :capa, quantidade_estoque = :quantidade_estoque, ativo = :ativo
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'                 => $livro->id,
            'titulo'             => $livro->titulo,
            'autor'              => $livro->autor,
            'editora'            => $livro->editora,
            'isbn'               => $livro->isbn,
            'categoria_id'       => $livro->categoriaId,
            'sinopse'            => $livro->sinopse,
            'ano_publicacao'     => $livro->anoPublicacao,
            'edicao'             => $livro->edicao,
            'capa'               => $livro->capa,
            'quantidade_estoque' => $livro->quantidadeEstoque,
            'ativo'              => $livro->ativo ? 1 : 0,
        ]);
    }

    /** Decrementa o estoque em 1 (uso ao registar um empréstimo). Retorna false se não houver estoque. */
    public function decrementarEstoque(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE livros SET quantidade_estoque = quantidade_estoque - 1
             WHERE id = :id AND quantidade_estoque > 0"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() === 1;
    }

    /** Incrementa o estoque em 1 (uso ao registar uma devolução) */
    public function incrementarEstoque(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE livros SET quantidade_estoque = quantidade_estoque + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
