<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Professor;

class ProfessorRepository extends AbstractRepository
{
    protected string $table = 'professores';

    protected function hydrate(array $row): Professor
    {
        return Professor::fromArray($row);
    }

    public function buscarPorId(int $id): ?Professor
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?Professor
    {
        $stmt = $this->pdo->prepare("SELECT * FROM professores WHERE usuario_id = :uid LIMIT 1");
        $stmt->execute(['uid' => $usuarioId]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorRegistro(string $registro): ?Professor
    {
        $stmt = $this->pdo->prepare("SELECT * FROM professores WHERE registro = :r LIMIT 1");
        $stmt->execute(['r' => $registro]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function listarTodos(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function criar(Professor $professor): int
    {
        $sql = "INSERT INTO professores (usuario_id, nome, registro, departamento)
                VALUES (:usuario_id, :nome, :registro, :departamento)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario_id'   => $professor->usuarioId,
            'nome'         => $professor->nome,
            'registro'     => $professor->registro,
            'departamento' => $professor->departamento,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Professor $professor): bool
    {
        $sql = "UPDATE professores SET nome = :nome, registro = :registro, departamento = :departamento WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'           => $professor->id,
            'nome'         => $professor->nome,
            'registro'     => $professor->registro,
            'departamento' => $professor->departamento,
        ]);
    }
}
