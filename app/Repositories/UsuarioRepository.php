<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Usuario;
use PDOException;

class UsuarioRepository extends AbstractRepository
{
    protected string $table = 'usuarios';

    protected function hydrate(array $row): Usuario
    {
        return Usuario::fromArray($row);
    }

    public function buscarPorId(int $id): ?Usuario
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function listarTodos(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function listarPorPerfil(string $perfil): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE perfil = :perfil");
        $stmt->execute(['perfil' => $perfil]);
        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    /** Cria o usuário e devolve o id gerado. Lança PDOException se o email já existir. */
    public function criar(Usuario $usuario): int
    {
        $sql = "INSERT INTO usuarios (email, senha_hash, perfil, ativo)
                VALUES (:email, :senha_hash, :perfil, :ativo)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email'      => $usuario->email,
            'senha_hash' => $usuario->senhaHash,
            'perfil'     => $usuario->perfil,
            'ativo'      => $usuario->ativo ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET email = :email, perfil = :perfil, ativo = :ativo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'     => $usuario->id,
            'email'  => $usuario->email,
            'perfil' => $usuario->perfil,
            'ativo'  => $usuario->ativo ? 1 : 0,
        ]);
    }

    public function atualizarSenha(int $id, string $novoHash): bool
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET senha_hash = :senha WHERE id = :id");
        return $stmt->execute(['senha' => $novoHash, 'id' => $id]);
    }

    public function desativar(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
