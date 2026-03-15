<?php

namespace App\Controller;

use App\Core\Container;
use App\Core\Session;
use App\Helpers\Validation\ValidationTrait;
use Smarty;
use App\Core\Database;

abstract class BaseController
{
    protected Smarty $smarty;
    protected Database $db;
    use ValidationTrait;
    protected Session $session;

    public function __construct()
    {
        $this->smarty = Container::get(Smarty::class);
        $this->setupSmarty();

        $this->assignGlobalVars();

        $this->session = Session::getInstance();
    }
    private function setupSmarty(): void
    {
        $rootDir = dirname(__DIR__, 2);

        $this->smarty->setTemplateDir($rootDir . '/templates');
        $this->smarty->setCompileDir($rootDir . '/smarty/compiled');
        $this->smarty->setCacheDir($rootDir . '/smarty/cache');
        $this->smarty->setConfigDir($rootDir . '/smarty/config');

        $this->smarty->compile_check = true;
        $this->smarty->debugging = false;
        $this->smarty->caching = false;
    }

    private function assignGlobalVars(): void
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $protocol . $domain;

        $this->smarty->assign('app_name', 'Тестовый блог');
        $this->smarty->assign('current_year', date('Y'));
        $this->smarty->assign('current_url', $_SERVER['REQUEST_URI'] ?? '');
        $this->smarty->assign('base_url', $baseUrl);
    }

    protected function render(string $template, array $data = []): void
    {
        try {
            foreach ($data as $key => $value) {
                $this->smarty->assign($key, $value);
            }

            $this->smarty->display($template);

        } catch (\Exception $e) {
            error_log("Smarty error: " . $e->getMessage());
            echo "Template error: " . $e->getMessage();
        }
    }

    protected function notFound(): void
    {
        header("HTTP/1.0 404 Not Found");
        $this->render('errors/404.tpl');
        exit;
    }

    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}