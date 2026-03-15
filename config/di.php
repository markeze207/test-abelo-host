<?php

use function DI\autowire;
use function DI\get;
use function DI\factory;

$dbConfig = require __DIR__ . '/database.php';

return [

    'db.config' => $dbConfig,

    App\Core\Database::class => autowire()
        ->constructorParameter('config', get('db.config')),

    App\Repositories\Interfaces\CategoryRepositoryInterface::class =>
        get(App\Repositories\CategoryRepository::class),

    App\Repositories\Interfaces\PostRepositoryInterface::class =>
        get(App\Repositories\PostRepository::class),

    Smarty::class => factory(function () {
        $smarty  = new Smarty();
        $rootDir = dirname(__DIR__, 2);

        $smarty->setTemplateDir($rootDir . '/templates');
        $smarty->setCompileDir($rootDir . '/smarty/compiled');
        $smarty->setCacheDir($rootDir . '/smarty/cache');
        $smarty->setConfigDir($rootDir . '/smarty/config');

        $smarty->compile_check = true;
        $smarty->debugging     = false;
        $smarty->caching       = false;

        return $smarty;
    }),
];