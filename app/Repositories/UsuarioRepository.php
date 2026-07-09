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

    public function buscarPorEmailExcluindoId(string $email, int $excludeId): ?Usuario
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND id != :id LIMIT 1");
        $stmt->execute(['email' => $email, 'id' => $excludeId]);
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
        $sql = "INSERT INTO usuarios (nome, email, senha_hash, perfil, ativo, criado_em, atualizado_em)
                VALUES (:nome, :email, :senha_hash, :perfil, :ativo, :criado_em, :atualizado_em)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome'          => $usuario->nome,
            'email'         => $usuario->email,
            'senha_hash'    => $usuario->senhaHash,
            'perfil'        => $usuario->perfil,
            'ativo'         => $usuario->ativo ? 1 : 0,
            'criado_em'     => $usuario->criadoEm ?? date('Y-m-d H:i:s'),
            'atualizado_em' => $usuario->atualizadoEm ?? date('Y-m-d H:i:s'),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, ativo = :ativo, atualizado_em = :atualizado_em WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'             => $usuario->id,
            'nome'           => $usuario->nome,
            'email'          => $usuario->email,
            'perfil'         => $usuario->perfil,
            'ativo'          => $usuario->ativo ? 1 : 0,
            'atualizado_em'  => $usuario->atualizadoEm ?? date('Y-m-d H:i:s'),
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
