<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $params = [];
    private string $namespace = 'App\Controller\\';

    /**
     * Добавить GET маршрут
     */
    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * Добавить POST маршрут
     */
    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Добавить маршрут
     */
    public function add(string $method, string $path, string $handler): void
    {
        $path = $this->normalizePath($path);

        // Преобразуем параметры в пути {id} в регулярное выражение
        $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'original' => $path
        ];
    }

    /**
     * Найти подходящий маршрут
     */
    public function match(string $method, string $uri): ?array
    {
        $method = strtoupper($method);
        $uri = $this->normalizePath($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Извлекаем параметры из URI
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return [
                    'handler' => $route['handler'],
                    'params' => $params
                ];
            }
        }

        return null;
    }

    /**
     * Диспетчеризация запроса
     */
    public function dispatch(string $method, string $uri): void
    {
        try {
            $match = $this->match($method, $uri);

            if (!$match) {
                $this->handleNotFound();
                return;
            }

            [$controllerName, $action] = explode('@', $match['handler']);
            $controllerClass = $this->namespace . $controllerName;

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller $controllerClass not found");
            }

            $controller = \App\Core\Container::get($controllerClass);

            if (!method_exists($controller, $action)) {
                throw new \Exception("Method $action not found in $controllerClass");
            }

            call_user_func_array([$controller, $action], $match['params']);

        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Нормализовать путь
     */
    private function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    /**
     * Обработать 404 ошибку
     */
    private function handleNotFound(): void
    {
        header("HTTP/1.0 404 Not Found");

        $homeControllerClass = $this->namespace . 'HomeController';
        if (class_exists($homeControllerClass)) {
            // ЗАМЕНА: Также используем контейнер здесь
            $controller = \App\Core\Container::get($homeControllerClass);
            if (method_exists($controller, 'notFound')) {
                $controller->notFound();
                return;
            }
        }

        echo "404 Not Found";
        exit;
    }

    /**
     * Обработать ошибку
     */
    private function handleError(\Exception $e): void
    {
        header("HTTP/1.0 500 Internal Server Error");

        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo "<h1>Error</h1>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
        } else {
            echo "500 Internal Server Error";
        }

        // Логирование ошибки
        error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        exit;
    }

    /**
     * Получить все маршруты (для отладки)
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}