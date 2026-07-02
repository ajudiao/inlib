<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Configuracao;

class ConfiguracaoRepository extends AbstractRepository
{
    protected string $table = 'configuracoes';

    protected function hydrate(array $row): Configuracao
    {
        return Configuracao::fromArray($row);
    }

    public function buscarPorId(int $id): ?Configuracao
    {
        $row = $this->findRawById($id);
        return $row ? $this->hydrate($row) : null;
    }

    public function buscarPorChave(string $chave): ?Configuracao
    {
        $stmt = $this->pdo->prepare("SELECT * FROM configuracoes WHERE chave = :chave LIMIT 1");
        $stmt->execute(['chave' => $chave]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** Atalho útil: devolve directamente o valor (string) de uma chave, ou $default se não existir */
    public function valor(string $chave, ?string $default = null): ?string
    {
        $config = $this->buscarPorChave($chave);
        return $config?->valor ?? $default;
    }

    public function listarTodas(): array
    {
        return array_map($this->hydrate(...), $this->findAllRaw());
    }

    public function atualizarValor(string $chave, string $novoValor): bool
    {
        $stmt = $this->pdo->prepare("UPDATE configuracoes SET valor = :valor WHERE chave = :chave");
        return $stmt->execute(['valor' => $novoValor, 'chave' => $chave]);
    }

    public function criar(Configuracao $config): int
    {
        $sql = "INSERT INTO configuracoes (chave, valor, descricao) VALUES (:chave, :valor, :descricao)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'chave'     => $config->chave,
            'valor'     => $config->valor,
            'descricao' => $config->descricao,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}
