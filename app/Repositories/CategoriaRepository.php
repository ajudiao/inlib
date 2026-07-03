<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Categoria;

class CategoriaRepository extends AbstractRepository
{
    protected string $table = 'categorias';

    protected function hydrate(array $row): Categoria
    {
        return Categoria::fromArray($row);
    }

    public function buscarPorId(int $id): ?Categoria
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorNome(string $nome): ?Categoria
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE nome = :nome LIMIT 1");
        $stmt->execute(['nome' => $nome]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function listarTodas(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function listarComContagem(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);
        $sql = "SELECT c.id, c.nome, c.descricao, c.criado_em, COUNT(l.id) AS livros_count
                FROM categorias c
                LEFT JOIN livros l ON l.categoria_id = c.id AND l.ativo = 1
                GROUP BY c.id, c.nome, c.descricao, c.criado_em
                ORDER BY c.nome
                LIMIT {$limit}";

        $stmt = $this->pdo->query($sql);

        return array_map(function (array $row) {
            $categoria = $this->hydrate($row);
            return [
                'id' => $categoria->id,
                'nome' => $categoria->nome,
                'descricao' => $categoria->descricao,
                'livros_count' => (int) $row['livros_count'],
            ];
        }, $stmt->fetchAll());
    }

    public function criar(Categoria $categoria): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)");
        $stmt->execute([
            'nome'      => $categoria->nome,
            'descricao' => $categoria->descricao,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Categoria $categoria): bool
    {
        $stmt = $this->pdo->prepare("UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id");
        return $stmt->execute([
            'id'        => $categoria->id,
            'nome'      => $categoria->nome,
            'descricao' => $categoria->descricao,
        ]);
    }
}
