<?php

namespace Anderdev\Inlib\models;

class BookModel
{
    public int $id;
    public string $title;
    public string $author;
    public ?string $description = null;
    public ?string $cover = null;
    public ?string $created_at = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
