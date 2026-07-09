<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Configuracao;

class ConfiguracaoRepository extends AbstractRepository
{
    protected string $table = 'configuracoes';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    protected function hydrate(array $row): Configuracao
    {
        return Configuracao::fromArray($row);
    }

    private function ensureTable(): void
    {
        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS configuracoes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NULL,
                email VARCHAR(255) NULL,
                contato VARCHAR(255) NULL,
                endereco TEXT NULL,
                atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            SQL);

        $columns = [];
        foreach ($this->pdo->query('SHOW COLUMNS FROM configuracoes') as $column) {
            $columns[$column['Field']] = true;
        }

        if (!isset($columns['nome'])) {
            $this->pdo->exec('ALTER TABLE configuracoes ADD COLUMN nome VARCHAR(255) NULL');
        }
        if (!isset($columns['email'])) {
            $this->pdo->exec('ALTER TABLE configuracoes ADD COLUMN email VARCHAR(255) NULL');
        }
        if (!isset($columns['contato'])) {
            $this->pdo->exec('ALTER TABLE configuracoes ADD COLUMN contato VARCHAR(255) NULL');
        }
        if (!isset($columns['endereco'])) {
            $this->pdo->exec('ALTER TABLE configuracoes ADD COLUMN endereco TEXT NULL');
        }
        if (!isset($columns['atualizado_em'])) {
            $this->pdo->exec('ALTER TABLE configuracoes ADD COLUMN atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        }
    }

    public function buscarPorId(int $id): ?Configuracao
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPrimeira(): ?Configuracao
    {
        $stmt = $this->pdo->prepare("SELECT * FROM configuracoes ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function valor(string $campo, ?string $default = null): ?string
    {
        $config = $this->buscarPrimeira();

        return match ($campo) {
            'nome', 'nome_biblioteca' => $config?->nome ?? $default,
            'email', 'email_contato' => $config?->email ?? $default,
            'contato', 'telefone' => $config?->contato ?? $default,
            'endereco' => $config?->endereco ?? $default,
            default => $default,
        };
    }

    public function listarTodas(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function salvar(array $dados): bool
    {
        $config = $this->buscarPrimeira();

        $nome = $dados['nome'] ?? null;
        $email = $dados['email'] ?? null;
        $contato = $dados['contato'] ?? null;
        $endereco = $dados['endereco'] ?? null;

        if ($config !== null) {
            $stmt = $this->pdo->prepare("UPDATE configuracoes SET nome = :nome, email = :email, contato = :contato, endereco = :endereco, atualizado_em = NOW() WHERE id = :id");
            return $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'contato' => $contato,
                'endereco' => $endereco,
                'id' => $config->id,
            ]);
        }

        $stmt = $this->pdo->prepare("INSERT INTO configuracoes (nome, email, contato, endereco, atualizado_em) VALUES (:nome, :email, :contato, :endereco, NOW())");
        return $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'contato' => $contato,
            'endereco' => $endereco,
        ]);
    }

    public function criar(Configuracao $config): int
    {
        $sql = "INSERT INTO configuracoes (nome, email, contato, endereco) VALUES (:nome, :email, :contato, :endereco)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome' => $config->nome,
            'email' => $config->email,
            'contato' => $config->contato,
            'endereco' => $config->endereco,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}
