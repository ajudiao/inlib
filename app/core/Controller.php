<?php

namespace Anderdev\Inlib\core;

class Controller
{
    protected function view(string $template, array $data = []): void
    {
        extract($data);

        $viewPath = dirname(__DIR__, 2) . '/views/' . str_replace('.', '/', $template) . '.html';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$template} not found.");
        }

        include $viewPath;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
