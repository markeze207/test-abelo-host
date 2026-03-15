<?php

namespace App\Core;

use DI\ContainerBuilder;
use Exception;

class Container
{
    private static ?\DI\Container $instance = null;

    /**
     * @return \DI\Container
     * @throws Exception
     */
    public static function getInstance(): \DI\Container
    {
        if (self::$instance === null) {
            $builder = new ContainerBuilder();
            $builder->useAutowiring(true);

            // Загружаем конфигурацию
            $configPath = __DIR__ . '/../../config/di.php';
            if (file_exists($configPath)) {
                $builder->addDefinitions(require $configPath);
            }

            self::$instance = $builder->build();
        }

        return self::$instance;
    }

    /**
     * @param string $class
     * @return object
     * @throws Exception
     */
    public static function get(string $class): object
    {
        return self::getInstance()->get($class);
    }
}