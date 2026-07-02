<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Funcionario;

class FuncionarioRepository extends AbstractRepository
{
    protected string $table = 'funcionarios';

    protected function hydrate(array $row): Funcionario
    {
        return Funcionario::fromArray($row);
    }

    public function buscarPorId(int $id): ?Funcionario
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?Funcionario
    {
        $stmt = $this->pdo->prepare("SELECT * FROM funcionarios WHERE usuario_id = :uid LIMIT 1");
        $stmt->execute(['uid' => $usuarioId]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function listarTodos(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function criar(Funcionario $funcionario): int
    {
        $sql = "INSERT INTO funcionarios (usuario_id, nome, registro, cargo)
                VALUES (:usuario_id, :nome, :registro, :cargo)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario_id' => $funcionario->usuarioId,
            'nome'       => $funcionario->nome,
            'registro'   => $funcionario->registro,
            'cargo'      => $funcionario->cargo,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Funcionario $funcionario): bool
    {
        $sql = "UPDATE funcionarios SET nome = :nome, registro = :registro, cargo = :cargo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'       => $funcionario->id,
            'nome'     => $funcionario->nome,
            'registro' => $funcionario->registro,
            'cargo'    => $funcionario->cargo,
        ]);
    }
}
