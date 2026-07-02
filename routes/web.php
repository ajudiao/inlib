<?php

use App\Core\View;
use Pecee\SimpleRouter\SimpleRouter as Router;
use Pecee\Http\Request;

Router::group([
    'namespace' => 'App\Controllers\Public'
], function () {
    Router::get('/', 'HomeController@index');
    Router::get('/livros', 'HomeController@livros');
    Router::get('/categorias', 'HomeController@categorias');
    Router::get('/sobre', 'HomeController@sobre');
    Router::get('/contato', 'HomeController@contato');
    Router::get('/login', 'HomeController@login');
    Router::get('/pesquisa', 'HomeController@pesquisa');
    Router::get('/livro/{id}', 'HomeController@livro');
});

Router::group([
    'prefix' => '/admin',
    'namespace' => 'App\Controllers\Admin'
], function () {
    Router::get('/', 'DashboardController@index');
    Router::get('/livros', 'DashboardController@livros');
    Router::get('/categorias', 'DashboardController@categorias');
    Router::get('/usuarios', 'DashboardController@usuarios');
    Router::get('/configuracoes', 'DashboardController@configuracoes');
    Router::get('/livros/adicionar', 'DashboardController@adicionarLivro');
    Router::get('/livros/editar/{id}', 'DashboardController@editarLivro');
});

Router::error(function (Request $request, \Throwable $exception) {
    error_log($exception->getMessage());

    if (DEBUG) {
        http_response_code(500);
        echo '<h1>Erro:</h1>';
        echo '<p><strong>Mensagem:</strong> ' . $exception->getMessage() . '</p>';
        echo '<p><strong>Arquivo:</strong> ' . $exception->getFile() . '</p>';
        echo '<p><strong>Linha:</strong> ' . $exception->getLine() . '</p>';
        echo '<pre>';
        print_r($exception->getTrace());
        echo '</pre>';
        return;
    }

    if ($exception->getCode() === 404) {
        http_response_code(404);
        View::render('errors/404', [
            'message' => 'Página não encontrada.'
        ]);
        return;
    }

    http_response_code(500);
    View::render('errors/500', [
        'message' => 'Erro interno. Tente novamente mais tarde.'
    ]);
});