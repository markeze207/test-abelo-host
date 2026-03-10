<?php
// Инициализация роутера
use App\Core\Router;

$router = new Router();

// ===== Публичные маршруты =====

// Ajax
$router->get('/load-more-popular', 'PostController@loadMorePopular');
$router->get('/load-more-latest', 'PostController@loadMoreLatest');
$router->get('/load-more-categories', 'HomeController@loadMoreCategories');

// Главная
$router->get('/', 'HomeController@index');

// Категории
$router->get('/category/{slug}', 'CategoryController@view');

// Посты
$router->get('/post/{slug}', 'PostController@view');

// routes
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $uri);