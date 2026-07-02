<?php
declare(strict_types=1);

namespace App\Model;

class Usuario
{
    public function __construct(
        public ?int $id = null,
        public string $email = '',
        public string $senhaHash = '',
        public string $perfil = '', // admin | bibliotecario | professor | aluno
        public bool $ativo = true,
        public ?string $criadoEm = null,
        public ?string $atualizadoEm = null,
    ) {
    }

    public static function fromArray(array $d): self
    {
        return new self(
            id: isset($d['id']) ? (int) $d['id'] : null,
            email: $d['email'] ?? '',
            senhaHash: $d['senha_hash'] ?? '',
            perfil: $d['perfil'] ?? '',
            ativo: (bool) ($d['ativo'] ?? true),
            criadoEm: $d['criado_em'] ?? null,
            atualizadoEm: $d['atualizado_em'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'senha_hash' => $this->senhaHash,
            'perfil'     => $this->perfil,
            'ativo'      => $this->ativo ? 1 : 0,
        ];
    }
}
