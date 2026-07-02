<?php

namespace Anderdev\Inlib\repositories;

use Anderdev\Inlib\core\Database;
use Anderdev\Inlib\models\BookModel;

class BookRepository
{
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM books ORDER BY id DESC');

        $books = [];
        while ($row = $stmt->fetch()) {
            $books[] = new BookModel($row);
        }

        return $books;
    }

    public function find(int $id): ?BookModel
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        return $row ? new BookModel($row) : null;
    }
}
