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
