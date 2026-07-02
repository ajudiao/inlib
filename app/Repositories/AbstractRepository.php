<?php
declare(strict_types=1);

namespace App\Repository;

use App\Core\Database;
use PDO;

/**
 * Base para todos os repositórios: guarda a ligação PDO e oferece
 * operações genéricas (buscar por id, listar tudo, apagar, contar).
 * Cada repositório concreto define $table e o método hydrate().
 */
abstract class AbstractRepository
{
    protected PDO $pdo;
    protected string $table;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    /** Converte uma linha (array associativo) no Model correspondente */
    abstract protected function hydrate(array $row): object;

    public function findRawById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAllRaw(): array
    {
        return $this->pdo->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function existe(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return (bool) $stmt->fetchColumn();
    }
}
