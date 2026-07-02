<?php
declare(strict_types=1);

namespace App\Model;

class Configuracao
{
    public function __construct(
        public ?int $id = null,
        public string $chave = '',
        public string $valor = '',
        public ?string $descricao = null,
        public ?string $atualizadoEm = null,
    ) {
    }

    public static function fromArray(array $d): self
    {
        return new self(
            id: isset($d['id']) ? (int) $d['id'] : null,
            chave: $d['chave'] ?? '',
            valor: $d['valor'] ?? '',
            descricao: $d['descricao'] ?? null,
            atualizadoEm: $d['atualizado_em'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'chave'     => $this->chave,
            'valor'     => $this->valor,
            'descricao' => $this->descricao,
        ];
    }
}
