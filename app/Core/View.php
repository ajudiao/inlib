<?php

namespace App\Core;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

class View
{
    private static $twig;

    private static function init()
    {
        if (self::$twig === null) {

            $loader = new FilesystemLoader(BASE_PATH . '/resources/views');

            self::$twig = new Environment($loader, [
                'cache' => false,
                'debug' => true
            ]);

            /*
            |--------------------------------------------------------------------------
            | Variáveis globais
            |--------------------------------------------------------------------------
            */
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            self::$twig->addGlobal('app_name', APP_NAME);
            self::$twig->addGlobal('base_url', URL_DESENVOLVIMENTO);
            self::$twig->addGlobal('session', $_SESSION ?? []);

            /*
            |--------------------------------------------------------------------------
            | Funções
            |--------------------------------------------------------------------------
            */
            // Caminhos para assets estáticos
            self::$twig->addFunction(
                new TwigFunction('asset', function ($path = '') {
                    return '/assets/' . ltrim($path, '/');
                })
            );

            // URL base do sistema
            self::$twig->addFunction(
                new TwigFunction('url', function ($path = '') {
                    return '/' . ltrim($path, '/');
                })
            );

            self::$twig->addFunction(
                new TwigFunction('bookCover', function (?string $path = null) {
                    $fallback = '/assets/images/preview.png';

                    if ($path === null || trim($path) === '') {
                        return $fallback;
                    }

                    $path = trim($path);

                    if (preg_match('#^https?://#i', $path) || strpos($path, 'data:') === 0) {
                        return $path;
                    }

                    if (strpos($path, '/assets/') === 0) {
                        return $path;
                    }

                    if (strpos($path, '/public/') === 0) {
                        $path = substr($path, strlen('/public'));
                    }

                    if (strpos($path, '/') !== 0) {
                        $path = '/' . $path;
                    }

                    $publicFile = BASE_PATH . '/public' . $path;
                    if (file_exists($publicFile)) {
                        return $path;
                    }

                    if (strpos($path, '/uploads/') === 0) {
                        return $path;
                    }

                    return '/uploads/books/covers/' . ltrim($path, '/');
                })
            );

            // Caminho completo de imagens de veículos
            self::$twig->addFunction(
                new TwigFunction('carImage', function (?string $filename) {
                    $uploadPath = '/uploads/cars/'; // URL pública para navegador
                    $filePath = BASE_PATH . '/public/uploads/cars/' . $filename; // caminho real no servidor

                    if ($filename && file_exists($filePath)) {
                        return $uploadPath . ltrim($filename, '/');
                    }

                    // Fallback caso não exista imagem
                    return 'https://via.placeholder.com/80x60?text=Carro';
                })
            );
        }

        return self::$twig;
    }
    
    public static function render(string $template, array $data = [])
    {
        $twig = self::init();

        echo $twig->render($template . '.twig', $data);
    }
}
