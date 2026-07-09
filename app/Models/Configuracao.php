<?php
declare(strict_types=1);

namespace App\Model;

class Configuracao
{
    public function __construct(
        public ?int $id = null,
        public ?string $nome = null,
        public ?string $email = null,
        public ?string $contato = null,
        public ?string $endereco = null,
        public ?string $atualizadoEm = null,
    ) {
    }

    public static function fromArray(array $d): self
    {
        return new self(
            id: isset($d['id']) ? (int) $d['id'] : null,
            nome: $d['nome'] ?? null,
            email: $d['email'] ?? null,
            contato: $d['contato'] ?? null,
            endereco: $d['endereco'] ?? null,
            atualizadoEm: $d['atualizado_em'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'nome'          => $this->nome,
            'email'         => $this->email,
            'contato'       => $this->contato,
            'endereco'      => $this->endereco,
            'atualizado_em' => $this->atualizadoEm,
        ];
    }
}
