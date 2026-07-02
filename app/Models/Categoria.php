<?php
declare(strict_types=1);

namespace App\Model;

class Categoria
{
    public function __construct(
        public ?int $id = null,
        public string $nome = '',
        public ?string $descricao = null,
        public ?string $criadoEm = null,
    ) {
    }

    public static function fromArray(array $d): self
    {
        return new self(
            id: isset($d['id']) ? (int) $d['id'] : null,
            nome: $d['nome'] ?? '',
            descricao: $d['descricao'] ?? null,
            criadoEm: $d['criado_em'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'nome'      => $this->nome,
            'descricao' => $this->descricao,
        ];
    }
}
