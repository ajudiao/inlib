<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Aluno;

class AlunoRepository extends AbstractRepository
{
    protected string $table = 'alunos';

    protected function hydrate(array $row): Aluno
    {
        return Aluno::fromArray($row);
    }

    public function buscarPorId(int $id): ?Aluno
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?Aluno
    {
        $stmt = $this->pdo->prepare("SELECT * FROM alunos WHERE usuario_id = :uid LIMIT 1");
        $stmt->execute(['uid' => $usuarioId]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorMatricula(string $matricula): ?Aluno
    {
        $stmt = $this->pdo->prepare("SELECT * FROM alunos WHERE matricula = :m LIMIT 1");
        $stmt->execute(['m' => $matricula]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function listarTodos(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function criar(Aluno $aluno): int
    {
        $sql = "INSERT INTO alunos (usuario_id, nome, matricula, curso)
                VALUES (:usuario_id, :nome, :matricula, :curso)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario_id' => $aluno->usuarioId,
            'nome'       => $aluno->nome,
            'matricula'  => $aluno->matricula,
            'curso'      => $aluno->curso,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Aluno $aluno): bool
    {
        $sql = "UPDATE alunos SET nome = :nome, matricula = :matricula, curso = :curso WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'        => $aluno->id,
            'nome'      => $aluno->nome,
            'matricula' => $aluno->matricula,
            'curso'     => $aluno->curso,
        ]);
    }

    public function deletePorUsuarioId(int $usuarioId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM alunos WHERE usuario_id = :uid");
        return $stmt->execute(['uid' => $usuarioId]);
    }
}
