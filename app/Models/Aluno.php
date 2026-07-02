<?php
declare(strict_types=1);

namespace App\Model;

class Aluno
{
    public function __construct(
        public ?int $id = null,
        public ?int $usuarioId = null,
        public string $nome = '',
        public string $matricula = '',
        public ?string $curso = null,
    ) {
    }

    public static function fromArray(array $d): self
    {
        return new self(
            id: isset($d['id']) ? (int) $d['id'] : null,
            usuarioId: isset($d['usuario_id']) ? (int) $d['usuario_id'] : null,
            nome: $d['nome'] ?? '',
            matricula: $d['matricula'] ?? '',
            curso: $d['curso'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'usuario_id' => $this->usuarioId,
            'nome'       => $this->nome,
            'matricula'  => $this->matricula,
            'curso'      => $this->curso,
        ];
    }
}
