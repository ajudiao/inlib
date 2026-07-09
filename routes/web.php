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
    Router::post('/login', 'HomeController@loginPost');
    Router::post('/register', 'HomeController@registerPost');
    Router::get('/logout', 'HomeController@logout');
    Router::get('/pesquisa', 'HomeController@pesquisa');
    Router::get('/ver_livro/{id}', 'HomeController@verLivro');
    Router::get('/ver_livros/{id}', 'HomeController@verLivro');
    Router::get('/livro/{id}', 'HomeController@verLivro');
});

Router::group([
    'prefix' => '/admin',
    'namespace' => 'App\Controllers\Admin',
    'middleware' => 'App\Middleware\AdminMiddleware'
], function () {
    Router::get('/', 'DashboardController@index');
    Router::get('/livros', 'DashboardController@livros');
    Router::get('/categorias', 'DashboardController@categorias');
    Router::get('/categorias/adicionar', 'DashboardController@adicionarCategoria');
    Router::get('/categorias/adicionar-categoria', 'DashboardController@adicionarCategoria');
    Router::post('/categorias', 'DashboardController@salvarCategoria');
    Router::get('/categorias/editar/{id}', 'DashboardController@editarCategoria');
    Router::post('/categorias/editar/{id}', 'DashboardController@atualizarCategoria');
    Router::post('/categorias/apagar/{id}', 'DashboardController@apagarCategoria');
    Router::get('/usuarios', 'DashboardController@usuarios');
    Router::get('/usuarios/adicionar-usuario', 'DashboardController@adicionarUsuario');
    Router::post('/usuarios/adicionar-usuario', 'DashboardController@salvarUsuario');
    Router::get('/usuarios/editar/{id}', 'DashboardController@editarUsuario');
    Router::post('/usuarios/editar/{id}', 'DashboardController@atualizarUsuario');
    Router::post('/usuarios/apagar/{id}', 'DashboardController@apagarUsuario');
    Router::get('/configuracoes', 'DashboardController@configuracoes');
    Router::post('/configuracoes', 'DashboardController@salvarConfiguracoes');
    Router::get('/livros/adicionar', 'DashboardController@adicionarLivro');
    Router::get('/livros/adicionar-livro', 'DashboardController@adicionarLivro');
    Router::post('/livros/adicionar-livro', 'DashboardController@salvarLivro');
    Router::get('/livros/editar/{id}', 'DashboardController@editarLivro');
    Router::post('/livros/editar/{id}', 'DashboardController@atualizarLivro');
    Router::post('/livros/apagar/{id}', 'DashboardController@apagarLivro');
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