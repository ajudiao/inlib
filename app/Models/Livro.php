<?php
declare(strict_types=1);

namespace App\Model;

class Livro
{
    public function __construct(
        public ?int $id = null,
        public string $titulo = '',
        public string $autor = '',
        public ?string $editora = null,
        public ?string $isbn = null,
        public ?int $categoriaId = null,
        public ?string $sinopse = null,
        public ?int $anoPublicacao = null,
        public ?string $edicao = null,
        public int $quantidadeEstoque = 0,
        public bool $ativo = true,
        public ?string $criadoEm = null,
        public ?string $atualizadoEm = null,
    ) {
    }

    public static function fromArray(array $d): self
    {
        return new self(
            id: isset($d['id']) ? (int) $d['id'] : null,
            titulo: $d['titulo'] ?? '',
            autor: $d['autor'] ?? '',
            editora: $d['editora'] ?? null,
            isbn: $d['isbn'] ?? null,
            categoriaId: isset($d['categoria_id']) ? (int) $d['categoria_id'] : null,
            sinopse: $d['sinopse'] ?? null,
            anoPublicacao: isset($d['ano_publicacao']) ? (int) $d['ano_publicacao'] : null,
            edicao: $d['edicao'] ?? null,
            quantidadeEstoque: (int) ($d['quantidade_estoque'] ?? 0),
            ativo: (bool) ($d['ativo'] ?? true),
            criadoEm: $d['criado_em'] ?? null,
            atualizadoEm: $d['atualizado_em'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'titulo'             => $this->titulo,
            'autor'              => $this->autor,
            'editora'            => $this->editora,
            'isbn'               => $this->isbn,
            'categoria_id'       => $this->categoriaId,
            'sinopse'            => $this->sinopse,
            'ano_publicacao'     => $this->anoPublicacao,
            'edicao'             => $this->edicao,
            'quantidade_estoque' => $this->quantidadeEstoque,
            'ativo'              => $this->ativo ? 1 : 0,
        ];
    }
}
