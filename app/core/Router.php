<?php

namespace Anderdev\Inlib\core;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $uri, mixed $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, mixed $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function addRoute(string $method, string $uri, mixed $action): void
    {
        $this->routes[strtoupper($method)][$this->normalizeUri($uri)] = $action;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $method = strtoupper($method);
        $uri = $this->normalizeUri($uri);

        if (!isset($this->routes[$method][$uri])) {
            return $this->notFound();
        }

        $action = $this->routes[$method][$uri];

        if (is_callable($action)) {
            return $action();
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controller, $methodName] = explode('@', $action, 2);
            $controllerClass = 'Anderdev\\Inlib\\controllers\\' . str_replace('/', '\\', $controller);

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller {$controllerClass} not found.");
            }

            $instance = new $controllerClass();
            if (!method_exists($instance, $methodName)) {
                throw new \RuntimeException("Method {$methodName} not found in {$controllerClass}.");
            }

            return $instance->$methodName();
        }

        throw new \RuntimeException('Invalid route action.');
    }

    protected function normalizeUri(string $uri): string
    {
        $uri = trim($uri, '/');
        return $uri === '' ? '/' : '/' . $uri;
    }

    protected function notFound(): string
    {
        http_response_code(404);
        return '404 - Página não encontrada.';
    }
}
